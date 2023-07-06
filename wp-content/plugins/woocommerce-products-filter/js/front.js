"use strict";
var woof_redirect = '';//if we use redirect attribute in shortcode [woof]
var woof_reset_btn_action = false;
var woof_additional_fields = {};
jQuery(function () {
    try
    {
        woof_current_values = JSON.parse(woof_current_values);
    } catch (e)
    {
        woof_current_values = null;
    }
    if (woof_current_values == null || woof_current_values.length == 0) {
        woof_current_values = {};
    }

});

//***
if (typeof woof_lang_custom == 'undefined') {
    var woof_lang_custom = {};/*!!important*/
}
if (typeof woof_ext_filter_titles != 'undefined') {
    woof_lang_custom = Object.assign({}, woof_lang_custom, woof_ext_filter_titles);
}

jQuery(function ($) {
    jQuery('body').append('<div id="woof_html_buffer" class="woof_info_popup" style="display: none;"></div>');
//http://stackoverflow.com/questions/2389540/jquery-hasparent
    jQuery.extend(jQuery.fn, {
        within: function (pSelector) {
            // Returns a subset of items using jQuery.filter
            return this.filter(function () {
                // Return truthy/falsey based on presence in parent
                return jQuery(this).closest(pSelector).length;
            });
        }
    });

    //+++

    if (jQuery('#woof_results_by_ajax').length > 0) {
        woof_is_ajax = 1;
    }

    //listening attributes in shortcode [woof]
    woof_autosubmit = parseInt(jQuery('.woof').eq(0).data('autosubmit'), 10);
    woof_ajax_redraw = parseInt(jQuery('.woof').eq(0).data('ajax-redraw'), 10);



    //+++

    woof_ext_init_functions = JSON.parse(woof_ext_init_functions);

    //fix for native woo price range
    woof_init_native_woo_price_filter();


    jQuery('body').on('price_slider_change', function (event, min, max) {

        if (woof_autosubmit && !woof_show_price_search_button && jQuery('.price_slider_wrapper').length < 3) {

            jQuery('.woof .widget_price_filter form').trigger('submit');

        } else {
            var min_price = jQuery(this).find('.price_slider_amount #min_price').val();
            var max_price = jQuery(this).find('.price_slider_amount #max_price').val();
            woof_current_values.min_price = min_price;
            woof_current_values.max_price = max_price;
        }
    });

    jQuery('body').on('change', '.woof_price_filter_dropdown', function () {
        var val = jQuery(this).val();
        if (parseInt(val, 10) == -1) {
            delete woof_current_values.min_price;
            delete woof_current_values.max_price;
        } else {
            var val = val.split("-");
            woof_current_values.min_price = val[0];
            woof_current_values.max_price = val[1];
        }

        if (woof_autosubmit || jQuery(this).within('.woof').length == 0) {
            woof_submit_link(woof_get_submit_link());
        }
    });

    //change value in textinput price filter if WOOCS is installed
    woof_recount_text_price_filter();
    //+++
    jQuery('body').on('change', '.woof_price_filter_txt', function () {

        var from = parseInt(jQuery(this).parent().find('.woof_price_filter_txt_from').val(), 10);
        var to = parseInt(jQuery(this).parent().find('.woof_price_filter_txt_to').val(), 10);

        if (to < from || from < 0) {
            delete woof_current_values.min_price;
            delete woof_current_values.max_price;
        } else {
            if (typeof woocs_current_currency !== 'undefined') {
                from = Math.ceil(from / parseFloat(woocs_current_currency.rate));
                to = Math.ceil(to / parseFloat(woocs_current_currency.rate));
            }

            woof_current_values.min_price = from;
            woof_current_values.max_price = to;
        }

        if (woof_autosubmit || jQuery(this).within('.woof').length == 0) {
            woof_submit_link(woof_get_submit_link());
        }
    });


    //***

    jQuery('body').on('click', '.woof_open_hidden_li_btn', function () {
        var state = jQuery(this).data('state');
        var type = jQuery(this).data('type');

        if (state == 'closed') {
            jQuery(this).parents('.woof_list').find('.woof_hidden_term').addClass('woof_hidden_term2');
            jQuery(this).parents('.woof_list').find('.woof_hidden_term').removeClass('woof_hidden_term');
            if (type == 'image') {
                jQuery(this).find('img').attr('src', jQuery(this).data('opened'));
            } else {
                jQuery(this).html(jQuery(this).data('opened'));
            }

            jQuery(this).data('state', 'opened');
        } else {
            jQuery(this).parents('.woof_list').find('.woof_hidden_term2').addClass('woof_hidden_term');
            jQuery(this).parents('.woof_list').find('.woof_hidden_term2').removeClass('woof_hidden_term2');

            if (type == 'image') {
                jQuery(this).find('img').attr('src', jQuery(this).data('closed'));
            } else {
                jQuery(this).text(jQuery(this).data('closed'));
            }

            jQuery(this).data('state', 'closed');
        }


        return false;
    });
    //open hidden block
    woof_open_hidden_li();

    //*** woocommerce native "AVERAGE RATING" widget synchronizing
    jQuery('.widget_rating_filter li.wc-layered-nav-rating a').on('click', function () {
        var is_chosen = jQuery(this).parent().hasClass('chosen');
        var parsed_url = woof_parse_url(jQuery(this).attr('href'));
        var rate = 0;
        if (parsed_url.query !== undefined) {
            if (parsed_url.query.indexOf('min_rating') !== -1) {
                var arrayOfStrings = parsed_url.query.split('min_rating=');
                rate = parseInt(arrayOfStrings[1], 10);
            }
        }
        jQuery(this).parents('ul').find('li').removeClass('chosen');
        if (is_chosen) {
            delete woof_current_values.min_rating;
        } else {
            woof_current_values.min_rating = rate;
            jQuery(this).parent().addClass('chosen');
        }

        woof_submit_link(woof_get_submit_link());

        return false;
    });

    //WOOF start filtering button action
    jQuery('body').on('click', '.woof_start_filtering_btn', function () {

        var shortcode = jQuery(this).parents('.woof').data('shortcode');
        jQuery(this).html(woof_lang_loading);
        jQuery(this).addClass('woof_start_filtering_btn2');
        jQuery(this).removeClass('woof_start_filtering_btn');
        //redrawing [woof ajax_redraw=1] only
        var data = {
            action: "woof_draw_products",
            page: 1,
            shortcode: 'woof_nothing', //we do not need get any products, seacrh form data only
            woof_shortcode: shortcode
        };

        jQuery.post(woof_ajaxurl, data, function (content) {
            content = JSON.parse(content);
            jQuery('div.woof_redraw_zone').replaceWith(jQuery(content.form).find('.woof_redraw_zone'));
            woof_mass_reinit();
            woof_init_tooltip();
        });


        return false;
    });

    //***

    window.addEventListener("pageshow", function (event) {
        var woof_check_history = event.persisted ||
                (typeof window.performance != "undefined" &&
                        window.performance.navigation.type === 2);
        if (woof_check_history) {
            woof_hide_info_popup();
            woof_submit_link_locked = false;
        }
    });

    var str = window.location.href;
    window.onpopstate = function (event) {

        try {
            if (Object.keys(woof_current_values).length) {

                var temp = str.split('?');
                var get1 = "";
                if (temp[1] != undefined) {
                    get1 = temp[1].split('#');
                }
                var str2 = window.location.href;
                var temp2 = str2.split('?');
                if (temp2[1] == undefined) {
                    //return false;
                    var get2 = {0: "", 1: ""};

                } else {
                    var get2 = temp2[1].split('#');
                }

                if (get2[0] != get1[0]) {

                    woof_show_info_popup(woof_lang_loading);
                    window.location.reload();
                }
                return false;
            }
        } catch (e) {
            console.log(e);
        }
    };
    //***

    //ion-slider price range slider
    woof_init_ion_sliders();

    //***

    woof_init_show_auto_form();
    woof_init_hide_auto_form();

    //***
    woof_remove_empty_elements();

    woof_init_search_form();
    woof_init_pagination();
    woof_init_orderby();
    woof_init_reset_button();
    woof_init_beauty_scroll();
    //+++
    woof_draw_products_top_panel();
    woof_shortcode_observer();

    //tooltip  
    woof_init_tooltip();

    //mobile filter
    woof_init_mobile_filter();


//+++
    //if we use redirect attribute in shortcode [woof is_ajax=0]
    //not for ajax, for redirect mode only
    if (!woof_is_ajax) {
        woof_redirect_init();
    }

    woof_init_toggles();

});

