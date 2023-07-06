"use strict";
function woof_init_meta_selects() {
    
    if (woof_select_type == 'chosen') {
	jQuery("select.woof_meta_select").chosen();
    } else if (woof_select_type == 'selectwoo') {
	jQuery("select.woof_meta_select").selectWoo();
    }    
    

    jQuery('.woof_meta_select').change(function () {
        var slug = jQuery(this).val();
        var name = jQuery(this).attr('name');
        woof_meta_select_direct_search(this, name, slug);
    });
}

function woof_meta_select_direct_search(_this, name, slug) {

    jQuery.each(woof_current_values, function (index, value) {
        if (index == name) {
            delete woof_current_values[name];
            return;
        }
    });

    if (slug != 0) {
        woof_current_values[name] = slug;
    }

    woof_ajax_page_num = 1;
    if (woof_autosubmit || jQuery(_this).within('.woof').length == 0) {
        woof_submit_link(woof_get_submit_link());
    }

}


