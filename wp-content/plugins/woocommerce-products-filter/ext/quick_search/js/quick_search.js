"use strict";

var data_text = [];
var woof_qt_current_values = [];
woof_qt_current_values.add_filter = {};
woof_qt_current_values.meta_filter = {};
var woof_qt_curr_page = 0;
var woof_qt_result_wraper = {};
var woof_qt_per_page = 12;
var woof_qt_template_code = "";
var woof_qt_show_products = 1;
var woof_qt_current_sort = "title-asc";
var woof_qt_load_count = 0;
var woof_qt_target = "_blank";
var woof_qt_tax_logic = "AND";
var woof_qt_group_text_logic = "AND";
var woof_qt_term_logic = {};
var woof_qt_tpl_cunstruct_selector = ".woof_qs_templates";
var woof_qt_tpl_container_selector = ".woof_qs_container";
var woof_qt_tpl_item_selector = ".woof_qs_item";
var woof_qt_tpl_no_product_selector = ".woof_qs_no_products_item";
var woof_qt_tpl_cunstruct = "";
var woof_qt_tpl_item = "";
var woof_qt_tpl_no_product = "";

/*
 * 
 * Init search  function
 * 
 */

/* default  text search ( it uses easyAutocomplete)*/
function init_text_filter_form(url, data) {

    var options = {
        data: data,
        //url:url,
        getValue: function (element) {
            return element.title + ": " + element.key_words + element.sku;
        },
        template: {
            type: "custom",
            method: function (value, item) {
                var template = woof_get_text_template(1);
                var key_words = woof_cut_words(item.key_words, 100);
                var title = woof_cut_words(item.title, 100);
                if (item.img == null || item.img.length < 1) {
                    item.img = wooftextfilelink.no_image
                } else {
                    /*if you need full url*/
                    /*item.img = wooftextfilelink.site_url+item.img;*/
                }
                /*if you need full url*/
                /*item.url = wooftextfilelink.site_url+item.url;*/
                return  String.format(template, item.url, woof_qt_target, item.img, title, key_words);
            }
        },
        list: {
            maxNumberOfElements: 10,
            match: {
                enabled: true
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
            }
        },

    };
    jQuery("#woof_quick_search_form").easyAutocomplete(options);
}

/* Extended text search #2 ( it uses alasql)  */
function init_text_filter_content() {
    /*init vars*/
    woof_qt_result_wraper = jQuery('.woof_quick_search_results');
    if (woof_qt_result_wraper != undefined && woof_qt_result_wraper.length > 0) {
        var templates = woof_qt_get_template();
        if ("object" == typeof templates && templates.item != undefined && templates.container != undefined) {
            woof_qt_tpl_cunstruct = templates.container;
            woof_qt_tpl_item = templates.item;
            woof_qt_tpl_no_product = templates.no_product;
        } else {
            /*show notise*/
            jQuery('#woof_quick_search_form').after(woof_qt_get_notice('no_template'));
        }

        woof_qt_per_page = woof_qt_result_wraper.attr('data-per_page');
        woof_qt_template_code = woof_qt_result_wraper.attr('data-template');
        woof_qt_show_products = woof_qt_result_wraper.attr('data-show_products');
        woof_qt_current_sort = woof_qt_result_wraper.attr('data-orderby');

        /*init all events*/
        woof_init_text_search();
        woof_qt_init_ion_sliders();
        woof_qt_init_meta_ion_sliders();
        woof_qt_init_select();
        woof_qt_init_checkbox();
        woof_qt_init_radio();
        woof_qt_reset_init();
        woof_qt_show_add_filter();
    } else {
        /*show notise*/
        jQuery('#woof_quick_search_form').after(woof_qt_get_notice('no_shortcode'));
    }


}

function woof_qt_show_add_filter() {
    jQuery(".woof_qt_add_filter").show(300);
}

/*  text search #1 ( it uses custom func) Now it not used  */
function init_text_filter_content_old(data) {
    jQuery('#woof_quick_search_form').keyup(function () {
        var searchField = jQuery(this).val();
        var result_wraper = jQuery('.woof_quick_search_results');
        var per_page = result_wraper.attr('data-per_page');
        var template_code = result_wraper.attr('data-template');
        var template_structure = result_wraper.attr('data-template_structure');
        if (result_wraper.length < 1) {
            return;
        }
        if (searchField === '') {
            result_wraper.html('');
            return;
        }

        var regex = new RegExp(searchField, "i");
        var output = [];
        var output_page = "";
        var template_item = woof_get_template_result_item(template_code, template_structure);
        var count = 0;
        jQuery.each(data, function (key, val) {

            if ((val.key_words.search(regex) != -1) || (val.title.search(regex) != -1) || (val.sku.search(regex) != -1)) {
                var key_words = woof_cut_words(val.key_words, 100);
                var title = woof_cut_words(val.title, 100);
                var price = 0.00;
                price = woof_get_price_html(val.price);
                if (val.img == null || val.img.length < 1) {
                    val.img = wooftextfilelink.no_image
                }
                output_page += String.format(template_item, val.url, '_blank', val.img, title, key_words, price, val.sku);
                count++;
                if (count == per_page) {
                    count = 0;
                    output[output.length] = output_page;
                    output_page = "";
                }
            }
        });

        if (count != 0) {
            count = 0;
            output[output.length] = output_page;
            output_page = "";
        }
        var out_content = "";
        var pages = output.length;
        jQuery.each(output, function (key, val) {
            out_content += woof_get_template_result_container(val, template_code, pages, key, template_structure);
        });


        result_wraper.html(out_content);
        woof_init_qt_pagination();
    });
}

