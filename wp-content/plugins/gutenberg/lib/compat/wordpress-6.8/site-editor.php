<?php
/**
 * Updates to the site editor in 6.8.
 *
 * Adds a mandatory dashboard link and redirects old urls.
 *
 * @package gutenberg
 */

add_filter(
	'block_editor_settings_all',
	function ( $settings ) {
		$settings['__experimentalDashboardLink'] = admin_url( '/' );
		return $settings;
	}
);

/**
 * Maps old site editor urls to the new updated ones.
 *
 * @since 6.8.0
 * @access private
 *
 * @global string $pagenow The filename of the current screen.
 *
 * @return string|false The new URL to redirect to, or false if no redirection is needed.
 */
function gutenberg_get_site_editor_redirection() {
	global $pagenow;
	if ( 'site-editor.php' !== $pagenow || isset( $_REQUEST['p'] ) || empty( $_SERVER['QUERY_STRING'] ) ) {
		return false;
	}

	// The following redirects are for the new permalinks in the site editor.
	if ( isset( $_REQUEST['postType'] ) && 'wp_navigation' === $_REQUEST['postType'] && ! empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/wp_navigation/' . $_REQUEST['postId'] ), remove_query_arg( array( 'postType', 'postId' ) ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_navigation' === $_REQUEST['postType'] && empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/navigation' ), remove_query_arg( 'postType' ) );
	}

	if ( isset( $_REQUEST['path'] ) && '/wp_global_styles' === $_REQUEST['path'] ) {
		return add_query_arg( array( 'p' => '/styles' ), remove_query_arg( 'path' ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'page' === $_REQUEST['postType'] && ( empty( $_REQUEST['canvas'] ) || empty( $_REQUEST['postId'] ) ) ) {
		return add_query_arg( array( 'p' => '/page' ), remove_query_arg( 'postType' ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'page' === $_REQUEST['postType'] && ! empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/page/' . $_REQUEST['postId'] ), remove_query_arg( array( 'postType', 'postId' ) ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_template' === $_REQUEST['postType'] && ( empty( $_REQUEST['canvas'] ) || empty( $_REQUEST['postId'] ) ) ) {
		return add_query_arg( array( 'p' => '/template' ), remove_query_arg( 'postType' ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_template' === $_REQUEST['postType'] && ! empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/wp_template/' . $_REQUEST['postId'] ), remove_query_arg( array( 'postType', 'postId' ) ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_block' === $_REQUEST['postType'] && ( empty( $_REQUEST['canvas'] ) || empty( $_REQUEST['postId'] ) ) ) {
		return add_query_arg( array( 'p' => '/pattern' ), remove_query_arg( 'postType' ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_block' === $_REQUEST['postType'] && ! empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/wp_block/' . $_REQUEST['postId'] ), remove_query_arg( array( 'postType', 'postId' ) ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_template_part' === $_REQUEST['postType'] && ( empty( $_REQUEST['canvas'] ) || empty( $_REQUEST['postId'] ) ) ) {
		return add_query_arg( array( 'p' => '/pattern' ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_template_part' === $_REQUEST['postType'] && ! empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/wp_template_part/' . $_REQUEST['postId'] ), remove_query_arg( array( 'postType', 'postId' ) ) );
	}

	// The following redirects are for backward compatibility with the old site editor URLs.
	if ( isset( $_REQUEST['path'] ) && '/wp_template_part/all' === $_REQUEST['path'] ) {
		return add_query_arg(
			array(
				'p'        => '/pattern',
				'postType' => 'wp_template_part',
			),
			remove_query_arg( 'path' )
		);
	}

	if ( isset( $_REQUEST['path'] ) && '/page' === $_REQUEST['path'] ) {
		return add_query_arg( array( 'p' => '/page' ), remove_query_arg( 'path' ) );
	}

	if ( isset( $_REQUEST['path'] ) && '/wp_template' === $_REQUEST['path'] ) {
		return add_query_arg( array( 'p' => '/template' ), remove_query_arg( 'path' ) );
	}

	if ( isset( $_REQUEST['path'] ) && '/patterns' === $_REQUEST['path'] ) {
		return add_query_arg( array( 'p' => '/pattern' ), remove_query_arg( 'path' ) );
	}

	if ( isset( $_REQUEST['path'] ) && '/navigation' === $_REQUEST['path'] ) {
		return add_query_arg( array( 'p' => '/navigation' ), remove_query_arg( 'path' ) );
	}

	return add_query_arg( array( 'p' => '/' ) );
}

/**
 * Redirect old site editor urls to the new updated ones.
 */
function gutenberg_redirect_site_editor_deprecated_urls() {
	$redirection = gutenberg_get_site_editor_redirection();
	if ( false !== $redirection ) {
		wp_safe_redirect( $redirection );
		exit;
	}
}
add_action( 'admin_init', 'gutenberg_redirect_site_editor_deprecated_urls' );

/**
 * Filter the `wp_die_handler` to allow access to the Site Editor's new pages page
 * for Classic themes.
 *
 * site-editor.php's access is forbidden for hybrid/classic themes and only allowed with some very special query args (some very special pages like template parts...).
 * The only way to disable this protection since we're changing the urls in Gutenberg is to override the wp_die_handler.
 *
 * @param callable $default_handler The default handler.
 * @return callable The default handler or a custom handler.
 */
function gutenberg_styles_wp_die_handler( $default_handler ) {
	if ( ! wp_is_block_theme() && str_contains( $_SERVER['REQUEST_URI'], 'site-editor.php' ) && current_user_can( 'edit_theme_options' ) ) {
		return '__return_false';
	}
	return $default_handler;
}
add_filter( 'wp_die_handler', 'gutenberg_styles_wp_die_handler' );

/**
 * Add a Styles submenu under the Appearance menu
 * for Classic themes.
 *
 * @global array $submenu
 */
function gutenberg_add_styles_submenu_item() {
	if ( ! wp_is_block_theme() && ( current_theme_supports( 'editor-styles' ) || wp_theme_has_theme_json() ) ) {
		global $submenu;

		$styles_menu_item = array(
			__( 'Design', 'gutenberg' ),
			'edit_theme_options',
			'site-editor.php',
		);
		// If $submenu exists, insert the Styles submenu item at position 2.
		if ( $submenu && isset( $submenu['themes.php'] ) ) {
			// This might not work as expected if the submenu has already been modified.
			array_splice( $submenu['themes.php'], 1, 1, array( $styles_menu_item ) );
		}
	}
}
add_action( 'admin_menu', 'gutenberg_add_styles_submenu_item' );
