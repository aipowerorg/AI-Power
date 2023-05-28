<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wp,$wpdb;
$wpaicg_ai_thinking = get_option('_wpaicg_ai_thinking','');
$wpaicg_you = get_option('_wpaicg_chatbox_you','');
$wpaicg_typing_placeholder = get_option('_wpaicg_typing_placeholder','');
$wpaicg_welcome_message = get_option('_wpaicg_chatbox_welcome_message','');
$wpaicg_chat_widget = get_option('wpaicg_chat_widget',[]);
$wpaicg_ai_name = get_option('_wpaicg_chatbox_ai_name','');
/*Check Custom Widget For Page Post*/
$current_context_ID = get_the_ID();
$wpaicg_bot_id = 0;
$wpaicg_bot_content = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->postmeta." WHERE meta_key=%s",'wpaicg_widget_page_'.$current_context_ID));
if($wpaicg_bot_content && isset($wpaicg_bot_content->post_id)){
    $wpaicg_bot_id = $wpaicg_bot_content->post_id;
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
        $wpaicg_you = isset($wpaicg_chat_widget['you']) && !empty($wpaicg_chat_widget['you']) ? $wpaicg_chat_widget['you'] : $wpaicg_you;
        $wpaicg_typing_placeholder = isset($wpaicg_chat_widget['placeholder']) && !empty($wpaicg_chat_widget['placeholder']) ? $wpaicg_chat_widget['placeholder'] : $wpaicg_typing_placeholder;
        $wpaicg_welcome_message = isset($wpaicg_chat_widget['welcome']) && !empty($wpaicg_chat_widget['welcome']) ? $wpaicg_chat_widget['welcome'] : $wpaicg_welcome_message;
        $wpaicg_ai_name = isset($wpaicg_chat_widget['ai_name']) && !empty($wpaicg_chat_widget['ai_name']) ? $wpaicg_chat_widget['ai_name'] : $wpaicg_ai_name;
        $wpaicg_ai_thinking = isset($wpaicg_chat_widget['ai_thinking']) && !empty($wpaicg_chat_widget['ai_thinking']) ? $wpaicg_chat_widget['ai_thinking'] : $wpaicg_ai_thinking;
    }
}
$wpaicg_chat_widget_width = isset($wpaicg_chat_widget['width']) && !empty($wpaicg_chat_widget['width']) ? $wpaicg_chat_widget['width'] : '350';
$wpaicg_chat_widget_height = isset($wpaicg_chat_widget['height']) && !empty($wpaicg_chat_widget['height']) ? $wpaicg_chat_widget['height'] : '400';
/*End check*/
$wpaicg_ai_name = !empty($wpaicg_ai_name) ? $wpaicg_ai_name : esc_html__('AI','gpt3-ai-content-generator');
$wpaicg_ai_thinking = !empty($wpaicg_ai_thinking) ? $wpaicg_ai_thinking : esc_html__('AI thinking','gpt3-ai-content-generator');
$wpaicg_you = !empty($wpaicg_you) ? $wpaicg_you : esc_html__('You','gpt3-ai-content-generator');
$wpaicg_typing_placeholder = !empty($wpaicg_typing_placeholder) ? $wpaicg_typing_placeholder : esc_html__('Type a message','gpt3-ai-content-generator');
$wpaicg_chat_content_aware = isset($wpaicg_chat_widget['content_aware']) && !empty($wpaicg_chat_widget['content_aware']) ? $wpaicg_chat_widget['content_aware'] : 'yes';
$wpaicg_welcome_message = !empty($wpaicg_welcome_message) ? $wpaicg_welcome_message : 'Hello human, I am a GPT powered AI chat bot. Ask me anything!';
$wpaicg_user_bg_color = isset($wpaicg_chat_widget['user_bg_color']) && !empty($wpaicg_chat_widget['user_bg_color']) ? $wpaicg_chat_widget['user_bg_color'] : '#444654';
$wpaicg_ai_bg_color = isset($wpaicg_chat_widget['ai_bg_color']) && !empty($wpaicg_chat_widget['ai_bg_color']) ? $wpaicg_chat_widget['ai_bg_color'] : '#343541';
$wpaicg_use_avatar = isset($wpaicg_chat_widget['use_avatar']) && !empty($wpaicg_chat_widget['use_avatar']) ? $wpaicg_chat_widget['use_avatar'] : false;
$wpaicg_ai_avatar = isset($wpaicg_chat_widget['ai_avatar']) && !empty($wpaicg_chat_widget['ai_avatar']) ? $wpaicg_chat_widget['ai_avatar'] : 'default';
$wpaicg_ai_avatar_id = isset($wpaicg_chat_widget['ai_avatar_id']) && !empty($wpaicg_chat_widget['ai_avatar_id']) ? $wpaicg_chat_widget['ai_avatar_id'] : '';
$wpaicg_ai_avatar_url = WPAICG_PLUGIN_URL.'admin/images/chatbot.png';
$wpaicg_user_avatar_url = is_user_logged_in() ? get_avatar_url(get_current_user_id()) : get_avatar_url('');
if($wpaicg_use_avatar && $wpaicg_ai_avatar == 'custom' && $wpaicg_ai_avatar_id != ''){
    $wpaicg_ai_avatar_url = wp_get_attachment_url($wpaicg_ai_avatar_id);
}
$wpaicg_chat_fontsize = isset($wpaicg_chat_widget['fontsize']) && !empty($wpaicg_chat_widget['fontsize']) ? $wpaicg_chat_widget['fontsize'] : '13';
$wpaicg_chat_fontcolor = isset($wpaicg_chat_widget['fontcolor']) && !empty($wpaicg_chat_widget['fontcolor']) ? $wpaicg_chat_widget['fontcolor'] : '#fff';
$wpaicg_save_logs = isset($wpaicg_chat_widget['save_logs']) && !empty($wpaicg_chat_widget['save_logs']) ? $wpaicg_chat_widget['save_logs'] : false;
$wpaicg_log_notice = isset($wpaicg_chat_widget['log_notice']) && !empty($wpaicg_chat_widget['log_notice']) ? $wpaicg_chat_widget['log_notice'] : false;
$wpaicg_log_notice_message = isset($wpaicg_chat_widget['log_notice_message']) && !empty($wpaicg_chat_widget['log_notice_message']) ? $wpaicg_chat_widget['log_notice_message'] : esc_html__('Please note that your conversations will be recorded.','gpt3-ai-content-generator');
$wpaicg_audio_enable = isset($wpaicg_chat_widget['audio_enable']) ? $wpaicg_chat_widget['audio_enable'] : false;
$wpaicg_pdf_enable = isset($wpaicg_chat_widget['embedding_pdf']) ? $wpaicg_chat_widget['embedding_pdf'] : false;
$wpaicg_pdf_pages = isset($wpaicg_chat_widget['pdf_pages']) ? $wpaicg_chat_widget['pdf_pages'] : 120;
$wpaicg_mic_color = isset($wpaicg_chat_widget['mic_color']) ? $wpaicg_chat_widget['mic_color'] : '#222';
$wpaicg_pdf_color = isset($wpaicg_chat_widget['pdf_color']) ? $wpaicg_chat_widget['pdf_color'] : '#222';
$wpaicg_stop_color = isset($wpaicg_chat_widget['stop_color']) ? $wpaicg_chat_widget['stop_color'] : '#f00';
$wpaicg_chat_fullscreen = isset($wpaicg_chat_widget['fullscreen']) && !empty($wpaicg_chat_widget['fullscreen']) ? $wpaicg_chat_widget['fullscreen'] : false;
$wpaicg_chat_close_btn = isset($wpaicg_chat_widget['close_btn']) && !empty($wpaicg_chat_widget['close_btn']) ? $wpaicg_chat_widget['close_btn'] : false;
$wpaicg_chat_download_btn = isset($wpaicg_chat_widget['download_btn']) && !empty($wpaicg_chat_widget['download_btn']) ? $wpaicg_chat_widget['download_btn'] : false;
$wpaicg_has_action_bar = false;
$wpaicg_chat_bgcolor = isset($wpaicg_chat_widget['bgcolor']) && !empty($wpaicg_chat_widget['bgcolor']) ? $wpaicg_chat_widget['bgcolor'] : '#222222';
$wpaicg_bar_color = isset($wpaicg_chat_widget['bar_color']) && !empty($wpaicg_chat_widget['bar_color']) ? $wpaicg_chat_widget['bar_color'] : '#fff';
$wpaicg_thinking_color = isset($wpaicg_chat_widget['thinking_color']) && !empty($wpaicg_chat_widget['thinking_color']) ? $wpaicg_chat_widget['thinking_color'] : '#fff';
$wpaicg_delay_time = isset($wpaicg_chat_widget['delay_time']) && !empty($wpaicg_chat_widget['delay_time']) ? $wpaicg_chat_widget['delay_time'] : '';
if($wpaicg_chat_fullscreen || $wpaicg_chat_download_btn || $wpaicg_chat_close_btn){
    $wpaicg_has_action_bar = true;
}
$wpaicg_text_height = isset($wpaicg_chat_widget['text_height']) && !empty($wpaicg_chat_widget['text_height']) ? $wpaicg_chat_widget['text_height'] : 60;
$wpaicg_text_rounded = isset($wpaicg_chat_widget['text_rounded']) && !empty($wpaicg_chat_widget['text_height']) ? $wpaicg_chat_widget['text_rounded'] : 20;
$wpaicg_chat_rounded = isset($wpaicg_chat_widget['chat_rounded']) && !empty($wpaicg_chat_widget['text_height']) ? $wpaicg_chat_widget['chat_rounded'] : 20;
$wpaicg_chat_to_speech = isset($wpaicg_chat_widget['chat_to_speech']) ? $wpaicg_chat_widget['chat_to_speech'] : false;
$wpaicg_elevenlabs_voice = isset($wpaicg_chat_widget['elevenlabs_voice']) ? $wpaicg_chat_widget['elevenlabs_voice'] : '';
$wpaicg_elevenlabs_api = get_option('wpaicg_elevenlabs_api', '');
$wpaicg_elevenlabs_hide_error = get_option('wpaicg_elevenlabs_hide_error', false);
if(empty($wpaicg_elevenlabs_api) && empty($wpaicg_google_api_key)){
    $wpaicg_chat_to_speech = false;
}
$wpaicg_chat_voice_service = isset($wpaicg_chat_widget['voice_service']) && !empty($wpaicg_chat_widget['voice_service']) ? $wpaicg_chat_widget['voice_service'] : 'en-US';
$wpaicg_voice_language = isset($wpaicg_chat_widget['voice_language']) && !empty($wpaicg_chat_widget['voice_language']) ? $wpaicg_chat_widget['voice_language'] : 'en-US';
$wpaicg_voice_name = isset($wpaicg_chat_widget['voice_name']) && !empty($wpaicg_chat_widget['voice_name']) ? $wpaicg_chat_widget['voice_name'] : 'en-US-Studio-M';
$wpaicg_voice_device = isset($wpaicg_chat_widget['voice_device']) && !empty($wpaicg_chat_widget['voice_device']) ? $wpaicg_chat_widget['voice_device'] : '';
$wpaicg_voice_speed = isset($wpaicg_chat_widget['voice_speed']) && !empty($wpaicg_chat_widget['voice_speed']) ? $wpaicg_chat_widget['voice_speed'] : 1;
$wpaicg_voice_pitch = isset($wpaicg_chat_widget['voice_pitch']) && !empty($wpaicg_chat_widget['voice_pitch']) ? $wpaicg_chat_widget['voice_pitch'] : 0;
?>
<style>
    .wpaicg_chat_widget,.wpaicg_chat_widget_content{
        z-index: 99999;
    }
    .wpaicg_chat_widget{
        overflow: hidden;
    }
    .wpaicg_widget_open.wpaicg_chat_widget{
        overflow: unset;
    }
    .wpaicg-chatbox{
        width: 100%;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    .wpaicg-chatbox-content{
        position: relative;
    }
    .wpaicg-chatbox-content ul{
        height: 400px;
        overflow-y: auto;
        background: #222;
        margin: 0;
        padding: 0;
    }
    .wpaicg-chatbox-content ul li{
        color: #90EE90;
        display: flex;
        margin-bottom: 10px;
    }
    .wpaicg-chatbox-content ul li strong{
        font-weight: bold;
        margin-right: 5px;
        float: left;
    }
    .wpaicg-chatbox-content ul li p{
        margin: 0;
        padding: 0;
    }
    .wpaicg-chatbox-content ul li p:after{
        clear: both;
        display: block;
    }
    .wpaicg-bot-thinking{
        bottom: 0;
        font-size: 11px;
        padding: 2px 6px;
        display: none;
    }
    .wpaicg-chat-message{
        color: #90EE90;
        text-align: justify;
    }
    .wpaicg-jumping-dots span {
        position: relative;
        bottom: 0px;
        -webkit-animation: wpaicg-jump 1500ms infinite;
        animation: wpaicg-jump 2s infinite;
    }
    .wpaicg-jumping-dots .wpaicg-dot-1{
        -webkit-animation-delay: 200ms;
        animation-delay: 200ms;
    }
    .wpaicg-jumping-dots .wpaicg-dot-2{
        -webkit-animation-delay: 400ms;
        animation-delay: 400ms;
    }
    .wpaicg-jumping-dots .wpaicg-dot-3{
        -webkit-animation-delay: 600ms;
        animation-delay: 600ms;
    }
    .wpaicg-chatbox-send{
        display: flex;
        align-items: center;
        color: #fff;
        padding: 2px 3px;
        cursor: pointer;
    }
    .wpaicg-chatbox-type{
        display: flex;
        align-items: center;
        padding: 5px;
        background: #141414;
        border-top: 1px solid #3e3e3e;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }
    textarea.wpaicg-chatbox-typing{
        flex: 1;
        border: 1px solid #ccc;
        border-radius: 3px;
        background: #fff;
        padding: 0 8px;
        min-height: 30px;
        line-height: 2;
        box-shadow: 0 0 0 transparent;
        color: #2c3338;
        margin: 0;
        resize: none; /* allows the user to resize the textarea vertically */
        overflow: auto;
        word-wrap: break-word;
    }
    .wpaicg-chatbox-send svg{
        width: 30px;
        height: 30px;
        fill: currentColor;
        stroke: currentColor;
    }
    .wpaicg-chat-message-error{
        color: #f00;
    }

    @-webkit-keyframes wpaicg-jump {
        0%   {bottom: 0px;}
        20%  {bottom: 5px;}
        40%  {bottom: 0px;}
    }

    @keyframes wpaicg-jump {
        0%   {bottom: 0px;}
        20%  {bottom: 5px;}
        40%  {bottom: 0px;}
    }
    @media (max-width: 599px){
        .wpaicg_chat_widget_content .wpaicg-chatbox{
            width: 100%;
        }
        .wpaicg_widget_left .wpaicg_chat_widget_content{
            left: -15px!important;
            right: auto;
        }
        .wpaicg_widget_right .wpaicg_chat_widget_content{
            right: -5px!important;
            left: auto;
        }
    }
    .wpaicg_chat_widget_content .wpaicg-chat-ai-message,
    .wpaicg_chat_widget_content .wpaicg-chat-ai-message *,
    .wpaicg_chat_widget_content .wpaicg-chat-user-message,
    .wpaicg_chat_widget_content .wpaicg-chat-user-message *,
    .wpaicg_chat_widget_content .wpaicg-chat-user-message .wpaicg-chat-message,
    .wpaicg_chat_widget_content .wpaicg-chat-ai-message .wpaicg-chat-message,
    .wpaicg_chat_widget_content .wpaicg-chat-ai-message a,
    .wpaicg_chat_widget_content .wpaicg-chat-user-message a
    {
        font-size: <?php echo esc_html($wpaicg_chat_fontsize)?>px;
        color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
    }
    .wpaicg-chat-user-message{
        padding: 10px;
        background: <?php echo esc_html($wpaicg_user_bg_color)?>;
    }
    .wpaicg-chat-ai-message{
        padding: 10px;
        background: <?php echo esc_html($wpaicg_ai_bg_color)?>;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-messages{
        padding: 0;
    }
    .wpaicg-chatbox-content ul li.wpaicg-chat-ai-message,.wpaicg-chatbox-content ul li.wpaicg-chat-user-message{
        margin-bottom: 0;
    }
    .wpaicg_chat_additions{
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        right: 47px;
    }
    .wpaicg-chatbox .wpaicg-mic-icon{
        color: <?php echo esc_html($wpaicg_mic_color)?>;
    }
    .wpaicg-chatbox .wpaicg-pdf-icon{
        color: <?php echo esc_html($wpaicg_pdf_color)?>;
    }
    .wpaicg-chatbox .wpaicg-pdf-remove{
        color: <?php echo esc_html($wpaicg_pdf_color)?>;
        font-size: 33px;
        justify-content: center;
        align-items: center;
        width: 22px;
        height: 22px;
        line-height: unset;
        font-family: Arial, serif;
        border-radius: 50%;
        font-weight: normal;
        padding: 0;
        margin: 0;
    }
    .wpaicg-chatbox .wpaicg-pdf-loading{
        border-color: <?php echo esc_html($wpaicg_pdf_color)?>;
        border-bottom-color: transparent;
    }
    .wpaicg-chatbox .wpaicg-mic-icon.wpaicg-recording{
        color: <?php echo esc_html($wpaicg_stop_color)?>;
    }
    .wpaicg-chatbox .wpaicg-bot-thinking{
        width: 100%;
        background-color: <?php echo esc_html($wpaicg_chat_widget['bgcolor'])?>;
    }
    .wpaicg-chatbox-action-bar{
        height: 30px;
        padding: 0 5px;
        display: none;
        justify-content: flex-end;
        align-items: center;
        border-top-left-radius: 2px;
        border-top-right-radius: 2px;
        background: <?php echo esc_html($wpaicg_chat_bgcolor)?>;
        color: <?php echo esc_html($wpaicg_bar_color)?>;
    }
    .wpaicg-chatbox-preview-box .wpaicg-chatbox-action-bar{
        width: calc(100% - 10px);
    }
    .wpaicg_widget_open .wpaicg-chatbox-action-bar{
        display: flex;
    }
    .wpaicg-chatbox-download-btn{
        cursor: pointer;
        padding: 2px;
        display: flex;
        align-items: center;
        margin: 0 3px;
    }
    .wpaicg-chatbox-download-btn svg{
        fill: currentColor;
        height: 16px;
        width: 16px;
    }
    .wpaicg-chatbox-fullscreen{
        cursor: pointer;
        padding: 2px;
        display: flex;
        align-items: center;
        margin: 0 3px;
    }
    .wpaicg-chatbox-close-btn{
        cursor: pointer;
        padding: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 3px;
    }
    .wpaicg-chatbox-close-btn svg{
        fill: currentColor;
        height: 16px;
        width: 16px;
    }
    .wpaicg-chatbox-fullscreen svg.wpaicg-exit-fullscreen{
        display: none;
        fill: none;
        height: 16px;
        width: 16px;
    }
    .wpaicg-chatbox-fullscreen svg.wpaicg-exit-fullscreen path{
        fill: currentColor;
    }
    .wpaicg-chatbox-fullscreen svg.wpaicg-active-fullscreen{
        fill: none;
        height: 16px;
        width: 16px;
    }
    .wpaicg-chatbox-fullscreen svg.wpaicg-active-fullscreen path{
        fill: currentColor;
    }
    .wpaicg-chatbox-fullscreen.wpaicg-fullscreen-box svg.wpaicg-active-fullscreen{
        display:none;
    }
    .wpaicg-chatbox-fullscreen.wpaicg-fullscreen-box svg.wpaicg-exit-fullscreen{
        display: block;
    }
    .wpaicg-fullscreened .wpaicg-chatbox-action-bar{
        top: 0;
        z-index: 99;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-bottom-left-radius: 3px;
    }
    .wpaicg-chatbox .wpaicg-chatbox-footer{
        margin: 0px;
        padding: 0 20px;
    }
    .wpaicg-chat-widget-has-footer .wpaicg-chatbox-type{
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
</style>
<div class="wpaicg-chatbox<?php echo $wpaicg_has_action_bar ? ' wpaicg-chatbox-has-action-bar':''?><?php echo isset($wpaicg_chat_widget['footer_text']) && !empty($wpaicg_chat_widget['footer_text']) ? ' wpaicg-chat-widget-has-footer':' wpaicg-chat-widget-no-footer'?>"
     data-user-bg-color="<?php echo esc_html($wpaicg_user_bg_color)?>"
     data-color="<?php echo esc_html($wpaicg_chat_fontcolor)?>"
     data-fontsize="<?php echo esc_html($wpaicg_chat_fontsize)?>"
     data-use-avatar="<?php echo esc_html($wpaicg_use_avatar)?>"
     data-user-avatar="<?php echo esc_html($wpaicg_user_avatar_url)?>"
     data-you="<?php echo esc_html($wpaicg_you)?>"
     data-ai-avatar="<?php echo esc_html($wpaicg_ai_avatar_url)?>"
     data-ai-name="<?php echo esc_html($wpaicg_ai_name)?>"
     data-ai-bg-color="<?php echo esc_html($wpaicg_ai_bg_color)?>"
     data-nonce="<?php echo esc_html(wp_create_nonce( 'wpaicg-chatbox' ))?>"
     data-post-id="<?php echo get_the_ID()?>"
     data-url="<?php echo home_url( $wp->request )?>"
     data-bot-id="<?php echo esc_html($wpaicg_bot_id)?>"
     data-width="<?php echo esc_html($wpaicg_chat_widget_width)?>"
     data-height="<?php echo esc_html($wpaicg_chat_widget_height)?>"
     data-footer="<?php echo isset($wpaicg_chat_widget['footer_text']) && !empty($wpaicg_chat_widget['footer_text']) ? 'true' : 'false'?>"
     data-speech="<?php echo esc_html($wpaicg_chat_to_speech)?>"
     data-voice="<?php echo esc_html($wpaicg_elevenlabs_voice)?>"
     data-voice-error="<?php echo esc_html($wpaicg_elevenlabs_hide_error)?>"
     data-text_height="<?php echo esc_html($wpaicg_text_height)?>"
     data-text_rounded="<?php echo esc_html($wpaicg_text_rounded)?>"
     data-chat_rounded="<?php echo esc_html($wpaicg_chat_rounded)?>"
     data-voice_service="<?php echo esc_html($wpaicg_chat_voice_service)?>"
     data-voice_language="<?php echo esc_html($wpaicg_voice_language)?>"
     data-voice_name="<?php echo esc_html($wpaicg_voice_name)?>"
     data-voice_device="<?php echo esc_html($wpaicg_voice_device)?>"
     data-voice_speed="<?php echo esc_html($wpaicg_voice_speed)?>"
     data-voice_pitch="<?php echo esc_html($wpaicg_voice_pitch)?>"
     data-type="widget"
>
    <?php
    if($wpaicg_has_action_bar):
        ?>
        <div class="wpaicg-chatbox-action-bar">
            <?php
            if($wpaicg_chat_download_btn):
                ?>
                <span data-type="widget" class="wpaicg-chatbox-download-btn">
            <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"  xml:space="preserve"><path class="st0" d="M243.591,309.362c3.272,4.317,7.678,6.692,12.409,6.692c4.73,0,9.136-2.376,12.409-6.689l89.594-118.094 c3.348-4.414,4.274-8.692,2.611-12.042c-1.666-3.35-5.631-5.198-11.168-5.198H315.14c-9.288,0-16.844-7.554-16.844-16.84V59.777 c0-11.04-8.983-20.027-20.024-20.027h-44.546c-11.04,0-20.022,8.987-20.022,20.027v97.415c0,9.286-7.556,16.84-16.844,16.84 h-34.305c-5.538,0-9.503,1.848-11.168,5.198c-1.665,3.35-0.738,7.628,2.609,12.046L243.591,309.362z"/><path class="st0" d="M445.218,294.16v111.304H66.782V294.16H0v152.648c0,14.03,11.413,25.443,25.441,25.443h461.118 c14.028,0,25.441-11.413,25.441-25.443V294.16H445.218z"/></svg>
        </span>
            <?php
            endif;
            ?>
            <?php
            if($wpaicg_chat_fullscreen):
                ?>
                <span data-type="widget" class="wpaicg-chatbox-fullscreen">
            <svg class="wpaicg-active-fullscreen" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 15H15V10H13.2V13.2H10V15ZM6 15V13.2H2.8V10H1V15H6ZM10 2.8H12.375H13.2V6H15V1H10V2.8ZM6 1V2.8H2.8V6H1V1H6Z"/></svg>
            <svg class="wpaicg-exit-fullscreen" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M1 6L6 6L6 1L4.2 1L4.2 4.2L1 4.2L1 6Z"/><path d="M15 10L10 10L10 15L11.8 15L11.8 11.8L15 11.8L15 10Z"/><path d="M6 15L6 10L1 10L1 11.8L4.2 11.8L4.2 15L6 15Z"/><path d="M10 1L10 6L15 6L15 4.2L11.8 4.2L11.8 1L10 1Z"/></svg>
        </span>
            <?php
            endif;
            ?>
            <?php
            if($wpaicg_chat_close_btn):
                ?>
                <span class="wpaicg-chatbox-close-btn">
            <svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path d="M195.2 195.2a64 64 0 0 1 90.496 0L512 421.504 738.304 195.2a64 64 0 0 1 90.496 90.496L602.496 512 828.8 738.304a64 64 0 0 1-90.496 90.496L512 602.496 285.696 828.8a64 64 0 0 1-90.496-90.496L421.504 512 195.2 285.696a64 64 0 0 1 0-90.496z"/></svg>
        </span>
            <?php
            endif;
            ?>
        </div>
    <?php
    endif;
    ?>
    <div class="wpaicg-chatbox-content">
        <ul class="wpaicg-chatbox-messages">
            <?php
            if($wpaicg_save_logs && $wpaicg_log_notice && !empty($wpaicg_log_notice_message)):
                ?>
                <li style="background: rgb(0 0 0 / 32%); padding: 10px;margin-bottom: 0">
                    <p>
                    <span class="wpaicg-chat-message">
                        <?php echo esc_html(str_replace("\\",'',$wpaicg_log_notice_message))?>
                    </span>
                    </p>
                </li>
            <?php
            endif;
            ?>
            <li class="wpaicg-chat-ai-message">
                <p>
                    <strong style="float: left"><?php echo $wpaicg_use_avatar ? '<img src="'.$wpaicg_ai_avatar_url.'" height="40" width="40">' : esc_html($wpaicg_ai_name).':' ?></strong>
                    <span class="wpaicg-chat-message">
                        <?php echo esc_html(str_replace("\\",'',$wpaicg_welcome_message))?>
                    </span>
                </p>
            </li>
        </ul>
        <span class="wpaicg-bot-thinking" style="color: <?php echo esc_html($wpaicg_thinking_color)?>;"><?php echo esc_html(str_replace("\\",'',$wpaicg_ai_thinking))?>&nbsp;<span class="wpaicg-jumping-dots"><span class="wpaicg-dot-1">.</span><span class="wpaicg-dot-2">.</span><span class="wpaicg-dot-3">.</span></span></span>
    </div>
    <div class="wpaicg-chatbox-type">
        <textarea type="text" class="wpaicg-chatbox-typing" placeholder="<?php echo esc_html(str_replace("\\",'',$wpaicg_typing_placeholder))?>"></textarea>
        <div class="wpaicg_chat_additions">
            <?php
            if($wpaicg_audio_enable):
                ?>
                <span class="wpaicg-mic-icon" data-type="widget">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M176 0C123 0 80 43 80 96V256c0 53 43 96 96 96s96-43 96-96V96c0-53-43-96-96-96zM48 216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 89.1 66.2 162.7 152 174.4V464H104c-13.3 0-24 10.7-24 24s10.7 24 24 24h72 72c13.3 0 24-10.7 24-24s-10.7-24-24-24H200V430.4c85.8-11.7 152-85.3 152-174.4V216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 70.7-57.3 128-128 128s-128-57.3-128-128V216z"/></svg>
            </span>
            <?php
            endif;
            ?>
            <?php
            if($wpaicg_pdf_enable && \WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                ?>
                <span class="wpaicg-pdf-icon" data-type="widget">
                <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"  xml:space="preserve"><path class="st0" d="M378.413,0H208.297h-13.182L185.8,9.314L57.02,138.102l-9.314,9.314v13.176v265.514 c0,47.36,38.528,85.895,85.896,85.895h244.811c47.353,0,85.881-38.535,85.881-85.895V85.896C464.294,38.528,425.766,0,378.413,0z M432.497,426.105c0,29.877-24.214,54.091-54.084,54.091H133.602c-29.884,0-54.098-24.214-54.098-54.091V160.591h83.716 c24.885,0,45.077-20.178,45.077-45.07V31.804h170.116c29.87,0,54.084,24.214,54.084,54.092V426.105z"/><path class="st0" d="M171.947,252.785h-28.529c-5.432,0-8.686,3.533-8.686,8.825v73.754c0,6.388,4.204,10.599,10.041,10.599 c5.711,0,9.914-4.21,9.914-10.599v-22.406c0-0.545,0.279-0.817,0.824-0.817h16.436c20.095,0,32.188-12.226,32.188-29.612 C204.136,264.871,192.182,252.785,171.947,252.785z M170.719,294.888h-15.208c-0.545,0-0.824-0.272-0.824-0.81v-23.23 c0-0.545,0.279-0.816,0.824-0.816h15.208c8.42,0,13.447,5.027,13.447,12.498C184.167,290,179.139,294.888,170.719,294.888z"/><path class="st0" d="M250.191,252.785h-21.868c-5.432,0-8.686,3.533-8.686,8.825v74.843c0,5.3,3.253,8.693,8.686,8.693h21.868 c19.69,0,31.923-6.249,36.81-21.324c1.76-5.3,2.723-11.681,2.723-24.857c0-13.175-0.964-19.557-2.723-24.856 C282.113,259.034,269.881,252.785,250.191,252.785z M267.856,316.896c-2.318,7.331-8.965,10.459-18.21,10.459h-9.23 c-0.545,0-0.824-0.272-0.824-0.816v-55.146c0-0.545,0.279-0.817,0.824-0.817h9.23c9.245,0,15.892,3.128,18.21,10.46 c0.95,3.128,1.62,8.56,1.62,17.93C269.476,308.336,268.805,313.768,267.856,316.896z"/><path class="st0" d="M361.167,252.785h-44.812c-5.432,0-8.7,3.533-8.7,8.825v73.754c0,6.388,4.218,10.599,10.055,10.599 c5.697,0,9.914-4.21,9.914-10.599v-26.351c0-0.538,0.265-0.81,0.81-0.81h26.086c5.837,0,9.23-3.532,9.23-8.56 c0-5.028-3.393-8.553-9.23-8.553h-26.086c-0.545,0-0.81-0.272-0.81-0.817v-19.425c0-0.545,0.265-0.816,0.81-0.816h32.733 c5.572,0,9.245-3.666,9.245-8.553C370.411,256.45,366.738,252.785,361.167,252.785z"/></svg>
            </span>
                <span class="wpaicg-pdf-loading" style="display: none"></span>
                <input data-type="widget" data-limit="<?php echo esc_html($wpaicg_pdf_pages)?>" type="file" accept="application/pdf" class="wpaicg-pdf-file" style="display: none">
                <span data-type="widget" alt="<?php echo esc_html__('Clear','gpt3-ai-content-generator')?>" title="<?php echo esc_html__('Clear','gpt3-ai-content-generator')?>" class="wpaicg-pdf-remove" style="display: none">&times;</span>
            <?php
            endif;
            ?>
        </div>
        <span class="wpaicg-chatbox-send">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5004 11.9998H5.00043M4.91577 12.2913L2.58085 19.266C2.39742 19.8139 2.3057 20.0879 2.37152 20.2566C2.42868 20.4031 2.55144 20.5142 2.70292 20.5565C2.87736 20.6052 3.14083 20.4866 3.66776 20.2495L20.3792 12.7293C20.8936 12.4979 21.1507 12.3822 21.2302 12.2214C21.2993 12.0817 21.2993 11.9179 21.2302 11.7782C21.1507 11.6174 20.8936 11.5017 20.3792 11.2703L3.66193 3.74751C3.13659 3.51111 2.87392 3.39291 2.69966 3.4414C2.54832 3.48351 2.42556 3.59429 2.36821 3.74054C2.30216 3.90893 2.3929 4.18231 2.57437 4.72906L4.91642 11.7853C4.94759 11.8792 4.96317 11.9262 4.96933 11.9742C4.97479 12.0168 4.97473 12.0599 4.96916 12.1025C4.96289 12.1506 4.94718 12.1975 4.91577 12.2913Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
    </div>
    <?php
    if(isset($wpaicg_chat_widget['footer_text']) && !empty($wpaicg_chat_widget['footer_text'])):
        ?>
        <div class="wpaicg-chatbox-footer">
            <?php
            echo esc_html(str_replace("\\",'',$wpaicg_chat_widget['footer_text']));
            ?>
        </div>
    <?php
    endif;
    ?>
</div>
