/**
 * Dark mode toggle functionality.
 *
 * @output wp-admin/js/dark-mode-toggle.js
 */

/* global ajaxurl, wpDarkModeToggle */
( function( document, window ) {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function() {
		var toggleButton = document.getElementById( 'wp-admin-bar-dark-mode-toggle' );

		if ( ! toggleButton ) {
			return;
		}

		var toggleLink = toggleButton.querySelector( '.ab-item' );

		if ( ! toggleLink ) {
			return;
		}

		toggleLink.addEventListener( 'click', function( event ) {
			event.preventDefault();

			var isDarkMode = toggleButton.classList.contains( 'dark-mode-active' );
			var enableDark = ! isDarkMode;

			// Send AJAX request.
			var xhr = new XMLHttpRequest();
			xhr.open( 'POST', ajaxurl, true );
			xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

			xhr.onreadystatechange = function() {
				if ( xhr.readyState === 4 && xhr.status === 200 ) {
					try {
						var response = JSON.parse( xhr.responseText );

						if ( response.success ) {
							// Update body classes.
							document.body.classList.remove( response.data.previousScheme );
							document.body.classList.add( response.data.currentScheme );

							// Update toggle button state.
							if ( response.data.darkModeEnabled ) {
								toggleButton.classList.add( 'dark-mode-active' );
							} else {
								toggleButton.classList.remove( 'dark-mode-active' );
							}

							// Update screen reader text.
							var srText = toggleButton.querySelector( '.screen-reader-text' );
							if ( srText ) {
								srText.textContent = response.data.darkModeEnabled
									? wpDarkModeToggle.lightModeText
									: wpDarkModeToggle.darkModeText;
							}

							// Load new color stylesheet.
							var colorsCss = document.getElementById( 'colors-css' );
							if ( response.data.cssUrl ) {
								if ( colorsCss ) {
									colorsCss.href = response.data.cssUrl;
								} else {
									var link = document.createElement( 'link' );
									link.id = 'colors-css';
									link.rel = 'stylesheet';
									link.href = response.data.cssUrl;
									document.head.appendChild( link );
								}
							} else if ( colorsCss ) {
								// Fresh scheme has no CSS file.
								colorsCss.parentNode.removeChild( colorsCss );
							}

							// Update SVG icon colors if wp.svgPainter exists.
							if ( typeof wp !== 'undefined' && wp.svgPainter && response.data.iconColors ) {
								wp.svgPainter.setColors( response.data.iconColors );
								wp.svgPainter.paint();
							}
						}
					} catch ( e ) {
						// Silent fail.
					}
				}
			};

			xhr.send(
				'action=toggle_dark_mode' +
				'&enable_dark=' + ( enableDark ? 'true' : 'false' ) +
				'&nonce=' + encodeURIComponent( wpDarkModeToggle.nonce )
			);
		} );

		// Handle keyboard accessibility.
		toggleLink.addEventListener( 'keydown', function( event ) {
			if ( event.which === 13 || event.which === 32 ) { // Enter or Space.
				event.preventDefault();
				toggleLink.click();
			}
		} );
	} );
} )( document, window );
