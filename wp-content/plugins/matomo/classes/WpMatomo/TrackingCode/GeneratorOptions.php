<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\TrackingCode;

use WpMatomo\Settings;

class GeneratorOptions {

	/**
	 * @var Settings|null
	 */
	private $settings;

	/**
	 * @var array
	 */
	private $options_override;

	public function __construct( Settings $settings, $options_override = [] ) {
		$this->settings         = $settings;
		$this->options_override = $options_override;
	}

	public function get_track_datacfasync() {
		return $this->get_option( 'track_datacfasync' );
	}

	public function get_track_content() {
		return $this->get_option( 'track_content' );
	}

	public function get_track_heartbeat() {
		return $this->get_option( 'track_heartbeat' );
	}

	public function get_limit_cookies() {
		return $this->get_option( 'limit_cookies' );
	}

	public function get_limit_cookies_visitor() {
		return $this->get_option( 'limit_cookies_visitor' );
	}

	public function get_limit_cookies_session() {
		return $this->get_option( 'limit_cookies_session' );
	}

	public function get_limit_cookies_referral() {
		return $this->get_option( 'limit_cookies_referral' );
	}

	public function get_cookie_consent() {
		return $this->get_option( 'cookie_consent' );
	}

	public function get_force_post() {
		return $this->get_option( 'force_post' );
	}

	public function get_track_across_alias() {
		return $this->get_option( 'track_across_alias' );
	}

	public function get_track_across() {
		return $this->get_option( 'track_across' );
	}

	public function get_track_crossdomain_linking() {
		return $this->get_option( 'track_crossdomain_linking' );
	}

	public function get_track_jserrors() {
		return $this->get_option( 'track_jserrors' );
	}

	public function get_disable_cookies() {
		return $this->get_option( 'disable_cookies' );
	}

	public function get_set_link_classes() {
		return $this->get_option( 'set_link_classes' );
	}

	public function get_set_download_classes() {
		return $this->get_option( 'set_download_classes' );
	}

	public function get_add_download_extensions() {
		return $this->get_option( 'add_download_extensions' );
	}

	public function get_track_api_endpoint() {
		return $this->get_option( 'track_api_endpoint' );
	}

	public function get_force_protocol() {
		return $this->get_option( 'force_protocol' );
	}

	public function get_track_js_endpoint() {
		return $this->get_option( 'track_js_endpoint' );
	}

	public function get_set_download_extensions() {
		return $this->get_option( 'set_download_extensions' );
	}

	private function get_option( $option_name ) {
		if ( isset( $this->options_override[ $option_name ] ) ) {
			return $this->options_override[ $option_name ];
		}

		return $this->settings->get_global_option( $option_name );
	}
}
