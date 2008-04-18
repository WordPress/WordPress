function checkAll(jQ) { // use attr( checked, fn )
	jQuery(jQ).find( 'tbody :checkbox' ).attr( 'checked', function() {
		return jQuery(this).attr( 'checked' ) ? '' : 'checked';
	} );
}

jQuery( function($) {
	var lastClicked = false;
	$( 'tbody :checkbox' ).click( function(e) {
		if ( 'undefined' == e.shiftKey ) { return true; }
		if ( e.shiftKey ) {
			if ( !lastClicked ) { return true; }
			var checks = $( lastClicked ).parents( 'form:first' ).find( ':checkbox' );
			var first = checks.index( lastClicked );
			var last = checks.index( this );
			if ( 0 < first && 0 < last && first != last ) {
				checks.slice( first, last ).attr( 'checked', $( this ).is( ':checked' ) ? 'checked' : '' );
			}
		}
		lastClicked = this;
		return true;
	} );
	$( 'thead :checkbox' ).click( function() {
		checkAll( $(this).parents( 'form:first' ) );
	} );
} );