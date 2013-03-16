(function ($) {
	// add mime-type aliases to MediaElement plugin support
	mejs.plugins.silverlight[0].types.push('video/x-ms-wmv');
	mejs.plugins.silverlight[0].types.push('audio/x-ms-wma');

    $(function () {
		$('.wp-audio-shortcode, .wp-video-shortcode').mediaelementplayer();
	});

}(jQuery));