"use strict";

var woobe_popup_clicked = null;
var woobe_sort_order = [];
var woobe_checked_products = [];//product id which been checked
var woobe_last_checked_product = {id: 0, checked: false};
var woobe_tools_panel_full_width = 0;


(function ($) {

    jQuery(function () {

        jQuery('.woobe-tabs').woobeTabs();

        //***

        jQuery(document).on('keyup',function (e) {
            if (e.keyCode === 27) {
                jQuery('.woobe-modal-close').trigger('click');
            }
        });

        woobe_init_tips(jQuery('.zebra_tips1'));

        //***
        //for columns coloring
        try {
            jQuery('.woobe-color-picker').wpColorPicker();
        } catch (e) {
            console.log(e);
        }

        setTimeout(function () {
            jQuery('.woobe_column_color_pickers').each(function (index, picker) {
                jQuery(picker).find('span.wp-color-result-text').eq(0).html(lang.color_picker_col);
                jQuery(picker).find('span.wp-color-result-text').eq(1).html(lang.color_picker_txt);
            });
        }, 1000);

        //***

        jQuery(".woobe_fields").sortable({
            items: "li:not(.unsortable)",
            update: function (event, ui) {
                woobe_sort_order = [];
                jQuery('.woobe_fields').children('li').each(function (index, value) {
                    var key = jQuery(this).data('key');
                    woobe_sort_order.push(key);
                });
                jQuery('input[name="woobe[items_order]"]').val(woobe_sort_order.toString());
            },
            opacity: 0.8,
            cursor: "crosshair",
            handle: '.woobe_drag_and_drope',
            placeholder: 'woobe-options-highlight'
        });

        //fix: to avoid jumping
        jQuery('body').on('click', '.woobe_drag_and_drope', function () {
            return false;
        });

        //***

        jQuery('#tabs_f .woobe_calendar_cell_clear').on('click', function () {
            jQuery(this).parent().find('.woobe_calendar').val('').trigger('change');
            return false;
        });


        //options saving
        jQuery('#mainform').submit(function () {
            woobe_save_form(this, 'woobe_save_options');
            return false;
        });

        //***

        jQuery('#show_all_columns').on('click', function () {
            jQuery('.woobe_fields li').show();
            jQuery(this).parent().remove();
            return false;
        });

        //columns finder
        jQuery('#woobe_columns_finder').on('keyup keypress', function (e) {
            var keyCode = e.keyCode || e.which;
            //preventing form submit if press Enter button
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }

            //***

            jQuery('#tabs-settings .woobe_fields li').show();
            var search = jQuery(this).val().toLowerCase();

            jQuery('#tabs-settings .woobe_fields li.woobe_options_li .woobe_column_li_option').each(function (index, input) {
                var txt = jQuery(input).val().toLowerCase();
                if (txt.indexOf(search) != -1) {
                    jQuery(input).parents('li').show();
                } else {
                    jQuery(input).parents('li').hide();
                }
            });

            return true;
        });

        //*****************************************

        jQuery('body').on('click', '.woobe_select_image', function ()
        {
            var input_object = jQuery(this).prev('input[type=text]');
            window.send_to_editor = function (html)
            {
                jQuery('#woobe_buffer').html(html);
                var imgurl = jQuery('#woobe_buffer').find('a').eq(0).attr('href');
                jQuery('#woobe_buffer').html("");
                jQuery(input_object).val(imgurl);
                jQuery(input_object).trigger('change');
                tb_remove();
            };
            tb_show('', 'media-upload.php?post_id=0&type=image&TB_iframe=true');

            return false;
        });

        //***

        woobe_init_advanced_panel();
        if (parseInt(woobe_get_from_storage('woobe_tools_panel_full_width_btn'), 10)) {
            jQuery('.woobe_tools_panel_full_width_btn').trigger('click');
        }
        //woobe_init_bulk_panel();

        //options columns switchers only!
        woobe_init_switchery(false);

        //***
        jQuery(document).scroll(function (e) {
            var offset = (jQuery('#tabs').offset().top + 15) - jQuery(document).scrollTop();

            if (offset < 0) {
                if (!jQuery('#woobe_tools_panel').hasClass('woobe-adv-panel-fixed')) {
                    jQuery('#woobe_tools_panel').addClass('woobe-adv-panel-fixed');
                    jQuery('#woobe_tools_panel').css('top', jQuery('#wpadminbar').height() + 'px');
                    jQuery('#woobe_tools_panel').css('width', (jQuery('#tabs-products').width() - 10) + 'px');
                }
            } else {
                jQuery('#woobe_tools_panel').removeClass('woobe-adv-panel-fixed');
            }
        });

        setTimeout(function () {
            jQuery('.dataTables_scrollBody').scrollbar({
                autoScrollSize: false,
                scrollx: jQuery('.external-scroll_x'),
                scrolly: jQuery('.external-scroll_y')
            });
     //***

            jQuery(document).on("tab_switched", {}, function (e, tab_id) {

                var allow = ['tabs-products'];
                /*
                 * moved to observer
                 if (jQuery.inArray(tab_id, allow) > -1) {
                 jQuery('.external-scroll_wrapper').show();
                 } else {
                 jQuery('.external-scroll_wrapper').hide();
                 }
                 */
                return true;
            });

        }, 2000);

        //***

        jQuery('.shop_manager_visibility').on('click', function () {
            var key = jQuery(this).data('key');
            var val = 0;

            if (jQuery(this).is(':checked')) {
                val = 1;
            }

            jQuery("input[name='woobe_options[fields][" + key + "][shop_manager_visibility]']").val(val);
            return true;
        });

        //+++
        //https://stackoverflow.com/questions/123999/how-can-i-tell-if-a-dom-element-is-visible-in-the-current-viewport
        //https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API
        (new window.IntersectionObserver(([entry]) => {
            if (entry.isIntersecting) {
                //enter
                jQuery('.external-scroll_wrapper').show();
                return;
            }
            //leave
            jQuery('.external-scroll_wrapper').hide();
        }, {
            root: null,
            threshold: 1.0, // set offset 0.1 means trigger if atleast 10% of element in viewport
        })).observe(document.querySelector('#woobe_tools_panel'));

    });


})(jQuery);


