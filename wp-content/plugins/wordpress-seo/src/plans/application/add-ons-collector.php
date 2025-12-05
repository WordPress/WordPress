<?php

namespace Yoast\WP\SEO\Plans\Application;

use Yoast\WP\SEO\Plans\Domain\Add_Ons\Add_On_Interface;

/**
 * The collector to get add-ons.
 */
class Add_Ons_Collector {

	/**
	 * All add-ons.
	 *
	 * @var array<Add_On_Interface>
	 */
	private $add_ons;

	/**
	 * Constructs the instance.
	 *
	 * @param Add_On_Interface ...$add_ons All add-ons.
	 */
	public function __construct( Add_On_Interface ...$add_ons ) {
		$this->add_ons = $add_ons;
	}

	/**
	 * Returns all the add-ons.
	 *
	 * @return array<Add_On_Interface> All the add-ons.
	 */
	public function get(): array {
		return $this->add_ons;
	}

	/**
	 * Returns the data for the add-ons in an array format.
	 *
	 * @return array<string, string|bool|array<string, string>> The add-ons in an array format.
	 */
	public function to_array(): array {
		$result = [];
		foreach ( $this->add_ons as $add_on ) {
			$result[ $add_on->get_id() ] = [
				'id'         => $add_on->get_id(),
				'isActive'   => $add_on->is_active(),
				'hasLicense' => $add_on->has_license(),
				'ctb'        => [
					'action' => $add_on->get_ctb_action(),
					'id'     => $add_on->get_ctb_id(),
				],
			];
		}

		return $result;
	}
}
