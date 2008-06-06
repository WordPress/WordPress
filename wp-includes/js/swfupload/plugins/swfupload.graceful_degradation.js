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
	SWFUpload.prototype.initSettings = (function (oldInitSettings) {
		return function () {
			if (typeof(oldInitSettings) === "function") {
				oldInitSettings.call(this);
			}
			
			this.ensureDefault = function (settingName, defaultValue) {
				this.settings[settingName] = (this.settings[settingName] == undefined) ? defaultValue : this.settings[settingName];
			};
			
			this.ensureDefault("swfupload_element_id", "swfupload_container");
			this.ensureDefault("degraded_element_id", "degraded_container");
			this.settings.user_swfupload_loaded_handler = this.settings.swfupload_loaded_handler;

			this.settings.swfupload_loaded_handler = SWFUpload.gracefulDegradation.swfUploadLoadedHandler;
			
			delete this.ensureDefault;
		};
	})(SWFUpload.prototype.initSettings);

	SWFUpload.gracefulDegradation.swfUploadLoadedHandler = function () {
		var swfuploadContainerID, swfuploadContainer, degradedContainerID, degradedContainer;

		swfuploadContainerID = this.settings.swfupload_element_id;
		degradedContainerID = this.settings.degraded_element_id;
		
		// Show the UI container
		swfuploadContainer = document.getElementById(swfuploadContainerID);
		if (swfuploadContainer != undefined) {
			swfuploadContainer.style.display = "block";

			// Now take care of hiding the degraded UI
			degradedContainer = document.getElementById(degradedContainerID);
			if (degradedContainer != undefined) {
				degradedContainer.style.display = "none";
			}
		}
		
		if (typeof(this.settings.user_swfupload_loaded_handler) === "function") {
			this.settings.user_swfupload_loaded_handler.apply(this);
		}
	};

}
