/**
 * $RCSfile: tiny_mce_src.js,v $
 * $Revision: 1.215 $
 * $Date: 2005/06/23 12:04:41 $
 *
 * @author Moxiecode
 * @copyright Copyright  2004, Moxiecode Systems AB, All rights reserved.
 */

function TinyMCE() {
	this.instances = new Array();
	this.stickyClassesLookup = new Array();
	this.windowArgs = new Array();
	this.loadedFiles = new Array();
	this.configs = new Array();
	this.currentConfig = 0;
	this.eventHandlers = new Array();

	// Browser check
	this.isMSIE = (navigator.appName == "Microsoft Internet Explorer");
	this.isMSIE5 = this.isMSIE && (navigator.userAgent.indexOf('MSIE 5') != -1);
	this.isMSIE5_0 = this.isMSIE && (navigator.userAgent.indexOf('MSIE 5.0') != -1);
	this.isGecko = navigator.userAgent.indexOf('Gecko') != -1;
	this.isSafari = navigator.userAgent.indexOf('Safari') != -1;
	this.isMac = navigator.userAgent.indexOf('Mac') != -1;
	this.dialogCounter = 0;

	// TinyMCE editor id instance counter
	this.idCounter = 0;
};

TinyMCE.prototype.defParam = function(key, def_val) {
	this.settings[key] = tinyMCE.getParam(key, def_val);
};

TinyMCE.prototype.init = function(settings) {
	var theme;

	this.settings = settings;

	// Check if valid browser has execcommand support
	if (typeof(document.execCommand) == 'undefined')
		return;

	// Get script base path
	if (!tinyMCE.baseURL) {
		var elements = document.getElementsByTagName('script');

		for (var i=0; i<elements.length; i++) {
			if (elements[i].src && (elements[i].src.indexOf("tiny_mce.js") != -1 || elements[i].src.indexOf("tiny_mce_src.js") != -1 || elements[i].src.indexOf("tiny_mce_gzip.php") != -1)) {
				var src = elements[i].src;

				tinyMCE.srcMode = (src.indexOf('_src') != -1) ? '_src' : '';
				src = src.substring(0, src.lastIndexOf('/'));

				tinyMCE.baseURL = src;
				break;
			}
		}
	}

	// Get document base path
	this.documentBasePath = document.location.href;
	if (this.documentBasePath.indexOf('?') != -1)
		this.documentBasePath = this.documentBasePath.substring(0, this.documentBasePath.indexOf('?'));
	this.documentURL = this.documentBasePath;
	this.documentBasePath = this.documentBasePath.substring(0, this.documentBasePath.lastIndexOf('/'));

	// If not HTTP absolute
	if (tinyMCE.baseURL.indexOf('://') == -1 && tinyMCE.baseURL.charAt(0) != '/') {
		// If site absolute
		tinyMCE.baseURL = this.documentBasePath + "/" + tinyMCE.baseURL;
	}

	// Set default values on settings
	this.defParam("mode", "none");
	this.defParam("theme", "advanced");
	this.defParam("plugins", "", true);
	this.defParam("language", "en");
	this.defParam("docs_language", this.settings['language']);
	this.defParam("elements", "");
	this.defParam("textarea_trigger", "mce_editable");
	this.defParam("valid_elements", "+a[name|href|target|title|class],strong/b[class],em/i[class],strike[class],u[class],+p[dir|class|align],ol,ul,li,br,img[class|src|border=0|alt|title|hspace|vspace|width|height|align],sub,sup,blockquote[dir|style],table[border=0|cellspacing|cellpadding|width|height|class|align],tr[class|rowspan|width|height|align|valign],td[dir|class|colspan|rowspan|width|height|align|valign],div[dir|class|align],span[class|align],pre[class|align],address[class|align],h1[dir|class|align],h2[dir|class|align],h3[dir|class|align],h4[dir|class|align],h5[dir|class|align],h6[dir|class|align],hr");
	this.defParam("extended_valid_elements", "");
	this.defParam("invalid_elements", "");
	this.defParam("encoding", "");
	this.defParam("urlconverter_callback", tinyMCE.getParam("urlconvertor_callback", "TinyMCE.prototype.convertURL"));
	this.defParam("save_callback", "");
	this.defParam("debug", false);
	this.defParam("force_br_newlines", false);
	this.defParam("force_p_newlines", true);
	this.defParam("add_form_submit_trigger", true);
	this.defParam("relative_urls", true);
	this.defParam("remove_script_host", true);
	this.defParam("focus_alert", true);
	this.defParam("document_base_url", this.documentURL);
	this.defParam("visual", true);
	this.defParam("visual_table_class", "mceVisualAid");
	this.defParam("setupcontent_callback", "");
	this.defParam("fix_content_duplication", true);
	this.defParam("custom_undo_redo", true);
	this.defParam("custom_undo_redo_levels", -1);
	this.defParam("custom_undo_redo_keyboard_shortcuts", true);
	this.defParam("verify_css_classes", false);
	this.defParam("trim_span_elements", true);
	this.defParam("verify_html", true);
	this.defParam("apply_source_formatting", false);
	this.defParam("directionality", "ltr");
	this.defParam("auto_cleanup_word", false);
	this.defParam("cleanup_on_startup", false);
	this.defParam("inline_styles", false);
	this.defParam("convert_newlines_to_brs", false);
	this.defParam("auto_reset_designmode", false);
	this.defParam("entities", "160,nbsp,38,amp,34,quot,162,cent,8364,euro,163,pound,165,yen,169,copy,174,reg,8482,trade,8240,permil,181,micro,183,middot,8226,bull,8230,hellip,8242,prime,8243,Prime,167,sect,182,para,223,szlig,8249,lsaquo,8250,rsaquo,171,laquo,187,raquo,8216,lsquo,8217,rsquo,8220,ldquo,8221,rdquo,8218,sbquo,8222,bdquo,60,lt,62,gt,8804,le,8805,ge,8211,ndash,8212,mdash,175,macr,8254,oline,164,curren,166,brvbar,168,uml,161,iexcl,191,iquest,710,circ,732,tilde,176,deg,8722,minus,177,plusmn,247,divide,8260,frasl,215,times,185,sup1,178,sup2,179,sup3,188,frac14,189,frac12,190,frac34,402,fnof,8747,int,8721,sum,8734,infin,8730,radic,8764,sim,8773,cong,8776,asymp,8800,ne,8801,equiv,8712,isin,8713,notin,8715,ni,8719,prod,8743,and,8744,or,172,not,8745,cap,8746,cup,8706,part,8704,forall,8707,exist,8709,empty,8711,nabla,8727,lowast,8733,prop,8736,ang,180,acute,184,cedil,170,ordf,186,ordm,8224,dagger,8225,Dagger,192,Agrave,194,Acirc,195,Atilde,196,Auml,197,Aring,198,AElig,199,Ccedil,200,Egrave,202,Ecirc,203,Euml,204,Igrave,206,Icirc,207,Iuml,208,ETH,209,Ntilde,210,Ograve,212,Ocirc,213,Otilde,214,Ouml,216,Oslash,338,OElig,217,Ugrave,219,Ucirc,220,Uuml,376,Yuml,222,THORN,224,agrave,226,acirc,227,atilde,228,auml,229,aring,230,aelig,231,ccedil,232,egrave,234,ecirc,235,euml,236,igrave,238,icirc,239,iuml,240,eth,241,ntilde,242,ograve,244,ocirc,245,otilde,246,ouml,248,oslash,339,oelig,249,ugrave,251,ucirc,252,uuml,254,thorn,255,yuml,914,Beta,915,Gamma,916,Delta,917,Epsilon,918,Zeta,919,Eta,920,Theta,921,Iota,922,Kappa,923,Lambda,924,Mu,925,Nu,926,Xi,927,Omicron,928,Pi,929,Rho,931,Sigma,932,Tau,933,Upsilon,934,Phi,935,Chi,936,Psi,937,Omega,945,alpha,946,beta,947,gamma,948,delta,949,epsilon,950,zeta,951,eta,952,theta,953,iota,954,kappa,955,lambda,956,mu,957,nu,958,xi,959,omicron,960,pi,961,rho,962,sigmaf,963,sigma,964,tau,965,upsilon,966,phi,967,chi,968,psi,969,omega,8501,alefsym,982,piv,8476,real,977,thetasym,978,upsih,8472,weierp,8465,image,8592,larr,8593,uarr,8594,rarr,8595,darr,8596,harr,8629,crarr,8656,lArr,8657,uArr,8658,rArr,8659,dArr,8660,hArr,8756,there4,8834,sub,8835,sup,8836,nsub,8838,sube,8839,supe,8853,oplus,8855,otimes,8869,perp,8901,sdot,8968,lceil,8969,rceil,8970,lfloor,8971,rfloor,9001,lang,9002,rang,9674,loz,9824,spades,9827,clubs,9829,hearts,9830,diams,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm,173,shy,233,eacute");
	this.defParam("entity_encoding", "named");
	this.defParam("cleanup_callback", "");
	this.defParam("add_unload_trigger", true);
	this.defParam("ask", false);
	this.defParam("nowrap", false);
	this.defParam("auto_resize", false);
	this.defParam("auto_focus", false);
	this.defParam("cleanup", true);
	this.defParam("remove_linebreaks", true);
	this.defParam("button_tile_map", false);
	this.defParam("submit_patch", true);
	this.defParam("browsers", "msie,safari,gecko");
	this.defParam("dialog_type", "window");

	// Browser check IE
	if (this.isMSIE && this.settings['browsers'].indexOf('msie') == -1)
		return;

	// Browser check Gecko
	if (this.isGecko && this.settings['browsers'].indexOf('gecko') == -1)
		return;

	// Browser check Safari
	if (this.isSafari && this.settings['browsers'].indexOf('safari') == -1)
		return;

	// Setup baseHREF
	var baseHREF = tinyMCE.settings['document_base_url'];
	if (baseHREF.indexOf('?') != -1)
		baseHREF = baseHREF.substring(0, baseHREF.indexOf('?'));
	this.settings['base_href'] = baseHREF.substring(0, baseHREF.lastIndexOf('/')) + "/";

	theme = this.settings['theme'];

	this.blockRegExp = new RegExp("^(h1|h2|h3|h4|h5|h6|p|div|address|pre|form|table|li|ol|ul|td)$", "i");

	// Theme url
	this.settings['theme_href'] = tinyMCE.baseURL + "/themes/" + theme;

	if (!tinyMCE.isMSIE)
		this.settings['force_br_newlines'] = false;

	if (tinyMCE.getParam("content_css", false)) {
		var cssPath = tinyMCE.getParam("content_css", "");

		// Is relative
		if (cssPath.indexOf('://') == -1 && cssPath.charAt(0) != '/')
			this.settings['content_css'] = this.documentBasePath + "/" + cssPath;
		else
			this.settings['content_css'] = cssPath;
	} else
		this.settings['content_css'] = tinyMCE.baseURL + "/themes/" + theme + "/editor_content.css";

	if (tinyMCE.getParam("popups_css", false)) {
		var cssPath = tinyMCE.getParam("popups_css", "");

		// Is relative
		if (cssPath.indexOf('://') == -1 && cssPath.charAt(0) != '/')
			this.settings['popups_css'] = this.documentBasePath + "/" + cssPath;
		else
			this.settings['popups_css'] = cssPath;
	} else
		this.settings['popups_css'] = tinyMCE.baseURL + "/themes/" + theme + "/editor_popup.css";

	if (tinyMCE.getParam("editor_css", false)) {
		var cssPath = tinyMCE.getParam("editor_css", "");

		// Is relative
		if (cssPath.indexOf('://') == -1 && cssPath.charAt(0) != '/')
			this.settings['editor_css'] = this.documentBasePath + "/" + cssPath;
		else
			this.settings['editor_css'] = cssPath;
	} else
		this.settings['editor_css'] = tinyMCE.baseURL + "/themes/" + theme + "/editor_ui.css";

	if (tinyMCE.settings['debug']) {
		var msg = "Debug: \n";

		msg += "baseURL: " + this.baseURL + "\n";
		msg += "documentBasePath: " + this.documentBasePath + "\n";
		msg += "content_css: " + this.settings['content_css'] + "\n";
		msg += "popups_css: " + this.settings['popups_css'] + "\n";
		msg += "editor_css: " + this.settings['editor_css'] + "\n";

		alert(msg);
	}

	// Init HTML cleanup
	this._initCleanup();

	// Only do this once
	if (this.configs.length == 0) {
		// Is Safari enabled
		if (this.isSafari)
			alert("Safari support is very limited and should be considered experimental.\nSo there is no need to even submit bugreports on this early version.");

		tinyMCE.addEvent(window, "load", TinyMCE.prototype.onLoad);

		if (tinyMCE.isMSIE) {
			if (tinyMCE.settings['add_unload_trigger']) {
				tinyMCE.addEvent(window, "unload", TinyMCE.prototype.unloadHandler);
				tinyMCE.addEvent(window.document, "beforeunload", TinyMCE.prototype.unloadHandler);
			}
		} else {
			if (tinyMCE.settings['add_unload_trigger'])
				tinyMCE.addEvent(window, "unload", function () {tinyMCE.triggerSave(true, true);});
		}
	}

	this.loadScript(tinyMCE.baseURL + '/themes/' + this.settings['theme'] + '/editor_template' + tinyMCE.srcMode + '.js');
	this.loadScript(tinyMCE.baseURL + '/langs/' + this.settings['language'] +  '.js');
	this.loadCSS(this.settings['editor_css']);

	// Add theme plugins
	var themePlugins = tinyMCE.getParam('plugins', '', true, ',');
	if (this.settings['plugins'] != '') {
		for (var i=0; i<themePlugins.length; i++)
			this.loadScript(tinyMCE.baseURL + '/plugins/' + themePlugins[i] + '/editor_plugin' + tinyMCE.srcMode + '.js');
	}

	// Save away this config
	settings['index'] = this.configs.length;
	this.configs[this.configs.length] = settings;
};

TinyMCE.prototype.loadScript = function(url) {
	for (var i=0; i<this.loadedFiles.length; i++) {
		if (this.loadedFiles[i] == url)
			return;
	}

	document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + url + '"></script>');

	this.loadedFiles[this.loadedFiles.length] = url;
};

TinyMCE.prototype.loadCSS = function(url) {
	for (var i=0; i<this.loadedFiles.length; i++) {
		if (this.loadedFiles[i] == url)
			return;
	}

	document.write('<link href="' + url + '" rel="stylesheet" type="text/css" />');

	this.loadedFiles[this.loadedFiles.length] = url;
};

TinyMCE.prototype.importCSS = function(doc, css_file) {
	if (tinyMCE.isMSIE)
		var styleSheet = doc.createStyleSheet(css_file);
	else {
		var elm = doc.createElement("link");

		elm.rel = "stylesheet";
		elm.href = css_file;

		if (headArr = doc.getElementsByTagName("head"))
			headArr[0].appendChild(elm);
	}
};

TinyMCE.prototype.confirmAdd = function(e, settings) {
	var elm = tinyMCE.isMSIE ? event.srcElement : e.target;
	var elementId = elm.name ? elm.name : elm.id;

	tinyMCE.settings = settings;

	if (!elm.getAttribute('mce_noask') && confirm(tinyMCELang['lang_edit_confirm']))
		tinyMCE.addMCEControl(elm, elementId);

	elm.setAttribute('mce_noask', 'true');
};

TinyMCE.prototype.updateContent = function(form_element_name) {
	// Find MCE instance linked to given form element and copy it's value
	var formElement = document.getElementById(form_element_name);
	for (var n in tinyMCE.instances) {
		var inst = tinyMCE.instances[n];

		inst.switchSettings();

		if (inst.formElement == formElement) {
			var doc = inst.getDoc();
	
			tinyMCE._setHTML(doc, inst.formElement.value);

			if (!tinyMCE.isMSIE)
				doc.body.innerHTML = tinyMCE._cleanupHTML(doc, this.settings, doc.body, inst.visualAid);
		}
	}
};

TinyMCE.prototype.addMCEControl = function(replace_element, form_element_name, target_document) {
	var id = "mce_editor_" + tinyMCE.idCounter++;
	var inst = new TinyMCEControl(tinyMCE.settings);

	inst.editorId = id;
	this.instances[id] = inst;

	inst.onAdd(replace_element, form_element_name, target_document);
};

TinyMCE.prototype.triggerSave = function(skip_cleanup, skip_callback) {
	// Cleanup and set all form fields
	for (var n in tinyMCE.instances) {
		var inst = tinyMCE.instances[n];

		inst.switchSettings();

		tinyMCE.settings['preformatted'] = false;

		// Default to false
		if (typeof(skip_cleanup) == "undefined")
			skip_cleanup = false;

		// Default to false
		if (typeof(skip_callback) == "undefined")
			skip_callback = false;

		tinyMCE._setHTML(inst.getDoc(), inst.getBody().innerHTML);

		var htm = skip_cleanup ? inst.getBody().innerHTML : tinyMCE._cleanupHTML(inst.getDoc(), this.settings, inst.getBody(), this.visualAid, true);

		//var htm = tinyMCE._cleanupHTML(inst.getDoc(), tinyMCE.settings, inst.getBody(), false, true);

		if (tinyMCE.settings["encoding"] == "xml" || tinyMCE.settings["encoding"] == "html")
			htm = tinyMCE.convertStringToXML(htm);

		if (!skip_callback && tinyMCE.settings['save_callback'] != "")
			var content = eval(tinyMCE.settings['save_callback'] + "(inst.formTargetElementId,htm,inst.getBody());");

		// Use callback content if available
		if ((typeof(content) != "undefined") && content != null)
			htm = content;

		// Replace some weird entities (Bug: #1056343)
		htm = tinyMCE.regexpReplace(htm, "&#40;", "(", "gi");
		htm = tinyMCE.regexpReplace(htm, "&#41;", ")", "gi");
		htm = tinyMCE.regexpReplace(htm, "&#59;", ";", "gi");
		htm = tinyMCE.regexpReplace(htm, "&#34;", "&quot;", "gi");
		htm = tinyMCE.regexpReplace(htm, "&#94;", "^", "gi");

		if (inst.formElement)
			inst.formElement.value = htm;
	}
};

TinyMCE.prototype._convertOnClick = function(node) {
	// Skip on MSIE < 6+
	if (tinyMCE.isMSIE5)
		return;

	// Convert all onclick to mce_onclick
	var elms = node.getElementsByTagName("a");
	for (var i=0; i<elms.length; i++) {
		var onclick = elms[i].getAttribute('onclick');
		if (onclick && onclick != "") {
			elms[i].removeAttribute("onclick");
			elms[i].setAttribute("mce_onclick", tinyMCE.cleanupEventStr("" + onclick));
			elms[i].onclick = null;
		}
	}
};

TinyMCE.prototype.resetForm = function(form_index) {
	var formObj = document.forms[form_index];

	for (var n in tinyMCE.instances) {
		var inst = tinyMCE.instances[n];

		inst.switchSettings();

		for (var i=0; i<formObj.elements.length; i++) {
			if (inst.formTargetElementId == formObj.elements[i].name) {
				inst.getBody().innerHTML = formObj.elements[i].value;
				return;
			}
		}
	}
};

TinyMCE.prototype.execInstanceCommand = function(editor_id, command, user_interface, value, focus) {
	var inst = tinyMCE.getInstanceById(editor_id);
	if (inst) {
		if (typeof(focus) == "undefined")
			focus = true;

		if (focus)
			inst.contentWindow.focus();

		// Reset design mode if lost
		inst.autoResetDesignMode();

		this.selectedElement = inst.getFocusElement();
		this.selectedInstance = inst;
		tinyMCE.execCommand(command, user_interface, value);
	}
};

TinyMCE.prototype.execCommand = function(command, user_interface, value) {
	// Default input
	user_interface = user_interface ? user_interface : false;
	value = value ? value : null;

	if (tinyMCE.selectedInstance)
		tinyMCE.selectedInstance.switchSettings();

	switch (command) {
		case 'mceHelp':
			window.open(tinyMCE.themeURL + "/docs/" + this.settings['docs_language'] + "/index.htm", "mceHelp", "menubar=yes,toolbar=yes,scrollbars=yes,left=20,top=20,width=550,height=600");
		return;

		case 'mceFocus':
			var inst = tinyMCE.getInstanceById(value);
			if (inst)
				inst.contentWindow.focus();
		return;

		case "mceAddControl":
		case "mceAddEditor":
			tinyMCE.addMCEControl(tinyMCE._getElementById(value), value);
			return;

		case "mceAddFrameControl":
			tinyMCE.addMCEControl(tinyMCE._getElementById(value), value['element'], value['document']);
			return;

		case "mceRemoveControl":
		case "mceRemoveEditor":
			tinyMCE.removeMCEControl(value);
			return;

		case "mceResetDesignMode":
			// Resets the designmode state of the editors in Gecko
			if (!tinyMCE.isMSIE) {
				for (var n in tinyMCE.instances) {
					try {
						tinyMCE.instances[n].getDoc().designMode = "on";
					} catch (e) {
						// Ignore any errors
					}
				}
			}

			return;
	}

	if (this.selectedInstance)
		this.selectedInstance.execCommand(command, user_interface, value);
	else if (tinyMCE.settings['focus_alert'])
		alert(tinyMCELang['lang_focus_alert']);
};

