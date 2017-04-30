<?php
/**
 * EventON Event lists shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-EL/Functions/shortcode
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_el_shortcode{

	
	function __construct(){

		add_shortcode('add_eventon_el', array($this,'evoEL_generate_calendar'));
		add_filter('eventon_shortcode_popup',array($this,'evoEL_add_shortcode_options'), 10, 1);

	}


	/**
	 *	Shortcode processing
	 */	
	function evoEL_generate_calendar($atts){
		global $eventon_el, $eventon;

		// add el scripts to footer
		add_action('wp_footer', array($eventon_el, 'print_scripts'));

		
		add_filter('eventon_shortcode_defaults', array($this,'evoEL_add_shortcode_defaults'), 10, 1);
		
		// /print_r($atts);
		// connect to support arguments
		$supported_defaults = $eventon->evo_generator->get_supported_shortcode_atts();
		//print_r($supported_defaults);
		
		$args = shortcode_atts( $supported_defaults, $atts ) ;
		
		
		ob_start();
			
			echo $eventon_el->generate_eventon_el_calendar($args);
		
		return ob_get_clean();
				
	}

	// add new default shortcode arguments
	function evoEL_add_shortcode_defaults($arr){
		
		return array_merge($arr, array(
			//'mobreaks'=>'no',
			'el_type'=>'ue',
			'el_title'=>'',
			'etop_month'=>'yes'
		));
		
	}

	/*
		ADD shortcode buttons to eventON shortcode popup
	*/
	function evoEL_add_shortcode_options($shortcode_array){
		global $evo_shortcode_box;
		
		$new_shortcode_array = array(
			array(
				'id'=>'s_el',
				'name'=>'Event Lists: Extended',
				'code'=>'add_eventon_el',
				'variables'=>array(
					$evo_shortcode_box->shortcode_default_field('cal_id'),
					array(
						'name'=>'Custom Calendar title',
						'type'=>'text',
						'guide'=>'You can add custom calendar title for event list calendar in here',
						'var'=>'el_title',	
					),array(
						'name'=>'Select event list type',
						'type'=>'select',
						'guide'=>'Type of event list you want to show.',
						'var'=>'el_type',
						'options'=>array(
							'ue'=>'Upcoming Events',
							'pe'=>'Past Events'
						)
					)
						
					,array(
						'name'=>'Event Cut-off',
						'type'=>'select_step',
						'guide'=>'Past or upcoming events cut-off time. This will allow you to override past event cut-off settings for calendar events. Current date = today at 12:00am',
						'var'=>'pec',
						'default'=>'Current Time',
						'options'=>array( 
							'ct'=>'Current Time: '.date('m/j/Y g:i a', current_time('timestamp')),
							'cd'=>'Current Date: '.date('m/j/Y', current_time('timestamp')),
							'ft'=>'Fixed Time'
						)
					)
					
						,array(
							'type'=>'open_select_steps','id'=>'ct'
						)
						,array(	'type'=>'close_select_step')
						,array(
							'type'=>'open_select_steps','id'=>'cd'
						)
						,array(	'type'=>'close_select_step')
						,array(
							'type'=>'open_select_steps','id'=>'ft'
						)
							,$evo_shortcode_box->shortcode_default_field('fixed_d_m_y')
							
						,array(	'type'=>'close_select_step')

					,array(
						'name'=>'Number of Months',
						'type'=>'text',
						'var'=>'number_of_months',
						'default'=>'0',
						'guide'=>'If number of month is not provided, by default it will get events from one month either back or forward of current month',
						'placeholder'=>'eg. 5'
					),
					array(
						'name'=>'Event count limit',
						'placeholder'=>'eg. 3',
						'type'=>'text',
						'guide'=>'Limit number of events displayed in the list eg. 3',
						'var'=>'event_count',
						'default'=>'0'
					),
					$evo_shortcode_box->shortcode_default_field('event_order'),
					$evo_shortcode_box->shortcode_default_field('hide_mult_occur')
					,array(
						'name'=>'Show eventTop month name',
						'type'=>'YN',
						'guide'=>'Show 3 letter month name for eventTop under event date on eventTop',
						'var'=>'etop_month',
						'default'=>'yes'	
					)
					
				)
			)
		);

		return array_merge($shortcode_array, $new_shortcode_array);
	}


}




?>