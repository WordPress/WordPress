if ( typeof wp === 'undefined' )
	var wp = {};

(function( exports, $ ){
	var api, extend, ctor, inherits, ready,
		slice = Array.prototype.slice;

	/* =====================================================================
	 * Micro-inheritance - thank you, backbone.js.
	 * ===================================================================== */

	extend = function( protoProps, classProps ) {
		var child = inherits( this, protoProps, classProps );
		child.extend = this.extend;
		return child;
	};

	// Shared empty constructor function to aid in prototype-chain creation.
	ctor = function() {};

	// Helper function to correctly set up the prototype chain, for subclasses.
	// Similar to `goog.inherits`, but uses a hash of prototype properties and
	// class properties to be extended.
	inherits = function( parent, protoProps, staticProps ) {
		var child;

		// The constructor function for the new subclass is either defined by you
		// (the "constructor" property in your `extend` definition), or defaulted
		// by us to simply call `super()`.
		if ( protoProps && protoProps.hasOwnProperty( 'constructor' ) ) {
			child = protoProps.constructor;
		} else {
			child = function() {
				// Storing the result `super()` before returning the value
				// prevents a bug in Opera where, if the constructor returns
				// a function, Opera will reject the return value in favor of
				// the original object. This causes all sorts of trouble.
				var result = parent.apply( this, arguments );
				return result;
			};
		}

		// Inherit class (static) properties from parent.
		$.extend( child, parent );

		// Set the prototype chain to inherit from `parent`, without calling
		// `parent`'s constructor function.
		ctor.prototype  = parent.prototype;
		child.prototype = new ctor();

		// Add prototype properties (instance properties) to the subclass,
		// if supplied.
		if ( protoProps )
			$.extend( child.prototype, protoProps );

		// Add static properties to the constructor function, if supplied.
		if ( staticProps )
			$.extend( child, staticProps );

		// Correctly set child's `prototype.constructor`.
		child.prototype.constructor = child;

		// Set a convenience property in case the parent's prototype is needed later.
		child.__super__ = parent.prototype;

		return child;
	};

	/* =====================================================================
	 * customize function.
	 * ===================================================================== */
	ready = $.Callbacks( 'once memory' );

	/*
	 * Sugar for main customize function. Supports several signatures.
	 *
	 * customize( callback, [context] );
	 *   Binds a callback to be fired when the customizer is ready.
	 *   - callback, function
	 *   - context, object
	 *
	 * customize( setting );
	 *   Fetches a setting object by ID.
	 *   - setting, string - The setting ID.
	 *
	 */
	api = {};
	// api = function( callback, context ) {
	// 	if ( $.isFunction( callback ) ) {
	// 		if ( context )
	// 			callback = $.proxy( callback, context );
	// 		ready.add( callback );
	//
	// 		return api;
	// 	}
	// }

	/* =====================================================================
	 * Base class.
	 * ===================================================================== */

	api.Class = function( applicator, argsArray, options ) {
		var magic, args = arguments;

		if ( applicator && argsArray && api.Class.applicator === applicator ) {
			args = argsArray;
			$.extend( this, options || {} );
		}

		magic = this;
		if ( this.instance ) {
			magic = function() {
				return magic.instance.apply( magic, arguments );
			};

			$.extend( magic, this );
		}

		magic.initialize.apply( magic, args );
		return magic;
	};

	api.Class.applicator = {};

	api.Class.prototype.initialize = function() {};

	/*
	 * Checks whether a given instance extended a constructor.
	 *
	 * The magic surrounding the instance parameter causes the instanceof
	 * keyword to return inaccurate results; it defaults to the function's
	 * prototype instead of the constructor chain. Hence this function.
	 */
	api.Class.prototype.extended = function( constructor ) {
		var proto = this;

		while ( typeof proto.constructor !== 'undefined' ) {
			if ( proto.constructor === constructor )
				return true;
			if ( typeof proto.constructor.__super__ === 'undefined' )
				return false;
			proto = proto.constructor.__super__;
		}
		return false;
	};

	api.Class.extend = extend;

	/* =====================================================================
	 * Light two-way binding.
	 * ===================================================================== */

	api.Value = api.Class.extend({
		initialize: function( initial, options ) {
			this._value = initial;
			this.callbacks = $.Callbacks();

			$.extend( this, options || {} );
		},

		/*
		 * Magic. Returns a function that will become the instance.
		 * Set to null to prevent the instance from extending a function.
		 */
		instance: function() {
			return arguments.length ? this.set.apply( this, arguments ) : this.get();
		},

		get: function() {
			return this._value;
		},

		set: function( to ) {
			var from = this._value;

			to = this.validate( to );

			// Bail if the sanitized value is null or unchanged.
			if ( null === to || this._value === to )
				return this;

			this._value = to;

			this.callbacks.fireWith( this, [ to, from ] );

			return this;
		},

		validate: function( value ) {
			return value;
		},

		bind: function( callback ) {
			this.callbacks.add.apply( this.callbacks, arguments );
			return this;
		},

		unbind: function( callback ) {
			this.callbacks.remove.apply( this.callbacks, arguments );
			return this;
		},

		/*
		 * Allows the creation of composite values.
		 * Overrides the native link method (can be reverted with `unlink`).
		 */
		link: function() {
			var keys = slice.call( arguments ),
				callback = keys.pop(),
				self = this,
				set, key, active;

			if ( this.links )
				this.unlink();

			this.links = [];

			// Single argument means a direct binding.
			if ( ! keys.length ) {
				keys = [ callback ];
				callback = function( value, to ) {
					return to;
				};
			}

			while ( key = keys.shift() ) {
				if ( this._parent && $.type( key ) == 'string' )
					this.links.push( this._parent[ key ] );
				else
					this.links.push( key );
			}

			// Replace this.set with the assignment function.
			set = function() {
				var args, result;

				// If we call set from within the assignment function,
				// pass the arguments to the original set.
				if ( active )
					return self.set.original.apply( self, arguments );

				active = true;

				args = self.links.concat( slice.call( arguments ) );
				result = callback.apply( self, args );

				active = false;

				if ( typeof result !== 'undefined' )
					self.set.original.call( self, result );
			};

			set.original = this.set;
			this.set = set;

			// Bind the new function to the master values.
			$.each( this.links, function( key, value ) {
				value.bind( self.set );
			});

			this.set( this.get() );

			return this;
		},

		unlink: function() {
			var set = this.set;

			$.each( this.links, function( key, value ) {
				value.unbind( set );
			});

			delete this.links;
			this.set = this.set.original;
			return this;
		}
	});

	api.ensure = function( element ) {
		return typeof element == 'string' ? $( element ) : element;
	};

	sync = {
		'val': {
			update: function() {
				this.element[ this._updater ]( this._value );
			},
			refresh: function() {
				this.set( this.element[ this._refresher ]() );
			}
		}
	}

	api.Element = api.Value.extend({
		initialize: function( element, options ) {
			var synchronizer = api.Element.synchronizer.html,
				type;

			this.element = api.ensure( element );
			this.events = '';

			if ( this.element.is('input, select, textarea') ) {
				this.events += 'change';
				synchronizer = api.Element.synchronizer.val;

				if ( this.element.is('input') ) {
					type = this.element.prop('type');
					if ( api.Element.synchronizer[ type ] )
						synchronizer = api.Element.synchronizer[ type ];
					if ( 'text' === type || 'password' === type )
						this.events += ' keyup';
				}
			}

			api.Value.prototype.initialize.call( this, null, $.extend( options || {}, synchronizer ) );
			this._value = this.get();

			this.bind( this.update );

			this.refresh = $.proxy( this.refresh, this );
			this.element.bind( this.events, this.refresh );
		},

		find: function( selector ) {
			return $( selector, this.element );
		},

		refresh: function() {},
		update: function() {}
	});

	api.Element.synchronizer = {};

	$.each( [ 'html', 'val' ], function( i, method ) {
		api.Element.synchronizer[ method ] = {
			update: function( to ) {
				this.element[ method ]( to );
			},
			refresh: function() {
				this.set( this.element[ method ]() );
			}
		};
	});

	api.Element.synchronizer.checkbox = {
		update: function( to ) {
			this.element.prop( 'checked', to );
		},
		refresh: function() {
			this.set( this.element.prop( 'checked' ) );
		}
	};

	api.Element.synchronizer.radio = {
		update: function( to ) {
			this.element.filter( function() {
				return this.value === to;
			}).prop( 'checked', true );
		},
		refresh: function() {
			this.set( this.element.filter( ':checked' ).val() );
		}
	};

	api.ValueFactory = function( constructor ) {
		constructor = constructor || api.Value;

		return function( key ) {
			var args = slice.call( arguments, 1 );
			this[ key ] = new constructor( api.Class.applicator, args );
			this[ key ]._parent = this;
			return this[ key ];
		};
	};

	api.Values = api.Value.extend({
		defaultConstructor: api.Value,

		initialize: function( options ) {
			api.Value.prototype.initialize.call( this, {}, options || {} );
		},

		instance: function( id ) {
			return this.value( id );
		},

		value: function( id ) {
			return this._value[ id ];
		},

		has: function( id ) {
			return typeof this._value[ id ] !== 'undefined';
		},

		add: function( id, value ) {
			if ( this.has( id ) )
				return;

			this._value[ id ] = value;
			this._value[ id ]._parent = this._value;
			return this._value[ id ];
		},

		set: function( id ) {
			if ( this.has( id ) )
				return this.pass( 'set', arguments );

			return this.add( id, new this.defaultConstructor( api.Class.applicator, slice.call( arguments, 1 ) ) );
		},

		remove: function( id ) {
			delete this._value[ id ];
		},

		pass: function( fn, args ) {
			var id, value;

			args = slice.call( args );
			id   = args.shift();

			if ( ! this.has( id ) )
				return;

			value = this.value( id );
			return value[ fn ].apply( value, args );
		}
	});

	$.each( [ 'get', 'bind', 'unbind', 'link', 'unlink' ], function( i, method ) {
		api.Values.prototype[ method ] = function() {
			return this.pass( method, arguments );
		};
	});

	/* =====================================================================
	 * Messenger for postMessage.
	 * ===================================================================== */

	api.Messenger = api.Class.extend({
		add: api.ValueFactory(),

		initialize: function( url, options ) {
			$.extend( this, options || {} );

			this.add( 'url', url );
			this.add( 'origin' ).link( 'url', function( url ) {
				return url().replace( /([^:]+:\/\/[^\/]+).*/, '$1' );
			});

			this.topics = {};

			$.receiveMessage( $.proxy( this.receive, this ), this.origin() || null );
		},
		receive: function( event ) {
			var message;

			console.log( 'messenger receiveMessage', arguments );

			// @todo: remove, this is done in the postMessage plugin.
			// if ( this.origin && event.origin !== this.origin )
			// 	return;

			message = JSON.parse( event.data );

			if ( message && message.id && message.data && this.topics[ message.id ] )
				this.topics[ message.id ].fireWith( this, [ message.data ]);
		},
		send: function( id, data ) {
			var message;

			if ( ! this.url() )
				return;

			console.log( 'sending message', id, data );
			message = JSON.stringify({ id: id, data: data });
			$.postMessage( message, this.url(), this.targetWindow || null );
		},
		bind: function( id, callback ) {
			var topic = this.topics[ id ] || ( this.topics[ id ] = $.Callbacks() );
			topic.add( callback );
		},
		unbind: function( id, callback ) {
			if ( this.topics[ id ] )
				this.topics[ id ].remove( callback );
		}
	});

	/* =====================================================================
	 * Core customize object.
	 * ===================================================================== */

	api = $.extend( new api.Values(), api );

	// Expose the API to the world.
	exports.customize = api;
})( wp, jQuery );
