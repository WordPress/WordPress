
<?php
/**
 * Template part for displaying posts.
 *
 * Please browse readme.txt for credits and forking information
 * @package noteblog
 */

?>
<article id="post-<?php the_ID(); ?>"  <?php post_class('post-content'); ?>>
	<div class="row post-feed-wrapper">
		<?php if ( has_post_thumbnail() ) : ?>

		<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
		<div class="featured-banner">
			<?php echo __( 'Featured', 'noteblog' ); ?>
		</div>
	<?php endif; ?>

	<div class="col-md-12 post-thumbnail-wrap">
		<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );?>
		<a href="<?php the_permalink(); ?>" rel="bookmark">
			<div class="post-thumbnail" style="background-image: url('<?php echo esc_url($thumb['0']);?>')"></div>
			<h5 class="entry-date"><?php noteblog_posted_on(); ?></h5>
		</a>
	</div>
<?php endif; ?>

<div class="col-md-12">
	<div class="blog-feed-contant">
		<header class="entry-header">	
			<?php if ( has_post_thumbnail() ) : ?>
		<?php else : ?>
		<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
		<div class="featured-banner-no-thumbnail">
			<div class="featured-banner">
				<?php echo __( 'Featured', 'noteblog' ); ?>
			</div>
		</div>
	<?php endif; ?>

<?php endif; ?>
<span class="screen-reader-text"><?php the_title();?></span>

<?php if ( is_single() ) : ?>
	<h1 class="entry-title"><?php the_title(); ?></h1>
<?php else : ?>
	<h2 class="entry-title">
		<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
	</h2>
<?php endif; // is_single() ?>

<?php if ( 'post' == get_post_type() ) : ?>
	<?php if ( has_post_thumbnail() ) : ?>
<?php else : ?>
	<div class="entry-meta">
		<h5 class="entry-date"><?php noteblog_posted_on(); ?></h5>
	</div><!-- .entry-meta -->
<?php endif; ?>
<?php endif; ?>
</header><!-- .entry-header -->
<a href="<?php the_permalink(); ?>" class="no-decoration">
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->	
</a>	   	
</div>
</div>
</div>
</article><!-- #post-## -->
