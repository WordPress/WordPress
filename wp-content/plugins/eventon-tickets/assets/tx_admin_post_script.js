/*
	Javascript: Event Tickets Calendar
	version: 0.1
*/
jQuery(document).ready(function($){
	
	

	// GET attendee list
		$('#evotx_attendees').on('click',function(){

			var data_arg = {
				action: 		'the_ajax_evotx_a1',
				eid:			$(this).data('eid'),
				wcid:			$(this).data('wcid'),
				postnonce: evotx_admin_ajax_script.postnonce, 
			};
			//console.log(data_arg);
			
			$.ajax({
				beforeSend: function(){},
				type: 'POST',
				url:evotx_admin_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//alert(data.status);
					if(data.status=='0'){
						$('.evotx_lightbox').find('.eventon_popup_text').html(data.content);
						$('.eventon_popup_text').find('.evotx span.checkin').click(function(){
							var obj = $(this);
							checkin_attendee(obj);
						});
					}else{
						$('.evotx_lightbox').find('.eventon_popup_text').html('Could not load attendee list');
					}

				},complete:function(){
					
				}
			});

		});	

	// CHECK in attendees
		function checkin_attendee(obj){

			var status = obj.attr('data-status');

			status = (status=='' || status=='check-in')? 'checked':'check-in';

			var data_arg = {
				action: 'the_ajax_evotx_a4',
				tid: obj.data('tid'),
				status:  status
			};
			$.ajax({
				beforeSend: function(){
					obj.parent().animate({'opacity':'0.3'});
				},
				type: 'POST',
				url:evors_admin_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//alert(data);
					if(data.status=='0'){
						obj.attr({'data-status':status}).html(status).removeAttr('class').addClass(status+' checkin');
					}

				},complete:function(){
					obj.parent().animate({'opacity':'1'});
				}
			});
		}
	
	
});