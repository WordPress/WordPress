/**
 * $Id: editor_plugin_src.js 425 2007-11-21 15:17:39Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	var JSONRequest = tinymce.util.JSONRequest, each = tinymce.each, DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.SpellcheckerPlugin', {
		getInfo : function() {
			return {
				longname : 'Spellchecker',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/spellchecker',
				version : "2.0.2"
			};
		},

		init : function(ed, url) {
			var t = this, cm;

			t.url = url;
			t.editor = ed;

			// Register commands
			ed.addCommand('mceSpellCheck', function() {
				if (!t.active) {
					ed.setProgressState(1);
					t._sendRPC('checkWords', [t.selectedLang, t._getWords()], function(r) {
						if (r.length > 0) {
							t.active = 1;
							t._markWords(r);
							ed.setProgressState(0);
							ed.nodeChanged();
						} else {
							ed.setProgressState(0);
							ed.windowManager.alert('spellchecker.no_mpell');
						}
					});
				} else
					t._done();
			});

			ed.onInit.add(function() {
				if (ed.settings.content_css !== false)
					ed.dom.loadCSS(url + '/css/content.css');
			});

			ed.onClick.add(t._showMenu, t);
			ed.onContextMenu.add(t._showMenu, t);
			ed.onBeforeGetContent.add(function() {
				if (t.active)
					t._removeWords();
			});

			ed.onNodeChange.add(function(ed, cm) {
				cm.setActive('spellchecker', t.active);
			});

			ed.onSetContent.add(function() {
				t._done();
			});

			ed.onBeforeGetContent.add(function() {
				t._done();
			});

			ed.onBeforeExecCommand.add(function(ed, cmd) {
				if (cmd == 'mceFullScreen')
					t._done();
			});

			// Find selected language
			t.languages = {};
			each(ed.getParam('spellchecker_languages', '+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv', 'hash'), function(v, k) {
				if (k.indexOf('+') === 0) {
					k = k.substring(1);
					t.selectedLang = v;
				}

				t.languages[k] = v;
			});
		},

		createControl : function(n, cm) {
			var t = this, c, ed = t.editor;

			if (n == 'spellchecker') {
				c = cm.createSplitButton(n, {title : 'spellchecker.desc', cmd : 'mceSpellCheck', scope : t});

				c.onRenderMenu.add(function(c, m) {
					m.add({title : 'spellchecker.langs', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
					each(t.languages, function(v, k) {
						var o = {icon : 1}, mi;

						o.onclick = function() {
							mi.setSelected(1);
							t.selectedItem.setSelected(0);
							t.selectedItem = mi;
							t.selectedLang = v;
						};

						o.title = k;
						mi = m.add(o);
						mi.setSelected(v == t.selectedLang);

						if (v == t.selectedLang)
							t.selectedItem = mi;
					})
				});

				return c;
			}
		},

		// Internal functions

		_walk : function(n, f) {
			var d = this.editor.getDoc(), w;

			if (d.createTreeWalker) {
				w = d.createTreeWalker(n, NodeFilter.SHOW_TEXT, null, false);

				while ((n = w.nextNode()) != null)
					f.call(this, n);
			} else
				tinymce.walk(n, f, 'childNodes');
		},

		_getSeparators : function() {
			var re = '', i, str = this.editor.getParam('spellchecker_word_separator_chars', '\\s!"#$%&()*+,-./:;<=>?@[\]^_{|}§©«®±¶·¸»¼½¾¿×÷¤\u201d\u201c');

			// Build word separator regexp
			for (i=0; i<str.length; i++)
				re += '\\' + str.charAt(i);

			return re;
		},

		_getWords : function() {
			var ed = this.editor, wl = [], tx = '', lo = {};

			// Get area text
			this._walk(ed.getBody(), function(n) {
				if (n.nodeType == 3)
					tx += n.nodeValue + ' ';
			});

			// Split words by separator
			tx = tx.replace(new RegExp('([0-9]|[' + this._getSeparators() + '])', 'g'), ' ');
			tx = tinymce.trim(tx.replace(/(\s+)/g, ' '));

			// Build word array and remove duplicates
			each(tx.split(' '), function(v) {
				if (!lo[v]) {
					wl.push(v);
					lo[v] = 1;
				}
			});

			return wl;
		},

		_removeWords : function(w) {
			var ed = this.editor, dom = ed.dom, se = ed.selection, b = se.getBookmark();

			each(dom.select('span').reverse(), function(n) {
				if (n && (dom.hasClass(n, 'mceItemHiddenSpellWord') || dom.hasClass(n, 'mceItemHidden'))) {
					if (!w || dom.decode(n.innerHTML) == w)
						dom.remove(n, 1);
				}
			});

			se.moveToBookmark(b);
		},

		_markWords : function(wl) {
			var r1, r2, r3, r4, r5, w = '', ed = this.editor, re = this._getSeparators(), dom = ed.dom, nl = [];
			var se = ed.selection, b = se.getBookmark();

			each(wl, function(v) {
				w += (w ? '|' : '') + v;
			});

			r1 = new RegExp('([' + re + '])(' + w + ')([' + re + '])', 'g');
			r2 = new RegExp('^(' + w + ')', 'g');
			r3 = new RegExp('(' + w + ')([' + re + ']?)$', 'g');
			r4 = new RegExp('^(' + w + ')([' + re + ']?)$', 'g');
			r5 = new RegExp('(' + w + ')([' + re + '])', 'g');

			// Collect all text nodes
			this._walk(this.editor.getBody(), function(n) {
				if (n.nodeType == 3) {
					nl.push(n);
				}
			});

			// Wrap incorrect words in spans
			each(nl, function(n) {
				var v;

				if (n.nodeType == 3) {
					v = n.nodeValue;

					if (r1.test(v) || r2.test(v) || r3.test(v) || r4.test(v)) {
						v = dom.encode(v);
						v = v.replace(r5, '<span class="mceItemHiddenSpellWord">$1</span>$2');
						v = v.replace(r3, '<span class="mceItemHiddenSpellWord">$1</span>$2');

						dom.replace(dom.create('span', {'class' : 'mceItemHidden'}, v), n);
					}
				}
			});

			se.moveToBookmark(b);
		},

		_showMenu : function(ed, e) {
			var t = this, ed = t.editor, m = t._menu, p1, dom = ed.dom, vp = dom.getViewPort(ed.getWin());

			if (!m) {
				p1 = DOM.getPos(ed.getContentAreaContainer());
				//p2 = DOM.getPos(ed.getContainer());

				m = ed.controlManager.createDropMenu('spellcheckermenu', {
					offset_x : p1.x,
					offset_y : p1.y,
					'class' : 'mceNoIcons'
				});

				t._menu = m;
			}

			if (dom.hasClass(e.target, 'mceItemHiddenSpellWord')) {
				m.removeAll();
				m.add({title : 'spellchecker.wait', 'class' : 'mceMenuItemTitle'}).setDisabled(1);

				t._sendRPC('getSuggestions', [t.selectedLang, dom.decode(e.target.innerHTML)], function(r) {
					m.removeAll();

					if (r.length > 0) {
						m.add({title : 'spellchecker.sug', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
						each(r, function(v) {
							m.add({title : v, onclick : function() {
								dom.replace(ed.getDoc().createTextNode(v), e.target);
								t._checkDone();
							}});
						});

						m.addSeparator();
					} else
						m.add({title : 'spellchecker.no_sug', 'class' : 'mceMenuItemTitle'}).setDisabled(1);

					m.add({
						title : 'spellchecker.ignore_word',
						onclick : function() {
							dom.remove(e.target, 1);
							t._checkDone();
						}
					});

					m.add({
						title : 'spellchecker.ignore_words',
						onclick : function() {
							t._removeWords(dom.decode(e.target.innerHTML));
							t._checkDone();
						}
					});

					m.update();
				});

				ed.selection.select(e.target);
				p1 = dom.getPos(e.target);
				m.showMenu(p1.x, p1.y + e.target.offsetHeight - vp.y);

				return tinymce.dom.Event.cancel(e);
			} else
				m.hideMenu();
		},

		_checkDone : function() {
			var t = this, ed = t.editor, dom = ed.dom, o;

			each(dom.select('span'), function(n) {
				if (n && dom.hasClass(n, 'mceItemHiddenSpellWord')) {
					o = true;
					return false;
				}
			});

			if (!o)
				t._done();
		},

		_done : function() {
			var t = this, la = t.active;

			if (t.active) {
				t.active = 0;
				t._removeWords();

				if (t._menu)
					t._menu.hideMenu();

				if (la)
					t.editor.nodeChanged();
			}
		},

		_sendRPC : function(m, p, cb) {
			var t = this, url = t.editor.getParam("spellchecker_rpc_url", this.url+'/rpc.php');

			if (url == '{backend}') {
				t.editor.setProgressState(0);
				alert('Please specify: spellchecker_rpc_url');
				return;
			}

			JSONRequest.sendRPC({
				url : url,
				method : m,
				params : p,
				success : cb,
				error : function(e, x) {
					t.editor.setProgressState(0);
					t.editor.windowManager.alert(e.errstr || ('Error response: ' + x.responseText));
				}
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('spellchecker', tinymce.plugins.SpellcheckerPlugin);
})();