/**
 * File customize-controls.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function() {

	wp.customize.bind( 'ready', function() {

		// Only show the color hue control when there's a custom primary color.
		wp.customize( 'primary_color', function( setting ) {
			wp.customize.control( 'primary_color_hue', function( control ) {
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
	});

})();
