<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$wpaicg_embedding_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$wpaicg_sub_action = isset($_GET['sub']) && !empty($_GET['sub']) ? sanitize_text_field($_GET['sub']) : false;
$search = isset($_GET['wsearch']) && !empty($_GET['wsearch']) ? sanitize_text_field($_GET['wsearch']) : '';
if($wpaicg_sub_action == 'reindexall'){
    $ids = $wpdb->get_results("SELECT ID FROM ".$wpdb->posts." WHERE post_type='wpaicg_embeddings'");
    $ids = wp_list_pluck($ids,'ID');
    if(count($ids)) {
        WPAICG\WPAICG_Embeddings::get_instance()->wpaicg_reindex_embeddings_ids($ids);
    }
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_embeddings&action=logs').'";</script>';
    exit;
}
if($wpaicg_sub_action == 'deleteall'){
    $ids = $wpdb->get_results("SELECT ID FROM ".$wpdb->posts." WHERE post_type='wpaicg_embeddings'");
    $ids = wp_list_pluck($ids,'ID');
    if(count($ids)) {
        WPAICG\WPAICG_Embeddings::get_instance()->wpaicg_delete_embeddings_ids($ids);
    }
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_embeddings&action=logs').'";</script>';
    exit;
}
$args = array(
    'post_type' => 'wpaicg_embeddings',
    'posts_per_page' => 40,
    'paged' => $wpaicg_embedding_page,
    'order' => 'DESC',
    'orderby' => 'date'
);
if(!empty($search)){
    $args['s'] = $search;
}
$wpaicg_embeddings = new WP_Query($args);
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
    .wp-core-ui .button.wpaicg-danger-btn{
        background: #c90000;
        color: #fff;
        border-color: #cb0000;
    }
</style>
<form action="" method="get">
    <input type="hidden" name="page" value="wpaicg_embeddings">
    <input type="hidden" name="action" value="logs">
    <?php wp_nonce_field('wpaicg_chatlogs_search_nonce', 'wpaicg_nonce'); ?>
    <div class="wpaicg-d-flex mb-5">
        <input style="width: 100%" value="<?php echo esc_html($search)?>" class="regular-text" name="wsearch" type="text" placeholder="<?php echo esc_html__('Type for search','gpt3-ai-content-generator')?>">
        <button class="button button-primary"><?php echo esc_html__('Search','gpt3-ai-content-generator')?></button>
    </div>
</form>
<?php
if($wpaicg_embeddings->have_posts()):
?>
<div class="tablenav top wpaicg-mb-10">
    <div class="alignleft actions bulkactions">
        <a onclick="return confirm('<?php echo esc_html__('Warning! All indexes will be deleted from Pinecone and elsewhere. Are you sure?','gpt3-ai-content-generator')?>')" href="<?php echo admin_url('admin.php?page=wpaicg_embeddings&action=logs&sub=deleteall')?>" class="button wpaicg-danger-btn"><?php echo esc_html__('Delete Everything','gpt3-ai-content-generator')?></a>
        <button class="button btn-delete-embeddings wpaicg-danger-btn"><?php echo esc_html__('Delete Selected','gpt3-ai-content-generator')?></button>
    </div>
