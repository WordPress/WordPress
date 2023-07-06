"use strict";
var woof_qs_cretor = {
    running: false,
    generating: false,
    timeout: null,
    qs_offset: 0
};

woof_qs_cretor.generate = function () {

    if (typeof args === "undefined") {
        var args = {};
    }

    if (!woof_qs_cretor.generating) {
        woof_qs_cretor.generating = true;
        jQuery.ajax({
            type: 'POST',
            url: woof_qs_cretor.url,
            data: {"action": "woof_qt_update_file", "nonce": woof_qs_cretor.nonce, "qs_start": woof_qs_cretor.qs_offset},
            complete: function () {
                woof_qs_cretor.generating = false;
            },
            success: function (date) {
                date = JSON.parse(date);

                if (typeof date.total !== "undefined") {

                    if (woof_qs_cretor.qs_offset !== null) {
                        woof_qs_cretor.qs_offset = date.total;
                        if (date.total < 0) {
                            woof_qs_cretor.stop();
                        } else {
                            jQuery('span.woof_qt_product_count').html('<p> Products: ' + date.total + '</p>');
                        }
                    }

                }
            },

        });
    }
};

function woof_qs_create_search_file(nonce, url) {
    if (!woof_qs_cretor.running) {
        woof_qs_cretor.running = true;
        woof_qs_cretor.url = url;
        woof_qs_cretor.nonce = nonce;
        woof_qs_cretor.exec();
        jQuery('#woof_quick_search_update').next('span.woof_qt_messange').html('<p class="woof_qt_succes">Creating ...</p>');
        jQuery('#woof_quick_search_update').hide();
    }
}
;

woof_qs_cretor.exec = function () {
    woof_qs_cretor.timeout = setTimeout(
            function () {
                if (woof_qs_cretor.running) {
                    if (!woof_qs_cretor.generating) {
                        woof_qs_cretor.generate();
                    }
                    woof_qs_cretor.exec();
                }
            },
            1000
            );
};

woof_qs_cretor.stop = function () {
    if (woof_qs_cretor.running) {
        woof_qs_cretor.running = false;
        clearTimeout(woof_qs_cretor.timeout);
        jQuery('#woof_quick_search_update').show();
        jQuery('#woof_quick_search_update').next('span.woof_qt_messange').html("<p class='woof_qt_succes'>File updated!!!</p>");
    }
};


jQuery(document).ready(function () {
    jQuery('#woof_quick_search_update').on('click', function () {
        var qs_nonce = jQuery('#woof_qs_update_nonce').val();
        woof_qs_create_search_file(qs_nonce, ajaxurl);
    });

});

