<?php
/**
 * OrdersDataStoreServiceProvider class file.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Caches\OrderCache;
use Automattic\WooCommerce\Caches\OrderCacheController;
use Automattic\WooCommerce\Caching\TransientsEngine;
use Automattic\WooCommerce\DataBase\Migrations\CustomOrderTable\CLIRunner;
use Automattic\WooCommerce\Database\Migrations\CustomOrderTable\PostsToOrdersMigrationController;
use Automattic\WooCommerce\Internal\BatchProcessing\BatchProcessingController;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableRefundDataStore;
use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;
use Automattic\WooCommerce\Internal\DataStores\Orders\DataSynchronizer;
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStoreMeta;
use Automattic\WooCommerce\Internal\Features\FeaturesController;
use Automattic\WooCommerce\Internal\Utilities\DatabaseUtil;
use Automattic\WooCommerce\Proxies\LegacyProxy;

/**
 * Service provider for the classes in the Internal\DataStores\Orders namespace.
 */
class OrdersDataStoreServiceProvider extends AbstractServiceProvider {

	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		DataSynchronizer::class,
		CustomOrdersTableController::class,
		OrdersTableDataStore::class,
		CLIRunner::class,
		OrdersTableDataStoreMeta::class,
		OrdersTableRefundDataStore::class,
		OrderCache::class,
		OrderCacheController::class,
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share( OrdersTableDataStoreMeta::class );

		$this->share( OrdersTableDataStore::class )->addArguments( array( OrdersTableDataStoreMeta::class, DatabaseUtil::class, LegacyProxy::class ) );
		$this->share( DataSynchronizer::class )->addArguments(
			array(
				OrdersTableDataStore::class,
				DatabaseUtil::class,
				PostsToOrdersMigrationController::class,
				LegacyProxy::class,
				OrderCacheController::class,
			)
		);
		$this->share( OrdersTableRefundDataStore::class )->addArguments( array( OrdersTableDataStoreMeta::class, DatabaseUtil::class, LegacyProxy::class ) );
		$this->share( CustomOrdersTableController::class )->addArguments(
			array(
				OrdersTableDataStore::class,
				DataSynchronizer::class,
				OrdersTableRefundDataStore::class,
				BatchProcessingController::class,
				FeaturesController::class,
				OrderCache::class,
				OrderCacheController::class,
			)
		);
		$this->share( OrderCache::class );
		$this->share( OrderCacheController::class )->addArgument( OrderCache::class );
		if ( Constants::is_defined( 'WP_CLI' ) && WP_CLI ) {
			$this->share( CLIRunner::class )->addArguments( array( CustomOrdersTableController::class, DataSynchronizer::class, PostsToOrdersMigrationController::class ) );
		}
	}
}
