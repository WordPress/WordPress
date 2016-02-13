/* global JSON, _wpCustomizePreviewNavMenusExports */

( function( $, _, wp ) {
	'use strict';

	if ( ! wp || ! wp.customize ) { return; }

	var api = wp.customize,
		currentRefreshDebounced = {},
		refreshDebounceDelay = 200,
		settings = {},
		defaultSettings = {
			renderQueryVar: null,
			renderNonceValue: null,
			renderNoncePostKey: null,
			requestUri: '/',
			navMenuInstanceArgs: {},
			l10n: {}
		};

	api.MenusCustomizerPreview = {
		/**
		 * Bootstrap functionality.
		 */
		init : function() {
			var self = this, initializedSettings = {};

			settings = _.extend( {}, defaultSettings );
			if ( 'undefined' !== typeof _wpCustomizePreviewNavMenusExports ) {
				_.extend( settings, _wpCustomizePreviewNavMenusExports );
			}

			api.each( function( setting, id ) {
				setting.id = id;
				initializedSettings[ setting.id ] = true;
				self.bindListener( setting );
			} );

			api.preview.bind( 'setting', function( args ) {
				var id, value, setting;
				args = args.slice();
				id = args.shift();
				value = args.shift();

				setting = api( id );
				if ( ! setting ) {
					// Currently customize-preview.js is not creating settings for dynamically-created settings in the pane, so we have to do it.
					setting = api.create( id, value ); // @todo This should be in core
				}
				if ( ! setting.id ) {
					// Currently customize-preview.js doesn't set the id property for each setting, like customize-controls.js does.
					setting.id = id;
				}

				if ( ! initializedSettings[ setting.id ] ) {
					initializedSettings[ setting.id ] = true;
					if ( self.bindListener( setting ) ) {
						setting.callbacks.fireWith( setting, [ setting(), null ] );
					}
				}
			} );

			self.highlightControls();
		},

		/**
		 *
		 * @param {wp.customize.Value} setting
		 * @returns {boolean} Whether the setting was bound.
		 */
		bindListener : function( setting ) {
			var matches, themeLocation;

			matches = setting.id.match( /^nav_menu\[(-?\d+)]$/ );
			if ( matches ) {
				setting.navMenuId = parseInt( matches[1], 10 );
				setting.bind( this.onChangeNavMenuSetting );
				return true;
			}

			matches = setting.id.match( /^nav_menu_item\[(-?\d+)]$/ );
			if ( matches ) {
				setting.navMenuItemId = parseInt( matches[1], 10 );
				setting.bind( this.onChangeNavMenuItemSetting );
				return true;
			}

			matches = setting.id.match( /^nav_menu_locations\[(.+?)]/ );
			if ( matches ) {
				themeLocation = matches[1];
				setting.bind( _.bind( function() {
					this.refreshMenuLocation( themeLocation );
				}, this ) );
				return true;
			}

			return false;
		},

		/**
		 * Handle changing of a nav_menu setting.
		 *
		 * @this {wp.customize.Setting}
		 */
		onChangeNavMenuSetting : function() {
			var setting = this;
			if ( ! setting.navMenuId ) {
				throw new Error( 'Expected navMenuId property to be set.' );
			}
			api.MenusCustomizerPreview.refreshMenu( setting.navMenuId );
		},

		/**
		 * Handle changing of a nav_menu_item setting.
		 *
		 * @this {wp.customize.Setting}
		 * @param {object} to
		 * @param {object} from
		 */
		onChangeNavMenuItemSetting : function( to, from ) {
			if ( from && from.nav_menu_term_id && ( ! to || from.nav_menu_term_id !== to.nav_menu_term_id ) ) {
				api.MenusCustomizerPreview.refreshMenu( from.nav_menu_term_id );
			}
			if ( to && to.nav_menu_term_id ) {
				api.MenusCustomizerPreview.refreshMenu( to.nav_menu_term_id );
			}
		},

		/**
		 * Update a given menu rendered in the preview.
		 *
		 * @param {int} menuId
		 */
		refreshMenu : function( menuId ) {
			var assignedLocations = [];

			api.each(function( setting, id ) {
				var matches = id.match( /^nav_menu_locations\[(.+?)]/ );
				if ( matches && menuId === setting() ) {
					assignedLocations.push( matches[1] );
				}
			});

			_.each( settings.navMenuInstanceArgs, function( navMenuArgs, instanceNumber ) {
				if ( menuId === navMenuArgs.menu || -1 !== _.indexOf( assignedLocations, navMenuArgs.theme_location ) ) {
					this.refreshMenuInstanceDebounced( instanceNumber );
				}
			}, this );
		},

		/**
		 * Refresh the menu(s) associated with a given nav menu location.
		 *
		 * @param {string} location
		 */
		refreshMenuLocation : function( location ) {
			var foundInstance = false;
			_.each( settings.navMenuInstanceArgs, function( navMenuArgs, instanceNumber ) {
				if ( location === navMenuArgs.theme_location ) {
					this.refreshMenuInstanceDebounced( instanceNumber );
					foundInstance = true;
				}
			}, this );
			if ( ! foundInstance ) {
				api.preview.send( 'refresh' );
			}
		},

		/**
		 * Update a specific instance of a given menu on the page.
		 *
		 * @param {int} instanceNumber
		 */
		refreshMenuInstance : function( instanceNumber ) {
			var data, menuId, customized, container, request, wpNavMenuArgs, instance, containerInstanceClassName;

			if ( ! settings.navMenuInstanceArgs[ instanceNumber ] ) {
				throw new Error( 'unknown_instance_number' );
			}
			instance = settings.navMenuInstanceArgs[ instanceNumber ];

			containerInstanceClassName = 'partial-refreshable-nav-menu-' + String( instanceNumber );
			container = $( '.' + containerInstanceClassName );

			if ( _.isNumber( instance.menu ) ) {
				menuId = instance.menu;
			} else if ( instance.theme_location && api.has( 'nav_menu_locations[' + instance.theme_location + ']' ) ) {
				menuId = api( 'nav_menu_locations[' + instance.theme_location + ']' ).get();
			}

			if ( ! menuId || ! instance.can_partial_refresh || 0 === container.length ) {
				api.preview.send( 'refresh' );
				return;
			}
			menuId = parseInt( menuId, 10 );

			data = {
				nonce: wp.customize.settings.nonce.preview,
				wp_customize: 'on'
			};
			if ( ! wp.customize.settings.theme.active ) {
				data.theme = wp.customize.settings.theme.stylesheet;
			}
			data[ settings.renderQueryVar ] = '1';

			// Gather settings to send in partial refresh request.
			customized = {};
			api.each( function( setting, id ) {
				var value = setting.get(), shouldSend = false;
				// @todo Core should propagate the dirty state into the Preview as well so we can use that here.

				// Send setting if it is a nav_menu_locations[] setting.
				shouldSend = shouldSend || /^nav_menu_locations\[/.test( id );

				// Send setting if it is the setting for this menu.
				shouldSend = shouldSend || id === 'nav_menu[' + String( menuId ) + ']';

				// Send setting if it is one that is associated with this menu, or it is deleted.
				shouldSend = shouldSend || ( /^nav_menu_item\[/.test( id ) && ( false === value || menuId === value.nav_menu_term_id ) );

				if ( shouldSend ) {
					customized[ id ] = value;
				}
			} );
			data.customized = JSON.stringify( customized );
			data[ settings.renderNoncePostKey ] = settings.renderNonceValue;

			wpNavMenuArgs = $.extend( {}, instance );
			data.wp_nav_menu_args_hash = wpNavMenuArgs.args_hash;
			delete wpNavMenuArgs.args_hash;
			data.wp_nav_menu_args = JSON.stringify( wpNavMenuArgs );

			container.addClass( 'customize-partial-refreshing' );

			request = wp.ajax.send( null, {
				data: data,
				url: api.settings.url.self
			} );
			request.done( function( data ) {
				// If the menu is now not visible, refresh since the page layout may have changed.
				if ( false === data ) {
					api.preview.send( 'refresh' );
					return;
				}

				var eventParam, previousContainer = container;
				container = $( data );
				container.addClass( containerInstanceClassName );
				container.addClass( 'partial-refreshable-nav-menu customize-partial-refreshing' );
				previousContainer.replaceWith( container );
				eventParam = {
					instanceNumber: instanceNumber,
					wpNavArgs: wpNavMenuArgs, // @deprecated
					wpNavMenuArgs: wpNavMenuArgs,
					oldContainer: previousContainer,
					newContainer: container
				};
				container.removeClass( 'customize-partial-refreshing' );
				$( document ).trigger( 'customize-preview-menu-refreshed', [ eventParam ] );
			} );
			request.fail( function() {
				api.preview.send( 'refresh' );
			} );
		},

		refreshMenuInstanceDebounced : function( instanceNumber ) {
			if ( currentRefreshDebounced[ instanceNumber ] ) {
				clearTimeout( currentRefreshDebounced[ instanceNumber ] );
			}
			currentRefreshDebounced[ instanceNumber ] = setTimeout(
				_.bind( function() {
					this.refreshMenuInstance( instanceNumber );
				}, this ),
				refreshDebounceDelay
			);
		},

		/**
		 * Connect nav menu items with their corresponding controls in the pane.
		 */
		highlightControls: function() {
			var selector = '.menu-item',
				addTooltips;

			// Open expand the menu item control when shift+clicking the menu item
			$( document ).on( 'click', selector, function( e ) {
				var navMenuItemParts;
				if ( ! e.shiftKey ) {
					return;
				}

				navMenuItemParts = $( this ).attr( 'class' ).match( /(?:^|\s)menu-item-(\d+)(?:\s|$)/ );
				if ( navMenuItemParts ) {
					e.preventDefault();
					e.stopPropagation(); // Make sure a sub-nav menu item will get focused instead of parent items.
					api.preview.send( 'focus-nav-menu-item-control', parseInt( navMenuItemParts[1], 10 ) );
				}
			});

			addTooltips = function( e, params ) {
				params.newContainer.find( selector ).attr( 'title', settings.l10n.editNavMenuItemTooltip );
			};

			addTooltips( null, { newContainer: $( document.body ) } );
			$( document ).on( 'customize-preview-menu-refreshed', addTooltips );
		}
	};

	api.bind( 'preview-ready', function() {
		api.preview.bind( 'active', function() {
			api.MenusCustomizerPreview.init();
		} );
	} );

}( jQuery, _, wp ) );
