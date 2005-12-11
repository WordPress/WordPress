/**
 * $RCSfile: tiny_mce_src.js,v $
 * $Revision: 1.281 $
 * $Date: 2005/12/02 08:12:07 $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004, Moxiecode Systems AB, All rights reserved.
 */

function TinyMCE() {
	this.majorVersion = "2";
	this.minorVersion = "0";
	this.releaseDate = "2005-12-01";

	this.instances = new Array();
	this.stickyClassesLookup = new Array();
	this.windowArgs = new Array();
	this.loadedFiles = new Array();
	this.configs = new Array();
	this.currentConfig = 0;
	this.eventHandlers = new Array();

	// Browser check
	var ua = navigator.userAgent;
	this.isMSIE = (navigator.appName == "Microsoft Internet Explorer");
	this.isMSIE5 = this.isMSIE && (ua.indexOf('MSIE 5') != -1);
	this.isMSIE5_0 = this.isMSIE && (ua.indexOf('MSIE 5.0') != -1);
	this.isGecko = ua.indexOf('Gecko') != -1;
	this.isSafari = ua.indexOf('Safari') != -1;
	this.isOpera = ua.indexOf('Opera') != -1;
	this.isMac = ua.indexOf('Mac') != -1;
	this.isNS7 = ua.indexOf('Netscape/7') != -1;
	this.isNS71 = ua.indexOf('Netscape/7.1') != -1;
	this.dialogCounter = 0;

	// Fake MSIE on Opera and if Opera fakes IE, Gecko or Safari cancel those
	if (this.isOpera) {
		this.isMSIE = true;
		this.isGecko = false;
		this.isSafari =  false;
	}

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
			if (elements[i].src && (elements[i].src.indexOf("tiny_mce.js") != -1 || elements[i].src.indexOf("tiny_mce_src.js") != -1 || elements[i].src.indexOf("tiny_mce_gzip") != -1)) {
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
	this.defParam("editor_selector", "");
	this.defParam("editor_deselector", "mceNoEditor");
	this.defParam("valid_elements", "+a[id|style|rel|rev|charset|hreflang|dir|lang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup],-strong/b[class|style],-em/i[class|style],-strike[class|style],-u[class|style],+p[style|dir|class|align],-ol[class|style],-ul[class|style],-li[class|style],br,img[id|dir|lang|longdesc|usemap|style|class|src|onmouseover|onmouseout|border=0|alt|title|hspace|vspace|width|height|align],-sub[style|class],-sup[style|class],-blockquote[dir|style],-table[border=0|cellspacing|cellpadding|width|height|class|align|summary|style|dir|id|lang|bgcolor|background|bordercolor],-tr[id|lang|dir|class|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor],tbody[id|class],thead[id|class],tfoot[id|class],-td[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor|scope],-th[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|scope],caption[id|lang|dir|class|style],-div[id|dir|class|align|style],-span[style|class|align],-pre[class|align|style],address[class|align|style],-h1[style|dir|class|align],-h2[style|dir|class|align],-h3[style|dir|class|align],-h4[style|dir|class|align],-h5[style|dir|class|align],-h6[style|dir|class|align],hr[class|style],font[face|size|style|id|class|dir|color]");
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
	this.defParam("verify_html", true);
	this.defParam("apply_source_formatting", false);
	this.defParam("directionality", "ltr");
	this.defParam("cleanup_on_startup", false);
	this.defParam("inline_styles", false);
	this.defParam("convert_newlines_to_brs", false);
	this.defParam("auto_reset_designmode", true);
	this.defParam("entities", "160,nbsp,38,amp,34,quot,162,cent,8364,euro,163,pound,165,yen,169,copy,174,reg,8482,trade,8240,permil,181,micro,183,middot,8226,bull,8230,hellip,8242,prime,8243,Prime,167,sect,182,para,223,szlig,8249,lsaquo,8250,rsaquo,171,laquo,187,raquo,8216,lsquo,8217,rsquo,8220,ldquo,8221,rdquo,8218,sbquo,8222,bdquo,60,lt,62,gt,8804,le,8805,ge,8211,ndash,8212,mdash,175,macr,8254,oline,164,curren,166,brvbar,168,uml,161,iexcl,191,iquest,710,circ,732,tilde,176,deg,8722,minus,177,plusmn,247,divide,8260,frasl,215,times,185,sup1,178,sup2,179,sup3,188,frac14,189,frac12,190,frac34,402,fnof,8747,int,8721,sum,8734,infin,8730,radic,8764,sim,8773,cong,8776,asymp,8800,ne,8801,equiv,8712,isin,8713,notin,8715,ni,8719,prod,8743,and,8744,or,172,not,8745,cap,8746,cup,8706,part,8704,forall,8707,exist,8709,empty,8711,nabla,8727,lowast,8733,prop,8736,ang,180,acute,184,cedil,170,ordf,186,ordm,8224,dagger,8225,Dagger,192,Agrave,194,Acirc,195,Atilde,196,Auml,197,Aring,198,AElig,199,Ccedil,200,Egrave,202,Ecirc,203,Euml,204,Igrave,206,Icirc,207,Iuml,208,ETH,209,Ntilde,210,Ograve,212,Ocirc,213,Otilde,214,Ouml,216,Oslash,338,OElig,217,Ugrave,219,Ucirc,220,Uuml,376,Yuml,222,THORN,224,agrave,226,acirc,227,atilde,228,auml,229,aring,230,aelig,231,ccedil,232,egrave,234,ecirc,235,euml,236,igrave,238,icirc,239,iuml,240,eth,241,ntilde,242,ograve,244,ocirc,245,otilde,246,ouml,248,oslash,339,oelig,249,ugrave,251,ucirc,252,uuml,254,thorn,255,yuml,914,Beta,915,Gamma,916,Delta,917,Epsilon,918,Zeta,919,Eta,920,Theta,921,Iota,922,Kappa,923,Lambda,924,Mu,925,Nu,926,Xi,927,Omicron,928,Pi,929,Rho,931,Sigma,932,Tau,933,Upsilon,934,Phi,935,Chi,936,Psi,937,Omega,945,alpha,946,beta,947,gamma,948,delta,949,epsilon,950,zeta,951,eta,952,theta,953,iota,954,kappa,955,lambda,956,mu,957,nu,958,xi,959,omicron,960,pi,961,rho,962,sigmaf,963,sigma,964,tau,965,upsilon,966,phi,967,chi,968,psi,969,omega,8501,alefsym,982,piv,8476,real,977,thetasym,978,upsih,8472,weierp,8465,image,8592,larr,8593,uarr,8594,rarr,8595,darr,8596,harr,8629,crarr,8656,lArr,8657,uArr,8658,rArr,8659,dArr,8660,hArr,8756,there4,8834,sub,8835,sup,8836,nsub,8838,sube,8839,supe,8853,oplus,8855,otimes,8869,perp,8901,sdot,8968,lceil,8969,rceil,8970,lfloor,8971,rfloor,9001,lang,9002,rang,9674,loz,9824,spades,9827,clubs,9829,hearts,9830,diams,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm,173,shy,233,eacute,237,iacute,243,oacute,250,uacute,193,Aacute,225,aacute,201,Eacute,205,Iacute,211,Oacute,218,Uacute,221,Yacute,253,yacute");
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
	this.defParam("browsers", "msie,safari,gecko,opera");
	this.defParam("dialog_type", "window");
	this.defParam("accessibility_warnings", true);
	this.defParam("merge_styles_invalid_parents", "");
	this.defParam("force_hex_style_colors", true);
	this.defParam("trim_span_elements", true);
	this.defParam("convert_fonts_to_spans", false);
	this.defParam("doctype", '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">');
	this.defParam("font_size_classes", '');
	this.defParam("font_size_style_values", 'xx-small,x-small,small,medium,large,x-large,xx-large');
	this.defParam("event_elements", 'a,img');
	this.defParam("convert_urls", true);
	this.defParam("table_inline_editing", false);
	this.defParam("object_resizing", true);

	// Browser check IE
	if (this.isMSIE && this.settings['browsers'].indexOf('msie') == -1)
		return;

	// Browser check Gecko
	if (this.isGecko && this.settings['browsers'].indexOf('gecko') == -1)
		return;

	// Browser check Safari
	if (this.isSafari && this.settings['browsers'].indexOf('safari') == -1)
		return;

	// Browser check Opera
	if (this.isOpera && this.settings['browsers'].indexOf('opera') == -1)
		return;

	// If not super absolute make it so
	var baseHREF = tinyMCE.settings['document_base_url'];
	var h = document.location.href;
	var p = h.indexOf('://');
	if (p > 0 && document.location.protocol != "file:") {
		p = h.indexOf('/', p + 3);
		h = h.substring(0, p);

		if (baseHREF.indexOf('://') == -1)
			baseHREF = h + baseHREF;

		tinyMCE.settings['document_base_url'] = baseHREF;
		tinyMCE.settings['document_base_prefix'] = h;
	}

	// Trim away query part
	if (baseHREF.indexOf('?') != -1)
		baseHREF = baseHREF.substring(0, baseHREF.indexOf('?'));

	this.settings['base_href'] = baseHREF.substring(0, baseHREF.lastIndexOf('/')) + "/";

	theme = this.settings['theme'];
	this.blockRegExp = new RegExp("^(h[1-6]|p|div|address|pre|form|table|li|ol|ul|td|blockquote|center|dl|dir|fieldset|form|noscript|noframes|menu|isindex)$", "i");
	this.posKeyCodes = new Array(13,45,36,35,33,34,37,38,39,40);
	this.uniqueURL = 'http://tinymce.moxiecode.cp/mce_temp_url'; // Make unique URL non real URL
	this.uniqueTag = '<div id="mceTMPElement" style="display: none">TMP</div>';

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
		this.settings['content_css'] = '';

	if (tinyMCE.getParam("popups_css", false)) {
		var cssPath = tinyMCE.getParam("popups_css", "");

		// Is relative
		if (cssPath.indexOf('://') == -1 && cssPath.charAt(0) != '/')
			this.settings['popups_css'] = this.documentBasePath + "/" + cssPath;
		else
			this.settings['popups_css'] = cssPath;
	} else
		this.settings['popups_css'] = tinyMCE.baseURL + "/themes/" + theme + "/css/editor_popup.css";

	if (tinyMCE.getParam("editor_css", false)) {
		var cssPath = tinyMCE.getParam("editor_css", "");

		// Is relative
		if (cssPath.indexOf('://') == -1 && cssPath.charAt(0) != '/')
			this.settings['editor_css'] = this.documentBasePath + "/" + cssPath;
		else
			this.settings['editor_css'] = cssPath;
	} else
		this.settings['editor_css'] = tinyMCE.baseURL + "/themes/" + theme + "/css/editor_ui.css";

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
		if (this.isSafari && this.getParam('safari_warning', true))
			alert("Safari support is very limited and should be considered experimental.\nSo there is no need to even submit bugreports on this early version.\nYou can disable this message by setting: safari_warning option to false");

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

	// Add plugins
	var themePlugins = tinyMCE.getParam('plugins', '', true, ',');
	if (this.settings['plugins'] != '') {
		for (var i=0; i<themePlugins.length; i++)
			this.loadScript(tinyMCE.baseURL + '/plugins/' + themePlugins[i] + '/editor_plugin' + tinyMCE.srcMode + '.js');
	}

	// Setup entities
	settings['cleanup_entities'] = new Array();
	var entities = tinyMCE.getParam('entities', '', true, ',');
	for (var i=0; i<entities.length; i+=2)
		settings['cleanup_entities']['c' + entities[i]] = entities[i+1];

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
	if (css_file == '')
		return;

	if (typeof(doc.createStyleSheet) == "undefined") {
		var elm = doc.createElement("link");

		elm.rel = "stylesheet";
		elm.href = css_file;

		if ((headArr = doc.getElementsByTagName("head")) != null && headArr.length > 0)
			headArr[0].appendChild(elm);
	} else
		var styleSheet = doc.createStyleSheet(css_file);
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
		if (!tinyMCE.isInstance(inst))
			continue;

		inst.switchSettings();

		if (inst.formElement == formElement) {
			var doc = inst.getDoc();
	
			tinyMCE._setHTML(doc, inst.formElement.value);

			if (!tinyMCE.isMSIE)
				doc.body.innerHTML = tinyMCE._cleanupHTML(inst, doc, this.settings, doc.body, inst.visualAid);
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
		if (!tinyMCE.isInstance(inst))
			continue;

		inst.switchSettings();

		tinyMCE.settings['preformatted'] = false;

		// Default to false
		if (typeof(skip_cleanup) == "undefined")
			skip_cleanup = false;

		// Default to false
		if (typeof(skip_callback) == "undefined")
			skip_callback = false;

		tinyMCE._setHTML(inst.getDoc(), inst.getBody().innerHTML);

		// Remove visual aids when cleanup is disabled
		if (inst.settings['cleanup'] == false) {
			tinyMCE.handleVisualAid(inst.getBody(), true, false, inst);
			tinyMCE._setEventsEnabled(inst.getBody(), true);
		}

		tinyMCE._customCleanup(inst, "submit_content_dom", inst.contentWindow.document.body);
		var htm = skip_cleanup ? inst.getBody().innerHTML : tinyMCE._cleanupHTML(inst, inst.getDoc(), this.settings, inst.getBody(), this.visualAid, true);
		htm = tinyMCE._customCleanup(inst, "submit_content", htm);

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

TinyMCE.prototype._setEventsEnabled = function(node, state) {
	var events = new Array('onfocus','onblur','onclick','ondblclick',
				'onmousedown','onmouseup','onmouseover','onmousemove',
				'onmouseout','onkeypress','onkeydown','onkeydown','onkeyup');

	var evs = tinyMCE.settings['event_elements'].split(',');
    for (var y=0; y<evs.length; y++){
		var elms = node.getElementsByTagName(evs[y]);
		for (var i=0; i<elms.length; i++) {
			var event = "";

			for (var x=0; x<events.length; x++) {
				if ((event = tinyMCE.getAttrib(elms[i], events[x])) != '') {
					event = tinyMCE.cleanupEventStr("" + event);

					if (!state)
						event = "return true;" + event;
					else
						event = event.replace(/^return true;/gi, '');

					elms[i].removeAttribute(events[x]);
					elms[i].setAttribute(events[x], event);
				}
			}
		}
	}
};

TinyMCE.prototype.resetForm = function(form_index) {
	var formObj = document.forms[form_index];

	for (var n in tinyMCE.instances) {
		var inst = tinyMCE.instances[n];
		if (!tinyMCE.isInstance(inst))
			continue;

		inst.switchSettings();

		for (var i=0; i<formObj.elements.length; i++) {
			if (inst.formTargetElementId == formObj.elements[i].name)
				inst.getBody().innerHTML = inst.startContent;
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

		// Cancel event so it doesn't call onbeforeonunlaod
		if (tinyMCE.isMSIE && window.event != null)
			tinyMCE.cancelEvent(window.event);
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
			var template = new Array();

			template['file']   = 'about.htm';
			template['width']  = 480;
			template['height'] = 380;

			tinyMCE.openWindow(template, {
				tinymce_version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion,
				tinymce_releasedate : tinyMCE.releaseDate,
				inline : "yes"
			});
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
					if (!tinyMCE.isInstance(tinyMCE.instances[n]))
						continue;

					try {
						tinyMCE.instances[n].getDoc().designMode = "on";
					} catch (e) {
						// Ignore any errors
					}
				}
			}

			return;
	}

	if (this.selectedInstance) {
		this.selectedInstance.execCommand(command, user_interface, value);
	} else if (tinyMCE.settings['focus_alert'])
		alert(tinyMCELang['lang_focus_alert']);
};

TinyMCE.prototype.eventPatch = function(editor_id) {
	// Remove odd, error
	if (typeof(tinyMCE) == "undefined")
		return true;

	for (var i=0; i<document.frames.length; i++) {
		try {
			if (document.frames[i].event) {
				var event = document.frames[i].event;

				if (!event.target)
					event.target = event.srcElement;

				TinyMCE.prototype.handleEvent(event);
				return;
			}
		} catch (ex) {
			// Ignore error if iframe is pointing to external URL
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
		tinyMCE.addEvent(doc, "keypress", TinyMCE.prototype.eventPatch);
		tinyMCE.addEvent(doc, "keyup", TinyMCE.prototype.eventPatch);
		tinyMCE.addEvent(doc, "keydown", TinyMCE.prototype.eventPatch);
		tinyMCE.addEvent(doc, "mouseup", TinyMCE.prototype.eventPatch);
		tinyMCE.addEvent(doc, "click", TinyMCE.prototype.eventPatch);
	} else {
		var inst = tinyMCE.instances[editor_id];
		var doc = inst.getDoc();

		inst.switchSettings();

		tinyMCE.addEvent(doc, "keypress", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "keydown", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "keyup", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "click", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "mouseup", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "mousedown", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "focus", tinyMCE.handleEvent);
		tinyMCE.addEvent(doc, "blur", tinyMCE.handleEvent);

		eval('try { doc.designMode = "On"; } catch(e) {}');
	}
};

TinyMCE.prototype._createIFrame = function(replace_element) {
	var iframe = document.createElement("iframe");
	var id = replace_element.getAttribute("id");
	var aw, ah;

	aw = "" + tinyMCE.settings['area_width'];
	ah = "" + tinyMCE.settings['area_height'];

	if (aw.indexOf('%') == -1) {
		aw = parseInt(aw);
		aw = aw < 0 ? 300 : aw;
		aw = aw + "px";
	}

	if (ah.indexOf('%') == -1) {
		ah = parseInt(ah);
		ah = ah < 0 ? 240 : ah;
		ah = ah + "px";
	}

	iframe.setAttribute("id", id);
	//iframe.setAttribute("className", "mceEditorArea");
	iframe.setAttribute("border", "0");
	iframe.setAttribute("frameBorder", "0");
	iframe.setAttribute("marginWidth", "0");
	iframe.setAttribute("marginHeight", "0");
	iframe.setAttribute("leftMargin", "0");
	iframe.setAttribute("topMargin", "0");
	iframe.setAttribute("width", aw);
	iframe.setAttribute("height", ah);
	iframe.setAttribute("allowtransparency", "true");

	if (tinyMCE.settings["auto_resize"])
		iframe.setAttribute("scrolling", "no");

	// Must have a src element in MSIE HTTPs breaks aswell as absoute URLs
	if (tinyMCE.isMSIE && !tinyMCE.isOpera)
		iframe.setAttribute("src", this.settings['default_document']);

	iframe.style.width = aw;
	iframe.style.height = ah;

	// MSIE 5.0 issue
	if (tinyMCE.isMSIE && !tinyMCE.isOpera)
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

	tinyMCE.operaOpacityCounter = 100 * tinyMCE.idCounter;

	inst.switchSettings();

	// Not loaded correctly hit it again, Mozilla bug #997860
	if (!tinyMCE.isMSIE && tinyMCE.getParam("setupcontent_reload", false) && doc.title != "blank_page") {
		// This part will remove the designMode status
		// Failes first time in Firefox 1.5b2 on Mac
		try {doc.location.href = tinyMCE.baseURL + "/blank.htm";} catch (ex) {}
		window.setTimeout("tinyMCE.setupContent('" + editor_id + "');", 1000);
		return;
	}

	if (!head) {
		window.setTimeout("tinyMCE.setupContent('" + editor_id + "');", 10);
		return;
	}

	// Import theme specific content CSS the user specific
	tinyMCE.importCSS(inst.getDoc(), tinyMCE.baseURL + "/themes/" + inst.settings['theme'] + "/css/editor_content.css");
	tinyMCE.importCSS(inst.getDoc(), inst.settings['content_css']);
	tinyMCE.executeCallback('init_instance_callback', '_initInstance', 0, inst);

	// Setup span styles
	if (tinyMCE.getParam("convert_fonts_to_spans"))
		inst.getDoc().body.setAttribute('id', 'mceSpanFonts');

	if (tinyMCE.settings['nowrap'])
		doc.body.style.whiteSpace = "nowrap";

	doc.body.dir = this.settings['directionality'];
	doc.editorId = editor_id;

	// Add on document element in Mozilla
	if (!tinyMCE.isMSIE)
		doc.documentElement.editorId = editor_id;

	// Setup base element
	var base = doc.createElement("base");
	base.setAttribute('href', tinyMCE.settings['base_href']);
	head.appendChild(base);

	// Replace new line characters to BRs
	if (tinyMCE.settings['convert_newlines_to_brs']) {
		content = tinyMCE.regexpReplace(content, "\r\n", "<br />", "gi");
		content = tinyMCE.regexpReplace(content, "\r", "<br />", "gi");
		content = tinyMCE.regexpReplace(content, "\n", "<br />", "gi");
	}

	// Open closed anchors
//	content = content.replace(new RegExp('<a(.*?)/>', 'gi'), '<a$1></a>');

	// Call custom cleanup code
	content = tinyMCE.storeAwayURLs(content);
	content = tinyMCE._customCleanup(inst, "insert_to_editor", content);

	if (tinyMCE.isMSIE) {
		// Ugly!!!
		window.setInterval('try{tinyMCE.getCSSClasses(document.frames["' + editor_id + '"].document, "' + editor_id + '");}catch(e){}', 500);

		if (tinyMCE.settings["force_br_newlines"])
			document.frames[editor_id].document.styleSheets[0].addRule("p", "margin: 0px;");

		var body = document.frames[editor_id].document.body;

		tinyMCE.addEvent(body, "beforepaste", TinyMCE.prototype.eventPatch);
		tinyMCE.addEvent(body, "beforecut", TinyMCE.prototype.eventPatch);

		body.editorId = editor_id;
	}

	content = tinyMCE.cleanupHTMLCode(content);

	// Fix for bug #958637
	if (!tinyMCE.isMSIE) {
		var contentElement = inst.getDoc().createElement("body");
		var doc = inst.getDoc();

		contentElement.innerHTML = content;

		// Remove weridness!
		if (tinyMCE.isGecko && tinyMCE.settings['remove_lt_gt'])
			content = content.replace(new RegExp('&lt;&gt;', 'g'), "");

		if (tinyMCE.settings['cleanup_on_startup'])
			tinyMCE.setInnerHTML(inst.getBody(), tinyMCE._cleanupHTML(inst, doc, this.settings, contentElement));
		else {
			// Convert all strong/em to b/i
			content = tinyMCE.regexpReplace(content, "<strong", "<b", "gi");
			content = tinyMCE.regexpReplace(content, "<em(/?)>", "<i$1>", "gi");
			content = tinyMCE.regexpReplace(content, "<em ", "<i ", "gi");
			content = tinyMCE.regexpReplace(content, "</strong>", "</b>", "gi");
			content = tinyMCE.regexpReplace(content, "</em>", "</i>", "gi");
			tinyMCE.setInnerHTML(inst.getBody(), content);
		}

		inst.convertAllRelativeURLs();
	} else {
		if (tinyMCE.settings['cleanup_on_startup']) {
			tinyMCE._setHTML(inst.getDoc(), content);

			// Produces permission denied error in MSIE 5.5
			eval('try {tinyMCE.setInnerHTML(inst.getBody(), tinyMCE._cleanupHTML(inst, inst.contentDocument, this.settings, inst.getBody()));} catch(e) {}');
		} else
			tinyMCE._setHTML(inst.getDoc(), content);
	}

	// Fix for bug #957681
	//inst.getDoc().designMode = inst.getDoc().designMode;

	// Setup element references
	var parentElm = document.getElementById(inst.editorId + '_parent');
	if (parentElm.lastChild.nodeName == "INPUT")
		inst.formElement = tinyMCE.isGecko ? parentElm.firstChild : parentElm.lastChild;
	else
		inst.formElement = tinyMCE.isGecko ? parentElm.previousSibling : parentElm.nextSibling;

	tinyMCE.handleVisualAid(inst.getBody(), true, tinyMCE.settings['visual'], inst);
	tinyMCE.executeCallback('setupcontent_callback', '_setupContent', 0, editor_id, inst.getBody(), inst.getDoc());

	// Re-add design mode on mozilla
	if (!tinyMCE.isMSIE)
		TinyMCE.prototype.addEventHandlers(editor_id);

	// Add blur handler
	if (tinyMCE.isMSIE)
		tinyMCE.addEvent(inst.getBody(), "blur", TinyMCE.prototype.eventPatch);

	// Trigger node change, this call locks buttons for tables and so forth
	tinyMCE.selectedInstance = inst;
	tinyMCE.selectedElement = inst.contentWindow.document.body;

	if (!inst.isHidden())
		tinyMCE.triggerNodeChange(false, true);

	// Call custom DOM cleanup
	tinyMCE._customCleanup(inst, "insert_to_editor_dom", inst.getBody());
	tinyMCE._customCleanup(inst, "setup_content_dom", inst.getBody());
	tinyMCE._setEventsEnabled(inst.getBody(), false);
	tinyMCE.cleanupAnchors(inst.getDoc());

	if (tinyMCE.getParam("convert_fonts_to_spans"))
		tinyMCE.convertSpansToFonts(inst.getDoc());

	inst.startContent = tinyMCE.trim(inst.getBody().innerHTML);
	inst.undoLevels[inst.undoLevels.length] = inst.startContent;

	tinyMCE.operaOpacityCounter = -1;
};

TinyMCE.prototype.cleanupHTMLCode = function(s) {
	s = s.replace(/<p \/>/gi, '<p>&nbsp;</p>');
	s = s.replace(/<p>\s*<\/p>/gi, '<p>&nbsp;</p>');

	// Open closed tags like <b/> to <b></b>
//	tinyMCE.debug("f:" + s);
	s = s.replace(/<(h[1-6]|p|div|address|pre|form|table|li|ol|ul|td|b|em|strong|i|strike|u|span|a|ul|ol|li|blockquote)([a-z]*)([^\\|>]*?)\/>/gi, '<$1$2$3></$1$2>');
//	tinyMCE.debug("e:" + s);

	// Remove trailing space <b > to <b>
	s = s.replace(new RegExp('\\s+></', 'gi'), '></');

	// Close tags <img></img> to <img/>
	s = s.replace(/<(img|br|hr)(.*?)><\/(img|br|hr)>/gi, '<$1$2 />');

	// Weird MSIE bug, <p><hr /></p> breaks runtime?
	if (tinyMCE.isMSIE)
		s = s.replace(/<p><hr \/><\/p>/gi, "<hr>");

	// Convert relative anchors to absolute URLs ex: #something to file.htm#something
	s = s.replace(new RegExp('(href=\"?)(\\s*?#)', 'gi'), '$1' + tinyMCE.settings['document_base_url'] + "#");

	return s;
};

TinyMCE.prototype.storeAwayURLs = function(s) {
	// Remove all mce_src, mce_href and replace them with new ones
	s = s.replace(new RegExp('mce_src\\s*=\\s*\"[^ >\"]*\"', 'gi'), '');
	s = s.replace(new RegExp('mce_href\\s*=\\s*\"[^ >\"]*\"', 'gi'), '');
	s = s.replace(new RegExp('src\\s*=\\s*\"([^ >\"]*)\"', 'gi'), 'src="$1" mce_src="$1"');
	s = s.replace(new RegExp('href\\s*=\\s*\"([^ >\"]*)\"', 'gi'), 'href="$1" mce_href="$1"');

	return s;
};

TinyMCE.prototype.cancelEvent = function(e) {
	if (tinyMCE.isMSIE) {
		e.returnValue = false;
		e.cancelBubble = true;
	} else
		e.preventDefault();
};

TinyMCE.prototype.removeTinyMCEFormElements = function(form_obj) {
	// Check if form is valid
	if (typeof(form_obj) == "undefined" || form_obj == null)
		return;

	// If not a form, find the form
	if (form_obj.nodeName != "FORM") {
		if (form_obj.form)
			form_obj = form_obj.form;
		else
			form_obj = tinyMCE.getParentElement(form_obj, "form");
	}

	// Still nothing
	if (form_obj == null)
		return;

	// Disable all UI form elements that TinyMCE created
	for (var i=0; i<form_obj.elements.length; i++) {
		var elementId = form_obj.elements[i].name ? form_obj.elements[i].name : form_obj.elements[i].id;

		if (elementId.indexOf('mce_editor_') == 0)
			form_obj.elements[i].disabled = true;
	}
};

TinyMCE.prototype.accessibleEventHandler = function(e) {
	var win = this._win;
	e = tinyMCE.isMSIE ? win.event : e;
	var elm = tinyMCE.isMSIE ? e.srcElement : e.target;

	// Piggyback onchange
	if (elm.nodeName == "SELECT" && !elm.oldonchange) {
		elm.oldonchange = elm.onchange;
		elm.onchange = null;
	}

	// Execute onchange and remove piggyback
	if (e.keyCode == 13 || e.keyCode == 32) {
		elm.onchange = elm.oldonchange;
		elm.onchange();
		elm.oldonchange = null;
		tinyMCE.cancelEvent(e);
	}
};

TinyMCE.prototype.addSelectAccessibility = function(e, select, win) {
	// Add event handlers 
	if (!select._isAccessible) {
		select.onkeydown = tinyMCE.accessibleEventHandler;
		select._isAccessible = true;
		select._win = win;
	}
};

TinyMCE.prototype.handleEvent = function(e) {
	// Remove odd, error
	if (typeof(tinyMCE) == "undefined")
		return true;

	//tinyMCE.debug(e.type + " " + e.target.nodeName + " " + (e.relatedTarget ? e.relatedTarget.nodeName : ""));

	switch (e.type) {
		case "blur":
			if (tinyMCE.selectedInstance)
				tinyMCE.selectedInstance.execCommand('mceEndTyping');

			return;

		case "submit":
			tinyMCE.removeTinyMCEFormElements(tinyMCE.isMSIE ? window.event.srcElement : e.target);
			tinyMCE.triggerSave();
			tinyMCE.isNotDirty = true;
			return;

		case "reset":
			var formObj = tinyMCE.isMSIE ? window.event.srcElement : e.target;

			for (var i=0; i<document.forms.length; i++) {
				if (document.forms[i] == formObj)
					window.setTimeout('tinyMCE.resetForm(' + i + ');', 10);
			}

			return;

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
					tinyMCE.execCommand("mceAddUndoLevel");
					tinyMCE.cancelEvent(e);
					return false;
				}
			}

			// Handle backspace
			if (tinyMCE.isGecko && tinyMCE.settings['force_p_newlines'] && (e.keyCode == 8 || e.keyCode == 46) && !e.shiftKey) {
				// Insert P element instead of BR
				if (tinyMCE.selectedInstance._handleBackSpace(e.type)) {
					// Cancel event
					tinyMCE.execCommand("mceAddUndoLevel");
					tinyMCE.cancelEvent(e);
					return false;
				}
			}

			// Mozilla custom key handling
			if (tinyMCE.isGecko && (e.ctrlKey && !e.altKey) && tinyMCE.settings['custom_undo_redo']) {
				if (tinyMCE.settings['custom_undo_redo_keyboard_shortcuts']) {
					if (e.charCode == 122) { // Ctrl+Z
						tinyMCE.selectedInstance.execCommand("Undo");
						tinyMCE.cancelEvent(e);
						return false;
					}

					if (e.charCode == 121) { // Ctrl+Y
						tinyMCE.selectedInstance.execCommand("Redo");
						tinyMCE.cancelEvent(e);
						return false;
					}
				}

				if (e.charCode == 98) { // Ctrl+B
					tinyMCE.selectedInstance.execCommand("Bold");
					tinyMCE.cancelEvent(e);
					return false;
				}

				if (e.charCode == 105) { // Ctrl+I
					tinyMCE.selectedInstance.execCommand("Italic");
					tinyMCE.cancelEvent(e);
					return false;
				}

				if (e.charCode == 117) { // Ctrl+U
					tinyMCE.selectedInstance.execCommand("Underline");
					tinyMCE.cancelEvent(e);
					return false;
				}

				if (e.charCode == 118) { // Ctrl+V
					tinyMCE.selectedInstance.execCommand("mceInsertContent", false, '<geckopastefix/>');
				}
			}

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

					tinyMCE.execCommand("mceAddUndoLevel");
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

			var inst = tinyMCE.selectedInstance;

			// Handle backspace
			if (tinyMCE.isGecko && tinyMCE.settings['force_p_newlines'] && (e.keyCode == 8 || e.keyCode == 46) && !e.shiftKey) {
				// Insert P element instead of BR
				if (tinyMCE.selectedInstance._handleBackSpace(e.type)) {
					// Cancel event
					tinyMCE.execCommand("mceAddUndoLevel");
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
				tinyMCE.handleVisualAid(tinyMCE.selectedInstance.getBody(), true, tinyMCE.settings['visual'], tinyMCE.selectedInstance);

			// Fix empty elements on return/enter, check where enter occured
			if (tinyMCE.isMSIE && e.type == "keydown" && e.keyCode == 13)
				tinyMCE.enterKeyElement = tinyMCE.selectedInstance.getFocusElement();

			// Fix empty elements on return/enter
			if (tinyMCE.isMSIE && e.type == "keyup" && e.keyCode == 13) {
				var elm = tinyMCE.enterKeyElement;
				if (elm) {
					var re = new RegExp('^HR|IMG|BR$','g'); // Skip these
					var dre = new RegExp('^H[1-6]$','g'); // Add double on these

					if (!elm.hasChildNodes() && !re.test(elm.nodeName)) {
						if (dre.test(elm.nodeName))
							elm.innerHTML = "&nbsp;&nbsp;";
						else
							elm.innerHTML = "&nbsp;";
					}
				}
			}

			// Check if it's a position key
			var keys = tinyMCE.posKeyCodes;
			var posKey = false;
			for (var i=0; i<keys.length; i++) {
				if (keys[i] == e.keyCode) {
					posKey = true;
					break;
				}
			}

			// MSIE custom key handling
			if (tinyMCE.isMSIE && tinyMCE.settings['custom_undo_redo']) {
				var keys = new Array(8,46); // Backspace,Delete
				for (var i=0; i<keys.length; i++) {
					if (keys[i] == e.keyCode) {
						if (e.type == "keyup")
							tinyMCE.triggerNodeChange(false);
					}
				}

				if (tinyMCE.settings['custom_undo_redo_keyboard_shortcuts']) {
					if (e.keyCode == 90 && (e.ctrlKey && !e.altKey) && e.type == "keydown") { // Ctrl+Z
						tinyMCE.selectedInstance.execCommand("Undo");
						tinyMCE.triggerNodeChange(false);
					}

					if (e.keyCode == 89 && (e.ctrlKey && !e.altKey) && e.type == "keydown") { // Ctrl+Y
						tinyMCE.selectedInstance.execCommand("Redo");
						tinyMCE.triggerNodeChange(false);
					}

					if ((e.keyCode == 90 || e.keyCode == 89) && (e.ctrlKey && !e.altKey)) {
						// Cancel event
						e.returnValue = false;
						e.cancelBubble = true;
						return false;
					}
				}
			}

			// If undo/redo key
			if ((e.keyCode == 90 || e.keyCode == 89) && (e.ctrlKey && !e.altKey))
				return true;

			// If Ctrl key
			if (e.keyCode == 17)
				return true;

			// Handle Undo/Redo when typing content

			// Start typing (non position key)
			if (!posKey && e.type == "keyup")
				tinyMCE.execCommand("mceStartTyping");

			// End typing (position key) or some Ctrl event
			if (e.type == "keyup" && (posKey || e.ctrlKey))
				tinyMCE.execCommand("mceEndTyping");

			if (posKey && e.type == "keyup")
				tinyMCE.triggerNodeChange(false);

			if (tinyMCE.isMSIE && e.ctrlKey)
				window.setTimeout('tinyMCE.triggerNodeChange(false);', 1);
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
				if (!tinyMCE.isInstance(tinyMCE.instances[instanceName]))
					continue;

				var inst = tinyMCE.instances[instanceName];

				// Reset design mode if lost (on everything just in case)
				inst.autoResetDesignMode();

				if (inst.getBody() == targetBody) {
					tinyMCE.selectedInstance = inst;
					tinyMCE.selectedElement = e.target;
					tinyMCE.linkElement = tinyMCE.getParentElement(tinyMCE.selectedElement, "a");
					tinyMCE.imgElement = tinyMCE.getParentElement(tinyMCE.selectedElement, "img");
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
			tinyMCE.execCommand("mceEndTyping");

			if (e.type == "mouseup")
				tinyMCE.execCommand("mceAddUndoLevel");

			// Just in case
			if (!tinyMCE.selectedInstance && e.target.editorId)
				tinyMCE.selectedInstance = tinyMCE.instances[e.target.editorId];

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

		// Fix opacity in Opera
		if (tinyMCE.isOpera) {
			if (class_name == "mceButtonDisabled") {
				var suffix = "";

				if (!element.mceOldSrc)
					element.mceOldSrc = element.src;

				if (this.operaOpacityCounter > -1)
					suffix = '?rnd=' + this.operaOpacityCounter++;

				element.src = tinyMCE.baseURL + "/themes/" + tinyMCE.getParam("theme") + "/images/opacity.png" + suffix;
				element.style.backgroundImage = "url('" + element.mceOldSrc + "')";
			} else {
				if (element.mceOldSrc) {
					element.src = element.mceOldSrc;
					element.parentNode.style.backgroundImage = "";
					element.mceOldSrc = null;
				}
			}
		}
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
	this.mceOldSubmit();
	tinyMCE.isNotDirty = true;
};

TinyMCE.prototype.onLoad = function() {
	for (var c=0; c<tinyMCE.configs.length; c++) {
		tinyMCE.settings = tinyMCE.configs[c];

		var selector = tinyMCE.getParam("editor_selector");
		var deselector = tinyMCE.getParam("editor_deselector");
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
						form.mceOldSubmit = form.submit;
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

					if (tinyMCE.getAttrib(element, "class").indexOf(deselector) != -1)
						continue;

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
					var elm = nodeList.item(i);
					var trigger = elm.getAttribute(tinyMCE.settings['textarea_trigger']);

					if (selector != '' && tinyMCE.getAttrib(elm, "class").indexOf(selector) == -1)
						continue;

					if (selector != '')
						trigger = selector != "" ? "true" : "";

					if (tinyMCE.getAttrib(elm, "class").indexOf(deselector) != -1)
						continue;

					if ((mode == "specific_textareas" && trigger == "true") || (mode == "textareas" && trigger != "false"))
						elementRefAr[elementRefAr.length] = elm;
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
			if (!tinyMCE.isInstance(instance))
				continue;

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
	var name = "";

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

				if (elmMatch.charAt(0) == '+' || elmMatch.charAt(0) == '-')
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

	if (element_name.charAt(0) == '+' || element_name.charAt(0) == '-')
		name = element_name.substring(1);

	// Special Mozilla stuff
	if (!tinyMCE.isMSIE) {
		// Fix for bug #958498
		if (name == "strong" && !tinyMCE.cleanup_on_save)
			element_name = "b";
		else if (name == "em" && !tinyMCE.cleanup_on_save)
			element_name = "i";
	}

	var elmData = new Object();

	elmData.element_name = element_name;
	elmData.valid_attribs = elementAttribs;

	return elmData;
};

/**
 * This function moves CSS styles to/from attributes.
 */
TinyMCE.prototype._moveStyle = function(elm, style, attrib) {
	if (tinyMCE.cleanup_inline_styles) {
		var val = tinyMCE.getAttrib(elm, attrib);

		if (val != '') {
			val = '' + val;

			switch (attrib) {
				case "background":
					val = "url('" + val + "')";
					break;

				case "bordercolor":
					if (elm.style.borderStyle == '' || elm.style.borderStyle == 'none')
						elm.style.borderStyle = 'solid';
					break;

				case "border":
				case "width":
				case "height":
					if (attrib == "border" && elm.style.borderWidth > 0)
						return;

					if (val.indexOf('%') == -1)
						val += 'px';
					break;

				case "vspace":
				case "hspace":
					elm.style.marginTop = val + "px";
					elm.style.marginBottom = val + "px";
					elm.removeAttribute(attrib);
					return;

				case "align":
					if (elm.nodeName == "IMG") {
						if (tinyMCE.isMSIE)
							elm.style.styleFloat = val;
						else
							elm.style.cssFloat = val;
					} else
						elm.style.textAlign = val;

					elm.removeAttribute(attrib);
					return;
			}

			if (val != '') {
				eval('elm.style.' + style + ' = val;');
				elm.removeAttribute(attrib);
			}
		}
	} else {
		if (style == '')
			return;

		var val = eval('elm.style.' + style) == '' ? tinyMCE.getAttrib(elm, attrib) : eval('elm.style.' + style);
		val = val == null ? '' : '' + val;

		switch (attrib) {
			// Always move background to style
			case "background":
				if (val.indexOf('url') == -1 && val != '')
					val = "url('" + val + "');";

				if (val != '') {
					elm.style.backgroundImage = val;
					elm.removeAttribute(attrib);
				}
				return;

			case "border":
			case "width":
			case "height":
				val = val.replace('px', '');
				break;

			case "align":
				if (tinyMCE.getAttrib(elm, 'align') == '') {
					if (elm.nodeName == "IMG") {
						if (tinyMCE.isMSIE && elm.style.styleFloat != '') {
							val = elm.style.styleFloat;
							style = 'styleFloat';
						} else if (tinyMCE.isGecko && elm.style.cssFloat != '') {
							val = elm.style.cssFloat;
							style = 'cssFloat';
						}
					}
				}
				break;
		}

		if (val != '') {
			elm.removeAttribute(attrib);
			elm.setAttribute(attrib, val);
			eval('elm.style.' + style + ' = "";');
		}
	}
};

TinyMCE.prototype._cleanupAttribute = function(valid_attributes, element_name, attribute_node, element_node) {
	var attribName = attribute_node.nodeName.toLowerCase();
	var attribValue = attribute_node.nodeValue;
	var attribMustBeValue = null;
	var verified = false;

	// Mozilla attibute, remove them
	if (attribName.indexOf('moz_') != -1)
		return null;

	if (!tinyMCE.cleanup_on_save && (attribName == "mce_href" || attribName == "mce_src"))
		return {name : attribName, value : attribValue};

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

		case "width":
		case "height":
		case "border":
			// Old MSIE needs this
			if (tinyMCE.isMSIE5)
				attribValue = eval("element_node." + attribName);
			break;

		case "shape":
			attribValue = attribValue.toLowerCase();
			break;

		case "cellspacing":
			if (tinyMCE.isMSIE5)
				attribValue = element_node.cellSpacing;
			break;

		case "cellpadding":
			if (tinyMCE.isMSIE5)
				attribValue = element_node.cellPadding;
			break;

		case "color":
			if (tinyMCE.isMSIE5 && element_name == "font")
				attribValue = element_node.color;
			break;

		case "class":
			// Remove mceItem classes from anchors
			if (tinyMCE.cleanup_on_save && attribValue.indexOf('mceItemAnchor') != -1)
				attribValue = attribValue.replace(/mceItem[a-z0-9]+/gi, '');

			if (element_name == "table" || element_name == "td" || element_name == "th") {
				// Handle visual aid
				if (tinyMCE.cleanup_visual_table_class != "")
					attribValue = tinyMCE.getVisualAidClass(attribValue, !tinyMCE.cleanup_on_save);
			}

			if (!tinyMCE._verifyClass(element_node) || attribValue == "")
				return null;

			break;

		case "onfocus":
		case "onblur":
		case "onclick":
		case "ondblclick":
		case "onmousedown":
		case "onmouseup":
		case "onmouseover":
		case "onmousemove":
		case "onmouseout":
		case "onkeypress":
		case "onkeydown":
		case "onkeydown":
		case "onkeyup":
			attribValue = tinyMCE.cleanupEventStr("" + attribValue);

			if (attribValue.indexOf('return false;') == 0)
				attribValue = attribValue.substring(14);

			break;

		case "style":
			attribValue = tinyMCE.serializeStyle(tinyMCE.parseStyle(tinyMCE.getAttrib(element_node, "style")));
			break;

		// Convert the URLs of these
		case "href":
		case "src":
		case "longdesc":
			attribValue = tinyMCE.getAttrib(element_node, attribName);

			// Use mce_href instead
			var href = tinyMCE.getAttrib(element_node, "mce_href");
			if (attribName == "href" && href != "")
				attribValue = href;

			// Use mce_src instead
			var src = tinyMCE.getAttrib(element_node, "mce_src");
			if (attribName == "src" && src != "")
				attribValue = src;

			// Always use absolute URLs within TinyMCE
			if (!tinyMCE.cleanup_on_save)
				attribValue = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], attribValue);
			else if (tinyMCE.getParam('convert_urls'))
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
		case "mce_href":
		case "mce_src":
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

TinyMCE.prototype.clearArray = function(ar) {
	// Since stupid people tend to extend core objects like
	// Array with their own crap I needed to make functions that clean away
	// this junk so the arrays get clean and nice as they should be
	for (var key in ar)
		ar[key] = null;
};

TinyMCE.prototype.isInstance = function(inst) {
	return inst != null && typeof(inst) == "object" && inst.isTinyMCEControl;
};

TinyMCE.prototype.parseStyle = function(str) {
	var ar = new Array();

	if (str == null)
		return ar;

	var st = str.split(';');

	tinyMCE.clearArray(ar);

	for (var i=0; i<st.length; i++) {
		if (st[i] == '')
			continue;

		var re = new RegExp('^\\s*([^:]*):\\s*(.*)\\s*$');
		var pa = st[i].replace(re, '$1||$2').split('||');
//tinyMCE.debug(str, pa[0] + "=" + pa[1], st[i].replace(re, '$1||$2'));
		if (pa.length == 2)
			ar[pa[0].toLowerCase()] = pa[1];
	}

	return ar;
};

TinyMCE.prototype.compressStyle = function(ar, pr, sf, res) {
	var box = new Array();

	box[0] = ar[pr + '-top' + sf];
	box[1] = ar[pr + '-left' + sf];
	box[2] = ar[pr + '-right' + sf];
	box[3] = ar[pr + '-bottom' + sf];

	for (var i=0; i<box.length; i++) {
		if (box[i] == null)
			return;

		for (var a=0; a<box.length; a++) {
			if (box[a] != box[i])
				return;
		}
	}

	// They are all the same
	ar[res] = box[0];
	ar[pr + '-top' + sf] = null;
	ar[pr + '-left' + sf] = null;
	ar[pr + '-right' + sf] = null;
	ar[pr + '-bottom' + sf] = null;
};

TinyMCE.prototype.serializeStyle = function(ar) {
	var str = "";

	// Compress box
	tinyMCE.compressStyle(ar, "border", "", "border");
	tinyMCE.compressStyle(ar, "border", "-width", "border-width");
	tinyMCE.compressStyle(ar, "border", "-color", "border-color");

	for (var key in ar) {
		var val = ar[key];
		if (typeof(val) == 'function')
			continue;

		if (val != null && val != '') {
			val = '' + val; // Force string

			// Fix style URL
			val = val.replace(new RegExp("url\\(\\'?([^\\']*)\\'?\\)", 'gi'), "url('$1')");

			// Convert URL
			if (val.indexOf('url(') != -1 && tinyMCE.getParam('convert_urls')) {
				var m = new RegExp("url\\('(.*?)'\\)").exec(val);

				if (m.length > 1)
					val = "url('" + eval(tinyMCE.getParam('urlconverter_callback') + "(m[1], null, true);") + "')";
			}

			// Force HEX colors
			if (tinyMCE.getParam("force_hex_style_colors"))
				val = tinyMCE.convertRGBToHex(val, true);

			if (val != "url('')")
				str += key.toLowerCase() + ": " + val + "; ";
		}
	}

	if (new RegExp('; $').test(str))
		str = str.substring(0, str.length - 2);

	return str;
};

TinyMCE.prototype.convertRGBToHex = function(s, k) {
	if (s.toLowerCase().indexOf('rgb') != -1) {
		var re = new RegExp("(.*?)rgb\\s*?\\(\\s*?([0-9]+).*?,\\s*?([0-9]+).*?,\\s*?([0-9]+).*?\\)(.*?)", "gi");
		var rgb = s.replace(re, "$1,$2,$3,$4,$5").split(',');
		if (rgb.length == 5) {
			r = parseInt(rgb[1]).toString(16);
			g = parseInt(rgb[2]).toString(16);
			b = parseInt(rgb[3]).toString(16);

			r = r.length == 1 ? '0' + r : r;
			g = g.length == 1 ? '0' + g : g;
			b = b.length == 1 ? '0' + b : b;

			s = "#" + r + g + b;

			if (k)
				s = rgb[0] + s + rgb[4];
		}
	}

	return s;
};

TinyMCE.prototype.convertHexToRGB = function(s) {
	if (s.indexOf('#') != -1) {
		s = s.replace(new RegExp('[^0-9A-F]', 'gi'), '');
		return "rgb(" + parseInt(s.substring(0, 2), 16) + "," + parseInt(s.substring(2, 4), 16) + "," + parseInt(s.substring(4, 6), 16) + ")";
	}

	return s;
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
			var openTag = false, nonEmptyTag = false;

			if (elementName != null && elementName.charAt(0) == '+') {
				elementName = elementName.substring(1);
				openTag = true;
			}

			if (elementName != null && elementName.charAt(0) == '-') {
				elementName = elementName.substring(1);
				nonEmptyTag = true;
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

			if (tinyMCE.cleanup_on_save) {
				if (node.nodeName == "A" && node.className == "mceItemAnchor") {
					if (node.hasChildNodes()) {
						for (var i=0; i<node.childNodes.length; i++)
							output += this.cleanupNode(node.childNodes[i]);
					}

					return '<a name="' + this.convertStringToXML(node.getAttribute("name")) + '"></a>' + output;
				}
			}

			// Remove deprecated attributes
			var re = new RegExp("^(TABLE|TD|TR)$");
			if (re.test(node.nodeName)) {
				// Move attrib to style
				if ((node.nodeName != "TABLE" || tinyMCE.cleanup_inline_styles) && (width = tinyMCE.getAttrib(node, "width")) != '') {
					node.style.width = width.indexOf('%') != -1 ? width : width.replace(/[^0-9]/gi, '') + "px";
					node.removeAttribute("width");
				}

				// Is table and not inline
				if ((node.nodeName == "TABLE" && !tinyMCE.cleanup_inline_styles) && node.style.width != '') {
					tinyMCE.setAttrib(node, "width", node.style.width.replace('px',''));
					node.style.width = '';
				}

				// Move attrib to style
				if ((height = tinyMCE.getAttrib(node, "height")) != '') {
					height = "" + height; // Force string
					node.style.height = height.indexOf('%') != -1 ? height : height.replace(/[^0-9]/gi, '') + "px";
					node.removeAttribute("height");
				}
			}

			// Handle inline/outline styles
			if (tinyMCE.cleanup_inline_styles) {
				var re = new RegExp("^(TABLE|TD|TR|IMG|HR)$");
				if (re.test(node.nodeName) && tinyMCE.getAttrib(node, "class").indexOf('mceItem') == -1) {
					tinyMCE._moveStyle(node, 'width', 'width');
					tinyMCE._moveStyle(node, 'height', 'height');
					tinyMCE._moveStyle(node, 'borderWidth', 'border');
					tinyMCE._moveStyle(node, '', 'vspace');
					tinyMCE._moveStyle(node, '', 'hspace');
					tinyMCE._moveStyle(node, 'textAlign', 'align');
					tinyMCE._moveStyle(node, 'backgroundColor', 'bgColor');
					tinyMCE._moveStyle(node, 'borderColor', 'borderColor');
					tinyMCE._moveStyle(node, 'backgroundImage', 'background');

					// Refresh element in old MSIE
					if (tinyMCE.isMSIE5)
						node.outerHTML = node.outerHTML;
				} else if (tinyMCE.isBlockElement(node))
					tinyMCE._moveStyle(node, 'textAlign', 'align');

				if (node.nodeName == "FONT")
					tinyMCE._moveStyle(node, 'color', 'color');
			}

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

			if ((tinyMCE.isMSIE && !tinyMCE.isOpera) && elementName == "style")
				return "<style>" + node.innerHTML + "</style>";

			// Remove empty tables
			if (elementName == "table" && !node.hasChildNodes())
				return "";

			// Handle element attributes
			if (node.attributes.length > 0) {
				var lastAttrib = "";

				for (var i=0; i<node.attributes.length; i++) {
					if (node.attributes[i].specified) {
						// Is the attrib already processed (removed duplicate attributes in opera TD[align=left])
						if (tinyMCE.isOpera) {
							if (node.attributes[i].nodeName == lastAttrib)
								continue;

							lastAttrib = node.attributes[i].nodeName;
						}

						// tinyMCE.debug(node.nodeName, node.attributes[i].nodeName, node.attributes[i].nodeValue, node.innerHTML);
						var attrib = tinyMCE._cleanupAttribute(elementValidAttribs, elementName, node.attributes[i], node);
						if (attrib && attrib.value != "")
							elementAttribs += " " + attrib.name + "=" + '"' + this.convertStringToXML("" + attrib.value) + '"';
					}
				}
			}

			// MSIE table summary fix (MSIE 5.5)
			if (tinyMCE.isMSIE && elementName == "table" && node.getAttribute("summary") != null && elementAttribs.indexOf('summary') == -1) {
				var summary = tinyMCE.getAttrib(node, 'summary');
				if (summary != '')
					elementAttribs += " summary=" + '"' + this.convertStringToXML(summary) + '"';
			}

			// Handle missing attributes in MSIE 5.5
			if (tinyMCE.isMSIE5 && /^(td|img|a)$/.test(elementName)) {
				var ma = new Array("scope", "longdesc", "hreflang", "charset", "type");

				for (var u=0; u<ma.length; u++) {
					if (node.getAttribute(ma[u]) != null) {
						var s = tinyMCE.getAttrib(node, ma[u]);

						if (s != '')
							elementAttribs += " " + ma[u] + "=" + '"' + this.convertStringToXML(s) + '"';
					}
				}
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
				// If not empty span
				if (!(elementName == "span" && elementAttribs == "" && tinyMCE.getParam("trim_span_elements"))) {
					// Force BR
					if (elementName == "p" && tinyMCE.cleanup_force_br_newlines)
						output += "<div" + elementAttribs + ">";
					else
						output += "<" + elementName + elementAttribs + ">";
				}

				for (var i=0; i<node.childNodes.length; i++)
					output += this.cleanupNode(node.childNodes[i]);

				// If not empty span
				if (!(elementName == "span" && elementAttribs == "" && tinyMCE.getParam("trim_span_elements"))) {
					// Force BR
					if (elementName == "p" && tinyMCE.cleanup_force_br_newlines)
						output += "</div><br />";
					else
						output += "</" + elementName + ">";
				}
			} else {
				if (!nonEmptyTag) {
					if (openTag)
						output += "<" + elementName + elementAttribs + "></" + elementName + ">";
					else
						output += "<" + elementName + elementAttribs + " />";
				}
			}

			return output;

		case 3: // Text
			// Do not convert script elements
			if (node.parentNode.nodeName == "SCRIPT" || node.parentNode.nodeName == "NOSCRIPT" || node.parentNode.nodeName == "STYLE")
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
		if (typeof(tinyMCE.settings['cleanup_entities']["c" + chr]) != 'undefined' && tinyMCE.settings['cleanup_entities']["c" + chr] != '')
			output += '&' + tinyMCE.settings['cleanup_entities']["c" + chr] + ';';
		else
			output += '' + String.fromCharCode(chr);
    }

    return output;
};

TinyMCE.prototype._getCleanupElementName = function(chunk) {
	var pos;

	if (chunk.charAt(0) == '+')
		chunk = chunk.substring(1);

	if (chunk.charAt(0) == '-')
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
};

TinyMCE.prototype._cleanupHTML = function(inst, doc, config, element, visual, on_save) {
	if (!tinyMCE.settings['cleanup']) {
		tinyMCE.handleVisualAid(inst.getBody(), true, false, inst);

		var html = element.innerHTML;

		// Remove mce_href/mce_src
		html = html.replace(new RegExp('(mce_href|mce_src)=".*?"', 'gi'), '');
		html = html.replace(/\s+>/gi, '>');

		return html;
	}

	if (on_save && tinyMCE.getParam("convert_fonts_to_spans"))
		tinyMCE.convertFontsToSpans(doc);

	// Call custom cleanup code
	tinyMCE._customCleanup(inst, on_save ? "get_from_editor_dom" : "insert_to_editor_dom", doc.body);

	// Move bgcolor to style
	var n = doc.getElementsByTagName("font");
	for (var i=0; i<n.length; i++) {
		var c = "";
		if ((c = tinyMCE.getAttrib(n[i], "bgcolor")) != "") {
			n[i].style.backgroundColor = c;
			tinyMCE.setAttrib(n[i], "bgcolor", "");
		}
	}

	// Set these for performance
	tinyMCE.cleanup_validElements = tinyMCE.settings['cleanup_validElements'];
	tinyMCE.cleanup_invalidElements = tinyMCE.settings['cleanup_invalidElements'];
	tinyMCE.cleanup_verify_html = tinyMCE.settings['verify_html'];
	tinyMCE.cleanup_force_br_newlines = tinyMCE.settings['force_br_newlines'];
	tinyMCE.cleanup_urlconverter_callback = tinyMCE.settings['urlconverter_callback'];
	tinyMCE.cleanup_verify_css_classes = tinyMCE.settings['verify_css_classes'];
	tinyMCE.cleanup_visual_table_class = tinyMCE.settings['visual_table_class'];
	tinyMCE.cleanup_apply_source_formatting = tinyMCE.settings['apply_source_formatting'];
	tinyMCE.cleanup_inline_styles = tinyMCE.settings['inline_styles'];
	tinyMCE.cleanup_visual_aid = visual;
	tinyMCE.cleanup_on_save = on_save;
	tinyMCE.cleanup_idCount = 0;
	tinyMCE.cleanup_elementLookupTable = new Array();

	var startTime = new Date().getTime();

	// Cleanup madness that breaks the editor in MSIE
	if (tinyMCE.isMSIE) {
		// Remove null ids from HR elements, results in runtime error
		var nodes = element.getElementsByTagName("hr");
		for (var i=0; i<nodes.length; i++) {
			if (nodes[i].id == "null")
				nodes[i].removeAttribute("id");
		}

		tinyMCE.setInnerHTML(element, tinyMCE.regexpReplace(element.innerHTML, '<p>[ \n\r]*<hr.*>[ \n\r]*</p>', '<hr />', 'gi'));
		tinyMCE.setInnerHTML(element, tinyMCE.regexpReplace(element.innerHTML, '<!([^-(DOCTYPE)]* )|<!/[^-]*>', '', 'gi'));
	}

	var html = this.cleanupNode(element);

	if (tinyMCE.settings['debug'])
		tinyMCE.debug("Cleanup process executed in: " + (new Date().getTime()-startTime) + " ms.");

	// Remove pesky HR paragraphs and other crap
	html = tinyMCE.regexpReplace(html, '<p><hr /></p>', '<hr />');
	html = tinyMCE.regexpReplace(html, '<p>&nbsp;</p><hr /><p>&nbsp;</p>', '<hr />');
	html = tinyMCE.regexpReplace(html, '<td>\\s*<br />\\s*</td>', '<td>&nbsp;</td>');
	html = tinyMCE.regexpReplace(html, '<p>\\s*<br />\\s*</p>', '<p>&nbsp;</p>');
	html = tinyMCE.regexpReplace(html, '<p>\\s*&nbsp;\\s*<br />\\s*&nbsp;\\s*</p>', '<p>&nbsp;</p>');
	html = tinyMCE.regexpReplace(html, '<p>\\s*&nbsp;\\s*<br />\\s*</p>', '<p>&nbsp;</p>');
	html = tinyMCE.regexpReplace(html, '<p>\\s*<br />\\s*&nbsp;\\s*</p>', '<p>&nbsp;</p>');

	// Remove empty anchors
	html = html.replace(new RegExp('<a>(.*?)</a>', 'gi'), '$1');

	// Remove some mozilla crap
	if (!tinyMCE.isMSIE)
		html = html.replace(new RegExp('<o:p _moz-userdefined="" />', 'g'), "");

	if (tinyMCE.settings['remove_linebreaks'])
		html = html.replace(new RegExp('\r|\n', 'g'), ' ');

	if (tinyMCE.getParam('apply_source_formatting')) {
		html = html.replace(new RegExp('<(p|div)([^>]*)>', 'g'), "\n<$1$2>\n");
		html = html.replace(new RegExp('<\/(p|div)([^>]*)>', 'g'), "\n</$1$2>\n");
		html = html.replace(new RegExp('<br />', 'g'), "<br />\n");
	}

	if (tinyMCE.settings['force_br_newlines']) {
		var re = new RegExp('<p>&nbsp;</p>', 'g');
		html = html.replace(re, "<br />");
	}

	if (tinyMCE.isGecko && tinyMCE.settings['remove_lt_gt']) {
		// Remove weridness!
		var re = new RegExp('&lt;&gt;', 'g');
		html = html.replace(re, "");
	}

	// Call custom cleanup code
	html = tinyMCE._customCleanup(inst, on_save ? "get_from_editor" : "insert_to_editor", html);

	// Emtpy node, return empty
	var chk = tinyMCE.regexpReplace(html, "[ \t\r\n]", "").toLowerCase();
	if (chk == "<br/>" || chk == "<br>" || chk == "<p>&nbsp;</p>" || chk == "<p>&#160;</p>" || chk == "<p></p>")
		html = "";

	if (tinyMCE.settings["preformatted"])
		return "<pre>" + html + "</pre>";

	return html;
};

TinyMCE.prototype.insertLink = function(href, target, title, onclick, style_class) {
	tinyMCE.execCommand('mceBeginUndoLevel');

	if (this.selectedInstance && this.selectedElement && this.selectedElement.nodeName.toLowerCase() == "img") {
		var doc = this.selectedInstance.getDoc();
		var linkElement = tinyMCE.getParentElement(this.selectedElement, "a");
		var newLink = false;

		if (!linkElement) {
			linkElement = doc.createElement("a");
			newLink = true;
		}

		var mhref = href;
		var thref = eval(tinyMCE.settings['urlconverter_callback'] + "(href, linkElement);");
		mhref = tinyMCE.getParam('convert_urls') ? href : mhref;

		tinyMCE.setAttrib(linkElement, 'href', thref);
		tinyMCE.setAttrib(linkElement, 'mce_href', mhref);
		tinyMCE.setAttrib(linkElement, 'target', target);
		tinyMCE.setAttrib(linkElement, 'title', title);
        tinyMCE.setAttrib(linkElement, 'onclick', onclick);
		tinyMCE.setAttrib(linkElement, 'class', style_class);

		if (newLink) {
			linkElement.appendChild(this.selectedElement.cloneNode(true));
			this.selectedElement.parentNode.replaceChild(linkElement, this.selectedElement);
		}

		return;
	}

	if (!this.linkElement && this.selectedInstance) {
		if (tinyMCE.isSafari) {
			tinyMCE.execCommand("mceInsertContent", false, '<a href="' + tinyMCE.uniqueURL + '">' + this.selectedInstance.getSelectedHTML() + '</a>');
		} else
			this.selectedInstance.contentDocument.execCommand("createlink", false, tinyMCE.uniqueURL);

		tinyMCE.linkElement = this.getElementByAttributeValue(this.selectedInstance.contentDocument.body, "a", "href", tinyMCE.uniqueURL);

		var elementArray = this.getElementsByAttributeValue(this.selectedInstance.contentDocument.body, "a", "href", tinyMCE.uniqueURL);

		for (var i=0; i<elementArray.length; i++) {
			var mhref = href;
			var thref = eval(tinyMCE.settings['urlconverter_callback'] + "(href, elementArray[i]);");
			mhref = tinyMCE.getParam('convert_urls') ? href : mhref;

			tinyMCE.setAttrib(elementArray[i], 'href', thref);
			tinyMCE.setAttrib(elementArray[i], 'mce_href', mhref);
			tinyMCE.setAttrib(elementArray[i], 'target', target);
			tinyMCE.setAttrib(elementArray[i], 'title', title);
            tinyMCE.setAttrib(elementArray[i], 'onclick', onclick);
			tinyMCE.setAttrib(elementArray[i], 'class', style_class);
		}

		tinyMCE.linkElement = elementArray[0];
	}

	if (this.linkElement) {
		var mhref = href;
		href = eval(tinyMCE.settings['urlconverter_callback'] + "(href, this.linkElement);");
		mhref = tinyMCE.getParam('convert_urls') ? href : mhref;

		tinyMCE.setAttrib(this.linkElement, 'href', href);
		tinyMCE.setAttrib(this.linkElement, 'mce_href', mhref);
		tinyMCE.setAttrib(this.linkElement, 'target', target);
		tinyMCE.setAttrib(this.linkElement, 'title', title);
        tinyMCE.setAttrib(this.linkElement, 'onclick', onclick);
		tinyMCE.setAttrib(this.linkElement, 'class', style_class);
	}

	tinyMCE.execCommand('mceEndUndoLevel');
};

TinyMCE.prototype.insertImage = function(src, alt, border, hspace, vspace, width, height, align, title, onmouseover, onmouseout) {
	tinyMCE.execCommand('mceBeginUndoLevel');

	if (src == "")
		return;

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
				tinyMCE.execCommand("mceInsertContent", false, '<img src="' + tinyMCE.uniqueURL + '" />');
			else
				this.selectedInstance.contentDocument.execCommand("insertimage", false, tinyMCE.uniqueURL);

			tinyMCE.imgElement = this.getElementByAttributeValue(this.selectedInstance.contentDocument.body, "img", "src", tinyMCE.uniqueURL);
		}
	}

	if (this.imgElement) {
		var needsRepaint = false;
		var msrc = src;

		src = eval(tinyMCE.settings['urlconverter_callback'] + "(src, tinyMCE.imgElement);");

		if (tinyMCE.getParam('convert_urls'))
			msrc = src;

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
		tinyMCE.setAttrib(this.imgElement, 'mce_src', msrc);
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

	tinyMCE.execCommand('mceEndUndoLevel');
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

	if (node && node.hasChildNodes()) {
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
	if (typeof(type) == "undefined" || node.nodeType == type && (typeof(node_name) == "undefined" || node.nodeName == node_name))
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
	} while ((node = node.parentNode) != null);

	return null;
};

TinyMCE.prototype.convertURL = function(url, node, on_save) {
	var prot = document.location.protocol;
	var host = document.location.hostname;
	var port = document.location.port;

	// Pass through file protocol
	if (prot == "file:")
		return url;

	// Something is wrong, remove weirdness
	url = tinyMCE.regexpReplace(url, '(http|https):///', '/');

	// Mailto link or anchor (Pass through)
	if (url.indexOf('mailto:') != -1 || url.indexOf('javascript:') != -1 || tinyMCE.regexpReplace(url,'[ \t\r\n\+]|%20','').charAt(0) == "#")
		return url;

	// Fix relative/Mozilla
	if (!tinyMCE.isMSIE && !on_save && url.indexOf("://") == -1 && url.charAt(0) != '/')
		return tinyMCE.settings['base_href'] + url;

	// Handle relative URLs
	if (on_save && tinyMCE.getParam('relative_urls')) {
		var curl = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], url);
		if (curl.charAt(0) == '/')
			curl = tinyMCE.settings['document_base_prefix'] + curl;

		var urlParts = tinyMCE.parseURL(curl);
		var tmpUrlParts = tinyMCE.parseURL(tinyMCE.settings['document_base_url']);

		// Force relative
		if (urlParts['host'] == tmpUrlParts['host'] && (urlParts['port'] == tmpUrlParts['port']))
			return tinyMCE.convertAbsoluteURLToRelativeURL(tinyMCE.settings['document_base_url'], curl);
	}

	// Handle absolute URLs
	if (!tinyMCE.getParam('relative_urls')) {
		var urlParts = tinyMCE.parseURL(url);
		var baseUrlParts = tinyMCE.parseURL(tinyMCE.settings['base_href']);

		// Force absolute URLs from relative URLs
		url = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], url);

		// If anchor and path is the same page
		if (urlParts['anchor'] && urlParts['path'] == baseUrlParts['path'])
			return "#" + urlParts['anchor'];
	}

	// Remove current domain
	if (tinyMCE.getParam('remove_script_host')) {
		var start = "", portPart = "";

		if (port != "")
			portPart = ":" + port;

		start = prot + "//" + host + portPart + "/";

		if (url.indexOf(start) == 0)
			url = url.substring(start.length-1);
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
		urlParts['port'] = "";
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

TinyMCE.prototype.serializeURL = function(up) {
	var url = "";

	if (up['protocol'])
		url += up['protocol'] + "://";

	if (up['host'])
		url += up['host'];

	if (up['port'])
		url += ":" + up['port'];

	if (up['path'])
		url += up['path'];

	if (up['query'])
		url += "?" + up['query'];

	if (up['anchor'])
		url += "#" + up['anchor'];

	return url;
};

/**
 * Converts an absolute path to relative path.
 */
TinyMCE.prototype.convertAbsoluteURLToRelativeURL = function(base_url, url_to_relative) {
	var baseURL = this.parseURL(base_url);
	var targetURL = this.parseURL(url_to_relative);
	var strTok1;
	var strTok2;
	var breakPoint = 0;
	var outPath = "";
	var forceSlash = false;

	if (targetURL.path == "")
		targetURL.path = "/";
	else
		forceSlash = true;

	// Crop away last path part
	base_url = baseURL.path.substring(0, baseURL.path.lastIndexOf('/'));
	strTok1 = base_url.split('/');
	strTok2 = targetURL.path.split('/');

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
		return targetURL.path;

	for (var i=0; i<(strTok1.length-(breakPoint-1)); i++)
		outPath += "../";

	for (var i=breakPoint-1; i<strTok2.length; i++) {
		if (i != (breakPoint-1))
			outPath += "/" + strTok2[i];
		else
			outPath += strTok2[i];
	}

	targetURL.protocol = null;
	targetURL.host = null;
	targetURL.port = null;
	targetURL.path = outPath == "" && forceSlash ? "/" : outPath;

	// Remove document prefix from local anchors
	var fileName = baseURL.path;
	var pos;

	if ((pos = fileName.lastIndexOf('/')) != -1)
		fileName = fileName.substring(pos + 1);

	// Is local anchor
	if (fileName == targetURL.path && targetURL.anchor != "")
		targetURL.path = "";

	return this.serializeURL(targetURL);
};

TinyMCE.prototype.convertRelativeToAbsoluteURL = function(base_url, relative_url) {
	var baseURL = TinyMCE.prototype.parseURL(base_url);
	var relURL = TinyMCE.prototype.parseURL(relative_url);

	if (relative_url == "" || relative_url.charAt(0) == '/' || relative_url.indexOf('://') != -1 || relative_url.indexOf('mailto:') != -1 || relative_url.indexOf('javascript:') != -1)
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

	// Build output URL
	relURL.protocol = baseURL.protocol;
	relURL.host = baseURL.host;
	relURL.port = baseURL.port;

	// Re-add trailing slash if it's removed
	if (relURL.path.charAt(relURL.path.length-1) == "/")
		absPath += "/";

	relURL.path = absPath;

	return TinyMCE.prototype.serializeURL(relURL);
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

	if (parse_entities)
		value = tinyMCE.entityDecode(value);

	return value;
};

TinyMCE.prototype.entityDecode = function(s) {
	var e = document.createElement("div");
	e.innerHTML = s;
	return e.innerHTML;
};

TinyMCE.prototype.addToLang = function(prefix, ar) {
	for (var key in ar) {
		if (typeof(ar[key]) == 'function')
			continue;

		tinyMCELang[(key.indexOf('lang_') == -1 ? 'lang_' : '') + (prefix != '' ? (prefix + "_") : '') + key] = ar[key];
	}

//	for (var key in ar)
//		tinyMCELang[(key.indexOf('lang_') == -1 ? 'lang_' : '') + (prefix != '' ? (prefix + "_") : '') + key] = "|" + ar[key] + "|";
};

TinyMCE.prototype.replaceVar = function(replace_haystack, replace_var, replace_str) {
	var re = new RegExp('{\\\$' + replace_var + '}', 'g');
	return replace_haystack.replace(re, replace_str);
};

TinyMCE.prototype.replaceVars = function(replace_haystack, replace_vars) {
	for (var key in replace_vars) {
		var value = replace_vars[key];
		if (typeof(value) == 'function')
			continue;

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

			inst.switchSettings();

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

			tinyMCE.executeCallback('handleNodeChangeCallback', '_handleNodeChange', 0, editorId, elm, undoIndex, undoLevels, inst.visualAid, anySelection, setup_content);
		}
	}

	if (this.selectedInstance && (typeof(focus) == "undefined" || focus))
		this.selectedInstance.contentWindow.focus();
};

TinyMCE.prototype._customCleanup = function(inst, type, content) {
	// Call custom cleanup
	var customCleanup = tinyMCE.settings['cleanup_callback'];
	if (customCleanup != "" && eval("typeof(" + customCleanup + ")") != "undefined")
		content = eval(customCleanup + "(type, content, inst);");

	// Trigger plugin cleanups
	var plugins = tinyMCE.getParam('plugins', '', true, ',');
	for (var i=0; i<plugins.length; i++) {
		if (eval("typeof(TinyMCE_" + plugins[i] +  "_cleanup)") != "undefined")
			content = eval("TinyMCE_" + plugins[i] +  "_cleanup(type, content, inst);");
	}

	return content;
};

TinyMCE.prototype.getContent = function(editor_id) {
	if (typeof(editor_id) != "undefined")
		tinyMCE.selectedInstance = tinyMCE.getInstanceById(editor_id);

	if (tinyMCE.selectedInstance) {
		var old = this.selectedInstance.getBody().innerHTML;
		var html = tinyMCE._cleanupHTML(this.selectedInstance, this.selectedInstance.getDoc(), tinyMCE.settings, this.selectedInstance.getBody(), false, true);
		tinyMCE.setInnerHTML(this.selectedInstance.getBody(), old);
		return html;
	}

	return null;
};

TinyMCE.prototype.setContent = function(html_content) {
	if (tinyMCE.selectedInstance) {
		tinyMCE.selectedInstance.execCommand('mceSetContent', false, html_content);
		tinyMCE.selectedInstance.repaint();
	}
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
	args['mce_width'] = template['width'];
	args['mce_height'] = template['height'];
	tinyMCE.windowArgs = args;

	html = template['html'];
	if (!(width = parseInt(template['width'])))
		width = 320;

	if (!(height = parseInt(template['height'])))
		height = 200;

	// Add to height in M$ due to SP2 WHY DON'T YOU GUYS IMPLEMENT innerWidth of windows!!
	if (tinyMCE.isMSIE)
		height += 40;
	else
		height += 20;

	x = parseInt(screen.width / 2.0) - (width / 2.0);
	y = parseInt(screen.height / 2.0) - (height / 2.0);

	resizable = (args && args['resizable']) ? args['resizable'] : "no";
	scrollbars = (args && args['scrollbars']) ? args['scrollbars'] : "no";

	if (template['file'].charAt(0) != '/' && template['file'].indexOf('://') == -1)
		url = tinyMCE.baseURL + "/themes/" + tinyMCE.getParam("theme") + "/" + template['file'];
	else
		url = template['file'];

	// Replace all args as variables in URL
	for (var name in args) {
		if (typeof(args[name]) == 'function')
			continue;

		url = tinyMCE.replaceVar(url, name, escape(args[name]));
	}

	if (html) {
		html = tinyMCE.replaceVar(html, "css", this.settings['popups_css']);
		html = tinyMCE.applyTemplate(html, args);

		var win = window.open("", "mcePopup" + new Date().getTime(), "top=" + y + ",left=" + x + ",scrollbars=" + scrollbars + ",dialog=yes,minimizable=" + resizable + ",modal=yes,width=" + width + ",height=" + height + ",resizable=" + resizable);
		if (win == null) {
			alert(tinyMCELang['lang_popup_blocked']);
			return;
		}

		win.document.write(html);
		win.document.close();
		win.resizeTo(width, height);
		win.focus();
	} else {
		if ((tinyMCE.isMSIE && !tinyMCE.isOpera) && resizable != 'yes' && tinyMCE.settings["dialog_type"] == "modal") {
            var features = "resizable:" + resizable 
                + ";scroll:"
                + scrollbars + ";status:yes;center:yes;help:no;dialogWidth:"
                + width + "px;dialogHeight:" + height + "px;";

			window.showModalDialog(url, window, features);
		} else {
			var modal = (resizable == "yes") ? "no" : "yes";

			if (tinyMCE.isGecko && tinyMCE.isMac)
				modal = "no";

			if (template['close_previous'] != "no")
				try {tinyMCE.lastWindow.close();} catch (ex) {}

			var win = window.open(url, "mcePopup" + new Date().getTime(), "top=" + y + ",left=" + x + ",scrollbars=" + scrollbars + ",dialog=" + modal + ",minimizable=" + resizable + ",modal=" + modal + ",width=" + width + ",height=" + height + ",resizable=" + resizable);
			if (win == null) {
				alert(tinyMCELang['lang_popup_blocked']);
				return;
			}

			if (template['close_previous'] != "no")
				tinyMCE.lastWindow = win;

			eval('try { win.resizeTo(width, height); } catch(e) { }');

			// Make it bigger if statusbar is forced
			if (tinyMCE.isGecko) {
				if (win.document.defaultView.statusbar.visible)
					win.resizeBy(0, tinyMCE.isMac ? 10 : 24);
			}

			win.focus();
		}
	}
};

TinyMCE.prototype.closeWindow = function(win) {
	win.close();
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

TinyMCE.prototype.handleVisualAid = function(el, deep, state, inst) {
	if (!el)
		return;

	var tableElement = null;

	switch (el.nodeName) {
		case "TABLE":
			var oldW = el.style.width;
			var oldH = el.style.height;
			var bo = tinyMCE.getAttrib(el, "border");

			bo = bo == "" || bo == "0" ? true : false;

			tinyMCE.setAttrib(el, "class", tinyMCE.getVisualAidClass(tinyMCE.getAttrib(el, "class"), state && bo));

			el.style.width = oldW;
			el.style.height = oldH;

			for (var y=0; y<el.rows.length; y++) {
				for (var x=0; x<el.rows[y].cells.length; x++) {
					var cn = tinyMCE.getVisualAidClass(tinyMCE.getAttrib(el.rows[y].cells[x], "class"), state && bo);
					tinyMCE.setAttrib(el.rows[y].cells[x], "class", cn);
				}
			}

			break;

		case "A":
			var anchorName = tinyMCE.getAttrib(el, "name");

			if (anchorName != '' && state) {
				el.title = anchorName;
				el.className = 'mceItemAnchor';
			} else if (anchorName != '' && !state)
				el.className = '';

			break;
	}

	if (deep && el.hasChildNodes()) {
		for (var i=0; i<el.childNodes.length; i++)
			tinyMCE.handleVisualAid(el.childNodes[i], deep, state, inst);
	}
};

TinyMCE.prototype.getAttrib = function(elm, name, default_value) {
	if (typeof(default_value) == "undefined")
		default_value = "";

	// Not a element
	if (!elm || elm.nodeType != 1)
		return default_value;

	var v = elm.getAttribute(name);

	// Try className for class attrib
	if (name == "class" && !v)
		v = elm.className;

	// Workaround for a issue with Firefox 1.5rc2+
	if (tinyMCE.isGecko && name == "src" && elm.src != null && elm.src != "")
		v = elm.src;

	// Workaround for a issue with Firefox 1.5rc2+
	if (tinyMCE.isGecko && name == "href" && elm.href != null && elm.href != "")
		v = elm.href;

	if (name == "style" && !tinyMCE.isOpera)
		v = elm.style.cssText;

	return (v && v != "") ? v : default_value;
};

TinyMCE.prototype.setAttrib = function(element, name, value, fix_value) {
	if (typeof(value) == "number" && value != null)
		value = "" + value;

	if (fix_value) {
		if (value == null)
			value = "";

		var re = new RegExp('[^0-9%]', 'g');
		value = value.replace(re, '');
	}

	if (name == "style")
		element.style.cssText = value;

	if (name == "class")
		element.className = value;

	if (value != null && value != "" && value != -1)
		element.setAttribute(name, value);
	else
		element.removeAttribute(name);
};

TinyMCE.prototype.setStyleAttrib = function(elm, name, value) {
	eval('elm.style.' + name + '=value;');

	// Style attrib deleted
	if (tinyMCE.isMSIE && value == null || value == '') {
		var str = tinyMCE.serializeStyle(tinyMCE.parseStyle(elm.style.cssText));
		elm.style.cssText = str;
		elm.setAttribute("style", str);
	}
};

TinyMCE.prototype.convertSpansToFonts = function(doc) {
	var sizes = tinyMCE.getParam('font_size_style_values').replace(/\s+/, '').split(',');

	var h = doc.body.innerHTML;
	h = h.replace(/<span/gi, '<font');
	h = h.replace(/<\/span/gi, '</font');
	doc.body.innerHTML = h;

	var s = doc.getElementsByTagName("font");
	for (var i=0; i<s.length; i++) {
		var size = tinyMCE.trim(s[i].style.fontSize).toLowerCase();
		var fSize = 0;

		for (var x=0; x<sizes.length; x++) {
			if (sizes[x] == size) {
				fSize = x + 1;
				break;
			}
		}

		if (fSize > 0) {
			tinyMCE.setAttrib(s[i], 'size', fSize);
			s[i].style.fontSize = '';
		}

		var fFace = s[i].style.fontFamily;
		if (fFace != null && fFace != "") {
			tinyMCE.setAttrib(s[i], 'face', fFace);
			s[i].style.fontFamily = '';
		}

		var fColor = s[i].style.color;
		if (fColor != null && fColor != "") {
			tinyMCE.setAttrib(s[i], 'color', tinyMCE.convertRGBToHex(fColor));
			s[i].style.color = '';
		}
	}
};

TinyMCE.prototype.convertFontsToSpans = function(doc) {
	var sizes = tinyMCE.getParam('font_size_style_values').replace(/\s+/, '').split(',');

	var h = doc.body.innerHTML;
	h = h.replace(/<font/gi, '<span');
	h = h.replace(/<\/font/gi, '</span');
	doc.body.innerHTML = h;

	var fsClasses = tinyMCE.getParam('font_size_classes');
	if (fsClasses != '')
		fsClasses = fsClasses.replace(/\s+/, '').split(',');
	else
		fsClasses = null;

	var s = doc.getElementsByTagName("span");
	for (var i=0; i<s.length; i++) {
		var fSize, fFace, fColor;

		fSize = tinyMCE.getAttrib(s[i], 'size');
		fFace = tinyMCE.getAttrib(s[i], 'face');
		fColor = tinyMCE.getAttrib(s[i], 'color');

		if (fSize != "") {
			fSize = parseInt(fSize);

			if (fSize > 0 && fSize < 8) {
				if (fsClasses != null)
					tinyMCE.setAttrib(s[i], 'class', fsClasses[fSize-1]);
				else
					s[i].style.fontSize = sizes[fSize-1];
			}

			s[i].removeAttribute('size');
		}

		if (fFace != "") {
			s[i].style.fontFamily = fFace;
			s[i].removeAttribute('face');
		}

		if (fColor != "") {
			s[i].style.color = fColor;
			s[i].removeAttribute('color');
		}
	}
};

/*
TinyMCE.prototype.applyClassesToFonts = function(doc, size) {
	var f = doc.getElementsByTagName("font");
	for (var i=0; i<f.length; i++) {
		var s = tinyMCE.getAttrib(f[i], "size");

		if (s != "")
			tinyMCE.setAttrib(f[i], 'class', "mceItemFont" + s);
	}

	if (typeof(size) != "undefined") {
		var css = "";

		for (var x=0; x<doc.styleSheets.length; x++) {
			for (var i=0; i<doc.styleSheets[x].rules.length; i++) {
				if (doc.styleSheets[x].rules[i].selectorText == '#mceSpanFonts .mceItemFont' + size) {
					css = doc.styleSheets[x].rules[i].style.cssText;
					break;
				}
			}

			if (css != "")
				break;
		}

		if (doc.styleSheets[0].rules[0].selectorText == "FONT")
			doc.styleSheets[0].removeRule(0);

		doc.styleSheets[0].addRule("FONT", css, 0);
	}
};
*/

TinyMCE.prototype.setInnerHTML = function(e, h) {
	if (tinyMCE.isMSIE && !tinyMCE.isOpera) {
		e.innerHTML = tinyMCE.uniqueTag + h;
		e.firstChild.removeNode(true);
	} else {
		h = this.fixGeckoBaseHREFBug(1, e, h);
		e.innerHTML = h;
		this.fixGeckoBaseHREFBug(2, e, h);
	}
};

TinyMCE.prototype.fixGeckoBaseHREFBug = function(m, e, h) {
	if (tinyMCE.isGecko) {
		if (m == 1) {
			h = h.replace(/\ssrc=/gi, " xsrc=");
			h = h.replace(/\shref=/gi, " xhref=");

			return h;
		} else {
			if (h.indexOf(' xsrc') != -1) {
				var n = e.getElementsByTagName("img");
				for (var i=0; i<n.length; i++) {
					var xsrc = tinyMCE.getAttrib(n[i], "xsrc");

					if (xsrc != "") {
						n[i].src = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], xsrc);
						n[i].removeAttribute("xsrc");
					}
				}

				// Select image form fields
				var n = e.getElementsByTagName("select");
				for (var i=0; i<n.length; i++) {
					var xsrc = tinyMCE.getAttrib(n[i], "xsrc");

					if (xsrc != "") {
						n[i].src = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], xsrc);
						n[i].removeAttribute("xsrc");
					}
				}

				// iframes
				var n = e.getElementsByTagName("iframe");
				for (var i=0; i<n.length; i++) {
					var xsrc = tinyMCE.getAttrib(n[i], "xsrc");

					if (xsrc != "") {
						n[i].src = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], xsrc);
						n[i].removeAttribute("xsrc");
					}
				}
			}

			if (h.indexOf(' xhref') != -1) {
				var n = e.getElementsByTagName("a");
				for (var i=0; i<n.length; i++) {
					var xhref = tinyMCE.getAttrib(n[i], "xhref");

					if (xhref != "") {
						n[i].href = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], xhref);
						n[i].removeAttribute("xhref");
					}
				}
			}
		}
	}

	return h;
};

