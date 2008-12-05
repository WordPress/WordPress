jQuery(document).ready( function($) {
	jQuery('.hide-if-no-js').show();
	jQuery('.hide-if-js').hide();

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
			commentL10n.submittedOn + ' <b>' +
			$( '#mm option[value=' + $('#mm').val() + ']' ).text() + ' ' +
			$('#jj').val() + ', ' +
			$('#aa').val() + ' @ ' +
			$('#hh').val() + ':' +
			$('#mn').val() + '</b> '
		);
		return false;
	});
});