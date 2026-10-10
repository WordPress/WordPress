/**
 * @output wp-admin/js/wp-tooltip.js
 */

/**
 * Add focus and hover support for the 'tooltip' type in `wp_tooltip()`.
 * This script can be made obsolete when support is available for Interest Invokers.
 */
(() => {

	const supportsPopover = /** @type {boolean} */ ( Object.prototype.hasOwnProperty.call( HTMLElement.prototype, 'popover' ) );
	let hidePopover = false;
	if ( ! supportsPopover ) {
		hidePopover = true;
	}

	const popovers = /** @type {NodeListOf<HTMLSpanElement>} */ ( document.querySelectorAll( '.wp-is-tooltip' ) );

	popovers.forEach( function( popover ) {
		const trigger = /** @type {HTMLButtonElement|HTMLAnchorElement|null} */ ( popover.querySelector( '.wp-tooltip__toggle' ) );
		const panel   = /** @type {HTMLSpanElement|null} */ ( popover.querySelector( 'span.wp-tooltip__bubble' ) );
		if ( hidePopover && panel ) {
			panel.classList.add( 'hidden' );

			return;
		}
		if ( ! trigger || ! panel ) {
			return;
		}

		/** @type {ReturnType<typeof setTimeout>} */
		let openTimeout;

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

/**
 * Add focus and hover support for the toggle tip's hint in `wp_get_toggletip()`.
 *
 * A toggle tip has no visible text label, so a hover/focus hint exposes the
 * toggle button's accessible name to sighted users. The hint is suppressed while
 * the toggle tip dialog itself is open, so the two never overlap.
 * This script can be made obsolete when support is available for Interest Invokers.
 */
(() => {

	const toggletips = /** @type {NodeListOf<HTMLSpanElement>} */ ( document.querySelectorAll( '.wp-is-toggletip' ) );

	/** @type {ReturnType<typeof setTimeout>} */
	let openTimeout;

	toggletips.forEach( function( toggletip ) {
		const trigger = /** @type {HTMLButtonElement|null} */ ( toggletip.querySelector( 'button.wp-tooltip__toggle' ) );
		const hint    = /** @type {HTMLSpanElement|null} */ ( toggletip.querySelector( '.wp-tooltip__hint' ) );
		const dialog  = /** @type {HTMLDialogElement|null} */ ( toggletip.querySelector( 'dialog.wp-tooltip__bubble' ) );

		if ( ! trigger || ! hint ) {
			return;
		}

		// Show Hint Function (with delay to prevent flickering).
		const showHint = () => {
			clearTimeout( openTimeout );
			openTimeout = setTimeout( () => {
				// Don't show the hint over an open toggle tip dialog.
				if ( dialog && dialog.matches( ':popover-open' ) ) {
					return;
				}
				// Only show if it's not already open.
				if ( ! hint.matches( ':popover-open' ) ) {
					// pass the triggering element so implicit position anchors work.
					hint.showPopover( { source: trigger } );
				}
			}, 300 );
		};
		// Hide Hint Function.
		const hideHint = () => {
			clearTimeout( openTimeout );
			if ( hint.matches( ':popover-open' ) ) {
				hint.hidePopover();
			}
		};

		// Bind Hover and Focus Events.
		trigger.addEventListener( 'mouseenter', showHint );
		trigger.addEventListener( 'focus', showHint );

		trigger.addEventListener( 'mouseleave', hideHint );
		trigger.addEventListener( 'blur', hideHint );

		// Hide the hint as soon as the toggle tip is opened.
		trigger.addEventListener( 'click', hideHint );
	});
})();
