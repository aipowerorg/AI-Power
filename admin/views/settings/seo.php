<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div id="tabs-6">
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Meta Description','gpt3-ai-content-generator')?>:</label>
        <?php $_wpaicg_seo_meta_desc = get_option('_wpaicg_seo_meta_desc',false); ?>
        <input<?php echo $_wpaicg_seo_meta_desc ? ' checked':'';?> type="checkbox" name="_wpaicg_seo_meta_desc" value="1">
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/seo#enable-or-disable-meta-description-generation" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Include in HTML','gpt3-ai-content-generator')?>:</label>
        <?php $_wpaicg_seo_meta_tag = get_option('_wpaicg_seo_meta_tag',false); ?>
        <input<?php echo $_wpaicg_seo_meta_tag ? ' checked':'';?> type="checkbox" id="_wpaicg_seo_meta_tag" name="_wpaicg_seo_meta_tag" value="1">
        <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/seo#meta-description-in-html" target="_blank">?</a>
    </div>
    <?php
    if(is_plugin_active('wordpress-seo/wp-seo.php')){
        ?>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label"><?php echo esc_html__('Update Yoast Meta?','gpt3-ai-content-generator')?>:</label>
            <?php $_yoast_wpseo_metadesc = get_option('_yoast_wpseo_metadesc',false); ?>
            <input<?php echo $_yoast_wpseo_metadesc ? ' checked':'';?> id="_yoast_wpseo_metadesc" type="checkbox" name="_yoast_wpseo_metadesc" value="1">
            <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/seo#integrations" target="_blank">?</a>
        </div>
        <?php
    }
    if(is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php')){
        ?>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label"><?php echo esc_html__('Update All In One SEO Meta?','gpt3-ai-content-generator')?>:</label>
            <?php $_aioseo_description = get_option('_aioseo_description',false); ?>
            <input<?php echo $_aioseo_description ? ' checked':'';?> id="_aioseo_description" type="checkbox" name="_aioseo_description" value="1">
            <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/seo#integrations" target="_blank">?</a>
        </div>
        <?php
    }
    if(is_plugin_active('seo-by-rank-math/rank-math.php')){
        ?>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label"><?php echo esc_html__('Update Rank Math Meta?','gpt3-ai-content-generator')?>:</label>
            <?php $rank_math_description = get_option('rank_math_description',false); ?>
            <input<?php echo $rank_math_description ? ' checked':'';?> id="rank_math_description" type="checkbox" name="rank_math_description" value="1">
            <a class="wpcgai_help_link" href="https://docs.aipower.org/docs/content-writer/express-mode/seo#integrations" target="_blank">?</a>
        </div>
        <?php
    }
    ?>
</div>
<script>
    jQuery(document).ready(function ($){
        $('#_yoast_wpseo_metadesc').on('click', function (){
            if($(this).prop('checked')){
                $('#_wpaicg_seo_meta_tag').prop('checked', false);
                $('#_aioseo_description').prop('checked', false);
                $('#rank_math_description').prop('checked', false);
            }
        });
        $('#_aioseo_description').on('click', function (){
            if($(this).prop('checked')){
                $('#_wpaicg_seo_meta_tag').prop('checked', false);
                $('#_yoast_wpseo_metadesc').prop('checked', false);
                $('#rank_math_description').prop('checked', false);
            }
        });
        $('#_wpaicg_seo_meta_tag').on('click', function (){
            if($(this).prop('checked')){
                $('#_aioseo_description').prop('checked', false);
                $('#_yoast_wpseo_metadesc').prop('checked', false);
                $('#rank_math_description').prop('checked', false);
            }
        });
        $('#rank_math_description').on('click', function (){
            if($(this).prop('checked')){
                console.log('acccc');
                $('#_aioseo_description').prop('checked', false);
                $('#_yoast_wpseo_metadesc').prop('checked', false);
                $('#_wpaicg_seo_meta_tag').prop('checked', false);
            }
        });
    })
</script>
