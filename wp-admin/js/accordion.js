jQuery(document).ready( function($) {
	// Expand/Collapse
	$('.accordion-container').on( 'click keydown', '.accordion-section-title', function(e) {
		if ( e.type === 'keydown' && 13 !== e.which ) // "return" key
				return;
		e.preventDefault(); // Keep this AFTER the key filter above

		var section = $( this ).closest( '.accordion-section' );

		if ( section.hasClass('cannot-expand') )
			return;

		section.siblings( '.open' ).removeClass( 'open' );
		section.toggleClass( 'open' );
	});
});
