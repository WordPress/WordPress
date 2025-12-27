<?php

/**
 * Make the title and description of the post format templates human readable.
 *
 * @param array $default_template_types Default template types.
 * @return array Filtered template type data.
 */
function gutenberg_post_format_template_title_description( $default_template_types ) {
	$post_formats = get_post_format_strings();

	foreach ( $post_formats as $post_format_slug => $post_format_name ) {
		$default_template_types[ 'taxonomy-post_format-post-format-' . $post_format_slug ] = array(
			'title'       => sprintf(
				/* translators: %s: Post format name. */
				_x( 'Post Format: %s', 'Template name' ),
				$post_format_name
			),
			'description' => sprintf(
				/* translators: %s: Post format name. */
				__( 'Displays the %s post format archive.' ),
				$post_format_name
			),
		);
	}
	return $default_template_types;
}

add_filter( 'default_template_types', 'gutenberg_post_format_template_title_description', 10, 1 );
