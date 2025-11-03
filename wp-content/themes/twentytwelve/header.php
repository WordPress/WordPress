<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="https://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="hfeed site">
	<a class="screen-reader-text skip-link" href="#content"><?php _e( 'Skip to content', 'twentytwelve' ); ?></a>
	<header id="masthead" class="site-header">
		<hgroup>
			<?php
			$is_front         = ! is_paged() && ( is_front_page() || ( is_home() && ( (int) get_option( 'page_for_posts' ) !== get_queried_object_id() ) ) );
			$site_name        = get_bloginfo( 'name', 'display' );
			$site_description = get_bloginfo( 'description', 'display' );

			if ( $site_name ) :
				?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" <?php echo $is_front ? 'aria-current="page"' : ''; ?>><?php echo $site_name; ?></a></h1>
				<?php
			endif;

			if ( $site_description ) :
				?>
				<h2 class="site-description"><?php echo $site_description; ?></h2>
			<?php endif; ?>
		</hgroup>

		<nav id="site-navigation" class="main-navigation">
			<button class="menu-toggle"><?php _e( 'Menu', 'twentytwelve' ); ?></button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_class'     => 'nav-menu',
				)
			);
			?>
		</nav><!-- #site-navigation -->

		<?php if ( get_header_image() ) : ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" <?php echo $is_front ? 'aria-current="page"' : ''; ?> rel="home"><?php twentytwelve_header_image(); ?></a>
		<?php endif; ?>
	</header><!-- #masthead -->

	<div id="main" class="wrapper">