/*make search query*/
function woof_do_quick_search_search(type, page, per_page) {
    var data_result = [];
    var sql_query = "";
    var text_group_logic = "AND";
    var taxonomy_logic = "AND";
    var term_logic = "OR";
    if (woof_qt_tax_logic) {
        taxonomy_logic = woof_qt_tax_logic;
    }
    if (woof_qt_group_text_logic) {
        text_group_logic = woof_qt_group_text_logic;
    }
    /*Text search*/
    var searchField = woof_qt_current_values.text_search;
    if (searchField != "" && searchField != undefined && searchField) {
        searchField = searchField.replace(/\s{2,}|\.|\,|\:|\;|\"/g, ' ');
        searchField = searchField.replace(/\'/g, '&#8217;');
        //&#8217; //\u2019s

        var queries = [];
        var words = searchField.split(" ");

        jQuery.each(words, function (key, val) {
            queries[key] = String.format("( d.title LIKE '%{0}%'  OR  d.key_words LIKE '%{0}%'  OR d.sku LIKE '%{0}%' ) ", val)
        });
        sql_query = "(" + queries.join(text_group_logic) + ")";
    }
    /*slider*/
    if ((woof_qt_current_values.min_price && woof_qt_current_values.max_price) || (woof_qt_current_values.min_price === 0 && woof_qt_current_values.max_price)) {
        var sql_query_slider = String.format("( (get_min_price(d.price) BETWEEN {0} AND {1}) OR (get_max_price(d.price) BETWEEN {0} AND {1})) ", woof_qt_current_values.min_price, woof_qt_current_values.max_price)
        if (sql_query != "") {
            sql_query_slider = " AND " + sql_query_slider;
        }
        sql_query += sql_query_slider;
    }
    /*additional filters*/
    if (woof_qt_current_values.add_filter) {
        var sql_query_add_filter = [];
        jQuery.each(woof_qt_current_values.add_filter, function (key_tax, tax) {
            var sql_tax = [];
            jQuery.each(tax, function (key, term_id) {
                if (term_id || term_id > 0) {
                    sql_tax.push(" d.term_ids LIKE '% " + term_id + " %' ");

                }
            })
            if (sql_tax.length) {
                if (woof_qt_term_logic[key_tax] != undefined && woof_qt_term_logic[key_tax] == "AND") {
                    term_logic = woof_qt_term_logic[key_tax];
                } else {
                    term_logic = "OR";
                }
                sql_query_add_filter.push(" (" + sql_tax.join(term_logic) + ") ");
            }
        });

        if (sql_query != "" && sql_query_add_filter.length > 0) {
            sql_query += " AND ";
        }
        if (sql_query_add_filter.length) {
            sql_query += sql_query_add_filter.join(taxonomy_logic);
        }

    }
    /*meta filters*/
    if (woof_qt_current_values.meta_filter) {

        var meta_query = woof_quick_search_generate_meta_filter(woof_qt_current_values.meta_filter);
        if (meta_query) {
            if (sql_query) {
                sql_query += " AND (" + meta_query + ")";
            } else {
                sql_query += meta_query;
            }

        }

    }

    /*If  search is not going*/
    if (sql_query == "") {
        woof_qt_reset_btn_state(false);//hide reset btn

        if (woof_qt_show_products == 1) {
            sql_query = "1";
        } else {
            return data_result;
        }

    } else {

        woof_qt_reset_btn_state(true);//show reset btn
    }


    /*sort*/
    alasql.fn.get_max_price = function (_prices) { /*init function*/
        var price = woof_get_price_limits(_prices);
        return price['max'];
    }
    alasql.fn.get_min_price = function (_prices) {/*init function*/
        var price = woof_get_price_limits(_prices);
        return price['min'];
    }
    var sort_sql = " ";
    sort_sql = woof_get_orderby_sql();


    /*Pagination*/
    var pagination_sql = "";
    if (type == "search") {
        if (page == undefined || page < 0 || page == NaN) {
            page = 0;
        }
        if (per_page > 0 || per_page != 'undefined') {
            pagination_sql = "LIMIT " + per_page + " OFFSET " + page * per_page;

        } else {
            pagination_sql = "LIMIT 12 ";
        }
    }
    /*+++*/

    if (type == "pagination") {
        return alasql("SELECT COUNT(1) FROM ? AS d  WHERE " + sql_query + sort_sql, [data_text]);
    }
    data_result = alasql("SELECT * FROM ? AS d WHERE " + sql_query + sort_sql + pagination_sql, [data_text]);
    if (data_result != undefined && data_result.length == 0) {
        data_result[0] = "nan";
    }
    return data_result;
}

function woof_quick_search_generate_meta_filter(meta_data) {
    var meta_query = [];
    var tmp_query = [];
    var term_logic = "OR";

    jQuery.each(meta_data, function (index, value) {
        if (woof_qt_term_logic[index] != undefined && woof_qt_term_logic[index] == "AND") {
            term_logic = "AND";
        } else {
            term_logic = "OR";
        }
        switch (value['type']) {
            case'exact':
                jQuery.each(value['value'], function (i, val) {
                    if (val != -1) {
                        tmp_query.push(String.format(" d.meta_data->('{0}') ='{1}' ", index, val));
                    }
                });
                if (tmp_query.length) {
                    meta_query.push("(" + tmp_query.join(term_logic) + ")");
                }
                break;
            case'exist':
                if (value['value'].length > 0) {
                    meta_query.push(String.format("( d.meta_data->('{0}') <> 'undefined' )", index));
                }
                break;
            case'range':
                if (value['value'].length > 1) {
                    meta_query.push(String.format("( d.meta_data->('{0}') BETWEEN {1} AND {2} )", index, value['value'][0], value['value'][1]));
                }
                break;
            default:
        }

    });
    return meta_query.join(woof_qt_tax_logic);
}

function woof_quick_search_draw() {
    if (!data_text.length) {
        woof_qt_load_count++;/*if no date*/
        if (woof_qt_load_count < 10) {
            woof_load_serch_data();
        }
        return;
    }

    var result_wraper = woof_qt_result_wraper;
    var per_page = woof_qt_per_page;
    var page = woof_qt_curr_page;
    if (page == undefined || page < 0 || page == 'NaN') {
        page = 0;
    }
    if (result_wraper.length < 1) {
        return;
    }
    var output = [];
    var output_page = "";

    var template_item = woof_qt_tpl_item;
    var template_container = woof_qt_tpl_cunstruct;

    var count = 0;
    var data_result = [];
    data_result = woof_do_quick_search_search("search", page, per_page);
    if (woof_qt_show_products != 1 && (!data_result || data_result.length < 1)) {
        result_wraper.html("");
        return;
    }
    if (data_result[0] === "nan") {
        output_page = woof_qt_tpl_no_product;
        data_result = [];
    }
    jQuery.each(data_result, function (key, val) {
        val.key_words = woof_cut_words(val.key_words, 100);
        val.title = woof_cut_words(val.title, 100);
        val.price = woof_get_price_html(val.price);
        if (val.img == null || val.img.length < 1) {
            val.img = wooftextfilelink.no_image
        } else {
            /*if you need full url*/
            /*val.img = wooftextfilelink.site_url+val.img;*/
        }
        val.target = woof_qt_target;
        val.src = "src='" + val.img + "'"
        output_page += woof_qt_parse_temlate(template_item, val);

        count++;
        if (count > per_page) {
            //  break;
        }
    });
    var out_content = "";
    var prod_count = woof_do_quick_search_search("pagination")[0]['COUNT(1)'];

    var pages = Math.ceil(prod_count / per_page);
    template_container = woof_qt_parse_temlate(template_container, {'pagination': get_pagination_html(pages, page)});
    result_wraper.html(template_container);
    result_wraper.find(woof_qt_tpl_container_selector).append(output_page);
    woof_qt_init_script_after_redraw();/* for init pagination, sort and  template script */

}

/* after redraw */

function woof_qt_init_script_after_redraw() {
    woof_init_qt_pagination();
    woof_qt_init_sort();
    //init function for current template
    var name = "woof_qs_after_redraw_" + woof_qt_template_code;
    window[name]();
}

/*
 * 
 * Templates
 * 
 */

/*
 for  Text input ( default search)              
 */
function woof_get_text_template(type) {
    if (type == 1) {
        return  "<a href='{0}' target='{1}'><div class='woof_quick_search_img'><img  src='{2}' alt='' /></div><div class='woof_quick_search_desc' > <span class='woof_quick_search_desc_title'>{3}</span> <p class='woof_qt_key_words'>{4}</p> </div></a>";
    } else {
        return  "Temlate error"
    }

}

function  woof_qt_get_template() {
    var templates = {};

    if (!jQuery(woof_qt_tpl_item_selector).length) {
        return  templates;
    }
    var item_tpl = jQuery(woof_qt_tpl_item_selector).wrap('<p/>').parent().html();
    jQuery(woof_qt_tpl_item_selector).unwrap();
    var no_product_tpl = jQuery(woof_qt_tpl_no_product_selector).wrap('<p/>').parent().html();
    jQuery(woof_qt_tpl_no_product_selector).unwrap();

    jQuery(woof_qt_tpl_no_product_selector).remove();
    jQuery(woof_qt_tpl_item_selector).remove();
    var container = jQuery(woof_qt_tpl_cunstruct_selector).html();

    if (item_tpl || container) {
        templates = {
            'item': item_tpl,
            'container': container,
            'no_product': no_product_tpl
        }
        return  templates;
    }

}
function woof_qt_parse_temlate(str, data) {
    if (data && "object" == typeof data) {

        jQuery.each(data, function (key, value) {
            if (key == 'src') {
                str = str.replace(new RegExp("__SRC__", "gi"), value);
            }
            str = str.replace(new RegExp("__" + key.toUpperCase() + "__", "g"), value);
        });
    }
    return str;
}


/*
 * 
 * Pagination
 * 
 */

/* Get html of pagination */
function get_pagination_html(pages, curr_page) {
    if (pages < 2) {
        return "";
    }

    var pagination_html = '<div class="wooqt_pagination" data-curr-page="' + (curr_page) + '" >';
    var current = "";

    if ((curr_page) > 0) {
        pagination_html += '<span class="woof_qt_pagination_item " data-page="' + (curr_page) + '"><</span>';
    }
    var i = 0;
    var max_pag = pages;
    var max_page_property = 5;
    if (pages > max_page_property) {
        i = curr_page;
        max_pag = curr_page + max_page_property;
        if (max_pag > pages) {
            max_pag = pages
            var offset_page = max_page_property - (pages - curr_page);
            i = i - offset_page;
        } else if (i >= 1) {
            i = i - 1;
            max_pag = max_pag - 1;
        }
    }
    for (i; i < max_pag; i++) {
        current = "";
        if (i == curr_page) {
            current = "qt_current";
        }
        pagination_html += '<span class="woof_qt_pagination_item ' + current + '" data-page="' + (i + 1) + '">' + (i + 1) + '</span>';

    }
    if (pages - curr_page > max_page_property) {
        pagination_html += '<span >...</span>';
        pagination_html += '<span class="woof_qt_pagination_item " data-page="' + pages + '">' + pages + '</span>';
    }
    if ((curr_page + 1) < pages) {
        pagination_html += '<span class="woof_qt_pagination_item " data-page="' + (curr_page + 2) + '">></span>';
    }
    pagination_html += '</div>';
    return pagination_html
}

/* Init pagination event */
function woof_init_qt_pagination() {
    jQuery('.woof_qt_pagination_item').on('click', function () {
        var page = jQuery(this).attr('data-page');
        jQuery(".qt_current").removeClass('qt_current');

        jQuery(".woof_qt_pagination_item[data-page='" + page + "']").addClass('qt_current');
        woof_qt_curr_page = page - 1;
        woof_quick_search_draw();
    })
}


/*
 * 
 * Works with price
 * 
 */

/* Get price HTML  for templates (Includes discounts and variations) */
function  woof_get_price_html(prices) {
    if (prices == undefined || !prices) {
        return "";
    }
    var symbol = wooftextfilelink.currency_data.symbol;
    var decimal = wooftextfilelink.currency_data.decimal;

    var rate = 1;
    var decPoint = wooftextfilelink.currency_data.d_separ;
    var thousandsSep = wooftextfilelink.currency_data.t_separ;
    var price_html = "";

    if (typeof woocs_current_currency != "undefined") {
        rate = woocs_current_currency['rate'];
        symbol = woocs_current_currency['symbol'];
        decimal = woocs_current_currency['decimals'];
    }
    var get_price_item = function (regular, sale) {
        var _price = "";

        if ((sale != "" && sale != null && parseFloat(sale) != 0.0) && parseFloat(regular) > parseFloat(sale)) {
            _price = '<del>' + woof_add_symbol(woof_number_format(regular * rate, decimal, decPoint, thousandsSep)) + '</del> ' + woof_add_symbol(woof_number_format(sale * rate, decimal, decPoint, thousandsSep));
        } else if (regular != "" || regular != null || regular != 0) {
            _price = woof_add_symbol(woof_number_format(regular * rate, decimal, decPoint, thousandsSep));
        }
        return _price;
    }

    if (prices.length <= 1) {
        if (prices[0] == undefined) {
            return "";//free  
        }
        var item_html = get_price_item(prices[0]['regular'], prices[0]['sale']);
        return item_html;

    } else {
        var from = "";
        var to = "";
        var min = 0;
        var max = 0;
        jQuery.each(prices, function (key, val) {
            var curr_price = 0;
            var max_price = 0;
            var min_price = 0;

            if ((val['sale'] != "" && val['sale'] != null && parseFloat(val['sale'] != 0.0)) && parseFloat(val['regular']) > parseFloat(val['sale'])) {
                curr_price = val['sale'];
            } else if (val['regular'] != "" && val['regular'] != null && val['regular'] != 0) {
                curr_price = val['regular'];
            }
            if ((prices[min]['sale'] != "" && prices[min]['sale'] != null && prices[min]['sale'] != 0) && parseFloat(prices[min]['regular']) > parseFloat(prices[min]['sale'])) {
                min_price = prices[min]['sale'];
            } else if (prices[min]['regular'] != "" && prices[min]['regular'] != null && prices[min]['regular'] != 0) {
                min_price = prices[min]['regular'];
            }
            if ((prices[max]['sale'] != "" && prices[max]['sale'] != null && prices[max]['sale'] != 0) && parseFloat(prices[max]['regular']) > parseFloat(prices[max]['sale'])) {
                max_price = prices[max]['sale'];
            } else if (prices[max]['regular'] != "" && prices[max]['regular'] != null && prices[max]['regular'] != 0) {
                max_price = prices[max]['regular'];
            }
            if (parseFloat(curr_price) < parseFloat(min_price)) {
                min = key;
            }
            if (parseFloat(curr_price) > parseFloat(max_price)) {
                max = key;
            }
        });
        from = get_price_item(prices[min]['regular'], prices[min]['sale']);
        to = get_price_item(prices[max]['regular'], prices[max]['sale']);
        if (from == to) {
            return  from;
        }
        return from + "-" + to;
    }
}


/*additional function for sort or price search*/
function  woof_get_price_limits(prices) {
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


/*add currency symbol*/
function woof_add_symbol(price) {
    var position = wooftextfilelink.currency_data.position;
    var symbol = wooftextfilelink.currency_data.symbol;
    if (typeof woocs_current_currency != "undefined") {
        position = woocs_current_currency['position'];
        symbol = woocs_current_currency['symbol'];
    }
    switch (position) {
        case"left_space":
            return symbol + " " + price;
            break;
        case"left":
            return symbol + " " + price;
            break
        case"right_space":
            return symbol + " " + price;
            break;
        default:
            return price + symbol;
            break;

    }
}

/*
 * 
 * Sort function!!!
 * 
 */

/*get order by string*/
function woof_get_orderby_sql() {
    var sort_sql = " ORDER  BY d.title ASC ";
    var order_data = woof_qt_current_sort;
    switch (order_data) {
        case'title-desc':
            sort_sql = " ORDER  BY d.title DESC ";
            break;
        case'price-desc':
            sort_sql = " ORDER  BY get_min_price(d.price)*1 DESC ";
            break;
        case'price-asc':
            sort_sql = " ORDER  BY get_min_price(d.price)*1 ASC ";
            break;
        default:
            break

    }
    return sort_sql;
}
/*init  sorting function*/
function woof_qt_init_sort() {
    jQuery('.woof_qt_sort_item[data-order="' + woof_qt_current_sort + '"]').addClass('current_sort');
    jQuery('.woof_qt_sort_select option[value="' + woof_qt_current_sort + '"]').attr('selected', "");

    jQuery('.woof_qt_sort_item').on('click', function () {
        var order = jQuery(this).attr('data-order');
        if (order == "" || order == undefined) {
            return false;
        }
        woof_qt_curr_page = 0
        woof_qt_current_sort = order;
        woof_quick_search_draw();
    })
    jQuery('.woof_qt_sort_select').on('change', function () {
        var order = jQuery(this).val();
        if (order == "" || order == undefined) {
            return false;
        }
        woof_qt_curr_page = 0
        woof_qt_current_sort = order;
        woof_quick_search_draw();
    })
}
function woof_qt_sotr_html(asc, desc) {
    var sort_html = "<span class='woof_qt_sort_wraper'>";
    var curr_sort = woof_qt_current_sort;

    sort_html += '<span class="woof_qt_sort_item ' + ((curr_sort == asc) ? "current_sort" : "") + '" data-order="' + asc + '">&#9650;</span>';
    sort_html += '<span class="woof_qt_sort_item ' + ((curr_sort == desc) ? "current_sort" : "") + '" data-order="' + desc + '">&#9660;</span>';
    sort_html += "</span>";
    return sort_html;
}
function woof_qt_sotr_html_select(sort_data) {
    var curr_sort = woof_qt_current_sort;
    var sort_html = "<span class='woof_qt_sort_wraper'><select class='woof_qt_sort_select'>";
    jQuery.each(sort_data, function (i, val) {
        sort_html += '<option value="' + val.key + '"' + ((curr_sort == val.key) ? "selected" : "") + '>' + val.title + '</option>'
    })
    sort_html += "</select></span>";
    return sort_html;
}


/*
 * 
 * Price slider!!!
 * 
 */
/*init slider*/
function woof_qt_init_ion_sliders() {

    jQuery.each(jQuery('.woof_qt_price_slider'), function (index, input) {
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
                    woof_qt_current_values.min_price = parseFloat(ui.from, 10);
                    woof_qt_current_values.max_price = parseFloat(ui.to, 10);
                    //woocs adaptation
                    if (typeof woocs_current_currency !== 'undefined') {
                        woof_qt_current_values.min_price = (woof_qt_current_values.min_price / parseFloat(woocs_current_currency.rate));
                        woof_qt_current_values.max_price = (woof_qt_current_values.max_price / parseFloat(woocs_current_currency.rate));
                    }
                    woof_qt_curr_page = 0;/*reset pagination*/
                    woof_quick_search_draw();
                    return false;
                }
            });
        } catch (e) {

        }
    });
}
/* reset  slider */
function woof_qt_reset_ion_sliders() {
    var slider = jQuery(".woof_qt_price_slider").data("ionRangeSlider");
    if (slider != undefined) {
        slider.reset();
    }

}
/*
 * 
 * meta slider
 * 
 */
