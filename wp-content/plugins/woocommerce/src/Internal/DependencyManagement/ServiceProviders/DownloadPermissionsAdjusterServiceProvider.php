<?php
/**
 * DownloadPermissionsAdjusterServiceProvider class file.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;
use Automattic\WooCommerce\Internal\DownloadPermissionsAdjuster;

/**
 * Service provider for the DownloadPermissionsAdjuster class.
 */
class DownloadPermissionsAdjusterServiceProvider extends AbstractServiceProvider {

	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		DownloadPermissionsAdjuster::class,
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share( DownloadPermissionsAdjuster::class );
	}
}
