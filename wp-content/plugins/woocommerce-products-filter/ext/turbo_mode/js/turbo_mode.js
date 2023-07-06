"use strict";
//init global array file
var woof_turbo_mode_file = [];

var WoofTurboMode_obj = function (data) {
    
    this.file_link = data.link;
    this.preload = data.pre_load;
    this.products_data = [];
    this.filter_settings = data.settings;
    this.sale_ids = data.sale_ids;

    this.show_count = parseInt(data.show_count);
    this.dynamic_recount_val = parseInt(data.dynamic_recount);
    this.hide_empty_term = parseInt(data.hide_empty_term);
    this.hide_count = parseInt(data.hide_count);

    this.messenger_btn = {};

    this.curr_tax = {};
    if (typeof data.current_tax.tax !== undefined) {
        this.curr_tax = data.current_tax;
    }
    
    this.additional_tax = {};
    if (typeof data.additional_tax !== undefined &&  data.additional_tax.length) {
        this.additional_tax = data.additional_tax;
    }

    this.keys_array = function (data) {
        var array_keys = {};
        array_keys['taxonomies'] = [];
        
        jQuery.each(data.settings.excluded_terms, function (i, item) {
            var logic = "OR";

            if (data.settings.comparison_logic[i] != undefined ) {
                logic = data.settings.comparison_logic[i];
            }
            array_keys['taxonomies'][i] = logic;
        });
        array_keys['meta'] = [];
        if(typeof data.settings.meta_filter != 'undefined'){
            jQuery.each(data.settings.meta_filter, function (i, item) {
                var search_logic = "OR";
                var checkbox_logic = "";
                var text_conditional = "";
                if (data.settings[i] != undefined && data.settings[i]['search_logic'] != undefined && data.settings[i]['search_logic'] == "AND") {
                    search_logic = "AND";
                }
                if (data.settings[i] != undefined) {
                    if (data.settings[i]['search_option'] != undefined && data.settings[i]['search_option'] == 1) {
                        checkbox_logic = "exist";
                    } else if (data.settings[i]['search_option'] != undefined && data.settings[i]['search_value'] != undefined && data.settings[i]['search_option'] == 0) {
                        if (data.settings[i]['search_value'].length) {
                            checkbox_logic = data.settings[i]['search_value'];
                        } else {
                            checkbox_logic = "exist";
                        }
                    }
                    if (data.settings[i]['text_conditional'] != undefined) {
                        checkbox_logic = data.settings[i]['text_conditional'];
                    }

                }
                item['search_logic'] = search_logic;
                item['checkbox_logic'] = checkbox_logic;
                item['text_conditional'] = checkbox_logic;
                array_keys['meta'][item["search_view"] + "_" + i] = item;

            });
        }
        var only = ['max_price', 'woof_text', 'min_rating', 'woof_author', 'woof_sku', 'stock', 'backorder', 'onsales'];
        array_keys['only'] = {};
        jQuery.each(only, function (i, item) {
            switch (item) {
                case "woof_sku":
                    array_keys['only'][item] = {};
                    array_keys['only'][item]['logic'] = "LIKE";
                    if (typeof data.settings['by_sku'] != 'undefined' && data.settings['by_sku']['logic'] != undefined) {
                        array_keys['only'][item]['logic'] = data.settings['by_sku']['logic'];
                    }
                    break;
                case "onsales":
                    array_keys['only'][item] = {};
                    array_keys['only'][item]['ids'] = data.sale_ids;
                    break;
                case "woof_text":
                    array_keys['only'][item] = {};
                    array_keys['only'][item]['search_by_full_word'] = 0;
                    if (typeof data.settings['by_text'] != 'undefined' && data.settings['by_text']['search_by_full_word'] != undefined) {
                        array_keys['only'][item]['search_by_full_word'] = data.settings['by_text']['search_by_full_word'];
                    }
                    break;
                default:
                    array_keys['only'][item] = {};

            }
        });
        return array_keys;
    };
    // init search key
    this.possible_terms = this.keys_array(data);

    this.init = function () {

        woof_is_ajax = 1;

        this.uploadFile();
    };
    this.do_after_upload = function (_this) {

        /*count after upload file*/
        if (_this.show_count) {
            jQuery(document).ready(function () {
                var filters = {};
                if (_this.dynamic_recount_val) {
                    filters = _this.dynamic_recount(woof_current_values);
                } else {
                    filters = _this.dynamic_recount({});
                }
                jQuery(".woof_turbo_mode_overlay").show();
                jQuery.each(filters, function (i, filter) {

                    var filter_count = 0;
                    jQuery.each(filter, function (ind, items) {
                        var count = 0;
                        var last = false;
                        if (Object.keys(items).length) {
                            filter_count = ind;
                        }
                        jQuery.each(items, function (indx, item) {
                            /* split streams */
                            setTimeout(function () {
                                last = false;
                                var res = {};
                                if (!item.current) {
                                    var recount=false;
                                    if(typeof _this.possible_terms.taxonomies[item.key.replace('rev_','')] !='undefined' && _this.possible_terms.taxonomies[item.key.replace('rev_','')]=="NOT IN"){                                            
                                        recount=true;
                                    }                                     
                                    res = _this.search(item.query,recount);
                                    //array unique
                                    res = res.filter((v, i, a) => a.indexOf(v) === i);
                                } else {
                                    count++;
                                }
                                filters[i][ind][indx].count = res.length;
                                if (res.length > 0) {
                                    count++;
                                }
                                if (typeof filters[i][ind][+indx + 1] == 'undefined') {
                                    last = true;
                                }

                                _this.draw_count_item(filters[i][ind][indx], count, last, _this);
                                if (last && filter_count == ind) {
                                    jQuery(".woof_turbo_mode_overlay").hide();
                                }
                                last = false;

                            }, 1);
                        });

                    });
                    _this.check_messenger_btn(_this);
                    _this.check_save_query_btn(_this);

                });

            });
        } else {
            jQuery(".woof_turbo_mode_overlay").hide();
        }

    }

    this.draw_counts = function (filters) { /*not used*/
        jQuery.each(filters, function (i, filter) {
            jQuery.each(filter, function (ind, items) {
                jQuery.each(items, function (indx, item) {
                    if (item.type == 'radio' || item.type == 'checkbox') {
                        jQuery(item.label).find(".woof_turbo_count").remove();
                        jQuery(item.label).append("<span class='woof_turbo_count'>(" + item.count + ")</span>");
                    }
                    if (item.type == 'drop_down') {

                        jQuery(item.label).attr('data-count', item.count);
                        var txt = jQuery(item.label).text();
                        txt = txt.replace(/\(.*?\)/g, "");
                        txt = txt.replace(/\s*$/, '');
                        jQuery(item.label).text(txt + " (" + item.count + ")");

                    }
                });
            });
        });
	
	woof_reinit_selects();	
	
    }
    this.draw_count_item = function (item, count, last, _this) {

        var hide_empty_term = _this.hide_empty_term;
        var hide_count = _this.hide_count;

        if (item.type == 'radio' || item.type == 'checkbox') {
            jQuery(item.label).find(".woof_turbo_count").remove();
            if (!item.current && !hide_count) {
                jQuery(item.label).append("<span class='woof_turbo_count'>(" + item.count + ")</span>");
            }

            if (item.count <= 0 && hide_empty_term) {
                jQuery(item.label).parent().addClass("woof_turbo_hide");
            } else {
                jQuery(item.label).parent().removeClass("woof_turbo_hide");
            }
            if (item.current) {
                jQuery(item.label).parent().removeClass("woof_turbo_hide");
            }

            if (last) {

                if (count == 0 && hide_empty_term) {

                    jQuery(item.label).parents(".woof_container").addClass("woof_turbo_hide");
                } else {

                    jQuery(item.label).parents(".woof_container").removeClass("woof_turbo_hide");
                }
                _this.check_show_more_less(_this, item.key);
            }
        }
        if (item.type == 'meta_checkbox') {

            jQuery(item.label).find(".woof_turbo_count").remove();

            if (!item.current && !hide_count) {
                jQuery(item.label).append("<span class='woof_turbo_count'>(" + item.count + ")</span>");
            }

            if (item.count <= 0 && hide_empty_term) {
                jQuery(item.label).parent().addClass("woof_turbo_hide");
            } else {
                count++;
                jQuery(item.label).parent().removeClass("woof_turbo_hide");
            }
            if (item.current) {
                jQuery(item.label).parent().removeClass("woof_turbo_hide");
            }
            if (last) {
                if (count == 0 && hide_empty_term) {
                    jQuery(item.label).parents(".woof_container").addClass("woof_turbo_hide");
                } else {
                    jQuery(item.label).parents(".woof_container").removeClass("woof_turbo_hide");
                }

            }

        }
        if (item.type == 'meta_datepicker') {
            if (typeof woof_current_values[item.tax] == "undefined") {

                if (item.count == 0 && hide_empty_term) {

                    jQuery(item.label).addClass("woof_turbo_hide");
                } else {
                    jQuery(item.label).removeClass("woof_turbo_hide");
                }
            } else {
                jQuery(item.label).removeClass("woof_turbo_hide");
            }
        }
        if (item.type == 'drop_down') {

            jQuery(item.label).attr('data-count', item.count);
            var txt = jQuery(item.label).text();
            txt = txt.replace(/\(.*?\)/g, "");
            txt = txt.replace(/\s*$/, '');

            if (!item.current && !hide_count) {

                jQuery(item.label).text(txt + " (" + item.count + ")");
            } else {

                jQuery(item.label).text(txt);
            }

            if (item.count <= 0 && hide_empty_term) {
                jQuery(item.label).addClass("woof_turbo_hide");
            } else {
                jQuery(item.label).removeClass("woof_turbo_hide");
            }
            if (item.current) {
                jQuery(item.label).removeClass("woof_turbo_hide");
            }
            if (last) {
                if (count == 0 && hide_empty_term) {
                    jQuery(item.label).parents(".woof_container").addClass("woof_turbo_hide");
                } else {
                    jQuery(item.label).parents(".woof_container").removeClass("woof_turbo_hide");
                }

		if (woof_select_type == 'chosen') {
		    try {
			jQuery(item.label).parent('select').chosen('destroy').trigger("liszt:updated");
			jQuery(item.label).parent('select').chosen(/*{disable_search_threshold: 10}*/);
		    } catch (e) {

		    }
		} else if (woof_select_type == 'selectwoo') {
		    try {
			jQuery(item.label).parent('select').selectWoo('destroy');
			jQuery(item.label).parent('select').selectWoo();
		    } catch (e) {

		    }	

		}

            }
        }
        if (item.type == 'slider') {
            if (woof_current_values[item.tax] == undefined) {
                if (item.count == 0 && hide_empty_term) {
                    jQuery(item.label).parents(".woof_container_slider").addClass("woof_turbo_hide");
                } else {
                    jQuery(item.label).parents(".woof_container_slider").removeClass("woof_turbo_hide");
                }
            } else {
                jQuery(item.label).parents(".woof_container_slider").removeClass("woof_turbo_hide");
            }
        }
        if (item.type == 'meta_slider') {
            if (woof_current_values[item.tax] == undefined) {
                if (item.count == 0 && hide_empty_term) {
                    jQuery(item.label).parents(".woof_meta_slider_container").addClass("woof_turbo_hide");
                } else {
                    jQuery(item.label).parents(".woof_meta_slider_container").removeClass("woof_turbo_hide");
                }
            } else {
                jQuery(item.label).parents(".woof_meta_slider_container").removeClass("woof_turbo_hide");
            }
        }
        if (item.type == 'color_image') {
            var item_label = jQuery(item.label).parents("li").find(".woof_tooltip_data");
            jQuery(item_label).find(".woof_turbo_count").remove();
            if (!item.current && !hide_count) {
                jQuery(item_label).append("<span class='woof_turbo_count'>(" + item.count + ")</span>");
            }
            if (item.count <= 0 && hide_empty_term) {
                jQuery(item.label).parents("li").addClass("woof_turbo_hide");
            } else {
                jQuery(item.label).parents("li").removeClass("woof_turbo_hide");
            }
            if (item.current) {
                jQuery(item.label).parents("li").removeClass("woof_turbo_hide");
            }
            if (last) {
                if (count == 0 && hide_empty_term) {
                    jQuery(item.label).parents(".woof_container").addClass("woof_turbo_hide");
                } else {
                    jQuery(item.label).parents(".woof_container").removeClass("woof_turbo_hide");
                }
                _this.check_show_more_less(_this, item.key);
            }
        }
        if (item.type == 'label') {

            jQuery(item.label).find(".woof_label_count").remove();
            if (!item.current && !hide_count) {
                jQuery(item.label).prepend("<span class='woof_label_count'>" + item.count + "</span>");
            }
            if (item.count <= 0 && hide_empty_term) {
                jQuery(item.label).addClass("woof_turbo_hide");
            } else {
                jQuery(item.label).removeClass("woof_turbo_hide");
            }
            if (item.current) {
                jQuery(item.label).removeClass("woof_turbo_hide");
            }
            if (last) {
                if (count == 0 && hide_empty_term) {
                    jQuery(item.label).parents(".woof_container").addClass("woof_turbo_hide");
                } else {
                    jQuery(item.label).parents(".woof_container").removeClass("woof_turbo_hide");
                }

                _this.check_show_more_less(_this, item.key);
            }

        }

    }
    this.uploadFile = function () {
        var do_after_upload = this.do_after_upload;
        var _this = this;
        if (!woof_turbo_mode_file.length) {
            jQuery.getJSON(this.file_link, function (file_data) {

            }).done(function (file_data) {
                woof_turbo_mode_file = file_data;
                console.log("Turbo mode file downloaded!");
                do_after_upload(_this);
            }).fail(function () {
                console.log("I can not access files!  Please create data file OR  check  .htaccess  settings");
            });
        }
    };


    this.do_query = function (where) {
        var ids = alasql("SELECT COLUMN id FROM ? AS d WHERE " + where, [woof_turbo_mode_file]);
        return  ids;
    }
    this.get_query = function (query, possible_terms, inlude_var,recount) {

        var query_tmp = [];
        var tax_q = this.taxonomy_query;
        var meta_q = this.meta_query;
        var only_q = this.only_query;
        var visibility_q = this.get_visibility_tax;
        var variation_q = this.without_variation;


        /* to add  current  category */
        if (typeof this.curr_tax.tax != 'undefined') {
            query_tmp.push(tax_q(this.curr_tax.tax, this.curr_tax.slug, possible_terms['taxonomies'][this.curr_tax.tax]));
        }
        /* to add  additional tax */
        
        if(this.additional_tax.length){
            jQuery.each(this.additional_tax,function(i,add_tax){
                query_tmp.push(tax_q(add_tax.tax, add_tax.terms, possible_terms['taxonomies'][add_tax.tax]));
            });
        }

        if (typeof inlude_var == 'undefined' || !inlude_var) {
            query_tmp.push(variation_q());
        }

        jQuery.each(query, function (i, item) {
            
            if (possible_terms['taxonomies'][i] != undefined || possible_terms['taxonomies'][i.replace('rev_','')] != undefined) {
                i=i.replace('rev_','');
                var logic=possible_terms['taxonomies'][i];
                
                if (typeof recount != 'undefined' && recount==true && possible_terms['taxonomies'][i]=="NOT IN") {
                    logic="OR";
                }
                query_tmp.push(tax_q(i, item,logic ));
            } else if (possible_terms['meta'][i] != undefined) {
                query_tmp.push(meta_q(i, item, possible_terms['meta'][i]));
            } else if (possible_terms['only'][i] != undefined) {
                query_tmp.push(only_q(i, item, possible_terms['only'][i]));
            }
        })

        if (query_tmp.length > 0) {
            query_tmp.push(visibility_q(true));
        } else {
            query_tmp.push(visibility_q(false));
        }
        query_tmp = query_tmp.join(" AND ");
        if (query_tmp.length) {
            return query_tmp;
        }
        return " 1 ";
    }

    /* generate queries */
    this.get_visibility_tax = function (search) {
        if (typeof search == "undefined") {
            search = false;
        }
        return " (get_visibility(taxonomies," + search + ")= true) ";
    }

    this.taxonomy_query = function (key, data, logic) {
        var query = [];
        data = data + "";
        var data_arr = data.split(',');
        if(logic=="NOT IN"){
            jQuery.each(data_arr, function (i, item) {
                query.push(" taxonomies->('" + key + "')->indexOf('" + item + "')== -1 ");
            }); 
            logic="AND";
        }else{
            jQuery.each(data_arr, function (i, item) {
                query.push(" taxonomies->('" + key + "')->indexOf('" + item + "')> -1 ");
            });            
        }

        return "( " + query.join(logic) + " )";
    }
    this.meta_query = function (key, data, settings) {
        var query = [];
        switch (settings["search_view"]) {
            case "select":
            case "mselect":
                var data_arr = data.split(',');
                var options = settings['options'].split(',');
                jQuery.each(data_arr, function (i, item) {
                    var value = options[item - 1];
                    var test_text = value.split('^');
                    if (test_text[1] != undefined) {
                        value = test_text[1];
                    }
                    query.push(" meta_data->('" + settings["meta_key"] + "') = '" + value + "'");
                });
                query = "( " + query.join(" " + settings["search_logic"] + " ") + ") ";
                break;
            case "checkbox":
                if (settings["checkbox_logic"] == "exist") {
                    query = " meta_data->('" + settings["meta_key"] + "')!='undefined' ";
                } else {
                    query = " meta_data->('" + settings["meta_key"] + "')='" + settings["checkbox_logic"] + "' ";
                }

                break;
            case "textinput":

                if (settings["text_conditional"] == "LIKE") {
                    query = " meta_data->('" + settings["meta_key"] + "') LIKE '%" + data + "%'";
                } else {
                    query = " meta_data->('" + settings["meta_key"] + "')='" + data + "' ";
                }

                break;
            case "slider":
                var data_arr = data.split('^');
                if (data_arr.length > 1) {
                    query = " (meta_data->('" + settings["meta_key"] + "') BETWEEN " + data_arr[0] + " AND " + data_arr[1] + ") ";
                } else {
                    query = "";
                }

                break;
            case "datepicker":
                var data_arr = data.split('-');
                if (data_arr.length > 1) {
                    if (data_arr[0] != "i" && data_arr[1] != "i") {
                        query = " (meta_data->('" + settings["meta_key"] + "') BETWEEN '" + data_arr[0] + "' AND '" + data_arr[1] + "') ";
                    } else if ((data_arr[0] == "i" && data_arr[1] != "i") || (data_arr[0] != "i" && data_arr[1] == "i")) {

                        var compare = " > ";
                        var val = data_arr[0];
                        if (data_arr[0] == "i" && data_arr[1] != "i") {
                            var compare = " < ";
                            var val = data_arr[1];
                        }

                        query = " (" + val + compare + " meta_data->('" + settings["meta_key"] + "')) ";

                    }

                } else {
                    query = "";
                }

                break;

        }
        return query;
    }
    this.only_query = function (key, data, settings) {

        var query = "";
        switch (key) {
            case "woof_author":
                query = " author='" + data + "' ";
                break;
            case "woof_sku":
                if (settings['logic'] == "LIKE") {
                    query = " sku LIKE '%" + data + "%' ";
                } else {
                    query = " sku='" + data + "' ";
                }

                break;
            case "stock":
                var is_simple = true;
                if (typeof WoofTurboMode.filter_settings['by_instock'] != "undefined" && WoofTurboMode.filter_settings['by_instock']["use_for"] != "simple") {
                    is_simple = false;
                }
                if (is_simple) {
                    query = " (stock='instock')";
                } else {
                    var count_query_r = Object.assign({}, woof_current_values);
                    if (typeof count_query_r["stock"] != "undefined") {
                        delete count_query_r["stock"];
                    }
                    var instock_g = WoofTurboMode.get_query(count_query_r, WoofTurboMode.possible_terms, true);

                    var res = alasql("SELECT COLUMN parent FROM ? AS d WHERE " + instock_g + " AND (parent!='-1' AND stock='outofstock')", [woof_turbo_mode_file]);
                    res = res.filter((v, i, a) => a.indexOf(v) === i);
                    if(res.length){
                        query = "( (stock='instock' ) OR check_id(id," + res + ")=true )";
                    }else{
                        query = " (stock='instock')";
                    }
                }

                break;
            case "onsales":
                query = " (id = ANY(" + Object.values(settings.ids).join(',') + ") )";
                break;
            case "backorder":
                query = " (meta_data->_stock_status !='onbackorder') ";
                break;
            case "min_rating":
                query = "(meta_data->_wc_average_rating BETWEEN '" + parseFloat(data) + "' AND '" + (parseFloat(data) + 1.1) + "' )";
                break;
            case "woof_text":
                data = data.replace(new RegExp('%20', 'g'), " ");
                var text_array = data.split(" ");
                var text_query = [];

                if (settings['search_by_full_word']) {
                    jQuery.each(text_array, function (i, item) {
                        text_query.push(" title LIKE '%" + item + "%' ");
                    });
                } else {
                    jQuery.each(text_array, function (i, item) {
                        text_query.push(" title ='" + item + "' ");
                    });
                }
                query = " (" + text_query.join(" OR ") + ") ";

                break;

            case "max_price":
                var min_price = woof_current_values.min_price;
                if (min_price == undefined) {
                    min_price = 0;
                }
                query = "( (get_min_price(d.price) BETWEEN " + min_price + " AND " + data + ") OR (get_max_price(d.price) BETWEEN " + min_price + " AND " + data + ")) ";
                break;

        }
        return query;
    }
    this.without_variation = function () {
        return "( parent=-1 )";
    }
    /* end generate queries */

    /* price */
    alasql.fn.get_max_price = function (_prices) { /*init function*/
        var price = WoofTurboMode.get_price_limits(_prices);
        return price['max'];
    }
    alasql.fn.get_min_price = function (_prices) {/*init function*/
        var price = WoofTurboMode.get_price_limits(_prices);
        return price['min'];
    }
    this.get_price_limits = function (prices) {
        var result = [];

        result['min'] = 0.0;
        result['max'] = 0.0;
        if (prices == undefined) {
            return result;
        }
        if (prices.length == 1) {
            if (prices[0]['sale'] != "" && prices[0]['sale'] != null && parseFloat(prices[0]['sale']) < parseFloat(prices[0]['regular'])) {
                result['min'] = result['max'] = prices[0]['sale'];

            } else {
                result['min'] = result['max'] = prices[0]['regular'];

            }
        } else if (prices.length > 1) {
            var min = 0.0;
            var max = 0.0;
            max = min = prices[0]['regular'];
            jQuery.each(prices, function (key, val) {
                var curr_price = 0.0;
                if (val['sale'] != "" && val['sale'] != null && parseFloat(val['sale']) < parseFloat(val['regular'])) {
                    curr_price = parseFloat(val['sale']);
                } else {
                    curr_price = parseFloat(val['regular']);
                }
                if (curr_price < min) {
                    min = curr_price
                }
                if (curr_price > max) {
                    max = curr_price
                }
            });
            result['min'] = min;
            result['max'] = max;
        }
        return result;
    }
    /* end price */
    /* visibility */
    alasql.fn.get_visibility = function (_taxonomies, search) {/*init function*/
        var show = true;
        if (typeof _taxonomies['product_visibility'] != 'undefined') {
            if (jQuery.inArray("exclude-from-catalog", _taxonomies['product_visibility']) > -1 && !search) {
                show = false;
            }
            if (jQuery.inArray("exclude-from-search", _taxonomies['product_visibility']) > -1 && search) {
                show = false;
            }
        }
        return show;
    }
    /* In array for alasql */
    alasql.fn.check_id = function (id, ids) {/*init function*/
        var show = true;
        if (jQuery.inArray(id, ids) > -1) {
            show = false;
        }
        show = false;
        return show;
    }

    this. search = function (query,recount) {

        return this.do_query(this.get_query(query, this.possible_terms,false,recount));
    }
    /* recount */
    this.add_query_recount = function (query, key, value) {
        if (query[key] != undefined) {
            return query[key] + "," + value;
        } else {
            return query[key] = value;
        }
    }

    this.dynamic_recount_special = function (query) { /* not used*/

        var filters = jQuery(".woof .woof_redraw_zone");

        var _this_obj = this;
        jQuery.each(filters, function (index, filter) {
            var items = jQuery(filter).find(".woof_container");
            var count = 0;
            jQuery.each(items, function (i, item) {
                /*radio and  checkbox*/
                if (jQuery(item).hasClass("woof_container_radio") || jQuery(item).hasClass("woof_container_checkbox") || jQuery(item).hasClass("woof_container_select_radio_check")) {
                    count = 0;
                    jQuery.each(jQuery(item).find("input[type='radio']"), function (i, input) {
                        var term = jQuery(input).data('slug');
                        var tax = jQuery(input).attr('name');

                        var count_query_r = {};
                        if (typeof woof_current_values == 'object')
                        {
                            count_query_r = Object.assign({}, woof_current_values);
                        } else {
                            count_query_r = Object.assign({}, JSON.parse(woof_current_values));
                        }

                        if (woof_current_values[tax] != undefined && woof_current_values[tax] == term) {
                            return true;
                        }
                        count_query_r[tax] = term;
                        var res = _this_obj.search(count_query_r);

                        var parent_li = jQuery(input).closest("li");
                        var item_label = jQuery(parent_li).find(".woof_radio_label")[0];

                        if (res.length <= 0) {
                            jQuery(item_label).parent().addClass("woof_turbo_hide");
                        } else {
                            count++;
                            jQuery(item_label).parent().removeClass("woof_turbo_hide");
                        }
                        jQuery(item_label).find(".woof_turbo_count").remove();
                        jQuery(item_label).append("<span class='woof_turbo_count'>(" + res.length + ")</span>");


                    });

                    jQuery.each(jQuery(item).find("input[type='checkbox']"), function (i, input) {
                        var tax = jQuery(input).data('tax');
                        var term = jQuery(input).attr('name');
                        if (woof_current_values[tax] != undefined) {
                            var arr_terms = woof_current_values[tax].split(',');
                            if (jQuery.inArray(term, arr_terms) != -1) {
                                return true;
                            }
                        }


                        var count_query = {};
                        if (typeof woof_current_values == 'object')
                        {
                            count_query = Object.assign({}, woof_current_values);
                        } else {
                            count_query = Object.assign({}, JSON.parse(woof_current_values));
                        }

                        var logic = "OR";
                        if (_this_obj.filter_settings.comparison_logic[tax] != undefined) {
                            logic = _this_obj.filter_settings.comparison_logic[tax];
                        }

                        if (woof_current_values[tax] != undefined && logic == "AND") {
                            count_query[tax] = count_query[tax] + "," + term;

                        } else {

                            count_query[tax] = term;

                        }

                        var res = _this_obj.search(count_query);

                        var parent_li = jQuery(input).closest("li");
                        var item_label = jQuery(parent_li).find(".woof_checkbox_label")[0];

                        if (res.length <= 0) {
                            jQuery(item_label).parent().addClass("woof_turbo_hide");
                        } else {
                            count++;
                            jQuery(item_label).parent().removeClass("woof_turbo_hide");
                        }
                        jQuery(item_label).find(".woof_turbo_count").remove();
                        jQuery(item_label).append("<span class='woof_turbo_count'>(" + res.length + ")</span>");

                    });
                    if (count == 0) {
                        jQuery(item).hide();
                    } else {
                        jQuery(item).show();
                    }
                }

                /*meta checkbox*/
                if (jQuery(item).hasClass("woof_meta_checkbox_container")) {
                    count = 0;
                    jQuery.each(jQuery(item).find("input[type='checkbox']"), function (i, input) {
                        var val = jQuery(input).val();
                        var meta = jQuery(input).attr('name');

                        var count_query = {};
                        if (typeof woof_current_values == 'object')
                        {
                            count_query = Object.assign({}, woof_current_values);
                        } else {
                            count_query = Object.assign({}, JSON.parse(woof_current_values));
                        }

                        if (woof_current_values[meta] != undefined && woof_current_values[meta] == val) {
                            return true;
                        }
                        var logic = "OR";
                        if (_this_obj.filter_settings.comparison_logic[meta] != undefined) {
                            logic = _this_obj.filter_settings.comparison_logic[meta];
                        }

                        if (woof_current_values[meta] != undefined && logic == "AND") {
                            count_query[meta] = count_query[meta] + "," + val;

                        } else {

                            count_query[meta] = val;

                        }
                        var res = _this_obj.search(count_query);

                        var item_label = jQuery(input).parents(".woof_container_inner").find("label");
                        if (res.length <= 0) {
                            jQuery(item_label).parent().addClass("woof_turbo_hide");
                        } else {
                            count++;
                            jQuery(item_label).parent().removeClass("woof_turbo_hide");
                        }
                        jQuery(item_label).find(".woof_turbo_count").remove();
                        jQuery(item_label).append("<span class='woof_turbo_count'>(" + res.length + ")</span>");


                    });
                    if (count == 0) {
                        jQuery(item).hide();
                    } else {
                        jQuery(item).show();
                    }
                }

                /*drop down*/
                if (jQuery(item).hasClass("woof_container_select")
                        || jQuery(item).hasClass("woof_container_mselect")
                        || jQuery(item).hasClass("woof_meta_select_container")
                        || jQuery(item).hasClass("woof_meta_mselect_container")
                        || jQuery(item).hasClass("woof_author_search_container")
                        || jQuery(item).hasClass("woof_by_rating_container")
                        )
                {
                    count = 0
                    var choosen = jQuery(item).parent().find(".chosen-container")
                    var tax = jQuery(item).find("select").attr('name');

                    jQuery.each(jQuery(item).find("select option"), function (i, option) {
                        if (i != 0 || jQuery(item).hasClass("woof_meta_mselect_container")) {
                            var term = jQuery(option).val();
                            if (woof_current_values[tax] != undefined) {
                                var arr_terms = woof_current_values[tax].split(',');
                                if (jQuery.inArray(term, arr_terms) != -1) {
                                    return true;
                                }
                            }

                            var count_query = {};
                            if (typeof woof_current_values == 'object')
                            {
                                count_query = Object.assign({}, woof_current_values);
                            } else {
                                count_query = Object.assign({}, JSON.parse(woof_current_values));
                            }

                            var logic = "OR";
                            if (_this_obj.filter_settings.comparison_logic[tax] != undefined) {
                                logic = _this_obj.filter_settings.comparison_logic[tax];
                            }

                            if (count_query[tax] != undefined && logic == "AND") {
                                count_query[tax] = count_query[tax] + "," + term;
                            } else {
                                count_query[tax] = term;
                            }

                            //var res = _this_obj.search(count_query);
                            var res = [1, 2, 3]

                            var count_prev = jQuery(option).data('count');
                            jQuery(option).attr('data-count', res.length);
                            var txt = jQuery(option).text();
                            txt = txt.replace(/\(.*?\)/g, "");
                            txt = txt.replace(/\s*$/, '');

                            jQuery(option).text(txt + " (" + res.length + ")");
                            if (res.length <= 0) {
                                jQuery(option).addClass("woof_turbo_hide");
                            } else {
                                count++;
                                jQuery(option).removeClass("woof_turbo_hide");
                            }

                        }
                    });
                    if (count == 0) {
                        jQuery(item).hide();
                    } else {
                        jQuery(item).show();
                    }
                }
                /*slider*/
                if (jQuery(item).hasClass("woof_container_slider")) {
                    var item_slider = jQuery(item).find("input.woof_taxrange_slider");
                    var tax = jQuery(item_slider).data('tax');
                    if (woof_current_values[tax] == undefined) {
                        var terms = jQuery(item_slider).data('values').split(",");
                        count = 0;
                        jQuery.each(terms, function (i, item) {
                            var count_query = {};
                            if (typeof woof_current_values == 'object')
                            {
                                count_query = Object.assign({}, woof_current_values);
                            } else {
                                count_query = Object.assign({}, JSON.parse(woof_current_values));
                            }

                            count_query[tax] = item;
                            var res = _this_obj.search(count_query);
                            if (res.length != 0) {
                                count++;
                            }

                        });
                        if (count == 0) {
                            jQuery(item).addClass("woof_turbo_hide");
                        } else {
                            jQuery(item).removeClass("woof_turbo_hide");
                        }
                    } else {
                        jQuery(item).removeClass("woof_turbo_hide");
                    }

                }
                /*meta slider*/
                if (jQuery(item).hasClass("woof_meta_slider_container")) {
                    var item_slider = jQuery(item).find("input.woof_metarange_slider");
                    var meta = jQuery(item_slider).attr('name');
                    if (woof_current_values[meta] == undefined) {

                        var from = jQuery(item_slider).data('min');
                        var to = jQuery(item_slider).data('max');
                        var count_query = {};
                        if (typeof woof_current_values == 'object')
                        {
                            count_query = Object.assign({}, woof_current_values);
                        } else {
                            var count_query = Object.assign({}, JSON.parse(woof_current_values));
                        }

                        count_query[meta] = from + "-" + to;

                        var res = _this_obj.search(count_query);

                        if (res.length == 0) {
                            jQuery(item).addClass("woof_turbo_hide");
                        } else {
                            jQuery(item).removeClass("woof_turbo_hide");
                        }

                    } else {
                        jQuery(item).removeClass("woof_turbo_hide");
                    }

                }
                /*color and  image*/
                if (jQuery(item).hasClass("woof_container_color") || jQuery(item).hasClass("woof_container_image")) {
                    var terms = jQuery(item).find("input.woof_color_term");
                    if (!terms.length) {
                        terms = jQuery(item).find("input.woof_image_term");
                    }
                    count = 0;
                    jQuery.each(terms, function (i, input) {
                        var tax = jQuery(input).data('tax');
                        var val = jQuery(input).attr('name');

                        var count_query = {};
                        if (typeof woof_current_values == 'object')
                        {
                            count_query = Object.assign({}, woof_current_values);
                        } else {
                            count_query = Object.assign({}, JSON.parse(woof_current_values));
                        }

                        var logic = "OR";
                        if (_this_obj.filter_settings.comparison_logic[tax] != undefined) {
                            logic = _this_obj.filter_settings.comparison_logic[tax];
                        }

                        if (count_query[tax] != undefined && logic == "AND") {
                            count_query[tax] = count_query[tax] + "," + val;
                        } else {
                            count_query[tax] = val;
                        }

                        var res = _this_obj.search(count_query);

                        var item_label = jQuery(input).parents("li").find(".woof_tooltip_data");

                        if (res.length <= 0) {
                            jQuery(input).parents("li").addClass("woof_turbo_hide");
                        } else {
                            count++;
                            jQuery(input).parents("li").removeClass("woof_turbo_hide");
                        }
                        jQuery(item_label).find(".woof_turbo_count").remove();
                        jQuery(item_label).append("<span class='woof_turbo_count'>(" + res.length + ")</span>");

                    });
                    if (count == 0) {
                        jQuery(item).hide();
                    } else {
                        jQuery(item).show();
                    }
                }
                /*label*/
                if (jQuery(item).hasClass("woof_container_label")) {
                    var terms = jQuery(item).find("input.woof_label_term");
                    count = 0;
                    jQuery.each(terms, function (i, input) {
                        var tax = jQuery(input).data('tax');
                        var val = jQuery(input).attr('name');
                        var count_query = {};
                        if (typeof woof_current_values == 'object')
                        {
                            count_query = Object.assign({}, woof_current_values);
                        } else {
                            count_query = Object.assign({}, JSON.parse(woof_current_values));
                        }

                        var logic = "OR";
                        if (_this_obj.filter_settings.comparison_logic[tax] != undefined) {
                            logic = _this_obj.filter_settings.comparison_logic[tax];
                        }

                        if (count_query[tax] != undefined && logic == "AND") {
                            count_query[tax] = count_query[tax] + "," + val;
                        } else {
                            count_query[tax] = val;
                        }

                        var res = _this_obj.search(count_query);
                        var li_item = jQuery(input).parents("li");
                        if (res.length <= 0) {
                            jQuery(li_item).addClass("woof_turbo_hide");
                        } else {
                            count++;
                            jQuery(li_item).removeClass("woof_turbo_hide");
                        }
                        jQuery(li_item).find(".woof_label_count").remove();
                        jQuery(li_item).prepend("<span class='woof_label_count'>" + res.length + "</span>");

                    });
                    if (count == 0) {
                        jQuery(item).hide();
                    } else {
                        jQuery(item).show();
                    }
                }

            });
        });
	
	woof_reinit_selects();	


        if (Object.keys(woof_current_values).length == 0) {
            jQuery(".woof_reset_search_form").hide();
        } else {
            jQuery(".woof_reset_search_form").show();
        }
        return;
    }
    this.dynamic_recount = function (query) {

        var filters = jQuery(".woof .woof_redraw_zone");
        var filters_data = {};
        var _this_obj = this;
        jQuery.each(filters, function (index, filter) {
            var items = jQuery(filter).find(".woof_container");
            var count = 0;
            filters_data[index] = {};
            jQuery.each(items, function (index_f, item) {
                filters_data[index][index_f] = {};
                /*radio and  checkbox*/
                if (jQuery(item).hasClass("woof_container_radio") || jQuery(item).hasClass("woof_container_checkbox") || jQuery(item).hasClass("woof_container_select_radio_check")) {

                    jQuery.each(jQuery(item).find("input[type='radio']"), function (i, input) {
                        var term = jQuery(input).data('slug');
                        var tax = jQuery(input).attr('name');
                        var current = false;
                        var count_query_r = {};
                        if (typeof query == 'object')
                        {
                            count_query_r = Object.assign({}, query);
                        } else {
                            count_query_r = Object.assign({}, JSON.parse(query));
                        }

                        if (query[tax] != undefined && query[tax] == term) {
                            current = true;
                        }
                        count_query_r[tax] = term;

                        var parent_li = jQuery(input).closest("li");
                        var item_label = jQuery(parent_li).find(".woof_radio_label")[0];
                        filters_data[index][index_f][i] = {};
                        filters_data[index][index_f][i] = {
                            key: tax,
                            val: term,
                            type: 'radio',
                            query: Object.assign({}, count_query_r),
                            label: item_label,
                            current: current,
                            count: 0
                        };

                    });
                    jQuery.each(jQuery(item).find("input[type='checkbox']"), function (i, input) {
                        var tax = jQuery(input).data('tax');
                        var term = jQuery(input).attr('name');
                        var current = false;
                        if (query[tax] != undefined) {
                            var arr_terms = query[tax].split(',');
                            if (jQuery.inArray(term, arr_terms) != -1) {
                                return true;
                            }
                        }

                        var count_query = {};
                        if (typeof query == 'object')
                        {
                            count_query = Object.assign({}, query);
                        } else {
                            count_query = Object.assign({}, JSON.parse(query));
                        }

                        var logic = "OR";
                        if (_this_obj.filter_settings.comparison_logic[tax] != undefined) {
                            logic = _this_obj.filter_settings.comparison_logic[tax];
                        }

                        if (query[tax] != undefined) {
                            var match = jQuery.inArray(term, count_query[tax].split(","));
                            if (match != -1) {
                                current = true;
                            }
                        }

                        if (query[tax] != undefined && logic == "AND") {
                            count_query[tax] = count_query[tax] + "," + term;
                        } else {
                            count_query[tax] = term;
                        }
                        var parent_li = jQuery(input).closest("li");
                        var item_label = jQuery(parent_li).find(".woof_checkbox_label")[0];
                        filters_data[index][index_f][i] = {};
                        filters_data[index][index_f][i] = {
                            key: tax,
                            val: term,
                            type: 'checkbox',
                            query: Object.assign({}, count_query),
                            label: item_label,
                            current: current,
                            count: 0
                        };
                    });
                }

                /*meta checkbox*/
                if (jQuery(item).hasClass("woof_meta_checkbox_container")) {
                    jQuery.each(jQuery(item).find("input[type='checkbox']"), function (i, input) {
                        var val = jQuery(input).val();
                        var meta = jQuery(input).attr('name');
                        var current = false;
                        var count_query = {};
                        if (typeof query == 'object')
                        {
                            count_query = Object.assign({}, query);
                        } else {
                            count_query = Object.assign({}, JSON.parse(query));
                        }

                        if (query[meta] != undefined && query[meta] == val) {
                            current = true;
                        }
                        var logic = "OR";
                        if (_this_obj.filter_settings.comparison_logic[meta] != undefined) {
                            logic = _this_obj.filter_settings.comparison_logic[meta];
                        }

                        if (query[meta] != undefined && logic == "AND") {
                            count_query[meta] = count_query[meta] + "," + val;

                        } else {
                            count_query[meta] = val;
                        }

                        var item_label = jQuery(input).parents(".woof_container_inner").find("label");
                        filters_data[index][index_f][i] = {};
                        filters_data[index][index_f][i] = {
                            key: meta,
                            val: val,
                            type: 'meta_checkbox',
                            query: Object.assign({}, count_query),
                            label: item_label,
                            current: current,
                            count: 0
                        };
                    });
                }
                /* meta datepicker */
                if (jQuery(item).hasClass("woof_meta_datepicker_container")) {
                    var val = 1;
                    var meta = jQuery(item).find('input.woof_calendar_from').data('meta-key');

                    var current = false;
                    var count_query = {};
                    if (typeof query == 'object')
                    {
                        count_query = Object.assign({}, query);
                    } else {
                        count_query = Object.assign({}, JSON.parse(query));
                    }

                    if (query["datepicker_" + meta] != undefined) {
                        current = true;
                    }

                    if (query[meta] == undefined) {
                        count_query["datepicker_" + meta] = "0-" + Number.MAX_VALUE;
                    }

                    filters_data[index][index_f][0] = {};
                    filters_data[index][index_f][0] = {
                        key: "datepicker_" + meta,
                        val: val,
                        type: 'meta_datepicker',
                        query: Object.assign({}, count_query),
                        label: item,
                        current: current,
                        count: 0
                    };

                }
                /*drop down*/
                if (jQuery(item).hasClass("woof_container_select")
                        || jQuery(item).hasClass("woof_container_mselect")
                        || jQuery(item).hasClass("woof_meta_select_container")
                        || jQuery(item).hasClass("woof_meta_mselect_container")
                        || jQuery(item).hasClass("woof_author_search_container")
                        || jQuery(item).hasClass("woof_by_rating_container")
                        )
                {
                    var choosen = jQuery(item).parent().find(".chosen-container")
                    var tax = jQuery(item).find("select").attr('name');
                    jQuery.each(jQuery(item).find("select option"), function (i, option) {
                        var current = false;
                        if (i != 0 || jQuery(item).hasClass("woof_meta_mselect_container")) {
                            var term = jQuery(option).val();
                            if (query[tax] != undefined) {
                                var arr_terms = query[tax].split(',');
                                if (jQuery.inArray(term, arr_terms) != -1) {
                                    //return true;
                                }
                            }

                            var count_query = {};
                            if (typeof query == 'object')
                            {
                                count_query = Object.assign({}, query);
                            } else {
                                count_query = Object.assign({}, JSON.parse(query));
                            }

                            var logic = "OR";
                            if (_this_obj.filter_settings.comparison_logic[tax] != undefined) {
                                logic = _this_obj.filter_settings.comparison_logic[tax];
                            }
                            if (count_query[tax] != undefined) {

                                var match = jQuery.inArray(term, count_query[tax].split(","));
                                if (match != -1) {
                                    current = true;
                                }
                            }
                            if (count_query[tax] != undefined && logic == "AND") {
                                count_query[tax] = count_query[tax] + "," + term;
                            } else {
                                count_query[tax] = term;
                            }
                            filters_data[index][index_f][i] = {};
                            filters_data[index][index_f][i] = {
                                key: tax,
                                val: term,
                                type: 'drop_down',
                                query: Object.assign({}, count_query),
                                label: option,
                                current: current,
                                count: 0
                            };
                        }
                    });
                }
                /*slider*/
                if (jQuery(item).hasClass("woof_container_slider")) {
                    var item_slider = jQuery(item).find("input.woof_taxrange_slider");
                    var tax = jQuery(item_slider).data('tax');
                    var current = false;
                    if (query[tax] != undefined) {
                        current = true;
                    }
                    var terms = jQuery(item_slider).data('values');
                    var count_query = {};
                    if (typeof query == 'object')
                    {
                        count_query = Object.assign({}, query);
                    } else {
                        count_query = Object.assign({}, JSON.parse(query));
                    }
                    count_query[tax] = terms;
                    filters_data[index][index_f][0] = {};
                    filters_data[index][index_f][0] = {
                        key: tax,
                        val: terms,
                        type: 'slider',
                        query: Object.assign({}, count_query),
                        label: item_slider,
                        current: current,
                        count: 0
                    };


                }
                /*meta slider*/
                if (jQuery(item).hasClass("woof_meta_slider_container")) {
                    var item_slider = jQuery(item).find("input.woof_metarange_slider");
                    var meta = jQuery(item_slider).attr('name');
                    var current = false;
                    if (query[meta] != undefined) {
                        current = true;
                    }

                    var from = jQuery(item_slider).data('min');
                    var to = jQuery(item_slider).data('max');
                    var count_query = {};
                    if (typeof query == 'object')
                    {
                        count_query = Object.assign({}, query);
                    } else {
                        var count_query = Object.assign({}, JSON.parse(query));
                    }
                    count_query[meta] = from + "^" + to;
 
                    filters_data[index][index_f][0] = {};
                    filters_data[index][index_f][0] = {
                        key: meta,
                        val: from + "-" + to,
                        type: 'meta_slider',
                        query: Object.assign({}, count_query),
                        label: item_slider,
                        current: current,
                        count: 0
                    };


                }
                /*color and  image*/
                if (jQuery(item).hasClass("woof_container_color") || jQuery(item).hasClass("woof_container_image")) {
                    var terms = jQuery(item).find("input.woof_color_term");

                    if (!terms.length) {
                        terms = jQuery(item).find("input.woof_image_term");
                    }
                    count = 0;
                    jQuery.each(terms, function (i, input) {
                        var tax = jQuery(input).data('tax');
                        var val = jQuery(input).attr('name');
                        var current = false;
                        var count_query = {};
                        if (typeof query == 'object')
                        {
                            count_query = Object.assign({}, query);
                        } else {
                            count_query = Object.assign({}, JSON.parse(query));
                        }

                        var logic = "OR";
                        if (_this_obj.filter_settings.comparison_logic[tax] != undefined) {
                            logic = _this_obj.filter_settings.comparison_logic[tax];
                        }
                        if (query[tax] != undefined) {
                            var match = jQuery.inArray(val, count_query[tax].split(","));
                            if (match != -1) {
                                current = true;
                            }
                        }
                        if (count_query[tax] != undefined && logic == "AND") {
                            count_query[tax] = count_query[tax] + "," + val;
                        } else {
                            count_query[tax] = val;
                        }

                        filters_data[index][index_f][i] = {};
                        filters_data[index][index_f][i] = {
                            key: tax,
                            val: val,
                            type: 'color_image',
                            query: Object.assign({}, count_query),
                            label: input,
                            current: current,
                            count: 0
                        };
                    });
                }
                /*label*/
                if (jQuery(item).hasClass("woof_container_label")) {
                    var terms = jQuery(item).find("input.woof_label_term");
                    jQuery.each(terms, function (i, input) {
                        var current = false;
                        var tax = jQuery(input).data('tax');
                        var val = jQuery(input).attr('name');
                        var count_query = {};
                        if (typeof query == 'object')
                        {
                            count_query = Object.assign({}, query);
                        } else {
                            count_query = Object.assign({}, JSON.parse(query));
                        }

                        var logic = "OR";
                        if (_this_obj.filter_settings.comparison_logic[tax] != undefined) {
                            logic = _this_obj.filter_settings.comparison_logic[tax];
                        }

                        if (query[tax] != undefined) {
                            var match = jQuery.inArray(val, count_query[tax].split(","));
                            if (match != -1) {
                                current = true;
                            }
                        }

                        if (count_query[tax] != undefined && logic == "AND") {
                            count_query[tax] = count_query[tax] + "," + val;
                        } else {
                            count_query[tax] = val;
                        }
                        var li_item = jQuery(input).parents("li");

                        filters_data[index][index_f][i] = {};
                        filters_data[index][index_f][i] = {
                            key: tax,
                            val: val,
                            type: 'label',
                            query: Object.assign({}, count_query),
                            label: li_item,
                            current: current,
                            count: 0
                        };
                    });
                }

            });
        });
        return filters_data;
    }
    /* end recount*/

    /* compatybility*/
    this.check_messenger_btn = function (_this) {

        if (typeof _this.filter_settings.products_messenger != "undefined" && _this.filter_settings.products_messenger.show_btn_subscr == "0") {

            if (Object.keys(woof_current_values).length != 0 && (typeof woof_current_values.swoof != "udefined" || typeof woof_current_values[_this.filter_settings.swoof_search_slug] != "udefined")) {

                jQuery("#woof_add_subscr").show();
            } else {
                jQuery("#woof_add_subscr").hide();
            }
        }

    }
    this.check_save_query_btn = function (_this) {

        if (typeof _this.filter_settings.products_messenger != "undefined") {

            if (Object.keys(woof_current_values).length != 0 && (typeof woof_current_values.swoof != "udefined" || typeof woof_current_values[_this.filter_settings.swoof_search_slug] != "udefined")) {

                jQuery(".woof_add_query_count").show();
            } else {
                jQuery(".woof_add_query_count").hide();
            }
        }

    }
    this.check_show_more_less = function (_this, tax) {

        if (typeof _this.filter_settings.not_toggled_terms_count[tax] != "undefined" && parseInt(_this.filter_settings.not_toggled_terms_count[tax]) > 0) {
            var count = parseInt(_this.filter_settings.not_toggled_terms_count[tax]);
            var items = jQuery(".woof_container_" + tax + " .woof_list").children("li")
            var state = jQuery(".woof_container_" + tax).find(".woof_open_hidden_li_btn").data('state');
            jQuery(items).removeClass('woof_hidden_term').removeClass('woof_hidden_term2')
            jQuery.each(items, function (i, item) {
                if (!jQuery(item).hasClass("woof_turbo_hide")) {
                    count--;
                }
                if (count < 0) {
                    if (state == "closed") {
                        jQuery(item).addClass('woof_hidden_term');
                    } else {
                        jQuery(item).addClass('woof_hidden_term2');
                    }
                }
            });
            if (count >= -1) {
                jQuery(".woof_container_" + tax).find(".woof_open_hidden_li_btn").hide();
            } else {
                jQuery(".woof_container_" + tax).find(".woof_open_hidden_li_btn").show();
            }

        }
    }
    /* end  compatybility*/

    this.woof_submit_link = function (link) {

        if (!woof_turbo_mode_file.length) { /*If the file did not have time to load*/
            this.uploadFile();
            setTimeout("this.woof_submit_link('" + link + "')", 2000);
            return;
        }
        woof_submit_link_locked = true;

        if (!woof_ajax_redraw) {

            var res_array = this.search(woof_current_values,false);
            var res = res_array.join(",");
            if (res.length < 1) {
                res = -1;
            }

            var shortcode = jQuery('#woof_results_by_ajax').data('shortcode');
            if (typeof shortcode == "undefined") {
                window.location = link;
                return false;
            }


            /****/
            woof_show_info_popup(woof_lang_loading);
            woof_ajax_first_done = true;
            var data = {
                action: "woof_draw_products",
                link: link,
                turbo_mode_ids: res,
                page: woof_ajax_page_num,
                shortcode: shortcode,
                woof_shortcode: jQuery('div.woof').data('shortcode')
            };

            jQuery.post(woof_ajaxurl, data, function (content) {

                content = JSON.parse(content);

                if (jQuery('.woof_results_by_ajax_shortcode').length) {
                    jQuery('#woof_results_by_ajax').replaceWith(content.products);
                } else {
                    jQuery('.woof_shortcode_output').replaceWith(content.products);
                }
                woof_hide_info_popup();
                if (woof_reset_btn_action) {
                    jQuery('div.woof_redraw_zone').replaceWith(jQuery(content.form).find('.woof_redraw_zone'));
                    woof_mass_reinit();
                }

                woof_draw_products_top_panel();

                woof_submit_link_locked = false;
                /*removing id woof_results_by_ajax - multi in ajax mode sometimes*/
                /*when uses shorcode woof_products in ajax and in settings try ajaxify shop is Yes*/
                jQuery.each(jQuery('#woof_results_by_ajax'), function (index, item) {
                    if (index == 0) {
                        return;
                    }
                    jQuery(item).removeAttr('id');
                });
                
                /* compatibility found products count*/
                var found_count = jQuery('.woof_found_count');
                jQuery(found_count).show();
                if (found_count.length > 0) {
                    var count_prod=jQuery("#woof_results_by_ajax").data('count');
                    if(typeof count_prod!="undefined"){
                        jQuery(found_count).text(count_prod);
                    }
                    
                }  
                
                
                //infinite scroll
                woof_infinite();
                //*** script after ajax loading here
                woof_js_after_ajax_done();
                //***  change  link  in button "add to cart"
                woof_change_link_addtocart();
                /*tooltip*/
                woof_init_tooltip();


                //messenger    extension
                WoofTurboMode.check_messenger_btn(WoofTurboMode);

                //save query  extension
                WoofTurboMode.check_save_query_btn(WoofTurboMode);
                /*dynamic recount*/
                if ((WoofTurboMode.show_count && WoofTurboMode.dynamic_recount_val) || (woof_reset_btn_action && WoofTurboMode.show_count)) {
                    
                    
                    var filters = WoofTurboMode.dynamic_recount(woof_current_values);
                   
                    jQuery.each(filters, function (i, filter) {
                        jQuery(".woof_turbo_mode_overlay").show();
                        var filter_count = 0;
                        jQuery.each(filter, function (ind, items) {
                            var count = 0;
                            var last = false;
                            if (Object.keys(items).length) {
                                filter_count = ind;
                            }
                           
                            jQuery.each(items, function (indx, item) {
                                /* split streams */
                                setTimeout(function () {
                                    last = false;
                                    var res = {};
                                    
                                    if (!item.current) {
                                        var recount=false;
                                        if(typeof WoofTurboMode.possible_terms.taxonomies[item.key.replace('rev_','')] !='undefined' && WoofTurboMode.possible_terms.taxonomies[item.key.replace('rev_','')]=="NOT IN"){                                            
                                            recount=true;
                                        }                                        
                                        res = WoofTurboMode.search(item.query,recount);  
                                        //array unique
                                        res = res.filter((v, i, a) => a.indexOf(v) === i);
                                    } else {
                                        count++;
                                    }
                                    filters[i][ind][indx].count = res.length;
                                    if (res.length > 0) {
                                        count++;
                                    }
                                    if (typeof filters[i][ind][+indx + 1] == 'undefined') {
                                        last = true;
                                    }
                                    WoofTurboMode.draw_count_item(filters[i][ind][indx], count, last, WoofTurboMode);
                                    if (last && filter_count == ind) {
                                        jQuery(".woof_turbo_mode_overlay").hide();
                                    }
                                    last = false;
                                }, 1);
                            });
                        });
                        //stat collection
                        if (woof_current_values.hasOwnProperty(swoof_search_slug)) {
                            var data = {
                                action: "woof_write_stat",
                                woof_current_values: woof_current_values
                            };
                            jQuery.post(woof_ajaxurl, data, function () {
                                //***
                            });
                        }

                    });
                } else {
                    jQuery(".woof_turbo_mode_overlay").hide();
                }
                woof_reset_btn_action = false;
            });

        } else {

            if (woof_ajax_redraw) {
                /*dynamic recount*/
                if ((WoofTurboMode.show_count && WoofTurboMode.dynamic_recount_val) || (woof_reset_btn_action && WoofTurboMode.show_count)) {
                    var filters = WoofTurboMode.dynamic_recount(woof_current_values);
                    jQuery.each(filters, function (i, filter) {
                        jQuery(".woof_turbo_mode_overlay").show();
                        var filter_count = 0;
                        jQuery.each(filter, function (ind, items) {
                            var count = 0;
                            var last = false;
                            if (Object.keys(items).length) {
                                filter_count = ind;
                            }
                            jQuery.each(items, function (indx, item) {
                                /* split streams */
                                setTimeout(function () {
                                    last = false;
                                    var res = {};
                                    if (!item.current) {
                                        var recount=false;
                                        if(typeof WoofTurboMode.possible_terms.taxonomies[item.key.replace('rev_','')] !='undefined' && WoofTurboMode.possible_terms.taxonomies[item.key.replace('rev_','')]=="NOT IN"){                                            
                                            recount=true;
                                        }                                        
                                        res = WoofTurboMode.search(item.query,recount);
                                        
                                        //array unique
                                        res = res.filter((v, i, a) => a.indexOf(v) === i);
                                    } else {
                                        count++;
                                    }
                                    filters[i][ind][indx].count = res.length;
                                    if (res.length > 0) {
                                        count++;
                                    }
                                    if (typeof filters[i][ind][+indx + 1] == 'undefined') {
                                        last = true;
                                    }
                                    WoofTurboMode.draw_count_item(filters[i][ind][indx], count, last, WoofTurboMode);
                                    if (last && filter_count == ind) {
                                        jQuery(".woof_turbo_mode_overlay").hide();
                                        if (typeof woof_step_filter_html_items == 'function') {
                                            woof_step_filter_html_items();
                                        }
                                    }
                                    last = false;
                                }, 1);
                            });
                        });


                        //stat collection
                        if (woof_current_values.hasOwnProperty(swoof_search_slug)) {
                            var data = {
                                action: "woof_write_stat",
                                woof_current_values: woof_current_values
                            };
                            jQuery.post(woof_ajaxurl, data, function () {
                                //***
                            });
                        }

                    });
                }
            }

        }

    };

    this.init();
};

/* INIT */
var WoofTurboMode = new WoofTurboMode_obj(woof_tm_data);

function woof_turbo_mode_sleep(sleepDuration) {
    var now = new Date().getTime();
    while (new Date().getTime() < now + sleepDuration) { /* do nothing */
    }
}
