<?php
/**
 * @package WPSEO\Internals\Options
 */

/**
 * Option: wpseo_social
 */
class WPSEO_Option_Social extends WPSEO_Option {

	/**
	 * @var  string  option name
	 */
	public $option_name = 'wpseo_social';

	/**
	 * @var  array  Array of defaults for the option
	 *        Shouldn't be requested directly, use $this->get_defaults();
	 */
	protected $defaults = array(
		// Non-form fields, set via procedural code in admin/pages/social.php.
		'fb_admins'          => array(), // Array of user id's => array( name => '', link => '' ).

		// Non-form field, set via translate_defaults() and validate_option() methods.
		'fbconnectkey'       => '',
		// Form fields:
		'facebook_site'      => '', // Text field.
		'instagram_url'      => '',
		'linkedin_url'       => '',
		'myspace_url'        => '',
		'og_default_image'   => '', // Text field.
		'og_frontpage_title' => '', // Text field.
		'og_frontpage_desc'  => '', // Text field.
		'og_frontpage_image' => '', // Text field.
		'opengraph'          => true,
		'googleplus'         => false,
		'pinterest_url'      => '',
		'pinterestverify'    => '',
		'plus-publisher'     => '', // Text field.
		'twitter'            => true,
		'twitter_site'       => '', // Text field.
		'twitter_card_type'  => 'summary',
		'youtube_url'        => '',
		'google_plus_url'    => '',
		// Form field, but not always available:
		'fbadminapp'         => '', // Facebook app ID.
	);

	/**
	 * @var array  Array of sub-options which should not be overloaded with multi-site defaults
	 */
	public $ms_exclude = array(
		/* privacy */
		'fb_admins',
		'fbconnectkey',
		'fbadminapp',
		'pinterestverify',
	);


	/**
	 * @var  array  Array of allowed twitter card types
	 *              While we only have the options summary and summary_large_image in the
	 *              interface now, we might change that at some point.
	 *
	 * @internal Uncomment any of these to allow them in validation *and* automatically add them as a choice
	 * in the options page
	 */
	public static $twitter_card_types = array(
		'summary'             => '',
		'summary_large_image' => '',
		// 'photo'               => '',
		// 'gallery'             => '',
		// 'app'                 => '',
		// 'player'              => '',
		// 'product'             => '',
	);


	/**
	 * Get the singleton instance of this class
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
	 * Translate/set strings used in the option defaults
	 *
	 * @return void
	 */
	public function translate_defaults() {
		/* Auto-magically set the fb connect key */
		$this->defaults['fbconnectkey'] = self::get_fbconnectkey();

		self::$twitter_card_types['summary']             = __( 'Summary', 'wordpress-seo' );
		self::$twitter_card_types['summary_large_image'] = __( 'Summary with large image', 'wordpress-seo' );
	}


	/**
	 * Get a Facebook connect key for the blog
	 *
	 * @static
	 * @return string
	 */
	public static function get_fbconnectkey() {
		return md5( get_bloginfo( 'url' ) . rand() );
	}


