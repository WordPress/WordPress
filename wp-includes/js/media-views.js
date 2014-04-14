/* global _wpMediaViewsL10n, confirm, getUserSetting, setUserSetting */
(function($, _){
	var media = wp.media, l10n;

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
	 * ========================================================================
	 * CONTROLLERS
	 * ========================================================================
	 */

	/**
	 * wp.media.controller.Region
	 *
	 * @constructor
	 * @augments Backbone.Model
	 *
	 * @param {Object} [options={}]
	 */
	media.controller.Region = function( options ) {
		_.extend( this, _.pick( options || {}, 'id', 'view', 'selector' ) );
	};

	// Use Backbone's self-propagating `extend` inheritance method.
	media.controller.Region.extend = Backbone.Model.extend;

	_.extend( media.controller.Region.prototype, {
		/**
		 * Switch modes
		 *
		 * @param {string} mode
		 *
		 * @fires wp.media.controller.Region#{id}:activate:{mode}
		 * @fires wp.media.controller.Region#{id}:deactivate:{mode}
		 *
		 * @returns {wp.media.controller.Region} Returns itself to allow chaining
		 */
		mode: function( mode ) {
			if ( ! mode ) {
				return this._mode;
			}
			// Bail if we're trying to change to the current mode.
			if ( mode === this._mode ) {
				return this;
			}

			this.trigger('deactivate');
			this._mode = mode;
			this.render( mode );
			this.trigger('activate');
			return this;
		},
		/**
		 * Render a new mode, the view is set in the `create` callback method
		 *   of the extending class
		 *
		 * If no mode is provided, just re-render the current mode.
		 * If the provided mode isn't active, perform a full switch.
		 *
		 * @param {string} mode
		 *
		 * @fires wp.media.controller.Region#{id}:create:{mode}
		 * @fires wp.media.controller.Region#{id}:render:{mode}
		 *
		 * @returns {wp.media.controller.Region} Returns itself to allow chaining
		 */
		render: function( mode ) {
			if ( mode && mode !== this._mode ) {
				return this.mode( mode );
			}

			var set = { view: null },
				view;

			this.trigger( 'create', set );
			view = set.view;
			this.trigger( 'render', view );
			if ( view ) {
				this.set( view );
			}
			return this;
		},

		/**
		 * @returns {wp.media.View} Returns the selector's first subview
		 */
		get: function() {
			return this.view.views.first( this.selector );
		},

		/**
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
		 * Helper function to trigger view events based on {id}:{event}:{mode}
		 *
		 * @param {string} event
		 * @returns {undefined|wp.media.controller.Region} Returns itself to allow chaining
		 */
		trigger: function( event ) {
			var base, args;

			if ( ! this._mode ) {
				return;
			}

			args = _.toArray( arguments );
			base = this.id + ':' + event;

			// Trigger `region:action:mode` event.
			args[0] = base + ':' + this._mode;
			this.view.trigger.apply( this.view, args );

			// Trigger `region:action` event.
			args[0] = base;
			this.view.trigger.apply( this.view, args );
			return this;
		}
	});

	/**
	 * wp.media.controller.StateMachine
	 *
	 * @constructor
	 * @augments Backbone.Model
	 * @mixin
	 * @mixes Backbone.Events
	 *
	 * @param {Array} states
	 */
	media.controller.StateMachine = function( states ) {
		this.states = new Backbone.Collection( states );
	};

	// Use Backbone's self-propagating `extend` inheritance method.
	media.controller.StateMachine.extend = Backbone.Model.extend;

	// Add events to the `StateMachine`.
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
		 * @returns {wp.media.controller.State} Returns a State model
		 *    from the StateMachine collection
		 */
		lastState: function() {
			if ( this._lastState ) {
				return this.state( this._lastState );
			}
		}
	});

	// Map methods from the `states` collection to the `StateMachine` itself.
	_.each([ 'on', 'off', 'trigger' ], function( method ) {
		/**
		 * @returns {wp.media.controller.StateMachine} Returns itself to allow chaining
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
	 * A state is a step in a workflow that when set will trigger
	 * the controllers for the regions to be updated as specified. This
	 * class is the base class that the various states used in the media
	 * modals extend.
	 *
	 * @constructor
	 * @augments Backbone.Model
	 */
	media.controller.State = Backbone.Model.extend({
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
		 * @abstract
		 */
		ready: function() {},
		/**
		 * @abstract
		 */
		activate: function() {},
		/**
		 * @abstract
		 */
		deactivate: function() {},
		/**
		 * @abstract
		 */
		reset: function() {},
		/**
		 * @access private
		 */
		_ready: function() {
			this._updateMenu();
		},
		/**
		 * @access private
		 */
		_preActivate: function() {
			this.active = true;
		},
		/**
		 * @access private
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
		 */
		_title: function() {
			this.frame.title.render( this.get('titleMode') || 'default' );
		},
		/**
		 * @access private
		 */
		_renderTitle: function( view ) {
			view.$el.text( this.get('title') || '' );
		},
		/**
		 * @access private
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
		 */
		_menu: function() {
			var menu = this.frame.menu,
				mode = this.get('menu'),
				view;

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
		 * @access private
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

	media.selectionSync = {
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
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.Library = media.controller.State.extend({
		defaults: {
			id:         'library',
			multiple:   false, // false, 'add', 'reset'
			describe:   false,
			toolbar:    'select',
			sidebar:    'settings',
			content:    'upload',
			router:     'browse',
			menu:       'default',
			searchable: true,
			filterable: false,
			sortable:   true,
			title:      l10n.mediaLibraryTitle,

			// Uses a user setting to override the content mode.
			contentUserSetting: true,

			// Sync the selection from the last state when 'multiple' matches.
			syncSelection: true
		},

		/**
		 * If a library isn't provided, query all media items.
		 * If a selection instance isn't provided, create one.
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

				// If the `selection` attribute is set to an object,
				// it will use those values as the selection instance's
				// `props` model. Otherwise, it will copy the library's
				// `props` model.
				this.set( 'selection', new media.model.Selection( null, {
					multiple: this.get('multiple'),
					props: props
				}) );
			}

			if ( ! this.get('edge') ) {
				this.set( 'edge', 120 );
			}

			if ( ! this.get('gutter') ) {
				this.set( 'gutter', 8 );
			}

			this.resetDisplays();
		},

		activate: function() {
			this.syncSelection();

			wp.Uploader.queue.on( 'add', this.uploading, this );

			this.get('selection').on( 'add remove reset', this.refreshContent, this );

			if ( this.get('contentUserSetting') ) {
				this.frame.on( 'content:activate', this.saveContentMode, this );
				this.set( 'content', getUserSetting( 'libraryContent', this.get('content') ) );
			}
		},

		deactivate: function() {
			this.recordSelection();

			this.frame.off( 'content:activate', this.saveContentMode, this );

			// Unbind all event handlers that use this state as the context
			// from the selection.
			this.get('selection').off( null, null, this );

			wp.Uploader.queue.off( null, null, this );
		},

		reset: function() {
			this.get('selection').reset();
			this.resetDisplays();
			this.refreshContent();
		},

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
		 * If the uploader was selected, navigate to the browser.
		 *
		 * Automatically select any uploading attachments.
		 *
		 * Selections that don't support multiple attachments automatically
		 * limit themselves to one attachment (in this case, the last
		 * attachment in the upload queue).
		 *
		 * @param {wp.media.model.Attachment} attachment
		 */
		uploading: function( attachment ) {
			var content = this.frame.content;

			if ( 'upload' === content.mode() ) {
				this.frame.content.mode('browse');
			}
			this.get('selection').add( attachment );
		},

		/**
		 * Only track the browse router on library states.
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

	_.extend( media.controller.Library.prototype, media.selectionSync );

	/**
	 * wp.media.controller.ImageDetails
	 *
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.ImageDetails = media.controller.State.extend({
		defaults: _.defaults({
			id: 'image-details',
			toolbar: 'image-details',
			title: l10n.imageDetailsTitle,
			content: 'image-details',
			menu: 'image-details',
			router: false,
			attachment: false,
			priority: 60,
			editing: false
		}, media.controller.Library.prototype.defaults ),

		initialize: function( options ) {
			this.image = options.image;
			media.controller.State.prototype.initialize.apply( this, arguments );
		},

		activate: function() {
			this.frame.modal.$el.addClass('image-details');
		}
	});

	/**
	 * wp.media.controller.GalleryEdit
	 *
	 * @constructor
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.GalleryEdit = media.controller.Library.extend({
		defaults: {
			id:         'gallery-edit',
			multiple:   false,
			describe:   true,
			edge:       199,
			editing:    false,
			sortable:   true,
			searchable: false,
			toolbar:    'gallery-edit',
			content:    'browse',
			title:      l10n.editGalleryTitle,
			priority:   60,
			dragInfo:   true,

			// Don't sync the selection, as the Edit Gallery library
			// *is* the selection.
			syncSelection: false
		},

		initialize: function() {
			// If we haven't been provided a `library`, create a `Selection`.
			if ( ! this.get('library') )
				this.set( 'library', new media.model.Selection() );

			// The single `Attachment` view to be used in the `Attachments` view.
			if ( ! this.get('AttachmentView') )
				this.set( 'AttachmentView', media.view.Attachment.EditLibrary );
			media.controller.Library.prototype.initialize.apply( this, arguments );
		},

		activate: function() {
			var library = this.get('library');

			// Limit the library to images only.
			library.props.set( 'type', 'image' );

			// Watch for uploaded attachments.
			this.get('library').observe( wp.Uploader.queue );

			this.frame.on( 'content:render:browse', this.gallerySettings, this );

			media.controller.Library.prototype.activate.apply( this, arguments );
		},

		deactivate: function() {
			// Stop watching for uploaded attachments.
			this.get('library').unobserve( wp.Uploader.queue );

			this.frame.off( 'content:render:browse', this.gallerySettings, this );

			media.controller.Library.prototype.deactivate.apply( this, arguments );
		},

		gallerySettings: function( browser ) {
			var library = this.get('library');

			if ( ! library || ! browser )
				return;

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
	 * wp.media.controller.GalleryAdd
	 *
	 * @constructor
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.GalleryAdd = media.controller.Library.extend({
		defaults: _.defaults({
			id:           'gallery-library',
			filterable:   'uploaded',
			multiple:     'add',
			menu:         'gallery',
			toolbar:      'gallery-add',
			title:        l10n.addToGalleryTitle,
			priority:     100,

			// Don't sync the selection, as the Edit Gallery library
			// *is* the selection.
			syncSelection: false
		}, media.controller.Library.prototype.defaults ),

		initialize: function() {
			// If we haven't been provided a `library`, create a `Selection`.
			if ( ! this.get('library') )
				this.set( 'library', media.query({ type: 'image' }) );

			media.controller.Library.prototype.initialize.apply( this, arguments );
		},

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
	 * @constructor
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.CollectionEdit = media.controller.Library.extend({
		defaults: {
			multiple:     false,
			describe:     true,
			edge:         199,
			editing:      false,
			sortable:     true,
			searchable:   false,
			content:      'browse',
			priority:     60,
			dragInfo:     true,
			SettingsView: false,

			// Don't sync the selection, as the Edit {Collection} library
			// *is* the selection.
			syncSelection: false
		},

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

		activate: function() {
			var library = this.get('library');

			// Limit the library to images only.
			library.props.set( 'type', this.get( 'type' ) );

			// Watch for uploaded attachments.
			this.get('library').observe( wp.Uploader.queue );

			this.frame.on( 'content:render:browse', this.renderSettings, this );

			media.controller.Library.prototype.activate.apply( this, arguments );
		},

		deactivate: function() {
			// Stop watching for uploaded attachments.
			this.get('library').unobserve( wp.Uploader.queue );

			this.frame.off( 'content:render:browse', this.renderSettings, this );

			media.controller.Library.prototype.deactivate.apply( this, arguments );
		},

		renderSettings: function( browser ) {
			var library = this.get('library'),
				collectionType = this.get('collectionType'),
				dragInfoText = this.get('dragInfoText'),
				SettingsView = this.get('SettingsView'),
				obj = {};

			if ( ! library || ! browser ) {
				return;
			}

			library[ collectionType ] = library[ collectionType ] || new Backbone.Model();

			obj[ collectionType ] = new SettingsView({
				controller: this,
				model:      library[ collectionType ],
				priority:   40
			});

			browser.sidebar.set( obj );

			if ( dragInfoText ) {
				browser.toolbar.set( 'dragInfo', new media.View({
					el: $( '<div class="instructions">' + dragInfoText + '</div>' )[0],
					priority: -40
				}) );
			}

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
	 * wp.media.controller.CollectionAdd
	 *
	 * @constructor
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.CollectionAdd = media.controller.Library.extend({
		defaults: _.defaults( {
			filterable:    'uploaded',
			multiple:      'add',
			priority:      100,
			syncSelection: false
		}, media.controller.Library.prototype.defaults ),

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
	 * @constructor
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.FeaturedImage = media.controller.Library.extend({
		defaults: _.defaults({
			id:         'featured-image',
			filterable: 'uploaded',
			multiple:   false,
			toolbar:    'featured-image',
			title:      l10n.setFeaturedImageTitle,
			priority:   60,
			syncSelection: true
		}, media.controller.Library.prototype.defaults ),

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

		activate: function() {
			this.updateSelection();
			this.frame.on( 'open', this.updateSelection, this );

			media.controller.Library.prototype.activate.apply( this, arguments );
		},

		deactivate: function() {
			this.frame.off( 'open', this.updateSelection, this );

			media.controller.Library.prototype.deactivate.apply( this, arguments );
		},

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
	 * Replace a selected single image
	 *
	 * @constructor
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.ReplaceImage = media.controller.Library.extend({
		defaults: _.defaults({
			id:         'replace-image',
			filterable: 'uploaded',
			multiple:   false,
			toolbar:    'replace',
			title:      l10n.replaceImageTitle,
			priority:   60,
			syncSelection: true
		}, media.controller.Library.prototype.defaults ),

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

		activate: function() {
			this.updateSelection();
			media.controller.Library.prototype.activate.apply( this, arguments );
		},

		updateSelection: function() {
			var selection = this.get('selection'),
				attachment = this.image.attachment;

			selection.reset( attachment ? [ attachment ] : [] );
		}
	});

	/**
	 * wp.media.controller.EditImage
	 *
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.EditImage = media.controller.State.extend({
		defaults: {
			id: 'edit-image',
			url: '',
			menu: false,
			toolbar: 'edit-image',
			title: l10n.editImage,
			content: 'edit-image'
		},

		activate: function() {
			this.listenTo( this.frame, 'toolbar:render:edit-image', this.toolbar );
		},

		deactivate: function() {
			this.stopListening( this.frame );
		},

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
	 * @constructor
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.MediaLibrary = media.controller.Library.extend({
		defaults: _.defaults({
			filterable: 'uploaded',
			priority:   80,
			syncSelection: false,
			displaySettings: false
		}, media.controller.Library.prototype.defaults ),

		initialize: function( options ) {
			this.media = options.media;
			this.type = options.type;
			this.set( 'library', media.query({ type: this.type }) );

			media.controller.Library.prototype.initialize.apply( this, arguments );
		},

		activate: function() {
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
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.Embed = media.controller.State.extend({
		defaults: {
			id:      'embed',
			url:     '',
			menu:    'default',
			content: 'embed',
			toolbar: 'main-embed',
			type:    'link',

			title:    l10n.insertFromUrlTitle,
			priority: 120
		},

		// The amount of time used when debouncing the scan.
		sensitivity: 200,

		initialize: function() {
			this.debouncedScan = _.debounce( _.bind( this.scan, this ), this.sensitivity );
			this.props = new Backbone.Model({ url: '' });
			this.props.on( 'change:url', this.debouncedScan, this );
			this.props.on( 'change:url', this.refresh, this );
			this.on( 'scan', this.scanImage, this );
		},

		/**
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
	 * Allows for a cropping step.
	 *
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.Cropper = media.controller.State.extend({
		defaults: {
			id: 'cropper',
			title: l10n.cropImage,
			toolbar: 'crop',
			content: 'crop',
			router: false,
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
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	/**
	 * wp.media.View
	 * -------------
	 *
	 * The base view class.
	 *
	 * Undelegating events, removing events from the model, and
	 * removing events from the controller mirror the code for
	 * `Backbone.View.dispose` in Backbone 0.9.8 development.
	 *
	 * This behavior has since been removed, and should not be used
	 * outside of the media manager.
	 *
	 * @constructor
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
	 * states. Only one state can be active at any given moment.
	 *
	 * @constructor
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 * @mixes wp.media.controller.StateMachine
	 */
	media.view.Frame = media.View.extend({
		initialize: function() {
			this._createRegions();
			this._createStates();
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
		 * @returns {wp.media.view.Frame} Returns itself to allow chaining
		 */
		reset: function() {
			this.states.invoke( 'trigger', 'reset' );
			return this;
		}
	});

	// Make the `Frame` a `StateMachine`.
	_.extend( media.view.Frame.prototype, media.controller.StateMachine.prototype );

	/**
	 * wp.media.view.MediaFrame
	 *
	 * Type of frame used to create the media modal.
	 *
	 * @constructor
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
	 * Type of media frame that is used to select an item or items from the media library
	 *
	 * @constructor
	 * @augments wp.media.view.MediaFrame
	 * @augments wp.media.view.Frame
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 * @mixes wp.media.controller.StateMachine
	 */
	media.view.MediaFrame.Select = media.view.MediaFrame.extend({
		initialize: function() {
			/**
			 * call 'initialize' directly on the parent class
			 */
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

		bindHandlers: function() {
			this.on( 'router:create:browse', this.createRouter, this );
			this.on( 'router:render:browse', this.browseRouter, this );
			this.on( 'content:create:browse', this.browseContent, this );
			this.on( 'content:render:upload', this.uploadContent, this );
			this.on( 'toolbar:create:select', this.createSelectToolbar, this );
		},

		// Routers
		browseRouter: function( view ) {
			view.set({
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
		 * Content
		 *
		 * @param {Object} content
		 * @this wp.media.controller.Region
		 */
		browseContent: function( content ) {
			var state = this.state();

			this.$el.removeClass('hide-toolbar');

			// Browse our library of attachments.
			content.view = new media.view.AttachmentsBrowser({
				controller: this,
				collection: state.get('library'),
				selection:  state.get('selection'),
				model:      state,
				sortable:   state.get('sortable'),
				search:     state.get('searchable'),
				filters:    state.get('filterable'),
				display:    state.get('displaySettings'),
				dragInfo:   state.get('dragInfo'),

				suggestedWidth:  state.get('suggestedWidth'),
				suggestedHeight: state.get('suggestedHeight'),

				AttachmentView: state.get('AttachmentView')
			});
		},

		/**
		 *
		 * @this wp.media.controller.Region
		 */
		uploadContent: function() {
			this.$el.removeClass('hide-toolbar');
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
	 * @constructor
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
				state:    'insert'
			});
			/**
			 * call 'initialize' directly on the parent class
			 */
			media.view.MediaFrame.Select.prototype.initialize.apply( this, arguments );
			this.createIframeStates();

		},

		createStates: function() {
			var options = this.options;

			// Add the default states.
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
				new media.controller.Embed(),

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
					dragInfoText:   l10n.playlistDragInfo,
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
			view.url.focus();
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
	 * @constructor
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
			this.on( 'menu:render:image-details', this.renderMenu, this );
			this.on( 'toolbar:render:image-details', this.renderImageDetailsToolbar, this );
			// override the select toolbar
			this.on( 'toolbar:render:replace', this.renderReplaceImageToolbar, this );
		},

		createStates: function() {
			this.states.add([
				new media.controller.ImageDetails({
					image: this.image,
					editable: false,
					menu: 'image-details'
				}),
				new media.controller.ReplaceImage({
					id: 'replace-image',
					library:   media.query( { type: 'image' } ),
					image: this.image,
					multiple:  false,
					title:     l10n.imageReplaceTitle,
					menu: 'image-details',
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

		renderMenu: function( view ) {
			var lastState = this.lastState(),
				previous = lastState && lastState.id,
				frame = this;

			view.set({
				cancel: {
					text:     l10n.imageDetailsCancel,
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
	 * @constructor
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
				options = this.options;

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

			$el.show().focus();
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

			this.$el.hide();
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
	 * @constructor
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.FocusManager = media.View.extend({
		events: {
			keydown: 'recordTab',
			focusin: 'updateIndex'
		},

		focus: function() {
			if ( _.isUndefined( this.index ) ) {
				return;
			}

			// Update our collection of `$tabbables`.
			this.$tabbables = this.$(':tabbable');

			// If tab is saved, focus it.
			this.$tabbables.eq( this.index ).focus();
		},
		/**
		 * @param {Object} event
		 */
		recordTab: function( event ) {
			// Look for the tab key.
			if ( 9 !== event.keyCode ) {
				return;
			}

			// First try to update the index.
			if ( _.isUndefined( this.index ) ) {
				this.updateIndex( event );
			}

			// If we still don't have an index, bail.
			if ( _.isUndefined( this.index ) ) {
				return;
			}

			var index = this.index + ( event.shiftKey ? -1 : 1 );

			if ( index >= 0 && index < this.$tabbables.length ) {
				this.index = index;
			} else {
				delete this.index;
			}
		},
		/**
		 * @param {Object} event
		 */
		updateIndex: function( event ) {
			this.$tabbables = this.$(':tabbable');

			var index = this.$tabbables.index( event.target );

			if ( -1 === index ) {
				delete this.index;
			} else {
				this.index = index;
			}
		}
	});

	/**
	 * wp.media.view.UploaderWindow
	 *
	 * @constructor
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
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
		}
	});

	/**
	 * wp.media.view.EditorUploader
	 *
	 * @constructor
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

		browserSupport: function() {
			var supports = false, div = document.createElement('div');

			supports = ( 'draggable' in div ) || ( 'ondragstart' in div && 'ondrop' in div );
			supports = supports && !! ( window.File && window.FileList && window.FileReader );
			return supports;
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

		drop: function( event ) {
			var $wrap = null;

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
				this.workflow.on( 'uploader:ready', this.addFiles, this );
			} else {
				this.workflow.state().reset();
				this.addFiles.apply( this );
				this.workflow.open();
			}

			return false;
		},

		addFiles: function() {
			if ( this.files.length ) {
				this.workflow.uploader.uploader.uploader.addFile( _.toArray( this.files ) );
				this.files = [];
			}
			return this;
		},

		containerDragover: function() {
			if ( this.localDrag ) {
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

		dropzoneDragover: function( e ) {
			if ( this.localDrag ) {
				return;
			}

			this.overDropzone = true;
			this.refresh( e );
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
	 * @constructor
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.UploaderInline = media.View.extend({
		tagName:   'div',
		className: 'uploader-inline',
		template:  media.template('uploader-inline'),

		initialize: function() {
			_.defaults( this.options, {
				message: '',
				status:  true
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
				suggestedHeight = this.controller.state().get('suggestedHeight');

			if ( suggestedWidth && suggestedHeight ) {
				return {
					suggestedWidth: suggestedWidth,
					suggestedHeight: suggestedHeight
				};
			}
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
		}
	});

	/**
	 * wp.media.view.UploaderStatus
	 *
	 * @constructor
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
	 * @constructor
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
	 * @constructor
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
			this.primary.$el.addClass('media-toolbar-primary');
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
	 * @constructor
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
			/**
			 * call 'initialize' directly on the parent class
			 */
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
	 * @constructor
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
			/**
			 * call 'initialize' directly on the parent class
			 */
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
	 * @constructor
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
	 * @constructor
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
	 * @constructor
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
	 * @constructor
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
		},

		click: function() {
			var state = this.options.state;
			if ( state ) {
				this.controller.setState( state );
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
	 * @constructor
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
	 * @constructor
	 * @augments wp.media.view.MenuItem
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.RouterItem = media.view.MenuItem.extend({
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
	 * @constructor
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
			/**
			 * call 'initialize' directly on the parent class
			 */
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
	 * @constructor
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
	 * @constructor
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachment = media.View.extend({
		tagName:   'li',
		className: 'attachment',
		template:  media.template('attachment'),

		events: {
			'click .attachment-preview':      'toggleSelectionHandler',
			'change [data-setting]':          'updateSetting',
			'change [data-setting] input':    'updateSetting',
			'change [data-setting] select':   'updateSetting',
			'change [data-setting] textarea': 'updateSetting',
			'click .close':                   'removeFromLibrary',
			'click .check':                   'removeFromSelection',
			'click a':                        'preventDefault'
		},

		buttons: {},

		initialize: function() {
			var selection = this.options.selection;

			this.model.on( 'change:sizes change:uploading', this.render, this );
			this.model.on( 'change:title', this._syncTitle, this );
			this.model.on( 'change:caption', this._syncCaption, this );
			this.model.on( 'change:percent', this.progress, this );

			// Update the selection.
			this.model.on( 'add', this.select, this );
			this.model.on( 'remove', this.deselect, this );
			if ( selection ) {
				selection.on( 'reset', this.updateSelect, this );
			}

			// Update the model's details view.
			this.model.on( 'selection:single selection:unsingle', this.details, this );
			this.details( this.model, this.controller.state().get('selection') );
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
				});

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

			if ( event.shiftKey ) {
				method = 'between';
			} else if ( event.ctrlKey || event.metaKey ) {
				method = 'toggle';
			}

			this.toggleSelection({
				method: method
			});
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
			var selection = this.options.selection;

			// Check if a selection exists and if it's the collection provided.
			// If they're not the same collection, bail; we're in another
			// selection's event loop.
			if ( ! selection || ( collection && collection !== selection ) ) {
				return;
			}

			this.$el.addClass('selected');
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
			this.$el.removeClass('selected');
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
			// Stop propagation so the model isn't selected.
			event.stopPropagation();

			this.collection.remove( this.model );
		},
		/**
		 * @param {Object} event
		 */
		removeFromSelection: function( event ) {
			var selection = this.options.selection;
			if ( ! selection ) {
				return;
			}

			// Stop propagation so the model isn't selected.
			event.stopPropagation();

			selection.remove( this.model );
		}
	});

	// Ensure settings remain in sync between attachment views.
	_.each({
		caption: '_syncCaption',
		title:   '_syncTitle'
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
	 * @constructor
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
	 * @constructor
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
	 * @constructor
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachments = media.View.extend({
		tagName:   'ul',
		className: 'attachments',

		cssTemplate: media.template('attachments-css'),

		events: {
			'scroll': 'scroll'
		},

		initialize: function() {
			this.el.id = _.uniqueId('__attachments-view-');

			_.defaults( this.options, {
				refreshSensitivity: 200,
				refreshThreshold:   3,
				AttachmentView:     media.view.Attachment,
				sortable:           false,
				resize:             true
			});

			this._viewsByCid = {};

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

			// Throttle the scroll handler.
			this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();

			this.initSortable();

			_.bindAll( this, 'css' );
			this.model.on( 'change:edge change:gutter', this.css, this );
			this._resizeCss = _.debounce( _.bind( this.css, this ), this.refreshSensitivity );
			if ( this.options.resize ) {
				$(window).on( 'resize.attachments', this._resizeCss );
			}
			this.css();
		},

		dispose: function() {
			this.collection.props.off( null, null, this );
			$(window).off( 'resize.attachments', this._resizeCss );
			/**
			 * call 'dispose' directly on the parent class
			 */
			media.View.prototype.dispose.apply( this, arguments );
		},

		css: function() {
			var $css = $( '#' + this.el.id + '-css' );

			if ( $css.length ) {
				$css.remove();
			}

			media.view.Attachments.$head().append( this.cssTemplate({
				id:     this.el.id,
				edge:   this.edge(),
				gutter: this.model.get('gutter')
			}) );
		},
		/**
		 * @returns {Number}
		 */
		edge: function() {
			var edge = this.model.get('edge'),
				gutter, width, columns;

			if ( ! this.$el.is(':visible') ) {
				return edge;
			}

			gutter  = this.model.get('gutter') * 2;
			width   = this.$el.width() - gutter;
			columns = Math.ceil( width / ( edge + gutter ) );
			edge = Math.floor( ( width - ( columns * gutter ) ) / columns );
			return edge;
		},

		initSortable: function() {
			var collection = this.collection;

			if ( ! this.options.sortable || ! $.fn.sortable ) {
				return;
			}

			this.$el.sortable( _.extend({
				// If the `collection` has a `comparator`, disable sorting.
				disabled: !! collection.comparator,

				// Prevent attachments from being dragged outside the bounding
				// box of the list.
				containment: this.$el,

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
			if ( ! this.options.sortable || ! $.fn.sortable ) {
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
				controller: this.controller,
				model:      attachment,
				collection: this.collection,
				selection:  this.options.selection
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
				toolbar;

			if ( ! this.$el.is(':visible') || ! this.collection.hasMore() ) {
				return;
			}

			toolbar = this.views.parent.toolbar;

			// Show the spinner only if we are close to the bottom.
			if ( this.el.scrollHeight - ( this.el.scrollTop + this.el.clientHeight ) < this.el.clientHeight / 3 ) {
				toolbar.get('spinner').show();
			}

			if ( this.el.scrollHeight < this.el.scrollTop + ( this.el.clientHeight * this.options.refreshThreshold ) ) {
				this.collection.more().done(function() {
					view.scroll();
					toolbar.get('spinner').hide();
				});
			}
		}
	}, {
		$head: (function() {
			var $head;
			return function() {
				return $head = $head || $('head');
			};
		}())
	});

	/**
	 * wp.media.view.Search
	 *
	 * @constructor
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Search = media.View.extend({
		tagName:   'input',
		className: 'search',

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
	 * @constructor
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.AttachmentFilters = media.View.extend({
		tagName:   'select',
		className: 'attachment-filters',

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

		createFilters: function() {
			this.filters = {};
		},

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
	 * wp.media.view.AttachmentFilters.Uploaded
	 *
	 * @constructor
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
				}
			};
		}
	});

	/**
	 * wp.media.view.AttachmentFilters.All
	 *
	 * @constructor
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
					type:    null,
					uploadedTo: null,
					orderby: 'date',
					order:   'DESC'
				},
				priority: 10
			};

			filters.uploaded = {
				text:  l10n.uploadedToThisPost,
				props: {
					type:    null,
					uploadedTo: media.view.settings.post.id,
					orderby: 'menuOrder',
					order:   'ASC'
				},
				priority: 20
			};

			this.filters = filters;
		}
	});


	/**
	 * wp.media.view.AttachmentsBrowser
	 *
	 * @constructor
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.AttachmentsBrowser = media.View.extend({
		tagName:   'div',
		className: 'attachments-browser',

		initialize: function() {
			_.defaults( this.options, {
				filters: false,
				search:  true,
				display: false,

				AttachmentView: media.view.Attachment.Library
			});

			this.createToolbar();
			this.updateContent();
			this.createSidebar();

			this.collection.on( 'add remove reset', this.updateContent, this );
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
			var filters, FiltersConstructor;

			/**
			 * @member {wp.media.view.Toolbar}
			 */
			this.toolbar = new media.view.Toolbar({
				controller: this.controller
			});

			this.views.add( this.toolbar );

			filters = this.options.filters;
			if ( 'uploaded' === filters ) {
				FiltersConstructor = media.view.AttachmentFilters.Uploaded;
			} else if ( 'all' === filters ) {
				FiltersConstructor = media.view.AttachmentFilters.All;
			}

			if ( FiltersConstructor ) {
				this.toolbar.set( 'filters', new FiltersConstructor({
					controller: this.controller,
					model:      this.collection.props,
					priority:   -80
				}).render() );
			}

			this.toolbar.set( 'spinner', new media.view.Spinner({
				priority: -70
			}) );

			if ( this.options.search ) {
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
			var view = this;

			if( ! this.attachments ) {
				this.createAttachments();
			}

			if ( ! this.collection.length ) {
				this.toolbar.get( 'spinner' ).show();
				this.collection.more().done(function() {
					if ( ! view.collection.length ) {
						view.createUploader();
					}
					view.toolbar.get( 'spinner' ).hide();
				});
			} else {
				view.toolbar.get( 'spinner' ).hide();
			}
		},

		removeContent: function() {
			_.each(['attachments','uploader'], function( key ) {
				if ( this[ key ] ) {
					this[ key ].remove();
					delete this[ key ];
				}
			}, this );
		},

		createUploader: function() {
			this.removeContent();

			this.uploader = new media.view.UploaderInline({
				controller: this.controller,
				status:     false,
				message:    l10n.noItemsFound
			});

			this.views.add( this.uploader );
		},

		createAttachments: function() {
			this.removeContent();

			this.attachments = new media.view.Attachments({
				controller: this.controller,
				collection: this.collection,
				selection:  this.options.selection,
				model:      this.model,
				sortable:   this.options.sortable,

				// The single `Attachment` view to be used in the `Attachments` view.
				AttachmentView: this.options.AttachmentView
			});

			this.views.add( this.attachments );
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
		},

		disposeSingle: function() {
			var sidebar = this.sidebar;
			sidebar.unset('details');
			sidebar.unset('compat');
			sidebar.unset('display');
		}
	});

	/**
	 * wp.media.view.Selection
	 *
	 * @constructor
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
				model:      new Backbone.Model({
					edge:   40,
					gutter: 5
				})
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
		}
	});


	/**
	 * wp.media.view.Attachment.Selection
	 *
	 * @constructor
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
	 * @constructor
	 * @augments wp.media.view.Attachments
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachments.Selection = media.view.Attachments.extend({
		events: {},
		initialize: function() {
			_.defaults( this.options, {
				sortable:   true,
				resize:     false,

				// The single `Attachment` view to be used in the `Attachments` view.
				AttachmentView: media.view.Attachment.Selection
			});
			/**
			 * call 'initialize' directly on the parent class
			 */
			return media.view.Attachments.prototype.initialize.apply( this, arguments );
		}
	});

	/**
	 * wp.media.view.Attachments.EditSelection
	 *
	 * @constructor
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
	 * @constructor
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
				$setting.prop( 'checked', !! value );
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
	 * @constructor
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
			/**
			 * call 'initialize' directly on the parent class
			 */
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
			if ( $input.is(':visible') ) {
				$input.focus()[0].select();
			}
		}
	});

	/**
	 * wp.media.view.Settings.Gallery
	 *
	 * @constructor
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
	 * @constructor
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
	 * @constructor
	 * @augments wp.media.view.Attachment
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachment.Details = media.view.Attachment.extend({
		tagName:   'div',
		className: 'attachment-details',
		template:  media.template('attachment-details'),

		events: {
			'change [data-setting]':          'updateSetting',
			'change [data-setting] input':    'updateSetting',
			'change [data-setting] select':   'updateSetting',
			'change [data-setting] textarea': 'updateSetting',
			'click .delete-attachment':       'deleteAttachment',
			'click .trash-attachment':        'trashAttachment',
			'click .edit-attachment':         'editAttachment',
			'click .refresh-attachment':      'refreshAttachment'
		},

		initialize: function() {
			/**
			 * @member {wp.media.view.FocusManager}
			 */
			this.focusManager = new media.view.FocusManager({
				el: this.el
			});
			/**
			 * call 'initialize' directly on the parent class
			 */
			media.view.Attachment.prototype.initialize.apply( this, arguments );
		},
		/**
		 * @returns {wp.media.view..Attachment.Details} Returns itself to allow chaining
		 */
		render: function() {
			/**
			 * call 'render' directly on the parent class
			 */
			media.view.Attachment.prototype.render.apply( this, arguments );
			this.focusManager.focus();
			return this;
		},
		/**
		 * @param {Object} event
		 */
		deleteAttachment: function( event ) {
			event.preventDefault();

			if ( confirm( l10n.warnDelete ) ) {
				this.model.destroy();
			}
		},
		/**
		 * @param {Object} event
		 */
		trashAttachment: function( event ) {
			event.preventDefault();

			this.model.destroy();
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
		}

	});

	/**
	 * wp.media.view.AttachmentCompat
	 *
	 * @constructor
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
			/**
			 * @member {wp.media.view.FocusManager}
			 */
			this.focusManager = new media.view.FocusManager({
				el: this.el
			});

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

			this.focusManager.focus();
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

			this.model.saveCompat( data );
		}
	});

	/**
	 * wp.media.view.Iframe
	 *
	 * @constructor
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
	 * @constructor
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
	 * wp.media.view.EmbedUrl
	 *
	 * @constructor
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
			this.$input = $('<input/>').attr( 'type', 'text' ).val( this.model.get('url') );
			this.input = this.$input[0];

			this.spinner = $('<span class="spinner" />')[0];
			this.$el.append([ this.input, this.spinner ]);

			this.model.on( 'change:url', this.render, this );
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
			this.focus();
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
	 * @constructor
	 * @augments wp.media.view.Settings
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.EmbedLink = media.view.Settings.extend({
		className: 'embed-link-settings',
		template:  media.template('embed-link-settings')
	});

	/**
	 * wp.media.view.EmbedImage
	 *
	 * @contructor
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
	 * @contructor
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
	 * @constructor
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
			this.editor.open( this.model.get('id'), this.model.get('nonces').edit, this );
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
	 * @constructor
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
