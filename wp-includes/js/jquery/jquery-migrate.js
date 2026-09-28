/*!
 * jQuery Migrate - v3.6.0 - 2025-12-11T02:48Z
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license
 * https://jquery.com/license/
 */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "jquery" ], function( jQuery ) {
			return factory( jQuery, window );
		} );
	} else if ( typeof module === "object" && module.exports ) {

		// Node/CommonJS
		// eslint-disable-next-line no-undef
		module.exports = factory( require( "jquery" ), window );
	} else {

		// Browser globals
		factory( jQuery, window );
	}
} )( function( jQuery, window ) {
"use strict";

jQuery.migrateVersion = "3.6.0";

// Returns 0 if v1 == v2, -1 if v1 < v2, 1 if v1 > v2
function compareVersions( v1, v2 ) {
	var i,
		rVersionParts = /^(\d+)\.(\d+)\.(\d+)/,
		v1p = rVersionParts.exec( v1 ) || [ ],
		v2p = rVersionParts.exec( v2 ) || [ ];

	for ( i = 1; i <= 3; i++ ) {
		if ( +v1p[ i ] > +v2p[ i ] ) {
			return 1;
		}
		if ( +v1p[ i ] < +v2p[ i ] ) {
			return -1;
		}
	}
	return 0;
}

function jQueryVersionSince( version ) {
	return compareVersions( jQuery.fn.jquery, version ) >= 0;
}

// A map from disabled patch codes to `true`. This should really
// be a `Set` but those are unsupported in IE.
var disabledPatches = Object.create( null );

// Don't apply patches for specified codes. Helpful for code bases
// where some Migrate warnings have been addressed, and it's desirable
// to avoid needless patches or false positives.
jQuery.migrateDisablePatches = function() {
	var i;
	for ( i = 0; i < arguments.length; i++ ) {
		disabledPatches[ arguments[ i ] ] = true;
	}
};

// Allow enabling patches disabled via `jQuery.migrateDisablePatches`.
// Helpful if you want to disable a patch only for some code that won't
// be updated soon to be able to focus on other warnings - and enable it
// immediately after such a call:
// ```js
// jQuery.migrateDisablePatches( "workaroundA" );
// elem.pluginViolatingWarningA( "pluginMethod" );
// jQuery.migrateEnablePatches( "workaroundA" );
// ```
jQuery.migrateEnablePatches = function() {
	var i;
	for ( i = 0; i < arguments.length; i++ ) {
		delete disabledPatches[ arguments[ i ] ];
	}
};

jQuery.migrateIsPatchEnabled = function( patchCode ) {
	return !disabledPatches[ patchCode ];
};

( function() {

// Support: IE9 only
// IE9 only creates console object when dev tools are first opened
// IE9 console is a host object, callable but doesn't have .apply()
if ( !window.console || !window.console.log ) {
	return;
}

// Need jQuery 3.x and no older Migrate loaded
if ( !jQuery || !jQueryVersionSince( "3.0.0" ) ||
		jQueryVersionSince( "4.0.0" ) ) {
	window.console.log( "JQMIGRATE: jQuery 3.x REQUIRED" );
}
if ( jQuery.migrateWarnings ) {
	window.console.log( "JQMIGRATE: Migrate plugin loaded multiple times" );
}

// Show a message on the console so devs know we're active
window.console.log( "JQMIGRATE: Migrate is installed" +
	( jQuery.migrateMute ? "" : " with logging active" ) +
	", version " + jQuery.migrateVersion );

} )();

var warnedAbout = {};

// By default, each warning is only reported once.
jQuery.migrateDeduplicateWarnings = true;

// List of warnings already given; public read only
jQuery.migrateWarnings = [];

// Set to `false` to disable traces that appear with warnings
if ( jQuery.migrateTrace === undefined ) {
	jQuery.migrateTrace = true;
}

// Forget any warnings we've already given; public
jQuery.migrateReset = function() {
	warnedAbout = {};
	jQuery.migrateWarnings.length = 0;
};

function migrateWarn( code, msg ) {
	var console = window.console;
	if ( jQuery.migrateIsPatchEnabled( code ) &&
		( !jQuery.migrateDeduplicateWarnings || !warnedAbout[ msg ] ) ) {
		warnedAbout[ msg ] = true;
		jQuery.migrateWarnings.push( msg + " [" + code + "]" );
		if ( console && console.warn && !jQuery.migrateMute ) {
			console.warn( "JQMIGRATE: " + msg );
			if ( jQuery.migrateTrace && console.trace ) {
				console.trace();
			}
		}
	}
}

function migrateWarnProp( obj, prop, value, code, msg ) {
	Object.defineProperty( obj, prop, {
		configurable: true,
		enumerable: true,
		get: function() {
			migrateWarn( code, msg );
			return value;
		},
		set: function( newValue ) {
			migrateWarn( code, msg );
			value = newValue;
		}
	} );
}

function migrateWarnFuncInternal( obj, prop, newFunc, code, msg ) {
	var finalFunc,
		origFunc = obj[ prop ];

	obj[ prop ] = function() {

		// If `msg` not provided, do not warn; more sophisticated warnings
		// logic is most likely embedded in `newFunc`, in that case here
		// we just care about the logic choosing the proper implementation
		// based on whether the patch is disabled or not.
		if ( msg ) {
			migrateWarn( code, msg );
		}

		// Since patches can be disabled & enabled dynamically, we
		// need to decide which implementation to run on each invocation.
		finalFunc = jQuery.migrateIsPatchEnabled( code ) ?
			newFunc :

			// The function may not have existed originally so we need a fallback.
			( origFunc || jQuery.noop );

		return finalFunc.apply( this, arguments );
	};
}

function migratePatchAndWarnFunc( obj, prop, newFunc, code, msg ) {
	if ( !msg ) {
		throw new Error( "No warning message provided" );
	}
	return migrateWarnFuncInternal( obj, prop, newFunc, code, msg );
}

function migratePatchFunc( obj, prop, newFunc, code ) {
	return migrateWarnFuncInternal( obj, prop, newFunc, code );
}

if ( window.document.compatMode === "BackCompat" ) {

	// jQuery has never supported or tested Quirks Mode
	migrateWarn( "quirks", "jQuery is not compatible with Quirks Mode" );
}

var arr = [],
	push = arr.push,
	sort = arr.sort,
	splice = arr.splice,
	class2type = {},

	// Require that the "whitespace run" starts from a non-whitespace
	// to avoid O(N^2) behavior when the engine would try matching "\s+$" at each space position.
	rtrim = /^[\s\uFEFF\xA0]+|([^\s\uFEFF\xA0])[\s\uFEFF\xA0]+$/g;

function isFunction( obj ) {

	// Support: Chrome <=57, Firefox <=52
	// In some browsers, typeof returns "function" for HTML <object> elements
	// (i.e., `typeof document.createElement( "object" ) === "function"`).
	// We don't want to classify *any* DOM node as a function.
	// Support: QtWeb <=3.8.5, WebKit <=534.34, wkhtmltopdf tool <=0.12.5
	// Plus for old WebKit, typeof returns "function" for HTML collections
	// (e.g., `typeof document.getElementsByTagName("div") === "function"`). (gh-4756)
	return typeof obj === "function" && typeof obj.nodeType !== "number" &&
		typeof obj.item !== "function";
}

// The number of elements contained in the matched element set
migratePatchAndWarnFunc( jQuery.fn, "size", function() {
	return this.length;
}, "size",
"jQuery.fn.size() is deprecated and removed; use the .length property" );

migratePatchAndWarnFunc( jQuery, "parseJSON", function() {
	return JSON.parse.apply( null, arguments );
}, "parseJSON",
"jQuery.parseJSON is deprecated; use JSON.parse" );

migratePatchAndWarnFunc( jQuery, "holdReady", jQuery.holdReady,
	"holdReady", "jQuery.holdReady is deprecated" );

migratePatchAndWarnFunc( jQuery, "unique", jQuery.uniqueSort,
	"unique", "jQuery.unique is deprecated; use jQuery.uniqueSort" );

// Prior to jQuery 3.1.1 there were internal refs so we don't warn there
if ( jQueryVersionSince( "3.1.1" ) ) {
	migratePatchAndWarnFunc( jQuery, "trim", function( text ) {
		return text == null ?
			"" :
			( text + "" ).replace( rtrim, "$1" );
	}, "trim",
	"jQuery.trim is deprecated; use String.prototype.trim" );
}

// Prior to jQuery 3.2 there were internal refs so we don't warn there
if ( jQueryVersionSince( "3.2.0" ) ) {
	migratePatchAndWarnFunc( jQuery, "nodeName", function( elem, name ) {
		return elem.nodeName && elem.nodeName.toLowerCase() === name.toLowerCase();
	}, "nodeName",
	"jQuery.nodeName is deprecated" );

	migratePatchAndWarnFunc( jQuery, "isArray", Array.isArray, "isArray",
		"jQuery.isArray is deprecated; use Array.isArray"
	);
}

if ( jQueryVersionSince( "3.3.0" ) ) {

	migratePatchAndWarnFunc( jQuery, "isNumeric",
		function( obj ) {

			// As of jQuery 3.0, isNumeric is limited to
			// strings and numbers (primitives or objects)
			// that can be coerced to finite numbers (gh-2662)
			var type = typeof obj;
			return ( type === "number" || type === "string" ) &&

				// parseFloat NaNs numeric-cast false positives ("")
				// ...but misinterprets leading-number strings, e.g. hex literals ("0x...")
				// subtraction forces infinities to NaN
				!isNaN( obj - parseFloat( obj ) );
		}, "isNumeric",
		"jQuery.isNumeric() is deprecated"
	);

	// Populate the class2type map
	jQuery.each( "Boolean Number String Function Array Date RegExp Object Error Symbol".
		split( " " ),
	function( _, name ) {
		class2type[ "[object " + name + "]" ] = name.toLowerCase();
	} );

	migratePatchAndWarnFunc( jQuery, "type", function( obj ) {
		if ( obj == null ) {
			return obj + "";
		}

		// Support: Android <=2.3 only (functionish RegExp)
		return typeof obj === "object" || typeof obj === "function" ?
			class2type[ Object.prototype.toString.call( obj ) ] || "object" :
			typeof obj;
	}, "type",
	"jQuery.type is deprecated" );

	migratePatchAndWarnFunc( jQuery, "isFunction", function( obj ) {
		return isFunction( obj );
	}, "isFunction",
	"jQuery.isFunction() is deprecated" );

	migratePatchAndWarnFunc( jQuery, "isWindow",
		function( obj ) {
			return obj != null && obj === obj.window;
		}, "isWindow",
		"jQuery.isWindow() is deprecated"
	);

	migratePatchAndWarnFunc( jQuery, "now", Date.now, "now",
		"jQuery.now() is deprecated; use Date.now()"
	);

	migratePatchAndWarnFunc( jQuery, "camelCase", jQuery.camelCase, "camelCase",
		"jQuery.camelCase() is deprecated"
	);

	// Bind a function to a context, optionally partially applying any
	// arguments.
	// jQuery.proxy is deprecated to promote standards (specifically Function#bind)
	// However, it is not slated for removal any time soon
	migratePatchAndWarnFunc( jQuery, "proxy", jQuery.proxy,
		"proxy", "DEPRECATED: jQuery.proxy()" );

}

if ( jQueryVersionSince( "3.7.0" ) ) {
	migratePatchAndWarnFunc( jQuery.fn, "push", push, "push",
		"jQuery.fn.push() is deprecated; use .add() or convert to an array" );
	migratePatchAndWarnFunc( jQuery.fn, "sort", sort, "sort",
		"jQuery.fn.sort() is deprecated; convert to an array before sorting" );
	migratePatchAndWarnFunc( jQuery.fn, "splice", splice, "splice",
		"jQuery.fn.splice() is deprecated; use .slice() or .not() with .eq()" );
}

var findProp,
	oldInit = jQuery.fn.init,
	oldFind = jQuery.find,

	rattrHashTest = /\[(\s*[-\w]+\s*)([~|^$*]?=)\s*([-\w#]*?#[-\w#]*)\s*\]/,
	rattrHashGlob = /\[(\s*[-\w]+\s*)([~|^$*]?=)\s*([-\w#]*?#[-\w#]*)\s*\]/g;

migratePatchFunc( jQuery.fn, "init", function( arg1 ) {
	var args = Array.prototype.slice.call( arguments );

	if ( jQuery.migrateIsPatchEnabled( "selector-empty-id" ) &&
		typeof arg1 === "string" && arg1 === "#" ) {

		// JQuery( "#" ) is a bogus ID selector, but it returned an empty set
		// before jQuery 3.0
		migrateWarn( "selector-empty-id", "jQuery( '#' ) is not a valid selector" );
		args[ 0 ] = [];
	}

	return oldInit.apply( this, args );
}, "selector-empty-id" );

// This is already done in Core but the above patch will lose this assignment
// so we need to redo it. It doesn't matter whether the patch is enabled or not
// as the method is always going to be a Migrate-created wrapper.
jQuery.fn.init.prototype = jQuery.fn;

migratePatchFunc( jQuery, "find", function( selector ) {
	var args = Array.prototype.slice.call( arguments );

	// Support: PhantomJS 1.x
	// String#match fails to match when used with a //g RegExp, only on some strings
	if ( typeof selector === "string" && rattrHashTest.test( selector ) ) {

		// The nonstandard and undocumented unquoted-hash was removed in jQuery 1.12.0
		// First see if qS thinks it's a valid selector, if so avoid a false positive
		try {
			window.document.querySelector( selector );
		} catch ( err1 ) {

			// Didn't *look* valid to qSA, warn and try quoting what we think is the value
			selector = selector.replace( rattrHashGlob, function( _, attr, op, value ) {
				return "[" + attr + op + "\"" + value + "\"]";
			} );

			// If the regexp *may* have created an invalid selector, don't update it
			// Note that there may be false alarms if selector uses jQuery extensions
			try {
				window.document.querySelector( selector );
				migrateWarn( "selector-hash",
					"Attribute selector with '#' must be quoted: " + args[ 0 ] );
				args[ 0 ] = selector;
			} catch ( err2 ) {
				migrateWarn( "selector-hash",
					"Attribute selector with '#' was not fixed: " + args[ 0 ] );
			}
		}
	}

	return oldFind.apply( this, args );
}, "selector-hash" );

// Copy properties attached to original jQuery.find method (e.g. .attr, .isXML)
for ( findProp in oldFind ) {
	if ( Object.prototype.hasOwnProperty.call( oldFind, findProp ) ) {
		jQuery.find[ findProp ] = oldFind[ findProp ];
	}
}

// Now jQuery.expr.pseudos is the standard incantation
migrateWarnProp( jQuery.expr, "filters", jQuery.expr.pseudos, "expr-pre-pseudos",
	"jQuery.expr.filters is deprecated; use jQuery.expr.pseudos" );
migrateWarnProp( jQuery.expr, ":", jQuery.expr.pseudos, "expr-pre-pseudos",
	"jQuery.expr[':'] is deprecated; use jQuery.expr.pseudos" );

function markFunction( fn ) {
	fn[ jQuery.expando ] = true;
	return fn;
}

// jQuery older than 3.7.0 used Sizzle which has its own private expando
// variable that we cannot access. This makes thi patch impossible in those
// jQuery versions.
if ( jQueryVersionSince( "3.7.0" ) ) {
	migratePatchFunc( jQuery.expr.filter, "PSEUDO", function( pseudo, argument ) {

		// pseudo-class names are case-insensitive
		// https://www.w3.org/TR/selectors/#pseudo-classes
		// Prioritize by case sensitivity in case custom pseudos are added with uppercase letters
		// Remember that setFilters inherits from pseudos
		var args,
			fn = jQuery.expr.pseudos[ pseudo ] ||
				jQuery.expr.setFilters[ pseudo.toLowerCase() ] ||
				jQuery.error(
					"Syntax error, unrecognized expression: unsupported pseudo: " +
						pseudo );

		// The user may use createPseudo to indicate that
		// arguments are needed to create the filter function
		// just as jQuery does
		if ( fn[ jQuery.expando ] ) {
			return fn( argument );
		}

		// But maintain support for old signatures
		if ( fn.length > 1 ) {
			migrateWarn( "legacy-custom-pseudos",
				"Pseudos with multiple arguments are deprecated; " +
					"use jQuery.expr.createPseudo()" );
			args = [ pseudo, pseudo, "", argument ];
			return jQuery.expr.setFilters.hasOwnProperty( pseudo.toLowerCase() ) ?
				markFunction( function( seed, matches ) {
					var idx,
						matched = fn( seed, argument ),
						i = matched.length;
					while ( i-- ) {
						idx = Array.prototype.indexOf.call( seed, matched[ i ] );
						seed[ idx ] = !( matches[ idx ] = matched[ i ] );
					}
				} ) :
				function( elem ) {
					return fn( elem, 0, args );
				};
		}

		return fn;
	}, "legacy-custom-pseudos" );

	if ( typeof Proxy !== "undefined" ) {
		jQuery.each( [ "pseudos", "setFilters" ], function( _, api ) {
			jQuery.expr[ api ] = new Proxy( jQuery.expr[ api ], {
				set: function( _target, _prop, fn ) {
					if ( typeof fn === "function" && !fn[ jQuery.expando ] && fn.length > 1 ) {
						migrateWarn( "legacy-custom-pseudos",
							"Pseudos with multiple arguments are deprecated; " +
								"use jQuery.expr.createPseudo()" );
					}
					return Reflect.set.apply( this, arguments );
				}
			} );
		} );
	}
}

// Support jQuery slim which excludes the ajax module
if ( jQuery.ajax ) {

var oldAjax = jQuery.ajax,
	oldCallbacks = [],
	guid = "migrate-" + Date.now(),
	origJsonpCallback = jQuery.ajaxSettings.jsonpCallback,
	rjsonp = /(=)\?(?=&|$)|\?\?/;

migratePatchFunc( jQuery, "ajax", function() {
	var jQXHR = oldAjax.apply( this, arguments );

	// Be sure we got a jQXHR (e.g., not sync)
	if ( jQXHR.promise ) {
		migratePatchAndWarnFunc( jQXHR, "success", jQXHR.done, "jqXHR-methods",
			"jQXHR.success is deprecated and removed" );
		migratePatchAndWarnFunc( jQXHR, "error", jQXHR.fail, "jqXHR-methods",
			"jQXHR.error is deprecated and removed" );
		migratePatchAndWarnFunc( jQXHR, "complete", jQXHR.always, "jqXHR-methods",
			"jQXHR.complete is deprecated and removed" );
	}

	return jQXHR;
}, "jqXHR-methods" );

jQuery.ajaxSetup( {
	jsonpCallback: function() {

		// Source is virtually the same as in Core, but we need to duplicate
		// to maintain a proper `oldCallbacks` reference.
		if ( jQuery.migrateIsPatchEnabled( "jsonp-promotion" ) ) {
			var callback = oldCallbacks.pop() || ( jQuery.expando + "_" + ( guid++ ) );
			this[ callback ] = true;
			return callback;
		} else {
			return origJsonpCallback.apply( this, arguments );
		}
	}
} );

// Register this prefilter before the jQuery one. Otherwise, a promoted
// request is transformed into one with the script dataType, and we can't
// catch it anymore.
// jQuery <4 already contains this prefilter; don't duplicate the whole logic,
// but only enough to know when to warn.
jQuery.ajaxPrefilter( "+json", function( s ) {

	if ( !jQuery.migrateIsPatchEnabled( "jsonp-promotion" ) ) {
		return;
	}

	// Warn if JSON-to-JSONP auto-promotion happens.
	if ( s.jsonp !== false && ( rjsonp.test( s.url ) ||
			typeof s.data === "string" &&
			( s.contentType || "" )
				.indexOf( "application/x-www-form-urlencoded" ) === 0 &&
			rjsonp.test( s.data )
	) ) {
		migrateWarn( "jsonp-promotion", "JSON-to-JSONP auto-promotion is deprecated" );
	}
} );

}

var oldRemoveAttr = jQuery.fn.removeAttr,
	oldJQueryAttr = jQuery.attr,
	oldToggleClass = jQuery.fn.toggleClass,
	booleans = "checked|selected|async|autofocus|autoplay|controls|defer|" +
		"disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",
	rbooleans = new RegExp( "^(?:" + booleans + ")$", "i" ),
	rmatchNonSpace = /\S+/g,

	// Some formerly boolean attributes gained new values with special meaning.
	// Skip the old boolean attr logic for those values.
	extraBoolAttrValues = {
		hidden: [ "until-found" ]
	};

// HTML boolean attributes have special behavior:
// we consider the lowercase name to be the only valid value, so
// getting (if the attribute is present) normalizes to that, as does
// setting to any non-`false` value (and setting to `false` removes the attribute).
// See https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#boolean-attributes
jQuery.each( booleans.split( "|" ), function( _i, name ) {
	var origAttrHooks = jQuery.attrHooks[ name ] || {};
	jQuery.attrHooks[ name ] = {
		get: origAttrHooks.get || function( elem ) {
			var attrValue;

			if ( jQuery.migrateIsPatchEnabled( "boolean-attributes" ) ) {
				attrValue = elem.getAttribute( name );

				if ( attrValue !== name && attrValue != null &&
					( extraBoolAttrValues[ name ] || [] )
						.indexOf( String( attrValue ).toLowerCase() ) === -1
				) {
					migrateWarn( "boolean-attributes",
						"Boolean attribute '" + name +
							"' value is different from its lowercased name" );
				}
			}

			return null;
		},

		set: origAttrHooks.set || function( elem, value, name ) {
			if ( jQuery.migrateIsPatchEnabled( "boolean-attributes" ) ) {
				if ( value !== name &&
					( extraBoolAttrValues[ name ] || [] )
						.indexOf( String( value ).toLowerCase() ) === -1
				) {
					if ( value !== false ) {
						migrateWarn( "boolean-attributes",
							"Boolean attribute '" + name +
								"' is not set to its lowercased name" );
					}

					if ( value === false ) {

						// Remove boolean attributes when set to false
						jQuery.removeAttr( elem, name );
					} else {
						elem.setAttribute( name, name );
					}
					return name;
				}
			} else {

				// jQuery <4 uses a private `boolHook` for the boolean attribute
				// setter. It's only activated if `attrHook` is not set, but we set
				// it here in Migrate so jQuery would not use it. Since we cannot
				// access it, let's just repeat its contents here.
				if ( value === false ) {

					// Remove boolean attributes when set to false
					jQuery.removeAttr( elem, name );
				} else {
					elem.setAttribute( name, name );
				}
				return name;
			}
		}
	};
} );

migratePatchFunc( jQuery, "attr", function( elem, name, value ) {
	var nType = elem.nodeType;

	// Fallback to the original method on text, comment and attribute nodes
	// and when attributes are not supported.
	if ( nType === 3 || nType === 8 || nType === 2 ||
			typeof elem.getAttribute === "undefined" ) {
		return oldJQueryAttr.apply( this, arguments );
	}

	if ( value === false && name.toLowerCase().indexOf( "aria-" ) !== 0 &&
			!rbooleans.test( name ) ) {
		migrateWarn( "attr-false",
			"Setting the non-ARIA non-boolean attribute '" + name +
				"' to false" );

		jQuery.attr( elem, name, "false" );
		return;
	}

	return oldJQueryAttr.apply( this, arguments );
}, "attr-false" );

migratePatchFunc( jQuery.fn, "removeAttr", function( name ) {
	var self = this,
		patchNeeded = false;

	jQuery.each( name.match( rmatchNonSpace ), function( _i, attr ) {
		if ( rbooleans.test( attr ) ) {

			// Only warn if at least a single node had the property set to
			// something else than `false`. Otherwise, this Migrate patch
			// doesn't influence the behavior and there's no need to set or warn.
			self.each( function() {
				if ( jQuery( this ).prop( attr ) !== false ) {
					patchNeeded = true;
					return false;
				}
			} );
		}

		if ( patchNeeded ) {
			migrateWarn( "removeAttr-bool",
				"jQuery.fn.removeAttr no longer sets boolean properties: " + attr );
			self.prop( attr, false );
		}
	} );

	return oldRemoveAttr.apply( this, arguments );
}, "removeAttr-bool" );

migratePatchFunc( jQuery.fn, "toggleClass", function( state ) {

	// Only deprecating no-args or single boolean arg
	if ( state !== undefined && typeof state !== "boolean" ) {

		return oldToggleClass.apply( this, arguments );
	}

	migrateWarn( "toggleClass-bool", "jQuery.fn.toggleClass( boolean ) is deprecated" );

	// Toggle entire class name of each element
	return this.each( function() {
		var className = this.getAttribute && this.getAttribute( "class" ) || "";

		if ( className ) {
			jQuery.data( this, "__className__", className );
		}

		// If the element has a class name or if we're passed `false`,
		// then remove the whole classname (if there was one, the above saved it).
		// Otherwise, bring back whatever was previously saved (if anything),
		// falling back to the empty string if nothing was stored.
		if ( this.setAttribute ) {
			this.setAttribute( "class",
				className || state === false ?
					"" :
					jQuery.data( this, "__className__" ) || ""
			);
		}
	} );
}, "toggleClass-bool" );

function camelCase( string ) {
	return string.replace( /-([a-z])/g, function( _, letter ) {
		return letter.toUpperCase();
	} );
}

// Make `object` inherit from `Object.prototype` via an additional object
// in between; that intermediate object proxies properties
// to `Object.prototype`, warning about their usage first.
function patchProto( object, options ) {

	// Support: IE 9 - 10 only, iOS 7 - 8 only
	// Older IE doesn't have a way to change an existing prototype.
	// Just return the original method there.
	// Older WebKit supports `__proto__` but not `Object.setPrototypeOf`.
	// To avoid complicating code, don't patch the API there either.
	if ( !Object.setPrototypeOf ) {
		return object;
	}

	var i,
		warningId = options.warningId,
		apiName = options.apiName,

		// `Object.prototype` keys are not enumerable so list the
		// official ones here. An alternative would be wrapping
		// objects with a Proxy but that creates additional issues
		// like breaking object identity on subsequent calls.
		objProtoKeys = [
			"__proto__",
			"__defineGetter__",
			"__defineSetter__",
			"__lookupGetter__",
			"__lookupSetter__",
			"hasOwnProperty",
			"isPrototypeOf",
			"propertyIsEnumerable",
			"toLocaleString",
			"toString",
			"valueOf"
		],

		// Use a null prototype at the beginning so that we can define our
		// `__proto__` getter & setter. We'll reset the prototype afterward.
		intermediateObj = Object.create( null );

	for ( i = 0; i < objProtoKeys.length; i++ ) {
		( function( key ) {
			Object.defineProperty( intermediateObj, key, {
				get: function() {
					migrateWarn( warningId,
						"Accessing properties from " + apiName +
						" inherited from Object.prototype is deprecated" );
					return ( key + "__cache" ) in intermediateObj ?
						intermediateObj[ key + "__cache" ] :
						Object.prototype[ key ];
				},
				set: function( value ) {
					migrateWarn( warningId,
						"Setting properties from " + apiName +
						" inherited from Object.prototype is deprecated" );
					intermediateObj[ key + "__cache" ] = value;
				}
			} );
		} )( objProtoKeys[ i ] );
	}

	Object.setPrototypeOf( intermediateObj, Object.prototype );
	Object.setPrototypeOf( object, intermediateObj );

	return object;
}

var origFnCss,
	internalSwapCall = false,
	ralphaStart = /^[a-z]/,

	// The regex visualized:
	//
	//                         /----------\
	//                        |            |    /-------\
	//                        |  / Top  \  |   |         |
	//         /--- Border ---+-| Right  |-+---+- Width -+---\
	//        |                 | Bottom |                    |
	//        |                  \ Left /                     |
	//        |                                               |
	//        |                              /----------\     |
	//        |          /-------------\    |            |    |- END
	//        |         |               |   |  / Top  \  |    |
	//        |         |  / Margin  \  |   | | Right  | |    |
	//        |---------+-|           |-+---+-| Bottom |-+----|
	//        |            \ Padding /         \ Left /       |
	// BEGIN -|                                               |
	//        |                /---------\                    |
	//        |               |           |                   |
	//        |               |  / Min \  |    / Width  \     |
	//         \--------------+-|       |-+---|          |---/
	//                           \ Max /       \ Height /
	rautoPx = /^(?:Border(?:Top|Right|Bottom|Left)?(?:Width|)|(?:Margin|Padding)?(?:Top|Right|Bottom|Left)?|(?:Min|Max)?(?:Width|Height))$/;

// If this version of jQuery has .swap(), don't false-alarm on internal uses
if ( jQuery.swap ) {
	jQuery.each( [ "height", "width", "reliableMarginRight" ], function( _, name ) {
		var oldHook = jQuery.cssHooks[ name ] && jQuery.cssHooks[ name ].get;

		if ( oldHook ) {
			jQuery.cssHooks[ name ].get = function() {
				var ret;

				internalSwapCall = true;
				ret = oldHook.apply( this, arguments );
				internalSwapCall = false;
				return ret;
			};
		}
	} );
}

migratePatchFunc( jQuery, "swap", function( elem, options, callback, args ) {
	var ret, name,
		old = {};

	if ( !internalSwapCall ) {
		migrateWarn( "swap", "jQuery.swap() is undocumented and deprecated" );
	}

	// Remember the old values, and insert the new ones
	for ( name in options ) {
		old[ name ] = elem.style[ name ];
		elem.style[ name ] = options[ name ];
	}

	ret = callback.apply( elem, args || [] );

	// Revert the old values
	for ( name in options ) {
		elem.style[ name ] = old[ name ];
	}

	return ret;
}, "swap" );

if ( jQueryVersionSince( "3.4.0" ) && typeof Proxy !== "undefined" ) {
	jQuery.cssProps = new Proxy( jQuery.cssProps || {}, {
		set: function() {
			migrateWarn( "cssProps", "jQuery.cssProps is deprecated" );
			return Reflect.set.apply( this, arguments );
		}
	} );
}

function isAutoPx( prop ) {

	// The first test is used to ensure that:
	// 1. The prop starts with a lowercase letter (as we uppercase it for the second regex).
	// 2. The prop is not empty.
	return ralphaStart.test( prop ) &&
		rautoPx.test( prop[ 0 ].toUpperCase() + prop.slice( 1 ) );
}

origFnCss = jQuery.fn.css;

migratePatchFunc( jQuery.fn, "css", function( name, value ) {
	var camelName,
		origThis = this;

	if ( name && typeof name === "object" && !Array.isArray( name ) ) {
		jQuery.each( name, function( n, v ) {
			jQuery.fn.css.call( origThis, n, v );
		} );
		return this;
	}

	if ( typeof value === "number" ) {
		camelName = camelCase( name );

		if ( !isAutoPx( camelName ) && !jQuery.cssNumber[ camelName ] ) {
			migrateWarn( "css-number",
				"Number-typed values are deprecated for jQuery.fn.css( \"" +
				name + "\", value )" );
		}
	}

	return origFnCss.apply( this, arguments );
}, "css-number" );

var rmultiDash = /[A-Z]/g,
	rnothtmlwhite = /[^\x20\t\r\n\f]+/g,
	origJQueryData = jQuery.data,
	origJQueryPrivateData = jQuery._data;

function unCamelCase( str ) {
	return str.replace( rmultiDash, "-$&" ).toLowerCase();
}

function patchDataCamelCase( origData, options ) {
	var apiName = options.apiName,
		isPrivateData = options.isPrivateData,
		isInstanceMethod = options.isInstanceMethod,
		origJQueryStaticData = isPrivateData ? origJQueryPrivateData : origJQueryData;

	function objectSetter( elem, obj ) {
		var curData, key;

		// Name can be an object, and each entry in the object is meant
		// to be set as data.
		// Let the original method handle the case of a missing elem.
		if ( elem ) {

			// Don't use the instance method here to avoid `data-*` attributes
			// detection this early.
			curData = origJQueryStaticData( elem );

			for ( key in obj ) {
				if ( key !== camelCase( key ) ) {
					migrateWarn( "data-camelCase",
						apiName + " always sets/gets camelCased names: " +
							key );
					curData[ key ] = obj[ key ];
				}
			}

			// Pass the keys handled above to the original API as well
			// so that both the camelCase & initial keys are saved.
			if ( isInstanceMethod ) {
				origData.call( this, obj );
			} else {
				origData.call( this, elem, obj );
			}

			return obj;
		}
	}

	function singleSetter( elem, name, value ) {
		var curData;

		// If the name is transformed, look for the un-transformed name
		// in the data object.
		// Let the original method handle the case of a missing elem.
		if ( elem ) {

			// Don't use the instance method here to avoid `data-*` attributes
			// detection this early.
			curData = origJQueryStaticData( elem );

			if ( curData && name in curData ) {
				migrateWarn( "data-camelCase",
					apiName + " always sets/gets camelCased names: " +
						name );

				curData[ name ] = value;
			}

			origJQueryStaticData( elem, name, value );

			// Since the "set" path can have two possible entry points
			// return the expected data based on which path was taken.
			return value !== undefined ? value : name;
		}
	}

	return function jQueryDataPatched( elem, name, value ) {
		var curData,
			that = this,

			// Support: IE 9 only
			// IE 9 doesn't support strict mode and later modifications of
			// parameters also modify the arguments object in sloppy mode.
			// We need the original arguments so save them here.
			args = Array.prototype.slice.call( arguments ),

			adjustedArgsLength = args.length;

		if ( isInstanceMethod ) {
			value = name;
			name = elem;
			elem = that[ 0 ];
			adjustedArgsLength++;
		}

		if ( name && typeof name === "object" && adjustedArgsLength === 2 ) {
			if ( isInstanceMethod ) {
				return that.each( function() {
					objectSetter.call( that, this, name );
				} );
			} else {
				return objectSetter.call( that, elem, name );
			}
		}

		// If the name is transformed, look for the un-transformed name
		// in the data object.
		// Let the original method handle the case of a missing elem.
		if ( name && typeof name === "string" && name !== camelCase( name ) &&
			adjustedArgsLength > 2 ) {

			if ( isInstanceMethod ) {
				return that.each( function() {
					singleSetter.call( that, this, name, value );
				} );
			} else {
				return singleSetter.call( that, elem, name, value );
			}
		}

		if ( elem && name && typeof name === "string" &&
			name !== camelCase( name ) &&
			adjustedArgsLength === 2 ) {

			// Don't use the instance method here to avoid `data-*` attributes
			// detection this early.
			curData = origJQueryStaticData( elem );

			if ( curData && name in curData ) {
				migrateWarn( "data-camelCase",
					apiName + " always sets/gets camelCased names: " +
						name );
				return curData[ name ];
			}
		}

		return origData.apply( this, args );
	};
}

function patchRemoveDataCamelCase( origRemoveData, options ) {
	var isPrivateData = options.isPrivateData,
		isInstanceMethod = options.isInstanceMethod,
		origJQueryStaticData = isPrivateData ? origJQueryPrivateData : origJQueryData;

	function remove( elem, keys ) {
		var i, singleKey, unCamelCasedKeys,
			curData = origJQueryStaticData( elem );

		if ( keys === undefined ) {
			origRemoveData( elem );
			return;
		}

		// Support array or space separated string of keys
		if ( !Array.isArray( keys ) ) {

			// If a key with the spaces exists, use it.
			// Otherwise, create an array by matching non-whitespace
			keys = keys in curData ?
				[ keys ] :
				( keys.match( rnothtmlwhite ) || [] );
		}

		// Remove:
		// * the original keys as passed
		// * their "unCamelCased" version
		// * their camelCase version
		// These may be three distinct values for each key!
		// jQuery 3.x only removes camelCase versions by default. However, in this patch
		// we set the original keys in the mass-setter case and if the key already exists
		// so without removing the "unCamelCased" versions the following would be broken:
		// ```js
		// elem.data( { "a-a": 1 } ).removeData( "aA" );
		// ```
		// Unfortunately, we'll still hit this issue for partially camelCased keys, e.g.:
		// ```js
		// elem.data( { "a-aA": 1 } ).removeData( "aAA" );
		// ```
		// won't work with this patch. We consider this an edge case, but to make sure that
		// at least piggybacking works:
		// ```js
		// elem.data( { "a-aA": 1 } ).removeData( "a-aA" );
		// ```
		// we also remove the original key. Hence, all three are needed.
		// The original API already removes the camelCase versions, though, so let's defer
		// to it.
		unCamelCasedKeys = keys.map( unCamelCase );

		i = keys.length;
		while ( i-- ) {
			singleKey = keys[ i ];
			if ( singleKey !== camelCase( singleKey ) && singleKey in curData ) {
				migrateWarn( "data-camelCase",
					"jQuery" + ( isInstanceMethod ? ".fn" : "" ) +
					".data() always sets/gets camelCased names: " +
					singleKey );
			}
			delete curData[ singleKey ];
		}

		// Don't warn when removing "unCamelCased" keys; we're already printing
		// a warning when setting them and the fix is needed there, not in
		// the `.removeData()` call.
		i = unCamelCasedKeys.length;
		while ( i-- ) {
			delete curData[ unCamelCasedKeys[ i ] ];
		}

		origRemoveData( elem, keys );
	}

	return function jQueryRemoveDataPatched( elem, key ) {
		if ( isInstanceMethod ) {
			key = elem;
			return this.each( function() {
				remove( this, key );
			} );
		} else {
			remove( elem, key );
		}
	};
}

migratePatchFunc( jQuery, "data",
	patchDataCamelCase( jQuery.data, {
		apiName: "jQuery.data()",
		isPrivateData: false,
		isInstanceMethod: false
	} ),
	"data-camelCase" );
migratePatchFunc( jQuery, "_data",
	patchDataCamelCase( jQuery._data, {
		apiName: "jQuery._data()",
		isPrivateData: true,
		isInstanceMethod: false
	} ),
	"data-camelCase" );
migratePatchFunc( jQuery.fn, "data",
	patchDataCamelCase( jQuery.fn.data, {
		apiName: "jQuery.fn.data()",
		isPrivateData: false,
		isInstanceMethod: true
	} ),
	"data-camelCase" );

migratePatchFunc( jQuery, "removeData",
	patchRemoveDataCamelCase( jQuery.removeData, {
		isPrivateData: false,
		isInstanceMethod: false
	} ),
	"data-camelCase" );
migratePatchFunc( jQuery, "_removeData",
	patchRemoveDataCamelCase( jQuery._removeData, {
		isPrivateData: true,
		isInstanceMethod: false
	} ),
	"data-camelCase" );
migratePatchFunc( jQuery.fn, "removeData",

	// No, it's not a typo - we're intentionally passing
	// the static method here as we need something working on
	// a single element.
	patchRemoveDataCamelCase( jQuery.removeData, {
		isPrivateData: false,
		isInstanceMethod: true
	} ),
	"data-camelCase" );

function patchDataProto( original, options ) {
	var warningId = options.warningId,
		apiName = options.apiName,
		isInstanceMethod = options.isInstanceMethod;

	return function apiWithProtoPatched() {
		var result = original.apply( this, arguments );

		if ( arguments.length !== ( isInstanceMethod ? 0 : 1 ) || result === undefined ) {
			return result;
		}

		patchProto( result, {
			warningId: warningId,
			apiName: apiName
		} );

		return result;
	};
}

// Yes, we are patching jQuery.data twice; here & above. This is necessary
// so that each of the two patches can be independently disabled.
migratePatchFunc( jQuery, "data",
	patchDataProto( jQuery.data, {
		warningId: "data-null-proto",
		apiName: "jQuery.data()",
		isInstanceMethod: false
	} ),
	"data-null-proto" );
migratePatchFunc( jQuery, "_data",
	patchDataProto( jQuery._data, {
		warningId: "data-null-proto",
		apiName: "jQuery._data()",
		isInstanceMethod: false
	} ),
	"data-null-proto" );
migratePatchFunc( jQuery.fn, "data",
	patchDataProto( jQuery.fn.data, {
		warningId: "data-null-proto",
		apiName: "jQuery.fn.data()",
		isInstanceMethod: true
	} ),
	"data-null-proto" );

// Support jQuery slim which excludes the effects module
if ( jQuery.fx ) {

var intervalValue, intervalMsg,
	oldTweenRun = jQuery.Tween.prototype.run,
	linearEasing = function( pct ) {
		return pct;
	};

migratePatchFunc( jQuery.Tween.prototype, "run", function( ) {
	if ( jQuery.easing[ this.easing ].length > 1 ) {
		migrateWarn(
			"easing-one-arg",
			"'jQuery.easing." + this.easing.toString() + "' should use only one argument"
		);

		jQuery.easing[ this.easing ] = linearEasing;
	}

	oldTweenRun.apply( this, arguments );
}, "easing-one-arg" );

intervalValue = jQuery.fx.interval;
intervalMsg = "jQuery.fx.interval is deprecated";

// Support: IE9, Android <=4.4
// Avoid false positives on browsers that lack rAF
// Don't warn if document is hidden, jQuery uses setTimeout (#292)
if ( window.requestAnimationFrame ) {
	Object.defineProperty( jQuery.fx, "interval", {
		configurable: true,
		enumerable: true,
		get: function() {
			if ( !window.document.hidden ) {
				migrateWarn( "fx-interval", intervalMsg );
			}

			// Only fallback to the default if patch is enabled
			if ( !jQuery.migrateIsPatchEnabled( "fx-interval" ) ) {
				return intervalValue;
			}
			return intervalValue === undefined ? 13 : intervalValue;
		},
		set: function( newValue ) {
			migrateWarn( "fx-interval", intervalMsg );
			intervalValue = newValue;
		}
	} );
}

}

var oldLoad = jQuery.fn.load,
	oldEventAdd = jQuery.event.add,
	originalFix = jQuery.event.fix;

jQuery.event.props = [];
jQuery.event.fixHooks = {};

migrateWarnProp( jQuery.event.props, "concat", jQuery.event.props.concat,
	"event-old-patch",
	"jQuery.event.props.concat() is deprecated and removed" );

migratePatchFunc( jQuery.event, "fix", function( originalEvent ) {
	var event,
		type = originalEvent.type,
		fixHook = this.fixHooks[ type ],
		props = jQuery.event.props;

	if ( props.length ) {
		migrateWarn( "event-old-patch",
			"jQuery.event.props are deprecated and removed: " + props.join() );
		while ( props.length ) {
			jQuery.event.addProp( props.pop() );
		}
	}

	if ( fixHook && !fixHook._migrated_ ) {
		fixHook._migrated_ = true;
		migrateWarn( "event-old-patch",
			"jQuery.event.fixHooks are deprecated and removed: " + type );
		if ( ( props = fixHook.props ) && props.length ) {
			while ( props.length ) {
				jQuery.event.addProp( props.pop() );
			}
		}
	}

	event = originalFix.call( this, originalEvent );

	return fixHook && fixHook.filter ?
		fixHook.filter( event, originalEvent ) :
		event;
}, "event-old-patch" );

migratePatchFunc( jQuery.event, "add", function( elem, types ) {

	// This misses the multiple-types case but that seems awfully rare
	if ( elem === window && types === "load" && window.document.readyState === "complete" ) {
		migrateWarn( "load-after-event",
			"jQuery(window).on('load'...) called after load event occurred" );
	}
	return oldEventAdd.apply( this, arguments );
}, "load-after-event" );

jQuery.each( [ "load", "unload", "error" ], function( _, name ) {

	migratePatchFunc( jQuery.fn, name, function() {
		var args = Array.prototype.slice.call( arguments, 0 );

		// If this is an ajax load() the first arg should be the string URL;
		// technically this could also be the "Anything" arg of the event .load()
		// which just goes to show why this dumb signature has been deprecated!
		// jQuery custom builds that exclude the Ajax module justifiably die here.
		if ( name === "load" && typeof args[ 0 ] === "string" ) {
			return oldLoad.apply( this, args );
		}

		migrateWarn( "shorthand-removed-v3",
			"jQuery.fn." + name + "() is deprecated" );

		args.splice( 0, 0, name );
		if ( arguments.length ) {
			return this.on.apply( this, args );
		}

		// Use .triggerHandler here because:
		// - load and unload events don't need to bubble, only applied to window or image
		// - error event should not bubble to window, although it does pre-1.7
		// See http://bugs.jquery.com/ticket/11820
		this.triggerHandler.apply( this, args );
		return this;
	}, "shorthand-removed-v3" );

} );

jQuery.each( ( "blur focus focusin focusout resize scroll click dblclick " +
	"mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave " +
	"change select submit keydown keypress keyup contextmenu" ).split( " " ),
function( _i, name ) {

	// Handle event binding
	migratePatchAndWarnFunc( jQuery.fn, name, jQuery.fn[ name ], "shorthand-deprecated-v3",
		"DEPRECATED: jQuery.fn." + name + "() event shorthand" );

} );

// Trigger "ready" event only once, on document ready
jQuery( function() {
	jQuery( window.document ).triggerHandler( "ready" );
} );

jQuery.event.special.ready = {
	setup: function() {
		if ( this === window.document ) {
			migrateWarn( "ready-event", "'ready' event is deprecated" );
		}
	}
};

// Support: jQuery <3.2.0 only
// jQuery 3.0.x & 3.1.x used to not include the deprecated module in the slim build.
// To maintain compatibility with those versions, we need to reimplement APIs
// deprecated in them.
// See https://github.com/jquery/jquery/blob/3.1.1/src/deprecated.js
migratePatchAndWarnFunc( jQuery.fn, "bind", function( types, data, fn ) {
	return this.on( types, null, data, fn );
}, "pre-on-methods", "jQuery.fn.bind() is deprecated" );
migratePatchAndWarnFunc( jQuery.fn, "unbind", function( types, fn ) {
	return this.off( types, null, fn );
}, "pre-on-methods", "jQuery.fn.unbind() is deprecated" );
migratePatchAndWarnFunc( jQuery.fn, "delegate", function( selector, types, data, fn ) {
	return this.on( types, selector, data, fn );
}, "pre-on-methods", "jQuery.fn.delegate() is deprecated" );
migratePatchAndWarnFunc( jQuery.fn, "undelegate", function( selector, types, fn ) {
	return arguments.length === 1 ?
		this.off( selector, "**" ) :
		this.off( types, selector || "**", fn );
}, "pre-on-methods", "jQuery.fn.undelegate() is deprecated" );
migratePatchAndWarnFunc( jQuery.fn, "hover", function( fnOver, fnOut ) {
	return this.on( "mouseenter", fnOver ).on( "mouseleave", fnOut || fnOver );
}, "pre-on-methods", "jQuery.fn.hover() is deprecated" );

// We can apply the patch unconditionally here as in the `3.x` line the API
// inherits from `Object.prototype` even without a patch and `migrateWarn`
// inside `patchProto` will already silence warnings if the patch gets disabled.
patchProto( jQuery.event.special, {
	warningId: "event-special-null-proto",
	apiName: "jQuery.event.special"
} );

var rxhtmlTag = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([a-z][^\/\0>\x20\t\r\n\f]*)[^>]*)\/>/gi,
	makeMarkup = function( html ) {
		var doc = window.document.implementation.createHTMLDocument( "" );
		doc.body.innerHTML = html;
		return doc.body && doc.body.innerHTML;
	},
	warnIfChanged = function( html ) {
		var changed = html.replace( rxhtmlTag, "<$1></$2>" );
		if ( changed !== html && makeMarkup( html ) !== makeMarkup( changed ) ) {
			migrateWarn( "self-closed-tags",
				"HTML tags must be properly nested and closed: " + html );
		}
	};

/**
 * Deprecated, please use `jQuery.migrateDisablePatches( "self-closed-tags" )` instead.
 * @deprecated
 */
migratePatchAndWarnFunc( jQuery, "UNSAFE_restoreLegacyHtmlPrefilter", function() {
	jQuery.migrateEnablePatches( "self-closed-tags" );
}, "legacy-self-closed-tags",
"jQuery.UNSAFE_restoreLegacyHtmlPrefilter deprecated; use " +
	"`jQuery.migrateEnablePatches( \"self-closed-tags\" )`" );

migratePatchFunc( jQuery, "htmlPrefilter", function( html ) {
	warnIfChanged( html );
	return html.replace( rxhtmlTag, "<$1></$2>" );
}, "self-closed-tags" );

// This patch needs to be disabled by default as it re-introduces
// security issues (CVE-2020-11022, CVE-2020-11023).
jQuery.migrateDisablePatches( "self-closed-tags" );

var origOffset = jQuery.fn.offset;

migratePatchFunc( jQuery.fn, "offset", function() {
	var elem = this[ 0 ];

	if ( elem && ( !elem.nodeType || !elem.getBoundingClientRect ) ) {
		migrateWarn( "offset-valid-elem", "jQuery.fn.offset() requires a valid DOM element" );
		return arguments.length ? this : undefined;
	}

	return origOffset.apply( this, arguments );
}, "offset-valid-elem" );

// Support jQuery slim which excludes the ajax module
// The jQuery.param patch is about respecting `jQuery.ajaxSettings.traditional`
// so it doesn't make sense for the slim build.
if ( jQuery.ajax ) {

var origParam = jQuery.param;

migratePatchFunc( jQuery, "param", function( data, traditional ) {
	var ajaxTraditional = jQuery.ajaxSettings && jQuery.ajaxSettings.traditional;

	if ( traditional === undefined && ajaxTraditional ) {

		migrateWarn( "param-ajax-traditional",
			"jQuery.param() no longer uses jQuery.ajaxSettings.traditional" );
		traditional = ajaxTraditional;
	}

	return origParam.call( this, data, traditional );
}, "param-ajax-traditional" );

}

migratePatchAndWarnFunc( jQuery.fn, "andSelf", jQuery.fn.addBack, "andSelf",
	"jQuery.fn.andSelf() is deprecated and removed, use jQuery.fn.addBack()" );

// Support jQuery slim which excludes the deferred module in jQuery 4.0+
if ( jQuery.Deferred ) {

var unpatchedGetStackHookValue,
	oldDeferred = jQuery.Deferred,
	tuples = [

		// Action, add listener, callbacks, .then handlers, final state
		[ "resolve", "done", jQuery.Callbacks( "once memory" ),
			jQuery.Callbacks( "once memory" ), "resolved" ],
		[ "reject", "fail", jQuery.Callbacks( "once memory" ),
			jQuery.Callbacks( "once memory" ), "rejected" ],
		[ "notify", "progress", jQuery.Callbacks( "memory" ),
			jQuery.Callbacks( "memory" ) ]
	];

migratePatchFunc( jQuery, "Deferred", function( func ) {
	var deferred = oldDeferred(),
		promise = deferred.promise();

	function newDeferredPipe( /* fnDone, fnFail, fnProgress */ ) {
		var fns = arguments;

		return jQuery.Deferred( function( newDefer ) {
			jQuery.each( tuples, function( i, tuple ) {
				var fn = typeof fns[ i ] === "function" && fns[ i ];

				// Deferred.done(function() { bind to newDefer or newDefer.resolve })
				// deferred.fail(function() { bind to newDefer or newDefer.reject })
				// deferred.progress(function() { bind to newDefer or newDefer.notify })
				deferred[ tuple[ 1 ] ]( function() {
					var returned = fn && fn.apply( this, arguments );
					if ( returned && typeof returned.promise === "function" ) {
						returned.promise()
							.done( newDefer.resolve )
							.fail( newDefer.reject )
							.progress( newDefer.notify );
					} else {
						newDefer[ tuple[ 0 ] + "With" ](
							this === promise ? newDefer.promise() : this,
							fn ? [ returned ] : arguments
						);
					}
				} );
			} );
			fns = null;
		} ).promise();
	}

	migratePatchAndWarnFunc( deferred, "pipe", newDeferredPipe, "deferred-pipe",
		"deferred.pipe() is deprecated" );
	migratePatchAndWarnFunc( promise, "pipe", newDeferredPipe, "deferred-pipe",
		"deferred.pipe() is deprecated" );

	if ( func ) {
		func.call( deferred, deferred );
	}

	return deferred;
}, "deferred-pipe" );

// Preserve handler of uncaught exceptions in promise chains
jQuery.Deferred.exceptionHook = oldDeferred.exceptionHook;

// Preserve the optional hook to record the error, if defined
jQuery.Deferred.getErrorHook = oldDeferred.getErrorHook;

// We want to mirror jQuery.Deferred.getErrorHook here, so we cannot use
// existing Migrate utils.
Object.defineProperty( jQuery.Deferred, "getStackHook", {
	configurable: true,
	enumerable: true,
	get: function() {
		if ( jQuery.migrateIsPatchEnabled( "deferred-getStackHook" ) ) {

			// jQuery 3.x checks `getStackHook` if `getErrorHook` is missing,
			// so don't warn on the getter.
			return jQuery.Deferred.getErrorHook;
		} else {
			return unpatchedGetStackHookValue;
		}
	},
	set: function( newValue ) {
		if ( jQuery.migrateIsPatchEnabled( "deferred-getStackHook" ) ) {

			// Only warn if `getErrorHook` wasn't set to the same value first.
			if ( jQuery.Deferred.getErrorHook !== newValue ) {
				migrateWarn( "deferred-getStackHook",
					"jQuery.Deferred.getStackHook is deprecated; " +
					"use jQuery.Deferred.getErrorHook" );
				jQuery.Deferred.getErrorHook = newValue;
			}
		} else {
			unpatchedGetStackHookValue = newValue;
		}
	}
} );

}

return jQuery;
} );
