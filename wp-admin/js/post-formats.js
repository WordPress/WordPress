(function($){

	// Post formats selection
	$('.post-format-select a').on( 'click.post-format', function(e) {
		var $this = $(this),
			editor,
			body,
			format = $this.data('wp-format'),
			container = $('#post-body-content');

		$('.post-format-select a.nav-tab-active').removeClass('nav-tab-active');
		$this.addClass('nav-tab-active').blur();
		$('#post_format').val(format);

		container.get(0).className = container.get(0).className.replace( /\bwp-format-[^ ]+/, '' );
		container.addClass('wp-format-' + format);

		if ( typeof tinymce != 'undefined' ) {
			editor = tinymce.get('content');

			if ( editor ) {
				body = editor.getBody();
				body.className = body.className.replace( /\bpost-format-[^ ]+/, '' );
				editor.dom.addClass( body, 'post-format-' + format );
			}
		}

		e.preventDefault();
	});

})(jQuery);
