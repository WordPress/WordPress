<?php
/**
 * EventON singlEvent shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	singlEvent/Functions/shortcode
 * @version     0.10
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class evo_se_shortcode{

	function __construct(){

		add_shortcode('add_single_eventon', array($this,'eventon_SE_get_event'));
		add_filter('eventon_shortcode_popup',array($this,'evoSE_add_shortcode_options'), 10, 1);
	}

	// add new default shortcode arguments
	function evoSE_add_shortcode_defaults($arr){
		
		return array_merge($arr, array(
			'id'=>0,
			'show_excerpt'=>'no',
			'show_exp_evc'=>'no',
			'open_as_popup'=>'no'
		));
		
	}

	/**
	 *	Shortcode processing
	 */	
	function eventon_SE_get_event($atts){
		global $eventon_sin_event, $eventon;	
		
		
		add_filter('eventon_shortcode_defaults', array($this,'evoSE_add_shortcode_defaults'), 10, 1);
		$supported_defaults = $eventon->evo_generator->get_supported_shortcode_atts();		
		$args = shortcode_atts( $supported_defaults, $atts ) ;
		//print_r($args);


		if(!empty($args['id'])){

			$eventon->evo_generator->is_eventcard_hide_forcer= true;
			//$eventon->evo_generator->is_eventcard_hide_forcer= false;
			$opt = $eventon->evo_generator->evopt1;

				// google map variables
				$evcal_gmap_format = ($opt['evcal_gmap_format']!='')?$opt['evcal_gmap_format']:'roadmap';	
				$evcal_gmap_zooml = ($opt['evcal_gmap_zoomlevel']!='')?$opt['evcal_gmap_zoomlevel']:'12';	
					
				$evcal_gmap_scrollw = (!empty($opt['evcal_gmap_scroll']) && $opt['evcal_gmap_scroll']=='yes')?'false':'true';	

			wp_enqueue_style( 'evcal_single_event_one_style');
			
			wp_enqueue_script( 'evcal_single_event_one');
			
			$event = $eventon->evo_generator->get_single_event_data($args['id']);
			
			
			ob_start();
			
			$ev_excerpt = ($args['show_excerpt']=='yes')? "data-excerpt='1'":null;
			$ev_expand = ($args['show_exp_evc']=='yes')? "data-expanded='1'":null;
			$ev_lightbox = ($args['open_as_popup']=='yes')? "data-ux_val='3'":null;
			
			echo "<div class='ajde_evcal_calendar eventon_single_event eventon_event' >";
			echo "<div class='evo-data' ".$ev_excerpt." ".$ev_lightbox." ".$ev_expand." data-mapscroll='".$evcal_gmap_scrollw."' data-mapformat='".$evcal_gmap_format."' data-mapzoom='".$evcal_gmap_zooml."' ></div> ";
			echo "<div id='evcal_list' class='eventon_events_list'>";
			echo $event[0]['content'];
			echo "</div></div>";
				
			
			return ob_get_clean();
		}
	}

	/*
		ADD shortcode buttons to eventON shortcode popup
	*/
	function evoSE_add_shortcode_options($shortcode_array){
		global $evo_shortcode_box;
		
		$new_shortcode_array = array(
			array(
				'id'=>'s_SE',
				'name'=>'Single Event',
				'code'=>'add_single_eventon',
				'variables'=>array(
					array(
						'name'=>'Event ID',
						'type'=>'text',
						'placeholder'=>'eg. 234',
						'guide'=>'ID of the event you want to show in the box',
						'var'=>'id',
						'default'=>'0'
					),array(
						'name'=>'Show Event Excerpt',
						'type'=>'YN',
						'guide'=>'Show event excerpt under the single event box',
						'var'=>'show_excerpt',
						'default'=>'no'
					),array(
						'name'=>'Show expanded eventCard',
						'type'=>'YN',
						'guide'=>'Show single event eventCard expanded on load',
						'var'=>'show_exp_evc',
						'default'=>'no'
					),array(
						'name'=>'Open event as popup (Lightbox)',
						'type'=>'YN',
						'guide'=>'User click on eventTop open as popup lightbox. IMPORTANT: make sure you DONT select show expanded eventCard for popup lightbox to work',
						'var'=>'open_as_popup',
						'default'=>'no'
					),
				)
			)
		);

		return array_merge($shortcode_array, $new_shortcode_array);
	}

}


	





?>