<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$wpaicg_files_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$wpaicg_files_per_page = 10;
$wpaicg_files_offset = ( $wpaicg_files_page * $wpaicg_files_per_page ) - $wpaicg_files_per_page;
$wpaicg_files_count_sql = "SELECT COUNT(*) FROM ".$wpdb->posts." f WHERE f.post_type='wpaicg_finetune' AND (f.post_status='publish' OR f.post_status = 'future')";
$wpaicg_files_sql = $wpdb->prepare("SELECT f.*
       ,(SELECT fn.meta_value FROM ".$wpdb->postmeta." fn WHERE fn.post_id=f.ID AND fn.meta_key='wpaicg_model' LIMIT 1) as model 
       ,(SELECT fp.meta_value FROM ".$wpdb->postmeta." fp WHERE fp.post_id=f.ID AND fp.meta_key='wpaicg_updated_at' LIMIT 1) as updated_at 
       ,(SELECT fm.meta_value FROM ".$wpdb->postmeta." fm WHERE fm.post_id=f.ID AND fm.meta_key='wpaicg_name' LIMIT 1) as ft_model 
       ,(SELECT fc.meta_value FROM ".$wpdb->postmeta." fc WHERE fc.post_id=f.ID AND fc.meta_key='wpaicg_org' LIMIT 1) as org_id 
       ,(SELECT fs.meta_value FROM ".$wpdb->postmeta." fs WHERE fs.post_id=f.ID AND fs.meta_key='wpaicg_status' LIMIT 1) as ft_status 
       ,(SELECT ft.meta_value FROM ".$wpdb->postmeta." ft WHERE ft.post_id=f.ID AND ft.meta_key='wpaicg_fine_tune' LIMIT 1) as finetune 
       ,(SELECT fd.meta_value FROM ".$wpdb->postmeta." fd WHERE fd.post_id=f.ID AND fd.meta_key='wpaicg_deleted' LIMIT 1) as deleted 
       FROM ".$wpdb->posts." f WHERE f.post_type='wpaicg_finetune' AND (f.post_status='publish' OR f.post_status = 'future') ORDER BY f.post_date DESC LIMIT %d,%d",$wpaicg_files_offset,$wpaicg_files_per_page);
$wpaicg_files = $wpdb->get_results($wpaicg_files_sql);
$wpaicg_files_total = $wpdb->get_var( $wpaicg_files_count_sql );
?>
<style>
    .wpaicg_delete_finetune,.wpaicg_cancel_finetune{
        color: #bb0505;
    }
</style>
<h1 class="wp-heading-inline"><?php echo esc_html__('Fine-tunes','gpt3-ai-content-generator')?></h1>
<button href="javascript:void(0)" class="page-title-action wpaicg_sync_finetunes"><?php echo esc_html__('Sync Fine-tunes','gpt3-ai-content-generator')?></button>
<table class="wp-list-table widefat fixed striped table-view-list comments">
    <thead>
    <tr>
        <th><?php echo esc_html__('ID','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Object','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Model','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Created At','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('FT Model','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Org ID','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Status','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Updated','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Training','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Action','gpt3-ai-content-generator')?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($wpaicg_files && is_array($wpaicg_files) && count($wpaicg_files)):
        foreach($wpaicg_files as $wpaicg_file):
            ?>
        <tr>
            <td><?php echo esc_html($wpaicg_file->post_title);?></td>
            <td>fine-tune</td>
            <td><?php echo esc_html($wpaicg_file->model);?></td>
            <td><?php echo esc_html($wpaicg_file->post_date);?></td>
            <td><?php echo esc_html($wpaicg_file->ft_model);?></td>
            <td><?php echo esc_html($wpaicg_file->org_id);?></td>
            <td class="wpaicg-finetune-<?php echo !$wpaicg_file->deleted ? esc_html($wpaicg_file->ft_status) : 'deleted';?>"><?php echo !$wpaicg_file->deleted ? esc_html($wpaicg_file->ft_status) : 'Deleted';?></td>
            <td><?php echo esc_html($wpaicg_file->updated_at);?></td>
            <td>
                <a class="wpaicg_get_other button button-small" data-type="events"  data-id="<?php echo esc_html($wpaicg_file->ID);?>" href="javascript:void(0)"><?php echo esc_html__('Events','gpt3-ai-content-generator')?></a><br>
                <a class="wpaicg_get_other button button-small mb-5" data-id="<?php echo esc_html($wpaicg_file->ID);?>" data-type="hyperparams" href="javascript:void(0)"><?php echo esc_html__('Hyper-params','gpt3-ai-content-generator')?></a><br>
                <a class="wpaicg_get_other button button-small mb-5" data-id="<?php echo esc_html($wpaicg_file->ID);?>" data-type="result_files" href="javascript:void(0)"><?php echo esc_html__('Result files','gpt3-ai-content-generator')?></a><br>
                <a class="wpaicg_get_other button button-small mb-5" data-id="<?php echo esc_html($wpaicg_file->ID);?>" data-type="training_files" href="javascript:void(0)"><?php echo esc_html__('Training-files','gpt3-ai-content-generator')?></a><br>
            </td>
            <td>
                <?php
                if(!$wpaicg_file->deleted):
                    if($wpaicg_file->ft_status == 'pending'):
                    ?>
                <a class="wpaicg_cancel_finetune button button-small button-link-delete" data-id="<?php echo esc_html($wpaicg_file->ID);?>" href="javascript:void(0)"><?php echo esc_html__('Cancel','gpt3-ai-content-generator')?></a><br>
                <?php
                    endif;
                    if(!empty($wpaicg_file->ft_model)):
                ?>
                <a class="wpaicg_delete_finetune button button-small button-link-delete" data-id="<?php echo esc_html($wpaicg_file->ID);?>" href="javascript:void(0)"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></a><br>
                <?php
                    endif;
                endif;
                ?>
            </td>
        </tr>
            <?php
        endforeach;
    endif;
    ?>
    </tbody>
</table>
<div class="wpaicg-paginate mb-5">
    <?php
    echo paginate_links( array(
        'base'         => admin_url('admin.php?page=wpaicg_finetune&action=fine-tunes&wpage=%#%'),
        'total'        => ceil($wpaicg_files_total / $wpaicg_files_per_page),
        'current'      => $wpaicg_files_page,
        'format'       => '?wpaged=%#%',
        'show_all'     => false,
        'prev_next'    => false,
        'add_args'     => false,
    ));
    ?>
</div>
<script>
    jQuery(document).ready(function ($){
        var wpaicgAjaxRunning = false;
        $('.wpaicg_modal_close').click(function (){
            $('.wpaicg_modal_close').closest('.wpaicg_modal').hide();
            $('.wpaicg-overlay').hide();
        })
        function wpaicgLoading(btn){
            btn.attr('disabled','disabled');
            if(btn.find('.spinner').length === 0){
                btn.append('<span class="wpaicg-spinner spinner"></span>');
            }
            btn.find('.spinner').css('visibility','unset');
        }
        function wpaicgRmLoading(btn){
            btn.removeAttr('disabled');
            btn.find('.spinner').remove();
        }
        var wpaicg_get_other = $('.wpaicg_get_other');
        var wpaicg_get_finetune = $('.wpaicg_get_finetune');
        var wpaicg_cancel_finetune = $('.wpaicg_cancel_finetune');
        var wpaicg_delete_finetune = $('.wpaicg_delete_finetune');
        var wpaicg_ajax_url = '<?php echo admin_url('admin-ajax.php')?>';
        wpaicg_cancel_finetune.click(function (){
            var conf = confirm('<?php echo esc_html__('Are you sure?','gpt3-ai-content-generator')?>');
            if(conf) {
                var btn = $(this);
                var id = btn.attr('data-id');
                if (!wpaicgAjaxRunning) {
                    wpaicgAjaxRunning = true;
                    $.ajax({
                        url: wpaicg_ajax_url,
                        data: {action: 'wpaicg_cancel_finetune', id: id,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {
                            wpaicgLoading(btn);
                        },
                        success: function (res) {
                            wpaicgRmLoading(btn);
                            wpaicgAjaxRunning = false;
                            if (res.status === 'success') {
                                window.location.reload();
                            } else {
                                alert(res.msg);
                            }
                        },
                        error: function () {
                            wpaicgRmLoading(btn);
                            wpaicgAjaxRunning = false;
                            alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                        }
                    })
                }
            }
        });
        wpaicg_delete_finetune.click(function (){
            var conf = confirm('<?php echo esc_html__('Are you sure?','gpt3-ai-content-generator')?>');
            if(conf) {
                var btn = $(this);
                var id = btn.attr('data-id');
                if (!wpaicgAjaxRunning) {
                    wpaicgAjaxRunning = true;
                    $.ajax({
                        url: wpaicg_ajax_url,
                        data: {action: 'wpaicg_delete_finetune', id: id,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {
                            wpaicgLoading(btn);
                        },
                        success: function (res) {
                            wpaicgRmLoading(btn);
                            wpaicgAjaxRunning = false;
                            if (res.status === 'success') {
                                window.location.reload();
                            } else {
                                alert(res.msg);
                            }
                        },
                        error: function () {
                            wpaicgRmLoading(btn);
                            wpaicgAjaxRunning = false;
                            alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                        }
                    })
                }
            }
        });
        wpaicg_get_other.click(function (){
            var btn = $(this);
            var id = btn.attr('data-id');
            var type = btn.attr('data-type');
            var wpaicgTitle = btn.text().trim();
            if(!wpaicgAjaxRunning){
                wpaicgAjaxRunning = true;
                $.ajax({
                    url: wpaicg_ajax_url,
                    data: {action: 'wpaicg_other_finetune', id: id, type: type,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function (){
                        wpaicgLoading(btn);
                    },
                    success: function (res){
                        wpaicgRmLoading(btn);
                        wpaicgAjaxRunning = false;
                        if(res.status === 'success'){
                            $('.wpaicg_modal_title').html(wpaicgTitle);
                            $('.wpaicg_modal_content').html(res.html);
                            $('.wpaicg-overlay').show();
                            $('.wpaicg_modal').show();
                        }
                        else{
                            alert(res.msg);
                        }
                    },
                    error: function (){
                        wpaicgRmLoading(btn);
                        wpaicgAjaxRunning = false;
                        alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                    }
                })
            }
        })
        $('.wpaicg_sync_finetunes').click(function (){
            var btn = $(this);
            $.ajax({
                url: wpaicg_ajax_url,
                data: {action: 'wpaicg_fetch_finetunes','nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                dataType: 'JSON',
                type: 'POST',
                beforeSend: function (){
                    wpaicgLoading(btn);
                },
                success: function (res){
                    wpaicgRmLoading(btn);
                    if(res.status === 'success'){
                        window.location.reload();
                    }
                    else{
                        alert(res.msg);
                    }
                },
                error: function (){
                    wpaicgRmLoading(btn);
                    alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                }
            })
        })
    })
</script>
