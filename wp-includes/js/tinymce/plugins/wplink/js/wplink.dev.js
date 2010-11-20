var wpLink;

(function($){
	var inputs = {}, rivers = {}, ed, River, Query;
	
	wpLink = {
		timeToTriggerRiver: 150,
		minRiverAJAXDuration: 200,
		riverBottomThreshold: 5,
		lastSearch: '',
		init : function() {
			inputs.dialog = $('#wp-link');
			// URL
			inputs.url = $('#url-field');
			// Secondary options
			inputs.title = $('#link-title-field');
			// Advanced Options
			inputs.openInNewTab = $('#link-target-checkbox');
			inputs.search = $('#search-field');
			// Build Rivers
			rivers.search = new River( $('#search-results') );
			rivers.recent = new River( $('#most-recent-results') );
			rivers.elements = $('.query-results', inputs.dialog);

			// Bind event handlers
			$('#wp-link-update').click( wpLink.update );
			$('#wp-link-cancel').click( function() { tinyMCEPopup.close(); } );
			
			rivers.elements.delegate('li', 'click', wpLink.selectInternalLink )
			
			inputs.search.keyup( wpLink.searchInternalLinks );
			
			inputs.dialog.bind('dialogopen', wpLink.refresh);
		},

		refresh : function() {
			var e;
			ed = tinyMCEPopup.editor;
			
			// Clear previously selected links
			rivers.elements.find('.selected').removeClass('selected');
			// Clear fields and focus the URL field
			inputs.url.val('http://');
			inputs.title.val('');
			
			tinyMCEPopup.restoreSelection();
			// If link exists, select proper values.
			if ( e = ed.dom.getParent(ed.selection.getNode(), 'A') ) {
				// Set URL and description.
				inputs.url.val( e.href );
				inputs.title.val( ed.dom.getAttrib(e, 'title') );
				// Set open in new tab.
				if ( "_blank" == ed.dom.getAttrib(e, 'target') )
					inputs.openInNewTab.attr('checked','checked');
			}
			tinyMCEPopup.storeSelection();
			// If the focus is moved above the selection changes,
			// IE will show a flashing cursor over the dialog.
			inputs.url.focus();
			// Load the most recent results if this is the first time opening the panel.
			if ( ! rivers.recent.ul.children().length )
				rivers.recent.ajax();
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
			if ( ! attrs.href || attrs.href == 'http://' ) {
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

			children = $(e).children();
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

		searchInternalLinks : function() {
			var t = $(this), waiting,
				search = t.val();

			if ( search.length > 2 ) {
				rivers.recent.element.hide();
				rivers.search.element.show();

				// Don't search if the keypress didn't change the title.
				if ( wpLink.lastSearch == search )
					return;

				wpLink.lastSearch = search;
				waiting = t.siblings('img.waiting').show();

				rivers.search.change( search );
				rivers.search.ajax( function(){ waiting.hide(); });
			} else {
				rivers.search.element.hide();
				rivers.recent.element.show();
			}
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
	
	River = function( element, search ) {
		var self = this;
		this.element = element;
		this.ul = element.children('ul');
		this.waiting = element.find('.river-waiting');
		
		this.change( search );
		
		element.scroll( function(){ self.maybeLoad(); });
	};
	
	$.extend( River.prototype, {
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
			this.element.scrollTop(0);
		},
		process: function( results, params ) {
			var list = '', alt = true,
				firstPage = params.page == 1;

			if ( !results ) {
				if ( firstPage ) {
					list += '<li class="unselectable"><span class="item-title"><em>'
					+ wpLinkL10n.noMatchesFound
					+ '</em></span></li>';
				}
			} else {
				$.each( results, function() {
					list += alt ? '<li class="alternate">' : '<li>';
					list += '<input type="hidden" class="item-permalink" value="' + this['permalink'] + '" />';
					list += '<span class="item-title">';
					list += this['title'] ? this['title'] : '<em>'+ wpLinkL10n.untitled + '</em>';
					list += '</span><span class="item-info">' + this['info'] + '</span></li>';
					alt = ! alt;
				});
			}

			this.ul[ firstPage ? 'html' : 'append' ]( list );
		},
		maybeLoad: function() {
			var self = this,
				el = this.element,
				bottom = el.scrollTop() + el.height();

			if ( ! this.query.ready() || bottom < this.ul.height() - wpLink.riverBottomThreshold )
				return;

			setTimeout(function() {
				var newTop = el.scrollTop(),
					newBottom = newTop + el.height();

				if ( ! self.query.ready() || newBottom < self.ul.height() - wpLink.riverBottomThreshold )
					return;

				self.waiting.show();
				el.scrollTop( newTop + self.waiting.outerHeight() );

				self.ajax( function() { self.waiting.hide(); });
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
			return !( this.querying || this.allLoaded );
		},
		ajax: function( callback ) {
			var self = this,
				query = {
					action : 'wp-link-ajax',
					page : this.page
				};

			if ( this.search )
				query.search = this.search;

			this.querying = true;
			
			$.post( ajaxurl, query, function(r) {
				self.page++;
				self.querying = false;
				self.allLoaded = !r;
				callback( r, query );
			}, "json" );
		}
	});

	$(document).ready( wpLink.init );
})(jQuery);