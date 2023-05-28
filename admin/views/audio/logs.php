<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (isset($_GET['wpaicg_nonce']) && !wp_verify_nonce($_GET['wpaicg_nonce'], 'wpaicg_audiolog_search_nonce')) {
    die(WPAICG_NONCE_ERROR);
}
if(isset($_GET['audio_delete']) && !empty($_GET['audio_delete'])){
    if(!wp_verify_nonce($_GET['_wpnonce'], 'wpaicg_delete_'.sanitize_text_field($_GET['audio_delete']))){
        die(WPAICG_NONCE_ERROR);
    }
    wp_delete_post(sanitize_text_field($_GET['audio_delete']));
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_audio&action=logs').'"</script>';
}
$wpaicg_audio_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$args = array(
    'post_type' => 'wpaicg_audio',
    'posts_per_page' => 40,
    'paged' => $wpaicg_audio_page,
    'order' => 'DESC',
    'orderby' => 'date'
);
$search = '';
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search = sanitize_text_field($_GET['search']);
    $args['s'] = $search;
}
$wpaicg_audios = new WP_Query($args);
?>
<style>
    .wpaicg_modal{
        height: 40%;
    }
    .wpaicg_modal_content{
        height: calc(100% - 80px);
        overflow-y: auto;
    }
    .wpaicg_modal_content pre{
        overflow-y: unset;
    }
</style>
<div>
    <div class="wpaicg-mb-10">
        <form action="" method="GET">
            <?php wp_nonce_field('wpaicg_audiolog_search_nonce', 'wpaicg_nonce'); ?>
            <input type="hidden" name="page" value="wpaicg_audio">
            <input type="hidden" name="action" value="logs">
            <input value="<?php echo esc_html($search)?>" name="search" type="text" placeholder="<?php echo esc_html__('Search Audio','gpt3-ai-content-generator')?>">
            <button class="button button-primary"><?php echo esc_html__('Search','gpt3-ai-content-generator')?></button>
        </form>
    </div>
</div>
<table class="wp-list-table widefat fixed striped table-view-list posts">
    <thead>
    <tr>
        <th width="40"><?php echo esc_html__('ID','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Title','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Format','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Date','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Duration','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Action','gpt3-ai-content-generator')?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($wpaicg_audios->have_posts()){
        foreach ($wpaicg_audios->posts as $wpaicg_audio){
            $wpaicg_response = get_post_meta($wpaicg_audio->ID,'wpaicg_response',true);
            $wpaicg_duration = get_post_meta($wpaicg_audio->ID,'wpaicg_duration',true);
            ?>
            <tr>
                <td><?php echo esc_html($wpaicg_audio->ID)?></td>
                <td>
                    <?php
                    if($wpaicg_response == 'post' || $wpaicg_response == 'page'):
                    $wpaicg_post_id = get_post_meta($wpaicg_audio->ID,'wpaicg_post',true);
                    ?>
                    <a href="<?php echo admin_url('post.php?post='.esc_html($wpaicg_post_id).'&action=edit')?>" class="wpaicg-view-content">
                    <?php
                    else:
                    ?>
                        <a data-response="<?php echo esc_html($wpaicg_response)?>" href="javascript:void(0)" class="wpaicg-view-content" data-content="<?php echo esc_html($wpaicg_audio->post_content)?>">
                    <?php
                    endif;
                    ?>
                    <?php
                    if($wpaicg_response == 'post' || $wpaicg_response == 'page'){
                        $post_title = get_the_title($wpaicg_post_id);
                        if(empty($post_title)){
                            echo esc_html($wpaicg_audio->post_title);
                        }
                        else{
                            echo esc_html($post_title);
                        }
                    }
                    else{
                        echo esc_html($wpaicg_audio->post_title);
                    }
                    ?>
                    </a>
                </td>
                <td><?php echo esc_html($wpaicg_response)?></td>
                <td><?php echo esc_html(date('d.m.Y H:i',strtotime($wpaicg_audio->post_date)))?></td>
                <td><?php echo esc_html(WPAICG\WPAICG_Content::get_instance()->wpaicg_seconds_to_time((int)$wpaicg_duration))?></td>
                <td>
                    <?php
                    if($wpaicg_response != 'post' && $wpaicg_response != 'page'):
                    ?>
                        <a download href="<?php echo wp_nonce_url(site_url('index.php?wpaicg_download_audio='.$wpaicg_audio->ID),'wpaicg_download_'.$wpaicg_audio->ID)?>" class="button button-primary button-small"><?php echo esc_html__('Download','gpt3-ai-content-generator')?></a>
                    <?php
                    endif;
                    ?>
                    <a onclick="return confirm('Are you sure?')" href="<?php echo wp_nonce_url(admin_url('admin.php?page=wpaicg_audio&action=logs&audio_delete='.$wpaicg_audio->ID),'wpaicg_delete_'.$wpaicg_audio->ID)?>" class="button button-link-delete button-small"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></a>
                </td>
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
        'base'         => admin_url('admin.php?page=wpaicg_audio&action=logs&wpage=%#%'),
        'total'        => $wpaicg_audios->max_num_pages,
        'current'      => $wpaicg_audio_page,
        'format'       => '?wpage=%#%',
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
        $('.wpaicg-view-content').click(function (){
            var content = $(this).attr('data-content');
            var response = $(this).attr('data-response');
            var html = '';
            html += content.replace(/\n/g, "<br />");
            if(response === 'json' || response === 'verbose_json') {
                $('.wpaicg_modal_content')[0].innerHTML = "";
                content = JSON.parse(content);
                $('.wpaicg_modal_content')[0].innerHTML = '<pre>'+JSON.stringify(content, undefined, 4)+'</pre>';
            }
            else{
                $('.wpaicg_modal_content').html(html);
            }
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
            $('.wpaicg_modal_title').html('<?php echo esc_html__('View Content','gpt3-ai-content-generator')?>');

        })
    })
</script>
