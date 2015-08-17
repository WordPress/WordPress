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
			],
			canUndo, refNode, refPattern;

		editor.on( 'selectionchange', function() {
			canUndo = null;
		} );

		editor.on( 'keydown', function( event ) {
			if ( ( canUndo && event.keyCode === 27 /* ESCAPE */ ) || ( canUndo === 'space' && event.keyCode === VK.BACKSPACE ) ) {
				editor.undoManager.undo();
				event.preventDefault();
				event.stopImmediatePropagation();
			}

			if ( event.keyCode === VK.ENTER && ! VK.modifierPressed( event ) ) {
				watchEnter();
			}
		}, true );

		editor.on( 'keyup', function( event ) {
			if ( event.keyCode === VK.SPACEBAR && ! event.ctrlKey && ! event.metaKey && ! event.altKey ) {
				space();
			} else if ( event.keyCode === VK.ENTER && ! VK.modifierPressed( event ) ) {
				enter();
			}
		} );

		function firstTextNode( node ) {
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
				if ( child.nextSibling && child.nextSibling.nodeType === 3 ) {
					child = child.nextSibling;
				} else {
					child = null;
				}
			}

			return child;
		}

		function space() {
			var rng = editor.selection.getRng(),
				node = rng.startContainer,
				parent,
				text;

			if ( ! node || firstTextNode( node ) !== node ) {
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
					canUndo = 'space';
				} );

				return false;
			} );
		}

		function watchEnter() {
			var rng = editor.selection.getRng(),
				start = rng.startContainer,
				node = firstTextNode( start ),
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

			if ( node === start && tinymce.trim( text ) === pattern.start ) {
				return;
			}

			refNode = node;
			refPattern = pattern;
		}

		function enter() {
			if ( refNode ) {
				editor.undoManager.add();

				editor.undoManager.transact( function() {
					editor.formatter.apply( refPattern.format, {}, refNode );
					refNode.replaceData( 0, refNode.data.length, tinymce.trim( refNode.data.slice( refPattern.start.length ) ) );
				} );

				// We need to wait for native events to be triggered.
				setTimeout( function() {
					canUndo = 'enter';
				} );
			}

			refNode = null;
			refPattern = null;
		}
	} );
} )( window.tinymce, window.setTimeout );
