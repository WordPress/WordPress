/*!
 * jQuery Migrate - v1.0.0b1 - 2012-12-16
 * https://github.com/jquery/jquery-migrate
 * Copyright 2012 jQuery Foundation and other contributors; Licensed MIT
 */

(function( jQuery, window, undefined ) {
"use strict";

// Use Uglify to do conditional compilation of warning messages;
// the minified version will set this to false and remove dead code.
if ( typeof JQMIGRATE_WARN === "undefined" ) {
	window.JQMIGRATE_WARN = true;
}


var warnedAbout = {};

// List of warnings already given; public read only
jQuery.migrateWarnings = [];

// Forget any warnings we've already given; public
jQuery.migrateReset = function() {
	warnedAbout = {};
	jQuery.migrateWarnings.length = 0;
};

function migrateWarn( msg) {
	if ( JQMIGRATE_WARN ) {
		if ( !warnedAbout[ msg ] ) {
			warnedAbout[ msg ] = true;
			jQuery.migrateWarnings.push( msg );
			if ( window.console && console.warn ) {
				console.warn( "JQMIGRATE: " + msg );
			}
		}
	}
}

function migrateWarnProp( obj, prop, value, msg ) {
	if ( JQMIGRATE_WARN && Object.defineProperty ) {
		// On ES5 browsers (non-oldIE), warn if the code tries to get prop;
		// allow property to be overwritten in case some other plugin wants it
		try {
			Object.defineProperty( obj, prop, {
				configurable: true,
				enumerable: true,
				get: function() {
					migrateWarn( msg );
					return value;
				},
				set: function( newValue ) {
					migrateWarn( msg );
					value = newValue;
				}
			});
			return;
		} catch( err ) {
			// IE8 is a dope about Object.defineProperty, can't warn there
		}
	}

	// Non-ES5 (or broken) browser; just set the property
	jQuery._definePropertyBroken = true;
	obj[ prop ] = value;
}

if ( JQMIGRATE_WARN && document.compatMode === "BackCompat" ) {
	// jQuery has never supported or tested Quirks Mode
	migrateWarn( "jQuery is not compatible with Quirks Mode" );
}


var attrFn = {},
	attr = jQuery.attr,
	valueAttrGet = jQuery.attrHooks.value && jQuery.attrHooks.value.get ||
		function() { return null; },
	valueAttrSet = jQuery.attrHooks.value && jQuery.attrHooks.value.set ||
		function() { return undefined; },
	rnoType = /^(?:input|button)$/i,
	rnoAttrNodeType = /^[238]$/;

// jQuery.attrFn
if ( JQMIGRATE_WARN ) {
	migrateWarnProp( jQuery, "attrFn", attrFn, "jQuery.attrFn is deprecated" );
}

jQuery.attr = function( elem, name, value, pass ) {
	if ( pass ) {
		if ( JQMIGRATE_WARN ) {
			migrateWarn("jQuery.fn.attr( props, pass ) is deprecated");
		}
		if ( elem && !rnoAttrNodeType.test( elem.nodeType ) && jQuery.isFunction( jQuery.fn[ name ] ) ) {
			return jQuery( elem )[ name ]( value );
		}
	}

	// Warn if user tries to set `type` since it breaks on IE 6/7/8
	if ( JQMIGRATE_WARN && name === "type" && value !== undefined && rnoType.test( elem.nodeName ) ) {
		migrateWarn("Can't change the 'type' of an input or button in IE 6/7/8");
	}

	return attr.call( jQuery, elem, name, value );
};

// attrHooks: value
jQuery.attrHooks.value = {
	get: function( elem, name ) {
		if ( jQuery.nodeName( elem, "button" ) ) {
			return valueAttrGet.apply( this, arguments );
		}
		if ( JQMIGRATE_WARN ) {
			migrateWarn("property-based jQuery.fn.attr('value') is deprecated");
		}
		return name in elem ?
			elem.value :
			null;
	},
	set: function( elem, value, name ) {
		if ( jQuery.nodeName( elem, "button" ) ) {
			return valueAttrSet.apply( this, arguments );
		}
		if ( JQMIGRATE_WARN ) {
			migrateWarn("property-based jQuery.fn.attr('value', val) is deprecated");
		}
		// Does not return so that setAttribute is also used
		elem.value = value;
	}
};


var matched, browser,
	oldAccess = jQuery.access,
	oldInit = jQuery.fn.init,
	// Note this does NOT include the # XSS fix from 1.7!
	rquickExpr = /^(?:.*(<[\w\W]+>)[^>]*|#([\w-]*))$/;

// $(html) "looks like html" rule change
jQuery.fn.init = function( selector, context, rootjQuery ) {
	var match;

	if ( selector && typeof selector === "string" && !jQuery.isPlainObject( context ) &&
			(match = rquickExpr.exec( selector )) && match[1] ) {
		// This is an HTML string according to the "old" rules; is it still?
		if ( selector.charAt( 0 ) !== "<" ) {
			migrateWarn("$(html) HTML strings must start with '<' character");
		}
		// Now process using loose rules; let pre-1.8 play too
		if ( context && context.context ) {
			// jQuery object as context; parseHTML expects a DOM object
			context = context.context;
		}
		if ( jQuery.parseHTML ) {
			return oldInit.call( this, jQuery.parseHTML( jQuery.trim(selector), context, true ),
					context, rootjQuery );
		}
	}
	return oldInit.apply( this, arguments );
};
jQuery.fn.init.prototype = jQuery.fn;

if ( jQuery.fn.jquery >= "1.9" ) {
	// jQuery.access( ..., pass )
	jQuery.access = function( elems, fn, key, value, chainable, emptyGet, pass ) {
		var i = 0,
			length = elems.length;

		if ( key && typeof key === "object" && value ) {
			for ( i in key ) {
				jQuery.access( elems, fn, i, key[i], true, emptyGet, value );
			}
			return elems;
		} else if ( pass && key != null && value !== undefined ) {
			for ( ; i < length; i++ ) {
				fn( elems[i], key, value, true );
			}
			return elems;
		}
		return oldAccess.call( jQuery, elems, fn, key, value, chainable, emptyGet );
	};
}

jQuery.uaMatch = function( ua ) {
	ua = ua.toLowerCase();

	var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
		/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
		/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
		/(msie) ([\w.]+)/.exec( ua ) ||
		ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
		[];

	return {
		browser: match[ 1 ] || "",
		version: match[ 2 ] || "0"
	};
};

matched = jQuery.uaMatch( navigator.userAgent );
browser = {};

if ( matched.browser ) {
	browser[ matched.browser ] = true;
	browser.version = matched.version;
}

// Chrome is Webkit, but Webkit is also Safari.
if ( browser.chrome ) {
	browser.webkit = true;
} else if ( browser.webkit ) {
	browser.safari = true;
}

jQuery.browser = browser;

if ( JQMIGRATE_WARN ) {
	// Warn if the code tries to get jQuery.browser
	migrateWarnProp( jQuery, "browser", browser, "jQuery.browser is deprecated" );
}

jQuery.sub = function() {
	function jQuerySub( selector, context ) {
		return new jQuerySub.fn.init( selector, context );
	}
	jQuery.extend( true, jQuerySub, this );
	jQuerySub.superclass = this;
	jQuerySub.fn = jQuerySub.prototype = this();
	jQuerySub.fn.constructor = jQuerySub;
	jQuerySub.sub = this.sub;
	jQuerySub.fn.init = function init( selector, context ) {
		if ( context && context instanceof jQuery && !(context instanceof jQuerySub) ) {
			context = jQuerySub( context );
		}

		return jQuery.fn.init.call( this, selector, context, rootjQuerySub );
	};
	jQuerySub.fn.init.prototype = jQuerySub.fn;
	var rootjQuerySub = jQuerySub(document);
	if ( JQMIGRATE_WARN ) {
		migrateWarn( "jQuery.sub() is deprecated" );
	}
	return jQuerySub;
};


var oldFnData = jQuery.fn.data;

jQuery.fn.data = function( name ) {
	var ret, evt,
		elem = this[0];

	// Handles 1.7 which has this behavior and 1.8 which doesn't
	if ( elem && name === "events" && arguments.length === 1 ) {
		ret = jQuery.data( elem, name );
		evt = jQuery._data( elem, name );
		if ( ( ret === undefined || ret === evt ) && evt !== undefined ) {
			if ( JQMIGRATE_WARN ) {
				migrateWarn("Use of jQuery.fn.data('events') is deprecated");
			}
			return evt;
		}
	}
	return oldFnData.apply( this, arguments );
};


var oldSelf = jQuery.fn.andSelf || jQuery.fn.addBack,
	oldFragment = jQuery.buildFragment;

jQuery.fn.andSelf = function() {
	migrateWarn("jQuery.fn.andSelf() replaced by jQuery.fn.addBack()");
	return oldSelf.apply( this, arguments );
};

jQuery.buildFragment = function( args, context, scripts ) {
	var fragment;

	if ( !oldFragment ) {
		// Set context from what may come in as undefined or a jQuery collection or a node
		// Updated to fix #12266 where accessing context[0] could throw an exception in IE9/10 &
		// also doubles as fix for #8950 where plain objects caused createDocumentFragment exception
		context = context || document;
		context = !context.nodeType && context[0] || context;
		context = context.ownerDocument || context;

		fragment = context.createDocumentFragment();
		jQuery.clean( args, context, fragment, scripts );

		migrateWarn("jQuery.buildFragment() is deprecated");
		return { fragment: fragment, cacheable: false };
	}
	// Don't warn if we are in a version where buildFragment is used internally
	return oldFragment.apply( this, arguments );
};

var eventAdd = jQuery.event.add,
	eventRemove = jQuery.event.remove,
	eventTrigger = jQuery.event.trigger,
	oldToggle = jQuery.event.toggle,
	oldLive = jQuery.fn.live,
	oldDie = jQuery.fn.die,
	ajaxEvents = "ajaxStart|ajaxStop|ajaxSend|ajaxComplete|ajaxError|ajaxSuccess",
	rajaxEvent = new RegExp( "\\b(?:" + ajaxEvents + ")\\b" ),
	rhoverHack = /(?:^|\s)hover(\.\S+|)\b/,
	hoverHack = function( events ) {
		if ( typeof( events ) != "string" || jQuery.event.special.hover ) {
			return events;
		}
		if ( JQMIGRATE_WARN && rhoverHack.test( events ) ) {
			migrateWarn("'hover' pseudo-event is deprecated, use 'mouseenter mouseleave'");
		}
		return events && events.replace( rhoverHack, "mouseenter$1 mouseleave$1" );
	};

// Event props removed in 1.9, put them back if needed; no practical way to warn them
if ( jQuery.event.props && jQuery.event.props[ 0 ] !== "attrChange" ) {
	jQuery.event.props.unshift( "attrChange", "attrName", "relatedNode", "srcElement" );
}

// Support for 'hover' pseudo-event and ajax event warnings
jQuery.event.add = function( elem, types, handler, data, selector ){
	if ( JQMIGRATE_WARN && elem !== document && rajaxEvent.test( types ) ) {
		migrateWarn( "AJAX events should be attached to document: " + types );
	}
	eventAdd.call( this, elem, hoverHack( types || "" ), handler, data, selector );
};
jQuery.event.remove = function( elem, types, handler, selector, mappedTypes ){
	eventRemove.call( this, elem, hoverHack( types ) || "", handler, selector, mappedTypes );
};

jQuery.fn.error = function( data, fn ) {
	var args = Array.prototype.slice.call( arguments, 0);
	if ( JQMIGRATE_WARN ) {
		migrateWarn("jQuery.fn.error() is deprecated");
	}
	args.splice( 0, 0, "error" );
	if ( arguments.length ) {
		return this.bind.apply( this, args );
	}
	// error event should not bubble to window, although it does pre-1.7
	this.triggerHandler.apply( this, args );
	return this;
};

jQuery.fn.toggle = function( fn, fn2 ) {

	// Don't mess with animation or css toggles
	if ( !jQuery.isFunction( fn ) || !jQuery.isFunction( fn2 ) ) {
		return oldToggle.apply( this, arguments );
	}
	if ( JQMIGRATE_WARN ) {
		migrateWarn("jQuery.fn.toggle(handler, handler...) is deprecated");
	}

	// Save reference to arguments for access in closure
	var args = arguments,
		guid = fn.guid || jQuery.guid++,
		i = 0,
		toggler = function( event ) {
			// Figure out which function to execute
			var lastToggle = ( jQuery._data( this, "lastToggle" + fn.guid ) || 0 ) % i;
			jQuery._data( this, "lastToggle" + fn.guid, lastToggle + 1 );

			// Make sure that clicks stop
			event.preventDefault();

			// and execute the function
			return args[ lastToggle ].apply( this, arguments ) || false;
		};

	// link all the functions, so any of them can unbind this click handler
	toggler.guid = guid;
	while ( i < args.length ) {
		args[ i++ ].guid = guid;
	}

	return this.click( toggler );
};

jQuery.fn.live = function( types, data, fn ) {
	if ( JQMIGRATE_WARN ) {
		migrateWarn("jQuery.fn.live() is deprecated");
	}
	if ( oldLive ) {
		return oldLive.apply( this, arguments );
	}
	jQuery( this.context ).on( types, this.selector, data, fn );
	return this;
};

jQuery.fn.die = function( types, fn ) {
	if ( JQMIGRATE_WARN ) {
		migrateWarn("jQuery.fn.die() is deprecated");
	}
	if ( oldDie ) {
		return oldDie.apply( this, arguments );
	}
	jQuery( this.context ).off( types, this.selector || "**", fn );
	return this;
};

// Turn global events into document-triggered events
jQuery.event.trigger = function( event, data, elem, onlyHandlers  ){
	if ( JQMIGRATE_WARN && !elem & !rajaxEvent.test( event ) ) {
		migrateWarn( "Global events are undocumented and deprecated" );
	}
	return eventTrigger.call( this,  event, data, elem || document, onlyHandlers  );
};
jQuery.each( ajaxEvents.split("|"),
	function( _, name ) {
		jQuery.event.special[ name ] = {
			setup: function( data ) {
				var elem = this;

				// The document needs no shimming; must be !== for oldIE
				if ( elem !== document ) {
					jQuery.event.add( document, name + "." + jQuery.guid, function( event ) {
						jQuery.event.trigger( name, null, elem, true );
					});
					jQuery._data( this, name, jQuery.guid++ );
				}
				return false;
			},
			teardown: function( data, handleObj ) {
				if ( this !== document ) {
					jQuery.event.remove( document, name + "." + jQuery._data( this, name ) );
				}
				return false;
			}
		}
	}
);


})( jQuery, window );
