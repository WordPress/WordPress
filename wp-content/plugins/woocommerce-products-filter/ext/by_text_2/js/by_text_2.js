"use strict";
var woof_text_do_submit = false;
function woof_init_text() {
    jQuery(".woof_show_text_search").on("paste", function (e) {
        var pastedData = e.originalEvent.clipboardData.getData('text');
        woof_text_process_value(pastedData, this, e);

    });
    jQuery('.woof_show_text_search').keyup(function (e) {
        var val = jQuery(this).val();
        woof_text_process_value(val, this, e);

    });

    //+++
    jQuery('body').on('click', '.woof_text_search_go', function () {
        var uid = jQuery(this).data('uid');
        woof_text_do_submit = true;

        var val = jQuery('.woof_show_text_search.' + uid).val();
        val = val.replace("\"", "\&quot;");
        woof_text_direct_search('woof_text', val);
    });
}
function woof_text_process_value(value, _this, e) {
    var val = value;
    val = val.replace("\'", "\&#039;");
    val = val.replace("\"", "\&quot;");
    var uid = jQuery(_this).data('uid');

    if (e.keyCode == 13 /*&& val.length > 0*/) {

        woof_text_do_submit = true;
        woof_text_direct_search('woof_text', val);
        return true;
    }

    //save new word into woof_current_values
    if (woof_autosubmit) {
        woof_current_values['woof_text'] = val;
    } else {
        woof_text_direct_search('woof_text', val);
    }


    //if (woof_is_mobile == 1) {
    if (val.length > 0) {
        jQuery('.woof_text_search_go.' + uid).show(222);
    } else {
        jQuery('.woof_text_search_go.' + uid).hide();
    }
    //}

    //http://easyautocomplete.com/examples
    if (val.length >= 3 && woof_text_autocomplete) {
        //http://stackoverflow.com/questions/1574008/how-to-simulate-target-blank-in-javascript
        jQuery('body').on('click', '.easy-autocomplete a', function () {

            if (!how_to_open_links) {
                window.open(jQuery(this).attr('href'), '_blank');
                return false;
            }

            return true;
        });
        //***
        //http://easyautocomplete.com/examples
        var input_id = jQuery(_this).attr('id');
        var options = {
            url: function (phrase) {
                return woof_ajaxurl;
            },
            //theme: "square",
            getValue: function (element) {
                jQuery("#" + input_id).parents('.woof_show_text_search_container').find('.woof_show_text_search_loader').hide();
                jQuery("#" + input_id).parents('.woof_show_text_search_container').find('.woof_text_search_go').show();
                return element.name;
            },
            ajaxSettings: {
                dataType: "json",
                method: "POST",
                data: {
                    action: "woof_text_autocomplete",
                    dataType: "json"
                }
            },
            preparePostData: function (data) {
                jQuery("#" + input_id).parents('.woof_show_text_search_container').find('.woof_text_search_go').hide();
                jQuery("#" + input_id).parents('.woof_show_text_search_container').find('.woof_show_text_search_loader').show();
                //***
                data.phrase = jQuery("#" + input_id).val();

                data.auto_res_count = jQuery("#" + input_id).data('auto_res_count');
                data.auto_search_by = jQuery("#" + input_id).data('auto_search_by');
                return data;
            },
            template: {
                type: woof_post_links_in_autocomplete ? 'links' : 'iconRight',
                fields: {
                    iconSrc: "icon",
                    link: "link"
                }
            },
            list: {
                maxNumberOfElements: jQuery("#" + input_id).data('auto_res_count') > 0 ? jQuery("#" + input_id).data('auto_res_count') : woof_text_autocomplete_items,
                onChooseEvent: function () {
                    woof_text_do_submit = true;

                    if (woof_post_links_in_autocomplete) {
                        return false;
                    } else {
                        woof_text_direct_search('woof_text', jQuery("#" + input_id).val());
                    }

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

}
function woof_text_direct_search(name, slug) {
    //slug = encodeURIComponent(slug);
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
    if (woof_autosubmit || woof_text_do_submit) {
        woof_text_do_submit = false;
        woof_submit_link(woof_get_submit_link(), 0);
    }
}


