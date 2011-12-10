<?php
/**
 * Multisite users administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can( 'manage_network_users' ) )
	wp_die( __( 'You do not have permission to access this page.' ) );

function confirm_delete_users( $users ) {
	$current_user = wp_get_current_user();
	if ( !is_array( $users ) )
		return false;

	screen_icon();
	?>
	<h2><?php esc_html_e( 'Users' ); ?></h2>
	<p><?php _e( 'Transfer or delete posts and links before deleting users.' ); ?></p>
	<form action="users.php?action=dodelete" method="post">
	<input type="hidden" name="dodelete" />
	<?php
	wp_nonce_field( 'ms-users-delete' );
	$site_admins = get_super_admins();
	$admin_out = "<option value='$current_user->ID'>$current_user->user_login</option>";

	foreach ( ( $allusers = (array) $_POST['allusers'] ) as $key => $val ) {
		if ( $val != '' && $val != '0' ) {
			$delete_user = new WP_User( $val );

			if ( ! current_user_can( 'delete_user', $delete_user->ID ) )
				wp_die( sprintf( __( 'Warning! User %s cannot be deleted.' ), $delete_user->user_login ) );

			if ( in_array( $delete_user->user_login, $site_admins ) )
				wp_die( sprintf( __( 'Warning! User cannot be deleted. The user %s is a network admnistrator.' ), $delete_user->user_login ) );

			echo "<input type='hidden' name='user[]' value='{$val}'/>\n";
			$blogs = get_blogs_of_user( $val, true );

			if ( !empty( $blogs ) ) {
				?>
				<br /><fieldset><p><legend><?php printf( __( "What should be done with posts and links owned by <em>%s</em>?" ), $delete_user->user_login ); ?></legend></p>
				<?php
				foreach ( (array) $blogs as $key => $details ) {
					$blog_users = get_users( array( 'blog_id' => $details->userblog_id ) );
					if ( is_array( $blog_users ) && !empty( $blog_users ) ) {
						$user_site = "<a href='" . esc_url( get_home_url( $details->userblog_id ) ) . "'>{$details->blogname}</a>";
						$user_dropdown = "<select name='blog[$val][{$key}]'>";
						$user_list = '';
						foreach ( $blog_users as $user ) {
							if ( ! in_array( $user->ID, $allusers ) )
								$user_list .= "<option value='{$user->ID}'>{$user->user_login}</option>";
						}
						if ( '' == $user_list )
							$user_list = $admin_out;
						$user_dropdown .= $user_list;
						$user_dropdown .= "</select>\n";
						?>
						<ul style="list-style:none;">
							<li><?php printf( __( 'Site: %s' ), $user_site ); ?></li>
							<li><label><input type="radio" id="delete_option0" name="delete[<?php echo $details->userblog_id . '][' . $delete_user->ID ?>]" value="delete" checked="checked" />
							<?php _e( 'Delete all posts and links.' ); ?></label></li>
							<li><label><input type="radio" id="delete_option1" name="delete[<?php echo $details->userblog_id . '][' . $delete_user->ID ?>]" value="reassign" />
							<?php echo __( 'Attribute all posts and links to:' ) . '</label>' . $user_dropdown; ?></li>
						</ul>
						<?php
					}
				}
				echo "</fieldset>";
			}
		}
	}

	submit_button( __('Confirm Deletion'), 'delete' );
	?>
	</form>
    <?php
	return true;
}

if ( isset( $_GET['action'] ) ) {
	do_action( 'wpmuadminedit' , '' );

	switch ( $_GET['action'] ) {
		case 'deleteuser':
			if ( ! current_user_can( 'manage_network_users' ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			check_admin_referer( 'deleteuser' );

			$id = intval( $_GET['id'] );
			if ( $id != '0' && $id != '1' ) {
				$_POST['allusers'] = array( $id ); // confirm_delete_users() can only handle with arrays
				$title = __( 'Users' );
				$parent_file = 'users.php';
				require_once( '../admin-header.php' );
				echo '<div class="wrap">';
				confirm_delete_users( $_POST['allusers'] );
				echo '</div>';
	            require_once( '../admin-footer.php' );
	  		} else {
				wp_redirect( network_admin_url( 'users.php' ) );
			}
			exit();
		break;

		case 'allusers':
			if ( !current_user_can( 'manage_network_users' ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			if ( ( isset( $_POST['action']) || isset($_POST['action2'] ) ) && isset( $_POST['allusers'] ) ) {
				check_admin_referer( 'bulk-users-network' );

				if ( $_GET['action'] != -1 || $_POST['action2'] != -1 )
					$doaction = $_POST['action'] != -1 ? $_POST['action'] : $_POST['action2'];

				$userfunction = '';

				foreach ( (array) $_POST['allusers'] as $key => $val ) {
					if ( !empty( $val ) ) {
						switch ( $doaction ) {
							case 'delete':
								if ( ! current_user_can( 'delete_users' ) )
									wp_die( __( 'You do not have permission to access this page.' ) );
								$title = __( 'Users' );
								$parent_file = 'users.php';
								require_once( '../admin-header.php' );
								echo '<div class="wrap">';
								confirm_delete_users( $_POST['allusers'] );
								echo '</div>';
					            require_once( '../admin-footer.php' );
					            exit();
	       					break;

							case 'spam':
								$user = new WP_User( $val );
								if ( in_array( $user->user_login, get_super_admins() ) )
									wp_die( sprintf( __( 'Warning! User cannot be modified. The user %s is a network administrator.' ), esc_html( $user->user_login ) ) );

								$userfunction = 'all_spam';
								$blogs = get_blogs_of_user( $val, true );
								foreach ( (array) $blogs as $key => $details ) {
									if ( $details->userblog_id != $current_site->blog_id ) // main blog not a spam !
										update_blog_status( $details->userblog_id, 'spam', '1' );
								}
								update_user_status( $val, 'spam', '1' );
							break;

							case 'notspam':
								$userfunction = 'all_notspam';
								$blogs = get_blogs_of_user( $val, true );
								foreach ( (array) $blogs as $key => $details )
									update_blog_status( $details->userblog_id, 'spam', '0' );

								update_user_status( $val, 'spam', '0' );
							break;
						}
					}
				}

				wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => $userfunction ), wp_get_referer() ) );
			} else {
				$location = network_admin_url( 'users.php' );

				if ( ! empty( $_REQUEST['paged'] ) )
					$location = add_query_arg( 'paged', (int) $_REQUEST['paged'], $location );
				wp_redirect( $location );
			}
			exit();
		break;

		case 'dodelete':
			check_admin_referer( 'ms-users-delete' );
			if ( ! ( current_user_can( 'manage_network_users' ) && current_user_can( 'delete_users' ) ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			if ( ! empty( $_POST['blog'] ) && is_array( $_POST['blog'] ) ) {
				foreach ( $_POST['blog'] as $id => $users ) {
					foreach ( $users as $blogid => $user_id ) {
						if ( ! current_user_can( 'delete_user', $id ) )
							continue;

						if ( ! empty( $_POST['delete'] ) && 'reassign' == $_POST['delete'][$blogid][$id] )
							remove_user_from_blog( $id, $blogid, $user_id );
						else
							remove_user_from_blog( $id, $blogid );
					}
				}
			}
			$i = 0;
			if ( is_array( $_POST['user'] ) && ! empty( $_POST['user'] ) )
				foreach( $_POST['user'] as $id ) {
					if ( ! current_user_can( 'delete_user', $id ) )
						continue;
					wpmu_delete_user( $id );
					$i++;
				}

			if ( $i == 1 )
				$deletefunction = 'delete';
			else
				$deletefunction = 'all_delete';

			wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => $deletefunction ), network_admin_url( 'users.php' ) ) );
			exit();
		break;
	}
}

$wp_list_table = _get_list_table('WP_MS_Users_List_Table');
$pagenum = $wp_list_table->get_pagenum();
$wp_list_table->prepare_items();
$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );

if ( $pagenum > $total_pages && $total_pages > 0 ) {
	wp_redirect( add_query_arg( 'paged', $total_pages ) );
	exit;
}
$title = __( 'Users' );
$parent_file = 'users.php';

add_screen_option( 'per_page', array('label' => _x( 'Users', 'users per page (screen options)' )) );

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' =>
		'<p>' . __('This table shows all users across the network and the sites to which they are assigned.') . '</p>' .
		'<p>' . __('Hover over any user on the list to make the edit links appear. The Edit link on the left will take you to his or her Edit User profile page; the Edit link on the right by any site name goes to an Edit Site screen for that site.') . '</p>' .
		'<p>' . __('You can also go to the user&#8217;s profile page by clicking on the individual username.') . '</p>' .
		'<p>' . __('You can sort the table by clicking on any of the bold headings and switch between list and excerpt views by using the icons in the upper right.') . '</p>' .
		'<p>' . __('The bulk action will permanently delete selected users, or mark/unmark those selected as spam. Spam users will have posts removed and will be unable to sign up again with the same email addresses.') . '</p>' .
		'<p>' . __('You can make an existing user an additional super admin by going to the Edit User profile page and checking the box to grant that privilege.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Network_Admin_Users_Screen" target="_blank">Documentation on Network Users</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/forum/multisite/" target="_blank">Support Forums</a>') . '</p>'
);

require_once( '../admin-header.php' );

if ( isset( $_REQUEST['updated'] ) && $_REQUEST['updated'] == 'true' && ! empty( $_REQUEST['action'] ) ) {
	?>
	<div id="message" class="updated"><p>
		<?php
		switch ( $_REQUEST['action'] ) {
			case 'delete':
				_e( 'User deleted.' );
			break;
			case 'all_spam':
				_e( 'Users marked as spam.' );
			break;
			case 'all_notspam':
				_e( 'Users removed from spam.' );
			break;
			case 'all_delete':
				_e( 'Users deleted.' );
			break;
			case 'add':
				_e( 'User added.' );
			break;
		}
		?>
	</p></div>
	<?php
}
	?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php esc_html_e( 'Users' );
	if ( current_user_can( 'create_users') ) : ?>
		<a href="<?php echo network_admin_url('user-new.php'); ?>" class="add-new-h2"><?php echo esc_html_x( 'Add New', 'user' ); ?></a><?php
	endif;

	if ( !empty( $usersearch ) )
	printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $usersearch ) );
	?>
	</h2>

	<?php $wp_list_table->views(); ?>

	<form action="" method="get" class="search-form">
		<?php $wp_list_table->search_box( __( 'Search Users' ), 'user' ); ?>
	</form>

	<form id="form-user-list" action='users.php?action=allusers' method='post'>
		<?php $wp_list_table->display(); ?>
	</form>
</div>

<?php require_once( '../admin-footer.php' ); ?>
