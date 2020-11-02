/* global ajaxurl, XMLHttpRequest, ResizeObserver, darkModeInitialLoad, setTimeout */

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

			// Re-position the toggle.
			twentytwentyoneDarkModeEditorTogglePosition();

			// Add an observer so the toggle gets re-positioned when the sidebar opens/closes.
			twentytwentyoneDarkModeEditorTogglePositionObserver();

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
	}

	toggler.addEventListener( 'click', function() {
		if ( 'true' === toggler.getAttribute( 'aria-pressed' ) ) {
			document.body.classList.add( 'is-dark-theme' );
		} else {
			document.body.classList.remove( 'is-dark-theme' );
		}
	} );
}

/**
 * Reposition the toggle inside the editor wrapper.
 *
 * @since 1.0.0
 *
 * @return {void}
 */
function twentytwentyoneDarkModeEditorTogglePosition() {
	var toggle = document.getElementById( 'dark-mode-toggler' ),
		toggleWidth,
		workSpace,
		workSpaceWidth,
		attempt = 0,
		attemptDelay = 25,
		maxAttempts = 10;

	if ( null === toggle ) {
		// Try again.
		if ( attempt < maxAttempts ) {
			setTimeout( function() {
				twentytwentyoneDarkModeEditorTogglePosition();
			}, attemptDelay );

			attempt++;
			attemptDelay *= 2;
		}
		return;
	}

	toggleWidth = window.getComputedStyle( document.getElementById( 'dark-mode-toggler' ) ).width;
	workSpace = document.querySelector( '.editor-styles-wrapper,.edit-post-visual-editor' );
	workSpaceWidth = window.getComputedStyle( workSpace ).width;

	// Add styles to reposition toggle.
	toggle.style.position = 'fixed';
	toggle.style.bottom = '30px';
	if ( document.body.classList.contains( 'is-fullscreen-mode' ) ) {
		toggle.style.left = 'calc(' + workSpaceWidth + ' - ' + toggleWidth + ' - 5px)';
	} else {
		toggle.style.left = 'calc(' + workSpaceWidth + ' - ' + toggleWidth + ' + 155px)';
	}
}

/**
 * Add a ResizeObserver to the editor wrapper
 * and trigger the toggle repositioning when needed.
 *
 * @since 1.0.0
 *
 * @return {void}
 */
function twentytwentyoneDarkModeEditorTogglePositionObserver() {
	var observer = new ResizeObserver( function() {
		twentytwentyoneDarkModeEditorTogglePosition();
	} );
	observer.observe( document.querySelector( '.editor-styles-wrapper,.edit-post-visual-editor' ) );
}
