<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if($post->post_status == 'auto-draft'){
    $wpaicg_generate_title = get_option('wpaicg_woo_generate_title','');
    $wpaicg_generate_description = get_option('wpaicg_woo_generate_description','');
    $wpaicg_generate_short = get_option('wpaicg_woo_generate_short','');
    $wpaicg_generate_tags = get_option('wpaicg_woo_generate_tags','');
    $wpaicg_generate_meta = get_option('wpaicg_woo_meta_description','');
}
else {
    $wpaicg_generate_title = get_post_meta($post->ID, 'wpaicg_generate_title', true);
    $wpaicg_generate_description = get_post_meta($post->ID, 'wpaicg_generate_description', true);
    $wpaicg_generate_short = get_post_meta($post->ID, 'wpaicg_generate_short', true);
    $wpaicg_generate_tags = get_post_meta($post->ID, 'wpaicg_generate_tags', true);
    $wpaicg_generate_meta = get_post_meta($post->ID, 'wpaicg_generate_meta', true);
}
?>
<div id="wpaicg-product-generator">
    <p class="wpaicg-form-row">
        <label><strong><?php echo esc_html__('Original Title','gpt3-ai-content-generator')?></strong></label>
        <input class="regular-text" name="wpaicg_original_title" id="wpaicg_original_title" type="text" value="<?php echo esc_html($post->post_title)?>">
    </p>
    <p class="wpaicg-form-row">
        <label for="wpaicg_generate_title"><?php echo esc_html__('Write a SEO friendly product title?','gpt3-ai-content-generator')?></label>
        <input<?php echo !empty($wpaicg_generate_title) ? ' checked':''?> type="checkbox" value="1" id="wpaicg_generate_title">
    </p>
    <p class="wpaicg-form-row">
        <label for="wpaicg_generate_title"><?php echo esc_html__('Write a SEO Meta Description?','gpt3-ai-content-generator')?></label>
        <input<?php echo !empty($wpaicg_generate_meta) ? ' checked':''?> type="checkbox" value="1" id="wpaicg_generate_meta">
    </p>
    <p class="wpaicg-form-row">
        <label for="wpaicg_generate_description"><?php echo esc_html__('Write a product description?','gpt3-ai-content-generator')?></label>
        <input<?php echo !empty($wpaicg_generate_description) ? ' checked':''?> type="checkbox" value="1" id="wpaicg_generate_description">
    </p>
    <p class="wpaicg-form-row">
        <label for="wpaicg_generate_short"><?php echo esc_html__('Write a short product description?','gpt3-ai-content-generator')?></label>
        <input<?php echo !empty($wpaicg_generate_short) ? ' checked':''?> type="checkbox" value="1" id="wpaicg_generate_short">
    </p>
    <p class="wpaicg-form-row">
        <label for="wpaicg_generate_tags"><?php echo esc_html__('Generate product tags?','gpt3-ai-content-generator')?></label>
        <input<?php echo !empty($wpaicg_generate_tags) ? ' checked':''?> type="checkbox" value="1" id="wpaicg_generate_tags">
    </p>
    <button type="button" class="button button-primary wpaicg_product_generator_btn"><?php echo esc_html__('Generate','gpt3-ai-content-generator')?></button>
    <hr>
    <p class="wpaicg-form-row">
        <label><strong><?php echo esc_html__('Product Title','gpt3-ai-content-generator')?></strong></label>
        <input class="regular-text" name="wpaicg_product_title" id="wpaicg_product_title" type="text">
    </p>
    <p class="wpaicg-form-row">
        <label><strong><?php echo esc_html__('SEO Meta Description','gpt3-ai-content-generator')?></strong></label>
        <input class="regular-text" name="wpaicg_product_meta" id="wpaicg_product_meta" type="text">
    </p>
    <p class="wpaicg-form-row">
        <label><strong><?php echo esc_html__('Product Short Description','gpt3-ai-content-generator')?></strong></label>
    </p>
    <p>
        <textarea rows="5" name="wpaicg_product_short" id="wpaicg_product_short"></textarea>
    </p>
    <p class="wpaicg-form-row">
        <label><strong><?php echo esc_html__('Product Description','gpt3-ai-content-generator')?></strong></label>
    </p>
    <p>
        <textarea rows="10" name="wpaicg_product_description" id="wpaicg_product_description"></textarea>
    </p>
    <p class="wpaicg-form-row">
        <label><strong><?php echo esc_html__('Product Tags','gpt3-ai-content-generator')?></strong></label>
        <input class="regular-text" name="wpaicg_product_tags" id="wpaicg_product_tags" type="text">
    </p>
    <button style="display: none;" type="button" class="button button-primary wpaicg_product_generator_save"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
