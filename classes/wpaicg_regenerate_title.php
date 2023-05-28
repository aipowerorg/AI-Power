<?php

namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Regenerate_Title')) {
    class WPAICG_Regenerate_Title
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
            add_filter('post_row_actions',[$this,'wpaicg_regenerate_action'],10,2);
            add_filter('page_row_actions',[$this,'wpaicg_regenerate_action'],10,2);
            add_action('admin_footer',[$this,'wpaicg_regenerate_footer']);
            add_action('wp_ajax_wpaicg_regenerate_title',[$this,'wpaicg_regenerate_title']);
            add_action('wp_ajax_wpaicg_regenerate_save',[$this,'wpaicg_regenerate_save']);
        }

        public function wpaicg_regenerate_save()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['title']) && !empty($_POST['title']) && isset($_POST['id']) && !empty($_POST['id'])){
                $id = sanitize_text_field($_POST['id']);
                $title = sanitize_text_field($_POST['title']);
                $check = wp_update_post(array(
                    'ID' => $id,
                    'post_title' => $title
                ));
                if(is_wp_error($check)){
                    $wpaicg_result['msg'] = $check->get_error_message();
                }
                else{
                    $wpaicg_result['status'] = 'success';
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_regenerate_title()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['title']) && !empty($_POST['title'])){
                $title = sanitize_text_field($_POST['title']);
                $open_ai = WPAICG_OpenAI::get_instance()->openai();
                if(!$open_ai){
                    $wpaicg_result['error'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                }
                else{
                    $temperature = floatval( $open_ai->temperature );
                    $max_tokens = intval( $open_ai->max_tokens );
                    $top_p = floatval( $open_ai->top_p );
                    $best_of = intval( $open_ai->best_of );
                    $frequency_penalty = floatval( $open_ai->frequency_penalty );
                    $presence_penalty = floatval( $open_ai->presence_penalty );
                    $wpai_language = sanitize_text_field( $open_ai->wpai_language );
                    if ( empty($wpai_language) ) {
                        $wpai_language = "en";
                    }
                    $wpaicg_language_file = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/languages/' . $wpai_language . '.json';
                    if ( !file_exists( $wpaicg_language_file ) ) {
                        $wpaicg_language_file = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/languages/en.json';
                    }
                    $wpaicg_language_json = file_get_contents( $wpaicg_language_file );
                    $wpaicg_languages = json_decode( $wpaicg_language_json, true );
                    $prompt = isset($wpaicg_languages['regenerate_prompt']) && !empty($wpaicg_languages['regenerate_prompt']) ? $wpaicg_languages['regenerate_prompt'] : 'Suggest me 5 different title for: %s.';
                    $prompt = sprintf($prompt, $title);
                    $wpaicg_ai_model = get_option('wpaicg_ai_model','text-davinci-003');
                    $wpaicg_generator = WPAICG_Generator::get_instance();
                    $wpaicg_generator->openai($open_ai);
                    if($wpaicg_ai_model == 'gpt-3.5-turbo' || $wpaicg_ai_model == 'gpt-4' || $wpaicg_ai_model == 'gpt-4-32k'){
                        $prompt = $wpaicg_languages['fixed_prompt_turbo'].' '.$prompt;
                    }
                    $complete = $wpaicg_generator->wpaicg_request( [
                        'model'             => $wpaicg_ai_model,
                        'prompt'            => $prompt,
                        'temperature'       => $temperature,
                        'max_tokens'        => $max_tokens,
                        'frequency_penalty' => $frequency_penalty,
                        'presence_penalty'  => $presence_penalty,
                        'top_p'             => $top_p,
                        'best_of'           => $best_of,
                        'stop' => '6.'
                    ] );
                    $wpaicg_result['prompt'] = $prompt;
                    if($complete['status'] == 'error'){
                        $wpaicg_result['msg'] = $complete['msg'];
                    }
                    else{
                        $complete = $complete['data'];
                        $complete = trim( $complete );
                        $complete=preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$complete)));
                        $mylist = preg_split( "/\r\n|\n|\r/", $complete );
                        $mylist = preg_replace( '/^\\d+\\.\\s/', '', $mylist );
                        $mylist = preg_replace( '/\\.$/', '', $mylist );
                        if($mylist && is_array($mylist) && count($mylist)){
                            $newlist = array();
                            foreach($mylist as $item){
                                $newlist[] = str_replace('"','',$item);
                            }
                            $wpaicg_result['data'] = $newlist;
                            $wpaicg_result['status'] = 'success';
                        }
                        else{
                            $wpaicg_result['msg'] = esc_html__('No title generated','gpt3-ai-content-generator');
                        }
                    }
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_regenerate_action($actions, $post)
        {
            if(current_user_can('wpaicg_suggester')) {
                $actions['wpaicg_regenerate'] = '<a class="wpaicg_regenerate_title" data-title="' . esc_html($post->post_title) . '" data-id="' . esc_attr($post->ID) . '" href="javascript:void(0)">' .esc_html__('Suggest Title','gpt3-ai-content-generator'). '</a>';
            }
            return $actions;
        }

        public function wpaicg_regenerate_footer()
        {
            ?>
            <script>
                jQuery(document).ready(function ($){
                    var wpaicgRegenerateRunning = false;
                    $('.wpaicg_modal_close').click(function (){
                        $('.wpaicg_modal_content').empty();
                        $('.wpaicg_modal_close').closest('.wpaicg_modal').hide();
                        $('.wpaicg_modal_close').closest('.wpaicg_modal').removeClass('wpaicg-small-modal');
                        $('.wpaicg-overlay').hide();
                        if(wpaicgRegenerateRunning){
                            wpaicgRegenerateRunning.abort();
                        }
                    })
                    function wpaicgLoading(btn){
                        btn.attr('disabled','disabled');
                        if(!btn.find('spinner').length){
                            btn.append('<span class="spinner"></span>');
                        }
                        btn.find('.spinner').css('visibility','unset');
                    }
                    function wpaicgRmLoading(btn){
                        btn.removeAttr('disabled');
                        btn.find('.spinner').remove();
                    }
                    $(document).on('click','.wpaicg_regenerate_save', function (e){
                        var btn = $(e.currentTarget);
                        var title = btn.parent().find('input').val();
                        var id = btn.attr('data-id');
                        if(title === ''){
                            alert('<?php echo esc_html__('Please insert title','gpt3-ai-content-generator')?>');
                        }
                        else{
                            wpaicgRegenerateRunning = $.ajax({
                                url: '<?php echo admin_url('admin-ajax.php')?>',
                                data: {action: 'wpaicg_regenerate_save',title: title, id: id,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                                dataType: 'JSON',
                                type: 'POST',
                                beforeSend: function (){
                                    $('.wpaicg_regenerate_save').attr('disabled','disabled');
                                    wpaicgLoading(btn);
                                },
                                success: function(res){
                                    if(res.status === 'success'){
                                        $('#post-'+id+' .row-title').text(title);
                                        $('.wpaicg_modal_close').click();
                                    }
                                    else{
                                        wpaicgRmLoading(btn);
                                        alert(res.msg);
                                    }
                                },
                                error: function (){
                                    wpaicgRmLoading(btn);
                                    alert('Something went wrong');
                                    $('.wpaicg_regenerate_save').removeAttr('disabled');
                                }
                            })
                        }
                    })
                    $(document).on('click','.wpaicg_regenerate_title', function (e){
                        var btn = $(e.currentTarget);
                        var id = btn.attr('data-id');
                        var title = btn.attr('data-title');
                        if(title === ''){
                            alert('Please update title first');
                        }
                        else{
                            if(wpaicgRegenerateRunning){
                                wpaicgRegenerateRunning.abort();
                            }
                            $('.wpaicg_modal_content').empty();
                            $('.wpaicg-overlay').show();
                            $('.wpaicg_modal').show();
                            $('.wpaicg_modal_title').html('AI Power - <?php echo esc_html__('Title Suggestion Tool','gpt3-ai-content-generator')?>');
                            $('.wpaicg_modal_content').html('<p style="font-style: italic;margin-top: 5px;text-align: center;"><?php echo esc_html__('Preparing suggestions...','gpt3-ai-content-generator')?></p>');
                            wpaicgRegenerateRunning = $.ajax({
                                url: '<?php echo admin_url('admin-ajax.php')?>',
                                data: {action: 'wpaicg_regenerate_title',title: title,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                                dataType: 'JSON',
                                type: 'POST',
                                success: function (res){
                                    if(res.status === 'success'){
                                        var html = '';
                                        if(res.data.length){
                                            $.each(res.data, function (idx, item){
                                                html += '<div class="wpaicg-regenerate-title"><input type="text" value="'+item+'"><button data-id="'+id+'" class="button button-primary wpaicg_regenerate_save"><?php echo esc_html__('Use','gpt3-ai-content-generator')?></button></div>';
                                            })
                                            $('.wpaicg_modal_content').html(html);
                                        }
                                        else{
                                            $('.wpaicg_modal_content').html('<p style="color: #f00;margin-top: 5px;text-align: center;"><?php echo esc_html__('No result','gpt3-ai-content-generator')?></p>');
                                        }
                                    }
                                    else{
                                        $('.wpaicg_modal_content').html('<p style="color: #f00;margin-top: 5px;text-align: center;">'+res.msg+'</p>');
                                    }
                                },
                                error: function (){
                                    $('.wpaicg_modal_content').html('<p style="color: #f00;margin-top: 5px;text-align: center;"><?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?></p>');
                                }
                            })
                        }
                    })
                })
            </script>
            <?php
        }
    }
    WPAICG_Regenerate_Title::get_instance();
}
