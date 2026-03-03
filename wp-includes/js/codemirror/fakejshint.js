/**
 * JSHINT has some GPL Compatability issues, so we are faking it out and using esprima for validation
 * Based on https://github.com/jquery/esprima/blob/gh-pages/demo/validate.js which is MIT licensed.
 * This is now deprecated in favor of Espree.
 *
 * @since 4.9.3
 * @deprecated 7.0.0
 * @output wp-includes/js/codemirror/fakejshint.js
 * @see https://core.trac.wordpress.org/ticket/42850
 * @see https://core.trac.wordpress.org/ticket/64558
 */

/* jshint -W057, -W058 */
var fakeJSHINT = new function() {
	var syntax, errors;
	var that = this;
	this.data = [];
	this.convertError = function( error ){
		return {
			line: error.lineNumber,
			character: error.column,
			reason: error.description,
			code: 'E'
		};
	};
	this.parse = function( code ){
		try {
			syntax = window.esprima.parse(code, { tolerant: true, loc: true });
			errors = syntax.errors;
			if ( errors.length > 0 ) {
				for ( var i = 0; i < errors.length; i++) {
					var error = errors[i];
					that.data.push( that.convertError( error ) );
				}
			} else {
				that.data = [];
			}
		} catch (e) {
			that.data.push( that.convertError( e ) );
		}
	};
};

window.JSHINT = function( text ){
	fakeJSHINT.parse( text );
};
window.JSHINT.data = function(){
	return {
		errors: fakeJSHINT.data
	};
};


