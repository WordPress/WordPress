"use strict";
function woof_init_labels() {
    jQuery('.woof_label_term').on('click', function () {

        var checkbox = jQuery(this).find('input.woof_label_term').eq(0);

        if (jQuery(checkbox).is(':checked')) {
            jQuery(checkbox).attr("checked", false);
            jQuery(this).removeClass("checked");
            woof_label_process_data(checkbox, false);
        } else {
            jQuery(checkbox).attr("checked", true);
            jQuery(this).addClass("checked");
            woof_label_process_data(checkbox, true);
        }
    });
}
function woof_label_process_data(_this, is_checked) {
    var tax = jQuery(_this).data('tax');
    var name = jQuery(_this).attr('name');
    var term_id = jQuery(_this).data('term-id');
    woof_label_direct_search(term_id, name, tax, is_checked);
}
function woof_label_direct_search(term_id, name, tax, is_checked) {
    var values = '';
    var checked = true;
    if (is_checked) {
        if (tax in woof_current_values) {
            woof_current_values[tax] = woof_current_values[tax] + ',' + name;
        } else {
            woof_current_values[tax] = name;
        }
        checked = true;
    } else {
        values = woof_current_values[tax];
        values = values.split(',');
        var tmp = [];
        jQuery.each(values, function (index, value) {
            if (value != name) {
                tmp.push(value);
            }
        });
        values = tmp;
        if (values.length) {
            woof_current_values[tax] = values.join(',');
        } else {
            delete woof_current_values[tax];
        }
        checked = false;
    }
    jQuery('.woof_label_term_' + term_id).attr('checked', checked);
    woof_ajax_page_num = 1;
    if (woof_autosubmit) {
        woof_submit_link(woof_get_submit_link());
    }
}


