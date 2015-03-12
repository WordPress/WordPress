( function( tinymce, wp ) {
	tinymce.PluginManager.add( 'wpemoji', function( editor, url ) {
		var typing;

		if ( ! wp.emoji.parseEmoji ) {
			return;
		}

		// Loads stylesheet for custom styles within the editor
		editor.on( 'init', function() {
			var cssId = editor.dom.uniqueId();
			var linkElm = editor.dom.create( 'link', {
				id:   cssId,
				rel:  'stylesheet',
				href: url + '/css/editor.css'
			});
			editor.getDoc().getElementsByTagName( 'head' )[0].appendChild( linkElm );
		} );

		editor.on( 'keydown keyup', function( event ) {
			typing = event.type === 'keydown';
		} );

		editor.on( 'input setcontent', function( event ) {
			var selection, node, bookmark, imgs;

			if ( typing && event.type === 'input' ) {
				return;
			}

			selection = editor.selection;
			node = selection.getNode();
			bookmark = selection.getBookmark();

			wp.emoji.parse( node );

			imgs = editor.dom.select( 'img.emoji', node );

			tinymce.each( imgs, function( elem ) {
				if ( ! elem.getAttribute( 'data-wp-emoji' ) ) {
					elem.setAttribute( 'data-mce-resize', 'false' );
					elem.setAttribute( 'data-mce-placeholder', '1' );
					elem.setAttribute( 'data-wp-emoji', elem.alt );
				}
			} );

			selection.moveToBookmark( bookmark );
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
