<?php
if ( ! defined( 'ABSPATH' ) ) exit;
wp_enqueue_script('wp-color-picker');
wp_enqueue_style('wp-color-picker');
global  $wpdb ;
$table = $wpdb->prefix . 'wpaicg';
$wpaicg_save_setting_success = false;
if(isset($_POST['wpaicg_chat_shortcode_options']) && is_array($_POST['wpaicg_chat_shortcode_options'])){
    check_admin_referer('wpaicg_chat_shortcode_save');
    $wpaicg_chat_shortcode_options = \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_chat_shortcode_options']);
    update_option('wpaicg_chat_shortcode_options',$wpaicg_chat_shortcode_options);
    $wpaicg_save_setting_success = 'Setting saved successfully';
}
$existingValue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE name = %s", 'wpaicg_settings' ), ARRAY_A );
$wpaicg_chat_shortcode_options = get_option('wpaicg_chat_shortcode_options',[]);
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
    'ai_name' => 'AI',
    'you' => __('You','gpt3-ai-content-generator'),
    'ai_thinking' => __('AI Thinking','gpt3-ai-content-generator'),
    'placeholder' => __('Type a message','gpt3-ai-content-generator'),
    'welcome' => __('Hello human, I am a GPT powered AI chat bot. Ask me anything!','gpt3-ai-content-generator'),
    'remember_conversation' => 'yes',
    'conversation_cut' => 10,
    'content_aware' => 'yes',
    'embedding' =>  false,
    'embedding_type' =>  false,
    'embedding_index' =>  '',
    'embedding_pdf' => false,
    'embedding_pdf_message' => "Congrats! Your PDF is uploaded now! You can ask questions about your document.\nExample Questions:[questions]",
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
    'log_notice_message' => __('Please note that your conversations will be recorded.','gpt3-ai-content-generator'),
    'bgcolor' => '#222',
    'bg_text_field' => '#fff',
    'send_color' => '#fff',
    'border_text_field' => '#ccc',
    'footer_text' => '',
    'chat_addition' => false,
    'chat_addition_option' => 1,
    'chat_addition_text' => '',
    'audio_enable' => false,
    'mic_color' => '#222',
    'pdf_color' => '#222',
    'stop_color' => '#f00',
    'user_aware' => 'no',
    'user_limited' => false,
    'guest_limited' => false,
    'user_tokens' => 0,
    'guest_tokens' => 0,
    'reset_limit' => 0,
    'limited_message' => __('You have reached your token limit.','gpt3-ai-content-generator'),
    'moderation' => false,
    'moderation_model' => 'text-moderation-latest',
    'moderation_notice' => __('Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.','gpt3-ai-content-generator'),
    'role_limited' => false,
    'limited_roles' => [],
    'log_request' => false,
    'fullscreen' => false,
    'download_btn' => false,
    'bar_color' => '#fff',
    'thinking_color' => '#fff',
    'chat_to_speech' => false,
    'elevenlabs_voice' => '',
    'text_height' => 60,
    'text_rounded' => 20,
    'chat_rounded' => 20,
    'pdf_pages' => 120,
    'voice_language' => 'en-US',
    'voice_name' => 'en-US-Studio-M',
    'voice_device' => '',
    'voice_speed' => 1,
    'voice_pitch' => 0,
    'voice_service' => ''
);
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
$wpaicg_settings = shortcode_atts($default_setting, $wpaicg_chat_shortcode_options);
$wpaicg_custom_models = get_option('wpaicg_custom_models',array());
$wpaicg_custom_models = array_merge(array('text-davinci-003','text-curie-001','text-babbage-001','text-ada-001'),$wpaicg_custom_models);
$wpaicg_embedding_field_disabled = empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment) ? true : false;
$wpaicg_save_logs = isset($wpaicg_settings['save_logs']) && !empty($wpaicg_settings['save_logs']) ? $wpaicg_settings['save_logs'] : false;
$wpaicg_log_notice = isset($wpaicg_settings['log_notice']) && !empty($wpaicg_settings['log_notice']) ? $wpaicg_settings['log_notice'] : false;
$wpaicg_log_notice_message = isset($wpaicg_settings['log_notice_message']) && !empty($wpaicg_settings['log_notice_message']) ? $wpaicg_settings['log_notice_message'] : __('Please note that your conversations will be recorded.','gpt3-ai-content-generator');
$wpaicg_user_limited = isset($wpaicg_settings['user_limited']) ? $wpaicg_settings['user_limited'] : false;
$wpaicg_guest_limited = isset($wpaicg_settings['guest_limited']) ? $wpaicg_settings['guest_limited'] : false;
$wpaicg_user_tokens = isset($wpaicg_settings['user_tokens']) ? $wpaicg_settings['user_tokens'] : 0;
$wpaicg_guest_tokens = isset($wpaicg_settings['guest_tokens']) ? $wpaicg_settings['guest_tokens'] : 0;
$wpaicg_reset_limit = isset($wpaicg_settings['reset_limit']) ? $wpaicg_settings['reset_limit'] : 0;
$wpaicg_limited_message = isset($wpaicg_settings['limited_message']) && !empty($wpaicg_settings['limited_message']) ? $wpaicg_settings['limited_message'] : __('You have reached your token limit.','gpt3-ai-content-generator');
$wpaicg_chat_fullscreen = isset($wpaicg_settings['fullscreen']) && !empty($wpaicg_settings['fullscreen']) ? $wpaicg_settings['fullscreen'] : false;
$wpaicg_chat_download_btn = isset($wpaicg_settings['download_btn']) && !empty($wpaicg_settings['download_btn']) ? $wpaicg_settings['download_btn'] : false;
$wpaicg_bar_color = isset($wpaicg_settings['bar_color']) && !empty($wpaicg_settings['bar_color']) ? $wpaicg_settings['bar_color'] : '#fff';
$wpaicg_thinking_color = isset($wpaicg_settings['thinking_color']) && !empty($wpaicg_settings['thinking_color']) ? $wpaicg_settings['thinking_color'] : '#fff';
$wpaicg_chat_to_speech = isset($wpaicg_settings['chat_to_speech']) ? $wpaicg_settings['chat_to_speech'] : false;
$wpaicg_elevenlabs_voice = isset($wpaicg_settings['elevenlabs_voice']) ? $wpaicg_settings['elevenlabs_voice'] : '';
$wpaicg_elevenlabs_api = get_option('wpaicg_elevenlabs_api', '');
$wpaicg_chat_voice_service = isset($wpaicg_settings['voice_service']) ? $wpaicg_settings['voice_service'] : '';
$wpaicg_google_voices = get_option('wpaicg_google_voices',[]);
$wpaicg_google_api_key = get_option('wpaicg_google_api_key', '');
$wpaicg_roles = wp_roles()->get_names();
$wpaicg_pinecone_indexes = get_option('wpaicg_pinecone_indexes','');
$wpaicg_pinecone_indexes = empty($wpaicg_pinecone_indexes) ? array() : json_decode($wpaicg_pinecone_indexes,true);
?>
<style>
    .asdisabled{
        background: #ebebeb!important;
    }
    .wp-picker-holder {
        position: absolute;
    }
    .wpaicg-collapse-content input.wp-color-picker[type=text]{
        width: 4rem!important;
    }
    .wpaicg-chat-shortcode{
        background-color: <?php echo esc_html($wpaicg_settings['bgcolor'])?>;
    }
    .wpaicg-chat-shortcode-type{
        background: rgb(0 0 0 / 19%);
    }
    .wpaicg-chat-shortcode-typing{
        background-color: <?php echo esc_html($wpaicg_settings['bg_text_field'])?>;
        border-color: <?php echo esc_html($wpaicg_settings['border_text_field'])?>;
    }
    .wpaicg-chat-shortcode-send{
        color: <?php echo esc_html($wpaicg_settings['send_color'])?>;
    }
    .wpaicg-collapse-content textarea{
        display: inline-block!important;
        width: 48%!important;
    }
    .wpaicg-collapse-content .wpaicg-form-label{
        vertical-align: top;
    }
    .wp-picker-input-wrap input[type=text]{
        width: 4rem!important;
    }
