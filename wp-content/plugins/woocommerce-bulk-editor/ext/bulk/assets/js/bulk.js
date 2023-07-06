"use strict";

var woobe_current_bulk_key = '';
var woobe_current_bulk_field_keys = [];
var woobe_bulk_chosen_inited = false;//just fix to init chosen
var woobe_bulk_xhr = null;//current ajax request (for cancel)
var woobe_bulk_user_cancel = false;//current ajax request (for cancel)
var woobe_bind_editing = 0;

//***

jQuery(function ($) {
    //init chosen by first click because chosen init doesn work for hidden containers
    jQuery(document).on("do_tabs-bulk", {}, function () {
        //if (!woobe_bulk_chosen_inited) {
        setTimeout(function () {
            //set chosen
            jQuery('#tabs-bulk .chosen-select').chosen('destroy');
            jQuery('#tabs-bulk .chosen-select').chosen();
            woobe_bulk_chosen_inited = true;
        }, 150);
        //}

        return true;
    });

    //***

    jQuery('#js_check_woobe_bind_editing').on('check_changed', function (event) {
        woobe_bind_editing = parseInt(jQuery(this).val(), 10);
        return true;
    });

    //***
    //we need to synhronize selection for calculator and bul edit form
    jQuery('.woobe_num_rounding').on('change',function () {
        jQuery('.woobe_num_rounding').val(jQuery(this).val());
        return true;
    });

    //***

    jQuery(document).on("woobe_page_field_updated", {}, function (event, product_id, field_name, value, operation) {
        if (woobe_bind_editing > 0) {

            if ((woobe_checked_products.length - 1) > 0 && product_id > 0 && field_name != 0 && (typeof value != 'undefined')) {

                var behavior = 'new';
                if (typeof operation != 'undefined') {
                    behavior = operation;
                }
//console.log(value);value = 'woobe_prod_ids[]=4862&woobe_prod_ids[]=4808&woobe_prod_ids[]=4809'
 // value = decodeURIComponent(value.replace(/%2F/g, " "));
                //console.log(product_id);
                //console.log(field_name);
                //console.log(value);
                //console.log(operation);

                //***

                try {
                    if (!woobe_active_fields[field_name]['direct']) {
                        alert(lang.is_deactivated_in_free);
                        return false;
                    }
                } catch (e) {
                    console.log(e);
                }


                //***

                woobe_set_progress('woobe_bulk_progress', 0);
                woobe_message(lang.bulk.bulking, 'warning', 999999);
                woobe_current_bulk_key = woobe_get_random_string(16);
                jQuery('.woobe_bulk_terminate').show();
                woobe_bulk_is_going();
                woobe_disable_bind_editing();
                //***

                woobe_bulk_xhr = jQuery.ajax({
                    method: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'woobe_bulk_products_count',
                        bulk_data: jQuery('#woobe_bulk_form').serialize(),
                        no_filter: 1,
                        bulk_key: woobe_current_bulk_key,
                        products_count: woobe_checked_products.length - 1,
                        woobe_bind_editing: 1,
                        field: field_name,
                        val: value,
                        behavior: behavior
                    },
                    success: function () {
                        var arrayWithout = woobe_checked_products.filter(function (value) {
                            return value != product_id;
                        });

                        __woobe_bulk_products(arrayWithout, 0, woobe_current_bulk_key, field_name);
                        // woobe_disable_bind_editing();
                    },
                    error: function () {
                        if (!woobe_bulk_user_cancel) {
                            alert(lang.error);
                            woobe_bulk_terminate();
                        }
                        woobe_bulk_is_going(false);
                    }
                });



            }
        }

        //***

        __trigger_resize();

        return true;
    });

    //***

    woobe_init_bulk_panel();

    //placeholder label
    jQuery('#woobe_bulk_form input[placeholder]:not(.woobe_calendar)').placeholderLabel();

    //***

    jQuery('.woobe_bulk_terminate').on('click', function () {
        woobe_bulk_terminate();
        return false;
    });

    //***

    //***

    jQuery(document).on("taxonomy_data_redrawn", {}, function (event, tax_key, term_id) {

        var select_id = 'woobe_bulk_taxonomies_' + tax_key;
        var select = jQuery('#' + select_id);
        jQuery(select).empty();
        __woobe_fill_select(select_id, taxonomies_terms[tax_key]);
        jQuery(jQuery('#' + select_id)).chosen({
            width: '100%'
        }).trigger("chosen:updated");

        return true;
    });

    //***
    //action for bulk gallery images
    jQuery(document).on("woobe_act_gallery_editor_saved", {}, function (event, product_id, field_name, value) {


        if (product_id === 0) {
            //looks like we want to apply it for bulk editing

            jQuery('#gallery_popup_editor').hide();
            jQuery("[name='woobe_bulk[gallery][value]']").val(value);
            jQuery("[name='woobe_bulk[gallery][behavior]']").val(jQuery('#woobe_gall_operations').val());

            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_bulk_draw_gallery_btn',
                    product_id: 0,
                    field: field_name,
                    images: value
                },
                success: function (response) {
                    response = JSON.parse(response);
                    jQuery('#popup_val_gallery_0').parent().html(response.html);
                }
            });
        }



        return true;
    });

    //***
    //action for bulk downloads
    jQuery(document).on("woobe_act_downloads_editor_saved", {}, function (event, product_id, field_name, value) {

        if (product_id === 0) {
            //looks like we want to apply it for bulk editing

            jQuery('#downloads_popup_editor').hide();
            jQuery("[name='woobe_bulk[download_files][value]']").val(value);
            jQuery("[name='woobe_bulk[download_files][behavior]']").val(jQuery('#woobe_downloads_operations').val());

            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_bulk_draw_download_files_btn',
                    product_id: 0,
                    field: field_name,
                    files: value
                },
                success: function (html) {
                    jQuery('#popup_val_download_files_0').parent().html(html);
                }
            });
        }



        return true;
    });

    //***
    //action for bulk cross_sells
    jQuery(document).on("woobe_act_cross_sells_editor_saved", {}, function (event, product_id, field_name, value) {

        if (product_id === 0) {
            //looks like we want to apply it for bulk editing

            jQuery('#cross_sells_popup_editor').hide();
            jQuery("[name='woobe_bulk[cross_sell_ids][value]']").val(value);
            jQuery("[name='woobe_bulk[cross_sell_ids][behavior]']").val(jQuery('#woobe_crossels_operations').val());

            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_bulk_draw_cross_sells_btn',
                    product_id: 0,
                    field: field_name,
                    products: value
                },
                success: function (html) {
                    jQuery('#cross_sells_cross_sell_ids_0').parent().html(html);
                }
            });
        }


        return true;
    });

    //***
    //action for bulk upsells
    jQuery(document).on("woobe_act_upsells_editor_saved", {}, function (event, product_id, field_name, value) {

        if (product_id === 0) {
            //looks like we want to apply it for bulk editing

            jQuery('#upsells_popup_editor').hide();
            jQuery("[name='woobe_bulk[upsell_ids][value]']").val(value);
            jQuery("[name='woobe_bulk[upsell_ids][behavior]']").val(jQuery('#woobe_upsells_operations').val());

            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_bulk_draw_upsell_ids_btn',
                    product_id: 0,
                    field: field_name,
                    products: value
                },
                success: function (html) {
                    jQuery('#upsell_ids_upsell_ids_0').parent().html(html);
                }
            });
        }


        return true;
    });

    //***
    //action for bulk grouped
    jQuery(document).on("woobe_act_grouped_editor_saved", {}, function (event, product_id, field_name, value) {

        if (product_id === 0) {
            //looks like we want to apply it for bulk editing

            jQuery('#grouped_popup_editor').hide();
            jQuery("[name='woobe_bulk[grouped_ids][value]']").val(value);
            jQuery("[name='woobe_bulk[grouped_ids][behavior]']").val(jQuery('#woobe_grouped_operations').val());

            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_bulk_draw_grouped_ids_btn',
                    product_id: 0,
                    field: field_name,
                    products: value
                },
                success: function (html) {
                    jQuery('#grouped_ids_grouped_ids_0').parent().html(html);
                }
            });
        }


        return true;
    });

    //***

    woobe_bulk_init_additional();
});

