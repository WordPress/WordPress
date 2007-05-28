
/* file:jscripts/tiny_mce/classes/TinyMCE_Engine.class.js */

function TinyMCE_Engine() {
	var ua;

	this.majorVersion = "2";
	this.minorVersion = "1.1.1";
	this.releaseDate = "2007-05-14";

	this.instances = [];
	this.switchClassCache = [];
	this.windowArgs = [];
	this.loadedFiles = [];
	this.pendingFiles = [];
	this.loadingIndex = 0;
	this.configs = [];
	this.currentConfig = 0;
	this.eventHandlers = [];
	this.log = [];
	this.undoLevels = [];
	this.undoIndex = 0;
	this.typingUndoIndex = -1;
	this.settings = [];

	// Browser check
	ua = navigator.userAgent;
	this.isMSIE = (navigator.appName == "Microsoft Internet Explorer");
	this.isMSIE5 = this.isMSIE && (ua.indexOf('MSIE 5') != -1);
	this.isMSIE5_0 = this.isMSIE && (ua.indexOf('MSIE 5.0') != -1);
	this.isMSIE7 = this.isMSIE && (ua.indexOf('MSIE 7') != -1);
	this.isGecko = ua.indexOf('Gecko') != -1; // Will also be true on Safari
	this.isSafari = ua.indexOf('Safari') != -1;
	this.isOpera = window['opera'] && opera.buildNumber ? true : false;
	this.isMac = ua.indexOf('Mac') != -1;
	this.isNS7 = ua.indexOf('Netscape/7') != -1;
	this.isNS71 = ua.indexOf('Netscape/7.1') != -1;
	this.dialogCounter = 0;
	this.plugins = [];
	this.themes = [];
	this.menus = [];
	this.loadedPlugins = [];
	this.buttonMap = [];
	this.isLoaded = false;

	// Fake MSIE on Opera and if Opera fakes IE, Gecko or Safari cancel those
	if (this.isOpera) {
		this.isMSIE = true;
		this.isGecko = false;
		this.isSafari =  false;
	}

	this.isIE = this.isMSIE;
	this.isRealIE = this.isMSIE && !this.isOpera;

	// TinyMCE editor id instance counter
	this.idCounter = 0;
};

