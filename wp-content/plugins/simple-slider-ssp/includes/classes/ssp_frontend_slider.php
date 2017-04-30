<?php

class SSP_FRONTEND_SLIDER{

	function __construct( $do_start = false ) {

		if ( $do_start )
			$this->start();

	}

	function start() {

		if ( is_admin() ) return;

		$this->hooks();
		$this->filters();
		$this->shortcodes();

	}

	function hooks() {

		add_action( 'init', array( $this, 'enqueue_scripts' ) );

	}

	function filters() {



	}

	function enqueue_scripts() {

		wp_enqueue_script( 'jquery' );

	}

	function shortcodes() {

		add_shortcode( SLIDER_PLUGIN_SLIDER_SHORTCODE, 
					array( $this, 'shortcode_slider' ) );

	}

	function shortcode_slider( $atts, $content = null ) {
		
		global $post;

		extract( shortcode_atts( array(
			'id' => NULL,
			'slides_src' => NULL
		), $atts ));


		if ( ( $id === NULL || is_int( $id ) ) && ! $slides_src  ) {
			echo __( 'Error: The shortcode attribute "id" is not a integer or is missing.', SLIDER_PLUGIN_PREFIX );
			return FALSE;
		}

		if ( get_post_status( $id ) == 'trash' && ! $slides_src ) {
			echo __( 'Error: Slider is in trash.', SLIDER_PLUGIN_PREFIX );
			return FALSE;
		}

		if ( get_post_status( $id ) !== 'publish' && ! $slides_src )
			return FALSE;

		$atts['slides_src'] = $slides_src;

		if ( $post )
			$atts['post_id'] = $post->ID;
		else
			$atts['post_id'] = NULL;

		$atts = apply_filters( 'ssp_shortcode_atts', $atts );

		ob_start();

		SSP_SKIN::setup_slider( $id, $atts );
		SSP_SKIN::render_slider();
		
		//$content = ob_get_contents();
		//ob_end_clean();

		return ob_get_clean();
		
	}

}