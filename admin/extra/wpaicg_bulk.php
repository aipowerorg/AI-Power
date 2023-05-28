<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if(isset($_POST['wpaicg_confirmed_cron'])){
    update_option('_wpaicg_crojob_bulk_confirm','true');
}
$wpaicg_track_id = isset($_GET['wpaicg_track']) && !empty($_GET['wpaicg_track']) ? sanitize_text_field($_GET['wpaicg_track']) : false;
$wpaicg_bulk_action = isset($_GET['wpaicg_action']) && !empty($_GET['wpaicg_action']) ? sanitize_text_field($_GET['wpaicg_action']) : false;
$checkRole = \WPAICG\wpaicg_roles()->user_can('wpaicg_bulk_content',empty($wpaicg_bulk_action) ? 'bulk' : $wpaicg_bulk_action,'wpaicg_action');
if($checkRole){
    echo '<script>window.location.href="'.$checkRole.'"</script>';
    exit;
}
$wpaicg_track = false;
if($wpaicg_track_id){
    $wpaicg_track = get_post($wpaicg_track_id);
}
$wpaicg_cron_job_last_time = get_option('_wpaicg_crojob_bulk_last_time','');
$wpaicg_cron_job_confirm = get_option('_wpaicg_crojob_bulk_confirm','');
$wpaicg_number_title = $this->wpaicg_limit_titles;
$wpaicg_cron_added = get_option('_wpaicg_cron_added','');
?>
<style>
.wpaicg_notice_text_rw {
    padding: 10px;
    background-color: #F8DC6F;
    text-align: left;
    margin-bottom: 12px;
    color: #000;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
}
</style>
<p class="wpaicg_notice_text_rw"><?php echo sprintf(esc_html__('Love our plugin? Support us with a quick review: %s[Write a Review]%s - Thank you! ❤️ 😊','gpt3-ai-content-generator'),'<a href="https://wordpress.org/support/plugin/gpt3-ai-content-generator/reviews/#new-post" target="_blank">','</a>')?></p>
<div class="wrap fs-section">
    <h2 class="nav-tab-wrapper">
        <?php
        if(current_user_can('wpaicg_bulk_content_bulk')):
        ?>
        <a href="<?php echo admin_url('admin.php?page=wpaicg_bulk_content')?>" class="nav-tab<?php echo !$wpaicg_track && !$wpaicg_bulk_action ? ' nav-tab-active' : ''?>"><?php echo esc_html__('Dashboard','gpt3-ai-content-generator')?></a>
        <?php
        endif;
        ?>
        <?php
        if(current_user_can('wpaicg_bulk_content_editor')):
        ?>
        <a href="<?php echo admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=editor')?>" class="nav-tab<?php echo $wpaicg_bulk_action == 'editor' ? ' nav-tab-active' : ''?>"><?php echo esc_html__('Bulk Editor','gpt3-ai-content-generator')?></a>
        <?php
        endif;
        ?>
        <?php
        if(current_user_can('wpaicg_bulk_content_csv')):
        ?>
        <a href="<?php echo admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=csv')?>" class="nav-tab<?php echo $wpaicg_bulk_action == 'csv' ? ' nav-tab-active' : ''?>"><?php echo esc_html__('CSV','gpt3-ai-content-generator')?></a>
        <?php
        endif;
        ?>
        <?php
        if(current_user_can('wpaicg_bulk_content_copy-paste')):
        ?>
        <a href="<?php echo admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=copy-paste')?>" class="nav-tab<?php echo $wpaicg_bulk_action == 'copy-paste' ? ' nav-tab-active' : ''?>"><?php echo esc_html__('Copy & Paste','gpt3-ai-content-generator')?></a>
        <?php
        endif;
        ?>
        <?php
        if(current_user_can('wpaicg_bulk_content_google-sheets')):
        ?>
        <a href="<?php echo admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=google-sheets')?>" class="nav-tab<?php echo $wpaicg_bulk_action == 'google-sheets' ? ' nav-tab-active' : ''?>">
            <?php echo esc_html__('Google Sheets','gpt3-ai-content-generator')?>
            <?php
            if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                ?>
                <span style="color: #000;padding: 2px 5px;font-size: 12px;background:#ffba00;border-radius: 2px;"><?php echo esc_html__('Pro','gpt3-ai-content-generator')?></span>
            <?php
            endif;
            ?>
        </a>
        <?php
        endif;
        ?>
        <?php
        if(current_user_can('wpaicg_bulk_content_rss')):
        ?>
        <a href="<?php echo admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=rss')?>" class="nav-tab<?php echo $wpaicg_bulk_action == 'rss' ? ' nav-tab-active' : ''?>">
            <?php echo esc_html__('RSS','gpt3-ai-content-generator')?>
            <?php
            if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
            ?>
            <span style="color: #000;padding: 2px 5px;font-size: 12px;background:#ffba00;border-radius: 2px;"><?php echo esc_html__('Pro','gpt3-ai-content-generator')?></span>
            <?php
            endif;
            ?>
        </a>
        <?php
        endif;
        ?>
        <?php
        if(current_user_can('wpaicg_bulk_content_tweet')):
        ?>
        <a href="<?php echo admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=tweet')?>" class="nav-tab<?php echo $wpaicg_bulk_action == 'tweet' ? ' nav-tab-active' : ''?>">
            <?php echo esc_html__('Twitter','gpt3-ai-content-generator')?>
            <?php
            if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
            ?>
            <span style="color: #000;padding: 2px 5px;font-size: 12px;background:#ffba00;border-radius: 2px;"><?php echo esc_html__('Pro','gpt3-ai-content-generator')?></span>
            <?php
            endif;
            ?>
        </a>
        <?php
        endif;
        ?>
        <?php
        if(current_user_can('wpaicg_bulk_content_tracking')):
        ?>
        <a href="<?php echo admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=tracking')?>" class="nav-tab<?php echo $wpaicg_track || $wpaicg_bulk_action == 'tracking' ? ' nav-tab-active' : ''?>"><?php echo esc_html__('Queue','gpt3-ai-content-generator')?></a>
        <?php
        endif;
        ?>
        <?php
        if(current_user_can('wpaicg_bulk_content_setting')):
        ?>
        <a href="<?php echo admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=setting')?>" class="nav-tab<?php echo $wpaicg_bulk_action == 'setting' ? ' nav-tab-active' : ''?>"><?php echo esc_html__('Settings','gpt3-ai-content-generator')?></a>
        <?php
        endif;
        ?>
    </h2>
    <div id="poststuff">
        <?php
        if(!$wpaicg_bulk_action && !$wpaicg_track):
            include __DIR__.'/wpaicg_bulk_dashboard.php';
        elseif($wpaicg_bulk_action == 'editor'):
            include __DIR__.'/wpaicg_bulk_index.php';
        elseif($wpaicg_bulk_action == 'tracking'):
            include __DIR__.'/wpaicg_bulk_queue.php';
        elseif($wpaicg_bulk_action == 'csv'):
            include __DIR__.'/wpaicg_bulk_csv.php';
        elseif($wpaicg_bulk_action == 'copy-paste'):
            include __DIR__.'/wpaicg_bulk_copy_paste.php';
        elseif($wpaicg_bulk_action == 'setting'):
            include __DIR__.'/wpaicg_bulk_setting.php';
        elseif($wpaicg_bulk_action == 'rss'):
            if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()) {
                include WPAICG_PLUGIN_DIR.'lib/views/rss/wpaicg_rss.php';
            }
            else{
                include __DIR__.'/wpaicg_rss.php';
            }
        elseif($wpaicg_bulk_action == 'google-sheets'):
            if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
                include WPAICG_PLUGIN_DIR.'lib/views/google-sheets/setting.php';
            }
            else{
                include __DIR__.'/wpaicg_google_sheets.php';
            }
        elseif($wpaicg_bulk_action == 'tweet'):
            if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
                include WPAICG_PLUGIN_DIR.'lib/views/twitter/index.php';
            }
            else{
                include __DIR__.'/wpaicg_twitter.php';
            }
        elseif($wpaicg_track):
            include __DIR__.'/wpaicg_bulk_tracking.php';
        endif;
        ?>
    </div>
</div>