TinyMCE.prototype.eventPatch = function(editor_id) {
	// Remove odd, error
	if (typeof(tinyMCE) == "undefined")
		return true;

	for (var i=0; i<document.frames.length; i++) {
		if (document.frames[i].event) {
			var event = document.frames[i].event;

			event.target = event.srcElement;
			event.target.editor_id = document.frames[i].editor_id;

			TinyMCE.prototype.handleEvent(event);
			return;
		}
	}
};

TinyMCE.prototype.unloadHandler = function() {
	tinyMCE.triggerSave(true, true);
};

TinyMCE.prototype.addEventHandlers = function(editor_id) {
	if (tinyMCE.isMSIE) {
		var doc = document.frames[editor_id].document;

		// Event patch
		document.frames[editor_id].editor_id = editor_id;
		tinyMCE.addEvent(doc, "keypress", TinyMCE.prototype.eventPatch);
		tinyMCE.addEvent(doc, "keyup", TinyMCE.prototype.eventPatch);
		tinyMCE.addEvent(doc, "keydown", TinyMCE.prototype.eventPatch);
		tinyMCE.addEvent(doc, "mouseup", TinyMCE.prototype.eventPatch);
		tinyMCE.addEvent(doc, "click", TinyMCE.prototype.eventPatch);
	} else {
		var inst = tinyMCE.instances[editor_id];
		var doc = inst.getDoc();

		inst.switchSettings();

		doc.editor_id = editor_id;
		tinyMCE.addEvent(doc, "keypress", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "keypress", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "keydown", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "keyup", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "click", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "mouseup", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "mousedown", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "focus", tinyMCE.handleEvent);

		eval('try { doc.designMode = "On"; } catch(e) {}');
	}
};

TinyMCE.prototype._createIFrame = function(replace_element) {
	var iframe = document.createElement("iframe");
	var id = replace_element.getAttribute("id");

	iframe.setAttribute("id", id);
	iframe.setAttribute("className", "mceEditorArea");
	iframe.setAttribute("border", "0");
	iframe.setAttribute("frameBorder", "0");
	iframe.setAttribute("marginWidth", "0");
	iframe.setAttribute("marginHeight", "0");
	iframe.setAttribute("leftMargin", "0");
	iframe.setAttribute("topMargin", "0");
	iframe.setAttribute("width", tinyMCE.settings['area_width']);
	iframe.setAttribute("height", "98%");
	iframe.setAttribute("allowtransparency", "true");

	if (tinyMCE.settings["auto_resize"])
		iframe.setAttribute("scrolling", "no");

	// Must have a src element in MSIE HTTPs breaks aswell as absoute URLs
	if (tinyMCE.isMSIE)
		iframe.setAttribute("src", this.settings['default_document']);

	iframe.style.width = tinyMCE.settings['area_width'];
	iframe.style.height = tinyMCE.settings['area_height'];

	// MSIE 5.0 issue
	if (tinyMCE.isMSIE)
		replace_element.outerHTML = iframe.outerHTML;
	else
		replace_element.parentNode.replaceChild(iframe, replace_element);

	if (tinyMCE.isMSIE)
		return window.frames[id];
	else
		return iframe;
};

TinyMCE.prototype.setupContent = function(editor_id) {
	var inst = tinyMCE.instances[editor_id];
	var doc = inst.getDoc();
	var head = doc.getElementsByTagName('head').item(0);
	var content = inst.startContent;

	inst.switchSettings();

	// Not loaded correctly hit it again, Mozilla bug #997860
	if (!tinyMCE.isMSIE && doc.title != "blank_page") {
		// This part will remove the designMode status
		doc.location.href = tinyMCE.baseURL + "/blank.htm";
		window.setTimeout("tinyMCE.setupContent('" + editor_id + "');", 1000);
		return;
	}

	if (!head) {
		window.setTimeout("tinyMCE.setupContent('" + editor_id + "');", 10);
		return;
	}

	tinyMCE.importCSS(inst.getDoc(), inst.settings['content_css']);
	tinyMCE.executeCallback('init_instance_callback', '_initInstance', 0, inst);

	if (tinyMCE.settings['nowrap'])
		doc.body.style.whiteSpace = "nowrap";

	doc.body.dir = this.settings['directionality'];
	doc.editorId = editor_id;

	// Add on document element in Mozilla
	if (!tinyMCE.isMSIE)
		doc.documentElement.editorId = editor_id;

	// Setup base element
	base = doc.createElement("base");
	base.setAttribute('href', tinyMCE.settings['base_href']);
	head.appendChild(base);

	// Replace new line characters to BRs
	if (tinyMCE.settings['convert_newlines_to_brs']) {
		content = tinyMCE.regexpReplace(content, "\r\n", "<br />", "gi");
		content = tinyMCE.regexpReplace(content, "\r", "<br />", "gi");
		content = tinyMCE.regexpReplace(content, "\n", "<br />", "gi");
	}

	// Call custom cleanup code
	content = tinyMCE._customCleanup("insert_to_editor", content);

	if (tinyMCE.isMSIE) {
		// Ugly!!!
		window.setInterval('try{tinyMCE.getCSSClasses(document.frames["' + editor_id + '"].document, "' + editor_id + '");}catch(e){}', 500);

		if (tinyMCE.settings["force_br_newlines"])
			document.frames[editor_id].document.styleSheets[0].addRule("p", "margin: 0px;");

		var body = document.frames[editor_id].document.body;

		tinyMCE.addEvent(body, "beforepaste", TinyMCE.prototype.eventPatch);
		tinyMCE.addEvent(body, "beforecut", TinyMCE.prototype.eventPatch);
		tinyMCE.addEvent(body, "paste", TinyMCE.prototype.eventPatch);

		body.editorId = editor_id;
	}

	// Fix for bug #958637
	if (!tinyMCE.isMSIE) {
		var contentElement = inst.getDoc().createElement("body");
		var doc = inst.getDoc();

		contentElement.innerHTML = content;

		// Remove weridness!
		if (tinyMCE.settings['force_p_newlines'])
			content = content.replace(new RegExp('&lt;&gt;', 'g'), "");

		if (tinyMCE.settings['cleanup_on_startup'])
			inst.getBody().innerHTML = tinyMCE._cleanupHTML(doc, this.settings, contentElement);
		else {
			// Convert all strong/em to b/i
			content = tinyMCE.regexpReplace(content, "<strong", "<b", "gi");
			content = tinyMCE.regexpReplace(content, "<em", "<i", "gi");
			content = tinyMCE.regexpReplace(content, "</strong>", "</b>", "gi");
			content = tinyMCE.regexpReplace(content, "</em>", "</i>", "gi");
			inst.getBody().innerHTML = content;
		}

		inst.convertAllRelativeURLs();
	} else {
		if (tinyMCE.settings['cleanup_on_startup']) {
			tinyMCE._setHTML(inst.getDoc(), content);

			// Produces permission denied error in MSIE 5.5
			eval('try {inst.getBody().innerHTML = tinyMCE._cleanupHTML(inst.contentDocument, this.settings, inst.getBody());} catch(e) {}');
		} else
			tinyMCE._setHTML(inst.getDoc(), content);
	}

	tinyMCE._convertOnClick(inst.getBody());

	// Fix for bug #957681
	//inst.getDoc().designMode = inst.getDoc().designMode;

	// Setup element references
	var parentElm = document.getElementById(inst.editorId + '_parent');
	if (parentElm.lastChild.nodeName.toLowerCase() == "input")
		inst.formElement = parentElm.lastChild;
	else
		inst.formElement = parentElm.nextSibling;

	tinyMCE.handleVisualAid(inst.getBody(), true, tinyMCE.settings['visual']);
	tinyMCE.executeCallback('setupcontent_callback', '_setupContent', 0, editor_id, inst.getBody(), inst.getDoc());

	// Re-add design mode on mozilla
	if (!tinyMCE.isMSIE)
		TinyMCE.prototype.addEventHandlers(editor_id);

	inst.startContent = inst.getBody().innerHTML;

	// Trigger node change, this call locks buttons for tables and so forth
	tinyMCE.selectedInstance = inst;
	tinyMCE.selectedElement = inst.contentWindow.document.body;
	tinyMCE.triggerNodeChange(false, true);

	// Call custom DOM cleanup
	tinyMCE._customCleanup("insert_to_editor_dom", inst.contentWindow.document.body);
};

TinyMCE.prototype.cancelEvent = function(e) {
	if (tinyMCE.isMSIE) {
		e.returnValue = false;
		e.cancelBubble = true;
	} else
		e.preventDefault();
};

TinyMCE.prototype.removeTinyMCEFormElements = function(form_obj) {
	// Disable all UI form elements that TinyMCE created
	for (var i=0; i<form_obj.elements.length; i++) {
		var elementId = form_obj.elements[i].name ? form_obj.elements[i].name : form_obj.elements[i].id;

		if (elementId.indexOf('mce_editor_') == 0)
			form_obj.elements[i].disabled = true;
	}
};

TinyMCE.prototype.handleEvent = function(e) {
	// Remove odd, error
	if (typeof(tinyMCE) == "undefined")
		return true;

	//debug(e.type + " " + e.target.nodeName + " " + (e.relatedTarget ? e.relatedTarget.nodeName : ""));

	switch (e.type) {
		case "submit":
			tinyMCE.removeTinyMCEFormElements(tinyMCE.isMSIE ? window.event.srcElement : e.target);
			tinyMCE.triggerSave();
			return;

		case "reset":
			var formObj = tinyMCE.isMSIE ? window.event.srcElement : e.target;

			for (var i=0; i<document.forms.length; i++) {
				if (document.forms[i] == formObj)
					window.setTimeout('tinyMCE.resetForm(' + i + ');', 10);
			}
			return;

		case "paste":
			if (tinyMCE.settings['auto_cleanup_word']) {
				var editorId = e.target.editorId;

				if (!editorId)
					editorId = e.target.ownerDocument.editorId;

				if (editorId)
					window.setTimeout("tinyMCE.execInstanceCommand('" + editorId + "', 'mceCleanupWord', false, null);", 1);
			}

			break;

		case "beforecut":
		case "beforepaste":
			if (tinyMCE.selectedInstance)
				tinyMCE.selectedInstance.execCommand("mceAddUndoLevel");
			break;

		case "keypress":
			if (e.target.editorId) {
				tinyMCE.selectedInstance = tinyMCE.instances[e.target.editorId];
			} else {
				if (e.target.ownerDocument.editorId)
					tinyMCE.selectedInstance = tinyMCE.instances[e.target.ownerDocument.editorId];
			}

			if (tinyMCE.selectedInstance)
				tinyMCE.selectedInstance.switchSettings();

			// Insert space instead of &nbsp;
/*			if (tinyMCE.isGecko && e.charCode == 32) {
				if (tinyMCE.selectedInstance._insertSpace()) {
					// Cancel event
					e.preventDefault();
					return false;
				}
			}*/

			// Insert P element
			if (tinyMCE.isGecko && tinyMCE.settings['force_p_newlines'] && e.keyCode == 13 && !e.shiftKey) {
				// Insert P element instead of BR
				if (tinyMCE.selectedInstance._insertPara(e)) {
					// Cancel event
					e.preventDefault();
					return false;
				}
			}

			// Handle backspace
			if (tinyMCE.isGecko && tinyMCE.settings['force_p_newlines'] && (e.keyCode == 8 || e.keyCode == 46) && !e.shiftKey) {
				// Insert P element instead of BR
				if (tinyMCE.selectedInstance._handleBackSpace(e.type)) {
					// Cancel event
					e.preventDefault();
					return false;
				}
			}

			// Mozilla custom key handling
			if (!tinyMCE.isMSIE && e.ctrlKey && tinyMCE.settings['custom_undo_redo']) {
				if (e.charCode == 120 || e.charCode == 118) { // Ctrl+X, Ctrl+V
					tinyMCE.selectedInstance.execCommand("mceAddUndoLevel");
					return;
				}

				if (tinyMCE.settings['custom_undo_redo_keyboard_shortcuts']) {
					if (e.charCode == 122) { // Ctrl+Z
						tinyMCE.selectedInstance.execCommand("Undo");

						// Cancel event
						e.preventDefault();
						return false;
					}

					if (e.charCode == 121) { // Ctrl+Y
						tinyMCE.selectedInstance.execCommand("Redo");

						// Cancel event
						e.preventDefault();
						return false;
					}
				}

				if (e.charCode == 98) { // Ctrl+B
					tinyMCE.selectedInstance.execCommand("Bold");

					// Cancel event
					e.preventDefault();
					return false;
				}

				if (e.charCode == 105) { // Ctrl+I
					tinyMCE.selectedInstance.execCommand("Italic");

					// Cancel event
					e.preventDefault();
					return false;
				}

				if (e.charCode == 117) { // Ctrl+U
					tinyMCE.selectedInstance.execCommand("Underline");

					// Cancel event
					e.preventDefault();
					return false;
				}
			}

			if (tinyMCE.settings['custom_undo_redo']) {
				// Check if it's a position key press
				var keys = new Array(13,45,36,35,33,34,37,38,39,40);
				var posKey = false;
				for (var i=0; i<keys.length; i++) {
					if (keys[i] == e.keyCode) {
						tinyMCE.selectedInstance.typing = false;
						posKey = true;
						break;
					}
				}

				// Add typing undo level
				if (!tinyMCE.selectedInstance.typing && !posKey) {
					tinyMCE.selectedInstance.execCommand("mceAddUndoLevel");
					tinyMCE.selectedInstance.typing = true;
				}
			}

			//window.status = e.keyCode;
			//window.status = e.type + " " + e.target.nodeName;

			// Return key pressed
			if (tinyMCE.isMSIE && tinyMCE.settings['force_br_newlines'] && e.keyCode == 13) {
				if (e.target.editorId)
					tinyMCE.selectedInstance = tinyMCE.instances[e.target.editorId];

				if (tinyMCE.selectedInstance) {
					var sel = tinyMCE.selectedInstance.getDoc().selection;
					var rng = sel.createRange();

					if (tinyMCE.getParentElement(rng.parentElement(), "li") != null)
						return false;

					// Cancel event
					e.returnValue = false;
					e.cancelBubble = true;

					// Insert BR element
					rng.pasteHTML("<br />");
					rng.collapse(false);
					rng.select();

					tinyMCE.triggerNodeChange(false);
					return false;
				}
			}

			// Backspace or delete
			if (e.keyCode == 8 || e.keyCode == 46) {
				tinyMCE.selectedElement = e.target;
				tinyMCE.linkElement = tinyMCE.getParentElement(e.target, "a");
				tinyMCE.imgElement = tinyMCE.getParentElement(e.target, "img");
				tinyMCE.triggerNodeChange(false);
			}

			return false;
		break;

		case "keyup":
		case "keydown":
			if (e.target.editorId)
				tinyMCE.selectedInstance = tinyMCE.instances[e.target.editorId];
			else
				return;

			if (tinyMCE.selectedInstance)
				tinyMCE.selectedInstance.switchSettings();

			// Handle backspace
			if (tinyMCE.isGecko && tinyMCE.settings['force_p_newlines'] && (e.keyCode == 8 || e.keyCode == 46) && !e.shiftKey) {
				// Insert P element instead of BR
				if (tinyMCE.selectedInstance._handleBackSpace(e.type)) {
					// Cancel event
					e.preventDefault();
					return false;
				}
			}

			tinyMCE.selectedElement = null;
			tinyMCE.selectedNode = null;
			var elm = tinyMCE.selectedInstance.getFocusElement();
			tinyMCE.linkElement = tinyMCE.getParentElement(elm, "a");
			tinyMCE.imgElement = tinyMCE.getParentElement(elm, "img");
			tinyMCE.selectedElement = elm;

			// Update visualaids on tabs
			if (tinyMCE.isGecko && e.type == "keyup" && e.keyCode == 9)
				tinyMCE.handleVisualAid(tinyMCE.selectedInstance.getBody(), true, tinyMCE.settings['visual']);

			// Run image/link fix on Gecko if diffrent document base on paste
			if (tinyMCE.isGecko && tinyMCE.settings['document_base_url'] != "" + document.location.href && e.type == "keyup" && e.ctrlKey && e.keyCode == 86)
				tinyMCE.selectedInstance.fixBrokenURLs();

			// Insert space instead of &nbsp;
/*			if (e.type == "keydown" && e.keyCode == 32) {
				if (tinyMCE.selectedInstance._insertSpace()) {
					// Cancel event
					e.returnValue = false;
					e.cancelBubble = true;
					return false;
				}
			}*/

			// MSIE custom key handling
			if (tinyMCE.isMSIE && tinyMCE.settings['custom_undo_redo']) {
				// Check if it's a position key press
				var keys = new Array(13,45,36,35,33,34,37,38,39,40);
				var posKey = false;
				for (var i=0; i<keys.length; i++) {
					if (keys[i] == e.keyCode) {
						tinyMCE.selectedInstance.typing = false;
						posKey = true;
						break;
					}
				}

				// Add typing undo level (unless pos keys or shift, alt, ctrl, capslock)
				if (!tinyMCE.selectedInstance.typing && !posKey && (e.keyCode < 16 || e.keyCode > 18 && e.keyCode != 255)) {
					tinyMCE.selectedInstance.execCommand("mceAddUndoLevel");
					tinyMCE.selectedInstance.typing = true;
					tinyMCE.triggerNodeChange(false);
				}

				if (posKey && e.type == "keyup")
					tinyMCE.triggerNodeChange(false);

				var keys = new Array(8,46); // Backspace,Delete
				for (var i=0; i<keys.length; i++) {
					if (keys[i] == e.keyCode) {
						if (!tinyMCE.selectedInstance.typing) {
							tinyMCE.selectedInstance.execCommand("mceAddUndoLevel");
							tinyMCE.selectedInstance.typing = true;
						}

						if (e.type == "keyup")
							tinyMCE.triggerNodeChange(false);

						return true;
					}
				}

				var ctrlKeys = new Array(66,73,85,86,88); // B/I/U/V/X
				for (var i=0; i<keys.length; i++) {
					if (ctrlKeys[i] == e.keyCode && e.ctrlKey) {
						tinyMCE.selectedInstance.execCommand("mceAddUndoLevel");
						tinyMCE.triggerNodeChange(false);
						return true;
					}
				}

				if (tinyMCE.settings['custom_undo_redo_keyboard_shortcuts']) {
					if (e.keyCode == 90 && e.ctrlKey && e.type == "keydown") { // Ctrl+Z
						tinyMCE.selectedInstance.execCommand("Undo");
						tinyMCE.triggerNodeChange(false);

						// Cancel event
						e.returnValue = false;
						e.cancelBubble = true;
						return false;
					}

					if (e.keyCode == 89 && e.ctrlKey && e.type == "keydown") { // Ctrl+Y
						tinyMCE.selectedInstance.execCommand("Redo");
						tinyMCE.triggerNodeChange(false);

						// Cancel event
						e.returnValue = false;
						e.cancelBubble = true;
						return false;
					}
				}
			}

			// Check if it's a position key press
			var keys = new Array(13,45,36,35,33,34,37,38,39,40);
			var posKey = false;
			for (var i=0; i<keys.length; i++) {
				if (keys[i] == e.keyCode) {
					posKey = true;
					break;
				}
			}

			// Trigger some nodechange on keyup
			if (posKey && e.type == "keyup")
				tinyMCE.triggerNodeChange(false);
		break;

		case "mousedown":
		case "mouseup":
		case "click":
		case "focus":
			if (tinyMCE.selectedInstance)
				tinyMCE.selectedInstance.switchSettings();

			// Check instance event trigged on
			var targetBody = tinyMCE.getParentElement(e.target, "body");
			for (var instanceName in tinyMCE.instances) {
				var inst = tinyMCE.instances[instanceName];

				// Reset design mode if lost (on everything just in case)
				inst.autoResetDesignMode();

				if (inst.getBody() == targetBody) {
					tinyMCE.selectedInstance = inst;
					tinyMCE.selectedElement = e.target;
					tinyMCE.linkElement = tinyMCE.getParentElement(tinyMCE.selectedElement, "a");
					tinyMCE.imgElement = tinyMCE.getParentElement(tinyMCE.selectedElement, "img");

					// Reset typing
					tinyMCE.selectedInstance.typing = false;
					break;
				}
			}

			if (tinyMCE.isSafari) {
				tinyMCE.selectedInstance.lastSafariSelection = tinyMCE.selectedInstance.getBookmark();
				tinyMCE.selectedInstance.lastSafariSelectedElement = tinyMCE.selectedElement;

				var lnk = tinyMCE.getParentElement(tinyMCE.selectedElement, "a");

				// Patch the darned link
				if (lnk && e.type == "mousedown") {
					lnk.setAttribute("mce_real_href", lnk.getAttribute("href"));
					lnk.setAttribute("href", "javascript:void(0);");
				}

				// Patch back
				if (lnk && e.type == "click") {
					window.setTimeout(function() {
						lnk.setAttribute("href", lnk.getAttribute("mce_real_href"));
						lnk.removeAttribute("mce_real_href");
					}, 10);
				}
			}

			// Reset selected node
			if (e.type != "focus")
				tinyMCE.selectedNode = null;

			tinyMCE.triggerNodeChange(false);

			// Just in case
			if (!tinyMCE.selectedInstance && e.target.editorId)
				tinyMCE.selectedInstance = tinyMCE.instances[e.target.editorId];

			// Was it alt click on link
			if (e.target.nodeName.toLowerCase() == "a" && e.type == "click" && e.altKey) {
				var evalCode = "" + tinyMCE.cleanupEventStr(e.target.getAttribute("mce_onclick"));

				// Remove any return too
				eval(evalCode.replace('return false;', ''));
			}

			//if (tinyMCE.selectedInstance)
			//	tinyMCE.selectedInstance.fixBrokenURLs();

			// Run image/link fix on Gecko if diffrent document base
			if (tinyMCE.isGecko && tinyMCE.settings['document_base_url'] != "" + document.location.href)
				window.setTimeout('tinyMCE.getInstanceById("' + inst.editorId + '").fixBrokenURLs();', 10);

			return false;
		break;
    } // end switch
}; // end function

