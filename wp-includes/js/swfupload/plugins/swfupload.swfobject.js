/*
	SWFUpload.SWFObject Plugin

	Summary:
		This plugin uses SWFObject to embed SWFUpload dynamically in the page.  SWFObject provides accurate Flash Player detection and DOM Ready loading.
		This plugin replaces the Graceful Degradation plugin.

	Features:
		* swfupload_load_failed_hander event
		* swfupload_pre_load_handler event
		* minimum_flash_version setting (default: "9.0.28")
		* SWFUpload.onload event for early loading

	Usage:
		Provide handlers and settings as needed.  When using the SWFUpload.SWFObject plugin you should initialize SWFUploading
		in SWFUpload.onload rather than in window.onload.  When initialized this way SWFUpload can load earlier preventing the UI flicker
		that was seen using the Graceful Degradation plugin.

		<script type="text/javascript">
			var swfu;
			SWFUpload.onload = function () {
				swfu = new SWFUpload({
					minimum_flash_version: "9.0.28",
					swfupload_pre_load_handler: swfuploadPreLoad,
					swfupload_load_failed_handler: swfuploadLoadFailed
				});
			};
		</script>
		
	Notes:
		You must provide set minimum_flash_version setting to "8" if you are using SWFUpload for Flash Player 8.
		The swfuploadLoadFailed event is only fired if the minimum version of Flash Player is not met.  Other issues such as missing SWF files, browser bugs
		 or corrupt Flash Player installations will not trigger this event.
		The swfuploadPreLoad event is fired as soon as the minimum version of Flash Player is found.  It does not wait for SWFUpload to load and can
		 be used to prepare the SWFUploadUI and hide alternate content.
		swfobject's onDomReady event is cross-browser safe but will default to the window.onload event when DOMReady is not supported by the browser.
		 Early DOM Loading is supported in major modern browsers but cannot be guaranteed for every browser ever made.
*/


// SWFObject v2.1 must be loaded
	
var SWFUpload;
if (typeof(SWFUpload) === "function") {
	SWFUpload.onload = function () {};

	swfobject.addDomLoadEvent(function () {
		if (typeof(SWFUpload.onload) === "function") {
			setTimeout(function(){SWFUpload.onload.call(window);}, 200);
		}
	});

	SWFUpload.prototype.initSettings = (function (oldInitSettings) {
		return function () {
			if (typeof(oldInitSettings) === "function") {
				oldInitSettings.call(this);
			}

			this.ensureDefault = function (settingName, defaultValue) {
				this.settings[settingName] = (this.settings[settingName] == undefined) ? defaultValue : this.settings[settingName];
			};

			this.ensureDefault("minimum_flash_version", "9.0.28");
			this.ensureDefault("swfupload_pre_load_handler", null);
			this.ensureDefault("swfupload_load_failed_handler", null);

			delete this.ensureDefault;

		};
	})(SWFUpload.prototype.initSettings);


	SWFUpload.prototype.loadFlash = function (oldLoadFlash) {
		return function () {
			var hasFlash = swfobject.hasFlashPlayerVersion(this.settings.minimum_flash_version);
			
			if (hasFlash) {
				this.queueEvent("swfupload_pre_load_handler");
				if (typeof(oldLoadFlash) === "function") {
					oldLoadFlash.call(this);
				}
			} else {
				this.queueEvent("swfupload_load_failed_handler");
			}
		};
		
	}(SWFUpload.prototype.loadFlash);
			
	SWFUpload.prototype.displayDebugInfo = function (oldDisplayDebugInfo) {
		return function () {
			if (typeof(oldDisplayDebugInfo) === "function") {
				oldDisplayDebugInfo.call(this);
			}
			
			this.debug(
				[
					"SWFUpload.SWFObject Plugin settings:", "\n",
					"\t", "minimum_flash_version:                      ", this.settings.minimum_flash_version, "\n",
					"\t", "swfupload_pre_load_handler assigned:     ", (typeof(this.settings.swfupload_pre_load_handler) === "function").toString(), "\n",
					"\t", "swfupload_load_failed_handler assigned:     ", (typeof(this.settings.swfupload_load_failed_handler) === "function").toString(), "\n",
				].join("")
			);
		};	
	}(SWFUpload.prototype.displayDebugInfo);
}
