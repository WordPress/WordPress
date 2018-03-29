function um_admin_live_update_scripts() {

	jQuery('.um-admin-modal-body:visible select').select2({
		allowClear: false,
		minimumResultsForSearch: 10
	});

	jQuery('.um-adm-conditional').each(function(){jQuery(this).trigger('change');});
	
	jQuery('.um-admin-colorpicker').wpColorPicker();
	
}

function um_admin_new_modal( id, ajax, size ){
	
	var modal = jQuery('body').find('.um-admin-overlay');
	
	jQuery('.tipsy').hide();
	
	um_admin_remove_modal();
		
	jQuery('body').addClass('um-admin-modal-open').append('<div class="um-admin-overlay" /><div class="um-admin-modal" />');
	jQuery('#' + id).prependTo('.um-admin-modal');
	jQuery('#' + id).show();
	jQuery('.um-admin-modal').show();
	
	jQuery('.um-admin-modal-head').append('<a href="#" data-action="UM_remove_modal" class="um-admin-modal-close"><i class="um-faicon-times"></i></a>');

	if ( ajax == true ) {
		um_admin_modal_size( size );
		um_admin_modal_preload();
		um_admin_modal_responsive();
	} else {
		um_admin_modal_responsive();
	}
	
}

function um_admin_modal_ajaxcall( act_id, arg1, arg2, arg3 ) {
	
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
	
	jQuery.ajax({
		url: ultimatemember_ajax_url,
		type: 'POST',
		data: {action: 'ultimatemember_dynamic_modal_content', act_id: act_id, arg1 : arg1, arg2 : arg2, arg3: arg3, in_row: in_row, in_sub_row: in_sub_row, in_column: in_column, in_group: in_group },
		complete: function(){
			um_admin_modal_loaded();
			um_admin_modal_responsive();
		},
		success: function(data){

			jQuery('.um-admin-modal').find('.um-admin-modal-body').html( data );
			
			um_responsive();
			
			um_admin_live_update_scripts();
			
			if ( jQuery('.um-admin-editor:visible').length > 0 ) {
			
				if ( act_id == 'um_admin_edit_field_popup' ) {
				
					tinyMCE.execCommand('mceRemoveEditor', true, 'um_editor_edit');
					jQuery('.um-admin-editor:visible').html( jQuery('.um-hidden-editor-edit').contents() );
					tinyMCE.execCommand('mceAddEditor', true, 'um_editor_edit');
					
					jQuery('.switch-html').trigger('click');
					jQuery('.switch-html').trigger('click');
					jQuery('.switch-tmce').trigger('click');
					
					jQuery('#um_editor_edit_ifr').height(200);

					var editor = tinyMCE.get('um_editor_edit');
					var content = editor.getContent();
					editor.setContent( jQuery('.um-admin-modal:visible .dynamic-mce-content').html() );
					
				} else {
				
					tinyMCE.execCommand('mceRemoveEditor', true, 'um_editor_new');
					jQuery('.um-admin-editor:visible').html( jQuery('.um-hidden-editor-new').contents() );
					tinyMCE.execCommand('mceAddEditor', true, 'um_editor_new');
					
					jQuery('.switch-html').trigger('click');
					jQuery('.switch-html').trigger('click');
					jQuery('.switch-tmce').trigger('click');
					
					jQuery('#um_editor_new_ifr').height(200);
					
				}
				
			}
			
		},
		error: function(data){

		}
	});
	return false;
}

function um_admin_modal_responsive() {
	var required_margin = jQuery('.um-admin-modal:visible').innerHeight() / 2 + 'px';
	jQuery('.um-admin-modal:visible').css({'margin-top': '-' + required_margin });
}

