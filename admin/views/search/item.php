<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if($wpaicg_key == 1){
    $key_text = '1st';
}
elseif($wpaicg_key == 2){
    $key_text = '2nd';
}
else if($wpaicg_key == 3){
    $key_text = '3rd';
}
else{
    $key_text = $wpaicg_key.'th';
}
$post_parent = get_post_meta($post_id,'wpaicg_parent',true);
$post_content = strip_tags($embedding->post_content);
$source_data = false;
if(!empty($post_parent)){
    $source_data = get_post($post_parent);
}
?>
<div class="wpaicg-search-item">
    <div class="wpaicg-search-item-title">
        <?php
        if(!$source_data){
            ?>
        <a href="javascript:void(0)" onclick="wpaicgInit.searchExpand(this)">
            <?php
        }
        else{
        ?>
            <a href="<?php echo get_permalink($source_data)?>" target="_blank">
        <?php
        }
        ?>
        <?php echo esc_html(substr($embedding->post_title,0,25)).(strlen($embedding->post_title) > 25 ? '...':'')?>
        </a>
    </div>
    <div class="wpaicg-search-item-date">
        <?php
        echo esc_html__('Posted','gpt3-ai-content-generator');
        if(!$source_data){
            echo get_the_date('',$embedding);
        }
        else{
            echo get_the_date('',$source_data);
        }
        ?>
    </div>
    <div class="wpaicg-search-item-content">
        <?php
        if(strlen($post_content) < 400){
            echo esc_html($post_content);
        }
        else{
        ?>
            <div class="wpaicg-search-item-excerpt">
                <?php
                echo substr(esc_html($post_content),0,400).'..';
                if(!$source_data){
                    ?>
                    <a href="javascript:void(0)" onclick="wpaicgInit.searchExpand(this)"><?php echo esc_html__('[Read more]','gpt3-ai-content-generator')?></a>
                    <?php
                }
                else{
                    ?>
                    <a href="<?php echo get_permalink($source_data)?>" target="_blank"><?php echo esc_html__('[Read more]','gpt3-ai-content-generator')?></a>
                    <?php
                }
                ?>
            </div>
            <?php
            if(empty($post_parent)){
                ?>
                <div class="wpaicg-search-item-full" style="display: none;"><?php echo esc_html($post_content)?></div>
                <?php
            }
            ?>
        <?php
        }
        ?>
    </div>
</div>
