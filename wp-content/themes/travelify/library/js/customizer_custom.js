/**
 * Custom Control javascript.
 *

 /**
 * JS for slider sorting saving
 */
jQuery(window).load( function (){


    jQuery('.featured-slider-sortable').sortable({
        handle: 'label'})
        .bind( "sortupdate", function(event, ui) {
            var index = 1;
            var attrname = jQuery(this).find('input:first').attr('name');
            var attrbase = attrname.substring(0, attrname.indexOf('][') + 1);
            jQuery(this).find('li').each(function() {console.log(index);
                jQuery(this).find('.count').html(index);
                jQuery(this).attr('id', 'customize-control-travelify_theme_options-featured_post_slider-' + index);
                jQuery(this).find('input').attr('name', attrbase + '[' + index + ']');

                index++
            });
            arrtojson();
        });


    jQuery('.featured-slider-sortable li input').change( function () {
        arrtojson();
    })

    function arrtojson(){
        var arr = [];
        jQuery('.featured-slider-sortable li').each(function( index ){
                val = jQuery(this).find("input").val();
                arr[index+1] = val;
        })
        slider_data = JSON.stringify(arr);
        jQuery("#featured_slider").val(slider_data).trigger('change');
    }

    /* Clonning of Slides */
    jQuery('.clone-wrapper').cloneya().on('after_append.cloneya after_delete.cloneya', function (toClone, newClone) {
        jQuery('.featured-slider-sortable').trigger('sortupdate');
    });
  });