</div>
<script>
    jQuery(document).ready(function ($){
        var wpaicg_product_generator_save = $('.wpaicg_product_generator_save');
        var wpaicg_product_generator_btn = $('.wpaicg_product_generator_btn');
        var wpaicg_generator_process = $('.wpaicg-generating-process');
        function wpaicg_ShowError(msg, timer){
            clearTimeout(window['wpaicgTimer']);
            wpaicg_generator_process.append('<div class="wpaicg-error-msg">'+msg+'</div>');
        }
        function wpaicg_generatorProcess(step, message){
            if(step === 'finished') {
                wpaicg_generator_process.append('<div class="wpaicg-generating-process-' + step + '"><p>' + message + '</p></div>');
            }
            else{
                wpaicg_generator_process.append('<div class="wpaicg-generating-process-' + step + '"><p>' + message + '</p><span>In Progress..</span></div>');
            }
        }
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
        function wpaicgProductGenerator(title,step, steps){
            var wpaicg_next_step = step+1;
            var wpaicg_step = steps[step];
            var data = {'action': 'wpaicg_product_generator', 'step': wpaicg_step,'title' : title,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'}
            if(wpaicg_step === 'title'){
                wpaicg_generatorProcess(wpaicg_step,'<?php echo esc_html__('Generating Title','gpt3-ai-content-generator')?>');
            }
            if(wpaicg_step === 'meta'){
                wpaicg_generatorProcess(wpaicg_step,'<?php echo esc_html__('Generating Meta Description','gpt3-ai-content-generator')?>');
            }
            if(wpaicg_step === 'description'){
                wpaicg_generatorProcess(wpaicg_step,'<?php echo esc_html__('Generating Description','gpt3-ai-content-generator')?>');
            }
            if(wpaicg_step === 'short'){
                wpaicg_generatorProcess(wpaicg_step,'<?php echo esc_html__('Generating Short Description','gpt3-ai-content-generator')?>');
            }
            if(wpaicg_step === 'tags'){
                wpaicg_generatorProcess(wpaicg_step,'<?php echo esc_html__('Generating Product Tags','gpt3-ai-content-generator')?>');
            }
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: data,
                dataType: 'JSON',
                type: 'POST',
                success: function (res){
                    if(res.status === 'success'){
                        wpaicg_generator_process.find('.wpaicg-generating-process-'+wpaicg_step).addClass('finished');
                        wpaicg_generator_process.find('.wpaicg-generating-process-'+wpaicg_step+' span').html('Done');
                        $('#wpaicg_product_'+wpaicg_step).val(res.data);
                        if(wpaicg_next_step === steps.length){
                            $('.wpcgai_lds-ellipsis').hide();
                            wpaicg_generatorProcess('finished','<?php echo esc_html__('Finished','gpt3-ai-content-generator')?>');
                            clearTimeout(window['wpaicgTimer']);
                            wpaicgRmLoading(wpaicg_product_generator_btn);
                            wpaicg_product_generator_save.show();
                        }
                        else{
                            wpaicgProductGenerator(title,wpaicg_next_step, steps);
                        }
                    }
                    else{
                        wpaicg_generator_process.find('.wpaicg-generating-process-'+wpaicg_step).addClass('wpaicg-error');
                        wpaicg_generator_process.find('.wpaicg-generating-process-'+wpaicg_step+' span').html('<?php echo esc_html__('Error','gpt3-ai-content-generator')?>');
                        wpaicg_ShowError(res.msg, window['wpaicgTimer']);
                        wpaicgRmLoading(wpaicg_product_generator_btn);
                    }
                },
                error: function (){
                    $('.wpcgai_lds-ellipsis').hide();
                    wpaicg_generator_process.find('.wpaicg-generating-process-'+wpaicg_step).addClass('error');
                    wpaicg_generator_process.find('.wpaicg-generating-process-'+wpaicg_step+' span').html('<?php echo esc_html__('Error','gpt3-ai-content-generator')?>');
                    wpaicg_ShowError('<?php echo esc_html__('The server is currently overloaded with other requests. Sorry about that! You can retry your request, or contact us through our help center at help.openai.com if the error persists..','gpt3-ai-content-generator')?>', window['wpaicgTimer']);
                    wpaicgRmLoading(wpaicg_product_generator_btn);
                }
            })
        }
        wpaicg_product_generator_save.click(function (){
            var data = $('#wpaicg-product-generator input').serialize()+'&'+$('#wpaicg-product-generator textarea').serialize()+'&action=wpaicg_product_save&id='+$('#post_ID').val();
            var wpaicg_generate_title = $('#wpaicg_generate_title').prop('checked') ? 1 : 0;
            var wpaicg_generate_description = $('#wpaicg_generate_description').prop('checked') ? 1 : 0;
            var wpaicg_generate_short = $('#wpaicg_generate_short').prop('checked') ? 1 : 0;
            var wpaicg_generate_tags = $('#wpaicg_generate_tags').prop('checked') ? 1 : 0;
            data += '&wpaicg_generate_title='+wpaicg_generate_title+'&wpaicg_generate_description='+wpaicg_generate_description+'&wpaicg_generate_short='+wpaicg_generate_short+'&wpaicg_generate_tags='+wpaicg_generate_tags;
            if($('#original_post_status').val() !== undefined && $('#original_post_status').val() === 'auto-draft'){
                data += '&mode=new';
            }
            else{
                data += '&mode=edit';
            }
            data += '&nonce=<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>';
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: data,
                dataType: 'JSON',
                type: 'POST',
                beforeSend: function (){
                    wpaicgLoading(wpaicg_product_generator_save);
                },
                success: function (res){
                    if(res.status === 'success'){
                        window.location.href =  res.url;
                    }
                    else{
                        wpaicgRmLoading(wpaicg_product_generator_save);
                        alert(res.msg);
                    }
                },
                error: function (){
                    wpaicgRmLoading(wpaicg_product_generator_save);
                    alert('Something went wrong');
                }
            });
        })
        wpaicg_product_generator_btn.click(function (){
            var wpaicg_title = $('#wpaicg_original_title').val();
            var wpaicg_generate_title = $('#wpaicg_generate_title').prop('checked') ? 1 : 0;
            var wpaicg_generate_description = $('#wpaicg_generate_description').prop('checked') ? 1 : 0;
            var wpaicg_generate_meta = $('#wpaicg_generate_meta').prop('checked') ? 1 : 0;
            var wpaicg_generate_short = $('#wpaicg_generate_short').prop('checked') ? 1 : 0;
            var wpaicg_generate_tags = $('#wpaicg_generate_tags').prop('checked') ? 1 : 0;
            var wpaicgSteps = [];
            if(wpaicg_generate_title){
                wpaicgSteps.push('title');
            }
            if(wpaicg_generate_meta){
                wpaicgSteps.push('meta');
            }
            if(wpaicg_generate_description){
                wpaicgSteps.push('description');
            }
            if(wpaicg_generate_short){
                wpaicgSteps.push('short');
            }
            if(wpaicg_generate_tags){
                wpaicgSteps.push('tags');
            }
            if(wpaicg_title === ''){
                alert('<?php echo esc_html__('Please enter product title','gpt3-ai-content-generator')?>')
            }
            else if(!wpaicgSteps.length){
                alert('<?php echo esc_html__('Please choose least one field','gpt3-ai-content-generator')?>')
            }
            else{
                var h1 = $('.wpaicg-timer'), seconds = 0, minutes = 0,t;
                function add() {
                    seconds++;
                    if (seconds >= 60) {
                        seconds = 0;
                        minutes++;
                    }
                    if (minutes >= 60) {
                        minutes = 0;
                        hours++;
                    }
                    var htmlTimer = (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);
                    h1.html(htmlTimer);

                    timer();
                }

                function timer() {
                    window['wpaicgTimer'] = setTimeout(add, 1000);
                }
                timer();
                wpaicg_generator_process.empty();
                $('.wpcgai_lds-ellipsis').show();
                wpaicg_product_generator_save.hide();
                wpaicgLoading(wpaicg_product_generator_btn);
                wpaicgProductGenerator(wpaicg_title,0,wpaicgSteps);
            }
        });
    })
</script>
