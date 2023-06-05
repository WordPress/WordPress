<?php
/**
 * OrdersControllersServiceProvider class file.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;
use Automattic\WooCommerce\Internal\Orders\CouponsController;
use Automattic\WooCommerce\Internal\Orders\TaxesController;

/**
 * Service provider for the orders controller classes in the Automattic\WooCommerce\Internal\Orders namespace.
 */
class OrdersControllersServiceProvider extends AbstractServiceProvider {

	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		CouponsController::class,
		TaxesController::class,
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share( CouponsController::class );
		$this->share( TaxesController::class );
	}
}
