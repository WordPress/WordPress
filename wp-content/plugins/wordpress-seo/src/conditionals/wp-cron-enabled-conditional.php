<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Class that checks if WP_CRON is enabled.
 */
class WP_CRON_Enabled_Conditional implements Conditional {

	/**
	 * Checks if WP_CRON is enabled.
	 *
	 * @return bool True when WP_CRON is enabled.
	 */
	public function is_met() {
		return ! ( \defined( 'DISABLE_WP_CRON' ) && \DISABLE_WP_CRON );
	}
}
