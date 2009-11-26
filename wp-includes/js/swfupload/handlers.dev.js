
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
	jQuery('#insert-gallery').attr('disabled', 'disabled');
	jQuery('#cancel-upload').attr('disabled', '');
}

function uploadStart(fileObj) {
	return true;
}

function uploadProgress(fileObj, bytesDone, bytesTotal) {
	// Lengthen the progress bar
	var w = jQuery('#media-items').width() - 2, item = jQuery('#media-item-' + fileObj.id);
	jQuery('.bar', item).width( w * bytesDone / bytesTotal );
	jQuery('.percent', item).html( Math.ceil(bytesDone / bytesTotal * 100) + '%' );

	if ( bytesDone == bytesTotal )
		jQuery('.bar', item).html('<strong class="crunching">' + swfuploadL10n.crunching + '</strong>');
}

function prepareMediaItem(fileObj, serverData) {
	var f = ( typeof shortform == 'undefined' ) ? 1 : 2, item = jQuery('#media-item-' + fileObj.id);
	// Move the progress bar to 100%
	jQuery('.bar', item).remove();
	jQuery('.progress', item).hide();

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
	jQuery('.thumbnail', item).clone().attr('className', 'pinkynail toggle').prependTo(item);

	// Replace the original filename with the new (unique) one assigned during upload
	jQuery('.filename.original', item).replaceWith( jQuery('.filename.new', item) );

	// Also bind toggle to the links
	jQuery('a.toggle', item).click(function(){
		jQuery(this).siblings('.slidetoggle').slideToggle(350, function(){
			var o = jQuery(this).offset();
			window.scrollTo(0, o.top - 36);
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
				jQuery('a.undo', item).addClass('hidden');
				jQuery('a.describe-toggle-on, .menu_order_input', item).show();
				item.animate( {backgroundColor: '#fff'}, { queue: false, duration: 300, complete: function(){ jQuery(this).css({backgroundColor:''}); } }).removeClass('trash-undo');
			}
		});
		return false;
	});

	// Open this item if it says to start open (e.g. to display an error)
	jQuery('#media-item-' + fileObj.id + '.startopen').removeClass('startopen').slideToggle(500).siblings('.toggle').toggle();
}

function itemAjaxError(id, html) {
	var error = jQuery('#media-item-error' + id);

	error.html('<div class="file-error"><button type="button" id="dismiss-'+id+'" class="button dismiss">'+swfuploadL10n.dismiss+'</button>'+html+'</div>');
	jQuery('#dismiss-'+id).click(function(){jQuery(this).parents('.file-error').slideUp(200, function(){jQuery(this).empty();})});
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
	item.css( {backgroundColor:'#fff'} ).animate( {backgroundColor:'#ffffe0'}, {queue:false, duration:500} ).addClass('trash-undo');

	jQuery('.filename:empty', item).remove();
	jQuery('.filename', item).append('<span class="trashnotice"> ' + swfuploadL10n.deleted + ' </span>').siblings('a.toggle').hide();
	jQuery('.filename', item).append( jQuery('a.undo', item).removeClass('hidden') );
	jQuery('.menu_order_input', item).hide();
	
	return;
}

function deleteError(X, textStatus, errorThrown) {
	// TODO
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

function uploadComplete(fileObj) {
	// If no more uploads queued, enable the submit button
	if ( swfu.getStats().files_queued == 0 ) {
		jQuery('#cancel-upload').attr('disabled', 'disabled');
		jQuery('#insert-gallery').attr('disabled', '');
	}
}


// wp-specific error handlers

// generic message
function wpQueueError(message) {
	jQuery('#media-upload-error').show().text(message);
}

// file-specific message
function wpFileError(fileObj, message) {
	jQuery('#media-item-' + fileObj.id + ' .filename').after('<div class="file-error"><button type="button" id="dismiss-' + fileObj.id + '" class="button dismiss">'+swfuploadL10n.dismiss+'</button>'+message+'</div>').siblings('.toggle').remove();
	jQuery('#dismiss-' + fileObj.id).click(function(){jQuery(this).parents('.media-item').slideUp(200, function(){jQuery(this).remove();})});
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

function switchUploader(s) {
	var f = document.getElementById(swfu.customSettings.swfupload_element_id), h = document.getElementById(swfu.customSettings.degraded_element_id);
	if ( s ) {
		f.style.display = 'block';
		h.style.display = 'none';
	} else {
		f.style.display = 'none';
		h.style.display = 'block';
	}
}

function swfuploadPreLoad() {
	if ( !uploaderMode ) {
		switchUploader(1);
	} else {
		switchUploader(0);
	}
}

function swfuploadLoadFailed() {
	switchUploader(0);
	jQuery('.upload-html-bypass').hide();
}

function uploadError(fileObj, errorCode, message) {

	switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
			wpFileError(fileObj, swfuploadL10n.missing_upload_url);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			wpFileError(fileObj, swfuploadL10n.upload_limit_exceeded);
			break;
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			wpQueueError(swfuploadL10n.http_error);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			wpQueueError(swfuploadL10n.upload_failed);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			wpQueueError(swfuploadL10n.io_error);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			wpQueueError(swfuploadL10n.security_error);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			jQuery('#media-item-' + fileObj.id).remove();
			break;
		default:
			wpFileError(fileObj, swfuploadL10n.default_error);
	}
}

function cancelUpload() {
	swfu.cancelQueue();
}

// remember the last used image size, alignment and url
jQuery(document).ready(function($){
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
});
