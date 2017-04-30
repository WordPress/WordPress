/*
	Javascript: Eventon Daily View
	version:	0.2
*/
jQuery(document).ready(function($){

	init();

	var current_date;
	var current_day;
	var current_events;

	function init(){

		set_daily_strip_sizes('');
		$('body').find('div.evoDV').each(function(){
			//$(this).attr({'data-runajax':0});
		});

		update_num_events();
	}
	

	// update number of events for current day
	function update_num_events(){
		$('.evoDV').each(function(){
			var numevents = $(this).find('.eventon_daily_in').find('p.on_focus').data('events');

			if(numevents!=='' && numevents!==false){
				$(this).find('.evodv_current_day .evodv_events span').html(numevents).parent().show();
			}

			// update day and date name
			var onfocus = $(this).find('p.evo_day.on_focus');
			changin_dates(onfocus.data('date'), $(this).attr('id'), onfocus);
		});
	}
	// click on a day
		$('.eventon_daily_list').on( 'click','.evo_day',function(){
			var new_day = $(this).find('.evo_day_num').html();
					
			var cal_id = $(this).closest('.ajde_evcal_calendar').attr('id');
			var day_obj = $(this);
			
			changin_dates(new_day, cal_id, day_obj, true);
		});

	// using arrows to switch dates
		$('.evodv_current_day').on('click', '.evodv_daynum span', function(){
			var dir = $(this).attr('class');
			var cal =$(this).closest('.ajde_evcal_calendar');
			var cal_id = cal.attr('id');

			if(dir =='next'){
				var day_obj = cal.find('p.evo_day.on_focus');
				var new_day = cal.find('p.evo_day.on_focus').next().data('date');
			}else{
				var day_obj = cal.find('p.evo_day.on_focus');
				var new_day = cal.find('p.evo_day.on_focus').prev().data('date');
			}
			
			changin_dates(new_day, cal_id, day_obj, true);

		});
	
	// change the dates on current date section
		function changin_dates(new_day, cal_id, day_obj, ajax){

			var new_day_obj = day_obj.parent().find('.evo_day[data-date='+ new_day+']');
			
			day_obj.parent().find('.evo_day').removeClass('on_focus');
			new_day_obj.addClass('on_focus');

			// update global values
			current_date = new_day;
			current_events = new_day_obj.data('events');
			current_day = new_day_obj.data('dnm');
			update_current_date_section(day_obj.closest('.ajde_evcal_calendar'));
			
			if(ajax)
				ajax_update_month_events(cal_id, new_day);
		}

	

	// update the current date section with new information
	function update_current_date_section(obj){
		obj.find('.evodv_current_day .evodv_events span').html(current_events).parent().show();
		obj.find('.evodv_current_day .evodv_daynum b').html(current_date);
		obj.find('.evodv_current_day .evodv_dayname').html(current_day);
	}
	


	// AJAX:  when changing date
	function ajax_update_month_events(cal_id, new_day){
		var ev_cal = $('#'+cal_id); 
		var cal_head = ev_cal.find('.calendar_header');
		var evodata = ev_cal.find('.evo-data');

		var evcal_sort = cal_head.siblings('div.evcal_sort');
				
		var sort_by=evcal_sort.attr('sort_by');
		var cat=evcal_sort.attr('cat');
		
		var ev_type = evodata.attr('data-ev_type'); 
		var ev_type_2 = evodata.attr('data-ev_type_2');
		
		// change values to new in ATTRs
		evodata.attr({'data-cday':new_day});
		
		var shortcode_array = get_shortcode_array(cal_id);
		var filter_array = get_filters_array(cal_id);
		
		var data_arg = {
			action: 		'the_ajax_hook',
			current_month: 	evodata.attr('data-cmonth'),	
			current_year: 	evodata.attr('data-cyear'),	
			sort_by: 		sort_by, 			
			event_count: 	evodata.attr('data-ev_cnt'),
			dv_focus_day: 	new_day,
			filters: 		filter_array,
			direction: 		'none',
			shortcode: 		shortcode_array
		};
		
		
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
				ev_cal.find('.eventon_other_vals').val(new_day);
			},complete:function(){
				ev_cal.find('#eventon_loadbar').css({width:'100%'}).fadeOut();
				ev_cal.find('.eventon_events_list').delay(300).slideDown();
				ev_cal.evoGenmaps({'delay':400});
			}
		});
		
	}
	
	

	$('.eventon_filter_dropdown').on( 'click','p',function(){
		var cal_head = $(this).closest('.eventon_sorting_section').siblings('.calendar_header');
		eventon_dv_get_new_days(cal_head,'','');
	});

	// MONTH JUMPER
		$('.evo_j_dates').on('click','a',function(){
			var container = $(this).closest('.evo_j_container');
			if(container.attr('m')!==undefined && container.attr('y')!==undefined){
				
				var cal_head = $(this).closest('.calendar_header');
				var evo_dv = cal_head.find('.eventon_other_vals').length;

				if(evo_dv>0)
					eventon_dv_get_new_days(cal_head,'','');
			}
		});

	// MONTH SWITCHING	
		$('.evcal_btn_prev').click(function(){
			var top = $(this).closest('.ajde_evcal_calendar');
			if(top.hasClass('evoDV')){
				var cal_head = $(this).parents('.calendar_header');
				var evo_dv = cal_head.find('.eventon_other_vals').length;		
				if(evo_dv>0){
					eventon_dv_get_new_days(cal_head,'prev','');
				}
			}
			
		});
		
		$('.evcal_btn_next').click(function(){	
			var top = $(this).closest('.ajde_evcal_calendar');
			if(top.hasClass('evoDV')){
				var cal_head = $(this).parents('.calendar_header');
				var evo_dv = cal_head.find('.eventon_other_vals').length;		
				if(evo_dv>0){
					eventon_dv_get_new_days(cal_head,'next','');
				}
			}
		});
	
	// AJAX: update the days list for new month
	function eventon_dv_get_new_days(cal_header, change, cday){
		
		var evodata = cal_header.siblings('.evo-data');

		var cur_m = parseInt(evodata.attr('data-cmonth'));
		var cur_y = parseInt(evodata.attr('data-cyear'));
		var cal_id = cal_header.parent().attr('id');
		
		// new dates
		var new_d = (cday=='')? cal_header.find('.eventon_other_vals').val(): cday;

		// set first to be the date
			//cal_header.find('.eventon_other_vals').attr({'value':1});
		
		if(change=='next'){
			var new_m = (cur_m==12)?1: cur_m+ 1 ;
			var new_y = (cur_m==12)? cur_y+1 : cur_y;
		}else if(change=='prev'){
			var new_m = (cur_m==1)?12:cur_m-1;
			var new_y = (cur_m==1)?cur_y-1:cur_y;
		}else{
			var new_m =cur_m;
			var new_y = cur_y;
		}
		
		var shortcode_array = get_shortcode_array(cal_id);
		var filter_array = get_filters_array(cal_id);
		
		var data_arg = {
			action:'the_ajax_daily_view',
			next_m:new_m,	
			next_y:new_y,
			next_d:new_d,
			filters:filter_array,
			cal_id: 	cal_id,
			send_unix: 	evodata.data('send_unix'),
			shortcode: 	shortcode_array
		};
		


		var this_section = cal_header.parent().find('.eventon_daily_in');
		var this_section_days = cal_header.parent().find('.eventon_daily_list');
		
		$.ajax({
			beforeSend: function(){
				this_section_days.slideUp('fast');		
			},
			type: 'POST',
			url:the_ajax_script.ajaxurl,
			data: data_arg,
			dataType:'json',
			success:function(data){
				//alert(data);
				this_section.html(data.days_list);
				revert_to_beginning(cal_id, data.last_date_of_month, new_d);

				// update current date section 
				update_num_events();

			},complete:function(){
				this_section_days.slideDown('slow');				
				set_daily_strip_sizes();


			}
		});

		//ajax_update_month_events(cal_id, new_d);
	}
	
	/**
	 *	Return filters array if exist for the active calendar
	 */
	function get_filters_array(cal_id){
		var ev_cal = $('#'+cal_id); 
		var evodata = ev_cal.find('.evo-data');
		
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
		
		return filter_array;
	}
	
	/*	RETURN: shortcode array	*/
		function get_shortcode_array(cal_id){
			var ev_cal = $('#'+cal_id); 
			
			var el = ev_cal.find('.cal_arguments');
			var shortcode_array ={};
					

			$(el[0].attributes).each(function() {
				if(this.nodeName!='class' && this.nodeName!='style' ){
					shortcode_array[this.nodeName] = this.nodeValue;
					
				}
			});
			
			return shortcode_array;
		}

	// SUPPORT: turn off runAJAX on calendar
	function turnoff_runajax(cal_id){

	}
	
	// RETURN: shortcode array
	function get_shortcode_array(cal_id){
		var ev_cal = $('#'+cal_id); 
		
		var el = ev_cal.find('.cal_arguments');
		var shortcode_array ={};
				

		$(el[0].attributes).each(function() {
			if(this.nodeName!='class' && this.nodeName!='style' ){
				shortcode_array[this.nodeName] = this.nodeValue;
				
			}
		});
		
		return shortcode_array;
	}
	
	
	// mouse wheel
	$('.eventon_daily_in').mousewheel(function(e, delta) {
		//$(this).scrollLeft -= (delta * 40);
		
		var cur_mleft = parseInt($(this).css('marginLeft'));
		var width = parseInt($(this).css('width') );
		
		if( cur_mleft<=0){
			
			var new_marl = (cur_mleft+ (delta * 140));
			
			if(new_marl>0){ new_marl=0;}
			
			if(delta == -1 && ( (new_marl*(-1))< (width -200)) ){
			
				$(this).stop().animate({'margin-left': new_marl });
			
			}else if(delta == 1){
				$(this).stop().animate({'margin-left': new_marl });
			}
		}
		
		
		
		e.preventDefault();
	});
	
	// remove days back to beginning of month
	function revert_to_beginning(cal_id, new_d, current_day){
		var day_holder = $('#'+cal_id).find('.eventon_daily_in');
		var date_w = parseInt(day_holder.find('.evo_day:gt(20)').outerWidth());

		// fix width 
		date_w = (date_w<=0 )? 30: date_w;
		
		
		var w_fb = ((date_w*current_day) - (date_w*8));
		var adjust_w = (w_fb>0)? (w_fb): 0;

		//var dpw = parseInt( day_holder.parent().width());
		//var dw = parseInt(day_holder.width());
		//var ml = day_holder.css('margin-left');

		//var new_ml = dw-dpw;
		day_holder.animate({'margin-left':'-'+(adjust_w)+'px'});

		//console.log(adjust_w+' '+(date_w*5)+' '+new_d+' '+date_w);
	}
	
	// daily list sliders	
	function set_daily_strip_sizes(cal_id){
		if(cal_id!=''){
			var holder = $('#'+cal_id).find('.eventon_daily_list');
			adjust_days_width(holder);
		}
		$('.eventon_daily_list').each(function(){
			adjust_days_width( $(this));
			
		});
	}
		function adjust_days_width(holder){
			var day_holder = holder.find('.eventon_daily_in');
			var days = day_holder.children('.evo_day');	
			var day_width = parseInt(day_holder.find('.evo_day:gt(20)').outerWidth());

			var d_holder_width = (parseInt(days.length) )* (day_width);
					
			day_holder.css({'width':d_holder_width});

			//console.log(day_width+' '+d_holder_width+' '+days.length);
		}

	
	// push one day back
	$(this).find('.evo_daily_prev').click(function(){
		var day_holder = $(this).siblings('.eventon_dv_outter').find('.eventon_daily_in');
		var cur_marginL = parseInt(day_holder.css('margin-left'));
		
		if(cur_marginL<0){
			var day_blk_width = day_holder.find('.evo_day:gt(20)').outerWidth();
			var new_marginL = cur_marginL + (day_blk_width*2);
			
			new_marginL = (new_marginL>0)?0:new_marginL;
			day_holder.animate({'margin-left':new_marginL});
		}
		
	});
	// push one day forward
	$(this).find('.evo_daily_next').click(function(){
		var day_holder_out = $(this).siblings('.eventon_dv_outter');
		var strip_width_in = parseInt(day_holder_out.width());
		var day_holder = day_holder_out.find('.eventon_daily_in');
		var cur_marginL = parseInt(day_holder.css('margin-left'));
		var strip_width_out = parseInt(day_holder.width());
		var exceed_width = cur_marginL - strip_width_in;
		
		if(exceed_width> ((-1)*strip_width_out)){
		
			var day_blk_width = day_holder.find('.evo_day:gt(20)').outerWidth();		
			var new_marginL = cur_marginL - (day_blk_width*2);
			
			day_holder.animate({'margin-left':new_marginL});
		}
		
		
	});
	
	
});