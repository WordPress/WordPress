<?php
/**
 * Server-side rendering of the `core/site-logo` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/site-logo` block on the server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The render.
 */
function render_block_core_site_logo( $attributes ) {
	$adjust_width_height_filter = function ( $image ) use ( $attributes ) {
		if ( empty( $attributes['width'] ) || empty( $image ) ) {
			return $image;
		}
		$height = (float) $attributes['width'] / ( (float) $image[1] / (float) $image[2] );
		return array( $image[0], (int) $attributes['width'], (int) $height );
	};

	add_filter( 'wp_get_attachment_image_src', $adjust_width_height_filter );

	$custom_logo = get_custom_logo();

	remove_filter( 'wp_get_attachment_image_src', $adjust_width_height_filter );

	if ( empty( $custom_logo ) ) {
		return ''; // Return early if no custom logo is set, avoiding extraneous wrapper div.
	}

	if ( ! $attributes['isLink'] ) {
		// Remove the link.
		$custom_logo = preg_replace( '#<a.*?>(.*?)</a>#i', '\1', $custom_logo );
	}

	if ( $attributes['isLink'] && '_blank' === $attributes['linkTarget'] ) {
		// Add the link target after the rel="home".
		// Add an aria-label for informing that the page opens in a new tab.
		$aria_label  = 'aria-label="' . esc_attr__( '(Home link, opens in a new tab)' ) . '"';
		$custom_logo = str_replace( 'rel="home"', 'rel="home" target="' . $attributes['linkTarget'] . '"' . $aria_label, $custom_logo );
	}

	$classnames = array();
	if ( ! empty( $attributes['className'] ) ) {
		$classnames[] = $attributes['className'];
	}

	if ( empty( $attributes['width'] ) ) {
		$classnames[] = 'is-default-size';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classnames ) ) );
	$html               = sprintf( '<div %s>%s</div>', $wrapper_attributes, $custom_logo );
	return $html;
}

/**
 * Register a core site setting for a site logo
 */
function register_block_core_site_logo_setting() {
	register_setting(
		'general',
		'site_logo',
		array(
			'show_in_rest' => array(
				'name' => 'site_logo',
			),
			'type'         => 'integer',
			'description'  => __( 'Site logo.' ),
		)
	);
}

add_action( 'rest_api_init', 'register_block_core_site_logo_setting', 10 );

/**
 * Register a core site setting for a site icon
 */
function register_block_core_site_icon_setting() {
	register_setting(
		'general',
		'site_icon',
		array(
			'show_in_rest' => true,
			'type'         => 'integer',
			'description'  => __( 'Site icon.' ),
		)
	);
}

add_action( 'rest_api_init', 'register_block_core_site_icon_setting', 10 );

/**
 * Registers the `core/site-logo` block on the server.
 */
function register_block_core_site_logo() {
	register_block_type_from_metadata(
		__DIR__ . '/site-logo',
		array(
			'render_callback' => 'render_block_core_site_logo',
		)
	);
}

add_action( 'init', 'register_block_core_site_logo' );

/**
 * Overrides the custom logo with a site logo, if the option is set.
 *
 * @param string $custom_logo The custom logo set by a theme.
 *
 * @return string The site logo if set.
 */
function _override_custom_logo_theme_mod( $custom_logo ) {
	$site_logo = get_option( 'site_logo' );
	return false === $site_logo ? $custom_logo : $site_logo;
}

add_filter( 'theme_mod_custom_logo', '_override_custom_logo_theme_mod' );

/**
 * Updates the site_logo option when the custom_logo theme-mod gets updated.
 *
 * @param  mixed $value Attachment ID of the custom logo or an empty value.
 * @return mixed
 */
function _sync_custom_logo_to_site_logo( $value ) {
	if ( empty( $value ) ) {
		delete_option( 'site_logo' );
	} else {
		update_option( 'site_logo', $value );
	}

	return $value;
}

add_filter( 'pre_set_theme_mod_custom_logo', '_sync_custom_logo_to_site_logo' );

/**
 * Deletes the site_logo when the custom_logo theme mod is removed.
 *
 * @param array $old_value Previous theme mod settings.
 * @param array $value     Updated theme mod settings.
 */
function _delete_site_logo_on_remove_custom_logo( $old_value, $value ) {
	global $_ignore_site_logo_changes;

	if ( $_ignore_site_logo_changes ) {
		return;
	}

	// If the custom_logo is being unset, it's being removed from theme mods.
	if ( isset( $old_value['custom_logo'] ) && ! isset( $value['custom_logo'] ) ) {
		delete_option( 'site_logo' );
	}
}

/**
 * Deletes the site logo when all theme mods are being removed.
 */
function _delete_site_logo_on_remove_theme_mods() {
	global $_ignore_site_logo_changes;

	if ( $_ignore_site_logo_changes ) {
		return;
	}

	if ( false !== get_theme_support( 'custom-logo' ) ) {
		delete_option( 'site_logo' );
	}
}

/**
 * Hooks `_delete_site_logo_on_remove_custom_logo` in `update_option_theme_mods_$theme`.
 * Hooks `_delete_site_logo_on_remove_theme_mods` in `delete_option_theme_mods_$theme`.
 *
 * Runs on `setup_theme` to account for dynamically-switched themes in the Customizer.
 */
function _delete_site_logo_on_remove_custom_logo_on_setup_theme() {
	$theme = get_option( 'stylesheet' );
	add_action( "update_option_theme_mods_$theme", '_delete_site_logo_on_remove_custom_logo', 10, 2 );
	add_action( "delete_option_theme_mods_$theme", '_delete_site_logo_on_remove_theme_mods' );
}
add_action( 'setup_theme', '_delete_site_logo_on_remove_custom_logo_on_setup_theme', 11 );

/**
 * Removes the custom_logo theme-mod when the site_logo option gets deleted.
 */
function _delete_custom_logo_on_remove_site_logo() {
	global $_ignore_site_logo_changes;

	// Prevent _delete_site_logo_on_remove_custom_logo and
	// _delete_site_logo_on_remove_theme_mods from firing and causing an
	// infinite loop.
	$_ignore_site_logo_changes = true;

	// Remove the custom logo.
	remove_theme_mod( 'custom_logo' );

	$_ignore_site_logo_changes = false;
}
add_action( 'delete_option_site_logo', '_delete_custom_logo_on_remove_site_logo' );