function woof_qt_init_meta_ion_sliders() {

    jQuery.each(jQuery('.woof_qt_meta_slider'), function (index, input) {
        var tax = jQuery(input).data('tax');
        try {
            jQuery(input).ionRangeSlider({
                min: jQuery(input).data('min'),
                max: jQuery(input).data('max'),
                type: 'double',
                prefix: jQuery(input).data('slider-prefix'),
                postfix: jQuery(input).data('slider-postfix'),
                prettify: true,
                hideMinMax: false,
                hideFromTo: false,
                grid: true,
                step: jQuery(input).data('step'),
                onFinish: function (ui) {
                    if (woof_qt_current_values.meta_filter[tax] == undefined) {
                        woof_qt_current_values.meta_filter[tax] = {value: [], type: "range"};
                    }
                    if (jQuery(input).data('min') == ui.from && jQuery(input).data('max') == ui.to) {
                        delete woof_qt_current_values.meta_filter[tax];
                    } else {
                        woof_qt_current_values.meta_filter[tax]['value'] = [parseFloat(ui.from, 10), parseFloat(ui.to, 10)];
                    }



                    woof_qt_curr_page = 0;/*reset pagination*/
                    woof_quick_search_draw();
                    return false;
                }
            });
        } catch (e) {

        }
    });
}
/* reset  slider */
function woof_qt_reset_meta_ion_sliders() {
    var slider = jQuery(".woof_qt_meta_slider").data("ionRangeSlider");
    if (slider != undefined) {
        slider.reset();
    }

}

