<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Feature flag conditional for the updated importer framework.
 */
class Updated_Importer_Framework_Conditional extends Feature_Flag_Conditional {

	/**
	 * Returns the name of the updated importer framework feature flag.
	 *
	 * @return string The name of the feature flag.
	 */
	protected function get_feature_flag() {
		return 'UPDATED_IMPORTER_FRAMEWORK';
	}
}
