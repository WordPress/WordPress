jQuery(document).ready( function($) {
	postboxes.add_postbox_toggles('page');
	make_slugedit_clickable();

	// close postboxes that should be closed
	jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');

	jQuery('#title').blur( function() { if ( (jQuery("#post_ID").val() > 0) || (jQuery("#title").val().length == 0) ) return; autosave(); } );

	var stamp = $('#timestamp').html();

	var visibility = $('#post-visibility-display').html();

	function updateVisibility() {
		if ( $('#post-visibility-select input:radio:checked').val() != 'public' ) {
			$('#sticky').attr('checked', false);
			$('#sticky-span').hide();
		} else {
			$('#sticky-span').show();
		}
		if ( $('#post-visibility-select input:radio:checked').val() != 'password' ) {
			$('#password-span').hide();
		} else {
			$('#password-span').show();
		}
	}

	function updateText() {
		var attemptedDate = new Date( $('#aa').val(), $('#mm').val() -1, $('#jj').val(), $('#hh').val(), $('#mn').val());
		var originalDate = new Date( $('#hidden_aa').val(), $('#hidden_mm').val() -1, $('#hidden_jj').val(), $('#hidden_hh').val(), $('#hidden_mn').val());
		var currentDate = new Date( $('#cur_aa').val(), $('#cur_mm').val() -1, $('#cur_jj').val(), $('#cur_hh').val(), $('#cur_mn').val());
		if ( attemptedDate > currentDate && $('#original_post_status').val() != 'future' ) {
			var publishOn = postL10n.publishOnFuture;
			$('#publish').val( postL10n.schedule );
		} else if ( attemptedDate <= currentDate && $('#original_post_status').val() != 'publish' ) {
			var publishOn = postL10n.publishOn;
			$('#publish').val( postL10n.publish );
		} else {
			var publishOn = postL10n.publishOnPast;
			$('#publish').val( postL10n.update );
		}
		if ( originalDate.toUTCString() == attemptedDate.toUTCString() ) { //hack
			$('#timestamp').html(stamp);
		} else {
			$('#timestamp').html(
				publishOn + ' <b>' +
				$( '#mm option[value=' + $('#mm').val() + ']' ).text() + ' ' +
				$('#jj').val() + ', ' +
				$('#aa').val() + ' @ ' +
				$('#hh').val() + ':' +
				$('#mn').val() + '</b> '
			);
		}

		if ( $('#post-visibility-select input:radio:checked').val() == 'private' ) {
			$('#publish').val( postL10n.update );
			if ( $('#post_status option[value=publish]').length == 0 ) {
				$('#post_status').append('<option value="publish">' + postL10n.privatelyPublished + '</option>');
			}
			$('#post_status option[value=publish]').html( postL10n.privatelyPublished );
			$('#post_status option[value=publish]').attr('selected', true);
			$('.edit-post-status').hide();
		} else {
			if ( $('#original_post_status').val() == 'future' || $('#original_post_status').val() == 'draft' ) {
				if ( $('#post_status option[value=publish]').length != 0 ) {
					$('#post_status option[value=publish]').remove();
					$('#post_status').val($('#hidden_post_status').val());
				}
			} else {
				$('#post_status option[value=publish]').html( postL10n.published );
			}
			$('.edit-post-status').show();
		}
		$('#post-status-display').html($('#post_status :selected').text());
		if ( $('#post_status :selected').val() == 'private' || $('#post_status :selected').val() == 'publish' ) {
			$('#save-post').hide();
		} else {
			$('#save-post').show();
			if ( $('#post_status :selected').val() == 'pending' ) {
				$('#save-post').show().val( postL10n.savePending );
			} else {
				$('#save-post').show().val( postL10n.saveDraft );
			}
		}
	}

	$('.edit-visibility').click(function () {
		if ($('#post-visibility-select').is(":hidden")) {
			updateVisibility();
			$('#post-visibility-select').slideDown("normal");
			$('.edit-visibility').hide();
		}
		return false;
	});

	$('.cancel-post-visibility').click(function () {
		$('#post-visibility-select').slideUp("normal");
		$('#visibility-radio-' + $('#hidden-post-visibility').val()).attr('checked', true);
		$('#post_password').val($('#hidden_post_password').val());
		$('#post-visibility-display').html(visibility);
		$('.edit-visibility').show();
		updateText();
		return false;
	});

	$('.save-post-visibility').click(function () { // crazyhorse - multiple ok cancels
		$('#post-visibility-select').slideUp("normal");
		$('.edit-visibility').show();
		updateText();

		$('#post-visibility-display').html(
			postL10n[$('#post-visibility-select input:radio:checked').val()]
		);

		return false;
	});

	$('#post-visibility-select input:radio').change(function() {
		updateVisibility();
	});

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
		$('.edit-timestamp').show();

		updateText();
		return false;
	});

	$('.save-timestamp').click(function () { // crazyhorse - multiple ok cancels
		$('#timestampdiv').slideUp("normal");
		$('.edit-timestamp').show();
		updateText();

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
		$('.edit-post-status').show();
		updateText();
		return false;
	});

	$('.cancel-post-status').click(function() {
		$('#post-status-select').slideUp("normal");
		$('#post_status').val($('#hidden_post_status').val());
		$('.edit-post-status').show();

		updateText();
		return false;
	});

	// Custom Fields
	jQuery('#the-list').wpList( { addAfter: function( xml, s ) {
		$('table#list-table').show();
		if ( jQuery.isFunction( autosave_update_post_ID ) ) {
			autosave_update_post_ID(s.parsed.responses[0].supplemental.postid);
		}
	}, addBefore: function( s ) {
		s.data += '&post_id=' + jQuery('#post_ID').val();
		return s;
	}
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
