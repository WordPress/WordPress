/* global _wpMediaViewsL10n, _wpmejsSettings, MediaElementPlayer */

(function($, _, Backbone) {
	var media = wp.media,
		baseSettings = {},
		l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;

	if ( ! _.isUndefined( window._wpmejsSettings ) ) {
		baseSettings.pluginPath = _wpmejsSettings.pluginPath;
	}

	/**
	 * @mixin
	 */
	wp.media.mixin = {
		mejsSettings: baseSettings,
		/**
		 * Pauses every instance of MediaElementPlayer
		 */
		pauseAllPlayers: function() {
			var p;
			if ( window.mejs && window.mejs.players ) {
				for ( p in window.mejs.players ) {
					window.mejs.players[p].pause();
				}
			}
		},

		/**
		 * Utility to identify the user's browser
		 */
		ua: {
			is : function( browser ) {
				var passes = false, ua = window.navigator.userAgent;

				switch ( browser ) {
					case 'oldie':
						passes = ua.match(/MSIE [6-8]/gi) !== null;
					break;
					case 'ie':
						passes = ua.match(/MSIE/gi) !== null;
					break;
					case 'ff':
						passes = ua.match(/firefox/gi) !== null;
					break;
					case 'opera':
						passes = ua.match(/OPR/) !== null;
					break;
					case 'safari':
						passes = ua.match(/safari/gi) !== null && ua.match(/chrome/gi) === null;
					break;
					case 'chrome':
						passes = ua.match(/safari/gi) !== null && ua.match(/chrome/gi) !== null;
					break;
				}

				return passes;
			}
		},

		/**
		 * Specify compatibility for native playback by browser
		 */
		compat :{
			'opera' : {
				audio: ['ogg', 'wav'],
				video: ['ogg', 'webm']
			},
			'chrome' : {
				audio: ['ogg', 'mpeg'],
				video: ['ogg', 'webm', 'mp4', 'm4v', 'mpeg']
			},
			'ff' : {
				audio: ['ogg', 'mpeg'],
				video: ['ogg', 'webm']
			},
			'safari' : {
				audio: ['mpeg', 'wav'],
				video: ['mp4', 'm4v', 'mpeg', 'x-ms-wmv', 'quicktime']
			},
			'ie' : {
				audio: ['mpeg'],
				video: ['mp4', 'm4v', 'mpeg']
			}
		},

		/**
		 * Determine if the passed media contains a <source> that provides
		 *  native playback in the user's browser
		 *
		 * @param {jQuery} media
		 * @returns {Boolean}
		 */
		isCompatible: function( media ) {
			if ( ! media.find( 'source' ).length ) {
				return false;
			}

			var ua = this.ua, test = false, found = false, sources;

			if ( ua.is( 'oldIE' ) ) {
				return false;
			}

			sources = media.find( 'source' );

			_.find( this.compat, function( supports, browser ) {
				if ( ua.is( browser ) ) {
					found = true;
					_.each( sources, function( elem ) {
						var audio = new RegExp( 'audio\/(' + supports.audio.join('|') + ')', 'gi' ),
							video = new RegExp( 'video\/(' + supports.video.join('|') + ')', 'gi' );

						if ( elem.type.match( video ) !== null || elem.type.match( audio ) !== null ) {
							test = true;
						}
					} );
				}

				return test || found;
			} );

			return test;
		},

		/**
		 * Override the MediaElement method for removing a player.
		 *	MediaElement tries to pull the audio/video tag out of
		 *	its container and re-add it to the DOM.
		 */
		removePlayer: function(t) {
			var featureIndex, feature;

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
				t.media.remove();
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
				wp.media.mixin.pauseAllPlayers();
				_.each( this.players, function (player) {
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
			var self = this, content;

			_.each( this.defaults, function( value, key ) {
				model[ key ] = self.coerce( model, key );

				if ( value === model[ key ] ) {
					delete model[ key ];
				}
			});

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
			var self = this, content;

			_.each( this.defaults, function( value, key ) {
				model[ key ] = self.coerce( model, key );

				if ( value === model[ key ] ) {
					delete model[ key ];
				}
			});

			content = model.content;
			delete model.content;

			return new wp.shortcode({
				tag: 'video',
				attrs: model,
				content: content
			});
		}
	};

	/**
	 * Shared model class for audio and video. Updates the model after
	 *   "Add Audio|Video Source" and "Replace Audio|Video" states return
	 *
	 * @constructor
	 * @augments Backbone.Model
	 */
	media.model.PostMedia = Backbone.Model.extend({
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
			var self = this;

			this.setSource( attachment );

			this.unset( 'src' );
			_.each( _.without( wp.media.view.settings.embedExts, this.extension ), function( ext ) {
				self.unset( ext );
			} );
		}
	});

	/**
	 * The controller for the Audio Details state
	 *
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.AudioDetails = media.controller.State.extend({
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
			media.controller.State.prototype.initialize.apply( this, arguments );
		}
	});

	/**
	 * The controller for the Video Details state
	 *
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.VideoDetails = media.controller.State.extend({
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
			media.controller.State.prototype.initialize.apply( this, arguments );
		}
	});

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
	media.view.MediaFrame.MediaDetails = media.view.MediaFrame.Select.extend({
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

			this.media = new media.model.PostMedia( options.metadata );
			this.options.selection = new media.model.Selection( this.media.attachment, { multiple: false } );
			media.view.MediaFrame.Select.prototype.initialize.apply( this, arguments );
		},

		bindHandlers: function() {
			var menu = this.defaults.menu;

			media.view.MediaFrame.Select.prototype.bindHandlers.apply( this, arguments );

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
				separateCancel: new media.View({
					className: 'separator',
					priority: 40
				})
			});

		},

		setPrimaryButton: function(text, handler) {
			this.toolbar.set( new media.view.Toolbar({
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
	media.view.MediaFrame.AudioDetails = media.view.MediaFrame.MediaDetails.extend({
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
			options.DetailsView = media.view.AudioDetails;
			options.cancelText = l10n.audioDetailsCancel;
			options.addText = l10n.audioAddSourceTitle;

			media.view.MediaFrame.MediaDetails.prototype.initialize.call( this, options );
		},

		bindHandlers: function() {
			media.view.MediaFrame.MediaDetails.prototype.bindHandlers.apply( this, arguments );

			this.on( 'toolbar:render:replace-audio', this.renderReplaceToolbar, this );
			this.on( 'toolbar:render:add-audio-source', this.renderAddSourceToolbar, this );
		},

		createStates: function() {
			this.states.add([
				new media.controller.AudioDetails( {
					media: this.media
				} ),

				new media.controller.MediaLibrary( {
					type: 'audio',
					id: 'replace-audio',
					title: l10n.audioReplaceTitle,
					toolbar: 'replace-audio',
					media: this.media,
					menu: 'audio-details'
				} ),

				new media.controller.MediaLibrary( {
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
	media.view.MediaFrame.VideoDetails = media.view.MediaFrame.MediaDetails.extend({
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
			options.DetailsView = media.view.VideoDetails;
			options.cancelText = l10n.videoDetailsCancel;
			options.addText = l10n.videoAddSourceTitle;

			media.view.MediaFrame.MediaDetails.prototype.initialize.call( this, options );
		},

		bindHandlers: function() {
			media.view.MediaFrame.MediaDetails.prototype.bindHandlers.apply( this, arguments );

			this.on( 'toolbar:render:replace-video', this.renderReplaceToolbar, this );
			this.on( 'toolbar:render:add-video-source', this.renderAddSourceToolbar, this );
			this.on( 'toolbar:render:select-poster-image', this.renderSelectPosterImageToolbar, this );
			this.on( 'toolbar:render:add-track', this.renderAddTrackToolbar, this );
		},

		createStates: function() {
			this.states.add([
				new media.controller.VideoDetails({
					media: this.media
				}),

				new media.controller.MediaLibrary( {
					type: 'video',
					id: 'replace-video',
					title: l10n.videoReplaceTitle,
					toolbar: 'replace-video',
					media: this.media,
					menu: 'video-details'
				} ),

				new media.controller.MediaLibrary( {
					type: 'video',
					id: 'add-video-source',
					title: l10n.videoAddSourceTitle,
					toolbar: 'add-video-source',
					media: this.media,
					menu: false
				} ),

				new media.controller.MediaLibrary( {
					type: 'image',
					id: 'select-poster-image',
					title: l10n.videoSelectPosterImageTitle,
					toolbar: 'select-poster-image',
					media: this.media,
					menu: 'video-details'
				} ),

				new media.controller.MediaLibrary( {
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
				var attachment = state.get( 'selection' ).single();

				controller.media.set( 'poster', attachment.get( 'url' ) );
				state.trigger( 'set-poster-image', controller.media.toJSON() );
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

	/**
	 * wp.media.view.MediaDetails
	 *
	 * @contructor
	 * @augments wp.media.view.Settings.AttachmentDisplay
	 * @augments wp.media.view.Settings
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.MediaDetails = media.view.Settings.AttachmentDisplay.extend({
		initialize: function() {
			_.bindAll(this, 'success');
			this.players = [];
			this.listenTo( this.controller, 'close', media.mixin.unsetPlayers );
			this.on( 'ready', this.setPlayer );
			this.on( 'media:setting:remove', media.mixin.unsetPlayers, this );
			this.on( 'media:setting:remove', this.render );
			this.on( 'media:setting:remove', this.setPlayer );
			this.events = _.extend( this.events, {
				'click .remove-setting' : 'removeSetting',
				'change .content-track' : 'setTracks',
				'click .remove-track' : 'setTracks'
			} );

			media.view.Settings.AttachmentDisplay.prototype.initialize.apply( this, arguments );
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

		/**
		 * @global MediaElementPlayer
		 */
		setPlayer : function() {
			if ( ! this.players.length && this.media ) {
				this.players.push( new MediaElementPlayer( this.media, this.settings ) );
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
			var self = this;

			media.view.Settings.AttachmentDisplay.prototype.render.apply( this, arguments );
			setTimeout( function() { self.resetFocus(); }, 10 );

			this.settings = _.defaults( {
				success : this.success
			}, baseSettings );

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
			var i = media.view.MediaDetails.instances++;
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

	/**
	 * wp.media.view.AudioDetails
	 *
	 * @contructor
	 * @augments wp.media.view.MediaDetails
	 * @augments wp.media.view.Settings.AttachmentDisplay
	 * @augments wp.media.view.Settings
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.AudioDetails = media.view.MediaDetails.extend({
		className: 'audio-details',
		template:  media.template('audio-details'),

		setMedia: function() {
			var audio = this.$('.wp-audio-shortcode');

			if ( audio.find( 'source' ).length ) {
				if ( audio.is(':hidden') ) {
					audio.show();
				}
				this.media = media.view.MediaDetails.prepareSrc( audio.get(0) );
			} else {
				audio.hide();
				this.media = false;
			}

			return this;
		}
	});

	/**
	 * wp.media.view.VideoDetails
	 *
	 * @contructor
	 * @augments wp.media.view.MediaDetails
	 * @augments wp.media.view.Settings.AttachmentDisplay
	 * @augments wp.media.view.Settings
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.VideoDetails = media.view.MediaDetails.extend({
		className: 'video-details',
		template:  media.template('video-details'),

		setMedia: function() {
			var video = this.$('.wp-video-shortcode');

			if ( video.find( 'source' ).length ) {
				if ( video.is(':hidden') ) {
					video.show();
				}

				if ( ! video.hasClass('youtube-video') ) {
					this.media = media.view.MediaDetails.prepareSrc( video.get(0) );
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

	/**
	 * Event binding
	 */
	function init() {
		$(document.body)
			.on( 'click', '.wp-switch-editor', wp.media.mixin.pauseAllPlayers )
			.on( 'click', '.add-media-source', function( e ) {
				media.frame.lastMime = $( e.currentTarget ).data( 'mime' );
				media.frame.setState( 'add-' + media.frame.defaults.id + '-source' );
			} );
	}

	$( init );

}(jQuery, _, Backbone));
