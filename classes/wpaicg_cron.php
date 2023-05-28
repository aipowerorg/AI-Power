<?php

namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Cron')) {
    class WPAICG_Cron
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
            add_action('init',[$this,'wpaicg_cron_job'],1);
        }

        public function wpaicg_cron_job()
        {
            if(isset($_SERVER['argv']) && is_array($_SERVER['argv']) && count($_SERVER['argv'])){
                foreach( $_SERVER['argv'] as $arg ) {
                    $e = explode( '=', $arg );
                    if($e[0] == 'wpaicg_cron') {
                        if (count($e) == 2)
                            $_GET[$e[0]] = sanitize_text_field($e[1]);
                        else
                            $_GET[$e[0]] = 0;
                    }
                }
            }
            if(isset($_GET['wpaicg_cron']) && sanitize_text_field($_GET['wpaicg_cron']) == 'yes'){
                $wpaicg_running = WPAICG_PLUGIN_DIR.'/wpaicg_running.txt';
                if(!file_exists($wpaicg_running)) {
                    $wpaicg_file = fopen($wpaicg_running, "a") or die("Unable to open file!");
                    $txt = 'running';
                    fwrite($wpaicg_file, $txt);
                    fclose($wpaicg_file);
                    try {
                        $_SERVER["REQUEST_METHOD"] = 'GET';
                        chmod($wpaicg_running,0755);
                        $wpaicg_custom_prompt_enable = get_option('wpaicg_custom_prompt_enable',false);
                        if($wpaicg_custom_prompt_enable){
                            $wpaicg_custom_prompt = WPAICG_Custom_Prompt::get_instance();
                            $wpaicg_custom_prompt->generator();
                        }
                        else{
                            $wpaicg_generator_content = WPAICG_Content::get_instance();
                            $wpaicg_generator_content->wpaicg_bulk_generator();
                        }
                    }
                    catch (\Exception $exception){
                        $wpaicg_error = WPAICG_PLUGIN_DIR.'wpaicg_error.txt';
                        $wpaicg_file = fopen($wpaicg_error, "a") or die("Unable to open file!");
                        $txt = $exception->getMessage();
                        fwrite($wpaicg_file, $txt);
                        fclose($wpaicg_file);

                    }
                    @unlink($wpaicg_running);
                }
                exit;
            }
        }
    }
    WPAICG_Cron::get_instance();
}
