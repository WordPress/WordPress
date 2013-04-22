window.wp = window.wp || {};

(function($) {
	var container, mediaFrame, lastMimeType, mediaPreview,
		noUIFormats = ['standard', 'chat', 'status', 'aside', 'gallery'],
		$container = $( '.post-formats-fields' ),
		$screenIcon = $( '.icon32' );

	function switchFormatClass( format ) {
		container.get(0).className = container.get(0).className.replace( /\s?\bwp-format-[^ ]+/g, '' );
		container.addClass('wp-format-' + format);
		$screenIcon.get(0).className = $screenIcon.get(0).className.replace( /\s?\bwp-format-[^ ]+/g, '' );
		$screenIcon.addClass('wp-format-' + format);
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

		postFormats.currentPostFormat = format;
	}

	$(function(){

		$('.post-format-change a').click(function() {
			$('.post-formats-fields, .post-format-change').slideUp();
			$('.post-format-options').slideDown();
			return false;
		});

		// Post formats selection
		$('.post-format-options').on( 'click', 'a', function(e){
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
