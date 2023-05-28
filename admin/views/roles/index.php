<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$roles = wp_roles();
$success = false;
if(isset($_POST['wpaicg_role_save'])){
    check_admin_referer('wpaicg_role_manager');
    foreach($roles->get_names() as $role => $name){
        if($role !== 'administrator') {
            delete_option('wpaicg_role_' . $role . '_modules');
            $user_role = get_role($role);
            foreach ($this->wpaicg_roles as $key => $wpaicg_role) {
                if(!empty($wpaicg_role['hide'])){
                    $user_role->remove_cap('wpaicg_' . $wpaicg_role['hide']);
                }
                if (isset($wpaicg_role['roles']) && count($wpaicg_role['roles'])) {
                    foreach ($wpaicg_role['roles'] as $key_role => $role_name) {
                        $user_role->remove_cap('wpaicg_' . $key . '_' . $key_role);
                    }
                } else {
                    $user_role->remove_cap('wpaicg_' . $key);
                }
            }
        }
    }
    if(isset($_POST['wpaicgroles'])){
        $wpaicg_roles = \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicgroles']);
        foreach($wpaicg_roles as $role=>$permissions){
            if(is_array($permissions) && count($permissions)){
                $user_role = get_role($role);
                update_option('wpaicg_role_'.$role.'_modules',$permissions);
                foreach($permissions as $permission){
                    $user_role->add_cap($permission);
                }
            }
        }
    }
    $success = true;
}
?>
<style>
    .wpaicg-list-roles{
        margin-bottom: 15px;
    }
    .wpaicg-role-item label{
        display: block;
        padding: 6px 12px;
        background: #fff;
        border-radius: 3px;
        border: 1px solid #dfdfdf;
        margin-bottom: 10px;
    }
    .wpaicg-grid-three{
        grid-row-gap: 0;
    }
    .wpaicg-role-title > div > span{
        font-weight: bold;
        font-size: 15px;
        margin-right: 10px;
    }
    .wpaicg-role-title > div{
        display: flex;
        align-items: center;
    }
    .wpaicg-role-title{
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #dfdfdf;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        padding: 5px 12px;
    }
    .wpaicg_role{
        margin-bottom: 10px;
    }
    .wpaicg_content_toggle{
        color: #007cba;
        cursor: pointer;
    }
    .wpaicg_role_content.wpaicg_expand{
        display: block;
    }
    .wpaicg_role_content{
        display: none;
        padding: 10px;
        border: 1px solid #ccc;
        background: #f7f7f7;
        border-bottom-left-radius: 3px;
        border-bottom-right-radius: 3px;
    }
