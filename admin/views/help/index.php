<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#help_tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
    jQuery( "#help_tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
  } );
</script>
<style>
  .ui-tabs-vertical { width: 98%; }
  .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12%; }
  .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 98%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
  .ui-tabs-vertical .ui-tabs-nav li a { display: block; }
  .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; }
  .ui-tabs-vertical .ui-tabs-panel { padding: 0em; float: right; width: 87%; }
</style>
<h3>Help</h3>
<div id="help_tabs">
        <ul>
            <li><a href="#help_tabs-17">About</a></li>
            <li><a href="#help_tabs-1">AI Engine</a></li>
            <li><a href="#help_tabs-2">Content Writer</a></li>
            <li><a href="#help_tabs-3">Auto Content Writer</a></li>
            <li><a href="#help_tabs-4">WooCommerce</a></li>
            <li><a href="#help_tabs-5">SEO Optimizer</a></li>
            <li><a href="#help_tabs-6">Title Suggester</a></li>
            <li><a href="#help_tabs-7">Image Generator</a></li>
            <li><a href="#help_tabs-8">ChatGPT</a></li>
            <li><a href="#help_tabs-9">SearchGPT</a></li>
            <li><a href="#help_tabs-10">PromptBase</a></li>
            <li><a href="#help_tabs-11">AI Forms</a></li>
            <li><a href="#help_tabs-12">Fine-Tuning</a></li>
            <li><a href="#help_tabs-13">Embeddings</a></li>
            <li><a href="#help_tabs-15">Audio Converter</a></li>
            <li><a href="#help_tabs-16">Speech to Post</a></li>
            <li><a href="#help_tabs-18"><mark class="wpcgai_container_help_h3">Pro Features</mark></a></li>
        </ul>
        <?php
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_about.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_engine.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_content_writer.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_bulk.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_woocommerce.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_seo.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_title_suggester.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_image_generator.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_chatgpt.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_searchgpt.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_promptbase.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_gpt_forms.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_fine_tune.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_embeddings.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_whisper.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_speechpost.php';
            include WPAICG_PLUGIN_DIR.'admin/views/help/help_pro.php';
            ?>
 </div>