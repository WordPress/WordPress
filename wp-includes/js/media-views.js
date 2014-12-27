/* global _wpMediaViewsL10n, confirm, getUserSetting, setUserSetting */
( function( $, _ ) {
	var l10n,
		media = wp.media,
		isTouchDevice = ( 'ontouchend' in document );

	// Link any localized strings.
	l10n = media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;

	// Link any settings.
	media.view.settings = l10n.settings || {};
	delete l10n.settings;

	// Copy the `post` setting over to the model settings.
	media.model.settings.post = media.view.settings.post;

	// Check if the browser supports CSS 3.0 transitions
	$.support.transition = (function(){
		var style = document.documentElement.style,
			transitions = {
				WebkitTransition: 'webkitTransitionEnd',
				MozTransition:    'transitionend',
				OTransition:      'oTransitionEnd otransitionend',
				transition:       'transitionend'
			}, transition;

		transition = _.find( _.keys( transitions ), function( transition ) {
			return ! _.isUndefined( style[ transition ] );
		});

		return transition && {
			end: transitions[ transition ]
		};
	}());

	/**
	 * A shared event bus used to provide events into
	 * the media workflows that 3rd-party devs can use to hook
	 * in.
	 */
	media.events = _.extend( {}, Backbone.Events );

	/**
	 * Makes it easier to bind events using transitions.
	 *
	 * @param {string} selector
	 * @param {Number} sensitivity
	 * @returns {Promise}
	 */
	media.transition = function( selector, sensitivity ) {
		var deferred = $.Deferred();

		sensitivity = sensitivity || 2000;

		if ( $.support.transition ) {
			if ( ! (selector instanceof $) ) {
				selector = $( selector );
			}

			// Resolve the deferred when the first element finishes animating.
			selector.first().one( $.support.transition.end, deferred.resolve );

			// Just in case the event doesn't trigger, fire a callback.
			_.delay( deferred.resolve, sensitivity );

		// Otherwise, execute on the spot.
		} else {
			deferred.resolve();
		}

		return deferred.promise();
	};

	/**
	 * wp.media.controller.Region
	 *
	 * A region is a persistent application layout area.
	 *
	 * A region assumes one mode at any time, and can be switched to another.
	 *
	 * When mode changes, events are triggered on the region's parent view.
	 * The parent view will listen to specific events and fill the region with an
	 * appropriate view depending on mode. For example, a frame listens for the
	 * 'browse' mode t be activated on the 'content' view and then fills the region
	 * with an AttachmentsBrowser view.
	 *
	 * @class
	 *
	 * @param {object}        options          Options hash for the region.
	 * @param {string}        options.id       Unique identifier for the region.
	 * @param {Backbone.View} options.view     A parent view the region exists within.
	 * @param {string}        options.selector jQuery selector for the region within the parent view.
	 */
	media.controller.Region = function( options ) {
		_.extend( this, _.pick( options || {}, 'id', 'view', 'selector' ) );
	};

	// Use Backbone's self-propagating `extend` inheritance method.
	media.controller.Region.extend = Backbone.Model.extend;

	_.extend( media.controller.Region.prototype, {
		/**
		 * Activate a mode.
		 *
		 * @since 3.5.0
		 *
		 * @param {string} mode
		 *
		 * @fires this.view#{this.id}:activate:{this._mode}
		 * @fires this.view#{this.id}:activate
		 * @fires this.view#{this.id}:deactivate:{this._mode}
		 * @fires this.view#{this.id}:deactivate
		 *
		 * @returns {wp.media.controller.Region} Returns itself to allow chaining.
		 */
		mode: function( mode ) {
			if ( ! mode ) {
				return this._mode;
			}
			// Bail if we're trying to change to the current mode.
			if ( mode === this._mode ) {
				return this;
			}

			/**
			 * Region mode deactivation event.
			 *
			 * @event this.view#{this.id}:deactivate:{this._mode}
			 * @event this.view#{this.id}:deactivate
			 */
			this.trigger('deactivate');

			this._mode = mode;
			this.render( mode );

			/**
			 * Region mode activation event.
			 *
			 * @event this.view#{this.id}:activate:{this._mode}
			 * @event this.view#{this.id}:activate
			 */
			this.trigger('activate');
			return this;
		},
		/**
		 * Render a mode.
		 *
		 * @since 3.5.0
		 *
		 * @param {string} mode
		 *
		 * @fires this.view#{this.id}:create:{this._mode}
		 * @fires this.view#{this.id}:create
		 * @fires this.view#{this.id}:render:{this._mode}
		 * @fires this.view#{this.id}:render
		 *
		 * @returns {wp.media.controller.Region} Returns itself to allow chaining
		 */
		render: function( mode ) {
			// If the mode isn't active, activate it.
			if ( mode && mode !== this._mode ) {
				return this.mode( mode );
			}

			var set = { view: null },
				view;

			/**
			 * Create region view event.
			 *
			 * Region view creation takes place in an event callback on the frame.
			 *
			 * @event this.view#{this.id}:create:{this._mode}
			 * @event this.view#{this.id}:create
			 */
			this.trigger( 'create', set );
			view = set.view;

			/**
			 * Render region view event.
			 *
			 * Region view creation takes place in an event callback on the frame.
			 *
			 * @event this.view#{this.id}:create:{this._mode}
			 * @event this.view#{this.id}:create
			 */
			this.trigger( 'render', view );
			if ( view ) {
				this.set( view );
			}
			return this;
		},

		/**
		 * Get the region's view.
		 *
		 * @since 3.5.0
		 *
		 * @returns {wp.media.View}
		 */
		get: function() {
			return this.view.views.first( this.selector );
		},

		/**
		 * Set the region's view as a subview of the frame.
		 *
		 * @since 3.5.0
		 *
		 * @param {Array|Object} views
		 * @param {Object} [options={}]
		 * @returns {wp.Backbone.Subviews} Subviews is returned to allow chaining
		 */
		set: function( views, options ) {
			if ( options ) {
				options.add = false;
			}
			return this.view.views.set( this.selector, views, options );
		},

		/**
		 * Trigger regional view events on the frame.
		 *
		 * @since 3.5.0
		 *
		 * @param {string} event
		 * @returns {undefined|wp.media.controller.Region} Returns itself to allow chaining.
		 */
		trigger: function( event ) {
			var base, args;

			if ( ! this._mode ) {
				return;
			}

			args = _.toArray( arguments );
			base = this.id + ':' + event;

			// Trigger `{this.id}:{event}:{this._mode}` event on the frame.
			args[0] = base + ':' + this._mode;
			this.view.trigger.apply( this.view, args );

			// Trigger `{this.id}:{event}` event on the frame.
			args[0] = base;
			this.view.trigger.apply( this.view, args );
			return this;
		}
	});

	/**
	 * wp.media.controller.StateMachine
	 *
	 * A state machine keeps track of state. It is in one state at a time,
	 * and can change from one state to another.
	 *
	 * States are stored as models in a Backbone collection.
	 *
	 * @since 3.5.0
	 *
	 * @class
	 * @augments Backbone.Model
	 * @mixin
	 * @mixes Backbone.Events
	 *
	 * @param {Array} states
	 */
	media.controller.StateMachine = function( states ) {
		// @todo This is dead code. The states collection gets created in media.view.Frame._createStates.
		this.states = new Backbone.Collection( states );
	};

	// Use Backbone's self-propagating `extend` inheritance method.
	media.controller.StateMachine.extend = Backbone.Model.extend;

	_.extend( media.controller.StateMachine.prototype, Backbone.Events, {
		/**
		 * Fetch a state.
		 *
		 * If no `id` is provided, returns the active state.
		 *
		 * Implicitly creates states.
		 *
		 * Ensure that the `states` collection exists so the `StateMachine`
		 *   can be used as a mixin.
		 *
		 * @since 3.5.0
		 *
		 * @param {string} id
		 * @returns {wp.media.controller.State} Returns a State model
		 *   from the StateMachine collection
		 */
		state: function( id ) {
			this.states = this.states || new Backbone.Collection();

			// Default to the active state.
			id = id || this._state;

			if ( id && ! this.states.get( id ) ) {
				this.states.add({ id: id });
			}
			return this.states.get( id );
		},

		/**
		 * Sets the active state.
		 *
		 * Bail if we're trying to select the current state, if we haven't
		 * created the `states` collection, or are trying to select a state
		 * that does not exist.
		 *
		 * @since 3.5.0
		 *
		 * @param {string} id
		 *
		 * @fires wp.media.controller.State#deactivate
		 * @fires wp.media.controller.State#activate
		 *
		 * @returns {wp.media.controller.StateMachine} Returns itself to allow chaining
		 */
		setState: function( id ) {
			var previous = this.state();

			if ( ( previous && id === previous.id ) || ! this.states || ! this.states.get( id ) ) {
				return this;
			}

			if ( previous ) {
				previous.trigger('deactivate');
				this._lastState = previous.id;
			}

			this._state = id;
			this.state().trigger('activate');

			return this;
		},

		/**
		 * Returns the previous active state.
		 *
		 * Call the `state()` method with no parameters to retrieve the current
		 * active state.
		 *
		 * @since 3.5.0
		 *
		 * @returns {wp.media.controller.State} Returns a State model
		 *    from the StateMachine collection
		 */
		lastState: function() {
			if ( this._lastState ) {
				return this.state( this._lastState );
			}
		}
	});

	// Map all event binding and triggering on a StateMachine to its `states` collection.
	_.each([ 'on', 'off', 'trigger' ], function( method ) {
		/**
		 * @returns {wp.media.controller.StateMachine} Returns itself to allow chaining.
		 */
		media.controller.StateMachine.prototype[ method ] = function() {
			// Ensure that the `states` collection exists so the `StateMachine`
			// can be used as a mixin.
			this.states = this.states || new Backbone.Collection();
			// Forward the method to the `states` collection.
			this.states[ method ].apply( this.states, arguments );
			return this;
		};
	});

	/**
	 * wp.media.controller.State
	 *
	 * A state is a step in a workflow that when set will trigger the controllers
	 * for the regions to be updated as specified in the frame.
	 *
	 * A state has an event-driven lifecycle:
	 *
	 *     'ready'      triggers when a state is added to a state machine's collection.
	 *     'activate'   triggers when a state is activated by a state machine.
	 *     'deactivate' triggers when a state is deactivated by a state machine.
	 *     'reset'      is not triggered automatically. It should be invoked by the
	 *                  proper controller to reset the state to its default.
	 *
	 * @class
	 * @augments Backbone.Model
	 */
	media.controller.State = Backbone.Model.extend({
		/**
		 * Constructor.
		 *
		 * @since 3.5.0
		 */
		constructor: function() {
			this.on( 'activate', this._preActivate, this );
			this.on( 'activate', this.activate, this );
			this.on( 'activate', this._postActivate, this );
			this.on( 'deactivate', this._deactivate, this );
			this.on( 'deactivate', this.deactivate, this );
			this.on( 'reset', this.reset, this );
			this.on( 'ready', this._ready, this );
			this.on( 'ready', this.ready, this );
			/**
			 * Call parent constructor with passed arguments
			 */
			Backbone.Model.apply( this, arguments );
			this.on( 'change:menu', this._updateMenu, this );
		},
		/**
		 * Ready event callback.
		 *
		 * @abstract
		 * @since 3.5.0
		 */
		ready: function() {},

		/**
		 * Activate event callback.
		 *
		 * @abstract
		 * @since 3.5.0
		 */
		activate: function() {},

		/**
		 * Deactivate event callback.
		 *
		 * @abstract
		 * @since 3.5.0
		 */
		deactivate: function() {},

		/**
		 * Reset event callback.
		 *
		 * @abstract
		 * @since 3.5.0
		 */
		reset: function() {},

		/**
		 * @access private
		 * @since 3.5.0
		 */
		_ready: function() {
			this._updateMenu();
		},

		/**
		 * @access private
		 * @since 3.5.0
		*/
		_preActivate: function() {
			this.active = true;
		},

		/**
		 * @access private
		 * @since 3.5.0
		 */
		_postActivate: function() {
			this.on( 'change:menu', this._menu, this );
			this.on( 'change:titleMode', this._title, this );
			this.on( 'change:content', this._content, this );
			this.on( 'change:toolbar', this._toolbar, this );

			this.frame.on( 'title:render:default', this._renderTitle, this );

			this._title();
			this._menu();
			this._toolbar();
			this._content();
			this._router();
		},

		/**
		 * @access private
		 * @since 3.5.0
		 */
		_deactivate: function() {
			this.active = false;

			this.frame.off( 'title:render:default', this._renderTitle, this );

			this.off( 'change:menu', this._menu, this );
			this.off( 'change:titleMode', this._title, this );
			this.off( 'change:content', this._content, this );
			this.off( 'change:toolbar', this._toolbar, this );
		},

		/**
		 * @access private
		 * @since 3.5.0
		 */
		_title: function() {
			this.frame.title.render( this.get('titleMode') || 'default' );
		},

		/**
		 * @access private
		 * @since 3.5.0
		 */
		_renderTitle: function( view ) {
			view.$el.text( this.get('title') || '' );
		},

		/**
		 * @access private
		 * @since 3.5.0
		 */
		_router: function() {
			var router = this.frame.router,
				mode = this.get('router'),
				view;

			this.frame.$el.toggleClass( 'hide-router', ! mode );
			if ( ! mode ) {
				return;
			}

			this.frame.router.render( mode );

			view = router.get();
			if ( view && view.select ) {
				view.select( this.frame.content.mode() );
			}
		},

		/**
		 * @access private
		 * @since 3.5.0
		 */
		_menu: function() {
			var menu = this.frame.menu,
				mode = this.get('menu'),
				view;

			this.frame.$el.toggleClass( 'hide-menu', ! mode );
			if ( ! mode ) {
				return;
			}

			menu.mode( mode );

			view = menu.get();
			if ( view && view.select ) {
				view.select( this.id );
			}
		},

		/**
		 * @access private
		 * @since 3.5.0
		 */
		_updateMenu: function() {
			var previous = this.previous('menu'),
				menu = this.get('menu');

			if ( previous ) {
				this.frame.off( 'menu:render:' + previous, this._renderMenu, this );
			}

			if ( menu ) {
				this.frame.on( 'menu:render:' + menu, this._renderMenu, this );
			}
		},

		/**
		 * Create a view in the media menu for the state.
		 *
		 * @access private
		 * @since 3.5.0
		 *
		 * @param {media.view.Menu} view The menu view.
		 */
		_renderMenu: function( view ) {
			var menuItem = this.get('menuItem'),
				title = this.get('title'),
				priority = this.get('priority');

			if ( ! menuItem && title ) {
				menuItem = { text: title };

				if ( priority ) {
					menuItem.priority = priority;
				}
			}

			if ( ! menuItem ) {
				return;
			}

			view.set( this.id, menuItem );
		}
	});

	_.each(['toolbar','content'], function( region ) {
		/**
		 * @access private
		 */
		media.controller.State.prototype[ '_' + region ] = function() {
			var mode = this.get( region );
			if ( mode ) {
				this.frame[ region ].render( mode );
			}
		};
	});

	/**
	 * wp.media.selectionSync
	 *
	 * Sync an attachments selection in a state with another state.
	 *
	 * Allows for selecting multiple images in the Insert Media workflow, and then
	 * switching to the Insert Gallery workflow while preserving the attachments selection.
	 *
	 * @mixin
	 */
	media.selectionSync = {
		/**
		 * @since 3.5.0
		 */
		syncSelection: function() {
			var selection = this.get('selection'),
				manager = this.frame._selection;

			if ( ! this.get('syncSelection') || ! manager || ! selection ) {
				return;
			}

			// If the selection supports multiple items, validate the stored
			// attachments based on the new selection's conditions. Record
			// the attachments that are not included; we'll maintain a
			// reference to those. Other attachments are considered in flux.
			if ( selection.multiple ) {
				selection.reset( [], { silent: true });
				selection.validateAll( manager.attachments );
				manager.difference = _.difference( manager.attachments.models, selection.models );
			}

			// Sync the selection's single item with the master.
			selection.single( manager.single );
		},

		/**
		 * Record the currently active attachments, which is a combination
		 * of the selection's attachments and the set of selected
		 * attachments that this specific selection considered invalid.
		 * Reset the difference and record the single attachment.
		 *
		 * @since 3.5.0
		 */
		recordSelection: function() {
			var selection = this.get('selection'),
				manager = this.frame._selection;

			if ( ! this.get('syncSelection') || ! manager || ! selection ) {
				return;
			}

			if ( selection.multiple ) {
				manager.attachments.reset( selection.toArray().concat( manager.difference ) );
				manager.difference = [];
			} else {
				manager.attachments.add( selection.toArray() );
			}

			manager.single = selection._single;
		}
	};

	/**
	 * wp.media.controller.Library
	 *
	 * A state for choosing an attachment or group of attachments from the media library.
	 *
	 * @class
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 * @mixes media.selectionSync
	 *
	 * @param {object}                          [attributes]                         The attributes hash passed to the state.
	 * @param {string}                          [attributes.id=library]              Unique identifier.
	 * @param {string}                          [attributes.title=Media library]     Title for the state. Displays in the media menu and the frame's title region.
	 * @param {wp.media.model.Attachments}      [attributes.library]                 The attachments collection to browse.
	 *                                                                               If one is not supplied, a collection of all attachments will be created.
	 * @param {wp.media.model.Selection|object} [attributes.selection]               A collection to contain attachment selections within the state.
	 *                                                                               If the 'selection' attribute is a plain JS object,
	 *                                                                               a Selection will be created using its values as the selection instance's `props` model.
	 *                                                                               Otherwise, it will copy the library's `props` model.
	 * @param {boolean}                         [attributes.multiple=false]          Whether multi-select is enabled.
	 * @param {string}                          [attributes.content=upload]          Initial mode for the content region.
	 *                                                                               Overridden by persistent user setting if 'contentUserSetting' is true.
	 * @param {string}                          [attributes.menu=default]            Initial mode for the menu region.
	 * @param {string}                          [attributes.router=browse]           Initial mode for the router region.
	 * @param {string}                          [attributes.toolbar=select]          Initial mode for the toolbar region.
	 * @param {boolean}                         [attributes.searchable=true]         Whether the library is searchable.
	 * @param {boolean|string}                  [attributes.filterable=false]        Whether the library is filterable, and if so what filters should be shown.
	 *                                                                               Accepts 'all', 'uploaded', or 'unattached'.
	 * @param {boolean}                         [attributes.sortable=true]           Whether the Attachments should be sortable. Depends on the orderby property being set to menuOrder on the attachments collection.
	 * @param {boolean}                         [attributes.autoSelect=true]         Whether an uploaded attachment should be automatically added to the selection.
	 * @param {boolean}                         [attributes.describe=false]          Whether to offer UI to describe attachments - e.g. captioning images in a gallery.
	 * @param {boolean}                         [attributes.contentUserSetting=true] Whether the content region's mode should be set and persisted per user.
	 * @param {boolean}                         [attributes.syncSelection=true]      Whether the Attachments selection should be persisted from the last state.
	 */
	media.controller.Library = media.controller.State.extend({
		defaults: {
			id:                 'library',
			title:              l10n.mediaLibraryTitle,
			multiple:           false,
			content:            'upload',
			menu:               'default',
			router:             'browse',
			toolbar:            'select',
			searchable:         true,
			filterable:         false,
			sortable:           true,
			autoSelect:         true,
			describe:           false,
			contentUserSetting: true,
			syncSelection:      true
		},

		/**
		 * If a library isn't provided, query all media items.
		 * If a selection instance isn't provided, create one.
		 *
		 * @since 3.5.0
		 */
		initialize: function() {
			var selection = this.get('selection'),
				props;

			if ( ! this.get('library') ) {
				this.set( 'library', media.query() );
			}

			if ( ! (selection instanceof media.model.Selection) ) {
				props = selection;

				if ( ! props ) {
					props = this.get('library').props.toJSON();
					props = _.omit( props, 'orderby', 'query' );
				}

				this.set( 'selection', new media.model.Selection( null, {
					multiple: this.get('multiple'),
					props: props
				}) );
			}

			this.resetDisplays();
		},

		/**
		 * @since 3.5.0
		 */
		activate: function() {
			this.syncSelection();

			wp.Uploader.queue.on( 'add', this.uploading, this );

			this.get('selection').on( 'add remove reset', this.refreshContent, this );

			if ( this.get( 'router' ) && this.get('contentUserSetting') ) {
				this.frame.on( 'content:activate', this.saveContentMode, this );
				this.set( 'content', getUserSetting( 'libraryContent', this.get('content') ) );
			}
		},

		/**
		 * @since 3.5.0
		 */
		deactivate: function() {
			this.recordSelection();

			this.frame.off( 'content:activate', this.saveContentMode, this );

			// Unbind all event handlers that use this state as the context
			// from the selection.
			this.get('selection').off( null, null, this );

			wp.Uploader.queue.off( null, null, this );
		},

		/**
		 * Reset the library to its initial state.
		 *
		 * @since 3.5.0
		 */
		reset: function() {
			this.get('selection').reset();
			this.resetDisplays();
			this.refreshContent();
		},

		/**
		 * Reset the attachment display settings defaults to the site options.
		 *
		 * If site options don't define them, fall back to a persistent user setting.
		 *
		 * @since 3.5.0
		 */
		resetDisplays: function() {
			var defaultProps = media.view.settings.defaultProps;
			this._displays = [];
			this._defaultDisplaySettings = {
				align: defaultProps.align || getUserSetting( 'align', 'none' ),
				size:  defaultProps.size  || getUserSetting( 'imgsize', 'medium' ),
				link:  defaultProps.link  || getUserSetting( 'urlbutton', 'file' )
			};
		},

		/**
		 * Create a model to represent display settings (alignment, etc.) for an attachment.
		 *
		 * @since 3.5.0
		 *
		 * @param {wp.media.model.Attachment} attachment
		 * @returns {Backbone.Model}
		 */
		display: function( attachment ) {
			var displays = this._displays;

			if ( ! displays[ attachment.cid ] ) {
				displays[ attachment.cid ] = new Backbone.Model( this.defaultDisplaySettings( attachment ) );
			}
			return displays[ attachment.cid ];
		},

		/**
		 * Given an attachment, create attachment display settings properties.
		 *
		 * @since 3.6.0
		 *
		 * @param {wp.media.model.Attachment} attachment
		 * @returns {Object}
		 */
		defaultDisplaySettings: function( attachment ) {
			var settings = this._defaultDisplaySettings;
			if ( settings.canEmbed = this.canEmbed( attachment ) ) {
				settings.link = 'embed';
			}
			return settings;
		},

		/**
		 * Whether an attachment can be embedded (audio or video).
		 *
		 * @since 3.6.0
		 *
		 * @param {wp.media.model.Attachment} attachment
		 * @returns {Boolean}
		 */
		canEmbed: function( attachment ) {
			// If uploading, we know the filename but not the mime type.
			if ( ! attachment.get('uploading') ) {
				var type = attachment.get('type');
				if ( type !== 'audio' && type !== 'video' ) {
					return false;
				}
			}

			return _.contains( media.view.settings.embedExts, attachment.get('filename').split('.').pop() );
		},


		/**
		 * If the state is active, no items are selected, and the current
		 * content mode is not an option in the state's router (provided
		 * the state has a router), reset the content mode to the default.
		 *
		 * @since 3.5.0
		 */
		refreshContent: function() {
			var selection = this.get('selection'),
				frame = this.frame,
				router = frame.router.get(),
				mode = frame.content.mode();

			if ( this.active && ! selection.length && router && ! router.get( mode ) ) {
				this.frame.content.render( this.get('content') );
			}
		},

		/**
		 * Callback handler when an attachment is uploaded.
		 *
		 * Switch to the Media Library if uploaded from the 'Upload Files' tab.
		 *
		 * Adds any uploading attachments to the selection.
		 *
		 * If the state only supports one attachment to be selected and multiple
		 * attachments are uploaded, the last attachment in the upload queue will
		 * be selected.
		 *
		 * @since 3.5.0
		 *
		 * @param {wp.media.model.Attachment} attachment
		 */
		uploading: function( attachment ) {
			var content = this.frame.content;

			if ( 'upload' === content.mode() ) {
				this.frame.content.mode('browse');
			}

			if ( this.get( 'autoSelect' ) ) {
				this.get('selection').add( attachment );
				this.frame.trigger( 'library:selection:add' );
			}
		},

		/**
		 * Persist the mode of the content region as a user setting.
		 *
		 * @since 3.5.0
		 */
		saveContentMode: function() {
			if ( 'browse' !== this.get('router') ) {
				return;
			}

			var mode = this.frame.content.mode(),
				view = this.frame.router.get();

			if ( view && view.get( mode ) ) {
				setUserSetting( 'libraryContent', mode );
			}
		}
	});

	// Make selectionSync available on any Media Library state.
	_.extend( media.controller.Library.prototype, media.selectionSync );

	/**
	 * wp.media.controller.ImageDetails
	 *
	 * A state for editing the attachment display settings of an image that's been
	 * inserted into the editor.
	 *
	 * @class
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 *
	 * @param {object}                    [attributes]                       The attributes hash passed to the state.
	 * @param {string}                    [attributes.id=image-details]      Unique identifier.
	 * @param {string}                    [attributes.title=Image Details]   Title for the state. Displays in the frame's title region.
	 * @param {wp.media.model.Attachment} attributes.image                   The image's model.
	 * @param {string|false}              [attributes.content=image-details] Initial mode for the content region.
	 * @param {string|false}              [attributes.menu=false]            Initial mode for the menu region.
	 * @param {string|false}              [attributes.router=false]          Initial mode for the router region.
	 * @param {string|false}              [attributes.toolbar=image-details] Initial mode for the toolbar region.
	 * @param {boolean}                   [attributes.editing=false]         Unused.
	 * @param {int}                       [attributes.priority=60]           Unused.
	 *
	 * @todo This state inherits some defaults from media.controller.Library.prototype.defaults,
	 *       however this may not do anything.
	 */
	media.controller.ImageDetails = media.controller.State.extend({
		defaults: _.defaults({
			id:       'image-details',
			title:    l10n.imageDetailsTitle,
			content:  'image-details',
			menu:     false,
			router:   false,
			toolbar:  'image-details',
			editing:  false,
			priority: 60
		}, media.controller.Library.prototype.defaults ),

		/**
		 * @since 3.9.0
		 *
		 * @param options Attributes
		 */
		initialize: function( options ) {
			this.image = options.image;
			media.controller.State.prototype.initialize.apply( this, arguments );
		},

		/**
		 * @since 3.9.0
		 */
		activate: function() {
			this.frame.modal.$el.addClass('image-details');
		}
	});

	/**
	 * wp.media.controller.GalleryEdit
	 *
	 * A state for editing a gallery's images and settings.
	 *
	 * @class
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 *
	 * @param {object}                     [attributes]                       The attributes hash passed to the state.
	 * @param {string}                     [attributes.id=gallery-edit]       Unique identifier.
	 * @param {string}                     [attributes.title=Edit Gallery]    Title for the state. Displays in the frame's title region.
	 * @param {wp.media.model.Attachments} [attributes.library]               The collection of attachments in the gallery.
	 *                                                                        If one is not supplied, an empty media.model.Selection collection is created.
	 * @param {boolean}                    [attributes.multiple=false]        Whether multi-select is enabled.
	 * @param {boolean}                    [attributes.searchable=false]      Whether the library is searchable.
	 * @param {boolean}                    [attributes.sortable=true]         Whether the Attachments should be sortable. Depends on the orderby property being set to menuOrder on the attachments collection.
	 * @param {string|false}               [attributes.content=browse]        Initial mode for the content region.
	 * @param {string|false}               [attributes.toolbar=image-details] Initial mode for the toolbar region.
	 * @param {boolean}                    [attributes.describe=true]         Whether to offer UI to describe attachments - e.g. captioning images in a gallery.
	 * @param {boolean}                    [attributes.displaySettings=true]  Whether to show the attachment display settings interface.
	 * @param {boolean}                    [attributes.dragInfo=true]         Whether to show instructional text about the attachments being sortable.
	 * @param {int}                        [attributes.idealColumnWidth=170]  The ideal column width in pixels for attachments.
	 * @param {boolean}                    [attributes.editing=false]         Whether the gallery is being created, or editing an existing instance.
	 * @param {int}                        [attributes.priority=60]           The priority for the state link in the media menu.
	 * @param {boolean}                    [attributes.syncSelection=false]   Whether the Attachments selection should be persisted from the last state.
	 *                                                                        Defaults to false for this state, because the library passed in  *is* the selection.
	 * @param {view}                       [attributes.AttachmentView]        The single `Attachment` view to be used in the `Attachments`.
	 *                                                                        If none supplied, defaults to wp.media.view.Attachment.EditLibrary.
	 */
	media.controller.GalleryEdit = media.controller.Library.extend({
		defaults: {
			id:               'gallery-edit',
			title:            l10n.editGalleryTitle,
			multiple:         false,
			searchable:       false,
			sortable:         true,
			display:          false,
			content:          'browse',
			toolbar:          'gallery-edit',
			describe:         true,
			displaySettings:  true,
			dragInfo:         true,
			idealColumnWidth: 170,
			editing:          false,
			priority:         60,
			syncSelection:    false
		},

		/**
		 * @since 3.5.0
		 */
		initialize: function() {
			// If we haven't been provided a `library`, create a `Selection`.
			if ( ! this.get('library') )
				this.set( 'library', new media.model.Selection() );

			// The single `Attachment` view to be used in the `Attachments` view.
			if ( ! this.get('AttachmentView') )
				this.set( 'AttachmentView', media.view.Attachment.EditLibrary );
			media.controller.Library.prototype.initialize.apply( this, arguments );
		},

		/**
		 * @since 3.5.0
		 */
		activate: function() {
			var library = this.get('library');

			// Limit the library to images only.
			library.props.set( 'type', 'image' );

			// Watch for uploaded attachments.
			this.get('library').observe( wp.Uploader.queue );

			this.frame.on( 'content:render:browse', this.gallerySettings, this );

			media.controller.Library.prototype.activate.apply( this, arguments );
		},

		/**
		 * @since 3.5.0
		 */
		deactivate: function() {
			// Stop watching for uploaded attachments.
			this.get('library').unobserve( wp.Uploader.queue );

			this.frame.off( 'content:render:browse', this.gallerySettings, this );

			media.controller.Library.prototype.deactivate.apply( this, arguments );
		},

		/**
		 * @since 3.5.0
		 *
		 * @param browser
		 */
		gallerySettings: function( browser ) {
			if ( ! this.get('displaySettings') ) {
				return;
			}

			var library = this.get('library');

			if ( ! library || ! browser ) {
				return;
			}

			library.gallery = library.gallery || new Backbone.Model();

			browser.sidebar.set({
				gallery: new media.view.Settings.Gallery({
					controller: this,
					model:      library.gallery,
					priority:   40
				})
			});

			browser.toolbar.set( 'reverse', {
				text:     l10n.reverseOrder,
				priority: 80,

				click: function() {
					library.reset( library.toArray().reverse() );
				}
			});
		}
	});

	/**
	 * A state for selecting more images to add to a gallery.
	 *
	 * @class
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 *
	 * @param {object}                     [attributes]                         The attributes hash passed to the state.
	 * @param {string}                     [attributes.id=gallery-library]      Unique identifier.
	 * @param {string}                     [attributes.title=Add to Gallery]    Title for the state. Displays in the frame's title region.
	 * @param {boolean}                    [attributes.multiple=add]            Whether multi-select is enabled. @todo 'add' doesn't seem do anything special, and gets used as a boolean.
	 * @param {wp.media.model.Attachments} [attributes.library]                 The attachments collection to browse.
	 *                                                                          If one is not supplied, a collection of all images will be created.
	 * @param {boolean|string}             [attributes.filterable=uploaded]     Whether the library is filterable, and if so what filters should be shown.
	 *                                                                          Accepts 'all', 'uploaded', or 'unattached'.
	 * @param {string}                     [attributes.menu=gallery]            Initial mode for the menu region.
	 * @param {string}                     [attributes.content=upload]          Initial mode for the content region.
	 *                                                                          Overridden by persistent user setting if 'contentUserSetting' is true.
	 * @param {string}                     [attributes.router=browse]           Initial mode for the router region.
	 * @param {string}                     [attributes.toolbar=gallery-add]     Initial mode for the toolbar region.
	 * @param {boolean}                    [attributes.searchable=true]         Whether the library is searchable.
	 * @param {boolean}                    [attributes.sortable=true]           Whether the Attachments should be sortable. Depends on the orderby property being set to menuOrder on the attachments collection.
	 * @param {boolean}                    [attributes.autoSelect=true]         Whether an uploaded attachment should be automatically added to the selection.
	 * @param {boolean}                    [attributes.contentUserSetting=true] Whether the content region's mode should be set and persisted per user.
	 * @param {int}                        [attributes.priority=100]            The priority for the state link in the media menu.
	 * @param {boolean}                    [attributes.syncSelection=false]     Whether the Attachments selection should be persisted from the last state.
	 *                                                                          Defaults to false because for this state, because the library of the Edit Gallery state is the selection.
	 */
	media.controller.GalleryAdd = media.controller.Library.extend({
		defaults: _.defaults({
			id:            'gallery-library',
			title:         l10n.addToGalleryTitle,
			multiple:      'add',
			filterable:    'uploaded',
			menu:          'gallery',
			toolbar:       'gallery-add',
			priority:      100,
			syncSelection: false
		}, media.controller.Library.prototype.defaults ),

		/**
		 * @since 3.5.0
		 */
		initialize: function() {
			// If a library wasn't supplied, create a library of images.
			if ( ! this.get('library') )
				this.set( 'library', media.query({ type: 'image' }) );

			media.controller.Library.prototype.initialize.apply( this, arguments );
		},

		/**
		 * @since 3.5.0
		 */
		activate: function() {
			var library = this.get('library'),
				edit    = this.frame.state('gallery-edit').get('library');

			if ( this.editLibrary && this.editLibrary !== edit )
				library.unobserve( this.editLibrary );

			// Accepts attachments that exist in the original library and
			// that do not exist in gallery's library.
			library.validator = function( attachment ) {
				return !! this.mirroring.get( attachment.cid ) && ! edit.get( attachment.cid ) && media.model.Selection.prototype.validator.apply( this, arguments );
			};

			// Reset the library to ensure that all attachments are re-added
			// to the collection. Do so silently, as calling `observe` will
			// trigger the `reset` event.
			library.reset( library.mirroring.models, { silent: true });
			library.observe( edit );
			this.editLibrary = edit;

			media.controller.Library.prototype.activate.apply( this, arguments );
		}
	});

	/**
	 * wp.media.controller.CollectionEdit
	 *
	 * A state for editing a collection, which is used by audio and video playlists,
	 * and can be used for other collections.
	 *
	 * @class
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 *
	 * @param {object}                     [attributes]                      The attributes hash passed to the state.
	 * @param {string}                     attributes.title                  Title for the state. Displays in the media menu and the frame's title region.
	 * @param {wp.media.model.Attachments} [attributes.library]              The attachments collection to edit.
	 *                                                                       If one is not supplied, an empty media.model.Selection collection is created.
	 * @param {boolean}                    [attributes.multiple=false]       Whether multi-select is enabled.
	 * @param {string}                     [attributes.content=browse]       Initial mode for the content region.
	 * @param {string}                     attributes.menu                   Initial mode for the menu region. @todo this needs a better explanation.
	 * @param {boolean}                    [attributes.searchable=false]     Whether the library is searchable.
	 * @param {boolean}                    [attributes.sortable=true]        Whether the Attachments should be sortable. Depends on the orderby property being set to menuOrder on the attachments collection.
	 * @param {boolean}                    [attributes.describe=true]        Whether to offer UI to describe the attachments - e.g. captioning images in a gallery.
	 * @param {boolean}                    [attributes.dragInfo=true]        Whether to show instructional text about the attachments being sortable.
	 * @param {boolean}                    [attributes.dragInfoText]         Instructional text about the attachments being sortable.
	 * @param {int}                        [attributes.idealColumnWidth=170] The ideal column width in pixels for attachments.
	 * @param {boolean}                    [attributes.editing=false]        Whether the gallery is being created, or editing an existing instance.
	 * @param {int}                        [attributes.priority=60]          The priority for the state link in the media menu.
	 * @param {boolean}                    [attributes.syncSelection=false]  Whether the Attachments selection should be persisted from the last state.
	 *                                                                       Defaults to false for this state, because the library passed in  *is* the selection.
	 * @param {view}                       [attributes.SettingsView]         The view to edit the collection instance settings (e.g. Playlist settings with "Show tracklist" checkbox).
	 * @param {view}                       [attributes.AttachmentView]       The single `Attachment` view to be used in the `Attachments`.
	 *                                                                       If none supplied, defaults to wp.media.view.Attachment.EditLibrary.
	 * @param {string}                     attributes.type                   The collection's media type. (e.g. 'video').
	 * @param {string}                     attributes.collectionType         The collection type. (e.g. 'playlist').
	 */
	media.controller.CollectionEdit = media.controller.Library.extend({
		defaults: {
			multiple:         false,
			sortable:         true,
			searchable:       false,
			content:          'browse',
			describe:         true,
			dragInfo:         true,
			idealColumnWidth: 170,
			editing:          false,
			priority:         60,
			SettingsView:     false,
			syncSelection:    false
		},

		/**
		 * @since 3.9.0
		 */
		initialize: function() {
			var collectionType = this.get('collectionType');

			if ( 'video' === this.get( 'type' ) ) {
				collectionType = 'video-' + collectionType;
			}

			this.set( 'id', collectionType + '-edit' );
			this.set( 'toolbar', collectionType + '-edit' );

			// If we haven't been provided a `library`, create a `Selection`.
			if ( ! this.get('library') ) {
				this.set( 'library', new media.model.Selection() );
			}
			// The single `Attachment` view to be used in the `Attachments` view.
			if ( ! this.get('AttachmentView') ) {
				this.set( 'AttachmentView', media.view.Attachment.EditLibrary );
			}
			media.controller.Library.prototype.initialize.apply( this, arguments );
		},

		/**
		 * @since 3.9.0
		 */
		activate: function() {
			var library = this.get('library');

			// Limit the library to images only.
			library.props.set( 'type', this.get( 'type' ) );

			// Watch for uploaded attachments.
			this.get('library').observe( wp.Uploader.queue );

			this.frame.on( 'content:render:browse', this.renderSettings, this );

			media.controller.Library.prototype.activate.apply( this, arguments );
		},

		/**
		 * @since 3.9.0
		 */
		deactivate: function() {
			// Stop watching for uploaded attachments.
			this.get('library').unobserve( wp.Uploader.queue );

			this.frame.off( 'content:render:browse', this.renderSettings, this );

			media.controller.Library.prototype.deactivate.apply( this, arguments );
		},

		/**
		 * Render the collection embed settings view in the browser sidebar.
		 *
		 * @todo This is against the pattern elsewhere in media. Typically the frame
		 *       is responsible for adding region mode callbacks. Explain.
		 *
		 * @since 3.9.0
		 *
		 * @param {wp.media.view.attachmentsBrowser} The attachments browser view.
		 */
		renderSettings: function( attachmentsBrowserView ) {
			var library = this.get('library'),
				collectionType = this.get('collectionType'),
				dragInfoText = this.get('dragInfoText'),
				SettingsView = this.get('SettingsView'),
				obj = {};

			if ( ! library || ! attachmentsBrowserView ) {
				return;
			}

			library[ collectionType ] = library[ collectionType ] || new Backbone.Model();

			obj[ collectionType ] = new SettingsView({
				controller: this,
				model:      library[ collectionType ],
				priority:   40
			});

			attachmentsBrowserView.sidebar.set( obj );

			if ( dragInfoText ) {
				attachmentsBrowserView.toolbar.set( 'dragInfo', new media.View({
					el: $( '<div class="instructions">' + dragInfoText + '</div>' )[0],
					priority: -40
				}) );
			}

			// Add the 'Reverse order' button to the toolbar.
			attachmentsBrowserView.toolbar.set( 'reverse', {
				text:     l10n.reverseOrder,
				priority: 80,

				click: function() {
					library.reset( library.toArray().reverse() );
				}
			});
		}
	});

	/**
	 * wp.media.controller.CollectionAdd
	 *
	 * A state for adding attachments to a collection (e.g. video playlist).
	 *
	 * @class
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 *
	 * @param {object}                     [attributes]                         The attributes hash passed to the state.
	 * @param {string}                     [attributes.id=library]      Unique identifier.
	 * @param {string}                     attributes.title                    Title for the state. Displays in the frame's title region.
	 * @param {boolean}                    [attributes.multiple=add]            Whether multi-select is enabled. @todo 'add' doesn't seem do anything special, and gets used as a boolean.
	 * @param {wp.media.model.Attachments} [attributes.library]                 The attachments collection to browse.
	 *                                                                          If one is not supplied, a collection of attachments of the specified type will be created.
	 * @param {boolean|string}             [attributes.filterable=uploaded]     Whether the library is filterable, and if so what filters should be shown.
	 *                                                                          Accepts 'all', 'uploaded', or 'unattached'.
	 * @param {string}                     [attributes.menu=gallery]            Initial mode for the menu region.
	 * @param {string}                     [attributes.content=upload]          Initial mode for the content region.
	 *                                                                          Overridden by persistent user setting if 'contentUserSetting' is true.
	 * @param {string}                     [attributes.router=browse]           Initial mode for the router region.
	 * @param {string}                     [attributes.toolbar=gallery-add]     Initial mode for the toolbar region.
	 * @param {boolean}                    [attributes.searchable=true]         Whether the library is searchable.
	 * @param {boolean}                    [attributes.sortable=true]           Whether the Attachments should be sortable. Depends on the orderby property being set to menuOrder on the attachments collection.
	 * @param {boolean}                    [attributes.autoSelect=true]         Whether an uploaded attachment should be automatically added to the selection.
	 * @param {boolean}                    [attributes.contentUserSetting=true] Whether the content region's mode should be set and persisted per user.
	 * @param {int}                        [attributes.priority=100]            The priority for the state link in the media menu.
	 * @param {boolean}                    [attributes.syncSelection=false]     Whether the Attachments selection should be persisted from the last state.
	 *                                                                          Defaults to false because for this state, because the library of the Edit Gallery state is the selection.
	 * @param {string}                     attributes.type                   The collection's media type. (e.g. 'video').
	 * @param {string}                     attributes.collectionType         The collection type. (e.g. 'playlist').
	 */
	media.controller.CollectionAdd = media.controller.Library.extend({
		defaults: _.defaults( {
			// Selection defaults. @see media.model.Selection
			multiple:      'add',
			// Attachments browser defaults. @see media.view.AttachmentsBrowser
			filterable:    'uploaded',

			priority:      100,
			syncSelection: false
		}, media.controller.Library.prototype.defaults ),

		/**
		 * @since 3.9.0
		 */
		initialize: function() {
			var collectionType = this.get('collectionType');

			if ( 'video' === this.get( 'type' ) ) {
				collectionType = 'video-' + collectionType;
			}

			this.set( 'id', collectionType + '-library' );
			this.set( 'toolbar', collectionType + '-add' );
			this.set( 'menu', collectionType );

			// If we haven't been provided a `library`, create a `Selection`.
			if ( ! this.get('library') ) {
				this.set( 'library', media.query({ type: this.get('type') }) );
			}
			media.controller.Library.prototype.initialize.apply( this, arguments );
		},

		/**
		 * @since 3.9.0
		 */
		activate: function() {
			var library = this.get('library'),
				editLibrary = this.get('editLibrary'),
				edit = this.frame.state( this.get('collectionType') + '-edit' ).get('library');

			if ( editLibrary && editLibrary !== edit ) {
				library.unobserve( editLibrary );
			}

			// Accepts attachments that exist in the original library and
			// that do not exist in gallery's library.
			library.validator = function( attachment ) {
				return !! this.mirroring.get( attachment.cid ) && ! edit.get( attachment.cid ) && media.model.Selection.prototype.validator.apply( this, arguments );
			};

			// Reset the library to ensure that all attachments are re-added
			// to the collection. Do so silently, as calling `observe` will
			// trigger the `reset` event.
			library.reset( library.mirroring.models, { silent: true });
			library.observe( edit );
			this.set('editLibrary', edit);

			media.controller.Library.prototype.activate.apply( this, arguments );
		}
	});

	/**
	 * wp.media.controller.FeaturedImage
	 *
	 * A state for selecting a featured image for a post.
	 *
	 * @class
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 *
	 * @param {object}                     [attributes]                          The attributes hash passed to the state.
	 * @param {string}                     [attributes.id=featured-image]        Unique identifier.
	 * @param {string}                     [attributes.title=Set Featured Image] Title for the state. Displays in the media menu and the frame's title region.
	 * @param {wp.media.model.Attachments} [attributes.library]                  The attachments collection to browse.
	 *                                                                           If one is not supplied, a collection of all images will be created.
	 * @param {boolean}                    [attributes.multiple=false]           Whether multi-select is enabled.
	 * @param {string}                     [attributes.content=upload]           Initial mode for the content region.
	 *                                                                           Overridden by persistent user setting if 'contentUserSetting' is true.
	 * @param {string}                     [attributes.menu=default]             Initial mode for the menu region.
	 * @param {string}                     [attributes.router=browse]            Initial mode for the router region.
	 * @param {string}                     [attributes.toolbar=featured-image]   Initial mode for the toolbar region.
	 * @param {int}                        [attributes.priority=60]              The priority for the state link in the media menu.
	 * @param {boolean}                    [attributes.searchable=true]          Whether the library is searchable.
	 * @param {boolean|string}             [attributes.filterable=false]         Whether the library is filterable, and if so what filters should be shown.
	 *                                                                           Accepts 'all', 'uploaded', or 'unattached'.
	 * @param {boolean}                    [attributes.sortable=true]            Whether the Attachments should be sortable. Depends on the orderby property being set to menuOrder on the attachments collection.
	 * @param {boolean}                    [attributes.autoSelect=true]          Whether an uploaded attachment should be automatically added to the selection.
	 * @param {boolean}                    [attributes.describe=false]           Whether to offer UI to describe attachments - e.g. captioning images in a gallery.
	 * @param {boolean}                    [attributes.contentUserSetting=true]  Whether the content region's mode should be set and persisted per user.
	 * @param {boolean}                    [attributes.syncSelection=true]       Whether the Attachments selection should be persisted from the last state.
	 */
	media.controller.FeaturedImage = media.controller.Library.extend({
		defaults: _.defaults({
			id:            'featured-image',
			title:         l10n.setFeaturedImageTitle,
			multiple:      false,
			filterable:    'uploaded',
			toolbar:       'featured-image',
			priority:      60,
			syncSelection: true
		}, media.controller.Library.prototype.defaults ),

		/**
		 * @since 3.5.0
		 */
		initialize: function() {
			var library, comparator;

			// If we haven't been provided a `library`, create a `Selection`.
			if ( ! this.get('library') ) {
				this.set( 'library', media.query({ type: 'image' }) );
			}

			media.controller.Library.prototype.initialize.apply( this, arguments );

			library    = this.get('library');
			comparator = library.comparator;

			// Overload the library's comparator to push items that are not in
			// the mirrored query to the front of the aggregate collection.
			library.comparator = function( a, b ) {
				var aInQuery = !! this.mirroring.get( a.cid ),
					bInQuery = !! this.mirroring.get( b.cid );

				if ( ! aInQuery && bInQuery ) {
					return -1;
				} else if ( aInQuery && ! bInQuery ) {
					return 1;
				} else {
					return comparator.apply( this, arguments );
				}
			};

			// Add all items in the selection to the library, so any featured
			// images that are not initially loaded still appear.
			library.observe( this.get('selection') );
		},

		/**
		 * @since 3.5.0
		 */
		activate: function() {
			this.updateSelection();
			this.frame.on( 'open', this.updateSelection, this );

			media.controller.Library.prototype.activate.apply( this, arguments );
		},

		/**
		 * @since 3.5.0
		 */
		deactivate: function() {
			this.frame.off( 'open', this.updateSelection, this );

			media.controller.Library.prototype.deactivate.apply( this, arguments );
		},

		/**
		 * @since 3.5.0
		 */
		updateSelection: function() {
			var selection = this.get('selection'),
				id = media.view.settings.post.featuredImageId,
				attachment;

			if ( '' !== id && -1 !== id ) {
				attachment = media.model.Attachment.get( id );
				attachment.fetch();
			}

			selection.reset( attachment ? [ attachment ] : [] );
		}
	});

	/**
	 * wp.media.controller.ReplaceImage
	 *
	 * A state for replacing an image.
	 *
	 * @class
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 *
	 * @param {object}                     [attributes]                         The attributes hash passed to the state.
	 * @param {string}                     [attributes.id=replace-image]        Unique identifier.
	 * @param {string}                     [attributes.title=Replace Image]     Title for the state. Displays in the media menu and the frame's title region.
	 * @param {wp.media.model.Attachments} [attributes.library]                 The attachments collection to browse.
	 *                                                                          If one is not supplied, a collection of all images will be created.
	 * @param {boolean}                    [attributes.multiple=false]          Whether multi-select is enabled.
	 * @param {string}                     [attributes.content=upload]          Initial mode for the content region.
	 *                                                                          Overridden by persistent user setting if 'contentUserSetting' is true.
	 * @param {string}                     [attributes.menu=default]            Initial mode for the menu region.
	 * @param {string}                     [attributes.router=browse]           Initial mode for the router region.
	 * @param {string}                     [attributes.toolbar=replace]         Initial mode for the toolbar region.
	 * @param {int}                        [attributes.priority=60]             The priority for the state link in the media menu.
	 * @param {boolean}                    [attributes.searchable=true]         Whether the library is searchable.
	 * @param {boolean|string}             [attributes.filterable=uploaded]     Whether the library is filterable, and if so what filters should be shown.
	 *                                                                          Accepts 'all', 'uploaded', or 'unattached'.
	 * @param {boolean}                    [attributes.sortable=true]           Whether the Attachments should be sortable. Depends on the orderby property being set to menuOrder on the attachments collection.
	 * @param {boolean}                    [attributes.autoSelect=true]         Whether an uploaded attachment should be automatically added to the selection.
	 * @param {boolean}                    [attributes.describe=false]          Whether to offer UI to describe attachments - e.g. captioning images in a gallery.
	 * @param {boolean}                    [attributes.contentUserSetting=true] Whether the content region's mode should be set and persisted per user.
	 * @param {boolean}                    [attributes.syncSelection=true]      Whether the Attachments selection should be persisted from the last state.
	 */
	media.controller.ReplaceImage = media.controller.Library.extend({
		defaults: _.defaults({
			id:            'replace-image',
			title:         l10n.replaceImageTitle,
			multiple:      false,
			filterable:    'uploaded',
			toolbar:       'replace',
			menu:          false,
			priority:      60,
			syncSelection: true
		}, media.controller.Library.prototype.defaults ),

		/**
		 * @since 3.9.0
		 *
		 * @param options
		 */
		initialize: function( options ) {
			var library, comparator;

			this.image = options.image;
			// If we haven't been provided a `library`, create a `Selection`.
			if ( ! this.get('library') ) {
				this.set( 'library', media.query({ type: 'image' }) );
			}

			media.controller.Library.prototype.initialize.apply( this, arguments );

			library    = this.get('library');
			comparator = library.comparator;

			// Overload the library's comparator to push items that are not in
			// the mirrored query to the front of the aggregate collection.
			library.comparator = function( a, b ) {
				var aInQuery = !! this.mirroring.get( a.cid ),
					bInQuery = !! this.mirroring.get( b.cid );

				if ( ! aInQuery && bInQuery ) {
					return -1;
				} else if ( aInQuery && ! bInQuery ) {
					return 1;
				} else {
					return comparator.apply( this, arguments );
				}
			};

			// Add all items in the selection to the library, so any featured
			// images that are not initially loaded still appear.
			library.observe( this.get('selection') );
		},

		/**
		 * @since 3.9.0
		 */
		activate: function() {
			this.updateSelection();
			media.controller.Library.prototype.activate.apply( this, arguments );
		},

		/**
		 * @since 3.9.0
		 */
		updateSelection: function() {
			var selection = this.get('selection'),
				attachment = this.image.attachment;

			selection.reset( attachment ? [ attachment ] : [] );
		}
	});

	/**
	 * wp.media.controller.EditImage
	 *
	 * A state for editing (cropping, etc.) an image.
	 *
	 * @class
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 *
	 * @param {object}                    attributes                      The attributes hash passed to the state.
	 * @param {wp.media.model.Attachment} attributes.model                The attachment.
	 * @param {string}                    [attributes.id=edit-image]      Unique identifier.
	 * @param {string}                    [attributes.title=Edit Image]   Title for the state. Displays in the media menu and the frame's title region.
	 * @param {string}                    [attributes.content=edit-image] Initial mode for the content region.
	 * @param {string}                    [attributes.toolbar=edit-image] Initial mode for the toolbar region.
	 * @param {string}                    [attributes.menu=false]         Initial mode for the menu region.
	 * @param {string}                    [attributes.url]                Unused. @todo Consider removal.
	 */
	media.controller.EditImage = media.controller.State.extend({
		defaults: {
			id:      'edit-image',
			title:   l10n.editImage,
			menu:    false,
			toolbar: 'edit-image',
			content: 'edit-image',
			url:     ''
		},

		/**
		 * @since 3.9.0
		 */
		activate: function() {
			this.listenTo( this.frame, 'toolbar:render:edit-image', this.toolbar );
		},

		/**
		 * @since 3.9.0
		 */
		deactivate: function() {
			this.stopListening( this.frame );
		},

		/**
		 * @since 3.9.0
		 */
		toolbar: function() {
			var frame = this.frame,
				lastState = frame.lastState(),
				previous = lastState && lastState.id;

			frame.toolbar.set( new media.view.Toolbar({
				controller: frame,
				items: {
					back: {
						style: 'primary',
						text:     l10n.back,
						priority: 20,
						click:    function() {
							if ( previous ) {
								frame.setState( previous );
							} else {
								frame.close();
							}
						}
					}
				}
			}) );
		}
	});

	/**
	 * wp.media.controller.MediaLibrary
	 *
	 * @class
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.MediaLibrary = media.controller.Library.extend({
		defaults: _.defaults({
			// Attachments browser defaults. @see media.view.AttachmentsBrowser
			filterable:      'uploaded',

			displaySettings: false,
			priority:        80,
			syncSelection:   false
		}, media.controller.Library.prototype.defaults ),

		/**
		 * @since 3.9.0
		 *
		 * @param options
		 */
		initialize: function( options ) {
			this.media = options.media;
			this.type = options.type;
			this.set( 'library', media.query({ type: this.type }) );

			media.controller.Library.prototype.initialize.apply( this, arguments );
		},

		/**
		 * @since 3.9.0
		 */
		activate: function() {
			// @todo this should use this.frame.
			if ( media.frame.lastMime ) {
				this.set( 'library', media.query({ type: media.frame.lastMime }) );
				delete media.frame.lastMime;
			}
			media.controller.Library.prototype.activate.apply( this, arguments );
		}
	});

	/**
	 * wp.media.controller.Embed
	 *
	 * A state for embedding media from a URL.
	 *
	 * @class
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 *
	 * @param {object} attributes                         The attributes hash passed to the state.
	 * @param {string} [attributes.id=embed]              Unique identifier.
	 * @param {string} [attributes.title=Insert From URL] Title for the state. Displays in the media menu and the frame's title region.
	 * @param {string} [attributes.content=embed]         Initial mode for the content region.
	 * @param {string} [attributes.menu=default]          Initial mode for the menu region.
	 * @param {string} [attributes.toolbar=main-embed]    Initial mode for the toolbar region.
	 * @param {string} [attributes.menu=false]            Initial mode for the menu region.
	 * @param {int}    [attributes.priority=120]          The priority for the state link in the media menu.
	 * @param {string} [attributes.type=link]             The type of embed. Currently only link is supported.
	 * @param {string} [attributes.url]                   The embed URL.
	 * @param {object} [attributes.metadata={}]           Properties of the embed, which will override attributes.url if set.
	 */
	media.controller.Embed = media.controller.State.extend({
		defaults: {
			id:       'embed',
			title:    l10n.insertFromUrlTitle,
			content:  'embed',
			menu:     'default',
			toolbar:  'main-embed',
			priority: 120,
			type:     'link',
			url:      '',
			metadata: {}
		},

		// The amount of time used when debouncing the scan.
		sensitivity: 200,

		initialize: function(options) {
			this.metadata = options.metadata;
			this.debouncedScan = _.debounce( _.bind( this.scan, this ), this.sensitivity );
			this.props = new Backbone.Model( this.metadata || { url: '' });
			this.props.on( 'change:url', this.debouncedScan, this );
			this.props.on( 'change:url', this.refresh, this );
			this.on( 'scan', this.scanImage, this );
		},

		/**
		 * Trigger a scan of the embedded URL's content for metadata required to embed.
		 *
		 * @fires wp.media.controller.Embed#scan
		 */
		scan: function() {
			var scanners,
				embed = this,
				attributes = {
					type: 'link',
					scanners: []
				};

			// Scan is triggered with the list of `attributes` to set on the
			// state, useful for the 'type' attribute and 'scanners' attribute,
			// an array of promise objects for asynchronous scan operations.
			if ( this.props.get('url') ) {
				this.trigger( 'scan', attributes );
			}

			if ( attributes.scanners.length ) {
				scanners = attributes.scanners = $.when.apply( $, attributes.scanners );
				scanners.always( function() {
					if ( embed.get('scanners') === scanners ) {
						embed.set( 'loading', false );
					}
				});
			} else {
				attributes.scanners = null;
			}

			attributes.loading = !! attributes.scanners;
			this.set( attributes );
		},
		/**
		 * Try scanning the embed as an image to discover its dimensions.
		 *
		 * @param {Object} attributes
		 */
		scanImage: function( attributes ) {
			var frame = this.frame,
				state = this,
				url = this.props.get('url'),
				image = new Image(),
				deferred = $.Deferred();

			attributes.scanners.push( deferred.promise() );

			// Try to load the image and find its width/height.
			image.onload = function() {
				deferred.resolve();

				if ( state !== frame.state() || url !== state.props.get('url') ) {
					return;
				}

				state.set({
					type: 'image'
				});

				state.props.set({
					width:  image.width,
					height: image.height
				});
			};

			image.onerror = deferred.reject;
			image.src = url;
		},

		refresh: function() {
			this.frame.toolbar.get().refresh();
		},

		reset: function() {
			this.props.clear().set({ url: '' });

			if ( this.active ) {
				this.refresh();
			}
		}
	});

	/**
	 * wp.media.controller.Cropper
	 *
	 * A state for cropping an image.
	 *
	 * @class
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.Cropper = media.controller.State.extend({
		defaults: {
			id:          'cropper',
			title:       l10n.cropImage,
			// Region mode defaults.
			toolbar:     'crop',
			content:     'crop',
			router:      false,

			canSkipCrop: false
		},

		activate: function() {
			this.frame.on( 'content:create:crop', this.createCropContent, this );
			this.frame.on( 'close', this.removeCropper, this );
			this.set('selection', new Backbone.Collection(this.frame._selection.single));
		},

		deactivate: function() {
			this.frame.toolbar.mode('browse');
		},

		createCropContent: function() {
			this.cropperView = new wp.media.view.Cropper({controller: this,
					attachment: this.get('selection').first() });
			this.cropperView.on('image-loaded', this.createCropToolbar, this);
			this.frame.content.set(this.cropperView);

		},
		removeCropper: function() {
			this.imgSelect.cancelSelection();
			this.imgSelect.setOptions({remove: true});
			this.imgSelect.update();
			this.cropperView.remove();
		},
		createCropToolbar: function() {
			var canSkipCrop, toolbarOptions;

			canSkipCrop = this.get('canSkipCrop') || false;

			toolbarOptions = {
				controller: this.frame,
				items: {
					insert: {
						style:    'primary',
						text:     l10n.cropImage,
						priority: 80,
						requires: { library: false, selection: false },

						click: function() {
							var self = this,
								selection = this.controller.state().get('selection').first();

							selection.set({cropDetails: this.controller.state().imgSelect.getSelection()});

							this.$el.text(l10n.cropping);
							this.$el.attr('disabled', true);
							this.controller.state().doCrop( selection ).done( function( croppedImage ) {
								self.controller.trigger('cropped', croppedImage );
								self.controller.close();
							}).fail( function() {
								self.controller.trigger('content:error:crop');
							});
						}
					}
				}
			};

			if ( canSkipCrop ) {
				_.extend( toolbarOptions.items, {
					skip: {
						style:      'secondary',
						text:       l10n.skipCropping,
						priority:   70,
						requires:   { library: false, selection: false },
						click:      function() {
							var selection = this.controller.state().get('selection').first();
							this.controller.state().cropperView.remove();
							this.controller.trigger('skippedcrop', selection);
							this.controller.close();
						}
					}
				});
			}

			this.frame.toolbar.set( new wp.media.view.Toolbar(toolbarOptions) );
		},

		doCrop: function( attachment ) {
			return wp.ajax.post( 'custom-header-crop', {
				nonce: attachment.get('nonces').edit,
				id: attachment.get('id'),
				cropDetails: attachment.get('cropDetails')
			} );
		}
	});

	/**
	 * wp.media.View
	 *
	 * The base view class for media.
	 *
	 * Undelegating events, removing events from the model, and
	 * removing events from the controller mirror the code for
	 * `Backbone.View.dispose` in Backbone 0.9.8 development.
	 *
	 * This behavior has since been removed, and should not be used
	 * outside of the media manager.
	 *
	 * @class
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.View = wp.Backbone.View.extend({
		constructor: function( options ) {
			if ( options && options.controller ) {
				this.controller = options.controller;
			}
			wp.Backbone.View.apply( this, arguments );
		},
		/**
		 * @todo The internal comment mentions this might have been a stop-gap
		 *       before Backbone 0.9.8 came out. Figure out if Backbone core takes
		 *       care of this in Backbone.View now.
		 *
		 * @returns {wp.media.View} Returns itself to allow chaining
		 */
		dispose: function() {
			// Undelegating events, removing events from the model, and
			// removing events from the controller mirror the code for
			// `Backbone.View.dispose` in Backbone 0.9.8 development.
			this.undelegateEvents();

			if ( this.model && this.model.off ) {
				this.model.off( null, null, this );
			}

			if ( this.collection && this.collection.off ) {
				this.collection.off( null, null, this );
			}

			// Unbind controller events.
			if ( this.controller && this.controller.off ) {
				this.controller.off( null, null, this );
			}

			return this;
		},
		/**
		 * @returns {wp.media.View} Returns itself to allow chaining
		 */
		remove: function() {
			this.dispose();
			/**
			 * call 'remove' directly on the parent class
			 */
			return wp.Backbone.View.prototype.remove.apply( this, arguments );
		}
	});

	/**
	 * wp.media.view.Frame
	 *
	 * A frame is a composite view consisting of one or more regions and one or more
	 * states.
	 *
	 * @see wp.media.controller.State
	 * @see wp.media.controller.Region
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 * @mixes wp.media.controller.StateMachine
	 */
	media.view.Frame = media.View.extend({
		initialize: function() {
			_.defaults( this.options, {
				mode: [ 'select' ]
			});
			this._createRegions();
			this._createStates();
			this._createModes();
		},

		_createRegions: function() {
			// Clone the regions array.
			this.regions = this.regions ? this.regions.slice() : [];

			// Initialize regions.
			_.each( this.regions, function( region ) {
				this[ region ] = new media.controller.Region({
					view:     this,
					id:       region,
					selector: '.media-frame-' + region
				});
			}, this );
		},
		/**
		 * Create the frame's states.
		 *
		 * @see wp.media.controller.State
		 * @see wp.media.controller.StateMachine
		 *
		 * @fires wp.media.controller.State#ready
		 */
		_createStates: function() {
			// Create the default `states` collection.
			this.states = new Backbone.Collection( null, {
				model: media.controller.State
			});

			// Ensure states have a reference to the frame.
			this.states.on( 'add', function( model ) {
				model.frame = this;
				model.trigger('ready');
			}, this );

			if ( this.options.states ) {
				this.states.add( this.options.states );
			}
		},

		/**
		 * A frame can be in a mode or multiple modes at one time.
		 *
		 * For example, the manage media frame can be in the `Bulk Select` or `Edit` mode.
		 */
		_createModes: function() {
			// Store active "modes" that the frame is in. Unrelated to region modes.
			this.activeModes = new Backbone.Collection();
			this.activeModes.on( 'add remove reset', _.bind( this.triggerModeEvents, this ) );

			_.each( this.options.mode, function( mode ) {
				this.activateMode( mode );
			}, this );
		},
		/**
		 * Reset all states on the frame to their defaults.
		 *
		 * @returns {wp.media.view.Frame} Returns itself to allow chaining
		 */
		reset: function() {
			this.states.invoke( 'trigger', 'reset' );
			return this;
		},
		/**
		 * Map activeMode collection events to the frame.
		 */
		triggerModeEvents: function( model, collection, options ) {
			var collectionEvent,
				modeEventMap = {
					add: 'activate',
					remove: 'deactivate'
				},
				eventToTrigger;
			// Probably a better way to do this.
			_.each( options, function( value, key ) {
				if ( value ) {
					collectionEvent = key;
				}
			} );

			if ( ! _.has( modeEventMap, collectionEvent ) ) {
				return;
			}

			eventToTrigger = model.get('id') + ':' + modeEventMap[collectionEvent];
			this.trigger( eventToTrigger );
		},
		/**
		 * Activate a mode on the frame.
		 *
		 * @param string mode Mode ID.
		 * @returns {this} Returns itself to allow chaining.
		 */
		activateMode: function( mode ) {
			// Bail if the mode is already active.
			if ( this.isModeActive( mode ) ) {
				return;
			}
			this.activeModes.add( [ { id: mode } ] );
			// Add a CSS class to the frame so elements can be styled for the mode.
			this.$el.addClass( 'mode-' + mode );

			return this;
		},
		/**
		 * Deactivate a mode on the frame.
		 *
		 * @param string mode Mode ID.
		 * @returns {this} Returns itself to allow chaining.
		 */
		deactivateMode: function( mode ) {
			// Bail if the mode isn't active.
			if ( ! this.isModeActive( mode ) ) {
				return this;
			}
			this.activeModes.remove( this.activeModes.where( { id: mode } ) );
			this.$el.removeClass( 'mode-' + mode );
			/**
			 * Frame mode deactivation event.
			 *
			 * @event this#{mode}:deactivate
			 */
			this.trigger( mode + ':deactivate' );

			return this;
		},
		/**
		 * Check if a mode is enabled on the frame.
		 *
		 * @param  string mode Mode ID.
		 * @return bool
		 */
		isModeActive: function( mode ) {
			return Boolean( this.activeModes.where( { id: mode } ).length );
		}
	});

	// Make the `Frame` a `StateMachine`.
	_.extend( media.view.Frame.prototype, media.controller.StateMachine.prototype );

	/**
	 * wp.media.view.MediaFrame
	 *
	 * The frame used to create the media modal.
	 *
	 * @class
	 * @augments wp.media.view.Frame
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 * @mixes wp.media.controller.StateMachine
	 */
	media.view.MediaFrame = media.view.Frame.extend({
		className: 'media-frame',
		template:  media.template('media-frame'),
		regions:   ['menu','title','content','toolbar','router'],

		events: {
			'click div.media-frame-title h1': 'toggleMenu'
		},

		/**
		 * @global wp.Uploader
		 */
		initialize: function() {
			media.view.Frame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				title:    '',
				modal:    true,
				uploader: true
			});

			// Ensure core UI is enabled.
			this.$el.addClass('wp-core-ui');

			// Initialize modal container view.
			if ( this.options.modal ) {
				this.modal = new media.view.Modal({
					controller: this,
					title:      this.options.title
				});

				this.modal.content( this );
			}

			// Force the uploader off if the upload limit has been exceeded or
			// if the browser isn't supported.
			if ( wp.Uploader.limitExceeded || ! wp.Uploader.browser.supported ) {
				this.options.uploader = false;
			}

			// Initialize window-wide uploader.
			if ( this.options.uploader ) {
				this.uploader = new media.view.UploaderWindow({
					controller: this,
					uploader: {
						dropzone:  this.modal ? this.modal.$el : this.$el,
						container: this.$el
					}
				});
				this.views.set( '.media-frame-uploader', this.uploader );
			}

			this.on( 'attach', _.bind( this.views.ready, this.views ), this );

			// Bind default title creation.
			this.on( 'title:create:default', this.createTitle, this );
			this.title.mode('default');

			this.on( 'title:render', function( view ) {
				view.$el.append( '<span class="dashicons dashicons-arrow-down"></span>' );
			});

			// Bind default menu.
			this.on( 'menu:create:default', this.createMenu, this );
		},
		/**
		 * @returns {wp.media.view.MediaFrame} Returns itself to allow chaining
		 */
		render: function() {
			// Activate the default state if no active state exists.
			if ( ! this.state() && this.options.state ) {
				this.setState( this.options.state );
			}
			/**
			 * call 'render' directly on the parent class
			 */
			return media.view.Frame.prototype.render.apply( this, arguments );
		},
		/**
		 * @param {Object} title
		 * @this wp.media.controller.Region
		 */
		createTitle: function( title ) {
			title.view = new media.View({
				controller: this,
				tagName: 'h1'
			});
		},
		/**
		 * @param {Object} menu
		 * @this wp.media.controller.Region
		 */
		createMenu: function( menu ) {
			menu.view = new media.view.Menu({
				controller: this
			});
		},

		toggleMenu: function() {
			this.$el.find( '.media-menu' ).toggleClass( 'visible' );
		},

		/**
		 * @param {Object} toolbar
		 * @this wp.media.controller.Region
		 */
		createToolbar: function( toolbar ) {
			toolbar.view = new media.view.Toolbar({
				controller: this
			});
		},
		/**
		 * @param {Object} router
		 * @this wp.media.controller.Region
		 */
		createRouter: function( router ) {
			router.view = new media.view.Router({
				controller: this
			});
		},
		/**
		 * @param {Object} options
		 */
		createIframeStates: function( options ) {
			var settings = media.view.settings,
				tabs = settings.tabs,
				tabUrl = settings.tabUrl,
				$postId;

			if ( ! tabs || ! tabUrl ) {
				return;
			}

			// Add the post ID to the tab URL if it exists.
			$postId = $('#post_ID');
			if ( $postId.length ) {
				tabUrl += '&post_id=' + $postId.val();
			}

			// Generate the tab states.
			_.each( tabs, function( title, id ) {
				this.state( 'iframe:' + id ).set( _.defaults({
					tab:     id,
					src:     tabUrl + '&tab=' + id,
					title:   title,
					content: 'iframe',
					menu:    'default'
				}, options ) );
			}, this );

			this.on( 'content:create:iframe', this.iframeContent, this );
			this.on( 'content:deactivate:iframe', this.iframeContentCleanup, this );
			this.on( 'menu:render:default', this.iframeMenu, this );
			this.on( 'open', this.hijackThickbox, this );
			this.on( 'close', this.restoreThickbox, this );
		},

		/**
		 * @param {Object} content
		 * @this wp.media.controller.Region
		 */
		iframeContent: function( content ) {
			this.$el.addClass('hide-toolbar');
			content.view = new media.view.Iframe({
				controller: this
			});
		},

		iframeContentCleanup: function() {
			this.$el.removeClass('hide-toolbar');
		},

		iframeMenu: function( view ) {
			var views = {};

			if ( ! view ) {
				return;
			}

			_.each( media.view.settings.tabs, function( title, id ) {
				views[ 'iframe:' + id ] = {
					text: this.state( 'iframe:' + id ).get('title'),
					priority: 200
				};
			}, this );

			view.set( views );
		},

		hijackThickbox: function() {
			var frame = this;

			if ( ! window.tb_remove || this._tb_remove ) {
				return;
			}

			this._tb_remove = window.tb_remove;
			window.tb_remove = function() {
				frame.close();
				frame.reset();
				frame.setState( frame.options.state );
				frame._tb_remove.call( window );
			};
		},

		restoreThickbox: function() {
			if ( ! this._tb_remove ) {
				return;
			}

			window.tb_remove = this._tb_remove;
			delete this._tb_remove;
		}
	});

	// Map some of the modal's methods to the frame.
	_.each(['open','close','attach','detach','escape'], function( method ) {
		/**
		 * @returns {wp.media.view.MediaFrame} Returns itself to allow chaining
		 */
		media.view.MediaFrame.prototype[ method ] = function() {
			if ( this.modal ) {
				this.modal[ method ].apply( this.modal, arguments );
			}
			return this;
		};
	});

	/**
	 * wp.media.view.MediaFrame.Select
	 *
	 * A frame for selecting an item or items from the media library.
	 *
	 * @class
	 * @augments wp.media.view.MediaFrame
	 * @augments wp.media.view.Frame
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 * @mixes wp.media.controller.StateMachine
	 */
	media.view.MediaFrame.Select = media.view.MediaFrame.extend({
		initialize: function() {
			// Call 'initialize' directly on the parent class.
			media.view.MediaFrame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				selection: [],
				library:   {},
				multiple:  false,
				state:    'library'
			});

			this.createSelection();
			this.createStates();
			this.bindHandlers();
		},

		/**
		 * Attach a selection collection to the frame.
		 *
		 * A selection is a collection of attachments used for a specific purpose
		 * by a media frame. e.g. Selecting an attachment (or many) to insert into
		 * post content.
		 *
		 * @see media.model.Selection
		 */
		createSelection: function() {
			var selection = this.options.selection;

			if ( ! (selection instanceof media.model.Selection) ) {
				this.options.selection = new media.model.Selection( selection, {
					multiple: this.options.multiple
				});
			}

			this._selection = {
				attachments: new media.model.Attachments(),
				difference: []
			};
		},

		/**
		 * Create the default states on the frame.
		 */
		createStates: function() {
			var options = this.options;

			if ( this.options.states ) {
				return;
			}

			// Add the default states.
			this.states.add([
				// Main states.
				new media.controller.Library({
					library:   media.query( options.library ),
					multiple:  options.multiple,
					title:     options.title,
					priority:  20
				})
			]);
		},

		/**
		 * Bind region mode event callbacks.
		 *
		 * @see media.controller.Region.render
		 */
		bindHandlers: function() {
			this.on( 'router:create:browse', this.createRouter, this );
			this.on( 'router:render:browse', this.browseRouter, this );
			this.on( 'content:create:browse', this.browseContent, this );
			this.on( 'content:render:upload', this.uploadContent, this );
			this.on( 'toolbar:create:select', this.createSelectToolbar, this );
		},

		/**
		 * Render callback for the router region in the `browse` mode.
		 *
		 * @param {wp.media.view.Router} routerView
		 */
		browseRouter: function( routerView ) {
			routerView.set({
				upload: {
					text:     l10n.uploadFilesTitle,
					priority: 20
				},
				browse: {
					text:     l10n.mediaLibraryTitle,
					priority: 40
				}
			});
		},

		/**
		 * Render callback for the content region in the `browse` mode.
		 *
		 * @param {wp.media.controller.Region} contentRegion
		 */
		browseContent: function( contentRegion ) {
			var state = this.state();

			this.$el.removeClass('hide-toolbar');

			// Browse our library of attachments.
			contentRegion.view = new media.view.AttachmentsBrowser({
				controller: this,
				collection: state.get('library'),
				selection:  state.get('selection'),
				model:      state,
				sortable:   state.get('sortable'),
				search:     state.get('searchable'),
				filters:    state.get('filterable'),
				date:       state.get('date'),
				display:    state.has('display') ? state.get('display') : state.get('displaySettings'),
				dragInfo:   state.get('dragInfo'),

				idealColumnWidth: state.get('idealColumnWidth'),
				suggestedWidth:   state.get('suggestedWidth'),
				suggestedHeight:  state.get('suggestedHeight'),

				AttachmentView: state.get('AttachmentView')
			});
		},

		/**
		 * Render callback for the content region in the `upload` mode.
		 */
		uploadContent: function() {
			this.$el.removeClass( 'hide-toolbar' );
			this.content.set( new media.view.UploaderInline({
				controller: this
			}) );
		},

		/**
		 * Toolbars
		 *
		 * @param {Object} toolbar
		 * @param {Object} [options={}]
		 * @this wp.media.controller.Region
		 */
		createSelectToolbar: function( toolbar, options ) {
			options = options || this.options.button || {};
			options.controller = this;

			toolbar.view = new media.view.Toolbar.Select( options );
		}
	});

	/**
	 * wp.media.view.MediaFrame.Post
	 *
	 * The frame for manipulating media on the Edit Post page.
	 *
	 * @class
	 * @augments wp.media.view.MediaFrame.Select
	 * @augments wp.media.view.MediaFrame
	 * @augments wp.media.view.Frame
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 * @mixes wp.media.controller.StateMachine
	 */
	media.view.MediaFrame.Post = media.view.MediaFrame.Select.extend({
		initialize: function() {
			this.counts = {
				audio: {
					count: media.view.settings.attachmentCounts.audio,
					state: 'playlist'
				},
				video: {
					count: media.view.settings.attachmentCounts.video,
					state: 'video-playlist'
				}
			};

			_.defaults( this.options, {
				multiple:  true,
				editing:   false,
				state:    'insert',
				metadata:  {}
			});

			// Call 'initialize' directly on the parent class.
			media.view.MediaFrame.Select.prototype.initialize.apply( this, arguments );
			this.createIframeStates();

		},

		/**
		 * Create the default states.
		 */
		createStates: function() {
			var options = this.options;

			this.states.add([
				// Main states.
				new media.controller.Library({
					id:         'insert',
					title:      l10n.insertMediaTitle,
					priority:   20,
					toolbar:    'main-insert',
					filterable: 'all',
					library:    media.query( options.library ),
					multiple:   options.multiple ? 'reset' : false,
					editable:   true,

					// If the user isn't allowed to edit fields,
					// can they still edit it locally?
					allowLocalEdits: true,

					// Show the attachment display settings.
					displaySettings: true,
					// Update user settings when users adjust the
					// attachment display settings.
					displayUserSettings: true
				}),

				new media.controller.Library({
					id:         'gallery',
					title:      l10n.createGalleryTitle,
					priority:   40,
					toolbar:    'main-gallery',
					filterable: 'uploaded',
					multiple:   'add',
					editable:   false,

					library:  media.query( _.defaults({
						type: 'image'
					}, options.library ) )
				}),

				// Embed states.
				new media.controller.Embed( { metadata: options.metadata } ),

				new media.controller.EditImage( { model: options.editImage } ),

				// Gallery states.
				new media.controller.GalleryEdit({
					library: options.selection,
					editing: options.editing,
					menu:    'gallery'
				}),

				new media.controller.GalleryAdd(),

				new media.controller.Library({
					id:         'playlist',
					title:      l10n.createPlaylistTitle,
					priority:   60,
					toolbar:    'main-playlist',
					filterable: 'uploaded',
					multiple:   'add',
					editable:   false,

					library:  media.query( _.defaults({
						type: 'audio'
					}, options.library ) )
				}),

				// Playlist states.
				new media.controller.CollectionEdit({
					type: 'audio',
					collectionType: 'playlist',
					title:          l10n.editPlaylistTitle,
					SettingsView:   media.view.Settings.Playlist,
					library:        options.selection,
					editing:        options.editing,
					menu:           'playlist',
					dragInfoText:   l10n.playlistDragInfo,
					dragInfo:       false
				}),

				new media.controller.CollectionAdd({
					type: 'audio',
					collectionType: 'playlist',
					title: l10n.addToPlaylistTitle
				}),

				new media.controller.Library({
					id:         'video-playlist',
					title:      l10n.createVideoPlaylistTitle,
					priority:   60,
					toolbar:    'main-video-playlist',
					filterable: 'uploaded',
					multiple:   'add',
					editable:   false,

					library:  media.query( _.defaults({
						type: 'video'
					}, options.library ) )
				}),

				new media.controller.CollectionEdit({
					type: 'video',
					collectionType: 'playlist',
					title:          l10n.editVideoPlaylistTitle,
					SettingsView:   media.view.Settings.Playlist,
					library:        options.selection,
					editing:        options.editing,
					menu:           'video-playlist',
					dragInfoText:   l10n.videoPlaylistDragInfo,
					dragInfo:       false
				}),

				new media.controller.CollectionAdd({
					type: 'video',
					collectionType: 'playlist',
					title: l10n.addToVideoPlaylistTitle
				})
			]);

			if ( media.view.settings.post.featuredImageId ) {
				this.states.add( new media.controller.FeaturedImage() );
			}
		},

		bindHandlers: function() {
			var handlers, checkCounts;

			media.view.MediaFrame.Select.prototype.bindHandlers.apply( this, arguments );

			this.on( 'activate', this.activate, this );

			// Only bother checking media type counts if one of the counts is zero
			checkCounts = _.find( this.counts, function( type ) {
				return type.count === 0;
			} );

			if ( typeof checkCounts !== 'undefined' ) {
				this.listenTo( media.model.Attachments.all, 'change:type', this.mediaTypeCounts );
			}

			this.on( 'menu:create:gallery', this.createMenu, this );
			this.on( 'menu:create:playlist', this.createMenu, this );
			this.on( 'menu:create:video-playlist', this.createMenu, this );
			this.on( 'toolbar:create:main-insert', this.createToolbar, this );
			this.on( 'toolbar:create:main-gallery', this.createToolbar, this );
			this.on( 'toolbar:create:main-playlist', this.createToolbar, this );
			this.on( 'toolbar:create:main-video-playlist', this.createToolbar, this );
			this.on( 'toolbar:create:featured-image', this.featuredImageToolbar, this );
			this.on( 'toolbar:create:main-embed', this.mainEmbedToolbar, this );

			handlers = {
				menu: {
					'default': 'mainMenu',
					'gallery': 'galleryMenu',
					'playlist': 'playlistMenu',
					'video-playlist': 'videoPlaylistMenu'
				},

				content: {
					'embed':          'embedContent',
					'edit-image':     'editImageContent',
					'edit-selection': 'editSelectionContent'
				},

				toolbar: {
					'main-insert':      'mainInsertToolbar',
					'main-gallery':     'mainGalleryToolbar',
					'gallery-edit':     'galleryEditToolbar',
					'gallery-add':      'galleryAddToolbar',
					'main-playlist':	'mainPlaylistToolbar',
					'playlist-edit':	'playlistEditToolbar',
					'playlist-add':		'playlistAddToolbar',
					'main-video-playlist': 'mainVideoPlaylistToolbar',
					'video-playlist-edit': 'videoPlaylistEditToolbar',
					'video-playlist-add': 'videoPlaylistAddToolbar'
				}
			};

			_.each( handlers, function( regionHandlers, region ) {
				_.each( regionHandlers, function( callback, handler ) {
					this.on( region + ':render:' + handler, this[ callback ], this );
				}, this );
			}, this );
		},

		activate: function() {
			// Hide menu items for states tied to particular media types if there are no items
			_.each( this.counts, function( type ) {
				if ( type.count < 1 ) {
					this.menuItemVisibility( type.state, 'hide' );
				}
			}, this );
		},

		mediaTypeCounts: function( model, attr ) {
			if ( typeof this.counts[ attr ] !== 'undefined' && this.counts[ attr ].count < 1 ) {
				this.counts[ attr ].count++;
				this.menuItemVisibility( this.counts[ attr ].state, 'show' );
			}
		},

		// Menus
		/**
		 * @param {wp.Backbone.View} view
		 */
		mainMenu: function( view ) {
			view.set({
				'library-separator': new media.View({
					className: 'separator',
					priority: 100
				})
			});
		},

		menuItemVisibility: function( state, visibility ) {
			var menu = this.menu.get();
			if ( visibility === 'hide' ) {
				menu.hide( state );
			} else if ( visibility === 'show' ) {
				menu.show( state );
			}
		},
		/**
		 * @param {wp.Backbone.View} view
		 */
		galleryMenu: function( view ) {
			var lastState = this.lastState(),
				previous = lastState && lastState.id,
				frame = this;

			view.set({
				cancel: {
					text:     l10n.cancelGalleryTitle,
					priority: 20,
					click:    function() {
						if ( previous ) {
							frame.setState( previous );
						} else {
							frame.close();
						}

						// Keep focus inside media modal
						// after canceling a gallery
						this.controller.modal.focusManager.focus();
					}
				},
				separateCancel: new media.View({
					className: 'separator',
					priority: 40
				})
			});
		},

		playlistMenu: function( view ) {
			var lastState = this.lastState(),
				previous = lastState && lastState.id,
				frame = this;

			view.set({
				cancel: {
					text:     l10n.cancelPlaylistTitle,
					priority: 20,
					click:    function() {
						if ( previous ) {
							frame.setState( previous );
						} else {
							frame.close();
						}
					}
				},
				separateCancel: new media.View({
					className: 'separator',
					priority: 40
				})
			});
		},

		videoPlaylistMenu: function( view ) {
			var lastState = this.lastState(),
				previous = lastState && lastState.id,
				frame = this;

			view.set({
				cancel: {
					text:     l10n.cancelVideoPlaylistTitle,
					priority: 20,
					click:    function() {
						if ( previous ) {
							frame.setState( previous );
						} else {
							frame.close();
						}
					}
				},
				separateCancel: new media.View({
					className: 'separator',
					priority: 40
				})
			});
		},

		// Content
		embedContent: function() {
			var view = new media.view.Embed({
				controller: this,
				model:      this.state()
			}).render();

			this.content.set( view );

			if ( ! isTouchDevice ) {
				view.url.focus();
			}
		},

		editSelectionContent: function() {
			var state = this.state(),
				selection = state.get('selection'),
				view;

			view = new media.view.AttachmentsBrowser({
				controller: this,
				collection: selection,
				selection:  selection,
				model:      state,
				sortable:   true,
				search:     false,
				dragInfo:   true,

				AttachmentView: media.view.Attachment.EditSelection
			}).render();

			view.toolbar.set( 'backToLibrary', {
				text:     l10n.returnToLibrary,
				priority: -100,

				click: function() {
					this.controller.content.mode('browse');
				}
			});

			// Browse our library of attachments.
			this.content.set( view );

			// Trigger the controller to set focus
			this.trigger( 'edit:selection', this );
		},

		editImageContent: function() {
			var image = this.state().get('image'),
				view = new media.view.EditImage( { model: image, controller: this } ).render();

			this.content.set( view );

			// after creating the wrapper view, load the actual editor via an ajax call
			view.loadEditor();

		},

		// Toolbars

		/**
		 * @param {wp.Backbone.View} view
		 */
		selectionStatusToolbar: function( view ) {
			var editable = this.state().get('editable');

			view.set( 'selection', new media.view.Selection({
				controller: this,
				collection: this.state().get('selection'),
				priority:   -40,

				// If the selection is editable, pass the callback to
				// switch the content mode.
				editable: editable && function() {
					this.controller.content.mode('edit-selection');
				}
			}).render() );
		},

		/**
		 * @param {wp.Backbone.View} view
		 */
		mainInsertToolbar: function( view ) {
			var controller = this;

			this.selectionStatusToolbar( view );

			view.set( 'insert', {
				style:    'primary',
				priority: 80,
				text:     l10n.insertIntoPost,
				requires: { selection: true },

				/**
				 * @fires wp.media.controller.State#insert
				 */
				click: function() {
					var state = controller.state(),
						selection = state.get('selection');

					controller.close();
					state.trigger( 'insert', selection ).reset();
				}
			});
		},

		/**
		 * @param {wp.Backbone.View} view
		 */
		mainGalleryToolbar: function( view ) {
			var controller = this;

			this.selectionStatusToolbar( view );

			view.set( 'gallery', {
				style:    'primary',
				text:     l10n.createNewGallery,
				priority: 60,
				requires: { selection: true },

				click: function() {
					var selection = controller.state().get('selection'),
						edit = controller.state('gallery-edit'),
						models = selection.where({ type: 'image' });

					edit.set( 'library', new media.model.Selection( models, {
						props:    selection.props.toJSON(),
						multiple: true
					}) );

					this.controller.setState('gallery-edit');

					// Keep focus inside media modal
					// after jumping to gallery view
					this.controller.modal.focusManager.focus();
				}
			});
		},

		mainPlaylistToolbar: function( view ) {
			var controller = this;

			this.selectionStatusToolbar( view );

			view.set( 'playlist', {
				style:    'primary',
				text:     l10n.createNewPlaylist,
				priority: 100,
				requires: { selection: true },

				click: function() {
					var selection = controller.state().get('selection'),
						edit = controller.state('playlist-edit'),
						models = selection.where({ type: 'audio' });

					edit.set( 'library', new media.model.Selection( models, {
						props:    selection.props.toJSON(),
						multiple: true
					}) );

					this.controller.setState('playlist-edit');

					// Keep focus inside media modal
					// after jumping to playlist view
					this.controller.modal.focusManager.focus();
				}
			});
		},

		mainVideoPlaylistToolbar: function( view ) {
			var controller = this;

			this.selectionStatusToolbar( view );

			view.set( 'video-playlist', {
				style:    'primary',
				text:     l10n.createNewVideoPlaylist,
				priority: 100,
				requires: { selection: true },

				click: function() {
					var selection = controller.state().get('selection'),
						edit = controller.state('video-playlist-edit'),
						models = selection.where({ type: 'video' });

					edit.set( 'library', new media.model.Selection( models, {
						props:    selection.props.toJSON(),
						multiple: true
					}) );

					this.controller.setState('video-playlist-edit');

					// Keep focus inside media modal
					// after jumping to video playlist view
					this.controller.modal.focusManager.focus();
				}
			});
		},

		featuredImageToolbar: function( toolbar ) {
			this.createSelectToolbar( toolbar, {
				text:  l10n.setFeaturedImage,
				state: this.options.state
			});
		},

		mainEmbedToolbar: function( toolbar ) {
			toolbar.view = new media.view.Toolbar.Embed({
				controller: this
			});
		},

		galleryEditToolbar: function() {
			var editing = this.state().get('editing');
			this.toolbar.set( new media.view.Toolbar({
				controller: this,
				items: {
					insert: {
						style:    'primary',
						text:     editing ? l10n.updateGallery : l10n.insertGallery,
						priority: 80,
						requires: { library: true },

						/**
						 * @fires wp.media.controller.State#update
						 */
						click: function() {
							var controller = this.controller,
								state = controller.state();

							controller.close();
							state.trigger( 'update', state.get('library') );

							// Restore and reset the default state.
							controller.setState( controller.options.state );
							controller.reset();
						}
					}
				}
			}) );
		},

		galleryAddToolbar: function() {
			this.toolbar.set( new media.view.Toolbar({
				controller: this,
				items: {
					insert: {
						style:    'primary',
						text:     l10n.addToGallery,
						priority: 80,
						requires: { selection: true },

						/**
						 * @fires wp.media.controller.State#reset
						 */
						click: function() {
							var controller = this.controller,
								state = controller.state(),
								edit = controller.state('gallery-edit');

							edit.get('library').add( state.get('selection').models );
							state.trigger('reset');
							controller.setState('gallery-edit');
						}
					}
				}
			}) );
		},

		playlistEditToolbar: function() {
			var editing = this.state().get('editing');
			this.toolbar.set( new media.view.Toolbar({
				controller: this,
				items: {
					insert: {
						style:    'primary',
						text:     editing ? l10n.updatePlaylist : l10n.insertPlaylist,
						priority: 80,
						requires: { library: true },

						/**
						 * @fires wp.media.controller.State#update
						 */
						click: function() {
							var controller = this.controller,
								state = controller.state();

							controller.close();
							state.trigger( 'update', state.get('library') );

							// Restore and reset the default state.
							controller.setState( controller.options.state );
							controller.reset();
						}
					}
				}
			}) );
		},

		playlistAddToolbar: function() {
			this.toolbar.set( new media.view.Toolbar({
				controller: this,
				items: {
					insert: {
						style:    'primary',
						text:     l10n.addToPlaylist,
						priority: 80,
						requires: { selection: true },

						/**
						 * @fires wp.media.controller.State#reset
						 */
						click: function() {
							var controller = this.controller,
								state = controller.state(),
								edit = controller.state('playlist-edit');

							edit.get('library').add( state.get('selection').models );
							state.trigger('reset');
							controller.setState('playlist-edit');
						}
					}
				}
			}) );
		},

		videoPlaylistEditToolbar: function() {
			var editing = this.state().get('editing');
			this.toolbar.set( new media.view.Toolbar({
				controller: this,
				items: {
					insert: {
						style:    'primary',
						text:     editing ? l10n.updateVideoPlaylist : l10n.insertVideoPlaylist,
						priority: 140,
						requires: { library: true },

						click: function() {
							var controller = this.controller,
								state = controller.state(),
								library = state.get('library');

							library.type = 'video';

							controller.close();
							state.trigger( 'update', library );

							// Restore and reset the default state.
							controller.setState( controller.options.state );
							controller.reset();
						}
					}
				}
			}) );
		},

		videoPlaylistAddToolbar: function() {
			this.toolbar.set( new media.view.Toolbar({
				controller: this,
				items: {
					insert: {
						style:    'primary',
						text:     l10n.addToVideoPlaylist,
						priority: 140,
						requires: { selection: true },

						click: function() {
							var controller = this.controller,
								state = controller.state(),
								edit = controller.state('video-playlist-edit');

							edit.get('library').add( state.get('selection').models );
							state.trigger('reset');
							controller.setState('video-playlist-edit');
						}
					}
				}
			}) );
		}
	});

	/**
	 * wp.media.view.MediaFrame.ImageDetails
	 *
	 * A media frame for manipulating an image that's already been inserted
	 * into a post.
	 *
	 * @class
	 * @augments wp.media.view.MediaFrame.Select
	 * @augments wp.media.view.MediaFrame
	 * @augments wp.media.view.Frame
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 * @mixes wp.media.controller.StateMachine
	 */
	media.view.MediaFrame.ImageDetails = media.view.MediaFrame.Select.extend({
		defaults: {
			id:      'image',
			url:     '',
			menu:    'image-details',
			content: 'image-details',
			toolbar: 'image-details',
			type:    'link',
			title:    l10n.imageDetailsTitle,
			priority: 120
		},

		initialize: function( options ) {
			this.image = new media.model.PostImage( options.metadata );
			this.options.selection = new media.model.Selection( this.image.attachment, { multiple: false } );
			media.view.MediaFrame.Select.prototype.initialize.apply( this, arguments );
		},

		bindHandlers: function() {
			media.view.MediaFrame.Select.prototype.bindHandlers.apply( this, arguments );
			this.on( 'menu:create:image-details', this.createMenu, this );
			this.on( 'content:create:image-details', this.imageDetailsContent, this );
			this.on( 'content:render:edit-image', this.editImageContent, this );
			this.on( 'toolbar:render:image-details', this.renderImageDetailsToolbar, this );
			// override the select toolbar
			this.on( 'toolbar:render:replace', this.renderReplaceImageToolbar, this );
		},

		createStates: function() {
			this.states.add([
				new media.controller.ImageDetails({
					image: this.image,
					editable: false
				}),
				new media.controller.ReplaceImage({
					id: 'replace-image',
					library:   media.query( { type: 'image' } ),
					image: this.image,
					multiple:  false,
					title:     l10n.imageReplaceTitle,
					toolbar: 'replace',
					priority:  80,
					displaySettings: true
				}),
				new media.controller.EditImage( {
					image: this.image,
					selection: this.options.selection
				} )
			]);
		},

		imageDetailsContent: function( options ) {
			options.view = new media.view.ImageDetails({
				controller: this,
				model: this.state().image,
				attachment: this.state().image.attachment
			});
		},

		editImageContent: function() {
			var state = this.state(),
				model = state.get('image'),
				view;

			if ( ! model ) {
				return;
			}

			view = new media.view.EditImage( { model: model, controller: this } ).render();

			this.content.set( view );

			// after bringing in the frame, load the actual editor via an ajax call
			view.loadEditor();

		},

		renderImageDetailsToolbar: function() {
			this.toolbar.set( new media.view.Toolbar({
				controller: this,
				items: {
					select: {
						style:    'primary',
						text:     l10n.update,
						priority: 80,

						click: function() {
							var controller = this.controller,
								state = controller.state();

							controller.close();

							// not sure if we want to use wp.media.string.image which will create a shortcode or
							// perhaps wp.html.string to at least to build the <img />
							state.trigger( 'update', controller.image.toJSON() );

							// Restore and reset the default state.
							controller.setState( controller.options.state );
							controller.reset();
						}
					}
				}
			}) );
		},

		renderReplaceImageToolbar: function() {
			var frame = this,
				lastState = frame.lastState(),
				previous = lastState && lastState.id;

			this.toolbar.set( new media.view.Toolbar({
				controller: this,
				items: {
					back: {
						text:     l10n.back,
						priority: 20,
						click:    function() {
							if ( previous ) {
								frame.setState( previous );
							} else {
								frame.close();
							}
						}
					},

					replace: {
						style:    'primary',
						text:     l10n.replace,
						priority: 80,

						click: function() {
							var controller = this.controller,
								state = controller.state(),
								selection = state.get( 'selection' ),
								attachment = selection.single();

							controller.close();

							controller.image.changeAttachment( attachment, state.display( attachment ) );

							// not sure if we want to use wp.media.string.image which will create a shortcode or
							// perhaps wp.html.string to at least to build the <img />
							state.trigger( 'replace', controller.image.toJSON() );

							// Restore and reset the default state.
							controller.setState( controller.options.state );
							controller.reset();
						}
					}
				}
			}) );
		}

	});

	/**
	 * wp.media.view.Modal
	 *
	 * A modal view, which the media modal uses as its default container.
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Modal = media.View.extend({
		tagName:  'div',
		template: media.template('media-modal'),

		attributes: {
			tabindex: 0
		},

		events: {
			'click .media-modal-backdrop, .media-modal-close': 'escapeHandler',
			'keydown': 'keydown'
		},

		initialize: function() {
			_.defaults( this.options, {
				container: document.body,
				title:     '',
				propagate: true,
				freeze:    true
			});

			this.focusManager = new media.view.FocusManager({
				el: this.el
			});
		},
		/**
		 * @returns {Object}
		 */
		prepare: function() {
			return {
				title: this.options.title
			};
		},

		/**
		 * @returns {wp.media.view.Modal} Returns itself to allow chaining
		 */
		attach: function() {
			if ( this.views.attached ) {
				return this;
			}

			if ( ! this.views.rendered ) {
				this.render();
			}

			this.$el.appendTo( this.options.container );

			// Manually mark the view as attached and trigger ready.
			this.views.attached = true;
			this.views.ready();

			return this.propagate('attach');
		},

		/**
		 * @returns {wp.media.view.Modal} Returns itself to allow chaining
		 */
		detach: function() {
			if ( this.$el.is(':visible') ) {
				this.close();
			}

			this.$el.detach();
			this.views.attached = false;
			return this.propagate('detach');
		},

		/**
		 * @returns {wp.media.view.Modal} Returns itself to allow chaining
		 */
		open: function() {
			var $el = this.$el,
				options = this.options,
				mceEditor;

			if ( $el.is(':visible') ) {
				return this;
			}

			if ( ! this.views.attached ) {
				this.attach();
			}

			// If the `freeze` option is set, record the window's scroll position.
			if ( options.freeze ) {
				this._freeze = {
					scrollTop: $( window ).scrollTop()
				};
			}

			// Disable page scrolling.
			$( 'body' ).addClass( 'modal-open' );

			$el.show();

			// Try to close the onscreen keyboard
			if ( 'ontouchend' in document ) {
				if ( ( mceEditor = window.tinymce && window.tinymce.activeEditor )  && ! mceEditor.isHidden() && mceEditor.iframeElement ) {
					mceEditor.iframeElement.focus();
					mceEditor.iframeElement.blur();

					setTimeout( function() {
						mceEditor.iframeElement.blur();
					}, 100 );
				}
			}

			this.$el.focus();

			return this.propagate('open');
		},

		/**
		 * @param {Object} options
		 * @returns {wp.media.view.Modal} Returns itself to allow chaining
		 */
		close: function( options ) {
			var freeze = this._freeze;

			if ( ! this.views.attached || ! this.$el.is(':visible') ) {
				return this;
			}

			// Enable page scrolling.
			$( 'body' ).removeClass( 'modal-open' );

			// Hide modal and remove restricted media modal tab focus once it's closed
			this.$el.hide().undelegate( 'keydown' );

			// Put focus back in useful location once modal is closed
			$('#wpbody-content').focus();

			this.propagate('close');

			// If the `freeze` option is set, restore the container's scroll position.
			if ( freeze ) {
				$( window ).scrollTop( freeze.scrollTop );
			}

			if ( options && options.escape ) {
				this.propagate('escape');
			}

			return this;
		},
		/**
		 * @returns {wp.media.view.Modal} Returns itself to allow chaining
		 */
		escape: function() {
			return this.close({ escape: true });
		},
		/**
		 * @param {Object} event
		 */
		escapeHandler: function( event ) {
			event.preventDefault();
			this.escape();
		},

		/**
		 * @param {Array|Object} content Views to register to '.media-modal-content'
		 * @returns {wp.media.view.Modal} Returns itself to allow chaining
		 */
		content: function( content ) {
			this.views.set( '.media-modal-content', content );
			return this;
		},

		/**
		 * Triggers a modal event and if the `propagate` option is set,
		 * forwards events to the modal's controller.
		 *
		 * @param {string} id
		 * @returns {wp.media.view.Modal} Returns itself to allow chaining
		 */
		propagate: function( id ) {
			this.trigger( id );

			if ( this.options.propagate ) {
				this.controller.trigger( id );
			}

			return this;
		},
		/**
		 * @param {Object} event
		 */
		keydown: function( event ) {
			// Close the modal when escape is pressed.
			if ( 27 === event.which && this.$el.is(':visible') ) {
				this.escape();
				event.stopImmediatePropagation();
			}
		}
	});

	/**
	 * wp.media.view.FocusManager
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.FocusManager = media.View.extend({

		events: {
			'keydown': 'constrainTabbing'
		},

		focus: function() { // Reset focus on first left menu item
			this.$('.media-menu-item').first().focus();
		},
		/**
		 * @param {Object} event
		 */
		constrainTabbing: function( event ) {
			var tabbables;

			// Look for the tab key.
			if ( 9 !== event.keyCode ) {
				return;
			}

			tabbables = this.$( ':tabbable' );

			// Keep tab focus within media modal while it's open
			if ( tabbables.last()[0] === event.target && ! event.shiftKey ) {
				tabbables.first().focus();
				return false;
			} else if ( tabbables.first()[0] === event.target && event.shiftKey ) {
				tabbables.last().focus();
				return false;
			}
		}

	});

	/**
	 * wp.media.view.UploaderWindow
	 *
	 * An uploader window that allows for dragging and dropping media.
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 *
	 * @param {object} [options]                   Options hash passed to the view.
	 * @param {object} [options.uploader]          Uploader properties.
	 * @param {jQuery} [options.uploader.browser]
	 * @param {jQuery} [options.uploader.dropzone] jQuery collection of the dropzone.
	 * @param {object} [options.uploader.params]
	 */
	media.view.UploaderWindow = media.View.extend({
		tagName:   'div',
		className: 'uploader-window',
		template:  media.template('uploader-window'),

		initialize: function() {
			var uploader;

			this.$browser = $('<a href="#" class="browser" />').hide().appendTo('body');

			uploader = this.options.uploader = _.defaults( this.options.uploader || {}, {
				dropzone:  this.$el,
				browser:   this.$browser,
				params:    {}
			});

			// Ensure the dropzone is a jQuery collection.
			if ( uploader.dropzone && ! (uploader.dropzone instanceof $) ) {
				uploader.dropzone = $( uploader.dropzone );
			}

			this.controller.on( 'activate', this.refresh, this );

			this.controller.on( 'detach', function() {
				this.$browser.remove();
			}, this );
		},

		refresh: function() {
			if ( this.uploader ) {
				this.uploader.refresh();
			}
		},

		ready: function() {
			var postId = media.view.settings.post.id,
				dropzone;

			// If the uploader already exists, bail.
			if ( this.uploader ) {
				return;
			}

			if ( postId ) {
				this.options.uploader.params.post_id = postId;
			}
			this.uploader = new wp.Uploader( this.options.uploader );

			dropzone = this.uploader.dropzone;
			dropzone.on( 'dropzone:enter', _.bind( this.show, this ) );
			dropzone.on( 'dropzone:leave', _.bind( this.hide, this ) );

			$( this.uploader ).on( 'uploader:ready', _.bind( this._ready, this ) );
		},

		_ready: function() {
			this.controller.trigger( 'uploader:ready' );
		},

		show: function() {
			var $el = this.$el.show();

			// Ensure that the animation is triggered by waiting until
			// the transparent element is painted into the DOM.
			_.defer( function() {
				$el.css({ opacity: 1 });
			});
		},

		hide: function() {
			var $el = this.$el.css({ opacity: 0 });

			media.transition( $el ).done( function() {
				// Transition end events are subject to race conditions.
				// Make sure that the value is set as intended.
				if ( '0' === $el.css('opacity') ) {
					$el.hide();
				}
			});

			// https://core.trac.wordpress.org/ticket/27341
			_.delay( function() {
				if ( '0' === $el.css('opacity') && $el.is(':visible') ) {
					$el.hide();
				}
			}, 500 );
		}
	});

	/**
	 * Creates a dropzone on WP editor instances (elements with .wp-editor-wrap
	 * or #wp-fullscreen-body) and relays drag'n'dropped files to a media workflow.
	 *
	 * wp.media.view.EditorUploader
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.EditorUploader = media.View.extend({
		tagName:   'div',
		className: 'uploader-editor',
		template:  media.template( 'uploader-editor' ),

		localDrag: false,
		overContainer: false,
		overDropzone: false,
		draggingFile: null,

		/**
		 * Bind drag'n'drop events to callbacks.
		 */
		initialize: function() {
			var self = this;

			this.initialized = false;

			// Bail if not enabled or UA does not support drag'n'drop or File API.
			if ( ! window.tinyMCEPreInit || ! window.tinyMCEPreInit.dragDropUpload || ! this.browserSupport() ) {
				return this;
			}

			this.$document = $(document);
			this.dropzones = [];
			this.files = [];

			this.$document.on( 'drop', '.uploader-editor', _.bind( this.drop, this ) );
			this.$document.on( 'dragover', '.uploader-editor', _.bind( this.dropzoneDragover, this ) );
			this.$document.on( 'dragleave', '.uploader-editor', _.bind( this.dropzoneDragleave, this ) );
			this.$document.on( 'click', '.uploader-editor', _.bind( this.click, this ) );

			this.$document.on( 'dragover', _.bind( this.containerDragover, this ) );
			this.$document.on( 'dragleave', _.bind( this.containerDragleave, this ) );

			this.$document.on( 'dragstart dragend drop', function( event ) {
				self.localDrag = event.type === 'dragstart';
			});

			this.initialized = true;
			return this;
		},

		/**
		 * Check browser support for drag'n'drop.
		 *
		 * @return Boolean
		 */
		browserSupport: function() {
			var supports = false, div = document.createElement('div');

			supports = ( 'draggable' in div ) || ( 'ondragstart' in div && 'ondrop' in div );
			supports = supports && !! ( window.File && window.FileList && window.FileReader );
			return supports;
		},

		isDraggingFile: function( event ) {
			if ( this.draggingFile !== null ) {
				return this.draggingFile;
			}

			if ( _.isUndefined( event.originalEvent ) || _.isUndefined( event.originalEvent.dataTransfer ) ) {
				return false;
			}

			this.draggingFile = _.indexOf( event.originalEvent.dataTransfer.types, 'Files' ) > -1 &&
				_.indexOf( event.originalEvent.dataTransfer.types, 'text/plain' ) === -1;

			return this.draggingFile;
		},

		refresh: function( e ) {
			var dropzone_id;
			for ( dropzone_id in this.dropzones ) {
				// Hide the dropzones only if dragging has left the screen.
				this.dropzones[ dropzone_id ].toggle( this.overContainer || this.overDropzone );
			}

			if ( ! _.isUndefined( e ) ) {
				$( e.target ).closest( '.uploader-editor' ).toggleClass( 'droppable', this.overDropzone );
			}

			if ( ! this.overContainer && ! this.overDropzone ) {
				this.draggingFile = null;
			}

			return this;
		},

		render: function() {
			if ( ! this.initialized ) {
				return this;
			}

			media.View.prototype.render.apply( this, arguments );
			$( '.wp-editor-wrap, #wp-fullscreen-body' ).each( _.bind( this.attach, this ) );
			return this;
		},

		attach: function( index, editor ) {
			// Attach a dropzone to an editor.
			var dropzone = this.$el.clone();
			this.dropzones.push( dropzone );
			$( editor ).append( dropzone );
			return this;
		},

		/**
		 * When a file is dropped on the editor uploader, open up an editor media workflow
		 * and upload the file immediately.
		 *
		 * @param  {jQuery.Event} event The 'drop' event.
		 */
		drop: function( event ) {
			var $wrap = null, uploadView;

			this.containerDragleave( event );
			this.dropzoneDragleave( event );

			this.files = event.originalEvent.dataTransfer.files;
			if ( this.files.length < 1 ) {
				return;
			}

			// Set the active editor to the drop target.
			$wrap = $( event.target ).parents( '.wp-editor-wrap' );
			if ( $wrap.length > 0 && $wrap[0].id ) {
				window.wpActiveEditor = $wrap[0].id.slice( 3, -5 );
			}

			if ( ! this.workflow ) {
				this.workflow = wp.media.editor.open( 'content', {
					frame:    'post',
					state:    'insert',
					title:    wp.media.view.l10n.addMedia,
					multiple: true
				});
				uploadView = this.workflow.uploader;
				if ( uploadView.uploader && uploadView.uploader.ready ) {
					this.addFiles.apply( this );
				} else {
					this.workflow.on( 'uploader:ready', this.addFiles, this );
				}
			} else {
				this.workflow.state().reset();
				this.addFiles.apply( this );
				this.workflow.open();
			}

			return false;
		},

		/**
		 * Add the files to the uploader.
		 */
		addFiles: function() {
			if ( this.files.length ) {
				this.workflow.uploader.uploader.uploader.addFile( _.toArray( this.files ) );
				this.files = [];
			}
			return this;
		},

		containerDragover: function( event ) {
			if ( this.localDrag || ! this.isDraggingFile( event ) ) {
				return;
			}

			this.overContainer = true;
			this.refresh();
		},

		containerDragleave: function() {
			this.overContainer = false;

			// Throttle dragleave because it's called when bouncing from some elements to others.
			_.delay( _.bind( this.refresh, this ), 50 );
		},

		dropzoneDragover: function( event ) {
			if ( this.localDrag || ! this.isDraggingFile( event ) ) {
				return;
			}

			this.overDropzone = true;
			this.refresh( event );
			return false;
		},

		dropzoneDragleave: function( e ) {
			this.overDropzone = false;
			_.delay( _.bind( this.refresh, this, e ), 50 );
		},

		click: function( e ) {
			// In the rare case where the dropzone gets stuck, hide it on click.
			this.containerDragleave( e );
			this.dropzoneDragleave( e );
			this.localDrag = false;
		}
	});

	/**
	 * wp.media.view.UploaderInline
	 *
	 * The inline uploader that shows up in the 'Upload Files' tab.
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.UploaderInline = media.View.extend({
		tagName:   'div',
		className: 'uploader-inline',
		template:  media.template('uploader-inline'),

		events: {
			'click .close': 'hide'
		},

		initialize: function() {
			_.defaults( this.options, {
				message: '',
				status:  true,
				canClose: false
			});

			if ( ! this.options.$browser && this.controller.uploader ) {
				this.options.$browser = this.controller.uploader.$browser;
			}

			if ( _.isUndefined( this.options.postId ) ) {
				this.options.postId = media.view.settings.post.id;
			}

			if ( this.options.status ) {
				this.views.set( '.upload-inline-status', new media.view.UploaderStatus({
					controller: this.controller
				}) );
			}
		},

		prepare: function() {
			var suggestedWidth = this.controller.state().get('suggestedWidth'),
				suggestedHeight = this.controller.state().get('suggestedHeight'),
				data = {};

			data.message = this.options.message;
			data.canClose = this.options.canClose;

			if ( suggestedWidth && suggestedHeight ) {
				data.suggestedWidth = suggestedWidth;
				data.suggestedHeight = suggestedHeight;
			}

			return data;
		},
		/**
		 * @returns {wp.media.view.UploaderInline} Returns itself to allow chaining
		 */
		dispose: function() {
			if ( this.disposing ) {
				/**
				 * call 'dispose' directly on the parent class
				 */
				return media.View.prototype.dispose.apply( this, arguments );
			}

			// Run remove on `dispose`, so we can be sure to refresh the
			// uploader with a view-less DOM. Track whether we're disposing
			// so we don't trigger an infinite loop.
			this.disposing = true;
			return this.remove();
		},
		/**
		 * @returns {wp.media.view.UploaderInline} Returns itself to allow chaining
		 */
		remove: function() {
			/**
			 * call 'remove' directly on the parent class
			 */
			var result = media.View.prototype.remove.apply( this, arguments );

			_.defer( _.bind( this.refresh, this ) );
			return result;
		},

		refresh: function() {
			var uploader = this.controller.uploader;

			if ( uploader ) {
				uploader.refresh();
			}
		},
		/**
		 * @returns {wp.media.view.UploaderInline}
		 */
		ready: function() {
			var $browser = this.options.$browser,
				$placeholder;

			if ( this.controller.uploader ) {
				$placeholder = this.$('.browser');

				// Check if we've already replaced the placeholder.
				if ( $placeholder[0] === $browser[0] ) {
					return;
				}

				$browser.detach().text( $placeholder.text() );
				$browser[0].className = $placeholder[0].className;
				$placeholder.replaceWith( $browser.show() );
			}

			this.refresh();
			return this;
		},
		show: function() {
			this.$el.removeClass( 'hidden' );
		},
		hide: function() {
			this.$el.addClass( 'hidden' );
		}

	});

	/**
	 * wp.media.view.UploaderStatus
	 *
	 * An uploader status for on-going uploads.
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.UploaderStatus = media.View.extend({
		className: 'media-uploader-status',
		template:  media.template('uploader-status'),

		events: {
			'click .upload-dismiss-errors': 'dismiss'
		},

		initialize: function() {
			this.queue = wp.Uploader.queue;
			this.queue.on( 'add remove reset', this.visibility, this );
			this.queue.on( 'add remove reset change:percent', this.progress, this );
			this.queue.on( 'add remove reset change:uploading', this.info, this );

			this.errors = wp.Uploader.errors;
			this.errors.reset();
			this.errors.on( 'add remove reset', this.visibility, this );
			this.errors.on( 'add', this.error, this );
		},
		/**
		 * @global wp.Uploader
		 * @returns {wp.media.view.UploaderStatus}
		 */
		dispose: function() {
			wp.Uploader.queue.off( null, null, this );
			/**
			 * call 'dispose' directly on the parent class
			 */
			media.View.prototype.dispose.apply( this, arguments );
			return this;
		},

		visibility: function() {
			this.$el.toggleClass( 'uploading', !! this.queue.length );
			this.$el.toggleClass( 'errors', !! this.errors.length );
			this.$el.toggle( !! this.queue.length || !! this.errors.length );
		},

		ready: function() {
			_.each({
				'$bar':      '.media-progress-bar div',
				'$index':    '.upload-index',
				'$total':    '.upload-total',
				'$filename': '.upload-filename'
			}, function( selector, key ) {
				this[ key ] = this.$( selector );
			}, this );

			this.visibility();
			this.progress();
			this.info();
		},

		progress: function() {
			var queue = this.queue,
				$bar = this.$bar;

			if ( ! $bar || ! queue.length ) {
				return;
			}

			$bar.width( ( queue.reduce( function( memo, attachment ) {
				if ( ! attachment.get('uploading') ) {
					return memo + 100;
				}

				var percent = attachment.get('percent');
				return memo + ( _.isNumber( percent ) ? percent : 100 );
			}, 0 ) / queue.length ) + '%' );
		},

		info: function() {
			var queue = this.queue,
				index = 0, active;

			if ( ! queue.length ) {
				return;
			}

			active = this.queue.find( function( attachment, i ) {
				index = i;
				return attachment.get('uploading');
			});

			this.$index.text( index + 1 );
			this.$total.text( queue.length );
			this.$filename.html( active ? this.filename( active.get('filename') ) : '' );
		},
		/**
		 * @param {string} filename
		 * @returns {string}
		 */
		filename: function( filename ) {
			return media.truncate( _.escape( filename ), 24 );
		},
		/**
		 * @param {Backbone.Model} error
		 */
		error: function( error ) {
			this.views.add( '.upload-errors', new media.view.UploaderStatusError({
				filename: this.filename( error.get('file').name ),
				message:  error.get('message')
			}), { at: 0 });
		},

		/**
		 * @global wp.Uploader
		 *
		 * @param {Object} event
		 */
		dismiss: function( event ) {
			var errors = this.views.get('.upload-errors');

			event.preventDefault();

			if ( errors ) {
				_.invoke( errors, 'remove' );
			}
			wp.Uploader.errors.reset();
		}
	});

	/**
	 * wp.media.view.UploaderStatusError
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.UploaderStatusError = media.View.extend({
		className: 'upload-error',
		template:  media.template('uploader-status-error')
	});

	/**
	 * wp.media.view.Toolbar
	 *
	 * A toolbar which consists of a primary and a secondary section. Each sections
	 * can be filled with views.
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Toolbar = media.View.extend({
		tagName:   'div',
		className: 'media-toolbar',

		initialize: function() {
			var state = this.controller.state(),
				selection = this.selection = state.get('selection'),
				library = this.library = state.get('library');

			this._views = {};

			// The toolbar is composed of two `PriorityList` views.
			this.primary   = new media.view.PriorityList();
			this.secondary = new media.view.PriorityList();
			this.primary.$el.addClass('media-toolbar-primary search-form');
			this.secondary.$el.addClass('media-toolbar-secondary');

			this.views.set([ this.secondary, this.primary ]);

			if ( this.options.items ) {
				this.set( this.options.items, { silent: true });
			}

			if ( ! this.options.silent ) {
				this.render();
			}

			if ( selection ) {
				selection.on( 'add remove reset', this.refresh, this );
			}

			if ( library ) {
				library.on( 'add remove reset', this.refresh, this );
			}
		},
		/**
		 * @returns {wp.media.view.Toolbar} Returns itsef to allow chaining
		 */
		dispose: function() {
			if ( this.selection ) {
				this.selection.off( null, null, this );
			}

			if ( this.library ) {
				this.library.off( null, null, this );
			}
			/**
			 * call 'dispose' directly on the parent class
			 */
			return media.View.prototype.dispose.apply( this, arguments );
		},

		ready: function() {
			this.refresh();
		},

		/**
		 * @param {string} id
		 * @param {Backbone.View|Object} view
		 * @param {Object} [options={}]
		 * @returns {wp.media.view.Toolbar} Returns itself to allow chaining
		 */
		set: function( id, view, options ) {
			var list;
			options = options || {};

			// Accept an object with an `id` : `view` mapping.
			if ( _.isObject( id ) ) {
				_.each( id, function( view, id ) {
					this.set( id, view, { silent: true });
				}, this );

			} else {
				if ( ! ( view instanceof Backbone.View ) ) {
					view.classes = [ 'media-button-' + id ].concat( view.classes || [] );
					view = new media.view.Button( view ).render();
				}

				view.controller = view.controller || this.controller;

				this._views[ id ] = view;

				list = view.options.priority < 0 ? 'secondary' : 'primary';
				this[ list ].set( id, view, options );
			}

			if ( ! options.silent ) {
				this.refresh();
			}

			return this;
		},
		/**
		 * @param {string} id
		 * @returns {wp.media.view.Button}
		 */
		get: function( id ) {
			return this._views[ id ];
		},
		/**
		 * @param {string} id
		 * @param {Object} options
		 * @returns {wp.media.view.Toolbar} Returns itself to allow chaining
		 */
		unset: function( id, options ) {
			delete this._views[ id ];
			this.primary.unset( id, options );
			this.secondary.unset( id, options );

			if ( ! options || ! options.silent ) {
				this.refresh();
			}
			return this;
		},

		refresh: function() {
			var state = this.controller.state(),
				library = state.get('library'),
				selection = state.get('selection');

			_.each( this._views, function( button ) {
				if ( ! button.model || ! button.options || ! button.options.requires ) {
					return;
				}

				var requires = button.options.requires,
					disabled = false;

				// Prevent insertion of attachments if any of them are still uploading
				disabled = _.some( selection.models, function( attachment ) {
					return attachment.get('uploading') === true;
				});

				if ( requires.selection && selection && ! selection.length ) {
					disabled = true;
				} else if ( requires.library && library && ! library.length ) {
					disabled = true;
				}
				button.model.set( 'disabled', disabled );
			});
		}
	});

	/**
	 * wp.media.view.Toolbar.Select
	 *
	 * @class
	 * @augments wp.media.view.Toolbar
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Toolbar.Select = media.view.Toolbar.extend({
		initialize: function() {
			var options = this.options;

			_.bindAll( this, 'clickSelect' );

			_.defaults( options, {
				event: 'select',
				state: false,
				reset: true,
				close: true,
				text:  l10n.select,

				// Does the button rely on the selection?
				requires: {
					selection: true
				}
			});

			options.items = _.defaults( options.items || {}, {
				select: {
					style:    'primary',
					text:     options.text,
					priority: 80,
					click:    this.clickSelect,
					requires: options.requires
				}
			});
			// Call 'initialize' directly on the parent class.
			media.view.Toolbar.prototype.initialize.apply( this, arguments );
		},

		clickSelect: function() {
			var options = this.options,
				controller = this.controller;

			if ( options.close ) {
				controller.close();
			}

			if ( options.event ) {
				controller.state().trigger( options.event );
			}

			if ( options.state ) {
				controller.setState( options.state );
			}

			if ( options.reset ) {
				controller.reset();
			}
		}
	});

	/**
	 * wp.media.view.Toolbar.Embed
	 *
	 * @class
	 * @augments wp.media.view.Toolbar.Select
	 * @augments wp.media.view.Toolbar
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Toolbar.Embed = media.view.Toolbar.Select.extend({
		initialize: function() {
			_.defaults( this.options, {
				text: l10n.insertIntoPost,
				requires: false
			});
			// Call 'initialize' directly on the parent class.
			media.view.Toolbar.Select.prototype.initialize.apply( this, arguments );
		},

		refresh: function() {
			var url = this.controller.state().props.get('url');
			this.get('select').model.set( 'disabled', ! url || url === 'http://' );
			/**
			 * call 'refresh' directly on the parent class
			 */
			media.view.Toolbar.Select.prototype.refresh.apply( this, arguments );
		}
	});

	/**
	 * wp.media.view.Button
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Button = media.View.extend({
		tagName:    'a',
		className:  'media-button',
		attributes: { href: '#' },

		events: {
			'click': 'click'
		},

		defaults: {
			text:     '',
			style:    '',
			size:     'large',
			disabled: false
		},

		initialize: function() {
			/**
			 * Create a model with the provided `defaults`.
			 *
			 * @member {Backbone.Model}
			 */
			this.model = new Backbone.Model( this.defaults );

			// If any of the `options` have a key from `defaults`, apply its
			// value to the `model` and remove it from the `options object.
			_.each( this.defaults, function( def, key ) {
				var value = this.options[ key ];
				if ( _.isUndefined( value ) ) {
					return;
				}

				this.model.set( key, value );
				delete this.options[ key ];
			}, this );

			this.model.on( 'change', this.render, this );
		},
		/**
		 * @returns {wp.media.view.Button} Returns itself to allow chaining
		 */
		render: function() {
			var classes = [ 'button', this.className ],
				model = this.model.toJSON();

			if ( model.style ) {
				classes.push( 'button-' + model.style );
			}

			if ( model.size ) {
				classes.push( 'button-' + model.size );
			}

			classes = _.uniq( classes.concat( this.options.classes ) );
			this.el.className = classes.join(' ');

			this.$el.attr( 'disabled', model.disabled );
			this.$el.text( this.model.get('text') );

			return this;
		},
		/**
		 * @param {Object} event
		 */
		click: function( event ) {
			if ( '#' === this.attributes.href ) {
				event.preventDefault();
			}

			if ( this.options.click && ! this.model.get('disabled') ) {
				this.options.click.apply( this, arguments );
			}
		}
	});

	/**
	 * wp.media.view.ButtonGroup
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.ButtonGroup = media.View.extend({
		tagName:   'div',
		className: 'button-group button-large media-button-group',

		initialize: function() {
			/**
			 * @member {wp.media.view.Button[]}
			 */
			this.buttons = _.map( this.options.buttons || [], function( button ) {
				if ( button instanceof Backbone.View ) {
					return button;
				} else {
					return new media.view.Button( button ).render();
				}
			});

			delete this.options.buttons;

			if ( this.options.classes ) {
				this.$el.addClass( this.options.classes );
			}
		},

		/**
		 * @returns {wp.media.view.ButtonGroup}
		 */
		render: function() {
			this.$el.html( $( _.pluck( this.buttons, 'el' ) ).detach() );
			return this;
		}
	});

	/**
	 * wp.media.view.PriorityList
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.PriorityList = media.View.extend({
		tagName:   'div',

		initialize: function() {
			this._views = {};

			this.set( _.extend( {}, this._views, this.options.views ), { silent: true });
			delete this.options.views;

			if ( ! this.options.silent ) {
				this.render();
			}
		},
		/**
		 * @param {string} id
		 * @param {wp.media.View|Object} view
		 * @param {Object} options
		 * @returns {wp.media.view.PriorityList} Returns itself to allow chaining
		 */
		set: function( id, view, options ) {
			var priority, views, index;

			options = options || {};

			// Accept an object with an `id` : `view` mapping.
			if ( _.isObject( id ) ) {
				_.each( id, function( view, id ) {
					this.set( id, view );
				}, this );
				return this;
			}

			if ( ! (view instanceof Backbone.View) ) {
				view = this.toView( view, id, options );
			}
			view.controller = view.controller || this.controller;

			this.unset( id );

			priority = view.options.priority || 10;
			views = this.views.get() || [];

			_.find( views, function( existing, i ) {
				if ( existing.options.priority > priority ) {
					index = i;
					return true;
				}
			});

			this._views[ id ] = view;
			this.views.add( view, {
				at: _.isNumber( index ) ? index : views.length || 0
			});

			return this;
		},
		/**
		 * @param {string} id
		 * @returns {wp.media.View}
		 */
		get: function( id ) {
			return this._views[ id ];
		},
		/**
		 * @param {string} id
		 * @returns {wp.media.view.PriorityList}
		 */
		unset: function( id ) {
			var view = this.get( id );

			if ( view ) {
				view.remove();
			}

			delete this._views[ id ];
			return this;
		},
		/**
		 * @param {Object} options
		 * @returns {wp.media.View}
		 */
		toView: function( options ) {
			return new media.View( options );
		}
	});

	/**
	 * wp.media.view.MenuItem
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.MenuItem = media.View.extend({
		tagName:   'a',
		className: 'media-menu-item',

		attributes: {
			href: '#'
		},

		events: {
			'click': '_click'
		},
		/**
		 * @param {Object} event
		 */
		_click: function( event ) {
			var clickOverride = this.options.click;

			if ( event ) {
				event.preventDefault();
			}

			if ( clickOverride ) {
				clickOverride.call( this );
			} else {
				this.click();
			}

			// When selecting a tab along the left side,
			// focus should be transferred into the main panel
			if ( ! isTouchDevice ) {
				$('.media-frame-content input').first().focus();
			}
		},

		click: function() {
			var state = this.options.state;

			if ( state ) {
				this.controller.setState( state );
				this.views.parent.$el.removeClass( 'visible' ); // TODO: or hide on any click, see below
			}
		},
		/**
		 * @returns {wp.media.view.MenuItem} returns itself to allow chaining
		 */
		render: function() {
			var options = this.options;

			if ( options.text ) {
				this.$el.text( options.text );
			} else if ( options.html ) {
				this.$el.html( options.html );
			}

			return this;
		}
	});

	/**
	 * wp.media.view.Menu
	 *
	 * @class
	 * @augments wp.media.view.PriorityList
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Menu = media.view.PriorityList.extend({
		tagName:   'div',
		className: 'media-menu',
		property:  'state',
		ItemView:  media.view.MenuItem,
		region:    'menu',

		/* TODO: alternatively hide on any click anywhere
		events: {
			'click': 'click'
		},

		click: function() {
			this.$el.removeClass( 'visible' );
		},
		*/

		/**
		 * @param {Object} options
		 * @param {string} id
		 * @returns {wp.media.View}
		 */
		toView: function( options, id ) {
			options = options || {};
			options[ this.property ] = options[ this.property ] || id;
			return new this.ItemView( options ).render();
		},

		ready: function() {
			/**
			 * call 'ready' directly on the parent class
			 */
			media.view.PriorityList.prototype.ready.apply( this, arguments );
			this.visibility();
		},

		set: function() {
			/**
			 * call 'set' directly on the parent class
			 */
			media.view.PriorityList.prototype.set.apply( this, arguments );
			this.visibility();
		},

		unset: function() {
			/**
			 * call 'unset' directly on the parent class
			 */
			media.view.PriorityList.prototype.unset.apply( this, arguments );
			this.visibility();
		},

		visibility: function() {
			var region = this.region,
				view = this.controller[ region ].get(),
				views = this.views.get(),
				hide = ! views || views.length < 2;

			if ( this === view ) {
				this.controller.$el.toggleClass( 'hide-' + region, hide );
			}
		},
		/**
		 * @param {string} id
		 */
		select: function( id ) {
			var view = this.get( id );

			if ( ! view ) {
				return;
			}

			this.deselect();
			view.$el.addClass('active');
		},

		deselect: function() {
			this.$el.children().removeClass('active');
		},

		hide: function( id ) {
			var view = this.get( id );

			if ( ! view ) {
				return;
			}

			view.$el.addClass('hidden');
		},

		show: function( id ) {
			var view = this.get( id );

			if ( ! view ) {
				return;
			}

			view.$el.removeClass('hidden');
		}
	});

	/**
	 * wp.media.view.RouterItem
	 *
	 * @class
	 * @augments wp.media.view.MenuItem
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.RouterItem = media.view.MenuItem.extend({
		/**
		 * On click handler to activate the content region's corresponding mode.
		 */
		click: function() {
			var contentMode = this.options.contentMode;
			if ( contentMode ) {
				this.controller.content.mode( contentMode );
			}
		}
	});

	/**
	 * wp.media.view.Router
	 *
	 * @class
	 * @augments wp.media.view.Menu
	 * @augments wp.media.view.PriorityList
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Router = media.view.Menu.extend({
		tagName:   'div',
		className: 'media-router',
		property:  'contentMode',
		ItemView:  media.view.RouterItem,
		region:    'router',

		initialize: function() {
			this.controller.on( 'content:render', this.update, this );
			// Call 'initialize' directly on the parent class.
			media.view.Menu.prototype.initialize.apply( this, arguments );
		},

		update: function() {
			var mode = this.controller.content.mode();
			if ( mode ) {
				this.select( mode );
			}
		}
	});

	/**
	 * wp.media.view.Sidebar
	 *
	 * @class
	 * @augments wp.media.view.PriorityList
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Sidebar = media.view.PriorityList.extend({
		className: 'media-sidebar'
	});

	/**
	 * wp.media.view.Attachment
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachment = media.View.extend({
		tagName:   'li',
		className: 'attachment',
		template:  media.template('attachment'),

		attributes: function() {
			return {
				'tabIndex':     0,
				'role':         'checkbox',
				'aria-label':   this.model.get( 'title' ),
				'aria-checked': false,
				'data-id':      this.model.get( 'id' )
			};
		},

		events: {
			'click .js--select-attachment':   'toggleSelectionHandler',
			'change [data-setting]':          'updateSetting',
			'change [data-setting] input':    'updateSetting',
			'change [data-setting] select':   'updateSetting',
			'change [data-setting] textarea': 'updateSetting',
			'click .close':                   'removeFromLibrary',
			'click .check':                   'checkClickHandler',
			'click a':                        'preventDefault',
			'keydown .close':                 'removeFromLibrary',
			'keydown':                        'toggleSelectionHandler'
		},

		buttons: {},

		initialize: function() {
			var selection = this.options.selection,
				options = _.defaults( this.options, {
					rerenderOnModelChange: true
				} );

			if ( options.rerenderOnModelChange ) {
				this.model.on( 'change', this.render, this );
			} else {
				this.model.on( 'change:percent', this.progress, this );
			}
			this.model.on( 'change:title', this._syncTitle, this );
			this.model.on( 'change:caption', this._syncCaption, this );
			this.model.on( 'change:artist', this._syncArtist, this );
			this.model.on( 'change:album', this._syncAlbum, this );

			// Update the selection.
			this.model.on( 'add', this.select, this );
			this.model.on( 'remove', this.deselect, this );
			if ( selection ) {
				selection.on( 'reset', this.updateSelect, this );
				// Update the model's details view.
				this.model.on( 'selection:single selection:unsingle', this.details, this );
				this.details( this.model, this.controller.state().get('selection') );
			}

			this.listenTo( this.controller, 'attachment:compat:waiting attachment:compat:ready', this.updateSave );
		},
		/**
		 * @returns {wp.media.view.Attachment} Returns itself to allow chaining
		 */
		dispose: function() {
			var selection = this.options.selection;

			// Make sure all settings are saved before removing the view.
			this.updateAll();

			if ( selection ) {
				selection.off( null, null, this );
			}
			/**
			 * call 'dispose' directly on the parent class
			 */
			media.View.prototype.dispose.apply( this, arguments );
			return this;
		},
		/**
		 * @returns {wp.media.view.Attachment} Returns itself to allow chaining
		 */
		render: function() {
			var options = _.defaults( this.model.toJSON(), {
					orientation:   'landscape',
					uploading:     false,
					type:          '',
					subtype:       '',
					icon:          '',
					filename:      '',
					caption:       '',
					title:         '',
					dateFormatted: '',
					width:         '',
					height:        '',
					compat:        false,
					alt:           '',
					description:   ''
				}, this.options );

			options.buttons  = this.buttons;
			options.describe = this.controller.state().get('describe');

			if ( 'image' === options.type ) {
				options.size = this.imageSize();
			}

			options.can = {};
			if ( options.nonces ) {
				options.can.remove = !! options.nonces['delete'];
				options.can.save = !! options.nonces.update;
			}

			if ( this.controller.state().get('allowLocalEdits') ) {
				options.allowLocalEdits = true;
			}

			if ( options.uploading && ! options.percent ) {
				options.percent = 0;
			}

			this.views.detach();
			this.$el.html( this.template( options ) );

			this.$el.toggleClass( 'uploading', options.uploading );

			if ( options.uploading ) {
				this.$bar = this.$('.media-progress-bar div');
			} else {
				delete this.$bar;
			}

			// Check if the model is selected.
			this.updateSelect();

			// Update the save status.
			this.updateSave();

			this.views.render();

			return this;
		},

		progress: function() {
			if ( this.$bar && this.$bar.length ) {
				this.$bar.width( this.model.get('percent') + '%' );
			}
		},

		/**
		 * @param {Object} event
		 */
		toggleSelectionHandler: function( event ) {
			var method;

			// Don't do anything inside inputs.
			if ( 'INPUT' === event.target.nodeName ) {
				return;
			}

			// Catch arrow events
			if ( 37 === event.keyCode || 38 === event.keyCode || 39 === event.keyCode || 40 === event.keyCode ) {
				this.controller.trigger( 'attachment:keydown:arrow', event );
				return;
			}

			// Catch enter and space events
			if ( 'keydown' === event.type && 13 !== event.keyCode && 32 !== event.keyCode ) {
				return;
			}

			event.preventDefault();

			// In the grid view, bubble up an edit:attachment event to the controller.
			if ( this.controller.isModeActive( 'grid' ) ) {
				if ( this.controller.isModeActive( 'edit' ) ) {
					// Pass the current target to restore focus when closing
					this.controller.trigger( 'edit:attachment', this.model, event.currentTarget );
					return;
				}

				if ( this.controller.isModeActive( 'select' ) ) {
					method = 'toggle';
				}
			}

			if ( event.shiftKey ) {
				method = 'between';
			} else if ( event.ctrlKey || event.metaKey ) {
				method = 'toggle';
			}

			this.toggleSelection({
				method: method
			});

			this.controller.trigger( 'selection:toggle' );
		},
		/**
		 * @param {Object} options
		 */
		toggleSelection: function( options ) {
			var collection = this.collection,
				selection = this.options.selection,
				model = this.model,
				method = options && options.method,
				single, models, singleIndex, modelIndex;

			if ( ! selection ) {
				return;
			}

			single = selection.single();
			method = _.isUndefined( method ) ? selection.multiple : method;

			// If the `method` is set to `between`, select all models that
			// exist between the current and the selected model.
			if ( 'between' === method && single && selection.multiple ) {
				// If the models are the same, short-circuit.
				if ( single === model ) {
					return;
				}

				singleIndex = collection.indexOf( single );
				modelIndex  = collection.indexOf( this.model );

				if ( singleIndex < modelIndex ) {
					models = collection.models.slice( singleIndex, modelIndex + 1 );
				} else {
					models = collection.models.slice( modelIndex, singleIndex + 1 );
				}

				selection.add( models );
				selection.single( model );
				return;

			// If the `method` is set to `toggle`, just flip the selection
			// status, regardless of whether the model is the single model.
			} else if ( 'toggle' === method ) {
				selection[ this.selected() ? 'remove' : 'add' ]( model );
				selection.single( model );
				return;
			} else if ( 'add' === method ) {
				selection.add( model );
				selection.single( model );
				return;
			}

			// Fixes bug that loses focus when selecting a featured image
			if ( ! method ) {
				method = 'add';
			}

			if ( method !== 'add' ) {
				method = 'reset';
			}

			if ( this.selected() ) {
				// If the model is the single model, remove it.
				// If it is not the same as the single model,
				// it now becomes the single model.
				selection[ single === model ? 'remove' : 'single' ]( model );
			} else {
				// If the model is not selected, run the `method` on the
				// selection. By default, we `reset` the selection, but the
				// `method` can be set to `add` the model to the selection.
				selection[ method ]( model );
				selection.single( model );
			}
		},

		updateSelect: function() {
			this[ this.selected() ? 'select' : 'deselect' ]();
		},
		/**
		 * @returns {unresolved|Boolean}
		 */
		selected: function() {
			var selection = this.options.selection;
			if ( selection ) {
				return !! selection.get( this.model.cid );
			}
		},
		/**
		 * @param {Backbone.Model} model
		 * @param {Backbone.Collection} collection
		 */
		select: function( model, collection ) {
			var selection = this.options.selection,
				controller = this.controller;

			// Check if a selection exists and if it's the collection provided.
			// If they're not the same collection, bail; we're in another
			// selection's event loop.
			if ( ! selection || ( collection && collection !== selection ) ) {
				return;
			}

			// Bail if the model is already selected.
			if ( this.$el.hasClass( 'selected' ) ) {
				return;
			}

			// Add 'selected' class to model, set aria-checked to true.
			this.$el.addClass( 'selected' ).attr( 'aria-checked', true );
			//  Make the checkbox tabable, except in media grid (bulk select mode).
			if ( ! ( controller.isModeActive( 'grid' ) && controller.isModeActive( 'select' ) ) ) {
				this.$( '.check' ).attr( 'tabindex', '0' );
			}
		},
		/**
		 * @param {Backbone.Model} model
		 * @param {Backbone.Collection} collection
		 */
		deselect: function( model, collection ) {
			var selection = this.options.selection;

			// Check if a selection exists and if it's the collection provided.
			// If they're not the same collection, bail; we're in another
			// selection's event loop.
			if ( ! selection || ( collection && collection !== selection ) ) {
				return;
			}
			this.$el.removeClass( 'selected' ).attr( 'aria-checked', false )
				.find( '.check' ).attr( 'tabindex', '-1' );
		},
		/**
		 * @param {Backbone.Model} model
		 * @param {Backbone.Collection} collection
		 */
		details: function( model, collection ) {
			var selection = this.options.selection,
				details;

			if ( selection !== collection ) {
				return;
			}

			details = selection.single();
			this.$el.toggleClass( 'details', details === this.model );
		},
		/**
		 * @param {Object} event
		 */
		preventDefault: function( event ) {
			event.preventDefault();
		},
		/**
		 * @param {string} size
		 * @returns {Object}
		 */
		imageSize: function( size ) {
			var sizes = this.model.get('sizes');

			size = size || 'medium';

			// Use the provided image size if possible.
			if ( sizes && sizes[ size ] ) {
				return _.clone( sizes[ size ] );
			} else {
				return {
					url:         this.model.get('url'),
					width:       this.model.get('width'),
					height:      this.model.get('height'),
					orientation: this.model.get('orientation')
				};
			}
		},
		/**
		 * @param {Object} event
		 */
		updateSetting: function( event ) {
			var $setting = $( event.target ).closest('[data-setting]'),
				setting, value;

			if ( ! $setting.length ) {
				return;
			}

			setting = $setting.data('setting');
			value   = event.target.value;

			if ( this.model.get( setting ) !== value ) {
				this.save( setting, value );
			}
		},

		/**
		 * Pass all the arguments to the model's save method.
		 *
		 * Records the aggregate status of all save requests and updates the
		 * view's classes accordingly.
		 */
		save: function() {
			var view = this,
				save = this._save = this._save || { status: 'ready' },
				request = this.model.save.apply( this.model, arguments ),
				requests = save.requests ? $.when( request, save.requests ) : request;

			// If we're waiting to remove 'Saved.', stop.
			if ( save.savedTimer ) {
				clearTimeout( save.savedTimer );
			}

			this.updateSave('waiting');
			save.requests = requests;
			requests.always( function() {
				// If we've performed another request since this one, bail.
				if ( save.requests !== requests ) {
					return;
				}

				view.updateSave( requests.state() === 'resolved' ? 'complete' : 'error' );
				save.savedTimer = setTimeout( function() {
					view.updateSave('ready');
					delete save.savedTimer;
				}, 2000 );
			});
		},
		/**
		 * @param {string} status
		 * @returns {wp.media.view.Attachment} Returns itself to allow chaining
		 */
		updateSave: function( status ) {
			var save = this._save = this._save || { status: 'ready' };

			if ( status && status !== save.status ) {
				this.$el.removeClass( 'save-' + save.status );
				save.status = status;
			}

			this.$el.addClass( 'save-' + save.status );
			return this;
		},

		updateAll: function() {
			var $settings = this.$('[data-setting]'),
				model = this.model,
				changed;

			changed = _.chain( $settings ).map( function( el ) {
				var $input = $('input, textarea, select, [value]', el ),
					setting, value;

				if ( ! $input.length ) {
					return;
				}

				setting = $(el).data('setting');
				value = $input.val();

				// Record the value if it changed.
				if ( model.get( setting ) !== value ) {
					return [ setting, value ];
				}
			}).compact().object().value();

			if ( ! _.isEmpty( changed ) ) {
				model.save( changed );
			}
		},
		/**
		 * @param {Object} event
		 */
		removeFromLibrary: function( event ) {
			// Catch enter and space events
			if ( 'keydown' === event.type && 13 !== event.keyCode && 32 !== event.keyCode ) {
				return;
			}

			// Stop propagation so the model isn't selected.
			event.stopPropagation();

			this.collection.remove( this.model );
		},

		/**
		 * Add the model if it isn't in the selection, if it is in the selection,
		 * remove it.
		 *
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		checkClickHandler: function ( event ) {
			var selection = this.options.selection;
			if ( ! selection ) {
				return;
			}
			event.stopPropagation();
			if ( selection.where( { id: this.model.get( 'id' ) } ).length ) {
				selection.remove( this.model );
				// Move focus back to the attachment tile (from the check).
				this.$el.focus();
			} else {
				selection.add( this.model );
			}
		}
	});

	// Ensure settings remain in sync between attachment views.
	_.each({
		caption: '_syncCaption',
		title:   '_syncTitle',
		artist:  '_syncArtist',
		album:   '_syncAlbum'
	}, function( method, setting ) {
		/**
		 * @param {Backbone.Model} model
		 * @param {string} value
		 * @returns {wp.media.view.Attachment} Returns itself to allow chaining
		 */
		media.view.Attachment.prototype[ method ] = function( model, value ) {
			var $setting = this.$('[data-setting="' + setting + '"]');

			if ( ! $setting.length ) {
				return this;
			}

			// If the updated value is in sync with the value in the DOM, there
			// is no need to re-render. If we're currently editing the value,
			// it will automatically be in sync, suppressing the re-render for
			// the view we're editing, while updating any others.
			if ( value === $setting.find('input, textarea, select, [value]').val() ) {
				return this;
			}

			return this.render();
		};
	});

	/**
	 * wp.media.view.Attachment.Library
	 *
	 * @class
	 * @augments wp.media.view.Attachment
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachment.Library = media.view.Attachment.extend({
		buttons: {
			check: true
		}
	});

	/**
	 * wp.media.view.Attachment.EditLibrary
	 *
	 * @class
	 * @augments wp.media.view.Attachment
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachment.EditLibrary = media.view.Attachment.extend({
		buttons: {
			close: true
		}
	});

	/**
	 * wp.media.view.Attachments
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachments = media.View.extend({
		tagName:   'ul',
		className: 'attachments',

		attributes: {
			tabIndex: -1
		},

		initialize: function() {
			this.el.id = _.uniqueId('__attachments-view-');

			_.defaults( this.options, {
				refreshSensitivity: isTouchDevice ? 300 : 200,
				refreshThreshold:   3,
				AttachmentView:     media.view.Attachment,
				sortable:           false,
				resize:             true,
				idealColumnWidth:   $( window ).width() < 640 ? 135 : 150
			});

			this._viewsByCid = {};
			this.$window = $( window );
			this.resizeEvent = 'resize.media-modal-columns';

			this.collection.on( 'add', function( attachment ) {
				this.views.add( this.createAttachmentView( attachment ), {
					at: this.collection.indexOf( attachment )
				});
			}, this );

			this.collection.on( 'remove', function( attachment ) {
				var view = this._viewsByCid[ attachment.cid ];
				delete this._viewsByCid[ attachment.cid ];

				if ( view ) {
					view.remove();
				}
			}, this );

			this.collection.on( 'reset', this.render, this );

			this.listenTo( this.controller, 'library:selection:add',    this.attachmentFocus );

			// Throttle the scroll handler and bind this.
			this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();

			this.options.scrollElement = this.options.scrollElement || this.el;
			$( this.options.scrollElement ).on( 'scroll', this.scroll );

			this.initSortable();

			_.bindAll( this, 'setColumns' );

			if ( this.options.resize ) {
				this.on( 'ready', this.bindEvents );
				this.controller.on( 'open', this.setColumns );

				// Call this.setColumns() after this view has been rendered in the DOM so
				// attachments get proper width applied.
				_.defer( this.setColumns, this );
			}
		},

		bindEvents: function() {
			this.$window.off( this.resizeEvent ).on( this.resizeEvent, _.debounce( this.setColumns, 50 ) );
		},

		attachmentFocus: function() {
			this.$( 'li:first' ).focus();
		},

		restoreFocus: function() {
			this.$( 'li.selected:first' ).focus();
		},

		arrowEvent: function( event ) {
			var attachments = this.$el.children( 'li' ),
				perRow = this.columns,
				index = attachments.filter( ':focus' ).index(),
				row = ( index + 1 ) <= perRow ? 1 : Math.ceil( ( index + 1 ) / perRow );

			if ( index === -1 ) {
				return;
			}

			// Left arrow
			if ( 37 === event.keyCode ) {
				if ( 0 === index ) {
					return;
				}
				attachments.eq( index - 1 ).focus();
			}

			// Up arrow
			if ( 38 === event.keyCode ) {
				if ( 1 === row ) {
					return;
				}
				attachments.eq( index - perRow ).focus();
			}

			// Right arrow
			if ( 39 === event.keyCode ) {
				if ( attachments.length === index ) {
					return;
				}
				attachments.eq( index + 1 ).focus();
			}

			// Down arrow
			if ( 40 === event.keyCode ) {
				if ( Math.ceil( attachments.length / perRow ) === row ) {
					return;
				}
				attachments.eq( index + perRow ).focus();
			}
		},

		dispose: function() {
			this.collection.props.off( null, null, this );
			if ( this.options.resize ) {
				this.$window.off( this.resizeEvent );
			}

			/**
			 * call 'dispose' directly on the parent class
			 */
			media.View.prototype.dispose.apply( this, arguments );
		},

		setColumns: function() {
			var prev = this.columns,
				width = this.$el.width();

			if ( width ) {
				this.columns = Math.min( Math.round( width / this.options.idealColumnWidth ), 12 ) || 1;

				if ( ! prev || prev !== this.columns ) {
					this.$el.closest( '.media-frame-content' ).attr( 'data-columns', this.columns );
				}
			}
		},

		initSortable: function() {
			var collection = this.collection;

			if ( isTouchDevice || ! this.options.sortable || ! $.fn.sortable ) {
				return;
			}

			this.$el.sortable( _.extend({
				// If the `collection` has a `comparator`, disable sorting.
				disabled: !! collection.comparator,

				// Change the position of the attachment as soon as the
				// mouse pointer overlaps a thumbnail.
				tolerance: 'pointer',

				// Record the initial `index` of the dragged model.
				start: function( event, ui ) {
					ui.item.data('sortableIndexStart', ui.item.index());
				},

				// Update the model's index in the collection.
				// Do so silently, as the view is already accurate.
				update: function( event, ui ) {
					var model = collection.at( ui.item.data('sortableIndexStart') ),
						comparator = collection.comparator;

					// Temporarily disable the comparator to prevent `add`
					// from re-sorting.
					delete collection.comparator;

					// Silently shift the model to its new index.
					collection.remove( model, {
						silent: true
					});
					collection.add( model, {
						silent: true,
						at:     ui.item.index()
					});

					// Restore the comparator.
					collection.comparator = comparator;

					// Fire the `reset` event to ensure other collections sync.
					collection.trigger( 'reset', collection );

					// If the collection is sorted by menu order,
					// update the menu order.
					collection.saveMenuOrder();
				}
			}, this.options.sortable ) );

			// If the `orderby` property is changed on the `collection`,
			// check to see if we have a `comparator`. If so, disable sorting.
			collection.props.on( 'change:orderby', function() {
				this.$el.sortable( 'option', 'disabled', !! collection.comparator );
			}, this );

			this.collection.props.on( 'change:orderby', this.refreshSortable, this );
			this.refreshSortable();
		},

		refreshSortable: function() {
			if ( isTouchDevice || ! this.options.sortable || ! $.fn.sortable ) {
				return;
			}

			// If the `collection` has a `comparator`, disable sorting.
			var collection = this.collection,
				orderby = collection.props.get('orderby'),
				enabled = 'menuOrder' === orderby || ! collection.comparator;

			this.$el.sortable( 'option', 'disabled', ! enabled );
		},

		/**
		 * @param {wp.media.model.Attachment} attachment
		 * @returns {wp.media.View}
		 */
		createAttachmentView: function( attachment ) {
			var view = new this.options.AttachmentView({
				controller:           this.controller,
				model:                attachment,
				collection:           this.collection,
				selection:            this.options.selection
			});

			return this._viewsByCid[ attachment.cid ] = view;
		},

		prepare: function() {
			// Create all of the Attachment views, and replace
			// the list in a single DOM operation.
			if ( this.collection.length ) {
				this.views.set( this.collection.map( this.createAttachmentView, this ) );

			// If there are no elements, clear the views and load some.
			} else {
				this.views.unset();
				this.collection.more().done( this.scroll );
			}
		},

		ready: function() {
			// Trigger the scroll event to check if we're within the
			// threshold to query for additional attachments.
			this.scroll();
		},

		scroll: function() {
			var view = this,
				el = this.options.scrollElement,
				scrollTop = el.scrollTop,
				toolbar;

			// The scroll event occurs on the document, but the element
			// that should be checked is the document body.
			if ( el == document ) {
				el = document.body;
				scrollTop = $(document).scrollTop();
			}

			if ( ! $(el).is(':visible') || ! this.collection.hasMore() ) {
				return;
			}

			toolbar = this.views.parent.toolbar;

			// Show the spinner only if we are close to the bottom.
			if ( el.scrollHeight - ( scrollTop + el.clientHeight ) < el.clientHeight / 3 ) {
				toolbar.get('spinner').show();
			}

			if ( el.scrollHeight < scrollTop + ( el.clientHeight * this.options.refreshThreshold ) ) {
				this.collection.more().done(function() {
					view.scroll();
					toolbar.get('spinner').hide();
				});
			}
		}
	});

	/**
	 * wp.media.view.Search
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Search = media.View.extend({
		tagName:   'input',
		className: 'search',
		id:        'media-search-input',

		attributes: {
			type:        'search',
			placeholder: l10n.search
		},

		events: {
			'input':  'search',
			'keyup':  'search',
			'change': 'search',
			'search': 'search'
		},

		/**
		 * @returns {wp.media.view.Search} Returns itself to allow chaining
		 */
		render: function() {
			this.el.value = this.model.escape('search');
			return this;
		},

		search: function( event ) {
			if ( event.target.value ) {
				this.model.set( 'search', event.target.value );
			} else {
				this.model.unset('search');
			}
		}
	});

	/**
	 * wp.media.view.AttachmentFilters
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.AttachmentFilters = media.View.extend({
		tagName:   'select',
		className: 'attachment-filters',
		id:        'media-attachment-filters',

		events: {
			change: 'change'
		},

		keys: [],

		initialize: function() {
			this.createFilters();
			_.extend( this.filters, this.options.filters );

			// Build `<option>` elements.
			this.$el.html( _.chain( this.filters ).map( function( filter, value ) {
				return {
					el: $( '<option></option>' ).val( value ).html( filter.text )[0],
					priority: filter.priority || 50
				};
			}, this ).sortBy('priority').pluck('el').value() );

			this.model.on( 'change', this.select, this );
			this.select();
		},

		/**
		 * @abstract
		 */
		createFilters: function() {
			this.filters = {};
		},

		/**
		 * When the selected filter changes, update the Attachment Query properties to match.
		 */
		change: function() {
			var filter = this.filters[ this.el.value ];
			if ( filter ) {
				this.model.set( filter.props );
			}
		},

		select: function() {
			var model = this.model,
				value = 'all',
				props = model.toJSON();

			_.find( this.filters, function( filter, id ) {
				var equal = _.all( filter.props, function( prop, key ) {
					return prop === ( _.isUndefined( props[ key ] ) ? null : props[ key ] );
				});

				if ( equal ) {
					return value = id;
				}
			});

			this.$el.val( value );
		}
	});

	/**
	 * A filter dropdown for month/dates.
	 *
	 * @class
	 * @augments wp.media.view.AttachmentFilters
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.DateFilter = media.view.AttachmentFilters.extend({
		id: 'media-attachment-date-filters',

		createFilters: function() {
			var filters = {};
			_.each( media.view.settings.months || {}, function( value, index ) {
				filters[ index ] = {
					text: value.text,
					props: {
						year: value.year,
						monthnum: value.month
					}
				};
			});
			filters.all = {
				text:  l10n.allDates,
				props: {
					monthnum: false,
					year:  false
				},
				priority: 10
			};
			this.filters = filters;
		}
	});

	/**
	 * wp.media.view.AttachmentFilters.Uploaded
	 *
	 * @class
	 * @augments wp.media.view.AttachmentFilters
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.AttachmentFilters.Uploaded = media.view.AttachmentFilters.extend({
		createFilters: function() {
			var type = this.model.get('type'),
				types = media.view.settings.mimeTypes,
				text;

			if ( types && type ) {
				text = types[ type ];
			}

			this.filters = {
				all: {
					text:  text || l10n.allMediaItems,
					props: {
						uploadedTo: null,
						orderby: 'date',
						order:   'DESC'
					},
					priority: 10
				},

				uploaded: {
					text:  l10n.uploadedToThisPost,
					props: {
						uploadedTo: media.view.settings.post.id,
						orderby: 'menuOrder',
						order:   'ASC'
					},
					priority: 20
				},

				unattached: {
					text:  l10n.unattached,
					props: {
						uploadedTo: 0,
						orderby: 'menuOrder',
						order:   'ASC'
					},
					priority: 50
				}
			};
		}
	});

	/**
	 * wp.media.view.AttachmentFilters.All
	 *
	 * @class
	 * @augments wp.media.view.AttachmentFilters
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.AttachmentFilters.All = media.view.AttachmentFilters.extend({
		createFilters: function() {
			var filters = {};

			_.each( media.view.settings.mimeTypes || {}, function( text, key ) {
				filters[ key ] = {
					text: text,
					props: {
						status:  null,
						type:    key,
						uploadedTo: null,
						orderby: 'date',
						order:   'DESC'
					}
				};
			});

			filters.all = {
				text:  l10n.allMediaItems,
				props: {
					status:  null,
					type:    null,
					uploadedTo: null,
					orderby: 'date',
					order:   'DESC'
				},
				priority: 10
			};

			if ( media.view.settings.post.id ) {
				filters.uploaded = {
					text:  l10n.uploadedToThisPost,
					props: {
						status:  null,
						type:    null,
						uploadedTo: media.view.settings.post.id,
						orderby: 'menuOrder',
						order:   'ASC'
					},
					priority: 20
				};
			}

			filters.unattached = {
				text:  l10n.unattached,
				props: {
					status:     null,
					uploadedTo: 0,
					type:       null,
					orderby:    'menuOrder',
					order:      'ASC'
				},
				priority: 50
			};

			if ( media.view.settings.mediaTrash &&
				this.controller.isModeActive( 'grid' ) ) {

				filters.trash = {
					text:  l10n.trash,
					props: {
						uploadedTo: null,
						status:     'trash',
						type:       null,
						orderby:    'date',
						order:      'DESC'
					},
					priority: 50
				};
			}

			this.filters = filters;
		}
	});

	/**
	 * wp.media.view.AttachmentsBrowser
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 *
	 * @param {object}      options
	 * @param {object}      [options.filters=false] Which filters to show in the browser's toolbar.
	 *                                              Accepts 'uploaded' and 'all'.
	 * @param {object}      [options.search=true]   Whether to show the search interface in the
	 *                                              browser's toolbar.
	 * @param {object}      [options.date=true]     Whether to show the date filter in the
	 *                                              browser's toolbar.
	 * @param {object}      [options.display=false] Whether to show the attachments display settings
	 *                                              view in the sidebar.
	 * @param {bool|string} [options.sidebar=true]  Whether to create a sidebar for the browser.
	 *                                              Accepts true, false, and 'errors'.
	 */
	media.view.AttachmentsBrowser = media.View.extend({
		tagName:   'div',
		className: 'attachments-browser',

		initialize: function() {
			_.defaults( this.options, {
				filters: false,
				search:  true,
				date:    true,
				display: false,
				sidebar: true,
				AttachmentView: media.view.Attachment.Library
			});

			this.listenTo( this.controller, 'toggle:upload:attachment', _.bind( this.toggleUploader, this ) );
			this.controller.on( 'edit:selection', this.editSelection );
			this.createToolbar();
			if ( this.options.sidebar ) {
				this.createSidebar();
			}
			this.createUploader();
			this.createAttachments();
			this.updateContent();

			if ( ! this.options.sidebar || 'errors' === this.options.sidebar ) {
				this.$el.addClass( 'hide-sidebar' );

				if ( 'errors' === this.options.sidebar ) {
					this.$el.addClass( 'sidebar-for-errors' );
				}
			}

			this.collection.on( 'add remove reset', this.updateContent, this );
		},

		editSelection: function( modal ) {
			modal.$( '.media-button-backToLibrary' ).focus();
		},

		/**
		 * @returns {wp.media.view.AttachmentsBrowser} Returns itself to allow chaining
		 */
		dispose: function() {
			this.options.selection.off( null, null, this );
			media.View.prototype.dispose.apply( this, arguments );
			return this;
		},

		createToolbar: function() {
			var LibraryViewSwitcher, Filters, toolbarOptions;

			toolbarOptions = {
				controller: this.controller
			};

			if ( this.controller.isModeActive( 'grid' ) ) {
				toolbarOptions.className = 'media-toolbar wp-filter';
			}

			/**
			* @member {wp.media.view.Toolbar}
			*/
			this.toolbar = new media.view.Toolbar( toolbarOptions );

			this.views.add( this.toolbar );

			this.toolbar.set( 'spinner', new media.view.Spinner({
				priority: -60
			}) );

			if ( -1 !== $.inArray( this.options.filters, [ 'uploaded', 'all' ] ) ) {
				// "Filters" will return a <select>, need to render
				// screen reader text before
				this.toolbar.set( 'filtersLabel', new media.view.Label({
					value: l10n.filterByType,
					attributes: {
						'for':  'media-attachment-filters'
					},
					priority:   -80
				}).render() );

				if ( 'uploaded' === this.options.filters ) {
					this.toolbar.set( 'filters', new media.view.AttachmentFilters.Uploaded({
						controller: this.controller,
						model:      this.collection.props,
						priority:   -80
					}).render() );
				} else {
					Filters = new media.view.AttachmentFilters.All({
						controller: this.controller,
						model:      this.collection.props,
						priority:   -80
					});

					this.toolbar.set( 'filters', Filters.render() );
				}
			}

			// Feels odd to bring the global media library switcher into the Attachment
			// browser view. Is this a use case for doAction( 'add:toolbar-items:attachments-browser', this.toolbar );
			// which the controller can tap into and add this view?
			if ( this.controller.isModeActive( 'grid' ) ) {
				LibraryViewSwitcher = media.View.extend({
					className: 'view-switch media-grid-view-switch',
					template: media.template( 'media-library-view-switcher')
				});

				this.toolbar.set( 'libraryViewSwitcher', new LibraryViewSwitcher({
					controller: this.controller,
					priority: -90
				}).render() );

				// DateFilter is a <select>, screen reader text needs to be rendered before
				this.toolbar.set( 'dateFilterLabel', new media.view.Label({
					value: l10n.filterByDate,
					attributes: {
						'for': 'media-attachment-date-filters'
					},
					priority: -75
				}).render() );
				this.toolbar.set( 'dateFilter', new media.view.DateFilter({
					controller: this.controller,
					model:      this.collection.props,
					priority: -75
				}).render() );

				// BulkSelection is a <div> with subviews, including screen reader text
				this.toolbar.set( 'selectModeToggleButton', new media.view.SelectModeToggleButton({
					text: l10n.bulkSelect,
					controller: this.controller,
					priority: -70
				}).render() );

				this.toolbar.set( 'deleteSelectedButton', new media.view.DeleteSelectedButton({
					filters: Filters,
					style: 'primary',
					disabled: true,
					text: media.view.settings.mediaTrash ? l10n.trashSelected : l10n.deleteSelected,
					controller: this.controller,
					priority: -60,
					click: function() {
						var changed = [], removed = [], self = this,
							selection = this.controller.state().get( 'selection' ),
							library = this.controller.state().get( 'library' );

						if ( ! selection.length ) {
							return;
						}

						if ( ! media.view.settings.mediaTrash && ! confirm( l10n.warnBulkDelete ) ) {
							return;
						}

						if ( media.view.settings.mediaTrash &&
							'trash' !== selection.at( 0 ).get( 'status' ) &&
							! confirm( l10n.warnBulkTrash ) ) {

							return;
						}

						selection.each( function( model ) {
							if ( ! model.get( 'nonces' )['delete'] ) {
								removed.push( model );
								return;
							}

							if ( media.view.settings.mediaTrash && 'trash' === model.get( 'status' ) ) {
								model.set( 'status', 'inherit' );
								changed.push( model.save() );
								removed.push( model );
							} else if ( media.view.settings.mediaTrash ) {
								model.set( 'status', 'trash' );
								changed.push( model.save() );
								removed.push( model );
							} else {
								model.destroy({wait: true});
							}
						} );

						if ( changed.length ) {
							selection.remove( removed );

							$.when.apply( null, changed ).then( function() {
								library._requery( true );
								self.controller.trigger( 'selection:action:done' );
							} );
						} else {
							this.controller.trigger( 'selection:action:done' );
						}
					}
				}).render() );

				if ( media.view.settings.mediaTrash ) {
					this.toolbar.set( 'deleteSelectedPermanentlyButton', new media.view.DeleteSelectedPermanentlyButton({
						filters: Filters,
						style: 'primary',
						disabled: true,
						text: l10n.deleteSelected,
						controller: this.controller,
						priority: -55,
						click: function() {
							var removed = [], selection = this.controller.state().get( 'selection' );

							if ( ! selection.length || ! confirm( l10n.warnBulkDelete ) ) {
								return;
							}

							selection.each( function( model ) {
								if ( ! model.get( 'nonces' )['delete'] ) {
									removed.push( model );
									return;
								}

								model.destroy();
							} );

							selection.remove( removed );
							this.controller.trigger( 'selection:action:done' );
						}
					}).render() );
				}

			} else if ( this.options.date ) {
				// DateFilter is a <select>, screen reader text needs to be rendered before
				this.toolbar.set( 'dateFilterLabel', new media.view.Label({
					value: l10n.filterByDate,
					attributes: {
						'for': 'media-attachment-date-filters'
					},
					priority: -75
				}).render() );
				this.toolbar.set( 'dateFilter', new media.view.DateFilter({
					controller: this.controller,
					model:      this.collection.props,
					priority: -75
				}).render() );
			}

			if ( this.options.search ) {
				// Search is an input, screen reader text needs to be rendered before
				this.toolbar.set( 'searchLabel', new media.view.Label({
					value: l10n.searchMediaLabel,
					attributes: {
						'for': 'media-search-input'
					},
					priority:   60
				}).render() );
				this.toolbar.set( 'search', new media.view.Search({
					controller: this.controller,
					model:      this.collection.props,
					priority:   60
				}).render() );
			}

			if ( this.options.dragInfo ) {
				this.toolbar.set( 'dragInfo', new media.View({
					el: $( '<div class="instructions">' + l10n.dragInfo + '</div>' )[0],
					priority: -40
				}) );
			}

			if ( this.options.suggestedWidth && this.options.suggestedHeight ) {
				this.toolbar.set( 'suggestedDimensions', new media.View({
					el: $( '<div class="instructions">' + l10n.suggestedDimensions + ' ' + this.options.suggestedWidth + ' &times; ' + this.options.suggestedHeight + '</div>' )[0],
					priority: -40
				}) );
			}
		},

		updateContent: function() {
			var view = this,
				noItemsView;

			if ( this.controller.isModeActive( 'grid' ) ) {
				noItemsView = view.attachmentsNoResults;
			} else {
				noItemsView = view.uploader;
			}

			if ( ! this.collection.length ) {
				this.toolbar.get( 'spinner' ).show();
				this.dfd = this.collection.more().done( function() {
					if ( ! view.collection.length ) {
						noItemsView.$el.removeClass( 'hidden' );
					} else {
						noItemsView.$el.addClass( 'hidden' );
					}
					view.toolbar.get( 'spinner' ).hide();
				} );
			} else {
				noItemsView.$el.addClass( 'hidden' );
				view.toolbar.get( 'spinner' ).hide();
			}
		},

		createUploader: function() {
			this.uploader = new media.view.UploaderInline({
				controller: this.controller,
				status:     false,
				message:    this.controller.isModeActive( 'grid' ) ? '' : l10n.noItemsFound,
				canClose:   this.controller.isModeActive( 'grid' )
			});

			this.uploader.hide();
			this.views.add( this.uploader );
		},

		toggleUploader: function() {
			if ( this.uploader.$el.hasClass( 'hidden' ) ) {
				this.uploader.show();
			} else {
				this.uploader.hide();
			}
		},

		createAttachments: function() {
			this.attachments = new media.view.Attachments({
				controller:           this.controller,
				collection:           this.collection,
				selection:            this.options.selection,
				model:                this.model,
				sortable:             this.options.sortable,
				scrollElement:        this.options.scrollElement,
				idealColumnWidth:     this.options.idealColumnWidth,

				// The single `Attachment` view to be used in the `Attachments` view.
				AttachmentView: this.options.AttachmentView
			});

			// Add keydown listener to the instance of the Attachments view
			this.attachments.listenTo( this.controller, 'attachment:keydown:arrow',     this.attachments.arrowEvent );
			this.attachments.listenTo( this.controller, 'attachment:details:shift-tab', this.attachments.restoreFocus );

			this.views.add( this.attachments );


			if ( this.controller.isModeActive( 'grid' ) ) {
				this.attachmentsNoResults = new media.View({
					controller: this.controller,
					tagName: 'p'
				});

				this.attachmentsNoResults.$el.addClass( 'hidden no-media' );
				this.attachmentsNoResults.$el.html( l10n.noMedia );

				this.views.add( this.attachmentsNoResults );
			}
		},

		createSidebar: function() {
			var options = this.options,
				selection = options.selection,
				sidebar = this.sidebar = new media.view.Sidebar({
					controller: this.controller
				});

			this.views.add( sidebar );

			if ( this.controller.uploader ) {
				sidebar.set( 'uploads', new media.view.UploaderStatus({
					controller: this.controller,
					priority:   40
				}) );
			}

			selection.on( 'selection:single', this.createSingle, this );
			selection.on( 'selection:unsingle', this.disposeSingle, this );

			if ( selection.single() ) {
				this.createSingle();
			}
		},

		createSingle: function() {
			var sidebar = this.sidebar,
				single = this.options.selection.single();

			sidebar.set( 'details', new media.view.Attachment.Details({
				controller: this.controller,
				model:      single,
				priority:   80
			}) );

			sidebar.set( 'compat', new media.view.AttachmentCompat({
				controller: this.controller,
				model:      single,
				priority:   120
			}) );

			if ( this.options.display ) {
				sidebar.set( 'display', new media.view.Settings.AttachmentDisplay({
					controller:   this.controller,
					model:        this.model.display( single ),
					attachment:   single,
					priority:     160,
					userSettings: this.model.get('displayUserSettings')
				}) );
			}

			// Show the sidebar on mobile
			if ( this.model.id === 'insert' ) {
				sidebar.$el.addClass( 'visible' );
			}
		},

		disposeSingle: function() {
			var sidebar = this.sidebar;
			sidebar.unset('details');
			sidebar.unset('compat');
			sidebar.unset('display');
			// Hide the sidebar on mobile
			sidebar.$el.removeClass( 'visible' );
		}
	});

	/**
	 * wp.media.view.Selection
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Selection = media.View.extend({
		tagName:   'div',
		className: 'media-selection',
		template:  media.template('media-selection'),

		events: {
			'click .edit-selection':  'edit',
			'click .clear-selection': 'clear'
		},

		initialize: function() {
			_.defaults( this.options, {
				editable:  false,
				clearable: true
			});

			/**
			 * @member {wp.media.view.Attachments.Selection}
			 */
			this.attachments = new media.view.Attachments.Selection({
				controller: this.controller,
				collection: this.collection,
				selection:  this.collection,
				model:      new Backbone.Model()
			});

			this.views.set( '.selection-view', this.attachments );
			this.collection.on( 'add remove reset', this.refresh, this );
			this.controller.on( 'content:activate', this.refresh, this );
		},

		ready: function() {
			this.refresh();
		},

		refresh: function() {
			// If the selection hasn't been rendered, bail.
			if ( ! this.$el.children().length ) {
				return;
			}

			var collection = this.collection,
				editing = 'edit-selection' === this.controller.content.mode();

			// If nothing is selected, display nothing.
			this.$el.toggleClass( 'empty', ! collection.length );
			this.$el.toggleClass( 'one', 1 === collection.length );
			this.$el.toggleClass( 'editing', editing );

			this.$('.count').text( l10n.selected.replace('%d', collection.length) );
		},

		edit: function( event ) {
			event.preventDefault();
			if ( this.options.editable ) {
				this.options.editable.call( this, this.collection );
			}
		},

		clear: function( event ) {
			event.preventDefault();
			this.collection.reset();

			// Keep focus inside media modal
			// after clear link is selected
			this.controller.modal.focusManager.focus();
		}
	});


	/**
	 * wp.media.view.Attachment.Selection
	 *
	 * @class
	 * @augments wp.media.view.Attachment
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachment.Selection = media.view.Attachment.extend({
		className: 'attachment selection',

		// On click, just select the model, instead of removing the model from
		// the selection.
		toggleSelection: function() {
			this.options.selection.single( this.model );
		}
	});

	/**
	 * wp.media.view.Attachments.Selection
	 *
	 * @class
	 * @augments wp.media.view.Attachments
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachments.Selection = media.view.Attachments.extend({
		events: {},
		initialize: function() {
			_.defaults( this.options, {
				sortable:   false,
				resize:     false,

				// The single `Attachment` view to be used in the `Attachments` view.
				AttachmentView: media.view.Attachment.Selection
			});
			// Call 'initialize' directly on the parent class.
			return media.view.Attachments.prototype.initialize.apply( this, arguments );
		}
	});

	/**
	 * wp.media.view.Attachments.EditSelection
	 *
	 * @class
	 * @augments wp.media.view.Attachment.Selection
	 * @augments wp.media.view.Attachment
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachment.EditSelection = media.view.Attachment.Selection.extend({
		buttons: {
			close: true
		}
	});


	/**
	 * wp.media.view.Settings
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Settings = media.View.extend({
		events: {
			'click button':    'updateHandler',
			'change input':    'updateHandler',
			'change select':   'updateHandler',
			'change textarea': 'updateHandler'
		},

		initialize: function() {
			this.model = this.model || new Backbone.Model();
			this.model.on( 'change', this.updateChanges, this );
		},

		prepare: function() {
			return _.defaults({
				model: this.model.toJSON()
			}, this.options );
		},
		/**
		 * @returns {wp.media.view.Settings} Returns itself to allow chaining
		 */
		render: function() {
			media.View.prototype.render.apply( this, arguments );
			// Select the correct values.
			_( this.model.attributes ).chain().keys().each( this.update, this );
			return this;
		},
		/**
		 * @param {string} key
		 */
		update: function( key ) {
			var value = this.model.get( key ),
				$setting = this.$('[data-setting="' + key + '"]'),
				$buttons, $value;

			// Bail if we didn't find a matching setting.
			if ( ! $setting.length ) {
				return;
			}

			// Attempt to determine how the setting is rendered and update
			// the selected value.

			// Handle dropdowns.
			if ( $setting.is('select') ) {
				$value = $setting.find('[value="' + value + '"]');

				if ( $value.length ) {
					$setting.find('option').prop( 'selected', false );
					$value.prop( 'selected', true );
				} else {
					// If we can't find the desired value, record what *is* selected.
					this.model.set( key, $setting.find(':selected').val() );
				}

			// Handle button groups.
			} else if ( $setting.hasClass('button-group') ) {
				$buttons = $setting.find('button').removeClass('active');
				$buttons.filter( '[value="' + value + '"]' ).addClass('active');

			// Handle text inputs and textareas.
			} else if ( $setting.is('input[type="text"], textarea') ) {
				if ( ! $setting.is(':focus') ) {
					$setting.val( value );
				}
			// Handle checkboxes.
			} else if ( $setting.is('input[type="checkbox"]') ) {
				$setting.prop( 'checked', !! value && 'false' !== value );
			}
		},
		/**
		 * @param {Object} event
		 */
		updateHandler: function( event ) {
			var $setting = $( event.target ).closest('[data-setting]'),
				value = event.target.value,
				userSetting;

			event.preventDefault();

			if ( ! $setting.length ) {
				return;
			}

			// Use the correct value for checkboxes.
			if ( $setting.is('input[type="checkbox"]') ) {
				value = $setting[0].checked;
			}

			// Update the corresponding setting.
			this.model.set( $setting.data('setting'), value );

			// If the setting has a corresponding user setting,
			// update that as well.
			if ( userSetting = $setting.data('userSetting') ) {
				setUserSetting( userSetting, value );
			}
		},

		updateChanges: function( model ) {
			if ( model.hasChanged() ) {
				_( model.changed ).chain().keys().each( this.update, this );
			}
		}
	});

	/**
	 * wp.media.view.Settings.AttachmentDisplay
	 *
	 * @class
	 * @augments wp.media.view.Settings
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Settings.AttachmentDisplay = media.view.Settings.extend({
		className: 'attachment-display-settings',
		template:  media.template('attachment-display-settings'),

		initialize: function() {
			var attachment = this.options.attachment;

			_.defaults( this.options, {
				userSettings: false
			});
			// Call 'initialize' directly on the parent class.
			media.view.Settings.prototype.initialize.apply( this, arguments );
			this.model.on( 'change:link', this.updateLinkTo, this );

			if ( attachment ) {
				attachment.on( 'change:uploading', this.render, this );
			}
		},

		dispose: function() {
			var attachment = this.options.attachment;
			if ( attachment ) {
				attachment.off( null, null, this );
			}
			/**
			 * call 'dispose' directly on the parent class
			 */
			media.view.Settings.prototype.dispose.apply( this, arguments );
		},
		/**
		 * @returns {wp.media.view.AttachmentDisplay} Returns itself to allow chaining
		 */
		render: function() {
			var attachment = this.options.attachment;
			if ( attachment ) {
				_.extend( this.options, {
					sizes: attachment.get('sizes'),
					type:  attachment.get('type')
				});
			}
			/**
			 * call 'render' directly on the parent class
			 */
			media.view.Settings.prototype.render.call( this );
			this.updateLinkTo();
			return this;
		},

		updateLinkTo: function() {
			var linkTo = this.model.get('link'),
				$input = this.$('.link-to-custom'),
				attachment = this.options.attachment;

			if ( 'none' === linkTo || 'embed' === linkTo || ( ! attachment && 'custom' !== linkTo ) ) {
				$input.addClass( 'hidden' );
				return;
			}

			if ( attachment ) {
				if ( 'post' === linkTo ) {
					$input.val( attachment.get('link') );
				} else if ( 'file' === linkTo ) {
					$input.val( attachment.get('url') );
				} else if ( ! this.model.get('linkUrl') ) {
					$input.val('http://');
				}

				$input.prop( 'readonly', 'custom' !== linkTo );
			}

			$input.removeClass( 'hidden' );

			// If the input is visible, focus and select its contents.
			if ( ! isTouchDevice && $input.is(':visible') ) {
				$input.focus()[0].select();
			}
		}
	});

	/**
	 * wp.media.view.Settings.Gallery
	 *
	 * @class
	 * @augments wp.media.view.Settings
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Settings.Gallery = media.view.Settings.extend({
		className: 'collection-settings gallery-settings',
		template:  media.template('gallery-settings')
	});

	/**
	 * wp.media.view.Settings.Playlist
	 *
	 * @class
	 * @augments wp.media.view.Settings
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Settings.Playlist = media.view.Settings.extend({
		className: 'collection-settings playlist-settings',
		template:  media.template('playlist-settings')
	});

	/**
	 * wp.media.view.Attachment.Details
	 *
	 * @class
	 * @augments wp.media.view.Attachment
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachment.Details = media.view.Attachment.extend({
		tagName:   'div',
		className: 'attachment-details',
		template:  media.template('attachment-details'),

		attributes: function() {
			return {
				'tabIndex':     0,
				'data-id':      this.model.get( 'id' )
			};
		},

		events: {
			'change [data-setting]':          'updateSetting',
			'change [data-setting] input':    'updateSetting',
			'change [data-setting] select':   'updateSetting',
			'change [data-setting] textarea': 'updateSetting',
			'click .delete-attachment':       'deleteAttachment',
			'click .trash-attachment':        'trashAttachment',
			'click .untrash-attachment':      'untrashAttachment',
			'click .edit-attachment':         'editAttachment',
			'click .refresh-attachment':      'refreshAttachment',
			'keydown':                        'toggleSelectionHandler'
		},

		initialize: function() {
			this.options = _.defaults( this.options, {
				rerenderOnModelChange: false
			});

			this.on( 'ready', this.initialFocus );
			// Call 'initialize' directly on the parent class.
			media.view.Attachment.prototype.initialize.apply( this, arguments );
		},

		initialFocus: function() {
			if ( ! isTouchDevice ) {
				this.$( ':input' ).eq( 0 ).focus();
			}
		},
		/**
		 * @param {Object} event
		 */
		deleteAttachment: function( event ) {
			event.preventDefault();

			if ( confirm( l10n.warnDelete ) ) {
				this.model.destroy();
				// Keep focus inside media modal
				// after image is deleted
				this.controller.modal.focusManager.focus();
			}
		},
		/**
		 * @param {Object} event
		 */
		trashAttachment: function( event ) {
			var library = this.controller.library;
			event.preventDefault();

			if ( media.view.settings.mediaTrash &&
				'edit-metadata' === this.controller.content.mode() ) {

				this.model.set( 'status', 'trash' );
				this.model.save().done( function() {
					library._requery( true );
				} );
			}  else {
				this.model.destroy();
			}
		},
		/**
		 * @param {Object} event
		 */
		untrashAttachment: function( event ) {
			var library = this.controller.library;
			event.preventDefault();

			this.model.set( 'status', 'inherit' );
			this.model.save().done( function() {
				library._requery( true );
			} );
		},
		/**
		 * @param {Object} event
		 */
		editAttachment: function( event ) {
			var editState = this.controller.states.get( 'edit-image' );
			if ( window.imageEdit && editState ) {
				event.preventDefault();

				editState.set( 'image', this.model );
				this.controller.setState( 'edit-image' );
			} else {
				this.$el.addClass('needs-refresh');
			}
		},
		/**
		 * @param {Object} event
		 */
		refreshAttachment: function( event ) {
			this.$el.removeClass('needs-refresh');
			event.preventDefault();
			this.model.fetch();
		},
		/**
		 * When reverse tabbing(shift+tab) out of the right details panel, deliver
		 * the focus to the item in the list that was being edited.
		 *
		 * @param {Object} event
		 */
		toggleSelectionHandler: function( event ) {
			if ( 'keydown' === event.type && 9 === event.keyCode && event.shiftKey && event.target === this.$( ':tabbable' ).get( 0 ) ) {
				this.controller.trigger( 'attachment:details:shift-tab', event );
				return false;
			}

			if ( 37 === event.keyCode || 38 === event.keyCode || 39 === event.keyCode || 40 === event.keyCode ) {
				this.controller.trigger( 'attachment:keydown:arrow', event );
				return;
			}
		}
	});

	/**
	 * wp.media.view.AttachmentCompat
	 *
	 * A view to display fields added via the `attachment_fields_to_edit` filter.
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.AttachmentCompat = media.View.extend({
		tagName:   'form',
		className: 'compat-item',

		events: {
			'submit':          'preventDefault',
			'change input':    'save',
			'change select':   'save',
			'change textarea': 'save'
		},

		initialize: function() {
			this.model.on( 'change:compat', this.render, this );
		},
		/**
		 * @returns {wp.media.view.AttachmentCompat} Returns itself to allow chaining
		 */
		dispose: function() {
			if ( this.$(':focus').length ) {
				this.save();
			}
			/**
			 * call 'dispose' directly on the parent class
			 */
			return media.View.prototype.dispose.apply( this, arguments );
		},
		/**
		 * @returns {wp.media.view.AttachmentCompat} Returns itself to allow chaining
		 */
		render: function() {
			var compat = this.model.get('compat');
			if ( ! compat || ! compat.item ) {
				return;
			}

			this.views.detach();
			this.$el.html( compat.item );
			this.views.render();
			return this;
		},
		/**
		 * @param {Object} event
		 */
		preventDefault: function( event ) {
			event.preventDefault();
		},
		/**
		 * @param {Object} event
		 */
		save: function( event ) {
			var data = {};

			if ( event ) {
				event.preventDefault();
			}

			_.each( this.$el.serializeArray(), function( pair ) {
				data[ pair.name ] = pair.value;
			});

			this.controller.trigger( 'attachment:compat:waiting', ['waiting'] );
			this.model.saveCompat( data ).always( _.bind( this.postSave, this ) );
		},

		postSave: function() {
			this.controller.trigger( 'attachment:compat:ready', ['ready'] );
		}
	});

	/**
	 * wp.media.view.Iframe
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Iframe = media.View.extend({
		className: 'media-iframe',
		/**
		 * @returns {wp.media.view.Iframe} Returns itself to allow chaining
		 */
		render: function() {
			this.views.detach();
			this.$el.html( '<iframe src="' + this.controller.state().get('src') + '" />' );
			this.views.render();
			return this;
		}
	});

	/**
	 * wp.media.view.Embed
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Embed = media.View.extend({
		className: 'media-embed',

		initialize: function() {
			/**
			 * @member {wp.media.view.EmbedUrl}
			 */
			this.url = new media.view.EmbedUrl({
				controller: this.controller,
				model:      this.model.props
			}).render();

			this.views.set([ this.url ]);
			this.refresh();
			this.model.on( 'change:type', this.refresh, this );
			this.model.on( 'change:loading', this.loading, this );
		},

		/**
		 * @param {Object} view
		 */
		settings: function( view ) {
			if ( this._settings ) {
				this._settings.remove();
			}
			this._settings = view;
			this.views.add( view );
		},

		refresh: function() {
			var type = this.model.get('type'),
				constructor;

			if ( 'image' === type ) {
				constructor = media.view.EmbedImage;
			} else if ( 'link' === type ) {
				constructor = media.view.EmbedLink;
			} else {
				return;
			}

			this.settings( new constructor({
				controller: this.controller,
				model:      this.model.props,
				priority:   40
			}) );
		},

		loading: function() {
			this.$el.toggleClass( 'embed-loading', this.model.get('loading') );
		}
	});

	/**
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Label = media.View.extend({
		tagName: 'label',
		className: 'screen-reader-text',

		initialize: function() {
			this.value = this.options.value;
		},

		render: function() {
			this.$el.html( this.value );

			return this;
		}
	});

	/**
	 * wp.media.view.EmbedUrl
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.EmbedUrl = media.View.extend({
		tagName:   'label',
		className: 'embed-url',

		events: {
			'input':  'url',
			'keyup':  'url',
			'change': 'url'
		},

		initialize: function() {
			var self = this;

			this.$input = $('<input id="embed-url-field" type="url" />').val( this.model.get('url') );
			this.input = this.$input[0];

			this.spinner = $('<span class="spinner" />')[0];
			this.$el.append([ this.input, this.spinner ]);

			this.model.on( 'change:url', this.render, this );

			if ( this.model.get( 'url' ) ) {
				_.delay( function () {
					self.model.trigger( 'change:url' );
				}, 500 );
			}
		},
		/**
		 * @returns {wp.media.view.EmbedUrl} Returns itself to allow chaining
		 */
		render: function() {
			var $input = this.$input;

			if ( $input.is(':focus') ) {
				return;
			}

			this.input.value = this.model.get('url') || 'http://';
			/**
			 * Call `render` directly on parent class with passed arguments
			 */
			media.View.prototype.render.apply( this, arguments );
			return this;
		},

		ready: function() {
			if ( ! isTouchDevice ) {
				this.focus();
			}
		},

		url: function( event ) {
			this.model.set( 'url', event.target.value );
		},

		/**
		 * If the input is visible, focus and select its contents.
		 */
		focus: function() {
			var $input = this.$input;
			if ( $input.is(':visible') ) {
				$input.focus()[0].select();
			}
		}
	});

	/**
	 * wp.media.view.EmbedLink
	 *
	 * @class
	 * @augments wp.media.view.Settings
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.EmbedLink = media.view.Settings.extend({
		className: 'embed-link-settings',
		template:  media.template('embed-link-settings'),

		initialize: function() {
			this.spinner = $('<span class="spinner" />');
			this.$el.append( this.spinner[0] );
			this.listenTo( this.model, 'change:url', this.updateoEmbed );
		},

		updateoEmbed: function() {
			var url = this.model.get( 'url' );

			this.$('.setting.title').show();
			// clear out previous results
			this.$('.embed-container').hide().find('.embed-preview').html('');

			// only proceed with embed if the field contains more than 6 characters
			if ( url && url.length < 6 ) {
				return;
			}

			this.spinner.show();

			setTimeout( _.bind( this.fetch, this ), 500 );
		},

		fetch: function() {
			// check if they haven't typed in 500 ms
			if ( $('#embed-url-field').val() !== this.model.get('url') ) {
				return;
			}

			wp.ajax.send( 'parse-embed', {
				data : {
					post_ID: media.view.settings.post.id,
					shortcode: '[embed]' + this.model.get('url') + '[/embed]'
				}
			} ).done( _.bind( this.renderoEmbed, this ) );
		},

		renderoEmbed: function( response ) {
			var html = ( response && response.body ) || '';

			this.spinner.hide();

			this.$('.setting.title').hide();
			this.$('.embed-container').show().find('.embed-preview').html( html );
		}
	});

	/**
	 * wp.media.view.EmbedImage
	 *
	 * @class
	 * @augments wp.media.view.Settings.AttachmentDisplay
	 * @augments wp.media.view.Settings
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.EmbedImage =  media.view.Settings.AttachmentDisplay.extend({
		className: 'embed-media-settings',
		template:  media.template('embed-image-settings'),

		initialize: function() {
			/**
			 * Call `initialize` directly on parent class with passed arguments
			 */
			media.view.Settings.AttachmentDisplay.prototype.initialize.apply( this, arguments );
			this.model.on( 'change:url', this.updateImage, this );
		},

		updateImage: function() {
			this.$('img').attr( 'src', this.model.get('url') );
		}
	});

	/**
	 * wp.media.view.ImageDetails
	 *
	 * @class
	 * @augments wp.media.view.Settings.AttachmentDisplay
	 * @augments wp.media.view.Settings
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.ImageDetails = media.view.Settings.AttachmentDisplay.extend({
		className: 'image-details',
		template:  media.template('image-details'),
		events: _.defaults( media.view.Settings.AttachmentDisplay.prototype.events, {
			'click .edit-attachment': 'editAttachment',
			'click .replace-attachment': 'replaceAttachment',
			'click .advanced-toggle': 'onToggleAdvanced',
			'change [data-setting="customWidth"]': 'onCustomSize',
			'change [data-setting="customHeight"]': 'onCustomSize',
			'keyup [data-setting="customWidth"]': 'onCustomSize',
			'keyup [data-setting="customHeight"]': 'onCustomSize'
		} ),
		initialize: function() {
			// used in AttachmentDisplay.prototype.updateLinkTo
			this.options.attachment = this.model.attachment;
			this.listenTo( this.model, 'change:url', this.updateUrl );
			this.listenTo( this.model, 'change:link', this.toggleLinkSettings );
			this.listenTo( this.model, 'change:size', this.toggleCustomSize );

			media.view.Settings.AttachmentDisplay.prototype.initialize.apply( this, arguments );
		},

		prepare: function() {
			var attachment = false;

			if ( this.model.attachment ) {
				attachment = this.model.attachment.toJSON();
			}
			return _.defaults({
				model: this.model.toJSON(),
				attachment: attachment
			}, this.options );
		},

		render: function() {
			var self = this,
				args = arguments;

			if ( this.model.attachment && 'pending' === this.model.dfd.state() ) {
				this.model.dfd.done( function() {
					media.view.Settings.AttachmentDisplay.prototype.render.apply( self, args );
					self.postRender();
				} ).fail( function() {
					self.model.attachment = false;
					media.view.Settings.AttachmentDisplay.prototype.render.apply( self, args );
					self.postRender();
				} );
			} else {
				media.view.Settings.AttachmentDisplay.prototype.render.apply( this, arguments );
				this.postRender();
			}

			return this;
		},

		postRender: function() {
			setTimeout( _.bind( this.resetFocus, this ), 10 );
			this.toggleLinkSettings();
			if ( getUserSetting( 'advImgDetails' ) === 'show' ) {
				this.toggleAdvanced( true );
			}
			this.trigger( 'post-render' );
		},

		resetFocus: function() {
			this.$( '.link-to-custom' ).blur();
			this.$( '.embed-media-settings' ).scrollTop( 0 );
		},

		updateUrl: function() {
			this.$( '.image img' ).attr( 'src', this.model.get( 'url' ) );
			this.$( '.url' ).val( this.model.get( 'url' ) );
		},

		toggleLinkSettings: function() {
			if ( this.model.get( 'link' ) === 'none' ) {
				this.$( '.link-settings' ).addClass('hidden');
			} else {
				this.$( '.link-settings' ).removeClass('hidden');
			}
		},

		toggleCustomSize: function() {
			if ( this.model.get( 'size' ) !== 'custom' ) {
				this.$( '.custom-size' ).addClass('hidden');
			} else {
				this.$( '.custom-size' ).removeClass('hidden');
			}
		},

		onCustomSize: function( event ) {
			var dimension = $( event.target ).data('setting'),
				num = $( event.target ).val(),
				value;

			// Ignore bogus input
			if ( ! /^\d+/.test( num ) || parseInt( num, 10 ) < 1 ) {
				event.preventDefault();
				return;
			}

			if ( dimension === 'customWidth' ) {
				value = Math.round( 1 / this.model.get( 'aspectRatio' ) * num );
				this.model.set( 'customHeight', value, { silent: true } );
				this.$( '[data-setting="customHeight"]' ).val( value );
			} else {
				value = Math.round( this.model.get( 'aspectRatio' ) * num );
				this.model.set( 'customWidth', value, { silent: true  } );
				this.$( '[data-setting="customWidth"]' ).val( value );
			}
		},

		onToggleAdvanced: function( event ) {
			event.preventDefault();
			this.toggleAdvanced();
		},

		toggleAdvanced: function( show ) {
			var $advanced = this.$el.find( '.advanced-section' ),
				mode;

			if ( $advanced.hasClass('advanced-visible') || show === false ) {
				$advanced.removeClass('advanced-visible');
				$advanced.find('.advanced-settings').addClass('hidden');
				mode = 'hide';
			} else {
				$advanced.addClass('advanced-visible');
				$advanced.find('.advanced-settings').removeClass('hidden');
				mode = 'show';
			}

			setUserSetting( 'advImgDetails', mode );
		},

		editAttachment: function( event ) {
			var editState = this.controller.states.get( 'edit-image' );

			if ( window.imageEdit && editState ) {
				event.preventDefault();
				editState.set( 'image', this.model.attachment );
				this.controller.setState( 'edit-image' );
			}
		},

		replaceAttachment: function( event ) {
			event.preventDefault();
			this.controller.setState( 'replace-image' );
		}
	});

	/**
	 * wp.media.view.Cropper
	 *
	 * Uses the imgAreaSelect plugin to allow a user to crop an image.
	 *
	 * Takes imgAreaSelect options from
	 * wp.customize.HeaderControl.calculateImageSelectOptions via
	 * wp.customize.HeaderControl.openMM.
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Cropper = media.View.extend({
		className: 'crop-content',
		template: media.template('crop-content'),
		initialize: function() {
			_.bindAll(this, 'onImageLoad');
		},
		ready: function() {
			this.controller.frame.on('content:error:crop', this.onError, this);
			this.$image = this.$el.find('.crop-image');
			this.$image.on('load', this.onImageLoad);
			$(window).on('resize.cropper', _.debounce(this.onImageLoad, 250));
		},
		remove: function() {
			$(window).off('resize.cropper');
			this.$el.remove();
			this.$el.off();
			wp.media.View.prototype.remove.apply(this, arguments);
		},
		prepare: function() {
			return {
				title: l10n.cropYourImage,
				url: this.options.attachment.get('url')
			};
		},
		onImageLoad: function() {
			var imgOptions = this.controller.get('imgSelectOptions');
			if (typeof imgOptions === 'function') {
				imgOptions = imgOptions(this.options.attachment, this.controller);
			}

			imgOptions = _.extend(imgOptions, {parent: this.$el});
			this.trigger('image-loaded');
			this.controller.imgSelect = this.$image.imgAreaSelect(imgOptions);
		},
		onError: function() {
			var filename = this.options.attachment.get('filename');

			this.views.add( '.upload-errors', new media.view.UploaderStatusError({
				filename: media.view.UploaderStatus.prototype.filename(filename),
				message: _wpMediaViewsL10n.cropError
			}), { at: 0 });
		}
	});

	media.view.EditImage = media.View.extend({

		className: 'image-editor',
		template: media.template('image-editor'),

		initialize: function( options ) {
			this.editor = window.imageEdit;
			this.controller = options.controller;
			media.View.prototype.initialize.apply( this, arguments );
		},

		prepare: function() {
			return this.model.toJSON();
		},

		render: function() {
			media.View.prototype.render.apply( this, arguments );
			return this;
		},

		loadEditor: function() {
			var dfd = this.editor.open( this.model.get('id'), this.model.get('nonces').edit, this );
			dfd.done( _.bind( this.focus, this ) );
		},

		focus: function() {
			this.$( '.imgedit-submit .button' ).eq( 0 ).focus();
		},

		back: function() {
			var lastState = this.controller.lastState();
			this.controller.setState( lastState );
		},

		refresh: function() {
			this.model.fetch();
		},

		save: function() {
			var self = this,
				lastState = this.controller.lastState();

			this.model.fetch().done( function() {
				self.controller.setState( lastState );
			});
		}

	});

	/**
	 * wp.media.view.Spinner
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Spinner = media.View.extend({
		tagName:   'span',
		className: 'spinner',
		spinnerTimeout: false,
		delay: 400,

		show: function() {
			if ( ! this.spinnerTimeout ) {
				this.spinnerTimeout = _.delay(function( $el ) {
					$el.show();
				}, this.delay, this.$el );
			}

			return this;
		},

		hide: function() {
			this.$el.hide();
			this.spinnerTimeout = clearTimeout( this.spinnerTimeout );

			return this;
		}
	});
}(jQuery, _));
