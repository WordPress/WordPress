var buttons = ['#pickcolor'], farbtastic;

function pickColor(color) {
	jQuery('#background-color').val(color);
	farbtastic.setColor(color);
	jQuery('#custom-background-image').css('background-color', color);
}

jQuery(document).ready(function() {
	jQuery('#pickcolor').click(function() {
		jQuery('#colorPickerDiv').show();
	});
	jQuery('#background-color').keyup(function() {
		var _hex = jQuery('#background-color').val();
		var hex = _hex;
		if ( hex[0] != '#' )
			hex = '#' + hex;
		hex = hex.replace(/[^#a-fA-F0-9]+/, '');
		if ( hex != _hex )
			jQuery('#background-color').val(hex);
		if ( hex.length == 4 || hex.length == 7 )
			pickColor( hex );
	});
	jQuery('input[name="background-position"]').change(function() {
		jQuery('#custom-background-image img').attr('align', jQuery(this).val() );
	});

	farbtastic = jQuery.farbtastic('#colorPickerDiv', function(color) {
		pickColor(color);
	});
	pickColor(customBackgroundL10n.backgroundcolor);
});

jQuery(document).mousedown(function(){
	hide_picker(); // Make the picker disappear if you click outside its div element
});

function hide_picker(what) {
	var update = false;
	jQuery('#colorPickerDiv').each(function(){
		var id = jQuery(this).attr('id');
		if ( id == what )
			return;

		var display = jQuery(this).css('display');
		if ( display == 'block' )
			jQuery(this).fadeOut(2);
	});
}