/*
	
	EventON Generate Google maps function

*/


(function($){
	$.fn.evoGenmaps = function(opt){
		
		var defaults = {
			delay:	0,
			fnt:	1,
			cal:	'',
			mapSpotId:	'',
			_action:''
		};
		var options = $.extend({}, defaults, opt); 
		
		var geocoder;
		

		// popup lightbox generation
		if(options._action=='lightbox'){

			var cur_window_top = parseInt($(window).scrollTop()) + 50;
			$('.evo_popin').css({'margin-top':cur_window_top});
			
			$('.evo_pop_body').html('');

			var event_list = this.closest('.eventon_events_list');
			var content = this.siblings('.event_description').html();
			var content_front = this.html();
			
			var _content = $(content).not('.evcal_close');
			

			// RTL
			if(event_list.hasClass('evortl')){
				$('.evo_popin').addClass('evortl');
			}
		
			$('.evo_pop_body').append('<div class="evopop_top">'+content_front+'</div>').append(_content);
			
			var this_map = $('.evo_pop_body').find('.evcal_gmaps');
			var idd = this_map.attr('id');
			this_map.attr({'id':idd+'_evop'});
			
			$('.evo_popup').fadeIn(300);
			$('.evo_popbg').fadeIn(300);

			// check if gmaps should run
			if( this.attr('data-gmtrig')=='1' && this.attr('data-gmap_status')!='null'){
			
				var cal = this.closest('div.ajde_evcal_calendar ');
				loadl_gmaps_in(this, cal, idd+'_evop');
			}

		}

		// functions
			if(options.fnt==1){
				this.each(function(){
					var eventcard = $(this).attr('eventcard');
				
					if(eventcard=='1'){
						$(this).find('a.desc_trig').each(function(elm){
							//$(this).siblings('.event_description').slideDown();
							var obj = $(this);
							
							if(options.delay==0){
								load_googlemaps_here(obj);
							}else{
								setTimeout(load_googlemaps_here, options.delay, obj);
							}
						});
					}
				});
			}
			
			if(options.fnt==2){
				if(options.delay==0){
					load_googlemaps_here(this);
				}else{
					setTimeout(load_googlemaps_here, options.delay, this);
				}
					
			}
			if(options.fnt==3){
				loadl_gmaps_in(this, options.cal, '');			
			}
			
			// gmaps on popup
			if(options.fnt==4){
				// check if gmaps should run
				if( this.attr('data-gmtrig')=='1' && this.attr('data-gmap_status')!='null'){
				
					var cal = this.closest('div.ajde_evcal_calendar ');
					loadl_gmaps_in(this, cal, options.mapSpotId);
				}			
				
			}

	
		
		// function to load google maps for eventcard
		function load_googlemaps_here(obj){
			if( obj.data('gmstat')!= '1'){				
				obj.attr({'data-gmstat':'1'});
			}
			
			var cal = obj.closest('div.ajde_evcal_calendar ');
			
			if( obj.attr('data-gmtrig')=='1' && obj.attr('data-gmap_status')!='null'){
				loadl_gmaps_in(obj, cal, '');
			}			
				
		}
		
		
		// Load the google map on the object
		function loadl_gmaps_in(obj, cal, mapId){

			var evodata = cal.find('.evo-data');

			var mapformat = evodata.data('mapformat');				
			var ev_location = obj.find('.evcal_desc');
			
			var latlon = ev_location.attr('latlon');
			if(latlon=='1'){
				var address = ev_location.attr('latlng');
				var location_type = 'latlng';
				
			}else{
				var address = ev_location.attr('add_str');
				var location_type = 'add';
			}
			
			
			var map_canvas_id= (mapId!=='')?
				mapId:
				obj.siblings('.event_description').find('.evcal_gmaps').attr('id');
				
				
			var zoom = evodata.data('mapzoom');
			var zoomlevel = (typeof zoom !== 'undefined' && zoom !== false)? parseInt(zoom):12;
			
			var scroll = evodata.data('mapscroll');	
			
								
			//obj.siblings('.event_description').find('.evcal_gmaps').html(address);
			initialize(map_canvas_id, address, mapformat, zoomlevel, location_type, scroll);

			//console.log(map_canvas_id+' '+mapformat+' '+ location_type +' '+scroll +' '+ address);
		}
		
		
		
		//console.log(options);
		
	};
}(jQuery));