jQuery(function(){
	//hover states on the static widgets
	jQuery('#dialog_link, ul#icons li').hover(
		function() { jQuery(this).addClass('ui-state-hover'); }, 
		function() { jQuery(this).removeClass('ui-state-hover'); }
	);	
});

/* initialize datepicker */
jQuery(function(){
	// Datepicker
	jQuery('.wppb_datepicker').datepicker({
		inline: true,
		changeMonth: true,
		changeYear: true
	});
});