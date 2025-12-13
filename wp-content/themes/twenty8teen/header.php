<?php
/**
 * This is the template that displays all of the <head> section.
 *
 * @package Twenty8teen
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); twenty8teen_attributes( 'html' ); ?>>
<head <?php twenty8teen_attributes( 'head' ); ?>>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta <?php twenty8teen_attributes( 'meta', array( 'name' => 'viewport',
		'content' => 'width=device-width, initial-scale=1' ) ); ?>>

	<?php wp_head(); ?>
</head>

<body <?php twenty8teen_attributes( 'body', array( 'class' => get_body_class() ) ); ?>>
	<?php do_action( 'wp_body_open' ); ?>

	<?php
	$default = twenty8teen_default_booleans();
	if ( get_theme_mod( 'show_header', $default['show_header'] ) ) : ?>
	<a class="skip-link screen-reader-text"
		href="#content"><?php esc_html_e( 'Skip to content', 'twenty8teen' ); ?></a>
	<header id="masthead" <?php twenty8teen_area_classes( 'header', 'site-header' ) ?>>
		<?php get_sidebar( 'header' ); ?>
	</header><!-- #masthead -->
	<?php endif; ?>
