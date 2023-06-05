<?php
namespace Automattic\WooCommerce\StoreApi\Exceptions;

/**
 * NotPurchasableException class.
 *
 * This exception is thrown when an item in the cart is not able to be purchased.
 */
class NotPurchasableException extends StockAvailabilityException {}
