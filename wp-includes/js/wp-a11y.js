window.wp = window.wp || {};

( function ( wp, $ ) {
	'use strict';

	var $container;

	/**
	 * Update the ARIA live notification area text node.
	 *
	 * @since 4.2.0
	 *
	 * @param {String} message
	 */
	function speak( message ) {
		if ( $container ) {
			$container.text( message );
		}
	}

	/**
	 * Initialize wp.a11y and define ARIA live notification area.
	 *
	 * @since 4.2.0
	 */
	$( document ).ready( function() {
		$container = $( '#wp-a11y-speak' );

		if ( ! $container.length ) {
			$container = $( '<div>', {
				id: 'wp-a11y-speak',
				role: 'status',
				'aria-live': 'polite',
				'aria-relevant': 'all',
				'aria-atomic': 'true',
				'class': 'screen-reader-text'
			} );

			$( document.body ).append( $container );
		}
	} );

	wp.a11y = wp.a11y || {};
	wp.a11y.speak = speak;

} )( window.wp, window.jQuery );
