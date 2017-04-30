<?php
/**
 * inline dynamic styles for front end
 *
 * @version		0.1
 * @package		eventon/Styles
 * @author 		AJDE
 */

header('Content-type: text/css');
//header('Cache-control: must-revalidate');

	// Load variables
	$evcal_val1= get_option('evcal_options_evcal_1');
	

	// magnifying glass cursor
	echo (!empty($evcal_val1['evo_ftim_mag']) && $evcal_val1['evo_ftim_mag']=='yes')? ".evcal_evdata_img{cursor: url(".AJDE_EVCAL_URL."/assets/images/zoom.png), auto;}":null;
	

	// background
	echo ".eventon_events_list .eventon_list_event .desc_trig{background-color:#".( (!empty($evcal_val1['evcal__bgc4']))? $evcal_val1['evcal__bgc4']:'fafafa' ).";}

	.eventon_events_list .eventon_list_event .desc_trig:hover{background-color:#".( (!empty($evcal_val1['evcal__bgc4h']))? $evcal_val1['evcal__bgc4h']:'f4f4f4' ).";}";

	// STYLES
	echo "
		
		".((!empty($evcal_val1['evo_ftimgheight']))?
			".evcal_evdata_img{height:".$evcal_val1['evo_ftimgheight']."px}":null )."
		
		.ajde_evcal_calendar .calendar_header p, .eventon_sort_line p, .eventon_filter_line p, .eventon_events_list .eventon_list_event .evcal_cblock, .evcal_cblock, .eventon_events_list .eventon_list_event .evcal_desc span.evcal_desc2, .evcal_desc span.evcal_desc2, .evcal_evdata_row .evcal_evdata_cell h2, .evcal_evdata_row .evcal_evdata_cell h3.evo_h3, .evcal_month_line p, .evo_clik_row .evo_h3{
			font-family:".( (!empty($evcal_val1['evcal_font_fam']))? $evcal_val1['evcal_font_fam']:"oswald, 'arial narrow'" )."; 
		}
		
	
		/* sort options text */
		.ajde_evcal_calendar .evo_sort_btn{
			color:#".( (!empty($evcal_val1['evcal__sot']))? $evcal_val1['evcal__sot']:'ededed' ).";
		}.ajde_evcal_calendar .evo_sort_btn:hover{
			color:#".( (!empty($evcal_val1['evcal__sotH']))? $evcal_val1['evcal__sotH']:'d8d8d8' ).";
		}

		/* icons */
		.evcal_evdata_row .evcal_evdata_icons i, .evcal_evdata_row .evcal_evdata_custometa_icons i{
			color:#".( (!empty($evcal_val1['evcal__ecI']))? $evcal_val1['evcal__ecI']:'6B6B6B' ).";
			font-size:".( (!empty($evcal_val1['evcal__ecIz']))? $evcal_val1['evcal__ecIz']:'18px' ).";
		}
		
		
		#evcal_list .eventon_list_event .event_description .evcal_btn{
			color:#".( (!empty($evcal_val1['evcal_gen_btn_fc']))? $evcal_val1['evcal_gen_btn_fc']:'fff' ).";			
			background-color:#".( (!empty($evcal_val1['evcal_gen_btn_bgc']))? $evcal_val1['evcal_gen_btn_bgc']:'237ebd' ).";			
		}
		#evcal_list .eventon_list_event .event_description .evcal_btn:hover{
			color:#".( (!empty($evcal_val1['evcal_gen_btn_fcx']))? $evcal_val1['evcal_gen_btn_fcx']:'fff' ).";
			background-color:#".( (!empty($evcal_val1['evcal_gen_btn_bgcx']))? $evcal_val1['evcal_gen_btn_bgcx']:'237ebd' ).";
		}
		
		/*-- font color match --*/
		#evcal_list .eventon_list_event .evcal_desc em{
			color:#".( (!empty($evcal_val1['evcal__fc6']))? $evcal_val1['evcal__fc6']:'8c8c8c' ).";
		}";
		
		if(!empty($evcal_val1['evcal__fc6'])){
			echo "#evcal_widget .eventon_events_list .eventon_list_event .evcal_desc .evcal_desc_info em{
				color:#". $evcal_val1['evcal__fc6']."
			}";
		}
		
		echo ".ajde_evcal_calendar #evcal_head.calendar_header #evcal_cur, .ajde_evcal_calendar .evcal_month_line p{
			color:#".( (!empty($evcal_val1['evcal_header1_fc']))? $evcal_val1['evcal_header1_fc']:'C6C6C6' ).";
		}
		#evcal_list .eventon_list_event .evcal_cblock{
			color:#".( (!empty($evcal_val1['evcal__fc2']))? $evcal_val1['evcal__fc2']:'ABABAB' ).";
		}
		#evcal_list .eventon_list_event .evcal_desc span.evcal_event_title{
			color:#".( (!empty($evcal_val1['evcal__fc3']))? $evcal_val1['evcal__fc3']:'6B6B6B' ).";
		}
		.evcal_evdata_row .evcal_evdata_cell h2, .evcal_evdata_row .evcal_evdata_cell h3{
			color:#".( (!empty($evcal_val1['evcal__fc4']))? $evcal_val1['evcal__fc4']:'6B6B6B' ).";
		}
		#evcal_list .eventon_list_event .evcal_eventcard p{
			color:#".( (!empty($evcal_val1['evcal__fc5']))? $evcal_val1['evcal__fc5']:'656565' ).";
		}

		/* event card color*/
		.eventon_events_list .eventon_list_event .evcal_eventcard, .evcal_evdata_row, .evorow .tbrow{
			background-color:#".eventon_styles('EAEAEA','evcal__bc1', $evcal_val1).";
		}
					
		#eventon_loadbar{
			background-color:#".eventon_styles('6B6B6B','evcal_header1_fc', $evcal_val1)."; height:2px; width:0%}
		
		/*-- font sizes --*/
		.evcal_evdata_row .evcal_evdata_cell h3, .evo_clik_row .evo_h3{font-size:".eventon_styles('18px','evcal_fs_001', $evcal_val1).";}";
		


		// featured event styles
		if(!empty($evcal_val1['evo_fte_override']) && $evcal_val1['evo_fte_override']=='yes'){
			echo "#evcal_list .eventon_list_event .evcal_list_a.featured_event{border-left-color:#".eventon_styles('ca594a','evcal__ftec', $evcal_val1)."!important;}";
		}






	// (---) Hook for addons
	if(has_action('eventon_inline_styles')){
		do_action('eventon_inline_styles');
	}
	
	echo get_option('evcal_styles');

	