function woobe_init_advanced_panel() {

    //full width button
    jQuery('.woobe_tools_panel_full_width_btn').on('click', function () {
        if (woobe_tools_panel_full_width === 0) {
            woobe_tools_panel_full_width = jQuery('#adminmenuwrap').width();
            jQuery('#adminmenuback').hide();
            jQuery('#adminmenuwrap').hide();
            jQuery('#wpcontent').css('margin-left', '0px');
            jQuery(this).addClass('button-primary');
            woobe_set_to_storage('woobe_tools_panel_full_width_btn', 1);
        } else {
            jQuery('#adminmenuback').show();
            jQuery('#adminmenuwrap').show();
            jQuery('#wpcontent').css('margin-left', woobe_tools_panel_full_width + 'px');
            jQuery(this).removeClass('button-primary');
            woobe_tools_panel_full_width = 0;
            woobe_set_to_storage('woobe_tools_panel_full_width_btn', 0);
        }

        __trigger_resize();

        return false;
    });

    //***

    jQuery('.woobe_tools_panel_profile_btn').on('click', function () {
        //jQuery('#woobe_tools_panel_profile_popup .woobe-modal-title').html(jQuery(this).data('name') + ' [' +jQuery(this).data('key') + ']');
        jQuery('#woobe_tools_panel_profile_popup').show();
        jQuery('#woobe_new_profile').focus();

        return false;
    });


    //***

    jQuery('.woobe-modal-close8').on('click', function () {
        jQuery('#woobe_tools_panel_profile_popup').hide();
    });

    //***

    woobe_init_profiles();

    //***
    //creating of new product
    jQuery('.woobe_tools_panel_newprod_btn').on('click', function () {

        var count = 1;

        if (count = prompt(lang.enter_new_count, 1)) {
            if (count > 0) {
                woobe_message(lang.creating, 'warning');
                __woobe_product_new(count, 0);
            }
        }

        return false;
    });

    //***

    jQuery('.woobe_tools_panel_duplicate_btn').on('click', function () {

        var products_ids = [];
        jQuery('.woobe_product_check').each(function (ii, ch) {
            if (jQuery(ch).prop('checked')) {
                products_ids.push(jQuery(ch).data('product-id'));
            }
        });

        if (products_ids.length) {
            var count = 1;
            if (count = prompt(lang.enter_duplicate_count, 1)) {
                if (count > 0) {
                    var products = [];
                    for (var i = 0; i < count; i++) {
                        for (var y = 0; y < products_ids.length; y++) {
                            products.push(products_ids[y]);
                        }
                    }

                    products = products.reverse();

                    woobe_message(lang.duplicating, 'warning', 99999);
                    __woobe_product_duplication(products, 0, 0);
                }
            }
        }

        return false;
    });

    //hide or show duplicate button
    jQuery('body').on('click', '.woobe_product_check', function (e) {

        var product_id = parseInt(jQuery(this).data('product-id'), 10);

        //if keep SHIFT button and check product checkbox - possible to select/deselect products rows
        if (e.shiftKey) {

            if (jQuery(this).prop('checked')) {
                var to_check = true;
            } else {
                var to_check = false;
            }
            var distance_now = jQuery('#product_row_' + jQuery(this).data('product-id')).offset().top;
            var distance_last = jQuery('#product_row_' + woobe_last_checked_product.id).offset().top;
            var rows = jQuery('#advanced-table tbody tr');

            if (distance_now > distance_last) {
                //check/uncheck all above to woobe_last_checked_product.id
                jQuery(rows).each(function (index, tr) {
                    var d = jQuery(tr).offset().top;
                    if (d < distance_now && d > distance_last) {
                        jQuery(tr).find('.woobe_product_check').prop('checked', to_check);
                    }
                });
            } else {
                //check/uncheck all below to woobe_last_checked_product.id
                jQuery(rows).each(function (index, tr) {
                    var d = jQuery(tr).offset().top;
                    if (d > distance_now && d < distance_last) {
                        jQuery(tr).find('.woobe_product_check').prop('checked', to_check);
                    }
                });
            }
        }

        //***

        if (jQuery(this).prop('checked')) {
            woobe_select_row(product_id);
            woobe_checked_products.push(product_id);
            woobe_last_checked_product.checked = true;
        } else {
            woobe_select_row(product_id, false);
            //woobe_checked_products.splice(woobe_checked_products.indexOf(product_id), 1);
            woobe_checked_products = jQuery.grep(woobe_checked_products, function (value) {
                return value != product_id;
            });
            woobe_last_checked_product.checked = false;
        }

        //***

        //push all another checked ids
        if (e.shiftKey) {
            jQuery(rows).each(function (index, tr) {
                var p_id = parseInt(jQuery(tr).data('product-id'), 10);
                if (jQuery(tr).find('.woobe_product_check').prop('checked')) {
                    //console.log(p_id);
                    woobe_checked_products.push(p_id);
                    woobe_select_row(p_id);
                } else {
                    //console.log('---' + p_id);
                    //woobe_checked_products.splice(woobe_checked_products.indexOf(p_id), 1);
                    for (var i = 0; i < woobe_checked_products.length; i++) {
                        if (p_id === woobe_checked_products[i]) {
                            woobe_select_row(woobe_checked_products[i], false);
                            delete woobe_checked_products[i];
                        }
                    }
                }
            });

        }

        //***

        //remove duplicates if exists and filter values
        woobe_checked_products = Array.from(new Set(woobe_checked_products));
        woobe_checked_products = woobe_checked_products.filter(function (n) {
            return n != undefined;
        });

        //***
        woobe_last_checked_product.id = product_id;
        __woobe_action_will_be_applied_to();
        __manipulate_by_depend_buttons();
        woobe_add_info_top_panel();
    });

    //***
    //check all products
    jQuery('.all_products_checker').on('click', function () {
        if (woobe_show_variations > 0) {
            jQuery('tr .woobe_product_check').trigger('click');
            if (jQuery('tr .woobe_product_check:checked').length) {
                jQuery(this).prop('checked', 'checked');
            }
        } else {
            //product_type_variation
            jQuery('tr:not(.product_type_variation) .woobe_product_check').trigger('click');
            if (jQuery('tr:not(.product_type_variation) .woobe_product_check:checked').length) {
                jQuery(this).prop('checked', 'checked');
            }
        }
    });

    //uncheck all products
    jQuery('.woobe_tools_panel_uncheck_all').on('click', function () {
        jQuery('.woobe_product_check').prop('checked', false);
        jQuery('.all_products_checker').prop('checked', false);
        woobe_checked_products = [];
        __manipulate_by_depend_buttons();
        __woobe_action_will_be_applied_to();
        jQuery('.woobe_checked_info').remove();

        return false;
    });

    //***

    jQuery('.woobe_tools_panel_delete_btn').on('click', function () {

        if (confirm(lang.sure)) {
            var products_ids = [];
            jQuery('.woobe_product_check').each(function (ii, ch) {
                if (jQuery(ch).prop('checked')) {
                    products_ids.push(jQuery(ch).data('product-id'));
                }
            });

            if (products_ids.length) {
                woobe_message(lang.deleting, 'warning', 999999);
                __woobe_product_removing(products_ids, 0, 0);
            }
        }

        return false;
    });

    //***
    //another way chosen drop-downs width is 0
    setTimeout(function () {
        jQuery('.woobe_top_panel').hide();
        jQuery('.woobe_top_panel').css('margin-top', '-' + jQuery('.woobe_top_panel').height());
        //page loader fade
        jQuery(".woobe-admin-preloader").fadeOut("slow");
    }, 1000);

    //Show/Hide button for filter
    jQuery('.woobe_top_panel_btn').on('click', function () {
        var _this = this;
        jQuery('.woobe_top_panel').slideToggle('slow', function () {
            if (jQuery(this).is(':visible')) {
                jQuery(_this).html(lang.close_panel);
            } else {
                jQuery(_this).html(lang.show_panel);
            }
        });

        jQuery(document).trigger("woobe_top_panel_clicked");

        return false;
    });


    jQuery('.woobe_top_panel_btn2').on('click', function (e) {
        jQuery('.woobe_top_panel_btn').trigger('click');
        return false;
    });

    //***

    jQuery('#js_check_woobe_show_variations').on('check_changed', function () {

        woobe_show_variations = parseInt(jQuery(this).val(), 10);
        woobe_set_to_storage('woobe_show_variations', woobe_show_variations);

        if (woobe_show_variations > 0) {
            if (jQuery('tr.product_type_variation').length > 0) {
                jQuery('tr.product_type_variation').show();
            } else {
                data_table.draw('page');

            }
            jQuery('.not-for-variations').hide();
            jQuery('#woobe_show_variations_mode').show();
            jQuery('#woobe_show_variations_mode_export').show();

            //***

            jQuery('#woobe_select_all_vars').show();

        } else {
            jQuery('tr.product_type_variation').hide();
            jQuery('.not-for-variations').show();
            woobe_init_js_intab('tabs-bulk');
            jQuery('#woobe_show_variations_mode').hide();
            jQuery('#woobe_show_variations_mode_export').hide();

            //***
            //uncheck all checked attributes to avoid confusing with any bulk operation!
            if (jQuery('tr.product_type_variation.woobe_selected_row').length > 0) {
                jQuery('tr.product_type_variation.woobe_selected_row .woobe_product_check').prop('checked', false);

                jQuery('tr.product_type_variation.woobe_selected_row').each(function (index, row) {
                    var product_id = parseInt(jQuery(row).data('product-id'));

                    //https://stackoverflow.com/questions/3596089/how-to-remove-specific-value-from-array-using-jquery
                    woobe_checked_products = jQuery.grep(woobe_checked_products, function (value) {
                        return value != product_id;
                    });

                });

                __manipulate_by_depend_buttons();
                __woobe_action_will_be_applied_to();
            }

            //***

            jQuery('#woobe_select_all_vars').hide();
            woobe_init_js_intab('tabs-products');
        }

        //***

        jQuery('#tabs-bulk .chosen-select').chosen('destroy');
        jQuery('#tabs-bulk .chosen-select').chosen();

        jQuery('#tabs-export .chosen-select').chosen('destroy');
        jQuery('#tabs-export .chosen-select').chosen();
        //***

        return true;
    });

    if (woobe_show_variations > 0) {
        jQuery("[data-numcheck='woobe_show_variations']").prop('checked', true);
        jQuery('#js_check_woobe_show_variations').prop('value', 1);
    }

    //***


}

