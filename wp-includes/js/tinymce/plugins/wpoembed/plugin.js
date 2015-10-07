(function ( tinymce ) {
	'use strict';

	tinymce.PluginManager.add( 'wpoembed', function ( editor, url ) {
		editor.on( 'init', function () {
			var scriptId = editor.dom.uniqueId();

			var scriptElm = editor.dom.create( 'script', {
				id: scriptId,
				type: 'text/javascript',
				src: url + '/../../../wp-oembed.js'
			} );

			editor.getDoc().getElementsByTagName( 'head' )[ 0 ].appendChild( scriptElm );
		} );
	} );
})( window.tinymce );
