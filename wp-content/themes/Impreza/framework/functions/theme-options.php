<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options: USOF + UpSolution extendings
 *
 * Should be included in global context.
 */

add_action( 'usof_after_save', 'us_generate_theme_options_css_file' );
function us_generate_theme_options_css_file() {

	global $usof_options;

	// Just in case the function was called separately, ex. from migrations file
	usof_load_options_once();

	if ( ! isset( $usof_options['generate_css_file'] ) OR ! $usof_options['generate_css_file'] ) {
		return;
	}

	// TODO Use WP_Filesystem instead
	$wp_upload_dir = wp_upload_dir();
	$styles_dir = wp_normalize_path( $wp_upload_dir['basedir'] . '/us-assets' );
	$site_url_parts = parse_url(site_url());
	$styles_file_suffix = ( ! empty( $site_url_parts['host'] ) ) ? $site_url_parts['host'] : '';
	$styles_file_suffix .= ( ! empty( $site_url_parts['path'] ) ) ? str_replace( '/', '_', $site_url_parts['host'] ) : '';
	$styles_file_suffix = ( ! empty( $styles_file_suffix ) ) ? '-' . $styles_file_suffix : '';
	$styles_file = $styles_dir . '/' . US_THEMENAME . $styles_file_suffix . '-theme-options.css';
	global $output_styles_to_file;
	$output_styles_to_file = TRUE;

	$styles_css = us_get_template( 'templates/theme-options.min.css' );

	if ( ! is_dir( $styles_dir ) ) {
		wp_mkdir_p( trailingslashit( $styles_dir ) );
	}
	$handle = @fopen( $styles_file, 'w' );
	if ( $handle ) {
		if ( ! fwrite( $handle, $styles_css ) ) {
			return FALSE;
		}
		fclose( $handle );

		return TRUE;
	}

	return FALSE;
}

// Flushing WP rewrite rules on portfolio slug changes
add_action( 'usof_before_save', 'us_maybe_flush_rewrite_rules' );
add_action( 'usof_after_save', 'us_maybe_flush_rewrite_rules' );
function us_maybe_flush_rewrite_rules( $updated_options ) {
	// The function is called twice: before and after options change
	static $old_portfolio_slug = NULL;
	static $old_portfolio_category_slug = NULL;
	$flush_rules = FALSE;
	if ( ! isset($updated_options['portfolio_slug']) ) {
		$updated_options['portfolio_slug'] = NULL;
	}
	if ( ! isset($updated_options['portfolio_category_slug']) ) {
		$updated_options['portfolio_category_slug'] = NULL;
	}
	if ( $old_portfolio_slug === NULL ) {
		// At first call we're storing the previous portfolio slug
		$old_portfolio_slug = us_get_option( 'portfolio_slug', 'portfolio' );
	} elseif ( $old_portfolio_slug != $updated_options['portfolio_slug'] ) {
		// At second call we're triggering flush rewrite rules at the next app execution
		// We're using transients to reduce the number of excess auto-loaded options
		$flush_rules = TRUE;
	}
	if ( $old_portfolio_category_slug === NULL ) {
		// At first call we're storing the previous portfolio slug
		$old_portfolio_category_slug = us_get_option( 'portfolio_category_slug', 'portfolio_category' );
	} elseif ( $old_portfolio_slug != $updated_options['portfolio_category_slug'] ) {
		// At second call we're triggering flush rewrite rules at the next app execution
		// We're using transients to reduce the number of excess auto-loaded options
		$flush_rules = TRUE;
	}

	if ($flush_rules) {
		set_transient( 'us_flush_rules', TRUE, DAY_IN_SECONDS );
	}
}

// Using USOF for theme options
$usof_directory = $us_template_directory . '/framework/vendor/usof';
$usof_directory_uri = $us_template_directory_uri . '/framework/vendor/usof';
$usof_version = US_THEMEVERSION;
require $us_template_directory . '/framework/vendor/usof/usof.php';
