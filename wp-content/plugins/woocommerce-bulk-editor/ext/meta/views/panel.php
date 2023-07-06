<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOBE;
?>
<h4 class="woobe-documentation"><a href="https://bulk-editor.com/document/woocommerce-products-meta-fields/" target="_blank" class="button button-primary"><span class="icon-book"></span></a>&nbsp;<?php esc_html_e('Meta Fields', 'woocommerce-bulk-editor') ?></h4>

<?php if ($WOOBE->show_notes) : ?>
    <div class="woobe_set_attention woobe_alert"><?php esc_html_e('In FREE version of the plugin it is possible manipulate with 2 meta fields.', 'woocommerce-bulk-editor') ?></div><br />
<?php endif; ?>

<div class="col-lg-6">
    <h5><?php esc_html_e('Add Custom key by hands', 'woocommerce-bulk-editor') ?>:</h5>
    <input type="text" value="" class="woobe_meta_key_input" style="width: 75%;" />
    <a href="#" id="woobe_meta_add_new_btn" class="button button-primary button-large"><span class="icon-plus"></span></a> 

</div>

<div class="col-lg-6">
    <h5><?php esc_html_e('Get meta keys from any product by its ID', 'woocommerce-bulk-editor') ?>:</h5>
    <input type="number" min="1" class="woobe_meta_keys_get_input" value="" style="width: 75%;" placeholder="<?php esc_html_e('enter product ID', 'woocommerce-bulk-editor') ?>" />
    <a href="#" id="woobe_meta_get_btn" class="button button-primary button-large"><?php esc_html_e('Get', 'woocommerce-bulk-editor') ?></a>
</div>

<div class="clear"></div>

<br />

<form id="metaform" method="post" action="">
    <input type="hidden" name="woobe_meta_fields[]" value="" />
    <ul class="woobe_fields" id="woobe_meta_list">

        <?php
        if (!empty($metas)) {
            foreach ($metas as $m) {
                woobe_meta_print_li($m);
            }
        }
        ?>

    </ul>


    <br />

    <input type="submit" class="button button-primary button-primary" value="<?php echo esc_html__('Save meta fields', 'woocommerce-bulk-editor') ?>" />

</form>

<div style="display: none;" id="woobe_meta_li_tpl">
    <?php
    woobe_meta_print_li(array(
        'meta_key' => '__META_KEY__',
        'title' => '__TITLE__',
        'edit_view' => '',
        'type' => ''
    ));
    ?>
</div>

<?php

function woobe_meta_print_li($m) {
    ?>
    <li class="woobe_options_li">
        <a href="#" class="help_tip woobe_drag_and_drope" title="<?php esc_html_e('drag and drop', 'woocommerce-bulk-editor') ?>"><img src="<?php echo WOOBE_ASSETS_LINK ?>images/move.png" alt="<?php echo esc_html__('move', 'woocommerce-bulk-editor') ?>" /></a>

        <div class="col-lg-4">
            <input type="text" name="woobe_meta_fields[<?php echo $m['meta_key'] ?>][meta_key]" value="<?php echo $m['meta_key'] ?>" readonly="" class="woobe_column_li_option" />&nbsp;

        </div>
        <div class="col-lg-4">
            <input type="text" style="color: teal !important; font-weight: normal !important;" name="woobe_meta_fields[<?php echo $m['meta_key'] ?>][title]" placeholder="<?php esc_html_e('enter title', 'woocommerce-bulk-editor') ?>" value="<?php echo $m['title'] ?>" class="woobe_column_li_option" />&nbsp;

        </div>
        <div class="col-lg-2">
            <div class="select-wrap">
                <select name="woobe_meta_fields[<?php echo $m['meta_key'] ?>][edit_view]" class="woobe_meta_view_selector" style="width: 99%;">
                    <option <?php selected($m['edit_view'], 'textinput') ?> value="textinput"><?php esc_html_e('textinput', 'woocommerce-bulk-editor') ?></option>
                    <option <?php selected($m['edit_view'], 'popupeditor') ?> value="popupeditor"><?php esc_html_e('textarea', 'woocommerce-bulk-editor') ?></option>
                    <option <?php selected($m['edit_view'], 'switcher') ?> value="switcher"><?php esc_html_e('checkbox', 'woocommerce-bulk-editor') ?></option>
                    <option <?php selected($m['edit_view'], 'meta_popup_editor') ?> value="meta_popup_editor"><?php esc_html_e('array', 'woocommerce-bulk-editor') ?></option>
                    <option <?php selected($m['edit_view'], 'calendar') ?> value="calendar"><?php esc_html_e('calendar', 'woocommerce-bulk-editor') ?></option>
					<option <?php selected($m['edit_view'], 'gallery_popup_editor') ?> value="gallery_popup_editor"><?php esc_html_e('gallery', 'woocommerce-bulk-editor') ?></option>
				</select>
            </div>
        </div>
        <div class="col-lg-1">
            <div class="select-wrap" <?php if (in_array($m['edit_view'], array('popupeditor', 'switcher', 'meta_popup_editor', 'gallery_popup_editor', 'calendar'))): ?>style="display: none;"<?php endif; ?>>
                <select name="woobe_meta_fields[<?php echo $m['meta_key'] ?>][type]" class="woobe_meta_type_selector">
                    <option <?php selected($m['type'], 'string') ?> value="string"><?php esc_html_e('string', 'woocommerce-bulk-editor') ?></option>
                    <option <?php selected($m['type'], 'number') ?> value="number"><?php esc_html_e('number', 'woocommerce-bulk-editor') ?></option>
                </select>
            </div>
        </div>
        <div class="col-lg-1">
            &nbsp;<a href="#" class="button button-primary woobe_meta_delete" title="<?php esc_html_e('delete', 'woocommerce-bulk-editor') ?>"></a>
        </div>

        <div class="clear"></div>
    </li>
    <?php
}
