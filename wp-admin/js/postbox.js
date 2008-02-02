function add_postbox_toggles() {
	jQuery('.postbox h3').prepend('<a class="togbox">+</a> ');
	jQuery('.togbox').click( function() { jQuery(jQuery(this).parent().parent().get(0)).toggleClass('closed'); save_postboxes_state(); } );
}

function save_postboxes_state() {
	var closed = jQuery('.postbox').filter('.closed').map(function() { return this.id; }).get().join(',');
	jQuery.post(postboxL10n.requestFile, {
		action: 'closed-postboxes',
		closed: closed,
		cookie: document.cookie});
}