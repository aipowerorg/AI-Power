<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$wpaicg_files_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$wpaicg_files_per_page = 20;
$wpaicg_files_offset = ( $wpaicg_files_page * $wpaicg_files_per_page ) - $wpaicg_files_per_page;
$wpaicg_files_count_sql = "SELECT COUNT(*) FROM ".$wpdb->posts." f WHERE f.post_type='wpaicg_file' AND (f.post_status='publish' OR f.post_status = 'future')";
$wpaicg_files_sql = $wpdb->prepare("SELECT f.*
       ,(SELECT fn.meta_value FROM ".$wpdb->postmeta." fn WHERE fn.post_id=f.ID AND fn.meta_key='wpaicg_filename') as filename 
       ,(SELECT fp.meta_value FROM ".$wpdb->postmeta." fp WHERE fp.post_id=f.ID AND fp.meta_key='wpaicg_purpose') as purpose 
       ,(SELECT fm.meta_value FROM ".$wpdb->postmeta." fm WHERE fm.post_id=f.ID AND fm.meta_key='wpaicg_purpose') as model 
       ,(SELECT fc.meta_value FROM ".$wpdb->postmeta." fc WHERE fc.post_id=f.ID AND fc.meta_key='wpaicg_custom_name') as custom_name 
       ,(SELECT fs.meta_value FROM ".$wpdb->postmeta." fs WHERE fs.post_id=f.ID AND fs.meta_key='wpaicg_file_size') as file_size 
       ,(SELECT ft.meta_value FROM ".$wpdb->postmeta." ft WHERE ft.post_id=f.ID AND ft.meta_key='wpaicg_fine_tune') as finetune 
       FROM ".$wpdb->posts." f WHERE f.post_type='wpaicg_file' AND (f.post_status='publish' OR f.post_status = 'future') ORDER BY f.post_date DESC LIMIT %d,%d",$wpaicg_files_offset,$wpaicg_files_per_page);
$wpaicg_files = $wpdb->get_results($wpaicg_files_sql);
$wpaicg_files_total = $wpdb->get_var( $wpaicg_files_count_sql );
$fileTypes = array(
    'fine-tune' => esc_html__('Fine-Tune','gpt3-ai-content-generator')
);
?>
<style>
    .wpaicg_form_upload_file{
        background: #e3e3e3;
        padding: 10px;
        border-radius: 4px;
        border: 1px solid #ccc;
        margin-bottom: 20px;
    }
    .wpaicg_form_upload_file table{
        max-width: 500px
    }
    .wpaicg_form_upload_file table th{
        padding: 5px;
    }
    .wpaicg_form_upload_file table td{
        padding: 5px;
    }
</style>
<?php
$wpaicgMaxFileSize = wp_max_upload_size();
if($wpaicgMaxFileSize > 104857600){
    $wpaicgMaxFileSize = 104857600;
}
?>
<h1 class="wp-heading-inline"><?php echo esc_html__('Files','gpt3-ai-content-generator')?></h1>
<button href="javascript:void(0)" class="page-title-action wpaicg_sync_files"><?php echo esc_html__('Sync Files','gpt3-ai-content-generator')?></button>
<table class="wp-list-table widefat fixed striped table-view-list comments">
    <thead>
    <tr>
        <th><?php echo esc_html__('ID','gpt3-ai-content-generator')?></th>
        <th style="width: 50px;"><?php echo esc_html__('Size','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Created At','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Filename','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Purpose','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Action','gpt3-ai-content-generator')?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($wpaicg_files && is_array($wpaicg_files) && count($wpaicg_files)):
        foreach($wpaicg_files as $wpaicg_file):
            ?>
            <tr>
                <td><?php echo esc_html($wpaicg_file->post_title)?></td>
                <td><?php echo esc_html(size_format($wpaicg_file->file_size))?></td>
                <td><?php echo esc_html($wpaicg_file->post_date)?></td>
                <td><?php echo esc_html($wpaicg_file->filename)?></td>
                <td><?php echo !empty($wpaicg_file->purpose) ? esc_html($fileTypes[$wpaicg_file->purpose]) : 'Fine-Tune'?></td>
                <td>
                    <button data-id="<?php echo esc_html($wpaicg_file->ID);?>" class="button button-small wpaicg_create_fine_tune"><?php echo esc_html__('Create Fine-Tune','gpt3-ai-content-generator')?></button>
                    <button data-id="<?php echo esc_html($wpaicg_file->ID);?>" class="button button-small wpaicg_retrieve_content"><?php echo esc_html__('Retrieve Content','gpt3-ai-content-generator')?></button>
                    <button data-id="<?php echo esc_html($wpaicg_file->ID);?>" class="button button-small button-link-delete wpaicg_delete_file"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></button>
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
        'base'         => admin_url('admin.php?page=wpaicg_finetune&wpage=%#%'),
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
        $('.wpaicg_modal_close').click(function (){
            $('.wpaicg_modal_close').closest('.wpaicg_modal').hide();
            $('.wpaicg_modal_close').closest('.wpaicg_modal').removeClass('wpaicg-small-modal');
            $('.wpaicg-overlay').hide();
        })
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
        var wpaicg_max_file_size = <?php echo esc_html($wpaicgMaxFileSize)?>;
        var wpaicg_max_size_in_mb = '<?php echo size_format(esc_html($wpaicgMaxFileSize))?>';
        var wpaicg_file_button = $('#wpaicg_file_button');
        var wpaicg_file_upload = $('#wpaicg_file_upload');
        var wpaicg_file_purpose = $('#wpaicg_file_purpose');
        var wpaicg_file_name = $('#wpaicg_file_name');
        var wpaicg_file_model = $('#wpaicg_file_model');
        var wpaicg_progress = $('.wpaicg_progress');
        var wpaicg_error_message = $('.wpaicg-error-msg');
        var wpaicg_create_fine_tune = $('.wpaicg_create_fine_tune');
        var wpaicg_retrieve_content = $('.wpaicg_retrieve_content');
        var wpaicg_delete_file = $('.wpaicg_delete_file');
        var wpaicg_ajax_url = '<?php echo admin_url('admin-ajax.php')?>';
        var wpaicgAjaxRunning = false;
        $('.wpaicg_sync_files').click(function (){
            var btn = $(this);
            if(!wpaicgAjaxRunning) {
                $.ajax({
                    url: wpaicg_ajax_url,
                    data: {action: 'wpaicg_fetch_finetune_files','nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        wpaicgAjaxRunning = true;
                        wpaicgLoading(btn);
                    },
                    success: function (res) {
                        wpaicgAjaxRunning = false;
                        wpaicgRmLoading(btn);
                        if (res.status === 'success') {
                            window.location.reload();
                        } else {
                            alert(res.msg);
                        }
                    },
                    error: function () {
                        wpaicgAjaxRunning = false;
                        wpaicgRmLoading(btn);
                        alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                    }
                })
            }
        })
        wpaicg_delete_file.click(function (){
            if(!wpaicgAjaxRunning) {
                var conf = confirm('<?php echo esc_html__('Are you sure?','gpt3-ai-content-generator')?>');
                if (conf) {
                    var btn = $(this);
                    var id = btn.attr('data-id');
                    $.ajax({
                        url: wpaicg_ajax_url,
                        data: {action: 'wpaicg_delete_finetune_file', id: id,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {
                            wpaicgAjaxRunning = true;
                            wpaicgLoading(btn);
                        },
                        success: function (res) {
                            wpaicgAjaxRunning = false;
                            wpaicgRmLoading(btn);
                            if (res.status === 'success') {
                                window.location.reload();
                            } else {
                                alert(res.msg);
                            }
                        },
                        error: function () {
                            wpaicgAjaxRunning = false;
                            wpaicgRmLoading(btn);
                            alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                        }
                    })
                }
                else{
                    wpaicgAjaxRunning = false;
                }
            }
        });
        $(document).on('click','#wpaicg_create_finetune_btn', function (e){
            if(!wpaicgAjaxRunning) {
                var btn = $(e.currentTarget);
                var id = $('#wpaicg_create_finetune_id').val();
                var model = $('#wpaicg_create_finetune_model').val();
                $.ajax({
                    url: wpaicg_ajax_url,
                    data: {action: 'wpaicg_create_finetune', id: id, model: model,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        wpaicgAjaxRunning = true;
                        wpaicgLoading(btn);
                    },
                    success: function (res) {
                        wpaicgRmLoading(btn);
                        wpaicgAjaxRunning = false;
                        if (res.status === 'success') {
                            $('.wpaicg_modal_content').empty();
                            $('.wpaicg-overlay').hide();
                            $('.wpaicg_modal').hide();
                            alert('<?php echo esc_html__('Congratulations! Your fine-tuning was created successfully. You can track its progress in the "Trainings" tab.','gpt3-ai-content-generator')?>');

                        } else {
                            alert(res.msg);
                        }
                    },
                    error: function () {
                        wpaicgAjaxRunning = false;
                        wpaicgRmLoading(btn);
                        alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                    }
                });
            }
        });
        wpaicg_create_fine_tune.click(function (){
            if(!wpaicgAjaxRunning) {
                var btn = $(this);
                var id = btn.attr('data-id');
                $.ajax({
                    url: wpaicg_ajax_url,
                    data: {action: 'wpaicg_create_finetune_modal','nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        wpaicgAjaxRunning = true;
                        wpaicgLoading(btn);
                    },
                    success: function (res) {
                        wpaicgAjaxRunning = false;
                        wpaicgRmLoading(btn);
                        if (res.status === 'success') {
                            $('.wpaicg_modal_content').empty();
                            $('.wpaicg-overlay').show();
                            $('.wpaicg_modal').show();
                            $('.wpaicg_modal_title').html('<?php echo esc_html__('Choose Model','gpt3-ai-content-generator')?>');
                            $('.wpaicg_modal').addClass('wpaicg-small-modal');
                            var html = '<input type="hidden" id="wpaicg_create_finetune_id" value="' + id + '"><p><label><?php echo esc_html__('Select Model','gpt3-ai-content-generator')?></label>';
                            html += '<select style="width: 100%" id="wpaicg_create_finetune_model">';
                            html += '<option value=""><?php echo esc_html__('New Model','gpt3-ai-content-generator')?></option>';
                            $.each(res.data, function (idx, item) {
                                html += '<option value="' + item + '">' + item + '</option>';
                            })
                            html += '</select>';
                            html += '</p>';
                            html += '<p><button style="width: 100%" class="button button-primary" id="wpaicg_create_finetune_btn"><?php echo esc_html__('Create','gpt3-ai-content-generator')?></button></p>'
                            $('.wpaicg_modal_content').append(html)
                        } else {
                            alert(res.msg);
                        }
                    },
                    error: function () {
                        wpaicgAjaxRunning = false;
                        wpaicgRmLoading(btn);
                        alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                    }
                })
            }
        });
        wpaicg_retrieve_content.click(function (){
            if(!wpaicgAjaxRunning) {
                var btn = $(this);
                var id = btn.attr('data-id');
                $.ajax({
                    url: wpaicg_ajax_url,
                    data: {action: 'wpaicg_get_finetune_file', id: id,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        wpaicgAjaxRunning = true;
                        wpaicgLoading(btn);
                    },
                    success: function (res) {
                        wpaicgAjaxRunning = false;
                        wpaicgRmLoading(btn);
                        if (res.status === 'success') {
                            $('.wpaicg_modal_title').html('<?php echo esc_html__('File Content','gpt3-ai-content-generator')?>');
                            $('.wpaicg_modal_content').html('<pre>' + res.data + '</pre>');
                            $('.wpaicg-overlay').show();
                            $('.wpaicg_modal').show();
                        } else {
                            alert(res.msg);
                        }
                    },
                    error: function () {
                        wpaicgAjaxRunning = false;
                        wpaicgRmLoading(btn);
                        alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                    }
                })
            }
        });
    })
</script>
