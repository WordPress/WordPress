<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Discussed in Tech Council, a better solution is being worked on.

namespace Yoast\WP\SEO\Integrations\Admin\Addon_Installation;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Addon_Installation_Conditional;
use Yoast\WP\SEO\Conditionals\Admin\Licenses_Page_Conditional;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Represents the Addon installation feature.
 */
class Dialog_Integration implements Integration_Interface {

	/**
	 * The addon manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	protected $addon_manager;

	/**
	 * The addons.
	 *
	 * @var array
	 */
	protected $owned_addons;

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [
			Admin_Conditional::class,
			Licenses_Page_Conditional::class,
			Addon_Installation_Conditional::class,
		];
	}

	/**
	 * Addon_Installation constructor.
	 *
	 * @param WPSEO_Addon_Manager $addon_manager The addon manager.
	 */
	public function __construct( WPSEO_Addon_Manager $addon_manager ) {
		$this->addon_manager = $addon_manager;
	}

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'start_addon_installation' ] );
	}

	/**
	 * Starts the addon installation flow.
	 *
	 * @return void
	 */
	public function start_addon_installation() {
		// Only show the dialog when we explicitly want to see it.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: This is not a form.
		if ( ! isset( $_GET['install'] ) || $_GET['install'] !== 'true' ) {
			return;
		}

		$this->bust_myyoast_addon_information_cache();
		$this->owned_addons = $this->get_owned_addons();

		if ( \count( $this->owned_addons ) > 0 ) {
			\add_action( 'admin_enqueue_scripts', [ $this, 'show_modal' ] );
		}
		else {
			\add_action( 'admin_notices', [ $this, 'throw_no_owned_addons_warning' ] );
		}
	}

	/**
	 * Throws a no owned addons warning.
	 *
	 * @return void
	 */
	public function throw_no_owned_addons_warning() {
		echo '<div class="notice notice-warning"><p>'
			. \sprintf(
				/* translators: %1$s expands to Yoast SEO */
				\esc_html__(
					'No %1$s plugins have been installed. You don\'t seem to own any active subscriptions.',
					'wordpress-seo'
				),
				'Yoast SEO'
			)
			. '</p></div>';
	}

	/**
	 * Shows the modal.
	 *
	 * @return void
	 */
	public function show_modal() {
		\wp_localize_script(
			WPSEO_Admin_Asset_Manager::PREFIX . 'addon-installation',
			'wpseoAddonInstallationL10n',
			[
				'addons' => $this->owned_addons,
				'nonce'  => \wp_create_nonce( 'wpseo_addon_installation' ),
			]
		);

		$asset_manager = new WPSEO_Admin_Asset_Manager();
		$asset_manager->enqueue_script( 'addon-installation' );
	}

	/**
	 * Retrieves a list of owned addons for the site in MyYoast.
	 *
	 * @return array List of owned addons with slug as key and name as value.
	 */
	protected function get_owned_addons() {
		$owned_addons = [];

		foreach ( $this->addon_manager->get_myyoast_site_information()->subscriptions as $addon ) {
			$owned_addons[] = $addon->product->name;
		}

		return $owned_addons;
	}

	/**
	 * Bust the site information transients to have fresh data.
	 *
	 * @return void
	 */
	protected function bust_myyoast_addon_information_cache() {
		$this->addon_manager->remove_site_information_transients();
	}
}
