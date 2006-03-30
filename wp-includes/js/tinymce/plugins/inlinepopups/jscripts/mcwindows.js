/**
 * $RCSfile: mcwindows.js,v $
 * $Revision: 1.2 $
 * $Date: 2005/10/18 13:59:43 $
 *
 * Moxiecode DHTML Windows script.
 *
 * @author Moxiecode
 * @copyright Copyright © 2004, Moxiecode Systems AB, All rights reserved.
 */

// Windows handler
function MCWindows() {
	this.settings = new Array();
	this.windows = new Array();
	this.isMSIE = (navigator.appName == "Microsoft Internet Explorer");
	this.isGecko = navigator.userAgent.indexOf('Gecko') != -1;
	this.isSafari = navigator.userAgent.indexOf('Safari') != -1;
	this.isMac = navigator.userAgent.indexOf('Mac') != -1;
	this.isMSIE5_0 = this.isMSIE && (navigator.userAgent.indexOf('MSIE 5.0') != -1);
	this.action = "none";
	this.selectedWindow = null;
	this.zindex = 100;
	this.mouseDownScreenX = 0;
	this.mouseDownScreenY = 0;
	this.mouseDownLayerX = 0;
	this.mouseDownLayerY = 0;
	this.mouseDownWidth = 0;
	this.mouseDownHeight = 0;
};

MCWindows.prototype.init = function(settings) {
	this.settings = settings;

	if (this.isMSIE)
		this.addEvent(document, "mousemove", mcWindows.eventDispatcher);
	else
		this.addEvent(window, "mousemove", mcWindows.eventDispatcher);

	this.addEvent(document, "mouseup", mcWindows.eventDispatcher);
};

MCWindows.prototype.getParam = function(name, default_value) {
	var value = null;

	value = (typeof(this.settings[name]) == "undefined") ? default_value : this.settings[name];

	// Fix bool values
	if (value == "true" || value == "false")
		return (value == "true");

	return value;
};

MCWindows.prototype.eventDispatcher = function(e) {
	e = typeof(e) == "undefined" ? window.event : e;

	if (mcWindows.selectedWindow == null)
		return;

	// Switch focus
	if (mcWindows.isGecko && e.type == "mousedown") {
		var elm = e.currentTarget;

		for (var n in mcWindows.windows) {
			var win = mcWindows.windows[n];
			if (typeof(win) == 'function')
				continue;

			if (win.headElement == elm || win.resizeElement == elm) {
				win.focus();
				break;
			}
		}
	}

	switch (e.type) {
		case "mousemove":
			mcWindows.selectedWindow.onMouseMove(e);
			break;

		case "mouseup":
			mcWindows.selectedWindow.onMouseUp(e);
			break;

		case "mousedown":
			mcWindows.selectedWindow.onMouseDown(e);
			break;

		case "focus":
			mcWindows.selectedWindow.onFocus(e);
			break;
	}
}

MCWindows.prototype.addEvent = function(obj, name, handler) {
	if (this.isMSIE)
		obj.attachEvent("on" + name, handler);
	else
		obj.addEventListener(name, handler, true);
};

MCWindows.prototype.cancelEvent = function(e) {
	if (this.isMSIE) {
		e.returnValue = false;
		e.cancelBubble = true;
	} else
		e.preventDefault();
};

MCWindows.prototype.parseFeatures = function(opts) {
	// Cleanup the options
	opts = opts.toLowerCase();
	opts = opts.replace(/;/g, ",");
	opts = opts.replace(/[^0-9a-z=,]/g, "");

	var optionChunks = opts.split(',');
	var options = new Array();

	options['left'] = 10;
	options['top'] = 10;
	options['width'] = 300;
	options['height'] = 300;
	options['resizable'] = true;
	options['minimizable'] = true;
	options['maximizable'] = true;
	options['close'] = true;
	options['movable'] = true;

	if (opts == "")
		return options;

	for (var i=0; i<optionChunks.length; i++) {
		var parts = optionChunks[i].split('=');

		if (parts.length == 2)
			options[parts[0]] = parts[1];
	}

	return options;
};

