<?php
/**
 * Used to set up all core blocks used with the block editor.
 *
 * @package WordPress
 */

// Include files required for core blocks registration.
require ABSPATH . WPINC . '/blocks/archives.php';
require ABSPATH . WPINC . '/blocks/block.php';
require ABSPATH . WPINC . '/blocks/calendar.php';
require ABSPATH . WPINC . '/blocks/categories.php';
require ABSPATH . WPINC . '/blocks/latest-comments.php';
require ABSPATH . WPINC . '/blocks/latest-posts.php';
require ABSPATH . WPINC . '/blocks/rss.php';
require ABSPATH . WPINC . '/blocks/search.php';
require ABSPATH . WPINC . '/blocks/shortcode.php';
require ABSPATH . WPINC . '/blocks/social-link.php';
require ABSPATH . WPINC . '/blocks/tag-cloud.php';

/**
 * Registers core block types using metadata files.
 * Dynamic core blocks are registered separately.
 *
 * @since 5.5.0
 */
function register_core_block_types_from_metadata() {
	$block_folders = array(
		'audio',
		'button',
		'buttons',
		'code',
		'column',
		'columns',
		'embed',
		'file',
		'freeform',
		'gallery',
		'group',
		'heading',
		'html',
		'image',
		'list',
		'media-text',
		'missing',
		'more',
		'nextpage',
		'paragraph',
		'preformatted',
		'pullquote',
		'quote',
		'separator',
		'social-links',
		'spacer',
		'subhead',
		'table',
		'text-columns',
		'verse',
		'video',
	);

	foreach ( $block_folders as $block_folder ) {
		register_block_type_from_metadata(
			ABSPATH . WPINC . '/blocks/' . $block_folder
		);
	}
}
add_action( 'init', 'register_core_block_types_from_metadata' );
