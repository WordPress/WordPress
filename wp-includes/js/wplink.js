/* global tinymce, wpLinkL10n, wpActiveEditor */
var wpLink;

( function( $ ) {
	var editor, correctedURL,
		inputs = {},
		isTouch = ( 'ontouchend' in document );

	function getLink() {
		return editor.dom.getParent( editor.selection.getNode(), 'a' );
	}

	wpLink = {
		textarea: '',

		init: function() {
			inputs.wrap = $('#wp-link-wrap');
			inputs.dialog = $( '#wp-link' );
			inputs.backdrop = $( '#wp-link-backdrop' );
			inputs.submit = $( '#wp-link-submit' );
			inputs.close = $( '#wp-link-close' );

			// Input
			inputs.text = $( '#wp-link-text' );
			inputs.url = $( '#wp-link-url' );
			inputs.openInNewTab = $( '#wp-link-target' );

			if ( $.ui && $.ui.autocomplete ) {
				wpLink.setAutocomplete();
			}

			inputs.dialog.on( 'keydown', wpLink.keydown );
			inputs.submit.on( 'click', function( event ) {
				event.preventDefault();
				wpLink.update();
			});

			inputs.close.add( inputs.backdrop ).add( '#wp-link-cancel a' ).click( function( event ) {
				event.preventDefault();
				wpLink.close();
			});

			inputs.url.on( 'paste', function() {
				setTimeout( wpLink.correctURL, 0 );
			} );
		},

		setAutocomplete: function() {
			var $input = inputs.url,
				cache, last;

			$input.on( 'keydown', function() {
				$input.removeAttr( 'aria-activedescendant' );
			} ).autocomplete( {
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
				},
				select: function( event, ui ) {
					$input.val( ui.item.permalink );

					if ( inputs.wrap.hasClass( 'has-text-field' ) && tinymce.trim( inputs.text.val() ) === '' ) {
						inputs.text.val( ui.item.title );
					}

					return false;
				},
				open: function() {
					$input.attr( 'aria-expanded', 'true' );
				},
				close: function() {
					$input.attr( 'aria-expanded', 'false' );
				},
				minLength: 2,
				position: {
					my: 'left top+2'
				}
			} ).autocomplete( 'instance' )._renderItem = function( ul, item ) {
				return $( '<li role="option" id="mce-wp-autocomplete-' + item.ID + '">' )
				.append( '<span class="item-title">' + item.title + '</span>&nbsp;<span class="item-date alignright">' + item.info + '</span>' )
				.appendTo( ul );
			};

			$input.attr( {
				'aria-owns': $input.autocomplete( 'widget' ).attr( 'id' )
			}  )
			.on( 'focus', function() {
				$input.autocomplete( 'search' );
			} )
			.autocomplete( 'widget' )
				.addClass( 'wplink-autocomplete' )
				.attr( 'role', 'listbox' );


		},

		// If URL wasn't corrected last time and doesn't start with http:, https:, ? # or /, prepend http://
		correctURL: function () {
			var url = $.trim( inputs.url.val() );

			if ( url && correctedURL !== url && ! /^(?:[a-z]+:|#|\?|\.|\/)/.test( url ) ) {
				inputs.url.val( 'http://' + url );
				correctedURL = url;
			}
		},

		open: function( editorId, url, text ) {
			var ed,
				$body = $( document.body );

			$body.addClass( 'modal-open' );

			wpLink.range = null;

			if ( editorId ) {
				window.wpActiveEditor = editorId;
			}

			if ( ! window.wpActiveEditor ) {
				return;
			}

			this.textarea = $( '#' + window.wpActiveEditor ).get( 0 );

			if ( typeof tinymce !== 'undefined' ) {
				// Make sure the link wrapper is the last element in the body,
				// or the inline editor toolbar may show above the backdrop.
				$body.append( inputs.backdrop, inputs.wrap );

				ed = tinymce.get( wpActiveEditor );

				if ( ed && ! ed.isHidden() ) {
					editor = ed;
				} else {
					editor = null;
				}

				if ( editor && tinymce.isIE && ! editor.windowManager.wplinkBookmark ) {
					editor.windowManager.wplinkBookmark = editor.selection.getBookmark();
				}
			}

			if ( ! wpLink.isMCE() && document.selection ) {
				this.textarea.focus();
				this.range = document.selection.createRange();
			}

			inputs.wrap.show();
			inputs.backdrop.show();

			wpLink.refresh( url, text );

			$( document ).trigger( 'wplink-open', inputs.wrap );
		},

		isMCE: function() {
			return editor && ! editor.isHidden();
		},

		refresh: function( url, text ) {
			var linkText = '';

			if ( wpLink.isMCE() ) {
				wpLink.mceRefresh( url, text );
			} else {
				// For the Text editor the "Link text" field is always shown
				if ( ! inputs.wrap.hasClass( 'has-text-field' ) ) {
					inputs.wrap.addClass( 'has-text-field' );
				}

				if ( document.selection ) {
					// Old IE
					linkText = document.selection.createRange().text || text || '';
				} else if ( typeof this.textarea.selectionStart !== 'undefined' &&
					( this.textarea.selectionStart !== this.textarea.selectionEnd ) ) {
					// W3C
					text = this.textarea.value.substring( this.textarea.selectionStart, this.textarea.selectionEnd ) || text || '';
				}

				inputs.text.val( text );
				wpLink.setDefaultValues();
			}

			if ( isTouch ) {
				// Close the onscreen keyboard
				inputs.url.focus().blur();
			} else {
				// Focus the URL field and highlight its contents.
				// If this is moved above the selection changes,
				// IE will show a flashing cursor over the dialog.
				window.setTimeout( function() {
					inputs.url.focus()[0].select();
				} );
			}

			correctedURL = inputs.url.val().replace( /^http:\/\//, '' );
		},

		hasSelectedText: function( linkNode ) {
			var node, nodes, i, html = editor.selection.getContent();

			// Partial html and not a fully selected anchor element
			if ( /</.test( html ) && ( ! /^<a [^>]+>[^<]+<\/a>$/.test( html ) || html.indexOf('href=') === -1 ) ) {
				return false;
			}

			if ( linkNode ) {
				nodes = linkNode.childNodes;

				if ( nodes.length === 0 ) {
					return false;
				}

				for ( i = nodes.length - 1; i >= 0; i-- ) {
					node = nodes[i];

					if ( node.nodeType != 3 && ! tinymce.dom.BookmarkManager.isBookmarkNode( node ) ) {
						return false;
					}
				}
			}

			return true;
		},

		mceRefresh: function( url, text ) {
			var linkText,
				linkNode = getLink(),
				onlyText = this.hasSelectedText( linkNode );

			if ( linkNode ) {
				linkText = linkNode.innerText || linkNode.textContent;

				if ( ! tinymce.trim( linkText ) ) {
					linkText = text || '';
				}

				url = url || editor.dom.getAttrib( linkNode, 'href' );

				if ( url === '_wp_link_placeholder' ) {
					url = '';
				}

				inputs.url.val( url );
				inputs.openInNewTab.prop( 'checked', '_blank' === editor.dom.getAttrib( linkNode, 'target' ) );
				inputs.submit.val( wpLinkL10n.update );
			} else {
				text = editor.selection.getContent({ format: 'text' }) || text;
				this.setDefaultValues();
			}

			if ( onlyText ) {
				inputs.text.val( linkText || '' );
				inputs.wrap.addClass( 'has-text-field' );
			} else {
				inputs.text.val( '' );
				inputs.wrap.removeClass( 'has-text-field' );
			}
		},

		close: function() {
			$( document.body ).removeClass( 'modal-open' );

			if ( ! wpLink.isMCE() ) {
				wpLink.textarea.focus();

				if ( wpLink.range ) {
					wpLink.range.moveToBookmark( wpLink.range.getBookmark() );
					wpLink.range.select();
				}
			} else {
				if ( editor.plugins.wplink ) {
					editor.plugins.wplink.close();
				}

				editor.focus();
			}

			inputs.backdrop.hide();
			inputs.wrap.hide();

			correctedURL = false;

			$( document ).trigger( 'wplink-close', inputs.wrap );
		},

		getAttrs: function() {
			wpLink.correctURL();

			return {
				href: $.trim( inputs.url.val() ),
				target: inputs.openInNewTab.prop( 'checked' ) ? '_blank' : ''
			};
		},

		buildHtml: function(attrs) {
			var html = '<a href="' + attrs.href + '"';

			if ( attrs.target ) {
				html += ' target="' + attrs.target + '"';
			}

			return html + '>';
		},

		update: function() {
			if ( wpLink.isMCE() ) {
				wpLink.mceUpdate();
			} else {
				wpLink.htmlUpdate();
			}
		},

		htmlUpdate: function() {
			var attrs, text, html, begin, end, cursor, selection,
				textarea = wpLink.textarea;

			if ( ! textarea ) {
				return;
			}

			attrs = wpLink.getAttrs();
			text = inputs.text.val();

			// If there's no href, return.
			if ( ! attrs.href ) {
				return;
			}

			html = wpLink.buildHtml(attrs);

			// Insert HTML
			if ( document.selection && wpLink.range ) {
				// IE
				// Note: If no text is selected, IE will not place the cursor
				//       inside the closing tag.
				textarea.focus();
				wpLink.range.text = html + ( text || wpLink.range.text ) + '</a>';
				wpLink.range.moveToBookmark( wpLink.range.getBookmark() );
				wpLink.range.select();

				wpLink.range = null;
			} else if ( typeof textarea.selectionStart !== 'undefined' ) {
				// W3C
				begin = textarea.selectionStart;
				end = textarea.selectionEnd;
				selection = text || textarea.value.substring( begin, end );
				html = html + selection + '</a>';
				cursor = begin + html.length;

				// If no text is selected, place the cursor inside the closing tag.
				if ( begin === end && ! selection ) {
					cursor -= 4;
				}

				textarea.value = (
					textarea.value.substring( 0, begin ) +
					html +
					textarea.value.substring( end, textarea.value.length )
				);

				// Update cursor position
				textarea.selectionStart = textarea.selectionEnd = cursor;
			}

			wpLink.close();
			textarea.focus();
		},

		mceUpdate: function() {
			var attrs = wpLink.getAttrs(),
				link, text;

			editor.focus();

			if ( tinymce.isIE ) {
				editor.selection.moveToBookmark( editor.windowManager.wplinkBookmark );
				editor.windowManager.wplinkBookmark = null;
			}

			if ( ! attrs.href ) {
				editor.execCommand( 'unlink' );
				return;
			}

			link = getLink();

			if ( inputs.wrap.hasClass( 'has-text-field' ) ) {
				text = inputs.text.val() || attrs.href;
			}

			if ( link ) {
				if ( text ) {
					if ( 'innerText' in link ) {
						link.innerText = text;
					} else {
						link.textContent = text;
					}
				}

				editor.dom.setAttribs( link, attrs );
			} else {
				if ( text ) {
					editor.selection.setNode( editor.dom.create( 'a', attrs, editor.dom.encode( text ) ) );
				} else {
					editor.execCommand( 'mceInsertLink', false, attrs );
				}
			}

			wpLink.close();
			editor.nodeChanged();
		},

		keydown: function( event ) {
			var id;

			// Escape key.
			if ( 27 === event.keyCode ) {
				wpLink.close();
				event.stopImmediatePropagation();
			// Tab key.
			} else if ( 9 === event.keyCode ) {
				id = event.target.id;

				// wp-link-submit must always be the last focusable element in the dialog.
				// following focusable elements will be skipped on keyboard navigation.
				if ( id === 'wp-link-submit' && ! event.shiftKey ) {
					inputs.close.focus();
					event.preventDefault();
				} else if ( id === 'wp-link-close' && event.shiftKey ) {
					inputs.submit.focus();
					event.preventDefault();
				}
			}
		},

		setDefaultValues: function() {
			var selection,
				emailRegexp = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i,
				urlRegexp = /^(https?|ftp):\/\/[A-Z0-9.-]+\.[A-Z]{2,4}[^ "]*$/i;

			if ( this.isMCE() ) {
				selection = editor.selection.getContent();
			} else if ( document.selection && wpLink.range ) {
				selection = wpLink.range.text;
			} else if ( typeof this.textarea.selectionStart !== 'undefined' ) {
				selection = this.textarea.value.substring( this.textarea.selectionStart, this.textarea.selectionEnd );
			}

			if ( selection && emailRegexp.test( selection ) ) {
				// Selection is email address
				inputs.url.val( 'mailto:' + selection );
			} else if ( selection && urlRegexp.test( selection ) ) {
				// Selection is URL
				inputs.url.val( selection.replace( /&amp;|&#0?38;/gi, '&' ) );
			} else {
				// Set URL to default.
				inputs.url.val( '' );
			}

			// Update save prompt.
			inputs.submit.val( wpLinkL10n.save );
		}
	};

	$( document ).ready( wpLink.init );
})( jQuery );
