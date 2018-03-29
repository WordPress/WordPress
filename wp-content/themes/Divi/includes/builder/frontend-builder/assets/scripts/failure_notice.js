jQuery( 'body' ).on( 'click', '.et_builder_increase_memory', function() {
	var $this_button = jQuery(this);

	jQuery.ajax({
		type: "POST",
		dataType: 'json',
		url: et_fb_options.ajaxurl,
		data: {
			action : 'et_pb_increase_memory_limit',
			et_admin_load_nonce : et_fb_options.et_admin_load_nonce
		},
		success: function( data ) {
			if ( ! _.isUndefined( data.success ) ) {
				$this_button.addClass( 'et_builder_modal_action_button_success' ).text( et_fb_options.memory_limit_increased );
			} else {
				$this_button.addClass( 'et_builder_modal_action_button_fail' ).prop( 'disabled', true ).text( et_fb_options.memory_limit_not_increased );
			}
		}
	});

	return false;
} );

jQuery( 'body' ).on( 'click', '.et-builder-timeout .et-core-modal-action', function() {
	location.reload();

	return false;
} );

// disable Visual Builder on Close button
jQuery( 'body' ).on( 'click', '.et-builder-timeout .et-core-modal-close, .et-builder-timeout', function() {

	var page_href = jQuery( '#wp-admin-bar-et-disable-visual-builder a' ).attr('href');
	location.assign( page_href );

	return false;
} );
