<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Site\Sync;

use Piwik\Config as PiwikConfig;
use WpMatomo;
use WpMatomo\Bootstrap;
use WpMatomo\Logger;
use WpMatomo\ScheduledTasks;
use WpMatomo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class SyncConfig {

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( Settings $settings ) {
		$this->logger   = new Logger();
		$this->settings = $settings;
	}

	public function sync_config_for_current_site() {
		if ( $this->settings->is_network_enabled() ) {
			$config     = PiwikConfig::getInstance();
			$has_change = false;
			foreach ( $this->get_all() as $category => $keys ) {
				$cat = $config->{$category};
				if ( empty( $cat ) ) {
					$cat = [];
				}

				if ( empty( $keys ) && ! empty( $cat ) ) {
					// need to unset all values
					$has_change          = true;
					$config->{$category} = [];
				}

				if ( ! empty( $keys ) ) {
					foreach ( $keys as $key => $value ) {
						// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
						if ( ! isset( $cat[ $key ] ) || $cat[ $key ] != $value ) {
							$has_change          = true;
							$cat[ $key ]         = $value;
							$config->{$category} = $cat;
						}
					}
				}
			}
			if ( $has_change ) {
				$config->forceSave();
			}
		}
	}

	private function get_all() {
		$options = $this->settings->get_global_option( Settings::NETWORK_CONFIG_OPTIONS );

		if ( empty( $options ) || ! is_array( $options ) ) {
			$options = [];
		}

		return $options;
	}

	public function get_config_value( $group, $key ) {
		if ( $this->settings->is_network_enabled() ) {
			$config = $this->get_all();
			if ( isset( $config[ $group ][ $key ] ) ) {
				return $config[ $group ][ $key ];
			}
		} else {
			Bootstrap::do_bootstrap();
			$config    = PiwikConfig::getInstance();
			$the_group = $config->{$group};
			if ( ! empty( $the_group ) && isset( $the_group[ $key ] ) ) {
				return $the_group[ $key ];
			}
		}
	}

	public function set_config_value( $group, $key, $value ) {
		if ( $this->settings->is_network_enabled() ) {
			$config = $this->get_all();

			if ( ! isset( $config[ $group ] ) ) {
				$config[ $group ] = [];
			}
			$config[ $group ][ $key ] = $value;

			$this->settings->apply_changes(
				[
					Settings::NETWORK_CONFIG_OPTIONS => $config,
				]
			);
			// need to update all config files
			wp_schedule_single_event( time() + 5, ScheduledTasks::EVENT_SYNC );
		} elseif ( ! WpMatomo::is_safe_mode() ) {
			Bootstrap::do_bootstrap();
			$config    = PiwikConfig::getInstance();
			$the_group = $config->{$group};
			if ( empty( $the_group ) ) {
				$the_group = [];
			}
			$the_group[ $key ] = $value;
			$config->{$group}  = $the_group;
			$config->forceSave();
		}
	}
}
