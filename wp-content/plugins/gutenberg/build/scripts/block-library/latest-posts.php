<?php
/**
 * Server-side rendering of the `core/latest-posts` block.
 *
 * @package WordPress
 */

/**
 * The excerpt length set by the Latest Posts core block
 * set at render time and used by the block itself.
 *
 * @var int
 */
global $block_core_latest_posts_excerpt_length;
$block_core_latest_posts_excerpt_length = 0;

/**
 * Callback for the excerpt_length filter used by
 * the Latest Posts block at render time.
 *
 * @since 5.4.0
 *
 * @return int Returns the global $block_core_latest_posts_excerpt_length variable
 *             to allow the excerpt_length filter respect the Latest Block setting.
 */
function gutenberg_block_core_latest_posts_get_excerpt_length() {
	global $block_core_latest_posts_excerpt_length;
	return $block_core_latest_posts_excerpt_length;
}

/**
 * Renders the `core/latest-posts` block on server.
 *
 * @since 5.0.0
 *
 * @global WP_Post $post                                   Global post object.
 * @global int     $block_core_latest_posts_excerpt_length Excerpt length set by the Latest Posts core block.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the post content with latest posts added.
 */
function gutenberg_render_block_core_latest_posts( $attributes ) {
	global $post, $block_core_latest_posts_excerpt_length;

	$args = array(
		'posts_per_page'      => $attributes['postsToShow'],
		'post_status'         => 'publish',
		'order'               => $attributes['order'],
		'orderby'             => $attributes['orderBy'],
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	$block_core_latest_posts_excerpt_length = $attributes['excerptLength'];
	add_filter( 'excerpt_length', 'gutenberg_block_core_latest_posts_get_excerpt_length', 20 );

	if ( ! empty( $attributes['categories'] ) ) {
		$args['category__in'] = array_column( $attributes['categories'], 'id' );
	}
	if ( isset( $attributes['selectedAuthor'] ) ) {
		$args['author'] = $attributes['selectedAuthor'];
	}

	$query        = new WP_Query();
	$recent_posts = $query->query( $args );

	if ( isset( $attributes['displayFeaturedImage'] ) && $attributes['displayFeaturedImage'] ) {
		update_post_thumbnail_cache( $query );
	}

	$list_items_markup = '';

	foreach ( $recent_posts as $post ) {
		$post_link = esc_url( get_permalink( $post ) );
		$title     = get_the_title( $post );

		if ( ! $title ) {
			$title = __( '(no title)' );
		}

		$list_items_markup .= '<li>';

		if ( $attributes['displayFeaturedImage'] && has_post_thumbnail( $post ) ) {
			$image_style = '';
			if ( isset( $attributes['featuredImageSizeWidth'] ) ) {
				$image_style .= sprintf( 'max-width:%spx;', $attributes['featuredImageSizeWidth'] );
			}
			if ( isset( $attributes['featuredImageSizeHeight'] ) ) {
				$image_style .= sprintf( 'max-height:%spx;', $attributes['featuredImageSizeHeight'] );
			}

			$image_classes = 'wp-block-latest-posts__featured-image';
			if ( isset( $attributes['featuredImageAlign'] ) ) {
				$image_classes .= ' align' . $attributes['featuredImageAlign'];
			}

			$featured_image = get_the_post_thumbnail(
				$post,
				$attributes['featuredImageSizeSlug'],
				array(
					'style' => esc_attr( $image_style ),
				)
			);
			if ( $attributes['addLinkToFeaturedImage'] ) {
				$featured_image = sprintf(
					'<a href="%1$s" aria-label="%2$s">%3$s</a>',
					esc_url( $post_link ),
					esc_attr( $title ),
					$featured_image
				);
			}
			$list_items_markup .= sprintf(
				'<div class="%1$s">%2$s</div>',
				esc_attr( $image_classes ),
				$featured_image
			);
		}

		$list_items_markup .= sprintf(
			'<a class="wp-block-latest-posts__post-title" href="%1$s">%2$s</a>',
			esc_url( $post_link ),
			$title
		);

		if ( isset( $attributes['displayAuthor'] ) && $attributes['displayAuthor'] ) {
			$author_display_name = get_the_author_meta( 'display_name', $post->post_author );

			/* translators: byline. %s: author. */
			$byline = sprintf( __( 'by %s' ), $author_display_name );

			if ( ! empty( $author_display_name ) ) {
				$list_items_markup .= sprintf(
					'<div class="wp-block-latest-posts__post-author">%1$s</div>',
					$byline
				);
			}
		}

		if ( isset( $attributes['displayPostDate'] ) && $attributes['displayPostDate'] ) {
			$list_items_markup .= sprintf(
				'<time datetime="%1$s" class="wp-block-latest-posts__post-date">%2$s</time>',
				esc_attr( get_the_date( 'c', $post ) ),
				get_the_date( '', $post )
			);
		}

		if ( isset( $attributes['displayPostContent'] ) && $attributes['displayPostContent']
			&& isset( $attributes['displayPostContentRadio'] ) && 'excerpt' === $attributes['displayPostContentRadio'] ) {

			$trimmed_excerpt = get_the_excerpt( $post );

			/*
			 * Adds a "Read more" link with screen reader text.
			 * [&hellip;] is the default excerpt ending from wp_trim_excerpt() in Core.
			 */
			if ( str_ends_with( $trimmed_excerpt, ' [&hellip;]' ) ) {
				/** This filter is documented in wp-includes/formatting.php */
				$excerpt_length = (int) apply_filters( 'excerpt_length', $block_core_latest_posts_excerpt_length );
				if ( $excerpt_length <= $block_core_latest_posts_excerpt_length ) {
					$trimmed_excerpt  = substr( $trimmed_excerpt, 0, -11 );
					$trimmed_excerpt .= sprintf(
						/* translators: 1: A URL to a post, 2: Hidden accessibility text: Post title */
						__( '… <a class="wp-block-latest-posts__read-more" href="%1$s" rel="noopener noreferrer">Read more<span class="screen-reader-text">: %2$s</span></a>' ),
						esc_url( $post_link ),
						esc_html( $title )
					);
				}
			}

			if ( post_password_required( $post ) ) {
				$trimmed_excerpt = __( 'This content is password protected.' );
			}

			$list_items_markup .= sprintf(
				'<div class="wp-block-latest-posts__post-excerpt">%1$s</div>',
				$trimmed_excerpt
			);
		}

		if ( isset( $attributes['displayPostContent'] ) && $attributes['displayPostContent']
			&& isset( $attributes['displayPostContentRadio'] ) && 'full_post' === $attributes['displayPostContentRadio'] ) {

			$post_content = html_entity_decode( $post->post_content, ENT_QUOTES, get_option( 'blog_charset' ) );

			if ( post_password_required( $post ) ) {
				$post_content = __( 'This content is password protected.' );
			}

			$list_items_markup .= sprintf(
				'<div class="wp-block-latest-posts__post-full-content">%1$s</div>',
				wp_kses_post( $post_content )
			);
		}

		$list_items_markup .= "</li>\n";
	}

	remove_filter( 'excerpt_length', 'gutenberg_block_core_latest_posts_get_excerpt_length', 20 );

	$classes = array( 'wp-block-latest-posts__list' );
	if ( isset( $attributes['postLayout'] ) && 'grid' === $attributes['postLayout'] ) {
		$classes[] = 'is-grid';
	}
	if ( isset( $attributes['columns'] ) && 'grid' === $attributes['postLayout'] ) {
		$classes[] = 'columns-' . $attributes['columns'];
	}
	if ( isset( $attributes['displayPostDate'] ) && $attributes['displayPostDate'] ) {
		$classes[] = 'has-dates';
	}
	if ( isset( $attributes['displayAuthor'] ) && $attributes['displayAuthor'] ) {
		$classes[] = 'has-author';
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	return sprintf(
		'<ul %1$s>%2$s</ul>',
		$wrapper_attributes,
		$list_items_markup
	);
}

/**
 * Registers the `core/latest-posts` block on server.
 *
 * @since 5.0.0
 */
function gutenberg_register_block_core_latest_posts() {
	register_block_type_from_metadata(
		__DIR__ . '/latest-posts',
		array(
			'render_callback' => 'gutenberg_render_block_core_latest_posts',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_latest_posts', 20 );

/**
 * Handles outdated versions of the `core/latest-posts` block by converting
 * attribute `categories` from a numeric string to an array with key `id`.
 *
 * This is done to accommodate the changes introduced in #20781 that sought to
 * add support for multiple categories to the block. However, given that this
 * block is dynamic, the usual provisions for block migration are insufficient,
 * as they only act when a block is loaded in the editor.
 *
 * TODO: Remove when and if the bottom client-side deprecation for this block
 * is removed.
 *
 * @since 5.5.0
 *
 * @param array $block A single parsed block object.
 *
 * @return array The migrated block object.
 */
function gutenberg_block_core_latest_posts_migrate_categories( $block ) {
	if (
		'core/latest-posts' === $block['blockName'] &&
		! empty( $block['attrs']['categories'] ) &&
		is_string( $block['attrs']['categories'] )
	) {
		$block['attrs']['categories'] = array(
			array( 'id' => absint( $block['attrs']['categories'] ) ),
		);
	}

	return $block;
}
add_filter( 'render_block_data', 'gutenberg_block_core_latest_posts_migrate_categories' );
