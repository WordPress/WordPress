<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

if ( ! function_exists( 'twentyfifteen_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since Twenty Fifteen 1.0
 * @uses paginate_links()
 *
 * @global WP_Query $wp_query WordPress Query object.
 */
function twentyfifteen_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

 	// Set up paginated links.
	$links = paginate_links( array(
		'prev_text'          => esc_html__( 'Previous', 'twentyfifteen' ),
		'next_text'          => esc_html__( 'Next', 'twentyfifteen' ),
		'before_page_number' => '<span class="meta-nav">' . esc_html__( 'Page', 'twentyfifteen' ) . '</span>',
	) );

	if ( $links ) :
	?>
	<nav class="navigation pagination" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'twentyfifteen' ); ?></h1>
		<div class="nav-links">
			<?php echo $links; ?>
		</div><!-- .nav-links -->
	</nav><!-- .pagination -->
	<?php
	endif;
}
endif;

if ( ! function_exists( 'twentyfifteen_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ( ! $next && ! $previous ) || ( is_attachment() && 'attachment' == $previous->post_type ) ) {
		return;
	}

	$prev_class = $next_class = '';

	if ( $previous && has_post_thumbnail( $previous->ID ) ) {
		$prev_class = " has-post-thumbnail";
	}

	if ( $next && has_post_thumbnail( $next->ID ) ) {
		$next_class = " has-post-thumbnail";
	}

	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'twentyfifteen' ); ?></h1>
		<div class="nav-links">
			<?php
			if ( is_attachment() ) :
				previous_post_link( '<div class="nav-previous' . $prev_class . '">%link</div>', _x( '<span class="meta-nav">Published In</span><span class="post-title">%title</span>', 'Parent post link', 'twentyfifteen' ) );
			else :
				previous_post_link( '<div class="nav-previous' . $prev_class . '">%link</div>', _x( '<span class="meta-nav">Previous</span><span class="post-title">%title</span>', 'Previous post link', 'twentyfifteen' ) );
				next_post_link( '<div class="nav-next' . $next_class . '">%link</div>', _x( '<span class="meta-nav">Next</span><span class="post-title">%title</span>', 'Next post link', 'twentyfifteen' ) );
			endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .post-navigation -->
	<?php
}
endif;

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
	<nav class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'twentyfifteen' ); ?></h1>
		<div class="nav-links">
			<?php
				if ( $prev_link = get_previous_comments_link( esc_html__( 'Older Comments', 'twentyfifteen' ) ) ) :
					printf( '<div class="nav-previous">%s</div>', $prev_link );
				endif;

				if ( $next_link = get_next_comments_link( esc_html__( 'Newer Comments', 'twentyfifteen' ) ) ) :
					printf( '<div class="nav-next">%s</div>', $next_link );
				endif;
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
		printf( '<span class="sticky-post">%s</span>', esc_html__( 'Featured', 'twentyfifteen' ) );
	}

	$format = get_post_format();
	if ( current_theme_supports( 'post-formats', $format ) ) {
		printf( '<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
			sprintf( '<span class="screen-reader-text">%s</span>', esc_html_x( 'Format', 'Used before post format.', 'twentyfifteen' ) ),
			esc_url( get_post_format_link( $format ) ),
			get_post_format_string( $format )
		);
	}

	if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		printf( '<span class="posted-on">%1$s<a href="%2$s" rel="bookmark">%3$s</a></span>',
			sprintf( _x( '<span class="screen-reader-text">Posted on</span>', 'Used before publish date.', 'twentyfifteen' ) ),
			esc_url( get_permalink() ),
			$time_string
		);
	}

	if ( 'post' == get_post_type() ) {
		if ( is_singular() || is_multi_author() ) {
			printf( '<span class="byline"><span class="author vcard">%1$s<a class="url fn n" href="%2$s">%3$s</a></span></span>',
				sprintf( _x( '<span class="screen-reader-text">Author</span>', 'Used before post author name.', 'twentyfifteen' ) ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				esc_html( get_the_author() )
			);
		}

		$categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfifteen' ) );
		if ( $categories_list && twentyfifteen_categorized_blog() ) {
			printf( '<span class="cat-links">%1$s%2$s</span>',
				sprintf( _x( '<span class="screen-reader-text">Categories</span>', 'Used before category names.', 'twentyfifteen' ) ),
				$categories_list
			);
		}

		$tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfifteen' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">%1$s%2$s</span>',
				sprintf( _x( '<span class="screen-reader-text">Tags</span>', 'Used before tag names.', 'twentyfifteen' ) ),
				$tags_list
			);
		}
	}

	if ( is_attachment() && wp_attachment_is_image() ) {
		// Retrieve attachment metadata.
		$metadata = wp_get_attachment_metadata();

		printf( '<span class="full-size-link">%1$s<a href="%2$s">%3$s &times; %4$s</a></span>',
			sprintf( _x( '<span class="screen-reader-text">Full size</span>', 'Used before full size attachment link.', 'twentyfifteen' ) ),
			esc_url( wp_get_attachment_url() ),
			$metadata['width'],
			$metadata['height']
		);
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( esc_html__( 'Leave a comment', 'twentyfifteen' ), esc_html__( '1 Comment', 'twentyfifteen' ), esc_html__( '% Comments', 'twentyfifteen' ) );
		echo '</span>';
	}
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @since Twenty Fifteen 1.0
 *
 * @return bool
 */
function twentyfifteen_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'twentyfifteen_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'twentyfifteen_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so twentyfifteen_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so twentyfifteen_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in twentyfifteen_categorized_blog.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'twentyfifteen_categories' );
}
add_action( 'edit_category', 'twentyfifteen_category_transient_flusher' );
add_action( 'save_post',     'twentyfifteen_category_transient_flusher' );

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

	<a class="post-thumbnail" href="<?php the_permalink(); ?>">
		<?php the_post_thumbnail(); ?>
	</a>

	<?php endif; // End is_singular()
}
endif;

if ( ! function_exists( 'twentyfifteen_get_link_url' ) ) :
/**
 * Return the post URL.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 * @since Twenty Fifteen 1.0
 * @uses get_url_in_content()
 *
 * @return string The Link format URL.
 */
function twentyfifteen_get_link_url() {
	$has_url = get_url_in_content( get_the_content() );

	return $has_url ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}
endif;