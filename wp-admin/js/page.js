jQuery(document).ready( function() {
	add_postbox_toggles('page');
	make_slugedit_clickable();

	// hide advanced slug field
	jQuery('#pageslugdiv').hide();

	jQuery('.edit-timestamp').click(function () {
		if (jQuery('#timestampdiv').is(":hidden")) {
			jQuery('#timestampdiv').slideDown("normal");
		} else {
			jQuery('#timestampdiv').hide();
		}
		return false;
    });
});