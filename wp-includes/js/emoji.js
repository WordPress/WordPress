/* global EmojiSettings, twemoji */
var WPEmoji;

(function() {
	WPEmoji = {
		/**
		 * The CDN URL for where emoji files are hosted.
		 *
		 * @since 4.2.0
		 *
		 * @var string
		 */
		base_url: '//s0.wp.com/wp-content/mu-plugins/emoji/twemoji/72x72',

		/**
		 * The extension of the hosted emoji files.
		 *
		 * @since 4.2.0
		 *
		 * @var string
		 */
		ext: '.png',

		/**
		 * Flag to determine if we should parse all emoji characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @var bool
		 */
		parseAllEmoji: false,

		/**
		 * Flag to determine if we should consider parsing emoji characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @var bool
		 */
		parseEmoji: false,

		/**
		 * Flag to determine if we should parse flag characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @var bool
		 */
		parseFlags: false,

		/**
		 * Initialize our emoji support, and set up listeners.
		 *
		 * @since 4.2.0
		 */
		init: function() {
			if ( typeof EmojiSettings !== 'undefined' ) {
				this.base_url = EmojiSettings.base_url || this.base_url;
				this.ext = EmojiSettings.ext || this.ext;
			}

			WPEmoji.parseAllEmoji = ! WPEmoji.browserSupportsEmoji();
			WPEmoji.parseFlags = ! WPEmoji.browserSupportsFlagEmoji();
			WPEmoji.parseEmoji = WPEmoji.parseAllEmoji || WPEmoji.parseFlags;

			if ( ! WPEmoji.parseEmoji ) {
				return;
			}
		},

		/**
		 * Runs when the document load event is fired, so we can do our first parse of the page.
		 *
		 * @since 4.2.0
		 */
		load: function() {
			WPEmoji.parse( document.body );
		},

		/**
		 * Detect if the browser supports rendering emoji.
		 *
		 * @since 4.2.0
		 *
		 * @return {bool} True if the browser can render emoji, false if it cannot.
		 */
		browserSupportsEmoji: function() {
			var context, smile;

			if ( ! document.createElement( 'canvas' ).getContext ) {
				return;
			}

			context = document.createElement( 'canvas' ).getContext( '2d' );
			if ( typeof context.fillText != 'function' ) {
				return;
			}

			smile = String.fromCharCode( 55357 ) + String.fromCharCode( 56835 );

			/*
			 * Chrome OS X added native emoji rendering in M41. Unfortunately,
			 * it doesn't work when the font is bolder than 500 weight. So, we
			 * check for bold rendering support to avoid invisible emoji in Chrome.
			 */
			context.textBaseline = 'top';
			context.font = '600 32px Arial';
			context.fillText( smile, 0, 0 );

			return context.getImageData( 16, 16, 1, 1 ).data[0] !== 0;
		},

		/**
		 * Detect if the browser supports rendering flag emoji. Flag emoji are a single glyph
		 * made of two characters, so some browsers (notably, Firefox OS X) don't support them.
		 *
		 * @since 4.2.0
		 * @return {bool} True if the browser renders flag characters as a flag glyph, false if it does not.
		 */
		browserSupportsFlagEmoji: function() {
			var context, flag, canvas;

			canvas = document.createElement( 'canvas' );

			if ( ! canvas.getContext ) {
				return;
			}

			context = canvas.getContext( '2d' );

			if ( typeof context.fillText != 'function' ) {
				return;
			}

			flag =  String.fromCharCode(55356) + String.fromCharCode(56812); // [G]
			flag += String.fromCharCode(55356) + String.fromCharCode(56807); // [B]

			context.textBaseline = 'top';
			context.font = '32px Arial';
			context.fillText( flag, 0, 0 );

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
		 * Given a DOM node, parse any emoji characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @param {Element} element The DOM node to parse.
		 */
		parse: function( element ) {
			if ( ! WPEmoji.parseEmoji ) {
				return;
			}

			return twemoji.parse( element, {
				base: this.base_url,
				ext: this.ext,
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

					if ( WPEmoji.parseFlags && ! WPEmoji.parseAllEmoji && ! icon.match( /^1f1(e[6-9a-f]|f[1-9a-f])-1f1(e[6-9a-f]|f[1-9a-f])$/ ) ) {
						return false;
					}

					return ''.concat( options.base, '/', icon, options.ext );
				}
			} );
		}
	};

	if ( window.addEventListener ) {
		window.addEventListener( 'load', WPEmoji.load, false );
	} else if ( window.attachEvent ) {
		window.attachEvent( 'onload', WPEmoji.load );
	}

	WPEmoji.init();
})();
