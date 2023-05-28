<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_product_sale_type = get_post_meta($post->ID,'wpaicg_product_sale_type',true);
$wpaicg_product_sale_tokens = get_post_meta($post->ID,'wpaicg_product_sale_tokens',true);
?>
<p class="wpaicg-form-row">
    <label><strong><?php echo esc_html__('Sell Token For?','gpt3-ai-content-generator')?></strong></label>
    <select name="wpaicg_product_sale_type">
        <option value=""><?php echo esc_html__('None','gpt3-ai-content-generator')?></option>
        <?php
        if($this->chat_sale):
        ?>
        <option<?php echo $wpaicg_product_sale_type == 'chat' ? ' selected':''?> value="chat"><?php echo esc_html__('ChatGPT','gpt3-ai-content-generator')?></option>
        <?php
        endif;
        ?>
        <?php
        if($this->image_sale):
        ?>
        <option<?php echo $wpaicg_product_sale_type == 'image' ? ' selected':''?> value="image"><?php echo esc_html__('Image Generator','gpt3-ai-content-generator')?></option>
        <?php
        endif;
        ?>
        <?php
        if($this->forms_sale):
        ?>
        <option<?php echo $wpaicg_product_sale_type == 'forms' ? ' selected':''?> value="forms"><?php echo esc_html__('AI Forms','gpt3-ai-content-generator')?></option>
        <?php
        endif;
        ?>
        <?php
        if($this->promptbase_sale):
        ?>
        <option<?php echo $wpaicg_product_sale_type == 'promptbase' ? ' selected':''?> value="promptbase"><?php echo esc_html__('Promptbase','gpt3-ai-content-generator')?></option>
        <?php
        endif;
        ?>
    </select>
</p>
<p class="wpaicg-form-row">
    <label><strong><?php echo esc_html__('Token Amount','gpt3-ai-content-generator')?>:</strong></label>
    <input type="number" value="<?php echo esc_html($wpaicg_product_sale_tokens)?>" name="wpaicg_product_sale_tokens">
</p>
