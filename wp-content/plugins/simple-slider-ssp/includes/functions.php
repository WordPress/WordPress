<?php

function muneeb_load_ssp() {

	muneeb_load_ssp_classes();
	muneeb_load_default_slider_skin();

}

function muneeb_load_ssp_classes() {

	muneeb_ssp_include( 'classes/ssp_slider_post_type.php' );
	muneeb_ssp_include( 'classes/ssp_settings.php' );
	muneeb_ssp_include( 'classes/ssp_skin.php' );
	muneeb_ssp_include( 'classes/ssp_frontend_slider.php' );

	new SSP_SLIDER_POST_TYPE( TRUE );
	new SSP_SETTINGS( TRUE );
	new SSP_FRONTEND_SLIDER( TRUE );

}

function muneeb_load_default_slider_skin() {

	$default_skin  = 'ssp_skins' . DIRECTORY_SEPARATOR . 'default' .
		DIRECTORY_SEPARATOR . 'functions.php';

	include muneeb_ssp_view_path( $default_skin, FALSE );

	muneeb_ssp_default_slider_skin_hooks();

}

function muneeb_ssp_loaded() { do_action( 'ssp_loaded' ); }

function muneeb_ssp_include( $file_name, $require = true ) {

	if ( ! $require )
		require SLIDER_PLUGIN_INCLUDE_DIRECTORY . $file_name;

	include SLIDER_PLUGIN_INCLUDE_DIRECTORY . $file_name;

}

function muneeb_ssp_view_path( $view_name, $is_php = true ) {

	if ( strpos( $view_name, '.php' ) === FALSE && $is_php )
		return SLIDER_PLUGIN_VIEW_DIRECTORY . $view_name . '.php';

	return SLIDER_PLUGIN_VIEW_DIRECTORY . $view_name;

}

function muneeb_ssp_get_slides( $slider_id ) {

	$slides = get_post_meta( $slider_id, 'slides', true );

	return apply_filters( 'ssp_get_slider_slides' , $slides, $slider_id );

}

function muneeb_ssp_slider_options( $slider_id ) {

	$options = get_post_meta( $slider_id, 'options', true );

	return apply_filters( 'ssp_get_slider_options' , $options, $slider_id );

}

//print out or render the slider
function muneeb_ssp_slider( $slider_id, $shortcode_atts = NULL ) {

	SSP_SKIN::setup_slider( $slider_id, $shortcode_atts );

	SSP_SKIN::render_slider();

}

//get slider active skin
function muneeb_ssp_get_slider_skin( $slider_id ) {

	$skin = get_post_meta( $slider_id, 'skin', true );

	if ( ! $skin  )
		return 'default';

	return $skin;

}

function muneeb_ssp_slider_fixes() {

	remove_filter( 'the_content', 'wpautop' );

	add_filter( 'the_content', 'wpautop', 10 );

}


?>
