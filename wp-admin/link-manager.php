<?php
/**
 * Link Management Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once ('admin.php');
if ( ! current_user_can( 'manage_links' ) )
	wp_die( __( 'You do not have sufficient permissions to edit the links for this site.' ) );

$wp_list_table = _get_list_table('WP_Links_List_Table');

// Handle bulk deletes
$doaction = $wp_list_table->current_action();

if ( $doaction && isset( $_REQUEST['linkcheck'] ) ) {
	check_admin_referer( 'bulk-bookmarks' );

	if ( 'delete' == $doaction ) {
		$bulklinks = (array) $_REQUEST['linkcheck'];
		foreach ( $bulklinks as $link_id ) {
			$link_id = (int) $link_id;

			wp_delete_link( $link_id );
		}

		wp_redirect( add_query_arg('deleted', count( $bulklinks ), admin_url( 'link-manager.php' ) ) );
		exit;
	}
} elseif ( ! empty( $_GET['_wp_http_referer'] ) ) {
	 wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) );
	 exit;
}

$wp_list_table->prepare_items();

$title = __('Links');
$this_file = $parent_file = 'link-manager.php';

add_contextual_help( $current_screen,
	'<p>' . sprintf(__('You can add links here to be displayed on your site, usually using <a href="%s">Widgets</a>. By default, links to several sites in the WordPress community are included as examples.'), 'widgets.php') . '</p>' .
    '<p>' . __('Links may be separated into categories; these are different than the categories used on your posts.') . '</p>' .
    '<p>' . __('You can customize the display of this screen using the Screen Options tab and/or the dropdown filters above the links table.') . '</p>' .
    '<p>' . __('If you delete a link, it will be removed permanently, as Links do not have a Trash function yet.') . '</p>'
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Links_Screen" target="_blank">Documentation on Managing Links</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

include_once ('./admin-header.php');

if ( ! current_user_can('manage_links') )
	wp_die(__("You do not have sufficient permissions to edit the links for this site."));

?>

<div class="wrap nosubsub">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?> <a href="link-add.php" class="add-new-h2"><?php echo esc_html_x('Add New', 'link'); ?></a> <?php
if ( !empty($_REQUEST['s']) )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( stripslashes($_REQUEST['s']) ) ); ?>
</h2>

<?php
if ( isset($_REQUEST['deleted']) ) {
	echo '<div id="message" class="updated"><p>';
	$deleted = (int) $_REQUEST['deleted'];
	printf(_n('%s link deleted.', '%s links deleted', $deleted), $deleted);
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('deleted'), $_SERVER['REQUEST_URI']);
}
?>

<form id="posts-filter" action="" method="get">

<?php $wp_list_table->search_box( __( 'Search Links' ), 'link' ); ?>

<?php $wp_list_table->display(); ?>

<div id="ajax-response"></div>
</form>

</div>

<?php
include('./admin-footer.php');
