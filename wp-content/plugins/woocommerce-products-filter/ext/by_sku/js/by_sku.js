"use strict";
var woof_sku_do_submit = false;
function woof_init_sku() {
    woof_sku_check_reset();
    jQuery('.woof_show_sku_search').keyup(function (e) {
        var val = jQuery(this).val();
        val = val.replace(' ', '');
        var uid = jQuery(this).data('uid');

        if (e.keyCode == 13 ) {
            woof_sku_do_submit = true;
            woof_sku_direct_search('woof_sku', val);
            return true;
        }

        //save new word into woof_current_values
        if (woof_autosubmit) {
            woof_current_values['woof_sku'] = val;
        } else {
            woof_sku_direct_search('woof_sku', val);
        }


        //if (woof_is_mobile == 1) {
        if (val.length > 0) {
            jQuery('.woof_sku_search_go.' + uid).show(222);
	    jQuery('.woof_sku_search_reset.' + uid).show(222);
        } else {
            jQuery('.woof_sku_search_go.' + uid).hide();
	    jQuery('.woof_sku_search_reset.' + uid).hide();
        }
        //}


        //http://easyautocomplete.com/examples
        if (val.length >= 3 && woof_sku_autocomplete) {
            var input_id = jQuery(this).attr('id');
            var options = {
                url: function (phrase) {
                    return woof_ajaxurl;
                },
                //theme: "square",
                getValue: function (element) {
                    return element.name;
                },
                ajaxSettings: {
                    dataType: "json",
                    method: "POST",
                    data: {
                        action: "woof_sku_autocomplete",
                        dataType: "json"
                    }
                },
                preparePostData: function (data) {
                    data.phrase = jQuery("#" + input_id).val();
                    return data;
                },
                template: {
                    type: "description",
                    fields: {

                        description: "type"
                    }
                },
                list: {
                    maxNumberOfElements: woof_sku_autocomplete_items,
                    onChooseEvent: function () {
                        woof_sku_do_submit = true;
                        woof_sku_direct_search('woof_sku', jQuery("#" + input_id).val());
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
                    }

                },
                requestDelay: 400
            };
            try {
                jQuery("#" + input_id).easyAutocomplete(options);
            } catch (e) {
                console.log(e);
            }
            jQuery("#" + input_id).focus();
        }
    });
    //+++
    jQuery('body').on('click','.woof_sku_search_go', function () {
        var uid = jQuery(this).data('uid');
        woof_sku_do_submit = true;
        var val = jQuery('.woof_show_sku_search.' + uid).val();
        val = val.replace(' ', '');
        woof_sku_direct_search('woof_sku', val);
    });
    jQuery('body').on('click','.woof_sku_search_reset', function () {
        var uid = jQuery(this).data('uid');
        jQuery('.woof_show_sku_search.' + uid).val("");
        if(typeof woof_current_values['woof_sku'] != 'undefined'){
	     delete woof_current_values['woof_sku'];
	}
	woof_sku_check_reset();
	if( woof_sku_reset_behavior ){
	    woof_submit_link(woof_get_submit_link());
	}
    });    
}

function woof_sku_check_reset() {
    var all_sku = jQuery('.woof_show_sku_search');	
    jQuery.each(all_sku, function(index, input){
	    var val = jQuery(input).val();
	    var uid = jQuery(input).data('uid');
	    if (val.length > 0) {
		jQuery('.woof_sku_search_reset.' + uid).show(222);
	    } else {
		jQuery('.woof_sku_search_reset.' + uid).hide();
	    }		
    });    
}

function woof_sku_direct_search(name, slug) {

    jQuery.each(woof_current_values, function (index, value) {
        if (index == name) {
            delete woof_current_values[name];
            return;
        }
    });

    if (slug != 0) {
        woof_current_values[name] = slug;
    }

    woof_ajax_page_num = 1;
    if (woof_autosubmit || woof_sku_do_submit) {
        woof_sku_do_submit = false;
        woof_submit_link(woof_get_submit_link());
    }
}


