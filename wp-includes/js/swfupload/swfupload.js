/**
 * SWFUpload v2.0 by Jacob Roberts, Nov 2007, http://www.swfupload.org, http://linebyline.blogspot.com
 * -------- -------- -------- -------- -------- -------- -------- --------
 * SWFUpload is (c) 2006 Lars Huring and Mammon Media and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * See Changelog.txt for version history
 *
 * Development Notes:
 *  * This version of SWFUpload requires Flash Player 9.0.28 and should autodetect the correct flash version.
 *  * In Linux Flash Player 9 setting the post file variable name does not work. It is always set to "Filedata".
 *  * There is a lot of repeated code that could be refactored to single functions.  Feel free.
 *  * It's dangerous to do "circular calls" between Flash and JavaScript. I've taken steps to try to work around issues
 *     by having the event calls pipe through setTimeout.  However you should still avoid calling in to Flash from
 *     within the event handler methods.  Especially the "startUpload" event since it cannot use the setTimeout hack.
 */


/* *********** */
/* Constructor */
/* *********** */

var SWFUpload = function (init_settings) {
	this.initSWFUpload(init_settings);
};

SWFUpload.prototype.initSWFUpload = function (init_settings) {
	// Remove background flicker in IE (read this: http://misterpixel.blogspot.com/2006/09/forensic-analysis-of-ie6.html)
	// This doesn't have anything to do with SWFUpload but can help your UI behave better in IE.
	try {
		document.execCommand('BackgroundImageCache', false, true);
	} catch (ex1) {
	}


	try {
		this.customSettings = {};	// A container where developers can place their own settings associated with this instance.
		this.settings = {};
		this.eventQueue = [];
		this.movieName = "SWFUpload_" + SWFUpload.movieCount++;
		this.movieElement = null;

		// Setup global control tracking
		SWFUpload.instances[this.movieName] = this;

		// Load the settings.  Load the Flash movie.
		this.initSettings(init_settings);
		this.loadFlash();

		this.displayDebugInfo();

	} catch (ex2) {
		this.debug(ex2);
	}
}

/* *************** */
/* Static thingies */
/* *************** */
SWFUpload.instances = {};
SWFUpload.movieCount = 0;
SWFUpload.QUEUE_ERROR = {
	QUEUE_LIMIT_EXCEEDED	  		: -100,
	FILE_EXCEEDS_SIZE_LIMIT  		: -110,
	ZERO_BYTE_FILE			  		: -120,
	INVALID_FILETYPE		  		: -130
};
SWFUpload.UPLOAD_ERROR = {
	HTTP_ERROR				  		: -200,
	MISSING_UPLOAD_URL	      		: -210,
	IO_ERROR				  		: -220,
	SECURITY_ERROR			  		: -230,
	UPLOAD_LIMIT_EXCEEDED	  		: -240,
	UPLOAD_FAILED			  		: -250,
	SPECIFIED_FILE_ID_NOT_FOUND		: -260,
	FILE_VALIDATION_FAILED	  		: -270,
	FILE_CANCELLED			  		: -280,
	UPLOAD_STOPPED					: -290
};
SWFUpload.FILE_STATUS = {
	QUEUED		 : -1,
	IN_PROGRESS	 : -2,
	ERROR		 : -3,
	COMPLETE	 : -4,
	CANCELLED	 : -5
};


/* ***************** */
/* Instance Thingies */
/* ***************** */
// init is a private method that ensures that all the object settings are set, getting a default value if one was not assigned.

