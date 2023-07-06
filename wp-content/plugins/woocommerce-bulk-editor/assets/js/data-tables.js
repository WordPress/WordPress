"use strict";

var woobe_sort_order = [];
var data_table = null;
var products_types = null;//data got from server
var products_titles = null;//data got from server
var woobe_show_variations = 0;//show or hide variations of the variable products
var autocomplete_request_delay = 999;
var autocomplete_curr_index = -1;//for selecting by Enter button

//***

jQuery(function ($) {
    if (typeof jQuery.fn.DataTable !== 'undefined') {
        //woobe_show_variations = woobe_get_from_storage('woobe_show_variations');// - disabled because not sure that it will be right for convinience

        //hiding not relevant filter and bulk operations
        if (woobe_show_variations > 0) {
            jQuery('.not-for-variations').hide();
            jQuery('#woobe_show_variations_mode').show();
            jQuery('#woobe_show_variations_mode_export').show();

            jQuery('#woobe_select_all_vars').show();
        }



        //***

        init_data_tables();//data tables

        //***
        //fix to close opened textinputs in the data table
        jQuery('#tabs-products *').on('mousedown',function (e) {
            if (typeof e.srcElement !== 'undefined' && !jQuery(e.srcElement).hasClass('editable')) {
                if (!jQuery(e.srcElement).parent().hasClass('editable')) {
                    woobe_close_prev_textinput();
                }
            }
            return true;
        });

        //***

        jQuery('body').on('click', '.woobe-id-permalink-var', function () {

            if (woobe_show_variations) {
                jQuery(this).parents('tr').nextAll('tr').each(function (ii, tr) {
                    if (jQuery(tr).hasClass('product_type_variation')) {
                        jQuery(tr).find('.woobe_product_check').prop('checked', true);
                        woobe_checked_products.push(parseInt(jQuery(tr).data('product-id'), 10));
                    } else {
                        return false;//terminate tr's selection
                    }
                });

                //remove duplicates if exists
                woobe_checked_products = Array.from(new Set(woobe_checked_products));
                __manipulate_by_depend_buttons();
                __woobe_action_will_be_applied_to();
                return false;
            }

            return true;
        });

        //***

        jQuery('#woobe_select_all_vars').on('click', function () {

            jQuery('tr.product_type_variation').each(function (ii, tr) {
                jQuery(tr).find('.woobe_product_check').prop('checked', true);
                woobe_checked_products.push(parseInt(jQuery(tr).data('product-id'), 10));
            });

            //remove duplicates if exists
            woobe_checked_products = Array.from(new Set(woobe_checked_products));
            __manipulate_by_depend_buttons();
            __woobe_action_will_be_applied_to();

            return false;
        });

        //***
        //fix for applying coloring css styles for stock status drop-downs and etc ...
        jQuery('body').on('change', 'td.editable .select-wrap select', function () {
            jQuery(this).attr('data-selected', jQuery(this).val());
            return true;
        });

    }
});



var do_data_tables_first = true;
function init_data_tables() {
    var oTable = jQuery('#advanced-table');

    var page_fields = oTable.data('fields');
    var page_fields_array = page_fields.split(',');

    var edit_views = oTable.data('edit-views');
    var edit_views_array = edit_views.split(',');

    var edit_sanitize = oTable.data('edit-sanitize');
    var edit_sanitize_array = edit_sanitize.split(',');

    var start_page = oTable.data('start-page');
    //var ajax_additional = oTable.data('additional');
    var per_page = parseInt(oTable.data('per-page'), 10);
    var extend_per_page = oTable.data('extend-per-page');
    var length_menu = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100];

    if (extend_per_page.length > 0) {
        length_menu = extend_per_page.split(',');
    }

    if (woobe_settings.show_notes) {
        length_menu = [5, 10];
    }

    //https://datatables.net/examples/advanced_init/dt_events.html
    data_table = oTable.on('order.dt', function () {
        jQuery('.woobe_tools_panel_uncheck_all').trigger('click');
    }).DataTable({
       // dom: 'Bfrtip',
        //https://tunatore.wordpress.com/2012/02/11/datatables-jquert-pagination-on-both-top-and-bottom-solution-if-you-use-bjqueryui/
        //sDom: '<"H"Bflrp>t<"F"ip>',
        sDom: '<"H"Blpr>t<"F"ip>',
	searching: false,
        orderClasses: false,
        scrollX: true,
	lengthChange: true,
        lengthMenu: length_menu,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        //https://datatables.net/examples/basic_init/table_sorting.html
        order: [[oTable.data('default-sort-by'), oTable.data('sort')]],
        //https://stackoverflow.com/questions/12008545/disable-sorting-on-last-column-when-using-jquery-datatables/22714994#22714994
        aoColumnDefs: [{
                bSortable: false,
                //aTargets: [-1] /* 1st one, start by the right */
                aTargets: (oTable.data('no-order')).toString().split(',').map(function (num) {
                    return parseInt(num, 10);
                })
            }, {className: "editable", targets: (oTable.data('editable')).toString().split(',').map(function (num) {
                    return parseInt(num, 10);
                })}],
        createdRow: function (row, data, dataIndex) {

            var p_id = data[1];//data[1] is ID col
            p_id = jQuery(p_id).text();//!! important as we have link <a> in ID cell
            jQuery(row).attr('data-product-id', p_id);
            jQuery(row).attr('id', 'product_row_' + p_id);
            jQuery(row).attr('data-row-num', dataIndex);
            jQuery(row).addClass('product_type_' + products_types[p_id]);

            //***

            jQuery.each(jQuery('td', row), function (colIndex) {
                jQuery(this).attr('onmouseover', 'woobe_td_hover(' + p_id + ', "' + products_titles[p_id] + '", ' + colIndex + ')');
                jQuery(this).attr('onmouseleave', 'woobe_td_hover(0, "",0)');

                //***

                jQuery(this).attr('data-field', page_fields_array[colIndex]);
                jQuery(this).attr('data-editable-view', edit_views_array[colIndex]);
                jQuery(this).attr('data-sanitize', edit_sanitize_array[colIndex]);
                jQuery(this).attr('data-col-num', colIndex);
                if (edit_views_array[colIndex] == 'url') {
                    jQuery(this).addClass('textinput_url');
                }
                if (edit_views_array[colIndex] == 'textinput' || edit_views_array[colIndex] == 'url') {
                    jQuery(this).addClass('textinput_col');
                    jQuery(this).attr('onclick', 'woobe_click_textinput(this, ' + colIndex + ')');
                    //jQuery(this).attr('title', 'test');
                }

                if (edit_sanitize_array[colIndex] == 'floatval' || edit_sanitize_array[colIndex] == 'intval') {
                    jQuery(this).attr('onmouseover', 'woobe_td_hover(' + p_id + ', "' + products_titles[p_id].replaceAll('"', '') + '", ' + colIndex + ');woobe_onmouseover_num_textinput(this, ' + colIndex + ');');
                    jQuery(this).attr('data-product-id', p_id);
                } else {
                    jQuery(this).attr('onmouseout', 'woobe_td_hover(0, "",0);woobe_onmouseout_num_textinput();');
                }

                //***
                //remove class editable in cells which are not editable
                if (jQuery(this).find('.info_restricked').length > 0) {
                    jQuery(this).removeClass('editable');
                }
            });

        },
        processing: true,
        serverSide: true,
        bDeferRender: true,
        deferRender: true,
        //https://datatables.net/manual/server-side
        //https://datatables.net/examples/data_sources/server_side.html
        //ajax: ajaxurl + '?action=woobe_get_products',
        ajax: {
            url: ajaxurl,
            type: "POST",
            bDeferRender: true,
            deferRender: true,
            data: {
                action: 'woobe_get_products',
                woobe_show_variations: function () {
                    return woobe_show_variations;//we use function to return actual value for the current moment
                },
                filter_current_key: function () {
                    return woobe_filter_current_key;//we use function to return actual value for the current moment
                },
                lang: woobe_lang
            }
        },
        searchDelay: 100,
        pageLength: per_page,
        displayStart: start_page > 0 ? (start_page - 1) * per_page : 0,
        oLanguage: {
            sEmptyTable: lang.sEmptyTable,
            sInfo: lang.sInfo,
            sInfoEmpty: lang.sInfoEmpty,
            sInfoFiltered: lang.sInfoFiltered,
            sLoadingRecords: lang.sLoadingRecords,
            sProcessing: lang.sProcessing,
            sZeroRecords: lang.sZeroRecords,
            oPaginate: {
                sFirst: lang.sFirst,
                sLast: lang.sLast,
                sNext: lang.sNext,
                sPrevious: lang.sPrevious
            }
        },
	language: {
	     lengthMenu: " _MENU_ "
	},
        fnPreDrawCallback: function (a) {

            if (typeof a.json != 'undefined') {
                //console.log(a.json.query);
                products_types = a.json.products_types;
                products_titles = a.json.products_titles;
            }
            //console.log(products_types);
            woobe_message(lang.loading, '', 300000);
        },
        fnDrawCallback: function () {

            do_data_tables_first = false;

            init_data_tables_edit();
            jQuery('.all_products_checker').prop('checked', false);
            __manipulate_by_depend_buttons(false);
            woobe_message('', 'clean');
            woobe_init_special_variation();
            woobe_init_scroll();


            jQuery('.woobe_product_check').each(function (ii, ch) {
                if (jQuery.inArray(parseInt(jQuery(ch).data('product-id'), 10), woobe_checked_products) != -1) {
                    jQuery(ch).prop('checked', true);
                }
            });


            __manipulate_by_depend_buttons();
            jQuery(document).trigger("data_redraw_done");

            //page jumper is here
            start_page = (this.fnSettings()._iDisplayStart / this.fnSettings()._iDisplayLength) + 1;

            jQuery("#advanced-table_paginate .paginate_button.next").after('<input type="number" id="woobe-page-jumper" min=1 class="" value="' + start_page + '" />');

            var _this = this;
            jQuery("#woobe-page-jumper").off().on('keyup', function (e) {
                if (e.keyCode === 13) {
                    var pp = jQuery(this).val() - 1;
                    if (pp < 0) {
                        pp = 0;
                        jQuery(this).val(1);
                    }
                    _this.fnPageChange(pp, true);
                }
            });

            //for on the input arrows clicks
            jQuery("#woobe-page-jumper").off().on('change', function (e) {
                var pp = jQuery(this).val() - 1;
                if (pp < 0) {
                    pp = 0;
                    jQuery(this).val(1);
                }
                _this.fnPageChange(pp, true);
            });
            //***

            __trigger_resize();
        }
    });
    //jQuery(data_table)

    jQuery("#advanced-table_paginate").on("click", "a", function () {
        //var info = table.page.info();
        //*** if remove next row - checked products will be stay checked even after page changing
        woobe_checked_products = [];

    });


    //https://stackoverflow.com/questions/5548893/jquery-datatables-delay-search-until-3-characters-been-typed-or-a-button-clicke
    jQuery(".dataTables_filter input")
            .off()
            .on('keyup change', function (e) {
                if (e.keyCode == 13/* || this.value == ""*/) {
                    data_table.search(this.value).draw();
                }
            });

    //to left/right scroll buttons init


}


