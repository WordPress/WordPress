jQuery(document).ready(function() {

	/* Remove field permanently */
	jQuery(document).on('click', '.um-admin-btns a span.remove', function(e){
		e.preventDefault();
		
		if (confirm('This will permanently delete this custom field from database')) {
		
			jQuery(this).parents('a').remove();
			
			arg1 = jQuery(this).parents('a').data('arg1');
			
			jQuery.ajax({
				url: ultimatemember_ajax_url,
				type: 'POST',
				data: {action: 'ultimatemember_do_ajax_action', act_id : 'um_admin_remove_field_global', arg1 : arg1 },
				success: function(data){
					
				},
				error: function(data){

				}
			});
		
			
		}
		
		return false;
	});
	
	/* Add a Field */
	jQuery(document).on('submit', 'form.um_add_field', function(e){
		
		e.preventDefault();
		
		var form = jQuery(this);

		jQuery.ajax({
			url: ultimatemember_ajax_url,
			type: 'POST',
			dataType: 'json',
			data: form.serialize(),
			beforeSend: function(){
				form.css({'opacity': 0.5});
				jQuery('.um-admin-error').removeClass('um-admin-error');
				form.find('.um-admin-error-block').hide();
				form.find('.um-admin-success-block').hide();
			},
			complete: function(){
				form.css({'opacity': 1});
			},
			success: function(data){
				
				if (data.error){
				
					c = 0;
					jQuery.each(data.error, function(i, v){
						c++;
						if ( c == 1 ) {
						form.find('#'+i).addClass('um-admin-error').focus();
						form.find('.um-admin-error-block').show().html(v);
						}
					});
					
					um_admin_modal_responsive();
					
				} else {
				
					jQuery('.um-col-demon-settings').data('in_row', '');
					jQuery('.um-col-demon-settings').data('in_sub_row', '');
					jQuery('.um-col-demon-settings').data('in_column', '');
					jQuery('.um-col-demon-settings').data('in_group', '');

					um_admin_remove_modal();
					um_admin_update_builder();

				}
				
			},
			error: function(data){

			}
		});
		
		return false;
		
	});

});