<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_defaults = array(
    'temperature' => 0.7,
    'max_tokens' => 500,
    'top_p' => 0.1,
    'frequency_penalty' => 0.1,
    'presence_penalty' => 0.1,

);
$wpaicg_prompts = array(
    esc_html__('Select Prompt','gpt3-ai-content-generator') => "",
    "Tweet Summary" => "Create a tweet summarizing the main points of a news article. Provide the article's title. \nArticle Title: 'New study shows link between air pollution and heart disease'",
    "Keyword Extract" => "Extract keywords from this text:\nBlack-on-black ware is a 20th- and 21st-century pottery tradition developed by the Puebloan Native American ceramic artists in Northern New Mexico. Traditional reduction-fired blackware has been made for centuries by pueblo artists. Black-on-black ware of the past century is produced with a smooth surface, with the designs applied through selective burnishing or the application of refractory slip. Another style involves carving or incising designs and selectively polishing the raised areas. For generations several families from Kha'po Owingeh and P'ohwhóge Owingeh pueblos have been making black-on-black ware with the techniques passed down from matriarch potters. Artists from other pueblos have also produced black-on-black ware. Several contemporary artists have created works honoring the pottery of their ancestors.\nArticle Title: 'New study shows link between air pollution and heart disease'",
    "Article generator" => "Generate an article about: healthy diet."
);
$wpaicg_custom_models = get_option('wpaicg_custom_models',array());
$wpaicg_custom_models = array_merge(array('text-davinci-003','text-curie-001','text-babbage-001','text-ada-001'),$wpaicg_custom_models);
?>
<style>
    .comparison_tool{
        display: flex;
    }
    .wpaicg-comparison-output,textarea[name=prompt]{
        height: 150px;
    }
    .wpaicg-comparison-second{
        height: 150px;
        display: flex;
        align-items: center;
    }
    input[type=text]{
        width: 100%;
        text-align: center;
    }
    select{
        width: 100%;
    }
    .button{
        display: block;
        width: 100%;
    }
    .wpaicg-comparison-height{
        height: 30px;
        display: flex;
        align-items: center;
    }
    .wpaicg-comparison-item{
        position: relative;
        width: 250px;
        padding: 10px;
        background: #d5d5d5;
        border-radius: 4px;
    }
    .wpaicg-comparison-close{
        position: absolute;
        width: 15px;
        height: 15px;
        right: 2px;
        top: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ef6262;
        border-radius: 3px;
        color: #fff;
        cursor: pointer;
    }
    .comparison_tool{
        display: flex;
        gap: 10px;
        min-width: min-content;
    }
    .wpaicg-comparison-add{
        position: relative;
        width: 250px;
        padding: 10px;
        border-radius: 4px;
        border-width: 2px;
        border-style: dashed;
        border-color: #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .wpaicg-comparison-add span{
        font-size: 59px;
        width: 59px;
        height: 59px;
        color: #a1a1a1;
    }
    .wpaicg-comparison-words,.wpaicg-comparison-tokens,.wpaicg-comparison-cost,.wpaicg-comparison-duration{
        text-align: center;
        flex: 1;
        position: relative;
    }
    .wpaicg-good:before{
        content: '✅';
        display: block;
        position: absolute;
        right: 0;
    }
    .wpaicg-not-good:before{
    }