TinyMCE.prototype.switchClass = function(element, class_name, lock_state) {
	var lockChanged = false;

	if (typeof(lock_state) != "undefined" && element != null) {
		element.classLock = lock_state;
		lockChanged = true;
	}

	if (element != null && (lockChanged || !element.classLock)) {
		element.oldClassName = element.className;
		element.className = class_name;
	}
};

TinyMCE.prototype.restoreAndSwitchClass = function(element, class_name) {
	if (element != null && !element.classLock) {
		this.restoreClass(element);
		this.switchClass(element, class_name);
	}
};

TinyMCE.prototype.switchClassSticky = function(element_name, class_name, lock_state) {
	var element, lockChanged = false;

	// Performance issue
	if (!this.stickyClassesLookup[element_name])
		this.stickyClassesLookup[element_name] = document.getElementById(element_name);

//	element = document.getElementById(element_name);
	element = this.stickyClassesLookup[element_name];

	if (typeof(lock_state) != "undefined" && element != null) {
		element.classLock = lock_state;
		lockChanged = true;
	}

	if (element != null && (lockChanged || !element.classLock)) {
		element.className = class_name;
		element.oldClassName = class_name;
	}
};

TinyMCE.prototype.restoreClass = function(element) {
	if (element != null && element.oldClassName && !element.classLock) {
		element.className = element.oldClassName;
		element.oldClassName = null;
	}
};

TinyMCE.prototype.setClassLock = function(element, lock_state) {
	if (element != null)
		element.classLock = lock_state;
};

TinyMCE.prototype.addEvent = function(obj, name, handler) {
	if (tinyMCE.isMSIE) {
		obj.attachEvent("on" + name, handler);
	} else
		obj.addEventListener(name, handler, false);
};

TinyMCE.prototype.submitPatch = function() {
	tinyMCE.removeTinyMCEFormElements(this);
	tinyMCE.triggerSave();
	this.oldSubmit();
};

TinyMCE.prototype.onLoad = function() {
	for (var c=0; c<tinyMCE.configs.length; c++) {
		tinyMCE.settings = tinyMCE.configs[c];

		var elementRefAr = new Array();

		// Add submit triggers
		if (document.forms && tinyMCE.settings['add_form_submit_trigger'] && !tinyMCE.submitTriggers) {
			for (var i=0; i<document.forms.length; i++) {
				var form = document.forms[i];

				tinyMCE.addEvent(form, "submit", TinyMCE.prototype.handleEvent);
				tinyMCE.addEvent(form, "reset", TinyMCE.prototype.handleEvent);
				tinyMCE.submitTriggers = true; // Do it only once

				// Patch the form.submit function
				if (tinyMCE.settings['submit_patch']) {
					try {
						form.oldSubmit = form.submit;
						form.submit = TinyMCE.prototype.submitPatch;
					} catch (e) {
						// Do nothing
					}
				}
			}
		}

		// Add editor instances based on mode
		var mode = tinyMCE.settings['mode'];
		switch (mode) {
			case "exact":
				var elements = tinyMCE.getParam('elements', '', true, ',');

				for (var i=0; i<elements.length; i++) {
					var element = tinyMCE._getElementById(elements[i]);
					var trigger = element ? element.getAttribute(tinyMCE.settings['textarea_trigger']) : "";

					if (trigger == "false")
						continue;

					if (tinyMCE.settings['ask'] && element) {
						elementRefAr[elementRefAr.length] = element;
						continue;
					}

					if (element)
						tinyMCE.addMCEControl(element, elements[i]);
					else if (tinyMCE.settings['debug'])
						alert("Error: Could not find element by id or name: " + elements[i]);
				}
			break;

			case "specific_textareas":
			case "textareas":
				var nodeList = document.getElementsByTagName("textarea");

				for (var i=0; i<nodeList.length; i++) {
					var trigger = nodeList.item(i).getAttribute(tinyMCE.settings['textarea_trigger']);

					if ((mode == "specific_textareas" && trigger == "true") || (mode == "textareas" && trigger != "false"))
						elementRefAr[elementRefAr.length] = nodeList.item(i);
				}
			break;
		}

		for (var i=0; i<elementRefAr.length; i++) {
			var element = elementRefAr[i];
			var elementId = element.name ? element.name : element.id;

			if (tinyMCE.settings['ask']) {
				// Focus breaks in Mozilla
				if (tinyMCE.isGecko) {
					var settings = tinyMCE.settings;

					tinyMCE.addEvent(element, "focus", function (e) {window.setTimeout(function() {TinyMCE.prototype.confirmAdd(e, settings);}, 10);});
				} else {
					var settings = tinyMCE.settings;

					tinyMCE.addEvent(element, "focus", function () { TinyMCE.prototype.confirmAdd(null, settings); });
				}
			} else
				tinyMCE.addMCEControl(element, elementId);
		}

		// Handle auto focus
		if (tinyMCE.settings['auto_focus']) {
			window.setTimeout(function () {
				var inst = tinyMCE.getInstanceById(tinyMCE.settings['auto_focus']);
				inst.selectNode(inst.getBody(), true, true);
				inst.contentWindow.focus();
			}, 10);
		}

		tinyMCE.executeCallback('oninit', '_oninit', 0);
	}
};

TinyMCE.prototype.removeMCEControl = function(editor_id) {
	var inst = tinyMCE.getInstanceById(editor_id);

	if (inst) {
		inst.switchSettings();

		editor_id = inst.editorId;
		var html = tinyMCE.getContent(editor_id);

		// Remove editor instance from instances array
		var tmpInstances = new Array();
		for (var instanceName in tinyMCE.instances) {
			var instance = tinyMCE.instances[instanceName];
			if (instanceName != editor_id)
					tmpInstances[instanceName] = instance;
		}
		tinyMCE.instances = tmpInstances;

		tinyMCE.selectedElement = null;
		tinyMCE.selectedInstance = null;

		// Remove element
		var replaceElement = document.getElementById(editor_id + "_parent");
		var oldTargetElement = inst.oldTargetElement;
		var targetName = oldTargetElement.nodeName.toLowerCase();

		if (targetName == "textarea" || targetName == "input") {
			// Just show the old text area
			replaceElement.parentNode.removeChild(replaceElement);
			oldTargetElement.style.display = "inline";
			oldTargetElement.value = html;
		} else {
			oldTargetElement.innerHTML = html;

			replaceElement.parentNode.insertBefore(oldTargetElement, replaceElement);
			replaceElement.parentNode.removeChild(replaceElement);
		}
	}
};

TinyMCE.prototype._cleanupElementName = function(element_name, element) {
	element_name = element_name.toLowerCase();

	// Never include body
	if (element_name == "body")
		return null;

	// If verification mode
	if (tinyMCE.cleanup_verify_html) {
		// Check if invalid element
		for (var i=0; i<tinyMCE.cleanup_invalidElements.length; i++) {
			if (tinyMCE.cleanup_invalidElements[i] == element_name)
				return null;
		}

		// Check if valid element
		var validElement = false;
		var elementAttribs = null;
		for (var i=0; i<tinyMCE.cleanup_validElements.length && !elementAttribs; i++) {
			for (var x=0, n=tinyMCE.cleanup_validElements[i][0].length; x<n; x++) {
				var elmMatch = tinyMCE.cleanup_validElements[i][0][x];

				if (elmMatch.charAt(0) == '+')
					elmMatch = elmMatch.substring(1);

				// Handle wildcard/regexp
				if (elmMatch.match(new RegExp('\\*|\\?|\\+', 'g')) != null) {
					elmMatch = elmMatch.replace(new RegExp('\\?', 'g'), '(\\S?)');
					elmMatch = elmMatch.replace(new RegExp('\\+', 'g'), '(\\S+)');
					elmMatch = elmMatch.replace(new RegExp('\\*', 'g'), '(\\S*)');
					elmMatch = "^" + elmMatch + "$";
					if (element_name.match(new RegExp(elmMatch, 'g'))) {
						elementAttribs = tinyMCE.cleanup_validElements[i];
						validElement = true;
						break;
					}
				}

				// Handle non regexp
				if (element_name == elmMatch) {
					elementAttribs = tinyMCE.cleanup_validElements[i];
					validElement = true;
					element_name = elementAttribs[0][0];
					break;
				}
			}
		}

		if (!validElement)
			return null;
	}

	// Special Mozilla stuff
	if (!tinyMCE.isMSIE) {
		// Fix for bug #958498
		if (element_name == "strong" && !tinyMCE.cleanup_on_save)
			element_name = "b";
		else if (element_name == "em" && !tinyMCE.cleanup_on_save)
			element_name = "i";
	}

	// Special MSIE stuff
	if (tinyMCE.isMSIE) {
		if (element_name == "table") {
			var attribValue = element.style.pixelWidth == 0 ? element.getAttribute("width") : element.style.pixelWidth;
			element.setAttribute("width", attribValue);

			attribValue = element.style.pixelHeight == 0 ? element.getAttribute("height") : element.style.pixelHeight;
			element.setAttribute("height", attribValue);
		}
	}

	var elmData = new Object();

	elmData.element_name = element_name;
	elmData.valid_attribs = elementAttribs;

	return elmData;
};

/**
 * Converts some element attributes to inline styles.
 */
TinyMCE.prototype._fixInlineStyles = function(elm) {
	var eName = elm.nodeName;

	if (elm.nodeName == "FONT") {
		// Move out color
		if ((c = tinyMCE.getAttrib(elm, "color")) != "") {
			elm.style.color = c;
		}
	}

	// Handle table, td and img elements
	if (eName == "TABLE" || eName == "TD" || eName == "IMG") {
		var value;

		// Setup width
		value = tinyMCE.isMSIE ? elm.width : elm.getAttribute("width");
		if (value && value != "") {
			if (typeof(value) != "string" || !value.indexOf("%"))
				value += "px";

			elm.style.width = value;
		}

		// Setup height
		value = tinyMCE.isMSIE ? elm.height : elm.getAttribute("height");
		if (value && value != "") {
			if (typeof(value) != "string" || !value.indexOf("%"))
				value += "px";

			elm.style.height = value;
		}

		// Setup border
		value = tinyMCE.isMSIE ? elm.border : elm.getAttribute("border");
		if (value && value != "" && (value != "0" && eName != "TABLE")) {
			elm.style.borderWidth = value + "px";
		}
	}

	// Setup align
	value = elm.getAttribute("align");
	if (value && value != "") {
		if (elm.nodeName.toLowerCase() == "img") {
			if (tinyMCE.isMSIE)
				elm.style.styleFloat = value;
			else
				elm.style.cssFloat = value;
		} else
			elm.style.textAlign = value;
	}

	// Setup vspace
	value = elm.getAttribute("vspace");
	if (value && value != "") {
		elm.style.marginTop = value + "px";
		elm.style.marginBottom = value + "px";
	}

	// Setup hspace
	value = elm.getAttribute("hspace");
	if (value && value != "") {
		elm.style.marginLeft = value + "px";
		elm.style.marginRight = value + "px";
	}
};

TinyMCE.prototype._cleanupAttribute = function(valid_attributes, element_name, attribute_node, element_node) {
	var attribName = attribute_node.nodeName.toLowerCase();
	var attribValue = attribute_node.nodeValue;
	var attribMustBeValue = null;
	var verified = false;

	// Inline styling, skip them
	if (tinyMCE.cleanup_inline_styles && (element_name == "table" || element_name == "td" || element_name == "img")) {
		if (attribName == "width" || attribName == "height" || attribName == "border" || attribName == "align" || attribName == "valign" || attribName == "hspace" || attribName == "vspace")
			return null;
	}

	// Mozilla attibute, remove them
	if (attribName.indexOf('moz_') != -1)
		return null;

	// Mozilla fix for drag-drop/copy/paste images
	if (!tinyMCE.isMSIE && (attribName == "mce_real_href" || attribName == "mce_real_src")) {
		if (!tinyMCE.cleanup_on_save) {
			var attrib = new Object();

			attrib.name = attribName;
			attrib.value = attribValue;

			return attrib;
		} else
			return null;
	}

	// Auto verify 
	if (attribName == "mce_onclick")
		verified = true;

	// Verify attrib
	if (tinyMCE.cleanup_verify_html && !verified) {
		for (var i=1; i<valid_attributes.length; i++) {
			var attribMatch = valid_attributes[i][0];
			var re = null;

			// Build regexp from wildcard
			if (attribMatch.match(new RegExp('\\*|\\?|\\+', 'g')) != null) {
				attribMatch = attribMatch.replace(new RegExp('\\?', 'g'), '(\\S?)');
				attribMatch = attribMatch.replace(new RegExp('\\+', 'g'), '(\\S+)');
				attribMatch = attribMatch.replace(new RegExp('\\*', 'g'), '(\\S*)');
				attribMatch = "^" + attribMatch + "$";
				re = new RegExp(attribMatch, 'g');
			}

			if ((re && attribName.match(re) != null) || attribName == attribMatch) {
				verified = true;
				attribMustBeValue = valid_attributes[i][3];
				break;
			}
		}

		// Allways pass styles on table and td elements if visual_aid
		if ((element_name == "table" || element_name == "td") && attribName == "style")
			verified = true;

		if (!verified)
			return false;
	} else
		verified = true;

	// Treat some attribs diffrent
	switch (attribName) {
		case "size":
			if (tinyMCE.isMSIE5 && element_name == "font")
				attribValue = element_node.size;
			break;

		case "color":
			if (tinyMCE.isMSIE5 && element_name == "font")
				attribValue = element_node.color;
			break;

		case "width":
			// MSIE 5.5 issue
			if (tinyMCE.isMSIE)
				attribValue = element_node.width;
			break;

		case "height":
			// MSIE 5.5 issue
			if (tinyMCE.isMSIE)
				attribValue = element_node.height;
			break;

		case "border":
			// MSIE 5.5 issue
			if (tinyMCE.isMSIE)
				attribValue = element_node.border;
			break;

//		case "className":
		case "class":
			if (element_name == "table" || element_name == "td") {
				// Handle visual aid
				if (tinyMCE.cleanup_visual_table_class != "")
					attribValue = tinyMCE.getVisualAidClass(attribValue, !tinyMCE.cleanup_on_save);
			}

			if (!tinyMCE._verifyClass(element_node) || attribValue == "")
				return null;

//			if (tinyMCE.isMSIE)
//				attribValue = node.getAttribute('className');

			break;

		case "style":
			attribValue = element_node.style.cssText.toLowerCase();

			// Compress borders some
			if (tinyMCE.isMSIE) {
				var border = element_node.style.border;
				var bt = element_node.style.borderTop;
				var bl = element_node.style.borderLeft;
				var br = element_node.style.borderRight;
				var bb = element_node.style.borderBottom;

				// All the same
				if (border != "" && (bt == border && bl == border && br == border && bb == border)) {
					attribValue = tinyMCE.regexpReplace(attribValue, 'border-top: ' + border + '?; ?', '');
					attribValue = tinyMCE.regexpReplace(attribValue, 'border-left: ' + border  + '?; ?', '');
					attribValue = tinyMCE.regexpReplace(attribValue, 'border-right: ' + border  + '?; ?', '');
					attribValue = tinyMCE.regexpReplace(attribValue, 'border-bottom: ' + border + '?;( ?)', 'border: ' + border + ';$1');
				}
			}
			break;

		// Handle onclick
		case "onclick":
		case "mce_onclick":
			// Skip on MSIE < 6+
			if (tinyMCE.isMSIE5)
				break;

			// Fix onclick attrib
			if (tinyMCE.cleanup_on_save) {
				if (element_node.getAttribute("mce_onclick")) {
					attribName = "onclick";
					attribValue = "" + element_node.getAttribute("mce_onclick");
				}
			} else {
				if (attribName == "onclick" && !tinyMCE.cleanup_on_save)
					return null;
			}

			break;

		// Convert the URLs of these
		case "href":
		case "src":
			// Fix for dragdrop/copy paste Mozilla issue
			if (!tinyMCE.isMSIE && attribName == "href" && element_node.getAttribute("mce_real_href"))
				attribValue = element_node.getAttribute("mce_real_href");

			// Fix for dragdrop/copy paste Mozilla issue
			if (!tinyMCE.isMSIE && attribName == "src" && element_node.getAttribute("mce_real_src"))
				attribValue = element_node.getAttribute("mce_real_src");

			// Force absolute URLs in Firefox
			if (tinyMCE.isGecko && !tinyMCE.settings['relative_urls'])
				attribValue = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], attribValue);

			attribValue = eval(tinyMCE.cleanup_urlconverter_callback + "(attribValue, element_node, tinyMCE.cleanup_on_save);");
			break;

		case "colspan":
		case "rowspan":
			// Not needed
			if (attribValue == "1")
				return null;
			break;

		// Skip these
		case "_moz-userdefined":
		case "editorid":
		case "editor_id":
		case "mce_real_href":
		case "mce_real_src":
			return null;
	}

	// Not the must be value
	if (attribMustBeValue != null) {
		var isCorrect = false;
		for (var i=0; i<attribMustBeValue.length; i++) {
			if (attribValue == attribMustBeValue[i]) {
				isCorrect = true;
				break;
			}
		}

		if (!isCorrect)
			return null;
	}

	var attrib = new Object();

	attrib.name = attribName;
	attrib.value = attribValue;

	return attrib;
};

TinyMCE.prototype._verifyClass = function(node) {
	// Sometimes the class gets set to null, weird Gecko bug?
	if (tinyMCE.isGecko) {
		var className = node.getAttribute('class');
		if (!className)
			return false;
	}

	// Trim CSS class
	if (tinyMCE.isMSIE)
		var className = node.getAttribute('className');

	if (tinyMCE.cleanup_verify_css_classes && tinyMCE.cleanup_on_save) {
		var csses = tinyMCE.getCSSClasses();
		nonDefinedCSS = true;
		for (var c=0; c<csses.length; c++) {
			if (csses[c] == className) {
				nonDefinedCSS = false;
				break;
			}
		}

		if (nonDefinedCSS && className.indexOf('mce_') != 0) {
			node.removeAttribute('className');
			node.removeAttribute('class');
			return false;
		}
	}

	return true;
};

