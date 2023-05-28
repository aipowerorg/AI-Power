<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.wpaicg_sync_finetune').click(function (){
            var btn = $(this);
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: {action: 'wpaicg_fetch_finetunes','nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                dataType: 'JSON',
                type: 'POST',
                beforeSend: function (){
                    btn.html('Loading..');
                },
                success: function (res){
                    btn.html("Sync");
                    if(res.status === 'success'){
                        window.location.reload();
                    }
                    else{
                        alert(res.msg);
                    }
                },
                error: function (){
                    btn.html("Sync");
                    alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                }
            })
        })
        $('#wpaicg-meta-description').on('keyup', function (){
            if($(this).val() === ''){
                $('#wpaicg-seo-tab-item').removeClass('wpaicg-has-seo');
            }
            else{
                $('#wpaicg-seo-tab-item').addClass('wpaicg-has-seo');
            }
        });
        $('#wpcgai_preview_box').on('keyup', function (){
            if($(this).val() === ''){
                $('#wpaicg-seo-tab-content').removeClass('wpaicg-has-seo');
            }
            else{
                $('#wpaicg-seo-tab-content').addClass('wpaicg-has-seo');
            }
        });
        $('.wpaicg-tabs ul li').click(function(){
            if(!$(this).hasClass('wpaicg-active')) {
                var targetID = $(this).attr('data-target');
                $('.wpaicg-tabs ul li').removeClass('wpaicg-active');
                $('.wpaicg-tab-content > div').hide();
                $(this).addClass('wpaicg-active');
                $('#' + targetID).show();
            }
        });
        var oldStep = '';
        var startTime;
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
                wpaicg_generator_process.append('<div class="wpaicg-generating-process-' + step + '"><p>' + message + '</p><span><?php echo esc_html__('In Progress..','gpt3-ai-content-generator')?></span></div>');
            }
        }
        jQuery('#wpcgai_load_plugin_settings').on('click', function(e) {
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
            var wpai_preview_title = $("#wpai_preview_title").val();
            // we need to display error if title is empty
            if (wpai_preview_title == "") {
                alert("<?php echo esc_html__('Please enter title','gpt3-ai-content-generator')?>");
                return false;
            }
            var wpai_number_of_heading = $("#wpai_number_of_heading").val();
            // we need to display error if number of heading is empty
            if (wpai_number_of_heading == "") {
                alert("<?php echo esc_html__('Please enter number of heading','gpt3-ai-content-generator')?>");
                return false;
            }
            if(wpai_number_of_heading > 15){
                alert("<?php echo esc_html__('Limited 15 headings','gpt3-ai-content-generator')?>");
                return false;
            }
            let wpaicg_custom_image_settings = {};
            $('[name^=wpaicg_custom_image_settings]').each(function(idx, elem) {
                let name = $(elem).attr('name');
                name = name.replace(/wpaicg_custom_image_settings/g,'');
                name = name.replace('[','');
                name = name.replace(']','');
                wpaicg_custom_image_settings[name] = $(elem).val();
            });
            var wpaicg_toc = $('#wpaicg_toc:checked').val();
            wpaicg_toc = wpaicg_toc ? wpaicg_toc : 0;
            var wpai_language = $("#wpai_language").val();
            var wpai_add_intro = $("#wpai_add_intro").val();
            var wpai_add_conclusion = $("#wpai_add_conclusion").val();
            var wpaicg_seo_meta_desc = $("#wpaicg_seo_meta_desc:checked").val();
            var wpai_writing_style = $("#wpai_writing_style").val();
            var wpai_writing_tone = $("#wpai_writing_tone").val();
            var wpai_keywords = $("#wpai_keywords").val();
            var wpai_add_keywords_bold = $("#wpai_add_keywords_bold").val();
            var wpai_heading_tag = $("#wpai_heading_tag").val();
            var wpai_words_to_avoid = $("#wpai_words_to_avoid").val();
            var wpai_add_tagline = $("#wpai_add_tagline").val();
            var wpai_add_faq = $("#wpai_add_faq2:checked").length;
            /*Fix auto add FAQ*/
            wpai_add_faq = wpai_add_faq ? 1 : 0;
            var wpai_target_url = $("#wpai_target_url").val();
            var wpai_anchor_text = $("#wpai_anchor_text").val();
            var wpai_target_url_cta = $("#wpai_target_url_cta").val();
            var wpai_cta_pos = $("#wpai_cta_pos").val();
            var wpai_img_size = $("#_wporg_img_size").val();
            var wpai_img_style = $("#_wporg_img_style").val();
            var wpaicg_toc_title = $("#wpaicg_toc_title").val();
            var wpaicg_toc_title_tag = $("#wpaicg_toc_title_tag").val();
            var wpaicg_intro_title_tag = $("#wpaicg_intro_title_tag").val();
            var wpaicg_conclusion_title_tag = $("#wpaicg_conclusion_title_tag").val();
            var wpai_modify_headings = $('#wpai_modify_headings').val();
            var is_generate_continue = $('#is_generate_continue').val();
            var hfHeadings = $("#hfHeadings").val();

            /*
            * Add Image and Featured
            * */
            var wpaicg_image_source = $('#wpaicg_image_source').val();
            var wpaicg_featured_image_source = $('#wpaicg_featured_image_source').val();
            var wpaicg_pexels_orientation = $('#wpaicg_pexels_orientation').val();
            var wpaicg_pexels_size = $('#wpaicg_pexels_size').val();
            var wpaicg_pixabay_order = $('#wpaicg_pixabay_order').val();
            var wpaicg_pixabay_orientation = $('#wpaicg_pixabay_orientation').val();
            var wpaicg_pixabay_language = $('#wpaicg_pixabay_language').val();
            var wpaicg_pixabay_type = $('#wpaicg_pixabay_type').val();
            var wpaicg_meta_custom_prompt_enable = $('.wpaicg_meta_custom_prompt_enable:checked').length ? 1 : 0;
            var wpaicg_meta_custom_prompt = $('.wpaicg_meta_custom_prompt').val();
            var wpaicg_pixabay_enable_prompt = $('#wpaicg_pixabay_enable_prompt:checked').length ? 1 : 0;
            var wpaicg_pixabay_custom_prompt = $('#wpaicg_pixabay_custom_prompt').val();
            var wpaicg_pexels_enable_prompt = $('#wpaicg_pexels_enable_prompt:checked').length ? 1 : 0;
            var wpaicg_pexels_custom_prompt = $('#wpaicg_pexels_custom_prompt').val();
            $('#is_generate_continue').val(0);

            $('.wpcgai_menu_editor').html('');

            e.preventDefault();

            jQuery('.wpcgai_lds-ellipsis').show();
            if(window['wpaicgTimer'] !== undefined){
                clearTimeout(window['wpaicgTimer']);
            }
            timer();
            var data = {
                'action': 'wpaicg_content_generator',
                'wpai_preview_title': wpai_preview_title,
                'wpai_number_of_heading': wpai_number_of_heading,
                'wpaicg_image_source': wpaicg_image_source,
                'wpaicg_featured_image_source': wpaicg_featured_image_source,
                'wpaicg_pexels_orientation': wpaicg_pexels_orientation,
                'wpaicg_pexels_size': wpaicg_pexels_size,
                'wpaicg_pixabay_type': wpaicg_pixabay_type,
                'wpaicg_pixabay_language': wpaicg_pixabay_language,
                'wpaicg_pixabay_order': wpaicg_pixabay_order,
                'wpaicg_pixabay_orientation': wpaicg_pixabay_orientation,
                'wpai_language': wpai_language,
                'wpai_add_intro': wpai_add_intro,
                'wpai_add_conclusion': wpai_add_conclusion,
                'wpai_writing_style': wpai_writing_style,
                'wpai_writing_tone': wpai_writing_tone,
                'wpai_keywords': wpai_keywords,
                'wpai_add_keywords_bold': wpai_add_keywords_bold,
                'wpai_heading_tag': wpai_heading_tag,
                'wpai_words_to_avoid': wpai_words_to_avoid,
                'wpai_add_tagline': wpai_add_tagline,
                'wpai_add_faq': wpai_add_faq,
                'wpai_target_url': wpai_target_url,
                'wpai_anchor_text': wpai_anchor_text,
                'wpai_modify_headings': wpai_modify_headings,
                'is_generate_continue': is_generate_continue,
                'hfHeadings': hfHeadings,
                'wpai_target_url_cta' : wpai_target_url_cta,
                'wpai_cta_pos' : wpai_cta_pos,
                'step': 'heading',
                'wpaicg_content': '',
                'wpai_img_size': wpai_img_size,
                'wpai_img_style': wpai_img_style,
                'wpaicg_seo_meta_desc': wpaicg_seo_meta_desc,
                'wpaicg_toc': wpaicg_toc,
                'wpaicg_toc_title': wpaicg_toc_title,
                'wpaicg_toc_title_tag': wpaicg_toc_title_tag,
                'wpaicg_hide_introduction': $('#wpaicg_hide_introduction:checked').length ? 1 : 0,
                'wpaicg_hide_conclusion': $('#wpaicg_hide_conclusion:checked').length ? 1 : 0,
                'wpaicg_intro_title_tag': wpaicg_intro_title_tag,
                'wpaicg_conclusion_title_tag': wpaicg_conclusion_title_tag,
                'wpaicg_toc_list': '',
                'wpaicg_custom_image_settings': wpaicg_custom_image_settings,
                'wpaicg_custom_prompt_enable': wpaicg_meta_custom_prompt_enable,
                'wpaicg_custom_prompt': wpaicg_meta_custom_prompt,
                'wpaicg_pixabay_enable_prompt': wpaicg_pixabay_enable_prompt,
                'wpaicg_pixabay_custom_prompt': wpaicg_pixabay_custom_prompt,
                'wpaicg_pexels_enable_prompt': wpaicg_pexels_enable_prompt,
                'wpaicg_pexels_custom_prompt': wpaicg_pexels_custom_prompt,
                'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'
            };
            $('#wpcgai_preview_box').text('');
            $('#wpcgai_preview_box').val('');
            $('#wpaicg-meta-description').val('');
            $('#wpaicg-meta-description').text('');
            wpaicg_generator_process.empty();
            /*Start with custom prompt*/
            if(wpaicg_meta_custom_prompt_enable){
                oldStep = 'custom_content';
                wpaicg_generatorProcess(oldStep,'<?php echo esc_html__('Generating Content','gpt3-ai-content-generator')?>');
            }
            else{
                oldStep = 'heading';
                wpaicg_generatorProcess(oldStep,'<?php echo esc_html__('Generating Headings','gpt3-ai-content-generator')?>');
            }
            $('#wpcgai_load_plugin_settings').attr('disabled','disabled');
            if(!$('#wpcgai_load_plugin_settings .spinner').length) {
                $('#wpcgai_load_plugin_settings').append('<span class="spinner"></span>');
            }
            $('#wpcgai_load_plugin_settings').find('.spinner').css('visibility','unset');
            $('#wpaicg-seo-tab-item').removeClass('wpaicg-has-seo');
            $('#wpaicg-seo-tab-content').removeClass('wpaicg-has-seo');
            startTime = new Date();
            var requestData = data;
            if(wpaicg_meta_custom_prompt_enable){
                requestData.action = 'wpaicg_generate_custom_prompt';
            }
            $.post(ajaxurl, requestData, function(response){
                if(response.status === 'success'){
                    if (wpai_modify_headings == 1 && is_generate_continue == 0 && !wpaicg_meta_custom_prompt_enable) {
                        clearTimeout(window['wpaicgTimer']);
                        var str = response.data;
                        var html = str.split('||');

                        var headings_array = [];
                        $.each(html, function (i, el) {
                            headings_array.push(el);
                        });
                        $.each(headings_array, function (key, value) {
                            value = value.replace("'", "&#39;");
                            var randomnum = Math.floor((Math.random() * 100000) + 1);
                            var itemTemplate = "<li><div>";
                            itemTemplate += "<input type='text' id='text' value='" + value + "' style='width: 90%;'/>";
                            itemTemplate += "<span class='wpcgai_sort_heading'><i class='fa fa-bars'></i></span>";
                            itemTemplate += "<span id='wpcgai_remove_heading'><i class='fa fa-trash-o'></i></span>";
                            itemTemplate += "<div style='display: none;'><span id='identifier'>" + randomnum + "</span>";
                            itemTemplate += "</div>";
                            itemTemplate += "</div></li>";
                            $(".wpcgai_menu_editor").prepend(itemTemplate);
                        });
                        $('#myModal').show();
                        $('.modal-backdrop').show();
                    } else {
                        wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep).addClass('finished');
                        wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep + ' span').html('<?php echo esc_html__('Done','gpt3-ai-content-generator')?>');
                        if(wpaicg_meta_custom_prompt_enable){
                            $('#wpaicg-seo-tab-content').addClass('wpaicg-has-seo');
                            $('#wpcgai_preview_box').val(response.content.replaceAll('\\',''));
                            $('#wpcgai_save_draft_post_action').show();
                            data.action = 'wpaicg_content_generator';
                            data.content = response.content.replaceAll('\\','');
                            data.step = 'seo';
                            data.wpaicg_toc_list = response.tocs;
                            data.wpaicg_heading_tag_modify = response.wpaicg_heading_tag_modify;
                            data.hfHeadings = response.headings;
                            wpaicg_generatorProcess(data.step, '<?php echo esc_html__('Generating SEO','gpt3-ai-content-generator')?>');
                        }
                        else{
                            data.step = 'content';
                            wpaicg_generatorProcess(data.step, '<?php echo esc_html__('Generating Content','gpt3-ai-content-generator')?>');
                            data.hfHeadings = response.data;
                        }
                        data.tokens = parseInt(response.tokens);
                        data.length = parseInt(response.length);
                        oldStep = data.step;
                        wpaicgStepWorking(data, t);
                    }
                }
                else{
                    wpaicg_generator_process.find('.wpaicg-generating-process-'+oldStep).addClass('wpaicg-error');
                    wpaicg_generator_process.find('.wpaicg-generating-process-'+oldStep+' span').html('<?php echo esc_html__('Error','gpt3-ai-content-generator')?>');
                    wpaicg_ShowError(response.msg, t);
                    $('#wpcgai_load_plugin_settings').removeAttr('disabled');
                    $('#wpcgai_load_plugin_settings').find('.spinner').remove();
                }
            }).fail(function (){
                wpaicg_generator_process.find('.wpaicg-generating-process-'+oldStep).addClass('error');
                wpaicg_generator_process.find('.wpaicg-generating-process-'+oldStep+' span').html('<?php echo esc_html__('Error','gpt3-ai-content-generator')?>');
                wpaicg_ShowError('<?php echo esc_html__('The server is currently overloaded with other requests. Sorry about that! You can retry your request, or contact us through our help center at help.openai.com if the error persists..','gpt3-ai-content-generator')?>', t);
                $('#wpcgai_load_plugin_settings').removeAttr('disabled');
                $('#wpcgai_load_plugin_settings').find('.spinner').remove();
            });
        });
        function wpaicgStepWorking(data,timer){
            var wpaicg_image_source = $('#wpaicg_image_source').val();
            var wpaicg_featured_image_source = $('#wpaicg_featured_image_source').val();
            var wpai_language = $("#wpai_language").val();
            var wpai_add_intro = $("#wpai_add_intro").val();
            var wpai_add_conclusion = $("#wpai_add_conclusion").val();
            var wpai_writing_style = $("#wpai_writing_style").val();
            var wpai_writing_tone = $("#wpai_writing_tone").val();
            var wpai_keywords = $("#wpai_keywords").val();
            var wpai_add_keywords_bold = $("#wpai_add_keywords_bold").val();
            var wpai_heading_tag = $("#wpai_heading_tag").val();
            var wpai_words_to_avoid = $("#wpai_words_to_avoid").val();
            var wpai_add_tagline = $("#wpai_add_tagline").val();
            var wpai_add_faq = $("#wpai_add_faq2:checked").length;
            wpai_add_faq = wpai_add_faq ? 1 : 0;
            var wpai_target_url = $("#wpai_target_url").val();
            var wpai_anchor_text = $("#wpai_anchor_text").val();
            var wpai_target_url_cta = $("#wpai_target_url_cta").val();
            var wpai_cta_pos = $("#wpai_cta_pos").val();
            $.post(ajaxurl, data, function(response){
                if(response.status === 'success' || response.status === 'no_image'){
                    data.wpaicg_toc_list = response.tocs;
                    if(response.next_step === 'DONE'){
                        $('#wpcgai_load_plugin_settings').removeAttr('disabled');
                        $('#wpcgai_load_plugin_settings').find('.spinner').remove();
                        if(response.featured_img !== undefined && response.featured_img !== ''){
                            if($('.wpaicg_featured_img_url').length){
                                $('.wpaicg_featured_img_url').val(response.featured_img);
                            }
                            else {
                                $('#wpcgai_save_draft_post_action').parent().append('<input class="wpaicg_featured_img_url" type="hidden" name="wpaicg_featured_img_url" value="' + response.featured_img + '">')
                            }
                        }
                        if(oldStep === 'image' && response.status === 'no_image'){
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep).addClass('wpaicg-error');
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep + ' span').html('<?php echo esc_html__('Rejected','gpt3-ai-content-generator')?>');
                        }
                        else{
                            wpaicg_generator_process.find('.wpaicg-generating-process-'+oldStep).addClass('finished');
                            wpaicg_generator_process.find('.wpaicg-generating-process-'+oldStep+' span').html('<?php echo esc_html__('Done','gpt3-ai-content-generator')?>');
                        }
                        if(oldStep === 'featuredimage' && response.status === 'no_image'){
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep).addClass('wpaicg-error');
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep + ' span').html('<?php echo esc_html__('Rejected','gpt3-ai-content-generator')?>');
                        }
                        else{
                            wpaicg_generator_process.find('.wpaicg-generating-process-'+oldStep).addClass('finished');
                            wpaicg_generator_process.find('.wpaicg-generating-process-'+oldStep+' span').html('<?php echo esc_html__('Done','gpt3-ai-content-generator')?>');
                        }
                        wpaicg_generatorProcess('finished','<?php echo esc_html__('Finished','gpt3-ai-content-generator')?>');
                        $('#wpcgai_preview_box').val(response.content.replaceAll('\\',''));
                        if(response.content !== ''){
                            $('#wpaicg-seo-tab-content').addClass('wpaicg-has-seo');
                        }
                        setTimeout(function(){
                            $('.wpcgai_lds-ellipsis').hide();
                        },3000);
                        if($('.wpaicg_content_changed').length){
                            $('.wpaicg_content_changed').val('true');
                        }
                        else{
                            $('#wpcgai_save_draft_post_action').parent().append('<input class="wpaicg_content_changed" type="hidden" name="wpaicg_content_changed" value="true">')
                        }
                        clearTimeout(window['wpaicgTimer']);
                        var endTime = new Date();
                        var timeDiff = endTime - startTime;
                        timeDiff = timeDiff/1000;
                        if($('.wpaicg_duration').length){
                            $('.wpaicg_duration').val(timeDiff);
                        }
                        else{
                            $('#wpcgai_save_draft_post_action').parent().append('<input class="wpaicg_duration" type="hidden" name="duration" value="'+timeDiff+'">');
                        }
                        if ($('.wpaicg_word_count').length){
                            $('.wpaicg_word_count').val(response.length)
                        }
                        else {
                            $('#wpcgai_save_draft_post_action').parent().append('<input class="wpaicg_word_count" type="hidden" name="word_count" value="' + response.length + '">');
                        }
                        if($('.wpaicg_usage_token').length){
                            $('.wpaicg_usage_token').val(response.tokens);
                        }
                        else{
                            $('#wpcgai_save_draft_post_action').parent().append('<input class="wpaicg_usage_token" type="hidden" name="usage_token" value="'+response.tokens+'">');
                        }
                        $('#wpcgai_save_draft_post_action').show();
                    }
                    else{
                        if(oldStep === 'content'){
                            $('#wpcgai_preview_box').val(response.content.replaceAll('\\',''));
                            $('#wpcgai_save_draft_post_action').show();
                        }
                        if(oldStep === 'image' && response.status === 'no_image'){
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep).addClass('wpaicg-error');
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep + ' span').html('<?php echo esc_html__('No Img','gpt3-ai-content-generator')?>');
                        }
                        else {
                            if(response.img !== undefined && response.img !== ''){
                                if($('.wpaicg_image_url').length){
                                    $('.wpaicg_image_url').val(response.img);
                                }
                                else{
                                    $('#wpcgai_save_draft_post_action').parent().append('<input class="wpaicg_image_url" type="hidden" name="wpaicg_image_url" value="'+response.img+'">')
                                }
                            }
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep).addClass('finished');
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep + ' span').html('<?php echo esc_html__('Done','gpt3-ai-content-generator')?>');
                        }
                        if(oldStep === 'featuredimage' && response.status === 'no_image'){
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep).addClass('wpaicg-error');
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep + ' span').html('<?php echo esc_html__('No Img','gpt3-ai-content-generator')?>');
                        }
                        else {
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep).addClass('finished');
                            wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep + ' span').html('<?php echo esc_html__('Done','gpt3-ai-content-generator')?>');
                        }
                        if(response.description !== ''){
                            $('#wpaicg-seo-tab-item').addClass('wpaicg-has-seo');
                            $('#wpaicg-meta-description').val(response.description);
                        }
                        data.tokens = parseInt(response.tokens);
                        data.length = parseInt(response.length);
                        data.step = response.next_step;
                        if(wpai_add_intro == 1 && data.step == 'intro'){
                            wpaicg_generatorProcess('intro','<?php echo esc_html__('Generating Introduction','gpt3-ai-content-generator')?>');
                        }
                        if(wpai_add_faq == 1 && data.step == 'faq'){
                            wpaicg_generatorProcess('faq','<?php echo esc_html__('Generating Q&A','gpt3-ai-content-generator')?>');
                        }
                        if(wpai_add_conclusion == 1 && data.step == 'conclusion'){
                            wpaicg_generatorProcess('conclusion','<?php echo esc_html__('Generating Conclusion','gpt3-ai-content-generator')?>');
                        }
                        if(wpai_add_tagline == 1 && data.step == 'tagline'){
                            wpaicg_generatorProcess('tagline','<?php echo esc_html__('Generating Tagline','gpt3-ai-content-generator')?>');
                        }
                        if(data.step == 'seo'){
                            wpaicg_generatorProcess('seo','<?php echo esc_html__('Generating SEO','gpt3-ai-content-generator')?>');
                        }
                        if(data.step == 'addition'){
                            wpaicg_generatorProcess('addition','<?php echo esc_html__('Applying Changes','gpt3-ai-content-generator')?>');
                        }
                        if(data.step === 'image'){
                            if(wpaicg_image_source === 'dalle'){
                                wpaicg_generatorProcess('image','<?php echo esc_html__('Generating DALL-E Image','gpt3-ai-content-generator')?>');
                            }
                            if(wpaicg_image_source === 'pexels'){
                                wpaicg_generatorProcess('image','<?php echo esc_html__('Getting Image from Pexels','gpt3-ai-content-generator')?>');
                            }
                            if(wpaicg_image_source === 'pixabay'){
                                wpaicg_generatorProcess('image','<?php echo esc_html__('Getting Image from Pixabay','gpt3-ai-content-generator')?>');
                            }
                        }
                        if(data.step === 'featuredimage'){
                            if(wpaicg_featured_image_source === 'dalle'){
                                wpaicg_generatorProcess('featuredimage','<?php echo esc_html__('Generating DALL-E Featured Image','gpt3-ai-content-generator')?>');
                            }
                            if(wpaicg_featured_image_source === 'pexels'){
                                wpaicg_generatorProcess('featuredimage','<?php echo esc_html__('Getting Featured Image from Pexels','gpt3-ai-content-generator')?>');
                            }
                            if(wpaicg_featured_image_source === 'pixabay'){
                                wpaicg_generatorProcess('featuredimage','<?php echo esc_html__('Getting Featured Image from Pixabay','gpt3-ai-content-generator')?>');
                            }
                        }
                        oldStep = data.step;
                        data.generated_img = response.img;
                        data.featured_img = response.featured_img;
                        data.content = response.content.replaceAll('\\','');
                        wpaicgStepWorking(data, timer)
                    }
                }
                else{
                    if(oldStep === 'seo'){
                        data.step = 'addition';
                        wpaicg_generator_process.append('<div class="wpaicg-error-msg">'+response.msg+'</div>');
                        wpaicgStepWorking(data, timer);
                    }
                    else {
                        if (oldStep !== 'heading' && oldStep !== 'content') {
                            if (response.description !== '') {
                                $('#wpaicg-seo-tab-item').addClass('wpaicg-has-seo');
                                $('#wpaicg-meta-description').val(response.description);
                            }
                            if (response.content !== '') {
                                $('#wpaicg-seo-tab-content').addClass('wpaicg-has-seo');
                                $('#wpcgai_preview_box').val(response.content.replaceAll('\\', ''));
                                if($('.wpaicg_content_changed').length){
                                    $('.wpaicg_content_changed').val('true');
                                }
                                else {
                                    $('#wpcgai_save_draft_post_action').parent().append('<input class="wpaicg_content_changed" type="hidden" name="wpaicg_content_changed" value="true">');
                                }
                            }
                            if (response.img !== undefined && response.img !== '') {
                                if($('.wpaicg_image_url').length){
                                    $('.wpaicg_image_url').val(response.img);
                                }
                                else {
                                    $('#wpcgai_save_draft_post_action').parent().append('<input class="wpaicg_image_url" type="hidden" name="wpaicg_image_url" value="' + response.img + '">')
                                }
                            }
                            if (response.featured_img !== undefined && response.featured_img !== '') {
                                if($('.wpaicg_featured_img_url').length){
                                    $('.wpaicg_featured_img_url').val(response.featured_img);
                                }
                                else {
                                    $('#wpcgai_save_draft_post_action').parent().append('<input class="wpaicg_featured_img_url" type="hidden" name="wpaicg_featured_img_url" value="' + response.featured_img + '">')
                                }
                            }
                        }
                        wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep).addClass('wpaicg-error');
                        wpaicg_generator_process.find('.wpaicg-generating-process-' + oldStep + ' span').html('<?php echo esc_html__('Error','gpt3-ai-content-generator')?>');
                        wpaicg_ShowError(response.msg, timer);
                        $('#wpcgai_load_plugin_settings').removeAttr('disabled');
                        $('#wpcgai_load_plugin_settings').find('.spinner').remove();
                    }
                }
            }).fail(function (){
                wpaicg_generator_process.find('.wpaicg-generating-process-'+oldStep).addClass('wpaicg-error');
                wpaicg_generator_process.find('.wpaicg-generating-process-'+oldStep+' span').html('<?php echo esc_html__('Error','gpt3-ai-content-generator')?>');
                wpaicg_ShowError('<?php echo esc_html__('It appears that your web server has some kind of timeout limit. This can also occur if you are using a VPS, CDN, proxy, firewall, or Cloudflare. To resolve this issue, please contact your hosting provider and request an increase in the timeout limit. Additionally you can enable Custom Prompts for faster response to avoid timeout.','gpt3-ai-content-generator')?>', timer);
                $('#wpcgai_load_plugin_settings').removeAttr('disabled');
                $('#wpcgai_load_plugin_settings').find('.spinner').remove();
            });
        }
        jQuery('#wpcgai_save_draft_post_action').click(function(){
            var wpaicg_draft_btn = jQuery(this);
            var title = jQuery('#wpai_preview_title').val();
            var content = jQuery('#wpcgai_preview_box').val();
            if(title === ''){
                alert('<?php echo esc_html__('Please enter title','gpt3-ai-content-generator')?>');
            }
            else if(content === ''){
                alert('<?php echo esc_html__('Please wait content generated','gpt3-ai-content-generator')?>')
            }
            else {
                var data = $('#wpaicg-post-form select').serialize()+'&'+$('#wpaicg-post-form input').serialize()+'&'+$('#wpaicg-post-form textarea').serialize();
                data = data+'&title='+title+'&content='+content+'&action=wpaicg_save_draft_post_extra&nonce=<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>';
                if($('#post_ID').length){
                    data += '&post_id='+$('#post_ID').val();
                }
                // var data_json = URLSearchParams2JSON_2(data);
                jQuery.ajax({
                    url: '<?php echo  admin_url( 'admin-ajax.php' ) ;?>',
                    data: data,
                    dataType: 'html',
                    type: 'POST',
                    beforeSend: function (){
                        wpaicg_draft_btn.attr('disabled','disabled');
                        wpaicg_draft_btn.append('<span class="spinner"></span>');
                        wpaicg_draft_btn.find('.spinner').css('visibility','unset');
                    },
                    success: function (res){
                        res = res.replace('postrevision{','{');
                        res = res.replace('revisionrevision{','{');
                        res = res.replace('postrevisionrevision{','{');
                        res = res.replace('postrevisionrevisionrevision{','{');
                        res = res.replace('postrevisionrevisionrevisionrevision{','{');
                        res = res.replaceAll('revision{','{');
                        res = res.replaceAll('post{','{');
                        res = JSON.parse(res);
                        wpaicg_draft_btn.removeAttr('disabled');
                        wpaicg_draft_btn.find('.spinner').remove();
                        if(res.status === 'success'){
                            window.location.href = '<?php echo  admin_url( 'post.php' ) ;?>?post='+res.id+'&action=edit';
                        }
                        else{
                            alert(res.msg);
                        }
                    },
                    error: function (){
                        wpaicg_draft_btn.removeAttr('disabled');
                        wpaicg_draft_btn.find('.spinner').remove();
                        alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                    }
                });
            }
        });
        $('.wpaicg_meta_custom_prompt_enable').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_meta_custom_prompt_box').show();
            }
            else{
                $('.wpaicg_meta_custom_prompt_box').hide();
            }
        });
        $('.wpaicg_meta_custom_prompt_reset').click(function (){
            let prompt = $(this).attr('data-prompt');
            $('.wpaicg_meta_custom_prompt').val(prompt);
            $('.wpaicg_meta_custom_prompt_auto_error').empty();
            $('#wpcgai_load_plugin_settings').removeAttr('disabled');
        });
        <?php
        if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
        ?>
        $('.wpaicg_meta_custom_prompt').on('input', function (e){
            let prompt = $(e.currentTarget).val();
            if(prompt.indexOf('[keywords_to_include]') > -1 || prompt.indexOf('[keywords_to_avoid]') > -1){
                $('.wpaicg_meta_custom_prompt_auto_error').html('<div style="color: #f00"><?php echo esc_html__('Please note that keywords are only available in pro plan. Please remove keywords from your prompt','gpt3-ai-content-generator')?></div>');
                $('#wpcgai_load_plugin_settings').attr('disabled','disabled');
            }
            else{
                $('.wpaicg_meta_custom_prompt_auto_error').empty();
                $('#wpcgai_load_plugin_settings').removeAttr('disabled');
            }
        });
        <?php
        endif;
        ?>
    });

</script>
