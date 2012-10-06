window.wp = window.wp || {};

(function($){
	var Attachment, Attachments, Query, compare;

	/**
	 * wp.media( attributes )
	 *
	 * Handles the default media experience. Automatically creates
	 * and opens a media workflow, and returns the result.
	 * Does nothing if the controllers do not exist.
	 *
	 * @param  {object} attributes The properties passed to the main media controller.
	 * @return {object}            A media workflow.
	 */
	media = wp.media = function( attributes ) {
		if ( media.controller.Workflow )
			return new media.controller.Workflow( attributes ).attach().render();
	};

	_.extend( media, { model: {}, view: {}, controller: {} });

	/**
	 * ========================================================================
	 * UTILITIES
	 * ========================================================================
	 */

	/**
	 * A basic comparator.
	 *
	 * @param  {mixed}  a  The primary parameter to compare.
	 * @param  {mixed}  b  The primary parameter to compare.
	 * @param  {string} ac The fallback parameter to compare, a's cid.
	 * @param  {string} bc The fallback parameter to compare, b's cid.
	 * @return {number}    -1: a should come before b.
	 *                      0: a and b are of the same rank.
	 *                      1: b should come before a.
	 */
	compare = function( a, b, ac, bc ) {
		if ( _.isEqual( a, b ) )
			return ac === bc ? 0 : (ac > bc ? -1 : 1);
		else
			return a > b ? -1 : 1;
	};

	_.extend( media, {
		/**
		 * media.template( id )
		 *
		 * Fetches a template by id.
		 *
		 * @param  {string} id   A string that corresponds to a DOM element with an id prefixed with "tmpl-".
		 *                       For example, "attachment" maps to "tmpl-attachment".
		 * @return {function}    A function that lazily-compiles the template requested.
		 */
		template: _.memoize( function( id ) {
			var compiled;
			return function( data ) {
				compiled = compiled || _.template( $( '#tmpl-' + id ).html() );
				return compiled( data );
			};
		}),

		/**
		 * media.post( [action], [data] )
		 *
		 * Sends a POST request to WordPress.
		 *
		 * @param  {string} action The slug of the action to fire in WordPress.
		 * @param  {object} data   The data to populate $_POST with.
		 * @return {$.promise}     A jQuery promise that represents the request.
		 */
		post: function( action, data ) {
			return media.ajax({
				data: _.isObject( action ) ? action : _.extend( data || {}, { action: action })
			});
		},

		/**
		 * media.ajax( [action], [options] )
		 *
		 * Sends a POST request to WordPress.
		 *
		 * @param  {string} action  The slug of the action to fire in WordPress.
		 * @param  {object} options The options passed to jQuery.ajax.
		 * @return {$.promise}      A jQuery promise that represents the request.
		 */
		ajax: function( action, options ) {
			if ( _.isObject( action ) ) {
				options = action;
			} else {
				options = options || {};
				options.data = _.extend( options.data || {}, { action: action });
			}

			options = _.defaults( options || {}, {
				type:    'POST',
				url:     ajaxurl,
				context: this
			});

			return $.Deferred( function( deferred ) {
				// Transfer success/error callbacks.
				if ( options.success )
					deferred.done( options.success );
				if ( options.error )
					deferred.fail( options.error );

				delete options.success;
				delete options.error;

				// Use with PHP's wp_send_json_success() and wp_send_json_error()
				$.ajax( options ).done( function( response ) {
					if ( _.isObject( response ) && ! _.isUndefined( response.success ) )
						deferred[ response.success ? 'resolveWith' : 'rejectWith' ]( this, [response.data] );
					else
						deferred.rejectWith( this, [response] );
				}).fail( function() {
					deferred.rejectWith( this, arguments );
				});
			}).promise();
		},

		// Scales a set of dimensions to fit within bounding dimensions.
		fit: function( dimensions ) {
			var width     = dimensions.width,
				height    = dimensions.height,
				maxWidth  = dimensions.maxWidth,
				maxHeight = dimensions.maxHeight,
				constraint;

			// Compare ratios between the two values to determine which
			// max to constrain by. If a max value doesn't exist, then the
			// opposite side is the constraint.
			if ( ! _.isUndefined( maxWidth ) && ! _.isUndefined( maxHeight ) ) {
				constraint = ( width / height > maxWidth / maxHeight ) ? 'width' : 'height';
			} else if ( _.isUndefined( maxHeight ) ) {
				constraint = 'width';
			} else if (  _.isUndefined( maxWidth ) && height > maxHeight ) {
				constraint = 'height';
			}

			// If the value of the constrained side is larger than the max,
			// then scale the values. Otherwise return the originals; they fit.
			if ( 'width' === constraint && width > maxWidth ) {
				return {
					width : maxWidth,
					height: maxWidth * height / width
				};
			} else if ( 'height' === constraint && height > maxHeight ) {
				return {
					width : maxHeight * width / height,
					height: maxHeight
				};
			} else {
				return {
					width : width,
					height: height
				};
			}
		}
	});


	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */

	/**
	 * wp.media.model.Attachment
	 */
	Attachment = media.model.Attachment = Backbone.Model.extend({
		sync: function( method, model, options ) {
			// Overload the read method so Attachment.fetch() functions correctly.
			if ( 'read' === method ) {
				options = options || {};
				options.context = this;
				options.data = _.extend( options.data || {}, {
					action: 'get-attachment',
					id: this.id
				});
				return media.ajax( options );

			// Otherwise, fall back to Backbone.sync()
			} else {
				return Backbone.sync.apply( this, arguments );
			}
		},

		parse: function( resp, xhr ) {
			// Convert date strings into Date objects.
			resp.date = new Date( resp.date );
			resp.modified = new Date( resp.modified );
			return resp;
		}
	}, {
		create: function( attrs ) {
			return Attachments.all.push( attrs );
		},

		get: _.memoize( function( id, attachment ) {
			return Attachments.all.push( attachment || { id: id } );
		})
	});

	/**
	 * wp.media.model.Attachments
	 */
	Attachments = media.model.Attachments = Backbone.Collection.extend({
		model: Attachment,

		initialize: function( models, options ) {
			options = options || {};

			this.props   = new Backbone.Model();
			this.filters = options.filters || {};

			// Bind default `change` events to the `props` model.
			this.props.on( 'change:order',   this._changeOrder,   this );
			this.props.on( 'change:orderby', this._changeOrderby, this );
			this.props.on( 'change:query',   this._changeQuery,   this );
			this.props.on( 'change:search',  this._changeSearch,  this );
			this.props.on( 'change:type',    this._changeType,    this );

			// Set the `props` model and fill the default property values.
			this.props.set( _.defaults( options.props || {} ) );

			// Observe another `Attachments` collection if one is provided.
			if ( options.observe )
				this.observe( options.observe );
		},

		// Automatically sort the collection when the order changes.
		_changeOrder: function( model, order ) {
			if ( this.comparator )
				this.sort();
		},

		// Set the default comparator only when the `orderby` property is set.
		_changeOrderby: function( model, orderby ) {
			// If a different comparator is defined, bail.
			if ( this.comparator && this.comparator !== Attachments.comparator )
				return;

			if ( orderby && 'post__in' !== orderby )
				this.comparator = Attachments.comparator;
			else
				delete this.comparator;
		},

		// If the `query` property is set to true, query the server using
		// the `props` values, and sync the results to this collection.
		_changeQuery: function( model, query ) {
			if ( query ) {
				this.props.on( 'change', this._requery, this );
				this._requery();
			} else {
				this.props.off( 'change', this._requery, this );
			}
		},

		_changeFilteredProp: function( prop, model, term ) {
			// Bail if we're currently searching for the same term.
			if ( this.props.get( prop ) === term )
				return;

			if ( term && ! this.filters[ prop ] )
				this.filters[ prop ] = Attachments.filters[ prop ];
			else if ( ! term && this.filters[ prop ] === Attachments.filters[ prop ] )
				delete this.filters[ prop ];

			// If no `Attachments` model is provided to source the searches
			// from, then automatically generate a source from the existing
			// models.
			if ( ! this.props.get('source') )
				this.props.set( 'source', new Attachments( this.models ) );

			this.reset( this.props.get('source').filter( this.validator ) );
		},

		_changeSearch: function( model, term ) {
			return this._changeFilteredProp( 'search', model, term );
		},

		_changeType: function( model, term ) {
			return this._changeFilteredProp( 'type', model, term );
		},

		validator: function( attachment ) {
			return _.all( this.filters, function( filter, key ) {
				return !! filter.call( this, attachment );
			}, this );
		},

		validate: function( attachment, options ) {
			return this[ this.validator( attachment ) ? 'add' : 'remove' ]( attachment, options );
		},

		observe: function( attachments ) {
			attachments.on( 'add change', this.validate, this );
		},

		unobserve: function( attachments ) {
			attachments.off( 'add change', this.validate, this );
		},

		mirror: function( attachments ) {
			if ( this.mirroring && this.mirroring === attachments )
				return;

			this.unmirror();
			this.mirroring = attachments;
			this.reset( attachments.models );
			attachments.on( 'add',    this._mirrorAdd,    this );
			attachments.on( 'remove', this._mirrorRemove, this );
			attachments.on( 'reset',  this._mirrorReset,  this );
		},

		unmirror: function() {
			if ( ! this.mirroring )
				return;

			this.mirroring.off( 'add',    this._mirrorAdd,    this );
			this.mirroring.off( 'remove', this._mirrorRemove, this );
			this.mirroring.off( 'reset',  this._mirrorReset,  this );
			delete this.mirroring;
		},

		_mirrorAdd: function( attachment, attachments, options ) {
			this.add( attachment, { at: options.index });
		},

		_mirrorRemove: function( attachment ) {
			this.remove( attachment );
		},

		_mirrorReset: function( attachments ) {
			this.reset( attachments.models );
		},

		more: function( options ) {
			if ( this.mirroring && this.mirroring.more )
				return this.mirroring.more( options );
			return $.Deferred().resolve().promise();
		},

		parse: function( resp, xhr ) {
			return _.map( resp, function( attrs ) {
				var attachment = Attachment.get( attrs.id );
				return attachment.set( attachment.parse( attrs, xhr ) );
			});
		},

		_requery: function() {
			if ( this.props.get('query') )
				this.mirror( Query.get( this.props.toJSON() ) );
		}
	}, {
		comparator: function( a, b ) {
			var key   = this.props.get('orderby'),
				order = this.props.get('order') || 'DESC',
				ac    = a.cid,
				bc    = b.cid;

			a = a.get( key );
			b = b.get( key );

			if ( 'date' === key || 'modified' === key ) {
				a = a || new Date();
				b = b || new Date();
			}

			return ( 'DESC' === order ) ? compare( a, b, ac, bc ) : compare( b, a, bc, ac );
		},

		filters: {
			// Note that this client-side searching is *not* equivalent
			// to our server-side searching.
			search: function( attachment ) {
				if ( ! this.props.get('search') )
					return true;

				return _.any(['title','filename','description','caption','name'], function( key ) {
					var value = attachment.get( key );
					return value && -1 !== value.search( this.props.get('search') );
				}, this );
			},

			type: function( attachment ) {
				var type = this.props.get('type');
				if ( ! type )
					return true;

				return -1 !== type.indexOf( attachment.get('type') );
			}
		}
	});

	Attachments.all = new Attachments();

	/**
	 * wp.media.query
	 */
	media.query = function( props ) {
		return new Attachments( null, {
			props: _.extend( _.defaults( props || {}, { orderby: 'date' } ), { query: true } )
		});
	};

	/**
	 * wp.media.model.Query
	 *
	 * A set of attachments that corresponds to a set of consecutively paged
	 * queries on the server.
	 *
	 * Note: Do NOT change this.args after the query has been initialized.
	 *       Things will break.
	 */
	Query = media.model.Query = Attachments.extend({
		initialize: function( models, options ) {
			var allowed;

			options = options || {};
			Attachments.prototype.initialize.apply( this, arguments );

			this.args    = options.args;
			this.hasMore = true;
			this.created = new Date();

			this.filters.order = function( attachment ) {
				if ( ! this.comparator )
					return true;

				// We want any items that can be placed before the last
				// item in the set. If we add any items after the last
				// item, then we can't guarantee the set is complete.
				if ( this.length ) {
					return 1 !== this.comparator( attachment, this.last() );

				// Handle the case where there are no items yet and
				// we're sorting for recent items. In that case, we want
				// changes that occurred after we created the query.
				} else if ( 'DESC' === this.args.order && ( 'date' === this.args.orderby || 'modified' === this.args.orderby ) ) {
					return attachment.get( this.args.orderby ) >= this.created;
				}

				// Otherwise, we don't want any items yet.
				return false;
			};

			// Observe the central `Attachments.all` model to watch for new
			// matches for the query.
			//
			// Only observe when a limited number of query args are set. There
			// are no filters for other properties, so observing will result in
			// false positives in those queries.
			allowed = [ 's', 'order', 'orderby', 'posts_per_page', 'post_mime_type' ];
			if ( _( this.args ).chain().keys().difference().isEmpty().value() )
				this.observe( Attachments.all );
		},

		more: function( options ) {
			var query = this;

			if ( ! this.hasMore )
				return $.Deferred().resolve().promise();

			options = options || {};
			options.add = true;

			return this.fetch( options ).done( function( resp ) {
				if ( _.isEmpty( resp ) || -1 === this.args.posts_per_page || resp.length < this.args.posts_per_page )
					query.hasMore = false;
			});
		},

		sync: function( method, model, options ) {
			var fallback;

			// Overload the read method so Attachment.fetch() functions correctly.
			if ( 'read' === method ) {
				options = options || {};
				options.context = this;
				options.data = _.extend( options.data || {}, {
					action: 'query-attachments'
				});

				// Clone the args so manipulation is non-destructive.
				args = _.clone( this.args );

				// Determine which page to query.
				if ( -1 !== args.posts_per_page )
					args.paged = Math.floor( this.length / args.posts_per_page ) + 1;

				options.data.query = args;
				return media.ajax( options );

			// Otherwise, fall back to Backbone.sync()
			} else {
				fallback = Attachments.prototype.sync ? Attachments.prototype : Backbone;
				return fallback.sync.apply( this, arguments );
			}
		}
	}, {
		defaultProps: {
			orderby: 'date',
			order:   'DESC'
		},

		defaultArgs: {
			posts_per_page: 40
		},

		orderby: {
			allowed:  [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo', 'id', 'post__in' ],
			valuemap: {
				'id':         'ID',
				'uploadedTo': 'parent'
			}
		},

		propmap: {
			'search':  's',
			'type':    'post_mime_type',
			'parent':  'post_parent',
			'perPage': 'posts_per_page'
		},

		// Caches query objects so queries can be easily reused.
		get: (function(){
			var queries = [];

			return function( props, options ) {
				var args     = {},
					orderby  = Query.orderby,
					defaults = Query.defaultProps,
					query;

				// Remove the `query` property. This isn't linked to a query,
				// this *is* the query.
				delete props.query;

				// Fill default args.
				_.defaults( props, defaults );

				// Normalize the order.
				props.order = props.order.toUpperCase();
				if ( 'DESC' !== props.order && 'ASC' !== props.order )
					props.order = defaults.order.toUpperCase();

				// Ensure we have a valid orderby value.
				if ( ! _.contains( orderby.allowed, props.orderby ) )
					props.orderby = defaults.orderby;

				// Generate the query `args` object.
				// Correct any differing property names.
				_.each( props, function( value, prop ) {
					args[ Query.propmap[ prop ] || prop ] = value;
				});

				// Fill any other default query args.
				_.defaults( args, Query.defaultArgs );

				// `props.orderby` does not always map directly to `args.orderby`.
				// Substitute exceptions specified in orderby.keymap.
				args.orderby = orderby.valuemap[ props.orderby ] || props.orderby;

				// Search the query cache for matches.
				query = _.find( queries, function( query ) {
					return _.isEqual( query.args, args );
				});

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

}(jQuery));