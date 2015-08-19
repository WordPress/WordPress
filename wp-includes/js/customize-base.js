window.wp = window.wp || {};

(function( exports, $ ){
	var api = {}, ctor, inherits,
		slice = Array.prototype.slice;

	// Shared empty constructor function to aid in prototype-chain creation.
	ctor = function() {};

	/**
	 * Helper function to correctly set up the prototype chain, for subclasses.
	 * Similar to `goog.inherits`, but uses a hash of prototype properties and
	 * class properties to be extended.
	 *
	 * @param  object parent      Parent class constructor to inherit from.
	 * @param  object protoProps  Properties to apply to the prototype for use as class instance properties.
	 * @param  object staticProps Properties to apply directly to the class constructor.
	 * @return child              The subclassed constructor.
	 */
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

	/**
	 * Base class for object inheritance.
	 */
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

	/**
	 * Creates a subclass of the class.
	 *
	 * @param  object protoProps  Properties to apply to the prototype.
	 * @param  object staticProps Properties to apply directly to the class.
	 * @return child              The subclass.
	 */
	api.Class.extend = function( protoProps, classProps ) {
		var child = inherits( this, protoProps, classProps );
		child.extend = this.extend;
		return child;
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

	/**
	 * An events manager object, offering the ability to bind to and trigger events.
	 *
	 * Used as a mixin.
	 */
	api.Events = {
		trigger: function( id ) {
			if ( this.topics && this.topics[ id ] )
				this.topics[ id ].fireWith( this, slice.call( arguments, 1 ) );
			return this;
		},

		bind: function( id ) {
			this.topics = this.topics || {};
			this.topics[ id ] = this.topics[ id ] || $.Callbacks();
			this.topics[ id ].add.apply( this.topics[ id ], slice.call( arguments, 1 ) );
			return this;
		},

		unbind: function( id ) {
			if ( this.topics && this.topics[ id ] )
				this.topics[ id ].remove.apply( this.topics[ id ], slice.call( arguments, 1 ) );
			return this;
		}
	};

	/**
	 * Observable values that support two-way binding.
	 *
	 * @constuctor
	 */
	api.Value = api.Class.extend({
		initialize: function( initial, options ) {
			this._value = initial; // @todo: potentially change this to a this.set() call.
			this.callbacks = $.Callbacks();
			this._dirty = false;

			$.extend( this, options || {} );

			this.set = $.proxy( this.set, this );
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

			to = this._setter.apply( this, arguments );
			to = this.validate( to );

			// Bail if the sanitized value is null or unchanged.
			if ( null === to || _.isEqual( from, to ) ) {
				return this;
			}

			this._value = to;
			this._dirty = true;

			this.callbacks.fireWith( this, [ to, from ] );

			return this;
		},

		_setter: function( to ) {
			return to;
		},

		setter: function( callback ) {
			var from = this.get();
			this._setter = callback;
			// Temporarily clear value so setter can decide if it's valid.
			this._value = null;
			this.set( from );
			return this;
		},

		resetSetter: function() {
			this._setter = this.constructor.prototype._setter;
			this.set( this.get() );
			return this;
		},

		validate: function( value ) {
			return value;
		},

		bind: function() {
			this.callbacks.add.apply( this.callbacks, arguments );
			return this;
		},

		unbind: function() {
			this.callbacks.remove.apply( this.callbacks, arguments );
			return this;
		},

		link: function() { // values*
			var set = this.set;
			$.each( arguments, function() {
				this.bind( set );
			});
			return this;
		},

		unlink: function() { // values*
			var set = this.set;
			$.each( arguments, function() {
				this.unbind( set );
			});
			return this;
		},

		sync: function() { // values*
			var that = this;
			$.each( arguments, function() {
				that.link( this );
				this.link( that );
			});
			return this;
		},

		unsync: function() { // values*
			var that = this;
			$.each( arguments, function() {
				that.unlink( this );
				this.unlink( that );
			});
			return this;
		}
	});

	/**
	 * A collection of observable values.
	 *
	 * @constuctor
	 * @augments wp.customize.Class
	 * @mixes wp.customize.Events
	 */
	api.Values = api.Class.extend({
		defaultConstructor: api.Value,

		initialize: function( options ) {
			$.extend( this, options || {} );

			this._value = {};
			this._deferreds = {};
		},

		instance: function( id ) {
			if ( arguments.length === 1 )
				return this.value( id );

			return this.when.apply( this, arguments );
		},

		value: function( id ) {
			return this._value[ id ];
		},

		has: function( id ) {
			return typeof this._value[ id ] !== 'undefined';
		},

		add: function( id, value ) {
			if ( this.has( id ) )
				return this.value( id );

			this._value[ id ] = value;
			value.parent = this;
			if ( value.extended( api.Value ) )
				value.bind( this._change );

			this.trigger( 'add', value );

			if ( this._deferreds[ id ] )
				this._deferreds[ id ].resolve();

			return this._value[ id ];
		},

		create: function( id ) {
			return this.add( id, new this.defaultConstructor( api.Class.applicator, slice.call( arguments, 1 ) ) );
		},

		each: function( callback, context ) {
			context = typeof context === 'undefined' ? this : context;

			$.each( this._value, function( key, obj ) {
				callback.call( context, obj, key );
			});
		},

		remove: function( id ) {
			var value;

			if ( this.has( id ) ) {
				value = this.value( id );
				this.trigger( 'remove', value );
				if ( value.extended( api.Value ) )
					value.unbind( this._change );
				delete value.parent;
			}

			delete this._value[ id ];
			delete this._deferreds[ id ];
		},

		/**
		 * Runs a callback once all requested values exist.
		 *
		 * when( ids*, [callback] );
		 *
		 * For example:
		 *     when( id1, id2, id3, function( value1, value2, value3 ) {} );
		 *
		 * @returns $.Deferred.promise();
		 */
		when: function() {
			var self = this,
				ids  = slice.call( arguments ),
				dfd  = $.Deferred();

			// If the last argument is a callback, bind it to .done()
			if ( $.isFunction( ids[ ids.length - 1 ] ) )
				dfd.done( ids.pop() );

			$.when.apply( $, $.map( ids, function( id ) {
				if ( self.has( id ) )
					return;

				return self._deferreds[ id ] = self._deferreds[ id ] || $.Deferred();
			})).done( function() {
				var values = $.map( ids, function( id ) {
						return self( id );
					});

				// If a value is missing, we've used at least one expired deferred.
				// Call Values.when again to generate a new deferred.
				if ( values.length !== ids.length ) {
					// ids.push( callback );
					self.when.apply( self, ids ).done( function() {
						dfd.resolveWith( self, values );
					});
					return;
				}

				dfd.resolveWith( self, values );
			});

			return dfd.promise();
		},

		_change: function() {
			this.parent.trigger( 'change', this );
		}
	});

	$.extend( api.Values.prototype, api.Events );


	/**
	 * Cast a string to a jQuery collection if it isn't already.
	 *
	 * @param {string|jQuery collection} element
	 */
	api.ensure = function( element ) {
		return typeof element == 'string' ? $( element ) : element;
	};

	/**
	 * An observable value that syncs with an element.
	 *
	 * Handles inputs, selects, and textareas by default.
	 *
	 * @constuctor
	 * @augments wp.customize.Value
	 * @augments wp.customize.Class
	 */
	api.Element = api.Value.extend({
		initialize: function( element, options ) {
			var self = this,
				synchronizer = api.Element.synchronizer.html,
				type, update, refresh;

			this.element = api.ensure( element );
			this.events = '';

			if ( this.element.is('input, select, textarea') ) {
				this.events += 'change';
				synchronizer = api.Element.synchronizer.val;

				if ( this.element.is('input') ) {
					type = this.element.prop('type');
					if ( api.Element.synchronizer[ type ] ) {
						synchronizer = api.Element.synchronizer[ type ];
					}
					if ( 'text' === type || 'password' === type ) {
						this.events += ' keyup';
					} else if ( 'range' === type ) {
						this.events += ' input propertychange';
					}
				} else if ( this.element.is('textarea') ) {
					this.events += ' keyup';
				}
			}

			api.Value.prototype.initialize.call( this, null, $.extend( options || {}, synchronizer ) );
			this._value = this.get();

			update  = this.update;
			refresh = this.refresh;

			this.update = function( to ) {
				if ( to !== refresh.call( self ) )
					update.apply( this, arguments );
			};
			this.refresh = function() {
				self.set( refresh.call( self ) );
			};

			this.bind( this.update );
			this.element.bind( this.events, this.refresh );
		},

		find: function( selector ) {
			return $( selector, this.element );
		},

		refresh: function() {},

		update: function() {}
	});

	api.Element.synchronizer = {};

	$.each( [ 'html', 'val' ], function( index, method ) {
		api.Element.synchronizer[ method ] = {
			update: function( to ) {
				this.element[ method ]( to );
			},
			refresh: function() {
				return this.element[ method ]();
			}
		};
	});

	api.Element.synchronizer.checkbox = {
		update: function( to ) {
			this.element.prop( 'checked', to );
		},
		refresh: function() {
			return this.element.prop( 'checked' );
		}
	};

	api.Element.synchronizer.radio = {
		update: function( to ) {
			this.element.filter( function() {
				return this.value === to;
			}).prop( 'checked', true );
		},
		refresh: function() {
			return this.element.filter( ':checked' ).val();
		}
	};

	$.support.postMessage = !! window.postMessage;

	/**
	 * Messenger for postMessage.
	 *
	 * @constuctor
	 * @augments wp.customize.Class
	 * @mixes wp.customize.Events
	 */
	api.Messenger = api.Class.extend({
		/**
		 * Create a new Value.
		 *
		 * @param  {string} key     Unique identifier.
		 * @param  {mixed}  initial Initial value.
		 * @param  {mixed}  options Options hash. Optional.
		 * @return {Value}          Class instance of the Value.
		 */
		add: function( key, initial, options ) {
			return this[ key ] = new api.Value( initial, options );
		},

		/**
		 * Initialize Messenger.
		 *
		 * @param  {object} params        Parameters to configure the messenger.
		 *         {string} .url          The URL to communicate with.
		 *         {window} .targetWindow The window instance to communicate with. Default window.parent.
		 *         {string} .channel      If provided, will send the channel with each message and only accept messages a matching channel.
		 * @param  {object} options       Extend any instance parameter or method with this object.
		 */
		initialize: function( params, options ) {
			// Target the parent frame by default, but only if a parent frame exists.
			var defaultTarget = window.parent == window ? null : window.parent;

			$.extend( this, options || {} );

			this.add( 'channel', params.channel );
			this.add( 'url', params.url || '' );
			this.add( 'origin', this.url() ).link( this.url ).setter( function( to ) {
				return to.replace( /([^:]+:\/\/[^\/]+).*/, '$1' );
			});

			// first add with no value
			this.add( 'targetWindow', null );
			// This avoids SecurityErrors when setting a window object in x-origin iframe'd scenarios.
			this.targetWindow.set = function( to ) {
				var from = this._value;

				to = this._setter.apply( this, arguments );
				to = this.validate( to );

				if ( null === to || from === to ) {
					return this;
				}

				this._value = to;
				this._dirty = true;

				this.callbacks.fireWith( this, [ to, from ] );

				return this;
			};
			// now set it
			this.targetWindow( params.targetWindow || defaultTarget );


			// Since we want jQuery to treat the receive function as unique
			// to this instance, we give the function a new guid.
			//
			// This will prevent every Messenger's receive function from being
			// unbound when calling $.off( 'message', this.receive );
			this.receive = $.proxy( this.receive, this );
			this.receive.guid = $.guid++;

			$( window ).on( 'message', this.receive );
		},

		destroy: function() {
			$( window ).off( 'message', this.receive );
		},

		receive: function( event ) {
			var message;

			event = event.originalEvent;

			if ( ! this.targetWindow() )
				return;

			// Check to make sure the origin is valid.
			if ( this.origin() && event.origin !== this.origin() )
				return;

			// Ensure we have a string that's JSON.parse-able
			if ( typeof event.data !== 'string' || event.data[0] !== '{' ) {
				return;
			}

			message = JSON.parse( event.data );

			// Check required message properties.
			if ( ! message || ! message.id || typeof message.data === 'undefined' )
				return;

			// Check if channel names match.
			if ( ( message.channel || this.channel() ) && this.channel() !== message.channel )
				return;

			this.trigger( message.id, message.data );
		},

		send: function( id, data ) {
			var message;

			data = typeof data === 'undefined' ? null : data;

			if ( ! this.url() || ! this.targetWindow() )
				return;

			message = { id: id, data: data };
			if ( this.channel() )
				message.channel = this.channel();

			this.targetWindow().postMessage( JSON.stringify( message ), this.origin() );
		}
	});

	// Add the Events mixin to api.Messenger.
	$.extend( api.Messenger.prototype, api.Events );

	// Core customize object.
	api = $.extend( new api.Values(), api );
	api.get = function() {
		var result = {};

		this.each( function( obj, key ) {
			result[ key ] = obj.get();
		});

		return result;
	};

	// Expose the API publicly on window.wp.customize
	exports.customize = api;
})( wp, jQuery );
