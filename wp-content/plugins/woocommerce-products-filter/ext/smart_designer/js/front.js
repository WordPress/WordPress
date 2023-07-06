document.addEventListener('woof_init_search_form', function () {
    woof_sd_slide_list();
});

function woof_sd_slide_list() {
    if (woof_checkboxes_slide_flag) {
        let childs = jQuery('.woof-sd-ie-childs');

        if (childs.length) {
            jQuery.each(childs, function (index, child) {
                if (jQuery(child).parents('.woof_no_close_childs').length) {
                    return;
                }

                let span_class = 'woof_is_closed';

                if (woof_supports_html5_storage()) {
                    let preulstate = localStorage.getItem(jQuery(child).prev().attr('class'));

                    if (preulstate && preulstate === 'woof_is_opened') {
                        span_class = 'woof_is_opened';
                        jQuery(child).show();
                    } else {
                        if (jQuery(child).find('input[type=checkbox],input[type=radio]').is(':checked')) {
                            jQuery(child).show();
                            span_class = 'woof_is_opened';
                        } else {
                            jQuery(child).hide();
                        }
                    }
                }

                jQuery(child).prev().find('woof-sd-list-opener').html('<a href="javascript:void(0);" class="woof_childs_list_opener" ><span class="' + span_class + '"></span></a>');
            });

            jQuery.each(jQuery('woof-sd-list-opener a.woof_childs_list_opener span'), function (index, a) {
                jQuery(a).on('click', function () {
                    let span = jQuery(this);
                    let this_ = span.parent();

                    if (span.hasClass('woof_is_closed')) {
                        //lets open
                        jQuery(this_).closest('.woof-sd-ie').next().show(333);
                        span.removeClass('woof_is_closed');
                        span.addClass('woof_is_opened');
                    } else {
                        //lets close
                        jQuery(this_).closest('.woof-sd-ie').next().hide(333);
                        span.removeClass('woof_is_opened');
                        span.addClass('woof_is_closed');
                    }

                    if (woof_supports_html5_storage()) {
                        let ullabel = jQuery(this_).closest('.woof-sd-ie').attr('class');
                        let ullstate = jQuery(this_).children('span').attr('class');
                        localStorage.setItem(ullabel, ullstate);
                    }

                    return false;
                });
            });
        }
    }
}


