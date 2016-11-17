/* global YT */
(function( window, settings ) {

	var NativeHandler, YouTubeHandler;

	window.wp = window.wp || {};

	// Fail gracefully in unsupported browsers.
	if ( ! ( 'addEventListener' in window ) ) {
		return;
	}

	/**
	 * Trigger an event.
	 *
	 * @param {Element} target HTML element to dispatch the event on.
	 * @param {string} name Event name.
	 */
	function trigger( target, name ) {
		var evt;

		if ( 'function' === typeof window.Event ) {
			evt = new Event( name );
		} else {
			evt = document.createEvent( 'Event' );
			evt.initEvent( name, true, true );
		}

		target.dispatchEvent( evt );
	}

	/**
	 * Create a custom header instance.
	 *
	 * @class CustomHeader
	 */
	function CustomHeader() {
		this.handlers = {
			nativeVideo: new NativeHandler(),
			youtube: new YouTubeHandler()
		};
	}

	CustomHeader.prototype = {
		/**
		 * Initalize the custom header.
		 *
		 * If the environment supports video, loops through registered handlers
		 * until one is found that can handle the video.
		 */
		initialize: function() {
			if ( this.supportsVideo() ) {
				for ( var id in this.handlers ) {
					var handler = this.handlers[ id ];

					if ( 'test' in handler && handler.test( settings ) ) {
						this.activeHandler = handler.initialize.call( handler, settings );

						// Dispatch custom event when the video is loaded.
						trigger( document, 'wp-custom-header-video-loaded' );
						break;
					}
				}
			}
		},

		/**
		 * Determines if the current environment supports video.
		 *
		 * Themes and plugins can override this method to change the criteria.
		 *
		 * @return {boolean}
		 */
		supportsVideo: function() {
			// Don't load video on small screens. @todo: consider bandwidth and other factors.
			if ( window.innerWidth < settings.minWidth || window.innerHeight < settings.minHeight ) {
				return false;
			}

			return true;
		},

		/**
		 * Base handler for custom handlers to extend.
		 *
		 * @type {BaseHandler}
		 */
		BaseVideoHandler: BaseHandler
	};

	/**
	 * Create a video handler instance.
	 *
	 * @class BaseHandler
	 */
	function BaseHandler() {}

	BaseHandler.prototype = {
		/**
		 * Initialize the video handler.
		 *
		 * @param {object} settings Video settings.
		 */
		initialize: function( settings ) {
			var handler = this,
				button = document.createElement( 'button' );

			this.settings = settings;
			this.container = document.getElementById( 'wp-custom-header' );
			this.button = button;

			button.setAttribute( 'type', 'button' );
			button.setAttribute( 'id', 'wp-custom-header-video-button' );
			button.setAttribute( 'class', 'wp-custom-header-video-button wp-custom-header-video-play' );
			button.innerHTML = settings.l10n.play;

			// Toggle video playback when the button is clicked.
			button.addEventListener( 'click', function() {
				if ( handler.isPaused() ) {
					handler.play();
				} else {
					handler.pause();
				}
			});

			// Update the button class and text when the video state changes.
			this.container.addEventListener( 'play', function() {
				button.className = 'wp-custom-header-video-button wp-custom-header-video-play';
				button.innerHTML = settings.l10n.pause;
				if ( 'a11y' in window.wp ) {
					window.wp.a11y.speak( settings.l10n.playSpeak);
				}
			});

			this.container.addEventListener( 'pause', function() {
				button.className = 'wp-custom-header-video-button wp-custom-header-video-pause';
				button.innerHTML = settings.l10n.play;
				if ( 'a11y' in window.wp ) {
					window.wp.a11y.speak( settings.l10n.pauseSpeak);
				}
			});

			this.ready();
		},

		/**
		 * Ready method called after a handler is initialized.
		 *
		 * @abstract
		 */
		ready: function() {},

		/**
		 * Whether the video is paused.
		 *
		 * @abstract
		 * @return {boolean}
		 */
		isPaused: function() {},

		/**
		 * Pause the video.
		 *
		 * @abstract
		 */
		pause: function() {},

		/**
		 * Play the video.
		 *
		 * @abstract
		 */
		play: function() {},

		/**
		 * Append a video node to the header container.
		 *
		 * @param {Element} node HTML element.
		 */
		setVideo: function( node ) {
			var editShortcutNode,
				editShortcut = this.container.getElementsByClassName( 'customize-partial-edit-shortcut' );

			if ( editShortcut.length ) {
				editShortcutNode = this.container.removeChild( editShortcut[0] );
			}

			this.container.innerHTML = '';
			this.container.appendChild( node );

			if ( editShortcutNode ) {
				this.container.appendChild( editShortcutNode );
			}
		},

		/**
		 * Show the video controls.
		 *
		 * Appends a play/pause button to header container.
		 */
		showControls: function() {
			if ( ! this.container.contains( this.button ) ) {
				this.container.appendChild( this.button );
			}
		},

		/**
		 * Whether the handler can process a video.
		 *
		 * @abstract
		 * @param {object} settings Video settings.
		 * @return {boolean}
		 */
		test: function() {
			return false;
		},

		/**
		 * Trigger an event on the header container.
		 *
		 * @param {string} name Event name.
		 */
		trigger: function( name ) {
			trigger( this.container, name );
		}
	};

	/**
	 * Create a custom handler.
	 *
	 * @param {object} protoProps Properties to apply to the prototype.
	 * @return CustomHandler The subclass.
	 */
	BaseHandler.extend = function( protoProps ) {
		var prop;

		function CustomHandler() {
			var result = BaseHandler.apply( this, arguments );
			return result;
		}

		CustomHandler.prototype = Object.create( BaseHandler.prototype );
		CustomHandler.prototype.constructor = CustomHandler;

		for ( prop in protoProps ) {
			CustomHandler.prototype[ prop ] = protoProps[ prop ];
		}

		return CustomHandler;
	};

	/**
	 * Native video handler.
	 *
	 * @class NativeHandler
	 */
	NativeHandler = BaseHandler.extend({
		/**
		 * Whether the native handler supports a video.
		 *
		 * @param {object} settings Video settings.
		 * @return {boolean}
		 */
		test: function( settings ) {
			var video = document.createElement( 'video' );
			return video.canPlayType( settings.mimeType );
		},

		/**
		 * Set up a native video element.
		 */
		ready: function() {
			var handler = this,
				video = document.createElement( 'video' );

			video.id = 'wp-custom-header-video';
			video.autoplay = 'autoplay';
			video.loop = 'loop';
			video.muted = 'muted';
			video.width = this.settings.width;
			video.height = this.settings.height;

			video.addEventListener( 'play', function() {
				handler.trigger( 'play' );
			});

			video.addEventListener( 'pause', function() {
				handler.trigger( 'pause' );
			});

			video.addEventListener( 'canplay', function() {
				handler.showControls();
			});

			this.video = video;
			handler.setVideo( video );
			video.src = this.settings.videoUrl;
		},

		/**
		 * Whether the video is paused.
		 *
		 * @return {boolean}
		 */
		isPaused: function() {
			return this.video.paused;
		},

		/**
		 * Pause the video.
		 */
		pause: function() {
			this.video.pause();
		},

		/**
		 * Play the video.
		 */
		play: function() {
			this.video.play();
		}
	});

	/**
	 * YouTube video handler.
	 *
	 * @class YouTubeHandler
	 */
	YouTubeHandler = BaseHandler.extend({
		/**
		 * Whether the handler supports a video.
		 *
		 * @param {object} settings Video settings.
		 * @return {boolean}
		 */
		test: function( settings ) {
			return 'video/x-youtube' === settings.mimeType;
		},

		/**
		 * Set up a YouTube iframe.
		 *
		 * Loads the YouTube IFrame API if the 'YT' global doesn't exist.
		 */
		ready: function() {
			var handler = this;

			if ( 'YT' in window ) {
				YT.ready( handler.loadVideo.bind( handler ) );
			} else {
				var tag = document.createElement( 'script' );
				tag.src = 'https://www.youtube.com/iframe_api';
				tag.onload = function () {
					YT.ready( handler.loadVideo.bind( handler ) );
				};

				document.getElementsByTagName( 'head' )[0].appendChild( tag );
			}
		},

		/**
		 * Load a YouTube video.
		 */
		loadVideo: function() {
			var handler = this,
				video = document.createElement( 'div' ),
				// @link http://stackoverflow.com/a/27728417
				VIDEO_ID_REGEX = /^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/;

			video.id = 'wp-custom-header-video';
			handler.setVideo( video );

			handler.player = new YT.Player( video, {
				height: this.settings.height,
				width: this.settings.width,
				videoId: this.settings.videoUrl.match( VIDEO_ID_REGEX )[1],
				events: {
					onReady: function( e ) {
						e.target.mute();
						handler.showControls();
					},
					onStateChange: function( e ) {
						if ( YT.PlayerState.PLAYING === e.data ) {
							handler.trigger( 'play' );
						} else if ( YT.PlayerState.PAUSED === e.data ) {
							handler.trigger( 'pause' );
						} else if ( YT.PlayerState.ENDED === e.data ) {
							e.target.playVideo();
						}
					}
				},
				playerVars: {
					autoplay: 1,
					controls: 0,
					disablekb: 1,
					fs: 0,
					iv_load_policy: 3,
					loop: 1,
					modestbranding: 1,
					playsinline: 1,
					rel: 0,
					showinfo: 0
				}
			});
		},

		/**
		 * Whether the video is paused.
		 *
		 * @return {boolean}
		 */
		isPaused: function() {
			return YT.PlayerState.PAUSED === this.player.getPlayerState();
		},

		/**
		 * Pause the video.
		 */
		pause: function() {
			this.player.pauseVideo();
		},

		/**
		 * Play the video.
		 */
		play: function() {
			this.player.playVideo();
		}
	});

	// Initialize the custom header when the DOM is ready.
	window.wp.customHeader = new CustomHeader();
	document.addEventListener( 'DOMContentLoaded', window.wp.customHeader.initialize.bind( window.wp.customHeader ), false );

	// Selective refresh support in the Customizer.
	if ( 'customize' in window.wp ) {
		window.wp.customize.selectiveRefresh.bind( 'render-partials-response', function( response ) {
			if ( 'custom_header_settings' in response ) {
				settings = response.custom_header_settings;
			}
		});

		window.wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function( placement ) {
			if ( 'custom_header' === placement.partial.id ) {
				window.wp.customHeader.initialize();
			}
		});
	}

})( window, window._wpCustomHeaderSettings || {} );
