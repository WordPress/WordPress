jQuery(document).ready( function($) {
	postboxes.add_postbox_toggles('page');
	make_slugedit_clickable();

	// close postboxes that should be closed
	jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');

	jQuery('#title').blur( function() { if ( (jQuery("#post_ID").val() > 0) || (jQuery("#title").val().length == 0) ) return; autosave(); } );

	// hide advanced slug field
	jQuery('#pageslugdiv').hide();

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

	// Edit Settings
	$('#show-settings-link').click(function () {
		$('#edit-settings').slideDown('normal', function(){
			$('#show-settings-link').hide();
			$('#hide-settings-link').show();
			
		});
		$('#show-settings').addClass('show-settings-opened');
		return false;
	});
	
	$('#hide-settings-link').click(function () {
		$('#edit-settings').slideUp('normal', function(){
			$('#hide-settings-link').hide();
			$('#show-settings-link').show();
			$('#show-settings').removeClass('show-settings-opened');
		});
		
		return false;
	});
});