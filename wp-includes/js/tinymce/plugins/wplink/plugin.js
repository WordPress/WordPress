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

	function setState( button, node ) {
		var parent = editor.dom.getParent( node, 'a' );

		button.disabled( ( editor.selection.isCollapsed() && ! parent ) || ( parent && ! parent.href ) );
		button.active( parent && parent.href );
	}

	editor.addButton( 'link', {
		icon: 'link',
		tooltip: 'Insert/edit link',
		shortcut: 'Alt+Shift+A',
		cmd: 'WP_Link',

		onPostRender: function() {
			linkButton = this;

			editor.on( 'nodechange', function( event ) {
				setState( linkButton, event.element );
			});
		}
	});

	editor.addButton( 'unlink', {
		icon: 'unlink',
		tooltip: 'Remove link',
		cmd: 'unlink',

		onPostRender: function() {
			var unlinkButton = this;

			editor.on( 'nodechange', function( event ) {
				setState( unlinkButton, event.element );
			});
		}
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
