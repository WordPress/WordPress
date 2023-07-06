"use strict";

function pn_woof_set_review(yes) {
    document.getElementById('pn_woof_review_suggestion').style.display = 'none';
    if (yes) {
        document.getElementById('pn_woof_review_yes').style.display = 'block';
    } else {
        document.getElementById('pn_woof_review_no').style.display = 'block';
    }
}

function pn_woof_dismiss_review(what = 1) {
    //1 maybe later, 2 do not ask more
    jQuery('#pn_woof_ask_favour').fadeOut();

    if (what === 1) {
        jQuery.post(ajaxurl, {
            action: 'woof_later_rate_alert'
        });
    } else {
        jQuery.post(ajaxurl, {
            action: 'woof_dismiss_rate_alert'
        });
    }

    return false;
}
