/**
 * Theme functions file
 *
 * Contains handlers for navigation and widget area.
 */

( function( $ ) {
	$( 'html' ).removeClass( 'no-js' );

	// Add dropdown toggle that display child menu items.
	$( '.main-navigation .page_item_has_children > a, .main-navigation .menu-item-has-children > a' ).append( '<button class="dropdown-toggle" aria-expanded="false">' + screenReaderText.expand + '</button>' );

	$( '.dropdown-toggle' ).click( function( e ) {
		var _this = $( this );
		e.preventDefault();
		_this.toggleClass( 'toggle-on' );
		_this.parent().next( '.children, .sub-menu' ).toggleClass( 'toggled-on' );
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
} )( jQuery );