<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Audio')) {
    class WPAICG_Audio
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
            add_action( 'wp_ajax_wpaicg_audio_converter', array( $this, 'wpaicg_audio_converter' ) );
            add_action( 'wp_ajax_wpaicg_audio_settings', array( $this, 'wpaicg_settings' ) );
            add_action('init',[$this,'wpaicg_download_audio'],1);
            add_filter('mime_types', function ($mime_types){
                $mime_types['wav'] = 'audio/wav';
                $mime_types['xwav'] = 'audio/x-wav';
                return $mime_types;
            });
        }

        public function wpaicg_download_audio()
        {
            if(isset($_GET['wpaicg_download_audio']) && !empty($_GET['wpaicg_download_audio'])){
                $audio_id = sanitize_text_field($_GET['wpaicg_download_audio']);
                if(!wp_verify_nonce($_GET['_wpnonce'], 'wpaicg_download_'.$audio_id)){
                    die(WPAICG_NONCE_ERROR);
                }
                $audio = get_post($audio_id);
                if($audio){
                    $response = get_post_meta($audio_id,'wpaicg_response',true);
                    $response = empty($response) ? 'text' : $response;
                    $content = $audio->post_content;
                    $filename = 'wpaicg_audio_'.$audio_id;
                    if($response == 'text' || $response == 'post' || $response == 'page'){
                        header('Content-Type: text/plain');
                        $filename .= '.txt';
                    }
                    if($response == 'json' || $response == 'verbose_json'){
                        header('Content-Type: application/json');
                        $filename .= '.json';
                    }
                    if($response == 'vtt'){
                        header('Content-Type: text/vtt');
                        $filename .= '.vtt';
                    }
                    if($response == 'srt'){
                        header('Content-Type: text/plain');
                        $filename .= '.srt';
                    }
                    header('Content-Disposition: attachment; filename="'.$filename.'"');
                    header('Content-Length: ' . strlen($content));
                    header('Connection: close');
                    echo wp_kses_post($content);
                }
                exit;
            }
        }

        public function wpaicg_menu()
        {
            add_submenu_page(
                'wpaicg',
                esc_html__('Audio Converter','gpt3-ai-content-generator'),
                esc_html__('Audio Converter','gpt3-ai-content-generator'),
                'wpaicg_audio',
                'wpaicg_audio',
                array( $this, 'wpaicg_audio' ),
                9
            );
        }

        public function wpaicg_audio()
        {
            include WPAICG_PLUGIN_DIR . 'admin/views/audio/index.php';
        }

        public function wpaicg_audio_converter()
        {
            $wpaicg_generator_start = microtime( true );
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            $purpose = isset($_REQUEST['purpose']) && !empty($_REQUEST['purpose']) ? sanitize_text_field($_REQUEST['purpose']) : 'transcriptions';
            $prompt = isset($_REQUEST['prompt']) && !empty($_REQUEST['prompt']) ? sanitize_text_field($_REQUEST['prompt']) : '';
            $type = isset($_REQUEST['type']) && !empty($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : 'upload';
            $url = isset($_REQUEST['url']) && !empty($_REQUEST['url']) ? sanitize_text_field($_REQUEST['url']) : '';
            $model = isset($_REQUEST['model']) && !empty($_REQUEST['model']) ? sanitize_text_field($_REQUEST['model']) : 'whisper-1';
            $response = isset($_REQUEST['response']) && !empty($_REQUEST['response']) ? sanitize_text_field($_REQUEST['response']) : 'post';
            $title = isset($_REQUEST['title']) && !empty($_REQUEST['title']) ? sanitize_text_field($_REQUEST['title']) : '';
            $category = isset($_REQUEST['category']) && !empty($_REQUEST['category']) ? sanitize_text_field($_REQUEST['category']) : '';
            $status = isset($_REQUEST['status']) && !empty($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : 'draft';
            $temperature = isset($_REQUEST['temperature']) && !empty($_REQUEST['temperature']) ? sanitize_text_field($_REQUEST['temperature']) : 0;
            $language = isset($_REQUEST['language']) && !empty($_REQUEST['language']) ? sanitize_text_field($_REQUEST['language']) : 'en';
            $author_id = isset($_REQUEST['author']) && !empty($_REQUEST['author']) ? sanitize_text_field($_REQUEST['author']) : '';
            $mime_types = ['mp3' => 'audio/mpeg','mp4' => 'video/mp4','mpeg' => 'video/mpeg','m4a' => 'audio/m4a','wav' => 'audio/wav','xwav' => 'audio/x-wav','webm' => 'video/webm'];
            if($type == 'upload' && !isset($_FILES['file'])){
                $wpaicg_result['msg'] = esc_html__('An audio file is mandatory.','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if(($response == 'post' || $response == 'page') && empty($title)){
                $wpaicg_result['msg'] = esc_html__('Please insert title','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if($type == 'upload'){
                $file = $_FILES['file'];
                $file_name = sanitize_file_name(basename($file['name']));
                $filetype = wp_check_filetype($file_name);
                if(!in_array($filetype['type'], $mime_types)){
                    $wpaicg_result['msg'] = esc_html__('We only accept mp3, mp4, mpeg, mpga, m4a, wav, or webm.','gpt3-ai-content-generator');
                    wp_send_json($wpaicg_result);
                }
                if($file['size'] > 26214400){
                    $wpaicg_result['msg'] = esc_html__('Audio file maximum 25MB','gpt3-ai-content-generator');
                    wp_send_json($wpaicg_result);
                }
            }
            if($type == 'record'){
                $file = $_FILES['recorded_audio'];
                $file_name = sanitize_file_name(basename($file['name']));
                $filetype = wp_check_filetype($file_name);
                if(!in_array($filetype['type'], $mime_types)){
                    $wpaicg_result['msg'] = esc_html__('We only accept mp3, mp4, mpeg, mpga, m4a, wav, or webm.','gpt3-ai-content-generator');
                    wp_send_json($wpaicg_result);
                }
                if($file['size'] > 26214400){
                    $wpaicg_result['msg'] = esc_html__('Audio file maximum 25MB','gpt3-ai-content-generator');
                    wp_send_json($wpaicg_result);
                }
                $tmp_file = $file['tmp_name'];

            }
            if($type == 'url'){
                if(empty($url)){
                    $wpaicg_result['msg'] = esc_html__('The audio URL is required','gpt3-ai-content-generator');
                    wp_send_json($wpaicg_result);
                }
                $remoteFile = get_headers($url, 1);
                $file_name = basename($url);
                $is_in_mime_types = false;
                $file_ext = '';
                foreach($mime_types as $key=>$mime_type){
                    if((is_array($remoteFile['Content-Type']) && in_array($mime_type,$remoteFile['Content-Type'])) || strpos($remoteFile['Content-Type'], $mime_type) !== false){
                        $is_in_mime_types = true;
                        $file_ext = '.'.$key;
                        break;
                    }
                }
                if(!$is_in_mime_types){
                    $wpaicg_result['msg'] = esc_html__('We only accept mp3, mp4, mpeg, mpga, m4a, wav, or webm.','gpt3-ai-content-generator');
                    wp_send_json($wpaicg_result);
                }

                if(strpos($file_name,$file_ext) === false){
                    $file_name = md5(uniqid().time()).$file_ext;
                }
                if($remoteFile['Content-Length'] > 26214400){
                    $wpaicg_result['msg'] = esc_html__('Audio file maximum 25MB','gpt3-ai-content-generator');
                    wp_send_json($wpaicg_result);
                }
            }
            $open_ai = WPAICG_OpenAI::get_instance()->openai();
            if(!$open_ai){
                $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if(!method_exists($open_ai, $purpose)){
                $wpaicg_result['msg'] = esc_html__('Method does not exist','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if($type == 'url'){
                if(!function_exists('download_url')){
                    include_once( ABSPATH . 'wp-admin/includes/file.php' );
                }
                $tmp_file = download_url($url);
                if ( is_wp_error( $tmp_file ) ){
                    $wpaicg_result['msg'] = $tmp_file->get_error_message();
                    wp_send_json($wpaicg_result);
                }
            }
            if($type == 'upload'){
                $tmp_file = $file['tmp_name'];
            }
            $response_format = $response == 'post' || $response == 'page' ? 'text' : $response;
            $data_request = array(
                'audio' => array(
                    'filename' => $file_name,
                    'data' => file_get_contents($tmp_file)
                ),
                'model' => $model,
                'temperature' => $temperature,
                'response_format' => $response_format,
                'prompt' => $prompt
            );
            if($purpose == 'transcriptions' && !empty($language)){
                $data_request['language'] = $language;
            }
            $completion = $open_ai->$purpose($data_request);
            $result = json_decode($completion);
            if($result && isset($result->error)){
                $wpaicg_result['msg'] = $result->error->message;
                if(empty($wpaicg_result['msg']) && isset($result->error->code) && $result->error->code == 'invalid_api_key'){
                    $wpaicg_result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                }
                wp_send_json($wpaicg_result);
            }
            $wpaicg_result['status'] = 'success';
            $text_generated = $completion;
            $wpaicg_result['data'] = $text_generated;
            $wpaicg_generator_end = microtime( true ) - $wpaicg_generator_start;
            if(empty($text_generated)){
                $wpaicg_result['msg'] = esc_html__('The model predicted a completion that begins with a stop sequence, resulting in no output. Consider adjusting your prompt or stop sequences.','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            $wpaicg_audio_id = wp_insert_post(array(
                'post_title' => $file_name,
                'post_type' => 'wpaicg_audio',
                'post_content' => $text_generated,
                'post_status' => 'publish'
            ));
            add_post_meta($wpaicg_audio_id,'wpaicg_duration',$wpaicg_generator_end);
            add_post_meta($wpaicg_audio_id,'wpaicg_response',$response);
            add_post_meta($wpaicg_audio_id,'wpaicg_type',$type);
            if($response == 'post' || $response == 'page'){
                $post_data = array(
                    'post_title' => $title,
                    'post_content' => $text_generated,
                    'post_type' => $response,
                    'post_status' => $status
                );
                if(!empty($author_id)){
                    $post_data['post_author'] = $author_id;
                }
                $wpaicg_post_id = wp_insert_post($post_data);
                if($response == 'post' && !empty($category)){
                    wp_set_post_terms($wpaicg_post_id,$category,'category');
                }
                add_post_meta($wpaicg_audio_id,'wpaicg_post',$wpaicg_post_id);
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_settings()
        {
            $wpaicg_result = array('status' => 'success');
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            $purpose = isset($_REQUEST['purpose']) && !empty($_REQUEST['purpose']) ? sanitize_text_field($_REQUEST['purpose']) : 'transcriptions';
            $prompt = isset($_REQUEST['prompt']) && !empty($_REQUEST['prompt']) ? sanitize_text_field($_REQUEST['prompt']) : '';
            $type = isset($_REQUEST['type']) && !empty($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : 'upload';
            $url = isset($_REQUEST['url']) && !empty($_REQUEST['url']) ? sanitize_text_field($_REQUEST['url']) : '';
            $model = isset($_REQUEST['model']) && !empty($_REQUEST['model']) ? sanitize_text_field($_REQUEST['model']) : 'whisper-1';
            $response = isset($_REQUEST['response']) && !empty($_REQUEST['response']) ? sanitize_text_field($_REQUEST['response']) : 'post';
            $title = isset($_REQUEST['title']) && !empty($_REQUEST['title']) ? sanitize_text_field($_REQUEST['title']) : '';
            $category = isset($_REQUEST['category']) && !empty($_REQUEST['category']) ? sanitize_text_field($_REQUEST['category']) : '';
            $status = isset($_REQUEST['status']) && !empty($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : 'draft';
            $temperature = isset($_REQUEST['temperature']) && !empty($_REQUEST['temperature']) ? sanitize_text_field($_REQUEST['temperature']) : 0;
            $language = isset($_REQUEST['language']) && !empty($_REQUEST['language']) ? sanitize_text_field($_REQUEST['language']) : 'en';
            $author_id = isset($_REQUEST['author']) && !empty($_REQUEST['author']) ? sanitize_text_field($_REQUEST['author']) : '';
            $wpaicg_audio_settings = array(
                'purpose' => $purpose,
                'type' => $type,
                'model' => $model,
                'response' => $response,
                'category' => $category,
                'status' => $status,
                'temperature' => $temperature,
                'language' => $language,
                'author' => $author_id,
            );
            update_option('wpaicg_audio_setting', $wpaicg_audio_settings);
            wp_send_json($wpaicg_result);
        }
    }

    WPAICG_Audio::get_instance();
}
