( function( $ ){

	$( document ).ready( function () {

		// Expand/Collapse on click
		$( '.accordion-container' ).on( 'click keydown', '.accordion-section-title', function( e ) {
			if ( e.type === 'keydown' && 13 !== e.which ) { // "return" key
				return;
			}

			e.preventDefault(); // Keep this AFTER the key filter above

			accordionSwitch( $( this ) );
		});

		// Back to top level
		$( '.accordion-container' ).on( 'click keydown', '.control-panel-back', function( e ) {
			if ( e.type === 'keydown' && 13 !== e.which ) { // "return" key
				return;
			}

			e.preventDefault(); // Keep this AFTER the key filter above

			panelSwitch( $( this ) );
		});

		// Re-initialize accordion when screen options are toggled
		$( '.hide-postbox-tog' ).click( function () {
			accordionInit();
		});

	});

	var accordionOptions = $( '.accordion-container li.accordion-section' ),
		sectionContent   = $( '.accordion-section-content' );

	function accordionInit () {
		// Rounded corners
		accordionOptions.removeClass( 'top bottom' );
		accordionOptions.filter( ':visible' ).first().addClass( 'top' );
		accordionOptions.filter( ':visible' ).last().addClass( 'bottom' ).find( sectionContent ).addClass( 'bottom' );
	}

	function accordionSwitch ( el ) {
		var section = el.closest( '.accordion-section' ),
			siblings = section.closest( '.accordion-container' ).find( '.open' ),
			content = section.find( sectionContent );

		if ( section.hasClass( 'cannot-expand' ) ) {
			return;
		}

		if ( section.hasClass( 'control-panel' ) ) {
			panelSwitch( section );
			return;
		}

		if ( section.hasClass( 'open' ) ) {
			section.toggleClass( 'open' );
			content.toggle( true ).slideToggle( 150 );
		} else {
			siblings.removeClass( 'open' );
			siblings.find( sectionContent ).show().slideUp( 150 );
			content.toggle( false ).slideToggle( 150 );
			section.toggleClass( 'open' );
		}

		accordionInit();
	}

	function panelSwitch( panel ) {
		var position,
			section = panel.closest( '.accordion-section' ),
			container = section.closest( '.wp-full-overlay' ),
			siblings = container.find( '.accordion-section.open' ),
			content = section.find( '.control-panel-content' );

		if ( section.hasClass( 'current-panel' ) ) {
			section.toggleClass( 'current-panel' );
			container.toggleClass( 'in-sub-panel' );
			content.delay( 180 ).hide( 0, function() {
				content.css( 'margin-top', 'inherit' ); // Reset
			} );
		} else {
			siblings.removeClass( 'open' );
			content.show( 0, function() {
				position = content.offset().top;
				content.css( 'margin-top', ( 45 - position ) );
				section.toggleClass( 'current-panel' );
				container.toggleClass( 'in-sub-panel' );
			} );
		}
	}

	// Initialize the accordion (currently just corner fixes)
	accordionInit();

})(jQuery);
