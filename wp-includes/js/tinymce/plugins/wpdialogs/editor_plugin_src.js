/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	tinymce.create('tinymce.plugins.WPDialogs', {
		init : function(ed, url) {
			tinymce.create('tinymce.WPWindowManager:tinymce.InlineWindowManager', {
				WPWindowManager : function(ed) {
					this.parent(ed);
				},

				open : function(f, p) {
					var t = this, element;

					if ( ! f.wpDialog )
						return this.parent( f, p );
					else if ( ! f.id )
						return;

					element = jQuery('#' + f.id);
					if ( ! element.length )
						return;

					t.features = f;
					t.params = p;
					t.onOpen.dispatch(t, f, p);
					t.element = t.windows[ f.id ] = element;

					// Store selection
					t.bookmark = t.editor.selection.getBookmark(1);

					// Create the dialog if necessary
					if ( ! element.data('wpdialog') ) {
						element.wpdialog({
							title: f.title,
							width: f.width,
							height: f.height,
							modal: true,
							dialogClass: 'wp-dialog',
							zIndex: 300000
						});
					}

					element.wpdialog('open');
				},
				close : function() {
					if ( ! this.features.wpDialog )
						return this.parent.apply( this, arguments );

					this.element.wpdialog('close');
				}
			});

			// Replace window manager
			ed.onBeforeRenderUI.add(function() {
				ed.windowManager = new tinymce.WPWindowManager(ed);
			});
		},

		getInfo : function() {
			return {
				longname : 'WPDialogs',
				author : 'WordPress',
				authorurl : 'http://wordpress.org',
				infourl : 'http://wordpress.org',
				version : '0.1'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wpdialogs', tinymce.plugins.WPDialogs);
})();
