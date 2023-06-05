<?php
/**
 * WooCommerce Marketing.
 */

namespace Automattic\WooCommerce\Internal\Admin;

use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\Marketing\InstalledExtensions;
use Automattic\WooCommerce\Admin\PageController;

/**
 * Contains backend logic for the Marketing feature.
 */
class Marketing {

	use CouponsMovedTrait;

	/**
	 * Class instance.
	 *
	 * @var Marketing instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hook into WooCommerce.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register_pages' ), 5 );
		add_action( 'admin_menu', array( $this, 'add_parent_menu_item' ), 6 );

		add_filter( 'woocommerce_admin_shared_settings', array( $this, 'component_settings' ), 30 );
	}

	/**
	 * Add main marketing menu item.
	 *
	 * Uses priority of 9 so other items can easily be added at the default priority (10).
	 */
	public function add_parent_menu_item() {
		if ( ! Features::is_enabled( 'navigation' ) ) {
			add_menu_page(
				__( 'Marketing', 'woocommerce' ),
				__( 'Marketing', 'woocommerce' ),
				'manage_woocommerce',
				'woocommerce-marketing',
				null,
				'dashicons-megaphone',
				58
			);
		}

		PageController::get_instance()->connect_page(
			[
				'id'         => 'woocommerce-marketing',
				'title'      => 'Marketing',
				'capability' => 'manage_woocommerce',
				'path'       => 'wc-admin&path=/marketing',
			]
		);
	}

	/**
	 * Registers report pages.
	 */
	public function register_pages() {
		$this->register_overview_page();

		$controller = PageController::get_instance();
		$defaults   = [
			'parent'        => 'woocommerce-marketing',
			'existing_page' => false,
		];

		$marketing_pages = apply_filters( 'woocommerce_marketing_menu_items', [] );
		foreach ( $marketing_pages as $marketing_page ) {
			if ( ! is_array( $marketing_page ) ) {
				continue;
			}

			$marketing_page = array_merge( $defaults, $marketing_page );

			if ( $marketing_page['existing_page'] ) {
				$controller->connect_page( $marketing_page );
			} else {
				$controller->register_page( $marketing_page );
			}
		}
	}

	/**
	 * Register the main Marketing page, which is Marketing > Overview.
	 *
	 * This is done separately because we need to ensure the page is registered properly and
	 * that the link is done properly. For some reason the normal page registration process
	 * gives us the wrong menu link.
	 */
	protected function register_overview_page() {
		global $submenu;

		// First register the page.
		PageController::get_instance()->register_page(
			[
				'id'       => 'woocommerce-marketing-overview',
				'title'    => __( 'Overview', 'woocommerce' ),
				'path'     => 'wc-admin&path=/marketing',
				'parent'   => 'woocommerce-marketing',
				'nav_args' => array(
					'parent' => 'woocommerce-marketing',
					'order'  => 10,
				),
			]
		);

		// Now fix the path, since register_page() gets it wrong.
		if ( ! isset( $submenu['woocommerce-marketing'] ) ) {
			return;
		}

		foreach ( $submenu['woocommerce-marketing'] as &$item ) {
			// The "slug" (aka the path) is the third item in the array.
			if ( 0 === strpos( $item[2], 'wc-admin' ) ) {
				$item[2] = 'admin.php?page=' . $item[2];
			}
		}
	}

	/**
	 * Add settings for marketing feature.
	 *
	 * @param array $settings Component settings.
	 * @return array
	 */
	public function component_settings( $settings ) {
		// Bail early if not on a wc-admin powered page.
		if ( ! PageController::is_admin_page() ) {
			return $settings;
		}

		$settings['marketing']['installedExtensions'] = InstalledExtensions::get_data();

		return $settings;
	}
}
