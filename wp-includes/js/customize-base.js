/**
 * @output wp-includes/js/customize-base.js
 */

/** @namespace wp */
window.wp = window.wp || {};

/**
 * @param {Object}       wp The WordPress global object.
 * @param {JQueryStatic} $  The jQuery object.
 */
(function( wp, $ ){
	var api = {}, ctor, inherits;

	// Shared empty constructor function to aid in prototype-chain creation.
	ctor = function() {};

	/**
	 * Helper function to correctly set up the prototype chain, for subclasses.
	 * Similar to `goog.inherits`, but uses a hash of prototype properties and
	 * class properties to be extended.
	 *
	 * @param {Object} parent        Parent class constructor to inherit from.
	 * @param {Object} [protoProps]  Properties to apply to the prototype for use as class instance properties.
	 * @param {Object} [staticProps] Properties to apply directly to the class constructor.
	 * @return {Function} The subclassed constructor.
	 */
	inherits = function( parent, protoProps, staticProps ) {
		var child;

		/*
		 * The constructor function for the new subclass is either defined by you
		 * (the "constructor" property in your `extend` definition), or defaulted
		 * by us to simply call `super()`.
		 */
		if ( protoProps && protoProps.hasOwnProperty( 'constructor' ) ) {
			child = protoProps.constructor;
		} else {
			child = function( ...args ) {
				/*
				 * Storing the result `super()` before returning the value
				 * prevents a bug in Opera where, if the constructor returns
				 * a function, Opera will reject the return value in favor of
				 * the original object. This causes all sorts of trouble.
				 */
				var result = parent.apply( this, args );
				return result;
			};
		}

		// Inherit class (static) properties from parent.
		$.extend( child, parent );

		// Set the prototype chain to inherit from `parent`,
		// without calling `parent`'s constructor function.
		ctor.prototype  = parent.prototype;
		child.prototype = new ctor();

		// Add prototype properties (instance properties) to the subclass,
		// if supplied.
		if ( protoProps ) {
			$.extend( child.prototype, protoProps );
		}

		// Add static properties to the constructor function, if supplied.
		if ( staticProps ) {
			$.extend( child, staticProps );
		}

		// Correctly set child's `prototype.constructor`.
		child.prototype.constructor = child;

		// Set a convenience property in case the parent's prototype is needed later.
		child.__super__ = parent.prototype;

		return child;
	};

	/**
	 * Base class for object inheritance.
	 *
	 * The arguments are normally passed straight through to the class's
	 * initialize method. As a special case, when the first argument is
	 * api.Class.applicator, the second argument is used as the array of
	 * arguments for initialize and the third is used to extend the instance.
	 * This allows a class to be constructed from an argument list that is only
	 * known at runtime. See {@link wp.customize.Values#create}.
	 *
	 * @param {...*} args Arguments for the class's initialize method. Or
	 *                    api.Class.applicator, followed by the array of
	 *                    arguments for the initialize method, followed by an
	 *                    optional object of properties to extend the instance
	 *                    with.
	 * @return {Object|Function} The instance of the class, which is a function when the class
	 *                           defines an instance method, so that the instance is callable.
	 */
	api.Class = function( ...args ) {
		var magic;

		if ( args[0] && args[1] && api.Class.applicator === args[0] ) {
			$.extend( this, args[2] || {} );
			args = args[1];
		}

		magic = this;

		/*
		 * If the class has a method called "instance",
		 * the return value from the class' constructor will be a function that
		 * calls the "instance" method.
		 *
		 * It is also an object that has properties and methods inside it.
		 */
		if ( this.instance ) {
			magic = function( ...instanceArgs ) {
				return magic.instance.apply( magic, instanceArgs );
			};

			$.extend( magic, this );
		}

		magic.initialize.apply( magic, args );
		return magic;
	};

	/**
	 * Creates a subclass of the class.
	 *
	 * @param {Object} [protoProps]  Properties to apply to the prototype.
	 * @param {Object} [staticProps] Properties to apply directly to the class.
	 * @return {Function} The subclass.
	 */
	api.Class.extend = function( protoProps, staticProps ) {
		var child = inherits( this, protoProps, staticProps );
		child.extend = this.extend;
		return child;
	};

	api.Class.applicator = {};

	/**
	 * Initialize a class instance.
	 *
	 * Override this function in a subclass as needed.
	 */
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
			if ( proto.constructor === constructor ) {
				return true;
			}
			if ( typeof proto.constructor.__super__ === 'undefined' ) {
				return false;
			}
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
		/**
		 * Trigger an event, invoking all of the callbacks bound to it.
		 *
		 * @param {string} id     ID of the event to trigger.
		 * @param {...*}   [args] Zero or more arguments to pass to the bound callbacks.
		 * @return {Object} The instance the mixin is applied to.
		 */
		trigger: function( id, ...args ) {
			if ( this.topics && this.topics[ id ] ) {
				this.topics[ id ].fireWith( this, args );
			}
			return this;
		},

		/**
		 * Bind one or more callbacks to an event.
		 *
		 * @param {string}      id        ID of the event to bind to.
		 * @param {...Function} callbacks A function, or multiple functions, to add to the callback stack.
		 * @return {Object} The instance the mixin is applied to.
		 */
		bind: function( id, ...callbacks ) {
			this.topics = this.topics || {};
			this.topics[ id ] = this.topics[ id ] || $.Callbacks();
			this.topics[ id ].add.apply( this.topics[ id ], callbacks );
			return this;
		},

		/**
		 * Unbind one or more previously bound callbacks from an event.
		 *
		 * @param {string}      id        ID of the event to unbind from.
		 * @param {...Function} callbacks A function, or multiple functions, to remove from the callback stack.
		 * @return {Object} The instance the mixin is applied to.
		 */
		unbind: function( id, ...callbacks ) {
			if ( this.topics && this.topics[ id ] ) {
				this.topics[ id ].remove.apply( this.topics[ id ], callbacks );
			}
			return this;
		}
	};

	/**
	 * Observable values that support two-way binding.
	 *
	 * @memberOf wp.customize
	 * @alias wp.customize.Value
	 *
	 * @class
	 * @augments wp.customize.Class
	 */
	api.Value = api.Class.extend(/** @lends wp.customize.Value.prototype */{
		/**
		 * @param {*}      initial   The initial value.
		 * @param {Object} [options] Options to extend the instance with.
		 */
		initialize: function( initial, options ) {
			this._value = initial; // @todo Potentially change this to a this.set() call.
			this.callbacks = $.Callbacks();
			this._dirty = false;

			$.extend( this, options || {} );

			this.set = this.set.bind( this );
		},

		/*
		 * Magic. Returns a function that will become the instance.
		 * Set to null to prevent the instance from extending a function.
		 */
		instance: function( ...args ) {
			return args.length ? this.set.apply( this, args ) : this.get();
		},

		/**
		 * Get the value.
		 *
		 * @return {*} The value.
		 */
		get: function() {
			return this._value;
		},

		/**
		 * Set the value and trigger all bound callbacks.
		 *
		 * @param {*}    to     New value.
		 * @param {...*} [args] Zero or more additional arguments to pass to the setter.
		 * @return {wp.customize.Value} The instance of the Value.
		 */
		set: function( to, ...args ) {
			var from = this._value;

			to = this._setter( to, ...args );
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

		/**
		 * Bind a function to be invoked whenever the value changes.
		 *
		 * @param {...Function} callbacks A function, or multiple functions, to add to the callback stack.
		 * @return {wp.customize.Value} The instance of the Value.
		 */
		bind: function( ...callbacks ) {
			this.callbacks.add.apply( this.callbacks, callbacks );
			return this;
		},

		/**
		 * Unbind a previously bound function.
		 *
		 * @param {...Function} callbacks A function, or multiple functions, to remove from the callback stack.
		 * @return {wp.customize.Value} The instance of the Value.
		 */
		unbind: function( ...callbacks ) {
			this.callbacks.remove.apply( this.callbacks, callbacks );
			return this;
		},

		/**
		 * Update this value whenever one or more other values change.
		 *
		 * Note that this is one-directional: this value follows the supplied
		 * values, not the other way around. Use sync() to link in both
		 * directions.
		 *
		 * @param {...wp.customize.Value} values A value, or multiple values, for this value to follow.
		 * @return {wp.customize.Value} The instance of the Value.
		 */
		link: function( ...values ) {
			var set = this.set;
			$.each( values, function() {
				this.bind( set );
			});
			return this;
		},

		/**
		 * Stop updating this value when one or more other values change.
		 *
		 * @param {...wp.customize.Value} values A value, or multiple values, for this value to stop following.
		 * @return {wp.customize.Value} The instance of the Value.
		 */
		unlink: function( ...values ) {
			var set = this.set;
			$.each( values, function() {
				this.unbind( set );
			});
			return this;
		},

		/**
		 * Link this value with one or more other values in both directions.
		 *
		 * @param {...wp.customize.Value} values A value, or multiple values, to keep in sync with this value.
		 * @return {wp.customize.Value} The instance of the Value.
		 */
		sync: function( ...values ) {
			var that = this;
			$.each( values, function() {
				that.link( this );
				this.link( that );
			});
			return this;
		},

		/**
		 * Stop keeping this value in sync with one or more other values.
		 *
		 * @param {...wp.customize.Value} values A value, or multiple values, to stop keeping in sync with this value.
		 * @return {wp.customize.Value} The instance of the Value.
		 */
		unsync: function( ...values ) {
			var that = this;
			$.each( values, function() {
				that.unlink( this );
				this.unlink( that );
			});
			return this;
		}
	});

	/**
	 * A collection of observable values.
	 *
	 * @memberOf wp.customize
	 * @alias wp.customize.Values
	 *
	 * @class
	 * @augments wp.customize.Class
	 * @mixes wp.customize.Events
	 */
	api.Values = api.Class.extend(/** @lends wp.customize.Values.prototype */{

		/**
		 * The default constructor for items of the collection.
		 *
		 * @type {Function}
		 */
		defaultConstructor: api.Value,

		initialize: function( options ) {
			$.extend( this, options || {} );

			this._value = {};
			this._deferreds = {};
		},

		/**
		 * Get the instance of an item from the collection if only ID is specified.
		 *
		 * If more than one argument is supplied, all are expected to be IDs and
		 * the last to be a function callback that will be invoked when the requested
		 * items are available.
		 *
		 * @see {@link wp.customize.Values#when}
		 *
		 * @param {string}               id     ID of the item.
		 * @param {...(string|Function)} [args] Zero or more IDs of items to wait for and a callback
		 *                                      function to invoke when they're available. Optional.
		 * @return {*} The item instance if only one ID was supplied.
		 *             A Deferred Promise object if a callback function is supplied.
		 */
		instance: function( id, ...args ) {
			if ( 0 === args.length ) {
				return this.value( id );
			}

			return this.when( id, ...args );
		},

		/**
		 * Get the instance of an item.
		 *
		 * @param {string} id The ID of the item.
		 * @return {*} The item instance.
		 */
		value: function( id ) {
			return this._value[ id ];
		},

		/**
		 * Whether the collection has an item with the given ID.
		 *
		 * @param {string} id The ID of the item to look for.
		 * @return {boolean} True if the collection has an item with the given ID, false otherwise.
		 */
		has: function( id ) {
			return typeof this._value[ id ] !== 'undefined';
		},

		/**
		 * Add an item to the collection.
		 *
		 * @param {string|wp.customize.Class} item         The item instance to add, or the ID for the instance to add.
		 *                                                 When an ID string is supplied, then itemObject must be provided.
		 * @param {wp.customize.Class}        [itemObject] The item instance when the first argument is an ID string.
		 * @return {wp.customize.Class} The new item's instance, or an existing instance if already added.
		 */
		add: function( item, itemObject ) {
			var collection = this, id, instance;
			if ( 'string' === typeof item ) {
				id = item;
				instance = itemObject;
			} else {
				if ( 'string' !== typeof item.id ) {
					throw new Error( 'Unknown key' );
				}
				id = item.id;
				instance = item;
			}

			if ( collection.has( id ) ) {
				return collection.value( id );
			}

			collection._value[ id ] = instance;
			instance.parent = collection;

			// Propagate a 'change' event on an item up to the collection.
			if ( instance.extended( api.Value ) ) {
				instance.bind( collection._change );
			}

			collection.trigger( 'add', instance );

			// If a deferred object exists for this item,
			// resolve it.
			if ( collection._deferreds[ id ] ) {
				collection._deferreds[ id ].resolve();
			}

			return collection._value[ id ];
		},

		/**
		 * Create a new item of the collection using the collection's default constructor
		 * and store it in the collection.
		 *
		 * @param {string} id     The ID of the item.
		 * @param {...*}   [args] Zero or more extra arguments to pass into the item's initialize method.
		 * @return {wp.customize.Class} The new item's instance.
		 */
		create: function( id, ...args ) {
			return this.add( id, new this.defaultConstructor( api.Class.applicator, args ) );
		},

		/**
		 * Iterate over all items in the collection invoking the provided callback.
		 *
		 * @param {Function} callback  Function to invoke.
		 * @param {Object}   [context] Object context to invoke the function with.
		 */
		each: function( callback, context ) {
			context = typeof context === 'undefined' ? this : context;

			$.each( this._value, function( key, obj ) {
				callback.call( context, obj, key );
			});
		},

		/**
		 * Remove an item from the collection.
		 *
		 * @param {string} id The ID of the item to remove.
		 */
		remove: function( id ) {
			var value = this.value( id );

			if ( value ) {

				// Trigger event right before the element is removed from the collection.
				this.trigger( 'remove', value );

				if ( value.extended( api.Value ) ) {
					value.unbind( this._change );
				}
				delete value.parent;
			}

			delete this._value[ id ];
			delete this._deferreds[ id ];

			// Trigger removed event after the item has been eliminated from the collection.
			if ( value ) {
				this.trigger( 'removed', value );
			}
		},

		/**
		 * Runs a callback once all requested values exist.
		 *
		 * when( ids*, [callback] );
		 *
		 * For example:
		 *     when( id1, id2, id3, function( value1, value2, value3 ) {} );
		 *
		 * @param {...(string|Function)} ids Zero or more IDs of items to wait for, optionally followed by
		 *                                   a callback function to invoke once they are all available.
		 * @return {JQuery.Promise<*>} A promise that is resolved when all of the requested values exist.
		 */
		when: function( ...ids ) {
			var self = this,
				dfd  = $.Deferred();

			// If the last argument is a callback, bind it to .done().
			if ( typeof ids[ ids.length - 1 ] === 'function' ) {
				dfd.done( ids.pop() );
			}

			/*
			 * Create a stack of deferred objects for each item that is not
			 * yet available, and invoke the supplied callback when they are.
			 */
			$.when.apply( $, $.map( ids, function( id ) {
				if ( self.has( id ) ) {
					return;
				}

				/*
				 * The requested item is not available yet, create a deferred
				 * object to resolve when it becomes available.
				 */
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

		/**
		 * A helper function to propagate a 'change' event from an item
		 * to the collection itself.
		 */
		_change: function() {
			this.parent.trigger( 'change', this );
		}
	});

	// Create a global events bus on the Customizer.
	$.extend( api.Values.prototype, api.Events );


	/**
	 * Cast a string to a jQuery object if it isn't already.
	 *
	 * @param {string|JQuery} element A selector or an existing jQuery collection.
	 * @return {JQuery} The jQuery collection.
	 */
	api.ensure = function( element ) {
		return typeof element === 'string' ? $( element ) : element;
	};

	/**
	 * An observable value that syncs with an element.
	 *
	 * Handles inputs, selects, and textareas by default.
	 *
	 * @memberOf wp.customize
	 * @alias wp.customize.Element
	 *
	 * @class
	 * @augments wp.customize.Value
	 * @augments wp.customize.Class
	 */
	api.Element = api.Value.extend(/** @lends wp.customize.Element */{
		initialize: function( element, options ) {
			var self = this,
				synchronizer = api.Element.synchronizer.html,
				type, update, refresh;

			this.element = api.ensure( element );
			this.events = '';

			if ( this.element.is( 'input, select, textarea' ) ) {
				type = this.element.prop( 'type' );
				this.events += ' change input';
				synchronizer = api.Element.synchronizer.val;

				if ( this.element.is( 'input' ) && api.Element.synchronizer[ type ] ) {
					synchronizer = api.Element.synchronizer[ type ];
				}
			}

			api.Value.prototype.initialize.call( this, null, $.extend( options || {}, synchronizer ) );
			this._value = this.get();

			update = this.update;
			refresh = this.refresh;

			this.update = function( to, ...args ) {
				if ( to !== refresh.call( self ) ) {
					update.call( this, to, ...args );
				}
			};
			this.refresh = function() {
				self.set( refresh.call( self ) );
			};

			this.bind( this.update );
			this.element.on( this.events, this.refresh );
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
	 * A communicator for sending data from one window to another over postMessage.
	 *
	 * @memberOf wp.customize
	 * @alias wp.customize.Messenger
	 *
	 * @class
	 * @augments wp.customize.Class
	 * @mixes wp.customize.Events
	 */
	api.Messenger = api.Class.extend(/** @lends wp.customize.Messenger.prototype */{
		/**
		 * Create a new Value.
		 *
		 * @param {string} key       Unique identifier.
		 * @param {*}      initial   Initial value.
		 * @param {*}      [options] Options hash.
		 * @return {wp.customize.Value} Class instance of the Value.
		 */
		add: function( key, initial, options ) {
			return this[ key ] = new api.Value( initial, options );
		},

		/**
		 * Initialize Messenger.
		 *
		 * @param {Object} params              Parameters to configure the messenger.
		 * @param {string} params.url          The URL to communicate with.
		 * @param {Window} params.targetWindow The window instance to communicate with. Default window.parent.
		 * @param {string} [params.channel]    If provided, will send the channel with each message and only accept messages a matching channel.
		 * @param {Object} [options]           Extend any instance parameter or method with this object.
		 */
		initialize: function( params, options ) {
			// Target the parent frame by default, but only if a parent frame exists.
			var defaultTarget = window.parent === window ? null : window.parent;

			$.extend( this, options || {} );

			this.add( 'channel', params.channel );
			this.add( 'url', params.url || '' );
			this.add( 'origin', this.url() ).link( this.url ).setter( function( to ) {
				var urlParser = document.createElement( 'a' );
				urlParser.href = to;
				// Port stripping needed by IE since it adds to host but not to event.origin.
				return urlParser.protocol + '//' + urlParser.host.replace( /:(80|443)$/, '' );
			});

			// First add with no value.
			this.add( 'targetWindow', null );
			// This avoids SecurityErrors when setting a window object in x-origin iframe'd scenarios.
			this.targetWindow.set = function( to, ...args ) {
				var from = this._value;

				to = this._setter( to, ...args );
				to = this.validate( to );

				if ( null === to || from === to ) {
					return this;
				}

				this._value = to;
				this._dirty = true;

				this.callbacks.fireWith( this, [ to, from ] );

				return this;
			};
			// Now set it.
			this.targetWindow( params.targetWindow || defaultTarget );


			/*
			 * Since we want jQuery to treat the receive function as unique
			 * to this instance, we give the function a new guid.
			 *
			 * This will prevent every Messenger's receive function from being
			 * unbound when calling $.off( 'message', this.receive );
			 */
			this.receive = this.receive.bind( this );
			this.receive.guid = $.guid++;

			$( window ).on( 'message', this.receive );
		},

		destroy: function() {
			$( window ).off( 'message', this.receive );
		},

		/**
		 * Receive data from the other window.
		 *
		 * @param {JQuery.Event} event Event with embedded data.
		 */
		receive: function( event ) {
			var message;

			event = event.originalEvent;

			if ( ! this.targetWindow || ! this.targetWindow() ) {
				return;
			}

			// Check to make sure the origin is valid.
			if ( this.origin() && event.origin !== this.origin() ) {
				return;
			}

			// Ensure we have a string that's JSON.parse-able.
			if ( typeof event.data !== 'string' || event.data[0] !== '{' ) {
				return;
			}

			message = JSON.parse( event.data );

			// Check required message properties.
			if ( ! message || ! message.id || typeof message.data === 'undefined' ) {
				return;
			}

			// Check if channel names match.
			if ( ( message.channel || this.channel() ) && this.channel() !== message.channel ) {
				return;
			}

			this.trigger( message.id, message.data );
		},

		/**
		 * Send data to the other window.
		 *
		 * @param {string} id     The event name.
		 * @param {*}      [data] Data.
		 */
		send: function( id, data ) {
			var message;

			data = typeof data === 'undefined' ? null : data;

			if ( ! this.url() || ! this.targetWindow() ) {
				return;
			}

			message = { id: id, data: data };
			if ( this.channel() ) {
				message.channel = this.channel();
			}

			this.targetWindow().postMessage( JSON.stringify( message ), this.origin() );
		}
	});

	// Add the Events mixin to api.Messenger.
	$.extend( api.Messenger.prototype, api.Events );

	/**
	 * Notification.
	 *
	 * @class
	 * @augments wp.customize.Class
	 * @since 4.6.0
	 *
	 * @memberOf wp.customize
	 * @alias wp.customize.Notification
	 *
	 * @param {string}  code                      The error code.
	 * @param {Object}  params                    Params.
	 * @param {string}  [params.message=null]     The error message.
	 * @param {string}  [params.type=error]       The notification type.
	 * @param {boolean} [params.fromServer=false] Whether the notification was server-sent.
	 * @param {string}  [params.setting=null]     The setting ID that the notification is related to.
	 * @param {*}       [params.data=null]        Any additional data.
	 */
	api.Notification = api.Class.extend(/** @lends wp.customize.Notification.prototype */{

		/**
		 * Template function for rendering the notification.
		 *
		 * This will be populated with template option or else it will be populated with template from the ID.
		 *
		 * @since 4.9.0
		 * @member {Function}
		 */
		template: null,

		/**
		 * ID for the template to render the notification.
		 *
		 * @since 4.9.0
		 * @member {string}
		 */
		templateId: 'customize-notification',

		/**
		 * Additional class names to add to the notification container.
		 *
		 * @since 4.9.0
		 * @member {string}
		 */
		containerClasses: '',

		/**
		 * Initialize notification.
		 *
		 * @since 4.9.0
		 *
		 * @param {string}   code                      Notification code.
		 * @param {Object}   params                    Notification parameters.
		 * @param {string}   params.message            Message.
		 * @param {string}   [params.type=error]       Type.
		 * @param {string}   [params.setting]          Related setting ID.
		 * @param {Function} [params.template]         Function for rendering template. If not provided, this will come from templateId.
		 * @param {string}   [params.templateId]       ID for template to render the notification.
		 * @param {string}   [params.containerClasses] Additional class names to add to the notification container.
		 * @param {boolean}  [params.dismissible]      Whether the notification can be dismissed.
		 */
		initialize: function( code, params ) {
			var _params;
			this.code = code;
			_params = _.extend(
				{
					message: null,
					type: 'error',
					fromServer: false,
					data: null,
					setting: null,
					template: null,
					dismissible: false,
					containerClasses: ''
				},
				params
			);
			delete _params.code;
			_.extend( this, _params );
		},

		/**
		 * Render the notification.
		 *
		 * @since 4.9.0
		 *
		 * @return {JQuery} Notification container element.
		 */
		render: function() {
			var notification = this, container, data;
			if ( ! notification.template ) {
				notification.template = wp.template( notification.templateId );
			}
			data = _.extend( {}, notification, {
				alt: notification.parent && notification.parent.alt
			} );
			container = $( notification.template( data ) );

			if ( notification.dismissible ) {
				container.find( '.notice-dismiss' ).on( 'click keydown', function( event ) {
					if ( 'keydown' === event.type && 13 !== event.which ) {
						return;
					}

					if ( notification.parent ) {
						notification.parent.remove( notification.code );
					} else {
						container.remove();
					}
				});
			}

			return container;
		}
	});

	// The main API object is also a collection of all customizer settings.
	api = $.extend( new api.Values(), api );

	/**
	 * Get all customize settings.
	 *
	 * @alias wp.customize.get
	 *
	 * @return {Object} All customize settings.
	 */
	api.get = function() {
		var result = {};

		this.each( function( obj, key ) {
			result[ key ] = obj.get();
		});

		return result;
	};

	/**
	 * Utility function namespace
	 *
	 * @namespace wp.customize.utils
	 */
	api.utils = {};

	/**
	 * Parse query string.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @alias wp.customize.utils.parseQueryString
	 *
	 * @param {string} queryString Query string.
	 * @return {Object} Parsed query string.
	 */
	api.utils.parseQueryString = function parseQueryString( queryString ) {
		var queryParams = {};
		_.each( queryString.split( '&' ), function( pair ) {
			var parts, key, value;
			parts = pair.split( '=', 2 );
			if ( ! parts[0] ) {
				return;
			}
			key = decodeURIComponent( parts[0].replace( /\+/g, ' ' ) );
			key = key.replace( / /g, '_' ); // What PHP does.
			if ( _.isUndefined( parts[1] ) ) {
				value = null;
			} else {
				value = decodeURIComponent( parts[1].replace( /\+/g, ' ' ) );
			}
			queryParams[ key ] = value;
		} );
		return queryParams;
	};

	/**
	 * Expose the API publicly on window.wp.customize
	 *
	 * @namespace wp.customize
	 */
	wp.customize = api;
})( wp, jQuery );
