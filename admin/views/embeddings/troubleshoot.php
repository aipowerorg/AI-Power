<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_openai = \WPAICG\WPAICG_OpenAI::get_instance()->openai();
$wpaicg_pinecone_api = get_option('wpaicg_troubleshoot_pinecone_api',get_option('wpaicg_pinecone_api',''));
$wpaicg_pinecone_env = get_option('wpaicg_troubleshoot_pinecone_env','');
$wpaicg_openai_trouble_api = get_option('wpaicg_openai_trouble_api',$wpaicg_openai->api_key);
?>
<style>
    pre{
        padding: 10px;
        background: #dbdbdb;
        border-radius: 4px;
        max-height: 200px;
        overflow-y: auto;
    }
</style>
<strong>Get Pinecone Indexes</strong>
<div class="wpaicg_pinecone_api_box">
    <p>
        <label>API Key: </label>
        <input value="<?php echo esc_html($wpaicg_pinecone_api)?>" class="wpaicg_pinecone_api" type="text" placeholder="000000-000-000-0000-0000000" />
        <label>Environment: </label>
        <input class="wpaicg_pinecone_environment" type="text" placeholder="us-east1-gcp" value="<?php echo esc_html($wpaicg_pinecone_env)?>" />
        <button class="button button-primary wpaicg_valid_pinecone_api">Get Index</button>
        <button class="button wpaicg_start_pinecone_api">Start Again</button>
    </p>
    <div class="wpaicg_valid_pinecone_result"></div>
</div>
<div class="wpaicg_pinecone_index_box" style="display: none">
    <div class="wpaicg_pinecone_index_list"></div>
</div>
<div class="wpaicg_openai_api_box" style="display: none">
    <p><strong>OpenAI Authentication</strong></p>
    <p>
        <label>OpenAI API Key: </label>
        <input value="<?php echo esc_html($wpaicg_openai_trouble_api)?>" class="wpaicg_openai_api" type="text" placeholder="sk-..." />
        <button class="button button-primary wpaicg_valid_openai_api">Validate</button>
    </p>
</div>
<div id="accordion_troubleshoot" style="display: none">
    <ul>
        <li><a href="#tab-embeddings">Embeddings</a></li>
        <li><a href="#tab-query">Query</a></li>
    </ul>
    <div id="tab-embeddings">
        <div class="wpaicg_valid_openai_api_result"></div>
        <div class="wpaicg_pinecone_test_vectors" style="display: none">
            <h3>Get Vectors</h3>
            <textarea rows="5"></textarea>
            <button class="button button-primary wpaicg_send_content_vectors">Send</button>
            <div class="wpaicg_pinecone_vectors_result"></div>
        </div>
        <div class="wpaicg_pinecone_send_vectors" style="display: none">
            <br>
            <button class="button button-primary wpaicg_pinecone_send_vectors_btn">Add to Pinecone</button>
            <div class="wpaicg_pinecone_send_vectors_result"></div>
            <div class="wpaicg_pinecone_delete_vectors_result"></div>
        </div>
    </div>
    <div id="tab-query">
        <label>Search: </label>
        <input type="text" class="wpaicg_pinecone_query">
        <label>Nearest Answers: </label>
        <input type="number" class="wpaicg_pinecone_topk" value="3" min="1" max="1000">
        <button class="button button-primary wpaicg_pinecone_search">Search</button>
        <div class="wpaicg_pinecone_search_embeddings_result"></div>
        <div class="wpaicg_pinecone_search_result"></div>
    </div>
