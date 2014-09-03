/* global ajaxurl, tinymce, wpLinkL10n, setUserSetting, wpActiveEditor */
var wpLink;

( function( $ ) {
	var editor, searchTimer, River, Query,
		inputs = {},
		rivers = {},
		isTouch = ( 'ontouchend' in document );

	wpLink = {
		timeToTriggerRiver: 150,
		minRiverAJAXDuration: 200,
		riverBottomThreshold: 5,
		keySensitivity: 100,
		lastSearch: '',
		textarea: '',

		init: function() {
			inputs.wrap = $('#wp-link-wrap');
			inputs.dialog = $( '#wp-link' );
			inputs.backdrop = $( '#wp-link-backdrop' );
			inputs.submit = $( '#wp-link-submit' );
			inputs.close = $( '#wp-link-close' );
			// URL
			inputs.url = $( '#url-field' );
			inputs.nonce = $( '#_ajax_linking_nonce' );
			// Secondary options
			inputs.title = $( '#link-title-field' );
			// Advanced Options
			inputs.openInNewTab = $( '#link-target-checkbox' );
			inputs.search = $( '#search-field' );
			// Build Rivers
			rivers.search = new River( $( '#search-results' ) );
			rivers.recent = new River( $( '#most-recent-results' ) );
			rivers.elements = inputs.dialog.find( '.query-results' );

			// Get search notice text
			inputs.queryNotice = $( '#query-notice-message' );
			inputs.queryNoticeTextDefault = inputs.queryNotice.find( '.query-notice-default' );
			inputs.queryNoticeTextHint = inputs.queryNotice.find( '.query-notice-hint' );

			// Bind event handlers
			inputs.dialog.keydown( wpLink.keydown );
			inputs.dialog.keyup( wpLink.keyup );
			inputs.submit.click( function( event ) {
				event.preventDefault();
				wpLink.update();
			});
			inputs.close.add( inputs.backdrop ).add( '#wp-link-cancel a' ).click( function( event ) {
				event.preventDefault();
				wpLink.close();
			});

			$( '#wp-link-search-toggle' ).on( 'click', wpLink.toggleInternalLinking );

			rivers.elements.on( 'river-select', wpLink.updateFields );

			// Display 'hint' message when search field or 'query-results' box are focused
			inputs.search.on( 'focus.wplink', function() {
				inputs.queryNoticeTextDefault.hide();
				inputs.queryNoticeTextHint.removeClass( 'screen-reader-text' ).show();
			} ).on( 'blur.wplink', function() {
				inputs.queryNoticeTextDefault.show();
				inputs.queryNoticeTextHint.addClass( 'screen-reader-text' ).hide();
			} );

			inputs.search.keyup( function() {
				var self = this;

				window.clearTimeout( searchTimer );
				searchTimer = window.setTimeout( function() {
					wpLink.searchInternalLinks.call( self );
				}, 500 );
			});
		},

		open: function( editorId ) {
			var ed;

			wpLink.range = null;

			if ( editorId ) {
				window.wpActiveEditor = editorId;
			}

			if ( ! window.wpActiveEditor ) {
				return;
			}

			this.textarea = $( '#' + window.wpActiveEditor ).get( 0 );

			if ( typeof tinymce !== 'undefined' ) {
				ed = tinymce.get( wpActiveEditor );

				if ( ed && ! ed.isHidden() ) {
					editor = ed;
				} else {
					editor = null;
				}

				if ( editor && tinymce.isIE ) {
					editor.windowManager.bookmark = editor.selection.getBookmark();
				}
			}

			if ( ! wpLink.isMCE() && document.selection ) {
				this.textarea.focus();
				this.range = document.selection.createRange();
			}

			inputs.wrap.show();
			inputs.backdrop.show();

			wpLink.refresh();
			$( document ).trigger( 'wplink-open', inputs.wrap );
		},

		isMCE: function() {
			return editor && ! editor.isHidden();
		},

		refresh: function() {
			// Refresh rivers (clear links, check visibility)
			rivers.search.refresh();
			rivers.recent.refresh();

			if ( wpLink.isMCE() ) {
				wpLink.mceRefresh();
			} else {
				wpLink.setDefaultValues();
			}

			if ( isTouch ) {
				// Close the onscreen keyboard
				inputs.url.focus().blur();
			} else {
				// Focus the URL field and highlight its contents.
				// If this is moved above the selection changes,
				// IE will show a flashing cursor over the dialog.
				inputs.url.focus()[0].select();
			}

			// Load the most recent results if this is the first time opening the panel.
			if ( ! rivers.recent.ul.children().length ) {
				rivers.recent.ajax();
			}
		},

		mceRefresh: function() {
			var e;

			// If link exists, select proper values.
			if ( e = editor.dom.getParent( editor.selection.getNode(), 'A' ) ) {
				// Set URL and description.
				inputs.url.val( editor.dom.getAttrib( e, 'href' ) );
				inputs.title.val( editor.dom.getAttrib( e, 'title' ) );
				// Set open in new tab.
				inputs.openInNewTab.prop( 'checked', ( '_blank' === editor.dom.getAttrib( e, 'target' ) ) );
				// Update save prompt.
				inputs.submit.val( wpLinkL10n.update );

			// If there's no link, set the default values.
			} else {
				wpLink.setDefaultValues();
			}
		},

		close: function() {
			if ( ! wpLink.isMCE() ) {
				wpLink.textarea.focus();

				if ( wpLink.range ) {
					wpLink.range.moveToBookmark( wpLink.range.getBookmark() );
					wpLink.range.select();
				}
			} else {
				editor.focus();
			}

			inputs.backdrop.hide();
			inputs.wrap.hide();
			$( document ).trigger( 'wplink-close', inputs.wrap );
		},

		getAttrs: function() {
			return {
				href: inputs.url.val(),
				title: inputs.title.val(),
				target: inputs.openInNewTab.prop( 'checked' ) ? '_blank' : ''
			};
		},

		update: function() {
			if ( wpLink.isMCE() )
				wpLink.mceUpdate();
			else
				wpLink.htmlUpdate();
		},

		htmlUpdate: function() {
			var attrs, html, begin, end, cursor, title, selection,
				textarea = wpLink.textarea;

			if ( ! textarea )
				return;

			attrs = wpLink.getAttrs();

			// If there's no href, return.
			if ( ! attrs.href || attrs.href == 'http://' )
				return;

			// Build HTML
			html = '<a href="' + attrs.href + '"';

			if ( attrs.title ) {
				title = attrs.title.replace( /</g, '&lt;' ).replace( />/g, '&gt;' ).replace( /"/g, '&quot;' );
				html += ' title="' + title + '"';
			}

			if ( attrs.target ) {
				html += ' target="' + attrs.target + '"';
			}

			html += '>';

			// Insert HTML
			if ( document.selection && wpLink.range ) {
				// IE
				// Note: If no text is selected, IE will not place the cursor
				//       inside the closing tag.
				textarea.focus();
				wpLink.range.text = html + wpLink.range.text + '</a>';
				wpLink.range.moveToBookmark( wpLink.range.getBookmark() );
				wpLink.range.select();

				wpLink.range = null;
			} else if ( typeof textarea.selectionStart !== 'undefined' ) {
				// W3C
				begin       = textarea.selectionStart;
				end         = textarea.selectionEnd;
				selection   = textarea.value.substring( begin, end );
				html        = html + selection + '</a>';
				cursor      = begin + html.length;

				// If no text is selected, place the cursor inside the closing tag.
				if ( begin == end )
					cursor -= '</a>'.length;

				textarea.value = textarea.value.substring( 0, begin ) + html +
					textarea.value.substring( end, textarea.value.length );

				// Update cursor position
				textarea.selectionStart = textarea.selectionEnd = cursor;
			}

			wpLink.close();
			textarea.focus();
		},

		mceUpdate: function() {
			var link,
				attrs = wpLink.getAttrs();

			wpLink.close();
			editor.focus();

			if ( tinymce.isIE ) {
				editor.selection.moveToBookmark( editor.windowManager.bookmark );
			}

			link = editor.dom.getParent( editor.selection.getNode(), 'a[href]' );

			// If the values are empty, unlink and return
			if ( ! attrs.href || attrs.href == 'http://' ) {
				editor.execCommand( 'unlink' );
				return;
			}

			if ( link ) {
				editor.dom.setAttribs( link, attrs );
			} else {
				editor.execCommand( 'mceInsertLink', false, attrs );
			}

			// Move the cursor to the end of the selection
			editor.selection.collapse();
		},

		updateFields: function( e, li ) {
			inputs.url.val( li.children( '.item-permalink' ).val() );
			inputs.title.val( li.hasClass( 'no-title' ) ? '' : li.children( '.item-title' ).text() );
		},

		setDefaultValues: function() {
			var selection = editor && editor.selection.getContent(),
				emailRegexp = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i,
				urlRegexp = /^(https?|ftp):\/\/[A-Z0-9.-]+\.[A-Z]{2,4}[^ "]*$/i;

			if ( selection && emailRegexp.test( selection ) ) {
				// Selection is email address
				inputs.url.val( 'mailto:' + selection );
			} else if ( selection && urlRegexp.test( selection ) ) {
				// Selection is URL
				inputs.url.val( selection.replace( /&amp;|&#0?38;/gi, '&' ) );
			} else {
				// Set URL to default.
				inputs.url.val( 'http://' );
			}

			// Set description to default.
			inputs.title.val( '' );

			// Update save prompt.
			inputs.submit.val( wpLinkL10n.save );
		},

		searchInternalLinks: function() {
			var t = $( this ), waiting,
				search = t.val();

			if ( search.length > 2 ) {
				rivers.recent.hide();
				rivers.search.show();

				// Don't search if the keypress didn't change the title.
				if ( wpLink.lastSearch == search )
					return;

				wpLink.lastSearch = search;
				waiting = t.parent().find('.spinner').show();

				rivers.search.change( search );
				rivers.search.ajax( function() {
					waiting.hide();
				});
			} else {
				rivers.search.hide();
				rivers.recent.show();
			}
		},

		next: function() {
			rivers.search.next();
			rivers.recent.next();
		},

		prev: function() {
			rivers.search.prev();
			rivers.recent.prev();
		},

		keydown: function( event ) {
			var fn, id,
				key = $.ui.keyCode;

			if ( key.ESCAPE === event.keyCode ) {
				wpLink.close();
				event.stopImmediatePropagation();
			} else if ( key.TAB === event.keyCode ) {
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

			if ( event.keyCode !== key.UP && event.keyCode !== key.DOWN ) {
				return;
			}

			if ( document.activeElement &&
				( document.activeElement.id === 'link-title-field' || document.activeElement.id === 'url-field' ) ) {
				return;
			}

			fn = event.keyCode === key.UP ? 'prev' : 'next';
			clearInterval( wpLink.keyInterval );
			wpLink[ fn ]();
			wpLink.keyInterval = setInterval( wpLink[ fn ], wpLink.keySensitivity );
			event.preventDefault();
		},

		keyup: function( event ) {
			var key = $.ui.keyCode;

			if ( event.which === key.UP || event.which === key.DOWN ) {
				clearInterval( wpLink.keyInterval );
				event.preventDefault();
			}
		},

		delayedCallback: function( func, delay ) {
			var timeoutTriggered, funcTriggered, funcArgs, funcContext;

			if ( ! delay )
				return func;

			setTimeout( function() {
				if ( funcTriggered )
					return func.apply( funcContext, funcArgs );
				// Otherwise, wait.
				timeoutTriggered = true;
			}, delay );

			return function() {
				if ( timeoutTriggered )
					return func.apply( this, arguments );
				// Otherwise, wait.
				funcArgs = arguments;
				funcContext = this;
				funcTriggered = true;
			};
		},

		toggleInternalLinking: function( event ) {
			var visible = inputs.wrap.hasClass( 'search-panel-visible' );

			inputs.wrap.toggleClass( 'search-panel-visible', ! visible );
			setUserSetting( 'wplink', visible ? '0' : '1' );
			inputs[ ! visible ? 'search' : 'url' ].focus();
			event.preventDefault();
		}
	};

	River = function( element, search ) {
		var self = this;
		this.element = element;
		this.ul = element.children( 'ul' );
		this.contentHeight = element.children( '#link-selector-height' );
		this.waiting = element.find('.river-waiting');

		this.change( search );
		this.refresh();

		$( '#wp-link .query-results, #wp-link #link-selector' ).scroll( function() {
			self.maybeLoad();
		});
		element.on( 'click', 'li', function( event ) {
			self.select( $( this ), event );
		});
	};

	$.extend( River.prototype, {
		refresh: function() {
			this.deselect();
			this.visible = this.element.is( ':visible' );
		},
		show: function() {
			if ( ! this.visible ) {
				this.deselect();
				this.element.show();
				this.visible = true;
			}
		},
		hide: function() {
			this.element.hide();
			this.visible = false;
		},
		// Selects a list item and triggers the river-select event.
		select: function( li, event ) {
			var liHeight, elHeight, liTop, elTop;

			if ( li.hasClass( 'unselectable' ) || li == this.selected )
				return;

			this.deselect();
			this.selected = li.addClass( 'selected' );
			// Make sure the element is visible
			liHeight = li.outerHeight();
			elHeight = this.element.height();
			liTop = li.position().top;
			elTop = this.element.scrollTop();

			if ( liTop < 0 ) // Make first visible element
				this.element.scrollTop( elTop + liTop );
			else if ( liTop + liHeight > elHeight ) // Make last visible element
				this.element.scrollTop( elTop + liTop - elHeight + liHeight );

			// Trigger the river-select event
			this.element.trigger( 'river-select', [ li, event, this ] );
		},
		deselect: function() {
			if ( this.selected )
				this.selected.removeClass( 'selected' );
			this.selected = false;
		},
		prev: function() {
			if ( ! this.visible )
				return;

			var to;
			if ( this.selected ) {
				to = this.selected.prev( 'li' );
				if ( to.length )
					this.select( to );
			}
		},
		next: function() {
			if ( ! this.visible )
				return;

			var to = this.selected ? this.selected.next( 'li' ) : $( 'li:not(.unselectable):first', this.element );
			if ( to.length )
				this.select( to );
		},
		ajax: function( callback ) {
			var self = this,
				delay = this.query.page == 1 ? 0 : wpLink.minRiverAJAXDuration,
				response = wpLink.delayedCallback( function( results, params ) {
					self.process( results, params );
					if ( callback )
						callback( results, params );
				}, delay );

			this.query.ajax( response );
		},
		change: function( search ) {
			if ( this.query && this._search == search )
				return;

			this._search = search;
			this.query = new Query( search );
			this.element.scrollTop( 0 );
		},
		process: function( results, params ) {
			var list = '', alt = true, classes = '',
				firstPage = params.page == 1;

			if ( ! results ) {
				if ( firstPage ) {
					list += '<li class="unselectable no-matches-found"><span class="item-title"><em>' +
						wpLinkL10n.noMatchesFound + '</em></span></li>';
				}
			} else {
				$.each( results, function() {
					classes = alt ? 'alternate' : '';
					classes += this.title ? '' : ' no-title';
					list += classes ? '<li class="' + classes + '">' : '<li>';
					list += '<input type="hidden" class="item-permalink" value="' + this.permalink + '" />';
					list += '<span class="item-title">';
					list += this.title ? this.title : wpLinkL10n.noTitle;
					list += '</span><span class="item-info">' + this.info + '</span></li>';
					alt = ! alt;
				});
			}

			this.ul[ firstPage ? 'html' : 'append' ]( list );
		},
		maybeLoad: function() {
			var self = this,
				el = this.element,
				bottom = el.scrollTop() + el.height();

			if ( ! this.query.ready() || bottom < this.contentHeight.height() - wpLink.riverBottomThreshold )
				return;

			setTimeout(function() {
				var newTop = el.scrollTop(),
					newBottom = newTop + el.height();

				if ( ! self.query.ready() || newBottom < self.contentHeight.height() - wpLink.riverBottomThreshold )
					return;

				self.waiting.show();
				el.scrollTop( newTop + self.waiting.outerHeight() );

				self.ajax( function() {
					self.waiting.hide();
				});
			}, wpLink.timeToTriggerRiver );
		}
	});

	Query = function( search ) {
		this.page = 1;
		this.allLoaded = false;
		this.querying = false;
		this.search = search;
	};

	$.extend( Query.prototype, {
		ready: function() {
			return ! ( this.querying || this.allLoaded );
		},
		ajax: function( callback ) {
			var self = this,
				query = {
					action : 'wp-link-ajax',
					page : this.page,
					'_ajax_linking_nonce' : inputs.nonce.val()
				};

			if ( this.search )
				query.search = this.search;

			this.querying = true;

			$.post( ajaxurl, query, function( r ) {
				self.page++;
				self.querying = false;
				self.allLoaded = ! r;
				callback( r, query );
			}, 'json' );
		}
	});

	$( document ).ready( wpLink.init );
})( jQuery );
