jQuery(document).ready( function($) {
	// Expand/Collapse
	$('.accordion-container').on( 'click keydown', '.accordion-section-title', function(e) {
		if ( e.type === 'keydown' && 13 !== e.which ) // "return" key
				return;
		e.preventDefault(); // Keep this AFTER the key filter above

		var section = $( this ).closest( '.accordion-section' ),
		    siblings = section.siblings( '.open' ),
		    content = section.find( '.accordion-section-content' );

		if ( section.hasClass('cannot-expand') )
			return;

		siblings.removeClass( 'open' );
		siblings.find( '.accordion-section-content' ).show().slideUp( 150 );
		content.toggle( section.hasClass( 'open' ) ).slideToggle( 150 );
		section.toggleClass( 'open' );
	});
});
