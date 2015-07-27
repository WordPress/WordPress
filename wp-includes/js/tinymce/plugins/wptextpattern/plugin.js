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
		var $$ = editor.$,
			VK = tinymce.util.VK,
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
			if ( event.keyCode === VK.SPACEBAR || ! VK.modifierPressed( event ) ) {
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
				text;

			if ( firstNode( node ) !== node ) {
				return;
			}

			text = node.data;

			tinymce.each( spacePatterns, function( pattern ) {
				var replace = text.replace( pattern.regExp, '' );

				if ( text === replace ) {
					return;
				}

				if ( rng.startOffset !== text.length - replace.length ) {
					return;
				}

				editor.undoManager.add();

				editor.undoManager.transact( function() {
					var parent = node.parentNode,
						$$parent;

					if ( replace ) {
						$$( node ).replaceWith( document.createTextNode( replace ) );
					} else  {
						$$parent = $$( parent );

						$$( node ).remove();

						if ( ! $$parent.html() ) {
							$$parent.append( '<br>' );
						}
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
