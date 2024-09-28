<?php
/**
 * Custom template tags for Twenty Fifteen
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

if ( ! function_exists( 'twentyfifteen_comment_nav' ) ) :
	/**
	 * Display navigation to next/previous comments when applicable.
	 *
	 * @since Twenty Fifteen 1.0
	 */
	function twentyfifteen_comment_nav() {
		// Are there comments to navigate through?
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
			?>
		<nav class="navigation comment-navigation">
		<h2 class="screen-reader-text">
			<?php
			/* translators: Hidden accessibility text. */
			_e( 'Comment navigation', 'twentyfifteen' );
			?>
		</h2>
		<div class="nav-links">
			<?php
			$prev_link = get_previous_comments_link( __( 'Older Comments', 'twentyfifteen' ) );
			if ( $prev_link ) {
				printf( '<div class="nav-previous">%s</div>', $prev_link );
			}

			$next_link = get_next_comments_link( __( 'Newer Comments', 'twentyfifteen' ) );
			if ( $next_link ) {
				printf( '<div class="nav-next">%s</div>', $next_link );
			}
			?>
			</div><!-- .nav-links -->
		</nav><!-- .comment-navigation -->
			<?php
		endif;
	}
endif;

if ( ! function_exists( 'twentyfifteen_entry_meta' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags.
	 *
	 * @since Twenty Fifteen 1.0
	 */
	function twentyfifteen_entry_meta() {
		if ( is_sticky() && is_home() && ! is_paged() ) {
			printf( '<span class="sticky-post">%s</span>', __( 'Featured', 'twentyfifteen' ) );
		}

		$format = get_post_format();
		if ( current_theme_supports( 'post-formats', $format ) ) {
			printf(
				'<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
				sprintf(
					'<span class="screen-reader-text">%s </span>',
					/* translators: Hidden accessibility text. */
					_x( 'Format', 'Used before post format.', 'twentyfifteen' )
				),
				esc_url( get_post_format_link( $format ) ),
				get_post_format_string( $format )
			);
		}

		if ( in_array( get_post_type(), array( 'post', 'attachment' ), true ) ) {
			$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

			if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
				$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
			}

			$time_string = sprintf(
				$time_string,
				esc_attr( get_the_date( 'c' ) ),
				get_the_date(),
				esc_attr( get_the_modified_date( 'c' ) ),
				get_the_modified_date()
			);

			printf(
				'<span class="posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
				/* translators: Hidden accessibility text. */
				_x( 'Posted on', 'Used before publish date.', 'twentyfifteen' ),
				esc_url( get_permalink() ),
				$time_string
			);
		}

		if ( 'post' === get_post_type() ) {
			if ( is_singular() || is_multi_author() ) {
				printf(
					'<span class="byline"><span class="screen-reader-text">%1$s </span><span class="author vcard"><a class="url fn n" href="%2$s">%3$s</a></span></span>',
					/* translators: Hidden accessibility text. */
					_x( 'Author', 'Used before post author name.', 'twentyfifteen' ),
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					get_the_author()
				);
			}

			$categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfifteen' ) );
			if ( $categories_list && twentyfifteen_categorized_blog() ) {
				printf(
					'<span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
					/* translators: Hidden accessibility text. */
					_x( 'Categories', 'Used before category names.', 'twentyfifteen' ),
					$categories_list
				);
			}

			$tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfifteen' ) );
			if ( $tags_list && ! is_wp_error( $tags_list ) ) {
				printf(
					'<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
					/* translators: Hidden accessibility text. */
					_x( 'Tags', 'Used before tag names.', 'twentyfifteen' ),
					$tags_list
				);
			}
		}

		if ( is_attachment() && wp_attachment_is_image() ) {
			// Retrieve attachment metadata.
			$metadata = wp_get_attachment_metadata();

			printf(
				'<span class="full-size-link"><span class="screen-reader-text">%1$s </span><a href="%2$s">%3$s &times; %4$s</a></span>',
				/* translators: Hidden accessibility text. */
				_x( 'Full size', 'Used before full size attachment link.', 'twentyfifteen' ),
				esc_url( wp_get_attachment_url() ),
				$metadata['width'],
				$metadata['height']
			);
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			/* translators: %s: Post title. Only visible to screen readers. */
			comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentyfifteen' ), get_the_title() ) );
			echo '</span>';
		}
	}
endif;

/**
 * Determine whether blog/site has more than one category.
 *
 * @since Twenty Fifteen 1.0
 *
 * @return bool True of there is more than one category, false otherwise.
 */
function twentyfifteen_categorized_blog() {
	$all_the_cool_cats = get_transient( 'twentyfifteen_categories' );
	if ( false === $all_the_cool_cats ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories(
			array(
				'fields'     => 'ids',
				'hide_empty' => 1,

				// We only need to know if there is more than one category.
				'number'     => 2,
			)
		);

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'twentyfifteen_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 || is_preview() ) {
		// This blog has more than 1 category so twentyfifteen_categorized_blog() should return true.
		return true;
	} else {
		// This blog has only 1 category so twentyfifteen_categorized_blog() should return false.
		return false;
	}
}

/**
 * Flush out the transients used in {@see twentyfifteen_categorized_blog()}.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'twentyfifteen_categories' );
}
add_action( 'edit_category', 'twentyfifteen_category_transient_flusher' );
add_action( 'save_post', 'twentyfifteen_category_transient_flusher' );

if ( ! function_exists( 'twentyfifteen_post_thumbnail' ) ) :
	/**
	 * Display an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 *
	 * @since Twenty Fifteen 1.0
	 */
	function twentyfifteen_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

		<div class="post-thumbnail">
			<?php the_post_thumbnail(); ?>
	</div><!-- .post-thumbnail -->

	<?php else : ?>

	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
		<?php
			the_post_thumbnail( 'post-thumbnail', array( 'alt' => get_the_title() ) );
		?>
	</a>

		<?php
	endif; // End is_singular().
	}
