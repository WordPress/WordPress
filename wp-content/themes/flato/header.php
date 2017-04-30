<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Theme Meme
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php wp_title( '-', true, 'right' ); ?></title>

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php do_action( 'before' ); ?>
	<div class="site-top">
		<div class="clearfix container">
			<nav class="site-menu" role="navigation">
				<div class="menu-toggle"><i class="fa fa-bars"></i></div>
				<div class="menu-text"></div>
				<?php wp_nav_menu( array( 'container_class' => 'clearfix menu-bar', 'theme_location' => 'primary' ) ); ?>
			<!-- .site-menu --></nav>

			<div class="site-search">
				<div class="search-toggle"><i class="fa fa-search"></i></div>
				<div class="search-expand">
					<div class="search-expand-inner">
						<?php get_search_form(); ?>
					</div>
				</div>
			<!-- .site-search --></div>
    	</div>
	<!-- .site-top --></div>

	<header class="site-header" role="banner">
		<div class="clearfix container">
			<div class="site-branding">
				<?php echo themememe_site_title(); ?>
				<?php if ( !ot_get_option('site-description') ): ?><div class="site-description"><?php bloginfo( 'description' ); ?></div><?php endif; ?>
			</div>
		</div>
	<!-- .site-header --></header>

	<div class="site-main">
		<div class="clearfix container">