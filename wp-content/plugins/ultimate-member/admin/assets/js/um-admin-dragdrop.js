function UM_Drag_and_Drop(){
	
	jQuery('.um-admin-drag-col,.um-admin-drag-group').sortable({
		items: '.um-admin-drag-fld',
		connectWith: '.um-admin-drag-col,.um-admin-drag-group',
		placeholder: "um-fld-placeholder",
		forcePlaceholderSize:true,
		update: function(event, ui){
			
			jQuery('#publish').attr('disabled','disabled');
			
			if ( ui.item.hasClass('um-field-type-group') && ui.item.parents('.um-field-type-group').length > 0  ) {
				
				jQuery('.um-admin-drag-col,.um-admin-drag-group').sortable('cancel');
				
				jQuery('#publish').removeAttr('disabled');
				
			} else {
				
				UM_Change_Field_Col();
				
				UM_Change_Field_Grp();
				
				UM_Rows_Refresh();
				
			}
			
		}
	});
	
	jQuery('.um-admin-drag-rowsubs').sortable({
		items: '.um-admin-drag-rowsub',
		placeholder: "um-rowsub-placeholder",
		forcePlaceholderSize:true,
		zIndex: 9999999999,
		update: function(){
			
			jQuery('#publish').attr('disabled','disabled');
		
			UM_update_subrows();
		
			UM_Rows_Refresh();
			
		}
	}).disableSelection();
	
	jQuery('.um-admin-drag-rowsub').sortable({
		items: '.um-admin-drag-col',
		zIndex: 9999999999,
		update: function(){
		
			jQuery('#publish').attr('disabled','disabled');
			
			row = jQuery(this);
			row.find('.um-admin-drag-col').removeClass('cols-1 cols-2 cols-3 cols-last cols-middle');
			row.find('.um-admin-drag-col').addClass('cols-' + row.find('.um-admin-drag-col').length );
			row.find('.um-admin-drag-col:last').addClass('cols-last');
			if ( row.find('.um-admin-drag-col').length == 3 ) {row.find('.um-admin-drag-col:eq(1)').addClass('cols-middle');}
			
			UM_Change_Field_Col();
			
			UM_Change_Field_Grp();
			
			UM_Rows_Refresh();
			
		}
	}).disableSelection();
	
	jQuery('.um-admin-drag-ajax').sortable({
		items: '.um-admin-drag-row',
		handle: ".um-admin-drag-row-start",
		zIndex: 9999999999,
		placeholder: "um-row-placeholder",
		forcePlaceholderSize:true,
		out: function(){
			jQuery('.tipsy').remove();
		},
		update: function(){
		
			jQuery('#publish').attr('disabled','disabled');
			
			UM_update_rows();
		
			UM_Change_Field_Col();
			
			UM_Change_Field_Grp();
			
			UM_Rows_Refresh();

		}
	}).disableSelection();

}

function UM_update_rows(){
	var c = 0;
	jQuery('a[data-remove_element="um-admin-drag-row"]').remove();
	jQuery('.um-admin-drag-row').each(function(){
		c++;
		row = jQuery(this);
		if ( c != 1 ) {
			row.find('.um-admin-drag-row-icons').append( '<a href="#" class="um-admin-tipsy-n" title="Delete Row" data-remove_element="um-admin-drag-row"><i class="um-faicon-trash-o"></i></a>' );
		}
	});
}

function UM_update_subrows(){
	jQuery('a[data-remove_element="um-admin-drag-rowsub"]').remove();
	jQuery('.um-admin-drag-row').each(function(){
		c = 0;
		jQuery(this).find('.um-admin-drag-rowsub').each(function(){
		c++;
		row = jQuery(this);
		if ( c != 1 ) {
			row.find('.um-admin-drag-rowsub-icons').append('<a href="#" class="um-admin-tipsy-n" title="Delete Row" data-remove_element="um-admin-drag-rowsub"><i class="um-faicon-trash-o"></i></a>');
		}
		});
	});
}

