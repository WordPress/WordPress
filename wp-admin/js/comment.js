addLoadEvent( function() {
	add_postbox_toggles('comment');

	jQuery('.edit-timestamp').click(function () {
		if (jQuery('#timestampdiv').is(":hidden")) {
			jQuery('#timestampdiv').slideDown("normal");
		} else {
			jQuery('#timestampdiv').hide();
		}
		return false;
    });
});