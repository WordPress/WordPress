<?php

namespace Yoast\WP\SEO\Editors\Framework;

use Yoast\WP\SEO\Editors\Domain\Analysis_Features\Analysis_Feature_Interface;
use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * This class describes the Readability analysis feature.
 */
class Readability_Analysis implements Analysis_Feature_Interface {

	public const NAME = 'readabilityAnalysis';

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The constructor.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * If this analysis is enabled.
	 *
	 * @return bool If this analysis is enabled.
	 */
	public function is_enabled(): bool {
		return $this->is_globally_enabled() && $this->is_user_enabled();
	}

	/**
	 * If this analysis is enabled by the user.
	 *
	 * @return bool If this analysis is enabled by the user.
	 */
	private function is_user_enabled(): bool {
		return ! \get_user_meta( \get_current_user_id(), 'wpseo_content_analysis_disable', true );
	}

	/**
	 * If this analysis is enabled globally.
	 *
	 * @return bool If this analysis is enabled globally.
	 */
	private function is_globally_enabled(): bool {
		return (bool) $this->options_helper->get( 'content_analysis_active', true );
	}

	/**
	 * Gets the name.
	 *
	 * @return string The name.
	 */
	public function get_name(): string {
		return self::NAME;
	}

	/**
	 * Gets the legacy key.
	 *
	 * @return string The legacy key.
	 */
	public function get_legacy_key(): string {
		return 'contentAnalysisActive';
	}
}
