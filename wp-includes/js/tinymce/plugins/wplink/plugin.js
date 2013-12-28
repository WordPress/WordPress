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
