(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
var media = wp.media,
	baseSettings = window._wpmejsSettings || {},
	l10n = window._wpMediaViewsL10n || {};

/**
 * @mixin
 */
wp.media.mixin = {
	mejsSettings: baseSettings,

	removeAllPlayers: function() {
		var p;

		if ( window.mejs && window.mejs.players ) {
			for ( p in window.mejs.players ) {
				window.mejs.players[p].pause();
				this.removePlayer( window.mejs.players[p] );
			}
		}
	},

	/**
	 * Override the MediaElement method for removing a player.
	 *	MediaElement tries to pull the audio/video tag out of
	 *	its container and re-add it to the DOM.
	 */
	removePlayer: function(t) {
		var featureIndex, feature;

		if ( ! t.options ) {
			return;
		}

		// invoke features cleanup
		for ( featureIndex in t.options.features ) {
			feature = t.options.features[featureIndex];
			if ( t['clean' + feature] ) {
				try {
					t['clean' + feature](t);
				} catch (e) {}
			}
		}

		if ( ! t.isDynamic ) {
			t.$node.remove();
		}

		if ( 'native' !== t.media.pluginType ) {
			t.$media.remove();
		}

		delete window.mejs.players[t.id];

		t.container.remove();
		t.globalUnbind();
		delete t.node.player;
	},

	/**
	 * Allows any class that has set 'player' to a MediaElementPlayer
	 *  instance to remove the player when listening to events.
	 *
	 *  Examples: modal closes, shortcode properties are removed, etc.
	 */
	unsetPlayers : function() {
		if ( this.players && this.players.length ) {
			_.each( this.players, function (player) {
				player.pause();
				wp.media.mixin.removePlayer( player );
			} );
			this.players = [];
		}
	}
};

/**
 * Autowire "collection"-type shortcodes
 */
wp.media.playlist = new wp.media.collection({
	tag: 'playlist',
	editTitle : l10n.editPlaylistTitle,
	defaults : {
		id: wp.media.view.settings.post.id,
		style: 'light',
		tracklist: true,
		tracknumbers: true,
		images: true,
		artists: true,
		type: 'audio'
	}
});

/**
 * Shortcode modeling for audio
 *  `edit()` prepares the shortcode for the media modal
 *  `shortcode()` builds the new shortcode after update
 *
 * @namespace
 */
wp.media.audio = {
	coerce : wp.media.coerce,

	defaults : {
		id : wp.media.view.settings.post.id,
		src : '',
		loop : false,
		autoplay : false,
		preload : 'none',
		width : 400
	},

	edit : function( data ) {
		var frame, shortcode = wp.shortcode.next( 'audio', data ).shortcode;

		frame = wp.media({
			frame: 'audio',
			state: 'audio-details',
			metadata: _.defaults( shortcode.attrs.named, this.defaults )
		});

		return frame;
	},

	shortcode : function( model ) {
		var content;

		_.each( this.defaults, function( value, key ) {
			model[ key ] = this.coerce( model, key );

			if ( value === model[ key ] ) {
				delete model[ key ];
			}
		}, this );

		content = model.content;
		delete model.content;

		return new wp.shortcode({
			tag: 'audio',
			attrs: model,
			content: content
		});
	}
};

/**
 * Shortcode modeling for video
 *  `edit()` prepares the shortcode for the media modal
 *  `shortcode()` builds the new shortcode after update
 *
 * @namespace
 */
wp.media.video = {
	coerce : wp.media.coerce,

	defaults : {
		id : wp.media.view.settings.post.id,
		src : '',
		poster : '',
		loop : false,
		autoplay : false,
		preload : 'metadata',
		content : '',
		width : 640,
		height : 360
	},

	edit : function( data ) {
		var frame,
			shortcode = wp.shortcode.next( 'video', data ).shortcode,
			attrs;

		attrs = shortcode.attrs.named;
		attrs.content = shortcode.content;

		frame = wp.media({
			frame: 'video',
			state: 'video-details',
			metadata: _.defaults( attrs, this.defaults )
		});

		return frame;
	},

	shortcode : function( model ) {
		var content;

		_.each( this.defaults, function( value, key ) {
			model[ key ] = this.coerce( model, key );

			if ( value === model[ key ] ) {
				delete model[ key ];
			}
		}, this );

		content = model.content;
		delete model.content;

		return new wp.shortcode({
			tag: 'video',
			attrs: model,
			content: content
		});
	}
};

media.model.PostMedia = require( './models/post-media.js' );
media.controller.AudioDetails = require( './controllers/audio-details.js' );
media.controller.VideoDetails = require( './controllers/video-details.js' );
media.view.MediaFrame.MediaDetails = require( './views/frame/media-details.js' );
media.view.MediaFrame.AudioDetails = require( './views/frame/audio-details.js' );
media.view.MediaFrame.VideoDetails = require( './views/frame/video-details.js' );
media.view.MediaDetails = require( './views/media-details.js' );
media.view.AudioDetails = require( './views/audio-details.js' );
media.view.VideoDetails = require( './views/video-details.js' );

},{"./controllers/audio-details.js":2,"./controllers/video-details.js":8,"./models/post-media.js":9,"./views/audio-details.js":21,"./views/frame/audio-details.js":25,"./views/frame/media-details.js":26,"./views/frame/video-details.js":28,"./views/media-details.js":31,"./views/video-details.js":50}],2:[function(require,module,exports){
/**
 * The controller for the Audio Details state
 *
 * @constructor
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 */
var State = require( './state.js' ),
	l10n = wp.media.view.l10n,
	AudioDetails;

AudioDetails = State.extend({
	defaults: {
		id: 'audio-details',
		toolbar: 'audio-details',
		title: l10n.audioDetailsTitle,
		content: 'audio-details',
		menu: 'audio-details',
		router: false,
		priority: 60
	},

	initialize: function( options ) {
		this.media = options.media;
		State.prototype.initialize.apply( this, arguments );
	}
});

module.exports = AudioDetails;

},{"./state.js":7}],3:[function(require,module,exports){
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
var selectionSync = require( '../utils/selection-sync.js' ),
	State = require( './state.js' ),
	l10n = wp.media.view.l10n,
	getUserSetting = window.getUserSetting,
	setUserSetting = window.setUserSetting,
	Library;

Library = State.extend({
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
			this.set( 'library', wp.media.query() );
		}

		if ( ! ( selection instanceof wp.media.model.Selection ) ) {
			props = selection;

			if ( ! props ) {
				props = this.get('library').props.toJSON();
				props = _.omit( props, 'orderby', 'query' );
			}

			this.set( 'selection', new wp.media.model.Selection( null, {
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
		var defaultProps = wp.media.view.settings.defaultProps;
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

		return _.contains( wp.media.view.settings.embedExts, attachment.get('filename').split('.').pop() );
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
_.extend( Library.prototype, selectionSync );

module.exports = Library;

},{"../utils/selection-sync.js":10,"./state.js":7}],4:[function(require,module,exports){
/**
 * wp.media.controller.MediaLibrary
 *
 * @class
 * @augments wp.media.controller.Library
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 */
var Library = require( './library.js' ),
	MediaLibrary;

MediaLibrary = Library.extend({
	defaults: _.defaults({
		// Attachments browser defaults. @see media.view.AttachmentsBrowser
		filterable:      'uploaded',

		displaySettings: false,
		priority:        80,
		syncSelection:   false
	}, Library.prototype.defaults ),

	/**
	 * @since 3.9.0
	 *
	 * @param options
	 */
	initialize: function( options ) {
		this.media = options.media;
		this.type = options.type;
		this.set( 'library', wp.media.query({ type: this.type }) );

		Library.prototype.initialize.apply( this, arguments );
	},

	/**
	 * @since 3.9.0
	 */
	activate: function() {
		// @todo this should use this.frame.
		if ( wp.media.frame.lastMime ) {
			this.set( 'library', wp.media.query({ type: wp.media.frame.lastMime }) );
			delete wp.media.frame.lastMime;
		}
		Library.prototype.activate.apply( this, arguments );
	}
});

module.exports = MediaLibrary;

},{"./library.js":3}],5:[function(require,module,exports){
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
var Region = function( options ) {
	_.extend( this, _.pick( options || {}, 'id', 'view', 'selector' ) );
};

// Use Backbone's self-propagating `extend` inheritance method.
Region.extend = Backbone.Model.extend;

_.extend( Region.prototype, {
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

module.exports = Region;

},{}],6:[function(require,module,exports){
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
var StateMachine = function( states ) {
	// @todo This is dead code. The states collection gets created in media.view.Frame._createStates.
	this.states = new Backbone.Collection( states );
};

// Use Backbone's self-propagating `extend` inheritance method.
StateMachine.extend = Backbone.Model.extend;

_.extend( StateMachine.prototype, Backbone.Events, {
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
	StateMachine.prototype[ method ] = function() {
		// Ensure that the `states` collection exists so the `StateMachine`
		// can be used as a mixin.
		this.states = this.states || new Backbone.Collection();
		// Forward the method to the `states` collection.
		this.states[ method ].apply( this.states, arguments );
		return this;
	};
});

module.exports = StateMachine;

},{}],7:[function(require,module,exports){
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
var State = Backbone.Model.extend({
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
	State.prototype[ '_' + region ] = function() {
		var mode = this.get( region );
		if ( mode ) {
			this.frame[ region ].render( mode );
		}
	};
});

module.exports = State;

},{}],8:[function(require,module,exports){
/**
 * The controller for the Video Details state
 *
 * @constructor
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 */
var State = require( './state.js' ),
	l10n = wp.media.view.l10n,
	VideoDetails;

VideoDetails = State.extend({
	defaults: {
		id: 'video-details',
		toolbar: 'video-details',
		title: l10n.videoDetailsTitle,
		content: 'video-details',
		menu: 'video-details',
		router: false,
		priority: 60
	},

	initialize: function( options ) {
		this.media = options.media;
		State.prototype.initialize.apply( this, arguments );
	}
});

module.exports = VideoDetails;

},{"./state.js":7}],9:[function(require,module,exports){
/**
 * Shared model class for audio and video. Updates the model after
 *   "Add Audio|Video Source" and "Replace Audio|Video" states return
 *
 * @constructor
 * @augments Backbone.Model
 */
var PostMedia = Backbone.Model.extend({
	initialize: function() {
		this.attachment = false;
	},

	setSource: function( attachment ) {
		this.attachment = attachment;
		this.extension = attachment.get( 'filename' ).split('.').pop();

		if ( this.get( 'src' ) && this.extension === this.get( 'src' ).split('.').pop() ) {
			this.unset( 'src' );
		}

		if ( _.contains( wp.media.view.settings.embedExts, this.extension ) ) {
			this.set( this.extension, this.attachment.get( 'url' ) );
		} else {
			this.unset( this.extension );
		}
	},

	changeAttachment: function( attachment ) {
		this.setSource( attachment );

		this.unset( 'src' );
		_.each( _.without( wp.media.view.settings.embedExts, this.extension ), function( ext ) {
			this.unset( ext );
		}, this );
	}
});

module.exports = PostMedia;

},{}],10:[function(require,module,exports){
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
var selectionSync = {
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

module.exports = selectionSync;

},{}],11:[function(require,module,exports){
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
var View = require( './view.js' ),
	AttachmentCompat;

AttachmentCompat = View.extend({
	tagName:   'form',
	className: 'compat-item',

	events: {
		'submit':          'preventDefault',
		'change input':    'save',
		'change select':   'save',
		'change textarea': 'save'
	},

	initialize: function() {
		this.listenTo( this.model, 'change:compat', this.render );
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
		return View.prototype.dispose.apply( this, arguments );
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

module.exports = AttachmentCompat;

},{"./view.js":51}],12:[function(require,module,exports){
/**
 * wp.media.view.AttachmentFilters
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	$ = jQuery,
	AttachmentFilters;

AttachmentFilters = View.extend({
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

		this.listenTo( this.model, 'change', this.select );
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

module.exports = AttachmentFilters;

},{"./view.js":51}],13:[function(require,module,exports){
/**
 * wp.media.view.AttachmentFilters.All
 *
 * @class
 * @augments wp.media.view.AttachmentFilters
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var AttachmentFilters = require( '../attachment-filters.js' ),
	l10n = wp.media.view.l10n,
	All;

All = AttachmentFilters.extend({
	createFilters: function() {
		var filters = {};

		_.each( wp.media.view.settings.mimeTypes || {}, function( text, key ) {
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

		if ( wp.media.view.settings.post.id ) {
			filters.uploaded = {
				text:  l10n.uploadedToThisPost,
				props: {
					status:  null,
					type:    null,
					uploadedTo: wp.media.view.settings.post.id,
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

		if ( wp.media.view.settings.mediaTrash &&
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

module.exports = All;

},{"../attachment-filters.js":12}],14:[function(require,module,exports){
/**
 * A filter dropdown for month/dates.
 *
 * @class
 * @augments wp.media.view.AttachmentFilters
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var AttachmentFilters = require( '../attachment-filters.js' ),
	l10n = wp.media.view.l10n,
	DateFilter;

DateFilter = AttachmentFilters.extend({
	id: 'media-attachment-date-filters',

	createFilters: function() {
		var filters = {};
		_.each( wp.media.view.settings.months || {}, function( value, index ) {
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

module.exports = DateFilter;

},{"../attachment-filters.js":12}],15:[function(require,module,exports){
/**
 * wp.media.view.AttachmentFilters.Uploaded
 *
 * @class
 * @augments wp.media.view.AttachmentFilters
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var AttachmentFilters = require( '../attachment-filters.js' ),
	l10n = wp.media.view.l10n,
	Uploaded;

Uploaded = AttachmentFilters.extend({
	createFilters: function() {
		var type = this.model.get('type'),
			types = wp.media.view.settings.mimeTypes,
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
					uploadedTo: wp.media.view.settings.post.id,
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

module.exports = Uploaded;

},{"../attachment-filters.js":12}],16:[function(require,module,exports){
/**
 * wp.media.view.Attachment
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	$ = jQuery,
	Attachment;

Attachment = View.extend({
	tagName:   'li',
	className: 'attachment',
	template:  wp.template('attachment'),

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
			this.listenTo( this.model, 'change', this.render );
		} else {
			this.listenTo( this.model, 'change:percent', this.progress );
		}
		this.listenTo( this.model, 'change:title', this._syncTitle );
		this.listenTo( this.model, 'change:caption', this._syncCaption );
		this.listenTo( this.model, 'change:artist', this._syncArtist );
		this.listenTo( this.model, 'change:album', this._syncAlbum );

		// Update the selection.
		this.listenTo( this.model, 'add', this.select );
		this.listenTo( this.model, 'remove', this.deselect );
		if ( selection ) {
			selection.on( 'reset', this.updateSelect, this );
			// Update the model's details view.
			this.listenTo( this.model, 'selection:single selection:unsingle', this.details );
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
		View.prototype.dispose.apply( this, arguments );
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
		var sizes = this.model.get('sizes'), matched = false;

		size = size || 'medium';

		// Use the provided image size if possible.
		if ( sizes ) {
			if ( sizes[ size ] ) {
				matched = sizes[ size ];
			} else if ( sizes.large ) {
				matched = sizes.large;
			} else if ( sizes.thumbnail ) {
				matched = sizes.thumbnail;
			} else if ( sizes.full ) {
				matched = sizes.full;
			}

			if ( matched ) {
				return _.clone( matched );
			}
		}

		return {
			url:         this.model.get('url'),
			width:       this.model.get('width'),
			height:      this.model.get('height'),
			orientation: this.model.get('orientation')
		};
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
	Attachment.prototype[ method ] = function( model, value ) {
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

module.exports = Attachment;

},{"./view.js":51}],17:[function(require,module,exports){
/**
 * wp.media.view.Attachment.Details
 *
 * @class
 * @augments wp.media.view.Attachment
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Attachment = require( '../attachment.js' ),
	l10n = wp.media.view.l10n,
	Details;

Details = Attachment.extend({
	tagName:   'div',
	className: 'attachment-details',
	template:  wp.template('attachment-details'),

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
		Attachment.prototype.initialize.apply( this, arguments );
	},

	initialFocus: function() {
		if ( ! wp.media.isTouchDevice ) {
			this.$( ':input' ).eq( 0 ).focus();
		}
	},
	/**
	 * @param {Object} event
	 */
	deleteAttachment: function( event ) {
		event.preventDefault();

		if ( window.confirm( l10n.warnDelete ) ) {
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

		if ( wp.media.view.settings.mediaTrash &&
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

module.exports = Details;

},{"../attachment.js":16}],18:[function(require,module,exports){
/**
 * wp.media.view.Attachment.Library
 *
 * @class
 * @augments wp.media.view.Attachment
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Attachment = require( '../attachment.js' ),
	Library;

Library = Attachment.extend({
	buttons: {
		check: true
	}
});

module.exports = Library;

},{"../attachment.js":16}],19:[function(require,module,exports){
/**
 * wp.media.view.Attachments
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	Attachment = require( './attachment.js' ),
	$ = jQuery,
	Attachments;

Attachments = View.extend({
	tagName:   'ul',
	className: 'attachments',

	attributes: {
		tabIndex: -1
	},

	initialize: function() {
		this.el.id = _.uniqueId('__attachments-view-');

		_.defaults( this.options, {
			refreshSensitivity: wp.media.isTouchDevice ? 300 : 200,
			refreshThreshold:   3,
			AttachmentView:     Attachment,
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
		View.prototype.dispose.apply( this, arguments );
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

		if ( wp.media.isTouchDevice || ! this.options.sortable || ! $.fn.sortable ) {
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
		if ( wp.media.isTouchDevice || ! this.options.sortable || ! $.fn.sortable ) {
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
		if ( el === document ) {
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

module.exports = Attachments;

},{"./attachment.js":16,"./view.js":51}],20:[function(require,module,exports){
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
var View = require( '../view.js' ),
	Library = require( '../attachment/library.js' ),
	Toolbar = require( '../toolbar.js' ),
	Spinner = require( '../spinner.js' ),
	Search = require( '../search.js' ),
	Label = require( '../label.js' ),
	Uploaded = require( '../attachment-filters/uploaded.js' ),
	All = require( '../attachment-filters/all.js' ),
	DateFilter = require( '../attachment-filters/date.js' ),
	UploaderInline = require( '../uploader/inline.js' ),
	Attachments = require( '../attachments.js' ),
	Sidebar = require( '../sidebar.js' ),
	UploaderStatus = require( '../uploader/status.js' ),
	Details = require( '../attachment/details.js' ),
	AttachmentCompat = require( '../attachment-compat.js' ),
	AttachmentDisplay = require( '../settings/attachment-display.js' ),
	mediaTrash = wp.media.view.settings.mediaTrash,
	l10n = wp.media.view.l10n,
	$ = jQuery,
	AttachmentsBrowser;

AttachmentsBrowser = View.extend({
	tagName:   'div',
	className: 'attachments-browser',

	initialize: function() {
		_.defaults( this.options, {
			filters: false,
			search:  true,
			date:    true,
			display: false,
			sidebar: true,
			AttachmentView: Library
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
		View.prototype.dispose.apply( this, arguments );
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
		this.toolbar = new Toolbar( toolbarOptions );

		this.views.add( this.toolbar );

		this.toolbar.set( 'spinner', new Spinner({
			priority: -60
		}) );

		if ( -1 !== $.inArray( this.options.filters, [ 'uploaded', 'all' ] ) ) {
			// "Filters" will return a <select>, need to render
			// screen reader text before
			this.toolbar.set( 'filtersLabel', new Label({
				value: l10n.filterByType,
				attributes: {
					'for':  'media-attachment-filters'
				},
				priority:   -80
			}).render() );

			if ( 'uploaded' === this.options.filters ) {
				this.toolbar.set( 'filters', new Uploaded({
					controller: this.controller,
					model:      this.collection.props,
					priority:   -80
				}).render() );
			} else {
				Filters = new All({
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
			LibraryViewSwitcher = View.extend({
				className: 'view-switch media-grid-view-switch',
				template: wp.template( 'media-library-view-switcher')
			});

			this.toolbar.set( 'libraryViewSwitcher', new LibraryViewSwitcher({
				controller: this.controller,
				priority: -90
			}).render() );

			// DateFilter is a <select>, screen reader text needs to be rendered before
			this.toolbar.set( 'dateFilterLabel', new Label({
				value: l10n.filterByDate,
				attributes: {
					'for': 'media-attachment-date-filters'
				},
				priority: -75
			}).render() );
			this.toolbar.set( 'dateFilter', new DateFilter({
				controller: this.controller,
				model:      this.collection.props,
				priority: -75
			}).render() );

			// BulkSelection is a <div> with subviews, including screen reader text
			this.toolbar.set( 'selectModeToggleButton', new wp.media.view.SelectModeToggleButton({
				text: l10n.bulkSelect,
				controller: this.controller,
				priority: -70
			}).render() );

			this.toolbar.set( 'deleteSelectedButton', new wp.media.view.DeleteSelectedButton({
				filters: Filters,
				style: 'primary',
				disabled: true,
				text: mediaTrash ? l10n.trashSelected : l10n.deleteSelected,
				controller: this.controller,
				priority: -60,
				click: function() {
					var changed = [], removed = [],
						selection = this.controller.state().get( 'selection' ),
						library = this.controller.state().get( 'library' );

					if ( ! selection.length ) {
						return;
					}

					if ( ! mediaTrash && ! window.confirm( l10n.warnBulkDelete ) ) {
						return;
					}

					if ( mediaTrash &&
						'trash' !== selection.at( 0 ).get( 'status' ) &&
						! window.confirm( l10n.warnBulkTrash ) ) {

						return;
					}

					selection.each( function( model ) {
						if ( ! model.get( 'nonces' )['delete'] ) {
							removed.push( model );
							return;
						}

						if ( mediaTrash && 'trash' === model.get( 'status' ) ) {
							model.set( 'status', 'inherit' );
							changed.push( model.save() );
							removed.push( model );
						} else if ( mediaTrash ) {
							model.set( 'status', 'trash' );
							changed.push( model.save() );
							removed.push( model );
						} else {
							model.destroy({wait: true});
						}
					} );

					if ( changed.length ) {
						selection.remove( removed );

						$.when.apply( null, changed ).then( _.bind( function() {
							library._requery( true );
							this.controller.trigger( 'selection:action:done' );
						}, this ) );
					} else {
						this.controller.trigger( 'selection:action:done' );
					}
				}
			}).render() );

			if ( mediaTrash ) {
				this.toolbar.set( 'deleteSelectedPermanentlyButton', new wp.media.view.DeleteSelectedPermanentlyButton({
					filters: Filters,
					style: 'primary',
					disabled: true,
					text: l10n.deleteSelected,
					controller: this.controller,
					priority: -55,
					click: function() {
						var removed = [], selection = this.controller.state().get( 'selection' );

						if ( ! selection.length || ! window.confirm( l10n.warnBulkDelete ) ) {
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
			this.toolbar.set( 'dateFilterLabel', new Label({
				value: l10n.filterByDate,
				attributes: {
					'for': 'media-attachment-date-filters'
				},
				priority: -75
			}).render() );
			this.toolbar.set( 'dateFilter', new DateFilter({
				controller: this.controller,
				model:      this.collection.props,
				priority: -75
			}).render() );
		}

		if ( this.options.search ) {
			// Search is an input, screen reader text needs to be rendered before
			this.toolbar.set( 'searchLabel', new Label({
				value: l10n.searchMediaLabel,
				attributes: {
					'for': 'media-search-input'
				},
				priority:   60
			}).render() );
			this.toolbar.set( 'search', new Search({
				controller: this.controller,
				model:      this.collection.props,
				priority:   60
			}).render() );
		}

		if ( this.options.dragInfo ) {
			this.toolbar.set( 'dragInfo', new View({
				el: $( '<div class="instructions">' + l10n.dragInfo + '</div>' )[0],
				priority: -40
			}) );
		}

		if ( this.options.suggestedWidth && this.options.suggestedHeight ) {
			this.toolbar.set( 'suggestedDimensions', new View({
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
		this.uploader = new UploaderInline({
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
		this.attachments = new Attachments({
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
			this.attachmentsNoResults = new View({
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
			sidebar = this.sidebar = new Sidebar({
				controller: this.controller
			});

		this.views.add( sidebar );

		if ( this.controller.uploader ) {
			sidebar.set( 'uploads', new UploaderStatus({
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

		sidebar.set( 'details', new Details({
			controller: this.controller,
			model:      single,
			priority:   80
		}) );

		sidebar.set( 'compat', new AttachmentCompat({
			controller: this.controller,
			model:      single,
			priority:   120
		}) );

		if ( this.options.display ) {
			sidebar.set( 'display', new AttachmentDisplay({
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

module.exports = AttachmentsBrowser;

},{"../attachment-compat.js":11,"../attachment-filters/all.js":13,"../attachment-filters/date.js":14,"../attachment-filters/uploaded.js":15,"../attachment/details.js":17,"../attachment/library.js":18,"../attachments.js":19,"../label.js":30,"../search.js":39,"../settings/attachment-display.js":41,"../sidebar.js":42,"../spinner.js":43,"../toolbar.js":44,"../uploader/inline.js":46,"../uploader/status.js":48,"../view.js":51}],21:[function(require,module,exports){
/**
 * wp.media.view.AudioDetails
 *
 * @constructor
 * @augments wp.media.view.MediaDetails
 * @augments wp.media.view.Settings.AttachmentDisplay
 * @augments wp.media.view.Settings
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MediaDetails = require( './media-details' ),
	AudioDetails;

AudioDetails = MediaDetails.extend({
	className: 'audio-details',
	template:  wp.template('audio-details'),

	setMedia: function() {
		var audio = this.$('.wp-audio-shortcode');

		if ( audio.find( 'source' ).length ) {
			if ( audio.is(':hidden') ) {
				audio.show();
			}
			this.media = MediaDetails.prepareSrc( audio.get(0) );
		} else {
			audio.hide();
			this.media = false;
		}

		return this;
	}
});

module.exports = AudioDetails;

},{"./media-details":31}],22:[function(require,module,exports){
/**
 * wp.media.view.Button
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	Button;

Button = View.extend({
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

		this.listenTo( this.model, 'change', this.render );
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

module.exports = Button;

},{"./view.js":51}],23:[function(require,module,exports){
/**
 * wp.media.view.FocusManager
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	FocusManager;

FocusManager = View.extend({

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

		// Skip the file input added by Plupload.
		tabbables = this.$( ':tabbable' ).not( '.moxie-shim input[type="file"]' );

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

module.exports = FocusManager;

},{"./view.js":51}],24:[function(require,module,exports){
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
var StateMachine = require( '../controllers/state-machine.js' ),
	State = require( '../controllers/state.js' ),
	Region = require( '../controllers/region.js' ),
	View = require( './view.js' ),
	Frame;

Frame = View.extend({
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
			this[ region ] = new Region({
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
			model: State
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
_.extend( Frame.prototype, StateMachine.prototype );

module.exports = Frame;

},{"../controllers/region.js":5,"../controllers/state-machine.js":6,"../controllers/state.js":7,"./view.js":51}],25:[function(require,module,exports){
/**
 * wp.media.view.MediaFrame.AudioDetails
 *
 * @constructor
 * @augments wp.media.view.MediaFrame.MediaDetails
 * @augments wp.media.view.MediaFrame.Select
 * @augments wp.media.view.MediaFrame
 * @augments wp.media.view.Frame
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 * @mixes wp.media.controller.StateMachine
 */
var MediaDetails = require( './media-details' ),
	MediaLibrary = require( '../../controllers/media-library.js' ),
	AudioDetailsView = require( '../audio-details.js' ),
	AudioDetailsController = require( '../../controllers/audio-details.js' ),
	l10n = wp.media.view.l10n,
	AudioDetails;

AudioDetails = MediaDetails.extend({
	defaults: {
		id:      'audio',
		url:     '',
		menu:    'audio-details',
		content: 'audio-details',
		toolbar: 'audio-details',
		type:    'link',
		title:    l10n.audioDetailsTitle,
		priority: 120
	},

	initialize: function( options ) {
		options.DetailsView = AudioDetailsView;
		options.cancelText = l10n.audioDetailsCancel;
		options.addText = l10n.audioAddSourceTitle;

		MediaDetails.prototype.initialize.call( this, options );
	},

	bindHandlers: function() {
		MediaDetails.prototype.bindHandlers.apply( this, arguments );

		this.on( 'toolbar:render:replace-audio', this.renderReplaceToolbar, this );
		this.on( 'toolbar:render:add-audio-source', this.renderAddSourceToolbar, this );
	},

	createStates: function() {
		this.states.add([
			new AudioDetailsController( {
				media: this.media
			} ),

			new MediaLibrary( {
				type: 'audio',
				id: 'replace-audio',
				title: l10n.audioReplaceTitle,
				toolbar: 'replace-audio',
				media: this.media,
				menu: 'audio-details'
			} ),

			new MediaLibrary( {
				type: 'audio',
				id: 'add-audio-source',
				title: l10n.audioAddSourceTitle,
				toolbar: 'add-audio-source',
				media: this.media,
				menu: false
			} )
		]);
	}
});

module.exports = AudioDetails;

},{"../../controllers/audio-details.js":2,"../../controllers/media-library.js":4,"../audio-details.js":21,"./media-details":26}],26:[function(require,module,exports){
/**
 * wp.media.view.MediaFrame.MediaDetails
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
var View = require( '../view.js' ),
	Toolbar = require( '../toolbar.js' ),
	Select = require( './select.js' ),
	l10n = wp.media.view.l10n,
	MediaDetails;

MediaDetails = Select.extend({
	defaults: {
		id:      'media',
		url:     '',
		menu:    'media-details',
		content: 'media-details',
		toolbar: 'media-details',
		type:    'link',
		priority: 120
	},

	initialize: function( options ) {
		this.DetailsView = options.DetailsView;
		this.cancelText = options.cancelText;
		this.addText = options.addText;

		this.media = new wp.media.model.PostMedia( options.metadata );
		this.options.selection = new wp.media.model.Selection( this.media.attachment, { multiple: false } );
		Select.prototype.initialize.apply( this, arguments );
	},

	bindHandlers: function() {
		var menu = this.defaults.menu;

		Select.prototype.bindHandlers.apply( this, arguments );

		this.on( 'menu:create:' + menu, this.createMenu, this );
		this.on( 'content:render:' + menu, this.renderDetailsContent, this );
		this.on( 'menu:render:' + menu, this.renderMenu, this );
		this.on( 'toolbar:render:' + menu, this.renderDetailsToolbar, this );
	},

	renderDetailsContent: function() {
		var view = new this.DetailsView({
			controller: this,
			model: this.state().media,
			attachment: this.state().media.attachment
		}).render();

		this.content.set( view );
	},

	renderMenu: function( view ) {
		var lastState = this.lastState(),
			previous = lastState && lastState.id,
			frame = this;

		view.set({
			cancel: {
				text:     this.cancelText,
				priority: 20,
				click:    function() {
					if ( previous ) {
						frame.setState( previous );
					} else {
						frame.close();
					}
				}
			},
			separateCancel: new View({
				className: 'separator',
				priority: 40
			})
		});

	},

	setPrimaryButton: function(text, handler) {
		this.toolbar.set( new Toolbar({
			controller: this,
			items: {
				button: {
					style:    'primary',
					text:     text,
					priority: 80,
					click:    function() {
						var controller = this.controller;
						handler.call( this, controller, controller.state() );
						// Restore and reset the default state.
						controller.setState( controller.options.state );
						controller.reset();
					}
				}
			}
		}) );
	},

	renderDetailsToolbar: function() {
		this.setPrimaryButton( l10n.update, function( controller, state ) {
			controller.close();
			state.trigger( 'update', controller.media.toJSON() );
		} );
	},

	renderReplaceToolbar: function() {
		this.setPrimaryButton( l10n.replace, function( controller, state ) {
			var attachment = state.get( 'selection' ).single();
			controller.media.changeAttachment( attachment );
			state.trigger( 'replace', controller.media.toJSON() );
		} );
	},

	renderAddSourceToolbar: function() {
		this.setPrimaryButton( this.addText, function( controller, state ) {
			var attachment = state.get( 'selection' ).single();
			controller.media.setSource( attachment );
			state.trigger( 'add-source', controller.media.toJSON() );
		} );
	}
});

module.exports = MediaDetails;

},{"../toolbar.js":44,"../view.js":51,"./select.js":27}],27:[function(require,module,exports){
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

var MediaFrame = require( '../media-frame.js' ),
	Library = require( '../../controllers/library.js' ),
	AttachmentsBrowser = require( '../attachments/browser.js' ),
	UploaderInline = require( '../uploader/inline.js' ),
	ToolbarSelect = require( '../toolbar/select.js' ),
	l10n = wp.media.view.l10n,
	Select;

Select = MediaFrame.extend({
	initialize: function() {
		// Call 'initialize' directly on the parent class.
		MediaFrame.prototype.initialize.apply( this, arguments );

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

		if ( ! (selection instanceof wp.media.model.Selection) ) {
			this.options.selection = new wp.media.model.Selection( selection, {
				multiple: this.options.multiple
			});
		}

		this._selection = {
			attachments: new wp.media.model.Attachments(),
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
			new Library({
				library:   wp.media.query( options.library ),
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
		contentRegion.view = new AttachmentsBrowser({
			controller: this,
			collection: state.get('library'),
			selection:  state.get('selection'),
			model:      state,
			sortable:   state.get('sortable'),
			search:     state.get('searchable'),
			filters:    state.get('filterable'),
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
		this.content.set( new UploaderInline({
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

		toolbar.view = new ToolbarSelect( options );
	}
});

module.exports = Select;

},{"../../controllers/library.js":3,"../attachments/browser.js":20,"../media-frame.js":32,"../toolbar/select.js":45,"../uploader/inline.js":46}],28:[function(require,module,exports){
/**
 * wp.media.view.MediaFrame.VideoDetails
 *
 * @constructor
 * @augments wp.media.view.MediaFrame.MediaDetails
 * @augments wp.media.view.MediaFrame.Select
 * @augments wp.media.view.MediaFrame
 * @augments wp.media.view.Frame
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 * @mixes wp.media.controller.StateMachine
 */
var MediaDetails = require( './media-details' ),
	MediaLibrary = require( '../../controllers/media-library.js' ),
	VideoDetailsView = require( '../video-details.js' ),
	VideoDetailsController = require( '../../controllers/video-details.js' ),
	l10n = wp.media.view.l10n,
	VideoDetails;

VideoDetails = MediaDetails.extend({
	defaults: {
		id:      'video',
		url:     '',
		menu:    'video-details',
		content: 'video-details',
		toolbar: 'video-details',
		type:    'link',
		title:    l10n.videoDetailsTitle,
		priority: 120
	},

	initialize: function( options ) {
		options.DetailsView = VideoDetailsView;
		options.cancelText = l10n.videoDetailsCancel;
		options.addText = l10n.videoAddSourceTitle;

		MediaDetails.prototype.initialize.call( this, options );
	},

	bindHandlers: function() {
		MediaDetails.prototype.bindHandlers.apply( this, arguments );

		this.on( 'toolbar:render:replace-video', this.renderReplaceToolbar, this );
		this.on( 'toolbar:render:add-video-source', this.renderAddSourceToolbar, this );
		this.on( 'toolbar:render:select-poster-image', this.renderSelectPosterImageToolbar, this );
		this.on( 'toolbar:render:add-track', this.renderAddTrackToolbar, this );
	},

	createStates: function() {
		this.states.add([
			new VideoDetailsController({
				media: this.media
			}),

			new MediaLibrary( {
				type: 'video',
				id: 'replace-video',
				title: l10n.videoReplaceTitle,
				toolbar: 'replace-video',
				media: this.media,
				menu: 'video-details'
			} ),

			new MediaLibrary( {
				type: 'video',
				id: 'add-video-source',
				title: l10n.videoAddSourceTitle,
				toolbar: 'add-video-source',
				media: this.media,
				menu: false
			} ),

			new MediaLibrary( {
				type: 'image',
				id: 'select-poster-image',
				title: l10n.videoSelectPosterImageTitle,
				toolbar: 'select-poster-image',
				media: this.media,
				menu: 'video-details'
			} ),

			new MediaLibrary( {
				type: 'text',
				id: 'add-track',
				title: l10n.videoAddTrackTitle,
				toolbar: 'add-track',
				media: this.media,
				menu: 'video-details'
			} )
		]);
	},

	renderSelectPosterImageToolbar: function() {
		this.setPrimaryButton( l10n.videoSelectPosterImageTitle, function( controller, state ) {
			var urls = [], attachment = state.get( 'selection' ).single();

			controller.media.set( 'poster', attachment.get( 'url' ) );
			state.trigger( 'set-poster-image', controller.media.toJSON() );

			_.each( wp.media.view.settings.embedExts, function (ext) {
				if ( controller.media.get( ext ) ) {
					urls.push( controller.media.get( ext ) );
				}
			} );

			wp.ajax.send( 'set-attachment-thumbnail', {
				data : {
					urls: urls,
					thumbnail_id: attachment.get( 'id' )
				}
			} );
		} );
	},

	renderAddTrackToolbar: function() {
		this.setPrimaryButton( l10n.videoAddTrackTitle, function( controller, state ) {
			var attachment = state.get( 'selection' ).single(),
				content = controller.media.get( 'content' );

			if ( -1 === content.indexOf( attachment.get( 'url' ) ) ) {
				content += [
					'<track srclang="en" label="English"kind="subtitles" src="',
					attachment.get( 'url' ),
					'" />'
				].join('');

				controller.media.set( 'content', content );
			}
			state.trigger( 'add-track', controller.media.toJSON() );
		} );
	}
});

module.exports = VideoDetails;

},{"../../controllers/media-library.js":4,"../../controllers/video-details.js":8,"../video-details.js":50,"./media-details":26}],29:[function(require,module,exports){
/**
 * wp.media.view.Iframe
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	Iframe;

Iframe = View.extend({
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

module.exports = Iframe;

},{"./view.js":51}],30:[function(require,module,exports){
/**
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	Label;

Label = View.extend({
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

module.exports = Label;

},{"./view.js":51}],31:[function(require,module,exports){
/**
 * wp.media.view.MediaDetails
 *
 * @constructor
 * @augments wp.media.view.Settings.AttachmentDisplay
 * @augments wp.media.view.Settings
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var AttachmentDisplay = require( './settings/attachment-display.js' ),
	$ = jQuery,
	MediaDetails;

MediaDetails = AttachmentDisplay.extend({
	initialize: function() {
		_.bindAll(this, 'success');
		this.players = [];
		this.listenTo( this.controller, 'close', wp.media.mixin.unsetPlayers );
		this.on( 'ready', this.setPlayer );
		this.on( 'media:setting:remove', wp.media.mixin.unsetPlayers, this );
		this.on( 'media:setting:remove', this.render );
		this.on( 'media:setting:remove', this.setPlayer );
		this.events = _.extend( this.events, {
			'click .remove-setting' : 'removeSetting',
			'change .content-track' : 'setTracks',
			'click .remove-track' : 'setTracks',
			'click .add-media-source' : 'addSource'
		} );

		AttachmentDisplay.prototype.initialize.apply( this, arguments );
	},

	prepare: function() {
		return _.defaults({
			model: this.model.toJSON()
		}, this.options );
	},

	/**
	 * Remove a setting's UI when the model unsets it
	 *
	 * @fires wp.media.view.MediaDetails#media:setting:remove
	 *
	 * @param {Event} e
	 */
	removeSetting : function(e) {
		var wrap = $( e.currentTarget ).parent(), setting;
		setting = wrap.find( 'input' ).data( 'setting' );

		if ( setting ) {
			this.model.unset( setting );
			this.trigger( 'media:setting:remove', this );
		}

		wrap.remove();
	},

	/**
	 *
	 * @fires wp.media.view.MediaDetails#media:setting:remove
	 */
	setTracks : function() {
		var tracks = '';

		_.each( this.$('.content-track'), function(track) {
			tracks += $( track ).val();
		} );

		this.model.set( 'content', tracks );
		this.trigger( 'media:setting:remove', this );
	},

	addSource : function( e ) {
		this.controller.lastMime = $( e.currentTarget ).data( 'mime' );
		this.controller.setState( 'add-' + this.controller.defaults.id + '-source' );
	},

	/**
	 * @global MediaElementPlayer
	 */
	setPlayer : function() {
		if ( ! this.players.length && this.media ) {
			this.players.push( new window.MediaElementPlayer( this.media, this.settings ) );
		}
	},

	/**
	 * @abstract
	 */
	setMedia : function() {
		return this;
	},

	success : function(mejs) {
		var autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;

		if ( 'flash' === mejs.pluginType && autoplay ) {
			mejs.addEventListener( 'canplay', function() {
				mejs.play();
			}, false );
		}

		this.mejs = mejs;
	},

	/**
	 * @returns {media.view.MediaDetails} Returns itself to allow chaining
	 */
	render: function() {
		AttachmentDisplay.prototype.render.apply( this, arguments );

		setTimeout( _.bind( function() {
			this.resetFocus();
		}, this ), 10 );

		this.settings = _.defaults( {
			success : this.success
		}, wp.media.mixin.mejsSettings );

		return this.setMedia();
	},

	resetFocus: function() {
		this.$( '.embed-media-settings' ).scrollTop( 0 );
	}
}, {
	instances : 0,
	/**
	 * When multiple players in the DOM contain the same src, things get weird.
	 *
	 * @param {HTMLElement} elem
	 * @returns {HTMLElement}
	 */
	prepareSrc : function( elem ) {
		var i = MediaDetails.instances++;
		_.each( $( elem ).find( 'source' ), function( source ) {
			source.src = [
				source.src,
				source.src.indexOf('?') > -1 ? '&' : '?',
				'_=',
				i
			].join('');
		} );

		return elem;
	}
});

module.exports = MediaDetails;

},{"./settings/attachment-display.js":41}],32:[function(require,module,exports){
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
var View = require( './view.js' ),
	Frame = require( './frame.js' ),
	Modal = require( './modal.js' ),
	UploaderWindow = require( './uploader/window.js' ),
	Menu = require( './menu.js' ),
	Toolbar = require( './toolbar.js' ),
	Router = require( './router.js' ),
	Iframe = require( './iframe.js' ),
	$ = jQuery,
	MediaFrame;

MediaFrame = Frame.extend({
	className: 'media-frame',
	template:  wp.template('media-frame'),
	regions:   ['menu','title','content','toolbar','router'],

	events: {
		'click div.media-frame-title h1': 'toggleMenu'
	},

	/**
	 * @global wp.Uploader
	 */
	initialize: function() {
		Frame.prototype.initialize.apply( this, arguments );

		_.defaults( this.options, {
			title:    '',
			modal:    true,
			uploader: true
		});

		// Ensure core UI is enabled.
		this.$el.addClass('wp-core-ui');

		// Initialize modal container view.
		if ( this.options.modal ) {
			this.modal = new Modal({
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
			this.uploader = new UploaderWindow({
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
		return Frame.prototype.render.apply( this, arguments );
	},
	/**
	 * @param {Object} title
	 * @this wp.media.controller.Region
	 */
	createTitle: function( title ) {
		title.view = new View({
			controller: this,
			tagName: 'h1'
		});
	},
	/**
	 * @param {Object} menu
	 * @this wp.media.controller.Region
	 */
	createMenu: function( menu ) {
		menu.view = new Menu({
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
		toolbar.view = new Toolbar({
			controller: this
		});
	},
	/**
	 * @param {Object} router
	 * @this wp.media.controller.Region
	 */
	createRouter: function( router ) {
		router.view = new Router({
			controller: this
		});
	},
	/**
	 * @param {Object} options
	 */
	createIframeStates: function( options ) {
		var settings = wp.media.view.settings,
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
		content.view = new Iframe({
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

		_.each( wp.media.view.settings.tabs, function( title, id ) {
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
	MediaFrame.prototype[ method ] = function() {
		if ( this.modal ) {
			this.modal[ method ].apply( this.modal, arguments );
		}
		return this;
	};
});

module.exports = MediaFrame;

},{"./frame.js":24,"./iframe.js":29,"./menu.js":34,"./modal.js":35,"./router.js":38,"./toolbar.js":44,"./uploader/window.js":49,"./view.js":51}],33:[function(require,module,exports){
/**
 * wp.media.view.MenuItem
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	$ = jQuery,
	MenuItem;

MenuItem = View.extend({
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
		if ( ! wp.media.isTouchDevice ) {
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

module.exports = MenuItem;

},{"./view.js":51}],34:[function(require,module,exports){
/**
 * wp.media.view.Menu
 *
 * @class
 * @augments wp.media.view.PriorityList
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MenuItem = require( './menu-item.js' ),
	PriorityList = require( './priority-list.js' ),
	Menu;

Menu = PriorityList.extend({
	tagName:   'div',
	className: 'media-menu',
	property:  'state',
	ItemView:  MenuItem,
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
		PriorityList.prototype.ready.apply( this, arguments );
		this.visibility();
	},

	set: function() {
		/**
		 * call 'set' directly on the parent class
		 */
		PriorityList.prototype.set.apply( this, arguments );
		this.visibility();
	},

	unset: function() {
		/**
		 * call 'unset' directly on the parent class
		 */
		PriorityList.prototype.unset.apply( this, arguments );
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

module.exports = Menu;

},{"./menu-item.js":33,"./priority-list.js":36}],35:[function(require,module,exports){
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
var View = require( './view.js' ),
	FocusManager = require( './focus-manager.js' ),
	$ = jQuery,
	Modal;

Modal = View.extend({
	tagName:  'div',
	template: wp.template('media-modal'),

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

		this.focusManager = new FocusManager({
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

module.exports = Modal;

},{"./focus-manager.js":23,"./view.js":51}],36:[function(require,module,exports){
/**
 * wp.media.view.PriorityList
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	PriorityList;

PriorityList = View.extend({
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
		return new View( options );
	}
});

module.exports = PriorityList;

},{"./view.js":51}],37:[function(require,module,exports){
/**
 * wp.media.view.RouterItem
 *
 * @class
 * @augments wp.media.view.MenuItem
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MenuItem = require( './menu-item.js' ),
	RouterItem;

RouterItem = MenuItem.extend({
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

module.exports = RouterItem;

},{"./menu-item.js":33}],38:[function(require,module,exports){
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
var Menu = require( './menu.js' ),
	RouterItem = require( './router-item.js' ),
	Router;

Router = Menu.extend({
	tagName:   'div',
	className: 'media-router',
	property:  'contentMode',
	ItemView:  RouterItem,
	region:    'router',

	initialize: function() {
		this.controller.on( 'content:render', this.update, this );
		// Call 'initialize' directly on the parent class.
		Menu.prototype.initialize.apply( this, arguments );
	},

	update: function() {
		var mode = this.controller.content.mode();
		if ( mode ) {
			this.select( mode );
		}
	}
});

module.exports = Router;

},{"./menu.js":34,"./router-item.js":37}],39:[function(require,module,exports){
/**
 * wp.media.view.Search
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	l10n = wp.media.view.l10n,
	Search;

Search = View.extend({
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

module.exports = Search;

},{"./view.js":51}],40:[function(require,module,exports){
/**
 * wp.media.view.Settings
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	$ = jQuery,
	Settings;

Settings = View.extend({
	events: {
		'click button':    'updateHandler',
		'change input':    'updateHandler',
		'change select':   'updateHandler',
		'change textarea': 'updateHandler'
	},

	initialize: function() {
		this.model = this.model || new Backbone.Model();
		this.listenTo( this.model, 'change', this.updateChanges );
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
		View.prototype.render.apply( this, arguments );
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
			window.setUserSetting( userSetting, value );
		}
	},

	updateChanges: function( model ) {
		if ( model.hasChanged() ) {
			_( model.changed ).chain().keys().each( this.update, this );
		}
	}
});

module.exports = Settings;

},{"./view.js":51}],41:[function(require,module,exports){
/**
 * wp.media.view.Settings.AttachmentDisplay
 *
 * @class
 * @augments wp.media.view.Settings
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Settings = require( '../settings.js' ),
	AttachmentDisplay;

AttachmentDisplay = Settings.extend({
	className: 'attachment-display-settings',
	template:  wp.template('attachment-display-settings'),

	initialize: function() {
		var attachment = this.options.attachment;

		_.defaults( this.options, {
			userSettings: false
		});
		// Call 'initialize' directly on the parent class.
		Settings.prototype.initialize.apply( this, arguments );
		this.listenTo( this.model, 'change:link', this.updateLinkTo );

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
		Settings.prototype.dispose.apply( this, arguments );
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
		Settings.prototype.render.call( this );
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
		if ( ! wp.media.isTouchDevice && $input.is(':visible') ) {
			$input.focus()[0].select();
		}
	}
});

module.exports = AttachmentDisplay;

},{"../settings.js":40}],42:[function(require,module,exports){
/**
 * wp.media.view.Sidebar
 *
 * @class
 * @augments wp.media.view.PriorityList
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var PriorityList = require( './priority-list.js' ),
	Sidebar;

Sidebar = PriorityList.extend({
	className: 'media-sidebar'
});

module.exports = Sidebar;

},{"./priority-list.js":36}],43:[function(require,module,exports){
/**
 * wp.media.view.Spinner
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	Spinner;

Spinner = View.extend({
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

module.exports = Spinner;

},{"./view.js":51}],44:[function(require,module,exports){
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
var View = require( './view.js' ),
	Button = require( './button.js' ),
	PriorityList = require( './priority-list.js' ),
	Toolbar;

Toolbar = View.extend({
	tagName:   'div',
	className: 'media-toolbar',

	initialize: function() {
		var state = this.controller.state(),
			selection = this.selection = state.get('selection'),
			library = this.library = state.get('library');

		this._views = {};

		// The toolbar is composed of two `PriorityList` views.
		this.primary   = new PriorityList();
		this.secondary = new PriorityList();
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
		return View.prototype.dispose.apply( this, arguments );
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
				view = new Button( view ).render();
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

module.exports = Toolbar;

},{"./button.js":22,"./priority-list.js":36,"./view.js":51}],45:[function(require,module,exports){
/**
 * wp.media.view.Toolbar.Select
 *
 * @class
 * @augments wp.media.view.Toolbar
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Toolbar = require( '../toolbar.js' ),
	l10n = wp.media.view.l10n,
	Select;

Select = Toolbar.extend({
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
		Toolbar.prototype.initialize.apply( this, arguments );
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

module.exports = Select;

},{"../toolbar.js":44}],46:[function(require,module,exports){
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
var View = require( '../view.js' ),
	UploaderStatus = require( './status.js' ),
	UploaderInline;

UploaderInline = View.extend({
	tagName:   'div',
	className: 'uploader-inline',
	template:  wp.template('uploader-inline'),

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
			this.options.postId = wp.media.view.settings.post.id;
		}

		if ( this.options.status ) {
			this.views.set( '.upload-inline-status', new UploaderStatus({
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
			return View.prototype.dispose.apply( this, arguments );
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
		var result = View.prototype.remove.apply( this, arguments );

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

module.exports = UploaderInline;

},{"../view.js":51,"./status.js":48}],47:[function(require,module,exports){
/**
 * wp.media.view.UploaderStatusError
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( '../view.js' ),
	UploaderStatusError;

UploaderStatusError = View.extend({
	className: 'upload-error',
	template:  wp.template('uploader-status-error')
});

module.exports = UploaderStatusError;

},{"../view.js":51}],48:[function(require,module,exports){
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
var View = require( '../view.js' ),
	UploaderStatusError = require( './status-error.js' ),
	UploaderStatus;

UploaderStatus = View.extend({
	className: 'media-uploader-status',
	template:  wp.template('uploader-status'),

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
		View.prototype.dispose.apply( this, arguments );
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
		return wp.media.truncate( _.escape( filename ), 24 );
	},
	/**
	 * @param {Backbone.Model} error
	 */
	error: function( error ) {
		this.views.add( '.upload-errors', new UploaderStatusError({
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

module.exports = UploaderStatus;

},{"../view.js":51,"./status-error.js":47}],49:[function(require,module,exports){
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
var View = require( '../view.js' ),
	$ = jQuery,
	UploaderWindow;

UploaderWindow = View.extend({
	tagName:   'div',
	className: 'uploader-window',
	template:  wp.template('uploader-window'),

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
		var postId = wp.media.view.settings.post.id,
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

		wp.media.transition( $el ).done( function() {
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

module.exports = UploaderWindow;

},{"../view.js":51}],50:[function(require,module,exports){
/**
 * wp.media.view.VideoDetails
 *
 * @constructor
 * @augments wp.media.view.MediaDetails
 * @augments wp.media.view.Settings.AttachmentDisplay
 * @augments wp.media.view.Settings
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MediaDetails = require( './media-details' ),
	VideoDetails;

VideoDetails = MediaDetails.extend({
	className: 'video-details',
	template:  wp.template('video-details'),

	setMedia: function() {
		var video = this.$('.wp-video-shortcode');

		if ( video.find( 'source' ).length ) {
			if ( video.is(':hidden') ) {
				video.show();
			}

			if ( ! video.hasClass('youtube-video') ) {
				this.media = MediaDetails.prepareSrc( video.get(0) );
			} else {
				this.media = video.get(0);
			}
		} else {
			video.hide();
			this.media = false;
		}

		return this;
	}
});

module.exports = VideoDetails;

},{"./media-details":31}],51:[function(require,module,exports){
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
var View = wp.Backbone.View.extend({
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

module.exports = View;

},{}]},{},[1]);
