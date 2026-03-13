<?php
/**
 * Template part for displaying main navigation
 *
 * @package Twenty8teen
 */

$an_id = 'main-nav-sub0' . microtime(true);
?>
<nav id="main-nav" aria-label="<?php esc_attr_e( 'Main menu', 'twenty8teen' ); ?>" <?php twenty8teen_widget_get_classes( 'site-navigation', true ); ?>>
	<input type="checkbox" id="<?php echo $an_id; ?>" tabindex="-1">
	<label for="<?php echo $an_id; ?>" class="menu-toggle">
		<?php esc_html_e( 'Menu', 'twenty8teen' ); ?></label>
	<?php
		// make the fallback args match.
		add_filter( 'wp_page_menu_args', 'twenty8teen_page_menu_args' );
		add_filter( 'walker_nav_menu_start_el', 'twenty8teen_nav_menu_start_el', 9, 4);
		wp_nav_menu( array(
			'theme_location' => 'mainmenu',
			'container_id'   => 'main-menu',
		) );
		remove_filter( 'wp_page_menu_args', 'twenty8teen_page_menu_args' );
		remove_filter( 'walker_nav_menu_start_el', 'twenty8teen_nav_menu_start_el', 9 );
	?>
</nav><!-- #main-nav -->
