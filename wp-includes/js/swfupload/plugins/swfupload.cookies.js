/*
	Cookie Plug-in
	
	This plug in automatically gets all the cookies for this site and adds them to the post_params.
	Cookies are loaded only on initialization.  The refreshCookies function can be called to update the post_params.
	The cookies will override any other post params with the same name.
*/

var SWFUpload;
if (typeof(SWFUpload) === "function") {
	SWFUpload.prototype.initSettings = function (old_initSettings) {
		return function (init_settings) {
			if (typeof(old_initSettings) === "function") {
				old_initSettings.call(this, init_settings);
			}
			
			this.refreshCookies(false);	// The false parameter must be sent since SWFUpload has not initialzed at this point
		};
	}(SWFUpload.prototype.initSettings);
	
	// refreshes the post_params and updates SWFUpload.  The send_to_flash parameters is optional and defaults to True
	SWFUpload.prototype.refreshCookies = function (send_to_flash) {
		if (send_to_flash !== false) send_to_flash = true;
		
		// Get the post_params object
		var post_params = this.getSetting("post_params");
		
		// Get the cookies
		var i, cookie_array = document.cookie.split(';'), ca_length = cookie_array.length, c, eq_index, name, value;
		for(i = 0; i < ca_length; i++) {
			c = cookie_array[i];
			
			// Left Trim spaces
			while (c.charAt(0) == " ") {
				c = c.substring(1, c.length);
			}
			eq_index = c.indexOf("=");
			if (eq_index > 0) {
				name = c.substring(0, eq_index);
				value = c.substring(eq_index+1);
				post_params[name] = value;
			}
		}
		
		if (send_to_flash) {
			this.setPostParams(post_params);
		}
	};

}
