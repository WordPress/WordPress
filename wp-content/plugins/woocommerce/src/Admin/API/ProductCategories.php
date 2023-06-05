<?php
/**
 * REST API Product Categories Controller
 *
 * Handles requests to /products/categories.
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

/**
 * Product categories controller.
 *
 * @internal
 * @extends WC_REST_Product_Categories_Controller
 */
class ProductCategories extends \WC_REST_Product_Categories_Controller {
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-analytics';
}
