<?php
/**
 * Template part for displaying posts.
 *
 * Please browse readme.txt for credits and forking information
 * @package noteblog
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post-content'); ?>>

		<?php noteblog_featured_image_disaplay(); ?>

		<header class="entry-header">
			<span class="screen-reader-text"><?php the_title();?></span>
			<?php if ( is_single() ) : ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php else : ?>
				<h2 class="entry-title">
					<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h2>
			<?php endif; // is_single() ?>

			<div class="entry-meta">
				<h5 class="entry-date"><?php noteblog_posted_on(); ?></h5>
			</div><!-- .entry-meta -->
		</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content('...<p class="read-more"><a class="btn btn-default" href="'. esc_url(get_permalink( get_the_ID() )) . '">' . __(' Read More', 'noteblog') . '<span class="screen-reader-text"> '. __(' Read More', 'noteblog').'</span></a></p>'); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'noteblog' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
	
</article><!-- #post -->
