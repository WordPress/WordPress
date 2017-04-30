<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Theme Meme
 */

get_header(); ?>

	<div class="row">
		<div class="col-md-8 content-area" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'themememe' ); ?></h1>
				<!-- .page-header --></header>

				<div class="page-content">
					<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'themememe' ); ?></p>

					<?php get_search_form(); ?>

					<?php the_widget( 'WP_Widget_Recent_Posts', '' , 'before_title=<h3 class="widget-title">&after_title=</h3>' ); ?>

					<?php if ( themememe_categorized_blog() ) : ?>
					<div class="widget widget_categories">
						<h3 class="widget-title"><?php _e( 'Most Used Categories', 'themememe' ); ?></h3>
						<ul>
						<?php
							wp_list_categories( array(
								'orderby'    => 'count',
								'order'      => 'DESC',
								'show_count' => 1,
								'title_li'   => '',
								'number'     => 10,
							) );
						?>
						</ul>
					</div>
					<?php endif; ?>

					<?php
					$archive_content = '<p>' . sprintf( __( 'Try looking in the monthly archives. %1$s', 'themememe' ), convert_smilies( ':)' ) ) . '</p>';
					the_widget( 'WP_Widget_Archives', 'dropdown=1', 'before_title=<h3 class="widget-title">&after_title=</h3>' );
					?>

					<?php the_widget( 'WP_Widget_Tag_Cloud', '' , 'before_title=<h3 class="widget-title">&after_title=</h3>' ); ?>

				<!-- .page-content --></div>
			<!-- .error-404 --></section>

		<!-- .content-area --></div>

		<?php get_sidebar(); ?>
	</div>

<?php get_footer(); ?>