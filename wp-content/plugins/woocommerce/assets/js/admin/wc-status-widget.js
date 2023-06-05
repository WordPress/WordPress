/*global jQuery */
(function( $ ) {
	$( document ).on( 'ready', function() {
		window.wcTracks.recordEvent( 'wcadmin_status_widget_view' );
	});

	var recordEvent = function( link ) {
		window.wcTracks.recordEvent( 'status_widget_click', {
			link: link
		} );
	};

	$( '.sales-this-month a' ).on( 'click', function() {
		recordEvent( 'net-sales' );
	});

	$( '.best-seller-this-month a' ).on( 'click', function() {
		recordEvent( 'best-seller-this-month' );
	});

	$( '.processing-orders a' ).on( 'click', function() {
		recordEvent( 'orders-processing' );
	});

	$( '.on-hold-orders a' ).on( 'click', function() {
		recordEvent( 'orders-on-hold' );
	});

	$( '.low-in-stock a' ).on( 'click', function() {
	   recordEvent( 'low-stock' );
	});

	$( '.out-of-stock a' ).on( 'click', function() {
		recordEvent( 'out-of-stock' );
	});
})( jQuery );