TinyMCE.prototype.cleanupNode = function(node) {
	var output = "";

	switch (node.nodeType) {
		case 1: // Element
			var elementData = tinyMCE._cleanupElementName(node.nodeName, node);
			var elementName = elementData ? elementData.element_name : null;
			var elementValidAttribs = elementData ? elementData.valid_attribs : null;
			var elementAttribs = "";
			var openTag = false;

			if (elementName != null && elementName.charAt(0) == '+') {
				elementName = elementName.substring(1);
				openTag = true;
			}

			// Checking DOM tree for MSIE weirdness!!
			if (tinyMCE.isMSIE && tinyMCE.settings['fix_content_duplication']) {
				var lookup = tinyMCE.cleanup_elementLookupTable;

				for (var i=0; i<lookup.length; i++) {
					// Found element reference else were, hmm?
					if (lookup[i] == node)
						return output;
				}

				// Add element to lookup table
				lookup[lookup.length] = node;
			}

			// Element not valid (only render children)
			if (!elementName) {
				if (node.hasChildNodes()) {
					for (var i=0; i<node.childNodes.length; i++)
						output += this.cleanupNode(node.childNodes[i]);
				}

				return output;
			}

			// Has mso/microsuck crap or empty attrib
			if (node.style && (node.style.cssText.indexOf('mso-') != -1 && tinyMCE.settings['auto_cleanup_word']) || node.style.cssText == "") {
				node.style.cssText = "";
				node.removeAttribute("style");
			}

			// Handle inline styles
			if (tinyMCE.cleanup_inline_styles)
				tinyMCE._fixInlineStyles(node);

			// Set attrib data
			if (elementValidAttribs) {
				for (var a=1; a<elementValidAttribs.length; a++) {
					var attribName, attribDefaultValue, attribForceValue, attribValue;

					attribName = elementValidAttribs[a][0];
					attribDefaultValue = elementValidAttribs[a][1];
					attribForceValue = elementValidAttribs[a][2];

					if (attribDefaultValue != null || attribForceValue != null) {
						var attribValue = node.getAttribute(attribName);

						if (node.getAttribute(attribName) == null || node.getAttribute(attribName) == "")
							attribValue = attribDefaultValue;

						attribValue = attribForceValue ? attribForceValue : attribValue;

						// Is to generate id
						if (attribValue == "{$uid}")
							attribValue = "uid_" + (tinyMCE.cleanup_idCount++);

						// Add visual aid class
						if (attribName == "class")
							attribValue = tinyMCE.getVisualAidClass(attribValue, tinyMCE.cleanup_on_save);

						node.setAttribute(attribName, attribValue);
						//alert(attribName + "=" + attribValue);
					}
				}
			}

			// Remove non needed span elements
			if (elementName == "span" && tinyMCE.cleanup_trim_span_elements) {
				var re = new RegExp('^[ \t]+', 'g');
				var onlyWhiteSpace = true;
				for (var a=0; a<node.childNodes.length; a++) {
					var tmpNode = node.childNodes[a];
					if ((tmpNode.nodeType == 3 && !tmpNode.nodeValue.match(re)) || tmpNode.nodeName.toLowerCase() != "span") {
						onlyWhiteSpace = false;
						break;
					}
				}

				// Count attributes
				tinyMCE._verifyClass(node);
				var numAttribs = 0;
				for (var i=0; i<node.attributes.length; i++) {
					if (node.attributes[i].specified)
						numAttribs++;
				}

				// Is not a valid span, remove it
				if (onlyWhiteSpace || numAttribs == 0) {
					if (node.hasChildNodes()) {
						for (var i=0; i<node.childNodes.length; i++)
							output += this.cleanupNode(node.childNodes[i]);
					}

					return output;
				}
			}

			// Add some visual aids
/*			if (elementName == "table" || elementName == "td") {
				// Handle visual aid
				if (tinyMCE.cleanup_visual_table_class != "") {
					// Find parent table
					var tableElement = node;
					if (elementName == "td")
						tableElement = tinyMCE.getParentElement(tableElement, "table");

					if (tableElement && tableElement.getAttribute("border") == 0) {
						if (tinyMCE.cleanup_visual_aid)
							elementAttribs += ' class="' + tinyMCE.getVisualAidClass(tinyMCE.getAttrib(node, "class")) + '"';
					}
				}
			}*/

			// Remove empty tables
			if (elementName == "table" && !node.hasChildNodes())
				return "";

			// Fix width/height attributes if the styles is specified
			if (tinyMCE.isGecko && elementName == "img") {
				var w = node.style.width;
				if (w != null && w != "")
					node.setAttribute("width", w);

				var h = node.style.height;
				if (h != null && h != "")
					node.setAttribute("height", h);
			}

			// Handle element attributes
			if (node.attributes.length > 0) {
				for (var i=0; i<node.attributes.length; i++) {
					if (node.attributes[i].specified) {
						var attrib = tinyMCE._cleanupAttribute(elementValidAttribs, elementName, node.attributes[i], node);
						if (attrib)
							elementAttribs += " " + attrib.name + "=" + '"' + attrib.value + '"';
					}
				}

				//alert(elementAttribs);
			}

			// MSIE form element issue
			if (tinyMCE.isMSIE && elementName == "input") {
				if (node.type) {
					if (!elementAttribs.match(/ type=/g))
						elementAttribs += " type=" + '"' + node.type + '"';
				}

				if (node.value) {
					if (!elementAttribs.match(/ value=/g))
						elementAttribs += " value=" + '"' + node.value + '"';
				}
			}

			// Add nbsp to some elements
			if ((elementName == "p" || elementName == "td") && (node.innerHTML == "" || node.innerHTML == "&nbsp;"))
				return "<" + elementName + elementAttribs + ">" + this.convertStringToXML(String.fromCharCode(160)) + "</" + elementName + ">";

			// Is MSIE script element
			if (tinyMCE.isMSIE && elementName == "script")
				return "<" + elementName + elementAttribs + ">" + node.text + "</" + elementName + ">";

			// Clean up children
			if (node.hasChildNodes()) {
				// Force BR
				if (elementName == "p" && tinyMCE.cleanup_force_br_newlines)
					output += "<div" + elementAttribs + ">";
				else
					output += "<" + elementName + elementAttribs + ">";

				for (var i=0; i<node.childNodes.length; i++)
					output += this.cleanupNode(node.childNodes[i]);

				// Force BR
				if (elementName == "p" && tinyMCE.cleanup_force_br_newlines)
					output += "</div><br />";
				else
					output += "</" + elementName + ">";
			} else {
				// Allways leave anchor elements open
				if (openTag)
					output += "<" + elementName + elementAttribs + "></" + elementName + ">";
				else {
					// No children
					output += "<" + elementName + elementAttribs + " />";
				}
			}

			return output;

		case 3: // Text
			// Do not convert script elements
			if (node.parentNode.nodeName.toLowerCase() == "script")
				return node.nodeValue;

			return this.convertStringToXML(node.nodeValue);

		case 8: // Comment
			return "<!--" + node.nodeValue + "-->";

		default: // Unknown
			return "[UNKNOWN NODETYPE " + node.nodeType + "]";
	}
};

TinyMCE.prototype.convertStringToXML = function(html_data) {
    var output = "";

	for (var i=0; i<html_data.length; i++) {
		var chr = html_data.charCodeAt(i);

		// Numeric entities
		if (tinyMCE.settings['entity_encoding'] == "numeric") {
			if (chr > 127)
				output += '&#' + chr + ";";
			else
				output += String.fromCharCode(chr);

			continue;
		}

		// Raw entities
		if (tinyMCE.settings['entity_encoding'] == "raw") {
			output += String.fromCharCode(chr);
			continue;
		}

		// Named entities
		if (typeof(tinyMCE.cleanup_entities["c" + chr]) != 'undefined' && tinyMCE.cleanup_entities["c" + chr] != '')
			output += '&' + tinyMCE.cleanup_entities["c" + chr] + ';';
		else
			output += '' + String.fromCharCode(chr);
    }

    return output;
};

TinyMCE.prototype._getCleanupElementName = function(chunk) {
	var pos;

	if (chunk.charAt(0) == '+')
		chunk = chunk.substring(1);

	if ((pos = chunk.indexOf('/')) != -1)
		chunk = chunk.substring(0, pos);

	if ((pos = chunk.indexOf('[')) != -1)
		chunk = chunk.substring(0, pos);

	return chunk;
};

TinyMCE.prototype._initCleanup = function() {
	// Parse valid elements and attributes
	var validElements = tinyMCE.settings["valid_elements"];
	validElements = validElements.split(',');

	// Handle extended valid elements
	var extendedValidElements = tinyMCE.settings["extended_valid_elements"];
	extendedValidElements = extendedValidElements.split(',');
	for (var i=0; i<extendedValidElements.length; i++) {
		var elementName = this._getCleanupElementName(extendedValidElements[i]);
		var skipAdd = false;

		// Check if it's defined before, if so override that one
		for (var x=0; x<validElements.length; x++) {
			if (this._getCleanupElementName(validElements[x]) == elementName) {
				validElements[x] = extendedValidElements[i];
				skipAdd = true;
				break;
			}
		}

		if (!skipAdd)
			validElements[validElements.length] = extendedValidElements[i];
	}

	for (var i=0; i<validElements.length; i++) {
		var item = validElements[i];

		item = item.replace('[','|');
		item = item.replace(']','');

		// Split and convert
		var attribs = item.split('|');
		for (var x=0; x<attribs.length; x++)
			attribs[x] = attribs[x].toLowerCase();

		// Handle change elements
		attribs[0] = attribs[0].split('/');

		// Handle default attribute values
		for (var x=1; x<attribs.length; x++) {
			var attribName = attribs[x];
			var attribDefault = null;
			var attribForce = null;
			var attribMustBe = null;

			// Default value
			if ((pos = attribName.indexOf('=')) != -1) {
				attribDefault = attribName.substring(pos+1);
				attribName = attribName.substring(0, pos);
			}

			// Force check
			if ((pos = attribName.indexOf(':')) != -1) {
				attribForce = attribName.substring(pos+1);
				attribName = attribName.substring(0, pos);
			}

			// Force check
			if ((pos = attribName.indexOf('<')) != -1) {
				attribMustBe = attribName.substring(pos+1).split('?');
				attribName = attribName.substring(0, pos);
			}

			attribs[x] = new Array(attribName, attribDefault, attribForce, attribMustBe);
		}

		validElements[i] = attribs;
	}

	var invalidElements = tinyMCE.settings['invalid_elements'].split(',');
	for (var i=0; i<invalidElements.length; i++)
		invalidElements[i] = invalidElements[i].toLowerCase();

	// Set these for performance
	tinyMCE.settings['cleanup_validElements'] = validElements;
	tinyMCE.settings['cleanup_invalidElements'] = invalidElements;

	// Setup entities
	tinyMCE.settings['cleanup_entities'] = new Array();
	var entities = tinyMCE.getParam('entities', '', true, ',');
	for (var i=0; i<entities.length; i+=2)
		tinyMCE.settings['cleanup_entities']['c' + entities[i]] = entities[i+1];
};

TinyMCE.prototype._cleanupHTML = function(doc, config, element, visual, on_save) {
	if (!tinyMCE.settings['cleanup'])
		return element.innerHTML;

	// Call custom cleanup code
	tinyMCE._customCleanup(on_save ? "get_from_editor_dom" : "insert_to_editor_dom", doc.body);

	// Set these for performance
	tinyMCE.cleanup_validElements = tinyMCE.settings['cleanup_validElements'];
	tinyMCE.cleanup_entities = tinyMCE.settings['cleanup_entities'];
	tinyMCE.cleanup_invalidElements = tinyMCE.settings['cleanup_invalidElements'];
	tinyMCE.cleanup_verify_html = tinyMCE.settings['verify_html'];
	tinyMCE.cleanup_force_br_newlines = tinyMCE.settings['force_br_newlines'];
	tinyMCE.cleanup_urlconverter_callback = tinyMCE.settings['urlconverter_callback'];
	tinyMCE.cleanup_verify_css_classes = tinyMCE.settings['verify_css_classes'];
	tinyMCE.cleanup_visual_table_class = tinyMCE.settings['visual_table_class'];
	tinyMCE.cleanup_apply_source_formatting = tinyMCE.settings['apply_source_formatting'];
	tinyMCE.cleanup_trim_span_elements = tinyMCE.settings['trim_span_elements'];
	tinyMCE.cleanup_inline_styles = tinyMCE.settings['inline_styles'];
	tinyMCE.cleanup_visual_aid = visual;
	tinyMCE.cleanup_on_save = on_save;
	tinyMCE.cleanup_idCount = 0;
	tinyMCE.cleanup_elementLookupTable = new Array();

	var startTime = new Date().getTime();

	tinyMCE._convertOnClick(element);

	// Cleanup madness that breaks the editor in MSIE
	if (tinyMCE.isMSIE) {
		element.innerHTML = tinyMCE.regexpReplace(element.innerHTML, '<p>[ \n\r]*<hr id=null>[ \n\r]*</p>', '<hr />', 'gi');
		element.innerHTML = tinyMCE.regexpReplace(element.innerHTML, '<!([^-(DOCTYPE)]* )|<!/[^-]*>', '', 'gi');
	}

	var html = this.cleanupNode(element);

	if (tinyMCE.settings['debug'])
		alert("Cleanup process executed in: " + (new Date().getTime()-startTime) + " ms.");

	// Remove pesky HR paragraphs
	html = tinyMCE.regexpReplace(html, '<p><hr /></p>', '<hr />');
	html = tinyMCE.regexpReplace(html, '<p>&nbsp;</p><hr /><p>&nbsp;</p>', '<hr />');

	// Remove some mozilla crap
	if (!tinyMCE.isMSIE)
		html = html.replace(new RegExp('<o:p _moz-userdefined="" />', 'g'), "");

	if (tinyMCE.settings['apply_source_formatting']) {
		html = html.replace(new RegExp('<(p|div)([^>]*)>', 'g'), "\n<$1$2>\n");
		html = html.replace(new RegExp('<\/(p|div)([^>]*)>', 'g'), "\n</$1$2>\n");
		html = html.replace(new RegExp('<br />', 'g'), "<br />\n");
	}

	if (tinyMCE.settings['force_br_newlines']) {
		var re = new RegExp('<p>&nbsp;</p>', 'g');
		html = html.replace(re, "<br />");
	}

	if (tinyMCE.settings['force_p_newlines']) {
		// Remove weridness!
		var re = new RegExp('&lt;&gt;', 'g');
		html = html.replace(re, "");
	}

	if (tinyMCE.settings['remove_linebreaks'])
		html = html.replace(new RegExp('\r|\n', 'g'), ' ');

	// Call custom cleanup code
	html = tinyMCE._customCleanup(on_save ? "get_from_editor" : "insert_to_editor", html);

	// Emtpy node, return empty
	var chk = tinyMCE.regexpReplace(html, "[ \t\r\n]", "").toLowerCase();
	if (chk == "<br/>" || chk == "<br>" || chk == "<p>&nbsp;</p>" || chk == "<p>&#160;</p>" || chk == "<p></p>")
		html = "";

	if (tinyMCE.settings["preformatted"])
		return "<pre>" + html + "</pre>";

	return html;
};

TinyMCE.prototype.setAttrib = function(element, name, value, no_fix_value) {
	if (!no_fix_value && value != null) {
		var re = new RegExp('[^0-9%]', 'g');
		value = value.replace(re, '');
	}

	if (value != null && value != "")
		element.setAttribute(name, value);
	else
		element.removeAttribute(name);

	if (value != null && value != "")
		element.setAttribute(name, value);
	else
		element.removeAttribute(name);
};

TinyMCE.prototype.insertLink = function(href, target, title, onclick, style_class) {
	this.execCommand("mceAddUndoLevel");

	if (this.selectedInstance && this.selectedElement && this.selectedElement.nodeName.toLowerCase() == "img") {
		var doc = this.selectedInstance.getDoc();
		var linkElement = tinyMCE.getParentElement(this.selectedElement, "a");
		var newLink = false;

		if (!linkElement) {
			linkElement = doc.createElement("a");
			newLink = true;
		}

		href = eval(tinyMCE.settings['urlconverter_callback'] + "(href, linkElement);");
		tinyMCE.setAttrib(linkElement, 'href', href);
		tinyMCE.setAttrib(linkElement, 'target', target);
		tinyMCE.setAttrib(linkElement, 'title', title);
        tinyMCE.setAttrib(linkElement, 'mce_onclick', onclick);
		tinyMCE.setAttrib(linkElement, 'class', style_class);

		if (newLink) {
			linkElement.appendChild(this.selectedElement.cloneNode(true));
			this.selectedElement.parentNode.replaceChild(linkElement, this.selectedElement);
		}

		return;
	}

	if (!this.linkElement && this.selectedInstance) {
		if (tinyMCE.isSafari) {
			tinyMCE.execCommand("mceInsertContent", false, '<a href="#mce_temp_url#">' + this.selectedInstance.getSelectedHTML() + '</a>');
		} else
			this.selectedInstance.contentDocument.execCommand("createlink", false, "#mce_temp_url#");

		tinyMCE.linkElement = this.getElementByAttributeValue(this.selectedInstance.contentDocument.body, "a", "href", "#mce_temp_url#");

		var elementArray = this.getElementsByAttributeValue(this.selectedInstance.contentDocument.body, "a", "href", "#mce_temp_url#");

		for (var i=0; i<elementArray.length; i++) {
			href = eval(tinyMCE.settings['urlconverter_callback'] + "(href, elementArray[i]);");
			tinyMCE.setAttrib(elementArray[i], 'href', href);
			tinyMCE.setAttrib(elementArray[i], 'mce_real_href', href);
			tinyMCE.setAttrib(elementArray[i], 'target', target);
			tinyMCE.setAttrib(elementArray[i], 'title', title);
            tinyMCE.setAttrib(elementArray[i], 'mce_onclick', onclick);
			tinyMCE.setAttrib(elementArray[i], 'class', style_class);
		}

		tinyMCE.linkElement = elementArray[0];
	}

	if (this.linkElement) {
		href = eval(tinyMCE.settings['urlconverter_callback'] + "(href, this.linkElement);");
		tinyMCE.setAttrib(this.linkElement, 'href', href);
		tinyMCE.setAttrib(this.linkElement, 'mce_real_href', href);
		tinyMCE.setAttrib(this.linkElement, 'target', target);
		tinyMCE.setAttrib(this.linkElement, 'title', title);
        tinyMCE.setAttrib(this.linkElement, 'mce_onclick', onclick);
		tinyMCE.setAttrib(this.linkElement, 'class', style_class);
	}
};

TinyMCE.prototype.insertImage = function(src, alt, border, hspace, vspace, width, height, align, title, onmouseover, onmouseout) {
	if (src == "")
		return;

	this.execCommand("mceAddUndoLevel");

	if (!this.imgElement && tinyMCE.isSafari) {
		var html = "";

		html += '<img src="' + src + '" alt="' + alt + '"';
		html += ' border="' + border + '" hspace="' + hspace + '"';
		html += ' vspace="' + vspace + '" width="' + width + '"';
		html += ' height="' + height + '" align="' + align + '" title="' + title + '" onmouseover="' + onmouseover + '" onmouseout="' + onmouseout + '" />';

		tinyMCE.execCommand("mceInsertContent", false, html);
	} else {
		if (!this.imgElement && this.selectedInstance) {
			if (tinyMCE.isSafari)
				tinyMCE.execCommand("mceInsertContent", false, '<img src="#mce_temp_url#" />');
			else
				this.selectedInstance.contentDocument.execCommand("insertimage", false, "#mce_temp_url#");

			tinyMCE.imgElement = this.getElementByAttributeValue(this.selectedInstance.contentDocument.body, "img", "src", "#mce_temp_url#");
		}
	}

	if (this.imgElement) {
		var needsRepaint = false;

		src = eval(tinyMCE.settings['urlconverter_callback'] + "(src, tinyMCE.imgElement);");

		if (onmouseover && onmouseover != "")
			onmouseover = "this.src='" + eval(tinyMCE.settings['urlconverter_callback'] + "(onmouseover, tinyMCE.imgElement);") + "';";

		if (onmouseout && onmouseout != "")
			onmouseout = "this.src='" + eval(tinyMCE.settings['urlconverter_callback'] + "(onmouseout, tinyMCE.imgElement);") + "';";

		// Use alt as title if it's undefined
		if (typeof(title) == "undefined")
			title = alt;

		if (width != this.imgElement.getAttribute("width") || height != this.imgElement.getAttribute("height") || align != this.imgElement.getAttribute("align"))
			needsRepaint = true;

		tinyMCE.setAttrib(this.imgElement, 'src', src);
		tinyMCE.setAttrib(this.imgElement, 'mce_real_src', src);
		tinyMCE.setAttrib(this.imgElement, 'alt', alt);
		tinyMCE.setAttrib(this.imgElement, 'title', title);
		tinyMCE.setAttrib(this.imgElement, 'align', align);
		tinyMCE.setAttrib(this.imgElement, 'border', border, true);
		tinyMCE.setAttrib(this.imgElement, 'hspace', hspace, true);
		tinyMCE.setAttrib(this.imgElement, 'vspace', vspace, true);
		tinyMCE.setAttrib(this.imgElement, 'width', width, true);
		tinyMCE.setAttrib(this.imgElement, 'height', height, true);
		tinyMCE.setAttrib(this.imgElement, 'onmouseover', onmouseover);
		tinyMCE.setAttrib(this.imgElement, 'onmouseout', onmouseout);

		// Fix for bug #989846 - Image resize bug
		if (width && width != "")
			this.imgElement.style.pixelWidth = width;

		if (height && height != "")
			this.imgElement.style.pixelHeight = height;

		if (needsRepaint)
			tinyMCE.selectedInstance.repaint();
	}
};

TinyMCE.prototype.getElementByAttributeValue = function(node, element_name, attrib, value) {
	var elements = this.getElementsByAttributeValue(node, element_name, attrib, value);
	if (elements.length == 0)
		return null;

	return elements[0];
};

TinyMCE.prototype.getElementsByAttributeValue = function(node, element_name, attrib, value) {
	var elements = new Array();

	if (node && node.nodeName.toLowerCase() == element_name) {
		if (node.getAttribute(attrib) && node.getAttribute(attrib).indexOf(value) != -1)
			elements[elements.length] = node;
	}

	if (node.hasChildNodes) {
		for (var x=0, n=node.childNodes.length; x<n; x++) {
			var childElements = this.getElementsByAttributeValue(node.childNodes[x], element_name, attrib, value);
			for (var i=0, m=childElements.length; i<m; i++)
				elements[elements.length] = childElements[i];
		}
	}

	return elements;
};

TinyMCE.prototype.isBlockElement = function(node) {
	return node != null && node.nodeType == 1 && this.blockRegExp.test(node.nodeName);
};

TinyMCE.prototype.getParentBlockElement = function(node) {
	// Search up the tree for block element
	while (node) {
		if (this.blockRegExp.test(node.nodeName))
			return node;

		node = node.parentNode;
	}

	return null;
};

TinyMCE.prototype.getNodeTree = function(node, node_array, type, node_name) {
	if (typeof(type) == "undefined" || node.nodeType == type && (typeof(node_name) == "undefined" || node.nodeName.toLowerCase() == node_name.toLowerCase()))
		node_array[node_array.length] = node;

	if (node.hasChildNodes()) {
		for (var i=0; i<node.childNodes.length; i++)
			tinyMCE.getNodeTree(node.childNodes[i], node_array, type, node_name);
	}

	return node_array;
};

