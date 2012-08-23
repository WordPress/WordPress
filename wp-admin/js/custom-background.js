var farbtastic, pickColor;

(function($) {

	var defaultColor = '';

	pickColor = function(color) {
		farbtastic.setColor(color);
		$('#background-color').val(color);
		$('#custom-background-image').css('background-color', color);
		// If we have a default color, and they match, then we need to hide the 'Default' link.
		// Otherwise, we hide the 'Clear' link when it is empty.
		if ( ( defaultColor && color === defaultColor ) || ( ! defaultColor && ( '' === color || '#' === color ) ) )
			$('#clearcolor').hide();
		else
			$('#clearcolor').show();
	}

	$(document).ready(function() {

		defaultColor = $('#defaultcolor').val();

		$('#pickcolor').click(function() {
			$('#colorPickerDiv').show();
			return false;
		});

		$('#clearcolor a').click( function(e) {
			pickColor( defaultColor );
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