window.wp = window.wp || {};

( function( window, wp, twemoji, settings ) {
	var emoji = {
		/**
		 * Flag to determine if we should parse all emoji characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @var Boolean
		 */
		parseAllEmoji: false,

		/**
		 * Flag to determine if we should consider parsing emoji characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @var Boolean
		 */
		parseEmoji: false,

		/**
		 * Flag to determine if we should parse flag characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @var Boolean
		 */
		parseFlags: false,

		/**
		 * Initialize our emoji support, and set up listeners.
		 *
		 * @since 4.2.0
		 */
		init: function() {
			emoji.parseAllEmoji = ! emoji.browserSupportsEmoji();
			emoji.parseFlags = ! emoji.browserSupportsFlagEmoji();
			emoji.parseEmoji = emoji.parseAllEmoji || emoji.parseFlags;

			if ( window.addEventListener ) {
				window.addEventListener( 'load', emoji.load, false );
			} else if ( window.attachEvent ) {
				window.attachEvent( 'onload', emoji.load );
			}
		},

		/**
		 * Runs when the document load event is fired, so we can do our first parse of the page.
		 *
		 * @since 4.2.0
		 */
		load: function() {
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

							if ( node.nodeType === 1 ) {
								emoji.parse( node );
							}
						}
					}
				} )

				.observe( document.body, {
					childList: true,
					subtree: true
				} );
			}

			emoji.parse( document.body );
		},

		/**
		 * Detect if the browser supports rendering emoji.
		 *
		 * @since 4.2.0
		 *
		 * @return {Boolean} True if the browser can render emoji, false if it cannot.
		 */
		browserSupportsEmoji: function() {
			var canvas = document.createElement( 'canvas' ),
				context = canvas.getContext && canvas.getContext( '2d' );

			if ( ! context.fillText ) {
				return false;
			}

			/*
			 * Chrome OS X added native emoji rendering in M41. Unfortunately,
			 * it doesn't work when the font is bolder than 500 weight. So, we
			 * check for bold rendering support to avoid invisible emoji in Chrome.
			 */
			context.textBaseline = 'top';
			context.font = '600 32px Arial';
			context.fillText( String.fromCharCode( 55357, 56835 ), 0, 0 );

			return context.getImageData( 16, 16, 1, 1 ).data[0] !== 0;
		},

		/**
		 * Detect if the browser supports rendering flag emoji. Flag emoji are a single glyph
		 * made of two characters, so some browsers (notably, Firefox OS X) don't support them.
		 *
		 * @since 4.2.0
		 *
		 * @return {Boolean} True if the browser renders flag characters as a flag glyph, false if it does not.
		 */
		browserSupportsFlagEmoji: function() {
			var canvas = document.createElement( 'canvas' ),
				context = canvas.getContext && canvas.getContext( '2d' );

			if ( ! context.fillText ) {
				return false;
			}

			context.textBaseline = 'top';
			context.font = '32px Arial';
			context.fillText( String.fromCharCode( 55356, 56812, 55356, 56807 ), 0, 0 );

			/*
			 * This works because the image will be one of three things:
			 * - Two empty squares, if the browser doen't render emoji
			 * - Two squares with 'G' and 'B' in them, if the browser doen't render flag emoji
			 * - The British flag
			 *
			 * The first two will encode to small images (1-2KB data URLs), the third will encode
			 * to a larger image (4-5KB data URL).
			 */
			return canvas.toDataURL().length > 3000;
		},

		/**
		 * Given an element or string, parse any emoji characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @param {HTMLElement|String} object The element or string to parse.
		 */
		parse: function( object ) {
			if ( ! emoji.parseEmoji ) {
				return object;
			}

			return twemoji.parse( object, {
				base: settings.baseUrl,
				ext: settings.ext,
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

					if ( emoji.parseFlags && ! emoji.parseAllEmoji && ! icon.match( /^1f1(e[6-9a-f]|f[1-9a-f])-1f1(e[6-9a-f]|f[1-9a-f])$/ ) ) {
						return false;
					}

					return ''.concat( options.base, '/', icon, options.ext );
				}
			} );
		}
	};

	emoji.init();

	wp.emoji = emoji;
} )( window, window.wp, window.twemoji, window._wpemojiSettings );
