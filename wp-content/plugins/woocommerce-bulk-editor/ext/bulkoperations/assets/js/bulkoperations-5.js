"use strict";

jQuery(function ($) {    

    jQuery('#bulkoperations_swap_att_from').on('change',function () {
        woobe_message(lang.loading, 'warning');
        jQuery(this).find('option[value=-1]').remove();
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_bulkoperations_get_att_terms',
                attribute: jQuery(this).val()
            },
            success: function (terms) {
                woobe_message(lang.loaded, 'notice');
                var select_id = 'bulkoperations_swap_terms_from';
                jQuery('#bulkoperations_swap_terms_from_container').html('<select id="' + select_id + '"></select>');
                __woobe_fill_select(select_id, JSON.parse(terms), [], 0, true);
            },
            error: function () {
                alert(lang.error);
            }
        });

        return true;
    });


    jQuery('#bulkoperations_swap_att_to').on('change',function () {
        woobe_message(lang.loading, 'warning');
        jQuery(this).find('option[value=-1]').remove();
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_bulkoperations_get_att_terms',
                attribute: jQuery(this).val()
            },
            success: function (terms) {
                woobe_message(lang.loaded, 'notice');
                var select_id = 'bulkoperations_swap_terms_to';
                jQuery('#bulkoperations_swap_terms_to_container').html('<select id="' + select_id + '"></select>');
                __woobe_fill_select(select_id, JSON.parse(terms), [], 0, true);
            },
            error: function () {
                alert(lang.error);
            }
        });

        return true;
    });



});

function bulkoperations_apply_5() {

    var from = {};
    from['attribute'] = jQuery('#bulkoperations_swap_att_from').val();
    from['term'] = jQuery('#bulkoperations_swap_terms_from').val();

    var to = {};
    to['attribute'] = jQuery('#bulkoperations_swap_att_to').val();
    to['term'] = jQuery('#bulkoperations_swap_terms_to').val();

    if (parseInt(from.attribute, 10) === -1 || parseInt(to.attribute, 10) === -1) {
        woobe_message(lang.bulkoperations.no_combinations, 'error');
        return;
    }

    //***

    if (confirm(lang.sure)) {

        woobe_bulkoperations_is_going();
        jQuery('.bulkoperations_apply_5_btn').hide();
        jQuery('.woobe_bulkoperations_terminate_btn').show();
        woobe_set_progress('woobe_bulkoperations_progress_5', 0);

        //***
        if (woobe_checked_products.length > 0) {
            __woobe_bulkoperations_5(woobe_checked_products, 0, from, to);
        } else {
            woobe_bulkoperations_xhr = jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_bulkoperations_get_prod_count',
                    filter_current_key: woobe_filter_current_key
                },
                success: function (products_ids) {
                    products_ids = JSON.parse(products_ids);

                    if (products_ids.length) {
                        __woobe_bulkoperations_5(products_ids, 0, from, to);
                    }
                },
                error: function () {
                    if (!woobe_bulkoperations_user_cancel) {
                        alert(lang.error);
                        woobe_bulkoperations_terminate_5();
                    }
                    woobe_bulkoperations_is_going(false);
                }
            });
        }

    }


    return false;
}

//***

//service
function __woobe_bulkoperations_5(products, start, from, to) {
    var step = 5;
    var products_ids = products.slice(start, start + step);

    woobe_bulkoperations_xhr = jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_bulkoperations_swap',
            products_ids: products_ids,
            from: from,
            to: to
        },
        success: function () {
            if ((start + step) > products.length) {

                woobe_message(lang.bulkoperations.finished5, 'notice');
                //https://datatables.net/reference/api/draw()
                data_table.draw('page');
                jQuery('.bulkoperations_apply_5_btn').show();
                jQuery('.woobe_bulkoperations_terminate_btn').hide();
                woobe_set_progress('woobe_bulkoperations_progress_5', 100);
                jQuery(document).trigger('woobe_bulkoperations_completed_ordering');
                woobe_bulkoperations_is_going(false);

            } else {
                //show %
                woobe_set_progress('woobe_bulkoperations_progress_5', (start + step) * 100 / products.length);
                __woobe_bulkoperations_5(products, start + step, from, to);
            }
        },
        error: function () {
            if (!woobe_bulkoperations_user_cancel) {
                alert(lang.error);
                woobe_bulkoperations_terminate_5();
            }
            woobe_bulkoperations_is_going(false);
        }
    });
}


function woobe_bulkoperations_terminate_5() {
    if (confirm(lang.sure)) {
        woobe_bulkoperations_user_cancel = true;
        woobe_bulkoperations_xhr.abort();
        woobe_hide_progress('woobe_bulkoperations_progress_5');
        jQuery('.bulkoperations_apply_5_btn').show();
        jQuery('.woobe_bulkoperations_terminate_btn').hide();
        woobe_message(lang.canceled, 'error');
        woobe_bulkoperations_user_cancel = false;
        woobe_bulkoperations_is_going(false);
    }
}

