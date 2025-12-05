<?php

/**
 * Returns path to a plugin file.
 *
 * @param string $path File path relative to the plugin root directory.
 * @return string Absolute file path.
 */
function wpcf7_plugin_path( $path = '' ) {
	return path_join( WPCF7_PLUGIN_DIR, trim( $path, '/' ) );
}


/**
 * Returns the URL to a plugin file.
 *
 * @param string $path File path relative to the plugin root directory.
 * @return string URL.
 */
function wpcf7_plugin_url( $path = '' ) {
	$url = plugins_url( $path, WPCF7_PLUGIN );

	if ( is_ssl() and 'http:' === substr( $url, 0, 5 ) ) {
		$url = 'https:' . substr( $url, 5 );
	}

	return $url;
}


/**
 * Include a file under WPCF7_PLUGIN_MODULES_DIR.
 *
 * @param string $path File path relative to the module dir.
 * @return bool True on success, false on failure.
 */
function wpcf7_include_module_file( $path ) {
	$dir = WPCF7_PLUGIN_MODULES_DIR;

	if ( empty( $dir ) or ! is_dir( $dir ) ) {
		return false;
	}

	$path = path_join( $dir, ltrim( $path, '/' ) );

	if ( file_exists( $path ) ) {
		include_once $path;
		return true;
	}

	return false;
}


/**
 * Retrieves uploads directory information.
 *
 * @param string|bool $type Optional. Type of output. Default false.
 * @return array|string Information about the upload directory.
 */
function wpcf7_upload_dir( $type = false ) {
	$uploads = wp_get_upload_dir();

	$uploads = apply_filters( 'wpcf7_upload_dir', array(
		'dir' => $uploads['basedir'],
		'url' => $uploads['baseurl'],
	) );

	if ( 'dir' === $type ) {
		return $uploads['dir'];
	} if ( 'url' === $type ) {
		return $uploads['url'];
	}

	return $uploads;
}


/**
 * Verifies that a correct security nonce was used with time limit.
 *
 * @param string $nonce Nonce value that was used for verification.
 * @param string $action Optional. Context to what is taking place.
 *                       Default 'wp_rest'.
 * @return int|bool 1 if the nonce is generated between 0-12 hours ago,
 *                  2 if the nonce is generated between 12-24 hours ago.
 *                  False if the nonce is invalid.
 */
function wpcf7_verify_nonce( $nonce, $action = 'wp_rest' ) {
	return wp_verify_nonce( $nonce, $action );
}


/**
 * Creates a cryptographic token tied to a specific action, user, user session,
 * and window of time.
 *
 * @param string $action Optional. Context to what is taking place.
 *                       Default 'wp_rest'.
 * @return string The token.
 */
function wpcf7_create_nonce( $action = 'wp_rest' ) {
	return wp_create_nonce( $action );
}


/**
 * Converts multi-dimensional array to a flat array.
 *
 * @param mixed $input Array or item of array.
 * @return array Flatten array.
 */
function wpcf7_array_flatten( $input ) {
	if ( ! is_array( $input ) ) {
		return array( $input );
	}

	$output = array();

	foreach ( $input as $value ) {
		$output = array_merge( $output, wpcf7_array_flatten( $value ) );
	}

	return $output;
}


/**
 * Excludes unset or blank text values from the given array.
 *
 * @param array $input The array.
 * @return array Array without blank text values.
 */
function wpcf7_exclude_blank( $input ) {
	$output = array_filter( $input,
		static function ( $i ) {
			return isset( $i ) && '' !== $i;
		}
	);

	return array_values( $output );
}


/**
 * Creates a comma-separated list from a multi-dimensional array.
 *
 * @param mixed $input Array or item of array.
 * @param string|array $options Optional. Output options.
 * @return string Comma-separated list.
 */
