/**
 * Fix the aspect ratio of iframes, assuming CSS for overlay is defined.
 * @package Twenty8teen
 */
( function($) {
	var ignore = [
		'.iframefix-ignore, .fitvidsignore',
	  '.iframe-wrapper iframe',
	  '.responsive-object iframe',
	  '.video-container iframe',
	  '.responsive-embed iframe',
	  '.embed-responsive iframe',
		'iframe.wp-embedded-content',
		'iframe:not([width])',
		'iframe:not([height])'
	];
	$('iframe')
	  .not(ignore.join(',')).each(function() {
			var $this = $(this),
				height = ( $this.attr('height') && !isNaN(parseInt($this.attr('height'), 10)) )
					? parseInt($this.attr('height'), 10) : null,
				width = ( $this.attr('width') && !isNaN(parseInt($this.attr('width'), 10)) )
					? parseInt($this.attr('width'), 10) : null,
				ratio = (height && width) ? (height / width * 100).toPrecision(4) + '%' : '';
	    $(this).wrap('<div class="iframe-wrapper"></div>')
			.parent().css('padding-bottom', ratio);
	  } );
} )( window.jQuery || window.Zepto );
