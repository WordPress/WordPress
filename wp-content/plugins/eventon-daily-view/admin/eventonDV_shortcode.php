<?php
/**
 * EventON dailyView shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	dailyView/Functions/shortcode
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *	Shortcode processing
 */	
function evoDV_generate_calendar($atts){
	global $eventon_dv, $eventon;
	
	$eventon_dv->is_running_dv=true;
	
	add_filter('eventon_shortcode_defaults', 'evoDV_add_shortcode_defaults', 10, 1);
	
	
	ob_start();
		
		echo $eventon_dv->generate_calendar($atts);
	
	return ob_get_clean();
			
}
add_shortcode('add_eventon_dv','evoDV_generate_calendar');

// add new default shortcode arguments
function evoDV_add_shortcode_defaults($arr){
	
	return array_merge($arr, array(
		'fixed_day'=>0,
		'day_incre'=>0,
		'hide_sort_options'=>'no'
	));
	
}

/*
	ADD shortcode buttons to eventON shortcode popup
*/
function evoDV_add_shortcode_options($shortcode_array){
	
	global $evo_shortcode_box;
	
	$new_shortcode_array = array(
		array(
			'id'=>'s_DV',
			'name'=>'DailyView',
			'code'=>'add_eventon_dv',
			'variables'=>array(
				$evo_shortcode_box->shortcode_default_field('show_et_ft_img'),
				$evo_shortcode_box->shortcode_default_field('ft_event_priority'),
				array(
					'name'=>'Day Increment',
					'type'=>'text',
					'placeholder'=>'eg. +1',
					'instruct'=>'Change starting date (eg. +1)',
					'var'=>'day_incre',
					'default'=>'0'
				),$evo_shortcode_box->shortcode_default_field('month_incre'),
				$evo_shortcode_box->shortcode_default_field('event_type'),
				$evo_shortcode_box->shortcode_default_field('event_type_2'),
				array(
					'name'=>'Fixed Day',
					'type'=>'text',
					'instruct'=>'Set fixed day as calendar focused day (integer)',
					'var'=>'fixed_day',
					'instruct'=>'Both fixed month and year should be set for this to work',
					'default'=>'0',
					'placeholder'=>'eg. 10'
				),$evo_shortcode_box->shortcode_default_field('fixed_month'),
				$evo_shortcode_box->shortcode_default_field('fixed_year'),
				$evo_shortcode_box->shortcode_default_field('event_order'),
				$evo_shortcode_box->shortcode_default_field('jumper'),
			)
		)
	);

	return array_merge($shortcode_array, $new_shortcode_array);
}
add_filter('eventon_shortcode_popup','evoDV_add_shortcode_options', 10, 1);

?>