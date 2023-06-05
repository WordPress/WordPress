<?php

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;
use Automattic\WooCommerce\Internal\Features\FeaturesController;
use Automattic\WooCommerce\Proxies\LegacyProxy;
use Automattic\WooCommerce\Utilities\PluginUtil;

/**
 * Service provider for the features enabling/disabling/compatibility engine.
 */
class FeaturesServiceProvider extends AbstractServiceProvider {

	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		FeaturesController::class,
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share( FeaturesController::class )
			->addArguments( array( LegacyProxy::class, PluginUtil::class ) );
	}
}
