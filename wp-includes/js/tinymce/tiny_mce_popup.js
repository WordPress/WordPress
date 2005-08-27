// Get tinyMCE window
var win = window.opener ? window.opener : window.dialogArguments;

var tinyMCE = null;
var tinyMCELang = null;

// Use top window if not defined
if (!win)
	win = top;

var tinyMCE = win.tinyMCE;
var tinyMCELang = win.tinyMCELang;

if (!tinyMCE)
	alert("tinyMCE object reference not found from popup.");

// Setup window openerer
window.opener = win;

// Setup title
var re = new RegExp('{|\\\$|}', 'g');
var title = document.title.replace(re, "");
if (typeof tinyMCELang[title] != "undefined") {
	var divElm = document.createElement("div");
	divElm.innerHTML = tinyMCELang[title];
	document.title = divElm.innerHTML;
}

// Setup dir
if (tinyMCELang['lang_dir'])
	document.dir = tinyMCELang['lang_dir'];

function TinyMCEPlugin_onLoad() {
	if (tinyMCE.getWindowArg('mce_replacevariables', true))
		document.body.innerHTML = tinyMCE.applyTemplate(document.body.innerHTML, tinyMCE.windowArgs);

	// Auto resize window
	if (tinyMCE.getWindowArg('mce_windowresize', true))
		TinyMCEPopup_autoResize();

	if (tinyMCE.settings["dialog_type"] == "window")
		window.focus();
}

function TinyMCEPopup_autoResize() {
	// Div mode, skip resize
	if (tinyMCE.settings["dialog_type"] == "div")
		return;

	var isMSIE = (navigator.appName == "Microsoft Internet Explorer");
	var isOpera = (navigator.userAgent.indexOf("Opera") != -1);

	if (isOpera)
		return;

	if (isMSIE) {
		try { window.resizeTo(10, 10); } catch (e) {}

		var elm = document.body;
		var width = elm.offsetWidth;
		var height = elm.offsetHeight;
		var dx = (elm.scrollWidth - width) + 4;
		var dy = elm.scrollHeight - height;

		try { window.resizeBy(dx, dy); } catch (e) {}
	} else {
		window.scrollBy(1000, 1000);
		if (window.scrollX > 0 || window.scrollY > 0) {
			window.resizeBy(window.innerWidth * 2, window.innerHeight * 2);
			window.sizeToContent();
			window.scrollTo(0, 0);
			var x = parseInt(screen.width / 2.0) - (window.outerWidth / 2.0);
			var y = parseInt(screen.height / 2.0) - (window.outerHeight / 2.0);
			window.moveTo(x, y);
		}
	}
}

// Re-patch it
if (tinyMCE.settings["dialog_type"] == "window") {
	tinyMCE.closeDialog = function() {
		// Remove div or close window
		if (tinyMCE.settings["dialog_type"] == "div") {
			var div = document.getElementById(tinyMCE._currentDialog);
			if (div)
				div.parentNode.removeChild(div);
		} else
			window.close();
	};
}

// Add onload trigger
tinyMCE.addEvent(window, "load", TinyMCEPlugin_onLoad);

// Output Popup CSS class
document.write('<link href="' + tinyMCE.getParam("popups_css") + '" rel="stylesheet" type="text/css">');
