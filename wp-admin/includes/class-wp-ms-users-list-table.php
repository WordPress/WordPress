<?php
/**
 * Multisite Users List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class WP_MS_Users_List_Table extends WP_List_Table {

	function ajax_user_can() {
		return current_user_can( 'manage_network_users' );
	}

	function prepare_items() {
		global $usersearch, $role, $wpdb, $mode;

		$usersearch = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

		$users_per_page = $this->get_items_per_page( 'users_network_per_page' );

		$role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : '';

		$paged = $this->get_pagenum();

		$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged-1 ) * $users_per_page,
			'search' => $usersearch,
			'blog_id' => 0,
			'fields' => 'all_with_meta'
		);

		$args['search'] = ltrim($args['search'], '*');

		if ( $role == 'super' ) {
			$logins = implode( "', '", get_super_admins() );
			$args['include'] = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE user_login IN ('$logins')" );
		}

		// If the network is large and a search is not being performed, show only the latest users with no paging in order
		// to avoid expensive count queries.
		if ( !$usersearch && ( get_blog_count() >= 10000 ) ) {
			if ( !isset($_REQUEST['orderby']) )
				$_GET['orderby'] = $_REQUEST['orderby'] = 'id';
			if ( !isset($_REQUEST['order']) )
				$_GET['order'] = $_REQUEST['order'] = 'DESC';
			$args['count_total'] = false;
		}

		if ( isset( $_REQUEST['orderby'] ) )
			$args['orderby'] = $_REQUEST['orderby'];

		if ( isset( $_REQUEST['order'] ) )
			$args['order'] = $_REQUEST['order'];

		$mode = empty( $_REQUEST['mode'] ) ? 'list' : $_REQUEST['mode'];

		// Query the user IDs for this page
		$wp_user_search = new WP_User_Query( $args );

		$this->items = $wp_user_search->get_results();

		$this->set_pagination_args( array(
			'total_items' => $wp_user_search->get_total(),
			'per_page' => $users_per_page,
		) );
	}

	function get_bulk_actions() {
		$actions = array();
		if ( current_user_can( 'delete_users' ) )
			$actions['delete'] = __( 'Delete' );
		$actions['spam'] = _x( 'Mark as Spam', 'user' );
		$actions['notspam'] = _x( 'Not Spam', 'user' );

		return $actions;
	}

	function no_items() {
		_e( 'No users found.' );
	}

	function get_views() {
		global $wp_roles, $role;

		$total_users = get_user_count();
		$super_admins = get_super_admins();
		$total_admins = count( $super_admins );

		$current_role = false;
		$class = $role != 'super' ? ' class="current"' : '';
		$role_links = array();
		$role_links['all'] = "<a href='" . network_admin_url('users.php') . "'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_users, 'users' ), number_format_i18n( $total_users ) ) . '</a>';
		$class = $role == 'super' ? ' class="current"' : '';
		$role_links['super'] = "<a href='" . network_admin_url('users.php?role=super') . "'$class>" . sprintf( _n( 'Super Admin <span class="count">(%s)</span>', 'Super Admins <span class="count">(%s)</span>', $total_admins ), number_format_i18n( $total_admins ) ) . '</a>';

		return $role_links;
	}

	function pagination( $which ) {
		global $mode;

		parent::pagination ( $which );

		if ( 'top' == $which )
			$this->view_switcher( $mode );
	}

	function get_columns() {
		$users_columns = array(
			'cb'         => '<input type="checkbox" />',
			'username'   => __( 'Username' ),
			'name'       => __( 'Name' ),
			'email'      => __( 'E-mail' ),
			'registered' => _x( 'Registered', 'user' ),
			'blogs'      => __( 'Sites' )
		);
		$users_columns = apply_filters( 'wpmu_users_columns', $users_columns );

		return $users_columns;
	}

	function get_sortable_columns() {
		return array(
			'username'   => 'login',
			'name'       => 'name',
			'email'      => 'email',
			'registered' => 'id',
		);
	}

	function display_rows() {
		global $current_site, $mode;

		$alt = '';
		$super_admins = get_super_admins();
		foreach ( $this->items as $user ) {
			$alt = ( 'alternate' == $alt ) ? '' : 'alternate';

			$status_list = array( 'spam' => 'site-spammed', 'deleted' => 'site-deleted' );

			foreach ( $status_list as $status => $col ) {
				if ( $user->$status )
					$alt .= " $col";
			}

			?>
			<tr class="<?php echo $alt; ?>">
			<?php

			list( $columns, $hidden ) = $this->get_column_info();

			foreach ( $columns as $column_name => $column_display_name ) :
				$class = "class='$column_name column-$column_name'";

				$style = '';
				if ( in_array( $column_name, $hidden ) )
					$style = ' style="display:none;"';

				$attributes = "$class$style";


				switch ( $column_name ) {
					case 'cb': ?>
						<th scope="row" class="check-column">
							<input type="checkbox" id="blog_<?php echo $user->ID ?>" name="allusers[]" value="<?php echo esc_attr( $user->ID ) ?>" />
						</th>
					<?php
					break;

					case 'username':
						$avatar	= get_avatar( $user->user_email, 32 );
						if ( get_current_user_id() == $user->ID ) {
							$edit_link = esc_url( network_admin_url( 'profile.php' ) );
						} else {
							$edit_link = esc_url( network_admin_url( add_query_arg( 'wp_http_referer', urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), 'user-edit.php?user_id=' . $user->ID ) ) );
						}

						echo "<td $attributes>"; ?>
							<?php echo $avatar; ?><strong><a href="<?php echo $edit_link; ?>" class="edit"><?php echo stripslashes( $user->user_login ); ?></a><?php
							if ( in_array( $user->user_login, $super_admins ) )
								echo ' - ' . __( 'Super Admin' );
							?></strong>
							<br/>
							<?php
								$actions = array();
								$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';

								if ( current_user_can( 'delete_user', $user->ID) && ! in_array( $user->user_login, $super_admins ) ) {
									$actions['delete'] = '<a href="' . $delete = esc_url( network_admin_url( add_query_arg( '_wp_http_referer', urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), wp_nonce_url( 'edit.php', 'deleteuser' ) . '&amp;action=deleteuser&amp;id=' . $user->ID ) ) ) . '" class="delete">' . __( 'Delete' ) . '</a>';
								}

								$actions = apply_filters( 'ms_user_row_actions', $actions, $user );
								echo $this->row_actions( $actions );
							?>
						</td>
					<?php
					break;

					case 'name':
						echo "<td $attributes>$user->first_name $user->last_name</td>";
					break;

					case 'email':
						echo "<td $attributes><a href='mailto:$user->user_email'>$user->user_email</a></td>";
					break;

					case 'registered':
						if ( 'list' == $mode )
							$date = 'Y/m/d';
						else
							$date = 'Y/m/d \<\b\r \/\> g:i:s a';

						echo "<td $attributes>" . mysql2date( $date, $user->user_registered ) . "</td>";
					break;

					case 'blogs':
						$blogs = get_blogs_of_user( $user->ID, true );
						echo "<td $attributes>";
							if ( is_array( $blogs ) ) {
								foreach ( (array) $blogs as $key => $val ) {
									if ( !can_edit_network( $val->site_id ) )
										continue;

									$path	= ( $val->path == '/' ) ? '' : $val->path;
									echo '<span class="site-' . $val->site_id . '" >';
									echo '<a href="'. esc_url( network_admin_url( 'site-info.php?id=' . $val->userblog_id ) ) .'">' . str_replace( '.' . $current_site->domain, '', $val->domain . $path ) . '</a>';
									echo ' <small class="row-actions">';
									$actions = array();
									$actions['edit'] = '<a href="'. esc_url( network_admin_url( 'site-info.php?id=' . $val->userblog_id ) ) .'">' . __( 'Edit' ) . '</a>';

									$class = '';
									if ( get_blog_status( $val->userblog_id, 'spam' ) == 1 )
										$class .= 'site-spammed ';
									if ( get_blog_status( $val->userblog_id, 'mature' ) == 1 )
										$class .= 'site-mature ';
									if ( get_blog_status( $val->userblog_id, 'deleted' ) == 1 )
										$class .= 'site-deleted ';
									if ( get_blog_status( $val->userblog_id, 'archived' ) == 1 )
										$class .= 'site-archived ';

									$actions['view'] = '<a class="' . $class . '" href="' .  esc_url( get_home_url( $val->userblog_id ) )  . '">' . __( 'View' ) . '</a>';

									$actions = apply_filters('ms_user_list_site_actions', $actions, $val->userblog_id);

									$i=0;
									$action_count = count( $actions );
									foreach ( $actions as $action => $link ) {
										++$i;
										( $i == $action_count ) ? $sep = '' : $sep = ' | ';
										echo "<span class='$action'>$link$sep</span>";
									}
									echo '</small></span><br/>';
								}
							}
							?>
						</td>
					<?php
					break;

					default:
						echo "<td $attributes>";
						echo apply_filters( 'manage_users_custom_column', '', $column_name, $user->ID );
						echo "</td>";
					break;
				}
			endforeach
			?>
			</tr>
			<?php
		}
	}
}

?>
