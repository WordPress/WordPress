var farbtastic, pickColor;

(function($) {

	pickColor = function(color, cleared) {
		farbtastic.setColor(color);
		$('#background-color').val(color);
		$('#custom-background-image').css('background-color', color);
		console.log( color );
		if ( typeof cleared === 'undefined' )
			cleared = ! color || color === '#';
		if ( cleared )
			$('#clearcolor').hide();
		else
			$('#clearcolor').show();
	}

	$(document).ready(function() {

		$('#pickcolor').click(function() {
			$('#colorPickerDiv').show();
			return false;
		});

		$('#clearcolor a').click( function(e) {
			pickColor( $('#defaultcolor').val(), true );
			e.preventDefault();
		});

		$('#background-color').keyup(function() {
			var _hex = $('#background-color').val(), hex = _hex;
			if ( hex.charAt(0) != '#' )
				hex = '#' + hex;
			hex = hex.replace(/[^#a-fA-F0-9]+/, '');
			if ( hex != _hex )
				$('#background-color').val(hex);
			if ( hex.length == 4 || hex.length == 7 )
				pickColor( hex );
		});

		$('input[name="background-position-x"]').change(function() {
			$('#custom-background-image').css('background-position', $(this).val() + ' top');
		});

		$('input[name="background-repeat"]').change(function() {
			$('#custom-background-image').css('background-repeat', $(this).val());
		});

		farbtastic = $.farbtastic('#colorPickerDiv', function(color) {
			pickColor(color);
		});
		pickColor($('#background-color').val());

		$(document).mousedown(function(){
			$('#colorPickerDiv').each(function(){
				var display = $(this).css('display');
				if ( display == 'block' )
					$(this).fadeOut(2);
			});
		});
	});

})(jQuery);