<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Search')) {
    class WPAICG_Search
    {
        private static $instance = null;

        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_shortcode('wpaicg_search',[$this,'wpaicg_search']);
            add_action('wp_ajax_wpaicg_search_data',[$this,'wpaicg_search_data']);
            add_action('wp_ajax_nopriv_wpaicg_search_data',[$this,'wpaicg_search_data']);
        }

        public function wpaicg_search_data()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            $open_ai = WPAICG_OpenAI::get_instance()->openai();
            if (!$open_ai) {
                $wpaicg_result['msg'] = esc_html__('Missing API Setting','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
                exit;
            }
            $wpaicg_nonce = sanitize_text_field($_REQUEST['_wpnonce']);
            if ( !wp_verify_nonce( $wpaicg_nonce, 'wpaicg-chatbox' ) ) {
                $wpaicg_result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($wpaicg_result);
                exit;
            }
            $wpaicg_search = isset( $_REQUEST['search'] ) && !empty($_REQUEST['search']) ? sanitize_text_field( $_REQUEST['search'] ) : '';
            if(empty($wpaicg_search)){
                $wpaicg_result['msg'] = esc_html(__('Nothing to search','gpt3-ai-content-generator'));
                wp_send_json($wpaicg_result);
                exit;
            }
            $wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
            $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
            $wpaicg_search_no_result = get_option('wpaicg_search_no_result','5');
            $wpaicg_embeddings_result = $this->wpaicg_embeddings_result($open_ai,$wpaicg_pinecone_api, $wpaicg_pinecone_environment, $wpaicg_search, $wpaicg_search_no_result);
            $wpaicg_result['status'] = $wpaicg_embeddings_result['status'];
            if($wpaicg_embeddings_result['status'] == 'error'){
                $wpaicg_result['msg'] = $wpaicg_embeddings_result['data'];
            }
            else if(is_array($wpaicg_embeddings_result['data'])){
                $ids = $wpaicg_embeddings_result['data'];
                $wpaicg_result['data'] = array();
                $wpaicg_result['source'] = array();
                foreach ($ids as $key=>$post_id){
                    $wpaicg_key = $key+1;
                    $embedding = get_post($post_id);
                    if($embedding){
                        ob_start();
                        include WPAICG_PLUGIN_DIR.'admin/views/search/item.php';
                        $wpaicg_result['data'][] = ob_get_clean();
                    }
                }
            }
            else{
                $wpaicg_result['msg'] = esc_html(__('No result found','gpt3-ai-content-generator'));
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_embeddings_result($open_ai,$wpaicg_pinecone_api,$wpaicg_pinecone_environment,$wpaicg_message, $wpaicg_chat_embedding_top)
        {
            $result = array('status' => 'error','data' => '');
            if(!empty($wpaicg_pinecone_api) && !empty($wpaicg_pinecone_environment) ) {
                $response = $open_ai->embeddings([
                    'input' => $wpaicg_message,
                    'model' => 'text-embedding-ada-002'
                ]);
                $response = json_decode($response, true);
                if (isset($response['error']) && !empty($response['error'])) {
                    $result['data'] = $response['error']['message'];
                    if(empty($result['msg']) && isset($response['error']['code']) && $response['error']['code'] == 'invalid_api_key'){
                        $result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                    }
                } else {
                    $result['data'] = esc_html(__('No result found','gpt3-ai-content-generator'));
                    $embedding = $response['data'][0]['embedding'];
                    if (!empty($embedding)) {
                        $headers = array(
                            'Content-Type' => 'application/json',
                            'Api-Key' => $wpaicg_pinecone_api
                        );
                        $response = wp_remote_post('https://' . $wpaicg_pinecone_environment . '/query', array(
                            'headers' => $headers,
                            'body' => json_encode(array(
                                'vector' => $embedding,
                                'topK' => $wpaicg_chat_embedding_top
                            ))
                        ));
                        if (is_wp_error($response)) {
                            $result['data'] = esc_html($response->get_error_message());
                        } else {
                            $body = json_decode($response['body'], true);
                            if ($body) {
                                if (isset($body['matches']) && is_array($body['matches']) && count($body['matches'])) {
                                    $result['data'] = array();
                                    foreach($body['matches'] as $match){
                                        $result['data'][] = $match['id'];
                                    }
                                    $result['status'] = 'success';
                                }
                            }
                        }
                    }
                }
            }
            else{
                $result['data'] = esc_html__('Missing PineCone Settings','gpt3-ai-content-generator');
            }
            return $result;
        }

        public function wpaicg_search()
        {
            ob_start();
            include WPAICG_PLUGIN_DIR.'admin/views/search/shortcode.php';
            return ob_get_clean();
        }
    }
    WPAICG_Search::get_instance();
}
