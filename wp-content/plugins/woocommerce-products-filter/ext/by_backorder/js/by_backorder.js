"use strict";
function woof_init_onbackorder() {
    if (icheck_skin != 'none') {
        
        jQuery('.woof_checkbox_onbackorder').iCheck({
            checkboxClass: 'icheckbox_' + icheck_skin.skin + '-' + icheck_skin.color,

        });
        
        jQuery('.woof_checkbox_onbackorder').on('ifChecked', function (event) {
            jQuery(this).attr("checked", true);
            woof_current_values.backorder = 'onbackorder';
            woof_ajax_page_num = 1;
            if (woof_autosubmit) {
                woof_submit_link(woof_get_submit_link());
            }
        });

        jQuery('.woof_checkbox_onbackorder').on('ifUnchecked', function (event) {
            jQuery(this).attr("onbackorder", false);
            delete woof_current_values.backorder;
            woof_ajax_page_num = 1;
            if (woof_autosubmit) {
                woof_submit_link(woof_get_submit_link());
            }
        });

    } else {
        jQuery('.woof_checkbox_onbackorder').on('change', function (event) {
            if (jQuery(this).is(':checked')) {
                jQuery(this).attr("checked", true);
                woof_current_values.backorder = 'onbackorder';
                woof_ajax_page_num = 1;
                if (woof_autosubmit) {
                    woof_submit_link(woof_get_submit_link());
                }
            } else {
                jQuery(this).attr("checked", false);
                delete woof_current_values.backorder;
                woof_ajax_page_num = 1;
                if (woof_autosubmit) {
                    woof_submit_link(woof_get_submit_link());
                }
            }
        });
    }
}
