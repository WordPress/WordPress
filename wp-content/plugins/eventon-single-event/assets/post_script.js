/*
	Javascript code for single events - backend event post
	version: 0.1
*/
jQuery(document).ready(function($){
	$('#evcal_exlink_evcard').change(function(){
		
		if(this.checked){
			var href = $(this).attr('href');
			$(this).parent().find('#evcal_exlink').attr({'value': href });
			
		}else{
		
		}
	});
	
	//$('#eventon_event_settings_externallink_metaboxrow').remove();
});