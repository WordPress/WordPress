/**
 * WordPress Administration Navigation Menu
 * Interface JS functions
 *
 * @version 2.0.0
 *
 * @package WordPress
 * @subpackage Administration
 */

var WPNavMenuHandler = function ($) {
	var autoCompleteData = {},
	
	menuItemDepthPerLevel = 30, // Do not use directly. Use depthToPx and pxToDepth instead.
	globalMaxDepth = 11,

	formatAutocompleteResponse = function( resultRow, pos, total, queryTerm ) {
		if ( resultRow && resultRow[0] ) {
			var data = $.parseJSON(resultRow[0]);
			if ( data.post_title ) {
				if ( data.ID && data.post_type )
					autoCompleteData[data.post_title] = {ID: data.ID, object_type: data.post_type};
				return data.post_title;
			}
		}
	},

	formatAutocompleteResult = function( resultRow, pos, total, queryTerm ) {
		if ( resultRow && resultRow[0] ) {
			var data = $.parseJSON(resultRow[0]);
			if ( data.post_title )
				return data.post_title;
		}
	},
	
	getListDataFromID = function(menuItemID, parentEl) {
		if ( ! menuItemID ) 
			return false;
		parentEl = parentEl || document;
		var fields = [
			'menu-item-db-id',
			'menu-item-object-id',
			'menu-item-object',
			'menu-item-parent-id',
			'menu-item-position',
			'menu-item-type',
			'menu-item-append',
			'menu-item-title',
			'menu-item-url',
			'menu-item-description',
			'menu-item-attr-title',
			'menu-item-target',
			'menu-item-classes',
			'menu-item-xfn'
		],
		itemData = {},
		inputs = parentEl.getElementsByTagName('input'),
		i = inputs.length,
		j,
		menuID = document.getElementById('nav-menu-meta-object-id').value;

		while ( i-- ) {
			j = fields.length;
			while ( j-- ) {
				if ( 
					inputs[i] &&
					inputs[i].name &&
					'menu-item[' + menuItemID + '][' + fields[j] + ']' == inputs[i].name 
				) {
					itemData[fields[j]] = inputs[i].value;
				}
			}
		}

		return itemData;
	},
	
	recalculateMenuItemPositions = function() {
		menuList.find('.menu-item-data-position').val( function(index) { return index + 1; } );
	},
	
	depthToPx = function(depth) {
		return depth * menuItemDepthPerLevel;
	},
	
	pxToDepth = function(px) {
		return Math.floor(px / menuItemDepthPerLevel);
	},

	menuList;
	
	// jQuery extensions
	$.fn.extend({
		menuItemDepth : function() {
			return pxToDepth( this.eq(0).css('margin-left').slice(0, -2) );
		},
		updateDepthClass : function(current, prev) {
			return this.each(function(){
				var t = $(this);
				prev = prev || t.menuItemDepth();
				$(this).removeClass('menu-item-depth-'+ prev )
					.addClass('menu-item-depth-'+ current );
			});
		},
		shiftDepthClass : function(change) {
			return this.each(function(){
				var t = $(this),
					depth = t.menuItemDepth();
				$(this).removeClass('menu-item-depth-'+ depth )
					.addClass('menu-item-depth-'+ (depth + change) );
			});
		},
		childMenuItems : function() {
			var result = $();
			this.each(function(){
				var t = $(this), depth = t.menuItemDepth(), next = t.next();
				while( next.length && next.menuItemDepth() > depth ) {
					result = result.add( next );
					next = next.next();
				}
			});
			return result;
		},
		updateParentMenuItemDBId : function() {
			return this.each(function(){
				var item = $(this),
					input = item.find('.menu-item-data-parent-id'),
					depth = item.menuItemDepth(),
					parent = item.prev();

				if( depth == 0 ) { // Item is on the top level, has no parent
					input.val(0);
				} else { // Find the parent item, and retrieve its object id.
					while( parent.menuItemDepth() != depth - 1 ) {
						parent = parent.prev();
					}
					input.val( parent.find('.menu-item-data-object-id').val() );
				}
			});
		},
		hideAdvancedMenuItemFields : function() {
			return this.each(function(){
				var that = $(this);
				$('.hide-column-tog').not(':checked').each(function(){
					that.find('.field-' + $(this).val() ).addClass('hidden-field');
				});
			});
		},
	});

	return {
		
		// Functions that run on init.
		init : function() {
			menuList = $('#menu-to-edit');
			
			this.attachMenuEditListeners();

			this.attachMenuMetaListeners(document.getElementById('nav-menu-meta'));
			
			this.attachTabsPanelListeners();
			
			if( menuList.length ) // If no menu, we're in the + tab.
				this.initSortables();

			this.initToggles();
		},
		
		initToggles : function() {
			// init postboxes
			postboxes.add_postbox_toggles('nav-menus');
			
			// adjust columns functions for menus UI
			columns.useCheckboxesForHidden();
			columns.checked = function(field) {
				$('.field-' + field).removeClass('hidden-field');
			}
			columns.unchecked = function(field) {
				$('.field-' + field).addClass('hidden-field');
			}
			// hide fields
			menuList.hideAdvancedMenuItemFields();
		},
		
		initSortables : function() {
			var currentDepth = 0, originalDepth, minDepth, maxDepth,
				menuLeft = menuList.offset().left;
			
			menuList.sortable({
				handle: ' > dl',
				placeholder: 'sortable-placeholder',
				start: function(e, ui) {
					var next, height, width, parent, children, maxChildDepth,
						transport = ui.item.children('.menu-item-transport');
					
					// Set depths
					originalDepth = ui.item.menuItemDepth();
					updateCurrentDepth(ui, originalDepth);
					
					// Attach child elements to parent
					// Skip the placeholder
					parent = ( ui.item.next()[0] == ui.placeholder[0] ) ? ui.item.next() : ui.item;
					children = parent.childMenuItems();
					transport.append( children );

					// Now that the element is complete, we can update...
					updateDepthRange(ui);
					
					// Update the height of the placeholder to match the moving item.
					height = transport.outerHeight();
					// If there are children, account for distance between top of children and parent
					height += ( height > 0 ) ? (ui.placeholder.css('margin-top').slice(0, -2) * 1) : 0;
					height += ui.item.outerHeight();
					height -= 2; // Subtract 2 for borders
					ui.placeholder.height(height);
					
					// Update the width of the placeholder to match the moving item.
					maxChildDepth = originalDepth;
					children.each(function(){
						var depth = $(this).menuItemDepth();
						maxChildDepth = (depth > maxChildDepth) ? depth : maxChildDepth;
					});
					width = ui.item.find('dl dt').outerWidth(); // Get original width
					width += depthToPx(maxChildDepth - originalDepth); // Account for children
					width -= 2; // Subtract 2 for borders
					ui.placeholder.width(width);
				},
				stop: function(e, ui) {
					var children, depthChange = currentDepth - originalDepth;
					
					// Return child elements to the list
					children = ui.item.children('.menu-item-transport').children().insertAfter(ui.item);
					
					// Update depth classes
					if( depthChange != 0 ) {
						ui.item.updateDepthClass( currentDepth );
						children.shiftDepthClass( depthChange );
					}
					// Finally, update the item/menu data.
					ui.item.updateParentMenuItemDBId();
					recalculateMenuItemPositions();
				},
				change: function(e, ui) {
					// Make sure the placeholder is inside the menu.
					// Otherwise fix it, or we're in trouble.
					if( ! ui.placeholder.parent().hasClass('menu') )
						ui.placeholder.appendTo(menuList);
						
					updateDepthRange(ui);
				},
				sort: function(e, ui) {
					var depth = pxToDepth(ui.item.offset().left - menuLeft);
					// Check and correct if depth is not within range.
					if ( depth < minDepth ) depth = minDepth;
					else if ( depth > maxDepth ) depth = maxDepth;
					
					if( depth != currentDepth )
						updateCurrentDepth(ui, depth);
				}
			});
			
			function updateDepthRange(ui) {
				var prev = ui.placeholder.prev(),
					next = ui.placeholder.next(), depth;
					
				// Make sure we don't select the moving item.
				if( prev[0] == ui.item[0] ) prev = prev.prev();	
				if( next[0] == ui.item[0] ) next = next.next();
				
				minDepth = (next.length) ? next.menuItemDepth() : 0;
				
				if( prev.length )
					maxDepth = ( (depth = prev.menuItemDepth() + 1) > globalMaxDepth ) ? globalMaxDepth : depth;
				else
					maxDepth = 0;
			}
			
			function updateCurrentDepth(ui, depth) {
				ui.placeholder.updateDepthClass( depth, currentDepth );
				currentDepth = depth;
			}
		},
		
		attachMenuEditListeners : function() {
			var that = this;
			$('#update-nav-menu').bind('click', function(e) {
				if ( e.target && e.target.className ) {
					if ( -1 != e.target.className.indexOf('item-edit') ) {
						return that.eventOnClickEditLink(e.target);
					} else if ( -1 != e.target.className.indexOf('menu-delete') ) {
						return that.eventOnClickMenuDelete(e.target);
					} else if ( -1 != e.target.className.indexOf('item-delete') ) {
						return that.eventOnClickMenuItemDelete(e.target);
					} else if ( -1 != e.target.className.indexOf('item-close') ) {
						return that.eventOnClickCloseLink(e.target);
					}
				}
			});
		},
		
		/**
		 * An interface for managing default values for input elements
		 * that is both JS and accessibility-friendly.
		 *
		 * Input elements that add the class 'input-with-default-title'
		 * will have their values set to the provided HTML title when empty.
		 */
		setupInputWithDefaultTitle : function() {
			var name = 'input-with-default-title';
			
			$('.' + name).each( function(){
				var $t = $(this), title = $t.attr('title'), val = $t.val();
				$t.data( name, title );
				
				if( '' == val ) $t.val( title );
				else if ( title == val ) return;
				else $t.removeClass( name );
			}).focus( function(){
				var $t = $(this);
				if( $t.val() == $t.data(name) )
					$t.val('').removeClass( name );
			}).blur( function(){
				var $t = $(this);
				if( '' == $t.val() )
					$t.val( $t.data(name) ).addClass( name );
			});
		},

		attachMenuMetaListeners : function(formEL) {
			if ( ! formEL )
				return;

			var that = this;
			this.setupInputWithDefaultTitle();
			
			// auto-suggest for the quick-search boxes
			$('input.quick-search').each(function(i, el) {
				that.setupQuickSearchEventListeners(el); 
			});
			
			$(formEL).bind('submit', function(e) {
				return that.eventSubmitMetaForm.call(that, this, e);
			});
			$(formEL).find('input:submit').click(function() { 
				$(this).siblings('img.waiting').show(); 
			});
		},

		attachTabsPanelListeners : function() {
			$('#menu-settings-column').bind('click', function(e) {
				if ( e.target && e.target.className && -1 != e.target.className.indexOf('nav-tab-link') ) {
					var activePanel,
					panelIdMatch = /#(.*)$/.exec(e.target.href),
					tabPanels,
					wrapper = $(e.target).parents('.inside').first()[0],
					inputs = wrapper ? wrapper.getElementsByTagName('input') : [],
					i = inputs.length;

					// upon changing tabs, we want to uncheck all checkboxes
					while( i-- ) 
						inputs[i].checked = false;

					$('.tabs-panel', wrapper).each(function() {
						if ( this.className )
							this.className = this.className.replace('tabs-panel-active', 'tabs-panel-inactive');
					});

					$('.tabs', wrapper).each(function() {
						this.className = this.className.replace('tabs', '');
					});

					e.target.parentNode.className += ' tabs';

					if ( panelIdMatch && panelIdMatch[1] ) {
						activePanel = document.getElementById(panelIdMatch[1]);
						if ( activePanel ) {
							activePanel.className = activePanel.className.replace('tabs-panel-inactive', 'tabs-panel-active');
						}
					}
					
					return false;
				} else if ( e.target && e.target.className && -1 != e.target.className.indexOf('select-all') ) {
					var selectAreaMatch = /#(.*)$/.exec(e.target.href);
					if ( selectAreaMatch && selectAreaMatch[1] ) {
						$('#' + selectAreaMatch[1] + ' .tabs-panel-active input[type=checkbox]').attr('checked', 'checked');
						return false;
					}
				}
			});
		},

		/**
		 * Set up quick-search input fields' events.
		 *
		 * @param object el The input element.
		 */
		setupQuickSearchEventListeners : function(el) {
			var that = this;
			$(el).autocomplete( ajaxurl + '?action=menu-quick-search&type=' + el.name, 
				{ 
					delay: 500, 
					formatItem: formatAutocompleteResponse,
					formatResult: formatAutocompleteResult,
					minchars: 2, 
					multiple: false 
				}
			).bind('blur', function(e) {
				var changedData = autoCompleteData[this.value],
				inputEl = this;
				if ( changedData ) {
					$.post( 
						ajaxurl + '?action=menu-quick-search&type=get-post-item&response-format=markup',
						changedData,
						function(r) {
							that.processQuickSearchQueryResponse.call(that, r, changedData); 
							autoCompleteData[inputEl.value] = false;
						}
					);
				}
			});
		},

		eventOnClickEditLink : function(clickedEl) {
			var activeEdit,
			matchedSection = /#(.*)$/.exec(clickedEl.href);
			if ( matchedSection && matchedSection[1] ) {
				activeEdit = $('#'+matchedSection[1]);
				if( 0 != activeEdit.length ) {
					if( activeEdit.hasClass('menu-item-edit-inactive') ) {
						activeEdit.slideDown('fast')
							.siblings('dl').andSelf()
							.removeClass('menu-item-edit-inactive')
							.addClass('menu-item-edit-active');
					} else {
						activeEdit.slideUp('fast')
							.siblings('dl').andSelf()
							.removeClass('menu-item-edit-active')
							.addClass('menu-item-edit-inactive');
					}
					return false;
				}
			}
		},
		
		eventOnClickCloseLink : function(clickedEl) {
			$(clickedEl).closest('.menu-item-settings').siblings('dl').find('.item-edit').click();
			return false;
		},
		
		eventOnClickMenuDelete : function(clickedEl) {
			// Delete warning AYS
			if ( confirm( navMenuL10n.warnDeleteMenu ) ) {
				return true;
			} else {
				return false;
			}
		},

		eventOnClickMenuItemDelete : function(clickedEl) {
			var itemID,
			matchedSection,
			that = this;

			// Delete warning AYS
			if ( confirm( navMenuL10n.warnDeleteMenuItem ) ) {
				matchedSection = /_wpnonce=([a-zA-Z0-9]*)$/.exec(clickedEl.href);
				if ( matchedSection && matchedSection[1] ) {
					itemID = parseInt(clickedEl.id.replace('delete-', ''), 10);	
					$.post(
						ajaxurl,
						{
							action:'delete-menu-item',
							'menu-item':itemID,
							'_wpnonce':matchedSection[1]
						},
						function (resp) {
							if ( '1' == resp )
								that.removeMenuItem(document.getElementById('menu-item-' + itemID));
						}
					);
					return false;	
				}
				return true;
			} else {
				return false;
			}
		},

		/**
		 * Callback for the meta form submit action listener.
		 *
		 * @param object thisForm The submitted form.
		 * @param object e The event object.
		 */
		eventSubmitMetaForm : function(thisForm, e) {
			var inputs = thisForm.getElementsByTagName('input'),
			i = inputs.length,
			j,
			listItemData,
			listItemDBID,
			listItemDBIDMatch,
			params = {},
			processMethod = function(){},
			re = new RegExp('menu-item\\[(\[^\\]\]*)');

			that = this;
			params['action'] = '';

			while ( i-- ) {
				if ( 	// we're submitting a checked item
					inputs[i].name &&
					-1 != inputs[i].name.indexOf('menu-item-object-id') &&
					inputs[i].checked ||
					( // or we're dealing with a custom link
						'undefined' != typeof inputs[i].id &&
						'custom-menu-item-url' == inputs[i].id &&
						'' != inputs[i].value &&
						'http://' != inputs[i].value
					)
				) {
					params['action'] = 'add-menu-item';
					processMethod = that.processAddMenuItemResponse;

					listItemDBIDMatch = re.exec(inputs[i].name);
					listItemDBID = 'undefined' == typeof listItemDBIDMatch[1] ? 0 : parseInt(listItemDBIDMatch[1], 10);
					listItemData = getListDataFromID(listItemDBID);
					
					for ( j in listItemData ) {
						params['menu-item[' + listItemDBID + '][' + j + ']'] = listItemData[j];
					}

					inputs[i].checked = false;

				// we're submitting a search term
				} else if (
					'' == params['action'] && // give precedence to adding items
					'' != inputs[i].value &&
					inputs[i].className &&
					-1 != inputs[i].className.search(/quick-search\b[^-]/)
				) {
					params['action'] = 'menu-quick-search';
					params['q'] = inputs[i].value;
					params['response-format'] = 'markup';
					params['type'] = inputs[i].name;
					processMethod = that.processQuickSearchQueryResponse;
				}
			}
			params['menu'] = thisForm.elements['menu'].value;
			params['menu-settings-column-nonce'] = thisForm.elements['menu-settings-column-nonce'].value;

			$.post( ajaxurl, params, function(menuMarkup) {
				processMethod.call(that, menuMarkup, params);	
				$(thisForm).find('img.waiting').hide();
			});

			return false;
		},

		/**
		 * Process the add menu item request response into menu list item.
		 *
		 * @param string menuMarkup The text server response of menu item markup.
		 * @param object req The request arguments.
		 */
		processAddMenuItemResponse : function( menuMarkup, req ) {
			$(menuMarkup).hideAdvancedMenuItemFields().appendTo( menuList );

			/* set custom link form back to defaults */
			$('#custom-menu-item-name').val('').blur();
			$('#custom-menu-item-url').val('http://');
		},

		/**
		 * Process the quick search response into a search result
		 * 
		 * @param string resp The server response to the query.
		 * @param object req The request arguments.
		 */
		processQuickSearchQueryResponse : function(resp, req) {
			if ( ! req )
				req = {};
			var wrap = document.createElement('ul'),
			form = document.getElementById('nav-menu-meta'),
			i,
			items,
			matched,
			message,
			newID,
			pattern = new RegExp('menu-item\\[(\[^\\]\]*)'),
			resultList;

			// make a unique DB ID number
			matched = pattern.exec(resp);
			if ( matched && matched[1] ) {
				newID = matched[1];
				while( form.elements['menu-item[' + newID + '][menu-item-type]'] ) {
					newID--;
				}

				if ( newID != matched[1] ) {
					resp = resp.replace(new RegExp('menu-item\\[' + matched[1] + '\\]', 'g'), 'menu-item[' + newID + ']');
				}
			}

			wrap.innerHTML = resp;
			
			items = wrap.getElementsByTagName('li');

			if ( items[0] && req.object_type ) {
				resultList = document.getElementById(req.object_type + '-search-checklist');
				if ( resultList ) {
					resultList.appendChild(items[0]);
				}
			} else if ( req.type ) {
				matched = /quick-search-(posttype|taxonomy)-([a-zA-Z_-]*)/.exec(req.type);
				if ( matched && matched[2] ) {
					resultList = document.getElementById(matched[2] + '-search-checklist');
					if ( resultList ) {
						i = items.length;
						if ( ! i ) {
							message = document.createElement('li');
							message.appendChild(document.createTextNode(navMenuL10n.noResultsFound));
							resultList.appendChild(message);
						}
						while( i-- ) {
							resultList.appendChild(items[i]);
						}
					}
				}
			}
		},

		removeMenuItem : function(el) {
			el = $(el)
			var children = el.childMenuItems();
			
			el.addClass('deleting').fadeOut( 350 , function() {
				el.remove();
				children.shiftDepthClass(-1).updateParentMenuItemDBId();
				recalculateMenuItemPositions();
			});
		}
	}
}

var wpNavMenu = new WPNavMenuHandler(jQuery);

jQuery(function() {
	wpNavMenu.init();
});
