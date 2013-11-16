/* global ajaxurl, current_site_id, isRtl */

(function( $ ) {
	var id = ( typeof current_site_id !== 'undefined' ) ? '&site_id=' + current_site_id : '';
	$(document).ready( function() {
		var position = { offset: '0, -1' };
		if ( typeof isRtl !== 'undefined' && isRtl ) {
			position.my = 'right top';
			position.at = 'right bottom';
		}
		$( '.wp-suggest-user' ).autocomplete({
			source:    ajaxurl + '?action=autocomplete-user&autocomplete_type=add' + id,
			delay:     500,
			minLength: 2,
			position:  position,
			open: function() {
				$( this ).addClass( 'open' );
			},
			close: function() {
				$( this ).removeClass( 'open' );
			}
		});
	});
})( jQuery );