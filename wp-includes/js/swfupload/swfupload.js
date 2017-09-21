/**
 * SWFUpload fallback
 *
 * @since 4.9.0
 */

var SWFUpload;

( function () {
	function noop() {}

	if (SWFUpload == undefined) {
		SWFUpload = function (settings) {
			this.initSWFUpload(settings);
		};
	}

	SWFUpload.prototype.initSWFUpload = function ( settings ) {
		function fallback() {
			var $ = window.jQuery;
			var $placeholder = settings.button_placeholder_id ? $( '#' + settings.button_placeholder_id ) : $( settings.button_placeholder );

			if ( ! $placeholder.length ) {
				return;
			}

			var $form = $placeholder.closest( 'form' );

			if ( ! $form.length ) {
				$form = $( '<form enctype="multipart/form-data" method="post">' );
				$form.attr( 'action', settings.upload_url );
				$form.insertAfter( $placeholder ).append( $placeholder );
			}

			$placeholder.replaceWith(
				$( '<div>' )
					.append(
						$( '<input type="file" multiple />' ).attr({
							name: settings.file_post_name || 'async-upload',
							accepts: settings.file_types || '*.*'
						})
					).append(
						$( '<input type="submit" name="html-upload" class="button" value="Upload" />' )
					)
			);
		}

		try {
			// Try the built-in fallback.
			if ( typeof settings.swfupload_load_failed_handler === 'function' && settings.custom_settings ) {

				window.swfu = {
					customSettings: settings.custom_settings
				};

				settings.swfupload_load_failed_handler();
			} else {
				fallback();
			}
		} catch ( ex ) {
			fallback();
		}
	};

	SWFUpload.instances = {};
	SWFUpload.movieCount = 0;
	SWFUpload.version = "0";
	SWFUpload.QUEUE_ERROR = {};
	SWFUpload.UPLOAD_ERROR = {};
	SWFUpload.FILE_STATUS = {};
	SWFUpload.BUTTON_ACTION = {};
	SWFUpload.CURSOR = {};
	SWFUpload.WINDOW_MODE = {};

	SWFUpload.completeURL = noop;
	SWFUpload.prototype.initSettings = noop;
	SWFUpload.prototype.loadFlash = noop;
	SWFUpload.prototype.getFlashHTML = noop;
	SWFUpload.prototype.getFlashVars = noop;
	SWFUpload.prototype.getMovieElement = noop;
	SWFUpload.prototype.buildParamString = noop;
	SWFUpload.prototype.destroy = noop;
	SWFUpload.prototype.displayDebugInfo = noop;
	SWFUpload.prototype.addSetting = noop;
	SWFUpload.prototype.getSetting = noop;
	SWFUpload.prototype.callFlash = noop;
	SWFUpload.prototype.selectFile = noop;
	SWFUpload.prototype.selectFiles = noop;
	SWFUpload.prototype.startUpload = noop;
	SWFUpload.prototype.cancelUpload = noop;
	SWFUpload.prototype.stopUpload = noop;
	SWFUpload.prototype.getStats = noop;
	SWFUpload.prototype.setStats = noop;
	SWFUpload.prototype.getFile = noop;
	SWFUpload.prototype.addFileParam = noop;
	SWFUpload.prototype.removeFileParam = noop;
	SWFUpload.prototype.setUploadURL = noop;
	SWFUpload.prototype.setPostParams = noop;
	SWFUpload.prototype.addPostParam = noop;
	SWFUpload.prototype.removePostParam = noop;
	SWFUpload.prototype.setFileTypes = noop;
	SWFUpload.prototype.setFileSizeLimit = noop;
	SWFUpload.prototype.setFileUploadLimit = noop;
	SWFUpload.prototype.setFileQueueLimit = noop;
	SWFUpload.prototype.setFilePostName = noop;
	SWFUpload.prototype.setUseQueryString = noop;
	SWFUpload.prototype.setRequeueOnError = noop;
	SWFUpload.prototype.setHTTPSuccess = noop;
	SWFUpload.prototype.setAssumeSuccessTimeout = noop;
	SWFUpload.prototype.setDebugEnabled = noop;
	SWFUpload.prototype.setButtonImageURL = noop;
	SWFUpload.prototype.setButtonDimensions = noop;
	SWFUpload.prototype.setButtonText = noop;
	SWFUpload.prototype.setButtonTextPadding = noop;
	SWFUpload.prototype.setButtonTextStyle = noop;
	SWFUpload.prototype.setButtonDisabled = noop;
	SWFUpload.prototype.setButtonAction = noop;
	SWFUpload.prototype.setButtonCursor = noop;
	SWFUpload.prototype.queueEvent = noop;
	SWFUpload.prototype.executeNextEvent = noop;
	SWFUpload.prototype.unescapeFilePostParams = noop;
	SWFUpload.prototype.testExternalInterface = noop;
	SWFUpload.prototype.flashReady = noop;
	SWFUpload.prototype.cleanUp = noop;
	SWFUpload.prototype.fileDialogStart = noop;
	SWFUpload.prototype.fileQueued = noop;
	SWFUpload.prototype.fileQueueError = noop;
	SWFUpload.prototype.fileDialogComplete = noop;
	SWFUpload.prototype.uploadStart = noop;
	SWFUpload.prototype.returnUploadStart = noop;
	SWFUpload.prototype.uploadProgress = noop;
	SWFUpload.prototype.uploadError = noop;
	SWFUpload.prototype.uploadSuccess = noop;
	SWFUpload.prototype.uploadComplete = noop;
	SWFUpload.prototype.debug = noop;
	SWFUpload.prototype.debugMessage = noop;
	SWFUpload.Console = {
		writeLine: noop
	};
}() );
