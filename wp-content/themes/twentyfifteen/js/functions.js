/* global screenReaderText */
/**
 * Theme functions file.
 *
 * Contains handlers for navigation and widget area.
 */

( function( $ ) {
	var $body, $window, sidebar, toolbarOffset;

	// Add dropdown toggle that display child menu items.
	$( '.main-navigation .page_item_has_children > a, .main-navigation .menu-item-has-children > a' ).after( '<button class="dropdown-toggle" aria-expanded="false">' + screenReaderText.expand + '</button>' );

	$( '.dropdown-toggle' ).click( function( e ) {
		var _this = $( this );
		e.preventDefault();
		_this.toggleClass( 'toggle-on' );
		_this.next( '.children, .sub-menu' ).toggleClass( 'toggled-on' );
		_this.attr( 'aria-expanded', _this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
		_this.html( _this.html() === screenReaderText.expand ? screenReaderText.collapse : screenReaderText.expand );
	} );

	// Enable menu toggle for small screens.
	( function() {
		var secondary = $( '#secondary' ), button, menu, widgets, social;
		if ( ! secondary ) {
			return;
		}

		button = $( '.site-branding' ).find( '.secondary-toggle' );
		if ( ! button ) {
			return;
		}

		// Hide button if there is no widgets and menu is missing or empty.
		menu    = secondary.find( '.nav-menu' );
		widgets = secondary.find( '#widget-area' );
		social  = secondary.find( '#social-navigation' );
		if ( ! widgets.length && ! social.length && ( ! menu || ! menu.children().length ) ) {
			button.hide();
			return;
		}

		button.on( 'click.twentyfifteen', function() {
			secondary.toggleClass( 'toggled-on' );
			secondary.trigger( 'resize' );
			$( this ).toggleClass( 'toggled-on' );
		} );
	} )();


	// Sidebar (un)fixing: fix when short, un-fix when scroll needed
	function fixedOrScrolledSidebar() {
		if ( $window.width() >= 955 ) {
			if ( sidebar.scrollHeight < ( $window.height() - toolbarOffset ) ) {
				$body.addClass( 'sidebar-fixed' );
			} else {
				$body.removeClass( 'sidebar-fixed' );
			}
		} else {
			$body.removeClass( 'sidebar-fixed' );
		}
	}

	function debouncedFixedOrScrolledSidebar() {
		var timeout;
		return function() {
			clearTimeout( timeout );
			timeout = setTimeout( function() {
				timeout = null;
				fixedOrScrolledSidebar();
			}, 150 );
		};
	}


	$( document ).ready( function() {
		// But! We only want to allow fixed sidebars when there are no submenus.
		if ( $( '#site-navigation .sub-menu' ).length ) {
			return;
		}

		// only initialize 'em if we need 'em
		$body         = $( 'body' );
		$window       = $( window );
		sidebar       = $( '#sidebar' )[0];
		toolbarOffset = $body.is( '.admin-bar' ) ? $( '#wpadminbar' ).height() : 0;

		$window
			.on( 'load.twentyfifteen', fixedOrScrolledSidebar )
			.on( 'resize.twentyfifteen', debouncedFixedOrScrolledSidebar() );
	} );

} )( jQuery );