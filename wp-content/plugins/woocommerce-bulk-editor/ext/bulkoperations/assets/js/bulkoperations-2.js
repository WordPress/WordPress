"use strict";

var bulkoperations_default_attributes = [];

jQuery(function ($) {

    jQuery(document).on("do_tabs-bulkoperations-default-values", {}, function () {

        jQuery('#bulkoperations_attributes_default').chosen({
            width: '100%'
        }).trigger("chosen:updated");

        jQuery('#bulkoperations_attributes_default').off('change');
        jQuery('#bulkoperations_attributes_default').on('change',function () {

            if (jQuery(this).val() && bulkoperations_default_attributes.length < jQuery(this).val().length) {
                //add
                //https://stackoverflow.com/questions/1187518/how-to-get-the-difference-between-two-arrays-in-javascript
                var diff = jQuery(jQuery(this).val()).not(bulkoperations_default_attributes).get();
                bulkoperations_default_attributes = jQuery(this).val();
                var new_attribute = diff[0];
                var new_attribute_label = jQuery(this).find('option[value="' + new_attribute + '"]').text();

                jQuery('.bulkoperations_apply_combination_btn').show();

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
                        var select_id = 'bulkoperations_def_t_' + new_attribute;
                        jQuery('#bulkoperations_attributes_terms_default').append('<li><select id="' + select_id + '" data-attribute="' + new_attribute + '" data-placeholder="' + new_attribute_label + '"></select><a href="#" class="help_tip woobe_drag_and_drope" title="'+lang.move+'"><img src="' + woobe_assets_link + 'images/move.png" width="18" alt="'+lang.move+'" /></a></li>');
                        __woobe_fill_select(select_id, JSON.parse(terms), [], 0, true);
                        jQuery('#' + select_id).chosen({
                            width: '100%'
                        });

                        //***

                        jQuery("#bulkoperations_attributes_terms_default").sortable({
                            items: "li:not(.unsortable)",
                            update: function (event, ui) {},
                            opacity: 0.8,
                            cursor: "crosshair",
                            handle: '.woobe_drag_and_drope',
                            placeholder: 'woobe-options-highlight'
                        });
                    }
                });

            } else {
                //remove
                if (jQuery(this).val()) {
                    bulkoperations_default_attributes = jQuery(this).val();
                } else {
                    bulkoperations_default_attributes = [];
                }

                //***

                if (bulkoperations_default_attributes.length === 0) {
                    jQuery('#bulkoperations_attributes_terms_default').html('');
                    jQuery('.bulkoperations_apply_combination_btn').hide();
                } else {

                    jQuery('#bulkoperations_attributes_terms_default select').each(function (i, s) {
                        var tax = jQuery(this).data('attribute');
                        if (jQuery.inArray(tax, bulkoperations_default_attributes) === -1) {
                            jQuery(this).chosen("destroy").parent().remove();
                        }
                    });
                }

            }



        });

        return true;
    });

});

function bulkoperations_apply_combination() {
    if (confirm(lang.sure)) {
        if (bulkoperations_default_attributes.length > 0) {

            woobe_bulkoperations_is_going();
            jQuery('.bulkoperations_apply_combination_btn').hide();
            jQuery('.woobe_bulkoperations_terminate_btn').show();
            woobe_set_progress('woobe_bulkoperations_progress_default', 0);

            //***
            var combination = {};
            jQuery('#bulkoperations_attributes_terms_default select').each(function (i, sel) {
                combination[jQuery(sel).data('attribute')] = jQuery(sel).val();
            });

            //***
            if (woobe_checked_products.length > 0) {
                __woobe_bulkoperations_products2(woobe_checked_products, 0, combination);
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
                            __woobe_bulkoperations_products2(products_ids, 0, combination);
                        }
                    },
                    error: function () {
                        if (!woobe_bulkoperations_user_cancel) {
                            alert(lang.error);
                            woobe_bulkoperations_terminate2();
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
function __woobe_bulkoperations_products2(products, start, combination) {
    var step = 10;
    var products_ids = products.slice(start, start + step);

    woobe_bulkoperations_xhr = jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_bulkoperations_apply_default_combination',
            products_ids: products_ids,
            combination: combination
        },
        success: function () {
            if ((start + step) > products.length) {

                woobe_message(lang.bulkoperations.finished2, 'notice');
                //https://datatables.net/reference/api/draw()
                //data_table.draw('page'); - we not need it here
                jQuery('.bulkoperations_apply_combination_btn').show();
                jQuery('.woobe_bulkoperations_terminate_btn').hide();
                woobe_set_progress('woobe_bulkoperations_progress_default', 100);
                jQuery(document).trigger('woobe_bulkoperations_completed_default');
                woobe_bulkoperations_is_going(false);

            } else {
                //show %
                woobe_set_progress('woobe_bulkoperations_progress_default', (start + step) * 100 / products.length);
                __woobe_bulkoperations_products2(products, start + step, combination);
            }
        },
        error: function () {
            if (!woobe_bulkoperations_user_cancel) {
                alert(lang.error);
                woobe_bulkoperations_terminate2();
            }
            woobe_bulkoperations_is_going(false);
        }
    });
}


function woobe_bulkoperations_terminate2() {
    if (confirm(lang.sure)) {
        woobe_bulkoperations_user_cancel = true;
        woobe_bulkoperations_xhr.abort();
        woobe_hide_progress('woobe_bulkoperations_progress_default');
        jQuery('.bulkoperations_apply_combination_btn').show();
        jQuery('.woobe_bulkoperations_terminate_btn').hide();
        woobe_message(lang.canceled, 'error');
        woobe_bulkoperations_user_cancel = false;
        woobe_bulkoperations_is_going(false);
    }
}

