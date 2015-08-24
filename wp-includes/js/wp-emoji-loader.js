( function( window, document, settings ) {
	var src, ready;

	/**
	 * Detect if the browser supports rendering emoji or flag emoji. Flag emoji are a single glyph
	 * made of two characters, so some browsers (notably, Firefox OS X) don't support them.
	 *
	 * @since 4.2.0
	 *
	 * @param type {String} Whether to test for support of "simple" or "flag" emoji.
	 * @return {Boolean} True if the browser can render emoji, false if it cannot.
	 */
	function browserSupportsEmoji( type ) {
		var canvas = document.createElement( 'canvas' ),
			context = canvas.getContext && canvas.getContext( '2d' );

		if ( ! context || ! context.fillText ) {
			return false;
		}

		/*
		 * Chrome on OS X added native emoji rendering in M41. Unfortunately,
		 * it doesn't work when the font is bolder than 500 weight. So, we
		 * check for bold rendering support to avoid invisible emoji in Chrome.
		 */
		context.textBaseline = 'top';
		context.font = '600 32px Arial';

		if ( type === 'flag' ) {
			/*
			 * This works because the image will be one of three things:
			 * - Two empty squares, if the browser doesn't render emoji
			 * - Two squares with 'G' and 'B' in them, if the browser doesn't render flag emoji
			 * - The British flag
			 *
			 * The first two will encode to small images (1-2KB data URLs), the third will encode
			 * to a larger image (4-5KB data URL).
			 */
			context.fillText( String.fromCharCode( 55356, 56812, 55356, 56807 ), 0, 0 );
			return canvas.toDataURL().length > 3000;
		} else {
			/*
			 * This creates a smiling emoji, and checks to see if there is any image data in the
			 * center pixel. In browsers that don't support emoji, the character will be rendered
			 * as an empty square, so the center pixel will be blank.
			 */
			context.fillText( String.fromCharCode( 55357, 56835 ), 0, 0 );
			return context.getImageData( 16, 16, 1, 1 ).data[0] !== 0;
		}
	}

	function addScript( src ) {
		var script = document.createElement( 'script' );

		script.src = src;
		script.type = 'text/javascript';
		document.getElementsByTagName( 'head' )[0].appendChild( script );
	}

	settings.supports = {
		simple: browserSupportsEmoji( 'simple' ),
		flag:   browserSupportsEmoji( 'flag' )
	};

	settings.DOMReady = false;
	settings.readyCallback = function() {
		settings.DOMReady = true;
	};

	if ( ! settings.supports.simple || ! settings.supports.flag ) {
		ready = function() {
			settings.readyCallback();
		};

		if ( document.addEventListener ) {
			document.addEventListener( 'DOMContentLoaded', ready, false );
			window.addEventListener( 'load', ready, false );
		} else {
			window.attachEvent( 'onload', ready );
			document.attachEvent( 'onreadystatechange', function() {
				if ( 'complete' === document.readyState ) {
					settings.readyCallback();
				}
			} );
		}

		src = settings.source || {};

		if ( src.concatemoji ) {
			addScript( src.concatemoji );
		} else if ( src.wpemoji && src.twemoji ) {
			addScript( src.twemoji );
			addScript( src.wpemoji );
		}
	}

} )( window, document, window._wpemojiSettings );
