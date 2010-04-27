/**
 * WordPress Administration Navigation Menu
 * Interface JS functions
 *
 * @version 2.0.0
 *
 * @package WordPress
 * @subpackage Administration
 */

var WPNavMenuHandler = function () {
	var $ = jQuery,
	activeHovering = false,
	currentDropzone = null,
	
	customLinkNameInput,
	customLinkURLInput,
	customLinkNameDefault,
	customLinkURLDefault,

	autoCompleteData = {},

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

	getParentMenuItemDBId = function() {
		var allInputs = this.getElementsByTagName('input'),
		i = allInputs.length,
		j,
		parentEl,
		parentInputs;

		while( i-- ) {
			if ( -1 != allInputs[i].name.indexOf('menu-item-parent-id[' + parseInt(this.id.replace('menu-item-', ''), 10) + ']') ) {
				/*  This LI element is not in a submenu */
				if ( ! this.parentNode.className || -1 == this.parentNode.className.indexOf('sub-menu') ) {
					allInputs[i].value = 0;

				/* This LI is in a submenu, so need to get the parent's object ID (which is different from the parent's DB ID, the ID in its attributes) */
				} else if ( 'LI' == this.parentNode.parentNode.nodeName && -1 != this.parentNode.parentNode.id.indexOf('menu-item-') )  {
					 parentEl = this.parentNode.parentNode;
					 parentInputs = parentEl.getElementsByTagName('input');
					 j = parentInputs.length;
					 while ( j-- ) {
						if ( parentInputs[j].name && -1 != parentInputs[j].name.indexOf('menu-item-object-id[' + parseInt(parentEl.id.replace('menu-item-', ''), 10) + ']') ) {
							allInputs[i].value = parseInt(parentInputs[j].value, 10);
							break;
						}
					 }
				}
				break;
			}
		}
	},

	makeDroppable = function(el) {
		var that = this;

		$(el).droppable({
			accept: '.menu li',
			tolerance: 'pointer',
			drop: function(e, ui) {
				that.eventOnDrop(ui.draggable[0], this, ui, e);
			},

			over: function(e,ui) {
				that.eventOnDragOver(ui.draggable[0], this, ui, e);
			},

			out: function(e, ui) {
				that.eventOnDragOut(ui.draggable[0], this, ui, e);
			}
		});
	},

	menuList,

	setupListItemsDragAndDrop = function(list) {
		if ( ! list )
			return;

		var menuListItems = list.getElementsByTagName('li'),
		i = menuListItems.length;
		
		while ( i-- )
			this.setupListItemDragAndDrop(menuListItems[i]);
	};

	return {
		
		// Functions that run on init.
		init : function() {
			menuList = document.getElementById('menu-to-edit');
			
			this.attachMenuEditListeners();

			this.attachMenuMetaListeners(document.getElementById('nav-menu-meta'));
			
			this.attachTabsPanelListeners();
			
			// init drag and drop
			setupListItemsDragAndDrop.call(this, menuList); 

			postboxes.add_postbox_toggles('nav-menus');
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
					}
				}
			});
		},

		attachMenuMetaListeners : function(formEL) {
			if ( ! formEL )
				return;

			var that = this;

			// set default value for custom link name
			customLinkNameInput = document.getElementById('custom-menu-item-name');
			customLinkURLInput = document.getElementById('custom-menu-item-url');

			if ( customLinkNameInput ) {
				customLinkNameDefault = 'undefined' != typeof customLinkNameInput.defaultValue ? customLinkNameInput.defaultValue : customLinkNameInput.getAttribute('value');
				customLinkURLDefault = 'undefined' != typeof customLinkURLInput.defaultValue ? customLinkURLInput.defaultValue : customLinkURLInput.getAttribute('value');
				$(customLinkNameInput).bind('focus', function(e) {
					this.value = customLinkNameDefault == this.value ? '' : this.value;
				});
				
				$(customLinkNameInput).bind('blur', function(e) {
					this.value = '' == this.value ? customLinkNameDefault : this.value;
				});
			}

			// auto-suggest for the quick-search boxes
			$('input.quick-search').each(function(i, el) {
				that.setupQuickSearchEventListeners(el); 
			});
			
			$(formEL).bind('submit', function(e) {
				return that.eventSubmitMetaForm.call(that, this, e);
			});
		},

		attachTabsPanelListeners : function() {
			$('#menu-settings-column').bind('click', function(e) {
				if ( e.target && e.target.className && -1 != e.target.className.indexOf('menu-tab-link') ) {
					var i = e.target.parentNode,
					activePanel,
					panelIdMatch = /#(.*)$/.exec(e.target.href),
					tabPanels;
					while ( ! i.className || -1 == i.className.indexOf('inside') ) {
						i = i.parentNode;
					}
					$('.tabs-panel', i).each(function() {
						if ( this.className )
							this.className = this.className.replace('tabs-panel-active', 'tabs-panel-inactive');
					});

					$('.tabs', i).each(function() {
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

		setupListItemDragAndDrop : function(el) {
			var defLists = el.getElementsByTagName('dl'),
			dropZone = this.makeListItemDropzone(el),
			i = defLists.length;

			makeDroppable.call(this, dropZone);
			this.makeListItemDraggable(el);

			while( i-- ) {
				makeDroppable.call(this, defLists[i]);
			}
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
				activeEdit = document.getElementById(matchedSection[1]);
				if ( activeEdit ) {
					if ( -1 != activeEdit.className.indexOf('menu-item-edit-inactive') ) {
						activeEdit.className = activeEdit.className.replace('menu-item-edit-inactive', 'menu-item-edit-active');
					} else { 
						activeEdit.className = activeEdit.className.replace('menu-item-edit-active', 'menu-item-edit-inactive');
					}
					return false;
				}
			}
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
		 * Callback for the drag over action when dragging a list item.
		 *
		 * @param object draggedEl The DOM element being dragged
		 * @param object dropEl The DOM element on top of which we're dropping.
		 */
		eventOnDragOver : function(draggedEl, dropEl) {
			activeHovering = true;
			currentDropzone = dropEl;
			dropEl.className += ' sortable-placeholder';
		},

		/**
		 * Callback for the drag out action when dragging a list item.
		 *
		 * @param object draggedEl The DOM element being dragged
		 * @param object dropEl The DOM element on top of which we're dropping.
		 */
		eventOnDragOut : function(draggedEl, dropEl) {
			activeHovering = false;

			/* delay the disappearance of the droppable area so it doesn't flicker in and out */
			(function(that) {
				setTimeout(function() {
					if ( that != currentDropzone || ( ! activeHovering && that.className && -1 != that.className.indexOf('sortable-placeholder') ) ) {
						that.className = that.className.replace(/sortable-placeholder/g, '');
					}
				}, 500);
			})(dropEl);
		},

		/**
		 * Callback for the drop action when dragging and dropping a list item.
		 *
		 * @param object draggedEl The DOM element being dragged (and now dropped)
		 * @param object dropEl The DOM element on top of which we're dropping.
		 */
		eventOnDrop : function(draggedEl, dropEl) {
			var dropIntoSublist = !! ( -1 == dropEl.className.indexOf('dropzone') ),
			subLists = dropEl.parentNode.getElementsByTagName('ul'),
			hasSublist = false,
			i = subLists.length,
			subList;

			activeHovering = false;
			
			dropEl.className = dropEl.className.replace(/sortable-placeholder/g, '');

			if ( dropIntoSublist ) {
				while ( i-- ) {
					if ( subLists[i] && 1 != subLists[i].className.indexOf('sub-menu') ) {
						hasSublist = true;
						subList = subLists[i];
					}
				}

				if ( ! hasSublist ) {
					subList = document.createElement('ul');
					subList.className = 'sub-menu';
					dropEl.parentNode.appendChild(subList);
				}

				subList.appendChild(draggedEl);
			} else {
				dropEl.parentNode.parentNode.insertBefore(draggedEl, dropEl.parentNode);
			}

			this.recalculateSortOrder(menuList);

			getParentMenuItemDBId.call(draggedEl); 
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

			thisForm.className = thisForm.className + ' processing',
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
				thisForm.className = thisForm.className.replace(/processing/g, '');
			});

			return false;
		},

		makeListItemDraggable : function(el) {
			// make menu item draggable
			$(el).draggable({
				handle: ' > dl',
				opacity: .8,
				addClasses: false,
				helper: 'clone',
				zIndex: 100
			});
		},

		/**
		 * Add the child element that acts as the dropzone for drag-n-drop.
		 * 
		 * @param object el The parent object to which we'll prepend the dropzone.
		 * @return object The dropzone DOM element.
		 */
		makeListItemDropzone : function(el) {
			if ( ! el )
				return false;
			var divs = el.getElementsByTagName('div'),
			i = divs.length,
			dropZone = document.createElement('div');

			while( i-- ) {
				if ( divs[i].className && -1 != divs[i].className.indexOf('dropzone') && ( el == divs[i].parentNode ) ) 
					return divs[i];
			}

			dropZone.className = 'dropzone';
			el.insertBefore(dropZone, el.firstChild);
			return dropZone;
		},

		/**
		 * Process the add menu item request response into menu list item.
		 *
		 * @param string menuMarkup The text server response of menu item markup.
		 * @param object req The request arguments.
		 */
		processAddMenuItemResponse : function( menuMarkup, req ) {
			if ( ! req )
				req = {};
			var dropZone,
			i,
			listElements,
			wrap = document.createElement('ul');

			wrap.innerHTML = menuMarkup;
			listElements = wrap.getElementsByTagName('li');
			i = listElements.length;
			while ( i-- ) {
				this.setupListItemDragAndDrop(listElements[i]);
				menuList.appendChild(listElements[i]);
			}

			/* set custom link form back to defaults */
			if ( customLinkNameInput && customLinkURLInput ) { 
				customLinkNameInput.value = customLinkNameDefault;
				customLinkURLInput.value = customLinkURLDefault; 
			}
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
				matched = /quick-search-posttype-([a-zA-Z_-]*)/.exec(req.type);
				if ( matched && matched[1] ) {
					resultList = document.getElementById(matched[1] + '-search-checklist');
					if ( resultList ) {
						i = items.length;
						while( i-- ) {
							resultList.appendChild(items[i]);
						}
					}
				}
			}
		},

		recalculateSortOrder : function(parentEl) {
			var allInputs = parentEl.getElementsByTagName('input'),
			i,
			j = 0;

			for( i = 0; i < allInputs.length; i++ ) {
				if ( allInputs[i].name && -1 != allInputs[i].name.indexOf('menu-item-position') ) {
					allInputs[i].value = ++j;
				}
			}
		},

		removeMenuItem : function(el) {
			if ( ! el ) 
				return false;

			var subMenus = el.getElementsByTagName('ul'),
			subs,
			i;

			if ( subMenus[0] ) {
				subs = subMenus[0].getElementsByTagName('li');
				for ( i = 0; i < subs.length; i++ ) {
					if ( subs[i].id && -1 != subs[i].id.indexOf('menu-item-') && subs[i].parentNode == subMenus[0] ) {
						el.parentNode.insertBefore(subs[i], el);
					}
				}
			}

			el.className += ' deleting';
			$(el).fadeOut( 350 , function() {
				this.parentNode.removeChild(this);	
			});
			
			this.recalculateSortOrder(menuList);
		}
	}
}

var wpNavMenu = new WPNavMenuHandler();

jQuery(function() {
	wpNavMenu.init();
});
