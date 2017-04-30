(function($){
	$(document).ready(function(){
		var code;
		var js_code;
		var id;
		var index_x;
		var ad_width;
		var ad_height;
		var ad_format;
		var ad_type;
		var color_border;
		var color_bg;
		var color_link;
		var color_text;
		var color_url;
		var client;
		var width;
		var height;
		var format;
		var type;
		var border_;
		var bg;
		var link;
		var text;
		var url;
		var ui_features;
		var features;
		var ad_script;
		var border_str;
		var pal_name = ['Default Google pallete','Open Air','Seaside','Shadow','Blue Mix','Ink','Graphite'];
		var border  = ['#FFFFFF','#FFFFFF','#336699','#000000','#6699CC','#000000','#CCCCCC'];
		var bgcolor = ['#FFFFFF','#FFFFFF','#FFFFFF','#F0F0F0','#003366','#000000','#CCCCCC'];
		var linkcol = ['#0000FF','#0000FF','#0000FF','#0000FF','#FFFFFF','#FFFFFF','#000000'];
		var urlcolor= ['#008000','#008000','#008000','#008000','#AECCEB','#999999','#666666'];
		var textcol = ['#000000','#000000','#000000','#000000','#AECCEB','#CCCCCC','#333333'];
		
		if ( $(window).width() > 321 && $(window).width() < 600 ) {
			$('#adsns_main').css('min-width', '360px');
		} else if ( $(window).width() > 601 ) {
			$('#adsns_main').css('min-width', '540px');
		} else {
			$('#adsns_main').css('min-width', '100%');
		}
		$( window ).resize(function() {
			if ( $(window).width() > 321 && $(window).width() < 600 ) {
				$('#adsns_main').css('min-width', '360px');
			} else if ( $(window).width() > 601 ) {
				$('#adsns_main').css('min-width', '540px');
			} else {
				$('#adsns_main').css('min-width', '100%');
			}
		});

		ad_script = '<script type="text/javascript"	src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>'
		if ($('#adtypesel_val').val() == 'image_only') {
			$('#def').css("visibility", "hidden");
			$('#img_only').css("visibility", "visible");
		}			
		
		ui_features = $('#corner_style :selected').val();
		if ($('#adtype_val').val() == 'link_unit') {
			$('#adtypeselect').attr('disabled', 'disabled');
			$('#def').css("visibility", "hidden");
			$('#img_only').css("visibility", "hidden");
			$('#lnk_unit').css("visibility", "visible");
			ad_format = $('#link_unit :selected').val();		
			index_x = ad_format.indexOf('x');
			ad_width = ad_format.substring(0, index_x);
			ad_height = ad_format.slice(index_x+1);	
		}
		if ($('#def').css('visibility') == 'visible') {
			ad_format = $('#default_val').val();
		}
		
		if ($('#img_only').css('visibility') == 'visible') {
			ad_format = $('#image_only_val').val();
		}

		if ($('#lnk_unit').css('visibility') == 'visible') {
			ad_format = $('#link_unit_val').val();
		}

		index_x = ad_format.indexOf('x');
		ad_width = ad_format.substring(0, index_x);
		ad_height = ad_format.slice(index_x+1);
		format = "google_ad_format = \"" + ad_format + "_as\";\n";
		
		curcolor = $('#pallete :selected').val();
		$.each(pal_name, function(i, val) {
			if (curcolor == val){
				$('#Border').val(border[i]);
				$('#Title').val(linkcol[i]);
				$('#Background').val(bgcolor[i]);
				$('#Text').val(textcol[i]);
				$('#URL').val(urlcolor[i]);
				$('#Border').css("background", $('#Border').val());		
				$('#Title').css("background", $('#Title').val());
				$('#Background').css("background", $('#Background').val());
				$('#Text').css("background", $('#Text').val());
				$('#URL').css("background", $('#URL').val());
			}
		});	
		
		$('#Border').val($('#border_val').val());
		$('#Title').val($('#title_val').val());
		$('#Background').val($('#background_val').val());
		$('#Text').val($('#text_val').val());
		$('#URL').val($('#url_val').val());
		$('#Border').css("background", $('#Border').val());		
		$('#Title').css("background", $('#Title').val());
		$('#Background').css("background", $('#Background').val());
		$('#Text').css("background", $('#Text').val());
		$('#URL').css("background", $('#URL').val());
		
		
		$('#pallete').change(function(){
			curcolor = $('#pallete :selected').val();
			$.each(pal_name, function(i, val) {
				if (curcolor == val){
				$('#Border').val(border[i]);					
				$('#Title').val(linkcol[i]);					
				$('#Background').val(bgcolor[i]);					
				$('#Text').val(textcol[i]);					
				$('#URL').val(urlcolor[i]);				
				$('#Border').css("background", $('#Border').val());		
				$('#Title').css("background", $('#Title').val());
				$('#Background').css("background", $('#Background').val());
				$('#Text').css("background", $('#Text').val());
				$('#URL').css("background", $('#URL').val());					
				}
			});	         
		});			
		
		$('#adtypeselect').change(function(){
			type_ = $('#adtypeselect :selected').val();
			if ( type_ == 'default' || type_ == 'default_image'){
				$('#def').css("visibility", "visible");
				$('#img_only').css("visibility", "hidden");
				ad_type = $('#adtypeselect :selected').val();
				ad_format = $('#default :selected').val();		
				index_x = ad_format.indexOf('x');
				ad_width = ad_format.substring(0, index_x);
				ad_height = ad_format.slice(index_x+1);	
			}
			if (type_ == 'image_only'){
				$('#def').css("visibility", "hidden");
				$('#img_only').css("visibility", "visible");
				ad_type = $('#adtypeselect :selected').val();		
				ad_format = $('#image_only :selected').val();		
				index_x = ad_format.indexOf('x');
				ad_width = ad_format.substring(0, index_x);
				ad_height = ad_format.slice(index_x+1);					
			}
		});
			
		$('#position').change(function(){
			if ($('#position :selected').val() == 'homepostend' || $('#position :selected').val() == 'homeandpostend' ) {
				$('#homeAds').removeAttr("disabled");
				//$("#homeAds option[value='" + $('#homeads_val').val() + "']").attr('selected', 'selected');
			}
			else {
				$('#homeAds').attr('disabled', 'disabled');
				//$("#homeAds option[value='1']").attr('selected', 'selected');
			}
		});
		if ($('#position :selected').val() == 'homepostend' || $('#position :selected').val() == 'homeandpostend' ) {
			$('#homeAds').removeAttr("disabled");
			//$("#homeAds option[value='" + $('#homeads_val').val() + "']").attr('selected', 'selected');
		}
		else {
			$('#homeAds').attr('disabled', 'disabled');
			//$("#homeAds option[value='1']").attr('selected', 'selected');
		}

		$("#ad_type1").click(function() {			
			$('#adtypeselect').removeAttr("disabled");
			$('#def').css("visibility", "visible");
			$('#lnk_unit').css("visibility", "hidden");
			ad_format = $('#default :selected').val();		
			index_x = ad_format.indexOf('x');
			ad_width = ad_format.substring(0, index_x);
			ad_height = ad_format.slice(index_x+1);
		});

		$("#ad_type2").click(function() {	
			$('#adtypeselect').attr('disabled', 'disabled');
			$('#def').css("visibility", "hidden");
			$('#img_only').css("visibility", "hidden");
			$('#lnk_unit').css("visibility", "visible");								
			ad_format = $('#link_unit :selected').val();		
			index_x = ad_format.indexOf('x');
			ad_width = ad_format.substring(0, index_x);
			ad_height = ad_format.slice(index_x+1);			
		});		
		
		$('#client_id').val($('#client_id_val').val());
		
		$('#default').change(function(){
			ad_format = $('#default :selected').val();		
			index_x = ad_format.indexOf('x');
			ad_width = ad_format.substring(0, index_x);
			ad_height = ad_format.slice(index_x+1);
		});	  
		
		$('#image_only').change(function(){
			ad_format = $('#image_only :selected').val();		
			index_x = ad_format.indexOf('x');
			ad_width = ad_format.substring(0, index_x);
			ad_height = ad_format.slice(index_x+1);
			format = "google_ad_format = \"" + ad_format + "_as\";\n";
		});
		
		$('#link_unit').change(function(){
			ad_format = $('#link_unit :selected').val();		
			index_x = ad_format.indexOf('x');
			ad_width = ad_format.substring(0, index_x);
			ad_height = ad_format.slice(index_x+1);
			format = "google_ad_format = \"" + ad_format + "_0ads_al\";\n";
		});
		
		$('#corner_style').change(function(){
			if ($('#corner_style :selected').val() != 'none') {
				ui_features = $('#corner_style :selected').val();
				features = "google_ui_features = \"rc:" + ui_features + "\";\n";
			}
		});

		$(".positive-integer").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
		
		$('.settings_body_3').hide();
		$('.settings_body_4').hide();
		$('tr[class^="settings_head_"]').on('click', function(){
			var tr_class= $(this).attr("class");
			tr_class = tr_class.replace(/[[A-Za-z_]/gi, "");
			if ( $(this).hasClass('arrow_up') ) {
				$(this).removeClass('arrow_up');
				$('#adsns_main').find(".settings_body_"+tr_class).show();
			} else {
				$(this).addClass('arrow_up');
				$('.settings_body_'+tr_class).hide();
			}
		return false;
		});
		arrows();
		
		
		$('#donate').val($('#donate_val').val());

		$("#Border").focus(function() {
			$('#colorpicker1').farbtastic('#Border');
			$("#colorpicker1").show();
			$("#colorpicker2").hide();
			$("#colorpicker3").hide();
			$("#colorpicker4").hide();
			$("#colorpicker5").hide();
		}).focusout(function () {
			$("#colorpicker1").hide();
		});
		$("#Title").focus(function() {
			$('#colorpicker2').farbtastic( '#Title' );
			$("#colorpicker1").hide();
			$("#colorpicker2").show();
			$("#colorpicker3").hide();
			$("#colorpicker4").hide();
			$("#colorpicker5").hide();
		}).focusout(function () {
			$("#colorpicker2").hide();
		});
		$("#Background").focus(function() {
			$('#colorpicker3').farbtastic( '#Background' );
			$("#colorpicker1").hide();
			$("#colorpicker2").hide();
			$("#colorpicker3").show();
			$("#colorpicker4").hide();
			$("#colorpicker5").hide();
		}).focusout(function () {
			$("#colorpicker3").hide();
		});
		$("#Text").focus(function() {
			$('#colorpicker4').farbtastic( '#Text' );
			$("#colorpicker1").hide();
			$("#colorpicker2").hide();
			$("#colorpicker3").hide();
			$("#colorpicker4").show();
			$("#colorpicker5").hide();
		}).focusout(function () {
			$("#colorpicker4").hide();
		});
		$("#URL").focus(function() {
			$('#colorpicker5').farbtastic( '#URL' );
			$("#colorpicker1").hide();
			$("#colorpicker2").hide();
			$("#colorpicker3").hide();
			$("#colorpicker4").hide();
			$("#colorpicker5").show();
		}).focusout(function () {
			$("#colorpicker5").hide();
		});

		$("#update").click(function () {
			id = $('#client_id').val();
			color_border = $('#Border').val();
			color_link = $('#Title').val();
			color_bg = $('#Background').val();
			color_text = $('#Text').val();
			color_url = $('#URL').val();
			client = '\ngoogle_ad_client = "pub-' + id + '";\n';
			width = 'google_ad_width = ' + ad_width + ';\n';
			height = 'google_ad_height = ' + ad_height + ';\n';
			type = '';
			if ($("[name=adtype]:radio").filter(":checked").val() == 'ad_unit') {
				ad_type = $('#adtypeselect :selected').val();
				type = 'google_ad_type = "' + ad_type + '";\n';
				format = 'google_ad_format = "' + ad_format + '_as";\n';
			}
			
			if ($("[name=adtype]:radio").filter(":checked").val() == 'link_unit') {
				ad_type = $('#adtypeselect :selected').val();
				type = "";
				format = 'google_ad_format = "' + ad_format + '_0ads_al";\n';
			}

			features = "google_ui_features = \"rc:" + ui_features + "\";\n";
			if ($('#corner_style :selected').val() == 'none') {
				features = "";
			}

			border_ = 'google_color_border = "' + color_border + '";\n';
			bg = 'google_color_bg = "' + color_bg + '";\n';
			link = 'google_color_link = "' + color_link + '";\n';
			text = 'google_color_text = "' + color_text + '";\n';
			url = 'google_color_url = "' + color_url + '";\n';	
						
			code = '<script type="text/javascript">' + client + width + height + format + type + border_ + bg + link + text + url + features +'</script>\n' + ad_script;
			js_code = client + width + height + format + type + border_ + bg + link + text + url + features;
			$('#mycode').val(code);
		});

	});
})(jQuery);

function arrows() {
	(function($){
		$('tr[class^="settings_body_"]').each( function() {
			var tr_class= $(this).attr("class");
			tr_class = tr_class.split("_")[2];
			if ( $(this).css('display') == 'none' ) {
			$('.settings_head_'+tr_class).addClass('arrow_up');
			}
			else if ( $(this).css('display') == 'block' ) {
				$('.settings_head_'+tr_class).removeClass('arrow_up');
			}
		});
	})(jQuery);
}
(function($) {
	$(document).ready( function() {
		$( '#adsns_settings_form input' ).bind( "change click select", function() {
			if ( $( this ).attr( 'type' ) != 'submit' ) {
				$( '.updated.fade' ).css( 'display', 'none' );
				$( '#adsns_settings_notice' ).css( 'display', 'block' );
			};
		});
		$( '#adsns_settings_form select' ).bind( "change", function() {
				$( '.updated.fade' ).css( 'display', 'none' );
				$( '#adsns_settings_notice' ).css( 'display', 'block' );
		});
	});
})(jQuery);
