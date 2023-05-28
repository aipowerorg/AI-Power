<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wpaicg_agent_guide_editor" style="display: none">
    <?php
    include __DIR__.'/wpaicg_alert.php';
    ?>
</div>
<div class="wpaicg_agent_guide_embeddings" style="display: none">
    <?php
    include WPAICG_PLUGIN_DIR.'admin/views/embeddings/builder_alert.php';
    ?>
</div>
<div class="wpaicg_agent_guide_rss" style="display: none">
    <?php
    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
        include WPAICG_LIBS_DIR.'views/rss/wpaicg_rss_alert.php';
    }
    else{
        echo esc_html__('You need to upgrade to Pro to use this agent','gpt3-ai-content-generator');
    }
    ?>
</div>
<div class="wpaicg_agent_guide_tweet" style="display: none">
    <?php
    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
        include WPAICG_LIBS_DIR.'views/twitter/alert.php';
    }
    else{
        echo esc_html__('You need to upgrade to Pro to use this agent','gpt3-ai-content-generator');
    }
    ?>
</div>
<?php
function wpaicgHumanTime($time){
    $wpaicg_current_timestamp = time();

    $wpaicg_time_diff = human_time_diff( $time, $wpaicg_current_timestamp );

    if ( strpos( $wpaicg_time_diff, 'hour' ) !== false ) {
        $wpaicg_output = str_replace( 'hours', esc_html__('hours','gpt3-ai-content-generator'), $wpaicg_time_diff );
        $wpaicg_output = str_replace( 'hour', esc_html__('hour','gpt3-ai-content-generator'), $wpaicg_output );
    } elseif ( strpos( $wpaicg_time_diff, 'day' ) !== false ) {
        $wpaicg_output = str_replace( 'days', esc_html__('days','gpt3-ai-content-generator'), $wpaicg_time_diff );
        $wpaicg_output = str_replace( 'day', esc_html__('day','gpt3-ai-content-generator'), $wpaicg_output );
    } elseif ( strpos( $wpaicg_time_diff, 'min' ) !== false ) {
        $wpaicg_output = str_replace( 'minutes', esc_html__('minutes','gpt3-ai-content-generator'), $wpaicg_time_diff );
        $wpaicg_output = str_replace( 'minute', esc_html__('minute','gpt3-ai-content-generator'), $wpaicg_output );
    } else {
        $wpaicg_output = $wpaicg_time_diff;
    }
    return sprintf(__('%s ago','gpt3-ai-content-generator'),$wpaicg_output);
}
$agents = array(
    'editor' => array(
        'name' => __('Queue Processor','gpt3-ai-content-generator'),
        'link' => admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=tracking'),
        'status' => 'error',
        'last_run' => __('Never','gpt3-ai-content-generator'),
        'last_content' => __('Never','gpt3-ai-content-generator'),
    ),
    'sheets' => array(
        'name' => __('Google Sheets','gpt3-ai-content-generator'),
        'link' => admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=google-sheets'),
        'status' => 'error',
        'last_run' => __('Never','gpt3-ai-content-generator'),
        'last_content' => __('Never','gpt3-ai-content-generator'),
    ),
    'rss' => array(
        'name' => __('RSS','gpt3-ai-content-generator'),
        'link' => admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=rss'),
        'status' => 'error',
        'last_run' => __('Never','gpt3-ai-content-generator'),
        'last_content' => __('Never','gpt3-ai-content-generator'),
    ),
    'embeddings' => array(
        'name' => __('Embeddings','gpt3-ai-content-generator'),
        'link' => admin_url('admin.php?page=wpaicg_embeddings&action=builder'),
        'status' => 'error',
        'last_run' => __('Never','gpt3-ai-content-generator'),
        'last_content' => __('Never','gpt3-ai-content-generator'),
    ),
    'tweet' => array(
        'name' => __('Twitter','gpt3-ai-content-generator'),
        'link' => admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=tweet'),
        'status' => 'error',
        'last_run' => __('Never','gpt3-ai-content-generator'),
        'last_content' => __('Never','gpt3-ai-content-generator'),
    ),
);
if(!current_user_can('wpaicg_bulk_content_tracking')){
    unset($agents['editor']);
}
if(!current_user_can('wpaicg_bulk_content_google-sheets')){
    unset($agents['sheets']);
}
if(!current_user_can('wpaicg_bulk_content_rss')){
    unset($agents['rss']);
}
if(!current_user_can('wpaicg_embeddings_builder')){
    unset($agents['embeddings']);
}
if(!current_user_can('wpaicg_bulk_content_tweet')){
    unset($agents['tweet']);
}
if(isset($agents['editor'])){
    $wpaicg_cron_added = get_option('_wpaicg_cron_added','');
    if(!empty($wpaicg_cron_added)){
        $agents['editor']['status'] = 'success';
        $wpaicg_crojob_last_time = get_option('_wpaicg_crojob_bulk_last_time','');
        $wpaicg_crojob_last_content = get_option('wpaicg_cronjob_bulk_content','');
        if(!empty($wpaicg_crojob_last_time)){
            $agents['editor']['last_run'] = wpaicgHumanTime($wpaicg_crojob_last_time);
        }
        if(!empty($wpaicg_crojob_last_content)){
            $agents['editor']['last_content'] = wpaicgHumanTime($wpaicg_crojob_last_content);
        }
    }
}
if(isset($agents['embeddings'])){
    $wpaicg_cron_added = get_option('wpaicg_cron_builder_added','');
    if(!empty($wpaicg_cron_added)){
        $agents['embeddings']['status'] = 'success';
        $wpaicg_crojob_last_time = get_option('wpaicg_crojob_builder_last_time','');
        $wpaicg_crojob_last_content = get_option('wpaicg_crojob_builder_content','');
        if(!empty($wpaicg_crojob_last_time)){
            $agents['embeddings']['last_run'] = wpaicgHumanTime($wpaicg_crojob_last_time);
        }
        if(!empty($wpaicg_crojob_last_content)){
            $agents['embeddings']['last_content'] = wpaicgHumanTime($wpaicg_crojob_last_content);
        }
    }
}
if(isset($agents['sheets']) && \WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
    $wpaicg_cron_added = get_option('wpaicg_cron_sheets_added','');
    if(!empty($wpaicg_cron_added)){
        $agents['sheets']['status'] = 'success';
        $wpaicg_crojob_last_time = get_option('wpaicg_crojob_sheets_last_time','');
        $wpaicg_crojob_last_content = get_option('wpaicg_cronjob_sheets_content','');
        if(!empty($wpaicg_crojob_last_time)){
            $agents['sheets']['last_run'] = wpaicgHumanTime($wpaicg_crojob_last_time);
        }
        if(!empty($wpaicg_crojob_last_content)){
            $agents['sheets']['last_content'] = wpaicgHumanTime($wpaicg_crojob_last_content);
        }
    }
}
if(isset($agents['rss']) && \WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
    $wpaicg_cron_added = get_option('_wpaicg_cron_rss_added','');
    if(!empty($wpaicg_cron_added)){
        $agents['rss']['status'] = 'success';
        $wpaicg_crojob_last_time = get_option('_wpaicg_crojob_rss_last_time','');
        $wpaicg_crojob_last_content = get_option('wpaicg_cronjob_rss_content','');
        if(!empty($wpaicg_crojob_last_time)){
            $agents['rss']['last_run'] = wpaicgHumanTime($wpaicg_crojob_last_time);
        }
        if(!empty($wpaicg_crojob_last_content)){
            $agents['rss']['last_content'] = wpaicgHumanTime($wpaicg_crojob_last_content);
        }
    }
}
if(isset($agents['tweet']) && \WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
    $wpaicg_cron_added = get_option('wpaicg_cron_tweet_added','');
    if(!empty($wpaicg_cron_added)){
        $agents['tweet']['status'] = 'success';
        $wpaicg_crojob_last_time = get_option('wpaicg_cron_tweet_last_time','');
        $wpaicg_crojob_last_content = get_option('wpaicg_cronjob_tweet_content','');
        if(!empty($wpaicg_crojob_last_time)){
            $agents['tweet']['last_run'] = wpaicgHumanTime($wpaicg_crojob_last_time);
        }
        if(!empty($wpaicg_crojob_last_content)){
            $agents['tweet']['last_content'] = wpaicgHumanTime($wpaicg_crojob_last_content);
        }
    }
}
?>
<style>
    .wpaicg_agent_status_error{
        width: 20px;
        height: 20px;
        display: block;
        border-radius: 50%;
        background: #e90e11;
        border: 1px solid #b11414;
    }
    .wpaicg_agent_status_success{
        width: 20px;
        height: 20px;
        display: block;
        border-radius: 50%;
        background: #1be90e;
        border: 1px solid #14b119;
    }
