<?php
/*
	DailyView Admin init
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function evoDV_admin_init(){

	add_filter( 'eventon_appearance_add', 'evoDV_appearance_settings' , 10, 1);
	add_filter( 'eventon_inline_styles_array','evoDV_dynamic_styles' , 1, 1);
}
add_action('admin_init', 'evoDV_admin_init');

function evoDV_appearance_settings($array){
	
	$new[] = array('id'=>'evoDV','type'=>'hiddensection_open','name'=>'DailyView Styles');
	$new[] = array('id'=>'evoDV','type'=>'fontation','name'=>'Date Number Font Color',
		'variations'=>array(
			array('id'=>'evoDV_1', 'name'=>'Default','type'=>'color', 'default'=>'e8e8e8'),
			array('id'=>'evoDV_2', 'name'=>'Default (Hover)','type'=>'color', 'default'=>'d4d4d4'),
			array('id'=>'evoDV_3', 'name'=>'Days with events','type'=>'color', 'default'=>'d5c3ac'),
			array('id'=>'evoDV_4', 'name'=>'Days with events (Hover)','type'=>'color', 'default'=>'d5c3ac')
			,array('id'=>'evoDV_5', 'name'=>'Focus Day','type'=>'color', 'default'=>'a4a4a4'),
			array('id'=>'evoDV_6', 'name'=>'Focus Day (Hover)','type'=>'color', 'default'=>'a4a4a4'),					
		)
	);
	$new[] = array('id'=>'evoDV','type'=>'fontation','name'=>'Date Number Box Color',
		'variations'=>array(
			array('id'=>'evoDV_1b', 'name'=>'Default','type'=>'color', 'default'=>'ffffff'),
			array('id'=>'evoDV_2b', 'name'=>'Default (Hover)','type'=>'color', 'default'=>'fbfbfb'),
			array('id'=>'evoDV_3b', 'name'=>'Days with events','type'=>'color', 'default'=>'ffffff'),
			array('id'=>'evoDV_4b', 'name'=>'Days with events (Hover)','type'=>'color', 'default'=>'fbfbfb')
			,array('id'=>'evoDV_5b', 'name'=>'Focus Day','type'=>'color', 'default'=>'f7f7f7'),
			array('id'=>'evoDV_6b', 'name'=>'Focus Day (Hover)','type'=>'color', 'default'=>'f7f7f7'),					
			array('id'=>'evoDV_7b', 'name'=>'Focus Day (Border Color)','type'=>'color', 'default'=>'747474'),					
		)
	);
	$new[] = array('id'=>'evoDV','type'=>'fontation','name'=>'Current Date Box',
		'variations'=>array(
			array('id'=>'evoDV_8', 'name'=>'Background Color','type'=>'color', 'default'=>'d5c3ac'),
			array('id'=>'evoDV_8b', 'name'=>'Font Color','type'=>'color', 'default'=>'ffffff'),							
		)
	);
	$new[] = array('id'=>'evoDV','type'=>'fontation','name'=>'Day Stripe',
		'variations'=>array(
			array('id'=>'evoDV_9', 'name'=>'Arrow Color','type'=>'color', 'default'=>'919191'),
			array('id'=>'evoDV_9b', 'name'=>'Arrow Color (hover)','type'=>'color', 'default'=>'919191'),
			//array('id'=>'evoDV_9c', 'name'=>'Gradient Background color','type'=>'color', 'default'=>'ffffff'),							
		)
	);
	$new[] = array('id'=>'evoDV','type'=>'hiddensection_close','name'=>'DailyView Styles');

	return array_merge($array, $new);
}

function evoDV_dynamic_styles($_existen){
	$new= array(
							
		array(
			'item'=>'.eventon_daily_list .evcal_arrows',
			'css'=>'color:#$', 'var'=>'evoDV_9','default'=>'919191'					
		),array(
			'item'=>'.eventon_daily_list .evcal_arrows:hover',
			'css'=>'color:#$', 'var'=>'evoDV_9b','default'=>'919191'					
		),array(
			'item'=>'.eventon_daily_in .evo_day',
			'multicss'=>array(
				array('css'=>'background-color:#$', 'var'=>'evoDV_1b','default'=>'ffffff'),
				array('css'=>'color:#$', 'var'=>'evoDV_1','default'=>'e8e8e8')
			)						
		),array(
			'item'=>'.eventon_daily_in .evo_day:hover',
			'multicss'=>array(
				array('css'=>'background-color:#$', 'var'=>'evoDV_2b','default'=>'fbfbfb'),
				array('css'=>'color:#$', 'var'=>'evoDV_2','default'=>'d4d4d4')
			)						
		),array(
			'item'=>'.eventon_daily_in .evo_day.has_events',
			'multicss'=>array(
				array('css'=>'background-color:#$', 'var'=>'evoDV_3b','default'=>'ffffff'),
				array('css'=>'color:#$', 'var'=>'evoDV_3','default'=>'d5c3ac')
			)						
		),array(
			'item'=>'.eventon_daily_in .evo_day.has_events:hover',
			'multicss'=>array(
				array('css'=>'background-color:#$', 'var'=>'evoDV_4b','default'=>'fbfbfb'),
				array('css'=>'color:#$', 'var'=>'evoDV_4','default'=>'d5c3ac')
			)						
		),array(
			'item'=>'.eventon_daily_in .evo_day.on_focus',
			'multicss'=>array(			
				array('css'=>'background-color:#$', 'var'=>'evoDV_5b','default'=>'f7f7f7'),	
				array('css'=>'color:#$', 'var'=>'evoDV_5','default'=>'a4a4a4'),
				array('css'=>'border-color:#$', 'var'=>'evoDV_7b','default'=>'747474'),
			)						
		),array(
			'item'=>'.evo_day.on_focus:hover',
			'multicss'=>array(
				array('css'=>'background-color:#$', 'var'=>'evoDV_6b','default'=>'f7f7f7'),
				array('css'=>'color:#$', 'var'=>'evoDV_6','default'=>'a4a4a4')
			)						
		),
		array(
			'item'=>'.evodv_current_day',
			'multicss'=>array(
				array('css'=>'background-color:#$', 'var'=>'evoDV_8','default'=>'d5c3ac'),
				array('css'=>'color:#$', 'var'=>'evoDV_8b','default'=>'ffffff'),
			)	
		)
	);
	

	return (is_array($_existen))? array_merge($_existen, $new): $_existen;
}

