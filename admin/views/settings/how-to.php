<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div id="tabs-3">
    <div>
        <?php
        $new_post_url = admin_url( 'post-new.php' );
        $single_content_page_url = admin_url( 'admin.php?page=wpaicg_single_content' );
        $bulk_content_page_url = admin_url( 'admin.php?page=wpaicg_bulk_content' );
        $train_your_ai_page_url = admin_url( 'admin.php?page=wpaicg_finetune' );
        $image_generator_page_url = admin_url( 'admin.php?page=wpaicg_image_generator' );
        $promptbase_page_url = admin_url( 'admin.php?page=wpaicg_promptbase' );
        $gpt_forms_page_url = admin_url( 'admin.php?page=wpaicg_forms' );
        $chatgpt_shortcode_page_url = admin_url( 'admin.php?page=wpaicg_chatgpt' );
        $chatgpt_widget_page_url = admin_url( 'admin.php?page=wpaicg_chatgpt&action=widget' );
        $chatgpt_bots_page_url = admin_url( 'admin.php?page=wpaicg_chatgpt&action=bots' );
        ?>
        <h2>What is AI Power</h2>
        <p>AI Power is a WordPress plugin that allows you to generate content, images, audio, and more using the power of AI.</p>
        <p>It is a complete AI pack that includes Custom ChatGPT, Content Writer, Auto Content Writer, AI Assistant, AI Forms, Image Generator, Audio Converter, WooCommerce Product Writer, SEO optimizer, AI Training, Embeddings, Title Suggester and more that are powered by GPT-3.5 and GPT-4.</p>
        <hr>
        <h2>How It Works</h2>
        <p>Our plugin works with the OpenAI API. To use it, you need to create an account on OpenAI and <a href="https://beta.openai.com/account/api-keys" target="_blank">obtain your API key</a>. OpenAI provides $5 in free credit for new users. If you encounter the message <b>"You exceeded your current quota, please check your plan and billing details."</b> it indicates that you have exhausted your OpenAI quota and need to purchase additional credit from OpenAI.</p>
        <p>Purchasing our plugin does not provide any credit from OpenAI. When you buy our plugin, you gain access to the pro features of the plugin, but it does not include any API credit. You will still need to purchase credit from OpenAI separately.</p>
        <p>If you experience any slowness or content not being generated, it may be due to issues with OpenAI API services. Please wait for their services to return to normal before trying again.</p>
        <hr>
        <h2>How to Setup API Key</h2>
        <p>Watch this tutorial: <a href="https://youtu.be/d0GSPU4P7FI" target="_blank">How to Setup OpenAI API Key</a></p>
        <p>1. Go to <a href="https://platform.openai.com/account/api-keys" target="_blank">OpenAI</a> and generate your API key.</p>
        <p>2. Go to <u><b>AI Engine</b></u> tab in this page.</p>
        <p>3. Enter your API key and click the Save button.</p>
        <p>4. Done!</p>
        <hr>
        <h2>How to Add ChatGPT to Your Website</h2>
        <p>You can create unlimited chatGPT bots that your know content.</p>
        <p>Learn how you can teach your content to the chat bot: <u><b><a href="https://www.youtube.com/watch?v=t3UQQ5-oNso" target="_blank">https://www.youtube.com/watch?v=t3UQQ5-oNso</a></u></b></p>
        <p>There are 3 different ways to add a chatbox in your website.</p>
        <b>1. Using the ShortCode</b>
        <p>Go to <u><b><a href="<?php
                    echo  esc_html( $chatgpt_shortcode_page_url ) ;
                    ?>">ChatGPT - ShortCode</a></b></u> page and configure your shortcode. To add the chat bot to your website, please create a new page or post and include the shortcode <code>[wpaicg_chatgpt]</code> in the desired location on your site. This will allow your users to interact with the bot directly from the frontend of your website.</p>
        <b>2. Using the Widget</b>
        <p>Go to <u><b><a href="<?php
                    echo  esc_html( $chatgpt_widget_page_url ) ;
                    ?>">ChatGPT - Widget</a></b></u> page and configure your widget. This will allow your users to interact with the bot directly from the frontend of your website.</p>
        <b>3. Creating Customized Bots</b>
        <p>You can also created bots with different parameters for different pages <u><b><a href="<?php
                    echo  esc_html( $chatgpt_bots_page_url ) ;
                    ?>">here</a></b></u>.</p>
        <hr>
        <h2>How to Use Content Writer</h2>
        <p>Watch this tutorial: <a href="https://youtu.be/ZBJyhr_DlxE" target="_blank">How to Use Content Writer</a></p>
        <p>1. Go to <u><b><a href="<?php
                    echo  esc_html( $single_content_page_url ) ;
                    ?>">Content Writer Page</a></b></u></p>
        <p>2. Enter your title and click the Generate button.</p>
        <p>3. Click the Save Draft button.</p>
        <p>4. If you are happy with the generated content, click the Publish button.</p>
        <p>5. Done!</p>
        <hr>
        <h2>How to Use Auto Content Writer</h2>
        <p>1. Go to <u><b><a href="<?php
                    echo  esc_html( $bulk_content_page_url ) ;
                    ?>">Auto Content Writer Page</a></b></u>.</p>
        <p>2. Make sure to complete Cron Job setup. Guide <a href="https://docs.aipower.org/docs/AutoGPT/gpt-agents#cron-job-setup" target="_blank">here</a>.</p>
        <p>3. In the Bulk Editor tab, enter your title, select Draft or Publish then hit generate button.</p>
        <p>4. In the CSV tab, upload a CSV with the title value in each line and hit generate button. Free plan is limited to generate 5 article at a time.</p>
        <p>5. In the Copy-Paste tab, copy and paste your titles and hit generate button. Free plan is limited to generate 5 article at a time.</p>
        <p>6. You can track your content generation status in the "Queue" tab.</p>
        <p>7. Done!</p>
        <hr>
        <h2>How to Use WooCommerce Product Writer</h2>
        <?php
        $woo_commerce_product_url2 = admin_url( 'post-new.php?post_type=product' );
        ?>
        <p>GPT powered AI can write your WooCommerce product title, description, short description and tags.</p>
        <p>Please note: the WooCommerce settings will only be visible <u>if the WooCommerce plugin is installed</u> on your site.</p>
        <p>1. Go to WooCommerce > Add New Product</p>
        <p>2. Scroll down to the "AI Power Product Writer" section.</p>
        <p>3. Select all checkboxes.</p>
        <p>4. Click on the "Generate" button.</p>
        <p>5. Click on the "Save Draft" button.</p>
        <p>6. Done!</p>
        <hr>
        <h2>How to Fine-Tune your model.</h2>
        <p>You can train your own model based on an existing GPT model.<p>
        <p>Visit <u><b><a href="<?php
                    echo  esc_html( $train_your_ai_page_url ) ;
                    ?>">Train Your AI</a></u></b> page to learn more about it.</p>
        <hr>
        <h2>How to use Embeddings for Chat bot.</h2>
        <p>1. First watch this video tutorial <u><b><a href="https://www.youtube.com/watch?v=t3UQQ5-oNso" target="_blank">here</a></u></b>.</p>
        <p>2. Get your API key from <a href="https://www.pinecone.io/" target="_blank">Pinecone</a>.</p>
        <p>3. Create an Index on Pinecone.</p>
        <p>4. Make sure to set your dimension to <b>1536</b>.</p>
        <p>5. Make sure to set your metric to <b>cosine</b>.</p>
        <p>6. Enter your data manually or use Index Builder to convert all your content automatically.</p>
        <p>7. Go to Settings - ChatGPT tab and select Embeddings method.</p>
        <hr>
        <h2>How to Use Image Generator</h2>
        <p>1. First watch this video tutorial <u><b><a href="https://www.youtube.com/watch?v=KYArfalJBbE" target="_blank">here</a></u></b>.</p>
        <p>1. Go to <u><b><a href="<?php
                    echo  esc_html( $image_generator_page_url ) ;
                    ?>">Image Generator</a></u></b>.</p>
        <p>2. Select DALL-E or Stable Diffusion</p>
        <p>3. Enter your title and click the Generate button.</p>
        <p>4. Select your favorite artist and style from list.</p>
        <p>5. Click the Generate button.</p>
        <p>6. Done!</p>
        <p><b>Shortcodes</b></p>
        <p>Copy and paste the following shortcode into your post or page to display the image generator.</p>
        <p>If you want to display both DALL-E and Stable Diffusion, use: <code>[wpcgai_img]</code></p>
        <p>If you want to display only DALL-E, use: <code>[wpcgai_img dalle=yes]</code></p>
        <p>If you want to display only Stable Diffusion, use: <code>[wpcgai_img sd=yes]</code></p>
        <p>If you want to display the settings, use: <code>[wpcgai_img settings=yes]</code> or <code>[wpcgai_img dalle=yes settings=yes]</code> or <code>[wpcgai_img sd=yes settings=yes]</code></p>
        <hr>
        <h2>How to Use PromptBase</h2>
        <p>1. First watch this video tutorial <u><b><a href="https://youtu.be/R3_siKOBnls" target="_blank">here</a></u></b>.</p>
        <p>2. Go to <u><b><a href="<?php
                    echo  esc_html( $promptbase_page_url ) ;
                    ?>">PromptBase</a></u></b>.</p>
        <p>3. Select a category or search for a prompt.</p>
        <p>4. Review the prompt and click the Generate button.</p>
        <p>5. Done!</p>
        <hr>
        <h2>How to Use AI Forms</h2>
        <p>1. First watch this video tutorial <u><b><a href="https://youtu.be/hetYOlR-ms4" target="_blank">here</a></u></b>.</p>
        <p>2. Go to <u><b><a href="<?php
                    echo  esc_html( $gpt_forms_page_url ) ;
                    ?>">AI Forms</a></u></b>.</p>
        <p>3. Design your form.</p>
        <p>4. Get the shortcode and embed it in your website.</p>
        <p>5. Done!</p>
        <hr>
        <h2>How to Use Title Suggestion Tool</h2>
        <p>Go to Posts or Pages. Hover over the title and click the <b>Suggest Title</b> link.</p>
        <p>If you are using WooCommerce, you can use the <b>Suggest Title</b> link on the product page.</p>
        <hr>
        <h2>How to Use SearchGPT</h2>
        <p>1. You need to enable Pinecone integration and enter some data for Embeddings. Or you can index your pages.</p>
        <p>2. Once step number 1 is completed then you can customize and embed short code for semantic search in your website.</p>
        <p>3. Copy the following code and paste it in your page or post where you want to show the search box: <code>[wpaicg_search]</code></p>
        <hr>
        <h2>How to Use AI Assistant</h2>
        <p>AI Assistant is a feature that allows you to add a button to the WordPress editor that will help you to create content. You can add your own menus with your own prompts.</p>
        <p>AI Assistant is compatible with both Gutenberg and Classic Editor.</p>
        <p>1. Go to your Gutenberg or Classic Edtor and look for our logo in the toolbar.</p>
        <p>2. Click on the logo and select the menu you want to use.</p>
        <p>3. Click the prompt you want to use.</p>
        <p>5. Done!</p>
        <p>Please note that you need to use "Convert to Block" feature in Gutenberg Editor to use AI Assistant.</p>
        <hr>
        <h2>Contact</h2>
        <p>For more information about the plugin, please visit <u><b><a href="https://aipower.org/" target="_blank">our website</a></u></b>.</p>
        <p>If you have any questions, suggestion, feedback please contact me: <b>support@aipower.org</b> </p>
        <p>I am also on <u><b><a href="https://twitter.com/sengpt" target="_blank">Twitter</a></u></b>.</p>
        <p>You can also join our Discord community <u><b><a href="https://discord.gg/EtkpBZYY6v" target="_blank">here</a></u></b>.</p>
        <hr>
        <h2>Notes</h2>
        <p><i><b>Note1:</b> Please do not forget to get your api key from <a href="https://beta.openai.com/account/api-keys" target="_blank">OpenAI</a> and enter it in <a href="<?php
                echo  admin_url( 'admin.php?page=wpaicg' ) ;
                ?>">the settings.</a></i></p>
        <p><i><b>Note2:</b> If you are using Cloudflare, please <a href="https://aipower.org/why-is-my-content-generation-process-taking-too-long/" target="_blank">read this</a>.</i></p>
        <p><i><b>Note3:</b> If you are using WP Rocket caching plugin, please de-activate and re-activate your caching plugin.</i></p>
        <p><i><b>Note4:</b> If your server have a timeout limit than most probably you will not be able to generate longer contents. Please ask your hosting provider to increase server timeout limit at least to 2-3 minutes to generate longer contents.</i></p>
        <p><i><b>Note5:</b> If you are using ithemes security, please make sure to allow php calls from plugin folder, otherwise PromptBase wont work.</i></p>
    </div>
</div>
