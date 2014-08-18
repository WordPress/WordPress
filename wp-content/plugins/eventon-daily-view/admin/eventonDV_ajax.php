<?php
/**
 * EventON dailyview Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	dailyview/Functions/AJAX
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 *	AJAX
 *	generate the date list for a given month
 *	hooks into each date in fullcal
 */
function evoDV_ajax_days_list(){
	global $eventon_dv;
	
	$filters = ((isset($_POST['filters']))? $_POST['filters']:null);
		
	$days_content = $eventon_dv->get_daily_view_list(
		$_POST['next_d'],
		$_POST['next_m'], 
		$_POST['next_y'], 
		$filters,
		$_POST['shortcode']
	);
			
	$return_content = array(
		'days_list'=> $days_content,
		'last_date_of_month'=>$eventon_dv->days_in_month($_POST['next_m'], $_POST['next_y']),
		'status'=>'ok'
	);
	
	echo json_encode($return_content);		
	exit;
}
add_action('wp_ajax_the_ajax_daily_view', 'evoDV_ajax_days_list');
add_action('wp_ajax_nopriv_the_ajax_daily_view', 'evoDV_ajax_days_list');

/**
 *	AJAX Filter
 *	Get events for a single day
 *	This plugs into evcal_ajax_callback()
 */
function evoDV_ajax_filter($eve_args){
	global $eventon_dv;
	
	if(isset($_POST['dv_focus_day'])){
		$focused_month_num = date('n', $eve_args['focus_start_date_range']);
		$focused_year = date('Y', $eve_args['focus_start_date_range'] );
		$new_day = $_POST['dv_focus_day'];

		$number_days_in_month = $eventon_dv->days_in_month( $focused_month_num, $focused_year);
		
		$new_day = ($new_day<$number_days_in_month)? $new_day: $number_days_in_month;

		$focus_start_date_range = mktime( 0,0,0,$focused_month_num,$new_day,$focused_year );
		$focus_end_date_range = mktime(23,59,59,($focused_month_num),$new_day, ($focused_year));
		
		
		
		$eve_args['focus_start_date_range']=$focus_start_date_range;
		$eve_args['focus_end_date_range']=$focus_end_date_range;
		
	}
	return $eve_args;
}
add_filter('eventon_ajax_arguments','evoDV_ajax_filter', 10, 2);
?>