jQuery(document).ready( function() {
	add_postbox_toggles('page');
	make_slugedit_clickable();

	// hide advanced slug field
	jQuery('#pageslugdiv').hide();

	jQuery('.edit-timestamp').click(function () {
		if (jQuery('#timestampdiv').is(":hidden")) {
			jQuery('#timestampdiv').slideDown("normal");
			jQuery('.edit-timestamp').text(postL10n.cancel);
		} else {
			jQuery('#timestampdiv').hide();
			jQuery('#mm').val(jQuery('#hidden_mm').val());
			jQuery('#jj').val(jQuery('#hidden_jj').val());
			jQuery('#aa').val(jQuery('#hidden_aa').val());
			jQuery('#hh').val(jQuery('#hidden_hh').val());
			jQuery('#mn').val(jQuery('#hidden_mn').val());
			jQuery('.edit-timestamp').text(postL10n.edit);
		}
		return false;
    });
});