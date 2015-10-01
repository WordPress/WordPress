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
	/**
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can( 'manage_network_users' );
	}

	/**
	 *
	 * @global string $usersearch
	 * @global string $role
	 * @global wpdb   $wpdb
	 * @global string $mode
	 */
	public function prepare_items() {
		global $usersearch, $role, $wpdb, $mode;

		$usersearch = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

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

		if ( wp_is_large_network( 'users' ) ) {
			$args['search'] = ltrim( $args['search'], '*' );
		} else if ( '' !== $args['search'] ) {
			$args['search'] = trim( $args['search'], '*' );
			$args['search'] = '*' . $args['search'] . '*';
		}

		if ( $role === 'super' ) {
			$logins = implode( "', '", get_super_admins() );
			$args['include'] = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE user_login IN ('$logins')" );
		}

		/*
		 * If the network is large and a search is not being performed,
		 * show only the latest users with no paging in order to avoid
		 * expensive count queries.
		 */
		if ( !$usersearch && wp_is_large_network( 'users' ) ) {
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

	/**
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		$actions = array();
		if ( current_user_can( 'delete_users' ) )
			$actions['delete'] = __( 'Delete' );
		$actions['spam'] = _x( 'Mark as Spam', 'user' );
		$actions['notspam'] = _x( 'Not Spam', 'user' );

		return $actions;
	}

	/**
	 * @access public
	 */
	public function no_items() {
		_e( 'No users found.' );
	}

	/**
	 *
	 * @global string $role
	 * @return array
	 */
	protected function get_views() {
		global $role;

		$total_users = get_user_count();
		$super_admins = get_super_admins();
		$total_admins = count( $super_admins );

		$class = $role != 'super' ? ' class="current"' : '';
		$role_links = array();
		$role_links['all'] = "<a href='" . network_admin_url('users.php') . "'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_users, 'users' ), number_format_i18n( $total_users ) ) . '</a>';
		$class = $role === 'super' ? ' class="current"' : '';
		$role_links['super'] = "<a href='" . network_admin_url('users.php?role=super') . "'$class>" . sprintf( _n( 'Super Admin <span class="count">(%s)</span>', 'Super Admins <span class="count">(%s)</span>', $total_admins ), number_format_i18n( $total_admins ) ) . '</a>';

		return $role_links;
	}

	/**
	 * @global string $mode
	 * @param string $which
	 */
	protected function pagination( $which ) {
		global $mode;

		parent::pagination ( $which );

		if ( 'top' === $which ) {
			$this->view_switcher( $mode );
		}
	}

	/**
	 *
	 * @return array
	 */
	public function get_columns() {
		$users_columns = array(
			'cb'         => '<input type="checkbox" />',
			'username'   => __( 'Username' ),
			'name'       => __( 'Name' ),
			'email'      => __( 'Email' ),
			'registered' => _x( 'Registered', 'user' ),
			'blogs'      => __( 'Sites' )
		);
		/**
		 * Filter the columns displayed in the Network Admin Users list table.
		 *
		 * @since MU
		 *
		 * @param array $users_columns An array of user columns. Default 'cb', 'username',
		 *                             'name', 'email', 'registered', 'blogs'.
		 */
		return apply_filters( 'wpmu_users_columns', $users_columns );
	}

	/**
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'username'   => 'login',
			'name'       => 'name',
			'email'      => 'email',
			'registered' => 'id',
		);
	}

	/**
	 * Handles the checkbox column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_User $user The current WP_User object.
	 */
	public function column_cb( $user ) {
		if ( is_super_admin( $user->ID ) ) {
			return;
		}
		?>
		<label class="screen-reader-text" for="blog_<?php echo $user->ID; ?>"><?php echo sprintf( __( 'Select %s' ), $user->user_login ); ?></label>
		<input type="checkbox" id="blog_<?php echo $user->ID ?>" name="allusers[]" value="<?php echo esc_attr( $user->ID ) ?>" />
		<?php
	}

	/**
	 * Handles the ID column output.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param WP_User $user The current WP_User object.
	 */
	public function column_id( $user ) {
		echo $user->ID;
	}

	/**
	 * Handles the username column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_User $user The current WP_User object.
	 */
	public function column_username( $user ) {
		$super_admins = get_super_admins();
		$avatar	= get_avatar( $user->user_email, 32 );
		$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user->ID ) ) );

		echo $avatar;

		?><strong><a href="<?php echo $edit_link; ?>" class="edit"><?php echo $user->user_login; ?></a><?php
		if ( in_array( $user->user_login, $super_admins ) ) {
			echo ' - ' . __( 'Super Admin' );
		}
		?></strong>
	<?php
	}

	/**
	 * Handles the name column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_User $user The current WP_User object.
	 */
	public function column_name( $user ) {
		echo "$user->first_name $user->last_name";
	}

	/**
	 * Handles the email column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_User $user The current WP_User object.
	 */
	public function column_email( $user ) {
		echo "<a href='" . esc_url( "mailto:$user->user_email" ) . "'>$user->user_email</a>";
	}

	/**
	 * Handles the registered date column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @global string $mode
	 *
	 * @param WP_User $user The current WP_User object.
	 */
	public function column_registered( $user ) {
		global $mode;
		if ( 'list' === $mode ) {
			$date = __( 'Y/m/d' );
		} else {
			$date = __( 'Y/m/d g:i:s a' );
		}
		echo mysql2date( $date, $user->user_registered );
	}

	/**
	 * @since 4.3.0
	 * @access protected
	 *
	 * @param WP_User $user
	 * @param string  $classes
	 * @param string  $data
	 * @param string  $primary
	 */
	protected function _column_blogs( $user, $classes, $data, $primary ) {
		echo '<td class="', $classes, ' has-row-actions" ', $data, '>';
		echo $this->column_blogs( $user );
		echo $this->handle_row_actions( $user, 'blogs', $primary );
		echo '</td>';
	}

	/**
	 * Handles the blogs/sites column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_User $user The current WP_User object.
	 */
	public function column_blogs( $user ) {
		$blogs = get_blogs_of_user( $user->ID, true );
		if ( ! is_array( $blogs ) ) {
			return;
		}

		foreach ( $blogs as $val ) {
			if ( ! can_edit_network( $val->site_id ) ) {
				continue;
			}

			$path	= ( $val->path === '/' ) ? '' : $val->path;
			echo '<span class="site-' . $val->site_id . '" >';
			echo '<a href="'. esc_url( network_admin_url( 'site-info.php?id=' . $val->userblog_id ) ) .'">' . str_replace( '.' . get_current_site()->domain, '', $val->domain . $path ) . '</a>';
			echo ' <small class="row-actions">';
			$actions = array();
			$actions['edit'] = '<a href="'. esc_url( network_admin_url( 'site-info.php?id=' . $val->userblog_id ) ) .'">' . __( 'Edit' ) . '</a>';

			$class = '';
			if ( $val->spam == 1 ) {
				$class .= 'site-spammed ';
			}
			if ( $val->mature == 1 ) {
				$class .= 'site-mature ';
			}
			if ( $val->deleted == 1 ) {
				$class .= 'site-deleted ';
			}
			if ( $val->archived == 1 ) {
				$class .= 'site-archived ';
			}

			$actions['view'] = '<a class="' . $class . '" href="' . esc_url( get_home_url( $val->userblog_id ) ) . '">' . __( 'View' ) . '</a>';

			/**
			 * Filter the action links displayed next the sites a user belongs to
			 * in the Network Admin Users list table.
			 *
			 * @since 3.1.0
			 *
			 * @param array $actions     An array of action links to be displayed.
			 *                           Default 'Edit', 'View'.
			 * @param int   $userblog_id The site ID.
			 */
			$actions = apply_filters( 'ms_user_list_site_actions', $actions, $val->userblog_id );

			$i=0;
			$action_count = count( $actions );
			foreach ( $actions as $action => $link ) {
				++$i;
				$sep = ( $i == $action_count ) ? '' : ' | ';
				echo "<span class='$action'>$link$sep</span>";
			}
			echo '</small></span><br/>';
		}
	}

	/**
	 * Handles the default column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_User $user       The current WP_User object.
	 * @param string $column_name The current column name.
	 */
	public function column_default( $user, $column_name ) {
		/** This filter is documented in wp-admin/includes/class-wp-users-list-table.php */
		echo apply_filters( 'manage_users_custom_column', '', $column_name, $user->ID );
	}

	public function display_rows() {
		foreach ( $this->items as $user ) {
			$class = '';

			$status_list = array( 'spam' => 'site-spammed', 'deleted' => 'site-deleted' );

			foreach ( $status_list as $status => $col ) {
				if ( $user->$status ) {
					$class .= " $col";
				}
			}

			?>
			<tr class="<?php echo trim( $class ); ?>">
				<?php $this->single_row_columns( $user ); ?>
			</tr>
			<?php
		}
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @return string Name of the default primary column, in this case, 'username'.
	 */
	protected function get_default_primary_column_name() {
		return 'username';
	}

	/**
	 * Generates and displays row action links.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @param object $user        User being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string Row actions output for users in Multisite.
	 */
	protected function handle_row_actions( $user, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		$super_admins = get_super_admins();
		$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user->ID ) ) );

		$actions = array();
		$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';

		if ( current_user_can( 'delete_user', $user->ID ) && ! in_array( $user->user_login, $super_admins ) ) {
			$actions['delete'] = '<a href="' . $delete = esc_url( network_admin_url( add_query_arg( '_wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), wp_nonce_url( 'users.php', 'deleteuser' ) . '&amp;action=deleteuser&amp;id=' . $user->ID ) ) ) . '" class="delete">' . __( 'Delete' ) . '</a>';
		}

		/**
		 * Filter the action links displayed under each user in the Network Admin Users list table.
		 *
		 * @since 3.2.0
		 *
		 * @param array   $actions An array of action links to be displayed.
		 *                         Default 'Edit', 'Delete'.
		 * @param WP_User $user    WP_User object.
		 */
		$actions = apply_filters( 'ms_user_row_actions', $actions, $user );
		return $this->row_actions( $actions );
	}
}
