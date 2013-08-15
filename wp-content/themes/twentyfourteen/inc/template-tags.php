<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

if ( ! function_exists( 'twentyfourteen_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 *
 */
function twentyfourteen_content_nav( $nav_id ) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = 'site-navigation paging-navigation';
	if ( is_single() )
		$nav_class = 'site-navigation post-navigation';

	?>
	<nav role="navigation" id="<?php echo $nav_id; ?>" class="<?php echo $nav_class; ?>">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'twentyfourteen' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>

		<?php previous_post_link( '%link', __( '<div class="nav-previous"><span class="meta-nav">Previous Post</span>%title</div>', 'twentyfourteen' ) ); ?>
		<?php next_post_link( '%link', __( '<div class="nav-next"><span class="meta-nav">Next Post</span>%title</div>', 'twentyfourteen' ) ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<div class="pagination loop-pagination">
		<?php
			/* Get the current page. */
			$current = ( get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );

			/* Get the max number of pages. */
			$max_num_pages = intval( $wp_query->max_num_pages );

			/* Set up arguments for the paginate_links() function. */
			$args = array(
				'base' => add_query_arg( 'paged', '%#%' ),
				'format' => '',
				'total' => $max_num_pages,
				'current' => $current,
				'prev_text' => __( '&larr; Previous', 'twentyfourteen' ),
				'next_text' => __( 'Next &rarr;', 'twentyfourteen' ),
				'mid_size' => 1
			);

			echo paginate_links( $args )
		?>
		</div>
	<?php endif; ?>

	</nav><!-- #<?php echo $nav_id; ?> -->
	<?php
}
endif; // twentyfourteen_content_nav

if ( ! function_exists( 'twentyfourteen_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 */
function twentyfourteen_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twentyfourteen' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'twentyfourteen' ), ' ' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer>
				<div class="comment-author vcard">
					<span class="comment-author-avatar"><?php echo get_avatar( $comment, 32 ); ?></span>
					<?php printf( __( '%s', 'twentyfourteen' ), sprintf( '<cite class="fn">%s</cite> says:', get_comment_author_link() ) ); ?>
				</div><!-- .comment-author .vcard -->
			</footer>

			<div class="comment-content">
				<?php comment_text(); ?>
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<p><em><?php _e( 'Your comment is awaiting moderation.', 'twentyfourteen' ); ?></em></p>
				<?php endif; ?>
			</div>

			<div class="comment-meta commentmetadata">
				<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
				<?php
					/* translators: 1: date, 2: time */
					printf( __( '%1$s at %2$s', 'twentyfourteen' ), get_comment_date(), get_comment_time() ); ?>
				</time></a>
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				<?php edit_comment_link( __( 'Edit', 'twentyfourteen' ), ' ' );
				?>
			</div><!-- .comment-meta .commentmetadata -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for twentyfourteen_comment()

if ( ! function_exists( 'twentyfourteen_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 */
function twentyfourteen_posted_on() {
	if ( is_sticky() && is_home() && ! is_paged() )
		echo '<span class="featured-post">' . __( 'Sticky', 'twentyfourteen' ) . '</span>';

	printf( __( '<span class="entry-date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span> <span class="byline"><span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'twentyfourteen' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'twentyfourteen' ), get_the_author() ) ),
		get_the_author()
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category
 *
 */
function twentyfourteen_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so twentyfourteen_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so twentyfourteen_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in twentyfourteen_categorized_blog
 *
 */
function twentyfourteen_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'twentyfourteen_category_transient_flusher' );
add_action( 'save_post',     'twentyfourteen_category_transient_flusher' );

/**
 * Include the Post-Format-specific template for the content.
 * This is called in index.php and single.php
 */
function twentyfourteen_get_template_part() {
	if ( has_post_format( array( 'aside', 'quote', 'link', 'video', 'image' ) ) )
		get_template_part( 'content', 'post-format' );
	else
		get_template_part( 'content', get_post_format() );
}