function init_data_tables_edit(product_id = 0) {

    if (product_id === 0) {
        //for multi-select drop-downs - disabled as take a lot of resources while loading page
        //replaced to init by woobe_multi_select_onmouseover(this)
        if (jQuery('.woobe_data_select').length) {
            if (jQuery("#advanced-table .chosen-select").length) {
                //jQuery("#advanced-table .chosen-select").chosen(/*{disable_search_threshold: 10}*/);
            }
        }

        //***
        //popup for taxonomies
        /*
         if (jQuery('.js_woobe_tax_popup').length) {
         jQuery.woobe_mod = jQuery.woobe_mod || {};
         
         jQuery.woobe_mod.popup_prepare = function () {
         new jQuery.woobe_popup_prepare('.js_woobe_tax_popup');
         };
         
         jQuery.woobe_mod.popup_prepare();
         }
         */

    }

    //***

    if (woobe_settings.load_switchers) {
        woobe_init_switchery(true, product_id);
    }

    __manipulate_by_depend_buttons();
    __woobe_action_will_be_applied_to();
}

var woobe_clicked_textinput_prev = [];//flag to track opened textinputs and close them
function woobe_click_textinput(_this, colIndex) {

    if (jQuery(_this).find('.editable_data').length > 0) {
        return false;
    }

    if (!jQuery(_this).hasClass('editable')) {
        return false;
    }

    //***
    //lest close previous opened any textinput/area
    woobe_close_prev_textinput();
    woobe_clicked_textinput_prev = [_this, colIndex];

    //***
    /*
     if (jQuery(_this).hasClass('textinput_url')) {
     var content = jQuery(_this).html();
     } else {
     var content = jQuery(_this).find('a').html();
     }
     */
    var content = jQuery(_this).html();

    //***

    var product_id = jQuery(_this).parents('tr').data('product-id');
    //var edit_view = jQuery(_this).data('editable-view');


    if (jQuery(_this).find('.info_restricked').length > 0) {
        return;
    }

    //***
    //fix to avoid editing titles of variable products
    if (jQuery(_this).data('editable-view') == 'textinput' && jQuery(_this).data('field') == 'post_title') {
        if (jQuery(_this).parents('tr').hasClass('product_type_variation')) {
            return;
        }
    }

    //***

    var input_type = 'text';

    if (jQuery(_this).data('sanitize') == 'intval' || jQuery(_this).data('sanitize') == 'floatval') {
        content = content.replace(/\,/g, "");
        input_type = 'number';
    }

    //inserting input into td cell
    if (input_type == 'text') {
        jQuery(_this).html('<textarea class="form-control input-sm editable_data">' + content + '</textarea>');
    } else {
        jQuery(_this).html('<input type="' + input_type + '" value="' + content + '" class="form-control input-sm editable_data" />');
    }

    var v = jQuery(_this).find('.editable_data').val();//set focus to the end
    jQuery(_this).find('.editable_data').focus().val("").val(v).select();

    woobe_th_width_synhronizer(colIndex, jQuery(_this).width());

    //***

    jQuery(_this).find('.editable_data').keydown(function (e) {

        var input = this;
        //38 - up, 40 - down, 13 - enter, 18 - ALT
        if (jQuery.inArray(e.keyCode, [13/*, 18*/, 38, 40]) > -1) { // keyboard keys
            e.preventDefault();
            if (content !== jQuery(input).val()) {
                //console.log(jQuery(_this).data('field'));
                //console.log(jQuery(input).val());
                woobe_message(lang.saving, '');
                jQuery(_this).html(jQuery(input).val());
                jQuery.ajax({
                    method: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'woobe_update_page_field',
                        product_id: product_id,
                        field: jQuery(_this).data('field'),
                        value: jQuery(input).val()
                    },
                    success: function (answer) {
                        //console.log(answer);
                        /*
                         if (jQuery(_this).hasClass('textinput_url')) {
                         answer = '<a href="' + answer + '" title="' + answer + '" class="zebra_tips1" target="_blank">' + answer + '</a>';
                         woobe_init_tips(jQuery(_this).find('.zebra_tips1'));
                         }
                         */
                        //***

                        jQuery(_this).html(answer);
                        woobe_message(lang.saved, 'notice');
                        woobe_th_width_synhronizer(colIndex, jQuery(_this).width());

                        //fix for stock_quantity + manage_stock
                        if (jQuery(_this).data('field') == 'stock_quantity') {
                            woobe_redraw_table_row(jQuery('#product_row_' + product_id));
                        }

                        jQuery('.woobe_num_rounding').val(0);
                        jQuery(document).trigger('woobe_page_field_updated', [product_id, jQuery(_this).data('field'), jQuery(input).val()]);
                    }
                });
            } else {
                jQuery(_this).html(content);
                woobe_th_width_synhronizer(colIndex, jQuery(_this).width());
            }

            //***
            //lets set focus to textinput under if its exists
            var col = jQuery(_this).data('col-num');
            switch (e.keyCode) {
                case 38:
                    //case 18://alt
                    //keys alt or up
                    if (jQuery(_this).closest('tr').prev('tr').length > 0) {
                        var prev_tr = jQuery(_this).closest('tr').prev('tr');
                    } else {
                        var prev_tr = jQuery(_this).closest('tbody').find('tr:last-child');
                    }
                    var c = jQuery(_this).closest('tbody').find('tr').length;
                    while (true) {
                        if (c < 0) {
                            break;
                        }
                        if (jQuery(prev_tr).find("td.editable[data-col-num='" + col + "']").length > 0) {
                            jQuery(prev_tr).find("td.editable[data-col-num='" + col + "']").trigger('click');
                            break;
                        }

                        if (jQuery(prev_tr).prev('tr').length) {
                            prev_tr = jQuery(prev_tr).prev('tr');
                        } else {
                            prev_tr = jQuery(_this).closest('tbody').find('tr:last-child');
                        }

                        c--;
                    }
                    woobe_th_width_synhronizer(colIndex, jQuery(_this).width());
                    break;

                default:
                    //13,40
                    //keys ENTER or down
                    if (jQuery(_this).closest('tr').next('tr').length > 0) {
                        var next_tr = jQuery(_this).closest('tr').next('tr');
                    } else {
                        var next_tr = jQuery(_this).closest('tbody').find('tr:first-child');
                    }
                    var c = jQuery(_this).closest('tbody').find('tr').length;
                    while (true) {
                        if (c < 0) {
                            break;
                        }
                        if (jQuery(next_tr).find("td.editable[data-col-num='" + col + "']").length > 0) {
                            jQuery(next_tr).find("td.editable[data-col-num='" + col + "']").trigger('click');
                            break;
                        }

                        if (jQuery(next_tr).next('tr').length) {
                            next_tr = jQuery(next_tr).next('tr');
                        } else {
                            next_tr = jQuery(_this).closest('tbody').find('tr:first-child');
                        }

                        c--;
                    }
                    woobe_th_width_synhronizer(colIndex, jQuery(_this).width());
                    break;
            }


            //***

            return false;
        }
        if (e.keyCode === 27) { // esc
            jQuery(_this).html(content);
            woobe_th_width_synhronizer(colIndex, jQuery(_this).width());
        }

    });

}




//if we have opened textinput and clcked another cell - previous textinput should be closed!!
function woobe_close_prev_textinput() {

    if (woobe_clicked_textinput_prev.length) {
        var prev = woobe_clicked_textinput_prev[0];

        if (jQuery(prev).find('input').length) {
            //jQuery(prev).html(jQuery(prev).find('input').val());
            jQuery(prev).find('input').trigger(jQuery.Event('keydown', {keyCode: 27}));
        } else {
            //jQuery(prev).html(jQuery(prev).find('textarea').val());
            jQuery(prev).find('textarea').trigger(jQuery.Event('keydown', {keyCode: 27}));
        }

        woobe_th_width_synhronizer(woobe_clicked_textinput_prev[1], jQuery(prev).width());
    }

    return true;
}


function woobe_click_checkbox(_this, numcheck) {

    var product_id = parseInt(numcheck, 10);
    var field = numcheck.replace(product_id + '_', '');
    var value = jQuery(_this).data('val-false');
    var label = jQuery(_this).data('false');

    var is = jQuery(_this).is(':checked');
    if (is) {
        value = jQuery(_this).data('val-true');
        label = jQuery(_this).data('true');
    }

    //***

    jQuery(_this).parent().find('label').text(label);

    //***

    woobe_message(lang.saving, 'warning');
    jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_update_page_field',
            product_id: product_id,
            field: field,
            value: value
        },
        success: function () {
            jQuery(document).trigger('woobe_page_field_updated', [product_id, field, is]);
            jQuery(this).trigger("check_changed", [_this, field, is, value, numcheck]);
            woobe_message(lang.saved, 'notice');
        }
    });

    return true;
}

//when appearing dynamic textinput in the table cell - column head <th> should has the same width!!
function woobe_th_width_synhronizer(colIndex, width) {
    //jQuery('#advanced-table_wrapper thead').find('th').eq(colIndex).width(width);
    //jQuery('#advanced-table_wrapper tfoot').find('th').eq(colIndex).width(width);
    //__trigger_resize();//conflict with calculator
}



