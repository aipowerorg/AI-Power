<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<p><strong><?php echo esc_html__('Epochs','gpt3-ai-content-generator')?>: </strong><?php echo esc_html($wpaicg_data->n_epochs);?></p>
<p><strong><?php echo esc_html__('Batch size','gpt3-ai-content-generator')?>: </strong><?php echo esc_html($wpaicg_data->batch_size);?></p>
<p><strong><?php echo esc_html__('Prompt loss weight','gpt3-ai-content-generator')?>: </strong><?php echo esc_html($wpaicg_data->prompt_loss_weight);?></p>
<p><strong><?php echo esc_html__('Learning rate multiplier','gpt3-ai-content-generator')?>: </strong><?php echo esc_html($wpaicg_data->learning_rate_multiplier);?></p>
