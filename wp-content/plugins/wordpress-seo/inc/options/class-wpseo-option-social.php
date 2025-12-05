<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals\Options
 */

/**
 * Option: wpseo_social.
 */
class WPSEO_Option_Social extends WPSEO_Option {

	/**
	 * Option name.
	 *
	 * @var string
	 */
	public $option_name = 'wpseo_social';

	/**
	 * Array of defaults for the option.
	 *
	 * Shouldn't be requested directly, use $this->get_defaults();
	 *
	 * @var array
	 */
	protected $defaults = [
		// Form fields.
		'facebook_site'         => '', // Text field.
		'instagram_url'         => '',
		'linkedin_url'          => '',
		'myspace_url'           => '',
		'og_default_image'      => '', // Text field.
		'og_default_image_id'   => '',
		'og_frontpage_title'    => '', // Text field.
		'og_frontpage_desc'     => '', // Text field.
		'og_frontpage_image'    => '', // Text field.
		'og_frontpage_image_id' => '',
		'opengraph'             => true,
		'pinterest_url'         => '',
		'pinterestverify'       => '',
		'twitter'               => true,
		'twitter_site'          => '', // Text field.
		'twitter_card_type'     => 'summary_large_image',
		'youtube_url'           => '',
		'wikipedia_url'         => '',
		'other_social_urls'     => [],
		'mastodon_url'          => '',
	];

	/**
	 * Array of sub-options which should not be overloaded with multi-site defaults.
	 *
	 * @var array
	 */
	public $ms_exclude = [
		/* Privacy. */
		'pinterestverify',
	];

	/**
	 * Array of allowed twitter card types.
	 *
	 * While we only have the options summary and summary_large_image in the
	 * interface now, we might change that at some point.
	 *
	 * {@internal Uncomment any of these to allow them in validation *and* automatically
	 *            add them as a choice in the options page.}}
	 *
	 * @var array
	 */
	public static $twitter_card_types = [
		'summary_large_image' => '',
		// 'summary'             => '',
		// 'photo'               => '',
		// 'gallery'             => '',
		// 'app'                 => '',
		// 'player'              => '',
		// 'product'             => '',
	];

	/**
	 * Add the actions and filters for the option.
	 */
	protected function __construct() {
		parent::__construct();

		add_filter( 'admin_title', [ 'Yoast_Input_Validation', 'add_yoast_admin_document_title_errors' ] );
	}

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
	 * Translate/set strings used in the option defaults.
	 *
	 * @return void
	 */
	public function translate_defaults() {
		self::$twitter_card_types['summary_large_image'] = 'Summary with large image';
	}

