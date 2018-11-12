/**
 * Touch & Keyboard navigation.
 *
 * Contains handlers for touch devices and keyboard navigation.
 */

(function() {

	// Debounce
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

	// Add Class
	function addClass(el, cls) {
		if ( ! el.className.match( '(?:^|\\s)' + cls + '(?!\\S)') ) {
			el.className += ' ' + cls;
		}
	}

	// Delete Class
	function deleteClass(el, cls) {
		el.className = el.className.replace( new RegExp( '(?:^|\\s)' + cls + '(?!\\S)' ),'' );
	}

	// Has Class?
	function hasClass(el, cls) {

		if ( el.className.match( '(?:^|\\s)' + cls + '(?!\\S)' ) ) {
			return true;
		}
	}

	// Toggle Aria Expanded state for screenreaders
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

	// Open sub-menu
	function openSubMenu( currentSubMenu ) {
		'use strict';

		// Update classes
		// classList.add is not supported in IE11
		currentSubMenu.parentElement.className += ' off-canvas';
		currentSubMenu.parentElement.lastElementChild.className += ' expanded-true';

		// Update aria-expanded state
		toggleAriaExpandedState( currentSubMenu.previousSibling );
	}

	// Close sub-menu
	function closeSubMenu( currentSubMenu ) {
		'use strict';

		var menuItem     = getCurrentParent( currentSubMenu, '.menu-item' ); // this.parentNode
		var menuItemAria = menuItem.querySelector('a[aria-expanded]');
		var subMenu      = currentSubMenu.closest('.sub-menu');

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
			toggleAriaExpandedState( menuItemAria );
		}
	}

	// Find first ancestor of an element by selector
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

	// Remove all off-canvas states
	function removeAllFocusStates() {
		'use strict';

		var getFocusedElements = document.querySelectorAll(':hover, :focus, :focus-within');
		var i;

		for ( i = 0; i < getFocusedElements.length; i++) {
			getFocusedElements[i].blur();
		}
	}

	// Matches polyfill for IE11
	if (!Element.prototype.matches) {
		Element.prototype.matches = Element.prototype.msMatchesSelector;
	}

	// Toggle `focus` class to allow sub-menu access on touch screens.
	function toggleSubmenuDisplay() {

		document.addEventListener('touchstart', function(event) {

			if ( event.target.matches('a') ) {

				var url = event.target.getAttribute( 'href' ) ? event.target.getAttribute( 'href' ) : '';

				// If there’s a link, go to it on touchend
				if ( '#' !== url && '' !== url ) {
					window.location = url;

				// Open submenu if url is #
				} else if ( '#' === url && event.target.nextSibling.matches('.submenu-expand') ) {

					openSubMenu( event.target );

				// Prevent default touch events
				} else {

					event.preventDefault();
				}
			}

			// Check if .submenu-expand is touched
			if ( event.target.matches('.submenu-expand') ) {
				openSubMenu(event.target);

			// Check if child of .submenu-expand is touched
			} else if ( null != getCurrentParent( event.target, '.submenu-expand' ) && getCurrentParent( event.target, '.submenu-expand' ).matches( '.submenu-expand' ) ) {
				openSubMenu( getCurrentParent( event.target, '.submenu-expand' ) );

			// Check if .menu-item-link-return is touched
			} else if ( event.target.matches('.menu-item-link-return') ) {
				closeSubMenu( event.target );

			// Check if child of .menu-item-link-return is touched
			} else if ( null != getCurrentParent( event.target, '.menu-item-link-return' ) && getCurrentParent( event.target, '.menu-item-link-return' ).matches( '.menu-item-link-return' ) ) {
				closeSubMenu( event.target );
			}

			// Prevent default mouse/focus events
			removeAllFocusStates();

		}, false);

		document.addEventListener('touchend', function(event) {

			var mainNav = getCurrentParent( event.target, '.main-navigation' );

			if ( null != mainNav && hasClass( mainNav, '.main-navigation' ) ) {
				// Prevent default mouse events
				event.preventDefault();
			}

			// Prevent default mouse events
			event.preventDefault();

			// Prevent default mouse/focus events
			removeAllFocusStates();

		}, false);

		document.addEventListener('focus', function(event) {

			if ( event.target.matches('.main-navigation > div > ul > li > a') ) {

				// Remove Focuse elements in sibling div
				var currentDiv        = getCurrentParent( event.target, 'div' );
				var currentDivSibling = currentDiv.previousElementSibling === null ? currentDiv.nextElementSibling : currentDiv.previousElementSibling;
				var focusedElement    = currentDivSibling.querySelector( '.is-focused' );
				var focusedClass      = 'is-focused';
				var prevLi            = event.target.parentNode.previousElementSibling;
				var nextLi            = event.target.parentNode.nextElementSibling;

				if ( null !== focusedElement && null !== hasClass( focusedElement, focusedClass ) ) {
					deleteClass( focusedElement, focusedClass );
				}

				// Add .is-focused class to top-level ul
				if ( event.target.parentNode.querySelector( '.main-navigation ul ul') ) {
					addClass( event.target.parentNode, focusedClass );
				}

				// Check for previous li
				if ( prevLi && hasClass( prevLi, focusedClass ) ) {
					deleteClass( prevLi, focusedClass );
				}

				// Check for next li
				if ( nextLi && hasClass( nextLi, focusedClass ) ) {
					deleteClass( nextLi, focusedClass );
				}
			}
		}, true);
	}

	// Run our sub-menu function as soon as the document is `ready`
	document.addEventListener( 'DOMContentLoaded', function() {
		toggleSubmenuDisplay();
	});
	// Annnnnd also every time the window resizes
	var isResizing = false;
	window.addEventListener( 'resize',
		debounce( function() {
			if ( isResizing ) {
				return;
			}

			isResizing = true;
			setTimeout( function() {
				toggleSubmenuDisplay();
				isResizing = false;
			}, 150 );
		} )
	);

})();
