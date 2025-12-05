( function( $ ) {
	$( function() {
		var supportHtml5 = ( function() {
			var features = {};
			var input = document.createElement( 'input' );
			var inputTypes = [ 'date' ];

			$.each( inputTypes, function( index, value ) {
				input.setAttribute( 'type', value );
				features[ value ] = input.type !== 'text';
			} );

			return features;
		} )();

		if ( ! supportHtml5.date ) {
			$( 'input.wpcf7-date[type="date"]' ).each( function() {
				$( this ).datepicker( {
					dateFormat: 'yy-mm-dd',
					minDate: new Date( $( this ).attr( 'min' ) ),
					maxDate: new Date( $( this ).attr( 'max' ) )
				} );
			} );
		}
	} );
} )( jQuery );