/*
 * 
 * Checkbox
 * 
 */
/*init  checkboxes*/
function woof_qt_init_checkbox() {
    if (icheck_skin != 'none') {
        jQuery('.woof_qt_checkbox').iCheck('destroy');
        jQuery('.woof_qt_checkbox').iCheck({
            checkboxClass: 'icheckbox_' + icheck_skin.skin + '-' + icheck_skin.color,
        });

        jQuery('.woof_qt_checkbox').off('ifChecked');
        jQuery('.woof_qt_checkbox').on('ifChecked', function (event) {
            jQuery(this).attr("checked", true);
            var slug = jQuery(this).attr('data-tax');
            if (woof_qt_current_values.add_filter[slug] == undefined) {
                woof_qt_current_values.add_filter[slug] = [];
            }
            //meta filter
            if (jQuery(this).hasClass("meta_" + slug)) {
                if (woof_qt_current_values.meta_filter[slug] == undefined) {
                    woof_qt_current_values.meta_filter[slug] = {value: [], type: ""};
                }
                woof_qt_current_values.meta_filter[slug]['value'].push(jQuery(this).val());
                if (jQuery(this).val() == "meta_exist") {
                    woof_qt_current_values.meta_filter[slug]['type'] = 'exist';
                } else {
                    woof_qt_current_values.meta_filter[slug]['type'] = 'exact';
                }

            } else {
                woof_qt_current_values.add_filter[slug].push(jQuery(this).val());
            }

            woof_qt_curr_page = 0;/*reset pagination*/
            woof_quick_search_draw();
        });

        jQuery('.woof_qt_checkbox').off('ifUnchecked');
        jQuery('.woof_qt_checkbox').on('ifUnchecked', function (event) {
            jQuery(this).attr("checked", false);
            var slug = jQuery(this).attr('data-tax');
            //meta filter
            if (jQuery(this).hasClass("meta_" + slug)) {
                if (woof_qt_current_values.meta_filter[slug] == undefined) {
                    woof_qt_current_values.meta_filter[slug] = {value: [], type: ""};
                }
                var temp_array = woof_qt_current_values.meta_filter[slug]['value'];
                woof_qt_delete_element_array(temp_array, jQuery(this).val());
                if (temp_array) {
                    woof_qt_current_values.meta_filter[slug]['value'] = temp_array;
                } else {
                    delete woof_qt_current_values.meta_filter[slug];
                }

            } else {
                var temp_array = woof_qt_current_values.add_filter[slug];
                woof_qt_delete_element_array(temp_array, jQuery(this).val());
                if (temp_array) {
                    woof_qt_current_values.add_filter[slug] = temp_array;
                }
            }

            woof_qt_curr_page = 0;/*reset pagination*/
            woof_quick_search_draw();


        });

        //this script should be, because another way wrong way of working if to click on the label
        jQuery('.woof_qt_checkbox_label').off();
        jQuery('label.woof_qt_checkbox_label').on('click', function () {
            if (jQuery(this).prev().find('.woof_qt_checkbox').is(':checked')) {
                jQuery(this).prev().find('.woof_qt_checkbox').trigger('ifUnchecked');
                jQuery(this).prev().removeClass('checked');
            } else {
                jQuery(this).prev().find('.woof_qt_checkbox').trigger('ifChecked');
                jQuery(this).prev().addClass('checked');
            }

            return false;
        });
        /***/

    } else {
        jQuery('.woof_qt_checkbox').on('change', function (event) {
            var slug = jQuery(this).attr('data-tax');
            if (jQuery(this).is(':checked')) {
                jQuery(this).attr("checked", true);

                if (woof_qt_current_values.add_filter[slug] == undefined) {
                    woof_qt_current_values.add_filter[slug] = [];
                }

                if (jQuery(this).hasClass("meta_" + slug)) {
                    if (woof_qt_current_values.meta_filter[slug] == undefined) {
                        woof_qt_current_values.meta_filter[slug] = {value: [], type: ""};
                    }
                    woof_qt_current_values.meta_filter[slug]['value'].push(jQuery(this).val());
                    if (jQuery(this).val() == "meta_exist") {
                        woof_qt_current_values.meta_filter[slug]['type'] = 'exist';
                    } else {
                        woof_qt_current_values.meta_filter[slug]['type'] = 'exact';
                    }

                } else {
                    woof_qt_current_values.add_filter[slug].push(jQuery(this).val());
                }

                woof_qt_curr_page = 0;/*reset pagination*/
                woof_quick_search_draw();
            } else {
                jQuery(this).attr("checked", false);
                if (jQuery(this).hasClass("meta_" + slug)) {
                    if (woof_qt_current_values.meta_filter[slug] == undefined) {
                        woof_qt_current_values.meta_filter[slug] = {value: [], type: ""};
                    }
                    var temp_array = woof_qt_current_values.meta_filter[slug]['value'];
                    woof_qt_delete_element_array(temp_array, jQuery(this).val());
                    if (temp_array) {
                        woof_qt_current_values.meta_filter[slug]['value'] = temp_array;
                    } else {
                        delete woof_qt_current_values.meta_filter[slug];
                    }

                } else {
                    var temp_array = woof_qt_current_values.add_filter[slug];
                    woof_qt_delete_element_array(temp_array, jQuery(this).val());
                    if (temp_array) {
                        woof_qt_current_values.add_filter[slug] = temp_array;
                    }
                }
                woof_qt_curr_page = 0;/*reset pagination*/
                woof_quick_search_draw();
            }
        });
    }
}
function woof_qt_reset_checkbox() {
    var radio = jQuery('.woof_qt_checkbox');
    if (radio) {
        radio.attr("checked", false);
        radio.parents('.woof_qt_item_container').find('.checked').removeClass('checked');
    }
}

