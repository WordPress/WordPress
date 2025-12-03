<?php
/**
 * Overrides the script-loader.php file.
 *
 * @package gutenberg
 */

// Remove core actions to override.
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

/**
 * Enqueues the global styles defined via theme.json.
 *
 * Copy of the core `wp_enqueue_global_styles`. Uses helper methods bundled with the plugin.
 *
 * @return void
 */
function gutenberg_enqueue_global_styles() {
	$separate_assets  = wp_should_load_separate_core_block_assets();
	$is_block_theme   = wp_is_block_theme();
	$is_classic_theme = ! $is_block_theme;

	/*
	 * Global styles should be printed in the head when loading all styles combined.
	 * The footer should only be used to print global styles for classic themes with separate core assets enabled.
	 *
	 * See https://core.trac.wordpress.org/ticket/53494.
	 */
	if (
		( $is_block_theme && doing_action( 'wp_footer' ) ) ||
		( $is_classic_theme && doing_action( 'wp_footer' ) && ! $separate_assets ) ||
		( $is_classic_theme && doing_action( 'wp_enqueue_scripts' ) && $separate_assets )
	) {
		return;
	}

	/*
	 * If loading the CSS for each block separately, then load the theme.json CSS conditionally.
	 * This removes the CSS from the global-styles stylesheet and adds it to the inline CSS for each block.
	 * This filter must be registered before calling wp_get_global_stylesheet();
	 */
	add_filter( 'wp_theme_json_get_style_nodes', 'wp_filter_out_block_nodes' );

	$stylesheet = gutenberg_get_global_stylesheet();

	if ( $is_block_theme ) {
		/*
		 * Dequeue the Customizer's custom CSS
		 * and add it before the global styles custom CSS.
		 */
		remove_action( 'wp_head', 'wp_custom_css_cb', 101 );

		/*
		 * Get the custom CSS from the Customizer and add it to the global stylesheet.
		 * Always do this in Customizer preview for the sake of live preview since it be empty.
		 */
		$custom_css = trim( wp_get_custom_css() );
		if ( $custom_css || is_customize_preview() ) {
			if ( is_customize_preview() ) {
				/*
				 * When in the Customizer preview, wrap the Custom CSS in milestone comments to allow customize-preview.js
				 * to locate the CSS to replace for live previewing. Make sure that the milestone comments are omitted from
				 * the stored Custom CSS if by chance someone tried to add them, which would be highly unlikely, but it
				 * would break live previewing.
				 */
				$before_milestone = '/*BEGIN_CUSTOMIZER_CUSTOM_CSS*/';
				$after_milestone  = '/*END_CUSTOMIZER_CUSTOM_CSS*/';
				$custom_css       = str_replace( array( $before_milestone, $after_milestone ), '', $custom_css );
				$custom_css       = $before_milestone . "\n" . $custom_css . "\n" . $after_milestone;
			}
			$custom_css = "\n" . $custom_css;
		}
		$stylesheet .= $custom_css;

		// Add the global styles custom CSS at the end.
		$stylesheet .= gutenberg_get_global_stylesheet( array( 'custom-css' ) );
	}

	if ( empty( $stylesheet ) ) {
		return;
	}

	wp_register_style( 'global-styles', false );
	wp_add_inline_style( 'global-styles', $stylesheet );
	wp_enqueue_style( 'global-styles' );

	// Add each block as an inline css.
	gutenberg_add_global_styles_for_blocks();
}
add_action( 'wp_enqueue_scripts', 'gutenberg_enqueue_global_styles' );
add_action( 'wp_footer', 'gutenberg_enqueue_global_styles', 1 );

/**
 * Enqueues the global styles custom css.
 *
 * @since 6.2.0
 */
function gutenberg_enqueue_global_styles_custom_css() {
	_deprecated_function( __FUNCTION__, 'Gutenberg 17.8.0', 'gutenberg_enqueue_global_styles' );
	if ( ! wp_is_block_theme() ) {
		return;
	}

	// Don't enqueue Customizer's custom CSS separately.
	remove_action( 'wp_head', 'wp_custom_css_cb', 101 );

	$custom_css  = wp_get_custom_css();
	$custom_css .= gutenberg_get_global_styles_custom_css();

	if ( ! empty( $custom_css ) ) {
		wp_add_inline_style( 'global-styles', $custom_css );
	}
}

/**
 * Function that enqueues the CSS Custom Properties coming from theme.json.
 *
 * @since 5.9.0
 */
function gutenberg_enqueue_global_styles_css_custom_properties() {
	wp_register_style( 'global-styles-css-custom-properties', false );
	wp_add_inline_style( 'global-styles-css-custom-properties', gutenberg_get_global_stylesheet( array( 'variables' ) ) );
	wp_enqueue_style( 'global-styles-css-custom-properties' );
}
remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_global_styles_css_custom_properties' );
add_action( 'enqueue_block_editor_assets', 'gutenberg_enqueue_global_styles_css_custom_properties' );
