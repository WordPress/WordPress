<?php
/**
 * Multisite Users List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */
class WP_MS_Users_List_Table extends WP_List_Table {

	function WP_MS_Users_List_Table() {
		parent::WP_List_Table( array(
			'screen' => 'users-network',
		) );
	}

	function check_permissions() {
		if ( !is_multisite() )
			wp_die( __( 'Multisite support is not enabled.' ) );

		if ( ! current_user_can( 'manage_network_users' ) )
			wp_die( __( 'You do not have permission to access this page.' ) );
	}

	function prepare_items() {
		global $usersearch;

		$usersearch = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

		$users_per_page = $this->get_items_per_page( 'users_network_per_page' );

		$paged = $this->get_pagenum();

		$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged-1 ) * $users_per_page,
			'search' => $usersearch,
			'blog_id' => 0
		);

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
		$actions['delete'] = __( 'Delete' );
		$actions['spam'] = _x( 'Mark as Spam', 'user' );
		$actions['notspam'] = _x( 'Not Spam', 'user' );

		return $actions;
	}

	function no_items() {
		_e( 'No users found.' );
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
			'login'      => __( 'Login' ),
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
			'id'         => 'id',
			'login'      => 'login',
			'name'       => 'name',
			'email'      => 'email',
			'registered' => 'registered',
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
								echo ' - ' . __( 'Super admin' );
							?></strong>
							<br/>
							<?php
								$actions = array();
								$actions['edit'] = '<a href="' . esc_url( self_admin_url( $edit_link ) ) . '">' . __( 'Edit' ) . '</a>';

								if ( ! in_array( $user->user_login, $super_admins ) ) {
									$actions['delete'] = '<a href="' . $delete = esc_url( network_admin_url( add_query_arg( '_wp_http_referer', urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), wp_nonce_url( 'edit.php', 'deleteuser' ) . '&amp;action=deleteuser&amp;id=' . $user->ID ) ) ) . '" class="delete">' . __( 'Delete' ) . '</a>';
								}

								echo $this->row_actions( $actions );
							?>
							</div>
						</td>
					<?php
					break;

					case 'name': ?>
						<td class="name column-name"><?php echo $user->display_name ?></td>
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
						<td><?php do_action( 'manage_users_custom_column', $column_name, $user->ID ); ?></td>
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