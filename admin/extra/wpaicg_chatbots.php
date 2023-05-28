<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb,$wp;
if(isset($_GET['wpaicg_bot_delete']) && !empty($_GET['wpaicg_bot_delete'])){
    if(!wp_verify_nonce($_GET['_wpnonce'], 'wpaicg_delete_'.sanitize_text_field($_GET['wpaicg_bot_delete']))){
        die(WPAICG_NONCE_ERROR);
    }
    wp_delete_post(sanitize_text_field($_GET['wpaicg_bot_delete']));
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_chatgpt&action=bots').'"</script>';
    exit;
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
$wpaicg_chat_icon = 'default';
$wpaicg_chat_fontsize = '13';
$wpaicg_chat_fontcolor = '#fff';
$wpaicg_chat_bgcolor = '#222222';
$wpaicg_bg_text_field = '#fff';
$wpaicg_send_color = '#fff';
$wpaicg_border_text_field = '#ccc';
$wpaicg_footer_text = '';
$wpaicg_user_bg_color = '#444654';
$wpaicg_ai_bg_color = '#343541';
$wpaicg_use_avatar = false;
$wpaicg_ai_avatar = 'default';
$wpaicg_ai_avatar_id = '';
$wpaicg_chat_width = '350';
$wpaicg_chat_height = '400';
$wpaicg_chat_position = 'left';
$wpaicg_chat_tone = 'friendly';
$wpaicg_user_aware = 'no';
$wpaicg_chat_proffesion = 'none';
$wpaicg_chat_icon_url = '';
$wpaicg_chat_remember_conversation = 'yes';
$wpaicg_chat_content_aware = 'yes';
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
$wpaicg_save_logs = false;
$wpaicg_log_notice = false;
$wpaicg_log_notice_message = __('Please note that your conversations will be recorded.','gpt3-ai-content-generator');
$wpaicg_conversation_cut = 10;
$wpaicg_embedding_field_disabled = empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment) ? true : false;
$wpaicg_chat_embedding = false;
$wpaicg_chat_addition = false;
$wpaicg_chat_addition_text = false;
$wpaicg_chat_embedding_type = false;
$wpaicg_chat_embedding_top = false;
$wpaicg_audio_enable = false;
$wpaicg_mic_color = '#222';
$wpaicg_stop_color = '#f00';
$wpaicg_user_limited = false;
$wpaicg_guest_limited = false;
$wpaicg_user_tokens = 0;
$wpaicg_guest_tokens = 0;
$wpaicg_reset_limit = 0;
$wpaicg_limited_message = __('You have reached your token limit.','gpt3-ai-content-generator');
$wpaicg_include_footer = 0;
$wpaicg_roles = wp_roles()->get_names();
$wpaicg_chat_close_btn = false;
$wpaicg_chat_download_btn = false;
$wpaicg_chat_fullscreen = false;
$wpaicg_elevenlabs_api = get_option('wpaicg_elevenlabs_api', '');
$wpaicg_google_api_key = get_option('wpaicg_google_api_key', '');
$wpaicg_google_voices = get_option('wpaicg_google_voices',[]);
$wpaicg_pinecone_indexes = get_option('wpaicg_pinecone_indexes','');
$wpaicg_pinecone_indexes = empty($wpaicg_pinecone_indexes) ? array() : json_decode($wpaicg_pinecone_indexes,true);
?>
<style>
    .wp-picker-holder{
        z-index: 99;
    }
    .wpaicg-bot-wizard{}
    .wpaicg-bot-wizard .wpaicg-mb-10{}
    .wpaicg-bot-wizard .wpaicg-form-label{
        width: 40%;
        display: inline-block;
    }
    .wpaicg-bot-wizard input[type=text],.wpaicg-bot-wizard input[type=number],.wpaicg-bot-wizard select{
        width: 55%;
        display: inline-block;
    }
    .wpaicg-bot-wizard textarea{
        width: 59%;
        display: inline-block;
    }
    .wpaicg_modal{
        top: 5%;
        height: 90%;
        position: relative;
    }
    .wpaicg_modal_content{
        max-height: calc(100% - 103px);
        overflow-y: auto;
    }
    .wp-picker-holder{
        position: absolute;
    }
    .wpaicg-chat-shortcode-send{
        display: flex;
        align-items: center;
        padding: 2px 3px;
        cursor: pointer;
    }
    .wpaicg-bot-thinking {
        bottom: 0;
        font-size: 11px;
        padding: 2px 6px;
        display: none;
    }
    .wpaicg-chat-shortcode-send svg{
        width: 30px;
        height: 30px;
        fill: currentColor;
        stroke: currentColor;
    }
    .wpaicg-chat-shortcode-type {
        display: flex;
        align-items: center;
        padding: 5px;
    }
    textarea.wpaicg-chat-shortcode-typing {
        flex: 1;
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 0 8px;
        min-height: 30px;
        line-height: 2;
        box-shadow: 0 0 0 transparent;
        margin: 0;
        resize: vertical; /* allows the user to resize the textarea vertically */
        overflow: auto;
        word-wrap: break-word;
    }
    .wpaicg-chat-shortcode-content ul {
        overflow-y: auto;
        margin: 0;
        padding: 0;
    }
    .wpaicg-chat-shortcode-content ul li {
        display: flex;
        margin-bottom: 0;
        padding: 10px;
    }
    .wpaicg-chat-shortcode-content ul li p {
        margin: 0;
        padding: 0;
    }
    .wp-picker-input-wrap input[type=text]{
        width: 4rem!important;
    }
    .wpaicg-chat-shortcode-content ul li p,.wpaicg-chat-shortcode-content ul li span{
        font-size: inherit;
    }
    .wpaicg-chat-shortcode-content ul li strong {
        font-weight: bold;
        margin-right: 5px;
        float: left;
    }
    .wpaicg_chat_additions{
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        right: 47px;
    }
    .wpaicg-mic-icon {
        cursor: pointer;
    }
    .wpaicg-mic-icon svg {
        width: 16px;
        height: 16px;
        fill: currentColor;
    }
    .wpaicg-chat-shortcode{
        border-radius: 4px;
        overflow: hidden;
    }
    .wpaicg-chat-shortcode-footer{
        height: 18px;
        font-size: 11px;
        padding: 0 5px;
        color: #424242;
        margin-bottom: 2px;
    }
    .wpaicg_chatbox_avatar,.wpaicg_chatbox_icon{
        cursor: pointer;
    }
    .asdisabled{
        background: #ebebeb!important;
    }
    .wpaicg-bot-footer{
        width: calc(100% - 31px);
        display: flex;
        bottom: 0px;
        position: absolute;
        margin-left: -21px;
        padding: 10px;
        border-top: 1px solid #d9d9d9;
        background: #fff;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }
    .wpaicg-bot-footer > div{
        flex: 1;
    }
    .wpaicg_modal_content{
    }
    .wpaicg-pdf-remove{
        color: #222;
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
    .wpaicg-grid-3{
        border: 1px solid #d9d9d9;
        border-radius: 5px;
        padding: 10px;
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
</style>
<div class="wpaicg-create-bot-default" style="display: none">
    <div class="wpaicg-grid">
        <div class="wpaicg-grid-3">
            <form action="" method="post" class="wpaicg-bot-form">
                <?php
                wp_nonce_field('wpaicg_chatbot_save');
                ?>
                <input value="<?php echo esc_html($wpaicg_chat_icon_url)?>" type="hidden" name="bot[icon_url]" class="wpaicg_chatbot_icon_url">
                <input value="<?php echo esc_html($wpaicg_ai_avatar_id)?>" type="hidden" name="bot[ai_avatar_id]" class="wpaicg_chatbot_ai_avatar_id">
                <input value="" type="hidden" name="bot[id]" class="wpaicg_chatbot_id">
                <input value="wpaicg_update_chatbot" type="hidden" name="action">
                <!--Type-->
                <div class="wpaicg-bot-type wpaicg-bot-wizard">
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Name','gpt3-ai-content-generator')?>:</label>
                        <input type="text" name="bot[name]" class="regular-text wpaicg_chatbot_name">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label><strong><?php echo esc_html__('What would you like to create?','gpt3-ai-content-generator')?></strong></label>
                    </div>
                    <div class="wpaicg-mb-10"><label><input type="radio" name="bot[type]" value="shortcode" class="wpaicg_chatbot_type_shortcode">&nbsp;<?php echo esc_html__('Shortcode','gpt3-ai-content-generator')?></label></div>
                    <div class="wpaicg-mb-10"><label><input type="radio" name="bot[type]" value="widget" class="wpaicg_chatbot_type_widget">&nbsp;<?php echo esc_html__('Widget','gpt3-ai-content-generator')?></label></div>
                    <div class="wpaicg-mb-10 wpaicg-widget-pages" style="display: none">
                    <div class="wpaicg-mb-10">
                        <label><strong><?php echo esc_html__('Where would you like to display it?','gpt3-ai-content-generator')?></strong></label>
                    </div>
                        <label class="wpaicg-form-label"><?php echo esc_html__('Page / Post ID','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_pages" name="bot[pages]" placeholder="<?php echo esc_html__('Example: 1,2,3','gpt3-ai-content-generator')?>">
                    </div>
                    <div class="wpaicg-mb-10 wpaicg_chatbot_position" style="display: none">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Position','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_position == 'left' ? ' checked': ''?> type="radio" value="left" name="bot[position]" class="wpaicg_chatbot_position_left"> <?php echo esc_html__('Bottom Left','gpt3-ai-content-generator')?>
                        <input<?php echo $wpaicg_chat_position == 'right' ? ' checked': ''?> type="radio" value="right" name="bot[position]" class="wpaicg_chatbot_position_right"> <?php echo esc_html__('Bottom Right','gpt3-ai-content-generator')?>
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                            <button type="button" class="button button-primary wpaicg-bot-step" data-type="language"><?php echo esc_html__('Next','gpt3-ai-content-generator')?></button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <!--Language-->
                <div class="wpaicg-bot-language wpaicg-bot-wizard" style="display: none">
                    <h3><?php echo esc_html__('Language, Tone and Profession','gpt3-ai-content-generator')?></h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Language','gpt3-ai-content-generator')?>:</label>
                        <select class="regular-text wpaicg_chatbot_language"  name="bot[language]">
                            <option value="en">English</option>
                            <option value="af">Afrikaans</option>
                            <option value="ar">Arabic</option>
                            <option value="bg">Bulgarian</option>
                            <option value="zh">Chinese</option>
                            <option value="hr">Croatian</option>
                            <option value="cs">Czech</option>
                            <option value="da">Danish</option>
                            <option value="nl">Dutch</option>
                            <option value="et">Estonian</option>
                            <option value="fil">Filipino</option>
                            <option value="fi">Finnish</option>
                            <option value="fr">French</option>
                            <option value="de">German</option>
                            <option value="el">Greek</option>
                            <option value="he">Hebrew</option>
                            <option value="hi">Hindi</option>
                            <option value="hu">Hungarian</option>
                            <option value="id">Indonesian</option>
                            <option value="it">Italian</option>
                            <option value="ja">Japanese</option>
                            <option value="ko">Korean</option>
                            <option value="lv">Latvian</option>
                            <option value="lt">Lithuanian</option>
                            <option value="ms">Malay</option>
                            <option value="no">Norwegian</option>
                            <option value="fa">Persian</option>
                            <option value="pl">Polish</option>
                            <option value="pt">Portuguese</option>
                            <option value="ro">Romanian</option>
                            <option value="ru">Russian</option>
                            <option value="sr">Serbian</option>
                            <option value="sk">Slovak</option>
                            <option value="sl">Slovenian</option>
                            <option value="sv">Swedish</option>
                            <option value="es">Spanish</option>
                            <option value="th">Thai</option>
                            <option value="tr">Turkish</option>
                            <option value="uk">Ukrainian</option>
                            <option value="vi">Vietnamese</option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Tone','gpt3-ai-content-generator')?>:</label>
                        <select class="regular-text wpaicg_chatbot_tone" name="bot[tone]">
                            <option value="friendly"><?php echo esc_html__('Friendly','gpt3-ai-content-generator')?></option>
                            <option value="professional"><?php echo esc_html__('Professional','gpt3-ai-content-generator')?></option>
                            <option value="sarcastic"><?php echo esc_html__('Sarcastic','gpt3-ai-content-generator')?></option>
                            <option value="humorous"><?php echo esc_html__('Humorous','gpt3-ai-content-generator')?></option>
                            <option value="cheerful"><?php echo esc_html__('Cheerful','gpt3-ai-content-generator')?></option>
                            <option value="anecdotal"><?php echo esc_html__('Anecdotal','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Act As:</label>
                        <select name="bot[proffesion]" class="regular-text wpaicg_chatbot_proffesion">
                            <option value="none"><?php echo esc_html__('None','gpt3-ai-content-generator')?></option>
                            <option value="accountant"><?php echo esc_html__('Accountant','gpt3-ai-content-generator')?></option>
                            <option value="advertisingspecialist"><?php echo esc_html__('Advertising Specialist','gpt3-ai-content-generator')?></option>
                            <option value="architect"><?php echo esc_html__('Architect','gpt3-ai-content-generator')?></option>
                            <option value="artist"><?php echo esc_html__('Artist','gpt3-ai-content-generator')?></option>
                            <option value="blogger"><?php echo esc_html__('Blogger','gpt3-ai-content-generator')?></option>
                            <option value="businessanalyst"><?php echo esc_html__('Business Analyst','gpt3-ai-content-generator')?></option>
                            <option value="businessowner"><?php echo esc_html__('Business Owner','gpt3-ai-content-generator')?></option>
                            <option value="carexpert"><?php echo esc_html__('Car Expert','gpt3-ai-content-generator')?></option>
                            <option value="consultant"><?php echo esc_html__('Consultant','gpt3-ai-content-generator')?></option>
                            <option value="counselor"><?php echo esc_html__('Counselor','gpt3-ai-content-generator')?></option>
                            <option value="cryptocurrencytrader"><?php echo esc_html__('Cryptocurrency Trader','gpt3-ai-content-generator')?></option>
                            <option value="cryptocurrencyexpert"><?php echo esc_html__('Cryptocurrency Expert','gpt3-ai-content-generator')?></option>
                            <option value="customersupport"><?php echo esc_html__('Customer Support','gpt3-ai-content-generator')?></option>
                            <option value="designer"><?php echo esc_html__('Designer','gpt3-ai-content-generator')?></option>
                            <option value="digitalmarketinagency"><?php echo esc_html__('Digital Marketing Agency','gpt3-ai-content-generator')?></option>
                            <option value="editor"><?php echo esc_html__('Editor','gpt3-ai-content-generator')?></option>
                            <option value="engineer"><?php echo esc_html__('Engineer','gpt3-ai-content-generator')?></option>
                            <option value="eventplanner"><?php echo esc_html__('Event Planner','gpt3-ai-content-generator')?></option>
                            <option value="freelancer"><?php echo esc_html__('Freelancer','gpt3-ai-content-generator')?></option>
                            <option value="insuranceagent"><?php echo esc_html__('Insurance Agent','gpt3-ai-content-generator')?></option>
                            <option value="insurancebroker"><?php echo esc_html__('Insurance Broker','gpt3-ai-content-generator')?></option>
                            <option value="interiordesigner"><?php echo esc_html__('Interior Designer','gpt3-ai-content-generator')?></option>
                            <option value="journalist"><?php echo esc_html__('Journalist','gpt3-ai-content-generator')?></option>
                            <option value="marketingagency"><?php echo esc_html__('Marketing Agency','gpt3-ai-content-generator')?></option>
                            <option value="marketingexpert"><?php echo esc_html__('Marketing Expert','gpt3-ai-content-generator')?></option>
                            <option value="marketingspecialist"><?php echo esc_html__('Marketing Specialist','gpt3-ai-content-generator')?></option>
                            <option value="photographer"><?php echo esc_html__('Photographer','gpt3-ai-content-generator')?></option>
                            <option value="programmer"><?php echo esc_html__('Programmer','gpt3-ai-content-generator')?></option>
                            <option value="publicrelationsagency"><?php echo esc_html__('Public Relations Agency','gpt3-ai-content-generator')?></option>
                            <option value="publisher"><?php echo esc_html__('Publisher','gpt3-ai-content-generator')?></option>
                            <option value="realestateagent"><?php echo esc_html__('Real Estate Agent','gpt3-ai-content-generator')?></option>
                            <option value="recruiter"><?php echo esc_html__('Recruiter','gpt3-ai-content-generator')?></option>
                            <option value="reporter"><?php echo esc_html__('Reporter','gpt3-ai-content-generator')?></option>
                            <option value="salesperson"><?php echo esc_html__('Sales Person','gpt3-ai-content-generator')?></option>
                            <option value="salerep"><?php echo esc_html__('Sales Representative','gpt3-ai-content-generator')?></option>
                            <option value="seoagency"><?php echo esc_html__('SEO Agency','gpt3-ai-content-generator')?></option>
                            <option value="seoexpert"><?php echo esc_html__('SEO Expert','gpt3-ai-content-generator')?></option>
                            <option value="socialmediaagency"><?php echo esc_html__('Social Media Agency','gpt3-ai-content-generator')?></option>
                            <option value="student"><?php echo esc_html__('Student','gpt3-ai-content-generator')?></option>
                            <option value="teacher"><?php echo esc_html__('Teacher','gpt3-ai-content-generator')?></option>
                            <option value="technicalsupport"><?php echo esc_html__('Technical Support','gpt3-ai-content-generator')?></option>
                            <option value="trainer"><?php echo esc_html__('Trainer','gpt3-ai-content-generator')?></option>
                            <option value="travelagency"><?php echo esc_html__('Travel Agency','gpt3-ai-content-generator')?></option>
                            <option value="videographer"><?php echo esc_html__('Videographer','gpt3-ai-content-generator')?></option>
                            <option value="webdesignagency"><?php echo esc_html__('Web Design Agency','gpt3-ai-content-generator')?></option>
                            <option value="webdesignexpert"><?php echo esc_html__('Web Design Expert','gpt3-ai-content-generator')?></option>
                            <option value="webdevelopmentagency"><?php echo esc_html__('Web Development Agency','gpt3-ai-content-generator')?></option>
                            <option value="webdevelopmentexpert"><?php echo esc_html__('Web Development Expert','gpt3-ai-content-generator')?></option>
                            <option value="webdesigner"><?php echo esc_html__('Web Designer','gpt3-ai-content-generator')?></option>
                            <option value="webdeveloper"><?php echo esc_html__('Web Developer','gpt3-ai-content-generator')?></option>
                            <option value="writer"><?php echo esc_html__('Writer','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                            <button type="button" class="button wpaicg-bot-step" data-type="type"><?php echo esc_html__('Previous','gpt3-ai-content-generator')?></button>
                            <button type="button" class="button button-primary wpaicg-bot-step" data-type="style"><?php echo esc_html__('Next','gpt3-ai-content-generator')?></button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <!--Style-->
                <div class="wpaicg-bot-style wpaicg-bot-wizard" style="display: none">
                    <h3><?php echo esc_html__('Style','gpt3-ai-content-generator')?></h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Font Size','gpt3-ai-content-generator')?>:</label>
                        <select name="bot[fontsize]" class="wpaicg_chatbot_fontsize">
                            <?php
                            for($i = 10; $i <= 30; $i++){
                                echo '<option'.($wpaicg_chat_fontsize == $i ? ' selected' :'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10" style="position: relative">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Font Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_fontcolor)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_fontcolor" name="bot[fontcolor]">
                    </div>
                    <div class="wpaicg-mb-10" style="position: relative">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Background Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_bgcolor)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_bgcolor" name="bot[bgcolor]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Border Radius - Window','gpt3-ai-content-generator')?>:</label>
                        <input style="width: 80px" value="20" type="number" min="0" class="wpaicg_chatbot_chat_rounded" name="bot[chat_rounded]">px
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Text Field Height','gpt3-ai-content-generator')?>:</label>
                        <input style="width: 80px" value="60" type="number" min="30" class="wpaicg_chatbot_text_height" name="bot[text_height]">px
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Border Radius - Text Field','gpt3-ai-content-generator')?>:</label>
                        <input style="width: 80px" value="20" type="number" min="0" class="wpaicg_chatbot_text_rounded" name="bot[text_rounded]">px
                    </div>
                    <div class="wpaicg-mb-10" style="position: relative">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Text Field Background','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_bg_text_field)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_bg_text_field" name="bot[bg_text_field]">
                    </div>
                    <div class="wpaicg-mb-10" style="position: relative">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Text Field Border','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_border_text_field)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_border_text_field" name="bot[border_text_field]">
                    </div>
                    <div class="wpaicg-mb-10" style="position: relative">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Button Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_send_color)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_send_color" name="bot[send_color]">
                    </div>
                    <div class="wpaicg-mb-10" style="position: relative">
                        <label class="wpaicg-form-label"><?php echo esc_html__('User Background Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_user_bg_color)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_user_bg_color" name="bot[user_bg_color]">
                    </div>
                    <div class="wpaicg-mb-10" style="position: relative">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Background Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_ai_bg_color)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_ai_bg_color" name="bot[ai_bg_color]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Width','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_width)?>" style="width: 100px;" class="wpaicg_chatbot_width" min="100" type="text" name="bot[width]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Height','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_height)?>" style="width: 100px;" class="wpaicg_chatbot_height" min="100" type="text" name="bot[height]">
                    </div>
                    <div class="wpaicg-widget-icon" style="display: none">
                        <div class="wpaicg-mb-10">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Icon (75x75)','gpt3-ai-content-generator')?>:</label>
                            <div style="display: inline-flex; align-items: center">
                                <input checked class="wpaicg_chatbox_icon_default wpaicg_chatbot_icon_default" type="radio" value="default" name="bot[icon]">
                                <div style="text-align: center">
                                    <img style="display: block;width: 40px; height: 40px" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>"<br>
                                    <strong><?php echo esc_html__('Default','gpt3-ai-content-generator')?></strong>
                                </div>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="wpaicg_chatbox_icon_custom wpaicg_chatbot_icon_custom" value="custom" name="bot[icon]">
                                <div style="text-align: center">
                                    <div class="wpaicg_chatbox_icon">
                                        <svg width="40px" height="40px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M246.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 109.3V320c0 17.7 14.3 32 32 32s32-14.3 32-32V109.3l73.4 73.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-128-128zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 53 43 96 96 96H352c53 0 96-43 96-96V352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V352z"/></svg><br>
                                    </div>
                                    <strong><?php echo esc_html__('Custom','gpt3-ai-content-generator')?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Use Avatars','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_use_avatar ? ' checked':''?> value="1" type="checkbox" class="wpaicg_chatbot_use_avatar" name="bot[use_avatar]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Avatar (40x40)','gpt3-ai-content-generator')?>:</label>
                        <div style="display: inline-flex; align-items: center">
                            <input checked class="wpaicg_chatbox_avatar_default wpaicg_chatbot_ai_avatar_default" type="radio" value="default" name="bot[ai_avatar]">
                            <div style="text-align: center">
                                <img style="display: block;width: 40px; height: 40px" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>"<br>
                                <strong><?php echo esc_html__('Default','gpt3-ai-content-generator')?></strong>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="wpaicg_chatbox_avatar_custom wpaicg_chatbot_ai_avatar_custom" value="custom" name="bot[ai_avatar]">
                            <div style="text-align: center">
                                <div class="wpaicg_chatbox_avatar">
                                    <svg width="40px" height="40px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M246.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 109.3V320c0 17.7 14.3 32 32 32s32-14.3 32-32V109.3l73.4 73.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-128-128zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 53 43 96 96 96H352c53 0 96-43 96-96V352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V352z"/></svg><br>
                                </div>
                                <strong><?php echo esc_html__('Custom','gpt3-ai-content-generator');?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Fullscreen Button','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_fullscreen ? ' checked':''?> value="1" type="checkbox" class="wpaicg_chatbot_fullscreen" name="bot[fullscreen]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Close Button','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_close_btn ? ' checked':''?> value="1" type="checkbox" class="wpaicg_chatbot_close_btn" name="bot[close_btn]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Download Button','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_chat_download_btn ? ' checked':''?> value="1" type="checkbox" class="wpaicg_chatbot_download_btn" name="bot[download_btn]">
                    </div>
                    <div class="mb-5" style="position: relative">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Bar Icons Color','gpt3-ai-content-generator')?>:</label>
                        <input value="#fff" type="text" class="wpaicgchat_color wpaicg_chatbot_bar_color" name="bot[bar_color]">
                    </div>
                    <div class="mb-5" style="position: relative">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Thinking Text Color','gpt3-ai-content-generator')?>:</label>
                        <input value="#fff" type="text" class="wpaicgchat_color wpaicg_chatbot_thinking_color" name="bot[thinking_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Delay time','gpt3-ai-content-generator')?>:</label>
                        <input placeholder="<?php echo esc_html__('in seconds. eg. 5','gpt3-ai-content-generator')?>" value="" type="text" class="wpaicg_chatbot_delay_time" name="bot[delay_time]">
                    </div>
                    <?php
                    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                        ?>
                        <div class="mb-5" style="position: relative">
                            <label class="wpaicg-form-label"><?php echo esc_html__('PDF Icon Color','gpt3-ai-content-generator')?>:</label>
                            <input value="#222" type="text" class="wpaicgchat_color wpaicg_chatbot_pdf_color" name="bot[pdf_color]">
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
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="language"><?php echo esc_html__('Previous','gpt3-ai-content-generator');?></button>
                        <button type="button" class="button button-primary wpaicg-bot-step" data-type="parameters"><?php echo esc_html__('Next','gpt3-ai-content-generator');?></button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit"><?php echo esc_html__('Save','gpt3-ai-content-generator');?></button>
                    </div>
                </div>
                <!--Parameters-->
                <div class="wpaicg-bot-parameters wpaicg-bot-wizard" style="display: none">
                    <h3><?php echo esc_html__('Parameters','gpt3-ai-content-generator');?></h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label" for="wpaicg_chat_model"><?php echo esc_html__('Model','gpt3-ai-content-generator');?>:</label>
                        <select class="regular-text wpaicg_chatbot_model" id="wpaicg_chat_model"  name="bot[model]" >
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
                                echo '<option value="'.esc_html($wpaicg_custom_model).'">'.esc_html($wpaicg_custom_model).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Temperature','gpt3-ai-content-generator');?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_temperature" id="label_temperature" name="bot[temperature]" value="<?php
                        echo  esc_html( $wpaicg_chat_temperature ) ;
                        ?>">
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Max Tokens','gpt3-ai-content-generator');?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_max_tokens" id="label_max_tokens" name="bot[max_tokens]" value="<?php
                        echo  esc_html( $wpaicg_chat_max_tokens ) ;
                        ?>" >
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Top P','gpt3-ai-content-generator');?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_top_p" id="label_top_p" name="bot[top_p]" value="<?php
                        echo  esc_html( $wpaicg_chat_top_p ) ;
                        ?>" >
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Best Of','gpt3-ai-content-generator');?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_best_of" id="label_best_of" name="bot[best_of]" value="<?php
                        echo  esc_html( $wpaicg_chat_best_of ) ;
                        ?>" >
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Frequency Penalty','gpt3-ai-content-generator');?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_frequency_penalty" id="label_frequency_penalty" name="bot[frequency_penalty]" value="<?php
                        echo  esc_html( $wpaicg_chat_frequency_penalty ) ;
                        ?>" >
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Presence Penalty','gpt3-ai-content-generator');?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_presence_penalty" id="label_presence_penalty" name="bot[presence_penalty]" value="<?php
                        echo  esc_html( $wpaicg_chat_presence_penalty ) ;
                        ?>" >
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                            <button type="button" class="button wpaicg-bot-step" data-type="style"><?php echo esc_html__('Previous','gpt3-ai-content-generator')?></button>
                            <button type="button" class="button button-primary wpaicg-bot-step" data-type="<?php echo \WPAICG\wpaicg_util_core()->wpaicg_is_pro() ? 'moderation' : 'audio'?>"><?php echo esc_html__('Next','gpt3-ai-content-generator')?></button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <?php
                if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                    ?>
                    <div class="wpaicg-bot-moderation wpaicg-bot-wizard" style="display: none">
                        <h3>Moderation</h3>
                        <div class="wpaicg-mb-10">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Enable','gpt3-ai-content-generator')?>:</label>
                            <input name="bot[moderation]" value="1" type="checkbox" class="wpaicg_chatbot_moderation">
                        </div>
                        <div class="wpaicg-mb-10">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Model','gpt3-ai-content-generator')?>:</label>
                            <select class="regular-text wpaicg_chatbot_moderation_model"  name="bot[moderation_model]" >
                                <option value="text-moderation-latest">text-moderation-latest</option>
                                <option value="text-moderation-stable">text-moderation-stable</option>
                            </select>
                        </div>
                        <div class="wpaicg-mb-10">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Notice','gpt3-ai-content-generator')?>:</label>
                            <textarea class="wpaicg_chatbot_moderation_notice" rows="8" name="bot[moderation_notice]"><?php echo esc_html__('Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.','gpt3-ai-content-generator')?></textarea>
                        </div>
                        <div class="wpaicg-bot-footer">
                            <div>
                            <button type="button" class="button wpaicg-bot-step" data-type="parameters"><?php echo esc_html__('Previous','gpt3-ai-content-generator')?></button>
                            <button type="button" class="button button-primary wpaicg-bot-step" data-type="audio"><?php echo esc_html__('Next','gpt3-ai-content-generator')?></button>
                            </div>
                            <button class="button button-primary wpaicg-chatbot-submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                        </div>
                    </div>
                <?php
                endif;
                ?>
                <div class="wpaicg-bot-audio wpaicg-bot-wizard" style="display: none">
                    <h3><?php echo esc_html__('VoiceChat','gpt3-ai-content-generator')?></h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Enable Speech to Text','gpt3-ai-content-generator')?>:</label>
                        <input value="1" type="checkbox" class="wpaicg_chatbot_audio_enable" name="bot[audio_enable]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Mic Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_mic_color)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_mic_color" name="bot[mic_color]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Stop Color','gpt3-ai-content-generator')?>:</label>
                        <input value="<?php echo esc_html($wpaicg_stop_color)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_stop_color" name="bot[stop_color]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Enable Text to Speech','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo empty($wpaicg_elevenlabs_api) && empty($wpaicg_google_api_key) ? ' disabled':''?> class="wpaicg_chatbot_chat_to_speech" value="1" type="checkbox" name="bot[chat_to_speech]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Provider','gpt3-ai-content-generator')?>:</label>
                        <select disabled name="bot[voice_service]" class="wpaicg_chatbot_voice_service">
                            <option value=""><?php echo esc_html__('ElevenLabs','gpt3-ai-content-generator')?></option>
                            <option value="google"><?php echo esc_html__('Google','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="wpaicg_voice_service_google" style="display:none">
                    <?php
                        $wpaicg_voice_language = 'en-US';
                        $wpaicg_voice_name = 'en-US-Studio-M';
                        $wpaicg_voice_device = '';
                        $wpaicg_voice_speed = 1;
                        $wpaicg_voice_pitch = 0;
                        $wpaicg_google_api_key = get_option('wpaicg_google_api_key', '');
                    ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Language','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo empty($wpaicg_google_api_key) ? ' disabled':''?> name="bot[voice_language]" class="wpaicg_voice_language wpaicg_chatbot_voice_language">
                                <?php
                                foreach(\WPAICG\WPAICG_Google_Speech::get_instance()->languages as $key=>$voice_language){
                                    echo '<option'.($wpaicg_voice_language == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($voice_language).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Name','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo empty($wpaicg_google_api_key) ? ' disabled':''?> data-value="<?php echo esc_html($wpaicg_voice_name)?>" name="bot[voice_name]" class="wpaicg_voice_name wpaicg_chatbot_voice_name">
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Audio Device Profile','gpt3-ai-content-generator')?>:</label>
                            <select<?php echo empty($wpaicg_google_api_key) ? ' disabled':''?> name="bot[voice_device]" class="wpaicg_chatbot_voice_device">
                                <?php
                                foreach(\WPAICG\WPAICG_Google_Speech::get_instance()->devices() as $key => $device){
                                    echo '<option'.($wpaicg_voice_device == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($device).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Speed','gpt3-ai-content-generator')?>:</label>
                            <input<?php echo empty($wpaicg_google_api_key) ? ' disabled':''?> type="text" class="wpaicg_voice_speed wpaicg_chatbot_voice_speed" value="<?php echo esc_html($wpaicg_voice_speed)?>" name="bot[voice_speed]">
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Voice Pitch','gpt3-ai-content-generator')?>:</label>
                            <input<?php echo empty($wpaicg_google_api_key) ? ' disabled':''?> type="text" class="wpaicg_voice_pitch wpaicg_chatbot_voice_pitch" value="<?php echo esc_html($wpaicg_voice_pitch)?>" name="bot[voice_pitch]">
                        </div>
                    </div>
                    <div class="wpaicg_voice_service_elevenlabs">
                        <div class="wpaicg-mb-10">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Select a Voice','gpt3-ai-content-generator')?>:</label>
                            <select disabled name="bot[elevenlabs_voice]" class="wpaicg_chatbot_elevenlabs_voice">
                                <?php
                                foreach(\WPAICG\WPAICG_ElevenLabs::get_instance()->voices as $key=>$voice){
                                    echo '<option value="'.esc_html($key).'">'.esc_html($voice).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="parameters"><?php echo esc_html__('Previous','gpt3-ai-content-generator')?></button>
                        <button type="button" class="button button-primary wpaicg-bot-step" data-type="custom"><?php echo esc_html__('Next','gpt3-ai-content-generator')?></button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <div class="wpaicg-bot-custom wpaicg-bot-wizard" style="display: none">
                    <h3><?php echo esc_html__('Custom Text','gpt3-ai-content-generator')?></h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Name','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_ai_name" name="bot[ai_name]" value="AI" >
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('You','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_you" name="bot[you]" value="<?php echo esc_html__('You','gpt3-ai-content-generator')?>" >
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('AI Thinking','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_ai_thinking" name="bot[ai_thinking]" value="<?php echo esc_html__('AI thinking','gpt3-ai-content-generator')?>" >
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Placeholder','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_placeholder" name="bot[placeholder]" value="<?php echo esc_html__('Type message..','gpt3-ai-content-generator')?>" >
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Welcome Message','gpt3-ai-content-generator')?>:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_welcome" name="bot[welcome]" value="<?php echo esc_html__('Hello human, I am a GPT powered AI chat bot. Ask me anything!','gpt3-ai-content-generator')?>" >
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('No Answer Message','gpt3-ai-content-generator')?>:</label>
                        <input class="regular-text wpaicg_chatbot_no_answer" type="text" value="" name="bot[no_answer]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Footer Note','gpt3-ai-content-generator')?>:</label>
                        <input class="regular-text wpaicg_chatbot_footer_text" value="" type="text" name="bot[footer_text]" placeholder="<?php echo esc_html__('Powered by ...','gpt3-ai-content-generator')?>">
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="audio"><?php echo esc_html__('Previous','gpt3-ai-content-generator')?></button>
                        <button type="button" class="button button-primary wpaicg-bot-step" data-type="context"><?php echo esc_html__('Next','gpt3-ai-content-generator')?></button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <div class="wpaicg-bot-context wpaicg-bot-wizard" style="display: none">
                    <h3><?php echo esc_html__('Context','gpt3-ai-content-generator')?></h3>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Additional Context?','gpt3-ai-content-generator')?>:</label>
                        <input name="bot[chat_addition]" class="wpaicg_chatbot_chat_addition" value="1" type="checkbox" id="wpaicg_chat_addition">
                    </div>
                    <?php
                    $wpaicg_additions_json = file_get_contents(WPAICG_PLUGIN_DIR.'admin/chat/context.json');
                    $wpaicg_additions = json_decode($wpaicg_additions_json, true);
                    ?>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Template','gpt3-ai-content-generator')?>:</label>
                        <select disabled class="wpaicg_chat_addition_template">
                            <option value=""><?php echo esc_html__('Select Template','gpt3-ai-content-generator')?></option>
                            <?php
                            foreach($wpaicg_additions as $key=>$wpaicg_addition){
                                echo '<option value="'.esc_html($wpaicg_addition).'">'.esc_html($key).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label" style="vertical-align: top;"><?php echo esc_html__('Context','gpt3-ai-content-generator')?>:
                            <small style="font-weight: normal;display: block"><?php echo sprintf(esc_html__('You can add shortcode %s and %s and %s and %s in context','gpt3-ai-content-generator'),'<code>[sitename]</code>','<code>[siteurl]</code>','<code>[domain]</code>','<code>[date]</code>')?></small>
                        </label>
                        <textarea rows="8" disabled name="bot[chat_addition_text]" id="wpaicg_chat_addition_text" class="regular-text wpaicg_chatbot_chat_addition_text"><?php echo esc_html__('You are a helpful AI Assistant. Please be friendly.','gpt3-ai-content-generator')?></textarea>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Remember Conversation','gpt3-ai-content-generator')?>:</label>
                        <select name="bot[remember_conversation]" class="wpaicg_chatbot_remember_conversation">
                            <option value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                            <option value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Remember Conv. Up To','gpt3-ai-content-generator')?>:</label>
                        <select name="bot[conversation_cut]" class="wpaicg_chatbot_conversation_cut">
                            <?php
                            for($i=3;$i<=20;$i++){
                                echo '<option'.(10 == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('User Aware','gpt3-ai-content-generator')?>:</label>
                        <select name="bot[user_aware]" class="wpaicg_chatbot_user_aware">
                            <option value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                            <option value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Content Aware','gpt3-ai-content-generator')?>:</label>
                        <select name="bot[content_aware]" class="wpaicg_chatbot_content_aware">
                            <option value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                            <option value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Use Excerpt','gpt3-ai-content-generator')?>:</label>
                        <input checked type="checkbox" id="wpaicg_chat_excerpt" class="wpaicg_chatbot_chat_excerpt">
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Use Embeddings','gpt3-ai-content-generator')?>:</label>
                        <input type="checkbox" value="1" name="bot[embedding]" id="wpaicg_chat_embedding" class="asdisabled wpaicg_chatbot_embedding">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Pinecone Index','gpt3-ai-content-generator')?>:</label>
                        <select disabled name="bot[embedding_index]" id="wpaicg_chat_embedding_index" class="asdisabled wpaicg_chatbot_embedding_index">
                            <option value=""><?php echo esc_html__('Default','gpt3-ai-content-generator')?></option>
                            <?php
                            foreach($wpaicg_pinecone_indexes as $wpaicg_pinecone_index){
                                echo '<option value="'.esc_html($wpaicg_pinecone_index['url']).'">'.esc_html($wpaicg_pinecone_index['name']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Method','gpt3-ai-content-generator')?>:</label>
                        <select disabled name="bot[embedding_type]" id="wpaicg_chat_embedding_type" class="asdisabled wpaicg_chatbot_embedding_type">
                            <option value="openai"><?php echo esc_html__('Embeddings + Completion','gpt3-ai-content-generator')?></option>
                            <option value=""><?php echo esc_html__('Embeddings only','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Nearest Answers Up To','gpt3-ai-content-generator')?>:</label>
                        <select disabled name="bot[embedding_top]" id="wpaicg_chat_embedding_top" class="asdisabled wpaicg_chatbot_embedding_top">
                            <?php
                            for($i = 1; $i <=5;$i++){
                                echo '<option value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <?php
                    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Enable PDF Upload','gpt3-ai-content-generator')?>:</label>
                            <input disabled type="checkbox" value="1" name="bot[embedding_pdf]" class="asdisabled wpaicg_chatbot_embedding_pdf">
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label"><?php echo esc_html__('Limit PDF Pages','gpt3-ai-content-generator')?>:</label>
                            <select disabled name="bot[pdf_pages]" id="wpaicg_chat_pdf_pages" class="asdisabled wpaicg_chatbot_pdf_pages" style="width: 65px!important;">
                                <?php
                                $pdf_pages = 120;
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
                            <textarea disabled rows="8" name="bot[embedding_pdf_message]" class="asdisabled wpaicg_chatbot_embedding_pdf_message">Congrats! Your PDF is uploaded now! You can ask questions about your document.\nExample Questions:[questions]</textarea>
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
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="custom"><?php echo esc_html__('Previous','gpt3-ai-content-generator')?></button>
                        <button type="button" class="button button-primary wpaicg-bot-step" data-type="logs"><?php echo esc_html__('Next','gpt3-ai-content-generator')?></button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <div class="wpaicg-bot-logs wpaicg-bot-wizard" style="display: none">
                    <h3><?php echo esc_html__('Logs','gpt3-ai-content-generator')?></h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Save Chat Logs','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_save_logs ? ' checked': ''?> class="wpaicg_chatbot_save_logs" value="1" type="checkbox" name="bot[save_logs]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Save Prompt','gpt3-ai-content-generator')?>:</label>
                        <input disabled class="wpaicg_chatbot_log_request" value="1" type="checkbox" name="bot[log_request]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Display Notice','gpt3-ai-content-generator')?>:</label>
                        <input disabled <?php echo $wpaicg_log_notice ? ' checked': ''?> class="wpaicg_chatbot_log_notice" value="1" type="checkbox" name="bot[log_notice]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Notice Text','gpt3-ai-content-generator')?>:</label>
                        <textarea disabled class="wpaicg_chatbot_log_notice_message" name="bot[log_notice_message]"><?php echo esc_html($wpaicg_log_notice_message)?></textarea>
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="context"><?php echo esc_html__('Previous','gpt3-ai-content-generator')?></button>
                        <button type="button" class="button button-primary wpaicg-bot-step" data-type="tokens"><?php echo esc_html__('Next','gpt3-ai-content-generator')?></button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <div class="wpaicg-bot-tokens wpaicg-bot-wizard" style="display: none">
                    <h3><?php echo esc_html__('Token Handling','gpt3-ai-content-generator')?></h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Limit Registered User','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_user_limited ? ' checked': ''?> type="checkbox" value="1" class="wpaicg_user_token_limit wpaicg_chatbot_user_limited" name="bot[user_limited]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Token Limit','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_user_limited ? '' : ' disabled'?> style="width: 80px" class="wpaicg_user_token_limit_text wpaicg_chatbot_user_tokens" type="text" value="<?php echo esc_html($wpaicg_user_tokens)?>" name="bot[user_tokens]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Role based limit','gpt3-ai-content-generator')?>:</label>
                        <?php
                        foreach($wpaicg_roles as $key=>$wpaicg_role){
                            echo '<input class="wpaicg_role_'.esc_html($key).'" type="hidden" name="bot[limited_roles]['.esc_html($key).']">';
                        }
                        ?>
                        <input type="checkbox" value="1" class="wpaicg_role_limited" name="bot[role_limited]">
                        <a href="javascript:void(0)" class="wpaicg_limit_set_role<?php echo $wpaicg_user_limited ? ' ': ' disabled'?>"><?php echo esc_html__('Set Limit','gpt3-ai-content-generator')?></a>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Limit Non-Registered User','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_guest_limited ? ' checked': ''?> type="checkbox" class="wpaicg_guest_token_limit wpaicg_chatbot_guest_limited" value="1" name="bot[guest_limited]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Token Limit','gpt3-ai-content-generator')?>:</label>
                        <input<?php echo $wpaicg_guest_limited ? '' : ' disabled'?> class="wpaicg_guest_token_limit_text wpaicg_chatbot_guest_tokens" style="width: 80px" type="text" value="<?php echo esc_html($wpaicg_guest_tokens)?>" name="bot[guest_tokens]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Notice','gpt3-ai-content-generator')?>:</label>
                        <input type="text" value="<?php echo esc_html($wpaicg_limited_message)?>" name="bot[limited_message]" class="wpaicg_chatbot_limited_message">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label"><?php echo esc_html__('Reset Limit','gpt3-ai-content-generator')?>:</label>
                        <select name="bot[reset_limit]" class="wpaicg_chatbot_reset_limit">
                            <option value="0"><?php echo esc_html__('Never','gpt3-ai-content-generator')?></option>
                            <option value="1"><?php echo esc_html__('1 Day','gpt3-ai-content-generator')?></option>
                            <option value="3"><?php echo esc_html__('3 Days','gpt3-ai-content-generator')?></option>
                            <option value="7"><?php echo esc_html__('1 Week','gpt3-ai-content-generator')?></option>
                            <option value="14"><?php echo esc_html__('2 Weeks','gpt3-ai-content-generator')?></option>
                            <option value="30"><?php echo esc_html__('1 Month','gpt3-ai-content-generator')?></option>
                            <option value="60"><?php echo esc_html__('2 Months','gpt3-ai-content-generator')?></option>
                            <option value="90"><?php echo esc_html__('3 Months','gpt3-ai-content-generator')?></option>
                            <option value="180"><?php echo esc_html__('6 Months','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="logs"><?php echo esc_html__('Previous','gpt3-ai-content-generator')?></button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
            </form>
        </div>
        <div class="wpaicg-grid-3">
            <div class="wpaicg-bot-preview">
                <div class="wpaicg-chat-shortcode"
                     data-user-bg-color="<?php echo esc_html($wpaicg_user_bg_color)?>"
                     data-color="<?php echo esc_html($wpaicg_chat_fontcolor)?>"
                     data-fontsize="<?php echo esc_html($wpaicg_chat_fontsize)?>"
                     data-use-avatar="<?php echo $wpaicg_use_avatar ? '1' : '0'?>"
                     data-user-avatar="<?php echo get_avatar_url('')?>"
                     data-you="You"
                     data-ai-avatar="<?php echo $wpaicg_use_avatar && !empty($wpaicg_ai_avatar_id) ? wp_get_attachment_url(esc_html($wpaicg_ai_avatar_id)) : WPAICG_PLUGIN_URL.'admin/images/chatbot.png'?>"
                     data-ai-name="AI"
                     data-ai-bg-color="<?php echo esc_html($wpaicg_ai_bg_color)?>"
                     data-nonce="<?php echo esc_html(wp_create_nonce( 'wpaicg-chatbox' ))?>"
                     data-post-id="<?php echo get_the_ID()?>"
                     data-url="<?php echo home_url( $wp->request )?>"
                     data-width="350"
                     data-height="400"
                     data-footer="0"
                     data-text_height="60"
                     data-text_rounded="20"
                     data-chat_rounded="20"
                     style="width: <?php echo esc_html($wpaicg_chat_width)?>px;"
                     data-voice_service=""
                     data-voice_language=""
                     data-voice_name=""
                     data-voice_device=""
                     data-voice_speed=""
                     data-voice_pitch=""
                     data-type="shortcode"
                     >
                    <div class="wpaicg-chat-shortcode-content" style="background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>;">
                        <ul class="wpaicg-chat-shortcode-messages" style="height: <?php echo esc_html($wpaicg_chat_height) - 44?>px;">
                            <li style="background: rgb(0 0 0 / 32%); padding: 10px;margin-bottom: 0;display:none" class="wpaicg_chatbot_log_preview">
                                <p><span class="wpaicg-chat-message"></span></p>
                            </li>
                            <li class="wpaicg-ai-message" style="color: <?php echo esc_html($wpaicg_chat_fontcolor)?>; font-size: <?php echo esc_html($wpaicg_chat_fontsize)?>px; background-color: <?php echo esc_html($wpaicg_ai_bg_color);?>">
                                <p>
                                    <strong style="float: left" class="wpaicg-chat-avatar"><?php echo esc_html__('AI','gpt3-ai-content-generator')?>: </strong>
                                    <span class="wpaicg-chat-message wpaicg_chatbot_welcome_message"><?php echo esc_html__('Hello human, I am a GPT powered AI chat bot. Ask me anything!','gpt3-ai-content-generator')?></span>
                                </p>
                            </li>
                        </ul>
                        <span class="wpaicg-bot-thinking" style="display: none;background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>;color:<?php echo esc_html($wpaicg_chat_fontcolor)?>"><span class="wpaicg_chatbot_ai_thinking_view"><?php echo esc_html__('AI thinking','gpt3-ai-content-generator')?></span>&nbsp;<span class="wpaicg-jumping-dots"><span class="wpaicg-dot-1">.</span><span class="wpaicg-dot-2">.</span><span class="wpaicg-dot-3">.</span></span></span>
                    </div>
                    <div class="wpaicg-chat-shortcode-type" style="background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>;">
                        <textarea style="border-color: <?php echo esc_html($wpaicg_border_text_field)?>;background-color: <?php echo esc_html($wpaicg_bg_text_field)?>" type="text" class="wpaicg-chat-shortcode-typing" placeholder="<?php echo esc_html__('Type message..','gpt3-ai-content-generator')?>"></textarea>
                        <div class="wpaicg_chat_additions">
                            <span class="wpaicg-mic-icon" data-type="shortcode" style="<?php echo $wpaicg_audio_enable ? '' : 'display:none'?>;color: <?php echo esc_html($wpaicg_mic_color)?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M176 0C123 0 80 43 80 96V256c0 53 43 96 96 96s96-43 96-96V96c0-53-43-96-96-96zM48 216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 89.1 66.2 162.7 152 174.4V464H104c-13.3 0-24 10.7-24 24s10.7 24 24 24h72 72c13.3 0 24-10.7 24-24s-10.7-24-24-24H200V430.4c85.8-11.7 152-85.3 152-174.4V216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 70.7-57.3 128-128 128s-128-57.3-128-128V216z"/></svg>
                            </span>
                            <span class="wpaicg-pdf-icon" data-type="shortcode" style="display:none">
                                <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"  xml:space="preserve"><path class="st0" d="M378.413,0H208.297h-13.182L185.8,9.314L57.02,138.102l-9.314,9.314v13.176v265.514 c0,47.36,38.528,85.895,85.896,85.895h244.811c47.353,0,85.881-38.535,85.881-85.895V85.896C464.294,38.528,425.766,0,378.413,0z M432.497,426.105c0,29.877-24.214,54.091-54.084,54.091H133.602c-29.884,0-54.098-24.214-54.098-54.091V160.591h83.716 c24.885,0,45.077-20.178,45.077-45.07V31.804h170.116c29.87,0,54.084,24.214,54.084,54.092V426.105z"/><path class="st0" d="M171.947,252.785h-28.529c-5.432,0-8.686,3.533-8.686,8.825v73.754c0,6.388,4.204,10.599,10.041,10.599 c5.711,0,9.914-4.21,9.914-10.599v-22.406c0-0.545,0.279-0.817,0.824-0.817h16.436c20.095,0,32.188-12.226,32.188-29.612 C204.136,264.871,192.182,252.785,171.947,252.785z M170.719,294.888h-15.208c-0.545,0-0.824-0.272-0.824-0.81v-23.23 c0-0.545,0.279-0.816,0.824-0.816h15.208c8.42,0,13.447,5.027,13.447,12.498C184.167,290,179.139,294.888,170.719,294.888z"/><path class="st0" d="M250.191,252.785h-21.868c-5.432,0-8.686,3.533-8.686,8.825v74.843c0,5.3,3.253,8.693,8.686,8.693h21.868 c19.69,0,31.923-6.249,36.81-21.324c1.76-5.3,2.723-11.681,2.723-24.857c0-13.175-0.964-19.557-2.723-24.856 C282.113,259.034,269.881,252.785,250.191,252.785z M267.856,316.896c-2.318,7.331-8.965,10.459-18.21,10.459h-9.23 c-0.545,0-0.824-0.272-0.824-0.816v-55.146c0-0.545,0.279-0.817,0.824-0.817h9.23c9.245,0,15.892,3.128,18.21,10.46 c0.95,3.128,1.62,8.56,1.62,17.93C269.476,308.336,268.805,313.768,267.856,316.896z"/><path class="st0" d="M361.167,252.785h-44.812c-5.432,0-8.7,3.533-8.7,8.825v73.754c0,6.388,4.218,10.599,10.055,10.599 c5.697,0,9.914-4.21,9.914-10.599v-26.351c0-0.538,0.265-0.81,0.81-0.81h26.086c5.837,0,9.23-3.532,9.23-8.56 c0-5.028-3.393-8.553-9.23-8.553h-26.086c-0.545,0-0.81-0.272-0.81-0.817v-19.425c0-0.545,0.265-0.816,0.81-0.816h32.733 c5.572,0,9.245-3.666,9.245-8.553C370.411,256.45,366.738,252.785,361.167,252.785z"/></svg>
                            </span>
                            <span class="wpaicg-pdf-loading" style="display: none"></span>
                            <span data-type="shortcode" alt="<?php echo esc_html__('Clear','gpt3-ai-content-generator')?>" title="<?php echo esc_html__('Clear','gpt3-ai-content-generator')?>" class="wpaicg-pdf-remove" style="display: none">&times;</span>
                            <input type="file" accept="application/pdf" class="wpaicg-pdf-file" style="display: none">
                        </div>
                        <span class="wpaicg-chat-shortcode-send" style="color:<?php echo esc_html($wpaicg_send_color)?>">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5004 11.9998H5.00043M4.91577 12.2913L2.58085 19.266C2.39742 19.8139 2.3057 20.0879 2.37152 20.2566C2.42868 20.4031 2.55144 20.5142 2.70292 20.5565C2.87736 20.6052 3.14083 20.4866 3.66776 20.2495L20.3792 12.7293C20.8936 12.4979 21.1507 12.3822 21.2302 12.2214C21.2993 12.0817 21.2993 11.9179 21.2302 11.7782C21.1507 11.6174 20.8936 11.5017 20.3792 11.2703L3.66193 3.74751C3.13659 3.51111 2.87392 3.39291 2.69966 3.4414C2.54832 3.48351 2.42556 3.59429 2.36821 3.74054C2.30216 3.90893 2.3929 4.18231 2.57437 4.72906L4.91642 11.7853C4.94759 11.8792 4.96317 11.9262 4.96933 11.9742C4.97479 12.0168 4.97473 12.0599 4.96916 12.1025C4.96289 12.1506 4.94718 12.1975 4.91577 12.2913Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </div>
                    <div style="<?php echo $wpaicg_include_footer ? '' :' display:none'?>;background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>" class="wpaicg-chat-shortcode-footer"></div>
                </div>
                <div class="wpaicg-chatbot-widget-icon" style="display: none">
                    <img src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>" height="75" width="75">
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if(isset($_GET['update_success']) && !empty($_GET['update_success'])){
    ?>
    <p style="color: #26a300; font-weight: bold;"><?php echo esc_html__('Congratulations! Your chatbot has been saved successfully!','gpt3-ai-content-generator')?></p>
    <?php
}
?>
<?php
$wpaicg_bot_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$args = array(
    'post_type' => 'wpaicg_chatbot',
    'posts_per_page' => 40,
    'paged' => $wpaicg_bot_page
);
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search = sanitize_text_field($_GET['search']);
    $args['s'] = $search;
}
$wpaicg_bots = new WP_Query($args);
?>
<div class="wpaicg-mb-10">
    <form action="" method="GET">
        <input type="hidden" name="page" value="wpaicg_chatgpt">
        <input type="hidden" name="action" value="bots">
        <input value="<?php echo isset($_GET['search']) && !empty($_GET['search']) ? esc_html($_GET['search']) : ''?>" name="search" type="text" placeholder="<?php echo esc_html__('Search Bot','gpt3-ai-content-generator')?>">
        <button class="button button-primary"><?php echo esc_html__('Search','gpt3-ai-content-generator')?></button>
        <button type="button" class="button button-primary wpaicg-create-bot"><?php echo esc_html__('Create New Bot','gpt3-ai-content-generator')?></button>
    </form>
</div>
<table class="wp-list-table widefat fixed striped table-view-list posts">
    <thead>
    <tr>
        <th><?php echo esc_html__('Name','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Type','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('ID / Shortcode','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Created','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Updated','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Model','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Context','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Action','gpt3-ai-content-generator')?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($wpaicg_bots->have_posts()){
        foreach($wpaicg_bots->posts as $wpaicg_bot){
            if(strpos($wpaicg_bot->post_content,'\"') !== false) {
                $wpaicg_bot->post_content = str_replace('\"', '&quot;', $wpaicg_bot->post_content);
            }
            if(strpos($wpaicg_bot->post_content,"\'") !== false) {
                $wpaicg_bot->post_content = str_replace('\\', '', $wpaicg_bot->post_content);
            }

            $bot = json_decode($wpaicg_bot->post_content,true);
            if($bot && is_array($bot)){
            $bot['id'] = $wpaicg_bot->ID;
            $bot['ai_avatar_url'] = isset($bot['ai_avatar_id']) && !empty($bot['ai_avatar_id']) ? wp_get_attachment_url($bot['ai_avatar_id']) : '';
            $bot['icon_url_url'] = isset($bot['icon_url']) && !empty($bot['icon_url']) ? wp_get_attachment_url($bot['icon_url']) : '';
            ?>
                <tr>
                    <td><?php echo esc_html($wpaicg_bot->post_title);?></td>
                    <td><?php echo isset($bot['type']) && $bot['type'] == 'shortcode' ? 'Shortcode' : 'Widget';?></td>
                    <td>
                        <code>
                        <?php
                        if(isset($bot['type']) && $bot['type'] === 'shortcode'){
                            echo '[wpaicg_chatgpt id='.esc_html($wpaicg_bot->ID).']';
                        }
                        else{
                            if(isset($bot['pages'])){
                                $pages = array_map('trim', explode(',', $bot['pages']));
                                $key = 0;
                                foreach($pages as $page){
                                    $link = get_permalink($page);
                                    if(!empty($link)){
                                        $key++;
                                        echo ($key == 1 ? '' : ', ').'<a href="'.$link.'" target="_blank">'.$page.'</a>';
                                    }
                                }
                            }
                        }
                        ?>
                        </code>
                    </td>
                    <td><?php echo esc_html(date('d.m.Y H:i',strtotime($wpaicg_bot->post_date)))?></td>
                    <td><?php echo esc_html(date('d.m.Y H:i',strtotime($wpaicg_bot->post_modified)))?></td>
                    <td><?php echo isset($bot['model']) && !empty($bot['model']) ? esc_html($bot['model']) : ''?></td>
                    <td>
                        <?php
                        if(isset($bot['content_aware']) && $bot['content_aware'] == 'yes'){
                            if(isset($bot['embedding']) && $bot['embedding']){
                                echo 'Embeddings';
                            }
                            else{
                                echo 'Excerpt';
                            }
                        }
                        else{
                            echo 'No';
                        }
                        ?>
                    </td>
                    <td>
                        <button class="button button-primary button-small wpaicg-bot-edit" data-content="<?php echo htmlspecialchars(json_encode($bot,JSON_UNESCAPED_UNICODE),ENT_QUOTES, 'UTF-8')?>"><?php echo esc_html__('Edit','gpt3-ai-content-generator')?></button>
                        <a class="button-small button button-link-delete" onclick="return confirm('<?php echo esc_html__('Are you sure?','gpt3-ai-content-generator')?>')" href="<?php echo wp_nonce_url(admin_url('admin.php?page=wpaicg_chatgpt&action=bots&wpaicg_bot_delete='.$wpaicg_bot->ID),'wpaicg_delete_'.$wpaicg_bot->ID)?>"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></a>
                    </td>
                </tr>
            <?php
            }
        }
    }
    ?>
    </tbody>
</table>
<div class="wpaicg-paginate">
    <?php
    echo paginate_links( array(
        'base'         => admin_url('admin.php?page=wpaicg_chatgpt&action=bots&wpage=%#%'),
        'total'        => $wpaicg_bots->max_num_pages,
        'current'      => $wpaicg_bot_page,
        'format'       => '?wpage=%#%',
        'show_all'     => false,
        'prev_next'    => false,
        'add_args'     => false,
    ));
    ?>
</div>
<script>
    jQuery(document).ready(function ($){
        let wpaicg_google_voices = <?php echo json_encode($wpaicg_google_voices)?>;
        let wpaicg_elevenlab_api = '<?php echo esc_html($wpaicg_elevenlabs_api)?>';
        let wpaicg_google_api_key = '<?php  echo $wpaicg_google_api_key?>';
        function wpaicgChangeVoiceService(element){
            let parent = element.parent().parent();
            let voice_service = parent.find('.wpaicg_chatbot_voice_service');
            if(element.prop('checked')){
                voice_service.removeAttr('disabled');
                if(wpaicg_elevenlab_api !== ''){
                    parent.find('.wpaicg_chatbot_elevenlabs_voice').removeAttr('disabled');
                    if(voice_service.val() === ''){
                        parent.find('.wpaicg_chatbot_elevenlabs_voice').remove('disabled');
                    }

                }
                if(wpaicg_google_api_key !== ''){
                    parent.find('.wpaicg_chatbot_voice_language').removeAttr('disabled');
                    parent.find('.wpaicg_chatbot_voice_name').removeAttr('disabled');
                    parent.find('.wpaicg_chatbot_voice_device').removeAttr('disabled');
                    parent.find('.wpaicg_chatbot_voice_speed').removeAttr('disabled');
                    parent.find('.wpaicg_chatbot_voice_pitch').removeAttr('disabled');
                }
            }
            else{
                voice_service.attr('disabled','disabled');
                parent.find('.wpaicg_chatbot_elevenlabs_voice').attr('disabled','disabled');
                parent.find('.wpaicg_chatbot_voice_language').attr('disabled','disabled');
                parent.find('.wpaicg_chatbot_voice_name').attr('disabled','disabled');
                parent.find('.wpaicg_chatbot_voice_device').attr('disabled','disabled');
                parent.find('.wpaicg_chatbot_voice_speed').attr('disabled','disabled');
                parent.find('.wpaicg_chatbot_voice_pitch').attr('disabled','disabled');
            }
        }
        $(document).on('click','.wpaicg_chatbot_chat_to_speech', function(e){
            wpaicgChangeVoiceService($(e.currentTarget));
        });
        $(document).on('keypress','.wpaicg_voice_speed,.wpaicg_voice_pitch', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
        });
        $(document).on('change','.wpaicg_chatbot_voice_service',function(e){
            console.log('accc');
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
        });
        let wpaicg_roles = <?php echo wp_kses_post(json_encode($wpaicg_roles))?>;
        let defaultAIAvatar = '<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>';
        let defaultUserAvatar = '<?php echo get_avatar_url(get_current_user_id())?>';
        $(document).on('change','.wpaicg_chatbot_fontsize', function(){
            wpaicgUpdateRealtime();
        });
        $(document).on('click','.wpaicg_chatbot_save_logs,.wpaicg_chatbot_log_notice,.wpaicg_chatbot_audio_enable,.wpaicg_chatbot_use_avatar,.wpaicg_chatbot_icon_default,.wpaicg_chatbot_ai_avatar_default,.wpaicg_chatbot_ai_avatar_custom,.wpaicg_chatbot_icon_custom', function(){
            wpaicgUpdateRealtime();
        })
        $(document).on('input','.wpaicg_chatbot_welcome,.wpaicg_chatbot_log_notice_message,.wpaicg_chatbot_footer_text,.wpaicg_chatbot_ai_name,.wpaicg_chatbot_you,.wpaicg_chatbot_placeholder,.wpaicg_chatbot_height,.wpaicg_chatbot_width', function(){
            wpaicgUpdateRealtime();
        });
        $(document).on('click', '.wpaicg_chatbot_save_logs', function(e){
            let modalContent = $(e.currentTarget).closest('.wpaicg_modal_content');
            if($(e.currentTarget).prop('checked')){
                modalContent.find('.wpaicg_chatbot_log_request').removeAttr('disabled');
                modalContent.find('.wpaicg_chatbot_log_notice').removeAttr('disabled');
                modalContent.find('.wpaicg_chatbot_log_notice_message').removeAttr('disabled');
            }
            else{
                modalContent.find('.wpaicg_chatbot_log_request').attr('disabled','disabled');
                modalContent.find('.wpaicg_chatbot_log_request').prop('checked',false);
                modalContent.find('.wpaicg_chatbot_log_notice').attr('disabled','disabled');
                modalContent.find('.wpaicg_chatbot_log_notice').prop('checked',false);
                modalContent.find('.wpaicg_chatbot_log_notice_message').attr('disabled','disabled');
            }
        });
        function wpaicgUpdateRealtime(){
            let modalContent = $('.wpaicg_modal_content');
            let fontsize = modalContent.find('.wpaicg_chatbot_fontsize').val();
            let fontcolor = modalContent.find('.wpaicg_chatbot_fontcolor').iris('color');
            let bgcolor = modalContent.find('.wpaicg_chatbot_bgcolor').iris('color');
            let inputbg = modalContent.find('.wpaicg_chatbot_bg_text_field').iris('color');
            let inputborder = modalContent.find('.wpaicg_chatbot_border_text_field').iris('color');
            let sendcolor = modalContent.find('.wpaicg_chatbot_send_color').iris('color');
            let userbg = modalContent.find('.wpaicg_chatbot_user_bg_color').iris('color');;
            let aibg = modalContent.find('.wpaicg_chatbot_ai_bg_color').iris('color');
            let useavatar = modalContent.find('.wpaicg_chatbot_use_avatar').prop('checked') ? true : false;
            let chatwidth = modalContent.find('.wpaicg_chatbot_width').val();
            let chatheight = modalContent.find('.wpaicg_chatbot_height').val();
            let enablemic = modalContent.find('.wpaicg_chatbot_audio_enable').prop('checked') ? true :false;
            let enablepdf = modalContent.find('.wpaicg_chatbot_embedding_pdf').prop('checked') ? true :false;
            let save_log = modalContent.find('.wpaicg_chatbot_save_logs').prop('checked') ? true :false;
            let log_notice = modalContent.find('.wpaicg_chatbot_log_notice').prop('checked') ? true :false;
            let log_notice_msg = modalContent.find('.wpaicg_chatbot_log_notice_message').val();
            let miccolor = modalContent.find('.wpaicg_chatbot_mic_color').iris('color');
            let pdf_color = modalContent.find('.wpaicg_chatbot_pdf_color').iris('color');
            let ai_thinking = modalContent.find('.wpaicg_chatbot_ai_thinking').val();
            let ai_name = modalContent.find('.wpaicg_chatbot_ai_name').val();
            let you_name = modalContent.find('.wpaicg_chatbot_you').val();
            let placeholder = modalContent.find('.wpaicg_chatbot_placeholder').val();
            let welcome = modalContent.find('.wpaicg_chatbot_welcome').val();
            let footer = modalContent.find('.wpaicg_chatbot_footer_text').val();
            let previewWidth = modalContent.find('.wpaicg-bot-preview').width();
            modalContent.find('.wpaicg-chat-shortcode').attr('data-width',chatwidth);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-height',chatheight);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-text_rounded',modalContent.find('.wpaicg_chatbot_text_rounded').val());
            modalContent.find('.wpaicg-chat-shortcode').attr('data-text_height',modalContent.find('.wpaicg_chatbot_text_height').val());
            modalContent.find('.wpaicg-chat-shortcode').attr('data-chat_rounded',modalContent.find('.wpaicg_chatbot_chat_rounded').val());
            if(welcome !== ''){
                modalContent.find('.wpaicg_chatbot_welcome_message').html(welcome);
            }
            if(save_log && log_notice && log_notice_msg !== ''){
                modalContent.find('.wpaicg_chatbot_log_preview span').html(log_notice_msg);
                modalContent.find('.wpaicg_chatbot_log_preview').show();
            }
            else{
                modalContent.find('.wpaicg_chatbot_log_preview').hide();
            }
            if(modalContent.find('.wpaicg_chatbot_icon_custom').prop('checked') && modalContent.find('.wpaicg_chatbox_icon img').length){
                modalContent.find('.wpaicg-chatbot-widget-icon').html('<img src="'+modalContent.find('.wpaicg_chatbox_icon img').attr('src')+'" height="75" width="75">')
            }
            else{
                modalContent.find('.wpaicg-chatbot-widget-icon').html('<img src="'+defaultAIAvatar+'" height="75" width="75">')
            }
            if(chatwidth === ''){
                chatwidth = 350;
            }
            if(chatheight === ''){
                chatheight = 400;
            }
            var wpaicgWindowWidth = window.innerWidth;
            var wpaicgWindowHeight = window.innerHeight;
            if(chatwidth.indexOf('%') < 0){
                if(chatwidth.indexOf('px') < 0){
                    chatwidth = parseFloat(chatwidth);
                }
                else{
                    chatwidth = parseFloat(chatwidth.replace(/px/g,''));
                }
            }
            else{
                chatwidth = parseFloat(chatwidth.replace(/%/g,''));
                chatwidth = chatwidth*wpaicgWindowWidth/100;
            }
            if(chatheight.indexOf('%') < 0){
                if(chatheight.indexOf('px') < 0){
                    chatheight = parseFloat(chatheight);
                }
                else{
                    chatheight = parseFloat(chatheight.replace(/px/g,''));
                }
            }
            else{
                chatheight = parseFloat(chatheight.replace(/%/g,''));
                chatheight = chatheight*wpaicgWindowHeight/100;
            }

            if(parseInt(chatwidth) > previewWidth){
                chatwidth = previewWidth;
            }
            modalContent.find('.wpaicg-chat-shortcode').css({
                width: chatwidth+'px'
            });
            let content_height = parseInt(chatheight) - 44;
            if(footer !== ''){
                content_height  = parseInt(chatheight) - 44 - 13;
                modalContent.find('.wpaicg-chat-shortcode-type').css({
                    padding: '5px 5px 0 5px'
                });
                $('.wpaicg-chat-shortcode-footer').html(footer);
                $('.wpaicg-chat-shortcode-footer').show();
            }
            else{
                $('.wpaicg-chat-shortcode-footer').hide();
                modalContent.find('.wpaicg-chat-shortcode-type').css({
                    padding: '5px'
                })
            }
            modalContent.find('.wpaicg-chat-shortcode-content ul').css({
                height: content_height+'px'
            })
            if(enablemic){
                modalContent.find('.wpaicg-mic-icon').show();
            }
            else{
                modalContent.find('.wpaicg-mic-icon').hide();
            }
            if(enablepdf){
                modalContent.find('.wpaicg-pdf-icon').show();
            }
            else{
                modalContent.find('.wpaicg-pdf-icon').hide();
            }
            modalContent.find('.wpaicg-chat-shortcode-messages li').css({
                'font-size': fontsize+'px',
                'color': fontcolor
            });
            modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-ai-message').css({
                'background-color': aibg
            });
            modalContent.find('.wpaicg-chat-shortcode-footer').css({
                'background-color': bgcolor
            });
            modalContent.find('.wpaicg-chat-shortcode').attr('data-fontsize',fontsize);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-color',fontcolor);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-use-avatar',useavatar ? 1 : 0);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-you',you_name);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-ai-name',ai_name);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-ai-bg-color',aibg);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-user-bg-color',userbg);
            if(useavatar){
                let messageAIAvatar = defaultAIAvatar;
                if(modalContent.find('.wpaicg_chatbox_avatar img').length && modalContent.find('.wpaicg_chatbot_ai_avatar_custom').prop('checked')){
                    messageAIAvatar = modalContent.find('.wpaicg_chatbox_avatar img').attr('src');
                }
                modalContent.find('.wpaicg-chat-shortcode').attr('data-ai-avatar',messageAIAvatar);
                modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-ai-message .wpaicg-chat-avatar').html('<img src="'+messageAIAvatar+'" height="40" width="40">');
                modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-user-message .wpaicg-chat-avatar').html('<img src="'+defaultUserAvatar+'" height="40" width="40">');
            }
            else{
                modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-ai-message .wpaicg-chat-avatar').html(ai_name+':&nbsp;');
                modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-user-message .wpaicg-chat-avatar').html(you_name+':&nbsp;');
            }
            modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-user-message').css({
                'background-color': userbg
            });
            modalContent.find('.wpaicg-chat-shortcode-content').css({
                'background-color': bgcolor
            });
            modalContent.find('.wpaicg-chat-shortcode-type').css({
                'background-color': bgcolor
            });
            modalContent.find('textarea.wpaicg-chat-shortcode-typing').css({
                'background-color': inputbg,
                'border-color':inputborder
            });
            modalContent.find('textarea.wpaicg-chat-shortcode-typing').attr('placeholder', placeholder);
            modalContent.find('.wpaicg-chat-shortcode-send').css({
                'color': sendcolor
            })
            modalContent.find('.wpaicg-mic-icon').css({
                'color': miccolor
            });
            modalContent.find('.wpaicg-pdf-icon').css({
                'color': pdf_color
            });
            modalContent.find('.wpaicg-pdf-remove').css({
                'color': pdf_color
            });
            modalContent.find('.wpaicg-pdf-loading').css({
                'border-color': pdf_color,
                'border-bottom-color': 'transparent'
            });
            let contentaware = modalContent.find('.wpaicg_chatbot_content_aware').val();
            if(contentaware === 'no'){
                $('.wpaicg_chatbot_chat_excerpt').prop('checked', false);
                $('.wpaicg_chatbot_chat_excerpt').attr('disabled','disabled');
                $('.wpaicg_chatbot_embedding').prop('checked', false);
                $('.wpaicg_chatbot_embedding').attr('disabled','disabled');
                $('.wpaicg_chatbot_embedding_type').attr('disabled','disabled');
                $('.wpaicg_chatbot_embedding_index').attr('disabled','disabled');
                $('.wpaicg_chatbot_embedding_pdf').attr('disabled','disabled');
                $('.wpaicg_chatbot_embedding_pdf_message').attr('disabled','disabled');
                $('.wpaicg_chatbot_pdf_pages').attr('disabled','disabled');
                $('.wpaicg_chatbot_embedding_top').attr('disabled','disabled');
            }
            if(footer !== ''){

            }
            wpaicgChatShortcodeSize();

        }
        $(document).on('click','.wpaicg-bot-step',function (e){
            let btn = $(e.currentTarget);
            let step = btn.attr('data-type');
            let wpaicgGrid = btn.closest('.wpaicg-grid');
            wpaicgGrid.find('.wpaicg-bot-wizard').hide();
            wpaicgGrid.find('.wpaicg-bot-'+step).show();
        });
        function wpaicgLoading(btn){
            btn.attr('disabled','disabled');
            if(!btn.find('spinner').length){
                btn.append('<span class="spinner"></span>');
            }
            btn.find('.spinner').css('visibility','unset');
        }
        function wpaicgRmLoading(btn){
            btn.removeAttr('disabled');
            btn.find('.spinner').remove();
        }
        $('.wpaicg_modal_close').click(function (){
            $('.wpaicg_modal_close').closest('.wpaicg_modal').hide();
            $('.wpaicg-overlay').hide();
        });
        $(document).on('click','.wpaicg_chatbot_type_widget', function (){
            $('.wpaicg_modal_content .wpaicg_chatbot_position').show();
            $('.wpaicg_modal_content .wpaicg-widget-icon').show();
            $('.wpaicg_modal_content .wpaicg-widget-pages').show();
            $('.wpaicg_modal_content .wpaicg-chatbot-widget-icon').show();
        });
        $(document).on('click','.wpaicg_chatbot_type_shortcode', function (){
            $('.wpaicg_modal_content .wpaicg-chatbot-widget-icon').hide();
            $('.wpaicg_modal_content .wpaicg_chatbot_position').hide();
            $('.wpaicg_modal_content .wpaicg-widget-pages').hide();
            $('.wpaicg_modal_content .wpaicg-widget-icon').hide();
        });
        $('.wpaicg-create-bot').click(function (){
            $('.wpaicg_modal_title').html('<?php echo esc_html__('Create New Bot','gpt3-ai-content-generator')?>');
            $('.wpaicg_modal_content').html($('.wpaicg-create-bot-default').html());
            $('.wpaicg_modal_content .wpaicgchat_color').wpColorPicker({
                change: function (event, ui){
                    wpaicgUpdateRealtime();
                },
                clear: function(event){
                    wpaicgUpdateRealtime();
                }
            });
            $('.wpaicg_modal_content .wpaicg_chatbot_type_shortcode').prop('checked',true);
            $('.wpaicg_modal_content .wpaicg_chatbot_position').hide();
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
            wpaicgcollectVoices($('.wpaicg_modal_content .wpaicg_voice_language'));
            wpaicgChatInit();
        });
        $(document).on('click', '.wpaicg_chatbox_icon', function (e){
            e.preventDefault();
            $('.wpaicg_modal_content .wpaicg_chatbox_icon_default').prop('checked',false);
            $('.wpaicg_modal_content .wpaicg_chatbox_icon_custom').prop('checked',true);
            let button = $(e.currentTarget),
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
                    $('.wpaicg_modal_content .wpaicg_chatbot_icon_url').val(attachment.id);
                    wpaicgUpdateRealtime();
                }).open();
        });
        $(document).on('click', '.wpaicg_chatbox_avatar', function (e){
            e.preventDefault();
            $('.wpaicg_modal_content .wpaicg_chatbot_ai_avatar_default').prop('checked',false);
            $('.wpaicg_modal_content .wpaicg_chatbox_avatar_custom').prop('checked',true);
            let button = $(e.currentTarget),
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
                    $('.wpaicg_modal_content .wpaicg_chatbot_ai_avatar_id').val(attachment.id);
                    wpaicgUpdateRealtime();
                }).open();
        });
        $(document).on('submit','.wpaicg_modal_content .wpaicg-bot-form', function (e){
            e.preventDefault();
            let form = $(e.currentTarget);
            let btn = form.find('.wpaicg-chatbot-submit');
            let data = form.serialize();
            let name = form.find('.wpaicg_chatbot_name').val();
            let has_error = false;
            if(name === ''){
                has_error = '<?php echo esc_html__('Please enter a name for your awesome chat bot','gpt3-ai-content-generator')?>';
            }
            else if(form.find('.wpaicg_voice_speed').length){
                let wpaicg_voice_speed = parseFloat(form.find('.wpaicg_voice_speed').val());
                let wpaicg_voice_pitch = parseFloat(form.find('.wpaicg_voice_pitch').val());
                let wpaicg_voice_name = parseFloat(form.find('.wpaicg_voice_name').val());
                if (wpaicg_voice_speed < 0.25 || wpaicg_voice_speed > 4) {
                    has_error = '<?php echo sprintf(esc_html__('Please enter valid voice speed value between %s and %s', 'gpt3-ai-content-generator'), 0.25, 4)?>';
                } else if (wpaicg_voice_pitch < -20 || wpaicg_voice_speed > 20) {
                    has_error = '<?php echo sprintf(esc_html__('Please enter valid voice pitch value between %s and %s', 'gpt3-ai-content-generator'), -20, 20)?>';
                }
                else if(wpaicg_voice_name === ''){
                    has_error = '<?php echo esc_html__('Please select voice name', 'gpt3-ai-content-generator')?>';
                }
            }
            if(has_error){
                alert(has_error);
            }
            else {
                $.ajax({
                    url: '<?php echo esc_url(admin_url('admin-ajax.php'))?>',
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function () {
                        wpaicgLoading(btn)
                    },
                    success: function (res) {
                        wpaicgRmLoading(btn);
                        if (res.status === 'success') {
                            window.location.href = '<?php echo admin_url('admin.php?page=wpaicg_chatgpt&action=bots&update_success=true')?>';
                        } else {
                            alert(res.msg);
                        }
                    }
                })
            }
        });
        $(document).on('input','.wpaicg_chatbot_chat_rounded,.wpaicg_chatbot_text_height,.wpaicg_chatbot_text_rounded', function(){
            wpaicgUpdateRealtime();
        })
        $('.wpaicg-bot-edit').click(function (){
            let fields = $(this).attr('data-content');
            // fields = fields.replace(/\\/g,'');
            fields = JSON.parse(fields);
            $('.wpaicg_modal_title').html('<?php echo esc_html__('Edit Bot','gpt3-ai-content-generator')?>');
            $('.wpaicg_modal_content').html($('.wpaicg-create-bot-default').html());
            let modalContent = $('.wpaicg_modal_content');
            let wpaicg_save_log = false;
            modalContent.find('.wpaicg_chatbot_log_request').removeAttr('disabled');
            modalContent.find('.wpaicg_chatbot_log_notice').removeAttr('disabled');
            modalContent.find('.wpaicg_chatbot_log_notice_message').removeAttr('disabled');
            modalContent.find('.wpaicg-chat-shortcode').attr('data-bot-id',fields.id);
            $.each(fields, function (key, field){
                if(key === 'chat_to_speech'){
                    if(field === '1'){
                        modalContent.find('.wpaicg-chat-shortcode').attr('data-speech',1);
                    }
                    else{
                        modalContent.find('.wpaicg-chat-shortcode').attr('data-speech','');
                    }
                }
                if(key === 'elevenlabs_voice'){
                    if(field !== ''){
                        modalContent.find('.wpaicg-chat-shortcode').attr('data-voice',field);
                    }
                    else{
                        modalContent.find('.wpaicg-chat-shortcode').attr('data-voice','');
                    }
                }
                if(key === 'voice_service' || key === 'voice_language' || key === 'voice_name' || key === 'voice_device' || key === 'voice_speed' || key === 'voice_pitch'){
                    modalContent.find('.wpaicg-chat-shortcode').attr('data-'+key,field);
                }
                if(key === 'chat_to_speech' && field === '1'){
                    if(wpaicg_elevenlab_api !== '' || wpaicg_google_api_key !== '') {
                        modalContent.find('.wpaicg_chatbot_voice_service').removeAttr('disabled');
                    }
                    if(wpaicg_elevenlab_api !== ''){
                        modalContent.find('.wpaicg_elevenlabs_voice').removeAttr('disabled');
                    }
                    if(wpaicg_google_api_key !== ''){
                        modalContent.find('.wpaicg_voice_language').removeAttr('disabled');
                        modalContent.find('.wpaicg_voice_name').removeAttr('disabled');
                        modalContent.find('.wpaicg_voice_device').removeAttr('disabled');
                        modalContent.find('.wpaicg_voice_speed').removeAttr('disabled');
                        modalContent.find('.wpaicg_voice_pitch').removeAttr('disabled');
                    }
                }
                if(key == 'voice_service'){
                    if(field === 'google'){
                        modalContent.find('.wpaicg_voice_service_elevenlabs').hide();
                        modalContent.find('.wpaicg_voice_service_google').show();
                    }
                }
                if(key === 'width'){
                    modalContent.find('.wpaicg-chat-shortcode').attr('data-width',field);
                }
                if(key === 'height'){
                    modalContent.find('.wpaicg-chat-shortcode').attr('data-height',field);
                }
                if(key === 'text_rounded'){
                    modalContent.find('.wpaicg-chat-shortcode').attr('data-text_rounded',field);
                }
                if(key === 'text_height'){
                    modalContent.find('.wpaicg-chat-shortcode').attr('data-text_height',field);
                }
                if(key === 'chat_rounded'){
                    modalContent.find('.wpaicg-chat-shortcode').attr('data-chat_rounded',field);
                }
                if(key === 'chat_addition' && field === '1'){
                    modalContent.find('.wpaicg_chatbot_chat_addition_text').removeAttr('disabled');
                    modalContent.find('.wpaicg_chat_addition_template').removeAttr('disabled');
                }
                if(typeof field === 'string' && field.indexOf('&quot;') > -1) {
                    field = field.replace(/&quot;/g, '"');
                }
                if(key === 'type'){
                    if(field === 'widget'){
                        modalContent.find('.wpaicg-chatbot-widget-icon').show();
                        modalContent.find('.wpaicg-widget-icon').show();
                        modalContent.find('.wpaicg-widget-pages').show();
                        modalContent.find('.wpaicg_chatbot_position').show();
                    }
                    else{
                        modalContent.find('.wpaicg-chatbot-widget-icon').hide();
                    }
                    modalContent.find('.wpaicg_chatbot_type_'+field).prop('checked',true);
                }
                else if(key === 'icon'){
                    modalContent.find('.wpaicg_chatbot_icon_default').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_icon_custom').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_icon_'+field).prop('checked',true);
                    if(field === 'custom' && fields.icon_url_url !== ''){
                        modalContent.find('.wpaicg_chatbox_icon').html('<img src="'+fields.icon_url_url+'" height="75" width="75">');
                        modalContent.find('.wpaicg-chatbot-widget-icon').html('<img src="'+fields.icon_url_url+'" height="75" width="75">');
                    }
                }
                else if(key === 'ai_avatar'){
                    modalContent.find('.wpaicg_chatbot_ai_avatar_default').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_ai_avatar_custom').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_ai_avatar_'+field).prop('checked',true);
                    if(field === 'custom' && fields.ai_avatar_url !== ''){
                        modalContent.find('.wpaicg_chatbox_avatar').html('<img src="'+fields.ai_avatar_url+'" height="40" width="40">');
                    }
                }
                else if(key === 'moderation_notice'){
                    if(field === ''){
                        field = '<?php echo esc_html__('Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.','gpt3-ai-content-generator')?>';
                    }
                    modalContent.find('.wpaicg_chatbot_'+key).val(field);
                }
                else if(key === 'position'){
                    modalContent.find('.wpaicg_chatbot_position_left').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_position_right').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_position_'+field).prop('checked',true);
                }
                else if(key === 'voice_name'){
                    modalContent.find('.wpaicg_chatbot_voice_name').attr('data-value',field);
                }
                else if((key === 'fullscreen' || key === 'embedding_pdf' || key === 'chat_to_speech' || key === 'close_btn' || key === 'download_btn' || key === 'log_request' || key === 'audio_enable' || key === 'moderation' || key === 'use_avatar' || key === 'chat_addition' || key === 'save_logs' || key === 'log_notice') && field === '1'){
                    if(key === 'save_logs'){
                        wpaicg_save_log = true;
                    }
                    if((key === 'log_request' || key === 'log_notice' || key === 'log_request') && wpaicg_save_log){
                        modalContent.find('.wpaicg_chatbot_'+key).prop('checked',true);
                        modalContent.find('.wpaicg_chatbot_'+key).removeAttr('disabled');
                    }
                    else if((key === 'log_request' || key === 'log_notice' || key === 'log_request') && !wpaicg_save_log){
                        modalContent.find('.wpaicg_chatbot_'+key).prop('checked',false);
                        modalContent.find('.wpaicg_chatbot_'+key).attr('disabled','disabled');
                    }
                    else{
                        modalContent.find('.wpaicg_chatbot_'+key).prop('checked',true);
                    }
                }
                else if(key === 'user_limited' && field === '1'){
                    modalContent.find('.wpaicg_chatbot_user_limited').prop('checked',true);
                    modalContent.find('.wpaicg_chatbot_user_tokens').removeAttr('disabled');
                    modalContent.find('.wpaicg_limit_set_role').addClass('disabled');
                    modalContent.find('.wpaicg_role_limited').prop('checked',false);
                }
                else if(key === 'role_limited' && field === '1'){
                    modalContent.find('.wpaicg_role_limited').prop('checked',true);
                    modalContent.find('.wpaicg_chatbot_user_limited').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_user_tokens').attr('disabled','disabled');
                    modalContent.find('.wpaicg_limit_set_role').removeClass('disabled');
                }
                else if(key === 'guest_limited' && field === '1'){
                    modalContent.find('.wpaicg_chatbot_guest_limited').prop('checked',true);
                    modalContent.find('.wpaicg_chatbot_guest_tokens').removeAttr('disabled');
                }
                else if(key === 'embedding' && field === '1'){
                    modalContent.find('.wpaicg_chatbot_chat_excerpt').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_chat_excerpt').addClass('asdisabled');
                    modalContent.find('.wpaicg_chatbot_embedding').removeClass('asdisabled');
                    modalContent.find('.wpaicg_chatbot_embedding').prop('checked',true);
                    modalContent.find('.wpaicg_chatbot_embedding_type').removeAttr('disabled');
                    modalContent.find('.wpaicg_chatbot_embedding_type').removeClass('asdisabled');
                    modalContent.find('.wpaicg_chatbot_embedding_index').removeAttr('disabled');
                    modalContent.find('.wpaicg_chatbot_embedding_index').removeClass('asdisabled');
                    modalContent.find('.wpaicg_chatbot_embedding_pdf').removeAttr('disabled');
                    modalContent.find('.wpaicg_chatbot_embedding_pdf').removeClass('asdisabled');
                    modalContent.find('.wpaicg_chatbot_embedding_pdf_message').removeAttr('disabled');
                    modalContent.find('.wpaicg_chatbot_embedding_pdf_message').removeClass('asdisabled');
                    modalContent.find('.wpaicg_chatbot_pdf_pages').removeAttr('disabled');
                    modalContent.find('.wpaicg_chatbot_pdf_pages').removeClass('asdisabled');
                    modalContent.find('.wpaicg_chatbot_embedding_top').removeAttr('disabled');
                    modalContent.find('.wpaicg_chatbot_embedding_top').removeClass('asdisabled');
                }
                if(key === 'limited_roles'){
                    if(typeof field === 'object'){
                        $.each(field, function(role,limit_num){
                            modalContent.find('.wpaicg_role_'+role).val(limit_num);
                        })
                    }
                }
                else if(key === 'chat_addition_text'){
                    if(field !== ''){
                        modalContent.find('.wpaicg_chatbot_chat_addition_text').val(field);
                    }
                }
                else{
                    if(typeof field === 'string' && field.indexOf('&quot;') > -1) {
                        field = field.replace(/&quot;/g, '"');
                    }
                    if(key === 'limited_message' && field === ''){
                        field = '<?php echo esc_html__('You have reached your token limit.','gpt3-ai-content-generator')?>';
                    }
                    if(key === 'log_notice_message' && !wpaicg_save_log){
                        modalContent.find('.wpaicg_chatbot_log_notice_message').attr('disabled','disabled');
                    }
                    modalContent.find('.wpaicg_chatbot_'+key).val(field);
                }
            });
            if(!wpaicg_save_log){
                modalContent.find('.wpaicg_chatbot_log_request').prop('checked',false);
                modalContent.find('.wpaicg_chatbot_log_request').attr('disabled','disabled');
                modalContent.find('.wpaicg_chatbot_log_notice').prop('checked',false);
                modalContent.find('.wpaicg_chatbot_log_notice').attr('disabled','disabled');
                modalContent.find('.wpaicg_chatbot_log_notice_message').attr('disabled','disabled');
            }
            $('.wpaicg_modal_content .wpaicgchat_color').wpColorPicker({
                change: function (event, ui){
                    wpaicgUpdateRealtime();
                },
                clear: function(event){
                    wpaicgUpdateRealtime();
                }
            });
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
            if(modalContent.find('.wpaicg_voice_language').length){
                wpaicgcollectVoices(modalContent.find('.wpaicg_voice_language'));
            }
            wpaicgUpdateRealtime();
            wpaicgChatInit();
            wpaicgChangeVoiceService(modalContent.find('.wpaicg_chatbot_chat_to_speech'));
        });
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
        $(document).on('click','.wpaicg_limit_set_role',function (e){
            if(!$(e.currentTarget).hasClass('disabled')) {
                if ($('.wpaicg_modal_content .wpaicg_role_limited').prop('checked')) {
                    let html = '';
                    $.each(wpaicg_roles, function (key, role) {
                        let valueRole = $('.wpaicg_modal_content .wpaicg_role_'+key).val();
                        html += '<div style="padding: 5px;display: flex;justify-content: space-between;align-items: center;"><label><strong>'+role+'</strong></label><input class="wpaicg_update_role_limit" data-target="'+key+'" value="'+valueRole+'" placeholder="<?php echo esc_html__('Empty for no-limit','gpt3-ai-content-generator')?>" type="text"></div>';
                    });
                    html += '<div style="padding: 5px"><button class="button button-primary wpaicg_save_role_limit" style="width: 100%;margin: 5px 0;"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button></div>';
                    $('.wpaicg_modal_title_second').html('<?php echo esc_html__('Role Limit','gpt3-ai-content-generator')?>');
                    $('.wpaicg_modal_content_second').html(html);
                    $('.wpaicg-overlay-second').css('display','flex');
                    $('.wpaicg_modal_second').show();

                } else {
                    $.each(wpaicg_roles, function (key, role) {
                        $('.wpaicg_modal_content .wpaicg_role_' + key).val('');
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
        $(document).on('click','.wpaicg_chatbot_embedding', function (e){
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding').prop('checked',true);
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').prop('checked',false);
            $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').removeAttr('disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_index').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_index').removeAttr('disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf').removeAttr('disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf_message').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf_message').removeAttr('disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_pdf_pages').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_pdf_pages').removeAttr('disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').removeAttr('disabled');
        });
        $(document).on('click','.wpaicg_chatbot_chat_addition', function (e){
            if($(e.currentTarget).prop('checked')){
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_addition_text').removeAttr('disabled');
                $('.wpaicg_modal_content .wpaicg_chat_addition_template').removeAttr('disabled');
            }
            else{
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_addition_text').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chat_addition_template').attr('disabled','disabled');
            }
        });
        $(document).on('change', '.wpaicg_chat_addition_template',function (e){
            var addition_text_template = $(e.currentTarget).val();
            if(addition_text_template !== ''){
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_addition_text').val(addition_text_template);
            }
        });
        $(document).on('click','.wpaicg_role_limited', function (e){
            if($(e.currentTarget).prop('checked')){
                $('.wpaicg_modal_content .wpaicg_chatbot_user_tokens').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_user_limited').prop('checked',false);
                $('.wpaicg_modal_content .wpaicg_limit_set_role').removeClass('disabled');
            }
            else{
                $('.wpaicg_modal_content .wpaicg_limit_set_role').addClass('disabled');
            }
        })
        $(document).on('click','.wpaicg_chatbot_user_limited', function (e){
            if($(e.currentTarget).prop('checked')){
                $('.wpaicg_modal_content .wpaicg_chatbot_user_tokens').removeAttr('disabled');
                $('.wpaicg_modal_content .wpaicg_role_limited').prop('checked',false);
                $('.wpaicg_modal_content .wpaicg_limit_set_role').addClass('disabled');
            }
            else{
                $('.wpaicg_modal_content .wpaicg_chatbot_user_tokens').attr('disabled','disabled');
            }
        });
        $(document).on('click','.wpaicg_chatbot_guest_limited', function (e){
            if($(e.currentTarget).prop('checked')){
                $('.wpaicg_modal_content .wpaicg_chatbot_guest_tokens').removeAttr('disabled');
            }
            else{
                $('.wpaicg_modal_content .wpaicg_chatbot_guest_tokens').attr('disabled','disabled');
            }
        });
        $(document).on('click','.wpaicg_chatbot_chat_excerpt', function (e){
            $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').prop('checked',true);
            $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding').prop('checked', false);
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').attr('disabled','disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_index').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_index').attr('disabled','disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf').attr('disabled','disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf_message').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf_message').attr('disabled','disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_pdf_pages').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_pdf_pages').attr('disabled','disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').attr('disabled','disabled');
        });
        $(document).on('change', '.wpaicg_chatbot_content_aware', function (e){
            if($(e.currentTarget).val() === 'yes'){
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').prop('checked',true);
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').removeClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').removeAttr('disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').prop('checked', false);
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').removeAttr('disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_index').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_index').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf_message').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf_message').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_pdf_pages').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_pdf_pages').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').attr('disabled','disabled');
            }
            else{
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').prop('checked',false);
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').removeClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').prop('checked', false);
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_index').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_index').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf_message').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_pdf_message').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_pdf_pages').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_pdf_pages').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').attr('disabled','disabled');
            }
        });
    })
</script>
