addLoadEvent( function() {
	add_postbox_toggles('comment');

	jQuery('#timestamp').css('display', 'none');
	jQuery('.edit-timestamp').click(function () {
		if (jQuery('#timestamp').is(":hidden")) {
			jQuery('#timestamp').slideDown("normal");
		} else {
			jQuery('#timestamp').hide();
		}
    });
});


