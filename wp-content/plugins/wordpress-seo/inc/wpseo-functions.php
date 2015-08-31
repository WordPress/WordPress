<?php
/**
 * @package WPSEO\Internals
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! function_exists( 'initialize_wpseo_front' ) ) {
	/**
	 * Wraps frontend class.
	 */
	function initialize_wpseo_front() {
		WPSEO_Frontend::get_instance();
	}
}


if ( ! function_exists( 'yoast_breadcrumb' ) ) {
	/**
	 * Template tag for breadcrumbs.
	 *
	 * @param string $before  What to show before the breadcrumb.
	 * @param string $after   What to show after the breadcrumb.
	 * @param bool   $display Whether to display the breadcrumb (true) or return it (false).
	 *
	 * @return string
	 */
	function yoast_breadcrumb( $before = '', $after = '', $display = true ) {
		$breadcrumbs_enabled = current_theme_supports( 'yoast-seo-breadcrumbs' );
		if ( ! $breadcrumbs_enabled ) {
			$options             = get_option( 'wpseo_internallinks' );
			$breadcrumbs_enabled = ( $options['breadcrumbs-enable'] === true );
		}

		if ( $breadcrumbs_enabled ) {
			return WPSEO_Breadcrumbs::breadcrumb( $before, $after, $display );
		}
	}
}

/**
 * Add the bulk edit capability to the proper default roles.
 */
function wpseo_add_capabilities() {
	$roles = array(
		'administrator',
		'editor',
	);

	$roles = apply_filters( 'wpseo_bulk_edit_roles', $roles );

	foreach ( $roles as $role ) {
		$r = get_role( $role );
		if ( $r ) {
			$r->add_cap( 'wpseo_bulk_edit' );
		}
	}
}


/**
 * Remove the bulk edit capability from the proper default roles.
 *
 * Contributor is still removed for legacy reasons.
 */
function wpseo_remove_capabilities() {
	$roles = array(
		'administrator',
		'editor',
		'author',
		'contributor',
	);

	$roles = apply_filters( 'wpseo_bulk_edit_roles', $roles );

	foreach ( $roles as $role ) {
		$r = get_role( $role );
		if ( $r ) {
			$r->remove_cap( 'wpseo_bulk_edit' );
		}
	}
}


/**
 * Replace `%%variable_placeholders%%` with their real value based on the current requested page/post/cpt
 *
 * @param string $string the string to replace the variables in.
 * @param object $args   the object some of the replacement values might come from, could be a post, taxonomy or term.
 * @param array  $omit   variables that should not be replaced by this function.
 *
 * @return string
 */
function wpseo_replace_vars( $string, $args, $omit = array() ) {
	$replacer = new WPSEO_Replace_Vars;

	return $replacer->replace( $string, $args, $omit );
}

