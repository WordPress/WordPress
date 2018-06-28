/**
 * Utility functions for parsing and handling shortcodes in JavaScript.
 *
 * @output wp-includes/js/shortcode.js
 */

/**
 * Ensure the global `wp` object exists.
 *
 * @namespace wp
 */
window.wp = window.wp || {};

(function(){
	wp.shortcode = {
		// ### Find the next matching shortcode
		//
		// Given a shortcode `tag`, a block of `text`, and an optional starting
		// `index`, returns the next matching shortcode or `undefined`.
		//
		// Shortcodes are formatted as an object that contains the match
		// `content`, the matching `index`, and the parsed `shortcode` object.
		next: function( tag, text, index ) {
			var re = wp.shortcode.regexp( tag ),
				match, result;

			re.lastIndex = index || 0;
			match = re.exec( text );

			if ( ! match ) {
				return;
			}

			// If we matched an escaped shortcode, try again.
			if ( '[' === match[1] && ']' === match[7] ) {
				return wp.shortcode.next( tag, text, re.lastIndex );
			}

			result = {
				index:     match.index,
				content:   match[0],
				shortcode: wp.shortcode.fromMatch( match )
			};

			// If we matched a leading `[`, strip it from the match
			// and increment the index accordingly.
			if ( match[1] ) {
				result.content = result.content.slice( 1 );
				result.index++;
			}

			// If we matched a trailing `]`, strip it from the match.
			if ( match[7] ) {
				result.content = result.content.slice( 0, -1 );
			}

			return result;
		},

		// ### Replace matching shortcodes in a block of text
		//
		// Accepts a shortcode `tag`, content `text` to scan, and a `callback`
		// to process the shortcode matches and return a replacement string.
		// Returns the `text` with all shortcodes replaced.
		//
		// Shortcode matches are objects that contain the shortcode `tag`,
		// a shortcode `attrs` object, the `content` between shortcode tags,
		// and a boolean flag to indicate if the match was a `single` tag.
		replace: function( tag, text, callback ) {
			return text.replace( wp.shortcode.regexp( tag ), function( match, left, tag, attrs, slash, content, closing, right ) {
				// If both extra brackets exist, the shortcode has been
				// properly escaped.
				if ( left === '[' && right === ']' ) {
					return match;
				}

				// Create the match object and pass it through the callback.
				var result = callback( wp.shortcode.fromMatch( arguments ) );

				// Make sure to return any of the extra brackets if they
				// weren't used to escape the shortcode.
				return result ? left + result + right : match;
			});
		},

		// ### Generate a string from shortcode parameters
		//
		// Creates a `wp.shortcode` instance and returns a string.
		//
		// Accepts the same `options` as the `wp.shortcode()` constructor,
		// containing a `tag` string, a string or object of `attrs`, a boolean
		// indicating whether to format the shortcode using a `single` tag, and a
		// `content` string.
		string: function( options ) {
			return new wp.shortcode( options ).string();
		},

		// ### Generate a RegExp to identify a shortcode
		//
		// The base regex is functionally equivalent to the one found in
		// `get_shortcode_regex()` in `wp-includes/shortcodes.php`.
		//
		// Capture groups:
		//
		// 1. An extra `[` to allow for escaping shortcodes with double `[[]]`
		// 2. The shortcode name
		// 3. The shortcode argument list
		// 4. The self closing `/`
		// 5. The content of a shortcode when it wraps some content.
		// 6. The closing tag.
		// 7. An extra `]` to allow for escaping shortcodes with double `[[]]`
		regexp: _.memoize( function( tag ) {
			return new RegExp( '\\[(\\[?)(' + tag + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)', 'g' );
		}),


		// ### Parse shortcode attributes
		//
		// Shortcodes accept many types of attributes. These can chiefly be
		// divided into named and numeric attributes:
		//
		// Named attributes are assigned on a key/value basis, while numeric
		// attributes are treated as an array.
		//
		// Named attributes can be formatted as either `name="value"`,
		// `name='value'`, or `name=value`. Numeric attributes can be formatted
		// as `"value"` or just `value`.
		attrs: _.memoize( function( text ) {
			var named   = {},
				numeric = [],
				pattern, match;

			// This regular expression is reused from `shortcode_parse_atts()`
			// in `wp-includes/shortcodes.php`.
			//
			// Capture groups:
			//
			// 1. An attribute name, that corresponds to...
			// 2. a value in double quotes.
			// 3. An attribute name, that corresponds to...
			// 4. a value in single quotes.
			// 5. An attribute name, that corresponds to...
			// 6. an unquoted value.
			// 7. A numeric attribute in double quotes.
			// 8. A numeric attribute in single quotes.
			// 9. An unquoted numeric attribute.
			pattern = /([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*'([^']*)'(?:\s|$)|([\w-]+)\s*=\s*([^\s'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|'([^']*)'(?:\s|$)|(\S+)(?:\s|$)/g;

			// Map zero-width spaces to actual spaces.
			text = text.replace( /[\u00a0\u200b]/g, ' ' );

			// Match and normalize attributes.
			while ( (match = pattern.exec( text )) ) {
				if ( match[1] ) {
					named[ match[1].toLowerCase() ] = match[2];
				} else if ( match[3] ) {
					named[ match[3].toLowerCase() ] = match[4];
				} else if ( match[5] ) {
					named[ match[5].toLowerCase() ] = match[6];
				} else if ( match[7] ) {
					numeric.push( match[7] );
				} else if ( match[8] ) {
					numeric.push( match[8] );
				} else if ( match[9] ) {
					numeric.push( match[9] );
				}
			}

			return {
				named:   named,
				numeric: numeric
			};
		}),

		// ### Generate a Shortcode Object from a RegExp match
		// Accepts a `match` object from calling `regexp.exec()` on a `RegExp`
		// generated by `wp.shortcode.regexp()`. `match` can also be set to the
		// `arguments` from a callback passed to `regexp.replace()`.
		fromMatch: function( match ) {
			var type;

			if ( match[4] ) {
				type = 'self-closing';
			} else if ( match[6] ) {
				type = 'closed';
			} else {
				type = 'single';
			}

			return new wp.shortcode({
				tag:     match[2],
				attrs:   match[3],
				type:    type,
				content: match[5]
			});
		}
	};


	// Shortcode Objects
	// -----------------
	//
	// Shortcode objects are generated automatically when using the main
	// `wp.shortcode` methods: `next()`, `replace()`, and `string()`.
	//
	// To access a raw representation of a shortcode, pass an `options` object,
	// containing a `tag` string, a string or object of `attrs`, a string
	// indicating the `type` of the shortcode ('single', 'self-closing', or
	// 'closed'), and a `content` string.
	wp.shortcode = _.extend( function( options ) {
		_.extend( this, _.pick( options || {}, 'tag', 'attrs', 'type', 'content' ) );

		var attrs = this.attrs;

		// Ensure we have a correctly formatted `attrs` object.
		this.attrs = {
			named:   {},
			numeric: []
		};

		if ( ! attrs ) {
			return;
		}

		// Parse a string of attributes.
		if ( _.isString( attrs ) ) {
			this.attrs = wp.shortcode.attrs( attrs );

		// Identify a correctly formatted `attrs` object.
		} else if ( _.isEqual( _.keys( attrs ), [ 'named', 'numeric' ] ) ) {
			this.attrs = attrs;

		// Handle a flat object of attributes.
		} else {
			_.each( options.attrs, function( value, key ) {
				this.set( key, value );
			}, this );
		}
	}, wp.shortcode );

	_.extend( wp.shortcode.prototype, {
		// ### Get a shortcode attribute
		//
		// Automatically detects whether `attr` is named or numeric and routes
		// it accordingly.
		get: function( attr ) {
			return this.attrs[ _.isNumber( attr ) ? 'numeric' : 'named' ][ attr ];
		},

		// ### Set a shortcode attribute
		//
		// Automatically detects whether `attr` is named or numeric and routes
		// it accordingly.
		set: function( attr, value ) {
			this.attrs[ _.isNumber( attr ) ? 'numeric' : 'named' ][ attr ] = value;
			return this;
		},

		// ### Transform the shortcode match into a string
		string: function() {
			var text    = '[' + this.tag;

			_.each( this.attrs.numeric, function( value ) {
				if ( /\s/.test( value ) ) {
					text += ' "' + value + '"';
				} else {
					text += ' ' + value;
				}
			});

			_.each( this.attrs.named, function( value, name ) {
				text += ' ' + name + '="' + value + '"';
			});

			// If the tag is marked as `single` or `self-closing`, close the
			// tag and ignore any additional content.
			if ( 'single' === this.type ) {
				return text + ']';
			} else if ( 'self-closing' === this.type ) {
				return text + ' /]';
			}

			// Complete the opening tag.
			text += ']';

			if ( this.content ) {
				text += this.content;
			}

			// Add the closing tag.
			return text + '[/' + this.tag + ']';
		}
	});
}());

