/**
 * PressThis App
 *
 */
( function( $, window ) {
	var PressThis = function() {
		var editor, $mediaList, $mediaThumbWrap,
			$window               = $( window ),
			$document             = $( document ),
			saveAlert             = false,
			textarea              = document.createElement( 'textarea' ),
			sidebarIsOpen         = false,
			settings              = window.wpPressThisConfig || {},
			data                  = window.wpPressThisData || {},
			smallestWidth         = 128,
			hasSetFocus           = false,
			catsCache             = [],
			isOffScreen           = 'is-off-screen',
			isHidden              = 'is-hidden',
			offscreenHidden       = isOffScreen + ' ' + isHidden,
			iOS                   = /iPad|iPod|iPhone/.test( window.navigator.userAgent ),
			$textEditor           = $( '#pressthis' ),
			textEditor            = $textEditor[0],
			textEditorMinHeight   = 600,
			textLength            = 0,
			transitionEndEvent    = ( function() {
				var style = document.documentElement.style;

				if ( typeof style.transition !== 'undefined' ) {
					return 'transitionend';
				}

				if ( typeof style.WebkitTransition !== 'undefined' ) {
					return 'webkitTransitionEnd';
				}

				return false;
			}() );

		/* ***************************************************************
		 * HELPER FUNCTIONS
		 *************************************************************** */

		/**
		 * Emulates our PHP __() gettext function, powered by the strings exported in pressThisL10n.
		 *
		 * @param key string Key of the string to be translated, as found in pressThisL10n.
		 * @returns string Original or translated string, or empty string if no key.
		 */
		function __( key ) {
			if ( key && window.pressThisL10n ) {
				return window.pressThisL10n[key] || key;
			}

			return key || '';
		}

		/**
		 * Strips HTML tags
		 *
		 * @param string string Text to have the HTML tags striped out of.
		 * @returns string Stripped text.
		 */
		function stripTags( string ) {
			string = string || '';

			return string
				.replace( /<!--[\s\S]*?(-->|$)/g, '' )
				.replace( /<(script|style)[^>]*>[\s\S]*?(<\/\1>|$)/ig, '' )
				.replace( /<\/?[a-z][\s\S]*?(>|$)/ig, '' );
		}

		/**
		 * Strip HTML tags and convert HTML entities.
		 *
		 * @param text string Text.
		 * @returns string Sanitized text.
		 */
		function sanitizeText( text ) {
			var _text = stripTags( text );

			try {
				textarea.innerHTML = _text;
				_text = stripTags( textarea.value );
			} catch ( er ) {}

			return _text;
		}

		/**
		 * Allow only HTTP or protocol relative URLs.
		 *
		 * @param url string The URL.
		 * @returns string Processed URL.
		 */
		function checkUrl( url ) {
			url = $.trim( url || '' );

			if ( /^(?:https?:)?\/\//.test( url ) ) {
				url = stripTags( url );
				return url.replace( /["\\]+/g, '' );
			}

			return '';
		}

		/**
		 * Show UX spinner
		 */
		function showSpinner() {
			$( '.spinner' ).addClass( 'is-active' );
			$( '.post-actions button' ).attr( 'disabled', 'disabled' );
		}

		/**
		 * Hide UX spinner
		 */
		function hideSpinner() {
			$( '.spinner' ).removeClass( 'is-active' );
			$( '.post-actions button' ).removeAttr( 'disabled' );
		}

		function textEditorResize( reset ) {
			var pageYOffset, height;

			if ( editor && ! editor.isHidden() ) {
 				return;
 			}

			reset = ( reset === 'reset' ) || ( textLength && textLength > textEditor.value.length );
			height = textEditor.style.height;

			if ( reset ) {
				pageYOffset = window.pageYOffset;

				textEditor.style.height = 'auto';
				textEditor.style.height = Math.max( textEditor.scrollHeight, textEditorMinHeight ) + 'px';
				window.scrollTo( window.pageXOffset, pageYOffset );
			} else if ( parseInt( textEditor.style.height, 10 ) < textEditor.scrollHeight ) {
				textEditor.style.height = textEditor.scrollHeight + 'px';
 			}

 			textLength = textEditor.value.length;
 		}

 		function mceGetCursorOffset() {
			if ( ! editor ) {
				return false;
			}

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

		// Make sure the caret is always visible.
		function mceKeyup( event ) {
			var VK = window.tinymce.util.VK,
				key = event.keyCode;

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
			var cursorTop, cursorBottom, editorBottom,
				offset = mceGetCursorOffset(),
				bufferTop = 50,
				bufferBottom = 65,
				VK = window.tinymce.util.VK;

			if ( ! offset ) {
				return;
			}

			cursorTop = offset.top + editor.iframeElement.getBoundingClientRect().top;
			cursorBottom = cursorTop + offset.height;
			cursorTop = cursorTop - bufferTop;
			cursorBottom = cursorBottom + bufferBottom;
			editorBottom = $window.height();

			// Don't scroll if the node is taller than the visible part of the editor
			if ( editorBottom < offset.height ) {
				return;
			}

			if ( cursorTop < 0 && ( key === VK.UP || key === VK.LEFT || key === VK.BACKSPACE ) ) {
				window.scrollTo( window.pageXOffset, cursorTop + window.pageYOffset );
			} else if ( cursorBottom > editorBottom ) {
				window.scrollTo( window.pageXOffset, cursorBottom + window.pageYOffset - editorBottom );
			}
		}

		/**
		 * Replace emoji images with chars and sanitize the text content.
		 */
		function getTitleText() {
			var $element = $( '#title-container' );

			$element.find( 'img.emoji' ).each( function() {
				var $image = $( this );
				$image.replaceWith( $( '<span>' ).text( $image.attr( 'alt' ) ) );
			});

			return sanitizeText( $element.text() );
		}

		/**
		 * Prepare the form data for saving.
		 */
		function prepareFormData() {
			var $form = $( '#pressthis-form' ),
				$input = $( '<input type="hidden" name="post_category[]" value="">' );

			editor && editor.save();

			$( '#post_title' ).val( getTitleText() );

			// Make sure to flush out the tags with tagBox before saving
			if ( window.tagBox ) {
				$( 'div.tagsdiv' ).each( function() {
					window.tagBox.flushTags( this, false, 1 );
				} );
			}

			// Get selected categories
			$( '.categories-select .category' ).each( function( i, element ) {
				var $cat = $( element );

				if ( $cat.hasClass( 'selected' ) ) {
					// Have to append a node as we submit the actual form on preview
					$form.append( $input.clone().val( $cat.attr( 'data-term-id' ) || '' ) );
				}
			});
		}

		/**
		 * Submit the post form via AJAX, and redirect to the proper screen if published vs saved as a draft.
		 *
		 * @param action string publish|draft
		 */
		function submitPost( action ) {
			var data;

			saveAlert = false;
			showSpinner();

			if ( 'publish' === action ) {
				$( '#post_status' ).val( 'publish' );
			}

			prepareFormData();
			data = $( '#pressthis-form' ).serialize();

			$.ajax( {
				type: 'post',
				url: window.ajaxurl,
				data: data
			}).always( function() {
				hideSpinner();
				clearNotices();
				$( '.publish-button' ).removeClass( 'is-saving' );
			}).done( function( response ) {
				if ( ! response.success ) {
					renderError( response.data.errorMessage );
				} else if ( response.data.redirect ) {
					if ( window.opener && ( settings.redirInParent || response.data.force ) ) {
						try {
							window.opener.location.href = response.data.redirect;

							window.setTimeout( function() {
								window.self.close();
							}, 200 );
						} catch( er ) {
							window.location.href = response.data.redirect;
						}
					} else {
						window.location.href = response.data.redirect;
					}
				}
			}).fail( function() {
				renderError( __( 'serverError' ) );
			});
		}

		/**
		 * Inserts the media a user has selected from the presented list inside the editor, as an image or embed, based on type
		 *
		 * @param type string img|embed
		 * @param src string Source URL
		 * @param link string Optional destination link, for images (defaults to src)
		 */
		function insertSelectedMedia( $element ) {
			var src, link, newContent = '';

			src = checkUrl( $element.attr( 'data-wp-src' ) || '' );
			link = checkUrl( data.u );

			if ( $element.hasClass( 'is-image' ) ) {
				if ( ! link ) {
					link = src;
				}

				newContent = '<a href="' + link + '"><img class="alignnone size-full" src="' + src + '" alt="" /></a>';
			} else {
				newContent = '[embed]' + src + '[/embed]';
			}

			if ( editor && ! editor.isHidden() ) {
				if ( ! hasSetFocus ) {
					editor.setContent( '<p>' + newContent + '</p>' + editor.getContent() );
				} else {
					editor.execCommand( 'mceInsertContent', false, newContent );
				}
			} else if ( window.QTags ) {
				window.QTags.insertContent( newContent );
			}
		}

		/**
		 * Save a new user-generated category via AJAX
		 */
		function saveNewCategory() {
			var data,
				name = $( '#new-category' ).val();

			if ( ! name ) {
				return;
			}

			data = {
				action: 'press-this-add-category',
				post_id: $( '#post_ID' ).val() || 0,
				name: name,
				new_cat_nonce: $( '#_ajax_nonce-add-category' ).val() || '',
				parent: $( '#new-category-parent' ).val() || 0
			};

			$.post( window.ajaxurl, data, function( response ) {
				if ( ! response.success ) {
					renderError( response.data.errorMessage );
				} else {
					var $parent, $ul,
						$wrap = $( 'ul.categories-select' );

					$.each( response.data, function( i, newCat ) {
						var $node = $( '<li>' ).append( $( '<div class="category selected" tabindex="0" role="checkbox" aria-checked="true">' )
							.attr( 'data-term-id', newCat.term_id )
							.text( newCat.name ) );

						if ( newCat.parent ) {
							if ( ! $ul || ! $ul.length ) {
								$parent = $wrap.find( 'div[data-term-id="' + newCat.parent + '"]' ).parent();
								$ul = $parent.find( 'ul.children:first' );

								if ( ! $ul.length ) {
									$ul = $( '<ul class="children">' ).appendTo( $parent );
								}
							}

							$ul.prepend( $node );
						} else {
							$wrap.prepend( $node );
						}

						$node.focus();
					} );

					refreshCatsCache();
				}
			} );
		}

		/* ***************************************************************
		 * RENDERING FUNCTIONS
		 *************************************************************** */

		/**
		 * Hide the form letting users enter a URL to be scanned, if a URL was already passed.
		 */
		function renderToolsVisibility() {
			if ( data.hasData ) {
				$( '#scanbar' ).hide();
			}
		}

		/**
		 * Render error notice
		 *
		 * @param msg string Notice/error message
		 * @param error string error|notice CSS class for display
		 */
		function renderNotice( msg, error ) {
			var $alerts = $( '.editor-wrapper div.alerts' ),
				className = error ? 'is-error' : 'is-notice';

			$alerts.append( $( '<p class="alert ' + className + '">' ).text( msg ) );
		}

		/**
		 * Render error notice
		 *
		 * @param msg string Error message
		 */
		function renderError( msg ) {
			renderNotice( msg, true );
		}

		function clearNotices() {
			$( 'div.alerts' ).empty();
		}

		/**
		 * Render notices on page load, if any already
		 */
		function renderStartupNotices() {
			// Render errors sent in the data, if any
			if ( data.errors ) {
				$.each( data.errors, function( i, msg ) {
					renderError( msg );
				} );
			}
		}

		/**
		 * Add an image to the list of found images.
		 */
		function addImg( src, displaySrc, i ) {
			var $element = $mediaThumbWrap.clone().addClass( 'is-image' );

			$element.attr( 'data-wp-src', src ).css( 'background-image', 'url(' + displaySrc + ')' )
				.find( 'span' ).text( __( 'suggestedImgAlt' ).replace( '%d', i + 1 ) );

			$mediaList.append( $element );
		}

		/**
		 * Render the detected images and embed for selection, if any
		 */
		function renderDetectedMedia() {
			var found = 0;

			$mediaList = $( 'ul.media-list' );
			$mediaThumbWrap = $( '<li class="suggested-media-thumbnail" tabindex="0"><span class="screen-reader-text"></span></li>' );

			if ( data._embeds ) {
				$.each( data._embeds, function ( i, src ) {
					var displaySrc = '',
						cssClass = '',
						$element = $mediaThumbWrap.clone().addClass( 'is-embed' );

					src = checkUrl( src );

					if ( src.indexOf( 'youtube.com/' ) > -1 ) {
						displaySrc = 'https://i.ytimg.com/vi/' + src.replace( /.+v=([^&]+).*/, '$1' ) + '/hqdefault.jpg';
						cssClass += ' is-video';
					} else if ( src.indexOf( 'youtu.be/' ) > -1 ) {
						displaySrc = 'https://i.ytimg.com/vi/' + src.replace( /\/([^\/])$/, '$1' ) + '/hqdefault.jpg';
						cssClass += ' is-video';
					} else if ( src.indexOf( 'dailymotion.com' ) > -1 ) {
						displaySrc = src.replace( '/video/', '/thumbnail/video/' );
						cssClass += ' is-video';
					} else if ( src.indexOf( 'soundcloud.com' ) > -1 ) {
						cssClass += ' is-audio';
					} else if ( src.indexOf( 'twitter.com' ) > -1 ) {
						cssClass += ' is-tweet';
					} else {
						cssClass += ' is-video';
					}

					$element.attr( 'data-wp-src', src ).find( 'span' ).text( __( 'suggestedEmbedAlt' ).replace( '%d', i + 1 ) );

					if ( displaySrc ) {
						$element.css( 'background-image', 'url(' + displaySrc + ')' );
					}

					$mediaList.append( $element );
					found++;
				} );
			}

			if ( data._images ) {
				$.each( data._images, function( i, src ) {
					var displaySrc, img = new Image();

					src = checkUrl( src );
					displaySrc = src.replace( /^(http[^\?]+)(\?.*)?$/, '$1' );

					if ( src.indexOf( 'files.wordpress.com/' ) > -1 ) {
						displaySrc = displaySrc.replace( /\?.*$/, '' ) + '?w=' + smallestWidth;
					} else if ( src.indexOf( 'gravatar.com/' ) > -1 ) {
						displaySrc = displaySrc.replace( /\?.*$/, '' ) + '?s=' + smallestWidth;
					} else {
						displaySrc = src;
					}

					img.onload = function() {
						if ( ( img.width && img.width < 256 ) ||
							( img.height && img.height < 128 ) ) {

							return;
						}

						addImg( src, displaySrc, i );
					};

					img.src = src;
					found++;
				} );
			}

			if ( found ) {
				$( '.media-list-container' ).addClass( 'has-media' );
			}
		}

		/* ***************************************************************
		 * MONITORING FUNCTIONS
		 *************************************************************** */

		/**
		 * Interactive navigation behavior for the options modal (post format, tags, categories)
		 */
		function monitorOptionsModal() {
			var $postOptions  = $( '.post-options' ),
				$postOption   = $( '.post-option' ),
				$settingModal = $( '.setting-modal' ),
				$modalClose   = $( '.modal-close' );

			$postOption.on( 'click', function() {
				var index = $( this ).index(),
					$targetSettingModal = $settingModal.eq( index );

				$postOptions.addClass( isOffScreen )
					.one( transitionEndEvent, function() {
						$( this ).addClass( isHidden );
					} );

				$targetSettingModal.removeClass( offscreenHidden )
					.one( transitionEndEvent, function() {
						$( this ).find( '.modal-close' ).focus();
					} );
			} );

			$modalClose.on( 'click', function() {
				var $targetSettingModal = $( this ).parent(),
					index = $targetSettingModal.index();

				$postOptions.removeClass( offscreenHidden );
				$targetSettingModal.addClass( isOffScreen );

				if ( transitionEndEvent ) {
					$targetSettingModal.one( transitionEndEvent, function() {
						$( this ).addClass( isHidden );
						$postOption.eq( index - 1 ).focus();
					} );
				} else {
					setTimeout( function() {
						$targetSettingModal.addClass( isHidden );
						$postOption.eq( index - 1 ).focus();
					}, 350 );
				}
			} );
		}

		/**
		 * Interactive behavior for the sidebar toggle, to show the options modals
		 */
		function openSidebar() {
			sidebarIsOpen = true;

			$( '.options' ).removeClass( 'closed' ).addClass( 'open' );
			$( '.press-this-actions, #scanbar' ).addClass( isHidden );
			$( '.options-panel-back' ).removeClass( isHidden );

			$( '.options-panel' ).removeClass( offscreenHidden )
				.one( transitionEndEvent, function() {
					$( '.post-option:first' ).focus();
				} );
		}

		function closeSidebar() {
			sidebarIsOpen = false;

			$( '.options' ).removeClass( 'open' ).addClass( 'closed' );
			$( '.options-panel-back' ).addClass( isHidden );
			$( '.press-this-actions, #scanbar' ).removeClass( isHidden );

			$( '.options-panel' ).addClass( isOffScreen )
				.one( transitionEndEvent, function() {
					$( this ).addClass( isHidden );
					// Reset to options list
					$( '.post-options' ).removeClass( offscreenHidden );
					$( '.setting-modal').addClass( offscreenHidden );
				});
		}

		/**
		 * Interactive behavior for the post title's field placeholder
		 */
		function monitorPlaceholder() {
			var $titleField = $( '#title-container' ),
				$placeholder = $( '.post-title-placeholder' );

			$titleField.on( 'focus', function() {
				$placeholder.addClass( 'is-hidden' );
			}).on( 'blur', function() {
				if ( ! $titleField.text() && ! $titleField.html() ) {
					$placeholder.removeClass( 'is-hidden' );
				}
			}).on( 'keyup', function() {
				saveAlert = true;
			}).on( 'paste', function( event ) {
				var text, range,
					clipboard = event.originalEvent.clipboardData || window.clipboardData;

				if ( clipboard ) {
					try{
						text = clipboard.getData( 'Text' ) || clipboard.getData( 'text/plain' );

						if ( text ) {
							text = $.trim( text.replace( /\s+/g, ' ' ) );

							if ( window.getSelection ) {
								range = window.getSelection().getRangeAt(0);

								if ( range ) {
									if ( ! range.collapsed ) {
										range.deleteContents();
									}

									range.insertNode( document.createTextNode( text ) );
								}
							} else if ( document.selection ) {
								range = document.selection.createRange();

								if ( range ) {
									range.text = text;
								}
							}
						}
					} catch ( er ) {}

					event.preventDefault();
				}

				saveAlert = true;

				setTimeout( function() {
					$titleField.text( getTitleText() );
				}, 50 );
			});

			if ( $titleField.text() || $titleField.html() ) {
				$placeholder.addClass('is-hidden');
			}
		}

		function toggleCatItem( $element ) {
			if ( $element.hasClass( 'selected' ) ) {
				$element.removeClass( 'selected' ).attr( 'aria-checked', 'false' );
			} else {
				$element.addClass( 'selected' ).attr( 'aria-checked', 'true' );
			}
		}

		function monitorCatList() {
			$( '.categories-select' ).on( 'click.press-this keydown.press-this', function( event ) {
				var $element = $( event.target );

				if ( $element.is( 'div.category' ) ) {
					if ( event.type === 'keydown' && event.keyCode !== 32 ) {
						return;
					}

					toggleCatItem( $element );
					event.preventDefault();
				}
			});
		}

		function splitButtonClose() {
			$( '.split-button' ).removeClass( 'is-open' );
			$( '.split-button-toggle' ).attr( 'aria-expanded', 'false' );
		}

		/* ***************************************************************
		 * PROCESSING FUNCTIONS
		 *************************************************************** */

		/**
		 * Calls all the rendring related functions to happen on page load
		 */
		function render(){
			// We're on!
			renderToolsVisibility();
			renderDetectedMedia();
			renderStartupNotices();

			if ( window.tagBox ) {
				window.tagBox.init();
			}

			// iOS doesn't fire click events on "standard" elements without this...
			if ( iOS ) {
				$( document.body ).css( 'cursor', 'pointer' );
			}
		}

		/**
		 * Set app events and other state monitoring related code.
		 */
		function monitor() {
			var $splitButton = $( '.split-button' );

			$document.on( 'tinymce-editor-init', function( event, ed ) {
				editor = ed;

				editor.on( 'nodechange', function() {
					hasSetFocus = true;
				});

				editor.on( 'focus', function() {
					splitButtonClose();
				});

				editor.on( 'show', function() {
					setTimeout( function() {
						editor.execCommand( 'wpAutoResize' );
					}, 300 );
				});

				editor.on( 'hide', function() {
					setTimeout( function() {
						textEditorResize( 'reset' );
					}, 100 );
				});

				editor.on( 'keyup', mceKeyup );
				editor.on( 'undo redo', mceScroll );

			}).on( 'click.press-this keypress.press-this', '.suggested-media-thumbnail', function( event ) {
				if ( event.type === 'click' || event.keyCode === 13 ) {
					insertSelectedMedia( $( this ) );
				}
			}).on( 'click.press-this', function( event ) {
				if ( ! $( event.target ).closest( 'button' ).hasClass( 'split-button-toggle' ) ) {
					splitButtonClose();
				}
			});

			// Publish, Draft and Preview buttons
			$( '.post-actions' ).on( 'click.press-this', function( event ) {
				var location,
					$target = $( event.target ),
					$button = $target.closest( 'button' );

				if ( $button.length ) {
					if ( $button.hasClass( 'draft-button' ) ) {
						$( '.publish-button' ).addClass( 'is-saving' );
						submitPost( 'draft' );
					} else if ( $button.hasClass( 'publish-button' ) ) {
						$button.addClass( 'is-saving' );

						if ( window.history.replaceState ) {
							location = window.location.href;
							location += ( location.indexOf( '?' ) !== -1 ) ? '&' : '?';
							location += 'wp-press-this-reload=true';

							window.history.replaceState( null, null, location );
						}

						submitPost( 'publish' );
					} else if ( $button.hasClass( 'preview-button' ) ) {
						prepareFormData();
						window.opener && window.opener.focus();

						$( '#wp-preview' ).val( 'dopreview' );
						$( '#pressthis-form' ).attr( 'target', '_blank' ).submit().attr( 'target', '' );
						$( '#wp-preview' ).val( '' );
					} else if ( $button.hasClass( 'standard-editor-button' ) ) {
						$( '.publish-button' ).addClass( 'is-saving' );
						$( '#pt-force-redirect' ).val( 'true' );
						submitPost( 'draft' );
					} else if ( $button.hasClass( 'split-button-toggle' ) ) {
						if ( $splitButton.hasClass( 'is-open' ) ) {
							$splitButton.removeClass( 'is-open' );
							$button.attr( 'aria-expanded', 'false' );
						} else {
							$splitButton.addClass( 'is-open' );
							$button.attr( 'aria-expanded', 'true' );
						}
					}
				}
			});

			monitorOptionsModal();
			monitorPlaceholder();
			monitorCatList();

			$( '.options' ).on( 'click.press-this', function() {
				if ( $( this ).hasClass( 'open' ) ) {
					closeSidebar();
				} else {
					openSidebar();
				}
			});

			// Close the sidebar when focus moves outside of it.
			$( '.options-panel, .options-panel-back' ).on( 'focusout.press-this', function() {
				setTimeout( function() {
					var node = document.activeElement,
						$node = $( node );

					if ( sidebarIsOpen && node && ! $node.hasClass( 'options-panel-back' ) &&
						( node.nodeName === 'BODY' ||
							( ! $node.closest( '.options-panel' ).length &&
							! $node.closest( '.options' ).length ) ) ) {

						closeSidebar();
					}
				}, 50 );
			});

			$( '#post-formats-select input' ).on( 'change', function() {
				var $this = $( this );

				if ( $this.is( ':checked' ) ) {
					$( '#post-option-post-format' ).text( $( 'label[for="' + $this.attr( 'id' ) + '"]' ).text() || '' );
				}
			} );

			$window.on( 'beforeunload.press-this', function() {
				if ( saveAlert || ( editor && editor.isDirty() ) ) {
					return __( 'saveAlert' );
				}
			} ).on( 'resize.press-this', function() {
				if ( ! editor || editor.isHidden() ) {
					textEditorResize( 'reset' );
				}
			});

			$( 'button.add-cat-toggle' ).on( 'click.press-this', function() {
				var $this = $( this );

				$this.toggleClass( 'is-toggled' );
				$this.attr( 'aria-expanded', 'false' === $this.attr( 'aria-expanded' ) ? 'true' : 'false' );
				$( '.setting-modal .add-category, .categories-search-wrapper' ).toggleClass( 'is-hidden' );
			} );

			$( 'button.add-cat-submit' ).on( 'click.press-this', saveNewCategory );

			$( '.categories-search' ).on( 'keyup.press-this', function() {
				var search = $( this ).val().toLowerCase() || '';

				// Don't search when less thasn 3 extended ASCII chars
				if ( /[\x20-\xFF]+/.test( search ) && search.length < 2 ) {
					return;
				}

				$.each( catsCache, function( i, cat ) {
					cat.node.removeClass( 'is-hidden searched-parent' );
				} );

				if ( search ) {
					$.each( catsCache, function( i, cat ) {
						if ( cat.text.indexOf( search ) === -1 ) {
							cat.node.addClass( 'is-hidden' );
						} else {
							cat.parents.addClass( 'searched-parent' );
						}
					} );
				}
			} );

			$textEditor.on( 'focus.press-this input.press-this propertychange.press-this', textEditorResize );

			return true;
		}

		function refreshCatsCache() {
			$( '.categories-select' ).find( 'li' ).each( function() {
				var $this = $( this );

				catsCache.push( {
					node: $this,
					parents: $this.parents( 'li' ),
					text: $this.children( '.category' ).text().toLowerCase()
				} );
			} );
		}

		// Let's go!
		$document.ready( function() {
			render();
			monitor();
			refreshCatsCache();
		});

		// Expose public methods?
		return {
			renderNotice: renderNotice,
			renderError: renderError
		};
	};

	window.wp = window.wp || {};
	window.wp.pressThis = new PressThis();

}( jQuery, window ));
