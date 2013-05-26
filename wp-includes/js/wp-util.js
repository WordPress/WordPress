window.wp = window.wp || {};

(function ($) {
	/**
	 * wp.template( id )
	 *
	 * Fetches a template by id.
	 *
	 * @param  {string} id   A string that corresponds to a DOM element with an id prefixed with "tmpl-".
	 *                       For example, "attachment" maps to "tmpl-attachment".
	 * @return {function}    A function that lazily-compiles the template requested.
	 */
	wp.template = _.memoize(function ( id ) {
		var compiled,
			options = {
				evaluate:    /<#([\s\S]+?)#>/g,
				interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
				escape:      /\{\{([^\}]+?)\}\}(?!\})/g,
				variable:    'data'
			};

		return function ( data ) {
			compiled = compiled || _.template( $( '#tmpl-' + id ).html(), null, options );
			return compiled( data );
		};
	});
}(jQuery));
