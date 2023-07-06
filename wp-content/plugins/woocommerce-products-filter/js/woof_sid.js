"use strict";
var woof_edit_view = false;
var woof_current_conatiner_class = '';
var woof_current_containers_data = {};

jQuery(function () {
    jQuery('.woof_edit_view').on('click',function () {
        woof_edit_view = true;
        var sid = jQuery(this).data('sid');
        var sid_tmp = sid.substring(0, sid.indexOf(' '));
        if(sid_tmp){
           sid=sid_tmp; 
        }
        var css_class = 'woof_sid_' + sid;
        jQuery(this).next('div').html(css_class);
        
        //+++
        jQuery("." + css_class + " .woof_container_overlay_item").show();
        jQuery("." + css_class + " .woof_container").addClass('woof_container_overlay');
        jQuery.each(jQuery("." + css_class + " .woof_container_overlay_item"), function (index, ul) {
            jQuery(this).html(jQuery(this).parents('.woof_container').data('css-class'));
        });

        return false;
    });
    
    
    woof_init_masonry();
    
});

function woof_init_masonry() {
    return;

}