TinyMCE.prototype.getParentElement = function(node, names, attrib_name, attrib_value) {
	if (typeof(names) == "undefined") {
		if (node.nodeType == 1)
			return node;

		// Find parent node that is a element
		while ((node = node.parentNode) != null && node.nodeType != 1) ;

		return node;
	}

	var namesAr = names.split(',');

	if (node == null)
		return null;

	do {
		for (var i=0; i<namesAr.length; i++) {
			if (node.nodeName.toLowerCase() == namesAr[i].toLowerCase() || names == "*") {
				if (typeof(attrib_name) == "undefined")
					return node;
				else if (node.getAttribute(attrib_name)) {
					if (typeof(attrib_value) == "undefined") {
						if (node.getAttribute(attrib_name) != "")
							return node;
					} else if (node.getAttribute(attrib_name) == attrib_value)
						return node;
				}
			}
		}
	} while (node = node.parentNode);

	return null;
};

TinyMCE.prototype.convertURL = function(url, node, on_save) {
	var prot = document.location.protocol;
	var host = document.location.hostname;
	var port = document.location.port;

	var fileProto = (prot == "file:");

	// Something is wrong, remove weirdness
	url = tinyMCE.regexpReplace(url, '(http|https):///', '/');

	// Mailto link or anchor (Pass through)
	if (url.indexOf('mailto:') != -1 || url.indexOf('javascript:') != -1 || tinyMCE.regexpReplace(url,'[ \t\r\n\+]|%20','').charAt(0) == "#")
		return url;

	// Fix relative/Mozilla
	if (!tinyMCE.isMSIE && !on_save && url.indexOf("://") == -1 && url.charAt(0) != '/')
		return tinyMCE.settings['base_href'] + url;

	// Handle absolute url anchors
	if (!tinyMCE.settings['relative_urls']) {
		var urlParts = tinyMCE.parseURL(url);
		var baseUrlParts = tinyMCE.parseURL(tinyMCE.settings['base_href']);

		// If anchor and path is the same page
		if (urlParts['anchor'] && urlParts['path'] == baseUrlParts['path'])
			return "#" + urlParts['anchor'];
	}

	// Convert to relative urls
	if (on_save && tinyMCE.settings['relative_urls']) {
		var urlParts = tinyMCE.parseURL(url);

		// If not absolute url, do nothing (Mozilla)
		// WEIRD STUFF?!
/*		if (!urlParts['protocol'] && !tinyMCE.isMSIE) {
			var urlPrefix = "http://";
			urlPrefix += host;
			if (port != "")
				urlPrefix += ":" + port;

			url = urlPrefix + url;
			urlParts = tinyMCE.parseURL(url);
		}*/

		var tmpUrlParts = tinyMCE.parseURL(tinyMCE.settings['document_base_url']);

		// Link is within this site
		if (urlParts['host'] == tmpUrlParts['host'] && (!urlParts['port'] || urlParts['port'] == tmpUrlParts['port']))
			return tinyMCE.convertAbsoluteURLToRelativeURL(tinyMCE.settings['document_base_url'], url);
	}

	// Remove current domain
	if (!fileProto && tinyMCE.settings['remove_script_host']) {
		var start = "", portPart = "";

		if (port != "")
			portPart = ":" + port;

		start = prot + "//" + host + portPart + "/";

		if (url.indexOf(start) == 0)
			url = url.substring(start.length-1);

		// Add first slash if missing on a absolute URL
		if (!tinyMCE.settings['relative_urls'] && url.indexOf('://') == -1 && url.charAt(0) != '/')
			url = '/' + url;
	}

	return url;
};

/**
 * Parses a URL in to its diffrent components.
 */
TinyMCE.prototype.parseURL = function(url_str) {
	var urlParts = new Array();

	if (url_str) {
		var pos, lastPos;

		// Parse protocol part
		pos = url_str.indexOf('://');
		if (pos != -1) {
			urlParts['protocol'] = url_str.substring(0, pos);
			lastPos = pos + 3;
		}

		// Find port or path start
		for (var i=lastPos; i<url_str.length; i++) {
			var chr = url_str.charAt(i);

			if (chr == ':')
				break;

			if (chr == '/')
				break;
		}
		pos = i;

		// Get host
		urlParts['host'] = url_str.substring(lastPos, pos);

		// Get port
		lastPos = pos;
		if (url_str.charAt(pos) == ':') {
			pos = url_str.indexOf('/', lastPos);
			urlParts['port'] = url_str.substring(lastPos+1, pos);
		}

		// Get path
		lastPos = pos;
		pos = url_str.indexOf('?', lastPos);

		if (pos == -1)
			pos = url_str.indexOf('#', lastPos);

		if (pos == -1)
			pos = url_str.length;

		urlParts['path'] = url_str.substring(lastPos, pos);

		// Get query
		lastPos = pos;
		if (url_str.charAt(pos) == '?') {
			pos = url_str.indexOf('#');
			pos = (pos == -1) ? url_str.length : pos;
			urlParts['query'] = url_str.substring(lastPos+1, pos);
		}

		// Get anchor
		lastPos = pos;
		if (url_str.charAt(pos) == '#') {
			pos = url_str.length;
			urlParts['anchor'] = url_str.substring(lastPos+1, pos);
		}
	}

	return urlParts;
};

/**
 * Converts an absolute path to relative path.
 */
TinyMCE.prototype.convertAbsoluteURLToRelativeURL = function(base_url, url_to_relative) {
	var strTok1;
	var strTok2;
	var breakPoint = 0;
	var outputString = "";

	// Crop away last path part
	base_url = base_url.substring(0, base_url.lastIndexOf('/'));
	strTok1 = base_url.split('/');
	strTok2 = url_to_relative.split('/');

	if (strTok1.length >= strTok2.length) {
		for (var i=0; i<strTok1.length; i++) {
			if (i >= strTok2.length || strTok1[i] != strTok2[i]) {
				breakPoint = i + 1;
				break;
			}
		}
	}

	if (strTok1.length < strTok2.length) {
		for (var i=0; i<strTok2.length; i++) {
			if (i >= strTok1.length || strTok1[i] != strTok2[i]) {
				breakPoint = i + 1;
				break;
			}
		}
	}

	if (breakPoint == 1)
		return url_to_relative;

	for (var i=0; i<(strTok1.length-(breakPoint-1)); i++)
		outputString += "../";

	for (var i=breakPoint-1; i<strTok2.length; i++) {
		if (i != (breakPoint-1))
			outputString += "/" + strTok2[i];
		else
			outputString += strTok2[i];
	}

	return outputString;
};

TinyMCE.prototype.convertRelativeToAbsoluteURL = function(base_url, relative_url) {
	var baseURL = TinyMCE.prototype.parseURL(base_url);
	var relURL = TinyMCE.prototype.parseURL(relative_url);

	if (relative_url == "" || relative_url.charAt(0) == '/' || relative_url.indexOf('://') != -1 || relative_url.indexOf('mailto:') != -1 || relative_url.indexOf('javascript:') != -1 || tinyMCE.regexpReplace(relative_url,'[ \t\r\n\+]|%20','').charAt(0) == "#")
		return relative_url;

	// Split parts
	baseURLParts = baseURL['path'].split('/');
	relURLParts = relURL['path'].split('/');

	// Remove empty chunks
	var newBaseURLParts = new Array();
	for (var i=baseURLParts.length-1; i>=0; i--) {
		if (baseURLParts[i].length == 0)
			continue;

		newBaseURLParts[newBaseURLParts.length] = baseURLParts[i];
	}
	baseURLParts = newBaseURLParts.reverse();

	// Merge relURLParts chunks
	var newRelURLParts = new Array();
	var numBack = 0;
	for (var i=relURLParts.length-1; i>=0; i--) {
		if (relURLParts[i].length == 0 || relURLParts[i] == ".")
			continue;

		if (relURLParts[i] == '..') {
			numBack++;
			continue;
		}

		if (numBack > 0) {
			numBack--;
			continue;
		}

		newRelURLParts[newRelURLParts.length] = relURLParts[i];
	}

	relURLParts = newRelURLParts.reverse();

	// Remove end from absolute path
	var len = baseURLParts.length-numBack;
	var absPath = (len <= 0 ? "" : "/") + baseURLParts.slice(0, len).join('/') + "/" + relURLParts.join('/');
	var start = "", end = "";

	// Build start part
	if (baseURL['protocol'])
		start += baseURL['protocol'] + "://";

	if (baseURL['host'])
		start += baseURL['host'];

	if (baseURL['port'])
		start += ":" + baseURL['port'];

	// Build end part
	if (relURL['query'])
		end += "?" + relURL['query'];

	if (relURL['anchor'])
		end += "#" + relURL['anchor'];

	// Re-add trailing slash if it's removed
	if (relative_url.charAt(relative_url.length-1) == "/")
		end += "/";

	return start + absPath + end;
};

TinyMCE.prototype.getParam = function(name, default_value, strip_whitespace, split_chr) {
	var value = (typeof(this.settings[name]) == "undefined") ? default_value : this.settings[name];

	// Fix bool values
	if (value == "true" || value == "false")
		return (value == "true");

	if (strip_whitespace)
		value = tinyMCE.regexpReplace(value, "[ \t\r\n]", "");

	if (typeof(split_chr) != "undefined" && split_chr != null) {
		value = value.split(split_chr);
		var outArray = new Array();

		for (var i=0; i<value.length; i++) {
			if (value[i] && value[i] != "")
				outArray[outArray.length] = value[i];
		}

		value = outArray;
	}

	return value;
};

TinyMCE.prototype.getLang = function(name, default_value, parse_entities) {
	var value = (typeof(tinyMCELang[name]) == "undefined") ? default_value : tinyMCELang[name];

	if (parse_entities) {
		var el = document.createElement("div");
		el.innerHTML = value;
		value = el.innerHTML;
	}

	return value;
};

TinyMCE.prototype.replaceVar = function(replace_haystack, replace_var, replace_str) {
	var re = new RegExp('{\\\$' + replace_var + '}', 'g');
	return replace_haystack.replace(re, replace_str);
};

TinyMCE.prototype.replaceVars = function(replace_haystack, replace_vars) {
	for (var key in replace_vars) {
		var value = replace_vars[key];
		replace_haystack = tinyMCE.replaceVar(replace_haystack, key, value);
	}

	return replace_haystack;
};

TinyMCE.prototype.triggerNodeChange = function(focus, setup_content) {
	if (tinyMCE.settings['handleNodeChangeCallback']) {
		if (tinyMCE.selectedInstance) {
			var inst = tinyMCE.selectedInstance;
			var editorId = inst.editorId;
			var elm = (typeof(setup_content) != "undefined" && setup_content) ? tinyMCE.selectedElement : inst.getFocusElement();
			var undoIndex = -1;
			var undoLevels = -1;
			var anySelection = false;
			var selectedText = inst.getSelectedText();

			if (tinyMCE.settings["auto_resize"]) {
				var doc = inst.getDoc();

				inst.iframeElement.style.width = doc.body.offsetWidth + "px";
				inst.iframeElement.style.height = doc.body.offsetHeight + "px";
			}

			if (tinyMCE.selectedElement)
				anySelection = (tinyMCE.selectedElement.nodeName.toLowerCase() == "img") || (selectedText && selectedText.length > 0);

			if (tinyMCE.settings['custom_undo_redo']) {
				undoIndex = inst.undoIndex;
				undoLevels = inst.undoLevels.length;
			}

			tinyMCE.executeCallback('handleNodeChangeCallback', '_handleNodeChange', 0, editorId, elm, undoIndex, undoLevels, inst.visualAid, anySelection);
		}
	}

	if (this.selectedInstance && (typeof(focus) == "undefined" || focus))
		this.selectedInstance.contentWindow.focus();
};

TinyMCE.prototype._customCleanup = function(type, content) {
	// Call custom cleanup
	var customCleanup = tinyMCE.settings['cleanup_callback'];
	if (customCleanup != "" && eval("typeof(" + customCleanup + ")") != "undefined")
		content = eval(customCleanup + "(type, content);");

	// Trigger plugin cleanups
	var plugins = tinyMCE.getParam('plugins', '', true, ',');
	for (var i=0; i<plugins.length; i++) {
		if (eval("typeof(TinyMCE_" + plugins[i] +  "_cleanup)") != "undefined")
			content = eval("TinyMCE_" + plugins[i] +  "_cleanup(type, content);");
	}

	return content;
};

TinyMCE.prototype.getContent = function(editor_id) {
	if (typeof(editor_id) != "undefined")
		tinyMCE.selectedInstance = tinyMCE.getInstanceById(editor_id);

	if (tinyMCE.selectedInstance)
		return tinyMCE._cleanupHTML(this.selectedInstance.getDoc(), tinyMCE.settings, this.selectedInstance.getBody(), false, true);

	return null;
};

TinyMCE.prototype.setContent = function(html_content) {
	if (tinyMCE.selectedInstance)
		tinyMCE.selectedInstance.execCommand('mceSetContent', false, html_content);
};

TinyMCE.prototype.importThemeLanguagePack = function(name) {
	if (typeof(name) == "undefined")
		name = tinyMCE.settings['theme'];

	tinyMCE.loadScript(tinyMCE.baseURL + '/themes/' + name + '/langs/' + tinyMCE.settings['language'] + '.js');
};

TinyMCE.prototype.importPluginLanguagePack = function(name, valid_languages) {
	var lang = "en";

	valid_languages = valid_languages.split(',');
	for (var i=0; i<valid_languages.length; i++) {
		if (tinyMCE.settings['language'] == valid_languages[i])
			lang = tinyMCE.settings['language'];
	}

	tinyMCE.loadScript(tinyMCE.baseURL + '/plugins/' + name + '/langs/' + lang +  '.js');
};

/**
 * Adds themeurl, settings and lang to HTML code.
 */
TinyMCE.prototype.applyTemplate = function(html, args) {
	html = tinyMCE.replaceVar(html, "themeurl", tinyMCE.themeURL);

	if (typeof(args) != "undefined")
		html = tinyMCE.replaceVars(html, args);

	html = tinyMCE.replaceVars(html, tinyMCE.settings);
	html = tinyMCE.replaceVars(html, tinyMCELang);

	return html;
};

TinyMCE.prototype.openWindow = function(template, args) {
	var html, width, height, x, y, resizable, scrollbars, url;

	args['mce_template_file'] = template['file'];
	tinyMCE.windowArgs = args;

	html = template['html'];
	if (!(width = template['width']))
		width = 320;

	if (!(height = template['height']))
		height = 200;

	// Add to height in M$ due to SP2 WHY DON'T YOU GUYS IMPLEMENT innerWidth of windows!!
	if (tinyMCE.isMSIE)
		height += 30;

	x = parseInt(screen.width / 2.0) - (width / 2.0);
	y = parseInt(screen.height / 2.0) - (height / 2.0);

	resizable = (args && args['resizable']) ? args['resizable'] : "no";
	scrollbars = (args && args['scrollbars']) ? args['scrollbars'] : "no";

	if (template['file'].charAt(0) != '/' && template['file'].indexOf('://') == -1)
		url = tinyMCE.baseURL + "/themes/" + tinyMCE.getParam("theme") + "/" + template['file'];
	else
		url = template['file'];

	// Replace all args as variables in URL
	for (var name in args)
		url = tinyMCE.replaceVar(url, name, escape(args[name]));

	if (html) {
		html = tinyMCE.replaceVar(html, "css", this.settings['popups_css']);
		html = tinyMCE.applyTemplate(html, args);

		var win = window.open("", "mcePopup", "top=" + y + ",left=" + x + ",scrollbars=" + scrollbars + ",dialog=yes,minimizable=" + resizable + ",modal=yes,width=" + width + ",height=" + height + ",resizable=" + resizable);
		if (win == null) {
			alert(tinyMCELang['lang_popup_blocked']);
			return;
		}

		win.document.write(html);
		win.document.close();
		win.resizeTo(width, height);
		win.focus();
	} else {
		if (tinyMCE.isMSIE && resizable != 'yes' && tinyMCE.settings["dialog_type"] == "modal") {
            var features = "resizable:" + resizable 
                + ";scroll:"
                + scrollbars + ";status:yes;center:yes;help:no;dialogWidth:"
                + width + "px;dialogHeight:" + height + "px;";

			window.showModalDialog(url, window, features);
		} else {
			if (tinyMCE.settings["dialog_type"] == "window" || tinyMCE.settings["dialog_type"] == "modal") {
				var modal = (resizable == "yes") ? "no" : "yes";

				if (tinyMCE.isGecko && tinyMCE.isMac)
					modal = "no";

				var win = window.open(url, "mcePopup", "top=" + y + ",left=" + x + ",scrollbars=" + scrollbars + ",dialog=" + modal + ",minimizable=" + resizable + ",modal=" + modal + ",width=" + width + ",height=" + height + ",resizable=" + resizable);
				if (win == null) {
					alert(tinyMCELang['lang_popup_blocked']);
					return;
				}

				eval('try { win.resizeTo(width, height); } catch(e) { }');
				win.focus();
			} else {
				var div = document.createElement("div");
				var id = "mceDialog" + (tinyMCE.dialogCounter++);

				height += 30;

				div.id = id;
				div.className = "mceDialog";
				div.style.width = width + "px";
				div.style.height = height + "px";

				var html = '<div class="mceDialogHeader"><div class="mceDialogTitle"></div><div class="mceDialogClose"><a href="javascript:tinyMCE.closeDialog();"></a></div></div>';
				html += '<div id="' + id + 'IFrameWrapper" class="mceDialogIFrameWrapper"><iframe border="0" marginwidth="0" marginheight="0" frameborder="0" hspace="0" vspace="0" src="' + url + '" width="' + width + '" height="' + height + '"></iframe></div>';

				div.innerHTML = html;

				document.body.appendChild(div);

				tinyMCE._currentDialog = id;
			}
		}
	}
};

TinyMCE.prototype.closeDialog = function() {
	// Remove div or close window
	if (tinyMCE.settings["dialog_type"] == "div") {
		var div = document.getElementById(tinyMCE._currentDialog);
		if (div)
			div.parentNode.removeChild(div);
	} else
		window.close();
};

TinyMCE.prototype.getVisualAidClass = function(class_name, state) {
	var aidClass = tinyMCE.settings['visual_table_class'];

	if (typeof(state) == "undefined")
		state = tinyMCE.settings['visual'];

	// Split
	var classNames = new Array();
	var ar = class_name.split(' ');
	for (var i=0; i<ar.length; i++) {
		if (ar[i] == aidClass)
			ar[i] = "";

		if (ar[i] != "")
			classNames[classNames.length] = ar[i];
	}

	if (state)
		classNames[classNames.length] = aidClass;

	// Glue
	var className = "";
	for (var i=0; i<classNames.length; i++) {
		if (i > 0)
			className += " ";

		className += classNames[i];
	}

	return className;
};

TinyMCE.prototype.handleVisualAid = function(element, deep, state) {
	if (!element)
		return;

	var tableElement = null;

	switch (element.nodeName.toLowerCase()) {
		case "table":
			var oldW = element.style.width;
			var oldH = element.style.height;

			element.className = tinyMCE.getVisualAidClass(element.className, state && element.getAttribute("border") == 0);

			element.style.width = oldW;
			element.style.height = oldH;

			for (var y=0; y<element.rows.length; y++) {
				for (var x=0; x<element.rows[y].cells.length; x++) {
					var className = tinyMCE.getVisualAidClass(element.rows[y].cells[x].className, state && element.getAttribute("border") == 0);
					element.rows[y].cells[x].className = className;
				}
			}

			break;

/*		case "a":
			var name = element.getAttribute("name");
			if (name && name != "" && state) {
				//element.innerHTML += '<img mceVisualAid="true" src="' + (tinyMCE.themeURL + "/images/anchor.gif") + '" />';
				return;
			}

			break;*/
	}

	if (deep && element.hasChildNodes()) {
		for (var i=0; i<element.childNodes.length; i++)
			tinyMCE.handleVisualAid(element.childNodes[i], deep, state);
	}
};

TinyMCE.prototype.getAttrib = function(elm, name, default_value) {
	var v = elm.getAttribute(name);

	// Try className for class attrib
	if (name == "class" && !v)
		v = elm.className;

	if (typeof(default_value) == "undefined")
		default_value = "";

	return (v && v != "") ? v : default_value;
};

TinyMCE.prototype.setAttrib = function(element, name, value, fix_value) {
	if (typeof(value) == "number")
		value = "" + value;

	if (fix_value) {
		if (value == null)
			value = "";

		var re = new RegExp('[^0-9%]', 'g');
		value = value.replace(re, '');
	}

	if (name == "class")
		element.className = value;

	if (value != null && value != "" && value != -1)
		element.setAttribute(name, value);
	else
		element.removeAttribute(name);
};

