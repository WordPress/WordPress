jQuery( document ).ready( function() {

	var bbp_author_id = jQuery( '#bbp_author_id' );

	bbp_author_id.suggest( ajaxurl + '?action=bbp_suggest_user', {
		onSelect: function() {
			var value = this.value;
			bbp_author_id.val( value.substr( 0, value.indexOf( ' ' ) ) );
		}
	} );
} );