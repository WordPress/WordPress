<?php
/**
 * EventON Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON/Functions/AJAX
 * @version     1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Frontend AJAX events **************************************************/

// ICS file generation for add to calendar buttons
	function eventon_ics_download(){
		$event_id = (int)($_GET['event_id']);
		$sunix = (int)($_GET['sunix']);
		$eunix = (int)($_GET['eunix']);

		//error_reporting(E_ALL);
		//ini_set('display_errors', '1');
		
		//$the_event = get_post($event_id);
		$ev_vals = get_post_custom($event_id);
		
		$event_start_unix = $sunix;
		$event_end_unix = (!empty($eunix))? $eunix : $sunix;
		
		
		$name = get_the_title($event_id);
		
		
		$location = (!empty($ev_vals['evcal_location']))? $ev_vals['evcal_location'][0] : ''; 
		$start = evo_get_adjusted_utc($event_start_unix);
		$end = evo_get_adjusted_utc($event_end_unix);
		//$description = $the_event->post_content;
		
		ob_clean();
		
		$slug = strtolower(str_replace(array(' ', "'", '.'), array('_', '', ''), $name));
		
		
		header("Content-Type: text/Calendar; charset=utf-8");
		header("Content-Disposition: inline; filename={$slug}.ics");
		echo "BEGIN:VCALENDAR\n";
		echo "VERSION:2.0\n";
		echo "PRODID:-//eventon.com NONSGML v1.0//EN\n";
		//echo "METHOD:REQUEST\n"; // requied by Outlook
		echo "BEGIN:VEVENT\n";
		echo "UID:eventon.com\n"; // required by Outlok
		echo "DTSTAMP:".date_i18n('Ymd').'T'.date_i18n('His')."\n"; // required by Outlook
		echo "DTSTART:{$start}\n"; 
		echo "DTEND:{$end}\n";
		echo "LOCATION:{$location}\n";
		echo "SUMMARY:{$name}\n";
		echo "DESCRIPTION: {$name}\n";
		echo "END:VEVENT\n";
		echo "END:VCALENDAR";
		exit;
	}
	add_action('wp_ajax_eventon_ics_download', 'eventon_ics_download');
	add_action('wp_ajax_nopriv_eventon_ics_download', 'eventon_ics_download');



// Verify eventon Licenses AJAX function
	function eventon_license_verification(){
		global $eventon;	
		$new_license_content= '';
		$error_msg='00';
		
		$license_errors = array( 
			'01'=>"No data returned from envato API",
			"02"=>'Your license is not a valid one!, please check and try again.',
			"03"=>'envato verification API is busy at moment, please try later.',
			"00"=>'Could not verify the License key. Please try again.'
		);
		
		
		$key = $_POST['key'];
		$slug = $_POST['slug'];
		
		
		// verify license from eventon server
		$status = $eventon->evo_updater->_verify_license_key($slug, $key);
		
		
		if($status=='1'){
			$save_license_date = $eventon->evo_updater->save_license_key($slug, $key);
			
					
			// successfully saved new verified license
			if($save_license_date!=false){
				$status = 'success';
				
				$new_license_content ="License Status: <strong>Activated</strong>";
			}else{
				$status='error';
			}
		}else{	
			if(!empty($status))
				$error_msg = $license_errors[$status];
				$status='error'; 		
		}
		
		
		$return_content = array(
			'status'=>$status,		
			'new_content'=>$new_license_content,
			'error_msg'=>$error_msg
		);
		echo json_encode($return_content);		
		exit;
		
	}
	add_action('wp_ajax_eventon_verify_lic', 'eventon_license_verification');
	add_action('wp_ajax_nopriv_eventon_verify_lic', 'eventon_license_verification');

// activate addon license
	function eventon_addon_license_activation(){
		global $eventon;	
		$new_license_content= '';
		$error_msg='00';
		
		$license_errors = array( 
			'01'=>"Could not connect to remote server at this time.",
			"02"=>'Your license is not a valid one!, please check and try again.',
			"00"=>'Could not verify the License key. Please try again.'
		);
		

		$__data = array(
			'slug'=>$_POST['slug'],
			'key'=>$_POST['key'],
			'email'=>$_POST['email'],
			'product_id'=>$_POST['product_id']
		);
		
		
		// verify license from eventon server
		$status = $eventon->evo_updater->ADD_verify_lic($__data);

			$server_response = $status;

		// response OK and returned values
		if($status){
			// if activated value is true
			if($status->activated){
				$status = 'success';
				$new_license_content ="License Status: <strong>Activated</strong>";

				$__save_new_lic = $eventon->evo_updater->ADD_save_lic($__data);
			}else{
			// return activated to be not true
				$status = 'failed';
				$error_msg = $license_errors['00'];
			}
			
		}else{
		// response was not OK
			$status = 'failed';
			$error_msg = $license_errors['02'];
		}
		
			
		
		$return_content = array(
			'response'=>$__save_new_lic,
			'status'=>$status,
			'error_msg'=>$error_msg,	
			'new_content'=>$new_license_content,
		);
		echo json_encode($return_content);		
		exit;
	}
	add_action('wp_ajax_eventon_addon_lic_activate', 'eventon_addon_license_activation');
	add_action('wp_ajax_nopriv_eventon_addon_lic_activate', 'eventon_addon_license_activation');




