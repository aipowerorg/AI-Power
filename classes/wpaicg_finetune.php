<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_FineTune')){
    class WPAICG_FineTune
    {
        private static  $instance = null ;
        public $wpaicg_max_file_size = 10485760;

        public static function get_instance()
        {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action('wp_ajax_wpaicg_finetune_upload', [$this,'wpaicg_finetune_upload']);
            add_action('wp_ajax_wpaicg_get_finetune_file', [$this,'wpaicg_get_finetune_file']);
            add_action('wp_ajax_wpaicg_get_finetune', [$this,'wpaicg_get_finetune']);
            add_action('wp_ajax_wpaicg_create_finetune', [$this,'wpaicg_create_finetune']);
            add_action('wp_ajax_wpaicg_finetune_events', [$this,'wpaicg_finetune_events']);
            add_action('wp_ajax_wpaicg_delete_finetune_file', [$this,'wpaicg_delete_finetune_file']);
            add_action('wp_ajax_wpaicg_delete_finetune', [$this,'wpaicg_delete_finetune']);
            add_action('wp_ajax_wpaicg_cancel_finetune', [$this,'wpaicg_cancel_finetune']);
            add_action('wp_ajax_wpaicg_other_finetune', [$this,'wpaicg_other_finetune']);
            add_action('wp_ajax_wpaicg_fetch_finetunes', [$this,'wpaicg_finetunes']);
            add_action('wp_ajax_wpaicg_fetch_finetune_files', [$this,'wpaicg_files']);
            add_action('wp_ajax_wpaicg_download', [$this,'wpaicg_download']);
            add_action('wp_ajax_wpaicg_create_finetune_modal', [$this,'wpaicg_create_finetune_modal']);
            add_action('wp_ajax_wpaicg_data_converter_count',[$this,'wpaicg_data_converter_count']);
            add_action('wp_ajax_wpaicg_data_converter',[$this,'wpaicg_data_converter']);
            add_action('wp_ajax_wpaicg_upload_convert',[$this,'wpaicg_upload_convert']);
            add_action('wp_ajax_wpaicg_data_insert',[$this,'wpaicg_data_insert']);
            add_action( 'admin_menu', array( $this, 'wpaicg_menu' ) );
            add_filter('mime_types', function ($mime_types){
                $mime_types['jsonl'] = 'application/octet-stream';
                return $mime_types;
            });
        }

        public function wpaicg_menu()
        {
            add_submenu_page(
                'wpaicg',
                esc_html__('Train Your AI','gpt3-ai-content-generator'),
                esc_html__('Train Your AI','gpt3-ai-content-generator'),
                'wpaicg_finetune',
                'wpaicg_finetune',
                array( $this, 'wpaicg_finetune' ),
                8
            );
        }

        public function wpaicgUploadOpenAI($file, $open_ai)
        {
            $model = isset($_POST['model']) && !empty($_POST['model']) ? sanitize_text_field($_POST['model']) : 'ada';
            $name = isset($_POST['custom']) && !empty($_POST['custom']) ? sanitize_title($_POST['custom']) : '';
            $result = $open_ai->uploadFile(array(
                'file' => array(
                    'data' => file_get_contents($file),
                    'filename' => basename($file)
                ),
            ));
            $result = json_decode($result);
            if(isset($result->error)){
                return trim($result->error->message);
            }
            else{
                $wpaicg_file_id = wp_insert_post(array(
                    'post_title' => $result->id,
                    'post_date' => date('Y-m-d H:i:s',$result->created_at),
                    'post_status' => 'publish',
                    'post_type' => 'wpaicg_file',
                ));
                if(!is_wp_error($wpaicg_file_id)){
                    add_post_meta($wpaicg_file_id, 'wpaicg_filename',$result->filename);
                    add_post_meta($wpaicg_file_id, 'wpaicg_purpose',$result->purpose);
                    add_post_meta($wpaicg_file_id, 'wpaicg_model',$model);
                    add_post_meta($wpaicg_file_id, 'wpaicg_custom_name',$name);
                    add_post_meta($wpaicg_file_id, 'wpaicg_file_size',$result->bytes);
                }
                else{
                    return $wpaicg_file_id->get_error_message();
                }
                return 'success';
            }
        }

        public function wpaicg_data_insert()
        {
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(
                isset($_POST['prompt'])
                && !empty($_POST['prompt'])
                && isset($_POST['completion'])
                && !empty($_POST['completion'])
            ){
                $data = array(
                    'prompt' => sanitize_text_field($_POST['prompt']).' ->',
                    'completion' => strip_tags(sanitize_text_field($_POST['completion']))
                );
                $file = isset($_POST['file']) && !empty($_POST['file']) ? sanitize_text_field($_POST['file']) : md5(time()).'.jsonl';
                $wpaicg_json_file = fopen(wp_upload_dir()['basedir'].'/'.$file, "a");
                fwrite($wpaicg_json_file, json_encode($data) . PHP_EOL);
                fclose($wpaicg_json_file);
                $wpaicg_result['file'] = $file;
                $wpaicg_result['status'] = 'success';
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_upload_convert()
        {
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(
                isset($_POST['file'])
                && !empty($_POST['file'])
            ){
                $filename = sanitize_text_field($_POST['file']);
                $line = isset($_POST['line']) && !empty($_POST['line']) ? sanitize_text_field($_POST['line']) : 0;
                $index = isset($_POST['index']) && !empty($_POST['index']) ? sanitize_text_field($_POST['index']) : 1;
                $file = wp_upload_dir()['basedir'].'/'.$filename;
                if(file_exists($file)){
                    $open_ai = WPAICG_OpenAI::get_instance()->openai();
                    if(!$open_ai){
                        $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                    }
                    else {
                        $wpaicg_lines = file($file);
                        $wpaicg_file_size = filesize($file);
                        if ($wpaicg_file_size < $this->wpaicg_max_file_size) {
                            $result = $this->wpaicgUploadOpenAI($file, $open_ai);
                            $wpaicg_result['next'] = 'DONE';
                        } else {
                            $filename =  str_replace('.jsonl','',$filename);
                            $filename = $filename.'-'.$index.'.jsonl';
                            try {
                                $split_file = wp_upload_dir()['basedir'].'/'.$filename;
                                $wpaicg_json_file = fopen($split_file, "a");
                                $wpaicg_content = '';
                                for($i = $line; $i <= count($wpaicg_lines);$i++){
                                    if($i == count($wpaicg_lines)){
                                        $wpaicg_content .= $wpaicg_lines[$i];
                                        $wpaicg_result['next'] = 'DONE';
                                    }
                                    else{
                                        if(mb_strlen($wpaicg_content, '8bit') > $this->wpaicg_max_file_size){
                                            $wpaicg_result['next'] = $i+1;
                                            break;
                                        }
                                        else{
                                            $wpaicg_content .= $wpaicg_lines[$i];
                                        }
                                    }
                                }
                                fwrite($wpaicg_json_file,$wpaicg_content);
                                fclose($wpaicg_json_file);
                                $result = $this->wpaicgUploadOpenAI($split_file, $open_ai);
                                unlink($split_file);
                            }
                            catch (\Exception $exception){
                                $result = $exception->getMessage();
                            }
                        }
                        if($result == 'success'){
                            $wpaicg_result['status'] = 'success';
                        }
                        else{
                            $wpaicg_result['msg'] = $result;
                        }
                    }
                }
                else $wpaicg_result['msg'] = esc_html__('The file has been removed','gpt3-ai-content-generator');

            }
            else{
                $wpaicg_result['msg'] = esc_html__('The file does not exist or removed','gpt3-ai-content-generator');
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_data_converter_count()
        {
            global $wpdb;
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg_data_converter_count' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if(isset($_POST['data']) && is_array($_POST['data']) && count($_POST['data'])){
                $types = \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST['data']);
                $commaDelimitedPlaceholders = implode(',', array_fill(0, count($types), '%s'));
                $sql = $wpdb->prepare("SELECT COUNT(*) FROM ".$wpdb->posts." WHERE post_status='publish' AND post_type IN ($commaDelimitedPlaceholders)", $types);
                $wpaicg_result['count'] = $wpdb->get_var($sql);
                $wpaicg_result['status'] = 'success';
                $wpaicg_result['types'] = $types;
            }
            else $wpaicg_result['msg'] = esc_html__('Please select least one data to convert','gpt3-ai-content-generator');
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_data_converter()
        {
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            global $wpdb;
            if(
                isset($_POST['types'])
                && is_array($_POST['types'])
                && count($_POST['types'])
                && isset($_POST['per_page'])
                && !empty($_POST['per_page'])
                && isset($_POST['total'])
                && !empty($_POST['total'])
            ){
                $types = \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST['types']);
                $wpaicg_total = sanitize_text_field($_POST['total']);
                $wpaicg_per_page = sanitize_text_field($_POST['per_page']);
                $wpaicg_page = isset($_POST['page']) && !empty($_POST['page']) ? sanitize_text_field($_POST['page']) : 1;
                if(isset($_POST['file']) && !empty($_POST['file'])){
                    $wpaicg_file = sanitize_text_field($_POST['file']);
                }
                else{
                    $wpaicg_file = md5(time()).'.jsonl';
                }
                if(isset($_POST['id']) && !empty($_POST['id'])){
                    $wpaicg_convert_id = sanitize_text_field($_POST['id']);
                }
                else{
                    $wpaicg_convert_id = wp_insert_post(array(
                        'post_title' => $wpaicg_file,
                        'post_type' => 'wpaicg_convert',
                        'post_status' => 'publish'
                    ));
                }
                try {
                    $wpaicg_json_file = fopen(wp_upload_dir()['basedir'].'/'.$wpaicg_file, "a");
                    $wpaicg_content = '';
                    $wpaicg_offset = ( $wpaicg_page * $wpaicg_per_page ) - $wpaicg_per_page;
                    $sql = $wpdb->prepare("SELECT post_title, post_content FROM ".$wpdb->posts." WHERE post_status='publish' AND post_type IN ('".implode("','",$types)."') ORDER BY post_date ASC LIMIT %d,%d",$wpaicg_offset,$wpaicg_per_page);
                    $wpaicg_data = $wpdb->get_results($sql);
                    if($wpaicg_data && is_array($wpaicg_data) && count($wpaicg_data)){
                        foreach($wpaicg_data as $item){
                            $data = array(
                                "prompt" => $item->post_title.' ->',
                                "completion" => strip_tags($item->post_content)
                            );
                            fwrite($wpaicg_json_file, json_encode($data) . PHP_EOL);
                        }
                    }
                    fclose($wpaicg_json_file);
                    $wpaicg_max_page = ceil($wpaicg_total / $wpaicg_per_page);
                    if($wpaicg_max_page == $wpaicg_page){
                        $wpaicg_result['next_page'] = 'DONE';
                        wp_update_post(array(
                            'ID' => $wpaicg_convert_id,
                            'post_modified' => date('Y-m-d H:i:s')
                        ));
                    }
                    else{
                        $wpaicg_result['next_page'] = $wpaicg_page+1;
                    }
                    $wpaicg_result['file'] = $wpaicg_file;
                    $wpaicg_result['id'] = $wpaicg_convert_id;
                    $wpaicg_result['status'] = 'success';
                }
                catch (\Exception $exception){
                    $wpaicg_result['msg'] = $exception->getMessage();
                }
            }
            else $wpaicg_result['msg'] = esc_html__('Please select least one data to convert','gpt3-ai-content-generator');
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_create_finetune_modal()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            $models = $this->wpaicg_get_models();
            if(is_array($models)){
                $wpaicg_result['status'] = 'success';
                $wpaicg_result['data'] = $models;
            }
            else{
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = $models;
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_get_models()
        {
            $result = false;
            $open_ai = WPAICG_OpenAI::get_instance()->openai();
            if ($open_ai) {
                $result = $open_ai->listModels();
                $json_parse = json_decode($result);
                if(isset($json_parse->error)){
                    return $json_parse->error->message;
                }
                elseif(isset($json_parse->data) && is_array($json_parse->data) && count($json_parse->data)){
                    $result = array();
                    foreach($json_parse->data  as $item){
                        if($item->owned_by != 'openai' && $item->owned_by != 'system' && $item->owned_by != 'openai-dev' && $item->owned_by != 'openai-internal'){
                            $result[] = $item->id;
                        }
                    }
                    if(count($result)){
                        update_option('wpaicg_custom_models', $result);
                    }
                }
            }
            return $result;
        }

        public function wpaicg_download()
        {
            $open_ai = WPAICG_OpenAI::get_instance()->openai();
            if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
                $id = sanitize_text_field($_REQUEST['id']);
                if (!$open_ai) {
                    echo 'Missing API Setting';
                } else {
                    $result = $open_ai->retrieveFileContent($id);
                    $json_parse = json_decode($result);
                    if(isset($json_parse->error)){
                        echo esc_html($json_parse->error->message);
                    }
                    else{
                        $filename = $id.'.csv';
                        header('Content-Type: application/csv');
                        header('Content-Disposition: attachment; filename="'.$filename.'";');
                        $f = fopen('php://output', 'w');
                        $lines = explode("\n", $result);
                        foreach($lines as $line) {
                            $line = explode(';',$line);
                            fputcsv($f, $line, ';');
                        }
                    }
                }
            }
            die();
        }

        public function wpaicg_create_finetune()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $wpaicg_file = get_post(sanitize_text_field($_POST['id']));
                if($wpaicg_file){
                    $open_ai = WPAICG_OpenAI::get_instance()->openai();
                    if(!$open_ai){
                        $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $model = get_post_meta($wpaicg_file->ID,'wpaicg_model', true);
                    $suffix = get_post_meta($wpaicg_file->ID,'wpaicg_custom_name', true);
                    $dataSend = [
                        'training_file' => $wpaicg_file->post_title
                    ];
                    if(isset($_POST['model']) && !empty($_POST['model'])){
                        $dataSend['model'] = sanitize_text_field($_POST['model']);
                    }
                    else{
                        $dataSend['model'] = $model;
                        $dataSend['suffix'] = $suffix;
                    }
                    if(empty($dataSend['model'])){
                        $dataSend['model'] = 'ada';
                    }
                    $result = $open_ai->createFineTune($dataSend);
                    $wpaicg_result['model'] = $model;
                    $result = json_decode($result);
                    if(isset($result->error)){
                        $wpaicg_result['msg'] = $result->error->message;
                    }
                    else{
                        update_post_meta($wpaicg_file->ID,'wpaicg_fine_tune', $result->id);
                        $wpaicg_file_id = wp_insert_post(array(
                            'post_title' => $result->id,
                            'post_date' => date('Y-m-d H:i:s', $result->created_at),
                            'post_status' => 'publish',
                            'post_type' => 'wpaicg_finetune',
                        ));
                        add_post_meta($wpaicg_file_id, 'wpaicg_model', $result->model);
                        add_post_meta($wpaicg_file_id, 'wpaicg_updated_at', date('Y-m-d H:i:s', $result->updated_at));
                        add_post_meta($wpaicg_file_id, 'wpaicg_name', $result->fine_tuned_model);
                        add_post_meta($wpaicg_file_id, 'wpaicg_org', $result->organization_id);
                        add_post_meta($wpaicg_file_id, 'wpaicg_status', $result->status);
                        $wpaicg_result['status'] = 'success';
                        $wpaicg_result['data'] = $result;
                    }
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('File not found','gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_finetune_upload()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_FILES['file']) && empty($_FILES['file']['error'])){
                $open_ai = WPAICG_OpenAI::get_instance()->openai();
                if(!$open_ai){
                    $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                    wp_send_json($wpaicg_result);
                }
                $file_name = sanitize_file_name(basename($_FILES['file']['name']));
                $filetype = wp_check_filetype($file_name);
                if($filetype['ext'] !== 'jsonl'){
                    $wpaicg_result['msg'] = esc_html__('Only files with the jsonl extension are supported','gpt3-ai-content-generator');
                    wp_send_json($wpaicg_result);
                }
                $tmp_file = $_FILES['file']['tmp_name'];
                $c_file = $tmp_file;
                $purpose = isset($_POST['purpose']) && !empty($_POST['purpose']) ? sanitize_text_field($_POST['purpose']) : 'fine-tune';
                $model = isset($_POST['model']) && !empty($_POST['model']) ? sanitize_text_field($_POST['model']) : 'ada';
                $name = isset($_POST['name']) && !empty($_POST['name']) ? sanitize_title($_POST['name']) : '';
                $result = $open_ai->uploadFile(array(
                    'file' => array(
                        'data' => file_get_contents($tmp_file),
                        'filename' => basename($_FILES['file']['name'])
                    ),
                ));
                $result = json_decode($result);
                if(isset($result->error)){
                    $wpaicg_result['msg'] = $result->error->message;
                }
                else{
                    $wpaicg_file_id = wp_insert_post(array(
                        'post_title' => $result->id,
                        'post_date' => date('Y-m-d H:i:s',get_date_from_gmt(date('Y-m-d H:i:s',$result->created_at),'U')),
                        'post_status' => 'publish',
                        'post_type' => 'wpaicg_file',
                    ));
                    if(!is_wp_error($wpaicg_file_id)){
                        $wpaicg_result['status'] = 'success';
                        add_post_meta($wpaicg_file_id, 'wpaicg_filename',$result->filename);
                        add_post_meta($wpaicg_file_id, 'wpaicg_purpose',$result->purpose);
                        add_post_meta($wpaicg_file_id, 'wpaicg_model',$model);
                        add_post_meta($wpaicg_file_id, 'wpaicg_custom_name',$name);
                        add_post_meta($wpaicg_file_id, 'wpaicg_file_size',$result->bytes);
                    }
                    else{
                        $wpaicg_result['msg'] = $wpaicg_file_id->get_error_message();
                    }
                }
            }
            else $wpaicg_result['msg'] = esc_html__('File upload required','gpt3-ai-content-generator');
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_get_finetune_file()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $wpaicg_file = get_post(sanitize_text_field($_POST['id']));
                if($wpaicg_file){
                    $open_ai = WPAICG_OpenAI::get_instance()->openai();
                    if(!$open_ai){
                        $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $result = $open_ai->retrieveFileContent($wpaicg_file->post_title);
                    $json_parse = json_decode($result);
                    if(isset($json_parse->error)){
                        $wpaicg_result['msg'] = $json_parse->error->message;
                    }
                    else{
                        $wpaicg_result['status'] = 'success';
                        $wpaicg_result['data'] = $result;
                    }
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('File not found','gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_finetune_events()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $wpaicg_file = get_post(sanitize_text_field($_POST['id']));
                if($wpaicg_file){
                    $open_ai = WPAICG_OpenAI::get_instance()->openai();
                    if(!$open_ai){
                        $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $result = $open_ai->retrieveFineTune($wpaicg_file->post_title);
                    $result = json_decode($result);
                    if(isset($result->error)){
                        $wpaicg_result['msg'] = $result->error->message;
                    }
                    else{
                        $wpaicg_result['status'] = 'success';
                        $wpaicg_result['data'] = $result->events;
                    }
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('Fine Tune not found','gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_get_finetune()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $wpaicg_file = get_post(sanitize_text_field($_POST['id']));
                if($wpaicg_file){
                    $open_ai = WPAICG_OpenAI::get_instance()->openai();
                    if(!$open_ai){
                        $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $result = $open_ai->retrieveFineTune($wpaicg_file->post_title);
                    $result = json_decode($result);
                    if(isset($result->error)){
                        $wpaicg_result['msg'] = $result->error->message;
                    }
                    else{
                        $wpaicg_result['status'] = 'success';
                        $wpaicg_result['data'] = $result;
                    }
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('Fine Tune not found','gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_other_finetune()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(
                isset($_POST['id'])
                && !empty($_POST['id'])
                && isset($_POST['type'])
                && !empty($_POST['type'])
                && in_array($_POST['type'], array('hyperparams','result_files','training_files','events'))
            ){
                $wpaicg_type = sanitize_text_field($_POST['type']);
                $wpaicg_file = get_post(sanitize_text_field($_POST['id']));
                if($wpaicg_file){
                    $open_ai = WPAICG_OpenAI::get_instance()->openai();
                    if(!$open_ai){
                        $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $result = $open_ai->retrieveFineTune($wpaicg_file->post_title);
                    $result = json_decode($result);
                    if(isset($result->error)){
                        $wpaicg_result['msg'] = $result->error->message;
                    }
                    elseif(isset($result->$wpaicg_type)){
                        $wpaicg_data = $result->$wpaicg_type;
                        ob_start();
                        include WPAICG_PLUGIN_DIR.'admin/views/finetune/'.$wpaicg_type.'.php';
                        $wpaicg_result['html'] = ob_get_clean();
                        $wpaicg_result['status'] = 'success';
                    }
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('Fine Tune not found','gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_delete_finetune_file()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $wpaicg_file = get_post(sanitize_text_field($_POST['id']));
                if($wpaicg_file){
                    $open_ai = WPAICG_OpenAI::get_instance()->openai();
                    if(!$open_ai){
                        $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $result = $open_ai->deleteFile($wpaicg_file->post_title);
                    $result = json_decode($result);
                    if(isset($result->error)){
                        $wpaicg_result['msg'] = $result->error->message;
                    }
                    else{
                        wp_delete_post($wpaicg_file->ID);
                        $wpaicg_result['status'] = 'success';
                    }
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('File not found','gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_delete_finetune()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $wpaicg_file = get_post(sanitize_text_field($_POST['id']));
                if($wpaicg_file){
                    $open_ai = WPAICG_OpenAI::get_instance()->openai();
                    if(!$open_ai){
                        $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $ft_model = get_post_meta($wpaicg_file->ID,'wpaicg_name',true);
                    if(!empty($ft_model)) {
                        $result = $open_ai->deleteFineTune($ft_model);
                        $result = json_decode($result);
                        if (isset($result->error)) {
                            $wpaicg_result['msg'] = $result->error->message;
                        } else {
                            update_post_meta($wpaicg_file->ID,'wpaicg_deleted','1');
                            $wpaicg_result['status'] = 'success';
                        }
                    }
                    else{
                        $wpaicg_result['msg'] = esc_html__('That model does not exist','gpt3-ai-content-generator');
                    }
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('File not found','gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_cancel_finetune()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $wpaicg_file = get_post(sanitize_text_field($_POST['id']));
                if($wpaicg_file){
                    $open_ai = WPAICG_OpenAI::get_instance()->openai();
                    if(!$open_ai){
                        $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                        wp_send_json($wpaicg_result);
                    }
                    $result = $open_ai->cancelFineTune($wpaicg_file->post_title);
                    $result = json_decode($result);
                    if(isset($result->error)){
                        $wpaicg_result['msg'] = $result->error->message;
                    }
                    else{
                        add_post_meta($wpaicg_file->ID, 'wpaicg_status', 'cancelled');
                        $wpaicg_result['status'] = 'success';
                    }
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('File not found','gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_finetunes()
        {
            global $wpdb;
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            $open_ai = WPAICG_OpenAI::get_instance()->openai();
            if(!$open_ai){
                $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            $result = $open_ai->listFineTunes();
            $result = json_decode($result);
            if(isset($result->error)){
                $wpaicg_result['msg'] = $result->error->message;
            }
            else{
                if(isset($result->data) && is_array($result->data) && count($result->data)){
                    $wpaicg_result['status'] = 'success';
                    $wpaicgExist = array();
                    $finetone_models = array();
                    foreach($result->data as $item){
                        $wpaicgExist[] = $item->id;
                        $wpaicg_check = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->posts." WHERE post_type='wpaicg_finetune' AND post_title=%s",$item->id));
                        if(!$wpaicg_check) {
                            $wpaicg_file_id = wp_insert_post(array(
                                'post_title' => $item->id,
                                'post_date' => date('Y-m-d H:i:s', $item->created_at),
                                'post_status' => 'publish',
                                'post_type' => 'wpaicg_finetune',
                            ));
                            if (!is_wp_error($wpaicg_file_id)) {
                                add_post_meta($wpaicg_file_id, 'wpaicg_model', $item->model);
                                add_post_meta($wpaicg_file_id, 'wpaicg_updated_at', date('Y-m-d H:i:s', $item->updated_at));
                                add_post_meta($wpaicg_file_id, 'wpaicg_name', $item->fine_tuned_model);
                                add_post_meta($wpaicg_file_id, 'wpaicg_org', $item->organization_id);
                                add_post_meta($wpaicg_file_id, 'wpaicg_status', $item->status);
                                add_post_meta($wpaicg_file_id, 'wpaicg_fine_tune', $item->training_files->id);
                            } else {
                                $wpaicg_result['status'] = 'error';
                                $wpaicg_result['msg'] = $wpaicg_file_id->get_error_message();
                                break;
                            }
                        }
                        else{
                            $wpaicg_file_id = $wpaicg_check->ID;
                            update_post_meta($wpaicg_check->ID, 'wpaicg_model', $item->model);
                            update_post_meta($wpaicg_check->ID, 'wpaicg_updated_at', date('Y-m-d H:i:s', $item->updated_at));
                            update_post_meta($wpaicg_check->ID, 'wpaicg_name', $item->fine_tuned_model);
                            update_post_meta($wpaicg_check->ID, 'wpaicg_org', $item->organization_id);
                            update_post_meta($wpaicg_check->ID, 'wpaicg_status', $item->status);
                            if(isset($item->training_files->id)) {
                                update_post_meta($wpaicg_check->ID, 'wpaicg_fine_tune', $item->training_files->id);
                            }
                        }
                        if(!empty($item->fine_tuned_model)) {
                            $resultModel = $open_ai->retrieveModel($item->fine_tuned_model);
                            $resultModel = json_decode($resultModel);
                            if(isset($resultModel->error)){
                                wp_delete_post($wpaicg_file_id);
                            }
                            elseif($item->status == 'succeeded'){
                                $finetone_models[] = $item->fine_tuned_model;
                            }
                        }
                    }
                    update_option('wpaicg_custom_models',$finetone_models);
                    if(count($wpaicgExist)){
                        $commaDelimitedPlaceholders = implode(',', array_fill(0, count($wpaicgExist), '%s'));
                        $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->posts." WHERE post_type='wpaicg_finetune' AND post_title NOT IN ($commaDelimitedPlaceholders)",$wpaicgExist));
                    }
                    else{
                        $wpdb->query("DELETE FROM ".$wpdb->posts." WHERE post_type='wpaicg_finetune'");
                    }
                }
                else{
                    $wpaicg_result['status'] = 'success';
                    $wpdb->query("DELETE FROM ".$wpdb->posts." WHERE post_type='wpaicg_finetune'");
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_save_files($items)
        {
            global $wpdb;
            $wpaicgExist = array();
            foreach($items as $item){
                if($item->purpose !== 'fine-tune-results' && $item->status != 'deleted') {
                    $wpaicg_check = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->posts . " WHERE post_type='wpaicg_file' AND post_title=%s",$item->id));
                    $wpaicgExist[] = $item->id;
                    if (!$wpaicg_check) {
                        $wpaicg_file_id = wp_insert_post(array(
                            'post_title' => $item->id,
                            'post_date' => date('Y-m-d H:i:s', $item->created_at),
                            'post_status' => 'publish',
                            'post_type' => 'wpaicg_file',
                        ));
                        if (!is_wp_error($wpaicg_file_id)) {
                            add_post_meta($wpaicg_file_id, 'wpaicg_filename', $item->filename);
                            add_post_meta($wpaicg_file_id, 'wpaicg_purpose', $item->purpose);
                            add_post_meta($wpaicg_file_id, 'wpaicg_file_size', $item->bytes);
                        } else {
                            $wpaicg_result['status'] = 'error';
                            $wpaicg_result['msg'] = $wpaicg_file_id->get_error_message();
                            break;
                        }
                    } else {
                        update_post_meta($wpaicg_check->ID, 'wpaicg_filename', $item->filename);
                        update_post_meta($wpaicg_check->ID, 'wpaicg_purpose', $item->purpose);
                        update_post_meta($wpaicg_check->ID, 'wpaicg_file_size', $item->bytes);
                    }

                }
            }
            if(count($wpaicgExist)) {
                $commaDelimitedPlaceholders = implode(',', array_fill(0, count($wpaicgExist), '%s'));
                $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->posts . " WHERE post_type='wpaicg_file' AND post_title NOT IN ($commaDelimitedPlaceholders)", $wpaicgExist));
            }
            else{
                $wpdb->query("DELETE FROM ".$wpdb->posts." WHERE post_type='wpaicg_file'");
            }
        }

        public function wpaicg_files()
        {
            global $wpdb;
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            $open_ai = WPAICG_OpenAI::get_instance()->openai();
            if(!$open_ai){
                $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            $result = $open_ai->listFiles();
            $result = json_decode($result);
            if(isset($result->error)){
                $wpaicg_result['msg'] = $result->error->message;
            }
            else{
                if(isset($result->data) && is_array($result->data) && count($result->data)){
                    $wpaicg_result['status'] = 'success';
                    $this->wpaicg_save_files($result->data);
                }
                else{
                    $wpaicg_result['status'] = 'success';
                    $wpdb->query("DELETE FROM ".$wpdb->posts." WHERE post_type='wpaicg_file'");
                }
            }
            wp_send_json($wpaicg_result);
        }

        public static function wpaicg_finetune()
        {
            include WPAICG_PLUGIN_DIR.'admin/views/finetune/index.php';
        }
    }
    WPAICG_FineTune::get_instance();
}
