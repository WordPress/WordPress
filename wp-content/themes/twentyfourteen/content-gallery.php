<?php
/**
 * The template for displaying posts in the Gallery post format.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

if ( has_post_thumbnail() ) :
	$image = get_post_thumbnail_id();
else :
	$images = get_posts( array(
		'post_parent'    => get_the_ID(),
		'fields'         => 'ids',
		'numberposts'    => 1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID',
	) );
	$image = array_shift( $images );
endif;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if( is_single() && $image ) : ?>
	<div class="attachment-featured-thumbnail">
		<?php echo wp_get_attachment_image( $image, 'featured-thumbnail-large' ); ?>
	</div>
	<?php elseif ( $image ) : ?>
	<a class="attachment-featured-thumbnail" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentyfourteen' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="<?php the_ID(); ?>">
		<?php echo wp_get_attachment_image( $image, 'featured-thumbnail-large' ); ?>
	</a>
	<?php endif; ?>

	<header class="entry-header">
		<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && twentyfourteen_categorized_blog() ) : ?>
		<div class="entry-meta">
			<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfourteen' ) ); ?></span>
		</div><!-- .entry-meta -->
		<?php
			endif;

			if ( is_single() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
			endif;
		?>

		<div class="entry-meta">
			<span class="post-format">
				<a class="entry-format" href="<?php echo esc_url( get_post_format_link( 'gallery' ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'All %s posts', 'twentyfourteen' ), get_post_format_string( 'gallery' ) ) ); ?>"><?php echo get_post_format_string( 'gallery' ); ?></a>
			</span>

			<?php twentyfourteen_posted_on(); ?>

			<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></span>
			<?php endif; ?>

			<?php edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) );
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );
		?>
	</div><!-- .entry-content -->

	<?php if ( has_tag() ) : ?>
	<footer class="entry-meta">
		<span class="tag-links"><?php echo get_the_tag_list(); ?></span>
	</footer><!-- .entry-meta -->
	<?php endif; ?>
</article><!-- #post-## -->
