<?php
if ( ! defined( 'ABSPATH' ) ) exit;
wp_enqueue_editor();
?>
<style>
    .button{
        align-items: center;
    }
</style>
<p><?php echo esc_html__('Simply press the record button and speak your prompt, just like you would in a conversation.','gpt3-ai-content-generator')?></p>
<strong><?php echo esc_html__('Example','gpt3-ai-content-generator')?></strong>
<p style="font-style: italic">"<?php echo esc_html__('Write a blog post about the latest mobile phones and their features. Include an introduction that highlights the importance of mobile phones in today\'s world. In the body of the post, discuss the latest mobile phone trends, such as foldable screens, 5G connectivity, and high refresh rate displays. Also, mention the most popular mobile phone brands and their latest releases. Don\'t forget to discuss the benefits and drawbacks of each phone and how they compare to one another. In the conclusion, summarize the key points of the post.','gpt3-ai-content-generator')?>"</p>
<button class="button button-primary button-hero btn-start-record" style="display: inline-flex"><span class="dashicons dashicons-microphone"></span><?php echo esc_html__('Speak','gpt3-ai-content-generator')?></button>
<button class="button button-primary button-hero btn-pause-record" style="display: none"><span class="dashicons dashicons-controls-pause"></span><?php echo esc_html__('Pause','gpt3-ai-content-generator')?></button>
<button class="button button-link-delete button-hero btn-stop-record" style="display: none"><span class="dashicons dashicons-saved"></span><?php echo esc_html__('Stop','gpt3-ai-content-generator')?></button>
<button class="button button-link-delete button-hero btn-abort-record" style="display: none"><span class="dashicons dashicons-no"></span><?php echo esc_html__('Cancel','gpt3-ai-content-generator')?></button>
<p class="wpaicg-sending-record" style="display:none;width: 150px; text-align: left">
    <button class="button button-link-delete button-hero btn-cancel-record" style="display: inline-flex;color: #0b9529;"><span class="spinner" style="visibility: unset;margin-top: 0"></span><?php echo esc_html__('Generating Content..Please Wait..','gpt3-ai-content-generator')?></button><br>
    [<?php echo esc_html__('Click to cancel','gpt3-ai-content-generator')?>]
</p>
<p></p>
<div class="wpaicg-speech-audio"></div>
<div class="wpaicg-speech-message"></div>
<div class="wpaicg-speech-result" style="margin-top: 20px;display: none">
    <div class="wpaicg-mb-10">
        <input type="text" class="regular-text wpaicg-speech-title" style="width: 100%;font-size: 20px" placeholder="<?php echo esc_html__('Enter a Post Title','gpt3-ai-content-generator')?>">
    </div>
    <div class="mb-5"><strong><?php echo esc_html__('Post Content','gpt3-ai-content-generator')?></strong></div>
    <div class="wpaicg-mb-10">
        <?php
        wp_editor('','wpaicg-speech-content',array(
            'editor_height' => 425,
            'textarea_rows' => 20
        ));
        ?>
    </div>
    <input type="hidden" class="wpaicg-audio-duration">
    <input type="hidden" class="wpaicg-audio-tokens">
    <input type="hidden" class="wpaicg-audio-length">
    <button class="button button-primary wpaicg-audio-save"><?php echo esc_html__('Save Draft','gpt3-ai-content-generator')?></button>
