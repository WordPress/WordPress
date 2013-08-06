<?php
/**
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
$format = get_post_format();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?>>
	<?php
		if ( 'gallery' == $format ) :
			$images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC' ) );
			if ( $images ) :
				$image = array_shift( $images ); ?>
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentyfourteen' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="<?php the_ID(); ?>" class="attachment-featured-thumbnail">
				<?php echo wp_get_attachment_image( $image->ID, 'featured-thumbnail-large' ); ?>
				</a><?php
			endif;
		endif;
	?>

	<header class="entry-header">
		<?php
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( __( ', ', 'twentyfourteen' ) );
			if ( $categories_list && twentyfourteen_categorized_blog() && 'post' == get_post_type() ) :
		?>
		<div class="entry-meta">
			<span class="cat-links"><?php echo $categories_list; ?></span>
		</div>
		<?php endif; ?>

		<?php
			/* Show title only if it exists */
			the_title( '<h1 class="entry-title"><a href="' . get_permalink() . '" rel="bookmark">', '</a></h1>' );
		?>

		<div class="entry-meta">
			<span class="post-format">
				<a class="entry-format" href="<?php echo esc_url( get_post_format_link( get_post_format() ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'All %s posts', 'twentyfourteen' ), get_post_format_string( get_post_format() ) ) ); ?>"><?php echo get_post_format_string( get_post_format() ); ?></a>
			</span>

			<?php
				if ( 'post' == get_post_type() )
					twentyfourteen_posted_on();
			?>

			<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></span>
			<?php endif; ?>

			<?php edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content clearfix">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) ); ?>
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
		</div>
		<?php endif; ?>

		<?php
			$tag_list = get_the_tag_list();
			if ( '' != $tag_list && 'post' == get_post_type() ) :
		?>
		<span class="tag-links"><?php echo $tag_list; ?></span>
		<?php endif; // End if $tag_list ?>
	</footer><!-- .entry-meta -->

</article><!-- #post-<?php the_ID(); ?> -->