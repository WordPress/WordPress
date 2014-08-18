/*
	Script that runs on all over the backend pages
	ver: 1.3
*/
jQuery(document).ready(function($){
	
	// ----------
	// EventON Sitewide POPUP
	// ----------
	// hide		
	$('.eventon_popup').on('click','.eventon_close_pop_btn', function(){
		var obj = $(this);
		hide_popupwindowbox();
	});
	
	$('.eventon_popup_text').on('click',' .evo_close_pop_trig',function(){
		var obj = $(this).parent();
		hide_popupwindowbox();
	});
	
	$(document).mouseup(function (e){
		var container=$('.eventon_popup');
		
		if(container.hasClass('active')){
			if (!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
			{
				container.animate({'margin-top':'70px','opacity':0}).fadeOut().removeClass('active');
				$('#evo_popup_bg').fadeOut();
				popup_open = false;
			}
		}
	});
	
	// function to hide popup that can be assign to click actions
		function hide_popupwindowbox(){
			
			var container=$('.eventon_popup');
			var clear_content = container.attr('clear');
			
			if(container.hasClass('active')){
				container.animate({'margin-top':'70px','opacity':0},300).fadeOut().
					removeClass('active')
					.delay(300)
					.queue(function(n){
						if(clear_content=='true')					
							$(this).find('.eventon_popup_text').html('');
							
						n();
					});
				$('#evo_popup_bg').fadeOut();
				popup_open = false;
					
			}
		}
	

	
	
	
	/*
		DISPLAY Eventon in-window popup box
		Usage: <a class='button eventon_popup_trig' content_id='is_for_content' dynamic_c='yes'>Click</a>
	*/
		var popup_open = false;
		$('#poststuff').on('click','.eventon_popup_trig', function(){
			if (popup_open) {
				return;
			}else{
				eventon_popup_open( $(this));
				popup_open = true;
			}
		});

		// Open popup trigger
			$('.eventon_popup_trig').on('click', function(){
				if (popup_open) {
					return;
				}else{
					eventon_popup_open( $(this));
					popup_open = true;
				}
			});
			// on addons page
				$('#evo_addons_list').on('click','.eventon_popup_trig', function(){
				if (popup_open) {
					return;
				}else{
					eventon_popup_open( $(this));
					popup_open = true;
				}
			});
			

	
	// OPEN POPUP BOX
		function eventon_popup_open(obj){

			// dynamic content within the site
			var dynamic_c = obj.attr('dynamic_c');
			if(typeof dynamic_c !== 'undefined' && dynamic_c !== false){
				
				var content_id = obj.attr('content_id');
				var content = $('#'+content_id).html();
				
				$('.eventon_popup').find('.eventon_popup_text').html( content);
			}
			
			// if content coming from a AJAX file
			var attr_ajax_url = obj.attr('ajax_url');
			
			if(typeof attr_ajax_url !== 'undefined' && attr_ajax_url !== false){
				
				$.ajax({
					beforeSend: function(){
						show_pop_loading();
					},
					url:attr_ajax_url,
					success:function(data){
						$('.eventon_popup').find('.eventon_popup_text').html( data);			
						
					},complete:function(){
						hide_pop_loading();
					}
				});
			}
			
			// change title if present		
			var poptitle = obj.attr('poptitle');
			if(typeof poptitle !== 'undefined' && poptitle !== false){
				$('#evoPOP_title').html(poptitle);
			}
			
			var popc = obj.data('popc');
			
			$('.eventon_popup').find('.message').removeClass('bad good').hide();
			
			// if specific popup addressed
			if(typeof popc !== 'undefined' && popc !== false){
				$('.eventon_popup.'+popc).addClass('active').show().animate({'margin-top':'0px','opacity':1}).fadeIn();

			}else{
				
				$('.eventon_popup.regular').addClass('active').show().animate({'margin-top':'0px','opacity':1}).fadeIn();
				$('.eventon_popup.eventon_shortcode:first-child').addClass('active').show().animate({'margin-top':'0px','opacity':1}).fadeIn();
			}
			
			$('html, body').animate({scrollTop:0}, 700);
			$('#evo_popup_bg').show();
		}
		
	
	

	
	// popup lightbox functions
		function show_pop_bad_msg(msg){
			$('.eventon_popup').find('.message').removeClass('bad good').addClass('bad').html(msg).fadeIn();
		}
		function show_pop_good_msg(msg){
			$('.eventon_popup').find('.message').removeClass('bad good').addClass('good').html(msg).fadeIn();
		}
		
		function show_pop_loading(){
			$('.eventon_popup_text').css({'opacity':0.3});
			$('#eventon_loading').fadeIn();
		}
		function hide_pop_loading(){
			$('.eventon_popup_text').css({'opacity':1});
			$('#eventon_loading').fadeOut(20);
		}
		
	
	
	
	
	// widget
		$('.widgets-sortables').on('click','.evowig_chbx', function(){
			
			if($(this).hasClass('selected')){
				$(this).removeClass('selected');
				
				$(this).siblings('input').val('no');
				$(this).parent().siblings('.evo_wug_hid').slideUp('fast');
			}else{
				$(this).addClass('selected');
				
				$(this).siblings('input').val('yes');
				$(this).parent().siblings('.evo_wug_hid').slideDown('fast');
			}
			
			
		});
		

	
	
// ==========================
// shortcode popup box
// evoPOSH


	evoPOSH_go_back();

	var shortcode;
	var shortcode_vars = [];
	var shortcode_keys = new Array();
	var ss_shortcode_vars = new Array();

	// click on each main step
		$('#evoPOSH_outter').on('click','.evoPOSH_btn',function(){
			var obj = $(this);
			var section = obj.attr('step2');
			var code = obj.attr('code');
			var section_name = obj.html();
			

			// no 2nd step
			if(obj.hasClass('nostep') ){
				$('#evoPOSH_code').html('['+code+']').attr({'data-curcode':code});
			}else{
				var pare = obj.parent().parent();
				pare.find('.step2').show();
				pare.find('#'+section).show();
				$('.evoPOSH_inner').animate({'margin-left':'-470px'});
				
				evoPOSH_show_back_btn();
				
				$('#evoPOSH_code').html('['+code+']').attr({'data-curcode':code});
				$('#evoPOSH_subtitle').html(section_name).attr({'data-section':section_name});
			}
		});
	// show back button
		function evoPOSH_show_back_btn(){
			$('#evoPOSH_back').animate({'left':'0px'});		
			$('h3.notifications').addClass('back');

		}
	// go back button on the shortcode popup
		function evoPOSH_go_back(){
			$('#evoPOSH_back').click(function(){		
				$(this).animate({'left':'-20px'},'fast');	
				
				$('h3.notifications').removeClass('back');
			
				$('.evoPOSH_inner').animate({'margin-left':'0px'}).find('.step2_in').fadeOut();
				
				// hide step 2
				$(this).closest('#evoPOSH_outter').find('.step2').fadeOut();

				// clear varianles
				shortcode_vars=[];
				shortcode_vars.length=0;

				var code_to_show = $('#evoPOSH_code').data('defsc');
				$('#evoPOSH_code')
					.html('['+code_to_show+']')
					.attr({'data-curcode':code_to_show});

				// change subtitle
				$('#evoPOSH_subtitle').html( $('#evoPOSH_subtitle').data('bf') );
			});
		}	
	
	// yes no buttons
	$('body').on('click','.evo_YN_btn', function(){

		var obj = $(this);
		var codevar = $(this).attr('codevar');
		var value;
		
		if(obj.hasClass('NO')){
			obj.removeClass('NO');	
			value = 'yes';
		}else{
			obj.addClass('NO');	value = 'no';
		}
		
		evoPOSH_update_codevars(codevar,value);
		report_select_steps_( obj, codevar );

		evoPOSH_update_shortcode();
	});


	
	// input and select fields
	$('.evoPOSH_inner').on('change','.evoPOSH_input, .evoPOSH_select', function(){
		
		var obj = $(this);
		var value = obj.val();
		var codevar = obj.attr('codevar');

		
		if(value!='' && value!='undefined'){			
			evoPOSH_update_codevars(codevar,value);
			evoPOSH_update_shortcode();
		}else if(!value){
			evoPOSH_remove_codevars(codevar);			
		}		
	});
	
	// afterstatements within shortcode gen
		$('.eventon_popup').on('click', '.trig_afterst',function(){
			$(this).next('.evo_afterst').toggle();
		});
	
	
	// SELECT STEP within 2ns step field
		$('.evoPOSH_inner').on('change','.evoPOSH_select_step', function(){
			var value = $(this).val();
			var codevar = $(this).data('codevar');
			var this_id = '#'+value;

			$(this_id).siblings('.evo_open_ss').hide();
			$(this_id).delay(300).show();

			// first time selecting
			if(!$(this).hasClass('touched') ){
				$(this).attr({'data-cur_sc': $('#evoPOSH_code').html() })
					.addClass('touched');
			}else{
				var send_code = $(this).data('cur_sc'); // send the code before selecting select step
				remove_select_step_vals();
				$(this).removeClass('touched');
			}


			// update the current shortcode based on selection
			if(value!='' && value!='undefined'){			
				evoPOSH_update_codevars(codevar,value);
			}else if(!value){
				evoPOSH_remove_codevars(codevar);			
			}

			if(value=='ss_1'){
				evoPOSH_remove_codevars(codevar);
			}


		});

		// RECORD step codevar for each select steps
		function report_select_steps_(obj, codevar){
			// ONLY SELECT STEP
			if( obj.closest('.fieldline').hasClass('ss_in')){
				if(ss_shortcode_vars.indexOf(codevar)==-1){
					ss_shortcode_vars.push(codevar);
				}
			}		
		}
		function remove_select_step_vals(){

			if(ss_shortcode_vars.length>0){
				for (var i=0;i<ss_shortcode_vars.length;i++){
					var this_code = ss_shortcode_vars[i];
					evoPOSH_remove_codevars(this_code);
					//delete ss_shortcode_vars[i];
				}
			}
			ss_shortcode_vars=[];
			
		}


	
	// update shortcode based on new selections
	function evoPOSH_update_shortcode(){
		
		var el = $('#evoPOSH_code');
		var string = el.data('curcode')+' ';
		
		if(shortcode_vars.length==0){
			string=string;
		}else{
			$.each( shortcode_vars, function( key, value ) {
				string += value.code+'="'+value.val+'" ';
			});			
		}
		
		// update the shortcode attr on insert button
		var stringx = '['+string+']';
		el.html(stringx).attr({'data-curcode': string});

	}
	
	// UPDATE or ADD new shortcode variable to obj
	function evoPOSH_update_codevars(codevar,value){		
		
		if(shortcode_keys.indexOf(codevar)>-1 
			&& shortcode_vars.length>0){
			$.each( shortcode_vars, function( key, arr ) {
				if(arr && arr.code==codevar){
					shortcode_vars[key].val=value;
				}
			});
		}else{
			var obj = {'code': codevar,'val':value};
			//shortcode_vars[codevar] = obj;
			shortcode_vars.push(obj);
			shortcode_keys.push(codevar);
		}
		evoPOSH_update_shortcode();
	}

	// REMOVE a shortcode variable to object
	function evoPOSH_remove_codevars(codevar){
		
		// remove from main object
		$.each( shortcode_vars, function( key, arr ) {
			if(arr.code==codevar){
				shortcode_vars.splice(key, 1);
			}
		});

		//remove from keys
		var index = shortcode_keys.indexOf(codevar);
		if(index>-1){
			shortcode_keys.splice(index, 1);
		}
		evoPOSH_update_shortcode();
	}
	
	// insert code into text editor
		$('.evoPOSH_footer').on('click','.evoPOSH_insert',function(){
			var shortcode = $('#evoPOSH_code').html();		
			
			tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode);
			
			hide_popupwindowbox();
		});
	
	
});