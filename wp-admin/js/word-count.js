/**
 * Word or character counting functionality. Count words or characters in a
 * provided text string.
 *
 * @namespace wp.utils
 *
 * @since 2.6.0
 * @output wp-admin/js/word-count.js
 */

( function() {
	/**
	 * Word counting utility
	 *
	 * @namespace wp.utils.wordcounter
	 * @memberof  wp.utils
	 *
	 * @class
	 *
	 * @param {Object} settings                                   Optional. Key-value object containing overrides for
	 *                                                            settings.
	 * @param {RegExp} settings.HTMLRegExp                        Optional. Regular expression to find HTML elements.
	 * @param {RegExp} settings.HTMLcommentRegExp                 Optional. Regular expression to find HTML comments.
	 * @param {RegExp} settings.spaceRegExp                       Optional. Regular expression to find irregular space
	 *                                                            characters.
	 * @param {RegExp} settings.HTMLEntityRegExp                  Optional. Regular expression to find HTML entities.
	 * @param {RegExp} settings.connectorRegExp                   Optional. Regular expression to find connectors that
	 *                                                            split words.
	 * @param {RegExp} settings.removeRegExp                      Optional. Regular expression to find remove unwanted
	 *                                                            characters to reduce false-positives.
	 * @param {RegExp} settings.astralRegExp                      Optional. Regular expression to find unwanted
	 *                                                            characters when searching for non-words.
	 * @param {RegExp} settings.wordsRegExp                       Optional. Regular expression to find words by spaces.
	 * @param {RegExp} settings.characters_excluding_spacesRegExp Optional. Regular expression to find characters which
	 *                                                            are non-spaces.
	 * @param {RegExp} settings.characters_including_spacesRegExp Optional. Regular expression to find characters
	 *                                                            including spaces.
	 * @param {RegExp} settings.shortcodesRegExp                  Optional. Regular expression to find shortcodes.
	 * @param {Object} settings.l10n                              Optional. Localization object containing specific
	 *                                                            configuration for the current localization.
	 * @param {string} settings.l10n.type                         Optional. Method of finding words to count.
	 * @param {Array}  settings.l10n.shortcodes                   Optional. Array of shortcodes that should be removed
	 *                                                            from the text.
	 *
	 * @return {void}
	 */
	function WordCounter( settings ) {
		var key,
			shortcodes;

		// Apply provided settings to object settings.
		if ( settings ) {
			for ( key in settings ) {

				// Only apply valid settings.
				if ( settings.hasOwnProperty( key ) ) {
					this.settings[ key ] = settings[ key ];
				}
			}
		}

		shortcodes = this.settings.l10n.shortcodes;

		// If there are any localization shortcodes, add this as type in the settings.
		if ( shortcodes && shortcodes.length ) {
			this.settings.shortcodesRegExp = new RegExp( '\\[\\/?(?:' + shortcodes.join( '|' ) + ')[^\\]]*?\\]', 'g' );
		}
	}

	// Default settings.
	WordCounter.prototype.settings = {
		HTMLRegExp: /<\/?[a-z][^>]*?>/gi,
		HTMLcommentRegExp: /<!--[\s\S]*?-->/g,
		spaceRegExp: /&nbsp;|&#160;/gi,
		HTMLEntityRegExp: /&\S+?;/g,

		// \u2014 = em-dash.
		connectorRegExp: /--|\u2014/g,

		// Characters to be removed from input text.
		removeRegExp: new RegExp( [
			'[',

				// Basic Latin (extract).
				'\u0021-\u0040\u005B-\u0060\u007B-\u007E',

				// Latin-1 Supplement (extract).
				'\u0080-\u00BF\u00D7\u00F7',

				/*
				 * The following range consists of:
				 * General Punctuation
				 * Superscripts and Subscripts
				 * Currency Symbols
				 * Combining Diacritical Marks for Symbols
				 * Letterlike Symbols
				 * Number Forms
				 * Arrows
				 * Mathematical Operators
				 * Miscellaneous Technical
				 * Control Pictures
				 * Optical Character Recognition
				 * Enclosed Alphanumerics
				 * Box Drawing
				 * Block Elements
				 * Geometric Shapes
				 * Miscellaneous Symbols
				 * Dingbats
				 * Miscellaneous Mathematical Symbols-A
				 * Supplemental Arrows-A
				 * Braille Patterns
				 * Supplemental Arrows-B
				 * Miscellaneous Mathematical Symbols-B
				 * Supplemental Mathematical Operators
				 * Miscellaneous Symbols and Arrows
				 */
				'\u2000-\u2BFF',

				// Supplemental Punctuation.
				'\u2E00-\u2E7F',
			']'
		].join( '' ), 'g' ),

		// Remove UTF-16 surrogate points, see https://en.wikipedia.org/wiki/UTF-16#U.2BD800_to_U.2BDFFF
		astralRegExp: /[\uD800-\uDBFF][\uDC00-\uDFFF]/g,
		wordsRegExp: /\S\s+/g,
		characters_excluding_spacesRegExp: /\S/g,

		/*
		 * Match anything that is not a formatting character, excluding:
		 * \f = form feed
		 * \n = new line
		 * \r = carriage return
		 * \t = tab
		 * \v = vertical tab
		 * \u00AD = soft hyphen
		 * \u2028 = line separator
		 * \u2029 = paragraph separator
		 */
		characters_including_spacesRegExp: /[^\f\n\r\t\v\u00AD\u2028\u2029]/g,
		l10n: window.wordCountL10n || {}
	};

	/**
	 * Counts the number of words (or other specified type) in the specified text.
	 *
	 * @since 2.6.0
	 *
	 * @memberof wp.utils.wordcounter
	 *
	 * @param {string}  text Text to count elements in.
	 * @param {string}  type Optional. Specify type to use.
	 *
	 * @return {number} The number of items counted.
	 */
	WordCounter.prototype.count = function( text, type ) {
		var count = 0;

		// Use default type if none was provided.
		type = type || this.settings.l10n.type;

		// Sanitize type to one of three possibilities: 'words', 'characters_excluding_spaces' or 'characters_including_spaces'.
		if ( type !== 'characters_excluding_spaces' && type !== 'characters_including_spaces' ) {
			type = 'words';
		}

		// If we have any text at all.
		if ( text ) {
			text = text + '\n';

			// Replace all HTML with a new-line.
			text = text.replace( this.settings.HTMLRegExp, '\n' );

			// Remove all HTML comments.
			text = text.replace( this.settings.HTMLcommentRegExp, '' );

			// If a shortcode regular expression has been provided use it to remove shortcodes.
			if ( this.settings.shortcodesRegExp ) {
				text = text.replace( this.settings.shortcodesRegExp, '\n' );
			}

			// Normalize non-breaking space to a normal space.
			text = text.replace( this.settings.spaceRegExp, ' ' );

			if ( type === 'words' ) {

				// Remove HTML Entities.
				text = text.replace( this.settings.HTMLEntityRegExp, '' );

				// Convert connectors to spaces to count attached text as words.
				text = text.replace( this.settings.connectorRegExp, ' ' );

				// Remove unwanted characters.
				text = text.replace( this.settings.removeRegExp, '' );
			} else {

				// Convert HTML Entities to "a".
				text = text.replace( this.settings.HTMLEntityRegExp, 'a' );

				// Remove surrogate points.
				text = text.replace( this.settings.astralRegExp, 'a' );
			}

			// Match with the selected type regular expression to count the items.
			text = text.match( this.settings[ type + 'RegExp' ] );

			// If we have any matches, set the count to the number of items found.
			if ( text ) {
				count = text.length;
			}
		}

		return count;
	};

	// Add the WordCounter to the WP Utils.
	window.wp = window.wp || {};
	window.wp.utils = window.wp.utils || {};
	window.wp.utils.WordCounter = WordCounter;
} )();