function woobe_act_tax_popup(_this) {

    jQuery('#taxonomies_popup .woobe-modal-title').html(jQuery(_this).data('name') + ' [' + jQuery(_this).data('key') + ']');
    //fix to avoid not popup opening after taxonomies button clicking
    woobe_popup_clicked = jQuery(_this);

    //***

    var product_id = jQuery(_this).data('product-id');
    var key = jQuery(_this).data('key');//tax key
    var checked_terms_ids = [];

    if (jQuery(_this).data('terms-ids').toString().length > 0) {

        checked_terms_ids = jQuery(_this).data('terms-ids').toString().split(',');

        checked_terms_ids = checked_terms_ids.map(function (x) {
            return parseInt(x, 10);
        });
    }

    //lets build terms tree
    jQuery('#taxonomies_popup_list').html('');
    if (Object.keys(taxonomies_terms[key]).length > 0) {
        __woobe_fill_terms_tree(checked_terms_ids, taxonomies_terms[key]);
    }

    jQuery('.quick_search_element').show();
    jQuery('.quick_search_element_container').show();
    jQuery('#taxonomies_popup').show();

    //***

    jQuery('.woobe-modal-save1').off('click');
    jQuery('.woobe-modal-save1').on('click', function () {
        jQuery('#taxonomies_popup').hide();
        var checked_ch = jQuery('#taxonomies_popup_list').find('input:checked');
        var checked_terms = [];

        jQuery(_this).find('ul').html('');

        if (checked_ch.length) {
            jQuery(checked_ch).each(function (i, ch) {
                checked_terms.push(jQuery(ch).val());
                jQuery(_this).find('ul').append('<li class="woobe_li_tag">' + jQuery(ch).parent().find('label').text() + '</li>');
            });
        } else {
            jQuery(_this).find('ul').append('<li class="woobe_li_tag">' + lang.no_items + '</li>');
        }

        //***

        jQuery(_this).data('terms-ids', checked_terms.join());

        //***

        woobe_message(lang.saving, 'warning');
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_update_page_field',
                product_id: product_id,
                field: key,
                value: checked_terms
            },
            success: function () {
                jQuery(document).trigger('woobe_page_field_updated', [product_id, key, checked_terms]);
                woobe_message(lang.saved, 'notice');
            }
        });
    });

    jQuery('.woobe-modal-close1').off('click');
    jQuery('.woobe-modal-close1').on('click', function () {
        jQuery('#taxonomies_popup').hide();
    });


    //***
    //terms quick search
    jQuery('#term_quick_search').off('keyup');
    jQuery('#term_quick_search').val('');
    jQuery('#term_quick_search').focus();
    jQuery('#term_quick_search').on('keyup',function () {
        var val = jQuery(this).val();
        if (val.length > 0) {
            setTimeout(function () {
                jQuery('.quick_search_element_container').show();

                jQuery('.quick_search_element_container').each(function (i, item) {
                    if (!(jQuery(item).parent().data('search-value').toString().indexOf(val.toLowerCase()) + 1)) {
                        jQuery(item).hide();
                    } else {
                        jQuery(item).show();
                    }
                });


                jQuery('.quick_search_element_container:not(:hidden)').each(function (i, item) {
                    jQuery(item).parents('li').children('.quick_search_element_container').show();
                });


            }, 250);
        } else {
            jQuery('.quick_search_element_container').show();
        }

        return true;
    });

    //***
    jQuery('#taxonomies_popup_list_checked_only').off('click');
    jQuery('#taxonomies_popup_list_checked_only').prop('checked', false);
    jQuery('#taxonomies_popup_list_checked_only').on('click', function () {
        check_popup_list_checked_only(this);
    });

    function check_popup_list_checked_only(_this) {
        if (jQuery(_this).is(':checked')) {

            jQuery('#taxonomies_popup_list li.top_quick_search_element').each(function (i, item) {
                if (!jQuery(item).find('input:checked').length) {
                    jQuery(item).hide();
                } else {
                    jQuery(item).show();
                    jQuery(item).find('li').each(function (ii, it) {
                        if (!jQuery(it).find('ul.woobe_child_taxes').length && !jQuery(it).find('input:checked').length) {
                            jQuery(it).hide();
                        }
                    });
                }
            });

        } else {
            jQuery('#taxonomies_popup_list li').show();
        }

        return true;
    }

    //***


    jQuery('#taxonomies_popup_select_all_terms').off('click');
    jQuery('#taxonomies_popup_select_all_terms').prop('checked', false);
    jQuery('#taxonomies_popup_select_all_terms').on('click', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#taxonomies_popup_list li input[type="checkbox"]').prop('checked', true);
        } else {
            jQuery('#taxonomies_popup_list li input[type="checkbox"]').prop('checked', false);
        }
        check_popup_list_checked_only(jQuery('#taxonomies_popup_list_checked_only'));
    });

    //***

    jQuery('.woobe_create_new_term').off('click');
    jQuery('.woobe_create_new_term').on('click', function () {
        __woobe_create_new_term(key, true, '', _this);
        return false;
    });
    //delete terms
    jQuery('.delete_tax_terms').off('click');
    jQuery('.delete_tax_terms').on('click', function(e){
	var term_id = jQuery(this).data('term_id');
	if (!term_id ) {
	    return false;
	}
	__woobe_delete_tax_term(key, term_id);
    }); 
    //update terms
    jQuery('.edit_tax_terms').off('click');
    jQuery('.edit_tax_terms').on('click', function(e){
	var term_id = jQuery(this).data('term_id');
	if (!term_id ) {
	    return false;
	}
	__woobe_update_tax_term(key, term_id, _this);
    });    
    

    return true;
}
function __woobe_recursive_search(terms, term_id){
    var current_val = {};
    jQuery(terms).each(function (i, d) {
	if (d.term_id == term_id) {
	    current_val = d;
	    return false;
	}
	if(d.childs.length) {
	    current_val = __woobe_recursive_search(d.childs, term_id);
	    if (Object.keys(current_val).length) {
		return false;
	    }
	    
	}
    });
    return current_val;
}
function __woobe_delete_tax_term(tax_key, term_id) {
    if (typeof taxonomies_terms[tax_key] == 'undefined') {
	return false;
    }
    if (!confirm(lang.sure)) {
	return false;
    }

    woobe_message(lang.delete, 'warning', 99999);
    jQuery.ajax({
	method: "POST",
	url: ajaxurl,
	data: {
	    action: 'woobe_delete_tax_term',
	    term_id: term_id,
	    tax_key: tax_key
	},
	success: function (response) {

	    response = JSON.parse(response);
	    
	    jQuery('input#term_' + term_id).parent('.quick_search_element_container').parent('li.quick_search_element').remove();

	    if (response.length > 0) {
		woobe_message(lang.deleted, 'notice');
		taxonomies_terms[tax_key] = response;

		jQuery(document).trigger("taxonomy_data_redrawn", [tax_key, response.term_id]);
	    } else {
		woobe_message(lang.error + ' ' + lang.term_maybe_exist, 'error');
	    }

	}
    });

    //***

    jQuery('.woobe-modal-close9').trigger('click');

}
function __woobe_update_tax_term(tax_key, term_id, popup) {
    if (typeof taxonomies_terms[tax_key] == 'undefined') {
	return false;
    }
    var show_parent = true;
    var current_term = {};
    var current_index = -1;

    current_term = __woobe_recursive_search(taxonomies_terms[tax_key], term_id);

    if (!Object.keys(current_term).length) {
	return false;
    }

    jQuery('#woobe_new_term_popup .woobe-modal-title span').html(tax_key);
    jQuery('#woobe_new_term_title').val(current_term.name);
    jQuery('#woobe_new_term_slug').val(current_term.slug);
    jQuery('#woobe_new_term_description').val(current_term.desc);   
    if (show_parent ) {
        jQuery('#woobe_new_term_parent').parents('.woobe-form-element-container').show();

        jQuery('#woobe_new_term_parent').val('');
        jQuery('#woobe_new_term_parent').html('');

        if (Object.keys(taxonomies_terms[tax_key]).length > 0) {
            jQuery('#woobe_new_term_parent').append('<option value="-1">' + lang.none + '</option>');
            __woobe_fill_select('woobe_new_term_parent', taxonomies_terms[tax_key],[current_term.parent]);
        }

        //***

        jQuery('#woobe_new_term_parent').chosen({
            //disable_search_threshold: 10,
            width: '100%'
        }).trigger("chosen:updated");
    } else {
        jQuery('#woobe_new_term_parent').parents('.woobe-form-element-container').hide();
    }
    
    jQuery('#woobe_new_term_popup').show();

    jQuery('.woobe-modal-close9').on('click', function () {
        jQuery('#woobe_new_term_popup').hide();
    });   
    //***
    jQuery('#woobe_new_term_create').off('click');
    jQuery('#woobe_new_term_create').on('click', function () {
        var title = jQuery('#woobe_new_term_title').val();
        var slug = jQuery('#woobe_new_term_slug').val();
        var parent = jQuery('#woobe_new_term_parent').val();
	var description = jQuery('#woobe_new_term_description').val();
	
        if (title.length > 0) {
            woobe_message(lang.creating, 'warning', 99999);
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_tax_term',
		    term_id: term_id,
                    tax_key: tax_key,
                    title: title,
                    slug: slug,
		    description: description,
                    parent: parent
                },
                success: function (response) {

                    response = JSON.parse(response);
		    

                    if (response.length > 0) {
                        woobe_message(lang.created, 'notice');
                        taxonomies_terms[tax_key] = response;
			//redraw popup
			jQuery('.woobe-modal-close1').trigger('click');
			jQuery(popup).trigger('click');
			

                        jQuery(document).trigger("taxonomy_data_redrawn", [tax_key, response.term_id]);
			

                    } else {
                        woobe_message(lang.error + ' ' + lang.term_maybe_exist, 'error');
                    }

                }
            });

            //***

            jQuery('.woobe-modal-close9').trigger('click');
        }

        return false;
    });   
}
function __woobe_create_new_term(tax_key, show_parent = true, select_id = '', popup = null) {
    jQuery('#woobe_new_term_popup .woobe-modal-title span').html(tax_key);

    jQuery('#woobe_new_term_title').val('');
    jQuery('#woobe_new_term_slug').val('');
    jQuery('#woobe_new_term_description').val('');

    if (show_parent) {
        jQuery('#woobe_new_term_parent').parents('.woobe-form-element-container').show();

        jQuery('#woobe_new_term_parent').val('');
        jQuery('#woobe_new_term_parent').html('');

        if (Object.keys(taxonomies_terms[tax_key]).length > 0) {
            jQuery('#woobe_new_term_parent').append('<option value="-1">' + lang.none + '</option>');
            __woobe_fill_select('woobe_new_term_parent', taxonomies_terms[tax_key]);
        }

        //***

        jQuery('#woobe_new_term_parent').chosen({
            //disable_search_threshold: 10,
            width: '100%'
        }).trigger("chosen:updated");
    } else {
        jQuery('#woobe_new_term_parent').parents('.woobe-form-element-container').hide();
    }


    jQuery('#woobe_new_term_popup').show();

    jQuery('.woobe-modal-close9').on('click', function () {
        jQuery('#woobe_new_term_popup').hide();
    });

    //***
    jQuery('#woobe_new_term_create').off('click');
    jQuery('#woobe_new_term_create').on('click', function () {
        var title = jQuery('#woobe_new_term_title').val();
        var slug = jQuery('#woobe_new_term_slug').val();
        var parent = jQuery('#woobe_new_term_parent').val();
	var description = jQuery('#woobe_new_term_description').val();
        if (title.length > 0) {
            woobe_message(lang.creating, 'warning', 99999);
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_create_new_term',
                    tax_key: tax_key,
                    titles: title,
                    slugs: slug,
		    description: description,
                    parent: parent
                },
                success: function (response) {
                    response = JSON.parse(response);

                    if (response.terms_ids.length > 0) {
                        woobe_message(lang.created, 'notice');
                        taxonomies_terms[tax_key] = response.terms;

                        for (var i = 0; i < response.terms_ids.length; i++) {

                            var li = jQuery('#taxonomies_popup_list_li_tpl').html();
                            li = li.replace(/__TERM_ID__/gi, response.terms_ids[i]);
                            li = li.replace(/__LABEL__/gi, response.titles[i]);
                            li = li.replace(/__SEARCH_TXT__/gi, response.titles[i].toLowerCase());
                            li = li.replace(/__CHECK__/gi, 'checked');
                            if (parent == 0) {
                                li = li.replace(/__TOP_LI__/gi, 'top_quick_search_element');
                            } else {
                                li = li.replace(/__TOP_LI__/gi, '');
                            }
                            li = li.replace(/__CHILDS__/gi, '');
			    
                            jQuery('#taxonomies_popup_list').prepend(li);
			    
			    if (popup) {
				jQuery('#taxonomies_popup_list').find('.edit_tax_terms[data-term_id='+ response.terms_ids[i] +']').on('click', function(){
				    var term_id = jQuery(this).data('term_id');
				    if (!term_id ) {
					return false;
				    }
				    __woobe_update_tax_term(tax_key, term_id, popup);
				});
				jQuery('#taxonomies_popup_list').find('.delete_tax_terms[data-term_id='+ response.terms_ids[i] +']').on('click', function(){
				    var term_id = jQuery(this).data('term_id');
				    if (!term_id ) {
					return false;
				    }
				    __woobe_delete_tax_term(tax_key, term_id);
				});				
				
			    }
                        }

                        //***
                        //if we working with any drop-down
                        if (select_id.length > 0) {
                            for (var i = 0; i < response.terms_ids.length; i++) {
                                jQuery('#' + select_id).prepend('<option selected value="' + response.terms_ids[i] + '">' + response.titles[i] + '</option>');
                            }

                            //***

                            jQuery(jQuery('#' + select_id)).chosen({
                                width: '100%'
                            }).trigger("chosen:updated");
                        }

                        //***
                        //lets all BEAR extensions knows about this event
                        jQuery(document).trigger("taxonomy_data_redrawn", [tax_key, response.term_id]);
			
			
                    } else {
                        woobe_message(lang.error + ' ' + lang.term_maybe_exist, 'error');
                    }

                }
            });

            //***

            jQuery('.woobe-modal-close9').trigger('click');
        }

        return false;
    });

}