function wpcf7_flat_join( $input, $options = '' ) {
	$options = wp_parse_args( $options, array(
		'separator' => ', ',
	) );

	$input = wpcf7_array_flatten( $input );
	$output = array();

	foreach ( $input as $value ) {
		if ( is_scalar( $value ) ) {
			$output[] = trim( (string) $value );
		}
	}

	return implode( $options['separator'], $output );
}


/**
 * Returns true if HTML5 is supported.
 */
function wpcf7_support_html5() {
	return (bool) wpcf7_apply_filters_deprecated(
		'wpcf7_support_html5',
		array( true ),
		'5.6',
		''
	);
}


/**
 * Returns true if HTML5 fallback is active.
 */
function wpcf7_support_html5_fallback() {
	return (bool) apply_filters( 'wpcf7_support_html5_fallback', false );
}


/**
 * Returns true if the Really Simple CAPTCHA plugin is used for contact forms.
 */
function wpcf7_use_really_simple_captcha() {
	return apply_filters( 'wpcf7_use_really_simple_captcha',
		WPCF7_USE_REALLY_SIMPLE_CAPTCHA
	);
}


/**
 * Returns true if config validation is active.
 */
function wpcf7_validate_configuration() {
	return apply_filters( 'wpcf7_validate_configuration',
		WPCF7_VALIDATE_CONFIGURATION
	);
}


/**
 * Returns true if wpcf7_autop() is applied.
 */
function wpcf7_autop_or_not( $options = '' ) {
	$options = wp_parse_args( $options, array(
		'for' => 'form',
	) );

	return (bool) apply_filters( 'wpcf7_autop_or_not', WPCF7_AUTOP, $options );
}


/**
 * Returns true if JavaScript for this plugin is loaded.
 */
function wpcf7_load_js() {
	return apply_filters( 'wpcf7_load_js', WPCF7_LOAD_JS );
}


/**
 * Returns true if CSS for this plugin is loaded.
 */
function wpcf7_load_css() {
	return apply_filters( 'wpcf7_load_css', WPCF7_LOAD_CSS );
}


/**
 * Builds an HTML anchor element.
 *
 * @param string $url Link URL.
 * @param string $anchor_text Anchor label text.
 * @param string|array $atts Optional. HTML attributes.
 * @return string Formatted anchor element.
 */
function wpcf7_link( $url, $anchor_text, $atts = '' ) {
	$atts = wp_parse_args( $atts, array(
		'id' => null,
		'class' => null,
	) );

	$atts = array_merge( $atts, array(
		'href' => esc_url( $url ),
	) );

	return sprintf(
		'<a %1$s>%2$s</a>',
		wpcf7_format_atts( $atts ),
		esc_html( $anchor_text )
	);
}


/**
 * Returns the current request URL.
 */
function wpcf7_get_request_uri() {
	static $request_uri = '';

	if ( empty( $request_uri ) ) {
		$request_uri = add_query_arg( array() );
		$request_uri = '/' . ltrim( $request_uri, '/' );
	}

	return sanitize_url( $request_uri );
}


/**
 * Registers post types used for this plugin.
 */
function wpcf7_register_post_types() {
	if ( class_exists( 'WPCF7_ContactForm' ) ) {
		WPCF7_ContactForm::register_post_type();
		return true;
	} else {
		return false;
	}
}


/**
 * Returns the version string of this plugin.
 *
 * @param string|array $options Optional. Output options.
 * @return string Version string.
 */
function wpcf7_version( $options = '' ) {
	$options = wp_parse_args( $options, array(
		'limit' => -1,
		'only_major' => false,
	) );

	if ( $options['only_major'] ) {
		$options['limit'] = 2;
	}

	$options['limit'] = (int) $options['limit'];

	$ver = WPCF7_VERSION;
	$ver = strtr( $ver, '_-+', '...' );
	$ver = preg_replace( '/[^0-9.]+/', '.$0.', $ver );
	$ver = preg_replace( '/[.]+/', '.', $ver );
	$ver = trim( $ver, '.' );
	$ver = explode( '.', $ver );

	if ( -1 < $options['limit'] ) {
		$ver = array_slice( $ver, 0, $options['limit'] );
	}

	$ver = implode( '.', $ver );

	return $ver;
}


