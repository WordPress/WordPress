<?php

namespace Yoast\WP\SEO\Initializers;

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Yoast\WP\SEO\Conditionals\No_Conditionals;

/**
 * Declares compatibility with the WooCommerce HPOS feature.
 */
class Woocommerce implements Initializer_Interface {

	use No_Conditionals;

	/**
	 * Hooks into WooCommerce.
	 *
	 * @return void
	 */
	public function initialize() {
			\add_action( 'before_woocommerce_init', [ $this, 'declare_custom_order_tables_compatibility' ] );
	}

	/**
	 * Declares compatibility with the WooCommerce HPOS feature.
	 *
	 * @return void
	 */
	public function declare_custom_order_tables_compatibility() {
		if ( \class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', \WPSEO_FILE, true );
		}
	}
}
