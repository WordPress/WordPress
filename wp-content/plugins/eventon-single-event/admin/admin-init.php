<?php
/*
	single event Admin init
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function evoSE_admin_init(){

	add_filter( 'eventon_appearance_add', 'evoSE_appearance_settings' , 10, 1);
	add_filter( 'eventon_inline_styles_array','evoSE_dynamic_styles' , 10, 1);
	add_filter( 'eventon_uix_shortcode_opts','evoSE_shortcode_ux_opts' , 10, 1);
}
add_action('admin_init', 'evoSE_admin_init');


// inject into shortcode generator popup for user interaction options
	function evoSE_shortcode_ux_opts($array){
		$new_arr = $array;

		$new_arr['4']='Open in single event page';
		return $new_arr;
	}

function evoSE_appearance_settings($array){
	
	$new[] = array('id'=>'evose','type'=>'hiddensection_open','name'=>'Social Media Styles');
	$new[] = array('id'=>'evose','type'=>'fontation','name'=>'Social Media Icons',
		'variations'=>array(
			array('id'=>'evose_1', 'name'=>'Icon Color','type'=>'color', 'default'=>'d4d4d4'),
			array('id'=>'evose_2', 'name'=>'Icon Color (Hover)','type'=>'color', 'default'=>'9e9e9e'),
			array('id'=>'evose_3', 'name'=>'Icon Box Color','type'=>'color', 'default'=>'dfa872'),
			array('id'=>'evose_4', 'name'=>'Icon Box Color (Hover)','type'=>'color', 'default'=>'9e9e9e')
			,				
		)
	);	
	$new[] = array('id'=>'evose','type'=>'hiddensection_close','name'=>'Social Media Styles');

	return array_merge($array, $new);
}

function evoSE_dynamic_styles($_existen){
	$new= array(
		array(
			'item'=>'.evo_metarow_socialmedia a.evo_ss',
			'multicss'=>array(
				array('css'=>'background-color:#$', 'var'=>'evose_3','default'=>'transparent'),
			)						
		),array(
			'item'=>'.evo_metarow_socialmedia a.evo_ss:hover',
			'multicss'=>array(
				array('css'=>'background-color:#$', 'var'=>'evose_4','default'=>'9d9d9d'),
			)						
		),array(
			'item'=>'.evo_metarow_socialmedia a.evo_ss i',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evose_1','default'=>'858585')
			)						
		),array(
			'item'=>'.evo_metarow_socialmedia a.evo_ss:hover i',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evose_2','default'=>'ffffff')
			)						
		),
	);
	

	return (is_array($_existen))? array_merge($_existen, $new): $_existen;
}