endif;

if ( ! function_exists( 'twentyfifteen_get_link_url' ) ) :
	/**
	 * Return the post URL.
	 *
	 * Falls back to the post permalink if no URL is found in the post.
	 *
	 * @since Twenty Fifteen 1.0
	 *
	 * @see get_url_in_content()
	 *
	 * @return string The Link format URL.
	 */
	function twentyfifteen_get_link_url() {
		$has_url = get_url_in_content( get_the_content() );

		return $has_url ? $has_url : apply_filters( 'the_permalink', get_permalink() );
	}
endif;

if ( ! function_exists( 'twentyfifteen_excerpt_more' ) && ! is_admin() ) :
	/**
	 * Replaces "[...]" (appended to automatically generated excerpts) with ... and a 'Continue reading' link.
	 *
	 * @since Twenty Fifteen 1.0
	 *
	 * @param string $more Default Read More excerpt link.
	 * @return string 'Continue reading' link prepended with an ellipsis.
	 */
	function twentyfifteen_excerpt_more( $more ) {
		$link = sprintf(
			'<a href="%1$s" class="more-link">%2$s</a>',
			esc_url( get_permalink( get_the_ID() ) ),
			/* translators: %s: Post title. Only visible to screen readers. */
			sprintf( __( 'Continue reading %s', 'twentyfifteen' ), '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>' )
		);
		return ' &hellip; ' . $link;
	}
	add_filter( 'excerpt_more', 'twentyfifteen_excerpt_more' );
endif;

if ( ! function_exists( 'twentyfifteen_the_custom_logo' ) ) :
	/**
	 * Displays the optional custom logo.
	 *
	 * Does nothing if the custom logo is not available.
	 *
	 * @since Twenty Fifteen 1.5
	 */
	function twentyfifteen_the_custom_logo() {
		if ( function_exists( 'the_custom_logo' ) ) {
			the_custom_logo();
		}
	}
endif;

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Fire the wp_body_open action.
	 *
	 * Added for backward compatibility to support pre-5.2.0 WordPress versions.
	 *
	 * @since Twenty Fifteen 2.5
	 */
	function wp_body_open() {
		/**
		 * Triggered after the opening <body> tag.
		 *
		 * @since Twenty Fifteen 2.5
		 */
		do_action( 'wp_body_open' );
	}
endif;
