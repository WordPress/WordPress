jQuery( function($) {
	var id = typeof( current_site_id ) != 'undefined' ? '&site_id=' + current_site_id : '';

	$( '#adduser-email, #newuser' ).autocomplete({
		source:   ajaxurl + '?action=autocomplete-user' + id,
		delay:    500,
		minLength: 2
	});
});