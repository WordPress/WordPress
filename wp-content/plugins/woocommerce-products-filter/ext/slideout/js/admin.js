"use strict";

jQuery(document).ready(function(){    
    jQuery("#woow_slideout_generate").on('click', function () {
        var data = {};
        data.action = 'woof_slideout_shortcode_gen';
        var values = jQuery(".slideout_value");
        jQuery.each(values, function (i, item) {
            var key = jQuery(item).data("name");
            if (key) {
                data[key] = jQuery(item).val();
            }
        });

        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: data,
            success: function (res) {
                jQuery(".woof_slideout_shortcode_res").text(res);
            }
        });
    });
    jQuery('select[name="woof_settings[woof_slideout_type_btn]"]').change(function () {
        var type = jQuery(this).val();
        if (type == 0) {
            jQuery('input[name="woof_settings[woof_slideout_img]"]').parents(".woof-control-section").show();
            jQuery('input[name="woof_settings[woof_slideout_txt]"]').parents(".woof-control-section").hide();
            jQuery('input[name="woof_settings[woof_slideout_img_w]"]').parents(".woof-control-section").show();
        } else {
            jQuery('input[name="woof_settings[woof_slideout_img]"]').parents(".woof-control-section").hide();
            jQuery('input[name="woof_settings[woof_slideout_img_w]"]').parents(".woof-control-section").hide();
            jQuery('input[name="woof_settings[woof_slideout_txt]"]').parents(".woof-control-section").show();
        }
    });

});