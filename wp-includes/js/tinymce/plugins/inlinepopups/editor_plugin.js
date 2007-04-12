/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * Moxiecode DHTML Windows script.
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

// Patch openWindow, closeWindow TinyMCE functions

var TinyMCE_InlinePopupsPlugin = {
	getInfo : function() {
		return {
			longname : 'Inline Popups',
			author : 'Moxiecode Systems AB',
			authorurl : 'http://tinymce.moxiecode.com',
			infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/inlinepopups',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	}
};

tinyMCE.addPlugin("inlinepopups", TinyMCE_InlinePopupsPlugin);

// Patch openWindow, closeWindow TinyMCE functions

TinyMCE_Engine.prototype.orgOpenWindow = TinyMCE_Engine.prototype.openWindow;
TinyMCE_Engine.prototype.orgCloseWindow = TinyMCE_Engine.prototype.closeWindow;

TinyMCE_Engine.prototype.openWindow = function(template, args) {
	// Does the caller support inline
	if (args['inline'] != "yes" || tinyMCE.isOpera || tinyMCE.getParam("plugins").indexOf('inlinepopups') == -1) {
		mcWindows.selectedWindow = null;
		args['mce_inside_iframe'] = false;
		this.orgOpenWindow(template, args);
		return;
	}

	var url, resizable, scrollbars;

	args['mce_inside_iframe'] = true;
	tinyMCE.windowArgs = args;

	if (template['file'].charAt(0) != '/' && template['file'].indexOf('://') == -1)
		url = tinyMCE.baseURL + "/themes/" + tinyMCE.getParam("theme") + "/" + template['file'];
	else
		url = template['file'];

	if (!(width = parseInt(template['width'])))
		width = 320;

	if (!(height = parseInt(template['height'])))
		height = 200;

	if (!(minWidth = parseInt(template['minWidth'])))
		minWidth = 100;

	if (!(minHeight = parseInt(template['minHeight'])))
		minHeight = 100;

	resizable = (args && args['resizable']) ? args['resizable'] : "no";
	scrollbars = (args && args['scrollbars']) ? args['scrollbars'] : "no";

	height += 18;

	// Replace all args as variables in URL
	for (var name in args) {
		if (typeof(args[name]) == 'function')
			continue;

		url = tinyMCE.replaceVar(url, name, escape(args[name]));
	}

	var elm = document.getElementById(this.selectedInstance.editorId + '_parent');

	if (tinyMCE.hasPlugin('fullscreen') && this.selectedInstance.getData('fullscreen').enabled)
		pos = { absLeft: 0, absTop: 0 };
	else
		pos = tinyMCE.getAbsPosition(elm);

	// Center div in editor area
	pos.absLeft += Math.round((elm.firstChild.clientWidth / 2) - (width / 2));
	pos.absTop += Math.round((elm.firstChild.clientHeight / 2) - (height / 2));

	url += tinyMCE.settings['imp_version'] ? (url.indexOf('?')==-1?'?':'&') + 'ver=' + tinyMCE.settings['imp_version'] : ''; // WordPress cache buster

	mcWindows.open(url, mcWindows.idCounter++, "modal=yes,width=" + width+ ",height=" + height + ",resizable=" + resizable + ",scrollbars=" + scrollbars + ",statusbar=" + resizable + ",left=" + pos.absLeft + ",top=" + pos.absTop + ",minWidth=" + minWidth + ",minHeight=" + minHeight );
};

TinyMCE_Engine.prototype.closeWindow = function(win) {
	var gotit = false, n, w;
	for (n in mcWindows.windows) {
		w = mcWindows.windows[n];
		if (typeof(w) == 'function') continue;
		if (win.name == w.id + '_iframe') {
			w.close();
			gotit = true;
		}
	}
	if (!gotit)
		this.orgCloseWindow(win);

	tinyMCE.selectedInstance.getWin().focus(); 
};

TinyMCE_Engine.prototype.setWindowTitle = function(win_ref, title) {
	for (var n in mcWindows.windows) {
		var win = mcWindows.windows[n];
		if (typeof(win) == 'function')
			continue;

		if (win_ref.name == win.id + "_iframe")
			window.frames[win.id + "_iframe"].document.getElementById(win.id + '_title').innerHTML = title;
	}
};

// * * * * * TinyMCE_Windows classes below

// Windows handler
function TinyMCE_Windows() {
	this.settings = new Array();
	this.windows = new Array();
	this.isMSIE = (navigator.appName == "Microsoft Internet Explorer");
	this.isGecko = navigator.userAgent.indexOf('Gecko') != -1;
	this.isSafari = navigator.userAgent.indexOf('Safari') != -1;
	this.isMac = navigator.userAgent.indexOf('Mac') != -1;
	this.isMSIE5_0 = this.isMSIE && (navigator.userAgent.indexOf('MSIE 5.0') != -1);
	this.action = "none";
	this.selectedWindow = null;
	this.lastSelectedWindow = null;
	this.zindex = 1001;
	this.mouseDownScreenX = 0;
	this.mouseDownScreenY = 0;
	this.mouseDownLayerX = 0;
	this.mouseDownLayerY = 0;
	this.mouseDownWidth = 0;
	this.mouseDownHeight = 0;
	this.idCounter = 0;
};

TinyMCE_Windows.prototype.init = function(settings) {
	this.settings = settings;

	if (this.isMSIE)
		this.addEvent(document, "mousemove", mcWindows.eventDispatcher);
	else
		this.addEvent(window, "mousemove", mcWindows.eventDispatcher);

	this.addEvent(document, "mouseup", mcWindows.eventDispatcher);

	this.addEvent(window, "resize", mcWindows.eventDispatcher);
	this.addEvent(document, "scroll", mcWindows.eventDispatcher);

	this.doc = document;
};

TinyMCE_Windows.prototype.getBounds = function() {
	if (!this.bounds) {
		var vp = tinyMCE.getViewPort(window);
		var top, left, bottom, right, docEl = this.doc.documentElement;

		top    = vp.top;
		left   = vp.left;
		bottom = vp.height + top - 2;
		right  = vp.width  + left - 22; // TODO this number is platform dependant
		// x1, y1, x2, y2
		this.bounds = [left, top, right, bottom];
	}
	return this.bounds;
};

TinyMCE_Windows.prototype.clampBoxPosition = function(x, y, w, h, minW, minH) {
	var bounds = this.getBounds();

	x = Math.max(bounds[0], Math.min(bounds[2], x + w) - w);
	y = Math.max(bounds[1], Math.min(bounds[3], y + h) - h);

	return this.clampBoxSize(x, y, w, h, minW, minH);
};

TinyMCE_Windows.prototype.clampBoxSize = function(x, y, w, h, minW, minH) {
	var bounds = this.getBounds();

	return [
		x, y,
		Math.max(minW, Math.min(bounds[2], x + w) - x),
		Math.max(minH, Math.min(bounds[3], y + h) - y)
	];
};

TinyMCE_Windows.prototype.getParam = function(name, default_value) {
	var value = null;

	value = (typeof(this.settings[name]) == "undefined") ? default_value : this.settings[name];

	// Fix bool values
	if (value == "true" || value == "false")
		return (value == "true");

	return value;
};

TinyMCE_Windows.prototype.eventDispatcher = function(e) {
	e = typeof(e) == "undefined" ? window.event : e;

	if (mcWindows.selectedWindow == null)
		return;

	// Switch focus
	if (mcWindows.isGecko && e.type == "mousedown") {
		var elm = e.currentTarget;

		for (var n in mcWindows.windows) {
			var win = mcWindows.windows[n];

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
		case "scroll":
		case "resize":
			if (mcWindows.clampUpdateTimeout)
				clearTimeout(mcWindows.clampUpdateTimeout);
			mcWindows.clampEventType = e.type;
			mcWindows.clampUpdateTimeout =
				setTimeout(function () {mcWindows.updateClamping()}, 100);
			break;
	}
};

TinyMCE_Windows.prototype.updateClamping = function () {
	var clamp, oversize, etype = mcWindows.clampEventType;

	this.bounds = null; // Recalc window bounds on resize/scroll
	this.clampUpdateTimeout = null;

	for (var n in this.windows) {
		win = this.windows[n];
		if (typeof(win) == 'function' || ! win.winElement) continue;

		clamp = mcWindows.clampBoxPosition(
			win.left, win.top,
			win.winElement.scrollWidth,
			win.winElement.scrollHeight,
			win.features.minWidth,
			win.features.minHeight
		);
		oversize = (
			clamp[2] != win.winElement.scrollWidth ||
			clamp[3] != win.winElement.scrollHeight
		) ? true : false;

		if (!oversize || win.features.resizable == "yes" || etype != "scroll")
			win.moveTo(clamp[0], clamp[1]);
		if (oversize && win.features.resizable == "yes")
			win.resizeTo(clamp[2], clamp[3]);
	}
};

TinyMCE_Windows.prototype.addEvent = function(obj, name, handler) {
	if (this.isMSIE)
		obj.attachEvent("on" + name, handler);
	else
		obj.addEventListener(name, handler, false);
};

TinyMCE_Windows.prototype.cancelEvent = function(e) {
	if (this.isMSIE) {
		e.returnValue = false;
		e.cancelBubble = true;
	} else
		e.preventDefault();
};

TinyMCE_Windows.prototype.parseFeatures = function(opts) {
	// Cleanup the options
	opts = opts.toLowerCase();
	opts = opts.replace(/;/g, ",");
	opts = opts.replace(/[^0-9a-z=,]/g, "");

	var optionChunks = opts.split(',');
	var options = new Array();

	options['left'] = "10";
	options['top'] = "10";
	options['width'] = "300";
	options['height'] = "300";
	options['minwidth'] = "100";
	options['minheight'] = "100";
	options['resizable'] = "yes";
	options['minimizable'] = "yes";
	options['maximizable'] = "yes";
	options['close'] = "yes";
	options['movable'] = "yes";
	options['statusbar'] = "yes";
	options['scrollbars'] = "auto";
	options['modal'] = "no";

	if (opts == "")
		return options;

	for (var i=0; i<optionChunks.length; i++) {
		var parts = optionChunks[i].split('=');

		if (parts.length == 2)
			options[parts[0]] = parts[1];
	}

	options['left'] = parseInt(options['left']);
	options['top'] = parseInt(options['top']);
	options['width'] = parseInt(options['width']);
	options['height'] = parseInt(options['height']);
	options['minWidth'] = parseInt(options['minwidth']);
	options['minHeight'] = parseInt(options['minheight']);

	return options;
};

TinyMCE_Windows.prototype.open = function(url, name, features) {
	this.lastSelectedWindow = this.selectedWindow;

	var win = new TinyMCE_Window();
	var winDiv, html = "", id;
	var imgPath = this.getParam("images_path");

	features = this.parseFeatures(features);

	// Clamp specified dimensions
	var clamp = mcWindows.clampBoxPosition(
		features['left'], features['top'],
		features['width'], features['height'],
		features['minWidth'], features['minHeight']
	);

	features['left'] = clamp[0];
	features['top'] = clamp[1];

	if (features['resizable'] == "yes") {
		features['width'] = clamp[2];
		features['height'] = clamp[3];
	}

	// Create div
	id = "mcWindow_" + name;
	win.deltaHeight = 18;

	if (features['statusbar'] == "yes") {
		win.deltaHeight += 13;

		if (this.isMSIE)
			win.deltaHeight += 1;
	}

	width = parseInt(features['width']);
	height = parseInt(features['height'])-win.deltaHeight;

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
	if (this.isMac) html += '<style type="text/css">.mceWindowTitle{float:none;margin:0;width:100%;text-align:center;}.mceWindowClose{float:none;position:absolute;left:0px;top:0px;}</style>';
	html += '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
	html += '<link href="' + this.getParam("css_file") + '" rel="stylesheet" type="text/css" />';
	html += '</head>';
	html += '<body onload="parent.mcWindows.onLoad(\'' + name + '\');">';

	html += '<div id="' + id + '_container" class="mceWindow">';
	html += '<div id="' + id + '_head" class="mceWindowHead" onmousedown="parent.mcWindows.windows[\'' + name + '\'].focus();">';
	html += '  <div id="' + id + '_title" class="mceWindowTitle"';
	html += '  onselectstart="return false;" unselectable="on" style="-moz-user-select: none !important;"></div>';
	html += '    <div class="mceWindowHeadTools">';
	html += '      <a href="javascript:parent.mcWindows.windows[\'' + name + '\'].close();" target="_self" onmousedown="return false;" class="mceWindowClose"><img border="0" src="' + imgPath + '/window_close.gif" /></a>';
	if (features['resizable'] == "yes" && features['maximizable'] == "yes")
		html += '      <a href="javascript:parent.mcWindows.windows[\'' + name + '\'].maximize();" target="_self" onmousedown="return false;" class="mceWindowMaximize"><img border="0" src="' + imgPath + '/window_maximize.gif" /></a>';
	// html += '      <a href="javascript:mcWindows.windows[\'' + name + '\'].minimize();" target="_self" onmousedown="return false;" class="mceWindowMinimize"></a>';
	html += '    </div>';
	html += '</div><div id="' + id + '_body" class="mceWindowBody" style="width: ' + width + 'px; height: ' + height + 'px;">';
	html += '<iframe id="' + id + '_iframe" name="' + id + '_iframe" frameborder="0" width="' + iframeWidth + '" height="' + iframeHeight + '" src="' + url + '" class="mceWindowBodyIframe" scrolling="' + features['scrollbars'] + '"></iframe></div>';

	if (features['statusbar'] == "yes") {
		html += '<div id="' + id + '_statusbar" class="mceWindowStatusbar" onmousedown="parent.mcWindows.windows[\'' + name + '\'].focus();">';

		if (features['resizable'] == "yes") {
			if (this.isGecko)
				html += '<div id="' + id + '_resize" class="mceWindowResize"><div style="background-image: url(\'' + imgPath + '/window_resize.gif\'); width: 12px; height: 12px;"></div></div>';
			else
				html += '<div id="' + id + '_resize" class="mceWindowResize"><img onmousedown="parent.mcWindows.windows[\'' + name + '\'].focus();" border="0" src="' + imgPath + '/window_resize.gif" /></div>';
		}

		html += '</div>';
	}

	html += '</div>';

	html += '</body>';
	html += '</html>';

	// Create iframe
	this.createFloatingIFrame(id, features['left'], features['top'], features['width'], features['height'], html);
};

// Blocks the document events by placing a image over the whole document
TinyMCE_Windows.prototype.setDocumentLock = function(state) {
	var elm = document.getElementById('mcWindowEventBlocker');

	if (state) {
		if (elm == null) {
			elm = document.createElement("div");

			elm.id = "mcWindowEventBlocker";
			elm.style.position = "absolute";
			elm.style.left = "0";
			elm.style.top = "0";

			document.body.appendChild(elm);
		}

		elm.style.display = "none";

		var imgPath = this.getParam("images_path");
		var width = document.body.clientWidth;
		var height = document.body.clientHeight;

		elm.style.width = width;
		elm.style.height = height;
		elm.innerHTML = '<img src="' + imgPath + '/spacer.gif" width="' + width + '" height="' + height + '" />';

		elm.style.zIndex = mcWindows.zindex-1;
		elm.style.display = "block";
	} else if (elm != null) {
		if (mcWindows.windows.length == 0)
			elm.parentNode.removeChild(elm);
		else
			elm.style.zIndex = mcWindows.zindex-1;
	}
};

// Gets called when wrapper iframe is initialized
TinyMCE_Windows.prototype.onLoad = function(name) {
	var win = mcWindows.windows[name];
	var id = "mcWindow_" + name;
	var wrapperIframe = window.frames[id + "_iframe"].frames[0];
	var wrapperDoc = window.frames[id + "_iframe"].document;
	var doc = window.frames[id + "_iframe"].document;
	var winDiv = document.getElementById("mcWindow_" + name + "_div");
	var realIframe = window.frames[id + "_iframe"].frames[0];

	// Set window data
	win.id = "mcWindow_" + name;
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

	if (win.resizeElement != null)
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

	// Dispatch open window event
	var func = this.getParam("on_open_window", "");
	if (func != "")
		eval(func + "(win);");

	win.focus();

	if (win.features['modal'] == "yes")
		mcWindows.setDocumentLock(true);
};

TinyMCE_Windows.prototype.createFloatingIFrame = function(id_prefix, left, top, width, height, html) {
	var iframe = document.createElement("iframe");
	var div = document.createElement("div"), doc;

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
	// iframe.setAttribute("src", "../jscripts/tiny_mce/blank.htm");
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
			var doc = window.frames[id_prefix + '_iframe'].document;
			doc.open();
			doc.write(html);
			doc.close();
		}, 10);
	} else {
		doc = window.frames[id_prefix + '_iframe'].window.document;
		doc.open();
		doc.write(html);
		doc.close();
	}

	div.style.display = "block";

	return div;
};