MCWindows.prototype.open = function(url, name, features) {
	var win = new MCWindow();
	var winDiv, html = "", id;

	features = this.parseFeatures(features);

	// Create div
	id = "mcWindow_" + name;

	width = parseInt(features['width']);
	height = parseInt(features['height'])-12-19;

	if (this.isMSIE)
		width -= 2;

	// Setup first part of window
	win.id = id;
	win.url = url;
	win.name = name;
	win.features = features;
	this.windows[name] = win;

	iframeWidth = width;
	iframeHeight = height;

	// Create inner content
	html += '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">';
	html += '<html>';
	html += '<head>';
	html += '<title>Wrapper iframe</title>';
	html += '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
	html += '<link href="../jscripts/tiny_mce/themes/advanced/css/editor_ui.css" rel="stylesheet" type="text/css" />';
	html += '</head>';
	html += '<body onload="parent.mcWindows.onLoad(\'' + name + '\');">';

	html += '<div id="' + id + '_container" class="mceWindow">';
	html += '<div id="' + id + '_head" class="mceWindowHead" onmousedown="parent.mcWindows.windows[\'' + name + '\'].focus();">';
	html += '  <div id="' + id + '_title" class="mceWindowTitle"';
	html += '  onselectstart="return false;" unselectable="on" style="-moz-user-select: none !important;">No name window</div>';
	html += '    <div class="mceWindowHeadTools">';
	html += '      <a href="javascript:parent.mcWindows.windows[\'' + name + '\'].close();" onmousedown="return false;" class="mceWindowClose"><img border="0" src="../jscripts/tiny_mce/themes/advanced/images/window_close.gif" /></a>';
//	html += '      <a href="javascript:mcWindows.windows[\'' + name + '\'].maximize();" onmousedown="return false;" class="mceWindowMaximize"></a>';
//	html += '      <a href="javascript:mcWindows.windows[\'' + name + '\'].minimize();" onmousedown="return false;" class="mceWindowMinimize"></a>';
	html += '    </div>';
	html += '</div><div id="' + id + '_body" class="mceWindowBody" style="width: ' + width + 'px; height: ' + height + 'px;">';
	html += '<iframe id="' + id + '_iframe" name="' + id + '_iframe" onfocus="parent.mcWindows.windows[\'' + name + '\'].focus();" frameborder="0" width="' + iframeWidth + '" height="' + iframeHeight + '" src="' + url + '" class="mceWindowBodyIframe"></iframe></div>';
	html += '<div id="' + id + '_statusbar" class="mceWindowStatusbar" onmousedown="parent.mcWindows.windows[\'' + name + '\'].focus();">';
	html += '<div id="' + id + '_resize" class="mceWindowResize"><img onmousedown="parent.mcWindows.windows[\'' + name + '\'].focus();" border="0" src="../jscripts/tiny_mce/themes/advanced/images/window_resize.gif" /></div>';
	html += '</div>';
	html += '</div>';

	html += '</body>';
	html += '</html>';

	// Create iframe
	this.createFloatingIFrame(id, features['left'], features['top'], features['width'], features['height'], html);
};

