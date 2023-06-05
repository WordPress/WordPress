<?php
/**
 * Service provider for various order admin classes.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\Admin\Orders\COTRedirectionController;
use Automattic\WooCommerce\Internal\Admin\Orders\Edit;
use Automattic\WooCommerce\Internal\Admin\Orders\ListTable;
use Automattic\WooCommerce\Internal\Admin\Orders\PageController;
use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;

/**
 * OrderAdminServiceProvider class.
 */
class OrderAdminServiceProvider extends AbstractServiceProvider {

	/**
	 * List services provided by this class.
	 *
	 * @var string[]
	 */
	protected $provides = array(
		COTRedirectionController::class,
		PageController::class,
		Edit::class,
		ListTable::class,
	);

	/**
	 * Registers services provided by this class.
	 *
	 * @return void
	 */
	public function register() {
		$this->share( COTRedirectionController::class );
		$this->share( PageController::class );
		$this->share( Edit::class )->addArgument( PageController::class );
		$this->share( ListTable::class )->addArgument( PageController::class );
	}
}
