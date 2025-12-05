<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals\Options
 */

/**
 * Option: wpseo_llmstxt.
 */
class WPSEO_Option_Llmstxt extends WPSEO_Option {

	private const OTHER_INCLUDED_PAGES_LIMIT = 100;

	/**
	 * Option name.
	 *
	 * @var string
	 */
	public $option_name = 'wpseo_llmstxt';

	/**
	 * Array of defaults for the option.
	 *
	 * Shouldn't be requested directly, use $this->get_defaults();
	 *
	 * @var array<string, int|string|array<int>>
	 */
	protected $defaults = [
		'llms_txt_selection_mode' => 'auto',
		'about_us_page'           => 0,
		'contact_page'            => 0,
		'terms_page'              => 0,
		'privacy_policy_page'     => 0,
		'shop_page'               => 0,
		'other_included_pages'    => [],
	];

	/**
	 * Get the singleton instance of this class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * All concrete classes must contain a validate_option() method which validates all
	 * values within the option.
	 *
	 * @param array $dirty New value for the option.
	 * @param array $clean Clean value for the option, normally the defaults.
	 * @param array $old   Old value of the option.
	 *
	 * @return array The clean option with the saved value.
	 */
	protected function validate_option( $dirty, $clean, $old ) {

		foreach ( $clean as $key => $value ) {
			switch ( $key ) {
				case 'other_included_pages':
					if ( isset( $dirty[ $key ] ) ) {
						$items = $dirty[ $key ];
						if ( ! is_array( $items ) ) {
							$items = json_decode( $dirty[ $key ], true );
						}

						if ( is_array( $items ) ) {
							$items = array_slice( $items, 0, $this->get_other_included_pages_limit() );
							foreach ( $items as $item ) {
								$validated_id = WPSEO_Utils::validate_int( $item );

								if ( $validated_id === false || $validated_id === 0 ) {
									continue;
								}

								$clean[ $key ][] = $validated_id;
							}
						}
					}

					break;
				case 'about_us_page':
				case 'contact_page':
				case 'terms_page':
				case 'privacy_policy_page':
				case 'shop_page':
					if ( isset( $dirty[ $key ] ) ) {
						$int = WPSEO_Utils::validate_int( $dirty[ $key ] );
						if ( $int !== false && $int >= 0 ) {
							$clean[ $key ] = $int;
						}
					}
					elseif ( isset( $old[ $key ] ) ) {
						$int = WPSEO_Utils::validate_int( $old[ $key ] );
						if ( $int !== false && $int >= 0 ) {
							$clean[ $key ] = $int;
						}
					}
					break;
				case 'llms_txt_selection_mode':
					if ( isset( $dirty[ $key ] ) && in_array( $dirty[ $key ], [ 'auto', 'manual' ], true ) ) {
						$clean[ $key ] = $dirty[ $key ];
					}
					break;
			}
		}

		return $clean;
	}

	/**
	 * Gets the limit for the other included pages.
	 *
	 * @return int The limit for the other included pages.
	 */
	public function get_other_included_pages_limit() {
		return self::OTHER_INCLUDED_PAGES_LIMIT;
	}
}