//service function to create terms tree in taxonomies popup
function __woobe_fill_terms_tree(checked_terms_ids, data, parent_term_id = 0) {

    var li_tpl = jQuery('#taxonomies_popup_list_li_tpl').html();

    //***
    jQuery(data).each(function (i, d) {
        var li = li_tpl;
        li = li.replace(/__TERM_ID__/gi, d.term_id);
        li = li.replace(/__LABEL__/gi, d.name);

        li = li.replace(/__SEARCH_TXT__/gi, d.name.toLowerCase());

        if (jQuery.inArray(d.term_id, checked_terms_ids) > -1) {
            li = li.replace(/__CHECK__/gi, 'checked');
        } else {
            li = li.replace(/__CHECK__/gi, '');
        }

        if (parent_term_id == 0) {
            li = li.replace(/__TOP_LI__/gi, 'top_quick_search_element');
        } else {
            li = li.replace(/__TOP_LI__/gi, '');
        }

        //***

        if (Object.keys(d.childs).length > 0) {
            li = li.replace(/__CHILDS__/gi, '<ul class="woobe_child_taxes woobe_child_taxes_' + d.term_id + '"></ul>');
        } else {
            li = li.replace(/__CHILDS__/gi, '');
        }

        //***

        if (parent_term_id == 0) {
            jQuery('#taxonomies_popup_list').append(li);
        } else {
            jQuery('#taxonomies_popup_list .woobe_child_taxes_' + parent_term_id).append(li);
        }


        if (d.childs) {
            __woobe_fill_terms_tree(checked_terms_ids, d.childs, d.term_id);
        }
    });

}

//use direct call only instead of attaching event to each element after page loading
//to up performance when a lot of product per page
function woobe_act_popupeditor(_this, post_parent) {

    jQuery('#popupeditor_popup .woobe-modal-title').html(jQuery(_this).data('name') + ' [' + jQuery(_this).data('key') + ']');
    woobe_popup_clicked = jQuery(_this);
    var product_id = jQuery(_this).data('product_id');
    var key = jQuery(_this).data('key');

    //***

    woobe_message(lang.loading, 'warning');
    jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_get_post_field',
            product_id: product_id,
            field: key,
            post_parent: post_parent
        },
        success: function (content) {

            woobe_message('', 'clean');

            jQuery('#popupeditor_popup').show();

            if (typeof tinyMCE != 'undefined') {
                try {
                    tinyMCE.get('popupeditor').setContent(content);
                    jQuery('.wp-editor-area').val(content);
                } catch (e) {
                    //fix if editor loaded not in rich mode
                    jQuery('.wp-editor-area').val(content);
                }
            }

            woobe_message(lang.loaded, 'notice');
        }
    });

    //***

    jQuery('.woobe-modal-save2').off('click');
    jQuery('.woobe-modal-save2').on('click', function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');

        jQuery('#popupeditor_popup').hide();
        woobe_message(lang.saving, 'warning');

        var content = '';

        /* try {
         content = tinyMCE.get('popupeditor').getContent();
         } catch (e) {
         //fix if editor loaded not in rich mode
         content = jQuery('.wp-editor-area').val();
         }*/

        //fix if editor loaded not in rich mode
        if (jQuery('.wp-editor-area').css('display') === 'none') {
            try {
                content = tinyMCE.get('popupeditor').getContent();
            } catch (e) {
                content = jQuery('.wp-editor-area').val();
            }
        } else {
            content = jQuery('.wp-editor-area').val();
        }
	//console.log(content);
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_update_page_field',
                product_id: product_id,
                field: key,
                value: content
            },
            success: function (content) {
                jQuery(document).trigger('woobe_page_field_updated', [product_id, key, content]);
		
		if (jQuery(_this).data('text-title')) {
		    let this_row = jQuery(_this).parents('tr');
		    woobe_redraw_table_row(this_row);		    
		}		
                woobe_message(lang.saved, 'notice');
            }
        });
    });

    jQuery('.woobe-modal-close2').off('click');
    jQuery('.woobe-modal-close2').on('click', function () {
        jQuery('#popupeditor_popup').hide();
    });


}

//use direct call only instead of attaching event to each element after page loading
//to up performance when a lot of product per page
function woobe_act_downloads_editor(_this) {

    var button = _this;
    jQuery('#downloads_popup_editor .woobe-modal-title').html(jQuery(_this).data('name') + ' [' + jQuery(_this).data('key') + ']');
    woobe_popup_clicked = jQuery(_this);
    var product_id = parseInt(jQuery(_this).data('product_id'), 10);
    var key = jQuery(_this).data('key');

    //***

    if (jQuery(_this).data('count') > 0 && product_id > 0) {

        var html = '';
        jQuery(jQuery(_this).data('downloads')).each(function (i, d) {
            var li_html = jQuery('#woobe_download_file_tpl').html();
            li_html = li_html.replace(/__TITLE__/gi, d.name);
            li_html = li_html.replace(/__HASH__/gi, d.id);
            li_html = li_html.replace(/__FILE_URL__/gi, d.file);
            html += li_html;
        });


        jQuery('#downloads_popup_editor form').html('<ul class="woobe_fields_tmp">' + html + '</ul>');
        jQuery('#downloads_popup_editor').show();
        jQuery('#woobe_downloads_bulk_operations').hide();
        __woobe_init_downloads();



        /*
         woobe_message(lang.loading, 'warning');
         jQuery.ajax({
         method: "POST",
         url: ajaxurl,
         data: {
         action: 'woobe_get_downloads',
         product_id: product_id,
         field: key
         },
         success: function (content) {
         woobe_message(lang.loaded, 'notice');
         jQuery('#downloads_popup_editor form').html(content);
         jQuery('#downloads_popup_editor').show();
         
         jQuery('#woobe_downloads_bulk_operations').hide();
         
         //***
         
         __woobe_init_downloads();
         }
         });
         
         */
    } else {

        if (product_id > 0) {
            jQuery('#downloads_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            jQuery('#woobe_downloads_bulk_operations').hide();
        } else {
            //this we need do for another applications, for example bulk editor
            if (jQuery('#downloads_popup_editor form .woobe_fields_tmp').length == 0) {
                jQuery('#downloads_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            }

            jQuery('#woobe_downloads_bulk_operations').show();
        }

        jQuery('#downloads_popup_editor').show();
        __woobe_init_downloads();
    }


    //***

    //init close and save buttons when first call of popup is done
    jQuery('.woobe-modal-save3').off('click');
    jQuery('.woobe-modal-save3').on('click', function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');


        if (product_id > 0) {
            jQuery('#downloads_popup_editor').hide();
            woobe_message(lang.saving, 'warning');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: key,
                    value: jQuery('#products_downloads_form').serialize()
                },
                success: function (html) {

                    woobe_message(lang.saved, 'notice');
                    jQuery('#downloads_popup_editor form').html('');
                    jQuery(button).parent().html(html);

                    jQuery(document).trigger('woobe_page_field_updated', [product_id, key, jQuery('#products_downloads_form').serialize()]);
                }
            });
        } else {
            //for downloads buttons in any extensions
            jQuery(document).trigger('woobe_act_downloads_editor_saved', [product_id, key, jQuery('#products_downloads_form').serialize()]);
        }

        return false;

    });


    jQuery('.woobe-modal-close3').off('click');
    jQuery('.woobe-modal-close3').on('click', function () {
        //jQuery('#downloads_popup_editor form').html(''); - do not do this, as it make incompatibility with another extensions
        jQuery('#downloads_popup_editor').hide();
        return false;
    });


    return false;
}


function woobe_act_gallery_editor(_this) {
    var button = _this;

    jQuery('#gallery_popup_editor .woobe-modal-title').html(jQuery(_this).data('name') + ' [' + jQuery(_this).data('key') + ']');
    woobe_popup_clicked = jQuery(_this);
    var product_id = parseInt(jQuery(_this).data('product_id'), 10);
    var key = jQuery(_this).data('key');

    //***

    if (jQuery(_this).data('count') > 0) {
        if (product_id > 0) {


            var html = '';
            jQuery(jQuery(_this).data('images')).each(function (i, a) {
                var li_html = jQuery('#woobe_gallery_li_tpl').html();
                li_html = li_html.replace(/__IMG_URL__/gi, a.url);
                li_html = li_html.replace(/__ATTACHMENT_ID__/gi, a.id);
                html += li_html;
            });

            jQuery('#gallery_popup_editor form').html('<ul class="woobe_fields_tmp">' + html + '</ul>');
            jQuery('#gallery_popup_editor').show();
            jQuery('#woobe_gallery_bulk_operations').hide();
            __woobe_init_gallery();


            /*
             woobe_message(lang.loading, 'warning');
             jQuery.ajax({
             method: "POST",
             url: ajaxurl,
             data: {
             action: 'woobe_get_gallery',
             product_id: product_id,
             field: key
             },
             success: function (content) {
             woobe_message(lang.loaded, 'notice');
             jQuery('#gallery_popup_editor form').html(content);
             jQuery('#gallery_popup_editor').show();
             
             jQuery('#woobe_gallery_bulk_operations').hide();
             
             //***
             
             __woobe_init_gallery();
             
             }
             });
             
             */
        } else {
            //we can use such button for any another extensions
            jQuery('#gallery_popup_editor').show();
            jQuery('#woobe_gallery_bulk_operations').show();
            __woobe_init_gallery();
        }

    } else {
        if (product_id > 0) {
            jQuery('#gallery_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            jQuery('#woobe_gallery_bulk_operations').hide();
        } else {
            //this we need do for another applications, for example bulk editor
            if (jQuery('#gallery_popup_editor form .woobe_fields_tmp').length == 0) {
                jQuery('#gallery_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            }
            jQuery('#woobe_gallery_bulk_operations').show();
        }


        jQuery('#gallery_popup_editor').show();
        __woobe_init_gallery();
    }


    //***


    jQuery('.woobe-modal-save4').off('click');
    jQuery('.woobe-modal-save4').on('click', function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');

        if (product_id > 0) {
            jQuery('#gallery_popup_editor').hide();
            woobe_message(lang.saving, 'warning');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: key,
                    value: jQuery('#products_gallery_form').serialize()
                },
                success: function (html) {

                    woobe_message(lang.saved, 'notice');
                    //jQuery('#gallery_popup_editor form').html('');
                    jQuery(button).parent().html(html);

                    jQuery(document).trigger('woobe_page_field_updated', [product_id, key, jQuery('#products_gallery_form').serialize()]);
                }
            });
        } else {
            //for gallery buttons in any extensions
            jQuery(document).trigger('woobe_act_gallery_editor_saved', [product_id, key, jQuery('#products_gallery_form').serialize()]);
        }


    });

    jQuery('.woobe-modal-close4').off('click');
    jQuery('.woobe-modal-close4').on('click', function () {
        //jQuery('#gallery_popup_editor form').html(''); - do not do this, as it make incompatibility with another extensions
        jQuery('#gallery_popup_editor').hide();
    });

    return false;
}


