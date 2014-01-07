/* global _wpMediaViewsL10n, confirm, getUserSetting, setUserSetting */
(function($){
	var media       = wp.media,
		Attachment  = media.model.Attachment,
		Attachments = media.model.Attachments,
		l10n;

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

	// Makes it easier to bind events using transitions.
	media.transition = function( selector, sensitivity ) {
		var deferred = $.Deferred();

		sensitivity = sensitivity || 2000;

		if ( $.support.transition ) {
			if ( ! (selector instanceof $) )
				selector = $( selector );

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
	 */
	media.controller.Region = function( options ) {
		_.extend( this, _.pick( options || {}, 'id', 'view', 'selector' ) );
	};

	// Use Backbone's self-propagating `extend` inheritance method.
	media.controller.Region.extend = Backbone.Model.extend;

	_.extend( media.controller.Region.prototype, {
		mode: function( mode ) {
			if ( ! mode )
				return this._mode;

			// Bail if we're trying to change to the current mode.
			if ( mode === this._mode )
				return this;

			this.trigger('deactivate');
			this._mode = mode;
			this.render( mode );
			this.trigger('activate');
			return this;
		},

		render: function( mode ) {
			// If no mode is provided, just re-render the current mode.
			// If the provided mode isn't active, perform a full switch.
			if ( mode && mode !== this._mode )
				return this.mode( mode );

			var set = { view: null },
				view;

			this.trigger( 'create', set );
			view = set.view;
			this.trigger( 'render', view );
			if ( view )
				this.set( view );
			return this;
		},

		get: function() {
			return this.view.views.first( this.selector );
		},

		set: function( views, options ) {
			if ( options )
				options.add = false;
			return this.view.views.set( this.selector, views, options );
		},

		trigger: function( event ) {
			var base, args;

			if ( ! this._mode )
				return;

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
	 */
	media.controller.StateMachine = function( states ) {
		this.states = new Backbone.Collection( states );
	};

	// Use Backbone's self-propagating `extend` inheritance method.
	media.controller.StateMachine.extend = Backbone.Model.extend;

	// Add events to the `StateMachine`.
	_.extend( media.controller.StateMachine.prototype, Backbone.Events, {

		// Fetch a state.
		//
		// If no `id` is provided, returns the active state.
		//
		// Implicitly creates states.
		state: function( id ) {
			// Ensure that the `states` collection exists so the `StateMachine`
			// can be used as a mixin.
			this.states = this.states || new Backbone.Collection();

			// Default to the active state.
			id = id || this._state;

			if ( id && ! this.states.get( id ) )
				this.states.add({ id: id });
			return this.states.get( id );
		},

		// Sets the active state.
		setState: function( id ) {
			var previous = this.state();

			// Bail if we're trying to select the current state, if we haven't
			// created the `states` collection, or are trying to select a state
			// that does not exist.
			if ( ( previous && id === previous.id ) || ! this.states || ! this.states.get( id ) )
				return this;

			if ( previous ) {
				previous.trigger('deactivate');
				this._lastState = previous.id;
			}

			this._state = id;
			this.state().trigger('activate');

			return this;
		},

		// Returns the previous active state.
		//
		// Call the `state()` method with no parameters to retrieve the current
		// active state.
		lastState: function() {
			if ( this._lastState )
				return this.state( this._lastState );
		}
	});

	// Map methods from the `states` collection to the `StateMachine` itself.
	_.each([ 'on', 'off', 'trigger' ], function( method ) {
		media.controller.StateMachine.prototype[ method ] = function() {
			// Ensure that the `states` collection exists so the `StateMachine`
			// can be used as a mixin.
			this.states = this.states || new Backbone.Collection();
			// Forward the method to the `states` collection.
			this.states[ method ].apply( this.states, arguments );
			return this;
		};
	});


	// wp.media.controller.State
	// ---------------------------
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
			Backbone.Model.apply( this, arguments );
			this.on( 'change:menu', this._updateMenu, this );
		},

		ready: function() {},
		activate: function() {},
		deactivate: function() {},
		reset: function() {},

		_ready: function() {
			this._updateMenu();
		},

		_preActivate: function() {
			this.active = true;
		},

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


		_deactivate: function() {
			this.active = false;

			this.frame.off( 'title:render:default', this._renderTitle, this );

			this.off( 'change:menu', this._menu, this );
			this.off( 'change:titleMode', this._title, this );
			this.off( 'change:content', this._content, this );
			this.off( 'change:toolbar', this._toolbar, this );
		},

		_title: function() {
			this.frame.title.render( this.get('titleMode') || 'default' );
		},

		_renderTitle: function( view ) {
			view.$el.text( this.get('title') || '' );
		},

		_router: function() {
			var router = this.frame.router,
				mode = this.get('router'),
				view;

			this.frame.$el.toggleClass( 'hide-router', ! mode );
			if ( ! mode )
				return;

			this.frame.router.render( mode );

			view = router.get();
			if ( view && view.select )
				view.select( this.frame.content.mode() );
		},

		_menu: function() {
			var menu = this.frame.menu,
				mode = this.get('menu'),
				view;

			if ( ! mode )
				return;

			menu.mode( mode );

			view = menu.get();
			if ( view && view.select )
				view.select( this.id );
		},

		_updateMenu: function() {
			var previous = this.previous('menu'),
				menu = this.get('menu');

			if ( previous )
				this.frame.off( 'menu:render:' + previous, this._renderMenu, this );

			if ( menu )
				this.frame.on( 'menu:render:' + menu, this._renderMenu, this );
		},

		_renderMenu: function( view ) {
			var menuItem = this.get('menuItem'),
				title = this.get('title'),
				priority = this.get('priority');

			if ( ! menuItem && title ) {
				menuItem = { text: title };

				if ( priority )
					menuItem.priority = priority;
			}

			if ( ! menuItem )
				return;

			view.set( this.id, menuItem );
		}
	});

	_.each(['toolbar','content'], function( region ) {
		media.controller.State.prototype[ '_' + region ] = function() {
			var mode = this.get( region );
			if ( mode )
				this.frame[ region ].render( mode );
		};
	});

	// wp.media.controller.Library
	// ---------------------------
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

		initialize: function() {
			var selection = this.get('selection'),
				props;

			// If a library isn't provided, query all media items.
			if ( ! this.get('library') )
				this.set( 'library', media.query() );

			// If a selection instance isn't provided, create one.
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

			if ( ! this.get('edge') )
				this.set( 'edge', 120 );

			if ( ! this.get('gutter') )
				this.set( 'gutter', 8 );

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

		display: function( attachment ) {
			var displays = this._displays;

			if ( ! displays[ attachment.cid ] )
				displays[ attachment.cid ] = new Backbone.Model( this.defaultDisplaySettings( attachment ) );

			return displays[ attachment.cid ];
		},

		defaultDisplaySettings: function( attachment ) {
			var settings = this._defaultDisplaySettings;
			if ( settings.canEmbed = this.canEmbed( attachment ) )
				settings.link = 'embed';
			return settings;
		},

		canEmbed: function( attachment ) {
			// If uploading, we know the filename but not the mime type.
			if ( ! attachment.get('uploading') ) {
				var type = attachment.get('type');
				if ( type !== 'audio' && type !== 'video' )
					return false;
			}

			return _.contains( media.view.settings.embedExts, attachment.get('filename').split('.').pop() );
		},

		syncSelection: function() {
			var selection = this.get('selection'),
				manager = this.frame._selection;

			if ( ! this.get('syncSelection') || ! manager || ! selection )
				return;

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

		recordSelection: function() {
			var selection = this.get('selection'),
				manager = this.frame._selection;

			if ( ! this.get('syncSelection') || ! manager || ! selection )
				return;

			// Record the currently active attachments, which is a combination
			// of the selection's attachments and the set of selected
			// attachments that this specific selection considered invalid.
			// Reset the difference and record the single attachment.
			if ( selection.multiple ) {
				manager.attachments.reset( selection.toArray().concat( manager.difference ) );
				manager.difference = [];
			} else {
				manager.attachments.add( selection.toArray() );
			}

			manager.single = selection._single;
		},

		refreshContent: function() {
			var selection = this.get('selection'),
				frame = this.frame,
				router = frame.router.get(),
				mode = frame.content.mode();

			// If the state is active, no items are selected, and the current
			// content mode is not an option in the state's router (provided
			// the state has a router), reset the content mode to the default.
			if ( this.active && ! selection.length && router && ! router.get( mode ) )
				this.frame.content.render( this.get('content') );
		},

		uploading: function( attachment ) {
			var content = this.frame.content;

			// If the uploader was selected, navigate to the browser.
			if ( 'upload' === content.mode() )
				this.frame.content.mode('browse');

			// Automatically select any uploading attachments.
			//
			// Selections that don't support multiple attachments automatically
			// limit themselves to one attachment (in this case, the last
			// attachment in the upload queue).
			this.get('selection').add( attachment );
		},

		saveContentMode: function() {
			// Only track the browse router on library states.
			if ( 'browse' !== this.get('router') )
				return;

			var mode = this.frame.content.mode(),
				view = this.frame.router.get();

			if ( view && view.get( mode ) )
				setUserSetting( 'libraryContent', mode );
		}
	});

	// wp.media.controller.GalleryEdit
	// -------------------------------
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

	// wp.media.controller.GalleryAdd
	// ---------------------------------
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

	// wp.media.controller.FeaturedImage
	// ---------------------------------
	media.controller.FeaturedImage = media.controller.Library.extend({
		defaults: _.defaults({
			id:         'featured-image',
			filterable: 'uploaded',
			multiple:   false,
			toolbar:    'featured-image',
			title:      l10n.setFeaturedImageTitle,
			priority:   60,

			syncSelection: false
		}, media.controller.Library.prototype.defaults ),

		initialize: function() {
			var library, comparator;

			// If we haven't been provided a `library`, create a `Selection`.
			if ( ! this.get('library') )
				this.set( 'library', media.query({ type: 'image' }) );

			media.controller.Library.prototype.initialize.apply( this, arguments );

			library    = this.get('library');
			comparator = library.comparator;

			// Overload the library's comparator to push items that are not in
			// the mirrored query to the front of the aggregate collection.
			library.comparator = function( a, b ) {
				var aInQuery = !! this.mirroring.get( a.cid ),
					bInQuery = !! this.mirroring.get( b.cid );

				if ( ! aInQuery && bInQuery )
					return -1;
				else if ( aInQuery && ! bInQuery )
					return 1;
				else
					return comparator.apply( this, arguments );
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
				attachment = Attachment.get( id );
				attachment.fetch();
			}

			selection.reset( attachment ? [ attachment ] : [] );
		}
	});


	// wp.media.controller.Embed
	// -------------------------
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
			if ( this.props.get('url') )
				this.trigger( 'scan', attributes );

			if ( attributes.scanners.length ) {
				scanners = attributes.scanners = $.when.apply( $, attributes.scanners );
				scanners.always( function() {
					if ( embed.get('scanners') === scanners )
						embed.set( 'loading', false );
				});
			} else {
				attributes.scanners = null;
			}

			attributes.loading = !! attributes.scanners;
			this.set( attributes );
		},

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

				if ( state !== frame.state() || url !== state.props.get('url') )
					return;

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

			if ( this.active )
				this.refresh();
		}
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	// wp.media.View
	// -------------
	//
	// The base view class.
	//
	// Undelegating events, removing events from the model, and
	// removing events from the controller mirror the code for
	// `Backbone.View.dispose` in Backbone 0.9.8 development.
	//
	// This behavior has since been removed, and should not be used
	// outside of the media manager.
	media.View = wp.Backbone.View.extend({
		constructor: function( options ) {
			if ( options && options.controller )
				this.controller = options.controller;

			wp.Backbone.View.apply( this, arguments );
		},

		dispose: function() {
			// Undelegating events, removing events from the model, and
			// removing events from the controller mirror the code for
			// `Backbone.View.dispose` in Backbone 0.9.8 development.
			this.undelegateEvents();

			if ( this.model && this.model.off )
				this.model.off( null, null, this );

			if ( this.collection && this.collection.off )
				this.collection.off( null, null, this );

			// Unbind controller events.
			if ( this.controller && this.controller.off )
				this.controller.off( null, null, this );

			return this;
		},

		remove: function() {
			this.dispose();
			return wp.Backbone.View.prototype.remove.apply( this, arguments );
		}
	});

	/**
	 * wp.media.view.Frame
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

			if ( this.options.states )
				this.states.add( this.options.states );
		},

		reset: function() {
			this.states.invoke( 'trigger', 'reset' );
			return this;
		}
	});

	// Make the `Frame` a `StateMachine`.
	_.extend( media.view.Frame.prototype, media.controller.StateMachine.prototype );

	/**
	 * wp.media.view.MediaFrame
	 */
	media.view.MediaFrame = media.view.Frame.extend({
		className: 'media-frame',
		template:  media.template('media-frame'),
		regions:   ['menu','title','content','toolbar','router'],

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
			if ( wp.Uploader.limitExceeded || ! wp.Uploader.browser.supported )
				this.options.uploader = false;

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

		render: function() {
			// Activate the default state if no active state exists.
			if ( ! this.state() && this.options.state )
				this.setState( this.options.state );

			return media.view.Frame.prototype.render.apply( this, arguments );
		},

		createTitle: function( title ) {
			title.view = new media.View({
				controller: this,
				tagName: 'h1'
			});
		},

		createMenu: function( menu ) {
			menu.view = new media.view.Menu({
				controller: this
			});
		},

		createToolbar: function( toolbar ) {
			toolbar.view = new media.view.Toolbar({
				controller: this
			});
		},

		createRouter: function( router ) {
			router.view = new media.view.Router({
				controller: this
			});
		},

		createIframeStates: function( options ) {
			var settings = media.view.settings,
				tabs = settings.tabs,
				tabUrl = settings.tabUrl,
				$postId;

			if ( ! tabs || ! tabUrl )
				return;

			// Add the post ID to the tab URL if it exists.
			$postId = $('#post_ID');
			if ( $postId.length )
				tabUrl += '&post_id=' + $postId.val();

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

		iframeContent: function( content ) {
			this.$el.addClass('hide-toolbar');
			content.view = new media.view.Iframe({
				controller: this
			});
		},

		iframeMenu: function( view ) {
			var views = {};

			if ( ! view )
				return;

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

			if ( ! window.tb_remove || this._tb_remove )
				return;

			this._tb_remove = window.tb_remove;
			window.tb_remove = function() {
				frame.close();
				frame.reset();
				frame.setState( frame.options.state );
				frame._tb_remove.call( window );
			};
		},

		restoreThickbox: function() {
			if ( ! this._tb_remove )
				return;

			window.tb_remove = this._tb_remove;
			delete this._tb_remove;
		}
	});

	// Map some of the modal's methods to the frame.
	_.each(['open','close','attach','detach','escape'], function( method ) {
		media.view.MediaFrame.prototype[ method ] = function() {
			if ( this.modal )
				this.modal[ method ].apply( this.modal, arguments );
			return this;
		};
	});

	/**
	 * wp.media.view.MediaFrame.Select
	 */
	media.view.MediaFrame.Select = media.view.MediaFrame.extend({
		initialize: function() {
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
				attachments: new Attachments(),
				difference: []
			};
		},

		createStates: function() {
			var options = this.options;

			if ( this.options.states )
				return;

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

		// Content
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

				AttachmentView: state.get('AttachmentView')
			});
		},

		uploadContent: function() {
			this.$el.removeClass('hide-toolbar');
			this.content.set( new media.view.UploaderInline({
				controller: this
			}) );
		},

		// Toolbars
		createSelectToolbar: function( toolbar, options ) {
			options = options || this.options.button || {};
			options.controller = this;

			toolbar.view = new media.view.Toolbar.Select( options );
		}
	});

	/**
	 * wp.media.view.MediaFrame.Post
	 */
	media.view.MediaFrame.Post = media.view.MediaFrame.Select.extend({
		initialize: function() {
			_.defaults( this.options, {
				multiple:  true,
				editing:   false,
				state:    'insert'
			});

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

				// Gallery states.
				new media.controller.GalleryEdit({
					library: options.selection,
					editing: options.editing,
					menu:    'gallery'
				}),

				new media.controller.GalleryAdd()
			]);


			if ( media.view.settings.post.featuredImageId ) {
				this.states.add( new media.controller.FeaturedImage() );
			}
		},

		bindHandlers: function() {
			media.view.MediaFrame.Select.prototype.bindHandlers.apply( this, arguments );
			this.on( 'menu:create:gallery', this.createMenu, this );
			this.on( 'toolbar:create:main-insert', this.createToolbar, this );
			this.on( 'toolbar:create:main-gallery', this.createToolbar, this );
			this.on( 'toolbar:create:featured-image', this.featuredImageToolbar, this );
			this.on( 'toolbar:create:main-embed', this.mainEmbedToolbar, this );

			var handlers = {
					menu: {
						'default': 'mainMenu',
						'gallery': 'galleryMenu'
					},

					content: {
						'embed':          'embedContent',
						'edit-selection': 'editSelectionContent'
					},

					toolbar: {
						'main-insert':      'mainInsertToolbar',
						'main-gallery':     'mainGalleryToolbar',
						'gallery-edit':     'galleryEditToolbar',
						'gallery-add':      'galleryAddToolbar'
					}
				};

			_.each( handlers, function( regionHandlers, region ) {
				_.each( regionHandlers, function( callback, handler ) {
					this.on( region + ':render:' + handler, this[ callback ], this );
				}, this );
			}, this );
		},

		// Menus
		mainMenu: function( view ) {
			view.set({
				'library-separator': new media.View({
					className: 'separator',
					priority: 100
				})
			});
		},

		galleryMenu: function( view ) {
			var lastState = this.lastState(),
				previous = lastState && lastState.id,
				frame = this;

			view.set({
				cancel: {
					text:     l10n.cancelGalleryTitle,
					priority: 20,
					click:    function() {
						if ( previous )
							frame.setState( previous );
						else
							frame.close();
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

		// Toolbars
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

		mainInsertToolbar: function( view ) {
			var controller = this;

			this.selectionStatusToolbar( view );

			view.set( 'insert', {
				style:    'primary',
				priority: 80,
				text:     l10n.insertIntoPost,
				requires: { selection: true },

				click: function() {
					var state = controller.state(),
						selection = state.get('selection');

					controller.close();
					state.trigger( 'insert', selection ).reset();
				}
			});
		},

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
		}
	});

	/**
	 * wp.media.view.Modal
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

		prepare: function() {
			return {
				title: this.options.title
			};
		},

		attach: function() {
			if ( this.views.attached )
				return this;

			if ( ! this.views.rendered )
				this.render();

			this.$el.appendTo( this.options.container );

			// Manually mark the view as attached and trigger ready.
			this.views.attached = true;
			this.views.ready();

			return this.propagate('attach');
		},

		detach: function() {
			if ( this.$el.is(':visible') )
				this.close();

			this.$el.detach();
			this.views.attached = false;
			return this.propagate('detach');
		},

		open: function() {
			var $el = this.$el,
				options = this.options;

			if ( $el.is(':visible') )
				return this;

			if ( ! this.views.attached )
				this.attach();

			// If the `freeze` option is set, record the window's scroll position.
			if ( options.freeze ) {
				this._freeze = {
					scrollTop: $( window ).scrollTop()
				};
			}

			$el.show().focus();
			return this.propagate('open');
		},

		close: function( options ) {
			var freeze = this._freeze;

			if ( ! this.views.attached || ! this.$el.is(':visible') )
				return this;

			this.$el.hide();
			this.propagate('close');

			// If the `freeze` option is set, restore the container's scroll position.
			if ( freeze ) {
				$( window ).scrollTop( freeze.scrollTop );
			}

			if ( options && options.escape )
				this.propagate('escape');

			return this;
		},

		escape: function() {
			return this.close({ escape: true });
		},

		escapeHandler: function( event ) {
			event.preventDefault();
			this.escape();
		},

		content: function( content ) {
			this.views.set( '.media-modal-content', content );
			return this;
		},

		// Triggers a modal event and if the `propagate` option is set,
		// forwards events to the modal's controller.
		propagate: function( id ) {
			this.trigger( id );

			if ( this.options.propagate )
				this.controller.trigger( id );

			return this;
		},

		keydown: function( event ) {
			// Close the modal when escape is pressed.
			if ( 27 === event.which ) {
				event.preventDefault();
				this.escape();
				return;
			}
		}
	});

	// wp.media.view.FocusManager
	// ----------------------------
	media.view.FocusManager = media.View.extend({
		events: {
			keydown: 'recordTab',
			focusin: 'updateIndex'
		},

		focus: function() {
			if ( _.isUndefined( this.index ) )
				return;

			// Update our collection of `$tabbables`.
			this.$tabbables = this.$(':tabbable');

			// If tab is saved, focus it.
			this.$tabbables.eq( this.index ).focus();
		},

		recordTab: function( event ) {
			// Look for the tab key.
			if ( 9 !== event.keyCode )
				return;

			// First try to update the index.
			if ( _.isUndefined( this.index ) )
				this.updateIndex( event );

			// If we still don't have an index, bail.
			if ( _.isUndefined( this.index ) )
				return;

			var index = this.index + ( event.shiftKey ? -1 : 1 );

			if ( index >= 0 && index < this.$tabbables.length )
				this.index = index;
			else
				delete this.index;
		},

		updateIndex: function( event ) {
			this.$tabbables = this.$(':tabbable');

			var index = this.$tabbables.index( event.target );

			if ( -1 === index )
				delete this.index;
			else
				this.index = index;
		}
	});

	// wp.media.view.UploaderWindow
	// ----------------------------
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
			if ( uploader.dropzone && ! (uploader.dropzone instanceof $) )
				uploader.dropzone = $( uploader.dropzone );

			this.controller.on( 'activate', this.refresh, this );
		},

		refresh: function() {
			if ( this.uploader )
				this.uploader.refresh();
		},

		ready: function() {
			var postId = media.view.settings.post.id,
				dropzone;

			// If the uploader already exists, bail.
			if ( this.uploader )
				return;

			if ( postId )
				this.options.uploader.params.post_id = postId;

			this.uploader = new wp.Uploader( this.options.uploader );

			dropzone = this.uploader.dropzone;
			dropzone.on( 'dropzone:enter', _.bind( this.show, this ) );
			dropzone.on( 'dropzone:leave', _.bind( this.hide, this ) );
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
				if ( '0' === $el.css('opacity') )
					$el.hide();
			});
		}
	});

	media.view.UploaderInline = media.View.extend({
		tagName:   'div',
		className: 'uploader-inline',
		template:  media.template('uploader-inline'),

		initialize: function() {
			_.defaults( this.options, {
				message: '',
				status:  true
			});

			if ( ! this.options.$browser && this.controller.uploader )
				this.options.$browser = this.controller.uploader.$browser;

			if ( _.isUndefined( this.options.postId ) )
				this.options.postId = media.view.settings.post.id;

			if ( this.options.status ) {
				this.views.set( '.upload-inline-status', new media.view.UploaderStatus({
					controller: this.controller
				}) );
			}
		},

		dispose: function() {
			if ( this.disposing )
				return media.View.prototype.dispose.apply( this, arguments );

			// Run remove on `dispose`, so we can be sure to refresh the
			// uploader with a view-less DOM. Track whether we're disposing
			// so we don't trigger an infinite loop.
			this.disposing = true;
			return this.remove();
		},

		remove: function() {
			var result = media.View.prototype.remove.apply( this, arguments );

			_.defer( _.bind( this.refresh, this ) );
			return result;
		},

		refresh: function() {
			var uploader = this.controller.uploader;

			if ( uploader )
				uploader.refresh();
		},

		ready: function() {
			var $browser = this.options.$browser,
				$placeholder;

			if ( this.controller.uploader ) {
				$placeholder = this.$('.browser');

				// Check if we've already replaced the placeholder.
				if ( $placeholder[0] === $browser[0] )
					return;

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

		dispose: function() {
			wp.Uploader.queue.off( null, null, this );
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

			if ( ! $bar || ! queue.length )
				return;

			$bar.width( ( queue.reduce( function( memo, attachment ) {
				if ( ! attachment.get('uploading') )
					return memo + 100;

				var percent = attachment.get('percent');
				return memo + ( _.isNumber( percent ) ? percent : 100 );
			}, 0 ) / queue.length ) + '%' );
		},

		info: function() {
			var queue = this.queue,
				index = 0, active;

			if ( ! queue.length )
				return;

			active = this.queue.find( function( attachment, i ) {
				index = i;
				return attachment.get('uploading');
			});

			this.$index.text( index + 1 );
			this.$total.text( queue.length );
			this.$filename.html( active ? this.filename( active.get('filename') ) : '' );
		},

		filename: function( filename ) {
			return media.truncate( _.escape( filename ), 24 );
		},

		error: function( error ) {
			this.views.add( '.upload-errors', new media.view.UploaderStatusError({
				filename: this.filename( error.get('file').name ),
				message:  error.get('message')
			}), { at: 0 });
		},

		dismiss: function( event ) {
			var errors = this.views.get('.upload-errors');

			event.preventDefault();

			if ( errors )
				_.invoke( errors, 'remove' );
			wp.Uploader.errors.reset();
		}
	});

	media.view.UploaderStatusError = media.View.extend({
		className: 'upload-error',
		template:  media.template('uploader-status-error')
	});

	/**
	 * wp.media.view.Toolbar
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

			if ( this.options.items )
				this.set( this.options.items, { silent: true });

			if ( ! this.options.silent )
				this.render();

			if ( selection )
				selection.on( 'add remove reset', this.refresh, this );
			if ( library )
				library.on( 'add remove reset', this.refresh, this );
		},

		dispose: function() {
			if ( this.selection )
				this.selection.off( null, null, this );
			if ( this.library )
				this.library.off( null, null, this );
			return media.View.prototype.dispose.apply( this, arguments );
		},

		ready: function() {
			this.refresh();
		},

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

			if ( ! options.silent )
				this.refresh();

			return this;
		},

		get: function( id ) {
			return this._views[ id ];
		},

		unset: function( id, options ) {
			delete this._views[ id ];
			this.primary.unset( id, options );
			this.secondary.unset( id, options );

			if ( ! options || ! options.silent )
				this.refresh();
			return this;
		},

		refresh: function() {
			var state = this.controller.state(),
				library = state.get('library'),
				selection = state.get('selection');

			_.each( this._views, function( button ) {
				if ( ! button.model || ! button.options || ! button.options.requires )
					return;

				var requires = button.options.requires,
					disabled = false;

				// Prevent insertion of attachments if any of them are still uploading
				disabled = _.some( selection.models, function( attachment ) {
					return attachment.get('uploading') === true;
				});

				if ( requires.selection && selection && ! selection.length )
					disabled = true;
				else if ( requires.library && library && ! library.length )
					disabled = true;

				button.model.set( 'disabled', disabled );
			});
		}
	});

	// wp.media.view.Toolbar.Select
	// ----------------------------
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

			media.view.Toolbar.prototype.initialize.apply( this, arguments );
		},

		clickSelect: function() {
			var options = this.options,
				controller = this.controller;

			if ( options.close )
				controller.close();

			if ( options.event )
				controller.state().trigger( options.event );

			if ( options.state )
				controller.setState( options.state );

			if ( options.reset )
				controller.reset();
		}
	});

	// wp.media.view.Toolbar.Embed
	// ---------------------------
	media.view.Toolbar.Embed = media.view.Toolbar.Select.extend({
		initialize: function() {
			_.defaults( this.options, {
				text: l10n.insertIntoPost,
				requires: false
			});

			media.view.Toolbar.Select.prototype.initialize.apply( this, arguments );
		},

		refresh: function() {
			var url = this.controller.state().props.get('url');
			this.get('select').model.set( 'disabled', ! url || url === 'http://' );

			media.view.Toolbar.Select.prototype.refresh.apply( this, arguments );
		}
	});

	/**
	 * wp.media.view.Button
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
			// Create a model with the provided `defaults`.
			this.model = new Backbone.Model( this.defaults );

			// If any of the `options` have a key from `defaults`, apply its
			// value to the `model` and remove it from the `options object.
			_.each( this.defaults, function( def, key ) {
				var value = this.options[ key ];
				if ( _.isUndefined( value ) )
					return;

				this.model.set( key, value );
				delete this.options[ key ];
			}, this );

			this.model.on( 'change', this.render, this );
		},

		render: function() {
			var classes = [ 'button', this.className ],
				model = this.model.toJSON();

			if ( model.style )
				classes.push( 'button-' + model.style );

			if ( model.size )
				classes.push( 'button-' + model.size );

			classes = _.uniq( classes.concat( this.options.classes ) );
			this.el.className = classes.join(' ');

			this.$el.attr( 'disabled', model.disabled );
			this.$el.text( this.model.get('text') );

			return this;
		},

		click: function( event ) {
			if ( '#' === this.attributes.href )
				event.preventDefault();

			if ( this.options.click && ! this.model.get('disabled') )
				this.options.click.apply( this, arguments );
		}
	});

	/**
	 * wp.media.view.ButtonGroup
	 */
	media.view.ButtonGroup = media.View.extend({
		tagName:   'div',
		className: 'button-group button-large media-button-group',

		initialize: function() {
			this.buttons = _.map( this.options.buttons || [], function( button ) {
				if ( button instanceof Backbone.View )
					return button;
				else
					return new media.view.Button( button ).render();
			});

			delete this.options.buttons;

			if ( this.options.classes )
				this.$el.addClass( this.options.classes );
		},

		render: function() {
			this.$el.html( $( _.pluck( this.buttons, 'el' ) ).detach() );
			return this;
		}
	});

	/**
	 * wp.media.view.PriorityList
	 */

	media.view.PriorityList = media.View.extend({
		tagName:   'div',

		initialize: function() {
			this._views = {};

			this.set( _.extend( {}, this._views, this.options.views ), { silent: true });
			delete this.options.views;

			if ( ! this.options.silent )
				this.render();
		},

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

			if ( ! (view instanceof Backbone.View) )
				view = this.toView( view, id, options );

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

		get: function( id ) {
			return this._views[ id ];
		},

		unset: function( id ) {
			var view = this.get( id );

			if ( view )
				view.remove();

			delete this._views[ id ];
			return this;
		},

		toView: function( options ) {
			return new media.View( options );
		}
	});

	/**
	 * wp.media.view.MenuItem
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

		_click: function( event ) {
			var clickOverride = this.options.click;

			if ( event )
				event.preventDefault();

			if ( clickOverride )
				clickOverride.call( this );
			else
				this.click();
		},

		click: function() {
			var state = this.options.state;
			if ( state )
				this.controller.setState( state );
		},

		render: function() {
			var options = this.options;

			if ( options.text )
				this.$el.text( options.text );
			else if ( options.html )
				this.$el.html( options.html );

			return this;
		}
	});

	/**
	 * wp.media.view.Menu
	 */
	media.view.Menu = media.view.PriorityList.extend({
		tagName:   'div',
		className: 'media-menu',
		property:  'state',
		ItemView:  media.view.MenuItem,
		region:    'menu',

		toView: function( options, id ) {
			options = options || {};
			options[ this.property ] = options[ this.property ] || id;
			return new this.ItemView( options ).render();
		},

		ready: function() {
			media.view.PriorityList.prototype.ready.apply( this, arguments );
			this.visibility();
		},

		set: function() {
			media.view.PriorityList.prototype.set.apply( this, arguments );
			this.visibility();
		},

		unset: function() {
			media.view.PriorityList.prototype.unset.apply( this, arguments );
			this.visibility();
		},

		visibility: function() {
			var region = this.region,
				view = this.controller[ region ].get(),
				views = this.views.get(),
				hide = ! views || views.length < 2;

			if ( this === view )
				this.controller.$el.toggleClass( 'hide-' + region, hide );
		},

		select: function( id ) {
			var view = this.get( id );

			if ( ! view )
				return;

			this.deselect();
			view.$el.addClass('active');
		},

		deselect: function() {
			this.$el.children().removeClass('active');
		}
	});

	/**
	 * wp.media.view.RouterItem
	 */
	media.view.RouterItem = media.view.MenuItem.extend({
		click: function() {
			var contentMode = this.options.contentMode;
			if ( contentMode )
				this.controller.content.mode( contentMode );
		}
	});

	/**
	 * wp.media.view.Router
	 */
	media.view.Router = media.view.Menu.extend({
		tagName:   'div',
		className: 'media-router',
		property:  'contentMode',
		ItemView:  media.view.RouterItem,
		region:    'router',

		initialize: function() {
			this.controller.on( 'content:render', this.update, this );
			media.view.Menu.prototype.initialize.apply( this, arguments );
		},

		update: function() {
			var mode = this.controller.content.mode();
			if ( mode )
				this.select( mode );
		}
	});


	/**
	 * wp.media.view.Sidebar
	 */
	media.view.Sidebar = media.view.PriorityList.extend({
		className: 'media-sidebar'
	});

	/**
	 * wp.media.view.Attachment
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
			if ( selection )
				selection.on( 'reset', this.updateSelect, this );

			// Update the model's details view.
			this.model.on( 'selection:single selection:unsingle', this.details, this );
			this.details( this.model, this.controller.state().get('selection') );
		},

		dispose: function() {
			var selection = this.options.selection;

			// Make sure all settings are saved before removing the view.
			this.updateAll();

			if ( selection )
				selection.off( null, null, this );

			media.View.prototype.dispose.apply( this, arguments );
			return this;
		},

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

			if ( 'image' === options.type )
				options.size = this.imageSize();

			options.can = {};
			if ( options.nonces ) {
				options.can.remove = !! options.nonces['delete'];
				options.can.save = !! options.nonces.update;
			}

			if ( this.controller.state().get('allowLocalEdits') )
				options.allowLocalEdits = true;

			this.views.detach();
			this.$el.html( this.template( options ) );

			this.$el.toggleClass( 'uploading', options.uploading );
			if ( options.uploading )
				this.$bar = this.$('.media-progress-bar div');
			else
				delete this.$bar;

			// Check if the model is selected.
			this.updateSelect();

			// Update the save status.
			this.updateSave();

			this.views.render();

			return this;
		},

		progress: function() {
			if ( this.$bar && this.$bar.length )
				this.$bar.width( this.model.get('percent') + '%' );
		},

		toggleSelectionHandler: function( event ) {
			var method;

			if ( event.shiftKey )
				method = 'between';
			else if ( event.ctrlKey || event.metaKey )
				method = 'toggle';

			this.toggleSelection({
				method: method
			});
		},

		toggleSelection: function( options ) {
			var collection = this.collection,
				selection = this.options.selection,
				model = this.model,
				method = options && options.method,
				single, models, singleIndex, modelIndex;

			if ( ! selection )
				return;

			single = selection.single();
			method = _.isUndefined( method ) ? selection.multiple : method;

			// If the `method` is set to `between`, select all models that
			// exist between the current and the selected model.
			if ( 'between' === method && single && selection.multiple ) {
				// If the models are the same, short-circuit.
				if ( single === model )
					return;

				singleIndex = collection.indexOf( single );
				modelIndex  = collection.indexOf( this.model );

				if ( singleIndex < modelIndex )
					models = collection.models.slice( singleIndex, modelIndex + 1 );
				else
					models = collection.models.slice( modelIndex, singleIndex + 1 );

				selection.add( models ).single( model );
				return;

			// If the `method` is set to `toggle`, just flip the selection
			// status, regardless of whether the model is the single model.
			} else if ( 'toggle' === method ) {
				selection[ this.selected() ? 'remove' : 'add' ]( model ).single( model );
				return;
			}

			if ( method !== 'add' )
				method = 'reset';

			if ( this.selected() ) {
				// If the model is the single model, remove it.
				// If it is not the same as the single model,
				// it now becomes the single model.
				selection[ single === model ? 'remove' : 'single' ]( model );
			} else {
				// If the model is not selected, run the `method` on the
				// selection. By default, we `reset` the selection, but the
				// `method` can be set to `add` the model to the selection.
				selection[ method ]( model ).single( model );
			}
		},

		updateSelect: function() {
			this[ this.selected() ? 'select' : 'deselect' ]();
		},

		selected: function() {
			var selection = this.options.selection;
			if ( selection )
				return !! selection.get( this.model.cid );
		},

		select: function( model, collection ) {
			var selection = this.options.selection;

			// Check if a selection exists and if it's the collection provided.
			// If they're not the same collection, bail; we're in another
			// selection's event loop.
			if ( ! selection || ( collection && collection !== selection ) )
				return;

			this.$el.addClass('selected');
		},

		deselect: function( model, collection ) {
			var selection = this.options.selection;

			// Check if a selection exists and if it's the collection provided.
			// If they're not the same collection, bail; we're in another
			// selection's event loop.
			if ( ! selection || ( collection && collection !== selection ) )
				return;

			this.$el.removeClass('selected');
		},

		details: function( model, collection ) {
			var selection = this.options.selection,
				details;

			if ( selection !== collection )
				return;

			details = selection.single();
			this.$el.toggleClass( 'details', details === this.model );
		},

		preventDefault: function( event ) {
			event.preventDefault();
		},

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

		updateSetting: function( event ) {
			var $setting = $( event.target ).closest('[data-setting]'),
				setting, value;

			if ( ! $setting.length )
				return;

			setting = $setting.data('setting');
			value   = event.target.value;

			if ( this.model.get( setting ) !== value )
				this.save( setting, value );
		},

		// Pass all the arguments to the model's save method.
		//
		// Records the aggregate status of all save requests and updates the
		// view's classes accordingly.
		save: function() {
			var view = this,
				save = this._save = this._save || { status: 'ready' },
				request = this.model.save.apply( this.model, arguments ),
				requests = save.requests ? $.when( request, save.requests ) : request;

			// If we're waiting to remove 'Saved.', stop.
			if ( save.savedTimer )
				clearTimeout( save.savedTimer );

			this.updateSave('waiting');
			save.requests = requests;
			requests.always( function() {
				// If we've performed another request since this one, bail.
				if ( save.requests !== requests )
					return;

				view.updateSave( requests.state() === 'resolved' ? 'complete' : 'error' );
				save.savedTimer = setTimeout( function() {
					view.updateSave('ready');
					delete save.savedTimer;
				}, 2000 );
			});

		},

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

				if ( ! $input.length )
					return;

				setting = $(el).data('setting');
				value = $input.val();

				// Record the value if it changed.
				if ( model.get( setting ) !== value )
					return [ setting, value ];
			}).compact().object().value();

			if ( ! _.isEmpty( changed ) )
				model.save( changed );
		},

		removeFromLibrary: function( event ) {
			// Stop propagation so the model isn't selected.
			event.stopPropagation();

			this.collection.remove( this.model );
		},

		removeFromSelection: function( event ) {
			var selection = this.options.selection;
			if ( ! selection )
				return;

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
		media.view.Attachment.prototype[ method ] = function( model, value ) {
			var $setting = this.$('[data-setting="' + setting + '"]');

			if ( ! $setting.length )
				return this;

			// If the updated value is in sync with the value in the DOM, there
			// is no need to re-render. If we're currently editing the value,
			// it will automatically be in sync, suppressing the re-render for
			// the view we're editing, while updating any others.
			if ( value === $setting.find('input, textarea, select, [value]').val() )
				return this;

			return this.render();
		};
	});

	/**
	 * wp.media.view.Attachment.Library
	 */
	media.view.Attachment.Library = media.view.Attachment.extend({
		buttons: {
			check: true
		}
	});

	/**
	 * wp.media.view.Attachment.EditLibrary
	 */
	media.view.Attachment.EditLibrary = media.view.Attachment.extend({
		buttons: {
			close: true
		}
	});

	/**
	 * wp.media.view.Attachments
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

				if ( view )
					view.remove();
			}, this );

			this.collection.on( 'reset', this.render, this );

			// Throttle the scroll handler.
			this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();

			this.initSortable();

			_.bindAll( this, 'css' );
			this.model.on( 'change:edge change:gutter', this.css, this );
			this._resizeCss = _.debounce( _.bind( this.css, this ), this.refreshSensitivity );
			if ( this.options.resize )
				$(window).on( 'resize.attachments', this._resizeCss );
			this.css();
		},

		dispose: function() {
			this.collection.props.off( null, null, this );
			$(window).off( 'resize.attachments', this._resizeCss );
			media.View.prototype.dispose.apply( this, arguments );
		},

		css: function() {
			var $css = $( '#' + this.el.id + '-css' );

			if ( $css.length )
				$css.remove();

			media.view.Attachments.$head().append( this.cssTemplate({
				id:     this.el.id,
				edge:   this.edge(),
				gutter: this.model.get('gutter')
			}) );
		},

		edge: function() {
			var edge = this.model.get('edge'),
				gutter, width, columns;

			if ( ! this.$el.is(':visible') )
				return edge;

			gutter  = this.model.get('gutter') * 2;
			width   = this.$el.width() - gutter;
			columns = Math.ceil( width / ( edge + gutter ) );
			edge = Math.floor( ( width - ( columns * gutter ) ) / columns );
			return edge;
		},

		initSortable: function() {
			var collection = this.collection;

			if ( ! this.options.sortable || ! $.fn.sortable )
				return;

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
					}).add( model, {
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
			if ( ! this.options.sortable || ! $.fn.sortable )
				return;

			// If the `collection` has a `comparator`, disable sorting.
			var collection = this.collection,
				orderby = collection.props.get('orderby'),
				enabled = 'menuOrder' === orderby || ! collection.comparator;

			this.$el.sortable( 'option', 'disabled', ! enabled );
		},

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
			// @todo: is this still necessary?
			if ( ! this.$el.is(':visible') )
				return;

			if ( this.collection.hasMore() && this.el.scrollHeight < this.el.scrollTop + ( this.el.clientHeight * this.options.refreshThreshold ) ) {
				this.collection.more().done( this.scroll );
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

		render: function() {
			this.el.value = this.model.escape('search');
			return this;
		},

		search: function( event ) {
			if ( event.target.value )
				this.model.set( 'search', event.target.value );
			else
				this.model.unset('search');
		}
	});

	/**
	 * wp.media.view.AttachmentFilters
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
					el: $('<option></option>').val(value).text(filter.text)[0],
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

			if ( filter )
				this.model.set( filter.props );
		},

		select: function() {
			var model = this.model,
				value = 'all',
				props = model.toJSON();

			_.find( this.filters, function( filter, id ) {
				var equal = _.all( filter.props, function( prop, key ) {
					return prop === ( _.isUndefined( props[ key ] ) ? null : props[ key ] );
				});

				if ( equal )
					return value = id;
			});

			this.$el.val( value );
		}
	});

	media.view.AttachmentFilters.Uploaded = media.view.AttachmentFilters.extend({
		createFilters: function() {
			var type = this.model.get('type'),
				types = media.view.settings.mimeTypes,
				text;

			if ( types && type )
				text = types[ type ];

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

		dispose: function() {
			this.options.selection.off( null, null, this );
			media.View.prototype.dispose.apply( this, arguments );
			return this;
		},

		createToolbar: function() {
			var filters, FiltersConstructor;

			this.toolbar = new media.view.Toolbar({
				controller: this.controller
			});

			this.views.add( this.toolbar );

			filters = this.options.filters;
			if ( 'uploaded' === filters )
				FiltersConstructor = media.view.AttachmentFilters.Uploaded;
			else if ( 'all' === filters )
				FiltersConstructor = media.view.AttachmentFilters.All;

			if ( FiltersConstructor ) {
				this.toolbar.set( 'filters', new FiltersConstructor({
					controller: this.controller,
					model:      this.collection.props,
					priority:   -80
				}).render() );
			}

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
		},

		updateContent: function() {
			var view = this;

			if( ! this.attachments )
				this.createAttachments();

			if ( ! this.collection.length ) {
				this.collection.more().done( function() {
					if ( ! view.collection.length )
						view.createUploader();
				});
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

			if ( selection.single() )
				this.createSingle();
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
			if ( ! this.$el.children().length )
				return;

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
			if ( this.options.editable )
				this.options.editable.call( this, this.collection );
		},

		clear: function( event ) {
			event.preventDefault();
			this.collection.reset();
		}
	});


	/**
	 * wp.media.view.Attachment.Selection
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
			return media.view.Attachments.prototype.initialize.apply( this, arguments );
		}
	});

	/**
	 * wp.media.view.Attachments.EditSelection
	 */
	media.view.Attachment.EditSelection = media.view.Attachment.Selection.extend({
		buttons: {
			close: true
		}
	});


	/**
	 * wp.media.view.Settings
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

		render: function() {
			media.View.prototype.render.apply( this, arguments );
			// Select the correct values.
			_( this.model.attributes ).chain().keys().each( this.update, this );
			return this;
		},

		update: function( key ) {
			var value = this.model.get( key ),
				$setting = this.$('[data-setting="' + key + '"]'),
				$buttons, $value;

			// Bail if we didn't find a matching setting.
			if ( ! $setting.length )
				return;

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
				if ( ! $setting.is(':focus') )
					$setting.val( value );

			// Handle checkboxes.
			} else if ( $setting.is('input[type="checkbox"]') ) {
				$setting.attr( 'checked', !! value );
			}
		},

		updateHandler: function( event ) {
			var $setting = $( event.target ).closest('[data-setting]'),
				value = event.target.value,
				userSetting;

			event.preventDefault();

			if ( ! $setting.length )
				return;

			// Use the correct value for checkboxes.
			if ( $setting.is('input[type="checkbox"]') )
				value = $setting[0].checked;

			// Update the corresponding setting.
			this.model.set( $setting.data('setting'), value );

			// If the setting has a corresponding user setting,
			// update that as well.
			if ( userSetting = $setting.data('userSetting') )
				setUserSetting( userSetting, value );
		},

		updateChanges: function( model ) {
			if ( model.hasChanged() )
				_( model.changed ).chain().keys().each( this.update, this );
		}
	});

	/**
	 * wp.media.view.Settings.AttachmentDisplay
	 */
	media.view.Settings.AttachmentDisplay = media.view.Settings.extend({
		className: 'attachment-display-settings',
		template:  media.template('attachment-display-settings'),

		initialize: function() {
			var attachment = this.options.attachment;

			_.defaults( this.options, {
				userSettings: false
			});

			media.view.Settings.prototype.initialize.apply( this, arguments );
			this.model.on( 'change:link', this.updateLinkTo, this );

			if ( attachment )
				attachment.on( 'change:uploading', this.render, this );
		},

		dispose: function() {
			var attachment = this.options.attachment;
			if ( attachment )
				attachment.off( null, null, this );

			media.view.Settings.prototype.dispose.apply( this, arguments );
		},

		render: function() {
			var attachment = this.options.attachment;
			if ( attachment ) {
				_.extend( this.options, {
					sizes: attachment.get('sizes'),
					type:  attachment.get('type')
				});
			}

			media.view.Settings.prototype.render.call( this );
			this.updateLinkTo();
			return this;
		},

		updateLinkTo: function() {
			var linkTo = this.model.get('link'),
				$input = this.$('.link-to-custom'),
				attachment = this.options.attachment;

			if ( 'none' === linkTo || 'embed' === linkTo || ( ! attachment && 'custom' !== linkTo ) ) {
				$input.hide();
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

			$input.show();

			// If the input is visible, focus and select its contents.
			if ( $input.is(':visible') )
				$input.focus()[0].select();
		}
	});

	/**
	 * wp.media.view.Settings.Gallery
	 */
	media.view.Settings.Gallery = media.view.Settings.extend({
		className: 'gallery-settings',
		template:  media.template('gallery-settings')
	});

	/**
	 * wp.media.view.Attachment.Details
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
			'click .edit-attachment':         'editAttachment',
			'click .refresh-attachment':      'refreshAttachment'
		},

		initialize: function() {
			this.focusManager = new media.view.FocusManager({
				el: this.el
			});

			media.view.Attachment.prototype.initialize.apply( this, arguments );
		},

		render: function() {
			media.view.Attachment.prototype.render.apply( this, arguments );
			this.focusManager.focus();
			return this;
		},

		deleteAttachment: function( event ) {
			event.preventDefault();

			if ( confirm( l10n.warnDelete ) )
				this.model.destroy();
		},

		editAttachment: function() {
			this.$el.addClass('needs-refresh');
		},

		refreshAttachment: function( event ) {
			this.$el.removeClass('needs-refresh');
			event.preventDefault();
			this.model.fetch();
		}
	});

	/**
	 * wp.media.view.AttachmentCompat
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
			this.focusManager = new media.view.FocusManager({
				el: this.el
			});

			this.model.on( 'change:compat', this.render, this );
		},

		dispose: function() {
			if ( this.$(':focus').length )
				this.save();

			return media.View.prototype.dispose.apply( this, arguments );
		},

		render: function() {
			var compat = this.model.get('compat');
			if ( ! compat || ! compat.item )
				return;

			this.views.detach();
			this.$el.html( compat.item );
			this.views.render();

			this.focusManager.focus();
			return this;
		},

		preventDefault: function( event ) {
			event.preventDefault();
		},

		save: function( event ) {
			var data = {};

			if ( event )
				event.preventDefault();

			_.each( this.$el.serializeArray(), function( pair ) {
				data[ pair.name ] = pair.value;
			});

			this.model.saveCompat( data );
		}
	});

	/**
	 * wp.media.view.Iframe
	 */
	media.view.Iframe = media.View.extend({
		className: 'media-iframe',

		render: function() {
			this.views.detach();
			this.$el.html( '<iframe src="' + this.controller.state().get('src') + '" />' );
			this.views.render();
			return this;
		}
	});

	/**
	 * wp.media.view.Embed
	 */
	media.view.Embed = media.View.extend({
		className: 'media-embed',

		initialize: function() {
			this.url = new media.view.EmbedUrl({
				controller: this.controller,
				model:      this.model.props
			}).render();

			this.views.set([ this.url ]);
			this.refresh();
			this.model.on( 'change:type', this.refresh, this );
			this.model.on( 'change:loading', this.loading, this );
		},

		settings: function( view ) {
			if ( this._settings )
				this._settings.remove();
			this._settings = view;
			this.views.add( view );
		},

		refresh: function() {
			var type = this.model.get('type'),
				constructor;

			if ( 'image' === type )
				constructor = media.view.EmbedImage;
			else if ( 'link' === type )
				constructor = media.view.EmbedLink;
			else
				return;

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

		render: function() {
			var $input = this.$input;

			if ( $input.is(':focus') )
				return;

			this.input.value = this.model.get('url') || 'http://';
			media.View.prototype.render.apply( this, arguments );
			return this;
		},

		ready: function() {
			this.focus();
		},

		url: function( event ) {
			this.model.set( 'url', event.target.value );
		},

		focus: function() {
			var $input = this.$input;
			// If the input is visible, focus and select its contents.
			if ( $input.is(':visible') )
				$input.focus()[0].select();
		}
	});

	/**
	 * wp.media.view.EmbedLink
	 */
	media.view.EmbedLink = media.view.Settings.extend({
		className: 'embed-link-settings',
		template:  media.template('embed-link-settings')
	});

	/**
	 * wp.media.view.EmbedImage
	 */
	media.view.EmbedImage =  media.view.Settings.AttachmentDisplay.extend({
		className: 'embed-image-settings',
		template:  media.template('embed-image-settings'),

		initialize: function() {
			media.view.Settings.AttachmentDisplay.prototype.initialize.apply( this, arguments );
			this.model.on( 'change:url', this.updateImage, this );
		},

		updateImage: function() {
			this.$('img').attr( 'src', this.model.get('url') );
		}
	});
}(jQuery));