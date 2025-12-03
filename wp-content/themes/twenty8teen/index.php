<?php defined('WPINC') or exit(); // No direct access.
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Twenty8teen
 */

get_header(); ?>

	<main <?php twenty8teen_area_classes( 'main', 'site-main' ); ?>>
		<div id="content" <?php twenty8teen_area_classes( 'content', 'content-area' ); ?>>

		<?php
		if ( have_posts() ) :

			if ( is_home() && ! is_front_page() ) : ?>
				<header <?php twenty8teen_attributes( 'header', 'class="page-header"' ); ?>>
					<h1 <?php twenty8teen_attributes( 'h1', 'class="page-title"' ); ?>>
					<?php single_post_title(); ?></h1>
				</header>

			<?php
			endif; ?>
			<div class="page-content"> <?php

		else : /* no posts */ ?>

			<header <?php twenty8teen_attributes( 'header', 'class="page-header"' ); ?>>
				<h1 <?php twenty8teen_attributes( 'h1', 'class="page-title"' ); ?>>
				<?php esc_html_e( 'Nothing Found', 'twenty8teen' ); ?></h1>
			</header><!-- .page-header -->
			<div class="page-content">
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
