<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_action = isset($_GET['action']) && !empty($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
$checkRole = \WPAICG\wpaicg_roles()->user_can('wpaicg_embeddings',empty($wpaicg_action) ? 'content' : $wpaicg_action);
if($checkRole){
    echo '<script>window.location.href="'.$checkRole.'"</script>';
    exit;
}
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
if(empty($wpaicg_pinecone_api) && empty($wpaicg_pinecone_environment) && $wpaicg_action != 'settings'){
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_embeddings&action=settings').'"</script>';
    exit;
}
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
        if(empty($wpaicg_action)){
            $wpaicg_action = 'content';
        }
        \WPAICG\wpaicg_util_core()->wpaicg_tabs('wpaicg_embeddings', array(
            'content' => esc_html__('Content Builder','gpt3-ai-content-generator'),
            'logs' => esc_html__('Entries','gpt3-ai-content-generator'),
            'pdf' => esc_html__('PDF','gpt3-ai-content-generator'),
            'builder' => esc_html__('Index Builder','gpt3-ai-content-generator'),
            'settings' => esc_html__('Settings','gpt3-ai-content-generator'),
            'troubleshoot' => esc_html__('Troubleshoot','gpt3-ai-content-generator'),
        ), $wpaicg_action);
        if(!$wpaicg_action || $wpaicg_action == 'content'){
            $wpaicg_action = '';
        }
        ?>
    </h2>
</div>
<div id="poststuff">
<?php
if(empty($wpaicg_action)){
    include __DIR__.'/entries.php';
}
elseif($wpaicg_action == 'logs'){
    include __DIR__.'/logs.php';
}
elseif($wpaicg_action == 'settings'){
    include __DIR__.'/settings.php';
}
elseif($wpaicg_action == 'builder'){
    include __DIR__.'/builder.php';
}
elseif($wpaicg_action == 'pdf') {
    if (\WPAICG\wpaicg_util_core()->wpaicg_is_pro()) {
        include WPAICG_PLUGIN_DIR . 'lib/views/pdf/index.php';
    } else {
        include __DIR__ . '/pdf.php';
    }
}
elseif($wpaicg_action == 'troubleshoot'){
    include __DIR__.'/troubleshoot.php';
}
?>
</div>
