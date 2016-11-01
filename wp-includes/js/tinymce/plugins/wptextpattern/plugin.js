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
	if ( tinymce.Env.ie && tinymce.Env.ie < 9 ) {
		return;
	}

	/**
	 * Escapes characters for use in a Regular Expression.
	 *
	 * @param  {String} string Characters to escape
	 *
	 * @return {String}        Escaped characters
	 */
	function escapeRegExp( string ) {
		return string.replace( /[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&' );
	}

	tinymce.PluginManager.add( 'wptextpattern', function( editor ) {
		var VK = tinymce.util.VK;
		var settings = editor.settings.wptextpattern || {};

		var spacePatterns = settings.space || [
			{ regExp: /^[*-]\s/, cmd: 'InsertUnorderedList' },
			{ regExp: /^1[.)]\s/, cmd: 'InsertOrderedList' }
		];

		var enterPatterns = settings.enter || [
			{ start: '##', format: 'h2' },
			{ start: '###', format: 'h3' },
			{ start: '####', format: 'h4' },
			{ start: '#####', format: 'h5' },
			{ start: '######', format: 'h6' },
			{ start: '>', format: 'blockquote' },
			{ regExp: /^(-){3,}$/, element: 'hr' }
		];

		var inlinePatterns = settings.inline || [
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

			if ( VK.metaKeyPressed( event ) ) {
				return;
			}

			if ( event.keyCode === VK.ENTER ) {
				enter();
			// Wait for the browser to insert the character.
			} else if ( event.keyCode === VK.SPACEBAR ) {
				setTimeout( space );
			} else if ( event.keyCode > 47 && ! ( event.keyCode >= 91 && event.keyCode <= 93 ) ) {
				setTimeout( inline );
			}
		}, true );

		function inline() {
			var rng = editor.selection.getRng();
			var node = rng.startContainer;
			var offset = rng.startOffset;
			var startOffset;
			var endOffset;
			var pattern;
			var format;
			var zero;

			// We need a non empty text node with an offset greater than zero.
			if ( ! node || node.nodeType !== 3 || ! node.data.length || ! offset ) {
				return;
			}

			// The ending character should exist in the patterns registered.
			if ( tinymce.inArray( chars, node.data.charAt( offset - 1 ) ) === -1 ) {
				return;
			}

			var string = node.data.slice( 0, offset );

			tinymce.each( inlinePatterns, function( p ) {
				var regExp = new RegExp( escapeRegExp( p.start ) + '\\S+' + escapeRegExp( p.end ) + '$' );
				var match = string.match( regExp );

				if ( ! match ) {
					return;
				}

				// Don't allow pattern characters in the text.
				if ( node.data.slice( match.index + p.start.length, offset - p.end.length ).indexOf( p.start.slice( 0, 1 ) ) !== -1 ) {
					return;
				}

				startOffset = match.index;
				endOffset = offset - p.end.length;
				pattern = p;

				return false;
			} );

			if ( ! pattern ) {
				return;
			}

			format = editor.formatter.get( pattern.format );

			if ( format && format[0].inline ) {
				editor.undoManager.add();

				editor.undoManager.transact( function() {
					node.insertData( offset, '\uFEFF' );

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
							offset = zero.data.indexOf( '\uFEFF' );

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
				text, pattern, parent;

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
						parent = node.parentNode && node.parentNode.parentNode;

						if ( parent ) {
							parent.replaceChild( document.createElement( pattern.element ), node.parentNode );
						}
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
