/* global plupload, pluploadL10n, ajaxurl, post_id, wpUploaderInit, deleteUserSetting, setUserSetting, getUserSetting, shortform */
var topWin = window.dialogArguments || opener || parent || top, uploader, uploader_init;

// progress and success handlers for media multi uploads
function fileQueued( fileObj ) {
	// Get rid of unused form
	jQuery( '.media-blank' ).remove();

	var items = jQuery( '#media-items' ).children(), postid = post_id || 0;

	// Collapse a single item
	if ( items.length == 1 ) {
		items.removeClass( 'open' ).find( '.slidetoggle' ).slideUp( 200 );
	}
	// Create a progress bar containing the filename
	jQuery( '<div class="media-item">' )
		.attr( 'id', 'media-item-' + fileObj.id )
		.addClass( 'child-of-' + postid )
		.append( '<div class="progress"><div class="percent">0%</div><div class="bar"></div></div>',
			jQuery( '<div class="filename original">' ).text( ' ' + fileObj.name ) )
		.appendTo( jQuery( '#media-items' ) );

	// Disable submit
	jQuery( '#insert-gallery' ).prop( 'disabled', true );
}

function uploadStart() {
	try {
		if ( typeof topWin.tb_remove != 'undefined' )
			topWin.jQuery( '#TB_overlay' ).unbind( 'click', topWin.tb_remove );
	} catch( e ){}

	return true;
}

function uploadProgress( up, file ) {
	var item = jQuery( '#media-item-' + file.id );

	jQuery( '.bar', item ).width( ( 200 * file.loaded ) / file.size );
	jQuery( '.percent', item ).html( file.percent + '%' );
}

// check to see if a large file failed to upload
function fileUploading( up, file ) {
	var hundredmb = 100 * 1024 * 1024,
		max = parseInt( up.settings.max_file_size, 10 );

	if ( max > hundredmb && file.size > hundredmb ) {
		setTimeout( function() {
			if ( file.status < 3 && file.loaded === 0 ) { // not uploading
				wpFileError( file, pluploadL10n.big_upload_failed.replace( '%1$s', '<a class="uploader-html" href="#">' ).replace( '%2$s', '</a>' ) );
				up.stop(); // stops the whole queue
				up.removeFile( file );
				up.start(); // restart the queue
			}
		}, 10000 ); // wait for 10 sec. for the file to start uploading
	}
}

function updateMediaForm() {
	var items = jQuery( '#media-items' ).children();

	// Just one file, no need for collapsible part
	if ( items.length == 1 ) {
		items.addClass( 'open' ).find( '.slidetoggle' ).show();
		jQuery( '.insert-gallery' ).hide();
	} else if ( items.length > 1 ) {
		items.removeClass( 'open' );
		// Only show Gallery/Playlist buttons when there are at least two files.
		jQuery( '.insert-gallery' ).show();
	}

	// Only show Save buttons when there is at least one file.
	if ( items.not( '.media-blank' ).length > 0 )
		jQuery( '.savebutton' ).show();
	else
		jQuery( '.savebutton' ).hide();
}

function uploadSuccess( fileObj, serverData ) {
	var item = jQuery( '#media-item-' + fileObj.id );

	// on success serverData should be numeric, fix bug in html4 runtime returning the serverData wrapped in a <pre> tag
	if ( typeof serverData === 'string' ) {
		serverData = serverData.replace( /^<pre>(\d+)<\/pre>$/, '$1' );

		// if async-upload returned an error message, place it in the media item div and return
		if ( /media-upload-error|error-div/.test( serverData ) ) {
			item.html( serverData );
			return;
		}
	}

	item.find( '.percent' ).html( pluploadL10n.crunching );

	prepareMediaItem( fileObj, serverData );
	updateMediaForm();

	// Increment the counter.
	if ( post_id && item.hasClass( 'child-of-' + post_id ) ) {
		jQuery( '#attachments-count' ).text( 1 * jQuery( '#attachments-count' ).text() + 1 );
	}
}

function setResize( arg ) {
	if ( arg ) {
		if ( window.resize_width && window.resize_height ) {
			uploader.settings.resize = {
				enabled: true,
				width: window.resize_width,
				height: window.resize_height,
				quality: 100
			};
		} else {
			uploader.settings.multipart_params.image_resize = true;
		}
	} else {
		delete( uploader.settings.multipart_params.image_resize );
	}
}