/**
 * Returns array entries that match the given version.
 *
 * @param string $version The version to search for.
 * @param array $input Search target array.
 * @return array|bool Array of matched entries. False on failure.
 */
function wpcf7_version_grep( $version, array $input ) {
	$pattern = '/^' . preg_quote( (string) $version, '/' ) . '(?:\.|$)/';

	return preg_grep( $pattern, $input );
}


/**
 * Returns an enctype attribute value.
 *
 * @param string $enctype Enctype value.
 * @return string Enctype value. Empty if not a valid enctype.
 */
function wpcf7_enctype_value( $enctype ) {
	$enctype = trim( $enctype );

	if ( empty( $enctype ) ) {
		return '';
	}

	$valid_enctypes = array(
		'application/x-www-form-urlencoded',
		'multipart/form-data',
		'text/plain',
	);

	if ( in_array( $enctype, $valid_enctypes, true ) ) {
		return $enctype;
	}

	$pattern = '%^enctype="(' . implode( '|', $valid_enctypes ) . ')"$%';

	if ( preg_match( $pattern, $enctype, $matches ) ) {
		return $matches[1]; // for back-compat
	}

	return '';
}


/**
 * Removes directory recursively.
 *
 * @param string $dir Directory path.
 * @return bool True on success, false on failure.
 */
function wpcf7_rmdir_p( $dir ) {
	$filesystem = WPCF7_Filesystem::get_instance();

	return $filesystem->delete( $dir, true );
}


/**
 * Builds a URL-encoded query string.
 *
 * @link https://developer.wordpress.org/reference/functions/_http_build_query/
 *
 * @param array $data URL query parameters.
 * @param string $key Optional. If specified, used to prefix key name.
 * @return string Query string.
 */
function wpcf7_build_query( $data, $key = '' ) {
	$sep = '&';
	$ret = array();

	foreach ( (array) $data as $k => $v ) {
		$k = urlencode( $k );

		if ( ! empty( $key ) ) {
			$k = $key . '%5B' . $k . '%5D';
		}

		if ( null === $v ) {
			continue;
		} elseif ( false === $v ) {
			$v = '0';
		}

		if ( is_array( $v ) or is_object( $v ) ) {
			array_push( $ret, wpcf7_build_query( $v, $k ) );
		} else {
			array_push( $ret, $k . '=' . urlencode( $v ) );
		}
	}

	return implode( $sep, $ret );
}


/**
 * Returns the number of code units in a string.
 *
 * @link http://www.w3.org/TR/html5/infrastructure.html#code-unit-length
 *
 * @param string $text Input string.
 * @return int|false The number of code units, or false if
 *                  mb_convert_encoding is not available.
 */
function wpcf7_count_code_units( $text ) {
	static $use_mb = null;

	if ( is_null( $use_mb ) ) {
		$use_mb = function_exists( 'mb_convert_encoding' );
	}

	if ( ! $use_mb ) {
		return false;
	}

	$text = (string) $text;

	if ( '' === $text ) {
		return 0;
	}

	$text = str_replace( "\r\n", "\n", $text );

	$text = mb_convert_encoding(
		$text,
		'UTF-16',
		mb_detect_encoding( $text, mb_detect_order(), true ) ?: 'UTF-8'
	);

	return intdiv( mb_strlen( $text, '8bit' ), 2 );
}


/**
 * Returns true if WordPress is running on the localhost.
 */
function wpcf7_is_localhost() {
	$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );

	return in_array(
		strtolower( $sitename ),
		array( 'localhost', '127.0.0.1' ),
		true
	);
}


/**
 * Marks a function as deprecated and informs when it has been used.
 *
 * @param string $function_name The function that was called.
 * @param string $version The version of Contact Form 7 that deprecated
 *                        the function.
 * @param string $replacement The function that should have been called.
 */
