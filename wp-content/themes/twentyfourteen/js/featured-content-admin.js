jQuery( document ).ready( function( $ ) {
	$( '#featured-content-tag-name' ).suggest( ajaxurl + '?action=ajax-tag-search&tax=post_tag', { delay: 500, minchars: 2 } );
} );