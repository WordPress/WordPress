jQuery(document).ready( function($) {
	postboxes.add_postbox_toggles('page');
	make_slugedit_clickable();

	// close postboxes that should be closed
	jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');

	jQuery('#title').blur( function() { if ( (jQuery("#post_ID").val() > 0) || (jQuery("#title").val().length == 0) ) return; autosave(); } );

	// hide advanced slug field
	jQuery('#pageslugdiv').hide();

	var stamp = $('#timestamp').html();

	$('.edit-timestamp').click(function () {
		if ($('#timestampdiv').is(":hidden")) {
			$('#timestampdiv').slideDown("normal");
			$('.edit-timestamp').hide();
		}

		return false;
	});

	$('.cancel-timestamp').click(function() {
		$('#timestampdiv').slideUp("normal");
		$('#mm').val($('#hidden_mm').val());
		$('#jj').val($('#hidden_jj').val());
		$('#aa').val($('#hidden_aa').val());
		$('#hh').val($('#hidden_hh').val());
		$('#mn').val($('#hidden_mn').val());
		$('#timestamp').html(stamp);
		$('.edit-timestamp').show();

		return false;
	});

	$('.save-timestamp').click(function () { // crazyhorse - multiple ok cancels
		$('#timestampdiv').slideUp("normal");
		$('.edit-timestamp').show();
		$('#timestamp').html(
			$( '#mm option[value=' + $('#mm').val() + ']' ).text() + ' ' +
			$('#jj').val() + ', ' +
			$('#aa').val() + ' @ ' +
			$('#hh').val() + ':' +
			$('#mn').val() + ' '
		);

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

	$('.edit-post-status').click(function() {
		if ($('#post-status-select').is(":hidden")) {
			$('#post-status-select').slideDown("normal");
			$(this).hide();
		}

		return false;
	});

	$('.save-post-status').click(function() {
		$('#post-status-select').slideUp("normal");
		$('#post-status-display').html($('#post_status :selected').text());
		$('.edit-post-status').show();
		
		return false;
	});
	
	$('.cancel-post-status').click(function() {
		$('#post-status-select').slideUp("normal");
		$('#post_status').val($('#hidden_post_status').val());
		$('#post-status-display').html($('#post_status :selected').text());
		$('.edit-post-status').show();
		
		return false;
	});
});
