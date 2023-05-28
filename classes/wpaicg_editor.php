<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Editor')) {
    class WPAICG_Editor
    {
        private static $instance = null;
        public $wpaicg_edit_default_menus = array(
            array('name' => 'Write a paragraph about this', 'prompt' => ' Write a paragraph about this: [text]'),
            array('name' => 'Summarize', 'prompt' => 'Summarize this: [text]'),
            array('name' => 'Expand', 'prompt' => 'Expand this: [text]'),
            array('name' => 'Rewrite', 'prompt' => 'Rewrite this: [text]'),
            array('name' => 'Generate ideas about this', 'prompt' => 'Generate ideas about this: [text]'),
            array('name' => 'Make a bulleted list', 'prompt' => 'Make a bulleted list: [text]'),
            array('name' => 'Paraphrase', 'prompt' => 'Paraphrase this: [text]'),
            array('name' => 'Generate a call to action', 'prompt' => 'Generate a call to action about this: [text]'),
            array('name' => 'Correct grammar', 'prompt' => 'Correct grammar in this: [text]'),
            array('name' => 'Generate a question', 'prompt' => 'Generate a question about this: [text]'),
            array('name' => 'Suggest a title', 'prompt' => 'Suggest a title for this: [text]'),
            array('name' => 'Convert to passive voice', 'prompt' => 'Convert this to passive voice: [text]'),
            array('name' => 'Convert to active voice', 'prompt' => 'Convert this to active voice: [text]'),
            array('name' => 'Write a conclusion', 'prompt' => 'Write a conclusion for this: [text]'),
            array('name' => 'Provide a counterargument', 'prompt' => 'Provide a counterargument for this: [text]'),
            array('name' => 'Generate a quote', 'prompt' => 'Generate a quote related to this: [text]'),
            array('name' => 'Translate to Spanish', 'prompt' => 'Translate this to Spanish: [text]')
        );

        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action( 'enqueue_block_editor_assets', array($this,'wpaicg_block_editor'), 9 );
            add_action('admin_head',array($this,'wpaicg_ai_buttons'));
            add_action('wp_ajax_wpaicg_editor_prompt', array($this,'wpaicg_editor_prompt'));
        }

        public function wpaicg_block_editor()
        {
            if(is_admin() && current_user_can('wpaicg_ai_assistant')) {
                wp_enqueue_script(
                    'wpaicg-gutenberg-custom-button',
                    WPAICG_PLUGIN_URL . 'admin/js/wpaicg_gutenberg.js',
                    ['wp-editor', 'wp-i18n', 'wp-element', 'wp-compose', 'wp-components'],
                    '1.0.0',
                    true
                );
                $wpaicg_editor_button_menus = get_option('wpaicg_editor_button_menus', []);
                if (!is_array($wpaicg_editor_button_menus) || count($wpaicg_editor_button_menus) == 0) {
                    $wpaicg_editor_button_menus = $this->wpaicg_edit_default_menus;
                }
                wp_localize_script('wpaicg-gutenberg-custom-button', 'wpaicg_gutenberg_editor', array(
                    'plugin_url' => WPAICG_PLUGIN_URL,
                    'editor_ajax_url' => admin_url('admin-ajax.php'),
                    'editor_menus' => $wpaicg_editor_button_menus,
                    'change_action' => get_option('wpaicg_editor_change_action', 'below')
                ));
            }
        }

        public function wpaicg_ai_buttons()
        {
            if(is_admin() && current_user_can('wpaicg_ai_assistant')) {
                ?>
                <script>
                    var wpaicg_editor_wp_nonce = '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>';
                </script>
                <?php
                if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
                    return;
                }
                if ( 'true' == get_user_option( 'rich_editing' ) ) {
                    $wpaicg_editor_button_menus = get_option('wpaicg_editor_button_menus', []);
                    if(!is_array($wpaicg_editor_button_menus) || count($wpaicg_editor_button_menus) == 0){
                        $wpaicg_editor_button_menus = $this->wpaicg_edit_default_menus;
                    }
                    ?>
                    <script>
                        var wpaicg_plugin_url = '<?php echo esc_html(WPAICG_PLUGIN_URL)?>';
                        var wpaicg_editor_ajax_url = '<?php echo esc_html(admin_url('admin-ajax.php'))?>';
                        var wpaicgTinymceEditorMenus = <?php echo _wp_specialchars(json_encode($wpaicg_editor_button_menus, JSON_UNESCAPED_UNICODE),ENT_NOQUOTES,'UTF-8',true)?>;
                        var wpaicgEditorChangeAction = '<?php echo get_option('wpaicg_editor_change_action','replace')?>';
                    </script>
                    <?php
                    add_filter('mce_external_plugins', array($this, 'wpaicg_add_buttons'));
                    add_filter('mce_buttons', array($this, 'wpaicg_register_buttons'));
                    add_filter('mce_css', array($this,'wpaicg_classic_mce_css'));
                }
            }
        }

        public function wpaicg_classic_mce_css($mce_css)
        {
            if (! empty($mce_css)) {
                $mce_css .= ',';
            }
            $mce_css .= WPAICG_PLUGIN_URL.'admin/css/wpaicg_tinymce.css';

            return $mce_css;

        }

        public function wpaicg_add_buttons($plugins)
        {
            if(current_user_can('wpaicg_ai_assistant')) {
                $plugins['wpaicgeditor'] = WPAICG_PLUGIN_URL . 'admin/js/wpaicg_tinymce.js';
            }
            return $plugins;
        }

        public function wpaicg_register_buttons($buttons)
        {
            array_push( $buttons, 'wpaicgeditor' );
            return $buttons;
        }

        public function wpaicg_editor_prompt()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Missing request parameters','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_REQUEST['prompt']) && !empty($_REQUEST['prompt'])){
                $prompt = sanitize_text_field($_REQUEST['prompt']);
                $wpaicg_openai = WPAICG_OpenAI::get_instance()->openai();
                $wpaicg_generator = WPAICG_Generator::get_instance();
                $wpaicg_generator->openai($wpaicg_openai);
                $result = $wpaicg_generator->wpaicg_request(array(
                    'model' => 'gpt-3.5-turbo',
                    'prompt' => $prompt,
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                    'frequency_penalty' => 0.01,
                    'presence_penalty' => 0.01
                ));
                if($result['status'] == 'error'){
                    $wpaicg_result['msg'] = $result['msg'];
                }
                else {
                    $wpaicg_result['status'] = 'success';
                    $wpaicg_result['data'] = str_replace("\n",'<br>',$result['data']);
                }
            }
            wp_send_json($wpaicg_result);
        }
    }
    WPAICG_Editor::get_instance();
}