// Window instance
function TinyMCE_Window() {
};

TinyMCE_Window.prototype.focus = function() {
	if (this != mcWindows.selectedWindow) {
		this.winElement.style.zIndex = ++mcWindows.zindex;
		mcWindows.lastSelectedWindow = mcWindows.selectedWindow;
		mcWindows.selectedWindow = this;
	}
};

TinyMCE_Window.prototype.minimize = function() {
};

TinyMCE_Window.prototype.maximize = function() {
	if (this.restoreSize) {
		this.moveTo(this.restoreSize[0], this.restoreSize[1]);
		this.resizeTo(this.restoreSize[2], this.restoreSize[3]);
		this.updateClamping();
		this.restoreSize = null;
	} else {
		var bounds = mcWindows.getBounds();
		this.restoreSize = [
			this.left, this.top,
			this.winElement.scrollWidth,
			this.winElement.scrollHeight
		];
		this.moveTo(bounds[0], bounds[1]);
		this.resizeTo(
			bounds[2] - bounds[0],
			bounds[3] - bounds[1]
		);
	}
};

TinyMCE_Window.prototype.startResize = function() {
	mcWindows.action = "resize";
};

TinyMCE_Window.prototype.startMove = function(e) {
	mcWindows.action = "move";
};

TinyMCE_Window.prototype.close = function() {
	if (this.frame && this.frame['tinyMCEPopup'])
		this.frame['tinyMCEPopup'].restoreSelection();

	if (mcWindows.lastSelectedWindow != null)
		mcWindows.lastSelectedWindow.focus();

	var mcWindowsNew = new Array();
	for (var n in mcWindows.windows) {
		var win = mcWindows.windows[n];
		if (typeof(win) == 'function')
			continue;

		if (win.name != this.name)
			mcWindowsNew[n] = win;
	}

	mcWindows.windows = mcWindowsNew;

	// alert(mcWindows.doc.getElementById(this.id + "_iframe"));

	var e = mcWindows.doc.getElementById(this.id + "_iframe");
	e.parentNode.removeChild(e);

	var e = mcWindows.doc.getElementById(this.id + "_div");
	e.parentNode.removeChild(e);

	mcWindows.setDocumentLock(false);
};

