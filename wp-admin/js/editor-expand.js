/* global tinymce */

window.wp = window.wp || {};

jQuery( document ).ready( function($) {
	var $window = $( window ),
		$document = $( document ),
		$adminBar = $( '#wpadminbar' ),
		$footer = $( '#wpfooter' ),
		$wrap = $( '#postdivrich' ),
		$contentWrap = $( '#wp-content-wrap' ),
		$tools = $( '#wp-content-editor-tools' ),
		$visualTop = $(),
		$visualEditor = $(),
		$textTop = $( '#ed_toolbar' ),
		$textEditor = $( '#content' ),
		$textEditorClone = $( '<div id="content-textarea-clone"></div>' ),
		$bottom = $( '#post-status-info' ),
		$menuBar = $(),
		$statusBar = $(),
		$sideSortables = $( '#side-sortables' ),
		$postboxContainer = $( '#postbox-container-1' ),
		$postBody = $('#post-body'),
		fullscreen = window.wp.editor && window.wp.editor.fullscreen,
		mceEditor,
		mceBind = function(){},
		mceUnbind = function(){},
		fixedTop = false,
		fixedBottom = false,
		fixedSideTop = false,
		fixedSideBottom = false,
		scrollTimer,
		lastScrollPosition = 0,
		pageYOffsetAtTop = 130,
		pinnedToolsTop = 56,
		sidebarBottom = 20,
		autoresizeMinHeight = 300,
		initialMode = window.getUserSetting( 'editor' ),
		// These are corrected when adjust() runs, except on scrolling if already set.
		heights = {
			windowHeight: 0,
			windowWidth: 0,
			adminBarHeight: 0,
			toolsHeight: 0,
			menuBarHeight: 0,
			visualTopHeight: 0,
			textTopHeight: 0,
			bottomHeight: 0,
			statusBarHeight: 0,
			sideSortablesHeight: 0
		};

	$textEditorClone.insertAfter( $textEditor );

	$textEditorClone.css( {
		'font-family': $textEditor.css( 'font-family' ),
		'font-size': $textEditor.css( 'font-size' ),
		'line-height': $textEditor.css( 'line-height' ),
		'white-space': 'pre-wrap',
		'word-wrap': 'break-word'
	} );

	function getHeights() {
		var windowWidth = $window.width();

		heights = {
			windowHeight: $window.height(),
			windowWidth: windowWidth,
			adminBarHeight: ( windowWidth > 600 ? $adminBar.outerHeight() : 0 ),
			toolsHeight: $tools.outerHeight() || 0,
			menuBarHeight: $menuBar.outerHeight() || 0,
			visualTopHeight: $visualTop.outerHeight() || 0,
			textTopHeight: $textTop.outerHeight() || 0,
			bottomHeight: $bottom.outerHeight() || 0,
			statusBarHeight: $statusBar.outerHeight() || 0,
			sideSortablesHeight: $sideSortables.height() || 0
		};

		// Adjust for hidden
		if ( heights.menuBarHeight < 3 ) {
			heights.menuBarHeight = 0;
		}
	}

	function textEditorKeyup( event ) {
		var VK = jQuery.ui.keyCode,
			key = event.keyCode,
			range = document.createRange(),
			selStart = $textEditor[0].selectionStart,
			selEnd = $textEditor[0].selectionEnd,
			textNode = $textEditorClone[0].firstChild,
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
		editorTop = heights.adminBarHeight + heights.toolsHeight + heights.textTopHeight;
		editorBottom = heights.windowHeight - heights.bottomHeight;

		if ( cursorTop < editorTop && ( key === VK.UP || key === VK.LEFT || key === VK.BACKSPACE ) ) {
			window.scrollTo( window.pageXOffset, cursorTop + window.pageYOffset - editorTop );
		} else if ( cursorBottom > editorBottom ) {
			window.scrollTo( window.pageXOffset, cursorBottom + window.pageYOffset - editorBottom );
		}
	}

	function textEditorResize() {
		if ( ( mceEditor && ! mceEditor.isHidden() ) || ( ! mceEditor && initialMode === 'tinymce' ) ) {
			return;
		}

		var textEditorHeight = $textEditor.height(),
			hiddenHeight;

		$textEditorClone.width( $textEditor.width() - 22 );
		$textEditorClone.text( $textEditor.val() + '&nbsp;' );

		hiddenHeight = $textEditorClone.height();

		if ( hiddenHeight < autoresizeMinHeight ) {
			hiddenHeight = autoresizeMinHeight;
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
		mceEditor = editor;

		// Set the minimum height to the initial viewport height.
		editor.settings.autoresize_min_height = autoresizeMinHeight;

		// Get the necessary UI elements.
		$visualTop = $contentWrap.find( '.mce-toolbar-grp' );
		$visualEditor = $contentWrap.find( '.mce-edit-area' );
		$statusBar = $contentWrap.find( '.mce-statusbar' );
		$menuBar = $contentWrap.find( '.mce-menubar' );

		function mceGetCursorOffset() {
			var node = editor.selection.getNode(),
				view, offset;

			if ( editor.plugins.wpview && ( view = editor.plugins.wpview.getView( node ) ) ) {
				offset = view.getBoundingClientRect();
			} else {
				offset = node.getBoundingClientRect();
			}

			return offset.height ? offset : false;
		}

		// Make sure the cursor is always visible.
		// This is not only necessary to keep the cursor between the toolbars,
		// but also to scroll the window when the cursor moves out of the viewport to a wpview.
		// Setting a buffer > 0 will prevent the browser default.
		// Some browsers will scroll to the middle,
		// others to the top/bottom of the *window* when moving the cursor out of the viewport.
		function mceKeyup( event ) {
			var VK = tinymce.util.VK,
				key = event.keyCode,
				offset = mceGetCursorOffset(),
				buffer = 10,
				cursorTop, cursorBottom, editorTop, editorBottom;

			if ( ! offset ) {
				return;
			}

			// Bail on special keys.
			if ( key <= 47 && ! ( key === VK.SPACEBAR || key === VK.ENTER || key === VK.DELETE || key === VK.BACKSPACE || key === VK.UP || key === VK.LEFT || key === VK.DOWN || key === VK.UP ) ) {
				return;
			// OS keys, function keys, num lock, scroll lock
			} else if ( ( key >= 91 && key <= 93 ) || ( key >= 112 && key <= 123 ) || key === 144 || key === 145 ) {
				return;
			}

			cursorTop = offset.top + editor.iframeElement.getBoundingClientRect().top;
			cursorBottom = cursorTop + offset.height;
			cursorTop = cursorTop - buffer;
			cursorBottom = cursorBottom + buffer;
			editorTop = heights.adminBarHeight + heights.toolsHeight + heights.menuBarHeight + heights.visualTopHeight;
			editorBottom = heights.windowHeight - heights.bottomHeight - heights.statusBarHeight;

			// Don't scroll if the node is taller than the visible part of the editor
			if ( editorBottom - editorTop < offset.height ) {
				return;
			}

			if ( cursorTop < editorTop && ( key === VK.UP || key === VK.LEFT || key === VK.BACKSPACE ) ) {
				window.scrollTo( window.pageXOffset, cursorTop + window.pageYOffset - editorTop );
			} else if ( cursorBottom > editorBottom ) {
				window.scrollTo( window.pageXOffset, cursorBottom + window.pageYOffset - editorBottom );
			}
		}

		// Adjust when switching editor modes.
		function mceShow() {
			setTimeout( function() {
				editor.execCommand( 'wpAutoResize' );
				adjust();
			}, 300 );
		}

		function mceHide() {
			setTimeout( function() {
				var top = $contentWrap.offset().top;

				if ( window.pageYOffset > top ) {
					window.scrollTo( window.pageXOffset, top - heights.adminBarHeight );
				}

				textEditorResize();
				adjust();
			}, 100 );

			adjust();
		}

		mceBind = function() {
			editor.on( 'keyup', mceKeyup );
			editor.on( 'show', mceShow );
			editor.on( 'hide', mceHide );
			// Adjust when the editor resizes.
			editor.on( 'setcontent wp-autoresize wp-toolbar-toggle', adjust );
		};

		mceUnbind = function() {
			editor.off( 'keyup', mceKeyup );
			editor.off( 'show', mceShow );
			editor.off( 'hide', mceHide );
			editor.off( 'setcontent wp-autoresize wp-toolbar-toggle', adjust );
		};

		if ( $wrap.hasClass( 'wp-editor-expand' ) ) {
			// Adjust "immediately"
			mceBind();
			initialResize( adjust );
		}
	} );

	// Adjust the toolbars based on the active editor mode.
	function adjust( type ) {
		// Make sure we're not in fullscreen mode.
		if ( fullscreen && fullscreen.settings.visible ) {
			return;
		}

		var windowPos = $window.scrollTop(),
			resize = type !== 'scroll',
			visual = ( mceEditor && ! mceEditor.isHidden() ),
			buffer = autoresizeMinHeight,
			postBodyTop = $postBody.offset().top,
			borderWidth = 1,
			contentWrapWidth = $contentWrap.width(),
			$top, $editor, sidebarTop, footerTop, canPin,
			topPos, topHeight, editorPos, editorHeight;

		// Refresh the heights
		if ( resize || ! heights.windowHeight ) {
			getHeights();
		}

		if ( ! visual && type === 'resize' ) {
			textEditorResize();
		}

		if ( visual ) {
			$top = $visualTop;
			$editor = $visualEditor;
			topHeight = heights.visualTopHeight;
		} else {
			$top = $textTop;
			$editor = $textEditor;
			topHeight = heights.textTopHeight;
		}

		topPos = $top.parent().offset().top;
		editorPos = $editor.offset().top;
		editorHeight = $editor.outerHeight();

		// Should we pin?
		canPin = visual ? autoresizeMinHeight + topHeight : autoresizeMinHeight + 20; // 20px from textarea padding
		canPin = editorHeight > ( canPin + 5 );

		if ( ! canPin ) {
			if ( resize ) {
				$tools.css( {
					position: 'absolute',
					top: 0,
					width: contentWrapWidth
				} );

				if ( visual && $menuBar.length ) {
					$menuBar.css( {
						position: 'absolute',
						top: 0,
						width: contentWrapWidth - ( borderWidth * 2 )
					} );
				}

				$top.css( {
					position: 'absolute',
					top: heights.menuBarHeight,
					width: contentWrapWidth - ( borderWidth * 2 ) - ( visual ? 0 : ( $top.outerWidth() - $top.width() ) )
				} );

				$statusBar.add( $bottom ).attr( 'style', '' );
			}
		} else {
			// Maybe pin the top.
			if ( ( ! fixedTop || resize ) &&
				// Handle scrolling down.
				( windowPos >= ( topPos - heights.toolsHeight - heights.adminBarHeight ) &&
				// Handle scrolling up.
				windowPos <= ( topPos - heights.toolsHeight - heights.adminBarHeight + editorHeight - buffer ) ) ) {
				fixedTop = true;

				$tools.css( {
					position: 'fixed',
					top: heights.adminBarHeight,
					width: contentWrapWidth
				} );

				if ( visual && $menuBar.length ) {
					$menuBar.css( {
						position: 'fixed',
						top: heights.adminBarHeight + heights.toolsHeight,
						width: contentWrapWidth - ( borderWidth * 2 ) - ( visual ? 0 : ( $top.outerWidth() - $top.width() ) )
					} );
				}

				$top.css( {
					position: 'fixed',
					top: heights.adminBarHeight + heights.toolsHeight + heights.menuBarHeight,
					width: contentWrapWidth - ( borderWidth * 2 ) - ( visual ? 0 : ( $top.outerWidth() - $top.width() ) )
				} );
			// Maybe unpin the top.
			} else if ( fixedTop || resize ) {
				// Handle scrolling up.
				if ( windowPos <= ( topPos - heights.toolsHeight - heights.adminBarHeight ) ) {
					fixedTop = false;

					$tools.css( {
						position: 'absolute',
						top: 0,
						width: contentWrapWidth
					} );

					if ( visual && $menuBar.length ) {
						$menuBar.css( {
							position: 'absolute',
							top: 0,
							width: contentWrapWidth - ( borderWidth * 2 )
						} );
					}

					$top.css( {
						position: 'absolute',
						top: heights.menuBarHeight,
						width: contentWrapWidth - ( borderWidth * 2 ) - ( visual ? 0 : ( $top.outerWidth() - $top.width() ) )
					} );
				// Handle scrolling down.
				} else if ( windowPos >= ( topPos - heights.toolsHeight - heights.adminBarHeight + editorHeight - buffer ) ) {
					fixedTop = false;

					$tools.css( {
						position: 'absolute',
						top: editorHeight - buffer,
						width: contentWrapWidth
					} );

					if ( visual && $menuBar.length ) {
						$menuBar.css( {
							position: 'absolute',
							top: editorHeight - buffer,
							width: contentWrapWidth - ( borderWidth * 2 )
						} );
					}

					$top.css( {
						position: 'absolute',
						top: editorHeight - buffer + heights.menuBarHeight,
						width: contentWrapWidth - ( borderWidth * 2 ) - ( visual ? 0 : ( $top.outerWidth() - $top.width() ) )
					} );
				}
			}

			// Maybe adjust the bottom bar.
			if ( ( ! fixedBottom || resize ) &&
				// +[n] for the border around the .wp-editor-container.
				( windowPos + heights.windowHeight ) <= ( editorPos + editorHeight + heights.bottomHeight + heights.statusBarHeight + borderWidth ) ) {
				fixedBottom = true;

				$statusBar.css( {
					position: 'fixed',
					bottom: heights.bottomHeight,
					width: contentWrapWidth - ( borderWidth * 2 )
				} );

				$bottom.css( {
					position: 'fixed',
					bottom: 0,
					width: contentWrapWidth
				} );
			} else if ( ( fixedBottom || resize ) &&
					( windowPos + heights.windowHeight ) > ( editorPos + editorHeight + heights.bottomHeight + heights.statusBarHeight - borderWidth ) ) {
				fixedBottom = false;

				$statusBar.add( $bottom ).attr( 'style', '' );
			}
		}

		// Sidebar pinning
		if ( $postboxContainer.width() < 300 && heights.windowWidth > 600 && // sidebar position is changed with @media from CSS, make sure it is on the side
			$document.height() > ( $sideSortables.height() + postBodyTop + 120 ) && // the sidebar is not the tallest element
			heights.windowHeight < editorHeight ) { // the editor is taller than the viewport

			if ( ( heights.sideSortablesHeight + pinnedToolsTop + sidebarBottom ) > heights.windowHeight || fixedSideTop || fixedSideBottom ) {
				// Reset when scrolling to the top
				if ( windowPos + pinnedToolsTop <= postBodyTop ) {
					$sideSortables.attr( 'style', '' );
					fixedSideTop = fixedSideBottom = false;
				} else {
					if ( windowPos > lastScrollPosition ) {
						// Scrolling down
						if ( fixedSideTop ) {
							// let it scroll
							fixedSideTop = false;
							sidebarTop = $sideSortables.offset().top - heights.adminBarHeight;
							footerTop = $footer.offset().top;

							// don't get over the footer
							if ( footerTop < sidebarTop + heights.sideSortablesHeight + sidebarBottom ) {
								sidebarTop = footerTop - heights.sideSortablesHeight - 12;
							}

							$sideSortables.css({
								position: 'absolute',
								top: sidebarTop,
								bottom: ''
							});
						} else if ( ! fixedSideBottom && heights.sideSortablesHeight + $sideSortables.offset().top + sidebarBottom < windowPos + heights.windowHeight ) {
							// pin the bottom
							fixedSideBottom = true;

							$sideSortables.css({
								position: 'fixed',
								top: 'auto',
								bottom: sidebarBottom
							});
						}
					} else if ( windowPos < lastScrollPosition ) {
						// Scrolling up
						if ( fixedSideBottom ) {
							// let it scroll
							fixedSideBottom = false;
							sidebarTop = $sideSortables.offset().top - sidebarBottom;
							footerTop = $footer.offset().top;

							// don't get over the footer
							if ( footerTop < sidebarTop + heights.sideSortablesHeight + sidebarBottom ) {
								sidebarTop = footerTop - heights.sideSortablesHeight - 12;
							}

							$sideSortables.css({
								position: 'absolute',
								top: sidebarTop,
								bottom: ''
							});
						} else if ( ! fixedSideTop && $sideSortables.offset().top >= windowPos + pinnedToolsTop ) {
							// pin the top
							fixedSideTop = true;

							$sideSortables.css({
								position: 'fixed',
								top: pinnedToolsTop,
								bottom: ''
							});
						}
					}
				}
			} else {
				// if the sidebar container is smaller than the viewport, then pin/unpin the top when scrolling
				if ( windowPos >= ( postBodyTop - pinnedToolsTop ) ) {

					$sideSortables.css( {
						position: 'fixed',
						top: pinnedToolsTop
					} );
				} else {
					$sideSortables.attr( 'style', '' );
				}

				fixedSideTop = fixedSideBottom = false;
			}

			lastScrollPosition = windowPos;
		} else {
			$sideSortables.attr( 'style', '' );
			fixedSideTop = fixedSideBottom = false;
		}

		if ( resize ) {
			$contentWrap.css( {
				paddingTop: heights.toolsHeight
			} );

			if ( visual ) {
				$visualEditor.css( {
					paddingTop: heights.visualTopHeight + heights.menuBarHeight
				} );
			} else {
				$textEditor.css( {
					marginTop: heights.textTopHeight
				} );

				$textEditorClone.width( contentWrapWidth - 20 - ( borderWidth * 2 ) );
			}
		}
	}

	function fullscreenHide() {
		textEditorResize();
		adjust();
	}

	function initialResize( callback ) {
		for ( var i = 1; i < 6; i++ ) {
			setTimeout( callback, 500 * i );
		}
	}

	function afterScroll() {
		clearTimeout( scrollTimer );
		scrollTimer = setTimeout( adjust, 100 );
	}

	function on() {
		// Scroll to the top when triggering this from JS.
		// Ensures toolbars are pinned properly.
		if ( window.pageYOffset && window.pageYOffset > pageYOffsetAtTop ) {
			window.scrollTo( window.pageXOffset, 0 );
		}

		$wrap.addClass( 'wp-editor-expand' );

		// Adjust when the window is scrolled or resized.
		$window.on( 'scroll.editor-expand resize.editor-expand', function( event ) {
			adjust( event.type );
			afterScroll();
		} );

		// Adjust when collapsing the menu, changing the columns, changing the body class.
		$document.on( 'wp-collapse-menu.editor-expand postboxes-columnchange.editor-expand editor-classchange.editor-expand', adjust )
			.on( 'postbox-toggled.editor-expand', function() {
				if ( ! fixedSideTop && ! fixedSideBottom && window.pageYOffset > pinnedToolsTop ) {
					fixedSideBottom = true;
					window.scrollBy( 0, -1 );
					adjust();
					window.scrollBy( 0, 1 );
				}

				adjust();
			});

		$textEditor.on( 'focus.editor-expand input.editor-expand propertychange.editor-expand', textEditorResize );
		$textEditor.on( 'keyup.editor-expand', textEditorKeyup );
		mceBind();

		// Adjust when entering/exiting fullscreen mode.
		fullscreen && fullscreen.pubsub.subscribe( 'hidden', fullscreenHide );

		if ( mceEditor ) {
			mceEditor.settings.wp_autoresize_on = true;
			mceEditor.execCommand( 'wpAutoResizeOn' );

			if ( ! mceEditor.isHidden() ) {
				mceEditor.execCommand( 'wpAutoResize' );
			}
		}

		if ( ! mceEditor || mceEditor.isHidden() ) {
			textEditorResize();
		}

		adjust();
	}

	function off() {
		var height = window.getUserSetting('ed_size');

		// Scroll to the top when triggering this from JS.
		// Ensures toolbars are reset properly.
		if ( window.pageYOffset && window.pageYOffset > pageYOffsetAtTop ) {
			window.scrollTo( window.pageXOffset, 0 );
		}

		$wrap.removeClass( 'wp-editor-expand' );

		$window.off( '.editor-expand' );
		$document.off( '.editor-expand' );
		$textEditor.off( '.editor-expand' );
		mceUnbind();

		// Adjust when entering/exiting fullscreen mode.
		fullscreen && fullscreen.pubsub.unsubscribe( 'hidden', fullscreenHide );

		// Reset all css
		$.each( [ $visualTop, $textTop, $tools, $menuBar, $bottom, $statusBar, $contentWrap, $visualEditor, $textEditor, $sideSortables ], function( i, element ) {
			element && element.attr( 'style', '' );
		});

		fixedTop = fixedBottom = fixedSideTop = fixedSideBottom = false;

		if ( mceEditor ) {
			mceEditor.settings.wp_autoresize_on = false;
			mceEditor.execCommand( 'wpAutoResizeOff' );

			if ( ! mceEditor.isHidden() ) {
				$textEditor.hide();

				if ( height ) {
					mceEditor.theme.resizeTo( null, height );
				}
			}
		}

		if ( height ) {
			$textEditor.height( height );
		}
	}

	// Start on load
	if ( $wrap.hasClass( 'wp-editor-expand' ) ) {
		on();

		// Ideally we need to resize just after CSS has fully loaded and QuickTags is ready.
		if ( $contentWrap.hasClass( 'html-active' ) ) {
			initialResize( function() {
				adjust();
				textEditorResize();
			} );
		}
	}

	// Show the on/off checkbox
	$( '#adv-settings .editor-expand' ).show();
	$( '#editor-expand-toggle' ).on( 'change.editor-expand', function() {
		if ( $(this).prop( 'checked' ) ) {
			on();
			window.setUserSetting( 'editor_expand', 'on' );
		} else {
			off();
			window.setUserSetting( 'editor_expand', 'off' );
		}
	});

	// Expose on() and off()
	window.editorExpand = {
		on: on,
		off: off
	};
});
