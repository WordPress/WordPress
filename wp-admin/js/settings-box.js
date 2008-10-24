jQuery(document).ready( function($) {
	$('#show-settings-link').click(function () {
		$('#screen-options-wrap').slideDown('normal', function(){
			$('#hide-settings-link').show();
			$('#show-settings-link').hide();
			$('#screen-options-link-wrap').removeClass('screen-options-closed').addClass('screen-options-open');
		});

		return false;
	});
	
	$('#hide-settings-link').click(function () {
		$('#screen-options-wrap').slideUp('normal', function(){
			$('#show-settings-link').show();
			$('#hide-settings-link').hide();
			$('#screen-options-link-wrap').removeClass('screen-options-open').addClass('screen-options-closed');
		});

		return false;
	});
});