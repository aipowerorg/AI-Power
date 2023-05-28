<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Comment')) {
    class WPAICG_Comment
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
            add_filter('comment_row_actions',[$this,'row_action'],10,2);
            add_action('admin_footer',[$this,'scripts']);
            add_action('wp_ajax_wpaicg_comment_replier', [$this,'wpaicg_comment_replier']);
        }

        public function row_action($actions,$post)
        {
            if(current_user_can('wpaicg_comment_reply')) {
                $actions['wpaicg_comment_replier_box'] = sprintf('<a class="wpaicg_comment_replier" href="javascript:void(0)" data-id="%s">%s</a>',
                esc_attr($post->comment_ID),
                esc_html__('Generate Reply','gpt3-ai-content-generator'));
            }
            return $actions;
        }

        public function scripts()
        {
            if(current_user_can('wpaicg_comment_reply')) {
                ?>
                <script>
                    jQuery(document).ready(function ($){
                        if($('#reviews-filter').length){
                            $('table.product-reviews tr.comment').each(function (idx, item){
                                let id = $(item).find('.check-column input[type=checkbox]').val();
                                $(item).find('.has-row-actions .row-actions').append(' | <span class="wpaicg_comment_replier_box"><a class="wpaicg_comment_replier" href="javascript:void(0)" data-id="'+id+'"><?php echo esc_html__('Generate Reply','gpt3-ai-content-generator')?></a></span>')
                            })
                        }
                        var wpaicgGeneratorCommentWorking = false;
                        $(document).on('click','.wpaicg_comment_replier', function (e){
                            var btn = $(e.currentTarget);
                            if(wpaicgGeneratorCommentWorking){
                                alert('<?php echo esc_html__('Please wait previous ajax request finished.','gpt3-ai-content-generator')?>');
                            }
                            else
                            {
                                var id = btn.attr('data-id');
                                if (id === '') {
                                    alert('<?php echo esc_html__('Can not find ID of comment or review.', 'gpt3-ai-content-generator')?>');
                                } else {
                                    wpaicgGeneratorCommentWorking = $.ajax({
                                        url: '<?php echo admin_url('admin-ajax.php')?>',
                                        data: {action: 'wpaicg_comment_replier',id: id,_wpnonce: '<?php echo wp_create_nonce('wpaicg_comment_replier')?>'},
                                        type: 'POST',
                                        dataType: 'JSON',
                                        beforeSend: function (){
                                            btn.html('<?php echo esc_html__('Generating Reply.. Please wait','gpt3-ai-content-generator')?>')
                                        },
                                        success: function (res){
                                            btn.html('<?php echo esc_html__('Generate Reply','gpt3-ai-content-generator')?>');
                                            wpaicgGeneratorCommentWorking = false;
                                            if(res.status === 'success'){
                                                btn.closest('.row-actions').find('.reply button').click();
                                                $('#replycontainer .wp-editor-area').val(res.data);
                                            }
                                            else{
                                                alert(res.msg);
                                            }
                                        },
                                        error: function (){
                                            wpaicgGeneratorCommentWorking = false;
                                        }
                                    })
                                }
                            }
                        })
                    })
                </script>
                <?php
            }
        }

        public function wpaicg_comment_replier()
        {
            global $wpdb;
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Missing parameters in request','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wpaicg_comment_replier' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
                exit;
            }
            $wpaicg_generator = WPAICG_Generator::get_instance();
            $openai = WPAICG_OpenAI::get_instance()->openai();
            if(!$openai){
                $wpaicg_result['msg'] = esc_html__('Missing OpenAI API Settings','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
                exit;
            }
            $wpaicg_generator->openai($openai);
            if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                $commentID = sanitize_text_field($_REQUEST['id']);
                $comment = get_comment($commentID);
                if($comment){
                    $post = get_post($comment->comment_post_ID);
                    if($post) {
                        $default_prompt = "Please generate a relevant and thoughtful response to [username]'s comment on the post titled '[post_title]' with the excerpt '[post_excerpt]'. The user's latest comment is: '[last_comment]'. If applicable, consider the context of the previous conversation: '[parent_comments]'. Keep the response focused on the topic and avoid creating any new information.";
                        $wpaicg_comment_prompt = get_option('wpaicg_comment_prompt',$default_prompt);
                        $prompt = str_replace('[post_title]', $post->post_title, $wpaicg_comment_prompt);
                        $prompt = str_replace('[post_excerpt]', $post->post_excerpt, $prompt);
                        $prompt = str_replace('[username]', $comment->comment_author, $prompt);
                        $prompt = str_replace('[last_comment]', $comment->comment_content, $prompt);
                        $totalWords = $wpaicg_generator->wpaicg_count_words($prompt);
                        $prompts_comments = array();
                        $prompts_final_comments = array();
                        $parent_comments = array();
                        if ($comment->comment_parent > 0) {
                            $parentComments = $this->wpaicg_comments($comment->comment_parent, array());
                            if ($parentComments && is_array($parentComments) && count($parentComments)) {
                                foreach ($parentComments as $item) {
                                    $prompts_comments[] = $item->comment_author.': '.$item->comment_content;
                                }
                            }
                        }
                        foreach($prompts_comments as $prompts_comment){
                            $comment_word_count = $wpaicg_generator->wpaicg_count_words($prompts_comment);
                            $totalWords += $comment_word_count;
                            if($totalWords > 1500){
                                break;
                            }
                            else{
                                $prompts_final_comments[] = $prompts_comment;
                            }
                        }
                        if(count($prompts_final_comments)){
                            foreach(array_reverse($prompts_final_comments) as $prompts_final_comment){
                                $parent_comments[] = $prompts_final_comment;
                            }
                        }
                        if(count($parent_comments)){
                            $parent_comments = implode("\n",$parent_comments);
                            $prompt = str_replace('[parent_comments]',$parent_comments,$prompt);
                        }
                        $result = $wpaicg_generator->wpaicg_request(array(
                            'model' => 'gpt-3.5-turbo',
                            'prompt' => $prompt,
                            'temperature' => 0.7,
                            'max_tokens' => 1000,
                            'frequency_penalty' => 0.01,
                            'presence_penalty' => 0.01,
                        ));
                        if($result['status'] == 'error'){
                            $wpaicg_result['msg'] = $result['msg'];
                        }
                        else{
                            $wpaicg_result['data'] = $result['data'];
                            $wpaicg_result['prompt'] = $prompt;
                            $wpaicg_result['status'] = 'success';
                        }
                    }
                    else{
                        $wpaicg_result['msg'] = esc_html__('Data not found or deleted','gpt3-ai-content-generator');
                    }
                }
                else $wpaicg_result['msg'] = esc_html__('Comment not found','gpt3-ai-content-generator');
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_comments($id, $comments)
        {
            $comment = get_comment($id);
            if($comment){
                $comments[] = $comment;
                if($comment->comment_parent > 0){
                    $comments = $this->wpaicg_comments($comment->comment_parent,$comments);
                }
            }
            return $comments;
        }
    }

    WPAICG_Comment::get_instance();
}