//***

function woobe_init_bulk_panel() {

    jQuery('.bulk_checker').on('click', function () {
        var disable = false;
        if (jQuery('.bulk_checker:checked').length > 0) {
            jQuery('#woobe_bulk_products_btn').show();
        } else {
            jQuery('#woobe_bulk_products_btn').hide();
            disable = true;
        }

        jQuery(this).parents('.filter-unit-wrap').find('input[type=text],input[type=number]').prop("disabled", disable);
        jQuery(this).parents('.filter-unit-wrap').find('select').prop("disabled", disable).trigger("chosen:updated");

        if (!disable) {
            jQuery(this).parents('.filter-unit-wrap').find('label').css('color', 'rgb(1, 1, 1) !important');
        } else {
            jQuery(this).parents('.filter-unit-wrap').find('label').css('color', 'rgb(170, 170, 170)');
        }
    });

    //***

    jQuery('.woobe_bulk_add_special_key').on('change',function () {
        var input = jQuery(this).parents('.filter-unit-wrap').eq(0).find('.woobe_bulk_value').eq(0);
        var caretPos = input[0].selectionStart;
        var textAreaTxt = input.val();
        jQuery(input).focus();//to up its placeholder
        jQuery(input).trigger('click');//to up its placeholder
        input.val(textAreaTxt.substring(0, caretPos) + jQuery(this).val() + textAreaTxt.substring(caretPos));

        //jQuery(input).selectionStart = caretPos +  jQuery(this).val().length;
        jQuery(this).val(-1);
    });

    //***

    jQuery('.woobe_bulk_value_signs').on('change',function () {
        var key = jQuery(this).data('key');

        if (jQuery(this).val() === 'replace') {
            jQuery('.woobe_bulk_replace_to_' + key).show();
        } else {
            jQuery('.woobe_bulk_replace_to_' + key).hide();
        }

    });

    //***

    jQuery('#woobe_bulk_products_btn').on('click', function () {

        var bulk_txt = lang.bulk.want_to_bulk + '\n';
        woobe_current_bulk_field_keys = [];
        jQuery('.bulk_checker').each(function (index, ch) {
            if (jQuery(ch).is(':checked')) {
                bulk_txt += jQuery(ch).data('title') + '\n';
                woobe_current_bulk_field_keys.push(jQuery(ch).data('field-key'));
            }
        });
	if ( typeof woobe_checked_products != 'undefined' && woobe_checked_products.length ){
	    bulk_txt += '\n' + lang.checked_products + ": " + woobe_checked_products.length;
	}
         
        //***

        if (confirm(bulk_txt)) {
            jQuery('#woobe_bulk_products_btn').hide();
            woobe_set_progress('woobe_bulk_progress', 0);
            woobe_message(lang.bulk.bulking, 'warning', 999999);
            woobe_current_bulk_key = woobe_get_random_string(16);
            jQuery('.woobe_bulk_terminate').show();
            woobe_bulk_is_going();

            if (woobe_checked_products.length > 0) {
                woobe_bulk_xhr = jQuery.ajax({
                    method: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'woobe_bulk_products_count',
                        bulk_data: jQuery('#woobe_bulk_form').serialize(),
                        no_filter: 1,
                        bulk_key: woobe_current_bulk_key,
                        products_count: woobe_checked_products.length
                    },
                    success: function () {
                        __woobe_bulk_products(woobe_checked_products, 0, woobe_current_bulk_key);
                    },
                    error: function () {
                        if (!woobe_bulk_user_cancel) {
                            alert(lang.error);
                            woobe_bulk_terminate();
                        }
                        woobe_bulk_is_going(false);
                    }
                });
            } else {
                woobe_bulk_xhr = jQuery.ajax({
                    method: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'woobe_bulk_products_count',
                        bulk_data: jQuery('#woobe_bulk_form').serialize(),
                        bulk_key: woobe_current_bulk_key,
                        filter_current_key: woobe_filter_current_key//!!! IMPORTANT !!!
                    },
                    success: function (products_ids) {
                        products_ids = JSON.parse(products_ids);

                        if (products_ids.length) {
                            jQuery('#woobe_bulk_progress').show();
                            __woobe_bulk_products(products_ids, 0, woobe_current_bulk_key);
                        }

                    },
                    error: function () {
                        if (!woobe_bulk_user_cancel) {
                            alert(lang.error);
                            woobe_bulk_terminate();
                        }
                        woobe_bulk_is_going(false);
                    }
                });
            }
        }

        return false;
    });

    function woode_check__delete_products_btn() {
        if (jQuery('#woobe_bulk_delete_products_btn_fuse:checked').length) {
            jQuery('#woobe_bulk_delete_products_btn').removeAttr("disabled");
        } else {
            jQuery('#woobe_bulk_delete_products_btn').attr("disabled", "disabled");
        }
    }
    jQuery('#woobe_bulk_delete_products_btn_fuse').on('click', function () {
        woode_check__delete_products_btn();
    });
