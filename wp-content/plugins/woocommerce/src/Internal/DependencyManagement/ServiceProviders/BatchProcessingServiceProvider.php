<?php
/**
 * Service provider for ActionUpdateController class.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;
use Automattic\WooCommerce\Internal\BatchProcessing\BatchProcessingController;

/**
 * Class BatchProcessingServiceProvider
 *
 * @package Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders
 */
class BatchProcessingServiceProvider extends AbstractServiceProvider {

	/**
	 * Services provided by this provider.
	 *
	 * @var string[]
	 */
	protected $provides = array(
		BatchProcessingController::class,
	);

	/**
	 * Use the register method to register items with the container via the
	 * protected $this->leagueContainer property or the `getLeagueContainer` method
	 * from the ContainerAwareTrait.
	 *
	 * @return void
	 */
	public function register() {
		$this->share( BatchProcessingController::class, new BatchProcessingController() );
	}
}
