(function($) {
	$(document).ready( function() {
	    $('.feature-slider a').on( 'click', function(e) {
	        $('.featured-posts section.featured-post').css({
	            opacity: 0,
	            visibility: 'hidden'
	        });
	        $(this.hash).css({
	            opacity: 1,
	            visibility: 'visible'
	        });
	        $('.feature-slider a').removeClass('active');
	        $(this).addClass('active');
	        e.preventDefault();
	    });
	});
})(jQuery);
