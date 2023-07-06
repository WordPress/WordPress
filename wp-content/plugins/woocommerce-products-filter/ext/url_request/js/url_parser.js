"use strict";

function woof_get_submit_link() {
    
	//delete  seo text
	jQuery('.woof_seo_text').remove();    
    
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
    if (Object.keys(woof_current_values).length === 0) {
        if (woof_is_ajax) {
            history.pushState({}, "", woof_current_page_link);
        }

        let tmp_url = woof_current_page_link.split('/' + swoof_search_slug + '/');

        return tmp_url[0];
    }

    let hash = window.location.hash;
    let vars = window.location.search;
    let url = woof_current_page_link.replace(new RegExp(/page\/(\d+)\//), "");
    let tmp_url = url.split('/' + swoof_search_slug + '/');
    let new_url = tmp_url[0];
    var link = "";
    if (new_url.slice(-1) != '/') {
        new_url += '/';
    }

    let url_array = [];

    const ordered_data = Object.keys(woof_current_values).sort().reduce(
            (obj, key) => {
        obj[key] = woof_current_values[key];
        return obj;
    }, {});
    
    for (let j in ordered_data) {
        if (typeof url_parser_data.special[j] != 'undefined') {
            url_array.push(url_parser_data.special[j]);
        } else if (j == 'min_price' || 'max_price' == j) {
            if (j == 'min_price') {
                url_array.push('price-' + woof_current_values['min_price'] + '-to-' + woof_current_values['max_price']);
            }
        } else if (typeof url_parser_data.filters[j] != 'undefined') {
            let request = woof_current_values[j] + '';
            request = request.replaceAll(',', '-and-');
            request = request.replaceAll('^', '-to-');
            request = request.replaceAll(/\s+/g, '+');
            url_array.push(url_parser_data.filters[j] + '-' + request);
        }


    }

    let search_request_url = "";
    if (url_array.length) {
        search_request_url = swoof_search_slug + '/' + url_array.join('/') + '/';
    }

    if(typeof woof_current_values['orderby'] != 'undefined'){
	let searchParams = new URLSearchParams(vars);
	if (searchParams.has('orderby')) {
	    searchParams.delete('orderby');
	}
	searchParams.append("orderby", woof_current_values['orderby']);
	vars = "?" + searchParams.toString();	
    }
    link = new_url + search_request_url + vars + hash
    link = link.replace(new RegExp(/page\/(\d+)\//), "");
    if (woof_is_ajax) {

        if (typeof woof_current_values.page != 'undefined' && woof_current_values.page > 1) {
            link += 'page/' + woof_current_values.page + '/';
        }

        history.pushState({}, "", link);

    }

    return link;
}

