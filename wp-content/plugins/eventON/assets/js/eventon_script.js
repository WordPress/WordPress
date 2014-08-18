/*
	Javascript code that is associated with the front end of the calendar
	version: 1.11
*/
jQuery(document).ready(function($){
	
	init();


	function init(){
		init_run_gmap_openevc();

		$('.evopop_top').click(function(){
			//_form_set_rsvp_choise($(this));
			alert('f');
		});
	}


	// popup
		var popupcode = "<div class='evo_popup' style='display:none'><div class='evo_popin'><a class='evopopclose'>X</a><div class='evo_pop_body evcal_eventcard'></div></div></div><div class='evo_popbg' style='display:none'></div>";
		$('body').append(popupcode);
		
		// close popup
		$('.evopopclose').click(function(){
			$(this).closest('.evo_popup').fadeOut().siblings('.evo_popbg').fadeOut();
		});
		// close with click outside popup box when pop is shown
			$(document).mouseup(function (e){
				var container=$('.evo_pop_body');
				
					if (!container.is(e.target) // if the target of the click isn't the container...
						&& e.pageX < ($(window).width() - 30)
					&& container.has(e.target).length === 0) // ... nor a descendant of the container
					{
						$('.evo_popup').fadeOut(300);
						$('.evo_popbg').fadeOut(300);
					}
				
			});
			
		// POPUP functions
			function prepair_popup(){
				var cur_window_top = parseInt($(window).scrollTop()) + 50;
				$('.evo_popin').css({'margin-top':cur_window_top});
				
				$('.evo_pop_body').html('');

			}
			
			function show_popup(){
				$('.evo_popup').fadeIn(300);
				$('.evo_popbg').fadeIn(300);
			}
			
			function appendTo_popup(content){
				$('.evo_pop_body').append(content);
			}

	
	// OPENING event card -- user interaction and loading google maps
		//event full description
		$('.eventon_events_list').on('click','.desc_trig', function(){
			
			var cal = $(this).closest('.ajde_evcal_calendar');
			var evodata = cal.find('.evo-data');
			var ux_val__ = evodata.data('ux_val');
			var accord__ = evodata.data('accord');

			var obj = $(this);
			
			var exlk = obj.data('exlk');
			var ux_val = obj.data('ux_val');
			
			// override overall calendar user intereaction OVER individual event UX
			if(ux_val__!='' && ux_val__!== undefined && ux_val__!='00'){
				ux_val = ux_val__;
			}
			
			
			// open as popup
			if(ux_val=='3'){
				
				$('.evo_pop_body').show();
				obj.evoGenmaps({
					'_action':'lightbox',
				});
				
				return false;

			// open in single events page -- require single event addon
			}else if(ux_val=='4'){
				
				if( $(this).attr('href')!='' &&  $(this).attr('href')!== undefined){
					return;
				// if there is no href like single event box	
				}else{
					var url = $(this).siblings('.evo_event_schema').find('a').attr('href');
					window.open(url, '_self');
					return false;
				}

			}else if(ux_val=='2'){
				return;
			}else if(ux_val=='X'){
				return false;
			}else if(ux_val=='none'){
				return false;
			}else{
				
				// redirecting to external link
				if(exlk=='1' ){
					// if there is a href and <a>
					if( $(this).attr('href')!='' &&  $(this).attr('href')!== undefined){
						return;

					// if there is no href like single event box	
					}else{
						var url = $(this).siblings('.evo_event_schema').find('a').attr('href');

						window.location = url;
						return false;
					}
				}else{

					//alert('ff');
					
					var click_item = $(this).siblings('.event_description');
					if(click_item.hasClass('open')){
						click_item.slideUp().removeClass('open');
					}else{
						// accordion
						if(accord__=='1'){
							cal.find('.event_description').slideUp().removeClass('open');
						}
						click_item.slideDown().addClass('open');
						
					}
					
					// This will make sure markers and gmaps run once and not multiples			
					if( obj.attr('data-gmstat')!= '1'){				
						obj.attr({'data-gmstat':'1'});
						//load_googlemaps(obj);
						
						obj.evoGenmaps({'fnt':2});
					}
							
					return false;
				}
			}
		});
		

		// call to run google maps on load
		function init_run_gmap_openevc(delay){
			$('.ajde_evcal_calendar').each(function(){
				if($(this).find('.evo-data').data('evc_open')=='1'){
					$(this).find('.desc_trig').each(function(){
						if(delay!=''){
							$(this).evoGenmaps({'fnt':2, 'delay':delay});
						}else{
							$(this).evoGenmaps({'fnt':2});
						}
					});
				}
			});
		}
	

	// MONTH jumper
		$('.ajde_evcal_calendar').on('click','.evo-jumper-btn', function(){
			$(this).siblings('.evo_j_container').slideToggle();
		});

		// select a new time from jumper
		$('.evo_j_dates').on('click','a',function(){
			var val = $(this).attr('val');
			var type = $(this).parent().parent().attr('val');
			var container = $(this).closest('.evo_j_container');

			if(type=='m'){
				container.attr({'m':val});
			}else{
				container.attr({'y':$(this).html() });
			}

			// update set class
				$(this).parent().find('a').removeClass('set');
				$(this).addClass('set');

			if(container.attr('m')!==undefined && container.attr('y')!==undefined){
				
				var calid = container.closest('.ajde_evcal_calendar').attr('id');
				var evo_data = $('#'+calid).find('.evo-data');
				evo_data.attr({
					'data-cmonth':container.attr('m'),
					'data-cyear':container.attr('y'),
				});


				ajax_post_content(evo_data.attr('data-sort_by'),calid,'none');

				container.delay(2000).slideUp();
			}
		});

		function change_jumper_set_values(cal_id){
			var evodata = $('#'+cal_id).find('.evo-data');
			var ej_container = $('#'+cal_id).find('.evo_j_container');
			
			ej_container.attr({'m':evodata.attr('data-cmonth')});
		}
	

	// close event card
		$('.eventon_events_list').on('click','.evcal_close',function(){
			$(this).parent().parent().slideUp();
		});
		
		
	// change IDs for map section for eventon widgets
		if( $('.ajde_evcal_calendar').hasClass('evcal_widget')){
			cal.find('.evcal_gmaps').each(function(){
				var gmap_id = obj.attr('id');
				var new_gmal_id =gmap_id+'_widget';
				obj.attr({'id':new_gmal_id})
			});
		}			
	
		
	//===============================
	// SORT BAR SECTION
	// ==============================
	
		// display sort section
		$('.evo_sort_btn').click(function(){
			$(this).siblings('.eventon_sorting_section').slideToggle('fast');
		});	
		
		// sorting section	
		$('.evo_srt_sel p.fa').click(function(){		
			$(this).siblings('.evo_srt_options').fadeToggle();
		});
		
			// update calendar based on the sorting selection
			$('.evo_srt_options').on('click','p',function(){		
					
				var evodata = $(this).closest('.eventon_sorting_section').siblings('.evo-data');
				var cmonth = parseInt( evodata.attr('data-cmonth'));
				var cyear = parseInt( evodata.attr('data-cyear'));	
				var sort_by = $(this).attr('val');
				var new_sorting_name = $(this).html();
				var cal_id = evodata.parent().attr('id');	
							
				ajax_post_content(sort_by,cal_id,'none');
				
				// update new values everywhere
				evodata.attr({'data-sort_by':sort_by});		
				$(this).parent().find('p').removeClass('evs_hide');
				$(this).addClass('evs_hide');		
				$(this).parent().siblings('p.fa').html(new_sorting_name);
				$(this).parent().hide();

			});
		
		
		// filtering section
		$('.filtering_set_val').click(function(){
			$(this).siblings('.eventon_filter_dropdown').fadeToggle();
		});	
		
			// selection on filter dropdown list
			$('.eventon_filter_dropdown').on('click','p',function(){
				var new_filter_val = $(this).attr('filter_val');
				var filter = $(this).closest('.eventon_filter');
				var filter_current_set_val = filter.attr('filter_val');
				
				if(filter_current_set_val == new_filter_val){
					$(this).parent().fadeOut();
				}else{
					// set new filtering changes				
					var evodata = $(this).closest('.eventon_sorting_section').siblings('.evo-data');
					var cmonth = parseInt( evodata.attr('data-cmonth'));
					var cyear = parseInt( evodata.attr('data-cyear'));	
					var sort_by = evodata.attr('data-sort_by');
					var cal_id = evodata.parent().attr('id');				
					
					// make changes
					filter.attr({'filter_val':new_filter_val});	
					evodata.attr({'data-filters_on':'true'});
					
					ajax_post_content(sort_by,cal_id,'none');
					
					// reset the new values				
					var new_filter_name = $(this).html();
					$(this).parent().find('p').removeClass('evf_hide');
					$(this).addClass('evf_hide');
					$(this).parent().fadeOut();
					$(this).parent().siblings('.filtering_set_val').html(new_filter_name);
				}
			});
		
			// fadeout dropdown menus
			$(document).mouseup(function (e){
				var container=$('.eventon_filter_dropdown, .evo_srt_options');
				
				if (!container.is(e.target) // if the target of the click isn't the container...
					&& e.pageX < ($(window).width() - 30)
				&& container.has(e.target).length === 0) // ... nor a descendant of the container
				{
					container.fadeOut();
				}
				
			});
		

	// MONTH SWITCHING
		// previous month
		$('.evcal_btn_prev').click(function(){
			var evodata = $(this).parent().siblings('.evo-data');				
			var sort_by=evodata.attr('data-sort_by');		
			cal_id = $(this).closest('.ajde_evcal_calendar').attr('id');
			
			ajax_post_content(sort_by,cal_id,'prev');			
			
		});
		
		// next month
		$('.evcal_btn_next').click(function(){	
			
			var evodata = $(this).parent().siblings('.evo-data');				
			var sort_by=evodata.attr('data-sort_by');		
			cal_id = $(this).closest('.ajde_evcal_calendar').attr('id');
			
			ajax_post_content(sort_by, cal_id,'next');
			
		});
		
	
	/*	PRIMARY hook to get content	*/
		function ajax_post_content(sort_by,cal_id, direction){
			
			// identify the calendar and its elements.
			var ev_cal = $('#'+cal_id); 
			var cal_head = ev_cal.find('.calendar_header');	
			var evodata = ev_cal.find('.evo-data');	

			// check if ajax post content should run for this calendar or not
			//console.log(ev_cal.attr('data-runajax'));
			if(ev_cal.attr('data-runajax')!='0'){	

				var filters_on = ( evodata.attr('data-filters_on')=='true')?'true':'false';
					
				// creat the filtering data array if exist
				if(filters_on =='true'){
					var filter_section = ev_cal.find('.eventon_filter_line');
					var filter_array = [];
					
					filter_section.find('.eventon_filter').each(function(index){
						var filter_val = $(this).attr('filter_val');
						
						if(filter_val !='all'){
							var filter_ar = {};
							filter_ar['filter_type'] = $(this).attr('filter_type');
							filter_ar['filter_name'] = $(this).attr('filter_field');
							filter_ar['filter_val'] = filter_val;
							filter_array.push(filter_ar);
						}
					});			
				}else{
					var filter_array ='';
				}
				
				var el = ev_cal.find('.cal_arguments');
				var shortcode_array ={};
						
		
				$(el[0].attributes).each(function() {
					if(this.nodeName!='class' && this.nodeName!='style' ){
						shortcode_array[this.nodeName] = this.nodeValue;
						
					}
					//console.log(this.nodeName);
				});
				
				//console.log(shortcode_array);
				
				
				
				// category filtering for the calendar
				var cat = ev_cal.find('.evcal_sort').attr('cat');
				var event_count = parseInt(evodata.attr('data-ev_cnt'));
				
				var data_arg = {
					action: 		'the_ajax_hook',
					current_month: 	evodata.attr('data-cmonth'),	
					current_year: 	evodata.attr('data-cyear'),
					focus_start_date_range: evodata.attr('data-range_start'),
					focus_end_date_range: evodata.attr('data-range_end'),
					send_unix: 		evodata.attr('data-send_unix'),
					direction: 		direction,
					sort_by: 		sort_by, 
					event_count: 	event_count,
					filters: 		filter_array,
					shortcode: 		shortcode_array
				};
				
				cal_head.find('.eventon_other_vals').each(function(){
					if($(this).val()!=''){
						data_arg[$(this).attr('name')] = $(this).val();
					}
				});
				
				
				$.ajax({
					beforeSend: function(){
						ev_cal.find('.eventon_events_list').slideUp('fast');
						ev_cal.find('#eventon_loadbar').show().css({width:'0%'}).animate({width:'100%'});
					},
					type: 'POST',
					url:the_ajax_script.ajaxurl,
					data: data_arg,
					dataType:'json',
					success:function(data){
						//alert(data);
						ev_cal.find('.eventon_events_list').html(data.content);
						animate_month_switch(data.cal_month_title, ev_cal.find('#evcal_cur'));
						
						evodata.attr({'data-cmonth':data.month,'data-cyear':data.year});
						change_jumper_set_values(cal_id);
						
					},complete:function(){
						ev_cal.find('#eventon_loadbar').css({width:'100%'}).fadeOut();
						ev_cal.find('.eventon_events_list').delay(300).slideDown('slow');
						ev_cal.evoGenmaps({'delay':400});
						init_run_gmap_openevc(600);
					}
				});
			}
			
		}
	
	// subtle animation when switching months
		function animate_month_switch(new_data, title_element){
			
			var current_text = title_element.html();
			var hei = title_element.height();
			var wid= title_element.width();
			
			title_element.html("<span style='position:absolute; width:"+wid+"; height:"+hei+" ;'>"+current_text+"</span><span style='position:absolute; display:none;'>"+new_data+"</span>").width(wid);
			
			
			title_element.find('span:first-child').fadeOut(800); 
			title_element.find('span:last-child').fadeIn(800, function(){
				title_element.html(new_data).width('');
			});
			
			
		}
	
	
	// show more and less of event details
		$('.eventon_events_list').on('click','.eventon_shad_p',function(){		
			control_more_less( $(this) );		
		});
		
		$('.evo_pop_body').on('click','.eventon_shad_p',function(){		
			control_more_less( $(this));		
		});	
	
	// actual animation/function for more/less button
		function control_more_less(obj){
			
			
			var content = obj.attr('content');
			var current_text = obj.find('.ev_more_text').html();
			var changeTo_text = obj.find('.ev_more_text').attr('txt');
			
			if(content =='less'){			
				
				var hei = obj.parent().siblings('.eventon_full_description').height();
				var orhei = obj.closest('.evcal_evdata_cell').height();
				
				obj.closest('.evcal_evdata_cell').attr({'orhei':orhei}).animate({height: (parseInt(hei)+40) });
				
				obj.attr({'content':'more'});
				obj.find('.ev_more_arrow').addClass('less');
				obj.find('.ev_more_text').attr({'txt':current_text}).html(changeTo_text);
				
			}else{
				var orhei = parseInt(obj.closest('.evcal_evdata_cell').attr('orhei'));
				
				obj.closest('.evcal_evdata_cell').animate({height: orhei });
				
				obj.attr({'content':'less'});
				obj.find('.ev_more_arrow').removeClass('less');
				obj.find('.ev_more_text').attr({'txt':current_text}).html(changeTo_text);
			}
		}
	
	
	// expand and shrink featured image
		$('.ajde_evcal_calendar').on('click','.evcal_evdata_img',function(){
			feature_image_expansion($(this));
		});
		$('.evo_popup').on('click','.evcal_evdata_img',function(){
			feature_image_expansion($(this));
		});
	
		function feature_image_expansion(image){
			img = image;
			
			var img_status = img.attr('status');
			
			if(img_status=='open'){
				img.attr({'status':'close'}).css({'height':''});
			}else{
				
				var cal_width = parseInt(img.parent().width());			
				var img_full_height = parseInt(img.attr('imgheight'));
				var img_full_width = parseInt(img.attr('imgwidth'));
				var img_current_height = parseInt(img.height());
				
				var relative_height = parseInt(img_full_height * (cal_width/img_full_width)) ;
				
				
				img.css({'height':relative_height}).attr({'status':'open'});
			}
			//console.log('9');
		}

		$('.evo_fullheight').each(function(){
			feature_image_expansion($(this));
		});
	
})