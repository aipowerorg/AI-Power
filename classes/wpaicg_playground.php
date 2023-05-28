<?php

namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Playground')) {
    class WPAICG_Playground
    {
        private static  $instance = null ;

        public static function get_instance()
        {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action('init',[$this,'wpaicg_stream'],1);
            add_action( 'admin_menu', array( $this, 'wpaicg_playground_menu' ) );
            add_action( 'wp_ajax_wpaicg_comparison', array( $this, 'wpaicg_comparison' ) );
        }

        public function wpaicg_comparison()
        {
            $wpaicg_result = array('status' => 'error');
            if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wpaicg_comparison_generator' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            $open_ai = WPAICG_OpenAI::get_instance()->openai();
            if(!$open_ai){
                $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
                exit;
            }
            $wpaicg_generator = WPAICG_Generator::get_instance();
            $wpaicg_generator->openai($open_ai);
            $model = sanitize_text_field($_REQUEST['model']);
            $prompt = sanitize_text_field($_REQUEST['prompt']);
            $temperature = sanitize_text_field($_REQUEST['temperature']);
            $max_tokens = sanitize_text_field($_REQUEST['max_tokens']);
            $top_p = sanitize_text_field($_REQUEST['top_p']);
            $frequency_penalty = sanitize_text_field($_REQUEST['frequency_penalty']);
            $presence_penalty = sanitize_text_field($_REQUEST['presence_penalty']);
            $complete = $wpaicg_generator->wpaicg_request([
                'model' => $model,
                'prompt' => $prompt,
                'temperature' => (float)$temperature,
                'max_tokens' => (float)$max_tokens,
                'frequency_penalty' => (float)$frequency_penalty,
                'presence_penalty' => (float)$presence_penalty,
                'top_p' => (float)$top_p
            ]);
            if($complete['status'] == 'error'){
                $wpaicg_result['msg'] = $complete['msg'];
            }
            else{
                $wpaicg_estimated = 0;
                $wpaicg_result['text'] = $complete['data'];
                $wpaicg_result['text'] = str_replace("\\",'',$wpaicg_result['text']);
                $wpaicg_result['tokens'] = $complete['tokens'];
                $wpaicg_result['words'] = $complete['length'];
                if($model === 'gpt-3.5-turbo') {
                    $wpaicg_estimated = 0.002 * $wpaicg_result['tokens'] / 1000;
                }
                if($model === 'gpt-4') {
                    $wpaicg_estimated = 0.06 * $wpaicg_result['tokens'] / 1000;
                }
                if($model === 'gpt-4-32k') {
                    $wpaicg_estimated = 0.12 * $wpaicg_result['tokens'] / 1000;
                }
                else{
                    $wpaicg_estimated = 0.02 * $wpaicg_result['tokens'] / 1000;
                }
                $wpaicg_result['cost'] = $wpaicg_estimated;
                $wpaicg_result['status'] = 'success';
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_token_handling($source)
        {
            global $wpdb;
            $result = array();
            $result['message'] = esc_html__('You have reached your token limit.','gpt3-ai-content-generator');
            $result['table'] = 'wpaicg_formtokens';
            $result['limit'] = false;
            $result['tokens'] = 0;
            $result['source'] = $source;
            $result['token_id'] = false;
            $result['limited'] = false;
            $result['old_tokens'] = 0;
            if(!is_user_logged_in()) {
                $wpaicg_client_id = $this->wpaicg_get_cookie_id($source);
            }
            else{
                $wpaicg_client_id = false;
            }
            $result['client_id'] = $wpaicg_client_id;
            if($result['source'] == 'promptbase'){
                $result['table'] = 'wpaicg_prompttokens';
            }
            if($result['source'] == 'image'){
                $result['table'] = 'wpaicg_imagetokens';
            }
            $wpaicg_settings = get_option('wpaicg_limit_tokens_'.$result['source'],[]);
            $result['message'] = isset($wpaicg_settings['limited_message']) && !empty($wpaicg_settings['limited_message']) ? $wpaicg_settings['limited_message'] : $result['message'];
            if(is_user_logged_in() && isset($wpaicg_settings['user_limited']) && $wpaicg_settings['user_limited'] && $wpaicg_settings['user_tokens'] > 0){
                $result['limit'] = true;
                $result['tokens'] = $wpaicg_settings['user_tokens'];
            }
            /*Check limit base role*/
            if(is_user_logged_in() && isset($wpaicg_settings['role_limited']) && $wpaicg_settings['role_limited']){
                $wpaicg_roles = ( array )wp_get_current_user()->roles;
                $limited_current_role = 0;
                foreach ($wpaicg_roles as $wpaicg_role) {
                    if(
                        isset($wpaicg_settings['limited_roles'])
                        && is_array($wpaicg_settings['limited_roles'])
                        && isset($wpaicg_settings['limited_roles'][$wpaicg_role])
                        && $wpaicg_settings['limited_roles'][$wpaicg_role] > $limited_current_role
                    ){
                        $limited_current_role = $wpaicg_settings['limited_roles'][$wpaicg_role];
                    }
                }
                if($limited_current_role > 0){
                    $result['limit'] = true;
                    $result['tokens'] = $limited_current_role;
                }
                else{
                    $result['limit'] = false;
                }
            }
            /*End check limit base role*/
            if(!is_user_logged_in() && isset($wpaicg_settings['guest_limited']) && $wpaicg_settings['guest_limited'] && $wpaicg_settings['guest_tokens'] > 0){
                $result['limit'] = true;
                $result['tokens'] = $wpaicg_settings['guest_tokens'];
            }
            if($result['limit']){
                if(is_user_logged_in()){
                    $wpaicg_chat_token_log = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix.$result['table']." WHERE  user_id=%d",get_current_user_id()));
                }
                else{
                    $wpaicg_chat_token_log = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix.$result['table']." WHERE session_id=%s",$wpaicg_client_id));
                }
                $result['old_tokens'] = $wpaicg_chat_token_log ? $wpaicg_chat_token_log->tokens : 0;
                $wpaicg_chat_token_id = $wpaicg_chat_token_log ? $wpaicg_chat_token_log->id : false;
                if(
                    $result['old_tokens'] > 0
                    && $result['tokens'] > 0
                    && $result['old_tokens'] > $result['tokens']
                ){
                    $result['limited'] = true;
                    $result['token_id'] = $wpaicg_chat_token_id;
                    $result['left_tokens'] = 0;
                }
                else{
                    $result['left_tokens'] = $result['tokens'] - $result['old_tokens'];
                    $result['token_id'] = $wpaicg_chat_token_id;
                    $result['limited'] = false;
                }
                /*Check if logged user has limit tokens in balance*/
                if(is_user_logged_in()){
                    if($source == 'form'){
                        $source = 'forms';
                    }
                    $user_meta_key = 'wpaicg_' . $source . '_tokens';
                    $user_tokens = get_user_meta(get_current_user_id(), $user_meta_key, true);
                    $result['left_tokens'] += (float)$user_tokens;
                }
                if($result['limited'] && is_user_logged_in()){
                    if(!empty($user_tokens) && $user_tokens > 0){
                        $result['limited'] = false;
                    }
                }
            }
            return $result;
        }

        public function wpaicg_stream()
        {
            if(isset($_GET['wpaicg_stream']) && sanitize_text_field($_GET['wpaicg_stream']) == 'yes'){
                global $wpdb;
                header('Content-type: text/event-stream');
                header('Cache-Control: no-cache');
                if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                    $words = explode(' ', WPAICG_NONCE_ERROR);
                    $words[count($words) + 1] = '[LIMITED]';
                    foreach ($words as $key => $word) {
                        echo "event: message\n";
                        if ($key == 0) {
                            echo 'data: {"choices":[{"delta":{"content":"' . $word . '"}}]}';
                        } else {
                            if ($word == '[LIMITED]') {
                                echo 'data: [LIMITED]';
                            } else {
                                echo 'data: {"choices":[{"delta":{"content":" ' . $word . '"}}]}';
                            }
                        }
                        echo "\n\n";
                        ob_end_flush();
                        flush();
                    }
                }
                else {
                    if (isset($_REQUEST['title']) && !empty($_REQUEST['title'])) {
                        $wpaicg_prompt = sanitize_text_field($_REQUEST['title']);
                        $openai = \WPAICG\WPAICG_OpenAI::get_instance()->openai();
                        if ($openai) {
                            $wpaicg_limited_tokens = false;
                            $wpaicg_args = array(
                                'prompt' => $wpaicg_prompt,
                                'temperature' => (float)$openai->temperature,
                                "max_tokens" => (float)$openai->max_tokens,
                                "frequency_penalty" => (float)$openai->frequency_penalty,
                                "presence_penalty" => (float)$openai->presence_penalty,
                                "stream" => true
                            );
                            if (isset($_REQUEST['temperature']) && !empty($_REQUEST['temperature'])) {
                                $wpaicg_args['temperature'] = (float)sanitize_text_field($_REQUEST['temperature']);
                            }
                            if (isset($_REQUEST['engine']) && !empty($_REQUEST['engine'])) {
                                $wpaicg_args['model'] = sanitize_text_field($_REQUEST['engine']);
                            } else {
                                $wpaicg_args['model'] = 'gpt-3.5-turbo';
                            }
                            if (isset($_REQUEST['max_tokens']) && !empty($_REQUEST['max_tokens'])) {
                                $wpaicg_args['max_tokens'] = (float)sanitize_text_field($_REQUEST['max_tokens']);
                            }
                            if (isset($_REQUEST['frequency_penalty']) && !empty($_REQUEST['frequency_penalty'])) {
                                $wpaicg_args['frequency_penalty'] = (float)sanitize_text_field($_REQUEST['frequency_penalty']);
                            }
                            if (isset($_REQUEST['presence_penalty']) && !empty($_REQUEST['presence_penalty'])) {
                                $wpaicg_args['presence_penalty'] = (float)sanitize_text_field($_REQUEST['presence_penalty']);
                            }
                            if (isset($_REQUEST['top_p']) && !empty($_REQUEST['top_p'])) {
                                $wpaicg_args['top_p'] = (float)sanitize_text_field($_REQUEST['top_p']);
                            }
                            if (isset($_REQUEST['best_of']) && !empty($_REQUEST['best_of'])) {
                                $wpaicg_args['best_of'] = (float)sanitize_text_field($_REQUEST['best_of']);
                            }
                            if (isset($_REQUEST['stop']) && !empty($_REQUEST['stop'])) {
                                $wpaicg_args['stop'] = explode(',', sanitize_text_field($_REQUEST['stop']));
                            }
                            $has_limited = false;
                            if (isset($_REQUEST['source_stream']) && in_array($_REQUEST['source_stream'], array('promptbase', 'form'))) {
                                $wpaicg_token_handling = $this->wpaicg_token_handling(sanitize_text_field($_REQUEST['source_stream']));
                                if ($wpaicg_token_handling['limited']) {
                                    $has_limited = true;
                                    $this->wpaicg_event_message($wpaicg_token_handling['message']);
                                }
                            }

                            if (!$has_limited) {
                                if ($wpaicg_args['model'] == 'gpt-3.5-turbo' || $wpaicg_args['model'] == 'gpt-4' || $wpaicg_args['model'] == 'gpt-4-32k') {
                                    unset($wpaicg_args['best_of']);
                                    $wpaicg_args['messages'] = array(
                                        array('role' => 'user', 'content' => $wpaicg_args['prompt'])
                                    );
                                    unset($wpaicg_args['prompt']);
                                    try {
                                        $openai->chat($wpaicg_args, function ($curl_info, $data) {
                                            echo $data;
                                            ob_flush();
                                            flush();
                                            return strlen($data);
                                        });
                                    }
                                    catch (\Exception $exception){
                                        $message = $exception->getMessage();
                                        $this->wpaicg_event_message($message);
                                    }
                                } else {
                                    try {
                                        $openai->completion($wpaicg_args, function ($curl_info, $data) {
                                            echo _wp_specialchars($data, ENT_NOQUOTES, 'UTF-8', true);
                                            ob_flush();
                                            flush();
                                            return strlen($data);
                                        });
                                    }
                                    catch (\Exception $exception){
                                        $message = $exception->getMessage();
                                        $this->wpaicg_event_message($message);
                                    }
                                }
                            }
                        }
                    }
                }
                exit;
            }
        }

        public function wpaicg_event_message($words)
        {
            $words = explode(' ', $words);
            $words[count($words) + 1] = '[LIMITED]';
            foreach ($words as $key => $word) {
                echo "event: message\n";
                if ($key == 0) {
                    echo 'data: {"choices":[{"delta":{"content":"' . $word . '"}}]}';
                } else {
                    if ($word == '[LIMITED]') {
                        echo 'data: [LIMITED]';
                    } else {
                        echo 'data: {"choices":[{"delta":{"content":" ' . $word . '"}}]}';
                    }
                }
                echo "\n\n";
                ob_end_flush();
                flush();
            }
        }

        public function wpaicg_get_cookie_id($source_stream)
        {
            if(!function_exists('PasswordHash')){
                require_once ABSPATH . 'wp-includes/class-phpass.php';
            }
            if(isset($_COOKIE['wpaicg_'.$source_stream.'_client_id']) && !empty($_COOKIE['wpaicg_'.$source_stream.'_client_id'])){
                return $_COOKIE['wpaicg_'.$source_stream.'_client_id'];
            }
            else{
                $hasher      = new \PasswordHash( 8, false );
                $cookie_id = 't_' . substr( md5( $hasher->get_random_bytes( 32 ) ), 2 );
                setcookie('wpaicg_'.$source_stream.'_client_id', $cookie_id, time() + 604800, COOKIEPATH, COOKIE_DOMAIN);
                return $cookie_id;
            }
        }

        public function wpaicg_playground_menu()
        {
        }

        public function wpaicg_playground_page()
        {
            include WPAICG_PLUGIN_DIR . 'admin/extra/wpaicg_playground.php';
        }

    }
    WPAICG_Playground::get_instance();
}
