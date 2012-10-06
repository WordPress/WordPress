// Ensure the global `wp` object exists.
window.wp = window.wp || {};

// HTML utility functions
// ----------------------
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
			if ( '/' === content[ content.length - 1 ] )
				content = content.slice( 0, -1 );

			result = wp.shortcode.attrs( content );
			attrs  = result.named;

			_.each( result.numeric, function( key ) {
				if ( /\s/.test( key ) )
					return;

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

				// Use empty attribute notation where possible.
				if ( '' === value )
					return;

				// Convert boolean values to strings.
				if ( _.isBoolean( value ) )
					value = value ? 'true' : 'false';

				text += '="' + value + '"';
			});

			// Return the result if it is a self-closing tag.
			if ( options.single )
				return text + ' />';

			// Complete the opening tag.
			text += '>';

			// If `content` is an object, recursively call this function.
			text += _.isObject( content ) ? wp.html.string( content ) : content;

			return text + '</' + options.tag + '>';
		}
	});
}());

(function($){
	var views = {},
		instances = {};

	// Create the `wp.mce` object if necessary.
	wp.mce = wp.mce || {};

	// wp.mce.view
	// -----------
	// A set of utilities that simplifies adding custom UI within a TinyMCE editor.
	// At its core, it serves as a series of converters, transforming text to a
	// custom UI, and back again.
	wp.mce.view = {
		// ### defaults
		defaults: {
			// The default properties used for objects with the `pattern` key in
			// `wp.mce.view.add()`.
			pattern: {
				view: Backbone.View,
				text: function( instance ) {
					return instance.options.original;
				},

				toView: function( content ) {
					if ( ! this.pattern )
						return;

					this.pattern.lastIndex = 0;
					var match = this.pattern.exec( content );

					if ( ! match )
						return;

					return {
						index:   match.index,
						content: match[0],
						options: {
							original: match[0],
							results:  match
						}
					};
				}
			},

			// The default properties used for objects with the `shortcode` key in
			// `wp.mce.view.add()`.
			shortcode: {
				view: Backbone.View,
				text: function( instance ) {
					return instance.options.shortcode.string();
				},

				toView: function( content ) {
					var match = wp.shortcode.next( this.shortcode, content );

					if ( ! match )
						return;

					return {
						index:   match.index,
						content: match.content,
						options: {
							shortcode: match.shortcode
						}
					};
				}
			}
		},

		// ### add( id, options )
		// Registers a new TinyMCE view.
		//
		// Accepts a unique `id` and an `options` object.
		//
		// `options` accepts the following properties:
		//
		// * `pattern` is the regular expression used to scan the content and
		// detect matching views.
		//
		// * `view` is a `Backbone.View` constructor. If a plain object is
		// provided, it will automatically extend the parent constructor
		// (usually `Backbone.View`). Views are instantiated when the `pattern`
		// is successfully matched. The instance's `options` object is provided
		// with the `original` matched value, the match `results` including
		// capture groups, and the `viewType`, which is the constructor's `id`.
		//
		// * `extend` an existing view by passing in its `id`. The current
		// view will inherit all properties from the parent view, and if
		// `view` is set to a plain object, it will extend the parent `view`
		// constructor.
		//
		// * `text` is a method that accepts an instance of the `view`
		// constructor and transforms it into a text representation.
		add: function( id, options ) {
			var parent, remove, base, properties;

			// Fetch the parent view or the default options.
			if ( options.extend )
				parent = wp.mce.view.get( options.extend );
			else if ( options.shortcode )
				parent = wp.mce.view.defaults.shortcode;
			else
				parent = wp.mce.view.defaults.pattern;

			// Extend the `options` object with the parent's properties.
			_.defaults( options, parent );
			options.id = id;

			// Create properties used to enhance the view for use in TinyMCE.
			properties = {
				// Ensure the wrapper element and references to the view are
				// removed. Otherwise, removed views could randomly restore.
				remove: function() {
					delete instances[ this.el.id ];
					this.$el.parent().remove();

					// Trigger the inherited `remove` method.
					if ( remove )
						remove.apply( this, arguments );

					return this;
				}
			};

			// If the `view` provided was an object, use the parent's
			// `view` constructor as a base. If a `view` constructor
			// was provided, treat that as the base.
			if ( _.isFunction( options.view ) ) {
				base = options.view;
			} else {
				base   = parent.view;
				remove = options.view.remove;
				_.defaults( properties, options.view );
			}

			// If there's a `remove` method on the `base` view that wasn't
			// created by this method, inherit it.
			if ( ! remove && ! base._mceview )
				remove = base.prototype.remove;

			// Automatically create the new `Backbone.View` constructor.
			options.view = base.extend( properties, {
				// Flag that the new view has been created by `wp.mce.view`.
				_mceview: true
			});

			views[ id ] = options;
		},

		// ### get( id )
		// Returns a TinyMCE view options object.
		get: function( id ) {
			return views[ id ];
		},

		// ### remove( id )
		// Unregisters a TinyMCE view.
		remove: function( id ) {
			delete views[ id ];
		},

		// ### toViews( content )
		// Scans a `content` string for each view's pattern, replacing any
		// matches with wrapper elements, and creates a new view instance for
		// every match.
		//
		// To render the views, call `wp.mce.view.render( scope )`.
		toViews: function( content ) {
			var pieces = [ { content: content } ],
				current;

			_.each( views, function( view, viewType ) {
				current = pieces.slice();
				pieces  = [];

				_.each( current, function( piece ) {
					var remaining = piece.content,
						result;

					// Ignore processed pieces, but retain their location.
					if ( piece.processed ) {
						pieces.push( piece );
						return;
					}

					// Iterate through the string progressively matching views
					// and slicing the string as we go.
					while ( remaining && (result = view.toView( remaining )) ) {
						// Any text before the match becomes an unprocessed piece.
						if ( result.index )
							pieces.push({ content: remaining.substring( 0, result.index ) });

						// Add the processed piece for the match.
						pieces.push({
							content:   wp.mce.view.toView( viewType, result.options ),
							processed: true
						});

						// Update the remaining content.
						remaining = remaining.slice( result.index + result.content.length );
					}

					// There are no additional matches. If any content remains,
					// add it as an unprocessed piece.
					if ( remaining )
						pieces.push({ content: remaining });
				});
			});

			return _.pluck( pieces, 'content' ).join('');
		},

		toView: function( viewType, options ) {
			var view = wp.mce.view.get( viewType ),
				instance, id, tag;

			if ( ! view )
				return '';

			// Create a new view instance.
			instance = new view.view( _.extend( options || {}, {
				viewType: viewType
			}) );

			// Use the view's `id` if it already exists. Otherwise,
			// create a new `id`.
			id = instance.el.id = instance.el.id || _.uniqueId('__wpmce-');
			instances[ id ] = instance;

			// If the view is a span, wrap it in a span.
			tag = 'span' === instance.tagName ? 'span' : 'div';

			return '<' + tag + ' class="wp-view-wrap" data-wp-view="' + id + '" contenteditable="false"></' + tag + '>';
		},

		// ### render( scope )
		// Renders any view instances inside a DOM node `scope`.
		//
		// View instances are detected by the presence of wrapper elements.
		// To generate wrapper elements, pass your content through
		// `wp.mce.view.toViews( content )`.
		render: function( scope ) {
			$( '.wp-view-wrap', scope ).each( function() {
				var wrapper = $(this),
					id = wrapper.data('wp-view'),
					view = instances[ id ];

				if ( ! view )
					return;

				// Render the view.
				view.render();
				// Detach the view element to ensure events are not unbound.
				view.$el.detach();

				// Empty the wrapper, attach the view element to the wrapper,
				// and add an ending marker to the wrapper to help regexes
				// scan the HTML string.
				wrapper.empty().append( view.el ).append('<span data-wp-view-end></span>');
			});
		},

		// ### toText( content )
		// Scans an HTML `content` string and replaces any view instances with
		// their respective text representations.
		toText: function( content ) {
			return content.replace( /<(?:div|span)[^>]+data-wp-view="([^"]+)"[^>]*>.*?<span data-wp-view-end[^>]*><\/span><\/(?:div|span)>/g, function( match, id ) {
				var instance = instances[ id ],
					view;

				if ( instance )
					view = wp.mce.view.get( instance.options.viewType );

				return instance && view ? view.text( instance ) : '';
			});
		},

		// ### Remove internal TinyMCE attributes.
		removeInternalAttrs: function( attrs ) {
			var result = {};
			_.each( attrs, function( value, attr ) {
				if ( -1 === attr.indexOf('data-mce') )
					result[ attr ] = value;
			});
			return result;
		},

		// ### Parse an attribute string and removes internal TinyMCE attributes.
		attrs: function( content ) {
			return wp.mce.view.removeInternalAttrs( wp.html.attrs( content ) );
		},

		// Link any localized strings.
		l10n: _.isUndefined( _wpMceViewL10n ) ? {} : _wpMceViewL10n
	};

}(jQuery));

