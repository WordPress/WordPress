function add_postbox_toggles(page) {
	jQuery('.postbox h3').prepend('<a class="togbox">+</a> ');
	jQuery('.postbox h3').click( function() { jQuery(jQuery(this).parent().get(0)).toggleClass('closed'); save_postboxes_state(page); } );
}

function save_postboxes_state(page) {
	var closed = jQuery('.postbox').filter('.closed').map(function() { return this.id; }).get().join(',');
	jQuery.post(postboxL10n.requestFile, {
		action: 'closed-postboxes',
		closed: closed,
		closedpostboxesnonce: jQuery('#closedpostboxesnonce').val(),
		page: page
	});
}