// Gets called when wrapper iframe is initialized
MCWindows.prototype.onLoad = function(name) {
	var win = mcWindows.windows[name];
	var id = "mcWindow_" + name;
	var wrapperIframe = window.frames[id + "_iframe"].frames[0];
	var wrapperDoc = window.frames[id + "_iframe"].document;
	var doc = window.frames[id + "_iframe"].document;
	var winDiv = document.getElementById("mcWindow_" + name + "_div");
	var realIframe = window.frames[id + "_iframe"].frames[0];

	// Set window data
	win.id = "mcWindow_" + name + "_iframe";
	win.winElement = winDiv;
	win.bodyElement = doc.getElementById(id + '_body');
	win.iframeElement = doc.getElementById(id + '_iframe');
	win.headElement = doc.getElementById(id + '_head');
	win.titleElement = doc.getElementById(id + '_title');
	win.resizeElement = doc.getElementById(id + '_resize');
	win.containerElement = doc.getElementById(id + '_container');
	win.left = win.features['left'];
	win.top = win.features['top'];
	win.frame = window.frames[id + '_iframe'].frames[0];
	win.wrapperFrame = window.frames[id + '_iframe'];
	win.wrapperIFrameElement = document.getElementById(id + "_iframe");

	// Add event handlers
	mcWindows.addEvent(win.headElement, "mousedown", mcWindows.eventDispatcher);
	mcWindows.addEvent(win.resizeElement, "mousedown", mcWindows.eventDispatcher);

	if (mcWindows.isMSIE) {
		mcWindows.addEvent(realIframe.document, "mousemove", mcWindows.eventDispatcher);
		mcWindows.addEvent(realIframe.document, "mouseup", mcWindows.eventDispatcher);
	} else {
		mcWindows.addEvent(realIframe, "mousemove", mcWindows.eventDispatcher);
		mcWindows.addEvent(realIframe, "mouseup", mcWindows.eventDispatcher);
		mcWindows.addEvent(realIframe, "focus", mcWindows.eventDispatcher);
	}

	for (var i=0; i<window.frames.length; i++) {
		if (!window.frames[i]._hasMouseHandlers) {
			if (mcWindows.isMSIE) {
				mcWindows.addEvent(window.frames[i].document, "mousemove", mcWindows.eventDispatcher);
				mcWindows.addEvent(window.frames[i].document, "mouseup", mcWindows.eventDispatcher);
			} else {
				mcWindows.addEvent(window.frames[i], "mousemove", mcWindows.eventDispatcher);
				mcWindows.addEvent(window.frames[i], "mouseup", mcWindows.eventDispatcher);
			}

			window.frames[i]._hasMouseHandlers = true;
		}
	}

	if (mcWindows.isMSIE) {
		mcWindows.addEvent(win.frame.document, "mousemove", mcWindows.eventDispatcher);
		mcWindows.addEvent(win.frame.document, "mouseup", mcWindows.eventDispatcher);
	} else {
		mcWindows.addEvent(win.frame, "mousemove", mcWindows.eventDispatcher);
		mcWindows.addEvent(win.frame, "mouseup", mcWindows.eventDispatcher);
		mcWindows.addEvent(win.frame, "focus", mcWindows.eventDispatcher);
	}

	this.selectedWindow = win;
};

MCWindows.prototype.createFloatingIFrame = function(id_prefix, left, top, width, height, html) {
	var iframe = document.createElement("iframe");
	var div = document.createElement("div");

	width = parseInt(width);
	height = parseInt(height)+1;

	// Create wrapper div
	div.setAttribute("id", id_prefix + "_div");
	div.setAttribute("width", width);
	div.setAttribute("height", (height));
	div.style.position = "absolute";
	div.style.left = left + "px";
	div.style.top = top + "px";
	div.style.width = width + "px";
	div.style.height = (height) + "px";
	div.style.backgroundColor = "white";
	div.style.display = "none";

	if (this.isGecko) {
		iframeWidth = width + 2;
		iframeHeight = height + 2;
	} else {
		iframeWidth = width;
		iframeHeight = height + 1;
	}

	// Create iframe
	iframe.setAttribute("id", id_prefix + "_iframe");
	iframe.setAttribute("name", id_prefix + "_iframe");
	iframe.setAttribute("border", "0");
	iframe.setAttribute("frameBorder", "0");
	iframe.setAttribute("marginWidth", "0");
	iframe.setAttribute("marginHeight", "0");
	iframe.setAttribute("leftMargin", "0");
	iframe.setAttribute("topMargin", "0");
	iframe.setAttribute("width", iframeWidth);
	iframe.setAttribute("height", iframeHeight);
//	iframe.setAttribute("src", "../jscripts/tiny_mce/blank.htm");
	// iframe.setAttribute("allowtransparency", "false");
	iframe.setAttribute("scrolling", "no");
	iframe.style.width = iframeWidth + "px";
	iframe.style.height = iframeHeight + "px";
	iframe.style.backgroundColor = "white";
	div.appendChild(iframe);

	document.body.appendChild(div);

	// Fixed MSIE 5.0 issue
	div.innerHTML = div.innerHTML;

	if (this.isSafari) {
		// Give Safari some time to setup
		window.setTimeout(function() {
			doc = window.frames[id_prefix + '_iframe'].document;
			doc.open();
			doc.write(html);
			doc.close();
		}, 10);
	} else {
		doc = window.frames[id_prefix + '_iframe'].window.document
		doc.open();
		doc.write(html);
		doc.close();
	}

	div.style.display = "block";

	return div;
};

