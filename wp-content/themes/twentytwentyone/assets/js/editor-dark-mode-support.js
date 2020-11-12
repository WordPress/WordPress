/* global ajaxurl, XMLHttpRequest, darkModeInitialLoad, setTimeout */

// Check the body class to determine if we want to add the toggler and handle dark-mode or not.
if ( document.body.classList.contains( 'twentytwentyone-supports-dark-theme' ) ) {
	// Add the toggler.
	twentytwentyoneDarkModeEditorToggle();
}

/**
 * Make an AJAX request, inject the toggle and call any functions that need to run.
 *
 * @since 1.0.0
 *
 * @return {void}
 */
function twentytwentyoneDarkModeEditorToggle() {
	var request = new XMLHttpRequest();

	// Define the request.
	request.open( 'POST', ajaxurl, true );

	// Add headers.
	request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );

	// On success call funtions that need to run.
	request.onload = function() {
		var selector = '.editor-styles-wrapper,.edit-post-visual-editor',
			editor,
			attemptDelay = 25,
			attempt = 0,
			maxAttempts = 8;

		if ( 200 <= this.status && 400 > this.status ) {
			editor = document.querySelector( selector );

			if ( null === editor ) {
				// Try again.
				if ( attempt < maxAttempts ) {
					setTimeout( function() {
						twentytwentyoneDarkModeEditorToggle();
					}, attemptDelay );

					// Increment the attempts counter.
					attempt++;

					// Double the delay, give the server some time to breathe.
					attemptDelay *= 2;
				}
				return;
			}
			// Inject the toggle.
			document.querySelector( selector ).insertAdjacentHTML( 'afterbegin', this.response );

			// Run toggler script.
			darkModeInitialLoad();

			// Switch editor styles if needed.
			twentytwentyoneDarkModeEditorToggleEditorStyles();
		}
	};

	// Send the request.
	request.send( 'action=tt1_dark_mode_editor_switch' );
}

/**
 * Toggle the editor dark styles depending on the user's preferences in the toggler.
 *
 * @since 1.0.0
 *
 * @return {void}
 */
function twentytwentyoneDarkModeEditorToggleEditorStyles() {
	var toggler = document.getElementById( 'dark-mode-toggler' );

	if ( 'true' === toggler.getAttribute( 'aria-pressed' ) ) {
		document.body.classList.add( 'is-dark-theme' );
		document.documentElement.classList.add( 'is-dark-theme' );
		document.querySelector( '.block-editor__typewriter' ).classList.add( 'is-dark-theme' );
	}
}