//if we use redirect attribute in shortcode [woof is_ajax=0]
//not for ajax, for redirect mode only
function woof_redirect_init() {

    try {
        if (jQuery('.woof').length) {
            //https://wordpress.org/support/topic/javascript-error-in-frontjs?replies=1
            if (undefined !== jQuery('.woof').val()) {
                woof_redirect = jQuery('.woof').eq(0).data('redirect');//default value
                if (woof_redirect.length > 0) {
                    woof_shop_page = woof_current_page_link = woof_redirect;
                }
                return woof_redirect;
            }
        }
    } catch (e) {
        console.log(e);
    }

}

function woof_init_orderby() {
    jQuery('body').on('submit', 'form.woocommerce-ordering', function () {
        /* woo3.3 */
        if (!jQuery("#is_woo_shortcode").length) {
            return false;
        }
        /* +++ */
    });
    jQuery('body').on('change', 'form.woocommerce-ordering select.orderby', function () {
        /* woo3.3 */
        if (!jQuery("#is_woo_shortcode").length) {
            woof_current_values.orderby = jQuery(this).val();
            woof_ajax_page_num = 1;
            woof_submit_link(woof_get_submit_link(), 0);
            return false;
        }
        /* +++ */
    });
}

function woof_init_reset_button() {

    jQuery('body').on('click', '.woof_reset_search_form', function () {
        //var link = jQuery(this).data('link');
        woof_ajax_page_num = 1;
        woof_ajax_redraw = 0;
        woof_reset_btn_action = true;
        if (woof_is_permalink) {
            woof_current_values = {};
            woof_submit_link(woof_get_submit_link().split("page/")[0]);

        } else {
            var link = woof_shop_page;
            if (woof_current_values.hasOwnProperty('page_id')) {
                link = location.protocol + '//' + location.host + "/?page_id=" + woof_current_values.page_id;
                woof_current_values = {'page_id': woof_current_values.page_id};
                woof_get_submit_link();
            }
            //***
            woof_submit_link(link);
            if (woof_is_ajax) {
                history.pushState({}, "", link);
                if (woof_current_values.hasOwnProperty('page_id')) {
                    woof_current_values = {'page_id': woof_current_values.page_id};
                } else {
                    woof_current_values = {};
                }
            }
        }
        return false;
    });
}

function woof_init_pagination() {

    if (woof_is_ajax === 1) {
        //jQuery('.woocommerce-pagination ul.page-numbers a.page-numbers').life('click', function () {
        jQuery('body').on('click', '.woocommerce-pagination a.page-numbers', function () {
            var l = jQuery(this).attr('href');

            if (woof_ajax_first_done) {
                //wp-admin/admin-ajax.php?paged=2
                var res = l.split("paged=");
                if (typeof res[1] !== 'undefined') {
                    woof_ajax_page_num = parseInt(res[1]);
                } else {
                    woof_ajax_page_num = 1;
                }
                var res2 = l.split("product-page=");
                if (typeof res2[1] !== 'undefined') {
                    woof_ajax_page_num = parseInt(res2[1]);
                }
            } else {
                var res = l.split("page/");
                if (typeof res[1] !== 'undefined') {
                    woof_ajax_page_num = parseInt(res[1]);
                } else {
                    woof_ajax_page_num = 1;
                }
                var res2 = l.split("product-page=");
                if (typeof res2[1] !== 'undefined') {
                    woof_ajax_page_num = parseInt(res2[1]);
                }
            }

            //+++


            {
                woof_submit_link(woof_get_submit_link(), 0);
            }

            return false;
        });
    }
}

function woof_init_search_form() {
    woof_init_checkboxes();
    woof_init_mselects();
    woof_init_radios();
    woof_price_filter_radio_init();
    woof_init_selects();


    //for extensions
    if (woof_ext_init_functions !== null) {
        jQuery.each(woof_ext_init_functions, function (type, func) {
            eval(func + '()');
        });
    }

    //+++
    jQuery('.woof_submit_search_form').on('click', function () {

        if (woof_ajax_redraw) {
            //[woof redirect="http://test-all/" autosubmit=1 ajax_redraw=1 is_ajax=1 tax_only="locations" by_only="none"]
            woof_ajax_redraw = 0;
            woof_is_ajax = 0;
        }
        //***
        woof_submit_link(woof_get_submit_link());
        return false;
    });



    //***
    jQuery('ul.woof_childs_list').parent('li').addClass('woof_childs_list_li');

    //***

    woof_remove_class_widget();
    woof_checkboxes_slide();
    
    document.dispatchEvent(new CustomEvent('woof_init_search_form', {detail: {}}));
}

