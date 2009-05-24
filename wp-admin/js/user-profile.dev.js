(function($){

	function check_pass_strength() {
		var pass = $('#pass1').val(), user = $('#user_login').val(), strength;

		$('#pass-strength-result').removeClass('short bad good strong');
		if ( ! pass ) {
			$('#pass-strength-result').html( pwsL10n.empty );
			return;
		}

		strength = passwordStrength(pass, user);

		switch ( strength ) {
			case 2:
				$('#pass-strength-result').addClass('bad').html( pwsL10n['bad'] );
				break;
			case 3:
				$('#pass-strength-result').addClass('good').html( pwsL10n['good'] );
				break;
			case 4:
				$('#pass-strength-result').addClass('strong').html( pwsL10n['strong'] );
				break;
			default:
				$('#pass-strength-result').addClass('short').html( pwsL10n['short'] );
		}
	}

	$(document).ready( function() {
		$('#pass1').val('').keyup( check_pass_strength );
		$('.color-palette').click(function(){$(this).siblings('input[name=admin_color]').attr('checked', 'checked')});
		$('#nickname').blur(function(){
			var str = $(this).val() || $('#user_login').val();
			$('#display_name #display_nickname').val(str).html(str);
		});
		$('#first_name, #last_name').blur(function(){
			var first = $('#first_name').val(), last = $('#last_name').val();
			$('#display_firstname, #display_lastname, #display_firstlast, #display_lastfirst').remove();
			if ( first && last ) {
				$('#display_name').append('<option id="display_firstname" value="' + first + '">' + first + '</option>' +
					'<option id="display_lastname" value="' + last + '">' + last + '</option>' +
					'<option id="display_firstlast" value="' + first + ' ' + last + '">' + first + ' ' + last + '</option>' +
					'<option id="display_lastfirst" value="' + last + ' ' + first + '">' + last + ' ' + first + '</option>');
			} else if ( first && !last ) {
				$('#display_name').append('<option id="display_firstname" value="' + first + '">' + first + '</option>');
			} else if ( !first && last ) {
				$('#display_name').append('<option id="display_lastname" value="' + last + '">' + last + '</option>');
			}
		});
    });

})(jQuery);
