<?php
/**
 * Adds inline script to extend customize-preview.js.
 *
 * @package gutenberg
 */

/**
 * Adds JS logic to the Customizer preview for live previewing Custom CSS for Block Themes.
 *
 * The logic in this function would be back-ported into customize-preview.js.
 * This was committed to wordpress-develop trunk in <https://core.trac.wordpress.org/changeset/60522>.
 */
function gutenberg_add_customizer_block_theme_custom_css_preview_js() {
	if ( ! wp_is_block_theme() ) {
		return;
	}

	$setting_id = 'custom_css[' . get_stylesheet() . ']';

	$js_function = <<<JS
		( settingId ) => {
			wp.customize.bind( 'preview-ready', () => {
				// Skip running logic that is already merged in trunk.
				if ( window._wpCustomizeSettings.theme.isBlockTheme ) {
					return;
				}

				wp.customize( settingId, function ( setting ) {
					setting.bind( function ( newValue ) {
						const style = document.querySelector( 'style#global-styles-inline-css' );
						if ( ! style ) {
							return;
						}

						// Forbid milestone comments from appearing in Custom CSS which would break live preview.
						newValue = newValue.replace( /\/\*(BEGIN|END)_CUSTOMIZER_CUSTOM_CSS\*\//g, '' );

						style.textContent = style.textContent.replace(
							/(\/\*BEGIN_CUSTOMIZER_CUSTOM_CSS\*\/)((?:.|\s)*?)(\/\*END_CUSTOMIZER_CUSTOM_CSS\*\/)/,
							function ( match, beforeComment, oldValue, afterComment ) {
								return beforeComment + newValue + afterComment;
							}
						);
					} );
				} );
			} );
		}
JS;
	wp_add_inline_script(
		'customize-preview',
		sprintf( '( %s )( %s )', $js_function, wp_json_encode( $setting_id, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ) )
	);
}

if ( version_compare( get_bloginfo( 'version' ), '6.9.0', '<' ) ) {
	add_action( 'customize_preview_init', 'gutenberg_add_customizer_block_theme_custom_css_preview_js' );
}
