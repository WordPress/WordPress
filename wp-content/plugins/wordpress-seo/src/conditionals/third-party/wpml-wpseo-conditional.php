<?php

namespace Yoast\WP\SEO\Conditionals\Third_Party;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Conditional that is met when the Yoast SEO Multilingual plugin,
 * a glue plugin developed by and for WPML, is active.
 */
class WPML_WPSEO_Conditional implements Conditional {

	/**
	 * Path to the Yoast SEO Multilingual plugin file.
	 *
	 * @internal
	 */
	public const PATH_TO_WPML_WPSEO_PLUGIN_FILE = 'wp-seo-multilingual/plugin.php';

	/**
	 * Returns whether or not the Yoast SEO Multilingual plugin is active.
	 *
	 * @return bool Whether or not the Yoast SEO Multilingual plugin is active.
	 */
	public function is_met() {
		return \is_plugin_active( self::PATH_TO_WPML_WPSEO_PLUGIN_FILE );
	}
}
