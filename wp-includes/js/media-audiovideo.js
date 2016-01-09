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

},{"./controllers/audio-details.js":2,"./controllers/video-details.js":3,"./models/post-media.js":4,"./views/audio-details.js":5,"./views/frame/audio-details.js":6,"./views/frame/media-details.js":7,"./views/frame/video-details.js":8,"./views/media-details.js":9,"./views/video-details.js":10}],2:[function(require,module,exports){
/**
 * wp.media.controller.AudioDetails
 *
 * The controller for the Audio Details state
 *
 * @class
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 */
var State = wp.media.controller.State,
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

},{}],3:[function(require,module,exports){
/**
 * wp.media.controller.VideoDetails
 *
 * The controller for the Video Details state
 *
 * @class
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 */
var State = wp.media.controller.State,
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

},{}],4:[function(require,module,exports){
/**
 * wp.media.model.PostMedia
 *
 * Shared model class for audio and video. Updates the model after
 *   "Add Audio|Video Source" and "Replace Audio|Video" states return
 *
 * @class
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

},{}],5:[function(require,module,exports){
/**
 * wp.media.view.AudioDetails
 *
 * @class
 * @augments wp.media.view.MediaDetails
 * @augments wp.media.view.Settings.AttachmentDisplay
 * @augments wp.media.view.Settings
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MediaDetails = wp.media.view.MediaDetails,
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

},{}],6:[function(require,module,exports){
/**
 * wp.media.view.MediaFrame.AudioDetails
 *
 * @class
 * @augments wp.media.view.MediaFrame.MediaDetails
 * @augments wp.media.view.MediaFrame.Select
 * @augments wp.media.view.MediaFrame
 * @augments wp.media.view.Frame
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 * @mixes wp.media.controller.StateMachine
 */
var MediaDetails = wp.media.view.MediaFrame.MediaDetails,
	MediaLibrary = wp.media.controller.MediaLibrary,

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
		options.DetailsView = wp.media.view.AudioDetails;
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
			new wp.media.controller.AudioDetails( {
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

},{}],7:[function(require,module,exports){
/**
 * wp.media.view.MediaFrame.MediaDetails
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
var Select = wp.media.view.MediaFrame.Select,
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
			separateCancel: new wp.media.View({
				className: 'separator',
				priority: 40
			})
		});

	},

	setPrimaryButton: function(text, handler) {
		this.toolbar.set( new wp.media.view.Toolbar({
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

},{}],8:[function(require,module,exports){
/**
 * wp.media.view.MediaFrame.VideoDetails
 *
 * @class
 * @augments wp.media.view.MediaFrame.MediaDetails
 * @augments wp.media.view.MediaFrame.Select
 * @augments wp.media.view.MediaFrame
 * @augments wp.media.view.Frame
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 * @mixes wp.media.controller.StateMachine
 */
var MediaDetails = wp.media.view.MediaFrame.MediaDetails,
	MediaLibrary = wp.media.controller.MediaLibrary,
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
		options.DetailsView = wp.media.view.VideoDetails;
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
			new wp.media.controller.VideoDetails({
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
					'<track srclang="en" label="English" kind="subtitles" src="',
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

},{}],9:[function(require,module,exports){
/* global MediaElementPlayer */

/**
 * wp.media.view.MediaDetails
 *
 * @class
 * @augments wp.media.view.Settings.AttachmentDisplay
 * @augments wp.media.view.Settings
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var AttachmentDisplay = wp.media.view.Settings.AttachmentDisplay,
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

	loadPlayer: function () {
		this.players.push( new MediaElementPlayer( this.media, this.settings ) );
		this.scriptXhr = false;
	},

	/**
	 * @global MediaElementPlayer
	 */
	setPlayer : function() {
		var baseSettings, src;

		if ( this.players.length || ! this.media || this.scriptXhr ) {
			return;
		}

		src = this.model.get( 'src' );

		if ( src && src.indexOf( 'vimeo' ) > -1 && ! ( 'Froogaloop' in window ) ) {
			baseSettings = wp.media.mixin.mejsSettings;
			this.scriptXhr = $.getScript( baseSettings.pluginPath + 'froogaloop.min.js', _.bind( this.loadPlayer, this ) );
		} else {
			this.loadPlayer();
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

},{}],10:[function(require,module,exports){
/**
 * wp.media.view.VideoDetails
 *
 * @class
 * @augments wp.media.view.MediaDetails
 * @augments wp.media.view.Settings.AttachmentDisplay
 * @augments wp.media.view.Settings
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MediaDetails = wp.media.view.MediaDetails,
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

			if ( ! video.hasClass( 'youtube-video' ) && ! video.hasClass( 'vimeo-video' ) ) {
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

},{}]},{},[1]);
