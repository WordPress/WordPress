<?php
/**
 * The template part for displaying results in search pages.
 *
 * Please browse readme.txt for credits and forking information
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package noteblog
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post-content'); ?>>
	<a href="<?php the_permalink(); ?>" rel="bookmark">
	<?php noteblog_featured_image_disaplay(); ?>
	</a>
	<header class="entry-header">
		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta">
			<?php noteblog_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->

</article><!-- #post-## -->