TinyMCE.prototype._setHTML = function(doc, html_content) {
	// Weird MSIE bug, <p><hr /></p> breaks runtime?
	if (tinyMCE.isMSIE) {
		var re = new RegExp('<p><hr /></p>', 'g');
		html_content = html_content.replace(re, "<hr>");
	}

	// Try innerHTML if it fails use pasteHTML in MSIE
	try {
		doc.body.innerHTML = html_content;
	} catch (e) {
		if (this.isMSIE)
			doc.body.createTextRange().pasteHTML(html_content);
	}

	// Content duplication bug fix
	if (tinyMCE.isMSIE && tinyMCE.settings['fix_content_duplication']) {
		// Remove P elements in P elements
		var paras = doc.getElementsByTagName("P");
		for (var i=0; i<paras.length; i++) {
			var node = paras[i];
			while ((node = node.parentNode) != null) {
				if (node.nodeName.toLowerCase() == "p")
					node.outerHTML = node.innerHTML;
			}
		}

		// Content duplication bug fix (Seems to be word crap)
		var html = doc.body.innerHTML;

		if (html.indexOf('="mso') != -1) {
			for (var i=0; i<doc.body.all.length; i++) {
				var el = doc.body.all[i];
				el.removeAttribute("className","",0);
				el.removeAttribute("style","",0);
			}

			html = doc.body.innerHTML;
			html = tinyMCE.regexpReplace(html, "<o:p><\/o:p>", "<br />");
			html = tinyMCE.regexpReplace(html, "<o:p>&nbsp;<\/o:p>", "");
			html = tinyMCE.regexpReplace(html, "<st1:.*?>", "");
			html = tinyMCE.regexpReplace(html, "<p><\/p>", "");
			html = tinyMCE.regexpReplace(html, "<p><\/p>\r\n<p><\/p>", "");
			html = tinyMCE.regexpReplace(html, "<p>&nbsp;<\/p>", "<br />");
			html = tinyMCE.regexpReplace(html, "<p>\s*(<p>\s*)?", "<p>");
			html = tinyMCE.regexpReplace(html, "<\/p>\s*(<\/p>\s*)?", "</p>");
		}

		// Always set the htmlText output
		doc.body.innerHTML = html;
	}
};

TinyMCE.prototype.getImageSrc = function(str) {
	var pos = -1;

	if (!str)
		return "";

	if ((pos = str.indexOf('this.src=')) != -1) {
		var src = str.substring(pos + 10);

		src = src.substring(0, src.indexOf('\''));

		return src;
	}

	return "";
};

TinyMCE.prototype._getElementById = function(element_id) {
	var elm = document.getElementById(element_id);
	if (!elm) {
		// Check for element in forms
		for (var j=0; j<document.forms.length; j++) {
			for (var k=0; k<document.forms[j].elements.length; k++) {
				if (document.forms[j].elements[k].name == element_id) {
					elm = document.forms[j].elements[k];
					break;
				}
			}
		}
	}

	return elm;
};

TinyMCE.prototype.getEditorId = function(form_element) {
	var inst = this.getInstanceById(form_element);
	if (!inst)
		return null;

	return inst.editorId;
};

TinyMCE.prototype.getInstanceById = function(editor_id) {
	var inst = this.instances[editor_id];
	if (!inst) {
		for (var instanceName in tinyMCE.instances) {
			var instance = tinyMCE.instances[instanceName];
			if (instance.formTargetElementId == editor_id) {
				inst = instance;
				break;
			}
		}
	}

	return inst;
};

TinyMCE.prototype.queryInstanceCommandValue = function(editor_id, command) {
	var inst = tinyMCE.getInstanceById(editor_id);
	if (inst)
		return inst.queryCommandValue(command);

	return false;
};

TinyMCE.prototype.queryInstanceCommandState = function(editor_id, command) {
	var inst = tinyMCE.getInstanceById(editor_id);
	if (inst)
		return inst.queryCommandState(command);

	return null;
};

TinyMCE.prototype.setWindowArg = function(name, value) {
	this.windowArgs[name] = value;
};

TinyMCE.prototype.getWindowArg = function(name, default_value) {
	return (typeof(this.windowArgs[name]) == "undefined") ? default_value : this.windowArgs[name];
};

TinyMCE.prototype.getCSSClasses = function(editor_id, doc) {
	var output = new Array();

	// Is cached, use that
	if (typeof(tinyMCE.cssClasses) != "undefined")
		return tinyMCE.cssClasses;

	if (typeof(editor_id) == "undefined" && typeof(doc) == "undefined") {
		var instance;

		for (var instanceName in tinyMCE.instances) {
			instance = tinyMCE.instances[instanceName];
			break;
		}

		doc = instance.getDoc();
	}

	if (typeof(doc) == "undefined") {
		var instance = tinyMCE.getInstanceById(editor_id);
		doc = instance.getDoc();
	}

	if (doc) {
		var styles = tinyMCE.isMSIE ? doc.styleSheets : doc.styleSheets;

		if (styles && styles.length > 0) {
			var csses = null;

			// Just ignore any errors
			eval("try {var csses = tinyMCE.isMSIE ? doc.styleSheets(0).rules : doc.styleSheets[0].cssRules;} catch(e) {}");
			if (!csses)
				return new Array();

			for (var i=0; i<csses.length; i++) {
				var selectorText = csses[i].selectorText;

				// Can be multiple rules per selector
				if (selectorText) {
					var rules = selectorText.split(',');
					for (var c=0; c<rules.length; c++) {
						// Invalid rule
						if (rules[c].indexOf(' ') != -1 || rules[c].indexOf(':') != -1 || rules[c].indexOf('mce_') == 1)
							continue;

						if (rules[c] == "." + tinyMCE.settings['visual_table_class'])
							continue;

						// Is class rule
						if (rules[c].indexOf('.') != -1) {
							//alert(rules[c].substring(rules[c].indexOf('.')));
							output[output.length] = rules[c].substring(rules[c].indexOf('.')+1);
						}
					}
				}
			}
		}
	}

	// Cache em
	if (output.length > 0)
		tinyMCE.cssClasses = output;

	return output;
};

TinyMCE.prototype.regexpReplace = function(in_str, reg_exp, replace_str, opts) {
	if (typeof(opts) == "undefined")
		opts = 'g';

	var re = new RegExp(reg_exp, opts);
	return in_str.replace(re, replace_str);
};

TinyMCE.prototype.cleanupEventStr = function(str) {
	str = "" + str;
	str = str.replace('function anonymous()\n{\n', '');
	str = str.replace('\n}', '');

	return str;
};

TinyMCE.prototype.getAbsPosition = function(node) {
	var pos = new Object();

	pos.absLeft = pos.absTop = 0;

	var parentNode = node;
	while (parentNode) {
		pos.absLeft += parentNode.offsetLeft;
		pos.absTop += parentNode.offsetTop;

		parentNode = parentNode.offsetParent;
	}

	return pos;
};

TinyMCE.prototype.openFileBrowser = function(field_name, url, type, win) {
	var cb = tinyMCE.getParam("file_browser_callback");

	this.setWindowArg("window", win);

	// Call to external callback
	if(eval('typeof('+cb+')') == "undefined")
		alert("Callback function: " + cb + " could not be found.");
	else
		eval(cb + "(field_name, url, type, win);");
};

TinyMCE.prototype.getControlHTML = function(control_name) {
	var themePlugins = tinyMCE.getParam('plugins', '', true, ',');
	var templateFunction;

	// Is it defined in any plugins
	for (var i=themePlugins.length; i>=0; i--) {
		templateFunction = 'TinyMCE_' + themePlugins[i] + "_getControlHTML";
		if (eval("typeof(" + templateFunction + ")") != 'undefined') {
			var html = eval(templateFunction + "('" + control_name + "');");
			if (html != "")
				return tinyMCE.replaceVar(html, "pluginurl", tinyMCE.baseURL + "/plugins/" + themePlugins[i]);
		}
	}

	return eval('TinyMCE_' + tinyMCE.settings['theme'] + "_getControlHTML" + "('" + control_name + "');");
};

TinyMCE.prototype._themeExecCommand = function(editor_id, element, command, user_interface, value) {
	var themePlugins = tinyMCE.getParam('plugins', '', true, ',');
	var templateFunction;

	// Is it defined in any plugins
	for (var i=themePlugins.length; i>=0; i--) {
		templateFunction = 'TinyMCE_' + themePlugins[i] + "_execCommand";
		if (eval("typeof(" + templateFunction + ")") != 'undefined') {
			if (eval(templateFunction + "(editor_id, element, command, user_interface, value);"))
				return true;
		}
	}

	// Theme funtion
	templateFunction = 'TinyMCE_' + tinyMCE.settings['theme'] + "_execCommand";
	if (eval("typeof(" + templateFunction + ")") != 'undefined')
		return eval(templateFunction + "(editor_id, element, command, user_interface, value);");

	// Pass to normal
	return false;
};

TinyMCE.prototype._getThemeFunction = function(suffix, skip_plugins) {
	if (skip_plugins)
		return 'TinyMCE_' + tinyMCE.settings['theme'] + suffix;

	var themePlugins = tinyMCE.getParam('plugins', '', true, ',');
	var templateFunction;

	// Is it defined in any plugins
	for (var i=themePlugins.length; i>=0; i--) {
		templateFunction = 'TinyMCE_' + themePlugins[i] + suffix;
		if (eval("typeof(" + templateFunction + ")") != 'undefined')
			return templateFunction;
	}

	return 'TinyMCE_' + tinyMCE.settings['theme'] + suffix;
};


TinyMCE.prototype.isFunc = function(func_name) {
	if (func_name == null || func_name == "")
		return false;

	return eval("typeof(" + func_name + ")") != "undefined";
};

TinyMCE.prototype.exec = function(func_name, args) {
	var str = func_name + '(';

	// Add all arguments
	for (var i=3; i<args.length; i++) {
		str += 'args[' + i + ']';

		if (i < args.length-1)
			str += ',';
	}

	str += ');';

	return eval(str);
};

TinyMCE.prototype.executeCallback = function(param, suffix, mode) {
	switch (mode) {
		// No chain
		case 0:
			var state = false;

			// Execute each plugin callback
			var plugins = tinyMCE.getParam('plugins', '', true, ',');
			for (var i=0; i<plugins.length; i++) {
				var func = "TinyMCE_" + plugins[i] + suffix;
				if (tinyMCE.isFunc(func)) {
					tinyMCE.exec(func, this.executeCallback.arguments);
					state = true;
				}
			}

			// Execute theme callback
			var func = 'TinyMCE_' + tinyMCE.settings['theme'] + suffix;
			if (tinyMCE.isFunc(func)) {
				tinyMCE.exec(func, this.executeCallback.arguments);
				state = true;
			}

			// Execute settings callback
			var func = tinyMCE.getParam(param, '');
			if (tinyMCE.isFunc(func)) {
				tinyMCE.exec(func, this.executeCallback.arguments);
				state = true;
			}

			return state;

		// Chain mode
		case 1:
			// Execute each plugin callback
			var plugins = tinyMCE.getParam('plugins', '', true, ',');
			for (var i=0; i<plugins.length; i++) {
				var func = "TinyMCE_" + plugins[i] + suffix;
				if (tinyMCE.isFunc(func)) {
					if (tinyMCE.exec(func, this.executeCallback.arguments))
						return true;
				}
			}

			// Execute theme callback
			var func = 'TinyMCE_' + tinyMCE.settings['theme'] + suffix;
			if (tinyMCE.isFunc(func)) {
				if (tinyMCE.exec(func, this.executeCallback.arguments))
					return true;
			}

			// Execute settings callback
			var func = tinyMCE.getParam(param, '');
			if (tinyMCE.isFunc(func)) {
				if (tinyMCE.exec(func, this.executeCallback.arguments))
					return true;
			}

			return false;
	}
};

// TinyMCEControl
function TinyMCEControl(settings) {
	// Undo levels
	this.undoLevels = new Array();
	this.undoIndex = 0;
	this.isDirty = false;

	// Default settings
	this.settings = settings;
	this.settings['theme'] = tinyMCE.getParam("theme", "default");
	this.settings['width'] = tinyMCE.getParam("width", -1);
	this.settings['height'] = tinyMCE.getParam("height", -1);
};

TinyMCEControl.prototype.repaint = function() {
	if (tinyMCE.isMSIE)
		return;

	this.getBody().style.display = 'none';
	this.getBody().style.display = 'block';
};

TinyMCEControl.prototype.switchSettings = function() {
	if (tinyMCE.configs.length > 1 && tinyMCE.currentConfig != this.settings['index']) {
		tinyMCE.settings = this.settings;
		tinyMCE.currentConfig = this.settings['index'];
	}
};

TinyMCEControl.prototype.fixBrokenURLs = function() {
	var body = this.getBody();

	var elms = body.getElementsByTagName("img");
	for (var i=0; i<elms.length; i++) {
		var src = elms[i].getAttribute('mce_real_src');
		if (src && src != "")
			elms[i].setAttribute("src", src);
	}

	var elms = body.getElementsByTagName("a");
	for (var i=0; i<elms.length; i++) {
		var href = elms[i].getAttribute('mce_real_href');
		if (href && href != "")
			elms[i].setAttribute("href", href);
	}
};

TinyMCEControl.prototype.convertAllRelativeURLs = function() {
	var body = this.getBody();

	// Convert all image URL:s to absolute URL
	var elms = body.getElementsByTagName("img");
	for (var i=0; i<elms.length; i++) {
		var src = elms[i].getAttribute('src');
		if (src && src != "") {
			src = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], src);
			elms[i].setAttribute("src", src);
			elms[i].setAttribute("mce_real_src", src);
		}
	}

	// Convert all link URL:s to absolute URL
	var elms = body.getElementsByTagName("a");
	for (var i=0; i<elms.length; i++) {
		var href = elms[i].getAttribute('href');
		if (href && href != "") {
			href = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], href);
			elms[i].setAttribute("href", href);
			elms[i].setAttribute("mce_real_href", href);
		}
	}
};

TinyMCEControl.prototype.getSelectedHTML = function() {
	if (tinyMCE.isSafari) {
		// Not realy perfect!!

		return this.getRng().toString();
	}

	var elm = document.createElement("body");

	if (tinyMCE.isGecko)
		elm.appendChild(this.getRng().cloneContents());
	else
		elm.innerHTML = this.getRng().htmlText;

	return tinyMCE._cleanupHTML(this.contentDocument, this.settings, elm, this.visualAid);
};

TinyMCEControl.prototype.getBookmark = function() {
	var rng = this.getRng();

	if (tinyMCE.isSafari)
		return rng;

	if (tinyMCE.isMSIE)
		return rng.getBookmark();

	if (tinyMCE.isGecko)
		return rng.cloneRange();

	return null;
};

TinyMCEControl.prototype.moveToBookmark = function(bookmark) {
	if (tinyMCE.isSafari) {
		var sel = this.getSel().realSelection;

		sel.setBaseAndExtent(bookmark.startContainer, bookmark.startOffset, bookmark.endContainer, bookmark.endOffset);

		return true;
	}

	if (tinyMCE.isMSIE)
		return this.getRng().moveToBookmark(bookmark);

	if (tinyMCE.isGecko) {
		var rng = this.getDoc().createRange();
		var sel = this.getSel();

		rng.setStart(bookmark.startContainer, bookmark.startOffset);
		rng.setEnd(bookmark.endContainer, bookmark.endOffset);

		sel.removeAllRanges();
		sel.addRange(rng);

		return true;
	}

	return false;
};

TinyMCEControl.prototype.getSelectedText = function() {
	if (tinyMCE.isMSIE) {
		var doc = this.getDoc();

		if (doc.selection.type == "Text") {
			var rng = doc.selection.createRange();
			selectedText = rng.text;
		} else
			selectedText = '';
	} else {
		var sel = this.getSel();

		if (sel && sel.toString)
			selectedText = sel.toString();
		else
			selectedText = '';
	}

	return selectedText;
};

TinyMCEControl.prototype.selectNode = function(node, collapse, select_text_node, to_start) {
	if (!node)
		return;

	if (typeof(collapse) == "undefined")
		collapse = true;

	if (typeof(select_text_node) == "undefined")
		select_text_node = false;

	if (typeof(to_start) == "undefined")
		to_start = true;

	if (tinyMCE.isMSIE) {
		var rng = this.getBody().createTextRange();

		try {
			rng.moveToElementText(node);

			if (collapse)
				rng.collapse(to_start);

			rng.select();
		} catch (e) {
			// Throws illigal agrument in MSIE some times
		}
	} else {
		var sel = this.getSel();

		if (!sel)
			return;

		if (tinyMCE.isSafari) {
			sel.realSelection.setBaseAndExtent(node, 0, node, node.innerText.length);

			if (collapse) {
				if (to_start)
					sel.realSelection.collapseToStart();
				else
					sel.realSelection.collapseToEnd();
			}

			this.scrollToNode(node);

			return;
		}

		var rng = this.getDoc().createRange();

		if (select_text_node) {
			// Find first textnode in tree
			var nodes = tinyMCE.getNodeTree(node, new Array(), 3);
			if (nodes.length > 0)
				rng.selectNodeContents(nodes[0]);
			else
				rng.selectNodeContents(node);
		} else
			rng.selectNode(node);

		if (collapse) {
			// Special treatment of textnode collapse
			if (!to_start && node.nodeType == 3) {
				rng.setStart(node, node.nodeValue.length);
				rng.setEnd(node, node.nodeValue.length);
			} else
				rng.collapse(to_start);
		}

		sel.removeAllRanges();
		sel.addRange(rng);
	}

	this.scrollToNode(node);

	// Set selected element
	tinyMCE.selectedElement = null;
	if (node.nodeType == 1)
		tinyMCE.selectedElement = node;
};

TinyMCEControl.prototype.scrollToNode = function(node) {
	// Scroll to node position
	var pos = tinyMCE.getAbsPosition(node);
	var doc = this.getDoc();
	var scrollX = doc.body.scrollLeft + doc.documentElement.scrollLeft;
	var scrollY = doc.body.scrollTop + doc.documentElement.scrollTop;
	var height = tinyMCE.isMSIE ? document.getElementById(this.editorId).style.pixelHeight : this.targetElement.clientHeight;

	// Only scroll if out of visible area
	if (!tinyMCE.settings['auto_resize'] && !(node.absTop > scrollY && node.absTop < (scrollY - 25 + height)))
		this.contentWindow.scrollTo(pos.absLeft, pos.absTop - height + 25);
};

TinyMCEControl.prototype.getBody = function() {
	return this.getDoc().body;
};

TinyMCEControl.prototype.getDoc = function() {
	return this.contentWindow.document;
};

TinyMCEControl.prototype.getWin = function() {
	return this.contentWindow;
};

TinyMCEControl.prototype.getSel = function() {
	if (tinyMCE.isMSIE)
		return this.getDoc().selection;

	var sel = this.contentWindow.getSelection();

	// Fake getRangeAt
	if (tinyMCE.isSafari && !sel.getRangeAt) {
		var newSel = new Object();
		var doc = this.getDoc();

		function getRangeAt(idx) {
			var rng = new Object();

			rng.startContainer = this.focusNode;
			rng.endContainer = this.anchorNode;
			rng.commonAncestorContainer = this.focusNode;
			rng.createContextualFragment = function (html) {
				// Seems to be a tag
				if (html.charAt(0) == '<') {
					var elm = doc.createElement("div");

					elm.innerHTML = html;

					return elm.firstChild;
				}

				return doc.createTextNode("UNSUPPORTED, DUE TO LIMITATIONS IN SAFARI!");
			};

			rng.deleteContents = function () {
				doc.execCommand("Delete", false, "");
			};

			return rng;
		}

		// Patch selection

		newSel.focusNode = sel.baseNode;
		newSel.focusOffset = sel.baseOffset;
		newSel.anchorNode = sel.extentNode;
		newSel.anchorOffset = sel.extentOffset;
		newSel.getRangeAt = getRangeAt;
		newSel.text = "" + sel;
		newSel.realSelection = sel;

		newSel.toString = function () {return this.text;};

		return newSel;
	}

	return sel;
};

TinyMCEControl.prototype.getRng = function() {
	var sel = this.getSel();
	if (sel == null)
		return null;

	if (tinyMCE.isMSIE)
		return sel.createRange();

	if (tinyMCE.isSafari) {
		var rng = this.getDoc().createRange();
		var sel = this.getSel().realSelection;

		rng.setStart(sel.baseNode, sel.baseOffset);
		rng.setEnd(sel.extentNode, sel.extentOffset);

		return rng;
	}

	return this.getSel().getRangeAt(0);
};

