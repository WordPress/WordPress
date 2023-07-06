"use strict";
jQuery(function ($) {    
    $('.woof_toggle_colors').on('click',function () {
        $(this).parent().find('ul.woof_color_list').toggleClass('woof_hide_options'); //toggle
    });
});