/**
 * Register a new variable replacement
 *
 * This function is for use by other plugins/themes to easily add their own additional variables to replace.
 * This function should be called from a function on the 'wpseo_register_extra_replacements' action hook.
 * The use of this function is preferred over the older 'wpseo_replacements' filter as a way to add new replacements.
 * The 'wpseo_replacements' filter should still be used to adjust standard WPSEO replacement values.
 * The function can not be used to replace standard WPSEO replacement value functions and will thrown a warning
 * if you accidently try.
 * To avoid conflicts with variables registered by WPSEO and other themes/plugins, try and make the
 * name of your variable unique. Variable names also can not start with "%%cf_" or "%%ct_" as these are reserved
 * for the standard WPSEO variable variables 'cf_<custom-field-name>', 'ct_<custom-tax-name>' and
 * 'ct_desc_<custom-tax-name>'.
 * The replacement function will be passed the undelimited name (i.e. stripped of the %%) of the variable
 * to replace in case you need it.
 *
 * Example code:
 * <code>
 * <?php
 * function retrieve_var1_replacement( $var1 ) {
 *        return 'your replacement value';
 * }
 *
 * function register_my_plugin_extra_replacements() {
 *        wpseo_register_var_replacement( '%%myvar1%%', 'retrieve_var1_replacement', 'advanced', 'this is a help text for myvar1' );
 *        wpseo_register_var_replacement( 'myvar2', array( 'class', 'method_name' ), 'basic', 'this is a help text for myvar2' );
 * }
 * add_action( 'wpseo_register_extra_replacements', 'register_my_plugin_extra_replacements' );
 * ?>
 * </code>
 *
 * @since 1.5.4
 *
 * @param  string $var              The name of the variable to replace, i.e. '%%var%%'
 *                                  - the surrounding %% are optional, name can only contain [A-Za-z0-9_-].
 * @param  mixed  $replace_function Function or method to call to retrieve the replacement value for the variable
 *                                  Uses the same format as add_filter/add_action function parameter and
 *                                  should *return* the replacement value. DON'T echo it.
 * @param  string $type             Type of variable: 'basic' or 'advanced', defaults to 'advanced'.
 * @param  string $help_text        Help text to be added to the help tab for this variable.
 *
 * @return bool  Whether the replacement function was succesfully registered
 */
function wpseo_register_var_replacement( $var, $replace_function, $type = 'advanced', $help_text = '' ) {
	return WPSEO_Replace_Vars::register_replacement( $var, $replace_function, $type, $help_text );
}

/**
 * Redirect /sitemap.xml to /sitemap_index.xml
 */
function wpseo_xml_redirect_sitemap() {
	$current_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://';
	$current_url .= sanitize_text_field( $_SERVER['SERVER_NAME'] ) . sanitize_text_field( $_SERVER['REQUEST_URI'] );

	// Must be 'sitemap.xml' and must be 404.
	if ( home_url( '/sitemap.xml' ) == $current_url && $GLOBALS['wp_query']->is_404 ) {
		wp_redirect( home_url( '/sitemap_index.xml' ), 301 );
		exit;
	}
}

/**
 * Create base URL for the sitemaps and applies filters
 *
 * @since 1.5.7
 *
 * @param string $page page to append to the base URL.
 *
 * @return string base URL (incl page) for the sitemaps
 */
function wpseo_xml_sitemaps_base_url( $page ) {
	$base = $GLOBALS['wp_rewrite']->using_index_permalinks() ? 'index.php/' : '/';

	/**
	 * Filter: 'wpseo_sitemaps_base_url' - Allow developer to change the base URL of the sitemaps
	 *
	 * @api string $base The string that should be added to home_url() to make the full base URL.
	 */
	$base = apply_filters( 'wpseo_sitemaps_base_url', $base );

	return home_url( $base . $page );
}

/**
 * Initialize sitemaps. Add sitemap & XSL rewrite rules and query vars
 */
function wpseo_xml_sitemaps_init() {
	$options = get_option( 'wpseo_xml' );
	if ( $options['enablexmlsitemap'] !== true ) {
		return;
	}

	// Redirects sitemap.xml to sitemap_index.xml.
	add_action( 'template_redirect', 'wpseo_xml_redirect_sitemap', 0 );

	if ( ! is_object( $GLOBALS['wp'] ) ) {
		return;
	}

	$GLOBALS['wp']->add_query_var( 'sitemap' );
	$GLOBALS['wp']->add_query_var( 'sitemap_n' );
	$GLOBALS['wp']->add_query_var( 'xsl' );
	add_rewrite_rule( 'sitemap_index\.xml$', 'index.php?sitemap=1', 'top' );
	add_rewrite_rule( '([^/]+?)-sitemap([0-9]+)?\.xml$', 'index.php?sitemap=$matches[1]&sitemap_n=$matches[2]', 'top' );
	add_rewrite_rule( '([a-z]+)?-?sitemap\.xsl$', 'index.php?xsl=$matches[1]', 'top' );
}

