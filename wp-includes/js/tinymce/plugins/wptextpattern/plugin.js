/**
 * Text pattern plugin for TinyMCE
 *
 * @since 4.3.0
 *
 * This plugin can automatically format text patterns as you type. It includes two patterns:
 *  - Unordered list (`* ` and `- `).
 *  - Ordered list (`1. ` and `1) `).
 *
 * If the transformation in unwanted, the user can undo the change by pressing backspace,
 * using the undo shortcut, or the undo button in the toolbar.
 */
( function( tinymce, setTimeout ) {
	tinymce.PluginManager.add( 'wptextpattern', function( editor ) {
		var VK = tinymce.util.VK,
			canUndo = false,
			spacePatterns = [
				{ regExp: /^[*-]\s/, cmd: 'InsertUnorderedList' },
				{ regExp: /^1[.)]\s/, cmd: 'InsertOrderedList' }
			],
			enterPatterns = [
				{ start: '##', format: 'h2' },
				{ start: '###', format: 'h3' },
				{ start: '####', format: 'h4' },
				{ start: '#####', format: 'h5' },
				{ start: '######', format: 'h6' },
				{ start: '>', format: 'blockquote' }
			];

		editor.on( 'selectionchange', function() {
			canUndo = false;
		} );

		editor.on( 'keydown', function( event ) {
			if ( canUndo && ( event.keyCode === VK.BACKSPACE || event.keyCode === 27 /* ESCAPE */ ) ) {
				editor.undoManager.undo();
				event.preventDefault();
			}

			if ( event.keyCode === VK.ENTER && ! VK.modifierPressed( event ) ) {
				enter();
			}
		}, true );

		editor.on( 'keyup', function( event ) {
			if ( event.keyCode === VK.SPACEBAR && ! VK.modifierPressed( event ) ) {
				space();
			}
		} );

		function firstNode( node ) {
			var parent = editor.dom.getParent( node, 'p' ),
				child;

			if ( ! parent ) {
				return;
			}

			while ( child = parent.firstChild ) {
				if ( child.nodeType !== 3 ) {
					parent = child;
				} else {
					break;
				}
			}

			if ( ! child ) {
				return;
			}

			if ( ! child.data ) {
				child = child.nextSibling;
			}

			return child;
		}

		function space() {
			var rng = editor.selection.getRng(),
				node = rng.startContainer,
				parent,
				text;

			if ( ! node || firstNode( node ) !== node ) {
				return;
			}

			parent = node.parentNode;
			text = node.data;

			tinymce.each( spacePatterns, function( pattern ) {
				var match = text.match( pattern.regExp );

				if ( ! match || rng.startOffset !== match[0].length ) {
					return;
				}

				editor.undoManager.add();

				editor.undoManager.transact( function() {
					node.deleteData( 0, match[0].length );

					if ( ! parent.innerHTML ) {
						parent.appendChild( document.createElement( 'br' ) );
					}

					editor.selection.setCursorLocation( parent );
					editor.execCommand( pattern.cmd );
				} );

				// We need to wait for native events to be triggered.
				setTimeout( function() {
					canUndo = true;
				} );

				return false;
			} );
		}

		function enter() {
			var selection = editor.selection,
				rng = selection.getRng(),
				offset = rng.startOffset,
				start = rng.startContainer,
				node = firstNode( start ),
				i = enterPatterns.length,
				text, pattern;

			if ( ! node ) {
				return;
			}

			text = node.data;

			while ( i-- ) {
				 if ( text.indexOf( enterPatterns[ i ].start ) === 0 ) {
				 	pattern = enterPatterns[ i ];
				 	break;
				 }
			}

			if ( ! pattern ) {
				return;
			}

			if ( node === start ) {
				if ( tinymce.trim( text ) === pattern.start ) {
					return;
				}

				offset = Math.max( 0, offset - pattern.start.length );
			}

			editor.undoManager.add();

			editor.undoManager.transact( function() {
				node.deleteData( 0, pattern.start.length );

				editor.formatter.apply( pattern.format, {}, start );

				rng.setStart( start, offset );
				rng.collapse( true );
				selection.setRng( rng );
			} );
		}
	} );
} )( window.tinymce, window.setTimeout );
