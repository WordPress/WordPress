'use strict';
var woof_turbo_mode_creator = {
    running: false,
    generating: false,
    timeout: null,
    turbo_mode_offset: 0
};

woof_turbo_mode_creator.generate = function () {
    if (!woof_turbo_mode_creator.generating) {
        woof_turbo_mode_creator.generating = true;
        jQuery.ajax({
            type: 'POST',
            url: woof_turbo_mode_creator.url,
            data: {"action": "woof_turbo_mode_update_file", "nonce": woof_turbo_mode_creator.nonce, "turbo_mode_start": woof_turbo_mode_creator.turbo_mode_offset},
            complete: function () {
                woof_turbo_mode_creator.generating = false;
            },
            success: function (date) {
                date = JSON.parse(date);

                if (typeof date.total !== 'undefined') {

                    if (woof_turbo_mode_creator.turbo_mode_offset !== null) {
                        woof_turbo_mode_creator.turbo_mode_offset = date.total;
                        if (date.total < 0) {
                            woof_turbo_mode_creator.stop();
                        } else {
                            jQuery('span.woof_turbo_mode_product_count').html('<p> ' + woof_turbo_products + ': ' + date.total + '</p>');
                        }
                    }

                }
            },

        });
    }
};

function woof_turbo_mode_create_search_file(nonce, url) {
    if (!woof_turbo_mode_creator.running) {
        woof_turbo_mode_creator.running = true;
        woof_turbo_mode_creator.url = url;
        woof_turbo_mode_creator.nonce = nonce;
        woof_turbo_mode_creator.exec();
        jQuery(".woof_turbo_mode_product_load img").show();
        jQuery('#woof_turbo_mode_update').next('span.woof_turbo_mode_messange').html('<p class="woof_turbo_mode_succes">' + woof_turbo_creating + ' ...</p>');
        jQuery('#woof_turbo_mode_update').hide();
        jQuery('.woof_turbo_mode_product_succes').hide();
    }
}


woof_turbo_mode_creator.exec = function () {
    woof_turbo_mode_creator.timeout = setTimeout(
            function () {
                if (woof_turbo_mode_creator.running) {
                    if (!woof_turbo_mode_creator.generating) {
                        woof_turbo_mode_creator.generate();
                    }
                    woof_turbo_mode_creator.exec();
                }
            },
            1000
            );
};

woof_turbo_mode_creator.stop = function () {
    if (woof_turbo_mode_creator.running) {
        woof_turbo_mode_creator.running = false;
        clearTimeout(woof_turbo_mode_creator.timeout);
        jQuery('#woof_turbo_mode_update').show();
        jQuery(".woof_turbo_mode_product_load img").hide();
        jQuery('.woof_turbo_mode_product_succes').show();
    }
};

//to avoid logic errors with the count options
jQuery('#woof_hide_dynamic_empty_pos_turbo_mode').change(function () {
    if (jQuery(this).val() == 1) {
        jQuery('#woof_show_count_turbo_mode').val(1);
        jQuery('#woof_show_count_dynamic_turbo_mode').val(1);
    }
});

jQuery('#woof_show_count_dynamic_turbo_mode').change(function () {
    if (jQuery(this).val() >= 1) {
        jQuery('#woof_show_count_turbo_mode').val(1);
    } else {
        jQuery('#woof_hide_dynamic_empty_pos_turbo_mode').val(0);
    }
});

jQuery('#woof_show_count_turbo_mode').change(function () {
    if (jQuery(this).val() == 0) {
        jQuery('#woof_show_count_dynamic_turbo_mode').val(0);
        jQuery('#woof_hide_dynamic_empty_pos_turbo_mode').val(0);
    }
});
jQuery(document).ready(function () {
    jQuery('#woof_turbo_mode_update').on('click', function () {
        var turbo_mode_nonce = jQuery('#woof_turbo_mode_update_nonce').val();
        woof_turbo_mode_create_search_file(turbo_mode_nonce, ajaxurl);
    });
});