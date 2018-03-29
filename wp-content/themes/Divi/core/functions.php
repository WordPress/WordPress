<?php

if ( ! function_exists( 'et_get_safe_localization' ) ) :
function et_get_safe_localization( $string ) {
	return apply_filters( 'et_get_safe_localization', wp_kses( $string, et_get_allowed_localization_html_elements() ) );
}
endif;

if ( ! function_exists( 'et_allow_ampersand' ) ) :
/**
 * Convert &amp; into &
 * Escaped ampersand by wp_kses() which is used by et_get_safe_localization()
 * can be a troublesome in some cases, ie.: when string is sent in an email.
 *
 * @param string $string original string
 *
 * @return string modified string
 */
function et_allow_ampersand( $string ) {
	return str_replace('&amp;', '&', $string);
}
endif;

if ( ! function_exists( 'et_get_allowed_localization_html_elements' ) ) :
function et_get_allowed_localization_html_elements() {
	$whitelisted_attributes = array(
		'id'    => array(),
		'class' => array(),
		'style' => array(),
	);

	$whitelisted_attributes = apply_filters( 'et_allowed_localization_html_attributes', $whitelisted_attributes );

	$elements = array(
		'a'      => array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
			'rel'    => array(),
		),
		'b'      => array(),
		'br'     => array(),
		'em'     => array(),
		'p'      => array(),
		'span'   => array(),
		'div'    => array(),
		'strong' => array(),
	);

	$elements = apply_filters( 'et_allowed_localization_html_elements', $elements );

	foreach ( $elements as $tag => $attributes ) {
		$elements[ $tag ] = array_merge( $attributes, $whitelisted_attributes );
	}

	return $elements;
}
endif;

if ( ! function_exists( 'et_core_get_main_fonts' ) ) :
function et_core_get_main_fonts() {
	global $wp_version;

	if ( version_compare( $wp_version, '4.6', '<' ) ) {
		return '';
	}

	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Open Sans, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$open_sans = _x( 'on', 'Open Sans font: on or off', 'Divi' );

	if ( 'off' !== $open_sans ) {
		$font_families = array();

		if ( 'off' !== $open_sans )
			$font_families[] = 'Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => implode( '%7C', $font_families ),
			'subset' => 'latin,latin-ext',
		);
		$fonts_url = add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" );
	}

	return $fonts_url;
}
endif;

if ( ! function_exists( 'et_core_load_main_fonts' ) ) :
function et_core_load_main_fonts() {
	$fonts_url = et_core_get_main_fonts();
	if ( empty( $fonts_url ) ) {
		return;
	}

	wp_enqueue_style( 'et-core-main-fonts', esc_url_raw( $fonts_url ), array(), null );
}
endif;

if ( ! function_exists( 'et_core_browser_body_class' ) ) :
function et_core_browser_body_class( $classes ) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if( $is_lynx ) $classes[] = 'lynx';
	elseif( $is_gecko ) $classes[] = 'gecko';
	elseif( $is_opera ) $classes[] = 'opera';
	elseif( $is_NS4 ) $classes[] = 'ns4';
	elseif( $is_safari ) $classes[] = 'safari';
	elseif( $is_chrome ) $classes[] = 'chrome';
	elseif( $is_IE ) $classes[] = 'ie';
	else $classes[] = 'unknown';

	if( $is_iphone ) $classes[] = 'iphone';
	return $classes;
}
endif;
add_filter( 'body_class', 'et_core_browser_body_class' );

if ( ! function_exists( 'et_force_edge_compatibility_mode' ) ) :
function et_force_edge_compatibility_mode() {
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
}
endif;
add_action( 'et_head_meta', 'et_force_edge_compatibility_mode' );

if ( ! function_exists( 'et_core_register_admin_assets' ) ) :
/**
 * Register Core admin assets.
 *
 * @since 1.0.0
 *
 * @private
 */
function et_core_register_admin_assets() {
	wp_register_style( 'et-core-admin', ET_CORE_URL . 'admin/css/core.css', array(), ET_CORE_VERSION );
	wp_register_script( 'et-core-admin', ET_CORE_URL . 'admin/js/core.js', array(), ET_CORE_VERSION );
	wp_localize_script( 'et-core-admin', 'etCore', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'text'    => array(
			'modalTempContentCheck' => esc_html__( 'Got it, thanks!', ET_CORE_TEXTDOMAIN ),
		),
	) );
}
endif;
add_action( 'admin_enqueue_scripts', 'et_core_register_admin_assets' );

if ( ! function_exists( 'et_core_load_main_styles' ) ) :
function et_core_load_main_styles( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
		return;
	}

	wp_enqueue_style( 'et-core-admin' );
}
endif;

if ( ! function_exists( 'et_core_get_ip_address' ) ):
/**
 * Returns the IP address of the client that initiated the current HTTP request.
 *
 * @return string
 */
function et_core_get_ip_address() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} else if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return sanitize_text_field( $ip );
}
endif;

if ( ! function_exists( 'et_core_initialize_component_group' ) ):
function et_core_initialize_component_group( $slug, $init_file = null ) {
	if ( null !== $init_file && file_exists( $init_file ) ) {
		// Load and run component group's init function
		require_once $init_file;

		$init = "et_core_{$slug}_init";

		$init();
	}

	/**
	 * Fires when a Core Component Group is loaded.
	 *
	 * The dynamic portion of the hook name, `$group`, refers to the name of the Core Component Group that was loaded.
	 *
	 * @since 1.0.0
	 */
	do_action( "et_core_{$slug}_loaded" );
}
endif;

