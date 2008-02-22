function uploadLoaded() {
	jQuery("#html-upload-ui").remove();
	jQuery("#flash-upload-ui").show();
}

function fileDialogStart() {
	jQuery("#media-upload-error").empty();
}

// progress and success handlers for multimedia multi uploads
function fileQueued(fileObj) {
	// Create a progress bar containing the filename
	jQuery('#multimedia-items').append('<div id="multimedia-item-' + fileObj.id + '" class="multimedia-item"><span class="filename original">' + fileObj.name + '</span><div class="progress"><div class="bar"></div></div></div>');

	// Disable the submit button
	jQuery('#insert-multimedia').attr('disabled', 'disabled');
}

function uploadStart(fileObj) { return true; }

function uploadProgress(fileObj, bytesDone, bytesTotal) {
	// Lengthen the progress bar
	jQuery('#multimedia-item-' + fileObj.id + ' .bar').width(620*bytesDone/bytesTotal);
}

function uploadSuccess(fileObj, serverData) {
	// if async-upload returned an error message, place it in the multimedia item div and return
	if ( serverData.match('media-upload-error') ) {
		jQuery('#multimedia-item-' + fileObj.id).html(serverData);
		return;
	}

	// Move the progress bar to 100%
	jQuery('#multimedia-item-' + fileObj.id + ' .bar').remove();

	// Append the HTML returned by the server -- thumbnail and form inputs
	jQuery('#multimedia-item-' + fileObj.id).append(serverData);

	// Clone the thumbnail as a "pinkynail" -- a tiny image to the left of the filename
	jQuery('#multimedia-item-' + fileObj.id + ' .thumbnail').clone().attr('className', 'pinkynail toggle').prependTo('#multimedia-item-' + fileObj.id);

	// Replace the original filename with the new (unique) one assigned during upload
	jQuery('#multimedia-item-' + fileObj.id + ' .filename.original').replaceWith(jQuery('#multimedia-item-' + fileObj.id + ' .filename.new'));

	// Bind toggle function to a new mask over the progress bar area
	jQuery('#multimedia-item-' + fileObj.id + ' .progress').clone().empty().addClass('clickmask').bind('click', function(){jQuery(this).siblings('.slidetoggle').slideToggle(150);jQuery(this).siblings('.toggle').toggle();}).appendTo('#multimedia-item-' + fileObj.id);

	// Also bind toggle to the links
	jQuery('#multimedia-item-' + fileObj.id + ' a.toggle').bind('click', function(){jQuery(this).siblings('.slidetoggle').slideToggle(150);jQuery(this).parent().eq(0).children('.toggle').toggle();jQuery(this).siblings('a.toggle').focus();return false;});

	// Bind AJAX to the new Delete button
	jQuery('#multimedia-item-' + fileObj.id + ' a.delete').bind('click',function(){jQuery.ajax({url:'admin-ajax.php',type:'post',data:{id:this.id.replace(/[^0-9]/g,''),action:'delete-post',_ajax_nonce:this.href.replace(/^.*wpnonce=/,'')}});jQuery(this).parents(".multimedia-item").eq(0).slideToggle(300, function(){jQuery(this).remove();if(jQuery('.multimedia-item').length==0)jQuery('.insert-gallery').hide();});return false;});

	// Open this item if it says to start open
	jQuery('#multimedia-item-' + fileObj.id + ' .startopen')
		.removeClass('startopen')
		.slideToggle(500)
		.parent().eq(0).children('.toggle').toggle();

	jQuery('.insert-gallery').show();
}

function uploadComplete(fileObj) {
	// If no more uploads queued, enable the submit button
	if ( swfu.getStats().files_queued == 0 )
		jQuery('#insert-multimedia').attr('disabled', '');
}


// wp-specific error handlers

// generic message
function wpQueueError(message) {
	jQuery('#media-upload-error').show().text(message);
}

// file-specific message
function wpFileError(fileObj, message) {
	jQuery('#media-upload-error-' + fileObj.id).show().text(message);
}

function fileQueueError(fileObj, error_code, message)  {
	// Handle this error separately because we don't want to create a FileProgress element for it.
	if ( error_code == SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED ) {
		wpQueueError(swfuploadL10n.queue_limit_exceeded);
	}
	else if ( error_code == SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT ) {
		wpQueueError(swfuploadL10n.file_exceeds_size_limit);
	}
	else if ( error_code == SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE ) {
		wpQueueError(swfuploadL10n.zero_byte_file);
	}
	else if ( error_code == SWFUpload.QUEUE_ERROR.INVALID_FILETYPE ) {
		wpQueueError(swfuploadL10n.invalid_filetype);
	}
	else {
		wpQueueError(swfuploadL10n.default_error);
	}
}

function fileDialogComplete(num_files_queued) {
	try {
		if (num_files_queued > 0) {
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(fileObj, error_code, message) {
alert(message);return;
	// first the file specific error
	if ( error_code == SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL ) {
		wpFileError(fileObj, swfuploadL10n.missing_upload_url);
	}
	else if ( error_code == SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED ) {
		wpFileError(fileObj, swfuploadL10n.upload_limit_exceeded);
	}
	else {
		wpFileError(fileObj, swfuploadL10n.default_error);
	}

	// now the general upload status
	if ( error_code == SWFUpload.UPLOAD_ERROR.HTTP_ERROR ) {
		wpQueueError(swfuploadL10n.http_error);
	}
	else if ( error_code == SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED ) {
		wpQueueError(swfuploadL10n.upload_failed);
	}
	else if ( error_code == SWFUpload.UPLOAD_ERROR.IO_ERROR ) {
		wpQueueError(swfuploadL10n.io_error);
	}
	else if ( error_code == SWFUpload.UPLOAD_ERROR.SECURITY_ERROR ) {
		wpQueueError(swfuploadL10n.security_error);
	}
	else if ( error_code == SWFUpload.UPLOAD_ERROR.FILE_CANCELLED ) {
		wpQueueError(swfuploadL10n.security_error);
	}
}
