window.wp = window.wp || {};

( function( $, wp ) {
	wp.editor = wp.editor || {};

	/**
	 * @summary Utility functions for the editor.
	 *
	 * @since 2.5.0
	 */
	function SwitchEditors() {
		var tinymce, $$,
			exports = {};

		function init() {
			if ( ! tinymce && window.tinymce ) {
				tinymce = window.tinymce;
				$$ = tinymce.$;

				/**
				 * @summary Handles onclick events for the Visual/Text tabs.
				 *
				 * @since 4.3.0
				 *
				 * @returns {void}
				 */
				$$( document ).on( 'click', function( event ) {
					var id, mode,
						target = $$( event.target );

					if ( target.hasClass( 'wp-switch-editor' ) ) {
						id = target.attr( 'data-wp-editor-id' );
						mode = target.hasClass( 'switch-tmce' ) ? 'tmce' : 'html';
						switchEditor( id, mode );
					}
				});
			}
		}

		/**
		 * @summary Returns the height of the editor toolbar(s) in px.
		 *
		 * @since 3.9.0
		 *
		 * @param {Object} editor The TinyMCE editor.
		 * @returns {number} If the height is between 10 and 200 return the height,
		 * else return 30.
		 */
		function getToolbarHeight( editor ) {
			var node = $$( '.mce-toolbar-grp', editor.getContainer() )[0],
				height = node && node.clientHeight;

			if ( height && height > 10 && height < 200 ) {
				return parseInt( height, 10 );
			}

			return 30;
		}

		/**
		 * @summary Switches the editor between Visual and Text mode.
		 *
		 * @since 2.5.0
		 *
		 * @memberof switchEditors
		 *
		 * @param {string} id The id of the editor you want to change the editor mode for. Default: `content`.
		 * @param {string} mode The mode you want to switch to. Default: `toggle`.
		 * @returns {void}
		 */
		function switchEditor( id, mode ) {
			id = id || 'content';
			mode = mode || 'toggle';

			var editorHeight, toolbarHeight, iframe,
				editor = tinymce.get( id ),
				wrap = $$( '#wp-' + id + '-wrap' ),
				$textarea = $$( '#' + id ),
				textarea = $textarea[0];

			if ( 'toggle' === mode ) {
				if ( editor && ! editor.isHidden() ) {
					mode = 'html';
				} else {
					mode = 'tmce';
				}
			}

			if ( 'tmce' === mode || 'tinymce' === mode ) {
				// If the editor is visible we are already in `tinymce` mode.
				if ( editor && ! editor.isHidden() ) {
					return false;
				}

				// Insert closing tags for any open tags in QuickTags.
				if ( typeof( window.QTags ) !== 'undefined' ) {
					window.QTags.closeAllTags( id );
				}

				editorHeight = parseInt( textarea.style.height, 10 ) || 0;

				// Save the selection
				addHTMLBookmarkInTextAreaContent( $textarea, $ );

				if ( editor ) {
					editor.show();

					// No point to resize the iframe in iOS.
					if ( ! tinymce.Env.iOS && editorHeight ) {
						toolbarHeight = getToolbarHeight( editor );
						editorHeight = editorHeight - toolbarHeight + 14;

						// Sane limit for the editor height.
						if ( editorHeight > 50 && editorHeight < 5000 ) {
							editor.theme.resizeTo( null, editorHeight );
						}
					}

					// Restore the selection
					focusHTMLBookmarkInVisualEditor( editor );
				} else {
					/**
					 * TinyMCE is still not loaded. In order to restore the selection
					 * when the editor loads, a `on('init')` event is added, that will
					 * do the restoration.
					 *
					 * To achieve that, the initialization config is cloned and extended
					 * to include the `setup` method, which makes it possible to add the
					 * `on('init')` event.
					 *
					 * Cloning is used to prevent modification of the original init config,
					 * which may cause unwanted side effects.
					 */
					var tinyMCEConfig = $.extend(
						{},
						window.tinyMCEPreInit.mceInit[id],
						{
							setup: function(editor) {
								editor.on('init', function(event) {
									focusHTMLBookmarkInVisualEditor( event.target );
								});
							}
						}
					);

					tinymce.init( tinyMCEConfig );
				}

				wrap.removeClass( 'html-active' ).addClass( 'tmce-active' );
				$textarea.attr( 'aria-hidden', true );
				window.setUserSetting( 'editor', 'tinymce' );

			} else if ( 'html' === mode ) {
				// If the editor is hidden (Quicktags is shown) we don't need to switch.
				if ( editor && editor.isHidden() ) {
					return false;
				}

				var selectionRange = null;
				if ( editor ) {
					// Don't resize the textarea in iOS. The iframe is forced to 100% height there, we shouldn't match it.
					if ( ! tinymce.Env.iOS ) {
						iframe = editor.iframeElement;
						editorHeight = iframe ? parseInt( iframe.style.height, 10 ) : 0;

						if ( editorHeight ) {
							toolbarHeight = getToolbarHeight( editor );
							editorHeight = editorHeight + toolbarHeight - 14;

							// Sane limit for the textarea height.
							if ( editorHeight > 50 && editorHeight < 5000 ) {
								textarea.style.height = editorHeight + 'px';
							}
						}
					}

					selectionRange = findBookmarkedPosition( editor );

					editor.hide();

					if ( selectionRange ) {
						selectTextInTextArea( editor, selectionRange );
					}
				} else {
					// There is probably a JS error on the page. The TinyMCE editor instance doesn't exist. Show the textarea.
					$textarea.css({ 'display': '', 'visibility': '' });
				}

				wrap.removeClass( 'tmce-active' ).addClass( 'html-active' );
				$textarea.attr( 'aria-hidden', false );
				window.setUserSetting( 'editor', 'html' );
			}
		}

		/**
		 * @summary Checks if a cursor is inside an HTML tag.
		 *
		 * In order to prevent breaking HTML tags when selecting text, the cursor
		 * must be moved to either the start or end of the tag.
		 *
		 * This will prevent the selection marker to be inserted in the middle of an HTML tag.
		 *
		 * This function gives information whether the cursor is inside a tag or not, as well as
		 * the tag type, if it is a closing tag and check if the HTML tag is inside a shortcode tag,
		 * e.g. `[caption]<img.../>..`.
		 *
		 * @param {string} content The test content where the cursor is.
		 * @param {number} cursorPosition The cursor position inside the content.
		 *
		 * @returns {(null|Object)} Null if cursor is not in a tag, Object if the cursor is inside a tag.
		 */
		function getContainingTagInfo( content, cursorPosition ) {
			var lastLtPos = content.lastIndexOf( '<', cursorPosition ),
				lastGtPos = content.lastIndexOf( '>', cursorPosition );

			if ( lastLtPos > lastGtPos || content.substr( cursorPosition, 1 ) === '>' ) {
				// find what the tag is
				var tagContent = content.substr( lastLtPos );
				var tagMatch = tagContent.match( /<\s*(\/)?(\w+)/ );
				if ( ! tagMatch ) {
					return null;
				}

				var tagType = tagMatch[ 2 ];
				var closingGt = tagContent.indexOf( '>' );
				var isClosingTag = ! ! tagMatch[ 1 ];
				var shortcodeWrapperInfo = getShortcodeWrapperInfo( content, lastLtPos );

				return {
					ltPos: lastLtPos,
					gtPos: lastLtPos + closingGt + 1, // offset by one to get the position _after_ the character,
					tagType: tagType,
					isClosingTag: isClosingTag,
					shortcodeTagInfo: shortcodeWrapperInfo
				};
			}
			return null;
		}

		/**
		 * @summary Check if a given HTML tag is enclosed in a shortcode tag
		 *
		 * If the cursor is inside a shortcode wrapping tag, e.g. `[caption]` it's better to
		 * move the selection marker to before the short tag.
		 *
		 * For example `[caption]` rewrites/removes anything that's between the `[caption]` tag and the
		 * `<img/>` tag inside.
		 *
		 * 	`[caption]<span>ThisIsGone</span><img .../>[caption]`
		 *
		 * 	Moving the selection to before the short code is better, since it allows to select
		 * 	something, instead of just losing focus and going to the start of the content.
		 *
		 * 	@param {string} content The text content to check against
		 * 	@param {number} cursorPosition 	The cursor position to check from. Usually this is the opening symbol of
		 * 									an HTML tag.
		 *
		 * @return {(null|Object)} 	Null if the oject is not wrapped in a shortcode tag.
		 * 							Information about the wrapping shortcode tag if it's wrapped in one.
		 */
		function getShortcodeWrapperInfo( content, cursorPosition ) {
			if ( content.substr( cursorPosition - 1, 1 ) === ']' ) {
				var shortTagStart = content.lastIndexOf( '[', cursorPosition );
				var shortTagContent = content.substr(shortTagStart, cursorPosition - shortTagStart);
				var shortTag = content.match( /\[\s*(\/)?(\w+)/ );
				var tagType = shortTag[ 2 ];
				var closingGt = shortTagContent.indexOf( '>' );
				var isClosingTag = ! ! shortTag[ 1 ];

				return {
					openingBracket: shortTagStart,
					shortcode: tagType,
					closingBracket: closingGt,
					isClosingTag: isClosingTag
				};
			}

			return null;
		}

		/**
		 * Generate a cursor marker element to be inserted in the content.
		 *
		 * `span` seems to be the least destructive element that can be used.
		 *
		 * Using DomQuery syntax to create it, since it's used as both text and as a DOM element.
		 *
		 * @param {Object} editor The TinyMCE editor instance.
		 * @param {string} content The content to insert into the cusror marker element.
		 */
		function getCursorMarkerSpan( editor, content ) {
			return editor.$( '<span>' ).css( {
						display: 'inline-block',
						width: 0,
						overflow: 'hidden',
						'line-height': 0
					} )
					.html( content ? content : '' );
		}

		/**
		 * @summary Adds text selection markers in the editor textarea.
		 *
		 * Adds selection markers in the content of the editor `textarea`.
		 * The method directly manipulates the `textarea` content, to allow TinyMCE plugins
		 * to run after the markers are added.
		 *
		 * @param {object} $textarea TinyMCE's textarea wrapped as a DomQuery object
		 * @param {object} jQuery A jQuery instance
		 */
		function addHTMLBookmarkInTextAreaContent( $textarea, jQuery ) {
			var textArea = $textarea[ 0 ], // TODO add error checking
				htmlModeCursorStartPosition = textArea.selectionStart,
				htmlModeCursorEndPosition = textArea.selectionEnd;

			var voidElements = [
				'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input',
				'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'
			];

			// check if the cursor is in a tag and if so, adjust it
			var isCursorStartInTag = getContainingTagInfo( textArea.value, htmlModeCursorStartPosition );
			if ( isCursorStartInTag ) {
				/**
				 * Only move to the start of the HTML tag (to select the whole element) if the tag
				 * is part of the voidElements list above.
				 *
				 * This list includes tags that are self-contained and don't need a closing tag, according to the
				 * HTML5 specification.
				 *
				 * This is done in order to make selection of text a bit more consistent when selecting text in
				 * `<p>` tags or such.
				 *
				 * In cases where the tag is not a void element, the cursor is put to the end of the tag,
				 * so it's either between the opening and closing tag elements or after the closing tag.
				 */
				if ( voidElements.indexOf( isCursorStartInTag.tagType ) !== - 1 ) {
					htmlModeCursorStartPosition = isCursorStartInTag.ltPos;
				}
				else {
					htmlModeCursorStartPosition = isCursorStartInTag.gtPos;
				}
			}

			var isCursorEndInTag = getContainingTagInfo( textArea.value, htmlModeCursorEndPosition );
			if ( isCursorEndInTag ) {
				htmlModeCursorEndPosition = isCursorEndInTag.gtPos;
			}

			var mode = htmlModeCursorStartPosition !== htmlModeCursorEndPosition ? 'range' : 'single';

			var selectedText = null;
			var cursorMarkerSkeleton = getCursorMarkerSpan( { $: jQuery }, '&#65279;' );

			if ( mode === 'range' ) {
				var markedText = textArea.value.slice( htmlModeCursorStartPosition, htmlModeCursorEndPosition );

				/**
				 * Since the shortcodes convert the tags in them a bit, we need to mark the tag itself,
				 * and not rely on the cursor marker.
				 *
				 * @see getShortcodeWrapperInfo
				 */
				if ( isCursorStartInTag && isCursorStartInTag.shortcodeTagInfo ) {
					// Get the tag on the cursor start
					var tagEndPosition = isCursorStartInTag.gtPos - isCursorStartInTag.ltPos;
					var tagContent = markedText.slice( 0, tagEndPosition );

					// Check if the tag already has a `class` attribute.
					var classMatch = /class=(['"])([^$1]*?)\1/;

					/**
					 * Add a marker class to the selected tag, to be used later.
					 *
					 * @see focusHTMLBookmarkInVisualEditor
					 */
					if ( tagContent.match( classMatch ) ) {
						tagContent = tagContent.replace( classMatch, 'class=$1$2 mce_SELRES_start_target$1' );
					}
					else {
						tagContent = tagContent.replace( /(<\w+)/, '$1 class="mce_SELRES_start_target" ' );
					}

					// Update the selected text content with the marked tag above
					markedText = [
						tagContent,
						markedText.substr( tagEndPosition )
					].join( '' );
				}

				var bookMarkEnd = cursorMarkerSkeleton.clone()
					.addClass( 'mce_SELRES_end' )[ 0 ].outerHTML;

				/**
				 * A small workaround when selecting just a single HTML tag inside a shortcode.
				 *
				 * This removes the end selection marker, to make sure the HTML tag is the only selected
				 * thing. This prevents the selection to appear like it contains multiple items in it (i.e.
				 * all highlighted blue)
				 */
				if ( isCursorStartInTag && isCursorStartInTag.shortcodeTagInfo && isCursorEndInTag &&
						isCursorStartInTag.ltPos === isCursorEndInTag.ltPos ) {
					bookMarkEnd = '';
				}

				selectedText = [
					markedText,
					bookMarkEnd
				].join( '' );
			}

			textArea.value = [
				textArea.value.slice( 0, htmlModeCursorStartPosition ), // text until the cursor/selection position
				cursorMarkerSkeleton.clone()							// cursor/selection start marker
					.addClass( 'mce_SELRES_start')[0].outerHTML,
				selectedText, 											// selected text with end cursor/position marker
				textArea.value.slice( htmlModeCursorEndPosition )		// text from last cursor/selection position to end
			].join( '' );
		}

		/**
		 * @summary Focus the selection markers in Visual mode.
		 *
		 * The method checks for existing selection markers inside the editor DOM (Visual mode)
		 * and create a selection between the two nodes using the DOM `createRange` selection API
		 *
		 * If there is only a single node, select only the single node through TinyMCE's selection API
		 *
		 * @param {Object} editor TinyMCE editor instance.
		 */
		function focusHTMLBookmarkInVisualEditor( editor ) {
			var startNode = editor.$( '.mce_SELRES_start' ),
				endNode = editor.$( '.mce_SELRES_end' );

			if ( ! startNode.length ) {
				startNode = editor.$( '.mce_SELRES_start_target' );
			}

			if ( startNode.length ) {
				editor.focus();

				if ( ! endNode.length ) {
					editor.selection.select( startNode[ 0 ] );
				} else {
					var selection = editor.getDoc().createRange();

					selection.setStartAfter( startNode[ 0 ] );
					selection.setEndBefore( endNode[ 0 ] );

					editor.selection.setRng( selection );
				}

				scrollVisualModeToStartElement( editor, startNode );
			}

			if ( startNode.hasClass( 'mce_SELRES_start_target' ) ) {
				startNode.removeClass( 'mce_SELRES_start_target' );
			}
			else {
				startNode.remove();
			}
			endNode.remove();
		}

		/**
		 * @summary Scrolls the content to place the selected element in the center of the screen.
		 *
		 * Takes an element, that is usually the selection start element, selected in
		 * `focusHTMLBookmarkInVisualEditor()` and scrolls the screen so the element appears roughly
		 * in the middle of the screen.
		 *
		 * I order to achieve the proper positioning, the editor media bar and toolbar are subtracted
		 * from the window height, to get the proper viewport window, that the user sees.
		 *
		 * @param {Object} editor TinyMCE editor instance.
		 * @param {Object} element HTMLElement that should be scrolled into view.
		 */
		function scrollVisualModeToStartElement( editor, element ) {
			/**
			 * TODO:
			 *  * Decide if we should animate the transition or not ( motion sickness/accessibility )
			 */
			var elementTop = editor.$( element ).offset().top;
			var TinyMCEContentAreaTop = editor.$( editor.getContentAreaContainer() ).offset().top;

			var edTools = $('#wp-content-editor-tools');
			var edToolsHeight = edTools.height();
			var edToolsOffsetTop = edTools.offset().top;

			var toolbarHeight = getToolbarHeight( editor );

			var windowHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

			var selectionPosition = TinyMCEContentAreaTop + elementTop;
			var visibleAreaHeight = windowHeight - ( edToolsHeight + toolbarHeight );

			/**
			 * The minimum scroll height should be to the top of the editor, to offer a consistent
			 * experience.
			 *
			 * In order to find the top of the editor, we calculate the offset of `#wp-content-editor-tools` and
			 * subtracting the height. This gives the scroll position where the top of the editor tools aligns with
			 * the top of the viewport (under the Master Bar)
			 */
			var adjustedScroll = Math.max(selectionPosition - visibleAreaHeight / 2, edToolsOffsetTop - edToolsHeight);


			$( 'body' ).animate( {
				scrollTop: parseInt( adjustedScroll, 10 )
			}, 100 );
		}

		/**
		 * This method was extracted from the `SaveContent` hook in
		 * `wp-includes/js/tinymce/plugins/wordpress/plugin.js`.
		 *
		 * It's needed here, since the method changes the content a bit, which confuses the cursor position.
		 *
		 * @param {Object} event TinyMCE event object.
		 */
		function fixTextAreaContent( event ) {
			// Keep empty paragraphs :(
			event.content = event.content.replace( /<p>(?:<br ?\/?>|\u00a0|\uFEFF| )*<\/p>/g, '<p>&nbsp;</p>' );
		}

		/**
		 * @summary Finds the current selection position in the Visual editor.
		 *
		 * Find the current selection in the Visual editor by inserting marker elements at the start
		 * and end of the selection.
		 *
		 * Uses the standard DOM selection API to achieve that goal.
		 *
		 * Check the notes in the comments in the code below for more information on some gotchas
		 * and why this solution was chosen.
		 *
		 * @param {Object} editor The editor where we must find the selection
		 * @returns {(null|Object)} The selection range position in the editor
		 */
		function findBookmarkedPosition( editor ) {
			// Get the TinyMCE `window` reference, since we need to access the raw selection.
			var TinyMCEWIndow = editor.getWin(),
				selection = TinyMCEWIndow.getSelection();

			if ( selection.rangeCount <= 0 ) {
				// no selection, no need to continue.
				return;
			}

			/**
			 * The ID is used to avoid replacing user generated content, that may coincide with the
			 * format specified below.
			 * @type {string}
			 */
			var selectionID = 'SELRES_' + Math.random();

			/**
			 * Create two marker elements that will be used to mark the start and the end of the range.
			 *
			 * The elements have hardcoded style that makes them invisible. This is done to avoid seeing
			 * random content flickering in the editor when switching between modes.
			 */
			var spanSkeleton = getCursorMarkerSpan(editor, selectionID);

			var startElement = spanSkeleton.clone().addClass('mce_SELRES_start');
			var endElement = spanSkeleton.clone().addClass('mce_SELRES_end');

			/**
			 * Inspired by:
			 * @link https://stackoverflow.com/a/17497803/153310
			 *
			 * Why do it this way and not with TinyMCE's bookmarks?
			 *
			 * TinyMCE's bookmarks are very nice when working with selections and positions, BUT
			 * there is no way to determine the precise position of the bookmark when switching modes, since
			 * TinyMCE does some serialization of the content, to fix things like shortcodes, run plugins, prettify
			 * HTML code and so on. In this process, the bookmark markup gets lost.
			 *
			 * If we decide to hook right after the bookmark is added, we can see where the bookmark is in the raw HTML
			 * in TinyMCE. Unfortunately this state is before the serialization, so any visual markup in the content will
			 * throw off the positioning.
			 *
			 * To avoid this, we insert two custom `span`s that will serve as the markers at the beginning and end of the
			 * selection.
			 *
			 * Why not use TinyMCE's selection API or the DOM API to wrap the contents? Because if we do that, this creates
			 * a new node, which is inserted in the dom. Now this will be fine, if we worked with fixed selections to
			 * full nodes. Unfortunately in our case, the user can select whatever they like, which means that the
			 * selection may start in the middle of one node and end in the middle of a completely different one. If we
			 * wrap the selection in another node, this will create artifacts in the content.
			 *
			 * Using the method below, we insert the custom `span` nodes at the start and at the end of the selection.
			 * This helps us not break the content and also gives us the option to work with multi-node selections without
			 * breaking the markup.
			 */
			var range = selection.getRangeAt( 0 ),
				startNode = range.startContainer,
				startOffset = range.startOffset,
				boundaryRange = range.cloneRange();

			boundaryRange.collapse( false );
			boundaryRange.insertNode( endElement[0] );

			/**
			 * Sometimes the selection starts at the `<img>` tag, which makes the
			 * boundary range `insertNode` insert `startElement` inside the `<img>` tag itself, i.e.:
			 *
			 * `<img><span class="mce_SELRES_start"...>...</span></img>`
			 *
			 * As this is an invalid syntax, it breaks the selection.
			 *
			 * The conditional below checks if `startNode` is a tag that suffer from that and
			 * manually inserts the selection start maker before it.
			 *
			 * In the future this will probably include a list of tags, not just `<img>`, depending on the needs.
			 */
			if ( startNode && startNode.tagName && startNode.tagName.toLowerCase() === 'img' ) {
				editor.$( startNode ).before( startElement[ 0 ] );
			}
			else {
				boundaryRange.setStart( startNode, startOffset );
				boundaryRange.collapse( true );
				boundaryRange.insertNode( startElement[ 0 ] );
			}


			range.setStartAfter( startElement[0] );
			range.setEndBefore( endElement[0] );
			selection.removeAllRanges();
			selection.addRange( range );

			/**
			 * Now the editor's content has the start/end nodes.
			 *
			 * Unfortunately the content goes through some more changes after this step, before it gets inserted
			 * in the `textarea`. This means that we have to do some minor cleanup on our own here.
			 */
			editor.on( 'GetContent', fixTextAreaContent );

			var content = removep( editor.getContent() );

			editor.off( 'GetContent', fixTextAreaContent );

			startElement.remove();
			endElement.remove();

			var startRegex = new RegExp(
				'<span[^>]*\\s*class="mce_SELRES_start"[^>]+>\\s*' + selectionID + '[^<]*<\\/span>'
			);

			var endRegex = new RegExp(
				'<span[^>]*\\s*class="mce_SELRES_end"[^>]+>\\s*' + selectionID + '[^<]*<\\/span>'
			);

			var startMatch = content.match( startRegex );
			var endMatch = content.match( endRegex );
			if ( ! startMatch ) {
				return null;
			}

			return {
				start: startMatch.index,

				// We need to adjust the end position to discard the length of the range start marker
				end: endMatch ? endMatch.index - startMatch[ 0 ].length : null
			};
		}

		/**
		 * @summary Selects text in the TinyMCE `textarea`.
		 *
		 * Selects the text in TinyMCE's textarea that's between `selection.start` and `selection.end`.
		 *
		 * For `selection` parameter:
		 * @see findBookmarkedPosition
		 *
		 * @param {Object} editor TinyMCE's editor instance.
		 * @param {Object} selection Selection data.
		 */
		function selectTextInTextArea( editor, selection ) {
			// only valid in the text area mode and if we have selection
			if ( ! selection ) {
				return;
			}

			var textArea = editor.getElement(),
				start = selection.start,
				end = selection.end || selection.start;

			if ( textArea.focus ) {
				// focus and scroll to the position
				setTimeout( function() {
					if ( textArea.blur ) {
						// defocus before focusing
						textArea.blur();
					}
					textArea.focus();
				}, 100 );

				textArea.focus();
			}

			textArea.setSelectionRange( start, end );
		}

		/**
		 * @summary Replaces <p> tags with two line breaks. "Opposite" of wpautop().
		 *
		 * Replaces <p> tags with two line breaks except where the <p> has attributes.
		 * Unifies whitespace.
		 * Indents <li>, <dt> and <dd> for better readability.
		 *
		 * @since 2.5.0
		 *
		 * @memberof switchEditors
		 *
		 * @param {string} html The content from the editor.
		 * @return {string} The content with stripped paragraph tags.
		 */
		function removep( html ) {
			var blocklist = 'blockquote|ul|ol|li|dl|dt|dd|table|thead|tbody|tfoot|tr|th|td|h[1-6]|fieldset|figure',
				blocklist1 = blocklist + '|div|p',
				blocklist2 = blocklist + '|pre',
				preserve_linebreaks = false,
				preserve_br = false,
				preserve = [];

			if ( ! html ) {
				return '';
			}

			// Protect script and style tags.
			if ( html.indexOf( '<script' ) !== -1 || html.indexOf( '<style' ) !== -1 ) {
				html = html.replace( /<(script|style)[^>]*>[\s\S]*?<\/\1>/g, function( match ) {
					preserve.push( match );
					return '<wp-preserve>';
				} );
			}

			// Protect pre tags.
			if ( html.indexOf( '<pre' ) !== -1 ) {
				preserve_linebreaks = true;
				html = html.replace( /<pre[^>]*>[\s\S]+?<\/pre>/g, function( a ) {
					a = a.replace( /<br ?\/?>(\r\n|\n)?/g, '<wp-line-break>' );
					a = a.replace( /<\/?p( [^>]*)?>(\r\n|\n)?/g, '<wp-line-break>' );
					return a.replace( /\r?\n/g, '<wp-line-break>' );
				});
			}

			// Remove line breaks but keep <br> tags inside image captions.
			if ( html.indexOf( '[caption' ) !== -1 ) {
				preserve_br = true;
				html = html.replace( /\[caption[\s\S]+?\[\/caption\]/g, function( a ) {
					return a.replace( /<br([^>]*)>/g, '<wp-temp-br$1>' ).replace( /[\r\n\t]+/, '' );
				});
			}

			// Normalize white space characters before and after block tags.
			html = html.replace( new RegExp( '\\s*</(' + blocklist1 + ')>\\s*', 'g' ), '</$1>\n' );
			html = html.replace( new RegExp( '\\s*<((?:' + blocklist1 + ')(?: [^>]*)?)>', 'g' ), '\n<$1>' );

			// Mark </p> if it has any attributes.
			html = html.replace( /(<p [^>]+>.*?)<\/p>/g, '$1</p#>' );

			// Preserve the first <p> inside a <div>.
			html = html.replace( /<div( [^>]*)?>\s*<p>/gi, '<div$1>\n\n' );

			// Remove paragraph tags.
			html = html.replace( /\s*<p>/gi, '' );
			html = html.replace( /\s*<\/p>\s*/gi, '\n\n' );

			// Normalize white space chars and remove multiple line breaks.
			html = html.replace( /\n[\s\u00a0]+\n/g, '\n\n' );

			// Replace <br> tags with line breaks.
			html = html.replace( /(\s*)<br ?\/?>\s*/gi, function( match, space ) {
				if ( space && space.indexOf( '\n' ) !== -1 ) {
					return '\n\n';
				}

				return '\n';
			});

			// Fix line breaks around <div>.
			html = html.replace( /\s*<div/g, '\n<div' );
			html = html.replace( /<\/div>\s*/g, '</div>\n' );

			// Fix line breaks around caption shortcodes.
			html = html.replace( /\s*\[caption([^\[]+)\[\/caption\]\s*/gi, '\n\n[caption$1[/caption]\n\n' );
			html = html.replace( /caption\]\n\n+\[caption/g, 'caption]\n\n[caption' );

			// Pad block elements tags with a line break.
			html = html.replace( new RegExp('\\s*<((?:' + blocklist2 + ')(?: [^>]*)?)\\s*>', 'g' ), '\n<$1>' );
			html = html.replace( new RegExp('\\s*</(' + blocklist2 + ')>\\s*', 'g' ), '</$1>\n' );

			// Indent <li>, <dt> and <dd> tags.
			html = html.replace( /<((li|dt|dd)[^>]*)>/g, ' \t<$1>' );

			// Fix line breaks around <select> and <option>.
			if ( html.indexOf( '<option' ) !== -1 ) {
				html = html.replace( /\s*<option/g, '\n<option' );
				html = html.replace( /\s*<\/select>/g, '\n</select>' );
			}

			// Pad <hr> with two line breaks.
			if ( html.indexOf( '<hr' ) !== -1 ) {
				html = html.replace( /\s*<hr( [^>]*)?>\s*/g, '\n\n<hr$1>\n\n' );
			}

			// Remove line breaks in <object> tags.
			if ( html.indexOf( '<object' ) !== -1 ) {
				html = html.replace( /<object[\s\S]+?<\/object>/g, function( a ) {
					return a.replace( /[\r\n]+/g, '' );
				});
			}

			// Unmark special paragraph closing tags.
			html = html.replace( /<\/p#>/g, '</p>\n' );

			// Pad remaining <p> tags whit a line break.
			html = html.replace( /\s*(<p [^>]+>[\s\S]*?<\/p>)/g, '\n$1' );

			// Trim.
			html = html.replace( /^\s+/, '' );
			html = html.replace( /[\s\u00a0]+$/, '' );

			if ( preserve_linebreaks ) {
				html = html.replace( /<wp-line-break>/g, '\n' );
			}

			if ( preserve_br ) {
				html = html.replace( /<wp-temp-br([^>]*)>/g, '<br$1>' );
			}

			// Restore preserved tags.
			if ( preserve.length ) {
				html = html.replace( /<wp-preserve>/g, function() {
					return preserve.shift();
				} );
			}

			return html;
		}

		/**
		 * @summary Replaces two line breaks with a paragraph tag and one line break with a <br>.
		 *
		 * Similar to `wpautop()` in formatting.php.
		 *
		 * @since 2.5.0
		 *
		 * @memberof switchEditors
		 *
		 * @param {string} text The text input.
		 * @returns {string} The formatted text.
		 */
		function autop( text ) {
			var preserve_linebreaks = false,
				preserve_br = false,
				blocklist = 'table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre' +
					'|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section' +
					'|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary';

			// Normalize line breaks.
			text = text.replace( /\r\n|\r/g, '\n' );

			if ( text.indexOf( '\n' ) === -1 ) {
				return text;
			}

			// Remove line breaks from <object>.
			if ( text.indexOf( '<object' ) !== -1 ) {
				text = text.replace( /<object[\s\S]+?<\/object>/g, function( a ) {
					return a.replace( /\n+/g, '' );
				});
			}

			// Remove line breaks from tags.
			text = text.replace( /<[^<>]+>/g, function( a ) {
				return a.replace( /[\n\t ]+/g, ' ' );
			});

			// Preserve line breaks in <pre> and <script> tags.
			if ( text.indexOf( '<pre' ) !== -1 || text.indexOf( '<script' ) !== -1 ) {
				preserve_linebreaks = true;
				text = text.replace( /<(pre|script)[^>]*>[\s\S]*?<\/\1>/g, function( a ) {
					return a.replace( /\n/g, '<wp-line-break>' );
				});
			}

			if ( text.indexOf( '<figcaption' ) !== -1 ) {
				text = text.replace( /\s*(<figcaption[^>]*>)/g, '$1' );
				text = text.replace( /<\/figcaption>\s*/g, '</figcaption>' );
			}

			// Keep <br> tags inside captions.
			if ( text.indexOf( '[caption' ) !== -1 ) {
				preserve_br = true;

				text = text.replace( /\[caption[\s\S]+?\[\/caption\]/g, function( a ) {
					a = a.replace( /<br([^>]*)>/g, '<wp-temp-br$1>' );

					a = a.replace( /<[^<>]+>/g, function( b ) {
						return b.replace( /[\n\t ]+/, ' ' );
					});

					return a.replace( /\s*\n\s*/g, '<wp-temp-br />' );
				});
			}

			text = text + '\n\n';
			text = text.replace( /<br \/>\s*<br \/>/gi, '\n\n' );

			// Pad block tags with two line breaks.
			text = text.replace( new RegExp( '(<(?:' + blocklist + ')(?: [^>]*)?>)', 'gi' ), '\n\n$1' );
			text = text.replace( new RegExp( '(</(?:' + blocklist + ')>)', 'gi' ), '$1\n\n' );
			text = text.replace( /<hr( [^>]*)?>/gi, '<hr$1>\n\n' );

			// Remove white space chars around <option>.
			text = text.replace( /\s*<option/gi, '<option' );
			text = text.replace( /<\/option>\s*/gi, '</option>' );

			// Normalize multiple line breaks and white space chars.
			text = text.replace( /\n\s*\n+/g, '\n\n' );

			// Convert two line breaks to a paragraph.
			text = text.replace( /([\s\S]+?)\n\n/g, '<p>$1</p>\n' );

			// Remove empty paragraphs.
			text = text.replace( /<p>\s*?<\/p>/gi, '');

			// Remove <p> tags that are around block tags.
			text = text.replace( new RegExp( '<p>\\s*(</?(?:' + blocklist + ')(?: [^>]*)?>)\\s*</p>', 'gi' ), '$1' );
			text = text.replace( /<p>(<li.+?)<\/p>/gi, '$1');

			// Fix <p> in blockquotes.
			text = text.replace( /<p>\s*<blockquote([^>]*)>/gi, '<blockquote$1><p>');
			text = text.replace( /<\/blockquote>\s*<\/p>/gi, '</p></blockquote>');

			// Remove <p> tags that are wrapped around block tags.
			text = text.replace( new RegExp( '<p>\\s*(</?(?:' + blocklist + ')(?: [^>]*)?>)', 'gi' ), '$1' );
			text = text.replace( new RegExp( '(</?(?:' + blocklist + ')(?: [^>]*)?>)\\s*</p>', 'gi' ), '$1' );

			text = text.replace( /(<br[^>]*>)\s*\n/gi, '$1' );

			// Add <br> tags.
			text = text.replace( /\s*\n/g, '<br />\n');

			// Remove <br> tags that are around block tags.
			text = text.replace( new RegExp( '(</?(?:' + blocklist + ')[^>]*>)\\s*<br />', 'gi' ), '$1' );
			text = text.replace( /<br \/>(\s*<\/?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)/gi, '$1' );

			// Remove <p> and <br> around captions.
			text = text.replace( /(?:<p>|<br ?\/?>)*\s*\[caption([^\[]+)\[\/caption\]\s*(?:<\/p>|<br ?\/?>)*/gi, '[caption$1[/caption]' );

			// Make sure there is <p> when there is </p> inside block tags that can contain other blocks.
			text = text.replace( /(<(?:div|th|td|form|fieldset|dd)[^>]*>)(.*?)<\/p>/g, function( a, b, c ) {
				if ( c.match( /<p( [^>]*)?>/ ) ) {
					return a;
				}

				return b + '<p>' + c + '</p>';
			});

			// Restore the line breaks in <pre> and <script> tags.
			if ( preserve_linebreaks ) {
				text = text.replace( /<wp-line-break>/g, '\n' );
			}

			// Restore the <br> tags in captions.
			if ( preserve_br ) {
				text = text.replace( /<wp-temp-br([^>]*)>/g, '<br$1>' );
			}

			return text;
		}

		/**
		 * @summary Fires custom jQuery events `beforePreWpautop` and `afterPreWpautop` when jQuery is available.
		 *
		 * @since 2.9.0
		 *
		 * @memberof switchEditors
		 *
		 * @param {String} html The content from the visual editor.
		 * @returns {String} the filtered content.
		 */
		function pre_wpautop( html ) {
			var obj = { o: exports, data: html, unfiltered: html };

			if ( $ ) {
				$( 'body' ).trigger( 'beforePreWpautop', [ obj ] );
			}

			obj.data = removep( obj.data );

			if ( $ ) {
				$( 'body' ).trigger( 'afterPreWpautop', [ obj ] );
			}

			return obj.data;
		}

		/**
		 * @summary Fires custom jQuery events `beforeWpautop` and `afterWpautop` when jQuery is available.
		 *
		 * @since 2.9.0
		 *
		 * @memberof switchEditors
		 *
		 * @param {String} text The content from the text editor.
		 * @returns {String} filtered content.
		 */
		function wpautop( text ) {
			var obj = { o: exports, data: text, unfiltered: text };

			if ( $ ) {
				$( 'body' ).trigger( 'beforeWpautop', [ obj ] );
			}

			obj.data = autop( obj.data );

			if ( $ ) {
				$( 'body' ).trigger( 'afterWpautop', [ obj ] );
			}

			return obj.data;
		}

		if ( $ ) {
			$( document ).ready( init );
		} else if ( document.addEventListener ) {
			document.addEventListener( 'DOMContentLoaded', init, false );
			window.addEventListener( 'load', init, false );
		} else if ( window.attachEvent ) {
			window.attachEvent( 'onload', init );
			document.attachEvent( 'onreadystatechange', function() {
				if ( 'complete' === document.readyState ) {
					init();
				}
			} );
		}

		wp.editor.autop = wpautop;
		wp.editor.removep = pre_wpautop;

		exports = {
			go: switchEditor,
			wpautop: wpautop,
			pre_wpautop: pre_wpautop,
			_wp_Autop: autop,
			_wp_Nop: removep
		};

		return exports;
	}

	/**
	 * @namespace {SwitchEditors} switchEditors
	 * Expose the switch editors to be used globally.
	 */
	window.switchEditors = new SwitchEditors();

	/**
	 * Initialize TinyMCE and/or Quicktags. For use with wp_enqueue_editor() (PHP).
	 *
	 * Intended for use with an existing textarea that will become the Text editor tab.
	 * The editor width will be the width of the textarea container, height will be adjustable.
	 *
	 * Settings for both TinyMCE and Quicktags can be passed on initialization, and are "filtered"
	 * with custom jQuery events on the document element, wp-before-tinymce-init and wp-before-quicktags-init.
	 *
	 * @since 4.8.0
	 *
	 * @param {string} id The HTML id of the textarea that is used for the editor.
	 *                    Has to be jQuery compliant. No brackets, special chars, etc.
	 * @param {object} settings Example:
	 * settings = {
	 *    // See https://www.tinymce.com/docs/configure/integration-and-setup/.
	 *    // Alternatively set to `true` to use the defaults.
	 *    tinymce: {
	 *        setup: function( editor ) {
	 *            console.log( 'Editor initialized', editor );
	 *        }
	 *    }
	 *
	 *    // Alternatively set to `true` to use the defaults.
	 *	  quicktags: {
	 *        buttons: 'strong,em,link'
	 *    }
	 * }
	 */
	wp.editor.initialize = function( id, settings ) {
		var init;
		var defaults;

		if ( ! $ || ! id || ! wp.editor.getDefaultSettings ) {
			return;
		}

		defaults = wp.editor.getDefaultSettings();

		// Initialize TinyMCE by default
		if ( ! settings ) {
			settings = {
				tinymce: true
			};
		}

		// Add wrap and the Visual|Text tabs.
		if ( settings.tinymce && settings.quicktags ) {
			var $textarea = $( '#' + id );

			var $wrap = $( '<div>' ).attr( {
					'class': 'wp-core-ui wp-editor-wrap tmce-active',
					id: 'wp-' + id + '-wrap'
				} );

			var $editorContainer = $( '<div class="wp-editor-container">' );

			var $button = $( '<button>' ).attr( {
					type: 'button',
					'data-wp-editor-id': id
				} );

			var $editorTools = $( '<div class="wp-editor-tools">' );

			if ( settings.mediaButtons ) {
				var buttonText = 'Add Media';

				if ( window._wpMediaViewsL10n && window._wpMediaViewsL10n.addMedia ) {
					buttonText = window._wpMediaViewsL10n.addMedia;
				}

				var $addMediaButton = $( '<button type="button" class="button insert-media add_media">' );

				$addMediaButton.append( '<span class="wp-media-buttons-icon"></span>' );
				$addMediaButton.append( document.createTextNode( ' ' + buttonText ) );
				$addMediaButton.data( 'editor', id );

				$editorTools.append(
					$( '<div class="wp-media-buttons">' )
						.append( $addMediaButton )
				);
			}

			$wrap.append(
				$editorTools
					.append( $( '<div class="wp-editor-tabs">' )
						.append( $button.clone().attr({
							id: id + '-tmce',
							'class': 'wp-switch-editor switch-tmce'
						}).text( window.tinymce.translate( 'Visual' ) ) )
						.append( $button.attr({
							id: id + '-html',
							'class': 'wp-switch-editor switch-html'
						}).text( window.tinymce.translate( 'Text' ) ) )
					).append( $editorContainer )
			);

			$textarea.after( $wrap );
			$editorContainer.append( $textarea );
		}

		if ( window.tinymce && settings.tinymce ) {
			if ( typeof settings.tinymce !== 'object' ) {
				settings.tinymce = {};
			}

			init = $.extend( {}, defaults.tinymce, settings.tinymce );
			init.selector = '#' + id;

			$( document ).trigger( 'wp-before-tinymce-init', init );
			window.tinymce.init( init );

			if ( ! window.wpActiveEditor ) {
				window.wpActiveEditor = id;
			}
		}

		if ( window.quicktags && settings.quicktags ) {
			if ( typeof settings.quicktags !== 'object' ) {
				settings.quicktags = {};
			}

			init = $.extend( {}, defaults.quicktags, settings.quicktags );
			init.id = id;

			$( document ).trigger( 'wp-before-quicktags-init', init );
			window.quicktags( init );

			if ( ! window.wpActiveEditor ) {
				window.wpActiveEditor = init.id;
			}
		}
	};

	/**
	 * Remove one editor instance.
	 *
	 * Intended for use with editors that were initialized with wp.editor.initialize().
	 *
	 * @since 4.8.0
	 *
	 * @param {string} id The HTML id of the editor textarea.
	 */
	wp.editor.remove = function( id ) {
		var mceInstance, qtInstance,
			$wrap = $( '#wp-' + id + '-wrap' );

		if ( window.tinymce ) {
			mceInstance = window.tinymce.get( id );

			if ( mceInstance ) {
				if ( ! mceInstance.isHidden() ) {
					mceInstance.save();
				}

				mceInstance.remove();
			}
		}

		if ( window.quicktags ) {
			qtInstance = window.QTags.getInstance( id );

			if ( qtInstance ) {
				qtInstance.remove();
			}
		}

		if ( $wrap.length ) {
			$wrap.after( $( '#' + id ) );
			$wrap.remove();
		}
	};

	/**
	 * Get the editor content.
	 *
	 * Intended for use with editors that were initialized with wp.editor.initialize().
	 *
	 * @since 4.8.0
	 *
	 * @param {string} id The HTML id of the editor textarea.
	 * @return The editor content.
	 */
	wp.editor.getContent = function( id ) {
		var editor;

		if ( ! $ || ! id ) {
			return;
		}

		if ( window.tinymce ) {
			editor = window.tinymce.get( id );

			if ( editor && ! editor.isHidden() ) {
				editor.save();
			}
		}

		return $( '#' + id ).val();
	};

}( window.jQuery, window.wp ));
