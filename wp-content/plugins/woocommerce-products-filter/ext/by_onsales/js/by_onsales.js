"use strict";
function woof_init_onsales() {

    if (icheck_skin != 'none') {

        jQuery('.woof_checkbox_sales').iCheck({
            checkboxClass: 'icheckbox_' + icheck_skin.skin + '-' + icheck_skin.color,
        });

        jQuery('.woof_checkbox_sales').on('ifChecked', function (event) {
            jQuery(this).attr("checked", true);
            woof_current_values.onsales = 'salesonly';
            woof_ajax_page_num = 1;
            if (woof_autosubmit) {
                woof_submit_link(woof_get_submit_link());
            }
        });

        jQuery('.woof_checkbox_sales').on('ifUnchecked', function (event) {
            jQuery(this).attr("checked", false);
            delete woof_current_values.onsales;
            woof_ajax_page_num = 1;
            if (woof_autosubmit) {
                woof_submit_link(woof_get_submit_link());
            }
        });

    } else {

        jQuery('.woof_checkbox_sales').on('change', function (event) {
            if (jQuery(this).is(':checked')) {
                jQuery(this).attr("checked", true);
                woof_current_values.onsales = 'salesonly';
                woof_ajax_page_num = 1;
                if (woof_autosubmit) {
                    woof_submit_link(woof_get_submit_link());
                }
            } else {
                jQuery(this).attr("checked", false);
                delete woof_current_values.onsales;
                woof_ajax_page_num = 1;
                if (woof_autosubmit) {
                    woof_submit_link(woof_get_submit_link());
                }
            }
        });
    }
}