//DELETE!!!
    jQuery('#woobe_bulk_delete_products_btn').on('click', function () {
        if (jQuery('#woobe_bulk_delete_products_btn_fuse:checked').length==0) {
            return false;
        }
        var delete_txt = lang.bulk.want_to_delete + '\n';
        woobe_current_bulk_field_keys = [];
        if (confirm(delete_txt)) {
            jQuery('#woobe_bulk_delete_products_btn').hide();
            woobe_set_progress('woobe_bulk_progress', 0);
            woobe_message(lang.bulk.deleting, 'warning', 999999);
            woobe_current_bulk_key = woobe_get_random_string(16);
            jQuery('.woobe_bulk_terminate').show();
            woobe_bulk_is_going();

            if (woobe_checked_products.length > 0) {
                woobe_bulk_xhr = jQuery.ajax({
                    method: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'woobe_bulk_delete_products_count',
                        bulk_data: jQuery('#woobe_bulk_form').serialize(),
                        no_filter: 1,
                        bulk_key: woobe_current_bulk_key,
                        products_count: woobe_checked_products.length
                    },
                    success: function () {
                        __woobe_bulk_delete_products(woobe_checked_products, 0, woobe_current_bulk_key);
                    },
                    error: function () {
                        if (!woobe_bulk_user_cancel) {
                            alert(lang.error);
                            woobe_bulk_terminate();
                        }
                        woobe_bulk_is_going(false);
                    }
                });
            } else {
                woobe_bulk_xhr = jQuery.ajax({
                    method: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'woobe_bulk_delete_products_count',
                        bulk_data: jQuery('#woobe_bulk_form').serialize(),
                        bulk_key: woobe_current_bulk_key,
                        filter_current_key: woobe_filter_current_key//!!! IMPORTANT !!!
                    },
                    success: function (products_ids) {
                        products_ids = JSON.parse(products_ids);

                        if (products_ids.length) {
                            jQuery('#woobe_bulk_progress').show();
                            __woobe_bulk_delete_products(products_ids, 0, woobe_current_bulk_key);
                        }

                    },
                    error: function () {
                        if (!woobe_bulk_user_cancel) {
                            alert(lang.error);
                            woobe_bulk_terminate();
                        }
                        woobe_bulk_is_going(false);
                    }
                });
            }
        }
        return false;
    });

    function __woobe_bulk_delete_products(products, start, bulk_key, field_key) {
        var step = 10;

        var products_ids = products.slice(start, start + step);

        woobe_bulk_xhr = jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_bulk_delete_products',
                products_ids: products_ids,
                woobe_show_variations: woobe_show_variations,
                bulk_key: woobe_current_bulk_key
            },
            success: function (data) {
                //console.log(data);
                if ((start + step) > products.length) {
                    var update_data_table = true;
                    //***
                    var products_count_bulked = 10;
                    if (update_data_table) {
                        woobe_message(lang.bulk.bulked, 'notice', 30000);
                        products_count_bulked = parseInt(products_count_bulked, 10);

                        if (products_count_bulked > 4) {
                            //https://datatables.net/reference/api/draw()
                            data_table.draw('page');
                        } else {
                            //updated <= 4 rows, lets redraw only them
                            for (var i = 0; i < products.length; i++) {
                                woobe_redraw_table_row(jQuery('#product_row_' + products[i]), false);
                            }
                        }
                    } else {
                        woobe_message(lang.bulk.deleted, 'notice');
                    }


                    //***
                    setTimeout(function () {
                        //if after bulk edit of filtrated products they will get out from filtration                        
                        if (woobe_checked_products.length > 0) {
                            var to_delete = [];
                            for (var i = 0; i < woobe_checked_products.length; i++) {
                                if (!jQuery('#product_row_' + woobe_checked_products[i]).length) {
                                    //console.log(woobe_checked_products[i]);
                                    to_delete.push(woobe_checked_products[i]);
                                }
                            }

                            //+++

                            for (var i = 0; i < to_delete.length; i++) {
                                woobe_checked_products.splice(woobe_checked_products.indexOf(to_delete[i]), 1);
                            }
                            //console.log(woobe_checked_products);

                            __woobe_action_will_be_applied_to();
                        }

                    }, 2000);


                    //***

                    jQuery('#woobe_bulk_delete_products_btn').show();
                    jQuery('.woobe_bulk_terminate').hide();
                    woobe_set_progress('woobe_bulk_progress', 100);
                    jQuery(document).trigger('woobe_bulk_completed');
                    woobe_bulk_is_going(false);
                    jQuery('.woobe_num_rounding').val(0);

                } else {
                    //show %
                    var percents = (start + step) * 100 / products.length;
                    woobe_set_progress('woobe_bulk_progress', percents);
                    woobe_bulk_is_going_txt(percents.toFixed(2));
                    __woobe_bulk_delete_products(products, start + step, bulk_key, field_key);
                }
            },
            error: function () {
                if (!woobe_bulk_user_cancel) {
                    alert(lang.error);
                    woobe_bulk_terminate();
                }
                woobe_bulk_is_going(false);
            }
        });
    }

