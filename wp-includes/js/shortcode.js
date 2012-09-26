// Utility functions for parsing and handling shortcodes in Javascript.

// Ensure the global `wp` object exists.
if ( typeof wp === 'undefined' )
	var wp = {};

(function(){
	wp.shortcode = {
		// ### Find the next matching shortcode
		//
		// Given a shortcode `tag`, a block of `text, and an optional starting
		// `index`, returns the next matching shortcode or `undefined`.
		//
		// Shortcodes are formatted as an object that contains the match
		// `content`, the matching `index`, and the parsed `shortcode` object.
		next: function( tag, text, index ) {
			var re = wp.shortcode.regexp( tag ),
				match, result;

			re.lastIndex = index || 0;
			match = re.exec( text );

			if ( ! match )
				return;

			// If we matched an escaped shortcode, try again.
			if ( match[1] === '[' && match[6] === ']' )
				return wp.shortcode.next( tag, text, re.lastIndex );

			result = {
				index:     match.index,
				content:   match[0],
				shortcode: new wp.shortcode.Match( match )
			};

			// If we matched a leading `[`, strip it from the match
			// and increment the index accordingly.
			if ( match[1] ) {
				result.match = result.match.slice( 1 );
				result.index++;
			}

			// If we matched a trailing `]`, strip it from the match.
			if ( match[6] )
				result.match = result.match.slice( 0, -1 );

			return result;
		},

		// ### Replace matching shortcodes in a block of text.
		//
		// Accepts a shortcode `tag`, content `text` to scan, and a `callback`
		// to process the shortcode matches and return a replacement string.
		// Returns the `text` with all shortcodes replaced.
		//
		// Shortcode matches are objects that contain the shortcode `tag`,
		// a shortcode `attrs` object, the `content` between shortcode tags,
		// and a boolean flag to indicate if the match was a `single` tag.
		replace: function( tag, text, callback ) {
			return text.replace( wp.shortcode.regexp( tag ), function( match, left, tag, attrs, closing, content, right, offset ) {
				// If both extra brackets exist, the shortcode has been
				// properly escaped.
				if ( left === '[' && right === ']' )
					return match;

				// Create the match object and pass it through the callback.
				var result = callback( new wp.shortcode.Match( arguments ) );

				// Make sure to return any of the extra brackets if they
				// weren't used to escape the shortcode.
				return result ? left + result + right : match;
			});
		},

		// ### Generate a shortcode RegExp.
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
		// 6. An extra `]` to allow for escaping shortcodes with double `[[]]`
		regexp: _.memoize( function( tag ) {
			return new RegExp( '\\[(\\[?)(' + tag + ')\\b([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)\\[\\/\\2\\])?)(\\]?)', 'g' );
		}),


		// ### Parse shortcode attributes.
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
			// 8. An unquoted numeric attribute.
			pattern = /(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/g;

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
				}
			}

			return {
				named:   named,
				numeric: numeric
			};
		})
	};


	// Shortcode Matches
	// -----------------
	//
	// Shortcode matches are generated automatically when using
	// `wp.shortcode.next()` and `wp.shortcode.replace()`. These two methods
	// should handle most shortcode needs.
	//
	// Accepts a `match` object from calling `regexp.exec()` on a `RegExp`
	// generated by `wp.shortcode.regexp()`. `match` can also be set to the
	// `arguments` from a callback passed to `regexp.replace()`.
	wp.shortcode.Match = function( match ) {
		this.tag     = match[2];
		this.attrs   = wp.shortcode.attrs( match[3] );
		this.single  = !! match[4];
		this.content = match[5];
	};

	_.extend( wp.shortcode.Match.prototype, {
		// ### Get a shortcode attribute.
		//
		// Automatically detects whether `attr` is named or numeric and routes
		// it accordingly.
		get: function( attr ) {
			return this.attrs[ _.isNumber( attr ) ? 'numeric' : 'named' ][ attr ];
		},

		// ### Set a shortcode attribute.
		//
		// Automatically detects whether `attr` is named or numeric and routes
		// it accordingly.
		set: function( attr, value ) {
			this.attrs[ _.isNumber( attr ) ? 'numeric' : 'named' ][ attr ] = value;
			return this;
		},

		// ### Transform the shortcode match into text.
		text: function() {
			var text    = '[' + this.tag;

			_.each( this.attrs.numeric, function( value ) {
				if ( /\s/.test( value ) )
					text += ' "' + value + '"';
				else
					text += ' ' + value;
			});

			_.each( this.attrs.named, function( value, name ) {
				text += ' ' + name + '="' + value + '"';
			});

			// If the tag is marked as singular, self-close the tag and
			// ignore any additional content.
			if ( this.single )
				return text + ' /]';

			// Complete the opening tag.
			text += ']';

			if ( this.content )
				text += this.content;

			// Add the closing tag.
			return text + '[/' + this.tag + ']';
		}
	});
}());
