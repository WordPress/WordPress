/* global tinymce */
tinymce.PluginManager.add('wpgallery', function( editor ) {

	function parseGallery( content ) {
		return content.replace( /\[gallery([^\]]*)\]/g, function( match, attr ) {
			var data = tinymce.DOM.encode( attr );

			return '<img src="' + tinymce.Env.transparentSrc + '" class="wp-gallery mceItem" ' +
				'title="gallery' + data + '" data-mce-resize="false" data-mce-placeholder="1" />';
		});
	}

	function getGallery( content ) {
		function getAttr( str, name ) {
			name = new RegExp( name + '=\"([^\"]+)\"', 'g' ).exec( str );
			return name ? tinymce.DOM.decode( name[1] ) : '';
		}

		return content.replace( /(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function( match, image ) {
			var cls = getAttr( image, 'class' );

			if ( cls.indexOf('wp-gallery') !== -1 ) {
				return '<p>['+ tinymce.trim( getAttr( image, 'title' ) ) +']</p>';
			}

			return match;
		});
	}

	// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('...');
	editor.addCommand( 'WP_Gallery', function() {
		var gallery, frame, node;

		// Check if the `wp.media.gallery` API exists.
		if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery ) {
			return;
		}

		node = editor.selection.getNode();
		gallery = wp.media.gallery;

		// Make sure we've selected a gallery node.
		if ( node.nodeName === 'IMG' && editor.dom.hasClass( node, 'wp-gallery' ) ) {
			frame = gallery.edit( '[' + editor.dom.getAttrib( node, 'title' ) + ']' );

			frame.state('gallery-edit').on( 'update', function( selection ) {
				var shortcode = gallery.shortcode( selection ).string().slice( 1, -1 );
				editor.dom.setAttrib( node, 'title', shortcode );
			});
		}
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
	editor.on( 'mouseup', function( e ) {
		if ( e.target.nodeName === 'IMG' && editor.dom.hasClass( e.target, 'wp-gallery' ) ) {
			// Don't trigger on right-click
			if ( e.button !== 2 ) {
				if ( editor.dom.hasClass( e.target, 'wp-gallery-selected' ) ) {
					editor.execCommand('WP_Gallery');
					editor.dom.removeClass( e.target, 'wp-gallery-selected' );
				} else {
					editor.dom.addClass( e.target, 'wp-gallery-selected' );
				}
			}
		} else {
			editor.dom.removeClass( editor.dom.select( 'img.wp-gallery-selected' ), 'wp-gallery-selected' );
		}
	});

	// Display 'gallery' instead of img in element path
	editor.on( 'ResolveName', function( e ) {
		var dom = editor.dom,
			target = e.target;

		if ( target.nodeName === 'IMG' && dom.hasClass( target, 'wp-gallery' ) ) {
			e.name = 'gallery';
		}
	});

	editor.on( 'BeforeSetContent', function( e ) {
		e.content = parseGallery( e.content );
	});

	editor.on( 'PostProcess', function( e ) {
		if ( e.get ) {
			e.content = getGallery( e.content );
		}
	});

	return {
		_do_gallery: parseGallery,
		_get_gallery: getGallery
	};
});