	/**
	 * Validate the option
	 *
	 * @param  array $dirty New value for the option.
	 * @param  array $clean Clean value for the option, normally the defaults.
	 * @param  array $old   Old value of the option.
	 *
	 * @return  array      Validated clean value for the option to be saved to the database
	 */
	protected function validate_option( $dirty, $clean, $old ) {

		foreach ( $clean as $key => $value ) {
			switch ( $key ) {
				/* Automagic Facebook connect key */
				case 'fbconnectkey':
					if ( ( isset( $old[ $key ] ) && $old[ $key ] !== '' ) && preg_match( '`^[a-f0-9]{32}$`', $old[ $key ] ) > 0 ) {
						$clean[ $key ] = $old[ $key ];
					}
					else {
						$clean[ $key ] = self::get_fbconnectkey();
					}
					break;


				/* Will not always exist in form */
				case 'fb_admins':
					if ( isset( $dirty[ $key ] ) && is_array( $dirty[ $key ] ) ) {
						if ( $dirty[ $key ] === array() ) {
							$clean[ $key ] = array();
						}
						else {
							foreach ( $dirty[ $key ] as $user_id => $fb_array ) {
								/*
								 * @todo [JRF/JRF => Yoast/whomever] add user_id validation -
								 * are these WP user-ids or FB user-ids ? Probably FB user-ids,
								 * if so, find out the rules for FB user-ids
								 */
								if ( is_array( $fb_array ) && $fb_array !== array() ) {
									foreach ( $fb_array as $fb_key => $fb_value ) {
										switch ( $fb_key ) {
											case 'name':
												/**
												 * @todo [JRF => whomever] add validation for name based
												 * on rules if there are any
												 * Input comes from: $_GET['userrealname']
												 */
												$clean[ $key ][ $user_id ][ $fb_key ] = sanitize_text_field( $fb_value );
												break;

											case 'link':
												$clean[ $key ][ $user_id ][ $fb_key ] = WPSEO_Utils::sanitize_url( $fb_value );
												break;
										}
									}
								}
							}
							unset( $user_id, $fb_array, $fb_key, $fb_value );
						}
					}
					elseif ( isset( $old[ $key ] ) && is_array( $old[ $key ] ) ) {
						$clean[ $key ] = $old[ $key ];
					}
					break;

				/* text fields */
				case 'og_frontpage_desc':
				case 'og_frontpage_title':
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
						$clean[ $key ] = WPSEO_Utils::sanitize_text_field( $dirty[ $key ] );
					}
					break;


				/* url text fields - no ftp allowed */
				case 'facebook_site':
				case 'instagram_url':
				case 'linkedin_url':
				case 'myspace_url':
				case 'pinterest_url':
				case 'plus-publisher':
				case 'og_default_image':
				case 'og_frontpage_image':
				case 'youtube_url':
				case 'google_plus_url':
					$this->validate_url( $key, $dirty, $old, $clean );
					break;

				case 'pinterestverify':
					$this->validate_verification_string( $key, $dirty, $old, $clean );
					break;

				/* twitter user name */
				case 'twitter_site':
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
						$twitter_id = sanitize_text_field( ltrim( $dirty[ $key ], '@' ) );
						/**
						 * From the Twitter documentation about twitter screen names:
						 * Typically a maximum of 15 characters long, but some historical accounts
						 * may exist with longer names.
						 * A username can only contain alphanumeric characters (letters A-Z, numbers 0-9)
						 * with the exception of underscores
						 * @link https://support.twitter.com/articles/101299-why-can-t-i-register-certain-usernames
						 * @link https://dev.twitter.com/docs/platform-objects/users
						 */
						if ( preg_match( '`^[A-Za-z0-9_]{1,25}$`', $twitter_id ) ) {
							$clean[ $key ] = $twitter_id;
						}
						elseif ( preg_match( '`^http(?:s)?://(?:www\.)?twitter\.com/(?P<handle>[A-Za-z0-9_]{1,25})/?$`', $twitter_id, $matches ) ) {
							$clean[ $key ] = $matches['handle'];
						}
						else {
							if ( isset( $old[ $key ] ) && $old[ $key ] !== '' ) {
								$twitter_id = sanitize_text_field( ltrim( $old[ $key ], '@' ) );
								if ( preg_match( '`^[A-Za-z0-9_]{1,25}$`', $twitter_id ) ) {
									$clean[ $key ] = $twitter_id;
								}
							}
							if ( function_exists( 'add_settings_error' ) ) {
								add_settings_error(
									$this->group_name, // Slug title of the setting.
									'_' . $key, // Suffix-id for the error message box.
									sprintf( __( '%s does not seem to be a valid Twitter user-id. Please correct.', 'wordpress-seo' ), '<strong>' . esc_html( sanitize_text_field( $dirty[ $key ] ) ) . '</strong>' ), // The error message.
									'error' // Error type, either 'error' or 'updated'.
								);
							}
						}
						unset( $twitter_id );
					}
					break;

				case 'twitter_card_type':
					if ( isset( $dirty[ $key ], self::$twitter_card_types[ $dirty[ $key ] ] ) && $dirty[ $key ] !== '' ) {
						$clean[ $key ] = $dirty[ $key ];
					}
					break;

				/* boolean fields */
				case 'googleplus':
				case 'opengraph':
				case 'twitter':
					$clean[ $key ] = ( isset( $dirty[ $key ] ) ? WPSEO_Utils::validate_bool( $dirty[ $key ] ) : false );
					break;
			}
		}

		/**
		 * Only validate 'fbadminapp', so leave the clean default.
		 */
		if ( isset( $dirty['fbadminapp'] ) && ! empty( $dirty['fbadminapp'] ) ) {
			$clean['fbadminapp'] = $dirty['fbadminapp'];
		}


		return $clean;
	}


	/**
	 * Clean a given option value
	 *
	 * @param  array  $option_value          Old (not merged with defaults or filtered) option value to
	 *                                       clean according to the rules for this option.
	 * @param  string $current_version       (optional) Version from which to upgrade, if not set,
	 *                                       version specific upgrades will be disregarded.
	 * @param  array  $all_old_option_values (optional) Only used when importing old options to have
	 *                                       access to the real old values, in contrast to the saved ones.
	 *
	 * @return  array            Cleaned option
	 */
	protected function clean_option( $option_value, $current_version = null, $all_old_option_values = null ) {

		/* Move options from very old option to this one */
		$old_option = null;
		if ( isset( $all_old_option_values ) ) {
			// Ok, we have an import.
			if ( isset( $all_old_option_values['wpseo_indexation'] ) && is_array( $all_old_option_values['wpseo_indexation'] ) && $all_old_option_values['wpseo_indexation'] !== array() ) {
				$old_option = $all_old_option_values['wpseo_indexation'];
			}
		}
		else {
			$old_option = get_option( 'wpseo_indexation' );
		}

		if ( is_array( $old_option ) && $old_option !== array() ) {
			$move = array(
				'opengraph',
				'fb_adminid',
				'fb_appid',
			);
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
