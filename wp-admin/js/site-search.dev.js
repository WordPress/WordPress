jQuery( function($) {
	$( '#site-search-input' ).autocomplete({
		source:   ajaxurl + '?action=autocomplete-site',
		delay:    500,
		minLength: 2,
		open: function(e, ui) { $(this).addClass('open'); },
		close: function(e, ui) { $(this).removeClass('open'); }
	});
});