/*
 * 
 * radio
 * 
 */
/*init  radio*/
function woof_qt_init_radio() {
    if (icheck_skin != 'none') {
        jQuery('.woof_qt_radio').iCheck('destroy');

        jQuery('.woof_qt_radio').iCheck({
            radioClass: 'iradio_' + icheck_skin.skin + '-' + icheck_skin.color,
        });

        jQuery('.woof_qt_radio').off('ifChecked');
        jQuery('.woof_qt_radio').on('ifChecked', function (event) {
            jQuery(this).attr("checked", true);
            var slug = jQuery(this).attr('data-tax');
            if (woof_qt_current_values.add_filter[slug] == undefined) {
                woof_qt_current_values.add_filter[slug] = [];
            }
            woof_qt_current_values.add_filter[slug] = [jQuery(this).val()]
            woof_qt_curr_page = 0;/*reset pagination*/
            woof_quick_search_draw();
        });

    } else {
        jQuery('.woof_qt_radio').on('change', function (event) {
            jQuery(this).attr("checked", true);
            var slug = jQuery(this).attr('data-tax');
            if (woof_qt_current_values.add_filter[slug] == undefined) {
                woof_qt_current_values.add_filter[slug] = [];
            }
            woof_qt_current_values.add_filter[slug] = [jQuery(this).val()]
            woof_qt_curr_page = 0;/*reset pagination*/
            woof_quick_search_draw();
        });
    }

    jQuery('.woof_qt_radio_reset').on('click', function () {
        var slug = jQuery(this).attr('data-tax');
        jQuery(this).parents('.woof_qt_item_container').find('.checked').removeClass('checked');
        jQuery(this).parents('.woof_qt_item_container').find('input[type=radio]').removeAttr('checked');
        woof_qt_current_values.add_filter[slug] = [];
        woof_qt_curr_page = 0;/*reset pagination*/
        woof_quick_search_draw();
    });
}
function woof_qt_reset_radio() {
    var radio = jQuery('.woof_qt_radio');
    if (radio) {
        radio.attr("checked", false);
        radio.parents('.woof_qt_item_container').find('.checked').removeClass('checked');
    }
}


