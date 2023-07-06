"use strict";
function woof_init_radios() {
    if (icheck_skin != 'none') {
        jQuery('.woof_radio_term').iCheck('destroy');

        jQuery('.woof_radio_term').iCheck({
            radioClass: 'iradio_' + icheck_skin.skin + '-' + icheck_skin.color,      
        });

        jQuery('.woof_radio_term').off('ifChecked');
        jQuery('.woof_radio_term').on('ifChecked', function (event) {
            jQuery(this).attr("checked", true);
            jQuery(this).parents('.woof_list').find('.woof_radio_term_reset').removeClass('woof_radio_term_reset_visible');
            jQuery(this).parents('.woof_list').find('.woof_radio_term_reset').hide();
            jQuery(this).parents('li').eq(0).find('.woof_radio_term_reset').eq(0).addClass('woof_radio_term_reset_visible');
            var slug = jQuery(this).data('slug');
            var name = jQuery(this).attr('name');
            var term_id = jQuery(this).data('term-id');
            woof_radio_direct_search(term_id, name, slug);
        });
        
        //***

  
         
    } else {
        jQuery('.woof_radio_term').on('change', function (event) {
            jQuery(this).attr("checked", true);
            var slug = jQuery(this).data('slug');
            var name = jQuery(this).attr('name');
            var term_id = jQuery(this).data('term-id');
			
			jQuery(this).parents('.woof_list').find('.woof_radio_term_reset').removeClass('woof_radio_term_reset_visible');
            jQuery(this).parents('.woof_list').find('.woof_radio_term_reset').hide();
            jQuery(this).parents('li').eq(0).find('.woof_radio_term_reset').eq(0).addClass('woof_radio_term_reset_visible');
			
            woof_radio_direct_search(term_id, name, slug);
        });
    }

    //***

    jQuery('.woof_radio_term_reset').on('click',function () {
        woof_radio_direct_search(jQuery(this).data('term-id'), jQuery(this).attr('data-name'), 0);
        jQuery(this).parents('.woof_list').find('.checked').removeClass('checked');
        jQuery(this).parents('.woof_list').find('input[type=radio]').removeAttr('checked');
        //jQuery(this).remove();
        jQuery(this).removeClass('woof_radio_term_reset_visible');
        return false;
    });
}


function woof_radio_direct_search(term_id, name, slug) {

    jQuery.each(woof_current_values, function (index, value) {
        if (index == name) {
            delete woof_current_values[name];
            return;
        }
    });

    if (slug != 0) {
        woof_current_values[name] = slug;
        jQuery('a.woof_radio_term_reset_' + term_id).hide();
        jQuery('woof_radio_term_' + term_id).filter(':checked').parents('li').find('a.woof_radio_term_reset').show();
        jQuery('woof_radio_term_' + term_id).parents('ul.woof_list').find('label').css({'fontWeight': 'normal'});
        jQuery('woof_radio_term_' + term_id).filter(':checked').parents('li').find('label.woof_radio_label_' + slug).css({'fontWeight': 'bold'});
    } else {
        jQuery('a.woof_radio_term_reset_' + term_id).hide();
        jQuery('woof_radio_term_' + term_id).attr('checked', false);
        jQuery('woof_radio_term_' + term_id).parent().removeClass('checked');
        jQuery('woof_radio_term_' + term_id).parents('ul.woof_list').find('label').css({'fontWeight': 'normal'});
    }

    woof_ajax_page_num = 1;
    if (woof_autosubmit) {
        woof_submit_link(woof_get_submit_link());
    }
}


