<?php
require_once('admin.php');

if ( !is_multisite() )
	wp_die( __('Multisite support is not enabled.') );

if ( !is_super_admin() )
	wp_die( __('You do not have permission to access this page.') );

do_action('wpmuadminedit', '');

if ( isset($_GET[ 'id' ]) )
	$id = intval( $_GET[ 'id' ] );
elseif ( isset($_POST[ 'id' ]) )
	$id = intval( $_POST[ 'id' ] );

if ( isset( $_POST['ref'] ) == false && !empty($_SERVER['HTTP_REFERER']) )
	$_POST['ref'] = $_SERVER['HTTP_REFERER'];

switch ( $_GET['action'] ) {
	case "siteoptions":
		check_admin_referer('siteoptions');
		if ( empty( $_POST ) )
			wp_die( __("You probably need to go back to the <a href='ms-options.php'>options page</a>") );

		if ( isset($_POST['WPLANG']) && ( '' === $_POST['WPLANG'] || in_array($_POST['WPLANG'], get_available_languages()) ) )
			update_site_option( "WPLANG", $_POST['WPLANG'] );

		if ( is_email( $_POST['admin_email'] ) )
			update_site_option( "admin_email", $_POST['admin_email'] );

		$illegal_names = split( ' ', $_POST['illegal_names'] );
		foreach ( (array) $illegal_names as $name ) {
			$name = trim( $name );
			if ( $name != '' )
				$names[] = trim( $name );
		}
		update_site_option( "illegal_names", $names );

		if ( $_POST['limited_email_domains'] != '' ) {
			$limited_email_domains = str_replace( ' ', "\n", $_POST[ 'limited_email_domains' ] );
			$limited_email_domains = split( "\n", stripslashes( $limited_email_domains ) );
			foreach ( (array) $limited_email_domains as $domain ) {
				$limited_email[] = trim( $domain );
			}
			update_site_option( "limited_email_domains", $limited_email );
		} else {
			update_site_option( "limited_email_domains", '' );
		}

		if ( $_POST['banned_email_domains'] != '' ) {
			$banned_email_domains = split( "\n", stripslashes( $_POST[ 'banned_email_domains' ] ) );
			foreach ( (array) $banned_email_domains as $domain ) {
				$banned[] = trim( $domain );
			}
			update_site_option( "banned_email_domains", $banned );
		} else {
			update_site_option( "banned_email_domains", '' );
		}
		update_site_option( 'default_user_role', $_POST[ 'default_user_role' ] );
		if ( trim( $_POST[ 'dashboard_blog_orig' ] ) == '' )
			$_POST[ 'dashboard_blog_orig' ] = $current_site->blog_id;
		if ( trim( $_POST[ 'dashboard_blog' ] ) == '' ) {
			$_POST[ 'dashboard_blog' ] = $current_site->blog_id;
			$dashboard_blog_id = $current_site->blog_id;
		} else {
			$dashboard_blog = untrailingslashit( sanitize_user( str_replace( '.', '', str_replace( $current_site->domain . $current_site->path, '', $_POST[ 'dashboard_blog' ] ) ) ) );
			$blog_details = get_blog_details( $dashboard_blog );
			if ( false === $blog_details ) {
				if ( is_numeric( $dashboard_blog ) )
					wp_die( __( 'Dashboard blog_id must be a blog that already exists' ) );
				if ( is_subdomain_install() ) {
					$domain = $dashboard_blog . '.' . $current_site->domain;
					$path = $current_site->path;
				} else {
					$domain = $current_site->domain;
					$path = trailingslashit( $current_site->path . $dashboard_blog );
				}
				$wpdb->hide_errors();
				$dashboard_blog_id = wpmu_create_blog( $domain, $path, __( 'My Dashboard' ), $current_user->id , array( "public" => 0 ), $current_site->id );
				$wpdb->show_errors();
			} else {
				$dashboard_blog_id = $blog_details->blog_id;
			}
		}
		if ( is_wp_error( $dashboard_blog_id ) )
			wp_die( __( 'Problem creating dashboard blog: ' ) . $dashboard_blog_id->get_error_message() );
		if ( $_POST[ 'dashboard_blog_orig' ] != $_POST[ 'dashboard_blog' ] ) {
			$users = get_users_of_blog( get_site_option( 'dashboard_blog' ) );
			$move_users = array();
			foreach ( (array)$users as $user ) {
				if ( array_pop( array_keys( unserialize( $user->meta_value ) ) ) == 'subscriber' )
					$move_users[] = $user->user_id;
			}
			if ( false == empty( $move_users ) ) {
				foreach ( (array)$move_users as $user_id ) {
					remove_user_from_blog($user_id, get_site_option( 'dashboard_blog' ) );
					add_user_to_blog( $dashboard_blog_id, $user_id, get_site_option( 'default_user_role', 'subscriber' ) );
					update_usermeta( $user_id, 'primary_blog', $dashboard_blog_id );
				}
			}
		}
		update_site_option( "dashboard_blog", $dashboard_blog_id );
		$options = array( 'registrationnotification', 'registration', 'add_new_users', 'menu_items', 'mu_media_buttons', 'upload_space_check_disabled', 'blog_upload_space', 'upload_filetypes', 'site_name', 'first_post', 'first_page', 'first_comment', 'first_comment_url', 'first_comment_author', 'welcome_email', 'welcome_user_email', 'fileupload_maxk', 'admin_notice_feed' );
		foreach ( $options as $option_name ) {
			if ( ! isset($_POST[ $option_name ]) )
				continue;
			$value = stripslashes_deep( $_POST[ $option_name ] );
			update_site_option( $option_name, $value );
		}

		$site_admins = explode( ' ', str_replace( ",", " ", $_POST['site_admins'] ) );
		if ( is_array( $site_admins ) ) {
			$mainblog_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE domain='{$current_site->domain}' AND path='{$current_site->path}'" );
			if ( $mainblog_id ) {
				reset( $site_admins );
				foreach ( (array) $site_admins as $site_admin ) {
					$uid = get_user_by('login', $site_admin);
					if ( $uid )
						add_user_to_blog( $mainblog_id, $uid->ID, 'administrator' );
				}
			}
			update_site_option( 'site_admins' , $site_admins );
		}

		// Update more options here
		do_action( 'update_wpmu_options' );

		wp_redirect( add_query_arg( "updated", "true", 'ms-options.php' ) );
		exit();
	break;
	case "addblog":
		check_admin_referer('add-blog');

		if ( is_array( $_POST[ 'blog' ] ) == false )
			wp_die( "Can't create an empty blog." );
		$blog = $_POST['blog'];
		$domain = sanitize_user( str_replace( '/', '', $blog[ 'domain' ] ) );
		$email = sanitize_email( $blog[ 'email' ] );
		$title = $blog[ 'title' ];

		if ( empty($domain) || empty($email) )
			wp_die( __('Missing blog address or email address.') );
		if ( !is_email( $email ) )
			wp_die( __('Invalid email address') );

		if ( is_subdomain_install() ) {
			$newdomain = $domain.".".$current_site->domain;
			$path = $base;
		} else {
			$newdomain = $current_site->domain;
			$path = $base.$domain.'/';
		}

		$password = 'N/A';
		$user_id = email_exists($email);
		if ( !$user_id ) { // Create a new user with a random password
			$password = wp_generate_password();
			$user_id = wpmu_create_user( $domain, $password, $email );
			if ( false == $user_id )
				wp_die( __('There was an error creating the user') );
			else
				wp_new_user_notification($user_id, $password);
		}

		$wpdb->hide_errors();
		$id = wpmu_create_blog($newdomain, $path, $title, $user_id , array( "public" => 1 ), $current_site->id);
		$wpdb->show_errors();
		if ( !is_wp_error($id) ) {
			$dashboard_blog = get_dashboard_blog();
			if ( get_user_option( 'primary_blog', $user_id ) == $dashboard_blog->blog_id )
				update_user_option( $user_id, 'primary_blog', $id, true );
			$content_mail = sprintf( __( "New blog created by %1s\n\nAddress: http://%2s\nName: %3s"), $current_user->user_login , $newdomain.$path, stripslashes( $title ) );
			wp_mail( get_site_option('admin_email'),  sprintf(__('[%s] New Blog Created'), $current_site->site_name), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
			wpmu_welcome_notification( $id, $user_id, $password, $title, array( "public" => 1 ) );
			wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'add-blog'), $_SERVER['HTTP_REFERER'] ) );
			exit();
		} else {
			wp_die( $id->get_error_message() );
		}
	break;

	case "updateblog":
		check_admin_referer('editblog');
		if ( empty( $_POST ) )
			wp_die( __('You probably need to go back to the <a href="ms-sites.php">sites page</a>') );

		// themes
		if ( isset($_POST[ 'theme' ]) && is_array( $_POST[ 'theme' ] ) )
			$_POST[ 'option' ][ 'allowedthemes' ] = $_POST[ 'theme' ];
		else
			$_POST[ 'option' ][ 'allowedthemes' ] = '';

		switch_to_blog( $id );
		if ( is_array( $_POST[ 'option' ] ) ) {
			$c = 1;
			$count = count( $_POST[ 'option' ] );
			foreach ( (array) $_POST['option'] as $key => $val ) {
				if ( $key === 0 )
					continue; // Avoids "0 is a protected WP option and may not be modified" error when edit blog options
				if ( $c == $count )
					update_option( $key, $val );
				else
					update_option( $key, $val, false ); // no need to refresh blog details yet
				$c++;
			}
		}

		if ( $_POST['update_home_url'] == 'update' ) {
			$blog_address = get_blogaddress_by_domain($_POST['blog']['domain'], $_POST['blog']['path']);
			if ( get_option( 'siteurl' ) !=  $blog_address )
				update_option( 'siteurl', $blog_address);

			if ( get_option( 'home' ) != $blog_address )
				update_option( 'home', $blog_address );
		}

		$wp_rewrite->flush_rules();

		// update blogs table
		$blog_data = stripslashes_deep($_POST[ 'blog' ]);
		update_blog_details($id, $blog_data);

		// get blog prefix
		$blog_prefix = $wpdb->get_blog_prefix( $id );

		// user roles
		if ( is_array( $_POST[ 'role' ] ) == true ) {
			$newroles = $_POST[ 'role' ];
			reset( $newroles );
			foreach ( (array) $newroles as $userid => $role ) {
				$user = new WP_User($userid);
				if ( ! $user )
					continue;
				$user->for_blog($id);
				$user->set_role($role);
			}
		}

		// remove user
		if ( isset($_POST[ 'blogusers' ]) && is_array( $_POST[ 'blogusers' ] ) ) {
			reset( $_POST[ 'blogusers' ] );
			foreach ( (array) $_POST[ 'blogusers' ] as $key => $val )
				remove_user_from_blog( $key, $id );
		}

		// change password
		if ( is_array( $_POST[ 'user_password' ] ) ) {
			reset( $_POST[ 'user_password' ] );
			$newroles = $_POST[ 'role' ];
			foreach ( (array) $_POST[ 'user_password' ] as $userid => $pass ) {
				unset( $_POST[ 'role' ] );
				$_POST[ 'role' ] = $newroles[ $userid ];
				if ( $pass != '' ) {
					$cap = $wpdb->get_var( "SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = '{$userid}' AND meta_key = '{$blog_prefix}capabilities' AND meta_value = 'a:0:{}'" );
					$userdata = get_userdata($userid);
					$_POST[ 'pass1' ] = $_POST[ 'pass2' ] = $pass;
					$_POST[ 'email' ] = $userdata->user_email;
					$_POST[ 'rich_editing' ] = $userdata->rich_editing;
					edit_user( $userid );
					if ( $cap == null )
						$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE user_id = '{$userid}' AND meta_key = '{$blog_prefix}capabilities' AND meta_value = 'a:0:{}'" );
				}
			}
			unset( $_POST[ 'role' ] );
			$_POST[ 'role' ] = $newroles;
		}

		// add user?
		if ( $_POST[ 'newuser' ] != '' ) {
			$newuser = $_POST[ 'newuser' ];
			$userid = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->users . " WHERE user_login = %s", $newuser ) );
			if ( $userid ) {
				$user = $wpdb->get_var( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE user_id='$userid' AND meta_key='wp_" . $id . "_capabilities'" );
				if ( $user == false )
					add_user_to_blog($id, $userid, $_POST[ 'new_role' ]);
			}
		}
		do_action( 'wpmu_update_blog_options' );
		restore_current_blog();
		wpmu_admin_do_redirect( "ms-sites.php?action=editblog&updated=true&id=".$id );
	break;

	case "deleteblog":
		check_admin_referer('deleteblog');
		if ( $id != '0' && $id != $current_site->blog_id )
			wpmu_delete_blog( $id, true );

		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'delete'), $_POST[ 'ref' ] ) );
		exit();
	break;

	case "allblogs":
		check_admin_referer('allblogs');
		foreach ( (array) $_POST[ 'allblogs' ] as $key => $val ) {
			if ( $val != '0' && $val != $current_site->blog_id ) {
				if ( isset($_POST['allblog_delete']) ) {
					$blogfunction = 'all_delete';
					wpmu_delete_blog( $val, true );
				} elseif ( isset($_POST['allblog_spam']) ) {
					$blogfunction = 'all_spam';
					update_blog_status( $val, "spam", '1', 0 );
					set_time_limit(60);
				} elseif ( isset($_POST['allblog_notspam']) ) {
					$blogfunction = 'all_notspam';
					update_blog_status( $val, "spam", '0', 0 );
					set_time_limit(60);
				}
			}
		}

		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => $blogfunction), $_SERVER['HTTP_REFERER'] ) );
		exit();
	break;

	case "archiveblog":
		check_admin_referer('archiveblog');
		update_blog_status( $id, "archived", '1' );
		do_action( "archive_blog", $id );
		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'archive'), $_POST['ref'] ) );
		exit();
	break;

	case "unarchiveblog":
		check_admin_referer('unarchiveblog');
		do_action( "unarchive_blog", $id );
		update_blog_status( $id, "archived", '0' );
		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'unarchive'), $_POST['ref'] ) );
		exit();
	break;

	case "activateblog":
		check_admin_referer('activateblog');
		update_blog_status( $id, "deleted", '0' );
		do_action( "activate_blog", $id );
		wp_redirect( add_query_arg( "updated", array('updated' => 'true', 'action' => 'activate'), $_POST['ref'] ) );
		exit();
	break;

	case "deactivateblog":
		check_admin_referer('deactivateblog');
		do_action( "deactivate_blog", $id );
		update_blog_status( $id, "deleted", '1' );
		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'deactivate'), $_POST['ref'] ) );
		exit();
	break;

	case "unspamblog":
		check_admin_referer('unspamblog');
		update_blog_status( $id, "spam", '0' );
		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'unspam'), $_POST['ref'] ) );
		exit();
	break;

	case "spamblog":
		check_admin_referer('spamblog');
		update_blog_status( $id, "spam", '1' );
		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'spam'), $_POST['ref'] ) );
		exit();
	break;

	case "mature":
		update_blog_status( $id, 'mature', '1' );
		do_action( 'mature_blog', $id );
		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'mature'), $_POST['ref'] ) );
		exit();
	break;

	case "unmature":
		update_blog_status( $id, 'mature', '0' );
		do_action( 'unmature_blog', $id );

		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'umature'), $_POST['ref'] ) );
		exit();
	break;

	// Themes
    case "updatethemes":
    	if ( is_array( $_POST['theme'] ) ) {
			$themes = get_themes();
			reset( $themes );
			foreach ( (array) $themes as $key => $theme ) {
				if ( $_POST['theme'][ wp_specialchars( $theme['Stylesheet'] ) ] == 'enabled' )
					$allowed_themes[ wp_specialchars( $theme['Stylesheet'] ) ] = true;
			}
			update_site_option( 'allowedthemes', $allowed_themes );
		}
		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'themes'), $_SERVER['HTTP_REFERER'] ) );
		exit();
	break;

	// Common
	case "confirm":
		$referrer = ( isset($_GET['ref']) ) ? stripslashes($_GET['ref']) : $_SERVER['HTTP_REFERER'];
		$referrer = clean_url($referrer);
		if ( !headers_sent() ) {
			nocache_headers();
			header( 'Content-Type: text/html; charset=utf-8' );
		}
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" <?php if ( function_exists('language_attributes') ) language_attributes(); ?>>
			<head>
				<title><?php _e("WordPress MU &rsaquo; Confirm your action"); ?></title>

				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<?php wp_admin_css( 'install', true ); ?>
			</head>
			<body id="error-page">
				<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
				<form action='ms-edit.php?action=<?php echo wp_specialchars( $_GET[ 'action2' ] ) ?>' method='post'>
					<input type='hidden' name='action' value='<?php echo wp_specialchars( $_GET['action2'] ) ?>' />
					<input type='hidden' name='id' value='<?php echo wp_specialchars( $id ); ?>' />
					<input type='hidden' name='ref' value='<?php echo $referrer; ?>' />
					<?php wp_nonce_field( $_GET['action2'] ) ?>
					<p><?php echo wp_specialchars( stripslashes($_GET['msg']) ); ?></p>
					<p class="submit"><input class="button" type='submit' value='<?php _e("Confirm"); ?>' /></p>
				</form>
			</body>
		</html>
		<?php
	break;

	// Users (not used any more)
	case "deleteuser":
		check_admin_referer('deleteuser');
		if ( $id != '0' && $id != '1' )
			wpmu_delete_user($id);

		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'delete'), $_POST['ref'] ) );
		exit();
	break;

	case "allusers":
		check_admin_referer('allusers');
		if ( isset($_POST['alluser_delete']) ) {
			require_once('admin-header.php');
			echo '<div class="wrap" style="position:relative;">';
			confirm_delete_users( $_POST['allusers'] );
			echo '</div>';
		} elseif ( isset( $_POST[ 'alluser_transfer_delete' ] ) ) {
			if ( is_array( $_POST[ 'blog' ] ) && !empty( $_POST[ 'blog' ] ) ) {
				foreach ( $_POST[ 'blog' ] as $id => $users ) {
					foreach ( $users as $blogid => $user_id ) {
						remove_user_from_blog( $id, $blogid, $user_id );
					}
				}
			}
			if ( is_array( $_POST[ 'user' ] ) && !empty( $_POST[ 'user' ] ) )
				foreach( $_POST[ 'user' ] as $id )
					wpmu_delete_user( $id );

			wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'all_delete'), 'ms-users.php' ) );
		} else {
			foreach ( (array) $_POST['allusers'] as $key => $val ) {
				if ( $val == '' || $val == '0' )
					continue;
				$user = new WP_User( $val );
				if ( in_array( $user->user_login, get_site_option( 'site_admins', array( 'admin' ) ) ) )
					wp_die( sprintf( __( 'Warning! User cannot be modified. The user %s is a site admnistrator.' ), $user->user_login ) );
				if ( isset($_POST['alluser_spam']) ) {
					$userfunction = 'all_spam';
					$blogs = get_blogs_of_user( $val, true );
					foreach ( (array) $blogs as $key => $details ) {
						if ( $details->userblog_id == $current_site->blog_id ) { continue; } // main blog not a spam !
						update_blog_status( $details->userblog_id, "spam", '1' );
					}
					update_user_status( $val, "spam", '1', 1 );
				} elseif ( isset($_POST['alluser_notspam']) ) {
					$userfunction = 'all_notspam';
					$blogs = get_blogs_of_user( $val, true );
					foreach ( (array) $blogs as $key => $details ) {
						update_blog_status( $details->userblog_id, "spam", '0' );
					}
					update_user_status( $val, "spam", '0', 1 );
				}
			}
			wp_redirect( add_query_arg( array('updated' => 'true', 'action' => $userfunction), $_SERVER['HTTP_REFERER'] ) );
		}
		exit();
	break;

	case "adduser":
		check_admin_referer('add-user');

		if ( is_array( $_POST[ 'user' ] ) == false )
			wp_die( __( "Cannot create an empty user." ) );
		$user = $_POST['user'];
		if ( empty($user['username']) && empty($user['email']) )
			wp_die( __('Missing username and email.') );
		elseif ( empty($user['username']) )
			wp_die( __('Missing username.') );
		elseif ( empty($user['email']) )
			wp_die( __('Missing email.') );

		$password = wp_generate_password();
		$user_id = wpmu_create_user(wp_specialchars( strtolower( $user['username'] ) ), $password, wp_specialchars( $user['email'] ) );

		if ( false == $user_id )
 			wp_die( __('Duplicated username or email address.') );
		else
			wp_new_user_notification($user_id, $password);

		if ( get_site_option( 'dashboard_blog' ) == false )
			add_user_to_blog( $current_site->blog_id, $user_id, get_site_option( 'default_user_role', 'subscriber' ) );
		else
			add_user_to_blog( get_site_option( 'dashboard_blog' ), $user_id, get_site_option( 'default_user_role', 'subscriber' ) );

		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'add'), $_SERVER['HTTP_REFERER'] ) );
		exit();
	break;

	default:
		wpmu_admin_do_redirect( "ms-admin.php" );
	break;
}
?>
