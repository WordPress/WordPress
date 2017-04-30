// ======================================================
// AJDE Backender Section


jQuery(document).ready(function($){

	init();

	function init(){
		// focusing on correct settings tabs
		var hash = window.location.hash;
		//console.log(hash);

		if(hash=='' || hash=='undefined'){
		}else{
			var hashId = hash.split('#');

			$('.nfer').hide();
			$(hash).show();

			var obj = $('a[data-c_id='+hashId[1]+']');
			change_tab_position(obj);
		}
	}


	// colpase menu
	$('.evo-collapse-menu').on('click', function(){
		if($(this).hasClass('close')){
			$(this).parent().removeClass('mini');
			$('.evo_diag').removeClass('mini');
			$(this).removeClass('close');
		}else{
			$(this).parent().addClass('mini');
			$('.evo_diag').addClass('mini');
			$(this).addClass('close');
		}
	});

	// switching between tabs
	$('#acus_left').find('a').click(function(){
		
		var nfer_id = $(this).data('c_id');
		$('.nfer').hide();
		$('#'+nfer_id).show();
		
		change_tab_position($(this));

		window.location.hash = nfer_id;

		if(nfer_id=='evcal_002'){
			$('#resetColor').show();
		}else{
			$('#resetColor').hide();
		}
		
		return false;
		
	});

		// position of the arrow
		function change_tab_position(obj){

			// class switch
			$('#acus_left').find('a').removeClass('focused');
			obj.addClass('focused');

			var menu_position = obj.position();
			//console.log(obj);
			$('#acus_arrow').css({'top':(menu_position.top+3)+'px'}).show();
		}

		// RESET colors
		$('#resetColor').on('click',function(){
			$('.colorselector ').each(function(){
				var item = $(this).siblings('input');
				item.attr({'value': item.attr('default') });
			});
			
		});

	// color circle guide popup
		$('#evcal_002 .hastitle').hover(function(){
			var poss = $(this).position();
			var title = $(this).attr('alt');
			//alert(poss.top)
			$('#evo_color_guide').css({'top':(poss.top-33)+'px', 'left':(poss.left+11)}).html(title).show();
			//$('#evo_color_guide').show();

		},function(){
			$('#evo_color_guide').hide();

		});



	// color picker
		/*
		$('.backender_colorpicker').ColorPicker({
			color: '#206177',
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).attr({'value':hex});
				$(el).siblings('.acus_colorp').css({'background-color':'#'+hex});
				$(el).ColorPickerHide();
			}
		});
		*/
		$('.colorselector').ColorPicker({
			onBeforeShow: function(){
				$(this).ColorPickerSetColor( $(this).attr('hex'));
			},	
			onChange:function(hsb, hex, rgb, el){
				//$(el).css({'backgroundColor': '#' + hex});
				
			},	
			onSubmit: function(hsb, hex, rgb, el) {
				var obj_input = $(el).siblings('input.backender_colorpicker');

				if($(el).hasClass('rgb')){
					$(el).siblings('input.rgb').attr({'value':rgb.r+','+rgb.g+','+rgb.b});
					//console.log(rgb);
				}

				obj_input.attr({'value':hex});

				$(el).css('backgroundColor', '#' + hex);
				$(el).attr({'title': '#' + hex});
				$(el).ColorPickerHide();
			}
		});

	var fa_icon_selection = '';

	// font awesome icons
	$('.faicon').on('click','i', function(){
		var poss = $(this).position();
		$('.fa_icons_selection').css({'top':(poss.top-220)+'px', 'left':(poss.left-74)}).fadeIn('fast');

		fa_icon_selection = $(this);
	});

		//selection of new font icon
		$('.fa_icons_selection').on('click','li', function(){

			var icon = $(this).find('i').data('name');
			console.log(icon)

			fa_icon_selection.attr({'class':'fa '+icon});
			fa_icon_selection.siblings('input').val(icon);

			$('.fa_icons_selection').fadeOut('fast');
		});
		// close with click outside popup box when pop is shown
		$(document).mouseup(function (e){
			var container=$('.fa_icons_selection');
			
				if (!container.is(e.target) // if the target of the click isn't the container...
				&& container.has(e.target).length === 0) // ... nor a descendant of the container
				{
					$('.fa_icons_selection').fadeOut('fast');
				}
			
		});
	




	// multicolor title/name display
	$('.row_multicolor').on('mouseover','em',function(){
		var name = $(this).data('name');
		$(this).closest('.row_multicolor').find('.multicolor_alt').html(name);

	});
	$('.row_multicolor').on('mouseout','em',function(){
		$(this).closest('.row_multicolor').find('.multicolor_alt').html(' ');

	});	

	
	//yes no buttons
	$('.evo_backender_uix').on('click','.evo_yn_btn', function(){
		
		if($(this).hasClass('disable')){
		
		}else{
			// yes
			if($(this).hasClass('NO')){
				$(this).removeClass('NO');
				$(this).siblings('input').val('yes');
				
				$('#'+$(this).attr('afterstatement')).fadeIn();
				
			}else{//no
				$(this).addClass('NO');
				$(this).siblings('input').val('no');
				
				$('#'+$(this).attr('afterstatement')).fadeOut();
			}
		}
		
	});
	
	//legend
	$('.legend_icon').hover(function(){
		$(this).siblings('.legend').show();
	},function(){
		$(this).siblings('.legend').hide();
	});
	
	// image
	var formfield;
	var preview;
	var the_variable;
	
  
    $('.custom_upload_image_button').click(function() {  
		formfield = $(this).siblings('.custom_upload_image');
		var parent_id = $(this).attr('parent');
		var parent = $('#'+parent_id);
		preview = parent.find('.custom_preview_image');  
        tb_show('', 'media-upload.php?type=image&from=t31os&TB_iframe=true');
		
		window.original_send_to_editor = window.send_to_editor;
		
        window.send_to_editor = function(html) {			
			if( $(html).find('img').length ){// <img is inside <a>
				the_variable = $(html).find('img');
			}else{	the_variable = $(html);	}
			
            imgurl = $(the_variable).attr('src');  
			
			//alert(imgurl);
            classes = $(the_variable).attr('class');  
            id = classes.replace(/(.*?)wp-image-/, '');  
            formfield.val(id);  
            preview.attr('src', imgurl);
			preview.show();
            tb_remove();
			parent.find('.custom_no_preview_img').hide();
			parent.find('.custom_upload_image_button ').hide();
			parent.find('.custom_clear_image_button').show();
        }  
        return false;  
    });  
  
    $('.custom_clear_image_button').click(function() {           
        $(this).parent().siblings('.custom_upload_image').val('');  
        $(this).parent().siblings('.custom_preview_image').attr('src', '').hide();
		
		$(this).parent().siblings('.custom_no_preview_img').show();
		$(this).parent().siblings('.custom_upload_image_button ').show();
		$(this).hide();
        return false;  
    });
	
	
	// hidden section
	$('.evoSET_hidden_open').click(function(){
		$(this).next('.evoSET_hidden_body').slideToggle();
		if( $(this).hasClass('open')){
			$(this).removeClass('open')
		}else{
			$(this).addClass('open');
		}
	});
	
	// sortable		
	$('#evoEVC_arrange_box').sortable({		
		update: function(e, ul){
			var sortedID = $(this).sortable('toArray',{attribute:'val'});
			$('#evoCard_order').val(sortedID);
		}
	});
	
	// at first run a check on list items against saved list -
	var items='';
	$('#evoEVC_arrange_box').find('p').each(function(){
		if($(this).attr('val')!='' && $(this).attr('val')!='undefined'){
			items += $(this).attr('val')+',';
		}
	});
	$('#evoCard_order').val(items);
	
	
	
// AJDE Backender Section -- END
// ======================================================

});