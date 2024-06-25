/* global setTimeout */
wp.domReady( function() {
	// Unregister "Wide" Separator Style.
	wp.blocks.unregisterBlockStyle( 'core/separator', 'wide' );

	// Add to ".block-editor__typewriter" the "is-dark-theme" class if needed.
	function twentytwentyoneCopyDarkThemeClass() {
		var editor,
			attemptDelay = 25,
			attempt = 0,
			maxAttempts = 10;

		if ( ! document.body.classList.contains( 'is-dark-theme' ) ) {
			return;
		}

		editor = document.querySelector( '.block-editor__typewriter' );
		if ( null === editor ) {
			// Try again.
			if ( attempt < maxAttempts ) {
				setTimeout( function() {
					twentytwentyoneCopyDarkThemeClass();
				}, attemptDelay );

				// Increment the attempts counter.
				attempt++;

				// Double the delay, give the server some time to breathe.
				attemptDelay *= 2;
			}
			return;
		}

		editor.classList.add( 'is-dark-theme' );
	}

	twentytwentyoneCopyDarkThemeClass();
} );
