<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div id="tabs-1">
    <?php
    $wpaicg_ai_model = get_option('wpaicg_ai_model','');
    ?>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label" for="wpaicg_ai_model"><?php echo esc_html__('Model','gpt3-ai-content-generator')?>:</label>
        <select class="regular-text" id="wpaicg_ai_model"  name="wpaicg_ai_model" >
            <?php
            foreach($wpaicg_custom_models as $wpaicg_custom_model){
                echo '<option'.($wpaicg_ai_model == $wpaicg_custom_model ? ' selected':'').' value="'.esc_html($wpaicg_custom_model).'">'.esc_html($wpaicg_custom_model).'</option>';
                if($wpaicg_custom_model == 'text-davinci-003'){
                    echo '<option'.($wpaicg_ai_model == 'gpt-3.5-turbo' ? ' selected':'').' value="gpt-3.5-turbo">gpt-3.5-turbo</option>';
                }
            }
            echo '<option'.($wpaicg_ai_model == 'gpt-4' ? ' selected':'').' value="gpt-4">gpt-4 ('.esc_html__('Limited Beta','gpt3-ai-content-generator').')</option>';
            echo '<option'.($wpaicg_ai_model == 'gpt-4-32k' ? ' selected':'').' value="gpt-4-32k">gpt-4-32k ('.esc_html__('Limited Beta','gpt3-ai-content-generator').')</option>';
            ?>
        </select>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/ai-engine/openai/gpt-models#model-configuration" target="_blank">?</a>
        <a class="wpaicg_sync_finetune" href="javascript:void(0)"><?php echo esc_html__('Sync','gpt3-ai-content-generator')?></a>
    </div>
    <?php
    $wpaicg_sleep_time = get_option('wpaicg_sleep_time',8);
    ?>
    <div class="wpcgai_form_row wpaicg_beta_notice" style="<?php echo $wpaicg_ai_model == 'gpt-4-32k' || $wpaicg_ai_model == 'gpt-4' ? '' : 'display:none'?>">
        <p><?php echo sprintf(esc_html__('Please note that GPT-4 is currently in limited beta, which means that access to the GPT-4 API from OpenAI is available only through a waiting list and is not open to everyone yet. You can sign up for the waiting list at %shere%s.','gpt3-ai-content-generator'),'<a href="https://openai.com/waitlist/gpt-4-api" target="_blank">','</a>')?></p>
    </div>
    <div class="wpcgai_form_row wpaicg_sleep_time" style="<?php echo $wpaicg_ai_model == 'gpt-3.5-turbo' || $wpaicg_ai_model == 'gpt-4-32k' || $wpaicg_ai_model == 'gpt-4' ? '' : 'display:none'?>">
        <label class="wpcgai_label"><?php echo esc_html__('Rate Limit Buffer (in Seconds)','gpt3-ai-content-generator')?>:</label>
        <select class="regular-text"  name="wpaicg_sleep_time" >
            <?php
            for($i=1;$i<=10;$i++){
                echo '<option'.($wpaicg_sleep_time == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
            }
            ?>
        </select>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/ai-engine/openai/gpt-models#rate-limit-buffer" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Temperature','gpt3-ai-content-generator')?>:</label>
        <input type="text" class="regular-text" id="label_temperature" name="wpaicg_settings[temperature]" value="<?php
        echo  esc_html( $existingValue['temperature'] ) ;
        ?>">
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/ai-engine/openai/temperature" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Max Tokens','gpt3-ai-content-generator')?>:</label>
        <input type="text" class="regular-text" id="label_max_tokens" name="wpaicg_settings[max_tokens]" value="<?php
        echo  esc_html( $existingValue['max_tokens'] ) ;
        ?>" >
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/ai-engine/openai/max-tokens#adjusting-the-max-tokens-setting" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Top P','gpt3-ai-content-generator')?>:</label>
        <input type="text" class="regular-text" id="label_top_p" name="wpaicg_settings[top_p]" value="<?php
        echo  esc_html( $existingValue['top_p'] ) ;
        ?>" >
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/ai-engine/openai/top-p#adjusting-the-top_p-setting" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Best Of','gpt3-ai-content-generator')?>:</label>
        <input type="text" class="regular-text" id="label_best_of" name="wpaicg_settings[best_of]" value="<?php
        echo  esc_html( $existingValue['best_of'] ) ;
        ?>" >
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/ai-engine/openai/best-of#adjusting-the-best-of-setting" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Frequency Penalty','gpt3-ai-content-generator')?>:</label>
        <input type="text" class="regular-text" id="label_frequency_penalty" name="wpaicg_settings[frequency_penalty]" value="<?php
        echo  esc_html( $existingValue['frequency_penalty'] ) ;
        ?>" >
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/ai-engine/openai/frequency-penalty#adjusting-the-frequency-penalty" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Presence Penalty','gpt3-ai-content-generator')?>:</label>
        <input type="text" class="regular-text" id="label_presence_penalty" name="wpaicg_settings[presence_penalty]" value="<?php
        echo  esc_html( $existingValue['presence_penalty'] ) ;
        ?>" >
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/ai-engine/openai/presence-penalty#adjusting-the-presence-penalty" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Api Key','gpt3-ai-content-generator')?>:</label>
        <input type="text" class="regular-text" id="label_api_key" name="wpaicg_settings[api_key]" value="<?php
        echo  esc_html( $existingValue['api_key'] ) ;
        ?>" >
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/ai-engine/openai/api-key#how-to-generate-an-openai-api-key" target="_blank">?</a>
        <a class="wpcgai_help_link" href="https://beta.openai.com/account/api-keys" target="_blank"><?php echo esc_html__('Get Your Api Key','gpt3-ai-content-generator')?></a>
        <p><?php echo sprintf(esc_html__('Watch this tutorial: %sHow to Setup OpenAI API Key%s','gpt3-ai-content-generator'),'<a href="https://youtu.be/d0GSPU4P7FI" target="_blank">','</a>')?></p>
        <p><?php echo sprintf(esc_html__('Please note that our plugin works with the OpenAI API. To use it, you need to create an account on OpenAI and %sobtain your API key%s. OpenAI provides $5 in free credit for new users. If you encounter the message %s"You exceeded your current quota, please check your plan and billing details."%s it indicates that you have exhausted your OpenAI quota and need to purchase additional credit from OpenAI.','gpt3-ai-content-generator'),'<a href="https://beta.openai.com/account/api-keys" target="_blank">','</a>','<b>','</b>')?></p>
        <p><?php echo esc_html__('Purchasing our plugin does not provide any credit from OpenAI. When you buy our plugin, you gain access to the pro features of the plugin, but it does not include any API credit. You will still need to purchase credit from OpenAI separately.','gpt3-ai-content-generator')?></p>
    </div>
</div>
<script>
    jQuery(document).ready(function ($){
        $('#wpaicg_ai_model').on('change', function (){
            if($(this).val() === 'gpt-3.5-turbo' || $(this).val() === 'gpt-4' || $(this).val() === 'gpt-4-32k'){
                $('.wpaicg_sleep_time').show();
            }
            else{
                $('.wpaicg_sleep_time').hide();
            }
            if($(this).val() === 'gpt-4' || $(this).val() === 'gpt-4-32k'){
                $('.wpaicg_beta_notice').show();
            }
            else{
                $('.wpaicg_beta_notice').hide();
            }
        })
    })
</script>
