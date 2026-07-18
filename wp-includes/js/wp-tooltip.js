/**
 * @output wp-admin/js/wp-tooltip.js
 */

/**
 * Add focus and hover support for the 'tooltip' type in `wp_tooltip()`.
 * This script can be made obsolete when support is available for Interest Invokers.
 */
(() => {

	const popovers = /** @type {NodeListOf<HTMLDivElement>} */ ( document.querySelectorAll( '.wp-is-tooltip' ) );

	/** @type {ReturnType<typeof setTimeout>} */
	let openTimeout;

	popovers.forEach( function( popover ) {
		const trigger = /** @type {HTMLButtonElement|null} */ ( popover.querySelector( 'button.wp-tooltip__toggle' ) );
		const panel   = /** @type {HTMLSpanElement|null} */ ( popover.querySelector( 'span.wp-tooltip__bubble' ) );
		if ( ! trigger || ! panel ) {
			return;
		}

		// Show Tooltip Function (with delay to prevent flickering).
		const showTooltip = () => {
			clearTimeout( openTimeout );
			openTimeout = setTimeout( () => {
				// Only show if it's not already open.
				if ( ! panel.matches( ':popover-open' ) ) {
					// pass the triggering element so implicit position anchors work.
					panel.showPopover( { source: trigger } );
				}
			}, 300 );
		};
		// Hide Tooltip Function.
		const hideTooltip = () => {
			clearTimeout( openTimeout );
			if ( panel.matches( ':popover-open' ) ) {
				panel.hidePopover();
			}
		};

		// Bind Hover and Focus Events.
		trigger.addEventListener( 'mouseenter', showTooltip );
		trigger.addEventListener( 'focus', showTooltip );

		trigger.addEventListener( 'mouseleave', hideTooltip );
		trigger.addEventListener( 'blur', hideTooltip );
	});
})();
