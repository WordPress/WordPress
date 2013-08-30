/**
 * WordPress plugin.
 */

(function() {
	var DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.WordPress', {
		init : function(ed, url) {
			var t = this, tbId = ed.getParam('wordpress_adv_toolbar', 'toolbar2'), last = 0, moreHTML, nextpageHTML, closeOnClick, mod_key, style;
			moreHTML = '<img src="' + url + '/img/trans.gif" class="mce-wp-more mceItemNoResize" title="'+ed.getLang('wordpress.wp_more_alt')+'" />';
			nextpageHTML = '<img src="' + url + '/img/trans.gif" class="mce-wp-nextpage mceItemNoResize" title="'+ed.getLang('wordpress.wp_page_alt')+'" />';

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

			ed.addCommand('WP_Medialib', function() {
				if ( typeof wp !== 'undefined' && wp.media && wp.media.editor )
					wp.media.editor.open( ed.id );
			});

			// Register buttons
			ed.addButton('wp_more', {
				title : 'wordpress.wp_more_desc',
				cmd : 'WP_More'
			});

			ed.addButton('wp_page', {
				title : 'wordpress.wp_page_desc',
				image : url + '/img/page.gif',
				cmd : 'WP_Page'
			});

			ed.addButton('wp_help', {
				title : 'wordpress.wp_help_desc',
				cmd : 'WP_Help'
			});

			ed.addButton('wp_adv', {
				title : 'wordpress.wp_adv_desc',
				cmd : 'WP_Adv'
			});

			// Add Media button
			ed.addButton('add_media', {
				title : 'wordpress.add_media',
				image : url + '/img/image.gif',
				cmd : 'WP_Medialib'
			});

			// Add Media buttons to fullscreen and handle align buttons for image captions
			ed.onBeforeExecCommand.add(function(ed, cmd, ui, val, o) {
				var DOM = tinymce.DOM, n, DL, DIV, cls, a, align;
				if ( 'mceFullScreen' == cmd ) {
					if ( 'mce_fullscreen' != ed.id && DOM.select('a.thickbox').length )
						ed.settings.theme_advanced_buttons1 += ',|,add_media';
				}

				if ( 'JustifyLeft' == cmd || 'JustifyRight' == cmd || 'JustifyCenter' == cmd ) {
					n = ed.selection.getNode();

					if ( n.nodeName == 'IMG' ) {
						align = cmd.substr(7).toLowerCase();
						a = 'align' + align;
						DL = ed.dom.getParent(n, 'dl.wp-caption');
						DIV = ed.dom.getParent(n, 'div.mceTemp');

						if ( DL && DIV ) {
							cls = ed.dom.hasClass(DL, a) ? 'alignnone' : a;
							DL.className = DL.className.replace(/align[^ '"]+\s?/g, '');
							ed.dom.addClass(DL, cls);

							if (cls == 'aligncenter')
								ed.dom.addClass(DIV, 'mceIEcenter');
							else
								ed.dom.removeClass(DIV, 'mceIEcenter');

							o.terminate = true;
							ed.execCommand('mceRepaint');
						} else {
							if ( ed.dom.hasClass(n, a) )
								ed.dom.addClass(n, 'alignnone');
							else
								ed.dom.removeClass(n, 'alignnone');
						}
					}
				}

				if ( tinymce.isWebKit && ( 'InsertUnorderedList' == cmd || 'InsertOrderedList' == cmd ) ) {
					if ( !style )
						style = ed.dom.create('style', {'type': 'text/css'}, '#tinymce,#tinymce span,#tinymce li,#tinymce li>span,#tinymce p,#tinymce p>span{font:medium sans-serif;color:#000;line-height:normal;}');

					ed.getDoc().head.appendChild( style );
				}
			});

			ed.onExecCommand.add( function( ed, cmd, ui, val ) {
				if ( tinymce.isWebKit && style && ( 'InsertUnorderedList' == cmd || 'InsertOrderedList' == cmd ) )
					ed.dom.remove( style );
			});

			ed.onInit.add(function(ed) {
				var bodyClass = ed.getParam('body_class', ''), body = ed.getBody();

				// add body classes
				if ( bodyClass )
					bodyClass = bodyClass.split(' ');
				else
					bodyClass = [];

				if ( ed.getParam('directionality', '') == 'rtl' )
					bodyClass.push('rtl');

				if ( tinymce.isIE9 )
					bodyClass.push('ie9');
				else if ( tinymce.isIE8 )
					bodyClass.push('ie8');
				else if ( tinymce.isIE7 )
					bodyClass.push('ie7');

				if ( ed.id != 'wp_mce_fullscreen' && ed.id != 'mce_fullscreen' )
					bodyClass.push('wp-editor');
				else if ( ed.id == 'mce_fullscreen' )
					bodyClass.push('mce-fullscreen');

				tinymce.each( bodyClass, function(cls){
					if ( cls )
						ed.dom.addClass(body, cls);
				});

				// make sure these run last
				ed.onNodeChange.add( function(ed, cm, e) {
					var DL;

					if ( e.nodeName == 'IMG' ) {
						DL = ed.dom.getParent(e, 'dl.wp-caption');
					} else if ( e.nodeName == 'DIV' && ed.dom.hasClass(e, 'mceTemp') ) {
						DL = e.firstChild;

						if ( ! ed.dom.hasClass(DL, 'wp-caption') )
							DL = false;
					}

					if ( DL ) {
						if ( ed.dom.hasClass(DL, 'alignleft') )
							cm.setActive('justifyleft', 1);
						else if ( ed.dom.hasClass(DL, 'alignright') )
							cm.setActive('justifyright', 1);
						else if ( ed.dom.hasClass(DL, 'aligncenter') )
							cm.setActive('justifycenter', 1);
					}
				});

				// remove invalid parent paragraphs when pasting HTML and/or switching to the HTML editor and back
				ed.onBeforeSetContent.add(function(ed, o) {
					if ( o.content ) {
						o.content = o.content.replace(/<p>\s*<(p|div|ul|ol|dl|table|blockquote|h[1-6]|fieldset|pre|address)( [^>]*)?>/gi, '<$1$2>');
						o.content = o.content.replace(/<\/(p|div|ul|ol|dl|table|blockquote|h[1-6]|fieldset|pre|address)>\s*<\/p>/gi, '</$1>');
					}
				});
			});

			// Word count
			if ( 'undefined' != typeof(jQuery) ) {
				ed.onKeyUp.add(function(ed, e) {
					var k = e.keyCode || e.charCode;

					if ( k == last )
						return;

					if ( 13 == k || 8 == last || 46 == last )
						jQuery(document).triggerHandler('wpcountwords', [ ed.getContent({format : 'raw'}) ]);

					last = k;
				});
			};

			// keep empty paragraphs :(
			ed.onSaveContent.addToTop(function(ed, o) {
				o.content = o.content.replace(/<p>(<br ?\/?>|\u00a0|\uFEFF)?<\/p>/g, '<p>&nbsp;</p>');
			});

			// Fix bug in iOS Safari where it's impossible to type after a touchstart event on the parent document.
			// Happens after zooming in or out while the keyboard is open. See #25131.
			if ( tinymce.isIOS5 ) {
				ed.onKeyDown.add( function() {
					if ( document.activeElement == document.body ) {
						ed.getWin().focus();
					}
				});
			}

			ed.onSaveContent.add(function(ed, o) {
				// If editor is hidden, we just want the textarea's value to be saved
				if ( ed.isHidden() )
					o.content = o.element.value;
				else if ( ed.getParam('wpautop', true) && typeof(switchEditors) == 'object' )
					o.content = switchEditors.pre_wpautop(o.content);
			});

			/* disable for now
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._setEmbed(o.content);
			});

			ed.onPostProcess.add(function(ed, o) {
				if ( o.get )
					o.content = t._getEmbed(o.content);
			});
			*/

			// Add listeners to handle more break
			t._handleMoreBreak(ed, url);

			// Add custom shortcuts
			mod_key = 'alt+shift';

		//	if ( tinymce.isGecko ) // disable for mow, too many shortcuts conflicts
		//		mod_key = 'ctrl+alt';

			ed.addShortcut(mod_key + '+c', 'justifycenter_desc', 'JustifyCenter');
			ed.addShortcut(mod_key + '+r', 'justifyright_desc', 'JustifyRight');
			ed.addShortcut(mod_key + '+l', 'justifyleft_desc', 'JustifyLeft');
			ed.addShortcut(mod_key + '+j', 'justifyfull_desc', 'JustifyFull');
			ed.addShortcut(mod_key + '+q', 'blockquote_desc', 'mceBlockQuote');
			ed.addShortcut(mod_key + '+u', 'bullist_desc', 'InsertUnorderedList');
			ed.addShortcut(mod_key + '+o', 'numlist_desc', 'InsertOrderedList');
			ed.addShortcut(mod_key + '+n', 'spellchecker.desc', 'mceSpellCheck');
			ed.addShortcut(mod_key + '+a', 'link_desc', 'WP_Link');
			ed.addShortcut(mod_key + '+s', 'unlink_desc', 'unlink');
			ed.addShortcut(mod_key + '+m', 'image_desc', 'WP_Medialib');
			ed.addShortcut(mod_key + '+z', 'wordpress.wp_adv_desc', 'WP_Adv');
			ed.addShortcut(mod_key + '+t', 'wordpress.wp_more_desc', 'WP_More');
			ed.addShortcut(mod_key + '+d', 'striketrough_desc', 'Strikethrough');
			ed.addShortcut(mod_key + '+h', 'help_desc', 'WP_Help');
			ed.addShortcut(mod_key + '+p', 'wordpress.wp_page_desc', 'WP_Page');
			ed.addShortcut('ctrl+s', 'save_desc', function(){if('function'==typeof autosave)autosave();});

			if ( /\bwpfullscreen\b/.test(ed.settings.plugins) )
				ed.addShortcut(mod_key + '+w', 'wordpress.wp_fullscreen_desc', 'wpFullScreen');
			else if ( /\bfullscreen\b/.test(ed.settings.plugins) )
				ed.addShortcut(mod_key + '+g', 'fullscreen.desc', 'mceFullScreen');

			// popup buttons for images and the gallery
			ed.onInit.add(function(ed) {
				tinymce.dom.Event.add(ed.getWin(), 'scroll', function(e) {
					ed.plugins.wordpress._hideButtons();
				});
				tinymce.dom.Event.add(ed.getBody(), 'dragstart', function(e) {
					ed.plugins.wordpress._hideButtons();
				});
			});

			ed.onBeforeExecCommand.add(function(ed, cmd, ui, val) {
				ed.plugins.wordpress._hideButtons();
			});

			ed.onSaveContent.add(function(ed, o) {
				ed.plugins.wordpress._hideButtons();
			});

			ed.onMouseDown.add(function(ed, e) {
				if ( e.target.nodeName != 'IMG' )
					ed.plugins.wordpress._hideButtons();
			});

			ed.onKeyDown.add(function(ed, e){
				if ( e.which == tinymce.VK.DELETE || e.which == tinymce.VK.BACKSPACE )
					ed.plugins.wordpress._hideButtons();
			});

			closeOnClick = function(e){
				var id;

				if ( e.target.id == 'mceModalBlocker' || e.target.className == 'ui-widget-overlay' ) {
					for ( id in ed.windowManager.windows ) {
						ed.windowManager.close(null, id);
					}
				}
			}

			// close popups when clicking on the background
			tinymce.dom.Event.remove(document.body, 'click', closeOnClick);
			tinymce.dom.Event.add(document.body, 'click', closeOnClick);
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
		_setEmbed : function(c) {
			return c.replace(/\[embed\]([\s\S]+?)\[\/embed\][\s\u00a0]*/g, function(a,b){
				return '<img width="300" height="200" src="' + tinymce.baseURL + '/plugins/wordpress/img/trans.gif" class="wp-oembed mceItemNoResize" alt="'+b+'" title="'+b+'" />';
			});
		},

		_getEmbed : function(c) {
			return c.replace(/<img[^>]+>/g, function(a) {
				if ( a.indexOf('class="wp-oembed') != -1 ) {
					var u = a.match(/alt="([^\"]+)"/);
					if ( u[1] )
						a = '[embed]' + u[1] + '[/embed]';
				}
				return a;
			});
		},

		_showButtons : function(n, id) {
			var ed = tinyMCE.activeEditor, p1, p2, vp, DOM = tinymce.DOM, X, Y;

			vp = ed.dom.getViewPort(ed.getWin());
			p1 = DOM.getPos(ed.getContentAreaContainer());
			p2 = ed.dom.getPos(n);

			X = Math.max(p2.x - vp.x, 0) + p1.x;
			Y = Math.max(p2.y - vp.y, 0) + p1.y;

			DOM.setStyles(id, {
				'top' : Y+5+'px',
				'left' : X+5+'px',
				'display' : 'block'
			});
		},

		_hideButtons : function() {
			var DOM = tinymce.DOM;
			DOM.hide( DOM.select('#wp_editbtns, #wp_gallerybtns') );
		},

		// Resizes the iframe by a relative height value
		_resizeIframe : function(ed, tb_id, dy) {
			var ifr = ed.getContentAreaContainer().firstChild;

			DOM.setStyle(ifr, 'height', ifr.clientHeight + dy); // Resize iframe
			ed.theme.deltaHeight += dy; // For resize cookie
		},

		_handleMoreBreak : function(ed, url) {
			var moreHTML, nextpageHTML;

			moreHTML = '<img src="' + url + '/img/trans.gif" alt="$1" class="mce-wp-more mceItemNoResize" title="'+ed.getLang('wordpress.wp_more_alt')+'" />';
			nextpageHTML = '<img src="' + url + '/img/trans.gif" class="mce-wp-nextpage mceItemNoResize" title="'+ed.getLang('wordpress.wp_page_alt')+'" />';

			// Display morebreak instead if img in element path
			ed.onPostRender.add(function() {
				if (ed.theme.onResolveName) {
					ed.theme.onResolveName.add(function(th, o) {
						if (o.node.nodeName == 'IMG') {
							if ( ed.dom.hasClass(o.node, 'mce-wp-more') )
								o.name = 'wpmore';
							if ( ed.dom.hasClass(o.node, 'mce-wp-nextpage') )
								o.name = 'wppage';
						}

					});
				}
			});

			// Replace morebreak with images
			ed.onBeforeSetContent.add(function(ed, o) {
				if ( o.content ) {
					o.content = o.content.replace(/<!--more(.*?)-->/g, moreHTML);
					o.content = o.content.replace(/<!--nextpage-->/g, nextpageHTML);
				}
			});

			// Replace images with morebreak
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = o.content.replace(/<img[^>]+>/g, function(im) {
						if (im.indexOf('class="mce-wp-more') !== -1) {
							var m, moretext = (m = im.match(/alt="(.*?)"/)) ? m[1] : '';
							im = '<!--more'+moretext+'-->';
						}
						if (im.indexOf('class="mce-wp-nextpage') !== -1)
							im = '<!--nextpage-->';

						return im;
					});
			});

			// Set active buttons if user selected pagebreak or more break
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('wp_page', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'mce-wp-nextpage'));
				cm.setActive('wp_more', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'mce-wp-more'));
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wordpress', tinymce.plugins.WordPress);
})();