add_action( 'init', 'wpseo_xml_sitemaps_init', 1 );

/**
 * Notify search engines of the updated sitemap.
 *
 * @param string|null $sitemapurl
 */
function wpseo_ping_search_engines( $sitemapurl = null ) {
	// Don't ping if blog is not public.
	if ( '0' == get_option( 'blog_public' ) ) {
		return;
	}

	if ( $sitemapurl == null ) {
		$sitemapurl = urlencode( wpseo_xml_sitemaps_base_url( 'sitemap_index.xml' ) );
	}

	// Ping Google and Bing.
	wp_remote_get( 'http://www.google.com/webmasters/tools/ping?sitemap=' . $sitemapurl, array( 'blocking' => false ) );
	wp_remote_get( 'http://www.bing.com/ping?sitemap=' . $sitemapurl, array( 'blocking' => false ) );
}

add_action( 'wpseo_ping_search_engines', 'wpseo_ping_search_engines' );

/**
 * WPML plugin support: Set titles for custom types / taxonomies as translatable.
 * It adds new keys to a wpml-config.xml file for a custom post type title, metadesc, title-ptarchive and metadesc-ptarchive fields translation.
 * Documentation: http://wpml.org/documentation/support/language-configuration-files/
 *
 * @global      $sitepress
 *
 * @param array $config
 *
 * @return array
 */
function wpseo_wpml_config( $config ) {
	global $sitepress;

	if ( ( is_array( $config ) && isset( $config['wpml-config']['admin-texts']['key'] ) ) && ( is_array( $config['wpml-config']['admin-texts']['key'] ) && $config['wpml-config']['admin-texts']['key'] !== array() ) ) {
		$admin_texts = $config['wpml-config']['admin-texts']['key'];
		foreach ( $admin_texts as $k => $val ) {
			if ( $val['attr']['name'] === 'wpseo_titles' ) {
				$translate_cp = array_keys( $sitepress->get_translatable_documents() );
				if ( is_array( $translate_cp ) && $translate_cp !== array() ) {
					foreach ( $translate_cp as $post_type ) {
						$admin_texts[ $k ]['key'][]['attr']['name'] = 'title-' . $post_type;
						$admin_texts[ $k ]['key'][]['attr']['name'] = 'metadesc-' . $post_type;
						$admin_texts[ $k ]['key'][]['attr']['name'] = 'metakey-' . $post_type;
						$admin_texts[ $k ]['key'][]['attr']['name'] = 'title-ptarchive-' . $post_type;
						$admin_texts[ $k ]['key'][]['attr']['name'] = 'metadesc-ptarchive-' . $post_type;
						$admin_texts[ $k ]['key'][]['attr']['name'] = 'metakey-ptarchive-' . $post_type;

						$translate_tax = $sitepress->get_translatable_taxonomies( false, $post_type );
						if ( is_array( $translate_tax ) && $translate_tax !== array() ) {
							foreach ( $translate_tax as $taxonomy ) {
								$admin_texts[ $k ]['key'][]['attr']['name'] = 'title-tax-' . $taxonomy;
								$admin_texts[ $k ]['key'][]['attr']['name'] = 'metadesc-tax-' . $taxonomy;
								$admin_texts[ $k ]['key'][]['attr']['name'] = 'metakey-tax-' . $taxonomy;
							}
						}
					}
				}
				break;
			}
		}
		$config['wpml-config']['admin-texts']['key'] = $admin_texts;
	}

	return $config;
}

add_filter( 'icl_wpml_config_array', 'wpseo_wpml_config' );

/**
 * Yoast SEO breadcrumb shortcode
 * [wpseo_breadcrumb]
 *
 * @return string
 */
function wpseo_shortcode_yoast_breadcrumb() {
	return yoast_breadcrumb( '', '', false );
}

