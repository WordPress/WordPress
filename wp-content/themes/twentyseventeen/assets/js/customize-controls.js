/**
 * Scripts within the customizer controls window.
 *
 * Contextually shows the color hue control and informs the preview
 * when users open or close the front page sections section.
 */

( function() {
	wp.customize.bind( 'ready', function() {

		// Only show the color hue control when there's a custom color scheme.
		wp.customize( 'colorscheme', function( setting ) {
			wp.customize.control( 'colorscheme_hue', function( control ) {
				var visibility = function() {
					if ( 'custom' === setting.get() ) {
						control.container.slideDown( 180 );
					} else {
						control.container.slideUp( 180 );
					}
				};

				visibility();
				setting.bind( visibility );
			});
		});

		// Detect when the front page sections section is expanded (or closed) so we can adjust the preview accordingly
		wp.customize.section( 'theme_options' ).expanded.bind( function( isExpanding ) {

			// Value of isExpanding will = true if you're entering the section, false if you're leaving it
			wp.customize.previewer.send( 'section-highlight', { expanded: isExpanding } );
		} );
	} );
} )( jQuery );