function UM_Change_Field_Col(){
	jQuery('.um-admin-drag-col .um-admin-drag-fld').each(function(){
		cols =  jQuery(this).parents('.um-admin-drag-rowsub').find('.um-admin-drag-col').length;
		col = jQuery(this).parents('.um-admin-drag-col');
		if ( col.hasClass('cols-last') ) {
			if ( cols == 1 ) {
				saved_col = 1;
			}
			if ( cols == 3 ) {
				saved_col = 3;
			} else if ( cols == 2 ) {
				saved_col = 2;
			}
		} else if ( col.hasClass('cols-middle') && cols == 3 ) {
			saved_col = 2;
		} else {
			saved_col = 1;
		}

		jQuery(this).data('column', saved_col);
	});
}

function UM_Change_Field_Grp(){
	jQuery('.um-admin-drag-col .um-admin-drag-fld:not(.um-field-type-group)').each(function(){
		if ( jQuery(this).parents('.um-admin-drag-group').length == 0 ){
			jQuery(this).data('group', '');
		} else {
			jQuery(this).data('group', jQuery(this).parents('.um-admin-drag-fld.um-field-type-group').data('key') );
		}
	});
}

function UM_Rows_Refresh(){

	jQuery('.um_update_order_fields').empty();
	
	/* ROWS */
	var c = 0;
	jQuery('.um-admin-drag-row').each(function(){
		c++;

		row = jQuery(this);

		col_num = '';
		row.find('.um-admin-drag-rowsub').each(function(){
		
			subrow = jQuery(this);
			
			subrow.find('.um-admin-drag-col').removeClass('cols-1 cols-2 cols-3 cols-last cols-middle');
			subrow.find('.um-admin-drag-col').addClass('cols-' + subrow.find('.um-admin-drag-col').length );
			subrow.find('.um-admin-drag-col:last').addClass('cols-last');
			if ( subrow.find('.um-admin-drag-col').length == 3 ) {subrow.find('.um-admin-drag-col:eq(1)').addClass('cols-middle');}
			
			if ( !col_num ) {
			col_num = subrow.find('.um-admin-drag-col').length;
			} else {
			col_num = col_num + ':' + subrow.find('.um-admin-drag-col').length;
			}
		
		});
		
		jQuery('.um_update_order_fields').append('<input type="hidden" name="_um_rowcols_'+c+'_cols" id="_um_rowcols_'+c+'_cols" value="'+col_num+'" />');
		
		sub_rows_count = row.find('.um-admin-drag-rowsub').length;
		
		var origin_id = jQuery(this).attr('data-original');

		jQuery('.um_update_order_fields').append('<input type="hidden" name="_um_row_'+c+'" id="_um_row_'+c+'" value="_um_row_'+c+'" />');
		jQuery('.um_update_order_fields').append('<input type="hidden" name="_um_roworigin_'+c+'_val" id="_um_roworigin_'+c+'_val" value="'+origin_id+'" />');
		jQuery('.um_update_order_fields').append('<input type="hidden" name="_um_rowsub_'+c+'_rows" id="_um_rowsub_'+c+'_rows" value="'+sub_rows_count+'" />');
		
		jQuery(this).attr('data-original', '_um_row_'+c );
	
	});

	/* FIELDS */
	var order;
	order = 0;
	jQuery('.um-admin-drag-col .um-admin-drag-fld').each(function(){
			
		if ( !jQuery(this).hasClass('group') ) {
			var group = jQuery(this).data('group');
			if ( group != '' ) {
			if ( jQuery('.um-admin-drag-fld.um-field-type-group.' + group ).find('.um-admin-drag-group').find( jQuery(this) ).length == 0 ) {
				jQuery(this).appendTo(  jQuery('.um-admin-drag-fld.um-field-type-group.' + group ).find('.um-admin-drag-group') );
			} else {
				//jQuery(this).prependTo(  jQuery('.um-admin-drag-fld.um-field-type-group.' + group ).find('.um-admin-drag-group') );
			}
			jQuery('.um_update_order_fields').append('<input type="hidden" name="um_group_'+jQuery(this).data('key')+'" id="um_group_'+jQuery(this).data('key')+'" value="'+group+'" />');
			} else {
			jQuery('.um_update_order_fields').append('<input type="hidden" name="um_group_'+jQuery(this).data('key')+'" id="um_group_'+jQuery(this).data('key')+'" value="" />');
			}
		}
		
		order++;

		row = jQuery(this).parents('.um-admin-drag-row').index()+1;
		row = '_um_row_'+row;
		
		saved_col = jQuery(this).data('column');
		
		if ( saved_col == 3 ){
			jQuery(this).appendTo( jQuery(this).parents('.um-admin-drag-rowsub').find('.um-admin-drag-col:eq(2)') );
		}
		if ( saved_col == 2 ){
			jQuery(this).appendTo( jQuery(this).parents('.um-admin-drag-rowsub').find('.um-admin-drag-col:eq(1)') );
		}

		sub_row = jQuery(this).parents('.um-admin-drag-rowsub').index();
				
		jQuery('.um_update_order_fields').append('<input type="hidden" name="um_position_'+jQuery(this).data('key')+'" id="um_position_'+jQuery(this).data('key')+'" value="'+order+'" />');
		
		jQuery('.um_update_order_fields').append('<input type="hidden" name="um_row_'+jQuery(this).data('key')+'" id="um_row_'+jQuery(this).data('key')+'" value="'+row+'" />');
		
		jQuery('.um_update_order_fields').append('<input type="hidden" name="um_subrow_'+jQuery(this).data('key')+'" id="um_subrow_'+jQuery(this).data('key')+'" value="'+sub_row+'" />');
		
		jQuery('.um_update_order_fields').append('<input type="hidden" name="um_col_'+jQuery(this).data('key')+'" id="um_col_'+jQuery(this).data('key')+'" value="'+saved_col+'" />');
		
	});
	
	UM_Drag_and_Drop();
	
	UM_Add_Icon();
	
	jQuery.ajax({
		url: ultimatemember_ajax_url,
		type: 'POST',
		data: jQuery('.um_update_order').serialize(),
		success: function(){
			jQuery('#publish').removeAttr('disabled');
		}
	});
	
}

