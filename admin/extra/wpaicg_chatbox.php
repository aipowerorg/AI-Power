<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wp,$wpdb;
$table = $wpdb->prefix . 'wpaicg';
$wpaicg_bot_id = 0;
$existingValue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE name = %s", 'wpaicg_settings' ), ARRAY_A );
$wpaicg_chat_shortcode_options = get_option('wpaicg_chat_shortcode_options',[]);
/*Check Custom Shortcode ID*/
if(isset($atts) && isset($atts['id']) && !empty($atts['id'])) {
    $wpaicg_bot = get_post($atts['id']);
    if ($wpaicg_bot) {
        $wpaicg_bot_id = $wpaicg_bot->ID;
        if(strpos($wpaicg_bot->post_content,'\"') !== false) {
            $wpaicg_bot->post_content = str_replace('\"', '&quot;', $wpaicg_bot->post_content);
        }
        if(strpos($wpaicg_bot->post_content,"\'") !== false) {
            $wpaicg_bot->post_content = str_replace('\\', '', $wpaicg_bot->post_content);
        }
        $wpaicg_chat_shortcode_options = json_decode($wpaicg_bot->post_content, true);
        $wpaicg_chat_shortcode_options['width'] = isset($wpaicg_chat_shortcode_options['width']) && !empty($wpaicg_chat_shortcode_options['width']) ? $wpaicg_chat_shortcode_options['width'].'px' : '350px';
        $wpaicg_chat_shortcode_options['height'] = isset($wpaicg_chat_shortcode_options['height']) && !empty($wpaicg_chat_shortcode_options['height']) ? $wpaicg_chat_shortcode_options['height'].'px' : '400px';
        $wpaicg_chat_shortcode_options['ai_icon'] = $wpaicg_chat_shortcode_options['ai_avatar'];
        $wpaicg_chat_shortcode_options['ai_icon_url'] = isset($wpaicg_chat_shortcode_options['ai_avatar_id']) ? $wpaicg_chat_shortcode_options['ai_avatar_id'] : false;
    }
}
/*End check*/
$default_setting = array(
    'language' => 'en',
    'tone' => 'friendly',
    'profession' => 'none',
    'model' => 'text-davinci-003',
    'temperature' => $existingValue['temperature'],
    'max_tokens' => $existingValue['max_tokens'],
    'top_p' => $existingValue['top_p'],
    'best_of' => $existingValue['best_of'],
    'frequency_penalty' => $existingValue['frequency_penalty'],
    'presence_penalty' => $existingValue['presence_penalty'],
    'ai_name' => esc_html__('AI','gpt3-ai-content-generator'),
    'you' => esc_html__('You','gpt3-ai-content-generator'),
    'ai_thinking' => esc_html__('AI Thinking','gpt3-ai-content-generator'),
    'placeholder' => esc_html__('Type a message','gpt3-ai-content-generator'),
    'welcome' => esc_html__('Hello human, I am a GPT powered AI chat bot. Ask me anything!','gpt3-ai-content-generator'),
    'remember_conversation' => 'yes',
    'conversation_cut' => 10,
    'content_aware' => 'yes',
    'embedding' =>  false,
    'embedding_type' =>  false,
    'embedding_pdf' => false,
    'pdf_color' => '#222',
    'embedding_top' =>  false,
    'no_answer' => '',
    'fontsize' => 13,
    'fontcolor' => '#fff',
    'user_bg_color' => '#444654',
    'ai_bg_color' => '#343541',
    'ai_icon_url' => '',
    'ai_icon' => 'default',
    'use_avatar' => false,
    'width' => '100%',
    'height' => '445px',
    'save_logs' => false,
    'log_notice' => false,
    'log_notice_message' => esc_html__('Please note that your conversations will be recorded.','gpt3-ai-content-generator'),
    'bgcolor' => '#222',
    'bg_text_field' => '#fff',
    'send_color' => '#fff',
    'border_text_field' => '#ccc',
    'footer_text' => '',
    'audio_enable' => false,
    'mic_color' => '#222',
    'stop_color' => '#f00',
    'fullscreen' => false,
    'download_btn' => false,
    'bar_color' => '#fff',
    'thinking_color' => '#fff',
    'chat_to_speech' => false,
    'elevenlabs_voice' => '',
    'text_height' => 60,
    'text_rounded' => 20,
    'pdf_pages' => 120,
    'chat_rounded' => 10,
    'voice_language' => 'en-US',
    'voice_name' => 'en-US-Studio-M',
    'voice_device' => '',
    'voice_speed' => 1,
    'voice_pitch' => 0,
    'voice_service' => ''
);
$wpaicg_settings = shortcode_atts($default_setting, $wpaicg_chat_shortcode_options);
$wpaicg_ai_thinking = $wpaicg_settings['ai_thinking'];
$wpaicg_you = $wpaicg_settings['you'];
$wpaicg_typing_placeholder = $wpaicg_settings['placeholder'];
$wpaicg_welcome_message = $wpaicg_settings['welcome'];
$wpaicg_ai_name = $wpaicg_settings['ai_name'];
$wpaicg_chat_content_aware = $wpaicg_settings['content_aware'];
$wpaicg_font_color = $wpaicg_settings['fontcolor'];
$wpaicg_font_size = $wpaicg_settings['fontsize'];
$wpaicg_user_bg_color = $wpaicg_settings['user_bg_color'];
$wpaicg_ai_bg_color = $wpaicg_settings['ai_bg_color'];
$wpaicg_save_logs = isset($wpaicg_settings['save_logs']) && !empty($wpaicg_settings['save_logs']) ? $wpaicg_settings['save_logs'] : false;
$wpaicg_log_notice = isset($wpaicg_settings['log_notice']) && !empty($wpaicg_settings['log_notice']) ? $wpaicg_settings['log_notice'] : false;
$wpaicg_log_notice_message = isset($wpaicg_settings['log_notice_message']) && !empty($wpaicg_settings['log_notice_message']) ? $wpaicg_settings['log_notice_message'] : esc_html__('Please note that your conversations will be recorded.','gpt3-ai-content-generator');
$wpaicg_include_footer = (isset($wpaicg_settings['footer_text']) && !empty($wpaicg_settings['footer_text'])) ? 5 : 0;
$wpaicg_audio_enable = $wpaicg_settings['audio_enable'];
$wpaicg_pdf_enable = $wpaicg_settings['embedding_pdf'];
$wpaicg_mic_color = (isset($wpaicg_settings['mic_color']) && !empty($wpaicg_settings['mic_color'])) ? $wpaicg_settings['mic_color'] : '#222';
$wpaicg_stop_color = (isset($wpaicg_settings['stop_color']) && !empty($wpaicg_settings['stop_color'])) ? $wpaicg_settings['stop_color'] : '#f00';
$wpaicg_chat_fullscreen = isset($wpaicg_settings['fullscreen']) && !empty($wpaicg_settings['fullscreen']) ? $wpaicg_settings['fullscreen'] : false;
$wpaicg_chat_download_btn = isset($wpaicg_settings['download_btn']) && !empty($wpaicg_settings['download_btn']) ? $wpaicg_settings['download_btn'] : false;
$wpaicg_chat_widget_width = isset($wpaicg_settings['width']) && !empty($wpaicg_settings['width']) ? $wpaicg_settings['width'] : '100%';
$wpaicg_chat_widget_height = isset($wpaicg_settings['height']) && !empty($wpaicg_settings['height']) ? $wpaicg_settings['height'] : '400';
$wpaicg_bar_color = isset($wpaicg_settings['bar_color']) && !empty($wpaicg_settings['bar_color']) ? $wpaicg_settings['bar_color'] : '#fff';
$wpaicg_thinking_color = isset($wpaicg_settings['thinking_color']) && !empty($wpaicg_settings['thinking_color']) ? $wpaicg_settings['thinking_color'] : '#fff';
$wpaicg_chat_to_speech = isset($wpaicg_settings['chat_to_speech']) ? $wpaicg_settings['chat_to_speech'] : false;
$wpaicg_elevenlabs_voice = isset($wpaicg_settings['elevenlabs_voice']) ? $wpaicg_settings['elevenlabs_voice'] : '';
$wpaicg_elevenlabs_hide_error = get_option('wpaicg_elevenlabs_hide_error', false);
$wpaicg_elevenlabs_api = get_option('wpaicg_elevenlabs_api', '');
$wpaicg_text_height = isset($wpaicg_settings['text_height']) && !empty($wpaicg_settings['text_height']) ? $wpaicg_settings['text_height'] : 60;
$wpaicg_text_rounded = isset($wpaicg_settings['text_rounded']) && !empty($wpaicg_settings['text_height']) ? $wpaicg_settings['text_rounded'] : 20;
$wpaicg_chat_rounded = isset($wpaicg_settings['chat_rounded']) && !empty($wpaicg_settings['text_height']) ? $wpaicg_settings['chat_rounded'] : 20;
$wpaicg_chat_voice_service = isset($wpaicg_settings['voice_service']) && !empty($wpaicg_settings['voice_service']) ? $wpaicg_settings['voice_service'] : '';
$wpaicg_voice_language = isset($wpaicg_settings['voice_language']) && !empty($wpaicg_settings['voice_language']) ? $wpaicg_settings['voice_language'] : 'en-US';
$wpaicg_voice_name = isset($wpaicg_settings['voice_name']) && !empty($wpaicg_settings['voice_name']) ? $wpaicg_settings['voice_name'] : 'en-US-Studio-M';
$wpaicg_voice_device = isset($wpaicg_settings['voice_device']) && !empty($wpaicg_settings['voice_device']) ? $wpaicg_settings['voice_device'] : '';
$wpaicg_voice_speed = isset($wpaicg_settings['voice_speed']) && !empty($wpaicg_settings['voice_speed']) ? $wpaicg_settings['voice_speed'] : 1;
$wpaicg_voice_pitch = isset($wpaicg_settings['voice_pitch']) && !empty($wpaicg_settings['voice_pitch']) ? $wpaicg_settings['voice_pitch'] : 0;
$wpaicg_google_api_key = get_option('wpaicg_google_api_key', '');
if(empty($wpaicg_elevenlabs_api) && empty($wpaicg_google_api_key)){
    $wpaicg_chat_to_speech = false;
}
?>
<style>
    .wpaicg-chat-shortcode{
        width: <?php echo esc_html($wpaicg_settings['width'])?>;
    }
    .wpaicg-chat-shortcode-content{
        position: relative;
    }
    .wpaicg-chat-shortcode-content ul{
        height: calc(<?php echo esc_html($wpaicg_settings['height'])?> - 44px);
        overflow-y: auto;
        margin: 0;
        padding: 0;
    }
    .wpaicg-chat-shortcode-footer{
        height: 18px;
        font-size: 11px;
        padding: 0 20px;
        color: <?php echo esc_html($wpaicg_settings['send_color'])?>;
        background: rgb(0 0 0 / 19%);
        margin-top:2px;
        margin-bottom: 2px;
    }
    .wpaicg-chat-shortcode-content ul li{
        display: flex;
        margin-bottom: 0;
        padding: 10px;
        color: <?php echo esc_html($wpaicg_font_color)?>;
    }
    .wpaicg-chat-shortcode-content ul li .wpaicg-chat-message{
        color: inherit;
    }
    .wpaicg-chat-shortcode-content ul li strong{
        font-weight: bold;
        margin-right: 5px;
        float: left;
    }
    .wpaicg-chat-shortcode-content ul li p{
        font-size: inherit;
    }
    .wpaicg-chat-shortcode-content ul li strong img{

    }
    .wpaicg-chat-shortcode-content ul li p{
        margin: 0;
        padding: 0;
    }
    .wpaicg-chat-shortcode-content ul li p:after{
        clear: both;
        display: block;
    }
    .wpaicg-chat-shortcode .wpaicg-bot-thinking{
        bottom: 0;
        font-size: 11px;
        color: <?php echo esc_html($wpaicg_thinking_color)?>;
        padding: 2px 6px;
        display: none;
    }
    .wpaicg-chat-shortcode{
        background-color: <?php echo esc_html($wpaicg_settings['bgcolor'])?>;
    }
    .wpaicg-chat-shortcode-type{
        background: rgb(0 0 0 / 19%);
    }
    .wpaicg-chat-message{
        text-align: justify;
    }
    .wpaicg-chat-shortcode .wpaicg-ai-message .wpaicg-chat-message,
    .wpaicg-chat-shortcode .wpaicg-user-message .wpaicg-chat-message,
    .wpaicg-chat-shortcode .wpaicg-ai-message .wpaicg-chat-message,
    .wpaicg-chat-shortcode .wpaicg-user-message .wpaicg-chat-message a,
    .wpaicg-chat-shortcode .wpaicg-ai-message .wpaicg-chat-message a{
        color: inherit;
    }
    .wpaicg-chat-shortcode .wpaicg-bot-thinking{
        width: calc(100% - 12px);
        background-color: <?php echo esc_html($wpaicg_settings['bgcolor'])?>;
    }
    .wpaicg-jumping-dots span {
        position: relative;
        bottom: 0;
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
    .wpaicg-chat-shortcode-send{
        display: flex;
        align-items: center;
        color: <?php echo esc_html($wpaicg_settings['send_color'])?>;
        padding: 2px 3px;
        cursor: pointer;
    }
    .wpaicg-chat-shortcode-type{
        display: flex;
        align-items: center;
    <?php
    if($wpaicg_include_footer):
    ?>
        padding: 5px 5px 0 5px;
    <?php
    else:
    ?>
        padding: 5px;
    <?php
    endif;
    ?>
    }
    textarea.wpaicg-chat-shortcode-typing{
        flex: 1;
        border: 1px solid #ccc;
        border-radius: 3px;
        background-color: <?php echo esc_html($wpaicg_settings['bg_text_field'])?>;
        border-color: <?php echo esc_html($wpaicg_settings['border_text_field'])?>;
        padding: 0 8px;
        min-height: 30px;
        line-height: 2;
        box-shadow: 0 0 0 transparent;
        color: #2c3338;
        margin: 0;
        position: relative;
        resize: vertical; /* allows the user to resize the textarea vertically */
        overflow: auto;
        word-wrap: break-word;
    }
    .wpaicg-chat-shortcode-send svg{
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
        .wpaicg_chat_widget_content .wpaicg-chat-shortcode{
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
    .wpaicg-chat-shortcode .wpaicg-mic-icon{
        color: <?php echo esc_html($wpaicg_mic_color)?>;
    }
    .wpaicg-chat-shortcode .wpaicg-pdf-icon{
        color: <?php echo esc_html($wpaicg_settings['pdf_color'])?>;
    }
    .wpaicg-chat-shortcode .wpaicg-pdf-remove{
        color: <?php echo esc_html($wpaicg_settings['pdf_color'])?>;
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
    .wpaicg-chat-shortcode .wpaicg-pdf-loading{
        border-color: <?php echo esc_html($wpaicg_settings['pdf_color'])?>;
        border-bottom-color: transparent;
    }
    .wpaicg-chat-shortcode .wpaicg-mic-icon.wpaicg-recording{
        color: <?php echo esc_html($wpaicg_stop_color)?>;
    }
    .wpaicg_chat_additions{
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        right: 47px;
    }
    .wpaicg-chat-message{
        line-height: auto;
    }
    .wpaicg-chat-shortcode .wpaicg-chatbox-action-bar{
        position: absolute;
        top: -30px;
        right: 0;
        height: 30px;
        padding: 0 5px;
        display: none;
        justify-content: center;
        align-items: center;
        border-top-left-radius: 2px;
        border-top-right-radius: 2px;
        background: rgb(0 0 0 / 20%);
        color: <?php echo esc_html($wpaicg_bar_color)?>;
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
    .wpaicg-chat-shortcode .wpaicg-chatbox-action-bar{
        position: relative;
        top: 0;
        display: flex;
        justify-content: flex-end;
    }
</style>
<?php
if(isset($wpaicg_settings['use_avatar']) && $wpaicg_settings['use_avatar']) {
    $wpaicg_ai_name = isset($wpaicg_settings['ai_icon_url']) && isset($wpaicg_settings['ai_icon']) && $wpaicg_settings['ai_icon'] == 'custom' && !empty($wpaicg_settings['ai_icon_url']) ? wp_get_attachment_url(esc_html($wpaicg_settings['ai_icon_url'])) : WPAICG_PLUGIN_URL . 'admin/images/chatbot.png';
    $wpaicg_ai_name = '<img src="'.$wpaicg_ai_name.'" height="40" width="40">';
}
$wpaicg_has_action_bar = false;
if($wpaicg_chat_fullscreen || $wpaicg_chat_download_btn){
    $wpaicg_has_action_bar = true;
}
?>
<div class="wpaicg-chat-shortcode"
     data-user-bg-color="<?php echo esc_html($wpaicg_user_bg_color)?>"
     data-color="<?php echo esc_html($wpaicg_font_color)?>"
     data-fontsize="<?php echo esc_html($wpaicg_font_size)?>"
     data-use-avatar="<?php echo isset($wpaicg_settings['use_avatar']) && $wpaicg_settings['use_avatar'] ? '1' : '0'?>"
     data-user-avatar="<?php echo is_user_logged_in() ? get_avatar_url(get_current_user_id()) : get_avatar_url('')?>"
     data-you="<?php echo esc_html($wpaicg_you)?>"
     data-ai-avatar="<?php echo isset($wpaicg_settings['use_avatar']) && $wpaicg_settings['use_avatar'] && isset($wpaicg_settings['ai_icon_url']) && !empty($wpaicg_settings['ai_icon_url']) && isset($wpaicg_settings['ai_icon']) && $wpaicg_settings['ai_icon'] == 'custom' ? wp_get_attachment_url(esc_html($wpaicg_settings['ai_icon_url'])) : WPAICG_PLUGIN_URL.'admin/images/chatbot.png'?>"
     data-ai-name="<?php echo esc_html($wpaicg_ai_name)?>"
     data-ai-bg-color="<?php echo esc_html($wpaicg_ai_bg_color)?>"
     data-nonce="<?php echo esc_html(wp_create_nonce( 'wpaicg-chatbox' ))?>"
     data-post-id="<?php echo get_the_ID()?>"
     data-url="<?php echo home_url( $wp->request )?>"
     data-bot-id="<?php echo esc_html($wpaicg_bot_id)?>"
     data-width="<?php echo esc_html($wpaicg_chat_widget_width)?>"
     data-height="<?php echo esc_html($wpaicg_chat_widget_height)?>"
     data-footer="<?php echo isset($wpaicg_settings['footer_text']) && !empty($wpaicg_settings['footer_text']) ? 'true' : 'false'?>"
     data-has-bar="<?php echo $wpaicg_has_action_bar ? 'true' : 'false'?>"
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
     data-type="shortcode"
>
    <?php
    if($wpaicg_has_action_bar):
        ?>
        <div class="wpaicg-chatbox-action-bar">
            <?php
            if($wpaicg_chat_download_btn):
                ?>
                <span class="wpaicg-chatbox-download-btn" data-type="shortcode">
            <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"  xml:space="preserve"><path class="st0" d="M243.591,309.362c3.272,4.317,7.678,6.692,12.409,6.692c4.73,0,9.136-2.376,12.409-6.689l89.594-118.094 c3.348-4.414,4.274-8.692,2.611-12.042c-1.666-3.35-5.631-5.198-11.168-5.198H315.14c-9.288,0-16.844-7.554-16.844-16.84V59.777 c0-11.04-8.983-20.027-20.024-20.027h-44.546c-11.04,0-20.022,8.987-20.022,20.027v97.415c0,9.286-7.556,16.84-16.844,16.84 h-34.305c-5.538,0-9.503,1.848-11.168,5.198c-1.665,3.35-0.738,7.628,2.609,12.046L243.591,309.362z"/><path class="st0" d="M445.218,294.16v111.304H66.782V294.16H0v152.648c0,14.03,11.413,25.443,25.441,25.443h461.118 c14.028,0,25.441-11.413,25.441-25.443V294.16H445.218z"/></svg>
        </span>
            <?php
            endif;
            ?>
            <?php
            if($wpaicg_chat_fullscreen):
                ?>
                <span data-type="shortcode" class="wpaicg-chatbox-fullscreen">
            <svg class="wpaicg-active-fullscreen" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 15H15V10H13.2V13.2H10V15ZM6 15V13.2H2.8V10H1V15H6ZM10 2.8H12.375H13.2V6H15V1H10V2.8ZM6 1V2.8H2.8V6H1V1H6Z"/></svg>
            <svg class="wpaicg-exit-fullscreen" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M1 6L6 6L6 1L4.2 1L4.2 4.2L1 4.2L1 6Z"/><path d="M15 10L10 10L10 15L11.8 15L11.8 11.8L15 11.8L15 10Z"/><path d="M6 15L6 10L1 10L1 11.8L4.2 11.8L4.2 15L6 15Z"/><path d="M10 1L10 6L15 6L15 4.2L11.8 4.2L11.8 1L10 1Z"/></svg>
        </span>
            <?php
            endif;
            ?>
        </div>
    <?php
    endif;
    ?>
    <div class="wpaicg-chat-shortcode-content">
        <ul class="wpaicg-chat-shortcode-messages">
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
            <li class="wpaicg-ai-message" style="color: <?php echo esc_html($wpaicg_font_color)?>; font-size: <?php echo esc_html($wpaicg_font_size)?>px; background-color: <?php echo esc_html($wpaicg_ai_bg_color);?>">
                <p>
                    <strong style="float: left" class="wpaicg-chat-avatar">
                        <?php
                        if(isset($wpaicg_settings['use_avatar']) && $wpaicg_settings['use_avatar']) {
                            echo wp_kses_post($wpaicg_ai_name);
                        }
                        else{
                            echo wp_kses_post($wpaicg_ai_name).':';
                        }
                        ?>
                    </strong>
                    <span class="wpaicg-chat-message">
                        <?php echo esc_html(str_replace("\\",'',$wpaicg_welcome_message))?>
                    </span>
                </p>
            </li>
        </ul>
        <span class="wpaicg-bot-thinking" style="color: <?php echo esc_html($wpaicg_thinking_color)?>;"><?php echo esc_html(str_replace("\\",'',$wpaicg_ai_thinking))?>&nbsp;<span class="wpaicg-jumping-dots"><span class="wpaicg-dot-1">.</span><span class="wpaicg-dot-2">.</span><span class="wpaicg-dot-3">.</span></span></span>
    </div>
    <div class="wpaicg-chat-shortcode-type">
        <textarea type="text" class="wpaicg-chat-shortcode-typing" placeholder="<?php echo esc_html(str_replace("\\",'',$wpaicg_typing_placeholder))?>"></textarea>
        <div class="wpaicg_chat_additions">
            <span class="wpaicg-mic-icon" data-type="shortcode" style="<?php echo $wpaicg_audio_enable ? '' : 'display:none'?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M176 0C123 0 80 43 80 96V256c0 53 43 96 96 96s96-43 96-96V96c0-53-43-96-96-96zM48 216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 89.1 66.2 162.7 152 174.4V464H104c-13.3 0-24 10.7-24 24s10.7 24 24 24h72 72c13.3 0 24-10.7 24-24s-10.7-24-24-24H200V430.4c85.8-11.7 152-85.3 152-174.4V216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 70.7-57.3 128-128 128s-128-57.3-128-128V216z"/></svg>
            </span>
            <?php
            if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                ?>
                <span class="wpaicg-pdf-icon" data-type="shortcode" style="<?php echo $wpaicg_pdf_enable ? '' : 'display:none'?>">
                <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"  xml:space="preserve"><path class="st0" d="M378.413,0H208.297h-13.182L185.8,9.314L57.02,138.102l-9.314,9.314v13.176v265.514 c0,47.36,38.528,85.895,85.896,85.895h244.811c47.353,0,85.881-38.535,85.881-85.895V85.896C464.294,38.528,425.766,0,378.413,0z M432.497,426.105c0,29.877-24.214,54.091-54.084,54.091H133.602c-29.884,0-54.098-24.214-54.098-54.091V160.591h83.716 c24.885,0,45.077-20.178,45.077-45.07V31.804h170.116c29.87,0,54.084,24.214,54.084,54.092V426.105z"/><path class="st0" d="M171.947,252.785h-28.529c-5.432,0-8.686,3.533-8.686,8.825v73.754c0,6.388,4.204,10.599,10.041,10.599 c5.711,0,9.914-4.21,9.914-10.599v-22.406c0-0.545,0.279-0.817,0.824-0.817h16.436c20.095,0,32.188-12.226,32.188-29.612 C204.136,264.871,192.182,252.785,171.947,252.785z M170.719,294.888h-15.208c-0.545,0-0.824-0.272-0.824-0.81v-23.23 c0-0.545,0.279-0.816,0.824-0.816h15.208c8.42,0,13.447,5.027,13.447,12.498C184.167,290,179.139,294.888,170.719,294.888z"/><path class="st0" d="M250.191,252.785h-21.868c-5.432,0-8.686,3.533-8.686,8.825v74.843c0,5.3,3.253,8.693,8.686,8.693h21.868 c19.69,0,31.923-6.249,36.81-21.324c1.76-5.3,2.723-11.681,2.723-24.857c0-13.175-0.964-19.557-2.723-24.856 C282.113,259.034,269.881,252.785,250.191,252.785z M267.856,316.896c-2.318,7.331-8.965,10.459-18.21,10.459h-9.23 c-0.545,0-0.824-0.272-0.824-0.816v-55.146c0-0.545,0.279-0.817,0.824-0.817h9.23c9.245,0,15.892,3.128,18.21,10.46 c0.95,3.128,1.62,8.56,1.62,17.93C269.476,308.336,268.805,313.768,267.856,316.896z"/><path class="st0" d="M361.167,252.785h-44.812c-5.432,0-8.7,3.533-8.7,8.825v73.754c0,6.388,4.218,10.599,10.055,10.599 c5.697,0,9.914-4.21,9.914-10.599v-26.351c0-0.538,0.265-0.81,0.81-0.81h26.086c5.837,0,9.23-3.532,9.23-8.56 c0-5.028-3.393-8.553-9.23-8.553h-26.086c-0.545,0-0.81-0.272-0.81-0.817v-19.425c0-0.545,0.265-0.816,0.81-0.816h32.733 c5.572,0,9.245-3.666,9.245-8.553C370.411,256.45,366.738,252.785,361.167,252.785z"/></svg>
            </span>
                <span class="wpaicg-pdf-loading" style="display: none"></span>
                <span data-type="shortcode" alt="<?php echo esc_html__('Clear','gpt3-ai-content-generator')?>" title="<?php echo esc_html__('Clear','gpt3-ai-content-generator')?>" class="wpaicg-pdf-remove" style="display: none">&times;</span>
                <input data-type="shortcode" data-limit="<?php echo esc_html($wpaicg_settings['pdf_pages'])?>" type="file" accept="application/pdf" class="wpaicg-pdf-file" style="display: none">
            <?php
            endif;
            ?>
        </div>
        <span class="wpaicg-chat-shortcode-send">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5004 11.9998H5.00043M4.91577 12.2913L2.58085 19.266C2.39742 19.8139 2.3057 20.0879 2.37152 20.2566C2.42868 20.4031 2.55144 20.5142 2.70292 20.5565C2.87736 20.6052 3.14083 20.4866 3.66776 20.2495L20.3792 12.7293C20.8936 12.4979 21.1507 12.3822 21.2302 12.2214C21.2993 12.0817 21.2993 11.9179 21.2302 11.7782C21.1507 11.6174 20.8936 11.5017 20.3792 11.2703L3.66193 3.74751C3.13659 3.51111 2.87392 3.39291 2.69966 3.4414C2.54832 3.48351 2.42556 3.59429 2.36821 3.74054C2.30216 3.90893 2.3929 4.18231 2.57437 4.72906L4.91642 11.7853C4.94759 11.8792 4.96317 11.9262 4.96933 11.9742C4.97479 12.0168 4.97473 12.0599 4.96916 12.1025C4.96289 12.1506 4.94718 12.1975 4.91577 12.2913Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
    </div>
    <?php
    if($wpaicg_include_footer):
        ?>
        <div class="wpaicg-chat-shortcode-footer">
            <?php
            echo esc_html(str_replace("\\",'',$wpaicg_settings['footer_text']));
            ?>
        </div>
    <?php
    endif;
    ?>
</div>
