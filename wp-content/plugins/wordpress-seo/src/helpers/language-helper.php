<?php

namespace Yoast\WP\SEO\Helpers;

use WPSEO_Language_Utils;
use Yoast\WP\SEO\Config\Researcher_Languages;

/**
 * A helper object for language features.
 */
class Language_Helper {

	/**
	 * The languages with inclusive language analysis support.
	 *
	 * @var string[]
	 */
	public static $languages_with_inclusive_language_support = [ 'en' ];

	/**
	 * Checks whether word form recognition is active for the used language.
	 *
	 * @param string $language The used language.
	 *
	 * @return bool Whether word form recognition is active for the used language.
	 */
	public function is_word_form_recognition_active( $language ) {
		$supported_languages = [ 'de', 'en', 'es', 'fr', 'it', 'nl', 'ru', 'id', 'pt', 'pl', 'ar', 'sv', 'he', 'hu', 'nb', 'tr', 'cs', 'sk', 'el', 'ja' ];

		return \in_array( $language, $supported_languages, true );
	}

	/**
	 * Checks whether the given language has function word support.
	 * (E.g. function words are used or filtered out for this language when doing some SEO and readability assessments).
	 *
	 * @param string $language The language to check.
	 *
	 * @return bool Whether the language has function word support.
	 */
	public function has_function_word_support( $language ) {
		$supported_languages = [ 'en', 'de', 'nl', 'fr', 'es', 'it', 'pt', 'ru', 'pl', 'sv', 'id', 'he', 'ar', 'hu', 'nb', 'tr', 'cs', 'sk', 'fa', 'el', 'ja' ];

		return \in_array( $language, $supported_languages, true );
	}

	/**
	 * Checks whether the given language has inclusive language support.
	 *
	 * @param string $language The language to check if inclusive language is supported.
	 *
	 * @return bool Whether the language has inclusive language support.
	 */
	public function has_inclusive_language_support( $language ) {
		return \in_array( $language, self::$languages_with_inclusive_language_support, true );
	}

	/**
	 * Checks whether we have a specific researcher for the current locale and returns that language.
	 * If there is no researcher for the current locale, returns default as the researcher.
	 *
	 * @return string The language to use to select a researcher.
	 */
	public function get_researcher_language() {
		$researcher_language = WPSEO_Language_Utils::get_language( \get_locale() );
		$supported_languages = Researcher_Languages::SUPPORTED_LANGUAGES;

		if ( ! \in_array( $researcher_language, $supported_languages, true ) ) {
			$researcher_language = 'default';
		}
		return $researcher_language;
	}

	/**
	 * Returns The site language code without region
	 * (e.g. 'en' for 'en_US' or 'en_GB').
	 *
	 * @return string The site language code without region.
	 */
	public function get_language() {
		return WPSEO_Language_Utils::get_language( \get_locale() );
	}
}