//init special function  for variation
function woobe_init_special_variation() {
    jQuery("select[data-field='tax_class']").find("option[value='parent']").hide();
    jQuery(".product_type_variation select[data-field='tax_class']").find("option[value='parent']").show();
    if (woobe_show_variations > 0) {
        jQuery('select[name="woobe_bulk[tax_class][value]"]').find("option[value='parent']").show();
    } else {
        jQuery('select[name="woobe_bulk[tax_class][value]"]').find("option[value='parent']").hide();
    }
}

//service
function __woobe_product_new(count, created) {

    var step = 10;
    var to_create = (created + step) < count ? step : count - created;
    var  woobe_nonce = jQuery('#woobe_tools_panel_nonce').val();
    woobe_message(lang.creating + ' (' + (created + to_create) + ')', 'warning');
    jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_create_new_product',
            to_create: to_create,
	    woobe_nonce: woobe_nonce
        },
        success: function () {
            if ((created + step) < count) {
                created += step;
                __woobe_product_new(count, created);
            } else {
                //https://stackoverflow.com/questions/25929347/how-to-redraw-datatable-with-new-data
                //data_table.clear().draw();
                woobe_checked_products = [];
                __manipulate_by_depend_buttons();
                data_table.order([1, 'desc']).draw();
                //data_table.draw();
                //data_table.rows.add(NewlyCreatedData); // Add new data
                //data_table.columns.adjust().draw(); // Redraw the DataTable
                woobe_message(lang.created, 'notice');
            }
        },
        error: function () {
            alert(lang.error);
        }
    });

}

