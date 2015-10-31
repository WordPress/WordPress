(function ( window, document ) {
	'use strict';

	var supportedBrowser = ( document.querySelector && window.addEventListener ),
		loaded = false;

	window.wp = window.wp || {};

	if ( !! window.wp.receiveEmbedMessage ) {
		return;
	}

	window.wp.receiveEmbedMessage = function( e ) {
		var data = e.data;
		if ( ! ( data.secret || data.message || data.value ) ) {
			return;
		}

		var iframes = document.querySelectorAll( 'iframe[data-secret="' + data.secret + '"]' ),
			blockquotes = document.querySelectorAll( 'blockquote[data-secret="' + data.secret + '"]' ),
			i, source, height, sourceURL, targetURL;

		for ( i = 0; i < blockquotes.length; i++ ) {
			blockquotes[ i ].style.display = 'none';
		}

		for ( i = 0; i < iframes.length; i++ ) {
			source = iframes[ i ];

			source.style.display = '';

			/* Resize the iframe on request. */
			if ( 'height' === data.message ) {
				height = data.value;
				if ( height > 1000 ) {
					height = 1000;
				} else if ( height < 200 ) {
					height = 200;
				}

				source.height = (height) + 'px';
			}

			/* Link to a specific URL on request. */
			if ( 'link' === data.message ) {
				sourceURL = document.createElement( 'a' );
				targetURL = document.createElement( 'a' );

				sourceURL.href = source.getAttribute( 'src' );
				targetURL.href = data.value;

				/* Only continue if link hostname matches iframe's hostname. */
				if ( targetURL.host === sourceURL.host && document.activeElement === source ) {
					window.top.location.href = data.value;
				}
			}
		}
	};

	function onLoad() {
		if ( loaded ) {
			return;
		}
		loaded = true;

		var isIE10 = -1 !== navigator.appVersion.indexOf( 'MSIE 10' ),
			isIE11 = !!navigator.userAgent.match( /Trident.*rv\:11\./ ),
			iframes, iframeClone, i;

		/* Remove security attribute from iframes in IE10 and IE11. */
		if ( isIE10 || isIE11 ) {
			iframes = document.querySelectorAll( '.wp-embedded-content[security]' );

			for ( i = 0; i < iframes.length; i++ ) {
				iframeClone = iframes[ i ].cloneNode( true );
				iframeClone.removeAttribute( 'security' );
				iframes[ i ].parentNode.replaceChild( iframeClone, iframes[ i ] );
			}
		}
	}

	if ( supportedBrowser ) {
		window.addEventListener( 'message', window.wp.receiveEmbedMessage, false );
		document.addEventListener( 'DOMContentLoaded', onLoad, false );
		window.addEventListener( 'load', onLoad, false );
	}
})( window, document );
