<?php

/**
 * Manages access to email provider class instances.
 */
class ET_Core_API_Email_Providers {

	private static $_metadata;
	private static $_names;
	private static $_slugs;

	public static $providers = array();

	public function __construct() {
		if ( null === self::$_metadata ) {
			self::$_metadata             = et_core_get_components_metadata();
			self::$_slugs                = self::$_metadata['slugs'];
			self::$_names['official']    = self::$_metadata['names'];
			self::$_names['third-party'] = et_core_get_component_names( 'third-party', 'api/email' );
		}
	}

	/**
	 * Returns the email provider accounts array from core.
	 *
	 * @return array|mixed
	 */
	public function accounts() {
		return ET_Core_API_Email_Provider::get_accounts();
	}

	/**
	 * @see {@link \ET_Core_API_Email_Provider::account_exists()}
	 */
	public function account_exists( $provider, $account_name ) {
		return ET_Core_API_Email_Provider::account_exists( $provider, $account_name );
	}

	/**
	 * Get class instance for a provider. Instance will be created if necessary.
	 *
	 * @param string $name_or_slug The provider's name or slug.
	 * @param string $account_name The identifier for the desired account with the provider.
	 * @param string $owner        The owner for the instance.
	 *
	 * @return bool|ET_Core_API_Email_Provider The provider instance or `false` if not found.
	 */
	public function get( $name_or_slug, $account_name, $owner = 'ET_Core' ) {
		$name_or_slug   = str_replace( array( '_', ' ' ), '', $name_or_slug );
		$is_official    = isset( self::$_metadata[ $name_or_slug ] );
		$is_third_party = in_array( $name_or_slug, self::$_names['third-party'] );

		if ( ! $is_official && ! $is_third_party ) {
			return false;
		} else if ( $is_official ) {
			// Make sure we have the component name
			$class_name = self::$_metadata[ $name_or_slug ];
			$name       = self::$_metadata[ $class_name ]['name'];
		} else {
			// This is a 3rd-party component, we already have the name
			$name       = $name_or_slug;
			$components = et_core_get_third_party_components( 'api/email' );
		}

		if ( isset( self::$providers[ $name ][ $owner ] ) ) {
			return self::$providers[ $name ][ $owner ];
		}

		if ( $is_official ) {
			// We have an official component for this provider.
			self::$providers[ $name ][ $owner ] = new $class_name( $owner, $account_name );
		} else {
			// We have a 3rd-party component for this provider.
			self::$providers[ $name ][ $owner ] = $components[ $name ];
		}

		return self::$providers[ $name ][ $owner ];
	}

	/**
	 * Returns the names of available providers. List can optionally be filtered.
	 *
	 * @param string $type The component type to include ('official'|'third-party'|'all'). Default is 'all'.
	 *
	 * @return array
	 */
	public function names( $type = 'all' ) {
		if ( 'all' === $type ) {
			return self::$_names['third-party'] + self::$_names['official'];
		}

		return self::$_names[ $type ];
	}

	/**
	 * @see {@link \ET_Core_API_Email_Provider::remove_account()}
	 */
	public function remove_account( $provider, $account_name ) {
		ET_Core_API_Email_Provider::remove_account( $provider, $account_name );
	}

	/**
	 * @see {@link \ET_Core_API_Email_Provider::update_account()}
	 */
	public function update_account( $provider, $account, $data ) {
		ET_Core_API_Email_Provider::update_account( $provider, $account, $data );
	}
}