//service
var woobe_product_duplication_errors = 0;
function __woobe_product_duplication(products, start, duplicated) {

    var step = 2;
    var products_ids = products.slice(start, start + step);
    var  woobe_nonce = jQuery('#woobe_tools_panel_nonce').val();
    jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_duplicate_products',
            products_ids: products_ids,
	    woobe_nonce: woobe_nonce
        },
        success: function () {
            if ((start + step) > products.length) {
                woobe_checked_products = [];
                __manipulate_by_depend_buttons();
                data_table.draw();
                woobe_message(lang.duplicated, 'notice', 99999);
            } else {
                duplicated += step;
                if (duplicated > products.length) {
                    duplicated = products.length;
                }
                woobe_message(lang.duplicating + ' (' + (products.length - duplicated) + ')', 'warning', 99999);
                __woobe_product_duplication(products, start + step, duplicated);
            }
        },
        error: function () {
            woobe_message(lang.error, 'error');
            woobe_product_duplication_errors++;
            if (woobe_product_duplication_errors > 5) {
                alert(lang.error);
                woobe_product_duplication_errors = 0;
            } else {
                //lets try again
                __woobe_product_duplication(products, start, duplicated);
            }
        }
    });


}


//service
function __woobe_product_removing(products, start, deleted) {
    var step = 10;

    var products_ids_portion = products.slice(start, start + step);
    var  woobe_nonce = jQuery('#woobe_tools_panel_nonce').val();
    jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_delete_products',
            products_ids: products_ids_portion,
	    woobe_nonce: woobe_nonce
        },
        success: function () {
            if ((start + step) > products.length) {
                woobe_checked_products = jQuery(woobe_checked_products).not(products).get();

                for (var i = 0; i < products.length; i++) {
                    if (jQuery('#product_row_' + products[i]).hasClass('product_type_variable')) {
                        (jQuery('#product_row_' + products[i]).nextAll('tr')).each(function (index, tr) {
                            if (jQuery(tr).hasClass('product_type_variation')) {
                                jQuery(tr).remove();
                            } else {
                                return false;
                            }
                        });
                    }

                    jQuery('#product_row_' + products[i]).remove();
                }
                woobe_message(lang.deleted, 'notice');

                __manipulate_by_depend_buttons();
                __woobe_action_will_be_applied_to();
            } else {
                deleted += step;
                if (deleted > products.length) {
                    deleted = products.length;
                }
                woobe_message(lang.deleting + ' (' + (products.length - deleted) + ')', 'warning');
                __woobe_product_removing(products, start + step, deleted);
            }
        },
        error: function () {
            alert(lang.error);
        }
    });


}