function prepareMediaItem( fileObj, serverData ) {
	var f = ( typeof shortform == 'undefined' ) ? 1 : 2, item = jQuery( '#media-item-' + fileObj.id );
	if ( f == 2 && shortform > 2 )
		f = shortform;

	try {
		if ( typeof topWin.tb_remove != 'undefined' )
			topWin.jQuery( '#TB_overlay' ).click( topWin.tb_remove );
	} catch( e ){}

	if ( isNaN( serverData ) || !serverData ) { // Old style: Append the HTML returned by the server -- thumbnail and form inputs
		item.append( serverData );
		prepareMediaItemInit( fileObj );
	} else { // New style: server data is just the attachment ID, fetch the thumbnail and form html from the server
		item.load( 'async-upload.php', {attachment_id:serverData, fetch:f}, function(){prepareMediaItemInit( fileObj );updateMediaForm();});
	}
}

function prepareMediaItemInit( fileObj ) {
	var item = jQuery( '#media-item-' + fileObj.id );
	// Clone the thumbnail as a "pinkynail" -- a tiny image to the left of the filename
	jQuery( '.thumbnail', item ).clone().attr( 'class', 'pinkynail toggle' ).prependTo( item );

	// Replace the original filename with the new (unique) one assigned during upload
	jQuery( '.filename.original', item ).replaceWith( jQuery( '.filename.new', item ) );

	// Bind AJAX to the new Delete button
	jQuery( 'a.delete', item ).click( function(){
		// Tell the server to delete it. TODO: handle exceptions
		jQuery.ajax({
			url: ajaxurl,
			type: 'post',
			success: deleteSuccess,
			error: deleteError,
			id: fileObj.id,
			data: {
				id : this.id.replace(/[^0-9]/g, '' ),
				action : 'trash-post',
				_ajax_nonce : this.href.replace(/^.*wpnonce=/,'' )
			}
		});
		return false;
	});

	// Bind AJAX to the new Undo button
	jQuery( 'a.undo', item ).click( function(){
		// Tell the server to untrash it. TODO: handle exceptions
		jQuery.ajax({
			url: ajaxurl,
			type: 'post',
			id: fileObj.id,
			data: {
				id : this.id.replace(/[^0-9]/g,'' ),
				action: 'untrash-post',
				_ajax_nonce: this.href.replace(/^.*wpnonce=/,'' )
			},
			success: function( ){
				var type,
					item = jQuery( '#media-item-' + fileObj.id );

				if ( type = jQuery( '#type-of-' + fileObj.id ).val() )
					jQuery( '#' + type + '-counter' ).text( jQuery( '#' + type + '-counter' ).text()-0+1 );

				if ( post_id && item.hasClass( 'child-of-'+post_id ) )
					jQuery( '#attachments-count' ).text( jQuery( '#attachments-count' ).text()-0+1 );

				jQuery( '.filename .trashnotice', item ).remove();
				jQuery( '.filename .title', item ).css( 'font-weight','normal' );
				jQuery( 'a.undo', item ).addClass( 'hidden' );
				jQuery( '.menu_order_input', item ).show();
				item.css( {backgroundColor:'#ceb'} ).animate( {backgroundColor: '#fff'}, { queue: false, duration: 500, complete: function(){ jQuery( this ).css({backgroundColor:''}); } }).removeClass( 'undo' );
			}
		});
		return false;
	});

	// Open this item if it says to start open (e.g. to display an error)
	jQuery( '#media-item-' + fileObj.id + '.startopen' ).removeClass( 'startopen' ).addClass( 'open' ).find( 'slidetoggle' ).fadeIn();
}

// generic error message
function wpQueueError( message ) {
	jQuery( '#media-upload-error' ).show().html( '<div class="error"><p>' + message + '</p></div>' );
}

// file-specific error messages
function wpFileError( fileObj, message ) {
	itemAjaxError( fileObj.id, message );
}

function itemAjaxError( id, message ) {
	var item = jQuery( '#media-item-' + id ), filename = item.find( '.filename' ).text(), last_err = item.data( 'last-err' );

	if ( last_err == id ) // prevent firing an error for the same file twice
		return;

	item.html( '<div class="error-div">' +
				'<a class="dismiss" href="#">' + pluploadL10n.dismiss + '</a>' +
				'<strong>' + pluploadL10n.error_uploading.replace( '%s', jQuery.trim( filename )) + '</strong> ' +
				message +
				'</div>' ).data( 'last-err', id );
}

