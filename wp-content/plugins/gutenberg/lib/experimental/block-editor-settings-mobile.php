<?php
/**
 * Adds settings to the mobile block editor.
 *
 * @package gutenberg
 */

/**
 * Adds settings to the mobile block editor.
 *
 * This is used by the settings REST endpoint and it should land in core
 * as soon as lib/class-wp-rest-block-editor-settings-controller.php does.
 *
 * @param array $settings Existing block editor settings.
 *
 * @return array New block editor settings.
 */
function gutenberg_get_block_editor_settings_mobile( $settings ) {
	if ( wp_theme_has_theme_json() ) {
		$settings['__experimentalStyles'] = gutenberg_get_global_styles();
	}

	// To tell mobile that the site uses quote v2 (inner blocks).
	// See https://github.com/WordPress/gutenberg/pull/25892.
	$settings['__experimentalEnableQuoteBlockV2'] = true;
	// To tell mobile that the site uses list v2 (inner blocks).
	$settings['__experimentalEnableListBlockV2'] = true;

	return $settings;
}