function wpcf7_deprecated_function( $function_name, $version, $replacement ) {

	if ( ! WP_DEBUG ) {
		return;
	}

	if ( function_exists( '__' ) ) {
		/* translators: 1: PHP function name, 2: version number, 3: alternative function name */
		$message = __( 'Function %1$s is <strong>deprecated</strong> since Contact Form 7 version %2$s! Use %3$s instead.', 'contact-form-7' );
	} else {
		$message = 'Function %1$s is <strong>deprecated</strong> since Contact Form 7 version %2$s! Use %3$s instead.';
	}

	$message = sprintf( $message, $function_name, $version, $replacement );

	wp_trigger_error( '', $message, E_USER_DEPRECATED );
}


/**
 * Fires functions attached to a deprecated filter hook.
 *
 * @param string $hook_name The name of the filter hook.
 * @param array $args Array of additional function arguments to be
 *                    passed to apply_filters().
 * @param string $version The version of Contact Form 7 that deprecated
 *                        the hook.
 * @param string $replacement The hook that should have been used.
 */
function wpcf7_apply_filters_deprecated( $hook_name, $args, $version, $replacement = '' ) {
	if ( ! has_filter( $hook_name ) ) {
		return $args[0];
	}

	if ( WP_DEBUG and apply_filters( 'deprecated_hook_trigger_error', true ) ) {
		if ( $replacement ) {
			wp_trigger_error(
				'',
				sprintf(
					/* translators: 1: WordPress hook name, 2: version number, 3: alternative hook name */
					__( 'Hook %1$s is <strong>deprecated</strong> since Contact Form 7 version %2$s! Use %3$s instead.', 'contact-form-7' ),
					$hook_name,
					$version,
					$replacement
				),
				E_USER_DEPRECATED
			);
		} else {
			wp_trigger_error(
				'',
				sprintf(
					/* translators: 1: WordPress hook name, 2: version number */
					__( 'Hook %1$s is <strong>deprecated</strong> since Contact Form 7 version %2$s with no alternative available.', 'contact-form-7' ),
					$hook_name,
					$version
				),
				E_USER_DEPRECATED
			);
		}
	}

	return apply_filters_ref_array( $hook_name, $args );
}


/**
 * Marks something as being incorrectly called.
 *
 * @param string $function_name The function that was called.
 * @param string $message A message explaining what has been done incorrectly.
 * @param string $version The version of Contact Form 7 where the message
 *                        was added.
 */
function wpcf7_doing_it_wrong( $function_name, $message, $version ) {

	if ( ! WP_DEBUG ) {
		return;
	}

	if ( function_exists( '__' ) ) {
		if ( $version ) {
			$version = sprintf(
				/* translators: %s: Contact Form 7 version number. */
				__( '(This message was added in Contact Form 7 version %s.)', 'contact-form-7' ),
				$version
			);
		}

		wp_trigger_error(
			'',
			sprintf(
				/* translators: Developer debugging message. 1: PHP function name, 2: Explanatory message, 3: Contact Form 7 version number. */
				__( 'Function %1$s was called incorrectly. %2$s %3$s', 'contact-form-7' ),
				$function_name,
				$message,
				$version
			),
			E_USER_NOTICE
		);
	} else {
		if ( $version ) {
			$version = sprintf(
				'(This message was added in Contact Form 7 version %s.)',
				$version
			);
		}

		wp_trigger_error(
			'',
			sprintf(
				'Function %1$s was called incorrectly. %2$s %3$s',
				$function_name,
				$message,
				$version
			),
			E_USER_NOTICE
		);
	}
}


/**
 * Triggers an error about a remote HTTP request and response.
 *
 * @param string $url The resource URL.
 * @param array $request Request arguments.
 * @param array|WP_Error $response The response or WP_Error on failure.
 */
