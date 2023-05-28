<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Generator')) {
    class WPAICG_Generator{
        private static $instance = null;
        public $wpaicg_engine = 'text-davinci-003';
        public $wpaicg_max_tokens = 2000;
        public $wpaicg_temperature = 0;
        public $wpaicg_top_p = 1;
        public $wpaicg_best_of = 1;
        public $wpaicg_frequency_penalty = 0;
        public $wpaicg_presence_penalty = 0;
        public $wpaicg_stop = [];
        public $wpaicg_allowed_html_content_post;
        public $wpaicg_image_style;
        public $wpaicg_number_of_heading;
        public $wpaicg_preview_title;
        public $wpaicg_opts = array();
        public $wpaicg_prompt = '';
        public $wpaicg_intro = '';
        public $wpaicg_conclusion = '';
        public $wpaicg_tagline = '';
        public $wpaicg_cta = '';
        public $wpaicg_image_source;
        public $wpaicg_featured_image_source;
        public $wpaicg_language;
        public $wpaicg_add_intro;
        public $wpaicg_add_conclusion;
        public $wpaicg_writing_style;
        public $wpaicg_writing_tone;
        public $wpaicg_keywords;
        public $wpaicg_add_keywords_bold;
        public $wpaicg_heading_tag;
        public $wpaicg_words_to_avoid;
        public $wpaicg_add_tagline;
        public $wpaicg_add_faq;
        public $wpaicg_target_url;
        public $wpaicg_anchor_text;
        public $wpaicg_cta_pos;
        public $wpaicg_target_url_cta;
        public $wpaicg_modify_headings;
        public $wpaicg_toc;
        public $wpaicg_toc_title;
        public $wpaicg_toc_title_tag;
        public $wpaicg_intro_title_tag;
        public $wpaicg_conclusion_title_tag;
        public $wpaicg_pexels_api;
        public $wpaicg_pexels_orientation;
        public $wpaicg_pexels_size;
        public $wpaicg_img_size;
        public $wpaicg_seo_meta_desc;
        public $wpaicg_img_style;
        public $wpaicg_toc_list = array();
        public $generate_continue = false;
        public $wpaicg_content = '';
        public $wpaicg_languages;
        public $writing_style;
        public $tone_text;
        public $conclusion_text;
        public $intro_text;
        public $tagline_text;
        public $faq_heading;
        public $introduction;
        public $faq_text;
        public $conclusion;
        public $style_text;
        public $error_msg = false;
        public $wpaicg_pixabay_api = '';
        public $wpaicg_pixabay_language = 'en';
        public $wpaicg_pixabay_type = 'all';
        public $wpaicg_pixabay_order = 'popular';
        public $wpaicg_pixabay_orientation = 'all';
        public $wpaicg_custom_image_settings = array(
            'artist' => 'None',
            'photography_style' => 'None',
            'lighting' => 'Ambient',
            'subject' => 'None',
            'camera_settings' => 'Aperture',
            'composition' => 'Rule of Thirds',
            'resolution' => '4K (3840x2160)',
            'color' => 'RGB',
            'special_effects' => 'Cinemagraph'
        );
        public $wpaicg_headings = array();
        public $wpaicg_result = array(
            'status'    => 'error',
            'msg'       => 'Something went wrong',
            'tokens' => 0,
            'length' => 0,
            'data' => '',
            'error' => '',
            'content' => '',
            'next_step' => 'content',
            'img' => '',
            'description' => '',
            'featured_img'       => '',
            'tocs' => ''
        );
        public $openai;
        public $wpaicg_sleep = 8;
        public $hide_introduction = false;
        public $hide_conclusion = false;

        public $pixabay_languages = array(
            'cs' => 'Čeština',
            'da' => 'Dansk',
            'de' => 'Deutsch',
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'id' => 'Bahasa Indonesia (Indonesia)',
            'it' => 'Italiano',
            'hu' => 'Magyar (Magyarország)',
            'nl' => 'Nederlands (Nederland)',
            'no' => 'Norwegian',
            'pl' => 'Polski (Polska)',
            'pt' => 'Português (Portugal)',
            'ro' => 'Română (România)',
            'sk' => 'Slovenčina (Slovensko)',
            'fi' => 'Suomi (Suomi)',
            'sv' => 'Svenska (Sverige)',
            'tr' => 'Türkçe (Türkiye)',
            'vi' => 'Tiếng việt',
            'th' => 'ไทย (ประเทศไทย)',
            'bg' => 'Български (България)',
            'ru' => 'Русский (Россия)',
            'el' => 'Ελληνικά (Ελλάδα)',
            'ja' => '日本語（日本)',
            'ko' => '한국어 (대한민국)',
            'zh' => '普通话 (中国大陆)'
        );
        public $wpaicg_pexels_enable_prompt = false;
        public $wpaicg_pexels_custom_prompt = 'Extract the most significant keyword from the given title: [title]. Please provide the keyword in the format #keyword, without any additional sentences, words, or characters. Ensure that the keyword consists of a single word, and do not combine or concatenate words or phrases in the keyword.';
        public $wpaicg_pixabay_enable_prompt = false;
        public $wpaicg_pixabay_custom_prompt = 'Extract the most significant keyword from the given title: [title]. Please provide the keyword in the format #keyword, without any additional sentences, words, or characters. Ensure that the keyword consists of a single word, and do not combine or concatenate words or phrases in the keyword.';

        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action('wp_ajax_wpaicg_content_generator',[$this,'wpaicg_content_generator']);
        }

        public function wpaicg_content_generator()
        {
            $step = isset( $_REQUEST['step'] ) && !empty($_REQUEST['step']) ? sanitize_text_field( $_REQUEST['step'] ) : 'heading';
            $openai = WPAICG_OpenAI::get_instance()->openai();
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $this->wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                $this->wpaicg_result['status'] = 'error';
                wp_send_json($this->wpaicgResult());
            }
            if(!$openai){
                $this->wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                $this->wpaicg_result['status'] = 'error';
            }
            else{
                $this->init($openai);
                $this->wpaicg_generator($step);
            }
            wp_send_json($this->wpaicgResult());
        }

        public function init($open_ai, $wpaicg_preview_title = false, $cronjob = false, $post_id = false)
        {
            $this->openai = $open_ai;
            $img_size = $open_ai->img_size;
            $this->wpaicg_image_style = get_option('_wpaicg_image_style', '');
            $this->wpaicg_temperature = floatval( $open_ai->temperature );
            $this->wpaicg_max_tokens = intval( $open_ai->max_tokens );
            $this->wpaicg_top_p = floatval( $open_ai->top_p );
            $this->wpaicg_best_of = intval( $open_ai->best_of );
            $this->wpaicg_frequency_penalty = floatval( $open_ai->frequency_penalty );
            $this->wpaicg_presence_penalty = floatval( $open_ai->presence_penalty );
            $this->wpaicg_engine = get_option('wpaicg_ai_model','text-davinci-003');
            $this->wpaicg_allowed_html_content_post = wp_kses_allowed_html( 'post' );
            if($cronjob){
                $this->wpaicg_preview_title = $wpaicg_preview_title;
                $this->wpaicg_number_of_heading = $open_ai->wpai_number_of_heading;
                $this->wpaicg_image_source = get_option('wpaicg_image_source','');
                $this->wpaicg_featured_image_source = get_option('wpaicg_featured_image_source','');
                $this->wpaicg_language = sanitize_text_field( $open_ai->wpai_language );
                $this->wpaicg_add_intro = intval( $open_ai->wpai_add_intro );
                $this->wpaicg_add_conclusion = intval( $open_ai->wpai_add_conclusion );
                $this->wpaicg_writing_style = sanitize_text_field( $open_ai->wpai_writing_style );
                $this->wpaicg_writing_tone = sanitize_text_field( $open_ai->wpai_writing_tone );
                $this->wpaicg_keywords = get_post_meta($post_id, '_wpaicg_keywords', true);
                $this->wpaicg_add_keywords_bold = intval($open_ai->wpai_add_keywords_bold);
                $this->wpaicg_heading_tag = sanitize_text_field( $open_ai->wpai_heading_tag );
                $this->wpaicg_words_to_avoid = get_post_meta($post_id,'_wpaicg_avoid',true);
                $this->wpaicg_add_tagline = intval( $open_ai->wpai_add_tagline );
                $this->wpaicg_add_faq = intval( $open_ai->wpai_add_faq );
                $this->wpaicg_seo_meta_desc = get_option('_wpaicg_seo_meta_desc',false);
                $this->wpaicg_target_url = get_post_meta($post_id,'_wpaicg_target',true);
                $this->wpaicg_anchor_text = get_post_meta($post_id,'_wpaicg_anchor',true);
                $this->wpaicg_cta_pos = sanitize_text_field( $open_ai->wpai_cta_pos );
                $this->wpaicg_target_url_cta = get_post_meta($post_id,'_wpaicg_cta',true);
                $this->wpaicg_modify_headings = false;
                $this->wpaicg_toc = get_option('wpaicg_toc',false);
                $this->wpaicg_toc_title = get_option('wpaicg_toc_title',esc_html__('Table of Contents','gpt3-ai-content-generator'));
                $this->wpaicg_toc_title_tag = get_option('wpaicg_toc_title_tag','h2');
                $this->wpaicg_intro_title_tag = get_option('wpaicg_intro_title_tag','h2');
                $this->wpaicg_conclusion_title_tag = get_option('wpaicg_conclusion_title_tag','h2');
                $this->wpaicg_pexels_api = get_option('wpaicg_pexels_api','');
                $this->wpaicg_pexels_orientation = get_option('wpaicg_pexels_orientation','');
                $this->wpaicg_pexels_size = get_option('wpaicg_pexels_size','');
                $this->wpaicg_pixabay_api = get_option('wpaicg_pixabay_api','');
                $this->wpaicg_pixabay_language = get_option('wpaicg_pixabay_language','en');
                $this->wpaicg_pixabay_type = get_option('wpaicg_pixabay_type','all');
                $this->wpaicg_pixabay_order = get_option('wpaicg_pixabay_order','popular');
                $this->wpaicg_pixabay_orientation = get_option('wpaicg_pixabay_orientation','all');
                $this->wpaicg_img_size = $img_size;
                $this->wpaicg_img_style = get_option('_wpaicg_image_style', '');
                $this->wpaicg_toc_list = array();
                $this->generate_continue = false;
                $this->wpaicg_result['content'] = '';
                $wpaicg_custom_image_settings = get_option('wpaicg_custom_image_settings',[]);
                $this->wpaicg_custom_image_settings = wp_parse_args($wpaicg_custom_image_settings, $this->wpaicg_custom_image_settings);
                $this->hide_introduction = get_option('wpaicg_hide_introduction',false);
                $this->hide_conclusion = get_option('wpaicg_hide_conclusion',false);
                $this->wpaicg_pexels_enable_prompt = get_option('wpaicg_pexels_enable_prompt',false);
                $this->wpaicg_pexels_custom_prompt = get_option('wpaicg_pexels_custom_prompt',$this->wpaicg_pexels_custom_prompt);
                $this->wpaicg_pixabay_enable_prompt = get_option('wpaicg_pixabay_enable_prompt',false);
                $this->wpaicg_pixabay_custom_prompt = get_option('wpaicg_pixabay_custom_prompt',$this->wpaicg_pexels_custom_prompt);
            }
            else{
                $this->wpaicg_number_of_heading = sanitize_text_field( $_REQUEST["wpai_number_of_heading"] );
                $this->wpaicg_image_source = sanitize_text_field($_REQUEST['wpaicg_image_source']);
                $this->wpaicg_featured_image_source = sanitize_text_field($_REQUEST['wpaicg_featured_image_source']);
                $this->wpaicg_language = sanitize_text_field( $_REQUEST["wpai_language"] );
                $this->wpaicg_add_intro = intval( sanitize_text_field($_REQUEST["wpai_add_intro"] ));
                $this->wpaicg_add_conclusion = intval( sanitize_text_field($_REQUEST["wpai_add_conclusion"] ));
                $this->wpaicg_writing_style = sanitize_text_field( $_REQUEST["wpai_writing_style"] );
                $this->wpaicg_writing_tone = sanitize_text_field( $_REQUEST["wpai_writing_tone"] );
                $this->wpaicg_keywords = isset($_REQUEST["wpai_keywords"]) ? sanitize_text_field( $_REQUEST["wpai_keywords"] ) : '';
                $this->wpaicg_add_keywords_bold = intval( sanitize_text_field($_REQUEST["wpai_add_keywords_bold"] ));
                $this->wpaicg_heading_tag = sanitize_text_field( $_REQUEST["wpai_heading_tag"] );
                $this->wpaicg_words_to_avoid = isset($_REQUEST['wpai_words_to_avoid']) ? sanitize_text_field( $_REQUEST["wpai_words_to_avoid"] ): '';
                $this->wpaicg_add_tagline = intval( sanitize_text_field($_REQUEST["wpai_add_tagline"] ));
                $this->wpaicg_add_faq = intval( sanitize_text_field($_REQUEST["wpai_add_faq"] ));
                $this->wpaicg_seo_meta_desc = isset($_REQUEST["wpaicg_seo_meta_desc"]) ? intval( sanitize_text_field($_REQUEST["wpaicg_seo_meta_desc"] )) : false;
                $this->wpaicg_target_url = sanitize_text_field( $_REQUEST["wpai_target_url"] );
                $this->wpaicg_anchor_text = sanitize_text_field( $_REQUEST["wpai_anchor_text"] );
                $this->wpaicg_cta_pos = sanitize_text_field( $_REQUEST["wpai_cta_pos"] );
                $this->wpaicg_target_url_cta = sanitize_text_field( $_REQUEST["wpai_target_url_cta"] );
                $this->wpaicg_img_size = sanitize_text_field( $_REQUEST["wpai_img_size"] );
                $this->wpaicg_img_size = ( empty($this->wpaicg_img_size) ? $img_size : $this->wpaicg_img_size );
                $this->wpaicg_img_style = sanitize_text_field( $_REQUEST["wpai_img_style"] );
                $this->wpaicg_img_style = ( empty($this->wpaicg_img_style) ? $this->wpaicg_image_style : $this->wpaicg_img_style );
                $this->wpaicg_modify_headings = intval( sanitize_text_field($_REQUEST["wpai_modify_headings"] ));
                $this->wpaicg_toc = intval(sanitize_text_field($_REQUEST['wpaicg_toc']));
                $this->wpaicg_toc_title = sanitize_text_field($_REQUEST['wpaicg_toc_title']);
                $this->wpaicg_toc_title = empty($this->wpaicg_toc_title) ? 'Table of Contents' : $this->wpaicg_toc_title;
                $this->wpaicg_toc_title_tag = sanitize_text_field($_REQUEST['wpaicg_toc_title_tag']);
                $this->wpaicg_toc_title_tag = empty($this->wpaicg_toc_title_tag) ? 'h2' : $this->wpaicg_toc_title_tag;
                $this->wpaicg_intro_title_tag = sanitize_text_field($_REQUEST['wpaicg_intro_title_tag']);
                $this->wpaicg_intro_title_tag = empty($this->wpaicg_intro_title_tag) ? 'h2' : $this->wpaicg_intro_title_tag;
                $this->wpaicg_conclusion_title_tag = sanitize_text_field($_REQUEST['wpaicg_conclusion_title_tag']);
                $this->wpaicg_conclusion_title_tag = empty($this->wpaicg_conclusion_title_tag) ? 'h2' : $this->wpaicg_conclusion_title_tag;
                $this->wpaicg_toc_list = isset($_REQUEST['wpaicg_toc_list']) && !empty($_REQUEST['wpaicg_toc_list']) ? explode('||',sanitize_text_field($_REQUEST['wpaicg_toc_list'])) : array();
                $this->wpaicg_pexels_orientation = isset($_REQUEST['wpaicg_pexels_orientation']) && !empty($_REQUEST['wpaicg_pexels_orientation']) ? sanitize_text_field($_REQUEST['wpaicg_pexels_orientation']) : '';
                $this->wpaicg_pexels_size = isset($_REQUEST['wpaicg_pexels_size']) && !empty($_REQUEST['wpaicg_pexels_size']) ? sanitize_text_field($_REQUEST['wpaicg_pexels_size']) : '';
                $this->wpaicg_pexels_api = get_option('wpaicg_pexels_api','');
                $this->wpaicg_pixabay_api = get_option('wpaicg_pixabay_api','');
                $this->wpaicg_pixabay_language = isset($_REQUEST['wpaicg_pixabay_language']) && !empty($_REQUEST['wpaicg_pixabay_language']) ? sanitize_text_field($_REQUEST['wpaicg_pixabay_language']) : 'en';
                $this->wpaicg_pixabay_type = isset($_REQUEST['wpaicg_pixabay_type']) && !empty($_REQUEST['wpaicg_pixabay_type']) ? sanitize_text_field($_REQUEST['wpaicg_pixabay_type']) : 'all';
                $this->wpaicg_pixabay_order = isset($_REQUEST['wpaicg_pixabay_order']) && !empty($_REQUEST['wpaicg_pixabay_order']) ? sanitize_text_field($_REQUEST['wpaicg_pixabay_order']) : 'popular';
                $this->wpaicg_pixabay_orientation = isset($_REQUEST['wpaicg_pixabay_orientation']) && !empty($_REQUEST['wpaicg_pixabay_orientation']) ? sanitize_text_field($_REQUEST['wpaicg_pixabay_orientation']) : 'all';
                $this->generate_continue = intval( sanitize_text_field($_REQUEST["is_generate_continue"] ));
                $this->wpaicg_result['tokens'] = isset($_REQUEST['tokens']) ? sanitize_text_field($_REQUEST['tokens']) : 0;
                $this->wpaicg_result['length'] = isset($_REQUEST['length']) ? sanitize_text_field($_REQUEST['length']) : 0;
                $this->wpaicg_result['content'] = ( isset( $_REQUEST['content'] ) ? wp_kses( $_REQUEST['content'], $this->wpaicg_allowed_html_content_post ) : '' );
                $this->wpaicg_preview_title = sanitize_text_field( $_REQUEST["wpai_preview_title"] );
                $hfHeadings = sanitize_text_field( $_REQUEST["hfHeadings"] );
                $this->wpaicg_headings = explode( "||", $hfHeadings );
                if(isset($_REQUEST['wpaicg_custom_image_settings']) && is_array($_REQUEST['wpaicg_custom_image_settings']) && count($_REQUEST['wpaicg_custom_image_settings'])){
                    $wpaicg_custom_image_settings = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['wpaicg_custom_image_settings']);
                }
                else{
                    $wpaicg_custom_image_settings = get_option('wpaicg_custom_image_settings',[]);
                }
                $this->wpaicg_custom_image_settings = wp_parse_args($wpaicg_custom_image_settings, $this->wpaicg_custom_image_settings);
                $this->hide_introduction = (int)sanitize_text_field($_REQUEST['wpaicg_hide_introduction']);
                $this->hide_conclusion = (int)sanitize_text_field($_REQUEST['wpaicg_hide_conclusion']);
                $this->wpaicg_pexels_enable_prompt = isset($_REQUEST["wpaicg_pexels_enable_prompt"]) ? intval( sanitize_text_field($_REQUEST["wpaicg_pexels_enable_prompt"] )) : false;
                $this->wpaicg_pexels_custom_prompt = isset($_REQUEST['wpaicg_pexels_custom_prompt']) && !empty($_REQUEST['wpaicg_pexels_custom_prompt']) ? sanitize_text_field($_REQUEST['wpaicg_pexels_custom_prompt']) : $this->wpaicg_pexels_custom_prompt;
                $this->wpaicg_pixabay_enable_prompt = isset($_REQUEST["wpaicg_pixabay_enable_prompt"]) ? intval( sanitize_text_field($_REQUEST["wpaicg_pixabay_enable_prompt"] )) : false;
                $this->wpaicg_pixabay_custom_prompt = isset($_REQUEST['wpaicg_pixabay_custom_prompt']) && !empty($_REQUEST['wpaicg_pixabay_custom_prompt']) ? sanitize_text_field($_REQUEST['wpaicg_pexels_custom_prompt']) : $this->wpaicg_pixabay_custom_prompt;
            }
            $this->wpaicg_opts = [
                'model'             => $this->wpaicg_engine,
                'temperature'       => $this->wpaicg_temperature,
                'max_tokens'        => $this->wpaicg_max_tokens,
                'frequency_penalty' => $this->wpaicg_frequency_penalty,
                'presence_penalty'  => $this->wpaicg_presence_penalty,
                'top_p'             => $this->wpaicg_top_p,
                'best_of'           => $this->wpaicg_best_of
            ];
            $this->wpaicg_sleep = get_option('wpaicg_sleep_time',8);
            if(empty($this->wpaicg_language)){
                $this->wpaicg_language = 'en';
            }
            if ( empty($this->wpaicg_number_of_heading) ) {
                $this->wpaicg_number_of_heading = 5;
            }
            if ( empty($this->wpaicg_writing_style) ) {
                $this->wpaicg_writing_style = "infor";
            }
            if ( empty($this->wpaicg_writing_tone) ) {
                $this->wpaicg_writing_tone = "formal";
            }
            // if heading tag is not set, set it to h2
            if ( empty($this->wpaicg_heading_tag) ) {
                $this->wpaicg_heading_tag = "h2";
            }
            $wpaicg_language_file = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/languages/' . $this->wpaicg_language . '.json';
            if ( !file_exists( $wpaicg_language_file ) ) {
                $wpaicg_language_file = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/languages/en.json';
            }

            $wpaicg_language_json = file_get_contents( $wpaicg_language_file );
            $this->wpaicg_languages = json_decode( $wpaicg_language_json, true );
            $this->writing_style = ( isset( $this->wpaicg_languages['writing_style'][$this->wpaicg_writing_style] ) ? $this->wpaicg_languages['writing_style'][$this->wpaicg_writing_style] : 'infor' );
            $this->tone_text = ( isset( $this->wpaicg_languages['writing_tone'][$this->wpaicg_writing_tone] ) ? $this->wpaicg_languages['writing_tone'][$this->wpaicg_writing_tone] : 'formal' );
            if ( $this->wpaicg_number_of_heading == 1 ) {
                $prompt_text = ( isset( $this->wpaicg_languages['prompt_text_1'] ) ? $this->wpaicg_languages['prompt_text_1'] : '' );
            } else {
                $prompt_text = ( isset( $this->wpaicg_languages['prompt_text'] ) ? $this->wpaicg_languages['prompt_text'] : '' );
            }
            $this->intro_text = ( isset( $this->wpaicg_languages['intro_text'] ) ? $this->wpaicg_languages['intro_text'] : '' );
            $this->conclusion_text = ( isset( $this->wpaicg_languages['conclusion_text'] ) ? $this->wpaicg_languages['conclusion_text'] : '' );
            $this->tagline_text = ( isset( $this->wpaicg_languages['tagline_text'] ) ? $this->wpaicg_languages['tagline_text'] : '' );
            $this->introduction = ( isset( $this->wpaicg_languages['introduction'] ) ? $this->wpaicg_languages['introduction'] : '' );
            $this->conclusion = ( isset( $this->wpaicg_languages['conclusion'] ) ? $this->wpaicg_languages['conclusion'] : '' );
            if ( $this->wpaicg_language == 'hi' || $this->wpaicg_language == 'tr' || $this->wpaicg_language == 'ja' || $this->wpaicg_language == 'zh' || $this->wpaicg_language == 'ko' ) {
                $this->faq_text = ( isset( $this->wpaicg_languages['faq_text'] ) ? sprintf( $this->wpaicg_languages['faq_text'], $this->wpaicg_preview_title, strval( $this->wpaicg_number_of_heading ) ) : '' );
            } else {
                $this->faq_text = ( isset( $this->wpaicg_languages['faq_text'] ) ? sprintf( $this->wpaicg_languages['faq_text'], strval( $this->wpaicg_number_of_heading ), $this->wpaicg_preview_title ) : '' );
            }

            $this->faq_heading = ( isset( $this->wpaicg_languages['faq_heading'] ) ? $this->wpaicg_languages['faq_heading'] : '' );
            $this->style_text = ( isset( $this->wpaicg_languages['style_text'] ) ? sprintf( $this->wpaicg_languages['style_text'], $this->writing_style ) : '' );
            $of_text = ( isset( $this->wpaicg_languages['of_text'] ) ? $this->wpaicg_languages['of_text'] : '' );
            $prompt_last = ( isset( $this->wpaicg_languages['prompt_last'] ) ? $this->wpaicg_languages['prompt_last'] : '' );
            $piece_text = ( isset( $this->wpaicg_languages['piece_text'] ) ? $this->wpaicg_languages['piece_text'] : '' );

            if ( $this->wpaicg_language == 'ru' || $this->wpaicg_language == 'ko' ) {

                if ( empty($this->wpaicg_keywords) ) {
                    $this->wpaicg_prompt = $prompt_text . strval( $this->wpaicg_number_of_heading ) . $prompt_last . $this->wpaicg_preview_title . ".";
                } else {
                    $keyword_text = ( isset( $this->wpaicg_languages['keyword_text'] ) ? sprintf( $this->wpaicg_languages['keyword_text'], $this->wpaicg_keywords ) : '' );
                    $this->wpaicg_prompt = $prompt_text . strval( $this->wpaicg_number_of_heading ) . $prompt_last . $this->wpaicg_preview_title . $keyword_text;
                }

            } elseif ( $this->wpaicg_language == 'zh' ) {

                if ( empty($this->wpaicg_keywords) ) {
                    $this->wpaicg_prompt = $prompt_text . $this->wpaicg_preview_title . $of_text . strval( $this->wpaicg_number_of_heading ) . $piece_text . ".";
                } else {
                    $keyword_text = ( isset( $this->wpaicg_languages['keyword_text'] ) ? sprintf( $this->wpaicg_languages['keyword_text'], $this->wpaicg_keywords ) : '' );
                    $this->wpaicg_prompt = $prompt_text . $this->wpaicg_preview_title . $of_text . strval( $this->wpaicg_number_of_heading ) . $piece_text . $keyword_text;
                }

            } elseif ( $this->wpaicg_language == 'ja' || $this->wpaicg_language == 'hi' || $this->wpaicg_language == 'tr' ) {

                if ( empty($this->wpaicg_keywords) ) {
                    $this->wpaicg_prompt = $this->wpaicg_preview_title . $prompt_text . strval( $this->wpaicg_number_of_heading ) . $prompt_last . ".";
                } else {
                    $keyword_text = ( isset( $this->wpaicg_languages['keyword_text'] ) ? sprintf( $this->wpaicg_languages['keyword_text'], $this->wpaicg_keywords ) : '' );
                    $this->wpaicg_prompt = $this->wpaicg_preview_title . $prompt_text . strval( $this->wpaicg_number_of_heading ) . $prompt_last . $keyword_text;
                }

            } else {

                if ( empty($this->wpaicg_keywords) ) {
                    $this->wpaicg_prompt = strval( $this->wpaicg_number_of_heading ) . $prompt_text . $this->wpaicg_preview_title . ".";
                } else {
                    $keyword_text = ( isset( $this->wpaicg_languages['keyword_text'] ) ? sprintf( $this->wpaicg_languages['keyword_text'], $this->wpaicg_keywords ) : '' );
                    $this->wpaicg_prompt = strval( $this->wpaicg_number_of_heading ) . $prompt_text . $this->wpaicg_preview_title . $keyword_text;
                }

            }


            if ( !empty($this->wpaicg_words_to_avoid) ) {
                $this->avoid_text = ( isset( $this->wpaicg_languages['avoid_text'] ) ? sprintf( $this->wpaicg_languages['avoid_text'], $this->wpaicg_words_to_avoid ) : '' );
                $this->wpaicg_prompt = $this->wpaicg_prompt . $this->avoid_text;
            }


            if ( $this->wpaicg_language == 'ja' || $this->wpaicg_language == 'tr' ) {
                $this->wpaicg_intro = $this->wpaicg_preview_title . $this->intro_text;
                $this->wpaicg_conclusion = $this->wpaicg_preview_title . $this->conclusion_text;
                $this->wpaicg_tagline = $this->wpaicg_preview_title . $this->tagline_text;
            } else {

                if ( $this->wpaicg_language == 'ko' || $this->wpaicg_language == 'hi' || $this->wpaicg_language == 'ar' ) {
                    $this->wpaicg_intro = $this->intro_text . $this->wpaicg_preview_title;
                    $this->wpaicg_conclusion = $this->conclusion_text . $this->wpaicg_preview_title;
                    $this->wpaicg_tagline = $this->wpaicg_preview_title . $this->tagline_text;
                } else {
                    $this->wpaicg_intro = $this->intro_text . $this->wpaicg_preview_title;
                    $this->wpaicg_conclusion = $this->conclusion_text . $this->wpaicg_preview_title;
                    $this->wpaicg_tagline = $this->tagline_text . $this->wpaicg_preview_title;
                }
            }
            $this->wpaicg_cta = ( isset( $this->wpaicg_languages['mycta'] ) ? sprintf( $this->wpaicg_languages['mycta'], $this->wpaicg_preview_title, $this->wpaicg_target_url_cta ) : '' );
        }

        public function sleep_request()
        {
            if($this->wpaicg_engine == 'gpt-3.5-turbo' || $this->wpaicg_engine == 'gpt-4' || $this->wpaicg_engine == 'gpt-4-32k'){
                sleep($this->wpaicg_sleep);
            }
        }

        public function wpaicg_generator($step = 'heading')
        {
            /*Generate Heading*/
            if($step == 'heading'){
                $this->sleep_request();
                $this->wpaicg_result['hide_introduction'] = $this->hide_introduction;
                $this->wpaicg_result['hide_conclusion'] = $this->hide_conclusion;
                if($this->wpaicg_modify_headings && $this->generate_continue){
                    $this->wpaicg_headings = sanitize_text_field( $_REQUEST["hfHeadings"] );
                    $this->wpaicg_result['next_step'] = 'content';
                    $this->wpaicg_result['data'] = $this->wpaicg_headings;
                    $this->wpaicg_result['status'] = 'success';
                }
                else{
                    if($this->wpaicg_engine == 'gpt-3.5-turbo' || $this->wpaicg_engine == 'gpt-4' || $this->wpaicg_engine == 'gpt-4-32k'){
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_languages['heading_prompt_turbo'].' '.$this->wpaicg_prompt;
                    }
                    else{
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_prompt;
                    }
                    $wpaicg_request = $this->wpaicg_request($this->wpaicg_opts);
                    if($wpaicg_request['status'] == 'error'){
                        $this->wpaicg_result['status'] = 'error';
                        $this->wpaicg_result['msg'] = $wpaicg_request['msg'];
                        $this->error_msg = $wpaicg_request['msg'];
                    }
                    else{
                        $wpaicg_response = $wpaicg_request['data'];
                        $wpaicg_response = preg_replace('/\n$/', '', preg_replace('/^\n/', '', preg_replace('/[\r\n]+/', "\n", $wpaicg_response)));
                        $wpaicg_response = preg_split("/\r\n|\n|\r/", $wpaicg_response);
                        $wpaicg_response = preg_replace('/^\\d+\\.\\s/', '', $wpaicg_response);
                        $wpaicg_response = preg_replace('/\\.$/', '', $wpaicg_response);
                        $wpaicg_response = array_splice($wpaicg_response, 0, strval($this->wpaicg_number_of_heading));
                        $headings = array();
                        foreach($wpaicg_response as $item){
                            $headings[] = str_replace('"','', $item);
                        }
                        $this->wpaicg_headings = $headings;
                        $this->wpaicg_result['next_step'] = 'content';
                        $this->wpaicg_result['data'] = implode('||', $headings);
                        $this->wpaicg_result['status'] = 'success';
                        if ($this->wpaicg_modify_headings && !$this->generate_continue) {
                            $this->wpaicg_result['next_step'] = 'modify_heading';
                        }
                        $this->wpaicg_result['tokens'] += $wpaicg_request['tokens'];
                        $this->wpaicg_result['length'] += $wpaicg_request['length'];
                    }
                }
            }
            /*Generate Content*/
            if($step == 'content'){
                foreach ( $this->wpaicg_headings as $key => $value ) {
                    $this->sleep_request();
                    $withstyle = $value . '. ' . $this->style_text . ', ' . $this->tone_text . '.';
                    if ( !empty(${$this->wpaicg_words_to_avoid}) ) {
                        $withstyle = $value . '. ' . $this->style_text . ', ' . $this->tone_text . ', ' . $this->avoid_text . '.';
                    }
                    if($this->wpaicg_engine == 'gpt-3.5-turbo' || $this->wpaicg_engine == 'gpt-4' || $this->wpaicg_engine == 'gpt-4-32k') {
                        $this->wpaicg_opts['prompt'] = sprintf($this->wpaicg_languages['content_prompt_turbo'],$this->wpaicg_preview_title).' '.$withstyle;
                    }
                    else{
                        $this->wpaicg_opts['prompt'] = $withstyle;
                    }
                    $wpaicg_request = $this->wpaicg_request($this->wpaicg_opts);
                    if($wpaicg_request['status'] == 'error'){
                        $this->wpaicg_result['status'] = 'error';
                        $this->wpaicg_result['msg'] = $wpaicg_request['msg'];
                        $this->error_msg = $wpaicg_request['msg'];
                    }
                    else{
                        $wpaicg_response = $wpaicg_request['data'];
                        $value = str_replace( '\\/', '', $value );
                        $value = str_replace( '\\', '', $value );
                        $value = trim( $value );
                        // we will add h tag if the user wants to
                        $wpaicg_heading_id = 'wpaicg-'.sanitize_title($value);
                        $this->wpaicg_toc_list[] = $value;
                        if ( $this->wpaicg_heading_tag == "h1" ) {
                            $result = "<h1 id=\"$wpaicg_heading_id\">" . $value . "</h1>" . $wpaicg_response;
                        } elseif ( $this->wpaicg_heading_tag == "h2" ) {
                            $result = "<h2 id=\"$wpaicg_heading_id\">" . $value . "</h2>" . $wpaicg_response;
                        } elseif ( $this->wpaicg_heading_tag == "h3" ) {
                            $result = "<h3 id=\"$wpaicg_heading_id\">" . $value . "</h3>" . $wpaicg_response;
                        } elseif ( $this->wpaicg_heading_tag == "h4" ) {
                            $result = "<h4 id=\"$wpaicg_heading_id\">" . $value . "</h4>" . $wpaicg_response;
                        } elseif ( $this->wpaicg_heading_tag == "h5" ) {
                            $result = "<h5 id=\"$wpaicg_heading_id\">" . $value . "</h5>" . $wpaicg_response;
                        } elseif ( $this->wpaicg_heading_tag == "h6" ) {
                            $result = "<h6 id=\"$wpaicg_heading_id\">" . $value . "</h6>" . $wpaicg_response;
                        } else {
                            $result = "<h2 id=\"$wpaicg_heading_id\">" . $value . "</h2>" . $wpaicg_response;
                        }
                        $this->wpaicg_result['content'] = $this->wpaicg_result['content'].$result;
                        $this->wpaicg_result['status'] = 'success';
                        $this->wpaicg_result['next_step'] = 'intro';
                        $this->wpaicg_result['tokens'] += $wpaicg_request['tokens'];
                        $this->wpaicg_result['length'] += $wpaicg_request['length'];
                    }
                }
            }
            /*Generate Intro*/
            if($step == 'intro'){
                if($this->wpaicg_add_intro){
                    $this->sleep_request();
                    if($this->wpaicg_engine == 'gpt-3.5-turbo' || $this->wpaicg_engine == 'gpt-4' || $this->wpaicg_engine == 'gpt-4-32k') {
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_languages['fixed_prompt_turbo'].' '.$this->wpaicg_intro;
                    }
                    else{
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_intro;
                    }
                    $wpaicg_request = $this->wpaicg_request($this->wpaicg_opts);
                    if($wpaicg_request['status'] == 'error'){
                        $this->wpaicg_result['status'] = 'error';
                        $this->wpaicg_result['msg'] = $wpaicg_request['msg'];
                        $this->error_msg = $wpaicg_request['msg'];
                    }
                    else{
                        $wpaicg_response = $wpaicg_request['data'];
                        if(!$this->hide_introduction) {
                            $wpaicg_toc_list_new = array($this->introduction);
                            foreach ($this->wpaicg_toc_list as $wpaicg_toc_item) {
                                $wpaicg_toc_list_new[] = $wpaicg_toc_item;
                            }
                            $this->wpaicg_toc_list = $wpaicg_toc_list_new;
                            $wpaicg_introduction_id = 'wpaicg-' . sanitize_title($this->introduction);
                            $wpaicg_response = '<' . $this->wpaicg_intro_title_tag . ' id="' . $wpaicg_introduction_id . '">' . $this->introduction . '</' . $this->wpaicg_intro_title_tag . '>' . $wpaicg_response;
                        }
                        $this->wpaicg_result['content'] = $wpaicg_response . $this->wpaicg_result['content'];
                        $this->wpaicg_result['status'] = 'success';
                        $this->wpaicg_result['next_step'] = 'faq';
                        $this->wpaicg_result['tokens'] += $wpaicg_request['tokens'];
                        $this->wpaicg_result['length'] += $wpaicg_request['length'];
                    }
                }
                else{
                    $this->wpaicg_result['status'] = 'success';
                    $this->wpaicg_result['next_step'] = 'faq';
                }
            }
            /*Generate FAQ*/
            if($step == 'faq'){
                if($this->wpaicg_add_faq){
                    $this->sleep_request();
                    if($this->wpaicg_engine == 'gpt-3.5-turbo' || $this->wpaicg_engine == 'gpt-4' || $this->wpaicg_engine == 'gpt-4-32k') {
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_languages['fixed_prompt_turbo'].' '.$this->faq_text;
                    }
                    else{
                        $this->wpaicg_opts['prompt'] = $this->faq_text;
                    }
                    $wpaicg_request = $this->wpaicg_request($this->wpaicg_opts);
                    if($wpaicg_request['status'] == 'error'){
                        $this->wpaicg_result['status'] = 'error';
                        $this->wpaicg_result['msg'] = $wpaicg_request['msg'];
                        $this->error_msg = $wpaicg_request['msg'];
                    }
                    else{
                        $wpaicg_response = $wpaicg_request['data'];
                        $this->wpaicg_toc_list[] = $this->faq_heading;
                        $wpaicg_faq_id = 'wpaicg-'.sanitize_title($this->faq_heading);
                        $wpaicg_response = "<h2 id=\"$wpaicg_faq_id\">" . $this->faq_heading . "</h2>" . $wpaicg_response;
                        $this->wpaicg_result['content'] = $this->wpaicg_result['content'].$wpaicg_response;
                        $this->wpaicg_result['status'] = 'success';
                        $this->wpaicg_result['next_step'] = 'conclusion';
                        $this->wpaicg_result['tokens'] += $wpaicg_request['tokens'];
                        $this->wpaicg_result['length'] += $wpaicg_request['length'];
                    }
                }
                else{
                    $this->wpaicg_result['status'] = 'success';
                    $this->wpaicg_result['next_step'] = 'conclusion';
                }
            }
            /*Generate Conclusion*/
            if($step == 'conclusion'){
                if($this->wpaicg_add_conclusion){
                    $this->sleep_request();
                    if($this->wpaicg_engine == 'gpt-3.5-turbo' || $this->wpaicg_engine == 'gpt-4' || $this->wpaicg_engine == 'gpt-4-32k') {
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_languages['fixed_prompt_turbo'].' '.$this->wpaicg_conclusion;
                    }
                    else{
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_conclusion;
                    }
                    $wpaicg_request = $this->wpaicg_request($this->wpaicg_opts);
                    if($wpaicg_request['status'] == 'error'){
                        $this->wpaicg_result['status'] = 'error';
                        $this->wpaicg_result['msg'] = $wpaicg_request['msg'];
                        $this->error_msg = $wpaicg_request['msg'];
                    }
                    else{
                        $wpaicg_response = $wpaicg_request['data'];
                        if(!$this->hide_conclusion) {
                            $this->wpaicg_toc_list[] = $this->conclusion;
                            $wpaicg_conclusion_id = 'wpaicg-' . sanitize_title($this->conclusion);
                            $wpaicg_response = '<' . $this->wpaicg_conclusion_title_tag . ' id="' . $wpaicg_conclusion_id . '">' . $this->conclusion . '</' . $this->wpaicg_conclusion_title_tag . '>' . $wpaicg_response;
                        }
                        $this->wpaicg_result['content'] = $this->wpaicg_result['content'].$wpaicg_response;
                        $this->wpaicg_result['status'] = 'success';
                        $this->wpaicg_result['next_step'] = 'tagline';
                        $this->wpaicg_result['tokens'] += $wpaicg_request['tokens'];
                        $this->wpaicg_result['length'] += $wpaicg_request['length'];
                    }
                }
                else{
                    $this->wpaicg_result['status'] = 'success';
                    $this->wpaicg_result['next_step'] = 'tagline';
                }
            }
            /*Generate Tagline*/
            if($step == 'tagline'){
                if($this->wpaicg_add_tagline){
                    $this->sleep_request();
                    if($this->wpaicg_engine == 'gpt-3.5-turbo' || $this->wpaicg_engine == 'gpt-4' || $this->wpaicg_engine == 'gpt-4-32k') {
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_languages['fixed_prompt_turbo'].' '.$this->wpaicg_tagline;
                    }
                    else{
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_tagline;
                    }
                    $wpaicg_request = $this->wpaicg_request($this->wpaicg_opts);
                    if($wpaicg_request['status'] == 'error'){
                        $this->wpaicg_result['status'] = 'error';
                        $this->wpaicg_result['msg'] = $wpaicg_request['msg'];
                        $this->error_msg = $wpaicg_request['msg'];
                    }
                    else{
                        $this->wpaicg_result['status'] = 'success';
                        $wpaicg_response = $wpaicg_request['data'];
                        $wpaicg_response = "<p>" . $wpaicg_response . "</p>";
                        $this->wpaicg_result['content'] = $wpaicg_response.$this->wpaicg_result['content'];
                        $this->wpaicg_result['tokens'] += $wpaicg_request['tokens'];
                        $this->wpaicg_result['length'] += $wpaicg_request['length'];
                    }
                }
                else{
                    $this->wpaicg_result['status'] = 'success';
                }
                if($this->wpaicg_seo_meta_desc){
                    $this->wpaicg_result['next_step'] = 'seo';
                }
                else{
                    $this->wpaicg_result['next_step'] = 'addition';
                }
            }
            /*Generate SEO*/
            if($step == 'seo'){
                $this->wpaicg_result['next_step'] = 'addition';
                if($this->wpaicg_seo_meta_desc){
                    $this->sleep_request();
                    $meta_desc_prompt = ( isset( $this->wpaicg_languages['meta_desc_prompt'] ) && !empty($this->wpaicg_languages['meta_desc_prompt']) ? sprintf( $this->wpaicg_languages['meta_desc_prompt'], $this->wpaicg_preview_title ) : 'Write a meta description about: ' . $this->wpaicg_preview_title .'. Max: 155 characters');
                    if($this->wpaicg_engine == 'gpt-3.5-turbo' || $this->wpaicg_engine == 'gpt-4' || $this->wpaicg_engine == 'gpt-4-32k') {
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_languages['fixed_prompt_turbo'].' '.$meta_desc_prompt;
                    }
                    else{
                        $this->wpaicg_opts['prompt'] = $meta_desc_prompt;
                    }
                    $wpaicg_request = $this->wpaicg_request($this->wpaicg_opts);
                    if($wpaicg_request['status'] == 'error'){
                        $this->wpaicg_result['status'] = 'error';
                        $this->wpaicg_result['msg'] = $wpaicg_request['msg'];
                        $this->error_msg = $wpaicg_request['msg'];
                    }
                    else{
                        $wpaicg_response = $wpaicg_request['data'];
                        $this->wpaicg_result['status'] = 'success';
                        $this->wpaicg_result['description'] = $wpaicg_response;
                        $this->wpaicg_result['tokens'] += $wpaicg_request['tokens'];
                        $this->wpaicg_result['length'] += $wpaicg_request['length'];
                    }
                }
                else{
                    $this->wpaicg_result['status'] = 'success';
                }
            }
            /*Generate Addition*/
            if($step == 'addition'){
                if($this->wpaicg_add_keywords_bold){
                    if($this->wpaicg_keywords != ''){
                        if ( strpos( $this->wpaicg_keywords, ',' ) !== false ) {
                            $keywords = explode( ",", $this->wpaicg_keywords );
                        } else {
                            $keywords = array( $this->wpaicg_keywords );
                        }

                        // loop through keywords and bold them
                        foreach ( $keywords as $keyword ) {
                            $keyword = trim( $keyword );
                            // replace keyword with bold keyword but make sure exact match is found. for example if the keyword is "the" then it should not replace "there" with "there".. capital dont matter
//                            $this->wpaicg_result['content'] = preg_replace( '/\\b' . $keyword . '\\b/', '<strong>' . $keyword . '</strong>', $this->wpaicg_result['content'] );
                            $this->wpaicg_result['content'] = preg_replace(
                                '/(?<!<h[1-6]><a href=")(?<!<a href=")(?<!<h[1-6]>)(?<!<h[1-6]><strong>)(?<!<strong>)(?<!<h[1-6]><em>)(?<!<em>)(?<!<h[1-6]><strong><em>)(?<!<strong><em>)(?<!<h[1-6]><em><strong>)(?<!<em><strong>)\\b' . $keyword . '\\b(?![^<]*<\\/a>)(?![^<]*<\\/h[1-6]>)(?![^<]*<\\/strong>)(?![^<]*<\\/em>)(?![^<]*<\\/strong><\\/em>)(?![^<]*<\\/em><\\/strong>)/i',
                                '<strong>'.$keyword.'</strong>',
                                $this->wpaicg_result['content']
                            );
                        }
                    }
                }
                if($this->wpaicg_target_url != '' && $this->wpaicg_anchor_text != ''){
                    $this->wpaicg_result['content'] = preg_replace(
                        '/(?<!<h[1-6]><a href=")(?<!<a href=")(?<!<h[1-6]>)(?<!<h[1-6]><strong>)(?<!<strong>)(?<!<h[1-6]><em>)(?<!<em>)(?<!<h[1-6]><strong><em>)(?<!<strong><em>)(?<!<h[1-6]><em><strong>)(?<!<em><strong>)\\b' . $this->wpaicg_anchor_text . '\\b(?![^<]*<\\/a>)(?![^<]*<\\/h[1-6]>)(?![^<]*<\\/strong>)(?![^<]*<\\/em>)(?![^<]*<\\/strong><\\/em>)(?![^<]*<\\/em><\\/strong>)/i',
                        '<a href="' . $this->wpaicg_target_url . '">' . $this->wpaicg_anchor_text . '</a>',
                        $this->wpaicg_result['content'],
                        1
                    );
                }
                $this->wpaicg_result['status'] = 'success';
                if($this->wpaicg_target_url_cta !== ''){
                    $this->sleep_request();
                    if($this->wpaicg_engine == 'gpt-3.5-turbo' || $this->wpaicg_engine == 'gpt-4' || $this->wpaicg_engine == 'gpt-4-32k') {
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_languages['fixed_prompt_turbo'].' '.$this->wpaicg_cta;
                    }
                    else{
                        $this->wpaicg_opts['prompt'] = $this->wpaicg_cta;
                    }
                    $wpaicg_request = $this->wpaicg_request($this->wpaicg_opts);
                    if($wpaicg_request['status'] == 'error'){
                        $this->wpaicg_result['status'] = 'error';
                        $this->wpaicg_result['msg'] = $wpaicg_request['msg'];
                        $this->error_msg = $wpaicg_request['msg'];
                    }
                    else{
                        $wpaicg_response = $wpaicg_request['data'];
                        $wpaicg_response = "<p>" . $wpaicg_response . "</p>";
                        if ( $this->wpaicg_cta_pos == "beg" ) {
                            $this->wpaicg_result['content'] = preg_replace(
                                '/(<h[1-6])/',
                                $wpaicg_response . ' $1',
                                $this->wpaicg_result['content'],
                                1
                            );
                        } else {
                            $this->wpaicg_result['content'] = $this->wpaicg_result['content'] . $wpaicg_response;
                        }
                        $this->wpaicg_result['tokens'] += $wpaicg_request['tokens'];
                        $this->wpaicg_result['length'] += $wpaicg_request['length'];
                    }
                }
                if($this->wpaicg_toc && is_array($this->wpaicg_toc_list) && count($this->wpaicg_toc_list)){
                    $wpaicg_table_content = '<ul class="wpaicg_toc"><li>';
                    if($this->wpaicg_toc_title !== ''){
                        $wpaicg_table_content .= '<'.$this->wpaicg_toc_title_tag.'>'.$this->wpaicg_toc_title.'</'.$this->wpaicg_toc_title_tag.'>';
                    }
                    $wpaicg_table_content .= '<ul>';
                    foreach($this->wpaicg_toc_list as $wpaicg_toc_item){
                        $wpaicg_toc_item_id = 'wpaicg-'.sanitize_title($wpaicg_toc_item);
                        $wpaicg_table_content .= '<li><a href="#'.$wpaicg_toc_item_id.'">'.$wpaicg_toc_item.'</a></li>';
                    }
                    $wpaicg_table_content .= '</ul>';
                    $wpaicg_table_content .= '</li></ul>';
                    $this->wpaicg_result['content'] = $wpaicg_table_content.$this->wpaicg_result['content'];
                }
                $this->wpaicg_result['next_step'] = 'image';
            }
            /*Generate Image*/
            if($step == 'image'){
                $this->wpaicg_result['status'] = 'success';
                $this->wpaicg_result['next_step'] = 'featuredimage';
                if(!empty($this->wpaicg_image_source)){
                    if($this->wpaicg_image_source == 'dalle') {
                        $this->sleep_request();
                        $_wpaicg_image_style = '';
                        $_wpaicg_art_style = '';
                        if(!empty($this->wpaicg_img_style)){
                            $_wpaicg_art_style = (isset($this->wpaicg_languages['art_style']) && !empty($this->wpaicg_languages['art_style']) ? ' ' . $this->wpaicg_languages['art_style'] : '');
                            $_wpaicg_image_style = (isset($this->wpaicg_languages['img_styles'][$this->wpaicg_img_style]) && !empty($this->wpaicg_languages['img_styles'][$this->wpaicg_img_style]) ? ' ' . $this->wpaicg_languages['img_styles'][$this->wpaicg_img_style] : '');
                        }
                        $prompt_image = $this->wpaicg_preview_title . $_wpaicg_art_style . $_wpaicg_image_style;
                        if($this->wpaicg_custom_image_settings && is_array($this->wpaicg_custom_image_settings) && count($this->wpaicg_custom_image_settings)) {
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
                            foreach ($this->wpaicg_custom_image_settings as $key => $value) {
                                if ($value != "None") {
                                    $prompt_image = $prompt_image . ". " . $prompt_elements[$key] . ": " . $value;
                                }
                            }
                        }
                        $wpaicg_request = $this->wpaicg_image([
                            "prompt" => $prompt_image,
                            "n" => 1,
                            "size" => $this->wpaicg_img_size,
                            "response_format" => "url",
                        ]);
                        if($wpaicg_request['status'] == 'error'){
                            $this->wpaicg_result['status'] = 'no_image';
                            $this->wpaicg_result['msg'] = $wpaicg_request['msg'];
                        }
                        else{
                            $this->wpaicg_result['img'] = trim($wpaicg_request['url']);
                        }
                    }
                    if($this->wpaicg_image_source == 'pixabay'){
                        $wpaicg_pixabay_response = $this->wpaicg_pixabay_generator();
                        if(isset($wpaicg_pixabay_response['img']) && !empty($wpaicg_pixabay_response['img'])){
                            $this->wpaicg_result['img'] = trim($wpaicg_pixabay_response['img']);
                        }
                    }
                    if($this->wpaicg_image_source == 'pexels'){
                        $wpaicg_pexels_response = $this->wpaicg_pexels_generator();
                        if(isset($wpaicg_pexels_response['pexels_response']) && !empty($wpaicg_pexels_response['pexels_response'])){
                            $this->wpaicg_result['img'] = trim($wpaicg_pexels_response['pexels_response']);
                        }
                    }
                    if(!empty($this->wpaicg_result['img'])){
                        $imgresult = "__WPAICG_IMAGE__";
                        $half = intval($this->wpaicg_number_of_heading) / 2;
                        $half = round($half);
                        $half = $half - 1;
                        $wpaicg_heading_tag_default = $this->wpaicg_heading_tag;
                        if(isset($_REQUEST['wpaicg_heading_tag_modify']) && !empty($_REQUEST['wpaicg_heading_tag_modify'])){
                            $wpaicg_heading_tag_default = sanitize_text_field($_REQUEST['wpaicg_heading_tag_modify']);
                            $half = 0;
                        }
                        $wpaicg_content = explode("</" . $wpaicg_heading_tag_default . ">", $this->wpaicg_result['content']);
                        if(count($wpaicg_content) >= 2){
                            $wpaicg_content[$half+1] = $imgresult.'<br />'.$wpaicg_content[$half+1];
                        }
                        else{
                            $wpaicg_content[$half] = $wpaicg_content[$half] . $imgresult;
                        }
                        $this->wpaicg_result['content'] = implode("</" . $wpaicg_heading_tag_default . ">", $wpaicg_content);
                    }
                }
            }
            /*Generate Featured Image*/
            if($step == 'featuredimage'){
                $this->wpaicg_result['status'] = 'success';
                $this->wpaicg_result['next_step'] = 'DONE';
                if(!empty($this->wpaicg_featured_image_source)){
                    if($this->wpaicg_featured_image_source == 'dalle') {
                        $this->sleep_request();
                        $_wpaicg_image_style = '';
                        $_wpaicg_art_style = '';
                        if(!empty($this->wpaicg_img_style)){
                            $_wpaicg_art_style = (isset($this->wpaicg_languages['art_style']) && !empty($this->wpaicg_languages['art_style']) ? ' ' . $this->wpaicg_languages['art_style'] : '');
                            $_wpaicg_image_style = (isset($this->wpaicg_languages['img_styles'][$this->wpaicg_img_style]) && !empty($this->wpaicg_languages['img_styles'][$this->wpaicg_img_style]) ? ' ' . $this->wpaicg_languages['img_styles'][$this->wpaicg_img_style] : '');
                        }
                        $prompt_image = $this->wpaicg_preview_title . $_wpaicg_art_style . $_wpaicg_image_style;
                        if($this->wpaicg_custom_image_settings && is_array($this->wpaicg_custom_image_settings) && count($this->wpaicg_custom_image_settings)) {
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
                            foreach ($this->wpaicg_custom_image_settings as $key => $value) {
                                if ($value != "None") {
                                    $prompt_image = $prompt_image . ". " . $prompt_elements[$key] . ": " . $value;
                                }
                            }
                        }
                        $wpaicg_request = $this->wpaicg_image([
                            "prompt" => $prompt_image,
                            "n" => 1,
                            "size" => $this->wpaicg_img_size,
                            "response_format" => "url",
                        ]);
                        if($wpaicg_request['status'] == 'error'){
                            $this->wpaicg_result['status'] = 'no_image';
                            $this->wpaicg_result['msg'] = $wpaicg_request['msg'];
                        }
                        else{
                            $this->wpaicg_result['featured_img'] = trim($wpaicg_request['url']);
                        }
                    }
                    if($this->wpaicg_featured_image_source == 'pixabay'){
                        $wpaicg_pixabay_response = $this->wpaicg_pixabay_generator();
                        if(isset($wpaicg_pixabay_response['img']) && !empty($wpaicg_pixabay_response['img'])){
                            $this->wpaicg_result['featured_img'] = trim($wpaicg_pixabay_response['img']);
                        }
                    }
                    if($this->wpaicg_featured_image_source == 'pexels'){
                        $wpaicg_pexels_response = $this->wpaicg_pexels_generator();
                        if(isset($wpaicg_pexels_response['pexels_response']) && !empty($wpaicg_pexels_response['pexels_response'])){
                            $this->wpaicg_result['featured_img'] = trim($wpaicg_pexels_response['pexels_response']);
                        }
                    }
                }
            }
            $this->wpaicg_result['tocs'] = implode('||', $this->wpaicg_toc_list);
        }

        public function wpaicgResult()
        {
            return $this->wpaicg_result;
        }

        public function wpaicg_pixabay_generator()
        {
            $wpaicg_result = array('status' => 'success');
            if(!empty($this->wpaicg_pixabay_api)) {
                $query = $this->wpaicg_preview_title;
                if($this->wpaicg_pixabay_enable_prompt){
                    $this->wpaicg_pixabay_custom_prompt = str_replace('[title]',$this->wpaicg_preview_title,$this->wpaicg_pixabay_custom_prompt);
                    $keyword = $this->wpaicg_request(array(
                        'prompt' => $this->wpaicg_pixabay_custom_prompt,
                        'model' => 'gpt-3.5-turbo',
                        'temperature' => 0.5,
                        'max_tokens' => 20,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                    ));
                    if($keyword && is_array($keyword)){
                        if($keyword['status'] == 'success'){
                            $query = trim($keyword['data']);
                            $query = str_replace('#','',$query);
                            $query = trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $query));
                        }
                    }
                }
                $requests = array(
                    'key' => $this->wpaicg_pixabay_api,
                    'q' => $query,
                    'pretty' => true,
                    'lang' => $this->wpaicg_pixabay_language,
                    'order' => $this->wpaicg_pixabay_order,
                    'image_type' => $this->wpaicg_pixabay_type,
                    'orientation' => $this->wpaicg_pixabay_orientation
                );
                $pixabay_url = 'https://pixabay.com/api/?'.http_build_query($requests);
                $response = wp_remote_get($pixabay_url);
                if(is_wp_error($response)){
                    $wpaicg_result['status'] = 'error';
                    $wpaicg_result['msg'] = $response->get_error_message();
                }
                else{
                    $json = wp_remote_retrieve_body($response);
                    $result = json_decode($json,true);
                    if($result && is_array($result) && isset($result['hits']) && is_array($result['hits']) && count($result['hits'])){
                        $first_image = $result['hits'][0];
                        $wpaicg_result['img'] = $first_image['webformatURL'];
                    }
                    else{
                        $wpaicg_result['status'] = 'error';
                        $wpaicg_result['msg'] = esc_html__('No image generated','gpt3-ai-content-generator');
                    }
                }
            }
            else{
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('Missing Pixabay API Setting','gpt3-ai-content-generator');
            }
            return $wpaicg_result;

        }

        public function wpaicg_pexels_generator()
        {
            $wpaicg_result = array('status' => 'success');
            if(!empty($this->wpaicg_pexels_api)) {
                $query = $this->wpaicg_preview_title;
                if($this->wpaicg_pexels_enable_prompt){
                    $this->wpaicg_pexels_custom_prompt = str_replace('[title]',$this->wpaicg_preview_title,$this->wpaicg_pexels_custom_prompt);
                    $keyword = $this->wpaicg_request(array(
                        'prompt' => $this->wpaicg_pexels_custom_prompt,
                        'model' => 'gpt-3.5-turbo',
                        'temperature' => 0.5,
                        'max_tokens' => 20,
                        'frequency_penalty' => 0,
                        'presence_penalty' => 0,
                    ));
                    if($keyword && is_array($keyword)){
                        if($keyword['status'] == 'success'){
                            $query = trim($keyword['data']);
                            $query = str_replace('#','',$query);
                            $query = trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $query));
                        }
                    }
                }
                $wpaicg_pexels_url = 'https://api.pexels.com/v1/search?query='.$query.'&per_page=1';
                if(!empty($this->wpaicg_pexels_orientation)){
                    $wpaicg_pexels_orientation = strtolower($this->wpaicg_pexels_orientation);
                    $wpaicg_pexels_url .= '&orientation='.$wpaicg_pexels_orientation;
                }
                $response = wp_remote_get($wpaicg_pexels_url,array(
                    'headers' => array(
                        'Authorization' => $this->wpaicg_pexels_api
                    ),
                    'timeout' => 100
                ));
                if(is_wp_error($response)){
                    $wpaicg_result['status'] = 'error';
                    $wpaicg_result['msg'] = $response->get_error_message();
                }
                else{
                    $body = json_decode($response['body'],true);
                    if($body && is_array($body) && isset($body['photos']) && is_array($body['photos']) && count($body['photos'])){
                        $wpaicg_pexels_key = 'medium';
                        if(!empty($this->wpaicg_pexels_size)){
                            $wpaicg_pexels_size = strtolower($this->wpaicg_pexels_size);
                            if(in_array($wpaicg_pexels_size,array('large','medium','small'))){
                                $wpaicg_pexels_key = $wpaicg_pexels_size;
                            }
                        }
                        if(isset($body['photos'][0]['src'][$wpaicg_pexels_key]) && !empty($body['photos'][0]['src'][$wpaicg_pexels_key])){
                            $wpaicg_result['pexels_response'] = trim($body['photos'][0]['src'][$wpaicg_pexels_key]);

                        }
                        else{
                            $wpaicg_result['status'] = 'no_image';
                            $wpaicg_result['msg'] = esc_html__('No image generated','gpt3-ai-content-generator');
                        }
                    }
                    else{
                        $wpaicg_result['status'] = 'no_image';
                        $wpaicg_result['msg'] = esc_html__('No image generated','gpt3-ai-content-generator');
                    }
                }

            }
            else{
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('Missing Pexels API Setting','gpt3-ai-content-generator');
            }
            return $wpaicg_result;
        }
        public function wpaicg_count_words($text)
        {
            $text = trim(strip_tags(html_entity_decode($text,ENT_QUOTES)));
            $text = preg_replace("/[\n]+/", " ", $text);
            $text = preg_replace("/[\s]+/", "@SEPARATOR@", $text);
            $text_array = explode('@SEPARATOR@', $text);
            $count = count($text_array);
            $last_key = end($text_array);
            if (empty($last_key)) {
                $count--;
            }
            return $count;
        }

        public function wpaicg_image($opts)
        {
            $result = array('status' => 'error');
            $imgresult = $this->openai->image($opts);
            $imgresult = json_decode($imgresult);
            if (isset($imgresult->error)) {
                $result['msg'] = esc_html($imgresult->error->message);
            } else {
                $result['status'] = 'success';
                $result['url'] = $imgresult->data[0]->url;
            }
            return $result;
        }

        public function openai($openai)
        {
            $this->openai = $openai;
        }

        public function wpaicg_request($opts)
        {
            $result = array('status' => 'error','tokens' => 0, 'length' => 0);
            if(!isset($opts['model']) || empty($opts['model'])){
                $opts['model'] = $this->wpaicg_engine;
            }
            $chat_model = false;
            if($opts['model'] == 'gpt-3.5-turbo' || $opts['model'] == 'gpt-4' || $opts['model'] == 'gpt-4-32k'){
                $chat_model = true;
                unset($opts['best_of']);
                $opts['messages'] = array(
                    array('role' => 'user', 'content' => $opts['prompt'])
                );
                unset($opts['prompt']);
                unset($opts['best_of']);
                $complete = $this->openai->chat($opts);
            }
            else{
                $complete = $this->openai->completion($opts);

            }
            $complete = json_decode( $complete );
            if ( isset( $complete->error ) ) {
                $result['msg'] = trim($complete->error->message);
                if(empty($result['msg']) && isset($complete->error->code) && $complete->error->code == 'invalid_api_key'){
                    $result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                }
                if(strpos($result['msg'],'exceeded your current quota') !== false){
                    $result['msg'] .= ' '.esc_html__('Please note that this message is coming from OpenAI and it is not related to our plugin. It means that you do not have enough credit from OpenAI. You can check your usage here: https://platform.openai.com/account/usage','gpt3-ai-content-generator');
                }
            }
            else{
                if(isset($complete->choices) && is_array($complete->choices)) {
                    $result['status'] = 'success';
                    if($chat_model) {
                        $result['tokens'] = $complete->usage->total_tokens;
                        $result['data'] = isset($complete->choices[0]->message->content) ? trim($complete->choices[0]->message->content) : '';
                    }
                    else{
                        $result['tokens'] = $complete->usage->total_tokens;
                        $result['data'] = trim($complete->choices[0]->text);
                    }
                    if(empty($result['data'])){
                        $result['status'] = 'error';
                        $result['msg'] = esc_html__('The model predicted a completion that begins with a stop sequence, resulting in no output. Consider adjusting your prompt or stop sequences.','gpt3-ai-content-generator');
                    }
                    else{
                        $result['length'] = $this->wpaicg_count_words($result['data']);
                    }
                }
                else $result['msg'] = esc_html__('The model predicted a completion that begins with a stop sequence, resulting in no output. Consider adjusting your prompt or stop sequences.','gpt3-ai-content-generator');
            }
            return $result;
        }
    }
    WPAICG_Generator::get_instance();
}
