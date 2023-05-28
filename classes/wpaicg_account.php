<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Account')) {
    class WPAICG_Account
    {
        private static $instance = null;
        public $promptbase_sale = false;
        public $form_sale = false;
        public $image_sale = false;
        public $chat_sale = false;
        public $table_name = 'wpaicg_token_logs';

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
            add_action('add_meta_boxes_product', array($this,'wpaicg_register_meta_box'));
            add_action('save_post_product',[$this,'wpaicg_save_product'],10,3);
            add_action('woocommerce_order_status_changed',[$this,'wpaicg_order_completed'],10,3);
            add_shortcode('wpaicg_my_account',[$this,'my_account']);
            $this->create_database_tables();
        }

        public function wpaicg_init()
        {
            $wpaicg_my_account_page_id = get_option('wpaicg_my_account_page_id','');
            if(empty($wpaicg_my_account_page_id)){
                $wpaicg_my_account_page_id = wp_insert_post(array(
                    'post_title' => esc_html__('My AI Account','gpt3-ai-content-generator'),
                    'post_name' => 'myai-account',
                    'post_content' => '[wpaicg_my_account]',
                    'post_type' => 'page',
                    'post_status' => 'publish'
                ));
                update_option('wpaicg_my_account_page_id',$wpaicg_my_account_page_id);
            }
        }

        public function save_log($module, $tokens)
        {
            global $wpdb;
            if(is_user_logged_in()) {
                $user_meta_key = 'wpaicg_' . $module . '_tokens';
                $user_tokens = get_user_meta(get_current_user_id(), $user_meta_key, true);
                $new_tokens = floatval($user_tokens) - floatval($tokens);
                $new_tokens = $new_tokens > 0 ? $new_tokens : 0;
                update_user_meta(get_current_user_id(), $user_meta_key, $new_tokens);
                $wpdb->insert($wpdb->prefix . $this->table_name, array(
                    'module' => $module,
                    'tokens' => $tokens,
                    'created_at' => time(),
                    'user_id' => get_current_user_id()
                ));
            }
        }

        public function create_database_tables()
        {
            global $wpdb;
            $wpaicgLogTable = $wpdb->prefix . $this->table_name;
            if(is_admin()){
                if($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s",$wpaicgLogTable)) != $wpaicgLogTable) {
                    $charset_collate = $wpdb->get_charset_collate();
                    $sql = "CREATE TABLE ".$wpaicgLogTable." (
                    `id` mediumint(11) NOT NULL AUTO_INCREMENT,
                    `user_id` VARCHAR(255) DEFAULT NULL,
                    `module` VARCHAR(255) DEFAULT NULL,
                    `tokens` VARCHAR(255) DEFAULT NULL,
                    `created_at` VARCHAR(255) NOT NULL,
                    PRIMARY KEY  (id)
                    ) $charset_collate";
                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    $wpdb->query($sql);
                    $sql = "ALTER TABLE `".$wpaicgLogTable."` ADD KEY `".$wpaicgLogTable."_user_id_index` (`user_id`)";
                    $wpdb->query($sql);
                }
            }
        }

        public function wpaicg_order_completed($order_id, $old_status, $new_status)
        {
            $order = wc_get_order($order_id);
            $wpaicg_order_status_token = get_option('wpaicg_order_status_token', 'completed');
            if($order && $new_status == $wpaicg_order_status_token){
                $items = $order->get_items();
                $user_id = $order->get_user_id();
                foreach($items as $item){
                    $product_id = $item->get_product_id();
                    $quantity = $item->get_quantity();
                    $wpaicg_product_sale_type = get_post_meta($product_id,'wpaicg_product_sale_type',true);
                    $wpaicg_product_sale_tokens = get_post_meta($product_id,'wpaicg_product_sale_tokens',true);
                    if(
                        !empty($wpaicg_product_sale_type)
                        && in_array($wpaicg_product_sale_type, array('chat','forms','promptbase','image'))
                        && !empty($wpaicg_product_sale_tokens)
                        && $wpaicg_product_sale_tokens > 0
                    ){
                        $wpaicg_service_enable = get_option('wpaicg_'.$wpaicg_product_sale_type.'_enable_sale',false);
                        if($wpaicg_service_enable) {
                            $user_meta_key = 'wpaicg_' . $wpaicg_product_sale_type . '_tokens';
                            $old_tokens = get_user_meta($user_id, $user_meta_key, true);
                            if (empty($old_tokens)) {
                                $old_tokens = 0;
                            }
                            $new_tokens = $old_tokens + ($quantity * $wpaicg_product_sale_tokens);
                            update_user_meta($user_id, $user_meta_key, $new_tokens);
                        }
                    }
                }
            }
        }

        public function wpaicg_save_product($post_id, $post, $update)
        {
            if(isset($_POST['wpaicg_product_sale_type']) && !empty($_POST['wpaicg_product_sale_type'])){
                update_post_meta($post_id,'wpaicg_product_sale_type',sanitize_text_field($_POST['wpaicg_product_sale_type']));
            }
            else{
                delete_post_meta($post_id,'wpaicg_product_sale_type');
            }
            if(isset($_POST['wpaicg_product_sale_tokens']) && !empty($_POST['wpaicg_product_sale_tokens'])){
                update_post_meta($post_id,'wpaicg_product_sale_tokens',sanitize_text_field($_POST['wpaicg_product_sale_tokens']));
            }
            else{
                delete_post_meta($post_id,'wpaicg_product_sale_tokens');
            }
        }

        public function wpaicg_register_meta_box()
        {
            $this->promptbase_sale = get_option('wpaicg_promptbase_enable_sale', false);
            $this->forms_sale = get_option('wpaicg_forms_enable_sale', false);
            $this->image_sale = get_option('wpaicg_image_enable_sale', false);
            $this->chat_sale = get_option('wpaicg_chat_enable_sale', false);
            if((!$this->promptbase_sale || $this->image_sale || $this->chat_sale || $this->form_sale) && current_user_can('wpaicg_woocommerce_meta_box')){
                add_meta_box('wpaicg-sale-tokens', esc_html__('AI Power Token Sale','gpt3-ai-content-generator'), [$this, 'wpaicg_meta_box']);
            }
        }

        public function wpaicg_meta_box($post)
        {
            include WPAICG_PLUGIN_DIR . 'admin/views/account/metabox.php';
        }

        public function wpaicg_menu()
        {
            if(in_array('administrator', (array)wp_get_current_user()->roles)) {
                add_submenu_page(
                    'wpaicg',
                    __('AI Account', 'gpt3-ai-content-generator'),
                    __('AI Account', 'gpt3-ai-content-generator'),
                    'manage_options',
                    'wpaicg_myai_account',
                    array($this, 'my_account_page'),
                    11
                );
            }
            else{
                add_submenu_page(
                    'wpaicg',
                    __('AI Account', 'gpt3-ai-content-generator'),
                    __('AI Account', 'gpt3-ai-content-generator'),
                    'wpaicg_myai_account',
                    'wpaicg_myai_account',
                    array($this, 'my_account_page'),
                    11
                );
            }
        }

        public function my_account_page()
        {
            echo do_shortcode('[wpaicg_my_account]');
        }

        public function my_account()
        {
            if(is_user_logged_in()){
                ob_start();
                include WPAICG_PLUGIN_DIR . 'admin/views/account/index.php';
                $myaccount = ob_get_clean();
                return $myaccount;
            }
            else{
                ?>
                <script>window.location.href='<?php echo esc_url(site_url())?>';</script>
                <?php
            }
        }
    }
    WPAICG_Account::get_instance();
}
