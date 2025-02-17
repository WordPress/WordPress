( function( $ ){
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '#site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '#site-description' ).text( to );
		} );
	} );

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '#site-title, #site-title a, #site-description' ).css( {
					'clip-path': 'inset(50%)',
					'position': 'absolute'
				} );
			} else {
				$( '#site-title, #site-title a, #site-description' ).css( {
					'clip-path': 'none',
					'color': to,
					'position': 'relative'
				} );
			}
		} );
	} );
} )( jQuery );
