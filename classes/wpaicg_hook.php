<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Hook')) {
    class WPAICG_Hook
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
            add_action( 'admin_menu', array( $this, 'wpaicg_change_menu_name' ) );
            add_action( 'admin_head', array( $this, 'wpaicg_hooks_admin_header' ) );
            add_action('wp_footer',[$this,'wpaicg_footer'],1);
            add_action('wp_head',[$this,'wpaicg_head_seo'],1);
            add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' );
            add_action('admin_footer',array($this,'wpaicg_admin_footer'));
            add_editor_style(WPAICG_PLUGIN_URL.'admin/css/editor.css');
            add_action( 'admin_enqueue_scripts', [$this,'wpaicg_enqueue_scripts'] );
            add_action( 'wp_enqueue_scripts', [$this,'wp_enqueue_scripts_hook'] );
        }


        public function wpaicg_enqueue_scripts()
        {
            wp_enqueue_script('wpaicg-jquery-datepicker',WPAICG_PLUGIN_URL.'admin/js/jquery.datetimepicker.full.min.js',array(),null);
            wp_enqueue_script('wpaicg-init',WPAICG_PLUGIN_URL.'public/js/wpaicg-init.js',array(),null,true);
            wp_localize_script( 'wpaicg-init', 'wpaicgParams', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'search_nonce' => wp_create_nonce( 'wpaicg-chatbox' ),
                'logged_in' => is_user_logged_in() ? 1 : 0,
                'languages' => array(
                    'source' => esc_html__('Sources','gpt3-ai-content-generator'),
                    'no_result' => esc_html__('No result found','gpt3-ai-content-generator'),
                    'wrong' => esc_html__('Something went wrong','gpt3-ai-content-generator'),
                    'prompt_strength' => sprintf(esc_html__('Please enter a valid prompt strength value between %d and %d.', 'gpt3-ai-content-generator'), 0, 1),
                    'num_inference_steps' => sprintf(esc_html__('Please enter a valid number of inference steps value between %d and %d.', 'gpt3-ai-content-generator'), 1, 500),
                    'guidance_scale' => sprintf(esc_html__('Please enter a valid guidance scale value between %d and %d.', 'gpt3-ai-content-generator'), 1, 20),
                    'error_image' => esc_html__('Please select least one image for generate', 'gpt3-ai-content-generator'),
                    'save_image_success' => esc_html__('Save images to media successfully','gpt3-ai-content-generator'),
                    'select_all' => esc_html__('Select All', 'gpt3-ai-content-generator'),
                    'unselect' => esc_html__('Unselect', 'gpt3-ai-content-generator'),
                    'select_save_error' => esc_html__('Please select least one image to save', 'gpt3-ai-content-generator'),
                    'alternative' => esc_html__('Alternative Text','gpt3-ai-content-generator'),
                    'title' => esc_html__('Title','gpt3-ai-content-generator'),
                    'caption' => esc_html__('Caption','gpt3-ai-content-generator'),
                    'description' => esc_html__('Description','gpt3-ai-content-generator'),
                    'edit_image' => esc_html__('Edit Image','gpt3-ai-content-generator'),
                    'save' => esc_html__('Save','gpt3-ai-content-generator'),
                    'removed_pdf' => esc_html__('Your pdf session is cleared','gpt3-ai-content-generator')
                )
            ));
            wp_enqueue_script('wpaicg-chat-shortcode',WPAICG_PLUGIN_URL.'public/js/wpaicg-chat.js',array(),null,true);
            wp_enqueue_style('wpaicg-extra-css',WPAICG_PLUGIN_URL.'admin/css/wpaicg_extra.css',array(),null);
            wp_enqueue_style('wpaicg-jquery-datepicker-css',WPAICG_PLUGIN_URL.'admin/css/jquery.datetimepicker.min.css',array(),null);
            wp_enqueue_style('wpaicg-rtl-css',WPAICG_PLUGIN_URL.'public/css/wpaicg-rtl.css',array(),null);
        }

        public function wpaicg_admin_footer()
        {
            ?>
            <div class="wpaicg-overlay" style="display: none">
                <div class="wpaicg_modal">
                    <div class="wpaicg_modal_head">
                        <span class="wpaicg_modal_title"><?php echo esc_html__('GPT3 Modal','gpt3-ai-content-generator')?></span>
                        <span class="wpaicg_modal_close">&times;</span>
                    </div>
                    <div class="wpaicg_modal_content"></div>
                </div>
            </div>
            <div class="wpaicg-overlay-second" style="display: none">
                <div class="wpaicg_modal_second">
                    <div class="wpaicg_modal_head_second">
                        <span class="wpaicg_modal_title_second"><?php echo esc_html__('GPT3 Modal','gpt3-ai-content-generator')?></span>
                        <span class="wpaicg_modal_close_second">&times;</span>
                    </div>
                    <div class="wpaicg_modal_content_second"></div>
                </div>
            </div>
            <div class="wpcgai_lds-ellipsis" style="display: none">
                <div class="wpaicg-generating-title"><?php echo esc_html__('Generating content..','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-generating-process"></div>
                <div class="wpaicg-timer"></div>
            </div>
            <script>
                let wpaicg_ajax_url = '<?php echo admin_url('admin-ajax.php')?>';
            </script>
            <?php
        }

        public function wp_enqueue_scripts_hook()
        {
            wp_enqueue_script('wpaicg-init',WPAICG_PLUGIN_URL.'public/js/wpaicg-init.js',array(),null,true);
            wp_localize_script( 'wpaicg-init', 'wpaicgParams', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'search_nonce' => wp_create_nonce( 'wpaicg-chatbox' ),
                'logged_in' => is_user_logged_in() ? 1 : 0,
                'languages' => array(
                    'source' => esc_html__('Sources','gpt3-ai-content-generator'),
                    'no_result' => esc_html__('No result found','gpt3-ai-content-generator'),
                    'wrong' => esc_html__('Something went wrong','gpt3-ai-content-generator'),
                    'prompt_strength' => sprintf(esc_html__('Please enter a valid prompt strength value between %d and %d.', 'gpt3-ai-content-generator'), 0, 1),
                    'num_inference_steps' => sprintf(esc_html__('Please enter a valid number of inference steps value between %d and %d.', 'gpt3-ai-content-generator'), 1, 500),
                    'guidance_scale' => sprintf(esc_html__('Please enter a valid guidance scale value between %d and %d.', 'gpt3-ai-content-generator'), 1, 20),
                    'error_image' => esc_html__('Please select least one image for generate', 'gpt3-ai-content-generator'),
                    'save_image_success' => esc_html__('Save images to media successfully','gpt3-ai-content-generator'),
                    'select_all' => esc_html__('Select All', 'gpt3-ai-content-generator'),
                    'unselect' => esc_html__('Unselect', 'gpt3-ai-content-generator'),
                    'select_save_error' => esc_html__('Please select least one image to save', 'gpt3-ai-content-generator'),
                    'alternative' => esc_html__('Alternative Text','gpt3-ai-content-generator'),
                    'title' => esc_html__('Title','gpt3-ai-content-generator'),
                    'edit_image' => esc_html__('Edit Image','gpt3-ai-content-generator'),
                    'caption' => esc_html__('Caption','gpt3-ai-content-generator'),
                    'description' => esc_html__('Description','gpt3-ai-content-generator'),
                    'save' => esc_html__('Save','gpt3-ai-content-generator'),
                    'removed_pdf' => esc_html__('Your pdf session is cleared','gpt3-ai-content-generator')
                )
            ));
            wp_enqueue_script('wpaicg-chat-script',WPAICG_PLUGIN_URL.'public/js/wpaicg-chat.js',null,null,true);
        }

        public function wpaicg_head_seo()
        {
            global $wpdb;
            $wpaicg_chat_widget = get_option('wpaicg_chat_widget',[]);
            /*Check Custom Widget For Page Post*/
            $current_context_ID = get_the_ID();
            $wpaicg_bot_content = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->postmeta." WHERE meta_key=%s",'wpaicg_widget_page_'.$current_context_ID));
            if($wpaicg_bot_content && isset($wpaicg_bot_content->post_id)){
                $wpaicg_bot = get_post($wpaicg_bot_content->post_id);
                if($wpaicg_bot) {
                    if(strpos($wpaicg_bot->post_content,'\"') !== false) {
                        $wpaicg_bot->post_content = str_replace('\"', '&quot;', $wpaicg_bot->post_content);
                    }
                    if(strpos($wpaicg_bot->post_content,"\'") !== false) {
                        $wpaicg_bot->post_content = str_replace('\\', '', $wpaicg_bot->post_content);
                    }
                    $wpaicg_chat_widget = json_decode($wpaicg_bot->post_content, true);
                }
            }
            /*End check*/
            $wpaicg_chat_icon = isset($wpaicg_chat_widget['icon']) && !empty($wpaicg_chat_widget['icon']) ? $wpaicg_chat_widget['icon'] : 'default';
            $wpaicg_chat_icon_url = isset($wpaicg_chat_widget['icon_url']) && !empty($wpaicg_chat_widget['icon_url']) ? $wpaicg_chat_widget['icon_url'] : '';
            $wpaicg_chat_status = isset($wpaicg_chat_widget['status']) && !empty($wpaicg_chat_widget['status']) ? $wpaicg_chat_widget['status'] : '';
            $wpaicg_chat_fontsize = isset($wpaicg_chat_widget['fontsize']) && !empty($wpaicg_chat_widget['fontsize']) ? $wpaicg_chat_widget['fontsize'] : '13';
            $wpaicg_chat_fontcolor = isset($wpaicg_chat_widget['fontcolor']) && !empty($wpaicg_chat_widget['fontcolor']) ? $wpaicg_chat_widget['fontcolor'] : '#90EE90';
            $wpaicg_chat_bgcolor = isset($wpaicg_chat_widget['bgcolor']) && !empty($wpaicg_chat_widget['bgcolor']) ? $wpaicg_chat_widget['bgcolor'] : '#222222';
            $wpaicg_bg_text_field = isset($wpaicg_chat_widget['bg_text_field']) && !empty($wpaicg_chat_widget['bg_text_field']) ? $wpaicg_chat_widget['bg_text_field'] : '#fff';
            $wpaicg_send_color = isset($wpaicg_chat_widget['send_color']) && !empty($wpaicg_chat_widget['send_color']) ? $wpaicg_chat_widget['send_color'] : '#fff';
            $wpaicg_border_text_field = isset($wpaicg_chat_widget['border_text_field']) && !empty($wpaicg_chat_widget['border_text_field']) ? $wpaicg_chat_widget['border_text_field'] : '#ccc';
            $wpaicg_chat_width = isset($wpaicg_chat_widget['width']) && !empty($wpaicg_chat_widget['width']) ? $wpaicg_chat_widget['width'] : '350';
            $wpaicg_chat_height = isset($wpaicg_chat_widget['height']) && !empty($wpaicg_chat_widget['height']) ? $wpaicg_chat_widget['height'] : '400';
            $wpaicg_chat_position = isset($wpaicg_chat_widget['position']) && !empty($wpaicg_chat_widget['position']) ? $wpaicg_chat_widget['position'] : 'left';
            $wpaicg_chat_tone = isset($wpaicg_chat_widget['tone']) && !empty($wpaicg_chat_widget['tone']) ? $wpaicg_chat_widget['tone'] : 'friendly';
            $wpaicg_chat_proffesion = isset($wpaicg_chat_widget['proffesion']) && !empty($wpaicg_chat_widget['proffesion']) ? $wpaicg_chat_widget['proffesion'] : 'none';
            $wpaicg_chat_remember_conversation = isset($wpaicg_chat_widget['remember_conversation']) && !empty($wpaicg_chat_widget['remember_conversation']) ? $wpaicg_chat_widget['remember_conversation'] : 'yes';
            $wpaicg_chat_content_aware = isset($wpaicg_chat_widget['content_aware']) && !empty($wpaicg_chat_widget['content_aware']) ? $wpaicg_chat_widget['content_aware'] : 'yes';
            $wpaicg_include_footer = (isset($wpaicg_chat_widget['footer_text']) && !empty($wpaicg_chat_widget['footer_text'])) ? 5 : 0;
            ?>
            <style>
                .wpaicg_toc h2{
                    margin-bottom: 20px;
                }
                .wpaicg_toc{
                    list-style: none;
                    margin: 0 0 30px 0!important;
                    padding: 0!important;
                }
                .wpaicg_toc li{}
                .wpaicg_toc li ul{
                    list-style: decimal;
                }
                .wpaicg_toc a{}
                .wpaicg_chat_widget{
                    position: fixed;
                }
                .wpaicg_widget_left{
                    bottom: 15px;
                    left: 15px;
                }
                .wpaicg_widget_right{
                    bottom: 15px;
                    right: 15px;
                }
                .wpaicg_widget_right .wpaicg_chat_widget_content{
                    right: 0;
                }
                .wpaicg_widget_left .wpaicg_chat_widget_content{
                    left: 0;
                }
                .wpaicg_chat_widget_content .wpaicg-chatbox{
                    height: 100%;
                    background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>;
                    border-radius: 5px;
                }
                .wpaicg_widget_open .wpaicg_chat_widget_content{
                    height: <?php echo esc_html($wpaicg_chat_height)?>px;
                }
                .wpaicg_chat_widget_content{
                    position: absolute;
                    bottom: calc(100% + 15px);
                    width: <?php echo esc_html($wpaicg_chat_width)?>px;
                    overflow: hidden;
                }
                .wpaicg_widget_open .wpaicg_chat_widget_content{
                    overflow: unset;
                }
                .wpaicg_widget_open .wpaicg_chat_widget_content .wpaicg-chatbox{
                    top: 0;
                }
                .wpaicg_chat_widget_content .wpaicg-chatbox{
                    position: absolute;
                    top: 100%;
                    left: 0;
                    width: <?php echo esc_html($wpaicg_chat_width)?>px;
                    height: <?php echo esc_html($wpaicg_chat_height)?>px;
                    transition: top 300ms cubic-bezier(0.17, 0.04, 0.03, 0.94);
                }
                .wpaicg_chat_widget_content .wpaicg-chatbox-content{
                }
                .wpaicg_chat_widget_content .wpaicg-chatbox-content ul{
                    box-sizing: border-box;
                    background: <?php echo esc_html($wpaicg_chat_bgcolor)?>;
                }
                .wpaicg_chat_widget_content .wpaicg-chatbox-content ul li{
                    color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
                    font-size: <?php echo esc_html($wpaicg_chat_fontsize)?>px;
                }
                .wpaicg_chat_widget_content .wpaicg-bot-thinking{
                    color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
                }
                .wpaicg_chat_widget_content .wpaicg-chatbox-type{
                <?php
                if($wpaicg_include_footer):
                ?>
                    padding: 5px 5px 0 5px;
                <?php
                endif;
                ?>
                    border-top: 0;
                    background: rgb(0 0 0 / 19%);
                }
                .wpaicg_chat_widget_content .wpaicg-chat-message{
                    color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
                }
                .wpaicg_chat_widget_content textarea.wpaicg-chatbox-typing{
                    background-color: <?php echo esc_html($wpaicg_bg_text_field)?>;
                    border-color: <?php echo esc_html($wpaicg_border_text_field)?>;
                }
                .wpaicg_chat_widget_content .wpaicg-chatbox-send{
                    color: <?php echo esc_html($wpaicg_send_color)?>;
                }
                .wpaicg-chatbox-footer{
                    height: 18px;
                    font-size: 11px;
                    padding: 0 5px;
                    color: <?php echo esc_html($wpaicg_send_color)?>;
                    background: rgb(0 0 0 / 19%);
                    margin-top:2px;
                    margin-bottom: 2px;
                }
                .wpaicg_chat_widget_content textarea.wpaicg-chatbox-typing:focus{
                    outline: none;
                }
                .wpaicg_chat_widget .wpaicg_toggle{
                    cursor: pointer;
                }
                .wpaicg_chat_widget .wpaicg_toggle img{
                    width: 75px;
                    height: 75px;
                }
                .wpaicg-chat-shortcode-type,.wpaicg-chatbox-type{
                    position: relative;
                }
                .wpaicg-mic-icon{
                    cursor: pointer;
                }
                .wpaicg-mic-icon svg{
                    width: 16px;
                    height: 16px;
                    fill: currentColor;
                }
                .wpaicg-pdf-icon svg{
                    width: 22px;
                    height: 22px;
                    fill: currentColor;
                }
                .wpaicg_chat_additions span{
                    cursor: pointer;
                    margin-right: 2px;
                }
                .wpaicg_chat_additions span:last-of-type{
                    margin-right: 0;
                }
                .wpaicg-pdf-loading{
                    width: 18px;
                    height: 18px;
                    border: 2px solid #FFF;
                    border-bottom-color: transparent;
                    border-radius: 50%;
                    display: inline-block;
                    box-sizing: border-box;
                    animation: wpaicg_rotation 1s linear infinite;
                }
                @keyframes wpaicg_rotation {
                    0% {
                        transform: rotate(0deg);
                    }
                    100% {
                        transform: rotate(360deg);
                    }
                }
                .wpaicg-chat-message code{
                    padding: 3px 5px 2px;
                    background: rgb(0 0 0 / 20%);
                    font-size: 13px;
                    font-family: Consolas,Monaco,monospace;
                    direction: ltr;
                    unicode-bidi: embed;
                    display: block;
                    margin: 5px 0px;
                    border-radius: 4px;
                    white-space: pre-wrap;
                }
                textarea.wpaicg-chat-shortcode-typing,textarea.wpaicg-chatbox-typing{
                    height: 30px;
                }
                .wpaicg_chat_widget_content .wpaicg-chatbox-content,.wpaicg-chat-shortcode-content{
                    overflow: hidden;
                }
                .wpaicg_chatbox_line{
                    overflow: hidden;
                    text-align: center;
                    display: block!important;
                    font-size: 12px;
                }
                .wpaicg_chatbox_line:after,.wpaicg_chatbox_line:before{
                    background-color: rgb(255 255 255 / 26%);
                    content: "";
                    display: inline-block;
                    height: 1px;
                    position: relative;
                    vertical-align: middle;
                    width: 50%;
                }
                .wpaicg_chatbox_line:before {
                    right: 0.5em;
                    margin-left: -50%;
                }

                .wpaicg_chatbox_line:after {
                    left: 0.5em;
                    margin-right: -50%;
                }
                .wpaicg-chat-shortcode-typing::-webkit-scrollbar,.wpaicg-chatbox-typing::-webkit-scrollbar{
                    width: 5px
                }
                .wpaicg-chat-shortcode-typing::-webkit-scrollbar-track,.wpaicg-chatbox-typing::-webkit-scrollbar-track{
                    -webkit-box-shadow:inset 0 0 6px rgba(0, 0, 0, 0.15);border-radius:5px;
                }
                .wpaicg-chat-shortcode-typing::-webkit-scrollbar-thumb,.wpaicg-chatbox-typing::-webkit-scrollbar-thumb{
                    border-radius:5px;
                    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.75);
                }
            </style>
            <script>
                var wpaicg_ajax_url = '<?php echo admin_url('admin-ajax.php')?>';
                var wpaicgUserLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false';?>;
            </script>
            <link href="<?php echo esc_html(WPAICG_PLUGIN_URL)?>public/css/wpaicg-rtl.css" type="text/css" rel="stylesheet" />
            <?php
            if(is_single()){
                $wpaicg_meta_description = get_post_meta(get_the_ID(),'_wpaicg_meta_description',true);
                $_wpaicg_seo_meta_tag = get_option('_wpaicg_seo_meta_tag',false);
                $wpaicg_seo_option = false;
                $wpaicg_seo_plugin = wpaicg_util_core()->seo_plugin_activated();
                if($wpaicg_seo_plugin) {
                    $wpaicg_seo_option = get_option($wpaicg_seo_plugin, false);
                }
                if(!empty($wpaicg_meta_description) && $_wpaicg_seo_meta_tag && !$wpaicg_seo_option){
                    ?>
                    <!--- This meta description generated by AI Power Plugin --->
                    <meta name="description" content="<?php echo esc_html($wpaicg_meta_description)?>">
                    <meta name="og:description" content="<?php echo esc_html($wpaicg_meta_description)?>">
                    <?php
                }
            }
        }

        public function wpaicg_footer()
        {
            include WPAICG_PLUGIN_DIR.'admin/extra/wpaicg_chat_widget.php';
            ?>
            <script>
                var wpaicgUserLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false';?>;
            </script>
            <?php
        }

        public function wpaicg_hooks_admin_header()
        {
            ?>
            <style>
                .wp-block .wpaicg_toc h2{
                    margin-bottom: 20px;
                }
                .wp-block .wpaicg_toc{
                    list-style: none;
                    margin: 0 0 30px 0!important;
                    padding: 0!important;
                }
                .wp-block .wpaicg_toc li{}
                .wp-block .wpaicg_toc li ul{
                    list-style: decimal;
                }
                .wp-block .wpaicg_toc a{}
                .wpaicg-chat-shortcode-type,.wpaicg-chatbox-type{
                    position: relative;
                }
                .wpaicg-mic-icon{
                    cursor: pointer;
                }
                .wpaicg-mic-icon svg{
                    width: 16px;
                    height: 16px;
                    fill: currentColor;
                }
                .wpaicg-pdf-icon svg{
                    width: 22px;
                    height: 22px;
                    fill: currentColor;
                }
                .wpaicg-pdf-loading{
                    width: 18px;
                    height: 18px;
                    border: 2px solid #FFF;
                    border-bottom-color: transparent;
                    border-radius: 50%;
                    display: inline-block;
                    box-sizing: border-box;
                    animation: wpaicg_rotation 1s linear infinite;
                }
                @keyframes wpaicg_rotation {
                    0% {
                        transform: rotate(0deg);
                    }
                    100% {
                        transform: rotate(360deg);
                    }
                }
                .wpaicg_chat_additions span{
                    cursor: pointer;
                    margin-right: 2px;
                }
                .wpaicg_chat_additions span:last-of-type{
                    margin-right: 0;
                }
                .wp-picker-container{
                    z-index: 99999;
                }

                .gpt-ai-power_page_wpaicg_help .ui-state-default,
                .gpt-ai-power_page_wpaicg_help .ui-widget-content .ui-state-default,
                .gpt-ai-power_page_wpaicg_help .ui-widget-header .ui-state-default,
                .gpt-ai-power_page_wpaicg_help .ui-button,
                html .gpt-ai-power_page_wpaicg_help  .ui-button.ui-state-disabled:hover,
                html .gpt-ai-power_page_wpaicg_help  .ui-button.ui-state-disabled:active,
                .wpcgai_container .ui-state-default,
                .wpcgai_container .ui-widget-content .ui-state-default,
                .wpcgai_container .ui-widget-header .ui-state-default,
                .wpcgai_container .ui-button,
                html .wpcgai_container .ui-button.ui-state-disabled:hover,
                html .wpcgai_container .ui-button.ui-state-disabled:active
                {
                    border: 1px solid #c5c5c5;
                    background: #f6f6f6;
                    font-weight: normal;
                    color: #454545;
                    border-bottom-width: 0;
                }
                .gpt-ai-power_page_wpaicg_help .ui-state-hover,
                .gpt-ai-power_page_wpaicg_help .ui-widget-content .ui-state-hover,
                .gpt-ai-power_page_wpaicg_help .ui-widget-header .ui-state-hover,
                .gpt-ai-power_page_wpaicg_help .ui-state-focus,
                .gpt-ai-power_page_wpaicg_help .ui-widget-content .ui-state-focus,
                .gpt-ai-power_page_wpaicg_help .ui-widget-header .ui-state-focus,
                .gpt-ai-power_page_wpaicg_help .ui-button:hover, .ui-button:focus,
                .wpcgai_container .ui-state-hover,
                .wpcgai_container .ui-widget-content .ui-state-hover,
                .wpcgai_container .ui-widget-header .ui-state-hover,
                .wpcgai_container .ui-state-focus,
                .wpcgai_container .ui-widget-content .ui-state-focus,
                .wpcgai_container .ui-widget-header .ui-state-focus,
                .wpcgai_container .ui-button:hover, .ui-button:focus
                {
                    border: 1px solid #cccccc;
                    background: #ededed;
                    font-weight: normal;
                    color: #2b2b2b;
                }

                .gpt-ai-power_page_wpaicg_help .ui-state-active,
                .gpt-ai-power_page_wpaicg_help .ui-widget-content .ui-state-active,
                .gpt-ai-power_page_wpaicg_help .ui-widget-header .ui-state-active,
                .gpt-ai-power_page_wpaicg_help a.ui-button:active,
                .gpt-ai-power_page_wpaicg_help .ui-button:active,
                .gpt-ai-power_page_wpaicg_help .ui-button.ui-state-active:hover
                .wpcgai_container .ui-state-active,
                .wpcgai_container .ui-widget-content .ui-state-active,
                .wpcgai_container .ui-widget-header .ui-state-active,
                .wpcgai_container a.ui-button:active,
                .wpcgai_container .ui-button:active,
                .wpcgai_container .ui-button.ui-state-active:hover{
                    border: 1px solid #003eff;
                    background: #007fff;
                    font-weight: normal;
                    color: #ffffff;
                }
                .wpaicg-overlay-second {
                    position: fixed;
                    width: 100%;
                    height: 100%;
                    z-index: 99999;
                    background: rgb(0 0 0 / 20%);
                    top: 0;
                    direction: ltr;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                .wpaicg_modal_second {
                    width: 400px;
                    min-height: 100px;
                    background: #fff;
                    border-radius: 5px;
                }
                .wpaicg_modal_head_second {
                    min-height: 30px;
                    border-bottom: 1px solid #ccc;
                    display: flex;
                    align-items: center;
                    padding: 6px 12px;
                    position: relative;
                }
                .wpaicg_modal_content_second {
                    max-height: calc(100% - 103px);
                    overflow-y: auto;
                }
                .wpaicg_modal_title_second {
                    font-size: 18px;
                }
                .wpaicg_modal_close_second {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    font-size: 30px;
                    font-weight: bold;
                    cursor: pointer;
                }
                .wpaicg-chat-message code{
                    padding: 3px 5px 2px;
                    background: rgb(0 0 0 / 20%);
                    font-size: 13px;
                    font-family: Consolas,Monaco,monospace;
                    direction: ltr;
                    unicode-bidi: embed;
                    display: block;
                    margin: 5px 0px;
                    border-radius: 4px;
                    white-space: pre-wrap;
                }
                .wpaicg_chat_widget_content .wpaicg-chatbox-content,.wpaicg-chat-shortcode-content{
                    overflow: hidden;
                }
                .wpaicg_chatbox_line{
                    overflow: hidden;
                    text-align: center;
                    display: block!important;
                    font-size: 12px;
                }
                .wpaicg_chatbox_line:after,.wpaicg_chatbox_line:before{
                    background-color: rgb(255 255 255 / 26%);
                    content: "";
                    display: inline-block;
                    height: 1px;
                    position: relative;
                    vertical-align: middle;
                    width: 50%;
                }
                .wpaicg_chatbox_line:before {
                    right: 0.5em;
                    margin-left: -50%;
                }

                .wpaicg_chatbox_line:after {
                    left: 0.5em;
                    margin-right: -50%;
                }
                .wpaicg-chat-shortcode-typing::-webkit-scrollbar,.wpaicg-chatbox-typing::-webkit-scrollbar{
                    width: 5px
                }
                .wpaicg-chat-shortcode-typing::-webkit-scrollbar-track,.wpaicg-chatbox-typing::-webkit-scrollbar-track{
                    -webkit-box-shadow:inset 0 0 6px rgba(0, 0, 0, 0.15);border-radius:5px;
                }
                .wpaicg-chat-shortcode-typing::-webkit-scrollbar-thumb,.wpaicg-chatbox-typing::-webkit-scrollbar-thumb{
                    border-radius:5px;
                    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.75);
                }
            </style>
            <?php
        }

        public function wpaicg_change_menu_name()
        {
            global  $menu ;
            global  $submenu ;
            if(isset($submenu['wpaicg'])) {
                if ($submenu['wpaicg'][0][2] == 'wpaicg') {
                    $submenu['wpaicg'][0][0] = 'Settings';
                }
            }
        }
    }
    WPAICG_Hook::get_instance();
}