add_shortcode( 'wpseo_breadcrumb', 'wpseo_shortcode_yoast_breadcrumb' );


/**
 * This invalidates our XML Sitemaps cache.
 *
 * @param string $type
 */
function wpseo_invalidate_sitemap_cache( $type ) {
	// Always delete the main index sitemaps cache, as that's always invalidated by any other change.
	delete_transient( 'wpseo_sitemap_cache_1' );
	delete_transient( 'wpseo_sitemap_cache_' . $type );

	WPSEO_Utils::clear_sitemap_cache( array( $type ) );
}

add_action( 'deleted_term_relationships', 'wpseo_invalidate_sitemap_cache' );

/**
 * Invalidate XML sitemap cache for taxonomy / term actions
 *
 * @param unsigned $unused
 * @param string   $type
 */
function wpseo_invalidate_sitemap_cache_terms( $unused, $type ) {
	wpseo_invalidate_sitemap_cache( $type );
}

add_action( 'edited_terms', 'wpseo_invalidate_sitemap_cache_terms', 10, 2 );
add_action( 'clean_term_cache', 'wpseo_invalidate_sitemap_cache_terms', 10, 2 );
add_action( 'clean_object_term_cache', 'wpseo_invalidate_sitemap_cache_terms', 10, 2 );

/**
 * Invalidate the XML sitemap cache for a post type when publishing or updating a post
 *
 * @param int $post_id
 */
function wpseo_invalidate_sitemap_cache_on_save_post( $post_id ) {

	// If this is just a revision, don't invalidate the sitemap cache yet.
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	wpseo_invalidate_sitemap_cache( get_post_type( $post_id ) );
}

add_action( 'save_post', 'wpseo_invalidate_sitemap_cache_on_save_post' );

/**
 * Emulate PHP native ctype_digit() function for when the ctype extension would be disabled *sigh*
 * Only emulates the behaviour for when the input is a string, does not handle integer input as ascii value
 *
 * @param    string $string
 *
 * @return    bool
 */
if ( ! extension_loaded( 'ctype' ) || ! function_exists( 'ctype_digit' ) ) {

	/**
	 * @param string $string
	 *
	 * @return bool
	 */
	function ctype_digit( $string ) {
		$return = false;
		if ( ( is_string( $string ) && $string !== '' ) && preg_match( '`^\d+$`', $string ) === 1 ) {
			$return = true;
		}

		return $return;
	}
}


/********************** DEPRECATED FUNCTIONS **********************/


/**
 * Get the value from the post custom values
 *
 * @deprecated 1.5.0
 * @deprecated use WPSEO_Meta::get_value()
 * @see        WPSEO_Meta::get_value()
 *
 * @param    string $val    Internal name of the value to get.
 * @param    int    $postid Post ID of the post to get the value for.
 *
 * @return    string
 */
function wpseo_get_value( $val, $postid = 0 ) {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.5.0', 'WPSEO_Meta::get_value()' );

	return WPSEO_Meta::get_value( $val, $postid );
}


/**
 * Save a custom meta value
 *
 * @deprecated 1.5.0
 * @deprecated use WPSEO_Meta::set_value() or just use update_post_meta()
 * @see        WPSEO_Meta::set_value()
 *
 * @param    string $meta_key   The meta to change.
 * @param    mixed  $meta_value The value to set the meta to.
 * @param    int    $post_id    The ID of the post to change the meta for.
 *
 * @return    bool    whether the value was changed
 */
function wpseo_set_value( $meta_key, $meta_value, $post_id ) {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.5.0', 'WPSEO_Meta::set_value()' );

	return WPSEO_Meta::set_value( $meta_key, $meta_value, $post_id );
}


/**
 * Retrieve an array of all the options the plugin uses. It can't use only one due to limitations of the options API.
 *
 * @deprecated 1.5.0
 * @deprecated use WPSEO_Options::get_option_names()
 * @see        WPSEO_Options::get_option_names()
 *
 * @return array of options.
 */
