/*
	DocumentReady Plug-in
	
	This plugin loads SWFUpload as soon as the document is ready.  You should not load SWFUpload inside window.onload using this plugin.
	You can also chain other functions by calling SWFUpload.DocumentReady(your function).
	
	Warning: Embedded Ads or other scripts that overwrite window.onload or use their own document ready functions may interfer with this plugin.  You
		should not set window.onload when using this plugin.
	
	Usage Example:
	
	var swfu = new SWFUpload(your settings object);
	SWFUpload.DocumentReady(function () { alert('Document Ready!'; });
	
*/

var SWFUpload;
if (typeof(SWFUpload) === "function") {
	// Override iniSWFUpload so SWFUpload gets inited when the document is ready rather than immediately
	SWFUpload.prototype.initSWFUpload = function (old_initSWFUpload) {
		return function (init_settings) {
			var self = this;
			if  (typeof(old_initSWFUpload) === "function") {
				SWFUpload.DocumentReady(function () {
					old_initSWFUpload.call(self, init_settings);
				});
			}
		}
		
	}(SWFUpload.prototype.initSWFUpload);

	
	// The DocumentReady function adds the passed in function to
	// the functions that will be executed when the document is ready/loaded
	SWFUpload.DocumentReady = function (fn) {
		// Add the function to the chain
		SWFUpload.DocumentReady.InternalOnloadChain = function (previous_link_fn) {
			return function () {
				if (typeof(previous_link_fn) === "function") {
					previous_link_fn();
				}
				fn();
			};
		}(SWFUpload.DocumentReady.InternalOnloadChain);
	};
	SWFUpload.DocumentReady.InternalOnloadChain = null;
	SWFUpload.DocumentReady.Onload = function () {
		// Execute the onload function chain
		if (typeof(SWFUpload.DocumentReady.InternalOnloadChain) === "function") {
			SWFUpload.DocumentReady.InternalOnloadChain();
		}
	};
	SWFUpload.DocumentReady.SetupComplete = false;


	/* ********************************************
		This portion of the code gets executed as soon it is loaded.
		It binds the proper event for executing JavaScript is
		early as possible.  This is a per browser function and so
		some browser sniffing is used.
		
		This solution still has the "exposed" issue (See the Global Delegation section at http://peter.michaux.ca/article/553 )
		
		Base solution from http://dean.edwards.name/weblog/2006/06/again/ and http://dean.edwards.name/weblog/2005/09/busted/
	******************************************** */
	if (!SWFUpload.DocumentReady.SetupComplete) {
		// for Internet Explorer (using conditional comments)
		/*@cc_on @*/
		/*@if (@_win32)
		document.write("<script id=__ie_onload defer src=javascript:void(0)><\/script>");
		var script = document.getElementById("__ie_onload");
		script.onreadystatechange = function() {
			if (this.readyState == "complete") {
				SWFUpload.DocumentReady.Onload(); // call the onload handler
			}
		};
		SWFUpload.DocumentReady.SetupComplete = true;
		/*@end @*/
	}

	if (!SWFUpload.DocumentReady.SetupComplete && /WebKit/i.test(navigator.userAgent)) { // sniff
		var _timer = setInterval(function() {
			if (/loaded|complete/.test(document.readyState)) {
				clearInterval(_timer);
				SWFUpload.DocumentReady.Onload(); // call the onload handler
			}
		}, 10);
		SWFUpload.DocumentReady.SetupComplete = true;
	}

	/* for Mozilla */
	if (!SWFUpload.DocumentReady.SetupComplete && document.addEventListener) {
		document.addEventListener("DOMContentLoaded", SWFUpload.DocumentReady.Onload, false);
		SWFUpload.DocumentReady.SetupComplete = true;
	}

	/* for other browsers */
	if (!SWFUpload.DocumentReady.SetupComplete) {
		window.onload = SWFUpload.DocumentReady.Onload;
		SWFUpload.DocumentReady.SetupComplete = true;
	}
}
