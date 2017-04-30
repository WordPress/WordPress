/*
	eventON backend scripts
*/
jQuery(document).ready(function($){
	
	
	//yes no buttons in event edit page
	$('#evcal_settings').on('click','.evcal_yn_btn',function(){
		// yes
		if($(this).hasClass('btn_at_no')){
			$(this).removeClass('btn_at_no');
			$(this).siblings('input').val('yes');
			
			$('#'+$(this).attr('afterstatement')).show();
			
		}else{//no
			$(this).addClass('btn_at_no');
			$(this).siblings('input').val('no');
			
			$('#'+$(this).attr('afterstatement')).hide();
		}
		
	});
	
	
	// language tab
	$('.eventon_cl_input').focus(function(){
		$(this).parent().addClass('onfocus');
	});
	$('.eventon_cl_input').blur(function(){
		$(this).parent().removeClass('onfocus');
	});
	
	// change language
	$('#evo_lang_selection').change(function(){
		var val = $(this).val();
		var url = $(this).attr('url');
		window.location.replace(url+'?page=eventon&tab=evcal_2&lang='+val);
	});
	
	// toggeling
	$('.evo_settings_toghead').on('click',function(){
		$(this).next('.evo_settings_togbox').toggle();
		$(this).toggleClass('open');
	});


});