function woobe_act_upsells_editor(_this) {
    var button = _this;

    jQuery('#upsells_popup_editor .woobe-modal-title').html(jQuery(_this).data('name') + ' [' + jQuery(_this).data('key') + ']');
    woobe_popup_clicked = jQuery(_this);
    var product_id = parseInt(jQuery(_this).data('product_id'), 10);
    var key = jQuery(_this).data('key');

    //***

    var button_data = [];

    if (jQuery('#upsell_ids_upsell_ids_' + product_id + ' li').length > 0) {
        jQuery('#upsell_ids_upsell_ids_' + product_id + ' li').each(function (i, li) {
            button_data.push(jQuery(li).data('product'));
        });
    }

    //***

    if (jQuery(_this).data('count') > 0 && product_id > 0) {

        var html = '';
        jQuery(button_data).each(function (i, li) {
            var li_html = jQuery('#woobe_product_li_tpl').html();
            li_html = li_html.replace(/__ID__/gi, li.id);
            li_html = li_html.replace(/__TITLE__/gi, li.title + ' (#' + li.id + ')');
            li_html = li_html.replace(/__PERMALINK__/gi, li.link);
            li_html = li_html.replace(/__IMG_URL__/gi, li.thumb);
            html += li_html;
        });

        jQuery('#upsells_popup_editor form').html('<ul class="woobe_fields_tmp">' + html + '</ul>');
        jQuery("#upsells_products_search").val('');
        jQuery('#upsells_popup_editor').show();
        jQuery('#woobe_upsells_bulk_operations').hide();
        __woobe_init_upsells();

        /*
         woobe_message(lang.loading, 'warning');
         jQuery.ajax({
         method: "POST",
         url: ajaxurl,
         data: {
         action: 'woobe_get_upsells',
         product_id: product_id,
         field: key
         },
         success: function (content) {
         woobe_message(lang.loaded, 'notice');
         jQuery('#upsells_popup_editor form').html(content);
         jQuery("#upsells_products_search").val('');
         jQuery('#upsells_popup_editor').show();
         jQuery('#woobe_upsells_bulk_operations').hide();
         
         //***
         
         __woobe_init_upsells();
         }
         });
         
         */
    } else {
        jQuery("#upsells_products_search").val('');
        if (product_id > 0) {
            jQuery('#upsells_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            jQuery('#woobe_upsells_bulk_operations').hide();
        } else {
            //this we need do for another applications, for example bulk editor
            if (jQuery('#upsells_popup_editor form .woobe_fields_tmp').length == 0) {
                jQuery('#upsells_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            }
            jQuery('#woobe_upsells_bulk_operations').show();
        }

        jQuery('#upsells_popup_editor').show();
        __woobe_init_upsells();
    }

    //***


    jQuery('.woobe-modal-save5').off('click');
    jQuery('.woobe-modal-save5').on('click', function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');

        if (product_id > 0) {
            jQuery('#upsells_popup_editor').hide();
            woobe_message(lang.saving, 'warning');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: key,
                    value: jQuery('#products_upsells_form').serialize()
                },
                success: function (html) {

                    woobe_message(lang.saved, 'notice');
                    //jQuery('#upsells_popup_editor form').html('');
                    jQuery(button).parent().html(html);

                    jQuery(document).trigger('woobe_page_field_updated', [product_id, key, jQuery('#products_upsells_form').serialize()]);
                }
            });
        } else {
            //for buttons in any extensions
            jQuery(document).trigger('woobe_act_upsells_editor_saved', [product_id, key, jQuery('#products_upsells_form').serialize()]);
        }

        return false;
    });

    jQuery('.woobe-modal-close5').off('click');
    jQuery('.woobe-modal-close5').on('click', function () {
        //jQuery('#upsells_popup_editor form').html(''); - do not do this, as it make incompatibility with another extensions
        jQuery("#upsells_products_search").val('');
        jQuery('#upsells_popup_editor').hide();
        return false;
    });

}



function woobe_act_cross_sells_editor(_this) {
    var button = _this;

    jQuery('#cross_sells_popup_editor .woobe-modal-title').html(jQuery(_this).data('name') + ' [' + jQuery(_this).data('key') + ']');
    woobe_popup_clicked = jQuery(_this);
    var product_id = parseInt(jQuery(_this).data('product_id'), 10);
    var key = jQuery(_this).data('key');

    //***

    var button_data = [];

    if (jQuery('#cross_sells_cross_sell_ids_' + product_id + ' li').length > 0) {
        jQuery('#cross_sells_cross_sell_ids_' + product_id + ' li').each(function (i, li) {
            button_data.push(jQuery(li).data('product'));
        });
    }

    //***

    if (jQuery(_this).data('count') > 0 && product_id > 0) {
        var html = '';
        jQuery(button_data).each(function (i, li) {
            var li_html = jQuery('#woobe_product_li_tpl').html();
            li_html = li_html.replace(/__ID__/gi, li.id);
            li_html = li_html.replace(/__TITLE__/gi, li.title + ' (#' + li.id + ')');
            li_html = li_html.replace(/__PERMALINK__/gi, li.link);
            li_html = li_html.replace(/__IMG_URL__/gi, li.thumb);
            html += li_html;
        });

        jQuery('#cross_sells_popup_editor form').html('<ul class="woobe_fields_tmp">' + html + '</ul>');
        jQuery("#cross_sells_products_search").val('');
        jQuery('#cross_sells_popup_editor').show();
        jQuery('#woobe_crossels_bulk_operations').hide();
        __woobe_init_cross_sells();

        /*
         woobe_message(lang.loading, 'warning');
         jQuery.ajax({
         method: "POST",
         url: ajaxurl,
         data: {
         action: 'woobe_get_cross_sells',
         product_id: product_id,
         field: key
         },
         success: function (content) {
         woobe_message(lang.loaded, 'notice');
         jQuery('#cross_sells_popup_editor form').html(content);
         jQuery("#cross_sells_products_search").val('');
         jQuery('#cross_sells_popup_editor').show();
         jQuery('#woobe_crossels_bulk_operations').hide();
         
         //***
         
         __woobe_init_cross_sells();
         }
         });
         */

    } else {

        if (product_id > 0) {
            jQuery('#cross_sells_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            jQuery('#woobe_crossels_bulk_operations').hide();
        } else {
            //this we need do for another applications, for example bulk editor
            if (jQuery('#cross_sells_popup_editor form .woobe_fields_tmp').length == 0) {
                jQuery('#cross_sells_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            }

            jQuery('#woobe_crossels_bulk_operations').show();
        }

        jQuery("#cross_sells_products_search").val('');
        jQuery('#cross_sells_popup_editor').show();
        __woobe_init_cross_sells();
    }

    //***


    jQuery('.woobe-modal-save6').off('click');
    jQuery('.woobe-modal-save6').on('click', function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');

        if (product_id > 0) {
            jQuery('#cross_sells_popup_editor').hide();
            woobe_message(lang.saving, 'warning');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: key,
                    value: jQuery('#products_cross_sells_form').serialize()
                },
                success: function (html) {

                    woobe_message(lang.saved, 'notice');
                    //jQuery('#cross_sells_popup_editor form').html('');
                    jQuery(button).parent().html(html);

                    jQuery(document).trigger('woobe_page_field_updated', [product_id, key, jQuery('#products_cross_sells_form').serialize()]);
                }
            });
        } else {
            //for buttons in any extensions
            jQuery(document).trigger('woobe_act_cross_sells_editor_saved', [product_id, key, jQuery('#products_cross_sells_form').serialize()]);
        }

        return false;
    });

    jQuery('.woobe-modal-close6').off('click');
    jQuery('.woobe-modal-close6').on('click', function () {
        //jQuery('#cross_sells_popup_editor form').html(''); - do not do this, as it make incompatibility with another extensions
        jQuery("#cross_sells_products_search").val('');
        jQuery('#cross_sells_popup_editor').hide();
        return false;
    });

}


function woobe_act_grouped_editor(_this) {
    var button = _this;

    jQuery('#grouped_popup_editor .woobe-modal-title').html(jQuery(_this).data('name') + ' [' + jQuery(_this).data('key') + ']');
    woobe_popup_clicked = jQuery(_this);
    var product_id = parseInt(jQuery(_this).data('product_id'), 10);
    var key = jQuery(_this).data('key');

    //***

    var button_data = [];

    if (jQuery('#grouped_ids_grouped_ids_' + product_id + ' li').length > 0) {
        jQuery('#grouped_ids_grouped_ids_' + product_id + ' li').each(function (i, li) {
            button_data.push(jQuery(li).data('product'));
        });
    }

    //***

    if (jQuery(_this).data('count') > 0 && product_id > 0) {

        var html = '';
        jQuery(button_data).each(function (i, li) {
            var li_html = jQuery('#woobe_product_li_tpl').html();
            li_html = li_html.replace(/__ID__/gi, li.id);
            li_html = li_html.replace(/__TITLE__/gi, li.title + ' (#' + li.id + ')');
            li_html = li_html.replace(/__PERMALINK__/gi, li.link);
            li_html = li_html.replace(/__IMG_URL__/gi, li.thumb);
            html += li_html;
        });

        jQuery('#grouped_popup_editor form').html('<ul class="woobe_fields_tmp">' + html + '</ul>');
        jQuery("#grouped_products_search").val('');
        jQuery('#grouped_popup_editor').show();
        jQuery('#woobe_grouped_bulk_operations').hide();
        __woobe_init_grouped();


        /*
         woobe_message(lang.loading, 'warning');
         jQuery.ajax({
         method: "POST",
         url: ajaxurl,
         data: {
         action: 'woobe_get_grouped',
         product_id: product_id,
         field: key
         },
         success: function (content) {
         woobe_message(lang.loaded, 'notice');
         jQuery('#grouped_popup_editor form').html(content);
         jQuery("#grouped_products_search").val('');
         jQuery('#grouped_popup_editor').show();
         jQuery('#woobe_grouped_bulk_operations').hide();
         
         //***
         
         __woobe_init_grouped();
         }
         });
         
         */
    } else {
        if (product_id > 0) {
            jQuery('#grouped_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            jQuery('#woobe_grouped_bulk_operations').hide();
        } else {
            //this we need do for another applications, for example bulk editor
            if (jQuery('#grouped_popup_editor form .woobe_fields_tmp').length == 0) {
                jQuery('#grouped_popup_editor form').html('<ul class="woobe_fields_tmp"></ul>');
            }

            jQuery('#woobe_grouped_bulk_operations').show();
        }

        jQuery("#grouped_products_search").val('');
        jQuery('#grouped_popup_editor').show();
        __woobe_init_grouped();
    }


    //***


    jQuery('.woobe-modal-save7').off('click');
    jQuery('.woobe-modal-save7').on('click', function () {

        var product_id = woobe_popup_clicked.data('product_id');
        var key = woobe_popup_clicked.data('key');

        if (product_id > 0) {
            jQuery('#grouped_popup_editor').hide();
            woobe_message(lang.saving, 'warning');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: key,
                    value: jQuery('#products_grouped_form').serialize()
                },
                success: function (html) {

                    woobe_message(lang.saved, 'notice');
                    jQuery('#grouped_popup_editor form').html('');
                    jQuery(button).parent().html(html);

                    jQuery(document).trigger('woobe_page_field_updated', [product_id, key, jQuery('#products_grouped_form').serialize()]);
                }
            });

        } else {
            //for buttons in any extensions
            jQuery(document).trigger('woobe_act_grouped_editor_saved', [product_id, key, jQuery('#products_grouped_form').serialize()]);
        }

        return false;
    });

    jQuery('.woobe-modal-close7').off('click');
    jQuery('.woobe-modal-close7').on('click', function () {
        //jQuery('#grouped_popup_editor form').html(''); - do not do this, as it make incompatibility with another extensions
        jQuery("#grouped_products_search").val('');
        jQuery('#grouped_popup_editor').hide();
        return false;
    });

}


