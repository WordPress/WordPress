<?php
/**
 * Class Aliases for graceful Backwards compatibility.
 *
 * This file is autoloaded via composer.json and maps the old namespaces to new namespaces.
 */

$class_aliases = [
	// Old to new namespaces for utils and exceptions.
	Automattic\WooCommerce\StoreApi\Exceptions\RouteException::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\RouteException::class,
	Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema::class => Automattic\WooCommerce\Blocks\Domain\Services\ExtendRestApi::class,
	Automattic\WooCommerce\StoreApi\SchemaController::class => Automattic\WooCommerce\Blocks\StoreApi\SchemaController::class,
	Automattic\WooCommerce\StoreApi\RoutesController::class => Automattic\WooCommerce\Blocks\StoreApi\RoutesController::class,
	Automattic\WooCommerce\StoreApi\Formatters::class      => Automattic\WooCommerce\Blocks\StoreApi\Formatters::class,
	Automattic\WooCommerce\StoreApi\Payments\PaymentResult::class => Automattic\WooCommerce\Blocks\Payments\PaymentResult::class,
	Automattic\WooCommerce\StoreApi\Payments\PaymentContext::class => Automattic\WooCommerce\Blocks\Payments\PaymentContext::class,

	// Old schemas to V1 schemas under new namespace.
	Automattic\WooCommerce\StoreApi\Schemas\V1\AbstractAddressSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\AbstractAddressSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\AbstractSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\AbstractSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\BillingAddressSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\BillingAddressSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\CartCouponSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartCouponSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\CartExtensionsSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartExtensionsSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\CartFeeSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartFeeSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartItemSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\CartShippingRateSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartShippingRateSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\CheckoutSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\ErrorSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\ErrorSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\ImageAttachmentSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\ImageAttachmentSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\OrderCouponSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\OrderCouponSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\ProductAttributeSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\ProductAttributeSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\ProductCategorySchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\ProductCategorySchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\ProductCollectionDataSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\ProductCollectionDataSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\ProductReviewSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\ProductReviewSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\ProductSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\ProductSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\ShippingAddressSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\ShippingAddressSchema::class,
	Automattic\WooCommerce\StoreApi\Schemas\V1\TermSchema::class => Automattic\WooCommerce\Blocks\StoreApi\Schemas\TermSchema::class,

	// Old routes to V1 routes under new namespace.
	Automattic\WooCommerce\StoreApi\Routes\V1\AbstractCartRoute::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\AbstractCartRoute::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\AbstractRoute::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\AbstractRoute::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\AbstractTermsRoute::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\AbstractTermsRoute::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\Batch::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\Batch::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\Cart::class  => Automattic\WooCommerce\Blocks\StoreApi\Routes\Cart::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartAddItem::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartAddItem::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartApplyCoupon::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartApplyCoupon::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartCoupons::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartCoupons::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartCouponsByCode::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartCouponsByCode::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartExtensions::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartExtensions::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartItems::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartItems::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartItemsByKey::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartItemsByKey::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartRemoveCoupon::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartRemoveCoupon::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartRemoveItem::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartRemoveItem::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartSelectShippingRate::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartSelectShippingRate::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartUpdateCustomer::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartUpdateCustomer::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\CartUpdateItem::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\CartUpdateItem::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\Checkout::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\Checkout::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\ProductAttributes::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\ProductAttributes::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\ProductAttributesById::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\ProductAttributesById::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\ProductAttributeTerms::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\ProductAttributeTerms::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\ProductCategories::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\ProductCategories::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\ProductCategoriesById::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\ProductCategoriesById::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\ProductCollectionData::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\ProductCollectionData::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\ProductReviews::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\ProductReviews::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\Products::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\Products::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\ProductsById::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\ProductsById::class,
	Automattic\WooCommerce\StoreApi\Routes\V1\ProductTags::class => Automattic\WooCommerce\Blocks\StoreApi\Routes\ProductTags::class,
];

foreach ( $class_aliases as $class => $alias ) {
	if ( ! class_exists( $alias, false ) ) {
		class_alias( $class, $alias );
	}
}

unset( $class_aliases );
