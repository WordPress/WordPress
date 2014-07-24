/* global tinymce */

window.wp = window.wp || {};

jQuery( document ).ready( function($) {
	var $window = $( window ),
		$document = $( document ),
		$adminBar = $( '#wpadminbar' ),
		$contentWrap = $( '#wp-content-wrap' ),
		$tools = $( '#wp-content-editor-tools' ),
		$visualTop,
		$visualEditor,
		$textTop = $( '#ed_toolbar' ),
		$textEditor = $( '#content' ),
		$textEditorClone = $( '<div id="content-textarea-clone"></div>' ),
		$bottom = $( '#post-status-info' ),
		$statusBar,
		buffer = 200,
		fullscreen = window.wp.editor && window.wp.editor.fullscreen,
		editorInstance,
		fixedTop = false,
		fixedBottom = false;

	$textEditorClone.insertAfter( $textEditor );

	// use to enable/disable
	$contentWrap.addClass( 'wp-editor-expand' );
	$( '#content-resize-handle' ).hide();

	$textEditorClone.css( {
		'font-family': $textEditor.css( 'font-family' ),
		'font-size': $textEditor.css( 'font-size' ),
		'line-height': $textEditor.css( 'line-height' ),
		'padding': $textEditor.css( 'padding' ),
		'padding-top': 37,
		'white-space': 'pre-wrap',
		'word-wrap': 'break-word'
	} );

	$textEditor.on( 'focus input propertychange', function() {
		textEditorResize();
	} );

	$textEditor.on( 'keyup', function( event ) {
		var VK = jQuery.ui.keyCode,
			key = event.keyCode,
			range = document.createRange(),
			selStart = $textEditor[0].selectionStart,
			selEnd = $textEditor[0].selectionEnd,
			textNode = $textEditorClone[0].firstChild,
			windowHeight = $window.height(),
			buffer = 10,
			offset, cursorTop, cursorBottom, editorTop, editorBottom;

		if ( selStart && selEnd && selStart !== selEnd ) {
			return;
		}

		// These are not TinyMCE ranges.
		try {
			range.setStart( textNode, selStart );
			range.setEnd( textNode, selEnd + 1 );
		} catch ( ex ) {}

		offset = range.getBoundingClientRect();

		if ( ! offset.height ) {
			return;
		}

		cursorTop = offset.top - buffer;
		cursorBottom = cursorTop + offset.height + buffer;
		editorTop = $adminBar.outerHeight() + $tools.outerHeight() + $textTop.outerHeight();
		editorBottom = windowHeight - $bottom.outerHeight();

		if ( cursorTop < editorTop && ( key === VK.UP || key === VK.LEFT || key === VK.BACKSPACE ) ) {
			window.scrollTo( window.pageXOffset, cursorTop + window.pageYOffset - editorTop );
		} else if ( cursorBottom > editorBottom ) {
			window.scrollTo( window.pageXOffset, cursorBottom + window.pageYOffset - editorBottom );
		}
	} );

	function textEditorResize() {
		if ( editorInstance && ! editorInstance.isHidden() ) {
			return;
		}

		var textEditorHeight = $textEditor.height(),
			hiddenHeight;

		$textEditorClone.width( $textEditor.width() );
		$textEditorClone.text( $textEditor.val() + '&nbsp;' );

		hiddenHeight = $textEditorClone.height();

		if ( hiddenHeight < 300 ) {
			hiddenHeight = 300;
		}

		if ( hiddenHeight === textEditorHeight ) {
			return;
		}

		$textEditor.height( hiddenHeight );

		adjust();
	}

	// We need to wait for TinyMCE to initialize.
	$document.on( 'tinymce-editor-init.editor-expand', function( event, editor ) {
		// Make sure it's the main editor.
		if ( editor.id !== 'content' ) {
			return;
		}

		// Copy the editor instance.
		editorInstance = editor;

		// Set the minimum height to the initial viewport height.
		editor.settings.autoresize_min_height = 300;

		// Get the necessary UI elements.
		$visualTop = $contentWrap.find( '.mce-toolbar-grp' );
		$visualEditor = $contentWrap.find( '.mce-edit-area' );
		$statusBar = $contentWrap.find( '.mce-statusbar' ).filter( ':visible' );

		// Adjust when switching editor modes.
		editor.on( 'show', function() {
			setTimeout( function() {
				editor.execCommand( 'wpAutoResize' );
				adjust();
			}, 300 );
		} );

		// Make sure the cursor is always visible.
		// This is not only necessary to keep the cursor between the toolbars,
		// but also to scroll the window when the cursor moves out of the viewport to a wpview.
		// Setting a buffer > 0 will prevent the browser default.
		// Some browsers will scroll to the middle,
		// others to the top/bottom of the *window* when moving the cursor out of the viewport.
		editor.on( 'keyup', function( event ) {
			var VK = tinymce.util.VK,
				key = event.keyCode,
				offset = getCursorOffset(),
				windowHeight = $window.height(),
				buffer = 10,
				cursorTop, cursorBottom, editorTop, editorBottom;

			if ( ! offset ) {
				return;
			}

			cursorTop = offset.top + editor.getContentAreaContainer().getElementsByTagName( 'iframe' )[0].getBoundingClientRect().top;
			cursorBottom = cursorTop + offset.height;
			cursorTop = cursorTop - buffer;
			cursorBottom = cursorBottom + buffer;
			editorTop = $adminBar.outerHeight() + $tools.outerHeight() + $visualTop.outerHeight();
			editorBottom = windowHeight - $bottom.outerHeight();

			if ( cursorTop < editorTop && ( key === VK.UP || key === VK.LEFT || key === VK.BACKSPACE ) ) {
				window.scrollTo( window.pageXOffset, cursorTop + window.pageYOffset - editorTop );
			} else if ( cursorBottom > editorBottom ) {
				window.scrollTo( window.pageXOffset, cursorBottom + window.pageYOffset - editorBottom );
			}
		} );

		function getCursorOffset() {
			var selection = editor.selection,
				node = selection.getNode(),
				range = selection.getRng(),
				view, clone, offset;

			if ( tinymce.Env.ie && tinymce.Env.ie < 9 ) {
				return;
			}

			if ( editor.plugins.wpview && ( view = editor.plugins.wpview.getView( node ) ) ) {
				offset = view.getBoundingClientRect();
			} else if ( selection.isCollapsed() ) {
				clone = range.cloneRange();

				if ( clone.startContainer.length > 1 ) {
					if ( clone.startContainer.length > clone.endOffset ) {
						clone.setEnd( clone.startContainer, clone.endOffset + 1 );
					} else {
						clone.setStart( clone.startContainer, clone.endOffset - 1 );
					}

					selection.setRng( clone );
					offset = selection.getRng().getBoundingClientRect();
					selection.setRng( range );
				} else {
					offset = node.getBoundingClientRect();
				}
			} else {
				offset = range.getBoundingClientRect();
			}

			if ( ! offset.height ) {
				return;
			}

			return offset;
		}

		editor.on( 'hide', function() {
			textEditorResize();
			adjust();
		} );

		// Adjust when the editor resizes.
		editor.on( 'setcontent wp-autoresize wp-toolbar-toggle', function() {
			adjust();
		} );

		// And adjust "immediately".
		// Allow some time to load CSS etc.
		initialResize( adjust );
	} );

	// Adjust the toolbars based on the active editor mode.
	function adjust( type ) {
		// Make sure we're not in fullscreen mode.
		if ( fullscreen && fullscreen.settings.visible ) {
			return;
		}

		var bottomHeight = $bottom.outerHeight(),
			windowPos = $window.scrollTop(),
			windowHeight = $window.height(),
			windowWidth = $window.width(),
			adminBarHeight = windowWidth > 600 ? $adminBar.height() : 0,
			resize = type !== 'scroll',
			visual = ( editorInstance && ! editorInstance.isHidden() ),
			$top, $editor,
			toolsHeight, topPos, topHeight, editorPos, editorHeight, editorWidth, statusBarHeight;

		if ( visual ) {
			$top = $visualTop;
			$editor = $visualEditor;
		} else {
			$top = $textTop;
			$editor = $textEditor;
		}

		toolsHeight = $tools.outerHeight();
		topPos = $top.parent().offset().top;
		topHeight = $top.outerHeight();
		editorPos = $editor.offset().top;
		editorHeight = $editor.outerHeight();
		editorWidth = $editor.outerWidth();
		statusBarHeight = visual ? $statusBar.outerHeight() : 0;

		// Maybe pin the top.
		if ( ( ! fixedTop || resize ) &&
				// Handle scrolling down.
				( windowPos >= ( topPos - toolsHeight - adminBarHeight ) &&
				// Handle scrolling up.
				windowPos <= ( topPos - toolsHeight - adminBarHeight + editorHeight - buffer ) ) ) {
			fixedTop = true;

			$top.css( {
				position: 'fixed',
				top: adminBarHeight + toolsHeight,
				width: $editor.parent().width() - ( $top.outerWidth() - $top.width() ),
				borderTop: '1px solid #e5e5e5'
			} );

			$tools.css( {
				position: 'fixed',
				top: adminBarHeight,
				width: editorWidth + 2
			} );
		// Maybe unpin the top.
		} else if ( fixedTop || resize ) {
			// Handle scrolling up.
			if ( windowPos <= ( topPos - toolsHeight -  adminBarHeight ) ) {
				fixedTop = false;

				$top.css( {
					position: 'absolute',
					top: 0,
					borderTop: 'none',
					width: $editor.parent().width() - ( $top.outerWidth() - $top.width() )
				} );

				$tools.css( {
					position: 'absolute',
					top: 0,
					width: $contentWrap.width()
				} );
			// Handle scrolling down.
			} else if ( windowPos >= ( topPos - toolsHeight - adminBarHeight + editorHeight - buffer ) ) {
				fixedTop = false;

				$top.css( {
					position: 'absolute',
					top: editorHeight - buffer
				} );

				$tools.css( {
					position: 'absolute',
					top: editorHeight - buffer + 1, // border
					width: $contentWrap.width()
				} );
			}
		}

		// Maybe adjust the bottom bar.
		if ( ( ! fixedBottom || resize ) &&
				// + 1 for the border around the .wp-editor-container.
				( windowPos + windowHeight ) <= ( editorPos + editorHeight + bottomHeight + statusBarHeight + 1 ) ) {
			fixedBottom = true;

			$bottom.css( {
				position: 'fixed',
				bottom: 0,
				width: editorWidth + 2,
				borderTop: '1px solid #dedede'
			} );
		} else if ( ( fixedBottom || resize ) &&
				( windowPos + windowHeight ) > ( editorPos + editorHeight + bottomHeight + statusBarHeight - 1 ) ) {
			fixedBottom = false;

			$bottom.css( {
				position: 'relative',
				bottom: 'auto',
				width: '100%',
				borderTop: 'none'
			} );
		}

		if ( resize ) {
			$contentWrap.css( {
				paddingTop: $tools.outerHeight()
			} );

			if ( visual ) {
				$visualEditor.css( {
					paddingTop: $visualTop.outerHeight()
				} );
			} else {
				$textEditor.css( {
					marginTop: $textTop.outerHeight()
				} );
				$textEditorClone.width( $textEditor.width() );
			}
		}
	}

	function initialResize( callback ) {
		for ( var i = 1; i < 6; i++ ) {
			setTimeout( callback, 500 * i );
		}
	}

	// Adjust when the window is scrolled or resized.
	$window.on( 'scroll.editor-expand resize.editor-expand', function( event ) {
		adjust( event.type );
	} );

	// Adjust when entering/exiting fullscreen mode.
	fullscreen && fullscreen.pubsub.subscribe( 'hidden', function() {
		textEditorResize();
		adjust();
	} );

	// Adjust when collapsing the menu, changing the columns, changing the body class.
	$document.on( 'wp-collapse-menu.editor-expand postboxes-columnchange.editor-expand editor-classchange.editor-expand', adjust );

	// Ideally we need to resize just after CSS has fully loaded and QuickTags is ready.
	if ( $contentWrap.hasClass( 'html-active' ) ) {
		initialResize( function() {
			adjust();
			textEditorResize();
		} );
	}
});
