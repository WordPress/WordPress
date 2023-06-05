<?php
/**
 * Customize Site Health recommendations for WooCommerce.
 */

namespace Automattic\WooCommerce\Internal\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * SiteHealth class.
 */
class SiteHealth {
	/**
	 * Class instance.
	 *
	 * @var SiteHealth instance
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
		add_filter( 'site_status_should_suggest_persistent_object_cache', array( $this, 'should_suggest_persistent_object_cache' ) );
	}

	/**
	 * Counts specific types of WooCommerce entities to determine if a persistent object cache would be beneficial.
	 *
	 * Note that if all measured WooCommerce entities are below their thresholds, this will return null so that the
	 * other normal WordPress checks will still be run.
	 *
	 * @param true|null $check A non-null value will short-circuit WP's normal tests for this.
	 *
	 * @return true|null True if the store would benefit from a persistent object cache. Otherwise null.
	 */
	public function should_suggest_persistent_object_cache( $check ) {
		// Skip this if some other filter has already determined yes.
		if ( true === $check ) {
			return $check;
		}

		$thresholds = array(
			'orders'   => 100,
			'products' => 100,
		);

		foreach ( $thresholds as $key => $threshold ) {
			try {
				switch ( $key ) {
					case 'orders':
						$orders_query   = new \WC_Order_Query(
							array(
								'status'   => 'any',
								'limit'    => 1,
								'paginate' => true,
								'return'   => 'ids',
							)
						);
						$orders_results = $orders_query->get_orders();
						if ( $orders_results->total >= $threshold ) {
							$check = true;
						}
						break;

					case 'products':
						$products_query   = new \WC_Product_Query(
							array(
								'status'   => 'any',
								'limit'    => 1,
								'paginate' => true,
								'return'   => 'ids',
							)
						);
						$products_results = $products_query->get_products();
						if ( $products_results->total >= $threshold ) {
							$check = true;
						}
						break;
				}
			} catch ( \Exception $exception ) {
				break;
			}

			if ( ! is_null( $check ) ) {
				break;
			}
		}

		return $check;
	}
}
