<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$fileTypes = array(
    'fine-tune' => esc_html__('Fine-Tune','gpt3-ai-content-generator')
);
$wpaicgMaxFileSize = wp_max_upload_size();
if($wpaicgMaxFileSize > 104857600){
    $wpaicgMaxFileSize = 104857600;
}
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
<h3 style="margin-bottom: 5px"><?php echo esc_html__('Upload New File','gpt3-ai-content-generator')?></h3>
<div class="wpaicg_form_upload_file">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><?php echo esc_html__('Dataset','gpt3-ai-content-generator')?> (*.jsonl)</th>
            <td>
                <input type="file" id="wpaicg_file_upload">
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Purpose','gpt3-ai-content-generator')?></th>
            <td>
                <select id="wpaicg_file_purpose">
                    <?php
                    foreach ($fileTypes as $key=>$fileType){
                        echo '<option value="'.esc_html($key).'">'.esc_html($fileType).'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Model Base','gpt3-ai-content-generator')?></th>
            <td>
                <select id="wpaicg_file_model">
                    <option value="ada">ada</option>
                    <option value="babbage">babbage</option>
                    <option value="curie">curie</option>
                    <option value="davinci">davinci</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Custom Model Name','gpt3-ai-content-generator')?></th>
            <td>
                <input type="text" class="regular-text" id="wpaicg_file_name">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="wpaicg_upload_success" style="display: none;margin-bottom: 5px;color: green;"><?php echo esc_html__('File uploaded successfully you can view it in Datasets tab.','gpt3-ai-content-generator')?></div>
                <div class="wpaicg_progress" style="display: none"><span></span><small><?php echo esc_html__('Uploading','gpt3-ai-content-generator')?></small></div>
                <div class="wpaicg-error-msg"></div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button class="button button-primary" id="wpaicg_file_button" style="display: block;width: 100%"><?php echo esc_html__('Upload','gpt3-ai-content-generator')?></button><br>
                <?php echo esc_html__('Maximum upload file size','gpt3-ai-content-generator')?>: <?php echo size_format($wpaicgMaxFileSize)?>.
                <?php
                if(wp_max_upload_size() < 104857600){
                    echo esc_html__('(It is supposed to be at least 100mb if you want to upload larger datasets)','gpt3-ai-content-generator');
                }
                ?>
            </td>
        </tr>
        </tbody>
    </table>
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
        var wpaicg_upload_success = $('.wpaicg_upload_success');
        wpaicg_file_button.click(function (){
            if(wpaicg_file_upload[0].files.length === 0){
                alert('<?php echo esc_html__('Please select file','gpt3-ai-content-generator')?>');
            }
            else{
                var wpaicg_file = wpaicg_file_upload[0].files[0];
                var wpaicg_file_extension = wpaicg_file.name.substr( (wpaicg_file.name.lastIndexOf('.') +1) );
                if(wpaicg_file_extension !== 'jsonl'){
                    wpaicg_file_upload.val('');
                    alert('<?php echo esc_html__('Only accept JSONL file type','gpt3-ai-content-generator')?>');
                }
                else if(wpaicg_file.size > wpaicg_max_file_size){
                    wpaicg_file_upload.val('');
                    alert('<?php echo esc_html__('Dataset allow maximum','gpt3-ai-content-generator')?> '+wpaicg_max_size_in_mb)
                }
                else{
                    var formData = new FormData();
                    formData.append('action', 'wpaicg_finetune_upload');
                    formData.append('file', wpaicg_file);
                    formData.append('purpose', wpaicg_file_purpose.val());
                    formData.append('model', wpaicg_file_model.val());
                    formData.append('name', wpaicg_file_name.val());
                    formData.append('nonce','<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>');
                    $.ajax({
                        url: wpaicg_ajax_url,
                        type: 'POST',
                        dataType: 'JSON',
                        data: formData,
                        beforeSend: function (){
                            wpaicg_progress.find('span').css('width','0');
                            wpaicg_progress.show();
                            wpaicgLoading(wpaicg_file_button);
                            wpaicg_error_message.hide();
                            wpaicg_upload_success.hide();
                        },
                        xhr: function() {
                            var xhr = $.ajaxSettings.xhr();
                            xhr.upload.addEventListener("progress", function(evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = evt.loaded / evt.total;
                                    wpaicg_progress.find('span').css('width',(Math.round(percentComplete * 100))+'%');
                                }
                            }, false);
                            return xhr;
                        },
                        success: function(res) {
                            if(res.status === 'success'){
                                wpaicgRmLoading(wpaicg_file_button);
                                wpaicg_progress.hide();
                                wpaicg_file_upload.val('');
                                wpaicg_upload_success.show();
                            }
                            else{
                                wpaicgRmLoading(wpaicg_file_button);
                                wpaicg_progress.find('small').html('<?php echo esc_html__('Error','gpt3-ai-content-generator')?>');
                                wpaicg_progress.addClass('wpaicg_error');
                                wpaicg_error_message.html(res.msg);
                                wpaicg_error_message.show();
                            }
                        },
                        cache: false,
                        contentType: false,
                        processData: false,
                        error: function (){
                            wpaicg_file_upload.val('');
                            wpaicgRmLoading(wpaicg_file_button);
                            wpaicg_progress.addClass('wpaicg_error');
                            wpaicg_progress.find('small').html('Error');
                            wpaicg_error_message.html('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                            wpaicg_error_message.show();
                        }
                    });
                }
            }
        })
    })
</script>
