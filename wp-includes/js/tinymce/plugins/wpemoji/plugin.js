( function( tinymce, wp, twemoji ) {
	tinymce.PluginManager.add( 'wpemoji', function( editor ) {
		var typing, match,
			env = tinymce.Env,
			ua = window.navigator.userAgent,
			isWin = ua.indexOf( 'Windows' ) > -1,
			isWin8 = ( function() {
				var match = ua.match( /Windows NT 6\.(\d)/ );

				if ( match && match[1] > 1 ) {
					return true;
				}

				return false;
			}());

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

		if ( isWin8 ) {
			// Windows 8+ emoji can be "typed" with the onscreen keyboard.
			// That triggers the normal keyboard events, but not the 'input' event.
			// Thankfully it sets keyCode 231 when the onscreen keyboard inserts any emoji.
			editor.on( 'keyup', function( event ) {
				var node;

				if ( event.keyCode === 231 ) {
					node = editor.selection.getNode();

					if ( twemoji.test( node.textContent || node.innerText ) ) {
						replaceEmoji( node );
					}
				}
			} );
		} else if ( ! isWin ) {
			// In MacOS inserting emoji doesn't trigger the stanradr keyboard events.
			// Thankfully it triggers the 'input' event.
			// This works in Android and iOS as well.
			editor.on( 'keydown keyup', function( event ) {
				typing = ( event.type === 'keydown' );
			} );

			editor.on( 'input', function( event ) {
				if ( typing ) {
					return;
				}

				var bookmark,
					selection = editor.selection,
					node = selection.getNode();

				if ( twemoji.test( node.textContent || node.innerText ) ) {
					if ( env.webkit ) {
						bookmark = selection.getBookmark();
					}

					replaceEmoji( node );

					if ( env.webkit ) {
						selection.moveToBookmark( bookmark );
					}
				}
			});
		}

		editor.on( 'setcontent', function( event ) {
			var selection = editor.selection,
				node = selection.getNode();

			if ( twemoji.test( node.textContent || node.innerText ) ) {
				replaceEmoji( node );

				// In IE all content in the editor is left selected after wp.emoji.parse()...
				// Collapse the selection to the beginning.
				if ( env.ie && env.ie < 9 && event.load && node && node.nodeName === 'BODY' ) {
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
				event.content = event.content.replace( /<img[^>]+data-wp-emoji="([^"]+)"[^>]*>/g, '$1' );
			}
		} );

		editor.on( 'resolvename', function( event ) {
			if ( event.target.nodeName === 'IMG' && editor.dom.getAttrib( event.target, 'data-wp-emoji' ) ) {
				event.preventDefault();
			}
		} );
	} );
} )( window.tinymce, window.wp, window.twemoji );
