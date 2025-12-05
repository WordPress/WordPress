<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Plans\Infrastructure\Add_Ons;

use InvalidArgumentException;
use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Plans\Domain\Add_Ons\Add_On_Interface;

/**
 * Represents a managed add-on.
 * Uses the WPSEO_Addon_Manager to check if the add-on is installed and activated, and if it has a valid license.
 */
abstract class Managed_Add_On implements Add_On_Interface {

	/**
	 * The slug of the add-on.
	 *
	 * @var string
	 */
	protected const SLUG = '';

	/**
	 * Holds the WPSEO_Addon_Manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * Constructs the instance.
	 *
	 * @param WPSEO_Addon_Manager $addon_manager The WPSEO_Addon_Manager.
	 *
	 * @throws InvalidArgumentException If the slug is not set.
	 */
	public function __construct( WPSEO_Addon_Manager $addon_manager ) {
		if ( static::SLUG === '' ) {
			throw new InvalidArgumentException( 'The add-on slug must be set.' );
		}

		$this->addon_manager = $addon_manager;
	}

	/**
	 * Returns whether the add-on is installed and activated.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return $this->addon_manager->is_installed( static::SLUG );
	}

	/**
	 * Returns whether the add-on has an valid license.
	 *
	 * @return bool
	 */
	public function has_license(): bool {
		return $this->addon_manager->has_valid_subscription( static::SLUG );
	}
}
