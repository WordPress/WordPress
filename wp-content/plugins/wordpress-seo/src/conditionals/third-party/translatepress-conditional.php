<?php

namespace Yoast\WP\SEO\Conditionals\Third_Party;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Conditional that is only met when the TranslatePress plugin is active.
 */
class TranslatePress_Conditional implements Conditional {

	/**
	 * Checks whether the TranslatePress plugin is active.
	 *
	 * @return bool Whether the TranslatePress plugin is active.
	 */
	public function is_met() {
		return \class_exists( 'TRP_Translate_Press' );
	}
}