//service
function woobe_add_info_top_panel() {
    jQuery('.woobe_checked_info').remove();
    if (typeof woobe_checked_products != 'undefined' && woobe_checked_products.length) {
        var text_info = "<span class='woobe_checked_info'>" + lang.checked_products + ": <b>" + woobe_checked_products.length + "</b></span>";
        jQuery('#advanced-table_wrapper').prepend(text_info);
    }
}
var __manipulate_by_depend_color_rows_lock = false;
function __manipulate_by_depend_buttons(show = true) {

    if (show) {
        show = jQuery('.woobe_product_check:checked').length;
    }

    //***

    if (show) {
        jQuery('.woobe_tools_panel_duplicate_btn').show();
        jQuery('.woobe_tools_panel_delete_btn').show();
    } else {
        jQuery('.woobe_tools_panel_duplicate_btn').hide();
        jQuery('.woobe_tools_panel_delete_btn').hide();
    }

    //***

    if (woobe_checked_products.length) {
        jQuery('.woobe_tools_panel_uncheck_all').show();

        if (!__manipulate_by_depend_color_rows_lock) {
            setTimeout(function () {

                for (var i = 0; i < woobe_checked_products.length; i++) {
                    woobe_select_row(woobe_checked_products[i]);
                }

                __manipulate_by_depend_color_rows_lock = false;
            }, 777);
            __manipulate_by_depend_color_rows_lock = true;
        }

    } else {
        jQuery('.woobe_tools_panel_uncheck_all').hide();
        jQuery('#advanced-table tr').removeClass('woobe_selected_row');
}
}

