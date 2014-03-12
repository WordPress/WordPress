/* global tinymce */
tinymce.PluginManager.add( 'wplink', function( editor ) {
	var linkButton;
	
	// Register a command so that it can be invoked by using tinyMCE.activeEditor.execCommand( 'WP_Link' );
	editor.addCommand( 'WP_Link', function() {
		if ( ( ! linkButton || ! linkButton.disabled() ) && typeof window.wpLink !== 'undefined' ) {
			window.wpLink.open( editor.id );
		}
	});

	// WP default shortcut
	editor.addShortcut( 'alt+shift+a', '', 'WP_Link' );
	// The "de-facto standard" shortcut, see #27305
	editor.addShortcut( 'ctrl+k', '', 'WP_Link' );

	editor.addButton( 'link', {
		icon: 'link',
		tooltip: 'Insert/edit link',
		shortcut: 'Alt+Shift+A',
		cmd: 'WP_Link',

		onPostRender: function() {
			linkButton = this;

			editor.on( 'nodechange', function( event ) {
				var node = event.element;

				linkButton.disabled( editor.selection.isCollapsed() && node.nodeName !== 'A' );
				linkButton.active( node.nodeName === 'A' && ! node.name );
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
