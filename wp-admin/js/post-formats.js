window.wp = window.wp || {};

(function($) {
	var container, $container, mediaFrame, lastMimeType, mediaPreview, lastHeight = 360, content, insertMediaButton,
		initialFormat = 'standard',
		shortClass = 'short-format',
		shortContentFormats = ['status', 'aside'],
		noUIFormats = ['standard', 'chat', 'status', 'aside', 'gallery'],
		$screenIcon = $( '.icon32' );


	function switchFormatClass( format ) {
		container.get(0).className = container.get(0).className.replace( /\s?\bwp-format-[^ ]+/g, '' );
		container.addClass('wp-format-' + format);
		$screenIcon.get(0).className = $screenIcon.get(0).className.replace( /\s?\bwp-format-[^ ]+/g, '' );
		$screenIcon.addClass('wp-format-' + format);
	}

	function resizeContent( format, noAnimate ) {
		var height;

		content = $('#content, #content_ifr');

		height = content.height();
		if ( 120 < height ) {
			lastHeight = height;
		}

		if ( -1 < $.inArray( format, shortContentFormats ) ) {
			if ( ! content.hasClass(shortClass) ) {
				content.addClass(shortClass);
				if ( noAnimate ) {
					content.each(function () {
						$(this).css({ height : 120 });
					});
				} else {
					content.each(function () {
						$(this).animate({ height : 120 });
					});
				}
			}
		} else {
			content.removeClass(shortClass).animate({ height : lastHeight });
		}
	}

	function switchFormat($this) {
		var editor, body,
			parent = $this.parent(),
			format = $this.data('wp-format'),
			description = $('.post-format-description'),
			postTitle = $('#title');

		if ( typeof container === 'undefined' )
			container = $('#post-body-content');

		parent.slideUp().find('a.active').removeClass('active');
		$this.addClass('active');
		$('#post_format').val(format);
		$('.post-format-change').show().find('span.icon').removeClass(postFormats.currentPostFormat).addClass(format);

		if ( -1 < $.inArray( format, noUIFormats ) ) {
			switchFormatClass( format ); // No slide
			$container.hide();
		} else {
			$container.slideUp( 200, function(){
				switchFormatClass( format );
				$container.slideDown( 400 );
			});
		}

		resizeContent( format );

		postTitle.focus();

		if ( '' === postTitle.val() )
			$('#title-prompt-text').removeClass('screen-reader-text');

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

		// If gallery, force it to open to gallery state
		insertMediaButton.toggleClass( 'gallery', 'gallery' === format );

		postFormats.currentPostFormat = format;
	}



	$(function() {
		insertMediaButton = $( '#insert-media-button' ).toggleClass( 'gallery', 'gallery' === postFormats.currentPostFormat );
		$container = $( '.post-formats-fields' );

		initialFormat = $( '.post-format-options .active' ).data( 'wp-format' );
		if ( -1 < $.inArray( initialFormat, shortContentFormats ) ) {
			resizeContent( initialFormat, true );
		}

		$('#show_post_format_ui').on('change', function() {
			$('.wp-post-format-ui').toggleClass('no-ui', ! this.checked );
			$.post( ajaxurl, {
				action: 'show-post-format-ui',
				post_type: $('#post_type').val(),
				show: this.checked ? 1 : 0,
				nonce: $('#show_post_format_ui_nonce').val()
			});
		});

		$('.post-format-change a').click(function() {
			$('.post-formats-fields, .post-format-change').slideUp();
			$('.post-format-options').slideDown();
			return false;
		});

		// Post formats selection
		$('.post-format-options').on( 'click', 'a', function (e) {
			e.preventDefault();
			switchFormat($(this));
		});

		// Media selection
		$('.wp-format-media-select').click(function(event) {
			event.preventDefault();
			var $el = $(this), mime = 'image',
				$holder = $el.closest('.wp-format-media-holder'),
				$field = $( '#wp_format_' + $holder.data('format') );

			mime = $holder.data('format');

			// If the media frame already exists, reopen it.
			if ( mediaFrame && lastMimeType === mime ) {
				mediaFrame.open();
				return;
			}

			lastMimeType = mime;

			mediaFrame = wp.media.frames.formatMedia = wp.media( {
				button: {
					text: $el.data('update')
				},
				states: [
					new wp.media.controller.Library({
						library: wp.media.query( { type: mime } ),
						title: $el.data('choose'),
						displaySettings: 'image' === mime
					})
				]
			} );

			mediaPreview = function(attachment) {
				var w, h, dimensions = '', url = attachment.url, mime = attachment.mime, format = attachment.type;

				if ( 'video' === format ) {
					if ( attachment.width ) {
						w = attachment.width;
						if ( w > 600 )
							w = 600;
						dimensions += ' width="' + w + '"';
					}

					if ( attachment.height ) {
						h = attachment.height;
						if ( attachment.width && w < attachment.width )
							h = Math.round( ( h * w ) / attachment.width );
						dimensions += ' height="' + h + '"';
					}
				}

				$('#' + format + '-preview').remove();
				$holder.parent().prepend( '<div id="' + format + '-preview" class="wp-format-media-preview">' +
					'<' + format + dimensions + ' class="wp-' + format + '-shortcode" controls="controls" preload="none">' +
						'<source type="' + mime + '" src="' + url + '" />' +
					'</' + format + '></div>' );
				$('.wp-' + format + '-shortcode').mediaelementplayer();
			};

			// When an image is selected, run a callback.
			mediaFrame.on( 'select', function() {
				// Grab the selected attachment.
				var w = 0, h = 0, html, attachment = mediaFrame.state().get('selection').first().toJSON();

				if ( 0 === attachment.mime.indexOf('audio') ) {
					$field.val(attachment.url);
					// show one preview at a time
					mediaPreview(attachment);
				} else if ( 0 === attachment.mime.indexOf('video') ) {
					attachment.src = attachment.url;
					$field.val(wp.shortcode.string({
						tag:     'video',
						attrs: _.pick( attachment, 'src', 'width', 'height' )
					}));
					// show one preview at a time
					mediaPreview(attachment);
				} else {
					html = wp.media.string.image({
						align : getUserSetting('align'),
						size : getUserSetting('imgsize'),
						link : getUserSetting('urlbutton')
					}, attachment);
					// set the hidden input's value
					$field.val(html);
					$('#image-preview').remove();
					if ( attachment.width )
						w = attachment.width > 600 ? 600 : attachment.width;
					if ( attachment.height )
						h = attachment.height;
					if ( w < attachment.width )
						h = Math.round( ( h * w ) / attachment.width );
					$holder.parent().prepend( ['<div id="image-preview" class="wp-format-media-preview">',
						'<img src="', attachment.url, '"',
						w ? ' width="' + w + '"' : '',
						h ? ' height="' + h + '"' : '',
						' />',
					'</div>'].join('') );
				}
			});

			mediaFrame.open();
		});
	});
})(jQuery);
