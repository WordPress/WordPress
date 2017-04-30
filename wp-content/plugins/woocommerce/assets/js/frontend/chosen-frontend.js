jQuery( function( $ ) {

	// Frontend Chosen selects
	$( 'select.country_select, select.state_select' ).chosen( { search_contains: true } );

	$( 'body' ).bind( 'country_to_state_changed', function() {
		$( 'select.state_select' ).chosen().trigger( 'chosen:updated' );
	});

});
