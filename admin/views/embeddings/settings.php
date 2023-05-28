<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
include __DIR__.'/builder_alert.php';
$wpaicg_embeddings_settings_updated = false;
if(isset($_POST['wpaicg_save_builder_settings'])){
    check_admin_referer('wpaicg_embeddings_settings');
    if(isset($_POST['wpaicg_pinecone_api']) && !empty($_POST['wpaicg_pinecone_api'])) {
        update_option('wpaicg_pinecone_api', sanitize_text_field($_POST['wpaicg_pinecone_api']));
    }
    else{
        delete_option('wpaicg_pinecone_api');
    }
    if(isset($_POST['wpaicg_pinecone_environment']) && !empty($_POST['wpaicg_pinecone_environment'])) {
        update_option('wpaicg_pinecone_environment', sanitize_text_field($_POST['wpaicg_pinecone_environment']));
    }
    else{
        delete_option('wpaicg_pinecone_environment');
    }
    if(isset($_POST['wpaicg_pinecone_sv']) && !empty($_POST['wpaicg_pinecone_sv'])) {
        update_option('wpaicg_pinecone_sv', sanitize_text_field($_POST['wpaicg_pinecone_sv']));
    }
    else{
        delete_option('wpaicg_pinecone_sv');
    }
    if(isset($_POST['wpaicg_builder_enable']) && !empty($_POST['wpaicg_builder_enable'])){
        update_option('wpaicg_builder_enable','yes');
    }
    else{
        delete_option('wpaicg_builder_enable');
    }
    if(isset($_POST['wpaicg_builder_types']) && is_array($_POST['wpaicg_builder_types']) && count($_POST['wpaicg_builder_types'])){
        update_option('wpaicg_builder_types',\WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_builder_types']));
    }
    else{
        delete_option('wpaicg_builder_types');
    }
    if(isset($_POST['wpaicg_instant_embedding']) && !empty($_POST['wpaicg_instant_embedding'])){
        update_option('wpaicg_instant_embedding',\WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_instant_embedding']));
    }
    else{
        update_option('wpaicg_instant_embedding','no');
    }
    $wpaicg_embeddings_settings_updated = true;
}
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_sv = get_option('wpaicg_pinecone_sv','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
$wpaicg_builder_types = get_option('wpaicg_builder_types',[]);
$wpaicg_builder_enable = get_option('wpaicg_builder_enable','');
$wpaicg_instant_embedding = get_option('wpaicg_instant_embedding','yes');
$wpaicg_pinecone_indexes = get_option('wpaicg_pinecone_indexes','');
$wpaicg_pinecone_indexes = empty($wpaicg_pinecone_indexes) ? array() : json_decode($wpaicg_pinecone_indexes,true);
$wpaicg_pinecone_environments = array(
    'asia-northeast1-gcp' => 'GCP Asia-Northeast-1 (Tokyo)',
    'asia-northeast1-gcp-free' => 'GCP Asia-Northeast-1 Free (Tokyo)',
    'asia-northeast2-gcp' => 'GCP Asia-Northeast-2 (Osaka)',
    'asia-northeast2-gcp-free' => 'GCP Asia-Northeast-2 Free (Osaka)',
    'asia-northeast3-gcp' => 'GCP Asia-Northeast-3 (Seoul)',
    'asia-northeast3-gcp-free' => 'GCP Asia-Northeast-3 Free (Seoul)',
    'asia-southeast1-gcp' => 'GCP Asia-Southeast-1 (Singapore)',
    'asia-southeast1-gcp-free' => 'GCP Asia-Southeast-1 Free',
    'eu-west1-gcp' => 'GCP EU-West-1 (Ireland)',
    'eu-west1-gcp-free' => 'GCP EU-West-1 Free (Ireland)',
    'eu-west2-gcp' => 'GCP EU-West-2 (London)',
    'eu-west2-gcp-free' => 'GCP EU-West-2 Free (London)',
    'eu-west3-gcp' => 'GCP EU-West-3 (Frankfurt)',
    'eu-west3-gcp-free' => 'GCP EU-West-3 Free (Frankfurt)',
    'eu-west4-gcp' => 'GCP EU-West-4 (Netherlands)',
    'eu-west4-gcp-free' => 'GCP EU-West-4 Free (Netherlands)',
    'eu-west6-gcp' => 'GCP EU-West-6 (Zurich)',
    'eu-west6-gcp-free' => 'GCP EU-West-6 Free (Zurich)',
    'eu-west8-gcp' => 'GCP EU-West-8 (Italy)',
    'eu-west8-gcp-free' => 'GCP EU-West-8 Free (Italy)',
    'eu-west9-gcp' => 'GCP EU-West-9 (France)',
    'eu-west9-gcp-free' => 'GCP EU-West-9 Free (France)',
    'northamerica-northeast1-gcp' => 'GCP Northamerica-Northeast1',
    'northamerica-northeast1-gcp-free' => 'GCP Northamerica-Northeast1 Free',
    'southamerica-northeast2-gcp' => 'GCP Southamerica-Northeast2 (Toronto)',
    'southamerica-northeast2-gcp-free' => 'GCP Southamerica-Northeast2 Free (Toronto)',
    'southamerica-east1-gcp' => 'GCP Southamerica-East1 (Sao Paulo)',
    'southamerica-east1-gcp-free' => 'GCP Southamerica-East1 Free (Sao Paulo)',
    'us-central1-gcp' => 'GCP US-Central-1 (Iowa)',
    'us-central1-gcp-free' => 'GCP US-Central-1 Free (Iowa)',
    'us-east1-aws' => 'AWS US-East-1 (Virginia)',
    'us-east1-aws-free' => 'AWS US-East-1 Free (Virginia)',
    'us-east-1-aws' => 'AWS US-East-1 (Virginia)',
    'us-east-1-aws-free' => 'AWS US-East-1 Free (Virginia)',
    'us-east1-gcp' => 'GCP US-East-1 (South Carolina)',
    'us-east1-gcp-free' => 'GCP US-East-1 Free (South Carolina)',
    'us-east4-gcp' =>  'GCP US-East-4 (Virginia)',
    'us-east4-gcp-free' =>  'GCP US-East-4 Free (Virginia)',
    'us-west1-gcp' => 'GCP US-West-1 (N. California)',
    'us-west1-gcp-free' => 'GCP US-West-1 Free (N. California)',
    'us-west2-gcp' => 'GCP US-West-2 (Oregon)',
    'us-west2-gcp-free' => 'GCP US-West-2 Free (Oregon)',
    'us-west3-gcp' => 'GCP US-West-3 (Salt Lake City)',
    'us-west3-gcp-free' => 'GCP US-West-3 Free (Salt Lake City)',
    'us-west4-gcp' => 'GCP US-West-4 (Las Vegas)',
    'us-west4-gcp-free' => 'GCP US-West-4 Free (Las Vegas)'
);
if($wpaicg_embeddings_settings_updated){
    ?>
    <div class="notice notice-success">
        <p><?php echo esc_html__('Records updated successfully','gpt3-ai-content-generator')?></p>
    </div>
    <?php
}
?>
<style>
    .wpaicg_modal {
        width: 600px;
        left: calc(50% - 300px);
        height: 40%;
    }
    .wpaicg_modal_content{
        height: calc(100% - 103px);
        overflow-y: auto;
    }
    .wpaicg_assign_footer{
        position: absolute;
        bottom: 0;
        display: flex;
        justify-content: space-between;
        width: calc(100% - 20px);
        align-items: center;
        border-top: 1px solid #ccc;
        left: 0;
        padding: 3px 10px;
    }
</style>
<form action="" method="post">
    <?php
    wp_nonce_field('wpaicg_embeddings_settings');
    ?>
    <h3>Pinecone</h3>
    <div class="wpaicg-alert">
        <h3><?php echo esc_html__('Steps','gpt3-ai-content-generator')?></h3>
        <p><?php echo sprintf(esc_html__('1. Begin by watching the video tutorial provided %shere%s.','gpt3-ai-content-generator'),'<a href="https://docs.aipower.org/docs/embeddings" target="_blank">','</a>')?></p>
        <p><?php echo sprintf(esc_html__('2. Obtain your API key from %sPinecone%s.','gpt3-ai-content-generator'),'<a href="https://www.pinecone.io/" target="_blank">','</a>')?></p>
        <p><?php echo esc_html__('3. Create an Index on Pinecone.','gpt3-ai-content-generator')?></p>
        <p><?php echo sprintf(esc_html__('4. Ensure your dimension is set to %s1536%s.','gpt3-ai-content-generator'),'<b>','</b>')?></p>
        <p><?php echo sprintf(esc_html__('5. Set your metric to %scosine%s.','gpt3-ai-content-generator'),'<b>','</b>')?></p>
        <p><?php echo esc_html__('6. Input your data.','gpt3-ai-content-generator')?></p>
        <p><?php echo esc_html__('7. Navigate to Settings - ChatGPT tab and choose the Embeddings method.','gpt3-ai-content-generator')?></p>
    </div>
    <table class="form-table">
        <tr>
            <th scope="row"><?php echo esc_html__('Pinecone API','gpt3-ai-content-generator')?></th>
            <td>
                <input type="text" class="regular-text wpaicg_pinecone_api" name="wpaicg_pinecone_api" value="<?php echo esc_attr($wpaicg_pinecone_api)?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Pinecone Environment','gpt3-ai-content-generator')?></th>
            <td>
                <select class="wpaicg_pinecone_sv" name="wpaicg_pinecone_sv">
                <?php
                foreach ($wpaicg_pinecone_environments as $key=>$wpaicg_pinecone_environment_detail){
                    echo '<option'.($wpaicg_pinecone_sv == $key ? ' selected':'').' value="'.$key.'">'.$key.'</option>';
                }
                ?>
                </select>
                <p class="description">Can't find your environment? Let us know at <a href="mailto:support@aipower.org">support@aipower.org</a></p>
            </td>
        </tr>
        <tr>
            <th scope="row">&nbsp;</th>
            <td>
                <button type="button" class="button button-primary wpaicg_pinecone_indexes"><?php echo esc_html__('Sync Indexes','gpt3-ai-content-generator')?></button>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Pinecone Index','gpt3-ai-content-generator')?></th>
            <td>
                <select class="wpaicg_pinecone_environment" name="wpaicg_pinecone_environment" old-value="<?php echo esc_attr($wpaicg_pinecone_environment)?>">
                    <option value=""><?php echo esc_html__('Select Index','gpt3-ai-content-generator')?></option>
                    <?php
                    foreach($wpaicg_pinecone_indexes as $wpaicg_pinecone_index){
                        echo '<option'.($wpaicg_pinecone_environment == $wpaicg_pinecone_index['url'] ? ' selected':'').' value="'.esc_html($wpaicg_pinecone_index['url']).'">'.esc_html($wpaicg_pinecone_index['name']).'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>
    <h3><?php echo esc_html__('Instant Embedding','gpt3-ai-content-generator')?></h3>
    <p><?php echo esc_html__('Enable this option to get instant embeddings for your content. Go to your post, page or products page and select all your contents and click on Instant Embedding button.','gpt3-ai-content-generator')?></p>
    <table class="form-table">
        <tr>
            <th scope="row"><?php echo esc_html__('Enable','gpt3-ai-content-generator')?>:</th>
            <td>
                <div class="mb-5">
                    <label><input<?php echo $wpaicg_instant_embedding == 'yes' ? ' checked':'';?> type="checkbox" name="wpaicg_instant_embedding" value="yes">
                </div>
            </td>
        </tr>
    </table>
    <h3><?php echo esc_html__('Index Builder','gpt3-ai-content-generator')?></h3>
    <p><?php echo esc_html__('You can use index builder to build your index. Difference between index builder and instant embedding is that once you complete the cron job, index builder will monitor your content and will update the index automatically.','gpt3-ai-content-generator')?></p>
    <table class="form-table">
        <tr>
            <th scope="row"><?php echo esc_html__('Cron Indexing','gpt3-ai-content-generator')?></th>
            <td>
                <select name="wpaicg_builder_enable">
                    <option value=""><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    <option<?php echo esc_html($wpaicg_builder_enable) == 'yes' ? ' selected':'';?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Build Index for','gpt3-ai-content-generator')?>:</th>
            <td>
                <div class="mb-5">
                    <div class="mb-5"><label><input<?php echo in_array('post',$wpaicg_builder_types) ? ' checked':'';?> type="checkbox" name="wpaicg_builder_types[]" value="post">&nbsp;<?php echo esc_html__('Posts','gpt3-ai-content-generator')?></label></div>
                    <div class="mb-5"><label><input<?php echo in_array('page',$wpaicg_builder_types) ? ' checked':'';?> type="checkbox" name="wpaicg_builder_types[]" value="page">&nbsp;<?php echo esc_html__('Pages','gpt3-ai-content-generator')?></label></div>
                    <?php
                    if(class_exists('WooCommerce')):
                        ?>
                        <div class="mb-5">
                            <label><input<?php echo in_array('product',$wpaicg_builder_types) ? ' checked':'';?> type="checkbox" name="wpaicg_builder_types[]" value="product">&nbsp;<?php echo esc_html__('Products','gpt3-ai-content-generator')?></label>
                        </div>
                    <?php
                    endif;
                    ?>
                    <?php
                    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
                        include WPAICG_LIBS_DIR.'views/builder/custom_post_type.php';
                    }
                    else{
                        include __DIR__.'/custom_post_type.php';
                    }
                    ?>
                </div>
            </td>
        </tr>
    </table>
    <button class="button button-primary" name="wpaicg_save_builder_settings"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
</form>
<script>
    jQuery(document).ready(function($){
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
        $('.wpaicg_pinecone_indexes').click(function (){
            var btn = $(this);
            var wpaicg_pinecone_api = $('.wpaicg_pinecone_api').val();
            var wpaicg_pinecone_sv = $('.wpaicg_pinecone_sv').val();
            var old_value = $('.wpaicg_pinecone_environment').attr('old-value');
            if(wpaicg_pinecone_api !== '' && wpaicg_pinecone_sv !== ''){
                $.ajax({
                    url: 'https://controller.'+wpaicg_pinecone_sv+'.pinecone.io/databases',
                    headers: {"Api-Key": wpaicg_pinecone_api},
                    dataType: 'json',
                    beforeSend: function (){
                        wpaicgLoading(btn);
                        btn.html('<?php echo esc_html__('Syncing...','gpt3-ai-content-generator')?>');
                    },
                    success: function (res){
                        if(res.length){
                            var selectedLists = [];
                            var totalIndex = res.length;
                            var currentIndex = 0;
                            for(var i=0;i<res.length;i++){
                                currentIndex = i + 1;
                                var indexName = res[i];
                                $.ajax({
                                    url: 'https://controller.'+wpaicg_pinecone_sv+'.pinecone.io/databases/'+indexName,
                                    headers: {"Api-Key": wpaicg_pinecone_api},
                                    dataType: 'json',
                                    success: function(resi){
                                        selectedLists.push({name: indexName,url: resi.status.host});
                                        if(totalIndex === currentIndex){
                                            btn.html('<?php echo esc_html__('Sync Indexes','gpt3-ai-content-generator')?>');
                                            $.post('<?php echo admin_url('admin-ajax.php')?>',{
                                                action: 'wpaicg_pinecone_indexes',
                                                nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>',
                                                indexes: JSON.stringify(selectedLists),
                                                api_key: wpaicg_pinecone_api,
                                                server: wpaicg_pinecone_sv
                                            });
                                            wpaicgRmLoading(btn)
                                            var selectList = '<option value=""><?php echo esc_html__('Select Index','gpt3-ai-content-generator')?></option>';
                                            for(var j=0;j<selectedLists.length;j++){
                                                var selectedList = selectedLists[j];
                                                selectList += '<option'+(old_value === selectedList.url ? ' selected':'')+' value="'+selectedList.url+'">'+selectedList.name+'</option>';
                                            }
                                            $('.wpaicg_pinecone_environment').html(selectList);
                                        }
                                    }
                                })
                            }
                        }
                    },
                    error: function (e){
                        btn.html('<?php echo esc_html__('Sync Indexes','gpt3-ai-content-generator')?>');
                        wpaicgRmLoading(btn);
                        alert(e.responseText);
                    }
                });
            }
            else{
                alert('<?php echo esc_html__('Please add Pinecone API key and Pinecone Environment before start sync','gpt3-ai-content-generator')?>')
            }
        })
    })
</script>
