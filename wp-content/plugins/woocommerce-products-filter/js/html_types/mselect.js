"use strict";
function woof_init_mselects() {

    if (woof_select_type == 'chosen') {
        jQuery('select.woof_mselect').chosen();
    } else if (woof_select_type == 'selectwoo') {
        try {
            jQuery('select.woof_mselect').selectWoo();
        } catch (e) {
            console.log(e);
        }
    }

    jQuery('.woof_mselect').change(function (a) {
        var slug = jQuery(this).val();
        var name = jQuery(this).attr('name');

        //fix for multiselect if in chosen mode remove options
        if (woof_select_type == 'chosen') {
            var vals = jQuery(this).chosen().val();
            jQuery('.woof_mselect[name=' + name + '] option:selected').removeAttr("selected");
            jQuery('.woof_mselect[name=' + name + '] option').each(function (i, option) {
                var v = jQuery(this).val();
                if (jQuery.inArray(v, vals) !== -1) {
                    jQuery(this).prop("selected", true);
                }
            });
        }

        woof_mselect_direct_search(name, slug);
        return true;
    });
    var containers = jQuery('.woof_hide_empty_container_ms');
    jQuery.each(containers, function (i, item) {
        var selector = jQuery(item).val();
        if (selector) {
            jQuery(selector).hide();
        }

    });
}

function woof_mselect_direct_search(name, slug) {
    //mode with Filter button
    var values = [];
    jQuery('.woof_mselect[name=' + name + '] option:selected').each(function (i, v) {
        values.push(jQuery(this).val());
    });

    //duplicates removing
    //http://stackoverflow.com/questions/9229645/remove-duplicates-from-javascript-array
    values = values.filter(function (item, pos) {
        return values.indexOf(item) == pos;
    });

    values = values.join(',');
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


