<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

$tool_page = '';

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
if ( isset( $_GET['tool'] ) && is_string( $_GET['tool'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
	$tool_page = sanitize_text_field( wp_unslash( $_GET['tool'] ) );
}

$yform = Yoast_Form::get_instance();
$yform->admin_header( false );

if ( $tool_page === '' ) {

	$tools = [];

	$tools['import-export'] = [
		'title' => __( 'Import and Export', 'wordpress-seo' ),
		'desc'  => __( 'Import settings from other SEO plugins and export your settings for re-use on (another) site.', 'wordpress-seo' ),
	];

	if ( WPSEO_Utils::allow_system_file_edit() === true && ! is_multisite() ) {
		$tools['file-editor'] = [
			'title' => __( 'File editor', 'wordpress-seo' ),
			'desc'  => __( 'This tool allows you to quickly change important files for your SEO, like your robots.txt and, if you have one, your .htaccess file.', 'wordpress-seo' ),
		];
	}

	$tools['bulk-editor'] = [
		'title' => __( 'Bulk editor', 'wordpress-seo' ),
		'desc'  => __( 'This tool allows you to quickly change titles and descriptions of your posts and pages without having to go into the editor for each page.', 'wordpress-seo' ),
	];

	echo '<p>';
	printf(
		/* translators: %1$s expands to Yoast SEO */
		esc_html__( '%1$s comes with some very powerful built-in tools:', 'wordpress-seo' ),
		'Yoast SEO'
	);
	echo '</p>';

	echo '<ul class="ul-disc">';

	$admin_url = admin_url( 'admin.php?page=wpseo_tools' );

	foreach ( $tools as $slug => $tool ) {
		$href = ( ! empty( $tool['href'] ) ) ? $admin_url . $tool['href'] : add_query_arg( [ 'tool' => $slug ], $admin_url );
		$attr = ( ! empty( $tool['attr'] ) ) ? $tool['attr'] : '';

		echo '<li>';
		echo '<strong><a href="', esc_url( $href ), '" ', esc_attr( $attr ), '>', esc_html( $tool['title'] ), '</a></strong><br/>';
		echo esc_html( $tool['desc'] );
		echo '</li>';
	}

	/**
	 * WARNING: This hook is intended for internal use only.
	 * Don't use it in your code as it will be removed shortly.
	 */
	do_action( 'wpseo_tools_overview_list_items_internal' );

	echo '</ul>';
}
else {
	echo '<a href="', esc_url( admin_url( 'admin.php?page=wpseo_tools' ) ), '">', esc_html__( '&laquo; Back to Tools page', 'wordpress-seo' ), '</a>';

	$tool_pages = [ 'bulk-editor', 'import-export' ];

	if ( WPSEO_Utils::allow_system_file_edit() === true && ! is_multisite() ) {
		$tool_pages[] = 'file-editor';
	}

	if ( in_array( $tool_page, $tool_pages, true ) ) {
		require_once WPSEO_PATH . 'admin/views/tool-' . $tool_page . '.php';
	}
}

$yform->admin_footer( false );
