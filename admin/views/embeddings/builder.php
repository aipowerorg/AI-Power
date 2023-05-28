<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$search = isset($_GET['wsearch']) && !empty($_GET['wsearch']) ? sanitize_text_field($_GET['wsearch']) : '';
$wpaicg_sub_action = isset($_GET['sub']) && !empty($_GET['sub']) ? sanitize_text_field($_GET['sub']) : false;
if($wpaicg_sub_action == 'deleteall'){
    $wpdb->query("DELETE FROM ".$wpdb->postmeta." WHERE meta_key IN ('wpaicg_indexed','wpaicg_source','wpaicg_parent','wpaicg_error_msg')");
    $wpaicg_embeddings = $wpdb->get_results("SELECT ID FROM ".$wpdb->posts." WHERE post_type='wpaicg_builder'");
    if($wpaicg_embeddings && count($wpaicg_embeddings)) {
        $wpaicg_embedding_ids = wp_list_pluck($wpaicg_embeddings,'ID');
        WPAICG\WPAICG_Embeddings::get_instance()->wpaicg_delete_embeddings_ids($wpaicg_embedding_ids);
    }
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_embeddings&action=builder').'";</script>';
    exit;
}
if($wpaicg_sub_action == 'reindexall'){
    $wpaicg_embeddings = $wpdb->get_results("SELECT ID FROM ".$wpdb->posts." WHERE post_type='wpaicg_builder'");
    if($wpaicg_embeddings && count($wpaicg_embeddings)) {
        foreach($wpaicg_embeddings as $wpaicg_embedding){
            $parent_id = get_post_meta($wpaicg_embedding->ID,'wpaicg_parent',true);
            if($parent_id && get_post($parent_id)){
                update_post_meta($wpaicg_embedding->ID,'wpaicg_indexed','reindex');
                update_post_meta($parent_id,'wpaicg_indexed','reindex');
            }
        }
    }
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_embeddings&action=builder').'";</script>';
    exit;
}
$wpaicg_builder_sub = isset($_GET['sub']) && !empty($_GET['sub']) ? sanitize_text_field($_GET['sub']) : false;
$wpaicg_builder_types = get_option('wpaicg_builder_types',[]);
$wpaicg_builder_enable = get_option('wpaicg_builder_enable','');
$wpaicg_total_indexed = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->posts." p LEFT JOIN ".$wpdb->postmeta." m ON m.post_id = p.ID WHERE p.post_type='wpaicg_builder' AND m.meta_key='wpaicg_indexed' AND m.meta_value='yes'");
$wpaicg_total_errors = array();
$wpaicg_total_skips = array();
if($wpaicg_builder_types && is_array($wpaicg_builder_types) && count($wpaicg_builder_types)) {
    $ids = implode("','",$wpaicg_builder_types);
    $commaDelimitedPlaceholders = implode(',', array_fill(0, count($wpaicg_builder_types), '%s'));

    if($wpaicg_builder_sub == 'errors' && !empty($search)){
        $wpaicg_total_errors = $wpdb->get_results($wpdb->prepare("SELECT p.ID,p.post_title FROM " . $wpdb->posts . " p LEFT JOIN " . $wpdb->postmeta . " m ON m.post_id = p.ID WHERE p.post_type IN ($commaDelimitedPlaceholders) AND m.meta_key='wpaicg_indexed' AND m.meta_value='error'",$wpaicg_builder_types)." AND p.post_title LIKE '%".$wpdb->esc_like($search)."%'");
    }
    else{
        $wpaicg_total_errors = $wpdb->get_results($wpdb->prepare("SELECT p.ID,p.post_title FROM " . $wpdb->posts . " p LEFT JOIN " . $wpdb->postmeta . " m ON m.post_id = p.ID WHERE p.post_type IN ($commaDelimitedPlaceholders) AND m.meta_key='wpaicg_indexed' AND m.meta_value='error'",$wpaicg_builder_types));
    }
    if($wpaicg_builder_sub == 'skip' && !empty($search)){
        $wpaicg_total_skips = $wpdb->get_results($wpdb->prepare("SELECT p.ID,p.post_title FROM " . $wpdb->posts . " p LEFT JOIN " . $wpdb->postmeta . " m ON m.post_id = p.ID WHERE p.post_type IN ($commaDelimitedPlaceholders) AND m.meta_key='wpaicg_indexed' AND m.meta_value='skip'",$wpaicg_builder_types)." AND p.post_title LIKE '%".$wpdb->esc_like($search)."%'");
    }
    else{
        $wpaicg_total_skips = $wpdb->get_results($wpdb->prepare("SELECT p.ID,p.post_title FROM " . $wpdb->posts . " p LEFT JOIN " . $wpdb->postmeta . " m ON m.post_id = p.ID WHERE p.post_type IN ($commaDelimitedPlaceholders) AND m.meta_key='wpaicg_indexed' AND m.meta_value='skip'",$wpaicg_builder_types));
    }
}
?>
<style>
    .wpaicg_modal{
        top: 5%;
        height: 90%;
        position: relative;
    }
    .wpaicg_modal_content{
        max-height: calc(100% - 103px);
        overflow-y: auto;
    }
    .wpaicg-builder-process{
        margin-bottom: 10px;
    }
    .wpaicg-builder-process-content{
        height: 20px;
        width: 100%;
        background: #dbdbdb;
        border-radius: 4px;
        position: relative;
        overflow: hidden;
    }
    .wpaicg-percent{
        position: absolute;
        display: block;
        height: 20px;
        background: #0d969d;
    }
    .wpaicg-numbers{}
    .wp-core-ui .button.wpaicg-danger-btn{
        background: #c90000;
        color: #fff;
        border-color: #cb0000;
    }
</style>
<?php
if(!$wpaicg_builder_sub){
    include __DIR__.'/builder_index.php';
}
if($wpaicg_builder_sub == 'errors'){
    include __DIR__.'/builder_errors.php';
}
if($wpaicg_builder_sub == 'skip'){
    include __DIR__.'/builder_skip.php';
}
?>
