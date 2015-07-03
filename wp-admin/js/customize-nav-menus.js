/* global _wpCustomizeNavMenusSettings, wpNavMenu, console */
( function( api, wp, $ ) {
	'use strict';

	/**
	 * Set up wpNavMenu for drag and drop.
	 */
	wpNavMenu.originalInit = wpNavMenu.init;
	wpNavMenu.options.menuItemDepthPerLevel = 20;
	wpNavMenu.options.sortableItems         = '> .customize-control-nav_menu_item';
	wpNavMenu.options.targetTolerance       = 10;
	wpNavMenu.init = function() {
		this.jQueryExtensions();
	};

	api.Menus = api.Menus || {};

	// Link settings.
	api.Menus.data = {
		nonce: '',
		itemTypes: {
			taxonomies: {},
			postTypes: {}
		},
		l10n: {},
		menuItemTransport: 'postMessage',
		phpIntMax: 0,
		defaultSettingValues: {
			nav_menu: {},
			nav_menu_item: {}
		}
	};
	if ( 'undefined' !== typeof _wpCustomizeNavMenusSettings ) {
		$.extend( api.Menus.data, _wpCustomizeNavMenusSettings );
	}

	/**
	 * Newly-created Nav Menus and Nav Menu Items have negative integer IDs which
	 * serve as placeholders until Save & Publish happens.
	 *
	 * @return {number}
	 */
	api.Menus.generatePlaceholderAutoIncrementId = function() {
		return -Math.ceil( api.Menus.data.phpIntMax * Math.random() );
	};

	/**
	 * wp.customize.Menus.AvailableItemModel
	 *
	 * A single available menu item model. See PHP's WP_Customize_Nav_Menu_Item_Setting class.
	 *
	 * @constructor
	 * @augments Backbone.Model
	 */
	api.Menus.AvailableItemModel = Backbone.Model.extend( $.extend(
		{
			id: null // This is only used by Backbone.
		},
		api.Menus.data.defaultSettingValues.nav_menu_item
	) );

	/**
	 * wp.customize.Menus.AvailableItemCollection
	 *
	 * Collection for available menu item models.
	 *
	 * @constructor
	 * @augments Backbone.Model
	 */
	api.Menus.AvailableItemCollection = Backbone.Collection.extend({
		model: api.Menus.AvailableItemModel,

		sort_key: 'order',

		comparator: function( item ) {
			return -item.get( this.sort_key );
		},

		sortByField: function( fieldName ) {
			this.sort_key = fieldName;
			this.sort();
		}
	});
	api.Menus.availableMenuItems = new api.Menus.AvailableItemCollection( api.Menus.data.availableMenuItems );

	/**
	 * wp.customize.Menus.AvailableMenuItemsPanelView
	 *
	 * View class for the available menu items panel.
	 *
	 * @constructor
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	api.Menus.AvailableMenuItemsPanelView = wp.Backbone.View.extend({

		el: '#available-menu-items',

		events: {
			'input #menu-items-search': 'debounceSearch',
			'keyup #menu-items-search': 'debounceSearch',
			'click #menu-items-search': 'debounceSearch',
			'focus .menu-item-tpl': 'focus',
			'click .menu-item-tpl': '_submit',
			'click #custom-menu-item-submit': '_submitLink',
			'keypress #custom-menu-item-name': '_submitLink',
			'keydown': 'keyboardAccessible'
		},

		// Cache current selected menu item.
		selected: null,

		// Cache menu control that opened the panel.
		currentMenuControl: null,
		debounceSearch: null,
		$search: null,
		searchTerm: '',
		rendered: false,
		pages: {},
		sectionContent: '',
		loading: false,

		initialize: function() {
			var self = this;

			this.$search = $( '#menu-items-search' );
			this.sectionContent = this.$el.find( '.accordion-section-content' );

			this.debounceSearch = _.debounce( self.search, 500 );

			_.bindAll( this, 'close' );

			// If the available menu items panel is open and the customize controls are
			// interacted with (other than an item being deleted), then close the
			// available menu items panel. Also close on back button click.
			$( '#customize-controls, .customize-section-back' ).on( 'click keydown', function( e ) {
				var isDeleteBtn = $( e.target ).is( '.item-delete, .item-delete *' ),
					isAddNewBtn = $( e.target ).is( '.add-new-menu-item, .add-new-menu-item *' );
				if ( $( 'body' ).hasClass( 'adding-menu-items' ) && ! isDeleteBtn && ! isAddNewBtn ) {
					self.close();
				}
			} );

			this.$el.on( 'input', '#custom-menu-item-name.invalid, #custom-menu-item-url.invalid', function() {
				$( this ).removeClass( 'invalid' );
			});

			// Load available items if it looks like we'll need them.
			api.panel( 'nav_menus' ).container.bind( 'expanded', function() {
				if ( ! self.rendered ) {
					self.initList();
					self.rendered = true;
				}
			});

			// Load more items.
			this.sectionContent.scroll( function() {
				var totalHeight = self.$el.find( '.accordion-section.open .accordion-section-content' ).prop( 'scrollHeight' ),
				    visibleHeight = self.$el.find( '.accordion-section.open' ).height();
				if ( ! self.loading && $( this ).scrollTop() > 3 / 4 * totalHeight - visibleHeight ) {
					var type = $( this ).data( 'type' ),
					    obj_type = $( this ).data( 'obj_type' );
					if ( 'search' === type ) {
						if ( self.searchTerm ) {
							self.doSearch( self.pages.search );
						}
					} else {
						self.loadItems( type, obj_type );
					}
				}
			});

			// Close the panel if the URL in the preview changes
			api.previewer.bind( 'url', this.close );
		},

		// Search input change handler.
		search: function( event ) {
			if ( ! event ) {
				return;
			}
			// Manual accordion-opening behavior.
			if ( this.searchTerm && ! $( '#available-menu-items-search' ).hasClass( 'open' ) ) {
				$( '#available-menu-items .accordion-section-content' ).slideUp( 'fast' );
				$( '#available-menu-items-search .accordion-section-content' ).slideDown( 'fast' );
				$( '#available-menu-items .accordion-section.open' ).removeClass( 'open' );
				$( '#available-menu-items-search' ).addClass( 'open' );
			}
			if ( '' === event.target.value ) {
				$( '#available-menu-items-search' ).removeClass( 'open' );
			}
			if ( this.searchTerm === event.target.value ) {
				return;
			}
			this.searchTerm = event.target.value;
			this.pages.search = 1;
			this.doSearch( 1 );
		},

		// Get search results.
		doSearch: function( page ) {
			var self = this, params,
			    $section = $( '#available-menu-items-search' ),
			    $content = $section.find( '.accordion-section-content' ),
			    itemTemplate = wp.template( 'available-menu-item' );

			if ( self.currentRequest ) {
				self.currentRequest.abort();
			}

			if ( page < 0 ) {
				return;
			} else if ( page > 1 ) {
				$section.addClass( 'loading-more' );
				$content.attr( 'aria-busy', 'true' );
				wp.a11y.speak( api.Menus.data.l10n.itemsLoadingMore );
			} else if ( '' === self.searchTerm ) {
				$content.html( '' );
				wp.a11y.speak( '' );
				return;
			}

			$section.addClass( 'loading' );
			self.loading = true;
			params = {
				'customize-menus-nonce': api.Menus.data.nonce,
				'wp_customize': 'on',
				'search': self.searchTerm,
				'page': page
			};

			self.currentRequest = wp.ajax.post( 'search-available-menu-items-customizer', params );

			self.currentRequest.done(function( data ) {
				var items;
				if ( 1 === page ) {
					// Clear previous results as it's a new search.
					$content.empty();
				}
				$section.removeClass( 'loading loading-more' );
				$content.attr( 'aria-busy', 'false' );
				$section.addClass( 'open' );
				self.loading = false;
				items = new api.Menus.AvailableItemCollection( data.items );
				self.collection.add( items.models );
				items.each( function( menuItem ) {
					$content.append( itemTemplate( menuItem.attributes ) );
				} );
				if ( 20 > items.length ) {
					self.pages.search = -1; // Up to 20 posts and 20 terms in results, if <20, no more results for either.
				} else {
					self.pages.search = self.pages.search + 1;
				}
				if ( items && page > 1 ) {
					wp.a11y.speak( api.Menus.data.l10n.itemsFoundMore.replace( '%d', items.length ) );
				} else if ( items && page === 1 ) {
					wp.a11y.speak( api.Menus.data.l10n.itemsFound.replace( '%d', items.length ) );
				}
			});

			self.currentRequest.fail(function( data ) {
				// data.message may be undefined, for example when typing slow and the request is aborted.
				if ( data.message ) {
					$content.empty().append( $( '<p class="nothing-found"></p>' ).text( data.message ) );
					wp.a11y.speak( data.message );
				}
				self.pages.search = -1;
			});

			self.currentRequest.always(function() {
				$section.removeClass( 'loading loading-more' );
				$content.attr( 'aria-busy', 'false' );
				self.loading = false;
				self.currentRequest = null;
			});
		},

		// Render the individual items.
		initList: function() {
			var self = this;

			// Render the template for each item by type.
			_.each( api.Menus.data.itemTypes, function( typeObjects, type ) {
				_.each( typeObjects, function( typeObject, slug ) {
					if ( 'postTypes' === type ) {
						type = 'post_type';
					} else if ( 'taxonomies' === type ) {
						type = 'taxonomy';
					}
					self.pages[ slug ] = 0; // @todo should prefix with type
					self.loadItems( slug, type );
				} );
			} );
		},

		// Load available menu items.
		loadItems: function( type, obj_type ) {
			var self = this, params, request, itemTemplate;
			itemTemplate = wp.template( 'available-menu-item' );

			if ( 0 > self.pages[type] ) {
				return;
			}
			$( '#available-menu-items-' + type + ' .accordion-section-title' ).addClass( 'loading' );
			self.loading = true;
			params = {
				'customize-menus-nonce': api.Menus.data.nonce,
				'wp_customize': 'on',
				'type': type,
				'obj_type': obj_type,
				'page': self.pages[ type ]
			};
			request = wp.ajax.post( 'load-available-menu-items-customizer', params );

			request.done(function( data ) {
				var items, typeInner;
				items = data.items;
				if ( 0 === items.length ) {
					self.pages[ type ] = -1;
					return;
				}
				items = new api.Menus.AvailableItemCollection( items ); // @todo Why is this collection created and then thrown away?
				self.collection.add( items.models );
				typeInner = $( '#available-menu-items-' + type + ' .accordion-section-content' );
				items.each(function( menu_item ) {
					typeInner.append( itemTemplate( menu_item.attributes ) );
				});
				self.pages[ type ] = self.pages[ type ] + 1;
			});
			request.fail(function( data ) {
				if ( typeof console !== 'undefined' && console.error ) {
					console.error( data );
				}
			});
			request.always(function() {
				$( '#available-menu-items-' + type + ' .accordion-section-title' ).removeClass( 'loading' );
				self.loading = false;
			});
		},

		// Adjust the height of each section of items to fit the screen.
		itemSectionHeight: function() {
			var sections, totalHeight, accordionHeight, diff;
			totalHeight = window.innerHeight;
			sections = this.$el.find( '.accordion-section-content' );
			accordionHeight =  46 * ( 1 + sections.length ) - 16; // Magic numbers.
			diff = totalHeight - accordionHeight;
			if ( 120 < diff && 290 > diff ) {
				sections.css( 'max-height', diff );
			} else if ( 120 >= diff ) {
				this.$el.addClass( 'allow-scroll' );
			}
		},

		// Highlights a menu item.
		select: function( menuitemTpl ) {
			this.selected = $( menuitemTpl );
			this.selected.siblings( '.menu-item-tpl' ).removeClass( 'selected' );
			this.selected.addClass( 'selected' );
		},

		// Highlights a menu item on focus.
		focus: function( event ) {
			this.select( $( event.currentTarget ) );
		},

		// Submit handler for keypress and click on menu item.
		_submit: function( event ) {
			// Only proceed with keypress if it is Enter or Spacebar
			if ( 'keypress' === event.type && ( 13 !== event.which && 32 !== event.which ) ) {
				return;
			}

			this.submit( $( event.currentTarget ) );
		},

		// Adds a selected menu item to the menu.
		submit: function( menuitemTpl ) {
			var menuitemId, menu_item;

			if ( ! menuitemTpl ) {
				menuitemTpl = this.selected;
			}

			if ( ! menuitemTpl || ! this.currentMenuControl ) {
				return;
			}

			this.select( menuitemTpl );

			menuitemId = $( this.selected ).data( 'menu-item-id' );
			menu_item = this.collection.findWhere( { id: menuitemId } );
			if ( ! menu_item ) {
				return;
			}

			this.currentMenuControl.addItemToMenu( menu_item.attributes );

			$( menuitemTpl ).find( '.menu-item-handle' ).addClass( 'item-added' );
		},

		// Submit handler for keypress and click on custom menu item.
		_submitLink: function( event ) {
			// Only proceed with keypress if it is Enter.
			if ( 'keypress' === event.type && 13 !== event.which ) {
				return;
			}

			this.submitLink();
		},

		// Adds the custom menu item to the menu.
		submitLink: function() {
			var menuItem,
				itemName = $( '#custom-menu-item-name' ),
				itemUrl = $( '#custom-menu-item-url' );

			if ( ! this.currentMenuControl ) {
				return;
			}

			if ( '' === itemName.val() ) {
				itemName.addClass( 'invalid' );
				return;
			} else if ( '' === itemUrl.val() || 'http://' === itemUrl.val() ) {
				itemUrl.addClass( 'invalid' );
				return;
			}

			menuItem = {
				'title': itemName.val(),
				'url': itemUrl.val(),
				'type': 'custom',
				'type_label': api.Menus.data.l10n.custom_label,
				'object': ''
			};

			this.currentMenuControl.addItemToMenu( menuItem );

			// Reset the custom link form.
			itemUrl.val( 'http://' );
			itemName.val( '' );
		},

		// Opens the panel.
		open: function( menuControl ) {
			this.currentMenuControl = menuControl;

			this.itemSectionHeight();

			$( 'body' ).addClass( 'adding-menu-items' );

			// Collapse all controls.
			_( this.currentMenuControl.getMenuItemControls() ).each( function( control ) {
				control.collapseForm();
			} );

			this.$el.find( '.selected' ).removeClass( 'selected' );

			this.$search.focus();
		},

		// Closes the panel
		close: function( options ) {
			options = options || {};

			if ( options.returnFocus && this.currentMenuControl ) {
				this.currentMenuControl.container.find( '.add-new-menu-item' ).focus();
			}

			this.currentMenuControl = null;
			this.selected = null;

			$( 'body' ).removeClass( 'adding-menu-items' );
			$( '#available-menu-items .menu-item-handle.item-added' ).removeClass( 'item-added' );

			this.$search.val( '' );
		},

		// Add a few keyboard enhancements to the panel.
		keyboardAccessible: function( event ) {
			var isEnter = ( 13 === event.which ),
				isEsc = ( 27 === event.which ),
				isBackTab = ( 9 === event.which && event.shiftKey ),
				isSearchFocused = $( event.target ).is( this.$search );

			// If enter pressed but nothing entered, don't do anything
			if ( isEnter && ! this.$search.val() ) {
				return;
			}

			if ( isSearchFocused && isBackTab ) {
				this.currentMenuControl.container.find( '.add-new-menu-item' ).focus();
				event.preventDefault(); // Avoid additional back-tab.
			} else if ( isEsc ) {
				this.close( { returnFocus: true } );
			}
		}
	});

	/**
	 * wp.customize.Menus.MenusPanel
	 *
	 * Customizer panel for menus. This is used only for screen options management.
	 * Note that 'menus' must match the WP_Customize_Menu_Panel::$type.
	 *
	 * @constructor
	 * @augments wp.customize.Panel
	 */
	api.Menus.MenusPanel = api.Panel.extend({

		attachEvents: function() {
			api.Panel.prototype.attachEvents.call( this );

			var panel = this,
				panelMeta = panel.container.find( '.panel-meta' ),
				help = panelMeta.find( '.customize-help-toggle' ),
				content = panelMeta.find( '.customize-panel-description' ),
				options = $( '#screen-options-wrap' ),
				button = panelMeta.find( '.customize-screen-options-toggle' );
			button.on( 'click', function() {
				// Hide description
				if ( content.not( ':hidden' ) ) {
					content.slideUp( 'fast' );
					help.attr( 'aria-expanded', 'false' );
				}

				if ( 'true' === button.attr( 'aria-expanded' ) ) {
					button.attr( 'aria-expanded', 'false' );
					panelMeta.removeClass( 'open' );
					panelMeta.removeClass( 'active-menu-screen-options' );
					options.slideUp( 'fast' );
				} else {
					button.attr( 'aria-expanded', 'true' );
					panelMeta.addClass( 'open' );
					panelMeta.addClass( 'active-menu-screen-options' );
					options.slideDown( 'fast' );
				}

				return false;
			} );

			// Help toggle
			help.on( 'click', function() {
				if ( 'true' === button.attr( 'aria-expanded' ) ) {
					button.attr( 'aria-expanded', 'false' );
					help.attr( 'aria-expanded', 'true' );
					panelMeta.addClass( 'open' );
					panelMeta.removeClass( 'active-menu-screen-options' );
					options.slideUp( 'fast' );
					content.slideDown( 'fast' );
				}
			} );
		},

		/**
		 * Show/hide/save screen options (columns). From common.js.
		 */
		ready: function() {
			var panel = this;
			this.container.find( '.hide-column-tog' ).click( function() {
				var $t = $( this ), column = $t.val();
				if ( $t.prop( 'checked' ) ) {
					panel.checked( column );
				} else {
					panel.unchecked( column );
				}

				panel.saveManageColumnsState();
			});
			this.container.find( '.hide-column-tog' ).each( function() {
			var $t = $( this ), column = $t.val();
				if ( $t.prop( 'checked' ) ) {
					panel.checked( column );
				} else {
					panel.unchecked( column );
				}
			});
		},

		saveManageColumnsState: function() {
			var hidden = this.hidden();
			$.post( wp.ajax.settings.url, {
				action: 'hidden-columns',
				hidden: hidden,
				screenoptionnonce: $( '#screenoptionnonce' ).val(),
				page: 'nav-menus'
			});
		},

		checked: function( column ) {
			this.container.addClass( 'field-' + column + '-active' );
		},

		unchecked: function( column ) {
			this.container.removeClass( 'field-' + column + '-active' );
		},

		hidden: function() {
			this.hidden = function() {
				return $( '.hide-column-tog' ).not( ':checked' ).map( function() {
					var id = this.id;
					return id.substring( id, id.length - 5 );
				}).get().join( ',' );
			};
		}
	} );

	/**
	 * wp.customize.Menus.MenuSection
	 *
	 * Customizer section for menus. This is used only for lazy-loading child controls.
	 * Note that 'nav_menu' must match the WP_Customize_Menu_Section::$type.
	 *
	 * @constructor
	 * @augments wp.customize.Section
	 */
	api.Menus.MenuSection = api.Section.extend({

		/**
		 * @since Menu Customizer 0.3
		 *
		 * @param {String} id
		 * @param {Object} options
		 */
		initialize: function( id, options ) {
			var section = this;
			api.Section.prototype.initialize.call( section, id, options );
			section.deferred.initSortables = $.Deferred();
		},

		/**
		 *
		 */
		ready: function() {
			var section = this;

			if ( 'undefined' === typeof section.params.menu_id ) {
				throw new Error( 'params.menu_id was not defined' );
			}

			/*
			 * Since newly created sections won't be registered in PHP, we need to prevent the
			 * preview's sending of the activeSections to result in this control
			 * being deactivated when the preview refreshes. So we can hook onto
			 * the setting that has the same ID and its presence can dictate
			 * whether the section is active.
			 */
			section.active.validate = function() {
				if ( ! api.has( section.id ) ) {
					return false;
				}
				return !! api( section.id ).get();
			};

			section.populateControls();

			section.navMenuLocationSettings = {};
			section.assignedLocations = new api.Value( [] );

			api.each(function( setting, id ) {
				var matches = id.match( /^nav_menu_locations\[(.+?)]/ );
				if ( matches ) {
					section.navMenuLocationSettings[ matches[1] ] = setting;
					setting.bind( function() {
						section.refreshAssignedLocations();
					});
				}
			});

			section.assignedLocations.bind(function( to ) {
				section.updateAssignedLocationsInSectionTitle( to );
			});

			section.refreshAssignedLocations();
		},

		populateControls: function() {
			var section = this, menuNameControlId, menuControl, menuNameControl;

			// Add the control for managing the menu name.
			menuNameControlId = section.id + '[name]';
			menuNameControl = api.control( menuNameControlId );
			if ( ! menuNameControl ) {
				menuNameControl = new api.controlConstructor.nav_menu_name( menuNameControlId, {
					params: {
						type: 'nav_menu_name',
						content: '<li id="customize-control-' + section.id.replace( '[', '-' ).replace( ']', '' ) + '-name" class="customize-control customize-control-nav_menu_name"></li>', // @todo core should do this for us
						label: '',
						active: true,
						section: section.id,
						priority: 0,
						settings: {
							'default': section.id
						}
					}
				} );
				api.control.add( menuNameControl.id, menuNameControl );
				menuNameControl.active.set( true );
			}

			// Add the menu control.
			menuControl = api.control( section.id );
			if ( ! menuControl ) {
				menuControl = new api.controlConstructor.nav_menu( section.id, {
					params: {
						type: 'nav_menu',
						content: '<li id="customize-control-' + section.id.replace( '[', '-' ).replace( ']', '' ) + '" class="customize-control customize-control-nav_menu"></li>', // @todo core should do this for us
						section: section.id,
						priority: 999,
						active: true,
						settings: {
							'default': section.id
						},
						menu_id: section.params.menu_id
					}
				} );
				api.control.add( menuControl.id, menuControl );
				menuControl.active.set( true );
			}

		},

		/**
		 *
		 */
		refreshAssignedLocations: function() {
			var section = this,
				menuTermId = section.params.menu_id,
				currentAssignedLocations = [];
			_.each( section.navMenuLocationSettings, function( setting, themeLocation ) {
				if ( setting() === menuTermId ) {
					currentAssignedLocations.push( themeLocation );
				}
			});
			section.assignedLocations.set( currentAssignedLocations );
		},

		/**
		 * @param {array} themeLocations
		 */
		updateAssignedLocationsInSectionTitle: function( themeLocations ) {
			var section = this,
				$title;

			$title = section.container.find( '.accordion-section-title:first' );
			$title.find( '.menu-in-location' ).remove();
			_.each( themeLocations, function( themeLocation ) {
				var $label = $( '<span class="menu-in-location"></span>' );
				$label.text( api.Menus.data.l10n.menuLocation.replace( '%s', themeLocation ) );
				$title.append( $label );
			});

			section.container.toggleClass( 'assigned-to-menu-location', 0 !== themeLocations.length );

		},

		onChangeExpanded: function( expanded, args ) {
			var section = this;

			if ( expanded ) {
				wpNavMenu.menuList = section.container.find( '.accordion-section-content:first' );
				wpNavMenu.targetList = wpNavMenu.menuList;

				// Add attributes needed by wpNavMenu
				$( '#menu-to-edit' ).removeAttr( 'id' );
				wpNavMenu.menuList.attr( 'id', 'menu-to-edit' ).addClass( 'menu' );

				_.each( api.section( section.id ).controls(), function( control ) {
					if ( 'nav_menu_item' === control.params.type ) {
						control.actuallyEmbed();
					}
				} );

				if ( 'resolved' !== section.deferred.initSortables.state() ) {
					wpNavMenu.initSortables(); // Depends on menu-to-edit ID being set above.
					section.deferred.initSortables.resolve( wpNavMenu.menuList ); // Now MenuControl can extend the sortable.

					// @todo Note that wp.customize.reflowPaneContents() is debounced, so this immediate change will show a slight flicker while priorities get updated.
					api.control( 'nav_menu[' + String( section.params.menu_id ) + ']' ).reflowMenuItems();
				}
			}
			api.Section.prototype.onChangeExpanded.call( section, expanded, args );
		}
	});

	/**
	 * wp.customize.Menus.NewMenuSection
	 *
	 * Customizer section for new menus.
	 * Note that 'new_menu' must match the WP_Customize_New_Menu_Section::$type.
	 *
	 * @constructor
	 * @augments wp.customize.Section
	 */
	api.Menus.NewMenuSection = api.Section.extend({

		/**
		 * Add behaviors for the accordion section.
		 *
		 * @since Menu Customizer 0.3
		 */
		attachEvents: function() {
			var section = this;
			this.container.on( 'click', '.add-menu-toggle', function() {
				if ( section.expanded() ) {
					section.collapse();
				} else {
					section.expand();
				}
			});
		},

		/**
		 * Update UI to reflect expanded state.
		 *
		 * @since 4.1.0
		 *
		 * @param {Boolean} expanded
		 */
		onChangeExpanded: function( expanded ) {
			var section = this,
				button = section.container.find( '.add-menu-toggle' ),
				content = section.container.find( '.new-menu-section-content' ),
				customizer = section.container.closest( '.wp-full-overlay-sidebar-content' );
			if ( expanded ) {
				button.addClass( 'open' );
				button.attr( 'aria-expanded', 'true' );
				content.slideDown( 'fast', function() {
					customizer.scrollTop( customizer.height() );
				});
			} else {
				button.removeClass( 'open' );
				button.attr( 'aria-expanded', 'false' );
				content.slideUp( 'fast' );
				content.find( '.menu-name-field' ).removeClass( 'invalid' );
			}
		}
	});

	/**
	 * wp.customize.Menus.MenuLocationControl
	 *
	 * Customizer control for menu locations (rendered as a <select>).
	 * Note that 'nav_menu_location' must match the WP_Customize_Nav_Menu_Location_Control::$type.
	 *
	 * @constructor
	 * @augments wp.customize.Control
	 */
	api.Menus.MenuLocationControl = api.Control.extend({
		initialize: function( id, options ) {
			var control = this,
				matches = id.match( /^nav_menu_locations\[(.+?)]/ );
			control.themeLocation = matches[1];
			api.Control.prototype.initialize.call( control, id, options );
		},

		ready: function() {
			var control = this, navMenuIdRegex = /^nav_menu\[(-?\d+)]/;

			// @todo It would be better if this was added directly on the setting itself, as opposed to the control.
			control.setting.validate = function( value ) {
				return parseInt( value, 10 );
			};

			// Add/remove menus from the available options when they are added and removed.
			api.bind( 'add', function( setting ) {
				var option, menuId, matches = setting.id.match( navMenuIdRegex );
				if ( ! matches || false === setting() ) {
					return;
				}
				menuId = matches[1];
				option = new Option( displayNavMenuName( setting().name ), menuId );
				control.container.find( 'select' ).append( option );
			});
			api.bind( 'remove', function( setting ) {
				var menuId, matches = setting.id.match( navMenuIdRegex );
				if ( ! matches ) {
					return;
				}
				menuId = parseInt( matches[1], 10 );
				if ( control.setting() === menuId ) {
					control.setting.set( '' );
				}
				control.container.find( 'option[value=' + menuId + ']' ).remove();
			});
			api.bind( 'change', function( setting ) {
				var menuId, matches = setting.id.match( navMenuIdRegex );
				if ( ! matches ) {
					return;
				}
				menuId = parseInt( matches[1], 10 );
				if ( false === setting() ) {
					if ( control.setting() === menuId ) {
						control.setting.set( '' );
					}
					control.container.find( 'option[value=' + menuId + ']' ).remove();
				} else {
					control.container.find( 'option[value=' + menuId + ']' ).text( displayNavMenuName( setting().name ) );
				}
			});
		}
	});

	/**
	 * wp.customize.Menus.MenuItemControl
	 *
	 * Customizer control for menu items.
	 * Note that 'menu_item' must match the WP_Customize_Menu_Item_Control::$type.
	 *
	 * @constructor
	 * @augments wp.customize.Control
	 */
	api.Menus.MenuItemControl = api.Control.extend({

		/**
		 * @inheritdoc
		 */
		initialize: function( id, options ) {
			var control = this;
			api.Control.prototype.initialize.call( control, id, options );
			control.active.validate = function() {
				return api.section( control.section() ).active();
			};
		},

		/**
		 * @since Menu Customizer 0.3
		 *
		 * Override the embed() method to do nothing,
		 * so that the control isn't embedded on load,
		 * unless the containing section is already expanded.
		 */
		embed: function() {
			var control = this,
				sectionId = control.section(),
				section;
			if ( ! sectionId ) {
				return;
			}
			section = api.section( sectionId );
			if ( section && section.expanded() ) {
				control.actuallyEmbed();
			}
		},

		/**
		 * This function is called in Section.onChangeExpanded() so the control
		 * will only get embedded when the Section is first expanded.
		 *
		 * @since Menu Customizer 0.3
		 */
		actuallyEmbed: function() {
			var control = this;
			if ( 'resolved' === control.deferred.embedded.state() ) {
				return;
			}
			control.renderContent();
			control.deferred.embedded.resolve(); // This triggers control.ready().
		},

		/**
		 * Set up the control.
		 */
		ready: function() {
			if ( 'undefined' === typeof this.params.menu_item_id ) {
				throw new Error( 'params.menu_item_id was not defined' );
			}

			this._setupControlToggle();
			this._setupReorderUI();
			this._setupUpdateUI();
			this._setupRemoveUI();
			this._setupLinksUI();
			this._setupTitleUI();
		},

		/**
		 * Show/hide the settings when clicking on the menu item handle.
		 */
		_setupControlToggle: function() {
			var control = this;

			this.container.find( '.menu-item-handle' ).on( 'click', function( e ) {
				e.preventDefault();
				e.stopPropagation();
				var menuControl = control.getMenuControl();
				if ( menuControl.isReordering || menuControl.isSorting ) {
					return;
				}
				control.toggleForm();
			} );
		},

		/**
		 * Set up the menu-item-reorder-nav
		 */
		_setupReorderUI: function() {
			var control = this, template, $reorderNav;

			template = wp.template( 'menu-item-reorder-nav' );

			// Add the menu item reordering elements to the menu item control.
			control.container.find( '.item-controls' ).after( template );

			// Handle clicks for up/down/left-right on the reorder nav.
			$reorderNav = control.container.find( '.menu-item-reorder-nav' );
			$reorderNav.find( '.menus-move-up, .menus-move-down, .menus-move-left, .menus-move-right' ).on( 'click', function() {
				var moveBtn = $( this );
				moveBtn.focus();

				var isMoveUp = moveBtn.is( '.menus-move-up' ),
					isMoveDown = moveBtn.is( '.menus-move-down' ),
					isMoveLeft = moveBtn.is( '.menus-move-left' ),
					isMoveRight = moveBtn.is( '.menus-move-right' );

				if ( isMoveUp ) {
					control.moveUp();
				} else if ( isMoveDown ) {
					control.moveDown();
				} else if ( isMoveLeft ) {
					control.moveLeft();
				} else if ( isMoveRight ) {
					control.moveRight();
				}

				moveBtn.focus(); // Re-focus after the container was moved.
			} );
		},

		/**
		 * Set up event handlers for menu item updating.
		 */
		_setupUpdateUI: function() {
			var control = this,
				settingValue = control.setting();

			control.elements = {};
			control.elements.url = new api.Element( control.container.find( '.edit-menu-item-url' ) );
			control.elements.title = new api.Element( control.container.find( '.edit-menu-item-title' ) );
			control.elements.attr_title = new api.Element( control.container.find( '.edit-menu-item-attr-title' ) );
			control.elements.target = new api.Element( control.container.find( '.edit-menu-item-target' ) );
			control.elements.classes = new api.Element( control.container.find( '.edit-menu-item-classes' ) );
			control.elements.xfn = new api.Element( control.container.find( '.edit-menu-item-xfn' ) );
			control.elements.description = new api.Element( control.container.find( '.edit-menu-item-description' ) );
			// @todo allow other elements, added by plugins, to be automatically picked up here; allow additional values to be added to setting array.

			_.each( control.elements, function( element, property ) {
				element.bind(function( value ) {
					if ( element.element.is( 'input[type=checkbox]' ) ) {
						value = ( value ) ? element.element.val() : '';
					}

					var settingValue = control.setting();
					if ( settingValue && settingValue[ property ] !== value ) {
						settingValue = _.clone( settingValue );
						settingValue[ property ] = value;
						control.setting.set( settingValue );
					}
				});
				if ( settingValue ) {
					element.set( settingValue[ property ] );
				}
			});

			control.setting.bind(function( to, from ) {
				var itemId = control.params.menu_item_id,
					followingSiblingItemControls = [],
					childrenItemControls = [],
					menuControl;

				if ( false === to ) {
					menuControl = api.control( 'nav_menu[' + String( from.nav_menu_term_id ) + ']' );
					control.container.remove();

					_.each( menuControl.getMenuItemControls(), function( otherControl ) {
						if ( from.menu_item_parent === otherControl.setting().menu_item_parent && otherControl.setting().position > from.position ) {
							followingSiblingItemControls.push( otherControl );
						} else if ( otherControl.setting().menu_item_parent === itemId ) {
							childrenItemControls.push( otherControl );
						}
					});

					// Shift all following siblings by the number of children this item has.
					_.each( followingSiblingItemControls, function( followingSiblingItemControl ) {
						var value = _.clone( followingSiblingItemControl.setting() );
						value.position += childrenItemControls.length;
						followingSiblingItemControl.setting.set( value );
					});

					// Now move the children up to be the new subsequent siblings.
					_.each( childrenItemControls, function( childrenItemControl, i ) {
						var value = _.clone( childrenItemControl.setting() );
						value.position = from.position + i;
						value.menu_item_parent = from.menu_item_parent;
						childrenItemControl.setting.set( value );
					});

					menuControl.debouncedReflowMenuItems();
				} else {
					// Update the elements' values to match the new setting properties.
					_.each( to, function( value, key ) {
						if ( control.elements[ key] ) {
							control.elements[ key ].set( to[ key ] );
						}
					} );
					control.container.find( '.menu-item-data-parent-id' ).val( to.menu_item_parent );

					// Handle UI updates when the position or depth (parent) change.
					if ( to.position !== from.position || to.menu_item_parent !== from.menu_item_parent ) {
						control.getMenuControl().debouncedReflowMenuItems();
					}
				}
			});
		},

		/**
		 * Set up event handlers for menu item deletion.
		 */
		_setupRemoveUI: function() {
			var control = this, $removeBtn;

			// Configure delete button.
			$removeBtn = control.container.find( '.item-delete' );

			$removeBtn.on( 'click', function() {
				// Find an adjacent element to add focus to when this menu item goes away
				var $adjacentFocusTarget;
				if ( control.container.next().is( '.customize-control-nav_menu_item' ) ) {
					if ( ! $( 'body' ).hasClass( 'adding-menu-items' ) ) {
						$adjacentFocusTarget = control.container.next().find( '.item-edit:first' );
					} else {
						$adjacentFocusTarget = control.container.next().find( '.item-delete:first' );
					}
				} else if ( control.container.prev().is( '.customize-control-nav_menu_item' ) ) {
					if ( ! $( 'body' ).hasClass( 'adding-menu-items' ) ) {
						$adjacentFocusTarget = control.container.prev().find( '.item-edit:first' );
					} else {
						$adjacentFocusTarget = control.container.prev().find( '.item-delete:first' );
					}
				} else {
					$adjacentFocusTarget = control.container.next( '.customize-control-nav_menu' ).find( '.add-new-menu-item' );
				}

				control.container.slideUp( function() {
					control.setting.set( false );
					wp.a11y.speak( api.Menus.data.l10n.itemDeleted );
					$adjacentFocusTarget.focus(); // keyboard accessibility
				} );
			} );
		},

		_setupLinksUI: function() {
			var $origBtn;

			// Configure original link.
			$origBtn = this.container.find( 'a.original-link' );

			$origBtn.on( 'click', function( e ) {
				e.preventDefault();
				api.previewer.previewUrl( e.target.toString() );
			} );
		},

		/**
		 * Update item handle title when changed.
		 */
		_setupTitleUI: function() {
			var control = this;

			control.setting.bind( function( item ) {
				if ( ! item ) {
					return;
				}

				var titleEl = control.container.find( '.menu-item-title' );

				// Don't update to an empty title.
				if ( item.title ) {
					titleEl
						.text( item.title )
						.removeClass( 'no-title' );
				} else {
					titleEl
						.text( api.Menus.data.l10n.untitled )
						.addClass( 'no-title' );
				}
			} );
		},

		/**
		 *
		 * @returns {number}
		 */
		getDepth: function() {
			var control = this, setting = control.setting(), depth = 0;
			if ( ! setting ) {
				return 0;
			}
			while ( setting && setting.menu_item_parent ) {
				depth += 1;
				control = api.control( 'nav_menu_item[' + setting.menu_item_parent + ']' );
				if ( ! control ) {
					break;
				}
				setting = control.setting();
			}
			return depth;
		},

		/**
		 * Amend the control's params with the data necessary for the JS template just in time.
		 */
		renderContent: function() {
			var control = this,
				settingValue = control.setting(),
				containerClasses;

			control.params.title = settingValue.title || '';
			control.params.depth = control.getDepth();
			control.container.data( 'item-depth', control.params.depth );
			containerClasses = [
				'menu-item',
				'menu-item-depth-' + String( control.params.depth ),
				'menu-item-' + settingValue.object,
				'menu-item-edit-inactive'
			];

			if ( settingValue.invalid ) {
				containerClasses.push( 'invalid' );
				control.params.title = api.Menus.data.invalidTitleTpl.replace( '%s', control.params.title );
			} else if ( 'draft' === settingValue.status ) {
				containerClasses.push( 'pending' );
				control.params.title = api.Menus.data.pendingTitleTpl.replace( '%s', control.params.title );
			}

			control.params.el_classes = containerClasses.join( ' ' );
			control.params.item_type_label = api.Menus.getTypeLabel( settingValue.type, settingValue.object );
			control.params.item_type = settingValue.type;
			control.params.url = settingValue.url;
			control.params.target = settingValue.target;
			control.params.attr_title = settingValue.attr_title;
			control.params.classes = _.isArray( settingValue.classes ) ? settingValue.classes.join( ' ' ) : settingValue.classes;
			control.params.attr_title = settingValue.attr_title;
			control.params.xfn = settingValue.xfn;
			control.params.description = settingValue.description;
			control.params.parent = settingValue.menu_item_parent;
			control.params.original_title = settingValue.original_title || '';

			control.container.addClass( control.params.el_classes );

			api.Control.prototype.renderContent.call( control );
		},

		/***********************************************************************
		 * Begin public API methods
		 **********************************************************************/

		/**
		 * @return {wp.customize.controlConstructor.nav_menu|null}
		 */
		getMenuControl: function() {
			var control = this, settingValue = control.setting();
			if ( settingValue && settingValue.nav_menu_term_id ) {
				return api.control( 'nav_menu[' + settingValue.nav_menu_term_id + ']' );
			} else {
				return null;
			}
		},

		/**
		 * Expand the accordion section containing a control
		 */
		expandControlSection: function() {
			var $section = this.container.closest( '.accordion-section' );

			if ( ! $section.hasClass( 'open' ) ) {
				$section.find( '.accordion-section-title:first' ).trigger( 'click' );
			}
		},

		/**
		 * Expand the menu item form control.
		 */
		expandForm: function() {
			this.toggleForm( true );
		},

		/**
		 * Collapse the menu item form control.
		 */
		collapseForm: function() {
			this.toggleForm( false );
		},

		/**
		 * Expand or collapse the menu item control.
		 *
		 * @param {boolean|undefined} [showOrHide] If not supplied, will be inverse of current visibility
		 */
		toggleForm: function( showOrHide ) {
			var self = this, $menuitem, $inside, complete;

			$menuitem = this.container;
			$inside = $menuitem.find( '.menu-item-settings:first' );
			if ( 'undefined' === typeof showOrHide ) {
				showOrHide = ! $inside.is( ':visible' );
			}

			// Already expanded or collapsed.
			if ( $inside.is( ':visible' ) === showOrHide ) {
				return;
			}

			if ( showOrHide ) {
				// Close all other menu item controls before expanding this one.
				api.control.each( function( otherControl ) {
					if ( self.params.type === otherControl.params.type && self !== otherControl ) {
						otherControl.collapseForm();
					}
				} );

				complete = function() {
					$menuitem
						.removeClass( 'menu-item-edit-inactive' )
						.addClass( 'menu-item-edit-active' );
					self.container.trigger( 'expanded' );
				};

				$inside.slideDown( 'fast', complete );

				self.container.trigger( 'expand' );
			} else {
				complete = function() {
					$menuitem
						.addClass( 'menu-item-edit-inactive' )
						.removeClass( 'menu-item-edit-active' );
					self.container.trigger( 'collapsed' );
				};

				self.container.trigger( 'collapse' );

				$inside.slideUp( 'fast', complete );
			}
		},

		/**
		 * Expand the containing menu section, expand the form, and focus on
		 * the first input in the control.
		 */
		focus: function() {
			this.expandControlSection();
			this.expandForm();
			this.container.find( '.menu-item-settings :focusable:first' ).focus();
		},

		/**
		 * Move menu item up one in the menu.
		 */
		moveUp: function() {
			this._changePosition( -1 );
			wp.a11y.speak( api.Menus.data.l10n.movedUp );
		},

		/**
		 * Move menu item up one in the menu.
		 */
		moveDown: function() {
			this._changePosition( 1 );
			wp.a11y.speak( api.Menus.data.l10n.movedDown );
		},
		/**
		 * Move menu item and all children up one level of depth.
		 */
		moveLeft: function() {
			this._changeDepth( -1 );
			wp.a11y.speak( api.Menus.data.l10n.movedLeft );
		},

		/**
		 * Move menu item and children one level deeper, as a submenu of the previous item.
		 */
		moveRight: function() {
			this._changeDepth( 1 );
			wp.a11y.speak( api.Menus.data.l10n.movedRight );
		},

		/**
		 * Note that this will trigger a UI update, causing child items to
		 * move as well and cardinal order class names to be updated.
		 *
		 * @private
		 *
		 * @param {Number} offset 1|-1
		 */
		_changePosition: function( offset ) {
			var control = this,
				adjacentSetting,
				settingValue = _.clone( control.setting() ),
				siblingSettings = [],
				realPosition;

			if ( 1 !== offset && -1 !== offset ) {
				throw new Error( 'Offset changes by 1 are only supported.' );
			}

			// Skip moving deleted items.
			if ( ! control.setting() ) {
				return;
			}

			// Locate the other items under the same parent (siblings).
			_( control.getMenuControl().getMenuItemControls() ).each(function( otherControl ) {
				if ( otherControl.setting().menu_item_parent === settingValue.menu_item_parent ) {
					siblingSettings.push( otherControl.setting );
				}
			});
			siblingSettings.sort(function( a, b ) {
				return a().position - b().position;
			});

			realPosition = _.indexOf( siblingSettings, control.setting );
			if ( -1 === realPosition ) {
				throw new Error( 'Expected setting to be among siblings.' );
			}

			// Skip doing anything if the item is already at the edge in the desired direction.
			if ( ( realPosition === 0 && offset < 0 ) || ( realPosition === siblingSettings.length - 1 && offset > 0 ) ) {
				// @todo Should we allow a menu item to be moved up to break it out of a parent? Adopt with previous or following parent?
				return;
			}

			// Update any adjacent menu item setting to take on this item's position.
			adjacentSetting = siblingSettings[ realPosition + offset ];
			if ( adjacentSetting ) {
				adjacentSetting.set( $.extend(
					_.clone( adjacentSetting() ),
					{
						position: settingValue.position
					}
				) );
			}

			settingValue.position += offset;
			control.setting.set( settingValue );
		},

		/**
		 * Note that this will trigger a UI update, causing child items to
		 * move as well and cardinal order class names to be updated.
		 *
		 * @private
		 *
		 * @param {Number} offset 1|-1
		 */
		_changeDepth: function( offset ) {
			if ( 1 !== offset && -1 !== offset ) {
				throw new Error( 'Offset changes by 1 are only supported.' );
			}
			var control = this,
				settingValue = _.clone( control.setting() ),
				siblingControls = [],
				realPosition,
				siblingControl,
				parentControl;

			// Locate the other items under the same parent (siblings).
			_( control.getMenuControl().getMenuItemControls() ).each(function( otherControl ) {
				if ( otherControl.setting().menu_item_parent === settingValue.menu_item_parent ) {
					siblingControls.push( otherControl );
				}
			});
			siblingControls.sort(function( a, b ) {
				return a.setting().position - b.setting().position;
			});

			realPosition = _.indexOf( siblingControls, control );
			if ( -1 === realPosition ) {
				throw new Error( 'Expected control to be among siblings.' );
			}

			if ( -1 === offset ) {
				// Skip moving left an item that is already at the top level.
				if ( ! settingValue.menu_item_parent ) {
					return;
				}

				parentControl = api.control( 'nav_menu_item[' + settingValue.menu_item_parent + ']' );

				// Make this control the parent of all the following siblings.
				_( siblingControls ).chain().slice( realPosition ).each(function( siblingControl, i ) {
					siblingControl.setting.set(
						$.extend(
							{},
							siblingControl.setting(),
							{
								menu_item_parent: control.params.menu_item_id,
								position: i
							}
						)
					);
				});

				// Increase the positions of the parent item's subsequent children to make room for this one.
				_( control.getMenuControl().getMenuItemControls() ).each(function( otherControl ) {
					var otherControlSettingValue, isControlToBeShifted;
					isControlToBeShifted = (
						otherControl.setting().menu_item_parent === parentControl.setting().menu_item_parent &&
						otherControl.setting().position > parentControl.setting().position
					);
					if ( isControlToBeShifted ) {
						otherControlSettingValue = _.clone( otherControl.setting() );
						otherControl.setting.set(
							$.extend(
								otherControlSettingValue,
								{ position: otherControlSettingValue.position + 1 }
							)
						);
					}
				});

				// Make this control the following sibling of its parent item.
				settingValue.position = parentControl.setting().position + 1;
				settingValue.menu_item_parent = parentControl.setting().menu_item_parent;
				control.setting.set( settingValue );

			} else if ( 1 === offset ) {
				// Skip moving right an item that doesn't have a previous sibling.
				if ( realPosition === 0 ) {
					return;
				}

				// Make the control the last child of the previous sibling.
				siblingControl = siblingControls[ realPosition - 1 ];
				settingValue.menu_item_parent = siblingControl.params.menu_item_id;
				settingValue.position = 0;
				_( control.getMenuControl().getMenuItemControls() ).each(function( otherControl ) {
					if ( otherControl.setting().menu_item_parent === settingValue.menu_item_parent ) {
						settingValue.position = Math.max( settingValue.position, otherControl.setting().position );
					}
				});
				settingValue.position += 1;
				control.setting.set( settingValue );
			}
		}
	} );

	/**
	 * wp.customize.Menus.MenuNameControl
	 *
	 * Customizer control for a nav menu's name.
	 *
	 * @constructor
	 * @augments wp.customize.Control
	 */
	api.Menus.MenuNameControl = api.Control.extend({

		ready: function() {
			var control = this,
				settingValue = control.setting();

			/*
			 * Since the control is not registered in PHP, we need to prevent the
			 * preview's sending of the activeControls to result in this control
			 * being deactivated.
			 */
			control.active.validate = function() {
				return api.section( control.section() ).active();
			};

			control.nameElement = new api.Element( control.container.find( '.menu-name-field' ) );

			control.nameElement.bind(function( value ) {
				var settingValue = control.setting();
				if ( settingValue && settingValue.name !== value ) {
					settingValue = _.clone( settingValue );
					settingValue.name = value;
					control.setting.set( settingValue );
				}
			});
			if ( settingValue ) {
				control.nameElement.set( settingValue.name );
			}

			control.setting.bind(function( object ) {
				if ( object ) {
					control.nameElement.set( object.name );
				}
			});
		}

	});

	/**
	 * wp.customize.Menus.MenuControl
	 *
	 * Customizer control for menus.
	 * Note that 'nav_menu' must match the WP_Menu_Customize_Control::$type
	 *
	 * @constructor
	 * @augments wp.customize.Control
	 */
	api.Menus.MenuControl = api.Control.extend({
		/**
		 * Set up the control.
		 */
		ready: function() {
			var control = this,
				menuId = control.params.menu_id,
				menu = control.setting(),
				name;

			if ( 'undefined' === typeof this.params.menu_id ) {
				throw new Error( 'params.menu_id was not defined' );
			}

			/*
			 * Since the control is not registered in PHP, we need to prevent the
			 * preview's sending of the activeControls to result in this control
			 * being deactivated.
			 */
			control.active.validate = function() {
				return api.section( control.section() ).active();
			};

			control.$controlSection = control.container.closest( '.control-section' );
			control.$sectionContent = control.container.closest( '.accordion-section-content' );

			this._setupModel();

			api.section( control.section(), function( section ) {
				section.deferred.initSortables.done(function( menuList ) {
					control._setupSortable( menuList );
				});
			} );

			this._setupAddition();
			this._setupLocations();
			this._setupTitle();

			// Add menu to Custom Menu widgets.
			if ( menu ) {
				name = displayNavMenuName( menu.name );

				api.control.each( function( widgetControl ) {
					if ( ! widgetControl.extended( api.controlConstructor.widget_form ) || 'nav_menu' !== widgetControl.params.widget_id_base ) {
						return;
					}
					var select = widgetControl.container.find( 'select' );
					if ( select.find( 'option[value=' + String( menuId ) + ']' ).length === 0 ) {
						select.append( new Option( name, menuId ) );
					}
				} );
				$( '#available-widgets-list .widget-inside:has(input.id_base[value=nav_menu]) select:first' ).append( new Option( name, menuId ) );
			}
		},

		/**
		 * Update ordering of menu item controls when the setting is updated.
		 */
		_setupModel: function() {
			var control = this,
				menuId = control.params.menu_id;

			control.elements = {};
			control.elements.auto_add = new api.Element( control.container.find( 'input[type=checkbox].auto_add' ) );

			control.elements.auto_add.bind(function( auto_add ) {
				var settingValue = control.setting();
				if ( settingValue && settingValue.auto_add !== auto_add ) {
					settingValue = _.clone( settingValue );
					settingValue.auto_add = auto_add;
					control.setting.set( settingValue );
				}
			});
			control.elements.auto_add.set( control.setting().auto_add );
			control.setting.bind(function( object ) {
				if ( ! object ) {
					return;
				}
				control.elements.auto_add.set( object.auto_add );
			});

			control.setting.bind( function( to ) {
				var name;
				if ( false === to ) {
					control._handleDeletion();
				} else {
					// Update names in the Custom Menu widgets.
					name = displayNavMenuName( to.name );
					api.control.each( function( widgetControl ) {
						if ( ! widgetControl.extended( api.controlConstructor.widget_form ) || 'nav_menu' !== widgetControl.params.widget_id_base ) {
							return;
						}
						var select = widgetControl.container.find( 'select' );
						select.find( 'option[value=' + String( menuId ) + ']' ).text( name );
					});
					$( '#available-widgets-list .widget-inside:has(input.id_base[value=nav_menu]) select:first option[value=' + String( menuId ) + ']' ).text( name );
				}
			} );

			control.container.find( '.menu-delete' ).on( 'click', function( event ) {
				event.stopPropagation();
				event.preventDefault();
				control.setting.set( false );
			});
		},

		/**
		 * Allow items in each menu to be re-ordered, and for the order to be previewed.
		 *
		 * Notice that the UI aspects here are handled by wpNavMenu.initSortables()
		 * which is called in MenuSection.onChangeExpanded()
		 *
		 * @param {object} menuList - The element that has sortable().
		 */
		_setupSortable: function( menuList ) {
			var control = this;

			if ( ! menuList.is( control.$sectionContent ) ) {
				throw new Error( 'Unexpected menuList.' );
			}

			menuList.on( 'sortstart', function() {
				control.isSorting = true;
			});

			menuList.on( 'sortstop', function() {
				setTimeout( function() { // Next tick.
					var menuItemContainerIds = control.$sectionContent.sortable( 'toArray' ),
						menuItemControls = [],
						position = 0,
						priority = 10;

					control.isSorting = false;

					_.each( menuItemContainerIds, function( menuItemContainerId ) {
						var menuItemId, menuItemControl, matches;
						matches = menuItemContainerId.match( /^customize-control-nav_menu_item-(-?\d+)$/, '' );
						if ( ! matches ) {
							return;
						}
						menuItemId = parseInt( matches[1], 10 );
						menuItemControl = api.control( 'nav_menu_item[' + String( menuItemId ) + ']' );
						if ( menuItemControl ) {
							menuItemControls.push( menuItemControl );
						}
					} );

					_.each( menuItemControls, function( menuItemControl ) {
						if ( false === menuItemControl.setting() ) {
							// Skip deleted items.
							return;
						}
						var setting = _.clone( menuItemControl.setting() );
						position += 1;
						priority += 1;
						setting.position = position;
						menuItemControl.priority( priority );

						// Note that wpNavMenu will be setting this .menu-item-data-parent-id input's value.
						setting.menu_item_parent = parseInt( menuItemControl.container.find( '.menu-item-data-parent-id' ).val(), 10 );
						if ( ! setting.menu_item_parent ) {
							setting.menu_item_parent = 0;
						}

						menuItemControl.setting.set( setting );
					});
				});
			});

			control.isReordering = false;

			/**
			 * Keyboard-accessible reordering.
			 */
			this.container.find( '.reorder-toggle' ).on( 'click', function() {
				control.toggleReordering( ! control.isReordering );
			} );
		},

		/**
		 * Set up UI for adding a new menu item.
		 */
		_setupAddition: function() {
			var self = this;

			this.container.find( '.add-new-menu-item' ).on( 'click', function( event ) {
				if ( self.$sectionContent.hasClass( 'reordering' ) ) {
					return;
				}

				if ( ! $( 'body' ).hasClass( 'adding-menu-items' ) ) {
					$( this ).attr( 'aria-expanded', 'true' );
					api.Menus.availableMenuItemsPanel.open( self );
				} else {
					$( this ).attr( 'aria-expanded', 'false' );
					api.Menus.availableMenuItemsPanel.close();
					event.stopPropagation();
				}
			} );
		},

		_handleDeletion: function() {
			var control = this,
				section,
				menuId = control.params.menu_id,
				removeSection;
			section = api.section( control.section() );
			removeSection = function() {
				section.container.remove();
				api.section.remove( section.id );
			};

			if ( section && section.expanded() ) {
				section.collapse({
					completeCallback: function() {
						removeSection();
						wp.a11y.speak( api.Menus.data.l10n.menuDeleted );
						api.panel( 'nav_menus' ).focus();
					}
				});
			} else {
				removeSection();
			}

			// Remove the menu from any Custom Menu widgets.
			api.control.each(function( widgetControl ) {
				if ( ! widgetControl.extended( api.controlConstructor.widget_form ) || 'nav_menu' !== widgetControl.params.widget_id_base ) {
					return;
				}
				var select = widgetControl.container.find( 'select' );
				if ( select.val() === String( menuId ) ) {
					select.prop( 'selectedIndex', 0 ).trigger( 'change' );
				}
				select.find( 'option[value=' + String( menuId ) + ']' ).remove();
			});
			$( '#available-widgets-list .widget-inside:has(input.id_base[value=nav_menu]) select:first option[value=' + String( menuId ) + ']' ).remove();
		},

		// Setup theme location checkboxes.
		_setupLocations: function() {
			var control = this;

			control.container.find( '.assigned-menu-location' ).each(function() {
				var container = $( this ),
					checkbox = container.find( 'input[type=checkbox]' ),
					element,
					updateSelectedMenuLabel,
					navMenuLocationSetting = api( 'nav_menu_locations[' + checkbox.data( 'location-id' ) + ']' );

				updateSelectedMenuLabel = function( selectedMenuId ) {
					var menuSetting = api( 'nav_menu[' + String( selectedMenuId ) + ']' );
					if ( ! selectedMenuId || ! menuSetting || ! menuSetting() ) {
						container.find( '.theme-location-set' ).hide();
					} else {
						container.find( '.theme-location-set' ).show().find( 'span' ).text( displayNavMenuName( menuSetting().name ) );
					}
				};

				element = new api.Element( checkbox );
				element.set( navMenuLocationSetting.get() === control.params.menu_id );

				checkbox.on( 'change', function() {
					// Note: We can't use element.bind( function( checked ){ ... } ) here because it will trigger a change as well.
					navMenuLocationSetting.set( this.checked ? control.params.menu_id : 0 );
				} );

				navMenuLocationSetting.bind(function( selectedMenuId ) {
					element.set( selectedMenuId === control.params.menu_id );
					updateSelectedMenuLabel( selectedMenuId );
				});
				updateSelectedMenuLabel( navMenuLocationSetting.get() );

			});
		},

		/**
		 * Update Section Title as menu name is changed.
		 */
		_setupTitle: function() {
			var control = this;

			control.setting.bind( function( menu ) {
				if ( ! menu ) {
					return;
				}

				var section = control.container.closest( '.accordion-section' ),
					menuId = control.params.menu_id,
					controlTitle = section.find( '.accordion-section-title' ),
					sectionTitle = section.find( '.customize-section-title h3' ),
					location = section.find( '.menu-in-location' ),
					action = sectionTitle.find( '.customize-action' ),
					name = displayNavMenuName( menu.name );

				// Update the control title
				controlTitle.text( name );
				if ( location.length ) {
					location.appendTo( controlTitle );
				}

				// Update the section title
				sectionTitle.text( name );
				if ( action.length ) {
					action.prependTo( sectionTitle );
				}

				// Update the nav menu name in location selects.
				api.control.each( function( control ) {
					if ( /^nav_menu_locations\[/.test( control.id ) ) {
						control.container.find( 'option[value=' + menuId + ']' ).text( name );
					}
				} );

				// Update the nav menu name in all location checkboxes.
				section.find( '.customize-control-checkbox input' ).each( function() {
					if ( $( this ).prop( 'checked' ) ) {
						$( '.current-menu-location-name-' + $( this ).data( 'location-id' ) ).text( name );
					}
				} );
			} );
		},

		/***********************************************************************
		 * Begin public API methods
		 **********************************************************************/

		/**
		 * Enable/disable the reordering UI
		 *
		 * @param {Boolean} showOrHide to enable/disable reordering
		 */
		toggleReordering: function( showOrHide ) {
			var addNewItemBtn = this.container.find( '.add-new-menu-item' ),
				reorderBtn = this.container.find( '.reorder-toggle' );

			showOrHide = Boolean( showOrHide );

			if ( showOrHide === this.$sectionContent.hasClass( 'reordering' ) ) {
				return;
			}

			this.isReordering = showOrHide;
			this.$sectionContent.toggleClass( 'reordering', showOrHide );
			this.$sectionContent.sortable( this.isReordering ? 'disable' : 'enable' );
			if ( this.isReordering ) {
				addNewItemBtn.attr( 'tabindex', '-1' );
				reorderBtn.find( '.reorder-done' ).focus();
				wp.a11y.speak( api.Menus.data.l10n.reorderModeOn );
			} else {
				addNewItemBtn.removeAttr( 'tabindex' );
				reorderBtn.find( '.reorder' ).focus();
				wp.a11y.speak( api.Menus.data.l10n.reorderModeOff );
			}

			if ( showOrHide ) {
				_( this.getMenuItemControls() ).each( function( formControl ) {
					formControl.collapseForm();
				} );
			}
		},

		/**
		 * @return {wp.customize.controlConstructor.nav_menu_item[]}
		 */
		getMenuItemControls: function() {
			var menuControl = this,
				menuItemControls = [],
				menuTermId = menuControl.params.menu_id;

			api.control.each(function( control ) {
				if ( 'nav_menu_item' === control.params.type && control.setting() && menuTermId === control.setting().nav_menu_term_id ) {
					menuItemControls.push( control );
				}
			});

			return menuItemControls;
		},

		/**
		 * Make sure that each menu item control has the proper depth.
		 */
		reflowMenuItems: function() {
			var menuControl = this,
				menuSection = api.section( 'nav_menu[' + String( menuControl.params.menu_id ) + ']' ),
				menuItemControls = menuControl.getMenuItemControls(),
				reflowRecursively;

			reflowRecursively = function( context ) {
				var currentMenuItemControls = [],
					thisParent = context.currentParent;
				_.each( context.menuItemControls, function( menuItemControl ) {
					if ( thisParent === menuItemControl.setting().menu_item_parent ) {
						currentMenuItemControls.push( menuItemControl );
						// @todo We could remove this item from menuItemControls now, for efficiency.
					}
				});
				currentMenuItemControls.sort( function( a, b ) {
					return a.setting().position - b.setting().position;
				});

				_.each( currentMenuItemControls, function( menuItemControl ) {
					// Update position.
					context.currentAbsolutePosition += 1;
					menuItemControl.priority.set( context.currentAbsolutePosition ); // This will change the sort order.

					// Update depth.
					if ( ! menuItemControl.container.hasClass( 'menu-item-depth-' + String( context.currentDepth ) ) ) {
						_.each( menuItemControl.container.prop( 'className' ).match( /menu-item-depth-\d+/g ), function( className ) {
							menuItemControl.container.removeClass( className );
						});
						menuItemControl.container.addClass( 'menu-item-depth-' + String( context.currentDepth ) );
					}
					menuItemControl.container.data( 'item-depth', context.currentDepth );

					// Process any children items.
					context.currentDepth += 1;
					context.currentParent = menuItemControl.params.menu_item_id;
					reflowRecursively( context );
					context.currentDepth -= 1;
					context.currentParent = thisParent;
				});

				// Update class names for reordering controls.
				if ( currentMenuItemControls.length ) {
					_( currentMenuItemControls ).each(function( menuItemControl ) {
						menuItemControl.container.removeClass( 'move-up-disabled move-down-disabled move-left-disabled move-right-disabled' );
					});

					currentMenuItemControls[0].container
						.addClass( 'move-up-disabled' )
						.addClass( 'move-right-disabled' )
						.toggleClass( 'move-down-disabled', 1 === currentMenuItemControls.length );
					currentMenuItemControls[ currentMenuItemControls.length - 1 ].container
						.addClass( 'move-down-disabled' )
						.toggleClass( 'move-up-disabled', 1 === currentMenuItemControls.length );
				}
			};

			reflowRecursively( {
				menuItemControls: menuItemControls,
				currentParent: 0,
				currentDepth: 0,
				currentAbsolutePosition: 0
			} );

			menuSection.container.find( '.menu-item .menu-item-reorder-nav button' ).prop( 'tabIndex', 0 );
			menuSection.container.find( '.menu-item.move-up-disabled .menus-move-up' ).prop( 'tabIndex', -1 );
			menuSection.container.find( '.menu-item.move-down-disabled .menus-move-down' ).prop( 'tabIndex', -1 );
			menuSection.container.find( '.menu-item.move-left-disabled .menus-move-left' ).prop( 'tabIndex', -1 );
			menuSection.container.find( '.menu-item.move-right-disabled .menus-move-right' ).prop( 'tabIndex', -1 );

			menuControl.container.find( '.reorder-toggle' ).toggle( menuItemControls.length > 1 );
		},

		/**
		 * Note that this function gets debounced so that when a lot of setting
		 * changes are made at once, for instance when moving a menu item that
		 * has child items, this function will only be called once all of the
		 * settings have been updated.
		 */
		debouncedReflowMenuItems: _.debounce( function() {
			this.reflowMenuItems.apply( this, arguments );
		}, 0 ),

		/**
		 * Add a new item to this menu.
		 *
		 * @param {object} item - Value for the nav_menu_item setting to be created.
		 * @returns {wp.customize.Menus.controlConstructor.nav_menu_item} The newly-created nav_menu_item control instance.
		 */
		addItemToMenu: function( item ) {
			var menuControl = this, customizeId, settingArgs, setting, menuItemControl, placeholderId, position = 0, priority = 10;

			_.each( menuControl.getMenuItemControls(), function( control ) {
				if ( false === control.setting() ) {
					return;
				}
				priority = Math.max( priority, control.priority() );
				if ( 0 === control.setting().menu_item_parent ) {
					position = Math.max( position, control.setting().position );
				}
			});
			position += 1;
			priority += 1;

			item = $.extend(
				{},
				api.Menus.data.defaultSettingValues.nav_menu_item,
				item,
				{
					nav_menu_term_id: menuControl.params.menu_id,
					original_title: item.title,
					position: position
				}
			);
			delete item.id; // only used by Backbone

			placeholderId = api.Menus.generatePlaceholderAutoIncrementId();
			customizeId = 'nav_menu_item[' + String( placeholderId ) + ']';
			settingArgs = {
				type: 'nav_menu_item',
				transport: 'postMessage',
				previewer: api.previewer
			};
			setting = api.create( customizeId, customizeId, {}, settingArgs );
			setting.set( item ); // Change from initial empty object to actual item to mark as dirty.

			// Add the menu item control.
			menuItemControl = new api.controlConstructor.nav_menu_item( customizeId, {
				params: {
					type: 'nav_menu_item',
					content: '<li id="customize-control-nav_menu_item-' + String( placeholderId ) + '" class="customize-control customize-control-nav_menu_item"></li>',
					section: menuControl.id,
					priority: priority,
					active: true,
					settings: {
						'default': customizeId
					},
					menu_item_id: placeholderId
				},
				previewer: api.previewer
			} );

			api.control.add( customizeId, menuItemControl );
			setting.preview();
			menuControl.debouncedReflowMenuItems();

			wp.a11y.speak( api.Menus.data.l10n.itemAdded );

			return menuItemControl;
		}
	} );

	/**
	 * wp.customize.Menus.NewMenuControl
	 *
	 * Customizer control for creating new menus and handling deletion of existing menus.
	 * Note that 'new_menu' must match the WP_New_Menu_Customize_Control::$type.
	 *
	 * @constructor
	 * @augments wp.customize.Control
	 */
	api.Menus.NewMenuControl = api.Control.extend({
		/**
		 * Set up the control.
		 */
		ready: function() {
			this._bindHandlers();
		},

		_bindHandlers: function() {
			var self = this,
				name = $( '#customize-control-new_menu_name input' ),
				submit = $( '#create-new-menu-submit' );
			name.on( 'keydown', function( event ) {
				if ( 13 === event.which ) { // Enter.
					self.submit();
				}
			} );
			submit.on( 'click', function( event ) {
				self.submit();
				event.stopPropagation();
				event.preventDefault();
			} );
		},

		/**
		 * Create the new menu with the name supplied.
		 */
		submit: function() {

			var control = this,
				container = control.container.closest( '.accordion-section-new-menu' ),
				nameInput = container.find( '.menu-name-field' ).first(),
				name = nameInput.val(),
				menuSection,
				customizeId,
				placeholderId = api.Menus.generatePlaceholderAutoIncrementId();

			if ( ! name ) {
				nameInput.addClass( 'invalid' );
				nameInput.focus();
				return;
			}

			customizeId = 'nav_menu[' + String( placeholderId ) + ']';

			// Register the menu control setting.
			api.create( customizeId, customizeId, {}, {
				type: 'nav_menu',
				transport: 'postMessage',
				previewer: api.previewer
			} );
			api( customizeId ).set( $.extend(
				{},
				api.Menus.data.defaultSettingValues.nav_menu,
				{
					name: name
				}
			) );

			/*
			 * Add the menu section (and its controls).
			 * Note that this will automatically create the required controls
			 * inside via the Section's ready method.
			 */
			menuSection = new api.Menus.MenuSection( customizeId, {
				params: {
					id: customizeId,
					panel: 'nav_menus',
					title: displayNavMenuName( name ),
					customizeAction: api.Menus.data.l10n.customizingMenus,
					type: 'nav_menu',
					priority: 10,
					menu_id: placeholderId
				}
			} );
			api.section.add( customizeId, menuSection );

			// Clear name field.
			nameInput.val( '' );
			nameInput.removeClass( 'invalid' );

			wp.a11y.speak( api.Menus.data.l10n.menuAdded );

			// Focus on the new menu section.
			api.section( customizeId ).focus(); // @todo should we focus on the new menu's control and open the add-items panel? Thinking user flow...
		}
	});

	/**
	 * Extends wp.customize.controlConstructor with control constructor for
	 * menu_location, menu_item, nav_menu, and new_menu.
	 */
	$.extend( api.controlConstructor, {
		nav_menu_location: api.Menus.MenuLocationControl,
		nav_menu_item: api.Menus.MenuItemControl,
		nav_menu: api.Menus.MenuControl,
		nav_menu_name: api.Menus.MenuNameControl,
		new_menu: api.Menus.NewMenuControl
	});

	/**
	 * Extends wp.customize.panelConstructor with section constructor for menus.
	 */
	$.extend( api.panelConstructor, {
		nav_menus: api.Menus.MenusPanel
	});

	/**
	 * Extends wp.customize.sectionConstructor with section constructor for menu.
	 */
	$.extend( api.sectionConstructor, {
		nav_menu: api.Menus.MenuSection,
		new_menu: api.Menus.NewMenuSection
	});

	/**
	 * Init Customizer for menus.
	 */
	api.bind( 'ready', function() {

		// Set up the menu items panel.
		api.Menus.availableMenuItemsPanel = new api.Menus.AvailableMenuItemsPanelView({
			collection: api.Menus.availableMenuItems
		});

		api.bind( 'saved', function( data ) {
			if ( data.nav_menu_updates || data.nav_menu_item_updates ) {
				api.Menus.applySavedData( data );
			}
		} );

		api.previewer.bind( 'refresh', function() {
			api.previewer.refresh();
		});
	} );

	/**
	 * When customize_save comes back with a success, make sure any inserted
	 * nav menus and items are properly re-added with their newly-assigned IDs.
	 *
	 * @param {object} data
	 * @param {array} data.nav_menu_updates
	 * @param {array} data.nav_menu_item_updates
	 */
	api.Menus.applySavedData = function( data ) {

		var insertedMenuIdMapping = {};

		_( data.nav_menu_updates ).each(function( update ) {
			var oldCustomizeId, newCustomizeId, customizeId, oldSetting, newSetting, setting, settingValue, oldSection, newSection, wasSaved;
			if ( 'inserted' === update.status ) {
				if ( ! update.previous_term_id ) {
					throw new Error( 'Expected previous_term_id' );
				}
				if ( ! update.term_id ) {
					throw new Error( 'Expected term_id' );
				}
				oldCustomizeId = 'nav_menu[' + String( update.previous_term_id ) + ']';
				if ( ! api.has( oldCustomizeId ) ) {
					throw new Error( 'Expected setting to exist: ' + oldCustomizeId );
				}
				oldSetting = api( oldCustomizeId );
				if ( ! api.section.has( oldCustomizeId ) ) {
					throw new Error( 'Expected control to exist: ' + oldCustomizeId );
				}
				oldSection = api.section( oldCustomizeId );

				settingValue = oldSetting.get();
				if ( ! settingValue ) {
					throw new Error( 'Did not expect setting to be empty (deleted).' );
				}
				settingValue = $.extend( _.clone( settingValue ), update.saved_value );

				insertedMenuIdMapping[ update.previous_term_id ] = update.term_id;
				newCustomizeId = 'nav_menu[' + String( update.term_id ) + ']';
				newSetting = api.create( newCustomizeId, newCustomizeId, settingValue, {
					type: 'nav_menu',
					transport: 'postMessage',
					previewer: api.previewer
				} );

				if ( oldSection.expanded() ) {
					oldSection.collapse();
				}

				// Add the menu section.
				newSection = new api.Menus.MenuSection( newCustomizeId, {
					params: {
						id: newCustomizeId,
						panel: 'nav_menus',
						title: settingValue.name,
						customizeAction: api.Menus.data.l10n.customizingMenus,
						type: 'nav_menu',
						priority: oldSection.priority.get(),
						active: true,
						menu_id: update.term_id
					}
				} );

				// Remove old setting and control.
				oldSection.container.remove();
				api.section.remove( oldCustomizeId );

				// Add new control to take its place.
				api.section.add( newCustomizeId, newSection );

				// Delete the placeholder and preview the new setting.
				oldSetting.callbacks.disable(); // Prevent setting triggering Customizer dirty state when set.
				oldSetting.set( false );
				oldSetting.preview();
				newSetting.preview();

				// Update nav_menu_locations to reference the new ID.
				api.each( function( setting ) {
					var wasSaved = api.state( 'saved' ).get();
					if ( /^nav_menu_locations\[/.test( setting.id ) && setting.get() === update.previous_term_id ) {
						setting.set( update.term_id );
						setting._dirty = false; // Not dirty because this is has also just been done on server in WP_Customize_Nav_Menu_Setting::update().
						api.state( 'saved' ).set( wasSaved );
						setting.preview();
					}
				} );

				if ( oldSection.expanded.get() ) {
					// @todo This doesn't seem to be working.
					newSection.expand();
				}

				// @todo Update the Custom Menu selects, ensuring the newly-inserted IDs are used for any that have selected a placeholder menu.
			} else if ( 'updated' === update.status ) {
				customizeId = 'nav_menu[' + String( update.term_id ) + ']';
				if ( ! api.has( customizeId ) ) {
					throw new Error( 'Expected setting to exist: ' + customizeId );
				}

				// Make sure the setting gets updated with its sanitized server value (specifically the conflict-resolved name).
				setting = api( customizeId );
				if ( ! _.isEqual( update.saved_value, setting.get() ) ) {
					wasSaved = api.state( 'saved' ).get();
					setting.set( update.saved_value );
					setting._dirty = false;
					api.state( 'saved' ).set( wasSaved );
				}
			}
		} );

		_( data.nav_menu_item_updates ).each(function( update ) {
			var oldCustomizeId, newCustomizeId, oldSetting, newSetting, settingValue, oldControl, newControl;
			if ( 'inserted' === update.status ) {
				if ( ! update.previous_post_id ) {
					throw new Error( 'Expected previous_post_id' );
				}
				if ( ! update.post_id ) {
					throw new Error( 'Expected post_id' );
				}
				oldCustomizeId = 'nav_menu_item[' + String( update.previous_post_id ) + ']';
				if ( ! api.has( oldCustomizeId ) ) {
					throw new Error( 'Expected setting to exist: ' + oldCustomizeId );
				}
				oldSetting = api( oldCustomizeId );
				if ( ! api.control.has( oldCustomizeId ) ) {
					throw new Error( 'Expected control to exist: ' + oldCustomizeId );
				}
				oldControl = api.control( oldCustomizeId );

				settingValue = oldSetting.get();
				if ( ! settingValue ) {
					throw new Error( 'Did not expect setting to be empty (deleted).' );
				}
				settingValue = _.clone( settingValue );

				// If the menu was also inserted, then make sure it uses the new menu ID for nav_menu_term_id.
				if ( insertedMenuIdMapping[ settingValue.nav_menu_term_id ] ) {
					settingValue.nav_menu_term_id = insertedMenuIdMapping[ settingValue.nav_menu_term_id ];
				}

				newCustomizeId = 'nav_menu_item[' + String( update.post_id ) + ']';
				newSetting = api.create( newCustomizeId, newCustomizeId, settingValue, {
					type: 'nav_menu_item',
					transport: 'postMessage',
					previewer: api.previewer
				} );

				// Add the menu control.
				newControl = new api.controlConstructor.nav_menu_item( newCustomizeId, {
					params: {
						type: 'nav_menu_item',
						content: '<li id="customize-control-nav_menu_item-' + String( update.post_id ) + '" class="customize-control customize-control-nav_menu_item"></li>',
						menu_id: update.post_id,
						section: 'nav_menu[' + String( settingValue.nav_menu_term_id ) + ']',
						priority: oldControl.priority.get(),
						active: true,
						settings: {
							'default': newCustomizeId
						},
						menu_item_id: update.post_id
					},
					previewer: api.previewer
				} );

				// Remove old setting and control.
				oldControl.container.remove();
				api.control.remove( oldCustomizeId );

				// Add new control to take its place.
				api.control.add( newCustomizeId, newControl );

				// Delete the placeholder and preview the new setting.
				oldSetting.callbacks.disable(); // Prevent setting triggering Customizer dirty state when set.
				oldSetting.set( false );
				oldSetting.preview();
				newSetting.preview();

				newControl.container.toggleClass( 'menu-item-edit-inactive', oldControl.container.hasClass( 'menu-item-edit-inactive' ) );
			}
		});

		// @todo trigger change event for each Custom Menu widget that was modified.
	};

	/**
	 * Focus a menu item control.
	 *
	 * @param {string} menuItemId
	 */
	api.Menus.focusMenuItemControl = function( menuItemId ) {
		var control = api.Menus.getMenuItemControl( menuItemId );

		if ( control ) {
			control.focus();
		}
	};

	/**
	 * Get the control for a given menu.
	 *
	 * @param menuId
	 * @return {wp.customize.controlConstructor.menus[]}
	 */
	api.Menus.getMenuControl = function( menuId ) {
		return api.control( 'nav_menu[' + menuId + ']' );
	};

	/**
	 * Given a menu item type & object, get the label associated with it.
	 *
	 * @param {string} type
	 * @param {string} object
	 * @return {string}
	 */
	api.Menus.getTypeLabel = function( type, object ) {
		var label,
			data = api.Menus.data;

		if ( 'post_type' === type ) {
			if ( data.itemTypes.postTypes[ object ] ) {
				label = data.itemTypes.postTypes[ object ].label;
			} else {
				label = data.l10n.postTypeLabel;
			}
		} else if ( 'taxonomy' === type ) {
			if ( data.itemTypes.taxonomies[ object ] ) {
				label = data.itemTypes.taxonomies[ object ].label;
			} else {
				label = data.l10n.taxonomyTermLabel;
			}
		} else {
			label = data.l10n.custom_label;
		}

		return label;
	};

	/**
	 * Given a menu item ID, get the control associated with it.
	 *
	 * @param {string} menuItemId
	 * @return {object|null}
	 */
	api.Menus.getMenuItemControl = function( menuItemId ) {
		return api.control( menuItemIdToSettingId( menuItemId ) );
	};

	/**
	 * @param {String} menuItemId
	 */
	function menuItemIdToSettingId( menuItemId ) {
		return 'nav_menu_item[' + menuItemId + ']';
	}

	/**
	 * Apply sanitize_text_field()-like logic to the supplied name, returning a
	 * "unnammed" fallback string if the name is then empty.
	 *
	 * @param {string} name
	 * @returns {string}
	 */
	function displayNavMenuName( name ) {
		name = $( '<div>' ).text( name ).html(); // Emulate esc_html() which is used in wp-admin/nav-menus.php.
		name = $.trim( name );
		return name || api.Menus.data.l10n.unnamed;
	}

})( wp.customize, wp, jQuery );
