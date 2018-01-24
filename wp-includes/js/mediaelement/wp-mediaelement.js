/* global _wpmejsSettings, mejsL10n */
(function( window, $ ) {

	window.wp = window.wp || {};

	function wpMediaElement() {
		var settings = {};

		/**
		 * Initialize media elements.
		 *
		 * Ensures media elements that have already been initialized won't be
		 * processed again.
		 *
		 * @memberOf wp.mediaelement
		 *
		 * @since 4.4.0
		 *
		 * @returns {void}
		 */
		function initialize() {
			if ( typeof _wpmejsSettings !== 'undefined' ) {
				settings = $.extend( true, {}, _wpmejsSettings );
			}
			settings.classPrefix = 'mejs-';
			settings.success = settings.success || function ( mejs ) {
				var autoplay, loop;

				if ( mejs.rendererName && -1 !== mejs.rendererName.indexOf( 'flash' ) ) {
					autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;
					loop = mejs.attributes.loop && 'false' !== mejs.attributes.loop;

					if ( autoplay ) {
						mejs.addEventListener( 'canplay', function() {
							mejs.play();
						}, false );
					}

					if ( loop ) {
						mejs.addEventListener( 'ended', function() {
							mejs.play();
						}, false );
					}
				}
			};

			/**
			 * Custom error handler.
			 *
			 * Sets up a custom error handler in case a video render fails, and provides a download
			 * link as the fallback.
			 *
			 * @since 4.9.3
			 *
			 * @param {object} media The wrapper that mimics all the native events/properties/methods for all renderers.
			 * @param {object} node  The original HTML video, audio, or iframe tag where the media was loaded.
			 * @returns {string}
			 */
			settings.customError = function ( media, node ) {
				// Make sure we only fall back to a download link for flash files.
				if ( -1 !== media.rendererName.indexOf( 'flash' ) || -1 !== media.rendererName.indexOf( 'flv' ) ) {
					return '<a href="' + node.src + '">' + mejsL10n.strings['mejs.download-video'] + '</a>';
				}
			};

			// Only initialize new media elements.
			$( '.wp-audio-shortcode, .wp-video-shortcode' )
				.not( '.mejs-container' )
				.filter(function () {
					return ! $( this ).parent().hasClass( 'mejs-mediaelement' );
				})
				.mediaelementplayer( settings );
		}

		return {
			initialize: initialize
		};
	}

	/**
	 * @namespace wp.mediaelement
	 * @memberOf wp
	 */
	window.wp.mediaelement = new wpMediaElement();

	$( window.wp.mediaelement.initialize );

})( window, jQuery );