</div>
<script>
    jQuery( function($) {
        $( "#accordion_troubleshoot" ).tabs();
        var wpaicg_pinecone_api = $('.wpaicg_pinecone_api');
        var accordion_troubleshoot = $('#accordion_troubleshoot');
        var wpaicg_pinecone_environment = $('.wpaicg_pinecone_environment');
        var wpaicg_openai_api = $('.wpaicg_openai_api');
        var wpaicg_valid_openai_api = $('.wpaicg_valid_openai_api');
        var wpaicg_openai_api_box = $('.wpaicg_openai_api_box');
        var wpaicg_valid_openai_api_result = $('.wpaicg_valid_openai_api_result');
        var wpaicg_valid_pinecone_result = $('.wpaicg_valid_pinecone_result');
        var wpaicg_pinecone_index_list = $('.wpaicg_pinecone_index_list');
        var wpaicg_pinecone_delete_vectors_result = $('.wpaicg_pinecone_delete_vectors_result');
        var wpaicg_valid_pinecone_api = $('.wpaicg_valid_pinecone_api');
        var wpaicg_pinone_index_selected = $('.wpaicg_pinone_index_selected');
        var wpaicg_pinecone_index_box = $('.wpaicg_pinecone_index_box');
        var wpaicg_send_content_vectors = $('.wpaicg_send_content_vectors');
        var wpaicg_pinecone_vectors_result = $('.wpaicg_pinecone_vectors_result');
        var wpaicg_pinecone_send_vectors = $('.wpaicg_pinecone_send_vectors');
        var wpaicg_pinecone_send_vectors_btn = $('.wpaicg_pinecone_send_vectors_btn');
        var wpaicg_pinecone_send_vectors_result = $('.wpaicg_pinecone_send_vectors_result');
        var wpaicg_pinecone_test_vectors = $('.wpaicg_pinecone_test_vectors');
        var wpaicg_start_pinecone_api = $('.wpaicg_start_pinecone_api');
        var wpaicg_pinecone_search = $('.wpaicg_pinecone_search');
        var wpaicg_pinecone_topk = $('.wpaicg_pinecone_topk');
        var wpaicg_pinecone_query = $('.wpaicg_pinecone_query');
        var wpaicg_pinecone_search_result = $('.wpaicg_pinecone_search_result');
        var wpaicg_pinecone_search_embeddings_result = $('.wpaicg_pinecone_search_embeddings_result');
        var pinecone_api,pinecone_environment,openai_api,openai_vectors,pinecone_id,search_vectors;
        wpaicg_pinecone_search.click(function (){
            var text = wpaicg_pinecone_query.val();
            wpaicg_pinecone_search_result.empty();
            wpaicg_pinecone_search_embeddings_result.empty();
            var topk = wpaicg_pinecone_topk.val();
            if(text !== ''){
                var data = {input: text, model: 'text-embedding-ada-002'};
                $.ajax({
                    url: 'https://api.openai.com/v1/embeddings',
                    data: JSON.stringify(data),
                    dataType: 'json',
                    contentType: "application/json; charset=utf-8",
                    type: 'POST',
                    headers: {"Authorization": 'Bearer '+openai_api},
                    beforeSend: function (){
                        wpaicgLoading(wpaicg_pinecone_search)
                    },
                    success: function (res){
                        wpaicg_pinecone_search_result.html('<p><strong>Response (Vectors):</strong></p><pre>'+JSON.stringify(res,undefined, 4)+'</pre>');
                        search_vectors = res.data[0].embedding;
                        var data = {vector: search_vectors,topK: topk};
                        var pineconeindexSelected = wpaicg_pinecone_index_list.find('select').val();
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php')?>',
                            data: {
                                action: 'wpaicg_troubleshoot_search',
                                nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>',
                                data: JSON.stringify(data),
                                api_key: pinecone_api,
                                environment: 'https://'+pineconeindexSelected+'/query'
                            },
                            dataType: 'json',
                            type: 'POST',
                            success: function (res){
                                wpaicgRmLoading(wpaicg_pinecone_search);
                                wpaicg_pinecone_search_embeddings_result.html('<p><strong>Response:</strong></p><pre>'+JSON.stringify(res,undefined, 4)+'</pre>');
                            },
                            error: function (e){
                                wpaicgRmLoading(wpaicg_pinecone_search)
                                wpaicg_pinecone_search_embeddings_result.html('<p><strong>Response:</strong></p><pre>'+e.responseText+'</pre>')
                            }
                        })
                    },
                    error: function (e){
                        wpaicgRmLoading(wpaicg_pinecone_search)
                        wpaicg_pinecone_search_result.html('<p><strong>Response:</strong></p><pre>'+e.responseText+'</pre>')
                    }
                })
            }
            else{
                alert('Please enter text for query');
            }
        })
        wpaicg_start_pinecone_api.click(function (){
            wpaicg_pinecone_index_list.empty();
            wpaicg_pinone_index_selected.empty();
            wpaicg_pinecone_delete_vectors_result.empty();
            wpaicg_valid_openai_api_result.empty();
            wpaicg_valid_pinecone_result.empty();
            wpaicg_pinecone_search_result.empty();
            wpaicg_pinecone_search_embeddings_result.empty();
            wpaicg_openai_api_box.hide();
            wpaicg_pinecone_test_vectors.hide();
            wpaicg_pinecone_send_vectors.hide();
            accordion_troubleshoot.hide();
            wpaicg_pinecone_api.removeAttr('disabled');
            wpaicg_pinecone_environment.removeAttr('disabled');
        })
        wpaicg_valid_pinecone_api.click(function(){
            wpaicg_pinecone_index_list.empty();
            wpaicg_pinone_index_selected.empty();
            wpaicg_pinecone_delete_vectors_result.empty();
            wpaicg_valid_openai_api_result.empty();
            wpaicg_valid_pinecone_result.empty();
            wpaicg_openai_api_box.hide();
            wpaicg_pinecone_test_vectors.hide();
            wpaicg_pinecone_send_vectors.hide();
            wpaicg_pinecone_search_result.empty();
            wpaicg_pinecone_search_embeddings_result.empty();
            pinecone_api = wpaicg_pinecone_api.val();
            accordion_troubleshoot.hide();
            pinecone_environment = wpaicg_pinecone_environment.val();
            if(pinecone_api !== '' && pinecone_environment !== ''){
                $.post('<?php echo admin_url('admin-ajax.php')?>',{action: 'wpaicg_troubleshoot_save',nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>',key: 'wpaicg_troubleshoot_pinecone_api',value:pinecone_api});
                $.post('<?php echo admin_url('admin-ajax.php')?>',{action: 'wpaicg_troubleshoot_save',nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>',key: 'wpaicg_troubleshoot_pinecone_env',value:pinecone_environment});
                $.ajax({
                    url: 'https://controller.'+pinecone_environment+'.pinecone.io/databases',
                    headers: {"Api-Key": pinecone_api},
                    dataType: 'json',
                    beforeSend: function (){
                        wpaicgLoading(wpaicg_valid_pinecone_api)
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
                                    url: 'https://controller.'+pinecone_environment+'.pinecone.io/databases/'+indexName,
                                    headers: {"Api-Key": pinecone_api},
                                    dataType: 'json',
                                    success: function(resi){
                                        selectedLists.push({name: indexName,url: resi.status.host});
                                        if(totalIndex === currentIndex){
                                            wpaicgRmLoading(wpaicg_valid_pinecone_api)
                                            wpaicg_valid_pinecone_result.html('<p><strong>Response:</strong></p><pre>'+JSON.stringify(res,undefined, 4)+'</pre>');
                                            wpaicg_pinecone_api.attr('disabled','disabled');
                                            wpaicg_pinecone_environment.attr('disabled','disabled');
                                            var selectList = '<label>Pinecone Index</label>: <select>';
                                            for(var j=0;j<selectedLists.length;j++){
                                                var selectedList = selectedLists[j];
                                                selectList += '<option value="'+selectedList.url+'">'+selectedList.name+'</option>';
                                            }
                                            selectList += '</select>'
                                            wpaicg_pinecone_index_list.html(selectList);
                                            wpaicg_pinecone_index_box.show();
                                            wpaicg_openai_api_box.show();
                                        }
                                    }
                                })
                            }
                        }
                    },
                    error: function (e){
                        wpaicgRmLoading(wpaicg_valid_pinecone_api)
                        wpaicg_valid_pinecone_result.html('<p><strong>Response:</strong></p><pre>'+e.responseText+'</pre>')
                    }
                });
                wpaicg_valid_pinecone_result.show();
            }
            else{
                alert('Please enter your Pinecone API key and Pinecone Environment')
            }
        });
        wpaicg_valid_openai_api.click(function (){
            openai_api = wpaicg_openai_api.val();
            wpaicg_valid_openai_api_result.empty();
            wpaicg_pinecone_vectors_result.empty();
            wpaicg_pinecone_test_vectors.hide();
            wpaicg_pinecone_test_vectors.hide();
            wpaicg_pinecone_send_vectors.hide();
            if(openai_api !== ''){
                $.post('<?php echo admin_url('admin-ajax.php')?>',{action: 'wpaicg_troubleshoot_save',nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>',key: 'wpaicg_openai_trouble_api',value:openai_api});
                $.ajax({
                    url: 'https://api.openai.com/v1/models',
                    headers: {"Authorization": 'Bearer '+openai_api},
                    dataType: 'json',
                    beforeSend: function (){
                        wpaicgLoading(wpaicg_valid_openai_api)
                    },
                    success: function (res){
                        wpaicgRmLoading(wpaicg_valid_openai_api)
                        wpaicg_valid_openai_api_result.html('<p><strong>Response:</strong></p><pre>'+JSON.stringify(res,undefined, 4)+'</pre>');
                        wpaicg_pinecone_test_vectors.show();
                        accordion_troubleshoot.show();
                    },
                    error: function (e){
                        wpaicgRmLoading(wpaicg_valid_openai_api)
                        wpaicg_valid_openai_api_result.html('<p><strong>Response:</strong></p><pre>'+e.responseText+'</pre>')
                    }
                });
                wpaicg_valid_openai_api_result.show();
            }
            else{
                alert('Please enter OpenAI API key');
            }
        });
        wpaicg_send_content_vectors.click(function (){
            var text = wpaicg_pinecone_test_vectors.find('textarea').val();
            wpaicg_pinecone_vectors_result.empty();
            wpaicg_pinecone_send_vectors_result.empty();
            wpaicg_pinecone_send_vectors.hide();
            if(text !== ''){
                var data = {input: text, model: 'text-embedding-ada-002'};
                $.ajax({
                    url: 'https://api.openai.com/v1/embeddings',
                    data: JSON.stringify(data),
                    dataType: 'json',
                    contentType: "application/json; charset=utf-8",
                    type: 'POST',
                    headers: {"Authorization": 'Bearer '+openai_api},
                    beforeSend: function (){
                        wpaicgLoading(wpaicg_send_content_vectors)
                    },
                    success: function (res){
                        wpaicgRmLoading(wpaicg_send_content_vectors)
                        wpaicg_pinecone_vectors_result.html('<p><strong>Response:</strong></p><pre>'+JSON.stringify(res,undefined, 4)+'</pre>');
                        wpaicg_pinecone_send_vectors.show();
                        openai_vectors = res.data[0].embedding;
                    },
                    error: function (e){
                        wpaicgRmLoading(wpaicg_send_content_vectors)
                        wpaicg_pinecone_vectors_result.html('<p><strong>Response:</strong></p><pre>'+e.responseText+'</pre>')
                    }
                })
            }
            else{
                alert('Please insert content for get vectors')
            }
        });
        wpaicg_pinecone_send_vectors_btn.click(function (){
            wpaicg_pinecone_send_vectors_result.empty();
            var pineconeindexSelected = wpaicg_pinecone_index_list.find('select').val();
            if(openai_vectors !== ''){
                pinecone_id = 'test_'+Math.ceil(Math.random()*10000);
                var data = {vectors: [{id: pinecone_id,values: openai_vectors}]};
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {
                        action: 'wpaicg_troubleshoot_add_vector',
                        nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>',
                        data: JSON.stringify(data),
                        api_key: pinecone_api,
                        environment: 'https://'+pineconeindexSelected+'/vectors/upsert'
                    },
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (){
                        wpaicgLoading(wpaicg_pinecone_send_vectors_btn)
                    },
                    success: function (res){
                        wpaicgRmLoading(wpaicg_pinecone_send_vectors_btn);
                        wpaicg_pinecone_send_vectors_result.html('<p><strong>Response:</strong></p><pre>'+JSON.stringify(res,undefined, 4)+'</pre><button class="button wpaicg_pinecone_delete_vectors_btn" style="background: #cc0000;color: #fff;border-color: #cb0404;">Delete Vector</button>');
                    },
                    error: function (e){
                        wpaicgRmLoading(wpaicg_pinecone_send_vectors_btn)
                        wpaicg_pinecone_send_vectors_result.html('<p><strong>Response:</strong></p><pre>'+e.responseText+'</pre>')
                    }
                })
            }
            else{
                alert('Please get vectors from OpenAI first')
            }
        });
        $(document).on('click','.wpaicg_pinecone_delete_vectors_btn',function(ev){
            var btn = $(ev.currentTarget);
            var pineconeindexSelected = wpaicg_pinecone_index_list.find('select').val();
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: {
                    action: 'wpaicg_troubleshoot_delete_vector',
                    nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>',
                    data: JSON.stringify({ids: [pinecone_id]}),
                    api_key: pinecone_api,
                    environment: 'https://' + pineconeindexSelected + '/vectors/delete'
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function () {
                    wpaicgLoading(btn)
                },
                success: function (res) {
                    wpaicgRmLoading(btn);
                    wpaicg_pinecone_delete_vectors_result.html('<p><strong>Response:</strong></p><pre>' + JSON.stringify(res, undefined, 4) + '</pre>');
                },
                error: function (e) {
                    wpaicgRmLoading(btn)
                    wpaicg_pinecone_delete_vectors_result.html('<p><strong>Response:</strong></p><pre>' + e.responseText + '</pre>')
                }
            })
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
    } );
</script>
