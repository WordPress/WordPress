<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<div style="padding: 0 0.2em;">
    <div id="woobe_tools_panel">

        <div style="position: relative">

            <a href="#" class="button button-secondary woobe_tools_panel_full_width_btn icon-resize-horizontal-1" title="<?php esc_html_e('Set full width', 'woocommerce-bulk-editor') ?>"></a>
            <a href="#" class="button button-secondary woobe_tools_panel_profile_btn" title="<?php esc_html_e('Columns profiles', 'woocommerce-bulk-editor') ?>"></a>


            <?php do_action('woobe_tools_panel_buttons') ?>


            <a href="#" class="button button-secondary woobe_tools_panel_newprod_btn" title="<?php esc_html_e('New Product', 'woocommerce-bulk-editor') ?>"></a>



            <a href="#" class="button button-primary woobe_tools_panel_duplicate_btn" title="<?php esc_html_e('Duplicate selected product(s). ATTENTION: duplication of variations of the products is locked!', 'woocommerce-bulk-editor') ?>" style="display: none;"></a>
            <a href="#" class="button button-primary woobe_tools_panel_delete_btn" title="<?php esc_html_e('Delete selected product(s)', 'woocommerce-bulk-editor') ?>" style="display: none;"></a>

            <a href="#" class="button button-primary woobe_tools_panel_uncheck_all" title="<?php esc_html_e('Uncheck all selected products', 'woocommerce-bulk-editor') ?>" style="display: none;"></a>
            <a href="#" class="button button-secondary woobe_filter_reset_btn2" title="<?php esc_html_e('Reset filters', 'woocommerce-bulk-editor') ?>" style="display: none;"></a>




            &nbsp;<span>
                <?php echo WOOBE_HELPER::draw_advanced_switcher(0, 'woobe_show_variations', '', array('true' => esc_html__('variations', 'woocommerce-bulk-editor'), 'false' => esc_html__('variations', 'woocommerce-bulk-editor')), array('true' => 1, 'false' => 0), 'js_check_woobe_show_variations', 'woobe_show_variations'); ?>
                <?php echo WOOBE_HELPER::draw_tooltip(esc_html__('Bulk editing of the parent products will be ignored! Enabling this mode hide not relevant bulk edit operations for the products [variations]! Before activation of this mode, for more convenient editing of [variations] recommend make filtering for all variable products. Binded operation also will be applied only to the products variations!', 'woocommerce-bulk-editor')) ?>
            </span>&nbsp;

            <span><a href="#" id="woobe_select_all_vars" class="button" style="display: none;"><?php esc_html_e('select all variations', 'woocommerce-bulk-editor') ?></a></span>

			<input type="hidden" id="woobe_tools_panel_nonce" value="<?php echo  wp_create_nonce( 'woobe_tools_panel_nonce' ); ?>">
            <?php do_action('woobe_tools_panel_buttons_end') ?>

            <div style="display: none;">
                <a href="#" id="woobe_scroll_right" class="button" title="<?php esc_html_e('Scroll right', 'woocommerce-bulk-editor') ?>" style="display: none;"></a>
                <a href="#" id="woobe_scroll_left" class="button" title="<?php esc_html_e('Scroll left', 'woocommerce-bulk-editor') ?>" style="display: none;"></a>
            </div>

        </div>
    </div>
</div>

<table id="advanced-table" data-editable="<?php echo $table_data['editable'] ?>" data-default-sort-by="<?php echo $table_data['default-sort-by'] ?>" data-sort="<?php echo $table_data['sort'] ?>" data-no-order="<?php echo $table_data['no-order'] ?>" data-additional='' data-start-page="<?php echo $table_data['start-page'] ?>"  data-extend-per-page="<?php echo $table_data['extend_per_page'] ?>" data-per-page="<?php echo $table_data['per-page'] ?>" data-fields="<?php echo $table_data['fields'] ?>" data-edit-views="<?php echo $table_data['edit_views'] ?>" data-edit-sanitize="<?php echo $table_data['edit_sanitize'] ?>" class="display table dt-responsive table-striped table-bordered nowrap">
    <thead>
        <tr>
            <?php foreach ($table_labels as $c => $label) : ?>
                <th id="woobe_col_<?php echo $c ?>"><?php echo trim($label['title']) ?><?php echo (!empty($label['desc']) AND $c > 0 ? WOOBE_HELPER::draw_tooltip($label['desc']) : '') ?></th>
                <?php endforeach; ?>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <?php foreach ($table_labels as $label) : ?>
                <th><?php echo trim($label['title']) ?></th>
            <?php endforeach; ?>
        </tr>
    </tfoot>
    <tbody></tbody>
</table>


