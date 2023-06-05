<?php
/**
 * OrdersDataStoreServiceProvider class file.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\Admin\ProductReviews\Reviews;
use Automattic\WooCommerce\Internal\Admin\ProductReviews\ReviewsCommentsOverrides;
use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;

/**
 * Service provider for the classes in the Internal\Admin\ProductReviews namespace.
 */
class ProductReviewsServiceProvider extends AbstractServiceProvider {

	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		Reviews::class,
		ReviewsCommentsOverrides::class,
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share( Reviews::class );
		$this->share( ReviewsCommentsOverrides::class );
	}
}
