var farbtastic;
function pickColor(a) {
	farbtastic.setColor(a);
	jQuery("#link-color").val(a);
	jQuery("#link-color").css("background-color", a);
}
jQuery(document).ready(function() {
	jQuery("#pickcolor").click(function() {
		jQuery("#colorPickerDiv").show();
		return false;
	});
	jQuery("#link-color").keyup(function() {
		var b = jQuery("#link-color").val(),
		a = b;
		if (a.charAt(0) != "#") {
			a = "#" + a;
		}
		a = a.replace(/[^#a-fA-F0-9]+/, "");
		if (a != b) {
			jQuery("#link-color").val(a);
		}
		if (a.length == 4 || a.length == 7) {
			pickColor(a);
		}
	});
	farbtastic = jQuery.farbtastic("#colorPickerDiv",
	function(a) {
		pickColor(a);
	});
	pickColor(jQuery("#link-color").val());
	jQuery(document).mousedown(function() {
		jQuery("#colorPickerDiv").each(function() {
			var a = jQuery(this).css("display");
			if (a == "block") {
				jQuery(this).fadeOut(2);
			}
		});
	});
});