SWFUpload.prototype.initSettings = function (init_settings) {
	// Upload backend settings
	this.addSetting("upload_url",		 		init_settings.upload_url,		  		"");
	this.addSetting("file_post_name",	 		init_settings.file_post_name,	  		"Filedata");
	this.addSetting("post_params",		 		init_settings.post_params,		  		{});

	// File Settings
	this.addSetting("file_types",			  	init_settings.file_types,				"*.*");
	this.addSetting("file_types_description", 	init_settings.file_types_description, 	"All Files");
	this.addSetting("file_size_limit",		  	init_settings.file_size_limit,			"1024");
	this.addSetting("file_upload_limit",	  	init_settings.file_upload_limit,		"0");
	this.addSetting("file_queue_limit",		  	init_settings.file_queue_limit,			"0");

	// Flash Settings
	this.addSetting("flash_url",		  		init_settings.flash_url,				"swfupload.swf");
	this.addSetting("flash_width",		  		init_settings.flash_width,				"1px");
	this.addSetting("flash_height",		  		init_settings.flash_height,				"1px");
	this.addSetting("flash_color",		  		init_settings.flash_color,				"#FFFFFF");

	// Debug Settings
	this.addSetting("debug_enabled", init_settings.debug,  false);

	// Event Handlers
	this.flashReady_handler         = SWFUpload.flashReady;	// This is a non-overrideable event handler
	this.swfUploadLoaded_handler    = this.retrieveSetting(init_settings.swfupload_loaded_handler,	    SWFUpload.swfUploadLoaded);
	
	this.fileDialogStart_handler	= this.retrieveSetting(init_settings.file_dialog_start_handler,		SWFUpload.fileDialogStart);
	this.fileQueued_handler			= this.retrieveSetting(init_settings.file_queued_handler,			SWFUpload.fileQueued);
	this.fileQueueError_handler		= this.retrieveSetting(init_settings.file_queue_error_handler,		SWFUpload.fileQueueError);
	this.fileDialogComplete_handler	= this.retrieveSetting(init_settings.file_dialog_complete_handler,	SWFUpload.fileDialogComplete);
	
	this.uploadStart_handler		= this.retrieveSetting(init_settings.upload_start_handler,			SWFUpload.uploadStart);
	this.uploadProgress_handler		= this.retrieveSetting(init_settings.upload_progress_handler,		SWFUpload.uploadProgress);
	this.uploadError_handler		= this.retrieveSetting(init_settings.upload_error_handler,			SWFUpload.uploadError);
	this.uploadSuccess_handler		= this.retrieveSetting(init_settings.upload_success_handler,		SWFUpload.uploadSuccess);
	this.uploadComplete_handler		= this.retrieveSetting(init_settings.upload_complete_handler,		SWFUpload.uploadComplete);

	this.debug_handler				= this.retrieveSetting(init_settings.debug_handler,			   		SWFUpload.debug);

	// Other settings
	this.customSettings = this.retrieveSetting(init_settings.custom_settings, {});
};

// loadFlash is a private method that generates the HTML tag for the Flash
// It then adds the flash to the "target" or to the body and stores a
// reference to the flash element in "movieElement".
SWFUpload.prototype.loadFlash = function () {
	var html, target_element, container;

	// Make sure an element with the ID we are going to use doesn't already exist
	if (document.getElementById(this.movieName) !== null) {
		return false;
	}

	// Get the body tag where we will be adding the flash movie
	try {
		target_element = document.getElementsByTagName("body")[0];
		if (typeof(target_element) === "undefined" || target_element === null) {
			this.debug('Could not find the BODY element. SWFUpload failed to load.');
			return false;
		}
	} catch (ex) {
		return false;
	}

	// Append the container and load the flash
	container = document.createElement("div");
	container.style.width = this.getSetting("flash_width");
	container.style.height = this.getSetting("flash_height");

	target_element.appendChild(container);
	container.innerHTML = this.getFlashHTML();	// Using innerHTML is non-standard but the only sensible way to dynamically add Flash in IE (and maybe other browsers)
};

// Generates the embed/object tags needed to embed the flash in to the document
SWFUpload.prototype.getFlashHTML = function () {
	var html = "";

	// Create Mozilla Embed HTML
	if (navigator.plugins && navigator.mimeTypes && navigator.mimeTypes.length) {
		// Build the basic embed html
		html = '<embed type="application/x-shockwave-flash" src="' + this.getSetting("flash_url") + '" width="' + this.getSetting("flash_width") + '" height="' + this.getSetting("flash_height") + '"';
		html += ' id="' + this.movieName + '" name="' + this.movieName + '" ';
		html += 'bgcolor="' + this.getSetting("flash_color") + '" quality="high" menu="false" flashvars="';

		html += this.getFlashVars();

		html += '" />';

		// Create IE Object HTML
	} else {

		// Build the basic Object tag
		html = '<object id="' + this.movieName + '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="' + this.getSetting("flash_width") + '" height="' + this.getSetting("flash_height") + '">';
		html += '<param name="movie" value="' + this.getSetting("flash_url") + '">';

		html += '<param name="bgcolor" value="' + this.getSetting("flash_color") + '" />';
		html += '<param name="quality" value="high" />';
		html += '<param name="menu" value="false" />';

		html += '<param name="flashvars" value="' + this.getFlashVars() + '" />';
		html += '</object>';
	}

	return html;
};