//END DELETE!!!
    //***
    //variation targeting
    jQuery('#woobe_bulk_add_combination_to_apply').on('click', function () {

        var select = jQuery('#woobe_bulk_combination_attributes');

        if (jQuery(select).val()) {

            woobe_message(lang.loading, 'warning');

            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_bulk_get_att_terms',
                    attributes: jQuery(select).val(),
                    hash_key: woobe_get_random_string(8).toLowerCase()
                },
                success: function (html) {
                    woobe_message(lang.loaded, 'notice');

                    jQuery('#woobe_bulk_to_var_combinations_apply').append('<li>' + html + '&nbsp;<a href="javascript: void(0);" class="woobe_bulk_get_att_terms_del button">x</a></li>');

                    jQuery('.woobe_bulk_get_att_terms_del').bind('click');
                    jQuery('.woobe_bulk_get_att_terms_del').on('click', function () {
                        jQuery(this).parent().remove();
                        return false;
                    });
                }
            });
        }


        return false;
    });
}

//service
function __woobe_bulk_products(products, start, bulk_key, field_key) {
    //var step = 10;
    var step = 50;
    var products_ids = products.slice(start, start + step);
    
    var rand_data = {
	action: jQuery('#woobe_random_action').eq(0).val(),
	decimal: jQuery('#woobe_random_decimal').eq(0).val(),
	from: jQuery('#woobe_random_from').eq(0).val(),
	to: jQuery('#woobe_random_to').eq(0).val()
    };

    woobe_bulk_xhr = jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_bulk_products',
            products_ids: products_ids,
            bulk_key: bulk_key,
            //filter_current_key: woobe_filter_current_key, - do not need here as we use products_ids
            woobe_show_variations: woobe_show_variations,
            num_rounding: jQuery('.woobe_num_rounding').eq(0).val(),
	    num_formula_action: jQuery('.woobe_formula_action').eq(0).val(),
	    num_formula_value: jQuery('.woobe_formula_value').eq(0).val(),
	    num_rand_data: rand_data
        },
        success: function (e) {

            if ((start + step) > products.length) {
                jQuery.ajax({
                    method: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'woobe_bulk_finish',
                        bulk_key: woobe_current_bulk_key,
                        filter_current_key: woobe_filter_current_key
                    },
                    success: function (products_count_bulked) {
                        var update_data_table = true;

                        if (typeof field_key !== 'undefined' && woobe_active_fields[field_key] !== 'undefined') {
                            if (woobe_active_fields[field_key].edit_view === 'popupeditor') {
                                //There is no sense in redrawing buttons with text if the value was updated only there in bind mode
                                //update_data_table = false;
                            }
                        } else {
                            if (woobe_current_bulk_field_keys.length > 0) {
                                //update_data_table = false;
                                //There is no sense in redrawing buttons with text if the value was updated only there in bulk mode
                                for (var i = 0; i < woobe_current_bulk_field_keys.length; i++) {
                                    if (typeof woobe_active_fields[woobe_current_bulk_field_keys[i]] != 'undefined') {

                                        if (woobe_active_fields[woobe_current_bulk_field_keys[i]].edit_view != 'popupeditor') {
                                            update_data_table = true;
                                            break;
                                        }

                                    } else {
                                        update_data_table = true;
                                        break;
                                    }
                                }
                            }
                        }

                        //***

                        if (update_data_table) {
                            woobe_message(lang.bulk.bulked, 'notice', 30000);
                            products_count_bulked = parseInt(products_count_bulked, 10);

                            if (products_count_bulked > 4) {
                                //https://datatables.net/reference/api/draw()
                                data_table.draw('page');
                            } else {
                                //updated <= 4 rows, lets redraw only them
                                for (var i = 0; i < products.length; i++) {
                                    woobe_redraw_table_row(jQuery('#product_row_' + products[i]), false);
                                }
                            }
                        } else {
                            woobe_message(lang.bulk.bulked2, 'notice');
                        }


                        //***
                        setTimeout(function () {
                            //if after bulk edit of filtrated products they will get out from filtration                        
                            if (woobe_checked_products.length > 0) {
                                var to_delete = [];
                                for (var i = 0; i < woobe_checked_products.length; i++) {
                                    if (!jQuery('#product_row_' + woobe_checked_products[i]).length) {
                                        //console.log(woobe_checked_products[i]);
                                        to_delete.push(woobe_checked_products[i]);
                                    }
                                }

                                //+++

                                for (var i = 0; i < to_delete.length; i++) {
                                    woobe_checked_products.splice(woobe_checked_products.indexOf(to_delete[i]), 1);
                                }
                                //console.log(woobe_checked_products);

                                __woobe_action_will_be_applied_to();
                            }

                        }, 2000);


                        //***

                        jQuery('#woobe_bulk_products_btn').show();
                        jQuery('.woobe_bulk_terminate').hide();
                        woobe_set_progress('woobe_bulk_progress', 100);
                        jQuery(document).trigger('woobe_bulk_completed');
                        woobe_bulk_is_going(false);
                        jQuery('.woobe_num_rounding').val(0);
                    },
                    error: function () {
                        if (!woobe_bulk_user_cancel) {
                            alert(lang.error);
                            woobe_bulk_terminate();
                        }
                        woobe_bulk_is_going(false);
                    }
                });

            } else {
                //show %
                var percents = (start + step) * 100 / products.length;
                woobe_set_progress('woobe_bulk_progress', percents);
                woobe_bulk_is_going_txt(percents.toFixed(2));
                __woobe_bulk_products(products, start + step, bulk_key, field_key);
            }
        },
        error: function () {
            if (!woobe_bulk_user_cancel) {
                alert(lang.error);
                woobe_bulk_terminate();
            }
            woobe_bulk_is_going(false);
        }
    });
}

