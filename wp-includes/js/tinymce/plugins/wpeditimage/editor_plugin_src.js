
(function() {
	tinymce.create('tinymce.plugins.wpEditImage', {
		url: '',
		editor: {},

		init: function(ed, url) {
			var t = this, mouse = {};

			t.url = url;
			t.editor = ed;
			t._createButtons();

			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('...');
			ed.addCommand('WP_EditImage', function() {
				var el = ed.selection.getNode(), vp, H, W, cls = ed.dom.getAttrib(el, 'class');

				if ( cls.indexOf('mceItem') != -1 || cls.indexOf('wpGallery') != -1 || el.nodeName != 'IMG' )
					return;

				vp = tinymce.DOM.getViewPort();
				H = 680 < (vp.h - 70) ? 680 : vp.h - 70;
				W = 650 < vp.w ? 650 : vp.w;

				ed.windowManager.open({
					file: url + '/editimage.html',
					width: W+'px',
					height: H+'px',
					inline: true
				});
			});

			ed.onInit.add(function(ed) {
				ed.dom.events.add(ed.getBody(), 'dragstart', function(e) {
					var parent;

					if ( e.target.nodeName == 'IMG' && ( parent = ed.dom.getParent(e.target, 'div.mceTemp') ) ) {
						ed.selection.select(parent);
					}
				});
			});

			// resize the caption <dl> when the image is soft-resized by the user (only possible in Firefox and IE)
			ed.onMouseUp.add(function(ed, e) {
				if ( tinymce.isWebKit || tinymce.isOpera )
					return;

				if ( mouse.x && (e.clientX != mouse.x || e.clientY != mouse.y) ) {
					var n = ed.selection.getNode();

					if ( 'IMG' == n.nodeName ) {
						window.setTimeout(function(){
							var DL = ed.dom.getParent(n, 'dl.wp-caption'), width;

							if ( n.width != mouse.img_w || n.height != mouse.img_h )
								n.className = n.className.replace(/size-[^ "']+/, '');

							if ( DL ) {
								width = ed.dom.getAttrib(n, 'width') || n.width;
								width = parseInt(width, 10);
								ed.dom.setStyle(DL, 'width', 10 + width);
								ed.execCommand('mceRepaint');
							}
						}, 100);
					}
				}
				mouse = {};
			});

			// show editimage buttons
			ed.onMouseDown.add(function(ed, e) {
				var target = e.target;

				if ( target.nodeName != 'IMG' ) {
					if ( target.firstChild && target.firstChild.nodeName == 'IMG' && target.childNodes.length == 1 )
						target = target.firstChild;
					else
						return;
				}

				if ( ed.dom.getAttrib(target, 'class').indexOf('mceItem') == -1 ) {
					mouse = {
						x: e.clientX,
						y: e.clientY,
						img_w: target.clientWidth,
						img_h: target.clientHeight
					};

					ed.plugins.wordpress._showButtons(target, 'wp_editbtns');
				}
			});

			// when pressing Return inside a caption move the caret to a new parapraph under it
			ed.onKeyPress.add(function(ed, e) {
				var n, DL, DIV, P;

				if ( e.keyCode == 13 ) {
					n = ed.selection.getNode();
					DL = ed.dom.getParent(n, 'dl.wp-caption');

					if ( DL )
						DIV = ed.dom.getParent(DL, 'div.mceTemp');

					if ( DIV ) {
						P = ed.dom.create('p', {}, '<br>');
						ed.dom.insertAfter( P, DIV );
						ed.selection.select(P.firstChild);

						if ( tinymce.isIE ) {
							ed.selection.setContent('');
						} else {
							ed.selection.setContent('<br _moz_dirty="">');
							ed.selection.setCursorLocation(P, 0);
						}

						ed.dom.events.cancel(e);
						return false;
					}
				}
			});

			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = ed.wpSetImgCaption(o.content);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = ed.wpGetImgCaption(o.content);
			});

			ed.wpSetImgCaption = function(content) {
				return t._do_shcode(content);
			};

			ed.wpGetImgCaption = function(content) {
				return t._get_shcode(content);
			};
		},

		_do_shcode : function(content) {
			return content.replace(/(?:<p>)?\[(?:wp_)?caption([^\]]+)\]([\s\S]+?)\[\/(?:wp_)?caption\](?:<\/p>)?/g, function(a,b,c){
				var id, cls, w, cap, div_cls, img, trim = tinymce.trim;

				id = b.match(/id=['"]([^'"]*)['"] ?/);
				if ( id )
					b = b.replace(id[0], '');

				cls = b.match(/align=['"]([^'"]*)['"] ?/);
				if ( cls )
					b = b.replace(cls[0], '');

				w = b.match(/width=['"]([0-9]*)['"] ?/);
				if ( w )
					b = b.replace(w[0], '');

				c = trim(c);
				img = c.match(/((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)([\s\S]*)/i);

				if ( img && img[2] ) {
					cap = trim( img[2] );
					img = trim( img[1] );
				} else {
					// old captions shortcode style
					cap = trim(b).replace(/caption=['"]/, '').replace(/['"]$/, '');
					img = c;
				}

				id = ( id && id[1] ) ? id[1] : '';
				cls = ( cls && cls[1] ) ? cls[1] : 'alignnone';
				w = ( w && w[1] ) ? w[1] : '';

				if ( !w || !cap )
					return c;

				div_cls = 'mceTemp';
				if ( cls == 'aligncenter' )
					div_cls += ' mceIEcenter';

				return '<div class="'+div_cls+'"><dl id="'+id+'" class="wp-caption '+cls+'" style="width: '+( 10 + parseInt(w) )+
				'px"><dt class="wp-caption-dt">'+img+'</dt><dd class="wp-caption-dd">'+cap+'</dd></dl></div>';
			});
		},

		_get_shcode : function(content) {
			return content.replace(/<div (?:id="attachment_|class="mceTemp)[^>]*>([\s\S]+?)<\/div>/g, function(a, b){
				var ret = b.replace(/<dl ([^>]+)>\s*<dt [^>]+>([\s\S]+?)<\/dt>\s*<dd [^>]+>([\s\S]*?)<\/dd>\s*<\/dl>/gi, function(a,b,c,cap){
					var id, cls, w;

					w = c.match(/width="([0-9]*)"/);
					w = ( w && w[1] ) ? w[1] : '';

					if ( !w || !cap )
						return c;

					id = b.match(/id="([^"]*)"/);
					id = ( id && id[1] ) ? id[1] : '';

					cls = b.match(/class="([^"]*)"/);
					cls = ( cls && cls[1] ) ? cls[1] : '';
					cls = cls.match(/align[a-z]+/) || 'alignnone';

					cap = cap.replace(/\r\n|\r/g, '\n').replace(/<[a-zA-Z0-9]+( [^<>]+)?>/g, function(a){
						// no line breaks inside HTML tags
						return a.replace(/[\r\n\t]+/, ' ');
					});

					// convert remaining line breaks to <br>
					cap = cap.replace(/\s*\n\s*/g, '<br />');

					return '[caption id="'+id+'" align="'+cls+'" width="'+w+'"]'+c+' '+cap+'[/caption]';
				});

				if ( ret.indexOf('[caption') !== 0 ) {
					// the caption html seems brocken, try to find the image that may be wrapped in a link
					// and may be followed by <p> with the caption text.
					ret = b.replace(/[\s\S]*?((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)(<p>[\s\S]*<\/p>)?[\s\S]*/gi, '<p>$1</p>$2');
				}

				return ret;
			});
		},

		_createButtons : function() {
			var t = this, ed = tinyMCE.activeEditor, DOM = tinymce.DOM, editButton, dellButton;

			DOM.remove('wp_editbtns');

			DOM.add(document.body, 'div', {
				id : 'wp_editbtns',
				style : 'display:none;'
			});

			editButton = DOM.add('wp_editbtns', 'img', {
				src : t.url+'/img/image.png',
				id : 'wp_editimgbtn',
				width : '24',
				height : '24',
				title : ed.getLang('wpeditimage.edit_img')
			});

			tinymce.dom.Event.add(editButton, 'mousedown', function(e) {
				var ed = tinyMCE.activeEditor;
				ed.windowManager.bookmark = ed.selection.getBookmark('simple');
				ed.execCommand("WP_EditImage");
			});

			dellButton = DOM.add('wp_editbtns', 'img', {
				src : t.url+'/img/delete.png',
				id : 'wp_delimgbtn',
				width : '24',
				height : '24',
				title : ed.getLang('wpeditimage.del_img')
			});

			tinymce.dom.Event.add(dellButton, 'mousedown', function(e) {
				var ed = tinyMCE.activeEditor, el = ed.selection.getNode(), p;

				if ( el.nodeName == 'IMG' && ed.dom.getAttrib(el, 'class').indexOf('mceItem') == -1 ) {
					if ( (p = ed.dom.getParent(el, 'div')) && ed.dom.hasClass(p, 'mceTemp') )
						ed.dom.remove(p);
					else if ( (p = ed.dom.getParent(el, 'A')) && p.childNodes.length == 1 )
						ed.dom.remove(p);
					else
						ed.dom.remove(el);

					ed.execCommand('mceRepaint');
					return false;
				}
			});
		},

		getInfo : function() {
			return {
				longname : 'Edit Image',
				author : 'WordPress',
				authorurl : 'http://wordpress.org',
				infourl : '',
				version : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('wpeditimage', tinymce.plugins.wpEditImage);
})();