var woof_submit_link_locked = false;
function woof_submit_link(link, ajax_redraw) {

    if (woof_submit_link_locked) {
        return;
    }
    if (typeof WoofTurboMode != 'undefined') {
        WoofTurboMode.woof_submit_link(link);

        return;
    }
    if (typeof ajax_redraw == 'undefined') {
        ajax_redraw = woof_ajax_redraw;
    }

    woof_submit_link_locked = true;

    woof_show_info_popup(woof_lang_loading);

    if (woof_is_ajax === 1 && !ajax_redraw) {

        woof_ajax_first_done = true;
        var data = {
            action: "woof_draw_products",
            link: link,
            page: woof_ajax_page_num,
            shortcode: jQuery('#woof_results_by_ajax').data('shortcode'),
            woof_shortcode: jQuery('div.woof').data('shortcode')
        };

        jQuery.post(woof_ajaxurl, data, function (content) {
            content = JSON.parse(content);

            woof_before_ajax_form_redrawing();

            if (jQuery('.woof_results_by_ajax_shortcode').length) {
                if (typeof content.products != "undefined") {
                    jQuery('#woof_results_by_ajax').replaceWith(content.products);

                    /* compatibility found products count*/
                    var found_count = jQuery('.woof_found_count');
                    jQuery(found_count).show();
                    if (found_count.length > 0) {
                        var count_prod = jQuery("#woof_results_by_ajax").data('count');
                        if (typeof count_prod != "undefined") {
                            jQuery(found_count).text(count_prod);
                        }

                    }

                }
            } else {
                if (typeof content.products != "undefined") {
                    jQuery('.woof_shortcode_output').replaceWith(content.products);
                }
            }
            if (typeof content.additional_fields != "undefined") {
                jQuery.each(content.additional_fields, function (selector, html_data) {
		    if (typeof woof_additional_fields[selector] == 'undefined') {
			
			woof_additional_fields[selector] = jQuery(selector);
		    }
                    jQuery(selector).replaceWith(html_data);
                });
		//draw old  values
                jQuery.each(woof_additional_fields, function (selector, html_data_old) {
		    if (typeof content.additional_fields[selector]== 'undefined') {
			jQuery(selector).replaceWith(html_data_old);
		    }
                    
                });		
            }


            jQuery('div.woof_redraw_zone').replaceWith(jQuery(content.form).find('.woof_redraw_zone'));
            woof_draw_products_top_panel();
            woof_mass_reinit();
            woof_submit_link_locked = false;
            //removing id woof_results_by_ajax - multi in ajax mode sometimes
            //when uses shorcode woof_products in ajax and in settings try ajaxify shop is Yes
            jQuery.each(jQuery('#woof_results_by_ajax'), function (index, item) {
                if (index == 0) {
                    return;
                }

                jQuery(item).removeAttr('id');
            });
            /*mobile  behavior*/
            //jQuery('.woof_hide_mobile_filter').trigger('click');
	    jQuery('.woof').removeClass('woof_show_filter_for_mobile');



            //infinite scroll
            woof_infinite();
            //*** script after ajax loading here
            woof_js_after_ajax_done();
            //***  change  link  in button "add to cart"
            woof_change_link_addtocart();

            /*tooltip*/
            woof_init_tooltip();

            document.dispatchEvent(new CustomEvent('woof-ajax-form-redrawing', {detail: {
                    link: link
                }}));

        });

    } else {

        if (ajax_redraw) {
            //redrawing [woof ajax_redraw=1] only
            var data = {
                action: "woof_draw_products",
                link: link,
                page: 1,
                shortcode: 'woof_nothing', //we do not need get any products, seacrh form data only
                woof_shortcode: jQuery('div.woof').eq(0).data('shortcode')
            };
            jQuery.post(woof_ajaxurl, data, function (content) {

                woof_before_ajax_form_redrawing();

                content = JSON.parse(content);
                jQuery('div.woof_redraw_zone').replaceWith(jQuery(content.form).find('.woof_redraw_zone'));
                woof_mass_reinit();
                woof_submit_link_locked = false;
                /*tooltip*/
                woof_init_tooltip();

                document.dispatchEvent(new CustomEvent('woof-ajax-form-redrawing', {detail: {
                        link: link
                    }}));
            });
        } else {

            window.location = link;
            woof_show_info_popup(woof_lang_loading);
        }
    }
}

function woof_remove_empty_elements() {
    // lets check for empty drop-downs
    jQuery.each(jQuery('.woof_container select'), function (index, select) {
        var size = jQuery(select).find('option').length;
        if (size === 0) {
            jQuery(select).parents('.woof_container').remove();
        }
    });
    //+++
    // lets check for empty checkboxes, radio, color conatiners
    jQuery.each(jQuery('ul.woof_list'), function (index, ch) {
        var size = jQuery(ch).find('li').length;
        if (size === 0) {
            jQuery(ch).parents('.woof_container').remove();
        }
    });
        jQuery.each(jQuery('.woof_container .woof_list_sd'), function (index, ch) {
        var size = jQuery(ch).find('.woof-sd-ie').length;
        if (size === 0) {
            jQuery(ch).parents('.woof_container').remove();
        }
    });
}

function woof_get_submit_link() {
//filter woof_current_values values

    if (woof_is_ajax) {
        woof_current_values.page = woof_ajax_page_num;
    }
//+++
    if (Object.keys(woof_current_values).length > 0) {
        jQuery.each(woof_current_values, function (index, value) {
            if (index == swoof_search_slug) {
                delete woof_current_values[index];
            }
            if (index == 's') {
                delete woof_current_values[index];
            }
            if (index == 'product') {
//for single product page (when no permalinks)
                delete woof_current_values[index];
            }
            if (index == 'really_curr_tax') {
                delete woof_current_values[index];
            }
        });
    }


    //***
    if (Object.keys(woof_current_values).length === 2) {
        if (('min_price' in woof_current_values) && ('max_price' in woof_current_values)) {
            woof_current_page_link = woof_current_page_link.replace(new RegExp(/page\/(\d+)/), "");
            var l = woof_current_page_link + '?min_price=' + woof_current_values.min_price + '&max_price=' + woof_current_values.max_price;
            if (woof_is_ajax) {
                history.pushState({}, "", l);
            }
            return l;
        }
    }



    //***

    if (Object.keys(woof_current_values).length === 0) {
        if (woof_is_ajax) {
            history.pushState({}, "", woof_current_page_link);
        }
        return woof_current_page_link;
    }
    //+++
    if (Object.keys(woof_really_curr_tax).length > 0) {
        woof_current_values['really_curr_tax'] = woof_really_curr_tax.term_id + '-' + woof_really_curr_tax.taxonomy;
    }
    //+++
    var link = woof_current_page_link + "?" + swoof_search_slug + "=1";

    //just for the case when no permalinks enabled
    if (!woof_is_permalink) {

        if (woof_redirect.length > 0) {
            link = woof_redirect + "?" + swoof_search_slug + "=1";
            if (woof_current_values.hasOwnProperty('page_id')) {
                delete woof_current_values.page_id;
            }
        } else {
            link = location.protocol + '//' + location.host + "?" + swoof_search_slug + "=1";

        }
    }

    //any trash for different sites, useful for quick support
    var woof_exclude_accept_array = ['path'];

    if (Object.keys(woof_current_values).length > 0) {
        jQuery.each(woof_current_values, function (index, value) {
            if (index == 'page' && woof_is_ajax) {
                index = 'paged';//for right pagination if copy/paste this link and send somebody another by email for example
            }
            if (index == "product-page") {
                return;
            }

            //http://dev.products-filter.com/?swoof=1&woof_author=3&woof_sku&woof_text=single
            //avoid links where values is empty
            if (typeof value !== 'undefined') {
                if ((typeof value && value.length > 0) || typeof value == 'number')
                {
                    if (jQuery.inArray(index, woof_exclude_accept_array) == -1) {

                        link = link + "&" + index + "=" + value;
                    }
                }
            }

        });
    }

    //+++
    //remove wp pagination like 'page/2'
    link = link.replace(new RegExp(/page\/(\d+)/), "");
    if (woof_is_ajax) {
        history.pushState({}, "", link);

    }

    return link;
}



