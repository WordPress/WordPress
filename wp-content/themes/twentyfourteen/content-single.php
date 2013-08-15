<?php
/**
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( has_post_thumbnail() && ! has_post_format( array( 'video', 'image', 'aside', 'link', 'quote' ) ) ) : ?>
	<div class="attachment-featured-thumbnail">
	<?php the_post_thumbnail( 'featured-thumbnail-large' ); ?>
	</div>
	<?php endif; ?>

	<header class="entry-header">
		<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && twentyfourteen_categorized_blog() ) : ?>
		<div class="entry-meta">
			<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfourteen' ) ); ?></span>
		</div><!-- .entry-meta -->
		<?php
			endif;

			if ( ! has_post_format( array( 'status', 'chat', 'aside', 'link', 'quote', 'audio' ) ) ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			endif;
		?>

		<div class="entry-meta">
			<?php if ( has_post_format( array( 'status', 'chat', 'aside', 'link', 'quote', 'audio' ) ) ) : ?>
			<span class="post-format">
				<a class="entry-format" href="<?php echo esc_url( get_post_format_link( get_post_format() ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'All %s posts', 'twentyfourteen' ), get_post_format_string( get_post_format() ) ) ); ?>"><?php echo get_post_format_string( get_post_format() ); ?></a>
			</span>
			<?php endif; ?>

			<?php twentyfourteen_posted_on(); ?>

			<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></span>
			<?php endif; ?>

			<?php edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			the_content();
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-meta">
		<?php if ( has_post_format( array( 'quote', 'aside' ) ) ) : ?>
		<div class="entry-meta">
			<?php the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' ); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>

		<?php if ( has_tag() ) : ?>
			<span class="tag-links">
				<?php echo get_the_tag_list(); ?>
			</span>
		<?php endif; ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-## -->
