<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use WpMatomo\Access;
use WpMatomo\Capabilities;
use WpMatomo\Roles;
use WpMatomo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class AccessSettings implements AdminSettingsInterface {
	const NONCE_NAME = 'matomo_tracking';
	const FORM_NAME  = 'matomo_role';

	/**
	 * @var Access
	 */
	private $access;

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( Access $access, Settings $settings ) {
		$this->access   = $access;
		$this->settings = $settings;
	}

	public function get_title() {
		return esc_html__( 'Access', 'matomo' );
	}

	public function show_settings() {
		$this->update_if_submitted();

		$access      = $this->access;
		$roles       = new Roles( $this->settings );
		$capabilites = new Capabilities( $this->settings );
		include dirname( __FILE__ ) . '/views/access.php';
	}

	private function update_if_submitted() {
		if ( isset( $_POST )
			 && ! empty( $_POST[ self::FORM_NAME ] )
			 && is_admin()
			 && check_admin_referer( self::NONCE_NAME )
			 && current_user_can( Capabilities::KEY_SUPERUSER ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$this->access->save( wp_unslash( $_POST[ self::FORM_NAME ] ) );

			return true;
		}

		return false;
	}
}
