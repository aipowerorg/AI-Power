<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<style>
    .wpaicg-form-field{
        display: flex;
        margin-bottom: 10px;
        align-items: center;
    }
    .wpaicg-form-field label{
        display: block;
        margin-right: 5px;
        width: 150px;
    }
    .wpaicg-form-field .regular-text{
        width: calc(100% - 150px);
    }
    .wpaicg-custom-parameters h3{
        margin: 0;
        background: #f1f1f1;
        padding: 10px;
        border-bottom: 1px solid #ccc;
    }
    .wpaicg-custom-parameters{
        background: #fff;
        border: 1px solid #ccc;
    }
    .wpaicg-custom-parameters-content{
        padding: 10px;
    }
    .wpaicg-custom-template-row{
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }
    .wpaicg_template_title_result{
        font-size: 16px;
        margin-bottom: 10px;
        font-weight: bold;
        border-radius: 4px;
        background: #e1e1e1;
        border: 1px solid #ccc;
        padding: 6px 12px;
    }
    .wpaicg_template_generate_stop{
        margin-left: 5px!important;
    }
    .wpaicg_modal{
        width: 600px;
        left: calc(50% - 300px)
    }
</style>
<form action="" method="post" class="wpaicg_custom_template_form">
    <?php
    wp_nonce_field('wpaicg_custom_mode_generator');
    ?>
    <div class="wpaicg-grid-three" style="margin-top: 20px;">
        <div class="wpaicg-grid-2">
            <div class="wpaicg-mb-10">
                <div class="mb-5" style="height:30px;display: flex;justify-content: space-between;align-items: center">
                    <div>
                        <label><input name="template[type]" checked type="radio" class="wpaicg_custom_template_type_topic" value="topic">&nbsp;<strong><?php echo esc_html__('Topic','gpt3-ai-content-generator')?></strong></label>
                        &nbsp;&nbsp;&nbsp;<label><input name="template[type]" class="wpaicg_custom_template_type_title" type="radio" value="title">&nbsp;<strong><?php echo esc_html__('Use My Own Title','gpt3-ai-content-generator')?></strong></label>
                    </div>
                    <div class="wpaicg-custom-template-row wpaicg_custom_template_row_type">
                        #<?php echo esc_html__('of titles','gpt3-ai-content-generator')?>&nbsp;
                        <select class="wpaicg_custom_template_title_count" name="title_count">
                            <option value="3">3</option>
                            <option selected value="5">5</option>
                            <option value="7">7</option>
                        </select>
                        &nbsp;
                        <button class="button button-primary wpaicg_template_generate_titles" type="button"><?php echo esc_html__('Suggest Titles','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <div class="wpaicg_custom_template_add_topic">
                    <div class="mb-5">
                        <input class="wpaicg_template_topic" type="text" style="width: 100%" placeholder="<?php echo esc_html__('Topic: e.g. Mobile Phones','gpt3-ai-content-generator')?>">
                    </div>
                </div>
                <div class="wpaicg_custom_template_add_title" style="display: none">
                    <div class="mb-5">
                        <input type="text" class="wpaicg_template_title_field" style="width: 100%" placeholder="<?php echo esc_html__('Please enter a title','gpt3-ai-content-generator')?>">
                    </div>
                </div>
            </div>
            <div class="wpaicg_template_title_result" style="display: none"></div>
            <div class="wpaicg-mb-10">
                <div class="mb-5" style="display: flex;justify-content: space-between;align-items: center">
                    <strong><?php echo esc_html__('Sections','gpt3-ai-content-generator')?></strong>
                    <div class="wpaicg-custom-template-row">
                        #<?php echo esc_html__('of sections','gpt3-ai-content-generator')?>&nbsp;
                        <select class="wpaicg_custom_template_section_count" name="section_count">
                            <?php
                            for($i = 1; $i < 13;$i++){
                                if($i%2 == 0) {
                                    echo '<option'.($i == 4 ? ' selected' : '').' value="' . esc_html($i) . '">' . esc_html($i) . '</option>';
                                }
                            }
                            ?>
                        </select>
                        &nbsp;
                        <button class="button button-primary wpaicg_template_generate_sections" type="button" disabled><?php echo esc_html__('Generate Sections','gpt3-ai-content-generator')?></button>
                        <button class="button button-link-delete wpaicg_template_generate_stop" data-type="section" type="button" style="display: none"><?php echo esc_html__('Stop','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <div class="mb-5">
                    <textarea class="wpaicg_template_section_result" rows="5"></textarea>
                </div>
            </div>
            <div class="wpaicg-mb-10">
                <div class="mb-5" style="display: flex;justify-content: space-between;align-items: center">
                    <strong><?php echo esc_html__('Content','gpt3-ai-content-generator')?></strong>
                    <div class="wpaicg-custom-template-row">
                        #<?php echo esc_html__('of Paragraph per Section','gpt3-ai-content-generator')?>&nbsp;
                        <select class="wpaicg_custom_template_paragraph_count" name="paragraph_count">
                            <?php
                            for($i = 1; $i < 11;$i++){
                                echo '<option'.($i == 4 ? ' selected' : '').' value="' . esc_html($i) . '">' . esc_html($i) . '</option>';
                            }
                            ?>
                        </select>
                        &nbsp;
                        <button class="button button-primary wpaicg_template_generate_content" type="button" disabled><?php echo esc_html__('Generate Content','gpt3-ai-content-generator')?></button>
                        <button class="button button-link-delete wpaicg_template_generate_stop" data-type="content" type="button" style="display: none"><?php echo esc_html__('Stop','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <div class="mb-5">
                    <textarea class="wpaicg_template_content_result" rows="15"></textarea>
                </div>
            </div>
            <div class="wpaicg-mb-10">
                <div class="mb-5" style="display: flex;justify-content: space-between;align-items: center">
                    <strong><?php echo esc_html__('Excerpt','gpt3-ai-content-generator')?></strong>
                    <div class="wpaicg-custom-template-row">
                        <button class="button button-primary wpaicg_template_generate_excerpt" type="button" disabled><?php echo esc_html__('Generate Excerpt','gpt3-ai-content-generator')?></button>
                        <button class="button button-link-delete wpaicg_template_generate_stop" data-type="excerpt" type="button" style="display: none"><?php echo esc_html__('Stop','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <div class="mb-5">
                    <textarea class="wpaicg_template_excerpt_result" rows="5"></textarea>
                </div>
            </div>
            <div class="wpaicg-mb-10">
                <div class="mb-5" style="display: flex;justify-content: space-between;align-items: center">
                    <strong><?php echo esc_html__('Meta Description','gpt3-ai-content-generator')?></strong>
                    <div class="wpaicg-custom-template-row">
                        <button class="button button-primary wpaicg_template_generate_meta" type="button" disabled><?php echo esc_html__('Generate Meta','gpt3-ai-content-generator')?></button>
                        <button class="button button-link-delete wpaicg_template_generate_stop" data-type="meta" type="button" style="display: none"><?php echo esc_html__('Stop','gpt3-ai-content-generator')?></button>
                    </div>
                </div>
                <div class="mb-5">
                    <textarea class="wpaicg_template_meta_result" rows="5"></textarea>
                </div>
            </div>
            <div class="">
                <button type="button" class="button button-primary wpaicg_template_save_post" style="display: none;width: 100%"><?php echo esc_html__('Create Post','gpt3-ai-content-generator')?></button>
            </div>
        </div>
        <div class="wpaicg-grid-1">
            <div class="wpaicg-custom-parameters">
                <?php
                include __DIR__.'/wpaicg_custom_model_template.php';
                ?>
            </div>
        </div>
    </div>
</form>
<script>
    jQuery(document).ready(function ($){
        let wpaicg_custom_template_form = $('.wpaicg_custom_template_form');
        let wpaicg_template_topic = $('.wpaicg_template_topic');
        let wpaicg_template_generate_titles = $('.wpaicg_template_generate_titles');
        let wpaicg_custom_template_title_count = $('.wpaicg_custom_template_title_count');
        let wpaicg_custom_template_model = $('.wpaicg_custom_template_model');
        let wpaicg_template_title_result = $('.wpaicg_template_title_result');
        let wpaicg_template_section_result = $('.wpaicg_template_section_result');
        let wpaicg_custom_template_section_count = $('.wpaicg_custom_template_section_count');
        let wpaicg_template_generate_sections = $('.wpaicg_template_generate_sections');
        let wpaicg_template_content_result = $('.wpaicg_template_content_result');
        let wpaicg_custom_template_paragraph_count = $('.wpaicg_custom_template_paragraph_count');
        let wpaicg_template_generate_content = $('.wpaicg_template_generate_content');
        let wpaicg_template_excerpt_result = $('.wpaicg_template_excerpt_result');
        let wpaicg_template_generate_excerpt = $('.wpaicg_template_generate_excerpt');
        let wpaicg_template_meta_result = $('.wpaicg_template_meta_result');
        let wpaicg_template_generate_meta = $('.wpaicg_template_generate_meta');
        let wpaicg_template_save_post = $('.wpaicg_template_save_post');
        let wpaicg_template_title_field = $('.wpaicg_template_title_field');
        let wpaicg_template_ajax_url = '<?php echo admin_url('admin-ajax.php')?>';
        let wpaicg_template_generate_stop = $('.wpaicg_template_generate_stop');
        let wpaicg_custom_template_add_topic = $('.wpaicg_custom_template_add_topic');
        let wpaicg_custom_template_add_title = $('.wpaicg_custom_template_add_title');
        let wpaicg_custom_template_type_topic = $('.wpaicg_custom_template_type_topic');
        let wpaicg_custom_template_type_title = $('.wpaicg_custom_template_type_title');
        let wpaicg_custom_template_row_type = $('.wpaicg_custom_template_row_type');
        let wpaicg_tokens = 0;
        let wpaicg_words_count = 0;
        let wpaicg_duration = 0;
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
        wpaicg_template_generate_stop.click(function (){
            let type = $(this).attr('data-type');
            window['wpaicg_template_generator_'+type].abort();
            $(this).hide();
            wpaicgRmLoading($(this).parent().find('.button-primary'));
        });
        wpaicg_custom_template_type_topic.click(function (){
            wpaicg_custom_template_add_title.hide();
            wpaicg_custom_template_add_topic.show();
            wpaicg_custom_template_row_type.show();
        });
        wpaicg_custom_template_type_title.click(function (){
            wpaicg_custom_template_add_title.show();
            wpaicg_custom_template_add_topic.hide();
            wpaicg_custom_template_row_type.hide();
            wpaicg_template_title_result.hide();
        });
        $(document).on('change','.wpaicg_custom_template_select', function (e){
            let selection = $(e.currentTarget);
            wpaicg_custom_template_title_count.val(3);
            wpaicg_custom_template_section_count.val(2);
            wpaicg_custom_template_paragraph_count.val(1);
            let val = parseFloat(selection.val());
            let selected = selection.find('option:selected');
            let parameters = selected.attr('data-parameters');
            parameters = JSON.parse(parameters);
            console.log(val);
            if(val > 0){
                $('.wpaicg_custom_template_title').val(selected.text().trim());
                $('.wpaicg_custom_template_title').after('<input class="wpaicg_custom_template_id" type="hidden" name="id" value="'+val+'">');
                $('.wpaicg_template_update').show();
                $('.wpaicg_template_delete').show();
                $('.wpaicg_template_delete').attr('data-id',val);
            }
            else{
                $('.wpaicg_template_delete').hide();
                $('.wpaicg_template_update').hide();
                $('.wpaicg_custom_template_id').remove();
                $('.wpaicg_custom_template_title').val('');
            }
            $.each(parameters, function (key, item){
                $('.wpaicg_custom_template_'+key).val(item);
            })
        });
        wpaicg_template_title_field.on('input', function (){
            let val = wpaicg_template_title_field.val();
            if(val !== ''){
                wpaicg_template_generate_sections.removeAttr('disabled');
                wpaicg_template_generate_meta.removeAttr('disabled');
                wpaicg_template_generate_excerpt.removeAttr('disabled');
            }
            else{
                wpaicg_template_generate_sections.attr('disabled','disabled');
                wpaicg_template_generate_meta.attr('disabled','disabled');
                wpaicg_template_generate_excerpt.attr('disabled','disabled');
            }
        })
        $(document).on('keypress','.wpaicg_custom_template_temperature,.wpaicg_custom_template_frequency_penalty,.wpaicg_custom_template_presence_penalty,.wpaicg_custom_template_max_tokens,.wpaicg_custom_template_top_p,.wpaicg_custom_template_best_of', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
        });
        $('.wpaicg_modal_close').click(function (){
            wpaicgRmLoading(wpaicg_template_generate_titles);
        })
        $(document).on('click','.wpaicg_template_delete',function (){
            let con = confirm('<?php echo esc_html__('Are you sure?','gpt3-ai-content-generator')?>');
            let id = $('.wpaicg_template_delete').attr('data-id');
            if(con) {
                $.ajax({
                    url: wpaicg_template_ajax_url,
                    data: {action: 'wpaicg_template_delete', id: id,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function () {
                        wpaicgLoading($('.wpaicg_template_delete'))
                    },
                    success: function (res) {
                        if (res.status === 'success') {
                            alert('<?php echo esc_html__('Template successfully deleted.','gpt3-ai-content-generator')?>')
                            window.location.reload();
                        } else alert(res.msg);
                    }
                })
            }

        })
        $(document).on('click','.wpaicg_template_use_title', function (e){
            let btn = $(e.currentTarget);
            let title = btn.closest('.wpaicg-regenerate-title').find('input').val();
            if(title === ''){
                alert('<?php echo esc_html__('Please choose correct title','gpt3-ai-content-generator')?>');
            }
            else{
                $('.wpaicg_modal_content').empty();
                $('.wpaicg-overlay').hide();
                $('.wpaicg_modal').hide();
                wpaicg_template_title_field.val(title);
                wpaicg_template_title_result.html('Title: '+title);
                wpaicg_template_title_result.show();
                wpaicg_template_generate_sections.removeAttr('disabled');
                wpaicg_template_generate_meta.removeAttr('disabled');
                wpaicg_template_generate_excerpt.removeAttr('disabled');
            }
        })
        // Generator Title
        wpaicg_template_generate_titles.click(function (){
            wpaicg_tokens = 0;
            wpaicg_words_count = 0;
            let topic = wpaicg_template_topic.val();
            if(topic === ''){
                alert('<?php echo esc_html__('Please enter a topic','gpt3-ai-content-generator')?>');
            }
            else{
                wpaicg_duration = new Date();
                wpaicg_template_generate_sections.attr('disabled','disabled');
                wpaicg_template_section_result.val('');
                wpaicg_template_title_result.empty();
                wpaicg_template_title_result.hide();
                wpaicg_template_generate_content.attr('disabled','disabled');
                wpaicg_template_content_result.val('');
                wpaicg_template_generate_excerpt.attr('disabled','disabled');
                wpaicg_template_excerpt_result.val('');
                wpaicg_template_generate_meta.attr('disabled','disabled');
                wpaicg_template_meta_result.val('');
                wpaicg_template_save_post.hide();
                let data = wpaicg_custom_template_form.serialize();
                data += '&action=wpaicg_template_generator&step=titles&topic='+topic;
                $.ajax({
                    url: wpaicg_template_ajax_url,
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function (){
                        wpaicgLoading(wpaicg_template_generate_titles);
                        $('.wpaicg_modal_content').empty();
                        $('.wpaicg-overlay').show();
                        $('.wpaicg_modal').show();
                        $('.wpaicg_modal_title').html('AI Power - <?php echo esc_html__('Title Suggestion Tool','gpt3-ai-content-generator')?>');
                        $('.wpaicg_modal_content').html('<p style="font-style: italic;margin-top: 5px;text-align: center;"><?php echo esc_html__('Preparing title suggestions...','gpt3-ai-content-generator')?></p>');
                    },
                    success: function (res){
                        wpaicgRmLoading(wpaicg_template_generate_titles);
                        if(res.status === 'success'){
                            var html = '';
                            wpaicg_tokens += parseFloat(res.tokens);
                            wpaicg_words_count += parseFloat(res.words);
                            if(res.data.length){
                                $.each(res.data, function (idx, item){
                                    html += '<div class="wpaicg-regenerate-title"><input type="text" value="'+item+'"><button class="button button-primary wpaicg_template_use_title"><?php echo esc_html__('Use','gpt3-ai-content-generator')?></button></div>';
                                })
                                $('.wpaicg_modal_content').html(html);
                            }
                            else{
                                $('.wpaicg_modal_content').html('<p style="color: #f00;margin-top: 5px;text-align: center;"><?php echo esc_html__('No result','gpt3-ai-content-generator')?></p>');
                            }
                        }
                        else{
                            alert(res.msg);
                        }
                    }
                })
            }
        });
        // Generator Sections
        wpaicg_template_generate_sections.click(function (){
            let title = wpaicg_template_title_field.val();
            if(title === ''){
                alert('Please generate title first');
            }
            else{
                let btnStop = $(this).parent().find('.wpaicg_template_generate_stop');
                wpaicg_template_section_result.val('');
                wpaicg_template_generate_content.attr('disabled','disabled');
                wpaicg_template_content_result.val('');
                wpaicg_template_generate_excerpt.attr('disabled','disabled');
                wpaicg_template_excerpt_result.val('');
                wpaicg_template_generate_meta.attr('disabled','disabled');
                wpaicg_template_meta_result.val('');
                let data = wpaicg_custom_template_form.serialize();
                data += '&action=wpaicg_template_generator&step=sections&post_title='+title;
                window['wpaicg_template_generator_section'] = $.ajax({
                    url: wpaicg_template_ajax_url,
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function (){
                        wpaicgLoading(wpaicg_template_generate_sections);
                        btnStop.show();
                    },
                    success: function (res){
                        wpaicgRmLoading(wpaicg_template_generate_sections);
                        btnStop.hide();
                        wpaicg_tokens += parseFloat(res.tokens);
                        wpaicg_words_count += parseFloat(res.words);
                        if(res.status === 'success'){
                            if(res.data.length){
                                $.each(res.data, function (idx, item){
                                    let section_result = wpaicg_template_section_result.val();
                                    wpaicg_template_section_result.val(section_result+(idx === 0 ? '' : "\n")+'## '+item);
                                });
                                wpaicg_template_generate_content.removeAttr('disabled');
                            }
                            else{
                                alert('No result');
                            }
                        }
                        else {
                            alert(res.msg);
                        }
                    }
                });
            }
        });
        // Generator Post Content
        wpaicg_template_generate_content.click(function (){
            let sections = wpaicg_template_section_result.val();
            let title = wpaicg_template_title_field.val();
            if(title === ''){
                alert('<?php echo esc_html__('Please generate title first','gpt3-ai-content-generator')?>');
            }
            else if(sections === ''){
                alert('<?php echo esc_html__('Please generate sections first','gpt3-ai-content-generator')?>');
            }
            else{
                let btnStop = $(this).parent().find('.wpaicg_template_generate_stop');
                wpaicg_template_save_post.hide();
                wpaicg_template_content_result.val('');
                wpaicg_template_excerpt_result.val('');
                wpaicg_template_meta_result.val('');
                let data = wpaicg_custom_template_form.serialize();
                data += '&action=wpaicg_template_generator&step=content&post_title='+title+'&sections='+sections;
                window['wpaicg_template_generator_content'] = $.ajax({
                    url: wpaicg_template_ajax_url,
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function (){
                        btnStop.show();
                        wpaicgLoading(wpaicg_template_generate_content);
                    },
                    success: function (res){
                        btnStop.hide();
                        wpaicgRmLoading(wpaicg_template_generate_content);
                        if(res.status === 'success'){
                            wpaicg_tokens += parseFloat(res.tokens);
                            wpaicg_words_count += parseFloat(res.words);
                            if(typeof res.data !== "undefined" && res.data !== ''){
                                wpaicg_template_content_result.val(res.data);
                                wpaicg_template_save_post.show();
                                wpaicg_template_generate_meta.removeAttr('disabled');
                                wpaicg_template_generate_excerpt.removeAttr('disabled');
                            }
                            else{
                                alert('<?php echo esc_html__('No result','gpt3-ai-content-generator')?>')
                            }
                        }
                        else{
                            alert(res.msg);
                        }
                    }
                });
            }
        });
        // Generator Excerpt
        wpaicg_template_generate_excerpt.click(function (){
            let title = wpaicg_template_title_field.val();
            if(title === ''){
                alert('Please generate title first');
            }
            else{
                let btnStop = $(this).parent().find('.wpaicg_template_generate_stop');
                wpaicg_template_excerpt_result.val('');
                let data = wpaicg_custom_template_form.serialize();
                data += '&action=wpaicg_template_generator&step=excerpt&post_title='+title;
                window['wpaicg_template_generator_excerpt'] = $.ajax({
                    url: wpaicg_template_ajax_url,
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function (){
                        btnStop.show();
                        wpaicgLoading(wpaicg_template_generate_excerpt);
                    },
                    success: function (res){
                        btnStop.hide();
                        wpaicgRmLoading(wpaicg_template_generate_excerpt);
                        if(res.status === 'success'){
                            wpaicg_tokens += parseFloat(res.tokens);
                            wpaicg_words_count += parseFloat(res.words);
                            if(typeof res.data !== "undefined" && res.data !== ''){
                                wpaicg_template_excerpt_result.val(res.data);
                            }
                            else{
                                alert('<?php echo esc_html__('No result','gpt3-ai-content-generator')?>')
                            }
                        }
                        else{
                            alert(res.msg);
                        }
                    }
                });
            }
        });
        // Generator Meta
        wpaicg_template_generate_meta.click(function (){
            let title = wpaicg_template_title_field.val();
            if(title === ''){
                alert('<?php echo esc_html__('Please generate title first','gpt3-ai-content-generator')?>');
            }
            else{
                let btnStop = $(this).parent().find('.wpaicg_template_generate_stop');
                wpaicg_template_meta_result.val('');
                let data = wpaicg_custom_template_form.serialize();
                data += '&action=wpaicg_template_generator&step=meta&post_title='+title;
                window['wpaicg_template_generator_meta'] = $.ajax({
                    url: wpaicg_template_ajax_url,
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function (){
                        btnStop.show();
                        wpaicgLoading(wpaicg_template_generate_meta);
                    },
                    success: function (res){
                        btnStop.hide();
                        wpaicgRmLoading(wpaicg_template_generate_meta);
                        if(res.status === 'success'){
                            wpaicg_tokens += parseFloat(res.tokens);
                            wpaicg_words_count += parseFloat(res.words);
                            if(typeof res.data !== "undefined" && res.data !== ''){
                                wpaicg_template_meta_result.val(res.data);
                            }
                            else{
                                alert('<?php echo esc_html__('No result','gpt3-ai-content-generator')?>')
                            }
                        }
                        else{
                            alert(res.msg);
                        }
                    }
                });
            }
        });
        wpaicg_template_save_post.click(function (){
            let title = wpaicg_template_title_field.val();
            let content = wpaicg_template_content_result.val();
            let excerpt = wpaicg_template_excerpt_result.val();
            let description = wpaicg_template_meta_result.val();
            let post_type = $('.wpaicg_custom_template_post_type').val();
            if(title === ''){
                alert('<?php echo esc_html__('Please generate title first','gpt3-ai-content-generator')?>');
            }
            else if(content === ''){
                alert('<?php echo esc_html__('Please generate content first','gpt3-ai-content-generator')?>');
            }
            else{
                let endTime = new Date();
                let duration = (endTime - wpaicg_duration)/1000;
                let model = wpaicg_custom_template_model.val();
                $.ajax({
                    url: wpaicg_template_ajax_url,
                    data: {action: 'wpaicg_template_post',post_type: post_type, model: model,duration: duration, title: title, excerpt: excerpt, content: content, description: description, tokens:wpaicg_tokens, words: wpaicg_words_count,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function () {
                        wpaicgLoading(wpaicg_template_save_post);
                    },
                    success: function (res) {
                        wpaicgRmLoading(wpaicg_template_save_post);
                        if(res.status === 'success'){
                            window.location.href = '<?php echo admin_url('post.php?action=edit&post=')?>'+res.id;
                        }
                        else{
                            alert(res.msg);
                        }
                    }
                });
            }
        });
        function wpaicgSaveTemplate(e){
            let btn = $(e.currentTarget);
            let name = $('.wpaicg_custom_template_title').val();
            let has_error = false;
            let temperature = $('.wpaicg_custom_template_temperature').val();
            let model = $('.wpaicg_custom_template_model').val();
            let top_p = $('.wpaicg_custom_template_top_p').val();
            let max_tokens = $('.wpaicg_custom_template_max_tokens').val();
            let best_of = $('.wpaicg_custom_template_best_of').val();
            let frequency_penalty = $('.wpaicg_custom_template_frequency_penalty').val();
            let presence_penalty = $('.wpaicg_custom_template_presence_penalty').val();
            if(name === ''){
                has_error = '<?php echo esc_html__('Please enter a template name','gpt3-ai-content-generator')?>';
            }
            if(!has_error && (temperature > 1 || temperature < 0)){
                has_error = '<?php echo sprintf(esc_html__('Please enter a valid temperature value between %d and %d.','gpt3-ai-content-generator'),0,1)?>';
            }
            if(!has_error && (best_of > 20 || best_of < 0)){
                has_error = '<?php echo sprintf(esc_html__('Please enter a valid best of value between %d and %d.','gpt3-ai-content-generator'),0,20)?>';
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
            if(has_error){
                alert(has_error);
            }
            else{
                let data = wpaicg_custom_template_form.serialize();
                data += '&action=wpaicg_save_template';
                $.ajax({
                    url: wpaicg_template_ajax_url,
                    data:data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function (){
                        wpaicgLoading(btn)
                    },
                    success: function (res){
                        if(res.status === 'success'){
                            $('.wpaicg-custom-parameters').html(res.setting);
                            if($('.wpaicg_custom_template_id').length && $('.wpaicg_custom_template_id') !== ''){
                                alert('<?php echo esc_html__('Template successfully updated.','gpt3-ai-content-generator')?>');
                                wpaicgRmLoading(btn);
                            }
                            else{
                                alert('<?php echo esc_html__('Create new template successfully','gpt3-ai-content-generator')?>');
                            }
                        }
                        else alert(res.msg);
                    }
                })
            }
        }
        $(document).on('click','.wpaicg_template_save',function (e){
            $('.wpaicg_custom_template_id').remove();
            wpaicgSaveTemplate(e);
        });
        $(document).on('click','.wpaicg_template_update',function (e){
            wpaicgSaveTemplate(e);
        });
    })
</script>
