<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
if(isset($_GET['sub_action']) && sanitize_text_field($_GET['sub_action']) == 'delete' && isset($_GET['id']) && !empty($_GET['id'])){
    $wpaicg_delete_id = sanitize_text_field($_GET['id']);
    if(!wp_verify_nonce($_GET['_wpnonce'], 'wpaicg_delete_'.$wpaicg_delete_id)){
        die(WPAICG_NONCE_ERROR);
    }
    $wpdb->delete($wpdb->posts,array('post_type' => 'wpaicg_bulk', 'ID' => $wpaicg_delete_id));
    /*Check if empty*/
    $wpaicg_bulks = get_posts(array('post_type' => 'wpaicg_bulk','post_status' => array('publish','pending','draft','trash','inherit'),'post_parent' => $wpaicg_track_id,'posts_per_page' => -1));
    if(!$wpaicg_bulks || !is_array($wpaicg_bulks)){
        $wpdb->delete($wpdb->posts,array('post_type' => 'wpaicg_tracking', 'ID' => $wpaicg_track_id));
        echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=tracking').'";</script>';
        exit;
    }
    else{
        // mark complete
        $wpaicg_bulks = get_posts(array('post_type' => 'wpaicg_bulk','post_status' => array('pending','draft'),'post_parent' => $wpaicg_track_id,'posts_per_page' => -1));
        if(!$wpaicg_bulks || !is_array($wpaicg_bulks)){
            wp_update_post(array(
                'ID' => $wpaicg_track_id,
                'post_status' => 'publish'
            ));
        }

    }
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_track='.$wpaicg_track_id).'";</script>';
    exit;
}
$wpaicg_bulks = get_posts(array('post_type' => 'wpaicg_bulk','post_status' => array('publish','pending','draft','trash','inherit'),'post_parent' => $wpaicg_track_id,'posts_per_page' => -1));
$wpaicg_bulk_completed = true;
if($wpaicg_bulks && is_array($wpaicg_bulks)):
// create nonce
$wpaicg_nonce = wp_create_nonce('wpaicg_nonce_action');
?>
<style>
    .wpaicg-bulk-track-status, .wpaicg-bulk-item-span{
        width: 120px;
    }
