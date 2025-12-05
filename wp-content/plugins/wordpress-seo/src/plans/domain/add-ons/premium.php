<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Plans\Domain\Add_Ons;

use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Plans\Infrastructure\Add_Ons\Managed_Add_On;

/**
 * The Yoast Premium SEO add-on.
 */
class Premium extends Managed_Add_On implements Add_On_Interface {

	/**
	 * The slug of the add-on.
	 *
	 * @var string
	 */
	protected const SLUG = WPSEO_Addon_Manager::PREMIUM_SLUG;

	/**
	 * Returns the ID.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return 'premium';
	}

	/**
	 * Returns the click-to-buy action.
	 *
	 * @return string
	 */
	public function get_ctb_action(): string {
		return 'load-nfd-ctb';
	}

	/**
	 * Returns the click-to-buy ID.
	 *
	 * @return string
	 */
	public function get_ctb_id(): string {
		return 'f6a84663-465f-4cb5-8ba5-f7a6d72224b2';
	}
}
