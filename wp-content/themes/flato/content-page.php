<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Theme Meme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	<!-- .entry-header --></header>

	<div class="clearfix entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'themememe' ),
				'after'  => '</div>',
			) );
		?>
	<!-- .entry-content --></div>
	<?php edit_post_link( __( 'Edit', 'themememe' ), '<footer class="entry-meta entry-footer"><span class="edit-link"><i class="fa fa-pencil"></i>', '</span></footer>' ); ?>
<!-- #post-<?php the_ID(); ?> --></article>