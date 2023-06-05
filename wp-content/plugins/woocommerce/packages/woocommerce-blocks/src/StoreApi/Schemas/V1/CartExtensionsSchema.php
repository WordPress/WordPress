<?php
namespace Automattic\WooCommerce\StoreApi\Schemas\V1;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\SchemaController;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Utilities\CartController;

/**
 * Class CartExtensionsSchema
 */
class CartExtensionsSchema extends AbstractSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'cart-extensions';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-extensions';

	/**
	 * Cart schema instance.
	 *
	 * @var CartSchema
	 */
	public $cart_schema;

	/**
	 * Constructor.
	 *
	 * @param ExtendSchema     $extend Rest Extending instance.
	 * @param SchemaController $controller Schema Controller instance.
	 */
	public function __construct( ExtendSchema $extend, SchemaController $controller ) {
		parent::__construct( $extend, $controller );
		$this->cart_schema = $this->controller->get( CartSchema::IDENTIFIER );
	}

	/**
	 * Cart extensions schema properties.
	 *
	 * @return array
	 */
	public function get_properties() {
		return [];
	}

	/**
	 * Handle the request and return a valid response for this endpoint.
	 *
	 * @param \WP_REST_Request $request Request containing data for the extension callback.
	 * @throws RouteException When callback is not callable or parameters are incorrect.
	 *
	 * @return array
	 */
	public function get_item_response( $request = null ) {
		try {
			$callback = $this->extend->get_update_callback( $request['namespace'] );
		} catch ( \Exception $e ) {
			throw new RouteException(
				'woocommerce_rest_cart_extensions_error',
				$e->getMessage(),
				400
			);
		}

		$controller = new CartController();

		if ( is_callable( $callback ) ) {
			$callback( $request['data'] );
			// We recalculate the cart if we had something to run.
			$controller->calculate_totals();
		}

		$cart = $controller->get_cart_instance();

		return rest_ensure_response( $this->cart_schema->get_item_response( $cart ) );
	}
}
