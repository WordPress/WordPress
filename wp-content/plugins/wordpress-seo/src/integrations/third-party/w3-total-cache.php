<?php

namespace Yoast\WP\SEO\Integrations\Third_Party;

use Yoast\WP\SEO\Conditionals\Third_Party\W3_Total_Cache_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * W3 Total Cache integration.
 */
class W3_Total_Cache implements Integration_Interface {

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ W3_Total_Cache_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * On successful update/add of the taxonomy meta option, flush the W3TC cache.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'add_option_wpseo_taxonomy_meta', 'w3tc_objectcache_flush' );
		\add_action( 'update_option_wpseo_taxonomy_meta', 'w3tc_objectcache_flush' );
	}
}
