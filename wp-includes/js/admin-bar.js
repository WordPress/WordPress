/**
 * @output wp-includes/js/admin-bar.js
 */
/**
 * Admin bar with Vanilla JS, no external dependencies.
 *
 * @param {Object} document  The document object.
 * @param {Object} window    The window object.
 * @param {Object} navigator The navigator object.
 *
 * @return {void}
 */
/* global hoverintent */
( function( document, window, navigator ) {
	document.addEventListener( 'DOMContentLoaded', function() {
		var adminBar = document.getElementById( 'wpadminbar' ),
			topMenuItems = adminBar.querySelectorAll( 'li.menupop' ),
			allMenuItems = adminBar.querySelectorAll( '.ab-item' ),
			adminBarLogout = document.getElementById( 'wp-admin-bar-logout' ),
			adminBarSearchForm = document.getElementById( 'adminbarsearch' ),
			shortlink = document.getElementById( 'wp-admin-bar-get-shortlink' ),
			skipLink = adminBar.querySelector( '.screen-reader-shortcut' ),
			mobileEvent = /Mobile\/.+Safari/.test( navigator.userAgent ) ? 'touchstart' : 'click',
			adminBarSearchInput,
			i;

		/**
		 * Remove nojs class after the DOM is loaded.
		 */
		adminBar.classList.remove( 'nojs' );

		if ( 'ontouchstart' in window ) {
			/**
			 * Remove hover class when the user touches outside the menu items.
			 */
			document.body.addEventListener( mobileEvent, function( e ) {
				if ( ! getClosest( e.target, 'li.menupop' ) ) {
					removeAllHoverClass( topMenuItems );
				}
			} );

			/**
			 * Add listener for menu items to toggle hover class by touches.
			 * Remove the callback later for better performance.
			 */
			adminBar.addEventListener( 'touchstart', function bindMobileEvents() {
				for ( var i = 0; i < topMenuItems.length; i++ ) {
					topMenuItems[i].addEventListener( 'click', mobileHover.bind( null, topMenuItems ) );
				}

				adminBar.removeEventListener( 'touchstart', bindMobileEvents );
			} );
		}

		/**
		 * Scroll page to top when clicking on the admin bar.
		 */
		adminBar.addEventListener( 'click', scrollToTop );

		for ( i = 0; i < topMenuItems.length; i++ ) {
			/**
			 * Adds or removes the hover class based on the hover intent.
			 */
			hoverintent(
				topMenuItems[i],
				addHoverClass.bind( null, topMenuItems[i] ),
				removeHoverClass.bind( null, topMenuItems[i] )
			).options( {
				timeout: 180
			} );

			/**
			 * Toggle hover class if the enter key is pressed.
			 */
			topMenuItems[i].addEventListener( 'keydown', toggleHoverIfEnter );
		}

		/**
		 * Remove hover class if the escape key is pressed.
		 */
		for ( i = 0; i < allMenuItems.length; i++ ) {
			allMenuItems[i].addEventListener( 'keydown', removeHoverIfEscape );
		}

		if ( adminBarSearchForm ) {
			adminBarSearchInput = document.getElementById( 'adminbar-search' );

			/**
			 * Adds the adminbar-focused class on focus.
			 */
			adminBarSearchInput.addEventListener( 'focus', function() {
				adminBarSearchForm.classList.add( 'adminbar-focused' );
			} );

			/**
			 * Removes the adminbar-focused class on blur.
			 */
			adminBarSearchInput.addEventListener( 'blur', function() {
				adminBarSearchForm.classList.remove( 'adminbar-focused' );
			} );
		}

		/**
		 * Focus the target of skip link after pressing Enter.
		 */
		skipLink.addEventListener( 'keydown', focusTargetAfterEnter );

		if ( shortlink ) {
			shortlink.addEventListener( 'click', clickShortlink );
		}

		/**
		 * Prevents the toolbar from covering up content when a hash is present
		 * in the URL.
		 */
		if ( window.location.hash ) {
			window.scrollBy( 0, -32 );
		}

		/**
		 * Add no-font-face class to body if needed.
		 */
		if ( navigator.userAgent && document.body.className.indexOf( 'no-font-face' ) === -1 &&
			/Android (1.0|1.1|1.5|1.6|2.0|2.1)|Nokia|Opera Mini|w(eb)?OSBrowser|webOS|UCWEB|Windows Phone OS 7|XBLWP7|ZuneWP7|MSIE 7/.test( navigator.userAgent ) ) {
			document.body.className += ' no-font-face';
		}

		/**
		 * Clear sessionStorage on logging out.
		 */
		adminBarLogout.addEventListener( 'click', emptySessionStorage );
	} );

	/**
	 * Remove hover class for top level menu item when escape is pressed.
	 *
	 * @since 5.3.0
	 *
	 * @param {Event} e The keydown event.
	 */
	function removeHoverIfEscape( e ) {
		var wrapper;

		if ( e.which != 27 ) {
			return;
		}

		wrapper = getClosest( e.target, '.menupop' );

		if ( ! wrapper ) {
			return;
		}

		wrapper.querySelector( '.menupop > .ab-item' ).focus();
		removeHoverClass( wrapper );
	}

	/**
	 * Toggle hover class for top level menu item when enter is pressed.
	 *
	 * @since 5.3.0
	 *
	 * @param {Event} e The keydown event.
	 */
	function toggleHoverIfEnter( e ) {
		var wrapper;

		if ( e.which != 13 ) {
			return;
		}

		if ( !! getClosest( e.target, '.ab-sub-wrapper' ) ) {
			return;
		}

		wrapper = getClosest( e.target, '.menupop' );

		if ( ! wrapper ) {
			return;
		}

		e.preventDefault();
		if ( hasHoverClass( wrapper ) ) {
			removeHoverClass( wrapper );
		} else {
			addHoverClass( wrapper );
		}
	}

	/**
	 * Focus the target of skip link after pressing Enter.
	 *
	 * @since 5.3.0
	 *
	 * @param {Event} e The keydown event.
	 */
	function focusTargetAfterEnter( e ) {
		var id, userAgent;

		if ( 13 !==	e.which ) {
			return;
		}

		id = e.target.getAttribute( 'href' );
		userAgent = navigator.userAgent.toLowerCase();

		if ( userAgent.indexOf( 'applewebkit' ) != -1 && id && id.charAt( 0 ) == '#' ) {
			setTimeout( function() {
				var target = document.getElementById( id.replace( '#', '' ) );

				target.setAttribute( 'tabIndex', '0' );
				target.focus();
			}, 100 );
		}
	}

	/**
	 * Toogle hover class for mobile devices.
	 *
	 * @since 5.3.0
	 *
	 * @param {NodeList} topMenuItems All menu items.
	 * @param {Event} e The click event.
	 */
	function mobileHover( topMenuItems, e ) {
		var wrapper;

		if ( !! getClosest( e.target, '.ab-sub-wrapper' ) ) {
			return;
		}

		e.preventDefault();

		wrapper = getClosest( e.target, '.menupop' );

		if ( ! wrapper ) {
			return;
		}

		if ( hasHoverClass( wrapper ) ) {
			removeHoverClass( wrapper );
		} else {
			removeAllHoverClass( topMenuItems );
			addHoverClass( wrapper );
		}
	}

	/**
	 * Handles the click on the Shortlink link in the adminbar.
	 *
	 * @since 3.1.0
	 * @since 5.3.0 Use querySelector to clean up the function.
	 *
	 * @param {Event} e The click event.
	 *
	 * @return {boolean} Returns false to prevent default click behavior.
	 */
	function clickShortlink( e ) {
		var wrapper = e.target.parentNode,
			input = wrapper.querySelector( '.shortlink-input' );

		// IE doesn't support preventDefault, and does support returnValue
		if ( e.preventDefault ) {
			e.preventDefault();
		}
		e.returnValue = false;

		wrapper.classList.add( 'selected' );
		input.focus();
		input.select();
		input.onblur = function() {
			wrapper.classList.remove( 'selected' );
		};

		return false;
	}

	/**
	 * Clear sessionStorage on logging out.
	 *
	 * @since 5.3.0
	 */
	function emptySessionStorage() {
		if ( 'sessionStorage' in window ) {
			try {
				for ( var key in sessionStorage ) {
					if ( key.indexOf( 'wp-autosave-' ) != -1 ) {
						sessionStorage.removeItem( key );
					}
				}
			} catch ( e ) {}
		}
	}

	/**
	 * Check if menu item has hover class.
	 *
	 * @since 5.3.0
	 *
	 * @param {HTMLElement} item Menu item Element.
	 */
	function hasHoverClass( item ) {
		return item.classList.contains( 'hover' );
	}

	/**
	 * Add hover class for menu item.
	 *
	 * @since 5.3.0
	 *
	 * @param {HTMLElement} item Menu item Element.
	 */
	function addHoverClass( item ) {
		item.classList.add( 'hover' );
	}

	/**
	 * Remove hover class for menu item.
	 *
	 * @since 5.3.0
	 *
	 * @param {HTMLElement} item Menu item Element.
	 */
	function removeHoverClass( item ) {
		item.classList.remove( 'hover' );
	}

	/**
	 * Remove hover class for all menu items.
	 *
	 * @since 5.3.0
	 *
	 * @param {NodeList} topMenuItems All menu items.
	 */
	function removeAllHoverClass( topMenuItems ) {
		for ( var i = 0; i < topMenuItems.length; i++ ) {
			if ( hasHoverClass( topMenuItems[i] ) ) {
				removeHoverClass( topMenuItems[i] );
			}
		}
	}

	/**
	 * Scrolls to the top of the page.
	 *
	 * @since 3.4.0
	 *
	 * @param {Event} e The Click event.
	 *
	 * @return {void}
	 */
	function scrollToTop( event ) {
		// Only scroll when clicking on the wpadminbar, not on menus or submenus.
		if (
			event.target &&
			event.target.id &&
			event.target.id != 'wpadminbar' &&
			event.target.id != 'wp-admin-bar-top-secondary'
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
	 * @since 5.3.0
	 *
	 * @param {HTMLElement} el Element to get parent.
	 * @param {string} selector CSS selector to match.
	 */
	function getClosest( el, selector ) {
		if ( ! Element.prototype.matches ) {
			Element.prototype.matches =
				Element.prototype.matchesSelector ||
				Element.prototype.mozMatchesSelector ||
				Element.prototype.msMatchesSelector ||
				Element.prototype.oMatchesSelector ||
				Element.prototype.webkitMatchesSelector ||
				function( s ) {
					var matches = ( this.document || this.ownerDocument ).querySelectorAll( s ),
						i = matches.length;
					while ( --i >= 0 && matches.item( i ) !== this ) { }
					return i > -1;
				};
		}

		// Get the closest matching elent
		for ( ; el && el !== document; el = el.parentNode ) {
			if ( el.matches( selector ) ) {
				return el;
			}
		}
		return null;
	}
} )( document, window, navigator );
