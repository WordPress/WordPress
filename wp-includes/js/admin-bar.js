/**
 * @output wp-includes/js/admin-bar.js
 */
/**
 * Admin bar with Vanilla JS, no external dependencies.
 *
 * @since 5.3.1
 *
 * @param {Object} document  The document object.
 * @param {Object} window    The window object.
 * @param {Object} navigator The navigator object.
 *
 * @return {void}
 */
( function( document, window, navigator ) {
	document.addEventListener( 'DOMContentLoaded', function() {
		var adminBar = document.getElementById( 'wpadminbar' ),
			topMenuItems,
			allMenuItems,
			adminBarLogout,
			adminBarSearchForm,
			shortlink,
			skipLink,
			mobileEvent,
			fontFaceRegex,
			adminBarSearchInput,
			i;

		if ( ! adminBar || ! ( 'querySelectorAll' in adminBar ) ) {
			return;
		}

		topMenuItems = adminBar.querySelectorAll( 'li.menupop' );
		allMenuItems = adminBar.querySelectorAll( '.ab-item' );
		adminBarLogout = document.getElementById( 'wp-admin-bar-logout' );
		adminBarSearchForm = document.getElementById( 'adminbarsearch' );
		shortlink = document.getElementById( 'wp-admin-bar-get-shortlink' );
		skipLink = adminBar.querySelector( '.screen-reader-shortcut' );
		mobileEvent = /Mobile\/.+Safari/.test( navigator.userAgent ) ? 'touchstart' : 'click';
		fontFaceRegex = /Android (1.0|1.1|1.5|1.6|2.0|2.1)|Nokia|Opera Mini|w(eb)?OSBrowser|webOS|UCWEB|Windows Phone OS 7|XBLWP7|ZuneWP7|MSIE 7/;

		// Remove nojs class after the DOM is loaded.
		removeClass( adminBar, 'nojs' );

		if ( 'ontouchstart' in window ) {
			// Remove hover class when the user touches outside the menu items.
			document.body.addEventListener( mobileEvent, function( e ) {
				if ( ! getClosest( e.target, 'li.menupop' ) ) {
					removeAllHoverClass( topMenuItems );
				}
			} );

			// Add listener for menu items to toggle hover class by touches.
			// Remove the callback later for better performance.
			adminBar.addEventListener( 'touchstart', function bindMobileEvents() {
				for ( var i = 0; i < topMenuItems.length; i++ ) {
					topMenuItems[i].addEventListener( 'click', mobileHover.bind( null, topMenuItems ) );
				}

				adminBar.removeEventListener( 'touchstart', bindMobileEvents );
			} );
		}

		// Scroll page to top when clicking on the admin bar.
		adminBar.addEventListener( 'click', scrollToTop );

		for ( i = 0; i < topMenuItems.length; i++ ) {
			// Adds or removes the hover class based on the hover intent.
			window.hoverintent(
				topMenuItems[i],
				addClass.bind( null, topMenuItems[i], 'hover' ),
				removeClass.bind( null, topMenuItems[i], 'hover' )
			).options( {
				timeout: 180
			} );

			// Toggle hover class if the enter key is pressed.
			topMenuItems[i].addEventListener( 'keydown', toggleHoverIfEnter );
		}

		// Remove hover class if the escape key is pressed.
		for ( i = 0; i < allMenuItems.length; i++ ) {
			allMenuItems[i].addEventListener( 'keydown', removeHoverIfEscape );
		}

		if ( adminBarSearchForm ) {
			adminBarSearchInput = document.getElementById( 'adminbar-search' );

			// Adds the adminbar-focused class on focus.
			adminBarSearchInput.addEventListener( 'focus', function() {
				addClass( adminBarSearchForm, 'adminbar-focused' );
			} );

			// Removes the adminbar-focused class on blur.
			adminBarSearchInput.addEventListener( 'blur', function() {
				removeClass( adminBarSearchForm, 'adminbar-focused' );
			} );
		}

		if ( skipLink ) {
			// Focus the target of skip link after pressing Enter.
			skipLink.addEventListener( 'keydown', focusTargetAfterEnter );
		}

		if ( shortlink ) {
			shortlink.addEventListener( 'click', clickShortlink );
		}

		// Prevents the toolbar from covering up content when a hash is present in the URL.
		if ( window.location.hash ) {
			window.scrollBy( 0, -32 );
		}

		// Add no-font-face class to body if needed.
		if (
			navigator.userAgent &&
			fontFaceRegex.test( navigator.userAgent ) &&
			! hasClass( document.body, 'no-font-face' )
		) {
			addClass( document.body, 'no-font-face' );
		}

		// Clear sessionStorage on logging out.
		if ( adminBarLogout ) {
			adminBarLogout.addEventListener( 'click', emptySessionStorage );
		}
	} );

	/**
	 * Remove hover class for top level menu item when escape is pressed.
	 *
	 * @since 5.3.1
	 *
	 * @param {Event} event The keydown event.
	 */
	function removeHoverIfEscape( event ) {
		var wrapper;

		if ( event.which !== 27 ) {
			return;
		}

		wrapper = getClosest( event.target, '.menupop' );

		if ( ! wrapper ) {
			return;
		}

		wrapper.querySelector( '.menupop > .ab-item' ).focus();
		removeClass( wrapper, 'hover' );
	}

	/**
	 * Toggle hover class for top level menu item when enter is pressed.
	 *
	 * @since 5.3.1
	 *
	 * @param {Event} event The keydown event.
	 */
	function toggleHoverIfEnter( event ) {
		var wrapper;

		if ( event.which !== 13 ) {
			return;
		}

		if ( !! getClosest( event.target, '.ab-sub-wrapper' ) ) {
			return;
		}

		wrapper = getClosest( event.target, '.menupop' );

		if ( ! wrapper ) {
			return;
		}

		event.preventDefault();

		if ( hasClass( wrapper, 'hover' ) ) {
			removeClass( wrapper, 'hover' );
		} else {
			addClass( wrapper, 'hover' );
		}
	}

	/**
	 * Focus the target of skip link after pressing Enter.
	 *
	 * @since 5.3.1
	 *
	 * @param {Event} event The keydown event.
	 */
	function focusTargetAfterEnter( event ) {
		var id, userAgent;

		if ( event.which !== 13 ) {
			return;
		}

		id = event.target.getAttribute( 'href' );
		userAgent = navigator.userAgent.toLowerCase();

		if ( userAgent.indexOf( 'applewebkit' ) > -1 && id && id.charAt( 0 ) === '#' ) {
			setTimeout( function() {
				var target = document.getElementById( id.replace( '#', '' ) );

				if ( target ) {
					target.setAttribute( 'tabIndex', '0' );
					target.focus();
				}
			}, 100 );
		}
	}

	/**
	 * Toogle hover class for mobile devices.
	 *
	 * @since 5.3.1
	 *
	 * @param {NodeList} topMenuItems All menu items.
	 * @param {Event} event The click event.
	 */
	function mobileHover( topMenuItems, event ) {
		var wrapper;

		if ( !! getClosest( event.target, '.ab-sub-wrapper' ) ) {
			return;
		}

		event.preventDefault();

		wrapper = getClosest( event.target, '.menupop' );

		if ( ! wrapper ) {
			return;
		}

		if ( hasClass( wrapper, 'hover' ) ) {
			removeClass( wrapper, 'hover' );
		} else {
			removeAllHoverClass( topMenuItems );
			addClass( wrapper, 'hover' );
		}
	}

	/**
	 * Handles the click on the Shortlink link in the adminbar.
	 *
	 * @since 3.1.0
	 * @since 5.3.1 Use querySelector to clean up the function.
	 *
	 * @param {Event} event The click event.
	 * @return {boolean} Returns false to prevent default click behavior.
	 */
	function clickShortlink( event ) {
		var wrapper = event.target.parentNode,
			input;

		if ( wrapper ) {
			input = wrapper.querySelector( '.shortlink-input' );
		}

		if ( ! input ) {
			return;
		}

		// (Old) IE doesn't support preventDefault, and does support returnValue.
		if ( event.preventDefault ) {
			event.preventDefault();
		}

		event.returnValue = false;

		addClass( wrapper, 'selected' );

		input.focus();
		input.select();
		input.onblur = function() {
			removeClass( wrapper, 'selected' );
		};

		return false;
	}

	/**
	 * Clear sessionStorage on logging out.
	 *
	 * @since 5.3.1
	 */
	function emptySessionStorage() {
		if ( 'sessionStorage' in window ) {
			try {
				for ( var key in sessionStorage ) {
					if ( key.indexOf( 'wp-autosave-' ) > -1 ) {
						sessionStorage.removeItem( key );
					}
				}
			} catch ( er ) {}
		}
	}

	/**
	 * Check if element has class.
	 *
	 * @since 5.3.1
	 *
	 * @param {HTMLElement} element The HTML element.
	 * @param {String}      className The class name.
	 * @return {bool} Whether the element has the className.
	 */
	function hasClass( element, className ) {
		var classNames;

		if ( ! element ) {
			return false;
		}

		if ( element.classList && element.classList.contains ) {
			return element.classList.contains( className );
		} else if ( element.className ) {
			classNames = element.className.split( ' ' );
			return classNames.indexOf( className ) > -1;
		}

		return false;
	}

	/**
	 * Add class to an element.
	 *
	 * @since 5.3.1
	 *
	 * @param {HTMLElement} element The HTML element.
	 * @param {String}      className The class name.
	 */
	function addClass( element, className ) {
		if ( ! element ) {
			return;
		}

		if ( element.classList && element.classList.add ) {
			element.classList.add( className );
		} else if ( ! hasClass( element, className ) ) {
			if ( element.className ) {
				element.className += ' ';
			}

			element.className += className;
		}
	}

	/**
	 * Remove class from an element.
	 *
	 * @since 5.3.1
	 *
	 * @param {HTMLElement} element The HTML element.
	 * @param {String}      className The class name.
	 */
	function removeClass( element, className ) {
		var testName,
			classes;

		if ( ! element || ! hasClass( element, className ) ) {
			return;
		}

		if ( element.classList && element.classList.remove ) {
			element.classList.remove( className );
		} else {
			testName = ' ' + className + ' ';
			classes = ' ' + element.className + ' ';

			while ( classes.indexOf( testName ) > -1 ) {
				classes = classes.replace( testName, '' );
			}

			element.className = classes.replace( /^[\s]+|[\s]+$/g, '' );
		}
	}

	/**
	 * Remove hover class for all menu items.
	 *
	 * @since 5.3.1
	 *
	 * @param {NodeList} topMenuItems All menu items.
	 */
	function removeAllHoverClass( topMenuItems ) {
		if ( topMenuItems && topMenuItems.length ) {
			for ( var i = 0; i < topMenuItems.length; i++ ) {
				removeClass( topMenuItems[i], 'hover' );
			}
		}
	}

	/**
	 * Scrolls to the top of the page.
	 *
	 * @since 3.4.0
	 *
	 * @param {Event} event The Click event.
	 *
	 * @return {void}
	 */
	function scrollToTop( event ) {
		// Only scroll when clicking on the wpadminbar, not on menus or submenus.
		if (
			event.target &&
			event.target.id !== 'wpadminbar' &&
			event.target.id !== 'wp-admin-bar-top-secondary'
		) {
			return;
		}

		try {
			window.scrollTo( {
				top: -32,
				left: 0,
				behavior: 'smooth'
			} );
		} catch ( er ) {
			window.scrollTo( 0, -32 );
		}
	}

	/**
	 * Get closest Element.
	 *
	 * @since 5.3.1
	 *
	 * @param {HTMLElement} el Element to get parent.
	 * @param {string} selector CSS selector to match.
	 */
	function getClosest( el, selector ) {
		if ( ! window.Element.prototype.matches ) {
			// Polyfill from https://developer.mozilla.org/en-US/docs/Web/API/Element/matches.
			window.Element.prototype.matches =
				window.Element.prototype.matchesSelector ||
				window.Element.prototype.mozMatchesSelector ||
				window.Element.prototype.msMatchesSelector ||
				window.Element.prototype.oMatchesSelector ||
				window.Element.prototype.webkitMatchesSelector ||
				function( s ) {
					var matches = ( this.document || this.ownerDocument ).querySelectorAll( s ),
						i = matches.length;

					while ( --i >= 0 && matches.item( i ) !== this ) { }

					return i > -1;
				};
		}

		// Get the closest matching elent.
		for ( ; el && el !== document; el = el.parentNode ) {
			if ( el.matches( selector ) ) {
				return el;
			}
		}

		return null;
	}

} )( document, window, navigator );
