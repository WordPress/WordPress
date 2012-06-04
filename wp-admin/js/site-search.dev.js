jQuery( function($) {
	var isRTL = !! ( 'undefined' != typeof isRtl && isRtl );
	$( '#site-search-input' ).autocomplete({
		source:    ajaxurl + '?action=autocomplete-site',
		delay:     500,
		minLength: 2,
		position:  isRTL ? { my: 'right top', at: 'right bottom', offset: '0, -1' } : { offset: '0, -1' },
		open:      function(e, ui) { $(this).addClass('open'); },
		close:     function(e, ui) { $(this).removeClass('open'); }
	});
});
