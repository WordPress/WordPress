<?php

namespace Yoast\WP\SEO\Editors\Framework;

use Yoast\WP\SEO\Editors\Domain\Analysis_Features\Analysis_Feature_Interface;
use Yoast\WP\SEO\Helpers\Language_Helper;

/**
 * Describes if the word for recognition analysis is enabled
 */
class Word_Form_Recognition implements Analysis_Feature_Interface {

	public const NAME = 'wordFormRecognition';

	/**
	 * The language helper.
	 *
	 * @var Language_Helper
	 */
	private $language_helper;

	/**
	 * The constructor.
	 *
	 * @param Language_Helper $language_helper The language helper.
	 */
	public function __construct( Language_Helper $language_helper ) {
		$this->language_helper = $language_helper;
	}

	/**
	 * If this analysis is enabled.
	 *
	 * @return bool If this analysis is enabled.
	 */
	public function is_enabled(): bool {
		return $this->language_helper->is_word_form_recognition_active( $this->language_helper->get_language() );
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
		return 'wordFormRecognitionActive';
	}
}