/*
 * 
 * drop-downs
 * 
 */
/*init  drop-downs*/
function woof_qt_init_select() {

    if (woof_select_type == 'chosen') {
	jQuery("select.woof_qt_select").chosen();
    } else if (woof_select_type == 'selectwoo') {
	jQuery("select.woof_qt_select").selectWoo();
    }  
    
    jQuery('select.woof_qt_select').change(function () {
        var tax_id = jQuery(this).val();
        var slug = jQuery(this).attr('data-tax');
        var tax_ids = [];
        if (Array.isArray(tax_id)) {
            tax_ids = tax_id;
        } else if (tax_id != -1) {
            tax_ids[0] = tax_id;
        }
        //meta filter
        if (jQuery(this).hasClass("meta_" + slug)) {
            woof_qt_current_values.meta_filter[slug] = {type: 'exact', value: tax_ids};

        } else {
            woof_qt_current_values.add_filter[slug] = tax_ids;
        }

        woof_qt_curr_page = 0;/*reset pagination*/
        woof_quick_search_draw();
    });

}
/*  reset drop-down */
function woof_qt_reset_select() {

    
    if (woof_select_type == 'chosen') {
	jQuery('select.woof_qt_select').val('').trigger("chosen:updated");
    } else if (woof_select_type == 'selectwoo') {
	jQuery('select.woof_qt_select').val('-1').trigger('change');
    } else {
        jQuery('select.woof_qt_select option:selected').each(function () {
            this.selected = false;
        });	
    }    
    
    
}