TinyMCE_Window.prototype.onMouseMove = function(e) {
	var clamp;
	// Calculate real X, Y
	var dx = e.screenX - mcWindows.mouseDownScreenX;
	var dy = e.screenY - mcWindows.mouseDownScreenY;

	switch (mcWindows.action) {
		case "resize":
			clamp = mcWindows.clampBoxSize(
				this.left, this.top,
				mcWindows.mouseDownWidth + (e.screenX - mcWindows.mouseDownScreenX),
				mcWindows.mouseDownHeight + (e.screenY - mcWindows.mouseDownScreenY),
				this.features.minWidth, this.features.minHeight
			);

			this.resizeTo(clamp[2], clamp[3]);

			mcWindows.cancelEvent(e);
			break;

		case "move":
			this.left = mcWindows.mouseDownLayerX + (e.screenX - mcWindows.mouseDownScreenX);
			this.top = mcWindows.mouseDownLayerY + (e.screenY - mcWindows.mouseDownScreenY);
			this.updateClamping();

			mcWindows.cancelEvent(e);
			break;
	}
};

TinyMCE_Window.prototype.moveTo = function (x, y) {
	this.left = x;
	this.top = y;

	this.winElement.style.left = this.left + "px";
	this.winElement.style.top = this.top + "px";
};

