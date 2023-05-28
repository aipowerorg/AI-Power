<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_default_comment_prompt = "Please generate a relevant and thoughtful response to [username]'s comment on the post titled '[post_title]' with the excerpt '[post_excerpt]'. The user's latest comment is: '[last_comment]'. If applicable, consider the context of the previous conversation: '[parent_comments]'. Keep the response focused on the topic and avoid creating any new information.";
$wpaicg_comment_prompt = get_option('wpaicg_comment_prompt',$wpaicg_default_comment_prompt);
?>
<div id="tabs-10">
    <div class="wpcgai_form_row" style="display: flex;align-items: flex-start;">
        <label class="wpcgai_label"><?php echo esc_html__('Prompt for Comment Replier','gpt3-ai-content-generator')?>:</label>
        <div style="width: 65%;">
            <textarea rows="10" type="text" name="wpaicg_comment_prompt"><?php echo esc_html(str_replace("\\",'',$wpaicg_comment_prompt));?></textarea>
            <p><?php echo sprintf(esc_html__('Ensure %s and %s and %s and %s and %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[username]</code>','<code>[post_title]</code>','<code>[post_excerpt]</code>','<code>[last_comment]</code>','<code>[parent_comments]</code>')?></p>
            <!-- read documentation here -->
            <p><?php echo sprintf(esc_html__('Read the %s for more information.','gpt3-ai-content-generator'),'<a href="https://aipower.org/comment-replier-gpt-powered-engagement-assistant-for-wordpress/" target="_blank">'.esc_html__('documentation','gpt3-ai-content-generator').'</a>')?></p>
        </div>
    </div>
</div>
