<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
class WPAICG_Url
{
    const ORIGIN = 'https://api.openai.com';
    const API_VERSION = 'v1';
    const OPEN_AI_URL = self::ORIGIN . "/" . self::API_VERSION;

    /**
     * @deprecated
     * @param string $engine
     * @return string
     */
    public static function completionURL(string $engine): string
    {
        return self::OPEN_AI_URL . "/engines/$engine/completions";
    }

    /**
     * @return string
     */
    public static function completionsURL(): string
    {
        return self::OPEN_AI_URL . "/completions";
    }

    /**
     *
     * @return string
     */
    public static function editsUrl(): string
    {
        return self::OPEN_AI_URL . "/edits";
    }

    /**
     * @param string $engine
     * @return string
     */
    public static function searchURL(string $engine): string
    {
        return self::OPEN_AI_URL . "/engines/$engine/search";
    }

    /**
     * @param
     * @return string
     */
    public static function enginesUrl(): string
    {
        return self::OPEN_AI_URL . "/engines";
    }

    /**
     * @param string $engine
     * @return string
     */
    public static function engineUrl(string $engine): string
    {
        return self::OPEN_AI_URL . "/engines/$engine";
    }

    /**
     * @param
     * @return string
     */
    public static function classificationsUrl(): string
    {
        return self::OPEN_AI_URL . "/classifications";
    }

    /**
     * @param
     * @return string
     */
    public static function moderationUrl(): string
    {
        return self::OPEN_AI_URL . "/moderations";
    }

    /**
     * @param
     * @return string
     */
    public static function filesUrl(): string
    {
        return self::OPEN_AI_URL . "/files";
    }

    /**
     * @param
     * @return string
     */
    public static function fineTuneUrl(): string
    {
        return self::OPEN_AI_URL . "/fine-tunes";
    }

    /**
     * @param
     * @return string
     */
    public static function chatUrl(): string
    {
        return self::OPEN_AI_URL . "/chat/completions";
    }

    /**
     * @param
     * @return string
     */
    public static function fineTuneModel(): string
    {
        return self::OPEN_AI_URL . "/models";
    }

    /**
     * @param
     * @return string
     */
    public static function answersUrl(): string
    {
        return self::OPEN_AI_URL . "/answers";
    }

    /**
     * @param
     * @return string
     */
    public static function imageUrl(): string
    {
        return self::OPEN_AI_URL . "/images";
    }

    /**
     * @param
     * @return string
     */
    public static function transcriptionsUrl(): string
    {
        return self::OPEN_AI_URL . "/audio/transcriptions";
    }

    /**
     * @param
     * @return string
     */
    public static function translationsUrl(): string
    {
        return self::OPEN_AI_URL . "/audio/translations";
    }

    /**
     * @param
     * @return string
     */
    public static function embeddings(): string
    {
        return self::OPEN_AI_URL . "/embeddings";
    }
}

