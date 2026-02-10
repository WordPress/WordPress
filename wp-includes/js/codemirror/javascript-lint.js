/**
 * CodeMirror JavaScript linter.
 *
 * @since 7.0.0
 */

import CodeMirror from 'codemirror';

/**
 * CodeMirror Lint Error.
 *
 * @see https://codemirror.net/5/doc/manual.html#addon_lint
 *
 * @typedef {Object} CodeMirrorLintError
 * @property {string} message - Error message.
 * @property {'error'} severity - Severity.
 * @property {CodeMirror.Position} from - From position.
 * @property {CodeMirror.Position} to - To position.
 */

/**
 * JSHint options supported by Espree.
 *
 * @see https://jshint.com/docs/options/
 * @see https://www.npmjs.com/package/espree#options
 *
 * @typedef {Object} SupportedJSHintOptions
 * @property {number} [esversion] - "This option is used to specify the ECMAScript version to which the code must adhere."
 * @property {boolean} [es5] - "This option enables syntax first defined in the ECMAScript 5.1 specification. This includes allowing reserved keywords as object properties."
 * @property {boolean} [es3] - "This option tells JSHint that your code needs to adhere to ECMAScript 3 specification. Use this option if you need your program to be executable in older browsers—such as Internet Explorer 6/7/8/9—and other legacy JavaScript environments."
 * @property {boolean} [module] - "This option informs JSHint that the input code describes an ECMAScript 6 module. All module code is interpreted as strict mode code."
 * @property {'implied'} [strict] - "This option requires the code to run in ECMAScript 5's strict mode."
 */

/**
 * Validates JavaScript.
 *
 * @since 7.0.0
 *
 * @param {string} text - Source.
 * @param {SupportedJSHintOptions} options - Linting options.
 * @returns {Promise<CodeMirrorLintError[]>}
 */
async function validator( text, options ) {
	const errors = /** @type {CodeMirrorLintError[]} */ [];
	try {
		const espree = await import( /* webpackIgnore: true */ 'espree' );
		espree.parse( text, {
			...getEspreeOptions( options ),
			loc: true,
		} );
	} catch ( error ) {
		if (
			// This is an `EnhancedSyntaxError` in Espree: <https://github.com/brettz9/espree/blob/3c1120280b24f4a5e4c3125305b072fa0dfca22b/packages/espree/lib/espree.js#L48-L54>.
			error instanceof SyntaxError &&
			typeof error.lineNumber === 'number' &&
			typeof error.column === 'number'
		) {
			const line = error.lineNumber - 1;
			errors.push( {
				message: error.message,
				severity: 'error',
				from: CodeMirror.Pos( line, error.column - 1 ),
				to: CodeMirror.Pos( line, error.column ),
			} );
		} else {
			console.warn( '[CodeMirror] Unable to lint JavaScript:', error ); // jshint ignore:line
		}
	}

	return errors;
}

CodeMirror.registerHelper( 'lint', 'javascript', validator );

/**
 * Gets the options for Espree from the supported JSHint options.
 *
 * @since 7.0.0
 *
 * @param {SupportedJSHintOptions} options - Linting options for JSHint.
 * @return {{
 *     ecmaVersion?: number|'latest',
 *     ecmaFeatures?: {
 *         impliedStrict?: true
 *     }
 * }}
 */
function getEspreeOptions( options ) {
	const ecmaFeatures = {};
	if ( options.strict === 'implied' ) {
		ecmaFeatures.impliedStrict = true;
	}

	return {
		ecmaVersion: getEcmaVersion( options ),
		sourceType: options.module ? 'module' : 'script',
		ecmaFeatures,
	};
}

/**
 * Gets the ECMAScript version.
 *
 * @since 7.0.0
 *
 * @param {SupportedJSHintOptions} options - Options.
 * @return {number|'latest'} ECMAScript version.
 */
function getEcmaVersion( options ) {
	if ( typeof options.esversion === 'number' ) {
		return options.esversion;
	}
	if ( options.es5 ) {
		return 5;
	}
	if ( options.es3 ) {
		return 3;
	}
	return 'latest';
}
