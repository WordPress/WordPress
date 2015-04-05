
( function( window, settings ) {
	function wpEmoji() {
		var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver,

		/**
		 * Flag to determine if the browser and the OS support emoji.
		 *
		 * @since 4.2.0
		 *
		 * @var Boolean
		 */
		supportsEmoji = false,

		/**
		 * Flag to determine if the browser and the OS support flag (two character) emoji.
		 *
		 * @since 4.2.0
		 *
		 * @var Boolean
		 */
		supportsFlagEmoji = false,

		/**
		 * Flag to determine if we should replace emoji characters with images.
		 *
		 * @since 4.2.0
		 *
		 * @var Boolean
		 */
		replaceEmoji = false,

		isIE8 = window.navigator.userAgent.indexOf( 'IE 8' ) !== -1,

		// Private
		twemoji, timer,
		count = 0;

		/**
		 * Runs when the document load event is fired, so we can do our first parse of the page.
		 *
		 * @since 4.2.0
		 */
		function load() {
			if ( typeof window.twemoji === 'undefined' ) {
				// Break if waiting for longer than 30 sec.
				if ( count > 600 ) {
					return;
				}

				// Still waiting.
				window.clearTimeout( timer );
				timer = window.setTimeout( load, 50 );
				count++;

				return;
			}

			twemoji = window.twemoji;

			if ( MutationObserver ) {
				new MutationObserver( function( mutationRecords ) {
					var i = mutationRecords.length,
						ii, node;

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
				} ).observe( document.body, {
					childList: true,
					subtree: true
				} );
			}

			parse( document.body );
		}

		/**
		 * Given an element or string, parse any emoji characters into Twemoji images.
		 *
		 * @since 4.2.0
		 *
		 * @param {HTMLElement|String} object The element or string to parse.
		 * @param {Object} args Additional options for Twemoji.
		 */
		function parse( object, args ) {
			if ( ! replaceEmoji ) {
				return object;
			}

			args = args || {};

			return twemoji.parse( object, {
				base: settings.baseUrl,
				ext: settings.ext,
				className: args.className || 'emoji',
				imgAttr: args.imgAttr,
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

					if ( ! supportsFlagEmoji && supportsEmoji &&
						! /^1f1(?:e[6-9a-f]|f[0-9a-f])-1f1(?:e[6-9a-f]|f[0-9a-f])$/.test( icon ) ) {

						return false;
					}

					return ''.concat( options.base, icon, options.ext );
				}
			} );
		}

		// Load when the readyState changes to 'interactive', not 'complete'.
		function onLoad() {
			if ( ( ! isIE8 && 'interactive' === document.readyState ) || ( isIE8 && 'complete' === document.readyState ) ) {
				load();
			}
		}

		/**
		 * Initialize our emoji support, and set up listeners.
		 */
		if ( settings ) {
			supportsEmoji = window._wpemojiSettings.supports.simple;
			supportsFlagEmoji = window._wpemojiSettings.supports.flag;
			replaceEmoji = ! supportsEmoji || ! supportsFlagEmoji;

			if ( ( ! isIE8 && 'loading' === document.readyState ) || ( isIE8 && 'complete' !== document.readyState ) ) {
				if ( document.addEventListener ) {
					document.addEventListener( 'readystatechange', onLoad, false );
				} else if ( document.attachEvent ) {
					document.attachEvent( 'onreadystatechange', onLoad );
				}
			} else {
				load();
			}
		}

		return {
			replaceEmoji: replaceEmoji,
			parse: parse
		};
	}

	window.wp = window.wp || {};
	window.wp.emoji = new wpEmoji();

} )( window, window._wpemojiSettings );
