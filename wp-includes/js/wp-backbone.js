/**
 * @output wp-includes/js/wp-backbone.js
 */

/** @namespace wp */
window.wp = window.wp || {};

(function ($) {
	/**
	 * Create the WordPress Backbone namespace.
	 *
	 * @namespace wp.Backbone
	 */
	wp.Backbone = {};

	/**
	 * A backbone subview manager.
	 *
	 * @since 3.5.0
	 * @since 3.6.0 Moved wp.media.Views to wp.Backbone.Subviews.
	 *
	 * @memberOf wp.Backbone
	 *
	 * @class
	 *
	 * @param  {wp.Backbone.View} view  The main view.
	 * @param  {Array|Object}     views The subviews for the main view.
	 */
	wp.Backbone.Subviews = function( view, views ) {
		this.view = view;
		this._views = _.isArray( views ) ? { '': views } : views || {};
	};

	wp.Backbone.Subviews.extend = Backbone.Model.extend;

	_.extend( wp.Backbone.Subviews.prototype, {
		/**
		 * Fetches all of the subviews.
		 *
		 * @since 3.5.0
		 *
		 * @return {Array} All the subviews.
		 */
		all: function() {
			return _.flatten( _.values( this._views ) );
		},

		/**
		 * Fetches all subviews that match a given `selector`.
		 *
		 * If no `selector` is provided, it will grab all subviews attached
		 * to the view's root.
		 *
		 * @since 3.5.0
		 *
		 * @param {string} selector A jQuery selector.
		 *
		 * @return {Array} All the subviews that match the selector.
		 */
		get: function( selector ) {
			selector = selector || '';
			return this._views[ selector ];
		},

		/**
		 * Fetches the first subview that matches a given `selector`.
		 *
		 * If no `selector` is provided, it will grab the first subview attached to the
		 * view's root.
		 *
		 * Useful when a selector only has one subview at a time.
		 *
		 * @since 3.5.0
		 *
		 * @param {string} selector A jQuery selector.
		 *
		 * @return {Backbone.View} The view.
		 */
		first: function( selector ) {
			var views = this.get( selector );
			return views && views.length ? views[0] : null;
		},

		/**
		 * Registers subview(s).
		 *
		 * Registers any number of `views` to a `selector`.
		 *
		 * When no `selector` is provided, the root selector (the empty string)
		 * is used. `views` accepts a `Backbone.View` instance or an array of
		 * `Backbone.View` instances.
		 *
		 * ---
		 *
		 * Accepts an `options` object, which has a significant effect on the
		 * resulting behavior.
		 *
		 * `options.silent` - *boolean, `false`*
		 * If `options.silent` is true, no DOM modifications will be made.
		 *
		 * `options.add` - *boolean, `false`*
		 * Use `Views.add()` as a shortcut for setting `options.add` to true.
		 *
		 * By default, the provided `views` will replace any existing views
		 * associated with the selector. If `options.add` is true, the provided
		 * `views` will be added to the existing views.
		 *
		 * `options.at` - *integer, `undefined`*
		 * When adding, to insert `views` at a specific index, use `options.at`.
		 * By default, `views` are added to the end of the array.
		 *
		 * @since 3.5.0
		 *
		 * @param {string}       selector A jQuery selector.
		 * @param {Array|Object} views    The subviews for the main view.
		 * @param {Object}       options  Options for call. If `options.silent` is true,
		 *                                no DOM  modifications will be made. Use
		 *                                `Views.add()` as a shortcut for setting
		 *                                `options.add` to true. If `options.add` is
		 *                                true, the provided `views` will be added to
		 *                                the existing views. When adding, to insert
		 *                                `views` at a specific index, use `options.at`.
		 *
		 * @return {wp.Backbone.Subviews} The current Subviews instance.
		 */
		set: function( selector, views, options ) {
			var existing, next;

			if ( ! _.isString( selector ) ) {
				options  = views;
				views    = selector;
				selector = '';
			}

			options  = options || {};
			views    = _.isArray( views ) ? views : [ views ];
			existing = this.get( selector );
			next     = views;

			if ( existing ) {
				if ( options.add ) {
					if ( _.isUndefined( options.at ) ) {
						next = existing.concat( views );
					} else {
						next = existing;
						next.splice.apply( next, [ options.at, 0 ].concat( views ) );
					}
				} else {
					_.each( next, function( view ) {
						view.__detach = true;
					});

					_.each( existing, function( view ) {
						if ( view.__detach )
							view.$el.detach();
						else
							view.remove();
					});

					_.each( next, function( view ) {
						delete view.__detach;
					});
				}
			}

			this._views[ selector ] = next;

			_.each( views, function( subview ) {
				var constructor = subview.Views || wp.Backbone.Subviews,
					subviews = subview.views = subview.views || new constructor( subview );
				subviews.parent   = this.view;
				subviews.selector = selector;
			}, this );

			if ( ! options.silent )
				this._attach( selector, views, _.extend({ ready: this._isReady() }, options ) );

			return this;
		},

		/**
		 * Add subview(s) to existing subviews.
		 *
		 * An alias to `Views.set()`, which defaults `options.add` to true.
		 *
		 * Adds any number of `views` to a `selector`.
		 *
		 * When no `selector` is provided, the root selector (the empty string)
		 * is used. `views` accepts a `Backbone.View` instance or an array of
		 * `Backbone.View` instances.
		 *
		 * Uses `Views.set()` when setting `options.add` to `false`.
		 *
		 * Accepts an `options` object. By default, provided `views` will be
		 * inserted at the end of the array of existing views. To insert
		 * `views` at a specific index, use `options.at`. If `options.silent`
		 * is true, no DOM modifications will be made.
		 *
		 * For more information on the `options` object, see `Views.set()`.
		 *
		 * @since 3.5.0
		 *
		 * @param {string}       selector A jQuery selector.
		 * @param {Array|Object} views    The subviews for the main view.
		 * @param {Object}       options  Options for call.  To insert `views` at a
		 *                                specific index, use `options.at`. If
		 *                                `options.silent` is true, no DOM modifications
		 *                                will be made.
		 *
		 * @return {wp.Backbone.Subviews} The current subviews instance.
		 */
		add: function( selector, views, options ) {
			if ( ! _.isString( selector ) ) {
				options  = views;
				views    = selector;
				selector = '';
			}

			return this.set( selector, views, _.extend({ add: true }, options ) );
		},

		/**
		 * Removes an added subview.
		 *
		 * Stops tracking `views` registered to a `selector`. If no `views` are
		 * set, then all of the `selector`'s subviews will be unregistered and
		 * removed.
		 *
		 * Accepts an `options` object. If `options.silent` is set, `remove`
		 * will *not* be triggered on the unregistered views.
		 *
		 * @since 3.5.0
		 *
		 * @param {string}       selector A jQuery selector.
		 * @param {Array|Object} views    The subviews for the main view.
		 * @param {Object}       options  Options for call. If `options.silent` is set,
		 *                                `remove` will *not* be triggered on the
		 *                                unregistered views.
		 *
		 * @return {wp.Backbone.Subviews} The current Subviews instance.
		 */
		unset: function( selector, views, options ) {
			var existing;

			if ( ! _.isString( selector ) ) {
				options = views;
				views = selector;
				selector = '';
			}

			views = views || [];

			if ( existing = this.get( selector ) ) {
				views = _.isArray( views ) ? views : [ views ];
				this._views[ selector ] = views.length ? _.difference( existing, views ) : [];
			}

			if ( ! options || ! options.silent )
				_.invoke( views, 'remove' );

			return this;
		},

		/**
		 * Detaches all subviews.
		 *
		 * Helps to preserve all subview events when re-rendering the master
		 * view. Used in conjunction with `Views.render()`.
		 *
		 * @since 3.5.0
		 *
		 * @return {wp.Backbone.Subviews} The current Subviews instance.
		 */
		detach: function() {
			$( _.pluck( this.all(), 'el' ) ).detach();
			return this;
		},

		/**
		 * Renders all subviews.
		 *
		 * Used in conjunction with `Views.detach()`.
		 *
		 * @since 3.5.0
		 *
		 * @return {wp.Backbone.Subviews} The current Subviews instance.
		*/
		render: function() {
			var options = {
					ready: this._isReady()
				};

			_.each( this._views, function( views, selector ) {
				this._attach( selector, views, options );
			}, this );

			this.rendered = true;
			return this;
		},

		/**
		 * Removes all subviews.
		 *
		 * Triggers the `remove()` method on all subviews. Detaches the master
		 * view from its parent. Resets the internals of the views manager.
		 *
		 * Accepts an `options` object. If `options.silent` is set, `unset`
		 * will *not* be triggered on the master view's parent.
		 *
		 * @since 3.6.0
		 *
		 * @param {Object}  options        Options for call.
		 * @param {boolean} options.silent If true, `unset` wil *not* be triggered on
		 *                                 the master views' parent.
		 *
		 * @return {wp.Backbone.Subviews} The current Subviews instance.
		*/
		remove: function( options ) {
			if ( ! options || ! options.silent ) {
				if ( this.parent && this.parent.views )
					this.parent.views.unset( this.selector, this.view, { silent: true });
				delete this.parent;
				delete this.selector;
			}

			_.invoke( this.all(), 'remove' );
			this._views = [];
			return this;
		},

		/**
		 * Replaces a selector's subviews
		 *
		 * By default, sets the `$target` selector's html to the subview `els`.
		 *
		 * Can be overridden in subclasses.
		 *
		 * @since 3.5.0
		 *
		 * @param {string} $target Selector where to put the elements.
		 * @param {*} els HTML or elements to put into the selector's HTML.
		 *
		 * @return {wp.Backbone.Subviews} The current Subviews instance.
		 */
		replace: function( $target, els ) {
			$target.html( els );
			return this;
		},

		/**
		 * Insert subviews into a selector.
		 *
		 * By default, appends the subview `els` to the end of the `$target`
		 * selector. If `options.at` is set, inserts the subview `els` at the
		 * provided index.
		 *
		 * Can be overridden in subclasses.
		 *
		 * @since 3.5.0
		 *
		 * @param {string}  $target    Selector where to put the elements.
		 * @param {*}       els        HTML or elements to put at the end of the
		 *                             $target.
		 * @param {?Object} options    Options for call.
		 * @param {?number} options.at At which index to put the elements.
		 *
		 * @return {wp.Backbone.Subviews} The current Subviews instance.
		 */
		insert: function( $target, els, options ) {
			var at = options && options.at,
				$children;

			if ( _.isNumber( at ) && ($children = $target.children()).length > at )
				$children.eq( at ).before( els );
			else
				$target.append( els );

			return this;
		},

		/**
		 * Triggers the ready event.
		 *
		 * Only use this method if you know what you're doing. For performance reasons,
		 * this method does not check if the view is actually attached to the DOM. It's
		 * taking your word for it.
		 *
		 * Fires the ready event on the current view and all attached subviews.
		 *
		 * @since 3.5.0
		 */
		ready: function() {
			this.view.trigger('ready');

			// Find all attached subviews, and call ready on them.
			_.chain( this.all() ).map( function( view ) {
				return view.views;
			}).flatten().where({ attached: true }).invoke('ready');
		},
		/**
		 * Attaches a series of views to a selector. Internal.
		 *
		 * Checks to see if a matching selector exists, renders the views,
		 * performs the proper DOM operation, and then checks if the view is
		 * attached to the document.
		 *
		 * @since 3.5.0
		 *
		 * @private
		 *
		 * @param {string}       selector    A jQuery selector.
		 * @param {Array|Object} views       The subviews for the main view.
		 * @param {Object}       options     Options for call.
		 * @param {boolean}      options.add If true the provided views will be added.
		 *
		 * @return {wp.Backbone.Subviews} The current Subviews instance.
		 */
		_attach: function( selector, views, options ) {
			var $selector = selector ? this.view.$( selector ) : this.view.$el,
				managers;

			// Check if we found a location to attach the views.
			if ( ! $selector.length )
				return this;

			managers = _.chain( views ).pluck('views').flatten().value();

			// Render the views if necessary.
			_.each( managers, function( manager ) {
				if ( manager.rendered )
					return;

				manager.view.render();
				manager.rendered = true;
			}, this );

			// Insert or replace the views.
			this[ options.add ? 'insert' : 'replace' ]( $selector, _.pluck( views, 'el' ), options );

			/*
			 * Set attached and trigger ready if the current view is already
			 * attached to the DOM.
			 */
			_.each( managers, function( manager ) {
				manager.attached = true;

				if ( options.ready )
					manager.ready();
			}, this );

			return this;
		},

		/**
		 * Determines whether or not the current view is in the DOM.
		 *
		 * @since 3.5.0
		 *
		 * @private
		 *
		 * @return {boolean} Whether or not the current view is in the DOM.
		 */
		_isReady: function() {
			var node = this.view.el;
			while ( node ) {
				if ( node === document.body )
					return true;
				node = node.parentNode;
			}

			return false;
		}
	});

	wp.Backbone.View = Backbone.View.extend({

		// The constructor for the `Views` manager.
		Subviews: wp.Backbone.Subviews,

		/**
		 * The base view class.
		 *
		 * This extends the backbone view to have a build-in way to use subviews. This
		 * makes it easier to have nested views.
		 *
		 * @since 3.5.0
		 * @since 3.6.0 Moved wp.media.View to wp.Backbone.View
		 *
		 * @constructs
		 * @augments Backbone.View
		 *
		 * @memberOf wp.Backbone
		 *
		 *
		 * @param {Object} options The options for this view.
		 */
		constructor: function( options ) {
			this.views = new this.Subviews( this, this.views );
			this.on( 'ready', this.ready, this );

			this.options = options || {};

			Backbone.View.apply( this, arguments );
		},

		/**
		 * Removes this view and all subviews.
		 *
		 * @since 3.5.0
		 *
		 * @return {wp.Backbone.Subviews} The current Subviews instance.
		 */
		remove: function() {
			var result = Backbone.View.prototype.remove.apply( this, arguments );

			// Recursively remove child views.
			if ( this.views )
				this.views.remove();

			return result;
		},

		/**
		 * Renders this view and all subviews.
		 *
		 * @since 3.5.0
		 *
		 * @return {wp.Backbone.View} The current instance of the view.
		 */
		render: function() {
			var options;

			if ( this.prepare )
				options = this.prepare();

			this.views.detach();

			if ( this.template ) {
				options = options || {};
				this.trigger( 'prepare', options );
				this.$el.html( this.template( options ) );
			}

			this.views.render();
			return this;
		},

		/**
		 * Returns the options for this view.
		 *
		 * @since 3.5.0
		 *
		 * @return {Object} The options for this view.
		 */
		prepare: function() {
			return this.options;
		},

		/**
		 * Method that is called when the ready event is triggered.
		 *
		 * @since 3.5.0
		 */
		ready: function() {}
	});
}(jQuery));