</style>
<h3><?php echo esc_html__('GPT Agents','gpt3-ai-content-generator')?></h3>
<p><?php echo sprintf(esc_html__('GPT agents function alongside server-side cron jobs. To deploy them, you need to set up cron jobs by following the detailed guide %shere%s','gpt3-ai-content-generator'),'<a href="'.esc_url("https://docs.aipower.org/docs/AutoGPT/gpt-agents#cron-job-setup").'" target="_blank">','</a>')?>.</p>
<div class="wpaicg-d-flex mb-5">
    <input style="width: 100%" value="" class="regular-text wpaicg_agent_search" type="text" placeholder="<?php echo esc_html__('Type for search','gpt3-ai-content-generator')?>">
</div>
<table class="wp-list-table widefat fixed striped table-view-list posts">
    <thead>
        <tr>
            <th><?php echo esc_html__('Agent','gpt3-ai-content-generator')?></th>
            <th><?php echo esc_html__('Status','gpt3-ai-content-generator')?></th>
            <th><?php echo esc_html__('Last Run Time','gpt3-ai-content-generator')?></th>
            <th><?php echo esc_html__('Last Content Generation','gpt3-ai-content-generator')?></th>
            <th><?php echo esc_html__('Info','gpt3-ai-content-generator')?></th>
        </tr>
    </thead>
    <tbody class="wpaicg_agent_table"></tbody>
