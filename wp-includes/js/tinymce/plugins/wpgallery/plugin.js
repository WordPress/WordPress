/* global tinymce */
tinymce.PluginManager.add('wpgallery', function( editor ) {

	function replaceGalleryShortcodes( content ) {
		return content.replace( /\[gallery([^\]]*)\]/g, function( match ) {
			return html( 'wp-gallery', match );
		});
	}

	function html( cls, data ) {
		data = window.encodeURIComponent( data );
		return '<img src="' + tinymce.Env.transparentSrc + '" class="wp-media mceItem ' + cls + '" ' +
			'data-wp-media="' + data + '" data-mce-resize="false" data-mce-placeholder="1" />';
	}

	function replaceCallback( match, type, close ) {
		var index;

		if ( close && close.indexOf( '[' + type ) > -1 ) {
			index = match.length - close.length;
			return html( 'wp-' + type, match.substring( 0, index ) ) + match.substring( index );
		}

		return html( 'wp-' + type, match );
	}

	function replaceAVShortcodes( content ) {
		var testRegex = /\[(audio|video)[^\]]*\]/,
			replaceRegex = /\[(audio|video)[^\]]*\]([\s\S]*?\[\/\1\])?/;

		while ( testRegex.test( content ) ) {
			content = content.replace( replaceRegex, replaceCallback );
		}

		return content;
	}

	function restoreMediaShortcodes( content ) {
		function getAttr( str, name ) {
			name = new RegExp( name + '=\"([^\"]+)\"' ).exec( str );
			return name ? window.decodeURIComponent( name[1] ) : '';
		}

		return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {
			var data = getAttr( image, 'data-wp-media' );

			if ( data ) {
				return '<p>' + data + '</p>';
			}

			return match;
		});
	}

	function editMedia( node ) {
		var gallery, frame, data;

		if ( node.nodeName !== 'IMG' ) {
			return;
		}

		// Check if the `wp.media.gallery` API exists.
		if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery ) {
			return;
		}

		// Make sure we've selected a gallery node.
		if ( editor.dom.hasClass( node, 'wp-gallery' ) ) {
			gallery = wp.media.gallery;
			data = window.decodeURIComponent( editor.dom.getAttrib( node, 'data-wp-media' ) );
			frame = gallery.edit( data );

			frame.state('gallery-edit').on( 'update', function( selection ) {
				var shortcode = gallery.shortcode( selection ).string();
				editor.dom.setAttrib( node, 'data-wp-media', window.encodeURIComponent( shortcode ) );
			});
		} else {
			// temp
			window.console && window.console.log( 'Edit AV shortcode ' + window.decodeURIComponent( editor.dom.getAttrib( node, 'data-wp-media' ) ) );
		}
	}

	// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('...');
	editor.addCommand( 'WP_Gallery', function() {
		editMedia( editor.selection.getNode() );
	});
/*
	editor.on( 'init', function( e ) {
	//	_createButtons()

		// iOS6 doesn't show the buttons properly on click, show them on 'touchstart'
		if ( 'ontouchstart' in window ) {
			editor.dom.events.bind( editor.getBody(), 'touchstart', function( e ) {
				var target = e.target;

				if ( target.nodeName == 'IMG' && editor.dom.hasClass( target, 'wp-gallery' ) ) {
					editor.selection.select( target );
					editor.dom.events.cancel( e );
					editor.plugins.wordpress._hideButtons();
					editor.plugins.wordpress._showButtons( target, 'wp_gallerybtns' );
				}
			});
		}
	});
*/
	editor.on( 'mouseup', function( event ) {
		var dom = editor.dom,
			node = event.target;

		function unselect() {
			dom.removeClass( dom.select( 'img.wp-media-selected' ), 'wp-media-selected' );
		}

		if ( node.nodeName === 'IMG' && dom.getAttrib( node, 'data-wp-media' ) ) {
			// Don't trigger on right-click
			if ( event.button !== 2 ) {
				if ( dom.hasClass( node, 'wp-media-selected' ) ) {
					editMedia( node );
				} else {
					unselect();
					dom.addClass( node, 'wp-media-selected' );
				}
			}
		} else {
			unselect();
		}
	});

	// Display gallery, audio or video instead of img in the element path
	editor.on( 'ResolveName', function( event ) {
		var dom = editor.dom,
			node = event.target;

		if ( node.nodeName === 'IMG' && dom.getAttrib( node, 'data-wp-media' ) ) {
			if ( dom.hasClass( node, 'wp-gallery' ) ) {
				event.name = 'gallery';
			} else if ( dom.hasClass( node, 'wp-video' ) ) {
				event.name = 'video';
			} else if ( dom.hasClass( node, 'wp-audio' ) ) {
				event.name = 'audio';
			}
		}
	});

	editor.on( 'BeforeSetContent', function( event ) {
		event.content = replaceGalleryShortcodes( event.content );
		event.content = replaceAVShortcodes( event.content );
	});

	editor.on( 'PostProcess', function( event ) {
		if ( event.get ) {
			event.content = restoreMediaShortcodes( event.content );
		}
	});
});