TinyMCE_Window.prototype.resizeTo = function (width, height) {
	this.wrapperIFrameElement.style.width = (width+2) + 'px';
	this.wrapperIFrameElement.style.height = (height+2) + 'px';
	this.wrapperIFrameElement.width = width+2;
	this.wrapperIFrameElement.height = height+2;
	this.winElement.style.width = width + 'px';
	this.winElement.style.height = height + 'px';

	height = height - this.deltaHeight;

	this.containerElement.style.width = width + 'px';
	this.iframeElement.style.width = width + 'px';
	this.iframeElement.style.height = height + 'px';
	this.bodyElement.style.width = width + 'px';
	this.bodyElement.style.height = height + 'px';
	this.headElement.style.width = width + 'px';
	//this.statusElement.style.width = width + 'px';
};

TinyMCE_Window.prototype.updateClamping = function () {
	var clamp, oversize;

	clamp = mcWindows.clampBoxPosition(
		this.left, this.top,
		this.winElement.scrollWidth,
		this.winElement.scrollHeight,
		this.features.minWidth, this.features.minHeight
	);
	oversize = (
		clamp[2] != this.winElement.scrollWidth ||
		clamp[3] != this.winElement.scrollHeight
	) ? true : false;

	this.moveTo(clamp[0], clamp[1]);
	if (this.features.resizable == "yes" && oversize)
		this.resizeTo(clamp[2], clamp[3]);
};