function woobe_act_select(_this) {
    woobe_message(lang.saving, '');
    var product_id = parseInt(jQuery(_this).data('product-id'), 10);
    jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_update_page_field',
            product_id: product_id,
            field: jQuery(_this).data('field'),
            value: jQuery(_this).val()
        },
        success: function (e) {
            jQuery(document).trigger('woobe_page_field_updated', [product_id, jQuery(_this).data('field'), jQuery(_this).val()]);
            woobe_message(lang.saved, 'notice');

            if (jQuery(_this).data('field') == 'product_type') {
                //redraw table row
                woobe_redraw_table_row(_this);
            }
        }
    });


    return false;

}

function woobe_redraw_table_row(row, do_trigger = true) {
    var product_id = parseInt(jQuery(row).data('product-id'), 10);

    if (!product_id) {
        return;
    }

    //***

    jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_redraw_table_row',
            product_id: product_id,
            field: jQuery(row).data('field'),
            value: jQuery(row).val()
        },
        success: function (row_data) {
            woobe_message(lang.saved, 'notice');
            var tr_index = jQuery('#product_row_' + product_id).data('row-num');
            data_table.row(tr_index).data(JSON.parse(row_data));

            jQuery.each(jQuery('td', jQuery('#product_row_' + product_id)), function (colIndex) {
                if (jQuery(this).find('.info_restricked').length > 0) {
                    jQuery(this).removeClass('editable');
                } else {
                    jQuery(this).addClass('editable');
                }
            });

            //***
            if (do_trigger) {
                jQuery(document).trigger('woobe_page_field_updated', [product_id, jQuery(row).data('field'), jQuery(row).val()]);
            }
            //woobe_checked_products.splice(woobe_checked_products.indexOf(product_id), 1);
            /*
             woobe_checked_products = jQuery.grep(woobe_checked_products, function (value) {
             return value != product_id;
             });
             */

            if (jQuery.inArray(product_id, woobe_checked_products) > -1) {
                jQuery('#product_row_' + product_id).find('.woobe_product_check').prop('checked', true);
            }

            init_data_tables_edit(product_id);
        }
    });
}

function woobe_init_calendar(calendar) {


    if (typeof jQuery(calendar).attr('data-dtp') !== typeof undefined && jQuery(calendar).attr('data-dtp') !== false) {
        return;
    }

    //***
    var format = "DD/MM/YYYY";
    var time = false;

    if (jQuery(calendar).data('time') == true) {
        format = 'DD/MM/YYYY HH:mm';
        time = true;
    }

    jQuery(calendar).bootstrapMaterialDatePicker({
        weekStart: 1,
        time: time,
        clearButton: false,
        //minDate: new Date(),
        format: format,
        autoclose: true,
        lang: 'en',
        title: jQuery(calendar).data('title'),
        icons: {
            time: "icofont icofont-clock-time",
            date: "icofont icofont-ui-calendar",
            up: "icofont icofont-rounded-up",
            down: "icofont icofont-rounded-down",
            next: "icofont icofont-rounded-right",
            previous: "icofont icofont-rounded-left"
        }
    }).on('change', function (e, date)
    {
        var hidden = jQuery('#' + jQuery(this).data('val-id'));
        if (typeof date != 'undefined') {
            var d = new Date(date);
            //hidden.val(parseInt(d.getTime() / 1000, 10));

            if (jQuery(this).data('time') == true) {
                hidden.val(d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate() + ' ' + d.getHours() + ":" + d.getMinutes() + ":00");
            } else {
                hidden.val(d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate());
            }
            //console.log(hidden.val())
        } else {
            //clear
            hidden.val(0);
        }

        //***
        var product_id = parseInt(hidden.data('product-id'), 10);
        if (product_id > 0) {
            woobe_message(lang.saving, '');

            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woobe_update_page_field',
                    product_id: product_id,
                    field: hidden.data('key'),
                    value: hidden.val()
                },
                success: function (e) {
                    //console.log(e);
                    jQuery(document).trigger('woobe_page_field_updated', [product_id, hidden.data('key'), hidden.val()]);
                    woobe_message(lang.saved, 'notice');
                }
            });
        }

    });



    //***

    jQuery(calendar).parents('td').find('.woobe_calendar_cell_clear').on('click', function () {
        jQuery(this).parent().find('.woobe_calendar').val('').trigger('change');
        return false;
    });


}

//redrawing of checkbox to switcher on onmouseover
//was in cycle but its make time of page redrawing longer, so been remade for individual initializating
function woobe_set_switchery(_this) {

    //http://abpetkov.github.io/switchery/
    if (typeof Switchery !== 'undefined') {
        new Switchery(_this);
        //while reinit allows more html switchers
        jQuery(_this).parent().find('span.switchery:not(:first)').remove();
    }

    //***

    jQuery(_this).off('change');
    jQuery(_this).on('change',function () {
        var state = _this.checked.toString();
        var numcheck = jQuery(_this).data('numcheck');
        var trigger_target = jQuery(_this).data('trigger-target');
        var label = jQuery("*[data-label-numcheck='" + numcheck + "']");
        var hidden = jQuery("*[data-hidden-numcheck='" + numcheck + "']");
        label.html(jQuery(_this).data(state));
        jQuery(label).removeClass(jQuery(_this).data('class-' + (!(_this.checked)).toString()));
        jQuery(label).addClass(jQuery(_this).data('class-' + state));
        var val = jQuery(_this).data('val-' + state);
        var field_name = jQuery(hidden).attr('name');
        jQuery(hidden).val(val);

        if (trigger_target.length) {
            jQuery(this).trigger("check_changed", [trigger_target, field_name, _this.checked, val, numcheck]);
        }
    });

    //***

    jQuery(_this).off('check_changed');
    jQuery(_this).on("check_changed", function (event, trigger_target, field_name, is_checked, val, product_id) {
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
            }
        });
    });



}

function woobe_act_thumbnail(_this) {
    var product_id = jQuery(_this).parents('tr').data('product-id');
    var field = jQuery(_this).parents('td').data('field');

    var image = wp.media({
        title: lang.upload_image,
        multiple: false,
        library: {
            type: ['image']
        }
    }).open()
            .on('select', function (e) {
                var uploaded_image = image.state().get('selection').first();
                // We convert uploaded_image to a JSON object to make accessing it easier
                uploaded_image = uploaded_image.toJSON();
                var uploaded_to = 0;
                if (uploaded_image.uploading != undefined || uploaded_image.uploading == false) {
                    uploaded_to = 1;
                }

                var img_url = uploaded_image.url;
                if (uploaded_image.sizes && uploaded_image.sizes.thumbnail) {
                    img_url = uploaded_image.sizes.thumbnail.url;
                }

                if (typeof uploaded_image.url != 'undefined') {
                    jQuery(_this).find('img').attr('src', img_url);
                    //jQuery(_this).removeAttr('srcset');

                    woobe_message(lang.saving, '');
                    jQuery.ajax({
                        method: "POST",
                        url: ajaxurl,
                        data: {
                            action: 'woobe_update_page_field',
                            product_id: product_id,
                            field: field,
                            value: uploaded_image.id,
                            uploaded_to: uploaded_to
                        },
                        success: function () {
                            jQuery(document).trigger('woobe_page_field_updated', [product_id, field, uploaded_image.id]);
                            woobe_message(lang.saved, 'notice');
                        }
                    });
                }
            });


    return false;

}

//service
function __woobe_init_downloads() {

    jQuery('.woobe_upload_file_button').off('click');
    jQuery('.woobe_upload_file_button').on('click', function ()
    {
        var input_object = jQuery(this).parents('tr').find('.woobe_down_file_url').eq(0);
        var image = wp.media({
            title: lang.upload_file,
            multiple: false
        })
        image.on('ready', function () { /* to add files  in woocommerce_uploads*/
            image.uploader.options.uploader.params = {
                type: 'downloadable_product'
            };
        });
        image.open()
                .on('select', function (e) {
                    var uploaded_image = image.state().get('selection').first();
                    // We convert uploaded_image to a JSON object to make accessing it easier
                    uploaded_image = uploaded_image.toJSON();
                    if (typeof uploaded_image.url != 'undefined') {
                        jQuery(input_object).val(uploaded_image.url);
                    }
                });

        return false;
    });

    //***

    jQuery("#downloads_popup_editor form .woobe_fields_tmp").sortable({
        update: function (event, ui) {
            //***
        },
        opacity: 0.8,
        cursor: "crosshair",
        handle: '.woobe_drag_and_drope',
        placeholder: 'woobe-options-highlight'
    });


    //***
    jQuery('.woobe_insert_download_file').off('click');
    jQuery('.woobe_insert_download_file').on('click', function () {

        var li_html = jQuery('#woobe_download_file_tpl').html();
        li_html = li_html.replace(/__TITLE__/gi, '');
        li_html = li_html.replace(/__HASH__/gi, '');
        li_html = li_html.replace(/__FILE_URL__/gi, '');

        if (jQuery(this).data('place') == 'top') {
            jQuery('#downloads_popup_editor form .woobe_fields_tmp').prepend(li_html);
        } else {
            jQuery('#downloads_popup_editor form .woobe_fields_tmp').append(li_html);
        }
        __woobe_init_downloads();

        return false;
    });


    jQuery('.woobe_down_file_delete').off('click');
    jQuery('.woobe_down_file_delete').on('click', function () {
        jQuery(this).parents('li').remove();
        return false;
    });

}

//service
function __woobe_init_gallery() {

    jQuery('.woobe_insert_gall_file').off('click');
    jQuery('.woobe_insert_gall_file').on('click', function (e)
    {
        e.preventDefault();

        var image = wp.media({
            title: lang.upload_images,
            multiple: true,
            //cache: 'refresh',
            library: {
                type: ['image'],
                //cache: false
            }
        }).open()
                .on('select', function (e) {
                    //var uploaded_images = image.state().get('selection').first();
                    var uploaded_images = image.state().get('selection');
                    // We convert uploaded_image to a JSON object to make accessing it easier
                    uploaded_images = uploaded_images.toJSON();
                    //console.log(uploaded_images);
                    if (uploaded_images.length) {
                        for (var i = 0; i < uploaded_images.length; i++) {
                            var html = jQuery('#woobe_gallery_li_tpl').html();
                            html = html.replace(/__IMG_URL__/gi, uploaded_images[i]['url']);
                            html = html.replace(/__ATTACHMENT_ID__/gi, uploaded_images[i]['id']);
                            jQuery('#gallery_popup_editor form .woobe_fields_tmp').prepend(html);
                        }
                        __woobe_init_gallery();
                        //jQuery('#media-attachment-date-filters').trigger('change');
                    }
                });

        return false;
    });

    //***

    jQuery("#gallery_popup_editor form .woobe_fields_tmp").sortable({
        update: function (event, ui) {
            //***
        },
        opacity: 0.8,
        cursor: "crosshair",
        //handle: '.woobe_drag_and_drope',
        placeholder: 'woobe-options-highlight'
    });


    //***

    jQuery('.woobe_gall_file_delete').off('click');
    jQuery('.woobe_gall_file_delete').on('click', function () {
        jQuery(this).parents('li').remove();
        return false;
    });


    jQuery('.woobe_gall_file_delete_all').off('click');
    jQuery('.woobe_gall_file_delete_all').on('click', function () {
        jQuery('#gallery_popup_editor form .woobe_fields_tmp').html('');
        return false;
    });


}

