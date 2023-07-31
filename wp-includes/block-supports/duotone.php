<?php
/**
 * Duotone block support flag.
 *
 * Parts of this source were derived and modified from TinyColor,
 * released under the MIT license.
 *
 * https://github.com/bgrins/TinyColor
 *
 * Copyright (c), Brian Grinstead, http://briangrinstead.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package WordPress
 * @since 5.8.0
 */

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'duotone',
	array(
		'register_attribute' => array( 'WP_Duotone', 'register_duotone_support' ),
	)
);

// Add classnames to blocks using duotone support.
add_filter( 'render_block', array( 'WP_Duotone', 'render_duotone_support' ), 10, 3 );

// Enqueue styles.
// Block styles (core-block-supports-inline-css) before the style engine (wp_enqueue_stored_styles).
// Global styles (global-styles-inline-css) after the other global styles (wp_enqueue_global_styles).
add_action( 'wp_enqueue_scripts', array( 'WP_Duotone', 'output_block_styles' ), 9 );
add_action( 'wp_enqueue_scripts', array( 'WP_Duotone', 'output_global_styles' ), 11 );

// Add SVG filters to the footer. Also, for classic themes, output block styles (core-block-supports-inline-css).
add_action( 'wp_footer', array( 'WP_Duotone', 'output_footer_assets' ), 10 );

// Add styles and SVGs for use in the editor via the EditorStyles component.
add_filter( 'block_editor_settings_all', array( 'WP_Duotone', 'add_editor_settings' ), 10 );

// Migrate the old experimental duotone support flag.
add_filter( 'block_type_metadata_settings', array( 'WP_Duotone', 'migrate_experimental_duotone_support_flag' ), 10, 2 );
