function showAddSlider(){
    window.location.href = '?page=wp-slider/wp-slider.php&controller=manage-slider&action=edit'
}

function showAddSlide(){
    
    jQuery('#new-slide-element').css('display','');
}

function deleteSlide(msg){
    check = confirm(msg);
    if(check == false){
        return false;
    } else {
        return true;
    }
}

function execExample(){
    if(jQuery('#slider-example').length > 0){
        jQuery('#slider-example').cycle('destroy');
        jQuery('#slider-example').cycle({
            fx: jQuery('#effect').val(),
            timeout: jQuery('#frecuency').val()*1000,
            delay:  -jQuery('#delay').val()*1000,
            easing: jQuery('#easing').val(),
            sync: 1
        });
    }
}

jQuery(document).ready(function(){
    
    execExample();
    jQuery('.examplified')
    .change(function(){
        execExample();
    })
    .keyup(function(){
        execExample();
    });

    jQuery('a.change-file').click(function(){
        jQuery(this).parent().find('input').val('').toggle();
        jQuery(this).parent().find('span.filename').toggle();
        return false;
    });
});