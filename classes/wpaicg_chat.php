<?php

namespace WPAICG;

if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Chat')) {
    class WPAICG_Chat
    {
        private static $instance = null;

        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action( 'admin_menu', array( $this, 'wpaicg_menu' ) );
            add_shortcode( 'wpaicg_chatgpt', [ $this, 'wpaicg_chatbox' ] );
            add_shortcode( 'wpaicg_chatgpt_widget', [ $this, 'wpaicg_chatbox_widget' ] );
            add_action( 'wp_ajax_wpaicg_update_chatbot', array( $this, 'wpaicg_update_chatbot' ) );
            add_action( 'wp_ajax_wpaicg_chatbox_message', array( $this, 'wpaicg_chatbox_message' ) );
            add_action( 'wp_ajax_nopriv_wpaicg_chatbox_message', array( $this, 'wpaicg_chatbox_message' ) );
            add_action( 'wp_ajax_wpaicg_chat_shortcode_message', array( $this, 'wpaicg_chatbox_message' ) );
            add_action( 'wp_ajax_nopriv_wpaicg_chat_shortcode_message', array( $this, 'wpaicg_chatbox_message' ) );
            if ( ! wp_next_scheduled( 'wpaicg_remove_chat_tokens_limited' ) ) {
                wp_schedule_event( time(), 'hourly', 'wpaicg_remove_chat_tokens_limited' );
            }
            add_action( 'wpaicg_remove_chat_tokens_limited', array( $this, 'wpaicg_remove_chat_tokens' ) );
            $this->create_database_tables();
        }

        public function wpaicg_update_chatbot()
        {
            global $wpdb;
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wpaicg_chatbot_save' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_REQUEST['bot']) && is_array($_REQUEST['bot'])){
                $bot = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['bot']);
                if(isset($bot['id']) && !empty($bot['id'])){
                    $wpaicg_chatbot_id = $bot['id'];
                    wp_update_post(array(
                        'ID' => $bot['id'],
                        'post_title' => $bot['name'],
                        'post_content' => json_encode($bot, JSON_UNESCAPED_UNICODE)
                    ));
                }
                else{
                    $wpaicg_chatbot_id = wp_insert_post(array(
                        'post_title' => $bot['name'],
                        'post_content' => json_encode($bot, JSON_UNESCAPED_UNICODE),
                        'post_type' => 'wpaicg_chatbot',
                        'post_status' => 'publish'
                    ));
                }
                $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->postmeta." WHERE post_id=%d",$wpaicg_chatbot_id));
                if(isset($bot['type']) && $bot['type'] == 'widget' && isset($bot['pages']) && !empty($bot['pages'])){
                    $pages = array_map('trim', explode(',', $bot['pages']));
                    foreach($pages as $page){
                        add_post_meta($wpaicg_chatbot_id,'wpaicg_widget_page_'.$page,'yes');
                    }
                }
                $wpaicg_result['status'] = 'success';
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_remove_chat_tokens()
        {
            global $wpdb;
            $wpaicg_chat_shortcode_options = get_option('wpaicg_chat_shortcode_options',[]);
            $wpaicg_chat_widget = get_option('wpaicg_chat_widget', []);
            $widget_reset_limit = isset($wpaicg_chat_widget['reset_limit']) && !empty($wpaicg_chat_widget['reset_limit']) ? $wpaicg_chat_widget['reset_limit'] : 0;
            $shortcode_reset_limit = isset($wpaicg_chat_shortcode_options['reset_limit']) && !empty($wpaicg_chat_shortcode_options['reset_limit']) ? $wpaicg_chat_shortcode_options['reset_limit'] : 0;
            if($widget_reset_limit > 0) {
                $widget_time = time() - ($widget_reset_limit * 86400);
                $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "wpaicg_chattokens WHERE source='widget' AND created_at < %s",$widget_time));
            }
            if($shortcode_reset_limit > 0) {
                $shortcode_time = time() - ($shortcode_reset_limit * 86400);
                $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "wpaicg_chattokens WHERE source='shortcode' AND created_at < %s",$shortcode_time));
            }
        }

        public function create_database_tables()
        {
            global $wpdb;
            $wpaicgChatLogTable = $wpdb->prefix . 'wpaicg_chatlogs';
            if($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s",$wpaicgChatLogTable)) != $wpaicgChatLogTable) {
                $charset_collate = $wpdb->get_charset_collate();
                $sql = "CREATE TABLE ".$wpaicgChatLogTable." (
    `id` mediumint(11) NOT NULL AUTO_INCREMENT,
    `log_session` VARCHAR(255) NOT NULL,
    `data` LONGTEXT NOT NULL,
    `page_title` TEXT DEFAULT NULL,
    `source` VARCHAR(255) DEFAULT NULL,
    `created_at` VARCHAR(255) NOT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                $wpdb->query( $sql );
            }
            $wpaicgChatTokensTable = $wpdb->prefix . 'wpaicg_chattokens';
            if($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s",$wpaicgChatTokensTable)) != $wpaicgChatTokensTable) {
                $charset_collate = $wpdb->get_charset_collate();
                $sql = "CREATE TABLE ".$wpaicgChatTokensTable." (
    `id` mediumint(11) NOT NULL AUTO_INCREMENT,
    `tokens` VARCHAR(255) DEFAULT NULL,
    `user_id` VARCHAR(255) DEFAULT NULL,
    `session_id` VARCHAR(255) DEFAULT NULL,
    `source` VARCHAR(255) DEFAULT NULL,
    `created_at` VARCHAR(255) NOT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                $wpdb->query( $sql );

            }
        }

        public function wpaicg_menu()
        {
            add_submenu_page(
                'wpaicg',
                esc_html__('ChatGPT','gpt3-ai-content-generator'),
                esc_html__('ChatGPT','gpt3-ai-content-generator'),
                'wpaicg_chatgpt',
                'wpaicg_chatgpt',
                array( $this, 'wpaicg_chatmode' ),
                3
            );
        }

        public function wpaicg_chatmode()
        {
            include WPAICG_PLUGIN_DIR . 'admin/extra/wpaicg_chatmode.php';
        }

        public function wpaicg_get_cookie_id()
        {
            if(!function_exists('PasswordHash')){
                require_once ABSPATH . 'wp-includes/class-phpass.php';
            }
            if(isset($_COOKIE['wpaicg_chat_client_id']) && !empty($_COOKIE['wpaicg_chat_client_id'])){
                return $_COOKIE['wpaicg_chat_client_id'];
            }
            else{
                $hasher      = new \PasswordHash( 8, false );
                $cookie_id = 't_' . substr( md5( $hasher->get_random_bytes( 32 ) ), 2 );
                setcookie('wpaicg_chat_client_id', $cookie_id, time() + 604800, COOKIEPATH, COOKIE_DOMAIN);
                return $cookie_id;
            }
        }

        public function wpaicg_chatbox_message()
        {
            global  $wpdb ;
            $wpaicg_client_id = $this->wpaicg_get_cookie_id();
            $wpaicg_result = array(
                'status' => 'error',
                'msg'    => esc_html__('Something went wrong','gpt3-ai-content-generator'),
            );
            $open_ai = WPAICG_OpenAI::get_instance()->openai();
            if (!$open_ai) {
                $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
                exit;
            }
            $wpaicg_save_request = false;
            $wpaicg_nonce = sanitize_text_field($_REQUEST['_wpnonce']);
            if ( !wp_verify_nonce( $wpaicg_nonce, 'wpaicg-chatbox' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
            } else {
                $wpaicg_message = ( isset( $_REQUEST['message'] ) && !empty($_REQUEST['message']) ? sanitize_text_field( $_REQUEST['message'] ) : '' );
//                $wpaicg_message = urldecode($wpaicg_message);
                $url = ( isset( $_REQUEST['url'] ) && !empty($_REQUEST['url']) ? sanitize_text_field( $_REQUEST['url'] ) : '' );
                $wpaicg_pinecone_api = get_option('wpaicg_pinecone_api', '');
                $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment', '');
                $wpaicg_total_tokens = 0;
                $wpaicg_limited_tokens = false;
                $wpaicg_token_usage_client = 0;
                $wpaicg_token_limit_message = esc_html__('You have reached your token limit.','gpt3-ai-content-generator');
                $wpaicg_limited_tokens_number = 0;
                $wpaicg_chat_source = 'widget';
                $wpaicg_chat_temperature = get_option('wpaicg_chat_temperature',$open_ai->temperature);
                $wpaicg_chat_max_tokens = get_option('wpaicg_chat_max_tokens',$open_ai->max_tokens);
                $wpaicg_chat_top_p = get_option('wpaicg_chat_top_p',$open_ai->top_p);
                $wpaicg_chat_best_of = get_option('wpaicg_chat_best_of',$open_ai->best_of);
                $wpaicg_chat_frequency_penalty = get_option('wpaicg_chat_frequency_penalty',$open_ai->frequency_penalty);
                $wpaicg_chat_presence_penalty = get_option('wpaicg_chat_presence_penalty',$open_ai->presence_penalty);
                if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'wpaicg_chat_shortcode_message') {
                    $wpaicg_chat_source = 'shortcode';
                }
                $wpaicg_moderation = false;
                $wpaicg_moderation_model = 'text-moderation-latest';
                $wpaicg_moderation_notice = esc_html__('Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.','gpt3-ai-content-generator');
                if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'wpaicg_chat_shortcode_message'){
                    $table = $wpdb->prefix . 'wpaicg';
                    $existingValue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE name = %s", 'wpaicg_settings' ), ARRAY_A );
                    $wpaicg_chat_shortcode_options = get_option('wpaicg_chat_shortcode_options',[]);
                    $default_setting = array(
                        'language' => 'en',
                        'tone' => 'friendly',
                        'profession' => 'none',
                        'model' => 'text-davinci-003',
                        'temperature' => $existingValue['temperature'],
                        'max_tokens' => $existingValue['max_tokens'],
                        'top_p' => $existingValue['top_p'],
                        'best_of' => $existingValue['best_of'],
                        'frequency_penalty' => $existingValue['frequency_penalty'],
                        'presence_penalty' => $existingValue['presence_penalty'],
                        'ai_name' => esc_html__('AI','gpt3-ai-content-generator'),
                        'you' => esc_html__('You','gpt3-ai-content-generator'),
                        'ai_thinking' => esc_html__('AI Thinking','gpt3-ai-content-generator'),
                        'placeholder' => esc_html__('Type a message','gpt3-ai-content-generator'),
                        'welcome' => esc_html__('Hello human, I am a GPT powered AI chat bot. Ask me anything!','gpt3-ai-content-generator'),
                        'remember_conversation' => 'yes',
                        'conversation_cut' => 10,
                        'content_aware' => 'yes',
                        'embedding' =>  false,
                        'embedding_type' =>  false,
                        'embedding_top' =>  false,
                        'embedding_index' => '',
                        'no_answer' => '',
                        'fontsize' => 13,
                        'fontcolor' => '#fff',
                        'user_bg_color' => '#444654',
                        'ai_bg_color' => '#343541',
                        'ai_icon_url' => '',
                        'ai_icon' => 'default',
                        'use_avatar' => false,
                        'save_logs' => false,
                        'chat_addition' => false,
                        'chat_addition_text' => '',
                        'user_aware' => 'no',
                        'user_limited' => false,
                        'guest_limited' => false,
                        'user_tokens' => 0,
                        'limited_message'=> esc_html__('You have reached your token limit.','gpt3-ai-content-generator'),
                        'guest_tokens' => 0,
                        'moderation' => false,
                        'moderation_model' => 'text-moderation-latest',
                        'moderation_notice' => esc_html__('Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.','gpt3-ai-content-generator'),
                        'role_limited' => false,
                        'limited_roles' => [],
                        'log_request' => false
                    );
                    $wpaicg_settings = shortcode_atts($default_setting, $wpaicg_chat_shortcode_options);

                    if(isset($_REQUEST['wpaicg_chat_shortcode_options']) && is_array($_REQUEST['wpaicg_chat_shortcode_options'])){
                        $wpaicg_chat_shortcode_options = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['wpaicg_chat_shortcode_options']);
                        $wpaicg_settings = shortcode_atts($wpaicg_settings, $wpaicg_chat_shortcode_options);
                    }
                    $wpaicg_save_request = isset($wpaicg_settings['log_request']) && $wpaicg_settings['log_request'] ? true : false;
                    $wpaicg_chat_embedding = isset($wpaicg_settings['embedding']) && $wpaicg_settings['embedding'] ? true : false;
                    $wpaicg_chat_embedding_type = isset($wpaicg_settings['embedding_type']) ? $wpaicg_settings['embedding_type'] : '' ;
                    $wpaicg_chat_no_answer = isset($wpaicg_settings['no_answer']) ? $wpaicg_settings['no_answer'] : '' ;
                    $wpaicg_chat_embedding_top = isset($wpaicg_settings['embedding_top']) ? $wpaicg_settings['embedding_top'] : 1 ;
                    $wpaicg_chat_no_answer = empty($wpaicg_chat_no_answer) ? 'I dont know' : $wpaicg_chat_no_answer;
                    $wpaicg_chat_with_embedding = false;
                    $wpaicg_chat_language = isset($wpaicg_settings['language']) ? $wpaicg_settings['language'] : 'en' ;
                    $wpaicg_chat_tone = isset($wpaicg_settings['tone']) ? $wpaicg_settings['tone'] : 'friendly' ;
                    $wpaicg_chat_proffesion = isset($wpaicg_settings['profession']) ? $wpaicg_settings['profession'] : 'none' ;
                    $wpaicg_chat_remember_conversation = isset($wpaicg_settings['remember_conversation']) ? $wpaicg_settings['remember_conversation'] : 'yes' ;
                    $wpaicg_chat_content_aware = isset($wpaicg_settings['content_aware']) ? $wpaicg_settings['content_aware'] : 'yes' ;
                    $wpaicg_ai_model = isset($wpaicg_settings['model']) ? $wpaicg_settings['model'] : 'gpt-3.5-turbo' ;
                    $wpaicg_conversation_cut = isset($wpaicg_settings['conversation_cut']) ? $wpaicg_settings['conversation_cut'] : 10 ;
                    $wpaicg_conversation_url = 'wpaicg_conversation_url_shortcode';
                    $wpaicg_save_logs = isset($wpaicg_settings['save_logs']) && $wpaicg_settings['save_logs'] ? true : false;
                    $wpaicg_chat_addition = isset($wpaicg_settings['chat_addition']) && $wpaicg_settings['chat_addition'] ? true : false;
                    $wpaicg_chat_addition_text = isset($wpaicg_settings['chat_addition_text']) && !empty($wpaicg_settings['chat_addition_text']) ? $wpaicg_settings['chat_addition_text'] : '';
                    $wpaicg_user_aware = isset($wpaicg_settings['user_aware']) ? $wpaicg_settings['user_aware'] : 'no';
                    $wpaicg_token_limit_message = isset($wpaicg_settings['limited_message']) ? $wpaicg_settings['limited_message'] : $wpaicg_token_limit_message;
                    $wpaicg_chat_temperature = isset($wpaicg_settings['temperature']) && !empty($wpaicg_settings['temperature']) ? $wpaicg_settings['temperature'] :$wpaicg_chat_temperature;
                    $wpaicg_chat_max_tokens = isset($wpaicg_settings['max_tokens']) && !empty($wpaicg_settings['max_tokens']) ? $wpaicg_settings['max_tokens'] :$wpaicg_chat_max_tokens;
                    $wpaicg_chat_top_p = isset($wpaicg_settings['top_p']) && !empty($wpaicg_settings['top_p']) ? $wpaicg_settings['top_p'] :$wpaicg_chat_top_p;
                    $wpaicg_chat_best_of = isset($wpaicg_settings['best_of']) && !empty($wpaicg_settings['best_of']) ? $wpaicg_settings['best_of'] :$wpaicg_chat_best_of;
                    $wpaicg_chat_frequency_penalty = isset($wpaicg_settings['frequency_penalty']) && !empty($wpaicg_settings['frequency_penalty']) ? $wpaicg_settings['frequency_penalty'] :$wpaicg_chat_frequency_penalty;
                    $wpaicg_chat_presence_penalty = isset($wpaicg_settings['presence_penalty']) && !empty($wpaicg_settings['presence_penalty']) ? $wpaicg_settings['presence_penalty'] :$wpaicg_chat_presence_penalty;
                    if(isset($wpaicg_settings['embedding_index']) && !empty($wpaicg_settings['embedding_index'])){
                        $wpaicg_pinecone_environment = $wpaicg_settings['embedding_index'];
                    }
                    if(is_user_logged_in() && $wpaicg_settings['user_limited'] && $wpaicg_settings['user_tokens'] > 0){
                        $wpaicg_limited_tokens = true;
                        $wpaicg_limited_tokens_number = $wpaicg_settings['user_tokens'];
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
                            $wpaicg_limited_tokens = true;
                            $wpaicg_limited_tokens_number = $limited_current_role;
                        }
                        else{
                            $wpaicg_limited_tokens = false;
                        }
                    }
                    /*End check limit base role*/
                    if(!is_user_logged_in() && $wpaicg_settings['guest_limited'] && $wpaicg_settings['guest_tokens'] > 0){
                        $wpaicg_limited_tokens = true;
                        $wpaicg_limited_tokens_number = $wpaicg_settings['guest_tokens'];
                    }
                    if(wpaicg_util_core()->wpaicg_is_pro()) {
                        $wpaicg_chat_pro = WPAICG_Chat_Pro::get_instance();
                        $wpaicg_moderation = $wpaicg_chat_pro->activated($wpaicg_settings);
                        $wpaicg_moderation_model = $wpaicg_chat_pro->model($wpaicg_settings);
                        $wpaicg_moderation_notice = $wpaicg_chat_pro->notice($wpaicg_settings);
                    }
                }
                else {
                    $wpaicg_limited_tokens = false;
                    $wpaicg_chat_widget = get_option('wpaicg_chat_widget', []);
                    $wpaicg_chat_embedding = get_option('wpaicg_chat_embedding', false);
                    $wpaicg_chat_embedding_type = get_option('wpaicg_chat_embedding_type', false);
                    $wpaicg_chat_no_answer = get_option('wpaicg_chat_no_answer', '');
                    $wpaicg_chat_embedding_top = get_option('wpaicg_chat_embedding_top', 1);
                    $wpaicg_chat_no_answer = empty($wpaicg_chat_no_answer) ? 'I dont know' : $wpaicg_chat_no_answer;
                    $wpaicg_chat_with_embedding = false;
                    $wpaicg_chat_language = get_option('wpaicg_chat_language', 'en');
                    $wpaicg_chat_tone = isset($wpaicg_chat_widget['tone']) && !empty($wpaicg_chat_widget['tone']) ? $wpaicg_chat_widget['tone'] : 'friendly';
                    $wpaicg_chat_proffesion = isset($wpaicg_chat_widget['proffesion']) && !empty($wpaicg_chat_widget['proffesion']) ? $wpaicg_chat_widget['proffesion'] : 'none';
                    $wpaicg_chat_remember_conversation = isset($wpaicg_chat_widget['remember_conversation']) && !empty($wpaicg_chat_widget['remember_conversation']) ? $wpaicg_chat_widget['remember_conversation'] : 'yes';
                    $wpaicg_chat_content_aware = isset($wpaicg_chat_widget['content_aware']) && !empty($wpaicg_chat_widget['content_aware']) ? $wpaicg_chat_widget['content_aware'] : 'yes';
                    $wpaicg_ai_model = get_option('wpaicg_chat_model', 'text-davinci-003');
                    $wpaicg_conversation_cut = get_option('wpaicg_conversation_cut', 10);
                    $wpaicg_conversation_url = 'wpaicg_conversation_url';
                    $wpaicg_save_logs = isset($wpaicg_chat_widget['save_logs']) && $wpaicg_chat_widget['save_logs'] ? true : false;
                    $wpaicg_chat_addition = get_option('wpaicg_chat_addition',false);
                    $wpaicg_chat_addition_text = get_option('wpaicg_chat_addition_text','');
                    $wpaicg_user_aware = isset($wpaicg_chat_widget['user_aware']) ? $wpaicg_chat_widget['user_aware'] : 'no';
                    $wpaicg_token_limit_message = isset($wpaicg_chat_widget['limited_message']) ? $wpaicg_chat_widget['limited_message'] : $wpaicg_token_limit_message;
                    $wpaicg_save_request = isset($wpaicg_chat_widget['log_request']) && $wpaicg_chat_widget['log_request'] ? true : false;
                    if(is_user_logged_in() && $wpaicg_chat_widget['user_limited'] && $wpaicg_chat_widget['user_tokens'] > 0){
                        $wpaicg_limited_tokens = true;
                        $wpaicg_limited_tokens_number = $wpaicg_chat_widget['user_tokens'];
                    }
                    /*Check limit base role*/
                    if(is_user_logged_in() && isset($wpaicg_chat_widget['role_limited']) && $wpaicg_chat_widget['role_limited']){
                        $wpaicg_roles = ( array )wp_get_current_user()->roles;
                        $limited_current_role = 0;
                        foreach ($wpaicg_roles as $wpaicg_role) {
                            if(
                                isset($wpaicg_chat_widget['limited_roles'])
                                && is_array($wpaicg_chat_widget['limited_roles'])
                                && isset($wpaicg_chat_widget['limited_roles'][$wpaicg_role])
                                && $wpaicg_chat_widget['limited_roles'][$wpaicg_role] > $limited_current_role
                            ){
                                $limited_current_role = $wpaicg_chat_widget['limited_roles'][$wpaicg_role];
                            }
                        }
                        if($limited_current_role > 0){
                            $wpaicg_limited_tokens = true;
                            $wpaicg_limited_tokens_number = $limited_current_role;
                        }
                        else{
                            $wpaicg_limited_tokens = false;
                        }
                    }
                    /*End check limit base role*/
                    if(!is_user_logged_in() && $wpaicg_chat_widget['guest_limited'] && $wpaicg_chat_widget['guest_tokens'] > 0){
                        $wpaicg_limited_tokens = true;
                        $wpaicg_limited_tokens_number = $wpaicg_chat_widget['guest_tokens'];
                    }
                    if(wpaicg_util_core()->wpaicg_is_pro()){
                        $wpaicg_chat_pro = WPAICG_Chat_Pro::get_instance();
                        $wpaicg_moderation = $wpaicg_chat_pro->activated($wpaicg_chat_widget);
                        $wpaicg_moderation_model = $wpaicg_chat_pro->model($wpaicg_chat_widget);
                        $wpaicg_moderation_notice = $wpaicg_chat_pro->notice($wpaicg_chat_widget);
                    }
                    if(isset($wpaicg_chat_widget['embedding_index']) && !empty($wpaicg_chat_widget['embedding_index'])){
                        $wpaicg_pinecone_environment = $wpaicg_chat_widget['embedding_index'];
                    }
                }
                if(isset($_REQUEST['bot_id']) && !empty($_REQUEST['bot_id'])){
                    $wpaicg_bot = get_post(sanitize_text_field($_REQUEST['bot_id']));
                    if($wpaicg_bot) {
                        $wpaicg_limited_tokens = false;
                        if(strpos($wpaicg_bot->post_content,'\"') !== false) {
                            $wpaicg_bot->post_content = str_replace('\"', '&quot;', $wpaicg_bot->post_content);
                        }
                        if(strpos($wpaicg_bot->post_content,"\'") !== false) {
                            $wpaicg_bot->post_content = str_replace('\\', '', $wpaicg_bot->post_content);
                        }
                        $wpaicg_chat_widget = json_decode($wpaicg_bot->post_content, true);
                        $wpaicg_bot_type = isset($wpaicg_chat_widget['type']) && $wpaicg_chat_widget['type'] == 'shortcode' ? 'Shortcode ' : 'Widget ';
                        $wpaicg_chat_embedding = isset($wpaicg_chat_widget['embedding']) && $wpaicg_chat_widget['embedding'] ? true : false;
                        $wpaicg_chat_embedding_type = isset($wpaicg_chat_widget['embedding_type']) ? $wpaicg_chat_widget['embedding_type'] : '' ;
                        $wpaicg_chat_no_answer = isset($wpaicg_chat_widget['no_answer']) ? $wpaicg_chat_widget['no_answer'] : '' ;
                        $wpaicg_chat_embedding_top = isset($wpaicg_chat_widget['embedding_top']) ? $wpaicg_chat_widget['embedding_top'] : 1 ;
                        $wpaicg_chat_no_answer = empty($wpaicg_chat_no_answer) ? 'I dont know' : $wpaicg_chat_no_answer;
                        $wpaicg_chat_with_embedding = false;
                        $wpaicg_chat_language = isset($wpaicg_chat_widget['language']) ? $wpaicg_chat_widget['language'] : 'en' ;
                        $wpaicg_chat_tone = isset($wpaicg_chat_widget['tone']) ? $wpaicg_chat_widget['tone'] : 'friendly' ;
                        $wpaicg_chat_proffesion = isset($wpaicg_chat_widget['proffesion']) ? $wpaicg_chat_widget['proffesion'] : 'none' ;
                        $wpaicg_chat_remember_conversation = isset($wpaicg_chat_widget['remember_conversation']) ? $wpaicg_chat_widget['remember_conversation'] : 'yes' ;
                        $wpaicg_chat_content_aware = isset($wpaicg_chat_widget['content_aware']) ? $wpaicg_chat_widget['content_aware'] : 'yes' ;
                        $wpaicg_ai_model = isset($wpaicg_chat_widget['model']) ? $wpaicg_chat_widget['model'] : 'gpt-3.5-turbo' ;
                        $wpaicg_conversation_cut = isset($wpaicg_chat_widget['conversation_cut']) ? $wpaicg_chat_widget['conversation_cut'] : 10 ;
                        $wpaicg_conversation_url = 'wpaicg_conversation_url_custom_bot_'.$wpaicg_bot->ID;
                        $wpaicg_save_logs = isset($wpaicg_chat_widget['save_logs']) && $wpaicg_chat_widget['save_logs'] ? true : false;
                        $wpaicg_chat_addition = isset($wpaicg_chat_widget['chat_addition']) && $wpaicg_chat_widget['chat_addition'] ? true : false;
                        $wpaicg_chat_addition_text = isset($wpaicg_chat_widget['chat_addition_text']) && !empty($wpaicg_chat_widget['chat_addition_text']) ? $wpaicg_chat_widget['chat_addition_text'] : '';
                        $wpaicg_user_aware = isset($wpaicg_chat_widget['user_aware']) ? $wpaicg_chat_widget['user_aware'] : 'no';
                        $wpaicg_token_limit_message = isset($wpaicg_chat_widget['limited_message']) ? $wpaicg_chat_widget['limited_message'] : $wpaicg_token_limit_message;
                        $wpaicg_save_request = isset($wpaicg_chat_widget['log_request']) && $wpaicg_chat_widget['log_request'] ? true : false;
                        $wpaicg_chat_temperature = isset($wpaicg_chat_widget['temperature']) && !empty($wpaicg_chat_widget['temperature']) ? $wpaicg_chat_widget['temperature'] :$wpaicg_chat_temperature;
                        $wpaicg_chat_max_tokens = isset($wpaicg_chat_widget['max_tokens']) && !empty($wpaicg_chat_widget['max_tokens']) ? $wpaicg_chat_widget['max_tokens'] :$wpaicg_chat_max_tokens;
                        $wpaicg_chat_top_p = isset($wpaicg_chat_widget['top_p']) && !empty($wpaicg_chat_widget['top_p']) ? $wpaicg_chat_widget['top_p'] :$wpaicg_chat_top_p;
                        $wpaicg_chat_best_of = isset($wpaicg_chat_widget['best_of']) && !empty($wpaicg_chat_widget['best_of']) ? $wpaicg_chat_widget['best_of'] :$wpaicg_chat_best_of;
                        $wpaicg_chat_frequency_penalty = isset($wpaicg_chat_widget['frequency_penalty']) && !empty($wpaicg_chat_widget['frequency_penalty']) ? $wpaicg_chat_widget['frequency_penalty'] :$wpaicg_chat_frequency_penalty;
                        $wpaicg_chat_presence_penalty = isset($wpaicg_chat_widget['presence_penalty']) && !empty($wpaicg_chat_widget['presence_penalty']) ? $wpaicg_chat_widget['presence_penalty'] :$wpaicg_chat_presence_penalty;
                        if(is_user_logged_in() && $wpaicg_chat_widget['user_limited'] && $wpaicg_chat_widget['user_tokens'] > 0){
                            $wpaicg_limited_tokens = true;
                            $wpaicg_limited_tokens_number = $wpaicg_chat_widget['user_tokens'];
                        }
                        /*Check limit base role*/
                        if(is_user_logged_in() && isset($wpaicg_chat_widget['role_limited']) && $wpaicg_chat_widget['role_limited']){
                            $wpaicg_roles = ( array )wp_get_current_user()->roles;
                            $limited_current_role = 0;
                            foreach ($wpaicg_roles as $wpaicg_role) {
                                if(
                                    isset($wpaicg_chat_widget['limited_roles'])
                                    && is_array($wpaicg_chat_widget['limited_roles'])
                                    && isset($wpaicg_chat_widget['limited_roles'][$wpaicg_role])
                                    && $wpaicg_chat_widget['limited_roles'][$wpaicg_role] > $limited_current_role
                                ){
                                    $limited_current_role = $wpaicg_chat_widget['limited_roles'][$wpaicg_role];
                                }
                            }
                            if($limited_current_role > 0){
                                $wpaicg_limited_tokens = true;
                                $wpaicg_limited_tokens_number = $limited_current_role;
                            }
                            else{
                                $wpaicg_limited_tokens = false;
                            }
                        }
                        /*End check limit base role*/
                        if(!is_user_logged_in() && $wpaicg_chat_widget['guest_limited'] && $wpaicg_chat_widget['guest_tokens'] > 0){
                            $wpaicg_limited_tokens = true;
                            $wpaicg_limited_tokens_number = $wpaicg_chat_widget['guest_tokens'];
                        }
                        if(wpaicg_util_core()->wpaicg_is_pro()){
                            $wpaicg_chat_pro = WPAICG_Chat_Pro::get_instance();
                            $wpaicg_moderation = $wpaicg_chat_pro->activated($wpaicg_chat_widget);
                            $wpaicg_moderation_model = $wpaicg_chat_pro->model($wpaicg_chat_widget);
                            $wpaicg_moderation_notice = $wpaicg_chat_pro->notice($wpaicg_chat_widget);
                        }
                        $wpaicg_chat_source = $wpaicg_bot_type.'ID: '.$wpaicg_bot->ID;
                        if(isset($wpaicg_chat_widget['embedding_index']) && !empty($wpaicg_chat_widget['embedding_index'])){
                            $wpaicg_pinecone_environment = $wpaicg_chat_widget['embedding_index'];
                        }
                    }
                }
                if(!is_user_logged_in()){
                    $wpaicg_user_aware = 'no';
                }
                $wpaicg_human_name = 'Human';
                $wpaicg_user_name = '';
                if($wpaicg_user_aware == 'yes'){
                    $wpaicg_human_name = wp_get_current_user()->user_login;
                    if(!empty(wp_get_current_user()->display_name)) {
                        $wpaicg_user_name = 'Username: ' . wp_get_current_user()->display_name;
                        $wpaicg_human_name = wp_get_current_user()->display_name;
                    }
                }
                /*Token handing*/
                $wpaicg_chat_token_id = false;
                if($wpaicg_limited_tokens){
                    if(is_user_logged_in()){
                        $wpaicg_chat_token_log = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpaicg_chattokens WHERE source = %s AND user_id=%d",$wpaicg_chat_source,get_current_user_id()));
                        $wpaicg_token_usage_client = $wpaicg_chat_token_log ? $wpaicg_chat_token_log->tokens : 0;
                    }
                    else{
                        $wpaicg_chat_token_log = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpaicg_chattokens WHERE source = %s AND session_id=%s",$wpaicg_chat_source,$wpaicg_client_id));
                        $wpaicg_token_usage_client = $wpaicg_chat_token_log ? $wpaicg_chat_token_log->tokens : 0;
                    }
                    $wpaicg_chat_token_id = $wpaicg_chat_token_log ? $wpaicg_chat_token_log->id : false;
                    if(
                        $wpaicg_token_usage_client > 0
                        && $wpaicg_limited_tokens_number > 0
                        && $wpaicg_token_usage_client > $wpaicg_limited_tokens_number
                    ){
                        /*check current user limited*/
                        $still_limited = true;
                        if(is_user_logged_in()) {
                            $user_meta_key = 'wpaicg_chat_tokens';
                            $user_tokens = get_user_meta(get_current_user_id(), $user_meta_key, true);
                            if(!empty($user_tokens) && $user_tokens > 0){
                                $still_limited = false;
                            }
                        }
                        if($still_limited) {
                            $wpaicg_result['msg'] = $wpaicg_token_limit_message;
                            wp_send_json($wpaicg_result);
                            exit;
                        }
                    }
                }
                /*End check token handing*/
                /*Start check Log*/
                $wpaicg_chat_log_id = false;
                $wpaicg_chat_log_data = array();

                /*Check Audio Converter*/
                if(isset($_FILES['audio']) && empty($_FILES['audio']['error'])){
                    $file = $_FILES['audio'];
                    $file_name = sanitize_file_name(basename($file['name']));
                    $filetype = wp_check_filetype($file_name);
                    $mime_types = ['mp3' => 'audio/mpeg','mp4' => 'video/mp4','mpeg' => 'video/mpeg','m4a' => 'audio/m4a','wav' => 'audio/wav','webm' => 'video/webm'];
                    if(!in_array($filetype['type'], $mime_types)){
                        $wpaicg_result['msg'] = esc_html__('We only accept mp3, mp4, mpeg, mpga, m4a, wav, or webm.','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    if($file['size'] > 26214400){
                        $wpaicg_result['msg'] = esc_html__('Audio file maximum 25MB','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $tmp_file = $file['tmp_name'];
                    $data_audio_request = array(
                        'audio' => array(
                            'filename' => $file_name,
                            'data' => file_get_contents($tmp_file)
                        ),
                        'model' => 'whisper-1',
                        'response_format' => 'json'
                    );
                    $completion = $open_ai->transcriptions($data_audio_request);
                    $completion = json_decode($completion);
                    if($completion && isset($completion->error)){
                        $wpaicg_result['msg'] = $completion->error->message;
                        if(empty($wpaicg_result['msg']) && isset($completion->error->code) && $completion->error->code == 'invalid_api_key'){
                            $wpaicg_result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                        }
                        wp_send_json($wpaicg_result);
                    }
                    $wpaicg_message = $completion->text;
                }
                if(!empty($wpaicg_message) && $wpaicg_save_logs) {
                    $wpaicg_current_context_id = isset($_REQUEST['post_id']) && !empty($_REQUEST['post_id']) ? sanitize_text_field($_REQUEST['post_id']) : '';
                    $wpaicg_current_context_title = !empty($wpaicg_current_context_id) ? get_the_title($wpaicg_current_context_id) : '';
                    $wpaicg_unique_chat = md5($wpaicg_client_id . '-' . $wpaicg_current_context_id);
                    $wpaicg_chat_log_check = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "wpaicg_chatlogs WHERE source=%s AND log_session=%s",$wpaicg_chat_source,$wpaicg_unique_chat));
                    if (!$wpaicg_chat_log_check) {
                        $wpdb->insert($wpdb->prefix . 'wpaicg_chatlogs', array(
                            'log_session' => $wpaicg_unique_chat,
                            'data' => json_encode(array()),
                            'page_title' => $wpaicg_current_context_title,
                            'source' => $wpaicg_chat_source,
                            'created_at' => time()
                        ));
                        $wpaicg_chat_log_id = $wpdb->insert_id;
                    } else {
                        $wpaicg_chat_log_id = $wpaicg_chat_log_check->id;
                        $wpaicg_current_log_data = json_decode($wpaicg_chat_log_check->data, true);
                        if ($wpaicg_current_log_data && is_array($wpaicg_current_log_data)) {
                            $wpaicg_chat_log_data = $wpaicg_current_log_data;
                        }
                    }
                    $wpaicg_chat_log_data[] = array('message' => $wpaicg_message, 'type' => 'user', 'date' => time(),'ip' => $this->getIpAddress());
                }
                /*End Check Log*/
                /*Check Moderation*/
                if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
                    $wpaicg_moderation = false;
                }
                if(!empty($wpaicg_message) && $wpaicg_moderation){
                    $wpaicg_chat_pro->moderation($open_ai,$wpaicg_message, $wpaicg_moderation_model, $wpaicg_moderation_notice, $wpaicg_save_logs, $wpaicg_chat_log_id,$wpaicg_chat_log_data);
                }
                /*End Check Moderation*/
                $wpaicg_embedding_content = '';
                if($wpaicg_chat_embedding){
                    /*Using embeddings only*/
                    $namespace = false;
                    if(isset($_REQUEST['namespace']) && !empty($_REQUEST['namespace'])){
                        $namespace = sanitize_text_field($_REQUEST['namespace']);
                    }
                    $wpaicg_embeddings_result = $this->wpaicg_embeddings_result($open_ai,$wpaicg_pinecone_api, $wpaicg_pinecone_environment, $wpaicg_message, $wpaicg_chat_embedding_top,$namespace);
                    if($wpaicg_embeddings_result['status'] == 'empty'){
                        $wpaicg_chat_with_embedding = false;
                    }
                    else {
                        if (!$wpaicg_chat_embedding_type || empty($wpaicg_chat_embedding_type)) {
                            $wpaicg_result['status'] = $wpaicg_embeddings_result['status'];
                            $wpaicg_result['data'] = empty($wpaicg_embeddings_result['data']) ? $wpaicg_chat_no_answer : $wpaicg_embeddings_result['data'];
                            $wpaicg_result['msg'] = empty($wpaicg_embeddings_result['data']) ? $wpaicg_chat_no_answer : $wpaicg_embeddings_result['data'];
                            $this->wpaicg_save_chat_log($wpaicg_chat_log_id, $wpaicg_chat_log_data, 'ai', $wpaicg_result['data']);
                            wp_send_json($wpaicg_result);
                            exit;
                        } else {
                            $wpaicg_result['status'] = $wpaicg_embeddings_result['status'];
                            if ($wpaicg_result['status'] == 'error') {
                                $wpaicg_result['msg'] = empty($wpaicg_embeddings_result['data']) ? $wpaicg_chat_no_answer : $wpaicg_embeddings_result['data'];
                                $this->wpaicg_save_chat_log($wpaicg_chat_log_id, $wpaicg_chat_log_data, 'ai', $wpaicg_result['data']);
                                wp_send_json($wpaicg_result);
                                exit;
                            } else {
                                $wpaicg_total_tokens += $wpaicg_embeddings_result['tokens']; // Add embedding tokens
                                $wpaicg_embedding_content = $wpaicg_embeddings_result['data'];
                            }
                            $wpaicg_chat_with_embedding = true;
                        }
                    }
                }
                if ($wpaicg_chat_remember_conversation == 'yes') {
                    $wpaicg_session_page = md5($wpaicg_client_id.$url);

                    if(!isset($_COOKIE[$wpaicg_conversation_url]) || empty($_COOKIE[$wpaicg_conversation_url])){
                        setcookie($wpaicg_conversation_url,$wpaicg_session_page,time()+86400,COOKIEPATH, COOKIE_DOMAIN);
                        $wpaicg_conversation_messages = array();
                    }
                    else{
                        $wpaicg_conversation_messages = isset($_COOKIE[$wpaicg_session_page]) ? $_COOKIE[$wpaicg_session_page] : '';
                        $wpaicg_conversation_messages = str_replace("\\",'',$wpaicg_conversation_messages);
                        if(!empty($wpaicg_conversation_messages && is_serialized($wpaicg_conversation_messages))){
                            $wpaicg_conversation_messages = unserialize($wpaicg_conversation_messages);
                            $wpaicg_conversation_messages = $wpaicg_conversation_messages ? $wpaicg_conversation_messages : array();
                        }
                        else{
                            $wpaicg_conversation_messages = array();
                        }
                    }
                    $wpaicg_conversation_messages_length = count($wpaicg_conversation_messages);
                    if ($wpaicg_conversation_messages_length > $wpaicg_conversation_cut) {
                        $wpaicg_conversation_messages_start = $wpaicg_conversation_messages_length - $wpaicg_conversation_cut;
                    } else {
                        $wpaicg_conversation_messages_start = 0;
                    }
                    $wpaicg_conversation_end_messages = array_splice($wpaicg_conversation_messages, $wpaicg_conversation_messages_start, $wpaicg_conversation_messages_length);
                }
                if (!empty($wpaicg_message)) {
                    $wpaicg_language_file = WPAICG_PLUGIN_DIR . 'admin/chat/languages/' . $wpaicg_chat_language . '.json';
                    if (!file_exists($wpaicg_language_file)) {
                        $wpaicg_language_file = WPAICG_PLUGIN_DIR . 'admin/chat/languages/en.json';
                    }
                    $wpaicg_language_json = file_get_contents($wpaicg_language_file);
                    $wpaicg_languages = json_decode($wpaicg_language_json, true);
                    $wpaicg_chat_tone = isset($wpaicg_languages['tone'][$wpaicg_chat_tone]) ? $wpaicg_languages['tone'][$wpaicg_chat_tone] : 'Professional';
                    $wpaicg_chat_proffesion = isset($wpaicg_languages['proffesion'][$wpaicg_chat_proffesion]) ? $wpaicg_languages['proffesion'][$wpaicg_chat_proffesion] : 'none';


                    $wpaicg_greeting_key = 'greeting';

                    if ($wpaicg_chat_proffesion != 'none') {
                        $wpaicg_greeting_key .= '_proffesion';
                    }
                    $wpaicg_chat_greeting_message = sprintf($wpaicg_languages[$wpaicg_greeting_key], $wpaicg_chat_tone, $wpaicg_chat_proffesion . ".\n");
                    if(!empty($wpaicg_chat_addition_text)){
                        $site_url = site_url();
                        $parse_url = wp_parse_url($site_url);
                        $domain_name = isset($parse_url['host']) && !empty($parse_url['host']) ? $parse_url['host'] : '';
                        $date = date(get_option( 'date_format'));
                        $sitename = get_bloginfo('name');
                        $wpaicg_chat_addition_text = str_replace('[siteurl]',$site_url, $wpaicg_chat_addition_text);
                        $wpaicg_chat_addition_text = str_replace('[domain]',$domain_name, $wpaicg_chat_addition_text);
                        $wpaicg_chat_addition_text = str_replace('[sitename]',$sitename, $wpaicg_chat_addition_text);
                        $wpaicg_chat_addition_text = str_replace('[date]',$date, $wpaicg_chat_addition_text);
                    }
                    if ($wpaicg_chat_content_aware == 'yes') {
                        if($wpaicg_chat_with_embedding && !empty($wpaicg_embedding_content)){
                            $wpaicg_greeting_key .= '_content';
                            $current_context = '"'.$wpaicg_embedding_content.'"';
                            if ($wpaicg_chat_proffesion != 'none') {
                                $wpaicg_chat_greeting_message = sprintf($wpaicg_languages[$wpaicg_greeting_key], $wpaicg_chat_tone, $wpaicg_chat_proffesion . ".\n", $current_context);
                            } else {
                                $wpaicg_chat_greeting_message = sprintf($wpaicg_languages[$wpaicg_greeting_key], $wpaicg_chat_tone . ".\n", $current_context);
                            }
                            if($wpaicg_chat_addition && !empty($wpaicg_chat_addition_text)){
                                $wpaicg_chat_greeting_message .= ' '.sprintf($wpaicg_languages[$wpaicg_greeting_key.'_extra'], $wpaicg_chat_addition_text);
                            }
                        }
                        elseif(isset($_REQUEST['post_id']) && !empty($_REQUEST['post_id'])){
                            $current_post = get_post(sanitize_text_field($_REQUEST['post_id']));
                            if ($current_post) {
                                $wpaicg_greeting_key .= '_content';
                                $current_context = '"' . strip_tags($current_post->post_title);
                                $current_post_excerpt = str_replace('[...]', '', strip_tags(get_the_excerpt($current_post)));
                                if ($current_post_excerpt !== '') {
                                    $current_post_excerpt = preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
                                        return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                                    }, $current_post_excerpt);
                                    $current_context .= "\n" . $current_post_excerpt;
                                }
                                $current_context .= '"';
                                if ($wpaicg_chat_proffesion != 'none') {
                                    $wpaicg_chat_greeting_message = sprintf($wpaicg_languages[$wpaicg_greeting_key], $wpaicg_chat_tone, $wpaicg_chat_proffesion . ".\n", $current_context);
                                } else {
                                    $wpaicg_chat_greeting_message = sprintf($wpaicg_languages[$wpaicg_greeting_key], $wpaicg_chat_tone . ".\n", $current_context);
                                }
                                if($wpaicg_chat_addition && !empty($wpaicg_chat_addition_text)){
                                    $wpaicg_chat_greeting_message .= ' '.sprintf($wpaicg_languages[$wpaicg_greeting_key.'_extra'], $wpaicg_chat_addition_text);
                                }
                            }
                        }
                        elseif($wpaicg_chat_addition && !empty($wpaicg_chat_addition_text)){
                            $wpaicg_greeting_key .= '_content';
                            $wpaicg_chat_greeting_message .= ' '.sprintf($wpaicg_languages[$wpaicg_greeting_key.'_extra'], $wpaicg_chat_addition_text);
                        }
                    }
                    elseif($wpaicg_chat_addition && !empty($wpaicg_chat_addition_text)){
                        $wpaicg_greeting_key .= '_content';
                        $wpaicg_chat_greeting_message .= ' '.sprintf($wpaicg_languages[$wpaicg_greeting_key.'_extra'], $wpaicg_chat_addition_text);
                    }
                    if(!empty($wpaicg_user_name)){
                        $wpaicg_chat_greeting_message .= '. '.$wpaicg_user_name;
                    }
                    $wpaicg_result['greeting_message'] = $wpaicg_chat_greeting_message;
                    $wpaicg_chatgpt_messages = array();
                    $wpaicg_chatgpt_messages[] = array('role' => 'user', 'content' => html_entity_decode($wpaicg_chat_greeting_message,ENT_QUOTES ,'UTF-8'));
                    if ($wpaicg_chat_remember_conversation == 'yes') {
                        // Clear cookies
                        //$wpaicg_conversation_end_messages = array();
                        $wpaicg_conversation_end_messages[] = $wpaicg_human_name.': ' . $wpaicg_message . "\nAI: ";
                        foreach ($wpaicg_conversation_end_messages as $wpaicg_conversation_end_message) {
                            $wpaicg_chatgpt_message = $wpaicg_conversation_end_message;
                            if(strpos($wpaicg_conversation_end_message, $wpaicg_human_name.': ') !== false){
                                $wpaicg_chatgpt_message = str_replace($wpaicg_human_name.': ','',$wpaicg_chatgpt_message);
                                $wpaicg_chatgpt_message = str_replace("\nAI: ",'',$wpaicg_chatgpt_message);
                                if(!empty($wpaicg_chatgpt_message)) {
                                    $wpaicg_chatgpt_messages[] = array('role' => 'user', 'content' => $wpaicg_chatgpt_message);
                                }
                            }
                            else{
                                if(!empty($wpaicg_chatgpt_message)) {
                                    $wpaicg_chatgpt_messages[] = array('role' => 'assistant', 'content' => $wpaicg_chatgpt_message);
                                }
                            }
                            $wpaicg_chat_greeting_message .= "\n" . $wpaicg_conversation_end_message;
                        }
                        $prompt = $wpaicg_chat_greeting_message;
                    } else {
                        $prompt = $wpaicg_chat_greeting_message. "\n".$wpaicg_human_name.": " . $wpaicg_message . "\nAI: ";
                        $wpaicg_chatgpt_messages[] = array('role' => 'user','content' => $wpaicg_message);
                    }
                    if($wpaicg_ai_model === 'gpt-3.5-turbo' || $wpaicg_ai_model == 'gpt-4' || $wpaicg_ai_model == 'gpt-4-32k'){
                        $wpaicg_data_request = [
                            'model' => $wpaicg_ai_model,
                            'messages' => $wpaicg_chatgpt_messages,
                            'temperature' => floatval($wpaicg_chat_temperature),
                            'max_tokens' => intval($wpaicg_chat_max_tokens),
                            'frequency_penalty' => floatval($wpaicg_chat_frequency_penalty),
                            'presence_penalty' => floatval($wpaicg_chat_presence_penalty),
                            'top_p' => floatval($wpaicg_chat_top_p)
                        ];
                        $complete = $open_ai->chat($wpaicg_data_request);
                    }
                    else {
                        /*Old completion*/
                        $wpaicg_data_request = [
                            'model' => $wpaicg_ai_model,
                            'prompt' => $prompt,
                            'temperature' => floatval($wpaicg_chat_temperature),
                            'max_tokens' => intval($wpaicg_chat_max_tokens),
                            'frequency_penalty' => floatval($wpaicg_chat_frequency_penalty),
                            'presence_penalty' => floatval($wpaicg_chat_presence_penalty),
                            'top_p' => floatval($wpaicg_chat_top_p),
                            'best_of' => intval($wpaicg_chat_best_of)
                        ];
                        $complete = $open_ai->completion($wpaicg_data_request);
                        /*Chat implement*/
                    }
                    $complete = json_decode($complete);
                    if (isset($complete->error)) {
                        $wpaicg_result['status'] = 'error';
                        //$wpaicg_result['data_request'] = $wpaicg_data_request;
                        $wpaicg_result['msg'] = esc_html(trim($complete->error->message));
                        if(empty($wpaicg_result['msg']) && isset($complete->error->code) && $complete->error->code == 'invalid_api_key'){
                            $result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                        }
                        $wpaicg_result['log'] = $wpaicg_chat_log_id;
                        //$wpaicg_result['messages'] = $wpaicg_chatgpt_messages;
                        //$wpaicg_result['prompt'] = $prompt;
                        //$wpaicg_result['chat_embedding'] = $wpaicg_chat_embedding;
                        //$wpaicg_result['chat_embedding_type'] = $wpaicg_chat_embedding_type;
                    } else {
                        if($wpaicg_ai_model === 'gpt-3.5-turbo' || $wpaicg_ai_model == 'gpt-4-32k' || $wpaicg_ai_model == 'gpt-4'){
                            $wpaicg_result['data'] = $complete->choices[0]->message->content;
                        }
                        else {
                            $wpaicg_result['data'] = $complete->choices[0]->text;
                        }
                        $wpaicg_total_tokens += $complete->usage->total_tokens;
                        if(!$wpaicg_save_request){
                            $wpaicg_data_request = false;
                        }
                        $this->wpaicg_save_chat_log($wpaicg_chat_log_id, $wpaicg_chat_log_data, 'ai',$wpaicg_result['data'],$wpaicg_total_tokens,false,$wpaicg_data_request);
                        if(is_user_logged_in() && $wpaicg_limited_tokens){
                            WPAICG_Account::get_instance()->save_log('chat', $wpaicg_total_tokens);
                        }
                        //$wpaicg_result['prompt'] = $prompt;
                        $wpaicg_result['status'] = 'success';
                        $wpaicg_result['log'] = $wpaicg_chat_log_id;
                        //$wpaicg_result['data_request'] = $wpaicg_data_request;
                        //$wpaicg_result['chat_embedding'] = $wpaicg_chat_embedding;
//                        $wpaicg_result['messages'] = $wpaicg_chatgpt_messages;
                        //$wpaicg_result['chat_embedding_type'] = $wpaicg_chat_embedding_type;
                        /*
                         * Save token handing
                         * */
                        //$wpaicg_result['cookie_id'] = $wpaicg_client_id;
                        if($wpaicg_limited_tokens){
                            if($wpaicg_chat_token_id){
                                $wpdb->update($wpdb->prefix.'wpaicg_chattokens', array(
                                    'tokens' => ($wpaicg_total_tokens + $wpaicg_token_usage_client)
                                ), array('id' => $wpaicg_chat_token_id));
                            }
                            else{
                                $wpaicg_chattoken_data = array(
                                    'tokens' => $wpaicg_total_tokens,
                                    'source' => $wpaicg_chat_source,
                                    'created_at' => time()
                                );
                                if(is_user_logged_in()){
                                    $wpaicg_chattoken_data['user_id'] = get_current_user_id();
                                }
                                else{
                                    $wpaicg_chattoken_data['session_id'] = $wpaicg_client_id;
                                }
                                $wpdb->insert($wpdb->prefix.'wpaicg_chattokens',$wpaicg_chattoken_data);
                            }
                        }
                        /*
                         * End save token handing
                         * */
                        if ($wpaicg_chat_remember_conversation == 'yes') {
                            $wpaicg_conversation_end_messages[] = $wpaicg_result['data'];
                            setcookie($wpaicg_session_page,serialize($wpaicg_conversation_end_messages),time()+86400,COOKIEPATH, COOKIE_DOMAIN);
                        }
                    }
                }
                else{
                    $wpaicg_result['status'] = 'error';
                    $wpaicg_result['msg'] = esc_html__('It appears that nothing was inputted.','gpt3-ai-content-generator');
                }

            }

            wp_send_json( $wpaicg_result );
        }
        public function getIpAddress()
        {
            $ipAddress = '';
            if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
                // to get shared ISP IP address
                $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
            } else if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                // check for IPs passing through proxy servers
                // check if multiple IP addresses are set and take the first one
                $ipAddressList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($ipAddressList as $ip) {
                    if (! empty($ip)) {
                        // if you prefer, you can check for valid IP address here
                        $ipAddress = $ip;
                        break;
                    }
                }
            } else if (! empty($_SERVER['HTTP_X_FORWARDED'])) {
                $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
            } else if (! empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
                $ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
            } else if (! empty($_SERVER['HTTP_FORWARDED_FOR'])) {
                $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
            } else if (! empty($_SERVER['HTTP_FORWARDED'])) {
                $ipAddress = $_SERVER['HTTP_FORWARDED'];
            } else if (! empty($_SERVER['REMOTE_ADDR'])) {
                $ipAddress = $_SERVER['REMOTE_ADDR'];
            }
            return $ipAddress;
        }

        public function wpaicg_save_chat_log($wpaicg_log_id, $wpaicg_log_data,$type = 'user', $message = '',$tokens = 0, $flag = false, $request = '')
        {
            global $wpdb;
            if($wpaicg_log_id){
                $wpaicg_log_data[] = array('message' => $message, 'type' => $type, 'date' => time(), 'token' => $tokens, 'flag' => $flag, 'request' => $request);
                $wpdb->update($wpdb->prefix.'wpaicg_chatlogs', array(
                    'data' => json_encode($wpaicg_log_data),
                    'created_at' => time()
                ), array(
                    'id' => $wpaicg_log_id
                ));
            }
        }

        public function wpaicg_embeddings_result($open_ai,$wpaicg_pinecone_api,$wpaicg_pinecone_environment,$wpaicg_message, $wpaicg_chat_embedding_top, $namespace = false)
        {
            $result = array('status' => 'error','data' => '');
            if(!empty($wpaicg_pinecone_api) && !empty($wpaicg_pinecone_environment) ) {
                $response = $open_ai->embeddings([
                    'input' => $wpaicg_message,
                    'model' => 'text-embedding-ada-002'
                ]);
                $response = json_decode($response, true);
                if (isset($response['error']) && !empty($response['error'])) {
                    $result['data'] = $response['error']['message'];
                    if(empty($result['data']) && isset($response['error']['code']) && $response['error']['code'] == 'invalid_api_key'){
                        $result['data'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                    }
                } else {
                    $embedding = $response['data'][0]['embedding'];
                    if (!empty($embedding)) {
                        $result['tokens'] = $response['usage']['total_tokens'];
                        $headers = array(
                            'Content-Type' => 'application/json',
                            'Api-Key' => $wpaicg_pinecone_api
                        );
                        $pinecone_body = array(
                            'vector' => $embedding,
                            'topK' => $wpaicg_chat_embedding_top
                        );
                        if($namespace){
                            $pinecone_body['namespace'] = $namespace;
                        }
                        $response = wp_remote_post('https://' . $wpaicg_pinecone_environment . '/query', array(
                            'headers' => $headers,
                            'body' => json_encode($pinecone_body)
                        ));
                        if (is_wp_error($response)) {
                            $result['data'] = esc_html($response->get_error_message());
                        } else {
                            $body = json_decode($response['body'], true);
                            if ($body) {
                                if (isset($body['matches']) && is_array($body['matches']) && count($body['matches'])) {
                                    $data = '';
                                    foreach($body['matches'] as $match){
                                        $wpaicg_embedding = get_post($match['id']);
                                        if ($wpaicg_embedding) {
                                            $data .= empty($data) ? $wpaicg_embedding->post_content : "\n".$wpaicg_embedding->post_content;
                                        }

                                    }
                                    $result['data'] = $data;
                                    $result['status'] = 'success';
                                }
                                else{
                                    $result['status'] = 'empty';
                                }
                            }
                            else{
                                $result['data'] = esc_html__('Pinecone return empty','gpt3-ai-content-generator');
                            }
                        }
                    }
                }
            }
            else{
                $result['data'] = esc_html__('Missing PineCone Settings','gpt3-ai-content-generator');
            }
            return $result;
        }

        public function wpaicg_chatbox($atts)
        {
            ob_start();
            include WPAICG_PLUGIN_DIR . 'admin/extra/wpaicg_chatbox.php';
            $wpaicg_chatbox = ob_get_clean();
            return $wpaicg_chatbox;
        }

        public function wpaicg_chatbox_widget()
        {
            ob_start();
            include WPAICG_PLUGIN_DIR . 'admin/extra/wpaicg_chatbox_widget.php';
            $wpaicg_chatbox = ob_get_clean();
            return $wpaicg_chatbox;
        }
    }
    WPAICG_Chat::get_instance();
}
