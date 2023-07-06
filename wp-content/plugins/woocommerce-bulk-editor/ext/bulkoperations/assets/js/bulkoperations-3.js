"use strict";

var bulkoperations_delete_attributes = [];
var bulkoperations_delete_how = 'combo';

//***

jQuery(function ($) {

    jQuery(document).on("do_tabs-woobe-bulkoperations-delete", {}, function () {

        jQuery('#bulkoperations_attributes_delete').chosen({
            width: '100%'
        }).trigger("chosen:updated");

        jQuery('#bulkoperations_attributes_delete').off('change');
        jQuery('#bulkoperations_attributes_delete').on('change',function () {

            if (jQuery(this).val() && bulkoperations_delete_attributes.length < jQuery(this).val().length) {
                //add
                //https://stackoverflow.com/questions/1187518/how-to-get-the-difference-between-two-arrays-in-javascript
                var diff = jQuery(jQuery(this).val()).not(bulkoperations_delete_attributes).get();
                bulkoperations_delete_attributes = jQuery(this).val();
                var new_attribute = diff[0];
                var new_attribute_label = jQuery(this).find('option[value="' + new_attribute + '"]').text();

                jQuery('.bulkoperations_apply_3_btn').show();

                //***

                woobe_message(lang.loading, 'warning');

                jQuery.ajax({
                    method: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'woobe_bulkoperations_get_att_terms',
                        attribute: new_attribute
                    },
                    success: function (terms) {
                        woobe_message(lang.loaded, 'notice');
                        var select_id = 'bulkoperations_del_t_' + new_attribute;
                        jQuery('#bulkoperations_attributes_terms_delete').append('<li><select id="' + select_id + '" data-attribute="' + new_attribute + '" data-placeholder="' + new_attribute_label + '"><option value="">' + lang.bulkoperations.not_selected_var + '</option></select></li>');
                        __woobe_fill_select(select_id, JSON.parse(terms), [], 0, true);
                        jQuery('#' + select_id).chosen({
                            width: '100%'
                        });

                        //***
                        /*
                         jQuery("#bulkoperations_attributes_terms_delete").sortable({
                         items: "li:not(.unsortable)",
                         update: function (event, ui) {},
                         opacity: 0.8,
                         cursor: "crosshair",
                         handle: '.woobe_drag_and_drope',
                         placeholder: 'woobe-options-highlight'
                         });
                         */
                    }
                });

            } else {
                //remove
                if (jQuery(this).val()) {
                    bulkoperations_delete_attributes = jQuery(this).val();
                } else {
                    bulkoperations_delete_attributes = [];
                }

                //***

                if (bulkoperations_delete_attributes.length === 0) {
                    jQuery('#bulkoperations_attributes_terms_delete').html('');
                    jQuery('.bulkoperations_apply_3_btn').hide();
                } else {

                    jQuery('#bulkoperations_attributes_terms_delete select').each(function (i, s) {
                        var tax = jQuery(this).data('attribute');
                        if (jQuery.inArray(tax, bulkoperations_delete_attributes) === -1) {
                            jQuery(this).chosen("destroy").parent().remove();
                        }
                    });
                }

            }



        });

        //***

        jQuery('#bulkoperations_attributes_delete_how').off('change');
        jQuery('#bulkoperations_attributes_delete_how').on('change',function () {
            bulkoperations_delete_how = jQuery(this).val();

            switch (bulkoperations_delete_how) {
                case 'all':
                    jQuery('.bulkoperations_attributes_delete_cont').hide();
                    jQuery('.bulkoperations_apply_3_btn').show();
                    jQuery('#bulkoperations_attributes_terms_delete').html('');
                    jQuery('#bulkoperations_attributes_delete').val('').trigger("chosen:updated");
                    bulkoperations_delete_attributes = [];
                    break;

                default:
                    jQuery('.bulkoperations_attributes_delete_cont').show();
                    if (jQuery('#bulkoperations_attributes_terms_delete li').length > 0) {
                        jQuery('.bulkoperations_apply_3_btn').show();
                    } else {
                        jQuery('.bulkoperations_apply_3_btn').hide();
                    }
                    break;
            }
            return true;
        });

        return true;
    });

});

function bulkoperations_apply_3() {
    if (confirm(lang.sure)) {
        var go = bulkoperations_delete_attributes.length > 0;
        if (bulkoperations_delete_how == 'all') {
            go = true;
        }

        //***

        if (go) {
            woobe_bulkoperations_is_going();
            jQuery('.bulkoperations_apply_3_btn').hide();
            jQuery('.woobe_bulkoperations_terminate_btn').show();
            woobe_set_progress('woobe_bulkoperations_progress_delete', 0);

            //***
            var combination = {};
            jQuery('#bulkoperations_attributes_terms_delete select').each(function (i, sel) {
                combination[jQuery(sel).data('attribute')] = jQuery(sel).val();
            });

            //***
            if (woobe_checked_products.length > 0) {
                __woobe_bulkoperations_products3(woobe_checked_products, 0, combination);
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
                            __woobe_bulkoperations_products3(products_ids, 0, combination);
                        }
                    },
                    error: function () {
                        if (!woobe_bulkoperations_user_cancel) {
                            alert(lang.error);
                            woobe_bulkoperations_terminate3();
                        }
                        woobe_bulkoperations_is_going(false);
                    }
                });
            }

        } else {
            woobe_message(lang.bulkoperations.no_combinations, 'warning', 3000);
        }
    }


    return false;
}

//***

//service
function __woobe_bulkoperations_products3(products, start, combination) {
    var step = 10;
    var products_ids = products.slice(start, start + step);

    woobe_bulkoperations_xhr = jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_bulkoperations_delete',
            products_ids: products_ids,
            combination: combination,
            delete_how: bulkoperations_delete_how
        },
        success: function (removed_ids) {
            removed_ids = JSON.parse(removed_ids);
            //console.log(removed_ids);
            if (removed_ids.length > 0) {
                for (var i = 0; i < removed_ids.length; i++) {
                    jQuery('#product_row_' + removed_ids[i]).remove();
                }
            }

            //***            

            if ((start + step) > products.length) {

                woobe_message(lang.bulkoperations.finished3, 'notice');
                //https://datatables.net/reference/api/draw()
                //data_table.draw('page'); - we not need it here
                jQuery('.bulkoperations_apply_3_btn').show();
                jQuery('.woobe_bulkoperations_terminate_btn').hide();
                woobe_set_progress('woobe_bulkoperations_progress_delete', 100);
                jQuery(document).trigger('woobe_bulkoperations_completed_delete');
                woobe_bulkoperations_is_going(false);

            } else {
                //show %
                woobe_set_progress('woobe_bulkoperations_progress_delete', (start + step) * 100 / products.length);
                __woobe_bulkoperations_products3(products, start + step, combination);
            }
        },
        error: function () {
            if (!woobe_bulkoperations_user_cancel) {
                alert(lang.error);
                woobe_bulkoperations_terminate3();
            }
            woobe_bulkoperations_is_going(false);
        }
    });
}


function woobe_bulkoperations_terminate3() {
    if (confirm(lang.sure)) {
        woobe_bulkoperations_user_cancel = true;
        woobe_bulkoperations_xhr.abort();
        woobe_hide_progress('woobe_bulkoperations_progress_delete');
        jQuery('.bulkoperations_apply_3_btn').show();
        jQuery('.woobe_bulkoperations_terminate_btn').hide();
        woobe_message(lang.canceled, 'error');
        woobe_bulkoperations_user_cancel = false;
        woobe_bulkoperations_is_going(false);
    }
}

