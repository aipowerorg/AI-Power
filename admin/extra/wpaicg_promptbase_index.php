<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$wpaicg_categories = array();
$wpaicg_items = array();
$wpaicg_icons = array();
$wpaicg_models = array();
$wpaicg_authors = array('default' => array('name' => 'AI Power','count' => 0));
if(file_exists(WPAICG_PLUGIN_DIR.'admin/data/categories.json')){
    $wpaicg_file_content = file_get_contents(WPAICG_PLUGIN_DIR.'admin/data/categories.json');
    $wpaicg_file_content = json_decode($wpaicg_file_content, true);
    if($wpaicg_file_content && is_array($wpaicg_file_content) && count($wpaicg_file_content)){
        foreach($wpaicg_file_content as $key=>$item){
            $wpaicg_categories[$key] = trim($item);
        }
    }
}
if(file_exists(WPAICG_PLUGIN_DIR.'admin/data/icons.json')){
    $wpaicg_file_content = file_get_contents(WPAICG_PLUGIN_DIR.'admin/data/icons.json');
    $wpaicg_file_content = json_decode($wpaicg_file_content, true);
    if($wpaicg_file_content && is_array($wpaicg_file_content) && count($wpaicg_file_content)){
        foreach($wpaicg_file_content as $key=>$item){
            $wpaicg_icons[$key] = trim($item);
        }
    }
}
if(file_exists(WPAICG_PLUGIN_DIR.'admin/data/prompts.json')){
    $wpaicg_file_content = file_get_contents(WPAICG_PLUGIN_DIR.'admin/data/prompts.json');
    $wpaicg_file_content = json_decode($wpaicg_file_content, true);
    if($wpaicg_file_content && is_array($wpaicg_file_content) && count($wpaicg_file_content)){
        foreach($wpaicg_file_content as $item){
            $item['type'] = 'json';
            $item['author'] = 'default';
            $wpaicg_authors['default']['count'] += 1;
            $wpaicg_items[] = $item;
        }
    }
}
if(file_exists(WPAICG_PLUGIN_DIR.'admin/data/models.json')){
    $wpaicg_file_content = file_get_contents(WPAICG_PLUGIN_DIR.'admin/data/models.json');
    $wpaicg_file_content = json_decode($wpaicg_file_content, true);
    if($wpaicg_file_content && is_array($wpaicg_file_content) && isset($wpaicg_file_content['models']) && is_array($wpaicg_file_content['models']) && count($wpaicg_file_content['models'])){
        foreach($wpaicg_file_content['models'] as $item){
            $wpaicg_models[] = $item['name'];
        }
    }
}
$sql = "SELECT p.ID as id,p.post_title as title,p.post_author as author, p.post_content as description";
$wpaicg_meta_keys = array('prompt','editor','response','category','engine','max_tokens','temperature','top_p','best_of','frequency_penalty','presence_penalty','stop','color','icon','bgcolor','header','dans','ddraft','dclear','dnotice','generate_text','noanswer_text','draft_text','clear_text','stop_text','cnotice_text','download_text','ddownload');
foreach($wpaicg_meta_keys as $wpaicg_meta_key){
//    $sql .= ",(SELECT ".$wpaicg_meta_key.".meta_value FROM ".$wpdb->postmeta." ".$wpaicg_meta_key." WHERE ".$wpaicg_meta_key.".meta_key='wpaicg_prompt_".$wpaicg_meta_key."' AND p.ID=".$wpaicg_meta_key.".post_id LIMIT 1) as ".$wpaicg_meta_key;
    $sql .= ", (".$wpdb->prepare("SELECT ".$wpaicg_meta_key.".meta_value FROM ".$wpdb->postmeta." ".$wpaicg_meta_key." WHERE ".$wpaicg_meta_key.".meta_key=%s AND p.ID=".$wpaicg_meta_key.".post_id LIMIT 1",
            'wpaicg_prompt_'.$wpaicg_meta_key
        ).") as  ".$wpaicg_meta_key;
}
//$sql .= " FROM ".$wpdb->posts." p WHERE p.post_type = 'wpaicg_prompt' AND p.post_status='publish' ORDER BY p.post_date DESC";
$sql .= $wpdb->prepare(" FROM ".$wpdb->posts." p WHERE p.post_type = 'wpaicg_prompt' AND p.post_status='publish' ORDER BY p.post_date DESC");
$wpaicg_custom_prompts = $wpdb->get_results($sql,ARRAY_A);
if($wpaicg_custom_prompts && is_array($wpaicg_custom_prompts) && count($wpaicg_custom_prompts)){
    foreach ($wpaicg_custom_prompts as $wpaicg_custom_prompt){
        $wpaicg_custom_prompt['type'] = 'custom';
        $wpaicg_items[] = $wpaicg_custom_prompt;
        if(!isset($wpaicg_authors[$wpaicg_custom_prompt['author']])){
            $prompt_author = get_user_by('ID', $wpaicg_custom_prompt['author']);
            $wpaicg_authors[$wpaicg_custom_prompt['author']] = array('name' => $prompt_author->display_name, 'count' => 1);
        }
        else{
            $wpaicg_authors[$wpaicg_custom_prompt['author']]['count'] += 1;
        }
    }
}
$wpaicg_per_page = 36;
wp_enqueue_editor();
wp_enqueue_script('wp-color-picker');
wp_enqueue_style('wp-color-picker');
$kses_defaults = wp_kses_allowed_html( 'post' );
$svg_args = array(
    'svg'   => array(
        'class'           => true,
        'aria-hidden'     => true,
        'aria-labelledby' => true,
        'role'            => true,
        'xmlns'           => true,
        'width'           => true,
        'height'          => true,
        'viewbox'         => true // <= Must be lower case!
    ),
    'g'     => array( 'fill' => true ),
    'title' => array( 'title' => true ),
    'path'  => array(
        'd'               => true,
        'fill'            => true
    )
);
$allowed_tags = array_merge( $kses_defaults, $svg_args );
?>
<style>
    .wpaicg-prompt-icon{
        width: 70px;
        height: 70px;
        border-radius: 3px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #fff;
    }
    .wpaicg-prompt-icon svg{
        fill: currentColor;
        width: 50px;
        height: 50px;
    }
    .wpaicg-prompt-item{
        cursor: pointer;
        height: 100px;
        position: relative;
    }
    .wpaicg-prompt-content{
        margin-left: 10px;
        flex: 1;
    }
    .wpaicg-prompt-content p{
        margin: 5px 0;
        font-size: 12px;
        height: 36px;
        overflow: hidden;
    }
    .wpaicg_modal{
        position: relative;
        top: 5%;
        height: 90%;
    }
    .disappear-item{
        position: absolute;
        top: -10000px;
    }
    .wpaicg-prompt-items{
        position: relative;
        overflow-y: hidden;
    }
    .wpaicg-paginate .page-numbers{
        background: #e5e5e5;
        margin-right: 5px;
        cursor: pointer;
    }
    .wpaicg-paginate .page-numbers.current{
        background: #fff;
    }
    .wpaicg-prompt-settings > div{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .wpaicg-prompt-settings > div > strong{
        display: inline-block;
        width: 50%;
    }
    .wpaicg-prompt-settings > div > strong > small{
        font-weight: normal;
        display: block;
    }
    .wpaicg-prompt-settings > div > input,.wpaicg-prompt-settings > div > select{
        width: 48%;
        margin: 0;
    }
    .wpaicg-prompt-settings .wpaicg-prompt-sample{
        display: block;
        position: relative;
    }
    .wpaicg-prompt-settings .wpaicg-prompt-sample:hover .wpaicg-prompt-response{
        display: block;
    }
    .wpaicg-prompt-settings .wpaicg-prompt-response{
        background: #333;
        border: 1px solid #444;
        position: absolute;
        border-radius: 3px;
        color: #fff;
        padding: 5px;
        width: 100%;
        bottom: calc(100% + 5px);
        right: calc(50% - 55px);
        z-index: 99;
        display: none;
    }
    .wpaicg-prompt-settings .wpaicg-prompt-response:after,.wpaicg-prompt-settings .wpaicg-prompt-response:before{
        top: 100%;
        left: 50%;
        border: solid transparent;
        content: "";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }
    .wpaicg-prompt-settings .wpaicg-prompt-response:before{
        border-color: rgba(68, 68, 68, 0);
        border-top-color: #444;
        border-width: 7px;
        margin-left: -7px;
    }
    .wpaicg-prompt-settings .wpaicg-prompt-response:after{
        border-color: rgba(51, 51, 51, 0);
        border-top-color: #333;
        border-width: 6px;
        margin-left: -6px;
    }
    .wpaicg_modal_content{
        max-height: calc(100% - 103px);
        overflow-y: auto;
    }
    .wpaicg_notice_text {
        padding: 10px;
        background-color: #F8DC6F;
        text-align: left;
        margin-bottom: 12px;
        color: #000;
        box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    }
    .wpaicg-create-prompt{
        width: 100%;
        display: block!important;
        margin-bottom: 10px!important;
    }
    .wpaicg-prompt-icons{}
    .wpaicg-prompt-icons span{
        padding: 10px;
        border-radius: 4px;
        border: 1px solid #ccc;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin-right: 5px;
        margin-bottom: 5px;
        cursor: pointer;
        color: #333;
    }
    .wpaicg-prompt-icons span svg{
        fill: currentColor;
        width: 30px;
        height: 30px;
    }
    .wpaicg-prompt-icons span.icon_selected{
        background: #343434;
        color: #fff;
    }
    .wp-picker-holder{
        position: absolute;
    }
    .wp-picker-container{
        position: relative;
    }
    .wpaicg-prompt-action{
        position: absolute;
        right: 0;
        top: 37px;
        display: none;
    }
    .wpaicg-prompt-item:hover .wpaicg-prompt-action{
        display: block;
    }
    .wpaicg-prompt-action-edit{}
    .wpaicg-prompt-action-delete{
        background: #9d0000!important;
        border-color: #9b0000!important;
        color: #fff!important;
    }
    .wpaicg-modal-tabs{
        margin: 0;
        display: flex;
    }
    .wpaicg-modal-tabs li{
        padding: 12px 15px;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        background: #2271b1;
        margin-bottom: 0;
        margin-right: 5px;
        border-top: 1px solid #2271b1;
        border-left: 1px solid #2271b1;
        border-right: 1px solid #2271b1;
        cursor: pointer;
        position: relative;
        top: 1px;
        color: #fff;
    }
    .wpaicg-modal-tabs li.wpaicg-active{
        background: #fff;
        color: #333;
    }
    .wpaicg-modal-tab-content{
        border: 1px solid #ccc;
    }
    .wpaicg-modal-tab{
        padding: 10px;
    }
    .wpaicg_notice_text_rw_b {
    padding: 10px;
    text-align: left;
    margin-bottom: 12px;
    }
    .wpaicg-prompt-item:before{
        display:none;
    }
</style>
<div class="wpaicg-create-prompt-content" style="display: none">
    <?php
    wp_nonce_field('wpaicg_promptbase_save');
    ?>
    <input type="hidden" name="action" value="wpaicg_update_prompt">
    <input type="hidden" name="id" value="" class="wpaicg-create-prompt-id">
    <ul class="wpaicg-modal-tabs">
        <li class="wpaicg-active" data-target="properties"><?php echo esc_html__('Properties','gpt3-ai-content-generator')?></li>
        <li data-target="ai-engine"><?php echo esc_html__('AI Engine','gpt3-ai-content-generator')?></li>
        <li data-target="style"><?php echo esc_html__('Style','gpt3-ai-content-generator')?></li>
        <li data-target="frontend"><?php echo esc_html__('Frontend','gpt3-ai-content-generator')?></li>
    </ul>
    <div class="wpaicg-modal-tab-content wpaicg-mb-10">
        <div class="wpaicg-modal-tab wpaicg-modal-tab-properties">
            <div class="wpaicg-grid wpaicg-mb-10">
                <div class="wpaicg-grid-3">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Title','gpt3-ai-content-generator')?></strong>
                    <input type="text" name="title" required class="regular-text wpaicg-w-100 wpaicg-create-prompt-title">
                </div>
                <div class="wpaicg-grid-3">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Category','gpt3-ai-content-generator')?></strong>
                    <select name="category" class="wpaicg-w-100 wpaicg-create-prompt-category">
                        <?php
                        foreach($wpaicg_categories as $key=>$wpaicg_category){
                            echo '<option value="'.esc_html($key).'">'.esc_html($wpaicg_category).'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="wpaicg-mb-10">
                <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Description','gpt3-ai-content-generator')?></strong>
                <input type="text" name="description" required class="regular-text wpaicg-w-100 wpaicg-create-prompt-description">
            </div>
            <div class="wpaicg-mb-10">
                <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Prompt','gpt3-ai-content-generator')?></strong>
                <textarea name="prompt" required class="regular-text wpaicg-w-100 wpaicg-create-prompt-prompt"></textarea>
            </div>
            <div class="wpaicg-mb-10">
                <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Sample Response','gpt3-ai-content-generator')?></strong>
                <textarea name="response" class="regular-text wpaicg-w-100 wpaicg-create-prompt-response"></textarea>
            </div>
        </div>
        <div class="wpaicg-modal-tab wpaicg-modal-tab-ai-engine" style="display: none">
            <div class="wpaicg-grid wpaicg-mb-10">
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Engine','gpt3-ai-content-generator')?></strong>
                    <select name="engine" class="wpaicg-w-100 wpaicg-create-prompt-engine" required>
                        <option value="gpt-3.5-turbo">gpt-3.5-turbo</option>
                        <?php
                        foreach($wpaicg_models as $wpaicg_model){
                            echo '<option value="' . esc_html($wpaicg_model) . '">' . esc_html($wpaicg_model) . '</option>';
                        }
                        ?>
                        <option value="gpt-4">gpt-4</option>
                        <option value="gpt-4-32k">gpt-4-32k</option>
                    </select>
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Max Tokens','gpt3-ai-content-generator')?></strong>
                    <input type="text" name="max_tokens" class="regular-text wpaicg-w-100 wpaicg-create-prompt-max_tokens">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Temperature','gpt3-ai-content-generator')?></strong>
                    <input type="text" name="temperature" class="regular-text wpaicg-w-100 wpaicg-create-prompt-temperature">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Top P','gpt3-ai-content-generator')?></strong>
                    <input type="text" name="top_p" class="regular-text wpaicg-w-100 wpaicg-create-prompt-top_p">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Best Of','gpt3-ai-content-generator')?></strong>
                    <input type="text" name="best_of" class="regular-text wpaicg-w-100 wpaicg-create-prompt-best_of">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Frequency Penalty','gpt3-ai-content-generator')?></strong>
                    <input type="text" name="frequency_penalty" class="regular-text wpaicg-w-100 wpaicg-create-prompt-frequency_penalty">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Presence Penalty','gpt3-ai-content-generator')?></strong>
                    <input type="text" name="presence_penalty" class="regular-text wpaicg-w-100 wpaicg-create-prompt-presence_penalty">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Stop','gpt3-ai-content-generator')?></strong>
                    <input type="text" name="stop" class="regular-text wpaicg-w-100 wpaicg-create-prompt-stop">
                </div>
            </div>
        </div>
        <div class="wpaicg-modal-tab wpaicg-modal-tab-style" style="display: none">
            <div class="wpaicg-grid wpaicg-mb-10">
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Icon Color','gpt3-ai-content-generator')?></strong>
                    <input type="text" name="color" class="regular-text wpaicg-w-100 wpaicg-create-prompt-color">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Background Color','gpt3-ai-content-generator')?></strong>
                    <input type="text" name="bgcolor" class="regular-text wpaicg-w-100 wpaicg-create-prompt-bgcolor">
                </div>
            </div>
            <div class="wpaicg-mb-10">
                <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Icon','gpt3-ai-content-generator')?></strong>
                <input type="hidden" class="wpaicg-create-prompt-icon" name="icon" value="robot">
                <div class="wpaicg-prompt-icons">
                    <?php
                    foreach($wpaicg_icons as $key=>$wpaicg_icon){
                        echo '<span data-key="'.esc_html($key).'">'.wp_kses($wpaicg_icon,$allowed_tags).'</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="wpaicg-modal-tab wpaicg-modal-tab-frontend" style="display: none">
            <h3><strong><?php echo esc_html__('Response','gpt3-ai-content-generator')?></strong></h3>
            <div class="wpaicg-grid wpaicg-mb-10">
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Result','gpt3-ai-content-generator')?></strong>
                    <select name="editor" class="wpaicg-w-100 wpaicg-create-prompt-editor">
                        <option value="textarea"><?php echo esc_html__('Text Editor','gpt3-ai-content-generator')?></option>
                        <option value="div"><?php echo esc_html__('Inline','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
            </div>
            <h3><strong><?php echo esc_html__('Display','gpt3-ai-content-generator')?></strong></h3>
            <div class="wpaicg-grid wpaicg-mb-10">
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Header','gpt3-ai-content-generator')?></strong>
                    <select name="header" class="wpaicg-w-100 wpaicg-create-prompt-header">
                        <option value=""><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        <option value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Num. Answers','gpt3-ai-content-generator')?></strong>
                    <select name="dans" class="wpaicg-w-100 wpaicg-create-prompt-dans">
                        <option value=""><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        <option value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Draft Button','gpt3-ai-content-generator')?></strong>
                    <select name="ddraft" class="wpaicg-w-100 wpaicg-create-prompt-ddraft">
                        <option value=""><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        <option value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Clear Button','gpt3-ai-content-generator')?></strong>
                    <select name="dclear" class="wpaicg-w-100 wpaicg-create-prompt-dclear">
                        <option value=""><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        <option value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Notice','gpt3-ai-content-generator')?></strong>
                    <select name="dnotice" class="wpaicg-w-100 wpaicg-create-prompt-dnotice">
                        <option value=""><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        <option value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Download','gpt3-ai-content-generator')?></strong>
                    <select name="ddownload" class="wpaicg-w-100 wpaicg-create-prompt-ddownload">
                        <option value=""><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        <option value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
            </div>
            <h3><strong><?php echo esc_html__('Custom Text','gpt3-ai-content-generator')?></strong></h3>
            <div class="wpaicg-grid wpaicg-mb-10">
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Generate Button','gpt3-ai-content-generator')?></strong>
                    <input value="<?php echo esc_html__('Generate','gpt3-ai-content-generator')?>" type="text" name="generate_text" class="regular-text wpaicg-w-100 wpaicg-create-prompt-generate_text">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Num. Answers Text','gpt3-ai-content-generator')?></strong>
                    <input value="<?php echo esc_html__('Number of Answers','gpt3-ai-content-generator')?>" type="text" name="noanswer_text" class="regular-text wpaicg-w-100 wpaicg-create-prompt-noanswer_text">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Draft Text','gpt3-ai-content-generator')?></strong>
                    <input value="<?php echo esc_html__('Save Draft','gpt3-ai-content-generator')?>" type="text" name="draft_text" class="regular-text wpaicg-w-100 wpaicg-create-prompt-draft_text">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Clear Text','gpt3-ai-content-generator')?></strong>
                    <input value="<?php echo esc_html__('Clear','gpt3-ai-content-generator')?>" type="text" name="clear_text" class="regular-text wpaicg-w-100 wpaicg-create-prompt-clear_text">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Stop Text','gpt3-ai-content-generator')?></strong>
                    <input value="<?php echo esc_html__('Stop','gpt3-ai-content-generator')?>" type="text" name="stop_text" class="regular-text wpaicg-w-100 wpaicg-create-prompt-stop_text">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Notice Text','gpt3-ai-content-generator')?></strong>
                    <input value="<?php echo esc_html__('Please register to save your result','gpt3-ai-content-generator')?>" type="text" name="cnotice_text" class="regular-text wpaicg-w-100 wpaicg-create-prompt-cnotice_text">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5"><?php echo esc_html__('Download Text','gpt3-ai-content-generator')?></strong>
                    <input value="<?php echo esc_html__('Download','gpt3-ai-content-generator')?>" type="text" name="download_text" class="regular-text wpaicg-w-100 wpaicg-create-prompt-download_text">
                </div>
            </div>
        </div>
    </div>
    <button class="button button-primary wpaicg-create-prompt-save"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
</div>
<?php
if(isset($_GET['update_prompt']) && !empty($_GET['update_prompt'])):
    ?>
    <p style="padding: 6px 12px;border: 1px solid green;border-radius: 3px;background: lightgreen;">
        <strong><?php echo esc_html__('Success','gpt3-ai-content-generator')?>:</strong> <?php echo esc_html__('Congrats! Your prompt created! You can add this shortcode to your page','gpt3-ai-content-generator')?>: [wpaicg_prompt id=<?php echo esc_html($_GET['update_prompt'])?> custom=yes]
    </p>
<?php
endif;
?>
<div class="wpaicg_promptbase">
    <div class="wpaicg-grid">
        <div class="wpaicg-grid-1">
            <button class="button button-primary wpaicg-create-prompt" type="button"><?php echo esc_html__('Design Your Prompt','gpt3-ai-content-generator')?></button>
            <strong><?php echo esc_html__('Author','gpt3-ai-content-generator')?></strong>
            <ul class="wpaicg-list wpaicg-mb-10 wpaicg-authors">
                <?php
                if(count($wpaicg_authors)){
                    foreach($wpaicg_authors as $key=>$wpaicg_author){
                        ?>
                        <li><label><input type="checkbox" value="<?php echo esc_attr($key)?>">&nbsp;<?php echo esc_html($wpaicg_author['name'])?> (<?php echo esc_html($wpaicg_author['count'])?>)</label></li>
                        <?php
                    }
                }
                ?>
            </ul>
            <strong><?php echo esc_html__('Category','gpt3-ai-content-generator')?></strong>
            <ul class="wpaicg-list wpaicg-categories">
                <?php
                if(count($wpaicg_categories)){
                    foreach($wpaicg_categories as $wpaicg_category){
                        ?>
                        <li><label><input type="checkbox" value="<?php echo sanitize_title($wpaicg_category)?>">&nbsp;<?php echo esc_html($wpaicg_category)?></label></li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
        <div class="wpaicg-grid-5">
            <div class="wpaicg-mb-10">
                <input class="wpaicg-w-100 wpaicg-d-block wpaicg-prompt-search" type="text" placeholder="<?php echo esc_html__('Search Prompt','gpt3-ai-content-generator')?>">
            </div>
            <div class="wpaicg-grid-three wpaicg-prompt-items">
                <?php
                if(count($wpaicg_items)):
                    foreach($wpaicg_items as $wpaicg_item):
                        $wpaicg_item_categories = array();
                        $wpaicg_item_categories_name = array();
                        if(isset($wpaicg_item['category']) && !empty($wpaicg_item['category'])){
                            $wpaicg_item_categories = array_map('trim', explode(',', $wpaicg_item['category']));
                        }
                        $wpaicg_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M320 0c17.7 0 32 14.3 32 32V96H480c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H160c-35.3 0-64-28.7-64-64V160c0-35.3 28.7-64 64-64H288V32c0-17.7 14.3-32 32-32zM208 384c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H208zm96 0c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H304zm96 0c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H400zM264 256c0-22.1-17.9-40-40-40s-40 17.9-40 40s17.9 40 40 40s40-17.9 40-40zm152 40c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40s17.9 40 40 40zM48 224H64V416H48c-26.5 0-48-21.5-48-48V272c0-26.5 21.5-48 48-48zm544 0c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H576V224h16z"/></svg>';
                        if(isset($wpaicg_item['icon']) && !empty($wpaicg_item['icon']) && isset($wpaicg_icons[$wpaicg_item['icon']]) && !empty($wpaicg_icons[$wpaicg_item['icon']])){
                            $wpaicg_icon = $wpaicg_icons[$wpaicg_item['icon']];
                        }
                        $wpaicg_icon_color = isset($wpaicg_item['color']) && !empty($wpaicg_item['color']) ? $wpaicg_item['color'] : '#19c37d';
                        $wpaicg_engine = isset($wpaicg_item['engine']) && !empty($wpaicg_item['engine']) ? $wpaicg_item['engine'] : $this->wpaicg_engine;
                        $wpaicg_max_tokens = isset($wpaicg_item['max_tokens']) && !empty($wpaicg_item['max_tokens']) ? $wpaicg_item['max_tokens'] : $this->wpaicg_max_tokens;
                        $wpaicg_temperature = isset($wpaicg_item['temperature']) && !empty($wpaicg_item['temperature']) ? $wpaicg_item['temperature'] : $this->wpaicg_temperature;
                        $wpaicg_top_p = isset($wpaicg_item['top_p']) && !empty($wpaicg_item['top_p']) ? $wpaicg_item['top_p'] : $this->wpaicg_top_p;
                        $wpaicg_best_of = isset($wpaicg_item['best_of']) && !empty($wpaicg_item['best_of']) ? $wpaicg_item['best_of'] : $this->wpaicg_best_of;
                        $wpaicg_frequency_penalty = isset($wpaicg_item['frequency_penalty']) && !empty($wpaicg_item['frequency_penalty']) ? $wpaicg_item['frequency_penalty'] : $this->wpaicg_frequency_penalty;
                        $wpaicg_presence_penalty = isset($wpaicg_item['presence_penalty']) && !empty($wpaicg_item['presence_penalty']) ? $wpaicg_item['presence_penalty'] : $this->wpaicg_presence_penalty;
                        $wpaicg_stop = isset($wpaicg_item['stop']) && !empty($wpaicg_item['stop']) ? $wpaicg_item['stop'] : $this->wpaicg_stop;
                        $wpaicg_stop_lists = '';
                        if(is_array($wpaicg_stop) && count($wpaicg_stop)){
                            foreach($wpaicg_stop as $item_stop){
                                if($item_stop === "\n"){
                                    $item_stop = '\n';
                                }
                                $wpaicg_stop_lists = empty($wpaicg_stop_lists) ? $item_stop : ','.$item_stop;
                            }
                        }
                        elseif(is_array($wpaicg_stop) && !count($wpaicg_stop)){
                            $wpaicg_stop_lists = '';
                        }
                        else{
                            $wpaicg_stop_lists = $wpaicg_stop;
                        }
                        if(count($wpaicg_item_categories)){
                            foreach($wpaicg_item_categories as $wpaicg_item_category){
                                if(isset($wpaicg_categories[$wpaicg_item_category]) && !empty($wpaicg_categories[$wpaicg_item_category])){
                                    $wpaicg_item_categories_name[] = $wpaicg_categories[$wpaicg_item_category];
                                }
                            }
                        }
                        ?>
                        <div
                            id="wpaicg-prompt-item-<?php echo esc_html($wpaicg_item['id'])?>"
                            data-title="<?php echo esc_html($wpaicg_item['title'])?>"
                            data-type="<?php echo esc_html($wpaicg_item['type'])?>"
                            data-id="<?php echo esc_html($wpaicg_item['id'])?>"
                            data-post_title="<?php echo esc_html($wpaicg_item['title'])?>"
                            data-desc="<?php echo esc_html(@$wpaicg_item['description'])?>"
                            data-description="<?php echo esc_html(@$wpaicg_item['description'])?>"
                            data-icon="<?php echo esc_html(@$wpaicg_item['icon'])?>"
                            data-color="<?php echo esc_html($wpaicg_icon_color)?>"
                            data-engine="<?php echo esc_html($wpaicg_engine)?>"
                            data-max_tokens="<?php echo esc_html($wpaicg_max_tokens)?>"
                            data-temperature="<?php echo esc_html($wpaicg_temperature)?>"
                            data-top_p="<?php echo esc_html($wpaicg_top_p)?>"
                            data-best_of="<?php echo esc_html($wpaicg_best_of)?>"
                            data-frequency_penalty="<?php echo esc_html($wpaicg_frequency_penalty)?>"
                            data-presence_penalty="<?php echo esc_html($wpaicg_presence_penalty)?>"
                            data-stop="<?php echo esc_html($wpaicg_stop_lists)?>"
                            data-categories="<?php echo esc_html(implode(', ',$wpaicg_item_categories_name))?>"
                            data-category="<?php echo esc_html($wpaicg_item['category'])?>"
                            data-prompt="<?php echo esc_html(@$wpaicg_item['prompt']);?>"
                            data-estimated="<?php echo isset($wpaicg_item['estimated']) ? esc_html($wpaicg_item['estimated']) : '';?>"
                            data-editor="<?php echo isset($wpaicg_item['editor']) && $wpaicg_item['editor'] == 'div' ? 'div' : 'textarea'?>"
                            data-response="<?php echo esc_html(@$wpaicg_item['response']);?>"
                            data-header="<?php echo isset($wpaicg_item['header']) ? esc_html($wpaicg_item['header']) : '';?>"
                            data-bgcolor="<?php echo isset($wpaicg_item['bgcolor']) ? esc_html($wpaicg_item['bgcolor']) : '';?>"
                            data-dans="<?php echo isset($wpaicg_item['dans']) ? esc_html($wpaicg_item['dans']) : '';?>"
                            data-ddraft="<?php echo isset($wpaicg_item['ddraft']) ? esc_html($wpaicg_item['ddraft']) : '';?>"
                            data-dclear="<?php echo isset($wpaicg_item['dclear']) ? esc_html($wpaicg_item['dclear']) : '';?>"
                            data-dnotice="<?php echo isset($wpaicg_item['dnotice']) ? esc_html($wpaicg_item['dnotice']) : '';?>"
                            data-generate_text="<?php echo isset($wpaicg_item['generate_text']) && !empty($wpaicg_item['generate_text']) ? esc_html($wpaicg_item['generate_text']) : esc_html__('Generate','gpt3-ai-content-generator');?>"
                            data-noanswer_text="<?php echo isset($wpaicg_item['noanswer_text']) && !empty($wpaicg_item['noanswer_text']) ? esc_html($wpaicg_item['noanswer_text']) : esc_html__('Number of Answers','gpt3-ai-content-generator');?>"
                            data-draft_text="<?php echo isset($wpaicg_item['draft_text']) && !empty($wpaicg_item['draft_text']) ? esc_html($wpaicg_item['draft_text']) : esc_html__('Save Draft','gpt3-ai-content-generator');?>"
                            data-clear_text="<?php echo isset($wpaicg_item['clear_text']) && !empty($wpaicg_item['clear_text']) ? esc_html($wpaicg_item['clear_text']) : esc_html__('Clear','gpt3-ai-content-generator');?>"
                            data-stop_text="<?php echo isset($wpaicg_item['stop_text']) && !empty($wpaicg_item['stop_text']) ? esc_html($wpaicg_item['stop_text']) : esc_html__('Stop','gpt3-ai-content-generator');?>"
                            data-cnotice_text="<?php echo isset($wpaicg_item['cnotice_text']) && !empty($wpaicg_item['cnotice_text']) ? esc_html($wpaicg_item['cnotice_text']) : esc_html__('Please register to save your result','gpt3-ai-content-generator');?>"
                            data-ddownload="<?php echo isset($wpaicg_item['ddownload']) ? esc_html($wpaicg_item['ddownload']) : '';?>"
                            data-download_text="<?php echo isset($wpaicg_item['download_text']) && !empty($wpaicg_item['download_text']) ? esc_html($wpaicg_item['download_text']) : esc_html__('Download','gpt3-ai-content-generator');?>"
                            class="wpaicg-prompt-item wpaicg-d-flex wpaicg-align-items-center <?php echo implode(' ',$wpaicg_item_categories)?><?php echo ' user-'.esc_html($wpaicg_item['author'])?><?php echo ' wpaicg-prompt-item-'.esc_html($wpaicg_item['type']).'-'.esc_html($wpaicg_item['id']);?>">
                            <div class="wpaicg-prompt-icon" style="background: <?php echo esc_html($wpaicg_icon_color)?>"><?php echo wp_kses($wpaicg_icon,$allowed_tags)?></div>
                            <div class="wpaicg-prompt-content">
                                <strong><?php echo isset($wpaicg_item['title']) && !empty($wpaicg_item['title']) ? esc_html($wpaicg_item['title']) : ''?></strong>
                                <?php
                                if(isset($wpaicg_item['description']) && !empty($wpaicg_item['description'])){
                                    echo '<p>'.esc_html($wpaicg_item['description']).'</p>';
                                }
                                ?>
                            </div>
                            <?php
                            if($wpaicg_item['type'] == 'custom'):
                                ?>
                                <div class="wpaicg-prompt-action">
                                    <button class="button button-small wpaicg-prompt-action-duplicate" data-id="<?php echo esc_html($wpaicg_item['id'])?>"><?php echo esc_html__('Duplicate','gpt3-ai-content-generator')?></button>
                                    <button class="button button-small wpaicg-prompt-action-edit" data-id="<?php echo esc_html($wpaicg_item['id'])?>"><?php echo esc_html__('Edit','gpt3-ai-content-generator')?></button>
                                    <button class="button button-small wpaicg-prompt-action-delete" data-id="<?php echo esc_html($wpaicg_item['id'])?>"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></button>
                                </div>
                            <?php
                            endif;
                            ?>
                        </div>
                    <?php
                    endforeach;
                endif;
                ?>
            </div>
            <div class="wpaicg-paginate"></div>
        </div>
    </div>
</div>
<div class="wpaicg-prompt-modal-content" style="display: none">
    <form method="post" action="">
        <div class="wpaicg-grid-three">
            <div class="wpaicg-grid-2">
                <input type="hidden" class="wpaicg-prompt-response-type" value="textarea">
                <strong><?php echo esc_html__('Prompt','gpt3-ai-content-generator')?></strong>
                <div class="wpaicg-mb-10">
                    <textarea name="title" class="wpaicg-prompt-title" rows="8"></textarea>
                    <strong class="wpaicg-prompt-text-noanswer_text"><?php echo esc_html__('Number of Answers','gpt3-ai-content-generator')?></strong>
                    <select class="wpaicg-prompt-max-lines">
                        <?php
                        for($i=1;$i<=10;$i++){
                            echo '<option value="'.$i.'">'.$i.'</option>';
                        }
                        ?>
                    </select>
                    <button class="button button-primary wpaicg-generate-button wpaicg-prompt-text-generate_text"><?php echo esc_html__('Generate','gpt3-ai-content-generator')?></button>
                    &nbsp;<button type="button" class="button button-primary wpaicg-prompt-stop-generate wpaicg-prompt-text-stop_text" style="display: none"><?php echo esc_html__('Stop','gpt3-ai-content-generator')?></button>
                </div>
                <div class="mb-5">
                    <div class="wpaicg-prompt-response-editor">
                        <textarea class="wpaicg-prompt-result" rows="12"></textarea>
                    </div>
                    <div class="wpaicg-prompt-response-element"></div>
                </div>
                <div class="wpaicg-prompt-save-result" style="display: none">
                    <button type="button" class="button button-primary wpaicg-prompt-save-draft wpaicg-prompt-text-draft_text"><?php echo esc_html__('Save Draft','gpt3-ai-content-generator')?></button>
                    <button type="button" class="button wpaicg-prompt-clear wpaicg-prompt-text-clear_text"><?php echo esc_html__('Clear','gpt3-ai-content-generator')?></button>
                    <button type="button" class="button wpaicg-prompt-download wpaicg-prompt-text-download_text"><?php echo esc_html__('Download','gpt3-ai-content-generator')?></button>
                </div>
            </div>
            <div class="wpaicg-grid-1">
                <div class="wpaicg-mb-10 wpaicg-prompt-settings">
                    <button type="button" style="width: 100%" class="button button-primary wpaicg-prompt-action-customize" data-id=""><?php echo esc_html__('Duplicate This Form','gpt3-ai-content-generator')?></button>
                    <h3><?php echo esc_html__('Settings','gpt3-ai-content-generator')?></h3>
                    <div class="mb-5 wpaicg-prompt-engine">
                        <strong><?php echo esc_html__('Engine','gpt3-ai-content-generator')?>: </strong>
                        <select name="engine">
                            <option value="gpt-3.5-turbo">gpt-3.5-turbo</option>
                            <?php
                            foreach($wpaicg_models as $wpaicg_model){
                                if($wpaicg_model == 'text-davinci-003') {
                                    echo '<option value="' . esc_html($wpaicg_model) . '">' . esc_html($wpaicg_model) . '</option>';
                                }
                            }
                            ?>
                            <option value="text-curie-001">text-curie-001</option>
                            <option value="text-babbage-001">text-babbage-001</option>
                            <option value="text-ada-001">text-ada-001</option>
                            <option value="gpt-4">gpt-4</option>
                            <option value="gpt-4-32k">gpt-4-32k</option>
                        </select>
                    </div>
                    <div class="mb-5 wpaicg-prompt-max_tokens"><strong><?php echo esc_html__('Max Tokens','gpt3-ai-content-generator')?>: </strong><input name="max_tokens" type="text" min="1" max="2048"></div>
                    <div class="mb-5 wpaicg-prompt-temperature"><strong><?php echo esc_html__('Temperature','gpt3-ai-content-generator')?>: </strong><input name="temperature" type="text" min="0" max="1" step="any"></div>
                    <div class="mb-5 wpaicg-prompt-top_p"><strong><?php echo esc_html__('Top P','gpt3-ai-content-generator')?>: </strong><input name="top_p" type="text" min="0" max="1"></div>
                    <div class="mb-5 wpaicg-prompt-best_of"><strong><?php echo esc_html__('Best Of','gpt3-ai-content-generator')?>: </strong><input name="best_of" type="text" min="1" max="20"></div>
                    <div class="mb-5 wpaicg-prompt-frequency_penalty"><strong><?php echo esc_html__('Frequency Penalty','gpt3-ai-content-generator')?>: </strong><input name="frequency_penalty" type="text" min="0" max="2" step="any"></div>
                    <div class="mb-5 wpaicg-prompt-presence_penalty"><strong><?php echo esc_html__('Presence Penalty','gpt3-ai-content-generator')?>: </strong><input name="presence_penalty" type="text" min="0" max="2" step="any"></div>
                    <div class="mb-5 wpaicg-prompt-stop"><strong><?php echo esc_html__('Stop','gpt3-ai-content-generator')?>:<small><?php echo esc_html__('separate by commas','gpt3-ai-content-generator')?></small></strong><input name="stop" type="text"></div>
                    <div class="mb-5 wpaicg-prompt-estimated"><strong><?php echo esc_html__('Estimated','gpt3-ai-content-generator')?>: </strong><span></span></div>
                    <div class="mb-5 wpaicg-prompt-post_title"><input type="hidden" name="post_title"></div>
                    <div class="mb-5 wpaicg-prompt-sample"><?php echo esc_html__('Sample Response','gpt3-ai-content-generator')?><div class="wpaicg-prompt-response"></div></div>
                    <div style="padding: 5px;background: #ffc74a;border-radius: 4px;color: #000;" class="wpaicg-prompt-shortcode"></div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    jQuery(document).ready(function ($){
        let prompt_id;
        let prompt_name;
        let prompt_response = '';
        let wpaicg_limited_token = false;
        let wp_nonce = '<?php echo esc_html(wp_create_nonce( 'wpaicg-promptbase' ))?>'
        /*Modal tab*/
        $(document).on('click','.wpaicg-modal-tabs li', function (e){
            var tab = $(e.currentTarget);
            var target =  tab.attr('data-target');
            var modal = tab.closest('.wpaicg_modal_content');
            modal.find('.wpaicg-modal-tabs li').removeClass('wpaicg-active');
            tab.addClass('wpaicg-active');
            modal.find('.wpaicg-modal-tab').hide();
            modal.find('.wpaicg-modal-tab-'+target).show();
        })
        /*Create Prompt*/
        var wpaicgPromptContent = $('.wpaicg-create-prompt-content');
        $(document).on('click','.wpaicg-prompt-icons span', function (e){
            var icon = $(e.currentTarget);
            icon.parent().find('span').removeClass('icon_selected');
            icon.addClass('icon_selected');
            icon.parent().parent().find('.wpaicg-create-prompt-icon').val(icon.attr('data-key'));
        });
        $('.wpaicg-create-prompt').click(function (){
            $('.wpaicg_modal_content').empty();
            $('.wpaicg_modal_title').html('<?php echo esc_html__('Design Your Prompt','gpt3-ai-content-generator')?>');
            $('.wpaicg_modal_content').html('<form action="" method="post" class="wpaicg-create-prompt-form">'+wpaicgPromptContent.html()+'</form>');
            $('.wpaicg-create-prompt-form .wpaicg-create-prompt-color').wpColorPicker();
            $('.wpaicg-create-prompt-form .wpaicg-create-prompt-bgcolor').wpColorPicker();
            $('.wpaicg-create-prompt-form .wpaicg-create-prompt-category').val('generation');
            $('.wpaicg-create-prompt-form .wpaicg-prompt-icons span[data-key=robot]').addClass('icon_selected');
            $('.wpaicg_modal').css('height','auto');
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
        })
        $(document).on('click','.wpaicg-prompt-item .wpaicg-prompt-action-delete',function (e){
            var id = $(e.currentTarget).attr('data-id');
            var conf = confirm('<?php echo esc_html__('Are you sure?','gpt3-ai-content-generator')?>');
            if(conf){
                $('.wpaicg-prompt-item-custom-'+id).remove();
                $.post('<?php echo admin_url('admin-ajax.php')?>', {action: 'wpaicg_prompt_delete', id: id,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'});
            }
        });
        $(document).on('click','.wpaicg-prompt-item .wpaicg-prompt-action-duplicate',function (e){
            var id = $(e.currentTarget).attr('data-id');
            var btn = $(e.currentTarget);
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: {action: 'wpaicg_prompt_duplicate',id: id,nonce:'<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                dataType: 'JSON',
                type:'POST',
                beforeSend: function(){
                    wpaicgLoading(btn);
                },
                success: function(res){
                    window.location.reload();
                }
            });
        });
        $(document).on('click','.wpaicg-prompt-item .wpaicg-prompt-action-edit',function (e){
            var id = $(e.currentTarget).attr('data-id');
            var item = $('.wpaicg-prompt-item-custom-'+id);
            $('.wpaicg_modal_content').empty();
            $('.wpaicg_modal_title').html('<?php echo esc_html__('Edit your Prompt','gpt3-ai-content-generator')?>');
            $('.wpaicg_modal_content').html('<form action="" method="post" class="wpaicg-create-prompt-form">'+wpaicgPromptContent.html()+'</form>');
            var form = $('.wpaicg-create-prompt-form');
            var wpaicg_prompt_keys = ['engine','editor','title','description','max_tokens','temperature','top_p','best_of','frequency_penalty','presence_penalty','stop','prompt','response','category','icon','color','bgcolor','header','dans','ddraft','dclear','dnotice','generate_text','noanswer_text','draft_text','clear_text','stop_text','cnotice_text','download_text','ddownload'];
            for(var i = 0; i < wpaicg_prompt_keys.length;i++){
                var wpaicg_prompt_key = wpaicg_prompt_keys[i];
                var wpaicg_prompt_key_value = item.attr('data-'+wpaicg_prompt_key);
                form.find('.wpaicg-create-prompt-'+wpaicg_prompt_key).val(wpaicg_prompt_key_value);
                if(wpaicg_prompt_key === 'icon'){
                    $('.wpaicg-create-prompt-form .wpaicg-prompt-icons span[data-key='+wpaicg_prompt_key_value+']').addClass('icon_selected');
                }
            }
            form.find('.wpaicg-create-prompt-id').val(id);
            $('.wpaicg-create-prompt-form .wpaicg-create-prompt-color').wpColorPicker();
            $('.wpaicg-create-prompt-form .wpaicg-create-prompt-bgcolor').wpColorPicker();
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').css('height','auto');
            $('.wpaicg_modal').show();
        });
        $(document).on('click','.wpaicg-prompt-action-customize',function (e){
            var id = $(e.currentTarget).attr('data-id');
            var item = $('.wpaicg-prompt-item-json-'+id);
            $('.wpaicg_modal_content').empty();
            $('.wpaicg_modal_title').html('<?php echo esc_html__('Customize your Prompt','gpt3-ai-content-generator')?>');
            $('.wpaicg_modal_content').html('<form action="" method="post" class="wpaicg-create-prompt-form">'+wpaicgPromptContent.html()+'</form>');
            var form = $('.wpaicg-create-prompt-form');
            var wpaicg_prompt_keys = ['engine','editor','title','description','max_tokens','temperature','top_p','best_of','frequency_penalty','presence_penalty','stop','prompt','response','category','icon','color','bgcolor','header','dans','ddraft','dclear','dnotice','generate_text','noanswer_text','draft_text','clear_text','stop_text','cnotice_text','download_text','ddownload'];
            for(var i = 0; i < wpaicg_prompt_keys.length;i++){
                var wpaicg_prompt_key = wpaicg_prompt_keys[i];
                var wpaicg_prompt_key_value = item.attr('data-'+wpaicg_prompt_key);
                if(wpaicg_prompt_key === 'category' && wpaicg_prompt_key_value !== ''){
                    if(wpaicg_prompt_key_value.indexOf(',') > -1){
                        wpaicg_prompt_key_value = wpaicg_prompt_key_value.split(',')[0];
                    }
                }
                form.find('.wpaicg-create-prompt-'+wpaicg_prompt_key).val(wpaicg_prompt_key_value);
                if(wpaicg_prompt_key === 'icon'){
                    $('.wpaicg-create-prompt-form .wpaicg-prompt-icons span[data-key='+wpaicg_prompt_key_value+']').addClass('icon_selected');
                }
            }
            $('.wpaicg-create-prompt-form .wpaicg-create-prompt-color').wpColorPicker();
            $('.wpaicg-create-prompt-form .wpaicg-create-prompt-bgcolor').wpColorPicker();
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').css('height','auto');
            $('.wpaicg_modal').show();
        });
        $(document).on('submit','.wpaicg-create-prompt-form', function(e){
            var form = $(e.currentTarget);
            var btn = form.find('.wpaicg-create-prompt-save');
            var max_tokens = form.find('.wpaicg-create-prompt-make-tokens').val();
            var temperature = form.find('.wpaicg-create-prompt-temperature').val();
            var top_p = form.find('.wpaicg-create-prompt-top_p').val();
            var best_of = form.find('.wpaicg-create-prompt-best_of').val();
            var frequency_penalty = form.find('.wpaicg-create-prompt-frequency_penalty').val();
            var presence_penalty = form.find('.wpaicg-create-prompt-presence_penalty').val();
            var error_message = false;
            var data = form.serialize();
            if(max_tokens !== '' && (parseFloat(max_tokens) < 1 || parseFloat(max_tokens) > 8000)){
                error_message = '<?php echo sprintf(esc_html__('Please enter a valid max token value between %d and %d.','gpt3-ai-content-generator'),1,8000)?>';
            }
            else if(temperature !== '' && (parseFloat(temperature) < 0 || parseFloat(temperature) > 1)){
                error_message = '<?php echo sprintf(esc_html__('Please enter a valid temperature value between %d and %d.','gpt3-ai-content-generator'),0,1)?>';
            }
            else if(top_p !== '' && (parseFloat(top_p) < 0 || parseFloat(top_p) > 1)){
                error_message = '<?php echo sprintf(esc_html__('Please enter a valid top p value between %d and %d.','gpt3-ai-content-generator'),0,1)?>';
            }
            else if(best_of !== '' && (parseFloat(best_of) < 1 || parseFloat(best_of) > 20)){
                error_message = '<?php echo sprintf(esc_html__('Please enter a valid best of value between %d and %d.','gpt3-ai-content-generator'),1,20)?>';
            }
            else if(frequency_penalty !== '' && (parseFloat(frequency_penalty) < 0 || parseFloat(frequency_penalty) > 2)){
                error_message = '<?php echo sprintf(esc_html__('Please enter a valid frequency penalty value between %d and %d.','gpt3-ai-content-generator'),0,2)?>';
            }
            else if(presence_penalty !== '' && (parseFloat(presence_penalty) < 0 || parseFloat(presence_penalty) > 2)){
                error_message = '<?php echo sprintf(esc_html__('Please enter a valid presence penalty value between %d and %d.','gpt3-ai-content-generator'),0,2)?>';
            }
            if(error_message){
                alert(error_message);
            }
            else{
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: data,
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function (){
                        wpaicgLoading(btn)
                    },
                    success: function (res){
                        wpaicgRmLoading(btn);
                        if(res.status === 'success'){
                            window.location.href = '<?php echo admin_url('admin.php?page=wpaicg_promptbase&update_prompt=')?>'+res.id;
                        }
                        else{
                            alert(res.msg)
                        }
                    },
                    error: function (){
                        wpaicgRmLoading(btn);
                        alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                    }
                })
            }
            return false;
        })
        /*End create*/
        var wpaicgNumberParse = 3;
        if($(window).width() < 900){
            wpaicgNumberParse = 2;
        }
        if($(window).width() < 480){
            wpaicgNumberParse = 1;
        }
        var wpaicg_per_page = <?php echo esc_html($wpaicg_per_page);?>;
        var wpaicg_count_prompts = <?php echo esc_html(count($wpaicg_items))?>;
        $('.wpaicg-list input').on('change',function (){
            wpaicgPromptsFilter();
        });
        var wpaicgPromptItem = $('.wpaicg-prompt-item');
        var wpaicgPromptSearch = $('.wpaicg-prompt-search');
        var wpaicgPromptItems = $('.wpaicg-prompt-items');
        var wpaicgPromptSettings = ['engine','max_tokens','temperature','top_p','best_of','frequency_penalty','presence_penalty','stop','post_title','generate_text','noanswer_text','draft_text','clear_text','stop_text','cnotice_text','download_text'];
        var wpaicgPromptDefaultContent = $('.wpaicg-prompt-modal-content');
        var wpaicgPromptEditor = false;
        var eventGenerator = false;
        wpaicgPromptSearch.on('input', function (){
            wpaicgPromptsFilter();
        });
        function wpaicgPromptsFilter(){
            var categories = [];
            var users = [];
            var filterClasses = [];
            $('.wpaicg-categories input').each(function (idx, item){
                if($(item).prop('checked')){
                    categories.push($(item).val());
                    filterClasses.push($(item).val());
                }
            });
            $('.wpaicg-authors input').each(function (idx, item){
                if($(item).prop('checked')){
                    users.push('user-'+$(item).val());
                    filterClasses.push('user-'+$(item).val());
                }
            });
            var search = wpaicgPromptSearch.val();
            wpaicgPromptItem.each(function (idx, item){
                var item_title = $(item).attr('data-title');
                var item_desc = $(item).attr('data-desc');
                var show = false;
                if(categories.length){
                    for(var i=0;i<categories.length;i++){
                        if($(item).hasClass(categories[i])){
                            show = true;
                            break;
                        }
                        else{
                            show = false;
                        }
                    }
                    if(show && users.length){
                        for(var i=0;i<users.length;i++){
                            if($(item).hasClass(users[i])){
                                show = true;
                                break;
                            }
                            else{
                                show = false;
                            }
                        }
                    }
                }
                if(users.length){
                    for(var i=0;i<users.length;i++){
                        if($(item).hasClass(users[i])){
                            show = true;
                            break;
                        }
                        else{
                            show = false;
                        }
                    }
                    if(show && categories.length){
                        for(var i=0;i<categories.length;i++){
                            if($(item).hasClass(categories[i])){
                                show = true;
                                break;
                            }
                            else{
                                show = false;
                            }
                        }
                    }
                }
                if(!users.length && !categories.length){
                    show = true;
                }
                if(search !== '' && show){
                    search = search.toLowerCase();
                    item_title = item_title.toLowerCase();
                    item_desc = item_desc.toLowerCase();
                    if(item_title.indexOf(search) === -1 && item_desc.indexOf(search) === -1){
                        show = false;
                    }
                }
                if(show){
                    $(item).show();
                }
                else{
                    $(item).hide();
                }
            });
            wpaicgPromptPagination();
        }
        wpaicgPromptPagination();
        function wpaicgPromptPagination(){
            wpaicgPromptItem.removeClass('disappear-item');
            var number_rows = 0 ;
            wpaicgPromptItem.each(function (idx, item){
                if($(item).is(':visible')){
                    number_rows++;
                }
            });
            $('.wpaicg-paginate').empty();
            if(number_rows > wpaicg_per_page){
                var  totalPage = Math.ceil(number_rows/wpaicg_per_page);
                for(var i=1;i <=totalPage;i++){
                    var classSpan = 'page-numbers';
                    if(i === 1){
                        classSpan = 'page-numbers current';
                    }
                    $('.wpaicg-paginate').append('<span class="'+classSpan+'" data-page="'+i+'">'+i+'</span>');
                }
            }
            var rowDisplay = 0;
            wpaicgPromptItem.each(function (idx, item){
                if($(item).is(':visible')){
                    rowDisplay += 1;
                }
            });
            if(rowDisplay > wpaicg_per_page) {
                wpaicgPromptItems.css('height', ((Math.ceil(wpaicg_per_page/wpaicgNumberParse) * 120) - 20) + 'px');
            }
            else{
                wpaicgPromptItems.css('height', ((Math.ceil(rowDisplay/wpaicgNumberParse) * 120) - 20) + 'px');
            }
        }

        $(document).on('click','.wpaicg-paginate span:not(.current)', function (e){
            var btn = $(e.currentTarget);
            var page = parseInt(btn.attr('data-page'));
            $('.wpaicg-paginate span').removeClass('current');
            btn.addClass('current');
            var prevpage = page-1;
            var startRow = prevpage*wpaicg_per_page;
            var endRow = startRow+wpaicg_per_page;
            var keyRow = 0;
            var rowDisplay = 0;
            wpaicgPromptItem.each(function (idx, item){
                if($(item).is(':visible')){
                    keyRow += 1;
                    if(keyRow > startRow && keyRow <= endRow){
                        rowDisplay += 1;
                        $(item).removeClass('disappear-item');
                    }
                    else{
                        $(item).addClass('disappear-item');
                    }
                }
            });
            wpaicgPromptItems.css('height',((Math.ceil(rowDisplay/wpaicgNumberParse)*120)- 20)+'px');
        });
        $('.wpaicg_modal_close').click(function (){
            $('.wpaicg_modal_close').closest('.wpaicg_modal').hide();
            $('.wpaicg_modal_close').closest('.wpaicg_modal').removeClass('wpaicg-small-modal');
            $('.wpaicg-overlay').hide();
            if(eventGenerator){
                eventGenerator.close();
            }
        });
        var wpaicgEditorNumber;
        $(document).on('click','.wpaicg-prompt-form .wpaicg-prompt-save-draft', function(e){
            var basicEditor = true;
            var btn = $(e.currentTarget);
            var response_type = $('.wpaicg-prompt-form .wpaicg-prompt-response-type').val();
            var post_content = '';
            if(response_type === 'textarea') {
                var editor = tinyMCE.get('editor-' + wpaicgEditorNumber);
                if ($('#wp-editor-' + wpaicgEditorNumber + '-wrap').hasClass('tmce-active') && editor) {
                    basicEditor = false;
                }
                if (basicEditor) {
                    post_content = $('#editor-' + wpaicgEditorNumber).val();
                } else {
                    post_content = editor.getContent();
                }
            }
            else{
                post_content = $('.wpaicg-prompt-response-element').html();
            }
            var post_title = $('.wpaicg-prompt-form .wpaicg-prompt-post_title input').val();
            if(post_content !== ''){
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {title: post_title, content: post_content, action: 'wpaicg_save_draft_post_extra',save_source: 'promptbase','nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (){
                        wpaicgLoading(btn);
                    },
                    success: function (res){
                        wpaicgRmLoading(btn);
                        if(res.status === 'success'){
                            window.location.href = '<?php echo admin_url('post.php')?>?post='+res.id+'&action=edit';
                        }
                        else{
                            alert(res.msg);
                        }
                    },
                    error: function (){
                        wpaicgRmLoading(btn);
                        alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                    }
                });
            }
            else{
                alert('<?php echo esc_html__('Please wait content generated','gpt3-ai-content-generator')?>');
            }

        });
        $(document).on('click','.wpaicg-prompt-form .wpaicg-prompt-clear', function(e){
            var basicEditor = true;
            var response_type = $('.wpaicg-prompt-form .wpaicg-prompt-response-type').val();
            if(response_type === 'textarea') {
                var editor = tinyMCE.get('editor-' + wpaicgEditorNumber);
                if ($('#wp-editor-' + wpaicgEditorNumber + '-wrap').hasClass('tmce-active') && editor) {
                    basicEditor = false;
                }
                if (basicEditor) {
                    $('#editor-' + wpaicgEditorNumber).val('');
                } else {
                    editor.setContent('');
                }
            }
            else{
                $('.wpaicg-prompt-response-element').empty();
            }
        });
        $(document).on('click','.wpaicg-prompt-download', function(e){
            var currentContent = '';
            var response_type = $('.wpaicg-prompt-form .wpaicg-prompt-response-type').val();
            if(response_type === 'textarea') {
                var basicEditor = true;
                var editor = tinyMCE.get('editor-' + wpaicgEditorNumber);
                if ($('#wp-editor-' + wpaicgEditorNumber + '-wrap').hasClass('tmce-active') && editor) {
                    basicEditor = false;
                }
                if (basicEditor) {
                    currentContent = $('#editor-' + wpaicgEditorNumber).val();
                } else {
                    currentContent = editor.getContent();
                    currentContent = currentContent.replace(/<\/?p(>|$)/g, "");
                }
            }
            else{
                currentContent = $('.wpaicg-prompt-response-element').html();
            }
            var element = document.createElement('a');
            currentContent = currentContent.replace(/<br>/g,"\n");
            currentContent = currentContent.replace(/<br \/>/g,"\n");
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(currentContent));
            element.setAttribute('download', 'ai-response.txt');

            element.style.display = 'none';
            document.body.appendChild(element);

            element.click();

            document.body.removeChild(element);
        });
        $(document).on('input','.wpaicg-prompt-form .wpaicg-prompt-max_tokens input', function(e){
            var maxtokens = $(e.currentTarget).val();
            var wpaicg_estimated_cost = maxtokens !== '' ? parseFloat(maxtokens)*0.02/1000 : 0;
            wpaicg_estimated_cost = '$'+parseFloat(wpaicg_estimated_cost.toFixed(5));
            $('.wpaicg-prompt-form .wpaicg-prompt-estimated span').html(wpaicg_estimated_cost);
        });
        $(document).on('click','.wpaicg-prompt-item .wpaicg-prompt-content,.wpaicg-prompt-item .wpaicg-prompt-icon',function (e){
            var item = $(e.currentTarget).parent();
            var title = item.attr('data-title');
            var id = item.attr('data-id');
            var type = item.attr('data-type');
            var response_type = item.attr('data-editor');
            prompt_name = title;
            prompt_id = id;
            $('.wpaicg-prompt-response-type').val(response_type);
            var categories = item.attr('data-categories');
            $('.wpaicg_modal_content').empty();
            if(type === 'json') {
                $('.wpaicg-prompt-action-customize').attr('data-id', id);
            }
            else{
                $('.wpaicg-prompt-action-customize').hide();
            }
            var modal_head = '<div class="wpaicg-d-flex wpaicg-align-items-center wpaicg-modal-prompt-head"><div style="margin-left: 10px;">';
            modal_head += '<strong>'+title+'</strong>';
            if(categories !== ''){
                modal_head += '<div><small>'+categories+'</small></div>';
            }
            modal_head += '</div></div>';
            $('.wpaicg_modal_title').html(modal_head);
            $('.wpaicg-modal-prompt-head').prepend(item.find('.wpaicg-prompt-icon').clone());
            var prompt = item.attr('data-prompt');
            if(type === 'custom'){
                prompt += ".\n\n";
            }
            var response = item.attr('data-response');
            wpaicgEditorNumber = Math.ceil(Math.random()*1000000);
            $('.wpaicg_modal_content').html('<div class="wpaicg-prompt-form">'+wpaicgPromptDefaultContent.html()+'</div>');
            $('.wpaicg-prompt-form').find('.wpaicg-prompt-title').val(prompt);
            wpaicgPromptEditor = $('.wpaicg-prompt-form').find('.wpaicg-prompt-result');
            if(id !== undefined){
                var embed_message = '<?php echo esc_html__('Embed this form to your website','gpt3-ai-content-generator')?>: [wpaicg_prompt id='+id+' settings=no';
                if(type === 'custom'){
                    embed_message += ' custom=yes';
                }
                embed_message += ']';
                $('.wpaicg-prompt-form .wpaicg-prompt-shortcode').html(embed_message);
            }
            for(var i = 0; i < wpaicgPromptSettings.length; i++){
                var item_name = wpaicgPromptSettings[i];
                var item_value = item.attr('data-'+item_name);
                if(item_name === 'max_tokens'){
                    var wpaicg_estimated_cost = item_value !== undefined ? parseFloat(item_value)*0.02/1000 : 0;
                    wpaicg_estimated_cost = '$'+parseFloat(wpaicg_estimated_cost.toFixed(5));
                    $('.wpaicg-prompt-form .wpaicg-prompt-estimated span').html(wpaicg_estimated_cost);
                }
                if(item_value !== undefined){
                    if(
                        item_name === 'generate_text'
                        || item_name === 'draft_text'
                        || item_name === 'noanswer_text'
                        || item_name === 'clear_text'
                        || item_name === 'stop_text'
                    ){
                        $('.wpaicg-prompt-text-'+item_name).html(item_value);
                    }
                    else{
                        if(item_name !== 'engine' && item_name !== 'stop' && item_name !== 'post_title'){
                            item_value = parseFloat(item_value);
                            item_value = item_value.toString().replace(/,/g, '.');
                        }
                        $('.wpaicg-prompt-form .wpaicg-prompt-'+item_name).find('[name='+item_name+']').val(item_value);
                        $('.wpaicg-prompt-form .wpaicg-prompt-'+item_name).show();
                    }
                }
                else{
                    $('.wpaicg-prompt-form .wpaicg-prompt-'+item_name).hide();
                }
            }
            $('.wpaicg-prompt-form .wpaicg-prompt-response').html(response);
            wpaicgPromptEditor.attr('id','editor-'+wpaicgEditorNumber);
            if(response_type === 'textarea') {
                wp.editor.initialize('editor-' + wpaicgEditorNumber, {
                    tinymce: {
                        wpautop: false,
                        plugins: 'charmap colorpicker hr lists paste tabfocus textcolor fullscreen wordpress wpautoresize wpeditimage wpemoji wpgallery wplink wptextpattern',
                        toolbar1: 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,fullscreen,wp_adv,listbuttons',
                        toolbar2: 'styleselect,strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
                        height: 300
                    },
                    quicktags: {buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close'},
                    mediaButtons: true
                });
            }
            else{
                $('.wpaicg-template-form .wpaicg-prompt-response-editor').hide();
            }
            $('.wpaicg_modal').css('top','');
            $('.wpaicg_modal').css('height','');
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
        });
        function wpaicgLoading(btn){
            btn.attr('disabled','disabled');
            if(!btn.find('spinner').length){
                btn.append('<span class="spinner"></span>');
            }
            btn.find('.spinner').css('visibility','unset');
        }
        function wpaicgRmLoading(btn){
            btn.removeAttr('disabled');
            btn.find('.spinner').remove();
        }
        function stopOpenAIGenerator(){
            $('.wpaicg-prompt-form .wpaicg-prompt-stop-generate').hide();
            if(!wpaicg_limited_token){
                $('.wpaicg-prompt-form .wpaicg-prompt-save-result').show();
            }
            wpaicgRmLoading($('.wpaicg-prompt-form .wpaicg-generate-button'));
            eventGenerator.close();
        }
        $(document).on('click','.wpaicg-prompt-form .wpaicg-prompt-stop-generate', function (e){
            stopOpenAIGenerator();
        });
        $(document).on('submit','.wpaicg-prompt-form form', function (e){
            var form = $(e.currentTarget);
            var btn = form.find('.wpaicg-generate-button');
            var prompt_title = form.find('.wpaicg-prompt-title').val();
            var response_type = form.find('.wpaicg-prompt-response-type').val();
            if(prompt_title !== '') {
                var max_tokens = form.find('.wpaicg-prompt-max_tokens input').val();
                var temperature = form.find('.wpaicg-prompt-temperature input').val();
                var top_p = form.find('.wpaicg-prompt-top_p input').val();
                var best_of = form.find('.wpaicg-prompt-best_of input').val();
                var frequency_penalty = form.find('.wpaicg-prompt-frequency_penalty input').val();
                var presence_penalty = form.find('.wpaicg-prompt-presence_penalty input').val();
                var error_message = false;
                if(max_tokens === ''){
                    error_message = '<?php echo esc_html__('Please enter max tokens','gpt3-ai-content-generator')?>';
                }
                else if(parseFloat(max_tokens) < 1 || parseFloat(max_tokens) > 4000){
                    error_message = '<?php echo sprintf(esc_html__('Please enter a valid max token value between %d and %d.','gpt3-ai-content-generator'),1,4000)?>';
                }
                else if(temperature === ''){
                    error_message = '<?php echo esc_html__('Please enter temperature','gpt3-ai-content-generator')?>';
                }
                else if(parseFloat(temperature) < 0 || parseFloat(temperature) > 1){
                    error_message = '<?php echo sprintf(esc_html__('Please enter a valid temperature value between %d and %d.','gpt3-ai-content-generator'),0,1)?>';
                }
                else if(top_p === ''){
                    error_message = '<?php echo esc_html__('Please enter Top P','gpt3-ai-content-generator')?>';
                }
                else if(parseFloat(top_p) < 0 || parseFloat(top_p) > 1){
                    error_message = '<?php echo sprintf(esc_html__('Please enter a valid top p value between %d and %d.','gpt3-ai-content-generator'),0,1)?>';
                }
                else if(best_of === ''){
                    error_message = '<?php echo esc_html__('Please enter best of','gpt3-ai-content-generator')?>';
                }
                else if(parseFloat(best_of) < 1 || parseFloat(best_of) > 20){
                    error_message = '<?php echo sprintf(esc_html__('Please enter a valid best of value between %d and %d.','gpt3-ai-content-generator'),1,20)?>';
                }
                else if(frequency_penalty === ''){
                    error_message = '<?php echo esc_html__('Please enter frequency penalty','gpt3-ai-content-generator')?>';
                }
                else if(parseFloat(frequency_penalty) < 0 || parseFloat(frequency_penalty) > 2){
                    error_message = '<?php echo sprintf(esc_html__('Please enter a valid frequency penalty value between %d and %d.','gpt3-ai-content-generator'),0,2)?>';
                }
                else if(presence_penalty === ''){
                    error_message = '<?php echo esc_html__('Please enter presence penalty','gpt3-ai-content-generator')?>';
                }
                else if(parseFloat(presence_penalty) < 0 || parseFloat(presence_penalty) > 2){
                    error_message = '<?php echo sprintf(esc_html__('Please enter a valid presence penalty value between %d and %d.','gpt3-ai-content-generator'),0,2)?>';
                }
                if(error_message){
                    alert(error_message);
                }
                else {
                    let startTime = new Date();
                    var data = form.serialize();
                    var basicEditor = true;
                    prompt_response = '';
                    if(response_type === 'textarea') {
                        var editor = tinyMCE.get('editor-' + wpaicgEditorNumber);
                        if ($('#wp-editor-' + wpaicgEditorNumber + '-wrap').hasClass('tmce-active') && editor) {
                            basicEditor = false;
                        }
                        if (basicEditor) {
                            $('#editor-' + wpaicgEditorNumber).val('');
                        } else {
                            editor.setContent('');
                        }
                    }
                    else{
                        $('.wpaicg-prompt-response-element').empty();
                    }
                    wpaicgLoading(btn);
                    form.find('.wpaicg-prompt-stop-generate').show();
                    form.find('.wpaicg-prompt-save-result').hide();
                    var wpaicg_limitLines = parseInt(form.find('.wpaicg-prompt-max-lines').val());
                    var count_line = 0;
                    var currentContent = '';
                    data += '&source_stream=promptbase&nonce=<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>';
                    eventGenerator = new EventSource('<?php echo esc_html(add_query_arg('wpaicg_stream','yes',site_url().'/index.php'));?>&' + data);
                    var wpaicg_response_events = 0;
                    var wpaicg_newline_before = false;
                    wpaicg_limited_token = false;
                    eventGenerator.onmessage = function (e) {
                        if(response_type === 'textarea') {
                            if (basicEditor) {
                                currentContent = $('#editor-' + wpaicgEditorNumber).val();
                            } else {
                                currentContent = editor.getContent();
                                currentContent = currentContent.replace(/<\/?p(>|$)/g, "");
                            }
                        }
                        else{
                            currentContent = $('.wpaicg-prompt-response-element').html();
                        }
                        if (e.data === "[DONE]") {
                            count_line += 1;
                            if(response_type === 'textarea') {
                                if (basicEditor) {
                                    $('#editor-' + wpaicgEditorNumber).val(currentContent + "<br /><br />");
                                } else {
                                    editor.setContent(currentContent + "<br /><br />");
                                }
                            }
                            else{
                                $('.wpaicg-prompt-response-element').append("<br>");
                            }
                            wpaicg_response_events = 0;
                        }
                        else if (e.data === "[LIMITED]") {
                            wpaicg_limited_token = true;
                            count_line += 1;
                            if(response_type === 'textarea') {
                                if (basicEditor) {
                                    $('#editor-' + wpaicgEditorNumber).val(currentContent + "<br /><br />");
                                } else {
                                    editor.setContent(currentContent + "<br /><br />");
                                }
                            }
                            else{
                                $('.wpaicg-prompt-response-element').append("<br>");
                            }
                            wpaicg_response_events = 0;
                        } else {
                            var result = JSON.parse(e.data);
                            if (result.error !== undefined) {
                                var content_generated = result.error.message;
                            } else {
                                var content_generated = result.choices[0].delta !== undefined ? (result.choices[0].delta.content !== undefined ? result.choices[0].delta.content : '') : result.choices[0].text;
                            }
                            prompt_response += content_generated;
                            if((content_generated === '\n' || content_generated === ' \n' || content_generated === '.\n' || content_generated === '\n\n') && wpaicg_response_events > 0 && currentContent !== ''){
                                if(!wpaicg_newline_before) {
                                    wpaicg_newline_before = true;
                                    if (response_type === 'textarea') {
                                        if (basicEditor) {
                                            $('#editor-' + wpaicgEditorNumber).val(currentContent + "<br /><br />");
                                        } else {
                                            editor.setContent(currentContent + "<br /><br />");
                                        }
                                    } else {
                                        $('.wpaicg-prompt-response-element').append("<br/>");
                                    }
                                }
                            }
                            else if(content_generated.indexOf("\n") > -1 && wpaicg_response_events > 0 && currentContent !== ''){
                                if (!wpaicg_newline_before) {
                                    wpaicg_newline_before = true;
                                    content_generated = content_generated.replace(/\n/g,'<br>');
                                    if(response_type === 'textarea') {
                                        if (basicEditor) {
                                            $('#editor-' + wpaicgEditorNumber).val(currentContent + content_generated);
                                        } else {
                                            editor.setContent(currentContent + content_generated);
                                        }
                                    }
                                    else{
                                        $('.wpaicg-prompt-response-element').append(content_generated);
                                    }
                                }
                            }
                            else if(content_generated === '\n' && wpaicg_response_events === 0  && currentContent === ''){

                            }
                            else {
                                wpaicg_newline_before = false;
                                wpaicg_response_events += 1;
                                if(response_type === 'textarea') {
                                    if (basicEditor) {
                                        $('#editor-' + wpaicgEditorNumber).val(currentContent + content_generated);
                                    } else {
                                        editor.setContent(currentContent + content_generated);
                                    }
                                }
                                else{
                                    $('.wpaicg-prompt-response-element').append(content_generated);
                                }
                            }
                        }
                        if (count_line === wpaicg_limitLines) {
                            $('.wpaicg-prompt-form .wpaicg-prompt-stop-generate').hide();
                            if(!wpaicg_limited_token) {
                                let endTime = new Date();
                                let timeDiff = endTime - startTime;
                                timeDiff = timeDiff / 1000;
                                data += '&action=wpaicg_prompt_log&prompt_id=' + prompt_id + '&prompt_name=' + prompt_name + '&prompt_response=' + prompt_response + '&duration=' + timeDiff + '&_wpnonce=' + wp_nonce;
                                $.ajax({
                                    url: '<?php echo admin_url('admin-ajax.php')?>',
                                    data: data,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    success: function (res) {

                                    }
                                })
                            }
                            stopOpenAIGenerator();
                            wpaicgRmLoading(btn);
                        }
                    }
                }
            }
            else{
                alert('<?php echo esc_html__('Please enter prompt','gpt3-ai-content-generator')?>');
            }
            return false;
        })
    })
</script>
