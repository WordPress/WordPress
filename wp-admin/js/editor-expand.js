/**
 * @output wp-admin/js/editor-expand.js
 */

( function( window, $, undefined ) {
	'use strict';

	var $window = $( window ),
		$document = $( document ),
		$adminBar = $( '#wpadminbar' ),
		$footer = $( '#wpfooter' );

	/**
	 * Handles the resizing of the editor.
	 *
	 * @since 4.0.0
	 *
	 * @return {void}
	 */
	$( function() {
		var $wrap = $( '#postdivrich' ),
			$contentWrap = $( '#wp-content-wrap' ),
			$tools = $( '#wp-content-editor-tools' ),
			$visualTop = $(),
			$visualEditor = $(),
			$textTop = $( '#ed_toolbar' ),
			$textEditor = $( '#content' ),
			textEditor = $textEditor[0],
			oldTextLength = 0,
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

		/**
		 * Resizes textarea based on scroll height and width.
		 *
		 * Doesn't shrink the editor size below the 300px auto resize minimum height.
		 *
		 * @since 4.6.1
		 *
		 * @return {void}
		 */
		var shrinkTextarea = window._.throttle( function() {
			var x = window.scrollX || document.documentElement.scrollLeft;
			var y = window.scrollY || document.documentElement.scrollTop;
			var height = parseInt( textEditor.style.height, 10 );

			textEditor.style.height = autoresizeMinHeight + 'px';

			if ( textEditor.scrollHeight > autoresizeMinHeight ) {
				textEditor.style.height = textEditor.scrollHeight + 'px';
			}

			if ( typeof x !== 'undefined' ) {
				window.scrollTo( x, y );
			}

			if ( textEditor.scrollHeight < height ) {
				adjust();
			}
		}, 300 );

		/**
		 * Resizes the text editor depending on the old text length.
		 *
		 * If there is an mceEditor and it is hidden, it resizes the editor depending
		 * on the old text length. If the current length of the text is smaller than
		 * the old text length, it shrinks the text area. Otherwise it resizes the editor to
		 * the scroll height.
		 *
		 * @since 4.6.1
		 *
		 * @return {void}
		 */
		function textEditorResize() {
			var length = textEditor.value.length;

			if ( mceEditor && ! mceEditor.isHidden() ) {
				return;
			}

			if ( ! mceEditor && initialMode === 'tinymce' ) {
				return;
			}

			if ( length < oldTextLength ) {
				shrinkTextarea();
			} else if ( parseInt( textEditor.style.height, 10 ) < textEditor.scrollHeight ) {
				textEditor.style.height = Math.ceil( textEditor.scrollHeight ) + 'px';
				adjust();
			}

			oldTextLength = length;
		}

		/**
		 * Gets the height and widths of elements.
		 *
		 * Gets the heights of the window, the adminbar, the tools, the menu,
		 * the visualTop, the textTop, the bottom, the statusbar and sideSortables
		 * and stores these in the heights object. Defaults to 0.
		 * Gets the width of the window and stores this in the heights object.
		 *
		 * @since 4.0.0
		 *
		 * @return {void}
		 */
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

			// Adjust for hidden menubar.
			if ( heights.menuBarHeight < 3 ) {
				heights.menuBarHeight = 0;
			}
		}

		// We need to wait for TinyMCE to initialize.
		/**
		 * Binds all necessary functions for editor expand to the editor when the editor
		 * is initialized.
		 *
		 * @since 4.0.0
		 *
		 * @param {event} event The TinyMCE editor init event.
		 * @param {object} editor The editor to bind the vents on.
		 *
		 * @return {void}
		 */
		$document.on( 'tinymce-editor-init.editor-expand', function( event, editor ) {
			// VK contains the type of key pressed. VK = virtual keyboard.
			var VK = window.tinymce.util.VK,
				/**
				 * Hides any float panel with a hover state. Additionally hides tooltips.
				 *
				 * @return {void}
				 */
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

			/**
			 * Gets the offset of the editor.
			 *
			 * @return {Number|Boolean} Returns the offset of the editor
			 * or false if there is no offset height.
			 */
			function mceGetCursorOffset() {
				var node = editor.selection.getNode(),
					range, view, offset;

				/*
				 * If editor.wp.getView and the selection node from the editor selection
				 * are defined, use this as a view for the offset.
				 */
				if ( editor.wp && editor.wp.getView && ( view = editor.wp.getView( node ) ) ) {
					offset = view.getBoundingClientRect();
				} else {
					range = editor.selection.getRng();

					// Try to get the offset from a range.
					try {
						offset = range.getClientRects()[0];
					} catch( er ) {}

					// Get the offset from the bounding client rectangle of the node.
					if ( ! offset ) {
						offset = node.getBoundingClientRect();
					}
				}

				return offset.height ? offset : false;
			}

			/**
			 * Filters the special keys that should not be used for scrolling.
			 *
			 * @since 4.0.0
			 *
			 * @param {event} event The event to get the key code from.
			 *
			 * @return {void}
			 */
			function mceKeyup( event ) {
				var key = event.keyCode;

				// Bail on special keys. Key code 47 is a '/'.
				if ( key <= 47 && ! ( key === VK.SPACEBAR || key === VK.ENTER || key === VK.DELETE || key === VK.BACKSPACE || key === VK.UP || key === VK.LEFT || key === VK.DOWN || key === VK.UP ) ) {
					return;
				// OS keys, function keys, num lock, scroll lock. Key code 91-93 are OS keys.
				// Key code 112-123 are F1 to F12. Key code 144 is num lock. Key code 145 is scroll lock.
				} else if ( ( key >= 91 && key <= 93 ) || ( key >= 112 && key <= 123 ) || key === 144 || key === 145 ) {
					return;
				}

				mceScroll( key );
			}

			/**
			 * Makes sure the cursor is always visible in the editor.
			 *
			 * Makes sure the cursor is kept between the toolbars of the editor and scrolls
			 * the window when the cursor moves out of the viewport to a wpview.
			 * Setting a buffer > 0 will prevent the browser default.
			 * Some browsers will scroll to the middle,
			 * others to the top/bottom of the *window* when moving the cursor out of the viewport.
			 *
			 * @since 4.1.0
			 *
			 * @param {string} key The key code of the pressed key.
			 *
			 * @return {void}
			 */
			function mceScroll( key ) {
				var offset = mceGetCursorOffset(),
					buffer = 50,
					cursorTop, cursorBottom, editorTop, editorBottom;

				// Don't scroll if there is no offset.
				if ( ! offset ) {
					return;
				}

				// Determine the cursorTop based on the offset and the top of the editor iframe.
				cursorTop = offset.top + editor.iframeElement.getBoundingClientRect().top;

				// Determine the cursorBottom based on the cursorTop and offset height.
				cursorBottom = cursorTop + offset.height;

				// Subtract the buffer from the cursorTop.
				cursorTop = cursorTop - buffer;

				// Add the buffer to the cursorBottom.
				cursorBottom = cursorBottom + buffer;
				editorTop = heights.adminBarHeight + heights.toolsHeight + heights.menuBarHeight + heights.visualTopHeight;

				/*
				 * Set the editorBottom based on the window Height, and add the bottomHeight and statusBarHeight if the
				 * advanced editor is enabled.
				 */
				editorBottom = heights.windowHeight - ( advanced ? heights.bottomHeight + heights.statusBarHeight : 0 );

				// Don't scroll if the node is taller than the visible part of the editor.
				if ( editorBottom - editorTop < offset.height ) {
					return;
				}

				/*
				 * If the cursorTop is smaller than the editorTop and the up, left
				 * or backspace key is pressed, scroll the editor to the position defined
				 * by the cursorTop, pageYOffset and editorTop.
				 */
				if ( cursorTop < editorTop && ( key === VK.UP || key === VK.LEFT || key === VK.BACKSPACE ) ) {
					window.scrollTo( window.pageXOffset, cursorTop + window.pageYOffset - editorTop );

				/*
				 * If any other key is pressed or the cursorTop is bigger than the editorTop,
				 * scroll the editor to the position defined by the cursorBottom,
				 * pageYOffset and editorBottom.
				 */
				} else if ( cursorBottom > editorBottom ) {
					window.scrollTo( window.pageXOffset, cursorBottom + window.pageYOffset - editorBottom );
				}
			}

			/**
			 * If the editor is fullscreen, calls adjust.
			 *
			 * @since 4.1.0
			 *
			 * @param {event} event The FullscreenStateChanged event.
			 *
			 * @return {void}
			 */
			function mceFullscreenToggled( event ) {
				// event.state is true if the editor is fullscreen.
				if ( ! event.state ) {
					adjust();
				}
			}

			/**
			 * Shows the editor when scrolled.
			 *
			 * Binds the hideFloatPanels function on the window scroll.mce-float-panels event.
			 * Executes the wpAutoResize on the active editor.
			 *
			 * @since 4.0.0
			 *
			 * @return {void}
			 */
			function mceShow() {
				$window.on( 'scroll.mce-float-panels', hideFloatPanels );

				setTimeout( function() {
					editor.execCommand( 'wpAutoResize' );
					adjust();
				}, 300 );
			}

			/**
			 * Resizes the editor.
			 *
			 * Removes all functions from the window scroll.mce-float-panels event.
			 * Resizes the text editor and scrolls to a position based on the pageXOffset and adminBarHeight.
			 *
			 * @since 4.0.0
			 *
			 * @return {void}
			 */
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

			/**
			 * Toggles advanced states.
			 *
			 * @since 4.1.0
			 *
			 * @return {void}
			 */
			function toggleAdvanced() {
				advanced = ! advanced;
			}

			/**
			 * Binds events of the editor and window.
			 *
			 * @since 4.0.0
			 *
			 * @return {void}
			 */
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

			/**
			 * Unbinds the events of the editor and window.
			 *
			 * @since 4.0.0
			 *
			 * @return {void}
			 */
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

				// Adjust "immediately".
				mceBind();
				initialResize( adjust );
			}
		} );

		/**
		 * Adjusts the toolbars heights and positions.
		 *
		 * Adjusts the toolbars heights and positions based on the scroll position on
		 * the page, the active editor mode and the heights of the editor, admin bar and
		 * side bar.
		 *
		 * @since 4.0.0
		 *
		 * @param {event} event The event that calls this function.
		 *
		 * @return {void}
		 */
		function adjust( event ) {

			// Makes sure we're not in fullscreen mode.
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

			/*
			 * Refresh the heights if type isn't 'scroll'
			 * or heights.windowHeight isn't set.
			 */
			if ( resize || ! heights.windowHeight ) {
				getHeights();
			}

			// Resize on resize event when the editor is in text mode.
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

			// Return if TinyMCE is still initializing.
			if ( ! visual && ! $top.length ) {
				return;
			}

			topPos = $top.parent().offset().top;
			editorPos = $editor.offset().top;
			editorHeight = $editor.outerHeight();

			/*
			 * If in visual mode, checks if the editorHeight is greater than the autoresizeMinHeight + topHeight.
			 * If not in visual mode, checks if the editorHeight is greater than the autoresizeMinHeight + 20.
			 */
			canPin = visual ? autoresizeMinHeight + topHeight : autoresizeMinHeight + 20; // 20px from textarea padding.
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
				// Check if the top is not already in a fixed position.
				if ( ( ! fixedTop || resize ) &&
					( windowPos >= ( topPos - heights.toolsHeight - heights.adminBarHeight ) &&
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
					// Check if the top is already in a fixed position.
				} else if ( fixedTop || resize ) {
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

				// Check if the bottom is not already in a fixed position.
				if ( ( ! fixedBottom || ( resize && advanced ) ) &&
						// Add borderWidth for the border around the .wp-editor-container.
						( windowPos + heights.windowHeight ) <= ( editorPos + editorHeight + heights.bottomHeight + heights.statusBarHeight + borderWidth ) ) {

					if ( event && event.deltaHeight > 0 && event.deltaHeight < 100 ) {
						window.scrollBy( 0, event.deltaHeight );
					} else if ( visual && advanced ) {
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

			// The postbox container is positioned with @media from CSS. Ensure it is pinned on the side.
			if ( $postboxContainer.width() < 300 && heights.windowWidth > 600 &&

				// Check if the sidebar is not taller than the document height.
				$document.height() > ( $sideSortables.height() + postBodyTop + 120 ) &&

				// Check if the editor is taller than the viewport.
				heights.windowHeight < editorHeight ) {

				if ( ( heights.sideSortablesHeight + pinnedToolsTop + sidebarBottom ) > heights.windowHeight || fixedSideTop || fixedSideBottom ) {

					// Reset the sideSortables style when scrolling to the top.
					if ( windowPos + pinnedToolsTop <= postBodyTop ) {
						$sideSortables.attr( 'style', '' );
						fixedSideTop = fixedSideBottom = false;
					} else {

						// When scrolling down.
						if ( windowPos > lastScrollPosition ) {
							if ( fixedSideTop ) {

								// Let it scroll.
								fixedSideTop = false;
								sidebarTop = $sideSortables.offset().top - heights.adminBarHeight;
								footerTop = $footer.offset().top;

								// Don't get over the footer.
								if ( footerTop < sidebarTop + heights.sideSortablesHeight + sidebarBottom ) {
									sidebarTop = footerTop - heights.sideSortablesHeight - 12;
								}

								$sideSortables.css({
									position: 'absolute',
									top: sidebarTop,
									bottom: ''
								});
							} else if ( ! fixedSideBottom && heights.sideSortablesHeight + $sideSortables.offset().top + sidebarBottom < windowPos + heights.windowHeight ) {
								// Pin the bottom.
								fixedSideBottom = true;

								$sideSortables.css({
									position: 'fixed',
									top: 'auto',
									bottom: sidebarBottom
								});
							}

						// When scrolling up.
						} else if ( windowPos < lastScrollPosition ) {
							if ( fixedSideBottom ) {
								// Let it scroll.
								fixedSideBottom = false;
								sidebarTop = $sideSortables.offset().top - sidebarBottom;
								footerTop = $footer.offset().top;

								// Don't get over the footer.
								if ( footerTop < sidebarTop + heights.sideSortablesHeight + sidebarBottom ) {
									sidebarTop = footerTop - heights.sideSortablesHeight - 12;
								}

								$sideSortables.css({
									position: 'absolute',
									top: sidebarTop,
									bottom: ''
								});
							} else if ( ! fixedSideTop && $sideSortables.offset().top >= windowPos + pinnedToolsTop ) {
								// Pin the top.
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
					// If the sidebar container is smaller than the viewport, then pin/unpin the top when scrolling.
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
				}
			}
		}

		/**
		 * Resizes the editor and adjusts the toolbars.
		 *
		 * @since 4.0.0
		 *
		 * @return {void}
		 */
		function fullscreenHide() {
			textEditorResize();
			adjust();
		}

		/**
		 * Runs the passed function with 500ms intervals.
		 *
		 * @since 4.0.0
		 *
		 * @param {function} callback The function to run in the timeout.
		 *
		 * @return {void}
		 */
		function initialResize( callback ) {
			for ( var i = 1; i < 6; i++ ) {
				setTimeout( callback, 500 * i );
			}
		}

		/**
		 * Runs adjust after 100ms.
		 *
		 * @since 4.0.0
		 *
		 * @return {void}
		 */
		function afterScroll() {
			clearTimeout( scrollTimer );
			scrollTimer = setTimeout( adjust, 100 );
		}

		/**
		 * Binds editor expand events on elements.
		 *
		 * @since 4.0.0
		 *
		 * @return {void}
		 */
		function on() {
			/*
			 * Scroll to the top when triggering this from JS.
			 * Ensure the toolbars are pinned properly.
			 */
			if ( window.pageYOffset && window.pageYOffset > pageYOffsetAtTop ) {
				window.scrollTo( window.pageXOffset, 0 );
			}

			$wrap.addClass( 'wp-editor-expand' );

			// Adjust when the window is scrolled or resized.
			$window.on( 'scroll.editor-expand resize.editor-expand', function( event ) {
				adjust( event.type );
				afterScroll();
			} );

			/*
		 	 * Adjust when collapsing the menu, changing the columns
		 	 * or changing the body class.
			 */
			$document.on( 'wp-collapse-menu.editor-expand postboxes-columnchange.editor-expand editor-classchange.editor-expand', adjust )
				.on( 'postbox-toggled.editor-expand postbox-moved.editor-expand', function() {
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
			mceBind();

			// Adjust when entering or exiting fullscreen mode.
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

		/**
		 * Unbinds editor expand events.
		 *
		 * @since 4.0.0
		 *
		 * @return {void}
		 */
		function off() {
			var height = parseInt( window.getUserSetting( 'ed_size', 300 ), 10 );

			if ( height < 50 ) {
				height = 50;
			} else if ( height > 5000 ) {
				height = 5000;
			}

			/*
			 * Scroll to the top when triggering this from JS.
			 * Ensure the toolbars are reset properly.
			 */
			if ( window.pageYOffset && window.pageYOffset > pageYOffsetAtTop ) {
				window.scrollTo( window.pageXOffset, 0 );
			}

			$wrap.removeClass( 'wp-editor-expand' );

			$window.off( '.editor-expand' );
			$document.off( '.editor-expand' );
			$textEditor.off( '.editor-expand' );
			mceUnbind();

			// Adjust when entering or exiting fullscreen mode.
			fullscreen && fullscreen.pubsub.unsubscribe( 'hidden', fullscreenHide );

			// Reset all CSS.
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

			// If there is a height found in the user setting.
			if ( height ) {
				$textEditor.height( height );
			}

			$document.trigger( 'editor-expand-off' );
		}

		// Start on load.
		if ( $wrap.hasClass( 'wp-editor-expand' ) ) {
			on();

			// Resize just after CSS has fully loaded and QuickTags is ready.
			if ( $contentWrap.hasClass( 'html-active' ) ) {
				initialResize( function() {
					adjust();
					textEditorResize();
				} );
			}
		}

		// Show the on/off checkbox.
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

		// Expose on() and off().
		window.editorExpand = {
			on: on,
			off: off
		};
	} );

	/**
	 * Handles the distraction free writing of TinyMCE.
	 *
	 * @since 4.1.0
	 *
	 * @return {void}
	 */
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

		/**
		 * Recalculates the bottom and right position of the editor in the DOM.
		 *
		 * @since 4.1.0
		 *
		 * @return {void}
		 */
		function recalcEditorRect() {
			editorRect = $editor.offset();
			editorRect.right = editorRect.left + $editor.outerWidth();
			editorRect.bottom = editorRect.top + $editor.outerHeight();
		}

		/**
		 * Activates the distraction free writing mode.
		 *
		 * @since 4.1.0
		 *
		 * @return {void}
		 */
		function activate() {
			if ( ! _isActive ) {
				_isActive = true;

				$document.trigger( 'dfw-activate' );
				$content.on( 'keydown.focus-shortcut', toggleViaKeyboard );
			}
		}

		/**
		 * Deactivates the distraction free writing mode.
		 *
		 * @since 4.1.0
		 *
		 * @return {void}
		 */
		function deactivate() {
			if ( _isActive ) {
				off();

				_isActive = false;

				$document.trigger( 'dfw-deactivate' );
				$content.off( 'keydown.focus-shortcut' );
			}
		}

		/**
		 * Returns _isActive.
		 *
		 * @since 4.1.0
		 *
		 * @return {boolean} Returns true is _isActive is true.
		 */
		function isActive() {
			return _isActive;
		}

		/**
		 * Binds events on the editor for distraction free writing.
		 *
		 * @since 4.1.0
		 *
		 * @return {void}
		 */
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

		/**
		 * Unbinds events on the editor for distraction free writing.
		 *
		 * @since 4.1.0
		 *
		 * @return {void}
		 */
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

		/**
		 * Binds or unbinds the editor expand events.
		 *
		 * @since 4.1.0
		 *
		 * @return {void}
		 */
		function toggle() {
			if ( _isOn ) {
				off();
			} else {
				on();
			}
		}

		/**
		 * Returns the value of _isOn.
		 *
		 * @since 4.1.0
		 *
		 * @return {boolean} Returns true if _isOn is true.
		 */
		function isOn() {
			return _isOn;
		}

		/**
		 * Fades out all elements except for the editor.
		 *
		 * The fading is done based on key presses and mouse movements.
		 * Also calls the fadeIn on certain key presses
		 * or if the mouse leaves the editor.
		 *
		 * @since 4.1.0
		 *
		 * @param event The event that triggers this function.
		 *
		 * @return {void}
		 */
		function fadeOut( event ) {
			var isMac,
				key = event && event.keyCode;

			if ( window.navigator.platform ) {
				isMac = ( window.navigator.platform.indexOf( 'Mac' ) > -1 );
			}

			// Fade in and returns on Escape and keyboard shortcut Alt+Shift+W and Ctrl+Opt+W.
			if ( key === 27 || ( key === 87 && event.altKey && ( ( ! isMac && event.shiftKey ) || ( isMac && event.ctrlKey ) ) ) ) {
				fadeIn( event );
				return;
			}

			// Return if any of the following keys or combinations of keys is pressed.
			if ( event && ( event.metaKey || ( event.ctrlKey && ! event.altKey ) || ( event.altKey && event.shiftKey ) || ( key && (
				// Special keys ( tab, ctrl, alt, esc, arrow keys... ).
				( key <= 47 && key !== 8 && key !== 13 && key !== 32 && key !== 46 ) ||
				// Windows keys.
				( key >= 91 && key <= 93 ) ||
				// F keys.
				( key >= 112 && key <= 135 ) ||
				// Num Lock, Scroll Lock, OEM.
				( key >= 144 && key <= 150 ) ||
				// OEM or non-printable.
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
					// Always recalculate the editor area when entering the overlay with the mouse.
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

					// When the overlay is touched, fade in and cancel the event.
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

		/**
		 * Fades all elements back in.
		 *
		 * @since 4.1.0
		 *
		 * @param event The event that triggers this function.
		 *
		 * @return {void}
		 */
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

		/**
		 * Fades in if the focused element based on it position.
		 *
		 * @since 4.1.0
		 *
		 * @return {void}
		 */
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

		/**
		 * Fades out the admin bar based on focus on the admin bar.
		 *
		 * @since 4.1.0
		 *
		 * @return {void}
		 */
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

		/**
		 * Fades in the admin bar.
		 *
		 * @since 4.1.0
		 *
		 * @return {void}
		 */
		function fadeInAdminBar() {
			if ( fadedAdminBar ) {
				fadedAdminBar = false;

				$adminBar.off( '.focus' );
			}
		}

		/**
		 * Fades out the edit slug box.
		 *
		 * @since 4.1.0
		 *
		 * @return {void}
		 */
		function fadeOutSlug() {
			if ( ! fadedSlug && faded && ! $slug.find( ':focus').length ) {
				fadedSlug = true;

				$slug.stop().fadeTo( 'fast', 0.3 ).on( 'mouseenter.focus', fadeInSlug ).off( 'mouseleave.focus' );

				$slugFocusEl.on( 'focus.focus', fadeInSlug ).off( 'blur.focus' );
			}
		}

		/**
		 * Fades in the edit slug box.
		 *
		 * @since 4.1.0
		 *
		 * @return {void}
		 */
		function fadeInSlug() {
			if ( fadedSlug ) {
				fadedSlug = false;

				$slug.stop().fadeTo( 'fast', 1 ).on( 'mouseleave.focus', fadeOutSlug ).off( 'mouseenter.focus' );

				$slugFocusEl.on( 'blur.focus', fadeOutSlug ).off( 'focus.focus' );
			}
		}

		/**
		 * Triggers the toggle on Alt + Shift + W.
		 *
		 * Keycode 87 = w.
		 *
		 * @since 4.1.0
		 *
		 * @param {event} event The event to trigger the toggle.
		 *
		 * @return {void}
		 */
		function toggleViaKeyboard( event ) {
			if ( event.altKey && event.shiftKey && 87 === event.keyCode ) {
				toggle();
			}
		}

		if ( $( '#postdivrich' ).hasClass( 'wp-editor-expand' ) ) {
			$content.on( 'keydown.focus-shortcut', toggleViaKeyboard );
		}

		/**
		 * Adds the distraction free writing button when setting up TinyMCE.
		 *
		 * @since 4.1.0
		 *
		 * @param {event} event The TinyMCE editor setup event.
		 * @param {object} editor The editor to add the button to.
		 *
		 * @return {void}
		 */
		$document.on( 'tinymce-editor-setup.focus', function( event, editor ) {
			editor.addButton( 'dfw', {
				active: _isOn,
				classes: 'wp-dfw btn widget',
				disabled: ! _isActive,
				onclick: toggle,
				onPostRender: function() {
					var button = this;

					editor.on( 'init', function() {
						if ( button.disabled() ) {
							button.hide();
						}
					} );

					$document
					.on( 'dfw-activate.focus', function() {
						button.disabled( false );
						button.show();
					} )
					.on( 'dfw-deactivate.focus', function() {
						button.disabled( true );
						button.hide();
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
			editor.addShortcut( 'access+w', '', 'wpToggleDFW' );
		} );

		/**
		 * Binds and unbinds events on the editor.
		 *
		 * @since 4.1.0
		 *
		 * @param {event} event The TinyMCE editor init event.
		 * @param {object} editor The editor to bind events on.
		 *
		 * @return {void}
		 */
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

				// Bind and unbind based on the distraction free writing focus.
				$document.on( 'dfw-on.focus', mceBind ).on( 'dfw-off.focus', mceUnbind );

				// Focuse the editor when it is the target of the click event.
				editor.on( 'click', function( event ) {
					if ( event.target === editor.getDoc().documentElement ) {
						editor.focus();
					}
				} );
			}
		} );

		/**
		 *  Binds events on quicktags init.
		 *
		 * @since 4.1.0
		 *
		 * @param {event} event The quicktags init event.
		 * @param {object} editor The editor to bind events on.
		 *
		 * @return {void}
		 */
		$document.on( 'quicktags-init', function( event, editor ) {
			var $button;

			// Bind the distraction free writing events if the distraction free writing button is available.
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
