"use strict";
jQuery(document).ready(function () {

    jQuery(".woof_add_sections").on('click', function () {
        var data = {
            action: "woof_get_section_html"
        };
        jQuery.post(ajaxurl, data, function (section) {

            jQuery('#woof_sections_list').append(section);
            woof_init_section_scripts();
        });

    });
    woof_check_sections_val();

    jQuery('#woof_sections_list .woof_section_item select').change(function () {
        woof_check_sections_val();
    });

    jQuery("#woof_sections_generate").on('click', function () {
        var data = {};
        data.action = 'woof_sections_shortcode_gen';
        var values = jQuery(".woof_section_item");
        var sections = [];
        jQuery.each(values, function (i, item) {
            var title = jQuery(item).find("input[type='text']").val();
            var from = jQuery(item).find(".woof_section_from").val();
            var to = jQuery(item).find(".woof_section_to").val();
            sections.push(from + '+' + to + '^' + title);
        });

        var section_beh = jQuery("select[name='woof_settings[sections_type]']").val();

        jQuery(".woof_sections_shortcode_res").text("sections='" + sections.join(',') + "' sections_type=" + section_beh);

    });

woof_init_section_scripts();
});

function woof_init_section_scripts() {
    jQuery('.woof_sections_delete').off('click');
    jQuery('.woof_sections_delete').on('click', function () {
        var key = jQuery(this).data('key');
        jQuery("li[data-key='" + key + "']").remove();
    });
    woof_check_sections_val();
    jQuery('#woof_sections_list .woof_section_item select').off('change');
    jQuery('#woof_sections_list .woof_section_item select').change(function () {
        woof_check_sections_val();
    });

}


function woof_check_sections_val() {
    var sections = jQuery('.woof_section_item');
    var latest_value = null;
    var out_of_range = false;
    jQuery.each(sections, function (i, section) {
        var from = jQuery(section).find('select.woof_section_from');
        var to = jQuery(section).find('select.woof_section_to');
        var from_value = jQuery(from).val();
        var to_value = jQuery(to).val();
        var selected_to = 0;
        var selected_from = 0;


        jQuery.each(jQuery(from).find('option'), function (j, option) {
            if (latest_value == jQuery(option).attr('value') || latest_value == null) {
                selected_from++;
            }
            if (selected_from) {

                jQuery(option).removeAttr("disabled");
                if (selected_from == 2 && out_of_range) {
                    jQuery(option).attr('selected', 'selected');
                    from_value = jQuery(from).val();
                }

                if (latest_value != null && selected_from == 1) {
                    if (jQuery(to).val() == from_value) {
                        out_of_range = true;
                    }
                    jQuery(option).attr("disabled", "disabled");
                    jQuery(option).removeAttr("selected");
                }
                selected_from++;
            } else {
                if (jQuery(to).val() == from_value) {
                    out_of_range = true;
                }
                jQuery(option).attr("disabled", "disabled");
                jQuery(option).removeAttr("selected");
            }
        });
        out_of_range = false;

        jQuery.each(jQuery(to).find('option'), function (j, option) {

            if (from_value == jQuery(option).attr('value')) {
                selected_to++;
            }
            if (selected_to) {
                jQuery(option).removeAttr("disabled");
                if (selected_to === 1) {
                    if (out_of_range) {
                        jQuery(option).attr('selected', 'selected');
                    }
                }

                selected_to++;
            } else {
                if (jQuery(to).val() == from_value) {
                    out_of_range = true;
                }
                jQuery(option).attr("disabled", "disabled");
                jQuery(option).removeAttr("selected");
            }
        });
        latest_value = jQuery(to).val();

    });
}