</div>
<?php
endif;
?>
<table class="wp-list-table widefat fixed striped table-view-list posts">
    <thead>
    <tr>
        <td id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" class="wpaicg-select-all"></td>
        <th scope="col"><?php echo esc_html__('Content','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Token','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Estimated','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Type','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Date','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Status','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Action','gpt3-ai-content-generator')?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($wpaicg_embeddings->have_posts()){
        foreach ($wpaicg_embeddings->posts as $wpaicg_embedding){
            $token = get_post_meta($wpaicg_embedding->ID,'wpaicg_embedding_token',true);
            $wpaicg_embedding_type = get_post_meta($wpaicg_embedding->ID,'wpaicg_embedding_type',true);
            $wpaicg_embedding_status = get_post_meta($wpaicg_embedding->ID,'wpaicg_embeddings_reindex',true);
            ?>
            <tr id="wpaicg-builder-<?php echo esc_html($wpaicg_embedding->ID)?>">
                <th scope="row" class="check-column">
                    <input class="cb-select-embedding" id="cb-select-<?php echo esc_html($wpaicg_embedding->ID);?>" type="checkbox" name="ids[]" value="<?php echo esc_html($wpaicg_embedding->ID);?>">
                </th>
                <td><a data-content="<?php echo htmlentities(wp_kses_post($wpaicg_embedding->post_content),ENT_QUOTES,'UTF-8')?>" href="javascript:void()" class="wpaicg-embedding-content"><?php echo esc_html($wpaicg_embedding->post_title)?>..</a></td>
                <td><?php echo esc_html($token)?></td>
                <td><?php echo !empty($token) ? (number_format((int)esc_html($token)*0.0004/1000,5)).'$': '--'?></td>
                <td>
                    <?php
                    if(!$wpaicg_embedding_type || $wpaicg_embedding_type == '' || $wpaicg_embedding_type == 'free'){
                        echo esc_html__('Free Text','gpt3-ai-content-generator');
                    }
                    if($wpaicg_embedding_type == 'faq'){
                        echo esc_html__('FAQ','gpt3-ai-content-generator');
                    }
                    if($wpaicg_embedding_type == 'knowledge'){
                        echo esc_html__('KnowledgeBase','gpt3-ai-content-generator');
                    }
                    if($wpaicg_embedding_type == 'product'){
                        echo esc_html__('Product','gpt3-ai-content-generator');
                    }
                    if($wpaicg_embedding_type == 'wooproduct'){
                        echo esc_html__('WooCommerce Product','gpt3-ai-content-generator');
                    }
                    if($wpaicg_embedding_type == 'link'){
                        echo esc_html__('URL','gpt3-ai-content-generator');
                    }
                    if($wpaicg_embedding_type == 'company'){
                        echo esc_html__('Company Profile','gpt3-ai-content-generator');
                    }
                    if($wpaicg_embedding_type == 'event'){
                        echo esc_html__('Event','gpt3-ai-content-generator');
                    }
                    if($wpaicg_embedding_type == 'pricing'){
                        echo esc_html__('Pricing Plan','gpt3-ai-content-generator');
                    }
                    if($wpaicg_embedding_type == 'contact'){
                        echo esc_html__('Contact','gpt3-ai-content-generator');
                    }
                    ?>
                </td>
                <td><?php echo esc_html($wpaicg_embedding->post_date)?></td>
                <td><?php echo $wpaicg_embedding_status ? '<span>'.esc_html__('Pending','gpt3-ai-content-generator').'</span>': '<span style="color: #26a300">'.esc_html__('Indexed','gpt3-ai-content-generator').'</span>' ?></td>
                <td><button data-id="<?php echo esc_html($wpaicg_embedding->ID)?>" class="button button-link-delete wpaicg_delete button-small"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></button></td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
<div class="wpaicg-paginate">
    <?php
    echo paginate_links( array(
        'base'         => admin_url('admin.php?page=wpaicg_embeddings&action=logs&wpage=%#%'),
        'total'        => $wpaicg_embeddings->max_num_pages,
        'current'      => $wpaicg_embedding_page,
        'format'       => '?wpage=%#%',
        'show_all'     => false,
        'prev_next'    => false,
        'add_args'     => false,
    ));
    ?>
</div>
<script>
    jQuery(document).ready(function ($){
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
        $('.wpaicg_modal_close').click(function (){
            $('.wpaicg_modal_close').closest('.wpaicg_modal').hide();
            $('.wpaicg-overlay').hide();
        })
        $('.wpaicg-embedding-content').click(function (){
            var content = $(this).attr('data-content');
            content = content.replace(/\n/g, "<br />");
            $('.wpaicg_modal_title').html('<?php echo esc_html__('Embedding Content','gpt3-ai-content-generator')?>');
            $('.wpaicg_modal_content').html(content);
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
        });
        $('.btn-reindex-embeddings').click(function (){
            var btn = $(this);
            var ids = [];
            $('.cb-select-embedding:checked').each(function (idx, item){
                ids.push($(item).val())
            });
            if(ids.length){
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {action: 'wpaicg_reindex_embeddings', ids: ids,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
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
        $('.btn-delete-embeddings').click(function (){
            var conf = confirm('<?php echo esc_html__('Warning! Entries will be deleted from Pinecone and elsewhere. Are you sure?','gpt3-ai-content-generator')?>')
            if(conf) {
                var btn = $(this);
                var ids = [];
                $('.cb-select-embedding:checked').each(function (idx, item) {
                    ids.push($(item).val())
                });
                if (ids.length) {
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php')?>',
                        data: {action: 'wpaicg_delete_embeddings', ids: ids,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {
                            wpaicgLoading(btn);
                        },
                        success: function (res) {
                            window.location.reload();
                        },
                        error: function () {

                        }
                    });
                } else {
                    alert('<?php echo esc_html__('Nothing to do','gpt3-ai-content-generator')?>');
                }
            }
        });
        $(document).on('click','.wpaicg_delete' ,function (e){
            var btn = $(e.currentTarget);
            var id = btn.attr('data-id');
            var conf = confirm('<?php echo esc_html__('Are you sure?','gpt3-ai-content-generator')?>');
            if(conf){
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {action: 'wpaicg_builder_delete', id: id,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function (){
                        wpaicgLoading(btn);
                    },
                    success: function (res){
                        wpaicgRmLoading(btn);
                        if(res.status === 'success'){
                            $('#wpaicg-builder-'+id).remove();
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
            }
        });
    })
</script>
