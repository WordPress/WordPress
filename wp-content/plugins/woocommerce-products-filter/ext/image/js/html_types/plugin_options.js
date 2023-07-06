"use strict";
jQuery(function ($) {
    $('.woof_toggle_images').on('click', function () {
        $(this).parent().find('ul.woof_image_list').toggleClass('woof_hide_options');
    });
});
