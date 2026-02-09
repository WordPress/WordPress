<?php
/**
 * Server-side rendering of the `core/post-excerpt` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-excerpt` block on the server.
 *
 * @since 5.8.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the filtered post excerpt for the current post wrapped inside "p" tags.
 */
function render_block_core_post_excerpt( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$more_text           = ! empty( $attributes['moreText'] ) ? '<a class="wp-block-post-excerpt__more-link" href="' . esc_url( get_the_permalink( $block->context['postId'] ) ) . '">' . wp_kses_post( $attributes['moreText'] ) . '</a>' : '';
	$filter_excerpt_more = static function ( $more ) use ( $more_text ) {
		return empty( $more_text ) ? $more : '';
	};
	/**
	 * Some themes might use `excerpt_more` filter to handle the
	 * `more` link displayed after a trimmed excerpt. Since the
	 * block has a `more text` attribute we have to check and
	 * override if needed the return value from this filter.
	 * So if the block's attribute is not empty override the
	 * `excerpt_more` filter and return nothing. This will
	 * result in showing only one `read more` link at a time.
	 *
	 * This hook needs to be applied before the excerpt is retrieved with get_the_excerpt.
	 * Otherwise, the read more link filter from the theme is not removed.
	 */
	add_filter( 'excerpt_more', $filter_excerpt_more );

	/*
	 * The purpose of the excerpt length setting is to limit the length of both
	 * automatically generated and user-created excerpts.
	 * Because the excerpt_length filter only applies to auto generated excerpts,
	 * wp_trim_words is used instead.
	 *
	 * To ensure the block's excerptLength setting works correctly for auto-generated
	 * excerpts, we temporarily override excerpt_length to 101 (the max block setting)
	 * so that wp_trim_excerpt doesn't pre-trim the content before wp_trim_words can
	 * apply the user's desired length.
	 */
	$excerpt_length = $attributes['excerptLength'];
	add_filter( 'excerpt_length', 'block_core_post_excerpt_excerpt_length', PHP_INT_MAX );

	$excerpt = get_the_excerpt( $block->context['postId'] );

	remove_filter( 'excerpt_length', 'block_core_post_excerpt_excerpt_length', PHP_INT_MAX );

	if ( isset( $excerpt_length ) ) {
		$excerpt = wp_trim_words( $excerpt, $excerpt_length );
	}

	$classes = array();
	if ( isset( $attributes['textAlign'] ) ) {
		$classes[] = 'has-text-align-' . $attributes['textAlign'];
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	$content               = '<p class="wp-block-post-excerpt__excerpt">' . $excerpt;
	$show_more_on_new_line = ! isset( $attributes['showMoreOnNewLine'] ) || $attributes['showMoreOnNewLine'];
	if ( $show_more_on_new_line && ! empty( $more_text ) ) {
		$content .= '</p><p class="wp-block-post-excerpt__more-text">' . $more_text . '</p>';
	} else {
		$content .= " $more_text</p>";
	}
	remove_filter( 'excerpt_more', $filter_excerpt_more );
	return sprintf( '<div %1$s>%2$s</div>', $wrapper_attributes, $content );
}

/**
 * Registers the `core/post-excerpt` block on the server.
 *
 * @since 5.8.0
 */
function register_block_core_post_excerpt() {
	register_block_type_from_metadata(
		__DIR__ . '/post-excerpt',
		array(
			'render_callback' => 'render_block_core_post_excerpt',
		)
	);
}
add_action( 'init', 'register_block_core_post_excerpt' );

/**
 * Callback for the excerpt_length filter to override the excerpt length.
 *
 * If themes or plugins filter the excerpt_length, we need to
 * override the filter in the editor, otherwise
 * the excerpt length block setting has no effect.
 * Returns 101 (one more than the max block setting of 100) to ensure
 * wp_trim_words can detect when trimming is needed and add the ellipsis.
 *
 * For REST API requests, the filter is added on 'rest_api_init'
 * because REST_REQUEST is not defined until 'parse_request'.
 *
 * @since 7.0.0
 *
 * @return int The excerpt length.
 */
function block_core_post_excerpt_excerpt_length() {
	return 101;
}

if ( is_admin() ) {
	add_filter( 'excerpt_length', 'block_core_post_excerpt_excerpt_length', PHP_INT_MAX );
}
add_action(
	'rest_api_init',
	static function () {
		add_filter( 'excerpt_length', 'block_core_post_excerpt_excerpt_length', PHP_INT_MAX );
	}
);
