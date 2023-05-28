<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wp, $wpdb;
if(is_admin()){
    $current_url = admin_url('admin.php?page=wpaicg_myai_account');
}
else{
    $current_url = home_url($wp->request);
}
$wpaicg_log_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$wpaicg_query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix.$this->table_name." WHERE user_id=%d", get_current_user_id());
$wpaicg_total_query = "SELECT COUNT(1) FROM (${wpaicg_query}) AS combined_table";
$wpaicg_total = $wpdb->get_var( $wpaicg_total_query );
$wpaicg_items_per_page = 30;
$wpaicg_offset = ( $wpaicg_log_page * $wpaicg_items_per_page ) - $wpaicg_items_per_page;
$wpaicg_logs = $wpdb->get_results( $wpdb->prepare( $wpaicg_query . " ORDER BY created_at DESC LIMIT %d, %d", $wpaicg_offset, $wpaicg_items_per_page ) );
$wpaicg_totalPage = ceil($wpaicg_total / $wpaicg_items_per_page);
$wpaicg_tokens_forms_left = $wpaicg_tokens_image_left = $wpaicg_tokens_promptbase_left = $wpaicg_tokens_chat_left = __('Unlimited','gpt3-ai-content-generator');
$wpaicg_playground = \WPAICG\WPAICG_Playground::get_instance();
$wpaicg_form_limited = $wpaicg_promptbase_limited = $wpaicg_image_limited = $wpaicg_chat_limited = false;
/*Get Form Limit Left*/
$wpaicg_form_tokens = $wpaicg_playground->wpaicg_token_handling('form');
if($wpaicg_form_tokens['limit']){
    if($wpaicg_form_tokens['limited']){
        $wpaicg_tokens_forms_left = esc_html__('Out of Quota','gpt3-ai-content-generator');
        $wpaicg_form_limited = true;
    }
    else{
        $wpaicg_tokens_forms_left = $wpaicg_form_tokens['left_tokens'];
    }
}
/*Get Promptbase Limit Left*/
$wpaicg_promptbase_tokens = $wpaicg_playground->wpaicg_token_handling('promptbase');
if($wpaicg_promptbase_tokens['limit']){
    if($wpaicg_promptbase_tokens['limited']){
        $wpaicg_promptbase_limited = true;
        $wpaicg_tokens_promptbase_left = esc_html__('Out of Quota','gpt3-ai-content-generator');
    }
    else{
        $wpaicg_tokens_promptbase_left = $wpaicg_promptbase_tokens['left_tokens'];
    }
}
/*Get Image Limit Left*/
$wpaicg_image_tokens = $wpaicg_playground->wpaicg_token_handling('image');
if($wpaicg_image_tokens['limit']){
    if($wpaicg_image_tokens['limited']){
        $wpaicg_image_limited = true;
        $wpaicg_tokens_image_left = esc_html__('Out of Quota','gpt3-ai-content-generator');
    }
    else{
        $wpaicg_tokens_image_left = $wpaicg_image_tokens['left_tokens'];
    }
}
/*Get Chat Limit Left*/
// Get all bots limit
$wpaicg_bots = new WP_Query(array(
    'post_type' => 'wpaicg_chatbot',
    'posts_per_page' => -1,
));
$wpaicg_chat_has_limit = false;
$wpaicg_chat_total_token = 0;
$wpaicg_user_roles = wp_get_current_user()->roles;
if($wpaicg_bots->have_posts()){
    foreach($wpaicg_bots->posts as $wpaicg_bot){
        if(strpos($wpaicg_bot->post_content,'\"') !== false) {
            $wpaicg_bot->post_content = str_replace('\"', '&quot;', $wpaicg_bot->post_content);
        }
        if(strpos($wpaicg_bot->post_content,"\'") !== false) {
            $wpaicg_bot->post_content = str_replace('\\', '', $wpaicg_bot->post_content);
        }
        $bot_settings = json_decode($wpaicg_bot->post_content,true);
        if(
            isset($bot_settings['user_limited'])
            && $bot_settings['user_limited']
            && isset($bot_settings['user_tokens'])
            && !empty($bot_settings['user_tokens'])
        ){
            $wpaicg_chat_has_limit = true;
            $wpaicg_chat_total_token += (float)$bot_settings['user_tokens'];
        }
        elseif(
            isset($bot_settings['role_limited'])
            && $bot_settings['role_limited']
            && isset($bot_settings['limited_roles'])
            && $bot_settings['limited_roles']
            && is_array($bot_settings['limited_roles'])
            && count($bot_settings['limited_roles'])
        ){
            foreach($wpaicg_user_roles as $wpaicg_user_role){
                if(isset($bot_settings['limited_roles'][$wpaicg_user_role]) && !empty($bot_settings['limited_roles'][$wpaicg_user_role])){
                    $wpaicg_chat_has_limit = true;
                    $wpaicg_chat_total_token += (float)$bot_settings['limited_roles'][$wpaicg_user_role];
                }
            }
        }
    }
}
$wpaicg_chat_widget = get_option('wpaicg_chat_widget',[]);
if(
    isset($wpaicg_chat_widget['user_limited'])
    && $wpaicg_chat_widget['user_limited']
    && isset($wpaicg_chat_widget['user_tokens'])
    && !empty($wpaicg_chat_widget['user_tokens'])
){
    $wpaicg_chat_has_limit = true;
    $wpaicg_chat_total_token += (float)$wpaicg_chat_widget['user_tokens'];
}
elseif(
    isset($wpaicg_chat_widget['role_limited'])
    && $wpaicg_chat_widget['role_limited']
    && isset($wpaicg_chat_widget['limited_roles'])
    && $wpaicg_chat_widget['limited_roles']
    && is_array($wpaicg_chat_widget['limited_roles'])
    && count($wpaicg_chat_widget['limited_roles'])
){
    foreach($wpaicg_user_roles as $wpaicg_user_role){
        if(isset($wpaicg_chat_widget['limited_roles'][$wpaicg_user_role]) && !empty($wpaicg_chat_widget['limited_roles'][$wpaicg_user_role])){
            $wpaicg_chat_has_limit = true;
            $wpaicg_chat_total_token += (float)$wpaicg_chat_widget['limited_roles'][$wpaicg_user_role];
        }
    }
}
$wpaicg_chat_shortcode_options = get_option('wpaicg_chat_shortcode_options',[]);
if(
    isset($wpaicg_chat_shortcode_options['user_limited'])
    && $wpaicg_chat_shortcode_options['user_limited']
    && isset($wpaicg_chat_shortcode_options['user_tokens'])
    && !empty($wpaicg_chat_shortcode_options['user_tokens'])
){
    $wpaicg_chat_has_limit = true;
    $wpaicg_chat_total_token += (float)$wpaicg_chat_shortcode_options['user_tokens'];
}
elseif(
    isset($wpaicg_chat_shortcode_options['role_limited'])
    && $wpaicg_chat_shortcode_options['role_limited']
    && isset($wpaicg_chat_shortcode_options['limited_roles'])
    && $wpaicg_chat_shortcode_options['limited_roles']
    && is_array($wpaicg_chat_shortcode_options['limited_roles'])
    && count($wpaicg_chat_shortcode_options['limited_roles'])
){
    foreach($wpaicg_user_roles as $wpaicg_user_role){
        if(isset($wpaicg_chat_shortcode_options['limited_roles'][$wpaicg_user_role]) && !empty($wpaicg_chat_shortcode_options['limited_roles'][$wpaicg_user_role])){
            $wpaicg_chat_has_limit = true;
            $wpaicg_chat_total_token += (float)$wpaicg_chat_shortcode_options['limited_roles'][$wpaicg_user_role];
        }
    }
}
$user_meta_key = 'wpaicg_chat_tokens';
$user_tokens = get_user_meta(get_current_user_id(), $user_meta_key, true);
$wpaicg_chat_total_token += (float)$user_tokens;
/*count total chat tokens*/
if($wpaicg_chat_has_limit){
    $wpaicg_chat_token_log = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpaicg_chattokens WHERE user_id=%d",get_current_user_id()));
    $wpaicg_token_usage_client = $wpaicg_chat_token_log ? $wpaicg_chat_token_log->tokens : 0;
    if($wpaicg_token_usage_client > $wpaicg_chat_total_token){
        $wpaicg_chat_limited = true;
        $wpaicg_tokens_chat_left = esc_html__('Out of Quota','gpt3-ai-content-generator');
    }
    else{
        $wpaicg_tokens_chat_left = $wpaicg_chat_total_token - $wpaicg_token_usage_client;
    }
}
?>
<style>
    <?php
    if(is_admin()):
    ?>
    .wpaicg_account_header{
        margin-top: 20px;
    }
    <?php
    endif;
    ?>
    .wpaicg_account_header{
        display: grid;
        grid-template-columns: repeat(4,1fr);
        grid-column-gap: 10px;
        grid-row-gap: 10px;
        grid-template-rows: auto auto;
    }
    .wpaicg_account_header_item{
        border: 1px solid #ccc;
        padding: 6px 12px;
        border-radius: 3px;
        font-size: 13px;
        background: rgb(0 0 0 / 4%);
    }
    .wpaicg_account_header_item span{}
    .wpaicg_account_header_item strong{}
    .wpaicg_account_logs_title{
        font-size: 16px;
        font-weight: bold;
        border-bottom: 1px solid #ccc;
        margin-bottom: 10px;
        padding-bottom: 10px;
    }
    .wpaicg_limited{
        color: #f00;
    }
    .wpaicg_avaiable_token{
        color: #1a881a;
    }
