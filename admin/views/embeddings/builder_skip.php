<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
if(isset($_GET['sub_action']) && sanitize_text_field($_GET['sub_action']) == 'reindexall'){
    if(count($wpaicg_total_skips)){
        foreach($wpaicg_total_skips as $wpaicg_total_skip){
            update_post_meta($wpaicg_total_skip->ID,'wpaicg_indexed','reindex');
        }
    }
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_embeddings&action=builder&sub=skip').'";</script>';
    exit;
}
?>
<div class="tablenav top">
    <div class="alignleft actions bulkactions">
        <a href="<?php echo admin_url('admin.php?page=wpaicg_embeddings&action=builder&sub=skip&sub_action=reindexall')?>" class="button button-primary"><?php echo esc_html__('Re-Index All','gpt3-ai-content-generator')?></a>
        <button class="button button-primary btn-reindex-builder"><?php echo esc_html__('Re-Index Selected','gpt3-ai-content-generator')?></button>
    </div>
</div>
<div class="tablenav top">
    <form action="" method="get">
        <div class="alignleft actions bulkactions">
            <a href="<?php echo admin_url('admin.php?page=wpaicg_embeddings&action=builder')?>"><?php echo esc_html__('Indexed','gpt3-ai-content-generator')?> (<?php echo esc_html($wpaicg_total_indexed)?>)</a> |
            <a href="<?php echo admin_url('admin.php?page=wpaicg_embeddings&action=builder&sub=errors')?>"><?php echo esc_html__('Failed','gpt3-ai-content-generator')?> (<?php echo esc_html(count($wpaicg_total_errors))?>)</a> |
            <?php echo esc_html__('Skipped','gpt3-ai-content-generator')?> (<?php echo esc_html(count($wpaicg_total_skips))?>)
        </div>
        <p class="search-box">
            <input type="hidden" name="page" value="wpaicg_embeddings">
            <input type="hidden" name="action" value="builder">
            <input type="hidden" name="sub" value="skip">
            <?php wp_nonce_field('wpaicg_chatlogs_search_nonce', 'wpaicg_nonce'); ?>
            <input value="<?php echo esc_html($search)?>" name="wsearch" type="text" placeholder="<?php echo esc_html__('Type for search','gpt3-ai-content-generator')?>">
            <button class="button button-primary"><?php echo esc_html__('Search','gpt3-ai-content-generator')?></button>
        </p>
    </form>
</div>
<table class="wp-list-table widefat fixed striped table-view-list posts">
    <thead>
    <tr>
        <td id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" class="wpaicg-select-all"></td>
        <th><?php echo esc_html__('Title','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Message','gpt3-ai-content-generator')?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(count($wpaicg_total_skips)){
        foreach ($wpaicg_total_skips as $wpaicg_total_skip) {
            ?>
            <tr>
                <th scope="row" class="check-column">
                    <input class="cb-select-embedding" id="cb-select-<?php echo esc_html($wpaicg_total_skip->ID);?>" type="checkbox" name="ids[]" value="<?php echo esc_html($wpaicg_total_skip->ID);?>">
                </th>
                <td><a href="<?php echo esc_url(admin_url('post.php?post='.esc_html($wpaicg_total_skip->ID).'&action=edit'))?>" target="_blank"><?php echo esc_html($wpaicg_total_skip->post_title)?></a></td>
                <td><?php echo esc_html(get_post_meta($wpaicg_total_skip->ID,'wpaicg_error_msg',true))?></td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
<script>
    jQuery(document).ready(function ($){
        $('.btn-reindex-builder').click(function (){
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
            var btn = $(this);
            var ids = [];
            $('.cb-select-embedding:checked').each(function (idx, item){
                ids.push($(item).val())
            });
            if(ids.length){
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {action: 'wpaicg_reindex_builder_data', ids: ids,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        wpaicgLoading(btn);
                    },
                    success: function (res){
                        window.location.reload();
                    },
                    error: function (){

                    }
                });
            }
            else{
                alert('<?php echo esc_html__('Nothing to do','gpt3-ai-content-generator')?>');
            }
        });
    })
</script>
