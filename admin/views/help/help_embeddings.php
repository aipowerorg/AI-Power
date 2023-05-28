<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#accordion13" ).accordion({
      collapsible: true,
      heightStyle: "content"
    });
  } );
</script>
<div id="help_tabs-13">
  <div id="accordion13">
  <h3>Embeddings</h3>
        <div>
          <p>OpenAI’s text embeddings measure the relatedness of text strings.</p>
          <p>We use this technology to create a semantic search engine, KnowledgeBase and a chat bot.</p>
          <p>Our plugin, which utilizes the ADA model, is the sole WordPress plugin that provides this functionality along with Pinecone integration.</p>
          <p>To use this feature, you must first create a Pinecone account.</p>
          <p>You can think of Pinecone as a <mark class="wpcgai_container_help_mark">long-term external memory</mark> for your bot.</p>
          <p>OpenAI charges a cost of <b>$0.0004</b> per 1,000 tokens for embeddings.</p>
          <p>Assuming that 750 words are approximately equal to 1,000 tokens, let's consider a scenario where you have a website with 1,000 pages, and each page has 750 words.</p>
          <p>In this case, the website would have a total of 750,000 words, which translates to 1,000,000 tokens. Therefore, the cost of embeddings for the entire website would be $0.40.</p>
          <p>Please note that this is only an approximation. For the precise cost, please refer to OpenAI usage.</p>
          <p>You can use embeddings for:</p>
          <p><b>Semantic Search</b> - We have a feature called SearchGPT where you can create your own search engine. You can use the embeddings feature to create a semantic search engine. For example, if you search for “dog”, you will get results for “puppy”, “pooch”, “canine”, “hound”, etc. This is because the embeddings feature will find the most similar text strings to “dog”.</p>
          <p><b>Chat Bot</b> - You can use the embeddings feature to create a chat widget that will respond to your visitors’ questions related to your website.</p>
          <p>There are four different feature under the embeddings tab:</p>
          <p><b>Embeddings</b> - This is where you enter the text strings that you want to embed.</p>
          <p><b>Entries</b> - This is where you can view the text strings that you have entered.</p>
          <p><b>Index Builder</b> - This is where you can create embeddings for all your website content.</p>
          <p><b>Settings</b> - This is where you can enter your Pinecone API key.</p>
          <p>Now let's take a look at all the features in detail.</p>
        </div>
  <h3>Pinecone Integration</h3>
        <div>
          <p>Pinecone is a vector database that allows you to store and query vectors.</p>
          <p>Vector databases are purpose-built to handle the unique structure of vector embeddings. They index vectors for easy search and retrieval by comparing values and finding those that are most similar to one another.</p>
          <p>Our plugin is the first and only WordPress plugin to offer this feature together with Pinecone integration.</p>
          <p><b>Steps to integrate Pinecone:</b></p>
          <ol>
            <li>First watch this video tutorial <a href="https://www.youtube.com/watch?v=t3UQQ5-oNso" target="_blank">here</a>.</li>
            <li>Then create an account with Pinecone <a href="https://www.pinecone.io/" target="_blank">here</a>.</li>
            <li>Copy your Pinecone API key and paste it in the Pinecone API Key field under the Settings tab.</li>
            <li>Create an index on Pinecone.</li>
            <li>Make sure to set your dimension to <b>1536</b>.</li>
            <li>Make sure to set your metric to <b>cosine</b>.</li>
            <li>Copy your index name and paste it in the Pinecone Index Name field under the Settings tab.</li>
            <li>Click on the Save Changes button.</li>
          </ol>
          <p>Now you are ready to use the embeddings feature. You will see that Data Entry, Entries and Index Builder tabs are now active.</p>
        </div>
  <h3>Content Builder</h3>
        <div>
          <p>You can use Content Builder to create embeddings for all your website content.</p>
          <p>You can enter text strings that you want to embed.</p>
          <p>There are couple of ready to use templates that you can use:</p>
          <p><b>Free Text</b> - This is a simple text string.</p>
          <p><b>FAQ</b> - This is a question and answer pair.</p>
          <p><b>KnowledgeBase</b> - This is a topic and description pair.</p>
          <p><b>Product</b> - This template has a product ID, name, description, price and URL.</p>
          <p><b>Woocommerce Product</b> - This template has a product SKU, name, description, regular price, sale price, stock status and URL.</p>
          <p><b>URL</b> - This template has a URL and description.</p>
          <p><b>Company Profile</b> - This template has a company name and other information.</p>
          <p>Select the content type that you want to use.</p>
          <p>Then enter all the required information.</p>
          <p>Click on the Save button.</p>
          <p>Now you can view your entries under the Entries tab.</p>
        </div>
  <h3>Entries</h3>
        <div>
          <p>There are four different information under the Entries tab:</p>
          <p><b>Content</b> - This is the text string that you entered. You can click on the text string to view the text string in a popup window.</p>
          <p><b>Token</b> - This shows spent tokens for the text string.</p>
          <p><b>Estimated</b> - This shows the estimated cost for the text string. Estimated cost calculation for Embeddings: $0.0004 / 1K tokens</p>
          <p><b>Type</b> - This shows the type of text string that you entered. It can be Free Text, FAQ, KnowledgeBase, Product, Woocommerce Product, URL or Company Profile.</p>
          <p><b>Date</b> - This shows the date that you entered the text string.</p>
          <p><b>Action</b> - This shows the action that you can perform on the text string. You can delete an index by clicking on the Delete button. This will delete your content from Pinecone.</p>
        </div>
  <h3>Index Builder</h3>
        <div>
          <p>Index Builder is where you can create embeddings for all your website content.</p>
          <p>To utilize the Index Builder, you will need to setup a Cron Job on your server.</p>
          <p>Cron is a built-in Linux utility that executes processes on your system at designated times. With a Cron Job in place, you can automate tasks.</p>
          <p>Here are the steps for setting up a Cron Job for different environments:</p>
          <p><b><u>For a Linux server:</b></u></p>
          <p>1. Open the terminal and type the following command:</p>
          <p><code>crontab -e</code></p>
          <p>2. Add the following line to the file:</p>
          <p><code>* * * * * php <?php echo esc_html(ABSPATH)?>index.php -- wpaicg_builder=yes</code></p>
          <p>This will run the Index Builder every minute. You can modify the schedule by changing the timing values (e.g., 0 */6 * * * will run the feature every 6 hours).</p>
          <p><b>Question</b>: Why do we need to set the cron job to run every minute?</p>
          <p><b>Answer</b>: You dont need to set the cron job to run every minute. You can set it to run every 6 hours or every day or even week. Let say you are posting a new content on your website once in a week then it wont make sense to run the cron job every minute. You can set it to run every week. But if you are posting a new content on your website every day then it makes sense to run the cron job every day. So it depends on your content posting frequency.</p>
          <p>3. Save the file and exit the terminal.</p>
          <p>4. Wait a few minutes and refresh your page. You will see a message that says "Great! It looks like your Cron Job is running correctly. You should now be able to use the Index Builder." This indicates that everything went well, and you can use the Index Builder.</p>
          <p><b><u>For a cPanel or Plesk hosting account:</b></u></p>
          <p>1. Log in to your account and navigate to the Cron Jobs section.</p>
          <p>2. Under “Add New Cron Job,” select the timing for the cron job (every minute, hour, day, month, or weekday).</p>
          <p>3. Add the following line in the command field:</p>
          <p><code>* * * * * php <?php echo esc_html(ABSPATH)?>index.php -- wpaicg_builder=yes</code></p>
          <p>4. Save and close the file. The Cron Job will be automatically added.</p>
          <p>5. Wait a few minutes and refresh your page. You will see a message that says "Great! It looks like your Cron Job is running correctly. You should now be able to use the Index Builder." This indicates that everything went well, and you can use the Index Builder.</p>
          <p><mark class="wpcgai_container_help_mark">Important Note:</mark> Certain servers may require different commands to run cron jobs. For example, instead of using the "php" command, some servers may use "php81" or "php74" or "/usr/bin/php". If you find that the command provided by the Auto Content Writer plugin doesn't work for your server, it's recommended to check with your hosting provider to determine the correct command to use.</p>
          <p>Once you have set up the Cron Job, you can use the Index Builder.</p>
          <p>1. Click on the Index Builder tab.</p>
          <p>2. Change "Start Indexing" to "Yes". Please note that once you change this setting, it will start indexing your website content and whenever you create a new post, page or product, it will automatically index it.</p>
          <p>3. Check the "Posts, Pages and Products" checkbox.</p>
          <p>4. Click on the "Save Changes" button.</p>
          <p>5. Wait for the indexing to finish.</p>
          <p>You can view the progress of the indexing in the Index Builder tab.</p>
          <p>There are 3 different tabs under index builder:</p>
          <p><b>Indexed</b> - This shows the list of pages that have been indexed successfully.</p>
          <p><b>Failed</b> - This shows the list of pages that have failed to index. There are couple of reasons why a page may fail to index but most common one is encoding issues. If you have pages that have some encoding issues, they may not be indexed. You need to clean them up.</p>
          <p><b>Skipped</b> - This shows the list of pages that have been skipped. Our index builder do not index pages if their content is less than 50 characters. So if you have shortcodes or other content that is less than 50 characters, most probably they will not be indexed.</p>
        </div>
  <h3>Instant Embedding</h3>
        <div>
          <p>In addition to the Index Builder, the AI Power plugin also includes an “Instant Embeddings” feature.</p>
          <p>This allows users to create embeddings for individual posts, pages, or products without needing to set up a Cron Job or wait for the Index Builder to run.</p>
          <p>To use Instant Embeddings, simply go to the post, page, or product you want to create embeddings for and select the checkbox next to “Instant Embedding”.</p>
          <p>The plugin will create embeddings for the selected content.</p>
        </div>
  </div>
</div>