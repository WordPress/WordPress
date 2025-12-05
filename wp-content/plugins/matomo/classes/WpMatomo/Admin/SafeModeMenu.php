<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use WpMatomo;
use WpMatomo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class SafeModeMenu {
	/**
	 * @var Settings
	 */
	private $settings;

	private $parent_slug = 'matomo';

	/**
	 * @param Settings $settings
	 */
	public function __construct( $settings ) {
		$this->settings = $settings;
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'network_admin_menu', [ $this, 'add_menu' ] );
	}

	public function add_menu() {
		if ( ! WpMatomo::is_admin_user() ) {
			return;
		}

		$system_report = new SystemReport( $this->settings );

		add_menu_page( 'Matomo Analytics', 'Matomo Analytics', Menu::CAP_NOT_EXISTS, 'matomo', null, 'dashicons-analytics' );

		add_submenu_page(
			$this->parent_slug,
			__( 'System Report', 'matomo' ),
			__( 'System Report', 'matomo' ),
			'administrator',
			Menu::SLUG_SYSTEM_REPORT,
			[
				$system_report,
				'show',
			]
		);
	}
}
