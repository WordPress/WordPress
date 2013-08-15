<?php
/**
 * The Sidebar containing the main widget area.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>
<div id="secondary">
	<div id="secondary-top">
		<?php
			$description = get_bloginfo( 'description' );
			if ( ! empty ( $description ) ) :
		?>
		<h2 class="site-description"><?php echo esc_html( $description ); ?></h2>
		<?php endif; ?>

		<?php if ( has_nav_menu( 'secondary' ) ) : ?>
		<nav role="navigation" class="navigation secondary-navigation">
			<?php wp_nav_menu( array( 'theme_location' => 'secondary' ) ); ?>
		</nav>
		<?php endif; ?>
	</div><!-- #secondary-top -->

	<div id="secondary-bottom" class="widget-area" role="complementary">
		<?php
			do_action( 'before_sidebar' );
			dynamic_sidebar( 'sidebar-1' );
		?>
	</div><!-- #secondary-bottom -->
</div><!-- #secondary -->
