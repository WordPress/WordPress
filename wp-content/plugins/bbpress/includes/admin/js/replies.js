jQuery( document ).ready(function() {

	var bbp_topic_id = jQuery( '#bbp_topic_id' );

	bbp_topic_id.suggest( ajaxurl + '?action=bbp_suggest_topic', {
		onSelect: function() {
			var value = this.value;
			bbp_topic_id.val( value.substr( 0, value.indexOf( ' ' ) ) );
		}
	} );
} );