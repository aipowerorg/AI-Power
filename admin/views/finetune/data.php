<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$wpaicg_files_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$wpaicg_files_per_page = 20;
$wpaicg_files_offset = ( $wpaicg_files_page * $wpaicg_files_per_page ) - $wpaicg_files_per_page;
$wpaicg_files_count_sql = "SELECT COUNT(*) FROM ".$wpdb->posts." f WHERE f.post_type='wpaicg_convert' AND f.post_status='publish'";
$wpaicg_files_sql = $wpdb->prepare("SELECT f.* FROM ".$wpdb->posts." f WHERE f.post_type='wpaicg_convert' AND f.post_status='publish' ORDER BY f.post_date DESC LIMIT %d,%d", $wpaicg_files_offset,$wpaicg_files_per_page);
$wpaicg_files = $wpdb->get_results($wpaicg_files_sql);
$wpaicg_files_total = $wpdb->get_var( $wpaicg_files_count_sql );
?>
<style>
    .wpaicg-convert-progress{
        height: 15px;
        max-width: 320px;
        background: #727272;
        border-radius: 5px;
        color: #fff;
        padding: 2px 12px;
        position: relative;
        font-size: 12px;
        text-align: center;
        margin-bottom: 10px;
        display: none;
    }
    .wpaicg-convert-progress.wpaicg_error span{
        background: #bb0505;
    }
    .wpaicg-convert-progress span{
        display: block;
        position: absolute;
        height: 100%;
        border-radius: 5px;
        background: #2271b1;
        top: 0;
        left: 0;
        transition: width .6s ease;
    }
    .wpaicg-convert-progress small{
        position: relative;
        font-size: 12px;
    }
</style>
<h1 class="wp-heading-inline"><?php echo esc_html__('Data Converter','gpt3-ai-content-generator')?></h1>
<form id="wpaicg_data_converter" method="post" action="">
    <?php
    wp_nonce_field('wpaicg_data_converter_count','nonce');
    ?>
    <input type="hidden" name="action" value="wpaicg_data_converter_count">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><?php echo esc_html__('Select Data','gpt3-ai-content-generator')?></th>
            <td>
                <label><input class="wpaicg_converter_data" checked type="checkbox" name="data[]" value="post"> <?php echo esc_html__('Posts','gpt3-ai-content-generator')?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label><input class="wpaicg_converter_data" type="checkbox" name="data[]" value="page"> <?php echo esc_html__('Pages','gpt3-ai-content-generator')?></label>
                <?php
                if(in_array('product',get_post_types()) && class_exists( 'woocommerce' )):
                ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input class="wpaicg_converter_data" type="checkbox" name="data[]" value="product"> <?php echo esc_html__('Products','gpt3-ai-content-generator')?></label>
                <?php
                endif;
                ?>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <div class="wpaicg-convert-progress wpaicg-convert-bar">
                    <span></span>
                    <small>0%</small>
                </div>
                <button class="button-primary button wpaicg_converter_button"><?php echo esc_html__('Convert','gpt3-ai-content-generator')?></button>
            </td>
        </tr>
        </tbody>
    </table>
</form>
<h1 class="wp-heading-inline"><?php echo esc_html__('Completed Conversions','gpt3-ai-content-generator')?></h1>
<table class="wp-list-table widefat fixed striped table-view-list comments">
    <thead>
    <tr>
        <th><?php echo esc_html__('Filename','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Started','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Completed','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Size','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Action','gpt3-ai-content-generator')?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($wpaicg_files && is_array($wpaicg_files) && count($wpaicg_files)):
    foreach($wpaicg_files as $wpaicg_file):
        $file = wp_upload_dir()['basedir'].'/'.$wpaicg_file->post_title;
        if(file_exists($file)):

    ?>
    <tr>
        <td><?php echo esc_html($wpaicg_file->post_title);?></td>
        <td><?php echo esc_html(date('d.m.Y H:i',strtotime($wpaicg_file->post_date)));?></td>
        <td><?php echo esc_html(date('d.m.Y H:i',strtotime($wpaicg_file->post_modified)));?></td>
        <td><?php echo esc_html(size_format(filesize($file)));?></td>
        <td>
            <a class="button button-small" href="<?php echo wp_upload_dir()['baseurl'].'/'.esc_html($wpaicg_file->post_title)?>" download><?php echo esc_html__('Download','gpt3-ai-content-generator')?></a>
            <button class="button button-small wpaicg_convert_upload" data-lines="<?php echo esc_html(count(file($file)))?>" data-file="<?php echo esc_html($wpaicg_file->post_title)?>"><?php echo esc_html__('Upload','gpt3-ai-content-generator')?></button>
        </td>
    </tr>
    <?php
        endif;
        endforeach;
    endif;
    ?>
    </tbody>
