"use strict";
function woof_init_meta_mselects(){

    if (woof_select_type == 'chosen') {
	jQuery("select.woof_meta_mselect").chosen();
    } else if (woof_select_type == 'selectwoo') {
	jQuery("select.woof_meta_mselect").selectWoo();
    }  
    
    
    jQuery('.woof_meta_mselect').change(function (a) {
        var slug = jQuery(this).val();
        var name = jQuery(this).attr('name');

        //fix for multiselect if in chosen mode remove options
        if (woof_select_type == 'chosen') {
            var vals = jQuery(this).chosen().val();
            jQuery('.woof_meta_mselect[name=' + name + '] option:selected').removeAttr("selected");
            jQuery('.woof_meta_mselect[name=' + name + '] option').each(function (i, option) {
                var v = jQuery(this).val();
                if (jQuery.inArray(v, vals) !== -1) {
                    jQuery(this).prop("selected", true);
                }
            });
        }

        woof_meta_mselect_direct_search(name, slug);
        return true;
    });
}

function woof_meta_mselect_direct_search(name, slug) {
    //mode with Filter button
    var values = [];
    var separator = ',';
    jQuery('.woof_meta_mselect[name=' + name + '] option:selected').each(function (i, v) {
        values.push(jQuery(this).val());
    });
    separator = jQuery('.woof_meta_mselect[name=' + name + ']').data('options_separator');
    //duplicates removing
    //http://stackoverflow.com/questions/9229645/remove-duplicates-from-javascript-array
    values = values.filter(function (item, pos) {
        return values.indexOf(item) == pos;
    });
    
    values = values.join(separator);
    if (values.length) {
        woof_current_values[name] = values;
    } else {
        delete woof_current_values[name];
    }

    woof_ajax_page_num = 1;
    if (woof_autosubmit) {
        woof_submit_link(woof_get_submit_link());
    }
}