TinyMCEControl.prototype._insertPara = function(e) {
	function isEmpty(para) {
		function isEmptyHTML(html) {
			return html.replace(new RegExp('[ \t\r\n]+', 'g'), '').toLowerCase() == "";
		}

		// Check for images
		if (para.getElementsByTagName("img").length > 0)
			return false;

		// Check for tables
		if (para.getElementsByTagName("table").length > 0)
			return false;

		// Check for HRs
		if (para.getElementsByTagName("hr").length > 0)
			return false;

		// Check all textnodes
		var nodes = tinyMCE.getNodeTree(para, new Array(), 3);
		for (var i=0; i<nodes.length; i++) {
			if (!isEmptyHTML(nodes[i].nodeValue))
				return false;
		}

		// No images, no tables, no hrs, no text content then it's empty
		return true;
	}

	var doc = this.getDoc();
	var sel = this.getSel();
	var win = this.contentWindow;
	var rng = sel.getRangeAt(0);
	var body = doc.body;
	var rootElm = doc.documentElement;
	var self = this;
	var blockName = "P";

//	debug(e.target, sel.anchorNode.nodeName, sel.focusNode.nodeName, rng.startContainer, rng.endContainer, rng.commonAncestorContainer, sel.anchorOffset, sel.focusOffset, rng.toString());

	// Setup before range
	var rngBefore = doc.createRange();
	rngBefore.setStart(sel.anchorNode, sel.anchorOffset);
	rngBefore.collapse(true);

	// Setup after range
	var rngAfter = doc.createRange();
	rngAfter.setStart(sel.focusNode, sel.focusOffset);
	rngAfter.collapse(true);

	// Setup start/end points
	var direct = rngBefore.compareBoundaryPoints(rngBefore.START_TO_END, rngAfter) < 0;
	var startNode = direct ? sel.anchorNode : sel.focusNode;
	var startOffset = direct ? sel.anchorOffset : sel.focusOffset;
	var endNode = direct ? sel.focusNode : sel.anchorNode;
	var endOffset = direct ? sel.focusOffset : sel.anchorOffset;

	// Get block elements
	var startBlock = tinyMCE.getParentBlockElement(startNode);
	var endBlock = tinyMCE.getParentBlockElement(endNode);

	// Use current block name
	if (startBlock != null) {
		blockName = startBlock.nodeName.toUpperCase();

		// Use P instead
		if (blockName == "TD" || blockName == "TABLE")
			blockName = "P";
	}

	// Within a list item (use normal behavior)
	if ((startBlock != null && startBlock.nodeName.toLowerCase() == "li") || (endBlock != null && endBlock.nodeName.toLowerCase() == "li"))
		return false;

	// Within a table create new paragraphs
	if ((startBlock != null && startBlock.nodeName.toLowerCase() == "table") || (endBlock != null && endBlock.nodeName.toLowerCase() == "table"))
		startBlock = endBlock = null;

	// Setup new paragraphs
	var paraBefore = (startBlock != null && startBlock.nodeName.toUpperCase() == blockName) ? startBlock.cloneNode(false) : doc.createElement(blockName);
	var paraAfter = (endBlock != null && endBlock.nodeName.toUpperCase() == blockName) ? endBlock.cloneNode(false) : doc.createElement(blockName);

	// Setup chop nodes
	var startChop = startNode;
	var endChop = endNode;

	// Get startChop node
	node = startChop;
	do {
		if (node == body || node.nodeType == 9 || tinyMCE.isBlockElement(node))
			break;

		startChop = node;
	} while ((node = node.previousSibling ? node.previousSibling : node.parentNode));

	// Get endChop node
	node = endChop;
	do {
		if (node == body || node.nodeType == 9 || tinyMCE.isBlockElement(node))
			break;

		endChop = node;
	} while ((node = node.nextSibling ? node.nextSibling : node.parentNode));

	// If not in a block element
	if (startBlock == null) {
		// Delete selection
		rng.deleteContents();
		sel.removeAllRanges();

		if (startChop != rootElm && endChop != rootElm) {
			// Insert paragraph before
			rngBefore = rng.cloneRange();

			if (startChop == body)
				rngBefore.setStart(startChop, 0);
			else
				rngBefore.setStartBefore(startChop);

			paraBefore.appendChild(rngBefore.cloneContents());

			// Insert paragraph after
			if (endChop.parentNode.nodeName == blockName)
				endChop = endChop.parentNode;

			rng.setEndAfter(endChop);

			var contents = rng.cloneContents();
			if (contents.firstChild && (contents.firstChild.nodeName == blockName || contents.firstChild.nodeName.toLowerCase() == "body")) {
				var nodes = contents.firstChild.childNodes;
				for (var i=0; i<nodes.length; i++) {
					if (nodes[i].nodeName.toLowerCase() != "body")
						paraAfter.appendChild(nodes[i]);
				}
			} else
				paraAfter.appendChild(contents);

			// Check if it's a empty paragraph
			if (isEmpty(paraBefore))
				paraBefore.innerHTML = "&nbsp;";

			// Check if it's a empty paragraph
			if (isEmpty(paraAfter))
				paraAfter.innerHTML = "&nbsp;";

			// Delete old contents
			rng.deleteContents();
			rngAfter.deleteContents();
			rngBefore.deleteContents();

			// Insert new paragraphs
			paraAfter.normalize();
			rngBefore.insertNode(paraAfter);
			paraBefore.normalize();
			rngBefore.insertNode(paraBefore);

			// debug("1: ", paraBefore.innerHTML, paraAfter.innerHTML);
		} else {
			body.innerHTML = "<" + blockName + ">&nbsp;</" + blockName + "><" + blockName + ">&nbsp;</" + blockName + ">";
			paraAfter = body.childNodes[1];
		}

		this.selectNode(paraAfter, true, true);

		return true;
	}

	// Place first part within new paragraph
	if (startChop.nodeName == blockName)
		rngBefore.setStart(startChop, 0);
	else
		rngBefore.setStartBefore(startChop);
	rngBefore.setEnd(startNode, startOffset);
	paraBefore.appendChild(rngBefore.cloneContents());

	// Place secound part within new paragraph
	rngAfter.setEndAfter(endChop);
	rngAfter.setStart(endNode, endOffset);
	var contents = rngAfter.cloneContents();
	if (contents.firstChild && contents.firstChild.nodeName == blockName) {
		var nodes = contents.firstChild.childNodes;
		for (var i=0; i<nodes.length; i++) {
			if (nodes[i].nodeName.toLowerCase() != "body")
				paraAfter.appendChild(nodes[i]);
		}
	} else
		paraAfter.appendChild(contents);

	// Check if it's a empty paragraph
	if (isEmpty(paraBefore))
		paraBefore.innerHTML = "&nbsp;";

	// Check if it's a empty paragraph
	if (isEmpty(paraAfter))
		paraAfter.innerHTML = "&nbsp;";

	// Create a range around everything
	var rng = doc.createRange();

	if (!startChop.previousSibling && startChop.parentNode.nodeName.toUpperCase() == blockName) {
		rng.setStartBefore(startChop.parentNode);
	} else {
		if (rngBefore.startContainer.nodeName.toUpperCase() == blockName && rngBefore.startOffset == 0)
			rng.setStartBefore(rngBefore.startContainer);
		else
			rng.setStart(rngBefore.startContainer, rngBefore.startOffset);
	}

	if (!endChop.nextSibling && endChop.parentNode.nodeName.toUpperCase() == blockName)
		rng.setEndAfter(endChop.parentNode);
	else
		rng.setEnd(rngAfter.endContainer, rngAfter.endOffset);

	// Delete all contents and insert new paragraphs
	rng.deleteContents();
	rng.insertNode(paraAfter);
	rng.insertNode(paraBefore);
	// debug("2", paraBefore.innerHTML, paraAfter.innerHTML);

	// Normalize
	paraAfter.normalize();
	paraBefore.normalize();

	this.selectNode(paraAfter, true, true);

	return true;
};

TinyMCEControl.prototype._handleBackSpace = function(evt_type) {
	var doc = this.getDoc();
	var sel = this.getSel();
	if (sel == null)
		return false;

	var rng = sel.getRangeAt(0);
	var node = rng.startContainer;
	var elm = node.nodeType == 3 ? node.parentNode : node;

	if (node == null)
		return;

	// Empty node, wrap contents in paragraph
	if (elm && elm.nodeName == "") {
		var para = doc.createElement("p");

		while (elm.firstChild)
			para.appendChild(elm.firstChild);

		elm.parentNode.insertBefore(para, elm);
		elm.parentNode.removeChild(elm);

		var rng = rng.cloneRange();
		rng.setStartBefore(node.nextSibling);
		rng.setEndAfter(node.nextSibling);
		rng.extractContents();

		this.selectNode(node.nextSibling, true, true);
	}

	// Remove empty paragraphs
	var para = tinyMCE.getParentBlockElement(node);
	if (para != null && para.nodeName.toLowerCase() == 'p' && evt_type == "keypress") {
		var htm = para.innerHTML;
		var block = tinyMCE.getParentBlockElement(node);

		// Empty node, we do the killing!!
		if (htm == "" || htm == "&nbsp;" || block.nodeName.toLowerCase() == "li") {
			var prevElm = para.previousSibling;

			while (prevElm != null && prevElm.nodeType != 1)
				prevElm = prevElm.previousSibling;

			if (prevElm == null)
				return false;

			// Get previous elements last text node
			var nodes = tinyMCE.getNodeTree(prevElm, new Array(), 3);
			var lastTextNode = nodes.length == 0 ? null : nodes[nodes.length-1];

			// Select the last text node and move curstor to end
			if (lastTextNode != null)
				this.selectNode(lastTextNode, true, false, false);

			// Remove the empty paragrapsh
			para.parentNode.removeChild(para);

			//debug("within p element" + para.innerHTML);
			//showHTML(this.getBody().innerHTML);
			return true;
		}
	}

	// Remove BR elements
/*	while (node != null && (node = node.nextSibling) != null) {
		if (node.nodeName.toLowerCase() == 'br')
			node.parentNode.removeChild(node);
		else if (node.nodeType == 1) // Break at other element
			break;
	}*/

	//showHTML(this.getBody().innerHTML);

	return false;
};

TinyMCEControl.prototype._insertSpace = function() {
	return true;
};

TinyMCEControl.prototype.autoResetDesignMode = function() {
	// Add fix for tab/style.display none/block problems in Gecko
	if (!tinyMCE.isMSIE && tinyMCE.settings['auto_reset_designmode']) {
		var sel = this.getSel();

		// Weird, wheres that cursor selection?
		if (!sel || !sel.rangeCount || sel.rangeCount == 0)
			eval('try { this.getDoc().designMode = "On"; } catch(e) {}');
	}
};

TinyMCEControl.prototype.isDirty = function() {
	return this.isDirty;
};

