( function( $ ){
	// Hook into background color change and adjust body class value as needed.
	wp.customize( 'background_color', function( value ) {
		var body = $( 'body' );
		value.bind( function( to ) {
			if ( '#ffffff' == to || '#fff' == to || '' == to )
				body.addClass( 'custom-background-white' );
			else if ( '' == to )
				body.addClass( 'custom-background-empty' );
			else
				body.removeClass( 'custom-background-empty custom-background-white' );
		} );
	} );
} )( jQuery );