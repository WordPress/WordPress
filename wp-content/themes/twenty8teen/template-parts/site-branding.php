<?php
/**
 * This is the template part that displays the site branding.
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package Twenty8teen
 */

?>
<div <?php twenty8teen_widget_get_classes( 'site-branding', true ); ?>>
	<?php if ( is_front_page() ) : ?>
		<h1 class="site-title">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
		</h1>
	<?php else : ?>
		<p class="site-title">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
		</p>
	<?php
	endif;

	$description = get_bloginfo( 'description', 'display' );
	if ( $description || is_customize_preview() ) : ?>
		<p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
	<?php
	endif; ?>
</div><!-- .site-branding -->
