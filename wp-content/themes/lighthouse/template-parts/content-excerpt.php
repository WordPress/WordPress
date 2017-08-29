<?php
/**
 * Template part for displaying posts.
 *
 * Please browse readme.txt for credits and forking information
 * @package Lighthouse
 */

?>

<article id="post-<?php the_ID(); ?>"  <?php post_class('post-content'); ?>>

	<?php
	if ( is_sticky() && is_home() && ! is_paged() ) {
		printf( '<span class="sticky-post">%s</span>', __( 'Featured', 'lighthouse' ) );
	} ?>
<a href="<?php the_permalink(); ?>" rel="bookmark">
	<?php lighthouse_featured_image_disaplay(); ?>
	</a>
	<header class="entry-header">	
	
		<span class="screen-reader-text"><?php the_title();?></span>

		<?php if ( is_single() ) : ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php else : ?>
			<h2 class="entry-title">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>
		<?php endif; // is_single() ?>

		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta">
			<h5 class="entry-date"><?php lighthouse_posted_on(); ?></h5>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->
		
    <div class="entry-summary">

        <?php the_excerpt(); ?>
    </div><!-- .entry-summary -->		   	

	<footer class="entry-footer">
		<?php lighthouse_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
