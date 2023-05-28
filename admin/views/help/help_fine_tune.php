<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#accordion12" ).accordion({
      collapsible: true,
      heightStyle: "content"
    });
  } );
</script>
<div id="help_tabs-12">
  <div id="accordion12">
  <h3>AI Training</h3>
        <div>
          <p>Fine-tuning allows you to customize a model for your application.</p>
          <p>Fine-tuning a model in OpenAI involves a few steps.</p>
          <p>There are 3 different ways of uploading your data to OpenAI and starting a fine-tuning process:</p>
          <p><b>Data Upload</b> - Upload your data from your computer. You can use this tool if you already have your data in the required format.</p>
          <p><b>Manual Entry</b> - Manually enter your data. You can use this tool if you want to enter your data manually.</p>
          <p><b>Data Converter</b> - This tool is one of the most popular one because it allows you to convert your database to the required format with one click.</p>
          <p>Below you can find a detailed explanation of how to fine-tune a model using our plugin. You can also watch a short demo <a href="https://www.youtube.com/watch?v=WQpPlSC8K0Q" target="_blank">here</a>.</p>
        </div>
  <h3>Data Upload</h3>
        <div>
          <ol>
            <li>First, navigate to the Upload tab.</li>
            <li>In the Upload tab, you will be able to upload your datasets directly from your computer. Please note that OpenAI only accepts <code>*.jsonl</code> files and the maximum upload size is <b>100MB</b> per file. To upload larger datasets, your WordPress maximum file upload size setting should be set to at least 100mb. You can follow this guide <a href="https://www.wpbeginner.com/wp-tutorials/how-to-increase-the-maximum-file-upload-size-in-wordpress/" target="_blank">here</a>.</li>
            <li>Jsonl file contains prompt and completion pairs. The prompt is the question and the completion is the answer. You can find some examples <a href="https://github.com/ledwards/gpt-swccg/tree/main/data" target="_blank">here</a>.</li>
            <li>There are 4 fields in the Datasets tab that you need to fill in:
               <ol>
                  <li><b>File</b>: Click on the "Choose File" button to select the file you wish to upload. As I mentioned earlier, OpenAI only accepts <code>*.jsonl</code> files and the maximum upload size is <b>100MB</b> per file. Learn about file format <a href="https://jsonlines.org/" target="_blank">here</a>.</li>
                  <li><b>Purpose</b>: Select the purpose. Currently there is only one option which is "Fine-tune".</li>
                  <li><b>Model Base</b>: Select the model base you wish to fine-tune your model. You can select from the dropdown list. Options are: ada, babbage, curie and davinci.</li>
                  <li><b>Custom Model Name</b>: Enter the custom model name for the fine-tuned model. This is optional. If you leave it blank, the fine-tuned model will be named after the model base you selected. I suggest you to use a custom model name so you can easily identify the fine-tuned model.</li>
              </ol>
            </li>
            <li>Once the file is uploaded, it will be displayed on the Datasets tab. You can view information such as the file's ID, size, creation date, filename, and purpose. There is also an "Actions" column where you can perform various actions on the uploaded files, such as creating a fine-tune request, retrieving content, and deleting the file.</li>
            <li>If you wish to view the content of the file, you can click on the "Retrieve Content" button. For security reasons, OpenAI does not allow free plan users to view the content of the uploaded files. If you wish to view the content of the file, you will need to upgrade your account to a paid plan.</li>
            <li>To delete the file, click on the "Delete" button.</li>
           </ol>
        </div>
    <h3>Manual Data Entry</h3>
        <div>
          <ol>
            <li>First, navigate to the Manual Entry tab.</li>
            <li>In the Manual Entry tab, you will be able to manually enter your data.</li>
            <li>Let say you want to enter your product data.
              <p>Here is an example:</p>
              <p><code>{"prompt":"Item is a handbag. Colour is army green. Price is midrange. Size is small.->", "completion":" This stylish small green handbag will add a unique touch to your look, without costing you a fortune."}</code></p></li>
            <li>Click on "Add more" to add more data.</li>
            <li>Once you have entered all your data, select a model base, give a custom name (optional), and click on "Upload".</li>
          </ol>
        </div>
    <h3>Data Converter</h3>
        <div>
        <ol>
          <li>First, navigate to the Data Converter tab.</li>
          <li>In the Data Converter tab, you will be able to convert your entire Database to a JSONL file.</li>
          <li>There are 3 different options here.</li>
          <ol>
            <li><b>Convert Your Posts</b>: This will convert all your posts in your DB to a JSONL file. Please note that this process will take a while depending on the number of posts you have. If you have huge DB it will split the datasets in small pieces.</li>
            <li><b>Convert Your Pages</b>: This will convert all your pages in your DB to a JSONL file. Please note that this process will take a while depending on the number of pages you have. If you have huge DB it will split the datasets in small pieces.</li>
            <li><b>Convert Your WooCommerce Products</b>: You will see this feature only if you have WooCommerce installed. This will convert all your WooCommerce products in your DB to a JSONL file. Please note that this process will take a while depending on the number of products you have. If you have huge DB it will split the datasets in small pieces.</li>
          </ol>
        </ol>
        <p><b>Important note:</b> If you have huge DB, conversion might take longer and your website might become unresponsive if resources are not enough.</p>
        </div>
    <h3>Creating Fine-tunes</h3>
        <div>
          <ol>
            <li>To create a fine-tune request, click on the "Create Fine-Tune" button in the Datasets tab. This will create a fine-tune request on the OpenAI API based on the uploaded dataset.</li>
            <li><p>There is an important step here before creating your fine-tune request. You need to either create a new model or select an existing model from the dropdown list. If you select an existing model, the fine-tuned model will be created based on the selected model. If you create a new model, the fine-tuned model will be created based on the model base you selected when you uploaded the dataset.</p>
            <p>So why this step is important? Because you can create multiple fine-tune requests for the same dataset. For example, you can create a fine-tune request for the same dataset using different model bases. Or you can create a fine-tune request for the same dataset using different models. This way, you can compare the results and choose the best model for your use case.</p>
            <p>A possible scenario is that you have huge dataset that is not possible to upload to OpenAI because of the 100MB limit. In this case, you can split your dataset into multiple files and upload them to OpenAI. Then, you can create a fine-tune request for each file using the same model base. This way, you can create a fine-tuned model based on the same model base but with different datasets.</p></li>
            <li>If you wish to upload a file for the same model, you will need to select the model from the dropdown list when you hit the "Create Fine-Tune" button.</li>
            <li>Now, let's move on to viewing the fine-tune requests.</li>
          </ol>
        </div>
    <h3>Viewing Fine-tunes</h3>
        <div>
          <li>To view the fine-tune requests, click on the "View Fine-Tunes" button. This will display all the fine-tune requests you have created.</li>
          <li>You can view information such as the fine-tune request's ID, creation date, model, and status. There is also an "Training" column where you can perform various actions on the fine-tune requests, such as viewing the fine-tune request's details, viewing the fine-tuned model, and deleting the fine-tune request.</li>
          <li>There are 4 buttons in the "Training" column: Events, Hyper-params, Result files and Training files. Let's take a look at each one of them.
            <ol>
              <li><b>Events</b>: This button will display the fine-tune request's events. You can view information such as the event's ID, creation date, and status. It's important to note that fine-tuning a model can take some time, depending on the size of the dataset and the complexity of the model.</li>
              <li><b>Hyper-params</b>: This button will display the fine-tune request's hyper-parameters. You can view information such as Epochs, batch size, Learning rate, and prompt loss weight.</li>
              <li><b>Result files</b>: This button will display the fine-tune request's result files. You can download the result file from training the model.</li>
              <li><b>Training files</b>: This button will display the fine-tune request's training files. It is basically the file you uploaded to OpenAI.</li>
            </ol>
          </li>
          <li>Now, let's move on to viewing the fine-tuned models.</li>
        </div>
    <h3>Using Fine-tuned Models</h3>
    <div>
      <li>Let say you already have a fine-tune request that is complete. Now, you want to use the fine-tuned model to with ChatBox in your website. To do that, please proceed to the plugins settings page and click  ChatGPT tab.</li>
      <li>You will see that your fine-tuned model is now available in the dropdown list. You can select it and click on the "Save Changes" button. This means from now on your ChatBox will use the fine-tuned model you selected.</li>
      <li>If you don't see your fine-tuned model in the dropdown list, please make sure that the fine-tune request is complete. You can also click on "Sync Models" link to get latest models.</li>
      <li>Now head over to the ChatGPT and ask your ChatBox a question. You should see that the ChatBox is now using the fine-tuned model.</li>
    </div>
  </div>
</div>