<?php

/**
 * Embed custom fonts
 */
add_action( 'wp_enqueue_scripts', 'us_enqueue_fonts' );
function us_enqueue_fonts() {
	$prefixes = array( 'heading', 'body', 'menu' );

	$fonts = array();

	foreach ( $prefixes as $prefix ) {
		$font = explode( '|', us_get_option( $prefix . '_font_family', 'none' ), 2 );
		if ( ! isset( $font[1] ) OR empty( $font[1] ) ) {
			// Fault tolerance for missing font-variants
			$font[1] = '400,700';
		}
		$selected_font_variants = explode( ',', $font[1] );
		// Empty font or web safe combination selected
		if ( $font[0] == 'none' OR strpos( $font[0], ',' ) !== FALSE ) {
			continue;
		}

		$font[0] = str_replace( ' ', '+', $font[0] );
		if ( ! isset( $fonts[ $font[0] ] ) ) {
			$fonts[ $font[0] ] = array();
		}

		foreach ( $selected_font_variants as $font_variant ) {
			$fonts[ $font[0] ][] = $font_variant;
		}
	}

	$protocol = is_ssl() ? 'https' : 'http';
	$subset = '&subset=' . us_get_option( 'font_subset', 'latin' );
	$font_index = 1;
	foreach ( $fonts as $font_name => $font_variants ) {
		if ( count( $font_variants ) == 0 ) {
			continue;
		}
		$font_variants = array_unique( $font_variants );

		// Google font url
		$font_url = $protocol . '://fonts.googleapis.com/css?family=' . $font_name . ':' . implode( ',', $font_variants ) . $subset;
		wp_enqueue_style( 'us-font-' . $font_index, $font_url );
		$font_index ++;
	}
}

add_action( 'wp_enqueue_scripts', 'us_styles', 12 );
function us_styles() {
	global $us_template_directory_uri;

	wp_register_style( 'us-base', $us_template_directory_uri . '/framework/css/us-base.css', array(), US_THEMEVERSION, 'all' );
	wp_enqueue_style( 'us-base' );

	wp_register_style( 'us-font-awesome', $us_template_directory_uri . '/framework/css/font-awesome.css', array(), '4.6.3', 'all' );
	wp_enqueue_style( 'us-font-awesome' );

	wp_register_style( 'us-font-mdfi', $us_template_directory_uri . '/framework/css/font-mdfi.css', array(), '1', 'all' );
	wp_enqueue_style( 'us-font-mdfi' );

	wp_register_style( 'us-royalslider', $us_template_directory_uri . '/framework/vendor/royalslider/royalslider.css', array(), '9.5.7', 'all' );

	wp_register_style( 'us-style', $us_template_directory_uri . '/css/style.css', array(), US_THEMEVERSION, 'all' );
	wp_enqueue_style( 'us-style' );

	if ( us_get_option( 'responsive_layout', TRUE ) ) {
		wp_register_style( 'us-responsive', $us_template_directory_uri . '/css/responsive.css', array(), US_THEMEVERSION, 'all' );
		wp_enqueue_style( 'us-responsive' );
	}

	if ( is_rtl() ) {
		wp_register_style( 'us-rtl', $us_template_directory_uri . '/css/rtl.css', array(), US_THEMEVERSION, 'all' );
		wp_enqueue_style( 'us-rtl' );
	}
}