/*
 * 
 * text search
 * 
 */

/* init text search */
function woof_init_text_search() {
    jQuery('#woof_quick_search_form').keyup(function () {
        var text = jQuery(this).val();
        woof_qt_current_values.text_search = text.trim();
        woof_qt_curr_page = 0;/*reset pagination*/
        woof_quick_search_draw();
    });
}

/*
 * 
 * RESET
 * 
 */
/*init  filter reset */
function woof_qt_reset_init() {
    jQuery('.woof_qt_reset_filter_btn').on('click', function () {
        woof_qt_current_values.add_filter = {};
        woof_qt_current_values.meta_filter = {};
        jQuery('#woof_quick_search_form').val('');
        woof_qt_curr_page = 0;/*reset pagination*/
        woof_qt_current_values.max_price = null;
        woof_qt_current_values.max_price = null;
        woof_qt_current_values.text_search = "";
        woof_qt_reset_radio();
        woof_qt_reset_checkbox();
        woof_qt_reset_select();
        woof_qt_reset_ion_sliders();
        woof_qt_reset_meta_ion_sliders();
        woof_quick_search_draw();
    });
}
function  woof_qt_reset_btn_state(show) {
    if (show) {
        jQuery('.woof_qt_reset_filter_btn').show();
    } else {
        jQuery('.woof_qt_reset_filter_btn').hide();
    }
}

