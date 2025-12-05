<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use Plugin_Upgrader;
use WP_Error;
use WP_Upgrader;
use Yoast\WP\SEO\Conditionals\Check_Required_Version_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * The Check_Required_Version class.
 *
 * This class checks if the required version of Yoast SEO is installed.
 * It also adds the `Requires Yoast SEO` header to the list of headers and updates the comparison table for the plugin overwrite.
 */
class Check_Required_Version implements Integration_Interface {

	/**
	 * Initializes the integration.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'upgrader_source_selection', [ $this, 'check_required_version' ], 10, 3 );
		\add_filter( 'install_plugin_overwrite_comparison', [ $this, 'update_comparison_table' ], 10, 3 );
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return string[] The conditionals based on which this loadable should be active.
	 */
	public static function get_conditionals() {
		return [ Check_Required_Version_Conditional::class ];
	}

	/**
	 * Checks if the required version of Yoast SEO is installed.
	 *
	 * The code is partly inspired by Plugin_Upgrader::check_package() in wp-admin/includes/class-plugin-upgrader.php.
	 *
	 * @param string           $source        File source location.
	 * @param string|null      $remote_source Remote file source location.
	 * @param WP_Upgrader|null $upgrader      WP_Upgrader instance.
	 *
	 * @return string|WP_Error The source location or a WP_Error object if the required version is not installed.
	 */
	public function check_required_version( $source, $remote_source = null, $upgrader = null ) {
		global $wp_filesystem;

		// Bail out early if we are not installing/upgrading a plugin.
		if ( ! $upgrader instanceof Plugin_Upgrader ) {
			return $source;
		}

		$info = [];

		if ( \is_wp_error( $source ) ) {
			return $source;
		}

		$working_directory = \str_replace( $wp_filesystem->wp_content_dir(), \trailingslashit( \WP_CONTENT_DIR ), $source );
		if ( ! \is_dir( $working_directory ) ) { // Confidence check, if the above fails, let's not prevent installation.
			return $source;
		}

		// Check that the folder contains at least 1 valid plugin.
		$files = \glob( $working_directory . '*.php' );
		if ( $files ) {
			foreach ( $files as $file ) {
				$info = \get_plugin_data( $file, false, false );
				if ( ! empty( $info['Name'] ) ) {
					break;
				}
			}
		}

		$requires_yoast_seo = ! empty( $info['Requires Yoast SEO'] ) ? $info['Requires Yoast SEO'] : false;

		if ( ! $this->check_requirement( $requires_yoast_seo ) ) {
			$error = \sprintf(
				/* translators: 1: Current Yoast SEO version, 2: Version required by the uploaded plugin. */
				\__( 'The Yoast SEO version on your site is %1$s, however the uploaded plugin requires %2$s.', 'wordpress-seo' ),
				\WPSEO_VERSION,
				\esc_html( $requires_yoast_seo )
			);

			return new WP_Error(
				'incompatible_yoast_seo_required_version',
				\__( 'The package could not be installed because it\'s not supported by the currently installed Yoast SEO version.', 'wordpress-seo' ),
				$error
			);
		}

		return $source;
	}

	/**
	 * Update the comparison table for the plugin installation when overwriting an existing plugin.
	 *
	 * @param string        $table               The output table with Name, Version, Author, RequiresWP, and RequiresPHP info.
	 * @param array<string> $current_plugin_data Array with current plugin data.
	 * @param array<string> $new_plugin_data     Array with uploaded plugin data.
	 *
	 * @return string The updated comparison table.
	 */
	public function update_comparison_table( $table, $current_plugin_data, $new_plugin_data ) {
		$requires_yoast_seo_current = ! empty( $current_plugin_data['Requires Yoast SEO'] ) ? $current_plugin_data['Requires Yoast SEO'] : false;
		$requires_yoast_seo_new     = ! empty( $new_plugin_data['Requires Yoast SEO'] ) ? $new_plugin_data['Requires Yoast SEO'] : false;

		if ( $requires_yoast_seo_current !== false || $requires_yoast_seo_new !== false ) {
			$new_row = \sprintf(
				'<tr><td class="name-label">%1$s</td><td>%2$s</td><td>%3$s</td></tr>',
				\__( 'Required Yoast SEO version', 'wordpress-seo' ),
				( $requires_yoast_seo_current !== false ) ? \esc_html( $requires_yoast_seo_current ) : '-',
				( $requires_yoast_seo_new !== false ) ? \esc_html( $requires_yoast_seo_new ) : '-'
			);

			$table = \str_replace( '</tbody>', $new_row . '</tbody>', $table );
		}

		return $table;
	}

	/**
	 * Check whether the required Yoast SEO version is installed.
	 *
	 * @param string|bool $required_version The required version.
	 *
	 * @return bool Whether the required version is installed, or no version is required.
	 */
	private function check_requirement( $required_version ) {
		if ( $required_version === false ) {
			return true;
		}

		return \version_compare( \WPSEO_VERSION, $required_version . '-RC0', '>=' );
	}
}