function deleteSuccess( data ) {
	var type, id, item;
	if ( data == '-1' )
		return itemAjaxError( this.id, 'You do not have permission. Has your session expired?' );

	if ( data == '0' )
		return itemAjaxError( this.id, 'Could not be deleted. Has it been deleted already?' );

	id = this.id;
	item = jQuery( '#media-item-' + id );

	// Decrement the counters.
	if ( type = jQuery( '#type-of-' + id ).val() )
		jQuery( '#' + type + '-counter' ).text( jQuery( '#' + type + '-counter' ).text() - 1 );

	if ( post_id && item.hasClass( 'child-of-'+post_id ) )
		jQuery( '#attachments-count' ).text( jQuery( '#attachments-count' ).text() - 1 );

	if ( jQuery( 'form.type-form #media-items' ).children().length == 1 && jQuery( '.hidden', '#media-items' ).length > 0 ) {
		jQuery( '.toggle' ).toggle();
		jQuery( '.slidetoggle' ).slideUp( 200 ).siblings().removeClass( 'hidden' );
	}

	// Vanish it.
	jQuery( '.toggle', item ).toggle();
	jQuery( '.slidetoggle', item ).slideUp( 200 ).siblings().removeClass( 'hidden' );
	item.css( {backgroundColor:'#faa'} ).animate( {backgroundColor:'#f4f4f4'}, {queue:false, duration:500} ).addClass( 'undo' );

	jQuery( '.filename:empty', item ).remove();
	jQuery( '.filename .title', item ).css( 'font-weight','bold' );
	jQuery( '.filename', item ).append( '<span class="trashnotice"> ' + pluploadL10n.deleted + ' </span>' ).siblings( 'a.toggle' ).hide();
	jQuery( '.filename', item ).append( jQuery( 'a.undo', item ).removeClass( 'hidden' ) );
	jQuery( '.menu_order_input', item ).hide();

	return;
}

function deleteError() {
}

function uploadComplete() {
	jQuery( '#insert-gallery' ).prop( 'disabled', false );
}

function switchUploader( s ) {
	if ( s ) {
		deleteUserSetting( 'uploader' );
		jQuery( '.media-upload-form' ).removeClass( 'html-uploader' );

		if ( typeof( uploader ) == 'object' )
			uploader.refresh();
	} else {
		setUserSetting( 'uploader', '1' ); // 1 == html uploader
		jQuery( '.media-upload-form' ).addClass( 'html-uploader' );
	}
}

function uploadError( fileObj, errorCode, message, up ) {
	var hundredmb = 100 * 1024 * 1024, max;

	switch ( errorCode ) {
		case plupload.FAILED:
			wpFileError( fileObj, pluploadL10n.upload_failed );
			break;
		case plupload.FILE_EXTENSION_ERROR:
			wpFileExtensionError( up, fileObj, pluploadL10n.invalid_filetype );
			break;
		case plupload.FILE_SIZE_ERROR:
			uploadSizeError( up, fileObj );
			break;
		case plupload.IMAGE_FORMAT_ERROR:
			wpFileError( fileObj, pluploadL10n.not_an_image );
			break;
		case plupload.IMAGE_MEMORY_ERROR:
			wpFileError( fileObj, pluploadL10n.image_memory_exceeded );
			break;
		case plupload.IMAGE_DIMENSIONS_ERROR:
			wpFileError( fileObj, pluploadL10n.image_dimensions_exceeded );
			break;
		case plupload.GENERIC_ERROR:
			wpQueueError( pluploadL10n.upload_failed );
			break;
		case plupload.IO_ERROR:
			max = parseInt( up.settings.filters.max_file_size, 10 );

			if ( max > hundredmb && fileObj.size > hundredmb ) {
				wpFileError( fileObj, pluploadL10n.big_upload_failed.replace( '%1$s', '<a class="uploader-html" href="#">' ).replace( '%2$s', '</a>' ) );
			} else {
				wpQueueError( pluploadL10n.io_error );
			}

			break;
		case plupload.HTTP_ERROR:
			wpQueueError( pluploadL10n.http_error );
			break;
		case plupload.INIT_ERROR:
			jQuery( '.media-upload-form' ).addClass( 'html-uploader' );
			break;
		case plupload.SECURITY_ERROR:
			wpQueueError( pluploadL10n.security_error );
			break;
/*		case plupload.UPLOAD_ERROR.UPLOAD_STOPPED:
		case plupload.UPLOAD_ERROR.FILE_CANCELLED:
			jQuery( '#media-item-' + fileObj.id ).remove();
			break;*/
		default:
			wpFileError( fileObj, pluploadL10n.default_error );
	}
}

