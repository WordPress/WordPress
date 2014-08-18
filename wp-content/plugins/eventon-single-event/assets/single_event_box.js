/*
	Javascript code for single events - single box
	version: 0.1
*/
jQuery(document).ready(function($){
	
	
	$('.eventon_single_event').find('.evcal_list_a').each(function(){
		
		var event = $(this);
		var calendar = $(this).closest('.eventon_event');
		
		var ev_link = $(this).siblings('.evo_event_schema').find('item[itemprop=url]').attr('href');
		
		
		if(ev_link!=''){
			event.attr({'href':ev_link, 'data-exlk':'1'});
			
			var month = event.find('.evo_month');
			//var month_e = event.find('.evo_month_end');
			
			month.html( month.attr('mo') );
			//month_e.html( month_e.attr('mo') );
		}
		
		// show event excerpt
		var ev_excerpt = event.siblings('.evcal_eventcard').find('.event_excerpt').html();

		//console.log(ev_excerpt);
		
		if(ev_excerpt!='' && calendar.data('excerpt')=='1' ){
			var appendation = '<div class="event_excerpt_in">'+ev_excerpt+'</div>'
			event.parent().append(appendation);
		}
	
	});

	$('.eventon_single_event').on('click', 'div.evcal_list_a',function(){
		var url = $(this).siblings('.evo_event_schema').find('item[itemprop=url]').attr('href');
		window.location.href= url;
	})


	// each single event box
	$('body').find('.eventon_single_event').each(function(){

		var _this = $(this);
		// show expanded eventCard
		if( _this.find('.evo-data').data('expanded')=='1'){
			_this.find('.evcal_eventcard').show();

			var idd = _this.find('.evcal_gmaps');

			// close button
			_this.find('.evcal_close').parent().css({'padding-right':0});
			_this.find('.evcal_close').hide();

			console.log(idd);
			var obj = _this.find('.desc_trig');

			obj.evoGenmaps({'fnt':2});

		// open eventBox and lightbox	
		}else if( _this.data('uxval')=='3'){

			var obj = _this.find('.desc_trig');

			// remove other attr - that cause to redirect
			obj.removeAttr('data-exlk').attr({'data-ux_val':'3'});

			
			
		}


	})
	
});