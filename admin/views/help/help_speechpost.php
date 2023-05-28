<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#accordion16" ).accordion({
      collapsible: true,
      heightStyle: "content"
    });
  } );
</script>
<div id="help_tabs-16">
  <div id="accordion16">
  <h3>Speech to Post</h3>
        <div>
          <p>You can convert your speech to a WordPress post with one click.</p>
          <p>Go to Content <b>Writer → Speech to Post</b> tab.</p>
          <p>Simply press the record button and speak your prompt, just like you would in a conversation.</p>
          <p><b>Example:</b></p>
          <p><i>"Write a blog post about the latest mobile phones and their features. Include an introduction that highlights the importance of mobile phones in today's world. In the body of the post, discuss the latest mobile phone trends, such as foldable screens, 5G connectivity, and high refresh rate displays. Also, mention the most popular mobile phone brands and their latest releases. Don't forget to discuss the benefits and drawbacks of each phone and how they compare to one another. In the conclusion, summarize the key points of the post."</i></p>
          <p>After you are done speaking, press the stop button and wait for the post to be generated.</p>
          <p>Once the post is generated, you can edit it and publish it.</p>
          <p>You can see the token usage and other details in the <b>Writer → Logs</b> tab.</p>
          <p><b>Parameters</b></p>
          <p>Currently, the following parameters are hard-coded for the Speech to Post feature:</p>
          <ol>
            <li>Model: Turbo</li>
            <li>Max Tokens: 2000</li>
            <li>Temperature: 0.7</li>
            <li>Top P: 1</li>
            <li>Frequency Penalty: 0.01</li>
            <li>Presence Penalty: 0.01</li>
          </ol>
          <p><b>Estimated Cost Calculation</b></p>
          <p>The cost of using OpenAI's Whisper API is <b>$0.006 per minute</b> of audio. Based on this pricing, a 10-minute audio would be approximately $0.06. When we calculate the final cost we are also adding cost of Completions API calls.</p>
        </div>
  </div>
</div>