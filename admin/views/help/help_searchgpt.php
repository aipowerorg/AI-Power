<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#accordion9" ).accordion({
      collapsible: true,
      heightStyle: "content"
    });
  } );
</script>
<div id="help_tabs-9">
  <div id="accordion9">
  <h3>SearchGPT</h3>
        <div>
          <p>Our plugin offers a semantic search feature called SearchGPT.</p>
          <p>You can use this feature to enhance the search functionality of your website.</p>
          <p>You can see the demo <a href="https://aipower.org/gpt-powered-semantic-search-with-embeddings/" target="_blank">here</a>.</p>
          <p>To use the SearchGPT feature, you must have a Pinecone API key entered under the "Embeddings-Settings" page.</p>
          <p>SearchGPT uses OpenAI embeddings and Pinecone vector database integration to provide a powerful semantic search experience.
          <p>Semantic search is a type of search that uses machine learning algorithms to understand the intent behind a search query and find results that are related to that intent, rather than just matching specific keywords.</p>
          <p>To use the SearchGPT feature, simply copy the following shortcode and paste it into the page or post where you want to show the search box:</p>
          <p><code>[wpaicg_search]</code></p>
          <p>Once you've added the shortcode, you can customize the SearchGPT feature from the <b>Settings → SearchGPT</b> page.</p>
          <p>Here, you can customize the placeholder message, font size, font color, border color, background color, width, and height for the search box.</p>
          <p>You can also customize the results, including the number of nearest results (from 1 to 5), font size, font color, background color, and in-progress background color.</p>
          <p>When a user enters a search query, the plugin uses OpenAI embeddings to convert the query into a vector representation. The vector representation is then compared to the vectors of the content on your website using cosine similarity, which measures the angle between two vectors. The content with the highest cosine similarity score is returned as the most relevant result.</p>
          <p>Pinecone is a vector database that allows for fast and efficient vector similarity search. You can get your Pinecone API key by signing up for a Pinecone account <a href="https://www.pinecone.io/" target="_blank">here</a>.</p>
          <p>You can find more information under the Embeddings tab.</p>
         </div>
  </div>
</div>