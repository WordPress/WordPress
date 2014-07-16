/**
 * plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

// Forked for WordPress so it can be turned on/off after loading.

/*global tinymce:true */
/*eslint no-nested-ternary:0 */

/**
 * Auto Resize
 *
 * This plugin automatically resizes the content area to fit its content height.
 * It will retain a minimum height, which is the height of the content area when
 * it's initialized.
 */
tinymce.PluginManager.add( 'wpautoresize', function( editor ) {
	var settings = editor.settings, oldSize = 0;

	function isFullscreen() {
		return editor.plugins.fullscreen && editor.plugins.fullscreen.isFullscreen();
	}

	if ( editor.settings.inline ) {
		return;
	}

	/**
	 * This method gets executed each time the editor needs to resize.
	 */
	function resize( e ) {
		var deltaSize, doc, body, docElm, DOM = tinymce.DOM, resizeHeight, myHeight, marginTop, marginBottom;

		doc = editor.getDoc();
		if (!doc) {
			return;
		}

		body = doc.body;
		docElm = doc.documentElement;
		resizeHeight = settings.autoresize_min_height;

		if ( ! body || ( e && e.type === 'setcontent' && e.initial ) || isFullscreen() ) {
			if ( body && docElm ) {
				body.style.overflowY = 'auto';
				docElm.style.overflowY = 'auto'; // Old IE
			}

			return;
		}

		// Calculate outer height of the body element using CSS styles
		marginTop = editor.dom.getStyle( body, 'margin-top', true );
		marginBottom = editor.dom.getStyle( body, 'margin-bottom', true );
		myHeight = body.offsetHeight + parseInt( marginTop, 10 ) + parseInt( marginBottom, 10 );

		// Make sure we have a valid height
		if ( isNaN( myHeight ) || myHeight <= 0 ) {
			// Get height differently depending on the browser used
			myHeight = tinymce.Env.ie ? body.scrollHeight : ( tinymce.Env.webkit && body.clientHeight === 0 ? 0 : body.offsetHeight );
		}

		// Don't make it smaller than the minimum height
		if ( myHeight > settings.autoresize_min_height ) {
			resizeHeight = myHeight;
		}

		// If a maximum height has been defined don't exceed this height
		if ( settings.autoresize_max_height && myHeight > settings.autoresize_max_height ) {
			resizeHeight = settings.autoresize_max_height;
			body.style.overflowY = 'auto';
			docElm.style.overflowY = 'auto'; // Old IE
		} else {
			body.style.overflowY = 'hidden';
			docElm.style.overflowY = 'hidden'; // Old IE
			body.scrollTop = 0;
		}

		// Resize content element
		if (resizeHeight !== oldSize) {
			deltaSize = resizeHeight - oldSize;
			DOM.setStyle( DOM.get( editor.id + '_ifr'), 'height', resizeHeight + 'px' );
			oldSize = resizeHeight;

			// WebKit doesn't decrease the size of the body element until the iframe gets resized
			// So we need to continue to resize the iframe down until the size gets fixed
			if ( tinymce.isWebKit && deltaSize < 0 ) {
				resize( e );
			}
		}
	}

	/**
	 * Calls the resize x times in 100ms intervals. We can't wait for load events since
	 * the CSS files might load async.
	 */
	function wait( times, interval, callback ) {
		setTimeout( function() {
			resize({});

			if ( times-- ) {
				wait( times, interval, callback );
			} else if ( callback ) {
				callback();
			}
		}, interval );
	}

	// Define minimum height
	settings.autoresize_min_height = parseInt(editor.getParam( 'autoresize_min_height', editor.getElement().offsetHeight), 10 );

	// Define maximum height
	settings.autoresize_max_height = parseInt(editor.getParam( 'autoresize_max_height', 0), 10 );

	function on() {
		if ( ! editor.dom.hasClass( editor.getBody(), 'wp-autoresize' ) ) {
			editor.dom.addClass( editor.getBody(), 'wp-autoresize' );
			// Add appropriate listeners for resizing the content area
			editor.on( 'nodechange setcontent keyup FullscreenStateChanged', resize );
		}
	}

	function off() {
		// Don't turn off if the setting is 'on'
		if ( ! settings.wp_autoresize_on ) {
			editor.dom.removeClass( editor.getBody(), 'wp-autoresize' );
			editor.off( 'nodechange setcontent keyup FullscreenStateChanged', resize );
			oldSize = 0;
		}
	}

	if ( settings.wp_autoresize_on ) {
		// Turn resizing on when the editor loads
		editor.on( 'init', function() {
			editor.dom.addClass( editor.getBody(), 'wp-autoresize' );
		});

		editor.on( 'nodechange keyup FullscreenStateChanged', resize );

		editor.on( 'setcontent', function() {
			wait( 3, 100 );
		});

		if ( editor.getParam( 'autoresize_on_init', true ) ) {
			editor.on( 'init', function() {
				// Hit it 20 times in 100 ms intervals
				wait( 10, 200, function() {
					// Hit it 5 times in 1 sec intervals
					wait( 5, 1000 );
				});
			});
		}
	}

	// Register the command
	editor.addCommand( 'wpAutoResize', resize );

	// On/off
	editor.addCommand( 'wpAutoResizeOn', on );
	editor.addCommand( 'wpAutoResizeOff', off );
});
