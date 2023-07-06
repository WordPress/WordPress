"use strict";

var woobe_calculator_current_cell = null;
var woobe_calculator_is_drawned = false;


jQuery(function ($) {

    jQuery('.woobe_calculator_operation').val(woobe_get_from_storage('woobe_calculator_operation'));
    jQuery('.woobe_calculator_how').val(woobe_get_from_storage('woobe_calculator_how'));

    //***

    jQuery('.woobe_calculator_close').on('click', function () {
        jQuery('#woobe_calculator').hide(99);
        woobe_calculator_is_drawned = false;
        return false;
    });

    //***

    jQuery(document).on("tab_switched", {}, function (event) {
        jQuery('.woobe_calculator_btn').hide();
        return true;
    });

    jQuery(document).on("data_redraw_done", {}, function (event) {
        jQuery('.woobe_calculator_btn').hide();
        return true;
    });

    jQuery(document).on("woobe_top_panel_clicked", {}, function (event) {
        jQuery('.woobe_calculator_btn').hide();
        return true;
    });

    //***

    jQuery(document).on("woobe_onmouseover_num_textinput", {}, function (event, o, colIndex) {
        woobe_calc_onmouseover_num_textinput(o, colIndex);
        return true;
    });

    jQuery(document).on("woobe_onmouseout_num_textinput", {}, function (event, o, colIndex) {
        woobe_calc_onmouseout_num_textinput(o, colIndex);
        return true;
    });

    //***

    jQuery('.woobe_calculator_set').on('click', function () {

        var val = parseFloat(jQuery('.woobe_calculator_value').val());

        if (isNaN(val)) {
            jQuery('.woobe_calculator_close').trigger('click');
            return;
        }

        var operation = jQuery('.woobe_calculator_operation').val();
        var how = jQuery('.woobe_calculator_how').val();



        //***

        var cell = woobe_calculator_current_cell;//to avoid mouse over set of another cell whicle ajaxing
        var product_id = jQuery(cell).data('product-id');

        //***

        //fix
        if (jQuery(cell).data('field') !== 'sale_price' && operation == 'rp-') {
            operation = '+';
        }

        if (jQuery(cell).data('field') !== 'regular_price' && operation == 'sp+') {
            operation = '+';
        }

        //***

        var cell_value = parseFloat(jQuery(cell).html().replace(/\,/g, ""));

        var bulk_operation = 'invalue';

        //***

        switch (operation) {
            case '+':
                if (how == 'value') {
                    cell_value += val;
                } else {
                    //%
                    cell_value = cell_value + cell_value * val / 100;
                    bulk_operation = 'inpercent';
                }
                break;

            case '-':
                if (how == 'value') {
                    cell_value -= val;
                    bulk_operation = 'devalue';
                } else {
                    //%
                    cell_value = cell_value - cell_value * val / 100;
                    bulk_operation = 'depercent';
                }
                break;

            case 'rp-':

                cell_value = parseFloat(jQuery('#product_row_' + product_id).find("[data-field='regular_price']").html().replace(/\,/g, ""));

                if (how == 'value') {
                    cell_value = cell_value - val;
                    bulk_operation = 'devalue_regular_price';
                } else {
                    //%
                    cell_value = cell_value - cell_value * val / 100;
                    bulk_operation = 'depercent_regular_price';
                }
                break;

            case 'sp+':

                cell_value = parseFloat(jQuery('#product_row_' + product_id).find("[data-field='sale_price']").html().replace(/\,/g, ""));

                if (how == 'value') {
                    cell_value = cell_value + val;
                    bulk_operation = 'invalue_sale_price';
                } else {
                    //%
                    cell_value = cell_value + cell_value * val / 100;
                    bulk_operation = 'inpercent_sale_price';
                }
                break;
        }

        //***

        woobe_message(lang.saving, '');


        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_update_page_field',
                product_id: product_id,
                field: jQuery(cell).data('field'),
                value: cell_value,
                num_rounding: jQuery('.woobe_num_rounding').eq(0).val()
            },
            success: function (answer) {
                jQuery(cell).html(answer);
                woobe_message(lang.saved, 'notice');


                //fix for stock_quantity + manage_stock
                if (!woobe_bind_editing) {
                    if (jQuery(cell).data('field') == 'stock_quantity') {
                        woobe_redraw_table_row(jQuery('#product_row_' + jQuery(cell).data('product-id')));
                    }
                }

                jQuery(document).trigger('woobe_page_field_updated', [jQuery(cell).data('product-id'), jQuery(cell).data('field'), val, bulk_operation]);

                //jQuery('.woobe_num_rounding').val(0);

                //woobe_calculator_current_cell = null;
            },
            error: function () {
                alert(lang.error);
            }
        });


        jQuery('.woobe_calculator_close').trigger('click');
        return false;
    });

    //***

    jQuery(".woobe_calculator_value").keydown(function (e) {
        if (e.keyCode == 13)
        {
            jQuery('.woobe_calculator_set').trigger('click');
        }

        if (e.keyCode == 27)
        {
            jQuery('.woobe_calculator_close').trigger('click');
        }
    });

    jQuery("#woobe_calculator").keydown(function (e) {
        if (e.keyCode == 27)
        {
            jQuery('.woobe_calculator_close').trigger('click');
        }
    });

    //***

    jQuery('.woobe_calculator_operation').on('change',function () {
        woobe_set_to_storage('woobe_calculator_operation', jQuery(this).val());
        return true;
    });

    jQuery('.woobe_calculator_how').on('change',function () {
        woobe_set_to_storage('woobe_calculator_how', jQuery(this).val());
        return true;
    });

    //***
    jQuery('div.dataTables_scrollBody').scroll(function () {
        jQuery('.woobe_calculator_btn').hide();
    });

});

