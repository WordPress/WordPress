/* 
	SWFUpload Graceful Degradation Plug-in

	This plugin allows SWFUpload to display only if it is loaded successfully.  Otherwise a default form is left displayed.
	
	Usage:
	
	To use this plugin create two HTML containers. Each should have an ID defined.  One container should hold the SWFUpload UI.  The other should hold the degraded UI.
	
	The SWFUpload container should have its CSS "display" property set to "none".
	
	If SWFUpload loads successfully the SWFUpload container will be displayed ("display" set to "block") and the
	degraded container will be hidden ("display" set to "none").
	
	Use the settings "swfupload_element_id" and "degraded_element_id" to indicate your container IDs.  The default values are "swfupload_container" and "degraded_container".
	
*/

var SWFUpload;
if (typeof(SWFUpload) === "function") {
	SWFUpload.gracefulDegradation = {};
	SWFUpload.prototype.initSettings = function (old_initSettings) {
		return function (init_settings) {
			if (typeof(old_initSettings) === "function") {
				old_initSettings.call(this, init_settings);
			}
			
			this.addSetting("swfupload_element_id",		  		init_settings.swfupload_element_id,				"swfupload_container");
			this.addSetting("degraded_element_id",		  		init_settings.degraded_element_id,				"degraded_container");
			this.addSetting("user_swfUploadLoaded_handler",		init_settings.swfupload_loaded_handler,			SWFUpload.swfUploadLoaded);

			this.swfUploadLoaded_handler = SWFUpload.gracefulDegradation.swfUploadLoaded;
		};
	}(SWFUpload.prototype.initSettings);

	SWFUpload.gracefulDegradation.swfUploadLoaded = function () {
		var swfupload_container_id, swfupload_container, degraded_container_id, degraded_container, user_swfUploadLoaded_handler;
		try {
			if (uploadDegradeOptions.is_lighttpd_before_150) throw "Lighttpd versions earlier than 1.5.0 aren't supported!";
			swfupload_element_id = this.getSetting("swfupload_element_id");
			degraded_element_id = this.getSetting("degraded_element_id");
			
			// Show the UI container
			swfupload_container = document.getElementById(swfupload_element_id);
			if (swfupload_container !== null) {
				swfupload_container.style.display = "block";

				// Now take care of hiding the degraded UI
				degraded_container = document.getElementById(degraded_element_id);
				if (degraded_container !== null) {
					degraded_container.style.display = "none";
				}
			}
		} catch (ex) {
			this.debug(ex);
		}
		
		user_swfUploadLoaded_handler = this.getSetting("user_swfUploadLoaded_handler");
		if (typeof(user_swfUploadLoaded_handler) === "function") {
			user_swfUploadLoaded_handler.apply(this);
		}
	};

}
