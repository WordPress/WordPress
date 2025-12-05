<?php

namespace Yoast\WP\SEO\Editors\Framework;

use Yoast\WP\SEO\Editors\Domain\Analysis_Features\Analysis_Feature_Interface;
use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Describes if the Cornerstone content features is enabled.
 */
class Cornerstone_Content implements Analysis_Feature_Interface {

	public const NAME = 'cornerstoneContent';

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
	 * If cornerstone is enabled.
	 *
	 * @return bool If cornerstone is enabled.
	 */
	public function is_enabled(): bool {
		return (bool) $this->options_helper->get( 'enable_cornerstone_content', false );
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
		return 'cornerstoneActive';
	}
}
