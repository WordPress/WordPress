<?php
namespace Automattic\WooCommerce\StoreApi\Exceptions;

/**
 * PartialOutOfStockException class.
 *
 * This exception is thrown when an item in a draft order has a quantity greater than what is available in stock.
 */
class PartialOutOfStockException extends StockAvailabilityException {}
