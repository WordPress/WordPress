/*
	Queue Plug-in
	
	Features:
		cancelQueue method for cancelling the entire queue.
		All queued files are uploaded when startUpload() is called.
		If false is returned from uploadComplete then the queue upload is stopped.  If false is not returned (strict comparison) then the queue upload is continued.
		
	*/

var SWFUpload;
if (typeof(SWFUpload) === "function") {
	SWFUpload.queue = {};
	
	SWFUpload.prototype.initSettings = function (old_initSettings) {
		return function (init_settings) {
			if (typeof(old_initSettings) === "function") {
				old_initSettings.call(this, init_settings);
			}
			
			this.customSettings.queue_cancelled_flag = false;
			
			this.addSetting("user_upload_complete_handler", init_settings.upload_complete_handler, SWFUpload.uploadComplete);
			this.uploadComplete_handler = SWFUpload.queue.uploadComplete;
		};
	}(SWFUpload.prototype.initSettings);

	SWFUpload.prototype.cancelQueue = function () {
		var stats = this.getStats();
		this.customSettings.queue_cancelled_flag = false;

		if (stats.in_progress > 0) {
			this.customSettings.queue_cancelled_flag = true;
		}
		
		while(stats.files_queued > 0) {
			this.cancelUpload();
			stats = this.getStats();
		}
	};
	
	SWFUpload.queue.uploadComplete = function (file) {
		var user_upload_complete_handler = this.getSetting("user_upload_complete_handler");
		var continue_upload = true;
		if (typeof(user_upload_complete_handler) === "function") {
			continue_upload = (user_upload_complete_handler.call(this, file) === false) ? false : true;
		}
		
		if (continue_upload) {
			var stats = this.getStats();
			if (stats.files_queued > 0 && this.customSettings.queue_cancelled_flag === false) {
				this.startUpload();
			} else {
				this.customSettings.queue_cancelled_flag = false;
			}
		}
	};
}
