"use strict";

var woobe_export_current_xhr = null;//current ajax request (for cancel)
var woobe_export_user_cancel = false;
var woobe_export_time_postfix = null;
var woobe_export_file_url = null;


function woobe_export_to_csv() {
    woobe_export_time_postfix = woobe_regenerate_exp_file_postfix();
    woobe_export('csv');
}
function woobe_export_to_xml() {
    woobe_export_time_postfix = woobe_regenerate_exp_file_postfix();
    woobe_export('xml');
}
function woobe_export_to_excel() {
    woobe_export_time_postfix = woobe_regenerate_exp_file_postfix();
    woobe_export('excel');//todo
}

jQuery(document).on("do_tabs-export", {}, function () {
    setTimeout(function () {
        //set chosen
        jQuery('#tabs-export .chosen-select').chosen('destroy');
        jQuery('#tabs-export .chosen-select').chosen();
    }, 150);

    if (!woobe_export_file_url) {
        woobe_export_file_url = jQuery('.woobe_export_products_btn_down').attr('href');
    }
    return true;
});

function woobe_regenerate_exp_file_postfix() {
    let currentTime = new Date();

    let d = currentTime.getDate();
    if (d < 10) {
        d = '0' + d;
    }

    let m = currentTime.getMonth() + 1;
    if (m < 10) {
        m = '0' + m;
    }

    let h = currentTime.getHours();
    if (h < 10) {
        h = '0' + h;
    }

    let min = currentTime.getMinutes();
    if (min < 10) {
        min = '0' + min;
    }

    return '_' + d + '-' + m + '-' + currentTime.getFullYear() + '-' + h + '-' + min;
}

function woobe_export(format) {
    var combinations = woobe_export_get_combination();

    jQuery('.woobe_export_products_btn').hide();
    jQuery('.woobe_export_products_btn_down').hide();
    jQuery('.woobe_export_products_btn_down_xml').hide();
    jQuery('.woobe_export_products_btn_cancel').show();
    woobe_export_is_going();

    //***

    jQuery('.woobe_progress_export').show();
    woobe_message(lang.export.exporting, 'warning', 999999);

    if (woobe_checked_products.length > 0) {

        woobe_export_current_xhr = jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_export_products_count',
                format: format,
                no_filter: 1,
                download_files_count: parseInt(jQuery('#woobe_export_download_files_count').val(), 10),
                csv_delimiter: jQuery('#woobe_export_delimiter').val(),
                file_postfix: woobe_export_time_postfix
            },
            success: function (e) {
                woobe_set_progress('woobe_export_progress', 0);
                __woobe_export_products(format, woobe_checked_products, 0, combinations);
            },
            error: function () {
                alert(lang.error);
                woobe_export_is_going(false);
            }
        });
    } else {
        woobe_export_current_xhr = jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_export_products_count',
                format: format,
                filter_current_key: woobe_filter_current_key,
                csv_delimiter: jQuery('#woobe_export_delimiter').val(),
                file_postfix: woobe_export_time_postfix
            },
            success: function (products_ids) {
                products_ids = JSON.parse(products_ids);

                if (products_ids.length) {
                    woobe_set_progress('woobe_export_progress', 0);
                    __woobe_export_products(format, products_ids, 0, combinations);
                } else {
                    woobe_export_is_going(false);
                }

            },
            error: function () {
                if (!woobe_export_user_cancel) {
                    alert(lang.error);
                    woobe_export_to_csv_cancel();
                }
                woobe_export_is_going(false);
            }
        });
    }


    return false;
}

//service
function __woobe_export_products(format, products, start, combinations) {
    var step = 10;
    var products_ids = products.slice(start, start + step);
    var behavior = jQuery("#woobe_bulk_combination_attributes_export_behavior").val();
    woobe_export_current_xhr = jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_export_products',
            products_ids: products_ids,
            format: format,
            download_files_count: parseInt(jQuery('#woobe_export_download_files_count').val(), 10),
            csv_delimiter: jQuery('#woobe_export_delimiter').val(),
            combination: combinations,
            behavior: behavior,
            file_postfix: woobe_export_time_postfix
        },
        success: function (e) {
            //console.log(e);
            //console.log(JSON.parse(e));
            //return
            if ((start + step) > products.length) {
                woobe_message(lang.export.exported, 'notice');
                jQuery('.woobe_export_products_btn').show();
                if (format == 'xml') {
                    jQuery('.woobe_export_products_btn_down_xml').show();
                    jQuery('.woobe_export_products_btn_down_xml').attr('href', woobe_export_file_url + 'woobe_exported' + woobe_export_time_postfix + '.xml');
                } else {
                    jQuery('.woobe_export_products_btn_down').show();
                    jQuery('.woobe_export_products_btn_down').attr('href', woobe_export_file_url + 'woobe_exported' + woobe_export_time_postfix + '.csv');
                }

                jQuery('.woobe_export_products_btn_cancel').hide();
                woobe_set_progress('woobe_export_progress', 100);
                woobe_export_is_going(false);
            } else {
                //show %
                woobe_set_progress('woobe_export_progress', (start + step) * 100 / products.length);
                __woobe_export_products(format, products, start + step, combinations);
            }
        },
        error: function () {
            if (!woobe_export_user_cancel) {
                alert(lang.error);
                woobe_export_to_csv_cancel();
            }
            woobe_export_is_going(false);
        }
    });
}

function woobe_export_to_csv_cancel() {
    woobe_export_user_cancel = true;
    woobe_export_current_xhr.abort();
    woobe_hide_progress('woobe_export_progress');
    jQuery('.woobe_export_products_btn').show();
    jQuery('.woobe_export_products_btn_down').hide();
    jQuery('.woobe_export_products_btn_down_xml').hide();
    jQuery('.woobe_export_products_btn_cancel').hide();
    woobe_message(lang.canceled, 'error');
    woobe_export_user_cancel = false;
    woobe_export_is_going(false);
}

function woobe_export_is_going(go = true) {
    if (go) {
        jQuery('#wp-admin-bar-root-default').append("<li id='woobe_export_is_going'>" + lang.export.export_is_going + "</li>");

    } else {
        jQuery('#woobe_export_is_going').remove();
}

}
function woobe_export_get_combination() {
    var combination = {};
    if (woobe_show_variations > 0) {
        var rows = jQuery("#woobe_bulk_to_var_combinations_apply_export li");
        jQuery.each(rows, function (i, item) {
            var values = jQuery(item).find("select").serializeArray();
            combination[i] = {};
            jQuery.each(values, function (j, value) {
                //console.log(value);
                if (value.name !== undefined && value.value !== undefined) {
                    combination[i][value.name] = value.value;
                }
            })
        });

    }
    return combination
}

function woobe_bulk_add_combination_to_apply_export() {
    var select = jQuery('#woobe_bulk_combination_attributes_export');

    if (jQuery(select).val()) {

        woobe_message(lang.loading, 'warning');

        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_bulk_get_att_terms_export',
                attributes: jQuery(select).val(),
                hash_key: woobe_get_random_string(8).toLowerCase()
            },
            success: function (html) {
                woobe_message(lang.loaded, 'notice');

                jQuery('#woobe_bulk_to_var_combinations_apply_export').append('<li>' + html + '&nbsp;<a href="javascript: void(0);" class="woobe_bulk_get_att_terms_del button">x</a></li>');

                jQuery('.woobe_bulk_get_att_terms_del').off('click');
                jQuery('.woobe_bulk_get_att_terms_del').on('click', function () {
                    jQuery(this).parent().remove();
                    return false;
                });
            }
        });
    }


    return false;
}

