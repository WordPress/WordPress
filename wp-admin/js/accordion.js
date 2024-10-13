/**
 * Accordion-folding functionality.
 *
 * Markup with the appropriate classes will be automatically hidden,
 * with one section opening at a time when its title is clicked.
 * Use the following markup structure for accordion behavior:
 *
 * <div class="accordion-container">
 *	<div class="accordion-section open">
 *		<h3 class="accordion-section-title"><button type="button" aria-expanded="true" aria-controls="target-1"></button></h3>
 *		<div class="accordion-section-content" id="target">
 *		</div>
 *	</div>
 *	<div class="accordion-section">
 *		<h3 class="accordion-section-title"><button type="button" aria-expanded="false" aria-controls="target-2"></button></h3>
 *		<div class="accordion-section-content" id="target-2">
 *		</div>
 *	</div>
 *	<div class="accordion-section">
 *		<h3 class="accordion-section-title"><button type="button" aria-expanded="false" aria-controls="target-3"></button></h3>
 *		<div class="accordion-section-content" id="target-3">
 *		</div>
 *	</div>
 * </div>
 *
 * Note that any appropriate tags may be used, as long as the above classes are present.
 *
 * @since 3.6.0
 * @output wp-admin/js/accordion.js
 */

( function( $ ){

	$( function () {

		// Expand/Collapse accordion sections on click.
		$( '.accordion-container' ).on( 'click', '.accordion-section-title button', function() {
			accordionSwitch( $( this ) );
		});

	});

	/**
	 * Close the current accordion section and open a new one.
	 *
	 * @param {Object} el Title element of the accordion section to toggle.
	 * @since 3.6.0
	 */
	function accordionSwitch ( el ) {
		var section = el.closest( '.accordion-section' ),
			container = section.closest( '.accordion-container' ),
			siblings = container.find( '.open' ),
			siblingsToggleControl = siblings.find( '[aria-expanded]' ).first(),
			content = section.find( '.accordion-section-content' );

		// This section has no content and cannot be expanded.
		if ( section.hasClass( 'cannot-expand' ) ) {
			return;
		}

		// Add a class to the container to let us know something is happening inside.
		// This helps in cases such as hiding a scrollbar while animations are executing.
		container.addClass( 'opening' );

		if ( section.hasClass( 'open' ) ) {
			section.toggleClass( 'open' );
			content.toggle( true ).slideToggle( 150 );
		} else {
			siblingsToggleControl.attr( 'aria-expanded', 'false' );
			siblings.removeClass( 'open' );
			siblings.find( '.accordion-section-content' ).show().slideUp( 150 );
			content.toggle( false ).slideToggle( 150 );
			section.toggleClass( 'open' );
		}

		// We have to wait for the animations to finish.
		setTimeout(function(){
		    container.removeClass( 'opening' );
		}, 150);

		// If there's an element with an aria-expanded attribute, assume it's a toggle control and toggle the aria-expanded value.
		if ( el ) {
			el.attr( 'aria-expanded', String( el.attr( 'aria-expanded' ) === 'false' ) );
		}
	}

})(jQuery);
