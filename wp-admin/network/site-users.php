<?php

/**
 * Edit Site Users Administration Screen
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can('manage_sites') )
	wp_die(__('You do not have sufficient permissions to edit this site.'));

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id )
	wp_die( __('Invalid site ID.') );

$details = get_blog_details( $id );
if ( $details->site_id != $wpdb->siteid )
	wp_die( __( 'You do not have permission to access this page.' ) );

$is_main_site = is_main_site( $id );

if ( isset($_REQUEST['action']) && 'update-site' == $_REQUEST['action'] ) {
	check_admin_referer( 'edit-site' );

	switch_to_blog( $id );

	// get blog prefix
	$blog_prefix = $wpdb->get_blog_prefix( $id );

	// user roles
	if ( isset( $_POST['role'] ) && is_array( $_POST['role'] ) == true ) {
		$newroles = $_POST['role'];

		reset( $newroles );
		foreach ( (array) $newroles as $userid => $role ) {
			$user = new WP_User( $userid );
			if ( empty( $user->ID ) )
				continue;
			$user->for_blog( $id );
			$user->set_role( $role );
		}
	}

	// remove user
	if ( isset( $_POST['blogusers'] ) && is_array( $_POST['blogusers'] ) ) {
		reset( $_POST['blogusers'] );
		foreach ( (array) $_POST['blogusers'] as $key => $val )
			remove_user_from_blog( $key, $id );
	}

	// change password
	if ( isset( $_POST['user_password'] ) && is_array( $_POST['user_password'] ) ) {
		reset( $_POST['user_password'] );
		$newroles = $_POST['role'];
		foreach ( (array) $_POST['user_password'] as $userid => $pass ) {
			unset( $_POST['role'] );
			$_POST['role'] = $newroles[ $userid ];
			if ( $pass != '' ) {
				$cap = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key = '{$blog_prefix}capabilities' AND meta_value = 'a:0:{}'", $userid ) );
				$userdata = get_userdata($userid);
				$_POST['pass1'] = $_POST['pass2'] = $pass;
				$_POST['email'] = $userdata->user_email;
				$_POST['rich_editing'] = $userdata->rich_editing;
				edit_user( $userid );
				if ( $cap == null )
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key = '{$blog_prefix}capabilities' AND meta_value = 'a:0:{}'", $userid ) );
			}
		}
		unset( $_POST['role'] );
		$_POST['role'] = $newroles;
	}

	// add user
	if ( !empty( $_POST['newuser'] ) ) {
		$newuser = $_POST['newuser'];
		$userid = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->users . " WHERE user_login = %s", $newuser ) );
		if ( $userid ) {
			$user = $wpdb->get_var( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE user_id='$userid' AND meta_key='{$blog_prefix}capabilities'" );
			if ( $user == false )
				add_user_to_blog( $id, $userid, $_POST['new_role'] );
		}
	}

	restore_current_blog();
	wp_redirect( add_query_arg( array( 'update' => 'updated', 'id' => $id ), 'site-users.php') );
}

if ( isset($_GET['update']) ) {
	$messages = array();
	if ( 'updated' == $_GET['update'] )
		$messages[] = __('Site users updated.');
}

$title = sprintf( __('Edit Site: %s'), get_blogaddress_by_id($id));
$parent_file = 'sites.php';
$submenu_file = 'sites.php';

require('../admin-header.php');

?>

<div class="wrap">
<?php screen_icon('index'); ?>
<h2 id="edit-site"><?php echo $title ?></h2>
<h3>
<?php
$tabs = array( 'site-info' => array( 'label' => __('Info'), 'url' => 'site-info.php'),  'site-options' => array( 'label' => __('Options'), 'url' => 'site-options.php'),
			  'site-users' => array( 'label' => __('Users'), 'url' => 'site-users.php'),  'site-themes' => array( 'label' => __('Themes'), 'url' => 'site-themes.php'));
foreach ( $tabs as $tab_id => $tab ) {
	$class = ( $tab['url'] == $pagenow ) ? ' nav-tab-active' : '';
	echo '<a href="' . $tab['url'] . '?id=' . $id .'" class="nav-tab' . $class . '">' .  esc_html( $tab['label'] ) . '</a>';
}
?>
</h3>
<?php
if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg )
		echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
} ?>
<form method="post" action="site-users.php?action=update-site">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
<?php
$blogusers = get_users( array( 'blog_id' => $id, 'number' => 20 ) );
if ( is_array( $blogusers ) ) {
	echo '<table class="form-table">';
	echo "<tr><th>" . __( 'User' ) . "</th><th>" . __( 'Role' ) . "</th><th>" . __( 'Password' ) . "</th><th>" . __( 'Remove' ) . "</th></tr>";
	$user_count = 0;
	$blog_prefix = $wpdb->get_blog_prefix( $id );
	$editblog_roles = get_blog_option( $id, "{$blog_prefix}user_roles" );

	foreach ( $blogusers as $user_id => $user_object ) {
		$user_count++;
		$existing_role = reset( $user_object->roles );

		echo '<tr><td><a href="user-edit.php?user_id=' . $user_id . '">' . $user_object->user_login . '</a></td>';
		if ( $user_id != $current_user->data->ID ) {
			?>
			<td>
				<select name="role[<?php echo $user_id ?>]" id="new_role_1"><?php
					foreach ( $editblog_roles as $role => $role_assoc ){
						$name = translate_user_role( $role_assoc['name'] );
						echo '<option ' . selected( $role, $existing_role, false ) . ' value="' . esc_attr( $role ) . '">' . esc_html( $name ) . '</option>';
					}
					?>
				</select>
			</td>
			<td>
				<input type="text" name="user_password[<?php echo esc_attr( $user_id ) ?>]" />
			</td>
			<?php
			echo '<td><input title="' . __( 'Click to remove user' ) . '" type="checkbox" name="blogusers[' . esc_attr( $user_id ) . ']" /></td>';
		} else {
			echo "<td><strong>" . __ ( 'N/A' ) . "</strong></td><td><strong>" . __ ( 'N/A' ) . "</strong></td><td><strong>" . __( 'N/A' ) . "</strong></td>";
		}
		echo '</tr>';
	}
	echo "</table>";
	submit_button();
	if ( 20 == $user_count )
		echo '<p>' . sprintf( __('First 20 users shown. <a href="%s">Manage all users</a>.'), get_admin_url($id, 'users.php') ) . '</p>';
} else {
	_e('This site has no users.');
}
?>
</form>

</div>
<?php
require('../admin-footer.php');