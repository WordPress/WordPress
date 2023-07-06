"use strict";
function woof_sections_html_items() {

    var sections = jQuery('.woof_section_tab');
    var request = woof_current_values.replace(/(\\)/, '');
    request = JSON.parse(request);

    jQuery.each(sections, function (e, item) {
        var _this = this;
        jQuery.each(request, function (k, val) {

            var selected = jQuery(_this).find(".woof_container_" + k);
            if (jQuery(selected).length) {
                if (!jQuery(_this).prev('label').prev("input:checked").length) {
                    jQuery(_this).prev('label').trigger('click');
                }

            }
        });


    });
    
    woof_sections_check_empty_items();

}

function woof_sections_check_empty_items(){
    var sections = jQuery('.woof_section_tab');
    jQuery.each(sections, function (e, item) {
	setTimeout(function(){ 	 
	    var filters = jQuery(item).find('.woof_container');
	    var hidden_filter = 0;
	    jQuery.each(filters, function (e, filter) {
		if (jQuery(filter).is(":hidden")){
		    hidden_filter++;
		}
	    });
	    if(filters.length == hidden_filter || filters.length == 0){
		jQuery(item).prev('.woof_section_tab_label').hide();
		jQuery(item).hide();
	    }
	}, 1500);	

    });
}
document.addEventListener('woof-ajax-form-redrawing', (e) => {     
    woof_sections_check_empty_items();
});

woof_sections_html_items();