function um_admin_remove_modal(){

	if ( jQuery('.um-admin-editor:visible').length > 0 ) {
	
		if ( jQuery('.um-admin-modal:visible').find('form').parent().attr('id') == 'UM_edit_field' ) {
		
			tinyMCE.execCommand('mceRemoveEditor', true, 'um_editor_edit');
			jQuery('.um-hidden-editor-edit').html( jQuery('.um-admin-editor:visible').contents() );
			tinyMCE.execCommand('mceAddEditor', true, 'um_editor_edit');
		
		} else {
		
			tinyMCE.execCommand('mceRemoveEditor', true, 'um_editor_new');
			jQuery('.um-hidden-editor-new').html( jQuery('.um-admin-editor:visible').contents() );
			tinyMCE.execCommand('mceAddEditor', true, 'um_editor_new');
		
		}
				
	}
			
	jQuery('body').removeClass('um-admin-modal-open');
	jQuery('.um-admin-modal div[id^="UM_"]').hide().appendTo('body');
	jQuery('.um-admin-modal,.um-admin-overlay').remove();
}

function um_admin_modal_preload() {
	jQuery('.um-admin-modal:visible').addClass('loading');
	jQuery('.um-admin-modal-body:visible').empty();
}

function um_admin_modal_loaded() {
	jQuery('.um-admin-modal:visible').removeClass('loading');
}

function um_admin_modal_size( aclass ) {
	jQuery('.um-admin-modal:visible').addClass(aclass);
}

function um_admin_modal_add_attr( id, value ) {
	jQuery('.um-admin-modal:visible').data( id, value );
}

/**
	Custom modal scripting starts
**/