function woobe_select_row(product_id, select = true) {
    if (select) {
        jQuery('#product_row_' + product_id).addClass('woobe_selected_row');
    } else {
        jQuery('#product_row_' + product_id).removeClass('woobe_selected_row');
}
}

function woobe_init_tips(obj) {
    new jQuery.Zebra_Tooltips(obj, {
        background_color: '#333',
        color: '#FFF'
    });
}


function woobe_init_switchery(only_data_table = true, product_id = 0) {

    var adv_tbl_id_string = '#advanced-table ';
    if (!only_data_table) {
        adv_tbl_id_string = '';//initialization switches for options too
    }

    //reinit only 1 row
    if (product_id > 0) {
        adv_tbl_id_string = adv_tbl_id_string + '#product_row_' + product_id + ' ';
    }

    //***

    //http://abpetkov.github.io/switchery/
    if (typeof Switchery !== 'undefined') {
        var elems = Array.prototype.slice.call(document.querySelectorAll(adv_tbl_id_string + '.js-switch'));
        elems.forEach(function (ch) {
            new Switchery(ch);
            //while reinit draws duplicates of switchers
            jQuery(ch).parent().find('span.switchery:not(:first)').remove();
        });
    }

    //***

    if (jQuery(adv_tbl_id_string + '.js-check-change').length > 0) {

        jQuery.each(jQuery(adv_tbl_id_string + '.js-check-change'), function (index, item) {

            jQuery(item).off('change');
            jQuery(item).on('change',function () {
                var state = item.checked.toString();
                var numcheck = jQuery(item).data('numcheck');
                var trigger_target = jQuery(item).data('trigger-target');
                var label = jQuery("*[data-label-numcheck='" + numcheck + "']");
                var hidden = jQuery("*[data-hidden-numcheck='" + numcheck + "']");
                label.html(jQuery(item).data(state));
                jQuery(label).removeClass(jQuery(item).data('class-' + (!(item.checked)).toString()));
                jQuery(label).addClass(jQuery(item).data('class-' + state));
                var val = jQuery(item).data('val-' + state);
                var field_name = jQuery(hidden).attr('name');
                jQuery(hidden).val(val);

                if (trigger_target.length) {
                    jQuery(this).trigger("check_changed", [trigger_target, field_name, item.checked, val, numcheck]);
                    jQuery('#' + trigger_target).trigger("check_changed");//for any single switchers
                }
            });

        });

        //***
        jQuery("#advanced-table .js-check-change").off('check_changed');
        jQuery("#advanced-table .js-check-change").on("check_changed", function (event, trigger_target, field_name, is_checked, val, product_id) {
            woobe_message(lang.saving, '');

            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: field_name,
                    value: val
                },
                success: function () {
                    jQuery(document).trigger('woobe_page_field_updated', [parseInt(product_id, 10), field_name, val]);
                    woobe_message(lang.saved, 'notice');
                },
                error: function () {
                    alert(lang.error);
                }
            });
        });

}
}

