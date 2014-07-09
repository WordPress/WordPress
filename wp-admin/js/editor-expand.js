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

	$textEditor.on( 'keyup', function() {
		var range = document.createRange(),
			start = $textEditor[0].selectionStart,
			end = $textEditor[0].selectionEnd,
			textNode = $textEditorClone[0].firstChild,
			windowHeight = $window.height(),
			offset, cursorTop, cursorBottom, editorTop, editorBottom;

		if ( start && end && start !== end ) {
			return;
		}

		range.setStart( textNode, start );
		range.setEnd( textNode, end + 1 );

		offset = range.getBoundingClientRect();

		if ( ! offset.height ) {
			return;
		}

		cursorTop = offset.top;
		cursorBottom = cursorTop + offset.height;
		editorTop = $adminBar.outerHeight() + $textTop.outerHeight();
		editorBottom = windowHeight - $bottom.outerHeight();

		if ( cursorTop < editorTop || cursorBottom > editorBottom ) {
			window.scrollTo( window.pageXOffset, cursorTop + window.pageYOffset - windowHeight / 2 );
		}
	} );

	function textEditorResize() {
		if ( editorInstance && ! editorInstance.isHidden() ) {
			return;
		}

		var hiddenHeight = $textEditorClone.width( $textEditor.width() ).text( $textEditor.val() + '&nbsp;' ).height(),
			textEditorHeight = $textEditor.height();

		if ( hiddenHeight < 300 ) {
			hiddenHeight = 300;
		}

		if ( hiddenHeight === textEditorHeight ) {
			return;
		}

		$textEditor.height( hiddenHeight );

		adjust( 'resize' );
	}

	// We need to wait for TinyMCE to initialize.
	$document.on( 'tinymce-editor-init.editor-expand', function( event, editor ) {
		// Make sure it's the main editor.
		if ( editor.id !== 'content' ) {
			return;
		}

		// Copy the editor instance.
		editorInstance = editor;

		// Resizing will be handled by the autoresize plugin.
		editor.theme.resizeTo = function() {};

		// Set the minimum height to the initial viewport height.
		editor.settings.autoresize_min_height = 300;

		// Get the necessary UI elements.
		$visualTop = $contentWrap.find( '.mce-toolbar-grp' );
		$visualEditor = $contentWrap.find( '.mce-edit-area' );
		$statusBar = $contentWrap.find( '.mce-statusbar' ).filter( ':visible' );

		// Adjust when switching editor modes.
		editor.on( 'show', function() {
			setTimeout( function() {
				editor.execCommand( 'mceAutoResize' );
				adjust( 'resize' );
			}, 200 );
		} );

		editor.on( 'keyup', function() {
			var offset = getCursorOffset(),
				windowHeight = $window.height(),
				cursorTop, cursorBottom, editorTop, editorBottom;

			if ( ! offset ) {
				return;
			}

			cursorTop = offset.top + editor.getContentAreaContainer().getElementsByTagName( 'iframe' )[0].getBoundingClientRect().top;
			cursorBottom = cursorTop + offset.height;
			editorTop = $adminBar.outerHeight() + $tools.outerHeight() + $visualTop.outerHeight();
			editorBottom = $window.height() - $bottom.outerHeight();

			if ( cursorTop < editorTop || cursorBottom > editorBottom ) {
				window.scrollTo( window.pageXOffset, cursorTop + window.pageYOffset - windowHeight / 2 );
			}
		} );

		function getCursorOffset() {
			var selection = editor.selection,
				node = selection.getNode(),
				range = selection.getRng(),
				view, clone, right, offset;

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
						right = true;
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
				return false;
			}

			return offset;
		}

		editor.on( 'hide', function() {
			textEditorResize();
			adjust( 'resize' );
		} );

		// Adjust when the editor resizes.
		editor.on( 'nodechange setcontent keyup FullscreenStateChanged', function() {
			adjust( 'resize' );
		} );

		editor.on( 'wp-toolbar-toggle', function() {
			$visualEditor.css( {
				paddingTop: $visualTop.outerHeight()
			} );
		} );

		// And adjust "immediately".
		// Allow some time to load CSS etc.
		setTimeout( function() {
			$visualEditor.css( {
				paddingTop: $visualTop.outerHeight()
			} );

			adjust( 'resize' );
		}, 500 );
	} );

	// Adjust when the window is scrolled or resized.
	$window.on( 'scroll resize', function( event ) {
		adjust( event.type );
	} );

	// Adjust when exiting fullscreen mode.
	fullscreen && fullscreen.pubsub.subscribe( 'hidden', function() {
		adjust( 'resize' );
	} );

	// Adjust when collapsing the menu.
	$document.on( 'wp-collapse-menu.editor-expand', function() {
		adjust( 'resize' );
	} )

	// Adjust when changing the columns.
	.on( 'postboxes-columnchange.editor-expand', function() {
		adjust( 'resize' );
	} )

	// Adjust when changing the body class.
	.on( 'editor-classchange.editor-expand', function() {
		adjust( 'resize' );
	} );

	// Adjust the toolbars based on the active editor mode.
	function adjust( eventType ) {
		// Make sure we're not in fullscreen mode.
		if ( fullscreen && fullscreen.settings.visible ) {
			return;
		}

		var bottomHeight = $bottom.outerHeight(),
			windowPos = $window.scrollTop(),
			windowHeight = $window.height(),
			windowWidth = $window.width(),
			adminBarHeight = windowWidth > 600 ? $adminBar.height() : 0,
			$top, $editor, visual,
			toolsHeight, topPos, topHeight, editorPos, editorHeight, editorWidth, statusBarHeight;

		// Visual editor.
		if ( editorInstance && ! editorInstance.isHidden() ) {
			$top = $visualTop;
			$editor = $visualEditor;
			visual = true;

			// Doesn't hide the panel of 'styleselect'. :(
			tinymce.each( editorInstance.controlManager.buttons, function( button ) {
				if ( button._active && ( button.type === 'colorbutton' || button.type === 'panelbutton' || button.type === 'menubutton' ) ) {
					button.hidePanel();
				}
			} );
		// Text editor.
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
		if ( ( ! fixedTop || eventType === 'resize' ) &&
				// Handle scrolling down.
				( windowPos >= ( topPos - toolsHeight - adminBarHeight ) &&
				// Handle scrolling up.
				windowPos <= ( topPos - toolsHeight - adminBarHeight + editorHeight - buffer ) ) ) {
			fixedTop = true;

			$top.css( {
				position: 'fixed',
				top: adminBarHeight + toolsHeight,
				width: editorWidth - ( visual ? 0 : 38 ),
				borderTop: '1px solid #e5e5e5'
			} );

			$tools.css( {
				position: 'fixed',
				top: adminBarHeight,
				width: editorWidth + 2
			} );
		// Maybe unpin the top.
		} else if ( fixedTop || eventType === 'resize' ) {
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
					borderTop: 'none',
					width: $contentWrap.width()
				} );
			// Handle scrolling down.
			} else if ( windowPos >= ( topPos - toolsHeight - adminBarHeight + editorHeight - buffer ) ) {
				fixedTop = false;

				$top.css( {
					position: 'absolute',
					top: window.pageYOffset - $editor.offset().top + adminBarHeight + $tools.outerHeight(),
					borderTop: 'none'
				} );

				$tools.css( {
					position: 'absolute',
					top: window.pageYOffset - $contentWrap.offset().top + adminBarHeight,
					borderTop: 'none',
					width: $contentWrap.width()
				} );
			}
		}

		// Maybe adjust the bottom bar.
		if ( ( ! fixedBottom || eventType === 'resize' ) &&
				// + 1 for the border around the .wp-editor-container.
				( windowPos + windowHeight ) <= ( editorPos + editorHeight + bottomHeight + statusBarHeight + 1 ) ) {
			fixedBottom = true;

			$bottom.css( {
				position: 'fixed',
				bottom: 0,
				width: editorWidth + 2,
				borderTop: '1px solid #dedede'
			} );
		} else if ( fixedBottom &&
				( windowPos + windowHeight ) > ( editorPos + editorHeight + bottomHeight + statusBarHeight - 1 ) ) {
			fixedBottom = false;

			$bottom.css( {
				position: 'relative',
				bottom: 'auto',
				width: '100%',
				borderTop: 'none'
			} );
		}
	}

	textEditorResize();

	$tools.css( {
		position: 'absolute',
		top: 0,
		width: $contentWrap.width()
	} );

	$contentWrap.css( {
		paddingTop: $tools.outerHeight()
	} );

	// This needs to execute after quicktags is ready or a button is added...
	setTimeout( function() {
		$textEditor.css( {
			paddingTop: $textTop.outerHeight() + parseInt( $textEditor.css( 'padding-top' ), 10 )
		} );
	}, 500 );
});