TinyMCEControl.prototype.execCommand = function(command, user_interface, value) {
	var doc = this.getDoc();
	var win = this.getWin();

	if (this.lastSafariSelection) {
		this.moveToBookmark(this.lastSafariSelection);
		tinyMCE.selectedElement = this.lastSafariSelectedElement;
	}

	// Mozilla issue
	if (!tinyMCE.isMSIE && !this.useCSS) {
		doc.execCommand("useCSS", false, true);
		this.useCSS = true;
	}

	//debug("command: " + command + ", user_interface: " + user_interface + ", value: " + value);
	this.contentDocument = doc; // <-- Strange, unless this is applied Mozilla 1.3 breaks

	// Call theme execcommand
	if (tinyMCE._themeExecCommand(this.editorId, this.getBody(), command, user_interface, value))
		return;

	// Add undo level of operation
	if (command != "mceAddUndoLevel" && command != "Undo" && command != "Redo" && command != "mceImage" && command != "mceLink" && command != "mceToggleVisualAid" && (command != "mceInsertTable" && !user_interface))
		this.execCommand("mceAddUndoLevel");

	// Fix align on images
	if (this.getFocusElement() && this.getFocusElement().nodeName.toLowerCase() == "img") {
		var align = this.getFocusElement().getAttribute('align');

		switch (command) {
			case "JustifyLeft":
				if (align == 'left')
					this.getFocusElement().removeAttribute('align');
				else
					this.getFocusElement().setAttribute('align', 'left');

				tinyMCE.triggerNodeChange();
				return;

			case "JustifyCenter":
				if (align == 'middle')
					this.getFocusElement().removeAttribute('align');
				else
					this.getFocusElement().setAttribute('align', 'middle');

				tinyMCE.triggerNodeChange();
				return;

			case "JustifyRight":
				if (align == 'right')
					this.getFocusElement().removeAttribute('align');
				else
					this.getFocusElement().setAttribute('align', 'right');

				tinyMCE.triggerNodeChange();
				return;
		}
	}

	if (tinyMCE.settings['force_br_newlines']) {
		var alignValue = "";

		if (doc.selection.type != "Control") {
			switch (command) {
					case "JustifyLeft":
						alignValue = "left";
						break;

					case "JustifyCenter":
						alignValue = "center";
						break;

					case "JustifyFull":
						alignValue = "justify";
						break;

					case "JustifyRight":
						alignValue = "right";
						break;
			}

			if (alignValue != "") {
				var rng = doc.selection.createRange();

				if ((divElm = tinyMCE.getParentElement(rng.parentElement(), "div")) != null)
					divElm.setAttribute("align", alignValue);
				else if (rng.pasteHTML && rng.htmlText.length > 0)
					rng.pasteHTML('<div align="' + alignValue + '">' + rng.htmlText + "</div>");

				tinyMCE.triggerNodeChange();
				return;
			}
		}
	}

	switch (command) {
		case "mceStoreSelection":
			this.selectionBookmark = this.getBookmark();
			break;

		case "mceRestoreSelection":
			this.moveToBookmark(this.selectionBookmark);
			break;

		case "InsertUnorderedList":
		case "InsertOrderedList":
			var tag = (command == "InsertUnorderedList") ? "ul" : "ol";

			if (tinyMCE.isSafari)
				this.execCommand("mceInsertContent", false, "<" + tag + "><li>&nbsp;</li><" + tag + ">");
			else
				this.getDoc().execCommand(command, user_interface, value);

			tinyMCE.triggerNodeChange();
			break;

		case "Strikethrough":
			if (tinyMCE.isSafari)
				this.execCommand("mceInsertContent", false, "<strike>" + this.getSelectedHTML() + "</strike>");
			else
				this.getDoc().execCommand(command, user_interface, value);

			tinyMCE.triggerNodeChange();
			break;

		case "mceSelectNode":
			this.selectNode(value);
			tinyMCE.triggerNodeChange();
			tinyMCE.selectedNode = value;
			break;

		case "FormatBlock":
			if (value == null || value == "") {
				var elm = tinyMCE.getParentElement(this.getFocusElement(), "p,div,h1,h2,h3,h4,h5,h6,pre,address");

				if (elm)
					this.execCommand("mceRemoveNode", false, elm);
			} else
				this.getDoc().execCommand("FormatBlock", false, value);

			tinyMCE.triggerNodeChange();

			break;

		case "mceRemoveNode":
			if (!value)
				value = tinyMCE.getParentElement(this.getFocusElement());

			if (tinyMCE.isMSIE) {
				value.outerHTML = value.innerHTML;
			} else {
				var rng = value.ownerDocument.createRange();
				rng.setStartBefore(value);
				rng.setEndAfter(value);
				rng.deleteContents();
				rng.insertNode(rng.createContextualFragment(value.innerHTML));
			}

			tinyMCE.triggerNodeChange();

			break;

		case "mceSelectNodeDepth":
			var parentNode = this.getFocusElement();
			for (var i=0; parentNode; i++) {
				if (parentNode.nodeName.toLowerCase() == "body")
					break;

				if (parentNode.nodeName.toLowerCase() == "#text") {
					i--;
					parentNode = parentNode.parentNode;
					continue;
				}

				if (i == value) {
					this.selectNode(parentNode, false);
					tinyMCE.triggerNodeChange();
					tinyMCE.selectedNode = parentNode;
					return;
				}

				parentNode = parentNode.parentNode;
			}

			break;

		case "HiliteColor":
			if (tinyMCE.isGecko) {
				this.getDoc().execCommand("useCSS", false, false);
				this.getDoc().execCommand('hilitecolor', false, value);
				this.getDoc().execCommand("useCSS", false, true);
			} else
				this.getDoc().execCommand('BackColor', false, value);

			break;

		case "Cut":
		case "Copy":
		case "Paste":
			var cmdFailed = false;

			// Try executing command
			eval('try {this.getDoc().execCommand(command, user_interface, value);} catch (e) {cmdFailed = true;}');

			// Alert error in gecko if command failed
			if (tinyMCE.isGecko && cmdFailed) {
				// Confirm more info
				if (confirm(tinyMCE.getLang('lang_clipboard_msg')))
					window.open('http://www.mozilla.org/editor/midasdemo/securityprefs.html', 'mceExternal');

				return;
			} else
				tinyMCE.triggerNodeChange();
		break;

		case "mceSetContent":
			if (!value)
				value = "";

			// Call custom cleanup code
			value = tinyMCE._customCleanup("insert_to_editor", value);
			tinyMCE._setHTML(doc, value);
			doc.body.innerHTML = tinyMCE._cleanupHTML(doc, tinyMCE.settings, doc.body);
			tinyMCE.handleVisualAid(doc.body, true, this.visualAid);
			return true;

		case "mceLink":
			var selectedText = "";

			if (tinyMCE.isMSIE) {
				var rng = doc.selection.createRange();
				selectedText = rng.text;
			} else
				selectedText = this.getSel().toString();

			if (!tinyMCE.linkElement) {
				if ((tinyMCE.selectedElement.nodeName.toLowerCase() != "img") && (selectedText.length <= 0))
					return;
			}

			var href = "", target = "", title = "", onclick = "", action = "insert", style_class = "";

			if (tinyMCE.selectedElement.nodeName.toLowerCase() == "a")
				tinyMCE.linkElement = tinyMCE.selectedElement;

			// Is anchor not a link
			if (tinyMCE.linkElement != null && tinyMCE.getAttrib(tinyMCE.linkElement, 'href') == "")
				tinyMCE.linkElement = null;

			if (tinyMCE.linkElement) {
				href = tinyMCE.getAttrib(tinyMCE.linkElement, 'href');
				target = tinyMCE.getAttrib(tinyMCE.linkElement, 'target');
				title = tinyMCE.getAttrib(tinyMCE.linkElement, 'title');
                onclick = tinyMCE.getAttrib(tinyMCE.linkElement, 'mce_onclick');
				style_class = tinyMCE.getAttrib(tinyMCE.linkElement, 'class');

				// Try old onclick to if copy/pasted content
				if (onclick == "")
					onclick = tinyMCE.getAttrib(tinyMCE.linkElement, 'onclick');

				onclick = tinyMCE.cleanupEventStr(onclick);

				// Fix for drag-drop/copy paste bug in Mozilla
				mceRealHref = tinyMCE.getAttrib(tinyMCE.linkElement, 'mce_real_href');
				if (mceRealHref != "")
					href = mceRealHref;

				href = eval(tinyMCE.settings['urlconverter_callback'] + "(href, tinyMCE.linkElement, true);");
				action = "update";
			}

			if (this.settings['insertlink_callback']) {
				var returnVal = eval(this.settings['insertlink_callback'] + "(href, target, title, onclick, action, style_class);");
				if (returnVal && returnVal['href'])
					tinyMCE.insertLink(returnVal['href'], returnVal['target'], returnVal['title'], returnVal['onclick'], returnVal['style_class']);
			} else {
				tinyMCE.openWindow(this.insertLinkTemplate, {href : href, target : target, title : title, onclick : onclick, action : action, className : style_class});
			}
		break;

		case "mceImage":
			var src = "", alt = "", border = "", hspace = "", vspace = "", width = "", height = "", align = "";
			var title = "", onmouseover = "", onmouseout = "", action = "insert";
			var img = tinyMCE.imgElement;

			if (tinyMCE.selectedElement != null && tinyMCE.selectedElement.nodeName.toLowerCase() == "img") {
				img = tinyMCE.selectedElement;
				tinyMCE.imgElement = img;
			}

			if (img) {
				// Is it a internal MCE visual aid image, then skip this one.
				if (tinyMCE.getAttrib(img, 'name').indexOf('mce_') == 0)
					return;

				src = tinyMCE.getAttrib(img, 'src');
				alt = tinyMCE.getAttrib(img, 'alt');

				// Try polling out the title
				if (alt == "")
					alt = tinyMCE.getAttrib(img, 'title');

				// Fix width/height attributes if the styles is specified
				if (tinyMCE.isGecko) {
					var w = img.style.width;
					if (w != null && w != "")
						img.setAttribute("width", w);

					var h = img.style.height;
					if (h != null && h != "")
						img.setAttribute("height", h);
				}

				border = tinyMCE.getAttrib(img, 'border');
				hspace = tinyMCE.getAttrib(img, 'hspace');
				vspace = tinyMCE.getAttrib(img, 'vspace');
				width = tinyMCE.getAttrib(img, 'width');
				height = tinyMCE.getAttrib(img, 'height');
				align = tinyMCE.getAttrib(img, 'align');
                onmouseover = tinyMCE.getAttrib(img, 'onmouseover');
                onmouseout = tinyMCE.getAttrib(img, 'onmouseout');
                title = tinyMCE.getAttrib(img, 'title');

				// Is realy specified?
				if (tinyMCE.isMSIE) {
					width = img.attributes['width'].specified ? width : "";
					height = img.attributes['height'].specified ? height : "";
				}

				onmouseover = tinyMCE.getImageSrc(tinyMCE.cleanupEventStr(onmouseover));
				onmouseout = tinyMCE.getImageSrc(tinyMCE.cleanupEventStr(onmouseout));

				// Fix for drag-drop/copy paste bug in Mozilla
				mceRealSrc = tinyMCE.getAttrib(img, 'mce_real_src');
				if (mceRealSrc != "")
					src = mceRealSrc;

				src = eval(tinyMCE.settings['urlconverter_callback'] + "(src, img, true);");

				if (onmouseover != "")
					onmouseover = eval(tinyMCE.settings['urlconverter_callback'] + "(onmouseover, img, true);");

				if (onmouseout != "")
					onmouseout = eval(tinyMCE.settings['urlconverter_callback'] + "(onmouseout, img, true);");

				action = "update";
			}

			if (this.settings['insertimage_callback']) {
				var returnVal = eval(this.settings['insertimage_callback'] + "(src, alt, border, hspace, vspace, width, height, align, title, onmouseover, onmouseout, action);");
				if (returnVal && returnVal['src'])
					tinyMCE.insertImage(returnVal['src'], returnVal['alt'], returnVal['border'], returnVal['hspace'], returnVal['vspace'], returnVal['width'], returnVal['height'], returnVal['align'], returnVal['title'], returnVal['onmouseover'], returnVal['onmouseout']);
			} else
				tinyMCE.openWindow(this.insertImageTemplate, {src : src, alt : alt, border : border, hspace : hspace, vspace : vspace, width : width, height : height, align : align, title : title, onmouseover : onmouseover, onmouseout : onmouseout, action : action});
		break;

		case "mceCleanupWord":
			if (tinyMCE.isMSIE) {
				var html = this.getBody().createTextRange().htmlText;

				if (html.indexOf('="mso') != -1) {
					tinyMCE._setHTML(this.contentDocument, this.getBody().innerHTML);
					html = tinyMCE._cleanupHTML(this.contentDocument, this.settings, this.getBody(), this.visualAid);
				}

				this.getBody().innerHTML = html;
			}
		break;

		case "mceCleanup":
			tinyMCE._setHTML(this.contentDocument, this.getBody().innerHTML);
			this.getBody().innerHTML = tinyMCE._cleanupHTML(this.contentDocument, this.settings, this.getBody(), this.visualAid);
			tinyMCE.handleVisualAid(this.getBody(), true, this.visualAid);
			this.repaint();
			tinyMCE.triggerNodeChange();
		break;

		case "mceAnchor":
			if (!user_interface) {
				var aElm = tinyMCE.getParentElement(this.getFocusElement(), "a", "name");
				if (aElm) {
					if (value == null || value == "") {
						if (tinyMCE.isMSIE) {
							aElm.outerHTML = aElm.innerHTML;
						} else {
							var rng = aElm.ownerDocument.createRange();
							rng.setStartBefore(aElm);
							rng.setEndAfter(aElm);
							rng.deleteContents();
							rng.insertNode(rng.createContextualFragment(aElm.innerHTML));
						}
					} else
						aElm.setAttribute('name', value);
				} else {
					this.getDoc().execCommand("fontname", false, "#mce_temp_font#");
					var elementArray = tinyMCE.getElementsByAttributeValue(this.getBody(), "font", "face", "#mce_temp_font#");
					for (var x=0; x<elementArray.length; x++) {
						elm = elementArray[x];

						var aElm = this.getDoc().createElement("a");
						aElm.setAttribute('name', value);

						if (elm.hasChildNodes()) {
							for (var i=0; i<elm.childNodes.length; i++)
								aElm.appendChild(elm.childNodes[i].cloneNode(true));
						}

						elm.parentNode.replaceChild(aElm, elm);
					}
				}

				tinyMCE.triggerNodeChange();
			}
			break;

		case "mceReplaceContent":
			var selectedText = "";

			if (tinyMCE.isMSIE) {
				var rng = doc.selection.createRange();
				selectedText = rng.text;
			} else
				selectedText = this.getSel().toString();

			if (selectedText.length > 0) {
				value = tinyMCE.replaceVar(value, "selection", selectedText);
				tinyMCE.execCommand('mceInsertContent', false, value);
			}

			tinyMCE.triggerNodeChange();
		break;

		case "mceSetAttribute":
			if (typeof(value) == 'object') {
				var targetElms = (typeof(value['targets']) == "undefined") ? "p,img,span,div,td,h1,h2,h3,h4,h5,h6,pre,address" : value['targets'];
				var targetNode = tinyMCE.getParentElement(this.getFocusElement(), targetElms);

				if (targetNode) {
					targetNode.setAttribute(value['name'], value['value']);
					tinyMCE.triggerNodeChange();
				}
			}
		break;

		case "mceSetCSSClass":
			var selectedText = false;

			if (tinyMCE.isMSIE) {
				var rng = doc.selection.createRange();
				selectedText = (rng.text && rng.text.length > 0);
			} else
				selectedText = (this.getSel().toString().length > 0);

			// Use selectedNode instead if defined
			if (tinyMCE.selectedNode)
				tinyMCE.selectedElement = tinyMCE.selectedNode;

			if (selectedText && !tinyMCE.selectedNode) {
				this.getDoc().execCommand("RemoveFormat", false, null);
				if (value == null)
					return this.execCommand("RemoveFormat", false, null);

				this.getDoc().execCommand("fontname", false, "#mce_temp_font#");
				var elementArray = tinyMCE.getElementsByAttributeValue(this.getBody(), "font", "face", "#mce_temp_font#");

				// Change them all
				for (var x=0; x<elementArray.length; x++) {
					elm = elementArray[x];
					if (elm) {
						var spanElm = this.getDoc().createElement("span");
						spanElm.className = value;
						if (elm.hasChildNodes()) {
							for (var i=0; i<elm.childNodes.length; i++)
								spanElm.appendChild(elm.childNodes[i].cloneNode(true));
						}

						elm.parentNode.replaceChild(spanElm, elm);
					}
				}
			} else {
				var targetElm = this.getFocusElement();

				// Select element
				if (tinyMCE.selectedElement.nodeName.toLowerCase() == "img" || tinyMCE.selectedElement.nodeName.toLowerCase() == "table")
					targetElm = tinyMCE.selectedElement;

				var targetNode = tinyMCE.getParentElement(targetElm, "p,img,span,div,td,h1,h2,h3,h4,h5,h6,pre,address");

				// Selected element
				if (tinyMCE.selectedElement.nodeType == 1)
					targetNode = tinyMCE.selectedElement;

				// Mozilla img patch
				if (!tinyMCE.isMSIE && !targetNode)
					targetNode = tinyMCE.imgElement;

				if (targetNode) {
					if (targetNode.nodeName.toLowerCase() == "span" && (!value || value == "")) {
						if (targetNode.hasChildNodes()) {
							for (var i=0; i<targetNode.childNodes.length; i++)
								targetNode.parentNode.insertBefore(targetNode.childNodes[i].cloneNode(true), targetNode);
						}

						targetNode.parentNode.removeChild(targetNode);
					} else {
						if (value != null && value != "")
							targetNode.className = value;
						else {
							targetNode.removeAttribute("className");
							targetNode.removeAttribute("class");
						}
					}
				}
			}

			tinyMCE.triggerNodeChange();
		break;

		case "mceInsertRawHTML":
			var key = 'tiny_mce_marker';

			// Insert marker key
			this.execCommand('mceInsertContent', false, key);

			// Find marker and replace with RAW HTML
			var html = this.getBody().innerHTML;
			if ((pos = html.indexOf(key)) != -1)
				this.getBody().innerHTML = html.substring(0, pos) + value + html.substring(pos + key.length);

			break;

		case "mceInsertContent":
			if (!tinyMCE.isMSIE) {
				var sel = this.getSel();
				var rng = this.getRng();
				var isHTML = value.indexOf('<') != -1;

				if (isHTML) {
					if (tinyMCE.isSafari) {
						var tmpRng = this.getDoc().createRange();

						tmpRng.setStart(this.getBody(), 0);
						tmpRng.setEnd(this.getBody(), 0);

						value = tmpRng.createContextualFragment(value);
					} else
						value = rng.createContextualFragment(value);
				} else {
					// Setup text node
					var el = document.createElement("div");
					el.innerHTML = value;
					value = el.firstChild.nodeValue;
					value = doc.createTextNode(value);
				}

				// Insert plain text in Safari
				if (tinyMCE.isSafari && !isHTML) {
					this.execCommand('InsertText', false, value.nodeValue);
					tinyMCE.triggerNodeChange();
					return true;
				} else if (tinyMCE.isSafari && isHTML) {
					rng.deleteContents();
					rng.insertNode(value);
					tinyMCE.triggerNodeChange();
					return true;
				}

				rng.deleteContents();

				// If target node is text do special treatment, (Mozilla 1.3 fix)
				if (rng.startContainer.nodeType == 3) {
					var node = rng.startContainer.splitText(rng.startOffset);
					node.parentNode.insertBefore(value, node); 
				} else
					rng.insertNode(value);

				if (!isHTML) {
					// Removes weird selection trails
					sel.selectAllChildren(doc.body);
					sel.removeAllRanges();

					// Move cursor to end of content
					var rng = doc.createRange();

					rng.selectNode(value);
					rng.collapse(false);

					sel.addRange(rng);
				} else
					rng.collapse(false);
			} else {
				var rng = doc.selection.createRange();

				if (rng.item)
					rng.item(0).outerHTML = value;
				else
					rng.pasteHTML(value);
			}

			tinyMCE.triggerNodeChange();
		break;

		case "mceAddUndoLevel":
			if (tinyMCE.settings['custom_undo_redo']) {
				var customUndoLevels = tinyMCE.settings['custom_undo_redo_levels'];

				var newHTML = this.getBody().innerHTML;
//debug("x: " + newHTML, this.undoLevels[this.undoLevels.length-1] + "\n");
				if (newHTML != this.undoLevels[this.undoLevels.length-1]) {
//					 debug(newHTML, this.undoLevels[this.undoLevels.length-1]);
					// Trigger onchange and set is dirty
					tinyMCE.executeCallback('onchange_callback', '_onchange', 0, this);
					this.isDirty = true;

					// Time to compress
					if (customUndoLevels != -1 && this.undoLevels.length > customUndoLevels) {
						for (var i=0; i<this.undoLevels.length-1; i++) {
							//alert(this.undoLevels[i] + "=" + this.undoLevels[i+1]);
							this.undoLevels[i] = this.undoLevels[i+1];
						}

						this.undoLevels.length--;
						this.undoIndex--;
					}

					//alert(newHTML + "=" + this.undoLevels[this.undoIndex]);
					// Add new level
					this.undoLevels[this.undoIndex++] = newHTML;
					this.undoLevels.length = this.undoIndex;
//					debug("mceAddUndoLevel - undo levels:" + this.undoLevels.length + ", undo index: " + this.undoIndex);
					//window.status = "mceAddUndoLevel - undo levels:" + this.undoLevels.length + ", undo index: " + this.undoIndex;
				}

				tinyMCE.triggerNodeChange(false);
			}
			break;

		case "Undo":
			if (tinyMCE.settings['custom_undo_redo']) {
				// Is first level
				if (this.undoIndex == this.undoLevels.length) {
					this.execCommand("mceAddUndoLevel");
					this.undoIndex--;
				}

				// Do undo
				if (this.undoIndex > 0) {
					this.undoIndex--;
					this.getBody().innerHTML = this.undoLevels[this.undoIndex];
				}

				// debug("Undo - undo levels:" + this.undoLevels.length + ", undo index: " + this.undoIndex);
				tinyMCE.triggerNodeChange();
			} else
				this.getDoc().execCommand(command, user_interface, value);
			break;

		case "Redo":
			if (tinyMCE.settings['custom_undo_redo']) {
				if (this.undoIndex < (this.undoLevels.length-1)) {
					this.undoIndex++;
					this.getBody().innerHTML = this.undoLevels[this.undoIndex];
					// debug("Redo - undo levels:" + this.undoLevels.length + ", undo index: " + this.undoIndex);
				}

				tinyMCE.triggerNodeChange();
			} else
				this.getDoc().execCommand(command, user_interface, value);
			break;

		case "mceToggleVisualAid":
			this.visualAid = !this.visualAid;
			tinyMCE.handleVisualAid(this.getBody(), true, this.visualAid);
			tinyMCE.triggerNodeChange();
			break;

		case "removeformat":
			var text = this.getSelectedText();

			if (tinyMCE.isMSIE) {
				try {
					win.focus();
					var rng = doc.selection.createRange();
					rng.execCommand("RemoveFormat", false, null);
					rng.pasteHTML(rng.text);
				} catch (e) {
					// Do nothing
				}
			} else
				this.getDoc().execCommand(command, user_interface, value);

			// Remove class
			if (text.length == 0)
				this.execCommand("mceSetCSSClass", false, "");

			tinyMCE.triggerNodeChange();
			break;

		default:
			this.getDoc().execCommand(command, user_interface, value);
			tinyMCE.triggerNodeChange();
	}
};

TinyMCEControl.prototype.queryCommandValue = function(command) {
	return this.getDoc().queryCommandValue(command);
};

TinyMCEControl.prototype.queryCommandState = function(command) {
	return this.getDoc().queryCommandState(command);
};

TinyMCEControl.prototype.onAdd = function(replace_element, form_element_name, target_document) {
	var targetDoc = target_document ? target_document : document;

	this.targetDoc = targetDoc;

	tinyMCE.themeURL = tinyMCE.baseURL + "/themes/" + this.settings['theme'];
	this.settings['themeurl'] = tinyMCE.themeURL;

	if (!replace_element) {
		alert("Error: Could not find the target element.");
		return false;
	}

	var templateFunction = tinyMCE._getThemeFunction('_getInsertLinkTemplate');
	if (eval("typeof(" + templateFunction + ")") != 'undefined')
		this.insertLinkTemplate = eval(templateFunction + '(this.settings);');

	var templateFunction = tinyMCE._getThemeFunction('_getInsertImageTemplate');
	if (eval("typeof(" + templateFunction + ")") != 'undefined')
		this.insertImageTemplate = eval(templateFunction + '(this.settings);');

	var templateFunction = tinyMCE._getThemeFunction('_getEditorTemplate');
	if (eval("typeof(" + templateFunction + ")") == 'undefined') {
		alert("Error: Could not find the template function: " + templateFunction);
		return false;
	}

	var editorTemplate = eval(templateFunction + '(this.settings, this.editorId);');

	var deltaWidth = editorTemplate['delta_width'] ? editorTemplate['delta_width'] : 0;
	var deltaHeight = editorTemplate['delta_height'] ? editorTemplate['delta_height'] : 0;
	var html = '<span id="' + this.editorId + '_parent">' + editorTemplate['html'];

	var templateFunction = tinyMCE._getThemeFunction('_handleNodeChange', true);
	if (eval("typeof(" + templateFunction + ")") != 'undefined')
		this.settings['handleNodeChangeCallback'] = templateFunction;

	html = tinyMCE.replaceVar(html, "editor_id", this.editorId);
	html = tinyMCE.replaceVar(html, "default_document", tinyMCE.baseURL + "/blank.htm");
	this.settings['default_document'] = tinyMCE.baseURL + "/blank.htm";

	this.settings['old_width'] = this.settings['width'];
	this.settings['old_height'] = this.settings['height'];

	// Set default width, height
	if (this.settings['width'] == -1)
		this.settings['width'] = replace_element.offsetWidth;

	if (this.settings['height'] == -1)
		this.settings['height'] = replace_element.offsetHeight;

	// Try the style width
	if (this.settings['width'] == 0)
		this.settings['width'] = replace_element.style.width;

	// Try the style height
	if (this.settings['height'] == 0)
		this.settings['height'] = replace_element.style.height; 

	// If no width/height then default to 320x240, better than nothing
	if (this.settings['width'] == 0)
		this.settings['width'] = 320;

	if (this.settings['height'] == 0)
		this.settings['height'] = 240;

	this.settings['area_width'] = parseInt(this.settings['width']);
	this.settings['area_height'] = parseInt(this.settings['height']);
	this.settings['area_width'] += deltaWidth;
	this.settings['area_height'] += deltaHeight;

	// Special % handling
	if (("" + this.settings['width']).indexOf('%') != -1)
		this.settings['area_width'] = "100%";

	if (("" + this.settings['height']).indexOf('%') != -1)
		this.settings['area_height'] = "100%";

	if (("" + replace_element.style.width).indexOf('%') != -1) {
		this.settings['width'] = replace_element.style.width;
		this.settings['area_width'] = "100%";
	}

	if (("" + replace_element.style.height).indexOf('%') != -1) {
		this.settings['height'] = replace_element.style.height;
		this.settings['area_height'] = "100%";
	}

	html = tinyMCE.applyTemplate(html);

	this.settings['width'] = this.settings['old_width'];
	this.settings['height'] = this.settings['old_height'];

	this.visualAid = this.settings['visual'];
	this.formTargetElementId = form_element_name;

	// Get replace_element contents
	if (replace_element.nodeName.toLowerCase() == "textarea")
		this.startContent = replace_element.value;
	else
		this.startContent = replace_element.innerHTML;

	// If not text area
	if (replace_element.nodeName.toLowerCase() != "textarea") {
		this.oldTargetElement = replace_element.cloneNode(true);

		// Debug mode
		if (tinyMCE.settings['debug'])
			html += '<textarea wrap="off" id="' + form_element_name + '" name="' + form_element_name + '" cols="100" rows="15"></textarea>';
		else
			html += '<input type="hidden" type="text" id="' + form_element_name + '" name="' + form_element_name + '" />';

		html += '</span>';

		// Output HTML and set editable
		if (!tinyMCE.isMSIE) {
			var rng = replace_element.ownerDocument.createRange();
			rng.setStartBefore(replace_element);

			var fragment = rng.createContextualFragment(html);
			replace_element.parentNode.replaceChild(fragment, replace_element);
		} else
			replace_element.outerHTML = html;
	} else {
		html += '</span>';

		// Just hide the textarea element
		this.oldTargetElement = replace_element;

		if (!tinyMCE.settings['debug'])
			this.oldTargetElement.style.display = "none";

		// Output HTML and set editable
		if (!tinyMCE.isMSIE) {
			var rng = replace_element.ownerDocument.createRange();
			rng.setStartBefore(replace_element);

			var fragment = rng.createContextualFragment(html);
			replace_element.parentNode.insertBefore(fragment, replace_element);
		} else
			replace_element.insertAdjacentHTML("beforeBegin", html);
	}

	// Setup iframe
	var dynamicIFrame = false;
	var tElm = targetDoc.getElementById(this.editorId);

	if (!tinyMCE.isMSIE) {
		if (tElm && tElm.nodeName.toLowerCase() == "span") {
			tElm = tinyMCE._createIFrame(tElm);
			dynamicIFrame = true;
		}

		this.targetElement = tElm;
		this.iframeElement = tElm;
		this.contentDocument = tElm.contentDocument;
		this.contentWindow = tElm.contentWindow;

		//this.getDoc().designMode = "on";
	} else {
		if (tElm && tElm.nodeName.toLowerCase() == "span")
			tElm = tinyMCE._createIFrame(tElm);
		else
			tElm = targetDoc.frames[this.editorId];

		this.targetElement = tElm;
		this.iframeElement = targetDoc.getElementById(this.editorId);
		this.contentDocument = tElm.window.document;
		this.contentWindow = tElm.window;
		this.getDoc().designMode = "on";
	}

	// Setup base HTML
	var doc = this.contentDocument;
	if (dynamicIFrame) {
        var html = ""
            + '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
            + '<html>'
            + '<head>'
			+ '<base href="' + tinyMCE.settings['base_href'] + '" />'
            + '<title>blank_page</title>'
            + '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'
            + '</head>'
            + '<body class="mceContentBody">'
            + '</body>'
            + '</html>';

		try {
			this.getDoc().designMode = "on";
			doc.open();
			doc.write(html);
			doc.close();
		} catch (e) {
			// Failed Mozilla 1.3
			this.getDoc().location.href = tinyMCE.baseURL + "/blank.htm";
		}
	}

	// This timeout is needed in MSIE 5.5 for some odd reason
	// it seems that the document.frames isn't initialized yet?
	if (tinyMCE.isMSIE)
		window.setTimeout("TinyMCE.prototype.addEventHandlers('" + this.editorId + "');", 1);

	tinyMCE.setupContent(this.editorId, true);

	return true;
};

TinyMCEControl.prototype.getFocusElement = function() {
	if (tinyMCE.isMSIE) {
		var doc = this.getDoc();
		var rng = doc.selection.createRange();

		if (rng.collapse)
			rng.collapse(true);

		var elm = rng.item ? rng.item(0) : rng.parentElement();
	} else {
		var sel = this.getSel();
		var elm = (sel && sel.anchorNode) ? sel.anchorNode : null;

		if (tinyMCE.selectedElement != null && tinyMCE.selectedElement.nodeName.toLowerCase() == "img")
			elm = tinyMCE.selectedElement;
	}

	return elm;
};

// Global instances
var tinyMCE = new TinyMCE();
var tinyMCELang = new Array();

function debug() {
	var msg = "";

	var elm = document.getElementById("tinymce_debug");
	if (!elm) {
		var debugDiv = document.createElement("div");
		debugDiv.setAttribute("className", "debugger");
		debugDiv.className = "debugger";
		debugDiv.innerHTML = '\
			Debug output:\
			<textarea id="tinymce_debug" style="width: 100%; height: 300px">\
			</textarea>';

		document.body.appendChild(debugDiv);
		elm = document.getElementById("tinymce_debug");
	}

	var args = this.debug.arguments;
	for (var i=0; i<args.length; i++) {
		msg += args[i];
		if (i<args.length-1)
			msg += ', ';
	}

	elm.value += msg + "\n";
};
