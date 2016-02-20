( function( tinymce ) {
	tinymce.ui.WPLinkPreview = tinymce.ui.Control.extend( {
		url: '#',
		renderHtml: function() {
			return (
				'<div id="' + this._id + '" class="wp-link-preview">' +
					'<a href="' + this.url + '" target="_blank" tabindex="-1">' + this.url + '</a>' +
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

				// If the URL is longer that 40 chars, concatenate the beginning (after the domain) and ending with ...
				if ( url.length > 40 && ( index = url.indexOf( '/' ) ) !== -1 && ( lastIndex = url.lastIndexOf( '/' ) ) !== -1 && lastIndex !== index ) {
					// If the beginning + ending are shorter that 40 chars, show more of the ending
					if ( index + url.length - lastIndex < 40 ) {
						lastIndex = -( 40 - ( index + 1 ) );
					}

					url = url.slice( 0, index + 1 ) + '\u2026' + url.slice( lastIndex );
				}

				tinymce.$( this.getEl().firstChild ).attr( 'href', this.url ).text( url );
			}
		}
	} );

	tinymce.ui.WPLinkInput = tinymce.ui.Control.extend( {
		renderHtml: function() {
			return (
				'<div id="' + this._id + '" class="wp-link-input">' +
					'<input type="text" value="" tabindex="-1" placeholder="' + tinymce.translate('Paste URL or type to search') + '" />' +
				'</div>'
			);
		},
		setURL: function( url ) {
			this.getEl().firstChild.value = url;
		}
	} );

	tinymce.PluginManager.add( 'wplink', function( editor ) {
		var a;
		var toolbar;
		var editToolbar;
		var previewInstance;
		var inputInstance;
		var $ = window.jQuery;

		function getSelectedLink() {
			var href, html
				node = editor.selection.getNode();
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
						editor.nodeChanged();
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
				} else if ( $element.attr( 'data-wp-link-edit' ) ) {
					$element.attr( 'data-wp-link-edit', null );
				}
			});
		}
		
		function removePlaceholderStrings( content, dataAttr ) {
			if ( dataAttr ) {
				content = content.replace( / data-wp-link-edit="true"/g, '' );
			}

			return content.replace( /<a [^>]*?href="_wp_link_placeholder"[^>]*>([\s\S]+)<\/a>/g, '$1' );
		}

		editor.on( 'preinit', function() {
			if ( editor.wp && editor.wp._createToolbar ) {
				toolbar = editor.wp._createToolbar( [
					'wp_link_preview',
					'wp_link_edit',
					'wp_link_remove'
				], true );

				editToolbar = editor.wp._createToolbar( [
					'wp_link_input',
					'wp_link_apply',
					'wp_link_advanced'
				], true );

				editToolbar.on( 'show', function() {
					var inputNode = editToolbar.find( 'toolbar' )[0];

					inputNode && inputNode.focus( true );
					a = getSelectedLink();
				} );

				editToolbar.on( 'hide', function() {
					editToolbar.scrolling || editor.execCommand( 'wp_link_cancel' );
				} );
			}
		} );

		editor.addCommand( 'WP_Link', function() {
			var link = getSelectedLink();

			if ( link ) {
				editor.dom.setAttribs( link, { 'data-wp-link-edit': true } );
			} else {
				removePlaceholders();

				editor.execCommand( 'mceInsertLink', false, { href: '_wp_link_placeholder' } );
				editor.selection.select( editor.$( 'a[href="_wp_link_placeholder"]' )[0] );
				editor.nodeChanged();
			}
		} );

		editor.addCommand( 'wp_link_apply', function() {
			if ( editToolbar.scrolling ) {
				return;
			}

			var href = tinymce.trim( inputInstance.getEl().firstChild.value );

			if ( href && ! /^(?:[a-z]+:|#|\?|\.|\/)/.test( href ) ) {
				href = 'http://' + href;
			}

			if ( ! href ) {
				editor.dom.remove( a, true );
				return;
			}

			if ( a ) {
				editor.dom.setAttribs( a, { href: href, 'data-wp-link-edit': null } );
			}

			a = false;

			editor.nodeChanged();
			editor.focus();
		} );

		editor.addCommand( 'wp_link_cancel', function() {
			removePlaceholders();
			a = false;
			editor.nodeChanged();
			editor.focus();
		} );

		// WP default shortcut
		editor.addShortcut( 'access+a', '', 'WP_Link' );
		// The "de-facto standard" shortcut, see #27305
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
			if ( event.level.content ) {
				event.level.content = removePlaceholderStrings( event.level.content );
			}
		});

		editor.addButton( 'wp_link_preview', {
			type: 'WPLinkPreview',
			onPostRender: function() {
				previewInstance = this;
			}
		} );

		editor.addButton( 'wp_link_input', {
			type: 'WPLinkInput',
			onPostRender: function() {
				var input = this.getEl().firstChild;
				var cache;
				var last;

				inputInstance = this;

				if ( $ && $.ui && $.ui.autocomplete ) {
					$( input )
					.on( 'keydown', function() {
						$( input ).removeAttr( 'aria-activedescendant' );
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
							$( input ).attr( 'aria-activedescendant', 'mce-wp-autocomplete-' + ui.item.ID );
						},
						select: function( event, ui ) {
							$( input ).val( ui.item.permalink );
							return false;
						},
						open: function() {
							$( input ).attr( 'aria-expanded', 'true' );
							editToolbar.blockHide = true;
						},
						close: function() {
							$( input ).attr( 'aria-expanded', 'false' );
							editToolbar.blockHide = false;
						},
						minLength: 2,
						position: {
							my: 'left top+5'
						}
					} ).autocomplete( 'instance' )._renderItem = function( ul, item ) {
						return $( '<li role="option" id="mce-wp-autocomplete-' + item.ID + '">' )
						.append( '<span>' + item.title + '</span>&nbsp;<span class="alignright">' + item.info + '</span>' )
						.appendTo( ul );
					};

					$( input )
					.attr( {
						'role': 'combobox',
						'aria-autocomplete': 'list',
						'aria-expanded': 'false',
						'aria-owns': $( input ).autocomplete( 'widget' ).attr( 'id' )
					}  )
					.on( 'focus', function() {
						$( input ).autocomplete( 'search' );
					} )
					.autocomplete( 'widget' )
						.addClass( 'mce-wp-autocomplete' )
						.attr( 'role', 'listbox' );
				}

				tinymce.$( input ).on( 'keydown', function( event ) {
					event.keyCode === 13 && editor.execCommand( 'wp_link_apply' );
				} );
			}
		} );

		editor.on( 'wptoolbar', function( event ) {
			var anchor = editor.dom.getParent( event.element, 'a' ),
				$anchor, href, edit;

			if ( anchor ) {
				$anchor = editor.$( anchor );
				href = $anchor.attr( 'href' );
				edit = $anchor.attr( 'data-wp-link-edit' );

				if ( href === '_wp_link_placeholder' || edit ) {
					inputInstance.setURL( edit ? href : '' );
					event.element = anchor;
					event.toolbar = editToolbar;
				} else if ( href && ! $anchor.find( 'img' ).length ) {
					previewInstance.setURL( href );
					event.element = anchor;
					event.toolbar = toolbar;
				}
			}
		} );

		editor.addButton( 'wp_link_edit', {
			tooltip: 'Edit ', // trailing space is needed, used for context
			icon: 'dashicon dashicons-edit',
			cmd: 'WP_Link'
		} );

		editor.addButton( 'wp_link_remove', {
			tooltip: 'Remove',
			icon: 'dashicon dashicons-no',
			cmd: 'unlink'
		} );

		// Advanced, more, options?
		editor.addButton( 'wp_link_advanced', {
			tooltip: 'Advanced',
			icon: 'dashicon dashicons-admin-generic',
			onclick: function() {
				if ( typeof window.wpLink !== 'undefined' ) {
					if ( inputInstance.getEl().firstChild.value ) {
						editor.execCommand( 'wp_link_apply' );
					}

					window.wpLink.open( editor.id );
				}
			}
		} );

		editor.addButton( 'wp_link_apply', {
			tooltip: 'Apply',
			icon: 'dashicon dashicons-editor-break',
			cmd: 'wp_link_apply',
			classes: 'widget btn primary'
		} );
	} );
} )( window.tinymce );
