/**
 * $Id: editor_plugin_src.js 544 2008-01-17 13:07:00Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	var DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.FullScreenPlugin', {
		init : function(ed, url) {
			var t = this, s = {}, vp;

			t.editor = ed;

			// Register commands
			ed.addCommand('mceFullScreen', function() {
				var win, de = document.documentElement;

				if (ed.getParam('fullscreen_is_enabled')) {
					if (ed.getParam('fullscreen_new_window'))
						closeFullscreen(); // Call to close in new window
					else {
						window.setTimeout(function() {
							tinyMCE.get(ed.getParam('fullscreen_editor_id')).setContent(ed.getContent({format : 'raw'}), {format : 'raw'});
							tinyMCE.remove(ed);
							DOM.remove('mce_fullscreen_container');
							de.style.overflow = ed.getParam('fullscreen_html_overflow');
							DOM.setStyle(document.body, 'overflow', ed.getParam('fullscreen_overflow'));
							window.scrollTo(ed.getParam('fullscreen_scrollx'), ed.getParam('fullscreen_scrolly'));
						}, 10);
					}

					return;
				}

				if (ed.getParam('fullscreen_new_window')) {
					win = window.open(url + "/fullscreen.htm", "mceFullScreenPopup", "fullscreen=yes,menubar=no,toolbar=no,scrollbars=no,resizable=no,left=0,top=0,width=" + screen.availWidth + ",height=" + screen.availHeight);
					try {
						win.resizeTo(screen.availWidth, screen.availHeight);
					} catch (e) {
						// Ignore
					}
				} else {
					s.fullscreen_overflow = DOM.getStyle(document.body, 'overflow', 1) || 'auto';
					s.fullscreen_html_overflow = DOM.getStyle(de, 'overflow', 1);
					vp = DOM.getViewPort();
					s.fullscreen_scrollx = vp.x;
					s.fullscreen_scrolly = vp.y;

					// Fixes an Opera bug where the scrollbars doesn't reappear
					if (tinymce.isOpera && s.fullscreen_overflow == 'visible')
						s.fullscreen_overflow = 'auto';

					// Fixes an IE bug where horizontal scrollbars would appear
					if (tinymce.isIE && s.fullscreen_overflow == 'scroll')
						s.fullscreen_overflow = 'auto';

					if (s.fullscreen_overflow == '0px')
						s.fullscreen_overflow = '';

					DOM.setStyle(document.body, 'overflow', 'hidden');
					de.style.overflow = 'hidden'; //Fix for IE6/7
					vp = DOM.getViewPort();
					window.scrollTo(0, 0);

					if (tinymce.isIE)
						vp.h -= 1;

					n = DOM.add(document.body, 'div', {id : 'mce_fullscreen_container', style : 'position:absolute;top:0;left:0;width:' + vp.w + 'px;height:' + vp.h + 'px;z-index:150;'});
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

					tinymce.each(ed.getParam('fullscreen_settings'), function(v, k) {
						s[k] = v;
					});

					if (s.theme_advanced_toolbar_location === 'external')
						s.theme_advanced_toolbar_location = 'top';

					t.fullscreenEditor = new tinymce.Editor('mce_fullscreen', s);
					t.fullscreenEditor.onInit.add(function() {
						t.fullscreenEditor.setContent(ed.getContent({format : 'raw', no_events : 1}), {format : 'raw', no_events : 1});
					});

					t.fullscreenEditor.render();
					tinyMCE.add(t.fullscreenEditor);

					t.fullscreenElement = new tinymce.dom.Element('mce_fullscreen_container');
					t.fullscreenElement.update();
					//document.body.overflow = 'hidden';
				}
			});

			// Register buttons
			ed.addButton('fullscreen', {title : 'fullscreen.desc', cmd : 'mceFullScreen'});

			ed.onNodeChange.add(function(ed, cm) {
				cm.setActive('fullscreen', ed.getParam('fullscreen_is_enabled'));
			});
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