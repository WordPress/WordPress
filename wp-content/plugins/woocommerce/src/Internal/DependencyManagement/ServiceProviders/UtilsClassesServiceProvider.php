<?php
/**
 * UtilsClassesServiceProvider class file.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Internal\DataStores\Orders\DataSynchronizer;
use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;
use Automattic\WooCommerce\Internal\Utilities\COTMigrationUtil;
use Automattic\WooCommerce\Internal\Utilities\DatabaseUtil;
use Automattic\WooCommerce\Internal\Utilities\HtmlSanitizer;
use Automattic\WooCommerce\Internal\Utilities\WebhookUtil;
use Automattic\WooCommerce\Proxies\LegacyProxy;
use Automattic\WooCommerce\Utilities\PluginUtil;
use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * Service provider for the non-static utils classes in the Automattic\WooCommerce\src namespace.
 */
class UtilsClassesServiceProvider extends AbstractServiceProvider {

	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		DatabaseUtil::class,
		HtmlSanitizer::class,
		OrderUtil::class,
		PluginUtil::class,
		COTMigrationUtil::class,
		WebhookUtil::class,
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share( DatabaseUtil::class );
		$this->share( HtmlSanitizer::class );
		$this->share( OrderUtil::class );
		$this->share( PluginUtil::class )
			->addArgument( LegacyProxy::class );
		$this->share( COTMigrationUtil::class )
			->addArguments( array( CustomOrdersTableController::class, DataSynchronizer::class ) );
		$this->share( WebhookUtil::class );
	}
}
