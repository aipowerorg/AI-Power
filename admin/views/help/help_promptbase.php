<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#accordion10" ).accordion({
      collapsible: true,
      heightStyle: "content"
    });
  } );
</script>
<div id="help_tabs-10">
  <div id="accordion10">
  <h3>PromptBase</h3>
        <div>
          <p>PromptBase is a powerful feature in our WordPress plugin that offers more than 100 ready-to-use prompt templates for your website.</p>
          <p>With this feature, you can easily search, run, and embed these templates on your website to provide an interactive experience for your users.</p>
          <p>In addition to the pre-built templates, PromptBase also lets you design and create your own prompts using a simple interface.</p>
          <p>You can choose from a variety of GPT models and customize the prompts to suit your needs.</p>
        </div>
  <h3>Using the Pre-Built Templates</h3>
        <div>
          <p>To use one of the pre-built templates, simply browse through the available options in the PromptBase menu.</p>
          <p>You can search for templates by keyword, category, or author. Once you find a template you like, simply click on it to view the details.</p>
          <p>From there, you can copy the shortcode provided and paste it into your WordPress post or page where you want the prompt to appear.</p>
          <p>The shortcode will embed the prompt form on your page, and your users can start interacting with it right away.</p>
        </div>
  <h3>Designing Your Own Prompts</h3>
        <div>
          <p>To design your own prompts, click on the <b>Design Your Prompt</b> button in the PromptBase menu.<p>
          <p>This will open a modal window with four different tabs: <b>Properties</b>, <b>AI Engine</b>, <b>Style</b>, and <b>Frontend</b>.</p>
          <p><b><u>Properties</b></u></p>
          <p>In the "Properties" tab, you can enter a title for your prompt, select a category, enter your prompt, and add a sample response. This information will be used to generate the prompt for your users.</p>
          <p><b>Title:</b> Mandatory - Enter a title for your prompt. This will be used to identify your prompt in the PromptBase menu.</p>
          <p><b>Category:</b> Mandatory - Select a category for your prompt. This will help you to organize your prompts in the PromptBase menu.</p>
          <p><b>Prompt:</b> Mandatory - Enter your prompt. This will be displayed to your users when they interact with your prompt.</p>
          <p><b>Sample Response:</b> Optional - Enter a sample response for your prompt. This is only for your reference and will not be displayed to your users.</p>
          <p>Example:</p>
          <p><b>Title:</b> SEO Meta Description Generator</p>
          <p><b>Category:</b> Generation, SEO, Google</p>
          <p><b>Prompt:</b> Generate an SEO friendly meta description for a website page. Page: [YOUR WEB PAGE]</p>
          <p><b>Sample Response:</b> Shop the latest fashion trends for men and women at our online clothing store. Find a wide selection of stylish and affordable clothing for any occasion.</p>
          <p><b><u>AI Engine</b></u></p>
          <p>In the "AI Engine" tab, you can select the GPT model you want to use for your prompt, and you can also customize the parameters for the model.</p>
          <p><b>Model:</b> Optional - Select a GPT model for your prompt. You can choose from a variety of models, including Turbo, Davinci, Curie, Ada and Babbage. If you don't select a model, the default model will be used which is currently set to Davinci.</p>
          <p><b>Temperature:</b> Optional - Enter a temperature value for your prompt. The temperature value determines how creative the prompt will be. The higher the temperature, the more creative the prompt will be. If you don't enter a temperature value, the default temperature value will be used which is currently set to 0.</p>
          <p><b>Top P:</b> Optional - Enter a top p value for your prompt. The top p value determines how diverse the prompt will be. The higher the top p value, the more diverse the prompt will be. If you don't enter a top p value, the default top p value will be used which is currently set to 0.</p>
          <p><b>Frequency Penalty:</b> Optional - Enter a frequency penalty value for your prompt. The frequency penalty value determines how unique the prompt will be. The higher the frequency penalty value, the more unique the prompt will be. If you don't enter a frequency penalty value, the default frequency penalty value will be used which is currently set to 0.</p>
          <p><b>Presence Penalty:</b> Optional - Enter a presence penalty value for your prompt. The presence penalty value determines how unique the prompt will be. The higher the presence penalty value, the more unique the prompt will be. If you don't enter a presence penalty value, the default presence penalty value will be used which is currently set to 0.</p>
          <p><b>Stop Sequence:</b> Optional - Enter a stop sequence for your prompt. The stop sequence determines when the prompt will stop generating text. If you don't enter a stop sequence, it will set to empty by default.</p>
          <p><b><u>Style</b></u></p>
          <p>In the "Style" tab, you can customize the appearance of your prompt. You can select an icon, icon color, and form background color to match your website's branding.</p>
          <p><b>Background Color:</b> Optional - Select a background color for your prompt. If you don't select a background color, it will be set to white by default.</p>
          <p><b>Icon:</b> Optional - Select an icon for your prompt. If you don't select an icon, it will be set to the default icon by default. This icon will be placed at the top of your form.</p>
          <p><b>Icon Color:</b> Optional - Select an icon color for your prompt. If you don't select an icon color, it will be set to the default icon color by default.</p>
          <p><b><u>Frontend</b></u></p>
          <p>In the "Frontend" tab, you can choose where you want to display the prompt result. You can select either "Text Editor" or "Inline" display. You can also choose to hide the display header, display draft and clear buttons, and display a notice for non-registered users.</p>
          <p><b>Result:</b> There are two options to choose from: <b>Text Editor</b> and <b>Inline</b>. If you select <b>Text Editor</b>, result will be displayed in a text editor. If you select <b>Inline</b>, result will be displayed inline.</p>
          <p><b>Header:</b> Header includes the title, category and icon. You can choose to display or hide the header.</p>
          <p><b>Number of Answers:</b> You can choose to display or hide the number of answers feature.</p>
          <p><b>Save as Draft Button:</b> You can choose to display or hide the save as draft button.</p>
          <p><b>Clear Button:</b> You can choose to display or hide the clear button.</p>
          <p><b>Notification Text:</b> You can choose to display or hide the notification text. Default text is "Please register to save your result". This is shown when the user is not logged in.</p>
          <p>Once you've customized your prompt, simply click the "Save" button to generate the shortcode. Copy the shortcode and paste it into your WordPress post or page to display the prompt on your website.</p>
        </div>
  </div>
</div>