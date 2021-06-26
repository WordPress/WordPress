var topWin = window.dialogArguments || opener || parent || top;

function fileDialogStart() {}
function fileQueued() {}
function uploadStart() {}
function uploadProgress() {}
function prepareMediaItem() {}
function prepareMediaItemInit() {}
function itemAjaxError() {}
function deleteSuccess() {}
function deleteError() {}
function updateMediaForm() {}
function uploadSuccess() {}
function uploadComplete() {}
function wpQueueError() {}
function wpFileError() {}
function fileQueueError() {}
function fileDialogComplete() {}
function uploadError() {}
function cancelUpload() {}

function switchUploader() {
	jQuery( '#' + swfu.customSettings.swfupload_element_id ).hide();
	jQuery( '#' + swfu.customSettings.degraded_element_id ).show();
	jQuery( '.upload-html-bypass' ).hide();
}

function swfuploadPreLoad() {
	switchUploader();
}

function swfuploadLoadFailed() {
	switchUploader();
}

jQuery(document).ready(function($){
	$( 'input[type="radio"]', '#media-items' ).on( 'click', function(){
		var tr = $(this).closest('tr');

		if ( $(tr).hasClass('align') )
			setUserSetting('align', $(this).val());
		else if ( $(tr).hasClass('image-size') )
			setUserSetting('imgsize', $(this).val());
	});

	$( 'button.button', '#media-items' ).on( 'click', function(){
		var c = this.className || '';
		c = c.match(/url([^ '"]+)/);
		if ( c && c[1] ) {
			setUserSetting('urlbutton', c[1]);
			$(this).siblings('.urlfield').val( $(this).attr('title') );
		}
	});
});
