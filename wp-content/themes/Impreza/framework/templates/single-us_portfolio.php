<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * The template for displaying portfolio items
 */
$us_layout = US_Layout::instance();
get_header();
us_load_template( 'templates/titlebar' );
$default_portfolio_sidebar_id = us_get_option( 'portfolio_sidebar_id', 'default_sidebar' );
?>
<!-- MAIN -->
<div class="l-main">
	<div class="l-main-h i-cf">

		<main class="l-content" itemprop="mainContentOfPage">

			<?php do_action( 'us_before_us_portfolio' ) ?>

			<?php
			while ( have_posts() ){
				the_post();

				$the_content = apply_filters( 'the_content', get_the_content() );

				// The page may be paginated itself via <!--nextpage--> tags
				$pagination = us_wp_link_pages( array(
					'before' => '<div class="w-blog-pagination"><nav class="navigation pagination">',
					'after' => '</nav></div>',
					'next_or_number' => 'next_and_number',
					'nextpagelink' => '>',
					'previouspagelink' => '<',
					'link_before' => '<span>',
					'link_after' => '</span>',
					'echo' => 0,
				) );

				// If content has no sections, we'll create them manually
				$has_own_sections = ( strpos( $the_content, ' class="l-section' ) !== FALSE );
				if ( ! $has_own_sections ) {
					$the_content = '<section class="l-section"><div class="l-section-h i-cf">' . $the_content . $pagination . '</div></section>';
				} elseif ( ! empty( $pagination ) ) {
					$the_content .= '<section class="l-section"><div class="l-section-h i-cf">' . $pagination . '</div></section>';
				}

				echo $the_content;

				// Post comments
				$show_comments = us_get_option( 'portfolio_comments', FALSE );
				if ( $show_comments AND ( comments_open() OR get_comments_number() != '0' ) ) {
					?>
					<section class="l-section for_comments">
					<div class="l-section-h i-cf"><?php
						wp_enqueue_script( 'comment-reply' );
						comments_template();
						?></div>
					</section><?php
				}
			}
			?>

			<?php do_action( 'us_after_us_portfolio' ) ?>

		</main>

		<?php if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ): ?>
			<aside class="l-sidebar at_<?php echo $us_layout->sidebar_pos . ' ' . us_dynamic_sidebar_id( $default_portfolio_sidebar_id ); ?>" itemscope="itemscope" itemtype="https://schema.org/WPSideBar">
				<?php us_dynamic_sidebar( $default_portfolio_sidebar_id ); ?>
			</aside>
		<?php endif; ?>

	</div>
</div>
<?php
if ( us_get_option( 'portfolio_sided_nav', 0 ) ) {
	$prevnext = us_get_post_prevnext();
	if ( ! empty( $prevnext ) ) {
		?>
		<div class="l-navigation">
			<?php
			$keys = array( 'next', 'prev' );
			global $us_template_directory_uri;
			$placeholder_url = $us_template_directory_uri . '/framework/img/us-placeholder-square.png';
			foreach ( $keys as $key ) {
				if ( isset( $prevnext[ $key ] ) ) {
					$item = $prevnext[ $key ];
					$tnail_id = get_post_thumbnail_id( $item['id'] );
					if ( $tnail_id ) {
						$image = wp_get_attachment_image_src( $tnail_id, 'thumbnail' );
					}
					if ( ! $tnail_id OR empty( $image ) ) {
						$image = array( $placeholder_url, 500, 500 );
					}
					//print_r($item);
					?>
					<a class="l-navigation-item to_<?php echo $key; ?>" href="<?php echo $item['link']; ?>">
						<div class="l-navigation-item-arrow"></div>
						<div class="l-navigation-item-preview">
							<img <?php echo 'src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '"'; ?> alt="<?php echo esc_attr( $item['title'] ); ?>">
						</div>
						<div class="l-navigation-item-title">
							<span><?php echo $item['title']; ?></span>
						</div>
					</a>
					<?php
				}
			}
			?>
		</div>
		<?php
	}
}
?>

<?php get_footer() ?>
