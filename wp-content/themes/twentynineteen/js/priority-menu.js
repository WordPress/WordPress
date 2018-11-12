(function() {
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
	function showElement(element) {
		// classList.remove is not supported in IE11
		element.className = element.className.replace('is-hidden', '');
	}

	/**
	 * Hides an element by removing the hidden className.
	 *
	 * @param {Element} element
	 */
	function hideElement(element) {
		// classList.add is not supported in IE11
		if (!element.classList.contains('is-hidden')) {
			element.className += ' is-hidden';
		}
	}

	/**
	 * Toggles the element visibility.
	 *
	 * @param {Element} element
	 */
	function toggleElementVisibility(element) {
		if (element.classList.contains('is-hidden')) {
			showElement(element);
		} else {
			hideElement(element);
		}
	}

	var navContainer = document.querySelector('.main-navigation');
	// Adds the necessary UI to operate the menu.
	var toggleButton = document.querySelector('.main-navigation .main-menu-more-toggle');
	var visibleList = document.querySelector('.main-navigation .main-menu[id]');
	var hiddenList = document.querySelector('.main-navigation .hidden-links');
	var breaks = [];

	/**
	 * Returns the currently available space in the menu container.
	 *
	 * @returns {number} Available space
	 */
	function getAvailableSpace() {
		return toggleButton.classList.contains('hidden') ? navContainer.offsetWidth : navContainer.offsetWidth - toggleButton.offsetWidth - 50;
	}

	/**
	 * Returns whether the current menu is overflowing or not.
	 *
	 * @returns {boolean} Is overflowing
	 */
	function isOverflowingNavivation() {
		return visibleList.offsetWidth > getAvailableSpace();
	}

	/**
	 * Refreshes the list item from the menu depending on the menu size
	 */
	function updateNavigationMenu() {

		if (isOverflowingNavivation()) {
			// Record the width of the list
			breaks.push(visibleList.offsetWidth);
			// Move item to the hidden list
			prependElement(hiddenList, visibleList.lastChild);
			// Show the toggle button
			showElement(toggleButton);
		} else {
			// There is space for another item in the nav
			if (getAvailableSpace() > breaks[breaks.length - 1]) {
				// Move the item to the visible list
				visibleList.appendChild(hiddenList.firstChild.nextSibling);
				breaks.pop();
			}

			// Hide the dropdown btn if hidden list is empty
			if (breaks.length < 2) {
				hideElement(toggleButton);
				hideElement(hiddenList);
			}
		}

		// Recur if the visible list is still overflowing the nav
		if (isOverflowingNavivation()) {
			updateNavigationMenu();
		}
	}

	// Event listeners
	// Run our sub-menu function as soon as the document is `ready`
	document.addEventListener( 'DOMContentLoaded', function() {
		updateNavigationMenu();
	});

	window.addEventListener('load', function() {
		updateNavigationMenu();
	});

	window.addEventListener('resize', function() {
		updateNavigationMenu();
	});

	toggleButton.addEventListener('click', function() {
		toggleElementVisibility(hiddenList);
	});

	updateNavigationMenu();
})();
