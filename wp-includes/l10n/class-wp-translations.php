<?php
/**
 * I18N: WP_Translations class.
 *
 * @package WordPress
 * @subpackage I18N
 * @since 6.5.0
 */

/**
 * Class WP_Translations.
 *
 * @since 6.5.0
 *
 * @property-read array<string, string> $headers
 * @property-read array<string, string[]> $entries
 */
class WP_Translations {
	/**
	 * Text domain.
	 *
	 * @since 6.5.0
	 * @var string
	 */
	protected $textdomain = 'default';

	/**
	 * Translation controller instance.
	 *
	 * @since 6.5.0
	 * @var WP_Translation_Controller
	 */
	protected $controller;

	/**
	 * Constructor.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Translation_Controller $controller I18N controller.
	 * @param string                    $textdomain Optional. Text domain. Default 'default'.
	 */
	public function __construct( WP_Translation_Controller $controller, string $textdomain = 'default' ) {
		$this->controller = $controller;
		$this->textdomain = $textdomain;
	}

	/**
	 * Magic getter for backward compatibility.
	 *
	 * @since 6.5.0
	 *
	 * @param string $name Property name.
	 * @return mixed
	 */
	public function __get( string $name ) {
		if ( 'entries' === $name ) {
			$entries = $this->controller->get_entries( $this->textdomain );

			$result = array();

			foreach ( $entries as $original => $translations ) {
				$result[] = $this->make_entry( $original, $translations );
			}

			return $result;
		}

		if ( 'headers' === $name ) {
			return $this->controller->get_headers( $this->textdomain );
		}

		return null;
	}

	/**
	 * Builds a Translation_Entry from original string and translation strings.
	 *
	 * @see MO::make_entry()
	 *
	 * @since 6.5.0
	 *
	 * @param string $original     Original string to translate from MO file. Might contain
	 *                             0x04 as context separator or 0x00 as singular/plural separator.
	 * @param string $translations Translation strings from MO file.
	 * @return Translation_Entry Entry instance.
	 */
	private function make_entry( $original, $translations ): Translation_Entry {
		$entry = new Translation_Entry();

		// Look for context, separated by \4.
		$parts = explode( "\4", $original );
		if ( isset( $parts[1] ) ) {
			$original       = $parts[1];
			$entry->context = $parts[0];
		}

		$entry->singular     = $original;
		$entry->translations = explode( "\0", $translations );
		$entry->is_plural    = count( $entry->translations ) > 1;

		return $entry;
	}

	/**
	 * Translates a plural string.
	 *
	 * @since 6.5.0
	 *
	 * @param string|null $singular Singular string.
	 * @param string|null $plural   Plural string.
	 * @param int|float   $count    Count. Should be an integer, but some plugins pass floats.
	 * @param string|null $context  Context.
	 * @return string|null Translation if it exists, or the unchanged singular string.
	 */
	public function translate_plural( $singular, $plural, $count = 1, $context = '' ) {
		if ( null === $singular || null === $plural ) {
			return $singular;
		}

		$translation = $this->controller->translate_plural( array( $singular, $plural ), (int) $count, (string) $context, $this->textdomain );
		if ( false !== $translation ) {
			return $translation;
		}

		// Fall back to the original with English grammar rules.
		return ( 1 === $count ? $singular : $plural );
	}

	/**
	 * Translates a singular string.
	 *
	 * @since 6.5.0
	 *
	 * @param string|null $singular Singular string.
	 * @param string|null $context  Context.
	 * @return string|null Translation if it exists, or the unchanged singular string
	 */
	public function translate( $singular, $context = '' ) {
		if ( null === $singular ) {
			return null;
		}

		$translation = $this->controller->translate( $singular, (string) $context, $this->textdomain );
		if ( false !== $translation ) {
			return $translation;
		}

		// Fall back to the original.
		return $singular;
	}
}
