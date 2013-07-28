<?php
/**
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
$format = get_post_format();
if ( false === $format )
	$format = 'standard';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ( 'video' != $format ) && ( 'image' != $format ) && ( 'aside' != $format ) && ( 'link' != $format ) && ( 'quote' != $format ) ) : ?>
	<div class="attachment-featured-thumbnail">
	<?php
		if ( '' != get_the_post_thumbnail() )
			the_post_thumbnail( 'featured-thumbnail-large' );
	?>
	</div>
	<?php endif; ?>

	<header class="entry-header">
		<div class="entry-meta">
			<?php
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'twentyfourteen' ) );
				if ( $categories_list && twentyfourteen_categorized_blog() ) :
			?>
			<span class="cat-links">
				<?php echo $categories_list; ?>
			</span>
			<?php endif; // End if categories ?>
		</div><!-- .entry-meta -->

		<?php if ( ( 'standard' == $format ) || ( 'video' == $format ) || ( 'image' == $format ) || ( 'gallery' == $format ) ) : ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php endif; ?>

		<div class="entry-meta">
			<?php if ( 'standard' != $format ) : ?>
			<span class="post-format">
				<a class="entry-format" href="<?php echo esc_url( get_post_format_link( get_post_format() ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'All %s posts', 'twentyfourteen' ), get_post_format_string( get_post_format() ) ) ); ?>"><?php echo get_post_format_string( get_post_format() ); ?></a>
			</span>
			<?php endif; ?>

			<?php twentyfourteen_posted_on(); ?>

			<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></span>
			<?php endif; ?>

			<?php edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content clearfix">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>'
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-meta">
		<?php if ( ( 'quote' == $format ) || ( 'aside' == $format ) ) : ?>
		<div class="entry-meta">
			<?php the_title( '<h1 class="entry-title"><a href="' . get_permalink() . '" rel="bookmark">', '</a></h1>' ); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>

		<?php
			$tag_list = get_the_tag_list();
			if ( '' != $tag_list ) :
		?>
			<span class="tag-links">
				<?php echo $tag_list; ?>
			</span>
		<?php endif; // End if $tag_list ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
