
(function() {
	tinymce.create('tinymce.plugins.wpEditImage', {

		init : function(ed, url) {
			var t = this;

			t.url = url;
			t._createButtons();

			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('...');
			ed.addCommand('WP_EditImage', function() {
				var el = ed.selection.getNode();

				if ( ed.dom.getAttrib(el, 'class').indexOf('mceItem') != -1 || el.nodeName != 'IMG' )
					return;

				tb_show('', url + '/editimage.html?ver=311g&TB_iframe=true');
				tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
			});

			ed.onInit.add(function(ed) {
				tinymce.dom.Event.add(ed.getWin(), 'scroll', function(e) {
					ed.plugins.wpeditimage.hideButtons();
				});
			});

			ed.onBeforeExecCommand.add(function(ed, cmd, ui, val) {
				ed.plugins.wpeditimage.hideButtons();
			});

			ed.onSaveContent.add(function(ed, o) {
				ed.plugins.wpeditimage.hideButtons();
			});

			ed.onMouseUp.add(function(ed, e) {
				if ( tinymce.isOpera ) {
					if ( e.target.nodeName == 'IMG' )
						ed.plugins.wpeditimage.showButtons(e.target);
				} else if ( ! tinymce.isWebKit ) {
					var n = ed.selection.getNode(), DL;
					
					if ( n.nodeName == 'IMG' && (DL = ed.dom.getParent(n, 'DL')) ) {					
						window.setTimeout(function(){
							var ed = tinyMCE.activeEditor, n = ed.selection.getNode(), DL = ed.dom.getParent(n, 'DL');
						
							if ( n.width != (parseInt(ed.dom.getStyle(DL, 'width')) - 10) ) {
								ed.dom.setStyle(DL, 'width', parseInt(n.width)+10);
								ed.execCommand('mceRepaint');
							}
						}, 100);
					}
				}
			});

			ed.onMouseDown.add(function(ed, e) {
				if ( tinymce.isOpera || e.target.nodeName != 'IMG' ) {
					t.hideButtons();
					return;
				}
				ed.plugins.wpeditimage.showButtons(e.target);
			});

			ed.onKeyPress.add(function(ed, e) {
				var DL, DIV;

				if ( e.keyCode == 13 && (DL = ed.dom.getParent(ed.selection.getNode(), 'DL')) ) {
					var P = ed.dom.create('p', {}, '&nbsp;');
					if ( (DIV = DL.parentNode) && DIV.nodeName == 'DIV' ) 
						ed.dom.insertAfter( P, DIV );
					else ed.dom.insertAfter( P, DL );

					tinymce.dom.Event.cancel(e);
					ed.selection.select(P);
					return false;
				}
			});

			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._do_shcode(o.content);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = t._get_shcode(o.content);
			});
		},

		_do_shcode : function(co) {
			return co.replace(/\[(?:wp_)?caption([^\]]+)\]([\s\S]+?)\[\/(?:wp_)?caption\][\s\u00a0]*/g, function(a,b,c){
				b = b.replace(/\\'|\\&#39;|\\&#039;/g, '&#39;').replace(/\\"|\\&quot;/g, '&quot;');
				c = c.replace(/\\&#39;|\\&#039;/g, '&#39;').replace(/\\&quot;/g, '&quot;');
				var id = b.match(/id=['"]([^'"]+)/i), cls = b.match(/align=['"]([^'"]+)/i);
				var w = b.match(/width=['"]([0-9]+)/), cap = b.match(/caption=['"]([^'"]+)/i);

				id = ( id && id[1] ) ? id[1] : '';
				cls = ( cls && cls[1] ) ? cls[1] : 'alignnone';
				w = ( w && w[1] ) ? w[1] : '';
				cap = ( cap && cap[1] ) ? cap[1] : '';
				if ( ! w || ! cap ) return c;
				
				var div_cls = (cls == 'aligncenter') ? 'mceTemp mceIEcenter' : 'mceTemp';

				return '<div class="'+div_cls+'"><dl id="'+id+'" class="wp-caption '+cls+'" style="width: '+(10+parseInt(w))+
				'px"><dt class="wp-caption-dt">'+c+'</dt><dd class="wp-caption-dd">'+cap+'</dd></dl></div>';
			});
		},

		_get_shcode : function(co) {
			return co.replace(/<div class="mceTemp[^"]*">\s*<dl([^>]+)>\s*<dt[^>]+>([\s\S]+?)<\/dt>\s*<dd[^>]+>(.+?)<\/dd>\s*<\/dl>\s*<\/div>\s*/gi, function(a,b,c,cap){
				var id = b.match(/id=['"]([^'"]+)/i), cls = b.match(/class=['"]([^'"]+)/i);
				var w = c.match(/width=['"]([0-9]+)/);

				id = ( id && id[1] ) ? id[1] : '';
				cls = ( cls && cls[1] ) ? cls[1] : 'alignnone';
				w = ( w && w[1] ) ? w[1] : '';

				if ( ! w || ! cap ) return c;
				cls = cls.match(/align[^ '"]+/) || 'alignnone';
				cap = cap.replace(/<\S[^<>]*>/gi, '').replace(/'/g, '&#39;').replace(/"/g, '&quot;');

				return '[caption id="'+id+'" align="'+cls+'" width="'+w+'" caption="'+cap+'"]'+c+'[/caption]';
			});
		},

		showButtons : function(n) {
			var t = this, ed = tinyMCE.activeEditor, p1, p2, vp, DOM = tinymce.DOM, X, Y;

			if (ed.dom.getAttrib(n, 'class').indexOf('mceItem') != -1)
				return;

			vp = ed.dom.getViewPort(ed.getWin());
			p1 = DOM.getPos(ed.getContentAreaContainer());
			p2 = ed.dom.getPos(n);

			X = Math.max(p2.x - vp.x, 0) + p1.x;
			Y = Math.max(p2.y - vp.y, 0) + p1.y;

			DOM.setStyles('wp_editbtns', {
				'top' : Y+5+'px',
				'left' : X+5+'px',
				'display' : 'block'
			});

			t.btnsTout = window.setTimeout( function(){ed.plugins.wpeditimage.hideButtons();}, 5000 );
		},

		hideButtons : function() {
			if ( tinymce.DOM.isHidden('wp_editbtns') ) return;

			tinymce.DOM.hide('wp_editbtns');
			window.clearTimeout(this.btnsTout);
		},

		_createButtons : function() {
			var t = this, ed = tinyMCE.activeEditor, DOM = tinymce.DOM;

			DOM.remove('wp_editbtns');

			var wp_editbtns = DOM.add(document.body, 'div', {
				id : 'wp_editbtns',
				style : 'display:none;'
			});

			var wp_editimgbtn = DOM.add('wp_editbtns', 'img', {
				src : t.url+'/img/image.png',
				id : 'wp_editimgbtn',
				width : '24',
				height : '24',
				title : ed.getLang('wpeditimage.edit_img')
			});

			wp_editimgbtn.onmousedown = function(e) {
				var ed = tinyMCE.activeEditor;
				ed.windowManager.bookmark = ed.selection.getBookmark('simple');
				ed.execCommand("WP_EditImage");
				this.parentNode.style.display = 'none';
			};

			var wp_delimgbtn = DOM.add('wp_editbtns', 'img', {
				src : t.url+'/img/delete.png',
				id : 'wp_delimgbtn',
				width : '24',
				height : '24',
				title : ed.getLang('wpeditimage.del_img')
			});

			wp_delimgbtn.onmousedown = function(e) {
				var ed = tinyMCE.activeEditor, el = ed.selection.getNode(), p;

				if ( el.nodeName == 'IMG' && ed.dom.getAttrib(el, 'class').indexOf('mceItem') == -1 ) {
					if ( (p = ed.dom.getParent(el, 'div')) && ed.dom.hasClass(p, 'mceTemp') )
						ed.dom.remove(p);
					else if ( (p = ed.dom.getParent(el, 'A')) && p.childNodes.length == 1 )
						ed.dom.remove(p);
					else ed.dom.remove(el);

					this.parentNode.style.display = 'none';
					ed.execCommand('mceRepaint');
					return false;
				}
			};
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
