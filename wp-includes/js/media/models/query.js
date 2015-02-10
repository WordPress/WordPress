/**
 * wp.media.model.Query
 *
 * A collection of attachments that match the supplied query arguments.
 *
 * Note: Do NOT change this.args after the query has been initialized.
 *       Things will break.
 *
 * @class
 * @augments wp.media.model.Attachments
 * @augments Backbone.Collection
 *
 * @param {array}  [models]                      Models to initialize with the collection.
 * @param {object} [options]                     Options hash.
 * @param {object} [options.args]                Attachments query arguments.
 * @param {object} [options.args.posts_per_page]
 */
var Attachments = require( './attachments.js' ),
	Query;

Query = Attachments.extend({
	/**
	 * @global wp.Uploader
	 *
	 * @param {array}  [models=[]]  Array of initial models to populate the collection.
	 * @param {object} [options={}]
	 */
	initialize: function( models, options ) {
		var allowed;

		options = options || {};
		Attachments.prototype.initialize.apply( this, arguments );

		this.args     = options.args;
		this._hasMore = true;
		this.created  = new Date();

		this.filters.order = function( attachment ) {
			var orderby = this.props.get('orderby'),
				order = this.props.get('order');

			if ( ! this.comparator ) {
				return true;
			}

			// We want any items that can be placed before the last
			// item in the set. If we add any items after the last
			// item, then we can't guarantee the set is complete.
			if ( this.length ) {
				return 1 !== this.comparator( attachment, this.last(), { ties: true });

			// Handle the case where there are no items yet and
			// we're sorting for recent items. In that case, we want
			// changes that occurred after we created the query.
			} else if ( 'DESC' === order && ( 'date' === orderby || 'modified' === orderby ) ) {
				return attachment.get( orderby ) >= this.created;

			// If we're sorting by menu order and we have no items,
			// accept any items that have the default menu order (0).
			} else if ( 'ASC' === order && 'menuOrder' === orderby ) {
				return attachment.get( orderby ) === 0;
			}

			// Otherwise, we don't want any items yet.
			return false;
		};

		// Observe the central `wp.Uploader.queue` collection to watch for
		// new matches for the query.
		//
		// Only observe when a limited number of query args are set. There
		// are no filters for other properties, so observing will result in
		// false positives in those queries.
		allowed = [ 's', 'order', 'orderby', 'posts_per_page', 'post_mime_type', 'post_parent' ];
		if ( wp.Uploader && _( this.args ).chain().keys().difference( allowed ).isEmpty().value() ) {
			this.observe( wp.Uploader.queue );
		}
	},
	/**
	 * Whether there are more attachments that haven't been sync'd from the server
	 * that match the collection's query.
	 *
	 * @returns {boolean}
	 */
	hasMore: function() {
		return this._hasMore;
	},
	/**
	 * Fetch more attachments from the server for the collection.
	 *
	 * @param   {object}  [options={}]
	 * @returns {Promise}
	 */
	more: function( options ) {
		var query = this;

		// If there is already a request pending, return early with the Deferred object.
		if ( this._more && 'pending' === this._more.state() ) {
			return this._more;
		}

		if ( ! this.hasMore() ) {
			return jQuery.Deferred().resolveWith( this ).promise();
		}

		options = options || {};
		options.remove = false;

		return this._more = this.fetch( options ).done( function( resp ) {
			if ( _.isEmpty( resp ) || -1 === this.args.posts_per_page || resp.length < this.args.posts_per_page ) {
				query._hasMore = false;
			}
		});
	},
	/**
	 * Overrides Backbone.Collection.sync
	 * Overrides wp.media.model.Attachments.sync
	 *
	 * @param {String} method
	 * @param {Backbone.Model} model
	 * @param {Object} [options={}]
	 * @returns {Promise}
	 */
	sync: function( method, model, options ) {
		var args, fallback;

		// Overload the read method so Attachment.fetch() functions correctly.
		if ( 'read' === method ) {
			options = options || {};
			options.context = this;
			options.data = _.extend( options.data || {}, {
				action:  'query-attachments',
				post_id: wp.media.model.settings.post.id
			});

			// Clone the args so manipulation is non-destructive.
			args = _.clone( this.args );

			// Determine which page to query.
			if ( -1 !== args.posts_per_page ) {
				args.paged = Math.round( this.length / args.posts_per_page ) + 1;
			}

			options.data.query = args;
			return wp.media.ajax( options );

		// Otherwise, fall back to Backbone.sync()
		} else {
			/**
			 * Call wp.media.model.Attachments.sync or Backbone.sync
			 */
			fallback = Attachments.prototype.sync ? Attachments.prototype : Backbone;
			return fallback.sync.apply( this, arguments );
		}
	}
}, {
	/**
	 * @readonly
	 */
	defaultProps: {
		orderby: 'date',
		order:   'DESC'
	},
	/**
	 * @readonly
	 */
	defaultArgs: {
		posts_per_page: 40
	},
	/**
	 * @readonly
	 */
	orderby: {
		allowed:  [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo', 'id', 'post__in', 'menuOrder' ],
		/**
		 * A map of JavaScript orderby values to their WP_Query equivalents.
		 * @type {Object}
		 */
		valuemap: {
			'id':         'ID',
			'uploadedTo': 'parent',
			'menuOrder':  'menu_order ID'
		}
	},
	/**
	 * A map of JavaScript query properties to their WP_Query equivalents.
	 *
	 * @readonly
	 */
	propmap: {
		'search':    's',
		'type':      'post_mime_type',
		'perPage':   'posts_per_page',
		'menuOrder': 'menu_order',
		'uploadedTo': 'post_parent',
		'status':     'post_status',
		'include':    'post__in',
		'exclude':    'post__not_in'
	},
	/**
	 * Creates and returns an Attachments Query collection given the properties.
	 *
	 * Caches query objects and reuses where possible.
	 *
	 * @static
	 * @method
	 *
	 * @param {object} [props]
	 * @param {Object} [props.cache=true]   Whether to use the query cache or not.
	 * @param {Object} [props.order]
	 * @param {Object} [props.orderby]
	 * @param {Object} [props.include]
	 * @param {Object} [props.exclude]
	 * @param {Object} [props.s]
	 * @param {Object} [props.post_mime_type]
	 * @param {Object} [props.posts_per_page]
	 * @param {Object} [props.menu_order]
	 * @param {Object} [props.post_parent]
	 * @param {Object} [props.post_status]
	 * @param {Object} [options]
	 *
	 * @returns {wp.media.model.Query} A new Attachments Query collection.
	 */
	get: (function(){
		/**
		 * @static
		 * @type Array
		 */
		var queries = [];

		/**
		 * @returns {Query}
		 */
		return function( props, options ) {
			var args     = {},
				orderby  = Query.orderby,
				defaults = Query.defaultProps,
				query,
				cache    = !! props.cache || _.isUndefined( props.cache );

			// Remove the `query` property. This isn't linked to a query,
			// this *is* the query.
			delete props.query;
			delete props.cache;

			// Fill default args.
			_.defaults( props, defaults );

			// Normalize the order.
			props.order = props.order.toUpperCase();
			if ( 'DESC' !== props.order && 'ASC' !== props.order ) {
				props.order = defaults.order.toUpperCase();
			}

			// Ensure we have a valid orderby value.
			if ( ! _.contains( orderby.allowed, props.orderby ) ) {
				props.orderby = defaults.orderby;
			}

			_.each( [ 'include', 'exclude' ], function( prop ) {
				if ( props[ prop ] && ! _.isArray( props[ prop ] ) ) {
					props[ prop ] = [ props[ prop ] ];
				}
			} );

			// Generate the query `args` object.
			// Correct any differing property names.
			_.each( props, function( value, prop ) {
				if ( _.isNull( value ) ) {
					return;
				}

				args[ Query.propmap[ prop ] || prop ] = value;
			});

			// Fill any other default query args.
			_.defaults( args, Query.defaultArgs );

			// `props.orderby` does not always map directly to `args.orderby`.
			// Substitute exceptions specified in orderby.keymap.
			args.orderby = orderby.valuemap[ props.orderby ] || props.orderby;

			// Search the query cache for a matching query.
			if ( cache ) {
				query = _.find( queries, function( query ) {
					return _.isEqual( query.args, args );
				});
			} else {
				queries = [];
			}

			// Otherwise, create a new query and add it to the cache.
			if ( ! query ) {
				query = new Query( [], _.extend( options || {}, {
					props: props,
					args:  args
				} ) );
				queries.push( query );
			}

			return query;
		};
	}())
});

module.exports = Query;
