<?php
/**
 * Link Management Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';
if ( ! current_user_can( 'manage_links' ) ) {
	wp_die( __( 'Sorry, you are not allowed to edit the links for this site.' ) );
}

$wp_list_table = _get_list_table( 'WP_Links_List_Table' );

// Handle bulk deletes.
$doaction = $wp_list_table->current_action();

if ( $doaction && isset( $_REQUEST['linkcheck'] ) ) {
	check_admin_referer( 'bulk-bookmarks' );

	$redirect_to = admin_url( 'link-manager.php' );
	$bulklinks   = (array) $_REQUEST['linkcheck'];

	if ( 'delete' === $doaction ) {
		foreach ( $bulklinks as $link_id ) {
			$link_id = (int) $link_id;

			wp_delete_link( $link_id );
		}

		$redirect_to = add_query_arg( 'deleted', count( $bulklinks ), $redirect_to );
	} else {
		$screen = get_current_screen()->id;

		/** This action is documented in wp-admin/edit.php */
		$redirect_to = apply_filters( "handle_bulk_actions-{$screen}", $redirect_to, $doaction, $bulklinks ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	}
	wp_redirect( $redirect_to );
	exit;
} elseif ( ! empty( $_GET['_wp_http_referer'] ) ) {
	wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
	exit;
}

$wp_list_table->prepare_items();

// Used in the HTML title tag.
$title       = __( 'Links' );
$this_file   = 'link-manager.php';
$parent_file = $this_file;

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' =>
			'<p>' . sprintf(
				/* translators: %s: URL to Widgets screen. */
				__( 'You can add links here to be displayed on your site, usually using <a href="%s">Widgets</a>. By default, links to several sites in the WordPress community are included as examples.' ),
				'widgets.php'
			) . '</p>' .
			'<p>' . __( 'Links may be separated into Link Categories; these are different than the categories used on your posts.' ) . '</p>' .
			'<p>' . __( 'You can customize the display of this screen using the Screen Options tab and/or the dropdown filters above the links table.' ) . '</p>',
	)
);
get_current_screen()->add_help_tab(
	array(
		'id'      => 'deleting-links',
		'title'   => __( 'Deleting Links' ),
		'content' =>
			'<p>' . __( 'If you delete a link, it will be removed permanently, as Links do not have a Trash function yet.' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.wordpress.org/Links_Screen">Documentation on Managing Links</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/forums/">Support forums</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_list' => __( 'Links list' ),
	)
);

require_once ABSPATH . 'wp-admin/admin-header.php';

if ( ! current_user_can( 'manage_links' ) ) {
	wp_die( __( 'Sorry, you are not allowed to edit the links for this site.' ) );
}

?>

<div class="wrap nosubsub">
<h1 class="wp-heading-inline">
<?php
echo esc_html( $title );
?>
</h1>

<a href="link-add.php" class="page-title-action"><?php echo esc_html_x( 'Add New', 'link' ); ?></a>

<?php
if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
	echo '<span class="subtitle">';
	printf(
		/* translators: %s: Search query. */
		__( 'Search results for: %s' ),
		'<strong>' . esc_html( wp_unslash( $_REQUEST['s'] ) ) . '</strong>'
	);
	echo '</span>';
}
?>

<hr class="wp-header-end">

<?php
if ( isset( $_REQUEST['deleted'] ) ) {
	echo '<div id="message" class="updated notice is-dismissible"><p>';
	$deleted = (int) $_REQUEST['deleted'];
	/* translators: %s: Number of links. */
	printf( _n( '%s link deleted.', '%s links deleted.', $deleted ), $deleted );
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'deleted' ), $_SERVER['REQUEST_URI'] );
}
?>

<form id="posts-filter" method="get">

<?php $wp_list_table->search_box( __( 'Search Links' ), 'link' ); ?>

<?php $wp_list_table->display(); ?>

<div id="ajax-response"></div>
</form>

</div>

<?php
require_once ABSPATH . 'wp-admin/admin-footer.php';
