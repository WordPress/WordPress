<?php
/**
 * Bootstraps the new posts dashboard page.
 *
 * @package gutenberg
 */

add_action( 'admin_menu', 'gutenberg_replace_posts_dashboard' );

/**
 * Registers the posts dashboard menu item using the gutenberg-boot-wp-admin routing infrastructure.
 */
function gutenberg_replace_posts_dashboard() {
	$gutenberg_experiments = get_option( 'gutenberg-experiments' );
	if ( ! $gutenberg_experiments || ! array_key_exists( 'gutenberg-new-posts-dashboard', $gutenberg_experiments ) || ! $gutenberg_experiments['gutenberg-new-posts-dashboard'] ) {
		return;
	}

	$ptype_obj = get_post_type_object( 'post' );
	$url       = admin_url( 'admin.php?page=gutenberg-boot-wp-admin&p=' . urlencode( '/types/post/list/all' ) );

	add_submenu_page(
		'gutenberg',
		$ptype_obj->labels->name,
		$ptype_obj->labels->name,
		'edit_posts',
		$url,
		''
	);
}
