<?php

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Admin\SyncUI;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Admin\UI;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Register;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Synchronize;

/**
 * Service provider for the Product Downloads-related services.
 */
class ProductDownloadsServiceProvider extends AbstractServiceProvider {
	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		Register::class,
		Synchronize::class,
		SyncUI::class,
		UI::class,
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share( Register::class );
		$this->share( Synchronize::class )->addArgument( Register::class );
		$this->share( SyncUI::class )->addArgument( Register::class );
		$this->share( UI::class )->addArgument( Register::class );
	}
}
