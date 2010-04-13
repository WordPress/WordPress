<?php
/**
 * The loop that displays posts
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * @package WordPress
 * @subpackage Twenty Ten
 * @since 3.0.0
 */
?>

<?php /* Display navigation to next/previous pages when applicable  */ ?>
<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div id="nav-above" class="navigation">
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
	</div><!-- #nav-above -->
<?php endif; ?>

<?php /* If there are no posts to display, such as an empty archive page  */ ?>
<?php if ( ! have_posts() ) : ?>
	<div id="post-0" class="post error404 not-found">
		<h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
		<div class="entry-content">
			<p><?php _e( 'Apologies, but no results were found for the requested Archive. Perhaps searching will help find a related post.', 'twentyten' ); ?></p>
			<?php get_search_form(); ?>
		</div><!-- .entry-content -->
	</div><!-- #post-0 -->
<?php endif; ?>

<?php /* Start the Loop  */ ?>
<?php while ( have_posts() ) : the_post(); ?>

<?php /* How to Display posts in the Gallery Category  */ ?>
	<?php if ( in_category( 'Gallery' ) ) : ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			<div class="entry-meta">
				<?php
					printf( __( '<span class="meta-prep meta-prep-author">Posted on </span><abbr title="%1$s"><span class="entry-date">%2$s</span></abbr> <span class="meta-sep"> by </span> <span class="author vcard"><a class="url fn n" href="%3$s" title="%4$s">%5$s</a></span>', 'twentyten' ),
						esc_attr( get_the_time() ),
						get_the_date(),
						get_author_posts_url( get_the_author_meta( 'ID' ) ),
						sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
						get_the_author()
					);
				?>
			</div><!-- .entry-meta -->

			<div class="entry-content">
				<div class="gallery-thumb">
					<a class="size-thumbnail" href="<?php the_permalink(); ?>"><?php
					$images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 999 ) );
					$total_images = count( $images );
					$image = array_shift( $images );
					echo wp_get_attachment_image( $image->ID, 'thumbnail' );
					?></a>
				</div>
				<p><em><?php printf( __( 'This gallery contains <a %1$s>%2$s photos</a>.', 'twentyten' ),
						'href="' . get_permalink() . '" title="' . sprintf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark"',
						$total_images
					); ?></em></p>

				<?php the_excerpt( '' ); ?>
			</div><!-- .entry-content -->

			<div class="entry-utility">
				<?php
					$category_id = get_cat_ID( 'Gallery' );
					$category_link = get_category_link( $category_id );
				?>
				<a href="<?php echo $category_link; ?>" title="<?php esc_attr_e( 'View posts in the Gallery category', 'twentyten' ); ?>"><?php _e( 'More Galleries', 'twentyten' ); ?></a>
				<span class="meta-sep"> | </span>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- #entry-utility -->
		</div>

<?php /* How to display posts in the asides category */ ?>
	<?php elseif ( in_category( 'asides' ) ) : ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( is_archive() || is_search() ) : //Only display Excerpts for archives & search ?>
			<div class="entry-summary">
				<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
			</div><!-- .entry-summary -->
	<?php else : ?>
			<div class="entry-content">
				<?php the_content( __( 'Continue&nbsp;reading&nbsp;<span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
			</div><!-- .entry-content -->
	<?php endif; ?>

			<div class="entry-utility">
				<?php
					printf( __( '<span class="meta-prep meta-prep-author">Posted on </span><abbr title="%1$s"><span class="entry-date">%2$s</span></abbr> <span class="meta-sep"> by </span> <span class="author vcard"><a class="url fn n" href="%3$s" title="%4$s">%5$s</a></span>', 'twentyten' ),
						esc_attr( get_the_time() ),
						get_the_date(),
						get_author_posts_url( get_the_author_meta( 'ID' ) ),
						sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
						get_the_author()
					);
				?>
				<span class="meta-sep"> | </span>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- #entry-utility -->
		</div><!-- #post-<?php the_ID(); ?> -->

<?php /* How to display all other posts  */ ?>
	<?php else : ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

			<div class="entry-meta">
				<?php
					printf( __( '<span class="meta-prep meta-prep-author">Posted on </span><abbr title="%1$s"><span class="entry-date">%2$s</span></abbr> <span class="meta-sep"> by </span> <span class="author vcard"><a class="url fn n" href="%3$s" title="%4$s">%5$s</a></span>', 'twentyten' ),
						esc_attr( get_the_time() ),
						get_the_date(),
						get_author_posts_url( get_the_author_meta( 'ID' ) ),
						sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
						get_the_author()
					);
				?>
			</div><!-- .entry-meta -->

	<?php if ( is_archive() || is_search() ) : //Only display Excerpts for archives & search ?>
			<div class="entry-summary">
				<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
			</div><!-- .entry-summary -->
	<?php else : ?>
			<div class="entry-content">
				<?php the_content( __( 'Continue&nbsp;reading&nbsp;<span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
			</div><!-- .entry-content -->
	<?php endif; ?>

			<div class="entry-utility">
				<span class="cat-links"><span class="entry-utility-prep entry-utility-prep-cat-links"><?php echo twentyten_cat_list(); ?></span></span>
				<span class="meta-sep"> | </span>
				<?php $tags_text = twentyten_tag_list(); ?>
				<?php if ( ! empty( $tags_text ) ) : ?>
				<span class="tag-links"><span class="entry-utility-prep entry-utility-prep-tag-links"><?php echo $tags_text; ?></span></span>
				<span class="meta-sep"> | </span>
				<?php endif; //$tags_text ?>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- #entry-utility -->
		</div><!-- #post-<?php the_ID(); ?> -->

		<?php comments_template( '', true ); ?>

	<?php endif; // if different categories queried ?>
<?php endwhile; ?>

<?php /* Display navigation to next/previous pages when applicable  */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
				</div><!-- #nav-below -->
<?php endif; ?>
