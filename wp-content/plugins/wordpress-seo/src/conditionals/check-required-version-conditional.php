<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional for the CHECK_REQUIRED_VERSION feature flag.
 */
class Check_Required_Version_Conditional extends Feature_Flag_Conditional {

	/**
	 * Returns the name of the feature flag.
	 *
	 * @return string The name of the feature flag.
	 */
	protected function get_feature_flag() {
		return 'CHECK_REQUIRED_VERSION';
	}
}
