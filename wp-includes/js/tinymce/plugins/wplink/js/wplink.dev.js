(function($){	
	var inputs = {}, results = {}, ed,
	wpLink = {
		init : function() {
			var e, etarget, eclass;
			// Init shared vars
			ed = tinyMCEPopup.editor;
			
			
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
			
			// Bind event handlers
			$('#wp-update').click( wpLink.update );
			$('#wp-cancel').click( function() { tinyMCEPopup.close(); } );
			$('.query-results').delegate('li', 'click', wpLink.selectInternalLink );
			$('.wp-results-pagelinks').delegate('a', 'click', wpLink.selectPageLink );
			inputs.search.keyup( wpLink.searchInternalLinks );

			// If link exists, select proper values.
			if ( e = ed.dom.getParent(ed.selection.getNode(), 'A') ) {
				// Set URL and description.
				inputs.url.val( e.href );
				inputs.title.val( ed.dom.getAttrib(e, 'title') );
				// Set open in new tab.
				if ( "_blank" == ed.dom.getAttrib(e, 'target') )
					inputs.openInNewTab.attr('checked','checked');
			}
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
					var el = ed.dom.create('a', { href: "#mce_temp_url#" }, defaultContent);
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
		
		selectPageLink : function(e) {
			var page = e.target.href.match(/page=(\d+)/);
			
			page = page ? page[1] : 1; // If there's no match, it's the first page.
			e.preventDefault(); // Prevent the link from redirecting.
			
			wpLink.linkAJAX( $(this), { page : page });
		},
		
		searchInternalLinks : function() {
			var t = $(this), waiting,
				title = t.val();
			
			if ( title ) {
				results.recent.hide();
				results.search.show();
				waiting = t.siblings('img.waiting').show();
				wpLink.linkAJAX( results.search, { title : title }, function(){ waiting.hide(); });
			} else {
				results.search.hide();
				results.recent.show();
			}
		},
		
		linkAJAX : function( $panel, params, callback ) {
			if ( ! $panel.hasClass('query-results') )
				$panel = $panel.parents('.query-results');
			
			if ( ! $panel.length )
				return;
			
			$.post( ajaxurl, $.extend({
				action : 'wp-link-ajax'
			}, params ), function(r) {
				var pagelinks = $panel.children('.wp-results-pagelinks');
				
				// Set results
				$panel.children('ul').html( wpLink.generateListMarkup( r['results'] ) );
				
				// Handle page links
				if ( r['page_links'] )
					pagelinks.html( r['page_links'] ).show();
				else
					pagelinks.hide();
				
				// Run callback
				if ( callback )
					callback( r['results'] );
			}, "json" );
		},
		
		generateListMarkup : function( results ) {
			var s = '';
			
			if ( ! results )
				return '<li class="no-matches-found unselectable"><span class="item-title"><em>' + wpLinkL10n.noMatchesFound + '</em></span></li>';
			
			$.each( results, function() {
				s+= '<li><input type="hidden" class="item-permalink" value="' + this['permalink'] + '" />';
				s+= '<span class="item-title">'
				s+= this['title'] ? this['title'] : '<em>'+ wpLinkL10n.untitled + '</em>';
				s+= '</span><span class="item-info">' + this['info'] + '</span>';
				s+= '</li>';
			});
			return s;
		}
	}
	
	$(document).ready( wpLink.init );
})(jQuery);