function uploadLoadedMultimedia() {
	jQuery("#html-upload-ui").empty();
}

function fileDialogStart() {
	jQuery("#media-upload-error").empty();
}

// progress and success handlers for multimedia multi uploads
function fileQueuedMultimedia(fileObj) {
	// Create a progress bar containing the filename
	jQuery('#multimedia-items').append('<div id="multimedia-item-' + fileObj.id + '" class="multimedia-item"><span class="filename original">' + fileObj.name + '</span><div class="progress"><div class="bar"></div></div></div>');

	// Disable the submit button
	jQuery('#insert-multimedia').attr('disabled', 'disabled');
}

function uploadProgressMultimedia(fileObj, bytesDone, bytesTotal) {
	// Lengthen the progress bar
	jQuery('#multimedia-item-' + fileObj.id + ' .bar').width(620*bytesDone/bytesTotal);
}

function uploadSuccessMultimedia(fileObj, serverData) {
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
	jQuery('#multimedia-item-' + fileObj.id + ' .thumbnail').clone().attr('className', 'pinkynail').prependTo('#multimedia-item-' + fileObj.id);

	// Replace the original filename with the new (unique) one assigned during upload
	jQuery('#multimedia-item-' + fileObj.id + ' .filename.original').replaceWith(jQuery('#multimedia-item-' + fileObj.id + ' .filename.new'));

	// Bind toggle function to a new mask over the progress bar area
	jQuery('#multimedia-item-' + fileObj.id + ' .progress').clone().empty().addClass('clickmask').bind('click', function(){jQuery(this).siblings('.slidetoggle').slideToggle(150);jQuery(this).siblings('.toggle').toggle();}).appendTo('#multimedia-item-' + fileObj.id);

	// Also bind toggle to the links
	jQuery('#multimedia-item-' + fileObj.id + ' a.toggle').bind('click', function(){jQuery(this).siblings('.slidetoggle').slideToggle(150);jQuery(this).parent().eq(0).children('.toggle').toggle();jQuery(this).siblings('a.toggle').focus();return false;});

	// Bind AJAX to the new Delete button
	jQuery('#multimedia-item-' + fileObj.id + ' a.delete').bind('click',function(){jQuery.ajax({url:'admin-ajax.php',type:'post',data:{id:this.id.replace(/[^0-9]/g,''),action:'delete-post',_ajax_nonce:this.href.replace(/^.*wpnonce=/,'')}});jQuery(this).parents(".multimedia-item").eq(0).slideToggle(300, function(){jQuery(this).remove();});return false;});

	// Open this item if it says to start open
	jQuery('#multimedia-item-' + fileObj.id + ' .startopen')
		.removeClass('startopen')
		.slideToggle(500)
		.parent().eq(0).children('.toggle').toggle();
}

function uploadCompleteMultimedia(fileObj) {
	// If no more uploads queued, enable the submit button
	if ( swfu.getStats().files_queued == 0 )
		jQuery('#insert-multimedia').attr('disabled', '');
}


// progress and success handlers for single image upload

function uploadLoadedImage() {
	jQuery('#image-alt').attr('disabled', true);
	jQuery('#image-url').attr('disabled', true);
	jQuery('#image-title').attr('disabled', true);
	jQuery('#image-add').attr('disabled', true);
}

function fileQueuedImage(fileObj) {
	jQuery('#flash-upload-ui').append('<div id="image-progress"><p class="filename">' + fileObj.name + '</p><div class="progress"><div class="bar"></div></div></div>');
}

function uploadProgressImage(fileObj, bytesDone, bytesTotal) {
	jQuery('#image-progress .bar').width(450*bytesDone/bytesTotal);
}

function uploadSuccessImage(fileObj, serverData) {
	if ( serverData.match('media-upload-error') ) {
		jQuery('#media-upload-error').replaceWith(serverData);
		jQuery('#image-progress').replaceWith('');
	}
	else {
		jQuery('#media-upload-error').replaceWith('');
		jQuery('#flash-upload-ui').replaceWith(serverData);
	}
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

function fileQueued(fileObj) {
	try {
		var txtFileName = document.getElementById("txtFileName");
		txtFileName.value = fileObj.name;
	} catch (e) { }

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

function uploadProgress(fileObj, bytesLoaded, bytesTotal) {

	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100)

		fileObj.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
		var progress = new FileProgress(fileObj, this.customSettings.progress_target);
		progress.SetProgress(percent);
		progress.SetStatus("Uploading...");
	} catch (e) { }
}