if (!class_exists('\\WPAICG\\WPAICG_OpenAI')){
    class WPAICG_OpenAI
    {
        private  static $instance = null ;
        private $engine = "davinci";
        private $model = "text-davinci-003";
        private $headers;
        public $response;

        private $timeout = 200;
        private $stream_method;

        public static function get_instance()
        {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function openai()
        {
            global $wpdb;
            $wpaicgTable = $wpdb->prefix . 'wpaicg';
            $sql = $wpdb->prepare( 'SELECT * FROM ' . $wpaicgTable . ' where name=%s','wpaicg_settings' );
            $wpaicg_settings = $wpdb->get_row( $sql, ARRAY_A );
            if($wpaicg_settings && isset($wpaicg_settings['api_key']) && !empty($wpaicg_settings['api_key'])){
                add_action('http_api_curl', array($this, 'filterCurlForStream'));
                $this->headers = [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$wpaicg_settings['api_key'],
                ];
                unset($wpaicg_settings['ID']);
                unset($wpaicg_settings['name']);
                unset($wpaicg_settings['added_date']);
                unset($wpaicg_settings['modified_date']);
                foreach($wpaicg_settings as $key=>$wpaicg_setting){
                    $this->$key = $wpaicg_setting;
                }
                return $this;
            }
            else return false;
        }

        public function filterCurlForStream($handle)
        {
            if ($this->stream_method !== null){
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($handle, CURLOPT_WRITEFUNCTION, function ($curl_info, $data) {
                    return call_user_func($this->stream_method, $this, $data);
                });
            }
        }

        public function listModels()
        {
            $url = WPAICG_Url::fineTuneModel();

            return $this->sendRequest($url, 'GET');
        }

        public function retrieveModel($model)
        {
            $model = "/$model";
            $url = WPAICG_Url::fineTuneModel() . $model;

            return $this->sendRequest($url, 'GET');
        }

        public function setResponse($content="")
        {
            $this->response = $content;
        }

        public function complete($opts)
        {
            $engine = $opts['engine'] ?? $this->engine;
            $url = WPAICG_Url::completionURL($engine);
            unset($opts['engine']);

            return $this->sendRequest($url, 'POST', $opts);
        }

        public function completion($opts, $stream = null)
        {
            if ($stream != null && array_key_exists('stream', $opts)) {
                if (! $opts['stream']) {
                    throw new \Exception(
                        'Please provide a stream function.'
                    );
                }
                $this->stream_method = $stream;
            }

            $opts['model'] = $opts['model'] ?? $this->model;
            $url = WPAICG_Url::completionsURL();

            return $this->sendRequest($url, 'POST', $opts);
        }

        public function chat($opts, $stream = null)
        {
            if ($stream != null && array_key_exists('stream', $opts)) {
                if (! $opts['stream']) {
                    throw new \Exception(
                        'Please provide a stream function.'
                    );
                }
                $this->stream_method = $stream;
            }

            $opts['model'] = $opts['model'] ?? $this->model;

            $url = WPAICG_Url::chatUrl();
            return $this->sendRequest($url, 'POST', $opts);
        }

        public function transcriptions($opts)
        {
            $url = WPAICG_Url::transcriptionsUrl();
            return $this->sendRequest($url, 'POST', $opts);
        }

        public function translations($opts)
        {
            $url = WPAICG_Url::translationsUrl();
            return $this->sendRequest($url, 'POST', $opts);
        }

        public function createEdit($opts)
        {
            $url = WPAICG_Url::editsUrl();

            return $this->sendRequest($url, 'POST', $opts);
        }

        public function image($opts)
        {
            $url = WPAICG_Url::imageUrl() . "/generations";

            return $this->sendRequest($url, 'POST', $opts);
        }

        public function imageEdit($opts)
        {
            $url = WPAICG_Url::imageUrl() . "/edits";

            return $this->sendRequest($url, 'POST', $opts);
        }

        public function createImageVariation($opts)
        {
            $url = WPAICG_Url::imageUrl() . "/variations";

            return $this->sendRequest($url, 'POST', $opts);
        }

        public function search($opts)
        {
            $engine = $opts['engine'] ?? $this->engine;
            $url = WPAICG_Url::searchURL($engine);
            unset($opts['engine']);

            return $this->sendRequest($url, 'POST', $opts);
        }

        public function answer($opts)
        {
            $url = WPAICG_Url::answersUrl();
            return $this->sendRequest($url, 'POST', $opts);
        }

        public function classification($opts)
        {
            $url = WPAICG_Url::classificationsUrl();

            return $this->sendRequest($url, 'POST', $opts);
        }

        public function moderation($opts)
        {
            $url = WPAICG_Url::moderationUrl();

            return $this->sendRequest($url, 'POST', $opts);
        }

        public function uploadFile($opts)
        {
            $url = WPAICG_Url::filesUrl();

            return $this->sendRequest($url, 'POST', $opts);
        }

        public function retrieveFile($file_id)
        {
            $file_id = "/$file_id";
            $url = WPAICG_Url::filesUrl() . $file_id;

            return $this->sendRequest($url, 'GET');
        }

        public function retrieveFileContent($file_id)
        {
            $file_id = "/$file_id/content";
            $url = WPAICG_Url::filesUrl() . $file_id;

            return $this->sendRequest($url, 'GET');
        }

        public function deleteFile($file_id)
        {
            $file_id = "/$file_id";
            $url = WPAICG_Url::filesUrl() . $file_id;

            return $this->sendRequest($url, 'DELETE');
        }

        public function createFineTune($opts)
        {
            $url = WPAICG_Url::fineTuneUrl();

            return $this->sendRequest($url, 'POST', $opts);
        }

        public function listFineTunes()
        {
            $url = WPAICG_Url::fineTuneUrl();

            return $this->sendRequest($url, 'GET');
        }

        public function retrieveFineTune($fine_tune_id)
        {
            $fine_tune_id = "/$fine_tune_id";
            $url = WPAICG_Url::fineTuneUrl() . $fine_tune_id;

            return $this->sendRequest($url, 'GET');
        }

        /**
         * @param $fine_tune_id
         * @return bool|string
         */
        public function cancelFineTune($fine_tune_id)
        {
            $fine_tune_id = "/$fine_tune_id/cancel";
            $url = WPAICG_Url::fineTuneUrl() . $fine_tune_id;

            return $this->sendRequest($url, 'POST');
        }

        /**
         * @param $fine_tune_id
         * @return bool|string
         */
        public function listFineTuneEvents($fine_tune_id)
        {
            $fine_tune_id = "/$fine_tune_id/events";
            $url = WPAICG_Url::fineTuneUrl() . $fine_tune_id;

            return $this->sendRequest($url, 'GET');
        }

        /**
         * @param $fine_tune_id
         * @return bool|string
         */
        public function deleteFineTune($fine_tune_id)
        {
            $fine_tune_id = "/$fine_tune_id";
            $url = WPAICG_Url::fineTuneModel() . $fine_tune_id;

            return $this->sendRequest($url, 'DELETE');
        }

        /**
         * @param
         * @return bool|string
         * @deprecated
         */
        public function engines()
        {
            $url = WPAICG_Url::enginesUrl();

            return $this->sendRequest($url, 'GET');
        }

        /**
         * @param $engine
         * @return bool|string
         * @deprecated
         */
        public function engine($engine)
        {
            $url = WPAICG_Url::engineUrl($engine);

            return $this->sendRequest($url, 'GET');
        }

        /**
         * @param $opts
         * @return bool|string
         */
        public function embeddings($opts)
        {
            $url = WPAICG_Url::embeddings();

            return $this->sendRequest($url, 'POST', $opts);
        }

        /**
         * @param int $timeout
         */
        public function setTimeout(int $timeout)
        {
            $this->timeout = $timeout;
        }

        public function create_body_for_file($file, $boundary)
        {
            $fields = array(
                'purpose' => 'fine-tune',
                'file' => $file['filename']
            );

            $body = '';
            foreach ($fields as $name => $value) {
                $body .= "--$boundary\r\n";
                $body .= "Content-Disposition: form-data; name=\"$name\"";
                if ($name == 'file') {
                    $body .= "; filename=\"{$value}\"\r\n";
                    $body .= "Content-Type: application/json\r\n\r\n";
                    $body .= $file['data'] . "\r\n";
                } else {
                    $body .= "\r\n\r\n$value\r\n";
                }
            }
            $body .= "--$boundary--\r\n";
            return $body;
        }

        public function create_body_for_audio($file, $boundary, $fields)
        {
            $fields['file'] = $file['filename'];
            unset($fields['audio']);
            $body = '';
            foreach ($fields as $name => $value) {
                $body .= "--$boundary\r\n";
                $body .= "Content-Disposition: form-data; name=\"$name\"";
                if ($name == 'file') {
                    $body .= "; filename=\"{$value}\"\r\n";
                    $body .= "Content-Type: application/json\r\n\r\n";
                    $body .= $file['data'] . "\r\n";
                } else {
                    $body .= "\r\n\r\n$value\r\n";
                }
            }
            $body .= "--$boundary--\r\n";
            return $body;
        }

        public function listFiles()
        {
            $url = WPAICG_Url::filesUrl();

            return $this->sendRequest($url, 'GET');
        }

        /**
         * @param string $url
         * @param string $method
         * @param array $opts
         * @return bool|string
         */
        private function sendRequest(string $url, string $method, array $opts = [])
        {
            $post_fields = json_encode($opts);
            if (array_key_exists('file', $opts)) {
                $boundary = wp_generate_password(24, false);
                $this->headers['Content-Type'] = 'multipart/form-data; boundary='.$boundary;
                $post_fields = $this->create_body_for_file($opts['file'], $boundary);
            }
            elseif (array_key_exists('audio', $opts)) {
                $boundary = wp_generate_password(24, false);
                $this->headers['Content-Type'] = 'multipart/form-data; boundary='.$boundary;
                $post_fields = $this->create_body_for_audio($opts['audio'], $boundary, $opts);
            } else {
                $this->headers['Content-Type'] = 'application/json';
            }
            $stream = false;
            if (array_key_exists('stream', $opts) && $opts['stream']) {
                $stream = true;
            }
            $request_options = array(
                'timeout' => $this->timeout,
                'headers' => $this->headers,
                'method' => $method,
                'body' => $post_fields,
                'stream' => $stream
            );
            if($post_fields == '[]'){
                unset($request_options['body']);
            }
            $response = wp_remote_request($url,$request_options);
            if(is_wp_error($response)){
                return json_encode(array('error' => array('message' => $response->get_error_message())));
            }
            else{
                if ($stream){
                    return $this->response;
                }
                else{
                    return wp_remote_retrieve_body($response);
                }
            }
        }
    }
}