</div>
<script>
    jQuery(document).ready(function ($){
        let wpaicgBtnRecord = $('.btn-start-record');
        let wpaicgPauseRecord = $('.btn-pause-record');
        let wpaicgStopRecord = $('.btn-stop-record');
        let wpaicgCancelRecord = $('.btn-cancel-record');
        let wpaicgSendingRecord = $('.wpaicg-sending-record');
        let wpaicgSpeechAudio = $('.wpaicg-speech-audio');
        let wpaicgSpeechResult = $('.wpaicg-speech-result');
        let wpaicgSpeechTitle = $('.wpaicg-speech-title');
        let wpaicgAbortRecord = $('.btn-abort-record');
        let wpaicgDuration = $('.wpaicg-audio-duration');
        let wpaicgAudioTokens = $('.wpaicg-audio-tokens');
        let wpaicgAudioLength = $('.wpaicg-audio-length');
        let wpaicgSaveAudio = $('.wpaicg-audio-save');
        let wpaicgSpeechEditor = tinyMCE.get('wpaicg-speech-content');
        let wpaicgSpeechMessage = $('.wpaicg-speech-message');
        let wpaicgSpeechStream;
        let wpaicgSpeechRec;
        let speechinput;
        let wpaicgSpeechAudioContext = window.AudioContext || window.webkitAudioContext;
        let SpeechaudioContext;
        let wpaicgSpeechAjaxRequest;
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
        function wpaicgspeechstartRecording() {
            var constraints = { audio: true, video:false }
            navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
                SpeechaudioContext = new wpaicgSpeechAudioContext();
                wpaicgSpeechStream = stream;
                speechinput = SpeechaudioContext.createMediaStreamSource(stream);
                wpaicgSpeechRec = new Recorder(speechinput,{numChannels:1});
                wpaicgSpeechRec.record();
            })
        }

        function wpaicgspeechpauseRecording(){
            if (wpaicgSpeechRec.recording){
                wpaicgSpeechRec.stop();
            }
            else{
                wpaicgSpeechRec.record()
            }
        }
        function wpaicgSpeechAbortRecording(){
            wpaicgSpeechRec.stop();
            wpaicgSpeechStream.getAudioTracks()[0].stop();
        }

        function wpaicgspeechstopRecording() {
            wpaicgSpeechRec.stop();
            wpaicgSpeechStream.getAudioTracks()[0].stop();
            wpaicgSpeechRec.exportWAV(function (blob){
                let url = URL.createObjectURL(blob);
                let reader = new FileReader();
                reader.onload = function (e){
                    let audio = document.createElement('audio');
                    audio.src = e.target.result;
                    audio.addEventListener('loadedmetadata', function(){
                        let duration = audio.duration;
                        wpaicgDuration.val(duration);
                    })
                }
                reader.readAsDataURL(blob);
                wpaicgSpeechAudio.html('<audio controls="true" src="'+url+'"></audio>');
                let data = new FormData();
                data.append('action','wpaicg_speech_record');
                data.append('audio',blob,'speech_record.wav');
                data.append('nonce','<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>');
                wpaicgSpeechAjaxRequest = $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (res){
                        wpaicgSendingRecord.hide();
                        wpaicgBtnRecord.css('display','inline-flex');
                        if(res.status === 'success'){
                            let basicEditor = true;
                            if($('#wp-wpaicg-speech-content-wrap').hasClass('tmce-active')){
                                basicEditor = false;
                            }
                            wpaicgSpeechMessage.html('<strong><?php echo esc_html__('Your Prompt','gpt3-ai-content-generator')?>: </strong><span style="font-style: italic">'+res.text+'</span>');
                            wpaicgSpeechResult.show();
                            wpaicgAudioTokens.val(res.tokens);
                            wpaicgAudioLength.val(res.length);
                            if(basicEditor){
                                $('#wp-wpaicg-speech-content').val(res.data);
                            }
                            else{
                                wpaicgSpeechEditor.setContent(res.data.replace(/\n/g, "<br />"));
                            }
                        }
                        else{
                            alert(res.msg);
                        }
                    }
                })
            });
        }

        wpaicgSaveAudio.click(function (){
            let title = wpaicgSpeechTitle.val();
            let duration = wpaicgDuration.val();
            let tokens = wpaicgAudioTokens.val();
            let wordcount = wpaicgAudioLength.val();
            let content = '';
            let basicEditor = true;
            if($('#wp-wpaicg-speech-content-wrap').hasClass('tmce-active')){
                content = wpaicgSpeechEditor.getContent();
                basicEditor = false;
            }
            else{
                content = $('#wp-wpaicg-speech-content').val();
            }
            if(title === ''){
                alert('<?php echo esc_html__('Please insert a title before saving.','gpt3-ai-content-generator')?>');
                return;
            }
            if(content === ''){
                alert('<?php echo esc_html__('Please record a speech before saving.','gpt3-ai-content-generator')?>');
                return;
            }
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: {
                    action: 'wpaicg_save_draft_post_extra',
                    title: title,
                    model: 'gpt-3.5-turbo',
                    content: content,
                    duration: duration,
                    usage_token: tokens,
                    word_count: wordcount,
                    source_log: 'speech',
                    'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'
                },
                dataType: 'JSON',
                type: 'POST',
                beforeSend: function (){
                    wpaicgLoading(wpaicgSaveAudio)
                },
                success: function (res){
                    wpaicgRmLoading(wpaicgSaveAudio);
                    if(res.status === 'success'){
                        wpaicgSpeechMessage.html('<span style="color: #26a300; font-weight: bold;display: block;margin-top: 10px;"><?php echo esc_html__('Your post has been saved successfully. You can view its details under the Logs tab.','gpt3-ai-content-generator')?></span>');
                        wpaicgSpeechResult.hide();
                        wpaicgSpeechTitle.val('');
                        wpaicgSpeechAudio.empty();
                    }
                    else{
                        alert(res.msg);
                    }
                }
            })
        });

        wpaicgAbortRecord.click(function (){
            wpaicgBtnRecord.css('display','inline-flex');
            wpaicgPauseRecord.hide();
            wpaicgStopRecord.hide();
            wpaicgAbortRecord.hide();
            wpaicgSpeechMessage.empty();
            wpaicgSpeechAbortRecording();
        })
        wpaicgBtnRecord.click(function (){
            wpaicgSpeechAudio.empty();
            wpaicgBtnRecord.hide();
            wpaicgSpeechMessage.empty();
            wpaicgPauseRecord.css('display','inline-flex');
            wpaicgStopRecord.css('display','inline-flex');
            wpaicgAbortRecord.css('display','inline-flex');
            wpaicgSpeechResult.hide();
            wpaicgspeechstartRecording();
        });
        wpaicgPauseRecord.click(function (){
            if(wpaicgPauseRecord.hasClass('wpaicg-paused')){
                wpaicgPauseRecord.html('<span class="dashicons dashicons-controls-pause"></span><?php echo esc_html__('Pause','gpt3-ai-content-generator')?>')
                wpaicgPauseRecord.removeClass('wpaicg-paused');
            }
            else{
                wpaicgPauseRecord.html('<span class="dashicons dashicons-controls-play"></span><?php echo esc_html__('Continue','gpt3-ai-content-generator')?>')
                wpaicgPauseRecord.addClass('wpaicg-paused');
            }
            wpaicgspeechpauseRecording();
        });
        wpaicgStopRecord.click(function (){
            wpaicgPauseRecord.hide();
            wpaicgStopRecord.hide();
            wpaicgAbortRecord.hide();
            wpaicgSendingRecord.show();
            wpaicgspeechstopRecording();
        });
        wpaicgCancelRecord.click(function (){
            if(wpaicgSpeechAjaxRequest !== undefined){
                wpaicgSpeechAjaxRequest.abort();
            }
            wpaicgSendingRecord.hide();
            wpaicgSpeechAudio.empty();
            wpaicgBtnRecord.css('display','inline-flex');
        });
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
    })
</script>
