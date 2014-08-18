<?php
/**
 * Language Settings 
 *
 * @version		2.2.10
 * @package		EventON/settings
 * @category	Settings
 * @author 		AJDE
 */

// Event type custom taxonomy NAMES
	$evopt = get_option('evcal_options_evcal_1');
	$event_type_names = evo_get_ettNames($evopt);
		

	$_ett_lang_ar = array();
	$ett_verify = evo_get_ett_count($evopt);
	for($x=1; $x< ($ett_verify+1); $x++){
		$_ett_lang_ar[$x]= array('label'=>$event_type_names[$x],'name'=>'evcal_lang_et'.$x);		
	}

// Custom meta fields
	$_cmd_lang_ar = array();
	$cmd_verify = evo_retrieve_cmd_count($evopt);
	for($x=1; $x< ($cmd_verify+1); $x++){

		$label = $evopt['evcal_ec_f'.$x.'a1'];
		$_cmd_lang_ar[$x]= array('label'=>$label,'name'=>'evcal_cmd_'.$x);		
	}


// MAIN ARRAY
$eventon_custom_language_array = array(
	array('type'=>'togheader','name'=>'General Calendar'),

		array(
			'label'=>'No Events',
			'name'=>'evcal_lang_noeve',
			'legend'=>''
		),array(
			'label'=>'All Day',
			'name'=>'evcal_lang_allday',
			'legend'=>''
		),array('type'=>'togend'),
	array(
		'type'=>'togheader',
		'name'=>'Calendar Header'
	),
		array(
			'label'=>'Jump Months',
			'name'=>'evcal_lang_jumpmonths',
			'legend'=>''
		),array(
			'label'=>'Jump Months: Month',
			'name'=>'evcal_lang_jumpmonthsM',
			'legend'=>''
		),array(
			'label'=>'Jump Months: Year',
			'name'=>'evcal_lang_jumpmonthsY',
			'legend'=>''
		),array(
			'label'=>'Sort Options',
			'name'=>'evcal_lang_sopt',
			'legend'=>''
		)
		,array(
			'label'=>'Sort By',
			'name'=>'evcal_lang_sort',
			'legend'=>''
		),array(
			'label'=>'Date',
			'name'=>'evcal_lang_sdate',
			'legend'=>''
		),array(
			'label'=>'Title',
			'name'=>'evcal_lang_stitle',
			'legend'=>''
		),array(
			'label'=>'All',
			'name'=>'evcal_lang_all',
			'legend'=>'Sort options all text'
		),
		$_ett_lang_ar[1],
		$_ett_lang_ar[2],
		( !empty($_ett_lang_ar[3])? $_ett_lang_ar[3]: null),
		( !empty($_ett_lang_ar[4])? $_ett_lang_ar[4]: null),		

	array('type'=>'togend'),
	array('type'=>'togheader','name'=>'Event Card'),

	array(
		'label'=>'Location Name',
		'name'=>'evcal_lang_location_name',
		'legend'=>''
	),array(
		'label'=>'Location',
		'name'=>'evcal_lang_location',
		'legend'=>''
	),array(
		'label'=>'Type your address',
		'name'=>'evcalL_getdir_placeholder',
		'legend'=>'Get directions section'
	),array(
		'label'=>'Click here to get directions',
		'name'=>'evcalL_getdir_title',
		'legend'=>'Get directions section'
	),array(
		'label'=>'Time',
		'name'=>'evcal_lang_time',
		'legend'=>''
	),array(
		'label'=>'Color',
		'name'=>'evcal_lang_scolor',
		'legend'=>''
	)	
	,array(
		'label'=>'At (event location)',
		'name'=>'evcal_lang_at',
		'legend'=>''
	),array(
		'label'=>'Event Details',
		'name'=>'evcal_evcard_details',
		'legend'=>''
	),array(
		'label'=>'Event Organized by',
		'name'=>'evcal_evcard_org',
		'legend'=>''
	),array(
		'label'=>'Close event button text',
		'name'=>'evcal_lang_close',
	),array(
		'label'=>'More',
		'name'=>'evcal_lang_more',
		'legend'=>'More/less text for long event description'
	),array(
		'label'=>'Less',
		'name'=>'evcal_lang_less',
		'legend'=>'More/less text for long event description'
	),array(
		'label'=>'Buy ticket via Paypal',
		'name'=>'evcal_evcard_tix1',
		'legend'=>'for Paypal'
	),array(
		'label'=>'Buy Now button text',
		'name'=>'evcal_evcard_btn1',
		'legend'=>'for Paypal'
	),array(
		'label'=>'Ticket for the event',
		'name'=>'evcal_evcard_tix2',
		'legend'=>'for eventbrite'
	),array(
		'label'=>'Buy now button',
		'name'=>'evcal_evcard_btn2',
		'legend'=>'for eventbrite'
	),array(
		'label'=>'Event Capacity',
		'name'=>'evcal_evcard_cap',
		'legend'=>''
	),array(
		'label'=>'Learn More about this event',
		'name'=>'evcal_evcard_learnmore',
		'legend'=>'for meetup'
	),array(
		'label'=>'Learn More link text',
		'name'=>'evcal_evcard_learnmore2',
		'legend'=>'for meetup'
	),

	array('type'=>'subheader','label'=>'Add to calendar Section'),

		array(
			'label'=>'Calendar','name'=>'evcal_evcard_calncal',			
		),array(
			'label'=>'GoogleCal','name'=>'evcal_evcard_calgcal',			
		),array(
			'label'=>'Add to your calendar',
			'name'=>'evcal_evcard_addics',
			'legend'=>'Alt text for add to calendar button'
		),array(
			'label'=>'Add to google calendar',
			'name'=>'evcal_evcard_addgcal',
			'legend'=>'Alt text for add to google calendar button'
		),
	array('type'=>'togend'),

	( !empty($_cmd_lang_ar[1])? $_cmd_lang_ar[1]: null),
	( !empty($_cmd_lang_ar[2])? $_cmd_lang_ar[2]: null),
	( !empty($_cmd_lang_ar[3])? $_cmd_lang_ar[3]: null),

	array('type'=>'togend'),
	
);
?>