if ( ! function_exists( 'et_core_get_third_party_components' ) ):
function et_core_get_third_party_components( $group = '' ) {
	static $third_party_components = null;

	if ( null !== $third_party_components ) {
		return $third_party_components;
	}

	/**
	 * 3rd-party components can be registered by adding the class instance to this array using it's name as the key.
	 *
	 * @since 1.1.0
	 *
	 * @param array $third_party {
	 *     An array mapping third party component names to a class instance reference.
	 *
	 *     @type ET_Core_3rdPartyComponent $name The component class instance.
	 *     ...
	 * }
	 * @param string $group If not empty, only components classified under this group should be included.
	 */
	return $third_party_components = apply_filters( 'et_core_get_third_party_components', array(), $group );
}
endif;

if ( ! function_exists( 'et_core_get_components_metadata' ) ):
function et_core_get_components_metadata() {
	static $metadata = null;

	if ( null === $metadata ) {
		require_once '_metadata.php';
		$metadata = json_decode( $metadata, true );
	}

	return $metadata;
}
endif;

if ( ! function_exists( 'et_core_get_component_names' ) ):
/**
 * Returns the names of all available components, optionally filtered by type and/or group.
 *
 * @param string $include The type of components to include (official|third-party|all). Default is 'official'.
 * @param string $group   Only include components in $group. Optional.
 *
 * @return array
 */
function et_core_get_component_names( $include = 'official', $group = '' ) {
	static $official_components = null;

	if ( null === $official_components ) {
		$official_components = et_core_get_components_metadata();
	}

	if ( 'official' === $include ) {
		return empty( $group ) ? $official_components['names'] : $official_components['groups'][ $group ]['members'];
	}

	$third_party_components = et_core_get_third_party_components();

	if ( 'third-party' === $include ) {
		return array_keys( $third_party_components );
	}

	return array_merge(
		array_keys( $third_party_components ),
		empty( $group ) ? $official_components['names'] : $official_components['groups'][ $group ]['members']
	);
}
endif;

if ( ! function_exists( 'wp_doing_ajax' ) ):
function wp_doing_ajax() {
	/**
	 * Filters whether the current request is an Ajax request.
	 *
	 * @since 4.7.0
	 *
	 * @param bool $wp_doing_ajax Whether the current request is an Ajax request.
	 */
	return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
}
endif;

if ( ! function_exists( 'et_core_die' ) ):
function et_core_die( $message = '' ) {
	if ( wp_doing_ajax() ) {
		$message = '' !== $message ? $message : esc_html__( 'Configuration Error', 'et_core' );
		wp_send_json_error( array( 'error' => $message ) );
	}

	die(-1);
}
endif;

if ( ! function_exists( 'et_core_security_check' ) ):
/**
 * Check if current user can perform an action and/or verify a nonce value. die() if not authorized.
 *
 * @examples:
 *   - Check if user can 'manage_options': `et_core_security_check();`
 *   - Verify a nonce value: `et_core_security_check( '', 'nonce_name' );`
 *   - Check if user can 'something' and verify a nonce value: `self::do_security_check( 'something', 'nonce_name' );`
 *
 * @param string $user_can       The name of the capability to check with `current_user_can()`.
 * @param string $nonce_action   The name of the nonce action to check (excluding '_nonce').
 * @param string $nonce_key      The key to use to lookup nonce value in `$nonce_location`. Default
 *                               is the value of `$nonce_action` with '_nonce' appended to it.
 * @param string $nonce_location Where the nonce is stored (_POST|_GET|_REQUEST). Default: _POST.
 *
 * @return bool `true` if check passed.
 */
function et_core_security_check( $user_can = 'manage_options', $nonce_action = '', $nonce_key = '', $nonce_location = '_POST' ) {
	if ( empty( $nonce_key ) && false === strpos( $nonce_action, '_nonce' ) ) {
		$nonce_key = $nonce_action . '_nonce';
	} else if ( empty( $nonce_key ) ) {
		$nonce_key = $nonce_action;
	}

	switch( $nonce_location ) {
		case '_POST':
			$nonce_location = $_POST;
			break;
		case '_GET':
			$nonce_location = $_GET;
			break;
		case '_REQUEST':
			$nonce_location = $_REQUEST;
			break;
		default:
			die(-1);
	}

	if ( '' !== $user_can && ! current_user_can( $user_can ) ) {
		die(-1);
	}

	if ( '' !== $nonce_action && ! wp_verify_nonce( $nonce_location[ $nonce_key ], $nonce_action ) ) {
		die(-1);
	}

	if ( '' === $user_can && '' === $nonce_action ) {
		die(-1);
	}

	return true;
}
endif;

if ( ! function_exists( 'et_core_load_component' ) ) :
/**
 * =============================
 * ----->>> DEPRECATED! <<<-----
 * =============================
 * Load Core components.
 *
 * This function loads Core components. Components are only loaded once, even if they are called many times.
 * Admin components/functions are automatically wrapped in an is_admin() check.
 *
 * @deprecated Component classes are now loaded automatically upon first use. Portability was the only component
 *             ever loaded by this function, so it now only handles that single use-case (for backwards compatibility).
 *
 * @param string|array $components Name of the Core component(s) to include as and indexed array.
 *
 * @return bool Always return true.
 */
function et_core_load_component( $components ) {
	static $portability_loaded = false;

	if ( $portability_loaded || empty( $components ) ) {
		return true;
	}

	$is_jetpack = isset( $_SERVER['HTTP_USER_AGENT'] ) && false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Jetpack' );

	if ( ! $is_jetpack && ! is_admin() && empty( $_GET['et_fb'] ) ) {
		return true;
	}

	include_once ET_CORE_PATH . 'components/Cache.php';
	include_once ET_CORE_PATH . 'components/Portability.php';

	return $portability_loaded = true;
}
endif;
