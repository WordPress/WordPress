<?php

namespace Yoast\WP\SEO\Promotions\Application;

/**
 * Interface for the promotion manager.
 */
interface Promotion_Manager_Interface {

	/**
	 * Whether the promotion is effective.
	 *
	 * @param string $promotion_name The name of the promotion.
	 *
	 * @return bool Whether the promotion is effective.
	 */
	public function is( string $promotion_name ): bool;

	/**
	 * Get the list of promotions.
	 *
	 * @return array The list of promotions.
	 */
	public function get_promotions_list(): array;
}
