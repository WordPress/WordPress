addLoadEvent( function() {
	add_postbox_toggles('page');
	make_slugedit_clickable();

	// hide advanced slug field
	jQuery('#pageslugdiv').hide();

	jQuery('#timestamp').css('display', 'none');
	jQuery('.edit-timestamp').click(function () {
		if (jQuery('#timestamp').is(":hidden")) {
			jQuery('#timestamp').slideDown("normal");
		} else {
			jQuery('#timestamp').hide();
		}
    });
});


