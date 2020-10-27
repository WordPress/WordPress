/**
 * File primary-navigation.js.
 *
 * Required to open and close the mobile navigation.
 */

/**
 * Toggle an attribute's value
 *
 * @param {Element} el - The element.
 * @param {boolean} withListeners - Whether we want to add/remove listeners or not.
 * @since 1.0.0
 */
function twentytwentyoneToggleAriaExpanded( el, withListeners ) {
	if ( 'true' !== el.getAttribute( 'aria-expanded' ) ) {
		el.setAttribute( 'aria-expanded', 'true' );
		if ( withListeners ) {
			document.addEventListener( 'click', twentytwentyoneCollapseMenuOnClickOutside );
		}
	} else {
		el.setAttribute( 'aria-expanded', 'false' );
		if ( withListeners ) {
			document.removeEventListener( 'click', twentytwentyoneCollapseMenuOnClickOutside );
		}
	}
}

function twentytwentyoneCollapseMenuOnClickOutside( event ) {
	if ( ! document.getElementById( 'site-navigation' ).contains( event.target ) ) {
		document.getElementById( 'site-navigation' ).querySelectorAll( '.sub-menu-toggle' ).forEach( function( button ) {
			button.setAttribute( 'aria-expanded', 'false' );
		} );
	}
}

/**
 * Handle clicks on submenu toggles.
 *
 * @param {Element} el - The element.
 */
function twentytwentyoneExpandSubMenu( el ) { // jshint ignore:line
	// Close other expanded items.
	el.closest( 'nav' ).querySelectorAll( '.sub-menu-toggle' ).forEach( function( button ) {
		if ( button !== el ) {
			button.setAttribute( 'aria-expanded', 'false' );
		}
	} );

	// Toggle aria-expanded on the button.
	twentytwentyoneToggleAriaExpanded( el, true );

	// On tab-away collapse the menu.
	el.parentNode.querySelectorAll( 'ul > li:last-child > a' ).forEach( function( linkEl ) {
		linkEl.addEventListener( 'blur', function( event ) {
			if ( ! el.parentNode.contains( event.relatedTarget ) ) {
				el.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	} );
}

( function() {
	/**
	 * Menu Toggle Behaviors
	 *
	 * @param {string} id - The ID.
	 */
	var navMenu = function( id ) {
		var wrapper = document.body, // this is the element to which a CSS class is added when a mobile nav menu is open
			mobileButton = document.getElementById( id + '-mobile-menu' );

		if ( mobileButton ) {
			mobileButton.onclick = function() {
				wrapper.classList.toggle( id + '-navigation-open' );
				wrapper.classList.toggle( 'lock-scrolling' );
				twentytwentyoneToggleAriaExpanded( mobileButton );
				mobileButton.focus();
			};
		}
		/**
		 * Trap keyboard navigation in the menu modal.
		 * Adapted from TwentyTwenty
		 */
		document.addEventListener( 'keydown', function( event ) {
			var modal, elements, selectors, lastEl, firstEl, activeEl, tabKey, shiftKey, escKey;
			if ( ! wrapper.classList.contains( id + '-navigation-open' ) ) {
				return;
			}

			modal = document.querySelector( '.' + id + '-navigation' );
			selectors = 'input, a, button';
			elements = modal.querySelectorAll( selectors );
			elements = Array.prototype.slice.call( elements );
			tabKey = event.keyCode === 9;
			shiftKey = event.shiftKey;
			escKey = event.keyCode === 27;
			activeEl = document.activeElement;
			lastEl = elements[ elements.length - 1 ];
			firstEl = elements[0];

			if ( escKey ) {
				event.preventDefault();
				wrapper.classList.remove( id + '-navigation-open', 'lock-scrolling' );
				twentytwentyoneToggleAriaExpanded( mobileButton );
				mobileButton.focus();
			}

			if ( ! shiftKey && tabKey && lastEl === activeEl ) {
				event.preventDefault();
				firstEl.focus();
			}

			if ( shiftKey && tabKey && firstEl === activeEl ) {
				event.preventDefault();
				lastEl.focus();
			}

			// If there are no elements in the menu, don't move the focus
			if ( tabKey && firstEl === lastEl ) {
				event.preventDefault();
			}
		} );

		document.getElementById( 'site-navigation' ).querySelectorAll( '.menu-wrapper > .menu-item-has-children' ).forEach( function( li ) {
			li.addEventListener( 'mouseenter', function() {
				this.querySelector( '.sub-menu-toggle' ).setAttribute( 'aria-expanded', 'true' );
			} );
			li.addEventListener( 'mouseleave', function() {
				this.querySelector( '.sub-menu-toggle' ).setAttribute( 'aria-expanded', 'false' );
			} );
		} );
	};

	window.addEventListener( 'load', function() {
		new navMenu( 'primary' );
	} );
}() );
