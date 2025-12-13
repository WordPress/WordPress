<?php
/**
 * Adds settings to the block editor.
 *
 * @package gutenberg
 */

/**
 * Replaces core 'styles' and '__experimentalFeatures' block editor settings from
 * wordpress-develop/block-editor.php with the Gutenberg versions. Much of the
 * code is copied from get_block_editor_settings() in that file.
 *
 * This hook should run first as it completely replaces the core settings that
 * other hooks may need to update.
 *
 * Note: The settings that are WP version specific should be handled inside the `compat` directory.
 *
 * @param array $settings Existing block editor settings.
 *
 * @return array New block editor settings.
 */
function gutenberg_get_block_editor_settings( $settings ) {
	$global_styles = array();
	$presets       = array(
		array(
			'css'            => 'variables',
			'__unstableType' => 'presets',
			'isGlobalStyles' => true,
		),
		array(
			'css'            => 'presets',
			'__unstableType' => 'presets',
			'isGlobalStyles' => true,
		),
	);
	foreach ( $presets as $preset_style ) {
		$actual_css = gutenberg_get_global_stylesheet( array( $preset_style['css'] ) );
		if ( '' !== $actual_css ) {
			$preset_style['css'] = $actual_css;
			$global_styles[]     = $preset_style;
		}
	}

	if ( wp_theme_has_theme_json() ) {
		$block_classes = array(
			'css'            => 'styles',
			'__unstableType' => 'theme',
			'isGlobalStyles' => true,
		);
		$actual_css    = gutenberg_get_global_stylesheet( array( $block_classes['css'] ) );
		if ( '' !== $actual_css ) {
			$block_classes['css'] = $actual_css;
			$global_styles[]      = $block_classes;
		}

		// Get any additional css from the customizer and add it before global styles custom CSS.
		$global_styles[] = array(
			'css'            => wp_get_custom_css(),
			'__unstableType' => 'user',
			'isGlobalStyles' => false,
		);

		/*
		 * Add the custom CSS as a separate stylesheet so any invalid CSS
		 * entered by users does not break other global styles.
		 */
		$global_styles[] = array(
			'css'            => gutenberg_get_global_stylesheet( array( 'custom-css' ) ),
			'__unstableType' => 'user',
			'isGlobalStyles' => true,
		);
	} else {
		// If there is no `theme.json` file, ensure base layout styles are still available.
		$block_classes = array(
			'css'            => 'base-layout-styles',
			'__unstableType' => 'base-layout',
			'isGlobalStyles' => true,
		);
		$actual_css    = gutenberg_get_global_stylesheet( array( $block_classes['css'] ) );
		if ( '' !== $actual_css ) {
			$block_classes['css'] = $actual_css;
			$global_styles[]      = $block_classes;
		}
		// Get any additional css from the customizer.
		$global_styles[] = array(
			'css'            => wp_get_custom_css(),
			'__unstableType' => 'user',
			'isGlobalStyles' => false,
		);
	}

	$settings['styles'] = array_merge( $global_styles, get_block_editor_theme_styles() );

	$settings['__experimentalFeatures'] = gutenberg_get_global_settings();
	// These settings may need to be updated based on data coming from theme.json sources.
	if ( isset( $settings['__experimentalFeatures']['color']['palette'] ) ) {
		$colors_by_origin   = $settings['__experimentalFeatures']['color']['palette'];
		$settings['colors'] = isset( $colors_by_origin['custom'] ) ?
			$colors_by_origin['custom'] : (
				isset( $colors_by_origin['theme'] ) ?
					$colors_by_origin['theme'] :
					$colors_by_origin['default']
			);
	}
	if ( isset( $settings['__experimentalFeatures']['color']['gradients'] ) ) {
		$gradients_by_origin   = $settings['__experimentalFeatures']['color']['gradients'];
		$settings['gradients'] = isset( $gradients_by_origin['custom'] ) ?
			$gradients_by_origin['custom'] : (
				isset( $gradients_by_origin['theme'] ) ?
					$gradients_by_origin['theme'] :
					$gradients_by_origin['default']
			);
	}
	if ( isset( $settings['__experimentalFeatures']['typography']['fontSizes'] ) ) {
		$font_sizes_by_origin  = $settings['__experimentalFeatures']['typography']['fontSizes'];
		$settings['fontSizes'] = isset( $font_sizes_by_origin['custom'] ) ?
			$font_sizes_by_origin['custom'] : (
				isset( $font_sizes_by_origin['theme'] ) ?
					$font_sizes_by_origin['theme'] :
					$font_sizes_by_origin['default']
			);
	}
	if ( isset( $settings['__experimentalFeatures']['color']['custom'] ) ) {
		$settings['disableCustomColors'] = ! $settings['__experimentalFeatures']['color']['custom'];
		unset( $settings['__experimentalFeatures']['color']['custom'] );
	}
	if ( isset( $settings['__experimentalFeatures']['color']['customGradient'] ) ) {
		$settings['disableCustomGradients'] = ! $settings['__experimentalFeatures']['color']['customGradient'];
		unset( $settings['__experimentalFeatures']['color']['customGradient'] );
	}
	if ( isset( $settings['__experimentalFeatures']['typography']['customFontSize'] ) ) {
		$settings['disableCustomFontSizes'] = ! $settings['__experimentalFeatures']['typography']['customFontSize'];
		unset( $settings['__experimentalFeatures']['typography']['customFontSize'] );
	}
	if ( isset( $settings['__experimentalFeatures']['typography']['lineHeight'] ) ) {
		$settings['enableCustomLineHeight'] = $settings['__experimentalFeatures']['typography']['lineHeight'];
		unset( $settings['__experimentalFeatures']['typography']['lineHeight'] );
	}
	if ( isset( $settings['__experimentalFeatures']['spacing']['units'] ) ) {
		$settings['enableCustomUnits'] = $settings['__experimentalFeatures']['spacing']['units'];
		unset( $settings['__experimentalFeatures']['spacing']['units'] );
	}
	if ( isset( $settings['__experimentalFeatures']['spacing']['padding'] ) ) {
		$settings['enableCustomSpacing'] = $settings['__experimentalFeatures']['spacing']['padding'];
		unset( $settings['__experimentalFeatures']['spacing']['padding'] );
	}
	if ( isset( $settings['__experimentalFeatures']['spacing']['customSpacingSize'] ) ) {
		$settings['disableCustomSpacingSizes'] = ! $settings['__experimentalFeatures']['spacing']['customSpacingSize'];
		unset( $settings['__experimentalFeatures']['spacing']['customSpacingSize'] );
	}

	if ( isset( $settings['__experimentalFeatures']['spacing']['spacingSizes'] ) ) {
		$spacing_sizes_by_origin  = $settings['__experimentalFeatures']['spacing']['spacingSizes'];
		$settings['spacingSizes'] = isset( $spacing_sizes_by_origin['custom'] ) ?
			$spacing_sizes_by_origin['custom'] : (
				isset( $spacing_sizes_by_origin['theme'] ) ?
					$spacing_sizes_by_origin['theme'] :
					$spacing_sizes_by_origin['default']
			);
	}

	return $settings;
}
add_filter( 'block_editor_settings_all', 'gutenberg_get_block_editor_settings', 0 );
