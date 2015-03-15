
( function( window, twemoji, settings ) {
	function wpEmoji() {
		var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver,

		/**
		 * Flag to determine if we should parse all emoji characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @var Boolean
		 */
		parseAllEmoji = false,

		/**
		 * Flag to determine if we should consider parsing emoji characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @var Boolean
		 */
		parseEmoji = false,

		/**
		 * Flag to determine if we should parse flag characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @var Boolean
		 */
		parseFlags = false;

		/**
		 * Runs when the document load event is fired, so we can do our first parse of the page.
		 *
		 * @since 4.2.0
		 */
		function load() {
			if ( MutationObserver ) {
				new MutationObserver( function( mutationRecords ) {
					var i = mutationRecords.length,
						ii,
						node;

					while ( i-- ) {
						ii = mutationRecords[ i ].addedNodes.length;

						while ( ii-- ) {
							node = mutationRecords[ i ].addedNodes[ ii ];

							if ( node.nodeType === 3 ) {
								node = node.parentNode;
							}

							if ( node && node.nodeType === 1 ) {
								parse( node );
							}
						}
					}
				} )

				.observe( document.body, {
					childList: true,
					subtree: true
				} );
			}

			parse( document.body );
		}

		/**
		 * Detect if the browser supports rendering emoji or flag emoji. Flag emoji are a single glyph
		 * made of two characters, so some browsers (notably, Firefox OS X) don't support them.
		 *
		 * @since 4.2.0
		 *
		 * @param flagEmoji {Boolean} Whether to test for support of flag emoji.
		 * @return {Boolean} True if the browser can render emoji, false if it cannot.
		 */
		function browserSupportsEmoji( type ) {
			var canvas = document.createElement( 'canvas' ),
				context = canvas.getContext && canvas.getContext( '2d' );

			if ( ! context || ! context.fillText ) {
				return false;
			}

			context.textBaseline = 'top';
			context.font = '600 32px Arial';

			if ( type === 'flag' ) {
				/*
				 * This works because the image will be one of three things:
				 * - Two empty squares, if the browser doen't render emoji
				 * - Two squares with 'G' and 'B' in them, if the browser doen't render flag emoji
				 * - The British flag
				 *
				 * The first two will encode to small images (1-2KB data URLs), the third will encode
				 * to a larger image (4-5KB data URL).
				 */
				context.fillText( String.fromCharCode( 55356, 56812, 55356, 56807 ), 0, 0 );
				return canvas.toDataURL().length > 3000;
			} else {
				/*
				 * Chrome on OS X added native emoji rendering in M41. Unfortunately,
				 * it doesn't work when the font is bolder than 500 weight. So, we
				 * check for bold rendering support to avoid invisible emoji in Chrome.
				 */
				context.fillText( String.fromCharCode( 55357, 56835 ), 0, 0 );
				return context.getImageData( 16, 16, 1, 1 ).data[0] !== 0;
			}
		}

		/**
		 * Given an element or string, parse any emoji characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @param {HTMLElement|String} object The element or string to parse.
		 */
		function parse( object, options ) {
			if ( ! parseEmoji ) {
				return object;
			}

			var className = ( options && options.className ) || 'emoji';

			return twemoji.parse( object, {
				base: settings.baseUrl,
				ext: settings.ext,
				className: className,
				callback: function( icon, options ) {
					// Ignore some standard characters that TinyMCE recommends in its character map.
					switch ( icon ) {
						case 'a9':
						case 'ae':
						case '2122':
						case '2194':
						case '2660':
						case '2663':
						case '2665':
						case '2666':
							return false;
					}

					if ( parseFlags && ! parseAllEmoji &&
						! /^1f1(?:e[6-9a-f]|f[1-9a-f])-1f1(?:e[6-9a-f]|f[1-9a-f])$/.test( icon ) ) {

						return false;
					}

					return ''.concat( options.base, '/', icon, options.ext );
				}
			} );
		}

		/**
		 * Initialize our emoji support, and set up listeners.
		 */
		if ( twemoji && settings ) {
			parseAllEmoji = ! browserSupportsEmoji();
			parseFlags = ! browserSupportsEmoji( 'flag' );
			parseEmoji = parseAllEmoji || parseFlags;
	
			if ( window.addEventListener ) {
				window.addEventListener( 'load', load, false );
			} else if ( window.attachEvent ) {
				window.attachEvent( 'onload', load );
			}
		}

		return {
			browserSupportsEmoji: browserSupportsEmoji,
			parse: parse
		};
	}

	window.wp = window.wp || {};
	window.wp.emoji = new wpEmoji();

} )( window, window.twemoji, window._wpemojiSettings );
