jQuery(document).ready( function($) {
	$('#show-settings-link').click(function () {
		$('#screen-options-wrap').slideDown('normal', function(){
			$('#hide-settings-link').show();
			$('#show-settings-link').hide();
		});

		return false;
	});
	
	$('#hide-settings-link').click(function () {
		$('#screen-options-wrap').slideUp('normal', function(){
			$('#show-settings-link').show();
			$('#hide-settings-link').hide();
		});

		return false;
	});
});