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
		 * @param {string} text Text to have the HTML tags striped out of.
		 *
		 * @return  Stripped text.
		 */
		stripTags: function( text ) {
			text = text || '';

			return text
				.replace( /<!--[\s\S]*?(-->|$)/g, '' )
				.replace( /<(script|style)[^>]*>[\s\S]*?(<\/\1>|$)/ig, '' )
				.replace( /<\/?[a-z][\s\S]*?(>|$)/ig, '' );
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
				textarea.innerHTML = _text;
				_text = wp.sanitize.stripTags( textarea.value );
			} catch ( er ) {}

			return _text;
		}
	};
}() );
