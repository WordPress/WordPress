(function($){
	$.widget('wp.wpTabs', {
		options: {},
		_create: function() {
			var self = this,
				ul = this.element,
				lis = ul.children();
			
			this.active = lis.filter('.wp-tab-active');
			// Calculate panel IDs
			lis.each(function() {
				var panel = self._getPanel( $(this) );
				if ( self.active[0] == this )
					panel.show();
				else
					panel.hide();
			});
			
			ul.delegate('li', 'click.wpTabs', function(e) {
				var li = $(this);
				
				// Prevent any child link from redirecting the page.
				e.preventDefault();
				// Deactivate previous tab.
				self._getPanel( self.active ).hide();
				self.active.removeClass('wp-tab-active');
				self._trigger("hide", e, self.widget() );
				
				// Activate current tab.
				self.active = li.addClass('wp-tab-active');
				self._getPanel( self.active ).show();
				self._trigger("show", e, self.widget() );
			});
		},
		widget: function() {
			return {
				ul: this.element,
				tab: this.active,
				panel: this._getPanel( this.active )
			};
		},
		_setPanel: function( $el ) {
			var panel = $( '#' + $el.children('.wp-tab-for-id').val() );
			$el.data( 'wp-tab-panel', panel );
			return panel;
		},
		_getPanel: function( $el ) {
			var panel = $el.data('wp-tab-panel');
			return ( !panel || !panel.length ) ? this._setPanel( $el ) : panel;
		}
	});
	// Create tab bars by default.
	$(function(){
		$('.wp-tab-bar').wpTabs();
	});
})(jQuery);

(function($){	
	var inputs = {}, panels, active, ed,
	wpLink = {
		init : function() {
			var e, etarget, eclass;
			// Init shared vars
			ed = tinyMCEPopup.editor;
			// Secondary options
			inputs.title = $('#link-title-field');
			// Advanced Options
			inputs.openInNewTab = $('#link-target-checkbox');
			// Types
			inputs.typeDropdown = $('#link-type');
			inputs.typeOptions = inputs.typeDropdown.find('option');
			
			panels = $('.link-panel');
			active = $('.link-panel-active');
			
			// Extract type names
			inputs.typeOptions.each( function(){
				var linkType = this.id.replace(/^link-option-id-/,''),
					parts = linkType.split('-');
				$(this).data( 'link-type', {
					full : linkType,
					type : parts[0],
					name : parts[1] || ''
				});
			});
			panels.each( function(){
				var linkType = this.id.replace(/^link-panel-id-/,''),
					parts = linkType.split('-');
				$(this).data( 'link-type', {
					full : linkType,
					type : parts[0],
					name : parts[1] || ''
				});
			});
			
			// Bind event handlers
			inputs.typeDropdown.change( wpLink.selectPanel );
			$('#wp-update').click( wpLink.update );
			$('#wp-cancel').click( function() { tinyMCEPopup.close(); } );
			$('.link-panel .wp-tab-bar').wpTabs('option', 'show', wpLink.maybeLoadPanel );
			$('.link-panel .wp-tab-panel').delegate('li', 'click', wpLink.selectInternalLink );
			$('.wp-tab-panel-pagelinks').delegate('a', 'click', wpLink.selectPageLink );
			$('.link-panel .link-search-field').keyup( wpLink.searchInternalLinks );
			
			// If link exists, select proper values.
			e = ed.dom.getParent(ed.selection.getNode(), 'A');
			if ( ! e )
				return;
			
			// @TODO: select proper panel/fill values when a link is edited
			active.find('input.url-field').val( e.href );
			inputs.title.val( ed.dom.getAttrib(e, 'title') );
			// Advanced Options
			
			if ( "_blank" == ed.dom.getAttrib(e, 'target') )
				inputs.openInNewTab.attr('checked','checked');
		},
		
		update : function() {
			var el,
				ed = tinyMCEPopup.editor,
				attrs = {
					title : inputs.title.val(),
					target : inputs.openInNewTab.attr('checked') ? '_blank' : ''
				}, defaultContent, e, b;
			
			if ( active.hasClass('link-panel-custom') ) {
				attrs.href = active.find('input.url-field').val();
				defaultContent = attrs.href;
			} else {
				el = active.find('li.selected:visible');
				if ( !el.length )
					return;
				
				attrs.href = el.children('input').val();
				defaultContent = el.text();
			}
			
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
		
		selectPanel : function( option ) {
			var sel = inputs.typeOptions.filter(':selected');
			
			if ( option.jquery ) {
				sel.removeAttr('selected');
				sel = option.attr('selected', 'selected');
			}
			
			active.removeClass('link-panel-active');
			active = $('#link-panel-id-' + sel.data('link-type').full ).addClass('link-panel-active');
			wpLink.maybeLoadPanel();
		},
		
		maybeLoadPanel : function() {
			var panel = active.find('.wp-tab-panel:visible');
			if ( panel.length && panel.find('.wp-tab-panel-loading').length )
				wpLink.linkPanelAJAX( panel );
		},
		
		linkPanelAJAX : function( $panel, params, callback ) {
			if ( ! $panel.hasClass('wp-tab-panel') )
				$panel = $panel.parents('.wp-tab-panel');
			
			if ( ! $panel.length )
				return;
				
			var query = $panel.children('.wp-tab-panel-query').val();
			
			wpLink.linkAJAX( $panel, $.extend({
				preset : query,
				page : 'all' == query ? 1 : 0
			}, params), function(r, lt) {
				var pagelinks = $panel.children('.wp-tab-panel-pagelinks');
				
				// Set results
				$panel.children('ul').html( wpLink.generateListMarkup( r['results'], lt ) );
				
				// Handle page links
				if ( r['page_links'] )
					pagelinks.html( r['page_links'] ).show();
				else
					pagelinks.hide();
				// Run callback
				if ( callback )
					callback(r, lt);
			})
		},
		
		selectInternalLink : function() {
			var t = $(this);
			if ( t.hasClass('unselectable') )
				return;
			t.siblings('.selected').removeClass('selected');
			t.addClass('selected');
		},
		
		selectPageLink : function(e) {
			var page = e.target.href.match(/page=(\d+)/);
			
			page = page ? page[1] : 1; // If there's no match, it's the first page.
			e.preventDefault(); // Prevent the link from redirecting.
			
			wpLink.linkPanelAJAX( $(this), { page : page });
		},
		
		searchInternalLinks : function() {
			var t = $(this),
				waiting = t.siblings('img.waiting').show();
				
			wpLink.linkPanelAJAX( t, { title : t.val() }, function(){ waiting.hide(); });
		},
		
		linkAJAX : function( el, params, callback ) {
			var linkType = el.parents('.link-panel').data('link-type');
			$.post( ajaxurl, $.extend({
				action : 'wp-link-ajax',
				type : linkType.type,
				name : linkType.name
			}, params ), function(r) {
				return callback(r, linkType); 
			}, "json" );
		},
		
		generateListMarkup : function( results, linkType ) {
			var s = '';
			
			if ( ! results )
				return '<li class="no-matches-found unselectable"><em>' + wpLinkL10n.noMatchesFound + '</em></li>';
			
			$.each( results, function() {
				s+= '<li id="link-to-' + linkType.full + '-' + this['ID'] + '">';
				s+= '<input type="hidden" value="' + this['permalink'] + '" />';
				s+= this['title'] ? this['title'] : '<em>'+ wpLinkL10n.untitled + '</em>';
				s+= '</li>';
			});
			return s;
		}
	}
	
	$(document).ready( wpLink.init );
})(jQuery);