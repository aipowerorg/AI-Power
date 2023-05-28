<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$errors = false;
$message = false;
if ( isset( $_POST['wpaicg_submit'] ) ) {
    check_admin_referer('wpaicg_chat_widget_save');
    if ( isset($_POST['wpaicg_chat_temperature']) && (!is_numeric( $_POST['wpaicg_chat_temperature'] ) || floatval( $_POST['wpaicg_chat_temperature'] ) < 0 || floatval( $_POST['wpaicg_chat_temperature'] ) > 1 )) {
        $errors = sprintf(esc_html__('Please enter a valid temperature value between %d and %d.','gpt3-ai-content-generator'),0,1);
    }
    if (isset($_POST['wpaicg_chat_max_tokens']) && ( !is_numeric( $_POST['wpaicg_chat_max_tokens'] ) || floatval( $_POST['wpaicg_chat_max_tokens'] ) < 64 || floatval( $_POST['wpaicg_chat_max_tokens'] ) > 8000 )) {
        $errors = sprintf(esc_html__('Please enter a valid max token value between %d and %d.','gpt3-ai-content-generator'),64,8000);
    }
    if (isset($_POST['wpaicg_chat_top_p']) && (!is_numeric( $_POST['wpaicg_chat_top_p'] ) || floatval( $_POST['wpaicg_chat_top_p'] ) < 0 || floatval( $_POST['wpaicg_chat_top_p'] ) > 1 )){
        $errors = sprintf(esc_html__('Please enter a valid top p value between %d and %d.','gpt3-ai-content-generator'),0,1);
    }
    if (isset($_POST['wpaicg_chat_best_of']) && ( !is_numeric( $_POST['wpaicg_chat_best_of'] ) || floatval( $_POST['wpaicg_chat_best_of'] ) < 1 || floatval( $_POST['wpaicg_chat_best_of'] ) > 20 )) {
        $errors = sprintf(esc_html__('Please enter a valid best of value between %d and %d.','gpt3-ai-content-generator'),1,20);
    }
    if (isset($_POST['wpaicg_chat_frequency_penalty']) && ( !is_numeric( $_POST['wpaicg_chat_frequency_penalty'] ) || floatval( $_POST['wpaicg_chat_frequency_penalty'] ) < 0 || floatval( $_POST['wpaicg_chat_frequency_penalty'] ) > 2 )) {
        $errors = sprintf(esc_html__('Please enter a valid frequency penalty value between %d and %d.','gpt3-ai-content-generator'),0,2);
    }
    if (isset($_POST['wpaicg_chat_presence_penalty']) && ( !is_numeric( $_POST['wpaicg_chat_presence_penalty'] ) || floatval( $_POST['wpaicg_chat_presence_penalty'] ) < 0 || floatval( $_POST['wpaicg_chat_presence_penalty'] ) > 2 ) ){
        $errors = sprintf(esc_html__('Please enter a valid presence penalty value between %d and %d.','gpt3-ai-content-generator'),0,2);
    }
    if(!$errors){
        $wpaicg_keys = array(
            '_wpaicg_chatbox_you',
            '_wpaicg_ai_thinking',
            '_wpaicg_typing_placeholder',
            '_wpaicg_chatbox_welcome_message',
            '_wpaicg_chatbox_ai_name',
            'wpaicg_chat_model',
            'wpaicg_chat_temperature',
            'wpaicg_chat_max_tokens',
            'wpaicg_chat_top_p',
            'wpaicg_chat_best_of',
            'wpaicg_chat_frequency_penalty',
            'wpaicg_chat_presence_penalty',
            'wpaicg_chat_widget',
            'wpaicg_chat_language',
            'wpaicg_conversation_cut',
            'wpaicg_chat_embedding',
            'wpaicg_chat_addition',
            'wpaicg_chat_addition_text',
            'wpaicg_chat_no_answer',
            'wpaicg_chat_embedding_type',
            'wpaicg_chat_embedding_top'
        );
        foreach($wpaicg_keys as $wpaicg_key){
            if(isset($_POST[$wpaicg_key]) && !empty($_POST[$wpaicg_key])){
                update_option($wpaicg_key, \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST[$wpaicg_key]));
            }
            else{
                delete_option($wpaicg_key);
            }
        }
        $message = esc_html__('Setting saved successfully','gpt3-ai-content-generator');
    }
}
wp_enqueue_script('wp-color-picker');
wp_enqueue_style('wp-color-picker');
$wpaicg_custom_models = get_option('wpaicg_custom_models',array());
$wpaicg_custom_models = array_merge(array('text-davinci-003','text-curie-001','text-babbage-001','text-ada-001'),$wpaicg_custom_models);
$table = $wpdb->prefix . 'wpaicg';
$existingValue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE name = %s", 'wpaicg_settings' ), ARRAY_A );
$wpaicg_chat_temperature = get_option('wpaicg_chat_temperature',$existingValue['temperature']);
$wpaicg_chat_max_tokens = get_option('wpaicg_chat_max_tokens',$existingValue['max_tokens']);
$wpaicg_chat_top_p = get_option('wpaicg_chat_top_p',$existingValue['top_p']);
$wpaicg_chat_best_of = get_option('wpaicg_chat_best_of',$existingValue['best_of']);
$wpaicg_chat_frequency_penalty = get_option('wpaicg_chat_frequency_penalty',$existingValue['frequency_penalty']);
$wpaicg_chat_presence_penalty = get_option('wpaicg_chat_presence_penalty',$existingValue['presence_penalty']);
$wpaicg_chat_widget = get_option('wpaicg_chat_widget',[]);
$wpaicg_chat_icon = isset($wpaicg_chat_widget['icon']) && !empty($wpaicg_chat_widget['icon']) ? $wpaicg_chat_widget['icon'] : 'default';
$wpaicg_chat_status = isset($wpaicg_chat_widget['status']) && !empty($wpaicg_chat_widget['status']) ? $wpaicg_chat_widget['status'] : '';
$wpaicg_chat_fontsize = isset($wpaicg_chat_widget['fontsize']) && !empty($wpaicg_chat_widget['fontsize']) ? $wpaicg_chat_widget['fontsize'] : '13';
$wpaicg_chat_fontcolor = isset($wpaicg_chat_widget['fontcolor']) && !empty($wpaicg_chat_widget['fontcolor']) ? $wpaicg_chat_widget['fontcolor'] : '#fff';
$wpaicg_chat_bgcolor = isset($wpaicg_chat_widget['bgcolor']) && !empty($wpaicg_chat_widget['bgcolor']) ? $wpaicg_chat_widget['bgcolor'] : '#222222';
$wpaicg_bg_text_field = isset($wpaicg_chat_widget['bg_text_field']) && !empty($wpaicg_chat_widget['bg_text_field']) ? $wpaicg_chat_widget['bg_text_field'] : '#fff';
$wpaicg_send_color = isset($wpaicg_chat_widget['send_color']) && !empty($wpaicg_chat_widget['send_color']) ? $wpaicg_chat_widget['send_color'] : '#fff';
$wpaicg_border_text_field = isset($wpaicg_chat_widget['border_text_field']) && !empty($wpaicg_chat_widget['border_text_field']) ? $wpaicg_chat_widget['border_text_field'] : '#ccc';
$wpaicg_footer_text = isset($wpaicg_chat_widget['footer_text']) && !empty($wpaicg_chat_widget['footer_text']) ? $wpaicg_chat_widget['footer_text'] : '';
$wpaicg_user_bg_color = isset($wpaicg_chat_widget['user_bg_color']) && !empty($wpaicg_chat_widget['user_bg_color']) ? $wpaicg_chat_widget['user_bg_color'] : '#444654';
$wpaicg_ai_bg_color = isset($wpaicg_chat_widget['ai_bg_color']) && !empty($wpaicg_chat_widget['ai_bg_color']) ? $wpaicg_chat_widget['ai_bg_color'] : '#343541';
$wpaicg_bar_color = isset($wpaicg_chat_widget['bar_color']) && !empty($wpaicg_chat_widget['bar_color']) ? $wpaicg_chat_widget['bar_color'] : '#fff';
$wpaicg_use_avatar = isset($wpaicg_chat_widget['use_avatar']) && !empty($wpaicg_chat_widget['use_avatar']) ? $wpaicg_chat_widget['use_avatar'] : false;
$wpaicg_ai_avatar = isset($wpaicg_chat_widget['ai_avatar']) && !empty($wpaicg_chat_widget['ai_avatar']) ? $wpaicg_chat_widget['ai_avatar'] : 'default';
$wpaicg_ai_avatar_id = isset($wpaicg_chat_widget['ai_avatar_id']) && !empty($wpaicg_chat_widget['ai_avatar_id']) ? $wpaicg_chat_widget['ai_avatar_id'] : '';
$wpaicg_chat_width = isset($wpaicg_chat_widget['width']) && !empty($wpaicg_chat_widget['width']) ? $wpaicg_chat_widget['width'] : '350';
$wpaicg_chat_height = isset($wpaicg_chat_widget['height']) && !empty($wpaicg_chat_widget['height']) ? $wpaicg_chat_widget['height'] : '400';
$wpaicg_chat_position = isset($wpaicg_chat_widget['position']) && !empty($wpaicg_chat_widget['position']) ? $wpaicg_chat_widget['position'] : 'left';
$wpaicg_chat_tone = isset($wpaicg_chat_widget['tone']) && !empty($wpaicg_chat_widget['tone']) ? $wpaicg_chat_widget['tone'] : 'friendly';
$wpaicg_user_aware = isset($wpaicg_chat_widget['user_aware']) && !empty($wpaicg_chat_widget['user_aware']) ? $wpaicg_chat_widget['user_aware'] : 'no';
$wpaicg_chat_proffesion = isset($wpaicg_chat_widget['proffesion']) && !empty($wpaicg_chat_widget['proffesion']) ? $wpaicg_chat_widget['proffesion'] : 'none';
$wpaicg_chat_remember_conversation = isset($wpaicg_chat_widget['remember_conversation']) && !empty($wpaicg_chat_widget['remember_conversation']) ? $wpaicg_chat_widget['remember_conversation'] : 'yes';
$wpaicg_chat_content_aware = isset($wpaicg_chat_widget['content_aware']) && !empty($wpaicg_chat_widget['content_aware']) ? $wpaicg_chat_widget['content_aware'] : 'yes';
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
$wpaicg_save_logs = isset($wpaicg_chat_widget['save_logs']) && !empty($wpaicg_chat_widget['save_logs']) ? $wpaicg_chat_widget['save_logs'] : false;
$wpaicg_log_notice = isset($wpaicg_chat_widget['log_notice']) && !empty($wpaicg_chat_widget['log_notice']) ? $wpaicg_chat_widget['log_notice'] : false;
$wpaicg_log_notice_message = isset($wpaicg_chat_widget['log_notice_message']) && !empty($wpaicg_chat_widget['log_notice_message']) ? $wpaicg_chat_widget['log_notice_message'] : esc_html__('Please note that your conversations will be recorded.','gpt3-ai-content-generator');
$wpaicg_conversation_cut = get_option('wpaicg_conversation_cut',10);
$wpaicg_embedding_field_disabled = empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment) ? true : false;
$wpaicg_chat_embedding = get_option('wpaicg_chat_embedding',false);
$wpaicg_chat_addition = get_option('wpaicg_chat_addition',false);
$wpaicg_chat_addition_text = get_option('wpaicg_chat_addition_text',false);
$wpaicg_chat_addition_text = str_replace("\\",'',$wpaicg_chat_addition_text);
$wpaicg_chat_embedding_type = get_option('wpaicg_chat_embedding_type',false);
$wpaicg_chat_embedding_top = get_option('wpaicg_chat_embedding_top',false);
$wpaicg_audio_enable = isset($wpaicg_chat_widget['audio_enable']) ? $wpaicg_chat_widget['audio_enable'] : false;
$wpaicg_mic_color = isset($wpaicg_chat_widget['mic_color']) ? $wpaicg_chat_widget['mic_color'] : '#222';
$wpaicg_stop_color = isset($wpaicg_chat_widget['stop_color']) ? $wpaicg_chat_widget['stop_color'] : '#f00';
$wpaicg_user_limited = isset($wpaicg_chat_widget['user_limited']) ? $wpaicg_chat_widget['user_limited'] : false;
$wpaicg_guest_limited = isset($wpaicg_chat_widget['guest_limited']) ? $wpaicg_chat_widget['guest_limited'] : false;
$wpaicg_user_tokens = isset($wpaicg_chat_widget['user_tokens']) ? $wpaicg_chat_widget['user_tokens'] : 0;
$wpaicg_guest_tokens = isset($wpaicg_chat_widget['guest_tokens']) ? $wpaicg_chat_widget['guest_tokens'] : 0;
$wpaicg_reset_limit = isset($wpaicg_chat_widget['reset_limit']) ? $wpaicg_chat_widget['reset_limit'] : 0;
$wpaicg_limited_message = isset($wpaicg_chat_widget['limited_message']) && !empty($wpaicg_chat_widget['limited_message']) ? $wpaicg_chat_widget['limited_message'] : esc_html__('You have reached your token limit.','gpt3-ai-content-generator');
$wpaicg_include_footer = (isset($wpaicg_chat_widget['footer_text']) && !empty($wpaicg_chat_widget['footer_text'])) ? 5 : 0;
$wpaicg_chat_widget['role_limited'] = isset($wpaicg_chat_widget['role_limited']) && !empty($wpaicg_chat_widget['role_limited']) ? $wpaicg_chat_widget['role_limited'] : false;
$wpaicg_chat_widget['limited_roles'] = isset($wpaicg_chat_widget['limited_roles']) && !empty($wpaicg_chat_widget['limited_roles']) ? $wpaicg_chat_widget['limited_roles'] : array();
$wpaicg_chat_fullscreen = isset($wpaicg_chat_widget['fullscreen']) && !empty($wpaicg_chat_widget['fullscreen']) ? $wpaicg_chat_widget['fullscreen'] : false;
$wpaicg_chat_close_btn = isset($wpaicg_chat_widget['close_btn']) && !empty($wpaicg_chat_widget['close_btn']) ? $wpaicg_chat_widget['close_btn'] : false;
$wpaicg_chat_download_btn = isset($wpaicg_chat_widget['download_btn']) && !empty($wpaicg_chat_widget['download_btn']) ? $wpaicg_chat_widget['download_btn'] : false;
$wpaicg_thinking_color = isset($wpaicg_chat_widget['thinking_color']) && !empty($wpaicg_chat_widget['thinking_color']) ? $wpaicg_chat_widget['thinking_color'] : '#fff';
$wpaicg_delay_time = isset($wpaicg_chat_widget['delay_time']) && !empty($wpaicg_chat_widget['delay_time']) ? $wpaicg_chat_widget['delay_time'] : '';
$wpaicg_chat_to_speech = isset($wpaicg_chat_widget['chat_to_speech']) ? $wpaicg_chat_widget['chat_to_speech'] : false;
$wpaicg_elevenlabs_voice = isset($wpaicg_chat_widget['elevenlabs_voice']) ? $wpaicg_chat_widget['elevenlabs_voice'] : '';
$wpaicg_text_height = isset($wpaicg_chat_widget['text_height']) && !empty($wpaicg_chat_widget['text_height']) ? $wpaicg_chat_widget['text_height'] : 60;
$wpaicg_text_rounded = isset($wpaicg_chat_widget['text_rounded']) && !empty($wpaicg_chat_widget['text_height']) ? $wpaicg_chat_widget['text_rounded'] : 20;
$wpaicg_chat_rounded = isset($wpaicg_chat_widget['chat_rounded']) && !empty($wpaicg_chat_widget['text_height']) ? $wpaicg_chat_widget['chat_rounded'] : 20;
$wpaicg_elevenlabs_api = get_option('wpaicg_elevenlabs_api', '');
$wpaicg_chat_voice_service = isset($wpaicg_chat_widget['voice_service']) ? $wpaicg_chat_widget['voice_service'] : '';
$wpaicg_google_voices = get_option('wpaicg_google_voices',[]);
$wpaicg_roles = wp_roles()->get_names();
$wpaicg_google_api_key = get_option('wpaicg_google_api_key', '');
$wpaicg_pinecone_indexes = get_option('wpaicg_pinecone_indexes','');
$wpaicg_pinecone_indexes = empty($wpaicg_pinecone_indexes) ? array() : json_decode($wpaicg_pinecone_indexes,true);
?>
<style>
    .asdisabled{
        background: #ebebeb!important;
    }
    .wpaicg_chatbox_avatar{
        cursor: pointer;
    }
    .wp-picker-holder{
        position: absolute;
    }
    .wpaicg_chatbox_icon{
        cursor: pointer;
    }
    .wpaicg_chatbox_icon svg{
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox{
        height: 100%;
        background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>;
        border-radius: 5px;
    }
    .wpaicg_widget_open .wpaicg_chat_widget_content{
        height: <?php echo esc_html($wpaicg_chat_height)?>px;
    }
    .wpaicg_chat_widget_content{
        position: absolute;
        bottom: calc(100% + 15px);
        width: <?php echo esc_html($wpaicg_chat_width)?>px;

    }
    .wpaicg-collapse-content textarea{
        display: inline-block!important;
        width: 48%!important;
    }
    .wpaicg_widget_open .wpaicg_chat_widget_content .wpaicg-chatbox{
        top: 0;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox{
        position: absolute;
        top: 100%;
        left: 0;
        width: <?php echo esc_html($wpaicg_chat_width)?>px;
        height: <?php echo esc_html($wpaicg_chat_height)?>px;
        transition: top 300ms cubic-bezier(0.17, 0.04, 0.03, 0.94);
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-content ul{
        box-sizing: border-box;
        background: <?php echo esc_html($wpaicg_chat_bgcolor)?>;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-content ul li{
        color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
        font-size: <?php echo esc_html($wpaicg_chat_fontsize)?>px;
    }
    .wpaicg_chat_widget_content .wpaicg-bot-thinking{
        color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-type{
    <?php
    if($wpaicg_include_footer):
    ?>
        padding: 5px 5px 0 5px;
    <?php
    endif;
    ?>
        border-top: 0;
        background: rgb(0 0 0 / 19%);
    }
    .wpaicg_chat_widget_content .wpaicg-chat-message{
        color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
    }
    .wpaicg_chat_widget_content textarea.wpaicg-chatbox-typing{
        background-color: <?php echo esc_html($wpaicg_bg_text_field)?>;
        border-color: <?php echo esc_html($wpaicg_border_text_field)?>;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-send{
        color: <?php echo esc_html($wpaicg_send_color)?>;
    }
    .wpaicg-chatbox-footer{
        height: 18px;
        font-size: 11px;
        padding: 0 5px;
        color: <?php echo esc_html($wpaicg_send_color)?>;
        background: rgb(0 0 0 / 19%);
        margin-top:2px;
        margin-bottom: 2px;
    }
    .wpaicg_chat_widget_content textarea.wpaicg-chatbox-typing:focus{
        outline: none;
    }
    .wpaicg_chat_widget .wpaicg_toggle{
        cursor: pointer;
    }
    .wpaicg_chat_widget .wpaicg_toggle img{
        width: 75px;
        height: 75px;
    }
    .wpaicg-chat-shortcode-type,.wpaicg-chatbox-type{
        position: relative;
    }
    .wpaicg-mic-icon{
        cursor: pointer;
    }
    .wp-picker-input-wrap input[type=text]{
        width: 4rem!important;
    }
    .wpaicg-mic-icon svg{
        width: 16px;
        height: 16px;
        fill: currentColor;
    }
    .wpaicg-chatbox-preview{
        position: relative;
    }
    /*.wpaicg_chat_widget_content{*/
    /*    position: relative;*/
    /*    overflow: unset;*/
    /*}*/
    /*.wpaicg_chat_widget_content .wpaicg-chatbox{*/
    /*    position: relative;*/
    /*}*/
    .wpaicg_toggle{}
    .wpaicg_chat_widget{
        position: absolute;
        bottom: 0;
    }
    .wpaicg_chat_widget_content .wpaicg-chat-ai-message *, .wpaicg_chat_widget_content .wpaicg-chat-user-message *, .wpaicg_chat_widget_content .wpaicg-chat-user-message .wpaicg-chat-message, .wpaicg_chat_widget_content .wpaicg-chat-ai-message .wpaicg-chat-message, .wpaicg_chat_widget_content .wpaicg-chat-ai-message a, .wpaicg_chat_widget_content .wpaicg-chat-user-message a{
        color: inherit!important;
        font-size: inherit!important;
    }
    .wpaicg_chat_widget_content{
        overflow: hidden;
    }
    .wpaicg_widget_open .wpaicg_chat_widget_content{
        overflow: unset;
    }
</style>
<div class="wpaicg-alert mb-5">
    <p><?php echo esc_html__('Learn how you can train the chat bot with your content','gpt3-ai-content-generator')?> <u><b><a href="https://docs.aipower.org/docs/ChatGPT/chatgpt-wordpress" target="_blank"><?php echo esc_html__('here','gpt3-ai-content-generator')?></a></u></b>.</p>
</div>
<?php
$wpaicg_chat_model = get_option('wpaicg_chat_model','');
$wpaicg_chat_language = get_option('wpaicg_chat_language','');
if ( !empty($errors)) {
    echo  "<h4 id='setting_message' style='color: red;'>" . esc_html( $errors ) . "</h4>" ;
} elseif(!empty($message)) {
    echo  "<h4 id='setting_message' style='color: green;'>" . esc_html( $message ) . "</h4>" ;
}
?>
<div class="wpaicg-grid-three">
    <div class="wpaicg-grid-2 wpaicg-chatbox-preview">
        <div class="wpaicg-chatbox-preview-box" style="position: relative;">
            <?php
            include __DIR__.'/wpaicg_chat_widget.php';
            ?>
        </div>
    </div>
    <div class="wpaicg-grid-1">
        <form action="" method="post" id="form-chatbox-setting">
            <?php
            wp_nonce_field('wpaicg_chat_widget_save');
            ?>
            <div class="wpaicg-collapse wpaicg-collapse-active">
                <div class="wpaicg-collapse-title"><span>-</span> <?php echo esc_html__('Enable','gpt3-ai-content-generator')?> / <?php echo esc_html__('Disable','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Enable Widget','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_widget[status]">
                            <option value="">No</option>
                            <option<?php echo $wpaicg_chat_status == 'active' ? ' selected': ''?> value="active">Yes</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> <?php echo esc_html__('Language, Tone and Profession','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Language:</label>
                        <select class="wpaicg-input" id="label_wpai_language"  name="wpaicg_chat_language" >
                            <option value="en" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'en' ? 'selected' : '' ) ;
                            ?>>English</option>
                            <option value="af" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'af' ? 'selected' : '' ) ;
                            ?>>Afrikaans</option>
                            <option value="ar" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ar' ? 'selected' : '' ) ;
                            ?>>Arabic</option>
                            <option value="bg" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'bg' ? 'selected' : '' ) ;
                            ?>>Bulgarian</option>
                            <option value="zh" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'zh' ? 'selected' : '' ) ;
                            ?>>Chinese</option>
                            <option value="hr" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'hr' ? 'selected' : '' ) ;
                            ?>>Croatian</option>
                            <option value="cs" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'cs' ? 'selected' : '' ) ;
                            ?>>Czech</option>
                            <option value="da" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'da' ? 'selected' : '' ) ;
                            ?>>Danish</option>
                            <option value="nl" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'nl' ? 'selected' : '' ) ;
                            ?>>Dutch</option>
                            <option value="et" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'et' ? 'selected' : '' ) ;
                            ?>>Estonian</option>
                            <option value="fil" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'fil' ? 'selected' : '' ) ;
                            ?>>Filipino</option>
                            <option value="fi" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'fi' ? 'selected' : '' ) ;
                            ?>>Finnish</option>
                            <option value="fr" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'fr' ? 'selected' : '' ) ;
                            ?>>French</option>
                            <option value="de" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'de' ? 'selected' : '' ) ;
                            ?>>German</option>
                            <option value="el" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'el' ? 'selected' : '' ) ;
                            ?>>Greek</option>
                            <option value="he" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'he' ? 'selected' : '' ) ;
                            ?>>Hebrew</option>
                            <option value="hi" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'hi' ? 'selected' : '' ) ;
                            ?>>Hindi</option>
                            <option value="hu" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'hu' ? 'selected' : '' ) ;
                            ?>>Hungarian</option>
                            <option value="id" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'id' ? 'selected' : '' ) ;
                            ?>>Indonesian</option>
                            <option value="it" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'it' ? 'selected' : '' ) ;
                            ?>>Italian</option>
                            <option value="ja" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ja' ? 'selected' : '' ) ;
                            ?>>Japanese</option>
                            <option value="ko" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ko' ? 'selected' : '' ) ;
                            ?>>Korean</option>
                            <option value="lv" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'lv' ? 'selected' : '' ) ;
                            ?>>Latvian</option>
                            <option value="lt" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'lt' ? 'selected' : '' ) ;
                            ?>>Lithuanian</option>
                            <option value="ms" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ms' ? 'selected' : '' ) ;
                            ?>>Malay</option>
                            <option value="no" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'no' ? 'selected' : '' ) ;
                            ?>>Norwegian</option>
                            <option value="fa" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'fa' ? 'selected' : '' ) ;
                            ?>>Persian</option>
                            <option value="pl" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'pl' ? 'selected' : '' ) ;
                            ?>>Polish</option>
                            <option value="pt" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'pt' ? 'selected' : '' ) ;
                            ?>>Portuguese</option>
                            <option value="ro" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ro' ? 'selected' : '' ) ;
                            ?>>Romanian</option>
                            <option value="ru" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ru' ? 'selected' : '' ) ;
                            ?>>Russian</option>
                            <option value="sr" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'sr' ? 'selected' : '' ) ;
                            ?>>Serbian</option>
                            <option value="sk" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'sk' ? 'selected' : '' ) ;
                            ?>>Slovak</option>
                            <option value="sl" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'sl' ? 'selected' : '' ) ;
                            ?>>Slovenian</option>
                            <option value="sv" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'sv' ? 'selected' : '' ) ;
                            ?>>Swedish</option>
                            <option value="es" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'es' ? 'selected' : '' ) ;
                            ?>>Spanish</option>
                            <option value="th" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'th' ? 'selected' : '' ) ;
                            ?>>Thai</option>
                            <option value="tr" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'tr' ? 'selected' : '' ) ;
                            ?>>Turkish</option>
                            <option value="uk" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'uk' ? 'selected' : '' ) ;
                            ?>>Ukrainian</option>
                            <option value="vi" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'vi' ? 'selected' : '' ) ;
                            ?>>Vietnamese</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Tone','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_widget[tone]">
                            <option<?php echo $wpaicg_chat_tone == 'friendly' ? ' selected': ''?> value="friendly"><?php echo esc_html__('Friendly','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_tone == 'professional' ? ' selected': ''?> value="professional"><?php echo esc_html__('Professional','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_tone == 'sarcastic' ? ' selected': ''?> value="sarcastic"><?php echo esc_html__('Sarcastic','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_tone == 'humorous' ? ' selected': ''?> value="humorous"><?php echo esc_html__('Humorous','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_tone == 'cheerful' ? ' selected': ''?> value="cheerful"><?php echo esc_html__('Cheerful','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_tone == 'anecdotal' ? ' selected': ''?> value="anecdotal"><?php echo esc_html__('Anecdotal','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Act As:</label>
                        <select name="wpaicg_chat_widget[proffesion]">
                            <option<?php echo $wpaicg_chat_proffesion == 'none' ? ' selected': ''?> value="none"><?php echo esc_html__('None','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'accountant' ? ' selected': ''?> value="accountant"><?php echo esc_html__('Accountant','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'advertisingspecialist' ? ' selected': ''?> value="advertisingspecialist"><?php echo esc_html__('Advertising Specialist','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'architect' ? ' selected': ''?> value="architect"><?php echo esc_html__('Architect','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'artist' ? ' selected': ''?> value="artist"><?php echo esc_html__('Artist','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'blogger' ? ' selected': ''?> value="blogger"><?php echo esc_html__('Blogger','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'businessanalyst' ? ' selected': ''?> value="businessanalyst"><?php echo esc_html__('Business Analyst','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'businessowner' ? ' selected': ''?> value="businessowner"><?php echo esc_html__('Business Owner','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'carexpert' ? ' selected': ''?> value="carexpert"><?php echo esc_html__('Car Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'consultant' ? ' selected': ''?> value="consultant"><?php echo esc_html__('Consultant','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'counselor' ? ' selected': ''?> value="counselor"><?php echo esc_html__('Counselor','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'cryptocurrencytrader' ? ' selected': ''?> value="cryptocurrencytrader"><?php echo esc_html__('Cryptocurrency Trader','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'cryptocurrencyexpert' ? ' selected': ''?> value="cryptocurrencyexpert"><?php echo esc_html__('Cryptocurrency Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'customersupport' ? ' selected': ''?> value="customersupport"><?php echo esc_html__('Customer Support','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'designer' ? ' selected': ''?> value="designer"><?php echo esc_html__('Designer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'digitalmarketinagency' ? ' selected': ''?> value="digitalmarketinagency"><?php echo esc_html__('Digital Marketing Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'editor' ? ' selected': ''?> value="editor"><?php echo esc_html__('Editor','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'engineer' ? ' selected': ''?> value="engineer"><?php echo esc_html__('Engineer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'eventplanner' ? ' selected': ''?> value="eventplanner"><?php echo esc_html__('Event Planner','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'freelancer' ? ' selected': ''?> value="freelancer"><?php echo esc_html__('Freelancer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'insuranceagent' ? ' selected': ''?> value="insuranceagent"><?php echo esc_html__('Insurance Agent','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'insurancebroker' ? ' selected': ''?> value="insurancebroker"><?php echo esc_html__('Insurance Broker','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'interiordesigner' ? ' selected': ''?> value="interiordesigner"><?php echo esc_html__('Interior Designer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'journalist' ? ' selected': ''?> value="journalist"><?php echo esc_html__('Journalist','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'marketingagency' ? ' selected': ''?> value="marketingagency"><?php echo esc_html__('Marketing Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'marketingexpert' ? ' selected': ''?> value="marketingexpert"><?php echo esc_html__('Marketing Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'marketingspecialist' ? ' selected': ''?> value="marketingspecialist"><?php echo esc_html__('Marketing Specialist','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'photographer' ? ' selected': ''?> value="photographer"><?php echo esc_html__('Photographer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'programmer' ? ' selected': ''?> value="programmer"><?php echo esc_html__('Programmer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'publicrelationsagency' ? ' selected': ''?> value="publicrelationsagency"><?php echo esc_html__('Public Relations Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'publisher' ? ' selected': ''?> value="publisher"><?php echo esc_html__('Publisher','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'realestateagent' ? ' selected': ''?> value="realestateagent"><?php echo esc_html__('Real Estate Agent','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'recruiter' ? ' selected': ''?> value="recruiter"><?php echo esc_html__('Recruiter','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'reporter' ? ' selected': ''?> value="reporter"><?php echo esc_html__('Reporter','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'salesperson' ? ' selected': ''?> value="salesperson"><?php echo esc_html__('Sales Person','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'salerep' ? ' selected': ''?> value="salerep"><?php echo esc_html__('Sales Representative','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'seoagency' ? ' selected': ''?> value="seoagency"><?php echo esc_html__('SEO Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'seoexpert' ? ' selected': ''?> value="seoexpert"><?php echo esc_html__('SEO Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'socialmediaagency' ? ' selected': ''?> value="socialmediaagency"><?php echo esc_html__('Social Media Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'student' ? ' selected': ''?> value="student"><?php echo esc_html__('Student','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'teacher' ? ' selected': ''?> value="teacher"><?php echo esc_html__('Teacher','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'technicalsupport' ? ' selected': ''?> value="technicalsupport"><?php echo esc_html__('Technical Support','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'trainer' ? ' selected': ''?> value="trainer"><?php echo esc_html__('Trainer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'travelagency' ? ' selected': ''?> value="travelagency"><?php echo esc_html__('Travel Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'videographer' ? ' selected': ''?> value="videographer"><?php echo esc_html__('Videographer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdesignagency' ? ' selected': ''?> value="webdesignagency"><?php echo esc_html__('Web Design Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdesignexpert' ? ' selected': ''?> value="webdesignexpert"><?php echo esc_html__('Web Design Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdevelopmentagency' ? ' selected': ''?> value="webdevelopmentagency"><?php echo esc_html__('Web Development Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdevelopmentexpert' ? ' selected': ''?> value="webdevelopmentexpert"><?php echo esc_html__('Web Development Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdesigner' ? ' selected': ''?> value="webdesigner"><?php echo esc_html__('Web Designer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdeveloper' ? ' selected': ''?> value="webdeveloper"><?php echo esc_html__('Web Developer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_proffesion == 'writer' ? ' selected': ''?> value="writer"><?php echo esc_html__('Writer','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                </div>
            </div>
            <!--Style-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> <?php echo esc_html__('Style','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Icon (75x75)','gpt3-ai-content-generator')?>:</label>
                        <div style="display: inline-flex; align-items: center">
                            <input<?php echo $wpaicg_chat_icon == 'default' ? ' checked': ''?> class="wpaicg_chatbox_icon_default" type="radio" value="default" name="wpaicg_chat_widget[icon]">
                            <div style="text-align: center">
                                <img style="display: block" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>"<br>
                                <strong><?php echo esc_html__('Default','gpt3-ai-content-generator')?></strong>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input<?php echo $wpaicg_chat_icon == 'custom' ? ' checked': ''?> type="radio" class="wpaicg_chatbox_icon_custom" value="custom" name="wpaicg_chat_widget[icon]">
                            <div style="text-align: center">
                                <div class="wpaicg_chatbox_icon">
                                    <?php
                                    $wpaicg_chat_icon_url = isset($wpaicg_chat_widget['icon_url']) && !empty($wpaicg_chat_widget['icon_url']) ? $wpaicg_chat_widget['icon_url'] : '';
                                    if(!empty($wpaicg_chat_icon_url) && $wpaicg_chat_icon == 'custom'):
                                        $wpaicg_chatbox_icon_url = wp_get_attachment_url($wpaicg_chat_icon_url);
                                        ?>
                                        <img src="<?php echo esc_html($wpaicg_chatbox_icon_url)?>" width="75" height="75">
                                    <?php
                                    else:
                                        ?>
                                        <svg width="60px" height="60px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M246.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 109.3V320c0 17.7 14.3 32 32 32s32-14.3 32-32V109.3l73.4 73.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-128-128zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 53 43 96 96 96H352c53 0 96-43 96-96V352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V352z"/></svg><br>
                                    <?php
                                    endif;
                                    ?>
                                </div>
                                <strong><?php echo esc_html__('Custom','gpt3-ai-content-generator')?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Font Size','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_widget[fontsize]" class="wpaicg_chat_widget_font_size">
                            <?php
                            for($i = 10; $i <= 30; $i++){
                                echo '<option'.($wpaicg_chat_fontsize == $i ? ' selected': '').' value="'.esc_html($i).'">'.esc_html($i).'px</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Width','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_width)?>" style="width: 100px;" class="wpaicg_chat_widget_width" min="100" type="text" name="wpaicg_chat_widget[width]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Height','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_height)?>" style="width: 100px;" class="wpaicg_chat_widget_height" min="100" type="text" name="wpaicg_chat_widget[height]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Font Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_fontcolor)?>" type="text" class="wpaicgchat_color wpaicgchat_font_color" name="wpaicg_chat_widget[fontcolor]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Background Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_bgcolor)?>" type="text" class="wpaicgchat_color wpaicgchat_bg_color" name="wpaicg_chat_widget[bgcolor]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Border Radius - Window','gpt3-ai-content-generator')?>:</label>
                        <input style="width: 80px" value="<?php echo esc_html($wpaicg_chat_rounded)?>" type="number" min="0" class="wpaicg_chat_rounded" name="wpaicg_chat_widget[chat_rounded]">px
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Text Field Height','gpt3-ai-content-generator')?>:</label>
                        <input style="width: 80px" value="<?php echo esc_html($wpaicg_text_height)?>" type="number" min="30" class="wpaicg_text_height" name="wpaicg_chat_widget[text_height]">px
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Border Radius - Text Field','gpt3-ai-content-generator')?>:</label>
                        <input style="width: 80px" value="<?php echo esc_html($wpaicg_text_rounded)?>" type="number" min="0" class="wpaicg_text_rounded" name="wpaicg_chat_widget[text_rounded]">px
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Text Field Background','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_bg_text_field)?>" type="text" class="wpaicgchat_color wpaicgchat_input_color" name="wpaicg_chat_widget[bg_text_field]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Text Field Border','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_border_text_field)?>" type="text" class="wpaicgchat_color wpaicgchat_input_border" name="wpaicg_chat_widget[border_text_field]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Button Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_send_color)?>" type="text" class="wpaicgchat_color wpaicgchat_send_color" name="wpaicg_chat_widget[send_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('User Background Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_user_bg_color)?>" type="text" class="wpaicgchat_color wpaicgchat_user_color" name="wpaicg_chat_widget[user_bg_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Background Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_ai_bg_color)?>" type="text" class="wpaicgchat_color wpaicgchat_ai_color" name="wpaicg_chat_widget[ai_bg_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Use Avatars','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_use_avatar ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_use_avatar" name="wpaicg_chat_widget[use_avatar]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Avatar (40x40)','gpt3-ai-content-generator')?>:</label>
                        <div style="display: inline-flex; align-items: center">
                            <input<?php echo $wpaicg_ai_avatar == 'default' ? ' checked': ''?> class="wpaicg_chatbox_avatar_default" type="radio" value="default" name="wpaicg_chat_widget[ai_avatar]">
                            <div style="text-align: center">
                                <img style="display: block;width: 40px; height: 40px" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>"<br>
                                <strong><?php echo esc_html__('Default','gpt3-ai-content-generator')?></strong>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input<?php echo $wpaicg_ai_avatar == 'custom' ? ' checked': ''?> type="radio" class="wpaicg_chatbox_avatar_custom" value="custom" name="wpaicg_chat_widget[ai_avatar]">
                            <div style="text-align: center">
                                <div class="wpaicg_chatbox_avatar">
                                    <?php
                                    if(!empty($wpaicg_ai_avatar_id) && $wpaicg_ai_avatar == 'custom'):
                                        $wpaicg_ai_avatar_url = wp_get_attachment_url($wpaicg_ai_avatar_id);
                                        ?>
                                        <img src="<?php echo esc_html($wpaicg_ai_avatar_url)?>" width="40" height="40">
                                    <?php
                                    else:
                                        ?>
                                        <svg width="40px" height="40px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M246.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 109.3V320c0 17.7 14.3 32 32 32s32-14.3 32-32V109.3l73.4 73.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-128-128zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 53 43 96 96 96H352c53 0 96-43 96-96V352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V352z"/></svg><br>
                                    <?php
                                    endif;
                                    ?>
                                </div>
                                <strong><?php echo esc_html__('Custom','gpt3-ai-content-generator')?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Position','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_position == 'left' ? ' checked': ''?> type="radio" value="left" name="wpaicg_chat_widget[position]"> <?php echo esc_html__('Left','gpt3-ai-content-generator')?>
                        <input<?php echo $wpaicg_chat_position == 'right' ? ' checked': ''?> type="radio" value="right" name="wpaicg_chat_widget[position]"> <?php echo esc_html__('Right','gpt3-ai-content-generator')?>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Fullscreen Button','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_fullscreen ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_fullscreen" name="wpaicg_chat_widget[fullscreen]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Close Button','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_close_btn ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_close_btn" name="wpaicg_chat_widget[close_btn]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Download Button','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_download_btn ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_download_btn" name="wpaicg_chat_widget[download_btn]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Bar Icons Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_bar_color)?>" type="text" class="wpaicgchat_color wpaicgchat_bar_color" name="wpaicg_chat_widget[bar_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Thinking Text Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_thinking_color)?>" type="text" class="wpaicgchat_color wpaicgchat_thinking_color" name="wpaicg_chat_widget[thinking_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Delay time','gpt3-ai-content-generator')?>:</label>
                        <input placeholder="<?php echo esc_html__('in seconds. eg. 5','gpt3-ai-content-generator')?>" value="<?php echo esc_html($wpaicg_delay_time)?>" type="text" class="wpaicgchat_delay_time" name="wpaicg_chat_widget[delay_time]">
                    </div>
                    <?php
                    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('PDF Icon Color','gpt3-ai-content-generator')?>:</label>
                            <input value="<?php echo isset($wpaicg_chat_widget['pdf_color']) ? esc_html($wpaicg_chat_widget['pdf_color']): '#222'?>" type="text" class="wpaicgchat_color wpaicg_pdf_color" name="wpaicg_chat_widget[pdf_color]">
                        </div>
                    <?php
                    else:
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('PDF Icon Color','gpt3-ai-content-generator')?>:</label>
                            <?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
            <!--Parameters-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> <?php echo esc_html__('Parameters','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label" for="wpaicg_chat_model"><?php echo esc_html__('Model','gpt3-ai-content-generator')?>:</label>
                        <select class="regular-text" id="wpaicg_chat_model"  name="wpaicg_chat_model" >
                            <?php
                            if(!in_array('gpt-3.5-turbo',$wpaicg_custom_models)) {
                                array_unshift($wpaicg_custom_models, 'gpt-3.5-turbo');
                            }
                            if(!in_array('gpt-4',$wpaicg_custom_models)) {
                                $wpaicg_custom_models[] = 'gpt-4';
                            }
                            if(!in_array('gpt-4-32k',$wpaicg_custom_models)) {
                                $wpaicg_custom_models[] = 'gpt-4-32k';
                            }
                            foreach($wpaicg_custom_models as $wpaicg_custom_model){
                                echo '<option'.($wpaicg_chat_model == $wpaicg_custom_model ? ' selected':'').' value="'.esc_html($wpaicg_custom_model).'">'.esc_html($wpaicg_custom_model).'</option>';
                            }
                            ?>
                        </select>
                        <a class="wpaicg_sync_finetune" href="javascript:void(0)"><?php echo esc_html__('Sync','gpt3-ai-content-generator')?></a>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Temperature','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_temperature" name="wpaicg_chat_temperature" value="<?php
                        echo  esc_html( $wpaicg_chat_temperature ) ;
                        ?>">
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Max Tokens','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_max_tokens" name="wpaicg_chat_max_tokens" value="<?php
                        echo  esc_html( $wpaicg_chat_max_tokens ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Top P','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_top_p" name="wpaicg_chat_top_p" value="<?php
                        echo  esc_html( $wpaicg_chat_top_p ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Best Of','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_best_of" name="wpaicg_chat_best_of" value="<?php
                        echo  esc_html( $wpaicg_chat_best_of ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Frequency Penalty','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_frequency_penalty" name="wpaicg_chat_frequency_penalty" value="<?php
                        echo  esc_html( $wpaicg_chat_frequency_penalty ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Presence Penalty','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_presence_penalty" name="wpaicg_chat_presence_penalty" value="<?php
                        echo  esc_html( $wpaicg_chat_presence_penalty ) ;
                        ?>" >
                    </div>
                </div>
            </div>
            <!--Moderation-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title">
                    <span>+</span><?php echo esc_html__('Moderation','gpt3-ai-content-generator')?>
                    <?php
                    if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
                        echo '<small style="background: #f90;padding: 1px 4px;border-radius: 2px;display: inline-block;margin-left: 5px;color: #000;">'.esc_html__('Pro Feature','gpt3-ai-content-generator').'</small>';
                    }
                    ?>
                </div>
                <div class="wpaicg-collapse-content">
                    <?php
                    if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Enable','gpt3-ai-content-generator')?>:</label>
                            <input disabled type="checkbox"> <?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Model','gpt3-ai-content-generator')?>:</label>
                            <select disabled class="regular-text">
                                <option value="text-moderation-latest">text-moderation-latest</option>
                                <option value="text-moderation-stable">text-moderation-stable</option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Notice','gpt3-ai-content-generator')?>:</label>
                            <textarea disabled ><?php echo esc_html__('Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.','gpt3-ai-content-generator')?></textarea>
                        </div>
                    <?php
                    else:
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Enable','gpt3-ai-content-generator')?>:</label>
                            <input<?php echo isset($wpaicg_chat_widget['moderation']) && $wpaicg_chat_widget['moderation'] ? ' checked': ''?>  name="wpaicg_chat_widget[moderation]" value="1" type="checkbox">
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Model','gpt3-ai-content-generator')?>:</label>
                            <select class="regular-text"  name="wpaicg_chat_widget[moderation_model]" >
                                <option<?php echo isset($wpaicg_chat_widget['moderation_model']) && $wpaicg_chat_widget['moderation_model'] == 'text-moderation-latest' ? ' selected':'';?> value="text-moderation-latest">text-moderation-latest</option>
                                <option<?php echo isset($wpaicg_chat_widget['moderation_model']) && $wpaicg_chat_widget['moderation_model'] == 'text-moderation-stable' ? ' selected':'';?> value="text-moderation-stable">text-moderation-stable</option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Notice','gpt3-ai-content-generator')?>:</label>
                            <textarea rows="8" name="wpaicg_chat_widget[moderation_notice]"><?php echo isset($wpaicg_chat_widget['moderation_notice']) ? esc_html($wpaicg_chat_widget['moderation_notice']) : esc_html__('Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.','gpt3-ai-content-generator')?></textarea>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
            <!--Voice-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> <?php echo esc_html__('VoiceChat','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Enable Speech to Text','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_audio_enable ? ' checked':''?> value="1" class="wpaicg_chat_widget_audio" type="checkbox" name="wpaicg_chat_widget[audio_enable]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Mic Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_mic_color)?>" type="text" class="wpaicgchat_color wpaicg_chat_widget_mic_color" name="wpaicg_chat_widget[mic_color]" class="wpaicgchat_color">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Stop Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_stop_color)?>" type="text" name="wpaicg_chat_widget[stop_color]" class="wpaicgchat_color">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Enable Text to Speech','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo empty($wpaicg_elevenlabs_api) && empty($wpaicg_google_api_key) ? ' disabled':''?><?php echo (!empty($wpaicg_elevenlabs_api) || !empty($wpaicg_google_api_key)) && $wpaicg_chat_to_speech ? ' checked':''?> value="1" type="checkbox" name="wpaicg_chat_widget[chat_to_speech]" class="wpaicg_chat_to_speech">
                    </div>
                    <?php
                    $disabled_voice_fields = false;
                    if(!$wpaicg_chat_to_speech){
                        $disabled_voice_fields = true;
                    }
                    ?>
                    <div class="mb-5" style="<?php echo empty($wpaicg_google_api_key) && empty($wpaicg_elevenlabs_api) ? ' display:none':''?>">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Provider','gpt3-ai-content-generator')?>:</label>
                        <select<?php echo $disabled_voice_fields || (empty($wpaicg_google_api_key) && empty($wpaicg_elevenlabs_api))  ? ' disabled': ''?> name="wpaicg_chat_widget[voice_service]" class="wpaicg_voice_service">
                            <option value=""><?php echo esc_html__('ElevenLabs','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_voice_service == 'google' ? ' selected':'';?> value="google"><?php echo esc_html__('Google','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="wpaicg_voice_service_google" style="<?php echo $wpaicg_chat_voice_service == 'google' && (!empty($wpaicg_google_api_key) || !empty($wpaicg_elevenlabs_api)) ? '' : 'display:none'?>">
                    <?php
                        $wpaicg_voice_language = isset($wpaicg_chat_widget['voice_language']) && !empty($wpaicg_chat_widget['voice_language']) ? $wpaicg_chat_widget['voice_language'] : 'en-US';
                        $wpaicg_voice_name = isset($wpaicg_chat_widget['voice_name']) && !empty($wpaicg_chat_widget['voice_name']) ? $wpaicg_chat_widget['voice_name'] : 'en-US-Studio-M';
                        $wpaicg_voice_device = isset($wpaicg_chat_widget['voice_device']) && !empty($wpaicg_chat_widget['voice_device']) ? $wpaicg_chat_widget['voice_device'] : '';
                        $wpaicg_voice_speed = isset($wpaicg_chat_widget['voice_speed']) && !empty($wpaicg_chat_widget['voice_speed']) ? $wpaicg_chat_widget['voice_speed'] : 1;
                        $wpaicg_voice_pitch = isset($wpaicg_chat_widget['voice_pitch']) && !empty($wpaicg_chat_widget['voice_pitch']) ? $wpaicg_chat_widget['voice_pitch'] : 0;
                    ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Language','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_widget[voice_language]" class="wpaicg_voice_language">
                                <?php
                                foreach(\WPAICG\WPAICG_Google_Speech::get_instance()->languages as $key=>$voice_language){
                                    echo '<option'.($wpaicg_voice_language == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($voice_language).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Name','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> data-value="<?php echo esc_html($wpaicg_voice_name)?>" name="wpaicg_chat_widget[voice_name]" class="wpaicg_voice_name">
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Audio Device Profile','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo empty($wpaicg_google_api_key) ? ' disabled':''?> name="wpaicg_chat_widget[voice_device]" class="wpaicg_voice_device">
                                <?php
                                foreach(\WPAICG\WPAICG_Google_Speech::get_instance()->devices() as $key => $device){
                                    echo '<option'.($wpaicg_voice_device == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($device).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Speed','gpt3-ai-content-generator')?>:</label>
                            <input<?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> type="text" class="wpaicg_voice_speed" value="<?php echo esc_html($wpaicg_voice_speed)?>" name="wpaicg_chat_widget[voice_speed]">
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Pitch','gpt3-ai-content-generator')?>:</label>
                            <input<?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> type="text" class="wpaicg_voice_pitch" value="<?php echo esc_html($wpaicg_voice_pitch)?>" name="wpaicg_chat_widget[voice_pitch]">
                        </div>
                    </div>
                    <div class="wpaicg_voice_service_elevenlabs" style="<?php echo $wpaicg_chat_voice_service == 'google' || (empty($wpaicg_google_api_key) && empty($wpaicg_elevenlabs_api)) ? 'display:none' : ''?>">
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Select a Voice','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo empty($wpaicg_elevenlabs_api) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_widget[elevenlabs_voice]" class="wpaicg_elevenlabs_voice">
                                <?php
                                foreach(\WPAICG\WPAICG_ElevenLabs::get_instance()->voices as $key=>$voice){
                                    echo '<option'.($wpaicg_elevenlabs_voice == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($voice).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!--CustomText-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> <?php echo esc_html__('Custom Text','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Name','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" name="_wpaicg_chatbox_ai_name" value="<?php
                        echo  esc_html( get_option( '_wpaicg_chatbox_ai_name', 'AI' ) ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('You','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" name="_wpaicg_chatbox_you" value="<?php
                        echo  esc_html( get_option( '_wpaicg_chatbox_you', __('You','gpt3-ai-content-generator') ) ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Thinking','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" name="_wpaicg_ai_thinking" value="<?php
                        echo  esc_html( get_option( '_wpaicg_ai_thinking', __('AI thinking','gpt3-ai-content-generator') ) ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Placeholder','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" name="_wpaicg_typing_placeholder" value="<?php
                        echo  esc_html( get_option( '_wpaicg_typing_placeholder', __('Type a message','gpt3-ai-content-generator') ) ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Welcome Message','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" name="_wpaicg_chatbox_welcome_message" value="<?php
                        echo  esc_html( get_option( '_wpaicg_chatbox_welcome_message', __('Hello human, I am a GPT powered AI chat bot. Ask me anything!','gpt3-ai-content-generator') ) ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <?php $wpaicg_chat_no_answer = get_option('wpaicg_chat_no_answer','')?>
                        <label class="wpaicg-form-label"><?php echo esc_html__('No Answer Message','gpt3-ai-content-generator')?>:</label>
                        <input class="regular-text" type="text" value="<?php echo esc_html($wpaicg_chat_no_answer)?>" name="wpaicg_chat_no_answer">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Footer Note','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_footer_text)?>" class="wpaicg-footer-note" type="text" name="wpaicg_chat_widget[footer_text]" placeholder="<?php echo esc_html__('Powered by ...','gpt3-ai-content-generator')?>">
                    </div>
                </div>
            </div>
            <!--Context-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> <?php echo esc_html__('Context','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <?php
                    if(!isset($wpaicg_chat_widget['chat_addition_option']) || $wpaicg_chat_addition){
                        $wpaicg_chat_addition = true;
                    }
                    ?>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Additional Context?','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_addition == '1' ? ' checked': ''?> name="wpaicg_chat_addition" value="1" type="checkbox" id="wpaicg_chat_addition">
                        <input name="wpaicg_chat_widget[chat_addition_option]" value="<?php echo $wpaicg_chat_addition ? 0 : 1?>" type="hidden" id="wpaicg_chat_addition_option">
                    </div>
                    <?php
                    $wpaicg_additions_json = file_get_contents(WPAICG_PLUGIN_DIR.'admin/chat/context.json');
                    $wpaicg_additions = json_decode($wpaicg_additions_json, true);
                    ?>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Template','gpt3-ai-content-generator')?>:</label>
                        <select<?php echo !$wpaicg_chat_addition ? ' disabled':'';?> class="wpaicg_chat_addition_template">
                            <option value=""><?php echo esc_html__('Select Template','gpt3-ai-content-generator')?></option>
                            <?php
                            foreach($wpaicg_additions as $key=>$wpaicg_addition){
                                echo '<option value="'.esc_html($wpaicg_addition).'">'.esc_html($key).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label" style="vertical-align:top">
                            <?php echo esc_html__('Context','gpt3-ai-content-generator')?>:
                            <small style="font-weight: normal;display: block"><?php echo sprintf(esc_html__('You can add shortcode %s and %s and %s and %s in context','gpt3-ai-content-generator'),'<code>[sitename]</code>','<code>[siteurl]</code>','<code>[domain]</code>','<code>[date]</code>')?></small>
                        </label>
                        <textarea<?php echo !$wpaicg_chat_addition ? ' disabled':''?> class="regular-text wpaicg_chat_addition_text" rows="8" id="wpaicg_chat_addition_text" name="wpaicg_chat_addition_text"><?php echo !empty($wpaicg_chat_addition_text) ? esc_html($wpaicg_chat_addition_text) : esc_html__('You are a helpful AI Assistant. Please be friendly.','gpt3-ai-content-generator')?></textarea>
                    </div>
                    <input value="<?php echo esc_html($wpaicg_chat_icon_url)?>" type="hidden" name="wpaicg_chat_widget[icon_url]" class="wpaicg_chat_icon_url">
                    <input value="<?php echo esc_html($wpaicg_ai_avatar_id)?>" type="hidden" name="wpaicg_chat_widget[ai_avatar_id]" class="wpaicg_ai_avatar_id">
                    <!-- wpaicg_chat_remember_conversation -->
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Remember Conversation','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_widget[remember_conversation]">
                            <option<?php echo $wpaicg_chat_remember_conversation == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_remember_conversation == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Remember Conv. Up To','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_conversation_cut">
                            <?php
                            for($i=3;$i<=20;$i++){
                                echo '<option'.($wpaicg_conversation_cut == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('User Aware','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_widget[user_aware]">
                            <option<?php echo $wpaicg_user_aware == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_user_aware == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Content Aware','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_widget[content_aware]" id="wpaicg_chat_content_aware">
                            <option<?php echo $wpaicg_chat_content_aware == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_content_aware == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <?php

                    ?>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Use Excerpt','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? ' checked': ''?><?php echo $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> type="checkbox" id="wpaicg_chat_excerpt" class="<?php echo $wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Use Embeddings','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? ' checked': ''?><?php echo $wpaicg_embedding_field_disabled || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> type="checkbox" value="1" name="wpaicg_chat_embedding" id="wpaicg_chat_embedding" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Pinecone Index','gpt3-ai-content-generator')?>:</label>
                        <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> name="wpaicg_chat_widget[embedding_index]" id="wpaicg_chat_embedding_index" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                            <option value=""><?php echo esc_html__('Default','gpt3-ai-content-generator')?></option>
                            <?php
                            foreach($wpaicg_pinecone_indexes as $wpaicg_pinecone_index){
                                echo '<option'.(isset($wpaicg_chat_widget['embedding_index']) && $wpaicg_chat_widget['embedding_index'] == $wpaicg_pinecone_index['url'] ? ' selected':'').' value="'.esc_html($wpaicg_pinecone_index['url']).'">'.esc_html($wpaicg_pinecone_index['name']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Method','gpt3-ai-content-generator')?>:</label>
                        <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> name="wpaicg_chat_embedding_type" id="wpaicg_chat_embedding_type" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                            <option<?php echo $wpaicg_chat_embedding_type ? ' selected':'';?> value="openai"><?php echo esc_html__('Embeddings + Completion','gpt3-ai-content-generator')?></option>
                            <option<?php echo empty($wpaicg_chat_embedding_type) ? ' selected':''?> value=""><?php echo esc_html__('Embeddings only','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Nearest Answers','gpt3-ai-content-generator')?>:</label>
                        <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> name="wpaicg_chat_embedding_top" id="wpaicg_chat_embedding_top" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                            <?php
                            for($i = 1; $i <=5;$i++){
                                echo '<option'.($wpaicg_chat_embedding_top == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <?php
                    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Enable PDF Upload','gpt3-ai-content-generator')?>:</label>
                            <input<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?><?php echo isset($wpaicg_chat_widget['embedding_pdf']) && $wpaicg_chat_widget['embedding_pdf'] ? ' checked':''?> type="checkbox" value="1" name="wpaicg_chat_widget[embedding_pdf]" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>" id="wpaicg_chat_embedding_pdf">
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Limit PDF Pages','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> name="wpaicg_chat_widget[pdf_pages]" id="wpaicg_chat_pdf_pages" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>" style="width: 65px!important;">
                                <?php
                                $pdf_pages = isset($wpaicg_chat_widget['pdf_pages']) && !empty($wpaicg_chat_widget['pdf_pages']) ? $wpaicg_chat_widget['pdf_pages'] : 120;
                                for($i=1;$i <= 120;$i++){
                                    echo '<option'.($pdf_pages == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label" style="vertical-align:top">
                                <?php echo esc_html__('PDF Success Message','gpt3-ai-content-generator')?>:
                                <small style="font-weight: normal;display: block"><?php echo sprintf(esc_html__('You can include the following shortcode in the message: %s.','gpt3-ai-content-generator'),'<code>[questions]</code>')?></small>
                            </label>
                            <textarea<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> rows="8" name="wpaicg_chat_widget[embedding_pdf_message]" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>" id="wpaicg_chat_embedding_pdf_message"><?php echo isset($wpaicg_chat_widget['embedding_pdf_message']) && !empty($wpaicg_chat_widget['embedding_pdf_message']) ? esc_html(str_replace("\\",'',$wpaicg_chat_widget['embedding_pdf_message'])):"Congrats! Your PDF is uploaded now! You can ask questions about your document.\nExample Questions:[questions]"?></textarea>
                        </div>
                    <?php
                    else:
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Enable PDF Upload','gpt3-ai-content-generator')?>:</label>
                            <input type="checkbox" disabled> <?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Limit PDF Pages','gpt3-ai-content-generator')?>:</label>
                            <select disabled style="width: 65px!important;">
                                <option><?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?></option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('PDF Success Message','gpt3-ai-content-generator')?>:</label>
                            <textarea disabled rows="8" ><?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?></textarea>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
            <!--Logs-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> <?php echo esc_html__('Logs','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Save Chat Logs','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_save_logs ? ' checked':''?> class="wpaicg_chatbot_save_logs" value="1" type="checkbox" name="wpaicg_chat_widget[save_logs]">
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Save Prompt','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_save_logs ? '': ' disabled'?><?php echo $wpaicg_save_logs && isset($wpaicg_chat_widget['log_request']) && $wpaicg_chat_widget['log_request'] ? ' checked' : ''?> class="wpaicg_chatbot_log_request" value="1" type="checkbox" name="wpaicg_chat_widget[log_request]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Display Notice','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_save_logs ? '': ' disabled'?><?php echo $wpaicg_log_notice ? ' checked':''?> class="wpaicg_chatbot_log_notice" value="1" type="checkbox" name="wpaicg_chat_widget[log_notice]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Notice Text','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_save_logs ? '': ' disabled'?> class="regular-text wpaicg_chatbot_log_notice_message" value="<?php echo esc_html($wpaicg_log_notice_message)?>" type="text" name="wpaicg_chat_widget[log_notice_message]">
                    </div>
                </div>
            </div>
            <!--Token Handing-->
            <div class="wpaicg-collapse mb-5">
                <div class="wpaicg-collapse-title">
                    <span>+</span><?php echo esc_html__('Token Handling','gpt3-ai-content-generator')?>
                </div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Limit Registered User','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_user_limited ? ' checked': ''?> class="wpaicg_user_token_limit" type="checkbox" value="1" name="wpaicg_chat_widget[user_limited]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Token Limit','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_user_limited ? '' : ' disabled'?> class="wpaicg_user_token_limit_text" style="width: 80px" type="text" value="<?php echo esc_html($wpaicg_user_tokens)?>" name="wpaicg_chat_widget[user_tokens]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Role based limit','gpt3-ai-content-generator')?>:</label>
                        <?php
                        foreach($wpaicg_roles as $key=>$wpaicg_role){
                            echo '<input class="wpaicg_role_'.esc_html($key).'" value="'.(isset($wpaicg_chat_widget['limited_roles'][$key]) && !empty($wpaicg_chat_widget['limited_roles'][$key]) ? esc_html($wpaicg_chat_widget['limited_roles'][$key]) : '').'" type="hidden" name="wpaicg_chat_widget[limited_roles]['.esc_html($key).']">';
                        }
                        ?>
                        <input<?php echo $wpaicg_user_limited ? '': (isset($wpaicg_chat_widget['role_limited']) && $wpaicg_chat_widget['role_limited'] ? ' checked':'')?> type="checkbox" value="1" class="wpaicg_role_limited" name="wpaicg_chat_widget[role_limited]">
                        <a href="javascript:void(0)" class="wpaicg_limit_set_role<?php echo $wpaicg_user_limited || !isset($wpaicg_chat_widget['role_limited']) || !$wpaicg_chat_widget['role_limited'] ? ' disabled': ''?>"><?php echo esc_html__('Set Limit','gpt3-ai-content-generator')?></a>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Limit Non-Registered User','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_guest_limited ? ' checked': ''?> class="wpaicg_guest_token_limit" type="checkbox" value="1" name="wpaicg_chat_widget[guest_limited]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Token Limit','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_guest_limited ? '' : ' disabled'?> class="wpaicg_guest_token_limit_text" style="width: 80px" type="text" value="<?php echo esc_html($wpaicg_guest_tokens)?>" name="wpaicg_chat_widget[guest_tokens]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Notice','gpt3-ai-content-generator')?>:</label>
                        <input type="text" value="<?php echo esc_html($wpaicg_limited_message)?>" name="wpaicg_chat_widget[limited_message]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Reset Limit','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_widget[reset_limit]">
                            <option<?php echo $wpaicg_reset_limit == 0 ? ' selected':''?> value="0"><?php echo esc_html__('Never','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_reset_limit == 1 ? ' selected':''?> value="1"><?php echo esc_html__('1 Day','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_reset_limit == 3 ? ' selected':''?> value="3"><?php echo esc_html__('3 Days','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_reset_limit == 7 ? ' selected':''?> value="7"><?php echo esc_html__('1 Week','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_reset_limit == 14 ? ' selected':''?> value="14"><?php echo esc_html__('2 Weeks','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_reset_limit == 30 ? ' selected':''?> value="30"><?php echo esc_html__('1 Month','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_reset_limit == 60 ? ' selected':''?> value="60"><?php echo esc_html__('2 Months','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_reset_limit == 90 ? ' selected':''?> value="90"><?php echo esc_html__('3 Months','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_reset_limit == 180 ? ' selected':''?> value="180"><?php echo esc_html__('6 Months','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                </div>
            </div>
            <button class="button button-primary" name="wpaicg_submit" style="width: 100%"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
        </form>
    </div>
</div>
<script>
    jQuery(document).ready(function ($){
        let wpaicg_google_voices = <?php echo json_encode($wpaicg_google_voices)?>;
        let wpaicg_elevenlab_api = '<?php echo esc_html($wpaicg_elevenlabs_api)?>';
        let wpaicg_google_api_key = '<?php  echo $wpaicg_google_api_key?>';
        $(document).on('click','.wpaicg_chat_to_speech', function(e){
            let parent = $(e.currentTarget).parent().parent();
            let voice_service = parent.find('.wpaicg_voice_service');
            if($(e.currentTarget).prop('checked')){
                if(wpaicg_elevenlab_api !== '' || wpaicg_google_api_key !== ''){
                    voice_service.removeAttr('disabled');
                }
                if(wpaicg_elevenlab_api !== ''){
                    parent.find('.wpaicg_elevenlabs_voice').removeAttr('disabled');
                }
                if(wpaicg_google_api_key !== ''){
                    parent.find('.wpaicg_voice_language').removeAttr('disabled');
                    parent.find('.wpaicg_voice_name').removeAttr('disabled');
                    parent.find('.wpaicg_voice_device').removeAttr('disabled');
                    parent.find('.wpaicg_voice_speed').removeAttr('disabled');
                    parent.find('.wpaicg_voice_pitch').removeAttr('disabled');
                }
            }
            else{
                voice_service.attr('disabled','disabled');
                parent.find('.wpaicg_elevenlabs_voice').attr('disabled','disabled');
                parent.find('.wpaicg_voice_language').attr('disabled','disabled');
                parent.find('.wpaicg_voice_name').attr('disabled','disabled');
                parent.find('.wpaicg_voice_device').attr('disabled','disabled');
                parent.find('.wpaicg_voice_speed').attr('disabled','disabled');
                parent.find('.wpaicg_voice_pitch').attr('disabled','disabled');
            }
        });
        $(document).on('change','.wpaicg_voice_service',function(e){
            let parent = $(e.currentTarget).parent().parent();
            if($(e.currentTarget).val() === 'google'){
                parent.find('.wpaicg_voice_service_elevenlabs').hide();
                parent.find('.wpaicg_voice_service_google').show();
            }
            else{
                parent.find('.wpaicg_voice_service_elevenlabs').show();
                parent.find('.wpaicg_voice_service_google').hide();
            }
        })
        $(document).on('keypress','.wpaicg_voice_speed,.wpaicg_voice_pitch', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
        });
        function wpaicgsetVoices(element){
            let parent = element.parent().parent();
            let language = element.val();
            let voiceNameInput = parent.find('.wpaicg_voice_name');
            voiceNameInput.empty();
            let selected = voiceNameInput.attr('data-value');
            $.each(wpaicg_google_voices[language], function (idx, item){
                voiceNameInput.append('<option'+(selected === item.name ? ' selected':'')+' value="'+item.name+'">'+item.name+' - '+item.ssmlGender+'</option>');
            })
        }
        function wpaicgcollectVoices(element){
            if(!Object.keys(wpaicg_google_voices).length === 0){
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {action: 'wpaicg_sync_google_voices',nonce: '<?php echo wp_create_nonce('wpaicg_sync_google_voices')?>'},
                    dataType: 'json',
                    type: 'post',
                    success: function(res){
                        if(res.status === 'success'){
                            wpaicg_google_voices = res.voices;
                            wpaicgsetVoices(element);
                        }else{
                            alert(res.message);
                        }
                    }

                });
            }
            else{
                wpaicgsetVoices(element);
            }
        }
        $(document).on('change','.wpaicg_voice_language', function(e){
            wpaicgcollectVoices($(e.currentTarget));
        })
        if($('.wpaicg_voice_language').length){
            wpaicgcollectVoices($('.wpaicg_voice_language'));
        }
        $('#form-chatbox-setting').on('submit', function (e){
            if($('.wpaicg_voice_speed').length) {
                let wpaicg_voice_speed = parseFloat($('.wpaicg_voice_speed').val());
                let wpaicg_voice_pitch = parseFloat($('.wpaicg_voice_pitch').val());
                let wpaicg_voice_name = parseFloat($('.wpaicg_voice_name').val());
                let has_error = false;
                if (wpaicg_voice_speed < 0.25 || wpaicg_voice_speed > 4) {
                    has_error = '<?php echo sprintf(esc_html__('Please enter valid voice speed value between %s and %s', 'gpt3-ai-content-generator'), 0.25, 4)?>';
                } else if (wpaicg_voice_pitch < -20 || wpaicg_voice_speed > 20) {
                    has_error = '<?php echo sprintf(esc_html__('Please enter valid voice pitch value between %s and %s', 'gpt3-ai-content-generator'), -20, 20)?>';
                }
                else if(wpaicg_voice_name === ''){
                    has_error = '<?php echo esc_html__('Please select voice name', 'gpt3-ai-content-generator')?>';
                }
                if (has_error) {
                    e.preventDefault();
                    alert(has_error);
                    return false;
                }
            }
        })
        let wpaicg_roles = <?php echo wp_kses_post(json_encode($wpaicg_roles))?>;
        $('.wpaicg_modal_close_second').click(function (){
            $('.wpaicg_modal_close_second').closest('.wpaicg_modal_second').hide();
            $('.wpaicg-overlay-second').hide();
        });
        $(document).on('click', '.wpaicg_chatbot_save_logs', function(e){
            if($(e.currentTarget).prop('checked')){
                $('.wpaicg_chatbot_log_request').removeAttr('disabled');
                $('.wpaicg_chatbot_log_notice').removeAttr('disabled');
                $('.wpaicg_chatbot_log_notice_message').removeAttr('disabled');
            }
            else{
                $('.wpaicg_chatbot_log_request').attr('disabled','disabled');
                $('.wpaicg_chatbot_log_request').prop('checked',false);
                $('.wpaicg_chatbot_log_notice').attr('disabled','disabled');
                $('.wpaicg_chatbot_log_notice').prop('checked',false);
                $('.wpaicg_chatbot_log_notice_message').attr('disabled','disabled');
            }
        });
        $(document).on('keypress','.wpaicg_user_token_limit_text,.wpaicg_update_role_limit,.wpaicg_guest_token_limit_text', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
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
        $('.wpaicg-chatbox-preview-box > .wpaicg_chat_widget').addClass('wpaicg_widget_open');
        $('.wpaicg-chatbox-preview-box .wpaicg_toggle').addClass('wpaicg_widget_open');
        function wpaicgChangeAvatarRealtime(){
            var wpaicg_user_avatar_check = $('input[name=_wpaicg_chatbox_you]').val()+':';
            var wpaicg_ai_avatar_check = $('input[name=_wpaicg_chatbox_ai_name]').val()+':';
            if($('.wpaicgchat_use_avatar').prop('checked')){
                wpaicg_user_avatar_check = '<img src="<?php echo get_avatar_url(get_current_user_id())?>" height="40" width="40">';
                wpaicg_ai_avatar_check = '<?php echo esc_html(WPAICG_PLUGIN_URL) . 'admin/images/chatbot.png';?>';
                if($('.wpaicg_chatbox_avatar_custom').prop('checked') && $('.wpaicg_chatbox_avatar img').length){
                    wpaicg_ai_avatar_check = $('.wpaicg_chatbox_avatar img').attr('src');
                }
                wpaicg_ai_avatar_check = '<img src="'+wpaicg_ai_avatar_check+'" height="40" width="40">';
            }

            $('.wpaicg-chat-ai-message').each(function (idx, item){
                $(item).find('strong').html(wpaicg_ai_avatar_check);
            });
            $('.wpaicg-chat-user-message').each(function (idx, item){
                $(item).find('strong').html(wpaicg_user_avatar_check);
            });
        }
        $('input[name=_wpaicg_chatbox_you],input[name=_wpaicg_chatbox_ai_name]').on('input', function (){
            wpaicgChangeAvatarRealtime();
        })
        $('.wpaicgchat_use_avatar,.wpaicg_chatbox_avatar_default,.wpaicg_chatbox_avatar_custom').on('click', function (){
            wpaicgChangeAvatarRealtime();
        })
        $('.wpaicg_chat_rounded,.wpaicg_text_rounded,.wpaicg_text_height').on('input', function(){
            wpaicgUpdateRealtime();
        })
        function wpaicgUpdateRealtime(){
            var wpaicgWindowWidth = window.innerWidth;
            var wpaicgWindowHeight = window.innerHeight;
            let fontsize = $('.wpaicg_chat_widget_font_size').val();
            let fontcolor = $('.wpaicgchat_font_color').iris('color');
            let bgcolor = $('.wpaicgchat_bg_color').iris('color');
            let inputbg = $('.wpaicgchat_input_color').iris('color');
            let inputborder = $('.wpaicgchat_input_border').iris('color');
            let buttoncolor = $('.wpaicgchat_send_color').iris('color');
            let chatbarcolor = $('.wpaicgchat_bar_color').iris('color');
            let userbg = $('.wpaicgchat_user_color').iris('color');
            let aibg = $('.wpaicgchat_ai_color').iris('color');
            let useavatar = $('.wpaicgchat_use_avatar').val();
            let width = $('.wpaicg_chat_widget_width').val();
            let height = $('.wpaicg_chat_widget_height').val();
            let mic_color = $('.wpaicg_chat_widget_mic_color').iris('color');
            let wpaicg_chat_rounded = $('.wpaicg_chat_rounded').val();
            let wpaicg_text_rounded = $('.wpaicg_text_rounded').val();
            let wpaicg_text_height = $('.wpaicg_text_height').val();
            $('.wpaicg-mic-icon').css('color', mic_color);
            $('.wpaicg-chatbox').attr('data-height',height);
            $('.wpaicg-chatbox').attr('data-width',width);
            $('.wpaicg-chatbox').attr('data-chat_rounded',wpaicg_chat_rounded);
            $('.wpaicg-chatbox').attr('data-text_rounded',wpaicg_text_rounded);
            $('.wpaicg-chatbox').attr('data-text_height',wpaicg_text_height);
            $('.wpaicg-chatbox-action-bar').css({
                'color':chatbarcolor,
                'background-color':bgcolor
            });
            let footernote = $('.wpaicg-footer-note').val();
            let footerheight = 0;

            if(footernote === ''){
                footerheight = 18;
                $('.wpaicg-chatbox-footer').hide();
                $('.wpaicg-chatbox-type').css('padding','5px');
            }
            else{
                $('.wpaicg-chatbox-type').css('padding','5px 5px 0 5px');
                $('.wpaicg-chatbox-footer').show();
                $('.wpaicg-chatbox-footer').html(footernote);
            }
            if($('.wpaicg_chat_widget_audio').prop('checked')){
                $('.wpaicg-mic-icon').show();
            }
            else{
                $('.wpaicg-mic-icon').hide();
            }
            $('.wpaicg-chatbox-messages li.wpaicg-chat-ai-message').css({
                'font-size': fontsize+'px',
                'color': fontcolor,
                'background-color': aibg
            })
            $('.wpaicg-chatbox-messages li.wpaicg-chat-user-message').css({
                'font-size': fontsize+'px',
                'color': fontcolor,
                'background-color': userbg
            });
            $('.wpaicg_chat_widget_content .wpaicg-chatbox-content ul,.wpaicg_chat_widget_content .wpaicg-chatbox').css({
                'background-color': bgcolor
            });
            $('.wpaicg-chatbox-typing').css({
                'border-color': inputborder,
                'background-color': inputbg
            });
            var previewboxWidth = $('.wpaicg-chatbox-preview-box').width();
            $('.wpaicg-chatbox-send').css('color',buttoncolor);
            if(width.indexOf('%') < 0){
                if(width.indexOf('px') < 0){
                    width = parseFloat(width);
                }
                else{
                    width = parseFloat(width.replace(/px/g,''));
                }
            }
            else{
                width = parseFloat(width.replace(/%/g,''));
                width = width*previewboxWidth/100;
            }
            if(height.indexOf('%') < 0){
                if(height.indexOf('px') < 0){
                    height = parseFloat(height);
                }
                else{
                    height = parseFloat(height.replace(/px/g,''));
                }
            }
            else{
                height = parseFloat(height.replace(/%/g,''));
                height = height*wpaicgWindowHeight/100;
            }
            $('.wpaicg-chatbox-preview-box').height((parseInt(height)+125)+'px');
            if(width > previewboxWidth){
                width = previewboxWidth;
            }
            $('.wpaicg_chat_widget_content .wpaicg-chatbox,.wpaicg_widget_open .wpaicg_chat_widget_content').css({
                'height': height+'px',
                'width': width+'px',
            });
            $('.wpaicg_chat_widget_content .wpaicg-chatbox-content').css({
                'height': (height - 58 + footerheight)+'px'
            });
            $('.wpaicg_chat_widget_content .wpaicg-chatbox-content ul').css({
                'height': (height - 82 + footerheight)+'px'
            });
            wpaicgChatBoxSize();
        }
        $('.wpaicg_chat_widget_font_size,.wpaicg_chat_widget_width,.wpaicg_chat_widget_height').on('input', function(){
            wpaicgUpdateRealtime();
        });
        $('.wpaicg_chat_widget_audio,.wpaicgchat_use_avatar,.wpaicgchat_fullscreen,.wpaicgchat_close_btn,.wpaicgchat_download_btn').click(function(){
            wpaicgUpdateRealtime();
        })
        $('.wpaicgchat_color').wpColorPicker({
            change: function (event, ui){
                wpaicgUpdateRealtime();
            },
            clear: function(event){
                wpaicgUpdateRealtime();
            }
        });
        $('.wpaicg-footer-note').on('input', function(){
            wpaicgUpdateRealtime();
        })
        $('.wpaicg_chatbox_icon').click(function (e){
            e.preventDefault();
            $('.wpaicg_chatbox_icon_default').prop('checked',false);
            $('.wpaicg_chatbox_icon_custom').prop('checked',true);
            var button = $(e.currentTarget),
                custom_uploader = wp.media({
                    title: '<?php echo esc_html__('Insert image','gpt3-ai-content-generator')?>',
                    library : {
                        type : 'image'
                    },
                    button: {
                        text: '<?php echo esc_html__('Use this image','gpt3-ai-content-generator')?>'
                    },
                    multiple: false
                }).on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    button.html('<img width="75" height="75" src="'+attachment.url+'">');
                    $('.wpaicg_chat_icon_url').val(attachment.id);
                }).open();
        });
        $('.wpaicg_chatbox_avatar').click(function (e){
            e.preventDefault();
            $('.wpaicg_chatbox_avatar_default').prop('checked',false);
            $('.wpaicg_chatbox_avatar_custom').prop('checked',true);
            var button = $(e.currentTarget),
                custom_uploader = wp.media({
                    title: '<?php echo esc_html__('Insert image','gpt3-ai-content-generator')?>',
                    library : {
                        type : 'image'
                    },
                    button: {
                        text: '<?php echo esc_html__('Use this image','gpt3-ai-content-generator')?>'
                    },
                    multiple: false
                }).on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    button.html('<img width="40" height="40" src="'+attachment.url+'">');
                    $('.wpaicg_ai_avatar_id').val(attachment.id);
                }).open();
        });
        $('.wpaicg-collapse-title').click(function (){
            if(!$(this).hasClass('wpaicg-collapse-active')){
                $('.wpaicg-collapse').removeClass('wpaicg-collapse-active');
                $('.wpaicg-collapse-title span').html('+');
                $(this).find('span').html('-');
                $(this).parent().addClass('wpaicg-collapse-active');
            }
        });
        $('#wpaicg_chat_excerpt').on('click', function (){
            if($(this).prop('checked')){
                $('#wpaicg_chat_excerpt').removeClass('asdisabled');
                $('#wpaicg_chat_embedding').prop('checked',false);
                $('#wpaicg_chat_embedding').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_index').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_index').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf_message').addClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').attr('disabled','disabled');
                $('#wpaicg_chat_pdf_pages').addClass('asdisabled');
            }
            else{
                $(this).prop('checked',true);
            }
        });
        $('#wpaicg_chat_addition').on('click', function (){
            if($(this).prop('checked')){
                $('#wpaicg_chat_addition_text').removeAttr('disabled');
                $('.wpaicg_chat_addition_template').removeAttr('disabled');
            }
            else{
                $('#wpaicg_chat_addition_text').attr('disabled','disabled');
                $('.wpaicg_chat_addition_template').attr('disabled','disabled');
            }
        });
        $(document).on('change', '.wpaicg_chat_addition_template',function (e){
            var addition_text_template = $(e.currentTarget).val();
            if(addition_text_template !== ''){
                $('.wpaicg_chat_addition_text').val(addition_text_template);
            }
        });
        $('#wpaicg_chat_embedding').on('click', function (){
            if($(this).prop('checked')){
                $('#wpaicg_chat_excerpt').prop('checked',false);
                $('#wpaicg_chat_excerpt').addClass('asdisabled');
                $('#wpaicg_chat_embedding').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_type').removeAttr('disabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_top').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_top').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf_message').removeClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').removeAttr('disabled');
                $('#wpaicg_chat_pdf_pages').removeClass('asdisabled');
            }
            else{
                $(this).prop('checked',true);
            }
        });
        <?php
        if(!$wpaicg_embedding_field_disabled):
        ?>
        $('#wpaicg_chat_content_aware').on('change', function (){
            if($(this).val() === 'yes'){
                $('#wpaicg_chat_excerpt').removeAttr('disabled');
                $('#wpaicg_chat_excerpt').prop('checked',true);
                $('#wpaicg_chat_embedding').removeAttr('disabled');
                $('#wpaicg_chat_embedding_type').removeAttr('disabled');
                $('#wpaicg_chat_embedding').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').addClass('asdisabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_top').addClass('asdisabled');
                $('#wpaicg_chat_embedding_index').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf_message').addClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').removeAttr('disabled');
                $('#wpaicg_chat_pdf_pages').addClass('asdisabled');
            }
            else{
                $('#wpaicg_chat_embedding_type').removeClass('asdisabled');
                $('#wpaicg_chat_excerpt').removeClass('asdisabled');
                $('#wpaicg_chat_embedding').removeClass('asdisabled');
                $('#wpaicg_chat_excerpt').prop('checked',false);
                $('#wpaicg_chat_embedding').prop('checked',false);
                $('#wpaicg_chat_excerpt').attr('disabled','disabled');
                $('#wpaicg_chat_embedding').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_type').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_index').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_index').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf_message').removeClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').attr('disabled','disabled');
                $('#wpaicg_chat_pdf_pages').removeClass('asdisabled');
            }
        })
        <?php
        else:
        ?>
        $('#wpaicg_chat_content_aware').on('change', function (){
            if($(this).val() === 'yes'){
                $('#wpaicg_chat_excerpt').removeAttr('disabled');
                $('#wpaicg_chat_excerpt').prop('checked',true);
            }
            else{
                $('#wpaicg_chat_excerpt').removeClass('asdisabled');
                $('#wpaicg_chat_excerpt').prop('checked',false);
                $('#wpaicg_chat_excerpt').attr('disabled','disabled');
            }
        })
        <?php
        endif;
        ?>
    })
</script>
