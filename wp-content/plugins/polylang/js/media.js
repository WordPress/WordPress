// when clicking on attach link, filters find post list per media language
(function( $ ){
	$.ajaxPrefilter(function ( options, originalOptions, jqXHR ) {
		options.data = 'pll_post_id=' + $( '#affected' ).val() + '&' + options.data;
	});
})( jQuery )