</style>
<div class="wpaicg-comparison-default" style="display: none">
    <div class="wpaicg-comparison-item wpaicg-comparison-item-[ID]">
        <form action="" method="post" class="wpaicg-comparison-form">
            <?php
            wp_nonce_field('wpaicg_comparison_generator');
            ?>
            <input type="hidden" name="action" value="wpaicg_comparison">
            <span class="wpaicg-comparison-close">&times;</span>
            <div class="" style="height: 10px"></div>
            <div class="wpaicg-mb-10 wpaicg-comparison-height">
                <select class="wpaicg-comparison-select-prompt">
                    <?php
                    foreach($wpaicg_prompts as $key=>$wpaicg_prompt){
                        echo '<option value="'.esc_html($wpaicg_prompt).'">'.esc_html($key).'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="wpaicg-mb-10 wpaicg-comparison-second">
                <textarea name="prompt"></textarea>
            </div>
            <div class="wpaicg-mb-10 wpaicg-comparison-height">
                <select name="model">
                    <?php
                    foreach($wpaicg_custom_models as $wpaicg_custom_model){
                        echo '<option value="'.esc_html($wpaicg_custom_model).'">'.esc_html($wpaicg_custom_model).'</option>';
                        if($wpaicg_custom_model == 'text-davinci-003'){
                            echo '<option value="gpt-3.5-turbo">gpt-3.5-turbo</option>';
                        }
                    }
                    ?>
                    <option value="gpt-4">gpt-4 (<?php echo esc_html__('Limited Beta','gpt3-ai-content-generator')?>)</option>
                    <option value="gpt-4-32k">gpt-4-32k (<?php echo esc_html__('Limited Beta','gpt3-ai-content-generator')?>)</option>
                </select>
            </div>
            <?php
            foreach ($wpaicg_defaults as $key=>$wpaicg_default):
            ?>
            <div class="wpaicg-mb-10 wpaicg-comparison-height">
                <input type="text" name="<?php echo esc_html($key)?>" value="<?php echo esc_html($wpaicg_default)?>">
            </div>
            <?php
            endforeach;
            ?>
            <div class="wpaicg-mb-10 wpaicg-comparison-height" style="justify-content: space-between">
                <button class="button button-primary wpaicg-comparison-submit"><?php echo esc_html__('Generate','gpt3-ai-content-generator')?></button>
                <span class="wpaicg-comparison-space" style="display: none">&nbsp;&nbsp;</span>
                <button style="display: none" type="button" class="button button-link-delete wpaicg-comparison-cancel"><?php echo esc_html__('Cancel','gpt3-ai-content-generator')?></button>
            </div>
            <div class="wpaicg-mb-10 wpaicg-comparison-second">
                <textarea class="wpaicg-comparison-output"></textarea>
            </div>
            <div class="wpaicg-mb-10 wpaicg-comparison-height" style="justify-content: center;border-bottom: 1px solid #ccc;">
                <div class="wpaicg-comparison-tokens"></div>
            </div>
            <div class="wpaicg-mb-10 wpaicg-comparison-height" style="justify-content: center;border-bottom: 1px solid #ccc;">
                <div class="wpaicg-comparison-cost"></div>
            </div>
            <div class="wpaicg-mb-10 wpaicg-comparison-height" style="justify-content: center;border-bottom: 1px solid #ccc;">
                <div class="wpaicg-comparison-duration"></div>
            </div>
            <div class="wpaicg-mb-10 wpaicg-comparison-height" style="justify-content: center;border-bottom: 1px solid #ccc;">
                <div class="wpaicg-comparison-words"></div>
            </div>
        </form>
    </div>
</div>
<div class="comparison_tool">
    <div style="padding: 10px 0px">
        <div class="wpaicg-mb-10"></div>
        <div class="wpaicg-mb-10"></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"><strong></strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-second"><strong><?php echo esc_html__('Prompt','gpt3-ai-content-generator')?>:</strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"><strong><?php echo esc_html__('Engine','gpt3-ai-content-generator')?>:</strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"><strong><?php echo esc_html__('Temp','gpt3-ai-content-generator')?>:</strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"><strong><?php echo esc_html__('Max Tokens','gpt3-ai-content-generator')?>:</strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"><strong><?php echo esc_html__('Top P','gpt3-ai-content-generator')?>:</strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"><strong><?php echo esc_html__('Fre. P','gpt3-ai-content-generator')?>:</strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"><strong><?php echo esc_html__('Pre. P','gpt3-ai-content-generator')?>:</strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-second"><strong><?php echo esc_html__('Output','gpt3-ai-content-generator')?>:</strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"><strong><?php echo esc_html__('Token','gpt3-ai-content-generator')?>:</strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"><strong><?php echo esc_html__('Cost','gpt3-ai-content-generator')?>:</strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"><strong><?php echo esc_html__('Duration','gpt3-ai-content-generator')?>:</strong></div>
        <div class="wpaicg-mb-10 wpaicg-comparison-height"><strong><?php echo esc_html__('Word','gpt3-ai-content-generator')?>:</strong></div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($){
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
        for(let i = 0;i< 4; i++){
            let html = $('.wpaicg-comparison-default').html();
            html = html.replace('[ID]',i);
            $('.comparison_tool').append(html);
            if(i === 1){
                $('.wpaicg-comparison-item-1 select[name=model]').val('gpt-3.5-turbo')
            }
            if(i === 2){
                $('.wpaicg-comparison-item-2 select[name=model]').val('gpt-4')
            }
            if(i === 3){
                $('.wpaicg-comparison-item-3 select[name=model]').val('text-ada-001')
            }
            if(i === 3){
                $('.comparison_tool').append('<div class="wpaicg-comparison-add"><span class="dashicons dashicons-plus-alt"></span></div>');
            }
        }
        $(document).on('click','.wpaicg-comparison-close', function (e){
            $(e.currentTarget).closest('.wpaicg-comparison-item').remove();
            wpaicgCompareResult();
        });
        $(document).on('click','.wpaicg-comparison-add', function (e){
            var button = $(e.currentTarget);
            button.before($('.wpaicg-comparison-default').html());
            $([document.documentElement, document.body]).animate({
                scrollLeft: document.body.scrollWidth
            }, 50);

        });
        $(document).on('change','.wpaicg-comparison-select-prompt', function (e){
            let sel = $(e.currentTarget);
            let value = sel.val();
            let form = sel.closest('.wpaicg-comparison-form');
            form.find('textarea[name=prompt]').val(value);
        });
        function wpaicgCompareResult(){
            let min_tokens = 0;
            let max_words = 0;
            let min_cost = 0;
            let min_duration = 0;
            let el_tokens = false;
            let el_words = false;
            let el_cost =false;
            let el_duration = false;
            $('.wpaicg-comparison-result').each(function (idx, item){
                let duration = parseFloat($(item).attr('data-duration'));
                let words = parseFloat($(item).attr('data-words'));
                let cost = parseFloat($(item).attr('data-cost'))
                let tokens = parseFloat($(item).attr('data-tokens'));
                if((min_tokens > 0 && tokens < min_tokens) || min_tokens === 0){
                    min_tokens = tokens;
                    el_tokens = item;
                }
                if((min_cost > 0 && cost < min_cost) || min_cost === 0){
                    min_cost = cost;
                    el_cost = item;
                }
                if((min_duration > 0 && duration < min_duration) || min_duration === 0){
                    min_duration = duration;
                    el_duration = item;
                }
                if((max_words > 0 && words > max_words) || max_words === 0){
                    max_words = words;
                    el_words = item;
                }
            });
            $('.wpaicg-comparison-result').each(function (idx, item){
                $(item).find('.wpaicg-comparison-cost').removeClass('wpaicg-good');
                $(item).find('.wpaicg-comparison-words').removeClass('wpaicg-good');
                $(item).find('.wpaicg-comparison-tokens').removeClass('wpaicg-good');
                $(item).find('.wpaicg-comparison-duration').removeClass('wpaicg-good');
                $(item).find('.wpaicg-comparison-tokens').addClass('wpaicg-not-good');
                $(item).find('.wpaicg-comparison-words').addClass('wpaicg-not-good');
                $(item).find('.wpaicg-comparison-duration').addClass('wpaicg-not-good');
                $(item).find('.wpaicg-comparison-cost').addClass('wpaicg-not-good');
            });
            if(el_tokens){
                $(el_tokens).find('.wpaicg-comparison-tokens').removeClass('wpaicg-not-good');
                $(el_tokens).find('.wpaicg-comparison-tokens').addClass('wpaicg-good');
            }
            if(el_words){
                $(el_words).find('.wpaicg-comparison-words').removeClass('wpaicg-not-good');
                $(el_words).find('.wpaicg-comparison-words').addClass('wpaicg-good');
            }
            if(el_cost){
                $(el_cost).find('.wpaicg-comparison-cost').removeClass('wpaicg-not-good');
                $(el_cost).find('.wpaicg-comparison-cost').addClass('wpaicg-good');
            }
            if(el_duration){
                $(el_duration).find('.wpaicg-comparison-duration').removeClass('wpaicg-not-good');
                $(el_duration).find('.wpaicg-comparison-duration').addClass('wpaicg-good');
            }

        }
        $(document).on('click', '.wpaicg-comparison-cancel', function (e){
            let id = $(e.currentTarget).attr('data-id');
            let item = $(e.currentTarget).closest('.wpaicg-comparison-item');
            window['wpaicg_comparison_'+id].abort();
            let btn = item.find('.wpaicg-comparison-submit');
            wpaicgRmLoading(btn);
            item.find('.wpaicg-comparison-space').hide();
            $(e.currentTarget).hide();
            wpaicgCompareResult();
        })
        $(document).on('submit','.wpaicg-comparison-form', function (e){
            e.preventDefault();
            let startTime = new Date();
            let form = $(e.currentTarget);
            let item = form.closest('.wpaicg-comparison-item');
            item.removeClass('wpaicg-comparison-result')
            let temperature = parseFloat(form.find('input[name=temperature]').val());
            let max_tokens = parseFloat(form.find('input[name=max_tokens]').val());
            let top_p = parseFloat(form.find('input[name=top_p]').val());
            let frequency_penalty = parseFloat(form.find('input[name=frequency_penalty]').val());
            let presence_penalty = parseFloat(form.find('input[name=presence_penalty]').val());
            let prompt = form.find('textarea[name=prompt]').val();
            let model = form.find('select[name=model]').val();
            let has_error = false;
            let btn = form.find('.wpaicg-comparison-submit');
            if(prompt === ''){
                has_error = 'Please enter Prompt';
            }
            if(!has_error && (temperature > 1 || temperature < 0)){
                has_error = '<?php echo sprintf(esc_html__('Please enter a valid temperature value between %d and %d.','gpt3-ai-content-generator'),0,1)?>';
            }
            if(!has_error && (top_p > 1 || top_p < 0)){
                has_error = '<?php echo sprintf(esc_html__('Please enter a valid top p value between %d and %d.','gpt3-ai-content-generator'),0,1)?>';
            }
            if(!has_error && (frequency_penalty > 2 || frequency_penalty < 0)){
                has_error = '<?php echo sprintf(esc_html__('Please enter a valid frequency penalty value between %d and %d.','gpt3-ai-content-generator'),0,2)?>';
            }
            if(!has_error && (presence_penalty > 2 || presence_penalty < 0)){
                has_error = '<?php echo sprintf(esc_html__('Please enter a valid presence penalty value between %d and %d.','gpt3-ai-content-generator'),0,2)?>';
            }
            if(!has_error && (model === 'gpt-3.5-turbo' || model === 'text-davinci-003') && (max_tokens > 4096 || max_tokens < 0)){
                has_error = '<?php echo sprintf(esc_html__('Please enter a valid max token value between %d and %d.','gpt3-ai-content-generator'),0,4096)?>';
            }
            if(!has_error && model === 'gpt-4' && (max_tokens > 8192 || max_tokens < 0)){
                has_error = '<?php echo sprintf(esc_html__('Please enter a valid max token value between %d and %d.','gpt3-ai-content-generator'),0,8192)?>';
            }
            if(!has_error && model === 'gpt-4-32k' && (max_tokens > 32768 || max_tokens < 0)){
                has_error = '<?php echo sprintf(esc_html__('Please enter a valid max token value between %d and %d.','gpt3-ai-content-generator'),0,32768)?>'
            }
            if(!has_error && (model === 'text-ada-001' || model === 'text-babbage-001' || model === 'text-curie-001') && (max_tokens > 2049 || max_tokens < 0)){
                has_error = '<?php echo sprintf(esc_html__('Please enter a valid max token value between %d and %d.','gpt3-ai-content-generator'),0,2049)?>'
            }
            if(!has_error){
                let randomID = Math.ceil(Math.random()*10000);
                item.find('.wpaicg-comparison-cancel').show();
                item.find('.wpaicg-comparison-space').show();
                item.find('.wpaicg-comparison-cancel').attr('data-id',randomID);
                $('.wpaicg-comparison-item').each(function (idx, itemx){
                    $(itemx).find('.wpaicg-comparison-cost').removeClass('wpaicg-good');
                    $(itemx).find('.wpaicg-comparison-words').removeClass('wpaicg-good');
                    $(itemx).find('.wpaicg-comparison-tokens').removeClass('wpaicg-good');
                    $(itemx).find('.wpaicg-comparison-duration').removeClass('wpaicg-good');
                    $(itemx).find('.wpaicg-comparison-tokens').removeClass('wpaicg-not-good');
                    $(itemx).find('.wpaicg-comparison-words').removeClass('wpaicg-not-good');
                    $(itemx).find('.wpaicg-comparison-duration').removeClass('wpaicg-not-good');
                    $(itemx).find('.wpaicg-comparison-cost').removeClass('wpaicg-not-good');
                });
                $(item).find('.wpaicg-comparison-cost').empty();
                $(item).find('.wpaicg-comparison-words').empty();
                $(item).find('.wpaicg-comparison-tokens').empty();
                $(item).find('.wpaicg-comparison-duration').empty();
                window['wpaicg_comparison_'+randomID] = $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: form.serialize(),
                    dataType: 'JSON',
                    type:'POST',
                    beforeSend: function (res){
                        wpaicgLoading(btn)
                    },
                    success: function (res){
                        wpaicgRmLoading(btn);
                        item.find('.wpaicg-comparison-cancel').hide();
                        item.find('.wpaicg-comparison-space').hide();
                        if(res.status === 'success'){
                            let endTime = new Date();
                            let timeDiff = (endTime - startTime)/1000;
                            let text = res.text;
                            text = text.replace(/\\/g,'');
                            form.find('.wpaicg-comparison-output').val(text);
                            form.find('.wpaicg-comparison-tokens').html(res.tokens);
                            form.find('.wpaicg-comparison-cost').html('$'+parseFloat(res.cost).toFixed(5));
                            form.find('.wpaicg-comparison-words').html(res.words);
                            form.find('.wpaicg-comparison-duration').html(timeDiff.toFixed(2)+' seconds');
                            item.addClass('wpaicg-comparison-result');
                            item.attr('data-tokens',res.tokens);
                            item.attr('data-cost',res.cost);
                            item.attr('data-words',res.words);
                            item.attr('data-duration',timeDiff);
                            wpaicgCompareResult();
                        }
                        else{
                            form.find('.wpaicg-comparison-output').val(res.msg);
                            wpaicgCompareResult();
                        }
                    }
                })
            }
            else{
                alert(has_error);
            }
        })
    })
</script>