</table>
<div class="wpaicg_agent_pagination wpaicg-paginate"></div>
<div class="wpaicg_agent_guide_sheets" style="display: none">
    <?php
    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()) {
        include WPAICG_LIBS_DIR . 'views/google-sheets/alert.php';
    }
    else{
        echo esc_html__('You need to upgrade to Pro to use this agent','gpt3-ai-content-generator');
    }
    ?>
</div>
<script>
    jQuery(document).ready(function ($){
        var agents = <?php echo wp_json_encode($agents)?>;
        var listAgents = [];
        var table_agents = $('.wpaicg_agent_table');
        var agent_pagination = $('.wpaicg_agent_pagination');
        function wpaicgAgentsTable(){
            listAgents = [];
            var search = $('.wpaicg_agent_search').val();
            search = search.toLowerCase();
            $.each(agents, function(key, item){
                var name = item.name.toLowerCase();
                var included = true;
                if(search !== ''){
                    if(name.indexOf(search) < 0){
                        included = false;
                    }
                }
                if(included){
                    item.key = key;
                    listAgents.push(item)
                }
            });
            table_agents.empty();
            agent_pagination.empty();
            if(listAgents.length){
                wpaicgDrawAgents(1);
            }
        }
        function wpaicgDrawAgents(page){
            table_agents.empty();
            agent_pagination.empty();
            page = parseInt(page);
            var number_per_page = 10;
            var endList = page*number_per_page;
            var startList = endList - number_per_page;
            if(endList > listAgents.length){
                endList = listAgents.length;
            }
            var max_pages = Math.ceil(listAgents.length/number_per_page);
            if(startList < 0){
                startList = 0;
            }
            for(var i=startList;i < endList;i++){
                var item = listAgents[i];
                if(typeof item !== "undefined") {
                    var item_name = item.name;
                    if(item.key === 'sheets' || item.key === 'rss') {
                    }
                    var html = '<tr>';
                    html += '<td>';
                    html += '<a href="' + item.link + '">' + item_name + '</a>';
                    html += '</td>';
                    html += '<td>';
                    html += '<span class="wpaicg_agent_status_' + item.status + '"></span>';
                    html += '</td>';
                    html += '<td>' + item.last_run + '</td>';
                    html += '<td>' + item.last_content + '</td>';
                    html += '<td><a class="wpaicg_agent_detail" data-title="'+item.name+'" href="javascript:void(0)" data-target="' + item.key + '"><?php echo esc_html__('Details', 'gpt3-ai-content-generator')?></a></td>';
                    html += '</tr>';
                    table_agents.append(html);
                }
            }
            if(max_pages > 1){
                for(var i = 1;i <= max_pages;i++){
                    if($.trim(agent_pagination.html())){
                        agent_pagination.append('&nbsp;');
                    }
                    if(i === page){
                        agent_pagination.append('<span aria-current="page" class="page-numbers current">'+i+'</span>');
                    }
                    else{
                        agent_pagination.append('<a class="page-numbers" data-page="'+i+'" href="javascript:void(0)">'+i+'</a>');
                    }
                }
            }

        }
        $(document).on('click','.wpaicg_agent_pagination .page-numbers',function (e){
            wpaicgDrawAgents($(e.currentTarget).attr('data-page'));
        })
        $('.wpaicg_agent_search').on('input', function (){
            wpaicgAgentsTable();
        })
        wpaicgAgentsTable();
        $('.wpaicg_modal_close').click(function (){
            $('.wpaicg_modal_close').closest('.wpaicg_modal').hide();
            $('.wpaicg-overlay').hide();
        });
        $(document).on('click','.wpaicg_agent_detail',function (e){
            var target = $(e.currentTarget).attr('data-target');
            var title = $(e.currentTarget).attr('data-title');
            $('.wpaicg_modal_title').html(title);
            $('.wpaicg_modal_content').html($('.wpaicg_agent_guide_'+target).html());
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
        })
    })
</script>
