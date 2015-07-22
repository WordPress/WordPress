( function() {
	function WordCounter( settings ) {
		var key,
			shortcodes;

		if ( settings ) {
			for ( key in settings ) {
				if ( settings.hasOwnProperty( key ) ) {
					this.settings[ key ] = settings[ key ];
				}
			}
		}

		shortcodes = this.settings.l10n.shortcodes;

		if ( shortcodes && shortcodes.length ) {
			this.settings.shortcodesRegExp = new RegExp( '\\[\\/?(?:' + shortcodes.join( '|' ) + ')[^\\]]*?\\]', 'g' );
		}
	}

	WordCounter.prototype.settings = {
		HTMLRegExp: /<\/?[a-z][^>]*?>/gi,
		HTMLcommentRegExp: /<!--[\s\S]*?-->/g,
		spaceRegExp: /&nbsp;|&#160;/gi,
		HTMLEntityRegExp: /&\S+?;/g,
		connectorRegExp: /--|\u2014/g,
		removeRegExp: new RegExp( [
			'[',
				// Basic Latin (extract)
				'\u0021-\u0040\u005B-\u0060\u007B-\u007E',
				// Latin-1 Supplement (extract)
				'\u0080-\u00BF\u00D7\u00F7',
				// General Punctuation
				// Superscripts and Subscripts
				// Currency Symbols
				// Combining Diacritical Marks for Symbols
				// Letterlike Symbols
				// Number Forms
				// Arrows
				// Mathematical Operators
				// Miscellaneous Technical
				// Control Pictures
				// Optical Character Recognition
				// Enclosed Alphanumerics
				// Box Drawing
				// Block Elements
				// Geometric Shapes
				// Miscellaneous Symbols
				// Dingbats
				// Miscellaneous Mathematical Symbols-A
				// Supplemental Arrows-A
				// Braille Patterns
				// Supplemental Arrows-B
				// Miscellaneous Mathematical Symbols-B
				// Supplemental Mathematical Operators
				// Miscellaneous Symbols and Arrows
				'\u2000-\u2BFF',
				// Supplemental Punctuation
				'\u2E00-\u2E7F',
			']'
		].join( '' ), 'g' ),
		astralRegExp: /[\uD800-\uDBFF][\uDC00-\uDFFF]/g,
		wordsRegExp: /\S\s+/g,
		charactersRegExp: /\S/g,
		allRegExp: /[^\f\n\r\t\v\u00AD\u2028\u2029]/g,
		l10n: window.wordCountL10n || {}
	};

	WordCounter.prototype.count = function( text, type ) {
		var count = 0;

		type = type || this.settings.l10n.type;

		if ( type !== 'characters' && type !== 'all' ) {
			type = 'words';
		}

		if ( text ) {
			text = text + '\n';

			text = text.replace( this.settings.HTMLRegExp, '\n' );
			text = text.replace( this.settings.HTMLcommentRegExp, '' );

			if ( this.settings.shortcodesRegExp ) {
				text = text.replace( this.settings.shortcodesRegExp, '\n' );
			}

			text = text.replace( this.settings.spaceRegExp, ' ' );

			if ( type === 'words' ) {
				text = text.replace( this.settings.HTMLEntityRegExp, '' );
				text = text.replace( this.settings.connectorRegExp, ' ' );
				text = text.replace( this.settings.removeRegExp, '' );
			} else {
				text = text.replace( this.settings.HTMLEntityRegExp, 'a' );
				text = text.replace( this.settings.astralRegExp, 'a' );
			}

			text = text.match( this.settings[ type + 'RegExp' ] );

			if ( text ) {
				count = text.length;
			}
		}

		return count;
	};

	window.wp = window.wp || {};
	window.wp.utils = window.wp.utils || {};
	window.wp.utils.WordCounter = WordCounter;
} )();