function woof_show_info_popup(text) {
    if (woof_overlay_skin == 'default') {
        jQuery("#woof_html_buffer").text(text);
        jQuery("#woof_html_buffer").fadeTo(200, 0.9);
    } else {
        //http://jxnblk.com/loading/
        switch (woof_overlay_skin) {
            case 'loading-balls':
            case 'loading-bars':
            case 'loading-bubbles':
            case 'loading-cubes':
            case 'loading-cylon':
            case 'loading-spin':
            case 'loading-spinning-bubbles':
            case 'loading-spokes':
                jQuery('body').plainOverlay('show', {progress: function () {
                        //img style should be inlined
                        return jQuery('<div id="woof_svg_load_container"><img style="height: 100%; width: 100%" src="' + woof_link + 'img/loading-master/' + woof_overlay_skin + '.svg" alt=""></div>');
                    }});
                break;
            default:
                jQuery('body').plainOverlay('show', {duration: -1});
                break;
        }
    }
}


function woof_hide_info_popup() {
    if (woof_overlay_skin == 'default') {
        window.setTimeout(function () {
            jQuery("#woof_html_buffer").fadeOut(400);
        }, 200);
    } else {
        jQuery('body').plainOverlay('hide');
    }
}

function woof_draw_products_top_panel() {

    if (woof_is_ajax) {
        jQuery('#woof_results_by_ajax').prev('.woof_products_top_panel').remove();
    }

    var panel = jQuery('.woof_products_top_panel');

    panel.html('');
    if (Object.keys(woof_current_values).length > 0) {
        panel.show();
        panel.html('<ul></ul>');
        panel.find('ul').attr('class', 'woof_products_top_panel_ul');
        var is_price_in = false;
        //lets show this on the panel

        jQuery.each(woof_current_values, function (index, value) {
            //lets filter data for the panel

            if (jQuery.inArray(index, woof_accept_array) == -1 && jQuery.inArray(index.replace("rev_", ""), woof_accept_array) == -1) {
                return;
            }

            //***

            if ((index == 'min_price' || index == 'max_price') && is_price_in) {
                return;
            }

            if ((index == 'min_price' || index == 'max_price') && !is_price_in) {
                is_price_in = true;
                index = 'price';
                value = woof_lang_pricerange;
            }
	    
	    //tax slider  fix
	    var is_range =false;
	    var range_txt = jQuery("input[data-anchor='woof_n_" + index + "_all_range']").val();
	   
	    if(typeof range_txt != 'undefined'){
		is_range = true;
	    }

            //+++
            value = value.toString().trim();
            if (value.search(',')) {
                value = value.split(',');
            }
            //+++
	    if (!is_range) {
		jQuery.each(value, function (i, v) {
		    if (index == 'page') {
			return;
		    }

		    if (index == 'post_type') {
			return;
		    }

		    var txt = v;
		    if (index == 'orderby') {
			if (woof_lang[v] !== undefined) {
			    txt = woof_lang.orderby + ': ' + woof_lang[v];
			} else {
			    txt = woof_lang.orderby + ': ' + v;
			}
		    } else if (index == 'perpage') {
			txt = woof_lang.perpage;
		    } else if (index == 'price') {
			txt = woof_lang.pricerange;
		    } else {

			var is_in_custom = false;
			if (Object.keys(woof_lang_custom).length > 0) {
			    jQuery.each(woof_lang_custom, function (i, tt) {
				if (i == index) {
				    is_in_custom = true;
				    txt = tt;
				    if (index == 'woof_sku') {
					txt += " " + v;//because search by SKU can by more than 1 value
				    }
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


		    }
		    if (typeof woof_filter_titles[index] != 'undefined') {

			var cont_item = panel.find('ul.woof_products_top_panel_ul li ul[data-container=' + index + ']');

			if (cont_item.length) {
			   
			    cont_item.append(
				    jQuery('<li>').append(
				    jQuery('<a>').attr('href', "").attr('data-tax', index).attr('data-slug', v).append(
				    jQuery('<span>').attr('class', 'woof_remove_ppi').append(txt)
				    )));
			} else {
			   
			    panel.find('ul.woof_products_top_panel_ul').append(
				    jQuery('<li>').append(
				    jQuery('<ul>').attr('data-container', index).append(
				    jQuery('<li>').text(woof_filter_titles[index] + ":")).append(
				    jQuery('<li>').append(
				    jQuery('<a>').attr('href', "").attr('data-tax', index).attr('data-slug', v).append(
				    jQuery('<span>').attr('class', 'woof_remove_ppi').append(txt)
				    )))));
			}
		    } else {
			panel.find('ul.woof_products_top_panel_ul').append(
				jQuery('<li>').append(
				jQuery('<a>').attr('href', "").attr('data-tax', index).attr('data-slug', v).append(
				jQuery('<span>').attr('class', 'woof_remove_ppi').append(txt)
				)));
		    }

		});
	    }else{
		    if (typeof woof_filter_titles[index] != 'undefined') {

			var cont_item = panel.find('ul.woof_products_top_panel_ul li ul[data-container=' + index + ']');

			if (cont_item.length) {
			    cont_item.append(
				    jQuery('<li>').append(
				    jQuery('<a>').attr('href', "").attr('data-tax', index).attr('data-slug', 'all_range').append(
				    jQuery('<span>').attr('class', 'woof_remove_ppi').append(range_txt)
				    )));
			} else {
			    panel.find('ul.woof_products_top_panel_ul').append(
				    jQuery('<li>').append(
				    jQuery('<ul>').attr('data-container', index).append(
				    jQuery('<li>').text(woof_filter_titles[index] + ":")).append(
				    jQuery('<li>').append(
				    jQuery('<a>').attr('href', "").attr('data-tax', index).attr('data-slug', 'all_range').append(
				    jQuery('<span>').attr('class', 'woof_remove_ppi').append(range_txt)
				    )))));
			}
		    } else {
			panel.find('ul.woof_products_top_panel_ul').append(
				jQuery('<li>').append(
				jQuery('<a>').attr('href', "").attr('data-tax', index).attr('data-slug', 'all_range').append(
				jQuery('<span>').attr('class', 'woof_remove_ppi').append(range_txt)
				)));
		    }		
	    }


        });
    }


    if (jQuery(panel).find('li').length == 0 || !jQuery('.woof_products_top_panel').length) {
        panel.hide();
    } else {
        panel.find('ul.woof_products_top_panel_ul').prepend(
                jQuery('<li>').append(
                jQuery('<button>').attr('class', "woof_reset_button_2").append(woof_lang.clear_all))
                );
    }

    jQuery('.woof_reset_button_2').on('click', function () {
        woof_ajax_page_num = 1;
        woof_ajax_redraw = 0;
        woof_reset_btn_action = true;

        if (woof_is_permalink) {
            woof_current_values = {};
            woof_submit_link(woof_get_submit_link().split("page/")[0]);
        } else {
            var link = woof_shop_page;
            if (woof_current_values.hasOwnProperty('page_id')) {
                link = location.protocol + '//' + location.host + "/?page_id=" + woof_current_values.page_id;
                woof_current_values = {'page_id': woof_current_values.page_id};
                woof_get_submit_link();
            }
            //***
            woof_submit_link(link);
            if (woof_is_ajax) {
                history.pushState({}, "", link);
                if (woof_current_values.hasOwnProperty('page_id')) {
                    woof_current_values = {'page_id': woof_current_values.page_id};
                } else {
                    woof_current_values = {};
                }
            }
        }
        return false;
    });
    //+++
    jQuery('.woof_remove_ppi').parent().on('click', function (event) {
        event.preventDefault();
        var tax = jQuery(this).data('tax');
        var name = jQuery(this).data('slug');

        //***

        if(name == 'all_range'){
	    delete woof_current_values[tax];
	}else if (tax != 'price') {

            var values = woof_current_values[tax];
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
        } else {
            delete woof_current_values['min_price'];
            delete woof_current_values['max_price'];
        }
        woof_ajax_page_num = 1;
        woof_reset_btn_action = true;
        {
            woof_submit_link(woof_get_submit_link());
        }
        jQuery('.woof_products_top_panel').find("[data-tax='" + tax + "'][href='" + name + "']").hide(333);
        return false;

    });

}

//control conditions if proucts shortcode uses on the page
function woof_shortcode_observer() {

    var redirect = true;
    if (jQuery('.woof_shortcode_output').length || (jQuery('.woocommerce .products').length && !jQuery('.single-product').length)) {
        redirect = false;
    }
    if (jQuery('.woocommerce .woocommerce-info').length) {
        redirect = false;
    }
    if (typeof woof_not_redirect !== 'undefined' && woof_not_redirect == 1) {
        redirect = false;
    }

    if (jQuery('.woot-data-table').length) {
        redirect = false;
    }

    if (!redirect) {
        woof_current_page_link = location.protocol + '//' + location.host + location.pathname;
    }

    if (jQuery('#woof_results_by_ajax').length) {
        woof_is_ajax = 1;
    }
}



function woof_init_beauty_scroll() {
    if (woof_use_beauty_scroll) {
        try {
            var anchor = ".woof_section_scrolled, .woof_sid_auto_shortcode .woof_container";
            jQuery("" + anchor).addClass('woof_use_beauty_scroll');
        } catch (e) {
            console.log(e);
        }
    }
}

//just for inbuilt price range widget
function woof_remove_class_widget() {
    jQuery('.woof_container_inner').find('.widget').removeClass('widget');
}

function woof_init_show_auto_form() {
    jQuery('.woof_show_auto_form').off('click');

    if (jQuery('.woof_show_auto_form.woof_btn').length) {
        jQuery('.woof_btn_default').remove();
    }

    jQuery('.woof_show_auto_form').on('click', function () {
        var _this = this;
        jQuery(_this).addClass('woof_hide_auto_form').removeClass('woof_show_auto_form');
        jQuery(".woof_auto_show").show().animate(
                {
                    height: (jQuery(".woof_auto_show_indent").height() + 20) + "px",
                    opacity: 0.96
                }, 377, function () {
            woof_init_hide_auto_form();
            jQuery('.woof_auto_show').removeClass('woof_overflow_hidden');
            jQuery('.woof_auto_show_indent').removeClass('woof_overflow_hidden');
            jQuery(".woof_auto_show").height('auto');
        });


        return false;
    });

}


//for woof_auto_show closing on blank place click
document.addEventListener('click', function (e) {
    let opened = document.querySelectorAll('.woof_auto_show');
    let target = e.target;
    let close = !target.classList.contains('woof_sid');
    if (close) {
        close = !target.closest('.woof_sid');
    }

    //this close btn self    
    if (target.classList.contains('woof_show_auto_form')) {
        return true;
    }

    if (close && Array.from(opened).length > 0) {
        Array.from(opened).forEach(function (item) {
            if (item.parentNode.querySelector('.woof_hide_auto_form')) {
                item.parentNode.querySelector('.woof_hide_auto_form').click();
            }
        });
    }

    return true;
});

function woof_init_hide_auto_form() {
    jQuery('.woof_hide_auto_form').off('click');
    jQuery('.woof_hide_auto_form').on('click', function () {
        var _this = this;
        jQuery(_this).addClass('woof_show_auto_form').removeClass('woof_hide_auto_form');
        jQuery(".woof_auto_show").show().animate(
                {
                    height: "1px",
                    opacity: 0
                }, 377, function () {

            jQuery('.woof_auto_show').addClass('woof_overflow_hidden');
            jQuery('.woof_auto_show_indent').addClass('woof_overflow_hidden');
            woof_init_show_auto_form();
        });

        return false;
    });


}

//if we have mode - child checkboxes closed - append openers buttons by js
function woof_checkboxes_slide() {
    if (woof_checkboxes_slide_flag) {
        var childs = jQuery('ul.woof_childs_list');
        if (childs.length) {
            jQuery.each(childs, function (index, ul) {

                if (jQuery(ul).parents('.woof_no_close_childs').length) {
                    return;
                }


                var span_class = 'woof_is_closed';
                if (woof_supports_html5_storage()) {
                    //test mode  from 06.11.2017
                    var preulstate = localStorage.getItem(jQuery(ul).closest('li').attr("class"));
                    if (preulstate && preulstate == 'woof_is_opened') {
                        var span_class = 'woof_is_opened';
                        jQuery(ul).show();
                    }
                    jQuery(ul).parent('li').children('label').after('<a href="javascript:void(0);" class="woof_childs_list_opener" title="'+woof_lang.list_opener+'" ><span class="' + span_class + '"></span></a>');
                    //++   
                } else {
                    if (jQuery(ul).find('input[type=checkbox],input[type=radio]').is(':checked')) {
                        jQuery(ul).show();
                        span_class = 'woof_is_opened';
                    }
                    jQuery(ul).parent('li').children('label').after('<a href="javascript:void(0);" class="woof_childs_list_opener" title="'+woof_lang.list_opener+'" ><span class="' + span_class + '"></span></a>');

                }

            });



            jQuery.each(jQuery('a.woof_childs_list_opener span'), function (index, a) {

                jQuery(a).on('click', function () {
                    var span = jQuery(this);
                    var this_ = jQuery(this).parent(".woof_childs_list_opener");
                    if (span.hasClass('woof_is_closed')) {
                        //lets open
                        jQuery(this_).parent().find('ul.woof_childs_list').first().show(333);
                        span.removeClass('woof_is_closed');
                        span.addClass('woof_is_opened');
                    } else {
                        //lets close
                        jQuery(this_).parent().find('ul.woof_childs_list').first().hide(333);
                        span.removeClass('woof_is_opened');
                        span.addClass('woof_is_closed');
                    }

                    if (woof_supports_html5_storage()) {
                        //test mode  from 06.11.2017
                        var ullabel = jQuery(this_).closest('li').attr("class");
                        var ullstate = jQuery(this_).children("span").attr("class");
                        localStorage.setItem(ullabel, ullstate);
                    }
                    return false;
                });
            });
        }
    }
}

function woof_init_ion_sliders() {

    jQuery.each(jQuery('.woof_range_slider'), function (index, input) {
        try {


            jQuery(input).ionRangeSlider({
                min: jQuery(input).data('min'),
                max: jQuery(input).data('max'),
                from: jQuery(input).data('min-now'),
                to: jQuery(input).data('max-now'),
                type: 'double',
                prefix: jQuery(input).data('slider-prefix'),
                postfix: jQuery(input).data('slider-postfix'),
                prettify: true,
                hideMinMax: false,
                hideFromTo: false,
                grid: true,
                step: jQuery(input).data('step'),
                onFinish: function (ui) {
                    var tax = jQuery(input).data('taxes');
                    woof_current_values.min_price = (parseFloat(ui.from, 10) / tax);
                    woof_current_values.max_price = (parseFloat(ui.to, 10) / tax);
                    //woocs adaptation
                    if (typeof woocs_current_currency !== 'undefined') {
                        woof_current_values.min_price = woof_current_values.min_price / parseFloat(woocs_current_currency.rate);
                        woof_current_values.max_price = woof_current_values.max_price / parseFloat(woocs_current_currency.rate);
                    }
                    //***
                    woof_ajax_page_num = 1;
                    if (woof_autosubmit || jQuery(input).within('.woof').length == 0) {
                        woof_submit_link(woof_get_submit_link());
                    }
                    return false;
                },
                onChange: function (data) {
                    if (jQuery('.woof_price_filter_txt')) {
                        var tax = jQuery(input).data('taxes');
                        jQuery('.woof_price_filter_txt_from').val(parseInt(data.from, 10) / tax);
                        jQuery('.woof_price_filter_txt_to').val(parseInt(data.to, 10) / tax);
                        //woocs adaptation
                        if (typeof woocs_current_currency !== 'undefined') {
                            jQuery('.woof_price_filter_txt_from').val(Math.ceil(jQuery('.woof_price_filter_txt_from').val() / parseFloat(woocs_current_currency.rate)));
                            jQuery('.woof_price_filter_txt_to').val(Math.ceil(jQuery('.woof_price_filter_txt_to').val() / parseFloat(woocs_current_currency.rate)));
                        }
                    }
                },
            });
        } catch (e) {

        }
    });
}

function woof_init_native_woo_price_filter() {
    jQuery('.widget_price_filter form').off('submit');
    jQuery('.widget_price_filter form').on('submit', function () {

        var min_price = jQuery(this).find('.price_slider_amount #min_price').val();
        var max_price = jQuery(this).find('.price_slider_amount #max_price').val();
        woof_current_values.min_price = min_price;
        woof_current_values.max_price = max_price;
        woof_ajax_page_num = 1;
       // if (woof_autosubmit) {
            //comment next code row to avoid endless ajax requests
            woof_submit_link(woof_get_submit_link());
       // }
        return false;
    });

}

//we need after ajax redrawing of the search form
function woof_reinit_native_woo_price_filter() {

    // woocommerce_price_slider_params is required to continue, ensure the object exists
    if (typeof woocommerce_price_slider_params === 'undefined') {

        return false;
    }

    // Get markup ready for slider
    jQuery('input#min_price, input#max_price').hide();
    jQuery('.price_slider, .price_label').show();

    // Price slider uses jquery ui
    var min_price = jQuery('.price_slider_amount #min_price').data('min'),
            max_price = jQuery('.price_slider_amount #max_price').data('max'),
            current_min_price = parseInt(min_price, 10),
            current_max_price = parseInt(max_price, 10);

    if (woof_current_values.hasOwnProperty('min_price')) {
        current_min_price = parseInt(woof_current_values.min_price, 10);
        current_max_price = parseInt(woof_current_values.max_price, 10);
    } else {
        if (woocommerce_price_slider_params.min_price) {
            current_min_price = parseInt(woocommerce_price_slider_params.min_price, 10);
        }
        if (woocommerce_price_slider_params.max_price) {
            current_max_price = parseInt(woocommerce_price_slider_params.max_price, 10);
        }
    }

    //***

    var currency_symbol = woocommerce_price_slider_params.currency_symbol;
    if (typeof currency_symbol == 'undefined') {
        currency_symbol = woocommerce_price_slider_params.currency_format_symbol;
    }

    jQuery(document.body).on('price_slider_create price_slider_slide', function (event, min, max) {

        if (typeof woocs_current_currency !== 'undefined') {
            var label_min = min;
            var label_max = max;
            if (typeof currency_symbol == 'undefined') {

                currency_symbol = woocs_current_currency.symbol
            }


            if (woocs_current_currency.rate !== 1) {
                label_min = Math.ceil(label_min * parseFloat(woocs_current_currency.rate));
                label_max = Math.ceil(label_max * parseFloat(woocs_current_currency.rate));
            }

            //+++
            label_min = woof_front_number_format(label_min, 2, '.', ',');
            label_max = woof_front_number_format(label_max, 2, '.', ',');
            if (jQuery.inArray(woocs_current_currency.name, woocs_array_no_cents) || woocs_current_currency.hide_cents == 1) {
                label_min = label_min.replace('.00', '');
                label_max = label_max.replace('.00', '');
            }
            //+++


            if (woocs_current_currency.position === 'left') {

                jQuery('.price_slider_amount span.from').html(currency_symbol + label_min);
                jQuery('.price_slider_amount span.to').html(currency_symbol + label_max);

            } else if (woocs_current_currency.position === 'left_space') {

                jQuery('.price_slider_amount span.from').html(currency_symbol + " " + label_min);
                jQuery('.price_slider_amount span.to').html(currency_symbol + " " + label_max);

            } else if (woocs_current_currency.position === 'right') {

                jQuery('.price_slider_amount span.from').html(label_min + currency_symbol);
                jQuery('.price_slider_amount span.to').html(label_max + currency_symbol);

            } else if (woocs_current_currency.position === 'right_space') {

                jQuery('.price_slider_amount span.from').html(label_min + " " + currency_symbol);
                jQuery('.price_slider_amount span.to').html(label_max + " " + currency_symbol);

            }

        } else {

            if (woocommerce_price_slider_params.currency_pos === 'left') {

                jQuery('.price_slider_amount span.from').html(currency_symbol + min);
                jQuery('.price_slider_amount span.to').html(currency_symbol + max);

            } else if (woocommerce_price_slider_params.currency_pos === 'left_space') {

                jQuery('.price_slider_amount span.from').html(currency_symbol + ' ' + min);
                jQuery('.price_slider_amount span.to').html(currency_symbol + ' ' + max);

            } else if (woocommerce_price_slider_params.currency_pos === 'right') {

                jQuery('.price_slider_amount span.from').html(min + currency_symbol);
                jQuery('.price_slider_amount span.to').html(max + currency_symbol);

            } else if (woocommerce_price_slider_params.currency_pos === 'right_space') {

                jQuery('.price_slider_amount span.from').html(min + ' ' + currency_symbol);
                jQuery('.price_slider_amount span.to').html(max + ' ' + currency_symbol);

            }
        }

        jQuery(document.body).trigger('price_slider_updated', [min, max]);
    });

    jQuery('.price_slider').slider({
        range: true,
        animate: true,
        min: min_price,
        max: max_price,
        values: [current_min_price, current_max_price],
        create: function () {

            jQuery('.price_slider_amount #min_price').val(current_min_price);
            jQuery('.price_slider_amount #max_price').val(current_max_price);

            jQuery(document.body).trigger('price_slider_create', [current_min_price, current_max_price]);
        },
        slide: function (event, ui) {

            jQuery('input#min_price').val(ui.values[0]);
            jQuery('input#max_price').val(ui.values[1]);

            jQuery(document.body).trigger('price_slider_slide', [ui.values[0], ui.values[1]]);
        },
        change: function (event, ui) {
            jQuery(document.body).trigger('price_slider_change', [ui.values[0], ui.values[1]]);
        }
    });


    //***
    woof_init_native_woo_price_filter();
}

function woof_mass_reinit() {
    woof_remove_empty_elements();
    woof_open_hidden_li();
    woof_init_search_form();
    woof_hide_info_popup();
    woof_init_beauty_scroll();
    woof_init_ion_sliders();
    woof_reinit_native_woo_price_filter();//native woo price range slider reinit
    woof_recount_text_price_filter();
    woof_draw_products_top_panel();
}

function woof_recount_text_price_filter() {
    //change value in textinput price filter if WOOCS is installed
    if (typeof woocs_current_currency !== 'undefined') {
        jQuery.each(jQuery('.woof_price_filter_txt_from, .woof_price_filter_txt_to'), function (i, item) {
            jQuery(this).val(Math.ceil(jQuery(this).data('value')));
        });
    }
}

function woof_init_toggles() {

    jQuery('body').off('click', '.woof_front_toggle');
    jQuery('body').on('click', '.woof_front_toggle', function () {

        if (jQuery(this).data('condition') == 'opened') {
            jQuery(this).removeClass('woof_front_toggle_opened');
            jQuery(this).addClass('woof_front_toggle_closed');
            jQuery(this).data('condition', 'closed');

            if (woof_toggle_type == 'text') {
                jQuery(this).text(woof_toggle_closed_text);
            } else {
                jQuery(this).find('img').prop('src', woof_toggle_closed_image);
            }
        } else {

            jQuery(this).addClass('woof_front_toggle_opened');
            jQuery(this).removeClass('woof_front_toggle_closed');
            jQuery(this).data('condition', 'opened');
            if (woof_toggle_type == 'text') {
                jQuery(this).text(woof_toggle_opened_text);
            } else {
                jQuery(this).find('img').prop('src', woof_toggle_opened_image);
            }
        }

        jQuery(this).parents('.woof_container_inner').find('.woof_block_html_items').slideToggle(500);

        /* fix  for chosen*/
        var is_chosen_here = jQuery(this).parents('.woof_container_inner').find('.chosen-container');
        if (is_chosen_here.length && jQuery(this).hasClass('woof_front_toggle_opened')) {
            jQuery(this).parents('.woof_container_inner').find('select').chosen('destroy').trigger("liszt:updated");
            jQuery(this).parents('.woof_container_inner').find('select').chosen(/*{disable_search_threshold: 10}*/);
        }
        if (jQuery(this).hasClass('woof_front_toggle_opened')) {
            woof_reinit_selects()
        }

        return false;
    });
}

//for "Show more" blocks
function woof_open_hidden_li() {
    if (jQuery('.woof_open_hidden_li_btn').length > 0) {
        jQuery.each(jQuery('.woof_open_hidden_li_btn'), function (i, b) {
            if (jQuery(b).parents('ul').find('li.woof_hidden_term input[type=checkbox],li.woof_hidden_term input[type=radio]').is(':checked')) {
                jQuery(b).trigger('click');
            }
        });
    }
}

//http://stackoverflow.com/questions/814613/how-to-read-get-data-from-a-url-using-javascript
function $_woof_GET(q, s) {
    s = (s) ? s : window.location.search;
    var re = new RegExp('&' + q + '=([^&]*)', 'i');
    return (s = s.replace(/^\?/, '&').match(re)) ? s = s[1] : s = '';
}

function woof_parse_url(url) {
    var pattern = RegExp("^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\\?([^#]*))?(#(.*))?");
    var matches = url.match(pattern);
    return {
        scheme: matches[2],
        authority: matches[4],
        path: matches[5],
        query: matches[7],
        fragment: matches[9]
    };
}


//      woof price radio;
function woof_price_filter_radio_init() {
    if (icheck_skin != 'none') {
        jQuery('.woof_price_filter_radio').iCheck('destroy');

        jQuery('.woof_price_filter_radio').iCheck({
            radioClass: 'iradio_' + icheck_skin.skin + '-' + icheck_skin.color,

        });

        jQuery('.woof_price_filter_radio').siblings('div').removeClass('checked');

        jQuery('.woof_price_filter_radio').off('ifChecked');
        jQuery('.woof_price_filter_radio').on('ifChecked', function (event) {
            jQuery(this).attr("checked", true);
            jQuery('.woof_radio_price_reset').removeClass('woof_radio_term_reset_visible');
            jQuery(this).parents('.woof_list').find('.woof_radio_price_reset').removeClass('woof_radio_term_reset_visible');
            jQuery(this).parents('.woof_list').find('.woof_radio_price_reset').hide();
            jQuery(this).parents('li').eq(0).find('.woof_radio_price_reset').eq(0).addClass('woof_radio_term_reset_visible');
            var val = jQuery(this).val();
            if (parseInt(val, 10) == -1) {
                delete woof_current_values.min_price;
                delete woof_current_values.max_price;
                jQuery(this).removeAttr('checked');
                jQuery(this).siblings('.woof_radio_price_reset').removeClass('woof_radio_term_reset_visible');
            } else {
                var val = val.split("-");
                woof_current_values.min_price = val[0];
                woof_current_values.max_price = val[1];
                jQuery(this).siblings('.woof_radio_price_reset').addClass('woof_radio_term_reset_visible');
                jQuery(this).attr("checked", true);
            }
            if (woof_autosubmit || jQuery(this).within('.woof').length == 0) {
                woof_submit_link(woof_get_submit_link());
            }
        });

    } else {
        jQuery('body').on('change', '.woof_price_filter_radio', function () {
            var val = jQuery(this).val();
            jQuery('.woof_radio_price_reset').removeClass('woof_radio_term_reset_visible');
            if (parseInt(val, 10) == -1) {
                delete woof_current_values.min_price;
                delete woof_current_values.max_price;
                jQuery(this).removeAttr('checked');
                jQuery(this).siblings('.woof_radio_price_reset').removeClass('woof_radio_term_reset_visible');
            } else {
                var val = val.split("-");
                woof_current_values.min_price = val[0];
                woof_current_values.max_price = val[1];
                jQuery(this).siblings('.woof_radio_price_reset').addClass('woof_radio_term_reset_visible');
                jQuery(this).attr("checked", true);
            }
            if (woof_autosubmit || jQuery(this).within('.woof').length == 0) {
                woof_submit_link(woof_get_submit_link());
            }
        });
    }
    //***
    jQuery('.woof_radio_price_reset').on('click', function () {
        delete woof_current_values.min_price;
        delete woof_current_values.max_price;
        jQuery(this).siblings('div').removeClass('checked');
        jQuery(this).parents('.woof_list').find('input[type=radio]').removeAttr('checked');

        jQuery(this).removeClass('woof_radio_term_reset_visible');
        if (woof_autosubmit) {
            woof_submit_link(woof_get_submit_link());
        }
        return false;
    });
}
//    END  woof price radio;



//compatibility with YITH Infinite Scrolling
function woof_serialize(serializedString) {
    var str = decodeURI(serializedString);
    var pairs = str.split('&');
    var obj = {}, p, idx, val;
    for (var i = 0, n = pairs.length; i < n; i++) {
        p = pairs[i].split('=');
        idx = p[0];

        if (idx.indexOf("[]") == (idx.length - 2)) {
            // Eh um vetor
            var ind = idx.substring(0, idx.length - 2)
            if (obj[ind] === undefined) {
                obj[ind] = [];
            }
            obj[ind].push(p[1]);
        } else {
            obj[idx] = p[1];
        }
    }
    return obj;
}


//compatibility with YITH Infinite Scrolling
function woof_infinite() {

    if (typeof yith_infs === 'undefined') {
        return;
    }


    //***
    var infinite_scroll1 = {
        //'nextSelector': ".woof_infinity .nav-links .next",
        'nextSelector': '.woocommerce-pagination li .next',
        'navSelector': yith_infs.navSelector,
        'itemSelector': yith_infs.itemSelector,
        'contentSelector': yith_infs.contentSelector,
        'loader': '<img src="' + yith_infs.loader + '">',
        'is_shop': yith_infs.shop
    };
    var curr_l = window.location.href;
    var curr_link = curr_l.split('?');
    var get = "";
    if (curr_link[1] != undefined) {
        var temp = woof_serialize(curr_link[1]);
        delete temp['paged'];
        get = decodeURIComponent(jQuery.param(temp))
    }

    var page_link = jQuery('.woocommerce-pagination li .next').attr("href");

    if (page_link == undefined) {
        page_link = curr_link + "page/1/"
    }

    var ajax_link = page_link.split('?');
    var page = "";
    if (ajax_link[1] != undefined) {
        var temp1 = woof_serialize(ajax_link[1]);
        if (temp1['paged'] != undefined) {
            page = "/page/" + temp1['paged'] + "/";
        }
    }

    page_link = curr_link[0].replace(/\/$/, "") + page + '?' + get;

    jQuery('.woocommerce-pagination li .next').attr('href', page_link);

    jQuery(window).off("yith_infs_start"), jQuery(yith_infs.contentSelector).yit_infinitescroll(infinite_scroll1)
}
//End infinity scroll

//fix  if woof - is ajax  and  cart - is redirect
function woof_change_link_addtocart() {
    if (!woof_is_ajax) {
        return;
    }
    jQuery(".add_to_cart_button").each(function (i, elem) {
        var link = jQuery(elem).attr('href');
        if (link) {
            var link_items = link.split("?");
            var site_link_items = window.location.href.split("?");
            if (link_items[1] != undefined) {
                link = site_link_items[0] + "?" + link_items[1];
                jQuery(elem).attr('href', link);
            }
        }
    });

}
//https://github.com/kvz/phpjs/blob/master/functions/strings/number_format.js
function woof_front_number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };
// Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
                .join('0');
    }
    return s.join(dec);
}

//additional function to check local storage

function woof_supports_html5_storage() {
    try {
        return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e) {
        return false;
    }
}

function woof_init_tooltip() {
    var tooltips = jQuery(".woof_tooltip_header");

    if (tooltips.length) {

        jQuery(tooltips).tooltipster({
            theme: 'tooltipster-noir',
            side: 'right',
            trigger: 'click'
        });
    }

}
function woof_before_ajax_form_redrawing() {
    if (woof_select_type == 'selectwoo') {
        try {
            jQuery("select.woof_mselect").selectWoo('destroy');
            jQuery("select.woof_meta_mselect").selectWoo('destroy');
        } catch (e) {
            return false;
        }

    }

}
function woof_reinit_selects() {
    if (woof_select_type == 'chosen') {
        try {
            jQuery("select.woof_select, select.woof_mselect").chosen('destroy').trigger("liszt:updated");
            jQuery("select.woof_select, select.woof_mselect").chosen(/*{disable_search_threshold: 10}*/);
            jQuery("select.woof_meta_select, select.woof_meta_mselect").chosen('destroy').trigger("liszt:updated");
            jQuery("select.woof_meta_select, select.woof_meta_mselect").chosen(/*{disable_search_threshold: 10}*/);
        } catch (e) {

        }
    } else if (woof_select_type == 'selectwoo') {
        try {
            jQuery("select.woof_select, select.woof_mselect").selectWoo('destroy');
            jQuery("select.woof_select, select.woof_mselect").selectWoo();
            jQuery("select.woof_meta_select, select.woof_meta_mselect").selectWoo('destroy');
            jQuery("select.woof_meta_select, select.woof_meta_mselect").selectWoo();
        } catch (e) {

        }

    }
}
function woof_init_mobile_filter() {
    var show_btn = jQuery('.woof_show_mobile_filter');
    var show_btn_container = jQuery('.woof_show_mobile_filter_container');
    var def_container = jQuery(woof_m_b_container);
    if (!show_btn_container.length) {
        show_btn_container = def_container;
    }
    if (show_btn && show_btn_container) {
        jQuery(show_btn_container).append(show_btn);
    }


    jQuery('.woof_show_mobile_filter').on('click', function (e) {
        var sid = jQuery(this).data('sid');
        jQuery('.woof.woof_sid_' + sid).toggleClass('woof_show_filter_for_mobile');
        setTimeout(function () {
            try {
                jQuery('.woof.woof_sid_' + sid).find("select.woof_mselect").chosen('destroy');
                jQuery('.woof.woof_sid_' + sid).find("select.woof_select").chosen('destroy');
                jQuery('.woof.woof_sid_' + sid).find("select.woof_mselect").chosen();
                jQuery('.woof.woof_sid_' + sid).find("select.woof_select").chosen();
            } catch (e) {

            }
        }, 300);

    });
    jQuery('.woof_hide_mobile_filter').on('click', function (e) {
        jQuery(this).parents('.woof').toggleClass('woof_show_filter_for_mobile');
    });


}