</style>
<div class="wpaicg_account_page">
    <div class="wpaicg_account_header">
        <div class="wpaicg_account_header_item">
            <span><?php echo esc_html__('AI Forms','gpt3-ai-content-generator')?>:</span>
            <strong class="<?php echo $wpaicg_form_limited ? 'wpaicg_limited':'wpaicg_avaiable_token'?>"><?php echo esc_html($wpaicg_tokens_forms_left)?></strong>
        </div>
        <div class="wpaicg_account_header_item">
            <span><?php echo esc_html__('Promptbase','gpt3-ai-content-generator')?>:</span>
            <strong class="<?php echo $wpaicg_promptbase_limited ? 'wpaicg_limited':'wpaicg_avaiable_token'?>"><?php echo esc_html($wpaicg_tokens_promptbase_left)?></strong>
        </div>
        <div class="wpaicg_account_header_item">
            <span><?php echo esc_html__('ChatGPT','gpt3-ai-content-generator')?>:</span>
            <strong class="<?php echo $wpaicg_chat_limited ? 'wpaicg_limited':'wpaicg_avaiable_token'?>"><?php echo esc_html($wpaicg_tokens_chat_left)?></strong>
        </div>
        <div class="wpaicg_account_header_item">
            <span><?php echo esc_html__('Image Generator','gpt3-ai-content-generator')?>:</span>
            <strong class="<?php echo $wpaicg_image_limited ? 'wpaicg_limited':'wpaicg_avaiable_token'?>"><?php echo $wpaicg_tokens_image_left != __('Unlimited','gpt3-ai-content-generator') && $wpaicg_tokens_image_left !== __('Out of Quota','gpt3-ai-content-generator') ? '$':''?><?php echo esc_html($wpaicg_tokens_image_left)?></strong>
        </div>
    </div>
    <div class="wpaicg_account_logs">
        <div class="wpaicg_account_logs_title"><?php echo esc_html__('Token Usage','gpt3-ai-content-generator')?></div>
        <?php
        if(is_admin()):
        ?>
        <table class="wp-list-table widefat fixed striped table-view-list posts">
        <?php
        else:
        ?>
        <table>
        <?php
        endif;
        ?>
            <thead>
                <tr>
                    <th><?php echo esc_html__('Module','gpt3-ai-content-generator')?></th>
                    <th><?php echo esc_html__('Token/Price','gpt3-ai-content-generator')?></th>
                    <th><?php echo esc_html__('Created At','gpt3-ai-content-generator')?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            if($wpaicg_logs && is_array($wpaicg_logs) && count($wpaicg_logs)){
                foreach ($wpaicg_logs as $wpaicg_log){
                    $wpaicg_moduleName = __('AI Forms','gpt3-ai-content-generator');
                    if($wpaicg_log->module == 'image'){
                        $wpaicg_moduleName = __('Image Generator','gpt3-ai-content-generator');
                    }
                    if($wpaicg_log->module == 'chat'){
                        $wpaicg_moduleName = __('ChatGPT','gpt3-ai-content-generator');
                    }
                    if($wpaicg_log->module == 'promptbase'){
                        $wpaicg_moduleName = __('Promptbase','gpt3-ai-content-generator');
                    }
                    if($wpaicg_log->module == 'image'){
                        $wpaicg_log->tokens = '$'.$wpaicg_log->tokens;
                    }
                    ?>
                    <tr>
                        <td><?php echo esc_html($wpaicg_moduleName)?></td>
                        <td><?php echo $wpaicg_log->tokens?></td>
                        <td><?php echo esc_html(date('d.m.Y H:i',$wpaicg_log->created_at))?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<?php
if ($wpaicg_totalPage > 1) {
    echo paginate_links(array(
        'base' => add_query_arg(array('wpage' => '%#%'), $current_url),
        'total' => $wpaicg_totalPage,
        'current' => $wpaicg_log_page,
        'format' => '?wpage=%#%',
        'show_all' => false,
        'prev_next' => false,
        'add_args' => false,
    ));
}

?>