/** 	Primary function to load event data */
	function evcal_ajax_callback(){
		global $eventon;
		$shortcode_args;
		
		// month year values
		$current_month = (int)($_POST['current_month']);
		$current_year = (int)($_POST['current_year']);	

		$send_unix = (isset($_POST['send_unix']))? $_POST['send_unix']:null;
		$direction = $_POST['direction'];
		$sort_by = (!empty($_POST['sort_by']))? $_POST['sort_by']: 'sort_date';
		
		// generate new UNIX of NOT
		if($send_unix=='1'){
			$focus_start_date_range = (isset($_POST['focus_start_date_range']))? (int)($_POST['focus_start_date_range']):null;
			$focus_end_date_range = (isset($_POST['focus_end_date_range']))? (int)($_POST['focus_end_date_range']):null;	
			
			$focused_month_num = $current_month;
			$focused_year = $current_year;

		}else{
			if($direction=='none'){
				$focused_month_num = $current_month;
				$focused_year = $current_year;
			}else{
				$focused_month_num = ($direction=='next')?
					(($current_month==12)? 1:$current_month+1):
					(($current_month==1)? 12:$current_month-1);
				
				$focused_year = ($direction=='next')? 
					(($current_month==12)? $current_year+1:$current_year):
					(($current_month==1)? $current_year-1:$current_year);
			}	
			
				
			$focus_start_date_range = mktime( 0,0,0,$focused_month_num,1,$focused_year );
			$time_string = $focused_year.'-'.$focused_month_num.'-1';		
			$focus_end_date_range = mktime(23,59,59,($focused_month_num),(date('t',(strtotime($time_string) ))), ($focused_year));
		}
		
		$eve_args = array(
			'focus_start_date_range'=>$focus_start_date_range,
			'focus_end_date_range'=>$focus_end_date_range,
			'sort_by'=>$sort_by,		
			'event_count'=>$_POST['event_count'],
			'filters'=>((isset($_POST['filters']))? $_POST['filters']:null)
		);
		
		// shortcode arguments USED to build calendar
		$shortcode_args_arr = $_POST['shortcode'];
		
		if(!empty($shortcode_args_arr) && count($shortcode_args_arr)>0){
			foreach($shortcode_args_arr as $f=>$v){
				$shortcode_args[$f]=$v;
			}
			$eve_args = array_merge($eve_args, $shortcode_args);
			$lang = $shortcode_args_arr['lang'];
		}else{
			$lang ='';
		}
		
		
		// GET calendar header month year values
		$calendar_month_title = get_eventon_cal_title_month($focused_month_num, $focused_year, $lang);
		
		
		// Addon hook
		if(has_filter('eventon_ajax_arguments')){
			$eve_args = apply_filters('eventon_ajax_arguments',$eve_args, $_POST);
		}
		
		//print_r($eve_args);
		
		$content_li = $eventon->evo_generator->eventon_generate_events( $eve_args);
		
		
		// RETURN VALUES
		// Array of content for the calendar's AJAX call returned in JSON format
		$return_content = array(		
			'content'=>$content_li,
			'cal_month_title'=>$calendar_month_title,
			'month'=>$focused_month_num,
			'year'=>$focused_year,
			'focus_start_date_range'=>$focus_start_date_range,
			'focus_end_date_range'=>$focus_end_date_range,		
		);			
		
		
		echo json_encode($return_content);
		exit;
	}
	add_action('wp_ajax_the_ajax_hook', 'evcal_ajax_callback');
	add_action('wp_ajax_nopriv_the_ajax_hook', 'evcal_ajax_callback');



/* dynamic styles */
	function eventon_dymanic_css(){
		//global $foodpress_menus;
		require('admin/inline-styles.php');
		exit;
	}
	add_action('wp_ajax_evo_dynamic_css', 'eventon_dymanic_css');
	add_action('wp_ajax_nopriv_evo_dynamic_css','eventon_dymanic_css');




/** Admin AJAX Event  *****************************************************/

/** Feature an event from admin */
	function eventon_feature_event() {

		if ( ! is_admin() ) die;

		if ( ! current_user_can('edit_eventon') ) wp_die( __( 'You do not have sufficient permissions to access this page.', 'eventon' ) );

		if ( ! check_admin_referer('eventon-feature-event')) wp_die( __( 'You have taken too long. Please go back and retry.', 'eventon' ) );

		$post_id = isset( $_GET['eventID'] ) && (int) $_GET['eventID'] ? (int) $_GET['eventID'] : '';

		if (!$post_id) die;

		$post = get_post($post_id);

		if ( ! $post || $post->post_type !== 'ajde_events' ) die;

		$featured = get_post_meta( $post->ID, '_featured', true );

		if ( $featured == 'yes' )
			update_post_meta($post->ID, '_featured', 'no');
		else
			update_post_meta($post->ID, '_featured', 'yes');

		wp_safe_redirect( remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer() ) );
	}
	add_action('wp_ajax_eventon-feature-event', 'eventon_feature_event');




