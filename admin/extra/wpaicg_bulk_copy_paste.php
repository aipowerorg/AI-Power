<?php
if ( ! defined( 'ABSPATH' ) ) exit;
include __DIR__.'/wpaicg_alert.php';
$wpaicg_all_categories = get_terms(array(
    'taxonomy' => 'category',
    'hide_empty' => false
));
?>
<h2><?php echo esc_html__('Auto Content From Multi Lines','gpt3-ai-content-generator')?></h2>
<div class="p-10">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">
                <?php echo esc_html__('Titles','gpt3-ai-content-generator')?><br>
                <small style="font-weight: normal">(<?php echo esc_html__('Enter one title per line.','gpt3-ai-content-generator')?>)</small>
            </th>
            <td>
                <textarea<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> rows="15" class="wpaicg-multi-line"></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Category','gpt3-ai-content-generator')?></th>
            <td>
                <select name="post_category">
                    <option value=""><?php echo esc_html__('None','gpt3-ai-content-generator')?></option>
                    <?php
                    foreach($wpaicg_all_categories as $wpaicg_all_category){
                        echo '<option value="'.esc_html($wpaicg_all_category->term_id).'">'.esc_html($wpaicg_all_category->name).'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Author','gpt3-ai-content-generator')?></th>
            <td>
                <select name="post_author">
                    <?php
                    foreach(get_users() as $user){
                        echo '<option'.($user->ID == get_current_user_id() ? ' selected':'').' value="'.esc_html($user->ID).'">'.esc_html($user->display_name).'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Status','gpt3-ai-content-generator')?></th>
            <td>
                <label>
                    <input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> checked type="radio" name="post_status" value="draft" class="wpaicg-post-status"> <?php echo esc_html__('Draft','gpt3-ai-content-generator')?>
                </label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label>
                    <input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> type="radio" value="publish" name="post_status" class="wpaicg-post-status"> <?php echo esc_html__('Publish','gpt3-ai-content-generator')?>
                </label>
                <p class="wpaicg-ajax-message"></p>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> class="button button-primary wpaicg-multi-button"><?php echo esc_html__('Generate','gpt3-ai-content-generator')?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script>
    (function ($){
        $('.wpaicg-schedule-post').datetimepicker({
            format: 'Y-m-d H:i',
            startDate: new Date()
        });
        var wpaicg_button = $('.wpaicg-multi-button');
        var wpaicg_multi_line = $('.wpaicg-multi-line');
        wpaicg_button.click(function (){
            var wpaicg_multi_line_value = wpaicg_multi_line.val();
            if(wpaicg_multi_line_value === ''){
                alert('<?php echo esc_html__('Please enter at least one line','gpt3-ai-content-generator')?>');
            }
            else{
                var wpaicg_lines = wpaicg_multi_line_value.split("\n");
                if(wpaicg_lines.length > <?php echo esc_html($wpaicg_number_title)?>){
                    <?php
                    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                    ?>
                    $('.wpaicg-ajax-message').html('<?php echo sprintf(esc_html__('You added more than %s lines so we are only processing first %s lines','gpt3-ai-content-generator'),$wpaicg_number_title,$wpaicg_number_title)?>');
                    <?php
                    else:
                    ?>
                    $('.wpaicg-ajax-message').html('<?php echo sprintf(esc_html__('Free users can only generate %s lines at a time. Please upgrade to the Pro plan to get access to more lines.','gpt3-ai-content-generator'),$wpaicg_number_title)?>');
                    <?php
                    endif;
                    ?>
                }
                var wpaicg_titles = wpaicg_lines.slice(0,<?php echo esc_html($wpaicg_number_title)?>);
                var wpaicg_schedules = [];
                var wpaicg_post_status = $('.wpaicg-post-status:checked').val();
                var wpaicg_schedule = $('.wpaicg-schedule-post').val();
                var wpaicg_category = $('select[name=post_category]').val();
                var wpaicg_author = $('select[name=post_author]').val();
                var wpaicg_categories = [];
                $.each(wpaicg_titles, function (idx,item){
                    wpaicg_schedules.push(wpaicg_schedule);
                    wpaicg_categories.push(wpaicg_category);
                });
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {wpaicg_titles: wpaicg_titles,wpaicg_schedules: wpaicg_schedules,post_author: wpaicg_author,post_status: wpaicg_post_status,wpaicg_category: wpaicg_categories, action: 'wpaicg_bulk_generator',source: 'multi','nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function(){
                        wpaicg_button.attr('disabled','disabled');
                        wpaicg_button.append('<span class="spinner"></span>');
                        wpaicg_button.find('.spinner').css('visibility','unset');
                    },
                    success: function (res){
                        wpaicg_button.removeAttr('disabled');
                        wpaicg_button.find('.spinner').remove();
                        if(res.status === 'success'){
                            window.location.href = '<?php echo admin_url('admin.php?page=wpaicg_bulk_content')?>&wpaicg_track='+res.id
                        }
                        else{
                            alert(res.msg);
                        }
                    },
                    error: function (){
                        wpaicg_button.removeAttr('disabled');
                        wpaicg_button.find('.spinner').remove();
                        alert('Something went wrong');
                    }
                })
            }
        })
    })(jQuery)
</script>
