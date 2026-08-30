/**
 * @output wp-includes/js/wp-embed-template.js
 */

/**
 * IIFE setup function.
 *
 * @param {Window} window Global window object.
 * @param {Document} document Global document object.
 */
(function ( window, document ) {
	'use strict';

	var supportedBrowser = ( document.querySelector && window.addEventListener ),
		loaded = false,
		secret,
		secretTimeout,
		resizing;

	function sendEmbedMessage( message, value ) {
		window.parent.postMessage( {
			message: message,
			value: value,
			secret: secret
		}, '*' );
	}

	/**
	 * Send the height message to the parent window.
	 */
	function sendHeightMessage() {
		sendEmbedMessage( 'height', Math.ceil( document.body.getBoundingClientRect().height ) );
	}

	/**
	 * Runs on DomContentLoaded and load event.
	 *
	 * Exits early on the load event if the earlier event has
	 * successfully fired.
	 */
	function onLoad() {
		if ( loaded ) {
			return;
		}
		loaded = true;

		var share_dialog = document.querySelector( '.wp-embed-share-dialog' ),
			share_dialog_open = document.querySelector( '.wp-embed-share-dialog-open' ),
			share_dialog_close = document.querySelector( '.wp-embed-share-dialog-close' ),
			share_input = document.querySelectorAll( '.wp-embed-share-input' ),
			share_dialog_tabs = document.querySelectorAll( '.wp-embed-share-tab-button button' ),
			featured_image = document.querySelector( '.wp-embed-featured-image img' ),
			i;

		if ( share_input ) {
			for ( i = 0; i < share_input.length; i++ ) {
				share_input[ i ].addEventListener( 'click', function ( event ) {
					event.target.select();
				} );
			}
		}

		/**
		 * Open share dialog within embed iframe.
		 */
		function openSharingDialog() {
			share_dialog.className = share_dialog.className.replace( 'hidden', '' );
			// Initial focus should go on the currently selected tab in the dialog.
			document.querySelector( '.wp-embed-share-tab-button [aria-selected="true"]' ).focus();
		}

		/**
		 * Close share dialog within embed iframe.
		 */
		function closeSharingDialog() {
			share_dialog.className += ' hidden';
			document.querySelector( '.wp-embed-share-dialog-open' ).focus();
		}

		if ( share_dialog_open ) {
			share_dialog_open.addEventListener( 'click', function () {
				openSharingDialog();
			} );
		}

		if ( share_dialog_close ) {
			share_dialog_close.addEventListener( 'click', function () {
				closeSharingDialog();
			} );
		}

		/**
		 * Click event handler for share button.
		 *
		 * @param {MouseEvent} event Event handler.
		 */
		function shareClickHandler( event ) {
			var currentTab = document.querySelector( '.wp-embed-share-tab-button [aria-selected="true"]' );
			currentTab.setAttribute( 'aria-selected', 'false' );
			document.querySelector( '#' + currentTab.getAttribute( 'aria-controls' ) ).setAttribute( 'aria-hidden', 'true' );

			event.target.setAttribute( 'aria-selected', 'true' );
			document.querySelector( '#' + event.target.getAttribute( 'aria-controls' ) ).setAttribute( 'aria-hidden', 'false' );
		}

		/**
		 * Keyboard event handler for share button.
		 *
		 * @param {KeyboardEvent} event Event handler.
		 * @return {boolean|void} Returns false if default action is to be prevented.
		 */
		function shareKeyHandler( event ) {
			var target = event.target,
				previousSibling = target.parentElement.previousElementSibling,
				nextSibling = target.parentElement.nextElementSibling,
				newTab, newTabChild;

			if ( 37 === event.keyCode ) {
				newTab = previousSibling;
			} else if ( 39 === event.keyCode ) {
				newTab = nextSibling;
			} else {
				return false;
			}

			if ( 'rtl' === document.documentElement.getAttribute( 'dir' ) ) {
				newTab = ( newTab === previousSibling ) ? nextSibling : previousSibling;
			}

			if ( newTab ) {
				newTabChild = newTab.firstElementChild;

				target.setAttribute( 'tabindex', '-1' );
				target.setAttribute( 'aria-selected', false );
				document.querySelector( '#' + target.getAttribute( 'aria-controls' ) ).setAttribute( 'aria-hidden', 'true' );

				newTabChild.setAttribute( 'tabindex', '0' );
				newTabChild.setAttribute( 'aria-selected', 'true' );
				newTabChild.focus();
				document.querySelector( '#' + newTabChild.getAttribute( 'aria-controls' ) ).setAttribute( 'aria-hidden', 'false' );
			}
		}

		if ( share_dialog_tabs ) {
			for ( i = 0; i < share_dialog_tabs.length; i++ ) {
				share_dialog_tabs[ i ].addEventListener( 'click', shareClickHandler );

				share_dialog_tabs[ i ].addEventListener( 'keydown', shareKeyHandler );
			}
		}

		document.addEventListener( 'keydown', function ( event ) {
			if ( 27 === event.keyCode && -1 === share_dialog.className.indexOf( 'hidden' ) ) {
				closeSharingDialog();
			} else if ( 9 === event.keyCode ) {
				constrainTabbing( event );
			}
		}, false );

		/**
		 * Constrain tabbing to share dialog when open.
		 *
		 * @param {KeyboardEvent} event Event object.
		 */
		function constrainTabbing( event ) {
			// Need to re-get the selected tab each time.
			var firstFocusable = document.querySelector( '.wp-embed-share-tab-button [aria-selected="true"]' );

			if ( share_dialog_close === event.target && ! event.shiftKey ) {
				firstFocusable.focus();
				event.preventDefault();
			} else if ( firstFocusable === event.target && event.shiftKey ) {
				share_dialog_close.focus();
				event.preventDefault();
			}
		}

		if ( window.self === window.top ) {
			return;
		}

		// Send this document's height to the parent (embedding) site.
		sendHeightMessage();

		// Send the document's height again after the featured image has been loaded.
		if ( featured_image ) {
			featured_image.addEventListener( 'load', sendHeightMessage );
		}

		/**
		 * Detect clicks to external (_top) links.
		 *
		 * @param {MouseEvent} event
		 */
		function linkClickHandler( event ) {
			/*
			 * The href property resolves to an absolute URL, which the parent window requires.
			 * Elements whose href is not a string, such as SVG anchors, are skipped.
			 */
			var link = event.target.closest( '[href]' ),
				href = 'string' === typeof link?.href ? link.href : null;

			// Only catch clicks from the primary mouse button, without any modifiers.
			if ( event.altKey || event.ctrlKey || event.metaKey || event.shiftKey ) {
				return;
			}

			// Send link target to the parent (embedding) site.
			if ( href ) {
				sendEmbedMessage( 'link', href );
				event.preventDefault();
			}
		}

		document.addEventListener( 'click', linkClickHandler );
	}

	/**
	 * Iframe resize handler.
	 */
	function onResize() {
		if ( window.self === window.top ) {
			return;
		}

		clearTimeout( resizing );

		resizing = setTimeout( sendHeightMessage, 100 );
	}

	/**
	 * Message handler.
	 *
	 * @param {MessageEvent} event
	 */
	function onMessage( event ) {
		var data = event.data;

		if ( ! data ) {
			return;
		}

		if ( event.source !== window.parent ) {
			return;
		}

		if ( ! ( data.secret || data.message ) ) {
			return;
		}

		if ( data.secret !== secret ) {
			return;
		}

		if ( 'ready' === data.message ) {
			sendHeightMessage();
		}
	}

	/**
	 * Re-get the secret when it was added later on.
	 */
	function getSecret() {
		if ( window.self === window.top || !!secret ) {
			return;
		}

		secret = window.location.hash.replace( /.*secret=([\d\w]{10}).*/, '$1' );

		clearTimeout( secretTimeout );

		secretTimeout = setTimeout( function () {
			getSecret();
		}, 100 );
	}

	if ( supportedBrowser ) {
		getSecret();
		document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/, '' ) + ' js';
		document.addEventListener( 'DOMContentLoaded', onLoad, false );
		window.addEventListener( 'load', onLoad, false );
		window.addEventListener( 'resize', onResize, false );
		window.addEventListener( 'message', onMessage, false );
	}
})( window, document );
