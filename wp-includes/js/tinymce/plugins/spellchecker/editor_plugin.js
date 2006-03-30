/**
 * $RCSfile: editor_plugin_src.js,v $
 * $Revision: 1.4 $
 * $Date: 2006/03/24 17:24:50 $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

tinyMCE.importPluginLanguagePack('spellchecker', 'en,sv,nn,nb');

// Plucin static class
var TinyMCE_SpellCheckerPlugin = {
	_contextMenu : new TinyMCE_Menu(),
	_menu : new TinyMCE_Menu(),
	_counter : 0,

	getInfo : function() {
		return {
			longname : 'Spellchecker',
			author : 'Moxiecode Systems AB',
			authorurl : 'http://tinymce.moxiecode.com',
			infourl : 'http://tinymce.moxiecode.com/tinymce/docs/plugin_spellchecker.html',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	},

	handleEvent : function(e) {
		var elm = tinyMCE.isMSIE ? e.srcElement : e.target;
		var inst = tinyMCE.selectedInstance, args = '';
		var self = TinyMCE_SpellCheckerPlugin;
		var cm = self._contextMenu;
		var p, p2, x, y, sx, sy, h, elm;

		// Handle click on word
		if ((e.type == "click" || e.type == "contextmenu") && elm) {
			do {
				if (tinyMCE.getAttrib(elm, 'class') == "mceItemHiddenSpellWord") {
					inst.spellCheckerElm = elm;

					// Setup arguments
					args += 'id=' + inst.editorId + "|" + (++self._counter);
					args += '&cmd=suggest&check=' + escape(elm.innerHTML);
					args += '&lang=' + escape(inst.spellCheckerLang);

					elm = inst.spellCheckerElm;
					p = tinyMCE.getAbsPosition(inst.iframeElement);
					p2 = tinyMCE.getAbsPosition(elm);
					h = parseInt(elm.offsetHeight);
					sx = inst.getBody().scrollLeft;
					sy = inst.getBody().scrollTop;
					x = p.absLeft + p2.absLeft - sx;
					y = p.absTop + p2.absTop - sy + h;

					cm.clear();
					cm.addTitle(tinyMCE.getLang('lang_spellchecker_wait', '', true));
					cm.show();
					cm.moveTo(x, y);

					inst.selection.selectNode(elm, false, false);

					self._sendAjax(self.baseURL + "/tinyspell.php", self._ajaxResponse, 'post', args);

					tinyMCE.cancelEvent(e);
					return false;
				}
			} while ((elm = elm.parentNode));
		}

		return true;
	},

	initInstance : function(inst) {
		var self = TinyMCE_SpellCheckerPlugin, m = self._menu, cm = self._contextMenu, e;

		tinyMCE.importCSS(inst.getDoc(), tinyMCE.baseURL + "/plugins/spellchecker/css/content.css");

		if (!tinyMCE.hasMenu('spellcheckercontextmenu')) {
			tinyMCE.importCSS(document, tinyMCE.baseURL + "/plugins/spellchecker/css/spellchecker.css");

			cm.init({drop_menu : false});
			tinyMCE.addMenu('spellcheckercontextmenu', cm);
		}

		if (!tinyMCE.hasMenu('spellcheckermenu')) {
			m.init({});
			tinyMCE.addMenu('spellcheckermenu', m);
		}

        inst.spellCheckerLang = 'en';
		self._buildSettingsMenu(inst, null);

		e = self._getBlockBoxLayer(inst).create('div', 'mceBlockBox', document.getElementById(inst.editorId + '_parent'));
		self._getMsgBoxLayer(inst).create('div', 'mceMsgBox', document.getElementById(inst.editorId + '_parent'));
	},

	_getMsgBoxLayer : function(inst) {
		if (!inst.spellCheckerMsgBoxL)
			inst.spellCheckerMsgBoxL = new TinyMCE_Layer(inst.editorId + '_spellcheckerMsgBox', false);

		return inst.spellCheckerMsgBoxL;
	},

	_getBlockBoxLayer : function(inst) {
		if (!inst.spellCheckerBoxL)
			inst.spellCheckerBoxL = new TinyMCE_Layer(inst.editorId + '_spellcheckerBlockBox', false);

		return inst.spellCheckerBoxL;
	},

	_buildSettingsMenu : function(inst, lang) {
		var i, ar = tinyMCE.getParam('spellchecker_languages', '+English=en').split(','), p;
		var self = TinyMCE_SpellCheckerPlugin, m = self._menu, c;

		m.clear();
		m.addTitle(tinyMCE.getLang('lang_spellchecker_langs', '', true));

		for (i=0; i<ar.length; i++) {
			if (ar[i] != '') {
				p = ar[i].split('=');
				c = 'mceMenuCheckItem';

				if (p[0].charAt(0) == '+') {
					p[0] = p[0].substring(1);

					if (lang == null) {
						c = 'mceMenuSelectedItem';
						inst.spellCheckerLang = p[1];
					}
				}

				if (lang == p[1])
					c = 'mceMenuSelectedItem';

				m.add({text : p[0], js : "tinyMCE.execInstanceCommand('" + inst.editorId + "','mceSpellCheckerSetLang',false,'" + p[1] + "');", class_name : c});
			}
		}
	},

	setupContent : function(editor_id, body, doc) {
		TinyMCE_SpellCheckerPlugin._removeWords(doc);
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case "spellchecker":
				return TinyMCE_SpellCheckerPlugin._getMenuButtonHTML(cn, 'lang_spellchecker_desc', '{$pluginurl}/images/spellchecker.gif', 'lang_spellchecker_desc', 'mceSpellCheckerMenu', 'mceSpellCheck');
		}

		return "";
	},

	/**
	 * Returns the HTML code for a normal button control.
	 *
	 * @param {string} id Button control id, this will be the suffix for the element id, the prefix is the editor id.
	 * @param {string} lang Language variable key name to insert as the title/alt of the button image.
	 * @param {string} img Image URL to insert, {$themeurl} and {$pluginurl} will be replaced.
	 * @param {string} mlang Language variable key name to insert as the title/alt of the menu button image.
	 * @param {string} mid Menu by id to display when the menu button is pressed.
	 * @param {string} cmd Command to execute when the user clicks the button.
	 * @param {string} ui Optional user interface boolean for command.
	 * @param {string} val Optional value for command.
	 * @return HTML code for a normal button based in input information.
	 * @type string
	 */
	_getMenuButtonHTML : function(id, lang, img, mlang, mid, cmd, ui, val) {
		var h = '', m, x;

		cmd = 'tinyMCE.hideMenus();tinyMCE.execInstanceCommand(\'{$editor_id}\',\'' + cmd + '\'';

		if (typeof(ui) != "undefined" && ui != null)
			cmd += ',' + ui;

		if (typeof(val) != "undefined" && val != null)
			cmd += ",'" + val + "'";

		cmd += ');';

		// Use tilemaps when enabled and found and never in MSIE since it loads the tile each time from cache if cahce is disabled
		if (tinyMCE.getParam('button_tile_map') && (!tinyMCE.isMSIE || tinyMCE.isOpera) && (m = tinyMCE.buttonMap[id]) != null && (tinyMCE.getParam("language") == "en" || img.indexOf('$lang') == -1)) {
			// Tiled button
			x = 0 - (m * 20) == 0 ? '0' : 0 - (m * 20);
			h += '<a id="{$editor_id}_' + id + '" href="javascript:' + cmd + '" onclick="' + cmd + 'return false;" onmousedown="return false;" class="mceTiledButton mceButtonNormal" target="_self">';
			h += '<img src="{$themeurl}/images/spacer.gif" style="background-position: ' + x + 'px 0" title="{$' + lang + '}" />';
			h += '<img src="{$themeurl}/images/button_menu.gif" title="{$' + lang + '}" class="mceMenuButton" onclick="' + mcmd + 'return false;" />';
			h += '</a>';
		} else {
			if (tinyMCE.isMSIE && !tinyMCE.isOpera)
				h += '<span id="{$editor_id}_' + id + '" class="mceMenuButton" onmouseover="tinyMCE.plugins.spellchecker._menuButtonEvent(\'over\',this);" onmouseout="tinyMCE.plugins.spellchecker._menuButtonEvent(\'out\',this);">';
			else
				h += '<span id="{$editor_id}_' + id + '" class="mceMenuButton">';

			h += '<a href="javascript:' + cmd + '" onclick="' + cmd + 'return false;" onmousedown="return false;" class="mceMenuButtonNormal" target="_self">';
			h += '<img src="' + img + '" title="{$' + lang + '}" /></a>';
			h += '<a href="#" onclick="tinyMCE.plugins.spellchecker._toggleMenu(\'{$editor_id}\',\'' + mid + '\');return false;" onmousedown="return false;"><img src="{$themeurl}/images/button_menu.gif" title="{$' + lang + '}" class="mceMenuButton" />';
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

	_toggleMenu : function(editor_id, id) {
		var self = TinyMCE_SpellCheckerPlugin;
		var e = document.getElementById(editor_id + '_spellchecker');
		var inst = tinyMCE.getInstanceById(editor_id);

		if (self._menu.isVisible()) {
			tinyMCE.hideMenus();
			return;
		}

		tinyMCE.lastMenuBtnClass = e.className.replace(/\s.*$/, '');
		tinyMCE.switchClass(editor_id + '_spellchecker', 'mceMenuButtonFocus');

		self._menu.moveRelativeTo(e, 'bl');
		self._menu.moveBy(tinyMCE.isMSIE && !tinyMCE.isOpera ? 0 : 1, -1);

		if (tinyMCE.isOpera)
			self._menu.moveBy(0, -2);

        self._onMenuEvent(inst, self._menu, 'show');

		self._menu.show();

		tinyMCE.lastSelectedMenuBtn = editor_id + '_spellchecker';
	},

	_onMenuEvent : function(inst, m, n) {
		TinyMCE_SpellCheckerPlugin._buildSettingsMenu(inst, inst.spellCheckerLang);
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		var inst = tinyMCE.getInstanceById(editor_id), self = TinyMCE_SpellCheckerPlugin, args = '', co, bb, mb, nl, i, e;

		// Handle commands
		switch (command) {
			case "mceSpellCheck":
				if (!inst.spellcheckerOn) {
					inst.spellCheckerBookmark = inst.selection.getBookmark();

					// Setup arguments
					args += 'id=' + inst.editorId + "|" + (++self._counter);
					args += '&cmd=spell&check=' + escape(self._getWordList(inst.getBody())).replace(/%20/g, '+');
					args += '&lang=' + escape(inst.spellCheckerLang);

					co = document.getElementById(inst.editorId + '_parent').firstChild;
					bb = self._getBlockBoxLayer(inst);
					bb.moveRelativeTo(co, 'tl');
					bb.resizeTo(co.offsetWidth, co.offsetHeight);
					bb.show();

					// Setup message box
					mb = self._getMsgBoxLayer(inst);
					e = mb.getElement();
					e.innerHTML = '<span>' + tinyMCE.getLang('lang_spellchecker_swait', '', true) + '</span>';
					mb.show();
					mb.moveRelativeTo(co, 'cc');

					if (tinyMCE.isMSIE && !tinyMCE.isOpera) {
						nl = co.getElementsByTagName('select');
						for (i=0; i<nl.length; i++)
							nl[i].disabled = true;
					}

					inst.spellcheckerOn = true;
					tinyMCE.switchClass(editor_id + '_spellchecker', 'mceMenuButtonSelected');

					self._sendAjax(self.baseURL + "/tinyspell.php", self._ajaxResponse, 'post', args);
				} else {
					self._removeWords(inst.getDoc());
					inst.spellcheckerOn = false;
					tinyMCE.switchClass(editor_id + '_spellchecker', 'mceMenuButton');
				}

				return true;

			case "mceSpellCheckReplace":
				if (inst.spellCheckerElm)
					tinyMCE.setOuterHTML(inst.spellCheckerElm, value);

				self._checkDone(inst);
				self._contextMenu.hide();
				self._menu.hide();

				return true;

			case "mceSpellCheckIgnore":
				if (inst.spellCheckerElm)
					self._removeWord(inst.spellCheckerElm);

				self._checkDone(inst);
				self._contextMenu.hide();
				self._menu.hide();
				return true;

			case "mceSpellCheckIgnoreAll":
				if (inst.spellCheckerElm)
					self._removeWords(inst.getDoc(), inst.spellCheckerElm.innerHTML);

				self._checkDone(inst);
				self._contextMenu.hide();
				self._menu.hide();
				return true;

			case "mceSpellCheckerSetLang":
				tinyMCE.hideMenus();
				inst.spellCheckerLang = value;
				self._removeWords(inst.getDoc());
				inst.spellcheckerOn = false;
				tinyMCE.switchClass(editor_id + '_spellchecker', 'mceMenuButton');
				return true;
		}

		// Pass to next handler in chain
		return false;
	},

	cleanup : function(type, content, inst) {
		switch (type) {
			case "get_from_editor_dom":
				TinyMCE_SpellCheckerPlugin._removeWords(content);
				inst.spellcheckerOn = false;
				break;
		}

		return content;
	},

	// Private plugin specific methods

	_displayUI : function(inst) {
		var self = TinyMCE_SpellCheckerPlugin;
		var bb = self._getBlockBoxLayer(inst);
		var mb = self._getMsgBoxLayer(inst);
		var nl, i;
		var co = document.getElementById(inst.editorId + '_parent').firstChild;

		if (tinyMCE.isMSIE && !tinyMCE.isOpera) {
			nl = co.getElementsByTagName('select');
			for (i=0; i<nl.length; i++)
				nl[i].disabled = false;
		}

		bb.hide();
		mb.hide();
	},

	_ajaxResponse : function(xml) {
		var el = xml ? xml.documentElement : null;
		var inst = tinyMCE.selectedInstance, self = TinyMCE_SpellCheckerPlugin;
		var cmd = el ? el.getAttribute("cmd") : null, err, id = el ? el.getAttribute("id") : null;

		if (id)
			inst = tinyMCE.getInstanceById(id.substring(0, id.indexOf('|')));

		self._displayUI(inst);

		// Ignore suggestions for other ajax responses
		if (cmd == "suggest" && id != inst.editorId + "|" + self._counter)
			return;

		if (!el) {
			inst.spellcheckerOn = false;
			tinyMCE.switchClass(inst.editorId + '_spellchecker', 'mceMenuButton');
			alert("Could not execute AJAX call, server didn't return valid a XML.");
			return;
		}

		err = el.getAttribute("error");

		if (err == "true") {
			inst.spellcheckerOn = false;
			tinyMCE.switchClass(inst.editorId + '_spellchecker', 'mceMenuButton');
			alert(el.getAttribute("msg"));
			return;
		}

		switch (cmd) {
			case "spell":
				if (xml.documentElement.firstChild) {
					self._markWords(inst.getDoc(), inst.getBody(), el.firstChild.nodeValue.split(' '));
					inst.selection.moveToBookmark(inst.spellCheckerBookmark);
				} else
					alert(tinyMCE.getLang('lang_spellchecker_no_mpell', '', true));

				self._checkDone(inst);

				break;

			case "suggest":
				self._buildMenu(el.firstChild ? el.firstChild.nodeValue.split(' ') : null, 10);
				self._contextMenu.show();
				break;
		}
	},

	_getWordSeparators : function() {
		var i, re = '', ch = tinyMCE.getParam('spellchecker_word_separator_chars', '\\s!"#$%&()*+,-./:;<=>?@[\]^_{|}§©«®±¶·¸»¼½¾¿×÷¤\u201d\u201c');

		for (i=0; i<ch.length; i++)
			re += '\\' + ch.charAt(i);

		return re;
	},

	_getWordList : function(n) {
		var i, x, s, nv = '', nl = tinyMCE.getNodeTree(n, new Array(), 3), wl = new Array();
		var re = TinyMCE_SpellCheckerPlugin._getWordSeparators();

		for (i=0; i<nl.length; i++)
			nv += nl[i].nodeValue + " ";

		nv = nv.replace(new RegExp('([0-9]|[' + re + '])', 'g'), ' ');
		nv = tinyMCE.trim(nv.replace(/(\s+)/g, ' '));

		nl = nv.split(/\s+/);
		for (i=0; i<nl.length; i++) {
			s = false;
			for (x=0; x<wl.length; x++) {
				if (wl[x] == nl[i]) {
					s = true;
					break;
				}
			}

			if (!s)
				wl[wl.length] = nl[i];
		}

		return wl.join(' ');
	},

	_removeWords : function(doc, word) {
		var i, c, nl = doc.getElementsByTagName("span");
		var self = TinyMCE_SpellCheckerPlugin;
		var inst = tinyMCE.selectedInstance, b = inst ? inst.selection.getBookmark() : null;

		word = typeof(word) == 'undefined' ? null : word;

		for (i=nl.length-1; i>=0; i--) {
			c = tinyMCE.getAttrib(nl[i], 'class');

			if ((c == 'mceItemHiddenSpellWord' || c == 'mceItemHidden') && (word == null || nl[i].innerHTML == word))
				self._removeWord(nl[i]);
		}

		if (b)
			inst.selection.moveToBookmark(b);
	},

	_checkDone : function(inst) {
		var i, w = 0, nl = inst.getDoc().getElementsByTagName("span")
		var self = TinyMCE_SpellCheckerPlugin;

		for (i=nl.length-1; i>=0; i--) {
			c = tinyMCE.getAttrib(nl[i], 'class');

			if (c == 'mceItemHiddenSpellWord')
				w++;
		}

		if (w == 0) {
			self._removeWords(inst.getDoc());
			inst.spellcheckerOn = false;
			tinyMCE.switchClass(inst.editorId + '_spellchecker', 'mceMenuButton');
		}
	},

	_removeWord : function(e) {
		tinyMCE.setOuterHTML(e, e.innerHTML);
	},

	_markWords : function(doc, n, wl) {
		var i, nv, nn, nl = tinyMCE.getNodeTree(n, new Array(), 3);
		var r1, r2, r3, r4, r5, w = '';
		var re = TinyMCE_SpellCheckerPlugin._getWordSeparators();

		for (i=0; i<wl.length; i++)
			w += wl[i] + ((i == wl.length-1) ? '' : '|');

		r1 = new RegExp('([' + re + '])(' + w + ')([' + re + '])', 'g');
		r2 = new RegExp('^(' + w + ')', 'g');
		r3 = new RegExp('(' + w + ')([' + re + ']?)$', 'g');
		r4 = new RegExp('^(' + w + ')([' + re + ']?)$', 'g');
		r5 = new RegExp('(' + w + ')([' + re + '])', 'g');

		for (i=0; i<nl.length; i++) {
			nv = nl[i].nodeValue;
			if (r1.test(nv) || r2.test(nv) || r3.test(nv) || r4.test(nv)) {
				nv = tinyMCE.xmlEncode(nv);
				nv = nv.replace(r5, '<span class="mceItemHiddenSpellWord">$1</span>$2');
				nv = nv.replace(r3, '<span class="mceItemHiddenSpellWord">$1</span>$2');

				nn = doc.createElement('span');
				nn.className = "mceItemHidden";
				nn.innerHTML = nv;

				// Remove old text node
				nl[i].parentNode.replaceChild(nn, nl[i]);
			}
		}
	},

	_buildMenu : function(sg, max) {
		var i, self = TinyMCE_SpellCheckerPlugin, cm = self._contextMenu;

		cm.clear();

		if (sg != null) {
			cm.addTitle(tinyMCE.getLang('lang_spellchecker_sug', '', true));

			for (i=0; i<sg.length && i<max; i++)
				cm.addItem(sg[i], 'tinyMCE.execCommand("mceSpellCheckReplace",false,"' + sg[i] + '");');

			cm.addSeparator();
			cm.addItem(tinyMCE.getLang('lang_spellchecker_ignore_word', '', true), 'tinyMCE.execCommand(\'mceSpellCheckIgnore\');');
			cm.addItem(tinyMCE.getLang('lang_spellchecker_ignore_words', '', true), 'tinyMCE.execCommand(\'mceSpellCheckIgnoreAll\');');
		} else
			cm.addTitle(tinyMCE.getLang('lang_spellchecker_no_sug', '', true));

		cm.update();
	},

	_getAjaxHTTP : function() {
		try {
			return new ActiveXObject('Msxml2.XMLHTTP')
		} catch (e) {
			try {
				return new ActiveXObject('Microsoft.XMLHTTP')
			} catch (e) {
				return new XMLHttpRequest();
			}
		}
	},

	/**
	 * Perform AJAX call.
	 *
	 * @param {string} u URL of AJAX service.
	 * @param {function} f Function to call when response arrives.
	 * @param {string} m Request method post or get.
	 * @param {Array} a Array with arguments to send.
	 */
	_sendAjax : function(u, f, m, a) {
		var x = TinyMCE_SpellCheckerPlugin._getAjaxHTTP();

		x.open(m, u, true);

		x.onreadystatechange = function() {
			if (x.readyState == 4)
				f(x.responseXML);
		};

		if (m == 'post')
			x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

		x.send(a);
	}
};

// Register plugin
tinyMCE.addPlugin('spellchecker', TinyMCE_SpellCheckerPlugin);