jQuery(document).ready(function() {
	
	/**
		disable link
	**/
	jQuery(document).on('click', '.um-admin-builder a, .um-admin-modal a', function(e){
		e.preventDefault();
		return false;
	});
	
	/**
		toggle area
	**/
	jQuery(document).on('click', '.um-admin-btn-toggle a', function(e){
		var content = jQuery(this).parent().find('.um-admin-btn-content');
		var link = jQuery(this);
		if ( content.is(':hidden') ) {
			content.show();
			link.find('i').removeClass().addClass('um-icon-minus');
			link.addClass('active');
		} else {
			content.hide();
			link.find('i').removeClass().addClass('um-icon-plus');
			link.removeClass('active');
		}
		um_admin_modal_responsive();
	});
	
	/**
		clone a condition
	**/
	jQuery(document).on('click', '.um-admin-new-condition', function(){
		var content = jQuery(this).parents('.um-admin-btn-content');
		content.find('select').select2('destroy');
		var length = content.find('.um-admin-cur-condition').length;
		if ( length < 5 ) {
		var cloned = jQuery(this).parents('.um-admin-cur-condition').clone();
		cloned.find('input[type=text],select').each(function(){
			jQuery(this).attr('id', jQuery(this).attr('id') + length );
			jQuery(this).attr('name', jQuery(this).attr('name') + length );
		});
		cloned.find('input[type=text]').val('');
		cloned.find('.um-admin-new-condition').replaceWith('<p><a href="#" class="um-admin-remove-condition button um-admin-tipsy-n" title="Remove condition"><i class="um-icon-close" style="margin-right:0!important"></i></a></p>');
		
		cloned.appendTo( content );
		cloned.find('select').val('');
		um_admin_live_update_scripts();
		um_admin_modal_responsive();
		} else {
			alert('You already have 5 rules');
		}
	});
	
	/**
		reset conditions
	**/
	jQuery(document).on('click', '.um-admin-reset-conditions a', function(){
		var content = jQuery(this).parents('.um-admin-btn-content');
		content.find('.um-admin-cur-condition').slice(1).remove();
		content.find('input[type=text]').val('');
		content.find('select').val('');
		um_admin_live_update_scripts();
		um_admin_modal_responsive();
	});
	
	/**
		remove a condition
	**/
	jQuery(document).on('click', '.um-admin-remove-condition', function(){
		var condition = jQuery(this).parents('.um-admin-cur-condition');
		jQuery('.tipsy').remove();
		condition.remove();
		um_admin_live_update_scripts();
		um_admin_modal_responsive();
	});
	
	/**
		remove modal via action
	**/
	jQuery(document).on('click', '.um-admin-overlay, a[data-action="UM_remove_modal"]', function(){
		um_admin_remove_modal();
	});
	
	/**
		fire new modal
	**/
	jQuery(document).on('click', 'a[data-modal^="UM_"], span[data-modal^="UM_"]', function(e){
		
		e.preventDefault();

		var modal_id = jQuery(this).attr('data-modal');

		if ( jQuery(this).attr('data-back') ) {
		
			jQuery('#UM_fonticons').find('a.um-admin-modal-back').attr("data-modal", jQuery(this).attr('data-back') );
			var current_icon = jQuery( '#' + jQuery(this).attr('data-back') ).find('input#_icon').val();
			if ( current_icon == '' ) {
				jQuery('#UM_fonticons').find('.um-admin-icons span').removeClass('highlighted');
			}
		
		}
		
		if ( jQuery(this).data('dynamic-content') ) {
			um_admin_new_modal( modal_id, true, jQuery(this).data('modal-size') );
			um_admin_modal_ajaxcall( jQuery(this).data('dynamic-content'), jQuery(this).data('arg1'), jQuery(this).data('arg2'), jQuery(this).data('arg3') );
		} else {
			um_admin_new_modal( modal_id );
		}
		
		return false;

	});
	
	/**
		choose font icon
	**/
	jQuery(document).on('click', '.um-admin-icons span', function(){
		var icon = jQuery(this).attr('data-code');
		jQuery(this).parent().find('span').removeClass('highlighted');
		jQuery(this).addClass('highlighted');
		jQuery('#UM_fonticons').find('a.um-admin-modal-back').attr("data-code", icon);
	});
	
	/**
		submit font icon
	**/
	jQuery(document).on('click', '#UM_fonticons a.um-admin-modal-back:not(.um-admin-modal-cancel)', function(){
		var v_id = '';
		var icon_selected = jQuery(this).attr('data-code');
		if (icon_selected != ''){
			if ( jQuery(this).attr('data-modal') ) {
				v_id = '#' + jQuery(this).attr('data-modal');
			} else {
				v_id = '.postbox';
			}
			jQuery( v_id ).find('input#_icon,input#_um_icon').val( icon_selected );
			jQuery( v_id ).find('span.um-admin-icon-value').html('<i class="'+icon_selected+'"></i>');
			jQuery( v_id ).find('.um-admin-icon-clear').show();
		}
		jQuery(this).attr('data-code', '');
		if ( v_id == '.postbox' ) {
			um_admin_remove_modal();
		}
	});
	
	/**
		restore font icon
	**/
	jQuery(document).on('click', 'span.um-admin-icon-clear', function(){
		var element = jQuery(this).parents('p');
		jQuery('#UM_fonticons a.um-admin-modal-back').attr('data-code', '');
		element.find('input[type=hidden]').val('');
		element.find('.um-admin-icon-value').html('No Icon');
		jQuery(this).hide();
	});
	
	/**
		search font icons
	**/
	jQuery(document).on('keyup blur', '#_icon_search', function(){
		if ( jQuery(this).val().toLowerCase() != '' ) {
			jQuery('.um-admin-icons span').hide();
			jQuery('.um-admin-icons span[data-code*="'+jQuery(this).val().toLowerCase()+'"]').show();
		} else {
			jQuery('.um-admin-icons span:hidden').show();
		}
		um_admin_modal_responsive();
	});

	
	/**
	 * Retrieve options from a callback function
	 */
	jQuery(document).on('blur',"#_custom_dropdown_options_source", function(){
        var me = jQuery(this);
        var _options = jQuery('textarea[id=_options]');
        
        if( me.val() != '' ){
        	var um_option_callback = me.val();
          	jQuery.ajax({
				url: ultimatemember_ajax_url,
				type: 'POST',
				data: {action: 'ultimatemember_populate_dropdown_options', um_option_callback: um_option_callback },
				complete: function(){
					
				},
				success: function( response ){
					var arr_opts = [];
					
					for (var key in response.data ){
                         arr_opts.push( response.data[ key ] );
					}

					_options.val( arr_opts.join('\n') );
					
		        }
			});
		}

	});

}); // end jQuery(document).ready

