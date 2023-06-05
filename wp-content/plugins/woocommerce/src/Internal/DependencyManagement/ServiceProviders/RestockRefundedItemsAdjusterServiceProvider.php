<?php
/**
 * RestockRefundedItemsAdjusterServiceProvider class file.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;
use Automattic\WooCommerce\Internal\RestockRefundedItemsAdjuster;

/**
 * Service provider for the RestockRefundedItemsAdjuster class.
 */
class RestockRefundedItemsAdjusterServiceProvider extends AbstractServiceProvider {

	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		RestockRefundedItemsAdjuster::class,
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share( RestockRefundedItemsAdjuster::class );
	}
}
