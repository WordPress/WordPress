(function($){
	var media       = wp.media,
		Attachment  = media.model.Attachment,
		Attachments = media.model.Attachments,
		Query       = media.model.Query,
		l10n;

	// Link any localized strings.
	l10n = media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;

	// Link any settings.
	media.view.settings = l10n.settings || {};
	delete l10n.settings;

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
	media.transition = function( selector ) {
		var deferred = $.Deferred();

		if ( $.support.transition ) {
			if ( ! (selector instanceof $) )
				selector = $( selector );

			// Resolve the deferred when the first element finishes animating.
			selector.first().one( $.support.transition.end, deferred.resolve );

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
		_.extend( this, _.pick( options || {}, 'id', 'controller', 'selector' ) );

		this.on( 'activate:empty', this.empty, this );
		this.mode('empty');
	};

	// Use Backbone's self-propagating `extend` inheritance method.
	media.controller.Region.extend = Backbone.Model.extend;

	_.extend( media.controller.Region.prototype, Backbone.Events, {
		trigger: (function() {
			var eventSplitter = /\s+/,
				trigger = Backbone.Events.trigger;

			return function( events ) {
				var mode = ':' + this._mode,
					modeEvents = events.split( eventSplitter ).join( mode ) + mode;

				trigger.apply( this, arguments );
				trigger.apply( this, [ modeEvents ].concat( _.rest( arguments ) ) );
				return this;
			};
		}()),

		mode: function( mode ) {
			if ( mode ) {
				this.trigger( 'deactivate', this );
				this._mode = mode;
				return this.trigger( 'activate', this );
			}
			return this._mode;
		},

		view: function( view ) {
			var previous = this._view,
				mode = this._mode,
				id = this.id;

			// If no argument is provided, return the current view.
			if ( ! view )
				return previous;

			// If we're attempting to switch to the current view, bail.
			if ( view === previous )
				return;

			// Add classes to the new view.
			if ( id )
				view.$el.addClass( 'region-' + id );

			if ( mode )
				view.$el.addClass( 'mode-' + mode );

			this.controller.views.set( this.selector, view );
			this._view = view;
		},

		empty: function() {
			this.view( new media.View() );
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

		// Fetch a state model.
		//
		// Implicitly creates states.
		get: function( id ) {
			// Ensure that the `states` collection exists so the `StateMachine`
			// can be used as a mixin.
			this.states = this.states || new Backbone.Collection();

			if ( ! this.states.get( id ) )
				this.states.add({ id: id });
			return this.states.get( id );
		},

		// Selects or returns the active state.
		//
		// If a `id` is provided, sets that as the current state.
		// If no parameters are provided, returns the current state object.
		state: function( id ) {
			var previous;

			if ( ! id )
				return this._state ? this.get( this._state ) : null;

			previous = this.state();

			// Bail if we're trying to select the current state, if we haven't
			// created the `states` collection, or are trying to select a state
			// that does not exist.
			if ( ( previous && id === previous.id ) || ! this.states || ! this.states.get( id ) )
				return;

			if ( previous ) {
				previous.trigger('deactivate');
				this._previous = previous.id;
			}

			this._state = id;
			this.state().trigger('activate');
		},

		previous: function() {
			return this._previous;
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
		initialize: function() {
			this.on( 'activate', this._activate, this );
			this.on( 'activate', this.activate, this );
			this.on( 'deactivate', this._deactivate, this );
			this.on( 'deactivate', this.deactivate, this );
			this.on( 'reset', this.reset, this );
		},

		activate: function() {},
		_activate: function() {
			this.active = true;

			this.menu();
			this.toolbar();
			this.sidebar();
			this.content();
		},

		deactivate: function() {},
		_deactivate: function() {
			this.active = false;
		},

		reset: function() {},

		menu: function() {
			var menu = this.frame.menu,
				mode = this.get('menu'),
				view;

			if ( ! mode )
				return;

			if ( menu.mode() !== mode )
				menu.mode( mode );

			view = menu.view();
			if ( view.select )
				view.select( this.id );
		}
	});

	_.each(['toolbar','sidebar','content'], function( region ) {
		media.controller.State.prototype[ region ] = function() {
			var mode = this.get( region );
			if ( mode )
				this.frame[ region ].mode( mode );
		};
	});

	// wp.media.controller.Library
	// ---------------------------
	media.controller.Library = media.controller.State.extend({
		defaults: {
			id:         'library',
			multiple:   false,
			describe:   false,
			toolbar:    'main-attachments',
			sidebar:    'settings',
			searchable: true
		},

		initialize: function() {
			if ( ! this.get('selection') ) {
				this.set( 'selection', new media.model.Selection( null, {
					multiple: this.get('multiple')
				}) );
			}

			if ( ! this.get('library') )
				this.set( 'library', media.query() );

			if ( ! this.get('edge') )
				this.set( 'edge', 120 );

			if ( ! this.get('gutter') )
				this.set( 'gutter', 8 );

			if ( ! this.get('details') )
				this.set( 'details', [] );

			media.controller.State.prototype.initialize.apply( this, arguments );
		},

		activate: function() {
			var selection = this.get('selection');

			this._excludeStateLibrary();
			this.buildComposite();
			this.on( 'change:library change:exclude', this.buildComposite, this );
			this.on( 'change:excludeState', this._excludeState, this );

			// If we're in a workflow that supports multiple attachments,
			// automatically select any uploading attachments.
			if ( this.get('multiple') )
				wp.Uploader.queue.on( 'add', this.selectUpload, this );

			selection.on( 'selection:single selection:unsingle', this.sidebar, this );
			selection.on( 'add remove reset', this.refreshSelection, this );

			this._updateEmpty();
			this.get('library').on( 'add remove reset', this._updateEmpty, this );
			this.on( 'change:empty', this.refresh, this );
			this.refresh();
		},

		deactivate: function() {
			this.off( 'change:empty', this.refresh, this );
			this.get('library').off( 'add remove reset', this._updateEmpty, this );

			// Unbind all event handlers that use this state as the context
			// from the selection.
			this.get('selection').off( null, null, this );

			wp.Uploader.queue.off( 'add', this.selectUpload, this );

			this.off( 'change:excludeState', this._excludeState, this );
			this.off( 'change:library change:exclude', this.buildComposite, this );

			this.destroyComposite();
		},

		reset: function() {
			this.get('selection').clear();
		},

		sidebar: function() {
			var sidebar = this.frame.sidebar;

			if ( this.get('selection').single() )
				sidebar.mode( this.get('sidebar') );
			else
				sidebar.mode('clear');
		},

		content: function() {
			var frame = this.frame;

			// Content.
			if ( this.get('empty') ) {
				// Attempt to fetch any Attachments we don't already have.
				this.get('library').more();

				// In the meantime, render an inline uploader.
				frame.content.mode('upload');
			} else {
				// Browse our library of attachments.
				frame.content.mode('browse');
			}
		},

		refresh: function() {
			this.frame.$el.toggleClass( 'hide-sidebar hide-toolbar', this.get('empty') );
			this.content();
			this.refreshSelection();
		},

		_updateEmpty: function() {
			var library = this.get('library');
			this.set( 'empty', ! library.length && ! library.props.get('search') );
		},

		refreshSelection: function() {
			var selection = this.get('selection'),
				mode = this.frame.content.mode();

			this.frame.toolbar.view().refresh();
			this.trigger( 'refresh:selection', this, selection );

			if ( ! selection.length && 'browse' !== mode && 'upload' !== mode )
				this.content();
		},

		selectUpload: function( attachment ) {
			this.get('selection').add( attachment );
		},

		buildComposite: function() {
			var original = this.get('_library'),
				exclude = this.get('exclude'),
				composite;

			this.destroyComposite();
			if ( ! this.get('exclude') )
				return;

			// Remember the state's original library.
			if ( ! original )
				this.set( '_library', original = this.get('library') );

			// Create a composite library in its place.
			composite = new media.model.Attachments( null, {
				props: _.pick( original.props.toJSON(), 'order', 'orderby' )
			});

			// Accepts attachments that exist in the original library and
			// that do not exist in the excluded library.
			composite.validator = function( attachment ) {
				return !! original.getByCid( attachment.cid ) && ! exclude.getByCid( attachment.cid );
			};

			composite.observe( original ).observe( exclude );

			// When `more()` is triggered on the composite collection,
			// pass the command over to the `original`, which will
			// populate the query.
			composite.more = _.bind( original.more, original );

			this.set( 'library', composite );
		},

		destroyComposite: function() {
			var composite = this.get('library'),
				original = this.get('_library');

			if ( ! original )
				return;

			composite.unobserve();
			this.set( 'library', original );
			this.unset('_library');
		},

		_excludeState: function() {
			var current = this.get('excludeState'),
				previous = this.previous('excludeState');

			if ( previous )
				this.frame.get( previous ).off( 'change:library', this._excludeStateLibrary, this );

			if ( current )
				this.frame.get( current ).on( 'change:library', this._excludeStateLibrary, this );
		},

		_excludeStateLibrary: function() {
			var current = this.get('excludeState');

			if ( ! current )
				return;

			this.set( 'exclude', this.frame.get( current ).get('library') );
		}
	});


	// wp.media.controller.Upload
	// ---------------------------
	media.controller.Upload = media.controller.Library.extend({
		defaults: _.defaults({
			id:         'upload',
			upload:     { text: l10n.uploadMoreFiles },
			searchable: false,
			sortable:   true
		}, media.controller.Library.prototype.defaults ),

		initialize: function() {
			var library = this.get('library');

			// If a `library` attribute isn't provided, create a new
			// `Attachments` collection that observes (and thereby receives
			// all uploading) attachments.
			if ( ! library ) {
				library = new Attachments();
				library.observe( wp.Uploader.queue );
				this.set( 'library', library );
			}

			media.controller.Library.prototype.initialize.apply( this, arguments );
		}

	});

	// wp.media.controller.Gallery
	// ---------------------------
	media.controller.Gallery = media.controller.Library.extend({
		defaults: {
			id:         'gallery-edit',
			multiple:   false,
			describe:   true,
			edge:       199,
			editing:    false,
			sortable:   true,
			searchable: false,
			toolbar:    'gallery-edit',
			sidebar:    'settings'
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

		sidebar: function() {
			media.controller.Library.prototype.sidebar.apply( this, arguments );
			this.frame.sidebar.trigger('gallery-settings');
			return this;
		}
	});


	// wp.media.controller.Embed
	// -------------------------
	media.controller.Embed = media.controller.State.extend({
		defaults: {
			id:      'embed',
			url:     '',
			menu:    'main',
			content: 'embed',
			toolbar: 'main-embed',
			type:    'link'
		},

		// The amount of time used when debouncing the scan.
		sensitivity: 200,

		initialize: function() {
			this.debouncedScan = _.debounce( _.bind( this.scan, this ), this.sensitivity );
			this.on( 'change:url', this.debouncedScan, this );
			this.on( 'scan', this.scanImage, this );
			media.controller.State.prototype.initialize.apply( this, arguments );
		},

		scan: function() {
			var attributes = { type: 'link' };

			this.trigger( 'scan', attributes );
			this.set( attributes );
		},

		scanImage: function( attributes ) {
			var frame = this.frame,
				state = this,
				url = this.get('url'),
				image = new Image();

			image.onload = function() {
				if ( state === frame.state() && url === state.get('url') )
					state.set( 'type', 'image' );
			};

			image.src = url;
		},

		reset: function() {
			_.each( _.difference( _.keys( this.attributes ), _.keys( this.defaults ) ), function( key ) {
				this.unset( key );
			}, this );

			this.set( 'url', '' );
			this.frame.toolbar.view().refresh();
		}
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	// wp.media.Views
	// -------------
	//
	// A subview manager.

	media.Views = function( view, views ) {
		this.view = view;
		this._views = _.isArray( views ) ? { '': views } : views || {};
	};

	media.Views.extend = Backbone.Model.extend;

	_.extend( media.Views.prototype, {
		// ### Fetch all of the subviews
		//
		// Returns an array of all subviews.
		all: function() {
			return _.flatten( this._views );
		},

		// ### Get a selector's subviews
		//
		// Fetches all subviews that match a given `selector`.
		//
		// If no `selector` is provided, it will grab all subviews attached
		// to the view's root.
		get: function( selector ) {
			selector = selector || '';
			return this._views[ selector ];
		},

		// ### Get a selector's first subview
		//
		// Fetches the first subview that matches a given `selector`.
		//
		// If no `selector` is provided, it will grab the first subview
		// attached to the view's root.
		//
		// Useful when a selector only has one subview at a time.
		first: function( selector ) {
			var views = this.get( selector );
			return views && views.length ? views[0] : null;
		},

		// ### Register subview(s)
		//
		// Registers any number of `views` to a `selector`.
		//
		// When no `selector` is provided, the root selector (the empty string)
		// is used. `views` accepts a `Backbone.View` instance or an array of
		// `Backbone.View` instances.
		//
		// ---
		//
		// Accepts an `options` object, which has a significant effect on the
		// resulting behavior.
		//
		// `options.silent` &ndash; *boolean, `false`*
		// > If `options.silent` is true, no DOM modifications will be made.
		//
		// `options.add` &ndash; *boolean, `false`*
		// > Use `Views.add()` as a shortcut for setting `options.add` to true.
		//
		// > By default, the provided `views` will replace
		// any existing views associated with the selector. If `options.add`
		// is true, the provided `views` will be added to the existing views.
		//
		// `options.at` &ndash; *integer, `undefined`*
		// > When adding, to insert `views` at a specific index, use
		// `options.at`. By default, `views` are added to the end of the array.
		set: function( selector, views, options ) {
			var existing, next;

			if ( ! _.isString( selector ) ) {
				options  = views;
				views    = selector;
				selector = '';
			}

			options  = options || {};
			views    = _.isArray( views ) ? views : [ views ];
			existing = this.get( selector );
			next     = views;

			if ( existing ) {
				if ( options.add ) {
					if ( _.isUndefined( options.at ) )
						next = existing.concat( views );
					else
						next = existing.splice.apply( existing, [ options.at, 0 ].concat( views ) );
				} else {
					_.each( next, function( view ) {
						view.__detach = true;
					});

					_.each( existing, function( view ) {
						if ( view.__detach )
							view.$el.detach();
						else
							view.dispose();
					});

					_.each( next, function( view ) {
						delete view.__detach;
					});
				}
			}

			this._views[ selector ] = next;

			_.each( views, function( subview ) {
				var constructor = subview.Views || media.Views,
					subviews = subview.views = subview.views || new constructor( subview );
				subviews.parent   = this.view;
				subviews.selector = selector;
			}, this );

			if ( ! options.silent )
				this._attach( selector, views, _.extend({ ready: this._isReady() }, options ) );

			return this;
		},

		// ### Add subview(s) to existing subviews
		//
		// An alias to `Views.set()`, which defaults `options.add` to true.
		//
		// Adds any number of `views` to a `selector`.
		//
		// When no `selector` is provided, the root selector (the empty string)
		// is used. `views` accepts a `Backbone.View` instance or an array of
		// `Backbone.View` instances.
		//
		// Use `Views.set()` when setting `options.add` to `false`.
		//
		// Accepts an `options` object. By default, provided `views` will be
		// inserted at the end of the array of existing views. To insert
		// `views` at a specific index, use `options.at`. If `options.silent`
		// is true, no DOM modifications will be made.
		//
		// For more information on the `options` object, see `Views.set()`.
		add: function( selector, views, options ) {
			return this.set( selector, views, _.extend({ add: true }, options ) );
		},

		// ### Stop tracking subviews
		//
		// Stops tracking `views` registered to a `selector`. If no `views` are
		// set, then all of the `selector`'s subviews will be unregistered and
		// disposed.
		//
		// Accepts an `options` object. If `options.silent` is set, `dispose`
		// will *not* be triggered on the unregistered views.
		unset: function( selector, views, options ) {
			var existing;

			if ( ! _.isString( selector ) ) {
				options = views;
				views = selector;
				selector = '';
			}

			if ( existing = this.get( selector ) ) {
				views = _.isArray( views ) ? views : [ views ];
				this._views[ selector ] = views.length ? _.difference( existing, views ) : [];
			}

			if ( ! options || ! options.silent )
				_.invoke( views, 'dispose', { silent: true });

			return this;
		},

		// ### Detach all subviews
		//
		// Detaches all subviews from the DOM.
		//
		// Helps to preserve all subview events when re-rendering the master
		// view. Used in conjunction with `Views.render()`.
		detach: function() {
			$( _.pluck( this.all(), 'el' ) ).detach();
			return this;
		},

		// ### Render all subviews
		//
		// Renders all subviews. Used in conjunction with `Views.detach()`.
		render: function() {
			var options = {
					ready: this._isReady()
				};

			_.each( this._views, function( views, selector ) {
				this._attach( selector, views, options );
			}, this );

			this.rendered = true;
			return this;
		},

		// ### Dispose all subviews
		//
		// Triggers the `dispose()` method on all subviews. Detaches the master
		// view from its parent. Resets the internals of the views manager.
		//
		// Accepts an `options` object. If `options.silent` is set, `unset`
		// will *not* be triggered on the master view's parent.
		dispose: function( options ) {
			if ( ! options || ! options.silent ) {
				if ( this.parent && this.parent.views )
					this.parent.views.unset( this.selector, this.view, { silent: true });
				delete this.parent;
				delete this.selector;
			}

			_.invoke( this.all(), 'dispose' );
			this._views = [];
			return this;
		},

		// ### Replace a selector's subviews
		//
		// By default, sets the `$target` selector's html to the subview `els`.
		//
		// Can be overridden in subclasses.
		replace: function( $target, els ) {
			$target.html( els );
			return this;
		},

		// ### Insert subviews into a selector
		//
		// By default, appends the subview `els` to the end of the `$target`
		// selector. If `options.at` is set, inserts the subview `els` at the
		// provided index.
		//
		// Can be overridden in subclasses.
		insert: function( $target, els, options ) {
			var at = options && options.at,
				$children;

			if ( _.isNumber( at ) && ($children = $target.children()).length > at )
				$children.eq( at ).before( els );
			else
				$target.append( els );

			return this;
		},

		// ### Trigger the ready event
		//
		// **Only use this method if you know what you're doing.**
		// For performance reasons, this method does not check if the view is
		// actually attached to the DOM. It's taking your word for it.
		//
		// Fires the ready event on the current view and all attached subviews.
		ready: function() {
			this.view.trigger('ready');

			// Find all attached subviews, and call ready on them.
			_.chain( this.all() ).map( function( view ) {
				return view.views;
			}).flatten().where({ attached: true }).invoke('ready');
		},

		// #### Internal. Attaches a series of views to a selector.
		//
		// Checks to see if a matching selector exists, renders the views,
		// performs the proper DOM operation, and then checks if the view is
		// attached to the document.
		_attach: function( selector, views, options ) {
			var $selector = selector ? this.view.$( selector ) : this.view.$el,
				managers;

			// Check if we found a location to attach the views.
			if ( ! $selector.length )
				return this;

			managers = _.chain( views ).pluck('views').flatten().value();

			// Render the views if necessary.
			_.each( managers, function( manager ) {
				if ( manager.rendered )
					return;

				manager.view.render();
				manager.rendered = true;
			}, this );

			// Insert or replace the views.
			this[ options.add ? 'insert' : 'replace' ]( $selector, _.pluck( views, 'el' ), options );

			// Set attached and trigger ready if the current view is already
			// attached to the DOM.
			_.each( managers, function( manager ) {
				manager.attached = true;

				if ( options.ready )
					manager.ready();
			}, this );

			return this;
		},

		// #### Internal. Checks if the current view is in the DOM.
		_isReady: function() {
			var node = this.view.el;
			while ( node ) {
				if ( node === document.body )
					return true;
				node = node.parentNode;
			}

			return false;
		}
	});

	// wp.media.View
	// -------------
	//
	// The base view class.
	media.View = Backbone.View.extend({
		// The constructor for the `Views` manager.
		Views: media.Views,

		constructor: function() {
			this.views = new this.Views( this, this.views );
			this.on( 'ready', this.ready, this );
			Backbone.View.apply( this, arguments );
		},

		dispose: function() {
			// Undelegating events, removing events from the model, and
			// removing events from the controller mirror the code for
			// `Backbone.View.dispose` in Backbone master.
			this.undelegateEvents();

			if ( this.model && this.model.off )
				this.model.off( null, null, this );

			if ( this.collection && this.collection.off )
				this.collection.off( null, null, this );

			// Recursively dispose child views.
			if ( this.views )
				this.views.dispose();

			return this;
		},

		remove: function() {
			this.dispose();
			return Backbone.View.prototype.remove.apply( this, arguments );
		},

		render: function() {
			var options;

			this.views.detach();

			if ( this.template ) {
				options = this.prepare ? this.prepare() : {};
				this.trigger( 'prepare', options );
				this.$el.html( this.template( options ) );
			}

			this.views.render();
			return this;
		},

		prepare: function() {
			return this.options;
		},

		ready: function() {}
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
					controller: this,
					id:         region,
					selector:   '.media-frame-' + region
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
			}, this );
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
		regions:   ['menu','content','sidebar','toolbar'],

		initialize: function() {
			media.view.Frame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				title:    '',
				modal:    true,
				uploader: true
			});

			// Initialize modal container view.
			if ( this.options.modal ) {
				this.modal = new media.view.Modal({
					controller: this,
					$content:   this.$el,
					title:      this.options.title
				});
			}

			// Initialize window-wide uploader.
			if ( this.options.uploader ) {
				this.uploader = new media.view.UploaderWindow({
					uploader: {
						dropzone:  this.modal ? this.modal.$el : this.$el,
						container: this.$el
					}
				});
			}

			this.on( 'attach', _.bind( this.views.ready, this.views ), this );
		},

		render: function() {
			if ( this.modal )
				this.modal.render();

			media.view.Frame.prototype.render.apply( this, arguments );

			// Render the window uploader if it exists.
			if ( this.uploader )
				this.uploader.render().$el.appendTo( this.$el );

			return this;
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
				var frame = this.get( 'iframe:' + id ).set( _.defaults({
					tab:     id,
					src:     tabUrl + '&tab=' + id,
					title:   title,
					content: 'iframe',
					menu:    'main'
				}, options ) );
			}, this );

			this.content.on( 'activate:iframe', this.iframeContent, this );
			this.menu.on( 'activate:main', this.iframeMenu, this );
			this.on( 'open', this.hijackThickbox, this );
			this.on( 'close', this.restoreThickbox, this );
		},

		iframeContent: function() {
			this.$el.addClass('hide-toolbar hide-sidebar');
			this.content.view( new media.view.Iframe({
				controller: this
			}).render() );
		},

		iframeMenu: function() {
			var views = {};

			_.each( media.view.settings.tabs, function( title, id ) {
				views[ 'iframe:' + id ] = {
					text: this.get( 'iframe:' + id ).get('title'),
					priority: 200
				};
			}, this );

			this.menu.view().add( views );
		},

		hijackThickbox: function() {
			var frame = this;

			if ( ! window.tb_remove || this._tb_remove )
				return;

			this._tb_remove = window.tb_remove;
			window.tb_remove = function() {
				frame.close();
				frame.reset();
				frame.state( frame.options.state );
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
	_.each(['open','close','attach','detach'], function( method ) {
		media.view.MediaFrame.prototype[ method ] = function( view ) {
			if ( this.modal )
				this.modal[ method ].apply( this.modal, arguments );
			this.trigger( method );
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
				state:     'upload',
				selection: [],
				library:   {},
				multiple:  false
			});

			this.createSelection();
			this.createStates();
			this.bindHandlers();
		},

		createSelection: function() {
			var controller = this,
				selection = this.options.selection;

			if ( ! (selection instanceof media.model.Selection) ) {
				this.options.selection = new media.model.Selection( selection, {
					multiple: this.options.multiple
				});
			}
		},

		createStates: function() {
			var options = this.options,
				attributes;

			attributes = {
				multiple: this.options.multiple,
				menu:     'main',
				toolbar:  'select'
			};

			// Add the default states.
			this.states.add([
				// Main states.
				new media.controller.Library( _.defaults({
					selection: options.selection,
					library:   media.query( options.library )
				}, attributes ) ),

				new media.controller.Upload( attributes )
			]);
		},

		bindHandlers: function() {
			this.menu.on( 'activate:main', this.mainMenu, this );
			this.content.on( 'activate:browse', this.browseContent, this );
			this.content.on( 'activate:upload', this.uploadContent, this );
			this.sidebar.on( 'activate:clear', this.clearSidebar, this );
			this.sidebar.on( 'activate:settings', this.settingsSidebar, this );
			this.toolbar.on( 'activate:select', this.selectToolbar, this );

			this.on( 'refresh:selection', this.refreshSelectToolbar, this );
		},

		mainMenu: function( options ) {
			this.menu.view( new media.view.Menu({
				controller: this,
				silent:     options && options.silent,

				views: {
					upload: {
						text: l10n.uploadFilesTitle,
						priority: 20
					},
					library: {
						text: l10n.mediaLibraryTitle,
						priority: 40
					}
				}
			}) );
		},

		// Content
		browseContent: function() {
			var state = this.state();

			// Browse our library of attachments.
			this.content.view( new media.view.AttachmentsBrowser({
				controller: this,
				collection: state.get('library'),
				selection:  state.get('selection'),
				model:      state,
				sortable:   state.get('sortable'),
				search:     state.get('searchable'),
				upload:     state.get('upload'),

				AttachmentView: state.get('AttachmentView')
			}) );
		},

		uploadContent: function() {
			// In the meantime, render an inline uploader.
			this.content.view( new media.view.UploaderInline({
				controller: this
			}) );
		},

		// Sidebars
		clearSidebar: function() {
			this.sidebar.view( new media.view.Sidebar({
				controller: this
			}) );
		},

		settingsSidebar: function( options ) {
			var single = this.state().get('selection').single(),
				views = {};

			views.details = new media.view.Attachment.Details({
				controller: this,
				model:      single,
				priority:   80
			}).render();


			if ( single.get('compat') ) {
				views.compat = new media.view.AttachmentCompat({
					controller: this,
					model:      single,
					priority:   120
				}).render();
			}

			this.sidebar.view( new media.view.Sidebar({
				controller: this,
				silent:     options && options.silent,
				views:      views
			}) );
		},

		// Toolbars
		selectToolbar: function( options ) {
			options = _.defaults( options || {}, {
				event:  'select',
				silent: false,
				state:  false
			});

			this.toolbar.view( new media.view.Toolbar({
				controller: this,
				silent:     options.silent,

				items: {
					select: {
						style:    'primary',
						text:     l10n.select,
						priority: 80,

						click: function() {
							var controller = this.controller;

							controller.close();
							controller.state().trigger( options.event );
							controller.reset();
							if ( options.state )
								controller.state( options.state );
						}
					}
				}
			}) );
		},

		refreshSelectToolbar: function() {
			var selection = this.state().get('selection');

			if ( ! selection || 'select' !== this.toolbar.mode() )
				return;

			this.toolbar.view().get('select').model.set( 'disabled', ! selection.length );
		}
	});

	/**
	 * wp.media.view.MediaFrame.Post
	 */
	media.view.MediaFrame.Post = media.view.MediaFrame.Select.extend({
		initialize: function() {
			_.defaults( this.options, {
				state:     'upload',
				multiple:  true,
				editing:   false
			});

			media.view.MediaFrame.Select.prototype.initialize.apply( this, arguments );
			this.createIframeStates();
		},

		createStates: function() {
			var options = this.options,
				main, gallery;

			main = {
				multiple: this.options.multiple,
				menu:      'main',
				sidebar:   'attachment-settings',

				// Update user settings when users adjust the
				// attachment display settings.
				displayUserSettings: true
			};

			gallery = {
				multiple:     true,
				menu:         'gallery',
				toolbar:      'gallery-add',
				excludeState: 'gallery-edit'
			};

			// Add the default states.
			this.states.add([
				// Main states.
				new media.controller.Library( _.defaults({
					selection: options.selection,
					library:   media.query( options.library ),
					editable:  true
				}, main ) ),

				new media.controller.Upload( main ),

				// Embed states.
				new media.controller.Embed(),

				// Gallery states.
				new media.controller.Gallery({
					library: options.selection,
					editing: options.editing,
					menu:    'gallery'
				}),

				new media.controller.Library( _.defaults({
					id:      'gallery-library',
					library: media.query({ type: 'image' })
				}, gallery ) ),

				new media.controller.Upload( _.defaults({
					id: 'gallery-upload'
				}, gallery ) )
			]);
		},

		bindHandlers: function() {
			media.view.MediaFrame.Select.prototype.bindHandlers.apply( this, arguments );

			var handlers = {
					menu: {
						'gallery': 'galleryMenu'
					},

					content: {
						'embed':          'embedContent',
						'edit-selection': 'editSelectionContent'
					},

					sidebar: {
						'attachment-settings': 'attachmentSettingsSidebar'
					},

					toolbar: {
						'main-attachments': 'mainAttachmentsToolbar',
						'main-embed':       'mainEmbedToolbar',
						'gallery-edit':     'galleryEditToolbar',
						'gallery-add':      'galleryAddToolbar'
					}
				};

			_.each( handlers, function( regionHandlers, region ) {
				_.each( regionHandlers, function( callback, handler ) {
					this[ region ].on( 'activate:' + handler, this[ callback ], this );
				}, this );
			}, this );

			this.sidebar.on( 'gallery-settings', this.onSidebarGallerySettings, this );
		},

		// Menus
		mainMenu: function() {
			media.view.MediaFrame.Select.prototype.mainMenu.call( this, { silent: true });

			this.menu.view().add({
				separateLibrary: new media.View({
					className: 'separator',
					priority: 60
				}),
				embed: {
					text: l10n.embedFromUrlTitle,
					priority: 80
				}
			});
		},

		galleryMenu: function() {
			var previous = this.previous(),
				frame = this;

			this.menu.view( new media.view.Menu({
				controller: this,
				views: {
					cancel: {
						text:     l10n.cancelGalleryTitle,
						priority: 20,
						click:    function() {
							if ( previous )
								frame.state( previous );
							else
								frame.close();
						}
					},
					separateCancel: new media.View({
						className: 'separator',
						priority: 40
					}),
					'gallery-edit': {
						text: l10n.editGalleryTitle,
						priority: 60
					},
					'gallery-upload': {
						text: l10n.uploadImagesTitle,
						priority: 80
					},
					'gallery-library': {
						text: l10n.mediaLibraryTitle,
						priority: 100
					}
				}
			}) );
		},

		// Content
		embedContent: function() {
			var view = new media.view.Embed({
				controller: this,
				model:      this.state()
			}).render();

			this.$el.addClass('hide-sidebar');
			this.content.view( view );
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
			this.content.view( view );
		},

		// Sidebars
		onSidebarGallerySettings: function( options ) {
			var library = this.state().get('library');

			if ( ! library )
				return;

			library.gallery = library.gallery || new Backbone.Model();

			this.sidebar.view().add({
				gallery: new media.view.Settings.Gallery({
					controller: this,
					model:      library.gallery,
					priority:   40
				}).render()
			}, options );
		},

		attachmentSettingsSidebar: function( options ) {
			var state = this.state(),
				display = state.get('details'),
				single = state.get('selection').single();

			this.settingsSidebar({ silent: true });

			display[ single.cid ] = display[ single.cid ] || new Backbone.Model({
				align: getUserSetting( 'align', 'none' ),
				size:  getUserSetting( 'imgsize', 'medium' ),
				link:  getUserSetting( 'urlbutton', 'post' )
			});

			this.sidebar.view().add({
				display: new media.view.Settings.AttachmentDisplay({
					controller:   this,
					model:        display[ single.cid ],
					sizes:        single.get('sizes'),
					priority:     160,
					type:         single.get('type'),
					userSettings: state.get('displayUserSettings')
				}).render()
			}, options );
		},

		// Toolbars
		mainAttachmentsToolbar: function() {
			this.toolbar.view( new media.view.Toolbar.Insert({
				controller: this,
				editable:   this.state().get('editable')
			}) );
		},

		mainEmbedToolbar: function() {
			this.toolbar.view( new media.view.Toolbar.Embed({
				controller: this
			}) );

			this.$el.removeClass('hide-toolbar');
		},

		galleryEditToolbar: function() {
			var editing = this.state().get('editing');
			this.toolbar.view( new media.view.Toolbar({
				controller: this,
				items: {
					insert: {
						style:    'primary',
						text:     editing ? l10n.updateGallery : l10n.insertGallery,
						priority: 80,

						click: function() {
							var controller = this.controller,
								state = controller.state();

							controller.close();
							state.trigger( 'update', state.get('library') );

							controller.reset();
							// @todo: Make the state activated dynamic (instead of hardcoded).
							controller.state('upload');
						}
					}
				}
			}) );
		},

		galleryAddToolbar: function() {
			this.toolbar.view( new media.view.Toolbar({
				controller: this,
				items: {
					insert: {
						style:    'primary',
						text:     l10n.addToGallery,
						priority: 80,

						click: function() {
							var controller = this.controller,
								state = controller.state(),
								edit = controller.get('gallery-edit');

							edit.get('library').add( state.get('selection').models );
							state.trigger('reset');
							controller.state('gallery-edit');
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

		events: {
			'click .media-modal-backdrop, .media-modal-close' : 'closeHandler'
		},

		initialize: function() {
			this.controller = this.options.controller;

			_.defaults( this.options, {
				container: document.body,
				title:     ''
			});
		},

		render: function() {
			// Ensure content div exists.
			this.options.$content = this.options.$content || $('<div />');

			// Detach the content element from the DOM to prevent
			// `this.$el.html()` from garbage collecting its events.
			this.options.$content.detach();

			this.$el.html( this.template({
				title: this.options.title
			}) );

			this.options.$content.addClass('media-modal-content');
			this.$('.media-modal').append( this.options.$content );
			return this;
		},

		attach: function() {
			this.$el.appendTo( this.options.container );
			this.trigger('attach');
			return this;
		},

		detach: function() {
			this.$el.detach();
			this.trigger('detach');
			return this;
		},

		open: function() {
			this.$el.show();
			this.trigger('open');
			return this;
		},

		close: function() {
			this.$el.hide();
			this.trigger('close');
			return this;
		},

		closeHandler: function( event ) {
			event.preventDefault();
			this.close();
		},

		content: function( $content ) {
			// Detach any existing content to prevent events from being lost.
			if ( this.options.$content )
				this.options.$content.detach();

			// Set and render the content.
			this.options.$content = ( $content instanceof Backbone.View ) ? $content.$el : $content;
			return this.render();
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

			this.controller = this.options.controller;

			this.$browser = $('<a href="#" class="browser" />').hide().appendTo('body');

			uploader = this.options.uploader = _.defaults( this.options.uploader || {}, {
				dropzone:  this.$el,
				browser:   this.$browser,
				params:    {}
			});

			if ( uploader.dropzone ) {
				// Ensure the dropzone is a jQuery collection.
				if ( ! (uploader.dropzone instanceof $) )
					uploader.dropzone = $( uploader.dropzone );

				// Attempt to initialize the uploader whenever the dropzone is hovered.
				uploader.dropzone.one( 'mouseenter dragenter', _.bind( this.maybeInitUploader, this ) );
			}
		},

		render: function() {
			this.maybeInitUploader();
			this.$el.html( this.template( this.options ) );
			return this;
		},

		refresh: function() {
			if ( this.uploader )
				this.uploader.refresh();
		},

		maybeInitUploader: function() {
			var $id, dropzone;

			// If the uploader already exists or the body isn't in the DOM, bail.
			if ( this.uploader || ! this.$el.closest('body').length )
				return;

			$id = $('#post_ID');
			if ( $id.length )
				this.options.uploader.params.post_id = $id.val();

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
			this.controller = this.options.controller;

			if ( ! this.options.$browser )
				this.options.$browser = this.controller.uploader.$browser;

			// Track uploading attachments.
			wp.Uploader.queue.on( 'add remove reset change:percent', this.renderUploadProgress, this );
		},

		destroy: function() {
			wp.Uploader.queue.off( 'add remove reset change:percent', this.renderUploadProgress, this );
			this.remove();
		},

		render: function() {
			var $browser = this.options.$browser,
				$placeholder;

			this.renderUploadProgress();
			this.$el.html( this.template( this.options ) );

			$placeholder = this.$('.browser');
			$browser.detach().text( $placeholder.text() );
			$browser[0].className = $placeholder[0].className;
			$placeholder.replaceWith( $browser.show() );

			this.$bar = this.$('.media-progress-bar div');
			return this;
		},

		renderUploadProgress: function() {
			var queue = wp.Uploader.queue;

			this.$el.toggleClass( 'uploading', !! queue.length );

			if ( ! this.$bar || ! queue.length )
				return;

			this.$bar.width( ( queue.reduce( function( memo, attachment ) {
				if ( attachment.get('uploading') )
					return memo + ( attachment.get('percent') || 0 );
				else
					return memo + 100;
			}, 0 ) / queue.length ) + '%' );
		}
	});

	/**
	 * wp.media.view.Toolbar
	 */
	media.view.Toolbar = media.View.extend({
		tagName:   'div',
		className: 'media-toolbar',

		initialize: function() {
			this.controller = this.options.controller;

			this._views     = {};
			this.$primary   = $('<div class="media-toolbar-primary" />').prependTo( this.$el );
			this.$secondary = $('<div class="media-toolbar-secondary" />').prependTo( this.$el );

			if ( this.options.items )
				this.set( this.options.items, { silent: true });

			if ( ! this.options.silent )
				this.render();
		},

		destroy: function() {
			this.remove();

			if ( this.model )
				this.model.off( null, null, this );

			if ( this.collection )
				this.collection.off( null, null, this );

			this.controller.off( null, null, this );

			_.each( this._views, function( view ) {
				if ( view.destroy )
					view.destroy();
			});
		},

		render: function() {
			var views = _.chain( this._views ).sortBy( function( view ) {
				return view.options.priority || 10;
			}).groupBy( function( view ) {
				return ( view.options.priority || 10 ) > 0 ? 'primary' : 'secondary';
			}).value();

			// Make sure to detach the elements we want to reuse.
			// Otherwise, `jQuery.html()` will unbind their events.
			$( _.pluck( this._views, 'el' ) ).detach();
			this.$primary.html( _.pluck( views.primary || [], 'el' ) );
			this.$secondary.html( _.pluck( views.secondary || [], 'el' ) );

			this.refresh();

			return this;
		},

		set: function( id, view, options ) {
			options = options || {};

			// Accept an object with an `id` : `view` mapping.
			if ( _.isObject( id ) ) {
				_.each( id, function( view, id ) {
					this.set( id, view, { silent: true });
				}, this );

			} else {
				if ( ! ( view instanceof Backbone.View ) ) {
					view.classes = [ id ].concat( view.classes || [] );
					view = new media.view.Button( view ).render();
				}

				view.controller = view.controller || this.controller;

				this._views[ id ] = view;
			}

			if ( ! options.silent )
				this.render();
			return this;
		},

		get: function( id ) {
			return this._views[ id ];
		},

		unset: function( id, options ) {
			delete this._views[ id ];
			if ( ! options || ! options.silent )
				this.render();
			return this;
		},

		refresh: function() {}
	});

	// wp.media.view.Toolbar.Select
	// ----------------------------
	media.view.Toolbar.Select = media.view.Toolbar.extend({
		initialize: function() {
			var options = this.options,
				controller = options.controller,
				selection = controller.state().get('selection');

			_.bindAll( this, 'clickSelect' );

			_.defaults( options, {
				event: 'select',
				state: false,
				reset: true,
				close: true,
				text:  l10n.select
			});

			options.items = _.defaults( options.items || {}, {
				select: {
					style:    'primary',
					text:     options.text,
					priority: 80,
					click:    this.clickSelect
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

			if ( options.reset )
				controller.reset();

			if ( options.state )
				controller.state( options.state );
		}
	});

	// wp.media.view.Toolbar.Embed
	// ---------------------------
	media.view.Toolbar.Embed = media.view.Toolbar.Select.extend({
		initialize: function() {
			var controller = this.options.controller;

			_.defaults( this.options, {
				text: l10n.insertEmbed
			});

			media.view.Toolbar.Select.prototype.initialize.apply( this, arguments );
			controller.on( 'change:url', this.refresh, this );
		},

		refresh: function() {
			var url = this.controller.state().get('url');
			this.get('select').model.set( 'disabled', ! url || /^https?:\/\/$/.test(url) );
		}
	});

	// wp.media.view.Toolbar.Insert
	// ----------------------------
	media.view.Toolbar.Insert = media.view.Toolbar.extend({
		initialize: function() {
			var controller = this.options.controller,
				selection = controller.state().get('selection'),
				selectionToLibrary;

			selectionToLibrary = function( state, filter ) {
				return function() {
					var controller = this.controller,
						selection = controller.state().get('selection'),
						edit = controller.get( state ),
						models = filter ? filter( selection ) : selection.models;

					edit.set( 'library', new media.model.Selection( models, {
						props:    selection.props.toJSON(),
						multiple: true
					}) );

					this.controller.state( state );
				};
			};

			this.options.items = _.defaults( this.options.items || {}, {
				selection: new media.view.Selection({
					controller: controller,
					collection: selection,
					priority:   -40,

					// If the selection is editable, pass the callback to
					// switch the content mode.
					editable: this.options.editable && function() {
						this.controller.content.mode('edit-selection');
					}
				}).render(),

				insert: {
					style:    'primary',
					priority: 80,
					text:     l10n.insertIntoPost,

					click: function() {
						controller.close();
						controller.state().trigger( 'insert', selection ).reset();
					}
				},

				gallery: {
					text:     l10n.createNewGallery,
					priority: 40,
					click:    selectionToLibrary('gallery-edit', function( selection ) {
						return selection.where({ type: 'image' });
					})
				}
			});

			media.view.Toolbar.prototype.initialize.apply( this, arguments );
		},

		refresh: function() {
			var selection = this.controller.state().get('selection'),
				count = selection.length;

			this.get('insert').model.set( 'disabled', ! selection.length );

			// Check if any attachment in the selection is an image.
			this.get('gallery').$el.toggle( count > 1 && selection.any( function( attachment ) {
				return 'image' === attachment.get('type');
			}) );
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

			if ( this.options.dropdown )
				this.options.dropdown.addClass('dropdown');

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

			// Detach the dropdown.
			if ( this.options.dropdown )
				this.options.dropdown.detach();

			this.$el.text( this.model.get('text') );

			if ( this.options.dropdown )
				this.$el.append( this.options.dropdown );

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
			this.controller = this.options.controller;
			this._views     = {};

			this.add( _.extend( {}, this._views, this.options.views ), { silent: true });
			delete this.options.views;

			if ( ! this.options.silent )
				this.render();
		},

		destroy: function() {
			this.remove();
			_.each( this._views, function( view ) {
				if ( view.destroy )
					view.destroy();
			});
		},

		render: function() {
			var els = _( this._views ).chain().sortBy( function( view ) {
					return view.options.priority || 10;
				}).pluck('el').value();

			// Make sure to detach the elements we want to reuse.
			// Otherwise, `jQuery.html()` will unbind their events.
			$( els ).detach();

			this.$el.html( els );
			return this;
		},

		add: function( id, view, options ) {
			options = options || {};

			// Accept an object with an `id` : `view` mapping.
			if ( _.isObject( id ) ) {
				_.each( id, function( view, id ) {
					this.add( id, view, { silent: true });
				}, this );

				if ( ! options.silent )
					this.render();
				return this;
			}

			if ( ! (view instanceof Backbone.View) )
				view = this.toView( view, id, options );

			view.controller = view.controller || this.controller;

			this._views[ id ] = view;
			if ( ! options.silent )
				this.render();
			return this;
		},

		get: function( id ) {
			return this._views[ id ];
		},

		remove: function( id, options ) {
			delete this._views[ id ];
			if ( ! options || ! options.silent )
				this.render();
			return this;
		},

		toView: function( options ) {
			return new media.View( options );
		}
	});


	/**
	 * wp.media.view.Menu
	 */
	media.view.Menu = media.view.PriorityList.extend({
		tagName:   'ul',
		className: 'media-menu',

		toView: function( options, id ) {
			options = options || {};
			options.id = options.id || id;
			return new media.view.MenuItem( options ).render();
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

	media.view.MenuItem = media.View.extend({
		tagName:   'li',
		className: 'media-menu-item',

		events: {
			'click': 'click'
		},

		click: function() {
			var options = this.options;

			if ( options.click )
				options.click.call( this );
			else if ( options.id )
				this.controller.state( options.id );
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
			'mousedown .attachment-preview':  'toggleSelection',
			'change [data-setting]':          'updateSetting',
			'change [data-setting] input':    'updateSetting',
			'change [data-setting] select':   'updateSetting',
			'change [data-setting] textarea': 'updateSetting',
			'click .close':                   'removeFromLibrary',
			'click a':                        'preventDefault'
		},

		buttons: {},

		initialize: function() {
			this.controller = this.options.controller;

			this.model.on( 'change:sizes change:uploading change:caption change:title', this.render, this );
			this.model.on( 'change:percent', this.progress, this );
			this.model.on( 'add', this.select, this );
			this.model.on( 'remove', this.deselect, this );

			// Update the model's details view.
			this.model.on( 'selection:single selection:unsingle', this.details, this );
			this.details( this.model, this.controller.state().get('selection') );
		},

		destroy: function() {
			this.model.off( null, null, this );
			this.remove();
		},

		render: function() {
			var attachment = this.model.toJSON(),
				options = _.defaults( this.model.toJSON(), {
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
					alt:           ''
				});

			options.buttons  = this.buttons;
			options.describe = this.controller.state().get('describe');

			if ( 'image' === options.type )
				options.size = this.imageSize();

			this.$el.html( this.template( options ) );

			this.$el.toggleClass( 'uploading', options.uploading );
			if ( options.uploading )
				this.$bar = this.$('.media-progress-bar div');
			else
				delete this.$bar;

			// Check if the model is selected.
			if ( this.selected() )
				this.select();

			return this;
		},

		progress: function() {
			if ( this.$bar && this.$bar.length )
				this.$bar.width( this.model.get('percent') + '%' );
		},

		toggleSelection: function( event ) {
			var selection = this.options.selection,
				model = this.model;

			if ( ! selection )
				return;

			if ( selection.has( model ) ) {
				// If the model is the single model, remove it.
				// If it is not the same as the single model,
				// it now becomes the single model.
				selection[ selection.single() === model ? 'remove' : 'single' ]( model );
			} else {
				selection.add( model ).single();
			}
		},

		selected: function() {
			var selection = this.options.selection;
			if ( selection )
				return selection.has( this.model );
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
			var $setting = $( event.target ).closest('[data-setting]');

			if ( ! $setting.length )
				return;

			this.model.save( $setting.data('setting'), event.target.value );
		},

		removeFromLibrary: function( event ) {
			// Stop propagation so the model isn't selected.
			event.stopPropagation();

			this.collection.remove( this.model );
		}
	});

	/**
	 * wp.media.view.Attachment.Library
	 */
	media.view.Attachment.Library = media.view.Attachment.extend({
		className: 'attachment library'
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
		template:  media.template('attachments-css'),

		events: {
			'scroll': 'scroll'
		},

		initialize: function() {
			this.controller = this.options.controller;
			this.el.id = _.uniqueId('__attachments-view-');

			_.defaults( this.options, {
				refreshSensitivity: 200,
				refreshThreshold:   3,
				AttachmentView:     media.view.Attachment,
				sortable:           false
			});

			_.each(['add','remove'], function( method ) {
				this.collection.on( method, function( attachment, attachments, options ) {
					this[ method ]( attachment, options.index );
				}, this );
			}, this );

			this.collection.on( 'reset', this.render, this );

			// Throttle the scroll handler.
			this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();

			this.initSortable();

			_.bindAll( this, 'css' );
			this.model.on( 'change:edge change:gutter', this.css, this );
			this._resizeCss = _.debounce( _.bind( this.css, this ), this.refreshSensitivity );
			$(window).on( 'resize.attachments', this._resizeCss );
			this.css();
		},

		destroy: function() {
			this.collection.off( 'add remove reset', null, this );
			this.model.off( 'change:edge change:gutter', this.css, this );
			$(window).off( 'resize.attachments', this._resizeCss );
			this.remove();
		},

		css: function() {
			var $css = $( '#' + this.el.id + '-css' );

			if ( $css.length )
				$css.remove();

			media.view.Attachments.$head().append( this.template({
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
			var collection = this.collection,
				from;

			if ( ! this.options.sortable || ! $.fn.sortable )
				return;

			this.$el.sortable({
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
					from = ui.item.index();
				},

				// Update the model's index in the collection.
				// Do so silently, as the view is already accurate.
				update: function( event, ui ) {
					var model = collection.at( from );

					collection.remove( model, {
						silent: true
					}).add( model, {
						at:     ui.item.index(),
						silent: true
					});
				}
			});

			// If the `orderby` property is changed on the `collection`,
			// check to see if we have a `comparator`. If so, disable sorting.
			collection.props.on( 'change:orderby', function() {
				this.$el.sortable( 'option', 'disabled', !! collection.comparator );
			}, this );
		},

		render: function() {
			// If there are no elements, load some.
			if ( ! this.collection.length ) {
				this.collection.more().done( this.scroll );
				this.$el.empty();
				return this;
			}

			// Otherwise, create all of the Attachment views, and replace
			// the list in a single DOM operation.
			this.$el.html( this.collection.map( function( attachment ) {
				return new this.options.AttachmentView({
					controller: this.controller,
					model:      attachment,
					collection: this.collection,
					selection:  this.options.selection
				}).render().$el;
			}, this ) );

			// Then, trigger the scroll event to check if we're within the
			// threshold to query for additional attachments.
			this.scroll();

			return this;
		},

		add: function( attachment, index ) {
			var view, children;

			view = new this.options.AttachmentView({
				controller: this.controller,
				model:      attachment,
				collection: this.collection,
				selection:  this.options.selection
			}).render();

			children = this.$el.children();

			if ( children.length > index )
				children.eq( index ).before( view.$el );
			else
				this.$el.append( view.$el );
		},

		remove: function( attachment, index ) {
			var children = this.$el.children();
			if ( children.length )
				children.eq( index ).detach();
		},

		scroll: function( event ) {
			// @todo: is this still necessary?
			if ( ! this.$el.is(':visible') )
				return;

			if ( this.el.scrollHeight < this.el.scrollTop + ( this.el.clientHeight * this.options.refreshThreshold ) ) {
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
	 * wp.media.view.AttachmentsBrowser
	 */
	media.view.AttachmentsBrowser = media.View.extend({
		tagName:   'div',
		className: 'attachments-browser',

		initialize: function() {
			this.controller = this.options.controller;

			_.defaults( this.options, {
				search: true,
				upload: false,
				total:  true,

				AttachmentView: media.view.Attachment.Library
			});

			this.toolbar = new media.view.Toolbar({
				controller: this.controller
			});

			if ( this.options.search ) {
				this.toolbar.set( 'search', new media.view.Search({
					controller: this.controller,
					model:      this.collection.props,
					priority:   -60
				}).render() );
			}

			if ( this.options.sortable ) {
				this.toolbar.set( 'dragInfo', new media.View({
					el: $( '<div class="instructions">' + l10n.dragInfo + '</div>' )[0],
					priority: -40
				}) );
			}

			if ( this.options.upload && this.controller.uploader ) {
				this.toolbar.set( 'upload', new media.view.Button( _.extend({
					el:       this.controller.uploader.$browser.detach()[0],
					priority: -60,
					size:     'large',
					text:     l10n.selectFiles
				}, this.options.upload ) ).render() );
			}

			this.attachments = new media.view.Attachments({
				controller: this.controller,
				collection: this.collection,
				selection:  this.options.selection,
				model:      this.model,
				sortable:   this.options.sortable,

				// The single `Attachment` view to be used in the `Attachments` view.
				AttachmentView: this.options.AttachmentView
			});
		},

		dispose: function() {
			this.toolbar.destroy();
			this.attachments.destroy();
			media.View.prototype.dispose.apply( this, arguments );
		},

		render: function() {
			this.toolbar.$el.detach();
			this.attachments.$el.detach();
			this.$el.html([ this.toolbar.render().el, this.attachments.render().el ]);
			return this;
		}
	});

	/**
	 * wp.media.view.SelectionPreview
	 */
	media.view.SelectionPreview = media.View.extend({
		tagName:   'div',
		className: 'selection-preview',
		template:  media.template('media-selection-preview'),

		events: {
			'click .clear-selection': 'clear'
		},

		initialize: function() {
			_.defaults( this.options, {
				clearable: true
			});

			this.controller = this.options.controller;
			this.collection.on( 'add change:url remove', this.render, this );
			this.render();
		},

		render: function() {
			var options = _.clone( this.options ),
				last, sizes, amount;

			// If nothing is selected, display nothing.
			if ( ! this.collection.length ) {
				this.$el.empty();
				return this;
			}

			options.count = this.collection.length;
			last  = this.collection.last();
			sizes = last.get('sizes');

			if ( 'image' === last.get('type') )
				options.thumbnail = ( sizes && sizes.thumbnail ) ? sizes.thumbnail.url : last.get('url');
			else
				options.thumbnail =  last.get('icon');

			this.$el.html( this.template( options ) );
			return this;
		},

		clear: function( event ) {
			event.preventDefault();
			this.collection.clear();
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

			this.controller = this.options.controller;
			this.attachments = new media.view.Attachments({
				controller: this.controller,
				collection: this.collection,
				selection:  this.collection,
				sortable:   true,
				model:      new Backbone.Model({
					edge:   40,
					gutter: 5
				}),

				// The single `Attachment` view to be used in the `Attachments` view.
				AttachmentView: media.view.Attachment.Selection
			});

			this.collection.on( 'add remove reset', this.refresh, this );
		},

		destroy: function() {
			this.remove();
			this.collection.off( 'add remove reset', this.refresh, this );
			this.attachments.destroy();
		},

		render: function() {
			this.attachments.$el.detach();
			this.attachments.render();

			this.$el.html( this.template( this.options ) );

			this.$('.selection-view').replaceWith( this.attachments.$el );
			this.refresh();
			return this;
		},

		refresh: function() {
			// If the selection hasn't been rendered, bail.
			if ( ! this.$el.children().length )
				return;

			// If nothing is selected, display nothing.
			this.$el.toggleClass( 'empty', ! this.collection.length );
			this.$('.count').text( this.collection.length + ' ' + l10n.selected );
		},

		edit: function( event ) {
			event.preventDefault();
			if ( this.options.editable )
				this.options.editable.call( this, this.collection );
		},

		clear: function( event ) {
			event.preventDefault();
			this.collection.clear();
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

		destroy: function() {
			this.model.off( null, null, this );
		},

		render: function() {
			this.$el.html( this.template( _.defaults({
				model: this.model.toJSON()
			}, this.options ) ) );

			// Select the correct values.
			_( this.model.attributes ).chain().keys().each( this.update, this );
			return this;
		},

		update: function( key ) {
			var value = this.model.get( key ),
				$setting = this.$('[data-setting="' + key + '"]'),
				$buttons;

			// Bail if we didn't find a matching setting.
			if ( ! $setting.length )
				return;

			// Attempt to determine how the setting is rendered and update
			// the selected value.

			// Handle dropdowns.
			if ( $setting.is('select') ) {
				$setting.find('[value="' + value + '"]').attr( 'selected', true );

			// Handle button groups.
			} else if ( $setting.hasClass('button-group') ) {
				$buttons = $setting.find('button').removeClass('active');
				$buttons.filter( '[value="' + value + '"]' ).addClass('active');

			// Handle text inputs and textareas.
			} else if ( $setting.is('input[type="text"], textarea') ) {
				if ( ! $setting.is(':focus') )
					$setting.val( value );
			}
		},

		updateHandler: function( event ) {
			var $setting = $( event.target ).closest('[data-setting]'),
				value = event.target.value,
				userSetting;

			event.preventDefault();

			if ( ! $setting.length )
				return;

			this.model.set( $setting.data('setting'), value );

			// If the setting has a corresponding user setting,
			// update that as well.
			if ( userSetting = $setting.data('userSetting') )
				setUserSetting( userSetting, value );
		},

		updateChanges: function( model, options ) {
			if ( options.changes )
				_( options.changes ).chain().keys().each( this.update, this );
		}
	});

	/**
	 * wp.media.view.Settings.AttachmentDisplay
	 */
	media.view.Settings.AttachmentDisplay = media.view.Settings.extend({
		className: 'attachment-display-settings',
		template:  media.template('attachment-display-settings'),

		initialize: function() {
			_.defaults( this.options, {
				userSettings: false
			});
			media.view.Settings.prototype.initialize.apply( this, arguments );
			this.model.on( 'change:link', this.updateCustomLink, this );
		},

		render: function() {
			media.view.Settings.prototype.render.call( this );
			this.updateCustomLink();
			return this;
		},

		updateCustomLink: function() {
			var isCustom = 'custom' === this.model.get('link'),
				$input = this.$('.link-to-custom');

			if ( ! isCustom ) {
				$input.hide();
				return;
			}

			$input.show();
			if ( ! this.model.get('linkUrl') )
				$input.val('http://');

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
			'change [data-setting] textarea': 'updateSetting'
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
			this.model.on( 'change:compat', this.render, this );
		},

		destroy: function() {
			this.model.off( null, null, this );
		},

		render: function() {
			var compat = this.model.get('compat');
			if ( ! compat || ! compat.item )
				return;

			this.$el.html( compat.item );
			return this;
		},

		preventDefault: function( event ) {
			event.preventDefault();
		},

		save: function( event ) {
			var data = {};

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

		initialize: function() {
			this.controller = this.options.controller;
		},

		render: function() {
			this.$el.html( '<iframe src="' + this.controller.state().get('src') + '" />' );
			return this;
		}
	});

	/**
	 * wp.media.view.Embed
	 */
	media.view.Embed = media.View.extend({
		className: 'media-embed',

		initialize: function() {
			this.controller = this.options.controller;

			this.url = new media.view.EmbedUrl({
				controller: this.controller,
				model:      this.model
			}).render();

			this._settings = new media.View();
			this.refresh();
			this.model.on( 'change:type', this.refresh, this );
		},

		render: function() {
			this.$el.html([ this.url.el, this._settings.el ]);
			this.url.focus();
			return this;
		},

		settings: function( view ) {
			view.render();
			this._settings.$el.replaceWith( view.$el );
			if ( this._settings.destroy )
				this._settings.destroy();
			this._settings.remove();
			this._settings = view;
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
				model:      this.model,
				priority:   40
			}) );
		}
	});

	/**
	 * wp.media.view.EmbedUrl
	 */
	media.view.EmbedUrl = media.View.extend({
		tagName:   'label',
		className: 'embed-url',

		events: {
			'keyup': 'url'
		},

		initialize: function() {
			this.label = this.make( 'span', null, this.options.label || l10n.url );
			this.input = this.make( 'input', {
				type:  'text',
				value: this.model.get('url') || ''
			});

			this.$label = $( this.label );
			this.$input = $( this.input );
			this.$el.append([ this.label, this.input ]);

			this.model.on( 'change:url', this.render, this );
		},

		destroy: function() {
			this.model.off( null, null, this );
		},

		render: function() {
			var $input = this.$input;

			if ( $input.is(':focus') )
				return;

			this.input.value = this.model.get('url') || 'http://';
			return this;
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

		destroy: function() {
			this.model.off( null, null, this );
			media.view.Settings.AttachmentDisplay.prototype.destroy.apply( this, arguments );
		},

		updateImage: function() {
			this.$('img').attr( 'src', this.model.get('url') );
		}
	});
}(jQuery));