function woobe_calc_onmouseover_num_textinput(_this, colIndex) {

    if (woobe_calculator_is_drawned) {
        return;
    }

    if (jQuery(_this).find('.info_restricked').length > 0 || jQuery(_this).data('editable-view') !== "textinput") {
        jQuery('.woobe_calculator_btn').hide();
        return;
    }

    //***

    woobe_calculator_current_cell = _this;
    jQuery('.woobe_calculator_btn').show();
    var rt = (jQuery(window).width() - (jQuery(_this).offset().left + jQuery(_this).outerWidth()));
    var tt = jQuery(_this).offset().top/* - jQuery(_this).outerHeight() / 2.3*/;
    jQuery('.woobe_calculator_btn').css({top: tt, right: rt});

    return true;
}

function woobe_draw_calculator() {
    jQuery('#woobe_calculator').show();
    jQuery('#woobe_calculator').css({top: jQuery('.woobe_calculator_btn').css('top'), right: jQuery('.woobe_calculator_btn').css('right')});
    jQuery(".woobe_calculator_value").focus();

    //if input activated and visible in the cell
    if (jQuery(woobe_calculator_current_cell).find('input')) {
        jQuery(woobe_calculator_current_cell).html(jQuery(woobe_calculator_current_cell).find('input').val());

        //***

        if (jQuery(woobe_calculator_current_cell).data('field') == 'sale_price') {
            var product_id = jQuery(woobe_calculator_current_cell).data('product-id');
            //reqular_price column is enabled
            if (jQuery('#product_row_' + product_id).find("[data-field='regular_price']").length > 0) {
                jQuery('.woobe_calc_rp').show();
            } else {
                jQuery('.woobe_calc_rp').hide();
                jQuery('.woobe_calculator_operation').val('+');
            }

        } else {
            jQuery('.woobe_calc_rp').hide();
            if (jQuery('.woobe_calculator_operation').val() == 'rp-') {
                jQuery('.woobe_calculator_operation').val('+');
            }
        }

        //***

        if (jQuery(woobe_calculator_current_cell).data('field') == 'regular_price') {
            var product_id = jQuery(woobe_calculator_current_cell).data('product-id');
            //reqular_price column is enabled
            if (jQuery('#product_row_' + product_id).find("[data-field='sale_price']").length > 0) {
                jQuery('.woobe_calc_sp').show();
            } else {
                jQuery('.woobe_calc_sp').hide();
                jQuery('.woobe_calculator_operation').val('+');
            }

        } else {
            jQuery('.woobe_calc_sp').hide();
            if (jQuery('.woobe_calculator_operation').val() == 'sp+') {
                jQuery('.woobe_calculator_operation').val('+');
            }
        }
    }

    woobe_calculator_is_drawned = true;

    return true;
}

function woobe_calc_onmouseout_num_textinput() {
    if (woobe_calculator_is_drawned) {
        //jQuery('.woobe_calculator_btn').hide();
    }
    return true;
}


