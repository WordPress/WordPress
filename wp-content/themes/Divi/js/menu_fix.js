(function($){
	$( document ).ready( function() {
		var $menu_item = $( '#toplevel_page_et_divi_options' );

		if ( $menu_item.length ) {
			var $first_menu_item = $menu_item.find( '.wp-first-item' );

			if ( 'Divi' === $first_menu_item.find( 'a' ).text() ) {
				$first_menu_item.remove();
			}
		}
	});
})(jQuery)