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
	 * Prepends an element to a container.
	 *
	 * @param {Element} container
	 * @param {Element} element
	 */
	function prependElement(container, element) {
		if (container.firstChild.nextSibling) {
			return container.insertBefore(element, container.firstChild.nextSibling);
		} else {
			return container.appendChild(element);
		}
	}

	/**
	 * Shows an element by adding a hidden className.
	 *
	 * @param {Element} element
	 */
	function showButton(element) {
<<<<<<< HEAD
		// classList.remove is not supported in IE11.
=======
		// classList.remove is not supported in IE11
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		element.className = element.className.replace('is-empty', '');
	}

	/**
	 * Hides an element by removing the hidden className.
	 *
	 * @param {Element} element
	 */
	function hideButton(element) {
<<<<<<< HEAD
		// classList.add is not supported in IE11.
=======
		// classList.add is not supported in IE11
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		if (!element.classList.contains('is-empty')) {
			element.className += ' is-empty';
		}
	}

	/**
	 * Returns the currently available space in the menu container.
	 *
	 * @returns {number} Available space
	 */
	function getAvailableSpace( button, container ) {
		return container.offsetWidth - button.offsetWidth - 22;
	}

	/**
	 * Returns whether the current menu is overflowing or not.
	 *
	 * @returns {boolean} Is overflowing
	 */
	function isOverflowingNavivation( list, button, container ) {
		return list.offsetWidth > getAvailableSpace( button, container );
	}

	/**
<<<<<<< HEAD
	 * Set menu container variable.
=======
	 * Set menu container variable
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	var navContainer = document.querySelector('.main-navigation');
	var breaks       = [];

	/**
<<<<<<< HEAD
	 * Let’s bail if we our menu doesn't exist.
=======
	 * Let’s bail if we our menu doesn't exist
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	if ( ! navContainer ) {
		return;
	}

	/**
<<<<<<< HEAD
	 * Refreshes the list item from the menu depending on the menu size.
=======
	 * Refreshes the list item from the menu depending on the menu size
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	function updateNavigationMenu( container ) {

		/**
<<<<<<< HEAD
		 * Let’s bail if our menu is empty.
=======
		 * Let’s bail if our menu is empty
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		 */
		if ( ! container.parentNode.querySelector('.main-menu[id]') ) {
			return;
		}

		// Adds the necessary UI to operate the menu.
		var visibleList  = container.parentNode.querySelector('.main-menu[id]');
		var hiddenList   = visibleList.parentNode.nextElementSibling.querySelector('.hidden-links');
		var toggleButton = visibleList.parentNode.nextElementSibling.querySelector('.main-menu-more-toggle');

		if ( isOverflowingNavivation( visibleList, toggleButton, container ) ) {

<<<<<<< HEAD
			// Record the width of the list.
			breaks.push( visibleList.offsetWidth );
			// Move last item to the hidden list.
			prependElement( hiddenList, ! visibleList.lastChild || null === visibleList.lastChild ? visibleList.previousElementSibling : visibleList.lastChild );
			// Show the toggle button.
=======
			// Record the width of the list
			breaks.push( visibleList.offsetWidth );
			// Move last item to the hidden list
			prependElement( hiddenList, ! visibleList.lastChild || null === visibleList.lastChild ? visibleList.previousElementSibling : visibleList.lastChild );
			// Show the toggle button
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			showButton( toggleButton );

		} else {

<<<<<<< HEAD
			// There is space for another item in the nav.
			if ( getAvailableSpace( toggleButton, container ) > breaks[breaks.length - 1] ) {
				// Move the item to the visible list.
=======
			// There is space for another item in the nav
			if ( getAvailableSpace( toggleButton, container ) > breaks[breaks.length - 1] ) {
				// Move the item to the visible list
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				visibleList.appendChild( hiddenList.firstChild.nextSibling );
				breaks.pop();
			}

<<<<<<< HEAD
			// Hide the dropdown btn if hidden list is empty.
=======
			// Hide the dropdown btn if hidden list is empty
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			if (breaks.length < 2) {
				hideButton( toggleButton );
			}
		}

<<<<<<< HEAD
		// Recur if the visible list is still overflowing the nav.
=======
		// Recur if the visible list is still overflowing the nav
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		if ( isOverflowingNavivation( visibleList, toggleButton, container ) ) {
			updateNavigationMenu( container );
		}
	}

	/**
<<<<<<< HEAD
	 * Run our priority+ function as soon as the document is `ready`.
=======
	 * Run our priority+ function as soon as the document is `ready`
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	document.addEventListener( 'DOMContentLoaded', function() {

		updateNavigationMenu( navContainer );

<<<<<<< HEAD
		// Also, run our priority+ function on selective refresh in the customizer.
=======
		// Also, run our priority+ function on selective refresh in the customizer
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		var hasSelectiveRefresh = (
			'undefined' !== typeof wp &&
			wp.customize &&
			wp.customize.selectiveRefresh &&
			wp.customize.navMenusPreview.NavMenuInstancePartial
		);

		if ( hasSelectiveRefresh ) {
<<<<<<< HEAD
			// Re-run our priority+ function on Nav Menu partial refreshes.
=======
			// Re-run our priority+ function on Nav Menu partial refreshes
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function ( placement ) {

				var isNewNavMenu = (
					placement &&
					placement.partial.id.includes( 'nav_menu_instance' ) &&
					'null' !== placement.container[0].parentNode &&
					placement.container[0].parentNode.classList.contains( 'main-navigation' )
				);

				if ( isNewNavMenu ) {
					updateNavigationMenu( placement.container[0].parentNode );
				}
			});
        }
	});

	/**
<<<<<<< HEAD
	 * Run our priority+ function on load.
=======
	 * Run our priority+ function on load
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	window.addEventListener( 'load', function() {
		updateNavigationMenu( navContainer );
	});

	/**
<<<<<<< HEAD
	 * Run our priority+ function every time the window resizes.
=======
	 * Run our priority+ function every time the window resizes
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	var isResizing = false;
	window.addEventListener( 'resize',
		debounce( function() {
			if ( isResizing ) {
				return;
			}

			isResizing = true;
			setTimeout( function() {
				updateNavigationMenu( navContainer );
				isResizing = false;
			}, 150 );
		} )
	);

	/**
<<<<<<< HEAD
	 * Run our priority+ function.
=======
	 * Run our priority+ function
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	 */
	updateNavigationMenu( navContainer );

})();
