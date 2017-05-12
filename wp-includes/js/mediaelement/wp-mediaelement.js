/* global mejs, _wpmejsSettings */
(function( window, $ ) {

	window.wp = window.wp || {};

	// add mime-type aliases to MediaElement plugin support
	mejs.plugins.silverlight[0].types.push('video/x-ms-wmv');
	mejs.plugins.silverlight[0].types.push('audio/x-ms-wma');

	function wpMediaElement() {
		var settings = {};

		/**
		 * Initialize media elements.
		 *
		 * Ensures media elements that have already been initialized won't be
		 * processed again.
		 *
		 * @since 4.4.0
		 */
		function initialize() {
			if ( typeof _wpmejsSettings !== 'undefined' ) {
				settings = $.extend( true, {}, _wpmejsSettings );
			}

			settings.success = settings.success || function (mejs) {
				var autoplay, loop;

				if ( 'flash' === mejs.pluginType ) {
					autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;
					loop = mejs.attributes.loop && 'false' !== mejs.attributes.loop;

					autoplay && mejs.addEventListener( 'canplay', function () {
						mejs.play();
					}, false );

					loop && mejs.addEventListener( 'ended', function () {
						mejs.play();
					}, false );
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

	window.wp.mediaelement = new wpMediaElement();

	$( window.wp.mediaelement.initialize );

})( window, jQuery );