</table>
<div class="wpaicg-paginate mb-5">
    <?php
    echo paginate_links( array(
        'base'         => admin_url('admin.php?page=wpaicg_finetune&action=data&wpage=%#%'),
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
        });
        var form = $('#wpaicg_data_converter');
        var btn = $('.wpaicg_converter_button');
        var progressBar = $('.wpaicg-convert-bar');
        var wpaicg_ajax_url = '<?php echo admin_url('admin-ajax.php')?>';
        var wpaicg_convert_upload = $('.wpaicg_convert_upload');
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
        function wpaicgConverter(data){
            $.ajax({
                url: wpaicg_ajax_url,
                data: data,
                type: 'POST',
                dataType: 'JSON',
                success: function (res){
                    if(res.status === 'success'){
                        if(res.next_page === 'DONE'){
                            wpaicgRmLoading(btn);
                            progressBar.find('small').html('100%');
                            progressBar.find('span').css('width','100%');
                            setTimeout(function (){
                                window.location.reload();
                            },1000);
                        }
                        else{
                            var percent = Math.ceil(data.page*100/data.total);
                            progressBar.find('small').html(percent+'%');
                            progressBar.find('span').css('width',percent+'%');
                            data.page = res.next_page;
                            data.file = res.file;
                            data.id = res.id;
                            wpaicgConverter(data);
                        }
                    }
                    else{
                        progressBar.addClass('wpaicg_error');
                        wpaicgRmLoading(btn);
                        alert(res.msg);
                    }
                },
                error: function (){
                    progressBar.addClass('wpaicg_error');
                    wpaicgRmLoading(btn);
                    alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                }
            })
        }
        form.on('submit', function (){
            if(!$('.wpaicg_converter_data:checked').length){
                alert('<?php echo esc_html__('Please select least one data to convert','gpt3-ai-content-generator')?>');
            }
            else{
                var data = form.serialize();
                $.ajax({
                    url: wpaicg_ajax_url,
                    data: data,
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function (){
                        progressBar.show();
                        progressBar.removeClass('wpaicg_error')
                        progressBar.find('span').css('width',0);
                        progressBar.find('small').html('0%');
                        wpaicgLoading(btn)
                    },
                    success: function (res){
                        if(res.status === 'success'){
                            if(res.count > 0){
                                wpaicgConverter({action: 'wpaicg_data_converter',types: res.types, total: res.count, page: 1,per_page: 100,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'});
                            }
                            else{
                                progressBar.addClass('wpaicg_error');
                                wpaicgRmLoading(btn);
                                alert('<?php echo esc_html__('Nothing to convert','gpt3-ai-content-generator')?>');
                            }
                        }
                        else{
                            progressBar.addClass('wpaicg_error');
                            wpaicgRmLoading(btn);
                            alert(res.msg);
                        }
                    },
                    error: function (){
                        progressBar.addClass('wpaicg_error');
                        wpaicgRmLoading(btn);
                        alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                    }
                })

            }
            return false;
        });
        wpaicg_convert_upload.click(function (){
            var btn = $(this);
            var file = btn.attr('data-file');
            var lines = btn.attr('data-lines');
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
            $('.wpaicg_modal_title').html('File Setting');
            $('.wpaicg_modal').addClass('wpaicg-small-modal');
            $('.wpaicg_modal_content').empty();
            var html = '<form id="wpaicg_upload_convert" action="" method="post"><?php wp_nonce_field('wpaicg-ajax-nonce','nonce');?><input type="hidden" name="action" value="wpaicg_upload_convert"><input type="hidden" id="wpaicg_upload_convert_index" name="index" value="1"><input id="wpaicg_upload_convert_line" type="hidden" name="line" value="0"><input id="wpaicg_upload_convert_lines" type="hidden" value="'+lines+'"><input type="hidden" name="file" value="'+file+'"><p><label><?php echo esc_html__('Purpose','gpt3-ai-content-generator')?></label><select style="width: 100%" name="purpose"><option value="fine-tune"><?php echo esc_html__('Fine-Tune','gpt3-ai-content-generator')?></option></select></p>';
            html += '<p><label><?php echo esc_html__('Model Base','gpt3-ai-content-generator')?></label><select style="width: 100%" name="model"><option value="ada">ada</option><option value="babbage">babbage</option><option value="curie">curie</option><option value="davinci">davinci</option></select></p>';
            html += '<p><label><?php echo esc_html__('Custom Name','gpt3-ai-content-generator')?></label><input style="width: 100%" type="text" name="custom"></p>';
            html += '<div class="wpaicg-convert-progress wpaicg-upload-bar"><span></span><small>0%</small></div>';
            html += '<div class="wpaicg-upload-message"></div><p><button style="width: 100%" class="button button-primary" id="wpaicg_create_finetune_btn"><?php echo esc_html__('Upload','gpt3-ai-content-generator')?></button></p>'
            $('.wpaicg_modal_content').append(html);
        });
        function wpaicgFileUpload(data, btn){
            var wpaicg_upload_convert_index = parseInt($('#wpaicg_upload_convert_index').val());
            var total_lines = parseInt($('#wpaicg_upload_convert_lines').val());
            var  wpaicg_upload_bar = $('.wpaicg-upload-bar');
            $.ajax({
                url: wpaicg_ajax_url,
                data: data,
                type: 'POST',
                dataType: 'JSON',
                success: function (res){
                    if(res.status === 'success'){
                        if(res.next === 'DONE'){
                            $('.wpaicg-upload-message').html('<?php echo esc_html__('Upload successfully','gpt3-ai-content-generator')?>');
                            wpaicgRmLoading(btn);
                            wpaicg_upload_bar.find('small').html('100%');
                            wpaicg_upload_bar.find('span').css('width','100%');
                        }
                        else{
                            var percent = Math.ceil(res.next*100/total_lines);
                            wpaicg_upload_bar.find('small').html(percent+'%');
                            wpaicg_upload_bar.find('span').css('width',percent+'%');
                            $('#wpaicg_upload_convert_line').val(res.next);
                            $('#wpaicg_upload_convert_index').val(wpaicg_upload_convert_index+1);
                            var data = $('#wpaicg_upload_convert').serialize();
                            wpaicgFileUpload(data,btn);
                        }
                    }
                    else{
                        wpaicg_upload_bar.addClass('wpaicg_error');
                        wpaicgRmLoading(btn);
                        alert(res.msg);
                    }
                },
                error: function (){
                    wpaicg_upload_bar.addClass('wpaicg_error');
                    wpaicgRmLoading(btn);
                    alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                }
            })
        }
        $(document).on('submit','#wpaicg_upload_convert', function (e){
            $('#wpaicg_upload_convert_index').val(1);
            $('#wpaicg_upload_convert_line').val(0);
            $('.wpaicg-upload-message').empty();
            var form = $(e.currentTarget);
            var data = form.serialize();
            var btn = form.find('button');
            wpaicgLoading(btn);
            var  wpaicg_upload_bar = $('.wpaicg-upload-bar');
            wpaicg_upload_bar.show();
            wpaicg_upload_bar.removeClass('wpaicg_error')
            wpaicg_upload_bar.find('span').css('width',0);
            wpaicg_upload_bar.find('small').html('0%');
            wpaicgFileUpload(data,btn);
            return false;
        })
    })
</script>