// This private method builds the parameter string that will be passed
// to flash.
SWFUpload.prototype.getFlashVars = function () {
	// Build a string from the post param object
	var param_string = this.buildParamString();

	// Build the parameter string
	var html = "";
	html += "movieName=" + encodeURIComponent(this.movieName);
	html += "&uploadURL=" + encodeURIComponent(this.getSetting("upload_url"));
	html += "&params=" + encodeURIComponent(param_string);
	html += "&filePostName=" + encodeURIComponent(this.getSetting("file_post_name"));
	html += "&fileTypes=" + encodeURIComponent(this.getSetting("file_types"));
	html += "&fileTypesDescription=" + encodeURIComponent(this.getSetting("file_types_description"));
	html += "&fileSizeLimit=" + encodeURIComponent(this.getSetting("file_size_limit"));
	html += "&fileUploadLimit=" + encodeURIComponent(this.getSetting("file_upload_limit"));
	html += "&fileQueueLimit=" + encodeURIComponent(this.getSetting("file_queue_limit"));
	html += "&debugEnabled=" + encodeURIComponent(this.getSetting("debug_enabled"));

	return html;
};

SWFUpload.prototype.getMovieElement = function () {
	if (typeof(this.movieElement) === "undefined" || this.movieElement === null) {
		this.movieElement = document.getElementById(this.movieName);

		// Fix IEs "Flash can't callback when in a form" issue (http://www.extremefx.com.ar/blog/fixing-flash-external-interface-inside-form-on-internet-explorer)
		// Removed because Revision 6 always adds the flash to the body (inside a containing div)
		// If you insist on adding the Flash file inside a Form then in IE you have to make you wait until the DOM is ready
		// and run this code to make the form's ID available from the window object so Flash and JavaScript can communicate.
		//if (typeof(window[this.movieName]) === "undefined" || window[this.moveName] !== this.movieElement) {
		//	window[this.movieName] = this.movieElement;
		//}
	}

	return this.movieElement;
};

SWFUpload.prototype.buildParamString = function () {
	var post_params = this.getSetting("post_params");
	var param_string_pairs = [];
	var i, value, name;

	// Retrieve the user defined parameters
	if (typeof(post_params) === "object") {
		for (name in post_params) {
			if (post_params.hasOwnProperty(name)) {
				if (typeof(post_params[name]) === "string") {
					param_string_pairs.push(encodeURIComponent(name) + "=" + encodeURIComponent(post_params[name]));
				}
			}
		}
	}

	return param_string_pairs.join("&");
};

// Saves a setting.	 If the value given is undefined or null then the default_value is used.
SWFUpload.prototype.addSetting = function (name, value, default_value) {
	if (typeof(value) === "undefined" || value === null) {
		this.settings[name] = default_value;
	} else {
		this.settings[name] = value;
	}

	return this.settings[name];
};

// Gets a setting.	Returns empty string if not found.
SWFUpload.prototype.getSetting = function (name) {
	if (typeof(this.settings[name]) === "undefined") {
		return "";
	} else {
		return this.settings[name];
	}
};

// Gets a setting, if the setting is undefined then return the default value
// This does not affect or use the interal setting object.
SWFUpload.prototype.retrieveSetting = function (value, default_value) {
	if (typeof(value) === "undefined" || value === null) {
		return default_value;
	} else {
		return value;
	}
};


// It loops through all the settings and displays
// them in the debug Console.
SWFUpload.prototype.displayDebugInfo = function () {
	var key, debug_message = "";

	debug_message += "----- SWFUPLOAD SETTINGS     ----\nID: " + this.moveName + "\n";

	debug_message += this.outputObject(this.settings);

	debug_message += "----- SWFUPLOAD SETTINGS END ----\n";
	debug_message += "\n";

	this.debug(debug_message);
};
SWFUpload.prototype.outputObject = function (object, prefix) {
	var output = "", key;

	if (typeof(prefix) !== "string") {
		prefix = "";
	}
	if (typeof(object) !== "object") {
		return "";
	}

	for (key in object) {
		if (object.hasOwnProperty(key)) {
			if (typeof(object[key]) === "object") {
				output += (prefix + key + ": { \n" + this.outputObject(object[key], "\t" + prefix) + prefix + "}" + "\n");
			} else {
				output += (prefix + key + ": " + object[key] + "\n");
			}
		}
	}

	return output;
};

