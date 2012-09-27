(function($) {

	$(document).ready(function() {
		var bgImage = $("#custom-background-image");

		$('#background-color').wpColorPicker({
			change: function( event, ui ) {
				bgImage.css('background-color', ui.color.toString());
			},
			clear: function() {
				bgImage.css('background-color', '');
			}
		});

		$('input[name="background-position-x"]').change(function() {
			bgImage.css('background-position', $(this).val() + ' top');
		});

		$('input[name="background-repeat"]').change(function() {
			bgImage.css('background-repeat', $(this).val());
		});
	});

})(jQuery);