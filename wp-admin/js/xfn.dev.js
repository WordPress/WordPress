jQuery(document).ready( function($) {
	$('#link_rel').prop('readonly', true);
	$('#linkxfndiv input').bind('click keyup', function() {
		var isMe = $('#me').is(':checked'), inputs = '';
		$('input.valinp').each( function() {
			if (isMe) {
				$(this).prop('disabled', true).parent().addClass('disabled');
			} else {
				$(this).removeAttr('disabled').parent().removeClass('disabled');
				if ( $(this).is(':checked') && $(this).val() != '')
					inputs += $(this).val() + ' ';
			}
		});
		$('#link_rel').val( (isMe) ? 'me' : inputs.substr(0,inputs.length - 1) );
	});
});
