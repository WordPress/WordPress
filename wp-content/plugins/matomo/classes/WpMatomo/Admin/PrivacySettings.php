<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use WpMatomo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class PrivacySettings implements AdminSettingsInterface {
	const EXAMPLE_MINIMAL = '[matomo_opt_out_form]';
	const EXAMPLE_FULL    = '[matomo_opt_out_form language=de]';

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function get_title() {
		return esc_html__( 'Privacy & GDPR', 'matomo' );
	}

	public function show_settings() {
		$matomo_settings = $this->settings;

		include dirname( __FILE__ ) . '/views/privacy_gdpr.php';
	}
}
