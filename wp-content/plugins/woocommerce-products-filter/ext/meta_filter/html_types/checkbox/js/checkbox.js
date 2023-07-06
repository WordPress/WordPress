"use strict";
function woof_init_meta_checkbox() {
    if (icheck_skin != 'none') {
        
        jQuery('.woof_meta_checkbox').iCheck({
            checkboxClass: 'icheckbox_' + icheck_skin.skin + '-' + icheck_skin.color,
        });
        
        jQuery('.woof_meta_checkbox').on('ifChecked', function (event) {
            jQuery(this).attr("checked", true);
            woof_current_values[jQuery(this).attr("name")] = 1;
            woof_ajax_page_num = 1;
            if (woof_autosubmit) {
                woof_submit_link(woof_get_submit_link());
            }
        });

        jQuery('.woof_meta_checkbox').on('ifUnchecked', function (event) {
            jQuery(this).attr("checked", false);
            delete woof_current_values[jQuery(this).attr("name")];
            woof_ajax_page_num = 1;
            if (woof_autosubmit) {
                woof_submit_link(woof_get_submit_link());
            }
        });

    } else {
        jQuery('.woof_meta_checkbox').on('change', function (event) {
            if (jQuery(this).is(':checked')) {
                jQuery(this).attr("checked", true);
                woof_current_values[jQuery(this).attr("name")] = 1;
                woof_ajax_page_num = 1;
                if (woof_autosubmit) {
                    woof_submit_link(woof_get_submit_link());
                }
            } else {
                jQuery(this).attr("checked", false);
                delete woof_current_values[jQuery(this).attr("name")];
                woof_ajax_page_num = 1;
                if (woof_autosubmit) {
                    woof_submit_link(woof_get_submit_link());
                }
            }
        });
    }
}


