<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Template')) {
    class WPAICG_Template
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
            add_action('wp_ajax_wpaicg_template_generator', [$this,'wpaicg_template_generator']);
            add_action('wp_ajax_wpaicg_template_post', [$this,'wpaicg_template_post']);
            add_action('wp_ajax_wpaicg_save_template', [$this,'wpaicg_save_template']);
            add_action('wp_ajax_wpaicg_template_delete', [$this,'wpaicg_template_delete']);
        }

        public function wpaicg_template_delete()
        {
            $wpaicg_result = array('status' => 'error', 'msg'=>'Missing request');
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(
                isset($_REQUEST['id'])
                && !empty($_REQUEST['id'])
            ){
                wp_delete_post(sanitize_text_field($_REQUEST['id']));
                $wpaicg_result['status'] = 'success';
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_save_template()
        {
            $wpaicg_result = array('status' => 'error', 'msg'=>esc_html__('Missing request','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wpaicg_custom_mode_generator' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(
                isset($_REQUEST['title'])
                && !empty($_REQUEST['title'])
                && isset($_REQUEST['template'])
                && is_array($_REQUEST['template'])
                && count($_REQUEST['template'])
            ){
                $template = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['template']);
                $template['title'] = sanitize_text_field($_REQUEST['title']);
                $template_id = false;
                if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                    $template_id = $_REQUEST['id'];
                }
                if(isset($_REQUEST['title_count']) && !empty($_REQUEST['title_count'])){
                    $template['title_count'] = sanitize_text_field($_REQUEST['title_count']);
                }
                if(isset($_REQUEST['section_count']) && !empty($_REQUEST['section_count'])){
                    $template['section_count'] = sanitize_text_field($_REQUEST['section_count']);
                }
                if(isset($_REQUEST['paragraph_count']) && !empty($_REQUEST['paragraph_count'])){
                    $template['paragraph_count'] = sanitize_text_field($_REQUEST['paragraph_count']);
                }
                if($template_id){
                    wp_update_post(array(
                        'ID' => $template_id,
                        'post_title' => $template['title'],
                        'post_content' => serialize($template)
                    ));
                }
                else{
                    $template_id = wp_insert_post(array(
                        'post_status' => 'publish',
                        'post_type' => 'wpaicg_mtemplate',
                        'post_title' => $template['title'],
                        'post_content' => serialize($template)
                    ));
                }
                $selected_template = $template_id;
                ob_start();
                include WPAICG_PLUGIN_DIR.'admin/extra/wpaicg_custom_model_template.php';
                $wpaicg_result['setting'] = ob_get_clean();
                $wpaicg_result['status'] = 'success';
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_template_post()
        {
            $wpaicg_result = array('status' => 'error', 'msg'=>esc_html__('Missing request','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(
                isset($_REQUEST['title'])
                && !empty($_REQUEST['title'])
                && isset($_REQUEST['content'])
                && !empty($_REQUEST['content'])
            ){
                $title = sanitize_text_field($_REQUEST['title']);
                $content = wp_kses_post($_REQUEST['content']);
                $new_content = array();
                $exs = array_map('trim', explode("\n", $content));
                foreach($exs as $ex){
                    if(strpos($ex, '##') !== false){
                        $new_content[] = '<h2>'.trim(str_replace('##','',$ex)).'</h2>';
                    }
                    else $new_content[] = $ex;
                }
                $new_content = implode("\n",$new_content);
                $post_type = 'post';
                if(isset($_REQUEST['post_type']) && !empty($_REQUEST['post_type'])){
                    $post_type = sanitize_text_field($_REQUEST['post_type']);
                }
                $wpaicg_data = array(
                    'post_title' => $title,
                    'post_content' => $new_content,
                    'post_status' => 'draft',
                    'post_type' => $post_type
                );
                if(isset($_REQUEST['excerpt']) && !empty($_REQUEST['excerpt'])){
                    $wpaicg_data['post_excerpt'] = sanitize_text_field($_REQUEST['excerpt']);
                }
                $wpaicg_post_id = wp_insert_post($wpaicg_data);
                if(is_wp_error($wpaicg_post_id)){
                    $wpaicg_result['msg'] = $wpaicg_post_id->get_error_message();
                    wp_send_json($wpaicg_result);
                }
                else{
                    $content_class = WPAICG_Content::get_instance();
                    if(isset($_REQUEST['description']) && !empty($_REQUEST['description'])){
                        $content_class->wpaicg_save_description($wpaicg_post_id, sanitize_text_field($_REQUEST['description']));
                    }
                    $wpaicg_duration = isset($_REQUEST['duration']) && !empty($_REQUEST['duration']) ? sanitize_text_field($_REQUEST['duration']) : 0;
                    $wpaicg_usage_token = isset($_REQUEST['tokens']) && !empty($_REQUEST['tokens']) ? sanitize_text_field($_REQUEST['tokens']) : 0;
                    $wpaicg_word_count = isset($_REQUEST['words']) && !empty($_REQUEST['words']) ? sanitize_text_field($_REQUEST['words']) : 0;
                    $wpaicg_log_id = wp_insert_post(array(
                        'post_title' => 'WPAICGLOG:' . $title,
                        'post_type' => 'wpaicg_slog',
                        'post_status' => 'publish'
                    ));
                    $wpaicg_ai_model = get_option('wpaicg_ai_model', 'text-davinci-003');
                    if (isset($_REQUEST['model']) && !empty($_REQUEST['model'])) {
                        $wpaicg_ai_model = sanitize_text_field($_REQUEST['model']);
                    }
                    $source_log = 'custom';
                    add_post_meta($wpaicg_log_id, 'wpaicg_source_log', $source_log);
                    add_post_meta($wpaicg_log_id, 'wpaicg_ai_model', $wpaicg_ai_model);
                    add_post_meta($wpaicg_log_id, 'wpaicg_duration', $wpaicg_duration);
                    add_post_meta($wpaicg_log_id, 'wpaicg_usage_token', $wpaicg_usage_token);
                    add_post_meta($wpaicg_log_id, 'wpaicg_word_count', $wpaicg_word_count);
                    add_post_meta($wpaicg_log_id, 'wpaicg_post_id', $wpaicg_post_id);
                    $wpaicg_result['status'] = 'success';
                    $wpaicg_result['id'] = $wpaicg_post_id;
                }


            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_template_generator()
        {
            $wpaicg_result = array('status' => 'error', 'msg'=>esc_html__('Missing request','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wpaicg_custom_mode_generator' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if($_REQUEST['template'] && is_array($_REQUEST['template']) && count($_REQUEST['template']) && isset($_REQUEST['step']) && !empty($_REQUEST['step'])){
                $step = sanitize_text_field($_REQUEST['step']);
                $template = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['template']);
                $prompt = '';
                $title_count = (int)sanitize_text_field($_REQUEST['title_count']);
                $section_count = (int)sanitize_text_field($_REQUEST['section_count']);
                $paragraph_count = sanitize_text_field($_REQUEST['paragraph_count']);
                $post_title = isset($_REQUEST['post_title']) && !empty($_REQUEST['post_title']) ? sanitize_text_field($_REQUEST['post_title']) : '';
                $sections = isset($_REQUEST['sections']) && !empty($_REQUEST['sections']) ? sanitize_text_field($_REQUEST['sections']) : '';
                $list_sections = array();
                if($step == 'titles'){
                    $topic = sanitize_text_field($_REQUEST['topic']);
                    $prompt = $template['prompt_title'];
                    $prompt = str_replace('[count]',$title_count,$prompt);
                    $prompt = str_replace('[topic]',$topic,$prompt);
                }
                if($step == 'sections'){
                    if(empty($post_title)){
                        $wpaicg_result['msg'] = esc_html__('Please generate title first','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $prompt = $template['prompt_section'];
                    $prompt = str_replace('[count]',$section_count,$prompt);
                    $prompt = str_replace('[title]',$post_title,$prompt);
                }
                if($step == 'excerpt'){
                    if(empty($post_title)){
                        $wpaicg_result['msg'] = esc_html__('Please generate title first','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $prompt = $template['prompt_excerpt'];
                    $prompt = str_replace('[title]',$post_title,$prompt);
                }
                if($step == 'meta'){
                    if(empty($post_title)){
                        $wpaicg_result['msg'] = esc_html__('Please generate title first','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $prompt = $template['prompt_meta'];
                    $prompt = str_replace('[title]',$post_title,$prompt);
                }
                if($step == 'content'){
                    if(empty($post_title)){
                        $wpaicg_result['msg'] = esc_html__('Please generate title first','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    if(empty($sections)){
                        $wpaicg_result['msg'] = esc_html__('Please generate sections first','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $exs = array_map('trim', explode("##", $sections));
                    foreach($exs as $key=> $ex){
                        $section = trim(str_replace("\n",'',$ex));
                        if(!empty($section)) {
                            $list_sections[] = $section;
                        }
                    }
                    $new_sections = implode("\n",$list_sections);
                    $prompt = $template['prompt_content'];
                    $prompt = str_replace('[count]',$paragraph_count,$prompt);
                    $prompt = str_replace('[title]',$post_title,$prompt);
                    $prompt = str_replace('[sections]',$new_sections,$prompt);
                }
                $openai = WPAICG_OpenAI::get_instance()->openai();
                $generator = WPAICG_Generator::get_instance();
                $generator->openai($openai);
                $data_request = array(
                    'prompt' => $prompt,
                    'model' => $template['model'],
                    'temperature' => (float)$template['temperature'],
                    'max_tokens' => (float)$template['max_tokens'],
                    'top_p' => (float)$template['top_p'],
                    'best_of' => (float)$template['best_of'],
                    'frequency_penalty' => (float)$template['frequency_penalty'],
                    'presence_penalty' => (float)$template['presence_penalty'],
                );
                if($step == 'sections'){
                    $data_request['stop'] = ($section_count+1).'.';
                }
                if($step == 'titles'){
                    $data_request['stop'] = ($title_count+1).'.';
                }
                $result = $generator->wpaicg_request($data_request);
                if($result['status'] == 'error'){
                    $wpaicg_result['msg'] = $result['msg'];
                }
                else{
//                    $wpaicg_result['data_open'] = $result['data'];
                    if($step == 'titles' || $step == 'sections'){
                        $complete = $result['data'];
                        $words_count = $generator->wpaicg_count_words($complete);
                        $complete = trim( $complete );
                        $complete=preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$complete)));
                        $mylist = preg_split( "/\r\n|\n|\r/", $complete );
                        $mylist = preg_replace( '/^\\d+\\.\\s/', '', $mylist );
                        $mylist = preg_replace( '/\\.$/', '', $mylist );
                        if($mylist && is_array($mylist) && count($mylist)){
                            $newlist = array();
                            foreach($mylist as $item){
                                $newlist[] = str_replace('"','',$item);
                            }
                            $wpaicg_result['data'] = $newlist;
                            $wpaicg_result['status'] = 'success';
                            $wpaicg_result['tokens'] = $result['tokens'];
                            $wpaicg_result['words'] = $words_count;
                        }
                        else{
                            $wpaicg_result['msg'] = esc_html__('No data generated','gpt3-ai-content-generator');
                        }
                    }
                    if($step == 'content'){
                        $content = $result['data'];
                        $wpaicg_result['content'] = $content;
                        $words_count = $generator->wpaicg_count_words($content);
                        foreach($list_sections as $list_section){
                            $list_section = str_replace('\\','',$list_section);
                            if(strpos($content,$list_section.':') !== false){
                                $content = str_replace($list_section.':',$list_section,$content);
                            }
                            if(strpos($content,$list_section."\n") === false){
                                $content = str_replace($list_section,$list_section."\n",$content);
                            }
                            $content = str_replace($list_section,'## '.$list_section, $content);
                        }
                        $wpaicg_result['data'] = $content;
                        $wpaicg_result['status'] = 'success';
                        $wpaicg_result['tokens'] = $result['tokens'];
                        $wpaicg_result['words'] = $words_count;
                    }
                    if($step == 'meta' || $step == 'excerpt'){
                        $content = $result['data'];
                        $words_count = $generator->wpaicg_count_words($content);
                        $wpaicg_result['data'] = $content;
                        $wpaicg_result['status'] = 'success';
                        $wpaicg_result['tokens'] = $result['tokens'];
                        $wpaicg_result['words'] = $words_count;
                    }
                    $wpaicg_result['prompt'] = $prompt;

                }
            }
            wp_send_json($wpaicg_result);
        }
    }
    WPAICG_Template::get_instance();
}
