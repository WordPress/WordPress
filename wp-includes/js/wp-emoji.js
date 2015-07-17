
( function( window, settings ) {
	function wpEmoji() {
		var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver,

		/**
		 * Flag to determine if we should replace emoji characters with images.
		 *
		 * @since 4.2.0
		 *
		 * @var Boolean
		 */
		replaceEmoji = false,

		// Private
		twemoji, timer,
		loaded = false,
		count = 0;

		/**
		 * Runs when the document load event is fired, so we can do our first parse of the page.
		 *
		 * @since 4.2.0
		 */
		function load() {
			if ( loaded ) {
				return;
			}

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
			loaded = true;

			if ( MutationObserver ) {
				new MutationObserver( function( mutationRecords ) {
					var i = mutationRecords.length,
						addedNodes, removedNodes, ii, node;

					while ( i-- ) {
						addedNodes = mutationRecords[ i ].addedNodes;
						removedNodes = mutationRecords[ i ].removedNodes;
						ii = addedNodes.length;

						if (
							ii === 1 && removedNodes.length === 1 &&
							addedNodes[0].nodeType === 3 &&
							removedNodes[0].nodeName === 'IMG' &&
							addedNodes[0].data === removedNodes[0].alt
						) {
							return;
						}

						while ( ii-- ) {
							node = addedNodes[ ii ];

							if ( node.nodeType === 3 ) {
								node = node.parentNode;
							}

							if ( ! node || node.nodeType !== 1 || ( 'ownerSVGElement' in node ) ||
								( node.className && typeof node.className === 'string' && node.className.indexOf( 'wp-exclude-emoji' ) !== -1 ) ) {

 								continue;
 							}

							parse( node );
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
			if ( ! replaceEmoji || ! twemoji ) {
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

					if ( ! settings.supports.flag && settings.supports.simple &&
						! /^1f1(?:e[6-9a-f]|f[0-9a-f])-1f1(?:e[6-9a-f]|f[0-9a-f])$/.test( icon ) ) {

						return false;
					}

					return ''.concat( options.base, icon, options.ext );
				}
			} );
		}

		/**
		 * Initialize our emoji support, and set up listeners.
		 */
		if ( settings ) {
			replaceEmoji = ! settings.supports.simple || ! settings.supports.flag;

			if ( settings.DOMReady ) {
				load();
			} else {
				settings.readyCallback = load;
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
