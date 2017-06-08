<?php

add_action( 'admin_menu', 'us_options_admin_menu', 9 );
function us_options_admin_menu() {
	add_menu_page( __( 'Theme Options', 'us' ), US_THEMENAME, 'manage_options', 'us-theme-options', 'us_theme_options_page', NULL, '59.001' );
	$usof_page = add_submenu_page( 'us-theme-options', US_THEMENAME, __( 'Theme Options', 'us' ), 'edit_theme_options', 'us-theme-options', 'us_theme_options_page' );
	add_action( 'admin_print_scripts-' . $usof_page, 'usof_print_scripts' );
	add_action( 'admin_print_styles-' . $usof_page, 'usof_print_styles' );
	add_action( 'admin_print_styles-' . $usof_page, 'usof_print_fonts' );

	add_action( 'admin_print_scripts-post-new.php', 'usof_print_scripts' );
	add_action( 'admin_print_scripts-post.php', 'usof_print_scripts' );

	add_action( 'admin_notices', 'usof_hide_admin_notices_start', 1 );
	add_action( 'admin_notices', 'usof_hide_admin_notices_end', 1000 );
}

function us_theme_options_page() {

	// For notices
	echo '<div class="wrap"><h2 class="hidden"></h2>';

	global $usof_directory, $usof_options;
	usof_load_options_once();
	$usof_options = array_merge( usof_defaults(), $usof_options );

	// Preserving values for hidden fields
	$hidden_fields_values = array();

	$visited_new_sections = array();
	if ( isset( $_COOKIE ) AND isset( $_COOKIE['usof_visited_new_sections'] ) ) {
		$visited_new_sections = explode( ',', $_COOKIE['usof_visited_new_sections'] );
	}

	$config = us_config( 'theme-options', array() );
	echo '<div class="usof-container';
	echo apply_filters( 'usof_container_classes', '' );
	if ( get_option( 'us_license_activated', 0 ) ) {
		echo ' theme_activated';
	}
	echo '" data-ajaxurl="' . esc_attr( admin_url( 'admin-ajax.php' ) ) . '">';
	echo '<form class="usof-form" method="post" action="#" autocomplete="off">';
	// Output _nonce and _wp_http_referer hidden fields for ajax secuirity checks
	wp_nonce_field( 'usof-actions' );
	echo '<div class="usof-header"><div class="usof-header-logo">';
	echo US_THEMENAME . ' <span class="version">' . US_THEMEVERSION . '</span><span class="dash">&mdash;</span></div>';
	echo '<div class="usof-header-title"><h2>' . __( 'General Settings', 'us' ) . '</h2></div>';
	echo '<div class="usof-control for_save status_clear">';
	echo '<button class="usof-button type_save" type="button"><span>' . __( 'Save Changes', 'us' ) . '</span>';
	echo '<span class="usof-preloader"></span></button>';
	echo '<div class="usof-control-message"></div></div></div>';

	// Main menu
	echo '<div class="usof-nav"><div class="usof-nav-control"></div><ul class="usof-nav-list level_1">';
	foreach ( $config as $section_id => &$section ) {
		if ( isset( $section['place_if'] ) AND ! $section['place_if'] ) {
			continue;
		}
		if ( ! isset( $active_section ) ) {
			$active_section = $section_id;
		}
		echo '<li class="usof-nav-item level_1 id_' . $section_id . ( ( $section_id == $active_section ) ? ' current' : '' ) . '"';
		echo ' data-id="' . $section_id . '">';
		echo '<a class="usof-nav-anchor level_1" href="#' . $section_id . '">';
		echo '<span class="usof-nav-icon" style="background-image: url(' . $section['icon'] . ')"></span>';
		echo '<span class="usof-nav-title">' . $section['title'] . '</span>';
		echo '<span class="usof-nav-arrow"></span>';
		echo '</a>';
		if ( isset( $section['new'] ) AND $section['new'] AND ! in_array( $section_id, $visited_new_sections ) ) {
			echo '<span class="usof-nav-popup">' . __( 'New', 'us' ) . '</span>';
		}
		echo '</li>';
	}
	echo '<ul></div>';

	// Content
	echo '<div class="usof-content">';
	foreach ( $config as $section_id => &$section ) {
		if ( isset( $section['place_if'] ) AND ! $section['place_if'] ) {
			if ( isset( $section['fields'] ) ) {
				$hidden_fields_values = array_merge( $hidden_fields_values, array_intersect_key( $usof_options, $section['fields'] ) );
			}
			continue;
		}
		echo '<section class="usof-section ' . ( ( $section_id == $active_section ) ? 'current' : '' ) . '" data-id="' . $section_id . '">';
		echo '<div class="usof-section-header" data-id="' . $section_id . '">';
		echo '<h3>' . $section['title'] . '</h3><span class="usof-section-header-control"></span></div>';
		echo '<div class="usof-section-content" style="display: ' . ( ( $section_id == $active_section ) ? 'block' : 'none' ) . '">';
		if ( isset( $section['fields'] ) ) {
			foreach ( $section['fields'] as $field_name => &$field ) {
				if ( isset( $field['place_if'] ) AND ! $field['place_if'] ) {
					if ( isset( $usof_options[ $field_name ] ) ) {
						$hidden_fields_values[ $field_name ] = $usof_options[ $field_name ];
					}
					continue;
				}
				us_load_template( 'vendor/usof/templates/field', array(
					'name' => $field_name,
					'id' => 'usof_' . $field_name,
					'field' => $field,
					'values' => &$usof_options,
				) );
				unset( $hidden_fields_values[ $field_name ] );
			}
		}
		echo '</div></section>';
	}
	echo '</div>';

	echo '</form>';
	echo '</div>';

	echo '</div>';
	echo '<div class="usof-hidden-fields"' . us_pass_data_to_js( $hidden_fields_values ) . '></div>';
}

function usof_print_scripts() {
	global $usof_directory_uri, $usof_version;
	if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
	wp_enqueue_script( 'usof-colorpicker', $usof_directory_uri . '/js/colpick.js', array( 'jquery' ), '1.0', TRUE );
	wp_enqueue_script( 'usof-select2', $usof_directory_uri . '/js/select2.min.js', array( 'jquery' ), '4.0', TRUE );
	wp_enqueue_script( 'usof-scripts', $usof_directory_uri . '/js/usof.js', array( 'jquery' ), $usof_version, TRUE );
	do_action( 'usof_print_scripts' );
}

function usof_print_styles() {
	global $usof_directory_uri, $usof_version, $us_template_directory_uri;
	wp_enqueue_style( 'font-awesome', $us_template_directory_uri . '/framework/css/font-awesome.css', array(), FALSE, 'all' );
	wp_enqueue_style( 'usof-select2', $usof_directory_uri . '/css/select2.css', array(), '4.0' );
	wp_enqueue_style( 'usof-styles', $usof_directory_uri . '/css/usof.css', array(), $usof_version );
	do_action( 'usof_print_styles' );
}

function usof_print_fonts() {
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

function usof_hide_admin_notices_start() {
	?><div class="usof-hide-notices"><?php
}

function usof_hide_admin_notices_end() {
	?></div><?php
}
