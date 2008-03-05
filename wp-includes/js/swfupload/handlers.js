function uploadLoaded() {
	jQuery("#html-upload-ui").remove();
	jQuery("#flash-upload-ui").show();
}

function fileDialogStart() {
	jQuery("#media-upload-error").empty();
}

// progress and success handlers for media multi uploads
function fileQueued(fileObj) {
	// Get rid of unused form
	jQuery('.media-blank').remove();
	// Collapse a single item
	if ( jQuery('.type-form #media-items>*').length == 1 && jQuery('#media-items .hidden').length > 0 ) {
		jQuery('.toggle').toggle();
		jQuery('.slidetoggle').slideUp(200).siblings().removeClass('hidden');
	}
	// Create a progress bar containing the filename
	jQuery('#media-items').append('<div id="media-item-' + fileObj.id + '" class="media-item child-of-' + post_id + '"><div class="filename original">' + fileObj.name + '</div><div class="progress"><div class="bar"></div></div></div>');

	// Disable the submit button
	jQuery('#insert-media').attr('disabled', 'disabled');
}

function uploadStart(fileObj) { return true; }

function uploadProgress(fileObj, bytesDone, bytesTotal) {
	// Lengthen the progress bar
	jQuery('#media-item-' + fileObj.id + ' .bar').width(620*bytesDone/bytesTotal);
}

function prepareMediaItem(fileObj, serverData) {
	// Move the progress bar to 100%
	jQuery('#media-item-' + fileObj.id + ' .bar').remove();

	// Append the HTML returned by the server -- thumbnail and form inputs
	jQuery('#media-item-' + fileObj.id).append(serverData);

	// Clone the thumbnail as a "pinkynail" -- a tiny image to the left of the filename
	jQuery('#media-item-' + fileObj.id + ' .thumbnail').clone().attr('className', 'pinkynail toggle').prependTo('#media-item-' + fileObj.id);

	// Replace the original filename with the new (unique) one assigned during upload
	jQuery('#media-item-' + fileObj.id + ' .filename.original').replaceWith(jQuery('#media-item-' + fileObj.id + ' .filename.new'));

	// Bind toggle function to a new mask over the progress bar area
	jQuery('#media-item-' + fileObj.id + ' .progress').clone().empty().addClass('clickmask').bind('click', function(){jQuery(this).siblings('.slidetoggle').slideToggle(150);jQuery(this).siblings('.toggle').toggle();}).appendTo('#media-item-' + fileObj.id);

	// Also bind toggle to the links
	jQuery('#media-item-' + fileObj.id + ' a.toggle').bind('click', function(){jQuery(this).siblings('.slidetoggle').slideToggle(150);jQuery(this).parent().eq(0).children('.toggle').toggle();jQuery(this).siblings('a.toggle').focus();return false;});

	// Bind AJAX to the new Delete button
	jQuery('#media-item-' + fileObj.id + ' a.delete').bind('click',function(){
		// Tell the server to delete it. TODO: handle exceptions
		jQuery.ajax({url:'admin-ajax.php',type:'post',data:{
			id : this.id.replace(/[^0-9]/g,''),
			action : 'delete-post',
			_ajax_nonce : this.href.replace(/^.*wpnonce=/,'')}
			});

		// Decrement the counters.
		if ( type = jQuery('#type-of-' + this.id.replace(/[^0-9]/g,'')).val() )
			jQuery('#' + type + '-counter').text(jQuery('#' + type + '-counter').text()-1);
		if ( jQuery(this).parents('.media-item').eq(0).hasClass('child-of-'+post_id) )
			jQuery('#attachments-count').text(jQuery('#attachments-count').text()-1);

		// Vanish it.
		jQuery(this).parents(".media-item").eq(0).slideToggle(300,function(){jQuery(this).remove();if(jQuery('.media-item').length==0)jQuery('.insert-gallery').hide();updateMediaForm();});
		return false;
	});

	// Open this item if it says to start open (e.g. to display an error)
	jQuery('#media-item-' + fileObj.id + '.startopen')
		.removeClass('startopen')
		.slideToggle(500)
		.parent().eq(0).children('.toggle').toggle();
}

function updateMediaForm() {
	// Just one file, no need for collapsible part
	if ( jQuery('.type-form #media-items>*').length == 1 ) {
		jQuery('#media-items .slidetoggle').slideDown(500).parent().eq(0).children('.toggle').toggle();
		jQuery('.type-form .slidetoggle').siblings().addClass('hidden');
	}

	// Only show Gallery button when there are at least two files.
	if ( jQuery('#media-items>*').length > 1 )
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

function uploadComplete(fileObj) {
	// If no more uploads queued, enable the submit button
	if ( swfu.getStats().files_queued == 0 )
		jQuery('#insert-media').attr('disabled', '');
}


// wp-specific error handlers

// generic message
function wpQueueError(message) {
	jQuery('#media-upload-error').show().text(message);
}

// file-specific message
function wpFileError(fileObj, message) {
	jQuery('#media-item-' + fileObj.id + ' .filename').after('<div class="file-error"><button type="button" class="button dismiss">'+swfuploadL10n.dismiss+'</button>'+message+'</div>').siblings('.progress').remove();
	jQuery('.dismiss').click(function(){jQuery(this).parents('.media-item').slideUp(200, function(){jQuery(this).remove();})});
}

function fileQueueError(fileObj, error_code, message)  {
	// Handle this error separately because we don't want to create a FileProgress element for it.
	if ( error_code == SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED ) {
		wpQueueError(swfuploadL10n.queue_limit_exceeded);
	}
	else if ( error_code == SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT ) {
		fileQueued(fileObj);
		wpFileError(fileObj, swfuploadL10n.file_exceeds_size_limit);
	}
	else if ( error_code == SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE ) {
		fileQueued(fileObj);
		wpFileError(fileObj, swfuploadL10n.zero_byte_file);
	}
	else if ( error_code == SWFUpload.QUEUE_ERROR.INVALID_FILETYPE ) {
		fileQueued(fileObj);
		wpFileError(fileObj, swfuploadL10n.invalid_filetype);
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
