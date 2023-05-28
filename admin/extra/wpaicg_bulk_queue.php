<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
if(isset($_GET['sub_action']) && sanitize_text_field($_GET['sub_action']) == 'delete' && isset($_GET['id']) && !empty($_GET['id'])){
    $wpaicg_delete_id = sanitize_text_field($_GET['id']);
    if(!wp_verify_nonce($_GET['_wpnonce'], 'wpaicg_delete_'.$wpaicg_delete_id)){
        die(esc_html__('Nonce verification failed','gpt3-ai-content-generator'));
    }
    $wpdb->delete($wpdb->posts,array('post_type' => 'wpaicg_tracking', 'ID' => $wpaicg_delete_id));
    $wpdb->delete($wpdb->posts,array('post_type' => 'wpaicg_bulk', 'post_parent' => $wpaicg_delete_id));
    /*Search Twitter Bot*/
    $wpaicg_twitter_track = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE meta_key=%s AND meta_value=%d", 'wpaicg_twitter_track', $wpaicg_delete_id));
    if($wpaicg_twitter_track){
        delete_post_meta($wpaicg_twitter_track->post_id,'wpaicg_tweeted');
    }
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=tracking').'";</script>';
    exit;
}
if (isset($_GET['wpaicg_nonce']) && !wp_verify_nonce($_GET['wpaicg_nonce'], 'wpaicg_queue_search_nonce')) {
    die(WPAICG_NONCE_ERROR);
}

$search = isset($_GET['wsearch']) && !empty($_GET['wsearch']) ? sanitize_text_field($_GET['wsearch']) : '';
$wpaicg_tracking_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$wpaicg_tracking_per_page = 10;
$wpaicg_tracking_offset = ( $wpaicg_tracking_page * $wpaicg_tracking_per_page ) - $wpaicg_tracking_per_page;
$wpaicg_sum_length = "SELECT SUM(meta_value) FROM ".$wpdb->postmeta." l LEFT JOIN ".$wpdb->posts." lp ON lp.ID=l.post_id WHERE l.meta_key='_wpaicg_generator_length' AND lp.post_parent=p.ID";
$wpaicg_sum_time = "SELECT SUM(meta_value) FROM ".$wpdb->postmeta." l LEFT JOIN ".$wpdb->posts." lp ON lp.ID=l.post_id WHERE l.meta_key='_wpaicg_generator_run' AND lp.post_parent=p.ID";
$wpaicg_sum_tokens = "SELECT SUM(meta_value) FROM ".$wpdb->postmeta." l LEFT JOIN ".$wpdb->posts." lp ON lp.ID=l.post_id WHERE l.meta_key='_wpaicg_generator_token' AND lp.post_parent=p.ID";
$where = " (p.post_type='wpaicg_tracking' OR p.post_type='wpaicg_twitter') AND p.post_status IN ('publish','pending','draft','trash')";
if(!empty($search)){
    $where .= $wpdb->prepare(" AND p.post_title LIKE %s",'%'.$wpdb->esc_like($search).'%');
}
$wpaicg_trackings_sql = $wpdb->prepare("SELECT p.*,(".$wpaicg_sum_length.") as word_count,(".$wpaicg_sum_time.") as time_run,(".$wpaicg_sum_tokens.") as total_tokens FROM ".$wpdb->posts." p WHERE ".$where." ORDER BY p.post_date DESC LIMIT %d, %d", $wpaicg_tracking_offset, $wpaicg_tracking_per_page);
$wpaicg_trackings_total_sql = "SELECT COUNT(*) FROM ".$wpdb->posts." p WHERE ".$where;
$wpaicg_trackings = $wpdb->get_results($wpaicg_trackings_sql);
$wpaicg_trackings_total = $wpdb->get_var( $wpaicg_trackings_total_sql );
?>
<p>Bulk Tracking <a href="https://docs.aipower.org/docs/AutoGPT/auto-content-writer/bulk-editor#monitoring" target="_blank">?</a></p>
<form action="" method="get">
    <input type="hidden" name="page" value="wpaicg_bulk_content">
    <input type="hidden" name="wpaicg_action" value="tracking">
    <?php wp_nonce_field('wpaicg_queue_search_nonce', 'wpaicg_nonce'); ?>
    <div class="wpaicg-d-flex mb-5">
        <input style="width: 100%" value="<?php echo esc_html($search)?>" class="regular-text" name="wsearch" type="text" placeholder="<?php echo esc_html__('Type for search','gpt3-ai-content-generator')?>">
        <button class="button button-primary"><?php echo esc_html__('Search','gpt3-ai-content-generator')?></button>
    </div>
