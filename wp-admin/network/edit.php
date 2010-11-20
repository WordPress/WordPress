<?php
/**
 * Action handler for Multisite administration panels.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( empty( $_GET['action'] ) )
	wp_redirect( admin_url( 'index.php' ) );

function confirm_delete_users( $users ) {
	$current_user = wp_get_current_user();
	if ( !is_array( $users ) )
		return false;

	screen_icon();
	?>
	<h2><?php esc_html_e( 'Users' ); ?></h2>
	<p><?php _e( 'Transfer or delete posts and links before deleting users.' ); ?></p>
	<form action="edit.php?action=dodelete" method="post">
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
					$blog_users = get_users_of_blog( $details->userblog_id );
					if ( is_array( $blog_users ) && !empty( $blog_users ) ) {
						$user_site = "<a href='" . esc_url( get_home_url( $details->userblog_id ) ) . "'>{$details->blogname}</a>";
						$user_dropdown = "<select name='blog[$val][{$key}]'>";
						$user_list = '';
						foreach ( $blog_users as $user ) {
							if ( $user->user_id != $val && !in_array( $user->id, $allusers ) )
								$user_list .= "<option value='{$user->id}'>{$user->user_login}</option>";
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

do_action( 'wpmuadminedit' , '');

if ( isset( $_GET['id' ]) )
	$id = intval( $_GET['id'] );
elseif ( isset( $_POST['id'] ) )
	$id = intval( $_POST['id'] );

switch ( $_GET['action'] ) {
	case 'siteoptions':
		check_admin_referer( 'siteoptions' );
		if ( ! current_user_can( 'manage_network_options' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		if ( empty( $_POST ) )
			wp_die( sprintf( __( 'You probably need to go back to the <a href="%s">options page</a>.', esc_url( admin_url( 'settings.php' ) ) ) ) );

		if ( isset($_POST['WPLANG']) && ( '' === $_POST['WPLANG'] || in_array( $_POST['WPLANG'], get_available_languages() ) ) )
			update_site_option( 'WPLANG', $_POST['WPLANG'] );

		if ( is_email( $_POST['admin_email'] ) )
			update_site_option( 'admin_email', $_POST['admin_email'] );

		$illegal_names = split( ' ', $_POST['illegal_names'] );
		foreach ( (array) $illegal_names as $name ) {
			$name = trim( $name );
			if ( $name != '' )
				$names[] = trim( $name );
		}
		update_site_option( 'illegal_names', $names );

		if ( $_POST['limited_email_domains'] != '' ) {
			$limited_email_domains = str_replace( ' ', "\n", $_POST['limited_email_domains'] );
			$limited_email_domains = split( "\n", stripslashes( $limited_email_domains ) );
			$limited_email = array();
			foreach ( (array) $limited_email_domains as $domain ) {
					$domain = trim( $domain );
				if ( ! preg_match( '/(--|\.\.)/', $domain ) && preg_match( '|^([a-zA-Z0-9-\.])+$|', $domain ) )
					$limited_email[] = trim( $domain );
			}
			update_site_option( 'limited_email_domains', $limited_email );
		} else {
			update_site_option( 'limited_email_domains', '' );
		}

		if ( $_POST['banned_email_domains'] != '' ) {
			$banned_email_domains = split( "\n", stripslashes( $_POST['banned_email_domains'] ) );
			$banned = array();
			foreach ( (array) $banned_email_domains as $domain ) {
				$domain = trim( $domain );
				if ( ! preg_match( '/(--|\.\.)/', $domain ) && preg_match( '|^([a-zA-Z0-9-\.])+$|', $domain ) )
					$banned[] = trim( $domain );
			}
			update_site_option( 'banned_email_domains', $banned );
		} else {
			update_site_option( 'banned_email_domains', '' );
		}

		$options = array( 'registrationnotification', 'registration', 'add_new_users', 'menu_items', 'mu_media_buttons', 'upload_space_check_disabled', 'blog_upload_space', 'upload_filetypes', 'site_name', 'first_post', 'first_page', 'first_comment', 'first_comment_url', 'first_comment_author', 'welcome_email', 'welcome_user_email', 'fileupload_maxk', 'global_terms_enabled' );
		$checked_options = array( 'mu_media_buttons' => array(), 'menu_items' => array(), 'registrationnotification' => 'no', 'upload_space_check_disabled' => 1, 'add_new_users' => 0 );
		foreach ( $checked_options as $option_name => $option_unchecked_value ) {
			if ( ! isset( $_POST[$option_name] ) )
				$_POST[$option_name] = $option_unchecked_value;
		}
		foreach ( $options as $option_name ) {
			if ( ! isset($_POST[$option_name]) )
				continue;
			$value = stripslashes_deep( $_POST[$option_name] );
			update_site_option( $option_name, $value );
		}

		// Update more options here
		do_action( 'update_wpmu_options' );

		wp_redirect( add_query_arg( 'updated', 'true', network_admin_url( 'settings.php' ) ) );
		exit();
	break;

	case 'updateblog':
		// No longer used.
	break;

	case 'deleteblog':
		check_admin_referer('deleteblog');
		if ( ! ( current_user_can( 'manage_sites' ) && current_user_can( 'delete_sites' ) ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		if ( $id != '0' && $id != $current_site->blog_id && current_user_can ( 'delete_site', $id ) ) {
			wpmu_delete_blog( $id, true );
			wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'delete' ), wp_get_referer() ) );
		} else {
			wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'not_deleted' ), wp_get_referer() ) );
		}
		
		exit();
	break;

	case 'allblogs':
		if ( isset( $_POST['doaction']) || isset($_POST['doaction2'] ) ) {
			check_admin_referer( 'bulk-sites' );

			if ( ! current_user_can( 'manage_sites' ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			if ( $_GET['action'] != -1 || $_POST['action2'] != -1 )
				$doaction = $_POST['action'] != -1 ? $_POST['action'] : $_POST['action2'];


			foreach ( (array) $_POST['allblogs'] as $key => $val ) {
				if ( $val != '0' && $val != $current_site->blog_id ) {
					switch ( $doaction ) {
						case 'delete':
							if ( ! current_user_can( 'delete_site', $val ) )
								wp_die( __( 'You are not allowed to delete the site.' ) );
							$blogfunction = 'all_delete';
							wpmu_delete_blog( $val, true );
						break;

						case 'spam':
							$blogfunction = 'all_spam';
							update_blog_status( $val, 'spam', '1', 0 );
							set_time_limit( 60 );
						break;

						case 'notspam':
							$blogfunction = 'all_notspam';
							update_blog_status( $val, 'spam', '0', 0 );
							set_time_limit( 60 );
						break;
					}
				} else {
					wp_die( __( 'You are not allowed to change the current site.' ) );
				}
			}

			wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => $blogfunction ), wp_get_referer() ) );
			exit();
		} else {
			wp_redirect( network_admin_url( 'sites.php' ) );
		}
	break;

	case 'archiveblog':
		check_admin_referer( 'archiveblog' );
		if ( ! current_user_can( 'manage_sites' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		update_blog_status( $id, 'archived', '1' );
		wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'archive' ), wp_get_referer() ) );
		exit();
	break;

	case 'unarchiveblog':
		check_admin_referer( 'unarchiveblog' );
		if ( ! current_user_can( 'manage_sites' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		update_blog_status( $id, 'archived', '0' );
		wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'unarchive' ), wp_get_referer() ) );
		exit();
	break;

	case 'activateblog':
		check_admin_referer( 'activateblog' );
		if ( ! current_user_can( 'manage_sites' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		update_blog_status( $id, 'deleted', '0' );
		do_action( 'activate_blog', $id );
		wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'activate' ), wp_get_referer() ) );
		exit();
	break;

	case 'deactivateblog':
		check_admin_referer( 'deactivateblog' );
		if ( ! current_user_can( 'manage_sites' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		do_action( 'deactivate_blog', $id );
		update_blog_status( $id, 'deleted', '1' );
		wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'deactivate' ), wp_get_referer() ) );
		exit();
	break;

	case 'unspamblog':
		check_admin_referer( 'unspamblog' );
		if ( ! current_user_can( 'manage_sites' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		update_blog_status( $id, 'spam', '0' );
		wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'unspam' ), wp_get_referer() ) );
		exit();
	break;

	case 'spamblog':
		check_admin_referer( 'spamblog' );
		if ( ! current_user_can( 'manage_sites' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		update_blog_status( $id, 'spam', '1' );
		wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'spam' ), wp_get_referer() ) );
		exit();
	break;

	case 'unmatureblog':
		check_admin_referer( 'unmatureblog' );
		if ( ! current_user_can( 'manage_sites' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		update_blog_status( $id, 'mature', '0' );
		wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'unmature' ), wp_get_referer() ) );
		exit();
	break;

	case 'matureblog':
		check_admin_referer( 'matureblog' );
		if ( ! current_user_can( 'manage_sites' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		update_blog_status( $id, 'mature', '1' );
		wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'mature' ), wp_get_referer() ) );
		exit();
	break;

	// Common
	case 'confirm':
		if ( !headers_sent() ) {
			nocache_headers();
			header( 'Content-Type: text/html; charset=utf-8' );
		}
		if ( $current_site->blog_id == $id )
			wp_die( __( 'You are not allowed to change the current site.' ) );
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" <?php if ( function_exists( 'language_attributes' ) ) language_attributes(); ?>>
			<head>
				<title><?php _e( 'WordPress &rsaquo; Confirm your action' ); ?></title>

				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<?php
				wp_admin_css( 'install', true );
				wp_admin_css( 'ie', true );
				?>
			</head>
			<body>
				<h1 id="logo"><img alt="WordPress" src="<?php echo esc_attr( admin_url( 'images/wordpress-logo.png' ) ); ?>" /></h1>
				<form action="edit.php?action=<?php echo esc_attr( $_GET['action2'] ) ?>" method="post">
					<input type="hidden" name="action" value="<?php echo esc_attr( $_GET['action2'] ) ?>" />
					<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
					<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( wp_get_referer() ); ?>" />
					<?php wp_nonce_field( $_GET['action2'], '_wpnonce', false ); ?>
					<p><?php echo esc_html( stripslashes( $_GET['msg'] ) ); ?></p>
					<?php submit_button( __('Confirm'), 'button' ); ?>
				</form>
			</body>
		</html>
		<?php
	break;

	// Users
	case 'deleteuser':
		if ( ! current_user_can( 'manage_network_users' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		check_admin_referer( 'deleteuser' );

		if ( $id != '0' && $id != '1' ) {
			$_POST['allusers'] = array( $id ); // confirm_delete_users() can only handle with arrays
			$title = __( 'Users' );
			$parent_file = 'users.php';
			require_once( '../admin-header.php' );
			echo '<div class="wrap">';
			confirm_delete_users( $_POST['allusers'] );
			echo '</div>';
            require_once( '../admin-footer.php' );
            exit();
		} else {
			wp_redirect( network_admin_url( 'users.php' ) );
		}
	break;

	case 'allusers':
		if ( !current_user_can( 'manage_network_users' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );

		if ( isset( $_POST['doaction']) || isset($_POST['doaction2'] ) ) {
			check_admin_referer( 'bulk-users-network' );

			if ( $_GET['action'] != -1 || $_POST['action2'] != -1 )
				$doaction = $_POST['action'] != -1 ? $_POST['action'] : $_POST['action2'];

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
							update_user_status( $val, 'spam', '1', 1 );
						break;

						case 'notspam':
							$userfunction = 'all_notspam';
							$blogs = get_blogs_of_user( $val, true );
							foreach ( (array) $blogs as $key => $details )
								update_blog_status( $details->userblog_id, 'spam', '0' );

							update_user_status( $val, 'spam', '0', 1 );
						break;
					}
				}
			}

			wp_redirect( add_query_arg( array( 'updated' => 'true', 'action' => $userfunction ), wp_get_referer() ) );
			exit();
		} else {
			wp_redirect( network_admin_url( 'users.php' ) );
		}
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
	break;

	default:
		wp_redirect( network_admin_url( 'index.php' ) );
	break;
}
?>
