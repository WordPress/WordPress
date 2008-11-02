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

		var attemptedDate = new Date( $('#aa').val(), $('#mm').val() -1, $('#jj').val(), $('#hh').val(), $('#mn').val() );
		var currentDate = new Date( $('#cur_aa').val(), $('#cur_mm').val() -1, $('#cur_jj').val(), $('#cur_hh').val(), $('#cur_mn').val() );
		if ( attemptedDate > currentDate ) {
			$('#publish').val( postL10n.schedule );
		} else if ( $('#original_post_status').val() != 'publish' ) {
			$('#publish').val( postL10n.publish );
		} else {
			$('#publish').val( postL10n.update );
		}

		return false;
	});

	$('.save-timestamp').click(function () { // crazyhorse - multiple ok cancels
		$('#timestampdiv').slideUp("normal");
		$('.edit-timestamp').show();
		var attemptedDate = new Date( $('#aa').val(), $('#mm').val() -1, $('#jj').val(), $('#hh').val(), $('#mn').val() );
		var currentDate = new Date( $('#cur_aa').val(), $('#cur_mm').val() -1, $('#cur_jj').val(), $('#cur_hh').val(), $('#cur_mn').val() );
		if ( attemptedDate > currentDate ) {
			var publishOn = postL10n.publishOnFuture;
			$('#publish').val( postL10n.schedule );
		} else if ( $('#original_post_status').val() != 'publish' ) {
			var publishOn = postL10n.publishOn;
			$('#publish').val( postL10n.publish );
		} else {
			var publishOn = postL10n.publishOnPast;
			$('#publish').val( postL10n.update );
		}
		$('#timestamp').html(
			publishOn + '<br />' +
			$( '#mm option[value=' + $('#mm').val() + ']' ).text() + ' ' +
			$('#jj').val() + ', ' +
			$('#aa').val() + ' @ ' +
			$('#hh').val() + ':' +
			$('#mn').val() + ' '
		);

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
		if ( $('#post_status :selected').val() == 'pending' ) {
			$('#save-post').val( postL10n.savePending );
		} else {
			$('#save-post').val( postL10n.saveDraft );
		}
		return false;
	});

	$('.cancel-post-status').click(function() {
		$('#post-status-select').slideUp("normal");
		$('#post_status').val($('#hidden_post_status').val());
		$('#post-status-display').html($('#post_status :selected').text());
		$('.edit-post-status').show();
		if ( $('#post_status :selected').val() == 'pending' ) {
			$('#save-post').val( postL10n.savePending );
		} else {
			$('#save-post').val( postL10n.saveDraft );
		}

		return false;
	});
	
	// preview
	$('#post-preview').click(function(e){
		if ( 1 > $('#post_ID').val() && autosaveFirst ) {
			autosaveDelayPreview = true;
			autosave();
			return false;
		}

		$('input#wp-preview').val('dopreview');
		$('form#post').attr('target', 'wp-preview').submit().attr('target', '');
		$('input#wp-preview').val('');
		return false;
	});
});
