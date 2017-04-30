jQuery(document).ready(function(){  
    jQuery('#the-list').on('click', '.editinline', function(){  
		
		inlineEditPost.revert();

		var post_id = jQuery(this).closest('tr').attr('id');
		
		post_id = post_id.replace("post-", "");
		
		var $evo_inline_data = jQuery('#eventon_inline_' + post_id );
		
		var evcal_start_date 		= $evo_inline_data.find('.evcal_start_date').text();
		var evcal_start_time_hour 	= $evo_inline_data.find('.evcal_start_time_hour').text();
		var evcal_start_time_min 	= $evo_inline_data.find('.evcal_start_time_min').text();
		var evcal_st_ampm 			= $evo_inline_data.find('.evcal_st_ampm').text();
		var evcal_end_date 			= $evo_inline_data.find('.evcal_end_date').text();
		var evcal_end_time_hour 	= $evo_inline_data.find('.evcal_end_time_hour').text();
		var evcal_end_time_min	 	= $evo_inline_data.find('.evcal_end_time_min').text();
		var evcal_et_ampm	 		= $evo_inline_data.find('.evcal_et_ampm').text();
		var evcal_location	 		= $evo_inline_data.find('.evcal_location').text();
		var evcal_organizer	 		= $evo_inline_data.find('.evcal_organizer').text();
		var _featured	 			= $evo_inline_data.find('._featured').text();
		var _evo_date_format		= $evo_inline_data.find('._evo_date_format').text();
		var _evo_time_format		= $evo_inline_data.find('._evo_time_format').text();
		
		jQuery('input[name="evcal_start_date"]', '.inline-edit-row').val(evcal_start_date);
		jQuery('input[name="evcal_start_time_hour"]', '.inline-edit-row').val(evcal_start_time_hour);
		jQuery('input[name="evcal_start_time_min"]', '.inline-edit-row').val(evcal_start_time_min);
		jQuery('input[name="evcal_st_ampm"]', '.inline-edit-row').val(evcal_st_ampm);
		
		jQuery('input[name="evcal_end_date"]', '.inline-edit-row').val(evcal_end_date);
		jQuery('input[name="evcal_end_time_hour"]', '.inline-edit-row').val(evcal_end_time_hour);
		jQuery('input[name="evcal_end_time_min"]', '.inline-edit-row').val(evcal_end_time_min);
		jQuery('input[name="evcal_et_ampm"]', '.inline-edit-row').val(evcal_et_ampm);
		
		jQuery('input[name="_evo_date_format"]', '.inline-edit-row').val(_evo_date_format);
		jQuery('input[name="_evo_time_format"]', '.inline-edit-row').val(_evo_time_format);
		
		jQuery('input[name="evcal_location"]', '.inline-edit-row').val(evcal_location);
		jQuery('input[name="evcal_organizer"]', '.inline-edit-row').val(evcal_organizer);
		
		
		
		if (_featured=='yes') {
			jQuery('input[name="_featured"]', '.inline-edit-row').attr('checked', 'checked'); 
		} else {
			jQuery('input[name="_featured"]', '.inline-edit-row').removeAttr('checked'); 
		}
		
		
    });  
    
   
    
    
    
});  