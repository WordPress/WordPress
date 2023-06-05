<?php
/**
 * Theme upgrader used in REST API response.
 */

namespace Automattic\WooCommerce\Admin\Overrides;

defined( 'ABSPATH' ) || exit;

/**
 * Admin\Overrides\ThemeUpgrader Class.
 */
class ThemeUpgrader extends \Theme_Upgrader {
	/**
	 * Install a theme package.
	 *
	 * @param string $package The full local path or URI of the package.
	 * @param array  $args {
	 *     Optional. Other arguments for installing a theme package. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the updates cache if successful.
	 *                                    Default true.
	 * }
	 *
	 * @return bool|WP_Error True if the installation was successful, false or a WP_Error object otherwise.
	 */
	public function install( $package, $args = array() ) {
		$defaults    = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->install_strings();

		add_filter( 'upgrader_source_selection', array( $this, 'check_package' ) );
		add_filter( 'upgrader_post_install', array( $this, 'check_parent_theme_filter' ), 10, 3 );
		if ( $parsed_args['clear_update_cache'] ) {
			// Clear cache so wp_update_themes() knows about the new theme.
			add_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9, 0 );
		}

		$result = $this->run(
			array(
				'package'           => $package,
				'destination'       => get_theme_root(),
				'clear_destination' => false, // Do not overwrite files.
				'clear_working'     => true,
				'hook_extra'        => array(
					'type'   => 'theme',
					'action' => 'install',
				),
			)
		);

		remove_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9 );
		remove_filter( 'upgrader_source_selection', array( $this, 'check_package' ) );
		remove_filter( 'upgrader_post_install', array( $this, 'check_parent_theme_filter' ) );

		if ( $result && ! is_wp_error( $result ) ) {
			// Refresh the Theme Update information.
			wp_clean_themes_cache( $parsed_args['clear_update_cache'] );
		}

		return $result;
	}
}
