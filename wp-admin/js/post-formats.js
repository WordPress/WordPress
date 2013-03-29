window.wp = window.wp || {};

(function($) {
	var mediaFrame, lastMimeType, lastMenu, mediaPreview;
	$(function(){
		var $container = $( '.post-formats-fields' );

		// Post formats selection
		$('.post-format-options').on( 'click', 'a', function(e){
			var $this = $(this), editor, body,
				parent = $this.parent(),
				format = $this.data('wp-format'),
				container = $('#post-body-content'),
				description = $('.post-format-description');

			parent.find('a.active').removeClass('active');
			$this.addClass('active');
			$('#icon-edit').removeClass(postFormats.currentPostFormat).addClass(format);
			$('#post_format').val(format);

			$container.slideUp( 200, function(){
				container.get(0).className = container.get(0).className.replace( /\bwp-format-[^ ]+/, '' );
				container.addClass('wp-format-' + format);
				$container.slideDown( 400 );
			});

			$('#title').focus();

			// Update description line
			description.html($this.data('description'));

			if (description.not(':visible'))
				description.slideDown('fast');

			if ( typeof tinymce != 'undefined' ) {
				editor = tinymce.get('content');

				if ( editor ) {
					body = editor.getBody();
					body.className = body.className.replace( /\bpost-format-[^ ]+/, '' );
					editor.dom.addClass( body, 'post-format-' + format );
				}
			}

			postFormats.currentPostFormat = format;

			e.preventDefault();
		}).on('mouseenter focusin', 'a', function () {
			$('.post-format-tip').html( $(this).prop('title') );
		}).on('mouseleave focusout', 'a', function () {
			$('.post-format-tip').html( $('.post-format-options a.active').prop('title') );
		});

		// Media selection
		$('.wp-format-media-select').click(function (event) {
			event.preventDefault();
			var $el = $(this), $holder, $field, mime = 'image', menu = '',
			    $holder = $el.closest('.wp-format-media-holder'),
			    $field = $( '#wp_format_' + $holder.data('format') );

			switch ( $holder.data('format') ) {
				case 'audio':
					mime = 'audio';
					break;
				case 'video':
					mime = 'video';
					break;
			}

			// If the media frame already exists, reopen it.
			if ( mediaFrame && lastMimeType === mime && lastMenu === menu ) {
				mediaFrame.open();
				return;
			}

			lastMimeType = mime;
			lastMenu = menu;

			// Create the media frame.
			mediaFrame = wp.media.frames.formatMedia = wp.media({
				// Set the title of the modal.
				title: $el.data('choose'),

				// Set the menu sidebar of the modal, if applicable
				toolbar: menu,

				// Tell the modal to show only items matching the current mime type.
				library: {
					type: mime
				},

				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: $el.data('update')
				}
			});

			mediaPreview = function (format, url, mime) {
				$('#' + format + '-preview').remove();
				$holder.parent().prepend( '<div id="' + format + '-preview" class="wp-format-media-preview">' +
					'<' + format + ' class="wp-' + format + '-shortcode" controls="controls" preload="none">' +
						'<source type="' + mime + '" src="' + url + '" />' +
					'</' + format + '></div>' );
				$('.wp-' + format + '-shortcode').mediaelementplayer();
			};

			// When an image is selected, run a callback.
			mediaFrame.on( 'select', function () {
				// Grab the selected attachment.
				var attachment = mediaFrame.state().get('selection').first(), mime, url, id;

				id = attachment.get('id');
				url = attachment.get('url');
				mime = attachment.get('mime');

				if ( 0 === mime.indexOf('audio') ) {
					$field.val(url);
					// show one preview at a time
					mediaPreview('audio', url, mime);
				} else if ( 0 === mime.indexOf('video') ) {
					$field.val(url);
					// show one preview at a time
					mediaPreview('video', url, mime);
				} else {
					// set the hidden input's value
					$field.val(id);
					// Show the image in the placeholder
					$el.html('<img src="' + url + '" />');
					$holder.removeClass('empty').show();
				}
			});

			mediaFrame.open();
		});
	});
})(jQuery);
