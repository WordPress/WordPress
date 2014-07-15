/* global tinymce */
/**
 * WordPress View plugin.
 */
tinymce.PluginManager.add( 'wpview', function( editor ) {
	var selected,
		Env = tinymce.Env,
		VK = tinymce.util.VK,
		TreeWalker = tinymce.dom.TreeWalker,
		toRemove = false,
		firstFocus = true,
		cursorInterval, lastKeyDownNode, setViewCursorTries, focus;

	function getView( node ) {
		return getParent( node, 'wpview-wrap' );
	}

	/**
	 * Returns the node or a parent of the node that has the passed className.
	 * Doing this directly is about 40% faster
	 */
	function getParent( node, className ) {
		while ( node && node.parentNode ) {
			if ( node.className && (' ' + node.className + ' ').indexOf(' ' + className + ' ') !== -1 ) {
				return node;
			}

			node = node.parentNode;
		}

		return false;
	}

	/**
	 * Get the text/shortcode string for a view.
	 *
	 * @param view The view wrapper's node
	 * @returns string The text/shoercode string of the view
	 */
	function getViewText( view ) {
		if ( view = getView( view ) ) {
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
		view = getView( view );

		if ( view ) {
			editor.dom.setAttrib( view, 'data-wpview-text', window.encodeURIComponent( text || '' ) );
			return true;
		}

		return false;
	}

	function _stop( event ) {
		event.stopPropagation();
	}

	function setViewCursor( before, view ) {
		var location = before ? 'before' : 'after',
			offset = before ? 0 : 1;
		deselect();
		editor.selection.setCursorLocation( editor.dom.select( '.wpview-selection-' + location, view )[0], offset );
		editor.nodeChanged();
	}

	function handleEnter( view, before, keyCode ) {
		var dom = editor.dom,
			padNode = dom.create( 'p' );

		if ( ! ( Env.ie && Env.ie < 11 ) ) {
			padNode.innerHTML = '<br data-mce-bogus="1">';
		}

		if ( before ) {
			view.parentNode.insertBefore( padNode, view );
		} else {
			dom.insertAfter( padNode, view );
		}

		deselect();

		if ( before && keyCode === VK.ENTER ) {
			setViewCursor( before, view );
		} else {
			editor.selection.setCursorLocation( padNode, 0 );
		}

		editor.nodeChanged();
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
		dom.setAttrib( viewNode, 'data-mce-selected', 1 );

		clipboard = dom.create( 'div', {
			'class': 'wpview-clipboard',
			'contenteditable': 'true'
		}, getViewText( viewNode ) );

		editor.dom.select( '.wpview-body', viewNode )[0].appendChild( clipboard );

		// Both of the following are necessary to prevent manipulating the selection/focus
		dom.bind( clipboard, 'beforedeactivate focusin focusout', _stop );
		dom.bind( selected, 'beforedeactivate focusin focusout', _stop );

		// Make sure that the editor is focused.
		// It is possible that the editor is not focused when the mouse event fires
		// without focus, the selection will not work properly.
		editor.getBody().focus();

		// select the hidden div
		editor.selection.select( clipboard, true );
		editor.nodeChanged();
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
			dom.setAttrib( selected, 'data-mce-selected', null );
		}

		selected = null;
	}

	// Check if the `wp.mce` API exists.
	if ( typeof wp === 'undefined' || ! wp.mce ) {
		return;
	}

	// Remove the content of view wrappers from HTML string
	function emptyViews( content ) {
		return content.replace(/<div[^>]+data-wpview-text=\"([^"]+)"[^>]*>[\s\S]+?wpview-selection-after[^>]+>(?:&nbsp;|\u00a0)*<\/p><\/div>/g, '$1' );
	}

	// Prevent adding undo levels on changes inside a view wrapper
	editor.on( 'BeforeAddUndo', function( event ) {
		if ( event.lastLevel && emptyViews( event.level.content ) === emptyViews( event.lastLevel.content ) ) {
			event.preventDefault();
		}
	});

	// When the editor's content changes, scan the new content for
	// matching view patterns, and transform the matches into
	// view wrappers.
	editor.on( 'BeforeSetContent', function( event ) {
		var node;

		if ( ! event.content ) {
			return;
		}

		if ( ! event.initial ) {
			wp.mce.views.unbind( editor );
		}

		node = editor.selection.getNode();

		// When a url is pasted, only try to embed it when pasted in an empty paragrapgh.
		if ( event.content.match( /^\s*(https?:\/\/[^\s"]+)\s*$/i ) &&
			( node.nodeName !== 'P' || node.parentNode !== editor.getBody() || ! editor.dom.isEmpty( node ) ) ) {
			return;
		}

		event.content = wp.mce.views.toViews( event.content );
	});

	// When the editor's content has been updated and the DOM has been
	// processed, render the views in the document.
	editor.on( 'SetContent', function() {
		wp.mce.views.render();
	});

	// Set the cursor before or after a view when clicking next to it.
	editor.on( 'click', function( event ) {
		var x = event.clientX,
			y = event.clientY,
			body = editor.getBody(),
			bodyRect = body.getBoundingClientRect(),
			first = body.firstChild,
			firstRect = first.getBoundingClientRect(),
			last = body.lastChild,
			lastRect = last.getBoundingClientRect(),
			view;

		if ( y < firstRect.top && ( view = getView( first ) ) ) {
			setViewCursor( true, view );
			event.preventDefault();
		} else if ( y > lastRect.bottom && ( view = getView( last ) ) ) {
			setViewCursor( false, view );
			event.preventDefault();
		} else {
			tinymce.each( editor.dom.select( '.wpview-wrap' ), function( view ) {
				var rect = view.getBoundingClientRect();

				if ( y >= rect.top && y <= rect.bottom ) {
					if ( x < bodyRect.left ) {
						setViewCursor( true, view );
						event.preventDefault();
					} else if ( x > bodyRect.right ) {
						setViewCursor( false, view );
						event.preventDefault();
					}
					return;
				}
			});
		}
	});

	editor.on( 'init', function() {
		var selection = editor.selection;

		// When a view is selected, ensure content that is being pasted
		// or inserted is added to a text node (instead of the view).
		editor.on( 'BeforeSetContent', function() {
			var walker, target,
				view = getView( selection.getNode() );

			// If the selection is not within a view, bail.
			if ( ! view ) {
				return;
			}

			if ( ! view.nextSibling || getView( view.nextSibling ) ) {
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

		editor.dom.bind( editor.getBody().parentNode, 'mousedown mouseup click', function( event ) {
			var view = getView( event.target ),
				deselectEventType;

			firstFocus = false;

			// Contain clicks inside the view wrapper
			if ( view ) {
				event.stopPropagation();

				// Hack to try and keep the block resize handles from appearing. They will show on mousedown and then be removed on mouseup.
				if ( Env.ie <= 10 ) {
					deselect();
				}

				select( view );

				if ( event.type === 'click' && ! event.metaKey && ! event.ctrlKey ) {
					if ( editor.dom.hasClass( event.target, 'edit' ) ) {
						wp.mce.views.edit( view );
					} else if ( editor.dom.hasClass( event.target, 'remove' ) ) {
						editor.dom.remove( view );
					}
				}

				// Returning false stops the ugly bars from appearing in IE11 and stops the view being selected as a range in FF.
				// Unfortunately, it also inhibits the dragging of views to a new location.
				return false;
			} else {
				// Fix issue with deselecting a view in IE8. Without this hack, clicking content above the view wouldn't actually deselect it
				// and the caret wouldn't be placed at the mouse location
				if ( Env.ie && Env.ie <= 8 ) {
					deselectEventType = 'mouseup';
				} else {
					deselectEventType = 'mousedown';
				}

				if ( event.type === deselectEventType ) {
					deselect();
				}
			}
		});
	});

	editor.on( 'PreProcess', function( event ) {
		// Empty the wpview wrap nodes
		tinymce.each( editor.dom.select( 'div[data-wpview-text]', event.node ), function( node ) {
			node.textContent = node.innerText = '\u00a0';
		});
    });

    editor.on( 'PostProcess', function( event ) {
		if ( event.content ) {
			event.content = event.content.replace( /<div [^>]*?data-wpview-text="([^"]*)"[^>]*>[\s\S]*?<\/div>/g, function( match, shortcode ) {
				if ( shortcode ) {
					return '<p>' + window.decodeURIComponent( shortcode ) + '</p>';
				}
				return ''; // If error, remove the view wrapper
			});
		}
	});

	// (De)select views when arrow keys are used to navigate the content of the editor.
	editor.on( 'keydown', function( event ) {
		if ( event.metaKey || event.ctrlKey || ( keyCode >= 112 && keyCode <= 123 ) ) {
			return;
		}

		if ( selected ) {
			return;
		}

		var keyCode = event.keyCode,
			dom = editor.dom,
			selection = editor.selection,
			node = selection.getNode(),
			view = getView( node ),
			cursorBefore, cursorAfter,
			range, clonedRange, tempRange;

		lastKeyDownNode = node;

		// Make sure we don't delete part of a view.
		// If the range ends or starts with the view, we'll need to trim it.
		if ( ! selection.isCollapsed() ) {
			range = selection.getRng();

			if ( view = getView( range.endContainer ) ) {
				clonedRange = range.cloneRange();
				selection.select( view.previousSibling, true );
				selection.collapse();
				tempRange = selection.getRng();
				clonedRange.setEnd( tempRange.endContainer, tempRange.endOffset );
				selection.setRng( clonedRange );
			} else if ( view = getView( range.startContainer ) ) {
				clonedRange = range.cloneRange();
				clonedRange.setStart( view.nextSibling, 0 );
				selection.setRng( clonedRange );
			}
		}

		if ( ! view ) {
			return;
		}

		if ( ! ( ( cursorBefore = dom.hasClass( view, 'wpview-selection-before' ) ) ||
				( cursorAfter = dom.hasClass( view, 'wpview-selection-after' ) ) ) ) {
			return;
		}

		if ( ( cursorAfter && keyCode === VK.UP ) || ( cursorBefore && keyCode === VK.BACKSPACE ) ) {
			if ( view.previousSibling ) {
				if ( getView( view.previousSibling ) ) {
					setViewCursor( false, view.previousSibling );
				} else {
					if ( dom.isEmpty( view.previousSibling ) && keyCode === VK.BACKSPACE ) {
						dom.remove( view.previousSibling );
					} else {
						selection.select( view.previousSibling, true );
						selection.collapse();
					}
				}
			} else {
				setViewCursor( true, view );
			}
			event.preventDefault();
		} else if ( cursorAfter && ( keyCode === VK.DOWN || keyCode === VK.RIGHT ) ) {
			if ( view.nextSibling ) {
				if ( getView( view.nextSibling ) ) {
					setViewCursor( keyCode === VK.RIGHT, view.nextSibling );
				} else {
					selection.setCursorLocation( view.nextSibling, 0 );
				}
			}
			event.preventDefault();
		} else if ( cursorBefore && ( keyCode === VK.UP || keyCode ===  VK.LEFT ) ) {
			if ( view.previousSibling ) {
				if ( getView( view.previousSibling ) ) {
					setViewCursor( keyCode === VK.UP, view.previousSibling );
				} else {
					selection.select( view.previousSibling, true );
					selection.collapse();
				}
			}
			event.preventDefault();
		} else if ( cursorBefore && keyCode === VK.DOWN ) {
			if ( view.nextSibling ) {
				if ( getView( view.nextSibling ) ) {
					setViewCursor( true, view.nextSibling );
				} else {
					selection.setCursorLocation( view.nextSibling, 0 );
				}
			} else {
				setViewCursor( false, view );
			}
			event.preventDefault();
		} else if ( ( cursorAfter && keyCode === VK.LEFT ) || ( cursorBefore && keyCode === VK.RIGHT ) ) {
			select( view );
			event.preventDefault();
			event.stopImmediatePropagation();
		} else if ( cursorAfter && keyCode === VK.BACKSPACE ) {
			dom.remove( view );
			event.preventDefault();
		} else if ( cursorAfter ) {
			handleEnter( view );
		} else if ( cursorBefore ) {
			handleEnter( view , true, keyCode );
		}

		if ( keyCode === VK.ENTER ) {
			event.preventDefault();
		}
	});

	// Handle key presses for selected views.
	editor.on( 'keydown', function( event ) {
		var dom = editor.dom,
			keyCode = event.keyCode,
			selection = editor.selection,
			view;

		// If a view isn't selected, let the event go on its merry way.
		if ( ! selected ) {
			return;
		}

		// Let key presses that involve the command or control keys through.
		// Also, let any of the F# keys through.
		if ( event.metaKey || event.ctrlKey || ( keyCode >= 112 && keyCode <= 123 ) ) {
			// But remove the view when cmd/ctrl + x/backspace are pressed.
			if ( ( event.metaKey || event.ctrlKey ) && ( keyCode === 88 || keyCode === VK.BACKSPACE ) ) {
				// We'll remove a cut view on keyup, otherwise the browser can't copy the content.
				if ( keyCode === 88 ) {
					toRemove = selected;
				} else {
					editor.dom.remove( selected );
				}
			}
			return;
		}

		view = getView( selection.getNode() );

		// If the caret is not within the selected view, deselect the view and bail.
		if ( view !== selected ) {
			deselect();
			return;
		}

		if ( keyCode === VK.LEFT ) {
			setViewCursor( true, view );
		} else if ( keyCode === VK.UP ) {
			if ( view.previousSibling ) {
				if ( getView( view.previousSibling ) ) {
					setViewCursor( true, view.previousSibling );
				} else {
					deselect();
					selection.select( view.previousSibling, true );
					selection.collapse();
				}
			} else {
				setViewCursor( true, view );
			}

		} else if ( keyCode === VK.RIGHT ) {
			setViewCursor( false, view );
		} else if ( keyCode === VK.DOWN ) {
			if ( view.nextSibling ) {
				if ( getView( view.nextSibling ) ) {
					setViewCursor( false, view.nextSibling );
				} else {
					deselect();
					selection.setCursorLocation( view.nextSibling, 0 );
				}
			} else {
				setViewCursor( false, view );
			}
		} else if ( keyCode === VK.ENTER ) {
			handleEnter( view );
		} else if ( keyCode === VK.DELETE || keyCode === VK.BACKSPACE ) {
			dom.remove( selected );
		}

		event.preventDefault();
	});

	// Make sure we don't eat any content.
	editor.on( 'keydown', function( event ) {
		var selection = editor.selection,
			node, range, view;

		if ( event.keyCode === VK.BACKSPACE ) {
			node = selection.getNode();

			if ( editor.dom.isEmpty( node ) ) {
				if ( view = getView( node.previousSibling ) ) {
					setViewCursor( false, view );
					editor.dom.remove( node );
					event.preventDefault();
				}
			} else if ( ( range = selection.getRng() ) &&
					range.startOffset === 0 &&
					range.endOffset === 0 &&
					( view = getView( node.previousSibling ) ) ) {
				setViewCursor( false, view );
				event.preventDefault();
			}
		}
	});

	editor.on( 'keyup', function() {
		if ( toRemove ) {
			editor.dom.remove( toRemove );
			toRemove = false;
		}
	});

	editor.on( 'focus', function() {
		var view;

		focus = true;
		editor.dom.addClass( editor.getBody(), 'has-focus' );

		// Edge case: show the fake caret when the editor is focused for the first time
		// and the first element is a view.
		if ( firstFocus && ( view = getView( editor.getBody().firstChild ) ) ) {
			setViewCursor( true, view );
		}

		firstFocus = false;
	} );

	editor.on( 'blur', function() {
		focus = false;
		editor.dom.removeClass( editor.getBody(), 'has-focus' );
	} );

	editor.on( 'nodechange', function( event ) {
		var dom = editor.dom,
			views = editor.dom.select( '.wpview-wrap' ),
			className = event.element.className,
			view = getView( event.element ),
			lKDN = lastKeyDownNode;

		lastKeyDownNode = false;

		clearInterval( cursorInterval );

		dom.removeClass( views, 'wpview-selection-before' );
		dom.removeClass( views, 'wpview-selection-after' );
		dom.removeClass( views, 'wpview-cursor-hide' );

		if ( focus ) {
			if ( view ) {
				if ( className === 'wpview-selection-before' || className === 'wpview-selection-after' && editor.selection.isCollapsed() ) {
					setViewCursorTries = 0;

					deselect();

					// Make sure the cursor arrived in the right node.
					// This is necessary for Firefox.
					if ( lKDN === view.previousSibling ) {
						setViewCursor( true, view );
						return;
					} else if ( lKDN === view.nextSibling ) {
						setViewCursor( false, view );
						return;
					}

					dom.addClass( view, className );

					cursorInterval = setInterval( function() {
						if ( dom.hasClass( view, 'wpview-cursor-hide' ) ) {
							dom.removeClass( view, 'wpview-cursor-hide' );
						} else {
							dom.addClass( view, 'wpview-cursor-hide' );
						}
					}, 500 );
				// If the cursor lands anywhere else in the view, set the cursor before it.
				// Only try this once to prevent a loop. (You never know.)
				} else if ( ! getParent( event.element, 'wpview-clipboard' ) && ! setViewCursorTries ) {
					deselect();
					setViewCursorTries++;
					setViewCursor( true, view );
				}
			} else {
				deselect();
			}
		}
	});

	editor.on( 'resolvename', function( event ) {
		if ( editor.dom.hasClass( event.target, 'wpview-wrap' ) ) {
			event.name = editor.dom.getAttrib( event.target, 'data-wpview-type' ) || 'wpview';
			event.stopPropagation();
		} else if ( getView( event.target ) ) {
			event.preventDefault();
			event.stopPropagation();
		}
	});

	return {
		getViewText: getViewText,
		setViewText: setViewText,
		getView: getView
	};
});