/*
 * 
 * Additional function!!!
 * 
 */

/* delete  from array */
function woof_qt_delete_element_array(arr, value) {
    var idx = arr.indexOf(value);
    if (idx != -1) {
        return arr.splice(idx, 1);
    }
    return false;
}

/* string format  */
if (!String.format) {
    String.format = function (format) {
        var args = Array.prototype.slice.call(arguments, 1);
        return format.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined'
                    ? args[number]
                    : match
                    ;
        });
    };
}

/* Number format  https://gist.github.com/xiel/5688446 */
function woof_number_format(number, decimals, decPoint, thousandsSep) {
    decimals = decimals || 0;
    number = parseFloat(number);

    if (!decPoint || !thousandsSep) {
        decPoint = '.';
        thousandsSep = ',';
    }

    var roundedNumber = Math.round(Math.abs(number) * ('1e' + decimals)) + '';
    var numbersString = decimals ? roundedNumber.slice(0, decimals * -1) : roundedNumber;
    var decimalsString = decimals ? roundedNumber.slice(decimals * -1) : '';
    var formattedNumber = "";

    while (numbersString.length > 3) {
        formattedNumber = thousandsSep +  numbersString.slice(-3)+  formattedNumber; 
        numbersString = numbersString.slice(0, -3);
    }
    var fix_num = numbersString + formattedNumber;
    if (fix_num == "") {
        fix_num = "0";
    }
    return (number < 0 ? '-' : '') + fix_num + (decimalsString ? (decPoint + decimalsString) : '');
}
/* Cuts very long strings */
function woof_cut_words(phrase, w_length) {
    if (phrase.length > w_length) {
        phrase = phrase.substr(0, w_length);
        phrase += '...';
    }
    return phrase;
}
/* Get text  of the notices */
function woof_qt_get_notice(type) {
    switch (type) {
        case'no_template':
            return '<div class="notice woof_qt_notice">Notice: Problems with the template or maybe you entered the template names incorrectly. Wrong structure <a href="#">shortcode [woof_quick_search_results]</a> </div>';
            break;
        case'no_shortcode':
            return '<div class="notice woof_qt_notice">Notice: To work with the advanced filter, you must use a <a href="#">shortcode [woof_quick_search_results]</a> </div>';
            break;
        default:
            return "<div class='notice  woof_qt_notice'>Something wrong!!!</div>";
    }
}
/* parse term logic */
function woof_qt_parse_term_logic(term_str) {
    var term_logic = {};
    var temp_arr = [];
    if (term_str == undefined || term_str.lenght) {
        return term_logic;
    }
    temp_arr = term_str.split(',');
    jQuery.each(temp_arr, function (i, item) {
        var logic_arr = item.split(':');
        if (logic_arr.length == 2) {
            term_logic[logic_arr[0]] = logic_arr[1];
        }
    });
    return  term_logic;
}
/*
 * 
 * File load
 * 
 */
function woof_load_serch_data() {
    jQuery.getJSON(wooftextfilelink.link, function (data) {
        data_text = data
    }).done(function () {
        console.log("File downloaded!");
        jQuery('#woof_quick_search_form').trigger("keyup");
    });
}

function woof_init_default_serch_data() {
    jQuery.getJSON(wooftextfilelink.link, function (data) {
        init_text_filter_form(wooftextfilelink.link, data);
    }).done(function () {
        console.log("File downloaded!");
    });
}
/*
 * 
 * Start!!!
 * 
 */

var woof_qt_form = jQuery('#woof_quick_search_form');
if (woof_qt_form.length) {
 
    woof_qt_target = woof_qt_form.attr('data-target-link')
    if (woof_qt_target != "_blank" && woof_qt_target != "_self") {
        woof_qt_target = "_blank";
    }
    if (woof_qt_form.attr('data-extended') != 0) {
        woof_qt_term_logic = woof_qt_parse_term_logic(woof_qt_form.attr('data-term_logic'));
        woof_qt_tax_logic = woof_qt_form.attr('data-tax_logic');
        if (woof_qt_tax_logic != 'AND' && woof_qt_tax_logic != 'OR') {
            woof_qt_tax_logic = 'AND';
        }
        woof_qt_group_text_logic = woof_qt_form.attr('data-text_group_logic');
        if (woof_qt_group_text_logic != 'AND' && woof_qt_group_text_logic != 'OR') {
            woof_qt_tax_logic = 'AND';
        }
        init_text_filter_content();
        if (woof_qt_form.attr('data-preload') == 1) {
            woof_load_serch_data();
        }
    } else {
        woof_init_default_serch_data();
    }

}
