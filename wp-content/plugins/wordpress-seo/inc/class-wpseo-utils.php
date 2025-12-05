<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals
 * @since   1.8.0
 */

use Yoast\WP\SEO\Integrations\Feature_Flag_Integration;

/**
 * Group of utility methods for use by WPSEO.
 * All methods are static, this is just a sort of namespacing class wrapper.
 */
class WPSEO_Utils {

	/**
	 * Whether the PHP filter extension is enabled.
	 *
	 * @since 1.8.0
	 *
	 * @var bool
	 */
	public static $has_filters;

	/**
	 * Check whether file editing is allowed for the .htaccess and robots.txt files.
	 *
	 * {@internal current_user_can() checks internally whether a user is on wp-ms and adjusts accordingly.}}
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	public static function allow_system_file_edit() {
		$allowed = true;

		if ( current_user_can( 'edit_files' ) === false ) {
			$allowed = false;
		}

		/**
		 * Filter: 'wpseo_allow_system_file_edit' - Allow developers to change whether the editing of
		 * .htaccess and robots.txt is allowed.
		 *
		 * @param bool $allowed Whether file editing is allowed.
		 */
		return apply_filters( 'wpseo_allow_system_file_edit', $allowed );
	}

	/**
	 * Check if the web server is running on Apache or compatible (LiteSpeed).
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	public static function is_apache() {
		if ( ! isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
			return false;
		}

		$software = sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) );

		return stripos( $software, 'apache' ) !== false || stripos( $software, 'litespeed' ) !== false;
	}

	/**
	 * Check if the web server is running on Nginx.
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	public static function is_nginx() {
		if ( ! isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
			return false;
		}

		$software = sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) );

		return stripos( $software, 'nginx' ) !== false;
	}

	/**
	 * Check whether a url is relative.
	 *
	 * @since 1.8.0
	 *
	 * @param string $url URL string to check.
	 *
	 * @return bool
	 */
	public static function is_url_relative( $url ) {
		return YoastSEO()->helpers->url->is_relative( $url );
	}

	/**
	 * Recursively trim whitespace round a string value or of string values within an array.
	 * Only trims strings to avoid typecasting a variable (to string).
	 *
	 * @since 1.8.0
	 *
	 * @param mixed $value Value to trim or array of values to trim.
	 *
	 * @return mixed Trimmed value or array of trimmed values.
	 */
	public static function trim_recursive( $value ) {
		if ( is_string( $value ) ) {
			$value = trim( $value );
		}
		elseif ( is_array( $value ) ) {
			$value = array_map( [ self::class, 'trim_recursive' ], $value );
		}

		return $value;
	}

	/**
	 * Emulate the WP native sanitize_text_field function in a %%variable%% safe way.
	 *
	 * Sanitize a string from user input or from the db.
	 *
	 * - Check for invalid UTF-8;
	 * - Convert single < characters to entity;
	 * - Strip all tags;
	 * - Remove line breaks, tabs and extra white space;
	 * - Strip octets - BUT DO NOT REMOVE (part of) VARIABLES WHICH WILL BE REPLACED.
	 *
	 * @link https://core.trac.wordpress.org/browser/trunk/src/wp-includes/formatting.php for the original.
	 *
	 * @since 1.8.0
	 *
	 * @param string $value String value to sanitize.
	 *
	 * @return string
	 */
	public static function sanitize_text_field( $value ) {
		$filtered = wp_check_invalid_utf8( $value );

		if ( strpos( $filtered, '<' ) !== false ) {
			$filtered = wp_pre_kses_less_than( $filtered );
			// This will strip extra whitespace for us.
			$filtered = wp_strip_all_tags( $filtered, true );
		}
		else {
			$filtered = trim( preg_replace( '`[\r\n\t ]+`', ' ', $filtered ) );
		}

		$found = false;
		while ( preg_match( '`[^%](%[a-f0-9]{2})`i', $filtered, $match ) ) {
			$filtered = str_replace( $match[1], '', $filtered );
			$found    = true;
		}
		unset( $match );

		if ( $found ) {
			// Strip out the whitespace that may now exist after removing the octets.
			$filtered = trim( preg_replace( '` +`', ' ', $filtered ) );
		}

		/**
		 * Filter a sanitized text field string.
		 *
		 * @since WP 2.9.0
		 *
		 * @param string $filtered The sanitized string.
		 * @param string $str      The string prior to being sanitized.
		 */
		return apply_filters( 'sanitize_text_field', $filtered, $value ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- Using WP native filter.
	}

	/**
	 * Sanitize a url for saving to the database.
	 * Not to be confused with the old native WP function.
	 *
	 * @since 1.8.0
	 *
	 * @param string $value             String URL value to sanitize.
	 * @param array  $allowed_protocols Optional set of allowed protocols.
	 *
	 * @return string
	 */
	public static function sanitize_url( $value, $allowed_protocols = [ 'http', 'https' ] ) {

		$url   = '';
		$parts = wp_parse_url( $value );

		if ( isset( $parts['scheme'], $parts['host'] ) ) {
			$url = $parts['scheme'] . '://';

			if ( isset( $parts['user'] ) ) {
				$url .= rawurlencode( $parts['user'] );
				$url .= isset( $parts['pass'] ) ? ':' . rawurlencode( $parts['pass'] ) : '';
				$url .= '@';
			}

			$parts['host'] = preg_replace(
				'`[^a-z0-9-.:\[\]\\x80-\\xff]`',
				'',
				strtolower( $parts['host'] )
			);

			$url .= $parts['host'] . ( isset( $parts['port'] ) ? ':' . intval( $parts['port'] ) : '' );
		}

		if ( isset( $parts['path'] ) && strpos( $parts['path'], '/' ) === 0 ) {
			$path = explode( '/', wp_strip_all_tags( $parts['path'] ) );
			$path = self::sanitize_encoded_text_field( $path );
			$url .= str_replace( '%40', '@', implode( '/', $path ) );
		}

		if ( ! $url ) {
			return '';
		}

		if ( isset( $parts['query'] ) ) {
			wp_parse_str( $parts['query'], $parsed_query );

			$parsed_query = array_combine(
				self::sanitize_encoded_text_field( array_keys( $parsed_query ) ),
				self::sanitize_encoded_text_field( array_values( $parsed_query ) )
			);

			$url = add_query_arg( $parsed_query, $url );
		}

		if ( isset( $parts['fragment'] ) ) {
			$url .= '#' . self::sanitize_encoded_text_field( $parts['fragment'] );
		}

		if ( strpos( $url, '%' ) !== false ) {
			$url = preg_replace_callback(
				'`%[a-fA-F0-9]{2}`',
				static function ( $octects ) {
					return strtolower( $octects[0] );
				},
				$url
			);
		}

		return esc_url_raw( $url, $allowed_protocols );
	}

	/**
	 * Decode, sanitize and encode the array of strings or the string.
	 *
	 * @since 13.3
	 *
	 * @param array|string $value The value to sanitize and encode.
	 *
	 * @return array|string The sanitized value.
	 */
	public static function sanitize_encoded_text_field( $value ) {
		if ( is_array( $value ) ) {
			return array_map( [ self::class, 'sanitize_encoded_text_field' ], $value );
		}

		return rawurlencode( sanitize_text_field( rawurldecode( $value ) ) );
	}

	/**
	 * Validate a value as boolean.
	 *
	 * @since 1.8.0
	 *
	 * @param mixed $value Value to validate.
	 *
	 * @return bool
	 */
	public static function validate_bool( $value ) {
		if ( ! isset( self::$has_filters ) ) {
			self::$has_filters = extension_loaded( 'filter' );
		}

		if ( self::$has_filters ) {
			return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
		}
		else {
			return self::emulate_filter_bool( $value );
		}
	}

	/**
	 * Cast a value to bool.
	 *
	 * @since 1.8.0
	 *
	 * @param mixed $value Value to cast.
	 *
	 * @return bool
	 */
	public static function emulate_filter_bool( $value ) {
		$true  = [
			'1',
			'true',
			'True',
			'TRUE',
			'y',
			'Y',
			'yes',
			'Yes',
			'YES',
			'on',
			'On',
			'ON',
		];
		$false = [
			'0',
			'false',
			'False',
			'FALSE',
			'n',
			'N',
			'no',
			'No',
			'NO',
			'off',
			'Off',
			'OFF',
		];

		if ( is_bool( $value ) ) {
			return $value;
		}
		elseif ( is_int( $value ) && ( $value === 0 || $value === 1 ) ) {
			return (bool) $value;
		}
		elseif ( ( is_float( $value ) && ! is_nan( $value ) ) && ( $value === (float) 0 || $value === (float) 1 ) ) {
			return (bool) $value;
		}
		elseif ( is_string( $value ) ) {
			$value = trim( $value );
			if ( in_array( $value, $true, true ) ) {
				return true;
			}
			elseif ( in_array( $value, $false, true ) ) {
				return false;
			}
			else {
				return false;
			}
		}

		return false;
	}

	/**
	 * Validate a value as integer.
	 *
	 * @since 1.8.0
	 *
	 * @param mixed $value Value to validate.
	 *
	 * @return int|bool Int or false in case of failure to convert to int.
	 */
	public static function validate_int( $value ) {
		if ( ! isset( self::$has_filters ) ) {
			self::$has_filters = extension_loaded( 'filter' );
		}

		if ( self::$has_filters ) {
			return filter_var( $value, FILTER_VALIDATE_INT );
		}
		else {
			return self::emulate_filter_int( $value );
		}
	}

	/**
	 * Cast a value to integer.
	 *
	 * @since 1.8.0
	 *
	 * @param mixed $value Value to cast.
	 *
	 * @return int|bool
	 */
	public static function emulate_filter_int( $value ) {
		if ( is_int( $value ) ) {
			return $value;
		}
		elseif ( is_float( $value ) ) {
			// phpcs:ignore Universal.Operators.StrictComparisons -- Purposeful loose comparison.
			if ( (int) $value == $value && ! is_nan( $value ) ) {
				return (int) $value;
			}
			else {
				return false;
			}
		}
		elseif ( is_string( $value ) ) {
			$value = trim( $value );
			if ( $value === '' ) {
				return false;
			}
			elseif ( ctype_digit( $value ) ) {
				return (int) $value;
			}
			elseif ( strpos( $value, '-' ) === 0 && ctype_digit( substr( $value, 1 ) ) ) {
				return (int) $value;
			}
			else {
				return false;
			}
		}

		return false;
	}

	/**
	 * Clears the WP or W3TC cache depending on which is used.
	 *
	 * @since 1.8.0
	 *
	 * @return void
	 */
	public static function clear_cache() {
		if ( function_exists( 'w3tc_flush_posts' ) ) {
			w3tc_flush_posts();
		}
		elseif ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
		}
	}

	/**
	 * Clear rewrite rules.
	 *
	 * @since 1.8.0
	 *
	 * @return void
	 */
	public static function clear_rewrites() {
		update_option( 'rewrite_rules', '' );
	}

	/**
	 * Do simple reliable math calculations without the risk of wrong results.
	 *
	 * In the rare case that the bcmath extension would not be loaded, it will return the normal calculation results.
	 *
	 * @link http://floating-point-gui.de/
	 * @link http://php.net/language.types.float.php See the big red warning.
	 *
	 * @since 1.5.0
	 * @since 1.8.0 Moved from stand-alone function to this class.
	 *
	 * @param mixed  $number1   Scalar (string/int/float/bool).
	 * @param string $action    Calculation action to execute. Valid input:
	 *                          '+' or 'add' or 'addition',
	 *                          '-' or 'sub' or 'subtract',
	 *                          '*' or 'mul' or 'multiply',
	 *                          '/' or 'div' or 'divide',
	 *                          '%' or 'mod' or 'modulus'
	 *                          '=' or 'comp' or 'compare'.
	 * @param mixed  $number2   Scalar (string/int/float/bool).
	 * @param bool   $round     Whether or not to round the result. Defaults to false.
	 *                          Will be disregarded for a compare operation.
	 * @param int    $decimals  Decimals for rounding operation. Defaults to 0.
	 * @param int    $precision Calculation precision. Defaults to 10.
	 *
	 * @return mixed Calculation Result or false if either or the numbers isn't scalar or
	 *               an invalid operation was passed.
	 *               - For compare the result will always be an integer.
	 *               - For all other operations, the result will either be an integer (preferred)
	 *                 or a float.
	 */
	public static function calc( $number1, $action, $number2, $round = false, $decimals = 0, $precision = 10 ) {
		static $bc;

		if ( ! is_scalar( $number1 ) || ! is_scalar( $number2 ) ) {
			return false;
		}

		if ( ! isset( $bc ) ) {
			$bc = extension_loaded( 'bcmath' );
		}

		if ( $bc ) {
			$number1 = number_format( $number1, 10, '.', '' );
			$number2 = number_format( $number2, 10, '.', '' );
		}

		$result  = null;
		$compare = false;

		switch ( $action ) {
			case '+':
			case 'add':
			case 'addition':
				$result = ( $bc ) ? bcadd( $number1, $number2, $precision ) /* string */ : ( $number1 + $number2 );
				break;

			case '-':
			case 'sub':
			case 'subtract':
				$result = ( $bc ) ? bcsub( $number1, $number2, $precision ) /* string */ : ( $number1 - $number2 );
				break;

			case '*':
			case 'mul':
			case 'multiply':
				$result = ( $bc ) ? bcmul( $number1, $number2, $precision ) /* string */ : ( $number1 * $number2 );
				break;

			case '/':
			case 'div':
			case 'divide':
				if ( $bc ) {
					$result = bcdiv( $number1, $number2, $precision ); // String, or NULL if right_operand is 0.
				}
				elseif ( $number2 != 0 ) { // phpcs:ignore Universal.Operators.StrictComparisons -- Purposeful loose comparison.
					$result = ( $number1 / $number2 );
				}

				if ( ! isset( $result ) ) {
					$result = 0;
				}
				break;

			case '%':
			case 'mod':
			case 'modulus':
				if ( $bc ) {
					$result = bcmod( $number1, $number2 ); // String, or NULL if modulus is 0.
				}
				elseif ( $number2 != 0 ) { // phpcs:ignore Universal.Operators.StrictComparisons -- Purposeful loose comparison.
					$result = ( $number1 % $number2 );
				}

				if ( ! isset( $result ) ) {
					$result = 0;
				}
				break;

			case '=':
			case 'comp':
			case 'compare':
				$compare = true;
				if ( $bc ) {
					$result = bccomp( $number1, $number2, $precision ); // Returns int 0, 1 or -1.
				}
				else {
					// phpcs:ignore Universal.Operators.StrictComparisons -- Purposeful loose comparison.
					$result = ( $number1 == $number2 ) ? 0 : ( ( $number1 > $number2 ) ? 1 : -1 );
				}
				break;
		}

		if ( isset( $result ) ) {
			if ( $compare === false ) {
				if ( $round === true ) {
					$result = round( floatval( $result ), $decimals );
					if ( $decimals === 0 ) {
						$result = (int) $result;
					}
				}
				else {
					// phpcs:ignore Universal.Operators.StrictComparisons -- Purposeful loose comparison.
					$result = ( intval( $result ) == $result ) ? intval( $result ) : floatval( $result );
				}
			}

			return $result;
		}

		return false;
	}

	/**
	 * Trim whitespace and NBSP (Non-breaking space) from string.
	 *
	 * @since 2.0.0
	 *
	 * @param string $text String input to trim.
	 *
	 * @return string
	 */
	public static function trim_nbsp_from_string( $text ) {
		$find = [ '&nbsp;', chr( 0xC2 ) . chr( 0xA0 ) ];
		$text = str_replace( $find, ' ', $text );
		$text = trim( $text );

		return $text;
	}

	/**
	 * Check if a string is a valid datetime.
	 *
	 * @since 2.0.0
	 *
	 * @param string $datetime String input to check as valid input for DateTime class.
	 *
	 * @return bool
	 */
	public static function is_valid_datetime( $datetime ) {
		return YoastSEO()->helpers->date->is_valid_datetime( $datetime );
	}

	/**
	 * Format the URL to be sure it is okay for using as a redirect url.
	 *
	 * This method will parse the URL and combine them in one string.
	 *
	 * @since 2.3.0
	 *
	 * @param string $url URL string.
	 *
	 * @return mixed
	 */
	public static function format_url( $url ) {
		$parsed_url = wp_parse_url( $url );

		$formatted_url = '';
		if ( ! empty( $parsed_url['path'] ) ) {
			$formatted_url = $parsed_url['path'];
		}

		// Prepend a slash if first char != slash.
		if ( stripos( $formatted_url, '/' ) !== 0 ) {
			$formatted_url = '/' . $formatted_url;
		}

		// Append 'query' string if it exists.
		if ( ! empty( $parsed_url['query'] ) ) {
			$formatted_url .= '?' . $parsed_url['query'];
		}

		return apply_filters( 'wpseo_format_admin_url', $formatted_url );
	}

	/**
	 * Retrieves the sitename.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public static function get_site_name() {
		return YoastSEO()->helpers->site->get_site_name();
	}

	/**
	 * Check if the current opened page is a Yoast SEO page.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public static function is_yoast_seo_page() {
		return YoastSEO()->helpers->current_page->is_yoast_seo_page();
	}

	/**
	 * Check if the current opened page belongs to Yoast SEO Free.
	 *
	 * @since 3.3.0
	 *
	 * @param string $current_page The current page the user is on.
	 *
	 * @return bool
	 */
	public static function is_yoast_seo_free_page( $current_page ) {
		$yoast_seo_free_pages = [
			'wpseo_tools',
			'wpseo_search_console',
		];

		return in_array( $current_page, $yoast_seo_free_pages, true );
	}

	/**
	 * Determine if Yoast SEO is in development mode?
	 *
	 * Inspired by JetPack (https://github.com/Automattic/jetpack/blob/master/class.jetpack.php#L1383-L1406).
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public static function is_development_mode() {
		$development_mode = false;

		if ( defined( 'YOAST_ENVIRONMENT' ) && YOAST_ENVIRONMENT === 'development' ) {
			$development_mode = true;
		}
		elseif ( defined( 'WPSEO_DEBUG' ) ) {
			$development_mode = WPSEO_DEBUG;
		}
		elseif ( site_url() && strpos( site_url(), '.' ) === false ) {
			$development_mode = true;
		}

		/**
		 * Filter the Yoast SEO development mode.
		 *
		 * @since 3.0
		 *
		 * @param bool $development_mode Is Yoast SEOs development mode active.
		 */
		return apply_filters( 'yoast_seo_development_mode', $development_mode );
	}

	/**
	 * Retrieve home URL with proper trailing slash.
	 *
	 * @since 3.3.0
	 *
	 * @param string      $path   Path relative to home URL.
	 * @param string|null $scheme Scheme to apply.
	 *
	 * @return string Home URL with optional path, appropriately slashed if not.
	 */
	public static function home_url( $path = '', $scheme = null ) {
		return YoastSEO()->helpers->url->home( $path, $scheme );
	}

	/**
	 * Checks if the WP-REST-API is available.
	 *
	 * @since 3.6
	 * @since 3.7 Introduced the $minimum_version parameter.
	 *
	 * @param string $minimum_version The minimum version the API should be.
	 *
	 * @return bool Returns true if the API is available.
	 */
	public static function is_api_available( $minimum_version = '2.0' ) {
		return ( defined( 'REST_API_VERSION' )
			&& version_compare( REST_API_VERSION, $minimum_version, '>=' ) );
	}

	/**
	 * Determine whether or not the metabox should be displayed for a post type.
	 *
	 * @param string|null $post_type Optional. The post type to check the visibility of the metabox for.
	 *
	 * @return bool Whether or not the metabox should be displayed.
	 */
	protected static function display_post_type_metabox( $post_type = null ) {
		if ( ! isset( $post_type ) ) {
			$post_type = get_post_type();
		}

		if ( ! isset( $post_type ) || ! WPSEO_Post_Type::is_post_type_accessible( $post_type ) ) {
			return false;
		}

		if ( $post_type === 'attachment' && WPSEO_Options::get( 'disable-attachment' ) ) {
			return false;
		}

		return apply_filters( 'wpseo_enable_editor_features_' . $post_type, WPSEO_Options::get( 'display-metabox-pt-' . $post_type ) );
	}

	/**
	 * Determine whether or not the metabox should be displayed for a taxonomy.
	 *
	 * @param string|null $taxonomy Optional. The post type to check the visibility of the metabox for.
	 *
	 * @return bool Whether or not the metabox should be displayed.
	 */
	protected static function display_taxonomy_metabox( $taxonomy = null ) {
		if ( ! isset( $taxonomy ) || ! in_array( $taxonomy, get_taxonomies( [ 'public' => true ], 'names' ), true ) ) {
			return false;
		}

		return WPSEO_Options::get( 'display-metabox-tax-' . $taxonomy );
	}

	/**
	 * Determines whether the metabox is active for the given identifier and type.
	 *
	 * @param string $identifier The identifier to check for.
	 * @param string $type       The type to check for.
	 *
	 * @return bool Whether or not the metabox is active.
	 */
	public static function is_metabox_active( $identifier, $type ) {
		if ( $type === 'post_type' ) {
			return self::display_post_type_metabox( $identifier );
		}

		if ( $type === 'taxonomy' ) {
			return self::display_taxonomy_metabox( $identifier );
		}

		return false;
	}

	/**
	 * Determines whether the plugin is active for the entire network.
	 *
	 * @return bool Whether the plugin is network-active.
	 */
	public static function is_plugin_network_active() {
		return YoastSEO()->helpers->url->is_plugin_network_active();
	}

	/**
	 * Gets the type of the current post.
	 *
	 * @return string The post type, or an empty string.
	 */
	public static function get_post_type() {
		$wp_screen = get_current_screen();

		if ( $wp_screen !== null && ! empty( $wp_screen->post_type ) ) {
			return $wp_screen->post_type;
		}
		return '';
	}

	/**
	 * Gets the type of the current page.
	 *
	 * @return string Returns 'post' if the current page is a post edit page. Taxonomy in other cases.
	 */
	public static function get_page_type() {
		global $pagenow;
		if ( WPSEO_Metabox::is_post_edit( $pagenow ) ) {
			return 'post';
		}

		return 'taxonomy';
	}

	/**
	 * Getter for the Adminl10n array. Applies the wpseo_admin_l10n filter.
	 *
	 * @return array The Adminl10n array.
	 */
	public static function get_admin_l10n() {
		$post_type = self::get_post_type();
		$page_type = self::get_page_type();

		$label_object = false;
		$no_index     = false;

		if ( $page_type === 'post' ) {
			$label_object = get_post_type_object( $post_type );
			$no_index     = WPSEO_Options::get( 'noindex-' . $post_type, false );
		}
		else {
			$label_object = WPSEO_Taxonomy::get_labels();

			$wp_screen = get_current_screen();

			if ( $wp_screen !== null && ! empty( $wp_screen->taxonomy ) ) {
				$taxonomy_slug = $wp_screen->taxonomy;
				$no_index      = WPSEO_Options::get( 'noindex-tax-' . $taxonomy_slug, false );
			}
		}

		$wpseo_admin_l10n = [
			'displayAdvancedTab'    => WPSEO_Capability_Utils::current_user_can( 'wpseo_edit_advanced_metadata' ) || ! WPSEO_Options::get( 'disableadvanced_meta' ),
			'noIndex'               => (bool) $no_index,
			'isPostType'            => (bool) get_post_type(),
			'postType'              => get_post_type(),
			'postTypeNamePlural'    => ( $page_type === 'post' ) ? $label_object->label : $label_object->name,
			'postTypeNameSingular'  => ( $page_type === 'post' ) ? $label_object->labels->singular_name : $label_object->singular_name,
			'isBreadcrumbsDisabled' => WPSEO_Options::get( 'breadcrumbs-enable', false ) !== true && ! current_theme_supports( 'yoast-seo-breadcrumbs' ),
			'isAiFeatureActive'     => (bool) WPSEO_Options::get( 'enable_ai_generator' ),
		];

		$additional_entries = apply_filters( 'wpseo_admin_l10n', [] );
		if ( is_array( $additional_entries ) ) {
			$wpseo_admin_l10n = array_merge( $wpseo_admin_l10n, $additional_entries );
		}

		return $wpseo_admin_l10n;
	}

	/**
	 * Retrieves the analysis worker log level. Defaults to errors only.
	 *
	 * Uses bool YOAST_SEO_DEBUG as flag to enable logging. Off equals ERROR.
	 * Uses string YOAST_SEO_DEBUG_ANALYSIS_WORKER as log level for the Analysis
	 * Worker. Defaults to INFO.
	 * Can be: TRACE, DEBUG, INFO, WARN or ERROR.
	 *
	 * @return string The log level to use.
	 */
	public static function get_analysis_worker_log_level() {
		if ( defined( 'YOAST_SEO_DEBUG' ) && YOAST_SEO_DEBUG ) {
			return defined( 'YOAST_SEO_DEBUG_ANALYSIS_WORKER' ) ? YOAST_SEO_DEBUG_ANALYSIS_WORKER : 'INFO';
		}

		return 'ERROR';
	}

	/**
	 * Returns the unfiltered home URL.
	 *
	 * In case WPML is installed, returns the original home_url and not the WPML version.
	 * In case of a multisite setup we return the network_home_url.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return string The home url.
	 */
	public static function get_home_url() {
		return YoastSEO()->helpers->url->network_safe_home_url();
	}

	/**
	 * Prepares data for outputting as JSON.
	 *
	 * @param array $data The data to format.
	 *
	 * @return string|false The prepared JSON string.
	 */
	public static function format_json_encode( $data ) {
		$flags = ( JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		if ( self::is_development_mode() ) {
			$flags = ( $flags | JSON_PRETTY_PRINT );

			/**
			 * Filter the Yoast SEO development mode.
			 *
			 * @param array $data Allows filtering of the JSON data for debug purposes.
			 */
			$data = apply_filters( 'wpseo_debug_json_data', $data );
		}

		// phpcs:ignore Yoast.Yoast.JsonEncodeAlternative.FoundWithAdditionalParams -- This is the definition of format_json_encode.
		return wp_json_encode( $data, $flags );
	}

	/**
	 * Extends the allowed post tags with accessibility-related attributes.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param array $allowed_post_tags The allowed post tags.
	 *
	 * @return array The allowed tags including post tags, input tags and select tags.
	 */
	public static function extend_kses_post_with_a11y( $allowed_post_tags ) {
		static $a11y_tags;

		if ( isset( $a11y_tags ) === false ) {
			$a11y_tags = [
				'button'   => [
					'aria-expanded' => true,
					'aria-controls' => true,
				],
				'div'      => [
					'tabindex' => true,
				],
				// Below are attributes that are needed for backwards compatibility (WP < 5.1).
				'span'     => [
					'aria-hidden' => true,
				],
				'input'    => [
					'aria-describedby' => true,
				],
				'select'   => [
					'aria-describedby' => true,
				],
				'textarea' => [
					'aria-describedby' => true,
				],
			];

			// Add the global allowed attributes to each html element.
			$a11y_tags = array_map( '_wp_add_global_attributes', $a11y_tags );
		}

		return array_merge_recursive( $allowed_post_tags, $a11y_tags );
	}

	/**
	 * Extends the allowed post tags with input, select and option tags.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param array $allowed_post_tags The allowed post tags.
	 *
	 * @return array The allowed tags including post tags, input tags, select tags and option tags.
	 */
	public static function extend_kses_post_with_forms( $allowed_post_tags ) {
		static $input_tags;

		if ( isset( $input_tags ) === false ) {
			$input_tags = [
				'input' => [
					'accept'          => true,
					'accesskey'       => true,
					'align'           => true,
					'alt'             => true,
					'autocomplete'    => true,
					'autofocus'       => true,
					'checked'         => true,
					'contenteditable' => true,
					'dirname'         => true,
					'disabled'        => true,
					'draggable'       => true,
					'dropzone'        => true,
					'form'            => true,
					'formaction'      => true,
					'formenctype'     => true,
					'formmethod'      => true,
					'formnovalidate'  => true,
					'formtarget'      => true,
					'height'          => true,
					'hidden'          => true,
					'lang'            => true,
					'list'            => true,
					'max'             => true,
					'maxlength'       => true,
					'min'             => true,
					'multiple'        => true,
					'name'            => true,
					'pattern'         => true,
					'placeholder'     => true,
					'readonly'        => true,
					'required'        => true,
					'size'            => true,
					'spellcheck'      => true,
					'src'             => true,
					'step'            => true,
					'tabindex'        => true,
					'translate'       => true,
					'type'            => true,
					'value'           => true,
					'width'           => true,

					/*
					 * Below are attributes that are needed for backwards compatibility (WP < 5.1).
					 * They are used for the social media image in the metabox.
					 * These can be removed once we move to the React versions of the social previews.
					 */
					'data-target'     => true,
					'data-target-id'  => true,
				],
				'select' => [
					'accesskey'       => true,
					'autofocus'       => true,
					'contenteditable' => true,
					'disabled'        => true,
					'draggable'       => true,
					'dropzone'        => true,
					'form'            => true,
					'hidden'          => true,
					'lang'            => true,
					'multiple'        => true,
					'name'            => true,
					'onblur'          => true,
					'onchange'        => true,
					'oncontextmenu'   => true,
					'onfocus'         => true,
					'oninput'         => true,
					'oninvalid'       => true,
					'onreset'         => true,
					'onsearch'        => true,
					'onselect'        => true,
					'onsubmit'        => true,
					'required'        => true,
					'size'            => true,
					'spellcheck'      => true,
					'tabindex'        => true,
					'translate'       => true,
				],
				'option' => [
					'class'    => true,
					'disabled' => true,
					'id'       => true,
					'label'    => true,
					'selected' => true,
					'value'    => true,
				],
			];

			// Add the global allowed attributes to each html element.
			$input_tags = array_map( '_wp_add_global_attributes', $input_tags );
		}

		return array_merge_recursive( $allowed_post_tags, $input_tags );
	}

	/**
	 * Gets an array of enabled features.
	 *
	 * @return string[] The array of enabled features.
	 */
	public static function retrieve_enabled_features() {
		/**
		 * The feature flag integration.
		 *
		 * @var Feature_Flag_Integration $feature_flag_integration
		 */
		$feature_flag_integration = YoastSEO()->classes->get( Feature_Flag_Integration::class );
		return $feature_flag_integration->get_enabled_features();
	}
}
