<?php
/**
 * Edit Site Users Administration Screen
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can('manage_sites') )
	wp_die(__('You do not have sufficient permissions to edit this site.'));

$wp_list_table = get_list_table('WP_Users_List_Table');
$wp_list_table->check_permissions();
$wp_list_table->prepare_items();

$action = $wp_list_table->current_action();

$s = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$_SERVER['REQUEST_URI'] = remove_query_arg(array('enable', 'disable', 'enable-selected', 'disable-selected'), $_SERVER['REQUEST_URI']);

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id )
	wp_die( __('Invalid site ID.') );

$details = get_blog_details( $id );
if ( $details->site_id != $wpdb->siteid )
	wp_die( __( 'You do not have permission to access this page.' ) );

$is_main_site = is_main_site( $id );

// get blog prefix
$blog_prefix = $wpdb->get_blog_prefix( $id );

// @todo This is a hack. Eventually, add API to WP_Roles allowing retrieval of roles for a particular blog.
if ( ! empty($wp_roles->use_db) ) {
	$editblog_roles = get_blog_option( $id, "{$blog_prefix}user_roles" );
} else {
	// Roles are stored in memory, not the DB.
	$editblog_roles = $wp_roles->roles;
}

$action = $wp_list_table->current_action();

if ( $action ) {
	switch_to_blog( $id );
	
	switch ( $action ) {
		case 'adduser':
			if ( !empty( $_POST['newuser'] ) ) {
				$newuser = $_POST['newuser'];
				$userid = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->users . " WHERE user_login = %s", $newuser ) );
				if ( $userid ) {
					$user = $wpdb->get_var( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE user_id='$userid' AND meta_key='{$blog_prefix}capabilities'" );
					if ( $user == false )
						add_user_to_blog( $id, $userid, $_POST['new_role'] );
				}
			}
			break;
		
		case 'remove':
			if ( !current_user_can('remove_users')  )
				die(__('You can&#8217;t remove users.'));

			if ( isset( $_REQUEST['users'] ) ) {
				$userids = $_REQUEST['users'];

				foreach ( $userids as $user_id ) {
					$user_id = (int) $user_id;
					remove_user_from_blog( $user_id, $id );
				}
			} else {
				remove_user_from_blog( $_GET['user'] );
			}
			break;

		case 'promote':
			$editable_roles = get_editable_roles();
			if ( empty( $editable_roles[$_REQUEST['new_role']] ) )
				wp_die(__('You can&#8217;t give users that role.'));

			$userids = $_REQUEST['users'];
			$update = 'promote';
			foreach ( $userids as $user_id ) {
				$user_id = (int) $user_id;

				// If the user doesn't already belong to the blog, bail.
				if ( !is_user_member_of_blog( $user_id ) )
					wp_die(__('Cheatin&#8217; uh?'));

				$user = new WP_User( $user_id );
				$user->set_role( $_REQUEST['new_role'] );
			}
			break;
	}
	
	restore_current_blog();
	wp_redirect( wp_get_referer() ); // @todo add_query_arg for update message
}

add_screen_option( 'per_page', array( 'label' => _x( 'Users', 'users per page (screen options)' ) ) );

$title = sprintf( __('Edit Site: %s'), get_blogaddress_by_id($id));
$parent_file = 'sites.php';
$submenu_file = 'sites.php';

require('../admin-header.php');

?>

<div class="wrap">
<?php screen_icon('ms-admin'); ?>
<h2 id="edit-site"><?php echo $title ?></h2>
<h3 class="nav-tab-wrapper">
<?php
$tabs = array( 'site-info' => array( 'label' => __('Info'), 'url' => 'site-info.php'),  'site-options' => array( 'label' => __('Options'), 'url' => 'site-options.php'),
			  'site-users' => array( 'label' => __('Users'), 'url' => 'site-users.php'),  'site-themes' => array( 'label' => __('Themes'), 'url' => 'site-themes.php'));
foreach ( $tabs as $tab_id => $tab ) {
	$class = ( $tab['url'] == $pagenow ) ? ' nav-tab-active' : '';
	echo '<a href="' . $tab['url'] . '?id=' . $id .'" class="nav-tab' . $class . '">' .  esc_html( $tab['label'] ) . '</a>';
}
?>
</h3>
<form class="search-form" action="" method="get">
<p class="search-box">
	<label class="screen-reader-text" for="user-search-input"><?php _e( 'Search Users' ); ?>:</label>
	<input type="text" id="user-search-input" name="s" value="<?php echo esc_attr($usersearch); ?>" />
	<?php submit_button( __( 'Search Users' ), 'button', 'submit', false ); ?>
</p>
</form>

<?php $wp_list_table->views(); ?>

<form method="post" action="site-users.php?action=update-site">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />

<?php $wp_list_table->display(); ?>

</form>

<h3 id="add-new-user"><?php _e('Add Existing User') ?></h3>
<p class="description"><?php _e( 'Enter the username of an existing user.' ) ?></p>
	<form action="site-users.php?action=adduser" id="adduser" method="post">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e( 'Username' ); ?></th>
			<td><input type="text" name="newuser" id="newuser" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Role'); ?></th>
			<td><select name="new_role" id="new_role_0">
			<?php
			$default_role = $wpdb->get_var( "SELECT `option_value` FROM {$blog_prefix}options WHERE option_name = 'default_role'" );
			reset( $editblog_roles );
			foreach ( $editblog_roles as $role => $role_assoc ){
				$name = translate_user_role( $role_assoc['name'] );
				$selected = ( $role == $default_role ) ? 'selected="selected"' : '';
				echo '<option ' . $selected . ' value="' . esc_attr( $role ) . '">' . esc_html( $name ) . '</option>';
			}
			?>
			</select></td>
		</tr>
	</table>
	<?php submit_button( __('Add User'), 'primary', 'add-user' ); ?>
	</form>
</div>
<?php
require('../admin-footer.php');