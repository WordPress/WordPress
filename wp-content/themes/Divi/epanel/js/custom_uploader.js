jQuery(document).ready(function() {
	var et_file_frame;

	jQuery( '.upload_image_button' ).click(function( event ) {
		var this_el = jQuery( this ),
			use_for = this_el.parents( '.epanel-box' ).find( '.box-title > h3' ).text(),
			button_text = this_el.data( 'button_text' ),
			window_title = epanel_uploader.media_window_title,
			fileInput = this_el.parent().prev('input.uploadfield');

			event.preventDefault();

			et_file_frame = wp.media.frames.et_file_frame = wp.media({
				title: window_title,
				library: {
					type: 'image'
				},
				button: {
					text: button_text,
				},
				multiple: false
			});

			et_file_frame.on( 'select', function() {
				var attachment = et_file_frame.state().get( 'selection' ).first().toJSON();
				fileInput.val( attachment.url );
			});

			et_file_frame.open();

		return false;
	});

	jQuery( '.upload_image_reset' ).click( function() {
		jQuery(this).parent().prev( 'input.uploadfield' ).val( '' );
	});
});