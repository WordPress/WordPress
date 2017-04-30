/*
	Version: 2.2.9
*/

jQuery(document).ready(function($){
	
	
	
	// meta box sections
	// click hide and show
	$('#evo_mb').on('click','.evomb_header',function(){
		
		var box = $(this).siblings('.evomb_body');
		
		if(box.hasClass('closed')){
			box.slideDown('fast').removeClass('closed');
		}else{
			box.slideUp('fast').addClass('closed');
		}
		update_eventEdit_meta_boxes_values();
	});
	
	function update_eventEdit_meta_boxes_values(){
		var box_ids ='';
		
		$('#evo_mb').find('.evomb_body').each(function(){
			
			if($(this).hasClass('closed'))
				box_ids+=$(this).attr('box_id')+',';
			
		});
		
		$('#evo_collapse_meta_boxes').val(box_ids);
	}
	

	// location picker
	$('#evcal_location_field').on('change',function(){
		var option = $('option:selected', this);

		if($(this).val()!=''){
			$('#evcal_location_name').val( $(this).val());
			$('#evcal_location').val( option.data('address')  );


			if(option.data('lat')!='')
				$('#evcal_lat').val( option.data('lat')  );
			if(option.data('lon')!='')
				$('#evcal_lon').val( option.data('lon')  );

		}

	});

	//makeInputSelect("evcal_location_field");
		function makeInputSelect(id) {
		    var $sel = $("#" + id);
		    var $inp = $("#" + id + "_Other");
		    var selW = $sel.width();
		    var selH = $sel.height();
		    var selOff = $sel.offset();
		    $inp.width(selW);
		    //
		    $sel.click(function(event) {
		        if(event.which <= 1) { //left click
		            var offX = event.pageX - selOff.left;
		            var offY = event.pageY - selOff.top;
		            if(offX < $sel.width() - 22 && offY < selH) { // input
		                $sel.hide();
		                $inp.show().focus();
		            }
		        }
		    });
		    $sel.change(function() {
		        $inp.val($sel.val());
		    });
		    $inp.blur(function() {
		        // remove selected attribute
		        $sel.find("option:selected").attr("selected",false);
		        // remove old user input option
		        $sel.find("option[frominput=1]").remove();
		        // add and select a new user input option
		        $sel.append($("<option />").val($inp.val()).text($inp.val()).attr("frominput", 1).attr("selected", true));
		        $inp.hide();
		        $sel.show();        
		    });
		    //
		    $sel.after($inp);
		    $inp.hide();
		}


	
	
	/** COLOR picker **/	
		$('#color_selector').ColorPicker({		
			color: get_default_set_color(),
			onChange:function(hsb, hex, rgb,el){
				set_hex_values(hex,rgb);
			},onSubmit: function(hsb, hex, rgb, el) {
				set_hex_values(hex,rgb);
				$(el).ColorPickerHide();
			}		
		});
		
			function set_hex_values(hex,rgb){
				var el = $('#evColor');
				el.find('.evcal_color_hex').html(hex);
				$('#evcal_event_color').attr({'value':hex});
				el.css({'background-color':'#'+hex});		
				set_rgb_min_value(rgb,'rgb');
			}
			
			function get_default_set_color(){
				var colorraw =$('#evColor').css("background-color");
						
				var def_color =rgb2hex( colorraw);	
					//alert(def_color);
				return def_color;
			}
		
	//event color
		$('.evcal_color_box').click(function(){		
			$(this).addClass('selected');
			var new_hex = $(this).attr('color');
			var new_hex_var = '#'+new_hex;
			
			set_rgb_min_value(new_hex_var,'hex');		
			$('#evcal_event_color').val( new_hex );
			
			$('#evColor').css({'background-color':new_hex_var});
			$('.evcal_color_hex').html(new_hex);
			
		});
	
	/** convert the HEX color code to RGB and get color decimal value**/
		function set_rgb_min_value(color,type){
			
			if( type === 'hex' ) {			
				var rgba = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(color);	
				var rgb = new Array();
				 rgb['r']= parseInt(rgba[1], 16);			
				 rgb['g']= parseInt(rgba[2], 16);			
				 rgb['b']= parseInt(rgba[3], 16);	
			}else{
				var rgb = color;
			}
			
			var val = parseInt((rgb['r'] + rgb['g'] + rgb['b'])/3);
			
			$('#evcal_event_color_n').attr({'value':val});
		}
		
		function rgb2hex(rgb){
			
			if(rgb=='1'){
				return;
			}else{
				rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
			
				return "#" +
				("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
				("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
				("0" + parseInt(rgb[3],10).toString(16)).slice(-2);
			}
		}
		
	
	
	
	
	/** 	User interaction meta field 	 **/
		// new window
		$('#evo_new_window_io').click(function(){
			var curval = $(this).hasClass('selected');
			if(curval){
				$(this).removeClass('selected');
				$('#evcal_exlink_target').val('no');
			}else{
				$(this).addClass('selected');
				$('#evcal_exlink_target').val('yes');
			}
		});
		 
		$('.evcal_db_ui').click(function(){
			var val = $(this).attr('value');
			$('#evcal_exlink_option').val(val);
			
			$('.evcal_db_ui').removeClass('selected');
			$(this).addClass('selected');
			
			var link = $(this).attr('link');		
			var linkval = $(this).attr('linkval');
			var opval = $(this).attr('value');
			
			if(link=='yes'){			
				$('#evcal_exlink').show();
				if(linkval!=''){
					$('#evcal_exlink').val(linkval);
				}
			}
			
			// slide down event card
			if(opval=='1' || opval=='3'){
				$('#evo_new_window_io').removeClass('selected');
				$('#evcal_exlink_target').val('no');
				$('#evcal_exlink').hide().attr({value:''});
				$('#evo_new_window_io').hide();
			}else{
				$('#evo_new_window_io').show();
			}
			
			
		});
		
	
	// repeating events UI
		$('#evd_repeat').click(function(){
			// yes
			if($(this).hasClass('NO')){
				$('.evcalr_2').slideDown();
			}else{
				$('.evcalr_2').slideUp();
			}
		});

		// frequency
		$('#evcal_rep_freq').change(function(){
			var field = $(this).find("option:selected").attr('field');


			// monthly
				if(field =='months'){
					$('.evo_rep_month').show();

					// show or hide day of week
					var field_x = $('#evo_rep_by').find("option:selected").attr('value');
					if(field_x=='dow'){
						$('.evo_rep_month_2').show();
					}else{
						$('.evo_rep_month_2').hide();
					}
				}else{
					$('.evo_rep_month').hide();
					$('.evo_rep_month_2').hide();
				}
				$('#evcal_re').html(field);
		});

		// repeat by selct field
		$('#evo_rep_by').change(function(){
			var field = $(this).find("option:selected").attr('value');
			if(field=='dow'){
				$('.evo_rep_month_2').show();


				//$('.evo_month_rep_value').html().show();
			}else{
				$('.evo_rep_month_2').hide();
			}	
		});
	
	
	// end time hide or not
		$('#evo_endtime').click(function(){
			// yes
			if($(this).hasClass('NO')){
				$('.evo_enddate_selection').slideUp();
				$('#evo_dp_to').attr({'value':''});
			}else{
				$('.evo_enddate_selection').slideDown();
			}
		});
	
	
	//date picker on 
		var date_format = $('#evcal_dates').attr('date_format');
		$('#evo_dp_from').datepicker({ 
			dateFormat: date_format,
			numberOfMonths: 2,
			onClose: function( selectedDate , obj) {
		        $( "#evo_dp_to" ).datepicker( "option", "minDate", selectedDate );
		        

		        var date = $(this).datepicker('getDate');
   				var dayOfWeek = date.getUTCDay();

   				$('.evo_days_list').find('input').removeAttr('checked');
   				$('.evo_days_list').find('input[value="'+dayOfWeek+'"]').attr({'checked':'checked'});

   				//alert($.datepicker.iso8601Week(new Date(selectedDate)))

   				//console.log(selectedDate);
		    }
		});
		$('#evo_dp_to').datepicker({ 
			dateFormat: date_format,
			numberOfMonths: 2,
			onClose: function( selectedDate ) {
	        	$( "#evo_dp_from" ).datepicker( "option", "maxDate", selectedDate );
	      	}
		});
		


	// yes no buttons in event edit page
		$('.evo_yn_btn').on( 'click',function(){
			// yes
			if($(this).hasClass('NO')){
				$(this).removeClass('NO');
				$(this).siblings('input').val('yes');
				
				if($(this).attr('allday_switch')=='1'){
					$('.evcal_time_selector').hide();
				}

				// afterstatment
				var type = ($(this).attr('as_type')=='class')? '.':'#';
				$(type+$(this).attr('afterstatement')).slideDown();

			}else{//no
				$(this).addClass('NO');
				$(this).siblings('input').val('no');
				
				if($(this).attr('allday_switch')=='1'){
					$('.evcal_time_selector').show(); 
				}

				var type = ($(this).attr('as_type')=='class')? '.':'#';
				$(type+$(this).attr('afterstatement')).slideUp();
			}
			
		});
	
	// eventbrite
		$('#evcal_eventb_btn').click(function(){
			$('#evcal_eventb_data').slideToggle();
		});
		$('#evcal_eventb_btn_2').click(function(){
			$('#evcal_eventb_msg').hide();
			var ev= $('#evcal_eventb_ev_id').val();
			if(ev==''){
				$('#evcal_eventb_msg').html('You gotta enter something other than blank space..').show();
			}else{
				
				$.ajax({
					beforeSend: function(){
						$('#evcal_eventb_msg').html('We are connecting to eventbrite..').show();
					},
					type: 'POST',
					url:the_ajax_script.ajaxurl,
					data: {	action:'the_post_ajax_hook_3',	
						event_id:ev
					},
					dataType:'json',
					success:function(data){
						//alert(data);
						if(data.status =='1'){
							$('#evcal_eventb_msg').hide();
							$('#evcal_eventb_data_tb').append(data.code);
							$('#evcal_eventb_s1').delay(400).slideDown();
							$('#evcal_eb1').html(ev);
							$('#evcal_eventb_ev_d2').val(ev);
						}else{
							$('#evcal_eventb_msg').html('Could not retrieve data at this time.').show();
						}
						
					},complete:function(){
						//ev_cal.find('.evcal_events_list').delay(300).fadeIn();
					}
				});
				
			}
		});
		$('#evcal_eventb_data_tb').on( 'click','.evcal_data_row',function(){
			
			var field = $(this).attr('var');
			var p_val = $(this).find('p.value');
			var value = p_val.html();
			var this_makd = $(this).attr('marked');
			
			if(this_makd =='yes'){
			// DESELECT
				// evcal_eb_
				// evcal_ebv_
				$(this).removeClass('evcal_checked_row');
				$(this).attr({'marked':'no'});
				
				if(field =='capacity' || field=='price' || field=='url' ){
					$('.evcal_eb_'+field).slideUp();
					$('#evcal_ebv_'+field).attr({'value':''});
				}else{
					var oldval = $('#'+field).attr('oldval');				
					$('#'+field).val(oldval);				
				}
			}else{
				// SELECT
				$(this).addClass('evcal_checked_row');
				$(this).attr({'marked':'yes'});
				
				if(field =='capacity'|| field=='price' || field=='url'){
					$('.evcal_eb_'+field).slideDown();
					$('#evcal_ebv_'+field).val(value);
				}else{
					var field_cv =$('#'+field).val();
					if(field_cv!=''){
						$('#'+field).attr({'oldval':field_cv});
					}
					$('#'+field).val(value);
				}
				if(field =='capacity' ){
					$('#evcal_eb3').html(value);
				}if(field =='price' ){
					$('#evcal_eb4').html(value);
				}
			}
			$('#evcal_eventb_ev_d1').val('yes');		
		});
		// disconnect event brite
		$('#evcal_eventb_btn_dis').click(function(){
			var val_ar = new Array('evcal_eventb_ev_d2', 'evcal_eventb_ev_d1',
				'evcal_ebv_url','evcal_ebv_capacity','evcal_eventb_tprice');
			
			for(i=0; i<val_ar.length; i++){
				$('#'+val_ar[i]).attr({'value':''});
			}
			
			$('.evcal_eb_r').slideUp();
			$('#evcal_eb5').hide();
		});	
		
	// MEETUP
		$('#evcal_meetup_btn').click(function(){
			$('#evcal_meetup_data').slideToggle();
		});
		
		$('#evcal_meetup_btn_2').click(function(){
			$('#evcal_meetup_msg').hide();
			var ev= $('#evcal_meetup_ev_id').val();
			if(ev==''){
				$('#evcal_meetup_msg').html('You gotta enter something other than blank space..').show();
			}else{
				
				$.ajax({
					beforeSend: function(){
						$('#evcal_meetup_msg').html('We are connecting to Meetup..').show();
					},
					type: 'POST',
					url:the_ajax_script.ajaxurl,
					data: {	action:'the_post_ajax_hook_2',	
						event_id:ev
					},
					dataType:'json',
					success:function(data){
						//alert(data);
						if(data.status =='1'){
							$('#evcal_meetup_msg').hide();
							$('#evcal_meetup_data_tb').append(data.code);
							$('#evcal_meetup_s1').delay(400).slideDown();
							$('#evcal_001').html(ev);
							$('#evcal_meetup_ev_d2').val(ev);
						}else{
							$('#evcal_meetup_msg').html('Could not retrieve data at this time.').show();
						}
						
					},complete:function(){
						//ev_cal.find('.evcal_events_list').delay(300).fadeIn();
					}
				});
				
			}
		});
		
		$('#evcal_meetup_data_tb').on( 'click','.evcal_data_row',function(){
			$(this).addClass('evcal_checked_row');
			
			var field = $(this).attr('var');
			var p_val = $(this).find('p.value');
			var value = p_val.html();
			var this_makd = $(this).attr('marked');
			
			if(this_makd =='yes'){
				// DESELECT
				// evcal_mu_
				// evcal_muv_
				$(this).removeClass('evcal_checked_row');
				$(this).attr({'marked':'no'});
				
				if(field=='url' ){
					//$('.evcal_mu_'+field).slideUp();
					$('#evcal_lmlink').attr({'value':''});
				}else{
					var oldval = $('#'+field).attr('oldval');				
					$('#'+field).val(oldval);				
				}
			}else{
				// SELECT
				$(this).addClass('evcal_checked_row');
				$(this).attr({'marked':'yes'});
				
				if(field=='url'){
					//$('.evcal_mu_'+field).slideDown();
					//$('#evcal_muv_'+field).val(value);
					$('#evcal_lmlink').val(value);
				}else if(field =='time' ){
					$('#evcal_start_date').val( p_val.attr('ftime') );
					$('#evcal_start_time_hour').val( p_val.attr('hr') );
					$('#evcal_start_time_min').val( p_val.attr('min') );
					$('#evcal_st_ampm').val( p_val.attr('ampm') );
				}else{
					var field_cv =$('#'+field).val();
					if(field_cv!=''){
						$('#'+field).attr({'oldval':field_cv});
					}
					$('#'+field).val(value);
				}			
			}
			$('#evcal_meetup_ev_d1').val('yes');		
		});
		
		// disconnect meetup
		$('#evcal_meetup_btn_dis').click(function(){
			// remove values from MU data set and MU id
			var val_ar = new Array('evcal_meetup_ev_d1', 'evcal_meetup_ev_d2');
			
			for(i=0; i<val_ar.length; i++){
				$('#'+val_ar[i]).attr({'value':''});
			}
			
			$('.evcal_meetup_url_field').slideUp();
			$('#evcal_mu2').hide();
		});	
	
});