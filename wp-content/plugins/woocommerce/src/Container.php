<?php
/**
 * Container class file.
 */

namespace Automattic\WooCommerce;

use Automattic\WooCommerce\Internal\DependencyManagement\ExtendedContainer;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\COTMigrationServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\DownloadPermissionsAdjusterServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\AssignDefaultCategoryServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\FeaturesServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\MarketingServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\OrdersControllersServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\OrderAdminServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\OrderMetaBoxServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\ObjectCacheServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\OrdersDataStoreServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\OptionSanitizerServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\ProductAttributesLookupServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\ProductDownloadsServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\ProductReviewsServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\ProxiesServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\RestockRefundedItemsAdjusterServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\UtilsClassesServiceProvider;
use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\BatchProcessingServiceProvider;

/**
 * PSR11 compliant dependency injection container for WooCommerce.
 *
 * Classes in the `src` directory should specify dependencies from that directory via an 'init' method having arguments
 * with type hints. If an instance of the container itself is needed, the type hint to use is \Psr\Container\ContainerInterface.
 *
 * Classes in the `src` directory should interact with anything outside (especially code in the `includes` directory
 * and WordPress functions) by using the classes in the `Proxies` directory. The exception is idempotent
 * functions (e.g. `wp_parse_url`), those can be used directly.
 *
 * Classes in the `includes` directory should use the `wc_get_container` function to get the instance of the container when
 * they need to get an instance of a class from the `src` directory.
 *
 * Class registration should be done via service providers that inherit from Automattic\WooCommerce\Internal\DependencyManagement
 * and those should go in the `src\Internal\DependencyManagement\ServiceProviders` folder unless there's a good reason
 * to put them elsewhere. All the service provider class names must be in the `SERVICE_PROVIDERS` constant.
 */
final class Container {
	/**
	 * The list of service provider classes to register.
	 *
	 * @var string[]
	 */
	private $service_providers = array(
		AssignDefaultCategoryServiceProvider::class,
		DownloadPermissionsAdjusterServiceProvider::class,
		OptionSanitizerServiceProvider::class,
		OrdersDataStoreServiceProvider::class,
		ProductAttributesLookupServiceProvider::class,
		ProductDownloadsServiceProvider::class,
		ProductReviewsServiceProvider::class,
		ProxiesServiceProvider::class,
		RestockRefundedItemsAdjusterServiceProvider::class,
		UtilsClassesServiceProvider::class,
		COTMigrationServiceProvider::class,
		OrdersControllersServiceProvider::class,
		ObjectCacheServiceProvider::class,
		BatchProcessingServiceProvider::class,
		OrderMetaBoxServiceProvider::class,
		OrderAdminServiceProvider::class,
		FeaturesServiceProvider::class,
		MarketingServiceProvider::class,
	);

	/**
	 * The underlying container.
	 *
	 * @var \League\Container\Container
	 */
	private $container;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->container = new ExtendedContainer();

		// Add ourselves as the shared instance of ContainerInterface,
		// register everything else using service providers.

		$this->container->share( __CLASS__, $this );

		foreach ( $this->service_providers as $service_provider_class ) {
			$this->container->addServiceProvider( $service_provider_class );
		}
	}

	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
	 * @throws Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
	 *
	 * @return mixed Entry.
	 */
	public function get( string $id ): object {
		return $this->container->get( $id );
	}

	/**
	 * Returns true if the container can return an entry for the given identifier.
	 * Returns false otherwise.
	 *
	 * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
	 * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @return bool
	 */
	public function has( string $id ): bool {
		return $this->container->has( $id );
	}
}
