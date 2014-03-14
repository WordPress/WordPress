/* global tinymce */
/**
 * WordPress View plugin.
 */
tinymce.PluginManager.add( 'wpview', function( editor ) {
	var selected,
		VK = tinymce.util.VK,
		TreeWalker = tinymce.dom.TreeWalker,
		toRemove = false;

	function getParentView( node ) {
		while ( node && node.nodeName !== 'BODY' ) {
			if ( isView( node ) ) {
				return node;
			}

			node = node.parentNode;
		}
	}

	function isView( node ) {
		return node && /\bwpview-wrap\b/.test( node.className );
	}

	function createPadNode() {
		return editor.dom.create( 'p', { 'data-wpview-pad': 1 },
			( tinymce.Env.ie && tinymce.Env.ie < 11 ) ? '' : '<br data-mce-bogus="1" />' );
	}

	/**
	 * Get the text/shortcode string for a view.
	 *
	 * @param view The view wrapper's HTML id or node
	 * @returns string The text/shoercode string of the view
	 */
	function getViewText( view ) {
		view = getParentView( typeof view === 'string' ? editor.dom.get( view ) : view );

		if ( view ) {
			return window.decodeURIComponent( editor.dom.getAttrib( view, 'data-wpview-text' ) || '' );
		}
		return '';
	}

	/**
	 * Set the view's original text/shortcode string
	 *
	 * @param view The view wrapper's HTML id or node
	 * @param text The text string to be set
	 */
	function setViewText( view, text ) {
		view = getParentView( typeof view === 'string' ? editor.dom.get( view ) : view );

		if ( view ) {
			editor.dom.setAttrib( view, 'data-wpview-text', window.encodeURIComponent( text || '' ) );
			return true;
		}
		return false;
	}

	function _stop( event ) {
		event.stopPropagation();
	}

	function select( viewNode ) {
		var clipboard,
			dom = editor.dom;

		// Bail if node is already selected.
		if ( viewNode === selected ) {
			return;
		}

		deselect();
		selected = viewNode;
		dom.addClass( viewNode, 'selected' );

		clipboard = dom.create( 'div', {
			'class': 'wpview-clipboard',
			'contenteditable': 'true'
		}, getViewText( viewNode ) );

		viewNode.appendChild( clipboard );

		// Both of the following are necessary to prevent manipulating the selection/focus
		editor.dom.bind( clipboard, 'beforedeactivate focusin focusout', _stop );
		editor.dom.bind( selected, 'beforedeactivate focusin focusout', _stop );

		// select the hidden div
		editor.selection.select( clipboard, true );
	}

	/**
	 * Deselect a selected view and remove clipboard
	 */
	function deselect() {
		var clipboard,
			dom = editor.dom;

		if ( selected ) {
			clipboard = editor.dom.select( '.wpview-clipboard', selected )[0];
			dom.unbind( clipboard );
			dom.remove( clipboard );

			dom.unbind( selected, 'beforedeactivate focusin focusout click mouseup', _stop );
			dom.removeClass( selected, 'selected' );

			editor.selection.select( selected.nextSibling );
			editor.selection.collapse();

		}

		selected = null;
	}

	// Check if the `wp.mce` API exists.
	if ( typeof wp === 'undefined' || ! wp.mce ) {
		return;
	}

	editor.on( 'BeforeAddUndo', function( event ) {
		if ( selected && ! toRemove ) {
			event.preventDefault();
		}
	});

	// When the editor's content changes, scan the new content for
	// matching view patterns, and transform the matches into
	// view wrappers.
	editor.on( 'BeforeSetContent', function( e ) {
		if ( ! e.content ) {
			return;
		}

		e.content = wp.mce.views.toViews( e.content );
	});

	// When the editor's content has been updated and the DOM has been
	// processed, render the views in the document.
	editor.on( 'SetContent', function( event ) {
		var body, padNode;

		// don't (re-)render views if the format of the content is raw
		// to avoid adding additional undo levels on undo/redo
		if ( event.format !== 'raw' ) {
			wp.mce.views.render();
		}

		// Add padding <p> if the noneditable node is last
		if ( event.load || ! event.set ) {
			body = editor.getBody();

			if ( isView( body.lastChild ) ) {
				padNode = createPadNode();
				body.appendChild( padNode );
				editor.selection.setCursorLocation( padNode, 0 );
			}
		}

	//	refreshEmptyContentNode();
	});

	// Detect mouse down events that are adjacent to a view when a view is the first view or the last view
	editor.on( 'click', function( event ) {
		var body = editor.getBody(),
			doc = editor.getDoc(),
			scrollTop = doc.documentElement.scrollTop || body.scrollTop || 0,
			x, y, firstNode, lastNode, padNode;

		if ( event.target.nodeName === 'HTML' && ! event.metaKey && ! event.ctrlKey ) {
			firstNode = body.firstChild;
			lastNode = body.lastChild;
			x = event.clientX;
			y = event.clientY;

			if ( isView( firstNode ) && ( ( x < firstNode.offsetLeft && y < ( firstNode.offsetHeight - scrollTop ) ) ||
				y < firstNode.offsetTop ) ) {
				// detect events above or to the left of the first view

				padNode = createPadNode();
				body.insertBefore( padNode, firstNode );
			} else if ( isView( lastNode ) && ( x > ( lastNode.offsetLeft + lastNode.offsetWidth ) ||
				( ( scrollTop + y ) - ( lastNode.offsetTop + lastNode.offsetHeight ) ) > 0 ) ) {
				// detect events to the right and below the last view

				padNode = createPadNode();
				body.appendChild( padNode );
			}

			if ( padNode ) {
				editor.selection.setCursorLocation( padNode, 0 );
			}
		}
	});

	editor.on( 'init', function() {
		var selection = editor.selection;
		// When a view is selected, ensure content that is being pasted
		// or inserted is added to a text node (instead of the view).
		editor.on( 'BeforeSetContent', function() {
			var walker, target,
				view = getParentView( selection.getNode() );

			// If the selection is not within a view, bail.
			if ( ! view ) {
				return;
			}

			if ( ! view.nextSibling || isView( view.nextSibling ) ) {
				// If there are no additional nodes or the next node is a
				// view, create a text node after the current view.
				target = editor.getDoc().createTextNode('');
				editor.dom.insertAfter( target, view );
			} else {
				// Otherwise, find the next text node.
				walker = new TreeWalker( view.nextSibling, view.nextSibling );
				target = walker.next();
			}

			// Select the `target` text node.
			selection.select( target );
			selection.collapse( true );
		});

		// When the selection's content changes, scan any new content
		// for matching views.
		//
		// Runs on paste and on inserting nodes/html.
		editor.on( 'SetContent', function( e ) {
			if ( ! e.context ) {
				return;
			}

			var node = selection.getNode();

			if ( ! node.innerHTML ) {
				return;
			}

			node.innerHTML = wp.mce.views.toViews( node.innerHTML );
		});

		editor.dom.bind( editor.getBody(), 'mousedown mouseup click', function( event ) {
			var view = getParentView( event.target );

			// Contain clicks inside the view wrapper
			if ( view ) {
				event.stopPropagation();

				if ( event.type === 'click' ) {
					if ( ! event.metaKey && ! event.ctrlKey ) {
						if ( editor.dom.hasClass( event.target, 'edit' ) ) {
							wp.mce.views.edit( view );
						} else if ( editor.dom.hasClass( event.target, 'remove' ) ) {
							editor.dom.remove( view );
						}
					}
				}
				select( view );
				// returning false stops the ugly bars from appearing in IE11 and stops the view being selected as a range in FF
				// unfortunately, it also inhibits the dragging fo views to a new location
				return false;
			} else {
				if ( event.type === 'click' ) {
					deselect();
				}
			}
		});

	});

	editor.on( 'PreProcess', function( event ) {
		var dom = editor.dom;

		// Remove empty padding nodes
		tinymce.each( dom.select( 'p[data-wpview-pad]', event.node ), function( node ) {
			if ( dom.isEmpty( node ) ) {
				dom.remove( node );
			} else {
				dom.setAttrib( node, 'data-wpview-pad', null );
			}
		});

		// Replace the wpview node with the wpview string/shortcode?
		tinymce.each( dom.select( 'div[data-wpview-text]', event.node ), function( node ) {
			// Empty the wrap node
			if ( 'textContent' in node ) {
				node.textContent = '';
			} else {
				node.innerText = '';
			}

			// TODO: that makes all views into block tags (as we use <div>).
			// Can use 'PostProcess' and a regex instead.
			dom.replace( dom.create( 'p', null, window.decodeURIComponent( dom.getAttrib( node, 'data-wpview-text' ) ) ), node );
		});
    });

	editor.on( 'keydown', function( event ) {
		var keyCode = event.keyCode,
			view;

		// If a view isn't selected, let the event go on its merry way.
		if ( ! selected ) {
			return;
		}

		// Let keypresses that involve the command or control keys through.
		// Also, let any of the F# keys through.
		if ( event.metaKey || event.ctrlKey || ( keyCode >= 112 && keyCode <= 123 ) ) {
			if ( ( event.metaKey || event.ctrlKey ) && keyCode === 88 ) {
				toRemove = selected;
			}
			return;
		}

		// If the caret is not within the selected view, deselect the
		// view and bail.
		view = getParentView( editor.selection.getNode() );

		if ( view !== selected ) {
			deselect();
			return;
		}

		// If delete or backspace is pressed, delete the view.
		if ( keyCode === VK.DELETE || keyCode === VK.BACKSPACE ) {
			editor.dom.remove( selected );
		}

		event.preventDefault();
	});

	editor.on( 'keyup', function( event ) {
		var padNode,
			keyCode = event.keyCode,
			body = editor.getBody(),
			range;

		if ( toRemove ) {
			editor.dom.remove( toRemove );
			toRemove = false;
		}

		if ( keyCode === VK.DELETE || keyCode === VK.BACKSPACE ) {
			// Make sure there is padding if the last element is a view
			if ( isView( body.lastChild ) ) {
				padNode = createPadNode();
				body.appendChild( padNode );

				if ( body.childNodes.length === 2 ) {
					editor.selection.setCursorLocation( padNode, 0 );
				}
			}

			range = editor.selection.getRng();

			// Allow an initial element in the document to be removed when it is before a view
			if ( body.firstChild === range.startContainer && range.collapsed === true &&
					isView( range.startContainer.nextSibling ) && range.startOffset === 0 ) {

				editor.dom.remove( range.startContainer );
			}
		}
	});

	return {
		getViewText: getViewText,
		setViewText: setViewText
	};
});
