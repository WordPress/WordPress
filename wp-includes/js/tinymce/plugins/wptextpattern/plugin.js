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
			patterns = [],
			canUndo = false;

		/**
		 * Add a pattern to format with a callback.
		 *
		 * @since 4.3.0
		 *
		 * @param {RegExp}   regExp
		 * @param {Function} callback
		 */
		function add( regExp, callback ) {
			patterns.push( {
				regExp: regExp,
				callback: callback
			} );
		}

		add( /^[*-]\s/, function() {
			this.execCommand( 'InsertUnorderedList' );
		} );

		add( /^1[.)]\s/, function() {
			this.execCommand( 'InsertOrderedList' );
		} );

		add( /^>\s/, function() {
			this.formatter.toggle( 'blockquote' );
		} );

		add( /^(#{2,6})\s/, function() {
			this.formatter.toggle( 'h' + arguments[1].length );
		} );

		editor.on( 'selectionchange', function() {
			canUndo = false;
		} );

		editor.on( 'keydown', function( event ) {
			if ( canUndo && event.keyCode === tinymce.util.VK.BACKSPACE ) {
				editor.undoManager.undo();
				event.preventDefault();
			}
		} );

		editor.on( 'keyup', function( event ) {
			var rng, node, text, parent, child;

			if ( event.keyCode !== tinymce.util.VK.SPACEBAR ) {
				return;
			}

			rng = editor.selection.getRng();
			node = rng.startContainer;

			if ( ! node || node.nodeType !== 3 ) {
				return;
			}

			text = node.nodeValue;
			parent = editor.dom.getParent( node, 'p' );

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

			if ( ! child.nodeValue ) {
				child = child.nextSibling;
			}

			if ( child !== node ) {
				return;
			}

			tinymce.each( patterns, function( pattern ) {
				var args,
					replace = text.replace( pattern.regExp, function() {
						args = arguments;
						return '';
					} );

				if ( text === replace ) {
					return;
				}

				if ( rng.startOffset !== text.length - replace.length ) {
					return;
				}

				editor.undoManager.add();

				editor.undoManager.transact( function() {
					var $$parent;

					if ( replace ) {
						$$( node ).replaceWith( document.createTextNode( replace ) );
					} else  {
						$$parent = $$( node.parentNode );

						$$( node ).remove();

						if ( ! $$parent.html() ) {
							$$parent.append( '<br>' );
						}
					}

					editor.selection.setCursorLocation( parent );

					pattern.callback.apply( editor, args );
				} );

				// We need to wait for native events to be triggered.
				setTimeout( function() {
					canUndo = true;
				} );

				return false;
			} );
		} );
	} );
} )( window.tinymce, window.setTimeout );
