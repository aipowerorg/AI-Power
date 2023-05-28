<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_all_post_types = get_post_types(array(
    'public'   => true,
    '_builtin' => false,
),'objects');
$wpaicg_custom_types = [];
foreach($wpaicg_all_post_types as $key=>$all_post_type) {
    if ($key != 'product') {
        $wpaicg_custom_types[$key] = (array)$all_post_type;
    }
}
if(count($wpaicg_custom_types)){
    foreach($wpaicg_custom_types as $key=>$wpaicg_custom_type){
        ?>
        <div class="mb-5">
            <label>
                <input disabled type="checkbox" >&nbsp;<?php echo esc_html($wpaicg_custom_type['label'])?>
            </label>
            <input class="wpaicg_builder_custom_<?php echo esc_html($key)?>" type="hidden">
            <a disabled
                href="javascript:void(0)">
                [<?php echo esc_html__('Select Fields','gpt3-ai-content-generator')?>]
            </a>
            <span style="font-size: 13px;display: inline-block;margin: 0 5px;background: #ffba00;padding: 2px 5px;border-radius: 3px;color: #000;font-weight: bold;"><?php echo esc_html__('Pro','gpt3-ai-content-generator')?></span>
        </div>
        <?php
    }
}
