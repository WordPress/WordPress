jQuery(document).ready( function() {
	postboxes.add_postbox_toggles('comment');

	// close postboxes that should be closed
	jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');

	// show things that should be visible, hide what should be hidden
	jQuery('.hide-if-no-js').show();
	jQuery('.hide-if-js').hide();

	jQuery('.edit-timestamp').click(function () {
		if (jQuery('#timestampdiv').is(":hidden")) {
			jQuery('#curtime').slideUp("normal");
			jQuery('#timestampdiv').slideDown("normal");
		} else {
			jQuery('#timestampdiv').slideUp("normal");
			jQuery('#mm').val(jQuery('#hidden_mm').val());
			jQuery('#jj').val(jQuery('#hidden_jj').val());
			jQuery('#aa').val(jQuery('#hidden_aa').val());
			jQuery('#hh').val(jQuery('#hidden_hh').val());
			jQuery('#mn').val(jQuery('#hidden_mn').val());
			jQuery('#curtime').slideDown("normal");
		}
		return false;
	});

	jQuery('.save-timestamp').click(function () { // crazyhorse - multiple ok cancels
		jQuery('#timestampdiv').hide();
		var link = jQuery('.timestamp a').clone( true );
		jQuery('.timestamp').show().html(
			jQuery( '#mm option[value=' + jQuery('#mm').val() + ']' ).text() + ' ' +
			jQuery('#jj').val() + ',' +
			jQuery('#aa').val() + '@' +
			jQuery('#hh').val() + ':' +
			jQuery('#mn').val() + ' '
		).append( link );
		jQuery('#curtime').slideDown("normal");
		return false;
	});
});