function get_wpseo_options_arr() {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.5.0', 'WPSEO_Options::get_option_names()' );

	return WPSEO_Options::get_option_names();
}


/**
 * Retrieve all the options for the SEO plugin in one go.
 *
 * @deprecated 1.5.0
 * @deprecated use WPSEO_Options::get_all()
 * @see        WPSEO_Options::get_all()
 *
 * @return array of options
 */
function get_wpseo_options() {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.5.0', 'WPSEO_Options::get_all()' );

	return WPSEO_Options::get_all();
}

/**
 * Used for imports, both in dashboard and import settings pages, this functions either copies
 * $old_metakey into $new_metakey or just plain replaces $old_metakey with $new_metakey
 *
 * @deprecated 1.5.0
 * @deprecated use WPSEO_Meta::replace_meta()
 * @see        WPSEO_Meta::replace_meta()
 *
 * @param string $old_metakey The old name of the meta value.
 * @param string $new_metakey The new name of the meta value, usually the Yoast SEO name.
 * @param bool   $replace     Whether to replace or to copy the values.
 */
function replace_meta( $old_metakey, $new_metakey, $replace = false ) {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.5.0', 'WPSEO_Meta::replace_meta()' );
	WPSEO_Meta::replace_meta( $old_metakey, $new_metakey, $replace );
}


/**
 * Retrieve a taxonomy term's meta value.
 *
 * @deprecated 1.5.0
 * @deprecated use WPSEO_Taxonomy_Meta::get_term_meta()
 * @see        WPSEO_Taxonomy_Meta::get_term_meta()
 *
 * @param string|object $term     Term to get the meta value for.
 * @param string        $taxonomy Name of the taxonomy to which the term is attached.
 * @param string        $meta     Meta value to get.
 *
 * @return bool|mixed value when the meta exists, false when it does not
 */
function wpseo_get_term_meta( $term, $taxonomy, $meta ) {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.5.0', 'WPSEO_Taxonomy_Meta::get_term_meta()' );
	return WPSEO_Taxonomy_Meta::get_term_meta( $term, $taxonomy, $meta );
}

/**
 * Throw a notice about an invalid custom taxonomy used
 *
 * @since      1.4.14
 * @deprecated 1.5.4 (removed)
 */
function wpseo_invalid_custom_taxonomy() {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.5.4' );
}

/**
 * Retrieve a post's terms, comma delimited.
 *
 * @deprecated 1.5.4
 * @deprecated use WPSEO_Replace_Vars::get_terms()
 * @see        WPSEO_Replace_Vars::get_terms()
 *
 * @param int    $id            ID of the post to get the terms for.
 * @param string $taxonomy      The taxonomy to get the terms for this post from.
 * @param bool   $return_single If true, return the first term.
 *
 * @return string either a single term or a comma delimited string of terms.
 */
function wpseo_get_terms( $id, $taxonomy, $return_single = false ) {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.5.4', 'WPSEO_Replace_Vars::get_terms()' );
	$replacer = new WPSEO_Replace_Vars;

	return $replacer->get_terms( $id, $taxonomy, $return_single );
}

/**
 * Generate an HTML sitemap
 *
 * @deprecated 1.5.5.4
 * @deprecated use plugin Yoast SEO Premium
 * @see        Yoast SEO Premium
 *
 * @param array $atts The attributes passed to the shortcode.
 *
 * @return string
 */
function wpseo_sitemap_handler( $atts ) {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.5.5.4', 'Functionality has been discontinued after being in beta, it\'ll be available in the Yoast SEO Premium plugin soon.' );

	return '';
}

add_shortcode( 'wpseo_sitemap', 'wpseo_sitemap_handler' );

