<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$success_save = false;
if(isset($_POST['save_bulk_setting'])) {
    // Verify nonce
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'save_bulk_setting_nonce')) {
        die(WPAICG_NONCE_ERROR);
    }
    if (isset($_POST['wpaicg_restart_queue']) && !empty($_POST['wpaicg_restart_queue'])) {
        update_option('wpaicg_restart_queue', sanitize_text_field($_POST['wpaicg_restart_queue']));
    } else {
        delete_option('wpaicg_restart_queue');
    }
    if (isset($_POST['wpaicg_custom_prompt_enable']) && !empty($_POST['wpaicg_custom_prompt_enable'])) {
        update_option('wpaicg_custom_prompt_enable', 1);
    } else {
        delete_option('wpaicg_custom_prompt_enable');
    }
    if (isset($_POST['wpaicg_rss_new_title']) && !empty($_POST['wpaicg_rss_new_title'])) {
        update_option('wpaicg_rss_new_title', 1);
    } else {
        delete_option('wpaicg_rss_new_title');
    }
    if (isset($_POST['wpaicg_custom_prompt_auto']) && !empty($_POST['wpaicg_custom_prompt_auto'])) {
        update_option('wpaicg_custom_prompt_auto', wp_kses_post($_POST['wpaicg_custom_prompt_auto']));
    } else {
        delete_option('wpaicg_custom_prompt_auto');
    }
    if (isset($_POST['wpaicg_try_queue']) && !empty($_POST['wpaicg_try_queue'])) {
        update_option('wpaicg_try_queue', sanitize_text_field($_POST['wpaicg_try_queue']));
    } else {
        delete_option('wpaicg_try_queue');
    }
    $success_save = true;
}
$wpaicg_restart_queue = get_option('wpaicg_restart_queue', 20);
$wpaicg_try_queue = get_option('wpaicg_try_queue', '');
$wpaicg_ai_model = get_option('wpaicg_ai_model','');
$wpaicg_custom_prompt_enable = get_option('wpaicg_custom_prompt_enable',false);
$wpaicg_default_custom_prompt = 'Create a compelling and well-researched article of at least 500 words on the topic of "[title]" in English. Structure the article with clear headings enclosed within the appropriate heading tags (e.g., <h1>, <h2>, etc.) and engaging subheadings. Ensure that the content is informative and provides valuable insights to the reader. Incorporate relevant examples, case studies, and statistics to support your points. Organize your ideas using unordered lists with <ul> and <li> tags where appropriate. Conclude with a strong summary that ties together the key takeaways of the article. Remember to enclose headings in the specified heading tags to make parsing the content easier. Additionally, wrap even paragraphs in <p> tags for improved readability.';
$wpaicg_custom_prompt_auto = get_option('wpaicg_custom_prompt_auto',$wpaicg_default_custom_prompt);
$wpaicg_rss_new_title = get_option('wpaicg_rss_new_title',false);
?>
<?php
if($success_save){
    echo '<p style="font-weight: bold; color: #00aa00">Record updated successfully</p>';
}
?>
<form action="" method="post" class="wpaicg_auto_settings">
    <?php wp_nonce_field('save_bulk_setting_nonce'); ?>
    <table class="form-table">
        <tr>
            <td colspan="2" style="padding: 0">
                <h1 style="font-size: 20px"><strong><?php echo esc_html__('Queue','gpt3-ai-content-generator')?></strong></h1>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Restart Failed Jobs After','gpt3-ai-content-generator')?></th>
            <td>
                <select name="wpaicg_restart_queue">
                    <option value=""><?php echo esc_html__('Do not Restart','gpt3-ai-content-generator')?></option>
                    <?php
                    for($i = 20; $i <=60; $i+=10){
                        echo '<option'.($wpaicg_restart_queue == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                    }
                    ?>
                </select>
                <?php echo esc_html__('minutes','gpt3-ai-content-generator')?>
                <a href="https://docs.aipower.org/docs/AutoGPT/auto-content-writer/bulk-editor#auto-restart-failed-jobs" target="_blank">?</a>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Attempt up to a maximum of','gpt3-ai-content-generator')?></th>
            <td>
                <select name="wpaicg_try_queue">
                    <?php
                    for($i = 1; $i <=10; $i++){
                        echo '<option'.($wpaicg_try_queue == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                    }
                    ?>
                </select>
                <?php echo esc_html__('times','gpt3-ai-content-generator')?>
                <a href="https://docs.aipower.org/docs/AutoGPT/auto-content-writer/bulk-editor#auto-restart-failed-jobs" target="_blank">?</a>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 0">
                <h1 style="font-size: 20px"><strong><?php echo esc_html__('Content Generation','gpt3-ai-content-generator')?></strong></h1>
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Enable Custom Prompt','gpt3-ai-content-generator')?></th>
            <td>
                <label><input<?php echo $wpaicg_custom_prompt_enable ? ' checked':''?> class="wpaicg_custom_prompt_enable" type="checkbox" value="1" name="wpaicg_custom_prompt_enable"></label>
                <a href="https://docs.aipower.org/docs/AutoGPT/auto-content-writer/bulk-editor#using-custom-prompt" target="_blank">?</a>
                <div style="<?php echo $wpaicg_custom_prompt_enable ? '' : 'display:none'?>" class="wpaicg_custom_prompt_guide">
                    <h3><?php echo esc_html__('Best Practices and Tips','gpt3-ai-content-generator')?></h3>
                    <ol>
                        <?php
                        if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                        ?>
                        <li><?php echo sprintf(esc_html__('Make sure to include %s in your prompt. You can also incorporate %s and %s to further customize your prompt.','gpt3-ai-content-generator'),'<code>[title]</code>','<code>[keywords_to_include]</code>','<code>[keywords_to_avoid]</code>')?></li>
                        <?php
                        else:
                        ?>
                        <li><?php echo sprintf(esc_html__('Ensure %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[title]</code>')?></li>
                        <?php
                        endif;
                        ?>
                        <li><?php echo esc_html__('You can add your language to the prompt. Just replace "in English" with your language.','gpt3-ai-content-generator')?></li>
                        <li><?php echo sprintf(esc_html__('This works best with gpt-4 and gpt-3.5-turbo. Please note that GPT-4 is currently in limited beta, which means that access to the GPT-4 API from OpenAI is available only through a waiting list and is not open to everyone yet. You can sign up for the waiting list at %shere%s.','gpt3-ai-content-generator'),'<a href="https://openai.com/waitlist/gpt-4-api" target="_blank">','</a>')?></li>
                        <li><?php echo esc_html__('Please note that if custom prompt is enabled the plugin will bypass language, style, tone etc settings. You need to specify them in your prompt.','gpt3-ai-content-generator')?></li>
                        </ol>
                </div>
            </td>
        </tr>
        <tr style="<?php echo $wpaicg_custom_prompt_enable ? '' : 'display:none'?>" class="wpaicg_custom_prompt_auto">
            <th><?php echo esc_html__('Custom Prompt','gpt3-ai-content-generator')?></th>
            <td>
                <textarea rows="20" class="wpaicg_custom_prompt_auto_text" name="wpaicg_custom_prompt_auto"><?php echo esc_html(str_replace("\\",'',$wpaicg_custom_prompt_auto))?></textarea>
                <div style="font-style: italic;display: flex; justify-content: space-between">
                    <?php
                    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                    ?>
                    <div><?php echo sprintf(esc_html__('Make sure to include %s in your prompt. You can also incorporate %s and %s to further customize your prompt.','gpt3-ai-content-generator'),'<code>[title]</code>','<code>[keywords_to_include]</code>','<code>[keywords_to_avoid]</code>')?></div>
                    <?php
                    else:
                    ?>
                    <div><?php echo sprintf(esc_html__('Ensure %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[title]</code>')?></div>
                    <?php
                    endif;
                    ?>
                    <button style="color: #fff;background: #df0707;border-color: #df0707;" data-prompt="<?php echo esc_html($wpaicg_default_custom_prompt)?>" class="button wpaicg_custom_prompt_reset" type="button"><?php echo esc_html__('Reset','gpt3-ai-content-generator')?></button>
                </div>
                <div class="wpaicg_custom_prompt_auto_error"></div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 0">
                <h1 style="font-size: 20px"><strong><?php echo esc_html__('RSS','gpt3-ai-content-generator')?></strong></h1>
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Generate New Title','gpt3-ai-content-generator')?></th>
            <td>
                <label><input<?php echo \WPAICG\wpaicg_util_core()->wpaicg_is_pro() ? '' : ' disabled'?><?php echo \WPAICG\wpaicg_util_core()->wpaicg_is_pro() && $wpaicg_rss_new_title ? ' checked':''?> class="wpaicg_rss_new_title" type="checkbox" value="1" name="wpaicg_rss_new_title"><?php echo !\WPAICG\wpaicg_util_core()->wpaicg_is_pro() ? esc_html__('Available in Pro','gpt3-ai-content-generator') : ''?></label>
                <a href="https://docs.aipower.org/docs/AutoGPT/auto-content-writer/rss#generate-new-title" target="_blank">?</a>
            </td>
        </tr>
    </table>
    <button class="button-primary button wpaicg_auto_settings_save" name="save_bulk_setting"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
</form>
<script>
    jQuery(document).ready(function ($){
        let wpaicg_ai_model = '<?php echo esc_html($wpaicg_ai_model)?>';
        $('.wpaicg_custom_prompt_enable').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_custom_prompt_auto').show();
                $('.wpaicg_custom_prompt_guide').show();
            }
            else{
                $('.wpaicg_custom_prompt_auto').hide();
                $('.wpaicg_custom_prompt_guide').hide();
            }
        });
        <?php
        if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
        ?>
        $('.wpaicg_custom_prompt_auto_text').on('input', function (e){
            let prompt = $(e.currentTarget).val();
            if(prompt.indexOf('[keywords_to_include]') > -1 || prompt.indexOf('[keywords_to_avoid]') > -1){
                $('.wpaicg_custom_prompt_auto_error').html('<div style="color: #f00"><?php echo esc_html__('Please note that keywords are only available in pro plan. Please remove keywords from your prompt','gpt3-ai-content-generator')?></div>');
                $('.wpaicg_auto_settings_save').attr('disabled','disabled');
            }
            else{
                $('.wpaicg_custom_prompt_auto_error').empty();
                $('.wpaicg_auto_settings_save').removeAttr('disabled');
            }
        });
        <?php
        endif;
        ?>
        $('.wpaicg_custom_prompt_reset').click(function (){
            let prompt = $(this).attr('data-prompt');
            $('textarea[name=wpaicg_custom_prompt_auto]').val(prompt);
            $('.wpaicg_custom_prompt_auto_error').empty();
            $('.wpaicg_auto_settings_save').removeAttr('disabled');
        });
    })
</script>