/**************************************************************************/


function woobe_set_progress(id, width) {
    if (jQuery('#' + id).length > 0) {
        jQuery('#' + id).parents('.woobe_progress').show();
        document.getElementById(id).style.width = width + '%';
        document.getElementById(id).innerHTML = width.toFixed(2) + '%';
    }
}

function woobe_hide_progress(id) {
    if (jQuery('#' + id).length > 0) {
        woobe_set_progress(id, 0);
        jQuery('#' + id).parents('.woobe_progress').hide();
    }
}

//attach event for any manipulations with content of the tabs by their id
function woobe_init_js_intab(tab_id) {
    jQuery(document).trigger("do_" + tab_id);
    jQuery(document).trigger("tab_switched", [tab_id]);
    return true;
}


function woobe_get_from_storage(key) {
    if (typeof (Storage) !== "undefined") {
        return localStorage.getItem(key);
    }

    return 0;
}

function woobe_set_to_storage(key, value) {
    if (typeof (Storage) !== "undefined") {
        localStorage.setItem(key, value);
        return key;
    }

    return 0;
}

function woobe_save_form(form, action) {
    woobe_message(lang.saving, 'warning');
    jQuery('[type=submit]').replaceWith('<img src="' + spinner + '" width="60" alt="" />');
    var data = {
        action: action,
        formdata: jQuery(form).serialize()
    };
    jQuery.post(ajaxurl, data, function () {
        window.location.reload();
    });
}


//give info about to which products will be applied bulk edition
function __woobe_action_will_be_applied_to() {
    //woobe_action_will_be_applied_to
    if (woobe_checked_products.length) {
        //high priority
        jQuery('.woobe_action_will_be_applied_to').html(lang.action_state_31 + ': ' + woobe_checked_products.length + '. ' + lang.action_state_32);
    } else {
        if (woobe_filtering_is_going) {
            //if there is filtering going
            jQuery('.woobe_action_will_be_applied_to').html(lang.action_state_2);
        } else {
            //no filtering and no checked products
            jQuery('.woobe_action_will_be_applied_to').html(lang.action_state_1);
        }
    }
}