function woobe_bulk_terminate() {
    woobe_bulk_user_cancel = true;
    woobe_bulk_xhr.abort();
    woobe_hide_progress('woobe_bulk_progress');
    jQuery('#woobe_bulk_products_btn').show();
    jQuery('.woobe_bulk_terminate').hide();
    woobe_message(lang.canceled, 'error');
    woobe_bulk_user_cancel = false;
    woobe_bulk_is_going(false);
}

function woobe_bulk_is_going(going = true) {
    if (going) {
        jQuery('#wp-admin-bar-root-default').append("<li id='woobe_bulk_is_going'>" + lang.bulk.bulk_is_going + " 0%</li>");
    } else {
        jQuery('#woobe_bulk_is_going').remove();
    }

    //any way bulk edition been done in some way
    jQuery(document).trigger('woobe_page_field_updated', [0, 0, 0]);

}

function woobe_bulk_is_going_txt(val) {
    jQuery('#woobe_bulk_is_going').html(lang.bulk.bulk_is_going + ' ' + val + '%');
}

function woobe_bulk_init_additional() {

    jQuery('#woobe_bulk_select_thumb_btn').on('click', function ()
    {
        var input_object = jQuery(this).parents('.filter-unit-wrap').find('.woobe_bulk_value').eq(0);
        var image = wp.media({
            title: lang.upload_file,
            multiple: false
        }).open()
                .on('select', function (e) {
                    var uploaded_image = image.state().get('selection').first();
                    // We convert uploaded_image to a JSON object to make accessing it easier
                    uploaded_image = uploaded_image.toJSON();
                    if (typeof uploaded_image.url != 'undefined') {
                        jQuery('#woobe_bulk_select_thumb').prop('src', uploaded_image.url);
                        jQuery(input_object).val(uploaded_image.id);
                    }
                });

        return false;
    });
}

