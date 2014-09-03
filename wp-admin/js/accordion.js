/**
 * Accordion-folding functionality.
 *
 * Markup with the appropriate classes will be automatically hidden,
 * with one section opening at a time when its title is clicked.
 * Use the following markup structure for accordion behavior:
 *
 * <div class="accordion-container">
 *	<div class="accordion-section open">
 *		<h3 class="accordion-section-title"></h3>
 *		<div class="accordion-section-content">
 *		</div>
 *	</div>
 *	<div class="accordion-section">
 *		<h3 class="accordion-section-title"></h3>
 *		<div class="accordion-section-content">
 *		</div>
 *	</div>
 *	<div class="accordion-section">
 *		<h3 class="accordion-section-title"></h3>
 *		<div class="accordion-section-content">
 *		</div>
 *	</div>
 * </div>
 *
 * Note that any appropriate tags may be used, as long as the above classes are present.
 *
 * In addition to the standard accordion behavior, this file includes JS for the
 * Customizer's "Panel" functionality.
 *
 * @since 3.6.0.
 */

( function( $ ){

	$( document ).ready( function () {

		// Expand/Collapse accordion sections on click.
		$( '.accordion-container' ).on( 'click keydown', '.accordion-section-title', function( e ) {
			if ( e.type === 'keydown' && 13 !== e.which ) { // "return" key
				return;
			}

			e.preventDefault(); // Keep this AFTER the key filter above

			accordionSwitch( $( this ) );
		});

		// Go back to the top-level Customizer accordion.
		$( '#customize-header-actions' ).on( 'click keydown', '.control-panel-back', function( e ) {
			if ( e.type === 'keydown' && 13 !== e.which ) { // "return" key
				return;
			}

			e.preventDefault(); // Keep this AFTER the key filter above

			panelSwitch( $( '.current-panel' ) );
		});
	});

	var sectionContent = $( '.accordion-section-content' );

	/**
	 * Close the current accordion section and open a new one.
	 *
	 * @param {Object} el Title element of the accordion section to toggle.
	 * @since 3.6.0
	 */
	function accordionSwitch ( el ) {
		var section = el.closest( '.accordion-section' ),
			siblings = section.closest( '.accordion-container' ).find( '.open' ),
			content = section.find( sectionContent );

		// This section has no content and cannot be expanded.
		if ( section.hasClass( 'cannot-expand' ) ) {
			return;
		}

		// Slide into a sub-panel instead of accordioning (Customizer-specific).
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
	}

	/**
	 * Slide into an accordion sub-panel.
	 *
	 * For the Customizer-specific panel functionality
	 *
	 * @param {Object} panel Title element or back button of the accordion panel to toggle.
	 * @since 4.0.0
	 */
	function panelSwitch( panel ) {
		var position, scroll,
			section = panel.closest( '.accordion-section' ),
			overlay = section.closest( '.wp-full-overlay' ),
			container = section.closest( '.accordion-container' ),
			siblings = container.find( '.open' ),
			topPanel = overlay.find( '#customize-theme-controls > ul > .accordion-section > .accordion-section-title' ).add( '#customize-info > .accordion-section-title' ),
			backBtn = overlay.find( '.control-panel-back' ),
			panelTitle = section.find( '.accordion-section-title' ).first(),
			content = section.find( '.control-panel-content' );

		if ( section.hasClass( 'current-panel' ) ) {
			section.toggleClass( 'current-panel' );
			overlay.toggleClass( 'in-sub-panel' );
			content.delay( 180 ).hide( 0, function() {
				content.css( 'margin-top', 'inherit' ); // Reset
			} );
			topPanel.attr( 'tabindex', '0' );
			backBtn.attr( 'tabindex', '-1' );
			panelTitle.focus();
			container.scrollTop( 0 );
		} else {
			// Close all open sections in any accordion level.
			siblings.removeClass( 'open' );
			siblings.find( sectionContent ).show().slideUp( 0 );
			content.show( 0, function() {
				position = content.offset().top;
				scroll = container.scrollTop();
				content.css( 'margin-top', ( 45 - position - scroll ) );
				section.toggleClass( 'current-panel' );
				overlay.toggleClass( 'in-sub-panel' );
				container.scrollTop( 0 );
			} );
			topPanel.attr( 'tabindex', '-1' );
			backBtn.attr( 'tabindex', '0' );
			backBtn.focus();
		}
	}

})(jQuery);