function woobe_get_random_string(len = 16) {
    var charSet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var randomString = '';
    for (var i = 0; i < len; i++) {
        var randomPoz = Math.floor(Math.random() * charSet.length);
        randomString += charSet.substring(randomPoz, randomPoz + 1);
    }

    return randomString;
}


function __woobe_fill_select(select_id, data, selected = [], level = 0, val_as_slug = false) {

    var margin_string = '';
    if (level > 0) {
        for (var i = 0; i < level; i++) {
            margin_string += '&nbsp;&nbsp;&nbsp;';
        }
    }

    //***

    jQuery(data).each(function (i, d) {
        var sel = '';
        var val = d.term_id;
        if (val_as_slug) {
            val = d.slug;
        }

        //***

        if (jQuery.inArray(val, selected) > -1) {
            sel = 'selected';
        }
        jQuery('#' + select_id).append('<option ' + sel + ' value="' + val + '">' + margin_string + d.name + '</option>');
        if (d.childs) {
            __woobe_fill_select(select_id, d.childs, selected, level + 1, val_as_slug);
        }
    });
}


function woobe_init_profiles() {
    jQuery('#woobe_load_profile').on('change',function () {

        var profile_key = jQuery(this).val();
        if (profile_key != 0) {
            jQuery('#woobe_load_profile_actions').show();
        } else {
            jQuery('#woobe_load_profile_actions').hide();
        }

    });

    //***

    jQuery('#woobe_load_profile_btn').on('click', function () {

        var profile_key = jQuery('#woobe_load_profile').val();

        jQuery('.woobe-modal-close8').trigger('click');

        if (profile_key != 0) {
            woobe_message(lang.loading, 'warning');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_load_profile',
                    profile_key: profile_key
                },
                success: function (answer) {
                    woobe_message(lang.loading, 'warning');
                    window.location.reload();
                }
            });
        }

    });

    //***

    jQuery('#woobe_new_profile_btn').on('click', function () {
        var profile_title = jQuery('#woobe_new_profile').val();
        if (profile_title.length) {
            woobe_message(lang.creating, 'warning');
            jQuery('#woobe_new_profile').val('');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_create_profile',
                    profile_title: profile_title
                },
                success: function (key) {
                    if (parseInt(key, 10) !== -2) {
                        jQuery('#woobe_load_profile').append('<option selected value="' + key + '">' + profile_title + '</option>');
                        woobe_message(lang.saved, 'notice');
                    } else {
                        alert(lang.free_ver_profiles);
                        woobe_message('', 'clean');
                    }
                }
            });
        } else {
            woobe_message(lang.fill_up_data, 'warning');
        }
    });

    jQuery('#woobe_new_profile').keydown(function (e) {
        if (e.keyCode == 13) {
            jQuery('#woobe_new_profile_btn').trigger('click');
        }
    });

    //***

    jQuery('.woobe_delete_profile').on('click', function () {

        var profile_key = jQuery(this).attr('href');
        if (profile_key === '#') {
            profile_key = jQuery('#woobe_load_profile').val();
        }

        if (profile_key == 'default') {
            woobe_message(lang.no_deletable, 'warning');
            return false;
        }

        //***

        if (confirm(lang.sure)) {
            woobe_message(lang.saving, 'warning');
            var select = document.getElementById('woobe_load_profile');
            select.removeChild(select.querySelector('option[value="' + profile_key + '"]'));
            jQuery('.current_profile_disclaimer').remove();
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_delete_profile',
                    profile_key: profile_key
                },
                success: function (key) {
                    woobe_message(lang.saved, 'notice');
                }
            });
        }
        return false;
    });

}

function woobe_disable_bind_editing() {
    if (woobe_bind_editing) {
        jQuery("[data-numcheck='woobe_bind_editing']").trigger('click');
        woobe_bind_editing = 0;
    }
}

//service
function __trigger_resize() {

    setTimeout(function () {
        window.dispatchEvent(new Event('resize'));
    }, 10);
}
