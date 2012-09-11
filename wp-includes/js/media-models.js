if ( typeof wp === 'undefined' )
	var wp = {};

(function($){
	var Attachment, Attachments, Query;

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

			this.filters = options.filters || {};

			if ( options.observe )
				this.observe( options.observe );

			if ( options.mirror )
				this.mirror( options.mirror );
		},

		validator: function( attachment ) {
			return _.all( this.filters, function( filter ) {
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
		},

		parse: function( resp, xhr ) {
			return _.map( resp, function( attrs ) {
				var attachment = Attachment.get( attrs.id );
				return attachment.set( attachment.parse( attrs, xhr ) );
			});
		}
	});

	Attachments.all = new Attachments();

	/**
	 * wp.media.query
	 */
	media.query = (function(){
		var queries = [];

		return function( args, options ) {
			args = _.defaults( args || {}, Query.defaultArgs );

			var query = _.find( queries, function( query ) {
				return _.isEqual( query.args, args );
			});

			if ( ! query ) {
				query = new Query( [], _.extend( options || {}, { args: args } ) );
				queries.push( query );
			}

			return query;
		};
	}());

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
			var orderby,
				defaultArgs = Query.defaultArgs;

			options = options || {};
			Attachments.prototype.initialize.apply( this, arguments );

			// Generate this.args. Don't mess with them.
			this.args = _.defaults( options.args || {}, defaultArgs );

			// Normalize the order.
			this.args.order = this.args.order.toUpperCase();
			if ( 'DESC' !== this.args.order && 'ASC' !== this.args.order )
				this.args.order = defaultArgs.order.toUpperCase();

			// Set allowed orderby values.
			// These map directly to attachment keys in most scenarios.
			// Exceptions are specified in orderby.keymap.
			orderby = {
				allowed: [ 'name', 'author', 'date', 'title', 'modified', 'parent', 'ID' ],
				keymap:  {
					'ID':     'id',
					'parent': 'uploadedTo'
				}
			};

			if ( ! _.contains( orderby.allowed, this.args.orderby ) )
				this.args.orderby = defaultArgs.orderby;
			this.orderkey = orderby.keymap[ this.args.orderby ] || this.args.orderby;

			this.hasMore = true;
			this.created = new Date();

			this.filters.order = function( attachment ) {
				// We want any items that can be placed before the last
				// item in the set. If we add any items after the last
				// item, then we can't guarantee the set is complete.
				if ( this.length ) {
					return 1 !== this.comparator( attachment, this.last() );

				// Handle the case where there are no items yet and
				// we're sorting for recent items. In that case, we want
				// changes that occurred after we created the query.
				} else if ( 'DESC' === this.args.order && ( 'date' === this.orderkey || 'modified' === this.orderkey ) ) {
					return attachment.get( this.orderkey ) >= this.created;
				}

				// Otherwise, we don't want any items yet.
				return false;
			};

			if ( this.args.s ) {
				// Note that this client-side searching is *not* equivalent
				// to our server-side searching.
				this.filters.search = function( attachment ) {
					return _.any(['title','filename','description','caption','name'], function( key ) {
						var value = attachment.get( key );
						return value && -1 !== value.search( this.args.s );
					}, this );
				};
			}

			this.observe( Attachments.all );
		},

		more: function( options ) {
			var query = this;

			if ( ! this.hasMore )
				return;

			options = options || {};
			options.add = true;

			return this.fetch( options ).done( function( resp ) {
				if ( _.isEmpty( resp ) || resp.length < this.args.posts_per_page )
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
				args.paged = Math.floor( this.length / args.posts_per_page ) + 1;

				options.data.query = args;
				return media.ajax( options );

			// Otherwise, fall back to Backbone.sync()
			} else {
				fallback = Attachments.prototype.sync ? Attachments.prototype : Backbone;
				return fallback.sync.apply( this, arguments );
			}
		},

		comparator: (function(){
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
			var compare = function( a, b, ac, bc ) {
				if ( _.isEqual( a, b ) )
					return ac === bc ? 0 : (ac > bc ? -1 : 1);
				else
					return a > b ? -1 : 1;
			};

			return function( a, b ) {
				var key   = this.orderkey,
					order = this.args.order,
					ac    = a.cid,
					bc    = b.cid;

				a = a.get( key );
				b = b.get( key );

				if ( 'date' === key || 'modified' === key ) {
					a = a || new Date();
					b = b || new Date();
				}

				return ( 'DESC' === order ) ? compare( a, b, ac, bc ) : compare( b, a, bc, ac );
			};
		}())
	}, {
		defaultArgs: {
			posts_per_page: 40,
			orderby:       'date',
			order:         'DESC'
		}
	});

}(jQuery));