/* *****************************
	-- Flash control methods --
	Your UI should use these
	to operate SWFUpload
   ***************************** */

SWFUpload.prototype.selectFile = function () {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SelectFile) === "function") {
		try {
			movie_element.SelectFile();
		}
		catch (ex) {
			this.debug("Could not call SelectFile: " + ex);
		}
	} else {
		this.debug("Could not find Flash element");
	}

};

SWFUpload.prototype.selectFiles = function () {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SelectFiles) === "function") {
		try {
			movie_element.SelectFiles();
		}
		catch (ex) {
			this.debug("Could not call SelectFiles: " + ex);
		}
	} else {
		this.debug("Could not find Flash element");
	}

};


/* Start the upload.  If a file_id is specified that file is uploaded. Otherwise the first
 * file in the queue is uploaded.  If no files are in the queue then nothing happens.
 * This call uses setTimeout since Flash will be calling back in to JavaScript
 */
SWFUpload.prototype.startUpload = function (file_id) {
	var self = this;
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.StartUpload) === "function") {
		setTimeout(
			function () {
				try {
					movie_element.StartUpload(file_id);
				}
				catch (ex) {
					self.debug("Could not call StartUpload: " + ex);
				}
			}, 0
		);
	} else {
		this.debug("Could not find Flash element");
	}

};

/* Cancels a the file upload.  You must specify a file_id */
SWFUpload.prototype.cancelUpload = function (file_id) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.CancelUpload) === "function") {
		try {
			movie_element.CancelUpload(file_id);
		}
		catch (ex) {
			this.debug("Could not call CancelUpload: " + ex);
		}
	} else {
		this.debug("Could not find Flash element");
	}

};

// Stops the current upload.  The file is re-queued.  If nothing is currently uploading then nothing happens.
SWFUpload.prototype.stopUpload = function () {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.StopUpload) === "function") {
		try {
			movie_element.StopUpload();
		}
		catch (ex) {
			this.debug("Could not call StopUpload: " + ex);
		}
	} else {
		this.debug("Could not find Flash element");
	}

};

/* ************************
 * Settings methods
 *   These methods change the settings inside SWFUpload
 *   They shouldn't need to be called in a setTimeout since they
 *   should not call back from Flash to JavaScript (except perhaps in a Debug call)
 *   and some need to return data so setTimeout won't work.
 */

/* Gets the file statistics object.	 It looks like this (where n = number):
	{
		files_queued: n,
		complete_uploads: n,
		upload_errors: n,
		uploads_cancelled: n,
		queue_errors: n
	}
*/
SWFUpload.prototype.getStats = function () {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.GetStats) === "function") {
		try {
			return movie_element.GetStats();
		}
		catch (ex) {
			this.debug("Could not call GetStats");
		}
	} else {
		this.debug("Could not find Flash element");
	}
};
SWFUpload.prototype.setStats = function (stats_object) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SetStats) === "function") {
		try {
			movie_element.SetStats(stats_object);
		}
		catch (ex) {
			this.debug("Could not call SetStats");
		}
	} else {
		this.debug("Could not find Flash element");
	}
};

SWFUpload.prototype.setCredentials = function(name, password) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SetCredentials) === "function") {
		try {
			return movie_element.SetCredentials(name, password);
		}
		catch (ex) {
			this.debug("Could not call SetCredentials");
		}
	} else {
		this.debug("Could not find Flash element");
	}
};

SWFUpload.prototype.getFile = function (file_id) {
	var movie_element = this.getMovieElement();
			if (typeof(file_id) === "number") {
				if (movie_element !== null && typeof(movie_element.GetFileByIndex) === "function") {
					try {
						return movie_element.GetFileByIndex(file_id);
					}
					catch (ex) {
						this.debug("Could not call GetFileByIndex");
					}
				} else {
					this.debug("Could not find Flash element");
				}
			} else {
				if (movie_element !== null && typeof(movie_element.GetFile) === "function") {
					try {
						return movie_element.GetFile(file_id);
					}
					catch (ex) {
						this.debug("Could not call GetFile");
					}
				} else {
					this.debug("Could not find Flash element");
				}
			}
};

