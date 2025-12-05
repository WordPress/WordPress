<?php
namespace Elementor\Core\Admin;

use Elementor\Api;
use Elementor\Core\Base\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * TODO: Move this class to pro version for better architecture.
 */
class Canary_Deployment extends Module {

	const CURRENT_VERSION = ELEMENTOR_VERSION;
	const PLUGIN_BASE = ELEMENTOR_PLUGIN_BASE;

	private $canary_deployment_info = null;

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @since  2.6.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'canary-deployment';
	}

	/**
	 * Check version.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $transient Plugin updates data.
	 *
	 * @return object Plugin updates data.
	 */
	public function check_version( $transient ) {
		// First transient before the real check.
		if ( ! isset( $transient->response ) ) {
			return $transient;
		}

		// Placeholder
		$stable_version = '0.0.0';

		if ( ! empty( $transient->response[ static::PLUGIN_BASE ]->new_version ) ) {
			$stable_version = $transient->response[ static::PLUGIN_BASE ]->new_version;
		}

		if ( null === $this->canary_deployment_info ) {
			$this->canary_deployment_info = $this->get_canary_deployment_info();
		}

		// Can be false - if canary version is not available.
		if ( empty( $this->canary_deployment_info ) ) {
			return $transient;
		}

		if ( ! version_compare( $this->canary_deployment_info['new_version'], $stable_version, '>' ) ) {
			return $transient;
		}

		$canary_deployment_info = $this->canary_deployment_info;

		// Most of plugin info comes from the $transient but on first check - the response is empty.
		if ( ! empty( $transient->response[ static::PLUGIN_BASE ] ) ) {
			$canary_deployment_info = array_merge( (array) $transient->response[ static::PLUGIN_BASE ], $canary_deployment_info );
		}

		$transient->response[ static::PLUGIN_BASE ] = (object) $canary_deployment_info;

		return $transient;
	}

	protected function get_canary_deployment_remote_info( $force ) {
		return Api::get_canary_deployment_info( $force );
	}

	private function get_canary_deployment_info() {
		global $pagenow;

		$force = 'update-core.php' === $pagenow && isset( $_GET['force-check'] );

		$canary_deployment = $this->get_canary_deployment_remote_info( $force );

		if ( empty( $canary_deployment['plugin_info']['new_version'] ) ) {
			return false;
		}

		$canary_version = $canary_deployment['plugin_info']['new_version'];

		if ( version_compare( $canary_version, static::CURRENT_VERSION, '<=' ) ) {
			return false;
		}

		if ( ! empty( $canary_deployment['conditions'] ) && ! $this->check_conditions( $canary_deployment['conditions'] ) ) {
			return false;
		}

		return $canary_deployment['plugin_info'];
	}

	private function check_conditions( $groups ) {
		foreach ( $groups as $group ) {
			if ( $this->check_group( $group ) ) {
				return true;
			}
		}

		return false;
	}

	private function check_group( $group ) {
		$is_or_relation = ! empty( $group['relation'] ) && 'OR' === $group['relation'];
		unset( $group['relation'] );
		$result = false;

		foreach ( $group as $condition ) {
			// Reset results for each condition.
			$result = false;
			switch ( $condition['type'] ) {
				case 'wordpress': // phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledInText
					// include an unmodified $wp_version
					include ABSPATH . WPINC . '/version.php';
					$result = version_compare( $wp_version, $condition['version'], $condition['operator'] );
					break;
				case 'multisite':
					$result = is_multisite() === $condition['multisite'];
					break;
				case 'language':
					$in_array = in_array( get_locale(), $condition['languages'], true );
					$result = 'in' === $condition['operator'] ? $in_array : ! $in_array;
					break;
				case 'plugin':
					if ( ! empty( $condition['plugin_file'] ) ) {
						$plugin_file = $condition['plugin_file']; // For PHP Unit tests.
					} else {
						$plugin_file = WP_PLUGIN_DIR . '/' . $condition['plugin']; // Default.
					}

					$version = '';

					if ( is_plugin_active( $condition['plugin'] ) && file_exists( $plugin_file ) ) {
						$plugin_data = get_plugin_data( $plugin_file );
						if ( isset( $plugin_data['Version'] ) ) {
							$version = $plugin_data['Version'];
						}
					}

					$result = version_compare( $version, $condition['version'], $condition['operator'] );
					break;
				case 'theme':
					$theme = wp_get_theme();
					if ( wp_get_theme()->parent() ) {
						$theme = wp_get_theme()->parent();
					}

					if ( $theme->get_template() === $condition['theme'] ) {
						$version = $theme->version;
					} else {
						$version = '';
					}

					$result = version_compare( $version, $condition['version'], $condition['operator'] );
					break;

			}

			if ( ( $is_or_relation && $result ) || ( ! $is_or_relation && ! $result ) ) {
				return $result;
			}
		}

		return $result;
	}

	/**
	 * @since 2.6.0
	 * @access public
	 */
	public function __construct() {
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_version' ] );
	}
}
