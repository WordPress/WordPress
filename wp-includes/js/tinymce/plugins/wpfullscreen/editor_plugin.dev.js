/**
 * WP Fullscreen TinyMCE plugin
 *
 * Contains code from Moxiecode Systems AB released under LGPL License http://tinymce.moxiecode.com/license
 */

(function() {
	tinymce.create('tinymce.plugins.wpFullscreenPlugin', {

		init : function(ed, url) {
			var t = this, oldHeight = 0, s = {}, DOM = tinymce.DOM;

			// Register commands
			ed.addCommand('wpFullScreenClose', function() {
				// this removes the editor, content has to be saved first with tinyMCE.execCommand('wpFullScreenSave');
				if ( ed.getParam('wp_fullscreen_is_enabled') ) {
					DOM.win.setTimeout(function() {
						tinyMCE.remove(ed);
						DOM.remove('wp_mce_fullscreen_parent');
						tinyMCE.settings = tinyMCE.oldSettings; // Restore old settings
					}, 10);
				}
			});
			
			ed.addCommand('wpFullScreenSave', function() {
				var ed = tinyMCE.get('wp_mce_fullscreen'), edd;

				ed.focus();
				edd = tinyMCE.get( ed.getParam('wp_fullscreen_editor_id') );

				edd.setContent( ed.getContent({format : 'raw'}), {format : 'raw'} );
			});
			
			ed.addCommand('wpFullScreenSaveContent', function() {
				ed.execCommand('wpFullScreenSave');
				tinyMCE.triggerSave();
			});
			
			ed.addCommand('wpFullScreenOpen', function() {
				var d = ed.getDoc(), b = d.body;

				tinyMCE.oldSettings = tinyMCE.settings; // Store old settings

				tinymce.each(ed.settings, function(v, n) {
					s[n] = v;
				});

				s.id = 'wp_mce_fullscreen';
				s.wp_fullscreen_is_enabled = true;
				s.wp_fullscreen_editor_id = ed.id;
				s.theme_advanced_resizing = false;
				s.theme_advanced_toolbar_location = 'external';
				s.theme_advanced_statusbar_location = 'none';
				s.content_css = s.wp_fullscreen_content_css || '';
				s.height = tinymce.isIE ? b.scrollHeight : b.offsetHeight;
				s.save_onsavecallback = function() {
					ed.setContent(tinyMCE.get(s.id).getContent({format : 'raw'}), {format : 'raw'});
					ed.execCommand('mceSave');
				};

				tinymce.each(ed.getParam('wp_fullscreen_settings'), function(v, k) {
					s[k] = v;
				});

				t.fullscreenEditor = new tinymce.Editor('wp_mce_fullscreen', s);
				t.fullscreenEditor.onInit.add(function() {
					t.fullscreenEditor.setContent(ed.getContent());
					t.fullscreenEditor.focus();
				});

				fullscreen.on();
				t.fullscreenEditor.render();
			});

			// Register buttons
			ed.addButton('fullscreen', {title : 'fullscreen.desc', cmd : 'wpFullScreenOpen'});

			// END fullscreen
//----------------------------------------------------------------
			// START autoresize

			if ( ed.getParam('fullscreen_is_enabled') || !ed.getParam('wp_fullscreen_is_enabled') )
				return;

			/**
			 * This method gets executed each time the editor needs to resize.
			 */
			function resize() {
				var d = ed.getDoc(), b = d.body, de = d.documentElement, DOM = tinymce.DOM, resizeHeight, myHeight;

				// Get height differently depending on the browser used
				if ( tinymce.isIE )
					myHeight = b.scrollHeight;
				else if ( tinymce.isWebKit )
					myHeight = b.offsetHeight;
				else
					myHeight = de.offsetHeight;

				// Don't make it smaller than the minimum height
				resizeHeight = (myHeight > 300) ? myHeight : 300;

				// Resize content element
				if ( oldHeight != resizeHeight ) {
					oldHeight = resizeHeight;
					DOM.setStyle(DOM.get(ed.id + '_ifr'), 'height', resizeHeight + 'px');
				}
			};

			// Add appropriate listeners for resizing content area
			ed.onInit.add(function(ed, l) {
				ed.onChange.add(resize);
				ed.onSetContent.add(resize);
				ed.onPaste.add(resize);
				ed.onKeyUp.add(resize);
				ed.onPostRender.add(resize);

				ed.getBody().style.overflowY = "hidden";
				ed.dom.setStyle( ed.getBody(), 'paddingBottom', ed.getParam('autoresize_bottom_margin', 50) + 'px' );
			});

			if (ed.getParam('autoresize_on_init', true)) {
				ed.onLoadContent.add(function(ed, l) {
				//	resize(); // runs before onInit, useless?

					// Because the content area resizes when its content CSS loads,
					// and we can't easily add a listener to its onload event,
					// we'll just trigger a resize after a short loading period
					setTimeout(function() {
						resize();
					}, 1200);
				});
			}

			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceAutoResize', resize);
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
