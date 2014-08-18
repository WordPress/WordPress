jQuery( function ( $ ) {
	var ak_js = $( '#ak_js' );

	// If the form field already exists just use that
	if ( ak_js.length == 0 ) {
		ak_js = $( '<input type="hidden" id="ak_js" name="ak_js" />' );
	}
	else {
		ak_js.remove();
	}

	ak_js.val( ( new Date() ).getTime() );

	// single page, front-end comment form
	// inline comment reply, wp-admin
	$( '#commentform, #replyrow td:first' ).append( ak_js );
} );
