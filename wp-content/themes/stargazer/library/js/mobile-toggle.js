/**
 * Mobile Menu Toggle
 *
 * Toggles a nav menu in mobile-ready designs.  The theme should have a link with the '.menu-toggle' class 
 * for toggling the menu.  The menu must be wrapped with an element with the '.wrap' and/or the '.menu-items' 
 * class.  The theme should also use media queries to handle any other design elements.  This script merely 
 * toggles the menu when the '.menu-toggle' link is clicked.
 *
 * This code is a modified version of David Chandra's original menu code for the Shell theme.
 *
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @author    David Chandra <david.warna@gmail.com>
 * @copyright Copyright (c) 2013-2014
 * @link      http://justintadlock.com
 * @link      http://shellcreeper.com
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
jQuery( document ).ready(
	function() {
		jQuery( '.menu-toggle' ).click(
			function() {
				jQuery( this ).parent().children( '.wrap, .menu-items' ).fadeToggle();
				jQuery( this ).toggleClass( 'active' );
			}
		);
	}
);