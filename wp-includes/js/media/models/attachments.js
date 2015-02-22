/*globals wp, _, Backbone */

/**
 * wp.media.model.Attachments
 *
 * A collection of attachments.
 *
 * This collection has no persistence with the server without supplying
 * 'options.props.query = true', which will mirror the collection
 * to an Attachments Query collection - @see wp.media.model.Attachments.mirror().
 *
 * @class
 * @augments Backbone.Collection
 *
 * @param {array}  [models]                Models to initialize with the collection.
 * @param {object} [options]               Options hash for the collection.
 * @param {string} [options.props]         Options hash for the initial query properties.
 * @param {string} [options.props.order]   Initial order (ASC or DESC) for the collection.
 * @param {string} [options.props.orderby] Initial attribute key to order the collection by.
 * @param {string} [options.props.query]   Whether the collection is linked to an attachments query.
 * @param {string} [options.observe]
 * @param {string} [options.filters]
 *
 */
var Attachment = require( './attachment.js' ),
	Attachments;

Attachments = Backbone.Collection.extend({
	/**
	 * @type {wp.media.model.Attachment}
	 */
	model: Attachment,
	/**
	 * @param {Array} [models=[]] Array of models used to populate the collection.
	 * @param {Object} [options={}]
	 */
	initialize: function( models, options ) {
		options = options || {};

		this.props   = new Backbone.Model();
		this.filters = options.filters || {};

		// Bind default `change` events to the `props` model.
		this.props.on( 'change', this._changeFilteredProps, this );

		this.props.on( 'change:order',   this._changeOrder,   this );
		this.props.on( 'change:orderby', this._changeOrderby, this );
		this.props.on( 'change:query',   this._changeQuery,   this );

		this.props.set( _.defaults( options.props || {} ) );

		if ( options.observe ) {
			this.observe( options.observe );
		}
	},
	/**
	 * Sort the collection when the order attribute changes.
	 *
	 * @access private
	 */
	_changeOrder: function() {
		if ( this.comparator ) {
			this.sort();
		}
	},
	/**
	 * Set the default comparator only when the `orderby` property is set.
	 *
	 * @access private
	 *
	 * @param {Backbone.Model} model
	 * @param {string} orderby
	 */
	_changeOrderby: function( model, orderby ) {
		// If a different comparator is defined, bail.
		if ( this.comparator && this.comparator !== Attachments.comparator ) {
			return;
		}

		if ( orderby && 'post__in' !== orderby ) {
			this.comparator = Attachments.comparator;
		} else {
			delete this.comparator;
		}
	},
	/**
	 * If the `query` property is set to true, query the server using
	 * the `props` values, and sync the results to this collection.
	 *
	 * @access private
	 *
	 * @param {Backbone.Model} model
	 * @param {Boolean} query
	 */
	_changeQuery: function( model, query ) {
		if ( query ) {
			this.props.on( 'change', this._requery, this );
			this._requery();
		} else {
			this.props.off( 'change', this._requery, this );
		}
	},
	/**
	 * @access private
	 *
	 * @param {Backbone.Model} model
	 */
	_changeFilteredProps: function( model ) {
		// If this is a query, updating the collection will be handled by
		// `this._requery()`.
		if ( this.props.get('query') ) {
			return;
		}

		var changed = _.chain( model.changed ).map( function( t, prop ) {
			var filter = Attachments.filters[ prop ],
				term = model.get( prop );

			if ( ! filter ) {
				return;
			}

			if ( term && ! this.filters[ prop ] ) {
				this.filters[ prop ] = filter;
			} else if ( ! term && this.filters[ prop ] === filter ) {
				delete this.filters[ prop ];
			} else {
				return;
			}

			// Record the change.
			return true;
		}, this ).any().value();

		if ( ! changed ) {
			return;
		}

		// If no `Attachments` model is provided to source the searches
		// from, then automatically generate a source from the existing
		// models.
		if ( ! this._source ) {
			this._source = new Attachments( this.models );
		}

		this.reset( this._source.filter( this.validator, this ) );
	},

	validateDestroyed: false,
	/**
	 * Checks whether an attachment is valid.
	 *
	 * @param {wp.media.model.Attachment} attachment
	 * @returns {Boolean}
	 */
	validator: function( attachment ) {
		if ( ! this.validateDestroyed && attachment.destroyed ) {
			return false;
		}
		return _.all( this.filters, function( filter ) {
			return !! filter.call( this, attachment );
		}, this );
	},
	/**
	 * Add or remove an attachment to the collection depending on its validity.
	 *
	 * @param {wp.media.model.Attachment} attachment
	 * @param {Object} options
	 * @returns {wp.media.model.Attachments} Returns itself to allow chaining
	 */
	validate: function( attachment, options ) {
		var valid = this.validator( attachment ),
			hasAttachment = !! this.get( attachment.cid );

		if ( ! valid && hasAttachment ) {
			this.remove( attachment, options );
		} else if ( valid && ! hasAttachment ) {
			this.add( attachment, options );
		}

		return this;
	},

	/**
	 * Add or remove all attachments from another collection depending on each one's validity.
	 *
	 * @param {wp.media.model.Attachments} attachments
	 * @param {object} [options={}]
	 *
	 * @fires wp.media.model.Attachments#reset
	 *
	 * @returns {wp.media.model.Attachments} Returns itself to allow chaining
	 */
	validateAll: function( attachments, options ) {
		options = options || {};

		_.each( attachments.models, function( attachment ) {
			this.validate( attachment, { silent: true });
		}, this );

		if ( ! options.silent ) {
			this.trigger( 'reset', this, options );
		}
		return this;
	},
	/**
	 * Start observing another attachments collection change events
	 * and replicate them on this collection.
	 *
	 * @param {wp.media.model.Attachments} The attachments collection to observe.
	 * @returns {wp.media.model.Attachments} Returns itself to allow chaining.
	 */
	observe: function( attachments ) {
		this.observers = this.observers || [];
		this.observers.push( attachments );

		attachments.on( 'add change remove', this._validateHandler, this );
		attachments.on( 'reset', this._validateAllHandler, this );
		this.validateAll( attachments );
		return this;
	},
	/**
	 * Stop replicating collection change events from another attachments collection.
	 *
	 * @param {wp.media.model.Attachments} The attachments collection to stop observing.
	 * @returns {wp.media.model.Attachments} Returns itself to allow chaining
	 */
	unobserve: function( attachments ) {
		if ( attachments ) {
			attachments.off( null, null, this );
			this.observers = _.without( this.observers, attachments );

		} else {
			_.each( this.observers, function( attachments ) {
				attachments.off( null, null, this );
			}, this );
			delete this.observers;
		}

		return this;
	},
	/**
	 * @access private
	 *
	 * @param {wp.media.model.Attachments} attachment
	 * @param {wp.media.model.Attachments} attachments
	 * @param {Object} options
	 *
	 * @returns {wp.media.model.Attachments} Returns itself to allow chaining
	 */
	_validateHandler: function( attachment, attachments, options ) {
		// If we're not mirroring this `attachments` collection,
		// only retain the `silent` option.
		options = attachments === this.mirroring ? options : {
			silent: options && options.silent
		};

		return this.validate( attachment, options );
	},
	/**
	 * @access private
	 *
	 * @param {wp.media.model.Attachments} attachments
	 * @param {Object} options
	 * @returns {wp.media.model.Attachments} Returns itself to allow chaining
	 */
	_validateAllHandler: function( attachments, options ) {
		return this.validateAll( attachments, options );
	},
	/**
	 * Start mirroring another attachments collection, clearing out any models already
	 * in the collection.
	 *
	 * @param {wp.media.model.Attachments} The attachments collection to mirror.
	 * @returns {wp.media.model.Attachments} Returns itself to allow chaining
	 */
	mirror: function( attachments ) {
		if ( this.mirroring && this.mirroring === attachments ) {
			return this;
		}

		this.unmirror();
		this.mirroring = attachments;

		// Clear the collection silently. A `reset` event will be fired
		// when `observe()` calls `validateAll()`.
		this.reset( [], { silent: true } );
		this.observe( attachments );

		return this;
	},
	/**
	 * Stop mirroring another attachments collection.
	 */
	unmirror: function() {
		if ( ! this.mirroring ) {
			return;
		}

		this.unobserve( this.mirroring );
		delete this.mirroring;
	},
	/**
	 * Retrive more attachments from the server for the collection.
	 *
	 * Only works if the collection is mirroring a Query Attachments collection,
	 * and forwards to its `more` method. This collection class doesn't have
	 * server persistence by itself.
	 *
	 * @param {object} options
	 * @returns {Promise}
	 */
	more: function( options ) {
		var deferred = jQuery.Deferred(),
			mirroring = this.mirroring,
			attachments = this;

		if ( ! mirroring || ! mirroring.more ) {
			return deferred.resolveWith( this ).promise();
		}
		// If we're mirroring another collection, forward `more` to
		// the mirrored collection. Account for a race condition by
		// checking if we're still mirroring that collection when
		// the request resolves.
		mirroring.more( options ).done( function() {
			if ( this === attachments.mirroring ) {
				deferred.resolveWith( this );
			}
		});

		return deferred.promise();
	},
	/**
	 * Whether there are more attachments that haven't been sync'd from the server
	 * that match the collection's query.
	 *
	 * Only works if the collection is mirroring a Query Attachments collection,
	 * and forwards to its `hasMore` method. This collection class doesn't have
	 * server persistence by itself.
	 *
	 * @returns {boolean}
	 */
	hasMore: function() {
		return this.mirroring ? this.mirroring.hasMore() : false;
	},
	/**
	 * A custom AJAX-response parser.
	 *
	 * See trac ticket #24753
	 *
	 * @param {Object|Array} resp The raw response Object/Array.
	 * @param {Object} xhr
	 * @returns {Array} The array of model attributes to be added to the collection
	 */
	parse: function( resp, xhr ) {
		if ( ! _.isArray( resp ) ) {
			resp = [resp];
		}

		return _.map( resp, function( attrs ) {
			var id, attachment, newAttributes;

			if ( attrs instanceof Backbone.Model ) {
				id = attrs.get( 'id' );
				attrs = attrs.attributes;
			} else {
				id = attrs.id;
			}

			attachment = Attachment.get( id );
			newAttributes = attachment.parse( attrs, xhr );

			if ( ! _.isEqual( attachment.attributes, newAttributes ) ) {
				attachment.set( newAttributes );
			}

			return attachment;
		});
	},
	/**
	 * If the collection is a query, create and mirror an Attachments Query collection.
	 *
	 * @access private
	 */
	_requery: function( refresh ) {
		var props, Query;
		if ( this.props.get('query') ) {
			Query = require( './query.js' );
			props = this.props.toJSON();
			props.cache = ( true !== refresh );
			this.mirror( Query.get( props ) );
		}
	},
	/**
	 * If this collection is sorted by `menuOrder`, recalculates and saves
	 * the menu order to the database.
	 *
	 * @returns {undefined|Promise}
	 */
	saveMenuOrder: function() {
		if ( 'menuOrder' !== this.props.get('orderby') ) {
			return;
		}

		// Removes any uploading attachments, updates each attachment's
		// menu order, and returns an object with an { id: menuOrder }
		// mapping to pass to the request.
		var attachments = this.chain().filter( function( attachment ) {
			return ! _.isUndefined( attachment.id );
		}).map( function( attachment, index ) {
			// Indices start at 1.
			index = index + 1;
			attachment.set( 'menuOrder', index );
			return [ attachment.id, index ];
		}).object().value();

		if ( _.isEmpty( attachments ) ) {
			return;
		}

		return wp.media.post( 'save-attachment-order', {
			nonce:       wp.media.model.settings.post.nonce,
			post_id:     wp.media.model.settings.post.id,
			attachments: attachments
		});
	}
}, {
	/**
	 * A function to compare two attachment models in an attachments collection.
	 *
	 * Used as the default comparator for instances of wp.media.model.Attachments
	 * and its subclasses. @see wp.media.model.Attachments._changeOrderby().
	 *
	 * @static
	 *
	 * @param {Backbone.Model} a
	 * @param {Backbone.Model} b
	 * @param {Object} options
	 * @returns {Number} -1 if the first model should come before the second,
	 *    0 if they are of the same rank and
	 *    1 if the first model should come after.
	 */
	comparator: function( a, b, options ) {
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

		// If `options.ties` is set, don't enforce the `cid` tiebreaker.
		if ( options && options.ties ) {
			ac = bc = null;
		}

		return ( 'DESC' === order ) ? wp.media.compare( a, b, ac, bc ) : wp.media.compare( b, a, bc, ac );
	},
	/**
	 * @namespace
	 */
	filters: {
		/**
		 * @static
		 * Note that this client-side searching is *not* equivalent
		 * to our server-side searching.
		 *
		 * @param {wp.media.model.Attachment} attachment
		 *
		 * @this wp.media.model.Attachments
		 *
		 * @returns {Boolean}
		 */
		search: function( attachment ) {
			if ( ! this.props.get('search') ) {
				return true;
			}

			return _.any(['title','filename','description','caption','name'], function( key ) {
				var value = attachment.get( key );
				return value && -1 !== value.search( this.props.get('search') );
			}, this );
		},
		/**
		 * @static
		 * @param {wp.media.model.Attachment} attachment
		 *
		 * @this wp.media.model.Attachments
		 *
		 * @returns {Boolean}
		 */
		type: function( attachment ) {
			var type = this.props.get('type');
			return ! type || -1 !== type.indexOf( attachment.get('type') );
		},
		/**
		 * @static
		 * @param {wp.media.model.Attachment} attachment
		 *
		 * @this wp.media.model.Attachments
		 *
		 * @returns {Boolean}
		 */
		uploadedTo: function( attachment ) {
			var uploadedTo = this.props.get('uploadedTo');
			if ( _.isUndefined( uploadedTo ) ) {
				return true;
			}

			return uploadedTo === attachment.get('uploadedTo');
		},
		/**
		 * @static
		 * @param {wp.media.model.Attachment} attachment
		 *
		 * @this wp.media.model.Attachments
		 *
		 * @returns {Boolean}
		 */
		status: function( attachment ) {
			var status = this.props.get('status');
			if ( _.isUndefined( status ) ) {
				return true;
			}

			return status === attachment.get('status');
		}
	}
});

module.exports = Attachments;
