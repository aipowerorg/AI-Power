<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
  jQuery( function() {
    jQuery( "#accordion" ).accordion({
      collapsible: true,
      heightStyle: "content"
    });
  } );
</script>
<div id="help_tabs-1">
  <div id="accordion">
    <h3>Models</h3>
        <div>
            <p>AI Power exclusively supports OpenAI <mark class="wpcgai_container_help_mark">GPT-3</mark>, <mark class="wpcgai_container_help_mark">GPT-3.5</mark> and <mark class="wpcgai_container_help_mark">GPT-4</mark>models.</p>
            <p>Supported GPT-4 models:</p>
            <table class="wpcgai_container_help_table">
                <tr>
                    <th>Model</th>
                    <th>Description</th>
                    <th>Max Tokens</th>
                    <th>Training Data</th>
                </tr>
                <tr>
                    <td>gpt-4</td>
                    <td>More capable than any GPT-3.5 model, able to do more complex tasks, and optimized for chat. Still in beta.</td>
                    <td>8,192</td>
                    <td>Up to Sep 2021</td>
                </tr>
                <tr>
                    <td>gpt-4-32k</td>
                    <td>Same capabilities as the base gpt-4 mode but with 4x the context length.</td>
                    <td>32,768</td>
                    <td>Up to Sep 2021</td>
                </tr>
            </table>
            <p>Supported GPT-3.5 models:</p>
            <table class="wpcgai_container_help_table">
                <tr>
                    <th>Model</th>
                    <th>Description</th>
                    <th>Max Tokens</th>
                    <th>Training Data</th>
                </tr>
                <tr>
                    <td>gpt-3.5-turbo</td>
                    <td>Most capable GPT-3.5 model and optimized for chat at 1/10th the cost of text-davinci-003.</td>
                    <td>4,096</td>
                    <td>Up to Sep 2021</td>
                </tr>
                <tr>
                    <td>text-davinci-003</td>
                    <td>Can do any language task with better quality, longer output, and consistent instruction-following.</td>
                    <td>4,000</td>
                    <td>Up to Jun 2021</td>
                </tr>
                <tr>
                    <td>text-davinci-002</td>
                    <td>Similar capabilities to text-davinci-003 but trained with supervised fine-tuning instead of reinforcement learning.</td>
                    <td>4,000</td>
                    <td>Up to Jun 2021</td>
                </tr>
            </table>
            <p>Supported GPT-3 models:</p>
            <table class="wpcgai_container_help_table">
                <tr>
                    <th>Model</th>
                    <th>Description</th>
                    <th>Max Tokens</th>
                    <th>Training Data</th>
                </tr>
                <tr>
                    <td>text-curie-001</td>
                    <td>Very capable, but faster and lower cost than Davinci.</td>
                    <td>2,048</td>
                    <td>Up to Oct 2019</td>
                </tr>
                <tr>
                    <td>text-babbage-001</td>
                    <td>Capable of straightforward tasks, very fast, and lower cost.</td>
                    <td>2,048</td>
                    <td>Up to Oct 2019</td>
                </tr>
                <tr>
                    <td>text-ada-001</td>
                    <td>Capable of very simple tasks, usually the fastest model in the GPT-3 series, and lowest cost.</td>
                    <td>2,048</td>
                    <td>Up to Oct 2019</td>
                </tr>
            </table>
            <p>OpenAI recommends using Davinci and Turbo while experimenting since they will yield the best results.</p>
            <table class="wpcgai_container_help_table">
                <tr>
                    <th>Model</th>
                    <th>Good at</th>
                </tr>
                <tr>
                    <td>Turbo</td>
                    <td>Conversation and text generation</td>
                </tr>
                <tr>
                    <td>Davinci</td>
                    <td>Complex intent, cause and effect, summarization for audience</td>
                </tr>
                <tr>
                    <td>Curie</td>
                    <td>Language translation, complex classification, text sentiment, summarization</td>
                </tr>
                <tr>
                    <td>Babbage</td>
                    <td>Moderate classification, semantic search classification</td>
                </tr>
                <tr>
                    <td>Ada</td>
                    <td>Parsing text, simple classification, address correction, keywords</td>
                </tr>
            </table>
            <p>You can change the model used by the plugin by selecting it from the <b>Model</b> dropdown in the <b>AI Engine</b> tab.</p>
            <p>You may notice a <b>"Sync"</b> button located next to the model dropdown. This button is used to synchronize the plugin with the most recent version of the OpenAI models. So if OpenAI releases a new model, you can use this button to update the plugin to use the new model.</p>
            
            <p>If your organization has fine-tuned any models on specific datasets, you can easily find them under the Settings-AI-Engine-Model dropdown.</p>
        </div>
    <h3>API Key</h3>
        <div>
            <p>Our plugin uses a <mark class="wpcgai_container_help_mark">Bring Your Own Key (BYOK)</mark> model, which means that you need to provide your own OpenAI API key in order to use the plugin. This allows you to maintain complete control over your own API keys and ensures that you have access to the latest and most advanced language processing technology provided by OpenAI. </p>
            <p>If you do not have an API key, you can get one by signing up for an OpenAI account <a href="https://platform.openai.com/signup" target="_blank">here</a>. </p>
            <p>After creating an account on OpenAI, you can proceed to generate a new API key by visiting <a href="https://platform.openai.com/account/api-keys" target="_blank">this page</a>. </p>
            <p>This key will be essential in accessing OpenAI's powerful language models and other AI tools through their API. Make sure to keep your API key secure as it provides access to your account and its associated resources.</p>
            <p>Please note that OpenAI offers a free trial version which provides $18 credit to new users. Once this credit runs out, you will need to purchase more credit from them.</p>
            <p>Once you have an API key, you can enter it in the <b>API Key</b> field in the <b>AI Engine</b> tab.</p>
        </div>
    <h3>Parameters</h3>
        <div>
            <p>All the values that are set in the <b>AI Engine</b> tab are used as parameters for the OpenAI API.</p>
            <p>These parameters will be used as the default values for <b>single</b> and <b>auto content</b> generation provided by the plugin.</p>
            <p>The following table provides an overview of the parameters that are used by the plugin.</p>
            <ol>
                <li><b>Temperature</b> - Controls the amount of randomness. Higher values will result in more random completions. Lower values will result in more predictable completions. The default value is 0.7. It can be set to a value between 0 and 1. <a href="https://docs.aipower.org/docs/ai-engine/openai/temperature" target="_blank">Learn more</a>.</li>
                <li><b>Max Tokens</b> - The maximum number of tokens to generate. The default value is 1300. It can be set to a value between 1 and 1400. <a href="https://docs.aipower.org/docs/ai-engine/openai/max-tokens#adjusting-the-max-tokens-setting" target="_blank">Learn more</a>.</li>
                <li><b>Top P</b> - Controls the diversity of the generated text. Lower values will result in more predictable completions. Higher values will result in more random completions. The default value is 1.0. It can be set to a value between 0 and 1. <a href="https://docs.aipower.org/docs/ai-engine/openai/top-p#adjusting-the-top_p-setting" target="_blank">Learn more</a>.</li>
                <li><b>Best Of</b> - The number of different completions to return. The default value is 1. It can be set to a value between 1 and 20. <a href="https://docs.aipower.org/docs/ai-engine/openai/best-of#adjusting-the-best-of-setting" target="_blank">Learn more</a>.</li>
                <li><b>Frequency Penalty</b> - Controls the frequency of the generated text. Lower values will result in more frequent completions. Higher values will result in less frequent completions. The default value is 0.01. It can be set to a value between 0 and 2. <a href="https://docs.aipower.org/docs/ai-engine/openai/frequency-penalty#adjusting-the-frequency-penalty" target="_blank">Learn more</a>.</li>
                <li><b>Presence Penalty</b> - Controls the frequency of the generated text. Lower values will result in more frequent completions. Higher values will result in less frequent completions. The default value is 0.01. It can be set to a value between 0 and 2. <a href="https://docs.aipower.org/docs/ai-engine/openai/presence-penalty#adjusting-the-presence-penalty" target="_blank">Learn more</a>.</li>
            </ol>
        </div>
    <h3>Additional Settings</h3>
        <div>
            <p>Under the AI Engine tab, we offer an additional setting called <b>"Sleep Time"</b> which enables control over the time interval between each request made to the OpenAI API.</p>
            <p>The default value is set at 5 seconds, but can be adjusted to any value between 1 and 10 seconds.</p>
            <p>To avoid exceeding OpenAI's rate limit, which restricts the number of times a user or client can access the server within a specified period of time, it is recommended to add a sleep time between each request. This approach is suggested by OpenAI as a means to prevent hitting the rate limit.</p>
            <p>One of the suggestion from OpenAI is to add a sleep time between each request. This will help you avoid hitting the rate limit.</p>
            <p>Please note that the "Sleep Time" option is only available if you are using gpt-3.5-turbo, and it does not apply to Davinci, as Davinci does not have a rate limit issue.</p>
            <p>We recommend starting with a low sleep time and conducting tests to determine if the rate limit is being hit. If so, gradually increase the sleep time until the rate limit is no longer an issue.</p>
            </ol>
        </div>
    <h3>Error Messages</h3>
        <div>
            <p>This list provides an overview of common error codes and their corresponding solutions. Each error code is explained in detail in its own dedicated section.</p>
            <p>Please note that, all these errors are generated by the OpenAI API and not by our plugin.</p>
            <table class="wpcgai_container_help_table">
                <tr>
                    <th>Error</th>
                    <th>Cause</th>
                    <th>Solution</th>
                </tr>
                <tr>
                    <td>401 - Invalid Authentication</td>
                    <td>Invalid Authentication</td>
                    <td>Ensure the correct <a href="https://platform.openai.com/account/api-keys" target="_blank">API key</a> and requesting organization are being used.</td>
                </tr>
                <tr>
                    <td>401 - Incorrect API key provided</td>
                    <td>The requesting API key is not correct. </td>
                    <td>Ensure the API key used is correct, clear your browser cache, or <a href="https://platform.openai.com/account/api-keys" target="_blank">generate a new one</a>.</td>
                </tr>
                <tr>
                    <td>401 - You must be a member of an organization to use the API</td>
                    <td>Your account is not part of an organization.</td>
                    <td>Contact OpenAI to get added to a new organization or ask your organization manager to <a href="https://platform.openai.com/account/members" target="_blank">invite you to an organization</a>.</td>
                </tr>
                <tr>
                    <td>429 - Rate limit reached for requests</td>
                    <td>You are sending requests too quickly.</td>
                    <td>Pace your requests. Read the <a href="https://platform.openai.com/account/members" target="_blank">Rate limit guide</a>.</td>
                </tr>
                <tr>
                    <td>429 - You exceeded your current quota, please check your plan and billing details</td>
                    <td>You have hit your maximum monthly spend (hard limit) which you can view in the <a href="https://platform.openai.com/account/billing/limits" target="_blank">account billing section.</a></td>
                    <td><a href="https://share.hsforms.com/1AQvscELNT724FkL2Hp5Lvg4sk30" target="_blank">Apply for a quota increase</a> from OpenAI.</td>
                </tr>
                <tr>
                    <td>429 - The engine is currently overloaded, please try again later</td>
                    <td>OpenAI servers are experiencing high traffic.</td>
                    <td>Please retry your requests after a brief wait or contact OpenAI.</td>
                </tr>
                <tr>
                    <td>500 - The server had an error while processing your request</td>
                    <td>Issue on OpenAI servers.</td>
                    <td>Retry your request after a brief wait and contact OpenAI if the issue persists. Check the <a href="https://status.openai.com" target="_blank">status page</a> for updates.</td>
                </tr>
            </table>
        <p>For more information, please visit the <a href="https://platform.openai.com/docs/guides/error-codes/api-errors" target="_blank">OpenAI API Reference</a>.</p>
        </div>
    <h3>Usage Policy</h3>
        <div>
        <p>Please visit OpenAI <a href="https://platform.openai.com/docs/usage-policies" target="_blank">Usage Policy</a> page to learn more about the usage of their API. Please make sure to follow the guidelines provided by OpenAI to avoid any issues.</p>
        </div>
    <h3>FAQ</h3>
        <div>
            <p>Here are some frequently asked questions about the AI engine, parameters, and the error messages.</p>
            <h4>When I asked the chatbot, 'What is the current date?' it did not provide the current date. Why?</h4>
            <p>OpenAI calls it "<i>Blindness to recent events</i>". It is a known limitation with the GPT-3 engine. OpenAI models are trained on datasets that contain some information about real world events up until 2020. If you rely on the models representing recent events, then they may not perform well.</p>
            <h4>Why we are not using same parameters for all features across the plugin? Why do we need to set different parameters for chat widget and content generation?</h4>
            <p>It is important to use different parameters for chat widget and content generation because these features have different requirements and goals, and therefore require different settings to achieve optimal performance. For instance, content generation may require longer output, which means you would need to set larger values in the "max tokens" field. On the other hand, a chatbot does not require that much token, and may perform better with smaller values.</p>
            <p>Overall, using different parameters for different features allows you to tailor the behavior of the language model to the specific requirements of each feature, and achieve the best possible performance for your application.</p>
            <h4>I am sure that my API key is correct, but I am still getting the error message "401 - Incorrect API key provided". What should I do?</h4>
            <p>Regenerate a new key on OpenAI, clear your cache and try again. If the problem persists, please contact OpenAI.</p>
            <h4>What are the best parameter settings for the AI engine?</h4>
            <p>There is no such thing as "best setting". Each website is different and has different requirements. Therefore, the best parameters for one website may not be the best for another website. It is important to test different parameters and find the best ones for your website.</p>
            <h4>I am getting "You exceeded your current quota, please check your plan and billing details" error message. What should I do?</h4>
            <p>This message is coming from OpenAI. You have hit your maximum monthly spend (hard limit) which you can view in the <a href="https://platform.openai.com/account/billing/limits" target="_blank">account billing section.</a></p>
            <h4>I am getting "The engine is currently overloaded, please try again later" error message. What should I do?</h4>
            <p>This message is coming from OpenAI. OpenAI servers are experiencing high traffic. Please retry your requests after a brief wait or contact OpenAI.</p>
            <h4>I am getting "The server had an error while processing your request" error message. What should I do?</h4>
            <p>This message is coming from OpenAI. Issue on OpenAI servers. Retry your request after a brief wait and contact OpenAI if the issue persists. Check the <a href="https://status.openai.com" target="_blank">status page</a> for updates.</p>
        </div>
    </div>
</div>