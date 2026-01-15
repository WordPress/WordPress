/**
 * Remove squared button style
 *
 * @since Twenty Twenty 1.0
 */
/* global wp */
wp.domReady( function() {
	wp.blocks.unregisterBlockStyle( 'core/button', 'squared' );
} );
