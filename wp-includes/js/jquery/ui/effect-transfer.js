/*!
 * jQuery UI Effects Transfer 1.13.3
 * https://jqueryui.com
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license.
 * https://jquery.org/license
 */

//>>label: Transfer Effect
//>>group: Effects
//>>description: Displays a transfer effect from one element to another.
//>>docs: https://api.jqueryui.com/transfer-effect/
//>>demos: https://jqueryui.com/effect/

( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [
			"jquery",
			"../version",
			"../effect"
		], factory );
	} else {

		// Browser globals
		factory( jQuery );
	}
} )( function( $ ) {
"use strict";

var effect;
if ( $.uiBackCompat !== false ) {
	effect = $.effects.define( "transfer", function( options, done ) {
		$( this ).transfer( options, done );
	} );
}
return effect;

} );
