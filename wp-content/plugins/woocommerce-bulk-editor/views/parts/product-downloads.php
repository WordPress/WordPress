<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<ul class="woobe_fields_tmp">

    <?php if (!empty($downloadable_files)): ?>
        <?php foreach ($downloadable_files as $key => $file) : ?>
            <li class="woobe_options_li">
                <table style="width: 100%;">
                    <tr>
                        <td class="sort" width="1%"><div style="margin: -4px 3px 0 0; line-height: 0;"><a href="#" class="help_tip woobe_drag_and_drope" title="<?php echo esc_html__('drag and drop', 'woocommerce-bulk-editor') ?>"><img style="vertical-align: middle;" src="<?php echo WOOBE_ASSETS_LINK ?>images/move.png" alt="<?php echo esc_html__('move', 'woocommerce-bulk-editor') ?>" /></a></div></td>
                        <td class="file_name">
                            <input type="text" class="input_text" placeholder="<?php esc_attr_e('File name', 'woocommerce-bulk-editor'); ?>" name="_wc_file_names[]" value="<?php echo esc_attr($file['name']); ?>" />
                            <input type="hidden" name="_wc_file_hashes[]" value="<?php echo esc_attr($key); ?>" />
                        </td>
                        <td class="file_url"><input type="text" class="input_text woobe_down_file_url" placeholder="http://product-link/" name="_wc_file_urls[]" value="<?php echo esc_attr($file['file']); ?>" /></td>
                        <td class="file_url_choose" width="1%"><a href="#" class="woobe-button woobe_upload_file_button" data-choose="<?php esc_attr_e('Choose file', 'woocommerce-bulk-editor'); ?>" data-update="<?php esc_attr_e('Insert file URL', 'woocommerce-bulk-editor'); ?>"><?php echo str_replace(' ', '&nbsp;', esc_html__('Choose file', 'woocommerce-bulk-editor')); ?></a></td>
                        <td width="1%"><a href="#" class="woobe_down_file_delete woobe-button">X</a></td>
                    </tr>
                </table>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>

</ul>


