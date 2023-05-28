<?php
if ( ! defined( 'ABSPATH' ) ) exit;
    ?>
    <div id="tabs-7">
        <h3>Product Writer</h3>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label"><?php echo esc_html__('Write a SEO friendly product title?','gpt3-ai-content-generator')?>:</label>
            <?php $wpaicg_woo_generate_title = get_option('wpaicg_woo_generate_title',false); ?>
            <input<?php echo $wpaicg_woo_generate_title ? ' checked':'';?> type="checkbox" name="wpaicg_woo_generate_title" value="1">
            <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/woocommerce#woocommerce-product-writer" target="_blank">?</a>
        </div>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label"><?php echo esc_html__('Write a SEO Meta Description?','gpt3-ai-content-generator')?>:</label>
            <?php $wpaicg_woo_meta_description = get_option('wpaicg_woo_meta_description',false); ?>
            <input<?php echo $wpaicg_woo_meta_description ? ' checked':'';?> type="checkbox" name="wpaicg_woo_meta_description" value="1">
            <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/woocommerce#woocommerce-product-writer" target="_blank">?</a>
        </div>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label"><?php echo esc_html__('Write a product description?','gpt3-ai-content-generator')?>:</label>
            <?php $wpaicg_woo_generate_description = get_option('wpaicg_woo_generate_description',false); ?>
            <input<?php echo $wpaicg_woo_generate_description ? ' checked':'';?> type="checkbox" name="wpaicg_woo_generate_description" value="1">
            <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/woocommerce#woocommerce-product-writer" target="_blank">?</a>
        </div>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label"><?php echo esc_html__('Write a short product description?','gpt3-ai-content-generator')?>:</label>
            <?php $wpaicg_woo_generate_short = get_option('wpaicg_woo_generate_short',false); ?>
            <input<?php echo $wpaicg_woo_generate_short ? ' checked':'';?> type="checkbox" name="wpaicg_woo_generate_short" value="1">
            <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/woocommerce#woocommerce-product-writer" target="_blank">?</a>
        </div>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label"><?php echo esc_html__('Generate product tags?','gpt3-ai-content-generator')?>:</label>
            <?php $wpaicg_woo_generate_tags = get_option('wpaicg_woo_generate_tags',false); ?>
            <input<?php echo $wpaicg_woo_generate_tags ? ' checked':'';?> type="checkbox" name="wpaicg_woo_generate_tags" value="1">
            <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/woocommerce#woocommerce-product-writer" target="_blank">?</a>
        </div>
        <?php
        $wpaicg_woo_custom_prompt = get_option('wpaicg_woo_custom_prompt',false);
        $wpaicg_woo_custom_prompt_title = get_option('wpaicg_woo_custom_prompt_title',esc_html__('Write a SEO friendly product title: %s.','gpt3-ai-content-generator'));
        $wpaicg_woo_custom_prompt_short = get_option('wpaicg_woo_custom_prompt_short',esc_html__('Summarize this product in 2 short sentences: %s.','gpt3-ai-content-generator'));
        $wpaicg_woo_custom_prompt_description = get_option('wpaicg_woo_custom_prompt_description',esc_html__('Write a detailed product description about: %s.','gpt3-ai-content-generator'));
        $wpaicg_woo_custom_prompt_keywords = get_option('wpaicg_woo_custom_prompt_keywords',esc_html__('Suggest keywords for this product: %s.','gpt3-ai-content-generator'));
        $wpaicg_woo_custom_prompt_meta = get_option('wpaicg_woo_custom_prompt_meta',esc_html__('Write a meta description about: %s. Max: 155 characters.','gpt3-ai-content-generator'));
        ?>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label"><?php echo esc_html__('Use Custom Prompt','gpt3-ai-content-generator')?>:</label>
            <input<?php echo $wpaicg_woo_custom_prompt ? ' checked':'';?> type="checkbox" class="wpaicg_woo_custom_prompt" name="wpaicg_woo_custom_prompt" value="1">
            <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/woocommerce#customizing-prompts" target="_blank">?</a>
        </div>
        <div<?php echo $wpaicg_woo_custom_prompt ? '':' style="display:none"';?> class="wpaicg_woo_custom_prompts">
            <div class="wpcgai_form_row">
                <label class="wpcgai_label"><?php echo esc_html__('Title Prompt','gpt3-ai-content-generator')?>:</label>
                <textarea style="width: 65%;" type="text" name="wpaicg_woo_custom_prompt_title"><?php echo esc_html($wpaicg_woo_custom_prompt_title);?></textarea>
            </div>
            <div class="wpcgai_form_row">
                <label class="wpcgai_label"><?php echo esc_html__('Short description prompt','gpt3-ai-content-generator')?>:</label>
                <textarea style="width: 65%;" type="text" name="wpaicg_woo_custom_prompt_short"><?php echo esc_html($wpaicg_woo_custom_prompt_short);?></textarea>
            </div>
            <div class="wpcgai_form_row">
                <label class="wpcgai_label"><?php echo esc_html__('Description prompt','gpt3-ai-content-generator')?>:</label>
                <textarea style="width: 65%;" type="text" name="wpaicg_woo_custom_prompt_description"><?php echo esc_html($wpaicg_woo_custom_prompt_description);?></textarea>
            </div>
            <div class="wpcgai_form_row">
                <label class="wpcgai_label"><?php echo esc_html__('Meta Description prompt','gpt3-ai-content-generator')?>:</label>
                <textarea style="width: 65%;" type="text" name="wpaicg_woo_custom_prompt_meta"><?php echo esc_html($wpaicg_woo_custom_prompt_meta);?></textarea>
            </div>
            <div class="wpcgai_form_row">
                <label class="wpcgai_label"><?php echo esc_html__('Keywords prompt','gpt3-ai-content-generator')?>:</label>
                <textarea style="width: 65%;" type="text" name="wpaicg_woo_custom_prompt_keywords"><?php echo esc_html($wpaicg_woo_custom_prompt_keywords);?></textarea>
            </div>
        </div>
        <h3><?php echo esc_html__('Token Sale','gpt3-ai-content-generator')?></h3>
        <?php
        $wpaicg_order_status_token = get_option('wpaicg_order_status_token','completed');
        ?>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label"><?php echo esc_html__('Add tokens to user account if order status is','gpt3-ai-content-generator')?>: </label>
            <select name="wpaicg_order_status_token">
                <option<?php echo $wpaicg_order_status_token == 'completed'? ' selected':''?> value="completed"><?php echo esc_html__('Completed','gpt3-ai-content-generator')?></option>
                <option<?php echo $wpaicg_order_status_token == 'processing'? ' selected':''?> value="processing"><?php echo esc_html__('Processing','gpt3-ai-content-generator')?></option>
            </select>
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/user-management-token-sale" target="_blank">?</a>
        </div>
    </div>
<script>
    jQuery(document).ready(function ($){
        $('.wpaicg_woo_custom_prompt').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_woo_custom_prompts').show();
            }
            else{
                $('.wpaicg_woo_custom_prompts').hide();
            }
        })
    })
</script>
