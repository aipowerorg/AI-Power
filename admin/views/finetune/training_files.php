<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wpaicg-modal-content">
<?php
if(isset($wpaicg_data) && is_array($wpaicg_data) && count($wpaicg_data)):
    ?>
    <table class="wp-list-table widefat fixed striped table-view-list comments">
        <thead>
        <tr>
            <th><?php echo esc_html__('ID','gpt3-ai-content-generator')?></th>
            <th><?php echo esc_html__('Purpose','gpt3-ai-content-generator')?></th>
            <th><?php echo esc_html__('Size','gpt3-ai-content-generator')?></th>
            <th><?php echo esc_html__('Created At','gpt3-ai-content-generator')?></th>
            <th><?php echo esc_html__('Filename','gpt3-ai-content-generator')?></th>
            <th><?php echo esc_html__('Status','gpt3-ai-content-generator')?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($wpaicg_data as $item){
            ?>
            <tr>
                <td><?php echo esc_html($item->id)?></td>
                <td><?php echo esc_html($item->purpose)?></td>
                <td><?php echo esc_html(size_format($item->bytes))?></td>
                <td><?php echo esc_html(date('Y-m-d H:i:s',$item->created_at))?></td>
                <td><?php echo esc_html($item->filename)?></td>
                <td><?php echo esc_html($item->status)?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
<?php
else:
    echo esc_html__('No training file','gpt3-ai-content-generator');
endif;
?>

</div>
