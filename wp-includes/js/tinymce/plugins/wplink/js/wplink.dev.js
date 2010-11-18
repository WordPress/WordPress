(function($){
	var inputs = {}, results = {}, ed,
	wpLink = {
		timeToTriggerRiver: 150,
		minRiverAJAXDuration: 200,
		lastSearch: '',
		riverDefaults: function() {
			return {
				page : 2,
				allLoaded: false,
				active: false
			};
		},
		init : function() {
			inputs.dialog = $('#wp-link');
			// URL
			inputs.url = $('#url-field');
			// Secondary options
			inputs.title = $('#link-title-field');
			// Advanced Options
			inputs.openInNewTab = $('#link-target-checkbox');
			inputs.search = $('#search-field');
			// Result lists
			results.search = $('#search-results');
			results.recent = $('#most-recent-results');
			results.search.data('river', wpLink.riverDefaults() );
			results.recent.data('river', wpLink.riverDefaults() );
			results.group = $('.query-results', inputs.dialog);

			// Bind event handlers
			$('#wp-link-update').click( wpLink.update );
			$('#wp-link-cancel').click( function() { tinyMCEPopup.close(); } );
			
			results.group.delegate('li', 'click', wpLink.selectInternalLink )
			results.group.scroll( wpLink.maybeLoadRiver );
			
			inputs.search.keyup( wpLink.searchInternalLinks );
			
			inputs.dialog.bind('dialogopen', wpLink.refresh);
		},

		refresh : function() {
			var e;
			
			ed = tinyMCEPopup.editor;
			
			// If link exists, select proper values.
			if ( e = ed.dom.getParent(ed.selection.getNode(), 'A') ) {
				// Set URL and description.
				inputs.url.val( e.href );
				inputs.title.val( ed.dom.getAttrib(e, 'title') );
				// Set open in new tab.
				if ( "_blank" == ed.dom.getAttrib(e, 'target') )
					inputs.openInNewTab.attr('checked','checked');
			}

			// Clear previously selected links
			results.group.find('.selected').removeClass('selected');
			// Focus the URL field
			inputs.url.focus();
		},

		update : function() {
			var el,
				ed = tinyMCEPopup.editor,
				attrs = {
					href : inputs.url.val(),
					title : inputs.title.val(),
					target : inputs.openInNewTab.attr('checked') ? '_blank' : ''
				}, e, b,
				defaultContent = attrs.title ? attrs.title : attrs.href;

			tinyMCEPopup.restoreSelection();
			e = ed.dom.getParent(ed.selection.getNode(), 'A');

			// If the values are empty...
			if ( ! attrs.href ) {
				// ...and nothing is selected, we should return
				if ( ed.selection.isCollapsed() ) {
					tinyMCEPopup.close();
					return;
				// ...and a link exists, we should unlink and return
				} else if ( e ) {
					tinyMCEPopup.execCommand("mceBeginUndoLevel");
					b = ed.selection.getBookmark();
					ed.dom.remove(e, 1);
					ed.selection.moveToBookmark(b);
					tinyMCEPopup.execCommand("mceEndUndoLevel");
					tinyMCEPopup.close();
					return;
				}
			}

			tinyMCEPopup.execCommand("mceBeginUndoLevel");

			if (e == null) {
				ed.getDoc().execCommand("unlink", false, null);

				// If no selection exists, create a new link from scratch.
				if ( ed.selection.isCollapsed() ) {
					el = ed.dom.create('a', { href: "#mce_temp_url#" }, defaultContent);
					ed.selection.setNode(el);
				// If a selection exists, wrap it in a link.
				} else {
					tinyMCEPopup.execCommand("CreateLink", false, "#mce_temp_url#", {skip_undo : 1});
				}

				tinymce.each(ed.dom.select("a"), function(n) {
					if (ed.dom.getAttrib(n, 'href') == '#mce_temp_url#') {
						e = n;
						ed.dom.setAttribs(e, attrs);
					}
				});
			} else {
				ed.dom.setAttribs(e, attrs);
			}

			// Don't move caret if selection was image
			if (e.childNodes.length != 1 || e.firstChild.nodeName != 'IMG') {
				ed.focus();
				ed.selection.select(e);
				ed.selection.collapse(0);
				tinyMCEPopup.storeSelection();
			}

			tinyMCEPopup.execCommand("mceEndUndoLevel");
			tinyMCEPopup.close();
		},

		selectInternalLink : function() {
			var t = $(this);
			if ( t.hasClass('unselectable') )
				return;
			t.siblings('.selected').removeClass('selected');
			t.addClass('selected');
			inputs.url.val( t.children('.item-permalink').val() );
			inputs.title.val( t.children('.item-title').text() );
		},

		maybeLoadRiver : function() {
			var t = $(this),
				ul = t.children('ul'),
				river = t.data('river'),
				bottom = t.scrollTop() + t.height();

			if ( bottom != ul.height() || river.active || river.allLoaded )
				return;

			setTimeout(function() {
				var newTop = t.scrollTop(),
					newBottom = newTop + t.height(),
					waiting = t.find('.river-waiting');

				if ( bottom != newBottom || newBottom != ul.height() || river.active || river.allLoaded )
					return;

				river.active = true;
				waiting.show();
				t.scrollTop( newTop + waiting.outerHeight() );

				wpLink.linkAJAX( t, { page : river.page }, function(r) {
					river.page++;
					river.active = false;
					river.allLoaded = !r;
					waiting.hide();
				}, {
					append : true,
					delay : wpLink.minRiverAJAXDuration
				});
			}, wpLink.timeToTriggerRiver );
		},
		searchInternalLinks : function() {
			var t = $(this), waiting,
				title = t.val();

			if ( title.length > 2 ) {
				results.recent.hide();
				results.search.show();

				// Don't search if the keypress didn't change the title.
				if ( wpLink.lastSearch == title )
					return;

				wpLink.lastSearch = title;
				waiting = t.siblings('img.waiting').show();

				results.search.data('river', wpLink.riverDefaults() );
				results.search.scrollTop(0);
				wpLink.linkAJAX( results.search, { title : title }, function(){ waiting.hide(); });
			} else {
				results.search.hide();
				results.recent.show();
			}
		},

		linkAJAX : function( $panel, params, callback, opts ) {
			var response;
			opts = opts || {};

			if ( ! $panel.hasClass('query-results') )
				$panel = $panel.parents('.query-results');

			if ( ! $panel.length )
				return;

			response = wpLink.delayedCallback( function( results ) {
				wpLink.processAJAXResponse( $panel, results, callback, opts );
			}, opts.delay );

			$.post( ajaxurl, $.extend({
				action : 'wp-link-ajax'
			}, params ), response, "json" );
		},

		processAJAXResponse: function( $panel, results, callback, opts ) {
			var list = '';

			if ( !results ) {
				if ( !opts.append ) {
					list += '<li class="no-matches-found unselectable"><span class="item-title"><em>'
					+ wpLinkL10n.noMatchesFound
					+ '</em></span></li>';
				}
			} else {
				$.each( results, function() {
					list += '<li><input type="hidden" class="item-permalink" value="' + this['permalink'] + '" />';
					list += '<span class="item-title">';
					list += this['title'] ? this['title'] : '<em>'+ wpLinkL10n.untitled + '</em>';
					list += '</span><span class="item-info">' + this['info'] + '</span></li>';
				});
			}

			// Set results
			$panel.children('ul')[ opts.append ? 'append' : 'html' ]( list );

			// Run callback
			if ( callback )
				callback( results );
		},

		delayedCallback : function( func, delay ) {
			var timeoutTriggered, funcTriggered, funcArgs, funcContext;

			if ( ! delay )
				return func;

			setTimeout( function() {
				if ( funcTriggered )
					return func.apply( funcContext, funcArgs );
				// Otherwise, wait.
				timeoutTriggered = true;
			}, delay);

			return function() {
				if ( timeoutTriggered )
					return func.apply( this, arguments );
				// Otherwise, wait.
				funcArgs = arguments;
				funcContext = this;
				funcTriggered = true;
			};
		}
	}

	$(document).ready( wpLink.init );
})(jQuery);