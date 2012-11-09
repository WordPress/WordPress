(function($) {
	// Fetch available headers and apply jQuery.masonry
	// once the images have loaded.
	$( function() {
		var $headers = $('.available-headers');

		$headers.imagesLoaded( function() {
			$headers.masonry({
				itemSelector: '.default-header'
			});
		});
	});
}(jQuery));
