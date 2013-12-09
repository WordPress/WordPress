/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	var DOM = tinymce.DOM;

	// State Transfer function
	var transferState = function(oldEditor, newEditor, bookmark) {
		var transferColorButtonState = function(swapme) {
			var c = oldEditor.controlManager.get(swapme);
			var newC = newEditor.controlManager.get(swapme);

			if (c && newC) {
				newC.displayColor(c.value);
			}

		};

		transferColorButtonState('forecolor');
		transferColorButtonState('backcolor');
		newEditor.setContent(oldEditor.getContent({format : 'raw'}), {format : 'raw'});
		newEditor.selection.moveToBookmark(bookmark);

		if (oldEditor.plugins.spellchecker && newEditor.plugins.spellchecker) {
			newEditor.plugins.spellchecker.setLanguage(oldEditor.plugins.spellchecker.selectedLang);
		}
	};

	tinymce.create('tinymce.plugins.FullScreenPlugin', {
		init : function(ed, url) {
			var t = this, s = {}, de = DOM.doc.documentElement, vp, fullscreen_overflow, fullscreen_html_overflow, fullscreen_scrollx, fullscreen_scrolly, posCss, bookmark;

			// Register commands
			ed.addCommand('mceFullScreen', function() {
				var win, oed;

				if (ed.getParam('fullscreen_is_enabled')) {
					if (ed.getParam('fullscreen_new_window'))
						closeFullscreen(); // Call to close in fullscreen.htm
					else {
						DOM.win.setTimeout(function() {
							var fullscreenEditor = ed;

							// find the editor that opened this one, execute restore function there
							var originalEditor = tinyMCE.get(fullscreenEditor.getParam('fullscreen_editor_id'));
							originalEditor.plugins.fullscreen.saveState(fullscreenEditor);

							tinyMCE.remove(fullscreenEditor);
						}, 10);
					}

					return;
				}

				if (ed.getParam('fullscreen_new_window')) {
					t.fullscreenSettings = {
						bookmark: ed.selection.getBookmark()
					};
					win = DOM.win.open(url + "/fullscreen.htm", "mceFullScreenPopup", "fullscreen=yes,menubar=no,toolbar=no,scrollbars=no,resizable=yes,left=0,top=0,width=" + screen.availWidth + ",height=" + screen.availHeight);
					try {
						win.resizeTo(screen.availWidth, screen.availHeight);
					} catch (e) {
						// Ignore
					}
				} else {
					fullscreen_overflow = DOM.getStyle(DOM.doc.body, 'overflow', 1) || 'auto';
					fullscreen_html_overflow = DOM.getStyle(de, 'overflow', 1);
					vp = DOM.getViewPort();
					fullscreen_scrollx = vp.x;
					fullscreen_scrolly = vp.y;

					// Fixes an Opera bug where the scrollbars doesn't reappear
					if (tinymce.isOpera && fullscreen_overflow == 'visible')
						fullscreen_overflow = 'auto';

					// Fixes an IE bug where horizontal scrollbars would appear
					if (tinymce.isIE && fullscreen_overflow == 'scroll')
						fullscreen_overflow = 'auto';

					// Fixes an IE bug where the scrollbars doesn't reappear
					if (tinymce.isIE && (fullscreen_html_overflow == 'visible' || fullscreen_html_overflow == 'scroll'))
						fullscreen_html_overflow = 'auto';

					if (fullscreen_overflow == '0px')
						fullscreen_overflow = '';

					DOM.setStyle(DOM.doc.body, 'overflow', 'hidden');
					de.style.overflow = 'hidden'; //Fix for IE6/7
					vp = DOM.getViewPort();
					DOM.win.scrollTo(0, 0);

					if (tinymce.isIE)
						vp.h -= 1;

					// Use fixed position if it exists
					if (tinymce.isIE6 || document.compatMode == 'BackCompat')
						posCss = 'absolute;top:' + vp.y;
					else
						posCss = 'fixed;top:0';

					n = DOM.add(DOM.doc.body, 'div', {
						id : 'mce_fullscreen_container',
						style : 'position:' + posCss + ';left:0;width:' + vp.w + 'px;height:' + vp.h + 'px;z-index:200000;'});
					DOM.add(n, 'div', {id : 'mce_fullscreen'});

					tinymce.each(ed.settings, function(v, n) {
						s[n] = v;
					});

					s.id = 'mce_fullscreen';
					s.width = n.clientWidth;
					s.height = n.clientHeight - 15;
					s.fullscreen_is_enabled = true;
					s.fullscreen_editor_id = ed.id;
					s.theme_advanced_resizing = false;
					s.save_onsavecallback = function() {
						ed.setContent(tinyMCE.get(s.id).getContent());
						ed.execCommand('mceSave');
					};

					tinymce.each(ed.getParam('fullscreen_settings'), function(v, k) {
						s[k] = v;
					});

					t.fullscreenSettings = {
						bookmark: ed.selection.getBookmark(),
						fullscreen_overflow: fullscreen_overflow,
						fullscreen_html_overflow: fullscreen_html_overflow,
						fullscreen_scrollx: fullscreen_scrollx,
						fullscreen_scrolly: fullscreen_scrolly
					};

					if (s.theme_advanced_toolbar_location === 'external')
						s.theme_advanced_toolbar_location = 'top';

					tinyMCE.oldSettings = tinyMCE.settings; // Store old settings, the Editor constructor overwrites them
					t.fullscreenEditor = new tinymce.Editor('mce_fullscreen', s);
					t.fullscreenEditor.onInit.add(function() {
						t.loadState(t.fullscreenEditor);
					});

					t.fullscreenEditor.render();

					t.fullscreenElement = new tinymce.dom.Element('mce_fullscreen_container');
					t.fullscreenElement.update();
					//document.body.overflow = 'hidden';

					t.resizeFunc = tinymce.dom.Event.add(DOM.win, 'resize', function() {
						var vp = tinymce.DOM.getViewPort(), fed = t.fullscreenEditor, outerSize, innerSize;

						// Get outer/inner size to get a delta size that can be used to calc the new iframe size
						outerSize = fed.dom.getSize(fed.getContainer().getElementsByTagName('table')[0]);
						innerSize = fed.dom.getSize(fed.getContainer().getElementsByTagName('iframe')[0]);

						fed.theme.resizeTo(vp.w - outerSize.w + innerSize.w, vp.h - outerSize.h + innerSize.h);
					});
				}
			});

			// Register buttons
			ed.addButton('fullscreen', {title : 'fullscreen.desc', cmd : 'mceFullScreen'});

			ed.onNodeChange.add(function(ed, cm) {
				cm.setActive('fullscreen', ed.getParam('fullscreen_is_enabled'));
			});

			// fullscreenEditor is a param here because in window mode we don't create it
			t.loadState = function(fullscreenEditor) {
				if (!(fullscreenEditor && t.fullscreenSettings)) {
					throw "No fullscreen editor to load to";
				}

				transferState(ed, fullscreenEditor, t.fullscreenSettings.bookmark);
				fullscreenEditor.focus();

			};

			// fullscreenEditor is a param here because in window mode we don't create it
			t.saveState = function(fullscreenEditor) {
				if (!(fullscreenEditor && t.fullscreenSettings)) {
					throw "No fullscreen editor to restore from";
				}
				var settings = t.fullscreenSettings;

				transferState(fullscreenEditor, ed, fullscreenEditor.selection.getBookmark());

				// cleanup only required if window mode isn't used
				if (!ed.getParam('fullscreen_new_window')) {
					tinymce.dom.Event.remove(DOM.win, 'resize', t.resizeFunc);
					delete t.resizeFunc;

					DOM.remove('mce_fullscreen_container');

					DOM.doc.documentElement.style.overflow = settings.fullscreen_html_overflow;
					DOM.setStyle(DOM.doc.body, 'overflow', settings.fullscreen_overflow);
					DOM.win.scrollTo(settings.fullscreen_scrollx, settings.fullscreen_scrolly);
				}
				tinyMCE.settings = tinyMCE.oldSettings; // Restore old settings

				// clear variables
				delete tinyMCE.oldSettings;
				delete t.fullscreenEditor;
				delete t.fullscreenElement;
				delete t.fullscreenSettings;

				// allow the fullscreen editor to be removed before restoring focus and selection
				DOM.win.setTimeout(function() {
					ed.selection.moveToBookmark(bookmark);
					ed.focus();
				}, 10);
			};
		},

		getInfo : function() {
			return {
				longname : 'Fullscreen',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/fullscreen',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('fullscreen', tinymce.plugins.FullScreenPlugin);
})();
