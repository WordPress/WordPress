<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
<<<<<<< HEAD
 * @since Twenty Nineteen 1.0
=======
 * @since 1.0.0
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ! twentynineteen_can_show_post_thumbnail() ) : ?>
	<header class="entry-header">
		<?php get_template_part( 'template-parts/header/entry', 'header' ); ?>
	</header>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'twentynineteen' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
					wp_kses(
<<<<<<< HEAD
						/* translators: %s: Post title. Only visible to screen readers. */
=======
						/* translators: %s: Name of current post. Only visible to screen readers */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
						__( 'Edit <span class="screen-reader-text">%s</span>', 'twentynineteen' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				),
<<<<<<< HEAD
				'<span class="edit-link">' . twentynineteen_get_icon_svg( 'edit', 16 ),
=======
				'<span class="edit-link">',
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				'</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
