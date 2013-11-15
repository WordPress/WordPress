/* global mejs, _wpmejsSettings */
(function ($) {
	// add mime-type aliases to MediaElement plugin support
	mejs.plugins.silverlight[0].types.push('video/x-ms-wmv');
	mejs.plugins.silverlight[0].types.push('audio/x-ms-wma');

	$(function () {
		var settings = {};

		if ( typeof _wpmejsSettings !== 'undefined' )
			settings.pluginPath = _wpmejsSettings.pluginPath;

		$('.wp-audio-shortcode, .wp-video-shortcode').mediaelementplayer( settings );
	});

}(jQuery));
