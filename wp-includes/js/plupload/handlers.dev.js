var topWin = window.dialogArguments || opener || parent || top, uploader, uploader_init;

function fileDialogStart() {
	jQuery("#media-upload-error").empty();
}

// progress and success handlers for media multi uploads
function fileQueued(fileObj) {
	// Get rid of unused form
	jQuery('.media-blank').remove();
	// Collapse a single item
	if ( jQuery('form.type-form #media-items').children().length == 1 && jQuery('.hidden', '#media-items').length > 0 ) {
		jQuery('.describe-toggle-on').show();
		jQuery('.describe-toggle-off').hide();
		jQuery('.slidetoggle').slideUp(200).siblings().removeClass('hidden');
	}
	// Create a progress bar containing the filename
	jQuery('#media-items').append('<div id="media-item-' + fileObj.id + '" class="media-item child-of-' + post_id + '"><div class="progress"><div class="bar"></div></div><div class="filename original"><span class="percent"></span> ' + fileObj.name + '</div></div>');
	// Display the progress div
	jQuery('.progress', '#media-item-' + fileObj.id).show();

	// Disable submit and enable cancel
	jQuery('#insert-gallery').prop('disabled', true);
	jQuery('#cancel-upload').prop('disabled', false);
}

function uploadStart(fileObj) {
	try {
		if ( typeof topWin.tb_remove != 'undefined' )
			topWin.jQuery('#TB_overlay').unbind('click', topWin.tb_remove); 
	} catch(e){}

	return true;
}

function uploadProgress(fileObj, bytesDone, bytesTotal) { // Lengthen the progress bar
	var w = jQuery('#media-items').width() - 2, item = jQuery('#media-item-' + fileObj.id);

	jQuery('.bar', item).width( w * bytesDone / bytesTotal );
	jQuery('.percent', item).html( Math.ceil(bytesDone / bytesTotal * 100) + '%' );

	if ( bytesDone == bytesTotal )
		jQuery('.bar', item).html('<strong class="crunching">' + pluploadL10n.crunching + '</strong>');
}

function updateMediaForm() {
	var one = jQuery('form.type-form #media-items').children(), items = jQuery('#media-items').children();

	// Just one file, no need for collapsible part
	if ( one.length == 1 ) {
		jQuery('.slidetoggle', one).slideDown(500).siblings().addClass('hidden').filter('.toggle').toggle();
	}

	// Only show Save buttons when there is at least one file.
	if ( items.not('.media-blank').length > 0 )
		jQuery('.savebutton').show();
	else
		jQuery('.savebutton').hide();

	// Only show Gallery button when there are at least two files.
	if ( items.length > 1 )
		jQuery('.insert-gallery').show();
	else
		jQuery('.insert-gallery').hide();
}

function uploadSuccess(fileObj, serverData) {
	// if async-upload returned an error message, place it in the media item div and return
	if ( serverData.match('media-upload-error') ) {
		jQuery('#media-item-' + fileObj.id).html(serverData);
		return;
	}

	prepareMediaItem(fileObj, serverData);
	updateMediaForm();

	// Increment the counter.
	if ( jQuery('#media-item-' + fileObj.id).hasClass('child-of-' + post_id) )
		jQuery('#attachments-count').text(1 * jQuery('#attachments-count').text() + 1);
}

function setResize(arg) {
	if ( arg ) {
		if ( uploader.features.jpgresize )
			uploader.settings['resize'] = { width: resize_width, height: resize_height, quality: 100 };
		else
			uploader.settings.multipart_params.image_resize = true;
	} else {
		delete(uploader.settings.resize);
		delete(uploader.settings.multipart_params.image_resize);
	}
}

