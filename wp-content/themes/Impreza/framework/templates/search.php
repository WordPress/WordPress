<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying search results pages
 */

$us_layout = US_Layout::instance();
// Needed for canvas class
$us_layout->titlebar = ( us_get_option( 'titlebar_archive_content', 'all' ) == 'hide' ) ? 'none' : 'default';
$us_layout->sidebar_pos = us_get_option( 'search_sidebar', 'right' );
get_header();

// Creating .l-titlebar
us_load_template( 'templates/titlebar', array(
	'title' => __( 'Search Results for', 'us' ) . ' &quot;' . esc_attr( get_search_query() ) . '&quot;',
) );

$template_vars = array(
	'layout_type' => us_get_option( 'search_layout', 'compact' ),
	'masonry' => us_get_option( 'search_masonry', 0 ),
	'columns' => us_get_option( 'search_cols', 1 ),
	'metas' => (array) us_get_option( 'search_meta', array() ),
	'content_type' => us_get_option( 'search_content_type', 'excerpt' ),
	'show_read_more' => in_array( 'read_more', (array) us_get_option( 'search_meta', array() ) ),
	'pagination' => us_get_option( 'search_pagination', 'regular' ),
);
?>
<!-- MAIN -->
<div class="l-main">
	<div class="l-main-h i-cf">

		<main class="l-content" itemprop="mainContentOfPage">
			<section class="l-section">
				<div class="l-section-h i-cf">

					<?php do_action( 'us_before_search' ) ?>

					<?php us_load_template( 'templates/blog/listing', $template_vars ) ?>

					<?php do_action( 'us_after_search' ) ?>

				</div>
			</section>
		</main>

<?php if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ): ?>
		<aside class="l-sidebar at_<?php echo $us_layout->sidebar_pos ?>" itemscope="itemscope" itemtype="https://schema.org/WPSideBar">
			<?php dynamic_sidebar( 'default_sidebar' ) ?>
		</aside>
<?php endif; ?>

	</div>
</div>


<?php
get_footer();