function uploadSuccess(fileObj, server_data) {
	try {
		fileObj.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
		var progress = new FileProgress(fileObj, this.customSettings.progress_target);
		progress.SetComplete();
		progress.SetStatus("Complete.");
		progress.ToggleCancel(false);
		
		if (server_data === " ") {
			this.customSettings.upload_successful = false;
		} else {
			this.customSettings.upload_successful = true;
			document.getElementById("hidFileID").value = server_data;
		}
		
	} catch (e) { }
}

function uploadComplete(fileObj) {
	try {
		if (this.customSettings.upload_successful) {
			document.getElementById("btnBrowse").disabled = "true";
			uploadDone();
		} else {
			fileObj.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
			var progress = new FileProgress(fileObj, this.customSettings.progress_target);
			progress.SetError();
			progress.SetStatus("File rejected");
			progress.ToggleCancel(false);
			
			var txtFileName = document.getElementById("txtFileName");
			txtFileName.value = "";
			//validateForm();

			alert("There was a problem with the upload.\nThe server did not accept it.");
		}
	} catch (e) {  }
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
		
		// not sure if this is needed
		fileObj.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
		var progress = new FileProgress(fileObj, this.customSettings.progress_target);
		progress.SetError();
		progress.ToggleCancel(false);

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


/* ********************************************************
 *  Utility for displaying the file upload information
 *  This is not part of SWFUpload, just part of the demo
 * ******************************************************** */
function FileProgress(fileObj, target_id) {
		this.file_progress_id = fileObj.id;

		this.fileProgressElement = document.getElementById(this.file_progress_id);
		if (!this.fileProgressElement) {
			this.fileProgressElement = document.createElement("div");
			this.fileProgressElement.className = "progressContainer";
			this.fileProgressElement.id = this.file_progress_id;

			var progressCancel = document.createElement("a");
			progressCancel.className = "progressCancel";
			progressCancel.href = "#";
			progressCancel.style.visibility = "hidden";
			progressCancel.appendChild(document.createTextNode(" "));

			var progressText = document.createElement("div");
			progressText.className = "progressName";
			progressText.appendChild(document.createTextNode(fileObj.name));

			var progressBar = document.createElement("div");
			progressBar.className = "progressBarInProgress";

			var progressStatus = document.createElement("div");
			progressStatus.className = "progressBarStatus";
			progressStatus.innerHTML = "&nbsp;";

			this.fileProgressElement.appendChild(progressCancel);
			this.fileProgressElement.appendChild(progressText);
			this.fileProgressElement.appendChild(progressStatus);
			this.fileProgressElement.appendChild(progressBar);

			document.getElementById(target_id).appendChild(this.fileProgressElement);

		}

}
FileProgress.prototype.SetStart = function() {
		this.fileProgressElement.className = "progressContainer";
		this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
		this.fileProgressElement.childNodes[3].style.width = "";
}

FileProgress.prototype.SetProgress = function(percentage) {
		this.fileProgressElement.className = "progressContainer green";
		this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
		this.fileProgressElement.childNodes[3].style.width = percentage + "%";
}
FileProgress.prototype.SetComplete = function() {
		this.fileProgressElement.className = "progressContainer blue";
		this.fileProgressElement.childNodes[3].className = "progressBarComplete";
		this.fileProgressElement.childNodes[3].style.width = "";


}
FileProgress.prototype.SetError = function() {
		this.fileProgressElement.className = "progressContainer red";
		this.fileProgressElement.childNodes[3].className = "progressBarError";
		this.fileProgressElement.childNodes[3].style.width = "";
}
FileProgress.prototype.SetCancelled = function() {
		this.fileProgressElement.className = "progressContainer";
		this.fileProgressElement.childNodes[3].className = "progressBarError";
		this.fileProgressElement.childNodes[3].style.width = "";
}
FileProgress.prototype.SetStatus = function(status) {
		this.fileProgressElement.childNodes[2].innerHTML = status;
}

FileProgress.prototype.ToggleCancel = function(show, upload_obj) {
		this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
		if (upload_obj) {
			var file_id = this.file_progress_id;
			this.fileProgressElement.childNodes[0].onclick = function() { upload_obj.cancelUpload(file_id); return false; };
		}
}
