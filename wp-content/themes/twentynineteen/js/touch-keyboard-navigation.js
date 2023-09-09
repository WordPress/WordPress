/**
 * Touch & Keyboard navigation.
 *
 * Contains handlers for touch devices and keyboard navigation.
 */

(function() {

	/**
<<<<<<< HEAD
	 * Debounce.
=======
	 * Debounce
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 *
	 * @param {Function} func
	 * @param {number} wait
	 * @param {boolean} immediate
	 */
	function debounce(func, wait, immediate) {
		'use strict';

		var timeout;
		wait      = (typeof wait !== 'undefined') ? wait : 20;
		immediate = (typeof immediate !== 'undefined') ? immediate : true;

		return function() {

			var context = this, args = arguments;
			var later = function() {
				timeout = null;

				if (!immediate) {
					func.apply(context, args);
				}
			};

			var callNow = immediate && !timeout;

			clearTimeout(timeout);
			timeout = setTimeout(later, wait);

			if (callNow) {
				func.apply(context, args);
			}
		};
	}

	/**
<<<<<<< HEAD
	 * Add class.
=======
	 * Add class
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 *
	 * @param {Object} el
	 * @param {string} cls
	 */
	function addClass(el, cls) {
		if ( ! el.className.match( '(?:^|\\s)' + cls + '(?!\\S)') ) {
			el.className += ' ' + cls;
		}
	}

	/**
<<<<<<< HEAD
	 * Delete class.
=======
	 * Delete class
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 *
	 * @param {Object} el
	 * @param {string} cls
	 */
	function deleteClass(el, cls) {
		el.className = el.className.replace( new RegExp( '(?:^|\\s)' + cls + '(?!\\S)' ),'' );
	}

	/**
	 * Has class?
	 *
	 * @param {Object} el
	 * @param {string} cls
	 *
	 * @returns {boolean} Has class
	 */
	function hasClass(el, cls) {

		if ( el.className.match( '(?:^|\\s)' + cls + '(?!\\S)' ) ) {
			return true;
		}
	}

	/**
<<<<<<< HEAD
	 * Toggle Aria Expanded state for screenreaders.
=======
	 * Toggle Aria Expanded state for screenreaders
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 *
	 * @param {Object} ariaItem
	 */
	function toggleAriaExpandedState( ariaItem ) {
		'use strict';

		var ariaState = ariaItem.getAttribute('aria-expanded');

		if ( ariaState === 'true' ) {
			ariaState = 'false';
		} else {
			ariaState = 'true';
		}

		ariaItem.setAttribute('aria-expanded', ariaState);
	}

	/**
<<<<<<< HEAD
	 * Open sub-menu.
=======
	 * Open sub-menu
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 *
	 * @param {Object} currentSubMenu
	 */
	function openSubMenu( currentSubMenu ) {
		'use strict';

<<<<<<< HEAD
		// Update classes.
		// classList.add is not supported in IE11.
		currentSubMenu.parentElement.className += ' off-canvas';
		currentSubMenu.parentElement.lastElementChild.className += ' expanded-true';

		// Update aria-expanded state.
=======
		// Update classes
		// classList.add is not supported in IE11
		currentSubMenu.parentElement.className += ' off-canvas';
		currentSubMenu.parentElement.lastElementChild.className += ' expanded-true';

		// Update aria-expanded state
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		toggleAriaExpandedState( currentSubMenu );
	}

	/**
<<<<<<< HEAD
	 * Close sub-menu.
=======
	 * Close sub-menu
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 *
	 * @param {Object} currentSubMenu
	 */
	function closeSubMenu( currentSubMenu ) {
		'use strict';

		var menuItem     = getCurrentParent( currentSubMenu, '.menu-item' ); // this.parentNode
		var menuItemAria = menuItem.querySelector('a[aria-expanded]');
		var subMenu      = currentSubMenu.closest('.sub-menu');

<<<<<<< HEAD
		// If this is in a sub-sub-menu, go back to parent sub-menu.
		if ( getCurrentParent( currentSubMenu, 'ul' ).classList.contains( 'sub-menu' ) ) {

			// Update classes.
			// classList.remove is not supported in IE11.
			menuItem.className = menuItem.className.replace( 'off-canvas', '' );
			subMenu.className  = subMenu.className.replace( 'expanded-true', '' );

			// Update aria-expanded and :focus states.
			toggleAriaExpandedState( menuItemAria );

		// Or else close all sub-menus.
		} else {

			// Update classes.
			// classList.remove is not supported in IE11.
			menuItem.className = menuItem.className.replace( 'off-canvas', '' );
			menuItem.lastElementChild.className = menuItem.lastElementChild.className.replace( 'expanded-true', '' );

			// Update aria-expanded and :focus states.
=======
		// If this is in a sub-sub-menu, go back to parent sub-menu
		if ( getCurrentParent( currentSubMenu, 'ul' ).classList.contains( 'sub-menu' ) ) {

			// Update classes
			// classList.remove is not supported in IE11
			menuItem.className = menuItem.className.replace( 'off-canvas', '' );
			subMenu.className  = subMenu.className.replace( 'expanded-true', '' );

			// Update aria-expanded and :focus states
			toggleAriaExpandedState( menuItemAria );

		// Or else close all sub-menus
		} else {

			// Update classes
			// classList.remove is not supported in IE11
			menuItem.className = menuItem.className.replace( 'off-canvas', '' );
			menuItem.lastElementChild.className = menuItem.lastElementChild.className.replace( 'expanded-true', '' );

			// Update aria-expanded and :focus states
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			toggleAriaExpandedState( menuItemAria );
		}
	}

	/**
<<<<<<< HEAD
	 * Find first ancestor of an element by selector.
=======
	 * Find first ancestor of an element by selector
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 *
	 * @param {Object} child
	 * @param {String} selector
	 * @param {String} stopSelector
	 */
	function getCurrentParent( child, selector, stopSelector ) {

		var currentParent = null;

		while ( child ) {

			if ( child.matches(selector) ) {

				currentParent = child;
				break;

			} else if ( stopSelector && child.matches(stopSelector) ) {

				break;
			}

			child = child.parentElement;
		}

		return currentParent;
	}

	/**
<<<<<<< HEAD
	 * Remove all off-canvas states.
=======
	 * Remove all off-canvas states
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	function removeAllFocusStates() {
		'use strict';

		var siteBranding            = document.getElementsByClassName( 'site-branding' )[0];
		var getFocusedElements      = siteBranding.querySelectorAll(':hover, :focus, :focus-within');
		var getFocusedClassElements = siteBranding.querySelectorAll('.is-focused');
		var i;
		var o;

		for ( i = 0; i < getFocusedElements.length; i++) {
			getFocusedElements[i].blur();
		}

		for ( o = 0; o < getFocusedClassElements.length; o++) {
			deleteClass( getFocusedClassElements[o], 'is-focused' );
		}
	}

	/**
<<<<<<< HEAD
	 * Matches polyfill for IE11.
=======
	 * Matches polyfill for IE11
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	if (!Element.prototype.matches) {
		Element.prototype.matches = Element.prototype.msMatchesSelector;
	}

	/**
	 * Toggle `focus` class to allow sub-menu access on touch screens.
	 */
	function toggleSubmenuDisplay() {

		document.addEventListener('touchstart', function(event) {

			if ( event.target.matches('a') ) {

				var url = event.target.getAttribute( 'href' ) ? event.target.getAttribute( 'href' ) : '';

<<<<<<< HEAD
				// Open submenu if URL is #.
=======
				// Open submenu if url is #
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				if ( '#' === url && event.target.nextSibling.matches('.submenu-expand') ) {
					openSubMenu( event.target );
				}
			}

<<<<<<< HEAD
			// Check if .submenu-expand is touched.
			if ( event.target.matches('.submenu-expand') ) {
				openSubMenu(event.target);

			// Check if child of .submenu-expand is touched.
=======
			// Check if .submenu-expand is touched
			if ( event.target.matches('.submenu-expand') ) {
				openSubMenu(event.target);

			// Check if child of .submenu-expand is touched
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			} else if ( null != getCurrentParent( event.target, '.submenu-expand' ) &&
								getCurrentParent( event.target, '.submenu-expand' ).matches( '.submenu-expand' ) ) {
				openSubMenu( getCurrentParent( event.target, '.submenu-expand' ) );

<<<<<<< HEAD
			// Check if .menu-item-link-return is touched.
			} else if ( event.target.matches('.menu-item-link-return') ) {
				closeSubMenu( event.target );

			// Check if child of .menu-item-link-return is touched.
=======
			// Check if .menu-item-link-return is touched
			} else if ( event.target.matches('.menu-item-link-return') ) {
				closeSubMenu( event.target );

			// Check if child of .menu-item-link-return is touched
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			} else if ( null != getCurrentParent( event.target, '.menu-item-link-return' ) && getCurrentParent( event.target, '.menu-item-link-return' ).matches( '.menu-item-link-return' ) ) {
				closeSubMenu( event.target );
			}

<<<<<<< HEAD
			// Prevent default mouse/focus events.
=======
			// Prevent default mouse/focus events
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			removeAllFocusStates();

		}, false);

		document.addEventListener('touchend', function(event) {

			var mainNav = getCurrentParent( event.target, '.main-navigation' );

			if ( null != mainNav && hasClass( mainNav, '.main-navigation' ) ) {
<<<<<<< HEAD
				// Prevent default mouse events.
=======
				// Prevent default mouse events
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				event.preventDefault();

			} else if (
				event.target.matches('.submenu-expand') ||
				null != getCurrentParent( event.target, '.submenu-expand' ) &&
				getCurrentParent( event.target, '.submenu-expand' ).matches( '.submenu-expand' ) ||
				event.target.matches('.menu-item-link-return') ||
				null != getCurrentParent( event.target, '.menu-item-link-return' ) &&
				getCurrentParent( event.target, '.menu-item-link-return' ).matches( '.menu-item-link-return' ) ) {
<<<<<<< HEAD
					// Prevent default mouse events.
					event.preventDefault();
			}

			// Prevent default mouse/focus events.
=======
					// Prevent default mouse events
					event.preventDefault();
			}

			// Prevent default mouse/focus events
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			removeAllFocusStates();

		}, false);

		document.addEventListener('focus', function(event) {

<<<<<<< HEAD
			if ( event.target !== window.document && event.target.matches( '.main-navigation > div > ul > li a' ) ) {

				// Remove Focused elements in sibling div.
=======
			if ( event.target.matches('.main-navigation > div > ul > li a') ) {

				// Remove Focused elements in sibling div
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				var currentDiv        = getCurrentParent( event.target, 'div', '.main-navigation' );
				var currentDivSibling = currentDiv.previousElementSibling === null ? currentDiv.nextElementSibling : currentDiv.previousElementSibling;
				var focusedElement    = currentDivSibling.querySelector( '.is-focused' );
				var focusedClass      = 'is-focused';
				var prevLi            = getCurrentParent( event.target, '.main-navigation > div > ul > li', '.main-navigation' ).previousElementSibling;
				var nextLi            = getCurrentParent( event.target, '.main-navigation > div > ul > li', '.main-navigation' ).nextElementSibling;

				if ( null !== focusedElement && null !== hasClass( focusedElement, focusedClass ) ) {
					deleteClass( focusedElement, focusedClass );
				}

<<<<<<< HEAD
				// Add .is-focused class to top-level li.
=======
				// Add .is-focused class to top-level li
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				if ( getCurrentParent( event.target, '.main-navigation > div > ul > li', '.main-navigation' ) ) {
					addClass( getCurrentParent( event.target, '.main-navigation > div > ul > li', '.main-navigation' ), focusedClass );
				}

<<<<<<< HEAD
				// Check for previous li.
=======
				// Check for previous li
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				if ( prevLi && hasClass( prevLi, focusedClass ) ) {
					deleteClass( prevLi, focusedClass );
				}

<<<<<<< HEAD
				// Check for next li.
=======
				// Check for next li
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				if ( nextLi && hasClass( nextLi, focusedClass ) ) {
					deleteClass( nextLi, focusedClass );
				}
			}

		}, true);

		document.addEventListener('click', function(event) {

<<<<<<< HEAD
			// Remove all focused menu states when clicking outside site branding.
			if ( event.target !== document.getElementsByClassName( 'site-branding' )[0] ) {
				removeAllFocusStates();
			} else {
				// Nothing.
=======
			// Remove all focused menu states when clicking outside site branding
			if ( event.target !== document.getElementsByClassName( 'site-branding' )[0] ) {
				removeAllFocusStates();
			} else {
				// nothing
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			}

		}, false);
	}

	/**
<<<<<<< HEAD
	 * Run our sub-menu function as soon as the document is `ready`.
=======
	 * Run our sub-menu function as soon as the document is `ready`
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	document.addEventListener( 'DOMContentLoaded', function() {
		toggleSubmenuDisplay();
	});

	/**
<<<<<<< HEAD
	 * Run our sub-menu function on selective refresh in the customizer.
=======
	 * Run our sub-menu function on selective refresh in the customizer
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	document.addEventListener( 'customize-preview-menu-refreshed', function( e, params ) {
		if ( 'menu-1' === params.wpNavMenuArgs.theme_location ) {
			toggleSubmenuDisplay();
		}
	});

	/**
<<<<<<< HEAD
	 * Run our sub-menu function every time the window resizes.
=======
	 * Run our sub-menu function every time the window resizes
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	var isResizing = false;
	window.addEventListener( 'resize', function() {
		isResizing = true;
		debounce( function() {
			if ( isResizing ) {
				return;
			}

			toggleSubmenuDisplay();
			isResizing = false;

		}, 150 );
	} );

})();
