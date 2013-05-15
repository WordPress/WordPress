/*globals window, $, jQuery, document, _, postFormats, tinymce, ajaxurl, wp, getUserSetting */

window.wp = window.wp || {};

(function ($) {
	"use strict";

	var mediaFrame, insertMediaButton, container, icon, formatField,
		body,
		lastMimeType,
		classRegex = /\s?\bwp-format-[^ ]+/g,
		shortHeight = 120,
		lastHeight = 360,
		initialFormat = 'standard',
		shortClass = 'short-format',
		noTitleFormats = ['status'],
		noMediaFormats = ['status', 'aside', 'image', 'audio', 'video'],
		shortContentFormats = ['status', 'aside'],
		noUIFormats = ['standard', 'chat', 'status', 'aside', 'gallery'];

	function imageFormatUploadProgress( uploader, file ) {
		var $bar = $( '#' + uploader.settings.drop_element + ' .media-progress-bar div' );
		$bar.width( file.percent + '%' );
	}

	function imageFormatUploadStart( uploader ) {
		$( '#' + uploader.settings.drop_element + ' .wp-format-media-select' ).append('<div class="media-progress-bar"><div></div></div>');
	}

	function imageFormatUploadError() {
		$( '.media-progress-bar', $('.wp-format-media-holder[data-format=image]') ).remove();
	}

	function imageFormatUploadSuccess( attachment ) {
		var $holder, $field, html = wp.media.string.image({
			size : 'full',
			align : false,
			link : getUserSetting( 'urlbutton' )
		}, attachment.attributes );

		$holder = $('.wp-format-media-holder[data-format=image]');
		$( '.media-progress-bar', $holder ).remove();

		if ( 'image' !== attachment.attributes.type )
			return;

		$field = $( '#wp_format_' + $holder.data( 'format' ) );

		// set the hidden input's value
		$field.val( html );

		$( '#image-preview' ).remove();

		$holder.parent().prepend( ['<div id="image-preview" class="wp-format-media-preview">',
			'<img src="', attachment.get('url'), '"',
			attachment.get('width') ? ' width="' + attachment.get('width') + '"' : '',
			attachment.get('height') ? ' height="' + attachment.get('height') + '"' : '',
			' />',
		'</div>'].join( '' ) );
	}

	function imageFormatUploadFilesAdded( uploader, files ) {
		$.each( files, function( i, file ) {
			if ( i > 0 )
				uploader.removeFile(file);
		});
	}

	var uploader = {
		dropzone:  $('.wp-format-media-holder[data-format=image]'),
		success:   imageFormatUploadSuccess,
		error:     imageFormatUploadError,
		plupload:  {
			runtimes: 'html5',
			filters: [ {title: 'Image', extensions: 'jpg,jpeg,gif,png'} ]
		},
		params:    {}
	};
	uploader = new wp.Uploader( uploader );
	uploader.uploader.bind( 'BeforeUpload', imageFormatUploadStart );
	uploader.uploader.bind( 'UploadProgress', imageFormatUploadProgress );
	uploader.uploader.bind( 'FilesAdded', imageFormatUploadFilesAdded );

	function switchFormatClass( format ) {
		formatField.val( format );

		$.each( [ container, icon, body ], function(i, thing) {
			thing.prop( 'className', thing.prop( 'className' ).replace( classRegex, '' ) )
			.addClass( 'wp-format-' + format );
		});
	}

	function resizeContent( format, noAnimate ) {
		var height, content = $( '#content, #content_ifr' );

		height = content.outerHeight();
		if ( shortHeight < height ) {
			lastHeight = height;
		}

		if ( -1 < $.inArray( format, shortContentFormats ) ) {
			if ( ! content.hasClass( shortClass ) ) {
				content.addClass( shortClass );
				_(content).each(function (elem) {
					$(elem)[noAnimate ? 'css' : 'animate']( { height : shortHeight } );
				});
			}
		} else {
			content.removeClass( shortClass ).animate( { height : lastHeight } );
		}
	}

	function switchFormat(elem) {
		var editor, body, formatTo, formatFrom,
			format = elem.data( 'wp-format' ),
			titlePrompt = $( '#title-prompt-text' ),
			description = $( '.post-format-description' ),
			postTitle = $( '#title'),
			fields = $( '.post-formats-fields' ),
			tinyIcon = $( '.post-format-change span.icon' );

		if ( format === postFormats.currentPostFormat ) {
			return;
		}

		elem.addClass( 'active' ).siblings().removeClass( 'active' );

		// Animate the media button going away or coming back
		formatTo = -1 < $.inArray( format, noMediaFormats );
		formatFrom = -1 < $.inArray( postFormats.currentPostFormat, noMediaFormats );
		if ( formatFrom ? !formatTo : formatTo ) { // XOR
			insertMediaButton.fadeToggle( 200 ).css( 'display', 'inline-block' );
		}
		// Animate the title going away or coming back
		formatTo = -1 < $.inArray( format, noTitleFormats );
		formatFrom = -1 < $.inArray( postFormats.currentPostFormat, noTitleFormats );
		if ( formatFrom ? !formatTo : formatTo ) { // XOR
			$( '#titlewrap' ).fadeToggle( 200 );
		}

		// Animate the fields moving going away or coming in
		formatTo = -1 < $.inArray( format, noUIFormats );
		formatFrom = -1 < $.inArray( postFormats.currentPostFormat, noUIFormats );
		if ( formatTo && formatFrom ) { // To/from have no UI. No slide.
			switchFormatClass( format );
			fields.hide();
		} else if ( formatFrom ) { // Only destination has UI. Slide down.
			fields.hide();
			switchFormatClass( format );
			fields.slideDown( 400 );
		} else if ( formatTo ) { // Only source has UI. Slide up.
			fields.slideUp( 200, function(){
				switchFormatClass( format );
			});
		} else { // Both have UI. Slide both ways.
			fields.slideUp( 200, function(){
				switchFormatClass( format );
				fields.slideDown( 400 );
			});
		}

		resizeContent( format );
		postTitle.focus();

		if ( '' === postTitle.val() ) {
			titlePrompt.removeClass( 'screen-reader-text' );

			postTitle.keydown( function (e) {
				titlePrompt.addClass( 'screen-reader-text' );
				$( e.currentTarget ).unbind( e );
			} );
		}

		// Update description line
		description.html( elem.data( 'description' ) );
		tinyIcon
			.show()
			.prop( 'className', tinyIcon.prop( 'className' ).replace( classRegex, '' ) )
			.addClass( 'wp-format-' + format );

		if ( description.not( ':visible' ) ) {
			description.slideDown( 'fast' );
		}

		if ( typeof tinymce !== 'undefined' ) {
			editor = tinymce.get( 'content' );

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

	$(function () {
		body = $( 'body' );
		container = $( '#post-body-content' );
		icon = $( '.icon32' );
		formatField = $( '#post_format' );
		insertMediaButton = $( '#insert-media-button' ).toggleClass( 'gallery', 'gallery' === postFormats.currentPostFormat );
		initialFormat = $( '.post-format-options .active' ).data( 'wp-format' );

		if ( -1 < $.inArray( initialFormat, shortContentFormats ) ) {
			resizeContent( initialFormat, true );
		}

		$( '#show_post_format_ui' ).on( 'change', function () {
			body.toggleClass( 'wp-post-format-show-ui', this.checked );

			// Reset the display properties of possibly hidden items.
			insertMediaButton.css( 'display', '' );
			$( '#titlewrap' ).css( 'display', '' );

			$.post( ajaxurl, {
				action: 'show-post-format-ui',
				post_type: $( '#post_type' ).val(),
				show: this.checked ? 1 : 0,
				nonce: $( '#show_post_format_ui_nonce' ).val()
			} );
		} );

		// Post formats selection
		$( '.post-format-options' ).on( 'click', 'a', function (e) {
			e.preventDefault();
			switchFormat( $( e.currentTarget ) );
		} );

		// Toggle select/upload and URL/HTML for images
		$( '.use-url-or-html' ).on( 'click', 'a', function(e) {
			e.preventDefault();
			$( '.wp-format-media-holder, .wp-format-image-textarea' ).toggle();
			$(this).closest( 'p' ).find( 'span' ).toggle();
		});

		// Media selection
		$( '.wp-format-media-select' ).click( function (e) {
			e.preventDefault();

			var $el = $(e.currentTarget), mediaPreview, mime = 'image', $holder, $field;

			$holder = $el.closest( '.wp-format-media-holder' );
			$field = $( '#wp_format_' + $holder.data( 'format' ) );
			mime = $holder.data( 'format' );

			// If the media frame already exists, reopen it.
			if ( mediaFrame && lastMimeType === mime ) {
				mediaFrame.open();
				return;
			}

			lastMimeType = mime;

			mediaFrame = wp.media.frames.formatMedia = wp.media( {
				button: {
					text: $el.data( 'update' )
				},
				states: [
					new wp.media.controller.Library({
						library: wp.media.query( { type: mime } ),
						title: $el.data( 'choose' ),
						displaySettings: 'image' === mime
					})
				]
			} );

			mediaPreview = function (attachment) {
				var w, h, dimensions = '', url = attachment.url, mime = attachment.mime, format = attachment.type;

				if ( 'video' === format ) {
					if ( attachment.width ) {
						w = attachment.width;
						if ( w > 600 ) {
							w = 600;
						}
						dimensions += ' width="' + w + '"';
					}

					if ( attachment.height ) {
						h = attachment.height;
						if ( attachment.width && w < attachment.width ) {
							h = Math.round( ( h * w ) / attachment.width );
						}
						dimensions += ' height="' + h + '"';
					}
				}

				$( '#' + format + '-preview' ).remove();
				$holder.parent().prepend( '<div id="' + format + '-preview" class="wp-format-media-preview">' +
					'<' + format + dimensions + ' class="wp-' + format + '-shortcode" controls="controls" preload="none">' +
						'<source type="' + mime + '" src="' + url + '" />' +
					'</' + format + '></div>' );
				$( '.wp-' + format + '-shortcode' ).mediaelementplayer();
			};

			// When an image is selected, run a callback.
			mediaFrame.on( 'select', function () {
				// Grab the selected attachment.
				var w = 0, h = 0, html, attachment = mediaFrame.state().get( 'selection' ).first().toJSON();

				if ( 0 === attachment.mime.indexOf( 'audio' ) ) {
					$field.val( attachment.url );
					// show one preview at a time
					mediaPreview( attachment );
				} else if ( 0 === attachment.mime.indexOf( 'video' ) ) {
					attachment.src = attachment.url;
					$field.val( wp.shortcode.string( {
						tag:     'video',
						attrs: _.pick( attachment, 'src', 'width', 'height' )
					} ) );
					// show one preview at a time
					mediaPreview( attachment );
				} else {
					html = wp.media.string.image({
						size: 'full',
						align : false,
						link : getUserSetting( 'urlbutton' )
					}, attachment);

					// set the hidden input's value
					$field.val( html );

					$( '#image-preview' ).remove();

					if ( attachment.width ) {
						w = attachment.width > 600 ? 600 : attachment.width;
					}

					if ( attachment.height ) {
						h = attachment.height;
					}

					if ( w < attachment.width ) {
						h = Math.round( ( h * w ) / attachment.width );
					}

					$holder.parent().prepend( ['<div id="image-preview" class="wp-format-media-preview">',
						'<img src="', attachment.url, '"',
						w ? ' width="' + w + '"' : '',
						h ? ' height="' + h + '"' : '',
						' />',
					'</div>'].join( '' ) );
				}
			});

			mediaFrame.open();
		});
	});
}( jQuery ) );
