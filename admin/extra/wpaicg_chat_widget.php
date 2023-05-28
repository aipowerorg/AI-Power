<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$wpaicg_chat_widget = get_option('wpaicg_chat_widget',[]);
$wpaicg_chat_status = isset($wpaicg_chat_widget['status']) && !empty($wpaicg_chat_widget['status']) ? $wpaicg_chat_widget['status'] : '';
/*Check Custom Widget For Page Post*/
$current_context_ID = get_the_ID();
$wpaicg_bot_content = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->postmeta." WHERE meta_key=%s",'wpaicg_widget_page_'.$current_context_ID));
if($wpaicg_bot_content && isset($wpaicg_bot_content->post_id)){
    $wpaicg_bot = get_post($wpaicg_bot_content->post_id);
    if($wpaicg_bot) {
        if(strpos($wpaicg_bot->post_content,'\"') !== false) {
            $wpaicg_bot->post_content = str_replace('\"', '&quot;', $wpaicg_bot->post_content);
        }
        if(strpos($wpaicg_bot->post_content,"\'") !== false) {
            $wpaicg_bot->post_content = str_replace('\\', '', $wpaicg_bot->post_content);
        }
        $wpaicg_chat_widget = json_decode($wpaicg_bot->post_content, true);
        $wpaicg_chat_status = 'active';
    }
}
/*End check*/
$wpaicg_chat_icon = isset($wpaicg_chat_widget['icon']) && !empty($wpaicg_chat_widget['icon']) ? $wpaicg_chat_widget['icon'] : 'default';
$wpaicg_chat_icon_url = isset($wpaicg_chat_widget['icon_url']) && !empty($wpaicg_chat_widget['icon_url']) ? $wpaicg_chat_widget['icon_url'] : '';
$wpaicg_chat_fontsize = isset($wpaicg_chat_widget['fontsize']) && !empty($wpaicg_chat_widget['fontsize']) ? $wpaicg_chat_widget['fontsize'] : '13';
$wpaicg_chat_fontcolor = isset($wpaicg_chat_widget['fontcolor']) && !empty($wpaicg_chat_widget['fontcolor']) ? $wpaicg_chat_widget['fontcolor'] : '#90EE90';
$wpaicg_chat_bgcolor = isset($wpaicg_chat_widget['bgcolor']) && !empty($wpaicg_chat_widget['bgcolor']) ? $wpaicg_chat_widget['bgcolor'] : '#222222';
$wpaicg_chat_width = isset($wpaicg_chat_widget['width']) && !empty($wpaicg_chat_widget['width']) ? $wpaicg_chat_widget['width'] : '350';
$wpaicg_chat_height = isset($wpaicg_chat_widget['height']) && !empty($wpaicg_chat_widget['height']) ? $wpaicg_chat_widget['height'] : '400';
$wpaicg_chat_position = isset($wpaicg_chat_widget['position']) && !empty($wpaicg_chat_widget['position']) ? $wpaicg_chat_widget['position'] : 'left';
$wpaicg_chat_icon_url = $wpaicg_chat_icon == 'default' ||  empty($wpaicg_chat_icon_url) ? WPAICG_PLUGIN_URL.'admin/images/chatbot.png' :  wp_get_attachment_url($wpaicg_chat_icon_url);
$wpaicg_chat_tone = isset($wpaicg_chat_widget['tone']) && !empty($wpaicg_chat_widget['tone']) ? $wpaicg_chat_widget['tone'] : 'friendly';
$wpaicg_chat_proffesion = isset($wpaicg_chat_widget['proffesion']) && !empty($wpaicg_chat_widget['proffesion']) ? $wpaicg_chat_widget['proffesion'] : 'none';
$wpaicg_chat_remember_conversation = isset($wpaicg_chat_widget['remember_conversation']) && !empty($wpaicg_chat_widget['remember_conversation']) ? $wpaicg_chat_widget['remember_conversation'] : 'yes';
$wpaicg_chat_content_aware = isset($wpaicg_chat_widget['content_aware']) && !empty($wpaicg_chat_widget['content_aware']) ? $wpaicg_chat_widget['content_aware'] : 'yes';
$wpaicg_delay_time = isset($wpaicg_chat_widget['delay_time']) && !empty($wpaicg_chat_widget['delay_time']) ? $wpaicg_chat_widget['delay_time'] : '';
if($wpaicg_chat_status == 'active'):
    $randomWidgetID = rand(100000,999999);
?>
<div data-id="<?php echo esc_html($randomWidgetID)?>" id="wpaicgChat<?php echo esc_html($randomWidgetID)?>" class="wpaicg_chat_widget<?php echo $wpaicg_chat_position == 'left' ? ' wpaicg_widget_left' : ' wpaicg_widget_right'?>">
    <div class="wpaicg_chat_widget_content">
        <?php
        echo do_shortcode('[wpaicg_chatgpt_widget]');
        ?>
    </div>
    <div class="wpaicg_toggle" id="wpaicg_toggle_<?php echo esc_html($randomWidgetID)?>">
        <img src="<?php echo esc_html($wpaicg_chat_icon_url)?>" />
    </div>
</div>
<?php
if(!empty($wpaicg_delay_time)){
?>
    <script>
        setTimeout(function (){
            var widget = document.getElementById('wpaicgChat<?php echo esc_html($randomWidgetID)?>');
            if(!widget.classList.contains('wpaicg_widget_open')) {
                var toggleBtn = document.getElementById('wpaicg_toggle_<?php echo esc_html($randomWidgetID)?>');
                toggleBtn.click();
            }
        },<?php echo esc_html($wpaicg_delay_time)*1000?>);
    </script>
<?php
}
?>
<?php
endif;
?>
