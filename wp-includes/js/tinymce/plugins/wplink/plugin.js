( function( tinymce ) {
	tinymce.ui.Factory.add( 'WPLinkPreview', tinymce.ui.Control.extend( {
		url: '#',
		renderHtml: function() {
			return (
				'<div id="' + this._id + '" class="wp-link-preview">' +
					'<a href="' + this.url + '" target="_blank" rel="noopener" tabindex="-1">' + this.url + '</a>' +
				'</div>'
			);
		},
		setURL: function( url ) {
			var index, lastIndex;

			if ( this.url !== url ) {
				this.url = url;

				url = window.decodeURIComponent( url );

				url = url.replace( /^(?:https?:)?\/\/(?:www\.)?/, '' );

				if ( ( index = url.indexOf( '?' ) ) !== -1 ) {
					url = url.slice( 0, index );
				}

				if ( ( index = url.indexOf( '#' ) ) !== -1 ) {
					url = url.slice( 0, index );
				}

				url = url.replace( /(?:index)?\.html$/, '' );

				if ( url.charAt( url.length - 1 ) === '/' ) {
					url = url.slice( 0, -1 );
				}

				// If nothing's left (maybe the URL was just a fragment), use the whole URL.
				if ( url === '' ) {
					url = this.url;
				}

				// If the URL is longer that 40 chars, concatenate the beginning (after the domain) and ending with '...'.
				if ( url.length > 40 && ( index = url.indexOf( '/' ) ) !== -1 && ( lastIndex = url.lastIndexOf( '/' ) ) !== -1 && lastIndex !== index ) {
					// If the beginning + ending are shorter that 40 chars, show more of the ending.
					if ( index + url.length - lastIndex < 40 ) {
						lastIndex = -( 40 - ( index + 1 ) );
					}

					url = url.slice( 0, index + 1 ) + '\u2026' + url.slice( lastIndex );
				}

				tinymce.$( this.getEl().firstChild ).attr( 'href', this.url ).text( url );
			}
		}
	} ) );

	tinymce.ui.Factory.add( 'WPLinkInput', tinymce.ui.Control.extend( {
		renderHtml: function() {
			return (
				'<div id="' + this._id + '" class="wp-link-input">' +
					'<input type="text" value="" placeholder="' + tinymce.translate( 'Paste URL or type to search' ) + '" />' +
					'<input type="text" style="display:none" value="" />' +
				'</div>'
			);
		},
		setURL: function( url ) {
			this.getEl().firstChild.value = url;
		},
		getURL: function() {
			return tinymce.trim( this.getEl().firstChild.value );
		},
		getLinkText: function() {
			var text = this.getEl().firstChild.nextSibling.value;

			if ( ! tinymce.trim( text ) ) {
				return '';
			}

			return text.replace( /[\r\n\t ]+/g, ' ' );
		},
		reset: function() {
			var urlInput = this.getEl().firstChild;

			urlInput.value = '';
			urlInput.nextSibling.value = '';
		}
	} ) );

	tinymce.PluginManager.add( 'wplink', function( editor ) {
		var toolbar;
		var editToolbar;
		var previewInstance;
		var inputInstance;
		var linkNode;
		var doingUndoRedo;
		var doingUndoRedoTimer;
		var $ = window.jQuery;
		var emailRegex = /^(mailto:)?[a-z0-9._%+-]+@[a-z0-9][a-z0-9.-]*\.[a-z]{2,63}$/i;
		var urlRegex1 = /^https?:\/\/([^\s/?.#-][^\s\/?.#]*\.?)+(\/[^\s"]*)?$/i;
		var urlRegex2 = /^https?:\/\/[^\/]+\.[^\/]+($|\/)/i;
		var speak = ( typeof window.wp !== 'undefined' && window.wp.a11y && window.wp.a11y.speak ) ? window.wp.a11y.speak : function() {};
		var hasLinkError = false;
		var __ = window.wp.i18n.__;
		var _n = window.wp.i18n._n;
		var sprintf = window.wp.i18n.sprintf;

		function getSelectedLink() {
			var href, html,
				node = editor.selection.getStart(),
				link = editor.dom.getParent( node, 'a[href]' );

			if ( ! link ) {
				html = editor.selection.getContent({ format: 'raw' });

				if ( html && html.indexOf( '</a>' ) !== -1 ) {
					href = html.match( /href="([^">]+)"/ );

					if ( href && href[1] ) {
						link = editor.$( 'a[href="' + href[1] + '"]', node )[0];
					}

					if ( link ) {
						editor.selection.select( link );
					}
				}
			}

			return link;
		}

		function removePlaceholders() {
			editor.$( 'a' ).each( function( i, element ) {
				var $element = editor.$( element );

				if ( $element.attr( 'href' ) === '_wp_link_placeholder' ) {
					editor.dom.remove( element, true );
				} else if ( $element.attr( 'data-wplink-edit' ) ) {
					$element.attr( 'data-wplink-edit', null );
				}
			});
		}

		function removePlaceholderStrings( content, dataAttr ) {
			return content.replace( /(<a [^>]+>)([\s\S]*?)<\/a>/g, function( all, tag, text ) {
				if ( tag.indexOf( ' href="_wp_link_placeholder"' ) > -1 ) {
					return text;
				}

				if ( dataAttr ) {
					tag = tag.replace( / data-wplink-edit="true"/g, '' );
				}

				tag = tag.replace( / data-wplink-url-error="true"/g, '' );

				return tag + text + '</a>';
			});
		}

		function checkLink( node ) {
			var $link = editor.$( node );
			var href = $link.attr( 'href' );

			if ( ! href || typeof $ === 'undefined' ) {
				return;
			}

			hasLinkError = false;

			if ( /^http/i.test( href ) && ( ! urlRegex1.test( href ) || ! urlRegex2.test( href ) ) ) {
				hasLinkError = true;
				$link.attr( 'data-wplink-url-error', 'true' );
				speak( editor.translate( 'Warning: the link has been inserted but may have errors. Please test it.' ), 'assertive' );
			} else {
				$link.removeAttr( 'data-wplink-url-error' );
			}
		}

		editor.on( 'preinit', function() {
			if ( editor.wp && editor.wp._createToolbar ) {
				toolbar = editor.wp._createToolbar( [
					'wp_link_preview',
					'wp_link_edit',
					'wp_link_remove'
				], true );

				var editButtons = [
					'wp_link_input',
					'wp_link_apply'
				];

				if ( typeof window.wpLink !== 'undefined' ) {
					editButtons.push( 'wp_link_advanced' );
				}

				editToolbar = editor.wp._createToolbar( editButtons, true );

				editToolbar.on( 'show', function() {
					if ( typeof window.wpLink === 'undefined' || ! window.wpLink.modalOpen ) {
						window.setTimeout( function() {
							var element = editToolbar.$el.find( 'input.ui-autocomplete-input' )[0],
								selection = linkNode && ( linkNode.textContent || linkNode.innerText );

							if ( element ) {
								if ( ! element.value && selection && typeof window.wpLink !== 'undefined' ) {
									element.value = window.wpLink.getUrlFromSelection( selection );
								}

								if ( ! doingUndoRedo ) {
									element.focus();
									element.select();
								}
							}
						} );
					}
				} );

				editToolbar.on( 'hide', function() {
					if ( ! editToolbar.scrolling ) {
						editor.execCommand( 'wp_link_cancel' );
					}
				} );
			}
		} );

		editor.addCommand( 'WP_Link', function() {
			if ( tinymce.Env.ie && tinymce.Env.ie < 10 && typeof window.wpLink !== 'undefined' ) {
				window.wpLink.open( editor.id );
				return;
			}

			linkNode = getSelectedLink();
			editToolbar.tempHide = false;

			if ( ! linkNode ) {
				removePlaceholders();
				editor.execCommand( 'mceInsertLink', false, { href: '_wp_link_placeholder' } );

				linkNode = editor.$( 'a[href="_wp_link_placeholder"]' )[0];
				editor.nodeChanged();
			}

			editor.dom.setAttribs( linkNode, { 'data-wplink-edit': true } );
		} );

		editor.addCommand( 'wp_link_apply', function() {
			if ( editToolbar.scrolling ) {
				return;
			}

			var href, text;

			if ( linkNode ) {
				href = inputInstance.getURL();
				text = inputInstance.getLinkText();
				editor.focus();

				var parser = document.createElement( 'a' );
				parser.href = href;

				if ( 'javascript:' === parser.protocol || 'data:' === parser.protocol ) { // jshint ignore:line
					href = '';
				}

				if ( ! href ) {
					editor.dom.remove( linkNode, true );
					return;
				}

				if ( ! /^(?:[a-z]+:|#|\?|\.|\/)/.test( href ) && ! emailRegex.test( href ) ) {
					href = 'http://' + href;
				}

				editor.dom.setAttribs( linkNode, { href: href, 'data-wplink-edit': null } );

				if ( ! tinymce.trim( linkNode.innerHTML ) ) {
					editor.$( linkNode ).text( text || href );
				}

				checkLink( linkNode );
			}

			inputInstance.reset();
			editor.nodeChanged();

			// Audible confirmation message when a link has been inserted in the Editor.
			if ( typeof window.wpLinkL10n !== 'undefined' && ! hasLinkError ) {
				speak( window.wpLinkL10n.linkInserted );
			}
		} );

		editor.addCommand( 'wp_link_cancel', function() {
			inputInstance.reset();

			if ( ! editToolbar.tempHide ) {
				removePlaceholders();
			}
		} );

		editor.addCommand( 'wp_unlink', function() {
			editor.execCommand( 'unlink' );
			editToolbar.tempHide = false;
			editor.execCommand( 'wp_link_cancel' );
		} );

		// WP default shortcuts.
		editor.addShortcut( 'access+a', '', 'WP_Link' );
		editor.addShortcut( 'access+s', '', 'wp_unlink' );
		// The "de-facto standard" shortcut, see #27305.
		editor.addShortcut( 'meta+k', '', 'WP_Link' );

		editor.addButton( 'link', {
			icon: 'link',
			tooltip: 'Insert/edit link',
			cmd: 'WP_Link',
			stateSelector: 'a[href]'
		});

		editor.addButton( 'unlink', {
			icon: 'unlink',
			tooltip: 'Remove link',
			cmd: 'unlink'
		});

		editor.addMenuItem( 'link', {
			icon: 'link',
			text: 'Insert/edit link',
			cmd: 'WP_Link',
			stateSelector: 'a[href]',
			context: 'insert',
			prependToContext: true
		});

		editor.on( 'pastepreprocess', function( event ) {
			var pastedStr = event.content,
				regExp = /^(?:https?:)?\/\/\S+$/i;

			if ( ! editor.selection.isCollapsed() && ! regExp.test( editor.selection.getContent() ) ) {
				pastedStr = pastedStr.replace( /<[^>]+>/g, '' );
				pastedStr = tinymce.trim( pastedStr );

				if ( regExp.test( pastedStr ) ) {
					editor.execCommand( 'mceInsertLink', false, {
						href: editor.dom.decode( pastedStr )
					} );

					event.preventDefault();
				}
			}
		} );

		// Remove any remaining placeholders on saving.
		editor.on( 'savecontent', function( event ) {
			event.content = removePlaceholderStrings( event.content, true );
		});

		// Prevent adding undo levels on inserting link placeholder.
		editor.on( 'BeforeAddUndo', function( event ) {
			if ( event.lastLevel && event.lastLevel.content && event.level.content &&
				event.lastLevel.content === removePlaceholderStrings( event.level.content ) ) {

				event.preventDefault();
			}
		});

		// When doing undo and redo with keyboard shortcuts (Ctrl|Cmd+Z, Ctrl|Cmd+Shift+Z, Ctrl|Cmd+Y),
		// set a flag to not focus the inline dialog. The editor has to remain focused so the users can do consecutive undo/redo.
		editor.on( 'keydown', function( event ) {
			if ( event.keyCode === 27 ) { // Esc
				editor.execCommand( 'wp_link_cancel' );
			}

			if ( event.altKey || ( tinymce.Env.mac && ( ! event.metaKey || event.ctrlKey ) ) ||
				( ! tinymce.Env.mac && ! event.ctrlKey ) ) {

				return;
			}

			if ( event.keyCode === 89 || event.keyCode === 90 ) { // Y or Z
				doingUndoRedo = true;

				window.clearTimeout( doingUndoRedoTimer );
				doingUndoRedoTimer = window.setTimeout( function() {
					doingUndoRedo = false;
				}, 500 );
			}
		} );

		editor.addButton( 'wp_link_preview', {
			type: 'WPLinkPreview',
			onPostRender: function() {
				previewInstance = this;
			}
		} );

		editor.addButton( 'wp_link_input', {
			type: 'WPLinkInput',
			onPostRender: function() {
				var element = this.getEl(),
					input = element.firstChild,
					$input, cache, last;

				inputInstance = this;

				if ( $ && $.ui && $.ui.autocomplete ) {
					$input = $( input );

					$input.on( 'keydown', function() {
						$input.removeAttr( 'aria-activedescendant' );
					} )
					.autocomplete( {
						source: function( request, response ) {
							if ( last === request.term ) {
								response( cache );
								return;
							}

							if ( /^https?:/.test( request.term ) || request.term.indexOf( '.' ) !== -1 ) {
								return response();
							}

							$.post( window.ajaxurl, {
								action: 'wp-link-ajax',
								page: 1,
								search: request.term,
								_ajax_linking_nonce: $( '#_ajax_linking_nonce' ).val()
							}, function( data ) {
								cache = data;
								response( data );
							}, 'json' );

							last = request.term;
						},
						focus: function( event, ui ) {
							$input.attr( 'aria-activedescendant', 'mce-wp-autocomplete-' + ui.item.ID );
							/*
							 * Don't empty the URL input field, when using the arrow keys to
							 * highlight items. See api.jqueryui.com/autocomplete/#event-focus
							 */
							event.preventDefault();
						},
						select: function( event, ui ) {
							$input.val( ui.item.permalink );
							$( element.firstChild.nextSibling ).val( ui.item.title );

							if ( 9 === event.keyCode && typeof window.wpLinkL10n !== 'undefined' ) {
								// Audible confirmation message when a link has been selected.
								speak( window.wpLinkL10n.linkSelected );
							}

							return false;
						},
						open: function() {
							$input.attr( 'aria-expanded', 'true' );
							editToolbar.blockHide = true;
						},
						close: function() {
							$input.attr( 'aria-expanded', 'false' );
							editToolbar.blockHide = false;
						},
						minLength: 2,
						position: {
							my: 'left top+2'
						},
						messages: {
							noResults: __( 'No results found.' ) ,
							results: function( number ) {
								return sprintf(
									/* translators: %d: Number of search results found. */
									_n(
										'%d result found. Use up and down arrow keys to navigate.',
										'%d results found. Use up and down arrow keys to navigate.',
										number
									),
									number
								);
							}
						}
					} ).autocomplete( 'instance' )._renderItem = function( ul, item ) {
						var fallbackTitle = ( typeof window.wpLinkL10n !== 'undefined' ) ? window.wpLinkL10n.noTitle : '',
							title = item.title ? item.title : fallbackTitle;

						return $( '<li role="option" id="mce-wp-autocomplete-' + item.ID + '">' )
						.append( '<span>' + title + '</span>&nbsp;<span class="wp-editor-float-right">' + item.info + '</span>' )
						.appendTo( ul );
					};

					$input.attr( {
						'role': 'combobox',
						'aria-autocomplete': 'list',
						'aria-expanded': 'false',
						'aria-owns': $input.autocomplete( 'widget' ).attr( 'id' )
					} )
					.on( 'focus', function() {
						var inputValue = $input.val();
						/*
						 * Don't trigger a search if the URL field already has a link or is empty.
						 * Also, avoids screen readers announce `No search results`.
						 */
						if ( inputValue && ! /^https?:/.test( inputValue ) ) {
							$input.autocomplete( 'search' );
						}
					} )
					// Returns a jQuery object containing the menu element.
					.autocomplete( 'widget' )
						.addClass( 'wplink-autocomplete' )
						.attr( 'role', 'listbox' )
						.removeAttr( 'tabindex' ) // Remove the `tabindex=0` attribute added by jQuery UI.
						/*
						 * Looks like Safari and VoiceOver need an `aria-selected` attribute. See ticket #33301.
						 * The `menufocus` and `menublur` events are the same events used to add and remove
						 * the `ui-state-focus` CSS class on the menu items. See jQuery UI Menu Widget.
						 */
						.on( 'menufocus', function( event, ui ) {
							ui.item.attr( 'aria-selected', 'true' );
						})
						.on( 'menublur', function() {
							/*
							 * The `menublur` event returns an object where the item is `null`
							 * so we need to find the active item with other means.
							 */
							$( this ).find( '[aria-selected="true"]' ).removeAttr( 'aria-selected' );
						});
				}

				tinymce.$( input ).on( 'keydown', function( event ) {
					if ( event.keyCode === 13 ) {
						editor.execCommand( 'wp_link_apply' );
						event.preventDefault();
					}
				} );
			}
		} );

		editor.on( 'wptoolbar', function( event ) {
			var linkNode = editor.dom.getParent( event.element, 'a' ),
				$linkNode, href, edit;

			if ( typeof window.wpLink !== 'undefined' && window.wpLink.modalOpen ) {
				editToolbar.tempHide = true;
				return;
			}

			editToolbar.tempHide = false;

			if ( linkNode ) {
				$linkNode = editor.$( linkNode );
				href = $linkNode.attr( 'href' );
				edit = $linkNode.attr( 'data-wplink-edit' );

				if ( href === '_wp_link_placeholder' || edit ) {
					if ( href !== '_wp_link_placeholder' && ! inputInstance.getURL() ) {
						inputInstance.setURL( href );
					}

					event.element = linkNode;
					event.toolbar = editToolbar;
				} else if ( href && ! $linkNode.find( 'img' ).length ) {
					previewInstance.setURL( href );
					event.element = linkNode;
					event.toolbar = toolbar;

					if ( $linkNode.attr( 'data-wplink-url-error' ) === 'true' ) {
						toolbar.$el.find( '.wp-link-preview a' ).addClass( 'wplink-url-error' );
					} else {
						toolbar.$el.find( '.wp-link-preview a' ).removeClass( 'wplink-url-error' );
						hasLinkError = false;
					}
				}
			} else if ( editToolbar.visible() ) {
				editor.execCommand( 'wp_link_cancel' );
			}
		} );

		editor.addButton( 'wp_link_edit', {
			tooltip: 'Edit|button', // '|button' is not displayed, only used for context.
			icon: 'dashicon dashicons-edit',
			cmd: 'WP_Link'
		} );

		editor.addButton( 'wp_link_remove', {
			tooltip: 'Remove link',
			icon: 'dashicon dashicons-editor-unlink',
			cmd: 'wp_unlink'
		} );

		editor.addButton( 'wp_link_advanced', {
			tooltip: 'Link options',
			icon: 'dashicon dashicons-admin-generic',
			onclick: function() {
				if ( typeof window.wpLink !== 'undefined' ) {
					var url = inputInstance.getURL() || null,
						text = inputInstance.getLinkText() || null;

					window.wpLink.open( editor.id, url, text );

					editToolbar.tempHide = true;
					editToolbar.hide();
				}
			}
		} );

		editor.addButton( 'wp_link_apply', {
			tooltip: 'Apply',
			icon: 'dashicon dashicons-editor-break',
			cmd: 'wp_link_apply',
			classes: 'widget btn primary'
		} );

		return {
			close: function() {
				editToolbar.tempHide = false;
				editor.execCommand( 'wp_link_cancel' );
			},
			checkLink: checkLink
		};
	} );
} )( window.tinymce );