//service

function __woobe_init_upsells() {

    jQuery("#upsells_popup_editor form .woobe_fields_tmp").sortable({
        update: function (event, ui) {
            //***
        },
        opacity: 0.8,
        cursor: "crosshair",
        handle: '.woobe_drag_and_drope',
        placeholder: 'woobe-options-highlight'
    });

    //***

    jQuery("#upsells_products_search").easyAutocomplete({
        url: function (phrase) {
            return ajaxurl;
        },
        //theme: "square",
        getValue: function (element) {
            jQuery('#upsells_popup_editor .cssload-container').hide();
            return element.name;
        },
        ajaxSettings: {
            dataType: "json",
            method: "POST",
            data: {
                action: "woobe_title_autocomplete",
                dataType: "json"
            }
        },
        preparePostData: function (data) {
            data.woobe_txt_search = jQuery("#upsells_products_search").val();
            data.auto_res_count = woobe_settings.autocomplete_max_elem_count;
            data.auto_search_by_behavior = 'title';
            data.exept_ids = jQuery('#products_upsells_form').serialize();
            jQuery('#upsells_popup_editor .cssload-container').show();
            return data;
        },
        ajaxCallback: function () {
            //***
        },
        template: {
            type: 'iconRight', //'links' | 'iconRight'
            fields: {
                iconSrc: "icon",
                link: "link"
            }
        },
        list: {
            maxNumberOfElements: woobe_settings.autocomplete_max_elem_count,
            onChooseEvent: function (e) {
                autocomplete_curr_index = jQuery("#upsells_products_search").getSelectedItemIndex();
                return true;
            },
            showAnimation: {
                type: "fade", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            hideAnimation: {
                type: "slide", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            onClickEvent: function () {
                var index = jQuery("#upsells_products_search").getSelectedItemIndex();
                var data = jQuery("#upsells_products_search").getItemData(index);

                if (parseInt(data.id, 10) > 0) {
                    var html = jQuery('#woobe_product_li_tpl').html();
                    html = html.replace(/__ID__/gi, data.id);
                    html = html.replace(/__TITLE__/gi, data.name + '(#' + data.id + ')');
                    html = html.replace(/__PERMALINK__/gi, data.link);
                    html = html.replace(/__IMG_URL__/gi, data.icon);
                    jQuery('#upsells_popup_editor form .woobe_fields_tmp').prepend(html);
                    jQuery("#upsells_products_search").val('');
                    __woobe_init_upsells();
                    jQuery("#upsells_products_search").focus();
                } else {
                    jQuery("#upsells_products_search").val('');
                }

                return false;
            }
        },
        requestDelay: autocomplete_request_delay
    });


    jQuery('#upsells_products_search').off('keydown');
    jQuery("#upsells_products_search").keydown(function (e) {
        if (e.keyCode == 13)
        {
            var index = jQuery("#upsells_products_search").getSelectedItemIndex();
            if (autocomplete_curr_index != -1) {
                index = autocomplete_curr_index;
            }
            var data = jQuery("#upsells_products_search").getItemData(index);

            if (parseInt(index, 10) > 0) {
                var html = jQuery('#woobe_product_li_tpl').html();
                html = html.replace(/__ID__/gi, data.id);
                html = html.replace(/__TITLE__/gi, data.name);
                html = html.replace(/__PERMALINK__/gi, data.link);
                html = html.replace(/__IMG_URL__/gi, data.icon);
                jQuery('#upsells_popup_editor form .woobe_fields_tmp').prepend(html);
                jQuery("#upsells_products_search").val('');
                __woobe_init_upsells();
                jQuery("#upsells_products_search").focus();
            } else {
                jQuery("#upsells_products_search").val('');
                jQuery("#upsells_products_search").focus();
            }
        }
    });

    //***

    jQuery('.woobe_prod_delete').off('click');
    jQuery('.woobe_prod_delete').on('click', function () {
        jQuery(this).parents('li').remove();
        jQuery("#upsells_products_search").focus();
        return false;
    });


    jQuery("#upsells_products_search").focus();



}

//service
function __woobe_init_cross_sells() {

    jQuery("#cross_sells_popup_editor form .woobe_fields_tmp").sortable({
        update: function (event, ui) {
            //***
        },
        opacity: 0.8,
        cursor: "crosshair",
        handle: '.woobe_drag_and_drope',
        placeholder: 'woobe-options-highlight'
    });

    //***

    jQuery("#cross_sells_products_search").easyAutocomplete({
        url: function (phrase) {
            return ajaxurl;
        },
        //theme: "square",
        getValue: function (element) {
            jQuery('#cross_sells_popup_editor .cssload-container').hide();
            return element.name;
        },
        ajaxSettings: {
            dataType: "json",
            method: "POST",
            data: {
                action: "woobe_title_autocomplete",
                dataType: "json"
            }
        },
        preparePostData: function (data) {
            data.woobe_txt_search = jQuery("#cross_sells_products_search").val();
            data.auto_res_count = woobe_settings.autocomplete_max_elem_count;
            data.auto_search_by_behavior = 'title';
            data.exept_ids = jQuery('#products_cross_sells_form').serialize();
            jQuery('#cross_sells_popup_editor .cssload-container').show();
            return data;
        },
        ajaxCallback: function () {
            //***
        },
        template: {
            type: 'iconRight', //'links' | 'iconRight'
            fields: {
                iconSrc: "icon",
                link: "link"
            }
        },
        list: {
            maxNumberOfElements: woobe_settings.autocomplete_max_elem_count,
            onChooseEvent: function (e) {
                autocomplete_curr_index = jQuery("#cross_sells_products_search").getSelectedItemIndex();
                return true;
            },
            showAnimation: {
                type: "fade", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            hideAnimation: {
                type: "slide", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            onClickEvent: function () {
                var index = jQuery("#cross_sells_products_search").getSelectedItemIndex();
                var data = jQuery("#cross_sells_products_search").getItemData(index);

                if (parseInt(data.id, 10) > 0) {
                    var html = jQuery('#woobe_product_li_tpl').html();
                    html = html.replace(/__ID__/gi, data.id);
                    html = html.replace(/__TITLE__/gi, data.name);
                    html = html.replace(/__PERMALINK__/gi, data.link);
                    html = html.replace(/__IMG_URL__/gi, data.icon);
                    jQuery('#cross_sells_popup_editor form .woobe_fields_tmp').prepend(html);
                    jQuery("#cross_sells_products_search").val('');
                    __woobe_init_cross_sells();
                    jQuery("#cross_sells_products_search").focus();
                } else {
                    jQuery("#cross_sells_products_search").val('');
                }
            }
        },
        requestDelay: autocomplete_request_delay
    });


    jQuery("#cross_sells_products_search").keydown(function (e) {
        if (e.keyCode == 13)
        {
            var index = jQuery("#cross_sells_products_search").getSelectedItemIndex();
            if (autocomplete_curr_index != -1) {
                index = autocomplete_curr_index;
            }
            var data = jQuery("#cross_sells_products_search").getItemData(index);

            if (parseInt(index, 10) > 0) {
                var html = jQuery('#woobe_product_li_tpl').html();
                html = html.replace(/__ID__/gi, data.id);
                html = html.replace(/__TITLE__/gi, data.name);
                html = html.replace(/__PERMALINK__/gi, data.link);
                html = html.replace(/__IMG_URL__/gi, data.icon);
                jQuery('#cross_sells_popup_editor form .woobe_fields_tmp').prepend(html);
                jQuery("#cross_sells_products_search").val('');
                __woobe_init_cross_sells();
                jQuery("#cross_sells_products_search").focus();
            } else {
                jQuery("#cross_sells_products_search").val('');
                jQuery("#cross_sells_products_search").focus();
            }
        }
    });

    //***

    jQuery('.woobe_prod_delete').off('click');
    jQuery('.woobe_prod_delete').on('click', function () {
        jQuery(this).parents('li').remove();
        jQuery("#cross_sells_products_search").focus();
        return false;
    });


    jQuery("#cross_sells_products_search").focus();
}

//service
function __woobe_init_grouped() {

    jQuery("#grouped_popup_editor form .woobe_fields_tmp").sortable({
        update: function (event, ui) {
            //***
        },
        opacity: 0.8,
        cursor: "crosshair",
        handle: '.woobe_drag_and_drope',
        placeholder: 'woobe-options-highlight'
    });

    //***

    jQuery("#grouped_products_search").easyAutocomplete({
        url: function (phrase) {
            return ajaxurl;
        },
        //theme: "square",
        getValue: function (element) {
            jQuery('#grouped_popup_editor .cssload-container').hide();
            return element.name;
        },
        ajaxSettings: {
            dataType: "json",
            method: "POST",
            data: {
                action: "woobe_title_autocomplete",
                dataType: "json"
            }
        },
        preparePostData: function (data) {
            data.woobe_txt_search = jQuery("#grouped_products_search").val();
            data.auto_res_count = woobe_settings.autocomplete_max_elem_count;
            data.auto_search_by_behavior = 'title';
            data.exept_ids = jQuery('#products_grouped_form').serialize();
            jQuery('#grouped_popup_editor .cssload-container').show();
            return data;
        },
        ajaxCallback: function () {
            //***
        },
        template: {
            type: 'iconRight', //'links' | 'iconRight'
            fields: {
                iconSrc: "icon",
                link: "link"
            }
        },
        list: {
            hideOnEmptyPhrase: false,
            maxNumberOfElements: woobe_settings.autocomplete_max_elem_count,
            onChooseEvent: function (e) {
                autocomplete_curr_index = jQuery("#grouped_products_search").getSelectedItemIndex();
                return true;
            },
            showAnimation: {
                type: "fade", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            hideAnimation: {
                type: "slide", //normal|slide|fade
                time: 333,
                callback: function () {
                }
            },
            onClickEvent: function () {
                var index = jQuery("#grouped_products_search").getSelectedItemIndex();
                var data = jQuery("#grouped_products_search").getItemData(index);

                if (parseInt(data.id, 10) > 0) {
                    var html = jQuery('#woobe_product_li_tpl').html();
                    html = html.replace(/__ID__/gi, data.id);
                    html = html.replace(/__TITLE__/gi, data.name);
                    html = html.replace(/__PERMALINK__/gi, data.link);
                    html = html.replace(/__IMG_URL__/gi, data.icon);
                    jQuery('#grouped_popup_editor form .woobe_fields_tmp').prepend(html);
                    jQuery("#grouped_products_search").val('');
                    __woobe_init_grouped();
                    jQuery("#grouped_products_search").focus();
                } else {
                    jQuery("#grouped_products_search").val('');
                }
            }
        },
        requestDelay: autocomplete_request_delay
    });

    //***

    jQuery("#grouped_products_search").keydown(function (e) {
        if (e.keyCode == 13)
        {
            var index = jQuery("#grouped_products_search").getSelectedItemIndex();
            if (autocomplete_curr_index != -1) {
                index = autocomplete_curr_index;
            }
            var data = jQuery("#grouped_products_search").getItemData(index);

            if (parseInt(index, 10) > 0) {
                var html = jQuery('#woobe_product_li_tpl').html();
                html = html.replace(/__ID__/gi, data.id);
                html = html.replace(/__TITLE__/gi, data.name);
                html = html.replace(/__PERMALINK__/gi, data.link);
                html = html.replace(/__IMG_URL__/gi, data.icon);
                jQuery('#grouped_popup_editor form .woobe_fields_tmp').prepend(html);
                jQuery("#grouped_products_search").val('');
                __woobe_init_grouped();
                jQuery("#grouped_products_search").focus();
            } else {
                jQuery("#grouped_products_search").val('');
                jQuery("#grouped_products_search").focus();
            }
        }
    });

    //***

    jQuery('.woobe_prod_delete').off('click');
    jQuery('.woobe_prod_delete').on('click', function () {
        jQuery(this).parents('li').remove();
        jQuery("#grouped_products_search").focus();
        return false;
    });


    jQuery("#grouped_products_search").focus();
}



function woobe_message(text, type, duration = 0) {
    jQuery('.growl').hide();
    if (duration > 0) {
        Growl.settings.duration = duration;
    } else {
        Growl.settings.duration = 1777;
    }
    switch (type) {
        case 'notice':
            jQuery.growl.notice({message: text});
            break;

        case 'warning':
            jQuery.growl.warning({message: text});
            break;

        case 'error':
            jQuery.growl.error({message: text});
            break;

        case 'clean':
            //clean
            break;

        default:
            jQuery.growl({title: '', message: text});
            break;
}

}

function woobe_init_scroll() {
    setTimeout(function () {

        //jQuery('#advanced-table').wrap( "<div class='woobe_scroll_wrapper'></div>" );

        if (jQuery('#advanced-table').width() > jQuery('#tabs-products').width() + 50) {
            jQuery('#woobe_scroll_left').show();
            jQuery('#woobe_scroll_right').show();

            var anchor1 = jQuery('.dataTables_scrollBody');
            //var anchor2 = jQuery('.dataTables_scrollHead');
            //var anchor3 = jQuery('.dataTables_scrollFoot');
            var corrective = 30;
            var animate_time = 300;
            var leftPos = null;

            jQuery('#woobe_scroll_left').on('click', function () {
                leftPos = anchor1.scrollLeft();
                jQuery('div.dataTables_scrollBody').animate({scrollLeft: leftPos + jQuery('#tabs-products').width() - corrective}, animate_time);

                //anchor1.animate({scrollLeft: leftPos + jQuery('#tabs-products').width() - corrective}, animate_time);
                //anchor2.animate({scrollLeft: leftPos + jQuery('#tabs-products').width() - corrective}, animate_time);
                //anchor3.animate({scrollLeft: leftPos + jQuery('#tabs-products').width() - corrective}, animate_time);
                return false;
            });


            jQuery('#woobe_scroll_right').on('click', function () {
                leftPos = anchor1.scrollLeft();
                jQuery('div.dataTables_scrollBody').animate({scrollLeft: leftPos - jQuery('#tabs-products').width() + corrective}, animate_time);

                //anchor1.animate({scrollLeft: leftPos - jQuery('#tabs-products').width() + corrective}, animate_time);
                //anchor2.animate({scrollLeft: leftPos - jQuery('#tabs-products').width() + corrective}, animate_time);
                //anchor3.animate({scrollLeft: leftPos - jQuery('#tabs-products').width() + corrective}, animate_time);
                return false;
            });
        }

    }, 1000);
}
function woobe_multi_select_cell_attr_visible(_this) {
    var cell_dropdown = jQuery(_this).parents('.woobe_multi_select_cell').find('.woobe_multi_select_cell_dropdown');
    var cell_list = jQuery(_this).parents('.woobe_multi_select_cell').find('.woobe_multi_select_cell_list');
    var ul = jQuery(cell_list).find('ul');
    var select = jQuery(cell_dropdown).find('select');
    var tax_key = jQuery(select).data('field');
    var product_id = jQuery(select).data('product-id');
    var selected = (jQuery(select).data('selected') + '').split(',').map(function (num) {
        return parseInt(num, 10);
    });

    var select_id = 'mselect_' + tax_key + '_' + product_id;

    jQuery(_this).hide();


    jQuery(select).chosen({
        //disable_search_threshold: 10,
        //max_shown_results: 5,
        width: '100%'
    }).trigger("chosen:updated");

    jQuery(cell_dropdown).show();

    //***

    jQuery(cell_dropdown).find('.woobe_multi_select_cell_cancel').off('click');
    jQuery(cell_dropdown).find('.woobe_multi_select_cell_cancel').on('click', function () {
        jQuery(select).chosen('destroy');
        jQuery(cell_dropdown).hide();
        jQuery(_this).show();
        return false;
    });

    jQuery(cell_dropdown).find('.woobe_multi_select_cell_select').off('click');
    jQuery(cell_dropdown).find('.woobe_multi_select_cell_select').on('click', function () {
        jQuery(select).find('option').prop('selected', true);
        jQuery(select).trigger('chosen:updated');
        return false;
    });
    jQuery(cell_dropdown).find('.woobe_multi_select_cell_deselect').off('click');
    jQuery(cell_dropdown).find('.woobe_multi_select_cell_deselect').on('click', function () {
        jQuery(select).find('option').removeAttr('selected');
        jQuery(select).trigger('chosen:updated');
        return false;
    });


    jQuery(cell_dropdown).find('.woobe_multi_select_cell_save').off('click');
    jQuery(cell_dropdown).find('.woobe_multi_select_cell_save').on('click', function () {
        jQuery(select).chosen('destroy');
        woobe_act_select(select);
        jQuery(cell_dropdown).hide();
        jQuery(_this).show();

        //***

        var sel = [];
        jQuery(ul).html('');
        if (jQuery(select).find(":selected").length) {
            jQuery(select).find(":selected").each(function (ii, option) {
                sel[ii] = option.value;
                jQuery(ul).append('<li>' + option.label + '</li>');
            });
        } else {
            jQuery(ul).append('<li>' + lang.no_items + '</li>');
        }

        jQuery(select).data('selected', sel.join(','));

        return false;
    });


    return false;
}
function woobe_multi_select_cell(_this) {

    var cell_dropdown = jQuery(_this).parents('.woobe_multi_select_cell').find('.woobe_multi_select_cell_dropdown');
    var cell_list = jQuery(_this).parents('.woobe_multi_select_cell').find('.woobe_multi_select_cell_list');
    var ul = jQuery(cell_list).find('ul');
    var select = jQuery(cell_dropdown).find('select');
    var tax_key = jQuery(select).data('field');
    var product_id = jQuery(select).data('product-id');
    var selected = (jQuery(select).data('selected') + '').split(',').map(function (num) {
        return parseInt(num, 10);
    });

    var select_id = 'mselect_' + tax_key + '_' + product_id;

    jQuery(_this).hide();

    //***

    jQuery(select).empty();
    __woobe_fill_select(select_id, taxonomies_terms[tax_key], selected);

    //***

    jQuery(select).chosen({
        //disable_search_threshold: 10,
        //max_shown_results: 5,
        width: '100%'
    }).trigger("chosen:updated");

    jQuery(cell_dropdown).show();

    //***

    jQuery(cell_dropdown).find('.woobe_multi_select_cell_cancel').off('click');
    jQuery(cell_dropdown).find('.woobe_multi_select_cell_cancel').on('click', function () {
        jQuery(select).chosen('destroy');
        jQuery(cell_dropdown).hide();
        jQuery(_this).show();
        return false;
    });

    jQuery(cell_dropdown).find('.woobe_multi_select_cell_select').off('click');
    jQuery(cell_dropdown).find('.woobe_multi_select_cell_select').on('click', function () {
        jQuery(select).find('option').prop('selected', true);
        jQuery(select).trigger('chosen:updated');
        return false;
    });
    jQuery(cell_dropdown).find('.woobe_multi_select_cell_deselect').off('click');
    jQuery(cell_dropdown).find('.woobe_multi_select_cell_deselect').on('click', function () {
        jQuery(select).find('option').removeAttr('selected');
        jQuery(select).trigger('chosen:updated');
        return false;
    });


    jQuery(cell_dropdown).find('.woobe_multi_select_cell_save').off('click');
    jQuery(cell_dropdown).find('.woobe_multi_select_cell_save').on('click', function () {
        jQuery(select).chosen('destroy');
        woobe_act_select(select);
        jQuery(cell_dropdown).hide();
        jQuery(_this).show();

        //***

        var sel = [];
        jQuery(ul).html('');
        if (jQuery(select).find(":selected").length) {
            jQuery(select).find(":selected").each(function (ii, option) {
                sel[ii] = option.value;
                jQuery(ul).append('<li>' + option.label + '</li>');
            });
        } else {
            jQuery(ul).append('<li>' + lang.no_items + '</li>');
        }

        jQuery(select).data('selected', sel.join(','));

        return false;
    });


    jQuery(cell_dropdown).find('.woobe_multi_select_cell_new').off('click');
    jQuery(cell_dropdown).find('.woobe_multi_select_cell_new').on('click', function () {

        __woobe_create_new_term(tax_key, false, select_id);

        return false;
    });


    return false;
}

//make images bigger on their event onmouseover
function woobe_init_image_preview(_this) {
    var xOffset = 150;
    var yOffset = 30;

    _this.t = _this.title;
    //_this.title = "";
    var c = (_this.t != "") ? "<br/>" + _this.t : "";
    jQuery("body").append("<p id='woobe_img_preview'><img src='" + _this.href + "' alt='" + lang.loading + "' width='300' />" + c + "</p>");
    jQuery("#woobe_img_preview")
            .css("top", (_this.pageY - xOffset) + "px")
            .css("left", (_this.pageX + yOffset) + "px")
            .fadeIn("fast");

    jQuery(_this).mousemove(function (e) {
        jQuery("#woobe_img_preview")
                .css("top", (e.pageY - xOffset) + "px")
                .css("left", (e.pageX + yOffset) + "px");
    });

    jQuery(_this).mouseleave(function (e) {
        jQuery("#woobe_img_preview").remove();
    });
}

//to display current product in the top wordpress admin bar
function woobe_td_hover(id, title, col_num) {
    if (!jQuery('#wp-admin-bar-root-default li.woobe_current_cell_view').length) {
        jQuery('#wp-admin-bar-root-default').append('<li class="woobe_current_cell_view">');
    }

    //***

    if (id > 0) {
        var content = '#' + id + '. ' + title + ' [<i>' + jQuery('#woobe_col_' + col_num).text() + '</i>]';
    } else {
        var content = '';
    }

    jQuery('#wp-admin-bar-root-default li.woobe_current_cell_view').html(content);

    return true;
}


function woobe_onmouseover_num_textinput(_this, colIndex) {
    jQuery(document).trigger("woobe_onmouseover_num_textinput", [_this, colIndex]);
    return true;
}

function woobe_onmouseout_num_textinput(_this, colIndex) {
    jQuery(document).trigger("woobe_onmouseout_num_textinput", [_this, colIndex]);
    return true;
}





