(function($) {

	var headers = $('.available-headers');
	headers.imagesLoaded( function() {
		headers.masonry({
			itemSelector: '.default-header'
		});
	});

})(jQuery);
