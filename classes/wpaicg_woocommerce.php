<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( '\\WPAICG\\WPAICG_WooCommerce' ) ) {
    class WPAICG_WooCommerce
    {
        private static  $instance = null ;

        public static function get_instance()
        {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action('add_meta_boxes_product', array($this,'wpaicg_register_meta_box'));
            add_action('wp_ajax_wpaicg_product_generator',array($this,'wpaicg_product_generator'));
            add_action('wp_ajax_wpaicg_product_save',array($this,'wpaicg_product_save'));
        }

        public function wpaicg_register_meta_box()
        {
            if(current_user_can('wpaicg_woocommerce')) {
                add_meta_box('wpaicg-woocommerce-generator', esc_html__('AI Power Product Writer','gpt3-ai-content-generator'), [$this, 'wpaicg_meta_box']);
            }
        }

        public function wpaicg_meta_box($post)
        {
                include WPAICG_PLUGIN_DIR . 'admin/views/woocommerce/wpaicg-meta-box.php';
        }

        public function wpaicg_product_save()
        {
            global $wpdb;
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(
                isset($_REQUEST['id'])
                && !empty($_REQUEST['id'])
                && isset($_REQUEST['mode'])
                && !empty($_REQUEST['mode'])
            ){
                $wpaicgMode = sanitize_text_field($_REQUEST['mode']);
                $wpaicgProductID = sanitize_text_field($_REQUEST['id']);
                if($wpaicgMode == 'new'){
                    $wpaicgProductData = array(
                        'post_title' => '',
                        'post_type' => 'product'
                    );
                    if(isset($_REQUEST['wpaicg_product_title']) && !empty($_REQUEST['wpaicg_product_title'])){
                        $wpaicgProductData['post_title'] = sanitize_text_field($_REQUEST['wpaicg_product_title']);
                    }
                    elseif(isset($_REQUEST['wpaicg_original_title']) && !empty($_REQUEST['wpaicg_original_title'])){
                        $wpaicgProductData['post_title'] = sanitize_text_field($_REQUEST['wpaicg_original_title']);
                    }
                    $wpaicgProductID = wp_insert_post($wpaicgProductData);
                }
                $wpaicgData = array('ID' => $wpaicgProductID);
                if(isset($_REQUEST['wpaicg_product_title']) && !empty($_REQUEST['wpaicg_product_title'])){
                    $wpaicgData['post_title'] = sanitize_text_field($_REQUEST['wpaicg_product_title']);
                    update_post_meta($wpaicgProductID,'wpaicg_product_title', sanitize_text_field($_REQUEST['wpaicg_product_title']));
                }
                if(isset($_REQUEST['wpaicg_product_short']) && !empty($_REQUEST['wpaicg_product_short'])){
                    $wpaicgData['post_excerpt'] = sanitize_text_field($_REQUEST['wpaicg_product_short']);
                    update_post_meta($wpaicgProductID,'wpaicg_product_short', sanitize_text_field($_REQUEST['wpaicg_product_short']));
                }
                if(isset($_REQUEST['wpaicg_product_meta']) && !empty($_REQUEST['wpaicg_product_meta'])){
                    $seo_description = sanitize_text_field($_REQUEST['wpaicg_product_meta']);
                    $seo_option = get_option('_yoast_wpseo_metadesc',false);
                    $seo_plugin_activated = wpaicg_util_core()->seo_plugin_activated();
                    if($seo_plugin_activated == '_yoast_wpseo_metadesc' && $seo_option){
                        update_post_meta($wpaicgProductID,$seo_plugin_activated,$seo_description);
                    }
                    $seo_option = get_option('_aioseo_description',false);
                    if($seo_plugin_activated == '_aioseo_description' && $seo_option){
                        update_post_meta($wpaicgProductID,$seo_plugin_activated,$seo_description);
                        $check = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."aioseo_posts WHERE post_id=%d",$wpaicgProductID));
                        if($check){
                            $wpdb->update($wpdb->prefix,'aioseo_posts', array(
                                'description' => $seo_description
                            ), array(
                                'post_id' => $wpaicgProductID
                            ));
                        }
                        else{
                            $wpdb->insert($wpdb->prefix.'aioseo_posts',array(
                                'post_id' => $wpaicgProductID,
                                'description' => $seo_description,
                                'created' => date('Y-m-d H:i:s'),
                                'updated' => date('Y-m-d H:i:s')
                            ));
                        }
                    }
                    $seo_option = get_option('rank_math_description',false);
                    if($seo_plugin_activated == 'rank_math_description' && $seo_option){
                        update_post_meta($wpaicgProductID,$seo_plugin_activated,$seo_description);
                    }
                    update_post_meta($wpaicgProductID,'_wpaicg_meta_description', $seo_description);

                }
                if(isset($_REQUEST['wpaicg_product_description']) && !empty($_REQUEST['wpaicg_product_description'])){
                    $wpaicgData['post_content'] = wp_kses_post($_REQUEST['wpaicg_product_description']);
                    update_post_meta($wpaicgProductID,'wpaicg_product_description', wp_kses_post($_REQUEST['wpaicg_product_description']));
                }
                if(isset($_REQUEST['wpaicg_product_tags']) && !empty($_REQUEST['wpaicg_product_tags'])){
                    $wpaicgTags = sanitize_text_field($_REQUEST['wpaicg_product_tags']);
                    $wpaicgTags = array_map('trim', explode(',', $wpaicgTags));
                    wp_set_object_terms($wpaicgProductID, $wpaicgTags,'product_tag');
                    update_post_meta($wpaicgProductID,'wpaicg_product_tags', sanitize_text_field($_REQUEST['wpaicg_product_tags']));
                }
                if(isset($_REQUEST['wpaicg_generate_title']) && $_REQUEST['wpaicg_generate_title']){
                    update_post_meta($wpaicgProductID,'wpaicg_generate_title', 1);
                }
                else{
                    delete_post_meta($wpaicgProductID,'wpaicg_generate_title');
                }
                if(isset($_REQUEST['wpaicg_generate_description']) && $_REQUEST['wpaicg_generate_description']){
                    update_post_meta($wpaicgProductID,'wpaicg_generate_description', 1);
                }
                else{
                    delete_post_meta($wpaicgProductID,'wpaicg_generate_description');
                }
                if(isset($_REQUEST['wpaicg_generate_short']) && $_REQUEST['wpaicg_generate_short']){
                    update_post_meta($wpaicgProductID,'wpaicg_generate_short', 1);
                }
                else{
                    delete_post_meta($wpaicgProductID,'wpaicg_generate_short');
                }
                if(isset($_REQUEST['wpaicg_generate_tags']) && $_REQUEST['wpaicg_generate_tags']){
                    update_post_meta($wpaicgProductID,'wpaicg_generate_tags', 1);
                }
                else{
                    delete_post_meta($wpaicgProductID,'wpaicg_generate_tags');
                }
                if(isset($_REQUEST['wpaicg_generate_meta']) && $_REQUEST['wpaicg_generate_meta']){
                    update_post_meta($wpaicgProductID,'wpaicg_generate_meta', 1);
                }
                else{
                    delete_post_meta($wpaicgProductID,'wpaicg_generate_meta');
                }
                wp_update_post($wpaicgData);
                $wpaicg_result['status'] = 'success';
                $wpaicg_result['url'] = admin_url('post.php?post='.$wpaicgProductID.'&action=edit');
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_product_generator()
        {
            global $wpdb;
            $open_ai = WPAICG_OpenAI::get_instance()->openai();
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'),'data' => '');
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(!$open_ai){
                $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
                exit;
            }
            ini_set( 'max_execution_time', 1000 );
            $temperature = floatval( $open_ai->temperature );
            $max_tokens = intval( $open_ai->max_tokens );
            $top_p = floatval( $open_ai->top_p );
            $best_of = intval( $open_ai->best_of );
            $frequency_penalty = floatval( $open_ai->frequency_penalty );
            $presence_penalty = floatval( $open_ai->presence_penalty );
            $wpai_language = sanitize_text_field( $open_ai->wpai_language );
            $wpaicg_language_file = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/languages/' . $wpai_language . '.json';
            if ( !file_exists( $wpaicg_language_file ) ) {
                $wpaicg_language_file = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/languages/en.json';
            }
            $wpaicg_language_json = file_get_contents( $wpaicg_language_file );
            $wpaicg_languages = json_decode( $wpaicg_language_json, true );
            if(isset($_REQUEST['step']) && !empty($_REQUEST['step']) && isset($_REQUEST['title']) && !empty($_REQUEST['title'])) {
                $wpaicg_step = sanitize_text_field($_REQUEST['step']);
                $wpaicg_title = sanitize_text_field($_REQUEST['title']);
                if($wpaicg_step == 'meta'){
                    $wpaicg_language_key = 'meta_desc_prompt';
                }
                else{
                    $wpaicg_language_key = isset($wpaicg_languages['woo_product_'.$wpaicg_step]) ? 'woo_product_'.$wpaicg_step : 'woo_product_title';
                }
                /*Custom Prompt*/
                $wpaicg_woo_custom_prompt = get_option('wpaicg_woo_custom_prompt',false);
                if($wpaicg_woo_custom_prompt) {
                    if($wpaicg_step == 'title'){
                        $wpaicg_languages[$wpaicg_language_key] = get_option('wpaicg_woo_custom_prompt_title', esc_html__('Write a SEO friendly product title: %s.','gpt3-ai-content-generator'));
                    }
                    if($wpaicg_step == 'meta'){
                        $wpaicg_languages[$wpaicg_language_key] = get_option('wpaicg_woo_custom_prompt_meta', esc_html__('Write a meta description about: %s. Max: 155 characters.','gpt3-ai-content-generator'));
                    }
                    if($wpaicg_step == 'short'){
                        $wpaicg_languages[$wpaicg_language_key] = get_option('wpaicg_woo_custom_prompt_short', esc_html__('Summarize this product in 2 short sentences: %s.','gpt3-ai-content-generator'));
                    }
                    if($wpaicg_step == 'description'){
                        $wpaicg_languages[$wpaicg_language_key] = get_option('wpaicg_woo_custom_prompt_description', esc_html__('Write a detailed product description about: %s.','gpt3-ai-content-generator'));
                    }
                    if($wpaicg_step == 'tags'){
                        $wpaicg_languages[$wpaicg_language_key] = get_option('wpaicg_woo_custom_prompt_keywords', esc_html__('Suggest keywords for this product: %s.','gpt3-ai-content-generator'));
                    }
                }
                /*End Custom Prompt*/
                $myprompt = isset($wpaicg_languages[$wpaicg_language_key]) && !empty($wpaicg_languages[$wpaicg_language_key]) ? sprintf($wpaicg_languages[$wpaicg_language_key], $wpaicg_title) : $wpaicg_title;
                $wpaicg_result['prompt'] = $myprompt;
                $wpaicg_ai_model = get_option('wpaicg_ai_model','text-davinci-003');
                if($wpaicg_ai_model == 'gpt-3.5-turbo' || $wpaicg_ai_model == 'gpt-4' || $wpaicg_ai_model == 'gpt-4-32k'){
                    $myprompt = $wpaicg_languages['fixed_prompt_turbo'].' '.$myprompt;
                }
                $wpaicg_generator = WPAICG_Generator::get_instance();
                $wpaicg_generator->openai($open_ai);
                $wpaicg_generator->sleep_request();
                $complete = $wpaicg_generator->wpaicg_request([
                    'model' => $wpaicg_ai_model,
                    'prompt' => $myprompt,
                    'temperature' => $temperature,
                    'max_tokens' => $max_tokens,
                    'frequency_penalty' => $frequency_penalty,
                    'presence_penalty' => $presence_penalty,
                    'top_p' => $top_p,
                    'best_of' => $best_of,
                ]);
                if($complete['status'] == 'error'){
                    $wpaicg_result['msg'] = $complete['msg'];
                }
                else{
                    $wpaicg_result['status'] = 'success';
                    $complete = $complete['data'];
                    if($wpaicg_step == 'tags'){
                        $wpaicgTags = preg_split( "/\r\n|\n|\r/", $complete );
                        $wpaicgTags = preg_replace( '/^\\d+\\.\\s/', '', $wpaicgTags );
                        foreach($wpaicgTags as $wpaicgTag){
                            if(!empty($wpaicgTag)){
                                $wpaicg_result['data'] .= (empty($wpaicg_result['data']) ? '' : ', ').trim($wpaicgTag);
                            }
                        }
                    }
                    else{
                        $wpaicg_result['data'] = trim($complete);
                        if($wpaicg_step == 'title'){
                            $wpaicg_result['data'] = str_replace('"','',$wpaicg_result['data']);
                        }
                        if(empty($wpaicg_result['data'])){
                            $wpaicg_result['data'] = esc_html__('There was no response for this product from OpenAI. Please try again','gpt3-ai-content-generator');
                        }
                    }
                }
            }
            wp_send_json($wpaicg_result);
        }
    }

    WPAICG_WooCommerce::get_instance();
}
