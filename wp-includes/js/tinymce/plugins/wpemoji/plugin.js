( function( tinymce, wp ) {
	tinymce.PluginManager.add( 'wpemoji', function( editor ) {
		var typing,
			isMacWebKit = tinymce.Env.mac && tinymce.Env.webkit;

		if ( ! wp || ! wp.emoji ) {
			return;
		}

		editor.on( 'keydown keyup', function( event ) {
			typing = event.type === 'keydown';
		} );

		editor.on( 'input setcontent', function( event ) {
			var selection, node, bookmark, images;

			if ( typing && event.type === 'input' ) {
				return;
			}

			selection = editor.selection;
			node = selection.getNode();

			if ( isMacWebKit ) {
				bookmark = selection.getBookmark();
			}

			wp.emoji.parse( node, { className: 'wp-emoji new-emoji' } );

			images = editor.dom.select( 'img.new-emoji', node );

			tinymce.each( images, function( image ) {
				image.className = 'wp-emoji';
				image.setAttribute( 'data-mce-resize', 'false' );
				image.setAttribute( 'data-mce-placeholder', '1' );
				image.setAttribute( 'data-wp-emoji', image.alt );
			} );

			// In IE all content in the editor is left selected aftrer wp.emoji.parse()...
			// Collapse the selection to the beginning.
			if ( tinymce.Env.ie && node && node.nodeName === 'BODY' ) {
				selection.collapse( true );
			}

			if ( isMacWebKit ) {
				selection.moveToBookmark( bookmark );
			}
		} );

		editor.on( 'postprocess', function( event ) {
			if ( event.content ) {
				event.content = event.content.replace( /<img[^>]+data-wp-emoji="([^"]+)"[^>]*>/g, function( match, emoji ) {
					return emoji;
				} );
			}
		} );

		editor.on( 'resolvename', function( event ) {
			if ( event.target.nodeName === 'IMG' && editor.dom.getAttrib( event.target, 'data-wp-emoji' ) ) {
				event.preventDefault();
			}
		} );
	} );
} )( window.tinymce, window.wp );