/**
 * EventBrite API loading function for admin
 *
 * @access public
 * @return void
 */	
add_action('wp_ajax_the_post_ajax_hook_3', 'evcal_ajax_callback_3');
add_action('wp_ajax_nopriv_the_post_ajax_hook_3', 'evcal_ajax_callback_3');
function evcal_ajax_callback_3(){
	// pre
	$code = $status = $message = '';
	$evcal_opt1= get_option('evcal_options_evcal_1');
	
	$eb_event_id = $_POST['event_id'];
	$eb_api = $evcal_opt1['evcal_evb_api'];
	
	$xml =simplexml_load_file('http://www.eventbrite.com/xml/event_get?app_key='.$eb_api.'&id='.$eb_event_id );					

	if($xml->getName()!='error'):		
		$status=1;
		
		if($xml->status =='Completed'){
			$message='past';
		}
		
		// pre
		$venue = $xml->venue;
		$location = ((!empty($venue->address) )? $venue->address.', ':null ).
			$venue->city.' '.$venue->region.' '.
			$venue->postal_code;
			
		
		$code.= "<div var='title' class='evcal_data_row '>
			<p>Event Name</p>
			<p class='value'>".$xml->title."</p>
			<em class='clear'></em>
		</div>";
		
		$code.= "<div var='evcal_location' class='evcal_data_row '>
			<p>Location</p>
			<p class='value'>".$location."</p>
			<em class='clear'></em>
		</div>";
		$code.= "<div var='capacity' class='evcal_data_row '>
			<p>Event Capacity</p>
			<p class='value'>".$xml->capacity."</p>
			<em class='clear'></em>
		</div>";
		$code.= "<div var='price' class='evcal_data_row '>
			<p>Ticket Price</p>
			<p class='value'>".$xml->tickets->ticket->currency.' '.$xml->tickets->ticket->price."</p>
			<em class='clear'></em>
		</div>";		
		$code.= "<div var='url' class='evcal_data_row '>
			<p>Buy Now Ticket URL</p>
			<p class='value'>".$xml->url."</p>								
		</div><p class='clear'></p>	";
		
	else:
		$status =0;
	endif;	

	$return_content = array(
		'status'=>$status,
		'message'=>$message,
		'code'=>$code	
	);			
	echo json_encode($return_content);		
	exit;
}

/**
 * Meetup API function for admin
 *
 * @access public
 * @return void
 */	
add_action('wp_ajax_the_post_ajax_hook_2', 'evcal_ajax_callback_2');
add_action('wp_ajax_nopriv_the_post_ajax_hook_2', 'evcal_ajax_callback_2');
function evcal_ajax_callback_2(){
	
	// pre
	$code = $status = '';
	$evcal_opt1= get_option('evcal_options_evcal_1');
	$wp_time_format = get_option('time_format');
	
	$mu_event_id = $_POST['event_id'];
	$mu_api = $evcal_opt1['evcal_api_mu_key'];
	
	$xml =simplexml_load_file('http://api.meetup.com/2/event/'.
		$mu_event_id.'.xml?key='.$mu_api.'&sign=true');					

	if($xml->getName()!='error'):
		$status=1;
		// pre
		$venue = $xml->venue;
		$location = $venue->address_1.', '.
			$venue->city.' '.$venue->state.' '.
			$venue->zip;
			
		$utc_offset = substr($xml->utc_offset, 0, -3);
		$time_raw = substr($xml->time, 0, -3);
		
		$time_s = ((int)($time_raw)) + ((int)($utc_offset));
		
		
		$time_formated = date("l F j, Y",$time_s);
		$time_formated_2 = date("n/j/Y",$time_s);
		$s_hour = date("g",$time_s);
		$s_min = date("i",$time_s);
		$s_ampm = date("A",$time_s);
		//print_r( $location);
		
		
		$code.= "<div var='title' class='evcal_data_row '>
			<p>Event Name</p>
			<p class='value'>".$xml->name."</p>
			<em class='clear'></em>
		</div>";
		$code.= "<div var='evcal_location' class='evcal_data_row '>
			<p>Location</p>
			<p class='value'>".$location."</p>
			<em class='clear'></em>
		</div>";
		
		$code.= "<div var='time' class='evcal_data_row '>
			<p>Time</p>
			<p class='value' ftime='".$time_formated_2."' hr='".$s_hour."' min='".$s_min."' ampm='".$s_ampm."'>".$time_formated."</p>
		</div>";
									
		
		$code.= "<div var='url' class='evcal_data_row '>
			<p>Event URL</p>
			<p class='value'>".$xml->event_url."</p>								
		</div><p class='clear'></p>	";
		
	else:
		$status =0;
	endif;	

	$return_content = array(
		'status'=>$status,
		'code'=>$code
	);			
	echo json_encode($return_content);		
	exit;
					
}	


?>