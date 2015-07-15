( function() {
	function WordCounter( settings ) {
		var key;

		if ( settings ) {
			for ( key in settings ) {
				if ( settings.hasOwnProperty( key ) ) {
					this.settings[ key ] = settings[ key ];
				}
			}
		}
	}

	WordCounter.prototype.settings = {
		HTMLRegExp: /<\/?[a-z][^>]*?>/gi,
		spaceRegExp: /&nbsp;|&#160;/gi,
		removeRegExp: /[0-9.(),;:!?%#$¿'"_+=\\\/-]+/g,
		wordsRegExp: /\S\s+/g,
		charactersRegExp: /\S/g,
		allRegExp: /[^\f\n\r\t\v\u00ad\u2028\u2029]/g,
		l10n: window.wordCountL10n || {}
	};

	WordCounter.prototype.count = function( text, type ) {
		var count = 0;

		type = type || this.settings.l10n.type || 'words';

		if ( text ) {
			text = text + '\n';

			text = text.replace( this.settings.HTMLRegExp, '\n' );
			text = text.replace( this.settings.spaceRegExp, ' ' );
			text = text.replace( this.settings.removeRegExp, '' );

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