function prepareMediaItem(fileObj, serverData) {
	var f = ( typeof shortform == 'undefined' ) ? 1 : 2, item = jQuery('#media-item-' + fileObj.id);
	// Move the progress bar to 100%
	jQuery('.bar', item).remove();
	jQuery('.progress', item).hide();

	try {
		if ( typeof topWin.tb_remove != 'undefined' )
			topWin.jQuery('#TB_overlay').click(topWin.tb_remove);
	} catch(e){}

	// Old style: Append the HTML returned by the server -- thumbnail and form inputs
	if ( isNaN(serverData) || !serverData ) {
		item.append(serverData);
		prepareMediaItemInit(fileObj);
	}
	// New style: server data is just the attachment ID, fetch the thumbnail and form html from the server
	else {
		item.load('async-upload.php', {attachment_id:serverData, fetch:f}, function(){prepareMediaItemInit(fileObj);updateMediaForm()});
	}
}

function prepareMediaItemInit(fileObj) {
	var item = jQuery('#media-item-' + fileObj.id);
	// Clone the thumbnail as a "pinkynail" -- a tiny image to the left of the filename
	jQuery('.thumbnail', item).clone().attr('class', 'pinkynail toggle').prependTo(item);

	// Replace the original filename with the new (unique) one assigned during upload
	jQuery('.filename.original', item).replaceWith( jQuery('.filename.new', item) );

	// Also bind toggle to the links
	jQuery('a.toggle', item).click(function(){
		jQuery(this).siblings('.slidetoggle').slideToggle(350, function(){
			var w = jQuery(window).height(), t = jQuery(this).offset().top, h = jQuery(this).height(), b;

			if ( w && t && h ) {
                b = t + h;

                if ( b > w && (h + 48) < w )
                    window.scrollBy(0, b - w + 13);
                else if ( b > w )
                    window.scrollTo(0, t - 36);
            }
		});
		jQuery(this).siblings('.toggle').andSelf().toggle();
		jQuery(this).siblings('a.toggle').focus();
		return false;
	});

	// Bind AJAX to the new Delete button
	jQuery('a.delete', item).click(function(){
		// Tell the server to delete it. TODO: handle exceptions
		jQuery.ajax({
			url: 'admin-ajax.php',
			type: 'post',
			success: deleteSuccess,
			error: deleteError,
			id: fileObj.id,
			data: {
				id : this.id.replace(/[^0-9]/g, ''),
				action : 'trash-post',
				_ajax_nonce : this.href.replace(/^.*wpnonce=/,'')
			}
		});
		return false;
	});

	// Bind AJAX to the new Undo button
	jQuery('a.undo', item).click(function(){
		// Tell the server to untrash it. TODO: handle exceptions
		jQuery.ajax({
			url: 'admin-ajax.php',
			type: 'post',
			id: fileObj.id,
			data: {
				id : this.id.replace(/[^0-9]/g,''),
				action: 'untrash-post',
				_ajax_nonce: this.href.replace(/^.*wpnonce=/,'')
			},
			success: function(data, textStatus){
				var item = jQuery('#media-item-' + fileObj.id);

				if ( type = jQuery('#type-of-' + fileObj.id).val() )
					jQuery('#' + type + '-counter').text(jQuery('#' + type + '-counter').text()-0+1);
				if ( item.hasClass('child-of-'+post_id) )
					jQuery('#attachments-count').text(jQuery('#attachments-count').text()-0+1);

				jQuery('.filename .trashnotice', item).remove();
				jQuery('.filename .title', item).css('font-weight','normal');
				jQuery('a.undo', item).addClass('hidden');
				jQuery('a.describe-toggle-on, .menu_order_input', item).show();
				item.css( {backgroundColor:'#ceb'} ).animate( {backgroundColor: '#fff'}, { queue: false, duration: 500, complete: function(){ jQuery(this).css({backgroundColor:''}); } }).removeClass('undo');
			}
		});
		return false;
	});

	// Open this item if it says to start open (e.g. to display an error)
	jQuery('#media-item-' + fileObj.id + '.startopen').removeClass('startopen').slideToggle(500).siblings('.toggle').toggle();
}

function cancelUpload() {
	uploader.stop();
	jQuery.each(uploader.files, function(i,file) {
		if (file.status == plupload.STOPPED)
			jQuery('#media-item-' + file.id).remove();
	});
}


// wp-specific error handlers

