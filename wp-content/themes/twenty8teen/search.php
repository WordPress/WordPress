<?php
/**
 * The template for displaying search results pages
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 * @package Twenty8teen
 */

get_header(); ?>
	<main <?php twenty8teen_area_classes( 'main', 'site-main' ); ?>>
		<div id="content" <?php twenty8teen_area_classes( 'content', 'content-area' ); ?>>

			<header <?php twenty8teen_attributes( 'header', 'class="page-header"' ); ?>>
				<h1 <?php twenty8teen_attributes( 'h1', 'class="page-title"' ); ?>>
					<?php
					/* translators: %s: search query. */
					printf( esc_html__( 'Search Results for: %s', 'twenty8teen' ), '<span>' . get_search_query() . '</span>' );
				?></h1>
				<?php get_search_form(); ?>
			</header><!-- .page-header -->

			<div class="page-content">
		<?php
		if ( ! have_posts() ) : ?>
			<p>
			<?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'twenty8teen' ); ?>
			</p>
			<?php
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