function wpcf7_log_remote_request( $url, $request, $response ) {

	if ( ! WP_DEBUG ) {
		return;
	}

	$log = sprintf(
		/* translators: 1: response code, 2: message, 3: body, 4: URL */
		__( 'HTTP Response: %1$s %2$s %3$s from %4$s', 'contact-form-7' ),
		(int) wp_remote_retrieve_response_code( $response ),
		wp_remote_retrieve_response_message( $response ),
		wp_remote_retrieve_body( $response ),
		$url
	);

	$log = apply_filters( 'wpcf7_log_remote_request',
		$log, $url, $request, $response
	);

	if ( $log ) {
		wp_trigger_error( '', $log, E_USER_NOTICE );
	}
}


/**
 * Anonymizes an IP address by masking local part.
 *
 * @param string $ip_addr The original IP address.
 * @return string|bool Anonymized IP address, or false on failure.
 */
function wpcf7_anonymize_ip_addr( $ip_addr ) {
	if (
		! function_exists( 'inet_ntop' ) or
		! function_exists( 'inet_pton' )
	) {
		return $ip_addr;
	}

	$packed = inet_pton( $ip_addr );

	if ( false === $packed ) {
		return $ip_addr;
	}

	if ( 4 === strlen( $packed ) ) { // IPv4
		$mask = '255.255.255.0';
	} elseif ( 16 === strlen( $packed ) ) { // IPv6
		$mask = 'ffff:ffff:ffff:0000:0000:0000:0000:0000';
	} else {
		return $ip_addr;
	}

	return inet_ntop( $packed & inet_pton( $mask ) );
}


/**
 * Retrieves a sanitized value from the $_GET superglobal.
 *
 * @param string $key Array key.
 * @param mixed $default The default value returned when
 *              the specified superglobal is not set.
 * @return mixed Sanitized value.
 */
function wpcf7_superglobal_get( $key, $default = '' ) {
	return wpcf7_superglobal( 'get', $key ) ?? $default;
}


/**
 * Retrieves a sanitized value from the $_POST superglobal.
 *
 * @param string $key Array key.
 * @param mixed $default The default value returned when
 *              the specified superglobal is not set.
 * @return mixed Sanitized value.
 */
function wpcf7_superglobal_post( $key, $default = '' ) {
	return wpcf7_superglobal( 'post', $key ) ?? $default;
}


/**
 * Retrieves a sanitized value from the $_REQUEST superglobal.
 *
 * @param string $key Array key.
 * @param mixed $default The default value returned when
 *              the specified superglobal is not set.
 * @return mixed Sanitized value.
 */
function wpcf7_superglobal_request( $key, $default = '' ) {
	return wpcf7_superglobal( 'request', $key ) ?? $default;
}


/**
 * Retrieves a sanitized value from the $_SERVER superglobal.
 *
 * @param string $key Array key.
 * @param mixed $default The default value returned when
 *              the specified superglobal is not set.
 * @return mixed Sanitized value.
 */
function wpcf7_superglobal_server( $key, $default = '' ) {
	return wpcf7_superglobal( 'server', $key ) ?? $default;
}


/**
 * Retrieves a sanitized value from the specified superglobal.
 *
 * @param string $superglobal A superglobal type.
 * @param string $key Array key.
 * @return string|array|null Sanitized value.
 */
function wpcf7_superglobal( $superglobal, $key ) {
	$superglobals = array(
		'get' => $_GET,
		'post' => $_POST,
		'request' => $_REQUEST,
		'server' => $_SERVER,
	);

	if ( isset( $superglobals[$superglobal][$key] ) ) {
		return map_deep(
			$superglobals[$superglobal][$key],
			static function ( $val ) {
				$val = wp_unslash( $val );
				$val = wp_check_invalid_utf8( $val );
				$val = wp_kses_no_null( $val );
				$val = wpcf7_strip_whitespaces( $val );
				return $val;
			}
		);
	}
}