// generic message
function wpQueueError(message) {
	jQuery('#media-upload-error').show().text(message);
}

// file-specific message
function wpFileError(fileObj, message) {
	var item = jQuery('#media-item-' + fileObj.id), filename = jQuery('.filename', item).text();

	item.html('<div class="error-div">'
				+ '<a class="dismiss" href="#">' + pluploadL10n.dismiss + '</a>'
				+ '<strong>' + pluploadL10n.error_uploading.replace('%s', filename) + '</strong><br />'
				+ message
				+ '</div>');
	item.find('a.dismiss').click(function(){jQuery(this).parents('.media-item').slideUp(200, function(){jQuery(this).remove();})});
}

function itemAjaxError(id, html) {
	var item = jQuery('#media-item-' + id), filename = jQuery('.filename', item).text();

	item.html('<div class="error-div">'
				+ '<a class="dismiss" href="#">' + pluploadL10n.dismiss + '</a>'
				+ '<strong>' + pluploadL10n.error_uploading.replace('%s', filename) + '</strong><br />'
				+ html
				+ '</div>');
	item.find('a.dismiss').click(function(){jQuery(this).parents('.media-item').slideUp(200, function(){jQuery(this).remove();})});
}

function deleteSuccess(data, textStatus) {
	if ( data == '-1' )
		return itemAjaxError(this.id, 'You do not have permission. Has your session expired?');
	if ( data == '0' )
		return itemAjaxError(this.id, 'Could not be deleted. Has it been deleted already?');

	var id = this.id, item = jQuery('#media-item-' + id);

	// Decrement the counters.
	if ( type = jQuery('#type-of-' + id).val() )
		jQuery('#' + type + '-counter').text( jQuery('#' + type + '-counter').text() - 1 );
	if ( item.hasClass('child-of-'+post_id) )
		jQuery('#attachments-count').text( jQuery('#attachments-count').text() - 1 );

	if ( jQuery('form.type-form #media-items').children().length == 1 && jQuery('.hidden', '#media-items').length > 0 ) {
		jQuery('.toggle').toggle();
		jQuery('.slidetoggle').slideUp(200).siblings().removeClass('hidden');
	}

	// Vanish it.
	jQuery('.toggle', item).toggle();
	jQuery('.slidetoggle', item).slideUp(200).siblings().removeClass('hidden');
	item.css( {backgroundColor:'#faa'} ).animate( {backgroundColor:'#f4f4f4'}, {queue:false, duration:500} ).addClass('undo');

	jQuery('.filename:empty', item).remove();
	jQuery('.filename .title', item).css('font-weight','bold');
	jQuery('.filename', item).append('<span class="trashnotice"> ' + pluploadL10n.deleted + ' </span>').siblings('a.toggle').hide();
	jQuery('.filename', item).append( jQuery('a.undo', item).removeClass('hidden') );
	jQuery('.menu_order_input', item).hide();

	return;
}

function deleteError(X, textStatus, errorThrown) {
	// TODO
}

// SWFUpload?
function uploadComplete(fileObj) {
	// If no more uploads queued, enable the submit button
	if ( swfu.getStats().files_queued == 0 ) {
		jQuery('#cancel-upload').prop('disabled', true);
		jQuery('#insert-gallery').prop('disabled', false);
	}
}

function switchUploader(s) {
	var p = document.getElementById('flash-upload-ui'), h = document.getElementById('html-upload-ui');

	if ( s ) {
		p.style.display = 'block';
		h.style.display = 'none';
	} else {
		p.style.display = 'none';
		h.style.display = 'block';
	}
}

function dndHelper(s) {
	var d = document.getElementById('dnd-helper');

	if ( s ) {
		d.style.display = 'block';
	} else {
		d.style.display = 'none';
	}
}

// SWFUpload?
function swfuploadPreLoad() {
	if ( !uploaderMode ) {
		switchUploader(1);
	} else {
		switchUploader(0);
	}
}

// SWFUpload?
function swfuploadLoadFailed() {
	switchUploader(0);
	jQuery('.upload-html-bypass').hide();
}

