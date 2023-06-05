<?php
/**
 * Service provider for COTMigration.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\DataBase\Migrations\CustomOrderTable\CLIRunner;
use Automattic\WooCommerce\Database\Migrations\CustomOrderTable\PostsToOrdersMigrationController;
use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;

/**
 * Class COTMigrationServiceProvider
 *
 * @package Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders
 */
class COTMigrationServiceProvider extends AbstractServiceProvider {

	/**
	 * Services provided by this provider.
	 *
	 * @var string[]
	 */
	protected $provides = array(
		PostsToOrdersMigrationController::class,
		CLIRunner::class,
	);

	/**
	 * Use the register method to register items with the container via the
	 * protected $this->leagueContainer property or the `getLeagueContainer` method
	 * from the ContainerAwareTrait.
	 *
	 * @return void
	 */
	public function register() {
		$this->share( PostsToOrdersMigrationController::class );
		$this->share( CLIRunner::class );
	}
}
