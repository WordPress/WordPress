<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Theme Meme
 */

if ( ! function_exists( 'themememe_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable.
 */
function themememe_content_nav( $nav_id ) {
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

	$nav_class = ( is_single() ) ? 'post-navigation' : 'paging-navigation';

	?>
	<nav id="<?php echo esc_attr( $nav_id ); ?>" class="clearfix <?php echo $nav_class; ?>" role="navigation">
	<?php if ( is_single() ) : ?>

		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '<i class="fa fa-chevron-left"></i> Previous Article', 'Previous Article', 'themememe' ) . '</span> %title' ); ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', '<span class="meta-nav">' . _x( 'Next Article <i class="fa fa-chevron-right"></i>', 'Next Article', 'themememe' ) . '</span> %title' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : ?>

		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav"><i class="fa fa-chevron-left"></i></span> Previous Articles', 'themememe' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( __( 'Next Articles <span class="meta-nav"><i class="fa fa-chevron-right"></i></span>', 'themememe' ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
	<?php
}
endif;

if ( ! function_exists( 'themememe_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function themememe_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<div class="comment-body">
			<?php _e( 'Pingback:', 'themememe' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'themememe' ), '<span class="comment-meta"><span class="edit-link"><i class="fa fa-pencil"></i>', '</span></span>' ); ?>
		</div>

	<?php else : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<div class="comment-meta">
				<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
				<?php printf( __( '<strong class="comment-author">%s</strong>', 'themememe' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
				<span class="comment-date"><?php printf( _x( '%1$s / %2$s', '1: date, 2: time', 'themememe' ), get_comment_date(), get_comment_time() ); ?></span>
			</div>

			<?php if ( '0' == $comment->comment_approved ) : ?>
			<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'themememe' ); ?></p>
			<?php endif; ?>

			<div class="comment-content">
				<?php comment_text(); ?>
			</div>

			<div class="comment-meta comment-footer">
				<?php edit_comment_link( __( 'Edit', 'themememe' ), '<span class="edit-link"><i class="fa fa-pencil"></i>', '</span>' ); ?>
				<?php
					comment_reply_link( array_merge( $args, array(
						'add_below' => 'div-comment',
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<span class="comment-reply"><i class="fa fa-reply"></i>',
						'after'     => '</span>',
					) ) );
				?>
			</div>
		<!-- #div-comment-<?php comment_ID(); ?> --></article>

	<?php
	endif;
}
endif; // ends check for themememe_comment()

if ( ! function_exists( 'themememe_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function themememe_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	printf( __( '<span class="byline"><i class="fa fa-user"></i>%1$s</span><span class="posted-on"><i class="fa fa-calendar"></i>%2$s</span>', 'themememe' ),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		),
		sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>',
			esc_url( get_permalink() ),
			$time_string
		)
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 */
function themememe_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so themememe_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so themememe_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in themememe_categorized_blog.
 */
function themememe_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'themememe_category_transient_flusher' );
add_action( 'save_post',     'themememe_category_transient_flusher' );
