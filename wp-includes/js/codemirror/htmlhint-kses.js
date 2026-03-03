/* global HTMLHint */
/* eslint no-magic-numbers: ["error", { "ignore": [1] }] */
HTMLHint.addRule( {
	id: 'kses',
	description: 'Element or attribute cannot be used.',

	/**
	 * Initialize.
	 *
	 * @this {import('htmlhint/types').Rule}
	 * @param {import('htmlhint').HTMLParser} parser - Parser.
	 * @param {import('htmlhint').Reporter} reporter - Reporter.
	 * @param {Record<string, Record<string, boolean>>} options - KSES options.
	 * @return {void}
	 */
	init: function ( parser, reporter, options ) {
		'use strict';

		parser.addListener( 'tagstart', ( event ) => {
			const tagName = event.tagName.toLowerCase();
			if ( ! options[ tagName ] ) {
				reporter.error(
					`Tag <${ event.tagName }> is not allowed.`,
					event.line,
					event.col,
					this,
					event.raw
				);
				return;
			}

			const allowedAttributes = options[ tagName ];
			const column = event.col + event.tagName.length + 1;
			for ( const attribute of event.attrs ) {
				if ( ! allowedAttributes[ attribute.name.toLowerCase() ] ) {
					reporter.error(
						`Tag attribute [${ attribute.raw }] is not allowed.`,
						event.line,
						column + attribute.index,
						this,
						attribute.raw
					);
				}
			}
		} );
	},
} );
