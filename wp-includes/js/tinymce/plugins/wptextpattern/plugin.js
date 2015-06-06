( function( tinymce, setTimeout ) {
	tinymce.PluginManager.add( 'wptextpattern', function( editor ) {
		var $$ = editor.$,
			patterns = [],
			canUndo = false;

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
			text = node.nodeValue;

			if ( node.nodeType !== 3 ) {
				return;
			}

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

			if ( child !== node ) {
				return;
			}

			tinymce.each( patterns, function( pattern ) {
				var replace = text.replace( pattern.regExp, '' );

				if ( text === replace ) {
					return;
				}

				if ( rng.startOffset !== text.length - replace.length ) {
					return;
				}

				editor.undoManager.add();

				editor.undoManager.transact( function() {
					if ( replace ) {
						$$( node ).replaceWith( document.createTextNode( replace ) );
					} else  {
						$$( node.parentNode ).empty().append( '<br>' );
					}

					editor.selection.setCursorLocation( parent );

					pattern.callback.apply( editor );
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
