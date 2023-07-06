"use strict";
var woof_messenger_init = 0;
function woof_init_products_messenger() {
    if (woof_messenger_init > 0) {
        return;
    }
    woof_messenger_init++;
    jQuery('#woof_add_subscr').attr('data-href', location.href);

    jQuery('body').on('click', '#woof_add_subscr', function () {
        var data = {
            action: "woof_messenger_add_subscr",
            user_id: jQuery(this).attr('data-user'),
            get_var: woof_current_values,
            link: location.href
        };
        jQuery.post(woof_ajaxurl, data, function (content) {

            if (content) {
                var req = content;
                jQuery('.woof_pm_max_count').remove();
                woof_redraw_subscr(req);
            }
        });

        return false;
    });
    jQuery('body').on('click', '.woof_remove_subscr', function () {
        if (!confirm(woof_confirm_lang)) {
            return false;
        }
        var data = {
            action: "woof_messenger_remove_subscr",
            user_id: jQuery(this).attr('data-user'),
            key: jQuery(this).attr('data-key')
        };

        jQuery.post(woof_ajaxurl, data, function (content) {

            var req = JSON.parse(content);

            jQuery('.woof_subscr_item_' + req.key).remove();
            woof_check_count_subscr();

        });

        return false;
    });
    function woof_redraw_subscr(data) {
        jQuery('.woof_subscr_list ul').append(data);
        woof_check_count_subscr();
    }

    function woof_check_count_subscr() {
        var count_li = jQuery('.woof_subscr_list ul:first').find('li').length;
        var max_count = jQuery('.woof_add_subscr_cont input').attr('data-count');

        if (count_li >= max_count) {
            jQuery('.woof_add_subscr_cont').hide();
        } else {
            jQuery('.woof_add_subscr_cont').show();

        }

    }
}
