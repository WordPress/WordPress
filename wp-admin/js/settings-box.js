jQuery(document).ready( function($) {
		$('#show-settings-link').click(function () {
		$('#edit-settings').slideDown('normal', function(){
			$('#show-settings-link').hide();
			$('#hide-settings-link').show();
			
		});
		$('#show-settings').addClass('show-settings-opened');
		return false;
	});
	
	$('#hide-settings-link').click(function () {
		$('#edit-settings').slideUp('normal', function(){
			$('#hide-settings-link').hide();
			$('#show-settings-link').show();
			$('#show-settings').removeClass('show-settings-opened');
		});
		
		return false;
	});
});