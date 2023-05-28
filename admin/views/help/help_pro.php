<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#accordion18" ).accordion({
      collapsible: true,
      heightStyle: "content"
    });
  } );
</script>
<div id="help_tabs-18">
  <div id="accordion18">
  <h3>Pro Features</h3>
        <div>
          <p>I believe in Open Source and I am committed to making this plugin <b>available to everyone for free.</b></p>
          <p>I intend to keep most of the core feature of this plugin free so that as many people as possible can benefit from it.</p>
          <p>If you would like to support me developing this plugin further, please consider buying a license <a href="<?php echo admin_url('admin.php?page=wpaicg-pricing')?>">here</a>.</p>
          <p>Please note that our plugin works with the OpenAI API. To use it, you need to create an account on OpenAI and obtain your API key. OpenAI provides $5 in free credit for new users. If you encounter the message "You exceeded your current quota, please check your plan and billing details." it indicates that you have exhausted your OpenAI quota and need to purchase additional credit from OpenAI.</p>

          <p>Purchasing our plugin does not provide any credit from OpenAI. When you buy our plugin, you gain access to the pro features of the plugin, but it does not include any API credit. You will still need to purchase credit from OpenAI separately.</p>
          <p>Pro features include:</p>
          <ol>
            <li>Prioritized support.</li>
            <li>Custom Post Types for Embeddings.</li>
            <li>Moderation for chat bot.</li>
            <li>Generating 100 posts per batch with auto content writer.</li>
            <li>Scheduling posts with auto content writer.</li>
            <li>Generating content from RSS.</li>
            <li>Generating content from Google Sheets.</li>
        </div>
  </div>
</div>