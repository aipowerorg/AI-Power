<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$success = false;
if(isset($_POST['wpaicg_save_image_setting'])) {
    check_admin_referer('wpaicg_setting_save');
    if (isset($_POST['wpaicg_limit_price']) && !empty($_POST['wpaicg_limit_price'])) {
        $wpaicg_limit_tokens = \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_limit_price']);
        update_option('wpaicg_limit_tokens_image', $wpaicg_limit_tokens);
    }
    $wpaicg_keys = array(
        'wpaicg_image_surprise_text',
        'wpaicg_image_generate_text',
        'wpaicg_image_view_text',
        'wpaicg_image_save_media_text',
        'wpaicg_image_select_all_text'
    );
    foreach ($wpaicg_keys as $wpaicg_key) {
        if (isset($_POST[$wpaicg_key]) && !empty($_POST[$wpaicg_key])) {
            update_option($wpaicg_key, sanitize_text_field($_POST[$wpaicg_key]));
        }
    }
    if (isset($_POST['wpaicg_image_default_prompts']) && !empty($_POST['wpaicg_image_default_prompts'])) {
        update_option('wpaicg_image_default_prompts', wp_kses_post($_POST['wpaicg_image_default_prompts']));
    } else {
        delete_option('wpaicg_image_default_prompts');
    }
    if (isset($_POST['wpaicg_image_enable_sale']) && !empty($_POST['wpaicg_image_enable_sale'])) {
        update_option('wpaicg_image_enable_sale', sanitize_text_field($_POST['wpaicg_image_enable_sale']));
    } else {
        delete_option('wpaicg_image_enable_sale');
    }
    $success = esc_html__('Records updated successfully','gpt3-ai-content-generator');
}
$wpaicg_settings = get_option('wpaicg_limit_tokens_image',[]);
$wpaicg_image_surprise_text = get_option('wpaicg_image_surprise_text','Surprise Me');
$wpaicg_image_generate_text = get_option('wpaicg_image_generate_text','Generate');
$wpaicg_image_view_text = get_option('wpaicg_image_view_text','View Image');
$wpaicg_image_save_media_text = get_option('wpaicg_image_save_media_text','Save to Media');
$wpaicg_image_select_all_text = get_option('wpaicg_image_select_all_text','Select All');
$wpaicg_image_default_prompts = get_option('wpaicg_image_default_prompts','');
$wpaicg_image_enable_sale = get_option('wpaicg_image_enable_sale',false);
$wpaicg_roles = wp_roles()->get_names();
if($success){
    echo '<h4 id="setting_message" style="color: green;">'.esc_html__('Records successfully updated!','gpt3-ai-content-generator').'</h4>';
}
?>
<h3><?php echo esc_html__('Price Handling','gpt3-ai-content-generator')?></h3>
<form action="" method="post">
    <?php
    wp_nonce_field('wpaicg_setting_save');
    ?>
    <table class="form-table">
        <tr>
            <th><?php echo esc_html__('Limit Registered User','gpt3-ai-content-generator')?>:</th>
            <td>
                <input<?php echo isset($wpaicg_settings['user_limited']) && $wpaicg_settings['user_limited'] ? ' checked':''?> type="checkbox" value="1" class="wpaicg_user_token_limit" name="wpaicg_limit_price[user_limited]">
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Price Limit','gpt3-ai-content-generator')?>:</th>
            <td><input<?php echo isset($wpaicg_settings['user_limited']) && $wpaicg_settings['user_limited'] ? '' :' disabled'?> value="<?php echo isset($wpaicg_settings['user_tokens']) ? esc_html($wpaicg_settings['user_tokens']) : ''?>" style="width: 80px" class="wpaicg_user_token_limit_text" type="text" name="wpaicg_limit_price[user_tokens]"></td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Role based limit','gpt3-ai-content-generator')?>:</th>
            <td>
                <?php
                foreach($wpaicg_roles as $key=>$wpaicg_role){
                    echo '<input value="'.(isset($wpaicg_settings['limited_roles'][$key]) ? $wpaicg_settings['limited_roles'][$key] : '').'" class="wpaicg_role_'.esc_html($key).'" type="hidden" name="wpaicg_limit_price[limited_roles]['.esc_html($key).']">';
                }
                ?>
                <input<?php echo isset($wpaicg_settings['role_limited']) && $wpaicg_settings['role_limited'] ? ' checked':''?> type="checkbox" value="1" class="wpaicg_role_limited" name="wpaicg_limit_price[role_limited]">
                <a href="javascript:void(0)" class="wpaicg_limit_set_role<?php echo (isset($wpaicg_settings['user_limited']) && $wpaicg_settings['user_limited']) || !isset($wpaicg_settings['role_limited']) ? ' disabled': ''?>"><?php echo esc_html__('Set Limit','gpt3-ai-content-generator')?></a>
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Limit Non-Registered User','gpt3-ai-content-generator')?>:</th>
            <td><input<?php echo isset($wpaicg_settings['guest_limited']) && $wpaicg_settings['guest_limited'] ? ' checked':''?> type="checkbox" class="wpaicg_guest_token_limit" value="1" name="wpaicg_limit_price[guest_limited]"></td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Price Limit','gpt3-ai-content-generator')?>:</th>
            <td><input<?php echo isset($wpaicg_settings['guest_limited']) && $wpaicg_settings['guest_limited'] ? '' :' disabled'?> class="wpaicg_guest_token_limit_text" style="width: 80px" type="text" value="<?php echo isset($wpaicg_settings['guest_tokens']) ? esc_html($wpaicg_settings['guest_tokens']) : ''?>" name="wpaicg_limit_price[guest_tokens]"></td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Notice','gpt3-ai-content-generator')?>:</th>
            <td><input type="text" value="<?php echo isset($wpaicg_settings['limited_message']) && !empty($wpaicg_settings['limited_message']) ? esc_html($wpaicg_settings['limited_message']) : esc_html__('You have reached your price limit.','gpt3-ai-content-generator')?>" name="wpaicg_limit_price[limited_message]"></td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Reset Limit','gpt3-ai-content-generator')?>:</th>
            <td>
                <select name="wpaicg_limit_price[reset_limit]">
                    <option value="0"><?php echo esc_html__('Never','gpt3-ai-content-generator')?></option>
                    <option<?php echo isset($wpaicg_settings['reset_limit']) && $wpaicg_settings['reset_limit'] == 1 ? ' selected':''?> value="1"><?php echo esc_html__('1 Day','gpt3-ai-content-generator')?></option>
                    <option<?php echo isset($wpaicg_settings['reset_limit']) && $wpaicg_settings['reset_limit'] == 3 ? ' selected':''?> value="3"><?php echo esc_html__('3 Days','gpt3-ai-content-generator')?></option>
                    <option<?php echo isset($wpaicg_settings['reset_limit']) && $wpaicg_settings['reset_limit'] == 7 ? ' selected':''?> value="7"><?php echo esc_html__('1 Week','gpt3-ai-content-generator')?></option>
                    <option<?php echo isset($wpaicg_settings['reset_limit']) && $wpaicg_settings['reset_limit'] == 14 ? ' selected':''?> value="14"><?php echo esc_html__('2 Weeks','gpt3-ai-content-generator')?></option>
                    <option<?php echo isset($wpaicg_settings['reset_limit']) && $wpaicg_settings['reset_limit'] == 30 ? ' selected':''?> value="30"><?php echo esc_html__('1 Month','gpt3-ai-content-generator')?></option>
                    <option<?php echo isset($wpaicg_settings['reset_limit']) && $wpaicg_settings['reset_limit'] == 60 ? ' selected':''?> value="60"><?php echo esc_html__('2 Months','gpt3-ai-content-generator')?></option>
                    <option<?php echo isset($wpaicg_settings['reset_limit']) && $wpaicg_settings['reset_limit'] == 90 ? ' selected':''?> value="90"><?php echo esc_html__('3 Months','gpt3-ai-content-generator')?></option>
                    <option<?php echo isset($wpaicg_settings['reset_limit']) && $wpaicg_settings['reset_limit'] == 180 ? ' selected':''?> value="180"><?php echo esc_html__('6 Months','gpt3-ai-content-generator')?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Enable Token Sale?','gpt3-ai-content-generator')?></th>
            <td><input<?php echo $wpaicg_image_enable_sale ? ' checked':''?> type="checkbox" class="wpaicg_image_enable_sale" value="1" name="wpaicg_image_enable_sale"></td>
        </tr>
        <tr>
            <th><h3><?php echo esc_html__('Button Customization','gpt3-ai-content-generator')?></h3></th>
        </tr>
        <tr>
            <th><?php echo esc_html__('Surprise','gpt3-ai-content-generator')?></th>
            <td>
                <input type="text" name="wpaicg_image_surprise_text" value="<?php echo esc_html($wpaicg_image_surprise_text)?>">
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Generate','gpt3-ai-content-generator')?></th>
            <td>
                <input type="text" name="wpaicg_image_generate_text" value="<?php echo esc_html($wpaicg_image_generate_text)?>">
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('View Image','gpt3-ai-content-generator')?></th>
            <td>
                <input type="text" name="wpaicg_image_view_text" value="<?php echo esc_html($wpaicg_image_view_text)?>">
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Save to Media','gpt3-ai-content-generator')?></th>
            <td>
                <input type="text" name="wpaicg_image_save_media_text" value="<?php echo esc_html($wpaicg_image_save_media_text)?>">
            </td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Select All','gpt3-ai-content-generator')?></th>
            <td>
                <input type="text" name="wpaicg_image_select_all_text" value="<?php echo esc_html($wpaicg_image_select_all_text)?>">
            </td>
        </tr>
        <tr>
            <th><h3><?php echo esc_html__('Surprise Me Prompts','gpt3-ai-content-generator')?></h3></th>
        </tr>
        <tr>
            <th>Prompts</th>
            <td>
                <textarea rows="8" name="wpaicg_image_default_prompts"><?php echo esc_html(str_replace("\\",'',$wpaicg_image_default_prompts))?></textarea>
                <span style="font-style: italic"><?php echo esc_html__('List each prompt on a separate line. When the user selects the "Surprise Me" button, these prompts will appear randomly. If left empty, default prompts will be used.','gpt3-ai-content-generator')?></span>
            </td>
        </tr>
        <tr>
            <th></th>
            <td><button class="button button-primary" name="wpaicg_save_image_setting"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button></td>
        </tr>
    </table>
