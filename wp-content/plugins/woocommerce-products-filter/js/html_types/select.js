"use strict";
function woof_init_selects() {
    
    if (woof_select_type == 'chosen') {
	jQuery("select.woof_select, select.woof_price_filter_dropdown").chosen();
    } else if (woof_select_type == 'selectwoo') {
	jQuery("select.woof_select, select.woof_price_filter_dropdown").selectWoo();
    }

    jQuery('.woof_select').change(function () {
        var slug = jQuery(this).val();
        var name = jQuery(this).attr('name');
        woof_select_direct_search(this, name, slug);
    });

    var containers = jQuery('.woof_hide_empty_container');
    jQuery.each(containers, function(i, item){
	var selector= jQuery(item).val();
	if(selector){
	    jQuery(selector).hide();
	}
	
    });
    
}

function woof_select_direct_search(_this, name, slug) {

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


