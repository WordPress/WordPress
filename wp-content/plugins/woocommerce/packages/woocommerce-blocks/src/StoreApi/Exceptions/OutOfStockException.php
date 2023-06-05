<?php
namespace Automattic\WooCommerce\StoreApi\Exceptions;

/**
 * OutOfStockException class.
 *
 * This exception is thrown when an item in a draft order is out of stock completely.
 */
class OutOfStockException extends StockAvailabilityException {}
