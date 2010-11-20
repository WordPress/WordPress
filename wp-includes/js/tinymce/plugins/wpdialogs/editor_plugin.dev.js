/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function($) {
	var wpDialogFn = function( fn ) {
		return function() {
			if ( this.features.wpDialog )
				return fn.apply( this, arguments );
			else
				return this.parent.apply( this, arguments );
		};
	};

	tinymce.create('tinymce.plugins.WPDialogs', {
		init : function(ed, url) {
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

	tinymce.create('tinymce.WPWindowManager:tinymce.InlineWindowManager', {
		WPWindowManager : function(ed) {
			this.parent(ed);
		},

		open : function(f, p) {
			var t = this, element;
			// Can't use wpDialogFn here; this.features isn't set yet.
			if ( ! f.wpDialog )
				return this.parent( f, p );
			else if ( ! f.id )
				return;
			
			element = $('#' + f.id);
			if ( ! element.length )
				return;
			
			t.features = f;
			t.params = p;
			t.onOpen.dispatch(t, f, p);
			t.element = t.windows[ f.id ] = element;
			
			// Store selection
			t.bookmark = t.editor.selection.getBookmark();
			
			if ( tinyMCEPopup )
				tinyMCEPopup.init();
			
			element.dialog({
				title: f.title,
				width: f.width,
				height: f.height,
				modal: true,
				dialogClass: 'wp-dialog'
			});
		},
		close : wpDialogFn(function() {
			this.element.dialog('close');
		}),
	});

	// Register plugin
	tinymce.PluginManager.add('wpdialogs', tinymce.plugins.WPDialogs);
})(jQuery);

