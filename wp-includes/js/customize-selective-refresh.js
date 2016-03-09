/* global jQuery, JSON, _customizePartialRefreshExports, console */

wp.customize.selectiveRefresh = ( function( $, api ) {
	'use strict';
	var self, Partial, Placement;

	self = {
		ready: $.Deferred(),
		data: {
			partials: {},
			renderQueryVar: '',
			l10n: {
				shiftClickToEdit: ''
			},
			refreshBuffer: 250
		},
		currentRequest: null
	};

	_.extend( self, api.Events );

	/**
	 * A Customizer Partial.
	 *
	 * A partial provides a rendering of one or more settings according to a template.
	 *
	 * @see PHP class WP_Customize_Partial.
	 *
	 * @class
	 * @augments wp.customize.Class
	 * @since 4.5.0
	 *
	 * @param {string} id                              Unique identifier for the control instance.
	 * @param {object} options                         Options hash for the control instance.
	 * @param {object} options.params
	 * @param {string} options.params.type             Type of partial (e.g. nav_menu, widget, etc)
	 * @param {string} options.params.selector         jQuery selector to find the container element in the page.
	 * @param {array}  options.params.settings         The IDs for the settings the partial relates to.
	 * @param {string} options.params.primarySetting   The ID for the primary setting the partial renders.
	 * @param {bool}   options.params.fallbackRefresh  Whether to refresh the entire preview in case of a partial refresh failure.
	 */
	Partial = self.Partial = api.Class.extend({

		id: null,

		 /**
		 * Constructor.
		 *
		 * @since 4.5.0
		 *
		 * @param {string} id - Partial ID.
		 * @param {Object} options
		 * @param {Object} options.params
		 */
		initialize: function( id, options ) {
			var partial = this;
			options = options || {};
			partial.id = id;

			partial.params = _.extend(
				{
					selector: null,
					settings: [],
					primarySetting: null,
					containerInclusive: false,
					fallbackRefresh: true // Note this needs to be false in a front-end editing context.
				},
				options.params || {}
			);

			partial.deferred = {};
			partial.deferred.ready = $.Deferred();

			partial.deferred.ready.done( function() {
				partial.ready();
			} );
		},

		/**
		 * Set up the partial.
		 *
		 * @since 4.5.0
		 */
		ready: function() {
			var partial = this;
			_.each( _.pluck( partial.placements(), 'container' ), function( container ) {
				$( container ).attr( 'title', self.data.l10n.shiftClickToEdit );
			} );
			$( document ).on( 'click', partial.params.selector, function( e ) {
				if ( ! e.shiftKey ) {
					return;
				}
				e.preventDefault();
				_.each( partial.placements(), function( placement ) {
					if ( $( placement.container ).is( e.currentTarget ) ) {
						partial.showControl();
					}
				} );
			} );
		},

		/**
		 * Find all placements for this partial int he document.
		 *
		 * @since 4.5.0
		 *
		 * @return {Array.<Placement>}
		 */
		placements: function() {
			var partial = this, selector;

			selector = partial.params.selector;
			if ( selector ) {
				selector += ', ';
			}
			selector += '[data-customize-partial-id="' + partial.id + '"]'; // @todo Consider injecting customize-partial-id-${id} classnames instead.

			return $( selector ).map( function() {
				var container = $( this ), context;

				context = container.data( 'customize-partial-placement-context' );
				if ( _.isString( context ) && '{' === context.substr( 0, 1 ) ) {
					throw new Error( 'context JSON parse error' );
				}

				return new Placement( {
					partial: partial,
					container: container,
					context: context
				} );
			} ).get();
		},

		/**
		 * Get list of setting IDs related to this partial.
		 *
		 * @since 4.5.0
		 *
		 * @return {String[]}
		 */
		settings: function() {
			var partial = this;
			if ( partial.params.settings && 0 !== partial.params.settings.length ) {
				return partial.params.settings;
			} else if ( partial.params.primarySetting ) {
				return [ partial.params.primarySetting ];
			} else {
				return [ partial.id ];
			}
		},

		/**
		 * Return whether the setting is related to the partial.
		 *
		 * @since 4.5.0
		 *
		 * @param {wp.customize.Value|string} setting  ID or object for setting.
		 * @return {boolean} Whether the setting is related to the partial.
		 */
		isRelatedSetting: function( setting /*... newValue, oldValue */ ) {
			var partial = this;
			if ( _.isString( setting ) ) {
				setting = api( setting );
			}
			if ( ! setting ) {
				return false;
			}
			return -1 !== _.indexOf( partial.settings(), setting.id );
		},

		/**
		 * Show the control to modify this partial's setting(s).
		 *
		 * This may be overridden for inline editing.
		 *
		 * @since 4.5.0
		 */
		showControl: function() {
			var partial = this, settingId = partial.params.primarySetting;
			if ( ! settingId ) {
				settingId = _.first( partial.settings() );
			}
			api.preview.send( 'focus-control-for-setting', settingId );
		},

		/**
		 * Prepare container for selective refresh.
		 *
		 * @since 4.5.0
		 *
		 * @param {Placement} placement
		 */
		preparePlacement: function( placement ) {
			$( placement.container ).addClass( 'customize-partial-refreshing' );
		},

		/**
		 * Reference to the pending promise returned from self.requestPartial().
		 *
		 * @since 4.5.0
		 * @private
		 */
		_pendingRefreshPromise: null,

		/**
		 * Request the new partial and render it into the placements.
		 *
		 * @since 4.5.0
		 *
		 * @this {wp.customize.selectiveRefresh.Partial}
		 * @return {jQuery.Promise}
		 */
		refresh: function() {
			var partial = this, refreshPromise;

			refreshPromise = self.requestPartial( partial );

			if ( ! partial._pendingRefreshPromise ) {
				_.each( partial.placements(), function( placement ) {
					partial.preparePlacement( placement );
				} );

				refreshPromise.done( function( placements ) {
					_.each( placements, function( placement ) {
						partial.renderContent( placement );
					} );
				} );

				refreshPromise.fail( function( data, placements ) {
					partial.fallback( data, placements );
				} );

				// Allow new request when this one finishes.
				partial._pendingRefreshPromise = refreshPromise;
				refreshPromise.always( function() {
					partial._pendingRefreshPromise = null;
				} );
			}

			return refreshPromise;
		},

		/**
		 * Apply the addedContent in the placement to the document.
		 *
		 * Note the placement object will have its container and removedNodes
		 * properties updated.
		 *
		 * @since 4.5.0
		 *
		 * @param {Placement}             placement
		 * @param {Element|jQuery}        [placement.container]  - This param will be empty if there was no element matching the selector.
		 * @param {string|object|boolean} placement.addedContent - Rendered HTML content, a data object for JS templates to render, or false if no render.
		 * @param {object}                [placement.context]    - Optional context information about the container.
		 * @returns {boolean} Whether the rendering was successful and the fallback was not invoked.
		 */
		renderContent: function( placement ) {
			var partial = this, content, newContainerElement, errorMessageElement;
			if ( ! placement.container ) {
				partial.fallback( new Error( 'no_container' ), [ placement ] );
				return false;
			}
			placement.container = $( placement.container );
			if ( false === placement.addedContent ) {
				partial.fallback( new Error( 'missing_render' ), [ placement ] );
				return false;
			}

			// Currently a subclass needs to override renderContent to handle partials returning data object.
			if ( ! _.isString( placement.addedContent ) ) {
				partial.fallback( new Error( 'non_string_content' ), [ placement ] );
				return false;
			}

			/* jshint ignore:start */
			self.orginalDocumentWrite = document.write;
			document.write = function() {
				throw new Error( self.data.l10n.badDocumentWrite );
			};
			/* jshint ignore:end */
			try {
				content = placement.addedContent;
				if ( wp.emoji && wp.emoji.parse && ! $.contains( document.head, placement.container[0] ) ) {
					content = wp.emoji.parse( content );
				}

				if ( partial.params.containerInclusive ) {

					// Note that content may be an empty string, and in this case jQuery will just remove the oldContainer
					newContainerElement = $( content );

					// Merge the new context on top of the old context.
					placement.context = _.extend(
						placement.context,
						newContainerElement.data( 'customize-partial-placement-context' ) || {}
					);
					newContainerElement.data( 'customize-partial-placement-context', placement.context );

					placement.removedNodes = placement.container;
					placement.container = newContainerElement;
					placement.removedNodes.replaceWith( placement.container );
					placement.container.attr( 'title', self.data.l10n.shiftClickToEdit );
				} else {
					placement.removedNodes = document.createDocumentFragment();
					while ( placement.container[0].firstChild ) {
						placement.removedNodes.appendChild( placement.container[0].firstChild );
					}

					placement.container.html( content );
				}

				placement.container.removeClass( 'customize-render-content-error' );
			} catch ( error ) {
				if ( 'undefined' !== typeof console && console.error ) {
					console.error( partial.id, error );
				}
			}
			/* jshint ignore:start */
			document.write = self.orginalDocumentWrite;
			self.orginalDocumentWrite = null;
			/* jshint ignore:end */

			placement.container.removeClass( 'customize-partial-refreshing' );

			// Prevent placement container from being being re-triggered as being rendered among nested partials.
			placement.container.data( 'customize-partial-content-rendered', true );

			/**
			 * Announce when a partial's placement has been rendered so that dynamic elements can be re-built.
			 */
			self.trigger( 'partial-content-rendered', placement );
			return true;
		},

		/**
		 * Handle fail to render partial.
		 *
		 * The first argument is either the failing jqXHR or an Error object, and the second argument is the array of containers.
		 *
		 * @since 4.5.0
		 */
		fallback: function() {
			var partial = this;
			if ( partial.params.fallbackRefresh ) {
				self.requestFullRefresh();
			}
		}
	} );

	/**
	 * A Placement for a Partial.
	 *
	 * A partial placement is the actual physical representation of a partial for a given context.
	 * It also may have information in relation to how a placement may have just changed.
	 * The placement is conceptually similar to a DOM Range or MutationRecord.
	 *
	 * @class
	 * @augments wp.customize.Class
	 * @since 4.5.0
	 */
	self.Placement = Placement = api.Class.extend({

		/**
		 * The partial with which the container is associated.
		 *
		 * @param {wp.customize.selectiveRefresh.Partial}
		 */
		partial: null,

		/**
		 * DOM element which contains the placement's contents.
		 *
		 * This will be null if the startNode and endNode do not point to the same
		 * DOM element, such as in the case of a sidebar partial.
		 * This container element itself will be replaced for partials that
		 * have containerInclusive param defined as true.
		 */
		container: null,

		/**
		 * DOM node for the initial boundary of the placement.
		 *
		 * This will normally be the same as endNode since most placements appear as elements.
		 * This is primarily useful for widget sidebars which do not have intrinsic containers, but
		 * for which an HTML comment is output before to mark the starting position.
		 */
		startNode: null,

		/**
		 * DOM node for the terminal boundary of the placement.
		 *
		 * This will normally be the same as startNode since most placements appear as elements.
		 * This is primarily useful for widget sidebars which do not have intrinsic containers, but
		 * for which an HTML comment is output before to mark the ending position.
		 */
		endNode: null,

		/**
		 * Context data.
		 *
		 * This provides information about the placement which is included in the request
		 * in order to render the partial properly.
		 *
		 * @param {object}
		 */
		context: null,

		/**
		 * The content for the partial when refreshed.
		 *
		 * @param {string}
		 */
		addedContent: null,

		/**
		 * DOM node(s) removed when the partial is refreshed.
		 *
		 * If the partial is containerInclusive, then the removedNodes will be
		 * the single Element that was the partial's former placement. If the
		 * partial is not containerInclusive, then the removedNodes will be a
		 * documentFragment containing the nodes removed.
		 *
		 * @param {Element|DocumentFragment}
		 */
		removedNodes: null,

		/**
		 * Constructor.
		 *
		 * @since 4.5.0
		 *
		 * @param {object}                   args
		 * @param {Partial}                  args.partial
		 * @param {jQuery|Element}           [args.container]
		 * @param {Node}                     [args.startNode]
		 * @param {Node}                     [args.endNode]
		 * @param {object}                   [args.context]
		 * @param {string}                   [args.addedContent]
		 * @param {jQuery|DocumentFragment}  [args.removedNodes]
		 */
		initialize: function( args ) {
			var placement = this;

			args = _.extend( {}, args || {} );
			if ( ! args.partial || ! args.partial.extended( Partial ) ) {
				throw new Error( 'Missing partial' );
			}
			args.context = args.context || {};
			if ( args.container ) {
				args.container = $( args.container );
			}

			_.extend( placement, args );
		}

	});

	/**
	 * Mapping of type names to Partial constructor subclasses.
	 *
	 * @since 4.5.0
	 *
	 * @type {Object.<string, wp.customize.selectiveRefresh.Partial>}
	 */
	self.partialConstructor = {};

	self.partial = new api.Values({ defaultConstructor: Partial });

	/**
	 * Get the POST vars for a Customizer preview request.
	 *
	 * @since 4.5.0
	 * @see wp.customize.previewer.query()
	 *
	 * @return {object}
	 */
	self.getCustomizeQuery = function() {
		var dirtyCustomized = {};
		api.each( function( value, key ) {
			if ( value._dirty ) {
				dirtyCustomized[ key ] = value();
			}
		} );

		return {
			wp_customize: 'on',
			nonce: api.settings.nonce.preview,
			theme: api.settings.theme.stylesheet,
			customized: JSON.stringify( dirtyCustomized )
		};
	};

	/**
	 * Currently-requested partials and their associated deferreds.
	 *
	 * @since 4.5.0
	 * @type {Object<string, { deferred: jQuery.Promise, partial: wp.customize.selectiveRefresh.Partial }>}
	 */
	self._pendingPartialRequests = {};

	/**
	 * Timeout ID for the current requesr, or null if no request is current.
	 *
	 * @since 4.5.0
	 * @type {number|null}
	 * @private
	 */
	self._debouncedTimeoutId = null;

	/**
	 * Current jqXHR for the request to the partials.
	 *
	 * @since 4.5.0
	 * @type {jQuery.jqXHR|null}
	 * @private
	 */
	self._currentRequest = null;

	/**
	 * Request full page refresh.
	 *
	 * When selective refresh is embedded in the context of front-end editing, this request
	 * must fail or else changes will be lost, unless transactions are implemented.
	 *
	 * @since 4.5.0
	 */
	self.requestFullRefresh = function() {
		api.preview.send( 'refresh' );
	};

	/**
	 * Request a re-rendering of a partial.
	 *
	 * @since 4.5.0
	 *
	 * @param {wp.customize.selectiveRefresh.Partial} partial
	 * @return {jQuery.Promise}
	 */
	self.requestPartial = function( partial ) {
		var partialRequest;

		if ( self._debouncedTimeoutId ) {
			clearTimeout( self._debouncedTimeoutId );
			self._debouncedTimeoutId = null;
		}
		if ( self._currentRequest ) {
			self._currentRequest.abort();
			self._currentRequest = null;
		}

		partialRequest = self._pendingPartialRequests[ partial.id ];
		if ( ! partialRequest || 'pending' !== partialRequest.deferred.state() ) {
			partialRequest = {
				deferred: $.Deferred(),
				partial: partial
			};
			self._pendingPartialRequests[ partial.id ] = partialRequest;
		}

		// Prevent leaking partial into debounced timeout callback.
		partial = null;

		self._debouncedTimeoutId = setTimeout(
			function() {
				var data, partialPlacementContexts, partialsPlacements, request;

				self._debouncedTimeoutId = null;
				data = self.getCustomizeQuery();

				/*
				 * It is key that the containers be fetched exactly at the point of the request being
				 * made, because the containers need to be mapped to responses by array indices.
				 */
				partialsPlacements = {};

				partialPlacementContexts = {};

				_.each( self._pendingPartialRequests, function( pending, partialId ) {
					partialsPlacements[ partialId ] = pending.partial.placements();
					if ( ! self.partial.has( partialId ) ) {
						pending.deferred.rejectWith( pending.partial, [ new Error( 'partial_removed' ), partialsPlacements[ partialId ] ] );
					} else {
						/*
						 * Note that this may in fact be an empty array. In that case, it is the responsibility
						 * of the Partial subclass instance to know where to inject the response, or else to
						 * just issue a refresh (default behavior). The data being returned with each container
						 * is the context information that may be needed to render certain partials, such as
						 * the contained sidebar for rendering widgets or what the nav menu args are for a menu.
						 */
						partialPlacementContexts[ partialId ] = _.map( partialsPlacements[ partialId ], function( placement ) {
							return placement.context || {};
						} );
					}
				} );

				data.partials = JSON.stringify( partialPlacementContexts );
				data[ self.data.renderQueryVar ] = '1';

				request = self._currentRequest = wp.ajax.send( null, {
					data: data,
					url: api.settings.url.self
				} );

				request.done( function( data ) {

					/**
					 * Announce the data returned from a request to render partials.
					 *
					 * The data is filtered on the server via customize_render_partials_response
					 * so plugins can inject data from the server to be utilized
					 * on the client via this event. Plugins may use this filter
					 * to communicate script and style dependencies that need to get
					 * injected into the page to support the rendered partials.
					 * This is similar to the 'saved' event.
					 */
					self.trigger( 'render-partials-response', data );

					// Relay errors (warnings) captured during rendering and relay to console.
					if ( data.errors && 'undefined' !== typeof console && console.warn ) {
						_.each( data.errors, function( error ) {
							console.warn( error );
						} );
					}

					/*
					 * Note that data is an array of items that correspond to the array of
					 * containers that were submitted in the request. So we zip up the
					 * array of containers with the array of contents for those containers,
					 * and send them into .
					 */
					_.each( self._pendingPartialRequests, function( pending, partialId ) {
						var placementsContents;
						if ( ! _.isArray( data.contents[ partialId ] ) ) {
							pending.deferred.rejectWith( pending.partial, [ new Error( 'unrecognized_partial' ), partialsPlacements[ partialId ] ] );
						} else {
							placementsContents = _.map( data.contents[ partialId ], function( content, i ) {
								var partialPlacement = partialsPlacements[ partialId ][ i ];
								if ( partialPlacement ) {
									partialPlacement.addedContent = content;
								} else {
									partialPlacement = new Placement( {
										partial: pending.partial,
										addedContent: content
									} );
								}
								return partialPlacement;
							} );
							pending.deferred.resolveWith( pending.partial, [ placementsContents ] );
						}
					} );
					self._pendingPartialRequests = {};
				} );

				request.fail( function( data, statusText ) {

					/*
					 * Ignore failures caused by partial.currentRequest.abort()
					 * The pending deferreds will remain in self._pendingPartialRequests
					 * for re-use with the next request.
					 */
					if ( 'abort' === statusText ) {
						return;
					}

					_.each( self._pendingPartialRequests, function( pending, partialId ) {
						pending.deferred.rejectWith( pending.partial, [ data, partialsPlacements[ partialId ] ] );
					} );
					self._pendingPartialRequests = {};
				} );
			},
			self.data.refreshBuffer
		);

		return partialRequest.deferred.promise();
	};

	/**
	 * Add partials for any nav menu container elements in the document.
	 *
	 * This method may be called multiple times. Containers that already have been
	 * seen will be skipped.
	 *
	 * @since 4.5.0
	 *
	 * @param {jQuery|HTMLElement} [rootElement]
	 * @param {object}             [options]
	 * @param {boolean=true}       [options.triggerRendered]
	 */
	self.addPartials = function( rootElement, options ) {
		var containerElements;
		if ( ! rootElement ) {
			rootElement = document.documentElement;
		}
		rootElement = $( rootElement );
		options = _.extend(
			{
				triggerRendered: true
			},
			options || {}
		);

		containerElements = rootElement.find( '[data-customize-partial-id]' );
		if ( rootElement.is( '[data-customize-partial-id]' ) ) {
			containerElements = containerElements.add( rootElement );
		}
		containerElements.each( function() {
			var containerElement = $( this ), partial, id, Constructor, partialOptions, containerContext;
			id = containerElement.data( 'customize-partial-id' );
			if ( ! id ) {
				return;
			}
			containerContext = containerElement.data( 'customize-partial-placement-context' ) || {};

			partial = self.partial( id );
			if ( ! partial ) {
				partialOptions = containerElement.data( 'customize-partial-options' ) || {};
				partialOptions.constructingContainerContext = containerElement.data( 'customize-partial-placement-context' ) || {};
				Constructor = self.partialConstructor[ containerElement.data( 'customize-partial-type' ) ] || self.Partial;
				partial = new Constructor( id, partialOptions );
				self.partial.add( partial.id, partial );
			}

			/*
			 * Only trigger renders on (nested) partials that have been not been
			 * handled yet. An example where this would apply is a nav menu
			 * embedded inside of a custom menu widget. When the widget's title
			 * is updated, the entire widget will re-render and then the event
			 * will be triggered for the nested nav menu to do any initialization.
			 */
			if ( options.triggerRendered && ! containerElement.data( 'customize-partial-content-rendered' ) ) {

				/**
				 * Announce when a partial's nested placement has been re-rendered.
				 */
				self.trigger( 'partial-content-rendered', new Placement( {
					partial: partial,
					context: containerContext,
					container: containerElement
				} ) );
			}
			containerElement.data( 'customize-partial-content-rendered', true );
		} );
	};

	api.bind( 'preview-ready', function() {
		var handleSettingChange, watchSettingChange, unwatchSettingChange;

		// Polyfill for IE8 to support the document.head attribute.
		if ( ! document.head ) {
			document.head = $( 'head:first' )[0];
		}

		_.extend( self.data, _customizePartialRefreshExports );

		// Create the partial JS models.
		_.each( self.data.partials, function( data, id ) {
			var Constructor, partial = self.partial( id );
			if ( ! partial ) {
				Constructor = self.partialConstructor[ data.type ] || self.Partial;
				partial = new Constructor( id, { params: data } );
				self.partial.add( id, partial );
			} else {
				_.extend( partial.params, data );
			}
		} );

		/**
		 * Handle change to a setting.
		 *
		 * Note this is largely needed because adding a 'change' event handler to wp.customize
		 * will only include the changed setting object as an argument, not including the
		 * new value or the old value.
		 *
		 * @since 4.5.0
		 * @this {wp.customize.Setting}
		 *
		 * @param {*|null} newValue New value, or null if the setting was just removed.
		 * @param {*|null} oldValue Old value, or null if the setting was just added.
		 */
		handleSettingChange = function( newValue, oldValue ) {
			var setting = this;
			self.partial.each( function( partial ) {
				if ( partial.isRelatedSetting( setting, newValue, oldValue ) ) {
					partial.refresh();
				}
			} );
		};

		/**
		 * Trigger the initial change for the added setting, and watch for changes.
		 *
		 * @since 4.5.0
		 * @this {wp.customize.Values}
		 *
		 * @param {wp.customize.Setting} setting
		 */
		watchSettingChange = function( setting ) {
			handleSettingChange.call( setting, setting(), null );
			setting.bind( handleSettingChange );
		};

		/**
		 * Trigger the final change for the removed setting, and unwatch for changes.
		 *
		 * @since 4.5.0
		 * @this {wp.customize.Values}
		 *
		 * @param {wp.customize.Setting} setting
		 */
		unwatchSettingChange = function( setting ) {
			handleSettingChange.call( setting, null, setting() );
			setting.unbind( handleSettingChange );
		};

		api.bind( 'add', watchSettingChange );
		api.bind( 'remove', unwatchSettingChange );
		api.each( function( setting ) {
			setting.bind( handleSettingChange );
		} );

		// Add (dynamic) initial partials that are declared via data-* attributes.
		self.addPartials( document.documentElement, {
			triggerRendered: false
		} );

		// Add new dynamic partials when the document changes.
		if ( 'undefined' !== typeof MutationObserver ) {
			self.mutationObserver = new MutationObserver( function( mutations ) {
				_.each( mutations, function( mutation ) {
					self.addPartials( $( mutation.target ) );
				} );
			} );
			self.mutationObserver.observe( document.documentElement, {
				childList: true,
				subtree: true
			} );
		}

		/**
		 * Handle rendering of partials.
		 *
		 * @param {api.selectiveRefresh.Placement} placement
		 */
		api.selectiveRefresh.bind( 'partial-content-rendered', function( placement ) {
			if ( placement.container ) {
				self.addPartials( placement.container );
			}
		} );

		api.preview.bind( 'active', function() {

			// Make all partials ready.
			self.partial.each( function( partial ) {
				partial.deferred.ready.resolve();
			} );

			// Make all partials added henceforth as ready upon add.
			self.partial.bind( 'add', function( partial ) {
				partial.deferred.ready.resolve();
			} );
		} );

	} );

	return self;
}( jQuery, wp.customize ) );
