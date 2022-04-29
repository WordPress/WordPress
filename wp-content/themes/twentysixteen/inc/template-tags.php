<?php
/**
 * Custom Twenty Sixteen template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

if ( ! function_exists( 'twentysixteen_entry_meta' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags.
	 *
	 * Create your own twentysixteen_entry_meta() function to override in a child theme.
	 *
	 * @since Twenty Sixteen 1.0
	 */
	function twentysixteen_entry_meta() {
		if ( 'post' === get_post_type() ) {
			$author_avatar_size = apply_filters( 'twentysixteen_author_avatar_size', 49 );
			printf(
				'<span class="byline"><span class="author vcard">%1$s<span class="screen-reader-text">%2$s </span> <a class="url fn n" href="%3$s">%4$s</a></span></span>',
				get_avatar( get_the_author_meta( 'user_email' ), $author_avatar_size ),
				_x( 'Author', 'Used before post author name.', 'twentysixteen' ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				get_the_author()
			);
		}

		if ( in_array( get_post_type(), array( 'post', 'attachment' ), true ) ) {
			twentysixteen_entry_date();
		}

		$format = get_post_format();
		if ( current_theme_supports( 'post-formats', $format ) ) {
			printf(
				'<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
				sprintf( '<span class="screen-reader-text">%s </span>', _x( 'Format', 'Used before post format.', 'twentysixteen' ) ),
				esc_url( get_post_format_link( $format ) ),
				get_post_format_string( $format )
			);
		}

		if ( 'post' === get_post_type() ) {
			twentysixteen_entry_taxonomies();
		}

		if ( ! is_singular() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			/* translators: %s: Post title. Only visible to screen readers. */
			comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentysixteen' ), get_the_title() ) );
			echo '</span>';
		}
	}
endif;

if ( ! function_exists( 'twentysixteen_entry_date' ) ) :
	/**
	 * Prints HTML with date information for current post.
	 *
	 * Create your own twentysixteen_entry_date() function to override in a child theme.
	 *
	 * @since Twenty Sixteen 1.0
	 */
	function twentysixteen_entry_date() {
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
			_x( 'Posted on', 'Used before publish date.', 'twentysixteen' ),
			esc_url( get_permalink() ),
			$time_string
		);
	}
endif;

if ( ! function_exists( 'twentysixteen_entry_taxonomies' ) ) :
	/**
	 * Prints HTML with category and tags for current post.
	 *
	 * Create your own twentysixteen_entry_taxonomies() function to override in a child theme.
	 *
	 * @since Twenty Sixteen 1.0
	 */
	function twentysixteen_entry_taxonomies() {
		$categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentysixteen' ) );
		if ( $categories_list && twentysixteen_categorized_blog() ) {
			printf(
				'<span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
				_x( 'Categories', 'Used before category names.', 'twentysixteen' ),
				$categories_list
			);
		}

		$tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'twentysixteen' ) );
		if ( $tags_list && ! is_wp_error( $tags_list ) ) {
			printf(
				'<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
				_x( 'Tags', 'Used before tag names.', 'twentysixteen' ),
				$tags_list
			);
		}
	}
endif;

if ( ! function_exists( 'twentysixteen_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 *
	 * Create your own twentysixteen_post_thumbnail() function to override in a child theme.
	 *
	 * @since Twenty Sixteen 1.0
	 */
	function twentysixteen_post_thumbnail() {
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
		<?php the_post_thumbnail( 'post-thumbnail', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
	</a>

		<?php
	endif; // End is_singular().
	}
endif;

if ( ! function_exists( 'twentysixteen_excerpt' ) ) :
	/**
	 * Displays the optional excerpt.
	 *
	 * Wraps the excerpt in a div element.
	 *
	 * Create your own twentysixteen_excerpt() function to override in a child theme.
	 *
	 * @since Twenty Sixteen 1.0
	 *
	 * @param string $css_class Optional. Class string of the div element. Defaults to 'entry-summary'.
	 */
	function twentysixteen_excerpt( $css_class = 'entry-summary' ) {
		$css_class = esc_attr( $css_class );

		if ( has_excerpt() || is_search() ) :
			?>
			<div class="<?php echo $css_class; ?>">
				<?php the_excerpt(); ?>
			</div><!-- .<?php echo $css_class; ?> -->
			<?php
		endif;
	}
endif;

if ( ! function_exists( 'twentysixteen_excerpt_more' ) && ! is_admin() ) :
	/**
	 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
	 * a 'Continue reading' link.
	 *
	 * Create your own twentysixteen_excerpt_more() function to override in a child theme.
	 *
	 * @since Twenty Sixteen 1.0
	 *
	 * @return string 'Continue reading' link prepended with an ellipsis.
	 */
	function twentysixteen_excerpt_more() {
		$link = sprintf(
			'<a href="%1$s" class="more-link">%2$s</a>',
			esc_url( get_permalink( get_the_ID() ) ),
			/* translators: %s: Post title. Only visible to screen readers. */
			sprintf( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentysixteen' ), get_the_title( get_the_ID() ) )
		);
		return ' &hellip; ' . $link;
	}
	add_filter( 'excerpt_more', 'twentysixteen_excerpt_more' );
endif;

if ( ! function_exists( 'twentysixteen_categorized_blog' ) ) :
	/**
	 * Determines whether blog/site has more than one category.
	 *
	 * Create your own twentysixteen_categorized_blog() function to override in a child theme.
	 *
	 * @since Twenty Sixteen 1.0
	 *
	 * @return bool True if there is more than one category, false otherwise.
	 */
	function twentysixteen_categorized_blog() {
		$all_the_cool_cats = get_transient( 'twentysixteen_categories' );
		if ( false === $all_the_cool_cats ) {
			// Create an array of all the categories that are attached to posts.
			$all_the_cool_cats = get_categories(
				array(
					'fields' => 'ids',
					// We only need to know if there is more than one category.
					'number' => 2,
				)
			);

			// Count the number of categories that are attached to the posts.
			$all_the_cool_cats = count( $all_the_cool_cats );

			set_transient( 'twentysixteen_categories', $all_the_cool_cats );
		}

		if ( $all_the_cool_cats > 1 || is_preview() ) {
			// This blog has more than 1 category so twentysixteen_categorized_blog() should return true.
			return true;
		} else {
			// This blog has only 1 category so twentysixteen_categorized_blog() should return false.
			return false;
		}
	}
endif;

/**
 * Flushes out the transients used in twentysixteen_categorized_blog().
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'twentysixteen_categories' );
}
add_action( 'edit_category', 'twentysixteen_category_transient_flusher' );
add_action( 'save_post', 'twentysixteen_category_transient_flusher' );

if ( ! function_exists( 'twentysixteen_the_custom_logo' ) ) :
	/**
	 * Displays the optional custom logo.
	 *
	 * Does nothing if the custom logo is not available.
	 *
	 * @since Twenty Sixteen 1.2
	 */
	function twentysixteen_the_custom_logo() {
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
	 * @since Twenty Sixteen 2.0
	 */
	function wp_body_open() {
		/**
		 * Triggered after the opening <body> tag.
		 *
		 * @since Twenty Sixteen 2.0
		 */
		do_action( 'wp_body_open' );
	}
endif;
