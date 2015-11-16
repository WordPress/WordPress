( function( window, $, undefined ) {
	'use strict';

	var $window = $( window ),
		$document = $( document ),
		$adminBar = $( '#wpadminbar' ),
		$footer = $( '#wpfooter' );

	/* Autoresize editor. */
	$( function() {
		var $wrap = $( '#postdivrich' ),
			$contentWrap = $( '#wp-content-wrap' ),
			$tools = $( '#wp-content-editor-tools' ),
			$visualTop = $(),
			$visualEditor = $(),
			$textTop = $( '#ed_toolbar' ),
			$textEditor = $( '#content' ),
			$textEditorClone = $( '<div id="content-textarea-clone" class="wp-exclude-emoji"></div>' ),
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
			initialMode = $contentWrap.hasClass( 'tmce-active' ) ? 'tinymce' : 'html',
			advanced = !! parseInt( window.getUserSetting( 'hidetb' ), 10 ),
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
			var VK = window.tinymce.util.VK,
				hideFloatPanels = _.debounce( function() {
					! $( '.mce-floatpanel:hover' ).length && window.tinymce.ui.FloatPanel.hideAll();
					$( '.mce-tooltip' ).hide();
				}, 1000, true );

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
					range, view, offset;

				if ( editor.wp && editor.wp.getView && ( view = editor.wp.getView( node ) ) ) {
					offset = view.getBoundingClientRect();
				} else {
					range = editor.selection.getRng();

					try {
						offset = range.getClientRects()[0];
					} catch( er ) {}

					if ( ! offset ) {
						offset = node.getBoundingClientRect();
					}
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
				var key = event.keyCode;

				// Bail on special keys.
				if ( key <= 47 && ! ( key === VK.SPACEBAR || key === VK.ENTER || key === VK.DELETE || key === VK.BACKSPACE || key === VK.UP || key === VK.LEFT || key === VK.DOWN || key === VK.UP ) ) {
					return;
				// OS keys, function keys, num lock, scroll lock
				} else if ( ( key >= 91 && key <= 93 ) || ( key >= 112 && key <= 123 ) || key === 144 || key === 145 ) {
					return;
				}

				mceScroll( key );
			}

			function mceScroll( key ) {
				var offset = mceGetCursorOffset(),
					buffer = 50,
					cursorTop, cursorBottom, editorTop, editorBottom;

				if ( ! offset ) {
					return;
				}

				cursorTop = offset.top + editor.iframeElement.getBoundingClientRect().top;
				cursorBottom = cursorTop + offset.height;
				cursorTop = cursorTop - buffer;
				cursorBottom = cursorBottom + buffer;
				editorTop = heights.adminBarHeight + heights.toolsHeight + heights.menuBarHeight + heights.visualTopHeight;
				editorBottom = heights.windowHeight - ( advanced ? heights.bottomHeight + heights.statusBarHeight : 0 );

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

			function mceFullscreenToggled( event ) {
				if ( ! event.state ) {
					adjust();
				}
			}

			// Adjust when switching editor modes.
			function mceShow() {
				$window.on( 'scroll.mce-float-panels', hideFloatPanels );

				setTimeout( function() {
					editor.execCommand( 'wpAutoResize' );
					adjust();
				}, 300 );
			}

			function mceHide() {
				$window.off( 'scroll.mce-float-panels' );

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

			function toggleAdvanced() {
				advanced = ! advanced;
			}

			mceBind = function() {
				editor.on( 'keyup', mceKeyup );
				editor.on( 'show', mceShow );
				editor.on( 'hide', mceHide );
				editor.on( 'wp-toolbar-toggle', toggleAdvanced );
				// Adjust when the editor resizes.
				editor.on( 'setcontent wp-autoresize wp-toolbar-toggle', adjust );
				// Don't hide the caret after undo/redo.
				editor.on( 'undo redo', mceScroll );
				// Adjust when exiting TinyMCE's fullscreen mode.
				editor.on( 'FullscreenStateChanged', mceFullscreenToggled );

				$window.off( 'scroll.mce-float-panels' ).on( 'scroll.mce-float-panels', hideFloatPanels );
			};

			mceUnbind = function() {
				editor.off( 'keyup', mceKeyup );
				editor.off( 'show', mceShow );
				editor.off( 'hide', mceHide );
				editor.off( 'wp-toolbar-toggle', toggleAdvanced );
				editor.off( 'setcontent wp-autoresize wp-toolbar-toggle', adjust );
				editor.off( 'undo redo', mceScroll );
				editor.off( 'FullscreenStateChanged', mceFullscreenToggled );

				$window.off( 'scroll.mce-float-panels' );
			};

			if ( $wrap.hasClass( 'wp-editor-expand' ) ) {
				// Adjust "immediately"
				mceBind();
				initialResize( adjust );
			}
		} );

		// Adjust the toolbars based on the active editor mode.
		function adjust( event ) {
			// Make sure we're not in fullscreen mode.
			if ( fullscreen && fullscreen.settings.visible ) {
				return;
			}

			var windowPos = $window.scrollTop(),
				type = event && event.type,
				resize = type !== 'scroll',
				visual = mceEditor && ! mceEditor.isHidden(),
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

			// TinyMCE still intializing.
			if ( ! visual && ! $top.length ) {
				return;
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

					$statusBar.attr( 'style', advanced ? '' : 'visibility: hidden;' );
					$bottom.attr( 'style', '' );
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
				if ( ( ! fixedBottom || ( resize && advanced ) ) &&
						// +[n] for the border around the .wp-editor-container.
						( windowPos + heights.windowHeight ) <= ( editorPos + editorHeight + heights.bottomHeight + heights.statusBarHeight + borderWidth ) ) {

					if ( event && event.deltaHeight > 0 && event.deltaHeight < 100 ) {
						window.scrollBy( 0, event.deltaHeight );
					} else if ( advanced ) {
						fixedBottom = true;

						$statusBar.css( {
							position: 'fixed',
							bottom: heights.bottomHeight,
							visibility: '',
							width: contentWrapWidth - ( borderWidth * 2 )
						} );

						$bottom.css( {
							position: 'fixed',
							bottom: 0,
							width: contentWrapWidth
						} );
					}
				} else if ( ( ! advanced && fixedBottom ) ||
						( ( fixedBottom || resize ) &&
						( windowPos + heights.windowHeight ) > ( editorPos + editorHeight + heights.bottomHeight + heights.statusBarHeight - borderWidth ) ) ) {
					fixedBottom = false;

					$statusBar.attr( 'style', advanced ? '' : 'visibility: hidden;' );
					$bottom.attr( 'style', '' );
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
				}).on( 'wp-window-resized.editor-expand', function() {
					if ( mceEditor && ! mceEditor.isHidden() ) {
						mceEditor.execCommand( 'wpAutoResize' );
					} else {
						textEditorResize();
					}
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

			$document.trigger( 'editor-expand-on' );
		}

		function off() {
			var height = parseInt( window.getUserSetting( 'ed_size', 300 ), 10 );

			if ( height < 50 ) {
				height = 50;
			} else if ( height > 5000 ) {
				height = 5000;
			}

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

			$document.trigger( 'editor-expand-off' );
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
	} );

	/* DFW. */
	$( function() {
		var $body = $( document.body ),
			$wrap = $( '#wpcontent' ),
			$editor = $( '#post-body-content' ),
			$title = $( '#title' ),
			$content = $( '#content' ),
			$overlay = $( document.createElement( 'DIV' ) ),
			$slug = $( '#edit-slug-box' ),
			$slugFocusEl = $slug.find( 'a' )
				.add( $slug.find( 'button' ) )
				.add( $slug.find( 'input' ) ),
			$menuWrap = $( '#adminmenuwrap' ),
			$editorWindow = $(),
			$editorIframe = $(),
			_isActive = window.getUserSetting( 'editor_expand', 'on' ) === 'on',
			_isOn = _isActive ? window.getUserSetting( 'post_dfw' ) === 'on' : false,
			traveledX = 0,
			traveledY = 0,
			buffer = 20,
			faded, fadedAdminBar, fadedSlug,
			editorRect, x, y, mouseY, scrollY,
			focusLostTimer, overlayTimer, editorHasFocus;

		$body.append( $overlay );

		$overlay.css( {
			display: 'none',
			position: 'fixed',
			top: $adminBar.height(),
			right: 0,
			bottom: 0,
			left: 0,
			'z-index': 9997
		} );

		$editor.css( {
			position: 'relative'
		} );

		$window.on( 'mousemove.focus', function( event ) {
			mouseY = event.pageY;
		} );

		function recalcEditorRect() {
			editorRect = $editor.offset();
			editorRect.right = editorRect.left + $editor.outerWidth();
			editorRect.bottom = editorRect.top + $editor.outerHeight();
		}

		function activate() {
			if ( ! _isActive ) {
				_isActive = true;

				$document.trigger( 'dfw-activate' );
				$content.on( 'keydown.focus-shortcut', toggleViaKeyboard );
			}
		}

		function deactivate() {
			if ( _isActive ) {
				off();

				_isActive = false;

				$document.trigger( 'dfw-deactivate' );
				$content.off( 'keydown.focus-shortcut' );
			}
		}

		function isActive() {
			return _isActive;
		}

		function on() {
			if ( ! _isOn && _isActive ) {
				_isOn = true;

				$content.on( 'keydown.focus', fadeOut );

				$title.add( $content ).on( 'blur.focus', maybeFadeIn );

				fadeOut();

				window.setUserSetting( 'post_dfw', 'on' );

				$document.trigger( 'dfw-on' );
			}
		}

		function off() {
			if ( _isOn ) {
				_isOn = false;

				$title.add( $content ).off( '.focus' );

				fadeIn();

				$editor.off( '.focus' );

				window.setUserSetting( 'post_dfw', 'off' );

				$document.trigger( 'dfw-off' );
			}
		}

		function toggle() {
			if ( _isOn ) {
				off();
			} else {
				on();
			}
		}

		function isOn() {
			return _isOn;
		}

		function fadeOut( event ) {
			var key = event && event.keyCode;

			// fadeIn and return on Escape and keyboard shortcut Alt+Shift+W.
			if ( key === 27 || ( key === 87 && event.altKey && event.shiftKey ) ) {
				fadeIn( event );
				return;
			}

			if ( event && ( event.metaKey || ( event.ctrlKey && ! event.altKey ) || ( event.altKey && event.shiftKey ) || ( key && (
				// Special keys ( tab, ctrl, alt, esc, arrow keys... )
				( key <= 47 && key !== 8 && key !== 13 && key !== 32 && key !== 46 ) ||
				// Windows keys
				( key >= 91 && key <= 93 ) ||
				// F keys
				( key >= 112 && key <= 135 ) ||
				// Num Lock, Scroll Lock, OEM
				( key >= 144 && key <= 150 ) ||
				// OEM or non-printable
				key >= 224
			) ) ) ) {
				return;
			}

			if ( ! faded ) {
				faded = true;

				clearTimeout( overlayTimer );

				overlayTimer = setTimeout( function() {
					$overlay.show();
				}, 600 );

				$editor.css( 'z-index', 9998 );

				$overlay
					// Always recalculate the editor area entering the overlay with the mouse.
					.on( 'mouseenter.focus', function() {
						recalcEditorRect();

						$window.on( 'scroll.focus', function() {
							var nScrollY = window.pageYOffset;

							if ( (
								scrollY && mouseY &&
								scrollY !== nScrollY
							) && (
								mouseY < editorRect.top - buffer ||
								mouseY > editorRect.bottom + buffer
							) ) {
								fadeIn();
							}

							scrollY = nScrollY;
						} );
					} )
					.on( 'mouseleave.focus', function() {
						x = y =  null;
						traveledX = traveledY = 0;

						$window.off( 'scroll.focus' );
					} )
					// Fade in when the mouse moves away form the editor area.
					.on( 'mousemove.focus', function( event ) {
						var nx = event.clientX,
							ny = event.clientY,
							pageYOffset = window.pageYOffset,
							pageXOffset = window.pageXOffset;

						if ( x && y && ( nx !== x || ny !== y ) ) {
							if (
								( ny <= y && ny < editorRect.top - pageYOffset ) ||
								( ny >= y && ny > editorRect.bottom - pageYOffset ) ||
								( nx <= x && nx < editorRect.left - pageXOffset ) ||
								( nx >= x && nx > editorRect.right - pageXOffset )
							) {
								traveledX += Math.abs( x - nx );
								traveledY += Math.abs( y - ny );

								if ( (
									ny <= editorRect.top - buffer - pageYOffset ||
									ny >= editorRect.bottom + buffer - pageYOffset ||
									nx <= editorRect.left - buffer - pageXOffset ||
									nx >= editorRect.right + buffer - pageXOffset
								) && (
									traveledX > 10 ||
									traveledY > 10
								) ) {
									fadeIn();

									x = y =  null;
									traveledX = traveledY = 0;

									return;
								}
							} else {
								traveledX = traveledY = 0;
							}
						}

						x = nx;
						y = ny;
					} )
					// When the overlay is touched, always fade in and cancel the event.
					.on( 'touchstart.focus', function( event ) {
						event.preventDefault();
						fadeIn();
					} );

				$editor.off( 'mouseenter.focus' );

				if ( focusLostTimer ) {
					clearTimeout( focusLostTimer );
					focusLostTimer = null;
				}

				$body.addClass( 'focus-on' ).removeClass( 'focus-off' );
			}

			fadeOutAdminBar();
			fadeOutSlug();
		}

		function fadeIn( event ) {
			if ( faded ) {
				faded = false;

				clearTimeout( overlayTimer );

				overlayTimer = setTimeout( function() {
					$overlay.hide();
				}, 200 );

				$editor.css( 'z-index', '' );

				$overlay.off( 'mouseenter.focus mouseleave.focus mousemove.focus touchstart.focus' );

				/*
				 * When fading in, temporarily watch for refocus and fade back out - helps
				 * with 'accidental' editor exits with the mouse. When fading in and the event
				 * is a key event (Escape or Alt+Shift+W) don't watch for refocus.
				 */
				if ( 'undefined' === typeof event ) {
					$editor.on( 'mouseenter.focus', function() {
						if ( $.contains( $editor.get( 0 ), document.activeElement ) || editorHasFocus ) {
							fadeOut();
						}
					} );
				}

				focusLostTimer = setTimeout( function() {
					focusLostTimer = null;
					$editor.off( 'mouseenter.focus' );
				}, 1000 );

				$body.addClass( 'focus-off' ).removeClass( 'focus-on' );
			}

			fadeInAdminBar();
			fadeInSlug();
		}

		function maybeFadeIn() {
			setTimeout( function() {
				var position = document.activeElement.compareDocumentPosition( $editor.get( 0 ) );

				function hasFocus( $el ) {
					return $.contains( $el.get( 0 ), document.activeElement );
				}

				// The focused node is before or behind the editor area, and not outside the wrap.
				if ( ( position === 2 || position === 4 ) && ( hasFocus( $menuWrap ) || hasFocus( $wrap ) || hasFocus( $footer ) ) ) {
					fadeIn();
				}
			}, 0 );
		}

		function fadeOutAdminBar() {
			if ( ! fadedAdminBar && faded ) {
				fadedAdminBar = true;

				$adminBar
					.on( 'mouseenter.focus', function() {
						$adminBar.addClass( 'focus-off' );
					} )
					.on( 'mouseleave.focus', function() {
						$adminBar.removeClass( 'focus-off' );
					} );
			}
		}

		function fadeInAdminBar() {
			if ( fadedAdminBar ) {
				fadedAdminBar = false;

				$adminBar.off( '.focus' );
			}
		}

		function fadeOutSlug() {
			if ( ! fadedSlug && faded && ! $slug.find( ':focus').length ) {
				fadedSlug = true;

				$slug.stop().fadeTo( 'fast', 0.3 ).on( 'mouseenter.focus', fadeInSlug ).off( 'mouseleave.focus' );

				$slugFocusEl.on( 'focus.focus', fadeInSlug ).off( 'blur.focus' );
			}
		}

		function fadeInSlug() {
			if ( fadedSlug ) {
				fadedSlug = false;

				$slug.stop().fadeTo( 'fast', 1 ).on( 'mouseleave.focus', fadeOutSlug ).off( 'mouseenter.focus' );

				$slugFocusEl.on( 'blur.focus', fadeOutSlug ).off( 'focus.focus' );
			}
		}

		function toggleViaKeyboard( event ) {
			if ( event.altKey && event.shiftKey && 87 === event.keyCode ) {
				toggle();
			}
		}

		if ( $( '#postdivrich' ).hasClass( 'wp-editor-expand' ) ) {
			$content.on( 'keydown.focus-shortcut', toggleViaKeyboard );
		}

		$document.on( 'tinymce-editor-setup.focus', function( event, editor ) {
			editor.addButton( 'dfw', {
				active: _isOn,
				classes: 'wp-dfw btn widget',
				disabled: ! _isActive,
				onclick: toggle,
				onPostRender: function() {
					var button = this;

					$document
					.on( 'dfw-activate.focus', function() {
						button.disabled( false );
					} )
					.on( 'dfw-deactivate.focus', function() {
						button.disabled( true );
					} )
					.on( 'dfw-on.focus', function() {
						button.active( true );
					} )
					.on( 'dfw-off.focus', function() {
						button.active( false );
					} );
				},
				tooltip: 'Distraction-free writing mode',
				shortcut: 'Alt+Shift+W'
			} );

			editor.addCommand( 'wpToggleDFW', toggle );
			editor.addShortcut( 'alt+shift+w', '', 'wpToggleDFW' );
		} );

		$document.on( 'tinymce-editor-init.focus', function( event, editor ) {
			var mceBind, mceUnbind;

			function focus() {
				editorHasFocus = true;
			}

			function blur() {
				editorHasFocus = false;
			}

			if ( editor.id === 'content' ) {
				$editorWindow = $( editor.getWin() );
				$editorIframe = $( editor.getContentAreaContainer() ).find( 'iframe' );

				mceBind = function() {
					editor.on( 'keydown', fadeOut );
					editor.on( 'blur', maybeFadeIn );
					editor.on( 'focus', focus );
					editor.on( 'blur', blur );
					editor.on( 'wp-autoresize', recalcEditorRect );
				};

				mceUnbind = function() {
					editor.off( 'keydown', fadeOut );
					editor.off( 'blur', maybeFadeIn );
					editor.off( 'focus', focus );
					editor.off( 'blur', blur );
					editor.off( 'wp-autoresize', recalcEditorRect );
				};

				if ( _isOn ) {
					mceBind();
				}

				$document.on( 'dfw-on.focus', mceBind ).on( 'dfw-off.focus', mceUnbind );

				// Make sure the body focuses when clicking outside it.
				editor.on( 'click', function( event ) {
					if ( event.target === editor.getDoc().documentElement ) {
						editor.focus();
					}
				} );
			}
		} );

		$document.on( 'quicktags-init', function( event, editor ) {
			var $button;

			if ( editor.settings.buttons && ( ',' + editor.settings.buttons + ',' ).indexOf( ',dfw,' ) !== -1 ) {
				$button = $( '#' + editor.name + '_dfw' );

				$( document )
				.on( 'dfw-activate', function() {
					$button.prop( 'disabled', false );
				} )
				.on( 'dfw-deactivate', function() {
					$button.prop( 'disabled', true );
				} )
				.on( 'dfw-on', function() {
					$button.addClass( 'active' );
				} )
				.on( 'dfw-off', function() {
					$button.removeClass( 'active' );
				} );
			}
		} );

		$document.on( 'editor-expand-on.focus', activate ).on( 'editor-expand-off.focus', deactivate );

		if ( _isOn ) {
			$content.on( 'keydown.focus', fadeOut );

			$title.add( $content ).on( 'blur.focus', maybeFadeIn );
		}

		window.wp = window.wp || {};
		window.wp.editor = window.wp.editor || {};
		window.wp.editor.dfw = {
			activate: activate,
			deactivate: deactivate,
			isActive: isActive,
			on: on,
			off: off,
			toggle: toggle,
			isOn: isOn
		};
	} );
} )( window, window.jQuery );