// Window instance
function MCWindow() {
};

MCWindow.prototype.focus = function() {
	this.winElement.style.zIndex = mcWindows.zindex++;
	mcWindows.selectedWindow = this;
};

MCWindow.prototype.minimize = function() {
};

MCWindow.prototype.maximize = function() {
	
};

MCWindow.prototype.startResize = function() {
	mcWindows.action = "resize";
};

MCWindow.prototype.startMove = function(e) {
	mcWindows.action = "move";
};

MCWindow.prototype.close = function() {
	document.body.removeChild(this.winElement);
	mcWindows.windows[this.name] = null;
};

MCWindow.prototype.onMouseMove = function(e) {
	var scrollX = 0;//this.doc.body.scrollLeft;
	var scrollY = 0;//this.doc.body.scrollTop;

	// Calculate real X, Y
	var dx = e.screenX - mcWindows.mouseDownScreenX;
	var dy = e.screenY - mcWindows.mouseDownScreenY;

	switch (mcWindows.action) {
		case "resize":
			width = mcWindows.mouseDownWidth + (e.screenX - mcWindows.mouseDownScreenX);
			height = mcWindows.mouseDownHeight + (e.screenY - mcWindows.mouseDownScreenY);

			width = width < 100 ? 100 : width;
			height = height < 100 ? 100 : height;

			this.wrapperIFrameElement.style.width = width+2;
			this.wrapperIFrameElement.style.height = height+2;
			this.wrapperIFrameElement.width = width+2;
			this.wrapperIFrameElement.height = height+2;
			this.winElement.style.width = width;
			this.winElement.style.height = height;

			height = height-12-19;

			this.containerElement.style.width = width;

			this.iframeElement.style.width = width;
			this.iframeElement.style.height = height;
			this.bodyElement.style.width = width;
			this.bodyElement.style.height = height;
			this.headElement.style.width = width;
			//this.statusElement.style.width = width;

			mcWindows.cancelEvent(e);
			break;

		case "move":
			this.left = mcWindows.mouseDownLayerX + (e.screenX - mcWindows.mouseDownScreenX);
			this.top = mcWindows.mouseDownLayerY + (e.screenY - mcWindows.mouseDownScreenY);
			this.winElement.style.left = this.left + "px";
			this.winElement.style.top = this.top + "px";

			mcWindows.cancelEvent(e);
			break;
	}
};

MCWindow.prototype.onMouseUp = function(e) {
	mcWindows.action = "none";
};

MCWindow.prototype.onFocus = function(e) {
	// Gecko only handler
	var winRef = e.currentTarget;

	for (var n in mcWindows.windows) {
		var win = mcWindows.windows[n];
		if (typeof(win) == 'function')
			continue;

		if (winRef.name == win.id) {
			win.focus();
			return;
		}
	}
};

MCWindow.prototype.onMouseDown = function(e) {
	var elm = mcWindows.isMSIE ? this.wrapperFrame.event.srcElement : e.target;

	var scrollX = 0;//this.doc.body.scrollLeft;
	var scrollY = 0;//this.doc.body.scrollTop;

	mcWindows.mouseDownScreenX = e.screenX;
	mcWindows.mouseDownScreenY = e.screenY;
	mcWindows.mouseDownLayerX = this.left;
	mcWindows.mouseDownLayerY = this.top;
	mcWindows.mouseDownWidth = parseInt(this.winElement.style.width);
	mcWindows.mouseDownHeight = parseInt(this.winElement.style.height);

	if (elm == this.resizeElement.firstChild)
		this.startResize(e);
	else
		this.startMove(e);

	mcWindows.cancelEvent(e);
};

// Global instance
var mcWindows = new MCWindows();
