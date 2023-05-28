<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#accordion3" ).accordion({
      collapsible: true,
      heightStyle: "content"
    });
  } );
</script>
<div id="help_tabs-3">
  <div id="accordion3">
  <h3>Cron Job</h3>
        <div>
          <p>Auto Content Writer requires the use of a Cron Job, which must be set up on the server level. It will not function with any WordPress Cronjob plugin.</p>
          <p>To utilize the bulk feature of the plugin, you will need to setup a Cron Job on your server.</p>
          <p>Cron is a built-in Linux utility that executes processes on your system at designated times. With a Cron Job in place, you can automate tasks, such as running the bulk feature of our plugin at a particular time or at fixed intervals.</p>
          <p>Here are the steps for setting up a Cron Job for different environments:</p>
          <p><b><u>For a Linux server:</b></u></p>
          <p>1. Open the terminal and type the following command:</p>
          <p><code>crontab -e</code></p>
          <p>2. Add the following line to the file:</p>
          <p><code>* * * * * php <?php echo esc_html(ABSPATH)?>index.php -- wpaicg_cron=yes</code></p>
          <p>This will run the bulk feature of the plugin every minute. You can modify the schedule by changing the timing values (e.g., 0 */6 * * * will run the feature every 6 hours).</p>
          <p>3. Save the file and exit the terminal.</p>
          <p>4. Wait a few minutes and refresh your page. You will see a message that says "Great! It looks like your Cron Job is running correctly. You should now be able to use the Bulk Editor." This indicates that everything went well, and you can use the bulk editor.</p>
          <p><b><u>For a cPanel or Plesk hosting account:</b></u></p>
          <p>1. Log in to your account and navigate to the Cron Jobs section.</p>
          <p>2. Under “Add New Cron Job,” select the timing for the cron job (every minute, hour, day, month, or weekday).</p>
          <p>3. Add the following line in the command field:</p>
          <p><code>php <?php echo esc_html(ABSPATH)?>index.php -- wpaicg_cron=yes</code></p>
          <p>4. Save and close the file. The Cron Job will be automatically added.</p>
          <p>5. Wait a few minutes and refresh your page. You will see a message that says "Great! It looks like your Cron Job is running correctly. You should now be able to use the Bulk Editor." This indicates that everything went well, and you can use the bulk editor.</p>
          <p><mark class="wpcgai_container_help_mark">Important Note:</mark> Certain servers may require different commands to run cron jobs. For example, instead of using the "php" command, some servers may use "php81" or "php74" or "/usr/bin/php". If you find that the command provided by the Auto Content Writer plugin doesn't work for your server, it's recommended to check with your hosting provider to determine the correct command to use.</p>
        </div>
    <h3>Bulk Editor</h3>
        <div>
          <p>Auto Content Writer is a robust tool that enables you to produce multiple articles simultaneously, saving you valuable time and energy.</p>
          <p>To use this feature, simply follow these steps:</p>
          <p>Navigate to the Auto Content Writer page on your website, and ensure that you have completed the Cron Job setup, which is essential for the Bulk Content Writer to operate efficiently.</p>
          <p>You can access the guide on how to set up the Cron Job <a href="https://docs.aipower.org/docs/AutoGPT/gpt-agents#cron-job-setup" target="_blank">here</a>.</p>
          <p>Once you are in the Bulk Editor tab, input your desired title, choose whether to save the content as a draft or publish it immediately, select the post category, author and enter tags, anchor text, target url, call to action link and click the “Generate” button.</p>
          <p>You can monitor the progress of your content creation in the “Queue” tab.</p>
          <p>If the content generation process is taking longer than expected, you have the option to cancel it.</p>      
          <p>Keep in mind that the free plan only allows you to generate up to 5 pieces of content at a time, whereas Pro users can generate up to 100.</p>
        </div>
    <h3>CSV Import</h3>
        <div>
          <p>To use this feature, follow these steps:</p>
          <p>1. Go to the Auto Content Writer page on your website and make sure that you have completed the Cron Job setup.</p>
          <p>2. Upload a CSV file with the title value in each line in the CSV tab.</p>
          <p>3. Hit the "Generate" button to start the content creation process.</p>
          <p>Please note that the CSV feature does not have a category or author option. It works with Title only. So you just need to put Title in each line.</p>
          <p>Additionally, with the free plan, you are limited to generating up to 5 pieces of content at a time, while Pro users can generate up to 100.</p>
        </div>
    <h3>Copy-Paste</h3>
        <div>
          <p>To use this feature, follow these steps:</p>
          <p>1. Go to the Auto Content Writer page on your website and make sure that you have completed the Cron Job setup.</p>
          <p>2. Navigate to the "Copy-Paste" tab.</p>
          <p>3. Paste your content in the text area.</p>
          <p>3. Hit the "Generate" button to start the content creation process.</p>
          <p>Please note that the Copy-Paste feature does not have a category or author option. It works with Title only. So you just need to put Title in each line.</p>
          <p>Additionally, with the free plan, you are limited to generating up to 5 pieces of content at a time, while Pro users can generate up to 100.</p>
        </div>
    <h3>Queue</h3>
      <div>
          <p>The queue feature in Auto Content Writer is a helpful tool that allows users to keep track of the progress of their content creation.</p>
          <p>When you generate multiple articles at once using the Bulk Editor, CSV or Copy-Paste feature, they are added to a queue, and you can monitor the status of each batch in the Queue tab.</p>
          <p>There are eight fields in the Queue tab that give you information about each batch of content.</p>
          <p><b>Batch: </b>The Batch field shows you the list of titles in that batch. It is clickable, and once you click on it, you can see the list of titles in that batch.</p>
          <p><b>Status: </b>The Status field shows whether the batch is pending, cancelled, in progress, or completed.</p>
          <p><b>Source: </b>The Source field shows where the batch was created, whether in the Bulk Editor, CSV, or Copy-Paste feature.</p>
          <p><b>Duration: </b>The Duration field shows how long it took to generate all the contents in that batch.</p>
          <p><b>Token: </b>Token field shows how many tokens were spent to generate the content.</p>
          <p><b>Word Count: </b>The Word Count field shows how many words are in that batch. </p>
          <p><b>Estimated: </b>The Estimated field gives you an estimated cost for generating that batch of content. </p>
          <p><b>Action: </b>Finally, the Action field provides a delete button to delete the batch if you no longer need it. Please note that deleting a batch will not delete the content that was generated.</p>
          <p>With the Queue feature, you can easily keep track of the progress of your content creation and stay organized. You can also cancel a batch if it is taking longer than expected or if you no longer need the content.</p>
      </div>
      <h3>Additional Settings</h3>
      <div>
          <p>In addition to the existing features, our Auto Content Writer tool now comes with two new settings that can help enhance your content creation experience.</p>
          <p>Firstly, the <b>Force Refresh</b> button.</p>
          <p>This button is designed to appear under the Bulk Editor tab only, and will be visible after 20 minutes of no Cron Job running. By pressing this button, you can restart your Cron Job to ensure that your content generation process runs smoothly and efficiently.</p>
          <p>Additionally, we have added a "Settings" tab that contains two options: <b>Restart Failed Jobs After</b> and <b>Attempt up to a Maximum of</b>.</p>
          <p>With these settings, you can choose to automatically restart failed jobs after a specific amount of time or after a certain number of attempts.</p>
          <p>For example, if a bulk job fails due to unforeseen circumstances such as OpenAI servers being down or you are out of quota, the tool will automatically attempt to restart the failed job based on the settings you have selected.</p>
      </div>
      <h3>Scheduling <mark class="wpcgai_container_help_h3">Pro</mark></h3>
        <div>
          <p>Auto Content Writer also offers a Schedule feature, which allows users to specify a specific date and time for publishing their generated content. This feature is only available for Pro users. With the Schedule feature, users can ensure that their content is published at the optimal time for their audience, without the need for manual intervention.</p>
          <p><a href="<?php echo admin_url('admin.php?page=wpaicg-pricing')?>">Click here</a> to upgrade to the Pro plan to use this feature.</p>
        </div>
      <h3>FAQ</h3>
        <div>
          <p><b>I followed the setup guide for the cron job, but my Auto Content feature still isn't working. What's going on?</b></p>
          <p>There could be a number of reasons why your Auto Content feature isn't working. One common issue is that some hosting servers use different versions of PHP, such as php74 or php81, or use the /usr/bin/php command instead of simply "php" to run cron jobs.</p>
          <p>If this is the case, you may need to check with your hosting provider to determine which PHP command to use when setting up your cron job.</p>
          <p><b>How can I add category and title when using the CSV or Copy-Paste features?</b></p>
          <p>The CSV and Copy-Paste features only allow for the inclusion of titles. If you want to add categories along with the titles, you need to use the Bulk Editor feature.</p>
        </div>
  </div>
</div>