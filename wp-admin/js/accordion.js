( function( $ ){

	$( document ).ready( function () {

		// Expand/Collapse on click
		$( '.accordion-container' ).on( 'click keydown', '.accordion-section-title', function( e ) {
			if ( e.type === 'keydown' && 13 !== e.which ) // "return" key
					return;
			e.preventDefault(); // Keep this AFTER the key filter above

			accordionSwitch( $( this ) );
			accordionCorners();
		});

		// Refresh selected accordion option when screen options are toggled
		$( '.hide-postbox-tog' ).click( function () {
			accordionInit();
		});

	});

	var accordionOptions = $( '.accordion-container li.accordion-section' ),
		sectionContent   = $( '.accordion-section-content' );

	// Rounded corners
	function accordionCorners () {
		accordionOptions.removeClass( 'top bottom' );
		accordionOptions.filter( ':visible' ).first().addClass( 'top' );
		accordionOptions.filter( ':visible' ).last().addClass( 'bottom' ).find( sectionContent ).addClass('bottom');
	};

	function accordionInit () {
		accordionSwitch( accordionOptions.filter( ':visible' ).first() );
		accordionCorners();
	}

	function accordionSwitch ( el ) {
		var section = el.closest( '.accordion-section' ),
		    siblings = section.parent().find( '.open' ),
		    content = section.find( sectionContent );

		if ( section.hasClass( 'cannot-expand' ) )
			return;

		siblings.removeClass( 'open' );
		siblings.find( sectionContent ).show().slideUp( 150 );
		content.toggle( section.hasClass( 'open' ) ).slideToggle( 150 );
		section.toggleClass( 'open' );
	}

	// Show the first accordion option by default
	accordionInit();

})(jQuery);
