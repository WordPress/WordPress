<?php
/**
 * Gutenberg Boot Page - Integration file for admin menu registration.
 *
 * @package gutenberg
 */

/**
 * Register boot admin page in WordPress admin menu.
 */
function gutenberg_register_boot_admin_page() {
	add_submenu_page(
		'nothing',
		__( 'Boot Demo', 'gutenberg' ),
		__( 'Boot Demo', 'gutenberg' ),
		'manage_options',
		'gutenberg-boot',
		'gutenberg_boot_render_page'
	);
}
add_action( 'admin_menu', 'gutenberg_register_boot_admin_page' );

/**
 * Register default menu items for the boot page.
 */
function gutenberg_boot_register_default_menu_items() {
	register_gutenberg_boot_menu_item( 'home', __( 'Home', 'gutenberg' ), '/', '' );
	register_gutenberg_boot_menu_item( 'posts', __( 'Posts', 'gutenberg' ), '/types/post', '' );
}
add_action( 'gutenberg-boot_init', 'gutenberg_boot_register_default_menu_items', 5 );
