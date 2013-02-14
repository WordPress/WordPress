jQuery(document).ready( function($) {
	// Expand/Collapse
	$('.accordion-section-title').on('click keydown', function( event ) {

		if ( event.type === 'keydown' &&  13 !== event.which ) // enter
				return;

		var clicked = $( this ).closest( '.accordion-section' );

		if ( clicked.hasClass('cannot-expand') )
			return;

		clicked.closest( '.accordion-container' ).find( '.accordion-section' ).not( clicked ).removeClass( 'open' );
		clicked.toggleClass( 'open' );
		event.preventDefault();
	});
});
jQuery(document).ready( function($) {
	// Expand/Collapse
	$('.accordion-section-title').on('click keydown', function( event ) {

		if ( event.type === 'keydown' &&  13 !== event.which ) // enter
				return;

		var clicked = $( this ).closest( '.accordion-section' );

		if ( clicked.hasClass('cannot-expand') )
			return;

		clicked.closest( '.accordion-container' ).find( '.accordion-section' ).not( clicked ).removeClass( 'open' );
		clicked.toggleClass( 'open' );
		event.preventDefault();
	});
});
jQuery(document).ready( function($) {
	// Expand/Collapse
	$('.accordion-section-title').on('click keydown', function( event ) {

		if ( event.type === 'keydown' &&  13 !== event.which ) // enter
				return;

		var clicked = $( this ).closest( '.accordion-section' );

		if ( clicked.hasClass('cannot-expand') )
			return;

		clicked.closest( '.accordion-container' ).find( '.accordion-section' ).not( clicked ).removeClass( 'open' );
		clicked.toggleClass( 'open' );
		event.preventDefault();
	});
});