</style>
<h3><?php echo esc_html__('Role Manager','gpt3-ai-content-generator')?></h3>
<p><?php echo esc_html__('Control which user has access to which options of AI Power','gpt3-ai-content-generator')?></p>
<?php
if($success){
    ?>
    <strong style="color: #00aa00"><?php echo esc_html__('Record updated successfully','gpt3-ai-content-generator')?></strong>
    <?php
}
?>
<form action="" method="post" style="max-width: 1000px">
<?php
wp_nonce_field('wpaicg_role_manager');
$keyx = 0;
foreach($roles->get_names() as $role => $name){
    if($role !== 'administrator'){
        $role_modules = get_option('wpaicg_role_'.$role.'_modules',[]);
    ?>
        <div class="wpaicg_role">
            <div class="wpaicg-role-title">
                <div>
                    <span><?php echo esc_html__($name,'gpt3-ai-content-generator')?></span>
                    <button style="opacity:0" class="button button-small wpaicg_toggle_role" data-target="<?php echo esc_html($role)?>" type="button"><?php echo esc_html__('Toggle All','gpt3-ai-content-generator')?></button>
                </div>
                <span class="wpaicg_content_toggle<?php echo $keyx == 1 ? ' wpaicg_expand':''?>">
                    <?php
                    if($keyx == 1){
                        echo esc_html__('Collapse','gpt3-ai-content-generator');
                    }
                    else{
                        echo esc_html__('Expand','gpt3-ai-content-generator');
                    }
                    ?>
                </span>
            </div>
            <div class="wpaicg_role_content<?php echo $keyx == 1? ' wpaicg_expand':''?>">
                <?php
                foreach($this->wpaicg_roles as $key=>$wpaicg_role){
                    ?>
                    <p style="margin-bottom: 5px">
                        <strong><?php echo esc_html__($wpaicg_role['name'],'gpt3-ai-content-generator')?></strong>
                    </p>
                    <div class="wpaicg-grid-three wpaicg-list-roles">
                    <?php
                    if(isset($wpaicg_role['roles']) && count($wpaicg_role['roles'])){
                        $has_checked = false;
                        foreach($wpaicg_role['roles'] as $key_role => $role_name){
                            if(!$has_checked && in_array('wpaicg_'.$key.'_'.$key_role, $role_modules)){
                                $has_checked = true;
                            }
                            if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro() && ($key_role == 'google-sheets' || $key_role == 'tweet' || $key_role == 'pdf' || $key_role == 'rss' || ($key_role == 'pdf' && $key == 'embeddings'))){
                                ?>
                                <div class="wpaicg-grid-1">
                                    <div class="wpaicg-role-item">
                                        <label><input type="checkbox" disabled>&nbsp;<?php echo esc_html__($role_name['name'],'gpt3-ai-content-generator')?><span style="font-size: 12px;display: inline-block;padding: 0 4px;background: #ffb30a;border-radius: 2px;margin-left: 5px;font-weight: bold;"><?php echo esc_html__('Pro','gpt3-ai-content-generator')?></span></label>
                                    </div>
                                </div>
                                <?php
                            }
                            else{
                            ?>
                            <div class="wpaicg-grid-1">
                                <div class="wpaicg-role-item">
                                    <label><input<?php echo in_array('wpaicg_'.$key.'_'.$key_role, $role_modules) ? ' checked':''?> name="wpaicgroles[<?php echo esc_html($role)?>][]" value="wpaicg_<?php echo esc_html($key)?>_<?php echo esc_html($key_role)?>" class="wpaicg_role_<?php echo esc_html($role)?> wpaicg_role_multi" type="checkbox">&nbsp;<?php echo esc_html__($role_name['name'],'gpt3-ai-content-generator')?></label>
                                </div>
                            </div>
                            <?php
                            }
                        }
                        if(isset($wpaicg_role['hide']) && !empty($wpaicg_role['hide'])){
                            ?>
                            <input<?php echo $has_checked ? '':' disabled'?> type="hidden" name="wpaicgroles[<?php echo esc_html($role)?>][]" class="wpaicg_role_hide" value="wpaicg_<?php echo esc_html($wpaicg_role['hide'])?>">
                            <?php
                        }
                    }
                    else{
                        ?>
                        <div class="wpaicg-grid-1">
                            <div class="wpaicg-role-item">
                                <label><input<?php echo in_array('wpaicg_'.$key, $role_modules) ? ' checked':''?> name="wpaicgroles[<?php echo esc_html($role)?>][]" value="wpaicg_<?php echo esc_html($key)?>" class="wpaicg_role_<?php echo esc_html($role)?>" type="checkbox">&nbsp;<?php echo esc_html__($wpaicg_role['name'],'gpt3-ai-content-generator')?></label>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    <?php
    }
}
?>
    <button name="wpaicg_role_save" class="button button-primary"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
</form>
<script>
    jQuery(document).ready(function ($){
        $('.wpaicg_toggle_role').click(function (){
            let target = $(this).attr('data-target');
            if($(this).hasClass('wpaicg_toggled')){
                $('.wpaicg_role_hide').attr('disabled', 'disabled');
                $('.wpaicg_role_'+target).prop('checked', false);
            }
            else{
                $('.wpaicg_role_hide').removeAttr('disabled');
                $('.wpaicg_role_hide').prop('checked', true);
                $('.wpaicg_role_'+target).prop('checked', true);
            }
            $(this).toggleClass('wpaicg_toggled');
        });
        $('.wpaicg_content_toggle').click(function (){
            if($(this).hasClass('wpaicg_expand')){
                $(this).html('<?php echo esc_html__('Expand','gpt3-ai-content-generator')?>');
                $(this).closest('.wpaicg_role').find('.wpaicg_role_content').removeClass('wpaicg_expand');
                $(this).closest('.wpaicg_role').find('.wpaicg_toggle_role').css('opacity',0);
            }
            else{
                $(this).html('<?php echo esc_html__('Collapse','gpt3-ai-content-generator')?>');
                $(this).closest('.wpaicg_role').find('.wpaicg_toggle_role').css('opacity',1);
                $(this).closest('.wpaicg_role').find('.wpaicg_role_content').addClass('wpaicg_expand');
            }
            $(this).toggleClass('wpaicg_expand');
        });
        $('.wpaicg_role_multi').click(function (){
            let list_roles = $(this).closest('.wpaicg-list-roles');
            let activeOneRole = false;
            list_roles.find('input[type=checkbox]').each(function (idx, item){
                if($(item).prop('checked')){
                    activeOneRole =true;
                }
            });
            if(activeOneRole){
                list_roles.find('.wpaicg_role_hide').removeAttr('disabled');
            }
            else{
                list_roles.find('.wpaicg_role_hide').attr('disabled','disabled');
            }
        })
    })
</script>
