jQuery(document).ready(function() {

	jQuery(document).ajaxStart(function(){
		jQuery('.tipsy').hide();
	});
	
	jQuery(document).on('click', 'a[data-silent_action^="um_"]',function(){
		
		if ( typeof jQuery(this).attr('disabled') !== 'undefined' )
			return false;
		
		in_row = '';
		in_sub_row = '';
		in_column = '';
		in_group = '';
		
		if ( jQuery('.um-col-demon-settings').data('in_column') ) {
			in_row = jQuery('.um-col-demon-settings').data('in_row');
			in_sub_row = jQuery('.um-col-demon-settings').data('in_sub_row');
			in_column = jQuery('.um-col-demon-settings').data('in_column');
			in_group = jQuery('.um-col-demon-settings').data('in_group');
		}
		
		act_id = jQuery(this).data('silent_action');
		arg1 = jQuery(this).data('arg1');
		arg2 = jQuery(this).data('arg2');
		
		jQuery('.tipsy').hide();
				
		um_admin_remove_modal();
		
		jQuery.ajax({
			url: ultimatemember_ajax_url,
			type: 'POST',
			data: {action: 'ultimatemember_do_ajax_action', act_id : act_id, arg1 : arg1, arg2 : arg2, in_row: in_row, in_sub_row: in_sub_row, in_column: in_column, in_group: in_group },
			success: function(data){
				
				jQuery('.um-col-demon-settings').data('in_row', '');
				jQuery('.um-col-demon-settings').data('in_sub_row', '');
				jQuery('.um-col-demon-settings').data('in_column', '');
				jQuery('.um-col-demon-settings').data('in_group', '');

				um_admin_modal_responsive();
				um_admin_update_builder();
				
			},
			error: function(data){

			}
		});
			
		return false;
		
	});

});
