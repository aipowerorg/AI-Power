<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_custom_models = get_option('wpaicg_custom_models',[]);
$wpaicg_custom_models = array_merge(array('text-davinci-003','gpt-3.5-turbo','text-curie-001','text-babbage-001','text-ada-001','gpt-4','gpt-4-32k'),$wpaicg_custom_models);
$openai = new \WPAICG\WPAICG_OpenAI();
$openai->openai();
$wpaicg_parameters = array(
    'type' => 'topic',
    'post_type' => 'post',
    'model' => get_option('wpaicg_ai_model','gpt-3.5-turbo'),
    'temperature' => $openai->temperature,
    'max_tokens' => 3000,
    'top_p' => $openai->top_p,
    'best_of' => $openai->best_of,
    'frequency_penalty' => $openai->frequency_penalty,
    'presence_penalty' => $openai->presence_penalty,
    'prompt_title' => esc_html__('Suggest [count] title for an article about [topic]','gpt3-ai-content-generator'),
    'prompt_section' => esc_html__('Write [count] consecutive headings for an article about [title]','gpt3-ai-content-generator'),
    'prompt_content' => esc_html__('Write a comprehensive article about [title], covering the following subtopics [sections]. Each subtopic should have at least [count] paragraphs. Use a cohesive structure to ensure smooth transitions between ideas. Include relevant statistics, examples, and quotes to support your arguments and engage the reader.','gpt3-ai-content-generator'),
    'prompt_meta' => esc_html__('Write a meta description about [title]. Max: 155 characters.','gpt3-ai-content-generator'),
    'prompt_excerpt' => esc_html__('Generate an excerpt for [title]. Max: 55 words.','gpt3-ai-content-generator')
);
$wpaicg_all_templates = get_posts(array(
    'post_type' => 'wpaicg_mtemplate',
    'posts_per_page' => -1
));
$wpaicg_templates = array(array(
    'title' => 'Default',
    'content' => $wpaicg_parameters
));
foreach ($wpaicg_all_templates as $wpaicg_all_template){
    $wpaicg_template_content = is_serialized($wpaicg_all_template->post_content) ? unserialize($wpaicg_all_template->post_content) : array();
    $wpaicg_template_content = wp_parse_args($wpaicg_template_content,$wpaicg_parameters);
    $wpaicg_templates[$wpaicg_all_template->ID] = array(
        'title' => $wpaicg_all_template->post_title,
        'content' => $wpaicg_template_content
    );
}
$default_name = '';
if(isset($selected_template) && !empty($selected_template)){
    $wpaicg_parameters = $wpaicg_templates[$selected_template]['content'];
    $default_name = $wpaicg_templates[$selected_template]['title'];
}
?>
<h3>Settings</h3>
<div class="wpaicg-custom-parameters-content">
    <div class="wpaicg-form-field">
        <label><strong><?php echo esc_html__('Template','gpt3-ai-content-generator')?>:</strong></label>
        <select class="wpaicg_custom_template_select regular-text">
            <?php
            foreach ($wpaicg_templates as $key=>$wpaicg_template){
                echo '<option'.(isset($selected_template) && $selected_template == $key ? ' selected':'').' class="wpaicg_custom_template_'.esc_html($key).'" data-parameters="'.esc_html(json_encode($wpaicg_template['content'], JSON_UNESCAPED_UNICODE)).'" value="'.esc_html($key).'">'.esc_html($wpaicg_template['title']).'</option>';
            }
            ?>
        </select>
    </div>
    <div class="wpaicg-form-field">
        <label><strong><?php echo esc_html__('Name','gpt3-ai-content-generator')?>:</strong></label>
        <input value="<?php echo esc_html($default_name)?>" type="text" class="regular-text wpaicg_custom_template_title" name="title" placeholder="Enter a Template Name">
        <?php
        if(isset($selected_template) && !empty($selected_template)){
        ?>
            <input class="wpaicg_custom_template_id" type="hidden" name="id" value="<?php echo esc_html($selected_template)?>">
        <?php
        }
        ?>
    </div>
    <div class="wpaicg-form-field">
        <label><strong><?php echo esc_html__('Post Type','gpt3-ai-content-generator')?>:</strong></label>
        <select name="template[post_type]" class="regular-text wpaicg_custom_template_post_type">
            <option value="post"><?php echo esc_html__('Post','gpt3-ai-content-generator')?></option>
            <option<?php echo isset($wpaicg_parameters['post_type']) && $wpaicg_parameters['post_type'] == 'page' ? ' selected' :''?> value="page"><?php echo esc_html__('Page','gpt3-ai-content-generator')?></option>
        </select>
    </div>
    <div class="wpaicg-form-field">
        <label><strong><?php echo esc_html__('Model','gpt3-ai-content-generator')?>:</strong></label>
        <select name="template[model]" class="regular-text wpaicg_custom_template_model">
            <?php
            foreach($wpaicg_custom_models as $wpaicg_custom_model){
                echo '<option'.($wpaicg_custom_model == $wpaicg_parameters['model'] ? ' selected':'').' value="'.esc_html($wpaicg_custom_model).'">'.esc_html($wpaicg_custom_model).'</option>';
            }
            ?>
        </select>
    </div>
    <div id="gpt4-notice" class="wpaicg-form-field" style="display:none;">
        <p style="color: red;"><?php echo sprintf(esc_html__('This works best with gpt-4 and gpt-3.5-turbo. Please note that GPT-4 is currently in limited beta, which means that access to the GPT-4 API from OpenAI is available only through a waiting list and is not open to everyone yet. You can sign up for the waiting list at %shere%s.','gpt3-ai-content-generator'),'<a href="https://openai.com/waitlist/gpt-4-api" target="_blank">','</a>')?></p>
    </div>
    <?php
    foreach(array('temperature','max_tokens','top_p','best_of','frequency_penalty','presence_penalty') as $item){
        ?>
        <div class="wpaicg-form-field">
            <label><strong><?php echo esc_html(ucwords(str_replace('_',' ',$item))) ?>:</strong></label>
            <input type="text" value="<?php echo esc_html($wpaicg_parameters[$item])?>" class="wpaicg_custom_template_<?php echo esc_html($item)?>" name="template[<?php echo esc_html($item)?>]" style="width: 80px">
        </div>
        <?php
    }
    ?>
    <div class="wpaicg-mb-10">
        <label class="mb-5" style="display: block"><strong><?php echo esc_html__('Prompt for Title','gpt3-ai-content-generator')?>:</strong></label>
        <textarea class="wpaicg_custom_template_prompt_title" name="template[prompt_title]" rows="2"><?php echo esc_html($wpaicg_parameters['prompt_title'])?></textarea>
        <p style="margin-top: 0;font-size: 13px;font-style: italic;"><?php echo sprintf(esc_html__('Ensure %s and %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[count]</code>','<code>[topic]</code>')?></code></p>
    </div>
    <div class="wpaicg-mb-10">
        <label class="mb-5" style="display: block"><strong><?php echo esc_html__('Prompt for Sections','gpt3-ai-content-generator')?>:</strong></label>
        <textarea class="wpaicg_custom_template_prompt_section" name="template[prompt_section]" rows="2"><?php echo esc_html($wpaicg_parameters['prompt_section'])?></textarea>
        <p style="margin-top: 0;font-size: 13px;font-style: italic;"><?php echo sprintf(esc_html__('Ensure %s and %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[count]</code>','<code>[title]</code>')?></code></p>
    </div>
    <div class="wpaicg-mb-10">
        <label class="mb-5" style="display: block"><strong><?php echo esc_html__('Prompt for Content','gpt3-ai-content-generator')?>:</strong></label>
        <textarea class="wpaicg_custom_template_prompt_content" name="template[prompt_content]" rows="5"><?php echo esc_html($wpaicg_parameters['prompt_content'])?></textarea>
        <p style="margin-top: 0;font-size: 13px;font-style: italic;"><?php echo sprintf(esc_html__('Ensure %s, %s and %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[title]</code>','<code>[sections]</code>','<code>[count]</code>')?></code></p>
    </div>
    <div class="wpaicg-mb-10">
        <label class="mb-5" style="display: block"><strong><?php echo esc_html__('Prompt for Excerpt','gpt3-ai-content-generator')?>:</strong></label>
        <textarea class="wpaicg_custom_template_prompt_excerpt" name="template[prompt_excerpt]" rows="2"><?php echo esc_html($wpaicg_parameters['prompt_excerpt'])?></textarea>
        <p style="margin-top: 0;font-size: 13px;font-style: italic;"><?php echo sprintf(esc_html__('Ensure %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[title]</code>')?></code></p>
    </div>
    <div class="wpaicg-mb-10">
        <label class="mb-5" style="display: block"><strong><?php echo esc_html__('Prompt for Meta','gpt3-ai-content-generator')?>:</strong></label>
        <textarea class="wpaicg_custom_template_prompt_meta" name="template[prompt_meta]" rows="2"><?php echo esc_html($wpaicg_parameters['prompt_meta'])?></textarea>
        <p style="margin-top: 0;font-size: 13px;font-style: italic;"><?php echo sprintf(esc_html__('Ensure %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[title]</code>')?></code></p>
    </div>
    <div style="display: flex;justify-content: space-between">
        <div>
            <button style="<?php echo isset($selected_template) ? '' : 'display:none'?>" type="button" class="button button-primary wpaicg_template_update"><?php echo esc_html__('Update','gpt3-ai-content-generator')?></button>
            <button type="button" class="button button-primary wpaicg_template_save"><?php echo esc_html__('Save Template','gpt3-ai-content-generator')?></button>
        </div>
        <button type="button" class="button button-link-delete wpaicg_template_delete" style="<?php echo isset($selected_template) ? '' : 'display:none'?>"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></button>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const modelSelect = document.querySelector('.wpaicg_custom_template_model');
    const gpt4Notice = document.getElementById('gpt4-notice');

    modelSelect.addEventListener('change', function () {
        if (this.value === 'gpt-4' || this.value === 'gpt-4-32k') {
            gpt4Notice.style.display = 'block';
        } else {
            gpt4Notice.style.display = 'none';
        }
    });
});
</script>