/**
 * Strip out the shortcodes with a filthy regex, because people don't properly register their shortcodes.
 *
 * @deprecated 1.6.1
 * @deprecated use WPSEO_Utils::strip_shortcode()
 * @see        WPSEO_Utils::strip_shortcode()
 *
 * @param string $text Input string that might contain shortcodes.
 *
 * @return string $text string without shortcodes
 */
function wpseo_strip_shortcode( $text ) {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.6.1', 'WPSEO_Utils::strip_shortcode()' );

	return WPSEO_Utils::strip_shortcode( $text );
}

/**
 * Do simple reliable math calculations without the risk of wrong results
 *
 * @see        http://floating-point-gui.de/
 * @see        the big red warning on http://php.net/language.types.float.php
 *
 * @deprecated 1.6.1
 * @deprecated use WPSEO_Utils::calc()
 * @see        WPSEO_Utils::calc()
 *
 * In the rare case that the bcmath extension would not be loaded, it will return the normal calculation results
 *
 * @since      1.5.0
 *
 * @param    mixed  $number1   Scalar (string/int/float/bool).
 * @param    string $action    Calculation action to execute.
 * @param    mixed  $number2   Scalar (string/int/float/bool).
 * @param    bool   $round     Whether or not to round the result. Defaults to false.
 * @param    int    $decimals  Decimals for rounding operation. Defaults to 0.
 * @param    int    $precision Calculation precision. Defaults to 10.
 *
 * @return    mixed                Calculation Result or false if either or the numbers isn't scalar or
 *                                an invalid operation was passed
 */
function wpseo_calc( $number1, $action, $number2, $round = false, $decimals = 0, $precision = 10 ) {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.6.1', 'WPSEO_Utils::calc()' );

	return WPSEO_Utils::calc( $number1, $action, $number2, $round, $decimals, $precision );
}

/**
 * Check if the web server is running on Apache
 *
 * @deprecated 1.6.1
 * @deprecated use WPSEO_Utils::is_apache()
 * @see        WPSEO_Utils::is_apache()
 *
 * @return bool
 */
function wpseo_is_apache() {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.6.1', 'WPSEO_Utils::is_apache()' );

	return WPSEO_Utils::is_apache();
}

/**
 * Check if the web service is running on Nginx
 *
 * @deprecated 1.6.1
 * @deprecated use WPSEO_Utils::is_nginx()
 * @see        WPSEO_Utils::is_nginx()
 *
 * @return bool
 */
function wpseo_is_nginx() {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.6.1', 'WPSEO_Utils::is_nginx()' );

	return WPSEO_Utils::is_nginx();
}

/**
 * List all the available user roles
 *
 * @deprecated 1.6.1
 * @deprecated use WPSEO_Utils::get_roles()
 * @see        WPSEO_Utils::get_roles()
 *
 * @return array $roles
 */
function wpseo_get_roles() {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.6.1', 'WPSEO_Utils::get_roles()' );

	return WPSEO_Utils::get_roles();
}

/**
 * Check whether a url is relative
 *
 * @deprecated 1.6.1
 * @deprecated use WPSEO_Utils::is_url_relative()
 * @see        WPSEO_Utils::is_url_relative()
 *
 * @param string $url
 *
 * @return bool
 */
function wpseo_is_url_relative( $url ) {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.6.1', 'WPSEO_Utils::is_url_relative()' );

	return WPSEO_Utils::is_url_relative( $url );
}

/**
 * Standardize whitespace in a string
 *
 * @deprecated 1.6.1
 * @deprecated use WPSEO_Utils::standardize_whitespace()
 * @see        WPSEO_Utils::standardize_whitespace()
 *
 * @since      1.6.0
 *
 * @param string $string
 *
 * @return string
 */
function wpseo_standardize_whitespace( $string ) {
	_deprecated_function( __FUNCTION__, 'WPSEO 1.6.1', 'WPSEO_Utils::standardize_whitespace()' );

	return WPSEO_Utils::standardize_whitespace( $string );
}

