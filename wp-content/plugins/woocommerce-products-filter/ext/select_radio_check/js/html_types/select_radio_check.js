"use strict";

jQuery(function ($) {
    $(document).on('click', function (e) {
        if (!$(e.target).parents().hasClass("woof_select_radio_check")) {
            $(".woof_select_radio_check dd ul").hide(200);
            $(".woof_select_radio_check_opened").removeClass('woof_select_radio_check_opened');
        }
    });
});


function woof_init_select_radio_check() {
    jQuery(".woof_select_radio_check dt a.woof_select_radio_check_opener").on('click', function () {
        var _this = this;
        jQuery.each(jQuery(".woof_select_radio_check_opener"), function (i, sel) {
            if (sel !== _this) {
                jQuery(this).parents('.woof_select_radio_check').find("dd ul").hide();
                jQuery(this).parents('.woof_select_radio_check').find('.woof_select_radio_check_opened').removeClass('woof_select_radio_check_opened');
            }
        });


        //+++
        jQuery(this).parents('.woof_select_radio_check').find("dd ul").slideToggle(200);
        if (jQuery(this).parent().hasClass('woof_select_radio_check_opened')) {
            jQuery(this).parent().removeClass('woof_select_radio_check_opened');
        } else {
            jQuery(this).parent().addClass('woof_select_radio_check_opened');
        }
    });

    //+++

    if (Object.keys(woof_current_values).length > 0) {
        jQuery.each(woof_current_values, function (index, value) {

            if (!jQuery('.woof_hida_' + index).length) {
                return;
            }

            value = value.toString().trim();
            if (value.search(',')) {
                value = value.split(',');
            }
            //+++
            var txt_results = new Array();
            var v_results = new Array();
            jQuery.each(value, function (i, v) {
                var txt = v;
                var is_in_custom = false;
                if (Object.keys(woof_lang_custom).length > 0) {
                    jQuery.each(woof_lang_custom, function (i, tt) {
                        if (i == index) {
                            is_in_custom = true;
                            txt = tt;
                        }
                    });
                }

                if (!is_in_custom) {
                    try {
                        txt = jQuery("input[data-anchor='woof_n_" + index + '_' + v + "']").val();
                    } catch (e) {
                        console.log(e);
                    }

                    if (typeof txt === 'undefined')
                    {
                        txt = v;
                    }
                }

                txt_results.push(txt);
                v_results.push(v);

            });

            if (txt_results.length) {
                jQuery('.woof_hida_' + index).addClass('woof_hida_small');
                jQuery('.woof_hida_' + index).html('<div class="woof_products_top_panel2"></div>');
                var panel = jQuery('.woof_hida_' + index).find('.woof_products_top_panel2');
                panel.show();
                panel.html('<ul></ul>');
                jQuery.each(txt_results, function (i, txt) {
                    panel.find('ul').append(
                            jQuery('<li>').append(
                            jQuery('<a>').attr('href', "").attr('data-tax', index).attr('data-slug', v_results[i]).append(
                            jQuery('<span>').attr('class', 'woof_remove_ppi').append(txt)
                            )));
                });

            } else {
                jQuery('.woof_hida_' + index).removeClass('woof_hida_small');
                jQuery('.woof_hida_' + index).html(jQuery('.woof_hida_' + index).data('title'));
            }

        });

    }

    //***

    jQuery.each(jQuery('.woof_mutliSelect'), function (i, txt) {
        if (parseInt(jQuery(this).data('height'), 10) > 0) {
            jQuery(this).find('ul.woof_list:first-child').eq(0).css('max-height', jQuery(this).data('height'));
        } else {
            jQuery(this).find('ul.woof_list:first-child').eq(0).css('max-height', 100);
        }
    });


}
