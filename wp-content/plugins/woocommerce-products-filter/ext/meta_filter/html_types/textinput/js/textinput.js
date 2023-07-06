"use strict";
var woof_text_do_submit = false;

function woof_init_meta_text_input() {
    jQuery('.woof_meta_filter_textinput').keyup(function (e) {
        var val = jQuery(this).val();
	val=val.replace("\'","\&#039;");

       console.log(val)
        var uid = jQuery(this).data('uid');
        if (e.keyCode == 13 /*&& val.length > 0*/) {
            woof_text_do_submit = true;
            woof_text_direct_search(jQuery(this).attr('name'), val);
            return true;
        }

        //save new word into woof_current_values
        if (woof_autosubmit) {
            woof_current_values[jQuery(this).attr('name')] = val;
        } else {
            woof_text_direct_search(jQuery(this).attr('name'), val);
        }



        if (val.length > 0) {
            jQuery('.woof_textinput_go.' + uid).show(222);
        } else {
            jQuery('.woof_textinput_go.' + uid).hide();
        }



    });

    //+++
    jQuery('.woof_textinput_go').on('click', function () {
        var uid = jQuery(this).data('uid');
        woof_text_do_submit = true;
        var textinput=jQuery('.woof_meta_filter_textinput.'+ uid);
        woof_text_direct_search(textinput.attr('name'), textinput.val());
    });
}

function woof_text_direct_search(name, slug) {
     slug = encodeURIComponent(slug);
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
        woof_submit_link(woof_get_submit_link());
    }
}

