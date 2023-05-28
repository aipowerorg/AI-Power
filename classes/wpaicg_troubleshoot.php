<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_TroubleShoot')) {
    class WPAICG_TroubleShoot
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
            add_action('wp_ajax_wpaicg_troubleshoot_add_vector',[$this,'wpaicg_troubleshoot_add_vector']);
            add_action('wp_ajax_wpaicg_troubleshoot_delete_vector',[$this,'wpaicg_troubleshoot_delete_vector']);
            add_action('wp_ajax_wpaicg_troubleshoot_search',[$this,'wpaicg_troubleshoot_search']);
            add_action('wp_ajax_wpaicg_troubleshoot_save',[$this,'wpaicg_troubleshoot_save']);
        }

        public function wpaicg_troubleshoot_save()
        {
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                die(WPAICG_NONCE_ERROR);
            }
            $key = sanitize_text_field($_REQUEST['key']);
            $value = sanitize_text_field($_REQUEST['value']);
            update_option($key, $value);
        }

        public function wpaicg_troubleshoot_add_vector()
        {
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                die(WPAICG_NONCE_ERROR);
            }
            $headers = array(
                'Api-Key' => sanitize_text_field($_REQUEST['api_key']),
                'Content-Type' => 'application/json'
            );
            $vectors = str_replace("\\",'',sanitize_text_field($_REQUEST['data']));
            $response = wp_remote_post(sanitize_text_field($_REQUEST['environment']),array(
                'headers' => $headers,
                'body' => $vectors
            ));
            if(is_wp_error($response)){
                die($response->get_error_message());
            }
            else{
                echo wp_remote_retrieve_body($response);
                die();
            }
        }

        public function wpaicg_troubleshoot_search()
        {
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                die(WPAICG_NONCE_ERROR);
            }
            $headers = array(
                'Api-Key' => sanitize_text_field($_REQUEST['api_key']),
                'Content-Type' => 'application/json'
            );
            $data = str_replace("\\",'',sanitize_text_field($_REQUEST['data']));

            $response = wp_remote_post(sanitize_text_field($_REQUEST['environment']),array(
                'headers' => $headers,
                'body' => $data
            ));
            if(is_wp_error($response)){
                die($response->get_error_message());
            }
            else{
                echo wp_remote_retrieve_body($response);
                die();
            }
        }

        public function wpaicg_troubleshoot_delete_vector()
        {
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                die(WPAICG_NONCE_ERROR);
            }
            $headers = array(
                'Api-Key' => sanitize_text_field($_REQUEST['api_key']),
                'Content-Type' => 'application/json'
            );
            $data = str_replace("\\",'',sanitize_text_field($_REQUEST['data']));
            $response = wp_remote_post(sanitize_text_field($_REQUEST['environment']),array(
                'headers' => $headers,
                'body' => $data
            ));
            if(is_wp_error($response)){
                die($response->get_error_message());
            }
            else{
                echo wp_remote_retrieve_body($response);
                die();
            }
        }
    }
    WPAICG_TroubleShoot::get_instance();
}
