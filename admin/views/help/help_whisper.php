<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#accordion15" ).accordion({
      collapsible: true,
      heightStyle: "content"
    });
  } );
</script>
<div id="help_tabs-15">
  <div id="accordion15">
  <h3>Audio Converter</h3>
        <div>
          <p>This feature allows you to easily convert audio files into text format.</p>
          <p>It uses OpenAI's state-of-the-art open source large-v2 Whisper model to convert audio files into text format.</p>
          <p>The cost of using OpenAI's Whisper API is <b>$0.006 per minute</b> of audio. Based on this pricing, a 10-minute audio would be approximately $0.06.</p>
          <p>In order to use this feature, you will need to follow a few simple steps:</p>
          <p>Firstly, you will need to upload your audio file to the plugin.</p>
          <p>Supported file types include mp3, mp4, mpeg, mpga, m4a, wav, and webm. The file size limit is 25 MB.</p>
          <p>1. Go to Audio Converter page from the plugin menu.</p>
          <p>2. There are two options that you can use: <mark class="wpcgai_container_help_mark">Transcription</mark> and <mark class="wpcgai_container_help_mark">Translation</mark>.</p>
          <p><b>Transcription:</b> This option allows you to convert audio files into text format. It currently supports 38 languages.</p>
          <p>Supported languages are Afrikaans, Arabic, Armenian, Azerbaijani, Belarusian, Bosnian, Bulgarian, Catalan, Chinese, Croatian, Czech, Danish, Dutch, English, Estonian, Finnish, French, Galician, German, Greek, Hebrew, Hindi, Hungarian, Icelandic, Indonesian, Italian, Japanese, Kannada, Kazakh, Korean, Latvian, Lithuanian, Macedonian, Malay, Marathi, Maori, Nepali, Norwegian, Persian, Polish, Portuguese, Romanian, Russian, Serbian, Slovak, Slovenian, Spanish, Swahili, Swedish, Tagalog, Tamil, Thai, Turkish, Ukrainian, Urdu, Vietnamese, and Welsh.</p>
          <p><b>Translation:</b> This option allows you to convert audio files into text format and translate the text into English. This means that you can convert audio files from any language into <b>English only</b>.</p>
          <p>3. There are three different methods that you can use to upload your audio file: <b>Upload File</b>, <b>URL</b>, and <b>Record</b>.</p>
          <p><b>Upload File:</b> This option allows you to upload your audio file from your computer. Simply click on the "Choose File" button and select the file that you want to upload.</p>
          <p><b>URL:</b> This option allows you to upload your audio file from a URL. Paste the URL of the audio file in the text box eg https://www.example.com/audio.mp3.</p>
          <p><b>Record:</b> This option allows you to record your audio file directly from your browser. Click on the "Record" button and start recording. Make sure that your microphone is enabled and that your browser has access to it. You can click pause and resume recording as many times as you want. Once you are done recording, click on the "Stop" button.</p>
          <p>4. Click on the "Start" button.</p>
          <p>5. Wait for the file to be converted.</p>
          <p>6. Once the file is converted, you will be able to see output under the Logs tab.</p>
          <p>There are some additional options that you can use to customize the output:</p>
          <p><b>Model:</b> This option allows you to select the model that you want to use for the conversion. Currently the only available model is "whisper-1".</p>
          <p><b>Prompt:</b> An optional text to guide the model's style or continue a previous audio segment. The prompt should match the audio language.</p>
          <p><b>Outout Format:</b> This option allows you to select the output format that you want to use for the conversion. Available options are post, page, text, json, srt, verbose_json, and vtt. If you select post or page then some additional options will be available such as title, category, author and post status.</p>
          <p><b>Temperature:</b> The sampling temperature, between 0 and 1. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic. If set to 0, the model will use log probability to automatically increase the temperature until certain thresholds are hit.</p>
          <p>Please note that while the underlying model was trained on 98 languages, OpenAI only list the languages that exceeded less than 50% word error rate (WER) which is an industry standard benchmark for speech to text model accuracy. The model will return results for languages not listed above but the quality will be low.</p>
          <p>We also have a "Set as Default" button that allows you to set the default options for the conversion.</p>
        </div>
  <h3>Logs</h3>
        <div>
          <p>This feature allows you to view the logs of the audio files that you have converted.</p>
          <p>1. Go to <b>Audio Converter → Logs</b>.</p>
          <p>2. You will be able to see the logs of the audio files that you have converted.</p>
          <p>3. There are six fields in the logs table: <b>ID</b>, <b>Title</b>, <b>Format</b>, <b>Date</b>, <b>Duration</b>, and <b>Action</b>.</p>
          <p><b>ID:</b> This is the ID of the audio file.</p>
          <p><b>Title:</b> This is the title of the audio file.</p>
          <p><b>Format:</b> This is the format of the audio file.</p>
          <p><b>Date:</b> This is the date when the audio file was converted.</p>
          <p><b>Duration:</b> This shows how long did it take to convert the audio file.</p>
          <p><b>Action:</b> You can delete or download the file from here.</p>
          <p>4. You can also search for a specific audio file by using the search box.</p>
        </div>
  </div>
</div>