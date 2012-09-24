if ( typeof wp === 'undefined' )
	var wp = {};

(function($){
	var views = {},
		instances = {};

	wp.mce = {};

	wp.mce.view = {
		// The default properties used for the objects in `wp.mce.view.add()`.
		defaults: {
			view: Backbone.View,
			text: function( instance ) {
				return instance.options.original;
			}
		},

		// Registers a new TinyMCE view.
		//
		// Accepts a unique `id` and an `options` object.
		//
		// `options` accepts the following properties:
		//
		// `pattern` is the regular expression used to scan the content and
		// detect matching views.
		//
		// `view` is a `Backbone.View` constructor. If a plain object is
		// provided, it will automatically extend the parent constructor
		// (usually `Backbone.View`). Views are instantiated when the `pattern`
		// is successfully matched. The instance's `options` object is provided
		// with the `original` matched value, the match `results` including
		// capture groups, and the `viewType`, which is the constructor's `id`.
		//
		// `extend` an existing view by passing in its `id`. The current
		// view will inherit all properties from the parent view, and if
		// `view` is set to a plain object, it will extend the parent `view`
		// constructor.
		//
		// `text` is a method that accepts an instance of the `view`
		// constructor and transforms it into a text representation.
		add: function( id, options ) {
			var parent;

			// Fetch the parent view or the default options.
			parent = options.extend ? wp.mce.view.get( options.extend ) : wp.mce.view.defaults;

			// Extend the `options` object with the parent's properties.
			_.defaults( options, parent );
			options.id = id;

			// If the `view` provided was an object, automatically create
			// a new `Backbone.View` constructor, using the parent's `view`
			// constructor as a base.
			if ( ! _.isFunction( options.view ) )
				options.view = parent.view.extend( options.view );

			views[ id ] = options;
		},

		// Returns a TinyMCE view options object.
		get: function( id ) {
			return views[ id ];
		},

		// Unregisters a TinyMCE view.
		remove: function( id ) {
			delete views[ id ];
		},

		// Scans a `content` string for each view's pattern, replacing any
		// matches with wrapper elements, and creates a new view instance for
		// every match.
		//
		// To render the views, call `wp.mce.view.render( scope )`.
		toViews: function( content ) {
			_.each( views, function( view, viewType ) {
				if ( ! view.pattern )
					return;

				// Scan for matches.
				content = content.replace( view.pattern, function( match ) {
					var instance, id, tag;

					// Create a new view instance.
					instance = new view.view({
						original: match,
						results:  _.toArray( arguments ),
						viewType: viewType
					});

					// Use the view's `id` if it already exists. Otherwise,
					// create a new `id`.
					id = instance.el.id = instance.el.id || _.uniqueId('__wpmce-');
					instances[ id ] = instance;

					// If the view is a span, wrap it in a span.
					tag = 'span' === instance.tagName ? 'span' : 'div';

					return '<' + tag + ' class="wp-view-wrap" data-wp-view="' + id + '" contenteditable="false"></' + tag + '>';
				});
			});

			return content;
		},

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
		}
	};

}(jQuery));