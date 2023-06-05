<?php
namespace Automattic\WooCommerce\StoreApi;

use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;

/**
 * SchemaController class.
 */
class SchemaController {

	/**
	 * Stores schema class instances.
	 *
	 * @var Schemas\V1\AbstractSchema[]
	 */
	protected $schemas = [];

	/**
	 * Stores Rest Extending instance
	 *
	 * @var ExtendSchema
	 */
	private $extend;

	/**
	 * Constructor.
	 *
	 * @param ExtendSchema $extend Rest Extending instance.
	 */
	public function __construct( ExtendSchema $extend ) {
		$this->extend  = $extend;
		$this->schemas = [
			'v1' => [
				Schemas\V1\BatchSchema::IDENTIFIER         => Schemas\V1\BatchSchema::class,
				Schemas\V1\ErrorSchema::IDENTIFIER         => Schemas\V1\ErrorSchema::class,
				Schemas\V1\ImageAttachmentSchema::IDENTIFIER => Schemas\V1\ImageAttachmentSchema::class,
				Schemas\V1\TermSchema::IDENTIFIER          => Schemas\V1\TermSchema::class,
				Schemas\V1\BillingAddressSchema::IDENTIFIER => Schemas\V1\BillingAddressSchema::class,
				Schemas\V1\ShippingAddressSchema::IDENTIFIER => Schemas\V1\ShippingAddressSchema::class,
				Schemas\V1\CartShippingRateSchema::IDENTIFIER => Schemas\V1\CartShippingRateSchema::class,
				Schemas\V1\CartCouponSchema::IDENTIFIER    => Schemas\V1\CartCouponSchema::class,
				Schemas\V1\CartFeeSchema::IDENTIFIER       => Schemas\V1\CartFeeSchema::class,
				Schemas\V1\CartItemSchema::IDENTIFIER      => Schemas\V1\CartItemSchema::class,
				Schemas\V1\CartSchema::IDENTIFIER          => Schemas\V1\CartSchema::class,
				Schemas\V1\CartExtensionsSchema::IDENTIFIER => Schemas\V1\CartExtensionsSchema::class,
				Schemas\V1\CheckoutSchema::IDENTIFIER      => Schemas\V1\CheckoutSchema::class,
				Schemas\V1\ProductSchema::IDENTIFIER       => Schemas\V1\ProductSchema::class,
				Schemas\V1\ProductAttributeSchema::IDENTIFIER => Schemas\V1\ProductAttributeSchema::class,
				Schemas\V1\ProductCategorySchema::IDENTIFIER => Schemas\V1\ProductCategorySchema::class,
				Schemas\V1\ProductCollectionDataSchema::IDENTIFIER => Schemas\V1\ProductCollectionDataSchema::class,
				Schemas\V1\ProductReviewSchema::IDENTIFIER => Schemas\V1\ProductReviewSchema::class,
			],
		];
	}

	/**
	 * Get a schema class instance.
	 *
	 * @throws \Exception If the schema does not exist.
	 *
	 * @param string $name Name of schema.
	 * @param int    $version API Version being requested.
	 * @return Schemas\V1\AbstractSchema A new instance of the requested schema.
	 */
	public function get( $name, $version = 1 ) {
		$schema = $this->schemas[ "v{$version}" ][ $name ] ?? false;

		if ( ! $schema ) {
			throw new \Exception( "{$name} v{$version} schema does not exist" );
		}

		return new $schema( $this->extend, $this );
	}
}
