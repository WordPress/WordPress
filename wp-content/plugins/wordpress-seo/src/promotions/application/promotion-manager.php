<?php

namespace Yoast\WP\SEO\Promotions\Application;

use Yoast\WP\SEO\Promotions\Domain\Promotion_Interface;

/**
 * Class to manage promotional promotions.
 *
 * @makePublic
 */
class Promotion_Manager implements Promotion_Manager_Interface {

	/**
	 * The centralized list of promotions: all promotions should be passed to the constructor.
	 *
	 * @var array<Abstract_Promotion>
	 */
	private $promotions_list = [];

	/**
	 * Class constructor.
	 *
	 * @param Promotion_Interface ...$promotions List of promotions.
	 */
	public function __construct( Promotion_Interface ...$promotions ) {
		$this->promotions_list = $promotions;
	}

	/**
	 * Whether the promotion is effective.
	 *
	 * @param string $promotion_name The name of the promotion.
	 *
	 * @return bool Whether the promotion is effective.
	 */
	public function is( string $promotion_name ): bool {
		$time = \time();

		foreach ( $this->promotions_list as $promotion ) {
			if ( $promotion->get_promotion_name() === $promotion_name ) {
				return $promotion->get_time_interval()->contains( $time );
			}
		}

		return false;
	}

	/**
	 * Get the list of promotions.
	 *
	 * @return array<Abstract_Promotion> The list of promotions.
	 */
	public function get_promotions_list(): array {
		return $this->promotions_list;
	}

	/**
	 * Get the names of currently active promotions.
	 *
	 * @return array<string> The list of promotions.
	 */
	public function get_current_promotions(): array {
		$current_promotions = [];
		$time               = \time();
		foreach ( $this->promotions_list as $promotion ) {
			if ( $promotion->get_time_interval()->contains( $time ) ) {
				$current_promotions[] = $promotion->get_promotion_name();
			}
		}

		return $current_promotions;
	}
}
