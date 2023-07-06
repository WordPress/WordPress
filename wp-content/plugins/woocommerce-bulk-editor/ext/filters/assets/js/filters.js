"use strict";

var woobe_filtering_is_going = false;//marker about that products are filtered
var woobe_filter_chosen_inited = false;//just fix to init chosen
var woobe_filter_current_key = null;//unique id of current filter operation, which allow make bulk by filter in different browser tabs!

jQuery(function ($) {

    //init chosen by first click because chosen init doesn work for hidden containers
    jQuery(document).on("do_tabs-filters", {}, function () {
        //if (!woobe_filter_chosen_inited) {
        setTimeout(function () {
            //set chosen
            jQuery('#tabs-filters .chosen-select').chosen();
            woobe_filter_chosen_inited = true;
        }, 150);
        //}

        return true;
    });

    //set chosen to filters tab only
    jQuery('a[href="#tabs-filters"]').trigger('click');

    jQuery('.woobe_filter_select').on('change',function () {
        if (jQuery(this).val() == -1 || jQuery(this).val() == 0) {
            jQuery(this).removeClass('woobe_set_attention');
        } else {
            jQuery(this).addClass('woobe_set_attention');
        }
        return true;
    });

    //***

    //placeholder label
    jQuery('#woobe_filter_form input[placeholder]:not(.woobe_calendar)').placeholderLabel();

    //***
    
      
    //Filter button
    jQuery('#woobe_filter_products_btn').on('click', function () {

        //jQuery('.woobe_txt_search').val('');
       // console.log( jQuery('#woobe_filter_form').serializeArray())
        woobe_message(lang.filters.filtering, 'warning');
        woobe_filter_current_key = (woobe_get_random_string(16)).toLowerCase();
        jQuery('.woobe_tools_panel_newprod_btn').hide();
	
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_filter_products',
                filter_data: jQuery('#woobe_filter_form').serialize(),
                filter_current_key: woobe_filter_current_key
            },
            success: function (e) {
                console.log(e);
                woobe_message(lang.filters.filtered, 'notice', 30000);
                data_table.clear().draw();

                jQuery('.woobe_filter_reset_btn1').show();
                jQuery('.woobe_filter_reset_btn2').show();
                woobe_filtering_is_going = true;
                __woobe_action_will_be_applied_to();
            },
            error: function () {
                alert(lang.error);
            }
        });

        return false;
    });
    
    jQuery(document).keydown(function(event) {
	var k = event.keyCode;
	if (event.altKey && k == 83 ) {
	    jQuery('#woobe_filter_products_btn').trigger('click');
	    jQuery('html, body').animate({
        scrollTop: jQuery(".bear-plugin-name").offset().top
}, 777);
	}
      });


    //Reset Filter button
    jQuery('.woobe_filter_reset_btn1, .woobe_filter_reset_btn2').on('click', function () {

        var _this = this;
        woobe_message(lang.reseting, 'warning', 99999);
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_reset_filter',
                filter_current_key: woobe_filter_current_key
            },
            success: function () {

                if (!jQuery(_this).hasClass('woobe_filter_reset_btn2')) {
                    //jQuery('.woobe_top_panel_btn').trigger('click');
                }

                woobe_filter_current_key = null;
                jQuery('.woobe_tools_panel_newprod_btn').show();

                data_table.clear().draw();
                woobe_message(lang.reseted, 'notice');
                jQuery('.woobe_filter_reset_btn1').hide();
                jQuery('.woobe_filter_reset_btn2').hide();
                //clear all filter drop-downs and inputs
                __woobe_clean_filter_form();

                woobe_filtering_is_going = false;
                __woobe_action_will_be_applied_to();
            },
            error: function () {
                alert(lang.error);
            }
        });

        return false;
    });

    //***

    jQuery('#woobe_filter_form input').keydown(function (e) {
        if (e.keyCode == 13) {
            jQuery('#woobe_filter_products_btn').trigger('click');
        }
    });

    //***

    jQuery(document).on("taxonomy_data_redrawn", {}, function (event, tax_key, term_id) {

        var select_id = 'woobe_filter_taxonomies_' + tax_key;
        var select = jQuery('#' + select_id);
        jQuery(select).empty();
        __woobe_fill_select(select_id, taxonomies_terms[tax_key]);
        jQuery(jQuery('#' + select_id)).chosen({
            width: '100%'
        }).trigger("chosen:updated");

        return true;
    });
    
    jQuery('.woobe_filter_tools_select[name="woobe_filter_tools_options"]').on('change',function(){
        var val=jQuery(this).val();
        var select_beh=jQuery('select[name="woobe_filter_tools_behavior"]');
        var val_beh=select_beh.val();
        
        var options=jQuery('select[name="woobe_filter['+val+'][behavior]"]').find("option").clone();
        
        select_beh.html(options);
        
        var selected=select_beh.find('option[value="'+val_beh+'"]');
        if(selected){
            selected.attr('selected','selected');
        }               
    });
    jQuery('#woobe_filter_btn_tools_panel').on('click', function () {
        var text=jQuery('input[name="woobe_filter_form_tools_value"]').val();
        var option = jQuery('select[name="woobe_filter_tools_options"]').val();
        var behavior="exact";
        if(option!='post__in'){
            behavior=jQuery('select[name="woobe_filter_tools_behavior"]').val();
        }
        var text_input=jQuery('input[name="woobe_filter['+option+'][value]"]');
        if(jQuery(text_input).length){
            __woobe_clean_filter_form();
            jQuery('input[name="woobe_filter_form_tools_value"]').val(text);
            jQuery(text_input).val(text);
            setTimeout(function(){
                jQuery(text_input).siblings('label').css("margin-top", "-11px"); 
            }, 2000);
            
            jQuery('select[name="woobe_filter['+option+'][behavior]"]').val(behavior);
        }
        jQuery('#woobe_filter_products_btn').trigger( "click" );

        /* do not use it!!
        jQuery('html, body').animate({
                scrollTop: jQuery("#woobe_tools_panel").offset().top
        }, 777);
        */
    }); 
    
    jQuery("input[name='woobe_filter_form_tools_value']").off().on('keyup change', function (e) {
        if (e.keyCode === 13) {
            jQuery('#woobe_filter_btn_tools_panel').trigger("click");
        }
    });

});


function __woobe_clean_filter_form() {
    jQuery('#woobe_filter_form input[type=text]').val('');
    jQuery('#woobe_filter_form input[type=number]').val('');
    jQuery('#woobe_filter_form .woobe_calendar').val('').trigger('change');
    jQuery('#woobe_filter_form select.chosen-select').val('').trigger("chosen:updated");
    jQuery('#woobe_filter_form select:not(.chosen-select)').each(function (i, s) {
        jQuery(s).val(jQuery(s).find('option:first').val());
    });
    jQuery('#woobe_filter_form select').removeClass('woobe_set_attention');
    //tool panel filter
    jQuery("#woobe_filter_form_tools_panel input[type=text]").val('');
}