	/**
	 * Validate the option.
	 *
	 * @param array $dirty New value for the option.
	 * @param array $clean Clean value for the option, normally the defaults.
	 * @param array $old   Old value of the option.
	 *
	 * @return array Validated clean value for the option to be saved to the database.
	 */
	protected function validate_option( $dirty, $clean, $old ) {

		foreach ( $clean as $key => $value ) {
			switch ( $key ) {
				/* Text fields. */
				case 'og_frontpage_desc':
				case 'og_frontpage_title':
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
						$clean[ $key ] = WPSEO_Utils::sanitize_text_field( $dirty[ $key ] );
					}
					break;

				case 'og_default_image_id':
				case 'og_frontpage_image_id':
					if ( isset( $dirty[ $key ] ) ) {
						$clean[ $key ] = (int) $dirty[ $key ];

						if ( $dirty[ $key ] === '' ) {
							$clean[ $key ] = $dirty[ $key ];
						}
					}
					break;

				/* URL text fields - no ftp allowed. */
				case 'facebook_site':
				case 'instagram_url':
				case 'linkedin_url':
				case 'myspace_url':
				case 'pinterest_url':
				case 'og_default_image':
				case 'og_frontpage_image':
				case 'youtube_url':
				case 'wikipedia_url':
				case 'mastodon_url':
					$this->validate_url( $key, $dirty, $old, $clean );
					break;

				case 'pinterestverify':
					$this->validate_verification_string( $key, $dirty, $old, $clean );
					break;

				/* Twitter user name. */
				case 'twitter_site':
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
						$twitter_id = $this->validate_twitter_id( $dirty[ $key ] );

						if ( $twitter_id ) {
							$clean[ $key ] = $twitter_id;
						}
						elseif ( isset( $old[ $key ] ) && $old[ $key ] !== '' ) {
								$twitter_id = sanitize_text_field( ltrim( $old[ $key ], '@' ) );
							if ( preg_match( '`^[A-Za-z0-9_]{1,25}$`', $twitter_id ) ) {
								$clean[ $key ] = $twitter_id;
							}
						}
						unset( $twitter_id );

						Yoast_Input_Validation::add_dirty_value_to_settings_errors( $key, $dirty[ $key ] );
					}
					break;

				case 'twitter_card_type':
					if ( isset( $dirty[ $key ], self::$twitter_card_types[ $dirty[ $key ] ] ) && $dirty[ $key ] !== '' ) {
						$clean[ $key ] = $dirty[ $key ];
					}
					break;

				/* Boolean fields. */
				case 'opengraph':
				case 'twitter':
					$clean[ $key ] = ( isset( $dirty[ $key ] ) ? WPSEO_Utils::validate_bool( $dirty[ $key ] ) : false );
					break;

				/* Array fields. */
				case 'other_social_urls':
					if ( isset( $dirty[ $key ] ) ) {
						$items = $dirty[ $key ];
						if ( ! is_array( $items ) ) {
							$items = json_decode( $dirty[ $key ], true );
						}

						if ( is_array( $items ) ) {
							foreach ( $items as $item_key => $item ) {
								$validated_url = $this->validate_social_url( $item );

								if ( $validated_url === false ) {
									// Restore the previous URL values, if any.
									$old_urls = ( isset( $old[ $key ] ) ) ? $old[ $key ] : [];
									foreach ( $old_urls as $old_item_key => $old_url ) {
										if ( $old_url !== '' ) {
											$url = WPSEO_Utils::sanitize_url( $old_url );
											if ( $url !== '' ) {
												$clean[ $key ][ $old_item_key ] = $url;
											}
										}
									}
									break;
								}

								// The URL format is valid, let's sanitize it.
								$url = WPSEO_Utils::sanitize_url( $validated_url );
								if ( $url !== '' ) {
									$clean[ $key ][ $item_key ] = $url;
								}
							}
						}
					}

					break;
			}
		}

		return $clean;
	}

	/**
	 * Validates a social URL.
	 *
	 * @param string $url The url to be validated.
	 *
	 * @return string|false The validated URL or false if the URL is not valid.
	 */
	public function validate_social_url( $url ) {
		$validated_url = filter_var( WPSEO_Utils::sanitize_url( trim( $url ) ), FILTER_VALIDATE_URL );

		return $validated_url;
	}

	/**
	 * Validates a twitter id.
	 *
	 * @param string $twitter_id    The twitter id to be validated.
	 * @param bool   $strip_at_sign Whether or not to strip the `@` sign.
	 *
	 * @return string|false The validated twitter id or false if it is not valid.
	 */
	public function validate_twitter_id( $twitter_id, $strip_at_sign = true ) {
		$twitter_id = ( $strip_at_sign ) ? sanitize_text_field( ltrim( $twitter_id, '@' ) ) : sanitize_text_field( $twitter_id );

		/*
		 * From the Twitter documentation about twitter screen names:
		 * Typically a maximum of 15 characters long, but some historical accounts
		 * may exist with longer names.
		 * A username can only contain alphanumeric characters (letters A-Z, numbers 0-9)
		 * with the exception of underscores.
		 *
		 * @link https://support.twitter.com/articles/101299-why-can-t-i-register-certain-usernames
		 */
		if ( preg_match( '`^[A-Za-z0-9_]{1,25}$`', $twitter_id ) ) {
			return $twitter_id;
		}

		if ( preg_match( '`^http(?:s)?://(?:www\.)?(?:twitter|x)\.com/(?P<handle>[A-Za-z0-9_]{1,25})/?$`', $twitter_id, $matches ) ) {
			return $matches['handle'];
		}

		return false;
	}

	/**
	 * Clean a given option value.
	 *
	 * @param array       $option_value          Old (not merged with defaults or filtered) option value to
	 *                                           clean according to the rules for this option.
	 * @param string|null $current_version       Optional. Version from which to upgrade, if not set,
	 *                                           version specific upgrades will be disregarded.
	 * @param array|null  $all_old_option_values Optional. Only used when importing old options to have
	 *                                           access to the real old values, in contrast to the saved ones.
	 *
	 * @return array Cleaned option.
	 */
	protected function clean_option( $option_value, $current_version = null, $all_old_option_values = null ) {

		/* Move options from very old option to this one. */
		$old_option = null;
		if ( isset( $all_old_option_values ) ) {
			// Ok, we have an import.
			if ( isset( $all_old_option_values['wpseo_indexation'] ) && is_array( $all_old_option_values['wpseo_indexation'] ) && $all_old_option_values['wpseo_indexation'] !== [] ) {
				$old_option = $all_old_option_values['wpseo_indexation'];
			}
		}
		else {
			$old_option = get_option( 'wpseo_indexation' );
		}

		if ( is_array( $old_option ) && $old_option !== [] ) {
			$move = [
				'opengraph',
			];
			foreach ( $move as $key ) {
				if ( isset( $old_option[ $key ] ) && ! isset( $option_value[ $key ] ) ) {
					$option_value[ $key ] = $old_option[ $key ];
				}
			}
			unset( $move, $key );
		}
		unset( $old_option );

		return $option_value;
	}
}