function debug(msg) {
	document.getElementById('debug').value += msg + "\n";
}

TinyMCE_Window.prototype.onMouseUp = function(e) {
	mcWindows.action = "none";
};

TinyMCE_Window.prototype.onFocus = function(e) {
	// Gecko only handler
	var winRef = e.currentTarget;

	for (var n in mcWindows.windows) {
		var win = mcWindows.windows[n];
		if (typeof(win) == 'function')
			continue;

		if (winRef.name == win.id + "_iframe") {
			win.focus();
			return;
		}
	}
};

TinyMCE_Window.prototype.onMouseDown = function(e) {
	var elm = mcWindows.isMSIE ? this.wrapperFrame.event.srcElement : e.target;

	mcWindows.mouseDownScreenX = e.screenX;
	mcWindows.mouseDownScreenY = e.screenY;
	mcWindows.mouseDownLayerX = this.left;
	mcWindows.mouseDownLayerY = this.top;
	mcWindows.mouseDownWidth = parseInt(this.winElement.style.width);
	mcWindows.mouseDownHeight = parseInt(this.winElement.style.height);

	if (this.resizeElement != null && elm == this.resizeElement.firstChild)
		this.startResize(e);
	else
		this.startMove(e);

	mcWindows.cancelEvent(e);
};

// Global instance
var mcWindows = new TinyMCE_Windows();

// Initialize windows
mcWindows.init({
	images_path : tinyMCE.baseURL + "/plugins/inlinepopups/images",
	css_file : tinyMCE.baseURL + "/plugins/inlinepopups/css/inlinepopup.css"
});
