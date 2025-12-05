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

class GetStarted {
	const NONCE_NAME = 'matomo_enable_tracking';
	const FORM_NAME  = 'matomo';

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * @param Settings $settings
	 */
	public function __construct( $settings ) {
		$this->settings = $settings;
	}

	public function show() {
		$was_updated    = $this->update_if_submitted();
		$settings       = $this->settings;
		$can_user_edit  = $this->can_user_manage();
		$show_this_page = $this->settings->get_global_option( Settings::SHOW_GET_STARTED_PAGE );

		include dirname( __FILE__ ) . '/views/get_started.php';
	}

	private function update_if_submitted() {
		if ( isset( $_POST )
			 && ! empty( $_POST[ self::FORM_NAME ] )
			 && is_admin()
			 && check_admin_referer( self::NONCE_NAME )
			 && $this->can_user_manage() ) {
			if ( ! empty( $_POST[ self::FORM_NAME ][ Settings::SHOW_GET_STARTED_PAGE ] )
				 && 'no' === $_POST[ self::FORM_NAME ][ Settings::SHOW_GET_STARTED_PAGE ] ) {
				$this->settings->apply_changes(
					[
						Settings::SHOW_GET_STARTED_PAGE => 0,
					]
				);

				return true;
			}
			if ( ! empty( $_POST[ self::FORM_NAME ]['track_mode'] )
				 && TrackingSettings::TRACK_MODE_DEFAULT === $_POST[ self::FORM_NAME ]['track_mode'] ) {
				$this->settings->apply_tracking_related_changes( [ 'track_mode' => TrackingSettings::TRACK_MODE_DEFAULT ] );

				return true;
			}
		}

		return false;
	}

	public function can_user_manage() {
		$tracking_settings = new TrackingSettings( $this->settings );

		return $tracking_settings->can_user_manage();
	}
}
