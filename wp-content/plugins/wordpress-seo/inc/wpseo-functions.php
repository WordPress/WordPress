<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
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
			$breadcrumbs_enabled = WPSEO_Options::get( 'breadcrumbs-enable', false );
		}

		if ( $breadcrumbs_enabled ) {
			return WPSEO_Breadcrumbs::breadcrumb( $before, $after, $display );
		}
	}
}

if ( ! function_exists( 'yoast_get_primary_term_id' ) ) {
	/**
	 * Get the primary term ID.
	 *
	 * @param string           $taxonomy Optional. The taxonomy to get the primary term ID for. Defaults to category.
	 * @param int|WP_Post|null $post     Optional. Post to get the primary term ID for.
	 *
	 * @return bool|int
	 */
	function yoast_get_primary_term_id( $taxonomy = 'category', $post = null ) {
		$post = get_post( $post );

		$primary_term = new WPSEO_Primary_Term( $taxonomy, $post->ID );
		return $primary_term->get_primary_term();
	}
}

if ( ! function_exists( 'yoast_get_primary_term' ) ) {
	/**
	 * Get the primary term name.
	 *
	 * @param string           $taxonomy Optional. The taxonomy to get the primary term for. Defaults to category.
	 * @param int|WP_Post|null $post     Optional. Post to get the primary term for.
	 *
	 * @return string Name of the primary term.
	 */
	function yoast_get_primary_term( $taxonomy = 'category', $post = null ) {
		$primary_term_id = yoast_get_primary_term_id( $taxonomy, $post );

		$term = get_term( $primary_term_id );
		if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
			return $term->name;
		}

		return '';
	}
}

/**
 * Replace `%%variable_placeholders%%` with their real value based on the current requested page/post/cpt.
 *
 * @param string $text The string to replace the variables in.
 * @param object $args The object some of the replacement values might come from,
 *                     could be a post, taxonomy or term.
 * @param array  $omit Variables that should not be replaced by this function.
 *
 * @return string
 */
function wpseo_replace_vars( $text, $args, $omit = [] ) {
	$replacer = new WPSEO_Replace_Vars();

	return $replacer->replace( $text, $args, $omit );
}

/**
 * Register a new variable replacement.
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
 * @param string $replacevar_name  The name of the variable to replace, i.e. '%%var%%'.
 *                                 Note: the surrounding %% are optional, name can only contain [A-Za-z0-9_-].
 * @param mixed  $replace_function Function or method to call to retrieve the replacement value for the variable.
 *                                 Uses the same format as add_filter/add_action function parameter and
 *                                 should *return* the replacement value. DON'T echo it.
 * @param string $type             Type of variable: 'basic' or 'advanced', defaults to 'advanced'.
 * @param string $help_text        Help text to be added to the help tab for this variable.
 *
 * @return bool Whether the replacement function was successfully registered.
 */
function wpseo_register_var_replacement( $replacevar_name, $replace_function, $type = 'advanced', $help_text = '' ) {
	return WPSEO_Replace_Vars::register_replacement( $replacevar_name, $replace_function, $type, $help_text );
}

/**
 * WPML plugin support: Set titles for custom types / taxonomies as translatable.
 *
 * It adds new keys to a wpml-config.xml file for a custom post type title, metadesc,
 * title-ptarchive and metadesc-ptarchive fields translation.
 * Documentation: http://wpml.org/documentation/support/language-configuration-files/
 *
 * @global $sitepress
 *
 * @param array $config WPML configuration data to filter.
 *
 * @return array
 */
function wpseo_wpml_config( $config ) {
	global $sitepress;

	if ( ( is_array( $config ) && isset( $config['wpml-config']['admin-texts']['key'] ) ) && ( is_array( $config['wpml-config']['admin-texts']['key'] ) && $config['wpml-config']['admin-texts']['key'] !== [] ) ) {
		$admin_texts = $config['wpml-config']['admin-texts']['key'];
		foreach ( $admin_texts as $k => $val ) {
			if ( $val['attr']['name'] === 'wpseo_titles' ) {
				$translate_cp = array_keys( $sitepress->get_translatable_documents() );
				if ( is_array( $translate_cp ) && $translate_cp !== [] ) {
					foreach ( $translate_cp as $post_type ) {
						$admin_texts[ $k ]['key'][]['attr']['name'] = 'title-' . $post_type;
						$admin_texts[ $k ]['key'][]['attr']['name'] = 'metadesc-' . $post_type;
						$admin_texts[ $k ]['key'][]['attr']['name'] = 'title-ptarchive-' . $post_type;
						$admin_texts[ $k ]['key'][]['attr']['name'] = 'metadesc-ptarchive-' . $post_type;

						$translate_tax = $sitepress->get_translatable_taxonomies( false, $post_type );
						if ( is_array( $translate_tax ) && $translate_tax !== [] ) {
							foreach ( $translate_tax as $taxonomy ) {
								$admin_texts[ $k ]['key'][]['attr']['name'] = 'title-tax-' . $taxonomy;
								$admin_texts[ $k ]['key'][]['attr']['name'] = 'metadesc-tax-' . $taxonomy;
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

if ( ! function_exists( 'ctype_digit' ) ) {
	/**
	 * Emulate PHP native ctype_digit() function for when the ctype extension would be disabled *sigh*.
	 * Only emulates the behaviour for when the input is a string, does not handle integer input as ascii value.
	 *
	 * @param string $text String input to validate.
	 *
	 * @return bool
	 */
	function ctype_digit( $text ) {
		$return = false;
		if ( ( is_string( $text ) && $text !== '' ) && preg_match( '`^\d+$`', $text ) === 1 ) {
			$return = true;
		}

		return $return;
	}
}

/**
 * Makes sure the taxonomy meta is updated when a taxonomy term is split.
 *
 * @link https://make.wordpress.org/core/2015/02/16/taxonomy-term-splitting-in-4-2-a-developer-guide/ Article explaining the taxonomy term splitting in WP 4.2.
 *
 * @param string $old_term_id      Old term id of the taxonomy term that was splitted.
 * @param string $new_term_id      New term id of the taxonomy term that was splitted.
 * @param string $term_taxonomy_id Term taxonomy id for the taxonomy that was affected.
 * @param string $taxonomy         The taxonomy that the taxonomy term was splitted for.
 *
 * @return void
 */
function wpseo_split_shared_term( $old_term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
	$tax_meta = get_option( 'wpseo_taxonomy_meta', [] );

	if ( ! empty( $tax_meta[ $taxonomy ][ $old_term_id ] ) ) {
		$tax_meta[ $taxonomy ][ $new_term_id ] = $tax_meta[ $taxonomy ][ $old_term_id ];
		unset( $tax_meta[ $taxonomy ][ $old_term_id ] );
		update_option( 'wpseo_taxonomy_meta', $tax_meta );
	}
}

add_action( 'split_shared_term', 'wpseo_split_shared_term', 10, 4 );

/**
 * Get all WPSEO related capabilities.
 *
 * @since 8.3
 * @return array
 */
function wpseo_get_capabilities() {
	if ( ! did_action( 'wpseo_register_capabilities' ) ) {
		do_action( 'wpseo_register_capabilities' );
	}
	return WPSEO_Capability_Manager_Factory::get()->get_capabilities();
}
