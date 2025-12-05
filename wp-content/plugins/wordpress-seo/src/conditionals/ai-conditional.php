<?php

namespace Yoast\WP\SEO\Conditionals;

use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Conditional for the Yoast AI feature.
 */
class AI_Conditional implements Conditional {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * The constructor.
	 *
	 * @param Options_Helper $options The options helper.
	 */
	public function __construct( Options_Helper $options ) {
		$this->options = $options;
	}

	/**
	 * Returns `true` when Yoast AI is enabled.
	 *
	 * @return bool `true` when Yoast AI is enabled.
	 */
	public function is_met(): bool {
		return $this->options->get( 'enable_ai_generator' ) === true;
	}
}