</form>
<script>
    jQuery(document).ready(function ($){
        let wpaicg_roles = <?php echo wp_kses_post(json_encode($wpaicg_roles))?>;
        $(document).on('keypress','.wpaicg_user_token_limit_text,.wpaicg_update_role_limit,.wpaicg_guest_token_limit_text', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
        });
        $('.wpaicg_modal_close_second').click(function (){
            $('.wpaicg_modal_close_second').closest('.wpaicg_modal_second').hide();
            $('.wpaicg-overlay-second').hide();
        });
        $('.wpaicg_limit_set_role').click(function (){
            if(!$(this).hasClass('disabled')) {
                if ($('.wpaicg_role_limited').prop('checked')) {
                    let html = '';
                    $.each(wpaicg_roles, function (key, role) {
                        let valueRole = $('.wpaicg_role_'+key).val();
                        html += '<div style="padding: 5px;display: flex;justify-content: space-between;align-items: center;"><label><strong>'+role+'</strong></label><input class="wpaicg_update_role_limit" data-target="'+key+'" value="'+valueRole+'" placeholder="<?php echo esc_html__('Empty for no-limit','gpt3-ai-content-generator')?>" type="text"></div>';
                    });
                    html += '<div style="padding: 5px"><button class="button button-primary wpaicg_save_role_limit" style="width: 100%;margin: 5px 0;"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button></div>';
                    $('.wpaicg_modal_title_second').html('<?php echo esc_html__('Role Limit','gpt3-ai-content-generator')?>');
                    $('.wpaicg_modal_content_second').html(html);
                    $('.wpaicg-overlay-second').css('display','flex');
                    $('.wpaicg_modal_second').show();

                } else {
                    $.each(wpaicg_roles, function (key, role) {
                        $('.wpaicg_role_' + key).val('');
                    })
                }
            }
        });
        $(document).on('click','.wpaicg_save_role_limit', function (e){
            $('.wpaicg_update_role_limit').each(function (idx, item){
                let input = $(item);
                let target = input.attr('data-target');
                $('.wpaicg_role_'+target).val(input.val());
            });
            $('.wpaicg_modal_close_second').closest('.wpaicg_modal_second').hide();
            $('.wpaicg-overlay-second').hide();
        });
        $('.wpaicg_guest_token_limit').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_guest_token_limit_text').removeAttr('disabled');
            }
            else{
                $('.wpaicg_guest_token_limit_text').val('');
                $('.wpaicg_guest_token_limit_text').attr('disabled','disabled');
            }
        });
        $('.wpaicg_role_limited').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_user_token_limit').prop('checked',false);
                $('.wpaicg_user_token_limit_text').attr('disabled','disabled');
                $('.wpaicg_limit_set_role').removeClass('disabled');
            }
            else{
                $('.wpaicg_limit_set_role').addClass('disabled');
            }
        });
        $('.wpaicg_user_token_limit').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_user_token_limit_text').removeAttr('disabled');
                $('.wpaicg_role_limited').prop('checked',false);
                $('.wpaicg_limit_set_role').addClass('disabled');
            }
            else{
                $('.wpaicg_user_token_limit_text').val('');
                $('.wpaicg_user_token_limit_text').attr('disabled','disabled');
            }
        });
    })
</script>
