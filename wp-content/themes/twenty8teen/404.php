<?php
/**
 * The template for displaying the 404 page (not found)
 *
 * @link https://developer.wordpress.org/themes/functionality/404-pages/
 * @package Twenty8teen
 */

get_header();
global $wp;

$taxonomies = get_taxonomies( array(
	'publicly_queryable' => true,
	), 'objects' );
$tax_title = '';
if ( in_array( $wp->request, array_keys( $taxonomies ) ) ) {
	$a_taxonomy = $taxonomies[$wp->request];
	$tax_title = $a_taxonomy->label;
}
?>

	<main <?php twenty8teen_area_classes( 'main', 'site-main' ); ?>>
		<div id="content" <?php twenty8teen_area_classes( 'content', 'content-area' ); ?>>

				<header <?php twenty8teen_attributes( 'header', 'class="page-header"' ); ?>>
					<h1 <?php twenty8teen_attributes( 'h1', 'class="page-title"' ); ?>>
					<?php
					if ( $tax_title ) {
						echo esc_html( $tax_title );
					} else {
						esc_html_e( 'Oops! That page can&rsquo;t be found.', 'twenty8teen' );
					} ?>

					</h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<?php
					if ( $tax_title ) {
						echo '<p class="taxonomy-description">' . esc_html( $a_taxonomy->description ) . '</p>';
						echo '<ul class="' . esc_attr( $a_taxonomy->name ) . ' taxonomy-list">';
						wp_list_categories( array(
							'taxonomy'   => $a_taxonomy->name,
							'orderby'    => 'count',
							'order'      => 'DESC',
							'show_count' => 1,
							'title_li'   => '',
						) );
						echo '</ul><!-- .taxonomy-list -->';
					}
					else {
						echo '<p>';
						esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search.', 'twenty8teen' );
						echo '</p>';
						get_search_form();
						$have_output = false;
						foreach ( $taxonomies  as $a_taxonomy ) {
							$out = wp_list_categories( array(
								'taxonomy'   => $a_taxonomy->name,
								'orderby'    => 'count',
								'order'      => 'DESC',
								'show_count' => 1,
								'title_li'   => '',
								'show_option_none'   => '',
								'number'     => 15,
								'echo'       => 0,
							) );
							if ( $out ) {
								$have_output = true; ?>
								<div class="widget widget_<?php echo esc_attr( $a_taxonomy->name ); ?>">
									<h3 class="widget-title"><?php printf(
									/* translators: %s: taxonomy label. */
										esc_html_x( 'Most Used %s', 'taxonomy label', 'twenty8teen' ),
										$a_taxonomy->label ); ?></h3>
									<ul class="taxonomy-list <?php echo esc_attr( $a_taxonomy->name ); ?>">
									<?php echo $out; ?>
									</ul>
								</div><!-- .widget -->
							<?php
							}
						}

						if ( ! $have_output ) {
							the_widget( 'WP_Widget_Pages' );
						}
					} ?>

				</div><!-- .page-content -->

		</div><!-- #content -->
		<?php get_sidebar(); ?>
	</main>

<?php

get_footer();
