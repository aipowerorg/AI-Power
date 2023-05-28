<?php

namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( '\\WPAICG\\WPAICG_Content' ) ) {
    final class WPAICG_Content
    {
        private static  $instance = null ;
        public  $wpaicg_token_price = 0.02 / 1000 ;
        public  $wpaicg_limit_titles = 5 ;
        public  $wpaicg_extra_titles = 15 ;

        public static function get_instance()
        {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            if(wpaicg_util_core()->wpaicg_is_pro()){
                $this->wpaicg_limit_titles = 100;
            }
            add_action( 'admin_menu', array( $this, 'wpaicg_content_menu' ) );
            add_action( 'wp_ajax_wpaicg_save_draft_post_extra', array( $this, 'wpaicg_save_draft_post' ) );
            add_action( 'wp_ajax_wpaicg_bulk_generator', array( $this, 'wpaicg_bulk_save' ) );
            add_action( 'wp_ajax_wpaicg_bulk_save_editor', array( $this, 'wpaicg_bulk_save_editor' ) );
            add_action( 'wp_ajax_wpaicg_bulk_cancel', array( $this, 'wpaicg_bulk_cancel' ) );
            add_action( 'wp_ajax_wpaicg_bulk_status', array( $this, 'wpaicg_bulk_status' ) );
            add_action( 'wp_ajax_wpaicg_read_csv', array( $this, 'wpaicg_read_csv' ) );
            add_action( 'wp_ajax_wpaicg_speech_record', array( $this, 'wpaicg_speech_record' ) );
        }

        public function wpaicg_speech_record()
        {
            $mime_types = ['mp3' => 'audio/mpeg','mp4' => 'video/mp4','mpeg' => 'video/mpeg','m4a' => 'audio/m4a','wav' => 'audio/wav','webm' => 'video/webm'];
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            $open_ai = WPAICG_OpenAI::get_instance()->openai();
            if (!$open_ai) {
                $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
                exit;
            }
            $file = $_FILES['audio'];
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
            $data_request = array(
                'audio' => array(
                    'filename' => $file_name,
                    'data' => file_get_contents($tmp_file)
                ),
                'model' => 'whisper-1',
                'response_format' => 'json'
            );
            $completion = $open_ai->transcriptions($data_request);
            $completion = json_decode($completion);
            if($completion && isset($completion->error)){
                $wpaicg_result['msg'] = $completion->error->message;
                if(empty($wpaicg_result['msg']) && isset($completion->error->code) && $completion->error->code == 'invalid_api_key'){
                    $wpaicg_result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                }
                wp_send_json($wpaicg_result);
            }
            $text_generated = trim($completion->text);
            if(empty($text_generated)){
                $wpaicg_result['msg'] = esc_html__('Please speak louder or say more words for accurate recognition.','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            $wpaicg_data_request = [
                'model' => 'gpt-3.5-turbo',
                'prompt' => $text_generated,
                'temperature' => 0.7,
                'max_tokens' => 2000,
                'frequency_penalty' => 0.01,
                'presence_penalty' => 0.01,
            ];
            $wpaicg_generator = WPAICG_Generator::get_instance();
            $wpaicg_generator->openai($open_ai);
            $result = $wpaicg_generator->wpaicg_request($wpaicg_data_request);
            if($result['status'] == 'error'){
                $wpaicg_result['msg'] = $result['msg'];
                wp_send_json($wpaicg_result);
            }
            $wpaicg_result['data'] = $result['data'];
            $wpaicg_result['status'] = 'success';
            $wpaicg_result['text'] = $text_generated;
            $wpaicg_result['tokens'] = $result['tokens'];
            $wpaicg_result['length'] = $result['length'];
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_read_csv()
        {
            $wpaicg_result = array(
                'status' => 'error',
                'msg'    => esc_html__('Something went wrong','gpt3-ai-content-generator'),
            );
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if ( !empty($_FILES['file']) && empty($_FILES['file']['error']) ) {
                $wpaicg_file = $_FILES['file'];
                $wpaicg_csv_lines = array();

                if ( ($handle = fopen( $wpaicg_file['tmp_name'], 'r' )) !== false ) {
                    while ( ($data = fgetcsv( $handle, 100, ',' )) !== false ) {
                        if ( isset( $data[0] ) && !empty($data[0]) ) {
                            $wpaicg_csv_lines[] = $data[0];
                        }
                    }
                    fclose( $handle );
                }


                if ( count( $wpaicg_csv_lines ) ) {
                    if ( count( $wpaicg_csv_lines ) > $this->wpaicg_limit_titles ) {

                        if ( wpaicg_util_core()->wpaicg_is_pro() ) {
                            $wpaicg_result['notice'] = sprintf(esc_html__('Your CSV was including more than %d lines so we are only processing first 10 lines','gpt3-ai-content-generator'),$this->wpaicg_limit_titles);
                        } else {
                            $wpaicg_result['notice'] = sprintf(esc_html__('Free users can only generate %d titles at a time. Please upgrade to the Pro plan to get access to more fields.','gpt3-ai-content-generator'),$this->wpaicg_limit_titles);
                        }

                    }
                    $wpaicg_result['status'] = 'success';
                    $wpaicg_result['data'] = implode( '|', array_splice( $wpaicg_csv_lines, 0, $this->wpaicg_limit_titles ) );
                } else {
                    $wpaicg_result['msg'] = esc_html__('Your CSV file is empty','gpt3-ai-content-generator');
                }

            }
            wp_send_json( $wpaicg_result );
        }


        public function wpaicg_content_menu()
        {
            add_submenu_page(
                'wpaicg',
                esc_html__('Content Writer','gpt3-ai-content-generator'),
                esc_html__('Content Writer','gpt3-ai-content-generator'),
                'wpaicg_single_content',
                'wpaicg_single_content',
                array( $this, 'wpaicg_single_content' ),
                1
            );
            add_submenu_page(
                'wpaicg',
                esc_html__('AutoGPT','gpt3-ai-content-generator'),
                esc_html__('AutoGPT','gpt3-ai-content-generator'),
                'wpaicg_bulk_content',
                'wpaicg_bulk_content',
                array( $this, 'wpaicg_bulk_content' ),
                2
            );
            add_submenu_page(
                'edit.php',
                esc_html__('Generate New Post','gpt3-ai-content-generator'),
                esc_html__('Generate New Post','gpt3-ai-content-generator'),
                'wpaicg_single_content',
                'wpaicg_single_content',
                array( $this, 'wpaicg_single_content' )
            );
        }

        public function wpaicg_single_content()
        {
            include WPAICG_PLUGIN_DIR . 'admin/extra/wpaicg_single.php';
        }

        public function wpaicg_bulk_content()
        {
            include WPAICG_PLUGIN_DIR . 'admin/extra/wpaicg_bulk.php';
        }

        public function wpaicg_bulk_cancel()
        {
            // Check nonce

            $wpaicg_result = array(
                'status' => 'error',
                'msg'    => esc_html__('Something went wrong','gpt3-ai-content-generator'),
            );
            if (!isset($_POST['wpaicg_nonce']) || !wp_verify_nonce($_POST['wpaicg_nonce'], 'wpaicg_nonce_action')) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if ( isset( $_POST['ids'] ) && !empty($_POST['ids']) ) {
                $wpaicg_ids = wpaicg_util_core()->sanitize_text_or_array_field($_POST['ids']);
                $wpaicg_bulks = get_posts( array(
                    'post_type'      => 'wpaicg_bulk',
                    'post_status'    => array(
                        'publish',
                        'pending',
                        'draft',
                        'trash'
                    ),
                    'post__in'       => $wpaicg_ids,
                    'posts_per_page' => -1,
                ) );

                if ( $wpaicg_bulks && is_array( $wpaicg_bulks ) && count( $wpaicg_bulks ) ) {
                    $wpaicg_bulk_id = false;
                    foreach ( $wpaicg_bulks as $wpaicg_bulk ) {
                        $wpaicg_bulk_id = $wpaicg_bulk->post_parent;
                        wp_update_post( array(
                            'ID'          => $wpaicg_bulk->ID,
                            'post_status' => 'inherit',
                        ) );
                    }
                    if ( $wpaicg_bulk_id && !empty($wpaicg_bulk_id) ) {
                        wp_update_post( array(
                            'ID'          => $wpaicg_bulk_id,
                            'post_status' => 'trash',
                        ) );
                    }
                }

            }

            wp_send_json( $wpaicg_result );
        }

        public function wpaicg_valid_date( $date, $format = 'Y-m-d H:i:s' )
        {
            $d = \DateTime::createFromFormat( $format, $date );
            return $d && $d->format( $format ) == $date;
        }

        public function wpaicg_bulk_save()
        {
            $wpaicg_result = array(
                'status' => 'error',
                'msg'    => esc_html__('Something went wrong','gpt3-ai-content-generator'),
            );
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if (isset($_POST['wpaicg_titles']) && !empty($_POST['wpaicg_titles'])) {
                $wpaicg_titles = wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_titles']);
                $wpaicg_schedules = (isset($_POST['wpaicg_schedules']) && !empty($_POST['wpaicg_schedules']) ? wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_schedules']) : array());
                $wpaicg_category = (isset($_POST['wpaicg_category']) && !empty($_POST['wpaicg_category']) ? wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_category']) : array());

                if (is_array($wpaicg_titles)) {
                    $post_status = (isset($_POST['post_status']) && !empty($_POST['post_status']) ? sanitize_text_field($_POST['post_status']) : 'draft');
                    $waicg_track_title = '';
                    foreach ($wpaicg_titles as $wpaicg_title) {
                        if (!empty($wpaicg_title)) {
                            $waicg_track_title .= (empty($waicg_track_title) ? trim($wpaicg_title) : ', ' . $wpaicg_title);
                        }
                    }
                    $wpaicg_source = (isset($_POST['source']) && !empty($_POST['source']) ? sanitize_text_field($_POST['source']) : 'editor');

                    if (!empty($waicg_track_title)) {
                        $wpaicg_track_id = wp_insert_post(array(
                            'post_type' => 'wpaicg_tracking',
                            'post_title' => $waicg_track_title,
                            'post_status' => 'pending',
                            'post_mime_type' => $wpaicg_source,
                        ));

                        if (!is_wp_error($wpaicg_track_id)) {
                            foreach ($wpaicg_titles as $key => $wpaicg_title) {

                                if (!empty($wpaicg_title)) {
                                    $wpaicg_bulk_data = array(
                                        'post_type' => 'wpaicg_bulk',
                                        'post_title' => trim($wpaicg_title),
                                        'post_status' => 'pending',
                                        'post_parent' => $wpaicg_track_id,
                                        'post_password' => $post_status,
                                        'post_mime_type' => $wpaicg_source,
                                    );
                                    if(isset($_POST['post_author']) && !empty($_POST['post_author'])){
                                        $wpaicg_bulk_data['post_author'] = sanitize_text_field($_POST['post_author']);
                                    }
                                    if (isset($wpaicg_schedules[$key]) && !empty($wpaicg_schedules[$key])) {
                                        $wpaicg_item_schedule = $wpaicg_schedules[$key] . ':00';
                                        if ($this->wpaicg_valid_date($wpaicg_item_schedule)) {
                                            $wpaicg_bulk_data['post_excerpt'] = $wpaicg_item_schedule;
                                        }
                                    }

                                    if (isset($wpaicg_category[$key]) && !empty($wpaicg_category[$key])) {
                                        $wpaicg_bulk_data['menu_order'] = sanitize_text_field($wpaicg_category[$key]);
                                    }

                                    wp_insert_post($wpaicg_bulk_data);
                                }

                            }
                            $wpaicg_result['id'] = $wpaicg_track_id;
                            $wpaicg_result['status'] = 'success';
                        }

                    }

                }

            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_bulk_save_editor()
        {
            $wpaicg_result = array(
                'status' => 'error',
                'msg'    => esc_html__('Something went wrong','gpt3-ai-content-generator'),
            );
            if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wpaicg_bulk_save' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['bulk']) && is_array($_POST['bulk']) && count($_POST['bulk'])){
                $post_status = ( isset( $_POST['post_status'] ) && !empty($_POST['post_status']) ? sanitize_text_field( $_POST['post_status'] ) : 'draft' );
                $bulks = wpaicg_util_core()->sanitize_text_or_array_field($_POST['bulk']);
                $waicg_track_title = '';
                foreach($bulks as $bulk){
                    if (isset($bulk['title']) && !empty($bulk['title'])) {
                        $waicg_track_title .= ( empty($waicg_track_title) ? trim( $bulk['title'] ) : ', ' . $bulk['title'] );
                    }
                }
                $wpaicg_source = ( isset( $_POST['source'] ) && !empty($_POST['source']) ? sanitize_text_field( $_POST['source'] ) : 'editor' );
                if ( !empty($waicg_track_title) ) {
                    $wpaicg_track_id = wp_insert_post(array(
                        'post_type' => 'wpaicg_tracking',
                        'post_title' => $waicg_track_title,
                        'post_status' => 'pending',
                        'post_mime_type' => $wpaicg_source,
                        'post_content' => ' '
                    ),true);
                    if ( !is_wp_error( $wpaicg_track_id ) ) {
                        foreach ($bulks as $bulk) {
                            if (isset($bulk['title']) && !empty($bulk['title'])) {
                                $wpaicg_bulk_data = array(
                                    'post_type'      => 'wpaicg_bulk',
                                    'post_title'     => trim( $bulk['title'] ),
                                    'post_status'    => 'pending',
                                    'post_parent'    => $wpaicg_track_id,
                                    'post_password'  => $post_status,
                                    'post_mime_type' => $wpaicg_source,
                                    'post_content' => ' '
                                );
                                if(isset($bulk['schedule']) && !empty($bulk['schedule'])){
                                    $wpaicg_item_schedule = $bulk['schedule'] . ':00';
                                    if ( $this->wpaicg_valid_date( $wpaicg_item_schedule ) ) {
                                        $wpaicg_bulk_data['post_excerpt'] = $wpaicg_item_schedule;
                                    }
                                }
                                if(isset($bulk['category']) && !empty($bulk['category'])){
                                    $wpaicg_bulk_data['menu_order'] = sanitize_text_field($bulk['category']);
                                }
                                if(isset($bulk['author']) && !empty($bulk['author'])){
                                    $wpaicg_bulk_data['post_author'] = sanitize_text_field($bulk['author']);
                                }
                                $wpaicg_bulk_id = wp_insert_post( $wpaicg_bulk_data );
                                if(isset($bulk['tags']) && !empty($bulk['tags'])){
                                    update_post_meta($wpaicg_bulk_id, '_wpaicg_tags', sanitize_text_field($bulk['tags']));
                                }
                                if(isset($bulk['keywords']) && !empty($bulk['keywords'])){
                                    update_post_meta($wpaicg_bulk_id, '_wpaicg_keywords', sanitize_text_field($bulk['keywords']));
                                }
                                if(isset($bulk['avoid']) && !empty($bulk['avoid'])){
                                    update_post_meta($wpaicg_bulk_id, '_wpaicg_avoid', sanitize_text_field($bulk['avoid']));
                                }
                                if(isset($bulk['anchor']) && !empty($bulk['anchor'])){
                                    update_post_meta($wpaicg_bulk_id, '_wpaicg_anchor', sanitize_text_field($bulk['anchor']));
                                }
                                if(isset($bulk['target']) && !empty($bulk['target'])){
                                    update_post_meta($wpaicg_bulk_id, '_wpaicg_target', sanitize_text_field($bulk['target']));
                                }
                                if(isset($bulk['cta']) && !empty($bulk['cta'])){
                                    update_post_meta($wpaicg_bulk_id, '_wpaicg_cta', sanitize_text_field($bulk['cta']));
                                }
                            }
                        }
                        $wpaicg_result['id'] = $wpaicg_track_id;
                        $wpaicg_result['status'] = 'success';
                    }
                    else{
                        $wpaicg_result['msg'] = $wpaicg_track_id->get_error_message();
                    }
                }
            }
            wp_send_json( $wpaicg_result );
        }

        public function wpaicg_bulk_status()
        {
            $wpaicg_result = array(
                'status' => 'error',
                'msg'    => esc_html__('Something went wrong','gpt3-ai-content-generator'),
            );
            if (!isset($_POST['wpaicg_nonce']) || !wp_verify_nonce($_POST['wpaicg_nonce'], 'wpaicg_nonce_action')) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if ( isset( $_POST['ids'] ) && !empty($_POST['ids']) ) {
                $wpaicg_ids = wpaicg_util_core()->sanitize_text_or_array_field($_POST['ids']);
                $wpaicg_bulks = get_posts( array(
                    'post_type'      => 'wpaicg_bulk',
                    'post_status'    => array(
                        'publish',
                        'pending',
                        'draft',
                        'trash',
                        'inherit'
                    ),
                    'post__in'       => $wpaicg_ids,
                    'posts_per_page' => -1,
                ) );

                if ( $wpaicg_bulks && is_array( $wpaicg_bulks ) && count( $wpaicg_bulks ) ) {
                    $wpaicg_result['data'] = array();
                    $wpaicg_result['status'] = 'success';
                    foreach ( $wpaicg_bulks as $wpaicg_bulk ) {
                        $wpaicg_generator_run = get_post_meta( $wpaicg_bulk->ID, '_wpaicg_generator_run', true );
                        $wpaicg_generator_length = get_post_meta( $wpaicg_bulk->ID, '_wpaicg_generator_length', true );
                        $wpaicg_generator_token = get_post_meta( $wpaicg_bulk->ID, '_wpaicg_generator_token', true );
                        $wpaicg_generator_post_id = get_post_meta( $wpaicg_bulk->ID, '_wpaicg_generator_post', true );
                        $wpaicg_cost = 0;
                        $wpaicg_ai_model = get_post_meta($wpaicg_bulk->ID,'wpaicg_ai_model',true);
                        if(!empty($wpaicg_generator_token)) {
                            if ($wpaicg_ai_model == 'gpt-3.5-turbo') {
                                $wpaicg_cost = '$' . esc_html(number_format($wpaicg_generator_token * 0.002 / 1000, 5));
                            }
                            elseif ($wpaicg_ai_model == 'gpt-4') {
                                $wpaicg_cost = '$' . esc_html(number_format($wpaicg_generator_token * 0.06 / 1000, 5));
                            }
                            elseif ($wpaicg_ai_model == 'gpt-4-32k') {
                                $wpaicg_cost = '$' . esc_html(number_format($wpaicg_generator_token * 0.12 / 1000, 5));
                            } else {
                                $wpaicg_cost = '$' . esc_html(number_format($wpaicg_generator_token * $this->wpaicg_token_price, 5));
                            }
                        }
                        $wpaicg_result['data'][] = array(
                            'id'       => $wpaicg_bulk->ID,
                            'title'    => $wpaicg_bulk->post_title,
                            'status'   => $wpaicg_bulk->post_status,
                            'duration' => ( $wpaicg_generator_run ? $this->wpaicg_seconds_to_time( (int) $wpaicg_generator_run ) : '' ),
                            'word'     => $wpaicg_generator_length,
                            'token'    => $wpaicg_generator_token,
                            'cost'     => $wpaicg_cost,
                            'msg'      => get_post_meta( $wpaicg_bulk->ID, '_wpaicg_error', true ),
                            'url'      => ( empty($wpaicg_generator_post_id) ? '' : admin_url( 'post.php?post=' . $wpaicg_generator_post_id . '&action=edit' ) ),
                        );
                    }
                }

            }

            wp_send_json( $wpaicg_result );
        }

        public function wpaicg_save_description($post_id, $description)
        {
            global $wpdb;
            update_post_meta($post_id,'_wpaicg_meta_description',$description);
            $seo_option = get_option('_yoast_wpseo_metadesc',false);
            $seo_plugin_activated = wpaicg_util_core()->seo_plugin_activated();
            if($seo_plugin_activated == '_yoast_wpseo_metadesc' && $seo_option){
                update_post_meta($post_id,$seo_plugin_activated,$description);
            }
            $seo_option = get_option('_aioseo_description',false);
            if($seo_plugin_activated == '_aioseo_description' && $seo_option){
                update_post_meta($post_id,$seo_plugin_activated,$description);
                $check = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."aioseo_posts WHERE post_id=%d",$post_id));
                if($check){
                    $wpdb->update($wpdb->prefix.'aioseo_posts',array(
                        'description' => $description
                    ), array(
                        'post_id' => $post_id
                    ));
                }
                else{
                    $wpdb->insert($wpdb->prefix.'aioseo_posts',array(
                        'post_id' => $post_id,
                        'description' => $description,
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s')
                    ));
                }
            }
            $seo_option = get_option('rank_math_description',false);
            if($seo_plugin_activated == 'rank_math_description' && $seo_option){
                update_post_meta($post_id,$seo_plugin_activated,$description);
            }
        }

        public function wpaicg_bulk_error_log($id, $msg)
        {
            update_post_meta( $id, '_wpaicg_error', $msg );
            wp_update_post( array(
                'ID'          => $id,
                'post_status' => 'trash',
            ) );
        }

        public function wpaicg_bulk_generator()
        {
            global  $wpdb ;
            $wpaicg_cron_added = get_option( '_wpaicg_cron_added', '' );

            if ( empty($wpaicg_cron_added) ) {
                update_option( '_wpaicg_cron_added', time() );
            } else {
                $sql = "SELECT * FROM " . $wpdb->posts . " WHERE post_type='wpaicg_bulk' AND post_status='pending' ORDER BY post_date ASC";
                $wpaicg_single = $wpdb->get_row( $sql );
                update_option( '_wpaicg_crojob_bulk_last_time', time() );
                /* Fix in progress task stuck*/
                $wpaicg_restart_queue = get_option('wpaicg_restart_queue','');
                $wpaicg_try_queue = get_option('wpaicg_try_queue','');
                if(!empty($wpaicg_restart_queue) && !empty($wpaicg_try_queue)) {
                    $wpaicg_fix_sql = $wpdb->prepare("SELECT p.ID,(SELECT m.meta_value FROM ".$wpdb->postmeta." m WHERE m.post_id=p.ID AND m.meta_key='wpaicg_try_queue_time') as try_time FROM ".$wpdb->posts." p WHERE (p.post_status='draft' OR p.post_status='trash') AND p.post_type='wpaicg_bulk' AND p.post_modified <  NOW() - INTERVAL %d MINUTE",$wpaicg_restart_queue);
                    $in_progress_posts = $wpdb->get_results($wpaicg_fix_sql);
                    if($in_progress_posts && is_array($in_progress_posts) && count($in_progress_posts)){
                        foreach($in_progress_posts as $in_progress_post){
                            if(!$in_progress_post->try_time || (int)$in_progress_post->try_time < $wpaicg_try_queue){
                                wp_update_post(array(
                                    'ID'          => $in_progress_post->ID,
                                    'post_status' => 'pending',
                                ));
                                wp_update_post(array(
                                    'ID'          => $in_progress_post->post_parent,
                                    'post_status' => 'pending',
                                ));
                                $next_time = (int)$in_progress_post->try_time + 1;
                                update_post_meta($in_progress_post->ID,'wpaicg_try_queue_time',$next_time);
                            }
                        }
                    }
                }
                /* END fix stuck */
                if ( $wpaicg_single ) {
                    $wpaicg_generator_start = microtime( true );
                    $wpaicg_generator_tokens = 0;
                    $wpaicg_generator_text_length = 0;
                    try {
                        wp_update_post( array(
                            'ID'          => $wpaicg_single->ID,
                            'post_status' => 'draft',
                            'post_modified' => date('Y-m-d H:i:s')
                        ) );
                        $wpaicg_generator = WPAICG_Generator::get_instance();
                        $openai = WPAICG_OpenAI::get_instance()->openai();
                        $wpaicg_generator_result = array();
                        if(!$openai){
                            $this->wpaicg_bulk_error_log($wpaicg_single->ID, esc_html__('Missing API Setting','gpt3-ai-content-generator'));
                        }
                        else{
                            $steps = array('heading','content','intro','faq','conclusion','tagline','seo','addition','image','featuredimage');
                            $wpaicg_generator->init($openai,$wpaicg_single->post_title,true,$wpaicg_single->ID);
                            $wpaicg_has_error = false;
                            $break_step = '';
                            foreach ($steps as $step){
                                $wpaicg_generator->wpaicg_generator($step);
                                if($wpaicg_generator->error_msg){
                                    $break_step = $step;
                                    $wpaicg_has_error = $wpaicg_generator->error_msg;
                                    break;
                                }
                            }
                            if($wpaicg_has_error){
                                $this->wpaicg_bulk_error_log($wpaicg_single->ID, $wpaicg_has_error.'. '.esc_html__('Break at step','gpt3-ai-content-generator').' '.$break_step);
                                $wpaicg_running = WPAICG_PLUGIN_DIR.'/wpaicg_running.txt';
                                if(file_exists($wpaicg_running)){
                                    unlink($wpaicg_running);
                                }
                            }
                            else{
                                $wpaicg_generator_result = $wpaicg_generator->wpaicgResult();
                                $wpaicg_generator_text_length = $wpaicg_generator_result['length'];
                                $wpaicg_generator_tokens = $wpaicg_generator_result['tokens'];
                                $wpaicg_allowed_html_content_post = wp_kses_allowed_html( 'post' );
                                $wpaicg_content = wp_kses( $wpaicg_generator_result['content'], $wpaicg_allowed_html_content_post );
                                $wpaicg_post_status = ( $wpaicg_single->post_password == 'draft' ? 'draft' : 'publish' );
                                $wpaicg_image_attachment_id = false;
                                if(isset($wpaicg_generator_result['img']) && !empty($wpaicg_generator_result['img'])){
                                    $wpaicg_image_url = sanitize_url($wpaicg_generator_result['img']);
                                    $wpaicg_image_attachment_id = $this->wpaicg_save_image($wpaicg_image_url,$wpaicg_single->post_title);
                                    if($wpaicg_image_attachment_id['status'] == 'success'){
                                        $wpaicg_image_attachment_url = wp_get_attachment_url($wpaicg_image_attachment_id['id']);
                                        $wpaicg_content = str_replace("__WPAICG_IMAGE__", '<img src="'.$wpaicg_image_attachment_url.'" alt="'.$wpaicg_single->post_title.'" />', $wpaicg_content);
                                    }
                                }
                                // Fix empty image
                                $wpaicg_content = str_replace("__WPAICG_IMAGE__", '', $wpaicg_content);

                                $wpaicg_post_data = array(
                                    'post_title'   => $wpaicg_single->post_title,
                                    'post_author'  => $wpaicg_single->post_author,
                                    'post_content' => $wpaicg_content,
                                    'post_status'  => $wpaicg_post_status,
                                );
                                if($wpaicg_single->menu_order && $wpaicg_single->menu_order > 0){
                                    $wpaicg_post_data['post_category'] = array($wpaicg_single->menu_order);
                                }

                                if ( !empty($wpaicg_single->post_excerpt) ) {
                                    $wpaicg_post_data['post_status'] = 'future';
                                    $wpaicg_post_data['post_date'] = $wpaicg_single->post_excerpt;
                                    $wpaicg_post_data['post_date_gmt'] = $wpaicg_single->post_excerpt;
                                }

                                $wpaicg_post_id = wp_insert_post( $wpaicg_post_data );

                                if ( is_wp_error( $wpaicg_post_id ) ) {
                                    update_post_meta( $wpaicg_single->ID, '_wpaicg_error', $wpaicg_post_id->get_error_message() );
                                    wp_update_post( array(
                                        'ID'          => $wpaicg_single->ID,
                                        'post_status' => 'trash',
                                    ) );
                                } else {
                                    $wpaicg_ai_model = get_option('wpaicg_ai_model','text-davinci-003');
                                    add_post_meta($wpaicg_post_id,'wpaicg_ai_model',$wpaicg_ai_model);
                                    add_post_meta($wpaicg_single->ID,'wpaicg_ai_model',$wpaicg_ai_model);
                                    if(isset($wpaicg_generator_result['description']) && !empty($wpaicg_generator_result['description'])){
                                        $this->wpaicg_save_description($wpaicg_post_id,sanitize_text_field($wpaicg_generator_result['description']));
                                    }

                                    if(isset($wpaicg_generator_result['featured_img']) && !empty($wpaicg_generator_result['featured_img'])){
                                        $wpaicg_featured_image_url = sanitize_url($wpaicg_generator_result['featured_img']);
                                        $wpaicg_image_attachment_id = $this->wpaicg_save_image($wpaicg_featured_image_url,$wpaicg_single->post_title);
                                        if($wpaicg_image_attachment_id['status'] == 'success'){
                                            update_post_meta( $wpaicg_post_id, '_thumbnail_id', $wpaicg_image_attachment_id['id']);
                                        }
                                    }

                                    $wpaicg_tags = get_post_meta($wpaicg_single->ID, '_wpaicg_tags',true);
                                    if(!empty($wpaicg_tags)){
                                        $wpaicg_tags = array_map('trim', explode(',', $wpaicg_tags));
                                        if($wpaicg_tags && is_array($wpaicg_tags) && count($wpaicg_tags)){
                                            wp_set_post_tags($wpaicg_post_id,$wpaicg_tags);
                                        }
                                    }
                                    update_post_meta( $wpaicg_single->ID, '_wpaicg_generator_post', $wpaicg_post_id );
                                    wp_update_post( array(
                                        'ID'          => $wpaicg_single->ID,
                                        'post_status' => 'publish',
                                    ));
                                }
                                /*Save Last Content*/
                                if($wpaicg_single->post_mime_type == 'sheets'){
                                    update_option('wpaicg_cronjob_sheets_content',time());
                                }
                                elseif($wpaicg_single->post_mime_type == 'rss'){
                                    update_option('wpaicg_cronjob_rss_content',time());
                                }
                                else{
                                    update_option('wpaicg_cronjob_bulk_content',time());
                                }
                            }
                        }
                    } catch ( \Exception $exception ) {
                    }
                    $wpaicg_bulks = get_posts( array(
                        'post_type'      => 'wpaicg_bulk',
                        'post_status'    => array(
                            'publish',
                            'pending',
                            'draft',
                            'trash',
                            'inherit'
                        ),
                        'post_parent'    => $wpaicg_single->post_parent,
                        'posts_per_page' => -1,
                    ) );
                    $wpaicg_bulk_completed = true;
                    $wpaicg_bulk_error = false;
                    foreach ( $wpaicg_bulks as $wpaicg_bulk ) {
                        if ( $wpaicg_bulk->post_status == 'pending' || $wpaicg_bulk->post_status == 'draft' ) {
                            $wpaicg_bulk_completed = false;
                        }

                        if ( $wpaicg_bulk->post_status == 'trash' ) {
                            $wpaicg_bulk_error = true;
                            $wpaicg_bulk_completed = false;
                        }

                    }
                    if ( $wpaicg_bulk_completed ) {
                        wp_update_post( array(
                            'ID'          => $wpaicg_single->post_parent,
                            'post_status' => 'publish',
                        ) );
                    }
                    if ( $wpaicg_bulk_error ) {
                        wp_update_post( array(
                            'ID'          => $wpaicg_single->post_parent,
                            'post_status' => 'draft',
                        ) );
                    }
                    $wpaicg_generator_end = microtime( true ) - $wpaicg_generator_start;
                    update_post_meta( $wpaicg_single->ID, '_wpaicg_generator_run', $wpaicg_generator_end );
                    update_post_meta( $wpaicg_single->ID, '_wpaicg_generator_length', $wpaicg_generator_text_length );
                    update_post_meta( $wpaicg_single->ID, '_wpaicg_generator_token', $wpaicg_generator_tokens );
                }

            }

        }

        public function wpaicg_seconds_to_time( $seconds )
        {
            $dtF = new \DateTime( '@0' );
            $dtT = new \DateTime( "@{$seconds}" );
            return $dtF->diff( $dtT )->format( '%h hours, %i minutes and %s seconds' );
        }

        public function wpaicg_post_image($post_id, $wpaicg_title = '')
        {
            if(isset($_REQUEST['wpaicg_content_changed']) && !empty($_REQUEST['wpaicg_content_changed'])){
                $my_post = array(
                    'ID'          => $post_id,
                    'post_status' => 'draft',
                );
                if ( isset( $_REQUEST['_wporg_preview_title'] ) && $_REQUEST['_wporg_preview_title'] != '' ) {
                    $my_post['post_title'] = sanitize_text_field($_REQUEST['_wporg_preview_title']);
                }
                if ( isset( $_REQUEST['_wporg_generated_text'] ) && $_REQUEST['_wporg_generated_text'] != '' ) {
                    $my_post['post_content'] = wp_kses_post($_REQUEST['_wporg_generated_text']);
                }
                $wpaicg_content = $my_post['post_content'];
                $wpaicg_image_attachment_id = false;
                if(isset($_REQUEST['wpaicg_image_url']) && !empty($_REQUEST['wpaicg_image_url'])){
                    $wpaicg_image_url = sanitize_url($_REQUEST['wpaicg_image_url']);
                    $wpaicg_image_attachment_id = $this->wpaicg_save_image($wpaicg_image_url, $wpaicg_title);
                    if($wpaicg_image_attachment_id['status'] == 'success'){
                        $wpaicg_image_attachment_url = wp_get_attachment_url($wpaicg_image_attachment_id['id']);
                        $wpaicg_content = str_replace('<img />', '<img src="'.$wpaicg_image_attachment_url.'" alt="'.$wpaicg_title.'" />', $wpaicg_content);
                        $wpaicg_content = str_replace("<img src=\\'__WPAICG_IMAGE__\\' alt=\\'".$wpaicg_title."\\' />", '<img src="'.$wpaicg_image_attachment_url.'" alt="'.$wpaicg_title.'" />', $wpaicg_content);
                        $wpaicg_content = str_replace("<img src=\'__WPAICG_IMAGE__\' alt=\'".$wpaicg_title."\' />", '<img src="'.$wpaicg_image_attachment_url.'" alt="'.$wpaicg_title.'" />', $wpaicg_content);
                        $wpaicg_content = str_replace("__WPAICG_IMAGE__", '<img src="'.$wpaicg_image_attachment_url.'" alt="'.$wpaicg_title.'" />', $wpaicg_content);
                    }
                }
                // Fix empty image
                $wpaicg_content = str_replace("__WPAICG_IMAGE__", '', $wpaicg_content);
                $my_post['post_content'] = $wpaicg_content;
                if(isset($_REQUEST['wpaicg_featured_img_url']) && !empty($_REQUEST['wpaicg_featured_img_url'])){
                    $wpaicg_featured_img_url = sanitize_url($_REQUEST['wpaicg_featured_img_url']);
                    $wpaicg_image_attachment_id = $this->wpaicg_save_image($wpaicg_featured_img_url, $wpaicg_title);
                    if($wpaicg_image_attachment_id['status'] == 'success'){
                        update_post_meta( $post_id, '_thumbnail_id', $wpaicg_image_attachment_id['id']);
                    }
                }
                wp_update_post( $my_post );
            }
        }

        public function wpaicg_save_image($imageurl, $wpaicg_title = '')
        {
            global $wpdb;
            $result = array('status' => 'error', 'msg' => esc_html__('Can not save image to media','gpt3-ai-content-generator'));
            if(!function_exists('wp_generate_attachment_metadata')){
                include_once( ABSPATH . 'wp-admin/includes/image.php' );
            }
            if(!function_exists('download_url')){
                include_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            if(!function_exists('media_handle_sideload')){
                include_once( ABSPATH . 'wp-admin/includes/media.php' );
            }
            try {
                $array = explode('/', getimagesize($imageurl)['mime']);
                $imagetype = end($array);
                $uniq_name = md5($imageurl);
                $filename = $uniq_name . '.' . $imagetype;
                $checkExist = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE meta_value LIKE %s",'%/'.$wpdb->esc_like($filename)));
                if($checkExist){
                    $result['status'] = 'success';
                    $result['id'] = $checkExist->post_id;
                }
                else{
                    $tmp = download_url($imageurl);
                    if ( is_wp_error( $tmp ) ){
                        $result['msg'] = $tmp->get_error_message();
                        return $result;
                    }
                    $args = array(
                        'name' => $filename,
                        'tmp_name' => $tmp,
                    );
                    $attachment_id = media_handle_sideload( $args, 0, '',array(
                        'post_title'     => $wpaicg_title,
                        'post_content'   => $wpaicg_title,
                        'post_excerpt'   => $wpaicg_title
                    ));
                    if(!is_wp_error($attachment_id)){
                        update_post_meta($attachment_id,'_wp_attachment_image_alt', $wpaicg_title);
                        $imagenew = get_post( $attachment_id );
                        $fullsizepath = get_attached_file( $imagenew->ID );
                        $attach_data = wp_generate_attachment_metadata( $attachment_id, $fullsizepath );
                        wp_update_attachment_metadata( $attachment_id, $attach_data );
                        $result['status'] = 'success';
                        $result['id'] = $attachment_id;
                    }
                    else{
                        $result['msg'] = $attachment_id->get_error_message();
                        return $result;
                    }
                }
            }
            catch (\Exception $exception){
                $result['msg'] = $exception->getMessage();
            }
            return $result;
        }

        public function wpaicg_save_draft_post()
        {
            ini_set('max_execution_time', 1000);
            $wpaicg_result = array(
                'status' => 'error',
                'msg'    => esc_html__('Something went wrong','gpt3-ai-content-generator'),
            );
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if ( isset( $_POST['title'] ) && !empty($_POST['title']) && isset( $_POST['content'] ) && !empty($_POST['content']) ) {
                $wpaicg_allowed_html_content_post = wp_kses_allowed_html( 'post' );
                $wpaicg_title = sanitize_text_field( $_POST['title'] );
                $wpaicg_content = wp_kses( $_POST['content'], $wpaicg_allowed_html_content_post );
                $wpaicg_content = str_replace("__WPAICG_IMAGE__", '', $wpaicg_content);
                if(isset($_POST['post_id']) && !empty($_POST['post_id'])){
                    $wpaicg_post_id = sanitize_text_field($_POST['post_id']);
                    wp_update_post(array(
                        'ID' => $wpaicg_post_id,
                        'post_title' => $wpaicg_title,
                        'post_content' => $wpaicg_content,
                    ));
                }
                else {
                    $wpaicg_post_id = wp_insert_post(array(
                        'post_title' => $wpaicg_title,
                        'post_content' => $wpaicg_content,
                    ));
                }
                if ( !is_wp_error( $wpaicg_post_id ) ) {
                    if ( array_key_exists( 'wpaicg_settings', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_meta_key', wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_settings']) );
                    }
                    if ( array_key_exists( '_wporg_language', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_language', sanitize_text_field($_POST['_wporg_language']) );
                    }
                    if ( array_key_exists( '_wporg_preview_title', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_preview_title', sanitize_text_field($_POST['_wporg_preview_title']) );
                    }
                    if ( array_key_exists( '_wporg_number_of_heading', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_number_of_heading', sanitize_text_field($_POST['_wporg_number_of_heading']) );
                    }
                    if ( array_key_exists( '_wporg_heading_tag', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_heading_tag', sanitize_text_field($_POST['_wporg_heading_tag']) );
                    }
                    if ( array_key_exists( '_wporg_writing_style', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_writing_style', sanitize_text_field($_POST['_wporg_writing_style']) );
                    }
                    if ( array_key_exists( '_wporg_writing_tone', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_writing_tone', sanitize_text_field($_POST['_wporg_writing_tone']) );
                    }
                    if ( array_key_exists( '_wporg_modify_headings', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_modify_headings', sanitize_text_field($_POST['_wporg_modify_headings']) );
                    }
                    if ( array_key_exists( 'wpaicg_image_source', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_image_source', sanitize_text_field($_POST['wpaicg_image_source']) );
                    }
                    if ( array_key_exists( 'wpaicg_featured_image_source', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_featured_image_source', sanitize_text_field($_POST['wpaicg_featured_image_source']) );
                    }
                    if ( array_key_exists( 'wpaicg_pexels_orientation', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_pexels_orientation', sanitize_text_field($_POST['wpaicg_pexels_orientation']) );
                    }
                    if ( array_key_exists( 'wpaicg_pexels_size', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_pexels_size', sanitize_text_field($_POST['wpaicg_pexels_size']) );
                    }
                    if ( array_key_exists( 'wpaicg_pexels_enable_prompt', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_pexels_enable_prompt', sanitize_text_field($_POST['wpaicg_pexels_enable_prompt']) );
                    }
                    if ( array_key_exists( 'wpaicg_pexels_custom_prompt', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_pexels_custom_prompt', sanitize_text_field($_POST['wpaicg_pexels_custom_prompt']) );
                    }
                    if ( array_key_exists( 'wpaicg_pixabay_type', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_pixabay_type', sanitize_text_field($_POST['wpaicg_pixabay_type']) );
                    }
                    if ( array_key_exists( 'wpaicg_pixabay_language', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_pixabay_language', sanitize_text_field($_POST['wpaicg_pixabay_language']) );
                    }
                    if ( array_key_exists( 'wpaicg_pixabay_order', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_pixabay_order', sanitize_text_field($_POST['wpaicg_pixabay_order']) );
                    }
                    if ( array_key_exists( 'wpaicg_pixabay_orientation', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_pixabay_orientation', sanitize_text_field($_POST['wpaicg_pixabay_orientation']) );
                    }
                    if ( array_key_exists( 'wpaicg_pixabay_enable_prompt', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_pixabay_enable_prompt', sanitize_text_field($_POST['wpaicg_pixabay_enable_prompt']) );
                    }
                    if ( array_key_exists( 'wpaicg_pixabay_custom_prompt', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_pixabay_custom_prompt', sanitize_text_field($_POST['wpaicg_pixabay_custom_prompt']) );
                    }
                    if ( array_key_exists( '_wporg_add_tagline', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_add_tagline', sanitize_text_field($_POST['_wporg_add_tagline']) );
                    }
                    if ( array_key_exists( '_wporg_add_intro', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_add_intro', sanitize_text_field($_POST['_wporg_add_intro']) );
                    }
                    if ( array_key_exists( '_wporg_add_conclusion', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_add_conclusion', sanitize_text_field($_POST['_wporg_add_conclusion']) );
                    }
                    if ( array_key_exists( '_wporg_anchor_text', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_anchor_text', sanitize_text_field($_POST['_wporg_anchor_text']) );
                    }
                    if ( array_key_exists( '_wporg_target_url', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_target_url', sanitize_text_field($_POST['_wporg_target_url']) );
                    }
                    if ( array_key_exists( '_wporg_generated_text', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_generated_text', sanitize_text_field($_POST['_wporg_generated_text']) );
                    }
                    // _wporg_cta_pos
                    if ( array_key_exists( '_wporg_cta_pos', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_cta_pos', sanitize_text_field($_POST['_wporg_cta_pos']) );
                    }
                    // _wporg_target_url_cta
                    if ( array_key_exists( '_wporg_target_url_cta', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_target_url_cta', sanitize_text_field($_POST['_wporg_target_url_cta']) );
                    }
                    if ( array_key_exists( '_wporg_img_size', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_img_size', sanitize_text_field($_POST['_wporg_img_size']) );
                    }
                    if ( array_key_exists( '_wporg_img_style', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wporg_img_style', sanitize_text_field($_POST['_wporg_img_style']) );
                    }
                    if ( array_key_exists( 'wpaicg_seo_meta_desc', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wpaicg_seo_meta_desc', 1 );
                    }
                    if ( array_key_exists( 'wpaicg_custom_image_settings', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_custom_image_settings', wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_custom_image_settings']) );
                    }
                    if ( array_key_exists( 'wpaicg_custom_prompt_enable', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_custom_prompt_enable', sanitize_text_field($_POST['wpaicg_custom_prompt_enable']));
                    }
                    if ( array_key_exists( 'wpaicg_custom_prompt', $_POST ) && array_key_exists( 'wpaicg_custom_prompt_enable', $_POST ) && $_POST['wpaicg_custom_prompt_enable'] ) {
                        update_post_meta( $wpaicg_post_id, 'wpaicg_custom_prompt', wp_kses_post($_POST['wpaicg_custom_prompt']));
                    }
                    if ( array_key_exists( 'wpaicg_post_tags', $_POST ) ) {
                        update_post_meta( $wpaicg_post_id, '_wpaicg_post_tags', sanitize_text_field($_POST['wpaicg_post_tags']) );
                        if(!empty($_POST['wpaicg_post_tags'])){
                            $wpaicg_tags = array_map('trim', explode(',', sanitize_text_field($_POST['wpaicg_post_tags'])));
                            if($wpaicg_tags && is_array($wpaicg_tags) && count($wpaicg_tags)){
                                wp_set_post_tags($wpaicg_post_id,$wpaicg_tags);
                            }
                        }
                    }
                    if ( array_key_exists( '_wpaicg_meta_description', $_POST ) ) {
                        $this->wpaicg_save_description($wpaicg_post_id,sanitize_text_field($_POST['_wpaicg_meta_description']));
                    }
                    $this->wpaicg_post_image($wpaicg_post_id,$wpaicg_title);
                    $wpaicg_post = get_post($wpaicg_post_id);
                    $wpaicg_content = str_replace("__WPAICG_IMAGE__", '', $wpaicg_post->post_content);
                    wp_update_post(array(
                        'ID' => $wpaicg_post_id,
                        'post_content' => $wpaicg_content
                    ));
                    $wpaicg_result['status'] = 'success';
                    $wpaicg_result['id'] = $wpaicg_post_id;
                    if(isset($_REQUEST['save_source']) && $_REQUEST['save_source'] == 'promptbase'){

                    }
                    else {
                        /*Save Single Content Log*/
                        $wpaicg_duration = isset($_REQUEST['duration']) && !empty($_REQUEST['duration']) ? sanitize_text_field($_REQUEST['duration']) : 0;
                        $wpaicg_usage_token = isset($_REQUEST['usage_token']) && !empty($_REQUEST['usage_token']) ? sanitize_text_field($_REQUEST['usage_token']) : 0;
                        $wpaicg_word_count = isset($_REQUEST['word_count']) && !empty($_REQUEST['word_count']) ? sanitize_text_field($_REQUEST['word_count']) : 0;
                        $wpaicg_log_id = wp_insert_post(array(
                            'post_title' => 'WPAICGLOG:' . $wpaicg_title,
                            'post_type' => 'wpaicg_slog',
                            'post_status' => 'publish'
                        ));
                        $wpaicg_ai_model = get_option('wpaicg_ai_model', 'text-davinci-003');
                        if (isset($_REQUEST['model']) && !empty($_REQUEST['model'])) {
                            $wpaicg_ai_model = sanitize_text_field($_REQUEST['model']);
                        }
                        $source_log = 'writer';
                        if (isset($_REQUEST['source_log']) && !empty($_REQUEST['source_log'])) {
                            $source_log = sanitize_text_field($_REQUEST['source_log']);
                        }
                        add_post_meta($wpaicg_log_id, 'wpaicg_source_log', $source_log);
                        add_post_meta($wpaicg_log_id, 'wpaicg_ai_model', $wpaicg_ai_model);
                        add_post_meta($wpaicg_log_id, 'wpaicg_duration', $wpaicg_duration);
                        add_post_meta($wpaicg_log_id, 'wpaicg_usage_token', $wpaicg_usage_token);
                        add_post_meta($wpaicg_log_id, 'wpaicg_word_count', $wpaicg_word_count);
                        add_post_meta($wpaicg_log_id, 'wpaicg_post_id', $wpaicg_post_id);
                    }
                }

            }

            wp_send_json( $wpaicg_result );
        }
    }
    WPAICG_Content::get_instance();
}
