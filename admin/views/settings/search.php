<?php
if ( ! defined( 'ABSPATH' ) ) exit;
//$wpaicg_search_language = get_option('wpaicg_search_language','en');
$wpaicg_search_placeholder = get_option('wpaicg_search_placeholder',esc_html__('Search anything..','gpt3-ai-content-generator'));
$wpaicg_search_no_result = get_option('wpaicg_search_no_result','5');
$wpaicg_search_font_size = get_option('wpaicg_search_font_size','13');
$wpaicg_search_font_color = get_option('wpaicg_search_font_color','#000');
$wpaicg_search_border_color = get_option('wpaicg_search_border_color','#ccc');
$wpaicg_search_bg_color = get_option('wpaicg_search_bg_color','');
$wpaicg_search_width = get_option('wpaicg_search_width','100%');
$wpaicg_search_height = get_option('wpaicg_search_height','45px');
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');

$wpaicg_search_result_font_size = get_option('wpaicg_search_result_font_size','13');
$wpaicg_search_result_font_color = get_option('wpaicg_search_result_font_color','#000');
$wpaicg_search_result_bg_color = get_option('wpaicg_search_result_bg_color','');
$wpaicg_search_loading_color = get_option('wpaicg_search_loading_color','#ccc');

$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
?>
<div id="tabs-8">
    <?php
    if(empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment)):
        ?>
        <p><?php echo sprintf(esc_html__('It appears that you haven\'t entered your keys for Pinecone, which is why this feature is currently disabled. Please go to %sthis page%s and enter your keys.','gpt3-ai-content-generator'),'<a href="'.admin_url('admin.php?page=wpaicg_embeddings&action=settings').'">','</a>')?></p>
    <?php
    else:
    ?>
    <p><b><?php echo esc_html__('Usage','gpt3-ai-content-generator')?></b></p>
    <p><?php echo sprintf(esc_html__('Copy the following code and paste it in your page or post where you want to show the search box: %s','gpt3-ai-content-generator'),'<code>[wpaicg_search]</code>')?></p>
    <hr>
    <p><b><?php echo esc_html__('Search Box','gpt3-ai-content-generator')?></b></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Placeholder','gpt3-ai-content-generator')?>:</label>
        <input type="text" class="regular-text" name="wpaicg_search_placeholder" value="<?php
        echo  esc_html( get_option( 'wpaicg_search_placeholder', 'Search anything..' ) ) ;
        ?>" >
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Font Size','gpt3-ai-content-generator')?>:</label>
        <select name="wpaicg_search_font_size">
            <?php
            for($i = 10; $i <= 30; $i++){
                echo '<option'.($wpaicg_search_font_size == $i ? ' selected': '').' value="'.esc_html($i).'">'.esc_html($i).'px</option>';
            }
            ?>
        </select>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Font Color','gpt3-ai-content-generator')?>:</label>
        <input value="<?php echo esc_html($wpaicg_search_font_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_font_color">
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Border Color','gpt3-ai-content-generator')?>:</label>
        <input value="<?php echo esc_html($wpaicg_search_border_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_border_color">
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Background Color','gpt3-ai-content-generator')?>:</label>
        <input value="<?php echo esc_html($wpaicg_search_bg_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_bg_color">
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Width','gpt3-ai-content-generator')?>:</label>
        <input value="<?php echo esc_html($wpaicg_search_width)?>" style="width: 100px;" min="100" type="text" name="wpaicg_search_width"> (You can use percent or pixel)
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Height','gpt3-ai-content-generator')?>:</label>
        <input value="<?php echo esc_html($wpaicg_search_height)?>" style="width: 100px;" min="100" type="text" name="wpaicg_search_height"> (You can use percent or pixel)
    </div>
    <hr>
    <p><b><?php echo esc_html__('Results','gpt3-ai-content-generator')?></b></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Number of Results','gpt3-ai-content-generator')?>:</label>
        <select name="wpaicg_search_no_result">
            <?php
            for($i = 1; $i <=5;$i++){
                echo '<option'.($wpaicg_search_no_result == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
            }
            ?>
        </select>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Font Size','gpt3-ai-content-generator')?>:</label>
        <select name="wpaicg_search_result_font_size">
            <?php
            for($i = 10; $i <= 30; $i++){
                echo '<option'.($wpaicg_search_result_font_size == $i ? ' selected': '').' value="'.esc_html($i).'">'.esc_html($i).'px</option>';
            }
            ?>
        </select>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Font Color','gpt3-ai-content-generator')?>:</label>
        <input value="<?php echo esc_html($wpaicg_search_result_font_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_result_font_color">
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Background Color','gpt3-ai-content-generator')?>:</label>
        <input value="<?php echo esc_html($wpaicg_search_result_bg_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_result_bg_color">
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label"><?php echo esc_html__('Progress Background Color','gpt3-ai-content-generator')?>:</label>
        <input value="<?php echo esc_html($wpaicg_search_loading_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_loading_color">
    </div>
    <?php
    endif;
    ?>
</div>