TinyMCE_Engine.prototype = {
	init : function(settings) {
		var theme, nl, baseHREF = "", i, cssPath, entities, h, p, src, elements = [], head;

		// IE 5.0x is no longer supported since 5.5, 6.0 and 7.0 now exists. We can't support old browsers forever, sorry.
		if (this.isMSIE5_0)
			return;

		this.settings = settings;

		// Check if valid browser has execcommand support
		if (typeof(document.execCommand) == 'undefined')
			return;

		// Get script base path
		if (!tinyMCE.baseURL) {
			// Search through head
			head = document.getElementsByTagName('head')[0];

			if (head) {
				for (i=0, nl = head.getElementsByTagName('script'); i<nl.length; i++)
					elements.push(nl[i]);
			}

			// Search through rest of document
			for (i=0, nl = document.getElementsByTagName('script'); i<nl.length; i++)
				elements.push(nl[i]);

			// If base element found, add that infront of baseURL
			nl = document.getElementsByTagName('base');
			for (i=0; i<nl.length; i++) {
				if (nl[i].href)
					baseHREF = nl[i].href;
			}

			for (i=0; i<elements.length; i++) {
				if (elements[i].src && (elements[i].src.indexOf("tiny_mce.js") != -1 || elements[i].src.indexOf("tiny_mce_dev.js") != -1 || elements[i].src.indexOf("tiny_mce_src.js") != -1 || elements[i].src.indexOf("tiny_mce_gzip") != -1)) {
					src = elements[i].src;

					tinyMCE.srcMode = (src.indexOf('_src') != -1 || src.indexOf('_dev') != -1) ? '_src' : '';
					tinyMCE.gzipMode = src.indexOf('_gzip') != -1;
					src = src.substring(0, src.lastIndexOf('/'));

					if (settings.exec_mode == "src" || settings.exec_mode == "normal")
						tinyMCE.srcMode = settings.exec_mode == "src" ? '_src' : '';

					// Force it absolute if page has a base href
					if (baseHREF !== '' && src.indexOf('://') == -1)
						tinyMCE.baseURL = baseHREF + src;
					else
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
		this._def("mode", "none");
		this._def("theme", "advanced");
		this._def("plugins", "", true);
		this._def("language", "en");
		this._def("docs_language", this.settings.language);
		this._def("elements", "");
		this._def("textarea_trigger", "mce_editable");
		this._def("editor_selector", "");
		this._def("editor_deselector", "mceNoEditor");
		this._def("valid_elements", "+a[id|style|rel|rev|charset|hreflang|dir|lang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup],-strong/-b[class|style],-em/-i[class|style],-strike[class|style],-u[class|style],#p[id|style|dir|class|align],-ol[class|style],-ul[class|style],-li[class|style],br,img[id|dir|lang|longdesc|usemap|style|class|src|onmouseover|onmouseout|border|alt=|title|hspace|vspace|width|height|align],-sub[style|class],-sup[style|class],-blockquote[dir|style],-table[border=0|cellspacing|cellpadding|width|height|class|align|summary|style|dir|id|lang|bgcolor|background|bordercolor],-tr[id|lang|dir|class|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor],tbody[id|class],thead[id|class],tfoot[id|class],#td[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor|scope],-th[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|scope],caption[id|lang|dir|class|style],-div[id|dir|class|align|style],-span[style|class|align],-pre[class|align|style],address[class|align|style],-h1[id|style|dir|class|align],-h2[id|style|dir|class|align],-h3[id|style|dir|class|align],-h4[id|style|dir|class|align],-h5[id|style|dir|class|align],-h6[id|style|dir|class|align],hr[class|style],-font[face|size|style|id|class|dir|color],dd[id|class|title|style|dir|lang],dl[id|class|title|style|dir|lang],dt[id|class|title|style|dir|lang],cite[title|id|class|style|dir|lang],abbr[title|id|class|style|dir|lang],acronym[title|id|class|style|dir|lang],del[title|id|class|style|dir|lang|datetime|cite],ins[title|id|class|style|dir|lang|datetime|cite]");
		this._def("extended_valid_elements", "");
		this._def("invalid_elements", "");
		this._def("encoding", "");
		this._def("urlconverter_callback", tinyMCE.getParam("urlconvertor_callback", "TinyMCE_Engine.prototype.convertURL"));
		this._def("save_callback", "");
		this._def("force_br_newlines", false);
		this._def("force_p_newlines", true);
		this._def("add_form_submit_trigger", true);
		this._def("relative_urls", true);
		this._def("remove_script_host", true);
		this._def("focus_alert", true);
		this._def("document_base_url", this.documentURL);
		this._def("visual", true);
		this._def("visual_table_class", "mceVisualAid");
		this._def("setupcontent_callback", "");
		this._def("fix_content_duplication", true);
		this._def("custom_undo_redo", true);
		this._def("custom_undo_redo_levels", -1);
		this._def("custom_undo_redo_keyboard_shortcuts", true);
		this._def("custom_undo_redo_restore_selection", true);
		this._def("custom_undo_redo_global", false);
		this._def("verify_html", true);
		this._def("apply_source_formatting", false);
		this._def("directionality", "ltr");
		this._def("cleanup_on_startup", false);
		this._def("inline_styles", false);
		this._def("convert_newlines_to_brs", false);
		this._def("auto_reset_designmode", true);
		this._def("entities", "39,#39,160,nbsp,161,iexcl,162,cent,163,pound,164,curren,165,yen,166,brvbar,167,sect,168,uml,169,copy,170,ordf,171,laquo,172,not,173,shy,174,reg,175,macr,176,deg,177,plusmn,178,sup2,179,sup3,180,acute,181,micro,182,para,183,middot,184,cedil,185,sup1,186,ordm,187,raquo,188,frac14,189,frac12,190,frac34,191,iquest,192,Agrave,193,Aacute,194,Acirc,195,Atilde,196,Auml,197,Aring,198,AElig,199,Ccedil,200,Egrave,201,Eacute,202,Ecirc,203,Euml,204,Igrave,205,Iacute,206,Icirc,207,Iuml,208,ETH,209,Ntilde,210,Ograve,211,Oacute,212,Ocirc,213,Otilde,214,Ouml,215,times,216,Oslash,217,Ugrave,218,Uacute,219,Ucirc,220,Uuml,221,Yacute,222,THORN,223,szlig,224,agrave,225,aacute,226,acirc,227,atilde,228,auml,229,aring,230,aelig,231,ccedil,232,egrave,233,eacute,234,ecirc,235,euml,236,igrave,237,iacute,238,icirc,239,iuml,240,eth,241,ntilde,242,ograve,243,oacute,244,ocirc,245,otilde,246,ouml,247,divide,248,oslash,249,ugrave,250,uacute,251,ucirc,252,uuml,253,yacute,254,thorn,255,yuml,402,fnof,913,Alpha,914,Beta,915,Gamma,916,Delta,917,Epsilon,918,Zeta,919,Eta,920,Theta,921,Iota,922,Kappa,923,Lambda,924,Mu,925,Nu,926,Xi,927,Omicron,928,Pi,929,Rho,931,Sigma,932,Tau,933,Upsilon,934,Phi,935,Chi,936,Psi,937,Omega,945,alpha,946,beta,947,gamma,948,delta,949,epsilon,950,zeta,951,eta,952,theta,953,iota,954,kappa,955,lambda,956,mu,957,nu,958,xi,959,omicron,960,pi,961,rho,962,sigmaf,963,sigma,964,tau,965,upsilon,966,phi,967,chi,968,psi,969,omega,977,thetasym,978,upsih,982,piv,8226,bull,8230,hellip,8242,prime,8243,Prime,8254,oline,8260,frasl,8472,weierp,8465,image,8476,real,8482,trade,8501,alefsym,8592,larr,8593,uarr,8594,rarr,8595,darr,8596,harr,8629,crarr,8656,lArr,8657,uArr,8658,rArr,8659,dArr,8660,hArr,8704,forall,8706,part,8707,exist,8709,empty,8711,nabla,8712,isin,8713,notin,8715,ni,8719,prod,8721,sum,8722,minus,8727,lowast,8730,radic,8733,prop,8734,infin,8736,ang,8743,and,8744,or,8745,cap,8746,cup,8747,int,8756,there4,8764,sim,8773,cong,8776,asymp,8800,ne,8801,equiv,8804,le,8805,ge,8834,sub,8835,sup,8836,nsub,8838,sube,8839,supe,8853,oplus,8855,otimes,8869,perp,8901,sdot,8968,lceil,8969,rceil,8970,lfloor,8971,rfloor,9001,lang,9002,rang,9674,loz,9824,spades,9827,clubs,9829,hearts,9830,diams,34,quot,38,amp,60,lt,62,gt,338,OElig,339,oelig,352,Scaron,353,scaron,376,Yuml,710,circ,732,tilde,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm,8211,ndash,8212,mdash,8216,lsquo,8217,rsquo,8218,sbquo,8220,ldquo,8221,rdquo,8222,bdquo,8224,dagger,8225,Dagger,8240,permil,8249,lsaquo,8250,rsaquo,8364,euro", true);
		this._def("entity_encoding", "named");
		this._def("cleanup_callback", "");
		this._def("add_unload_trigger", true);
		this._def("ask", false);
		this._def("nowrap", false);
		this._def("auto_resize", false);
		this._def("auto_focus", false);
		this._def("cleanup", true);
		this._def("remove_linebreaks", true);
		this._def("button_tile_map", false);
		this._def("submit_patch", true);
		this._def("browsers", "msie,safari,gecko,opera", true);
		this._def("dialog_type", "window");
		this._def("accessibility_warnings", true);
		this._def("accessibility_focus", true);
		this._def("merge_styles_invalid_parents", "");
		this._def("force_hex_style_colors", true);
		this._def("trim_span_elements", true);
		this._def("convert_fonts_to_spans", false);
		this._def("doctype", '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">');
		this._def("font_size_classes", '');
		this._def("font_size_style_values", 'xx-small,x-small,small,medium,large,x-large,xx-large', true);
		this._def("event_elements", 'a,img', true);
		this._def("convert_urls", true);
		this._def("table_inline_editing", false);
		this._def("object_resizing", true);
		this._def("custom_shortcuts", true);
		this._def("convert_on_click", false);
		this._def("content_css", '');
		this._def("fix_list_elements", true);
		this._def("fix_table_elements", false);
		this._def("strict_loading_mode", document.contentType == 'application/xhtml+xml');
		this._def("hidden_tab_class", '');
		this._def("display_tab_class", '');
		this._def("gecko_spellcheck", false);
		this._def("hide_selects_on_submit", true);
		this._def("forced_root_block", false);
		this._def("remove_trailing_nbsp", false);

		// Force strict loading mode to false on non Gecko browsers
		if (this.isMSIE && !this.isOpera)
			this.settings.strict_loading_mode = false;

		// Browser check IE
		if (this.isMSIE && this.settings.browsers.indexOf('msie') == -1)
			return;

		// Browser check Gecko
		if (this.isGecko && this.settings.browsers.indexOf('gecko') == -1)
			return;

		// Browser check Safari
		if (this.isSafari && this.settings.browsers.indexOf('safari') == -1)
			return;

		// Browser check Opera
		if (this.isOpera && this.settings.browsers.indexOf('opera') == -1)
			return;

		// If not super absolute make it so
		baseHREF = tinyMCE.settings.document_base_url;
		h = document.location.href;
		p = h.indexOf('://');
		if (p > 0 && document.location.protocol != "file:") {
			p = h.indexOf('/', p + 3);
			h = h.substring(0, p);

			if (baseHREF.indexOf('://') == -1)
				baseHREF = h + baseHREF;

			tinyMCE.settings.document_base_url = baseHREF;
			tinyMCE.settings.document_base_prefix = h;
		}

		// Trim away query part
		if (baseHREF.indexOf('?') != -1)
			baseHREF = baseHREF.substring(0, baseHREF.indexOf('?'));

		this.settings.base_href = baseHREF.substring(0, baseHREF.lastIndexOf('/')) + "/";

		theme = this.settings.theme;
		this.inlineStrict = 'A|BR|SPAN|BDO|MAP|OBJECT|IMG|TT|I|B|BIG|SMALL|EM|STRONG|DFN|CODE|Q|SAMP|KBD|VAR|CITE|ABBR|ACRONYM|SUB|SUP|#text|#comment';
		this.inlineTransitional = 'A|BR|SPAN|BDO|OBJECT|APPLET|IMG|MAP|IFRAME|TT|I|B|U|S|STRIKE|BIG|SMALL|FONT|BASEFONT|EM|STRONG|DFN|CODE|Q|SAMP|KBD|VAR|CITE|ABBR|ACRONYM|SUB|SUP|INPUT|SELECT|TEXTAREA|LABEL|BUTTON|#text|#comment';
		this.blockElms = 'H[1-6]|P|DIV|ADDRESS|PRE|FORM|TABLE|LI|OL|UL|TD|CAPTION|BLOCKQUOTE|CENTER|DL|DT|DD|DIR|FIELDSET|FORM|NOSCRIPT|NOFRAMES|MENU|ISINDEX|SAMP';
		this.blockRegExp = new RegExp("^(" + this.blockElms + ")$", "i");
		this.posKeyCodes = [13,45,36,35,33,34,37,38,39,40];
		this.uniqueURL = 'javascript:void(091039730);'; // Make unique URL non real URL
		this.uniqueTag = '<div id="mceTMPElement" style="display: none">TMP</div>';
		this.callbacks = ['onInit', 'getInfo', 'getEditorTemplate', 'setupContent', 'onChange', 'onPageLoad', 'handleNodeChange', 'initInstance', 'execCommand', 'getControlHTML', 'handleEvent', 'cleanup', 'removeInstance'];

		// Theme url
		this.settings.theme_href = tinyMCE.baseURL + "/themes/" + theme;

		if (!tinyMCE.isIE || tinyMCE.isOpera)
			this.settings.force_br_newlines = false;

		if (tinyMCE.getParam("popups_css", false)) {
			cssPath = tinyMCE.getParam("popups_css", "");

			// Is relative
			if (cssPath.indexOf('://') == -1 && cssPath.charAt(0) != '/')
				this.settings.popups_css = this.documentBasePath + "/" + cssPath;
			else
				this.settings.popups_css = cssPath;
		} else
			this.settings.popups_css = tinyMCE.baseURL + "/themes/" + theme + "/css/editor_popup.css";

		if (tinyMCE.getParam("editor_css", false)) {
			cssPath = tinyMCE.getParam("editor_css", "");

			// Is relative
			if (cssPath.indexOf('://') == -1 && cssPath.charAt(0) != '/')
				this.settings.editor_css = this.documentBasePath + "/" + cssPath;
			else
				this.settings.editor_css = cssPath;
		} else {
			if (this.settings.editor_css !== '')
				this.settings.editor_css = tinyMCE.baseURL + "/themes/" + theme + "/css/editor_ui.css";
		}

		// Only do this once
		if (this.configs.length == 0) {
			if (typeof(TinyMCECompressed) == "undefined") {
				tinyMCE.addEvent(window, "DOMContentLoaded", TinyMCE_Engine.prototype.onLoad);

				if (tinyMCE.isRealIE) {
					if (document.body)
						tinyMCE.addEvent(document.body, "readystatechange", TinyMCE_Engine.prototype.onLoad);
					else
						tinyMCE.addEvent(document, "readystatechange", TinyMCE_Engine.prototype.onLoad);
				}

				tinyMCE.addEvent(window, "load", TinyMCE_Engine.prototype.onLoad);
				tinyMCE._addUnloadEvents();
			}
		}

		this.loadScript(tinyMCE.baseURL + '/themes/' + this.settings.theme + '/editor_template' + tinyMCE.srcMode + '.js');
		this.loadScript(tinyMCE.baseURL + '/langs/' + this.settings.language +  '.js');
		this.loadCSS(this.settings.editor_css);

		// Add plugins
		p = tinyMCE.getParam('plugins', '', true, ',');
		if (p.length > 0) {
			for (i=0; i<p.length; i++) {
				if (p[i].charAt(0) != '-')
					this.loadScript(tinyMCE.baseURL + '/plugins/' + p[i] + '/editor_plugin' + tinyMCE.srcMode + '.js');
			}
		}

		// Setup entities
		if (tinyMCE.getParam('entity_encoding') == 'named') {
			settings.cleanup_entities = [];
			entities = tinyMCE.getParam('entities', '', true, ',');
			for (i=0; i<entities.length; i+=2)
				settings.cleanup_entities['c' + entities[i]] = entities[i+1];
		}

		// Save away this config
		settings.index = this.configs.length;
		this.configs[this.configs.length] = settings;

		// Start loading first one in chain
		this.loadNextScript();

		// Force flicker free CSS backgrounds in IE
		if (this.isIE && !this.isOpera) {
			try {
				document.execCommand('BackgroundImageCache', false, true);
			} catch (e) {
				// Ignore
			}
		}

		// Setup XML encoding regexps
		this.xmlEncodeRe = new RegExp('[<>&"]', 'g');
	},

	_addUnloadEvents : function() {
		var st = tinyMCE.settings.add_unload_trigger;

		if (tinyMCE.isIE) {
			if (st) {
				tinyMCE.addEvent(window, "unload", TinyMCE_Engine.prototype.unloadHandler);
				tinyMCE.addEvent(window.document, "beforeunload", TinyMCE_Engine.prototype.unloadHandler);
			}
		} else {
			if (st)
				tinyMCE.addEvent(window, "unload", function () {tinyMCE.triggerSave(true, true);});
		}
	},

	_def : function(key, def_val, t) {
		var v = tinyMCE.getParam(key, def_val);

		v = t ? v.replace(/\s+/g, "") : v;

		this.settings[key] = v;
	},

	hasPlugin : function(n) {
		return typeof(this.plugins[n]) != "undefined" && this.plugins[n] != null;
	},

	addPlugin : function(n, p) {
		var op = this.plugins[n];

		// Use the previous plugin object base URL used when loading external plugins
		p.baseURL = op ? op.baseURL : tinyMCE.baseURL + "/plugins/" + n;
		this.plugins[n] = p;

		this.loadNextScript();
	},

	setPluginBaseURL : function(n, u) {
		var op = this.plugins[n];

		if (op)
			op.baseURL = u;
		else
			this.plugins[n] = {baseURL : u};
	},

	loadPlugin : function(n, u) {
		u = u.indexOf('.js') != -1 ? u.substring(0, u.lastIndexOf('/')) : u;
		u = u.charAt(u.length-1) == '/' ? u.substring(0, u.length-1) : u;
		this.plugins[n] = {baseURL : u};
		this.loadScript(u + "/editor_plugin" + (tinyMCE.srcMode ? '_src' : '') + ".js");
	},

	hasTheme : function(n) {
		return typeof(this.themes[n]) != "undefined" && this.themes[n] != null;
	},

	addTheme : function(n, t) {
		this.themes[n] = t;

		this.loadNextScript();
	},

	addMenu : function(n, m) {
		this.menus[n] = m;
	},

	hasMenu : function(n) {
		return typeof(this.plugins[n]) != "undefined" && this.plugins[n] != null;
	},

	loadScript : function(url) {
		var i;

		for (i=0; i<this.loadedFiles.length; i++) {
			if (this.loadedFiles[i] == url)
				return;
		}

		if (tinyMCE.settings.strict_loading_mode)
			this.pendingFiles[this.pendingFiles.length] = url;
		else
			document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + url + '"></script>');

		this.loadedFiles[this.loadedFiles.length] = url;
	},

	loadNextScript : function() {
		var d = document, se;

		if (!tinyMCE.settings.strict_loading_mode)
			return;

		if (this.loadingIndex < this.pendingFiles.length) {
			se = d.createElementNS('http://www.w3.org/1999/xhtml', 'script');
			se.setAttribute('language', 'javascript');
			se.setAttribute('type', 'text/javascript');
			se.setAttribute('src', this.pendingFiles[this.loadingIndex++]);

			d.getElementsByTagName("head")[0].appendChild(se);
		} else
			this.loadingIndex = -1; // Done with loading
	},

	loadCSS : function(url) {
		var ar = url.replace(/\s+/, '').split(',');
		var lflen = 0, csslen = 0, skip = false;
		var x = 0, i = 0, nl, le;

		for (x = 0,csslen = ar.length; x<csslen; x++) {
			if (ar[x] != null && ar[x] != 'null' && ar[x].length > 0) {
				/* Make sure it doesn't exist. */
				for (i=0, lflen=this.loadedFiles.length; i<lflen; i++) {
					if (this.loadedFiles[i] == ar[x]) {
						skip = true;
						break;
					}
				}

				if (!skip) {
					if (tinyMCE.settings.strict_loading_mode) {
						nl = document.getElementsByTagName("head");

						le = document.createElement('link');
						le.setAttribute('href', ar[x]);
						le.setAttribute('rel', 'stylesheet');
						le.setAttribute('type', 'text/css');

						nl[0].appendChild(le);			
					} else
						document.write('<link href="' + ar[x] + '" rel="stylesheet" type="text/css" />');

					this.loadedFiles[this.loadedFiles.length] = ar[x];
				}
			}
		}
	},

	importCSS : function(doc, css) {
		var css_ary = css.replace(/\s+/, '').split(',');
		var csslen, elm, headArr, x, css_file;

		for (x = 0, csslen = css_ary.length; x<csslen; x++) {
			css_file = css_ary[x];

			if (css_file != null && css_file != 'null' && css_file.length > 0) {
				// Is relative, make absolute
				if (css_file.indexOf('://') == -1 && css_file.charAt(0) != '/')
					css_file = this.documentBasePath + "/" + css_file;

				if (typeof(doc.createStyleSheet) == "undefined") {
					elm = doc.createElement("link");

					elm.rel = "stylesheet";
					elm.href = css_file;

					if ((headArr = doc.getElementsByTagName("head")) != null && headArr.length > 0)
						headArr[0].appendChild(elm);
				} else
					doc.createStyleSheet(css_file);
			}
		}
	},

	confirmAdd : function(e, settings) {
		var elm = tinyMCE.isIE ? event.srcElement : e.target;
		var elementId = elm.name ? elm.name : elm.id;

		tinyMCE.settings = settings;

		if (tinyMCE.settings.convert_on_click || (!elm.getAttribute('mce_noask') && confirm(tinyMCELang.lang_edit_confirm)))
			tinyMCE.addMCEControl(elm, elementId);

		elm.setAttribute('mce_noask', 'true');
	},

	updateContent : function(form_element_name) {
		var formElement, n, inst, doc;

		// Find MCE instance linked to given form element and copy it's value
		formElement = document.getElementById(form_element_name);
		for (n in tinyMCE.instances) {
			inst = tinyMCE.instances[n];

			if (!tinyMCE.isInstance(inst))
				continue;

			inst.switchSettings();

			if (inst.formElement == formElement) {
				doc = inst.getDoc();

				tinyMCE._setHTML(doc, inst.formElement.value);

				if (!tinyMCE.isIE)
					doc.body.innerHTML = tinyMCE._cleanupHTML(inst, doc, this.settings, doc.body, inst.visualAid);
			}
		}
	},

	addMCEControl : function(replace_element, form_element_name, target_document) {
		var id = "mce_editor_" + tinyMCE.idCounter++;
		var inst = new TinyMCE_Control(tinyMCE.settings);

		inst.editorId = id;
		this.instances[id] = inst;

		inst._onAdd(replace_element, form_element_name, target_document);
	},

	removeInstance : function(ti) {
		var t = [], n, i;

		// Remove from instances
		for (n in tinyMCE.instances) {
			i = tinyMCE.instances[n];

			if (tinyMCE.isInstance(i) && ti != i)
					t[n] = i;
		}

		tinyMCE.instances = t;

		// Remove from global undo/redo
		n = [];
		t = tinyMCE.undoLevels;

		for (i=0; i<t.length; i++) {
			if (t[i] != ti)
				n.push(t[i]);
		}

		tinyMCE.undoLevels = n;
		tinyMCE.undoIndex = n.length;

		// Dispatch remove instance call
		tinyMCE.dispatchCallback(ti, 'remove_instance_callback', 'removeInstance', ti);

		return ti;
	},

	removeMCEControl : function(editor_id) {
		var inst = tinyMCE.getInstanceById(editor_id), h, re, ot, tn;

		if (inst) {
			inst.switchSettings();

			editor_id = inst.editorId;
			h = tinyMCE.getContent(editor_id);

			this.removeInstance(inst);

			tinyMCE.selectedElement = null;
			tinyMCE.selectedInstance = null;

			// Remove element
			re = document.getElementById(editor_id + "_parent");
			ot = inst.oldTargetElement;
			tn = ot.nodeName.toLowerCase();

			if (tn == "textarea" || tn == "input") {
				re.parentNode.removeChild(re);
				ot.style.display = "inline";
				ot.value = h;
			} else {
				ot.innerHTML = h;
				ot.style.display = 'block';
				re.parentNode.insertBefore(ot, re);
				re.parentNode.removeChild(re);
			}
		}
	},

	triggerSave : function(skip_cleanup, skip_callback) {
		var inst, n;

		// Default to false
		if (typeof(skip_cleanup) == "undefined")
			skip_cleanup = false;

		// Default to false
		if (typeof(skip_callback) == "undefined")
			skip_callback = false;

		// Cleanup and set all form fields
		for (n in tinyMCE.instances) {
			inst = tinyMCE.instances[n];

			if (!tinyMCE.isInstance(inst))
				continue;

			inst.triggerSave(skip_cleanup, skip_callback);
		}
	},

	resetForm : function(form_index) {
		var i, inst, n, formObj = document.forms[form_index];

		for (n in tinyMCE.instances) {
			inst = tinyMCE.instances[n];

			if (!tinyMCE.isInstance(inst))
				continue;

			inst.switchSettings();

			for (i=0; i<formObj.elements.length; i++) {
				if (inst.formTargetElementId == formObj.elements[i].name)
					inst.getBody().innerHTML = inst.startContent;
			}
		}
	},

	execInstanceCommand : function(editor_id, command, user_interface, value, focus) {
		var inst = tinyMCE.getInstanceById(editor_id), r;

		if (inst) {
			r = inst.selection.getRng();

			if (typeof(focus) == "undefined")
				focus = true;

			// IE bug lost focus on images in absolute divs Bug #1534575
			if (focus && (!r || !r.item))
				inst.contentWindow.focus();

			// Reset design mode if lost
			inst.autoResetDesignMode();

			this.selectedElement = inst.getFocusElement();
			inst.select();
			tinyMCE.execCommand(command, user_interface, value);

			// Cancel event so it doesn't call onbeforeonunlaod
			if (tinyMCE.isIE && window.event != null)
				tinyMCE.cancelEvent(window.event);
		}
	},

	execCommand : function(command, user_interface, value) {
		var inst = tinyMCE.selectedInstance, n, pe, te;

		// Default input
		user_interface = user_interface ? user_interface : false;
		value = value ? value : null;

		if (inst)
			inst.switchSettings();

		switch (command) {
			case "Undo":
				if (this.getParam('custom_undo_redo_global')) {
					if (this.undoIndex > 0) {
						tinyMCE.nextUndoRedoAction = 'Undo';
						inst = this.undoLevels[--this.undoIndex];
						inst.select();

						if (!tinyMCE.nextUndoRedoInstanceId)
							inst.execCommand('Undo');
					}
				} else
					inst.execCommand('Undo');
				return true;

			case "Redo":
				if (this.getParam('custom_undo_redo_global')) {
					if (this.undoIndex <= this.undoLevels.length - 1) {
						tinyMCE.nextUndoRedoAction = 'Redo';
						inst = this.undoLevels[this.undoIndex++];
						inst.select();

						if (!tinyMCE.nextUndoRedoInstanceId)
							inst.execCommand('Redo');
					}
				} else
					inst.execCommand('Redo');

				return true;

			case 'mceFocus':
				inst = tinyMCE.getInstanceById(value);

				if (inst)
					inst.getWin().focus();
			return;

			case "mceAddControl":
			case "mceAddEditor":
				tinyMCE.addMCEControl(tinyMCE._getElementById(value), value);
				return;

			case "mceAddFrameControl":
				tinyMCE.addMCEControl(tinyMCE._getElementById(value.element, value.document), value.element, value.document);
				return;

			case "mceRemoveControl":
			case "mceRemoveEditor":
				tinyMCE.removeMCEControl(value);
				return;

			case "mceToggleEditor":
				inst = tinyMCE.getInstanceById(value);

				if (inst) {
					pe = document.getElementById(inst.editorId + '_parent');
					te = inst.oldTargetElement;

					if (typeof(inst.enabled) == 'undefined')
						inst.enabled = true;

					inst.enabled = !inst.enabled;

					if (!inst.enabled) {
						pe.style.display = 'none';

						if (te.nodeName == 'TEXTAREA' || te.nodeName == 'INPUT')
							te.value = inst.getHTML();
						else
							te.innerHTML = inst.getHTML();

						te.style.display = inst.oldTargetDisplay;
						tinyMCE.dispatchCallback(inst, 'hide_instance_callback', 'hideInstance', inst);
					} else {
						pe.style.display = 'block';
						te.style.display = 'none';

						if (te.nodeName == 'TEXTAREA' || te.nodeName == 'INPUT')
							inst.setHTML(te.value);
						else
							inst.setHTML(te.innerHTML);

						inst.useCSS = false;
						tinyMCE.dispatchCallback(inst, 'show_instance_callback', 'showInstance', inst);
					}
				} else
					tinyMCE.addMCEControl(tinyMCE._getElementById(value), value);

				return;

			case "mceResetDesignMode":
				// Resets the designmode state of the editors in Gecko
				if (tinyMCE.isGecko) {
					for (n in tinyMCE.instances) {
						if (!tinyMCE.isInstance(tinyMCE.instances[n]))
							continue;

						try {
							tinyMCE.instances[n].getDoc().designMode = "off";
							tinyMCE.instances[n].getDoc().designMode = "on";
							tinyMCE.instances[n].useCSS = false;
						} catch (e) {
							// Ignore any errors
						}
					}
				}

				return;
		}

		if (inst) {
			inst.execCommand(command, user_interface, value);
		} else if (tinyMCE.settings.focus_alert)
			alert(tinyMCELang.lang_focus_alert);
	},

	_createIFrame : function(replace_element, doc, win) {
		var iframe, id = replace_element.getAttribute("id");
		var aw, ah;

		if (typeof(doc) == "undefined")
			doc = document;

		if (typeof(win) == "undefined")
			win = window;

		iframe = doc.createElement("iframe");

		aw = "" + tinyMCE.settings.area_width;
		ah = "" + tinyMCE.settings.area_height;

		if (aw.indexOf('%') == -1) {
			aw = parseInt(aw);
			aw = (isNaN(aw) || aw < 0) ? 300 : aw;
			aw = aw + "px";
		}

		if (ah.indexOf('%') == -1) {
			ah = parseInt(ah);
			ah = (isNaN(ah) || ah < 0) ? 240 : ah;
			ah = ah + "px";
		}

		iframe.setAttribute("id", id);
		iframe.setAttribute("name", id);
		iframe.setAttribute("class", "mceEditorIframe");
		iframe.setAttribute("border", "0");
		iframe.setAttribute("frameBorder", "0");
		iframe.setAttribute("marginWidth", "0");
		iframe.setAttribute("marginHeight", "0");
		iframe.setAttribute("leftMargin", "0");
		iframe.setAttribute("topMargin", "0");
		iframe.setAttribute("width", aw);
		iframe.setAttribute("height", ah);
		iframe.setAttribute("allowtransparency", "true");
		iframe.className = 'mceEditorIframe';

		if (tinyMCE.settings.auto_resize)
			iframe.setAttribute("scrolling", "no");

		// Must have a src element in MSIE HTTPs breaks aswell as absoute URLs
		if (tinyMCE.isRealIE)
			iframe.setAttribute("src", this.settings.default_document);

		iframe.style.width = aw;
		iframe.style.height = ah;

		// Ugly hack for Gecko problem in strict mode
		if (tinyMCE.settings.strict_loading_mode)
			iframe.style.marginBottom = '-5px';

		// MSIE 5.0 issue
		if (tinyMCE.isRealIE)
			replace_element.outerHTML = iframe.outerHTML;
		else
			replace_element.parentNode.replaceChild(iframe, replace_element);

		if (tinyMCE.isRealIE)
			return win.frames[id];
		else
			return iframe;
	},

	setupContent : function(editor_id) {
		var inst = tinyMCE.instances[editor_id], i, doc = inst.getDoc(), head = doc.getElementsByTagName('head').item(0);
		var content = inst.startContent, contentElement, body;

		// HTML values get XML encoded in strict mode
		if (tinyMCE.settings.strict_loading_mode) {
			content = content.replace(/&lt;/g, '<');
			content = content.replace(/&gt;/g, '>');
			content = content.replace(/&quot;/g, '"');
			content = content.replace(/&amp;/g, '&');
		}

		tinyMCE.selectedInstance = inst;
		inst.switchSettings();

		// Not loaded correctly hit it again, Mozilla bug #997860
		if (!tinyMCE.isIE && tinyMCE.getParam("setupcontent_reload", false) && doc.title != "blank_page") {
			// This part will remove the designMode status
			// Failes first time in Firefox 1.5b2 on Mac
			try {doc.location.href = tinyMCE.baseURL + "/blank.htm";} catch (ex) {}
			window.setTimeout("tinyMCE.setupContent('" + editor_id + "');", 1000);
			return;
		}

		// Wait for it to load
		if (!head || !doc.body) {
			window.setTimeout("tinyMCE.setupContent('" + editor_id + "');", 10);
			return;
		}

		// Import theme specific content CSS the user specific
		tinyMCE.importCSS(inst.getDoc(), tinyMCE.baseURL + "/themes/" + inst.settings.theme + "/css/editor_content.css");
		tinyMCE.importCSS(inst.getDoc(), inst.settings.content_css);
		tinyMCE.dispatchCallback(inst, 'init_instance_callback', 'initInstance', inst);

		// Setup keyboard shortcuts
		if (tinyMCE.getParam('custom_undo_redo_keyboard_shortcuts')) {
			inst.addShortcut('ctrl', 'z', 'lang_undo_desc', 'Undo');
			inst.addShortcut('ctrl', 'y', 'lang_redo_desc', 'Redo');
		}

		// BlockFormat shortcuts keys
		for (i=1; i<=6; i++)
			inst.addShortcut('ctrl', '' + i, '', 'FormatBlock', false, '<h' + i + '>');

		inst.addShortcut('ctrl', '7', '', 'FormatBlock', false, '<p>');
		inst.addShortcut('ctrl', '8', '', 'FormatBlock', false, '<div>');
		inst.addShortcut('ctrl', '9', '', 'FormatBlock', false, '<address>');

		// Add default shortcuts for gecko
		if (tinyMCE.isGecko) {
			inst.addShortcut('ctrl', 'b', 'lang_bold_desc', 'Bold');
			inst.addShortcut('ctrl', 'i', 'lang_italic_desc', 'Italic');
			inst.addShortcut('ctrl', 'u', 'lang_underline_desc', 'Underline');
		}

		// Setup span styles
		if (tinyMCE.getParam("convert_fonts_to_spans"))
			inst.getBody().setAttribute('id', 'mceSpanFonts');

		if (tinyMCE.settings.nowrap)
			doc.body.style.whiteSpace = "nowrap";

		doc.body.dir = this.settings.directionality;
		doc.editorId = editor_id;

		// Add on document element in Mozilla
		if (!tinyMCE.isIE)
			doc.documentElement.editorId = editor_id;

		inst.setBaseHREF(tinyMCE.settings.base_href);

		// Replace new line characters to BRs
		if (tinyMCE.settings.convert_newlines_to_brs) {
			content = tinyMCE.regexpReplace(content, "\r\n", "<br />", "gi");
			content = tinyMCE.regexpReplace(content, "\r", "<br />", "gi");
			content = tinyMCE.regexpReplace(content, "\n", "<br />", "gi");
		}

		// Open closed anchors
	//	content = content.replace(new RegExp('<a(.*?)/>', 'gi'), '<a$1></a>');

		// Call custom cleanup code
		content = tinyMCE.storeAwayURLs(content);
		content = tinyMCE._customCleanup(inst, "insert_to_editor", content);

		if (tinyMCE.isIE) {
			// Ugly!!!
			window.setInterval('try{tinyMCE.getCSSClasses(tinyMCE.instances["' + editor_id + '"].getDoc(), "' + editor_id + '");}catch(e){}', 500);

			if (tinyMCE.settings.force_br_newlines)
				doc.styleSheets[0].addRule("p", "margin: 0;");

			body = inst.getBody();
			body.editorId = editor_id;
		}

		content = tinyMCE.cleanupHTMLCode(content);

		// Fix for bug #958637
		if (!tinyMCE.isIE) {
			contentElement = inst.getDoc().createElement("body");
			doc = inst.getDoc();

			contentElement.innerHTML = content;

			if (tinyMCE.settings.cleanup_on_startup)
				tinyMCE.setInnerHTML(inst.getBody(), tinyMCE._cleanupHTML(inst, doc, this.settings, contentElement));
			else
				tinyMCE.setInnerHTML(inst.getBody(), content);

			tinyMCE.convertAllRelativeURLs(inst.getBody());
		} else {
			if (tinyMCE.settings.cleanup_on_startup) {
				tinyMCE._setHTML(inst.getDoc(), content);

				// Produces permission denied error in MSIE 5.5
				try {
					tinyMCE.setInnerHTML(inst.getBody(), tinyMCE._cleanupHTML(inst, inst.contentDocument, this.settings, inst.getBody()));
				} catch(e) {
					// Ignore
				}
			} else
				tinyMCE._setHTML(inst.getDoc(), content);
		}

		// Fix for bug #957681
		//inst.getDoc().designMode = inst.getDoc().designMode;

		tinyMCE.handleVisualAid(inst.getBody(), true, tinyMCE.settings.visual, inst);
		tinyMCE.dispatchCallback(inst, 'setupcontent_callback', 'setupContent', editor_id, inst.getBody(), inst.getDoc());

		// Re-add design mode on mozilla
		if (!tinyMCE.isIE)
			tinyMCE.addEventHandlers(inst);

		// Add blur handler
		if (tinyMCE.isIE) {
			tinyMCE.addEvent(inst.getBody(), "blur", TinyMCE_Engine.prototype._eventPatch);
			tinyMCE.addEvent(inst.getBody(), "beforedeactivate", TinyMCE_Engine.prototype._eventPatch); // Bug #1439953

			// Workaround for drag drop/copy paste base href bug
			if (!tinyMCE.isOpera) {
				tinyMCE.addEvent(doc.body, "mousemove", TinyMCE_Engine.prototype.onMouseMove);
				tinyMCE.addEvent(doc.body, "beforepaste", TinyMCE_Engine.prototype._eventPatch);
				tinyMCE.addEvent(doc.body, "drop", TinyMCE_Engine.prototype._eventPatch);
			}
		}

		// Trigger node change, this call locks buttons for tables and so forth
		inst.select();
		tinyMCE.selectedElement = inst.contentWindow.document.body;

		// Call custom DOM cleanup
		tinyMCE._customCleanup(inst, "insert_to_editor_dom", inst.getBody());
		tinyMCE._customCleanup(inst, "setup_content_dom", inst.getBody());
		tinyMCE._setEventsEnabled(inst.getBody(), false);
		tinyMCE.cleanupAnchors(inst.getDoc());

		if (tinyMCE.getParam("convert_fonts_to_spans"))
			tinyMCE.convertSpansToFonts(inst.getDoc());

		inst.startContent = tinyMCE.trim(inst.getBody().innerHTML);
		inst.undoRedo.add({ content : inst.startContent });

		// Cleanup any mess left from storyAwayURLs
		if (tinyMCE.isGecko) {
			// Remove mce_src from textnodes and comments
			tinyMCE.selectNodes(inst.getBody(), function(n) {
				if (n.nodeType == 3 || n.nodeType == 8)
					n.nodeValue = n.nodeValue.replace(new RegExp('\\s(mce_src|mce_href)=\"[^\"]*\"', 'gi'), "");

				return false;
			});
		}

		// Remove Gecko spellchecking
		if (tinyMCE.isGecko)
			inst.getBody().spellcheck = tinyMCE.getParam("gecko_spellcheck");

		// Cleanup any mess left from storyAwayURLs
		tinyMCE._removeInternal(inst.getBody());

		inst.select();
		tinyMCE.triggerNodeChange(false, true);
	},

	storeAwayURLs : function(s) {
		// Remove all mce_src, mce_href and replace them with new ones
		// s = s.replace(new RegExp('mce_src\\s*=\\s*\"[^ >\"]*\"', 'gi'), '');
		// s = s.replace(new RegExp('mce_href\\s*=\\s*\"[^ >\"]*\"', 'gi'), '');

		if (!s.match(/(mce_src|mce_href)/gi, s)) {
			s = s.replace(new RegExp('src\\s*=\\s*\"([^ >\"]*)\"', 'gi'), 'src="$1" mce_src="$1"');
			s = s.replace(new RegExp('href\\s*=\\s*\"([^ >\"]*)\"', 'gi'), 'href="$1" mce_href="$1"');
		}

		return s;
	},

	_removeInternal : function(n) {
		if (tinyMCE.isGecko) {
			// Remove mce_src from textnodes and comments
			tinyMCE.selectNodes(n, function(n) {
				if (n.nodeType == 3 || n.nodeType == 8)
					n.nodeValue = n.nodeValue.replace(new RegExp('\\s(mce_src|mce_href)=\"[^\"]*\"', 'gi'), "");

				return false;
			});
		}
	},

	removeTinyMCEFormElements : function(form_obj) {
		var i, elementId;

		// Skip form element removal
		if (!tinyMCE.getParam('hide_selects_on_submit'))
			return;

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
		for (i=0; i<form_obj.elements.length; i++) {
			elementId = form_obj.elements[i].name ? form_obj.elements[i].name : form_obj.elements[i].id;

			if (elementId.indexOf('mce_editor_') == 0)
				form_obj.elements[i].disabled = true;
		}
	},

	handleEvent : function(e) {
		var inst = tinyMCE.selectedInstance, i, elm, keys;

		// Remove odd, error
		if (typeof(tinyMCE) == "undefined")
			return true;

		//tinyMCE.debug(e.type + " " + e.target.nodeName + " " + (e.relatedTarget ? e.relatedTarget.nodeName : ""));

		if (tinyMCE.executeCallback(tinyMCE.selectedInstance, 'handle_event_callback', 'handleEvent', e))
			return false;

		switch (e.type) {
			case "beforedeactivate": // Was added due to bug #1439953
			case "blur":
				if (tinyMCE.selectedInstance)
					tinyMCE.selectedInstance.execCommand('mceEndTyping');

				tinyMCE.hideMenus();

				return;

			// Workaround for drag drop/copy paste base href bug
			case "drop":
			case "beforepaste":
				if (tinyMCE.selectedInstance)
					tinyMCE.selectedInstance.setBaseHREF(null);

				// Fixes odd MSIE bug where drag/droping elements in a iframe with height 100% breaks
				// This logic forces the width/height to be in pixels while the user is drag/dropping
				if (tinyMCE.isRealIE) {
					var ife = tinyMCE.selectedInstance.iframeElement;

					/*if (ife.style.width.indexOf('%') != -1) {
						ife._oldWidth = ife.width.height;
						ife.style.width = ife.clientWidth;
					}*/

					if (ife.style.height.indexOf('%') != -1) {
						ife._oldHeight = ife.style.height;
						ife.style.height = ife.clientHeight;
					}
				}

				window.setTimeout("tinyMCE.selectedInstance.setBaseHREF(tinyMCE.settings.base_href);tinyMCE._resetIframeHeight();", 1);
				return;

			case "submit":
				tinyMCE.formSubmit(tinyMCE.isMSIE ? window.event.srcElement : e.target);
				return;

			case "reset":
				var formObj = tinyMCE.isIE ? window.event.srcElement : e.target;

				for (i=0; i<document.forms.length; i++) {
					if (document.forms[i] == formObj)
						window.setTimeout('tinyMCE.resetForm(' + i + ');', 10);
				}

				return;

			case "keypress":
				if (inst && inst.handleShortcut(e))
					return false;

				if (e.target.editorId) {
					tinyMCE.instances[e.target.editorId].select();
				} else {
					if (e.target.ownerDocument.editorId)
						tinyMCE.instances[e.target.ownerDocument.editorId].select();
				}

				if (tinyMCE.selectedInstance)
					tinyMCE.selectedInstance.switchSettings();

				// Insert P element
				if ((tinyMCE.isGecko || tinyMCE.isOpera || tinyMCE.isSafari) && tinyMCE.settings.force_p_newlines && e.keyCode == 13 && !e.shiftKey) {
					// Insert P element instead of BR
					if (TinyMCE_ForceParagraphs._insertPara(tinyMCE.selectedInstance, e)) {
						// Cancel event
						tinyMCE.execCommand("mceAddUndoLevel");
						return tinyMCE.cancelEvent(e);
					}
				}

				// Handle backspace
				if ((tinyMCE.isGecko && !tinyMCE.isSafari) && tinyMCE.settings.force_p_newlines && (e.keyCode == 8 || e.keyCode == 46) && !e.shiftKey) {
					// Insert P element instead of BR
					if (TinyMCE_ForceParagraphs._handleBackSpace(tinyMCE.selectedInstance, e.type)) {
						// Cancel event
						tinyMCE.execCommand("mceAddUndoLevel");
						return tinyMCE.cancelEvent(e);
					}
				}

				// Return key pressed
				if (tinyMCE.isIE && tinyMCE.settings.force_br_newlines && e.keyCode == 13) {
					if (e.target.editorId)
						tinyMCE.instances[e.target.editorId].select();

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

			case "keyup":
			case "keydown":
				tinyMCE.hideMenus();
				tinyMCE.hasMouseMoved = false;

				if (inst && inst.handleShortcut(e))
					return false;

				inst._fixRootBlocks();

				if (inst.settings.remove_trailing_nbsp)
					inst._fixTrailingNbsp();

				if (e.target.editorId)
					tinyMCE.instances[e.target.editorId].select();

				if (tinyMCE.selectedInstance)
					tinyMCE.selectedInstance.switchSettings();

				inst = tinyMCE.selectedInstance;

				// Handle backspace
				if (tinyMCE.isGecko && tinyMCE.settings.force_p_newlines && (e.keyCode == 8 || e.keyCode == 46) && !e.shiftKey) {
					// Insert P element instead of BR
					if (TinyMCE_ForceParagraphs._handleBackSpace(tinyMCE.selectedInstance, e.type)) {
						// Cancel event
						tinyMCE.execCommand("mceAddUndoLevel");
						e.preventDefault();
						return false;
					}
				}

				tinyMCE.selectedElement = null;
				tinyMCE.selectedNode = null;
				elm = tinyMCE.selectedInstance.getFocusElement();
				tinyMCE.linkElement = tinyMCE.getParentElement(elm, "a");
				tinyMCE.imgElement = tinyMCE.getParentElement(elm, "img");
				tinyMCE.selectedElement = elm;

				// Update visualaids on tabs
				if (tinyMCE.isGecko && e.type == "keyup" && e.keyCode == 9)
					tinyMCE.handleVisualAid(tinyMCE.selectedInstance.getBody(), true, tinyMCE.settings.visual, tinyMCE.selectedInstance);

				// Fix empty elements on return/enter, check where enter occured
				if (tinyMCE.isIE && e.type == "keydown" && e.keyCode == 13)
					tinyMCE.enterKeyElement = tinyMCE.selectedInstance.getFocusElement();

				// Fix empty elements on return/enter
				if (tinyMCE.isIE && e.type == "keyup" && e.keyCode == 13) {
					elm = tinyMCE.enterKeyElement;
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
				keys = tinyMCE.posKeyCodes;
				var posKey = false;
				for (i=0; i<keys.length; i++) {
					if (keys[i] == e.keyCode) {
						posKey = true;
						break;
					}
				}

				// MSIE custom key handling
				if (tinyMCE.isIE && tinyMCE.settings.custom_undo_redo) {
					keys = [8, 46]; // Backspace,Delete

					for (i=0; i<keys.length; i++) {
						if (keys[i] == e.keyCode) {
							if (e.type == "keyup")
								tinyMCE.triggerNodeChange(false);
						}
					}
				}

				// If Ctrl key
				if (e.keyCode == 17)
					return true;

				// Handle Undo/Redo when typing content

				if (tinyMCE.isGecko) {
					// Start typing (not a position key or ctrl key, but ctrl+x and ctrl+p is ok)
					if (!posKey && e.type == "keyup" && !e.ctrlKey || (e.ctrlKey && (e.keyCode == 86 || e.keyCode == 88)))
						tinyMCE.execCommand("mceStartTyping");
				} else {
					// IE seems to be working better with this setting
					if (!posKey && e.type == "keyup")
						tinyMCE.execCommand("mceStartTyping");
				}

				// Store undo bookmark
				if (e.type == "keydown" && (posKey || e.ctrlKey) && inst)
					inst.undoBookmark = inst.selection.getBookmark();

				// End typing (position key) or some Ctrl event
				if (e.type == "keyup" && (posKey || e.ctrlKey))
					tinyMCE.execCommand("mceEndTyping");

				if (posKey && e.type == "keyup")
					tinyMCE.triggerNodeChange(false);

				if (tinyMCE.isIE && e.ctrlKey)
					window.setTimeout('tinyMCE.triggerNodeChange(false);', 1);
			break;

			case "mousedown":
			case "mouseup":
			case "click":
			case "dblclick":
			case "focus":
				tinyMCE.hideMenus();

				if (tinyMCE.selectedInstance) {
					tinyMCE.selectedInstance.switchSettings();
					tinyMCE.selectedInstance.isFocused = true;
				}

				// Check instance event trigged on
				var targetBody = tinyMCE.getParentElement(e.target, "html");
				for (var instanceName in tinyMCE.instances) {
					if (!tinyMCE.isInstance(tinyMCE.instances[instanceName]))
						continue;

					inst = tinyMCE.instances[instanceName];

					// Reset design mode if lost (on everything just in case)
					inst.autoResetDesignMode();

					// Use HTML element since users might click outside of body element
					if (inst.getBody().parentNode == targetBody) {
						inst.select();
						tinyMCE.selectedElement = e.target;
						tinyMCE.linkElement = tinyMCE.getParentElement(tinyMCE.selectedElement, "a");
						tinyMCE.imgElement = tinyMCE.getParentElement(tinyMCE.selectedElement, "img");
						break;
					}
				}

				// Add first bookmark location
				if (!tinyMCE.selectedInstance.undoRedo.undoLevels[0].bookmark && (e.type == "mouseup" || e.type == "dblclick"))
					tinyMCE.selectedInstance.undoRedo.undoLevels[0].bookmark = tinyMCE.selectedInstance.selection.getBookmark();

				// Reset selected node
				if (e.type != "focus")
					tinyMCE.selectedNode = null;

				tinyMCE.triggerNodeChange(false);
				tinyMCE.execCommand("mceEndTyping");

				if (e.type == "mouseup")
					tinyMCE.execCommand("mceAddUndoLevel");

				// Just in case
				if (!tinyMCE.selectedInstance && e.target.editorId)
					tinyMCE.instances[e.target.editorId].select();

				return false;
		}
	},

	getButtonHTML : function(id, lang, img, cmd, ui, val) {
		var h = '', m, x, io = '';

		cmd = 'tinyMCE.execInstanceCommand(\'{$editor_id}\',\'' + cmd + '\'';

		if (typeof(ui) != "undefined" && ui != null)
			cmd += ',' + ui;

		if (typeof(val) != "undefined" && val != null)
			cmd += ",'" + val + "'";

		cmd += ');';

		// Patch for IE7 bug with hover out not restoring correctly
		if (tinyMCE.isRealIE)
			io = 'onmouseover="tinyMCE.lastHover = this;"';

		// Use tilemaps when enabled and found and never in MSIE since it loads the tile each time from cache if cahce is disabled
		if (tinyMCE.getParam('button_tile_map') && (!tinyMCE.isIE || tinyMCE.isOpera) && (m = this.buttonMap[id]) != null && (tinyMCE.getParam("language") == "en" || img.indexOf('$lang') == -1)) {
			// Tiled button
			x = 0 - (m * 20) == 0 ? '0' : 0 - (m * 20);
			h += '<a id="{$editor_id}_' + id + '" href="javascript:' + cmd + '" onclick="' + cmd + 'return false;" onmousedown="return false;" ' + io + ' class="mceTiledButton mceButtonNormal" target="_self">';
			h += '<img src="{$themeurl}/images/spacer.gif" style="background-position: ' + x + 'px 0" alt="{$'+lang+'}" title="{$' + lang + '}" />';
			h += '</a>';
		} else {
			// Normal button
			h += '<a id="{$editor_id}_' + id + '" href="javascript:' + cmd + '" onclick="' + cmd + 'return false;" onmousedown="return false;" ' + io + ' class="mceButtonNormal" target="_self">';
			h += '<img src="' + img + '" alt="{$'+lang+'}" title="{$' + lang + '}" />';
			h += '</a>';
		}

		return h;
	},

	getMenuButtonHTML : function(id, lang, img, mcmd, cmd, ui, val) {
		var h = '', m, x;

		mcmd = 'tinyMCE.execInstanceCommand(\'{$editor_id}\',\'' + mcmd + '\');';
		cmd = 'tinyMCE.execInstanceCommand(\'{$editor_id}\',\'' + cmd + '\'';

		if (typeof(ui) != "undefined" && ui != null)
			cmd += ',' + ui;

		if (typeof(val) != "undefined" && val != null)
			cmd += ",'" + val + "'";

		cmd += ');';

		// Use tilemaps when enabled and found and never in MSIE since it loads the tile each time from cache if cahce is disabled
		if (tinyMCE.getParam('button_tile_map') && (!tinyMCE.isIE || tinyMCE.isOpera) && (m = tinyMCE.buttonMap[id]) != null && (tinyMCE.getParam("language") == "en" || img.indexOf('$lang') == -1)) {
			x = 0 - (m * 20) == 0 ? '0' : 0 - (m * 20);

			if (tinyMCE.isRealIE)
				h += '<span id="{$editor_id}_' + id + '" class="mceMenuButton" onmouseover="tinyMCE._menuButtonEvent(\'over\',this);tinyMCE.lastHover = this;" onmouseout="tinyMCE._menuButtonEvent(\'out\',this);">';
			else
				h += '<span id="{$editor_id}_' + id + '" class="mceMenuButton">';

			h += '<a href="javascript:' + cmd + '" onclick="' + cmd + 'return false;" onmousedown="return false;" class="mceTiledButton mceMenuButtonNormal" target="_self">';
			h += '<img src="{$themeurl}/images/spacer.gif" style="width: 20px; height: 20px; background-position: ' + x + 'px 0" title="{$' + lang + '}" /></a>';
			h += '<a href="javascript:' + mcmd + '" onclick="' + mcmd + 'return false;" onmousedown="return false;"><img src="{$themeurl}/images/button_menu.gif" title="{$' + lang + '}" class="mceMenuButton" />';
			h += '</a></span>';
		} else {
			if (tinyMCE.isRealIE)
				h += '<span id="{$editor_id}_' + id + '" dir="ltr" class="mceMenuButton" onmouseover="tinyMCE._menuButtonEvent(\'over\',this);tinyMCE.lastHover = this;" onmouseout="tinyMCE._menuButtonEvent(\'out\',this);">';
			else
				h += '<span id="{$editor_id}_' + id + '" dir="ltr" class="mceMenuButton">';

			h += '<a href="javascript:' + cmd + '" onclick="' + cmd + 'return false;" onmousedown="return false;" class="mceMenuButtonNormal" target="_self">';
			h += '<img src="' + img + '" title="{$' + lang + '}" /></a>';
			h += '<a href="javascript:' + mcmd + '" onclick="' + mcmd + 'return false;" onmousedown="return false;"><img src="{$themeurl}/images/button_menu.gif" title="{$' + lang + '}" class="mceMenuButton" />';
			h += '</a></span>';
		}

		return h;
	},

	_menuButtonEvent : function(e, o) {
		if (o.className == 'mceMenuButtonFocus')
			return;

		if (e == 'over')
			o.className = o.className + ' mceMenuHover';
		else
			o.className = o.className.replace(/\s.*$/, '');
	},

	addButtonMap : function(m) {
		var i, a = m.replace(/\s+/, '').split(',');

		for (i=0; i<a.length; i++)
			this.buttonMap[a[i]] = i;
	},

	formSubmit : function(f, p) {
		var n, inst, found = false;

		if (f.form)
			f = f.form;

		// Is it a form that has a TinyMCE instance
		for (n in tinyMCE.instances) {
			inst = tinyMCE.instances[n];

			if (!tinyMCE.isInstance(inst))
				continue;

			if (inst.formElement) {
				if (f == inst.formElement.form) {
					found = true;
					inst.isNotDirty = true;
				}
			}
		}

		// Is valid
		if (found) {
			tinyMCE.removeTinyMCEFormElements(f);
			tinyMCE.triggerSave();
		}

		// Is it patched
		if (f.mceOldSubmit && p)
			f.mceOldSubmit();
	},

	submitPatch : function() {
		tinyMCE.formSubmit(this, true);
	},

	onLoad : function() {
		var r, i, c, mode, trigger, elements, element, settings, elementId, elm;
		var selector, deselector, elementRefAr, form;

		// Wait for everything to be loaded first
		if (tinyMCE.settings.strict_loading_mode && this.loadingIndex != -1) {
			window.setTimeout('tinyMCE.onLoad();', 1);
			return;
		}

		if (tinyMCE.isRealIE && window.event.type == "readystatechange" && document.readyState != "complete")
			return true;

		if (tinyMCE.isLoaded)
			return true;

		tinyMCE.isLoaded = true;

		// IE produces JS error if TinyMCE is placed in a frame
		// It seems to have something to do with the selection not beeing
		// correctly initialized in IE so this hack solves the problem
		if (tinyMCE.isRealIE && document.body && window.location.href != window.top.location.href) {
			r = document.body.createTextRange();
			r.collapse(true);
			r.select();
		}

		tinyMCE.dispatchCallback(null, 'onpageload', 'onPageLoad');

		for (c=0; c<tinyMCE.configs.length; c++) {
			tinyMCE.settings = tinyMCE.configs[c];

			selector = tinyMCE.getParam("editor_selector");
			deselector = tinyMCE.getParam("editor_deselector");
			elementRefAr = [];

			// Add submit triggers
			if (document.forms && tinyMCE.settings.add_form_submit_trigger && !tinyMCE.submitTriggers) {
				for (i=0; i<document.forms.length; i++) {
					form = document.forms[i];

					tinyMCE.addEvent(form, "submit", TinyMCE_Engine.prototype.handleEvent);
					tinyMCE.addEvent(form, "reset", TinyMCE_Engine.prototype.handleEvent);
					tinyMCE.submitTriggers = true; // Do it only once

					// Patch the form.submit function
					if (tinyMCE.settings.submit_patch) {
						try {
							form.mceOldSubmit = form.submit;
							form.submit = TinyMCE_Engine.prototype.submitPatch;
						} catch (e) {
							// Do nothing
						}
					}
				}
			}

			// Add editor instances based on mode
			mode = tinyMCE.settings.mode;
			switch (mode) {
				case "exact":
					elements = tinyMCE.getParam('elements', '', true, ',');

					for (i=0; i<elements.length; i++) {
						element = tinyMCE._getElementById(elements[i]);
						trigger = element ? element.getAttribute(tinyMCE.settings.textarea_trigger) : "";

						if (new RegExp('\\b' + deselector + '\\b').test(tinyMCE.getAttrib(element, "class")))
							continue;

						if (trigger == "false")
							continue;

						if ((tinyMCE.settings.ask || tinyMCE.settings.convert_on_click) && element) {
							elementRefAr[elementRefAr.length] = element;
							continue;
						}

						if (element)
							tinyMCE.addMCEControl(element, elements[i]);
					}
				break;

				case "specific_textareas":
				case "textareas":
					elements = document.getElementsByTagName("textarea");

					for (i=0; i<elements.length; i++) {
						elm = elements.item(i);
						trigger = elm.getAttribute(tinyMCE.settings.textarea_trigger);

						if (selector !== '' && !new RegExp('\\b' + selector + '\\b').test(tinyMCE.getAttrib(elm, "class")))
							continue;

						if (selector !== '')
							trigger = selector !== '' ? "true" : "";

						if (new RegExp('\\b' + deselector + '\\b').test(tinyMCE.getAttrib(elm, "class")))
							continue;

						if ((mode == "specific_textareas" && trigger == "true") || (mode == "textareas" && trigger != "false"))
							elementRefAr[elementRefAr.length] = elm;
					}
				break;
			}

			for (i=0; i<elementRefAr.length; i++) {
				element = elementRefAr[i];
				elementId = element.name ? element.name : element.id;

				if (tinyMCE.settings.ask || tinyMCE.settings.convert_on_click) {
					// Focus breaks in Mozilla
					if (tinyMCE.isGecko) {
						settings = tinyMCE.settings;

						tinyMCE.addEvent(element, "focus", function (e) {window.setTimeout(function() {TinyMCE_Engine.prototype.confirmAdd(e, settings);}, 10);});

						if (element.nodeName != "TEXTAREA" && element.nodeName != "INPUT")
							tinyMCE.addEvent(element, "click", function (e) {window.setTimeout(function() {TinyMCE_Engine.prototype.confirmAdd(e, settings);}, 10);});
						// tinyMCE.addEvent(element, "mouseover", function (e) {window.setTimeout(function() {TinyMCE_Engine.prototype.confirmAdd(e, settings);}, 10);});
					} else {
						settings = tinyMCE.settings;

						tinyMCE.addEvent(element, "focus", function () { TinyMCE_Engine.prototype.confirmAdd(null, settings); });
						tinyMCE.addEvent(element, "click", function () { TinyMCE_Engine.prototype.confirmAdd(null, settings); });
						// tinyMCE.addEvent(element, "mouseenter", function () { TinyMCE_Engine.prototype.confirmAdd(null, settings); });
					}
				} else
					tinyMCE.addMCEControl(element, elementId);
			}

			// Handle auto focus
			if (tinyMCE.settings.auto_focus) {
				window.setTimeout(function () {
					var inst = tinyMCE.getInstanceById(tinyMCE.settings.auto_focus);
					inst.selection.selectNode(inst.getBody(), true, true);
					inst.contentWindow.focus();
				}, 100);
			}

			tinyMCE.dispatchCallback(null, 'oninit', 'onInit');
		}
	},

	isInstance : function(o) {
		return o != null && typeof(o) == "object" && o.isTinyMCE_Control;
	},

	getParam : function(name, default_value, strip_whitespace, split_chr) {
		var i, outArray, value = (typeof(this.settings[name]) == "undefined") ? default_value : this.settings[name];

		// Fix bool values
		if (value == "true" || value == "false")
			return (value == "true");

		if (strip_whitespace)
			value = tinyMCE.regexpReplace(value, "[ \t\r\n]", "");

		if (typeof(split_chr) != "undefined" && split_chr != null) {
			value = value.split(split_chr);
			outArray = [];

			for (i=0; i<value.length; i++) {
				if (value[i] && value[i] !== '')
					outArray[outArray.length] = value[i];
			}

			value = outArray;
		}

		return value;
	},

	getLang : function(name, default_value, parse_entities, va) {
		var v = (typeof(tinyMCELang[name]) == "undefined") ? default_value : tinyMCELang[name], n;

		if (parse_entities)
			v = tinyMCE.entityDecode(v);

		if (va) {
			for (n in va)
				v = this.replaceVar(v, n, va[n]);
		}

		return v;
	},

	entityDecode : function(s) {
		var e = document.createElement("div");

		e.innerHTML = s;

		return !e.firstChild ? s : e.firstChild.nodeValue;
	},

	addToLang : function(prefix, ar) {
		var k;

		for (k in ar) {
			if (typeof(ar[k]) == 'function')
				continue;

			tinyMCELang[(k.indexOf('lang_') == -1 ? 'lang_' : '') + (prefix !== '' ? (prefix + "_") : '') + k] = ar[k];
		}

		this.loadNextScript();
	},

	triggerNodeChange : function(focus, setup_content) {
		var elm, inst, editorId, undoIndex = -1, undoLevels = -1, doc, anySelection = false, st;

		if (tinyMCE.selectedInstance) {
			inst = tinyMCE.selectedInstance;
			elm = (typeof(setup_content) != "undefined" && setup_content) ? tinyMCE.selectedElement : inst.getFocusElement();

/*			if (elm == inst.lastTriggerEl)
				return;

			inst.lastTriggerEl = elm;*/

			editorId = inst.editorId;
			st = inst.selection.getSelectedText();

			if (tinyMCE.settings.auto_resize)
				inst.resizeToContent();

			if (setup_content && tinyMCE.isGecko && inst.isHidden())
				elm = inst.getBody();

			inst.switchSettings();

			if (tinyMCE.selectedElement)
				anySelection = (tinyMCE.selectedElement.nodeName.toLowerCase() == "img") || (st && st.length > 0);

			if (tinyMCE.settings.custom_undo_redo) {
				undoIndex = inst.undoRedo.undoIndex;
				undoLevels = inst.undoRedo.undoLevels.length;
			}

			tinyMCE.dispatchCallback(inst, 'handle_node_change_callback', 'handleNodeChange', editorId, elm, undoIndex, undoLevels, inst.visualAid, anySelection, setup_content);
		}

		if (this.selectedInstance && (typeof(focus) == "undefined" || focus))
			this.selectedInstance.contentWindow.focus();
	},

	_customCleanup : function(inst, type, content) {
		var pl, po, i, customCleanup;

		// Call custom cleanup
		customCleanup = tinyMCE.settings.cleanup_callback;
		if (customCleanup != '')
			content = tinyMCE.resolveDots(tinyMCE.settings.cleanup_callback, window)(type, content, inst);

		// Trigger theme cleanup
		po = tinyMCE.themes[tinyMCE.settings.theme];
		if (po && po.cleanup)
			content = po.cleanup(type, content, inst);

		// Trigger plugin cleanups
		pl = inst.plugins;
		for (i=0; i<pl.length; i++) {
			po = tinyMCE.plugins[pl[i]];

			if (po && po.cleanup)
				content = po.cleanup(type, content, inst);
		}

		return content;
	},

	setContent : function(h) {
		if (tinyMCE.selectedInstance) {
			tinyMCE.selectedInstance.execCommand('mceSetContent', false, h);
			tinyMCE.selectedInstance.repaint();
		}
	},

	importThemeLanguagePack : function(name) {
		if (typeof(name) == "undefined")
			name = tinyMCE.settings.theme;

		tinyMCE.loadScript(tinyMCE.baseURL + '/themes/' + name + '/langs/' + tinyMCE.settings.language + '.js');
	},

	importPluginLanguagePack : function(name) {
		var b = tinyMCE.baseURL + '/plugins/' + name;

		if (this.plugins[name])
			b = this.plugins[name].baseURL;

		tinyMCE.loadScript(b + '/langs/' + tinyMCE.settings.language +  '.js');
	},

	applyTemplate : function(h, ag) {
		return h.replace(new RegExp('\\{\\$([a-z0-9_]+)\\}', 'gi'), function(m, s) {
			if (s.indexOf('lang_') == 0 && tinyMCELang[s])
				return tinyMCELang[s];

			if (ag && ag[s])
				return ag[s];

			if (tinyMCE.settings[s])
				return tinyMCE.settings[s];

			if (m == 'themeurl')
				return tinyMCE.themeURL;

			return m;
		});
	},

	replaceVar : function(h, r, v) {
		return h.replace(new RegExp('{\\\$' + r + '}', 'g'), v);
	},

	openWindow : function(template, args) {
		var html, width, height, x, y, resizable, scrollbars, url, name, win, modal, features;

		args = !args ? {} : args;

		args.mce_template_file = template.file;
		args.mce_width = template.width;
		args.mce_height = template.height;
		tinyMCE.windowArgs = args;

		html = template.html;
		if (!(width = parseInt(template.width)))
			width = 320;

		if (!(height = parseInt(template.height)))
			height = 200;

		// Add to height in M$ due to SP2 WHY DON'T YOU GUYS IMPLEMENT innerWidth of windows!!
		if (tinyMCE.isIE)
			height += 40;
		else
			height += 20;

		x = parseInt(screen.width / 2.0) - (width / 2.0);
		y = parseInt(screen.height / 2.0) - (height / 2.0);

		resizable = (args && args.resizable) ? args.resizable : "no";
		scrollbars = (args && args.scrollbars) ? args.scrollbars : "no";

		if (template.file.charAt(0) != '/' && template.file.indexOf('://') == -1)
			url = tinyMCE.baseURL + "/themes/" + tinyMCE.getParam("theme") + "/" + template.file;
		else
			url = template.file;

		// Replace all args as variables in URL
		for (name in args) {
			if (typeof(args[name]) == 'function')
				continue;

			url = tinyMCE.replaceVar(url, name, escape(args[name]));
		}

		if (html) {
			html = tinyMCE.replaceVar(html, "css", this.settings.popups_css);
			html = tinyMCE.applyTemplate(html, args);

			win = window.open("", "mcePopup" + new Date().getTime(), "top=" + y + ",left=" + x + ",scrollbars=" + scrollbars + ",dialog=yes,minimizable=" + resizable + ",modal=yes,width=" + width + ",height=" + height + ",resizable=" + resizable);
			if (win == null) {
				alert(tinyMCELang.lang_popup_blocked);
				return;
			}

			win.document.write(html);
			win.document.close();
			win.resizeTo(width, height);
			win.focus();
		} else {
			if ((tinyMCE.isRealIE) && resizable != 'yes' && tinyMCE.settings.dialog_type == "modal") {
				height += 10;

				features = "resizable:" + resizable + ";scroll:" + scrollbars + ";status:yes;center:yes;help:no;dialogWidth:" + width + "px;dialogHeight:" + height + "px;";

				window.showModalDialog(url, window, features);
			} else {
				modal = (resizable == "yes") ? "no" : "yes";

				if (tinyMCE.isGecko && tinyMCE.isMac)
					modal = "no";

				if (template.close_previous != "no")
					try {tinyMCE.lastWindow.close();} catch (ex) {}

				win = window.open(url, "mcePopup" + new Date().getTime(), "top=" + y + ",left=" + x + ",scrollbars=" + scrollbars + ",dialog=" + modal + ",minimizable=" + resizable + ",modal=" + modal + ",width=" + width + ",height=" + height + ",resizable=" + resizable);
				if (win == null) {
					alert(tinyMCELang.lang_popup_blocked);
					return;
				}

				if (template.close_previous != "no")
					tinyMCE.lastWindow = win;

				try {
					win.resizeTo(width, height);
				} catch(e) {
					// Ignore
				}

				// Make it bigger if statusbar is forced
				if (tinyMCE.isGecko) {
					if (win.document.defaultView.statusbar.visible)
						win.resizeBy(0, tinyMCE.isMac ? 10 : 24);
				}

				win.focus();
			}
		}
	},

	closeWindow : function(win) {
		win.close();
	},

	getVisualAidClass : function(class_name, state) {
		var i, classNames, ar, className, aidClass = tinyMCE.settings.visual_table_class;

		if (typeof(state) == "undefined")
			state = tinyMCE.settings.visual;

		// Split
		classNames = [];
		ar = class_name.split(' ');
		for (i=0; i<ar.length; i++) {
			if (ar[i] == aidClass)
				ar[i] = "";

			if (ar[i] !== '')
				classNames[classNames.length] = ar[i];
		}

		if (state)
			classNames[classNames.length] = aidClass;

		// Glue
		className = "";
		for (i=0; i<classNames.length; i++) {
			if (i > 0)
				className += " ";

			className += classNames[i];
		}

		return className;
	},

	handleVisualAid : function(el, deep, state, inst, skip_dispatch) {
		var i, x, y, tableElement, anchorName, oldW, oldH, bo, cn;

		if (!el)
			return;

		if (!skip_dispatch)
			tinyMCE.dispatchCallback(inst, 'handle_visual_aid_callback', 'handleVisualAid', el, deep, state, inst);

		tableElement = null;

		switch (el.nodeName) {
			case "TABLE":
				oldW = el.style.width;
				oldH = el.style.height;
				bo = tinyMCE.getAttrib(el, "border");

				bo = bo == '' || bo == "0" ? true : false;

				tinyMCE.setAttrib(el, "class", tinyMCE.getVisualAidClass(tinyMCE.getAttrib(el, "class"), state && bo));

				el.style.width = oldW;
				el.style.height = oldH;

				for (y=0; y<el.rows.length; y++) {
					for (x=0; x<el.rows[y].cells.length; x++) {
						cn = tinyMCE.getVisualAidClass(tinyMCE.getAttrib(el.rows[y].cells[x], "class"), state && bo);
						tinyMCE.setAttrib(el.rows[y].cells[x], "class", cn);
					}
				}

				break;

			case "A":
				anchorName = tinyMCE.getAttrib(el, "name");

				if (anchorName !== '' && state) {
					el.title = anchorName;
					tinyMCE.addCSSClass(el, 'mceItemAnchor');
				} else if (anchorName !== '' && !state)
					el.className = '';

				break;
		}

		if (deep && el.hasChildNodes()) {
			for (i=0; i<el.childNodes.length; i++)
				tinyMCE.handleVisualAid(el.childNodes[i], deep, state, inst, true);
		}
	},

	fixGeckoBaseHREFBug : function(m, e, h) {
		var xsrc, xhref;

		if (tinyMCE.isGecko) {
			if (m == 1) {
				h = h.replace(/\ssrc=/gi, " mce_tsrc=");
				h = h.replace(/\shref=/gi, " mce_thref=");

				return h;
			} else {
				// Why bother if there is no src or href broken
				if (!new RegExp('(src|href)=', 'g').test(h))
					return h;

				// Restore src and href that gets messed up by Gecko
				tinyMCE.selectElements(e, 'A,IMG,SELECT,AREA,IFRAME,BASE,INPUT,SCRIPT,EMBED,OBJECT,LINK', function (n) {
					xsrc = tinyMCE.getAttrib(n, "mce_tsrc");
					xhref = tinyMCE.getAttrib(n, "mce_thref");

					if (xsrc !== '') {
						try {
							n.src = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings.base_href, xsrc);
						} catch (e) {
							// Ignore, Firefox cast exception if local file wasn't found
						}

						n.removeAttribute("mce_tsrc");
					}

					if (xhref !== '') {
						try {
							n.href = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings.base_href, xhref);
						} catch (e) {
							// Ignore, Firefox cast exception if local file wasn't found
						}

						n.removeAttribute("mce_thref");
					}

					return false;
				});

				// Restore text/comment nodes
				tinyMCE.selectNodes(e, function(n) {
					if (n.nodeType == 3 || n.nodeType == 8) {
						n.nodeValue = n.nodeValue.replace(/\smce_tsrc=/gi, " src=");
						n.nodeValue = n.nodeValue.replace(/\smce_thref=/gi, " href=");
					}

					return false;
				});
			}
		}

		return h;
	},

	_setHTML : function(doc, html_content) {
		var i, html, paras, node;

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
		if (tinyMCE.isIE && tinyMCE.settings.fix_content_duplication) {
			// Remove P elements in P elements
			paras = doc.getElementsByTagName("P");
			for (i=0; i<paras.length; i++) {
				node = paras[i];

				while ((node = node.parentNode) != null) {
					if (node.nodeName == "P")
						node.outerHTML = node.innerHTML;
				}
			}

			// Content duplication bug fix (Seems to be word crap)
			html = doc.body.innerHTML;

			// Always set the htmlText output
			tinyMCE.setInnerHTML(doc.body, html);
		}

		tinyMCE.cleanupAnchors(doc);

		if (tinyMCE.getParam("convert_fonts_to_spans"))
			tinyMCE.convertSpansToFonts(doc);
	},

	getEditorId : function(form_element) {
		var inst = this.getInstanceById(form_element);

		if (!inst)
			return null;

		return inst.editorId;
	},

	getInstanceById : function(editor_id) {
		var inst = this.instances[editor_id], n;

		if (!inst) {
			for (n in tinyMCE.instances) {
				inst = tinyMCE.instances[n];

				if (!tinyMCE.isInstance(inst))
					continue;

				if (inst.formTargetElementId == editor_id)
					return inst;
			}
		} else
			return inst;

		return null;
	},

	queryInstanceCommandValue : function(editor_id, command) {
		var inst = tinyMCE.getInstanceById(editor_id);

		if (inst)
			return inst.queryCommandValue(command);

		return false;
	},

	queryInstanceCommandState : function(editor_id, command) {
		var inst = tinyMCE.getInstanceById(editor_id);

		if (inst)
			return inst.queryCommandState(command);

		return null;
	},

	setWindowArg : function(n, v) {
		this.windowArgs[n] = v;
	},

	getWindowArg : function(n, d) {
		return (typeof(this.windowArgs[n]) == "undefined") ? d : this.windowArgs[n];
	},

	getCSSClasses : function(editor_id, doc) {
		var i, c, x, rule, styles, rules, csses, selectorText, inst = tinyMCE.getInstanceById(editor_id);
		var cssClass, addClass, p;

		if (!inst)
			inst = tinyMCE.selectedInstance;

		if (!inst)
			return [];

		if (!doc)
			doc = inst.getDoc();

		// Is cached, use that
		if (inst && inst.cssClasses.length > 0)
			return inst.cssClasses;

		if (!doc)
			return;

		styles = doc.styleSheets;

		if (styles && styles.length > 0) {
			for (x=0; x<styles.length; x++) {
				csses = null;

				try {
					csses = tinyMCE.isIE ? doc.styleSheets(x).rules : styles[x].cssRules;
				} catch(e) {
					// Just ignore any errors I know this is ugly!!
				}
	
				if (!csses)
					return [];

				for (i=0; i<csses.length; i++) {
					selectorText = csses[i].selectorText;

					// Can be multiple rules per selector
					if (selectorText) {
						rules = selectorText.split(',');
						for (c=0; c<rules.length; c++) {
							rule = rules[c];

							// Strip spaces between selectors
							while (rule.indexOf(' ') == 0)
								rule = rule.substring(1);

							// Invalid rule
							if (rule.indexOf(' ') != -1 || rule.indexOf(':') != -1 || rule.indexOf('mceItem') != -1)
								continue;

							if (rule.indexOf(tinyMCE.settings.visual_table_class) != -1 || rule.indexOf('mceEditable') != -1 || rule.indexOf('mceNonEditable') != -1)
								continue;

							// Is class rule
							if (rule.indexOf('.') != -1) {
								cssClass = rule.substring(rule.indexOf('.') + 1);
								addClass = true;

								for (p=0; p<inst.cssClasses.length && addClass; p++) {
									if (inst.cssClasses[p] == cssClass)
										addClass = false;
								}

								if (addClass)
									inst.cssClasses[inst.cssClasses.length] = cssClass;
							}
						}
					}
				}
			}
		}

		return inst.cssClasses;
	},

	regexpReplace : function(in_str, reg_exp, replace_str, opts) {
		var re;

		if (in_str == null)
			return in_str;

		if (typeof(opts) == "undefined")
			opts = 'g';

		re = new RegExp(reg_exp, opts);

		return in_str.replace(re, replace_str);
	},

	trim : function(s) {
		return s.replace(/^\s*|\s*$/g, "");
	},

	cleanupEventStr : function(s) {
		s = "" + s;
		s = s.replace('function anonymous()\n{\n', '');
		s = s.replace('\n}', '');
		s = s.replace(/^return true;/gi, ''); // Remove event blocker

		return s;
	},

	getControlHTML : function(c) {
		var i, l, n, o, v, rtl = tinyMCE.getLang('lang_dir') == 'rtl';

		l = tinyMCE.plugins;
		for (n in l) {
			o = l[n];

			if (o.getControlHTML && (v = o.getControlHTML(c)) !== '') {
				if (rtl)
					return '<span dir="rtl">' + tinyMCE.replaceVar(v, "pluginurl", o.baseURL) + '</span>';

				return tinyMCE.replaceVar(v, "pluginurl", o.baseURL);
			}
		}

		o = tinyMCE.themes[tinyMCE.settings.theme];
		if (o.getControlHTML && (v = o.getControlHTML(c)) !== '') {
			if (rtl)
				return '<span dir="rtl">' + v + '</span>';

			return v;
		}

		return '';
	},

	evalFunc : function(f, idx, a, o) {
		o = !o ? window : o;
		f = typeof(f) == 'function' ? f : o[f];

		return f.apply(o, Array.prototype.slice.call(a, idx));
	},

	dispatchCallback : function(i, p, n) {
		return this.callFunc(i, p, n, 0, this.dispatchCallback.arguments);
	},

	executeCallback : function(i, p, n) {
		return this.callFunc(i, p, n, 1, this.executeCallback.arguments);
	},

	execCommandCallback : function(i, p, n) {
		return this.callFunc(i, p, n, 2, this.execCommandCallback.arguments);
	},

	callFunc : function(ins, p, n, m, a) {
		var l, i, on, o, s, v;

		s = m == 2;

		l = tinyMCE.getParam(p, '');

		if (l !== '' && (v = tinyMCE.evalFunc(l, 3, a)) == s && m > 0)
			return true;

		if (ins != null) {
			for (i=0, l = ins.plugins; i<l.length; i++) {
				o = tinyMCE.plugins[l[i]];

				if (o[n] && (v = tinyMCE.evalFunc(n, 3, a, o)) == s && m > 0)
					return true;
			}
		}

		l = tinyMCE.themes;
		for (on in l) {
			o = l[on];

			if (o[n] && (v = tinyMCE.evalFunc(n, 3, a, o)) == s && m > 0)
				return true;
		}

		return false;
	},

	resolveDots : function(s, o) {
		var i;

		if (typeof(s) == 'string') {
			for (i=0, s=s.split('.'); i<s.length; i++)
				o = o[s[i]];
		} else
			o = s;

		return o;
	},

	xmlEncode : function(s) {
		return s ? ('' + s).replace(this.xmlEncodeRe, function (c, b) {
			switch (c) {
				case '&':
					return '&amp;';

				case '"':
					return '&quot;';

				case '<':
					return '&lt;';

				case '>':
					return '&gt;';
			}

			return c;
		}) : s;
	},

	add : function(c, m) {
		var n;

		for (n in m)
			c.prototype[n] = m[n];
	},

	extend : function(p, np) {
		var o = {}, n;

		o.parent = p;

		for (n in p)
			o[n] = p[n];

		for (n in np)
			o[n] = np[n];

		return o;
	},

	hideMenus : function() {
		var e = tinyMCE.lastSelectedMenuBtn;

		if (tinyMCE.lastMenu) {
			tinyMCE.lastMenu.hide();
			tinyMCE.lastMenu = null;
		}

		if (e) {
			tinyMCE.switchClass(e, tinyMCE.lastMenuBtnClass);
			tinyMCE.lastSelectedMenuBtn = null;
		}
	}

	};

// Global instances
var TinyMCE = TinyMCE_Engine; // Compatiblity with gzip compressors
var tinyMCE = new TinyMCE_Engine();
var tinyMCELang = {};

/* file:jscripts/tiny_mce/classes/TinyMCE_Control.class.js */

function TinyMCE_Control(settings) {
	var t, i, tos, fu, p, x, fn, fu, pn, s = settings;

	this.undoRedoLevel = true;
	this.isTinyMCE_Control = true;

	// Default settings
	this.enabled = true;
	this.settings = s;
	this.settings.theme = tinyMCE.getParam("theme", "default");
	this.settings.width = tinyMCE.getParam("width", -1);
	this.settings.height = tinyMCE.getParam("height", -1);
	this.selection = new TinyMCE_Selection(this);
	this.undoRedo = new TinyMCE_UndoRedo(this);
	this.cleanup = new TinyMCE_Cleanup();
	this.shortcuts = [];
	this.hasMouseMoved = false;
	this.foreColor = this.backColor = "#999999";
	this.data = {};
	this.cssClasses = [];

	this.cleanup.init({
		valid_elements : s.valid_elements,
		extended_valid_elements : s.extended_valid_elements,
		valid_child_elements : s.valid_child_elements,
		entities : s.entities,
		entity_encoding : s.entity_encoding,
		debug : s.cleanup_debug,
		indent : s.apply_source_formatting,
		invalid_elements : s.invalid_elements,
		verify_html : s.verify_html,
		fix_content_duplication : s.fix_content_duplication,
		convert_fonts_to_spans : s.convert_fonts_to_spans
	});

	// Wrap old theme
	t = this.settings.theme;
	if (!tinyMCE.hasTheme(t)) {
		fn = tinyMCE.callbacks;
		tos = {};

		for (i=0; i<fn.length; i++) {
			if ((fu = window['TinyMCE_' + t + "_" + fn[i]]))
				tos[fn[i]] = fu;
		}

		tinyMCE.addTheme(t, tos);
	}

	// Wrap old plugins
	this.plugins = [];
	p = tinyMCE.getParam('plugins', '', true, ',');
	if (p.length > 0) {
		for (i=0; i<p.length; i++) {
			pn = p[i];

			if (pn.charAt(0) == '-')
				pn = pn.substring(1);

			if (!tinyMCE.hasPlugin(pn)) {
				fn = tinyMCE.callbacks;
				tos = {};

				for (x=0; x<fn.length; x++) {
					if ((fu = window['TinyMCE_' + pn + "_" + fn[x]]))
						tos[fn[x]] = fu;
				}

				tinyMCE.addPlugin(pn, tos);
			}

			this.plugins[this.plugins.length] = pn; 
		}
	}
};

TinyMCE_Control.prototype = {
	selection : null,

	settings : null,

	cleanup : null,

	getData : function(na) {
		var o = this.data[na];

		if (!o)
			o = this.data[na] = {};

		return o;
	},

	hasPlugin : function(n) {
		var i;

		for (i=0; i<this.plugins.length; i++) {
			if (this.plugins[i] == n)
				return true;
		}

		return false;
	},

	addPlugin : function(n, p) {
		if (!this.hasPlugin(n)) {
			tinyMCE.addPlugin(n, p);
			this.plugins[this.plugins.length] = n;
		}
	},

	repaint : function() {
		var s, b, ex;

		if (tinyMCE.isRealIE)
			return;

		try {
			s = this.selection;
			b = s.getBookmark(true);
			this.getBody().style.display = 'none';
			this.getDoc().execCommand('selectall', false, null);
			this.getSel().collapseToStart();
			this.getBody().style.display = 'block';
			s.moveToBookmark(b);
		} catch (ex) {
			// Ignore
		}
	},

	switchSettings : function() {
		if (tinyMCE.configs.length > 1 && tinyMCE.currentConfig != this.settings.index) {
			tinyMCE.settings = this.settings;
			tinyMCE.currentConfig = this.settings.index;
		}
	},

	select : function() {
		var oldInst = tinyMCE.selectedInstance;

		if (oldInst != this) {
			if (oldInst)
				oldInst.execCommand('mceEndTyping');

			tinyMCE.dispatchCallback(this, 'select_instance_callback', 'selectInstance', this, oldInst);
			tinyMCE.selectedInstance = this;
		}
	},

	getBody : function() {
		return this.contentBody ? this.contentBody : this.getDoc().body;
	},

	getDoc : function() {
//		return this.contentDocument ? this.contentDocument : this.contentWindow.document; // Removed due to IE 5.5 ?
		return this.contentWindow.document;
	},

	getWin : function() {
		return this.contentWindow;
	},

	getContainerWin : function() {
		return this.containerWindow ? this.containerWindow : window;
	},

	getViewPort : function() {
		return tinyMCE.getViewPort(this.getWin());
	},

	getParentNode : function(n, f) {
		return tinyMCE.getParentNode(n, f, this.getBody());
	},

	getParentElement : function(n, na, f) {
		return tinyMCE.getParentElement(n, na, f, this.getBody());
	},

	getParentBlockElement : function(n) {
		return tinyMCE.getParentBlockElement(n, this.getBody());
	},

	resizeToContent : function() {
		var d = this.getDoc(), b = d.body, de = d.documentElement;

		this.iframeElement.style.height = (tinyMCE.isRealIE) ? b.scrollHeight : de.offsetHeight + 'px';
	},

	addShortcut : function(m, k, d, cmd, ui, va) {
		var n = typeof(k) == "number", ie = tinyMCE.isIE, c, sc, i, scl = this.shortcuts;

		if (!tinyMCE.getParam('custom_shortcuts'))
			return false;

		m = m.toLowerCase();
		k = ie && !n ? k.toUpperCase() : k;
		c = n ? null : k.charCodeAt(0);
		d = d && d.indexOf('lang_') == 0 ? tinyMCE.getLang(d) : d;

		sc = {
			alt : m.indexOf('alt') != -1,
			ctrl : m.indexOf('ctrl') != -1,
			shift : m.indexOf('shift') != -1,
			charCode : c,
			keyCode : n ? k : (ie ? c : null),
			desc : d,
			cmd : cmd,
			ui : ui,
			val : va
		};

		for (i=0; i<scl.length; i++) {
			if (sc.alt == scl[i].alt && sc.ctrl == scl[i].ctrl && sc.shift == scl[i].shift
				&& sc.charCode == scl[i].charCode && sc.keyCode == scl[i].keyCode) {
				return false;
			}
		}

		scl[scl.length] = sc;

		return true;
	},

	handleShortcut : function(e) {
		var i, s, o;

		// Normal key press, then ignore it
		if (!e.altKey && !e.ctrlKey)
			return false;

		s = this.shortcuts;

		for (i=0; i<s.length; i++) {
			o = s[i];

			if (o.alt == e.altKey && o.ctrl == e.ctrlKey && (o.keyCode == e.keyCode || o.charCode == e.charCode)) {
				if (o.cmd && (e.type == "keydown" || (e.type == "keypress" && !tinyMCE.isOpera)))
					tinyMCE.execCommand(o.cmd, o.ui, o.val);

				tinyMCE.cancelEvent(e);
				return true;
			}
		}

		return false;
	},

	autoResetDesignMode : function() {
		// Add fix for tab/style.display none/block problems in Gecko
		if (!tinyMCE.isIE && this.isHidden() && tinyMCE.getParam('auto_reset_designmode'))
			eval('try { this.getDoc().designMode = "On"; this.useCSS = false; } catch(e) {}');
	},

	isHidden : function() {
		var s;

		if (tinyMCE.isIE)
			return false;

		s = this.getSel();

		// Weird, wheres that cursor selection?
		return (!s || !s.rangeCount || s.rangeCount == 0);
	},

	isDirty : function() {
		// Is content modified and not in a submit procedure
		return tinyMCE.trim(this.startContent) != tinyMCE.trim(this.getBody().innerHTML) && !this.isNotDirty;
	},

	_mergeElements : function(scmd, pa, ch, override) {
		var st, stc, className, n;

		if (scmd == "removeformat") {
			pa.className = "";
			pa.style.cssText = "";
			ch.className = "";
			ch.style.cssText = "";
			return;
		}

		st = tinyMCE.parseStyle(tinyMCE.getAttrib(pa, "style"));
		stc = tinyMCE.parseStyle(tinyMCE.getAttrib(ch, "style"));
		className = tinyMCE.getAttrib(pa, "class");

		// Removed class adding due to bug #1478272
		className = tinyMCE.getAttrib(ch, "class");

		if (override) {
			for (n in st) {
				if (typeof(st[n]) == 'function')
					continue;

				stc[n] = st[n];
			}
		} else {
			for (n in stc) {
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
	},

	_fixRootBlocks : function() {
		var rb, b, ne, be, nx, bm;

		rb = tinyMCE.getParam('forced_root_block');
		if (!rb)
			return;

		b = this.getBody();
		ne = b.firstChild;

		while (ne) {
			nx = ne.nextSibling;

			// If text node or inline element wrap it in a block element
			if (ne.nodeType == 3 || !tinyMCE.blockRegExp.test(ne.nodeName)) {
				if (!bm)
					bm = this.selection.getBookmark();

				if (!be) {
					be = this.getDoc().createElement(rb);
					be.appendChild(ne.cloneNode(true));
					b.replaceChild(be, ne);
				} else {
					be.appendChild(ne.cloneNode(true));
					b.removeChild(ne);
				}
			} else
				be = null;

			ne = nx;
		}

		if (bm)
			this.selection.moveToBookmark(bm);
	},

	_fixTrailingNbsp : function() {
		var s = this.selection, e = s.getFocusElement(), bm, v;

		if (e && tinyMCE.blockRegExp.test(e.nodeName) && e.firstChild) {
			v = e.firstChild.nodeValue;

			if (v && v.length > 1 && /(^\u00a0|\u00a0$)/.test(v)) {
				e.firstChild.nodeValue = v.replace(/(^\u00a0|\u00a0$)/, '');
				s.selectNode(e.firstChild, true, false, false); // Select and collapse
			}
		}
	},

	_setUseCSS : function(b) {
		var d = this.getDoc();

		try {d.execCommand("useCSS", false, !b);} catch (ex) {}
		try {d.execCommand("styleWithCSS", false, b);} catch (ex) {}

		if (!tinyMCE.getParam("table_inline_editing"))
			try {d.execCommand('enableInlineTableEditing', false, "false");} catch (ex) {}

		if (!tinyMCE.getParam("object_resizing"))
			try {d.execCommand('enableObjectResizing', false, "false");} catch (ex) {}
	},

	execCommand : function(command, user_interface, value) {
		var i, x, z, align, img, div, doc = this.getDoc(), win = this.getWin(), focusElm = this.getFocusElement();

		// Is not a undo specific command
		if (!new RegExp('mceStartTyping|mceEndTyping|mceBeginUndoLevel|mceEndUndoLevel|mceAddUndoLevel', 'gi').test(command))
			this.undoBookmark = null;

		// Mozilla issue
		if (!tinyMCE.isIE && !this.useCSS) {
			this._setUseCSS(false);
			this.useCSS = true;
		}

		//debug("command: " + command + ", user_interface: " + user_interface + ", value: " + value);
		this.contentDocument = doc; // <-- Strange, unless this is applied Mozilla 1.3 breaks

		// Don't dispatch key commands
		if (!/mceStartTyping|mceEndTyping/.test(command)) {
			if (tinyMCE.execCommandCallback(this, 'execcommand_callback', 'execCommand', this.editorId, this.getBody(), command, user_interface, value))
				return;
		}

		// Fix align on images
		if (focusElm && focusElm.nodeName == "IMG") {
			align = focusElm.getAttribute('align');
			img = command == "JustifyCenter" ? focusElm.cloneNode(false) : focusElm;

			switch (command) {
				case "JustifyLeft":
					if (align == 'left')
						img.removeAttribute('align');
					else
						img.setAttribute('align', 'left');

					// Remove the div
					div = focusElm.parentNode;
					if (div && div.nodeName == "DIV" && div.childNodes.length == 1 && div.parentNode)
						div.parentNode.replaceChild(img, div);

					this.selection.selectNode(img);
					this.repaint();
					tinyMCE.triggerNodeChange();
					return;

				case "JustifyCenter":
					img.removeAttribute('align');

					// Is centered
					div = tinyMCE.getParentElement(focusElm, "div");
					if (div && div.style.textAlign == "center") {
						// Remove div
						if (div.nodeName == "DIV" && div.childNodes.length == 1 && div.parentNode)
							div.parentNode.replaceChild(img, div);
					} else {
						// Add div
						div = this.getDoc().createElement("div");
						div.style.textAlign = 'center';
						div.appendChild(img);
						focusElm.parentNode.replaceChild(div, focusElm);
					}

					this.selection.selectNode(img);
					this.repaint();
					tinyMCE.triggerNodeChange();
					return;

				case "JustifyRight":
					if (align == 'right')
						img.removeAttribute('align');
					else
						img.setAttribute('align', 'right');

					// Remove the div
					div = focusElm.parentNode;
					if (div && div.nodeName == "DIV" && div.childNodes.length == 1 && div.parentNode)
						div.parentNode.replaceChild(img, div);

					this.selection.selectNode(img);
					this.repaint();
					tinyMCE.triggerNodeChange();
					return;
			}
		}

		if (tinyMCE.settings.force_br_newlines) {
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

				if (alignValue !== '') {
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

			case "unlink":
				// Unlink if caret is inside link
				if (tinyMCE.isGecko && this.getSel().isCollapsed) {
					focusElm = tinyMCE.getParentElement(focusElm, 'A');

					if (focusElm)
						this.selection.selectNode(focusElm, false);
				}

				this.getDoc().execCommand(command, user_interface, value);

				tinyMCE.isGecko && this.getSel().collapseToEnd();

				tinyMCE.triggerNodeChange();

				return true;

			case "InsertUnorderedList":
			case "InsertOrderedList":
				this.getDoc().execCommand(command, user_interface, value);
				tinyMCE.triggerNodeChange();
				break;

			case "Strikethrough":
				this.getDoc().execCommand(command, user_interface, value);
				tinyMCE.triggerNodeChange();
				break;

			case "mceSelectNode":
				this.selection.selectNode(value);
				tinyMCE.triggerNodeChange();
				tinyMCE.selectedNode = value;
				break;

			case "FormatBlock":
				if (value == null || value == '') {
					var elm = tinyMCE.getParentElement(this.getFocusElement(), "p,div,h1,h2,h3,h4,h5,h6,pre,address,blockquote,dt,dl,dd,samp");

					if (elm)
						this.execCommand("mceRemoveNode", false, elm);
				} else {
					if (!this.cleanup.isValid(value))
						return true;

					if (tinyMCE.isGecko && new RegExp('<(div|blockquote|code|dt|dd|dl|samp)>', 'gi').test(value))
						value = value.replace(/[^a-z]/gi, '');

					if (tinyMCE.isIE && new RegExp('blockquote|code|samp', 'gi').test(value)) {
						var b = this.selection.getBookmark();
						this.getDoc().execCommand("FormatBlock", false, '<p>');
						tinyMCE.renameElement(tinyMCE.getParentBlockElement(this.getFocusElement()), value);
						this.selection.moveToBookmark(b);
					} else
						this.getDoc().execCommand("FormatBlock", false, value);
				}

				tinyMCE.triggerNodeChange();

				break;

			case "mceRemoveNode":
				if (!value)
					value = tinyMCE.getParentElement(this.getFocusElement());

				if (tinyMCE.isIE) {
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
				for (i=0; parentNode; i++) {
					if (parentNode.nodeName.toLowerCase() == "body")
						break;

					if (parentNode.nodeName.toLowerCase() == "#text") {
						i--;
						parentNode = parentNode.parentNode;
						continue;
					}

					if (i == value) {
						this.selection.selectNode(parentNode, false);
						tinyMCE.triggerNodeChange();
						tinyMCE.selectedNode = parentNode;
						return;
					}

					parentNode = parentNode.parentNode;
				}

				break;

			case "mceSetStyleInfo":
			case "SetStyleInfo":
				var rng = this.getRng();
				var sel = this.getSel();
				var scmd = value.command;
				var sname = value.name;
				var svalue = value.value == null ? '' : value.value;
				//var svalue = value['value'] == null ? '' : value['value'];
				var wrapper = value.wrapper ? value.wrapper : "span";
				var parentElm = null;
				var invalidRe = new RegExp("^BODY|HTML$", "g");
				var invalidParentsRe = tinyMCE.settings.merge_styles_invalid_parents !== '' ? new RegExp(tinyMCE.settings.merge_styles_invalid_parents, "gi") : null;

				// Whole element selected check
				if (tinyMCE.isIE) {
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
					if (sel.isCollapsed || (new RegExp('td|tr|tbody|table|img', 'gi').test(felm.nodeName) && sel.anchorNode == felm.parentNode))
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
					var ch = tinyMCE.getNodeTree(parentElm, [], 1);
					for (z=0; z<ch.length; z++) {
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
					this._setUseCSS(false); // Bug in FF when running in fullscreen
					doc.execCommand("FontName", false, "#mce_temp_font#");
					var elementArray = tinyMCE.getElementsByAttributeValue(this.getBody(), "font", "face", "#mce_temp_font#");

					// Change them all
					for (x=0; x<elementArray.length; x++) {
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
								for (i=0; i<elm.childNodes.length; i++)
									spanElm.appendChild(elm.childNodes[i].cloneNode(true));
							}

							spanElm.setAttribute("mce_new", "true");
							elm.parentNode.replaceChild(spanElm, elm);

							// Remove style/attribs from all children
							var ch = tinyMCE.getNodeTree(spanElm, [], 1);
							for (z=0; z<ch.length; z++) {
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
				for (i=nodes.length-1; i>=0; i--) {
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
				for (i=nodes.length-1; i>=0; i--) {
					var elm = nodes[i], isEmpty = true;

					// Check if it has any attribs
					var tmp = doc.createElement("body");
					tmp.appendChild(elm.cloneNode(false));

					// Is empty span, remove it
					tmp.innerHTML = tmp.innerHTML.replace(new RegExp('style=""|class=""', 'gi'), '');
					//tinyMCE.debug(tmp.innerHTML);
					if (new RegExp('<span>', 'gi').test(tmp.innerHTML)) {
						for (x=0; x<elm.childNodes.length; x++) {
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
							this.selection.selectNode(f, false);
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
				value = value == null ? this.foreColor : value;
				value = tinyMCE.trim(value);
				value = value.charAt(0) != '#' ? (isNaN('0x' + value) ? value : '#' + value) : value;

				this.foreColor = value;
				this.getDoc().execCommand('forecolor', false, value);
				break;

			case "HiliteColor":
				value = value == null ? this.backColor : value;
				value = tinyMCE.trim(value);
				value = value.charAt(0) != '#' ? (isNaN('0x' + value) ? value : '#' + value) : value;
				this.backColor = value;

				if (tinyMCE.isGecko) {
					this._setUseCSS(true);
					this.getDoc().execCommand('hilitecolor', false, value);
					this._setUseCSS(false);
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
					if (confirm(tinyMCE.entityDecode(tinyMCE.getLang('lang_clipboard_msg'))))
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
				value = tinyMCE._customCleanup(this, "insert_to_editor", value);

				if (this.getBody().nodeName == 'BODY')
					tinyMCE._setHTML(doc, value);
				else
					this.getBody().innerHTML = value;

				tinyMCE.setInnerHTML(this.getBody(), tinyMCE._cleanupHTML(this, doc, this.settings, this.getBody(), false, false, false, true));
				tinyMCE.convertAllRelativeURLs(this.getBody());

				// Cleanup any mess left from storyAwayURLs
				tinyMCE._removeInternal(this.getBody());

				// When editing always use fonts internaly
				if (tinyMCE.getParam("convert_fonts_to_spans"))
					tinyMCE.convertSpansToFonts(doc);

				tinyMCE.handleVisualAid(this.getBody(), true, this.visualAid, this);
				tinyMCE._setEventsEnabled(this.getBody(), false);
				this._addBogusBR();

				return true;

			case "mceCleanup":
				var b = this.selection.getBookmark();
				tinyMCE._setHTML(this.contentDocument, this.getBody().innerHTML);
				tinyMCE.setInnerHTML(this.getBody(), tinyMCE._cleanupHTML(this, this.contentDocument, this.settings, this.getBody(), this.visualAid));
				tinyMCE.convertAllRelativeURLs(doc.body);

				// When editing always use fonts internaly
				if (tinyMCE.getParam("convert_fonts_to_spans"))
					tinyMCE.convertSpansToFonts(doc);

				tinyMCE.handleVisualAid(this.getBody(), true, this.visualAid, this);
				tinyMCE._setEventsEnabled(this.getBody(), false);
				this._addBogusBR();
				this.repaint();
				this.selection.moveToBookmark(b);
				tinyMCE.triggerNodeChange();
			break;

			case "mceReplaceContent":
				// Force empty string
				if (!value)
					value = '';

				this.getWin().focus();

				var selectedText = "";

				if (tinyMCE.isIE) {
					var rng = doc.selection.createRange();
					selectedText = rng.text;
				} else
					selectedText = this.getSel().toString();

				if (selectedText.length > 0) {
					value = tinyMCE.replaceVar(value, "selection", selectedText);
					tinyMCE.execCommand('mceInsertContent', false, value);
				}

				this._addBogusBR();
				tinyMCE.triggerNodeChange();
			break;

			case "mceSetAttribute":
				if (typeof(value) == 'object') {
					var targetElms = (typeof(value.targets) == "undefined") ? "p,img,span,div,td,h1,h2,h3,h4,h5,h6,pre,address" : value.targets;
					var targetNode = tinyMCE.getParentElement(this.getFocusElement(), targetElms);

					if (targetNode) {
						targetNode.setAttribute(value.name, value.value);
						tinyMCE.triggerNodeChange();
					}
				}
			break;

			case "mceSetCSSClass":
				this.execCommand("mceSetStyleInfo", false, {command : "setattrib", name : "class", value : value});
			break;

			case "mceInsertRawHTML":
				var key = 'tiny_mce_marker';

				this.execCommand('mceBeginUndoLevel');

				// Insert marker key
				this.execCommand('mceInsertContent', false, key);

				// Store away scroll pos
				var scrollX = this.getBody().scrollLeft + this.getDoc().documentElement.scrollLeft;
				var scrollY = this.getBody().scrollTop + this.getDoc().documentElement.scrollTop;

				// Find marker and replace with RAW HTML
				var html = this.getBody().innerHTML;
				if ((pos = html.indexOf(key)) != -1)
					tinyMCE.setInnerHTML(this.getBody(), html.substring(0, pos) + value + html.substring(pos + key.length));

				// Restore scoll pos
				this.contentWindow.scrollTo(scrollX, scrollY);

				this.execCommand('mceEndUndoLevel');

				break;

			case "mceInsertContent":
				// Force empty string
				if (!value)
					value = '';

				var insertHTMLFailed = false;

				// Removed since it produced problems in IE
				// this.getWin().focus();

				if (tinyMCE.isGecko || tinyMCE.isOpera) {
					try {
						// Is plain text or HTML, &amp;, &nbsp; etc will be encoded wrong in FF
						if (value.indexOf('<') == -1 && !value.match(/(&#38;|&#160;|&#60;|&#62;)/g)) {
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

				if (!tinyMCE.isIE) {
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
						value = doc.createTextNode(tinyMCE.entityDecode(value));
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

					tinyMCE.fixGeckoBaseHREFBug(2, this.getDoc(), value);
				} else {
					var rng = doc.selection.createRange(), tmpRng = null;
					var c = value.indexOf('<!--') != -1;

					// Fix comment bug, add tag before comments
					if (c)
						value = tinyMCE.uniqueTag + value;

					//	tmpRng = rng.duplicate(); // Store away range (Fixes Undo bookmark bug in IE)

					if (rng.item)
						rng.item(0).outerHTML = value;
					else
						rng.pasteHTML(value);

					//if (tmpRng)
					//	tmpRng.select(); // Restore range  (Fixes Undo bookmark bug in IE)

					// Remove unique tag
					if (c) {
						var e = this.getDoc().getElementById('mceTMPElement');
						e.parentNode.removeChild(e);
					}
				}

				tinyMCE.execCommand("mceAddUndoLevel");
				tinyMCE.triggerNodeChange();
			break;

			case "mceStartTyping":
				if (tinyMCE.settings.custom_undo_redo && this.undoRedo.typingUndoIndex == -1) {
					this.undoRedo.typingUndoIndex = this.undoRedo.undoIndex;
					tinyMCE.typingUndoIndex = tinyMCE.undoIndex;
					this.execCommand('mceAddUndoLevel');
				}
				break;

			case "mceEndTyping":
				if (tinyMCE.settings.custom_undo_redo && this.undoRedo.typingUndoIndex != -1) {
					this.execCommand('mceAddUndoLevel');
					this.undoRedo.typingUndoIndex = -1;
				}

				tinyMCE.typingUndoIndex = -1;
				break;

			case "mceBeginUndoLevel":
				this.undoRedoLevel = false;
				break;

			case "mceEndUndoLevel":
				this.undoRedoLevel = true;
				this.execCommand('mceAddUndoLevel');
				break;

			case "mceAddUndoLevel":
				if (tinyMCE.settings.custom_undo_redo && this.undoRedoLevel) {
					if (this.undoRedo.add())
						tinyMCE.triggerNodeChange(false);
				}
				break;

			case "Undo":
				if (tinyMCE.settings.custom_undo_redo) {
					tinyMCE.execCommand("mceEndTyping");
					this.undoRedo.undo();
					tinyMCE.triggerNodeChange();
				} else
					this.getDoc().execCommand(command, user_interface, value);
				break;

			case "Redo":
				if (tinyMCE.settings.custom_undo_redo) {
					tinyMCE.execCommand("mceEndTyping");
					this.undoRedo.redo();
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

				if (tinyMCE.isIE) {
					var n = tinyMCE.getParentElement(this.getFocusElement(), "blockquote");
					do {
						if (n && n.nodeName == "BLOCKQUOTE") {
							n.removeAttribute("dir");
							n.removeAttribute("style");
						}
					} while (n != null && (n = n.parentNode) != null);
				}
				break;

			case "RemoveFormat":
			case "removeformat":
				var text = this.selection.getSelectedText();

				if (tinyMCE.isOpera) {
					this.getDoc().execCommand("RemoveFormat", false, null);
					return;
				}

				if (tinyMCE.isIE) {
					try {
						var rng = doc.selection.createRange();
						rng.execCommand("RemoveFormat", false, null);
					} catch (e) {
						// Do nothing
					}

					this.execCommand("mceSetStyleInfo", false, {command : "removeformat"});
				} else {
					this.getDoc().execCommand(command, user_interface, value);

					this.execCommand("mceSetStyleInfo", false, {command : "removeformat"});
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
	},

	queryCommandValue : function(c) {
		try {
			return this.getDoc().queryCommandValue(c);
		} catch (e) {
			return null;
		}
	},

	queryCommandState : function(c) {
		return this.getDoc().queryCommandState(c);
	},

	_addBogusBR : function() {
		var b = this.getBody();

		if (tinyMCE.isGecko && !b.hasChildNodes())
			b.innerHTML = '<br _moz_editor_bogus_node="TRUE" />';
	},

	_onAdd : function(replace_element, form_element_name, target_document) {
		var hc, th, tos, editorTemplate, targetDoc, deltaWidth, deltaHeight, html, rng, fragment;
		var dynamicIFrame, tElm, doc, parentElm;

		th = this.settings.theme;
		tos = tinyMCE.themes[th];

		targetDoc = target_document ? target_document : document;

		this.targetDoc = targetDoc;

		tinyMCE.themeURL = tinyMCE.baseURL + "/themes/" + this.settings.theme;
		this.settings.themeurl = tinyMCE.themeURL;

		if (!replace_element) {
			alert("Error: Could not find the target element.");
			return false;
		}

		if (tos.getEditorTemplate)
			editorTemplate = tos.getEditorTemplate(this.settings, this.editorId);

		deltaWidth = editorTemplate.delta_width ? editorTemplate.delta_width : 0;
		deltaHeight = editorTemplate.delta_height ? editorTemplate.delta_height : 0;
		html = '<span id="' + this.editorId + '_parent" class="mceEditorContainer">' + editorTemplate.html;

		html = tinyMCE.replaceVar(html, "editor_id", this.editorId);

		if (!this.settings.default_document)
			this.settings.default_document = tinyMCE.baseURL + "/blank.htm";

		this.settings.old_width = this.settings.width;
		this.settings.old_height = this.settings.height;

		// Set default width, height
		if (this.settings.width == -1)
			this.settings.width = replace_element.offsetWidth;

		if (this.settings.height == -1)
			this.settings.height = replace_element.offsetHeight;

		// Try the style width
		if (this.settings.width == 0)
			this.settings.width = replace_element.style.width;

		// Try the style height
		if (this.settings.height == 0)
			this.settings.height = replace_element.style.height; 

		// If no width/height then default to 320x240, better than nothing
		if (this.settings.width == 0)
			this.settings.width = 320;

		if (this.settings.height == 0)
			this.settings.height = 240;

		this.settings.area_width = parseInt(this.settings.width);
		this.settings.area_height = parseInt(this.settings.height);
		this.settings.area_width += deltaWidth;
		this.settings.area_height += deltaHeight;
		this.settings.width_style = "" + this.settings.width;
		this.settings.height_style = "" + this.settings.height;

		// Special % handling
		if (("" + this.settings.width).indexOf('%') != -1)
			this.settings.area_width = "100%";
		else
			this.settings.width_style += 'px';

		if (("" + this.settings.height).indexOf('%') != -1)
			this.settings.area_height = "100%";
		else
			this.settings.height_style += 'px';

		if (("" + replace_element.style.width).indexOf('%') != -1) {
			this.settings.width = replace_element.style.width;
			this.settings.area_width = "100%";
			this.settings.width_style = "100%";
		}

		if (("" + replace_element.style.height).indexOf('%') != -1) {
			this.settings.height = replace_element.style.height;
			this.settings.area_height = "100%";
			this.settings.height_style = "100%";
		}

		html = tinyMCE.applyTemplate(html);

		this.settings.width = this.settings.old_width;
		this.settings.height = this.settings.old_height;

		this.visualAid = this.settings.visual;
		this.formTargetElementId = form_element_name;

		// Get replace_element contents
		if (replace_element.nodeName == "TEXTAREA" || replace_element.nodeName == "INPUT")
			this.startContent = replace_element.value;
		else
			this.startContent = replace_element.innerHTML;

		// If not text area or input
		if (replace_element.nodeName != "TEXTAREA" && replace_element.nodeName != "INPUT") {
			this.oldTargetElement = replace_element;

			// Debug mode
			hc = '<input type="hidden" id="' + form_element_name + '" name="' + form_element_name + '" />';
			this.oldTargetDisplay = tinyMCE.getStyle(this.oldTargetElement, 'display', 'inline');
			this.oldTargetElement.style.display = "none";

			html += '</span>';

			if (tinyMCE.isGecko)
				html = hc + html;
			else
				html += hc;

			// Output HTML and set editable
			if (tinyMCE.isGecko) {
				rng = replace_element.ownerDocument.createRange();
				rng.setStartBefore(replace_element);

				fragment = rng.createContextualFragment(html);
				tinyMCE.insertAfter(fragment, replace_element);
			} else
				replace_element.insertAdjacentHTML("beforeBegin", html);
		} else {
			html += '</span>';

			// Just hide the textarea element
			this.oldTargetElement = replace_element;

			this.oldTargetDisplay = tinyMCE.getStyle(this.oldTargetElement, 'display', 'inline');
			this.oldTargetElement.style.display = "none";

			// Output HTML and set editable
			if (tinyMCE.isGecko) {
				rng = replace_element.ownerDocument.createRange();
				rng.setStartBefore(replace_element);

				fragment = rng.createContextualFragment(html);
				tinyMCE.insertAfter(fragment, replace_element);
			} else
				replace_element.insertAdjacentHTML("beforeBegin", html);
		}

		// Setup iframe
		dynamicIFrame = false;
		tElm = targetDoc.getElementById(this.editorId);

		if (!tinyMCE.isIE) {
			// Node case is preserved in XML strict mode
			if (tElm && (tElm.nodeName == "SPAN" || tElm.nodeName == "span")) {
				tElm = tinyMCE._createIFrame(tElm, targetDoc);
				dynamicIFrame = true;
			}

			this.targetElement = tElm;
			this.iframeElement = tElm;
			this.contentDocument = tElm.contentDocument;
			this.contentWindow = tElm.contentWindow;

			//this.getDoc().designMode = "on";
		} else {
			if (tElm && tElm.nodeName == "SPAN")
				tElm = tinyMCE._createIFrame(tElm, targetDoc, targetDoc.parentWindow);
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
		doc = this.contentDocument;
		if (dynamicIFrame) {
			html = tinyMCE.getParam('doctype') + '<html><head xmlns="http://www.w3.org/1999/xhtml"><base href="' + tinyMCE.settings.base_href + '" /><title>blank_page</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body class="mceContentBody"></body></html>';

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
		if (tinyMCE.isIE)
			window.setTimeout("tinyMCE.addEventHandlers(tinyMCE.instances[\"" + this.editorId + "\"]);", 1);

		// Setup element references
		parentElm = this.targetDoc.getElementById(this.editorId + '_parent');
		this.formElement = tinyMCE.isGecko ? parentElm.previousSibling : parentElm.nextSibling;

		tinyMCE.setupContent(this.editorId, true);

		return true;
	},

	setBaseHREF : function(u) {
		var h, b, d, nl;

		d = this.getDoc();
		nl = d.getElementsByTagName("base");
		b = nl.length > 0 ? nl[0] : null;

		if (!b) {
			nl = d.getElementsByTagName("head");
			h = nl.length > 0 ? nl[0] : null;

			b = d.createElement("base");
			b.setAttribute('href', u);
			h.appendChild(b);
		} else {
			if (u == '' || u == null)
				b.parentNode.removeChild(b);
			else
				b.setAttribute('href', u);
		}
	},

	getHTML : function(r) {
		var h, d = this.getDoc(), b = this.getBody();

		if (r)
			return b.innerHTML;

		h = tinyMCE._cleanupHTML(this, d, this.settings, b, false, true, false, true);

		if (tinyMCE.getParam("convert_fonts_to_spans"))
			tinyMCE.convertSpansToFonts(d);

		return h;
	},

	setHTML : function(h) {
		this.execCommand('mceSetContent', false, h);
		this.repaint();
	},

	getFocusElement : function() {
		return this.selection.getFocusElement();
	},

	getSel : function() {
		return this.selection.getSel();
	},

	getRng : function() {
		return this.selection.getRng();
	},

	triggerSave : function(skip_cleanup, skip_callback) {
		var e, nl = [], i, s, content, htm;

		if (!this.enabled)
			return;

		this.switchSettings();
		s = tinyMCE.settings;

		// Force hidden tabs visible while serializing
		if (tinyMCE.isRealIE) {
			e = this.iframeElement;

			do {
				if (e.style && e.style.display == 'none') {
					e.style.display = 'block';
					nl[nl.length] = {elm : e, type : 'style'};
				}

				if (e.style && s.hidden_tab_class.length > 0 && e.className.indexOf(s.hidden_tab_class) != -1) {
					e.className = s.display_tab_class;
					nl[nl.length] = {elm : e, type : 'class'};
				}
			} while ((e = e.parentNode) != null)
		}

		tinyMCE.settings.preformatted = false;

		// Default to false
		if (typeof(skip_cleanup) == "undefined")
			skip_cleanup = false;

		// Default to false
		if (typeof(skip_callback) == "undefined")
			skip_callback = false;

		tinyMCE._setHTML(this.getDoc(), this.getBody().innerHTML);

		// Remove visual aids when cleanup is disabled
		if (this.settings.cleanup == false) {
			tinyMCE.handleVisualAid(this.getBody(), true, false, this);
			tinyMCE._setEventsEnabled(this.getBody(), true);
		}

		tinyMCE._customCleanup(this, "submit_content_dom", this.contentWindow.document.body);
		htm = skip_cleanup ? this.getBody().innerHTML : tinyMCE._cleanupHTML(this, this.getDoc(), this.settings, this.getBody(), tinyMCE.visualAid, true, true);
		htm = tinyMCE._customCleanup(this, "submit_content", htm);

		if (!skip_callback && tinyMCE.settings.save_callback !== '')
			content = tinyMCE.resolveDots(tinyMCE.settings.save_callback, window)(this.formTargetElementId,htm,this.getBody());

		// Use callback content if available
		if ((typeof(content) != "undefined") && content != null)
			htm = content;

		// Replace some weird entities (Bug: #1056343)
		htm = tinyMCE.regexpReplace(htm, "&#40;", "(", "gi");
		htm = tinyMCE.regexpReplace(htm, "&#41;", ")", "gi");
		htm = tinyMCE.regexpReplace(htm, "&#59;", ";", "gi");
		htm = tinyMCE.regexpReplace(htm, "&#34;", "&quot;", "gi");
		htm = tinyMCE.regexpReplace(htm, "&#94;", "^", "gi");

		if (this.formElement)
			this.formElement.value = htm;

		if (tinyMCE.isSafari && this.formElement)
			this.formElement.innerText = htm;

		// Hide them again (tabs in MSIE)
		for (i=0; i<nl.length; i++) {
			if (nl[i].type == 'style')
				nl[i].elm.style.display = 'none';
			else
				nl[i].elm.className = s.hidden_tab_class;
		}
	}

	};

/* file:jscripts/tiny_mce/classes/TinyMCE_Cleanup.class.js */

tinyMCE.add(TinyMCE_Engine, {
	cleanupHTMLCode : function(s) {
		s = s.replace(new RegExp('<p \\/>', 'gi'), '<p>&nbsp;</p>');
		s = s.replace(new RegExp('<p>\\s*<\\/p>', 'gi'), '<p>&nbsp;</p>');

		// Fix close BR elements
		s = s.replace(new RegExp('<br>\\s*<\\/br>', 'gi'), '<br />');

		// Open closed tags like <b/> to <b></b>
		s = s.replace(new RegExp('<(h[1-6]|p|div|address|pre|form|table|li|ol|ul|td|b|font|em|strong|i|strike|u|span|a|ul|ol|li|blockquote)([a-z]*)([^\\\\|>]*)\\/>', 'gi'), '<$1$2$3></$1$2>');

		// Remove trailing space <b > to <b>
		s = s.replace(new RegExp('\\s+></', 'gi'), '></');

		// Close tags <img></img> to <img/>
		s = s.replace(new RegExp('<(img|br|hr)([^>]*)><\\/(img|br|hr)>', 'gi'), '<$1$2 />');

		// Weird MSIE bug, <p><hr /></p> breaks runtime?
		if (tinyMCE.isIE)
			s = s.replace(new RegExp('<p><hr \\/><\\/p>', 'gi'), "<hr>");

		// Weird tags will make IE error #bug: 1538495
		if (tinyMCE.isIE)
			s = s.replace(/<!(\s*)\/>/g, '');

		// Convert relative anchors to absolute URLs ex: #something to file.htm#something
		// Removed: Since local document anchors should never be forced absolute example edit.php?id=something
		//if (tinyMCE.getParam('convert_urls'))
		//	s = s.replace(new RegExp('(href=\"{0,1})(\\s*#)', 'gi'), '$1' + tinyMCE.settings.document_base_url + "#");

		return s;
	},

	parseStyle : function(str) {
		var ar = [], st, i, re, pa;

		if (str == null)
			return ar;

		st = str.split(';');

		tinyMCE.clearArray(ar);

		for (i=0; i<st.length; i++) {
			if (st[i] == '')
				continue;

			re = new RegExp('^\\s*([^:]*):\\s*(.*)\\s*$');
			pa = st[i].replace(re, '$1||$2').split('||');
	//tinyMCE.debug(str, pa[0] + "=" + pa[1], st[i].replace(re, '$1||$2'));
			if (pa.length == 2)
				ar[pa[0].toLowerCase()] = pa[1];
		}

		return ar;
	},

	compressStyle : function(ar, pr, sf, res) {
		var box = [], i, a;

		box[0] = ar[pr + '-top' + sf];
		box[1] = ar[pr + '-left' + sf];
		box[2] = ar[pr + '-right' + sf];
		box[3] = ar[pr + '-bottom' + sf];

		for (i=0; i<box.length; i++) {
			if (box[i] == null)
				return;

			for (a=0; a<box.length; a++) {
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
	},

	serializeStyle : function(ar) {
		var str = "", key, val, m;

		// Compress box
		tinyMCE.compressStyle(ar, "border", "", "border");
		tinyMCE.compressStyle(ar, "border", "-width", "border-width");
		tinyMCE.compressStyle(ar, "border", "-color", "border-color");
		tinyMCE.compressStyle(ar, "border", "-style", "border-style");
		tinyMCE.compressStyle(ar, "padding", "", "padding");
		tinyMCE.compressStyle(ar, "margin", "", "margin");

		for (key in ar) {
			val = ar[key];

			if (typeof(val) == 'function')
				continue;

			if (key.indexOf('mso-') == 0)
				continue;

			if (val != null && val !== '') {
				val = '' + val; // Force string

				// Fix style URL
				val = val.replace(new RegExp("url\\(\\'?([^\\']*)\\'?\\)", 'gi'), "url('$1')");

				// Convert URL
				if (val.indexOf('url(') != -1 && tinyMCE.getParam('convert_urls')) {
					m = new RegExp("url\\('(.*?)'\\)").exec(val);

					if (m.length > 1)
						val = "url('" + eval(tinyMCE.getParam('urlconverter_callback') + "(m[1], null, true);") + "')";
				}

				// Force HEX colors
				if (tinyMCE.getParam("force_hex_style_colors"))
					val = tinyMCE.convertRGBToHex(val, true);

				val = val.replace(/\"/g, '\'');

				if (val != "url('')")
					str += key.toLowerCase() + ": " + val + "; ";
			}
		}

		if (new RegExp('; $').test(str))
			str = str.substring(0, str.length - 2);

		return str;
	},

	convertRGBToHex : function(s, k) {
		var re, rgb;

		if (s.toLowerCase().indexOf('rgb') != -1) {
			re = new RegExp("(.*?)rgb\\s*?\\(\\s*?([0-9]+).*?,\\s*?([0-9]+).*?,\\s*?([0-9]+).*?\\)(.*?)", "gi");
			rgb = s.replace(re, "$1,$2,$3,$4,$5").split(',');

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
	},

	convertHexToRGB : function(s) {
		if (s.indexOf('#') != -1) {
			s = s.replace(new RegExp('[^0-9A-F]', 'gi'), '');
			return "rgb(" + parseInt(s.substring(0, 2), 16) + "," + parseInt(s.substring(2, 4), 16) + "," + parseInt(s.substring(4, 6), 16) + ")";
		}

		return s;
	},

	convertSpansToFonts : function(doc) {
		var s, i, size, fSize, x, fFace, fColor, sizes = tinyMCE.getParam('font_size_style_values').replace(/\s+/, '').split(',');

		s = tinyMCE.selectElements(doc, 'span,font');
		for (i=0; i<s.length; i++) {
			size = tinyMCE.trim(s[i].style.fontSize).toLowerCase();
			fSize = 0;

			for (x=0; x<sizes.length; x++) {
				if (sizes[x] == size) {
					fSize = x + 1;
					break;
				}
			}

			if (fSize > 0) {
				tinyMCE.setAttrib(s[i], 'size', fSize);
				s[i].style.fontSize = '';
			}

			fFace = s[i].style.fontFamily;
			if (fFace != null && fFace !== '') {
				tinyMCE.setAttrib(s[i], 'face', fFace);
				s[i].style.fontFamily = '';
			}

			fColor = s[i].style.color;
			if (fColor != null && fColor !== '') {
				tinyMCE.setAttrib(s[i], 'color', tinyMCE.convertRGBToHex(fColor));
				s[i].style.color = '';
			}
		}
	},

	convertFontsToSpans : function(doc) {
		var fsClasses, s, i, fSize, fFace, fColor, sizes = tinyMCE.getParam('font_size_style_values').replace(/\s+/, '').split(',');

		fsClasses = tinyMCE.getParam('font_size_classes');
		if (fsClasses !== '')
			fsClasses = fsClasses.replace(/\s+/, '').split(',');
		else
			fsClasses = null;

		s = tinyMCE.selectElements(doc, 'span,font');
		for (i=0; i<s.length; i++) {
			fSize = tinyMCE.getAttrib(s[i], 'size');
			fFace = tinyMCE.getAttrib(s[i], 'face');
			fColor = tinyMCE.getAttrib(s[i], 'color');

			if (fSize !== '') {
				fSize = parseInt(fSize);

				if (fSize > 0 && fSize < 8) {
					if (fsClasses != null)
						tinyMCE.setAttrib(s[i], 'class', fsClasses[fSize-1]);
					else
						s[i].style.fontSize = sizes[fSize-1];
				}

				s[i].removeAttribute('size');
			}

			if (fFace !== '') {
				s[i].style.fontFamily = fFace;
				s[i].removeAttribute('face');
			}

			if (fColor !== '') {
				s[i].style.color = fColor;
				s[i].removeAttribute('color');
			}
		}
	},

	cleanupAnchors : function(doc) {
		var i, cn, x, an = doc.getElementsByTagName("a");

		// Loops backwards due to bug #1467987
		for (i=an.length-1; i>=0; i--) {
			if (tinyMCE.getAttrib(an[i], "name") !== '' && tinyMCE.getAttrib(an[i], "href") == '') {
				cn = an[i].childNodes;

				for (x=cn.length-1; x>=0; x--)
					tinyMCE.insertAfter(cn[x], an[i]);
			}
		}
	},

	getContent : function(editor_id) {
		if (typeof(editor_id) != "undefined")
			 tinyMCE.getInstanceById(editor_id).select();

		if (tinyMCE.selectedInstance)
			return tinyMCE.selectedInstance.getHTML();

		return null;
	},

	_fixListElements : function(d) {
		var nl, x, a = ['ol', 'ul'], i, n, p, r = new RegExp('^(OL|UL)$'), np;

		for (x=0; x<a.length; x++) {
			nl = d.getElementsByTagName(a[x]);

			for (i=0; i<nl.length; i++) {
				n = nl[i];
				p = n.parentNode;

				if (r.test(p.nodeName)) {
					np = tinyMCE.prevNode(n, 'LI');

					if (!np) {
						np = d.createElement('li');
						np.innerHTML = '&nbsp;';
						np.appendChild(n);
						p.insertBefore(np, p.firstChild);
					} else
						np.appendChild(n);
				}
			}
		}
	},

	_fixTables : function(d) {
		var nl, i, n, p, np, x, t;

		nl = d.getElementsByTagName('table');
		for (i=0; i<nl.length; i++) {
			n = nl[i];

			if ((p = tinyMCE.getParentElement(n, 'p,h1,h2,h3,h4,h5,h6')) != null) {
				np = p.cloneNode(false);
				np.removeAttribute('id');

				t = n;

				while ((n = n.nextSibling))
					np.appendChild(n);

				tinyMCE.insertAfter(np, p);
				tinyMCE.insertAfter(t, p);
			}
		}
	},

	_cleanupHTML : function(inst, doc, config, elm, visual, on_save, on_submit, inn) {
		var h, d, t1, t2, t3, t4, t5, c, s, nb;

		if (!tinyMCE.getParam('cleanup'))
			return elm.innerHTML;

		on_save = typeof(on_save) == 'undefined' ? false : on_save;

		c = inst.cleanup;
		s = inst.settings;
		d = c.settings.debug;

		if (d)
			t1 = new Date().getTime();

		inst._fixRootBlocks();

		if (tinyMCE.getParam("convert_fonts_to_spans"))
			tinyMCE.convertFontsToSpans(doc);

		if (tinyMCE.getParam("fix_list_elements"))
			tinyMCE._fixListElements(doc);

		if (tinyMCE.getParam("fix_table_elements"))
			tinyMCE._fixTables(doc);

		// Call custom cleanup code
		tinyMCE._customCleanup(inst, on_save ? "get_from_editor_dom" : "insert_to_editor_dom", doc.body);

		if (d)
			t2 = new Date().getTime();

		c.settings.on_save = on_save;

		c.idCount = 0;
		c.serializationId = new Date().getTime().toString(32); // Unique ID needed for the content duplication bug
		c.serializedNodes = [];
		c.sourceIndex = -1;

		if (s.cleanup_serializer == "xml")
			h = c.serializeNodeAsXML(elm, inn);
		else
			h = c.serializeNodeAsHTML(elm, inn);

		if (d)
			t3 = new Date().getTime();

		// Post processing
		nb = tinyMCE.getParam('entity_encoding') == 'numeric' ? '&#160;' : '&nbsp;';
		h = h.replace(/<\/?(body|head|html)[^>]*>/gi, '');
		h = h.replace(new RegExp(' (rowspan="1"|colspan="1")', 'g'), '');
		h = h.replace(/<p><hr \/><\/p>/g, '<hr />');
		h = h.replace(/<p>(&nbsp;|&#160;)<\/p><hr \/><p>(&nbsp;|&#160;)<\/p>/g, '<hr />');
		h = h.replace(/<td>\s*<br \/>\s*<\/td>/g, '<td>' + nb + '</td>');
		h = h.replace(/<p>\s*<br \/>\s*<\/p>/g, '<p>' + nb + '</p>');
		h = h.replace(/<br \/>$/, ''); // Remove last BR for Gecko
		h = h.replace(/<br \/><\/p>/g, '</p>'); // Remove last BR in P tags for Gecko
		h = h.replace(/<p>\s*(&nbsp;|&#160;)\s*<br \/>\s*(&nbsp;|&#160;)\s*<\/p>/g, '<p>' + nb + '</p>');
		h = h.replace(/<p>\s*(&nbsp;|&#160;)\s*<br \/>\s*<\/p>/g, '<p>' + nb + '</p>');
		h = h.replace(/<p>\s*<br \/>\s*&nbsp;\s*<\/p>/g, '<p>' + nb + '</p>');
		h = h.replace(new RegExp('<a>(.*?)<\\/a>', 'g'), '$1');
		h = h.replace(/<p([^>]*)>\s*<\/p>/g, '<p$1>' + nb + '</p>');

		// Clean body
		if (/^\s*(<br \/>|<p>&nbsp;<\/p>|<p>&#160;<\/p>|<p><\/p>)\s*$/.test(h))
			h = '';

		// If preformatted
		if (s.preformatted) {
			h = h.replace(/^<pre>/, '');
			h = h.replace(/<\/pre>$/, '');
			h = '<pre>' + h + '</pre>';
		}

		// Gecko specific processing
		if (tinyMCE.isGecko) {
			// Makes no sence but FF generates it!!
			h = h.replace(/<br \/>\s*<\/li>/g, '</li>');
			h = h.replace(/&nbsp;\s*<\/(dd|dt)>/g, '</$1>');
			h = h.replace(/<o:p _moz-userdefined="" \/>/g, '');
			h = h.replace(/<td([^>]*)>\s*<br \/>\s*<\/td>/g, '<td$1>' + nb + '</td>');
		}

		if (s.force_br_newlines)
			h = h.replace(/<p>(&nbsp;|&#160;)<\/p>/g, '<br />');

		// Call custom cleanup code
		h = tinyMCE._customCleanup(inst, on_save ? "get_from_editor" : "insert_to_editor", h);

		// Remove internal classes
		if (on_save) {
			h = h.replace(new RegExp(' ?(mceItem[a-zA-Z0-9]*|' + s.visual_table_class + ')', 'g'), '');
			h = h.replace(new RegExp(' ?class=""', 'g'), '');
		}

		if (s.remove_linebreaks && !c.settings.indent)
			h = h.replace(/\n|\r/g, ' ');

		if (d)
			t4 = new Date().getTime();

		if (on_save && c.settings.indent)
			h = c.formatHTML(h);

		// If encoding (not recommended option)
		if (on_submit && (s.encoding == "xml" || s.encoding == "html"))
			h = c.xmlEncode(h);

		if (d)
			t5 = new Date().getTime();

		if (c.settings.debug)
			tinyMCE.debug("Cleanup in ms: Pre=" + (t2-t1) + ", Serialize: " + (t3-t2) + ", Post: " + (t4-t3) + ", Format: " + (t5-t4) + ", Sum: " + (t5-t1) + ".");

		return h;
	}
});

function TinyMCE_Cleanup() {
	this.isIE = (navigator.appName == "Microsoft Internet Explorer");
	this.rules = tinyMCE.clearArray([]);

	// Default config
	this.settings = {
		indent_elements : 'head,table,tbody,thead,tfoot,form,tr,ul,ol,blockquote,object',
		newline_before_elements : 'h1,h2,h3,h4,h5,h6,pre,address,div,ul,ol,li,meta,option,area,title,link,base,script,td',
		newline_after_elements : 'br,hr,p,pre,address,div,ul,ol,meta,option,area,link,base,script',
		newline_before_after_elements : 'html,head,body,table,thead,tbody,tfoot,tr,form,ul,ol,blockquote,p,object,param,hr,div',
		indent_char : '\t',
		indent_levels : 1,
		entity_encoding : 'raw',
		valid_elements : '*[*]',
		entities : '',
		url_converter : '',
		invalid_elements : '',
		verify_html : false
	};

	this.vElements = tinyMCE.clearArray([]);
	this.vElementsRe = '';
	this.closeElementsRe = /^(IMG|BR|HR|LINK|META|BASE|INPUT|AREA)$/;
	this.codeElementsRe = /^(SCRIPT|STYLE)$/;
	this.serializationId = 0;
	this.mceAttribs = {
		href : 'mce_href',
		src : 'mce_src',
		type : 'mce_type'
	};
}

TinyMCE_Cleanup.prototype = {
	init : function(s) {
		var n, a, i, ir, or, st;

		for (n in s)
			this.settings[n] = s[n];

		// Setup code formating
		s = this.settings;

		// Setup regexps
		this.inRe = this._arrayToRe(s.indent_elements.split(','), '', '^<(', ')[^>]*');
		this.ouRe = this._arrayToRe(s.indent_elements.split(','), '', '^<\\/(', ')[^>]*');
		this.nlBeforeRe = this._arrayToRe(s.newline_before_elements.split(','), 'gi', '<(',  ')([^>]*)>');
		this.nlAfterRe = this._arrayToRe(s.newline_after_elements.split(','), 'gi', '<(',  ')([^>]*)>');
		this.nlBeforeAfterRe = this._arrayToRe(s.newline_before_after_elements.split(','), 'gi', '<(\\/?)(', ')([^>]*)>');
		this.serializedNodes = [];

		if (s.invalid_elements !== '')
			this.iveRe = this._arrayToRe(s.invalid_elements.toUpperCase().split(','), 'g', '^(', ')$');
		else
			this.iveRe = null;

		// Setup separator
		st = '';
		for (i=0; i<s.indent_levels; i++)
			st += s.indent_char;

		this.inStr = st;

		// If verify_html if false force *[*]
		if (!s.verify_html) {
			s.valid_elements = '*[*]';
			s.extended_valid_elements = '';
		}

		this.fillStr = s.entity_encoding == "named" ? "&nbsp;" : "&#160;";
		this.idCount = 0;
		this.xmlEncodeRe = new RegExp('[\u007F-\uFFFF<>&"]', 'g');
	},

	addRuleStr : function(s) {
		var r = this.parseRuleStr(s), n;

		for (n in r) {
			if (r[n])
				this.rules[n] = r[n];
		}

		this.vElements = tinyMCE.clearArray([]);

		for (n in this.rules) {
			if (this.rules[n])
				this.vElements[this.vElements.length] = this.rules[n].tag;
		}

		this.vElementsRe = this._arrayToRe(this.vElements, '');
	},

	isValid : function(n) {
		if (!this.rulesDone)
			this._setupRules(); // Will initialize cleanup rules

		// Empty is true since it removes formatting
		if (!n)
			return true;

		// Clean the name up a bit
		n = n.replace(/[^a-z0-9]+/gi, '').toUpperCase();

		return !tinyMCE.getParam('cleanup') || this.vElementsRe.test(n);
	},

	addChildRemoveRuleStr : function(s) {
		var x, y, p, i, t, tn, ta, cl, r;

		if (!s)
			return;

		ta = s.split(',');
		for (x=0; x<ta.length; x++) {
			s = ta[x];

			// Split tag/children
			p = this.split(/\[|\]/, s);
			if (p == null || p.length < 1)
				t = s.toUpperCase();
			else
				t = p[0].toUpperCase();

			// Handle all tag names
			tn = this.split('/', t);
			for (y=0; y<tn.length; y++) {
				r = "^(";

				// Build regex
				cl = this.split(/\|/, p[1]);
				for (i=0; i<cl.length; i++) {
					if (cl[i] == '%istrict')
						r += tinyMCE.inlineStrict;
					else if (cl[i] == '%itrans')
						r += tinyMCE.inlineTransitional;
					else if (cl[i] == '%istrict_na')
						r += tinyMCE.inlineStrict.substring(2);
					else if (cl[i] == '%itrans_na')
						r += tinyMCE.inlineTransitional.substring(2);
					else if (cl[i] == '%btrans')
						r += tinyMCE.blockElms;
					else if (cl[i] == '%strict')
						r += tinyMCE.blockStrict;
					else
						r += (cl[i].charAt(0) != '#' ? cl[i].toUpperCase() : cl[i]);

					r += (i != cl.length - 1 ? '|' : '');
				}

				r += ')$';

				if (this.childRules == null)
					this.childRules = tinyMCE.clearArray([]);

				this.childRules[tn[y]] = new RegExp(r);

				if (p.length > 1)
					this.childRules[tn[y]].wrapTag = p[2];
			}
		}
	},

	parseRuleStr : function(s) {
		var ta, p, r, a, i, x, px, t, tn, y, av, or = tinyMCE.clearArray([]), dv;

		if (s == null || s.length == 0)
			return or;

		ta = s.split(',');
		for (x=0; x<ta.length; x++) {
			s = ta[x];
			if (s.length == 0)
				continue;

			// Split tag/attrs
			p = this.split(/\[|\]/, s);
			if (p == null || p.length < 1)
				t = s.toUpperCase();
			else
				t = p[0].toUpperCase();

			// Handle all tag names
			tn = this.split('/', t);
			for (y=0; y<tn.length; y++) {
				r = {};

				r.tag = tn[y];
				r.forceAttribs = null;
				r.defaultAttribs = null;
				r.validAttribValues = null;

				// Handle prefixes
				px = r.tag.charAt(0);
				r.forceOpen = px == '+';
				r.removeEmpty = px == '-';
				r.fill = px == '#';
				r.tag = r.tag.replace(/\+|-|#/g, '');
				r.oTagName = tn[0].replace(/\+|-|#/g, '').toLowerCase();
				r.isWild = new RegExp('\\*|\\?|\\+', 'g').test(r.tag);
				r.validRe = new RegExp(this._wildcardToRe('^' + r.tag + '$'));

				// Setup valid attributes
				if (p.length > 1) {
					r.vAttribsRe = '^(';
					a = this.split(/\|/, p[1]);

					for (i=0; i<a.length; i++) {
						t = a[i];

						if (t.charAt(0) == '!') {
							a[i] = t = t.substring(1);

							if (!r.reqAttribsRe)
								r.reqAttribsRe = '\\s+(' + t;
							else
								r.reqAttribsRe += '|' + t;
						}

						av = new RegExp('(=|:|<)(.*?)$').exec(t);
						t = t.replace(new RegExp('(=|:|<).*?$'), '');
						if (av && av.length > 0) {
							if (av[0].charAt(0) == ':') {
								if (!r.forceAttribs)
									r.forceAttribs = tinyMCE.clearArray([]);

								r.forceAttribs[t.toLowerCase()] = av[0].substring(1);
							} else if (av[0].charAt(0) == '=') {
								if (!r.defaultAttribs)
									r.defaultAttribs = tinyMCE.clearArray([]);

								dv = av[0].substring(1);

								r.defaultAttribs[t.toLowerCase()] = dv == '' ? "mce_empty" : dv;
							} else if (av[0].charAt(0) == '<') {
								if (!r.validAttribValues)
									r.validAttribValues = tinyMCE.clearArray([]);

								r.validAttribValues[t.toLowerCase()] = this._arrayToRe(this.split('?', av[0].substring(1)), 'i');
							}
						}

						r.vAttribsRe += '' + t.toLowerCase() + (i != a.length - 1 ? '|' : '');

						a[i] = t.toLowerCase();
					}

					if (r.reqAttribsRe)
						r.reqAttribsRe = new RegExp(r.reqAttribsRe + ')=\"', 'g');

					r.vAttribsRe += ')$';
					r.vAttribsRe = this._wildcardToRe(r.vAttribsRe);
					r.vAttribsReIsWild = new RegExp('\\*|\\?|\\+', 'g').test(r.vAttribsRe);
					r.vAttribsRe = new RegExp(r.vAttribsRe);
					r.vAttribs = a.reverse();

					//tinyMCE.debug(r.tag, r.oTagName, r.vAttribsRe, r.vAttribsReWC);
				} else {
					r.vAttribsRe = '';
					r.vAttribs = tinyMCE.clearArray([]);
					r.vAttribsReIsWild = false;
				}

				or[r.tag] = r;
			}
		}

		return or;
	},

	serializeNodeAsXML : function(n) {
		var s, b;

		if (!this.xmlDoc) {
			if (this.isIE) {
				try {this.xmlDoc = new ActiveXObject('MSXML2.DOMDocument');} catch (e) {}

				if (!this.xmlDoc)
					try {this.xmlDoc = new ActiveXObject('Microsoft.XmlDom');} catch (e) {}
			} else
				this.xmlDoc = document.implementation.createDocument('', '', null);

			if (!this.xmlDoc)
				alert("Error XML Parser could not be found.");
		}

		if (this.xmlDoc.firstChild)
			this.xmlDoc.removeChild(this.xmlDoc.firstChild);

		b = this.xmlDoc.createElement("html");
		b = this.xmlDoc.appendChild(b);

		this._convertToXML(n, b);

		if (this.isIE)
			return this.xmlDoc.xml;
		else
			return new XMLSerializer().serializeToString(this.xmlDoc);
	},

	_convertToXML : function(n, xn) {
		var xd, el, i, l, cn, at, no, hc = false;

		if (tinyMCE.isRealIE && this._isDuplicate(n))
			return;

		xd = this.xmlDoc;

		switch (n.nodeType) {
			case 1: // Element
				hc = n.hasChildNodes();

				el = xd.createElement(n.nodeName.toLowerCase());

				at = n.attributes;
				for (i=at.length-1; i>-1; i--) {
					no = at[i];

					if (no.specified && no.nodeValue)
						el.setAttribute(no.nodeName.toLowerCase(), no.nodeValue);
				}

				if (!hc && !this.closeElementsRe.test(n.nodeName))
					el.appendChild(xd.createTextNode(""));

				xn = xn.appendChild(el);
				break;

			case 3: // Text
				xn.appendChild(xd.createTextNode(n.nodeValue));
				return;

			case 8: // Comment
				xn.appendChild(xd.createComment(n.nodeValue));
				return;
		}

		if (hc) {
			cn = n.childNodes;

			for (i=0, l=cn.length; i<l; i++)
				this._convertToXML(cn[i], xn);
		}
	},

	serializeNodeAsHTML : function(n, inn) {
		var en, no, h = '', i, l, t, st, r, cn, va = false, f = false, at, hc, cr, nn;

		if (!this.rulesDone)
			this._setupRules(); // Will initialize cleanup rules

		if (tinyMCE.isRealIE && this._isDuplicate(n))
			return '';

		// Skip non valid child elements
		if (n.parentNode && this.childRules != null) {
			cr = this.childRules[n.parentNode.nodeName];

			if (typeof(cr) != "undefined" && !cr.test(n.nodeName)) {
				st = true;
				t = null;
			}
		}

		switch (n.nodeType) {
			case 1: // Element
				hc = n.hasChildNodes();

				if (st)
					break;

				nn = n.nodeName;

				if (tinyMCE.isRealIE) {
					// MSIE sometimes produces <//tag>
					if (n.nodeName.indexOf('/') != -1)
						break;

					// MSIE has it's NS in a separate attrib
					if (n.scopeName && n.scopeName != 'HTML')
						nn = n.scopeName.toUpperCase() + ':' + nn.toUpperCase();
				} else if (tinyMCE.isOpera && nn.indexOf(':') > 0)
					nn = nn.toUpperCase();

				// Convert fonts to spans
				if (this.settings.convert_fonts_to_spans) {
					// On get content FONT -> SPAN
					if (this.settings.on_save && nn == 'FONT')
						nn = 'SPAN';

					// On insert content SPAN -> FONT
					if (!this.settings.on_save && nn == 'SPAN')
						nn = 'FONT';
				}

				if (this.vElementsRe.test(nn) && (!this.iveRe || !this.iveRe.test(nn)) && !inn) {
					va = true;

					r = this.rules[nn];
					if (!r) {
						at = this.rules;
						for (no in at) {
							if (at[no] && at[no].validRe.test(nn)) {
								r = at[no];
								break;
							}
						}
					}

					en = r.isWild ? nn.toLowerCase() : r.oTagName;
					f = r.fill;

					if (r.removeEmpty && !hc)
						return "";

					t = '<' + en;

					if (r.vAttribsReIsWild) {
						// Serialize wildcard attributes
						at = n.attributes;
						for (i=at.length-1; i>-1; i--) {
							no = at[i];
							if (no.specified && r.vAttribsRe.test(no.nodeName))
								t += this._serializeAttribute(n, r, no.nodeName);
						}
					} else {
						// Serialize specific attributes
						for (i=r.vAttribs.length-1; i>-1; i--)
							t += this._serializeAttribute(n, r, r.vAttribs[i]);
					}

					// Serialize mce_ atts
					if (!this.settings.on_save) {
						at = this.mceAttribs;

						for (no in at) {
							if (at[no])
								t += this._serializeAttribute(n, r, at[no]);
						}
					}

					// Check for required attribs
					if (r.reqAttribsRe && !t.match(r.reqAttribsRe))
						t = null;

					// Close these
					if (t != null && this.closeElementsRe.test(nn))
						return t + ' />';

					if (t != null)
						h += t + '>';

					if (this.isIE && this.codeElementsRe.test(nn))
						h += n.innerHTML;
				}
			break;

			case 3: // Text
				if (st)
					break;

				if (n.parentNode && this.codeElementsRe.test(n.parentNode.nodeName))
					return this.isIE ? '' : n.nodeValue;

				return this.xmlEncode(n.nodeValue);

			case 8: // Comment
				if (st)
					break;

				return "<!--" + this._trimComment(n.nodeValue) + "-->";
		}

		if (hc) {
			cn = n.childNodes;

			for (i=0, l=cn.length; i<l; i++)
				h += this.serializeNodeAsHTML(cn[i]);
		}

		// Fill empty nodes
		if (f && !hc)
			h += this.fillStr;

		// End element
		if (t != null && va)
			h += '</' + en + '>';

		return h;
	},

	_serializeAttribute : function(n, r, an) {
		var av = '', t, os = this.settings.on_save;

		if (os && (an.indexOf('mce_') == 0 || an.indexOf('_moz') == 0))
			return '';

		if (os && this.mceAttribs[an])
			av = this._getAttrib(n, this.mceAttribs[an]);

		if (av.length == 0)
			av = this._getAttrib(n, an);

		if (av.length == 0 && r.defaultAttribs && (t = r.defaultAttribs[an])) {
			av = t;

			if (av == "mce_empty")
				return " " + an + '=""';
		}

		if (r.forceAttribs && (t = r.forceAttribs[an]))
			av = t;

		if (os && av.length != 0 && /^(src|href|longdesc)$/.test(an))
			av = this._urlConverter(this, n, av);

		if (av.length != 0 && r.validAttribValues && r.validAttribValues[an] && !r.validAttribValues[an].test(av))
			return "";

		if (av.length != 0 && av == "{$uid}")
			av = "uid_" + (this.idCount++);

		if (av.length != 0) {
			if (an.indexOf('on') != 0)
				av = this.xmlEncode(av, 1);

			return " " + an + "=" + '"' + av + '"';
		}

		return "";
	},

	formatHTML : function(h) {
		var s = this.settings, p = '', i = 0, li = 0, o = '', l;

		// Replace BR in pre elements to \n
		h = h.replace(/<pre([^>]*)>(.*?)<\/pre>/gi, function (a, b, c) {
			c = c.replace(/<br\s*\/>/gi, '\n');
			return '<pre' + b + '>' + c + '</pre>';
		});

		h = h.replace(/\r/g, ''); // Windows sux, isn't carriage return a thing of the past :)
		h = '\n' + h;
		h = h.replace(new RegExp('\\n\\s+', 'gi'), '\n'); // Remove previous formatting
		h = h.replace(this.nlBeforeRe, '\n<$1$2>');
		h = h.replace(this.nlAfterRe, '<$1$2>\n');
		h = h.replace(this.nlBeforeAfterRe, '\n<$1$2$3>\n');
		h += '\n';

		//tinyMCE.debug(h);

		while ((i = h.indexOf('\n', i + 1)) != -1) {
			if ((l = h.substring(li + 1, i)).length != 0) {
				if (this.ouRe.test(l) && p.length >= s.indent_levels)
					p = p.substring(s.indent_levels);

				o += p + l + '\n';
	
				if (this.inRe.test(l))
					p += this.inStr;
			}

			li = i;
		}

		//tinyMCE.debug(h);

		return o;
	},

	xmlEncode : function(s) {
		var cl = this, re = this.xmlEncodeRe;

		if (!this.entitiesDone)
			this._setupEntities(); // Will intialize lookup table

		switch (this.settings.entity_encoding) {
			case "raw":
				return tinyMCE.xmlEncode(s);

			case "named":
				return s.replace(re, function (c) {
					var b = cl.entities[c.charCodeAt(0)];

					return b ? '&' + b + ';' : c;
				});

			case "numeric":
				return s.replace(re, function (c) {
					return '&#' + c.charCodeAt(0) + ';';
				});
		}

		return s;
	},

	split : function(re, s) {
		var i, l, o = [], c = s.split(re);

		for (i=0, l=c.length; i<l; i++) {
			if (c[i] !== '')
				o[i] = c[i];
		}

		return o;
	},

	_trimComment : function(s) {
		// Remove mce_src, mce_href
		s = s.replace(new RegExp('\\smce_src=\"[^\"]*\"', 'gi'), "");
		s = s.replace(new RegExp('\\smce_href=\"[^\"]*\"', 'gi'), "");

		return s;
	},

	_getAttrib : function(e, n, d) {
		var v, ex, nn;

		if (typeof(d) == "undefined")
			d = "";

		if (!e || e.nodeType != 1)
			return d;

		try {
			v = e.getAttribute(n, 0);
		} catch (ex) {
			// IE 7 may cast exception on invalid attributes
			v = e.getAttribute(n, 2);
		}

		if (n == "class" && !v)
			v = e.className;

		if (this.isIE) {
			if (n == "http-equiv")
				v = e.httpEquiv;

			nn = e.nodeName;

			// Skip the default values that IE returns
			if (nn == "FORM" && n == "enctype" && v == "application/x-www-form-urlencoded")
				v = "";

			if (nn == "INPUT" && n == "size" && v == "20")
				v = "";

			if (nn == "INPUT" && n == "maxlength" && v == "2147483647")
				v = "";

			// Images
			if (n == "width" || n == "height")
				v = e.getAttribute(n, 2);
		}

		if (n == 'style' && v) {
			if (!tinyMCE.isOpera)
				v = e.style.cssText;

			v = tinyMCE.serializeStyle(tinyMCE.parseStyle(v));
		}

		if (this.settings.on_save && n.indexOf('on') != -1 && this.settings.on_save && v && v !== '')
			v = tinyMCE.cleanupEventStr(v);

		return (v && v !== '') ? '' + v : d;
	},

	_urlConverter : function(c, n, v) {
		if (!c.settings.on_save)
			return tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings.base_href, v);
		else if (tinyMCE.getParam('convert_urls')) {
			if (!this.urlConverter)
				this.urlConverter = eval(tinyMCE.settings.urlconverter_callback);

			return this.urlConverter(v, n, true);
		}

		return v;
	},

	_arrayToRe : function(a, op, be, af) {
		var i, r;

		op = typeof(op) == "undefined" ? "gi" : op;
		be = typeof(be) == "undefined" ? "^(" : be;
		af = typeof(af) == "undefined" ? ")$" : af;

		r = be;

		for (i=0; i<a.length; i++)
			r += this._wildcardToRe(a[i]) + (i != a.length-1 ? "|" : "");

		r += af;

		return new RegExp(r, op);
	},

	_wildcardToRe : function(s) {
		s = s.replace(/\?/g, '(\\S?)');
		s = s.replace(/\+/g, '(\\S+)');
		s = s.replace(/\*/g, '(\\S*)');

		return s;
	},

	_setupEntities : function() {
		var n, a, i, s = this.settings;

		// Setup entities
		if (s.entity_encoding == "named") {
			n = tinyMCE.clearArray([]);
			a = this.split(',', s.entities);
			for (i=0; i<a.length; i+=2)
				n[a[i]] = a[i+1];

			this.entities = n;
		}

		this.entitiesDone = true;
	},

	_setupRules : function() {
		var s = this.settings;

		// Setup default rule
		this.addRuleStr(s.valid_elements);
		this.addRuleStr(s.extended_valid_elements);
		this.addChildRemoveRuleStr(s.valid_child_elements);

		this.rulesDone = true;
	},

	_isDuplicate : function(n) {
		var i, l, sn;

		if (!this.settings.fix_content_duplication)
			return false;

		if (tinyMCE.isRealIE && n.nodeType == 1) {
			// Mark elements
			if (n.mce_serialized == this.serializationId)
				return true;

			n.setAttribute('mce_serialized', this.serializationId);
		} else {
			sn = this.serializedNodes;

			// Search lookup table for text nodes  and comments
			for (i=0, l = sn.length; i<l; i++) {
				if (sn[i] == n)
					return true;
			}

			sn.push(n);
		}

		return false;
	}

	};

/* file:jscripts/tiny_mce/classes/TinyMCE_DOMUtils.class.js */

tinyMCE.add(TinyMCE_Engine, {
	createTagHTML : function(tn, a, h) {
		var o = '', f = tinyMCE.xmlEncode, n;

		o = '<' + tn;

		if (a) {
			for (n in a) {
				if (typeof(a[n]) != 'function' && a[n] != null)
					o += ' ' + f(n) + '="' + f('' + a[n]) + '"';
			}
		}

		o += !h ? ' />' : '>' + h + '</' + tn + '>';

		return o;
	},

	createTag : function(d, tn, a, h) {
		var o = d.createElement(tn), n;

		if (a) {
			for (n in a) {
				if (typeof(a[n]) != 'function' && a[n] != null)
					tinyMCE.setAttrib(o, n, a[n]);
			}
		}

		if (h)
			o.innerHTML = h;

		return o;
	},

	getElementByAttributeValue : function(n, e, a, v) {
		return (n = this.getElementsByAttributeValue(n, e, a, v)).length == 0 ? null : n[0];
	},

	getElementsByAttributeValue : function(n, e, a, v) {
		var i, nl = n.getElementsByTagName(e), o = [];

		for (i=0; i<nl.length; i++) {
			if (tinyMCE.getAttrib(nl[i], a).indexOf(v) != -1)
				o[o.length] = nl[i];
		}

		return o;
	},

	isBlockElement : function(n) {
		return n != null && n.nodeType == 1 && this.blockRegExp.test(n.nodeName);
	},

	getParentBlockElement : function(n, r) {
		return this.getParentNode(n, function(n) {
			return tinyMCE.isBlockElement(n);
		}, r);

		return null;
	},

	insertAfter : function(n, r){
		if (r.nextSibling)
			r.parentNode.insertBefore(n, r.nextSibling);
		else
			r.parentNode.appendChild(n);
	},

	setInnerHTML : function(e, h) {
		var i, nl, n;

		// Convert all strong/em to b/i in Gecko
		if (tinyMCE.isGecko) {
			h = h.replace(/<embed([^>]*)>/gi, '<tmpembed$1>');
			h = h.replace(/<em([^>]*)>/gi, '<i$1>');
			h = h.replace(/<tmpembed([^>]*)>/gi, '<embed$1>');
			h = h.replace(/<strong([^>]*)>/gi, '<b$1>');
			h = h.replace(/<\/strong>/gi, '</b>');
			h = h.replace(/<\/em>/gi, '</i>');
		}

		if (tinyMCE.isRealIE) {
			// Since MSIE handles invalid HTML better that valid XHTML we
			// need to make some things invalid. <hr /> gets converted to <hr>.
			h = h.replace(/\s\/>/g, '>');

			// Since MSIE auto generated emtpy P tags some times we must tell it to keep the real ones
			h = h.replace(/<p([^>]*)>\u00A0?<\/p>/gi, '<p$1 mce_keep="true">&nbsp;</p>'); // Keep empty paragraphs
			h = h.replace(/<p([^>]*)>\s*&nbsp;\s*<\/p>/gi, '<p$1 mce_keep="true">&nbsp;</p>'); // Keep empty paragraphs
			h = h.replace(/<p([^>]*)>\s+<\/p>/gi, '<p$1 mce_keep="true">&nbsp;</p>'); // Keep empty paragraphs

			// Remove first comment
			e.innerHTML = tinyMCE.uniqueTag + h;
			e.firstChild.removeNode(true);

			// Remove weird auto generated empty paragraphs unless it's supposed to be there
			nl = e.getElementsByTagName("p");
			for (i=nl.length-1; i>=0; i--) {
				n = nl[i];

				if (n.nodeName == 'P' && !n.hasChildNodes() && !n.mce_keep)
					n.parentNode.removeChild(n);
			}
		} else {
			h = this.fixGeckoBaseHREFBug(1, e, h);
			e.innerHTML = h;
			this.fixGeckoBaseHREFBug(2, e, h);
		}
	},

	getOuterHTML : function(e) {
		var d;

		if (tinyMCE.isIE)
			return e.outerHTML;

		d = e.ownerDocument.createElement("body");
		d.appendChild(e.cloneNode(true));

		return d.innerHTML;
	},

	setOuterHTML : function(e, h, d) {
		var d = typeof(d) == "undefined" ? e.ownerDocument : d, i, nl, t;

		if (tinyMCE.isIE && e.nodeType == 1)
			e.outerHTML = h;
		else {
			t = d.createElement("body");
			t.innerHTML = h;

			for (i=0, nl=t.childNodes; i<nl.length; i++)
				e.parentNode.insertBefore(nl[i].cloneNode(true), e);

			e.parentNode.removeChild(e);
		}
	},

	_getElementById : function(id, d) {
		var e, i, j, f;

		if (typeof(d) == "undefined")
			d = document;

		e = d.getElementById(id);
		if (!e) {
			f = d.forms;

			for (i=0; i<f.length; i++) {
				for (j=0; j<f[i].elements.length; j++) {
					if (f[i].elements[j].name == id) {
						e = f[i].elements[j];
						break;
					}
				}
			}
		}

		return e;
	},

	getNodeTree : function(n, na, t, nn) {
		return this.selectNodes(n, function(n) {
			return (!t || n.nodeType == t) && (!nn || n.nodeName == nn);
		}, na ? na : []);
	},

	getParentElement : function(n, na, f, r) {
		var re = na ? new RegExp('^(' + na.toUpperCase().replace(/,/g, '|') + ')$') : 0, v;

		// Compatiblity with old scripts where f param was a attribute string
		if (f && typeof(f) == 'string')
			return this.getParentElement(n, na, function(no) {return tinyMCE.getAttrib(no, f) !== '';});

		return this.getParentNode(n, function(n) {
			return ((n.nodeType == 1 && !re) || (re && re.test(n.nodeName))) && (!f || f(n));
		}, r);
	},

	getParentNode : function(n, f, r) {
		while (n) {
			if (n == r)
				return null;

			if (f(n))
				return n;

			n = n.parentNode;
		}

		return null;
	},

	getAttrib : function(elm, name, dv) {
		var v;

		if (typeof(dv) == "undefined")
			dv = "";

		// Not a element
		if (!elm || elm.nodeType != 1)
			return dv;

		try {
			v = elm.getAttribute(name, 0);
		} catch (ex) {
			// IE 7 may cast exception on invalid attributes
			v = elm.getAttribute(name, 2);
		}

		// Try className for class attrib
		if (name == "class" && !v)
			v = elm.className;

		// Workaround for a issue with Firefox 1.5rc2+
		if (tinyMCE.isGecko) {
			if (name == "src" && elm.src != null && elm.src !== '')
				v = elm.src;

			// Workaround for a issue with Firefox 1.5rc2+
			if (name == "href" && elm.href != null && elm.href !== '')
				v = elm.href;
		} else if (tinyMCE.isIE) {
			switch (name) {
				case "http-equiv":
					v = elm.httpEquiv;
					break;

				case "width":
				case "height":
					v = elm.getAttribute(name, 2);
					break;
			}
		}

		if (name == "style" && !tinyMCE.isOpera)
			v = elm.style.cssText;

		return (v && v !== '') ? v : dv;
	},

	setAttrib : function(el, name, va, fix) {
		if (typeof(va) == "number" && va != null)
			va = "" + va;

		if (fix) {
			if (va == null)
				va = "";

			va = va.replace(/[^0-9%]/g, '');
		}

		if (name == "style")
			el.style.cssText = va;

		if (name == "class")
			el.className = va;

		if (va != null && va !== '' && va != -1)
			el.setAttribute(name, va);
		else
			el.removeAttribute(name);
	},

	setStyleAttrib : function(e, n, v) {
		e.style[n] = v;

		// Style attrib deleted in IE
		if (tinyMCE.isIE && v == null || v == '') {
			v = tinyMCE.serializeStyle(tinyMCE.parseStyle(e.style.cssText));
			e.style.cssText = v;
			e.setAttribute("style", v);
		}
	},

	switchClass : function(ei, c) {
		var e;

		if (tinyMCE.switchClassCache[ei])
			e = tinyMCE.switchClassCache[ei];
		else
			e = tinyMCE.switchClassCache[ei] = document.getElementById(ei);

		if (e) {
			// Keep tile mode
			if (tinyMCE.settings.button_tile_map && e.className && e.className.indexOf('mceTiledButton') == 0)
				c = 'mceTiledButton ' + c;

			e.className = c;
		}
	},

	getAbsPosition : function(n, cn) {
		var l = 0, t = 0;

		while (n && n != cn) {
			l += n.offsetLeft;
			t += n.offsetTop;
			n = n.offsetParent;
		}

		return {absLeft : l, absTop : t};
	},

	prevNode : function(e, n) {
		var a = n.split(','), i;

		while ((e = e.previousSibling) != null) {
			for (i=0; i<a.length; i++) {
				if (e.nodeName == a[i])
					return e;
			}
		}

		return null;
	},

	nextNode : function(e, n) {
		var a = n.split(','), i;

		while ((e = e.nextSibling) != null) {
			for (i=0; i<a.length; i++) {
				if (e.nodeName == a[i])
					return e;
			}
		}

		return null;
	},

	selectElements : function(n, na, f) {
		var i, a = [], nl, x;

		for (x=0, na = na.split(','); x<na.length; x++)
			for (i=0, nl = n.getElementsByTagName(na[x]); i<nl.length; i++)
				(!f || f(nl[i])) && a.push(nl[i]);

		return a;
	},

	selectNodes : function(n, f, a) {
		var i;

		if (!a)
			a = [];

		if (f(n))
			a[a.length] = n;

		if (n.hasChildNodes()) {
			for (i=0; i<n.childNodes.length; i++)
				tinyMCE.selectNodes(n.childNodes[i], f, a);
		}

		return a;
	},

	addCSSClass : function(e, c, b) {
		var o = this.removeCSSClass(e, c);
		return e.className = b ? c + (o !== '' ? (' ' + o) : '') : (o !== '' ? (o + ' ') : '') + c;
	},

	removeCSSClass : function(e, c) {
		c = e.className.replace(new RegExp("(^|\\s+)" + c + "(\\s+|$)"), ' ');
		return e.className = c != ' ' ? c : '';
	},

	hasCSSClass : function(n, c) {
		return new RegExp('\\b' + c + '\\b', 'g').test(n.className);
	},

	renameElement : function(e, n, d) {
		var ne, i, ar;

		d = typeof(d) == "undefined" ? tinyMCE.selectedInstance.getDoc() : d;

		if (e) {
			ne = d.createElement(n);

			ar = e.attributes;
			for (i=ar.length-1; i>-1; i--) {
				if (ar[i].specified && ar[i].nodeValue)
					ne.setAttribute(ar[i].nodeName.toLowerCase(), ar[i].nodeValue);
			}

			ar = e.childNodes;
			for (i=0; i<ar.length; i++)
				ne.appendChild(ar[i].cloneNode(true));

			e.parentNode.replaceChild(ne, e);
		}
	},

	getViewPort : function(w) {
		var d = w.document, m = d.compatMode == 'CSS1Compat', b = d.body, de = d.documentElement;

		return {
			left : w.pageXOffset || (m ? de.scrollLeft : b.scrollLeft),
			top : w.pageYOffset || (m ? de.scrollTop : b.scrollTop),
			width : w.innerWidth || (m ? de.clientWidth : b.clientWidth),
			height : w.innerHeight || (m ? de.clientHeight : b.clientHeight)
		};
	},

	getStyle : function(n, na, d) {
		if (!n)
			return false;

		// Gecko
		if (tinyMCE.isGecko && n.ownerDocument.defaultView) {
			try {
				return n.ownerDocument.defaultView.getComputedStyle(n, null).getPropertyValue(na);
			} catch (n) {
				// Old safari might fail
				return null;
			}
		}

		// Camelcase it, if needed
		na = na.replace(/-(\D)/g, function(a, b){
			return b.toUpperCase();
		});

		// IE & Opera
		if (n.currentStyle)
			return n.currentStyle[na];

		return false;
	}

	});

/* file:jscripts/tiny_mce/classes/TinyMCE_URL.class.js */

tinyMCE.add(TinyMCE_Engine, {
	parseURL : function(url_str) {
		var urlParts = [], i, pos, lastPos, chr;

		if (url_str) {
			// Parse protocol part
			pos = url_str.indexOf('://');
			if (pos != -1) {
				urlParts.protocol = url_str.substring(0, pos);
				lastPos = pos + 3;
			}

			// Find port or path start
			for (i=lastPos; i<url_str.length; i++) {
				chr = url_str.charAt(i);

				if (chr == ':')
					break;

				if (chr == '/')
					break;
			}
			pos = i;

			// Get host
			urlParts.host = url_str.substring(lastPos, pos);

			// Get port
			urlParts.port = "";
			lastPos = pos;
			if (url_str.charAt(pos) == ':') {
				pos = url_str.indexOf('/', lastPos);
				urlParts.port = url_str.substring(lastPos+1, pos);
			}

			// Get path
			lastPos = pos;
			pos = url_str.indexOf('?', lastPos);

			if (pos == -1)
				pos = url_str.indexOf('#', lastPos);

			if (pos == -1)
				pos = url_str.length;

			urlParts.path = url_str.substring(lastPos, pos);

			// Get query
			lastPos = pos;
			if (url_str.charAt(pos) == '?') {
				pos = url_str.indexOf('#');
				pos = (pos == -1) ? url_str.length : pos;
				urlParts.query = url_str.substring(lastPos+1, pos);
			}

			// Get anchor
			lastPos = pos;
			if (url_str.charAt(pos) == '#') {
				pos = url_str.length;
				urlParts.anchor = url_str.substring(lastPos+1, pos);
			}
		}

		return urlParts;
	},

	serializeURL : function(up) {
		var o = "";

		if (up.protocol)
			o += up.protocol + "://";

		if (up.host)
			o += up.host;

		if (up.port)
			o += ":" + up.port;

		if (up.path)
			o += up.path;

		if (up.query)
			o += "?" + up.query;

		if (up.anchor)
			o += "#" + up.anchor;

		return o;
	},

	convertAbsoluteURLToRelativeURL : function(base_url, url_to_relative) {
		var baseURL = this.parseURL(base_url), targetURL = this.parseURL(url_to_relative);
		var i, strTok1, strTok2, breakPoint = 0, outPath = "", forceSlash = false;
		var fileName, pos;

		if (targetURL.path == '')
			targetURL.path = "/";
		else
			forceSlash = true;

		// Crop away last path part
		base_url = baseURL.path.substring(0, baseURL.path.lastIndexOf('/'));
		strTok1 = base_url.split('/');
		strTok2 = targetURL.path.split('/');

		if (strTok1.length >= strTok2.length) {
			for (i=0; i<strTok1.length; i++) {
				if (i >= strTok2.length || strTok1[i] != strTok2[i]) {
					breakPoint = i + 1;
					break;
				}
			}
		}

		if (strTok1.length < strTok2.length) {
			for (i=0; i<strTok2.length; i++) {
				if (i >= strTok1.length || strTok1[i] != strTok2[i]) {
					breakPoint = i + 1;
					break;
				}
			}
		}

		if (breakPoint == 1)
			return targetURL.path;

		for (i=0; i<(strTok1.length-(breakPoint-1)); i++)
			outPath += "../";

		for (i=breakPoint-1; i<strTok2.length; i++) {
			if (i != (breakPoint-1))
				outPath += "/" + strTok2[i];
			else
				outPath += strTok2[i];
		}

		targetURL.protocol = null;
		targetURL.host = null;
		targetURL.port = null;
		targetURL.path = outPath == '' && forceSlash ? "/" : outPath;

		// Remove document prefix from local anchors
		fileName = baseURL.path;

		if ((pos = fileName.lastIndexOf('/')) != -1)
			fileName = fileName.substring(pos + 1);

		// Is local anchor
		if (fileName == targetURL.path && targetURL.anchor !== '')
			targetURL.path = "";

		// If empty and not local anchor force filename or slash
		if (targetURL.path == '' && !targetURL.anchor)
			targetURL.path = fileName !== '' ? fileName : "/";

		return this.serializeURL(targetURL);
	},

	convertRelativeToAbsoluteURL : function(base_url, relative_url) {
		var baseURL = this.parseURL(base_url), baseURLParts, relURLParts, newRelURLParts, numBack, relURL = this.parseURL(relative_url), i;
		var len, absPath, start, end, newBaseURLParts;

		if (relative_url == '' || relative_url.indexOf('://') != -1 || /^(mailto:|javascript:|#|\/)/.test(relative_url))
			return relative_url;

		// Split parts
		baseURLParts = baseURL.path.split('/');
		relURLParts = relURL.path.split('/');

		// Remove empty chunks
		newBaseURLParts = [];
		for (i=baseURLParts.length-1; i>=0; i--) {
			if (baseURLParts[i].length == 0)
				continue;

			newBaseURLParts[newBaseURLParts.length] = baseURLParts[i];
		}
		baseURLParts = newBaseURLParts.reverse();

		// Merge relURLParts chunks
		newRelURLParts = [];
		numBack = 0;
		for (i=relURLParts.length-1; i>=0; i--) {
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
		len = baseURLParts.length-numBack;
		absPath = (len <= 0 ? "" : "/") + baseURLParts.slice(0, len).join('/') + "/" + relURLParts.join('/');
		start = "";
		end = "";

		// Build output URL
		relURL.protocol = baseURL.protocol;
		relURL.host = baseURL.host;
		relURL.port = baseURL.port;

		// Re-add trailing slash if it's removed
		if (relURL.path.charAt(relURL.path.length-1) == "/")
			absPath += "/";

		relURL.path = absPath;

		return this.serializeURL(relURL);
	},

	convertURL : function(url, node, on_save) {
		var dl = document.location, start, portPart, urlParts, baseUrlParts, tmpUrlParts, curl;
		var prot = dl.protocol, host = dl.hostname, port = dl.port;

		// Pass through file protocol
		if (prot == "file:")
			return url;

		// Something is wrong, remove weirdness
		url = tinyMCE.regexpReplace(url, '(http|https):///', '/');

		// Mailto link or anchor (Pass through)
		if (url.indexOf('mailto:') != -1 || url.indexOf('javascript:') != -1 || /^[ \t\r\n\+]*[#\?]/.test(url))
			return url;

		// Fix relative/Mozilla
		if (!tinyMCE.isIE && !on_save && url.indexOf("://") == -1 && url.charAt(0) != '/')
			return tinyMCE.settings.base_href + url;

		// Handle relative URLs
		if (on_save && tinyMCE.getParam('relative_urls')) {
			curl = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings.base_href, url);
			if (curl.charAt(0) == '/')
				curl = tinyMCE.settings.document_base_prefix + curl;

			urlParts = tinyMCE.parseURL(curl);
			tmpUrlParts = tinyMCE.parseURL(tinyMCE.settings.document_base_url);

			// Force relative
			if (urlParts.host == tmpUrlParts.host && (urlParts.port == tmpUrlParts.port))
				return tinyMCE.convertAbsoluteURLToRelativeURL(tinyMCE.settings.document_base_url, curl);
		}

		// Handle absolute URLs
		if (!tinyMCE.getParam('relative_urls')) {
			urlParts = tinyMCE.parseURL(url);
			baseUrlParts = tinyMCE.parseURL(tinyMCE.settings.base_href);

			// Force absolute URLs from relative URLs
			url = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings.base_href, url);

			// If anchor and path is the same page
			if (urlParts.anchor && urlParts.path == baseUrlParts.path)
				return "#" + urlParts.anchor;
		}

		// Remove current domain
		if (tinyMCE.getParam('remove_script_host')) {
			start = "";
			portPart = "";

			if (port !== '')
				portPart = ":" + port;

			start = prot + "//" + host + portPart + "/";

			if (url.indexOf(start) == 0)
				url = url.substring(start.length-1);
		}

		return url;
	},

	convertAllRelativeURLs : function(body) {
		var i, elms, src, href, mhref, msrc;

		// Convert all image URL:s to absolute URL
		elms = body.getElementsByTagName("img");
		for (i=0; i<elms.length; i++) {
			src = tinyMCE.getAttrib(elms[i], 'src');

			msrc = tinyMCE.getAttrib(elms[i], 'mce_src');
			if (msrc !== '')
				src = msrc;

			if (src !== '') {
				src = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings.base_href, src);
				elms[i].setAttribute("src", src);
			}
		}

		// Convert all link URL:s to absolute URL
		elms = body.getElementsByTagName("a");
		for (i=0; i<elms.length; i++) {
			href = tinyMCE.getAttrib(elms[i], 'href');

			mhref = tinyMCE.getAttrib(elms[i], 'mce_href');
			if (mhref !== '')
				href = mhref;

			if (href && href !== '') {
				href = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings.base_href, href);
				elms[i].setAttribute("href", href);
			}
		}
	}

	});

/* file:jscripts/tiny_mce/classes/TinyMCE_Array.class.js */

tinyMCE.add(TinyMCE_Engine, {
	clearArray : function(a) {
		var n;

		for (n in a)
			a[n] = null;

		return a;
	},

	explode : function(d, s) {
		var ar = s.split(d), oar = [], i;

		for (i = 0; i<ar.length; i++) {
			if (ar[i] !== '')
				oar[oar.length] = ar[i];
		}

		return oar;
	}
});

/* file:jscripts/tiny_mce/classes/TinyMCE_Event.class.js */

tinyMCE.add(TinyMCE_Engine, {
	_setEventsEnabled : function(node, state) {
		var evs, x, y, elms, i, event;
		var events = ['onfocus','onblur','onclick','ondblclick',
					'onmousedown','onmouseup','onmouseover','onmousemove',
					'onmouseout','onkeypress','onkeydown','onkeydown','onkeyup'];

		evs = tinyMCE.settings.event_elements.split(',');
		for (y=0; y<evs.length; y++){
			elms = node.getElementsByTagName(evs[y]);
			for (i=0; i<elms.length; i++) {
				event = "";

				for (x=0; x<events.length; x++) {
					if ((event = tinyMCE.getAttrib(elms[i], events[x])) !== '') {
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
	},

	_eventPatch : function(editor_id) {
		var n, inst, win, e;

		// Remove odd, error
		if (typeof(tinyMCE) == "undefined")
			return true;

		try {
			// Try selected instance first
			if (tinyMCE.selectedInstance) {
				win = tinyMCE.selectedInstance.getWin();

				if (win && win.event) {
					e = win.event;

					if (!e.target)
						e.target = e.srcElement;

					TinyMCE_Engine.prototype.handleEvent(e);
					return;
				}
			}

			// Search for it
			for (n in tinyMCE.instances) {
				inst = tinyMCE.instances[n];

				if (!tinyMCE.isInstance(inst))
					continue;

				inst.select();
				win = inst.getWin();

				if (win && win.event) {
					e = win.event;

					if (!e.target)
						e.target = e.srcElement;

					TinyMCE_Engine.prototype.handleEvent(e);
					return;
				}
			}
		} catch (ex) {
			// Ignore error if iframe is pointing to external URL
		}
	},

	findEvent : function(e) {
		var n, inst;

		if (e)
			return e;

		for (n in tinyMCE.instances) {
			inst = tinyMCE.instances[n];

			if (tinyMCE.isInstance(inst) && inst.getWin().event)
				return inst.getWin().event;
		}

		return null;
	},

	unloadHandler : function() {
		tinyMCE.triggerSave(true, true);
	},

	addEventHandlers : function(inst) {
		this.setEventHandlers(inst, 1);
	},

	setEventHandlers : function(inst, s) {
		var doc = inst.getDoc(), ie, ot, i, f = s ? tinyMCE.addEvent : tinyMCE.removeEvent;

		ie = ['keypress', 'keyup', 'keydown', 'click', 'mouseup', 'mousedown', 'controlselect', 'dblclick'];
		ot = ['keypress', 'keyup', 'keydown', 'click', 'mouseup', 'mousedown', 'focus', 'blur', 'dragdrop'];

		inst.switchSettings();

		if (tinyMCE.isIE) {
			for (i=0; i<ie.length; i++)
				f(doc, ie[i], TinyMCE_Engine.prototype._eventPatch);
		} else {
			for (i=0; i<ot.length; i++)
				f(doc, ot[i], tinyMCE.handleEvent);

			// Force designmode
			try {
				doc.designMode = "On";
			} catch (e) {
				// Ignore
			}
		}
	},

	onMouseMove : function() {
		var inst, lh;

		// Fix for IE7 bug where it's not restoring hover on anchors correctly
		if (tinyMCE.lastHover) {
			lh = tinyMCE.lastHover;

			// Call out on menus and refresh class on normal buttons
			if (lh.className.indexOf('mceMenu') != -1)
				tinyMCE._menuButtonEvent('out', lh);
			else
				lh.className = lh.className;

			tinyMCE.lastHover = null;
		}

		if (!tinyMCE.hasMouseMoved) {
			inst = tinyMCE.selectedInstance;

			// Workaround for bug #1437457 (Odd MSIE bug)
			if (inst.isFocused) {
				inst.undoBookmark = inst.selection.getBookmark();
				tinyMCE.hasMouseMoved = true;
			}
		}

	//	tinyMCE.cancelEvent(inst.getWin().event);
	//	return false;
	},

	cancelEvent : function(e) {
		if (!e)
			return false;

		if (tinyMCE.isIE) {
			e.returnValue = false;
			e.cancelBubble = true;
		} else {
			e.preventDefault();
			e.stopPropagation && e.stopPropagation();
		}

		return false;
	},

	addEvent : function(o, n, h) {
		// Add cleanup for all non unload events
		if (n != 'unload') {
			function clean() {
				var ex;

				try {
					tinyMCE.removeEvent(o, n, h);
					tinyMCE.removeEvent(window, 'unload', clean);
					o = n = h = null;
				} catch (ex) {
					// IE may produce access denied exception on unload
				}
			}

			// Add memory cleaner
			tinyMCE.addEvent(window, 'unload', clean);
		}

		if (o.attachEvent)
			o.attachEvent("on" + n, h);
		else
			o.addEventListener(n, h, false);
	},

	removeEvent : function(o, n, h) {
		if (o.detachEvent)
			o.detachEvent("on" + n, h);
		else
			o.removeEventListener(n, h, false);
	},

	addSelectAccessibility : function(e, s, w) {
		// Add event handlers 
		if (!s._isAccessible) {
			s.onkeydown = tinyMCE.accessibleEventHandler;
			s.onblur = tinyMCE.accessibleEventHandler;
			s._isAccessible = true;
			s._win = w;
		}

		return false;
	},

	accessibleEventHandler : function(e) {
		var elm, win = this._win;

		e = tinyMCE.isIE ? win.event : e;
		elm = tinyMCE.isIE ? e.srcElement : e.target;

		// Unpiggyback onchange on blur
		if (e.type == "blur") {
			if (elm.oldonchange) {
				elm.onchange = elm.oldonchange;
				elm.oldonchange = null;
			}

			return true;
		}

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
			return false;
		}

		return true;
	},

	_resetIframeHeight : function() {
		var ife;

		if (tinyMCE.isRealIE) {
			ife = tinyMCE.selectedInstance.iframeElement;

	/*		if (ife._oldWidth) {
				ife.style.width = ife._oldWidth;
				ife.width = ife._oldWidth;
			}*/

			if (ife._oldHeight) {
				ife.style.height = ife._oldHeight;
				ife.height = ife._oldHeight;
			}
		}
	}

	});

/* file:jscripts/tiny_mce/classes/TinyMCE_Selection.class.js */

function TinyMCE_Selection(inst) {
	this.instance = inst;
};

TinyMCE_Selection.prototype = {
	getSelectedHTML : function() {
		var inst = this.instance, e, r = this.getRng(), h;

		if (!r)
			return null;

		e = document.createElement("body");

		if (r.cloneContents)
			e.appendChild(r.cloneContents());
		else if (typeof(r.item) != 'undefined' || typeof(r.htmlText) != 'undefined')
			e.innerHTML = r.item ? r.item(0).outerHTML : r.htmlText;
		else
			e.innerHTML = r.toString(); // Failed, use text for now

		h = tinyMCE._cleanupHTML(inst, inst.contentDocument, inst.settings, e, e, false, true, false);

		// When editing always use fonts internaly
		//if (tinyMCE.getParam("convert_fonts_to_spans"))
		//	tinyMCE.convertSpansToFonts(inst.getDoc());

		return h;
	},

	getSelectedText : function() {
		var inst = this.instance, d, r, s, t;

		if (tinyMCE.isIE) {
			d = inst.getDoc();

			if (d.selection.type == "Text") {
				r = d.selection.createRange();
				t = r.text;
			} else
				t = '';
		} else {
			s = this.getSel();

			if (s && s.toString)
				t = s.toString();
			else
				t = '';
		}

		return t;
	},

	getBookmark : function(simple) {
		var inst = this.instance, rng = this.getRng(), doc = inst.getDoc(), b = inst.getBody();
		var trng, sx, sy, xx = -999999999, vp = inst.getViewPort();
		var sp, le, s, e, nl, i, si, ei, w;

		sx = vp.left;
		sy = vp.top;

		if (simple)
			return {rng : rng, scrollX : sx, scrollY : sy};

		if (tinyMCE.isRealIE) {
			if (rng.item) {
				e = rng.item(0);

				nl = b.getElementsByTagName(e.nodeName);
				for (i=0; i<nl.length; i++) {
					if (e == nl[i]) {
						sp = i;
						break;
					}
				}

				return {
					tag : e.nodeName,
					index : sp,
					scrollX : sx,
					scrollY : sy
				};
			} else {
				trng = doc.body.createTextRange();
				trng.moveToElementText(inst.getBody());
				trng.collapse(true);
				bp = Math.abs(trng.move('character', xx));

				trng = rng.duplicate();
				trng.collapse(true);
				sp = Math.abs(trng.move('character', xx));

				trng = rng.duplicate();
				trng.collapse(false);
				le = Math.abs(trng.move('character', xx)) - sp;

				return {
					start : sp - bp,
					length : le,
					scrollX : sx,
					scrollY : sy
				};
			}
		} else {
			s = this.getSel();
			e = this.getFocusElement();

			if (!s)
				return null;

			if (e && e.nodeName == 'IMG') {
				/*nl = b.getElementsByTagName('IMG');
				for (i=0; i<nl.length; i++) {
					if (e == nl[i]) {
						sp = i;
						break;
					}
				}*/

				return {
					start : -1,
					end : -1,
					index : sp,
					scrollX : sx,
					scrollY : sy
				};
			}

			// Caret or selection
			if (s.anchorNode == s.focusNode && s.anchorOffset == s.focusOffset) {
				e = this._getPosText(b, s.anchorNode, s.focusNode);

				if (!e)
					return {scrollX : sx, scrollY : sy};

				return {
					start : e.start + s.anchorOffset,
					end : e.end + s.focusOffset,
					scrollX : sx,
					scrollY : sy
				};
			} else {
				e = this._getPosText(b, rng.startContainer, rng.endContainer);

				if (!e)
					return {scrollX : sx, scrollY : sy};

				return {
					start : e.start + rng.startOffset,
					end : e.end + rng.endOffset,
					scrollX : sx,
					scrollY : sy
				};
			}
		}

		return null;
	},

	moveToBookmark : function(bookmark) {
		var inst = this.instance, rng, nl, i, ex, b = inst.getBody(), sd;
		var doc = inst.getDoc(), win = inst.getWin(), sel = this.getSel();

		if (!bookmark)
			return false;

		if (tinyMCE.isSafari && bookmark.rng) {
			sel.setBaseAndExtent(bookmark.rng.startContainer, bookmark.rng.startOffset, bookmark.rng.endContainer, bookmark.rng.endOffset);
			return true;
		}

		if (tinyMCE.isRealIE) {
			if (bookmark.rng) {
				try {
					bookmark.rng.select();
				} catch (ex) {
					// Ignore
				}

				return true;
			}

			win.focus();

			if (bookmark.tag) {
				rng = b.createControlRange();

				nl = b.getElementsByTagName(bookmark.tag);

				if (nl.length > bookmark.index) {
					try {
						rng.addElement(nl[bookmark.index]);
					} catch (ex) {
						// Might be thrown if the node no longer exists
					}
				}
			} else {
				// Try/catch needed since this operation breaks when TinyMCE is placed in hidden divs/tabs
				try {
					// Incorrect bookmark
					if (bookmark.start < 0)
						return true;

					rng = inst.getSel().createRange();
					rng.moveToElementText(inst.getBody());
					rng.collapse(true);
					rng.moveStart('character', bookmark.start);
					rng.moveEnd('character', bookmark.length);
				} catch (ex) {
					return true;
				}
			}

			rng.select();

			win.scrollTo(bookmark.scrollX, bookmark.scrollY);
			return true;
		}

		if (tinyMCE.isGecko || tinyMCE.isOpera) {
			if (!sel)
				return false;

			if (bookmark.rng) {
				sel.removeAllRanges();
				sel.addRange(bookmark.rng);
			}

			if (bookmark.start != -1 && bookmark.end != -1) {
				try {
					sd = this._getTextPos(b, bookmark.start, bookmark.end);
					rng = doc.createRange();
					rng.setStart(sd.startNode, sd.startOffset);
					rng.setEnd(sd.endNode, sd.endOffset);
					sel.removeAllRanges();
					sel.addRange(rng);

					if (!tinyMCE.isOpera)
						win.focus();
				} catch (ex) {
					// Ignore
				}
			}

			/*
			if (typeof(bookmark.index) != 'undefined') {
				tinyMCE.selectElements(b, 'IMG', function (n) {
					if (bookmark.index-- == 0) {
						// Select image in Gecko here
					}

					return false;
				});
			}
			*/

			win.scrollTo(bookmark.scrollX, bookmark.scrollY);
			return true;
		}

		return false;
	},

	_getPosText : function(r, sn, en) {
		var w = document.createTreeWalker(r, NodeFilter.SHOW_TEXT, null, false), n, p = 0, d = {};

		while ((n = w.nextNode()) != null) {
			if (n == sn)
				d.start = p;

			if (n == en) {
				d.end = p;
				return d;
			}

			p += n.nodeValue ? n.nodeValue.length : 0;
		}

		return null;
	},

	_getTextPos : function(r, sp, ep) {
		var w = document.createTreeWalker(r, NodeFilter.SHOW_TEXT, null, false), n, p = 0, d = {};

		while ((n = w.nextNode()) != null) {
			p += n.nodeValue ? n.nodeValue.length : 0;

			if (p >= sp && !d.startNode) {
				d.startNode = n;
				d.startOffset = sp - (p - n.nodeValue.length);
			}

			if (p >= ep) {
				d.endNode = n;
				d.endOffset = ep - (p - n.nodeValue.length);

				return d;
			}
		}

		return null;
	},

	selectNode : function(node, collapse, select_text_node, to_start) {
		var inst = this.instance, sel, rng, nodes;

		if (!node)
			return;

		if (typeof(collapse) == "undefined")
			collapse = true;

		if (typeof(select_text_node) == "undefined")
			select_text_node = false;

		if (typeof(to_start) == "undefined")
			to_start = true;

		if (inst.settings.auto_resize)
			inst.resizeToContent();

		if (tinyMCE.isRealIE) {
			rng = inst.getDoc().body.createTextRange();

			try {
				rng.moveToElementText(node);

				if (collapse)
					rng.collapse(to_start);

				rng.select();
			} catch (e) {
				// Throws illigal agrument in MSIE some times
			}
		} else {
			sel = this.getSel();

			if (!sel)
				return;

			if (tinyMCE.isSafari) {
				sel.setBaseAndExtent(node, 0, node, node.innerText.length);

				if (collapse) {
					if (to_start)
						sel.collapseToStart();
					else
						sel.collapseToEnd();
				}

				this.scrollToNode(node);

				return;
			}

			rng = inst.getDoc().createRange();

			if (select_text_node) {
				// Find first textnode in tree
				nodes = tinyMCE.getNodeTree(node, [], 3);
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
	},

	scrollToNode : function(node) {
		var inst = this.instance, w = inst.getWin(), vp = inst.getViewPort(), pos = tinyMCE.getAbsPosition(node), cvp, p, cwin;

		// Only scroll if out of visible area
		if (pos.absLeft < vp.left || pos.absLeft > vp.left + vp.width || pos.absTop < vp.top || pos.absTop > vp.top + (vp.height-25))
			w.scrollTo(pos.absLeft, pos.absTop - vp.height + 25);

		// Scroll container window
		if (inst.settings.auto_resize) {
			cwin = inst.getContainerWin();
			cvp = tinyMCE.getViewPort(cwin);
			p = this.getAbsPosition(node);

			if (p.absLeft < cvp.left || p.absLeft > cvp.left + cvp.width || p.absTop < cvp.top || p.absTop > cvp.top + cvp.height)
				cwin.scrollTo(p.absLeft, p.absTop - cvp.height + 25);
		}
	},

	getAbsPosition : function(n) {
		var pos = tinyMCE.getAbsPosition(n), ipos = tinyMCE.getAbsPosition(this.instance.iframeElement);

		return {
			absLeft : ipos.absLeft + pos.absLeft,
			absTop : ipos.absTop + pos.absTop
		};
	},

	getSel : function() {
		var inst = this.instance;

		if (tinyMCE.isRealIE)
			return inst.getDoc().selection;

		return inst.contentWindow.getSelection();
	},

	getRng : function() {
		var s = this.getSel();

		if (s == null)
			return null;

		if (tinyMCE.isRealIE)
			return s.createRange();

		if (tinyMCE.isSafari && !s.getRangeAt)
			return '' + window.getSelection();

		if (s.rangeCount > 0)
			return s.getRangeAt(0);

		return null;
	},

	isCollapsed : function() {
		var r = this.getRng();

		if (r.item)
			return false;

		return r.boundingWidth == 0 || this.getSel().isCollapsed;
	},

	collapse : function(b) {
		var r = this.getRng(), s = this.getSel();

		if (r.select) {
			r.collapse(b);
			r.select();
		} else {
			if (b)
				s.collapseToStart();
			else
				s.collapseToEnd();
		}
	},

	getFocusElement : function() {
		var inst = this.instance, doc, rng, sel, elm;

		if (tinyMCE.isRealIE) {
			doc = inst.getDoc();
			rng = doc.selection.createRange();

	//		if (rng.collapse)
	//			rng.collapse(true);

			elm = rng.item ? rng.item(0) : rng.parentElement();
		} else {
			if (!tinyMCE.isSafari && inst.isHidden())
				return inst.getBody();

			sel = this.getSel();
			rng = this.getRng();

			if (!sel || !rng)
				return null;

			elm = rng.commonAncestorContainer;
			//elm = (sel && sel.anchorNode) ? sel.anchorNode : null;

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
	}

	};

/* file:jscripts/tiny_mce/classes/TinyMCE_UndoRedo.class.js */

function TinyMCE_UndoRedo(inst) {
	this.instance = inst;
	this.undoLevels = [];
	this.undoIndex = 0;
	this.typingUndoIndex = -1;
	this.undoRedo = true;
};

TinyMCE_UndoRedo.prototype = {
	add : function(l) {
		var b, customUndoLevels, newHTML, inst = this.instance, i, ul, ur;

		if (l) {
			this.undoLevels[this.undoLevels.length] = l;
			return true;
		}

		if (this.typingUndoIndex != -1) {
			this.undoIndex = this.typingUndoIndex;

			if (tinyMCE.typingUndoIndex != -1)
				tinyMCE.undoIndex = tinyMCE.typingUndoIndex;
		}

		newHTML = tinyMCE.trim(inst.getBody().innerHTML);
		if (this.undoLevels[this.undoIndex] && newHTML != this.undoLevels[this.undoIndex].content) {
			//tinyMCE.debug(newHTML, this.undoLevels[this.undoIndex].content);

			// Is dirty again
			inst.isNotDirty = false;

			tinyMCE.dispatchCallback(inst, 'onchange_callback', 'onChange', inst);

			// Time to compress
			customUndoLevels = tinyMCE.settings.custom_undo_redo_levels;
			if (customUndoLevels != -1 && this.undoLevels.length > customUndoLevels) {
				for (i=0; i<this.undoLevels.length-1; i++)
					this.undoLevels[i] = this.undoLevels[i+1];

				this.undoLevels.length--;
				this.undoIndex--;

				// Todo: Implement global undo/redo logic here
			}

			b = inst.undoBookmark;

			if (!b)
				b = inst.selection.getBookmark();

			this.undoIndex++;
			this.undoLevels[this.undoIndex] = {
				content : newHTML,
				bookmark : b
			};

			// Remove all above from global undo/redo
			ul = tinyMCE.undoLevels;
			for (i=tinyMCE.undoIndex + 1; i<ul.length; i++) {
				ur = ul[i].undoRedo;

				if (ur.undoIndex == ur.undoLevels.length -1)
					ur.undoIndex--;

				ur.undoLevels.length--;
			}

			// Add global undo level
			tinyMCE.undoLevels[tinyMCE.undoIndex++] = inst;
			tinyMCE.undoLevels.length = tinyMCE.undoIndex;

			this.undoLevels.length = this.undoIndex + 1;

			return true;
		}

		return false;
	},

	undo : function() {
		var inst = this.instance;

		// Do undo
		if (this.undoIndex > 0) {
			this.undoIndex--;

			tinyMCE.setInnerHTML(inst.getBody(), this.undoLevels[this.undoIndex].content);
			inst.repaint();

			if (inst.settings.custom_undo_redo_restore_selection)
				inst.selection.moveToBookmark(this.undoLevels[this.undoIndex].bookmark);
		}
	},

	redo : function() {
		var inst = this.instance;

		tinyMCE.execCommand("mceEndTyping");

		if (this.undoIndex < (this.undoLevels.length-1)) {
			this.undoIndex++;

			tinyMCE.setInnerHTML(inst.getBody(), this.undoLevels[this.undoIndex].content);
			inst.repaint();

			if (inst.settings.custom_undo_redo_restore_selection)
				inst.selection.moveToBookmark(this.undoLevels[this.undoIndex].bookmark);
		}

		tinyMCE.triggerNodeChange();
	}

	};

/* file:jscripts/tiny_mce/classes/TinyMCE_ForceParagraphs.class.js */

var TinyMCE_ForceParagraphs = {
	_insertPara : function(inst, e) {
		var doc = inst.getDoc(), sel = inst.getSel(), body = inst.getBody(), win = inst.contentWindow, rng = sel.getRangeAt(0);
		var rootElm = doc.documentElement, blockName = "P", startNode, endNode, startBlock, endBlock;
		var rngBefore, rngAfter, direct, startNode, startOffset, endNode, endOffset, b = tinyMCE.isOpera ? inst.selection.getBookmark() : null;
		var paraBefore, paraAfter, startChop, endChop, contents, i;

		function isEmpty(para) {
			var nodes;

			function isEmptyHTML(html) {
				return html.replace(new RegExp('[ \t\r\n]+', 'g'), '').toLowerCase() == '';
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
			nodes = tinyMCE.getNodeTree(para, [], 3);
			for (i=0; i<nodes.length; i++) {
				if (!isEmptyHTML(nodes[i].nodeValue))
					return false;
			}

			// No images, no tables, no hrs, no text content then it's empty
			return true;
		}

	//	tinyMCE.debug(body.innerHTML);

	//	debug(e.target, sel.anchorNode.nodeName, sel.focusNode.nodeName, rng.startContainer, rng.endContainer, rng.commonAncestorContainer, sel.anchorOffset, sel.focusOffset, rng.toString());

		// Setup before range
		rngBefore = doc.createRange();
		rngBefore.setStart(sel.anchorNode, sel.anchorOffset);
		rngBefore.collapse(true);

		// Setup after range
		rngAfter = doc.createRange();
		rngAfter.setStart(sel.focusNode, sel.focusOffset);
		rngAfter.collapse(true);

		// Setup start/end points
		direct = rngBefore.compareBoundaryPoints(rngBefore.START_TO_END, rngAfter) < 0;
		startNode = direct ? sel.anchorNode : sel.focusNode;
		startOffset = direct ? sel.anchorOffset : sel.focusOffset;
		endNode = direct ? sel.focusNode : sel.anchorNode;
		endOffset = direct ? sel.focusOffset : sel.anchorOffset;

		startNode = startNode.nodeName == "BODY" ? startNode.firstChild : startNode;
		endNode = endNode.nodeName == "BODY" ? endNode.firstChild : endNode;

		// Get block elements
		startBlock = inst.getParentBlockElement(startNode);
		endBlock = inst.getParentBlockElement(endNode);

		// If absolute force paragraph generation within
		if (startBlock && (startBlock.nodeName == 'CAPTION' || /absolute|relative|static/gi.test(startBlock.style.position)))
			startBlock = null;

		if (endBlock && (endBlock.nodeName == 'CAPTION' || /absolute|relative|static/gi.test(endBlock.style.position)))
			endBlock = null;

		// Use current block name
		if (startBlock != null) {
			blockName = startBlock.nodeName;

			// Use P instead
			if (/(TD|TABLE|TH|CAPTION)/.test(blockName) || (blockName == "DIV" && /left|right/gi.test(startBlock.style.cssFloat)))
				blockName = "P";
		}

		// Within a list use normal behaviour
		if (tinyMCE.getParentElement(startBlock, "OL,UL", null, body) != null)
			return false;

		// Within a table create new paragraphs
		if ((startBlock != null && startBlock.nodeName == "TABLE") || (endBlock != null && endBlock.nodeName == "TABLE"))
			startBlock = endBlock = null;

		// Setup new paragraphs
		paraBefore = (startBlock != null && startBlock.nodeName == blockName) ? startBlock.cloneNode(false) : doc.createElement(blockName);
		paraAfter = (endBlock != null && endBlock.nodeName == blockName) ? endBlock.cloneNode(false) : doc.createElement(blockName);

		// Is header, then force paragraph under
		if (/^(H[1-6])$/.test(blockName))
			paraAfter = doc.createElement("p");

		// Setup chop nodes
		startChop = startNode;
		endChop = endNode;

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

			if (!tinyMCE.isSafari)
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

				contents = rng.cloneContents();
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
				if (tinyMCE.isOpera) {
					paraBefore.normalize();
					rngBefore.insertNode(paraBefore);
					paraAfter.normalize();
					rngBefore.insertNode(paraAfter);
				} else {
					paraAfter.normalize();
					rngBefore.insertNode(paraAfter);
					paraBefore.normalize();
					rngBefore.insertNode(paraBefore);
				}

				//tinyMCE.debug("1: ", paraBefore.innerHTML, paraAfter.innerHTML);
			} else {
				body.innerHTML = "<" + blockName + ">&nbsp;</" + blockName + "><" + blockName + ">&nbsp;</" + blockName + ">";
				paraAfter = body.childNodes[1];
			}

			inst.selection.moveToBookmark(b);
			inst.selection.selectNode(paraAfter, true, true);

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
		contents = rngAfter.cloneContents();

		if (contents.firstChild && contents.firstChild.nodeName == blockName) {
	/*		var nodes = contents.firstChild.childNodes;
			for (i=0; i<nodes.length; i++) {
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
		rng = doc.createRange();

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

		if (tinyMCE.isOpera) {
			rng.insertNode(paraBefore);
			rng.insertNode(paraAfter);
		} else {
			rng.insertNode(paraAfter);
			rng.insertNode(paraBefore);
		}

		//tinyMCE.debug("2", paraBefore.innerHTML, paraAfter.innerHTML);

		// Normalize
		paraAfter.normalize();
		paraBefore.normalize();

		inst.selection.moveToBookmark(b);
		inst.selection.selectNode(paraAfter, true, true);

		return true;
	},

	_handleBackSpace : function(inst) {
		var r = inst.getRng(), sn = r.startContainer, nv, s = false;

		// Added body check for bug #1527787
		if (sn && sn.nextSibling && sn.nextSibling.nodeName == "BR" && sn.parentNode.nodeName != "BODY") {
			nv = sn.nodeValue;

			// Handle if a backspace is pressed after a space character #bug 1466054 removed since fix for #1527787
			/*if (nv != null && nv.length >= r.startOffset && nv.charAt(r.startOffset - 1) == ' ')
				s = true;*/

			// Only remove BRs if we are at the end of line #bug 1464152
			if (nv != null && r.startOffset == nv.length)
				sn.nextSibling.parentNode.removeChild(sn.nextSibling);
		}

		if (inst.settings.auto_resize)
			inst.resizeToContent();

		return s;
	}

	};

/* file:jscripts/tiny_mce/classes/TinyMCE_Layer.class.js */

function TinyMCE_Layer(id, bm) {
	this.id = id;
	this.blockerElement = null;
	this.events = false;
	this.element = null;
	this.blockMode = typeof(bm) != 'undefined' ? bm : true;
	this.doc = document;
};

TinyMCE_Layer.prototype = {
	moveRelativeTo : function(re, p) {
		var rep = this.getAbsPosition(re), e = this.getElement(), x, y;
		var w = parseInt(re.offsetWidth), h = parseInt(re.offsetHeight);
		var ew = parseInt(e.offsetWidth), eh = parseInt(e.offsetHeight);

		switch (p) {
			case "tl":
				x = rep.absLeft;
				y = rep.absTop;
				break;

			case "tr":
				x = rep.absLeft + w;
				y = rep.absTop;
				break;

			case "bl":
				x = rep.absLeft;
				y = rep.absTop + h;
				break;

			case "br":
				x = rep.absLeft + w;
				y = rep.absTop + h;
				break;

			case "cc":
				x = rep.absLeft + (w / 2) - (ew / 2);
				y = rep.absTop + (h / 2) - (eh / 2);
				break;
		}

		this.moveTo(x, y);
	},

	moveBy : function(x, y) {
		var e = this.getElement();
		this.moveTo(parseInt(e.style.left) + x, parseInt(e.style.top) + y);
	},

	moveTo : function(x, y) {
		var e = this.getElement();

		e.style.left = x + "px";
		e.style.top = y + "px";

		this.updateBlocker();
	},

	resizeBy : function(w, h) {
		var e = this.getElement();
		this.resizeTo(parseInt(e.style.width) + w, parseInt(e.style.height) + h);
	},

	resizeTo : function(w, h) {
		var e = this.getElement();

		if (w != null)
			e.style.width = w + "px";

		if (h != null)
			e.style.height = h + "px";

		this.updateBlocker();
	},

	show : function() {
		var el = this.getElement();

		if (el) {
			el.style.display = 'block';
			this.updateBlocker();
		}
	},

	hide : function() {
		var el = this.getElement();

		if (el) {
			el.style.display = 'none';
			this.updateBlocker();
		}
	},

	isVisible : function() {
		return this.getElement().style.display == 'block';
	},

	getElement : function() {
		if (!this.element)
			this.element = this.doc.getElementById(this.id);

		return this.element;
	},

	setBlockMode : function(s) {
		this.blockMode = s;
	},

	updateBlocker : function() {
		var e, b, x, y, w, h;

		b = this.getBlocker();
		if (b) {
			if (this.blockMode) {
				e = this.getElement();
				x = this.parseInt(e.style.left);
				y = this.parseInt(e.style.top);
				w = this.parseInt(e.offsetWidth);
				h = this.parseInt(e.offsetHeight);

				b.style.left = x + 'px';
				b.style.top = y + 'px';
				b.style.width = w + 'px';
				b.style.height = h + 'px';
				b.style.display = e.style.display;
			} else
				b.style.display = 'none';
		}
	},

	getBlocker : function() {
		var d, b;

		if (!this.blockerElement && this.blockMode) {
			d = this.doc;
			b = d.getElementById(this.id + "_blocker");

			if (!b) {
				b = d.createElement("iframe");

				b.setAttribute('id', this.id + "_blocker");
				b.style.cssText = 'display: none; position: absolute; left: 0; top: 0';
				b.src = 'javascript:false;';
				b.frameBorder = '0';
				b.scrolling = 'no';
	
				d.body.appendChild(b);
			}

			this.blockerElement = b;
		}

		return this.blockerElement;
	},

	getAbsPosition : function(n) {
		var p = {absLeft : 0, absTop : 0};

		while (n) {
			p.absLeft += n.offsetLeft;
			p.absTop += n.offsetTop;
			n = n.offsetParent;
		}

		return p;
	},

	create : function(n, c, p, h) {
		var d = this.doc, e = d.createElement(n);

		e.setAttribute('id', this.id);

		if (c)
			e.className = c;

		if (!p)
			p = d.body;

		if (h)
			e.innerHTML = h;

		p.appendChild(e);

		return this.element = e;
	},

	exists : function() {
		return this.doc.getElementById(this.id) != null;
	},

	parseInt : function(s) {
		if (s == null || s == '')
			return 0;

		return parseInt(s);
	},

	remove : function() {
		var e = this.getElement(), b = this.getBlocker();

		if (e)
			e.parentNode.removeChild(e);

		if (b)
			b.parentNode.removeChild(b);
	}

	};

/* file:jscripts/tiny_mce/classes/TinyMCE_Menu.class.js */

function TinyMCE_Menu() {
	var id;

	if (typeof(tinyMCE.menuCounter) == "undefined")
		tinyMCE.menuCounter = 0;

	id = "mc_menu_" + tinyMCE.menuCounter++;

	TinyMCE_Layer.call(this, id, true);

	this.id = id;
	this.items = [];
	this.needsUpdate = true;
};

TinyMCE_Menu.prototype = tinyMCE.extend(TinyMCE_Layer.prototype, {
	init : function(s) {
		var n;

		// Default params
		this.settings = {
			separator_class : 'mceMenuSeparator',
			title_class : 'mceMenuTitle',
			disabled_class : 'mceMenuDisabled',
			menu_class : 'mceMenu',
			drop_menu : true
		};

		for (n in s)
			this.settings[n] = s[n];

		this.create('div', this.settings.menu_class);
	},

	clear : function() {
		this.items = [];
	},

	addTitle : function(t) {
		this.add({type : 'title', text : t});
	},

	addDisabled : function(t) {
		this.add({type : 'disabled', text : t});
	},

	addSeparator : function() {
		this.add({type : 'separator'});
	},

	addItem : function(t, js) {
		this.add({text : t, js : js});
	},

	add : function(mi) {
		this.items[this.items.length] = mi;
		this.needsUpdate = true;
	},

	update : function() {
		var e = this.getElement(), h = '', i, t, m = this.items, s = this.settings;

		if (this.settings.drop_menu)
			h += '<span class="mceMenuLine"></span>';

		h += '<table border="0" cellpadding="0" cellspacing="0">';

		for (i=0; i<m.length; i++) {
			t = tinyMCE.xmlEncode(m[i].text);
			c = m[i].class_name ? ' class="' + m[i].class_name + '"' : '';

			switch (m[i].type) {
				case 'separator':
					h += '<tr class="' + s.separator_class + '"><td>';
					break;

				case 'title':
					h += '<tr class="' + s.title_class + '"><td><span' + c +'>' + t + '</span>';
					break;

				case 'disabled':
					h += '<tr class="' + s.disabled_class + '"><td><span' + c +'>' + t + '</span>';
					break;

				default:
					h += '<tr><td><a href="' + tinyMCE.xmlEncode(m[i].js) + '" onmousedown="' + tinyMCE.xmlEncode(m[i].js) + ';return tinyMCE.cancelEvent(event);" onclick="return tinyMCE.cancelEvent(event);" onmouseup="return tinyMCE.cancelEvent(event);"><span' + c +'>' + t + '</span></a>';
			}

			h += '</td></tr>';
		}

		h += '</table>';

		e.innerHTML = h;

		this.needsUpdate = false;
		this.updateBlocker();
	},

	show : function() {
		var nl, i;

		if (tinyMCE.lastMenu == this)
			return;

		if (this.needsUpdate)
			this.update();

		if (tinyMCE.lastMenu && tinyMCE.lastMenu != this)
			tinyMCE.lastMenu.hide();

		TinyMCE_Layer.prototype.show.call(this);

		if (!tinyMCE.isOpera) {
			// Accessibility stuff
/*			nl = this.getElement().getElementsByTagName("a");
			if (nl.length > 0)
				nl[0].focus();*/
		}

		tinyMCE.lastMenu = this;
	}

	});

/* file:jscripts/tiny_mce/classes/TinyMCE_Debug.class.js */

tinyMCE.add(TinyMCE_Engine, {
	debug : function() {
		var m = "", a, i, l = tinyMCE.log.length;

		for (i=0, a = this.debug.arguments; i<a.length; i++) {
			m += a[i];

			if (i<a.length-1)
				m += ', ';
		}

		if (l < 1000)
			tinyMCE.log[l] = "[debug] " + m;
	}

	});

