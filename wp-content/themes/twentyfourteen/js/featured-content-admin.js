/* global ajaxurl:true */
jQuery( function( $ ) {
	$( '#customize-control-featured-content-tag-name input' ).suggest( ajaxurl + '?action=ajax-tag-search&tax=post_tag', { delay: 500, minchars: 2 } );
} );