</form>
<table class="wp-list-table widefat fixed striped table-view-list comments">
    <thead>
    <tr>
        <th><?php echo esc_html__('Batch','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Status','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Source','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Duration','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Token','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Words Count','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Action','gpt3-ai-content-generator')?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($wpaicg_trackings && is_array($wpaicg_trackings) && count($wpaicg_trackings)):
        foreach($wpaicg_trackings as $wpaicg_tracking):
            ?>
            <tr>
                <td>
                    <?php
                    if($wpaicg_tracking->post_type == 'wpaicg_twitter'):
                        echo esc_html($wpaicg_tracking->post_title);
                    else:
                    ?>
                    <a href="<?php echo admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_track='.$wpaicg_tracking->ID)?>"><?php echo esc_html($wpaicg_tracking->post_title)?></a>
                    <?php
                    endif;
                    ?>
                </td>
                <td>
                    <?php
                    if($wpaicg_tracking->post_status == 'pending'){
                        echo esc_html__('Pending','gpt3-ai-content-generator');
                    }
                    if($wpaicg_tracking->post_status == 'publish'){
                        echo '<span style="color: #10922c">'.esc_html__('Completed','gpt3-ai-content-generator').'</span>';
                    }
                    if($wpaicg_tracking->post_status == 'draft'){
                        echo '<span style="color: #bb0505">'.esc_html__('Error','gpt3-ai-content-generator').'</span>';
                        if($wpaicg_tracking->post_type == 'wpaicg_twitter'){
                            echo '<p><small>'.esc_html($wpaicg_tracking->post_content).'</small></p>';
                        }
                    }
                    if($wpaicg_tracking->post_status == 'trash'){
                        echo '<span style="color: #bb0505">'.esc_html__('Cancelled','gpt3-ai-content-generator').'</span>';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if($wpaicg_tracking->post_type == 'wpaicg_twitter'){
                        echo esc_html__('Twitter','gpt3-ai-content-generator');
                    }
                    elseif(empty($wpaicg_tracking->post_mime_type) || $wpaicg_tracking->post_mime_type == 'editor'){
                        echo esc_html__('Bulk Editor','gpt3-ai-content-generator');
                    }
                    elseif($wpaicg_tracking->post_mime_type == 'csv'){
                        echo esc_html__('CSV','gpt3-ai-content-generator');
                    }
                    elseif($wpaicg_tracking->post_mime_type == 'rss'){
                        echo esc_html__('RSS','gpt3-ai-content-generator');
                    }
                    elseif($wpaicg_tracking->post_mime_type == 'sheets'){
                        echo esc_html__('Google Sheets','gpt3-ai-content-generator');
                    }
                    elseif($wpaicg_tracking->post_mime_type == 'multi'){
                        echo esc_html__('Copy-Paste','gpt3-ai-content-generator');
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if($wpaicg_tracking->post_type == 'wpaicg_twitter'){
                        $wpaicg_twitter_duration = get_post_meta($wpaicg_tracking->ID,'wpaicg_twitter_duration',true);
                        echo !empty($wpaicg_twitter_duration) ? esc_html($this->wpaicg_seconds_to_time((int)$wpaicg_twitter_duration)) : '';
                    }
                    else {
                        echo !empty($wpaicg_tracking->time_run) ? esc_html($this->wpaicg_seconds_to_time((int)$wpaicg_tracking->time_run)) : '';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if($wpaicg_tracking->post_type == 'wpaicg_twitter'){
                        $wpaicg_twitter_tokens = get_post_meta($wpaicg_tracking->ID,'wpaicg_twitter_tokens',true);
                        echo esc_html($wpaicg_twitter_tokens);
                    }
                    else {
                        echo esc_html($wpaicg_tracking->total_tokens);
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if($wpaicg_tracking->post_type == 'wpaicg_twitter'){
                        $wpaicg_twitter_length = get_post_meta($wpaicg_tracking->ID,'wpaicg_twitter_length',true);
                        echo esc_html($wpaicg_twitter_length);
                    }
                    else {
                        echo esc_html($wpaicg_tracking->word_count);
                    }
                    ?>
                </td>
                <td><a onclick="return confirm('<?php echo esc_html__('Are you sure?','gpt3-ai-content-generator')?>')" class="button button-link-delete button-small" href="<?php echo wp_nonce_url(admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=tracking&sub_action=delete&id='.$wpaicg_tracking->ID), 'wpaicg_delete_'.$wpaicg_tracking->ID)?>"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></a></td>
            </tr>
        <?php
        endforeach;
    endif;
    ?>
    </tbody>
</table>
<div class="wpaicg-paginate">
    <?php
    echo paginate_links( array(
        'base'         => admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=tracking&wpage=%#%'),
        'total'        => ceil($wpaicg_trackings_total / $wpaicg_tracking_per_page),
        'current'      => $wpaicg_tracking_page,
        'format'       => '?wpaged=%#%',
        'show_all'     => false,
        'prev_next'    => false,
        'add_args'     => false,
    ));
    ?>
</div>