add_action( 'wp_enqueue_scripts', 'us_custom_styles', 17 );
function us_custom_styles() {

	if ( is_child_theme() ) {
		global $us_stylesheet_directory_uri;
		wp_enqueue_style( 'theme-style', $us_stylesheet_directory_uri . '/style.css', array(), US_THEMEVERSION, 'all' );
	}

	global $us_generate_css_file;
	$us_generate_css_file = us_get_option( 'generate_css_file', TRUE );
	if ( $us_generate_css_file ) {
		$wp_upload_dir = wp_upload_dir();
		$styles_dir = $wp_upload_dir['basedir'] . '/us-assets';
		$styles_dir = str_replace( '\\', '/', $styles_dir );
		$site_url_parts = parse_url(site_url());
		$styles_file_suffix = ( ! empty( $site_url_parts['host'] ) ) ? $site_url_parts['host'] : '';
		$styles_file_suffix .= ( ! empty( $site_url_parts['path'] ) ) ? str_replace( '/', '_', $site_url_parts['host'] ) : '';
		$styles_file_suffix = ( ! empty( $styles_file_suffix ) ) ? '-' . $styles_file_suffix : '';
		$styles_file = $styles_dir . '/' . US_THEMENAME . $styles_file_suffix . '-theme-options.css';
		if ( file_exists( $styles_file ) ) {
			$styles_file_uri = $wp_upload_dir['baseurl'] . '/us-assets/' . US_THEMENAME . $styles_file_suffix . '-theme-options.css';
			// Removing protocols for better compatibility with caching plugins and services
			$styles_file_uri = str_replace( array( 'http:', 'https:' ), '', $styles_file_uri );
			wp_enqueue_style( 'us-theme-options', $styles_file_uri, array(), US_THEMEVERSION, 'all' );
		} else {
			$us_generate_css_file = FALSE;
		}
	}
}

add_action( 'wp_enqueue_scripts', 'us_jscripts' );
function us_jscripts() {
	global $us_template_directory_uri;

	// Retrieving theme version
	wp_register_script( 'us-jquery-easing', $us_template_directory_uri . '/framework/js/jquery.easing.min.js', array( 'jquery' ), '', TRUE );
	wp_enqueue_script( 'us-jquery-easing' );

	wp_register_script( 'us-isotope', $us_template_directory_uri . '/framework/js/jquery.isotope.js', array( 'jquery' ), '', TRUE );

	wp_register_script( 'us-royalslider', $us_template_directory_uri . '/framework/vendor/royalslider/jquery.royalslider.min.js', array( 'jquery' ), '9.5.6', TRUE );

	wp_register_script( 'us-owl', $us_template_directory_uri . '/framework/js/owl.carousel.min.js', array( 'jquery' ), '2.0.0', TRUE );

	wp_register_script( 'us-magnific-popup', $us_template_directory_uri . '/framework/js/jquery.magnific-popup.js', array( 'jquery' ), '1.1.0', TRUE );
	wp_enqueue_script( 'us-magnific-popup' );

	// Google Maps are enqueued in the first map shortcode
	wp_register_script( 'us-google-maps', '//maps.googleapis.com/maps/api/js', array(), '', FALSE );
	wp_register_script( 'us-gmap', $us_template_directory_uri . '/framework/js/gmaps.min.js', array( 'jquery' ), '', TRUE );

	wp_register_script( 'us-parallax', $us_template_directory_uri . '/framework/js/jquery.parallax.js', array( 'jquery' ), US_THEMEVERSION, TRUE );
	wp_register_script( 'us-hor-parallax', $us_template_directory_uri . '/framework/js/jquery.horparallax.js', array( 'jquery' ), US_THEMEVERSION, TRUE );

	wp_register_script( 'us-simpleplaceholder', $us_template_directory_uri . '/framework/js/jquery.simpleplaceholder.js', array( 'jquery' ), '', TRUE );
	wp_enqueue_script( 'us-simpleplaceholder' );

	wp_register_script( 'us-imagesloaded', $us_template_directory_uri . '/framework/js/imagesloaded.js', array( 'jquery' ), '', TRUE );
	wp_enqueue_script( 'us-imagesloaded' );

	wp_register_script( 'us-core', $us_template_directory_uri . '/framework/js/us.core.js', array( 'jquery' ), US_THEMEVERSION, TRUE );
	wp_register_script( 'us-widgets', $us_template_directory_uri . '/framework/js/us.widgets.js', array( 'us-core' ), US_THEMEVERSION, TRUE );
	wp_register_script( 'us-theme', $us_template_directory_uri . '/js/us.theme.js', array( 'us-widgets' ), US_THEMEVERSION, TRUE );
	wp_enqueue_script( 'us-core' );
	wp_enqueue_script( 'us-widgets' );
	wp_enqueue_script( 'us-theme' );

	wp_enqueue_script( 'comment-reply' );
}

add_action( 'wp_footer', 'us_custom_html_output', 99 );
function us_custom_html_output() {
	echo us_get_option( 'custom_html', '' );
}