TinyMCE.prototype.getOuterHTML = function(e) {
	if (tinyMCE.isMSIE)
		return e.outerHTML;

	var d = e.ownerDocument.createElement("body");
	d.appendChild(e);
	return d.innerHTML;
};

TinyMCE.prototype.setOuterHTML = function(doc, e, h) {
	if (tinyMCE.isMSIE) {
		e.outerHTML = h;
		return;
	}

	var d = e.ownerDocument.createElement("body");
	d.innerHTML = h;
	e.parentNode.replaceChild(d.firstChild, e);
};

TinyMCE.prototype.insertAfter = function(nc, rc){
	if (rc.nextSibling)
		rc.parentNode.insertBefore(nc, rc.nextSibling);
	else
		rc.parentNode.appendChild(nc);
};

TinyMCE.prototype.cleanupAnchors = function(doc) {
	var an = doc.getElementsByTagName("a");

	for (var i=0; i<an.length; i++) {
		if (tinyMCE.getAttrib(an[i], "name") != "") {
			var cn = an[i].childNodes;
			for (var x=cn.length-1; x>=0; x--)
				tinyMCE.insertAfter(cn[x], an[i]);
		}
	}
};

TinyMCE.prototype._setHTML = function(doc, html_content) {
	// Force closed anchors open
	//html_content = html_content.replace(new RegExp('<a(.*?)/>', 'gi'), '<a$1></a>');

	html_content = tinyMCE.cleanupHTMLCode(html_content);

	// Try innerHTML if it fails use pasteHTML in MSIE
	try {
		tinyMCE.setInnerHTML(doc.body, html_content);
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
				if (node.nodeName == "P")
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
		tinyMCE.setInnerHTML(doc.body, html);
	}

	tinyMCE.cleanupAnchors(doc);

	if (tinyMCE.getParam("convert_fonts_to_spans"))
		tinyMCE.convertSpansToFonts(doc);
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
		for (var n in tinyMCE.instances) {
			var instance = tinyMCE.instances[n];
			if (!tinyMCE.isInstance(instance))
				continue;

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
			if (!tinyMCE.isInstance(instance))
				continue;

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
			for (var x=0; x<styles.length; x++) {
				var csses = null;

				// Just ignore any errors
				eval("try {var csses = tinyMCE.isMSIE ? doc.styleSheets(" + x + ").rules : doc.styleSheets[" + x + "].cssRules;} catch(e) {}");
				if (!csses)
					return new Array();

				for (var i=0; i<csses.length; i++) {
					var selectorText = csses[i].selectorText;

					// Can be multiple rules per selector
					if (selectorText) {
						var rules = selectorText.split(',');
						for (var c=0; c<rules.length; c++) {
							// Invalid rule
							if (rules[c].indexOf(' ') != -1 || rules[c].indexOf(':') != -1 || rules[c].indexOf('mceItem') != -1)
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
	}

	// Cache em
	if (output.length > 0)
		tinyMCE.cssClasses = output;

	return output;
};

TinyMCE.prototype.regexpReplace = function(in_str, reg_exp, replace_str, opts) {
	if (in_str == null)
		return in_str;

	if (typeof(opts) == "undefined")
		opts = 'g';

	var re = new RegExp(reg_exp, opts);
	return in_str.replace(re, replace_str);
};

TinyMCE.prototype.trim = function(str) {
	return str.replace(/^\s*|\s*$/g, "");
};

TinyMCE.prototype.cleanupEventStr = function(str) {
	str = "" + str;
	str = str.replace('function anonymous()\n{\n', '');
	str = str.replace('\n}', '');
	str = str.replace(/^return true;/gi, ''); // Remove event blocker

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

TinyMCE.prototype.debug = function() {
	var msg = "";

	var elm = document.getElementById("tinymce_debug");
	if (!elm) {
		var debugDiv = document.createElement("div");
		debugDiv.setAttribute("className", "debugger");
		debugDiv.className = "debugger";
		debugDiv.innerHTML = '\
			Debug output:\
			<textarea id="tinymce_debug" style="width: 100%; height: 300px" wrap="nowrap"></textarea>';

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

// TinyMCEControl
function TinyMCEControl(settings) {
	// Undo levels
	this.undoLevels = new Array();
	this.undoIndex = 0;
	this.typingUndoIndex = -1;
	this.undoRedo = true;
	this.isTinyMCEControl = true;

	// Default settings
	this.settings = settings;
	this.settings['theme'] = tinyMCE.getParam("theme", "default");
	this.settings['width'] = tinyMCE.getParam("width", -1);
	this.settings['height'] = tinyMCE.getParam("height", -1);
};

TinyMCEControl.prototype.repaint = function() {
	if (tinyMCE.isMSIE && !tinyMCE.isOpera)
		return;

	// Ugly mozilla hack to remove ghost resize handles
	try {
		this.getBody().style.display = 'none';
		this.getDoc().execCommand('selectall', false, null);
		this.getSel().collapseToStart();
		this.getBody().style.display = 'block';
	} catch (ex) {
		// Could I care less!!
	}
};

TinyMCEControl.prototype.switchSettings = function() {
	if (tinyMCE.configs.length > 1 && tinyMCE.currentConfig != this.settings['index']) {
		tinyMCE.settings = this.settings;
		tinyMCE.currentConfig = this.settings['index'];
	}
};

TinyMCEControl.prototype.convertAllRelativeURLs = function() {
	var body = this.getBody();

	// Convert all image URL:s to absolute URL
	var elms = body.getElementsByTagName("img");
	for (var i=0; i<elms.length; i++) {
		var src = tinyMCE.getAttrib(elms[i], 'src');

		var msrc = tinyMCE.getAttrib(elms[i], 'mce_src');
		if (msrc != "")
			src = msrc;

		if (src != "") {
			src = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], src);
			elms[i].setAttribute("src", src);
		}
	}

	// Convert all link URL:s to absolute URL
	var elms = body.getElementsByTagName("a");
	for (var i=0; i<elms.length; i++) {
		var href = tinyMCE.getAttrib(elms[i], 'href');

		var mhref = tinyMCE.getAttrib(elms[i], 'mce_href');
		if (mhref != "")
			href = mhref;

		if (href && href != "") {
			href = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], href);
			elms[i].setAttribute("href", href);
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

	return tinyMCE._cleanupHTML(this, this.contentDocument, this.settings, elm, this.visualAid);
};

TinyMCEControl.prototype.getBookmark = function() {
	var rng = this.getRng();

	if (tinyMCE.isSafari)
		return rng;

	if (tinyMCE.isMSIE)
		return rng;

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
		return bookmark.select();

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
	if (!tinyMCE.settings['auto_resize'] && !(pos.absTop > scrollY && pos.absTop < (scrollY - 25 + height)))
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
	if (tinyMCE.isMSIE && !tinyMCE.isOpera)
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

	if (tinyMCE.isMSIE && !tinyMCE.isOpera)
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

//	tinyMCE.debug(body.innerHTML);

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

	startNode = startNode.nodeName == "BODY" ? startNode.firstChild : startNode;
	endNode = endNode.nodeName == "BODY" ? endNode.firstChild : endNode;

	// tinyMCE.debug(startNode, endNode);

	// Get block elements
	var startBlock = tinyMCE.getParentBlockElement(startNode);
	var endBlock = tinyMCE.getParentBlockElement(endNode);

	// Use current block name
	if (startBlock != null) {
		blockName = startBlock.nodeName;

		// Use P instead
		if (blockName == "TD" || blockName == "TABLE" || (blockName == "DIV" && new RegExp('left|right', 'gi').test(startBlock.style.cssFloat)))
			blockName = "P";
	}

	// Within a list use normal behaviour
	if (tinyMCE.getParentElement(startBlock, "OL,UL") != null)
		return false;

	// Within a table create new paragraphs
	if ((startBlock != null && startBlock.nodeName == "TABLE") || (endBlock != null && endBlock.nodeName == "TABLE"))
		startBlock = endBlock = null;

	// Setup new paragraphs
	var paraBefore = (startBlock != null && startBlock.nodeName == blockName) ? startBlock.cloneNode(false) : doc.createElement(blockName);
	var paraAfter = (endBlock != null && endBlock.nodeName == blockName) ? endBlock.cloneNode(false) : doc.createElement(blockName);

	// Is header, then force paragraph under
	if (/^(H[1-6])$/.test(blockName))
		paraAfter = doc.createElement("p");

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

	// Fix when only a image is within the TD
	if (startChop.nodeName == "TD")
		startChop = startChop.firstChild;

	if (endChop.nodeName == "TD")
		endChop = endChop.lastChild;

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

			// If not after image
			//if (rng.startContainer.nodeName != "BODY" && rng.endContainer.nodeName != "BODY")
				rng.setEndAfter(endChop);

			if (endChop.nodeName != "#text" && endChop.nodeName != "BODY")
				rngBefore.setEndAfter(endChop);

			var contents = rng.cloneContents();
			if (contents.firstChild && (contents.firstChild.nodeName == blockName || contents.firstChild.nodeName == "BODY"))
				paraAfter.innerHTML = contents.firstChild.innerHTML;
			else
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

			// tinyMCE.debug("1: ", paraBefore.innerHTML, paraAfter.innerHTML);
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
/*		var nodes = contents.firstChild.childNodes;
		for (var i=0; i<nodes.length; i++) {
			//tinyMCE.debug(nodes[i].nodeName);
			if (nodes[i].nodeName != "BODY")
				paraAfter.appendChild(nodes[i]);
		}
*/
		paraAfter.innerHTML = contents.firstChild.innerHTML;
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
	//tinyMCE.debug("2", paraBefore.innerHTML, paraAfter.innerHTML);

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
	if (!tinyMCE.isMSIE && tinyMCE.settings['auto_reset_designmode'] && this.isHidden())
		eval('try { this.getDoc().designMode = "On"; } catch(e) {}');
};

TinyMCEControl.prototype.isHidden = function() {
	if (tinyMCE.isMSIE)
		return false;

	var sel = this.getSel();

	// Weird, wheres that cursor selection?
	return (!sel || !sel.rangeCount || sel.rangeCount == 0);
};

TinyMCEControl.prototype.isDirty = function() {
	// Is content modified and not in a submit procedure
	return this.startContent != tinyMCE.trim(this.getBody().innerHTML) && !tinyMCE.isNotDirty;
};

TinyMCEControl.prototype._mergeElements = function(scmd, pa, ch, override) {
	if (scmd == "removeformat") {
		pa.className = "";
		pa.style.cssText = "";
		ch.className = "";
		ch.style.cssText = "";
		return;
	}

	var st = tinyMCE.parseStyle(tinyMCE.getAttrib(pa, "style"));
	var stc = tinyMCE.parseStyle(tinyMCE.getAttrib(ch, "style"));
	var className = tinyMCE.getAttrib(pa, "class");

	className += " " + tinyMCE.getAttrib(ch, "class");

	if (override) {
		for (var n in st) {
			if (typeof(st[n]) == 'function')
				continue;

			stc[n] = st[n];
		}
	} else {
		for (var n in stc) {
			if (typeof(stc[n]) == 'function')
				continue;

			st[n] = stc[n];
		}
	}

	tinyMCE.setAttrib(pa, "style", tinyMCE.serializeStyle(st));
	tinyMCE.setAttrib(pa, "class", tinyMCE.trim(className));
	ch.className = "";
	ch.style.cssText = "";
	ch.removeAttribute("class");
	ch.removeAttribute("style");
};

TinyMCEControl.prototype.setUseCSS = function(b) {
	var doc = this.getDoc();
	try {doc.execCommand("useCSS", false, !b);} catch (ex) {}
	try {doc.execCommand("styleWithCSS", false, b);} catch (ex) {}

	if (!tinyMCE.getParam("table_inline_editing"))
		try {doc.execCommand('enableInlineTableEditing', false, "false");} catch (ex) {}

	if (!tinyMCE.getParam("object_resizing"))
		try {doc.execCommand('enableObjectResizing', false, "false");} catch (ex) {}
};

TinyMCEControl.prototype.execCommand = function(command, user_interface, value) {
	var doc = this.getDoc();
	var win = this.getWin();
	var focusElm = this.getFocusElement();

	if (this.lastSafariSelection && !new RegExp('mceStartTyping|mceEndTyping|mceBeginUndoLevel|mceEndUndoLevel|mceAddUndoLevel', 'gi').test(command)) {
		this.moveToBookmark(this.lastSafariSelection);
		tinyMCE.selectedElement = this.lastSafariSelectedElement;
	}

	// Mozilla issue
	if (!tinyMCE.isMSIE && !this.useCSS) {
		this.setUseCSS(false);
		this.useCSS = true;
	}

	//debug("command: " + command + ", user_interface: " + user_interface + ", value: " + value);
	this.contentDocument = doc; // <-- Strange, unless this is applied Mozilla 1.3 breaks

	// Call theme execcommand
	if (tinyMCE._themeExecCommand(this.editorId, this.getBody(), command, user_interface, value))
		return;

	// Fix align on images
	if (focusElm && focusElm.nodeName == "IMG") {
		var align = focusElm.getAttribute('align');
		var img = command == "JustifyCenter" ? focusElm.cloneNode(false) : focusElm;

		switch (command) {
			case "JustifyLeft":
				if (align == 'left')
					img.removeAttribute('align');
				else
					img.setAttribute('align', 'left');

				// Remove the div
				var div = focusElm.parentNode;
				if (div && div.nodeName == "DIV" && div.childNodes.length == 1 && div.parentNode)
					div.parentNode.replaceChild(img, div);

				this.selectNode(img);
				this.repaint();
				tinyMCE.triggerNodeChange();
				return;

			case "JustifyCenter":
				img.removeAttribute('align');

				// Is centered
				var div = tinyMCE.getParentElement(focusElm, "div");
				if (div && div.style.textAlign == "center") {
					// Remove div
					if (div.nodeName == "DIV" && div.childNodes.length == 1 && div.parentNode)
						div.parentNode.replaceChild(img, div);
				} else {
					// Add div
					var div = this.getDoc().createElement("div");
					div.style.textAlign = 'center';
					div.appendChild(img);
					focusElm.parentNode.replaceChild(div, focusElm);
				}

				this.selectNode(img);
				this.repaint();
				tinyMCE.triggerNodeChange();
				return;

			case "JustifyRight":
				if (align == 'right')
					img.removeAttribute('align');
				else
					img.setAttribute('align', 'right');

				// Remove the div
				var div = focusElm.parentNode;
				if (div && div.nodeName == "DIV" && div.childNodes.length == 1 && div.parentNode)
					div.parentNode.replaceChild(img, div);

				this.selectNode(img);
				this.repaint();
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
		case "mceRepaint":
			this.repaint();
			return true;

		case "mceStoreSelection":
			this.selectionBookmark = this.getBookmark();
			return true;

		case "mceRestoreSelection":
			this.moveToBookmark(this.selectionBookmark);
			return true;

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

		case "SetStyleInfo":
			var rng = this.getRng();
			var sel = this.getSel();
			var scmd = value['command'];
			var sname = value['name'];
			var svalue = value['value'] == null ? '' : value['value'];
			//var svalue = value['value'] == null ? '' : value['value'];
			var wrapper = value['wrapper'] ? value['wrapper'] : "span";
			var parentElm = null;
			var invalidRe = new RegExp("^BODY|HTML$", "g");
			var invalidParentsRe = tinyMCE.settings['merge_styles_invalid_parents'] != '' ? new RegExp(tinyMCE.settings['merge_styles_invalid_parents'], "gi") : null;

			// Whole element selected check
			if (tinyMCE.isMSIE) {
				// Control range
				if (rng.item)
					parentElm = rng.item(0);
				else {
					var pelm = rng.parentElement();
					var prng = doc.selection.createRange();
					prng.moveToElementText(pelm);

					if (rng.htmlText == prng.htmlText || rng.boundingWidth == 0) {
						if (invalidParentsRe == null || !invalidParentsRe.test(pelm.nodeName))
							parentElm = pelm;
					}
				}
			} else {
				var felm = this.getFocusElement();
				if (sel.isCollapsed || (/td|tr|tbody|table/ig.test(felm.nodeName) && sel.anchorNode == felm.parentNode))
					parentElm = felm;
			}

			// Whole element selected
			if (parentElm && !invalidRe.test(parentElm.nodeName)) {
				if (scmd == "setstyle")
					tinyMCE.setStyleAttrib(parentElm, sname, svalue);

				if (scmd == "setattrib")
					tinyMCE.setAttrib(parentElm, sname, svalue);

				if (scmd == "removeformat") {
					parentElm.style.cssText = '';
					tinyMCE.setAttrib(parentElm, 'class', '');
				}

				// Remove style/attribs from all children
				var ch = tinyMCE.getNodeTree(parentElm, new Array(), 1);
				for (var z=0; z<ch.length; z++) {
					if (ch[z] == parentElm)
						continue;

					if (scmd == "setstyle")
						tinyMCE.setStyleAttrib(ch[z], sname, '');

					if (scmd == "setattrib")
						tinyMCE.setAttrib(ch[z], sname, '');

					if (scmd == "removeformat") {
						ch[z].style.cssText = '';
						tinyMCE.setAttrib(ch[z], 'class', '');
					}
				}
			} else {
				doc.execCommand("fontname", false, "#mce_temp_font#");
				var elementArray = tinyMCE.getElementsByAttributeValue(this.getBody(), "font", "face", "#mce_temp_font#");

				// Change them all
				for (var x=0; x<elementArray.length; x++) {
					elm = elementArray[x];
					if (elm) {
						var spanElm = doc.createElement(wrapper);

						if (scmd == "setstyle")
							tinyMCE.setStyleAttrib(spanElm, sname, svalue);

						if (scmd == "setattrib")
							tinyMCE.setAttrib(spanElm, sname, svalue);

						if (scmd == "removeformat") {
							spanElm.style.cssText = '';
							tinyMCE.setAttrib(spanElm, 'class', '');
						}

						if (elm.hasChildNodes()) {
							for (var i=0; i<elm.childNodes.length; i++)
								spanElm.appendChild(elm.childNodes[i].cloneNode(true));
						}

						spanElm.setAttribute("mce_new", "true");
						elm.parentNode.replaceChild(spanElm, elm);

						// Remove style/attribs from all children
						var ch = tinyMCE.getNodeTree(spanElm, new Array(), 1);
						for (var z=0; z<ch.length; z++) {
							if (ch[z] == spanElm)
								continue;

							if (scmd == "setstyle")
								tinyMCE.setStyleAttrib(ch[z], sname, '');

							if (scmd == "setattrib")
								tinyMCE.setAttrib(ch[z], sname, '');

							if (scmd == "removeformat") {
								ch[z].style.cssText = '';
								tinyMCE.setAttrib(ch[z], 'class', '');
							}
						}
					}
				}
			}

			// Cleaup wrappers
			var nodes = doc.getElementsByTagName(wrapper);
			for (var i=nodes.length-1; i>=0; i--) {
				var elm = nodes[i];
				var isNew = tinyMCE.getAttrib(elm, "mce_new") == "true";

				elm.removeAttribute("mce_new");

				// Is only child a element
				if (elm.childNodes && elm.childNodes.length == 1 && elm.childNodes[0].nodeType == 1) {
					//tinyMCE.debug("merge1" + isNew);
					this._mergeElements(scmd, elm, elm.childNodes[0], isNew);
					continue;
				}

				// Is I the only child
				if (elm.parentNode.childNodes.length == 1 && !invalidRe.test(elm.nodeName) && !invalidRe.test(elm.parentNode.nodeName)) {
					//tinyMCE.debug("merge2" + isNew + "," + elm.nodeName + "," + elm.parentNode.nodeName);
					if (invalidParentsRe == null || !invalidParentsRe.test(elm.parentNode.nodeName))
						this._mergeElements(scmd, elm.parentNode, elm, false);
				}
			}

			// Remove empty wrappers
			var nodes = doc.getElementsByTagName(wrapper);
			for (var i=nodes.length-1; i>=0; i--) {
				var elm = nodes[i];
				var isEmpty = true;

				// Check if it has any attribs
				var tmp = doc.createElement("body");
				tmp.appendChild(elm.cloneNode(false));

				// Is empty span, remove it
				tmp.innerHTML = tmp.innerHTML.replace(new RegExp('style=""|class=""', 'gi'), '');
				//tinyMCE.debug(tmp.innerHTML);
				if (new RegExp('<span>', 'gi').test(tmp.innerHTML)) {
					for (var x=0; x<elm.childNodes.length; x++) {
						if (elm.parentNode != null)
							elm.parentNode.insertBefore(elm.childNodes[x].cloneNode(true), elm);
					}

					elm.parentNode.removeChild(elm);
				}
			}

			// Re add the visual aids
			if (scmd == "removeformat")
				tinyMCE.handleVisualAid(this.getBody(), true, this.visualAid, this);

			tinyMCE.triggerNodeChange();

			break;

		case "FontName":
			if (value == null) {
				var s = this.getSel();

				// Find font and select it
				if (tinyMCE.isGecko && s.isCollapsed) {
					var f = tinyMCE.getParentElement(this.getFocusElement(), "font");

					if (f != null)
						this.selectNode(f, false);
				}

				// Remove format
				this.getDoc().execCommand("RemoveFormat", false, null);

				// Collapse range if font was found
				if (f != null && tinyMCE.isGecko) {
					var r = this.getRng().cloneRange();
					r.collapse(true);
					s.removeAllRanges();
					s.addRange(r);
				}
			} else
				this.getDoc().execCommand('FontName', false, value);

			if (tinyMCE.isGecko)
				window.setTimeout('tinyMCE.triggerNodeChange(false);', 1);

			return;

		case "FontSize":
			this.getDoc().execCommand('FontSize', false, value);

			if (tinyMCE.isGecko)
				window.setTimeout('tinyMCE.triggerNodeChange(false);', 1);

			return;

		case "forecolor":
			this.getDoc().execCommand('forecolor', false, value);
			break;

		case "HiliteColor":
			if (tinyMCE.isGecko) {
				this.setUseCSS(true);
				this.getDoc().execCommand('hilitecolor', false, value);
				this.setUseCSS(false);
			} else
				this.getDoc().execCommand('BackColor', false, value);
			break;

		case "Cut":
		case "Copy":
		case "Paste":
			var cmdFailed = false;

			// Try executing command
			eval('try {this.getDoc().execCommand(command, user_interface, value);} catch (e) {cmdFailed = true;}');

			if (tinyMCE.isOpera && cmdFailed)
				alert('Currently not supported by your browser, use keyboard shortcuts instead.');

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
			value = tinyMCE.storeAwayURLs(value);
			//value = tinyMCE._customCleanup(this, "insert_to_editor", value);
			tinyMCE._setHTML(doc, value);
			tinyMCE.setInnerHTML(doc.body, tinyMCE._cleanupHTML(this, doc, tinyMCE.settings, doc.body));
			this.convertAllRelativeURLs();
			tinyMCE.handleVisualAid(doc.body, true, this.visualAid, this);
			tinyMCE._setEventsEnabled(doc.body, false);
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
                onclick = tinyMCE.getAttrib(tinyMCE.linkElement, 'onclick');
				style_class = tinyMCE.getAttrib(tinyMCE.linkElement, 'class');

				// Try old onclick to if copy/pasted content
				if (onclick == "")
					onclick = tinyMCE.getAttrib(tinyMCE.linkElement, 'onclick');

				onclick = tinyMCE.cleanupEventStr(onclick);

				href = eval(tinyMCE.settings['urlconverter_callback'] + "(href, tinyMCE.linkElement, true);");

				// Use mce_href if defined
				mceRealHref = tinyMCE.getAttrib(tinyMCE.linkElement, 'mce_href');
				if (mceRealHref != "") {
					href = mceRealHref;

					if (tinyMCE.getParam('convert_urls'))
						href = eval(tinyMCE.settings['urlconverter_callback'] + "(href, tinyMCE.linkElement, true);");
				}

				action = "update";
			}

			if (this.settings['insertlink_callback']) {
				var returnVal = eval(this.settings['insertlink_callback'] + "(href, target, title, onclick, action, style_class);");
				if (returnVal && returnVal['href'])
					tinyMCE.insertLink(returnVal['href'], returnVal['target'], returnVal['title'], returnVal['onclick'], returnVal['style_class']);
			} else {
				tinyMCE.openWindow(this.insertLinkTemplate, {href : href, target : target, title : title, onclick : onclick, action : action, className : style_class, inline : "yes"});
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

				src = eval(tinyMCE.settings['urlconverter_callback'] + "(src, img, true);");

				// Use mce_src if defined
				mceRealSrc = tinyMCE.getAttrib(img, 'mce_src');
				if (mceRealSrc != "") {
					src = mceRealSrc;

					if (tinyMCE.getParam('convert_urls'))
						src = eval(tinyMCE.settings['urlconverter_callback'] + "(src, img, true);");
				}

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
				tinyMCE.openWindow(this.insertImageTemplate, {src : src, alt : alt, border : border, hspace : hspace, vspace : vspace, width : width, height : height, align : align, title : title, onmouseover : onmouseover, onmouseout : onmouseout, action : action, inline : "yes"});
		break;

		case "mceCleanup":
			tinyMCE._setHTML(this.contentDocument, this.getBody().innerHTML);
			tinyMCE.setInnerHTML(this.getBody(), tinyMCE._cleanupHTML(this, this.contentDocument, this.settings, this.getBody(), this.visualAid));
			this.convertAllRelativeURLs();
			tinyMCE.handleVisualAid(this.getBody(), true, this.visualAid, this);
			tinyMCE._setEventsEnabled(this.getBody(), false);
			this.repaint();
			tinyMCE.triggerNodeChange();
		break;

		case "mceReplaceContent":
			this.getWin().focus();

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
			this.execCommand("SetStyleInfo", false, {command : "setattrib", name : "class", value : value});
		break;

		case "mceInsertRawHTML":
			var key = 'tiny_mce_marker';

			this.execCommand('mceBeginUndoLevel');

			// Insert marker key
			this.execCommand('mceInsertContent', false, key);

			// Store away scroll pos
			var scrollX = this.getDoc().body.scrollLeft + this.getDoc().documentElement.scrollLeft;
			var scrollY = this.getDoc().body.scrollTop + this.getDoc().documentElement.scrollTop;

			// Find marker and replace with RAW HTML
			var html = this.getBody().innerHTML;
			if ((pos = html.indexOf(key)) != -1)
				tinyMCE.setInnerHTML(this.getBody(), html.substring(0, pos) + value + html.substring(pos + key.length));

			// Restore scoll pos
			this.contentWindow.scrollTo(scrollX, scrollY);

			this.execCommand('mceEndUndoLevel');

			break;

		case "mceInsertContent":
			var insertHTMLFailed = false;
			this.getWin().focus();
/* WP
			if (tinyMCE.isGecko || tinyMCE.isOpera) {
				try {
					// Is plain text or HTML
					if (value.indexOf('<') == -1) {
						var r = this.getRng();
						var n = this.getDoc().createTextNode(tinyMCE.entityDecode(value));
						var s = this.getSel();
						var r2 = r.cloneRange();

						// Insert text at cursor position
						s.removeAllRanges();
						r.deleteContents();
						r.insertNode(n);

						// Move the cursor to the end of text
						r2.selectNode(n);
						r2.collapse(false);
						s.removeAllRanges();
						s.addRange(r2);
					} else {
						value = tinyMCE.fixGeckoBaseHREFBug(1, this.getDoc(), value);
						this.getDoc().execCommand('inserthtml', false, value);
						tinyMCE.fixGeckoBaseHREFBug(2, this.getDoc(), value);
					}
				} catch (ex) {
					insertHTMLFailed = true;
				}

				if (!insertHTMLFailed) {
					tinyMCE.triggerNodeChange();
					return;
				}
			}
*/
			// Ugly hack in Opera due to non working "inserthtml"
			if (tinyMCE.isOpera && insertHTMLFailed) {
				this.getDoc().execCommand("insertimage", false, tinyMCE.uniqueURL);
				var ar = tinyMCE.getElementsByAttributeValue(this.getBody(), "img", "src", tinyMCE.uniqueURL);
				ar[0].outerHTML = value;
				return;
			}

			if (!tinyMCE.isMSIE) {
				var isHTML = value.indexOf('<') != -1;
				var sel = this.getSel();
				var rng = this.getRng();

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
				var c = value.indexOf('<!--') != -1;

				// Fix comment bug, add tag before comments
				if (c)
					value = tinyMCE.uniqueTag + value;

				if (rng.item)
					rng.item(0).outerHTML = value;
				else
					rng.pasteHTML(value);

				// Remove unique tag
				if (c) {
					var e = this.getDoc().getElementById('mceTMPElement');
					e.parentNode.removeChild(e);
				}
			}

			tinyMCE.triggerNodeChange();
		break;

		case "mceStartTyping":
			if (tinyMCE.settings['custom_undo_redo'] && this.typingUndoIndex == -1) {
				this.typingUndoIndex = this.undoIndex;
				this.execCommand('mceAddUndoLevel');
				//tinyMCE.debug("mceStartTyping");
			}
			break;

		case "mceEndTyping":
			if (tinyMCE.settings['custom_undo_redo'] && this.typingUndoIndex != -1) {
				this.execCommand('mceAddUndoLevel');
				this.typingUndoIndex = -1;
				//tinyMCE.debug("mceEndTyping");
			}
			break;

		case "mceBeginUndoLevel":
			this.undoRedo = false;
			break;

		case "mceEndUndoLevel":
			this.undoRedo = true;
			this.execCommand('mceAddUndoLevel');
			break;

		case "mceAddUndoLevel":
			if (tinyMCE.settings['custom_undo_redo'] && this.undoRedo) {
				// tinyMCE.debug("add level");

				if (this.typingUndoIndex != -1) {
					this.undoIndex = this.typingUndoIndex;
					// tinyMCE.debug("Override: " + this.undoIndex);
				}

				var newHTML = tinyMCE.trim(this.getBody().innerHTML);
				if (newHTML != this.undoLevels[this.undoIndex]) {
					tinyMCE.executeCallback('onchange_callback', '_onchange', 0, this);

					// Time to compress
					var customUndoLevels = tinyMCE.settings['custom_undo_redo_levels'];
					if (customUndoLevels != -1 && this.undoLevels.length > customUndoLevels) {
						for (var i=0; i<this.undoLevels.length-1; i++) {
							//tinyMCE.debug(this.undoLevels[i] + "=" + this.undoLevels[i+1]);
							this.undoLevels[i] = this.undoLevels[i+1];
						}

						this.undoLevels.length--;
						this.undoIndex--;
					}

					this.undoIndex++;
					this.undoLevels[this.undoIndex] = newHTML;
					this.undoLevels.length = this.undoIndex + 1;

					// tinyMCE.debug("level added" + this.undoIndex);
					tinyMCE.triggerNodeChange(false);

					// tinyMCE.debug(this.undoIndex + "," + (this.undoLevels.length-1));
				}
			}
			break;

		case "Undo":
			if (tinyMCE.settings['custom_undo_redo']) {
				tinyMCE.execCommand("mceEndTyping");

				// Do undo
				if (this.undoIndex > 0) {
					this.undoIndex--;
					tinyMCE.setInnerHTML(this.getBody(), this.undoLevels[this.undoIndex]);
					this.repaint();
				}

				// tinyMCE.debug("Undo - undo levels:" + this.undoLevels.length + ", undo index: " + this.undoIndex);
				tinyMCE.triggerNodeChange();
			} else
				this.getDoc().execCommand(command, user_interface, value);
			break;

		case "Redo":
			if (tinyMCE.settings['custom_undo_redo']) {
				tinyMCE.execCommand("mceEndTyping");

				if (this.undoIndex < (this.undoLevels.length-1)) {
					this.undoIndex++;
					tinyMCE.setInnerHTML(this.getBody(), this.undoLevels[this.undoIndex]);
					this.repaint();
					// tinyMCE.debug("Redo - undo levels:" + this.undoLevels.length + ", undo index: " + this.undoIndex);
				}

				tinyMCE.triggerNodeChange();
			} else
				this.getDoc().execCommand(command, user_interface, value);
			break;

		case "mceToggleVisualAid":
			this.visualAid = !this.visualAid;
			tinyMCE.handleVisualAid(this.getBody(), true, this.visualAid, this);
			tinyMCE.triggerNodeChange();
			break;

		case "Indent":
			this.getDoc().execCommand(command, user_interface, value);
			tinyMCE.triggerNodeChange();
			if (tinyMCE.isMSIE) {
				var n = tinyMCE.getParentElement(this.getFocusElement(), "blockquote");
				do {
					if (n && n.nodeName == "BLOCKQUOTE") {
						n.removeAttribute("dir");
						n.removeAttribute("style");
					}
				} while (n != null && (n = n.parentNode) != null);
			}
			break;

		case "removeformat":
			var text = this.getSelectedText();

			if (tinyMCE.isOpera) {
				this.getDoc().execCommand("RemoveFormat", false, null);
				return;
			}

			if (tinyMCE.isMSIE) {
				try {
					var rng = doc.selection.createRange();
					rng.execCommand("RemoveFormat", false, null);
				} catch (e) {
					// Do nothing
				}

				this.execCommand("SetStyleInfo", false, {command : "removeformat"});
			} else {
				this.getDoc().execCommand(command, user_interface, value);

				this.execCommand("SetStyleInfo", false, {command : "removeformat"});
			}

			// Remove class
			if (text.length == 0)
				this.execCommand("mceSetCSSClass", false, "");

			tinyMCE.triggerNodeChange();
			break;

		default:
			this.getDoc().execCommand(command, user_interface, value);

			if (tinyMCE.isGecko)
				window.setTimeout('tinyMCE.triggerNodeChange(false);', 1);
			else
				tinyMCE.triggerNodeChange();
	}

	// Add undo level after modification
	if (command != "mceAddUndoLevel" && command != "Undo" && command != "Redo" && command != "mceStartTyping" && command != "mceEndTyping")
		tinyMCE.execCommand("mceAddUndoLevel");
};

TinyMCEControl.prototype.queryCommandValue = function(command) {
	try {
		return this.getDoc().queryCommandValue(command);
	} catch (ex) {
		return null;
	}
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
	if (replace_element.nodeName == "TEXTAREA" || replace_element.nodeName == "INPUT")
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

			if (tinyMCE.isGecko)
				tinyMCE.insertAfter(fragment, replace_element);
			else
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

		if (tinyMCE.isOpera) {
			this.contentDocument = this.iframeElement.contentDocument;
			this.contentWindow = this.iframeElement.contentWindow;
			dynamicIFrame = true;
		} else {
			this.contentDocument = tElm.window.document;
			this.contentWindow = tElm.window;
		}

		this.getDoc().designMode = "on";
	}

	// Setup base HTML
	var doc = this.contentDocument;
	if (dynamicIFrame) {
		var html = tinyMCE.getParam('doctype') + '<html><head xmlns="http://www.w3.org/1999/xhtml"><base href="' + tinyMCE.settings['base_href'] + '" /><title>blank_page</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body class="mceContentBody"></body></html>';

		try {
			if (!this.isHidden())
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
	if (tinyMCE.isMSIE && !tinyMCE.isOpera) {
		var doc = this.getDoc();
		var rng = doc.selection.createRange();

//		if (rng.collapse)
//			rng.collapse(true);

		var elm = rng.item ? rng.item(0) : rng.parentElement();
	} else {
		if (this.isHidden())
			return this.getBody();

		var sel = this.getSel();
		var rng = this.getRng();

		var elm = rng.commonAncestorContainer;
		//var elm = (sel && sel.anchorNode) ? sel.anchorNode : null;

		// Handle selection a image or other control like element such as anchors
		if (!rng.collapsed) {
			// Is selection small
			if (rng.startContainer == rng.endContainer) {
				if (rng.startOffset - rng.endOffset < 2) {
					if (rng.startContainer.hasChildNodes())
						elm = rng.startContainer.childNodes[rng.startOffset];
				}
			}
		}

		// Get the element parent of the node
		elm = tinyMCE.getParentElement(elm);

		//if (tinyMCE.selectedElement != null && tinyMCE.selectedElement.nodeName.toLowerCase() == "img")
		//	elm = tinyMCE.selectedElement;
	}

	return elm;
};

// Global instances
var tinyMCE = new TinyMCE();
var tinyMCELang = new Array();
