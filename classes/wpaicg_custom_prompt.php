<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( '\\WPAICG\\WPAICG_Custom_Prompt' ) ) {
    class WPAICG_Custom_Prompt
    {
        private static  $instance = null ;

        public $wpaicg_default_custom_prompt = 'Create a compelling and well-researched article of at least 500 words on the topic of "[title]" in English. Structure the article with clear headings enclosed within the appropriate heading tags (e.g., <h1>, <h2>, etc.) and engaging subheadings. Ensure that the content is informative and provides valuable insights to the reader. Incorporate relevant examples, case studies, and statistics to support your points. Organize your ideas using unordered lists with <ul> and <li> tags where appropriate. Conclude with a strong summary that ties together the key takeaways of the article. Remember to enclose headings in the specified heading tags to make parsing the content easier. Additionally, wrap even paragraphs in <p> tags for improved readability.';

        public static function get_instance()
        {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action('wp_ajax_wpaicg_generate_custom_prompt',array($this,'wpaicg_generate_custom_prompt'));
        }

        public function wpaicg_generate_custom_prompt()
        {
            $wpaicg_result = array('status' => 'error','tokens' => 0, 'length' => 0);
            if(
                isset($_REQUEST['wpai_preview_title'])
                && !empty($_REQUEST['wpai_preview_title'])
                && isset($_REQUEST['wpaicg_custom_prompt'])
                && !empty($_REQUEST['wpaicg_custom_prompt'])
            ) {
                $wpaicg_generator = WPAICG_Generator::get_instance();
                $openai = WPAICG_OpenAI::get_instance()->openai();
                if(!$openai){
                    $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                    wp_send_json($wpaicg_result);
                    exit;
                }
                $wpaicg_single = new \stdClass();
                $wpaicg_single->wpaicg_keywords = '';
                $wpaicg_single->wpaicg_words_to_avoid = '';
                if(isset($_REQUEST['wpai_keywords']) && !empty($_REQUEST['wpai_keywords'])){
                    $wpaicg_single->wpaicg_keywords = sanitize_text_field($_REQUEST['wpai_keywords']);
                }
                if(isset($_REQUEST['wpai_words_to_avoid']) && !empty($_REQUEST['wpai_words_to_avoid'])){
                    $wpaicg_single->wpaicg_words_to_avoid = sanitize_text_field($_REQUEST['wpai_words_to_avoid']);
                }
                $wpaicg_single->post_title = sanitize_text_field($_REQUEST['wpai_preview_title']);
                $wpaicg_generator->init($openai,$wpaicg_single->post_title);
                $wpaicg_custom_prompt_auto = sanitize_text_field($_REQUEST['wpaicg_custom_prompt']);
                $wpaicg_custom_prompt_auto = str_replace('[title]', $wpaicg_single->post_title, $wpaicg_custom_prompt_auto);
                $wpaicg_generator->wpaicg_opts['prompt'] = $wpaicg_custom_prompt_auto;
                if(wpaicg_util_core()->wpaicg_is_pro()){
                    $result = WPAICG_Custom_Prompt_Pro::get_instance()->request($wpaicg_generator);
                }
                else{
                    $result = $wpaicg_generator->wpaicg_request($wpaicg_generator->wpaicg_opts);
                }
                if($result['status'] == 'success'){
                    $wpaicg_result['status'] = 'success';
                    $generated_content = $result['data'];
                    $wpaicg_result['tokens'] = $result['tokens'];
                    $wpaicg_result['length'] = $result['length'];
                    preg_match_all('/<h\d>([^<]*)<\/h\d>/iU', $generated_content, $matches);
                    $wpaicg_toc_lists = [];
                    $first_heading_tag = $wpaicg_generator->wpaicg_heading_tag;
                    if($matches && is_array($matches) && count($matches) == 2){
                        foreach($matches[1] as $key=>$match){
                            if($key == 0){
                                $first_heading_tag = str_replace(array('<','>'),'',substr($matches[0][0],0,3));
                            }
                            $heading_id = sanitize_title($match);
                            $wpaicg_toc_lists[] = $match;
                            $generated_content = str_replace('>'.$match.'<',' id="wpaicg-'.$heading_id.'">'.$match.'<', $generated_content);
                        }
                    }
                    $wpaicg_result['wpaicg_heading_tag_modify'] = $first_heading_tag;
                    $wpaicg_result['tocs'] = implode('||',$wpaicg_toc_lists);
                    $wpaicg_result['headings'] = implode('||',$wpaicg_toc_lists);
                    $wpaicg_result['content'] = $generated_content;
                }
                else{
                    $wpaicg_result['msg'] = $result['msg'];
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function generator()
        {
            global  $wpdb ;
            update_option( '_wpaicg_cron_added', time() );
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
                $wpaicg_generator = WPAICG_Generator::get_instance();
                $wpaicg_content_class = WPAICG_Content::get_instance();
                $wpaicg_generator_start = microtime( true );
                $wpaicg_generator_tokens = 0;
                $wpaicg_generator_text_length = 0;
                try {
                    wp_update_post( array(
                        'ID'          => $wpaicg_single->ID,
                        'post_status' => 'draft',
                        'post_modified' => date('Y-m-d H:i:s')
                    ) );
                    $openai = WPAICG_OpenAI::get_instance()->openai();
                    if(!$openai){
                        $wpaicg_content_class->wpaicg_bulk_error_log($wpaicg_single->ID, 'Missing API Setting');
                    }
                    else{
                        $wpaicg_custom_prompt_auto = get_option('wpaicg_custom_prompt_auto',$this->wpaicg_default_custom_prompt);
                        $wpaicg_custom_prompt_auto = str_replace('[title]', $wpaicg_single->post_title,$wpaicg_custom_prompt_auto);
                        $wpaicg_generator->init($openai,$wpaicg_single->post_title,true,$wpaicg_single->ID);
                        $wpaicg_has_error = false;
                        $break_step = '';
                        $wpaicg_generator->wpaicg_opts['prompt'] = $wpaicg_custom_prompt_auto;
                        if(wpaicg_util_core()->wpaicg_is_pro()){
                            $result = WPAICG_Custom_Prompt_Pro::get_instance()->request($wpaicg_generator);
                        }
                        else{
                            $result = $wpaicg_generator->wpaicg_request($wpaicg_generator->wpaicg_opts);
                        }
                        if($result['status'] == 'success'){
                            $generated_content = $result['data'];
                            $wpaicg_generator_tokens += $result['tokens'];
                            $wpaicg_generator_text_length += $result['length'];
                            preg_match_all('/<h\d>([^<]*)<\/h\d>/iU', $generated_content, $matches);
                            $wpaicg_toc_lists = [];
                            $first_heading_tag = $wpaicg_generator->wpaicg_heading_tag;
                            if($matches && is_array($matches) && count($matches) == 2){
                                foreach($matches[1] as $key=>$match){
                                    if($key == 0){
                                        $first_heading_tag = str_replace(array('<','>'),'',substr($matches[0][0],0,3));
                                    }
                                    $heading_id = sanitize_title($match);
                                    $wpaicg_toc_lists[] = $match;
                                    $generated_content = str_replace('>'.$match.'<',' id="wpaicg-'.$heading_id.'">'.$match.'<', $generated_content);
                                }
                            }
                            $wpaicg_generator->wpaicg_result['content'] = $generated_content;
                            $steps = array('seo','addition','featuredimage');
                            foreach ($steps as $step){
                                $wpaicg_generator->wpaicg_generator($step);
                                if($wpaicg_generator->error_msg){
                                    $break_step = $step;
                                    $wpaicg_has_error = $wpaicg_generator->error_msg;
                                    break;
                                }
                            }
                            if($wpaicg_has_error){
                                $wpaicg_content_class->wpaicg_bulk_error_log($wpaicg_single->ID, $wpaicg_has_error.'. Break at step '.$break_step);
                                $wpaicg_running = WPAICG_PLUGIN_DIR.'/wpaicg_running.txt';
                                if(file_exists($wpaicg_running)){
                                    unlink($wpaicg_running);
                                }
                            }
                            else{

                                /*Generate Image*/
                                if($wpaicg_generator->wpaicg_image_source == 'dalle') {
                                    $wpaicg_generator->sleep_request();
                                    $_wpaicg_image_style = '';
                                    $_wpaicg_art_style = '';
                                    if(!empty($wpaicg_generator->wpaicg_img_style)){
                                        $_wpaicg_art_style = (isset($wpaicg_generator->wpaicg_languages['art_style']) && !empty($wpaicg_generator->wpaicg_languages['art_style']) ? ' ' . $wpaicg_generator->wpaicg_languages['art_style'] : '');
                                        $_wpaicg_image_style = (isset($wpaicg_generator->wpaicg_languages['img_styles'][$wpaicg_generator->wpaicg_img_style]) && !empty($wpaicg_generator->wpaicg_languages['img_styles'][$wpaicg_generator->wpaicg_img_style]) ? ' ' . $wpaicg_generator->wpaicg_languages['img_styles'][$wpaicg_generator->wpaicg_img_style] : '');
                                    }
                                    $prompt_image = $wpaicg_generator->wpaicg_preview_title . $_wpaicg_art_style . $_wpaicg_image_style;
                                    if($wpaicg_generator->wpaicg_custom_image_settings && is_array($wpaicg_generator->wpaicg_custom_image_settings) && count($wpaicg_generator->wpaicg_custom_image_settings)) {
                                        $prompt_elements = array(
                                            'artist' => esc_html__('Painter','gpt3-ai-content-generator'),
                                            'photography_style' => esc_html__('Photography Style','gpt3-ai-content-generator'),
                                            'composition' => esc_html__('Composition','gpt3-ai-content-generator'),
                                            'resolution' => esc_html__('Resolution','gpt3-ai-content-generator'),
                                            'color' => esc_html__('Color','gpt3-ai-content-generator'),
                                            'special_effects' => esc_html__('Special Effects','gpt3-ai-content-generator'),
                                            'lighting' => esc_html__('Lighting','gpt3-ai-content-generator'),
                                            'subject' => esc_html__('Subject','gpt3-ai-content-generator'),
                                            'camera_settings' => esc_html__('Camera Settings','gpt3-ai-content-generator'),
                                        );
                                        foreach ($wpaicg_generator->wpaicg_custom_image_settings as $key => $value) {
                                            if ($value != "None") {
                                                $prompt_image = $prompt_image . ". " . $prompt_elements[$key] . ": " . $value;
                                            }
                                        }
                                    }
                                    $wpaicg_request = $wpaicg_generator->wpaicg_image([
                                        "prompt" => $prompt_image,
                                        "n" => 1,
                                        "size" => $wpaicg_generator->wpaicg_img_size,
                                        "response_format" => "url",
                                    ]);
                                    if($wpaicg_request['status'] == 'error'){
                                        $wpaicg_generator->wpaicg_result['status'] = 'no_image';
                                        $wpaicg_generator->wpaicg_result['msg'] = $wpaicg_request['msg'];
                                    }
                                    else{
                                        $wpaicg_generator->wpaicg_result['img'] = trim($wpaicg_request['url']);
                                    }
                                }
                                if($wpaicg_generator->wpaicg_image_source == 'pexels'){
                                    $wpaicg_pexels_response = $wpaicg_generator->wpaicg_pexels_generator();
                                    if(isset($wpaicg_pexels_response['pexels_response']) && !empty($wpaicg_pexels_response['pexels_response'])){
                                        $wpaicg_generator->wpaicg_result['img'] = trim($wpaicg_pexels_response['pexels_response']);
                                    }
                                }
                                if(!empty($wpaicg_generator->wpaicg_result['img'])){
                                    $imgresult = "__WPAICG_IMAGE__";
                                    $wpaicg_content = explode("</" . $first_heading_tag . ">", $wpaicg_generator->wpaicg_result['content']);
                                    $wpaicg_content[1] = $imgresult.$wpaicg_content[1];
                                    $wpaicg_generator->wpaicg_result['content'] = implode("</" . $first_heading_tag . ">", $wpaicg_content);
                                }
                                /*End Generate Image*/

                                $wpaicg_generator_result = $wpaicg_generator->wpaicgResult();
                                $wpaicg_generator_text_length += $wpaicg_generator_result['length'];
                                $wpaicg_generator_tokens += $wpaicg_generator_result['tokens'];
                                $wpaicg_allowed_html_content_post = wp_kses_allowed_html( 'post' );
                                $wpaicg_content = wp_kses( $wpaicg_generator_result['content'], $wpaicg_allowed_html_content_post );
                                $wpaicg_post_status = ( $wpaicg_single->post_password == 'draft' ? 'draft' : 'publish' );
                                $wpaicg_image_attachment_id = false;
                                if(isset($wpaicg_generator_result['img']) && !empty($wpaicg_generator_result['img'])){
                                    $wpaicg_image_url = sanitize_url($wpaicg_generator_result['img']);
                                    $wpaicg_image_attachment_id = $wpaicg_content_class->wpaicg_save_image($wpaicg_image_url,$wpaicg_single->post_title);
                                    if($wpaicg_image_attachment_id['status'] == 'success'){
                                        $wpaicg_image_attachment_url = wp_get_attachment_url($wpaicg_image_attachment_id['id']);
                                        $wpaicg_content = str_replace("__WPAICG_IMAGE__", '<img src="'.$wpaicg_image_attachment_url.'" alt="'.$wpaicg_single->post_title.'" />', $wpaicg_content);
                                    }
                                }
                                // Fix empty image
                                $wpaicg_content = str_replace("__WPAICG_IMAGE__", '', $wpaicg_content);
                                /*Add TOC*/
                                if($wpaicg_generator->wpaicg_toc && count($wpaicg_toc_lists)){
                                    $wpaicg_table_content = '<ul class="wpaicg_toc"><li>';
                                    if($wpaicg_generator->wpaicg_toc_title !== ''){
                                        $wpaicg_table_content .= '<'.$wpaicg_generator->wpaicg_toc_title_tag.'>'.$wpaicg_generator->wpaicg_toc_title.'</'.$wpaicg_generator->wpaicg_toc_title_tag.'>';
                                    }
                                    $wpaicg_table_content .= '<ul>';
                                    foreach($wpaicg_toc_lists as $wpaicg_toc_item){
                                        $wpaicg_toc_item_id = 'wpaicg-'.sanitize_title($wpaicg_toc_item);
                                        $wpaicg_table_content .= '<li><a href="#'.$wpaicg_toc_item_id.'">'.$wpaicg_toc_item.'</a></li>';
                                    }
                                    $wpaicg_table_content .= '</ul>';
                                    $wpaicg_table_content .= '</li></ul>';
                                    $wpaicg_content = $wpaicg_table_content.$wpaicg_content;
                                }

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
                                        $wpaicg_content_class->wpaicg_save_description($wpaicg_post_id,sanitize_text_field($wpaicg_generator_result['description']));
                                    }

                                    if(isset($wpaicg_generator_result['featured_img']) && !empty($wpaicg_generator_result['featured_img'])){
                                        $wpaicg_featured_image_url = sanitize_url($wpaicg_generator_result['featured_img']);
                                        $wpaicg_image_attachment_id = $wpaicg_content_class->wpaicg_save_image($wpaicg_featured_image_url,$wpaicg_single->post_title);
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
                        }
                        else{
                            $wpaicg_content_class->wpaicg_bulk_error_log($wpaicg_single->ID, $result['msg']);
                            $wpaicg_running = WPAICG_PLUGIN_DIR.'/wpaicg_running.txt';
                            if(file_exists($wpaicg_running)){
                                unlink($wpaicg_running);
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
    WPAICG_Custom_Prompt::get_instance();
}
