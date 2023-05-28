<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Embeddings')) {
    class WPAICG_Embeddings
    {
        private static  $instance = null ;
        public $wpaicg_max_file_size = 10485760;

        public static function get_instance()
        {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action('wp_ajax_wpaicg_embeddings',[$this,'wpaicg_embeddings']);
            add_action( 'admin_menu', array( $this, 'wpaicg_menu' ) );
            add_action('init',[$this,'wpaicg_cron_job'],9999);
            add_action('wp_ajax_wpaicg_builder_reindex',[$this,'wpaicg_builder_reindex']);
            add_action('wp_ajax_wpaicg_builder_delete',[$this,'wpaicg_builder_delete']);
            add_action('wp_ajax_wpaicg_builder_list',[$this,'wpaicg_builder_list']);
            add_action('wp_ajax_wpaicg_reindex_embeddings',[$this,'wpaicg_reindex_embeddings']);
            add_action('wp_ajax_wpaicg_delete_embeddings',[$this,'wpaicg_delete_embeddings']);
            add_action('wp_ajax_wpaicg_reindex_builder_data',[$this,'wpaicg_reindex_builder_data']);
            $wpaicg_instant_embedding = get_option('wpaicg_instant_embedding','yes');
            if($wpaicg_instant_embedding == 'yes'){
                add_action('manage_posts_extra_tablenav',[$this,'wpaicg_instant_embedding_button']);
                add_action('admin_footer',[$this,'wpaicg_instant_embedding_footer']);
                add_action('wp_ajax_wpaicg_instant_embedding',[$this,'wpaicg_instant_embedding']);
            }
            /*Pinecone sync Indexes*/
            add_action('wp_ajax_wpaicg_pinecone_indexes',[$this,'wpaicg_pinecone_indexes']);
        }

        public function wpaicg_pinecone_indexes()
        {
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                die(WPAICG_NONCE_ERROR);
            }
            $indexes = sanitize_text_field(str_replace("\\",'',$_REQUEST['indexes']));
            update_option('wpaicg_pinecone_indexes',$indexes);
            if(isset($_REQUEST['api_key']) && !empty($_REQUEST['api_key'])){
                update_option('wpaicg_pinecone_api', sanitize_text_field($_REQUEST['api_key']));
            }
            if(isset($_REQUEST['server']) && !empty($_REQUEST['server'])){
                update_option('wpaicg_pinecone_sv', sanitize_text_field($_REQUEST['server']));
            }
        }

        public function wpaicg_instant_embedding()
        {
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Missing ID request','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                $id = sanitize_text_field($_REQUEST['id']);
                $wpaicg_data = get_post($id);
                if($wpaicg_data){
                    $result = $this->wpaicg_builder_data($wpaicg_data);
                    if($result == 'success'){
                        $wpaicg_result['status'] = 'success';
                    }
                    else{
                        $wpaicg_result['msg'] = $result;
                    }
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('Data not found','gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_instant_embedding_footer()
        {
            ?>
            <script>
                jQuery(document).ready(function ($){
                    let wpaicgInstantAjax = false;
                    let wpaicgInstantWorking = true;
                    let wpaicgInstantSuccess = 0;
                    $(document).on('click', '.wpaicg-instant-embedding-cancel', function (){
                        wpaicgInstantWorking = false;
                        if(wpaicgInstantAjax){
                            wpaicgInstantAjax.abort();
                        }
                        let pendings = $('.wpaicg-instant-pending');
                        pendings.find('.wpaicg-instant-embedding-status').html('<?php echo esc_html__('Cancelled','gpt3-ai-content-generator')?>');
                        pendings.find('.wpaicg-instant-embedding-status').css({
                            'font-style': 'normal',
                            'font-weight': 'bold',
                            'color': '#e30000'
                        })
                        $('.wpaicg_modal_close').show();
                        $('.wpaicg-instant-embedding-cancel').hide();
                    });
                    function wpaicgInstantEmbedding(start,ids){
                        let id = ids[start];
                        let nextId = start+1;
                        let embedding = $('#wpaicg-instant-embedding-'+id);
                        if(wpaicgInstantWorking) {
                            wpaicgInstantAjax = $.ajax({
                                url: '<?php echo admin_url('admin-ajax.php')?>',
                                data: {action: 'wpaicg_instant_embedding', id: id,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                                type: 'POST',
                                dataType: 'JSON',
                                success: function (res) {
                                    if (res.status === 'success') {
                                        wpaicgInstantSuccess += 1;
                                        $('.wpaicg-embedding-remain').html(wpaicgInstantSuccess+'/'+ids.length);
                                        embedding.css({
                                            'background-color': '#cde5dd'
                                        });
                                        embedding.removeClass('wpaicg-instant-pending');
                                        embedding.find('.wpaicg-instant-embedding-status').html('<?php echo esc_html__('Indexed','gpt3-ai-content-generator')?>');
                                        embedding.find('.wpaicg-instant-embedding-status').css({
                                            'font-style': 'normal',
                                            'font-weight': 'bold',
                                            'color': '#008917'
                                        })
                                    } else {
                                        embedding.css({
                                            'background-color': '#e5cdcd'
                                        });
                                        embedding.find('.wpaicg-instant-embedding-status').html('<?php echo esc_html__('Error','gpt3-ai-content-generator')?>');
                                        embedding.find('.wpaicg-instant-embedding-status').css({
                                            'font-style': 'normal',
                                            'font-weight': 'bold',
                                            'color': '#e30000'
                                        })
                                        embedding.append('<div style="color: #e30000;font-size: 12px;">' + res.msg + '</div>');
                                    }
                                    if (nextId < ids.length) {
                                        wpaicgInstantEmbedding(nextId, ids);
                                    } else {
                                        $('.wpaicg_modal_close').show();
                                        $('.wpaicg-instant-embedding-cancel').hide();
                                    }
                                },
                                error: function () {
                                    embedding.css({
                                        'background-color': '#e5cdcd'
                                    });
                                    embedding.find('.wpaicg-instant-embedding-status').html('<?php echo esc_html__('Error','gpt3-ai-content-generator')?>');
                                    embedding.find('.wpaicg-instant-embedding-status').css({
                                        'font-style': 'normal',
                                        'font-weight': 'bold',
                                        'color': '#e30000'
                                    })
                                    embedding.append('<div style="color: #e30000;font-size: 12px;"><?php echo esc_html__('Either something went wrong or you cancelled it.','gpt3-ai-content-generator')?></div>');
                                    if (nextId < ids.length) {
                                        wpaicgInstantEmbedding(nextId, ids);
                                    } else {
                                        $('.wpaicg_modal_close').show();
                                        $('.wpaicg-instant-embedding-cancel').hide();
                                    }
                                }
                            })
                        }
                    }
                    $('.wpaicg-instan-embedding-btn').click(function (){
                        let form = $(this).closest('#posts-filter');
                        let ids = [];
                        let titles = {};
                        form.find('.wp-list-table th.check-column input[type=checkbox]:checked').each(function (idx, item){
                            let post_id = $(item).val();
                            ids.push(post_id);
                            let row = form.find('#post-'+post_id);
                            let post_name = row.find('.column-title .row-title').text();
                            if(post_name === ''){
                                post_name = row.find('.column-name .row-title').text();
                            }
                            titles[post_id] = post_name.trim();
                        });
                        if(ids.length === 0){
                            alert('<?php echo esc_html__('Please select data to index','gpt3-ai-content-generator')?>');
                        }
                        else{
                            let html = '';
                            wpaicgInstantWorking = true;
                            wpaicgInstantSuccess = 0;
                            $('.wpaicg_modal_title').html('<?php echo esc_html__('Instant Embedding','gpt3-ai-content-generator')?><span style="font-weight: bold;font-size: 16px;background: #fba842;padding: 1px 5px;border-radius: 3px;display: inline-block;margin-left: 6px;color: #222;" class="wpaicg-embedding-remain">0/'+ids.length+'</span>');
                            $('.wpaicg_modal').css({
                                top: '5%',
                                height: '90%'
                            })
                            $('.wpaicg_modal_content').css({
                                'max-height': 'calc(100% - 103px)',
                                'overflow-y': 'auto'
                            })
                            $.each(ids, function(idx, id){
                                html += '<div class="wpaicg-instant-pending" id="wpaicg-instant-embedding-'+id+'" style="background: #ebebeb;border-radius: 3px;padding: 5px;margin-bottom: 5px;border: 1px solid #dfdfdf;"><div style="display: flex; justify-content: space-between;"><span>'+titles[id]+'</span><span style="font-style: italic" class="wpaicg-instant-embedding-status"><?php echo esc_html__('Indexing...','gpt3-ai-content-generator')?></span></div></div>';
                            });
                            html += '<div style="text-align: center"><button class="button button-link-delete wpaicg-instant-embedding-cancel"><?php echo esc_html__('Cancel','gpt3-ai-content-generator')?></button></div>';
                            $('.wpaicg_modal_content').html(html);
                            $('.wpaicg-overlay').show();
                            $('.wpaicg_modal').show();
                            $('.wpaicg_modal_close').hide();
                            wpaicgInstantEmbedding(0,ids);
                        }
                    })
                })
            </script>
            <?php
        }

        public function wpaicg_instant_embedding_button($which)
        {
            global $post_type;
            $post_types = array('post','page','product');
            if(wpaicg_util_core()->wpaicg_is_pro()) {
                $wpaicg_all_post_types = get_post_types(array(
                    'public' => true,
                    '_builtin' => false,
                ), 'array');
                $post_types = wp_parse_args($post_types, array_keys($wpaicg_all_post_types));
            }
            if(in_array($post_type,$post_types)):
                if(current_user_can('wpaicg_instant_embedding')):
            ?>
            <div class="alignleft actions">
                <a style="height: 32px" href="javascript:void(0)" class="button button-primary wpaicg-instan-embedding-btn"><?php echo esc_html__('Instant Embedding','gpt3-ai-content-generator')?></a>
            </div>
            <?php
                endif;
            endif;
        }

        public function wpaicg_reindex_builder_data()
        {
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            $ids = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['ids']);
            if(count($ids)){
                foreach ($ids as $id){
                    update_post_meta($id,'wpaicg_indexed','reindex');
                }
            }
        }

        public function wpaicg_delete_embeddings()
        {
            $wpaicg_result = array('status' => 'success');
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }

            $ids = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['ids']);
            $this->wpaicg_delete_embeddings_ids($ids);
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_reindex_embeddings_ids($ids)
        {
            foreach($ids as $id){
                update_post_meta($id,'wpaicg_embeddings_reindex',1);
            }
        }

        public function wpaicg_delete_embeddings_ids($ids)
        {
            global $wpdb;
            $wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
            $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
            try {
                $headers = array(
                    'Content-Type' => 'application/json',
                    'Api-Key' => $wpaicg_pinecone_api
                );
                $pinecone_ids = '';
                foreach ($ids as $id){
                    $pinecone_ids .= empty($pinecone_ids) ? 'ids='.$id : '&ids='.$id;
                }
                $response = wp_remote_request('https://' . $wpaicg_pinecone_environment . '/vectors/delete?'.$pinecone_ids, array(
                    'method' => 'DELETE',
                    'headers' => $headers
                ));
            }
            catch (\Exception $exception){

            }
            foreach ($ids as $id){
                wp_delete_post($id);
            }
        }

        public function wpaicg_reindex_embeddings()
        {
            $wpaicg_result = array('status' => 'success');
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            $ids = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['ids']);
            $this->wpaicg_reindex_embeddings_ids($ids);
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_builder_list()
        {
            global $wpdb;
            $wpaicg_result = array('status' => 'success', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            $wpaicg_embedding_page = isset($_REQUEST['wpage']) && !empty($_REQUEST['wpage']) ? sanitize_text_field($_REQUEST['wpage']) : 1;
            $wpaicg_embeddings = new \WP_Query(array(
                'post_type' => 'wpaicg_builder',
                'posts_per_page' => 40,
                'paged' => $wpaicg_embedding_page,
                'order' => 'DESC',
                'orderby' => 'meta_value',
                'meta_key' => 'wpaicg_start'
            ));
            ob_start();
            if($wpaicg_embeddings->have_posts()){
                foreach ($wpaicg_embeddings->posts as $wpaicg_embedding){
                    include WPAICG_PLUGIN_DIR.'admin/views/embeddings/builder_item.php';
                }
            }
            $wpaicg_result['html'] = ob_get_clean();
            ob_start();
            echo paginate_links( array(
                'base'         => admin_url('admin.php?page=wpaicg_embeddings&action=builder&wpage=%#%'),
                'total'        => $wpaicg_embeddings->max_num_pages,
                'current'      => $wpaicg_embedding_page,
                'format'       => '?wpage=%#%',
                'show_all'     => false,
                'prev_next'    => false,
                'add_args'     => false,
            ));
            $wpaicg_result['paginate'] = ob_get_clean();
            $wpaicg_builder_types = get_option('wpaicg_builder_types',[]);
            $wpaicg_result['types'] = array();
            if($wpaicg_builder_types && is_array($wpaicg_builder_types) && count($wpaicg_builder_types)){
                foreach($wpaicg_builder_types as $wpaicg_builder_type){
                    $sql_count_data = $wpdb->prepare("SELECT COUNT(p.ID) FROM ".$wpdb->posts." p WHERE p.post_type=%s AND p.post_status = 'publish'",$wpaicg_builder_type);
                    $total_data = $wpdb->get_var($sql_count_data);
                    $sql_done_data = $wpdb->prepare("SELECT COUNT(p.ID) FROM ".$wpdb->postmeta." m LEFT JOIN ".$wpdb->posts." p ON p.ID=m.post_id WHERE p.post_type=%s AND p.post_status = 'publish' AND m.meta_key='wpaicg_indexed' AND m.meta_value IN ('error','skip','yes')",$wpaicg_builder_type);
                    $total_converted = $wpdb->get_var($sql_done_data);
                    if($total_data > 0) {
                        $percent_process = ceil($total_converted * 100 / $total_data);
                        $wpaicg_result['types'][] = array(
                            'type' => $wpaicg_builder_type,
                            'text' => $total_converted.'/'.$total_data,
                            'percent' => $percent_process
                        );
                    }
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_builder_delete()
        {
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['id']) && !empty($_POST['id'])) {
                $id = sanitize_text_field($_POST['id']);
                $wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
                $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
                if(empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment)){
                    $wpaicg_result['msg'] = esc_html__('Missing Pinecone API Settings','gpt3-ai-content-generator');
                }
                else {
                    $headers = array(
                        'Content-Type' => 'application/json',
                        'Api-Key' => $wpaicg_pinecone_api
                    );
                    $response = wp_remote_get('https://'.$wpaicg_pinecone_environment.'/databases',array(
                        'headers' => $headers
                    ));
                    if(is_wp_error($response)){
                        $wpaicg_result['msg'] = $response->get_error_message();
                        return $wpaicg_result;
                    }
                    $response_code = $response['response']['code'];
                    if($response_code !== 200){
                        $wpaicg_result['msg'] = $response['body'];
                        return $wpaicg_result;
                    }
                    $response = wp_remote_request('https://' . $wpaicg_pinecone_environment . '/vectors/delete?ids='.$id, array(
                        'method' => 'DELETE',
                        'headers' => $headers
                    ));
                    if(is_wp_error($response)){
                        $wpaicg_result['msg'] = $response->get_error_message();
                    }
                    else{
                        wp_delete_post($id);
                        $wpaicg_result['status'] = 'success';
                    }
                }

            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_builder_reindex()
        {
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $id = sanitize_text_field($_POST['id']);
                $parent_id = get_post_meta($id,'wpaicg_parent',true);
                if($parent_id && get_post($parent_id)){
                    update_post_meta($id,'wpaicg_indexed','reindex');
                    update_post_meta($parent_id,'wpaicg_indexed','reindex');
                    $wpaicg_result['status'] = 'success';
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('Data need convert has been deleted','gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_cron_job()
        {
            if(isset($_SERVER['argv']) && is_array($_SERVER['argv']) && count($_SERVER['argv'])){
                foreach( $_SERVER['argv'] as $arg ) {
                    $e = explode( '=', $arg );
                    if($e[0] == 'wpaicg_builder') {
                        if (count($e) == 2)
                            $_GET[$e[0]] = sanitize_text_field($e[1]);
                        else
                            $_GET[$e[0]] = 0;
                    }
                }
            }
            if(isset($_GET['wpaicg_builder']) && sanitize_text_field($_GET['wpaicg_builder']) == 'yes'){
                $wpaicg_running = WPAICG_PLUGIN_DIR.'/wpaicg_builder.txt';
                if(!file_exists($wpaicg_running)) {
                    $wpaicg_file = fopen($wpaicg_running, "a") or die("Unable to open file!");
                    $txt = 'running';
                    fwrite($wpaicg_file, $txt);
                    fclose($wpaicg_file);
                    try {
                        $_SERVER["REQUEST_METHOD"] = 'GET';
                        chmod($wpaicg_running,0755);
                        $this->wpaicg_builer();
                    }
                    catch (\Exception $exception){
                        $wpaicg_error = WPAICG_PLUGIN_DIR.'wpaicg_error.txt';
                        $wpaicg_file = fopen($wpaicg_error, "a") or die("Unable to open file!");
                        $txt = $exception->getMessage();
                        fwrite($wpaicg_file, $txt);
                        fclose($wpaicg_file);

                    }
                    @unlink($wpaicg_running);
                }
                exit;
            }
        }

        public function wpaicg_print_array($arr, $pad = 0, $padStr = "\t")
        {
            $outerPad = $pad;
            $innerPad = $pad + 1;
            $out = '[';
            foreach ($arr as $k => $v) {
                if (is_array($v)) {
                    $out .= str_repeat($padStr, $innerPad) . $k . ': ' . $this->wpaicg_print_array($v, $innerPad);
                } else {
                    $out .= str_repeat($padStr, $innerPad) . $k . ': ' . $v;
                }
            }
            $out .= str_repeat($padStr, $outerPad) . ']';
            return $out;
        }

        public function wpaicg_custom_post_type($content, $post)
        {
            if(!in_array($post->post_type, array('post','page','product'))){
                $wpaicg_custom_post_fields = get_option('wpaicg_builder_custom_'.$post->post_type,'');
                $new_content = '';
                if(!empty($wpaicg_custom_post_fields)){
                    $exs = explode('||',$wpaicg_custom_post_fields);
                    foreach($exs as $ex){
                        $item = explode('##',$ex);
                        if($item && is_array($item) && count($item) == 2){
                            $key = $item[0];
                            $name = $item[1];
                            /*Check is standard field*/
                            if(substr($key,0,8) == 'wpaicgp_'){
                                $post_key = str_replace('wpaicgp_','',$key);
                                if($post_key == 'post_content'){
                                    $post_value = $content;
                                }
                                elseif($post_key == 'post_date'){
                                    $post_value = get_the_date('', $post->ID);
                                }
                                elseif($post_key == 'post_parent'){
                                    $post_value = get_the_title($post->post_parent);
                                }
                                elseif($post_key == 'permalink'){
                                    $post_value = get_permalink($post->ID);
                                }
                                else{
                                    $post_value = $post->$post_key;
                                }
                                $new_content .= (empty($new_content) ? '': "\n"). $name.': '.$post_value;
                            }
                            /*Check if Custom Meta*/
                            if(substr($key,0,9) == 'wpaicgcf_'){
                                $meta_key = str_replace('wpaicgcf_','',$key);
                                $meta_value = get_post_meta($post->ID,$meta_key,true);
                                $meta_value = apply_filters('wpaicg_meta_value_embedding',$meta_value,$post,$meta_key);
                                if(is_array($meta_value)){
                                    $meta_value = $this->wpaicg_print_array($meta_value);
                                }
                                $new_content .= (empty($new_content) ? '': "\n"). $name.': '.$meta_value;
                            }
                            /*Check if is author fields*/
                            if(substr($key,0,13) == 'wpaicgauthor_'){
                                $user_key = str_replace('wpaicgauthor_','',$key);
                                $author = get_user_by('ID',$post->post_author);
                                $new_content .= (empty($new_content) ? '': "\n"). $name.': '.$author->$user_key;
                            }
                            /*Check Taxonomies*/
                            if(substr($key,0,9) == 'wpaicgtx_'){
                                $taxonomy = str_replace('wpaicgtx_','',$key);
                                $terms = get_the_terms($post->ID,$taxonomy);
                                if(!is_wp_error($terms)){
                                    $terms_string = join(', ', wp_list_pluck($terms, 'name'));
                                    if(!empty($terms_string)){
                                        $new_content .= (empty($new_content) ? '': "\n"). $name.': '.$terms_string;
                                    }
                                }
                            }
                        }
                    }
                    if(empty($new_content)){
                        $new_content .= esc_html__('Post Title','gpt3-ai-content-generator').': '.$post->post_title;
                        $new_content .= "\n".esc_html__('Post Content','gpt3-ai-content-generator').': '.$content;
                    }
                }
                else{
                    $new_content .= esc_html__('Post Title','gpt3-ai-content-generator').': '.$post->post_title;
                    $new_content .= "\n".esc_html__('Post Content','gpt3-ai-content-generator').': '.$content;
                }
                $content = $new_content;
            }
            return $content;
        }

        public function wpaicg_builder_data($wpaicg_data)
        {
            global $wpdb;
            $wpaicg_content = $wpaicg_data->post_content;
            preg_match_all('@\[([^<>&/\[\]\x00-\x20=]++)@', $wpaicg_content, $matches);
            if ($matches && is_array($matches) && count($matches)) {
                $pattern = get_shortcode_regex($matches[1]);
                $wpaicg_content = preg_replace_callback("/$pattern/", 'strip_shortcode_tag', $wpaicg_content);
            }
            $wpaicg_content = trim($wpaicg_content);
            $wpaicg_content = preg_replace("/<((?:style)).*>.*<\/style>/si", ' ',$wpaicg_content);
            $wpaicg_content = preg_replace("/<((?:script)).*>.*<\/script>/si", ' ',$wpaicg_content);
            $wpaicg_content = preg_replace('/<a(.*)href="([^"]*)"(.*)>(.*?)<\/a>/i', '$2', $wpaicg_content);
            $wpaicg_content = strip_tags($wpaicg_content);
            $wpaicg_content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $wpaicg_content);
            $wpaicg_content = trim($wpaicg_content);
            if (empty($wpaicg_content)) {
                update_post_meta($wpaicg_data->ID, 'wpaicg_indexed', 'skip');
                return 'Empty content or probably a shortcode';
            } else {
                /*Check If is Re-Index*/
                $check = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key='wpaicg_parent' AND meta_value=%d",$wpaicg_data->ID));
                $wpaicg_old_builder = false;
                if ($check) {
                    $wpaicg_old_builder = $check->post_id;
                }
                /*Check if old index exist*/
                $wpaicg_old_index_builder = get_post($check->post_id);
                if(!$wpaicg_old_index_builder){
                    $wpaicg_old_builder = false;
                }
                /*For Post*/
                if($wpaicg_data->post_type == 'post'){
                    $wpaicg_new_content = esc_html__('Post Title','gpt3-ai-content-generator').': '.$wpaicg_data->post_title."\n";
                    $wpaicg_new_content .= esc_html__('Post Content','gpt3-ai-content-generator').': '.$wpaicg_content."\n";
                    $wpaicg_new_content .= esc_html__('Post URL','gpt3-ai-content-generator').': '.get_permalink($wpaicg_data->ID);
                    /*Categories*/
                    $categories_name = wp_get_post_categories($wpaicg_data->ID, array('fields' => 'names'));
                    if($categories_name && is_array($categories_name) && count($categories_name)){
                        $wpaicg_new_content .= "\n".esc_html__('Post Categories','gpt3-ai-content-generator').": ".implode(',',$categories_name);
                    }
                    $wpaicg_content = $wpaicg_new_content;
                }
                /*For Page*/
                if($wpaicg_data->post_type == 'page'){
                    $wpaicg_new_content = esc_html__('Page Title','gpt3-ai-content-generator').': '.$wpaicg_data->post_title."\n";
                    $wpaicg_new_content .= esc_html__('Page Content','gpt3-ai-content-generator').': '.$wpaicg_content."\n";
                    $wpaicg_new_content .= esc_html__('Page URL','gpt3-ai-content-generator').': '.get_permalink($wpaicg_data->ID);
                    $wpaicg_content = $wpaicg_new_content;
                }
                /*For Product*/
                if($wpaicg_data->post_type == 'product' && class_exists('WC_Product_Factory')){
                    $wooFac = new \WC_Product_Factory();
                    $wpaicg_product = $wooFac->get_product($wpaicg_data->ID);
                    if($wpaicg_product) {
                        $wpaicg_content_product = '';
                        $product_sku = $wpaicg_product->get_sku();
                        if (!empty($product_sku)) {
                            $wpaicg_content_product .= esc_html__('Product SKU','gpt3-ai-content-generator').': ' . $product_sku . "\n";
                        }
                        $product_title = $wpaicg_product->get_title();
                        $wpaicg_content_product .= esc_html__('Product Name','gpt3-ai-content-generator').': ' . $product_title . "\n";
                        $wpaicg_content_product .= esc_html__('Product Description','gpt3-ai-content-generator').': ' . $wpaicg_content . "\n";
                        if(!empty($wpaicg_data->post_excerpt)){
                            $wpaicg_content_product .= esc_html__('Product Short Description','gpt3-ai-content-generator').': ' . $wpaicg_data->post_excerpt . "\n";
                        }
                        $product_url = $wpaicg_product->get_permalink();
                        $wpaicg_content_product .= esc_html__('Product URL','gpt3-ai-content-generator').': ' . $product_url . "\n";
                        $product_regular_price = $wpaicg_product->get_regular_price();
                        if (!empty($product_regular_price)) {
                            $wpaicg_content_product .= esc_html__('Product Regular Price','gpt3-ai-content-generator').": " . $product_regular_price.' '.get_option('woocommerce_currency','USD') . "\n";
                        }
                        $product_sale_price = $wpaicg_product->get_sale_price();
                        if (!empty($product_sale_price)) {
                            $wpaicg_content_product .= esc_html__('Product Sale Price','gpt3-ai-content-generator').': ' . $product_sale_price.' '.get_option('woocommerce_currency','USD') . "\n";
                        }
                        $product_tax_status = $wpaicg_product->get_tax_status();
                        if (!empty($product_tax_status)) {
                            $wpaicg_content_product .= esc_html__('Tax Status','gpt3-ai-content-generator').': ' . $product_tax_status . "\n";
                        }
                        $product_tax_class = $wpaicg_product->get_tax_class();
                        if (!empty($product_tax_class)) {
                            $wpaicg_content_product .= esc_html__('Tax Class','gpt3-ai-content-generator').': ' . $product_tax_class . "\n";
                        }
                        $product_external_url = '';
                        if ($wpaicg_product->get_type() == 'external') {
                            $product_external_url = $product_url;
                        }
                        if (!empty($product_external_url)) {
                            $wpaicg_content_product .= esc_html__('External Product URL','gpt3-ai-content-generator').': ' . $product_tax_class . "\n";
                        }
                        $product_shipping_weight = $wpaicg_product->get_weight();
                        if (!empty($product_shipping_weight)) {
                            $wpaicg_content_product .= esc_html__('Shipping Weight','gpt3-ai-content-generator').': ' . $product_shipping_weight .' '.get_option('woocommerce_weight_unit','oz'). "\n";
                        }
                        $product_dimensions = '';
                        if (!empty($wpaicg_product->get_length()) || !empty($wpaicg_product->get_width()) || !empty($wpaicg_product->get_height())) {
                            $dimension_unit = get_option('woocommerce_dimension_unit','cm');
                            $product_dimensions = $wpaicg_product->get_length() .$dimension_unit. ', ' . $wpaicg_product->get_width().$dimension_unit . ', ' . $wpaicg_product->get_height().$dimension_unit;
                        }
                        if (!empty($product_dimensions)) {
                            $wpaicg_content_product .= esc_html__('Dimensions','gpt3-ai-content-generator').': ' . $product_dimensions . "\n";
                        }
                        $product_stock_status = $wpaicg_product->get_stock_status();
                        $stock_status_options = wc_get_product_stock_status_options();
                        if(isset($stock_status_options[$product_stock_status]) && !empty($stock_status_options[$product_stock_status])){
                            $wpaicg_content_product .= esc_html__('Stock Status','gpt3-ai-content-generator').': '.$stock_status_options[$product_stock_status]."\n";
                        }
                        $product_attributes = $wpaicg_product->get_attributes();
                        if ($product_attributes && is_array($product_attributes) && count($product_attributes)) {
                            $wpaicg_content_product .= esc_html__('Custom Product Attributes','gpt3-ai-content-generator').': ';
                            foreach ($product_attributes as $keyx => $att) {
                                $options = $att->get_options();
                                $wpaicg_content_product .= $att->get_name() . ': ';
                                foreach ($options as $key => $option) {
                                    $wpaicg_content_product .= $key == 0 ? $option : ',' . $option;
                                }
                                if ($key + 1 == count($options)) {
                                    $wpaicg_content_product .= '; ';
                                }
                            }
                            $wpaicg_content_product .= "\n";
                        }
                        $wpaicg_content = $wpaicg_content_product;
                    }
                }
                /*For custom post type*/
                $wpaicg_content = $this->wpaicg_custom_post_type($wpaicg_content,$wpaicg_data);
                $wpaicg_content = apply_filters('wpaicg_embedding_content_custom_post_type',$wpaicg_content,$wpaicg_data);
                /*End for custom post_type*/
                $wpaicg_result = $this->wpaicg_save_embedding($wpaicg_content, 'wpaicg_builder', $wpaicg_data->post_title, $wpaicg_old_builder);
                if ($wpaicg_result && is_array($wpaicg_result) && isset($wpaicg_result['status'])) {
                    if ($wpaicg_result['status'] == 'error') {
                        /*
                         * If save embedding error
                         * */
                        if ($wpaicg_old_builder) {
                            $embedding_id = $wpaicg_old_builder;
                        } else {
                            $embedding_data = array(
                                'post_type' => 'wpaicg_builder',
                                'post_title' => $wpaicg_data->post_title,
                                'post_content' => $wpaicg_content,
                                'post_status' => 'publish'
                            );
                            $embedding_id = wp_insert_post($embedding_data);
                        }
                        update_post_meta($wpaicg_data->ID, 'wpaicg_indexed', 'error');
                        update_post_meta($embedding_id, 'wpaicg_indexed', 'error');
                        update_post_meta($embedding_id, 'wpaicg_source', $wpaicg_data->post_type);
                        update_post_meta($embedding_id, 'wpaicg_parent', $wpaicg_data->ID);
                        update_post_meta($embedding_id, 'wpaicg_error_msg', $wpaicg_result['msg']);
                        return $wpaicg_result['msg'];
                    } else {
                        update_option('wpaicg_crojob_builder_content',time());
                        wp_update_post(array(
                            'ID' => $wpaicg_result['id'],
                            'post_content' => $wpaicg_content
                        ));
                        update_post_meta($wpaicg_data->ID, 'wpaicg_indexed', 'yes');
                        update_post_meta($wpaicg_result['id'], 'wpaicg_indexed', 'yes');
                        update_post_meta($wpaicg_result['id'], 'wpaicg_source', $wpaicg_data->post_type);
                        update_post_meta($wpaicg_result['id'], 'wpaicg_parent', $wpaicg_data->ID);
                        return 'success';
                    }
                } else {
                    if ($wpaicg_old_builder) {
                        $embedding_id = $wpaicg_old_builder;
                    } else {
                        $embedding_data = array(
                            'post_type' => 'wpaicg_builder',
                            'post_title' => $wpaicg_data->post_title,
                            'post_content' => $wpaicg_content,
                            'post_status' => 'publish'
                        );
                        $embedding_id = wp_insert_post($embedding_data);
                    }
                    update_post_meta($embedding_id, 'wpaicg_source', $wpaicg_data->post_type);
                    update_post_meta($embedding_id, 'wpaicg_parent', $wpaicg_data->ID);
                    update_post_meta($wpaicg_data->ID, 'wpaicg_indexed', 'error');
                    update_post_meta($embedding_id, 'wpaicg_indexed', 'error');
                    update_post_meta($embedding_id, 'wpaicg_error_msg', esc_html__('Something went wrong','gpt3-ai-content-generator'));
                    return esc_html__('Something went wrong','gpt3-ai-content-generator');
                }
            }
        }

        public function wpaicg_builer()
        {
            global $wpdb;
            $wpaicg_cron_added = get_option( 'wpaicg_cron_builder_added', '' );
            if(empty($wpaicg_cron_added)){
                update_option( 'wpaicg_cron_builder_added', time() );
            }
            else {
                $wpaicg_has_builder_run = false;
                update_option( 'wpaicg_crojob_builder_last_time', time() );
                $wpaicg_builder_types = get_option('wpaicg_builder_types', []);
                $wpaicg_builder_enable = get_option('wpaicg_builder_enable', '');
                if ($wpaicg_builder_enable == 'yes' && is_array($wpaicg_builder_types) && count($wpaicg_builder_types)) {
                    $commaDelimitedPlaceholders = implode(',', array_fill(0, count($wpaicg_builder_types), '%s'));
                    $wpaicg_sql = $wpdb->prepare("SELECT p.ID,p.post_title, p.post_content,p.post_type,p.post_excerpt,p.post_date,p.post_parent,p.post_status,p.post_author FROM " . $wpdb->posts . " p LEFT JOIN " . $wpdb->postmeta . " m ON m.post_id=p.ID AND m.meta_key='wpaicg_indexed' WHERE (m.meta_value IS NULL OR m.meta_value='' OR m.meta_value='reindex') AND p.post_content!='' AND p.post_type IN ($commaDelimitedPlaceholders) AND p.post_status = 'publish' ORDER BY p.ID ASC LIMIT 1",$wpaicg_builder_types);
                    $wpaicg_data = $wpdb->get_row($wpaicg_sql);
                    if($wpaicg_data) {
                        $wpaicg_has_builder_run = true;
                        $this->wpaicg_builder_data($wpaicg_data);
                    }
                }
                if(!$wpaicg_has_builder_run){
                    // wpaicg_embeddings_reindex
                    $wpaicg_embedding_data = $wpdb->get_row("SELECT p.* FROM ".$wpdb->posts." p LEFT JOIN ".$wpdb->postmeta." m ON m.post_id=p.ID WHERE p.post_type='wpaicg_embeddings' AND m.meta_key='wpaicg_embeddings_reindex' AND m.meta_value=1");
                    if($wpaicg_embedding_data){
                        $wpaicg_result = $this->wpaicg_save_embedding($wpaicg_embedding_data->post_content,'','', $wpaicg_embedding_data->ID);
                        if($wpaicg_result['status'] == 'success'){
                            delete_post_meta($wpaicg_embedding_data->ID,'wpaicg_embeddings_reindex');
                        }
                    }
                }
            }
        }

        public function wpaicg_menu()
        {
            add_submenu_page(
                'wpaicg',
                esc_html__('Embeddings','gpt3-ai-content-generator'),
                esc_html__('Embeddings','gpt3-ai-content-generator'),
                'wpaicg_embeddings',
                'wpaicg_embeddings',
                array( $this, 'wpaicg_main' ),
                7
            );
        }

        public function wpaicg_main()
        {
            include WPAICG_PLUGIN_DIR.'admin/views/embeddings/index.php';
        }

        public function wpaicg_save_embedding($content, $post_type = '', $title = '', $embeddings_id = false)
        {
            global $wpdb;
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            $openai = WPAICG_OpenAI::get_instance()->openai();
            $content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content);
            if($openai){
                $wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
                $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
                if(empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment)){
                    $wpaicg_result['msg'] = esc_html__('Missing Pinecone API Settings','gpt3-ai-content-generator');
                }
                else{
                    $headers = array(
                        'Content-Type' => 'application/json',
                        'Api-Key' => $wpaicg_pinecone_api
                    );
                    /*Check Pinecone API*/
                    $response = wp_remote_get('https://'.$wpaicg_pinecone_environment.'/databases',array(
                        'headers' => $headers
                    ));
                    if(is_wp_error($response)){
                        $wpaicg_result['msg'] = $response->get_error_message();
                        return $wpaicg_result;
                    }

                    $response_code = $response['response']['code'];
                    if($response_code !== 200){
                        $wpaicg_result['msg'] = $response['response']['message'];
                        return $wpaicg_result;
                    }
                    $response = $openai->embeddings(array(
                        'input' => $content,
                        'model' => 'text-embedding-ada-002'
                    ));
                    $response = json_decode($response,true);
                    if(isset($response['error']) && !empty($response['error'])) {
                        $wpaicg_result['msg'] = $response['error']['message'];
                        if(empty($wpaicg_result['msg']) && isset($response['error']['code']) && $response['error']['code'] == 'invalid_api_key'){
                            $wpaicg_result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                        }
                    }
                    else{
                        $embedding = $response['data'][0]['embedding'];
                        if(empty($embedding)){
                            $wpaicg_result['msg'] = esc_html__('No data returned','gpt3-ai-content-generator');
                        }
                        else{
                            $pinecone_url = 'https://' . $wpaicg_pinecone_environment . '/vectors/upsert';
                            if(!$embeddings_id) {
                                $embedding_title = empty($title) ? substr($content, 0, 50) : $title;
                                $embedding_data = array(
                                    'post_type' => 'wpaicg_embeddings',
                                    'post_title' => $embedding_title,
                                    'post_content' => $content,
                                    'post_status' => 'publish'
                                );
                                if (!empty($post_type)) {
                                    $embedding_data['post_type'] = $post_type;
                                }
                                $embeddings_id = wp_insert_post($embedding_data);
                                if(isset($_REQUEST['type']) && !empty($_REQUEST['type'])){
                                    add_post_meta($embeddings_id,'wpaicg_embedding_type',sanitize_text_field($_REQUEST['type']));
                                }
                            }
                            if(is_wp_error($embeddings_id)){
                                $wpaicg_result['msg'] = $embeddings_id->get_error_message();
                            }
                            else {
                                update_post_meta($embeddings_id,'wpaicg_start',time());
                                $usage_tokens = $response['usage']['total_tokens'];
                                add_post_meta($embeddings_id, 'wpaicg_embedding_token', $usage_tokens);
                                $vectors = array(
                                    array(
                                        'id' => (string)$embeddings_id,
                                        'values' => $embedding
                                    )
                                );
                                $response = wp_remote_post($pinecone_url, array(
                                    'headers' => $headers,
                                    'body' => json_encode(array('vectors' => $vectors))
                                ));
                                if(is_wp_error($response)){
                                    $wpaicg_result['msg'] = $response->get_error_message();
                                    wp_delete_post($embeddings_id);
                                    $wpdb->delete($wpdb->postmeta, array(
                                        'meta_value' => $embeddings_id,
                                        'meta_key' => 'wpaicg_parent'
                                    ));
                                }
                                else{
                                    $body = json_decode($response['body'],true);
                                    if($body){
                                        if(isset($body['code']) && isset($body['message'])){
                                            $wpaicg_result['msg'] = strip_tags($body['message']);
                                            wp_delete_post($embeddings_id);
                                            $wpdb->delete($wpdb->postmeta, array(
                                                'meta_value' => $embeddings_id,
                                                'meta_key' => 'wpaicg_parent'
                                            ));
                                        }
                                        else{
                                            $wpaicg_result['status'] = 'success';
                                            $wpaicg_result['id'] = $embeddings_id;
                                            update_post_meta($embeddings_id,'wpaicg_completed',time());
                                        }
                                    }
                                    else{
                                        $wpaicg_result['msg'] = esc_html__('No data returned','gpt3-ai-content-generator');
                                        wp_delete_post($embeddings_id);
                                        $wpdb->delete($wpdb->postmeta, array(
                                            'meta_value' => $embeddings_id,
                                            'meta_key' => 'wpaicg_parent'
                                        ));
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else{
                $wpaicg_result['msg'] = esc_html__('Missing OpenAI API Settings','gpt3-ai-content-generator');
            }
            return $wpaicg_result;
        }

        public function wpaicg_embeddings()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wpaicg_embeddings_save' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['content']) && !empty($_POST['content'])){
                $content = wp_kses_post(strip_tags($_POST['content']));
                if(!empty($content)){
                    $wpaicg_result = $this->wpaicg_save_embedding($content);
                }
                else $wpaicg_result['msg'] = esc_html__('Please insert content','gpt3-ai-content-generator');
            }
            wp_send_json($wpaicg_result);
        }
    }
    WPAICG_Embeddings::get_instance();
}
