<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wpaicg-modal-content">
    <?php
    if(isset($wpaicg_data) && is_array($wpaicg_data) && count($wpaicg_data)):
        usort($wpaicg_data, function ($item1, $item2) {
            return $item2->created_at <=> $item1->created_at;
        });
        ?>
        <table class="wp-list-table widefat fixed striped table-view-list comments">
            <thead>
            <tr>
                <th><?php echo esc_html__('Object','gpt3-ai-content-generator')?></th>
                <th><?php echo esc_html__('Level','gpt3-ai-content-generator')?></th>
                <th><?php echo esc_html__('Created At','gpt3-ai-content-generator')?></th>
                <th><?php echo esc_html__('Message','gpt3-ai-content-generator')?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($wpaicg_data as $item){
                ?>
                <tr>
                    <td><?php echo esc_html($item->object)?></td>
                    <td><?php echo esc_html($item->level)?></td>
                    <td><?php echo esc_html(date('Y-m-d H:i:s',$item->created_at))?></td>
                    <td><?php echo esc_html($item->message)?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    <?php
    else:
        echo esc_html__('No events','gpt3-ai-content-generator');
    endif;
    ?>

</div>