</style>
<?php
if($wpaicg_save_setting_success):
    ?>
    <div class="notice notice-success">
        <p><?php echo esc_html($wpaicg_save_setting_success);?></p>
    </div>
<?php
endif;
?>
<div class="wpaicg-alert mb-5">
    <p><?php echo sprintf(esc_html__('Include the shortcode %s in the desired location on your site.','gpt3-ai-content-generator'),'<code>[wpaicg_chatgpt]</code>')?><?php echo sprintf(esc_html__('Learn how you can train the chat bot with your content','gpt3-ai-content-generator'),'<b>Widget</b>')?> <u><b><a href="https://docs.aipower.org/docs/ChatGPT/chatgpt-wordpress" target="_blank"><?php echo esc_html__('here','gpt3-ai-content-generator')?></a></u></b>.</p>
</div>
<div class="wpaicg-grid-three">
    <div class="wpaicg-grid-2 wpaicg-chat-shortcode-preview">
        <?php
        echo do_shortcode('[wpaicg_chatgpt]');
        ?>
    </div>
    <div class="wpaicg-grid-1">
        <form action="" method="post" id="form-chatbox-setting">
            <?php
            wp_nonce_field('wpaicg_chat_shortcode_save');
            ?>
            <div class="wpaicg-collapse wpaicg-collapse-active">
                <div class="wpaicg-collapse-title"><span>-</span> <?php echo esc_html__('Language, Tone and Profession','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label" for="label_title"><?php
                            echo  esc_html( __( "Language", "wp-ai-content-generator" ) ) ;
                            ?>
                        </label>
                        <select class="wpaicg-input" id="label_wpai_language" name="wpaicg_chat_shortcode_options[language]" >
                            <option value="en" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'en' ? 'selected' : '' ) ;
                            ?>>English</option>
                            <option value="af" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'af' ? 'selected' : '' ) ;
                            ?>>Afrikaans</option>
                            <option value="ar" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ar' ? 'selected' : '' ) ;
                            ?>>Arabic</option>
                            <option value="bg" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'bg' ? 'selected' : '' ) ;
                            ?>>Bulgarian</option>
                            <option value="zh" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'zh' ? 'selected' : '' ) ;
                            ?>>Chinese</option>
                            <option value="hr" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'hr' ? 'selected' : '' ) ;
                            ?>>Croatian</option>
                            <option value="cs" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'cs' ? 'selected' : '' ) ;
                            ?>>Czech</option>
                            <option value="da" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'da' ? 'selected' : '' ) ;
                            ?>>Danish</option>
                            <option value="nl" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'nl' ? 'selected' : '' ) ;
                            ?>>Dutch</option>
                            <option value="et" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'et' ? 'selected' : '' ) ;
                            ?>>Estonian</option>
                            <option value="fil" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'fil' ? 'selected' : '' ) ;
                            ?>>Filipino</option>
                            <option value="fi" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'fi' ? 'selected' : '' ) ;
                            ?>>Finnish</option>
                            <option value="fr" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'fr' ? 'selected' : '' ) ;
                            ?>>French</option>
                            <option value="de" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'de' ? 'selected' : '' ) ;
                            ?>>German</option>
                            <option value="el" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'el' ? 'selected' : '' ) ;
                            ?>>Greek</option>
                            <option value="he" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'he' ? 'selected' : '' ) ;
                            ?>>Hebrew</option>
                            <option value="hi" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'hi' ? 'selected' : '' ) ;
                            ?>>Hindi</option>
                            <option value="hu" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'hu' ? 'selected' : '' ) ;
                            ?>>Hungarian</option>
                            <option value="id" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'id' ? 'selected' : '' ) ;
                            ?>>Indonesian</option>
                            <option value="it" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'it' ? 'selected' : '' ) ;
                            ?>>Italian</option>
                            <option value="ja" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ja' ? 'selected' : '' ) ;
                            ?>>Japanese</option>
                            <option value="ko" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ko' ? 'selected' : '' ) ;
                            ?>>Korean</option>
                            <option value="lv" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'lv' ? 'selected' : '' ) ;
                            ?>>Latvian</option>
                            <option value="lt" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'lt' ? 'selected' : '' ) ;
                            ?>>Lithuanian</option>
                            <option value="ms" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ms' ? 'selected' : '' ) ;
                            ?>>Malay</option>
                            <option value="no" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'no' ? 'selected' : '' ) ;
                            ?>>Norwegian</option>
                            <option value="fa" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'fa' ? 'selected' : '' ) ;
                            ?>>Persian</option>
                            <option value="pl" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'pl' ? 'selected' : '' ) ;
                            ?>>Polish</option>
                            <option value="pt" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'pt' ? 'selected' : '' ) ;
                            ?>>Portuguese</option>
                            <option value="ro" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ro' ? 'selected' : '' ) ;
                            ?>>Romanian</option>
                            <option value="ru" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ru' ? 'selected' : '' ) ;
                            ?>>Russian</option>
                            <option value="sr" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'sr' ? 'selected' : '' ) ;
                            ?>>Serbian</option>
                            <option value="sk" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'sk' ? 'selected' : '' ) ;
                            ?>>Slovak</option>
                            <option value="sl" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'sl' ? 'selected' : '' ) ;
                            ?>>Slovenian</option>
                            <option value="sv" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'sv' ? 'selected' : '' ) ;
                            ?>>Swedish</option>
                            <option value="es" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'es' ? 'selected' : '' ) ;
                            ?>>Spanish</option>
                            <option value="th" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'th' ? 'selected' : '' ) ;
                            ?>>Thai</option>
                            <option value="tr" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'tr' ? 'selected' : '' ) ;
                            ?>>Turkish</option>
                            <option value="uk" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'uk' ? 'selected' : '' ) ;
                            ?>>Ukrainian</option>
                            <option value="vi" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'vi' ? 'selected' : '' ) ;
                            ?>>Vietnamese</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Tone','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_chat_shortcode_options[tone]">
                            <option<?php echo $wpaicg_settings['tone'] == 'friendly' ? ' selected': ''?> value="friendly"><?php echo esc_html__('Friendly','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['tone'] == 'professional' ? ' selected': ''?> value="professional"><?php echo esc_html__('Professional','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['tone'] == 'sarcastic' ? ' selected': ''?> value="sarcastic"><?php echo esc_html__('Sarcastic','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['tone'] == 'humorous' ? ' selected': ''?> value="humorous"><?php echo esc_html__('Humorous','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['tone'] == 'cheerful' ? ' selected': ''?> value="cheerful"><?php echo esc_html__('Cheerful','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['tone'] == 'anecdotal' ? ' selected': ''?> value="anecdotal"><?php echo esc_html__('Anecdotal','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Act As','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_chat_shortcode_options[profession]">
                            <option<?php echo $wpaicg_settings['profession'] == 'none' ? ' selected': ''?> value="none"><?php echo esc_html__('None','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'accountant' ? ' selected': ''?> value="accountant"><?php echo esc_html__('Accountant','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'advertisingspecialist' ? ' selected': ''?> value="advertisingspecialist"><?php echo esc_html__('Advertising Specialist','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'architect' ? ' selected': ''?> value="architect"><?php echo esc_html__('Architect','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'artist' ? ' selected': ''?> value="artist"><?php echo esc_html__('Artist','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'blogger' ? ' selected': ''?> value="blogger"><?php echo esc_html__('Blogger','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'businessanalyst' ? ' selected': ''?> value="businessanalyst"><?php echo esc_html__('Business Analyst','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'businessowner' ? ' selected': ''?> value="businessowner"><?php echo esc_html__('Business Owner','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'carexpert' ? ' selected': ''?> value="carexpert"><?php echo esc_html__('Car Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'consultant' ? ' selected': ''?> value="consultant"><?php echo esc_html__('Consultant','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'counselor' ? ' selected': ''?> value="counselor"><?php echo esc_html__('Counselor','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'cryptocurrencytrader' ? ' selected': ''?> value="cryptocurrencytrader"><?php echo esc_html__('Cryptocurrency Trader','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'cryptocurrencyexpert' ? ' selected': ''?> value="cryptocurrencyexpert"><?php echo esc_html__('Cryptocurrency Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'customersupport' ? ' selected': ''?> value="customersupport"><?php echo esc_html__('Customer Support','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'designer' ? ' selected': ''?> value="designer"><?php echo esc_html__('Designer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'digitalmarketinagency' ? ' selected': ''?> value="digitalmarketinagency"><?php echo esc_html__('Digital Marketing Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'editor' ? ' selected': ''?> value="editor"><?php echo esc_html__('Editor','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'engineer' ? ' selected': ''?> value="engineer"><?php echo esc_html__('Engineer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'eventplanner' ? ' selected': ''?> value="eventplanner"><?php echo esc_html__('Event Planner','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'freelancer' ? ' selected': ''?> value="freelancer"><?php echo esc_html__('Freelancer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'insuranceagent' ? ' selected': ''?> value="insuranceagent"><?php echo esc_html__('Insurance Agent','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'insurancebroker' ? ' selected': ''?> value="insurancebroker"><?php echo esc_html__('Insurance Broker','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'interiordesigner' ? ' selected': ''?> value="interiordesigner"><?php echo esc_html__('Interior Designer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'journalist' ? ' selected': ''?> value="journalist"><?php echo esc_html__('Journalist','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'marketingagency' ? ' selected': ''?> value="marketingagency"><?php echo esc_html__('Marketing Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'marketingexpert' ? ' selected': ''?> value="marketingexpert"><?php echo esc_html__('Marketing Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'marketingspecialist' ? ' selected': ''?> value="marketingspecialist"><?php echo esc_html__('Marketing Specialist','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'photographer' ? ' selected': ''?> value="photographer"><?php echo esc_html__('Photographer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'programmer' ? ' selected': ''?> value="programmer"><?php echo esc_html__('Programmer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'publicrelationsagency' ? ' selected': ''?> value="publicrelationsagency"><?php echo esc_html__('Public Relations Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'publisher' ? ' selected': ''?> value="publisher"><?php echo esc_html__('Publisher','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'realestateagent' ? ' selected': ''?> value="realestateagent"><?php echo esc_html__('Real Estate Agent','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'recruiter' ? ' selected': ''?> value="recruiter"><?php echo esc_html__('Recruiter','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'reporter' ? ' selected': ''?> value="reporter"><?php echo esc_html__('Reporter','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'salesperson' ? ' selected': ''?> value="salesperson"><?php echo esc_html__('Sales Person','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'salerep' ? ' selected': ''?> value="salerep"><?php echo esc_html__('Sales Representative','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'seoagency' ? ' selected': ''?> value="seoagency"><?php echo esc_html__('SEO Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'seoexpert' ? ' selected': ''?> value="seoexpert"><?php echo esc_html__('SEO Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'socialmediaagency' ? ' selected': ''?> value="socialmediaagency"><?php echo esc_html__('Social Media Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'student' ? ' selected': ''?> value="student"><?php echo esc_html__('Student','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'teacher' ? ' selected': ''?> value="teacher"><?php echo esc_html__('Teacher','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'technicalsupport' ? ' selected': ''?> value="technicalsupport"><?php echo esc_html__('Technical Support','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'trainer' ? ' selected': ''?> value="trainer"><?php echo esc_html__('Trainer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'travelagency' ? ' selected': ''?> value="travelagency"><?php echo esc_html__('Travel Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'videographer' ? ' selected': ''?> value="videographer"><?php echo esc_html__('Videographer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdesignagency' ? ' selected': ''?> value="webdesignagency"><?php echo esc_html__('Web Design Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdesignexpert' ? ' selected': ''?> value="webdesignexpert"><?php echo esc_html__('Web Design Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdevelopmentagency' ? ' selected': ''?> value="webdevelopmentagency"><?php echo esc_html__('Web Development Agency','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdevelopmentexpert' ? ' selected': ''?> value="webdevelopmentexpert"><?php echo esc_html__('Web Development Expert','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdesigner' ? ' selected': ''?> value="webdesigner"><?php echo esc_html__('Web Designer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdeveloper' ? ' selected': ''?> value="webdeveloper"><?php echo esc_html__('Web Developer','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['profession'] == 'writer' ? ' selected': ''?> value="writer"><?php echo esc_html__('Writer','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                </div>
            </div>
            <!--Style-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span><?php echo esc_html__('Style','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Font Size','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_shortcode_options[fontsize]" class="wpaicg_chat_shortcode_font_size">
                            <?php
                            for($i = 10; $i <= 30; $i++){
                                echo '<option'.($wpaicg_settings['fontsize'] == $i ? ' selected': '').' value="'.esc_html($i).'">'.esc_html($i).'px</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Width','gpt3-ai-content-generator')?>:</label>
                        <input min="300" type="text" class="wpaicg_chat_shortcode_width" name="wpaicg_chat_shortcode_options[width]" value="<?php
                        echo  esc_html( $wpaicg_settings['width'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Height','gpt3-ai-content-generator')?>:</label>
                        <input min="300" type="text" class="wpaicg_chat_shortcode_height" name="wpaicg_chat_shortcode_options[height]" value="<?php
                        echo  esc_html( $wpaicg_settings['height'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Background Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['bgcolor'])?>" type="text" class="wpaicgchat_color wpaicg_bgcolor" name="wpaicg_chat_shortcode_options[bgcolor]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Font Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['fontcolor'])?>" type="text" class="wpaicgchat_color wpaicg_font_color" name="wpaicg_chat_shortcode_options[fontcolor]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Border Radius - Window','gpt3-ai-content-generator')?>:</label>
                        <input style="width: 80px" value="<?php echo esc_html($wpaicg_settings['chat_rounded'])?>" type="number" min="0" class="wpaicg_chat_rounded" name="wpaicg_chat_shortcode_options[chat_rounded]">px
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Text Field Height','gpt3-ai-content-generator')?>:</label>
                        <input style="width: 80px" value="<?php echo esc_html($wpaicg_settings['text_height'])?>" type="number" min="30" class="wpaicg_text_height" name="wpaicg_chat_shortcode_options[text_height]">px
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Border Radius - Text Field','gpt3-ai-content-generator')?>:</label>
                        <input style="width: 80px" value="<?php echo esc_html($wpaicg_settings['text_rounded'])?>" type="number" min="0" class="wpaicg_text_rounded" name="wpaicg_chat_shortcode_options[text_rounded]">px
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Text Field Background','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['bg_text_field'])?>" type="text" class="wpaicgchat_color wpaicg_bg_text_field" name="wpaicg_chat_shortcode_options[bg_text_field]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Text Field Border','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['border_text_field'])?>" type="text" class="wpaicgchat_color wpaicg_border_text_field" name="wpaicg_chat_shortcode_options[border_text_field]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Button Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['send_color'])?>" type="text" class="wpaicgchat_color wpaicg_send_color" name="wpaicg_chat_shortcode_options[send_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('User Background Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['user_bg_color'])?>" type="text" class="wpaicgchat_color wpaicg_user_bg_color" name="wpaicg_chat_shortcode_options[user_bg_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Background Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['ai_bg_color'])?>" type="text" class="wpaicgchat_color wpaicg_ai_bg_color" name="wpaicg_chat_shortcode_options[ai_bg_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Use Avatars','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_settings['use_avatar'] ? ' checked': ''?> class="wpaicg_chat_shortcode_use_avatar" value="1" type="checkbox" name="wpaicg_chat_shortcode_options[use_avatar]">
                    </div>
                    <input value="<?php echo esc_html($wpaicg_settings['ai_icon_url'])?>" type="hidden" name="wpaicg_chat_shortcode_options[ai_icon_url]" class="wpaicg_chat_icon_url">
                    <div class="wpcgai_form_row">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Avatar (40x40)','gpt3-ai-content-generator')?>:</label>
                        <div style="display: inline-flex; align-items: center">
                            <input<?php echo $wpaicg_settings['ai_icon'] == 'default' ? ' checked': ''?> class="wpaicg_chatbox_icon_default" type="radio" value="default" name="wpaicg_chat_shortcode_options[ai_icon]">
                            <div style="text-align: center">
                                <img style="display: block;width: 40px; height: 40px;" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>"<br>
                                <strong><?php echo esc_html__('Default','gpt3-ai-content-generator')?></strong>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input<?php echo $wpaicg_settings['ai_icon'] == 'custom' ? ' checked': ''?> type="radio" class="wpaicg_chatbox_icon_custom" value="custom" name="wpaicg_chat_shortcode_options[ai_icon]">
                            <div style="text-align: center">
                                <div class="wpaicg_chatbox_icon">
                                    <?php
                                    if(!empty($wpaicg_settings['ai_icon_url']) && $wpaicg_settings['ai_icon'] == 'custom'):
                                        $wpaicg_chatbox_icon_url = wp_get_attachment_url($wpaicg_settings['ai_icon_url']);
                                        ?>
                                        <img src="<?php echo esc_html($wpaicg_chatbox_icon_url)?>" width="40" height="40">
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
                        <label class="wpaicg-form-label"><?php echo esc_html__('Fullscreen Button','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_fullscreen ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_fullscreen" name="wpaicg_chat_shortcode_options[fullscreen]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Download Button','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_download_btn ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_download_btn" name="wpaicg_chat_shortcode_options[download_btn]">
                    </div>
                    <div class="mb-5" style="position: relative">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Bar Icons Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_bar_color)?>" type="text" class="wpaicgchat_color wpaicgchat_bar_color" name="wpaicg_chat_shortcode_options[bar_color]">
                    </div>
                    <div class="mb-5" style="position: relative">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Thinking Text Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_thinking_color)?>" type="text" class="wpaicgchat_color wpaicgchat_thinking_color" name="wpaicg_chat_shortcode_options[thinking_color]">
                    </div>
                    <?php
                    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('PDF Icon Color','gpt3-ai-content-generator')?>:</label>
                            <input value="<?php echo esc_html($wpaicg_settings['pdf_color'])?>" type="text" class="wpaicgchat_color wpaicg_pdf_color" name="wpaicg_chat_shortcode_options[pdf_color]">
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
            <!--AI Engine-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span><?php echo esc_html__('Parameters','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Model','gpt3-ai-content-generator')?>:</label>
                        <select class="regular-text" id="wpaicg_chat_model"  name="wpaicg_chat_shortcode_options[model]" >
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
                                echo '<option'.($wpaicg_settings['model'] == $wpaicg_custom_model ? ' selected':'').' value="'.esc_html($wpaicg_custom_model).'">'.esc_html($wpaicg_custom_model).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Temperature','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_temperature" name="wpaicg_chat_shortcode_options[temperature]" value="<?php
                        echo  esc_html( $wpaicg_settings['temperature'] ) ;
                        ?>">
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Max Tokens','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_max_tokens" name="wpaicg_chat_shortcode_options[max_tokens]" value="<?php
                        echo  esc_html( $wpaicg_settings['max_tokens'] ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Top P','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_top_p" name="wpaicg_chat_shortcode_options[top_p]" value="<?php
                        echo  esc_html( $wpaicg_settings['top_p'] ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Best Of','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_best_of" name="wpaicg_chat_shortcode_options[best_of]" value="<?php
                        echo  esc_html( $wpaicg_settings['best_of'] ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Frequency Penalty','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_frequency_penalty" name="wpaicg_chat_shortcode_options[frequency_penalty]" value="<?php
                        echo  esc_html( $wpaicg_settings['frequency_penalty'] ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Presence Penalty','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text" id="label_presence_penalty" name="wpaicg_chat_shortcode_options[presence_penalty]" value="<?php
                        echo  esc_html( $wpaicg_settings['presence_penalty'] ) ;
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
                            <input<?php echo isset($wpaicg_settings['moderation']) && $wpaicg_settings['moderation'] ? ' checked': ''?>  name="wpaicg_chat_shortcode_options[moderation]" value="1" type="checkbox">
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Model','gpt3-ai-content-generator')?>:</label>
                            <select class="regular-text"  name="wpaicg_chat_shortcode_options[moderation_model]" >
                                <option<?php echo isset($wpaicg_settings['moderation_model']) && $wpaicg_settings['moderation_model'] == 'text-moderation-latest' ? ' selected':'';?> value="text-moderation-latest">text-moderation-latest</option>
                                <option<?php echo isset($wpaicg_settings['moderation_model']) && $wpaicg_settings['moderation_model'] == 'text-moderation-stable' ? ' selected':'';?> value="text-moderation-stable">text-moderation-stable</option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Notice','gpt3-ai-content-generator')?>:</label>
                            <textarea rows="8" name="wpaicg_chat_shortcode_options[moderation_notice]"><?php echo isset($wpaicg_settings['moderation_notice']) ? esc_html($wpaicg_settings['moderation_notice']) : ''?></textarea>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
            <!--Audio-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span><?php echo esc_html__('VoiceChat','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Enable Speech to Text','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo isset($wpaicg_settings['audio_enable']) && $wpaicg_settings['audio_enable'] ? ' checked': ''?> value="1" type="checkbox" class="wpaicg_audio_enable" name="wpaicg_chat_shortcode_options[audio_enable]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Mic Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['mic_color'])?>" type="text" class="wpaicgchat_color wpaicg_mic_color" name="wpaicg_chat_shortcode_options[mic_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Stop Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['stop_color'])?>" type="text" class="wpaicgchat_color wpaicg_stop_color" name="wpaicg_chat_shortcode_options[stop_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Enable Text to Speech','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo empty($wpaicg_elevenlabs_api) && empty($wpaicg_google_api_key) ? ' disabled':''?><?php echo (!empty($wpaicg_elevenlabs_api) || !empty($wpaicg_google_api_key)) && $wpaicg_chat_to_speech ? ' checked':''?> value="1" type="checkbox" name="wpaicg_chat_shortcode_options[chat_to_speech]" class="wpaicg_chat_to_speech">
                    </div>
                    <?php
                    $disabled_voice_fields = false;
                    if(!$wpaicg_chat_to_speech){
                        $disabled_voice_fields = true;
                    }
                    ?>
                    <div class="mb-5" style="<?php echo empty($wpaicg_google_api_key) && empty($wpaicg_elevenlabs_api) ? ' display:none' : ''?>">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Provider','gpt3-ai-content-generator')?>:</label>
                        <select<?php echo $disabled_voice_fields || (empty($wpaicg_google_api_key) && empty($wpaicg_elevenlabs_api))  ? ' disabled': ''?> name="wpaicg_chat_shortcode_options[voice_service]" class="wpaicg_voice_service">
                            <option value=""><?php echo esc_html__('ElevenLabs','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_chat_voice_service == 'google' ? ' selected':'';?> value="google"><?php echo esc_html__('Google','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="wpaicg_voice_service_google" style="<?php echo $wpaicg_chat_voice_service == 'google' && (!empty($wpaicg_google_api_key) || !empty($wpaicg_elevenlabs_api)) ? '' : 'display:none'?>">
                        <?php
                        $wpaicg_voice_language = isset($wpaicg_settings['voice_language']) && !empty($wpaicg_settings['voice_language']) ? $wpaicg_settings['voice_language'] : 'en-US';
                        $wpaicg_voice_name = isset($wpaicg_settings['voice_name']) && !empty($wpaicg_settings['voice_name']) ? $wpaicg_settings['voice_name'] : 'en-US-Studio-M';
                        $wpaicg_voice_device = isset($wpaicg_settings['voice_device']) && !empty($wpaicg_settings['voice_device']) ? $wpaicg_settings['voice_device'] : '';
                        $wpaicg_voice_speed = isset($wpaicg_settings['voice_speed']) && !empty($wpaicg_settings['voice_speed']) ? $wpaicg_settings['voice_speed'] : 1;
                        $wpaicg_voice_pitch = isset($wpaicg_settings['voice_pitch']) && !empty($wpaicg_settings['voice_pitch']) ? $wpaicg_settings['voice_pitch'] : 0;
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Language','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_shortcode_options[voice_language]" class="wpaicg_voice_language">
                                <?php
                                foreach(\WPAICG\WPAICG_Google_Speech::get_instance()->languages as $key=>$voice_language){
                                    echo '<option'.($wpaicg_voice_language == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($voice_language).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Name','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> data-value="<?php echo esc_html($wpaicg_voice_name)?>" name="wpaicg_chat_shortcode_options[voice_name]" class="wpaicg_voice_name">
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Audio Device Profile','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_shortcode_options[voice_device]" class="wpaicg_voice_device">
                                <?php
                                foreach(\WPAICG\WPAICG_Google_Speech::get_instance()->devices() as $key => $device){
                                    echo '<option'.($wpaicg_voice_device == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($device).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Speed','gpt3-ai-content-generator')?>:</label>
                            <input<?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> type="text" class="wpaicg_voice_speed" value="<?php echo esc_html($wpaicg_voice_speed)?>" name="wpaicg_chat_shortcode_options[voice_speed]">
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Pitch','gpt3-ai-content-generator')?>:</label>
                            <input<?php echo empty($wpaicg_google_api_key) ||$disabled_voice_fields ? ' disabled':''?> type="text" class="wpaicg_voice_pitch" value="<?php echo esc_html($wpaicg_voice_pitch)?>" name="wpaicg_chat_shortcode_options[voice_pitch]">
                        </div>
                    </div>
                    <div class="wpaicg_voice_service_elevenlabs" style="<?php echo $wpaicg_chat_voice_service == 'google' || (empty($wpaicg_google_api_key) && empty($wpaicg_elevenlabs_api)) ? 'display:none' : ''?>">
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Select a Voice','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo empty($wpaicg_elevenlabs_api) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_shortcode_options[elevenlabs_voice]" class="wpaicg_elevenlabs_voice">
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
            <!--Text message-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span><?php echo esc_html__('Custom Text','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Name','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text wpaicg_chat_shortcode_ai_name" name="wpaicg_chat_shortcode_options[ai_name]" value="<?php
                        echo  esc_html( $wpaicg_settings['ai_name'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('You','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text wpaicg_chat_shortcode_you" name="wpaicg_chat_shortcode_options[you]" value="<?php
                        echo  esc_html( $wpaicg_settings['you'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Thinking','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text wpaicg_chat_shortcode_ai_thinking" name="wpaicg_chat_shortcode_options[ai_thinking]" value="<?php
                        echo  esc_html( $wpaicg_settings['ai_thinking'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Placeholder','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text wpaicg_chat_shortcode_placeholder" name="wpaicg_chat_shortcode_options[placeholder]" value="<?php
                        echo  esc_html( $wpaicg_settings['placeholder'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Welcome Message','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text wpaicg_chat_shortcode_welcome" name="wpaicg_chat_shortcode_options[welcome]" value="<?php
                        echo  esc_html( $wpaicg_settings['welcome'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('No Answer Message','gpt3-ai-content-generator')?>:</label>
                        <input class="regular-text" type="text" value="<?php echo esc_html($wpaicg_settings['no_answer'])?>" name="wpaicg_chat_shortcode_options[no_answer]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Footer Note','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['footer_text'])?>" type="text" name="wpaicg_chat_shortcode_options[footer_text]" placeholder="<?php echo esc_html__('Powered by ...','gpt3-ai-content-generator')?>">
                    </div>
                </div>
            </div>
            <!--Context-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span><?php echo esc_html__('Context','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <?php
                    $wpaicg_chat_addition = false;
                    if(!isset($wpaicg_settings['chat_addition_option']) || $wpaicg_settings['chat_addition']){
                        $wpaicg_chat_addition = true;
                    }
                    ?>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Additional Context?','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_addition ? ' checked': ''?> name="wpaicg_chat_shortcode_options[chat_addition]" value="1" type="checkbox" id="wpaicg_chat_addition">
                        <input name="wpaicg_chat_shortcode_options[chat_addition_option]" value="<?php echo $wpaicg_chat_addition ? 0 : 1?>" type="hidden" id="wpaicg_chat_addition_option">
                    </div>
                    <?php
                    $wpaicg_additions_json = file_get_contents(WPAICG_PLUGIN_DIR.'admin/chat/context.json');
                    $wpaicg_additions = json_decode($wpaicg_additions_json, true);
                    $wpaicg_settings['chat_addition_text'] = str_replace("\\",'',$wpaicg_settings['chat_addition_text']);
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
                        <label class="wpaicg-form-label">
                            <?php echo esc_html__('Context','gpt3-ai-content-generator')?>:
                            <small style="font-weight: normal;display: block"><?php echo sprintf(esc_html__('You can include the following shortcode in the context: %s, %s, %s, and %s.','gpt3-ai-content-generator'),'<code>[sitename]</code>','<code>[siteurl]</code>','<code>[domain]</code>','<code>[date]</code>')?></small>
                        </label>
                        <textarea<?php echo !$wpaicg_chat_addition ? ' disabled':''?> name="wpaicg_chat_shortcode_options[chat_addition_text]" id="wpaicg_chat_addition_text" class="regular-text wpaicg_chat_addition_text" rows="8"><?php echo !empty($wpaicg_settings['chat_addition_text']) ? esc_html($wpaicg_settings['chat_addition_text']) : esc_html__('You are a helpful AI Assistant. Please be friendly.','gpt3-ai-content-generator')?></textarea>

                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Remember Conversation','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_shortcode_options[remember_conversation]">
                            <option<?php echo $wpaicg_settings['remember_conversation'] == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['remember_conversation'] == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Remember Conv. Up To','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_shortcode_options[conversation_cut]">
                            <?php
                            for($i=3;$i<=20;$i++){
                                echo '<option'.($wpaicg_settings['conversation_cut'] == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('User Aware','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_shortcode_options[user_aware]">
                        <option<?php echo $wpaicg_settings['user_aware'] == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['user_aware'] == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Content Aware','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_shortcode_options[content_aware]" id="wpaicg_chat_content_aware">
                            <option<?php echo $wpaicg_settings['content_aware'] == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                            <option<?php echo $wpaicg_settings['content_aware'] == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Use Excerpt','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? ' checked': ''?><?php echo $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> type="checkbox" id="wpaicg_chat_excerpt" class="<?php echo $wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Use Embeddings','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? ' checked': ''?><?php echo $wpaicg_embedding_field_disabled || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> type="checkbox" value="1" name="wpaicg_chat_shortcode_options[embedding]" id="wpaicg_chat_embedding" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Pinecone Index','gpt3-ai-content-generator')?>:</label>
                        <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> name="wpaicg_chat_shortcode_options[embedding_index]" id="wpaicg_chat_embedding_index" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                            <option value=""><?php echo esc_html__('Default','gpt3-ai-content-generator')?></option>
                            <?php
                            foreach($wpaicg_pinecone_indexes as $wpaicg_pinecone_index){
                                echo '<option'.(isset($wpaicg_settings['embedding_index']) && $wpaicg_settings['embedding_index'] == $wpaicg_pinecone_index['url'] ? ' selected':'').' value="'.esc_html($wpaicg_pinecone_index['url']).'">'.esc_html($wpaicg_pinecone_index['name']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Method','gpt3-ai-content-generator')?>:</label>
                        <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> name="wpaicg_chat_shortcode_options[embedding_type]" id="wpaicg_chat_embedding_type" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                            <option<?php echo $wpaicg_settings['embedding_type'] ? ' selected':'';?> value="openai"><?php echo esc_html__('Embeddings + Completion','gpt3-ai-content-generator')?></option>
                            <option<?php echo empty($wpaicg_settings['embedding_type']) ? ' selected':''?> value=""><?php echo esc_html__('Embeddings only','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Nearest Answers Up To','gpt3-ai-content-generator')?>:</label>
                        <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> name="wpaicg_chat_shortcode_options[embedding_top]" id="wpaicg_chat_embedding_top" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                            <?php
                            for($i = 1; $i <=5;$i++){
                                echo '<option'.($wpaicg_settings['embedding_top'] == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <?php
                    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Enable PDF Upload','gpt3-ai-content-generator')?>:</label>
                            <input<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?><?php echo isset($wpaicg_settings['embedding_pdf']) && $wpaicg_settings['embedding_pdf'] ? ' checked':''?> type="checkbox" value="1" name="wpaicg_chat_shortcode_options[embedding_pdf]" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>" id="wpaicg_chat_embedding_pdf">
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Limit PDF Pages','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> name="wpaicg_chat_shortcode_options[pdf_pages]" id="wpaicg_chat_pdf_pages" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>" style="width: 65px!important;">
                                <?php
                                $pdf_pages = isset($wpaicg_settings['pdf_pages']) && !empty($wpaicg_settings['pdf_pages']) ? $wpaicg_settings['pdf_pages'] : 120;
                                for($i=1;$i <= 120;$i++){
                                    echo '<option'.($pdf_pages == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label">
                                <?php echo esc_html__('PDF Success Message','gpt3-ai-content-generator')?>:
                                <small style="font-weight: normal;display: block"><?php echo sprintf(esc_html__('You can include the following shortcode in the message: %s.','gpt3-ai-content-generator'),'<code>[questions]</code>')?></small>
                            </label>
                            <textarea<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> rows="8" name="wpaicg_chat_shortcode_options[embedding_pdf_message]" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>" id="wpaicg_chat_embedding_pdf_message"><?php echo isset($wpaicg_settings['embedding_pdf_message']) && $wpaicg_settings['embedding_pdf_message'] ? esc_html(str_replace("\\",'',$wpaicg_settings['embedding_pdf_message'])):''?></textarea>
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
            <!--Log-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span><?php echo esc_html__('Logs','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Save Chat Logs','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_save_logs ? ' checked': ''?> value="1" type="checkbox" class="wpaicg_chatbot_save_logs" name="wpaicg_chat_shortcode_options[save_logs]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Save Prompt','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_save_logs ? '' : ' disabled'?><?php echo $wpaicg_save_logs && isset($wpaicg_settings['log_request']) && $wpaicg_settings['log_request'] ? ' checked' : ''?> class="wpaicg_chatbot_log_request" value="1" type="checkbox" name="wpaicg_chat_shortcode_options[log_request]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Display Notice','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_save_logs ? '': ' disabled'?><?php echo $wpaicg_log_notice ? ' checked': ''?> value="1" class="wpaicg_chatbot_log_notice" type="checkbox" name="wpaicg_chat_shortcode_options[log_notice]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Notice Text','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_save_logs ? '': ' disabled'?> value="<?php echo esc_html($wpaicg_log_notice_message)?>" class="wpaicg_chatbot_log_notice_message" type="text" name="wpaicg_chat_shortcode_options[log_notice_message]">
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
                        <input<?php echo $wpaicg_user_limited ? ' checked': ''?> type="checkbox" value="1" class="wpaicg_user_token_limit" name="wpaicg_chat_shortcode_options[user_limited]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Token Limit','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_user_limited ? '' : ' disabled'?> style="width: 80px" class="wpaicg_user_token_limit_text" type="text" value="<?php echo esc_html($wpaicg_user_tokens)?>" name="wpaicg_chat_shortcode_options[user_tokens]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Role based limit','gpt3-ai-content-generator')?>:</label>
                        <?php
                        foreach($wpaicg_roles as $key=>$wpaicg_role){
                            echo '<input class="wpaicg_role_'.esc_html($key).'" value="'.(isset($wpaicg_settings['limited_roles'][$key]) && !empty($wpaicg_settings['limited_roles'][$key]) ? esc_html($wpaicg_settings['limited_roles'][$key]) : '').'" type="hidden" name="wpaicg_chat_shortcode_options[limited_roles]['.esc_html($key).']">';
                        }
                        ?>
                        <input<?php echo $wpaicg_user_limited ? '': ($wpaicg_settings['role_limited'] ? ' checked':'')?> type="checkbox" value="1" class="wpaicg_role_limited" name="wpaicg_chat_shortcode_options[role_limited]">
                        <a href="javascript:void(0)" class="wpaicg_limit_set_role<?php echo $wpaicg_user_limited || !$wpaicg_settings['role_limited'] ? ' disabled': ''?>"><?php echo esc_html__('Set Limit','gpt3-ai-content-generator')?></a>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Limit Non-Registered User','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_guest_limited ? ' checked': ''?> type="checkbox" class="wpaicg_guest_token_limit" value="1" name="wpaicg_chat_shortcode_options[guest_limited]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Token Limit','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_guest_limited ? '' : ' disabled'?> class="wpaicg_guest_token_limit_text" style="width: 80px" type="text" value="<?php echo esc_html($wpaicg_guest_tokens)?>" name="wpaicg_chat_shortcode_options[guest_tokens]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Notice','gpt3-ai-content-generator')?>:</label>
                        <input type="text" value="<?php echo esc_html($wpaicg_limited_message)?>" name="wpaicg_chat_shortcode_options[limited_message]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Reset Limit','gpt3-ai-content-generator')?>:</label>
                        <select name="wpaicg_chat_shortcode_options[reset_limit]">
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

            <button class="button button-primary wpaicg-w-100"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
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
        $(document).on('keypress','.wpaicg_user_token_limit_text,.wpaicg_update_role_limit,.wpaicg_guest_token_limit_text', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
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
        $('.wpaicg_limit_set_role').click(function (){
            if(!$(this).hasClass('disabled')) {
                if ($('.wpaicg_role_limited').prop('checked')) {
                    let html = '';
                    $.each(wpaicg_roles, function (key, role) {
                        let valueRole = $('.wpaicg_role_'+key).val();
                        html += '<div style="padding: 5px;display: flex;justify-content: space-between;align-items: center;"><label><strong>'+role+'</strong></label><input class="wpaicg_update_role_limit" data-target="'+key+'" value="'+valueRole+'" placeholder="Empty for no-limit" type="text"></div>';
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
                $('#wpaicg_chat_embedding_index').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_index').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf_message').addClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').attr('disabled','disabled');
                $('#wpaicg_chat_pdf_pages').addClass('asdisabled');
                $('#wpaicg_chat_embedding_top').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').val(1);
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
                $('#wpaicg_chat_embedding_index').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf_message').removeClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').removeAttr('disabled');
                $('#wpaicg_chat_pdf_pages').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_top').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_top').removeAttr('disabled');
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
                $('#wpaicg_chat_embedding_index').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf_message').addClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').removeAttr('disabled');
                $('#wpaicg_chat_pdf_pages').addClass('asdisabled');
                $('#wpaicg_chat_embedding').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').addClass('asdisabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_top').addClass('asdisabled');
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
                $('#wpaicg_chat_embedding_index').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_index').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf_message').removeClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').attr('disabled','disabled');
                $('#wpaicg_chat_pdf_pages').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_top').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').removeClass('asdisabled');
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
        $('.wpaicg_font_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-user-message').css('color', color);
                $('.wpaicg-ai-message').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-user-message').css('color', '');
                $('.wpaicg-ai-message').css('color', '');
            }
        });
        $('.wpaicg_pdf_color').wpColorPicker();
        $('.wpaicg_audio_enable').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg-mic-icon').show();
            }
            else{
                $('.wpaicg-mic-icon').hide();
            }
        })
        $('.wpaicg_mic_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-mic-icon').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-mic-icon').css('color', '');
            }
        });
        $('.wpaicg_stop_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-mic-icon').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-mic-icon').css('color', '');
            }
        });
        $('.wpaicgchat_thinking_color').wpColorPicker();
        $('.wpaicg_user_bg_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-user-message').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-user-message').css('background-color', '');
            }
        });
        $('.wpaicg_bgcolor').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode').css('background-color', '#222');
            }
        });
        $('.wpaicg_bg_text_field').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode-typing').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode-typing').css('background-color', '#fff');
            }
        });
        $('.wpaicg_border_text_field').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode-typing').css('border-color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode-typing').css('border-color', '#ccc');
            }
        });
        $('.wpaicg_send_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode-send').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode-send').css('color', '#fff');
            }
        });
        $('.wpaicgchat_bar_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chatbox-action-bar').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-chatbox-action-bar').css('color', '');
            }
        });
        $('.wpaicg_ai_bg_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-ai-message').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-ai-message').css('background-color', '');
            }
        });
        $('.wpaicg_chat_shortcode_width').on('input', function (){
            var chatbox_width = $(this).val();
            var preview_width = $('.wpaicg-chat-shortcode-preview').width();
            if(chatbox_width.indexOf('%') > -1){
                chatbox_width = chatbox_width.replace('%','');
                chatbox_width = parseFloat(chatbox_width);
                chatbox_width = chatbox_width*preview_width/100;
            }
            else{
                chatbox_width = chatbox_width.replace('px','');
                chatbox_width = parseFloat(chatbox_width);
            }
            if(chatbox_width > preview_width){
                chatbox_width = preview_width;
            }
            $('.wpaicg-chat-shortcode').width(chatbox_width+'px');
            $('.wpaicg-chat-shortcode').attr('data-width',chatbox_width);
            wpaicgChatShortcodeSize();
        });
        $('.wpaicg_chat_rounded,.wpaicg_text_height,wpaicg_text_rounded').on('input', function (){
            $('.wpaicg-chat-shortcode').attr('data-chat_rounded',$('.wpaicg_chat_rounded').val());
            $('.wpaicg-chat-shortcode').attr('data-text_height',$('.wpaicg_text_height').val());
            $('.wpaicg-chat-shortcode').attr('data-text_rounded',$('.wpaicg_text_rounded').val());
            wpaicgChatShortcodeSize();
        })
        $('.wpaicg_chat_shortcode_height').on('input', function (){
            var chatbox_height = $(this).val();
            var preview_width = $(window).height();
            if(chatbox_height.indexOf('%') > -1){
                chatbox_height = chatbox_height.replace('%','');
                chatbox_height = parseFloat(chatbox_height);
                chatbox_height = chatbox_height*preview_width/100;
            }
            else{
                chatbox_height = chatbox_height.replace('px','');
                chatbox_height = parseFloat(chatbox_height);
            }
            if(chatbox_height > preview_width){
                chatbox_height = preview_width;
            }
            $('.wpaicg-chat-shortcode-content ul').height((chatbox_height - 44)+'px');
            $('.wpaicg-chat-shortcode').attr('data-height',chatbox_height);
        });
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
                    button.html('<img width="40" height="40" src="'+attachment.url+'">');
                    $('.wpaicg_chat_icon_url').val(attachment.id);
                }).open();
        });
        $('.wpaicg_chat_shortcode_font_size').on('change', function (){
            var font_size = $(this).val();
            $('.wpaicg-chat-shortcode-messages li').each(function (idx, item){
                $(item).css('font-size',font_size+'px');
            })
        });
        function wpaicgChangeAvatarRealtime(){
            var wpaicg_user_avatar_check = $('.wpaicg_chat_shortcode_you').val()+':';
            var wpaicg_ai_avatar_check = $('.wpaicg_chat_shortcode_ai_name').val()+':';
            if($('.wpaicg_chat_shortcode_use_avatar').prop('checked')){
                wpaicg_user_avatar_check = '<img src="<?php echo get_avatar_url(get_current_user_id())?>" height="40" width="40">';
                wpaicg_ai_avatar_check = '<?php echo esc_html(WPAICG_PLUGIN_URL) . 'admin/images/chatbot.png';?>';
                if($('.wpaicg_chatbox_icon_custom').prop('checked') && $('.wpaicg_chatbox_icon img').length){
                    wpaicg_ai_avatar_check = $('.wpaicg_chatbox_icon img').attr('src');
                }
                wpaicg_ai_avatar_check = '<img src="'+wpaicg_ai_avatar_check+'" height="40" width="40">';
            }

            $('.wpaicg-chat-shortcode-messages li.wpaicg-ai-message').each(function (idx, item){
                $(item).find('.wpaicg-chat-avatar').html(wpaicg_ai_avatar_check);
            });
            $('.wpaicg-chat-shortcode-messages li.wpaicg-user-message').each(function (idx, item){
                $(item).find('.wpaicg-chat-avatar').html(wpaicg_user_avatar_check);
            });
        }
        $('.wpaicg_chat_shortcode_ai_name,.wpaicg_chat_shortcode_you').on('input', function (){
            wpaicgChangeAvatarRealtime();
        })
        $('.wpaicg_chat_shortcode_use_avatar,.wpaicg_chatbox_icon_default,.wpaicg_chatbox_icon_custom').on('click', function (){
            wpaicgChangeAvatarRealtime();
        })
    })
</script>
