// JSHINT has some GPL Compatability issues, so we are faking it out and using esprima for validation
// Based on https://github.com/jquery/esprima/blob/gh-pages/demo/validate.js which is MIT licensed

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


