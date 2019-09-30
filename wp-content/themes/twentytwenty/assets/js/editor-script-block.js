/**
 * Remove squared button style
 *
 * @since 1.0.0
 */
/* global wp */
wp.domReady( function() {
	wp.blocks.unregisterBlockStyle( 'core/button', 'squared' );
} );
