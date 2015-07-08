/*global jQuery, JSON, _wpCustomizePreviewNavMenusExports, _ */

wp.customize.menusPreview = ( function( $, api ) {
	'use strict';
	var self;

	self = {
		renderQueryVar: null,
		renderNonceValue: null,
		renderNoncePostKey: null,
		previewCustomizeNonce: null,
		requestUri: '/',
		theme: {
			active: false,
			stylesheet: ''
		},
		navMenuInstanceArgs: {},
		refreshDebounceDelay: 200
	};

	api.bind( 'preview-ready', function() {
		api.preview.bind( 'active', function() {
			self.init();
		} );
	} );

	/**
	 * Bootstrap functionality.
	 */
	self.init = function() {
		var self = this;

		if ( 'undefined' !== typeof _wpCustomizePreviewNavMenusExports ) {
			$.extend( self, _wpCustomizePreviewNavMenusExports );
		}

		api.each( function( setting, id ) {
			setting.id = id;
			self.bindListener( setting );
		} );

		api.preview.bind( 'setting', function( args ) {
			var id, value, setting;
			args = args.slice();
			id = args.shift();
			value = args.shift();
			if ( ! api.has( id ) ) {
				// Currently customize-preview.js is not creating settings for dynamically-created settings in the pane; so we have to do it
				setting = api.create( id, value ); // @todo This should be in core
				setting.id = id;
				if ( self.bindListener( setting ) ) {
					setting.callbacks.fireWith( setting, [ setting(), null ] );
				}
			}
		} );
	};

	/**
	 *
	 * @param {wp.customize.Value} setting
	 * @returns {boolean} Whether the setting was bound.
	 */
	self.bindListener = function( setting ) {
		var matches, themeLocation;

		matches = setting.id.match( /^nav_menu\[(-?\d+)]$/ );
		if ( matches ) {
			setting.navMenuId = parseInt( matches[1], 10 );
			setting.bind( self.onChangeNavMenuSetting );
			return true;
		}

		matches = setting.id.match( /^nav_menu_item\[(-?\d+)]$/ );
		if ( matches ) {
			setting.navMenuItemId = parseInt( matches[1], 10 );
			setting.bind( self.onChangeNavMenuItemSetting );
			return true;
		}

		matches = setting.id.match( /^nav_menu_locations\[(.+?)]/ );
		if ( matches ) {
			themeLocation = matches[1];
			setting.bind( function() {
				self.refreshMenuLocation( themeLocation );
			} );
			return true;
		}

		return false;
	};

	/**
	 * Handle changing of a nav_menu setting.
	 *
	 * @this {wp.customize.Setting}
	 */
	self.onChangeNavMenuSetting = function() {
		var setting = this;
		if ( ! setting.navMenuId ) {
			throw new Error( 'Expected navMenuId property to be set.' );
		}
		self.refreshMenu( setting.navMenuId );
	};

	/**
	 * Handle changing of a nav_menu_item setting.
	 *
	 * @this {wp.customize.Setting}
	 * @param {object} to
	 * @param {object} from
	 */
	self.onChangeNavMenuItemSetting = function( to, from ) {
		if ( from && from.nav_menu_term_id && ( ! to || from.nav_menu_term_id !== to.nav_menu_term_id ) ) {
			self.refreshMenu( from.nav_menu_term_id );
		}
		if ( to && to.nav_menu_term_id ) {
			self.refreshMenu( to.nav_menu_term_id );
		}
	};

	/**
	 * Update a given menu rendered in the preview.
	 *
	 * @param {int} menuId
	 */
	self.refreshMenu = function( menuId ) {
		var self = this, assignedLocations = [];

		api.each(function( setting, id ) {
			var matches = id.match( /^nav_menu_locations\[(.+?)]/ );
			if ( matches && menuId === setting() ) {
				assignedLocations.push( matches[1] );
			}
		});

		_.each( self.navMenuInstanceArgs, function( navMenuArgs, instanceNumber ) {
			if ( menuId === navMenuArgs.menu || -1 !== _.indexOf( assignedLocations, navMenuArgs.theme_location ) ) {
				self.refreshMenuInstanceDebounced( instanceNumber );
			}
		} );
	};

	self.refreshMenuLocation = function( location ) {
		var foundInstance = false;
		_.each( self.navMenuInstanceArgs, function( navMenuArgs, instanceNumber ) {
			if ( location === navMenuArgs.theme_location ) {
				self.refreshMenuInstanceDebounced( instanceNumber );
				foundInstance = true;
			}
		} );
		if ( ! foundInstance ) {
			api.preview.send( 'refresh' );
		}
	};

	/**
	 * Update a specific instance of a given menu on the page.
	 *
	 * @param {int} instanceNumber
	 */
	self.refreshMenuInstance = function( instanceNumber ) {
		var self = this, data, customized, container, request, wpNavArgs, instance;

		if ( ! self.navMenuInstanceArgs[ instanceNumber ] ) {
			throw new Error( 'unknown_instance_number' );
		}
		instance = self.navMenuInstanceArgs[ instanceNumber ];

		container = $( '#partial-refresh-menu-container-' + String( instanceNumber ) );

		if ( ! instance.can_partial_refresh || 0 === container.length ) {
			api.preview.send( 'refresh' );
			return;
		}

		data = {
			nonce: self.previewCustomizeNonce, // for Customize Preview
			wp_customize: 'on'
		};
		if ( ! self.theme.active ) {
			data.theme = self.theme.stylesheet;
		}
		data[ self.renderQueryVar ] = '1';
		customized = {};
		api.each( function( setting, id ) {
			// @todo We need to limit this to just the menu items that are associated with this menu/location.
			if ( /^(nav_menu|nav_menu_locations)/.test( id ) ) {
				customized[ id ] = setting.get();
			}
		} );
		data.customized = JSON.stringify( customized );
		data[ self.renderNoncePostKey ] = self.renderNonceValue;

		wpNavArgs = $.extend( {}, instance );
		data.wp_nav_menu_args_hash = wpNavArgs.args_hash;
		delete wpNavArgs.args_hash;
		data.wp_nav_menu_args = JSON.stringify( wpNavArgs );

		container.addClass( 'customize-partial-refreshing' );

		request = wp.ajax.send( null, {
			data: data,
			url: self.requestUri
		} );
		request.done( function( data ) {
			var eventParam;
			container.empty().append( $( data ) );
			eventParam = {
				instanceNumber: instanceNumber,
				wpNavArgs: wpNavArgs
			};
			$( document ).trigger( 'customize-preview-menu-refreshed', [ eventParam ] );
		} );
		request.fail( function() {
			// @todo provide some indication for why
		} );
		request.always( function() {
			container.removeClass( 'customize-partial-refreshing' );
		} );
	};

	self.currentRefreshMenuInstanceDebouncedCalls = {};

	self.refreshMenuInstanceDebounced = function( instanceNumber ) {
		if ( self.currentRefreshMenuInstanceDebouncedCalls[ instanceNumber ] ) {
			clearTimeout( self.currentRefreshMenuInstanceDebouncedCalls[ instanceNumber ] );
		}
		self.currentRefreshMenuInstanceDebouncedCalls[ instanceNumber ] = setTimeout(
			function() {
				self.refreshMenuInstance( instanceNumber );
			},
			self.refreshDebounceDelay
		);
	};

	return self;

}( jQuery, wp.customize ) );
