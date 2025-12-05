<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Plans\Domain\Add_Ons;

use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Plans\Infrastructure\Add_Ons\Managed_Add_On;

/**
 * The Yoast WooCommerce SEO add-on.
 */
class Woo extends Managed_Add_On implements Add_On_Interface {

	/**
	 * The slug of the add-on.
	 *
	 * @var string
	 */
	protected const SLUG = WPSEO_Addon_Manager::WOOCOMMERCE_SLUG;

	/**
	 * Returns the ID.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return 'woo';
	}

	/**
	 * Returns the click-to-buy action.
	 *
	 * @return string
	 */
	public function get_ctb_action(): string {
		return 'load-nfd-woo-ctb';
	}

	/**
	 * Returns the click-to-buy ID.
	 *
	 * @return string
	 */
	public function get_ctb_id(): string {
		return '5b32250e-e6f0-44ae-ad74-3cefc8e427f9';
	}
}
