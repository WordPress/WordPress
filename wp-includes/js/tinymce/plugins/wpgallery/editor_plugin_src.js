
(function() {
	tinymce.create('tinymce.plugins.wpGallery', {

		init : function(ed, url) {
			var t = this;

			t.url = url;
			t.editor = ed;
			t._createButtons();

			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('...');
			ed.addCommand('WP_Gallery', function() {
				if ( tinymce.isIE )
					ed.selection.moveToBookmark( ed.wpGalleryBookmark );

				var el = ed.selection.getNode(),
					gallery = wp.media.gallery,
					frame;

				// Check if the `wp.media.gallery` API exists.
				if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery )
					return;

				// Make sure we've selected a gallery node.
				if ( el.nodeName != 'IMG' || ed.dom.getAttrib(el, 'class').indexOf('wpGallery') == -1 )
					return;

				frame = gallery.edit( '[' + ed.dom.getAttrib( el, 'title' ) + ']' );

				frame.state('gallery-edit').on( 'update', function( selection ) {
					var shortcode = gallery.shortcode( selection ).string().slice( 1, -1 );
					ed.dom.setAttrib( el, 'title', shortcode );
				});
			});

			ed.onInit.add(function(ed) {
				// iOS6 doesn't show the buttons properly on click, show them on 'touchstart'
				if ( 'ontouchstart' in window ) {
					ed.dom.events.add(ed.getBody(), 'touchstart', function(e){
						var target = e.target;

						if ( target.nodeName == 'IMG' && ed.dom.hasClass(target, 'wpGallery') ) {
							ed.selection.select(target);
							ed.dom.events.cancel(e);
							ed.plugins.wordpress._hideButtons();
							ed.plugins.wordpress._showButtons(target, 'wp_gallerybtns');
						}
					});
				}
			});

			ed.onMouseDown.add(function(ed, e) {
				if ( e.target.nodeName == 'IMG' && ed.dom.hasClass(e.target, 'wpGallery') ) {
					ed.plugins.wordpress._hideButtons();
					ed.plugins.wordpress._showButtons(e.target, 'wp_gallerybtns');
				}
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

		_createButtons : function() {
			var t = this, ed = tinymce.activeEditor, DOM = tinymce.DOM, editButton, dellButton, isRetina;

			if ( DOM.get('wp_gallerybtns') )
				return;

			isRetina = ( window.devicePixelRatio && window.devicePixelRatio > 1 ) || // WebKit, Opera
				( window.matchMedia && window.matchMedia('(min-resolution:130dpi)').matches ); // Firefox, IE10, Opera

			DOM.add(document.body, 'div', {
				id : 'wp_gallerybtns',
				style : 'display:none;'
			});

			editButton = DOM.add('wp_gallerybtns', 'img', {
				src : isRetina ? t.url+'/img/edit-2x.png' : t.url+'/img/edit.png',
				id : 'wp_editgallery',
				width : '24',
				height : '24',
				title : ed.getLang('wordpress.editgallery')
			});

			tinymce.dom.Event.add(editButton, 'mousedown', function(e) {
				var ed = tinymce.activeEditor;
				ed.wpGalleryBookmark = ed.selection.getBookmark('simple');
				ed.execCommand("WP_Gallery");
				ed.plugins.wordpress._hideButtons();
			});

			dellButton = DOM.add('wp_gallerybtns', 'img', {
				src : isRetina ? t.url+'/img/delete-2x.png' : t.url+'/img/delete.png',
				id : 'wp_delgallery',
				width : '24',
				height : '24',
				title : ed.getLang('wordpress.delgallery')
			});

			tinymce.dom.Event.add(dellButton, 'mousedown', function(e) {
				var ed = tinymce.activeEditor, el = ed.selection.getNode();

				if ( el.nodeName == 'IMG' && ed.dom.hasClass(el, 'wpGallery') ) {
					ed.dom.remove(el);

					ed.execCommand('mceRepaint');
					ed.dom.events.cancel(e);
				}

				ed.plugins.wordpress._hideButtons();
			});
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
