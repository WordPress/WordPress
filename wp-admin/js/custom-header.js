(function($) {
	var frame;

	$( function() {
		// Fetch available headers and apply jQuery.masonry
		// once the images have loaded.
		var $headers = $('.available-headers');

		$headers.imagesLoaded( function() {
			$headers.masonry({
				itemSelector: '.default-header'
			});
		});

		// Build the choose from library frame.
		$('#choose-from-library-link').click( function( event ) {
			var $el = $(this);
			event.preventDefault();

			frame = wp.media({
				title:     $el.data('choose'),
				library:   {
					type: 'image'
				}
			});

			frame.toolbar.on( 'activate:select', function() {
				frame.toolbar.view().set({
					select: {
						style: 'primary',
						text:  $el.data('update'),

						click: function() {
							var attachment = frame.state().get('selection').first(),
								link = $el.data('updateLink');

							window.location = link + '&file=' + attachment.id;
						}
					}
				});
			});

			frame.setState('library');
		});
	});
}(jQuery));
