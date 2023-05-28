<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#accordion2" ).accordion({
      collapsible: true,
      heightStyle: "content"
    });
  } );
</script>
<div id="help_tabs-2">
  <div id="accordion2">
  <h3>Express Mode</h3>
        <div>
          <p>Express Mode is a feature that allows you to generate content in a single click.</p>
          <p>It is a great way to get started with our plugin.</p>
          <p>Express Mode is available in the <b>Content Writer</b> tab.</p>
          <p>You just need to enter a Title and click on the <b>Generate</b> button.</p>
          <p>By default, Express Mode generates 3 headings and 500 word article for each heading.</p>
        </div>
  <h3>Custom Mode</h3>
        <div>
          <p>Custom Mode is a feature that allows you to generate content in a more detailed way.</p>
          <p>It is a great way to get started with our plugin.</p>
          <p>Custom Mode is available in the <b>Content Writer</b> tab.</p>
          <p>The main difference between Express Mode and Custom Mode is that in Custom Mode you can modify the prompts and instructions for the AI.</p>
          <p>Express Mode uses built-in prompts and instructions for the AI, while in Custom Mode you can write your own prompts and instructions.</p>
        </div>
    <h3>Language, Style and Tone</h3>
        <div>
          <p>AI Power supports following languages:</p>
          <p>Afrikaans, Arabic, Armenian, Bosnian, Bulgarian, Chinese (Simplified), Chinese (Traditional), Croatian, Czech, Danish, Dutch, English, Estonian, Filipino, Finnish, French, German, Greek, Hebrew, Hindi, Hungarian, Indonesian, Italian, Japanese,
            Korean, Latvian, Lithuanian, Malay, Norwegian, Persian, Polish, Portuguese, Romanian, Russian, Serbian, Slovak, Slovenian, Spanish, Swedish, Thai, Turkish, Ukrainian and Vietnamese.
          </p>
          <p>By default, the plugin uses the English language. You can change the language, style and tone from <b>Settings → Content</b> tab.</p>
          <p>Our plugin also supports total of 44 writing styles and 24 tones:</p>
          <p><b>Writing Styles:</b></p>
          <p>Academic, analytical, anecdotal, argumentative, articulate, biographical, blog, casual, colloquial, comparative, concise, creative, critical, descriptive, detailed, dialog, direct, dramatic, emotional, evaluative, expository, fiction, historical, informative, journalistic, letter, lyrical, metaphorical, monologue, narrative, news, objective, pastoral, personal, persuasive, poetic, reflective, rhetorical, satirical, sensory, simple, technical, theoretical and vivid.</p>
          <p>Informative is the default style.</p>
          <p>Settting a writing style can be useful if you want to convey a particular style or purpose with your writing, or if you want to follow a certain style guide or writing convention.</p>      
          <p><b>Writing Tones:</b></p>
          <p>Assertive, authoritative, cheerful, conversational, factual, formal, friendly, humorous, informal, inspirational, neutral, nostalgic, polite, professional, romantic, sarcastic, sensitive, serious, sincere, skeptical, suspenseful and sympathetic.</p>
          <p>Formal is the default tone.</p>
          <p>Setting a writing tone can be useful if you want to convey a particular emotion or attitude with your writing, or if you want to match the tone to the subject matter or audience of your content.</p>
        </div>
    <h3>Headings</h3>
        <div>
          <p><b>Number of Headings:</b>You can specify how many headings you would like to include in your content. The default number is 5, but you can choose anywhere between 1 and 15 headings.</p>
          <p>However, please note that each request to generate ideas or outlines for a heading will take around 20 seconds. Therefore, we recommend keeping the number of headings between 1 to 5 to avoid longer content generation durations, which may result in a timeout if the server has a limit on the length of time a request can take.</p>
          <p>The number of headings determines the number of <mark class="wpcgai_container_help_mark">ideas</mark> or <mark class="wpcgai_container_help_mark">outlines</mark> that will be generated. </p>
          <p>For example, if you enter the title "Mobile Phones" and specify 5 headings, the plugin will send a request to OpenAI and get 5 ideas or outlines for an article about mobile phones. Keep in mind that a higher number of headings will require more requests to the server, which may result in longer waiting times and possible timeouts.</p>
          <p><b>Heading Tag:</b>You can specify the heading tag for your headings from H1 to H6. The default tag is H2.</p>
          <p>Currently, you can only set one tag for all headings, but we are working on a feature that will enable you to set a different tag for each heading.</p>
          <p><b>Modify Headings:</b>You can modify the headings before generating a full content. This will open a modal window where you can edit the heading text.</p>
          <p>You can also use this window to write your own prompts or instructions for the AI. For example, you can write "Write an article about mobile phones" or "Write an article about mobile phones. Include the following points: 1. History of mobile phones. 2. How mobile phones work. 3. How mobile phones have changed our lives."</p>
        </div>
    <h3>Introduction</h3>
        <div>
          <p>This feature allows you to add introduction to your content.</p>
          <p>An introduction is a paragraph or two that introduces the main topic of your content and sets the stage for the rest of the article or blog post.</p>
          <p>Introduction title is tagged with an H2 by default. You can modify the tag by navigating to the <b>Settings → Content</b> tab.</p>
          <p>By default, introduction is positioned at the beginning of the content.</p>
        </div>
    <h3>Conclusion</h3>
      <div>
          <p>This feature allows you to add conclusion to your content.</p>
          <p>A conclusion is a paragraph or two that summarizes the main points of your content and ties everything together. It’s an important part of any piece of writing, and can help engage your readers and leave a lasting impression.</p>
          <p>Conclusion title is tagged with an H2 by default. You can modify the tag by navigating to the <b>Settings → Content</b> tab.</p>
          <p>By default, conclusion is positioned at the end of the content.</p>
      </div>
    <h3>Table of Contents</h3>
        <div>
          <p>To add a Table of Contents (ToC) to your content, go to the <b>Settings → Content</b> tab and select the Table of Contents feature.</p>
          <p>You can also customize the title; by default, it is "Table of Contents." Once you've made these changes, go to the Single Content Writer or Post page to create your content. You will see that the ToC is automatically added to your content and linked to the relevant headings.</p>
        </div>
    <h3>Tagline</h3>
        <div>
          <p>Tagline is a short phrase that summarizes the main idea of your content. It is usually placed at the end of the introduction.</p>
          <p>To add a tagline to your content, go to the <b>Settings → Content</b> tab and select the Tagline feature.</p>
        </div>
    <h3>Links</h3>
        <div>
          <p>You can enhance your content by adding anchor text and a call to action.</p>
          <p>Anchor text is a hyperlink that directs readers to another webpage or resource, while a call to action is a statement that encourages readers to take a specific action, such as signing up for a newsletter or making a purchase.</p>
          <p>These features can help engage your readers and drive traffic to your website or other resources.</p>
          <p>Here's how to use these features:</p>
          <p><b>Adding Anchor Text</b></p>
          <p>Enter the anchor text you want to use, which should be relevant to your article or blog post, and the target URL you want to link it to. Click "Generate," and the plugin will search for the anchor text in the response from AI engine.</p>
          <p>If found, it will create a hyperlink to the target URL. The process may take a few seconds, depending on your title and settings. Review the generated content and decide whether to use it.</p>
          <p>The plugin will only look for the <mark class="wpcgai_container_help_mark">first occurrence</mark> of the anchor text in the model's response and create a hyperlink for that occurrence.</p>
          <p>The generated content will automatically be added to your article or blog post, with the anchor text hyperlinked to the target URL. You can edit or delete the content using the WordPress editor.</p>
          <p><b>Example:</b></p>
          <p>Let say you are generating a content about "GPT Models" and your anchor text is: "Davinci" and your target URL is: https://aipower.org/davinci.</p>
          <p>The plugin will look for the anchor text "Davinci" in the response from AI engine and create a hyperlink to the target URL.</p>
          <p>If the anchor text is not found in the response, the plugin will not create a hyperlink.</p>
          <p><b>Adding a Call to Action</b></p>
          <p>To generate a call to action related to your content, enter the target URL you want the reader to visit or interact with after reading the call to action.</p>
          <p>Click "Generate," and the plugin will send a request to the AI engine to get a call to action for your URL, including a hyperlink to the target URL.</p>
          <p>Review the generated call to action and decide whether to use it.</p>
          <p>The plugin will automatically add the call to action to your content based on your location selection (beginning or end of the content), with the hyperlink directing the reader to the target URL. You can edit or delete the call to action using the WordPress editor.</p>
          <p><b>Example:</b></p>
          <p>Let say you are generating a content about "GPT Models" and your target call to action URL is: https://aipower.org.</p>
          <p>The plugin will send a request to the AI engine to get a call to action for the URL. The response may look like this:</p>
          <p>"Are you interested in exploring the power and potential of GPT models? Look no further than AI Power! Visit our website at <a href="https://aipower.org">https://aipower.org</a> to learn more and get started today!"</p>
        </div>
    <h3>Image</h3>
        <div>
          <p>You can add images to your content using OpenAI’s DALL-E model or Pexels.</p>
          <p>Here's how to use this feature:</p>
          <p><b>Adding an Image</b></p>
          <p>Go to <b>Settings → Image</b> tab.</p>
          <p>Select the image source you want to use. This will add image to the content.</p>
          <p>Select featured image source. This will add image to the featured image.</p>
          <p>There are two sources to choose from:</p>
          <ol>
            <li>DALL-E</li>
            <li>Pexels</li>
          </ol>
          <p><b><u>Using DALL-E</u></b></p>
          <p>Go to the <b>Settings → Image</b> tab and select the DALL-E image source.</p>
          <p>Now navigate to the Single Content Writer or Post page.</p>
          <p>Enter the title of your content and number of headings and other details.</p>
          <p>Click the “Generate” button. The plugin will send a request to the Dall-E model and get an image based on your title. This process may take a few seconds, depending on the complexity of the prompt.</p>
          <p>Review the generated image and decide whether to use it or not.</p>
          <p>The image will be automatically added to your content. You can resize or reposition it as needed using the image editing tools in the WordPress editor.</p>
          <p><b>Setting Image Size</b></p>
          <p>There are three image sizes to choose from:</p>
          <ol>
            <li>Small (256 x 256)</li>
            <li>Medium (512 x 512)</li>
            <li>Large (1024 x 1024)</li>
          </ol>
          <p><b>Setting Image Style</b></p>
          <p>There are ten image styles to choose from:</p>
          <ol>
            <li>None</li>
            <li>Abstract</li>
            <li>Contemporary</li>
            <li>Cubism</li>
            <li>Fantasy</li>
            <li>Graffiti</li>
            <li>Impressionism</li>
            <li>Modern</li>
            <li>Pop Art</li>
            <li>Surrealism</li>
          </ol>
          <p><b>Setting Featured Image</b></p>
          <p>You can enable this by selecting the “Featured Image Source” option in the Settings → Image tab.</p>
          <p>When enabled, the plugin will automatically set the generated image as the featured image for your content.</p>
          <p>Images generated by DALL-E will have their <b>title</b>, <b>alternative text</b>, <b>caption</b> and <b>description</b> filled with prompt text by default.</p>
          <p>Please note that not all requests made to OpenAI will result in an image being returned. OpenAI filters both prompts and images in accordance with their content policy, and if either is flagged, an error will be returned.</p>
          <p>This is the error message that will be displayed if the request is not in compliance with OpenAI's content policy:</p>
          <p><i>"Your request was rejected as a result of our safety system. Your prompt may contain text that is not allowed by our safety system."</i></p>
          <p><b><u>Using Pexels</u></b></p>
          <p>Go to the <b>Settings → Image</b> tab and select the Pexels image source.</p>
          <p>There are 3 additional text field that you need to fill in:</p>
          <ol>
            <li>API Key</li>
            <li>Image Size</li>
            <li>Image Orientation</li>
          </ol>
          <p><b>API Key</b></p>
          <p>You can get your API key by signing up for a free account at <a href="https://www.pexels.com/" target="_blank">https://www.pexels.com/</a>.</p>
          <p>Once you have signed up, you can get your API key by going to <a href="https://www.pexels.com/api/new/" target="_blank">https://www.pexels.com/api/new/</a>.</p>
          <p>Copy the API key and paste it into the API Key field.</p>
          <p><b>Image Size</b></p>
          <p>There are 3 image sizes to choose from:</p>
          <ol>
            <li>None (Original)</li>
            <li>Large</li>
            <li>Medium</li>
            <li>Small</li>
          </ol>
          <p><b>Image Orientation</b></p>
          <p>There are 3 image orientations to choose from:</p>
          <ol>
            <li>Portrait</li>
            <li>Landscape</li>
            <li>Square</li>
          </ol>
          <p><b>Setting Featured Image</b></p>
          <p>You can enable this by selecting the “Featured Image Source” option in the Settings → Image tab.</p>
          <p>When enabled, the plugin will automatically set the generated image as the featured image for your content.</p>
          <p>Navigate to the Single Content Writer or Post page.</p>
          <p>Enter the title of your content and number of headings and other details.</p>
          <p>Click the “Generate” button. The plugin will send a request to the Pexels API and get an image based on your title. This process may take some tine, depending on the complexity of the prompt.</p>
          <p>Review the generated image and decide whether to use it or not.</p>
          <p>The image will be automatically added to your content. You can resize or reposition it as needed using the image editing tools in the WordPress editor.</p>
        </div>
    <h3>Keywords <mark class="wpcgai_container_help_h3">Pro</mark></h3>
        <div>
          <p>If you want to ensure that your content focuses on specific topics or concepts in a more prominent way, adding specific keywords can be useful.</p>
          <p><a href="<?php echo admin_url('admin.php?page=wpaicg-pricing')?>">Click here</a> to upgrade to the Pro plan to use this feature.</p>
          <p><a href="https://docs.aipower.org/docs/content-writer/express-mode/keywords#add-keywords" target="_blank">Learn more</a>.</p>
        </div>
    <h3>Q&A <mark class="wpcgai_container_help_h3">Pro</mark></h3>
        <div>
          <p>Q&A is a feature that allows you to generate content based on a question and answer format.</p>
          <p><a href="<?php echo admin_url('admin.php?page=wpaicg-pricing')?>">Click here</a> to upgrade to the Pro plan to use this feature.</p>
          <p><a href="https://aipower.org/adding-qa/" target="_blank">Learn more</a>.</p>
        </div>
    <h3>Logs</h3>
        <div>
          <p>Logs are a record of all the requests made to OpenAI. You can view the logs by going to the <b>Content Writer → Logs</b> tab.</p>
          <p>Each log entry contains the following information:</p>
          <p><b>ID:</b> The ID of the log entry. This is usually post ID.</p>
          <p><b>Title:</b> The title of the content.</p>
          <p><b>Date:</b> The date and time when the request was made.</p>
          <p><b>Duration:</b> The duration of the request.</p>
          <p><b>Token:</b> The number of tokens used for the request.</p>
          <p><b>Estimated:</b> The estimated number of tokens used for the request.</p>
          <p><b>Model:</b> The model used for the request.</p>
          <p><b>Author:</b> The author of the content.</p>
          <p><b>Category:</b> The category of the content.</p>
          <p><b>Word Count:</b> The word count of the content.</p>
          <p>You can also search for specific log entries by entering a keyword in the search box.</p>
        </div>
    <h3>FAQ</h3>
      <div>
        <p><b>I received an error message saying "It appears that your web server has some kind of timeout limit. This can also occur if you are using a VPS, CDN, proxy, firewall, or Cloudflare. To resolve this issue, please contact your hosting provider and request an increase in the timeout limit." What should I do?</b></p>
        <p>This message indicates that your server has a limitation on the amount of time it allows for a request to be processed.</p>
        <p>If you are using CloudFlare, note that their default timeout limit is 100 seconds, meaning you won't be able to generate content if the generation process exceeds that limit. You can consider upgrading your plan with CloudFlare or disabling their service, or reducing the number of headings used for content generation.</p>
        <p>If you are using Apache, you will need to contact your hosting provider to find out the current timeout value in the <b>httpd.conf</b> file. This value can be increased.</p>
        <p>If you are using Nginx, you will need to contact your hosting provider to find out the current timeout value in the <b>/etc/nginx/conf.d/timeout.conf</b> file.</p>
        <p><b>I enabled "Add Image" option, but the image is not showing up in my content. Why?</b></p>
        <p>If your request does not comply with OpenAI's content policy, the image may not be displayed in your content. OpenAI filters both prompts and images according to their content policy, and if either is flagged, an error will be returned and the image will not be added to your content.</p>
        <p><b>I added anchor tag and target url to my content, but they are not showing up in the generated content. Why?</b></p>
        <p>Anchor tag will be added if it is found in the response from the AI engine. If it is not found, it will not be added.</p>
        <p><b>How can I customize or create my own prompts for each heading?</b></p>
        <p>To modify or write your own prompts for each heading, you can enable the "Modify Headings" option. When this option is enabled, a modal window will appear that allows you to customize the prompts for each heading. From there, you can create and use your own unique prompts to enhance the generated content.</p>
      </div>
  </div>
</div>