"use strict";
jQuery(document).ready(function() {
    jQuery(function () {
        
        if(!document.querySelector('.woof-slide-out-div')){
            return;
        }
        
        document.querySelector('.woof-slide-out-div').removeAttribute('style');
        jQuery('.woof-slide-out-div').css('opacity', 0.95);
        jQuery.each(jQuery('.woof-slide-out-div'), function(i,item){
            var key=jQuery(item).data("key");

            jQuery(item).tabSlideOut({
                tabHandle: '.woof-handle.'+key, //class of the element that will be your tab
                tabImage: jQuery(item).data('image'), //link to the image for the tab *required*
                tabImageHeight: jQuery(item).data('image_h') + 'px', //height of tab image *required*
                tabImageWidth: jQuery(item).data('image_w') + 'px', //width of tab image *required*    
                tabLocation: jQuery(item).data('location'), //side of screen where tab lives, top, right, bottom, or left
                bounceSpeed: jQuery(item).data('speed'), //speed of animation
                action: jQuery(item).data('action'), //options: 'click' or 'hover', action to trigger animation
                offset: jQuery(item).data('toppos'), //position from the top
               // fixedPosition: true, //options: true makes it stick(fixed position) on scroll
                onLoadSlideOut: jQuery(item).data('onloadslideout')
            });

            if(woof_slideout_screenHeight()-jQuery(item).position().top<jQuery(item).height()){
                var height=0;
                if(jQuery(item).data('location')=="top"|| jQuery(item).data('location')=="bottom"){
                    height=woof_slideout_screenHeight()- jQuery('.woof-handle.'+key).height() -10;
                }else{
                    height=woof_slideout_screenHeight()-jQuery(item).position().top-15;
                }
                if(height){
                    jQuery(item).find(".woof-slide-content").css("height",height);
                }                
            } 
            if(woof_slideout_screenWidth()<=jQuery(item).width()){
                var width=0;
                if(jQuery(item).data('location')=="right"|| jQuery(item).data('location')=="left"){
                    
                    var width=woof_slideout_screenWidth()- jQuery('.woof-handle.'+key).width()-20;
                    jQuery(item).find(".woof-slide-content").css("width",width);
                }
                
            }
           
          
        });

    });
});    
function woof_slideout_screenHeight(){
    return  jQuery(window).height();
}
function woof_slideout_screenWidth(){
    return jQuery(window).width();
}