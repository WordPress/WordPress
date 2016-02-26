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
		var VK = tinymce.util.VK;

		var spacePatterns = [
			{ regExp: /^[*-]\s/, cmd: 'InsertUnorderedList' },
			{ regExp: /^1[.)]\s/, cmd: 'InsertOrderedList' }
		];

		var enterPatterns = [
			{ start: '##', format: 'h2' },
			{ start: '###', format: 'h3' },
			{ start: '####', format: 'h4' },
			{ start: '#####', format: 'h5' },
			{ start: '######', format: 'h6' },
			{ start: '>', format: 'blockquote' },
			{ regExp: /^\s*(?:(?:\* ?){3,}|(?:_ ?){3,}|(?:- ?){3,})\s*$/, element: 'hr' }
		];

		var inlinePatterns = [
			{ start: '*', end: '*', format: 'italic' },
			{ start: '**', end: '**', format: 'bold' },
			{ start: '_', end: '_', format: 'italic' },
			{ start: '__', end: '__', format: 'bold' },
			{ start: '`', end: '`', format: 'code' }
		];

		var canUndo;
		var chars = [];

		tinymce.each( inlinePatterns, function( pattern ) {
			tinymce.each( ( pattern.start + pattern.end ).split( '' ), function( c ) {
				if ( tinymce.inArray( chars, c ) === -1 ) {
					chars.push( c );
				}
			} );
		} );

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
				enter();
			}
		}, true );

		editor.on( 'keyup', function( event ) {
			if ( event.keyCode === VK.SPACEBAR && ! event.ctrlKey && ! event.metaKey && ! event.altKey ) {
				space();
			} else if ( event.keyCode > 47 && ! ( event.keyCode >= 91 && event.keyCode <= 93 ) ) {
				inline();
			}
		} );

		function inline() {
			var rng = editor.selection.getRng();
			var node = rng.startContainer;
			var offset = rng.startOffset;
			var startOffset;
			var endOffset;
			var pattern;
			var format;
			var zero;

			if ( node.nodeType !== 3 || ! node.data.length || ! offset ) {
				return;
			}

			if ( tinymce.inArray( chars, node.data.charAt( offset - 1 ) ) === -1 ) {
				return;
			}

			function findStart( node ) {
				var i = inlinePatterns.length;
				var offset;

				while ( i-- ) {
					pattern = inlinePatterns[ i ];
					offset = node.data.indexOf( pattern.end );

					if ( offset !== -1 ) {
						return offset;
					}
				}
			}

			startOffset = findStart( node );
			endOffset = node.data.lastIndexOf( pattern.end );

			if ( startOffset === endOffset || endOffset === -1 ) {
				return;
			}

			if ( endOffset - startOffset <= pattern.start.length ) {
				return;
			}

			if ( node.data.slice( startOffset + pattern.start.length, endOffset ).indexOf( pattern.start.slice( 0, 1 ) ) !== -1 ) {
				return;
			}

			format = editor.formatter.get( pattern.format );

			if ( format && format[0].inline ) {
				editor.undoManager.add();

				editor.undoManager.transact( function() {
					node.insertData( offset, '\u200b' );

					node = node.splitText( startOffset );
					zero = node.splitText( offset - startOffset );

					node.deleteData( 0, pattern.start.length );
					node.deleteData( node.data.length - pattern.end.length, pattern.end.length );

					editor.formatter.apply( pattern.format, {}, node );

					editor.selection.setCursorLocation( zero, 1 );
				} );

				// We need to wait for native events to be triggered.
				setTimeout( function() {
					canUndo = 'space';

					editor.once( 'selectionchange', function() {
						var offset;

						if ( zero ) {
							offset = zero.data.indexOf( '\u200b' );

							if ( offset !== -1 ) {
								zero.deleteData( offset, offset + 1 );
							}
						}
					} );
				} );
			}
		}

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

		function enter() {
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
				if ( enterPatterns[ i ].start ) {
					if ( text.indexOf( enterPatterns[ i ].start ) === 0 ) {
						pattern = enterPatterns[ i ];
						break;
					}
				} else if ( enterPatterns[ i ].regExp ) {
					if ( enterPatterns[ i ].regExp.test( text ) ) {
						pattern = enterPatterns[ i ];
						break;
					}
				}
			}

			if ( ! pattern ) {
				return;
			}

			if ( node === start && tinymce.trim( text ) === pattern.start ) {
				return;
			}

			editor.once( 'keyup', function() {
				editor.undoManager.add();

				editor.undoManager.transact( function() {
					if ( pattern.format ) {
						editor.formatter.apply( pattern.format, {}, node );
						node.replaceData( 0, node.data.length, ltrim( node.data.slice( pattern.start.length ) ) );
					} else if ( pattern.element ) {
						editor.getBody().replaceChild( document.createElement( pattern.element ), node.parentNode );
					}
				} );

				// We need to wait for native events to be triggered.
				setTimeout( function() {
					canUndo = 'enter';
				} );
			} );
		}

		function ltrim( text ) {
			return text ? text.replace( /^\s+/, '' ) : '';
		}
	} );
} )( window.tinymce, window.setTimeout );
