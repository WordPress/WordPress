<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Feature flag conditional for the new settings UI.
 */
class New_Settings_Ui_Conditional extends Feature_Flag_Conditional {

	/**
	 * Returns the name of the feature flag.
	 *
	 * @return string The name of the feature flag.
	 */
	protected function get_feature_flag() {
		return 'NEW_SETTINGS_UI';
	}
}
