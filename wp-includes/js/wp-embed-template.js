(function ( window, document ) {
	'use strict';

	var secret = window.location.hash.replace( /.*secret=([\d\w]{10}).*/, '$1' ),
		supportedBrowser = ( document.querySelector && window.addEventListener ),
		loaded = false,
		resizing;

	function sendEmbedMessage( message, value ) {
		window.parent.postMessage( {
			message: message,
			value: value,
			secret: secret
		}, '*' );
	}

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
			links = document.getElementsByTagName( 'a' ),
			i;

		if ( share_input ) {
			for ( i = 0; i < share_input.length; i++ ) {
				share_input[ i ].addEventListener( 'click', function ( e ) {
					e.target.select();
				} );
			}
		}

		function openSharingDialog() {
			share_dialog.className = share_dialog.className.replace( 'hidden', '' );
			share_input[ 0 ].select();
		}

		function closeSharingDialog() {
			share_dialog.className += ' hidden';
			document.querySelector( '.wp-embed-share-dialog-open' ).focus();
		}

		if ( share_dialog_open ) {
			share_dialog_open.addEventListener( 'click', function ( e ) {
				openSharingDialog();
				e.preventDefault();
			} );
		}

		if ( share_dialog_close ) {
			share_dialog_close.addEventListener( 'click', function ( e ) {
				closeSharingDialog();
				e.preventDefault();
			} );
		}

		function shareClickHandler( e ) {
			var currentTab = document.querySelector( '.wp-embed-share-tab-button [aria-selected="true"]' );
			currentTab.setAttribute( 'aria-selected', 'false' );
			document.querySelector( '#' + currentTab.getAttribute( 'aria-controls' ) ).setAttribute( 'aria-hidden', 'true' );

			e.target.setAttribute( 'aria-selected', 'true' );
			document.querySelector( '#' + e.target.getAttribute( 'aria-controls' ) ).setAttribute( 'aria-hidden', 'false' );
		}

		function shareKeyHandler( e ) {
			var target = e.target,
				previousSibling = target.parentElement.previousElementSibling,
				nextSibling = target.parentElement.nextElementSibling,
				newTab, newTabChild;

			if ( 37 === e.keyCode ) {
				newTab = previousSibling;
			} else if ( 39 === e.keyCode ) {
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

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.keyCode === 27 && -1 === share_dialog.className.indexOf( 'hidden' ) ) {
				closeSharingDialog();
			}
		}, false );

		if ( window.self === window.top ) {
			return;
		}

		/**
		 * Send this document's height to the parent (embedding) site.
		 */
		sendEmbedMessage( 'height', Math.ceil( document.body.getBoundingClientRect().height ) );

		/**
		 * Detect clicks to external (_top) links.
		 */
		function linkClickHandler( e ) {
			var target = e.target,
				href;
			if ( target.hasAttribute( 'href' ) ) {
				href = target.getAttribute( 'href' );
			} else {
				href = target.parentElement.getAttribute( 'href' );
			}

			/**
			 * Send link target to the parent (embedding) site.
			 */
			sendEmbedMessage( 'link', href );
			e.preventDefault();
		}

		for ( i = 0; i < links.length; i++ ) {
			links[ i ].addEventListener( 'click', linkClickHandler );
		}
	}

	/**
	 * Iframe resize handler.
	 */
	function onResize() {
		if ( window.self === window.top ) {
			return;
		}

		clearTimeout( resizing );

		resizing = setTimeout( function () {
			sendEmbedMessage( 'height', Math.ceil( document.body.getBoundingClientRect().height ) );
		}, 100 );
	}

	if ( supportedBrowser ) {
		document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/, '' ) + ' js';
		document.addEventListener( 'DOMContentLoaded', onLoad, false );
		window.addEventListener( 'load', onLoad, false );
		window.addEventListener( 'resize', onResize, false );
	}
})( window, document );