SWFUpload.prototype.addFileParam = function (file_id, name, value) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.AddFileParam) === "function") {
		try {
			return movie_element.AddFileParam(file_id, name, value);
		}
		catch (ex) {
			this.debug("Could not call AddFileParam");
		}
	} else {
		this.debug("Could not find Flash element");
	}
};

SWFUpload.prototype.removeFileParam = function (file_id, name) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.RemoveFileParam) === "function") {
		try {
			return movie_element.RemoveFileParam(file_id, name);
		}
		catch (ex) {
			this.debug("Could not call AddFileParam");
		}
	} else {
		this.debug("Could not find Flash element");
	}

};

SWFUpload.prototype.setUploadURL = function (url) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SetUploadURL) === "function") {
		try {
			this.addSetting("upload_url", url);
			movie_element.SetUploadURL(this.getSetting("upload_url"));
		}
		catch (ex) {
			this.debug("Could not call SetUploadURL");
		}
	} else {
		this.debug("Could not find Flash element in setUploadURL");
	}
};

SWFUpload.prototype.setPostParams = function (param_object) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SetPostParams) === "function") {
		try {
			this.addSetting("post_params", param_object);
			movie_element.SetPostParams(this.getSetting("post_params"));
		}
		catch (ex) {
			this.debug("Could not call SetPostParams");
		}
	} else {
		this.debug("Could not find Flash element in SetPostParams");
	}
};

SWFUpload.prototype.setFileTypes = function (types, description) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SetFileTypes) === "function") {
		try {
			this.addSetting("file_types", types);
			this.addSetting("file_types_description", description);
			movie_element.SetFileTypes(this.getSetting("file_types"), this.getSetting("file_types_description"));
		}
		catch (ex) {
			this.debug("Could not call SetFileTypes");
		}
	} else {
		this.debug("Could not find Flash element in SetFileTypes");
	}
};

SWFUpload.prototype.setFileSizeLimit = function (file_size_limit) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SetFileSizeLimit) === "function") {
		try {
			this.addSetting("file_size_limit", file_size_limit);
			movie_element.SetFileSizeLimit(this.getSetting("file_size_limit"));
		}
		catch (ex) {
			this.debug("Could not call SetFileSizeLimit");
		}
	} else {
		this.debug("Could not find Flash element in SetFileSizeLimit");
	}
};

SWFUpload.prototype.setFileUploadLimit = function (file_upload_limit) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SetFileUploadLimit) === "function") {
		try {
			this.addSetting("file_upload_limit", file_upload_limit);
			movie_element.SetFileUploadLimit(this.getSetting("file_upload_limit"));
		}
		catch (ex) {
			this.debug("Could not call SetFileUploadLimit");
		}
	} else {
		this.debug("Could not find Flash element in SetFileUploadLimit");
	}
};

SWFUpload.prototype.setFileQueueLimit = function (file_queue_limit) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SetFileQueueLimit) === "function") {
		try {
			this.addSetting("file_queue_limit", file_queue_limit);
			movie_element.SetFileQueueLimit(this.getSetting("file_queue_limit"));
		}
		catch (ex) {
			this.debug("Could not call SetFileQueueLimit");
		}
	} else {
		this.debug("Could not find Flash element in SetFileQueueLimit");
	}
};

SWFUpload.prototype.setFilePostName = function (file_post_name) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SetFilePostName) === "function") {
		try {
			this.addSetting("file_post_name", file_post_name);
			movie_element.SetFilePostName(this.getSetting("file_post_name"));
		}
		catch (ex) {
			this.debug("Could not call SetFilePostName");
		}
	} else {
		this.debug("Could not find Flash element in SetFilePostName");
	}
};

SWFUpload.prototype.setDebugEnabled = function (debug_enabled) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.SetDebugEnabled) === "function") {
		try {
			this.addSetting("debug_enabled", debug_enabled);
			movie_element.SetDebugEnabled(this.getSetting("debug_enabled"));
		}
		catch (ex) {
			this.debug("Could not call SetDebugEnabled");
		}
	} else {
		this.debug("Could not find Flash element in SetDebugEnabled");
	}
};

/* *******************************
	Internal Event Callers
	Don't override these! These event callers ensure that your custom event handlers
	are called safely and in order.
******************************* */

