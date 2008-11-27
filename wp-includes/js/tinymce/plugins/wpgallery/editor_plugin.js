
(function() {
	tinymce.create('tinymce.plugins.wpGallery', {

		init : function(ed, url) {
			var t = this;

			t.url = url;
			t._createButtons();

			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('...');
			ed.addCommand('WP_Gallery', function() {
				var el = ed.selection.getNode(), vp = tinymce.DOM.getViewPort(), W = ( 720 < vp.w ) ? 720 : vp.w;

				if ( el.nodeName != 'IMG' ) return;
				if ( ed.dom.getAttrib(el, 'class').indexOf('wpGallery') == -1 )	return;

				var post_id = tinymce.DOM.get('post_ID').value;
				tb_show('', tinymce.documentBaseURL + '/media-upload.php?post_id='+post_id+'&tab=gallery&TB_iframe=true');

				tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
			});

			ed.onInit.add(function(ed) {
				tinymce.dom.Event.add(ed.getWin(), 'scroll', function(e) {
					ed.plugins.wpgallery.hideButtons();
				});
			});

			ed.onBeforeExecCommand.add(function(ed, cmd, ui, val) {
				ed.plugins.wpgallery.hideButtons();
			});

			ed.onSaveContent.add(function(ed, o) {
				ed.plugins.wpgallery.hideButtons();
			});

			ed.onMouseUp.add(function(ed, e) {
				if ( tinymce.isOpera ) {
					if ( e.target.nodeName == 'IMG' )
						ed.plugins.wpgallery.showButtons(e.target);
				}

			});

			ed.onMouseDown.add(function(ed, e) {
				if ( tinymce.isOpera || e.target.nodeName != 'IMG' ) {
					t.hideButtons();
					return;
				}
				ed.plugins.wpgallery.showButtons(e.target);
			});

			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._do_gallery(o.content);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = t._get_gallery(o.content);
			});
		},

		_do_gallery : function(co) {
			return co.replace(/\[gallery([^\]]*)\]/g, function(a,b){
				return '<img src="'+tinymce.baseURL+'/plugins/wpgallery/img/t.gif" class="wpGallery mceItem" title="gallery'+tinymce.DOM.encode(b)+'" />';
			});
		},

		_get_gallery : function(co) {

			function getAttr(s, n) {
				n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
				return n ? tinymce.DOM.decode(n[1]) : '';
			};

			return co.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function(a,im) {
				var cls = getAttr(im, 'class');

				if ( cls.indexOf('wpGallery') != -1 )
					return '<p>['+tinymce.trim(getAttr(im, 'title'))+']</p>';

				return a;
			});
		},

		showButtons : function(n) {
			var t = this, ed = tinyMCE.activeEditor, p1, p2, vp, DOM = tinymce.DOM, X, Y;

			if (ed.dom.getAttrib(n, 'class').indexOf('wpGallery') == -1)
				return;

			vp = ed.dom.getViewPort(ed.getWin());
			p1 = DOM.getPos(ed.getContentAreaContainer());
			p2 = ed.dom.getPos(n);

			X = Math.max(p2.x - vp.x, 0) + p1.x;
			Y = Math.max(p2.y - vp.y, 0) + p1.y;

			DOM.setStyles('wp_gallerybtns', {
				'top' : Y+5+'px',
				'left' : X+5+'px',
				'display' : 'block'
			});

			t.btnsTout = window.setTimeout( function(){ed.plugins.wpgallery.hideButtons();}, 5000 );
		},

		hideButtons : function() {
			if ( tinymce.DOM.isHidden('wp_gallerybtns') ) return;

			tinymce.DOM.hide('wp_gallerybtns');
			window.clearTimeout(this.btnsTout);
		},

		_createButtons : function() {
			var t = this, ed = tinyMCE.activeEditor, DOM = tinymce.DOM;

			DOM.remove('wp_gallerybtns');

			var wp_gallerybtns = DOM.add(document.body, 'div', {
				id : 'wp_gallerybtns',
				style : 'display:none;'
			});

			var wp_editgallery = DOM.add('wp_gallerybtns', 'img', {
				src : t.url+'/img/edit.png',
				id : 'wp_editgallery',
				width : '24',
				height : '24',
				title : ed.getLang('wordpress.editgallery')
			});

			wp_editgallery.onmousedown = function(e) {
				var ed = tinyMCE.activeEditor;
				ed.windowManager.bookmark = ed.selection.getBookmark('simple');
				ed.execCommand("WP_Gallery");
				this.parentNode.style.display = 'none';
			};

			var wp_delgallery = DOM.add('wp_gallerybtns', 'img', {
				src : t.url+'/img/delete.png',
				id : 'wp_delgallery',
				width : '24',
				height : '24',
				title : ed.getLang('wordpress.delgallery')
			});

			wp_delgallery.onmousedown = function(e) {
				var ed = tinyMCE.activeEditor, el = ed.selection.getNode();

				if ( el.nodeName == 'IMG' && ed.dom.getAttrib(el, 'class').indexOf('wpGallery') != -1 ) {
					ed.dom.remove(el);

					this.parentNode.style.display = 'none';
					ed.execCommand('mceRepaint');
					return false;
				}
			};
		},

		getInfo : function() {
			return {
				longname : 'Gallery Settings',
				author : 'WordPress',
				authorurl : 'http://wordpress.org',
				infourl : '',
				version : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('wpgallery', tinymce.plugins.wpGallery);
})();
