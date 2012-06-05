(function($) {
	var id = 'undefined' !== typeof current_site_id ? '&site_id=' + current_site_id : '';
	$(document).ready( function() {
		$( '.wp-suggest-user' ).autocomplete({
			source:    ajaxurl + '?action=autocomplete-user&autocomplete_type=add' + id,
			delay:     500,
			minLength: 2,
			position:  ( 'undefined' !== typeof isRtl && isRtl ) ? { my: 'right top', at: 'right bottom', offset: '0, -1' } : { offset: '0, -1' },
			open:      function() { $(this).addClass('open'); },
			close:     function() { $(this).removeClass('open'); }
		});
	});
})(jQuery);