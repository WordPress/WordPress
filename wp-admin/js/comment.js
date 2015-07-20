/* global postboxes, commentL10n */
jQuery(document).ready( function($) {

	postboxes.add_postbox_toggles('comment');

	var stamp = $('#timestamp').html();
	$('.edit-timestamp').click(function () {
		if ($('#timestampdiv').is(':hidden')) {
			$('#timestampdiv').slideDown('normal');
			$('.edit-timestamp').hide();
		}
		return false;
	});

	$('.cancel-timestamp').click(function() {
		$('#timestampdiv').slideUp('normal');
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
		var aa = $('#aa').val(), mm = $('#mm').val(), jj = $('#jj').val(), hh = $('#hh').val(), mn = $('#mn').val(),
			newD = new Date( aa, mm - 1, jj, hh, mn );

		if ( newD.getFullYear() != aa || (1 + newD.getMonth()) != mm || newD.getDate() != jj || newD.getMinutes() != mn ) {
			$('.timestamp-wrap', '#timestampdiv').addClass('form-invalid');
			return false;
		} else {
			$('.timestamp-wrap', '#timestampdiv').removeClass('form-invalid');
		}

		$('#timestampdiv').slideUp('normal');
		$('.edit-timestamp').show();
		$('#timestamp').html(
			commentL10n.submittedOn + ' <b>' +
			$( '#mm option[value="' + mm + '"]' ).text() + ' ' +
			jj + ', ' +
			aa + ' @ ' +
			hh + ':' +
			mn + '</b> '
		);
		return false;
	});
});