function uploadSizeError( up, file ) {
	var message, errorDiv;

	message = pluploadL10n.file_exceeds_size_limit.replace( '%s', file.name );

	// Construct the error div.
	errorDiv = jQuery( '<div />' )
		.attr( {
			'id':    'media-item-' + file.id,
			'class': 'media-item error'
		} )
		.append(
			jQuery( '<p />' )
				.text( message )
		);

	// Append the error.
	jQuery( '#media-items' ).append( errorDiv );
	up.removeFile( file );
}

function wpFileExtensionError( up, file, message ) {
	jQuery( '#media-items' ).append( '<div id="media-item-' + file.id + '" class="media-item error"><p>' + message + '</p></div>' );
	up.removeFile( file );
}

jQuery( document ).ready( function( $ ) {
	var tryAgainCount = {};
	var tryAgain;

	$( '.media-upload-form' ).bind( 'click.uploader', function( e ) {
		var target = $( e.target ), tr, c;

		if ( target.is( 'input[type="radio"]' ) ) { // remember the last used image size and alignment
			tr = target.closest( 'tr' );

			if ( tr.hasClass( 'align' ) )
				setUserSetting( 'align', target.val() );
			else if ( tr.hasClass( 'image-size' ) )
				setUserSetting( 'imgsize', target.val() );

		} else if ( target.is( 'button.button' ) ) { // remember the last used image link url
			c = e.target.className || '';
			c = c.match( /url([^ '"]+)/ );

			if ( c && c[1] ) {
				setUserSetting( 'urlbutton', c[1] );
				target.siblings( '.urlfield' ).val( target.data( 'link-url' ) );
			}
		} else if ( target.is( 'a.dismiss' ) ) {
			target.parents( '.media-item' ).fadeOut( 200, function() {
				$( this ).remove();
			} );
		} else if ( target.is( '.upload-flash-bypass a' ) || target.is( 'a.uploader-html' ) ) { // switch uploader to html4
			$( '#media-items, p.submit, span.big-file-warning' ).css( 'display', 'none' );
			switchUploader( 0 );
			e.preventDefault();
		} else if ( target.is( '.upload-html-bypass a' ) ) { // switch uploader to multi-file
			$( '#media-items, p.submit, span.big-file-warning' ).css( 'display', '' );
			switchUploader( 1 );
			e.preventDefault();
		} else if ( target.is( 'a.describe-toggle-on' ) ) { // Show
			target.parent().addClass( 'open' );
			target.siblings( '.slidetoggle' ).fadeIn( 250, function() {
				var S = $( window ).scrollTop(),
					H = $( window ).height(),
					top = $( this ).offset().top,
					h = $( this ).height(),
					b,
					B;

				if ( H && top && h ) {
					b = top + h;
					B = S + H;

					if ( b > B ) {
						if ( b - B < top - S )
							window.scrollBy( 0, ( b - B ) + 10 );
						else
							window.scrollBy( 0, top - S - 40 );
					}
				}
			} );

			e.preventDefault();
		} else if ( target.is( 'a.describe-toggle-off' ) ) { // Hide
			target.siblings( '.slidetoggle' ).fadeOut( 250, function() {
				target.parent().removeClass( 'open' );
			} );

			e.preventDefault();
		}
	});

	// Attempt to create image sub-sizes when an image was uploaded successfully
	// but the server responded with HTTP 500 error.
	tryAgain = function( up, error ) {
		var file = error.file;
		var times;

		if ( ! file || ! file.id ) {
			wpQueueError( error.message || pluploadL10n.default_error );
			return;
		}

		times = tryAgainCount[ file.id ];

		if ( times && times > 4 ) {
			// The file may have been uploaded and attachment post created,
			// but post-processing and resizing failed...
			// Do a cleanup then tell the user to scale down the image and upload it again.
			$.ajax({
				type: 'post',
				url: ajaxurl,
				dataType: 'json',
				data: {
					action: 'media-create-image-subsizes',
					_wpnonce: _wpPluploadSettings.defaults.multipart_params._wpnonce,
					_wp_temp_image_ref: file.id,
					_wp_upload_failed_cleanup: true,
				}
			});

			if ( error.message && error.status !== 500 ) {
				wpQueueError( error.message );
			} else {
				wpQueueError( pluploadL10n.http_error_image );
			}

			return;
		}

		if ( ! times ) {
			tryAgainCount[ file.id ] = 1;
		} else {
			tryAgainCount[ file.id ] = ++times;
		}

		// Try to create the missing image sizes.
		$.ajax({
			type: 'post',
			url: ajaxurl,
			dataType: 'json',
			data: {
				action: 'media-create-image-subsizes',
				_wpnonce: wpUploaderInit.multipart_params._wpnonce,
				_wp_temp_image_ref: file.id,
				_legasy_support: 'true',
			}
		}).done( function( response ) {
			var message;

			if ( response.success ) {
				uploadSuccess( file, response.data.id );
			} else {
				if ( response.data && response.data.message ) {
					message = response.data.message;
				}

				wpQueueError( message || pluploadL10n.http_error_image );
			}
		}).fail( function( jqXHR ) {
			// If another HTTP 500 error, try try again...
			if ( jqXHR.status === 500 ) {
				tryAgain( up, error );
				return;
			}

			wpQueueError( pluploadL10n.http_error_image );
		});
	}

	// init and set the uploader
	uploader_init = function() {
		uploader = new plupload.Uploader( wpUploaderInit );

		$( '#image_resize' ).bind( 'change', function() {
			var arg = $( this ).prop( 'checked' );

			setResize( arg );

			if ( arg )
				setUserSetting( 'upload_resize', '1' );
			else
				deleteUserSetting( 'upload_resize' );
		});

		uploader.bind( 'Init', function( up ) {
			var uploaddiv = $( '#plupload-upload-ui' );

			setResize( getUserSetting( 'upload_resize', false ) );

			if ( up.features.dragdrop && ! $( document.body ).hasClass( 'mobile' ) ) {
				uploaddiv.addClass( 'drag-drop' );

				$( '#drag-drop-area' ).on( 'dragover.wp-uploader', function() { // dragenter doesn't fire right :(
					uploaddiv.addClass( 'drag-over' );
				}).on( 'dragleave.wp-uploader, drop.wp-uploader', function() {
					uploaddiv.removeClass( 'drag-over' );
				});
			} else {
				uploaddiv.removeClass( 'drag-drop' );
				$( '#drag-drop-area' ).off( '.wp-uploader' );
			}

			if ( up.runtime === 'html4' ) {
				$( '.upload-flash-bypass' ).hide();
			}
		});

		uploader.bind( 'postinit', function( up ) {
			up.refresh();
		});

		uploader.init();

		uploader.bind( 'FilesAdded', function( up, files ) {
			$( '#media-upload-error' ).empty();
			uploadStart();

			plupload.each( files, function( file ) {
				fileQueued( file );
			});

			up.refresh();
			up.start();
		});

		uploader.bind( 'UploadFile', function( up, file ) {
			fileUploading( up, file );
		});

		uploader.bind( 'UploadProgress', function( up, file ) {
			uploadProgress( up, file );
		});

		uploader.bind( 'Error', function( up, error ) {
			var isImage = error.file && error.file.type && error.file.type.indexOf( 'image/' ) === 0;
			var status  = error && error.status;

			// If the file is an image and the error is HTTP 500 try to create sub-sizes again.
			if ( status === 500 && isImage ) {
				tryAgain( up, error );
				return;
			}

			uploadError( error.file, error.code, error.message, up );
			up.refresh();
		});

		uploader.bind( 'FileUploaded', function( up, file, response ) {
			uploadSuccess( file, response.response );
		});

		uploader.bind( 'UploadComplete', function() {
			uploadComplete();
		});

		/**
		 * When uploading images add a file reference used to retrieve the attachment_id
		 * if the uploading fails due to a server timeout of out of memoty (HTTP 500) error.
		 *
		 * @param {plupload.Uploader} up   Uploader instance.
		 * @param {plupload.File}     file File for uploading.
		 */
		uploader.bind( 'BeforeUpload', function( up, file ) {
			if ( file.type && file.type.indexOf( 'image/' ) === 0 ) {
				up.settings.multipart_params._wp_temp_image_ref = file.id;
			} else {
				up.settings.multipart_params._wp_temp_image_ref = '';
			}
		} );
	};

	if ( typeof( wpUploaderInit ) == 'object' ) {
		uploader_init();
	}

});
