<?php

function muneeb_ssp_default_slider_skin_enqueue() {

	if ( is_admin() ) return FALSE;

	$flex_stylesheet = plugins_url( 'lib/flexslider.css', __FILE__ );
	$flex_script = plugins_url( 'lib/jquery.flexslider-min.js', __FILE__ );

	wp_enqueue_style( 'ssp-flexslider-css', $flex_stylesheet, array(), SLIDER_PLUGIN_VERSION );

	wp_enqueue_script( 'ssp-flexslider', $flex_script, array( 'jquery' ), SLIDER_PLUGIN_VERSION );

}

function muneeb_ssp_default_slider_skin_wp_head() {

	
	//stylesheet
	echo '<style>';
		include 'lib/style.css';
	echo '</style>';
	//stylesheet end

}

function muneeb_ssp_default_slider_skin_theme_option( $slider_id ) {

	$skin = muneeb_ssp_get_slider_skin( $slider_id );

	if ( $skin !== 'default' )
		return FALSE;
	
 
}

function muneeb_ssp_default_slider_skin_hooks() {

	add_action( 'wp_enqueue_scripts', 'muneeb_ssp_default_slider_skin_enqueue', 99 );
	
	//add_action( 'wp_head', 'muneeb_ssp_default_slider_skin_wp_head' );

	add_action( 'ssp_options_before_control_option', 
		'muneeb_ssp_default_slider_skin_theme_option' );

}

?>