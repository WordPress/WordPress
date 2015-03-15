( function( tinymce, wp, twemoji ) {
	tinymce.PluginManager.add( 'wpemoji', function( editor ) {
		var typing,
			isMacWebKit = tinymce.Env.mac && tinymce.Env.webkit;

		if ( ! wp || ! wp.emoji || ! wp.emoji.replaceEmoji ) {
			return;
		}

		function setImgAttr( image ) {
			image.className = 'emoji';
			image.setAttribute( 'data-mce-resize', 'false' );
			image.setAttribute( 'data-mce-placeholder', '1' );
			image.setAttribute( 'data-wp-emoji', image.alt );
		}

		function replaceEmoji( node ) {
			wp.emoji.parse( node, { className: 'emoji _inserted-emoji' } );
			tinymce.each( editor.dom.$( 'img._inserted-emoji', node ), setImgAttr );
		}

		editor.on( 'keydown keyup', function( event ) {
			typing = event.type === 'keydown';
		} );

		editor.on( 'input', function() {
			if ( typing ) {
				return;
			}

			var bookmark,
				selection = editor.selection,
				node = selection.getNode();

			if ( twemoji.test( node.textContent || node.innerText ) ) {
				if ( isMacWebKit ) {
					bookmark = selection.getBookmark();
				}

				replaceEmoji( node );

				if ( isMacWebKit ) {
					selection.moveToBookmark( bookmark );
				}
			}
		});

		editor.on( 'setcontent', function( event ) {
			var selection = editor.selection,
				node = selection.getNode();

			if ( twemoji.test( node.textContent || node.innerText ) ) {
				replaceEmoji( node );

				// In IE all content in the editor is left selected after wp.emoji.parse()...
				// Collapse the selection to the beginning.
				if ( tinymce.Env.ie && tinymce.Env.ie < 9 && event.load && node && node.nodeName === 'BODY' ) {
					selection.collapse( true );
				}
			}
		} );

		// Convert Twemoji compatible pasted emoji replacement images into our format.
		editor.on( 'PastePostProcess', function( event ) {
			if ( twemoji ) {
				tinymce.each( editor.dom.$( 'img.emoji', event.node ), function( image ) {
					if ( image.alt && twemoji.test( image.alt ) ) {
						setImgAttr( image );
					}
				});
			}
		});

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
} )( window.tinymce, window.wp, window.twemoji );
