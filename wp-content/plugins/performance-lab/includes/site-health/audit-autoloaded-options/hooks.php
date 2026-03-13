<?php
/**
 * Hook callbacks used for Autoloaded Options Health Check.
 *
 * @package performance-lab
 * @since 2.1.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

/**
 * Adds test to site health.
 *
 * @since 1.0.0
 *
 * @param array{direct: array<string, array{label: string, test: string}>} $tests Site Health Tests.
 * @return array{direct: array<string, array{label: string, test: string}>} Amended tests.
 */
function perflab_aao_add_autoloaded_options_test( array $tests ): array {
	// Bail early if check already registered in WordPress core version 6.6.
	if ( isset( $tests['direct']['autoloaded_options'] ) ) {
		return $tests;
	}

	$tests['direct']['autoloaded_options'] = array(
		'label' => __( 'Autoloaded options', 'performance-lab' ),
		'test'  => 'perflab_aao_autoloaded_options_test',
	);
	return $tests;
}
add_filter( 'site_status_tests', 'perflab_aao_add_autoloaded_options_test' );

/**
 * Register admin actions for handling autoload enable/disable.
 *
 * @since 3.0.0
 */
function perflab_aao_register_admin_actions(): void {
	add_action( 'admin_action_perflab_aao_update_autoload', 'perflab_aao_handle_update_autoload' );
}
add_action( 'admin_init', 'perflab_aao_register_admin_actions' );

/**
 * Callback for handling disable autoload action.
 *
 * @since 3.0.0
 */
function perflab_aao_handle_update_autoload(): void {
	check_admin_referer( 'perflab_aao_update_autoload' );

	if ( ! isset( $_GET['option_name'], $_GET['autoload'] ) ) {
		wp_die( esc_html__( 'Missing required parameter.', 'performance-lab' ) );
	}

	$option_name = sanitize_text_field( wp_unslash( $_GET['option_name'] ) );
	$autoload    = rest_sanitize_boolean( wp_unslash( $_GET['autoload'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Won't be needed after <https://github.com/WordPress/WordPress-Coding-Standards/pull/2530>.

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'performance-lab' ) );
	}

	if ( '' === $option_name ) {
		wp_die( esc_html__( 'Invalid option name.', 'performance-lab' ) );
	}

	// Check if the option exists.
	if ( false === get_option( $option_name ) ) {
		// Option doesn't exist, return an error or handle the situation accordingly.
		wp_die( esc_html__( 'The option does not exist.', 'performance-lab' ) );
	}

	$result = wp_set_option_autoload( $option_name, $autoload );

	if ( $result ) {
		// Update disabled options list.
		$disabled_options = get_option( 'perflab_aao_disabled_options', array() );

		$key = array_search( $option_name, $disabled_options, true );
		if ( ! $autoload && false === $key ) {
			$disabled_options[] = $option_name;
		} elseif ( $autoload && false !== $key ) {
			unset( $disabled_options[ $key ] );
		}

		update_option( 'perflab_aao_disabled_options', $disabled_options );

		if ( wp_safe_redirect( admin_url( 'site-health.php?autoload_updated=true' ) ) ) {
			exit;
		}
	} else {
		wp_die( esc_html__( 'Failed to disable autoload.', 'performance-lab' ) );
	}
}

/**
 * Callback function hooked to admin_notices to render admin notices on the site health screen.
 *
 * @since 3.0.0
 *
 * @global string $pagenow The filename of the current screen.
 */
function perflab_aao_admin_notices(): void {
	if ( 'site-health.php' !== $GLOBALS['pagenow'] ) {
		return;
	}

	if ( isset( $_GET['autoload_updated'] ) && 'true' === $_GET['autoload_updated'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_admin_notice(
			esc_html__( 'The option has been successfully updated.', 'performance-lab' ),
			array(
				'type' => 'success',
			)
		);
	}
}
add_action( 'admin_notices', 'perflab_aao_admin_notices' );

/**
 * Extends the health check description that merged in WordPress 6.6.
 *
 * @since 3.3.0
 *
 * @param string $description Description message when autoloaded options bigger than threshold.
 * @return string Extended health check description.
 */
function perflab_aao_extend_core_check( string $description ): string {
	return $description . perflab_aao_get_autoloaded_options_table() . perflab_aao_get_disabled_autoloaded_options_table();
}
add_filter( 'site_status_autoloaded_options_limit_description', 'perflab_aao_extend_core_check' );

/**
 * Filters the list of disabled options to exclude options that are autoloaded.
 *
 * This filter modifies the 'option_perflab_aao_disabled_options' to ensure
 * that autoloaded options are not included in the disabled options list.
 *
 * @since 3.4.0
 *
 * @param string[]|mixed $disabled_options Array of disabled options.
 * @return string[] Filtered array of disabled options excluding autoloaded options.
 */
function perflab_filter_option_perflab_aao_disabled_options( $disabled_options ): array {
	$autoload_option_names = wp_list_pluck( perflab_aao_query_autoloaded_options(), 'option_name' );
	return array_filter(
		(array) $disabled_options,
		static function ( $option ) use ( $autoload_option_names ): bool {
			return ! in_array( $option, $autoload_option_names, true );
		}
	);
}
add_filter( 'option_perflab_aao_disabled_options', 'perflab_filter_option_perflab_aao_disabled_options' );
