<?php
/**
 * Server-side rendering of the `core/latest-posts` block.
 *
 * @package gutenberg
 */

/**
 * Renders the `core/latest-posts` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the post content with latest posts added.
 */
function render_block_core_latest_posts( $attributes ) {
	$recent_posts = wp_get_recent_posts(
		array(
			'numberposts' => $attributes['postsToShow'],
			'post_status' => 'publish',
			'order'       => $attributes['order'],
			'orderby'     => $attributes['orderBy'],
			'category'    => $attributes['categories'],
		)
	);

	$list_items_markup = '';

	foreach ( $recent_posts as $post ) {
		$post_id = $post['ID'];

		$title = get_the_title( $post_id );
		if ( ! $title ) {
			$title = __( '(Untitled)', 'gutenberg' );
		}
		$list_items_markup .= sprintf(
			'<li><a href="%1$s">%2$s</a>',
			esc_url( get_permalink( $post_id ) ),
			esc_html( $title )
		);

		if ( isset( $attributes['displayPostDate'] ) && $attributes['displayPostDate'] ) {
			$list_items_markup .= sprintf(
				'<time datetime="%1$s" class="wp-block-latest-posts__post-date">%2$s</time>',
				esc_attr( get_the_date( 'c', $post_id ) ),
				esc_html( get_the_date( '', $post_id ) )
			);
		}

		$list_items_markup .= "</li>\n";
	}

	$class = 'wp-block-latest-posts';
	if ( isset( $attributes['align'] ) ) {
		$class .= ' align' . $attributes['align'];
	}

	if ( isset( $attributes['postLayout'] ) && 'grid' === $attributes['postLayout'] ) {
		$class .= ' is-grid';
	}

	if ( isset( $attributes['columns'] ) && 'grid' === $attributes['postLayout'] ) {
		$class .= ' columns-' . $attributes['columns'];
	}

	if ( isset( $attributes['displayPostDate'] ) && $attributes['displayPostDate'] ) {
		$class .= ' has-dates';
	}

	if ( isset( $attributes['className'] ) ) {
		$class .= ' ' . $attributes['className'];
	}

	$block_content = sprintf(
		'<ul class="%1$s">%2$s</ul>',
		esc_attr( $class ),
		$list_items_markup
	);

	return $block_content;
}

/**
 * Registers the `core/latest-posts` block on server.
 */
function register_block_core_latest_posts() {
	register_block_type(
		'core/latest-posts',
		array(
			'attributes'      => array(
				'categories'      => array(
					'type' => 'string',
				),
				'className'       => array(
					'type' => 'string',
				),
				'postsToShow'     => array(
					'type'    => 'number',
					'default' => 5,
				),
				'displayPostDate' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'postLayout'      => array(
					'type'    => 'string',
					'default' => 'list',
				),
				'columns'         => array(
					'type'    => 'number',
					'default' => 3,
				),
				'align'           => array(
					'type' => 'string',
				),
				'order'           => array(
					'type'    => 'string',
					'default' => 'desc',
				),
				'orderBy'         => array(
					'type'    => 'string',
					'default' => 'date',
				),
			),
			'render_callback' => 'render_block_core_latest_posts',
		)
	);
}

add_action( 'init', 'register_block_core_latest_posts' );
