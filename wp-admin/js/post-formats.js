window.wp = window.wp || {};

(function($){
	var imageFrame;

	// Post formats selection
	$('.post-format-select a').on( 'click.post-format', function(e) {
		var $this = $(this), editor, body,
			format = $this.data('wp-format'), container = $('#post-body-content');

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

	// Image selection
	$('#wp-format-image-select').click( function( event ) {
		var $el = $(this),
			$holder = $('#wp-format-image-holder'),
			$field = $('#wp_format_image');
		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( imageFrame ) {
			imageFrame.open();
			return;
		}

		// Create the media frame.
		imageFrame = wp.media.frames.formatImage = wp.media({
			// Set the title of the modal.
			title: $el.data('choose'),

			// Tell the modal to show only images.
			library: {
				type: 'image'
			},

			// Customize the submit button.
			button: {
				// Set the text of the button.
				text: $el.data('update')
			}
		});

		// When an image is selected, run a callback.
		imageFrame.on( 'select', function() {
			// Grab the selected attachment.
			var attachment = imageFrame.state().get('selection').first(),
				imageUrl = attachment.get('url');

			// set the hidden input's value
			$field.attr('value', attachment.id);

			// Show the image in the placeholder
			$el.html('<img src="' + imageUrl + '" />');
			$holder.removeClass('empty');
		});

		imageFrame.open();
	});
})(jQuery);
