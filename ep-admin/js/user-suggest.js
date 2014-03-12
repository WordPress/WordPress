/* global ajaxurl, current_site_id, isRtl */

(function( $ ) {
	var id = ( typeof current_site_id !== 'undefined' ) ? '&site_id=' + current_site_id : '';
	$(document).ready( function() {
		var position = { offset: '0, -1' };
		if ( typeof isRtl !== 'undefined' && isRtl ) {
			position.my = 'right top';
			position.at = 'right bottom';
		}
		$( '.wp-suggest-user' ).each( function(){
			var $this = $( this ),
				autocompleteType = ( typeof $this.data( 'autocompleteType' ) !== 'undefined' ) ? $this.data( 'autocompleteType' ) : 'add',
				autocompleteField = ( typeof $this.data( 'autocompleteField' ) !== 'undefined' ) ? $this.data( 'autocompleteField' ) : 'user_login';

			$this.autocomplete({
				source:    ajaxurl + '?action=autocomplete-user&autocomplete_type=' + autocompleteType + '&autocomplete_field=' + autocompleteField + id,
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
	});
})( jQuery );
