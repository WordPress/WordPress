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

	menuList, targetList;

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
		selectItem : function() {
			return this.each(function(){
				$(this).addClass('selected-menu-item')
					.next().children('input').attr('checked','checked');
			});
		},
		deselectItem : function() {
			return this.each(function(){
				$(this).removeClass('selected-menu-item')
					.next().children('input').removeAttr('checked');
			});
		},
		toggleItem : function() {
			return this.each(function(){
				var t = $(this);
				if( t.hasClass('selected-menu-item') )
					t.deselectItem();
				else
					t.selectItem();
			});
		}
	});

	return {

		// Functions that run on init.
		init : function() {
			menuList = $('#menu-to-edit');
			targetList = menuList;

			this.attachMenuEditListeners();

			this.attachMenuMetaListeners(document.getElementById('nav-menu-meta'));

			this.attachTabsPanelListeners();

			this.attachHomeLinkListener();

			if( menuList.length ) // If no menu, we're in the + tab.
				this.initSortables();

			this.initToggles();

			this.initTabManager();
			
			this.initAddMenuItemDraggables();
			
			this.checkForEmptyMenu();
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
				menuLeft = menuList.offset().left,
				newItem, transport;

			menuList.sortable({
				handle: '.menu-item-handle',
				placeholder: 'sortable-placeholder',
				start: function(e, ui) {
					var next, height, width, parent, children, maxChildDepth;

					transport = ui.item.children('.menu-item-transport');
					// Check if the item is in the menu, or new
					newItem = ( ui.helper.hasClass('new-menu-item') );
					
					// Set depths. currentDepth must be set before children are located.
					originalDepth = ( newItem ) ? 0 : ui.item.menuItemDepth();
					updateCurrentDepth(ui, originalDepth);
					
					if( ! newItem ) {
						// Attach child elements to parent
						// Skip the placeholder
						parent = ( ui.item.next()[0] == ui.placeholder[0] ) ? ui.item.next() : ui.item;
						children = parent.childMenuItems();
						transport.append( children );
					}

					// Now that the element is complete, we can update...
					updateDepthRange(ui);

					// Update the height of the placeholder to match the moving item.
					height = transport.outerHeight();
					// If there are children, account for distance between top of children and parent
					height += ( height > 0 ) ? (ui.placeholder.css('margin-top').slice(0, -2) * 1) : 0;
					height += ui.helper.outerHeight();
					height -= 2; // Subtract 2 for borders
					ui.placeholder.height(height);

					// Update the width of the placeholder to match the moving item.
					maxChildDepth = originalDepth;
					if( ! newItem ) { // Children have already been attached to new items
						children.each(function(){
							var depth = $(this).menuItemDepth();
							maxChildDepth = (depth > maxChildDepth) ? depth : maxChildDepth;
						});
					}
					width = ui.helper.find('.menu-item-handle').outerWidth(); // Get original width
					width += depthToPx(maxChildDepth - originalDepth); // Account for children
					width -= 2; // Subtract 2 for borders
					ui.placeholder.width(width);
				},
				stop: function(e, ui) {
					var children, depthChange = currentDepth - originalDepth;

					// Return child elements to the list
					children = transport.children().insertAfter(ui.item);
					
					if( newItem ) {
						// Remove the helper item
						ui.item.remove();
						// Update depth classes
						if( depthChange != 0 )
							children.shiftDepthClass( depthChange );
						// All new menu items must be updated
						children.updateParentMenuItemDBId();
					} else {
						// Update depth classes
						if( depthChange != 0 ) {
							ui.item.updateDepthClass( currentDepth );
							children.shiftDepthClass( depthChange );
						}
						// Update the item data.
						ui.item.updateParentMenuItemDBId();
					}
					// Update positions
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
					var depth = pxToDepth(ui.helper.offset().left - menuLeft);
					// Check and correct if depth is not within range.
					if ( depth < minDepth ) depth = minDepth;
					else if ( depth > maxDepth ) depth = maxDepth;

					if( depth != currentDepth )
						updateCurrentDepth(ui, depth);
				},
				receive: function(e, ui) {
					transport = ui.sender.children('.menu-item-transport');
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
		
		initAddMenuItemDraggables : function() {
			var menuItems = $('.potential-menu-item');
			menuItems.click(function(e){
				$(this).toggleItem();
			}).children().draggable({
				helper: 'clone',
				connectToSortable: 'ul#menu-to-edit',
				distance: 5,
				zIndex: 100,
				start: function(e, ui) {
					var target = $(e.target),
					 	item = target.parent(),
						li = item.parent(),
						items;
					
					// Make sure the item we're dragging is selected.
					item.selectItem();
					// Set us to be the ajax target
					targetList = target.children('.menu-item-transport');
					// Get all checked elements and assemble selected items.
					items = li.parents('.tabs-panel').find('.selected-menu-item').children().not( ui.helper ).clone();
					ui.helper.children('.additional-menu-items').append( items );
					// This class tells the sortables to treat it as a new item.
					ui.helper.addClass('new-menu-item');
					
					// CSS tweaks to remove some unnecessary items
					ui.helper.children('div').hide();
					items.first().css('margin-top', 0);

					// Make the items look like menu items
					items.children('div').addClass('menu-item-handle');
					ui.helper.children('div').addClass('hidden-handle');
					
					// Trigger the ajax
					li.parents('.inside').find('.add-to-menu input').click();
					
					// Lock dimensions
					ui.helper.width( ui.helper.width() );
					ui.helper.height( ui.helper.height() );
				},
				stop: function(e, ui) {
					// Reset the targetList and unselect the menu items
					targetList = menuList;
					$(e.target).parents('.tabs-panel').find('.selected-menu-item').deselectItem();
				}
			});
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

			// If a "Add to Menu" button was clicked, submit that metabox ajax style.
			$(formEL).click(function(e) {
				// based on the input, call that function
				var divcontainer = $(e.target).parent().parent().parent();
			
				if ( $(e.target).is('input') && $(e.target).hasClass('button-secondary') && !$(e.target).hasClass('quick-search-submit') ) {
					if ( $(divcontainer).hasClass('customlinkdiv') ) {
						that.addCustomLink();
					} else if ( $(divcontainer).hasClass('posttypediv') || $(divcontainer).hasClass('taxonomydiv') ) {
						that.addItemsToMenu( $(divcontainer).attr('id') );
					};
					return false;
				} else if ( $(e.target).is('input') && $(e.target).hasClass('quick-search-submit') ) {
					that.quickSearch( $(divcontainer).attr('id') );
					return false;
				};
			});
		},
		
		quickSearch : function(id) {
			var type = $('#' + id + ' .quick-search').attr('name'),
			q = $('#' + id + ' .quick-search').val(),
			menu = $('#menu').val(),
			nonce = $('#menu-settings-column-nonce').val(),
			params = {},
			that = this,
			processMethod = function(){};

			processMethod = that.processQuickSearchQueryResponse;

			params = {
				'action': 'menu-quick-search',
				'response-format': 'markup',
				'menu': menu,
				'menu-settings-column-nonce': nonce,
				'q': q,
				'type': type
			};

			$.post( ajaxurl, params, function(menuMarkup) {
				processMethod.call(that, menuMarkup, params);
			});
		},
		
		addCustomLink : function(url, label, addToTop) {
			var url = url || $('#custom-menu-item-url').val(),
			label = label || $('#custom-menu-item-name').val(),
			addToTop = addToTop || false,
			menu = $('#menu').val(),
			nonce = $('#menu-settings-column-nonce').val(),
			params = {},
			that = this,
			processMethod = function(){};
			
			if ( '' == url || 'http://' == url )
				return false;
			
			// Show the ajax spinner
			$('.customlinkdiv img.waiting').show();
			
			params = {
				'action': 'add-menu-item',
				'menu': menu,
				'menu-settings-column-nonce': nonce,
				'menu-item': {
					'-1': {
						'menu-item-type': 'custom',
						'menu-item-url': url,
						'menu-item-title': label
					}
				}
			};
			
			processMethod = addToTop ? that.addMenuItemToTop : that.addMenuItemToBottom;

			$.post( ajaxurl, params, function(menuMarkup) {
				processMethod.call(that, menuMarkup, params);
				
				// Remove the ajax spinner
				$('.customlinkdiv img.waiting').hide();

				// Reset the form
				wpNavMenu.resetCustomLinkForm();
			});
		},
		
		resetCustomLinkForm : function() {
			// set custom link form back to defaults
			$('#custom-menu-item-name').val('').blur();
			$('#custom-menu-item-url').val('http://');
		},
		
		attachHomeLinkListener : function() {
			$('.add-home-link', '.customlinkdiv').click(function(e) {
				wpNavMenu.addCustomLink( navMenuL10n.homeurl, navMenuL10n.home, true);
				return false;
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
					var selectAreaMatch = /#(.*)$/.exec(e.target.href), items;
					if ( selectAreaMatch && selectAreaMatch[1] ) {
						items = $('#' + selectAreaMatch[1] + ' .tabs-panel-active .potential-menu-item');
						if( items.length === items.filter('.selected-menu-item').length )
							items.deselectItem();
						else
							items.selectItem();
						return false;
					}
				}
			});
		},

		initTabManager : function() {
			var fixed = $('.nav-tabs-wrapper'),
				fluid = fixed.children('.nav-tabs'),
				active = fluid.children('.nav-tab-active'),
				tabs = fluid.children('.nav-tab'),
				tabsWidth = 0,
				fixedRight, fixedLeft,
				arrowLeft, arrowRight
				resizing = false;

			function resetMenuTabs() {
				fixedLeft = fixed.offset().left;
				fixedRight = fixedLeft + fixed.width();
				active.makeTabVisible();
			}

			$.fn.extend({
				makeTabVisible : function() {
					var t = this.eq(0), left, right;
					if( ! t.length ) return;
					left = t.offset().left;
					right = left + t.outerWidth();
					if( right > fixedRight )
						fluid.animate({ 'margin-left' :  "+=" + (fixedRight - right) + 'px', }, 'fast');
					else if ( left < fixedLeft )
						fluid.animate({ 'margin-left' :  "-=" + (left - fixedLeft) + 'px', }, 'fast');
					return t;
				},
				isTabVisible : function() {
					var t = this.eq(0),
						left = t.offset().left,
						right = left + t.outerWidth();
					return ( right <= fixedRight && left >= fixedLeft ) ? true : false;
				}
			});

			// Find the width of all tabs
			tabs.each(function(){
				tabsWidth += $(this).outerWidth(true);
			});

			// Check if we need the tab manager
			if( tabsWidth <= fixed.width()
				- fluid.css('padding-left').slice(0,-2)
				- fluid.css('padding-right').slice(0,-2) )
				return;

			// Set up right margin for overflow, unset padding
			fluid.css({
				'margin-right'  : (-1 * tabsWidth) + 'px',
				'padding' : 0,
			});

			// Build tab navigation
			arrowLeft = $('<div class="nav-tabs-arrow nav-tabs-arrow-left"><a>&laquo;</a></div>');
			arrowRight = $('<div class="nav-tabs-arrow nav-tabs-arrow-right"><a>&raquo;</a></div>');
			// Attach to the document
			fixed.wrap('<div class="nav-tabs-nav"/>').parent().prepend( arrowLeft ).append( arrowRight );

			// Set the menu tabs
			resetMenuTabs();
			// Make sure the tabs reset on resize
			$(window).resize(function() {
				if( resizing ) return;
				resizing = true;
				setTimeout(function(){
					resetMenuTabs();
					resizing = false;
				}, 1000);
			});

			// Build arrow functions
			$.each([{
					arrow : arrowLeft,
					next : "next",
					last : "first",
					operator : "+=",
				},{
					arrow : arrowRight,
					next : "prev",
					last : "last",
					operator : "-=",
				}], function(){
				var that = this;
				this.arrow.mousedown(function(){
					var last = tabs[that.last](),
						fn = function() {
							if( ! last.isTabVisible() )
								fluid.animate({ 'margin-left' :  that.operator + '90px', }, 300, "linear", fn);
						};
						fn();
				}).mouseup(function(){
					var tab, next;
					fluid.stop(true);
					tab = tabs[that.last]();
					while( (next = tab[that.next]()) && next.length && ! next.isTabVisible() ) {
						tab = next;
					}
					tab.makeTabVisible();
				});
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
			if ( confirm( navMenuL10n.warnDeleteMenu ) )
				return true;
			else
				return false;
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
		 * Adds menu items to the menu.
		 *
		 * @param string id The id of the metabox
		 */
		addItemsToMenu : function(id, addToTop) {
			var items = $( '.tabs-panel-active .categorychecklist li input:checked', '#' + id),
			menu = $('#menu').val(),
			nonce = $('#menu-settings-column-nonce').val(),
			params = {},
			that = this,
			addToTop = addToTop || false,
			processMethod = function(){},
			re = new RegExp('menu-item\\[(\[^\\]\]*)');
			
			processMethod = addToTop ? that.addMenuItemToTop : that.addMenuItemToBottom;
			
			// If no items are checked, bail.
			if ( !items.length )
				return false;
			
			// Show the ajax spinner
			$('#' + id + ' img.waiting').show();

			// do stuff
			$(items).each(function(){
				listItemDBIDMatch = re.exec( $(this).attr('name') );
				listItemDBID = 'undefined' == typeof listItemDBIDMatch[1] ? 0 : parseInt(listItemDBIDMatch[1], 10);
				listItemData = getListDataFromID(listItemDBID);

				params = {
					'action': 'add-menu-item',
					'menu': menu,
					'menu-settings-column-nonce': nonce,
					'menu-item': {}
				};

				params['menu-item'][listItemDBID] = listItemData;

				$.post( ajaxurl, params, function(menuMarkup) {
					processMethod.call(that, menuMarkup, params);
				});

				// Uncheck the item
				$(this).parent().prev().deselectItem();
			});

			// Remove the ajax spinner
			$('#' + id + ' img.waiting').hide();
		},

		/**
		 * Process the add menu item request response into menu list item.
		 *
		 * @param string menuMarkup The text server response of menu item markup.
		 * @param object req The request arguments.
		 */
		addMenuItemToBottom : function( menuMarkup, req ) {
			$(menuMarkup).hideAdvancedMenuItemFields().appendTo( targetList );
		},
		
		addMenuItemToTop : function( menuMarkup, req ) {
			$(menuMarkup).hideAdvancedMenuItemFields().prependTo( targetList );
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
			var that = this;

			el.addClass('deleting').fadeOut( 350 , function() {
				el.remove();
				children.shiftDepthClass(-1).updateParentMenuItemDBId();
				recalculateMenuItemPositions();
				that.checkForEmptyMenu();
			});
		},
		
		checkForEmptyMenu : function() {
			if( menuList.children().length ) return;
			menuList.height(80).one('sortstop', function(){
				$(this).height('auto');
			});
		}
	}
}

var wpNavMenu = new WPNavMenuHandler(jQuery);

jQuery(function() {
	wpNavMenu.init();
});