</style>
    <div id="wpaicg-bulk-track">
    <input type="hidden" id="wpaicg_nonce" name="wpaicg_nonce" value="<?php echo esc_attr($wpaicg_nonce); ?>">
        <h2><?php echo esc_html__('Auto Content Writer','gpt3-ai-content-generator')?></h2>
        <p style="padding: 6px 12px;border: 1px solid green;border-radius: 3px;background: lightgreen;"><?php echo esc_html__('You can leave this page and return at a later time.','gpt3-ai-content-generator')?></p>
        <table class="wp-list-table widefat fixed striped table-view-list comments">
            <thead>
            <tr>
                <th><?php echo esc_html__('Title','gpt3-ai-content-generator')?></th>
                <th><?php echo esc_html__('Status','gpt3-ai-content-generator')?></th>
                <th><?php echo esc_html__('Category','gpt3-ai-content-generator')?></th>
                <th><?php echo esc_html__('Duration','gpt3-ai-content-generator')?></th>
                <th><?php echo esc_html__('Token','gpt3-ai-content-generator')?></th>
                <th><?php echo esc_html__('Words Count','gpt3-ai-content-generator')?></th>
                <th><?php echo esc_html__('Estimated','gpt3-ai-content-generator')?></th>
                <th><?php echo esc_html__('Model','gpt3-ai-content-generator')?></th>
                <th><?php echo esc_html__('Action','gpt3-ai-content-generator')?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($wpaicg_bulks as $key=>$wpaicg_bulk):
                if($wpaicg_bulk->post_status == 'pending' || $wpaicg_bulk->post_status == 'draft'){
                    $wpaicg_bulk_completed = false;
                }
                $wpaicg_generator_run = get_post_meta($wpaicg_bulk->ID,'_wpaicg_generator_run',true);
                $wpaicg_generator_length = get_post_meta($wpaicg_bulk->ID,'_wpaicg_generator_length',true);
                $wpaicg_generator_token = get_post_meta($wpaicg_bulk->ID,'_wpaicg_generator_token',true);
                $wpaicg_generator_post = get_post_meta($wpaicg_bulk->ID,'_wpaicg_generator_post',true);
                $wpaicg_ai_model = get_post_meta($wpaicg_bulk->ID,'wpaicg_ai_model',true);
            ?>
            <tr class="wpaicg-bulk-item-<?php echo esc_html($wpaicg_bulk->ID)?>">
                <td class="wpaicg-bulk-item-title">
                    <?php
                    if(!empty($wpaicg_generator_post)){
                        echo '<a href="'.admin_url('post.php?post='.esc_html($wpaicg_generator_post).'&action=edit').'">';
                    }
                    ?>
                    <strong><?php echo esc_html($wpaicg_bulk->post_title)?></strong>
                    <?php
                    if(!empty($wpaicg_generator_post)){
                        echo '</a>';
                    }
                    ?>
                </td>
                <td data-id="<?php echo esc_html($wpaicg_bulk->ID)?>" class="wpaicg-bulk-track-status wpaicg-bulk-track-<?php echo esc_html($wpaicg_bulk->post_status);?>">
                    <?php
                    if($wpaicg_bulk->post_status == 'pending'){
                        echo esc_html__('Pending..','gpt3-ai-content-generator');
                    }
                    if($wpaicg_bulk->post_status == 'draft'){
                        echo esc_html__('In progress..','gpt3-ai-content-generator');
                    }
                    if($wpaicg_bulk->post_status == 'publish'){
                        echo esc_html__('Completed','gpt3-ai-content-generator');
                    }
                    if($wpaicg_bulk->post_status == 'inherit'){
                        echo esc_html__('Cancelled','gpt3-ai-content-generator');
                    }
                    if($wpaicg_bulk->post_status == 'trash'){
                        echo esc_html__('Error: ','gpt3-ai-content-generator').esc_html(get_post_meta($wpaicg_bulk->ID,'_wpaicg_error',true));
                    }
                    ?>
                </td>
                <td><?php echo $wpaicg_bulk->menu_order && $wpaicg_bulk->menu_order > 0 ? esc_html(get_term($wpaicg_bulk->menu_order)->name) : '--'?></td>
                <td class="wpaicg-bulk-item-duration"><?php echo !empty($wpaicg_generator_run) ? esc_html($this->wpaicg_seconds_to_time((int)$wpaicg_generator_run)): ''?></td>
                <td class="wpaicg-bulk-item-token"><?php echo esc_html($wpaicg_generator_token)?></td>
                <td class="wpaicg-bulk-item-word"><?php echo esc_html($wpaicg_generator_length)?></td>
                <td class="wpaicg-bulk-item-cost">
                    <?php
                    if(!empty($wpaicg_generator_token)) {
                        if ($wpaicg_ai_model == 'gpt-3.5-turbo') {
                            echo '$'.esc_html(number_format($wpaicg_generator_token*0.002/1000,5));
                        }
                        elseif ($wpaicg_ai_model == 'gpt-4') {
                            echo '$'.esc_html(number_format($wpaicg_generator_token*0.06/1000,5));
                        }
                        elseif ($wpaicg_ai_model == 'gpt-4-32k') {
                            echo '$'.esc_html(number_format($wpaicg_generator_token*0.12/1000,5));
                        }
                        else{
                            echo '$'.esc_html(number_format($wpaicg_generator_token*$this->wpaicg_token_price,5));
                        }
                    }
                    ?>
                </td>
                <td><?php echo esc_html($wpaicg_ai_model);?></td>
                <td><a onclick="return confirm('<?php echo esc_html__('Are you sure?','gpt3-ai-content-generator')?>')" class="button button-link-delete button-small" href="<?php echo wp_nonce_url(admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_track='.$wpaicg_track_id.'&sub_action=delete&id='.$wpaicg_bulk->ID),'wpaicg_delete_'.$wpaicg_bulk->ID)?>"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></a></td>
            </tr>
            <?php
            endforeach;
            ?>
            </tbody>
        </table>
        <?php
        if(!$wpaicg_bulk_completed):
            ?>
            <p><button class="button wpaicg-bulk-button wpaicg-bulk-button-cancel"><?php echo esc_html__('Cancel All','gpt3-ai-content-generator')?></button></p>
        <?php
        endif;
        ?>
    </div>
    <script>
        jQuery(document).ready(function ($){
            var wpaicg_track_ids = <?php echo json_encode(wp_list_pluck($wpaicg_bulks,'ID'))?>;
            var wpaicg_Tracking = false;
            if(wpaicg_track_ids.length){
                wpaicg_Tracking = setInterval(function(){
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php')?>',
                        data: {
                            action: 'wpaicg_bulk_status',
                            ids: wpaicg_track_ids,
                            wpaicg_nonce: $('#wpaicg_nonce').val()
                        },
                        dataType: 'JSON',
                        type: 'POST',
                        success: function (res){
                            if(res.status === 'success' && res.data.length){
                                var wpaicgCompletedAll = true;
                                $.each(res.data, function(idx, item){
                                    var wpaicg_item = $('.wpaicg-bulk-item-'+item.id);
                                    var wpaicg_item_status = wpaicg_item.find('.wpaicg-bulk-track-status');
                                    wpaicg_item_status.removeClass();
                                    wpaicg_item_status.addClass('wpaicg-bulk-track-status');
                                    wpaicg_item_status.addClass('wpaicg-bulk-track-'+item.status);
                                    if(item.status === 'pending'){
                                        wpaicgCompletedAll = false;
                                        wpaicg_item_status.html('<?php echo esc_html__('Pending..','gpt3-ai-content-generator')?>');
                                    }
                                    if(item.status === 'draft'){
                                        wpaicgCompletedAll = false;
                                        wpaicg_item_status.html('<?php echo esc_html__('In progress..','gpt3-ai-content-generator')?>');
                                    }
                                    if(item.status === 'inherit'){
                                        wpaicgCompletedAll = false;
                                        wpaicg_item_status.html('<?php echo esc_html__('Cancelled','gpt3-ai-content-generator')?>');
                                    }
                                    if(item.status === 'trash'){
                                        wpaicgCompletedAll = false;
                                        wpaicg_item_status.html('<?php echo esc_html__('Error: ','gpt3-ai-content-generator')?>'+item.msg);
                                    }
                                    if(item.status === 'publish'){
                                        wpaicg_item_status.html('<?php echo esc_html__('Completed','gpt3-ai-content-generator')?>');
                                        if(item.duration !== undefined){
                                            wpaicg_item.find('.wpaicg-bulk-item-duration').html(item.duration);
                                        }
                                        if(item.token !== undefined){
                                            wpaicg_item.find('.wpaicg-bulk-item-token').html(item.token);
                                        }
                                        if(item.word !== undefined){
                                            wpaicg_item.find('.wpaicg-bulk-item-word').html(item.word);
                                        }
                                        if(item.cost !== undefined){
                                            wpaicg_item.find('.wpaicg-bulk-item-cost').html(item.cost);
                                        }
                                    }
                                    if(item.url !== ''){
                                        wpaicg_item.find('.wpaicg-bulk-item-title').html('<a href="'+item.url+'"><strong>'+item.title+'</strong></a>');
                                    }
                                });
                                if(wpaicgCompletedAll){
                                    $('.wpaicg-bulk-button-cancel').hide();
                                }
                            }
                        }
                    })
                }, 5000);
                $('.wpaicg-bulk-button-cancel').click(function (){
                    var wpaicg_track_cancel = [];
                    $('.wpaicg-bulk-track-status:not(.wpaicg-bulk-track-publish)').each(function (idx, item){
                        var wpaicg_id = $(item).attr('data-id');
                        $(item).removeClass();
                        $(item).addClass('wpaicg-bulk-track-status');
                        $(item).addClass('wpaicg-bulk-track-inherit');
                        $(item).html('Cancelled');
                        wpaicg_track_cancel.push(wpaicg_id);
                    });
                    if(!wpaicg_Tracking){
                        clearInterval(wpaicg_Tracking);
                    }
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php')?>',
                        data: {
                            action: 'wpaicg_bulk_cancel',
                            ids: wpaicg_track_cancel,
                            wpaicg_nonce: $('#wpaicg_nonce').val()
                        },
                        dataType: 'JSON',
                        type: 'POST',
                        success: function (res){

                        }
                    });
                    $(this).hide();
                })
            }
        })
    </script>
<?php
else:
    ?>
    <script>window.location.href = '<?php echo admin_url('admin.php?page=wpaicg_bulk_content')?>';</script>
<?php
endif;
