/**
 * @output wp-admin/js/custom-header.js
 */

/* global isRtl */

/**
 * Initializes the custom header selection page.
 *
 * @since 3.5.0
 *
 * @deprecated 4.1.0 The page this is used on is never linked to from the UI.
 *             Setting a custom header is completely handled by the Customizer.
 */
(function($) {
	var frame;

	$( function() {
		// Fetch available headers.
		var $headers = $('.available-headers');

		// Apply jQuery.masonry once the images have loaded.
		$headers.imagesLoaded( function() {
			$headers.masonry({
				itemSelector: '.default-header',
				isRTL: !! ( 'undefined' != typeof isRtl && isRtl )
			});
		});

		/**
		 * Opens the 'choose from library' frame and creates it if it doesn't exist.
		 *
		 * @since 3.5.0
		 * @deprecated 4.1.0
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
			frame = wp.media.frames.customHeader = wp.media({
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
					// Tell the button not to close the modal, since we're
					// going to refresh the page when the image is selected.
					close: false
				}
			});

			/**
			 * Updates the window location to include the selected attachment.
			 *
			 * @since 3.5.0
			 * @deprecated 4.1.0
			 *
			 * @return {void}
			 */
			frame.on( 'select', function() {
				// Grab the selected attachment.
				var attachment = frame.state().get('selection').first(),
					link = $el.data('updateLink');

				// Tell the browser to navigate to the crop step.
				window.location = link + '&file=' + attachment.id;
			});

			frame.open();
		});
	});
}(jQuery));
