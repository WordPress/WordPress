(function($) {
	var frame;

	$( function() {
		// Build the choose from library frame.
		$( '#choose-from-library-link' ).on( 'click', function( event ) {
			var $el = $(this);
			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}

			// Create the media frame.
			frame = wp.media({
				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: $el.data('update'),
					// Tell the button not to close the modal, since we're
					// going to refresh the page when the image is selected.
					close: false
				},
				states: [
					new wp.media.controller.Library({
						title: $el.data( 'choose' ),
						library: wp.media.query({ type: 'image' }),
						date: false,
						suggestedWidth: $el.data( 'size' ),
						suggestedHeight: $el.data( 'size' )
					})
				]
			});

			// When an image is selected, run a callback.
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
