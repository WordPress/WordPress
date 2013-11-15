/* global tinymce:false, switchEditors, fullscreen */
/**
 * WP Fullscreen TinyMCE plugin
 *
 * Contains code from Moxiecode Systems AB released under LGPL http://tinymce.moxiecode.com/license
 */

(function() {
	tinymce.create('tinymce.plugins.wpFullscreenPlugin', {
		resize_timeout: false,

		init : function( ed ) {
			var t = this, s = {}, DOM = tinymce.DOM;

			// Register commands
			ed.addCommand('wpFullScreenClose', function() {
				// this removes the editor, content has to be saved first with tinymce.execCommand('wpFullScreenSave');
				if ( ed.getParam('wp_fullscreen_is_enabled') ) {
					DOM.win.setTimeout(function() {
						tinymce.remove(ed);
						DOM.remove('wp_mce_fullscreen_parent');
						tinymce.settings = tinymce.oldSettings; // Restore old settings
					}, 10);
				}
			});

			ed.addCommand('wpFullScreenSave', function() {
				var ed = tinymce.get('wp_mce_fullscreen'), edd;

				ed.focus();
				edd = tinymce.get( ed.getParam('wp_fullscreen_editor_id') );

				edd.setContent( ed.getContent({format : 'raw'}), {format : 'raw'} );
			});

			ed.addCommand('wpFullScreenInit', function() {
				var d, b, fsed;

				ed = tinymce.activeEditor;
				d = ed.getDoc();
				b = d.body;

				tinymce.oldSettings = tinymce.settings; // Store old settings

				tinymce.each(ed.settings, function(v, n) {
					s[n] = v;
				});

				s.id = 'wp_mce_fullscreen';
				s.wp_fullscreen_is_enabled = true;
				s.wp_fullscreen_editor_id = ed.id;
				s.theme_advanced_resizing = false;
				s.theme_advanced_statusbar_location = 'none';
				s.content_css = s.content_css ? s.content_css + ',' + s.wp_fullscreen_content_css : s.wp_fullscreen_content_css;
				s.height = tinymce.isIE ? b.scrollHeight : b.offsetHeight;

				tinymce.each(ed.getParam('wp_fullscreen_settings'), function(v, k) {
					s[k] = v;
				});

				fsed = new tinymce.Editor('wp_mce_fullscreen', s);
				fsed.onInit.add(function(edd) {
					var DOM = tinymce.DOM, buttons = DOM.select('a.mceButton', DOM.get('wp-fullscreen-buttons'));

					if ( !ed.isHidden() )
						edd.setContent( ed.getContent() );
					else
						edd.setContent( switchEditors.wpautop( edd.getElement().value ) );

					setTimeout(function(){ // add last
						edd.onNodeChange.add( function() {
							tinymce.each(buttons, function(c) {
								var btn, cls;

								if ( btn = DOM.get( 'wp_mce_fullscreen_' + c.id.substr(6) ) ) {
									cls = btn.className;

									if ( cls )
										c.className = cls;
								}
							});
						});
					}, 1000);

					edd.dom.addClass(edd.getBody(), 'wp-fullscreen-editor');
					edd.focus();
				});

				fsed.render();

				if ( 'undefined' != fullscreen ) {
					fsed.dom.bind( fsed.dom.doc, 'mousemove', function(e){
						fullscreen.bounder( 'showToolbar', 'hideToolbar', 2000, e );
					});
				}
			});

			ed.addCommand('wpFullScreen', function() {
				if ( typeof(fullscreen) == 'undefined' )
					return;

				if ( 'wp_mce_fullscreen' == ed.id )
					fullscreen.off();
				else
					fullscreen.on();
			});

			// Register buttons
			ed.addButton('wp_fullscreen', {
				title : 'wordpress.wp_fullscreen_desc',
				cmd : 'wpFullScreen'
			});

			// END fullscreen
//----------------------------------------------------------------
			// START autoresize

			if ( ed.getParam('fullscreen_is_enabled') || !ed.getParam('wp_fullscreen_is_enabled') )
				return;

			/**
			 * This method gets executed each time the editor needs to resize.
			 */
			function resize(editor, e) {
				var DOM = tinymce.DOM, body = ed.getBody(), ifr = DOM.get(ed.id + '_ifr'), height, y = ed.dom.win.scrollY;

				if ( t.resize_timeout )
					return;

				// sometimes several events are fired few ms apart, trottle down resizing a little
				t.resize_timeout = true;
				setTimeout(function(){
					t.resize_timeout = false;
				}, 500);

				height = body.scrollHeight > 300 ? body.scrollHeight : 300;

				if ( height != ifr.scrollHeight ) {
					DOM.setStyle(ifr, 'height', height + 'px');
					ed.getWin().scrollTo(0, 0); // iframe window object, make sure there's no scrolling
				}

				// WebKit scrolls to top on paste...
				if ( e && e.type == 'paste' && tinymce.isWebKit ) {
					setTimeout(function(){
						ed.dom.win.scrollTo(0, y);
					}, 40);
				}
			}

			// Add appropriate listeners for resizing content area
			ed.onInit.add( function( ed ) {
				ed.onChange.add(resize);
				ed.onSetContent.add(resize);
				ed.onPaste.add(resize);
				ed.onKeyUp.add(resize);
				ed.onPostRender.add(resize);

				ed.getBody().style.overflowY = 'hidden';
			});

			if ( ed.getParam('autoresize_on_init', true) ) {
				ed.onLoadContent.add( function() {
					// Because the content area resizes when its content CSS loads,
					// and we can't easily add a listener to its onload event,
					// we'll just trigger a resize after a short loading period
					setTimeout(function() {
						resize();
					}, 1200);
				});
			}

			// Register the command so that it can be invoked by using tinymce.activeEditor.execCommand('mceExample');
			ed.addCommand('wpAutoResize', resize);
		},

		getInfo : function() {
			return {
				longname : 'WP Fullscreen',
				author : 'WordPress',
				authorurl : 'http://wordpress.org',
				infourl : '',
				version : '1.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wpfullscreen', tinymce.plugins.wpFullscreenPlugin);
})();
