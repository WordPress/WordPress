<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use Piwik\IP;
use Piwik\Plugins\SitesManager\API;
use WpMatomo\Bootstrap;
use WpMatomo\Capabilities;
use WpMatomo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class ExclusionSettings implements AdminSettingsInterface {
	const NONCE_NAME = 'matomo_exclusion';
	const FORM_NAME  = 'matomo_exclusions';

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function get_title() {
		return esc_html__( 'Exclusions', 'matomo' );
	}

	public function show_settings( $throw_exception = false ) {
		global $wp_roles;
		$settings_errors = [];
		$was_updated     = false;
		try {
			$was_updated = $this->update_if_submitted();
		} catch ( InvalidIpException $e ) {
			$settings_errors[] = $e->getMessage();
			if ( $throw_exception ) {
				throw $e;
			}
		}

		Bootstrap::do_bootstrap();

		$api                   = API::getInstance();
		$excluded_ips          = $this->from_comma_list( $api->getExcludedIpsGlobal() );
		$excluded_query_params = $this->from_comma_list( $api->getExcludedQueryParametersGlobal() );
		$excluded_user_agents  = $this->from_comma_list( $api->getExcludedUserAgentsGlobal() );
		$keep_url_fragments    = $api->getKeepURLFragmentsGlobal();
		$current_ip            = $this->get_current_ip();
		$settings              = $this->settings;

		include dirname( __FILE__ ) . '/views/exclusion_settings.php';
	}

	private function update_if_submitted() {
		if ( isset( $_POST )
			 && ! empty( $_POST[ self::FORM_NAME ] )
			 && is_admin()
			 && check_admin_referer( self::NONCE_NAME )
			 && current_user_can( Capabilities::KEY_SUPERUSER ) ) {
			Bootstrap::do_bootstrap();
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$post = wp_unslash( $_POST[ self::FORM_NAME ] );

			$api = API::getInstance();
			if ( isset( $post['excluded_ips'] ) ) {
				$ips = $this->to_comma_list( $post['excluded_ips'] );
				if ( $ips !== $api->getExcludedIpsGlobal() ) {
					try {
						$api->setGlobalExcludedIps( $ips );
					} catch ( \Exception $e ) {
						throw new InvalidIpException( $e->getMessage() );
					}
				}
			}

			if ( isset( $post['excluded_query_parameters'] ) ) {
				$params = $this->to_comma_list( $post['excluded_query_parameters'] );
				if ( $params !== $api->getExcludedQueryParametersGlobal() ) {
					$api->setGlobalExcludedQueryParameters( $params );
				}
			}

			if ( isset( $post['excluded_user_agents'] ) ) {
				$useragents = $this->to_comma_list( $post['excluded_user_agents'] );
				if ( $useragents !== $api->getExcludedUserAgentsGlobal() ) {
					$api->setGlobalExcludedUserAgents( $useragents );
				}
			}

			$keep_fragments = ! empty( $post['keep_url_fragments'] );
			// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( $keep_fragments != $api->getKeepURLFragmentsGlobal() ) {
				$api->setKeepURLFragmentsGlobal( $keep_fragments );
			}

			$setting_values = [ Settings::OPTION_KEY_STEALTH => [] ];
			if ( ! empty( $post[ Settings::OPTION_KEY_STEALTH ] ) ) {
				$setting_values[ Settings::OPTION_KEY_STEALTH ] = $post[ Settings::OPTION_KEY_STEALTH ];
			}

			$this->settings->apply_changes( $setting_values );

			return true;
		}

		return false;
	}

	private function to_comma_list( $value ) {
		if ( empty( $value ) ) {
			return '';
		}
		$value = stripslashes( $value ); // WordPress adds slashes
		$value = str_replace( "\r", '', $value );

		return implode( ',', array_filter( explode( "\n", $value ) ) );
	}

	private function from_comma_list( $value ) {
		if ( empty( $value ) ) {
			return '';
		}

		return implode( "\n", array_filter( explode( ',', $value ) ) );
	}

	/**
	 * do not sanitize $_SERVER variables
	 * phpcs:disable WordPress.Security.ValidatedSanitizedInput
	 *
	 * @return mixed|string
	 */
	private function get_current_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = IP::getIpFromHeader();
		}

		return $ip;
	}
}