function uploadError(fileObj, errorCode, message) {

	switch (errorCode) {
		case plupload.FAILED:
			wpFileError(fileObj, pluploadL10n.upload_failed);
			break;
		case plupload.FILE_EXTENSION_ERROR:
			wpFileError(fileObj, pluploadL10n.invalid_filetype);
			break;
		case plupload.FILE_SIZE_ERROR:
			wpFileError(fileObj, pluploadL10n.upload_limit_exceeded);
			break;
		case plupload.IMAGE_FORMAT_ERROR:
			wpFileError(fileObj, pluploadL10n.not_an_image);
			break;
		case plupload.IMAGE_MEMORY_ERROR:
			wpFileError(fileObj, pluploadL10n.image_memory_exceeded);
			break;
		case plupload.IMAGE_DIMENSIONS_ERROR:
			wpFileError(fileObj, pluploadL10n.image_dimensions_exceeded);
			break;
		case plupload.GENERIC_ERROR:
			wpQueueError(pluploadL10n.upload_failed);
			break;
		case plupload.IO_ERROR:
			wpQueueError(pluploadL10n.io_error);
			break;
		case plupload.HTTP_ERROR:
			wpQueueError(pluploadL10n.http_error);
			break;
		case plupload.INIT_ERROR:
			switchUploader(0);
			jQuery('.upload-html-bypass').hide();
			break;
		case plupload.SECURITY_ERROR:
			wpQueueError(pluploadL10n.security_error);
			break;
/*		case plupload.UPLOAD_ERROR.UPLOAD_STOPPED:
		case plupload.UPLOAD_ERROR.FILE_CANCELLED:
			jQuery('#media-item-' + fileObj.id).remove();
			break;*/
		default:
			wpFileError(fileObj, pluploadL10n.default_error);
	}
}

jQuery(document).ready(function($){
	// remember the last used image size, alignment and url
	$('input[type="radio"]', '#media-items').live('click', function(){
		var tr = $(this).closest('tr');

		if ( $(tr).hasClass('align') )
			setUserSetting('align', $(this).val());
		else if ( $(tr).hasClass('image-size') )
			setUserSetting('imgsize', $(this).val());
	});

	$('button.button', '#media-items').live('click', function(){
		var c = this.className || '';
		c = c.match(/url([^ '"]+)/);
		if ( c && c[1] ) {
			setUserSetting('urlbutton', c[1]);
			$(this).siblings('.urlfield').val( $(this).attr('title') );
		}
	});

	// init and set the uploader
	uploader_init = function() {
		uploader = new plupload.Uploader(wpUploaderInit);

		$('#image_resize').bind('change', function() {
			var arg = $(this).prop('checked');

			setResize( arg );

			if ( arg )
				setUserSetting('upload_resize', '1');
			else
				deleteUserSetting('upload_resize');
		});

		uploader.bind('Init', function(up) {
			setResize( getUserSetting('upload_resize', false) );

			if ( !up.features.dragdrop )
				$('#plupload-upload-ui').removeClass('drag-drop');
		});

		uploader.init();

		uploader.bind('FilesAdded', function(up, files) {
			$.each(files, function(i, file) {
				/*
				if ( up.features.chunks && up.runtime != 'flash' && file.size > 1048576 )
					up.settings.chunk_size = '1048576';
				else
					delete(up.settings.chunk_size);
				*/

				fileQueued(file);
			});

			up.refresh();
			up.start();
		});

		uploader.bind('BeforeUpload', function(up, file) {
			uploadStart(file);
		});
		
		uploader.bind('UploadProgress', function(up, file) {
			uploadProgress(file, file.loaded, file.size);
		});

		uploader.bind('Error', function(up, err) {
			uploadError(err.file, err.code, err.message);

			up.refresh();
		});

		uploader.bind('FileUploaded', function(up, file, response) {
			uploadSuccess(file, response.response);
		});
	}

	if ( typeof(wpUploaderInit) == 'object' )
		uploader_init();

});
