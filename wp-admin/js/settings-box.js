jQuery(document).ready( function($) {
	$('#show-settings-link').click(function () {
		$(this).hide()
		$('#edit-settings-wrap').slideDown('normal');
		return false;
	});
	
	$('#hide-settings-link').click(function () {
		$('#edit-settings-wrap').slideUp('normal', function(){
			$('#show-settings-link').show();
		});
		return false;
	});
});