// Default TinyMCE Views
// ---------------------
(function($){
	var mceview = wp.mce.view;

	wp.media.string = {};
	wp.media.string.image = function( attachment, props ) {
		var classes, img, options, size;

		attachment = attachment.toJSON();

		props = _.defaults( props || {}, {
			img:   {},
			align: getUserSetting( 'align', 'none' ),
			size:  getUserSetting( 'imgsize', 'medium' ),
			link:  getUserSetting( 'urlbutton', 'post' )
		});

		img     = _.clone( props.img );
		classes = img['class'] ? img['class'].split(/\s+/) : [];
		size    = attachment.sizes ? attachment.sizes[ props.size ] : {};

		if ( ! size )
			delete props.size;

		img.width  = size.width  || attachment.width;
		img.height = size.height || attachment.height;
		img.src    = size.url    || attachment.url;

		// Update `img` classes.
		if ( props.align )
			classes.push( 'align' + props.align );

		if ( props.size )
			classes.push( 'size-' + props.size );

		classes.push( 'wp-image-' + attachment.id );

		img['class'] = _.compact( classes ).join(' ');

		// Generate `img` tag options.
		options = {
			tag:    'img',
			attrs:  img,
			single: true
		};

		// Generate the `a` element options, if they exist.
		if ( props.anchor ) {
			options = {
				tag:     'a',
				attrs:   props.anchor,
				content: options
			};
		}

		return wp.html.string( options );
	};

	mceview.add( 'attachment', {
		pattern: new RegExp( '(?:<a([^>]*)>)?<img([^>]*class=(?:"[^"]*|\'[^\']*)\\bwp-image-(\\d+)[^>]*)>(?:</a>)?' ),

		text: function( instance ) {
			var props = _.pick( instance, 'align', 'size', 'link', 'img', 'anchor' );
			return wp.media.string.image( instance.model, props );
		},

		view: {
			className: 'editor-attachment',
			template:  media.template('editor-attachment'),

			events: {
				'click .close': 'remove'
			},

			initialize: function() {
				var view    = this,
					results = this.options.results,
					id      = results[3],
					className;

				this.model = wp.media.model.Attachment.get( id );

				if ( results[1] )
					this.anchor = mceview.attrs( results[1] );

				this.img  = mceview.attrs( results[2] );
				className = this.img['class'];

				// Strip ID class.
				className = className.replace( /(?:^|\s)wp-image-\d+/, '' );

				// Calculate thumbnail `size` and remove class.
				className = className.replace( /(?:^|\s)size-(\S+)/, function( match, size ) {
					view.size = size;
					return '';
				});

				// Calculate `align` and remove class.
				className = className.replace( /(?:^|\s)align(left|center|right|none)(?:\s|$)/, function( match, align ) {
					view.align = align;
					return '';
				});

				this.img['class'] = className;

				this.$el.addClass('spinner');
				this.model.fetch().done( _.bind( this.render, this ) );
			},

			render: function() {
				var attachment = this.model.toJSON(),
					options;

				// If we don't have the attachment data, bail.
				if ( ! attachment.url )
					return;

				options = {
					url: 'image' === attachment.type ? attachment.url : attachment.icon,
					uploading: attachment.uploading
				};

				_.extend( options, wp.media.fit({
					width:    attachment.width,
					height:   attachment.height,
					maxWidth: mceview.l10n.contentWidth
				}) );

				// Use the specified size if it exists.
				if ( this.size && attachment.sizes && attachment.sizes[ this.size ] )
					_.extend( options, _.pick( attachment.sizes[ this.size ], 'url', 'width', 'height' ) );

				this.$el.html( this.template( options ) );
			}
		}
	});

	mceview.add( 'gallery', {
		shortcode: 'gallery',

		gallery: (function() {
			var galleries = {};

			return {
				attachments: function( shortcode, parent ) {
					var shortcodeString = shortcode.string(),
						result = galleries[ shortcodeString ],
						attrs, args;

					delete galleries[ shortcodeString ];

					if ( result )
						return result;

					attrs = shortcode.attrs.named;
					args  = _.pick( attrs, 'orderby', 'order' );

					args.type    = 'image';
					args.perPage = -1;

					// Map the `ids` param to the correct query args.
					if ( attrs.ids ) {
						args.post__in = attrs.ids.split(',');
						args.orderby  = 'post__in';
					} else if ( attrs.include ) {
						args.post__in = attrs.include.split(',');
					}

					if ( attrs.exclude )
						args.post__not_in = attrs.exclude.split(',');

					if ( ! args.post__in )
						args.parent = attrs.id || parent;

					return media.query( args );
				},

				shortcode: function( attachments ) {
					var attrs = _.pick( attachments.props.toJSON(), 'include', 'exclude', 'orderby', 'order' ),
						shortcode;

					attrs.ids = attachments.pluck('id');

					shortcode = new wp.shortcode({
						tag:    'gallery',
						attrs:  attrs,
						type:   'single'
					});

					galleries[ shortcode.string() ] = attachments;
					return shortcode;
				}
			};
		}()),

		view: {
			className: 'editor-gallery',
			template:  media.template('editor-gallery'),

			// The fallback post ID to use as a parent for galleries that don't
			// specify the `ids` or `include` parameters.
			//
			// Uses the hidden input on the edit posts page by default.
			parent: $('#post_ID').val(),

			events: {
				'click .close': 'remove'
			},

			initialize: function() {
				var	view      = mceview.get('gallery'),
					shortcode = this.options.shortcode;

				this.attachments = view.gallery.attachments( shortcode, this.parent );
				this.attachments.more().done( _.bind( this.render, this ) );
			},

			render: function() {
				var options, thumbnail, size;

				if ( ! this.attachments.length )
					return;

				thumbnail = this.attachments.first().toJSON();
				size = thumbnail.sizes && thumbnail.sizes.thumbnail ? thumbnail.sizes.thumbnail : thumbnail;

				options = {
					url:         size.url,
					orientation: size.orientation,
					count:       this.attachments.length
				};

				this.$el.html( this.template( options ) );
			}
		}
	});
}(jQuery));