"use strict";
function woof_init_meta_slider() {
    
    jQuery('.woof_metarange_slider_inputs input').on('change', function () {

        var from = parseFloat(jQuery(this).parent().find('.woof_metarange_slider_from').val(), 10);
        var to = parseFloat(jQuery(this).parent().find('.woof_metarange_slider_to').val(), 10);
	var min = parseFloat(jQuery(this).parent().find('.woof_metarange_slider_from').attr('min'),10);
        var max = parseFloat(jQuery(this).parent().find('.woof_metarange_slider_to').attr('max'), 10);
	var name = jQuery(this).data('name');
	if (min > from) {
	    from = min;
	}
	if (max < to) {
	    to = max;
	}	
	
	woof_current_values[name] = from + "^" + to;


        if (woof_autosubmit || jQuery(this).within('.woof').length == 0) {
            woof_submit_link(woof_get_submit_link());
        }
    });
    
    jQuery.each(jQuery('.woof_metarange_slider'), function (index, input) {
        try {
            jQuery(input).ionRangeSlider({
                min: jQuery(input).data('min'),
                max: jQuery(input).data('max'),
                from: jQuery(input).data('min-now'),
                to: jQuery(input).data('max-now'),
                type: 'double',
                prefix: jQuery(input).data('slider-prefix'),
                postfix: jQuery(input).data('slider-postfix'),
                prettify_enabled: jQuery(input).data('prettify'),
                hideMinMax: false,
                hideFromTo: false,
                grid: true,
                step: jQuery(input).data('step'),
                onFinish: function (ui) {
                    woof_current_values[jQuery(input).attr('name')] = parseFloat(ui.from, 10) + "^" + parseFloat(ui.to, 10);
		     
                    //***
		    var top_panel = jQuery('input[data-anchor^="woof_n_' + jQuery(input).attr('name') + '"]');
		    var title = jQuery(input).parents('.woof_container_inner').find('h4').text();
		    jQuery(top_panel).val(title + ': ' + parseFloat(ui.from, 10) + "-" + parseFloat(ui.to, 10));
		    jQuery(top_panel).attr('data-anchor','woof_n_' + jQuery(input).attr('name')+ '_' + parseFloat(ui.from, 10) + "^" + parseFloat(ui.to, 10));

                    if (woof_autosubmit || jQuery(input).within('.woof').length == 0) {
                        woof_submit_link(woof_get_submit_link());
                    }
                    return false;
                },
                onChange: function (data) {
                    if (jQuery('.woof_metarange_slider_inputs input')) {
                        jQuery('.woof_metarange_slider_from').val(parseFloat(data.from, 10) );
                        jQuery('.woof_metarange_slider_to').val(parseFloat(data.to, 10) );
                    }
                }		
            });
        } catch (e) {

        }
    });
}
