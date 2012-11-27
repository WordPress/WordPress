
(function() {
	tinymce.create('tinymce.plugins.wpEditImage', {
		url: '',
		editor: {},

		init: function(ed, url) {
			var t = this, mouse = {};

			t.url = url;
			t.editor = ed;
			t._createButtons();

			ed.addCommand('WP_EditImage', t._editImage);

			ed.onInit.add(function(ed) {
				ed.dom.events.add(ed.getBody(), 'mousedown', function(e) {
					var parent;

					if ( e.target.nodeName == 'IMG' && ( parent = ed.dom.getParent(e.target, 'div.mceTemp') ) ) {
						if ( tinymce.isGecko )
							ed.selection.select(parent);
						else if ( tinymce.isWebKit )
							ed.dom.events.prevent(e);
					}
				});

				// when pressing Return inside a caption move the caret to a new parapraph under it
				ed.dom.events.add(ed.getBody(), 'keydown', function(e) {
					var n, DL, DIV, P, content;

					if ( e.keyCode == 13 ) {
						n = ed.selection.getNode();
						DL = ed.dom.getParent(n, 'dl.wp-caption');

						if ( DL )
							DIV = ed.dom.getParent(DL, 'div.mceTemp');

						if ( DIV ) {
							ed.dom.events.cancel(e);
							P = ed.dom.create('p', {}, '\uFEFF');
							ed.dom.insertAfter( P, DIV );
							ed.selection.setCursorLocation(P, 0);
							return false;
						}
					}
				});

				// iOS6 doesn't show the buttons properly on click, show them on 'touchstart'
				if ( 'ontouchstart' in window ) {
					ed.dom.events.add(ed.getBody(), 'touchstart', function(e){
						t._showButtons(e);
					});
				}
			});

			// resize the caption <dl> when the image is soft-resized by the user
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
			ed.onMouseDown.add(function(ed, e){
				t._showButtons(e);
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

			// When inserting content, if the caret is inside a caption create new paragraph under
			// and move the caret there
			ed.onBeforeExecCommand.add(function(ed, cmd, ui, val) {
				var node, p;

				if ( cmd == 'mceInsertContent' ) {
					node = ed.dom.getParent(ed.selection.getNode(), 'div.mceTemp');

					if ( !node )
						return;

					p = ed.dom.create('p');
					ed.dom.insertAfter( p, node );
					ed.selection.setCursorLocation(p, 0);
				}
			});
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
			var t = this, ed = tinymce.activeEditor, DOM = tinymce.DOM, editButton, dellButton, isRetina;

			if ( DOM.get('wp_editbtns') )
				return;

			isRetina = ( window.devicePixelRatio && window.devicePixelRatio > 1 ) || // WebKit, Opera
				( window.matchMedia && window.matchMedia('(min-resolution:130dpi)').matches ); // Firefox, IE10, Opera

			DOM.add(document.body, 'div', {
				id : 'wp_editbtns',
				style : 'display:none;'
			});

			editButton = DOM.add('wp_editbtns', 'img', {
				src : isRetina ? t.url+'/img/image-2x.png' : t.url+'/img/image.png',
				id : 'wp_editimgbtn',
				width : '24',
				height : '24',
				title : ed.getLang('wpeditimage.edit_img')
			});

			tinymce.dom.Event.add(editButton, 'mousedown', function(e) {
				t._editImage();
				ed.plugins.wordpress._hideButtons();
			});

			dellButton = DOM.add('wp_editbtns', 'img', {
				src : isRetina ? t.url+'/img/delete-2x.png' : t.url+'/img/delete.png',
				id : 'wp_delimgbtn',
				width : '24',
				height : '24',
				title : ed.getLang('wpeditimage.del_img')
			});

			tinymce.dom.Event.add(dellButton, 'mousedown', function(e) {
				var ed = tinymce.activeEditor, el = ed.selection.getNode(), parent;

				if ( el.nodeName == 'IMG' && ed.dom.getAttrib(el, 'class').indexOf('mceItem') == -1 ) {
					if ( (parent = ed.dom.getParent(el, 'div')) && ed.dom.hasClass(parent, 'mceTemp') ) {
						ed.dom.remove(parent);
					} else {
						if ( el.parentNode.nodeName == 'A' && el.parentNode.childNodes.length == 1 )
							el = el.parentNode;

						if ( el.parentNode.nodeName == 'P' && el.parentNode.childNodes.length == 1 )
							el = el.parentNode;

						ed.dom.remove(el);
					}

					ed.execCommand('mceRepaint');
					return false;
				}
				ed.plugins.wordpress._hideButtons();
			});
		},
		
		_editImage : function() {
			var ed = tinymce.activeEditor, url = this.url, el = ed.selection.getNode(), vp, H, W, cls = el.className;

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
		},

		_showButtons : function(e) {
			var ed = this.editor, target = e.target;

			if ( target.nodeName != 'IMG' ) {
				if ( target.firstChild && target.firstChild.nodeName == 'IMG' && target.childNodes.length == 1 ) {
					target = target.firstChild;
				} else {
					ed.plugins.wordpress._hideButtons();
					return;
				}
			}

			if ( ed.dom.getAttrib(target, 'class').indexOf('mceItem') == -1 ) {
				mouse = {
					x: e.clientX,
					y: e.clientY,
					img_w: target.clientWidth,
					img_h: target.clientHeight
				};

				if ( e.type == 'touchstart' ) {
					ed.selection.select(target);
					ed.dom.events.cancel(e);
				}

				ed.plugins.wordpress._hideButtons();
				ed.plugins.wordpress._showButtons(target, 'wp_editbtns');
			}
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
