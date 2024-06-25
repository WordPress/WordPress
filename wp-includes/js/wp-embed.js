/**
 * WordPress inline HTML embed
 *
 * @since 4.4.0
 * @output wp-includes/js/wp-embed.js
 *
 * Single line comments should not be used since they will break
 * the script when inlined in get_post_embed_html(), specifically
 * when the comments are not stripped out due to SCRIPT_DEBUG
 * being turned on.
 */
(function ( window, document ) {
	'use strict';

	/* Abort for ancient browsers. */
	if ( ! document.querySelector || ! window.addEventListener || typeof URL === 'undefined' ) {
		return;
	}

	/** @namespace wp */
	window.wp = window.wp || {};

	/* Abort if script was already executed. */
	if ( !! window.wp.receiveEmbedMessage ) {
		return;
	}

	/**
	 * Receive embed message.
	 *
	 * @param {MessageEvent} e
	 */
	window.wp.receiveEmbedMessage = function( e ) {
		var data = e.data;

		/* Verify shape of message. */
		if (
			! ( data || data.secret || data.message || data.value ) ||
			/[^a-zA-Z0-9]/.test( data.secret )
		) {
			return;
		}

		var iframes = document.querySelectorAll( 'iframe[data-secret="' + data.secret + '"]' ),
			blockquotes = document.querySelectorAll( 'blockquote[data-secret="' + data.secret + '"]' ),
			allowedProtocols = new RegExp( '^https?:$', 'i' ),
			i, source, height, sourceURL, targetURL;

		for ( i = 0; i < blockquotes.length; i++ ) {
			blockquotes[ i ].style.display = 'none';
		}

		for ( i = 0; i < iframes.length; i++ ) {
			source = iframes[ i ];

			if ( e.source !== source.contentWindow ) {
				continue;
			}

			source.removeAttribute( 'style' );

			if ( 'height' === data.message ) {
				/* Resize the iframe on request. */
				height = parseInt( data.value, 10 );
				if ( height > 1000 ) {
					height = 1000;
				} else if ( ~~height < 200 ) {
					height = 200;
				}

				source.height = height;
			} else if ( 'link' === data.message ) {
				/* Link to a specific URL on request. */
				sourceURL = new URL( source.getAttribute( 'src' ) );
				targetURL = new URL( data.value );

				if (
					allowedProtocols.test( targetURL.protocol ) &&
					targetURL.host === sourceURL.host &&
					document.activeElement === source
				) {
					window.top.location.href = data.value;
				}
			}
		}
	};

	function onLoad() {
		var iframes = document.querySelectorAll( 'iframe.wp-embedded-content' ),
			i, source, secret;

		for ( i = 0; i < iframes.length; i++ ) {
			/** @var {IframeElement} */
			source = iframes[ i ];

			secret = source.getAttribute( 'data-secret' );
			if ( ! secret ) {
				/* Add secret to iframe */
				secret = Math.random().toString( 36 ).substring( 2, 12 );
				source.src += '#?secret=' + secret;
				source.setAttribute( 'data-secret', secret );
			}

			/*
			 * Let post embed window know that the parent is ready for receiving the height message, in case the iframe
			 * loaded before wp-embed.js was loaded. When the ready message is received by the post embed window, the
			 * window will then (re-)send the height message right away.
			 */
			source.contentWindow.postMessage( {
				message: 'ready',
				secret: secret
			}, '*' );
		}
	}

	window.addEventListener( 'message', window.wp.receiveEmbedMessage, false );
	document.addEventListener( 'DOMContentLoaded', onLoad, false );
})( window, document );
