<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$categories = get_terms(array(
    'hide_empty' => false,
    'taxonomy' => 'category'
));
$wpaicg_languages = array(
    'en' => 'English',
    'af' => 'Afrikaans',
    'ar' => 'Arabic',
    'hy' => 'Armenian',
    'az' => 'Azerbaijani',
    'be' => 'Belarusian',
    'bs' => 'Bosnian',
    'bg' => 'Bulgarian',
    'ca' => 'Catalan',
    'zh' => 'Chinese',
    'hr' => 'Croatian',
    'cs' => 'Czech',
    'da' => 'Danish',
    'nl' => 'Dutch',
    'et' => 'Estonian',
    'fi' => 'Finnish',
    'fr' => 'French',
    'gl' => 'Galician',
    'de' => 'German',
    'el' => 'Greek',
    'he' => 'Hebrew',
    'hi' => 'Hindi',
    'hu' => 'Hungarian',
    'is' => 'Icelandic',
    'id' => 'Indonesian',
    'it' => 'Italian',
    'ja' => 'Japanese',
    'kn' => 'Kannada',
    'kk' => 'Kazakh',
    'ko' => 'Korean',
    'lv' => 'Latvian',
    'lt' => 'Lithuanian',
    'mk' => 'Macedonian',
    'ms' => 'Malay',
    'mr' => 'Marathi',
    'mi' => 'Maori',
    'ne' => 'Nepali',
    'no' => 'Norwegian',
    'fa' => 'Persian',
    'pl' => 'Polish',
    'pt' => 'Portuguese',
    'ro' => 'Romanian',
    'ru' => 'Russian',
    'sr' => 'Serbian',
    'sk' => 'Slovak',
    'sl' => 'Slovenian',
    'es' => 'Spanish',
    'sw' => 'Swahili',
    'sv' => 'Swedish',
    'tl' => 'Tagalog',
    'ta' => 'Tamil',
    'th' => 'Thai',
    'tr' => 'Turkish',
    'uk' => 'Ukrainian',
    'ur' => 'Urdu',
    'vi' => 'Vietnamese',
    'cy' => 'Welsh'
);
$wpaicg_authors = get_users();
$wpaicg_audio_default_settings = array(
    'purpose' => 'transcriptions',
    'type' => 'upload',
    'model' => 'whisper-1',
    'response' => 'post',
    'category' => '',
    'status' => 'draft',
    'temperature' => '',
    'language' => 'en',
    'author' => '',
);
$wpaicg_audio_settings = get_option('wpaicg_audio_setting');
$wpaicg_audio_settings = wp_parse_args($wpaicg_audio_settings, $wpaicg_audio_default_settings);
?>
<form action="" method="post" class="wpaicg-audio-form">
    <table class="form-table">
        <tr>
            <th scope="row"><?php echo esc_html__('Purpose','gpt3-ai-content-generator')?></th>
            <td>
                <select name="purpose" class="regular-text wpaicg-audio-purpose">
                    <option<?php echo $wpaicg_audio_settings['purpose'] == 'transcriptions' ? ' selected':'';?> value="transcriptions"><?php echo esc_html__('Transcription','gpt3-ai-content-generator')?></option>
                    <option<?php echo $wpaicg_audio_settings['purpose'] == 'translations' ? ' selected':'';?> value="translations"><?php echo esc_html__('Translation','gpt3-ai-content-generator')?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('File','gpt3-ai-content-generator')?></th>
            <td>
                <div class="mb-5">
                    <label><input<?php echo $wpaicg_audio_settings['type'] == 'upload' ? ' checked':'';?> class="wpaicg-audio-select" name="type" value="upload" type="radio">&nbsp;<?php echo esc_html__('Computer','gpt3-ai-content-generator')?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label><input<?php echo $wpaicg_audio_settings['type'] == 'url' ? ' checked':'';?> class="wpaicg-audio-select" name="type" value="url" type="radio">&nbsp;<?php echo esc_html__('URL','gpt3-ai-content-generator')?></label>
                    <label><input<?php echo $wpaicg_audio_settings['type'] == 'record' ? ' checked':'';?> class="wpaicg-audio-select" name="type" value="record" type="radio">&nbsp;<?php echo esc_html__('Recording','gpt3-ai-content-generator')?></label>
                </div>
                <div class="wpaicg-audio-type wpaicg-audio-upload" style="<?php echo $wpaicg_audio_settings['type'] == 'upload' ? '': 'display:none'?>">
                    <input type="file" name="file" accept="audio/mpeg,video/mp4,video/mpeg,audio/m4a,audio/wav,video/webm">
                </div>
                <div class="wpaicg-audio-type wpaicg-audio-url" style="<?php echo $wpaicg_audio_settings['type'] == 'url' ? '': 'display:none'?>">
                    <input type="url" name="url" class="regular-text" placeholder="Example: https://domain.com/audio.mp3">
                </div>
                <div class="wpaicg-audio-type wpaicg-audio-record" style="<?php echo $wpaicg_audio_settings['type'] == 'record' ? '': 'display:none'?>">
                    <button type="button" class="button button-primary btn-audio-record"><?php echo esc_html__('Record','gpt3-ai-content-generator')?></button>
                    <button type="button" class="button button-primary btn-audio-record-pause" style="display: none"><?php echo esc_html__('Pause','gpt3-ai-content-generator')?></button>
                    <button type="button" class="button button-link-delete btn-audio-record-stop" style="display: none"><?php echo esc_html__('Stop','gpt3-ai-content-generator')?></button>
                    <div class="wpaicg-audio-record-result" style="display: none"></div>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Model','gpt3-ai-content-generator')?></th>
            <td>
                <select name="model" class="regular-text">
                    <option<?php echo $wpaicg_audio_settings['model'] == 'whisper-1' ? ' selected':'';?> value="whisper-1">whisper-1</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Prompt (Optional)','gpt3-ai-content-generator')?></th>
            <td>
                <textarea class="regular-text" name="prompt" maxlength="255" rows="4" cols="50"></textarea>
                <p><?php echo esc_html__('Prompts can be very helpful for correcting specific words or acronyms that the model often misrecognizes in the audio. For example, the following prompt improves the transcription of the words DALL·E and GPT-3, which were previously written as "GDP 3" and "DALI".','gpt3-ai-content-generator')?></p>
                <p><i>"<?php echo esc_html__('The transcript is about OpenAI which makes technology like DALL·E, GPT-3, and ChatGPT with the hope of one day building an AGI system that benefits all of humanity','gpt3-ai-content-generator')?>".</i></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Output Format','gpt3-ai-content-generator')?></th>
            <td>
                <select name="response" class="regular-text wpaicg-audio-response">
                    <option<?php echo $wpaicg_audio_settings['response'] == 'post' ? ' selected':'';?> value="post">post</option>
                    <option<?php echo $wpaicg_audio_settings['response'] == 'page' ? ' selected':'';?> value="page">page</option>
                    <option<?php echo $wpaicg_audio_settings['response'] == 'json' ? ' selected':'';?> value="json">json</option>
                    <option<?php echo $wpaicg_audio_settings['response'] == 'text' ? ' selected':'';?> value="text">text</option>
                    <option<?php echo $wpaicg_audio_settings['response'] == 'srt' ? ' selected':'';?> value="srt">srt</option>
                    <option<?php echo $wpaicg_audio_settings['response'] == 'verbose_json' ? ' selected':'';?> value="verbose_json">verbose_json</option>
                    <option<?php echo $wpaicg_audio_settings['response'] == 'vtt' ? ' selected':'';?> value="vtt">vtt</option>
                </select>
            </td>
        </tr>
        <tr class="wpaicg_post_type" style="<?php echo !in_array($wpaicg_audio_settings['response'], ['post','page']) ? 'display:none':''?>">
            <th scope="row"><?php echo esc_html__('Title','gpt3-ai-content-generator')?></th>
            <td>
                <input class="regular-text wpaicg-audio-title" name="title" type="text">
            </td>
        </tr>
        <tr class="wpaicg_post_type" style="<?php echo !in_array($wpaicg_audio_settings['response'], ['post','page']) ? 'display:none':''?>">
            <th scope="row"><?php echo esc_html__('Category','gpt3-ai-content-generator')?></th>
            <td>
                <select name="category" class="regular-text">
                    <?php
                    foreach($categories as $category){
                        ?>
                        <option<?php echo $wpaicg_audio_settings['category'] == $category->term_id ? ' selected':'';?> value="<?php echo esc_html($category->term_id)?>"><?php echo esc_html($category->name)?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr class="wpaicg_post_type" style="<?php echo !in_array($wpaicg_audio_settings['response'], ['post','page']) ? 'display:none':''?>">
            <th scope="row"><?php echo esc_html__('Author','gpt3-ai-content-generator')?></th>
            <td>
                <select name="author" class="regular-text">
                    <?php
                    foreach($wpaicg_authors as $wpaicg_author){
                        ?>
                        <option<?php echo $wpaicg_audio_settings['author'] == $wpaicg_author->ID ? ' selected':'';?> value="<?php echo esc_html($wpaicg_author->ID)?>"><?php echo esc_html($wpaicg_author->display_name)?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr class="wpaicg_post_type" style="<?php echo !in_array($wpaicg_audio_settings['response'], ['post','page']) ? 'display:none':''?>">
            <th scope="row"><?php echo esc_html__('Status','gpt3-ai-content-generator')?></th>
            <td>
                <select name="status" class="regular-text">
                    <option<?php echo $wpaicg_audio_settings['status'] == 'draft' ? ' selected':'';?> value="draft"><?php echo esc_html__('Draft','gpt3-ai-content-generator')?></option>
                    <option<?php echo $wpaicg_audio_settings['status'] == 'publish' ? ' selected':'';?> value="publish"><?php echo esc_html__('Publish','gpt3-ai-content-generator')?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Temperature (Optional)','gpt3-ai-content-generator')?></th>
            <td>
                <input value="<?php echo esc_html($wpaicg_audio_settings['temperature']);?>" class="regular-text" name="temperature" type="number" min="0" max="1">
            </td>
        </tr>
        <tr class="wpaicg_languages" style="<?php echo $wpaicg_audio_settings['purpose'] == 'translations' ? 'display:none':''?>">
            <th scope="row"><?php echo esc_html__('Language (Optional)','gpt3-ai-content-generator')?></th>
            <td>
                <select name="language" class="regular-text">
                    <?php
                    foreach ($wpaicg_languages as $key=>$wpaicg_language){
                        echo '<option'.($wpaicg_audio_settings['language'] == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($wpaicg_language).'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td>
                <div style="max-width: 400px">
                    <div class="wpaicg_upload_success" style="display: none;margin-bottom: 5px;color: green;"><?php echo esc_html__('Conversion has completed successfully. You can view the results in the Logs tab.','gpt3-ai-content-generator')?></div>
                    <div class="wpaicg_progress" style="display: none"><span></span><small><?php echo esc_html__('Converting.. This will take some time.. Please wait','gpt3-ai-content-generator')?></small></div>
                    <div class="wpaicg-error-msg"></div>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td>
                <button class="button button-primary button-start-converter"><?php echo esc_html__('Start','gpt3-ai-content-generator')?></button>
                <button style="display: none" class="button button-link-delete wpaicg-btn-cancel" type="button"><?php echo esc_html__('Cancel','gpt3-ai-content-generator')?></button>
                <button class="button wpaicg-btn-setting" type="button"><?php echo esc_html__('Set as Default','gpt3-ai-content-generator')?></button>
            </td>
        </tr>
    </table>
</form>
<script>
    jQuery(document).ready(function ($){
        (function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.Recorder = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
                "use strict";

                module.exports = require("./recorder").Recorder;

            },{"./recorder":2}],2:[function(require,module,exports){
                'use strict';

                var _createClass = (function () {
                    function defineProperties(target, props) {
                        for (var i = 0; i < props.length; i++) {
                            var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ("value" in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
                        }
                    }return function (Constructor, protoProps, staticProps) {
                        if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
                    };
                })();

                Object.defineProperty(exports, "__esModule", {
                    value: true
                });
                exports.Recorder = undefined;

                var _inlineWorker = require('inline-worker');

                var _inlineWorker2 = _interopRequireDefault(_inlineWorker);

                function _interopRequireDefault(obj) {
                    return obj && obj.__esModule ? obj : { default: obj };
                }

                function _classCallCheck(instance, Constructor) {
                    if (!(instance instanceof Constructor)) {
                        throw new TypeError("Cannot call a class as a function");
                    }
                }

                var Recorder = exports.Recorder = (function () {
                    function Recorder(source, cfg) {
                        var _this = this;

                        _classCallCheck(this, Recorder);

                        this.config = {
                            bufferLen: 4096,
                            numChannels: 2,
                            mimeType: 'audio/wav'
                        };
                        this.recording = false;
                        this.callbacks = {
                            getBuffer: [],
                            exportWAV: []
                        };

                        Object.assign(this.config, cfg);
                        this.context = source.context;
                        this.node = (this.context.createScriptProcessor || this.context.createJavaScriptNode).call(this.context, this.config.bufferLen, this.config.numChannels, this.config.numChannels);

                        this.node.onaudioprocess = function (e) {
                            if (!_this.recording) return;

                            var buffer = [];
                            for (var channel = 0; channel < _this.config.numChannels; channel++) {
                                buffer.push(e.inputBuffer.getChannelData(channel));
                            }
                            _this.worker.postMessage({
                                command: 'record',
                                buffer: buffer
                            });
                        };

                        source.connect(this.node);
                        this.node.connect(this.context.destination); //this should not be necessary

                        var self = {};
                        this.worker = new _inlineWorker2.default(function () {
                            var recLength = 0,
                                recBuffers = [],
                                sampleRate = undefined,
                                numChannels = undefined;

                            self.onmessage = function (e) {
                                switch (e.data.command) {
                                    case 'init':
                                        init(e.data.config);
                                        break;
                                    case 'record':
                                        record(e.data.buffer);
                                        break;
                                    case 'exportWAV':
                                        exportWAV(e.data.type);
                                        break;
                                    case 'getBuffer':
                                        getBuffer();
                                        break;
                                    case 'clear':
                                        clear();
                                        break;
                                }
                            };

                            function init(config) {
                                sampleRate = config.sampleRate;
                                numChannels = config.numChannels;
                                initBuffers();
                            }

                            function record(inputBuffer) {
                                for (var channel = 0; channel < numChannels; channel++) {
                                    recBuffers[channel].push(inputBuffer[channel]);
                                }
                                recLength += inputBuffer[0].length;
                            }

                            function exportWAV(type) {
                                var buffers = [];
                                for (var channel = 0; channel < numChannels; channel++) {
                                    buffers.push(mergeBuffers(recBuffers[channel], recLength));
                                }
                                var interleaved = undefined;
                                if (numChannels === 2) {
                                    interleaved = interleave(buffers[0], buffers[1]);
                                } else {
                                    interleaved = buffers[0];
                                }
                                var dataview = encodeWAV(interleaved);
                                var audioBlob = new Blob([dataview], { type: type });

                                self.postMessage({ command: 'exportWAV', data: audioBlob });
                            }

                            function getBuffer() {
                                var buffers = [];
                                for (var channel = 0; channel < numChannels; channel++) {
                                    buffers.push(mergeBuffers(recBuffers[channel], recLength));
                                }
                                self.postMessage({ command: 'getBuffer', data: buffers });
                            }

                            function clear() {
                                recLength = 0;
                                recBuffers = [];
                                initBuffers();
                            }

                            function initBuffers() {
                                for (var channel = 0; channel < numChannels; channel++) {
                                    recBuffers[channel] = [];
                                }
                            }

                            function mergeBuffers(recBuffers, recLength) {
                                var result = new Float32Array(recLength);
                                var offset = 0;
                                for (var i = 0; i < recBuffers.length; i++) {
                                    result.set(recBuffers[i], offset);
                                    offset += recBuffers[i].length;
                                }
                                return result;
                            }

                            function interleave(inputL, inputR) {
                                var length = inputL.length + inputR.length;
                                var result = new Float32Array(length);

                                var index = 0,
                                    inputIndex = 0;

                                while (index < length) {
                                    result[index++] = inputL[inputIndex];
                                    result[index++] = inputR[inputIndex];
                                    inputIndex++;
                                }
                                return result;
                            }

                            function floatTo16BitPCM(output, offset, input) {
                                for (var i = 0; i < input.length; i++, offset += 2) {
                                    var s = Math.max(-1, Math.min(1, input[i]));
                                    output.setInt16(offset, s < 0 ? s * 0x8000 : s * 0x7FFF, true);
                                }
                            }

                            function writeString(view, offset, string) {
                                for (var i = 0; i < string.length; i++) {
                                    view.setUint8(offset + i, string.charCodeAt(i));
                                }
                            }

                            function encodeWAV(samples) {
                                var buffer = new ArrayBuffer(44 + samples.length * 2);
                                var view = new DataView(buffer);

                                /* RIFF identifier */
                                writeString(view, 0, 'RIFF');
                                /* RIFF chunk length */
                                view.setUint32(4, 36 + samples.length * 2, true);
                                /* RIFF type */
                                writeString(view, 8, 'WAVE');
                                /* format chunk identifier */
                                writeString(view, 12, 'fmt ');
                                /* format chunk length */
                                view.setUint32(16, 16, true);
                                /* sample format (raw) */
                                view.setUint16(20, 1, true);
                                /* channel count */
                                view.setUint16(22, numChannels, true);
                                /* sample rate */
                                view.setUint32(24, sampleRate, true);
                                /* byte rate (sample rate * block align) */
                                view.setUint32(28, sampleRate * 4, true);
                                /* block align (channel count * bytes per sample) */
                                view.setUint16(32, numChannels * 2, true);
                                /* bits per sample */
                                view.setUint16(34, 16, true);
                                /* data chunk identifier */
                                writeString(view, 36, 'data');
                                /* data chunk length */
                                view.setUint32(40, samples.length * 2, true);

                                floatTo16BitPCM(view, 44, samples);

                                return view;
                            }
                        }, self);

                        this.worker.postMessage({
                            command: 'init',
                            config: {
                                sampleRate: this.context.sampleRate,
                                numChannels: this.config.numChannels
                            }
                        });

                        this.worker.onmessage = function (e) {
                            var cb = _this.callbacks[e.data.command].pop();
                            if (typeof cb == 'function') {
                                cb(e.data.data);
                            }
                        };
                    }

                    _createClass(Recorder, [{
                        key: 'record',
                        value: function record() {
                            this.recording = true;
                        }
                    }, {
                        key: 'stop',
                        value: function stop() {
                            this.recording = false;
                        }
                    }, {
                        key: 'clear',
                        value: function clear() {
                            this.worker.postMessage({ command: 'clear' });
                        }
                    }, {
                        key: 'getBuffer',
                        value: function getBuffer(cb) {
                            cb = cb || this.config.callback;
                            if (!cb) throw new Error('Callback not set');

                            this.callbacks.getBuffer.push(cb);

                            this.worker.postMessage({ command: 'getBuffer' });
                        }
                    }, {
                        key: 'exportWAV',
                        value: function exportWAV(cb, mimeType) {
                            mimeType = mimeType || this.config.mimeType;
                            cb = cb || this.config.callback;
                            if (!cb) throw new Error('Callback not set');

                            this.callbacks.exportWAV.push(cb);

                            this.worker.postMessage({
                                command: 'exportWAV',
                                type: mimeType
                            });
                        }
                    }], [{
                        key: 'forceDownload',
                        value: function forceDownload(blob, filename) {
                            var url = (window.URL || window.webkitURL).createObjectURL(blob);
                            var link = window.document.createElement('a');
                            link.href = url;
                            link.download = filename || 'output.wav';
                            var click = document.createEvent("Event");
                            click.initEvent("click", true, true);
                            link.dispatchEvent(click);
                        }
                    }]);

                    return Recorder;
                })();

                exports.default = Recorder;

            },{"inline-worker":3}],3:[function(require,module,exports){
                "use strict";

                module.exports = require("./inline-worker");
            },{"./inline-worker":4}],4:[function(require,module,exports){
                (function (global){
                    "use strict";

                    var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

                    var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

                    var WORKER_ENABLED = !!(global === global.window && global.URL && global.Blob && global.Worker);

                    var InlineWorker = (function () {
                        function InlineWorker(func, self) {
                            var _this = this;

                            _classCallCheck(this, InlineWorker);

                            if (WORKER_ENABLED) {
                                var functionBody = func.toString().trim().match(/^function\s*\w*\s*\([\w\s,]*\)\s*{([\w\W]*?)}$/)[1];
                                var url = global.URL.createObjectURL(new global.Blob([functionBody], { type: "text/javascript" }));

                                return new global.Worker(url);
                            }

                            this.self = self;
                            this.self.postMessage = function (data) {
                                setTimeout(function () {
                                    _this.onmessage({ data: data });
                                }, 0);
                            };

                            setTimeout(function () {
                                func.call(self);
                            }, 0);
                        }

                        _createClass(InlineWorker, {
                            postMessage: {
                                value: function postMessage(data) {
                                    var _this = this;

                                    setTimeout(function () {
                                        _this.self.onmessage({ data: data });
                                    }, 0);
                                }
                            }
                        });

                        return InlineWorker;
                    })();

                    module.exports = InlineWorker;
                }).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
            },{}]},{},[1])(1)
        });
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
        var wpaicg_audio_mime_types = ['audio/mpeg','video/mp4','video/mpeg','audio/m4a','audio/wav','audio/x-wav','video/webm'];
        var wpaicg_progress = $('.wpaicg_progress');
        var wpaicg_error_message = $('.wpaicg-error-msg');
        var wpaicg_upload_success = $('.wpaicg_upload_success');
        /*Start Record*/
        var wpaicg_btn_record = $('.btn-audio-record');
        var wpaicg_btn_record_pause = $('.btn-audio-record-pause');
        var wpaicg_btn_record_stop = $('.btn-audio-record-stop');
        var wpaicg_btn_record_play = $('.btn-audio-record-play');
        var wpaicg_audio_record_result = $('.wpaicg-audio-record-result');
        var wpaicgStream;
        var wpaicgRec;
        var input;
        var wpaicgAudioContext = window.AudioContext || window.webkitAudioContext;
        var audioContext;
        var wpaicgAudioBlob;
        function wpaicgstartRecording() {
            var constraints = { audio: true, video:false }
            navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
                audioContext = new wpaicgAudioContext();
                wpaicgStream = stream;
                input = audioContext.createMediaStreamSource(stream);
                wpaicgRec = new Recorder(input,{numChannels:1});
                wpaicgRec.record();
            })
        }
        function wpaicgpauseRecording(){
            if (wpaicgRec.recording){
                wpaicgRec.stop();
            }
            else{
                wpaicgRec.record()
            }
        }
        function wpaicgstopRecording() {
            wpaicgRec.stop();
            wpaicgStream.getAudioTracks()[0].stop();
            wpaicgRec.exportWAV(wpaicgcreateDownloadLink);
        }
        function wpaicgcreateDownloadLink(blob) {
            wpaicgAudioBlob = blob;
            var url = URL.createObjectURL(blob);
            wpaicg_audio_record_result.html('<audio controls="true" src="'+url+'"></audio>');
        }
        wpaicg_btn_record_pause.click(function (){
            if(wpaicg_btn_record_pause.hasClass('wpaicg-paused')){
                wpaicg_btn_record_pause.html('<?php echo esc_html__('Pause','gpt3-ai-content-generator')?>');
                wpaicg_btn_record_pause.removeClass('wpaicg-paused');
            }
            else{
                wpaicg_btn_record_pause.html('<?php echo esc_html__('Continue','gpt3-ai-content-generator')?>');
                wpaicg_btn_record_pause.addClass('wpaicg-paused');
            }
            wpaicgpauseRecording();
        })
        wpaicg_btn_record.click(function (){
            wpaicgstartRecording();
            wpaicg_btn_record.hide();
            wpaicg_audio_record_result.empty();
            wpaicg_audio_record_result.hide();
            wpaicg_btn_record_pause.show();
            wpaicg_btn_record_stop.show();
            wpaicg_btn_record_play.hide();
        });
        wpaicg_btn_record_stop.click(function () {
            wpaicgstopRecording();
            wpaicg_btn_record_play.show();
            wpaicg_btn_record_pause.hide();
            wpaicg_btn_record_stop.hide();
            wpaicg_btn_record.html('<?php echo esc_html__('Re-Record','gpt3-ai-content-generator')?>');
            wpaicg_btn_record.show();
            wpaicg_audio_record_result.show();
        })
        $('.wpaicg-audio-purpose').on('change', function (){
            if($(this).val() === 'translations'){
                $('.wpaicg_languages').hide();
            }
            else{
                $('.wpaicg_languages').show();
            }
        });
        $('.wpaicg-audio-response').on('change', function (){
            if($(this).val() === 'post' || $(this).val() === 'page'){
                $('.wpaicg_post_type').show();
            }
            else{
                $('.wpaicg_post_type').hide();
            }
        });
        $('.wpaicg-audio-select').on('click', function (){
            var type = $(this).val();
            $('.wpaicg-audio-type').hide();
            $('.wpaicg-audio-'+type).show();
        });
        var wpaicgAudioWorking = false;
        $('.wpaicg-btn-cancel').click(function (){
            var btn = $('.wpaicg-audio-form').find('.button-primary');
            $(this).hide();
            wpaicgRmLoading(btn);
            wpaicg_progress.hide();
            if(wpaicgAudioWorking){
                wpaicgAudioWorking.abort();
            }
        });
        $('.wpaicg-btn-setting').click(function (){
            var data = new FormData($('.wpaicg-audio-form')[0]);
            data.append('action','wpaicg_audio_settings');
            data.append('nonce','<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>');
            var btn = $(this);
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: data,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function (){
                    wpaicgLoading(btn);
                },
                success: function (res){
                    wpaicgRmLoading(btn);
                    alert('<?php echo esc_html__('The default settings have been successfully saved.','gpt3-ai-content-generator')?>');
                },
                error: function (){
                    wpaicgRmLoading(btn);
                    alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                }
            })
        })
        function wpaicgUploadConverter(data){
            var btn = $('.wpaicg-audio-form .button-start-converter');
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: data,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                xhr: function () {
                    var xhr = $.ajaxSettings.xhr();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            wpaicg_progress.find('span').css('width', (Math.round(percentComplete * 100)) + '%');
                        }
                    }, false);
                    return xhr;
                },
                beforeSend: function () {
                    $('.button-link-delete').show();
                    wpaicg_progress.find('span').css('width', '0');
                    wpaicg_progress.show();
                    wpaicgLoading(btn);
                    wpaicg_error_message.hide();
                    wpaicg_upload_success.hide();
                },
                success: function (res) {
                    if (res.status === 'success') {
                        wpaicgRmLoading(btn);
                        $('.button-link-delete').hide();
                        $('.wpaicg-audio-upload input').val('');
                        wpaicg_progress.hide();
                        wpaicg_upload_success.show();
                    } else {
                        wpaicgRmLoading(btn);
                        $('.button-link-delete').hide();
                        wpaicg_progress.find('small').html('Error');
                        wpaicg_progress.addClass('wpaicg_error');
                        wpaicg_error_message.html(res.msg);
                        wpaicg_error_message.show();
                    }
                },
                error: function () {
                    wpaicgRmLoading(btn);
                    $('.button-link-delete').hide();
                    wpaicg_progress.addClass('wpaicg_error');
                    wpaicg_progress.find('small').html('Error');
                    wpaicg_error_message.html('<?php echo esc_html__('Please try again','gpt3-ai-content-generator')?>');
                    wpaicg_error_message.show();
                }
            })
        }
        $('.wpaicg-audio-form').on('submit', function (e){
            e.preventDefault();
            var type = $('.wpaicg-audio-select:checked').val();
            var error_message = false;
            var response = $('.wpaicg-audio-response').val();
            if(type === 'upload'){
                if($('.wpaicg-audio-upload input')[0].files.length === 0){
                    error_message = '<?php echo esc_html__('An audio file is mandatory.','gpt3-ai-content-generator')?>';
                }
                else{
                    var file = $('.wpaicg-audio-upload input')[0].files[0];
                    if($.inArray(file.type, wpaicg_audio_mime_types) < 0){
                        error_message = '<?php echo esc_html__('We only accept mp3, mp4, mpeg, mpga, m4a, wav, or webm.','gpt3-ai-content-generator')?>'
                    }
                    else if(file.size > 26214400){
                        error_message = '<?php echo esc_html__('Audio file maximum 25MB','gpt3-ai-content-generator')?>';
                    }
                }
            }
            if(!error_message && type === 'url' && $('.wpaicg-audio-url input').val() === ''){
                error_message = '<?php echo esc_html__('Please insert audio URL','gpt3-ai-content-generator')?>';
            }
            if(!error_message && (response === 'post' || response === 'page') && $('.wpaicg-audio-title').val() === ''){
                error_message = '<?php echo esc_html__('The title field is required','gpt3-ai-content-generator')?>'
            }
            if(type === 'record' && wpaicgAudioBlob.size > (10 * Math.pow(1024, 25))){
                error_message = '<?php echo esc_html__('Audio file maximum 25MB','gpt3-ai-content-generator')?>';
            }
            if(error_message){
                alert(error_message)
            }
            else{
                var data = new FormData($('.wpaicg-audio-form')[0]);
                data.append('action','wpaicg_audio_converter');
                data.append('nonce','<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>');
                if(type === 'record'){
                    data.append('recorded_audio', wpaicgAudioBlob,'wpaicg_recording.wav');
                    wpaicgUploadConverter(data);
                }
                else {
                    wpaicgUploadConverter(data);
                }
            }
            return false;
        });
    })
</script>
