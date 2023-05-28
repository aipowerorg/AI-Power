<?php
$wpaicg_editor_button_menus = get_option('wpaicg_editor_button_menus', []);
$wpaicg_editor_change_action = get_option('wpaicg_editor_change_action', 'below');
if(!is_array($wpaicg_editor_button_menus) || count($wpaicg_editor_button_menus) == 0){
    $wpaicg_editor_button_menus = \WPAICG\WPAICG_Editor::get_instance()->wpaicg_edit_default_menus;
}
?>
<style>
    .wpaicg_editor_menu{
        display: flex;
        justify-content: space-between;
        position: relative;
        background: #d7d7d7;
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 3px;
    }
    .wpaicg_editor_menu > div{
        width: 48%;
    }
    .wpaicg_editor_menu label{
        display: block;
        font-weight: bold;
    }
    .wpaicg_editor_menu input{
        width: 100%
    }
    .wpaicg_editor_menu_close{
        position: absolute;
        top: 2px;
        right: 2px;
        width: 20px;
        height: 20px;
        border-radius: 2px;
        background: #c70000;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 20px;
        color: #fff;
        cursor: pointer;
    }
    .wpaicg_editor_menu_help{
        font-size: 12px;
        font-style: italic;
    }
    .wpaicg_editor_add_menu{
        display: block!important;
        margin-bottom: 10px!important;
        width: 100%;
    }
</style>
<div id="tabs-9">
    <p><?php echo esc_html__('AI Assistant is a feature that allows you to add a button to the WordPress editor that will help you to create content. You can add your own menus with your own prompts.','gpt3-ai-content-generator')?></p>
    <p><?php echo esc_html__('AI Assistant is compatible with both Gutenberg and Classic Editor.','gpt3-ai-content-generator')?></p>
    <p><?php echo esc_html__('Use the form below to add, modify, or remove menus as needed.','gpt3-ai-content-generator')?></p>
    <p><?php echo sprintf(esc_html__('For more information, please see %s.','gpt3-ai-content-generator'),'<a href="https://docs.aipower.org/docs/content-writer/ai-assistant" target="_blank">'.esc_html__('this documentation','gpt3-ai-content-generator').'</a>')?></p>
    <div class="wpaicg_editor_menus">
        <?php
        if($wpaicg_editor_button_menus && is_array($wpaicg_editor_button_menus) && count($wpaicg_editor_button_menus)){
            $key = 0;
            foreach ($wpaicg_editor_button_menus as $wpaicg_editor_button_menu){
                if(isset($wpaicg_editor_button_menu['name']) && isset($wpaicg_editor_button_menu['prompt']) && $wpaicg_editor_button_menu['name'] != '' && $wpaicg_editor_button_menu['prompt'] != ''){
                ?>
                <div class="wpaicg_editor_menu">
                    <span class="wpaicg_editor_menu_close">&times;</span>
                    <div>
                        <label><?php echo esc_html__('Menu Name','gpt3-ai-content-generator')?></label>
                        <input name="wpaicg_editor_button_menus[<?php echo esc_html($key)?>][name]" class="wpaicg_editor_menu_name" type="text" value="<?php echo esc_html($wpaicg_editor_button_menu['name'])?>">
                    </div>
                    <div>
                        <label><?php echo esc_html__('Prompt','gpt3-ai-content-generator')?></label>
                        <input name="wpaicg_editor_button_menus[<?php echo esc_html($key)?>][prompt]" class="wpaicg_editor_menu_prompt" type="text" value="<?php echo esc_html($wpaicg_editor_button_menu['prompt'])?>">
                        <span class="wpaicg_editor_menu_help"><?php echo sprintf(esc_html__('Ensure %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[text]</code>')?></span>
                    </div>
                </div>
                <?php
                    $key++;
                }
            }
        }
        ?>
    </div>
    <button class="button button-primary wpaicg_editor_add_menu" type="button"><?php echo esc_html__('Add More','gpt3-ai-content-generator')?></button>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Content Position','gpt3-ai-content-generator')?></label>
        <select class="regular-text" name="wpaicg_editor_change_action">
            <option<?php echo $wpaicg_editor_change_action == 'below' ? ' selected':'';?> value="below"><?php echo esc_html__('Below','gpt3-ai-content-generator')?></option>
            <option<?php echo $wpaicg_editor_change_action == 'above' ? ' selected':'';?> value="above"><?php echo esc_html__('Above','gpt3-ai-content-generator')?></option>
        </select>
    </div>
</div>
<script>
    jQuery(document).ready(function ($){
        $(document).on('click','.wpaicg_editor_menu_close', function (e){
            $(e.currentTarget).closest('.wpaicg_editor_menu').remove();
            wpaicgSortMenu();
        });
        function wpaicgSortMenu(){
            $('.wpaicg_editor_menu').each(function (idx, item){
                $(item).find('.wpaicg_editor_menu_name').attr('name','wpaicg_editor_button_menus['+idx+'][name]');
                $(item).find('.wpaicg_editor_menu_prompt').attr('name','wpaicg_editor_button_menus['+idx+'][prompt]');
            })
        }
        $('.wpaicg_editor_add_menu').click(function (){
            let html = '<div class="wpaicg_editor_menu">';
            html += '<span class="wpaicg_editor_menu_close">&times;</span>';
            html += '<div>';
            html += '<label><?php echo esc_html__('Menu Name','gpt3-ai-content-generator')?></label>';
            html += '<input class="wpaicg_editor_menu_name" type="text">';
            html += '</div><div>';
            html += '<label><?php echo esc_html__('Prompt','gpt3-ai-content-generator')?></label>';
            html += '<input class="wpaicg_editor_menu_prompt" type="text">';
            html += '<span class="wpaicg_editor_menu_help"><?php echo sprintf(esc_html__('Ensure %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[text]</code>')?></span>';
            html += '</div></div>';
            $('.wpaicg_editor_menus').append(html);
            wpaicgSortMenu();
        });
    })
</script>
