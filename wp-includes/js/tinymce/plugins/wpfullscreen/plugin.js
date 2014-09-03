/* global tinymce */
/**
 * WP Fullscreen (Distraction Free Writing) TinyMCE plugin
 */
tinymce.PluginManager.add( 'wpfullscreen', function( editor ) {
	var settings = editor.settings;

	function fullscreenOn() {
		settings.wp_fullscreen = true;
		editor.dom.addClass( editor.getDoc().documentElement, 'wp-fullscreen' );
		// Start auto-resizing
		editor.execCommand( 'wpAutoResizeOn' );
	}

	function fullscreenOff() {
		settings.wp_fullscreen = false;
		editor.dom.removeClass( editor.getDoc().documentElement, 'wp-fullscreen' );
		// Stop auto-resizing
		editor.execCommand( 'wpAutoResizeOff' );
	}

	// For use from outside the editor.
	editor.addCommand( 'wpFullScreenOn', fullscreenOn );
	editor.addCommand( 'wpFullScreenOff', fullscreenOff );

	function getExtAPI() {
		return ( typeof wp !== 'undefined' && wp.editor && wp.editor.fullscreen );
	}

	// Toggle DFW mode. For use from inside the editor.
	function toggleFullscreen() {
		var fullscreen = getExtAPI();

		if ( fullscreen ) {
			if ( editor.getParam('wp_fullscreen') ) {
				fullscreen.off();
			} else {
				fullscreen.on();
			}
		}
	}

	editor.addCommand( 'wpFullScreen', toggleFullscreen );

	editor.on( 'keydown', function( event ) {
		var fullscreen;

		// Turn fullscreen off when Esc is pressed.
		if ( event.keyCode === 27 && ( fullscreen = getExtAPI() ) && fullscreen.settings.visible ) {
			fullscreen.off();
		}
	});

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
