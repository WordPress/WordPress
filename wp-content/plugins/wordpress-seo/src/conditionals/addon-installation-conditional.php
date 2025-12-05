<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Checks if the Addon_Installation constant is set.
 */
class Addon_Installation_Conditional extends Feature_Flag_Conditional {

	/**
	 * Returns the name of the feature flag.
	 * 'YOAST_SEO_' is automatically prepended to it and it will be uppercased.
	 *
	 * @return string the name of the feature flag.
	 */
	protected function get_feature_flag() {
		return 'ADDON_INSTALLATION';
	}
}
