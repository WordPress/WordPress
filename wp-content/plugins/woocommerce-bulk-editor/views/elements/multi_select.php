<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//$val is terms ids here
?>

<div class="woobe_multi_select_cell">
    <div class="woobe_multi_select_cell_list"><?php echo WOOBE_HELPER::draw_attribute_list_btn($active_fields[$field_key]['select_options'], $val, $field_key, $post) ?></div>
    <div class="woobe_multi_select_cell_dropdown" style="display: none;">
        <?php
        echo WOOBE_HELPER::draw_select(array(
            'field' => $field_key,
            'product_id' => $product_id,
            'class' => 'woobe_data_select chosen-select',
            //'options' => $this->settings->active_fields[$field_key]['select_options'],
            'options' => array(),
            'selected' => $val,
                //'onmouseover' => 'woobe_multi_select_onmouseover(this)',
                //'onchange' => 'woobe_act_select(this)'
                ), true);
        ?>

        <div class="taxonomy_cell_edit">
            <div>
                <a href="#" class="page-title-action woobe_multi_select_cell_select button button-small"><?php esc_html_e('Select all', 'woocommerce-bulk-editor') ?></a>
            </div>
            <div>
                <a href="#" class="page-title-action woobe_multi_select_cell_deselect button button-small"><?php esc_html_e('Deselect all', 'woocommerce-bulk-editor') ?></a>
            </div>
            <div class="taxonomy_cell_edit2">
                <div>
                    <a href="#" class="page-title-action woobe_multi_select_cell_save button button-small"><?php esc_html_e('save', 'woocommerce-bulk-editor') ?></a>
                    <a href="#" class="page-title-action woobe_multi_select_cell_new button button-small" data-tax-key="<?php echo $field_key ?>"><?php esc_html_e('new', 'woocommerce-bulk-editor') ?></a>
                </div>
                <div>
                    <a href="#" class="page-title-action woobe_multi_select_cell_cancel button button-small"><?php esc_html_e('cancel', 'woocommerce-bulk-editor') ?></a>
                </div>
            </div>
        </div>


        <div class="clear"></div>

    </div>
</div>

