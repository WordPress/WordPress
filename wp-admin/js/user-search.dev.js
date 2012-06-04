jQuery( function($) {
	var id = typeof( current_site_id ) != 'undefined' ? '&site_id=' + current_site_id : '',
	    isRTL = !! ( 'undefined' != typeof isRtl && isRtl ),
	    position = isRTL ? { my: 'right top', at: 'right bottom', offset: '0, -1' } : { offset: '0, -1' },
	    open = function(e, ui) {
	    	$(this).addClass('open');
	    },
	    close = function(e, ui) {
	    	$(this).removeClass('open');
	    };

	$( '#adduser-email, #newuser' ).autocomplete({
		source:    ajaxurl + '?action=autocomplete-user&autocomplete_type=add' + id,
		delay:     500,
		minLength: 2,
		position:  position,
		open:      open,
		close:     close
	});

	$( '#user-search-input' ).autocomplete({
		source:    ajaxurl + '?action=autocomplete-user&autocomplete_type=search' + id,
		delay:     500,
		minLength: 2,
		position:  position,
		open:      open,
		close:     close
	});

	$( '#all-user-search-input' ).autocomplete({
		source:    ajaxurl + '?action=autocomplete-user&autocomplete_type=search-all' + id,
		delay:     500,
		minLength: 2,
		position:  position,
		open:      open,
		close:     close
	});
});