// HTML utility functions
// ----------------------
//
// Experimental. These functions may change or be removed in the future.
(function(){
	wp.html = _.extend( wp.html || {}, {
		// ### Parse HTML attributes.
		//
		// Converts `content` to a set of parsed HTML attributes.
		// Utilizes `wp.shortcode.attrs( content )`, which is a valid superset of
		// the HTML attribute specification. Reformats the attributes into an
		// object that contains the `attrs` with `key:value` mapping, and a record
		// of the attributes that were entered using `empty` attribute syntax (i.e.
		// with no value).
		attrs: function( content ) {
			var result, attrs;

			// If `content` ends in a slash, strip it.
			if ( '/' === content[ content.length - 1 ] ) {
				content = content.slice( 0, -1 );
			}

			result = wp.shortcode.attrs( content );
			attrs  = result.named;

			_.each( result.numeric, function( key ) {
				if ( /\s/.test( key ) ) {
					return;
				}

				attrs[ key ] = '';
			});

			return attrs;
		},

		// ### Convert an HTML-representation of an object to a string.
		string: function( options ) {
			var text = '<' + options.tag,
				content = options.content || '';

			_.each( options.attrs, function( value, attr ) {
				text += ' ' + attr;

				// Convert boolean values to strings.
				if ( _.isBoolean( value ) ) {
					value = value ? 'true' : 'false';
				}

				text += '="' + value + '"';
			});

			// Return the result if it is a self-closing tag.
			if ( options.single ) {
				return text + ' />';
			}

			// Complete the opening tag.
			text += '>';

			// If `content` is an object, recursively call this function.
			text += _.isObject( content ) ? wp.html.string( content ) : content;

			return text + '</' + options.tag + '>';
		}
	});
}());
