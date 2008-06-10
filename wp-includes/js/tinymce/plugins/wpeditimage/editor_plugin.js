
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

				tb_show('', url + '/editimage.html?TB_iframe=true');
				tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
			});

			ed.onInit.add(function(ed) {
				tinymce.dom.Event.add(ed.getWin(), 'scroll', function(e) {
	  				ed.plugins.wpeditimage.hideButtons();
				});
			});

			ed.onExecCommand.add(function(ed, cmd, ui, val) {
          		if ( 'mceFullScreen' == cmd )
					ed.plugins.wpeditimage.hideButtons();
      		});

			ed.onSaveContent.add(function(ed, o) {
   				ed.plugins.wpeditimage.hideButtons();
			});

			ed.onMouseUp.add(function(ed, e) {
				if ( tinymce.isOpera )
					ed.plugins.wpeditimage.showButtons(e);
			});

			ed.onMouseDown.add(function(ed, e) {
				if ( tinymce.isOpera ) return;
				ed.plugins.wpeditimage.showButtons(e);
			});
		},

		showButtons : function(e) {
			var t = this, ed = tinyMCE.activeEditor, p1, p2, vp, DOM = tinymce.DOM, X, Y, el = e.target;

			t.hideButtons();
			if (el.nodeName == 'IMG') {
				if (ed.dom.getAttrib(el, 'class').indexOf('mceItem') != -1)
					return;

				vp = ed.dom.getViewPort(ed.getWin());
				p1 = DOM.getPos(ed.getContentAreaContainer());
				p2 = ed.dom.getPos(el);

				X = Math.max(p2.x - vp.x, 0) + p1.x;
				Y = Math.max(p2.y - vp.y, 0) + p1.y;

				DOM.setStyles('wp_editbtns', {
					'top' : Y+5+'px',
					'left' : X+5+'px',
					'display' : 'block'
				});

				t.btnsTout = window.setTimeout( function(){ed.plugins.wpeditimage.hideButtons();}, 5000 );
			}
		},
		
		hideButtons : function() {
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
				title : 'Edit'
			});

			wp_editimgbtn.onmousedown = function(e) {
				var ed = tinyMCE.activeEditor;
				ed.windowManager.bookmark = ed.selection.getBookmark('simple');
				ed.execCommand("WP_EditImage");
				this.parentNode.style.display = 'none';
			}

			var wp_delimgbtn = DOM.add('wp_editbtns', 'img', {
				src : t.url+'/img/delete.png',
				id : 'wp_delimgbtn',
				width : '24',
				height : '24',
				title : 'Delete'
			});

			wp_delimgbtn.onmousedown = function(e) {
				var ed = tinyMCE.activeEditor, el = ed.selection.getNode(), p;

				if ( el.nodeName != 'IMG' || ed.dom.getAttrib(el, 'class').indexOf('mceItem') != -1 ) return;

				if ( (p = ed.dom.getParent(el, 'A')) && p.childNodes.length == 1)
					ed.dom.remove(p);
				else ed.dom.remove(el);

				this.parentNode.style.display = 'none';
				ed.execCommand('mceRepaint');
				return false;
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
