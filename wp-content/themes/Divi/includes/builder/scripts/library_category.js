(function($){
	$( document ).ready( function() {
		var $main_table = $( '.wp-list-table' );
		var $all_links = $main_table.find( '.row-title' );
		var $opened_wp_menu = $( 'li.wp-menu-open' );

		// hide unsupported options from the Categories list table
		$main_table.find( '.row-actions .edit, .row-actions .view' ).css( 'display', 'none' );

		// close the opened categories menu which is not related to library categories
		$opened_wp_menu.find( 'a.wp-menu-open' ).removeClass( 'wp-menu-open wp-has-current-submenu' );
		$opened_wp_menu.find( '.wp-submenu' ).addClass( 'wp-not-current-submenu' );
		$opened_wp_menu.removeClass( 'wp-menu-open wp-has-current-submenu' );

		// activate quick edit instead on category tite click because full editor is not supported for the library categories
		$main_table.on( 'click', '.row-title', function() {
			$( this ).closest( 'td' ).find( '.row-actions .inline a' ).trigger( 'click' );
			return false;
		} );

		// disable links for posts counts because it doesn't work with library items which are not public.
		$main_table.on( 'click', 'td.column-posts a', function() {
			return false;
		} );
	});
})(jQuery)