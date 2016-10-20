<?php
/**
 * The front page template file
 *
 * If the user has selected a static page for their homepage, this is what will
 * appear.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php // Show the selected frontpage content.
		if ( have_posts() ) :
			while ( have_posts() ) : the_post();
				get_template_part( 'components/page/content', 'front-page' );
			endwhile;
		else : // I'm not sure it's possible to have no posts when this page is shown, but WTH.
			get_template_part( 'components/post/content', 'none' );
		endif; ?>

		<?php
		// Get each of our panels and show the post data.
		$panels = array( '1', '2', '3', '4' );
		$titles = array();

		global $twentyseventeencounter; // Used in components/page/content-front-page-panels.php file.

		if ( 0 !== twentyseventeen_panel_count() || is_customize_preview() ) : // If we have pages to show.

			$twentyseventeencounter = 1;

			foreach ( $panels as $panel ) :
				if ( get_theme_mod( 'panel_' . $panel ) ) :
					$post = get_post( get_theme_mod( 'panel_' . $panel ) );
					setup_postdata( $post );
					set_query_var( 'panel', $panel );

					$titles[] = get_the_title(); // Put page titles in an array for use in navigation.
					get_template_part( 'components/page/content', 'front-page-panels' );

					wp_reset_postdata();
				else :
					// The output placeholder anchor.
					echo '<article class="panel-placeholder panel twentyseventeen-panel twentyseventeen-panel' . esc_attr( $twentyseventeencounter ) . '" id="panel' . esc_attr( $twentyseventeencounter ) . '"><span class="twentyseventeen-panel-title">' . sprintf( __( 'Panel %1$s Placeholder', 'twentyseventeen' ), esc_attr( $twentyseventeencounter ) ) . '</span></article>';
				endif;

				$twentyseventeencounter++;
			endforeach;
			?>

	<?php endif; // The if ( 0 !== twentyseventeen_panel_count() ) ends here.
	?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
