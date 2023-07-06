"use strict";
function woof_init_sliders() {
    jQuery.each(jQuery('.woof_taxrange_slider'), function (index, input) {
	
      try {	    
	    var slags = jQuery(input).data('slags').split(',');
            var tax = jQuery(input).data('tax');
	    var skin = jQuery(input).data('skin');
            var current = String(jQuery(input).data('current')).split(',');
            var from_index = 0, to_index = slags.length - 1;

            //***
            if (current.length > 0 && slags.length > 0) {
                jQuery.each(slags, function (index, v) {
                    if (v.toLowerCase() == current[0].toLowerCase()) {
                        from_index = index;
                    }
                    if (v.toLowerCase() == current[current.length - 1].toLowerCase()) {
                        to_index = index;
                    }
                });
            } else {
                to_index = parseInt(jQuery(input).data('max'), 10) - 1;
            }

            jQuery(input).ionRangeSlider({
                decorate_both: false,
                values_separator: "",
                from: from_index,
                to: to_index,
                //min_interval: 1,
                type: 'double',
                prefix: '',
                postfix: '',
                prettify: true,
                hideMinMax: false,
                hideFromTo: false,
                grid: true,
                step: 1,
                onFinish: function (ui) {
                    //*** range

                    woof_current_values[tax] = (slags.slice(ui.from, ui.to + 1)).join(',');
                    woof_ajax_page_num = 1;
                    if (woof_autosubmit) {
                        woof_submit_link(woof_get_submit_link());
                    }

                    woof_update_tax_slider(input);
                    return false;
                },
                onChange: function (ui) {
		       
                    woof_update_tax_slider(input);
                },
                onRedraw: function (ui) {
		    woof_update_tax_slider(input);
                }
            });

            woof_update_tax_slider(input);

        } catch (e) {

        }
    });

    //***

    jQuery('.woof_hide_slider').parent('.woof_block_html_items').parent('.woof_container_inner').parent('.woof_container_slider').remove();
}



function woof_update_tax_slider( input) {

    var step = 1;

    if (jQuery(input).data('grid_step') != undefined) {
        step = parseInt(jQuery(input).data('grid_step'));
        if (step == 0) {
            return false;
        }
    }
    var lbls = jQuery(input).prev('span').find(".irs-grid-text");
    var i = 0;
    for (i = 1; i < jQuery(lbls).length - 1; i++) {

        if (i % step == 0 && step != -1) {
            jQuery(lbls[i]).css('visibility', 'visible');
        } else {
            jQuery(lbls[i]).css('visibility', 'hidden');
        }

    }

}