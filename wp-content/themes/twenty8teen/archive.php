<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Twenty8teen
 */

get_header(); ?>

	<main <?php twenty8teen_area_classes( 'main', 'site-main' ); ?>>
		<div id="content" <?php twenty8teen_area_classes( 'content', 'content-area' ); ?>>

			<header <?php twenty8teen_attributes( 'header', 'class="page-header"' ); ?>>
				<?php
					the_archive_title( '<h1 ' . twenty8teen_attributes( 'h1', 'class="page-title"', false ) . '>', '</h1>' );
					if ( ! is_paged() ) {
						the_archive_description( '<div class="archive-description">', '</div>' );
					}
				?>
			</header><!-- .page-header -->
			<div class="page-content">

				<?php if ( ! have_posts() ) : ?>
					<h2><?php esc_html_e( 'Nothing Found', 'twenty8teen' ); ?></h2>
					<p>
						<?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'twenty8teen' ); ?>
					</p>
					<?php get_search_form();
				endif;

				if ( is_active_sidebar( 'content-widget-area' ) ) {
					dynamic_sidebar( 'content-widget-area' );
				}
				else {
					get_template_part( 'template-parts/content-loop' );
					get_template_part( 'template-parts/posts-pagination' );
				} ?>
			</div><!-- .page-content -->

		</div><!-- #content -->
		<?php get_sidebar(); ?>
	</main>

<?php

get_footer();
