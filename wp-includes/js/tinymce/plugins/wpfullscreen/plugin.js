/* global tinymce */
/**
 * WP Fullscreen (Distraction Free Writing) TinyMCE plugin
 */
tinymce.PluginManager.add( 'wpfullscreen', function( editor ) {
	var settings = editor.settings,
		oldSize = 0;

	function resize( e ) {
		var deltaSize, myHeight,
			d = editor.getDoc(),
			body = d.body,
			DOM = tinymce.DOM,
			resizeHeight = 250;

		if ( ( e && e.type === 'setcontent' && e.initial ) || editor.settings.inline ) {
			return;
		}

		// Get height differently depending on the browser used
		myHeight = tinymce.Env.ie ? body.scrollHeight : ( tinymce.Env.webkit && body.clientHeight === 0 ? 0 : body.offsetHeight );

		// Don't make it smaller than 250px
		if ( myHeight > 250 ) {
			resizeHeight = myHeight;
		}

		body.scrollTop = 0;

		// Resize content element
		if ( resizeHeight !== oldSize ) {
			deltaSize = resizeHeight - oldSize;
			DOM.setStyle( DOM.get( editor.id + '_ifr' ), 'height', resizeHeight + 'px' );
			oldSize = resizeHeight;

			// WebKit doesn't decrease the size of the body element until the iframe gets resized
			// So we need to continue to resize the iframe down until the size gets fixed
			if ( tinymce.isWebKit && deltaSize < 0 ) {
				resize( e );
			}
		}
	}

	// Register the command
	editor.addCommand( 'wpAutoResize', resize );

	function fullscreenOn() {
		settings.wp_fullscreen = true;
		editor.dom.addClass( editor.getDoc().documentElement, 'wp-fullscreen' );
		// Add listeners for auto-resizing
		editor.on( 'change setcontent paste keyup', resize );
	}

	function fullscreenOff() {
		settings.wp_fullscreen = false;
		editor.dom.removeClass( editor.getDoc().documentElement, 'wp-fullscreen' );
		// Remove listeners for auto-resizing
		editor.off( 'change setcontent paste keyup', resize );
		oldSize = 0;
	}

	// For use from outside the editor.
	editor.addCommand( 'wpFullScreenOn', fullscreenOn );
	editor.addCommand( 'wpFullScreenOff', fullscreenOff );

	function toggleFullscreen() {
		// Toggle DFW mode. For use from inside the editor.
		if ( typeof wp === 'undefined' || ! wp.editor || ! wp.editor.fullscreen ) {
			return;
		}

		if ( editor.getParam('wp_fullscreen') ) {
			wp.editor.fullscreen.off();
		} else {
			wp.editor.fullscreen.on();
		}
	}

	editor.addCommand( 'wpFullScreen', toggleFullscreen );

	editor.on( 'init', function() {
		// Set the editor when initializing from whitin DFW
		if ( editor.getParam('wp_fullscreen') ) {
			fullscreenOn();
		}

		editor.addShortcut( 'alt+shift+w', '', 'wpFullScreen' );
	});

	// Register buttons
	editor.addButton( 'wp_fullscreen', {
		tooltip: 'Distraction Free Writing',
		shortcut: 'Alt+Shift+W',
		onclick: toggleFullscreen,
		classes: 'wp-fullscreen btn widget' // This overwrites all classes on the container!
	});

	editor.addMenuItem( 'wp_fullscreen', {
		text: 'Distraction Free Writing',
		icon: 'wp_fullscreen',
		shortcut: 'Alt+Shift+W',
		context: 'view',
		onclick: toggleFullscreen
	});
});
