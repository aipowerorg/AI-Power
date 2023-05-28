<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Google_Speech')) {
    class WPAICG_Google_Speech
    {
        public $api_key;
        public $languages = array(
            'af-ZA' => 'Afrikaans (Suid-Afrika)',
            'ar-XA' => 'Arabic, multi-region',
            'id-ID' => 'Bahasa Indonesia (Indonesia)',
            'ms-MY' => 'Bahasa Melayu (Malaysia)',
            'ca-ES' => 'Català (Espanya)',
            'da-DK' => 'Dansk (Danmark)',
            'de-DE' => 'Deutsch (Deutschland)',
            'en-AU' => 'English (Australia)',
            'en-GB' => 'English (Great Britain)',
            'en-IN' => 'English (India)',
            'en-US' => 'English (United States)',
            'es-ES' => 'Español (España)',
            'es-US' => 'Español (Estados Unidos)',
            'eu-ES' => 'Euskara (Espainia)',
            'fil-PH' => 'Filipino (Pilipinas)',
            'fr-CA' => 'Français (Canada)',
            'fr-FR' => 'Français (France)',
            'gl-ES' => 'Galego (España)',
            'it-IT' => 'Italiano (Italia)',
            'lv-LV' => 'Latviešu (latviešu)',
            'lt-LT' => 'Lietuvių (Lietuva)',
            'hu-HU' => 'Magyar (Magyarország)',
            'nl-NL' => 'Nederlands (Nederland)',
            'nb-NO' => 'Norsk bokmål (Norge)',
            'pl-PL' => 'Polski (Polska)',
            'pt-BR' => 'Português (Brasil)',
            'pt-PT' => 'Português (Portugal)',
            'ro-RO' => 'Română (România)',
            'sk-SK' => 'Slovenčina (Slovensko)',
            'fi-FI' => 'Suomi (Suomi)',
            'sv-SE' => 'Svenska (Sverige)',
            'vi-VN' => 'Tiếng Việt (Việt Nam)',
            'tr-TR' => 'Türkçe (Türkiye)',
            'is-IS' => 'Íslenska (Ísland)',
            'cs-CZ' => 'Čeština (Česká republika)',
            'el-GR' => 'Ελληνικά (Ελλάδα)',
            'bg-BG' => 'Български (България)',
            'ru-RU' => 'Русский (Россия)',
            'sr-RS' => 'Српски (Србија)',
            'uk-UA' => 'Українська (Україна)',
            'he-IL' => 'עברית (ישראל)',
            'mr-IN' => 'मराठी (भारत)',
            'hi-IN' => 'हिन्दी (भारत)',
            'bn-IN' => 'বাংলা (ভারত)',
            'gu-IN' => 'ગુજરાતી (ભારત)',
            'ta-IN' => 'தமிழ் (இந்தியா)',
            'te-IN' => 'తెలుగు (భారతదేశం)',
            'kn-IN' => 'ಕನ್ನಡ (ಭಾರತ)',
            'ml-IN' => 'മലയാളം (ഇന്ത്യ)',
            'th-TH' => 'ไทย (ประเทศไทย)',
            'cmn-TW' => '國語 (台灣)',
            'yue-HK' => '廣東話 (香港)',
            'ja-JP' => '日本語（日本)',
            'cmn-CN' => '普通话 (中国大陆)',
            'ko-KR' => '한국어 (대한민국)'
        );
        public $url = 'https://texttospeech.googleapis.com/v1/';
        private static  $instance = null ;

        public static function get_instance()
        {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action('wp_ajax_wpaicg_google_voices', [$this,'voices']);
            add_action('wp_ajax_wpaicg_sync_google_voices', [$this,'wpaicg_sync_google_voices']);
            add_action('wp_ajax_wpaicg_google_speech', [$this,'speech']);
            add_action('wp_ajax_nopriv_wpaicg_google_speech', [$this,'speech']);
        }

        public function devices()
        {
            return array(
                '' => __('Default','gpt3-ai-content-generator'),
                'wearable-class-device' => __('Smart watch or wearable','gpt3-ai-content-generator'),
                'handset-class-device' => __('Smartphone','gpt3-ai-content-generator'),
                'headphone-class-device' => __('Headphones or earbuds','gpt3-ai-content-generator'),
                'small-bluetooth-speaker-class-device' => __('Small home speaker','gpt3-ai-content-generator'),
                'medium-bluetooth-speaker-class-device' => __('Smart home speaker','gpt3-ai-content-generator'),
                'large-home-entertainment-class-device' => __('Home entertainment system or smart TV','gpt3-ai-content-generator'),
                'large-automotive-class-device' => __('Car speaker','gpt3-ai-content-generator'),
                'telephony-class-application' => __('Interactive Voice Response (IVR) system','gpt3-ai-content-generator')
            );
        }

        public function wpaicg_sync_google_voices()
        {
            $result = array('status' => 'error', 'msg' => __('Missing Google API Key','gpt3-ai-content-generator'));
            if ( !wp_verify_nonce( sanitize_text_field($_REQUEST['nonce']), 'wpaicg_sync_google_voices' ) ) {
                $result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($result);
                exit;
            }
            $apiKey = get_option('wpaicg_google_api_key','');
            if(!empty($apiKey)) {
                $google_voices = array();
                foreach ($this->languages as $key=>$language) {
                    $response = wp_remote_get($this->url.'voices?languageCode='.$key.'&key='.$apiKey);
                    if(is_wp_error($response)){
                        $result['status'] = 'error';
                        $result['msg'] = $response->get_error_message();
                        break;
                    }
                    else{
                        $body = wp_remote_retrieve_body($response);
                        $body = json_decode($body,true);
                        if(isset($body['error'])){
                            $result['status'] = 'error';
                            $result['msg'] = $body['error']['message'];
                            break;
                        }
                        else{
                            $result['status'] = 'success';
                            $google_voices[$key] = $body['voices'];
                        }
                    }
                }
                $result['voices'] = $google_voices;
                update_option('wpaicg_google_voices',$google_voices);
            }
            wp_send_json($result);
        }

        public function voices()
        {
            $result = array('status' => 'error', 'msg' => __('Missing Google API Key','gpt3-ai-content-generator'));
            if ( !wp_verify_nonce( sanitize_text_field($_REQUEST['nonce']), 'wpaicg-ajax-action' ) ) {
                $result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($result);
                exit;
            }
            $apiKey = get_option('wpaicg_google_api_key','');
            $language = isset($_REQUEST['language']) && !empty($_REQUEST['language']) ? sanitize_text_field($_REQUEST['language']) : 'en-US';
            if(!empty($apiKey)){
                $response = wp_remote_get($this->url.'voices?languageCode='.$language.'&key='.$apiKey);
                if(is_wp_error($response)){
                    $result['msg'] = $response->get_error_message();
                }
                else{
                    $body = wp_remote_retrieve_body($response);
                    $body = json_decode($body,true);
                    if(isset($body['error'])){
                        $result['msg'] = $body['error']['message'];
                    }
                    else{
                        $result['status'] = 'success';
                        $result['voices'] = $body['voices'];
                    }
                }
            }
            wp_send_json($result);
        }

        public function speech()
        {
            $result = array('status' => 'error','msg' => __('Missing parameters','gpt3-ai-content-generator'));
            $language = 'en-US';
            $voiceName = 'en-US-Studio-M';
            $device = '';
            $speed = 1;
            $pitch = 0;
            $apiKey = get_option('wpaicg_google_api_key','');
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-chatbox' ) ) {
                $result['msg'] = WPAICG_NONCE_ERROR;
                wp_send_json($result);
            }
            if(empty($apiKey)){
                $result['msg'] = __('Missing Google API Key','gpt3-ai-content-generator');
                wp_send_json($result);
            }
            if(isset($_REQUEST['language']) && !empty($_REQUEST['language'])){
                $language = sanitize_text_field($_REQUEST['language']);
                if(!isset($this->languages[$language])){
                    $language = 'en-US';
                }
            }
            if(isset($_REQUEST['name']) && !empty($_REQUEST['name'])) {
                $voiceName = sanitize_text_field($_REQUEST['name']);
            }
            if(isset($_REQUEST['device']) && !empty($_REQUEST['device'])) {
                $device = sanitize_text_field($_REQUEST['device']);
            }
            if(isset($_REQUEST['speed']) && !empty($_REQUEST['speed'])) {
                $speed = sanitize_text_field($_REQUEST['speed']);
            }
            if(isset($_REQUEST['pitch']) && !empty($_REQUEST['pitch'])) {
                $speed = sanitize_text_field($_REQUEST['pitch']);
            }
            if(isset($_REQUEST['text']) && !empty($_REQUEST['text'])){
                $text = sanitize_text_field($_REQUEST['text']);
                $text = str_replace("\\",'',$text);
                $params = array(
                    'audioConfig' => array(
                        'audioEncoding' => 'LINEAR16',
                        'pitch' => $pitch,
                        'speakingRate' => $speed,
                    ),
                    'input' => array(
                        'text' => $text
                    ),
                    'voice' => array(
                        'languageCode' => $language,
                        'name' => $voiceName
                    )
                );
                if(!empty($device)){
                    $params['audioConfig']['effectsProfileId'] = array($device);
                }
                $response = wp_remote_post($this->url.'text:synthesize?fields=audioContent&key='.$apiKey,array(
                    'headers' => array(
                        'Content-Type' => 'application/json'
                    ),
                    'body' => json_encode($params),
                    'timeout' => 1000
                ));
                if(is_wp_error($response)){
                    $result['msg'] = $response->get_error_message();
                }
                else{
                    $body = wp_remote_retrieve_body($response);
                    $body = json_decode($body,true);
                    if(isset($body['error'])){
                        $result['msg'] = $body['error']['message'];
                    }
                    elseif(isset($body['audioContent']) && !empty($body['audioContent'])){
                        $result['audio'] = $body['audioContent'];
                        $result['status'] = 'success';
                    }
                    else{
                        $result['msg'] = __('Google does not return audio','gpt3-ai-content-generator');
                    }
                }
            }
            wp_send_json($result);
        }
    }
    WPAICG_Google_Speech::get_instance();
}