/* This is the callback method that the Flash movie will call when it has been loaded and is ready to go.
   Calling this or showUI() "manually" will bypass the Flash Detection built in to SWFUpload.
   Use a ui_function setting if you want to control the UI loading after the flash has loaded.
*/
SWFUpload.prototype.flashReady = function () {
	// Check that the movie element is loaded correctly with its ExternalInterface methods defined
	var movie_element = this.getMovieElement();
	if (movie_element === null || typeof(movie_element.StartUpload) !== "function") {
		this.debug("ExternalInterface methods failed to initialize.");
		return;
	}
	
	var self = this;
	if (typeof(self.flashReady_handler) === "function") {
		this.eventQueue[this.eventQueue.length] = function() { self.flashReady_handler(); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	} else {
		this.debug("flashReady_handler event not defined");
	}
};

/*
	Event Queue.  Rather can call events directly from Flash they events are
	are placed in a queue and then executed.  This ensures that each event is
	executed in the order it was called which is not guarenteed when calling
	setTimeout.  Out of order events was especially problematic in Safari.
*/
SWFUpload.prototype.executeNextEvent = function () {
	var  f = this.eventQueue.shift();
	if (typeof(f) === "function") {
		f();
	}
}

/* This is a chance to do something before the browse window opens */
SWFUpload.prototype.fileDialogStart = function () {
	var self = this;
	if (typeof(self.fileDialogStart_handler) === "function") {
		this.eventQueue[this.eventQueue.length] = function() { self.fileDialogStart_handler(); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	} else {
		this.debug("fileDialogStart event not defined");
	}
};


/* Called when a file is successfully added to the queue. */
SWFUpload.prototype.fileQueued = function (file) {
	var self = this;
	if (typeof(self.fileQueued_handler) === "function") {
		this.eventQueue[this.eventQueue.length] = function() { self.fileQueued_handler(file); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	} else {
		this.debug("fileQueued event not defined");
	}
};


/* Handle errors that occur when an attempt to queue a file fails. */
SWFUpload.prototype.fileQueueError = function (file, error_code, message) {
	var self = this;
	if (typeof(self.fileQueueError_handler) === "function") {
		this.eventQueue[this.eventQueue.length] = function() {  self.fileQueueError_handler(file, error_code, message); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	} else {
		this.debug("fileQueueError event not defined");
	}
};

/* Called after the file dialog has closed and the selected files have been queued.
	You could call startUpload here if you want the queued files to begin uploading immediately. */
SWFUpload.prototype.fileDialogComplete = function (num_files_selected) {
	var self = this;
	if (typeof(self.fileDialogComplete_handler) === "function") {
		this.eventQueue[this.eventQueue.length] = function() { self.fileDialogComplete_handler(num_files_selected); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	} else {
		this.debug("fileDialogComplete event not defined");
	}
};

/* Gets called when a file upload is about to be started.  Return true to continue the upload. Return false to stop the upload.
	If you return false then uploadError and uploadComplete are called (like normal).
	
	This is a good place to do any file validation you need.
	*/
SWFUpload.prototype.uploadStart = function (file) {
	var self = this;
	if (typeof(self.fileDialogComplete_handler) === "function") {
		this.eventQueue[this.eventQueue.length] = function() { self.returnUploadStart(self.uploadStart_handler(file)); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	} else {
		this.debug("uploadStart event not defined");
	}
};

/* Note: Internal use only.  This function returns the result of uploadStart to
	flash.  Since returning values in the normal way can result in Flash/JS circular
	call issues we split up the call in a Timeout.  This is transparent from the API
	point of view.
*/
SWFUpload.prototype.returnUploadStart = function (return_value) {
	var movie_element = this.getMovieElement();
	if (movie_element !== null && typeof(movie_element.ReturnUploadStart) === "function") {
		try {
			movie_element.ReturnUploadStart(return_value);
		}
		catch (ex) {
			this.debug("Could not call ReturnUploadStart");
		}
	} else {
		this.debug("Could not find Flash element in returnUploadStart");
	}
};



/* Called during upload as the file progresses. Use this event to update your UI. */
SWFUpload.prototype.uploadProgress = function (file, bytes_complete, bytes_total) {
	var self = this;
	if (typeof(self.uploadProgress_handler) === "function") {
		this.eventQueue[this.eventQueue.length] = function() { self.uploadProgress_handler(file, bytes_complete, bytes_total); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	} else {
		this.debug("uploadProgress event not defined");
	}
};

/* Called when an error occurs during an upload. Use error_code and the SWFUpload.UPLOAD_ERROR constants to determine
   which error occurred. The uploadComplete event is called after an error code indicating that the next file is
   ready for upload.  For files cancelled out of order the uploadComplete event will not be called. */
SWFUpload.prototype.uploadError = function (file, error_code, message) {
	var self = this;
	if (typeof(this.uploadError_handler) === "function") {
		this.eventQueue[this.eventQueue.length] = function() { self.uploadError_handler(file, error_code, message); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	} else {
		this.debug("uploadError event not defined");
	}
};

/* This gets called when a file finishes uploading and the server-side upload script has completed and returned a 200
status code. Any text returned by the server is available in server_data.
**NOTE: The upload script MUST return some text or the uploadSuccess and uploadComplete events will not fire and the
upload will become 'stuck'. */
SWFUpload.prototype.uploadSuccess = function (file, server_data) {
	var self = this;
	if (typeof(self.uploadSuccess_handler) === "function") {
		this.eventQueue[this.eventQueue.length] = function() { self.uploadSuccess_handler(file, server_data); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	} else {
		this.debug("uploadSuccess event not defined");
	}
};

/* uploadComplete is called when the file is uploaded or an error occurred and SWFUpload is ready to make the next upload.
   If you want the next upload to start to automatically you can call startUpload() from this event. */
SWFUpload.prototype.uploadComplete = function (file) {
	var self = this;
	if (typeof(self.uploadComplete_handler) === "function") {
		this.eventQueue[this.eventQueue.length] = function() { self.uploadComplete_handler(file); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	} else {
		this.debug("uploadComplete event not defined");
	}
};

/* Called by SWFUpload JavaScript and Flash functions when debug is enabled. By default it writes messages to the
   internal debug console.  You can override this event and have messages written where you want. */
SWFUpload.prototype.debug = function (message) {
	var self = this;
	if (typeof(self.debug_handler) === "function") {
		this.eventQueue[this.eventQueue.length] = function() { self.debug_handler(message); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	} else {
		this.eventQueue[this.eventQueue.length] = function() { self.debugMessage(message); };
		setTimeout(function () { self.executeNextEvent();}, 0);
	}
};


/* **********************************
	Default Event Handlers.
	These event handlers are used by default if an overriding handler is
	not defined in the SWFUpload settings object.
	
	JS Note: even though these are defined on the SWFUpload object (rather than the prototype) they
	are attached (read: copied) to a SWFUpload instance and 'this' is given the proper context.
   ********************************** */

/* This is a special event handler that has no override in the settings.  Flash calls this when it has
   been loaded by the browser and is ready for interaction.  You should not override it.  If you need
   to do something with SWFUpload has loaded then use the swfupload_loaded_handler setting.
*/
SWFUpload.flashReady = function () {
	try {
		this.debug("Flash called back and is ready.");

		if (typeof(this.swfUploadLoaded_handler) === "function") {
			this.swfUploadLoaded_handler();
		}
	} catch (ex) {
		this.debug(ex);
	}
};

/* This is a chance to something immediately after SWFUpload has loaded.
   Like, hide the default/degraded upload form and display the SWFUpload form. */
SWFUpload.swfUploadLoaded = function () {
};

/* This is a chance to do something before the browse window opens */
SWFUpload.fileDialogStart = function () {
};


/* Called when a file is successfully added to the queue. */
SWFUpload.fileQueued = function (file) {
};


/* Handle errors that occur when an attempt to queue a file fails. */
SWFUpload.fileQueueError = function (file, error_code, message) {
	try {
		switch (error_code) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			this.debug("Error Code: Zero Byte File, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
			this.debug("Error Code: Upload limit reached, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			this.debug("Error Code: File extension is not allowed, Message: " + message);
			break;
		default:
			this.debug("Error Code: Unhandled error occured. Errorcode: " + error_code);
		}
	} catch (ex) {
		this.debug(ex);
	}
};

/* Called after the file dialog has closed and the selected files have been queued.
	You could call startUpload here if you want the queued files to begin uploading immediately. */
SWFUpload.fileDialogComplete = function (num_files_selected) {
};

/* Gets called when a file upload is about to be started.  Return true to continue the upload. Return false to stop the upload.
	If you return false then the uploadError callback is called and then uploadComplete (like normal).
	
	This is a good place to do any file validation you need.
	
	This is the only function that cannot be called on a setTimeout because it must return a value to Flash.
	You SHOULD NOT make any calls in to Flash (e.i, changing settings, getting stats, etc).  Flash Player bugs prevent
	calls in to Flash from working reliably.
*/
SWFUpload.uploadStart = function (file) {
	return true;
};

// Called during upload as the file progresses
SWFUpload.uploadProgress = function (file, bytes_complete, bytes_total) {
	this.debug("File Progress: " + file.id + ", Bytes: " + bytes_complete + ". Total: " + bytes_total);
};

/* This gets called when a file finishes uploading and the upload script has completed and returned a 200 status code.	Any text returned by the
server is available in server_data.	 The upload script must return some text or uploadSuccess will not fire (neither will uploadComplete). */
SWFUpload.uploadSuccess = function (file, server_data) {
	this.debug("Upload Success: " + file.id + ", Server: " + server_data);
};

/* This is called last.	 The file is uploaded or an error occurred and SWFUpload is ready to make the next upload.
	If you want to automatically start the next file just call startUpload from here.
*/
SWFUpload.uploadComplete = function (file) {
	this.debug("Upload Complete: " + file.id);
};

// Called by SWFUpload JavaScript and Flash functions when debug is enabled.
// Override this method in your settings to call your own debug message handler
SWFUpload.debug = function (message) {
	if (this.getSetting("debug_enabled")) {
		this.debugMessage(message);
	}
};

/* Called when an upload occurs during upload.  For HTTP errors 'message' will contain the HTTP STATUS CODE */
SWFUpload.uploadError = function (file, errcode, msg) {
	try {
		switch (errcode) {
		case SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND:
			this.debug("Error Code: File ID specified for upload was not found, Message: " + msg);
			break;
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + msg);
			break;
		case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
			this.debug("Error Code: No backend file, File name: " + file.name + ", Message: " + msg);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + msg);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + msg);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			this.debug("Error Code: Upload limit reached, File name: " + file.name + ", File size: " + file.size + ", Message: " + msg);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			this.debug("Error Code: Upload Initialization exception, File name: " + file.name + ", File size: " + file.size + ", Message: " + msg);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			this.debug("Error Code: uploadStart callback returned false, File name: " + file.name + ", File size: " + file.size + ", Message: " + msg);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			this.debug("Error Code: The file upload was cancelled, File name: " + file.name + ", File size: " + file.size + ", Message: " + msg);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			this.debug("Error Code: The file upload was stopped, File name: " + file.name + ", File size: " + file.size + ", Message: " + msg);
			break;
		default:
			this.debug("Error Code: Unhandled error occured. Errorcode: " + errcode);
		}
	} catch (ex) {
		this.debug(ex);
	}
};



/* **********************************
	Debug Console
	The debug console is a self contained, in page location
	for debug message to be sent.  The Debug Console adds
	itself to the body if necessary.

	The console is automatically scrolled as messages appear.
	
	You can override this console (to use FireBug's console for instance) by setting the debug event method to your own function
	that handles the debug message
   ********************************** */
SWFUpload.prototype.debugMessage = function (message) {
	var exception_message, exception_values;

	if (typeof(message) === "object" && typeof(message.name) === "string" && typeof(message.message) === "string") {
		exception_message = "";
		exception_values = [];
		for (var key in message) {
			exception_values.push(key + ": " + message[key]);
		}
		exception_message = exception_values.join("\n");
		exception_values = exception_message.split("\n");
		exception_message = "EXCEPTION: " + exception_values.join("\nEXCEPTION: ");
		SWFUpload.Console.writeLine(exception_message);
	} else {
		SWFUpload.Console.writeLine(message);
	}
};

SWFUpload.Console = {};
SWFUpload.Console.writeLine = function (message) {
	var console, documentForm;

	try {
		console = document.getElementById("SWFUpload_Console");

		if (!console) {
			documentForm = document.createElement("form");
			document.getElementsByTagName("body")[0].appendChild(documentForm);

			console = document.createElement("textarea");
			console.id = "SWFUpload_Console";
			console.style.fontFamily = "monospace";
			console.setAttribute("wrap", "off");
			console.wrap = "off";
			console.style.overflow = "auto";
			console.style.width = "700px";
			console.style.height = "350px";
			console.style.margin = "5px";
			documentForm.appendChild(console);
		}

		console.value += message + "\n";

		console.scrollTop = console.scrollHeight - console.clientHeight;
	} catch (ex) {
		alert("Exception: " + ex.name + " Message: " + ex.message);
	}
};
