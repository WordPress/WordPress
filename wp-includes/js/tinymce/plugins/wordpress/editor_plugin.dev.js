/**
 * WordPress plugin.
 */

(function() {
	var DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.WordPress', {
		init : function(ed, url) {
			var t = this, tbId = ed.getParam('wordpress_adv_toolbar', 'toolbar2'), last = 0, moreHTML, nextpageHTML;
			moreHTML = '<img src="' + url + '/img/trans.gif" class="mceWPmore mceItemNoResize" title="'+ed.getLang('wordpress.wp_more_alt')+'" />';
			nextpageHTML = '<img src="' + url + '/img/trans.gif" class="mceWPnextpage mceItemNoResize" title="'+ed.getLang('wordpress.wp_page_alt')+'" />';

			if ( getUserSetting('hidetb', '0') == '1' )
				ed.settings.wordpress_adv_hidden = 0;

			// Hides the specified toolbar and resizes the iframe
			ed.onPostRender.add(function() {
				var adv_toolbar = ed.controlManager.get(tbId);
				if ( ed.getParam('wordpress_adv_hidden', 1) && adv_toolbar ) {
					DOM.hide(adv_toolbar.id);
					t._resizeIframe(ed, tbId, 28);
				}
			});

			// Register commands
			ed.addCommand('WP_More', function() {
				ed.execCommand('mceInsertContent', 0, moreHTML);
			});

			ed.addCommand('WP_Page', function() {
				ed.execCommand('mceInsertContent', 0, nextpageHTML);
			});

			ed.addCommand('WP_Help', function() {
					ed.windowManager.open({
						url : tinymce.baseURL + '/wp-mce-help.php',
						width : 450,
						height : 420,
						inline : 1
					});
				});

			ed.addCommand('WP_Adv', function() {
				var cm = ed.controlManager, id = cm.get(tbId).id;

				if ( 'undefined' == id )
					return;

				if ( DOM.isHidden(id) ) {
					cm.setActive('wp_adv', 1);
					DOM.show(id);
					t._resizeIframe(ed, tbId, -28);
					ed.settings.wordpress_adv_hidden = 0;
					setUserSetting('hidetb', '1');
				} else {
					cm.setActive('wp_adv', 0);
					DOM.hide(id);
					t._resizeIframe(ed, tbId, 28);
					ed.settings.wordpress_adv_hidden = 1;
					setUserSetting('hidetb', '0');
				}
			});

			// Register buttons
			ed.addButton('wp_more', {
				title : 'wordpress.wp_more_desc',
				image : url + '/img/more.gif',
				cmd : 'WP_More'
			});

			ed.addButton('wp_page', {
				title : 'wordpress.wp_page_desc',
				image : url + '/img/page.gif',
				cmd : 'WP_Page'
			});

			ed.addButton('wp_help', {
				title : 'wordpress.wp_help_desc',
				image : url + '/img/help.gif',
				cmd : 'WP_Help'
			});

			ed.addButton('wp_adv', {
				title : 'wordpress.wp_adv_desc',
				image : url + '/img/toolbars.gif',
				cmd : 'WP_Adv'
			});

			// Add Media buttons
			ed.addButton('add_media', {
				title : 'wordpress.add_media',
				image : url + '/img/media.gif',
				onclick : function() {
					tb_show('', tinymce.DOM.get('add_media').href);
					tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
				}
			});

			ed.addButton('add_image', {
				title : 'wordpress.add_image',
				image : url + '/img/image.gif',
				onclick : function() {
					tb_show('', tinymce.DOM.get('add_image').href);
					tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
				}
			});

			ed.addButton('add_video', {
				title : 'wordpress.add_video',
				image : url + '/img/video.gif',
				onclick : function() {
					tb_show('', tinymce.DOM.get('add_video').href);
					tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
				}
			});

			ed.addButton('add_audio', {
				title : 'wordpress.add_audio',
				image : url + '/img/audio.gif',
				onclick : function() {
					tb_show('', tinymce.DOM.get('add_audio').href);
					tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
				}
			});

			// Add Media buttons to fullscreen
			ed.onBeforeExecCommand.add(function(ed, cmd, ui, val) {
				var DOM = tinymce.DOM;
				if ( 'mceFullScreen' != cmd ) return;
				if ( 'mce_fullscreen' != ed.id && DOM.get('add_audio') && DOM.get('add_video') && DOM.get('add_image') && DOM.get('add_media') )
					ed.settings.theme_advanced_buttons1 += ',|,add_image,add_video,add_audio,add_media';
			});

			// Add class "alignleft", "alignright" and "aligncenter" when selecting align for images.
			ed.addCommand('JustifyLeft', function() {
				var n = ed.selection.getNode();

				if ( n.nodeName != 'IMG' )
					ed.editorCommands.mceJustify('JustifyLeft', 'left');
				else ed.plugins.wordpress.do_align(n, 'alignleft');
			});

			ed.addCommand('JustifyRight', function() {
				var n = ed.selection.getNode();

				if ( n.nodeName != 'IMG' )
					ed.editorCommands.mceJustify('JustifyRight', 'right');
				else ed.plugins.wordpress.do_align(n, 'alignright');
			});

			ed.addCommand('JustifyCenter', function() {
				var n = ed.selection.getNode(), P = ed.dom.getParent(n, 'p'), DL = ed.dom.getParent(n, 'dl');

				if ( n.nodeName == 'IMG' && ( P || DL ) )
					ed.plugins.wordpress.do_align(n, 'aligncenter');
				else ed.editorCommands.mceJustify('JustifyCenter', 'center');
			});

			// Word count if script is loaded
			if ( 'undefined' != typeof wpWordCount ) {
				ed.onKeyUp.add(function(ed, e) {
					if ( e.keyCode == last ) return;
					if ( 13 == e.keyCode || 8 == last || 46 == last ) wpWordCount.wc( ed.getContent({format : 'raw'}) );
					last = e.keyCode;
				});
			};

			ed.onSaveContent.add(function(ed, o) {
				if ( typeof(switchEditors) == 'object' ) {
					if ( ed.isHidden() )
						o.content = o.element.value;
					else
						o.content = switchEditors.pre_wpautop(o.content);
				}
			});

			// Add listeners to handle more break
			t._handleMoreBreak(ed, url);

			// Add custom shortcuts
			ed.addShortcut('alt+shift+c', ed.getLang('justifycenter_desc'), 'JustifyCenter');
			ed.addShortcut('alt+shift+r', ed.getLang('justifyright_desc'), 'JustifyRight');
			ed.addShortcut('alt+shift+l', ed.getLang('justifyleft_desc'), 'JustifyLeft');
			ed.addShortcut('alt+shift+j', ed.getLang('justifyfull_desc'), 'JustifyFull');
			ed.addShortcut('alt+shift+q', ed.getLang('blockquote_desc'), 'mceBlockQuote');
			ed.addShortcut('alt+shift+u', ed.getLang('bullist_desc'), 'InsertUnorderedList');
			ed.addShortcut('alt+shift+o', ed.getLang('numlist_desc'), 'InsertOrderedList');
			ed.addShortcut('alt+shift+d', ed.getLang('striketrough_desc'), 'Strikethrough');
			ed.addShortcut('alt+shift+n', ed.getLang('spellchecker.desc'), 'mceSpellCheck');
			ed.addShortcut('alt+shift+a', ed.getLang('link_desc'), 'mceLink');
			ed.addShortcut('alt+shift+s', ed.getLang('unlink_desc'), 'unlink');
			ed.addShortcut('alt+shift+m', ed.getLang('image_desc'), 'mceImage');
			ed.addShortcut('alt+shift+g', ed.getLang('fullscreen.desc'), 'mceFullScreen');
			ed.addShortcut('alt+shift+z', ed.getLang('wp_adv_desc'), 'WP_Adv');
			ed.addShortcut('alt+shift+h', ed.getLang('help_desc'), 'WP_Help');
			ed.addShortcut('alt+shift+t', ed.getLang('wp_more_desc'), 'WP_More');
			ed.addShortcut('alt+shift+p', ed.getLang('wp_page_desc'), 'WP_Page');
			ed.addShortcut('ctrl+s', ed.getLang('save_desc'), function(){if('function'==typeof autosave)autosave();});

			if ( tinymce.isWebKit ) {
				ed.addShortcut('alt+shift+b', ed.getLang('bold_desc'), 'Bold');
				ed.addShortcut('alt+shift+i', ed.getLang('italic_desc'), 'Italic');
			}
		},

		getInfo : function() {
			return {
				longname : 'WordPress Plugin',
				author : 'WordPress', // add Moxiecode?
				authorurl : 'http://wordpress.org',
				infourl : 'http://wordpress.org',
				version : '3.0'
			};
		},

		// Internal functions
		do_align : function(n, a) {
			var P, DL, DIV, cls, c, ed = tinyMCE.activeEditor;

			if ( /^(mceItemFlash|mceItemShockWave|mceItemWindowsMedia|mceItemQuickTime|mceItemRealMedia)$/.test(n.className) )
				return;

			P = ed.dom.getParent(n, 'p');
			DL = ed.dom.getParent(n, 'dl');
			DIV = ed.dom.getParent(n, 'div');

			if ( DL && DIV ) {
				cls = ed.dom.hasClass(DL, a) ? 'alignnone' : a;
				DL.className = DL.className.replace(/align[^ '"]+\s?/g, '');
				ed.dom.addClass(DL, cls);
				c = (cls == 'aligncenter') ? ed.dom.addClass(DIV, 'mceIEcenter') : ed.dom.removeClass(DIV, 'mceIEcenter');
			} else if ( P ) {
				cls = ed.dom.hasClass(n, a) ? 'alignnone' : a;
				n.className = n.className.replace(/align[^ '"]+\s?/g, '');
				ed.dom.addClass(n, cls);
				if ( cls == 'aligncenter' )
					ed.dom.setStyle(P, 'textAlign', 'center');
				else if (P.style && P.style.textAlign == 'center')
					ed.dom.setStyle(P, 'textAlign', '');
			}

			ed.execCommand('mceRepaint');
		},

		// Resizes the iframe by a relative height value
		_resizeIframe : function(ed, tb_id, dy) {
			var ifr = ed.getContentAreaContainer().firstChild;

			DOM.setStyle(ifr, 'height', ifr.clientHeight + dy); // Resize iframe
			ed.theme.deltaHeight += dy; // For resize cookie
		},

		_handleMoreBreak : function(ed, url) {
			var moreHTML, nextpageHTML;
			
			moreHTML = '<img src="' + url + '/img/trans.gif" alt="$1" class="mceWPmore mceItemNoResize" title="'+ed.getLang('wordpress.wp_more_alt')+'" />';
			nextpageHTML = '<img src="' + url + '/img/trans.gif" class="mceWPnextpage mceItemNoResize" title="'+ed.getLang('wordpress.wp_page_alt')+'" />';

			// Load plugin specific CSS into editor
			ed.onInit.add(function() {
				ed.dom.loadCSS(url + '/css/content.css');
			});

			// Display morebreak instead if img in element path
			ed.onPostRender.add(function() {
				if (ed.theme.onResolveName) {
					ed.theme.onResolveName.add(function(th, o) {
						if (o.node.nodeName == 'IMG') {
							if ( ed.dom.hasClass(o.node, 'mceWPmore') )
								o.name = 'wpmore';
							if ( ed.dom.hasClass(o.node, 'mceWPnextpage') )
								o.name = 'wppage';
						}

					});
				}
			});

			// Replace morebreak with images
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = o.content.replace(/<!--more(.*?)-->/g, moreHTML);
				o.content = o.content.replace(/<!--nextpage-->/g, nextpageHTML);
			});

			// Replace images with morebreak
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = o.content.replace(/<img[^>]+>/g, function(im) {
						if (im.indexOf('class="mceWPmore') !== -1) {
							var m, moretext = (m = im.match(/alt="(.*?)"/)) ? m[1] : '';
							im = '<!--more'+moretext+'-->';
						}
						if (im.indexOf('class="mceWPnextpage') !== -1)
							im = '<!--nextpage-->';

						return im;
					});
			});

			// Set active buttons if user selected pagebreak or more break
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('wp_page', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'mceWPnextpage'));
				cm.setActive('wp_more', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'mceWPmore'));
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wordpress', tinymce.plugins.WordPress);
})();
