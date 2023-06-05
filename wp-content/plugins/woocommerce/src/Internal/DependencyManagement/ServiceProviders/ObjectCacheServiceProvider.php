<?php

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Caching\WPCacheEngine;
use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;

/**
 * Service provider for the object cache mechanism.
 */
class ObjectCacheServiceProvider extends AbstractServiceProvider {

	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		WPCacheEngine::class,
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share( WPCacheEngine::class );
	}
}
