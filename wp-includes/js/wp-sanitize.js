/**
 * @output wp-includes/js/wp-sanitize.js
 */

/* eslint-env es6 */

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
		 * @param {string} text - Text to strip the HTML tags from.
		 *
		 * @return {string} Stripped text.
		 */
		stripTags: function( text ) {
			if ( ! text ) {
				return '';
			}

			const domParser = new DOMParser();
			const htmlDocument = domParser.parseFromString(
				text,
				'text/html'
			);

			/*
			 * The following self-assignment appears to be a no-op, but it isn't.
			 * It enforces the escaping. Reading the `innerText` property decodes
			 * character references, returning a raw string. When written, however,
			 * the text is re-escaped to ensure that the rendered text replicates
			 * what it's given.
			 *
			 * See <https://github.com/WordPress/wordpress-develop/pull/10536#discussion_r2550615378>.
			 */
			htmlDocument.body.innerText = htmlDocument.body.innerText;

			// Return the text with stripped tags.
			return htmlDocument.body.innerHTML;
		},

		/**
		 * Strip HTML tags and convert HTML entities.
		 *
		 * @param {string} text - Text to strip tags and convert HTML entities.
		 *
		 * @return {string} Sanitized text.
		 */
		stripTagsAndEncodeText: function( text ) {
			let _text = wp.sanitize.stripTags( text ),
				textarea = document.createElement( 'textarea' );

			try {
				textarea.textContent = _text;
				_text = wp.sanitize.stripTags( textarea.value );
			} catch ( er ) {}

			return _text;
		}
	};
}() );
