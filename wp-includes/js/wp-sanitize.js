/**
 * @output wp-includes/js/wp-sanitize.js
 */

( function () {

	window.wp = window.wp || {};

	/**
	 * wp.sanitize
	 *
	 * Helper functions to sanitize strings.
	 */
	wp.sanitize = {

		/**
		 * Strip HTML tags.
		 *
		 * @param {string} text Text to strip the HTML tags from.
		 *
		 * @return  Stripped text.
		 */
		stripTags: function( text ) {
			text = text || '';

			// Do the replacement.
			var _text = text
					.replace( /<!--[\s\S]*?(-->|$)/g, '' )
					.replace( /<(script|style)[^>]*>[\s\S]*?(<\/\1>|$)/ig, '' )
					.replace( /<\/?[a-z][\s\S]*?(>|$)/ig, '' );

			// If the initial text is not equal to the modified text,
			// do the search-replace again, until there is nothing to be replaced.
			if ( _text !== text ) {
				return wp.sanitize.stripTags( _text );
			}

			// Return the text with stripped tags.
			return _text;
		},

		/**
		 * Strip HTML tags and convert HTML entities.
		 *
		 * @param {string} text Text to strip tags and convert HTML entities.
		 *
		 * @return Sanitized text. False on failure.
		 */
		stripTagsAndEncodeText: function( text ) {
			var _text = wp.sanitize.stripTags( text ),
				textarea = document.createElement( 'textarea' );

			try {
				textarea.textContent = _text;
				_text = wp.sanitize.stripTags( textarea.value );
			} catch ( er ) {}

			return _text;
		}
	};
}() );
