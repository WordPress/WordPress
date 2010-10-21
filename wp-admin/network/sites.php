<?php
/**
 * Multisite sites administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

$wp_list_table = get_list_table('sites');
$wp_list_table->check_permissions();

$title = __( 'Sites' );
$parent_file = 'sites.php';

add_screen_option( 'per_page', array('label' => _x( 'Sites', 'sites per page (screen options)' )) );

add_contextual_help($current_screen,
	'<p>' . __('Add New takes you to the Add New Site screen. You can search for a site by Name, ID number, or IP address. Screen Options allows you to choose how many sites to display on one page.') . '</p>' .
	'<p>' . __('This is the main table of all sites on this network. Switch between list and excerpt views by using the icons above the right side of the table.') . '</p>' .
	'<p>' . __('Hovering over each site reveals seven options (three for the primary site):') . '</p>' .
	'<ul><li>' . __('An Edit link to a separate Edit Site screen.') . '</li>' .
	'<li>' . __('Dashboard to the Dashboard for that site.') . '</li>' .
	'<li>' . __('Deactivate, Archive, and Spam which lead to confirmation screens. These actions can be reversed later.') . '</li>' .
	'<li>' . __('Delete which is a permanent action after the confirmations screen.') . '</li>' .
	'<li>' . __('Visit to go to the frontend site live.') . '</li></ul>' .
	'<p>' . __('The site ID is used internally, and is not shown on the front end of the site or to users/viewers.') . '</p>' .
	'<p>' . __('Clicking on bold settings can re-sort this table. The upper right icons switch between list and excerpt views.') . '</p>' .
	'<p>' . __('If the admin email for the new site does not exist in the database, a new user will also be created.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Super_Admin_Sites_SubPanel" target="_blank">Documentation on Sites</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

$msg = '';
if ( isset( $_REQUEST['updated'] ) && $_REQUEST['updated'] == 'true' && ! empty( $_REQUEST['action'] ) ) {
	switch ( $_REQUEST['action'] ) {
		case 'all_notspam':
			$msg = __( 'Sites removed from spam.' );
		break;
		case 'all_spam':
			$msg = __( 'Sites marked as spam.' );
		break;
		case 'all_delete':
			$msg = __( 'Sites deleted.' );
		break;
		case 'delete':
			$msg = __( 'Site deleted.' );
		break;
		case 'archive':
			$msg = __( 'Site archived.' );
		break;
		case 'unarchive':
			$msg = __( 'Site unarchived.' );
		break;
		case 'activate':
			$msg = __( 'Site activated.' );
		break;
		case 'deactivate':
			$msg = __( 'Site deactivated.' );
		break;
		case 'unspam':
			$msg = __( 'Site removed from spam.' );
		break;
		case 'spam':
			$msg = __( 'Site marked as spam.' );
		break;
		default:
			$msg = __( 'Settings saved.' );
		break;
	}
	if ( $msg )
		$msg = '<div class="updated" id="message"><p>' . $msg . '</p></div>';
}

$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'list';

switch ( $action ) {
	// Edit site
	case 'editblog':
		// No longer used.
	break;

	// List sites
	case 'list':
	default:
		$wp_list_table->prepare_items();

		require_once( '../admin-header.php' );
		?>

		<div class="wrap">
		<?php screen_icon('ms-admin'); ?>
		<h2><?php _e('Sites') ?>
		<?php echo $msg; ?>
		<a href="<?php echo network_admin_url('site-new.php'); ?>" class="button add-new-h2"><?php echo esc_html_x( 'Add New', 'sites' ); ?></a>
		<?php if ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] ) {
			printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $s ) );
		} ?>
		</h2>

		<form action="" method="get" id="ms-search">
		<p class="search-box">
		<input type="hidden" name="action" value="blogs" />
		<input type="text" name="s" value="<?php echo esc_attr( $s ); ?>" />
		<input type="submit" class="button" value="<?php esc_attr_e( 'Search Site by' ) ?>" />
		<select name="searchaction">
			<option value="name" selected="selected"><?php _e( 'Name' ); ?></option>
			<option value="id"><?php _e( 'ID' ); ?></option>
			<option value="ip"><?php _e( 'IP address' ); ?></option>
		</select>
		</p>
		</form>

		<form id="form-site-list" action="edit.php?action=allblogs" method="post">
			<?php $wp_list_table->display(); ?>
		</form>
		</div>
		<?php
	break;
} // end switch( $action )

require_once( '../admin-footer.php' ); ?>
