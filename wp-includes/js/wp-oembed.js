(function ( window, document ) {
	'use strict';

	window.wp = window.wp || {};

	if ( !! window.wp.receiveEmbedMessage ) {
		return;
	}

	window.wp.receiveEmbedMessage = function( e ) {
		var data = e.data;
		if ( ! ( data.secret || data.message || data.value ) ) {
			return;
		}

		var iframes = document.querySelectorAll( '.wp-embedded-content[data-secret="' + data.secret + '"]' );

		for ( var i = 0; i < iframes.length; i++ ) {
			var source = iframes[ i ];

			/* Resize the iframe on request. */
			if ( 'height' === data.message ) {
				var height = data.value;
				if ( height > 1000 ) {
					height = 1000;
				} else if ( height < 200 ) {
					height = 200;
				}

				source.height = (height) + 'px';
			}

			/* Link to a specific URL on request. */
			if ( 'link' === data.message ) {
				var sourceURL = document.createElement( 'a' ), targetURL = document.createElement( 'a' );
				sourceURL.href = source.getAttribute( 'src' );
				targetURL.href = data.value;

				/* Only continue if link hostname matches iframe's hostname. */
				if ( targetURL.host === sourceURL.host && document.activeElement === source ) {
					window.top.location.href = data.value;
				}
			}
		}
	};

	window.addEventListener( 'message', window.wp.receiveEmbedMessage, false );

	function onLoad() {
		var isIE10 = -1 !== navigator.appVersion.indexOf( 'MSIE 10' ),
			isIE11 = !!navigator.userAgent.match( /Trident.*rv\:11\./ );

		/* Remove security attribute from iframes in IE10 and IE11. */
		if ( isIE10 || isIE11 ) {
			var iframes = document.querySelectorAll( '.wp-embedded-content[security]' ), iframeClone;

			for ( var i = 0; i < iframes.length; i++ ) {
				iframeClone = iframes[ i ].cloneNode( true );
				iframeClone.removeAttribute( 'security' );
				iframes[ i ].parentNode.insertBefore( iframeClone, iframes[ i ].nextSibling );
				iframes[ i ].parentNode.removeChild( iframes[ i ] );
			}
		}
	}

	document.addEventListener( 'DOMContentLoaded', onLoad, false );
})( window, document );
