jQuery( function($) {
	$( '#site-search-input' ).autocomplete({
		source:   ajaxurl + '?action=autocomplete-site',
		delay:    500,
		minLength: 2
	});
});