function UM_Add_Icon(){

	var add_icon_html = '<a href="#" class="um-admin-drag-add-field um-admin-tipsy-n" title="Add Field" data-modal="UM_fields" data-modal-size="normal" data-dynamic-content="um_admin_show_fields" data-arg2="'+jQuery('.um-admin-drag-ajax').data('form_id')+'" data-arg1=""><i class="um-icon-plus"></i></a>';
		
	jQuery('.um-admin-drag-col').each(function(){
		if ( jQuery(this).find('.um-admin-drag-add-field').length == 0 ) {
			jQuery(this).append(add_icon_html);
		} else {
			jQuery(this).find('.um-admin-drag-add-field').remove();
			jQuery(this).append(add_icon_html); 
		}
	});
	
	jQuery('.um-admin-drag-group').each(function(){
		if ( jQuery(this).find('.um-admin-drag-add-field').length == 0 ) {
			jQuery(this).append(add_icon_html);
		} else {
			jQuery(this).find('.um-admin-drag-add-field').remove();
			jQuery(this).append(add_icon_html); 
		}
	});
	
}

jQuery(document).ready(function() {
	
	if ( !jQuery('.um-admin-drag').length ) return false;
	
	UM_Drag_and_Drop();

	/* add field to respected area */
	jQuery(document).on('click', 'a.um-admin-drag-add-field', function(){
		in_row = jQuery(this).parents('.um-admin-drag-row').index();
		in_sub_row = jQuery(this).parents('.um-admin-drag-rowsub').index();
		if ( jQuery(this).parents('.um-admin-drag-rowsub').find('.um-admin-drag-col').length == 1 ) {
			in_column = 1;
		} else {
			if ( jQuery(this).parents('.um-admin-drag-col').hasClass('cols-middle')){
				in_column = 2;
			} else if ( jQuery(this).parents('.um-admin-drag-col').hasClass('cols-last') ) {
				if ( jQuery(this).parents('.um-admin-drag-rowsub').find('.um-admin-drag-col').length == 3 ) {
				in_column = 3;
				} else {
				in_column = 2;
				}
			} else {
				in_column = 1;
			}
		}
		
		if ( jQuery(this).parents('.um-admin-drag-group').length ) {
			in_group = jQuery(this).parents('.um-admin-drag-fld.um-field-type-group').data('key');
		} else {
			in_group = '';
		}
		
		jQuery('.um-col-demon-settings').data('in_row', in_row);
		jQuery('.um-col-demon-settings').data('in_sub_row', in_sub_row);
		jQuery('.um-col-demon-settings').data('in_column', in_column);
		jQuery('.um-col-demon-settings').data('in_group', in_group);
	});
	
	/* add row */
	jQuery(document).on('click', '*[data-row_action="add_row"]', function(){
		var dragg = jQuery('.um-admin-drag-ajax');
		dragg.append( '<div class="um-admin-drag-row">' + jQuery('.um-col-demon-row').html() + '</div>' );
		dragg.find('.um-admin-drag-row:last').find('.um-admin-drag-row-icons').find('a.um-admin-drag-row-edit').attr('data-arg3', '_um_row_' + ( dragg.find('.um-admin-drag-row').length ) );
		dragg.find('.um-admin-drag-row:last').attr('data-original', '_um_row_' + ( dragg.find('.um-admin-drag-row').length ) );
		UM_update_rows();
		UM_update_subrows();
		UM_Rows_Refresh();
	});
	
	/* add sub row */
	jQuery(document).on('click', '*[data-row_action="add_subrow"]', function(){
		var dragg = jQuery(this).parents('.um-admin-drag-row').find('.um-admin-drag-rowsubs');
		dragg.append( '<div class="um-admin-drag-rowsub">' + jQuery('.um-col-demon-subrow').html() + '</div>' );
		UM_update_subrows();
		UM_Rows_Refresh();
	});
	
	/* remove element */
	jQuery(document).on('click', 'a[data-remove_element^="um-"]',function(){
		element = jQuery(this).data('remove_element');

		jQuery(this).parents('.' +element).find('.um-admin-drag-fld').each(function(){
			jQuery(this).find('a[data-silent_action="um_admin_remove_field"]').trigger('click');
		});
		
		jQuery(this).parents('.' +element).remove();
		jQuery('.tipsy').remove();
		UM_Rows_Refresh();
	});
	
	/* dynamically change columns */
	jQuery(document).on('click', '.um-admin-drag-ctrls.columns a', function(){
		
		var row = jQuery(this).parents('.um-admin-drag-rowsub');
		var tab = jQuery(this);
		var tabs = jQuery(this).parent();
		tabs.find('a').removeClass('active');
		tab.addClass('active');
		var existing_cols = row.find('.um-admin-drag-col').length;
		var required_cols = tab.data('cols');
		var needed_cols = required_cols - existing_cols;
					
		if ( needed_cols > 0 ) {
		
			for (i = 0; i < needed_cols; i++){
				row.find('.um-admin-drag-col-dynamic').append('<div class="um-admin-drag-col"></div>');
			}
			
			row.find('.um-admin-drag-col').removeClass('cols-1 cols-2 cols-3 cols-last cols-middle');
			row.find('.um-admin-drag-col').addClass('cols-' + row.find('.um-admin-drag-col').length );
			row.find('.um-admin-drag-col:last').addClass('cols-last');
			
			if ( row.find('.um-admin-drag-col').length == 3 ) {row.find('.um-admin-drag-col:eq(1)').addClass('cols-middle');}
		
		} else if ( needed_cols < 0 ) {
		
			needed_cols = needed_cols + 3;
			if ( needed_cols == 2 ) {
				row.find('.um-admin-drag-col:first').append( row.find('.um-admin-drag-col.cols-last').html() );
				row.find('.um-admin-drag-col.cols-last').remove();
			}
			if ( needed_cols == 1 ) {
				row.find('.um-admin-drag-col:first').append( row.find('.um-admin-drag-col.cols-last').html() );
				row.find('.um-admin-drag-col:first').append( row.find('.um-admin-drag-col.cols-middle').html() );
				row.find('.um-admin-drag-col.cols-last').remove();
				row.find('.um-admin-drag-col.cols-middle').remove();
			}
		
			row.find('.um-admin-drag-col').removeClass('cols-1 cols-2 cols-3 cols-last cols-middle');
			row.find('.um-admin-drag-col').addClass('cols-' + row.find('.um-admin-drag-col:visible').length );
			row.find('.um-admin-drag-col:last').addClass('cols-last');
			
		}
		
		if ( allow_update_via_col_click == true ) {
			UM_Change_Field_Col();
			UM_Rows_Refresh();
		}

	});
	
	/* trigger columns at start */
	allow_update_via_col_click = false;
	jQuery('.um-admin-drag-ctrls.columns a.active').each(function(){
		jQuery(this).trigger('click');
	}).promise().done( function(){ allow_update_via_col_click = true; } );
	
	UM_Rows_Refresh();

});