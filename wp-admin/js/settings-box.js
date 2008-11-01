jQuery(document).ready( function($) {
	$('#show-settings-link').click(function () {
		$('#screen-options-wrap').slideToggle('normal', function(){
			if ( $(this).hasClass('screen-options-open') ) {
				$('#show-settings-link').css({'backgroundImage':'url("images/screen-options-right.gif")'});
				$(this).removeClass('screen-options-open');
			} else {
				$('#show-settings-link').css({'backgroundImage':'url("images/screen-options-right-up.gif")'});
				$(this).addClass('screen-options-open');
			}
		});
		return false;
	});
});