/* global postboxes, commentL10n */
jQuery(document).ready( function($) {

	postboxes.add_postbox_toggles('comment');

	var $timestampdiv = $('#timestampdiv'),
		stamp = $('#timestamp').html();

	$timestampdiv.siblings('a.edit-timestamp').click( function( event ) {
		if ( $timestampdiv.is( ':hidden' ) ) {
			$timestampdiv.slideDown('fast');
			$('#mm').focus();
			$(this).hide();
		}
		event.preventDefault();
	});

	$timestampdiv.find('.cancel-timestamp').click( function( event ) {
		$timestampdiv.slideUp('fast').siblings('a.edit-timestamp').show().focus();
		$('#mm').val($('#hidden_mm').val());
		$('#jj').val($('#hidden_jj').val());
		$('#aa').val($('#hidden_aa').val());
		$('#hh').val($('#hidden_hh').val());
		$('#mn').val($('#hidden_mn').val());
		$('#timestamp').html(stamp);
		event.preventDefault();
	});

	$timestampdiv.find('.save-timestamp').click( function( event ) { // crazyhorse - multiple ok cancels
		var aa = $('#aa').val(), mm = $('#mm').val(), jj = $('#jj').val(), hh = $('#hh').val(), mn = $('#mn').val(),
			newD = new Date( aa, mm - 1, jj, hh, mn );

		event.preventDefault();

		if ( newD.getFullYear() != aa || (1 + newD.getMonth()) != mm || newD.getDate() != jj || newD.getMinutes() != mn ) {
			$timestampdiv.find('.timestamp-wrap').addClass('form-invalid');
			return;
		} else {
			$timestampdiv.find('.timestamp-wrap').removeClass('form-invalid');
		}

		$('#timestamp').html(
			commentL10n.submittedOn + ' <b>' +
			commentL10n.dateFormat
				.replace( '%1$s', $( 'option[value="' + mm + '"]', '#mm' ).attr( 'data-text' ) )
				.replace( '%2$s', parseInt( jj, 10 ) )
				.replace( '%3$s', aa )
				.replace( '%4$s', ( '00' + hh ).slice( -2 ) )
				.replace( '%5$s', ( '00' + mn ).slice( -2 ) ) +
				'</b> '
		);

		$timestampdiv.slideUp('fast');
		$timestampdiv.siblings('a.edit-timestamp').show();
	});
});
