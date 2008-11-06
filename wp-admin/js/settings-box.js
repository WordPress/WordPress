jQuery(document).ready( function($) {
	$('#show-settings-link').click(function () {
		$('#screen-options-wrap').slideToggle('fast', function(){
			if ( $(this).hasClass('screen-options-open') ) {
				$('#show-settings-link').css({'backgroundImage':'url("images/screen-options-right.gif")'});
				$('#contextual-help-link-wrap').removeClass('invisible');
				$(this).removeClass('screen-options-open');
				
			} else {
				$('#show-settings-link').css({'backgroundImage':'url("images/screen-options-right-up.gif")'});
				$('#contextual-help-link-wrap').addClass('invisible');
				$(this).addClass('screen-options-open');
			}
		});
		return false;
	}).parent();
	$('#contextual-help-link').click(function () {
		$('#contextual-help-wrap').slideToggle('fast', function(){
			if ( $(this).hasClass('contextual-help-open') ) {
				$('#contextual-help-link').css({'backgroundImage':'url("images/screen-options-right.gif")'});
				$('#screen-options-link-wrap').removeClass('invisible');
				$(this).removeClass('contextual-help-open');
			} else {
				$('#contextual-help-link').css({'backgroundImage':'url("images/screen-options-right-up.gif")'});
				$('#screen-options-link-wrap').addClass('invisible');
				$(this).addClass('contextual-help-open');
			}
		});
		return false;
	});
});