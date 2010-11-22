<?php
/**
 * Multisite Users List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */
class WP_MS_Users_List_Table extends WP_List_Table {

	function check_permissions() {
		if ( !is_multisite() )
			wp_die( __( 'Multisite support is not enabled.' ) );

		if ( ! current_user_can( 'manage_network_users' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );
	}

	function prepare_items() {
		global $usersearch, $role, $wpdb;

		$usersearch = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

		$users_per_page = $this->get_items_per_page( 'users_network_per_page' );

		$role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : '';

		$paged = $this->get_pagenum();

		$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged-1 ) * $users_per_page,
			'search' => $usersearch,
			'blog_id' => 0
		);

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

		$users_of_blog = count_users();
		$total_users = $users_of_blog['total_users'];
		$super_admins = get_super_admins();
		$total_admins = count( $super_admins );
		unset($users_of_blog);

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
			'login'      => __( 'Username' ),
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
			'login'      => 'login',
			'name'       => 'name',
			'email'      => 'email',
			'registered' => 'id',
		);
	}

	function display_rows() {
		global $current_site, $mode;

		$class = '';
		$super_admins = get_super_admins();
		foreach ( $this->items as $user ) {
			$class = ( 'alternate' == $class ) ? '' : 'alternate';

			$status_list = array( 'spam' => 'site-spammed', 'deleted' => 'site-deleted' );

			foreach ( $status_list as $status => $col ) {
				if ( $user->$status )
					$class = $col;
			}

			?>
			<tr class="<?php echo $class; ?>">
			<?php

			list( $columns, $hidden ) = $this->get_column_info();

			foreach ( $columns as $column_name => $column_display_name ) :
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

					case 'id': ?>
						<th valign="top" scope="row">
							<?php echo $user->ID ?>
						</th>
					<?php
					break;

					case 'login':
						$avatar	= get_avatar( $user->user_email, 32 );
						$edit_link = ( get_current_user_id() == $user->ID ) ? 'profile.php' : 'user-edit.php?user_id=' . $user->ID;
						?>
						<td class="username column-username">
							<?php echo $avatar; ?><strong><a href="<?php echo esc_url( self_admin_url( $edit_link ) ); ?>" class="edit"><?php echo stripslashes( $user->user_login ); ?></a><?php
							if ( in_array( $user->user_login, $super_admins ) )
								echo ' - ' . __( 'Super Admin' );
							?></strong>
							<br/>
							<?php
								$actions = array();
								$actions['edit'] = '<a href="' . esc_url( self_admin_url( $edit_link ) ) . '">' . __( 'Edit' ) . '</a>';

								if ( current_user_can( 'delete_user', $user->ID) && ! in_array( $user->user_login, $super_admins ) ) {
									$actions['delete'] = '<a href="' . $delete = esc_url( network_admin_url( add_query_arg( '_wp_http_referer', urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), wp_nonce_url( 'edit.php', 'deleteuser' ) . '&amp;action=deleteuser&amp;id=' . $user->ID ) ) ) . '" class="delete">' . __( 'Delete' ) . '</a>';
								}

								echo $this->row_actions( $actions );
							?>
							</div>
						</td>
					<?php
					break;

					case 'name': ?>
						<td class="name column-name"><?php echo "$user->first_name $user->last_name"; ?></td>
					<?php
					break;

					case 'email': ?>
						<td class="email column-email"><a href="mailto:<?php echo $user->user_email ?>"><?php echo $user->user_email ?></a></td>
					<?php
					break;

					case 'registered':
						if ( 'list' == $mode )
							$date = 'Y/m/d';
						else
							$date = 'Y/m/d \<\b\r \/\> g:i:s a';
					?>
						<td><?php echo mysql2date( $date, $user->user_registered ); ?></td>
					<?php
					break;

					case 'blogs':
						$blogs = get_blogs_of_user( $user->ID, true );
						?>
						<td>
							<?php
							if ( is_array( $blogs ) ) {
								foreach ( (array) $blogs as $key => $val ) {
									$path	= ( $val->path == '/' ) ? '' : $val->path;
									echo '<a href="'. esc_url( network_admin_url( 'site-info.php?id=' . $val->userblog_id ) ) .'">' . str_replace( '.' . $current_site->domain, '', $val->domain . $path ) . '</a>';
									echo ' <small class="row-actions">';

									// Edit
									echo '<a href="'. esc_url( network_admin_url( 'site-info.php?id=' . $val->userblog_id ) ) .'">' . __( 'Edit' ) . '</a> | ';

									// View
									echo '<a ';
									if ( get_blog_status( $val->userblog_id, 'spam' ) == 1 )
										echo 'style="background-color: #faa" ';
									echo 'href="' .  esc_url( get_home_url( $val->userblog_id ) )  . '">' . __( 'View' ) . '</a>';

									echo '</small><br />';
								}
							}
							?>
						</td>
					<?php
					break;

					default: ?>
						<td><?php echo apply_filters( 'manage_users_custom_column', '', $column_name, $user->ID ); ?></td>
					<?php
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
