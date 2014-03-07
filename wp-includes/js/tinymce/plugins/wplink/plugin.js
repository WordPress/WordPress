/* global tinymce */
tinymce.PluginManager.add( 'wplink', function( editor ) {
	// Register a command so that it can be invoked by using tinyMCE.activeEditor.execCommand( 'WP_Link' );
	editor.addCommand( 'WP_Link', function() {
		if ( typeof window.wpLink !== 'undefined' ) {
			window.wpLink.open( editor.id );
		}
	});

	editor.addButton( 'link', {
		icon: 'link',
		tooltip: 'Insert/edit link',
		shortcut: 'Alt+Shift+A',
		cmd: 'WP_Link',

		onPostRender: function() {
			var ctrl = this;

			editor.on( 'nodechange', function( event ) {
				var node = event.element;

				ctrl.disabled( editor.selection.isCollapsed() && node.nodeName !== 'A' );
				ctrl.active( node.nodeName === 'A' && ! node.name );
			});
		}
	});

	editor.addButton( 'unlink', {
		icon: 'unlink',
		tooltip: 'Remove link',
		cmd: 'unlink',
		stateSelector: 'a[href]'
	});

	editor.addMenuItem( 'link', {
		icon: 'link',
		text: 'Insert link',
		shortcut: 'Alt+Shift+A',
		cmd: 'WP_Link',
		stateSelector: 'a[href]',
		context: 'insert',
		prependToContext: true
	});
});
