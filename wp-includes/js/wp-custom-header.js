(function( window, $, settings ) {

	if ( ! ( 'addEventListener' in window ) ) {
		// Fail gracefully in unsupported browsers.
		return;
	}

	function wpCustomHeader() {
		var handlers = {
			nativeVideo: {
				test: function( settings ) {
					var video = document.createElement( 'video' );
					return video.canPlayType( settings.mimeType );
				},
				callback: nativeHandler
			},
			youtube: {
				test: function( settings ) {
					return 'video/x-youtube' === settings.mimeType;
				},
				callback: youtubeHandler
			}
		};

		function initialize() {
			settings.container = document.getElementById( 'wp-custom-header' );

			if ( supportsVideo() ) {
				for ( var id in handlers ) {
					var handler = handlers[ id ];

					if ( handlers.hasOwnProperty( id ) && handler.test( settings ) ) {
						handler.callback( settings );
						break;
					}
				}

				$( 'body' ).trigger( 'wp-custom-header-video-loaded' );
			}
		}

		function supportsVideo() {
			// Don't load video on small screens. @todo: consider bandwidth and other factors.
			if ( window.innerWidth < settings.minWidth  || window.innerHeight < settings.minHeight ) {
				return false;
			}

			return true;
		}

		return {
			handlers: handlers,
			initialize: initialize,
			supportsVideo: supportsVideo
		};
	}

	function nativeHandler( settings ) {
		var video = document.createElement( 'video' );

		video.id = 'wp-custom-header-video';
		video.autoplay = 'autoplay';
		video.loop = 'loop';
		video.muted = 'muted';
		video.width = settings.width;
		video.height = settings.height;

		video.addEventListener( 'click', function() {
			if ( video.paused ) {
				video.play();
			} else {
				video.pause();
			}
		});

		settings.container.innerHTML = '';
		settings.container.appendChild( video );
		video.src = settings.videoUrl;
	}

	function youtubeHandler( settings ) {
		// @link http://stackoverflow.com/a/27728417
		var VIDEO_ID_REGEX = /^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/,
			videoId = settings.videoUrl.match( VIDEO_ID_REGEX )[1];

		function loadVideo() {
			var YT = window.YT || {};

			YT.ready(function() {
				var video = document.createElement( 'div' );
				video.id = 'wp-custom-header-video';
				settings.container.innerHTML = '';
				settings.container.appendChild( video );

				new YT.Player( video, {
					height: settings.height,
					width: settings.width,
					videoId: videoId,
					events: {
						onReady: function( e ) {
							e.target.mute();
						},
						onStateChange: function( e ) {
							if ( YT.PlayerState.ENDED === e.data ) {
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
						//origin: '',
						playsinline: 1,
						rel: 0,
						showinfo: 0
					}
				});
			});
		}

		if ( 'YT' in window ) {
			loadVideo();
		} else {
			var tag = document.createElement( 'script' );
			tag.src = 'https://www.youtube.com/player_api';
			tag.onload = function () { loadVideo(); };
			document.getElementsByTagName( 'head' )[0].appendChild( tag );
		}
	}

	window.wp = window.wp || {};
	window.wp.customHeader = new wpCustomHeader();
	document.addEventListener( 'DOMContentLoaded', window.wp.customHeader.initialize, false );

	if ( 'customize' in window.wp ) {
		wp.customize.selectiveRefresh.bind( 'render-partials-response', function( response ) {
			if ( 'custom_header_settings' in response ) {
				settings = response.custom_header_settings;
			}
		});

		wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function( placement ) {
			if ( 'custom_header' === placement.partial.id ) {
				window.wp.customHeader.initialize();
			}
		});
	}

})( window, jQuery, window._wpCustomHeaderSettings || {} );
