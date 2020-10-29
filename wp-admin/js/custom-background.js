/**
 * @output wp-admin/js/custom-background.js
 */

/* global ajaxurl */

/**
 * Registers all events for customizing the background.
 *
 * @since 3.0.0
 *
 * @requires jQuery
 */
(function($) {
	$(document).ready(function() {
		var frame,
			bgImage = $( '#custom-background-image' );

		/**
		 * Instantiates the WordPress color picker and binds the change and clear events.
		 *
		 * @since 3.5.0
		 *
		 * @return {void}
		 */
		$('#background-color').wpColorPicker({
			change: function( event, ui ) {
				bgImage.css('background-color', ui.color.toString());
			},
			clear: function() {
				bgImage.css('background-color', '');
			}
		});

		/**
		 * Alters the background size CSS property whenever the background size input has changed.
		 *
		 * @since 4.7.0
		 *
		 * @return {void}
		 */
		$( 'select[name="background-size"]' ).change( function() {
			bgImage.css( 'background-size', $( this ).val() );
		});

		/**
		 * Alters the background position CSS property whenever the background position input has changed.
		 *
		 * @since 4.7.0
		 *
		 * @return {void}
		 */
		$( 'input[name="background-position"]' ).change( function() {
			bgImage.css( 'background-position', $( this ).val() );
		});

		/**
		 * Alters the background repeat CSS property whenever the background repeat input has changed.
		 *
		 * @since 3.0.0
		 *
		 * @return {void}
		 */
		$( 'input[name="background-repeat"]' ).change( function() {
			bgImage.css( 'background-repeat', $( this ).is( ':checked' ) ? 'repeat' : 'no-repeat' );
		});

		/**
		 * Alters the background attachment CSS property whenever the background attachment input has changed.
		 *
		 * @since 4.7.0
		 *
		 * @return {void}
		 */
		$( 'input[name="background-attachment"]' ).change( function() {
			bgImage.css( 'background-attachment', $( this ).is( ':checked' ) ? 'scroll' : 'fixed' );
		});

		/**
		 * Binds the event for opening the WP Media dialog.
		 *
		 * @since 3.5.0
		 *
		 * @return {void}
		 */
		$('#choose-from-library-link').click( function( event ) {
			var $el = $(this);

			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}

			// Create the media frame.
			frame = wp.media.frames.customBackground = wp.media({
				// Set the title of the modal.
				title: $el.data('choose'),

				// Tell the modal to show only images.
				library: {
					type: 'image'
				},

				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: $el.data('update'),
					/*
					 * Tell the button not to close the modal, since we're
					 * going to refresh the page when the image is selected.
					 */
					close: false
				}
			});

			/**
			 * When an image is selected, run a callback.
			 *
			 * @since 3.5.0
			 *
			 * @return {void}
 			 */
			frame.on( 'select', function() {
				// Grab the selected attachment.
				var attachment = frame.state().get('selection').first();
				var nonceValue = $( '#_wpnonce' ).val() || '';

				// Run an Ajax request to set the background image.
				$.post( ajaxurl, {
					action: 'set-background-image',
					attachment_id: attachment.id,
					_ajax_nonce: nonceValue,
					size: 'full'
				}).done( function() {
					// When the request completes, reload the window.
					window.location.reload();
				});
			});

			// Finally, open the modal.
			frame.open();
		});
	});
})(jQuery);
