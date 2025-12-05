<?php

namespace Yoast\WP\SEO\Editors\Framework;

use Yoast\WP\SEO\Editors\Domain\Analysis_Features\Analysis_Feature_Interface;

/**
 * Describes if the previously used keyword feature should be enabled.
 */
class Previously_Used_Keyphrase implements Analysis_Feature_Interface {

	public const NAME = 'previouslyUsedKeyphrase';

	/**
	 * If this analysis is enabled.
	 *
	 * @return bool If this analysis is enabled.
	 */
	public function is_enabled(): bool {
		/**
		 * Filter to determine If the PreviouslyUsedKeyphrase assessment should run.
		 *
		 * @param bool $previouslyUsedKeyphraseActive If the PreviouslyUsedKeyphrase assessment should run.
		 */
		return (bool) \apply_filters( 'wpseo_previously_used_keyword_active', true );
	}

	/**
	 * Returns the name of the object.
	 *
	 * @return string
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
		return 'previouslyUsedKeywordActive';
	}
}
