<?php
/**
 * List Table API: WP_MS_Users_List_Table class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

/**
 * Core class used to implement displaying users in a list table for the network admin.
 *
 * @since 3.1.0
 * @access private
 *
 * @see WP_List_Table
 */
class WP_MS_Users_List_Table extends WP_List_Table {
	/**
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can( 'manage_network_users' );
	}

	/**
	 * @global string $mode       List table view mode.
	 * @global string $usersearch
	 * @global string $role
	 */
	public function prepare_items() {
		global $mode, $usersearch, $role;

		if ( ! empty( $_REQUEST['mode'] ) ) {
			$mode = 'excerpt' === $_REQUEST['mode'] ? 'excerpt' : 'list';
			set_user_setting( 'network_users_list_mode', $mode );
		} else {
			$mode = get_user_setting( 'network_users_list_mode', 'list' );
		}

		$usersearch = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

		$users_per_page = $this->get_items_per_page( 'users_network_per_page' );

		$role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : '';

		$paged = $this->get_pagenum();

		$args = array(
			'number'  => $users_per_page,
			'offset'  => ( $paged - 1 ) * $users_per_page,
			'search'  => $usersearch,
			'blog_id' => 0,
			'fields'  => 'all_with_meta',
		);

		if ( wp_is_large_network( 'users' ) ) {
			$args['search'] = ltrim( $args['search'], '*' );
		} elseif ( '' !== $args['search'] ) {
			$args['search'] = trim( $args['search'], '*' );
			$args['search'] = '*' . $args['search'] . '*';
		}

		if ( 'super' === $role ) {
			$args['login__in'] = get_super_admins();
		}

		/*
		 * If the network is large and a search is not being performed,
		 * show only the latest users with no paging in order to avoid
		 * expensive count queries.
		 */
		if ( ! $usersearch && wp_is_large_network( 'users' ) ) {
			if ( ! isset( $_REQUEST['orderby'] ) ) {
				$_GET['orderby']     = 'id';
				$_REQUEST['orderby'] = 'id';
			}
			if ( ! isset( $_REQUEST['order'] ) ) {
				$_GET['order']     = 'DESC';
				$_REQUEST['order'] = 'DESC';
			}
			$args['count_total'] = false;
		}

		if ( isset( $_REQUEST['orderby'] ) ) {
			$args['orderby'] = $_REQUEST['orderby'];
		}

		if ( isset( $_REQUEST['order'] ) ) {
			$args['order'] = $_REQUEST['order'];
		}

		/** This filter is documented in wp-admin/includes/class-wp-users-list-table.php */
		$args = apply_filters( 'users_list_table_query_args', $args );

		// Query the user IDs for this page.
		$wp_user_search = new WP_User_Query( $args );

		$this->items = $wp_user_search->get_results();

		$this->set_pagination_args(
			array(
				'total_items' => $wp_user_search->get_total(),
				'per_page'    => $users_per_page,
			)
		);
	}

	/**
	 * @return array
	 */
	protected function get_bulk_actions() {
		$actions = array();
		if ( current_user_can( 'delete_users' ) ) {
			$actions['delete'] = __( 'Delete' );
		}
		$actions['spam']    = _x( 'Mark as spam', 'user' );
		$actions['notspam'] = _x( 'Not spam', 'user' );

		return $actions;
	}

	/**
	 */
	public function no_items() {
		_e( 'No users found.' );
	}

	/**
	 * @global string $role
	 * @return array
	 */
	protected function get_views() {
		global $role;

		$total_users  = get_user_count();
		$super_admins = get_super_admins();
		$total_admins = count( $super_admins );

		$current_link_attributes = 'super' !== $role ? ' class="current" aria-current="page"' : '';
		$role_links              = array();
		$role_links['all']       = sprintf(
			'<a href="%s"%s>%s</a>',
			network_admin_url( 'users.php' ),
			$current_link_attributes,
			sprintf(
				/* translators: Number of users. */
				_nx(
					'All <span class="count">(%s)</span>',
					'All <span class="count">(%s)</span>',
					$total_users,
					'users'
				),
				number_format_i18n( $total_users )
			)
		);
		$current_link_attributes = 'super' === $role ? ' class="current" aria-current="page"' : '';
		$role_links['super']     = sprintf(
			'<a href="%s"%s>%s</a>',
			network_admin_url( 'users.php?role=super' ),
			$current_link_attributes,
			sprintf(
				/* translators: Number of users. */
				_n(
					'Super Admin <span class="count">(%s)</span>',
					'Super Admins <span class="count">(%s)</span>',
					$total_admins
				),
				number_format_i18n( $total_admins )
			)
		);

		return $role_links;
	}

	/**
	 * @global string $mode List table view mode.
	 *
	 * @param string $which
	 */
	protected function pagination( $which ) {
		global $mode;

		parent::pagination( $which );

		if ( 'top' === $which ) {
			$this->view_switcher( $mode );
		}
	}

	/**
	 * @return array
	 */
	public function get_columns() {
		$users_columns = array(
			'cb'         => '<input type="checkbox" />',
			'username'   => __( 'Username' ),
			'name'       => __( 'Name' ),
			'email'      => __( 'Email' ),
			'registered' => _x( 'Registered', 'user' ),
			'blogs'      => __( 'Sites' ),
		);
		/**
		 * Filters the columns displayed in the Network Admin Users list table.
		 *
		 * @since MU (3.0.0)
		 *
		 * @param string[] $users_columns An array of user columns. Default 'cb', 'username',
		 *                                'name', 'email', 'registered', 'blogs'.
		 */
		return apply_filters( 'wpmu_users_columns', $users_columns );
	}

	/**
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
	 * @since 5.9.0 Renamed `$user` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param WP_User $item The current WP_User object.
	 */
	public function column_cb( $item ) {
		// Restores the more descriptive, specific name for use within this method.
		$user = $item;

		if ( is_super_admin( $user->ID ) ) {
			return;
		}
		?>
		<label class="screen-reader-text" for="blog_<?php echo $user->ID; ?>">
			<?php
			/* translators: %s: User login. */
			printf( __( 'Select %s' ), $user->user_login );
			?>
		</label>
		<input type="checkbox" id="blog_<?php echo $user->ID; ?>" name="allusers[]" value="<?php echo esc_attr( $user->ID ); ?>" />
		<?php
	}

	/**
	 * Handles the ID column output.
	 *
	 * @since 4.4.0
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
	 *
	 * @param WP_User $user The current WP_User object.
	 */
	public function column_username( $user ) {
		$super_admins = get_super_admins();
		$avatar       = get_avatar( $user->user_email, 32 );

		echo $avatar;

		if ( current_user_can( 'edit_user', $user->ID ) ) {
			$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user->ID ) ) );
			$edit      = "<a href=\"{$edit_link}\">{$user->user_login}</a>";
		} else {
			$edit = $user->user_login;
		}

		?>
		<strong>
			<?php
			echo $edit;

			if ( in_array( $user->user_login, $super_admins, true ) ) {
				echo ' &mdash; ' . __( 'Super Admin' );
			}
			?>
		</strong>
		<?php
	}

	/**
	 * Handles the name column output.
	 *
	 * @since 4.3.0
	 *
	 * @param WP_User $user The current WP_User object.
	 */
	public function column_name( $user ) {
		if ( $user->first_name && $user->last_name ) {
			printf(
				/* translators: 1: User's first name, 2: Last name. */
				_x( '%1$s %2$s', 'Display name based on first name and last name' ),
				$user->first_name,
				$user->last_name
			);
		} elseif ( $user->first_name ) {
			echo $user->first_name;
		} elseif ( $user->last_name ) {
			echo $user->last_name;
		} else {
			echo '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">' . _x( 'Unknown', 'name' ) . '</span>';
		}
	}

	/**
	 * Handles the email column output.
	 *
	 * @since 4.3.0
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
	 *
	 * @global string $mode List table view mode.
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
	 * Handles the sites column output.
	 *
	 * @since 4.3.0
	 *
	 * @param WP_User $user The current WP_User object.
	 */
	public function column_blogs( $user ) {
		$blogs = get_blogs_of_user( $user->ID, true );
		if ( ! is_array( $blogs ) ) {
			return;
		}

		foreach ( $blogs as $site ) {
			if ( ! can_edit_network( $site->site_id ) ) {
				continue;
			}

			$path         = ( '/' === $site->path ) ? '' : $site->path;
			$site_classes = array( 'site-' . $site->site_id );
			/**
			 * Filters the span class for a site listing on the mulisite user list table.
			 *
			 * @since 5.2.0
			 *
			 * @param string[] $site_classes Array of class names used within the span tag. Default "site-#" with the site's network ID.
			 * @param int      $site_id      Site ID.
			 * @param int      $network_id   Network ID.
			 * @param WP_User  $user         WP_User object.
			 */
			$site_classes = apply_filters( 'ms_user_list_site_class', $site_classes, $site->userblog_id, $site->site_id, $user );
			if ( is_array( $site_classes ) && ! empty( $site_classes ) ) {
				$site_classes = array_map( 'sanitize_html_class', array_unique( $site_classes ) );
				echo '<span class="' . esc_attr( implode( ' ', $site_classes ) ) . '">';
			} else {
				echo '<span>';
			}
			echo '<a href="' . esc_url( network_admin_url( 'site-info.php?id=' . $site->userblog_id ) ) . '">' . str_replace( '.' . get_network()->domain, '', $site->domain . $path ) . '</a>';
			echo ' <small class="row-actions">';
			$actions         = array();
			$actions['edit'] = '<a href="' . esc_url( network_admin_url( 'site-info.php?id=' . $site->userblog_id ) ) . '">' . __( 'Edit' ) . '</a>';

			$class = '';
			if ( 1 === (int) $site->spam ) {
				$class .= 'site-spammed ';
			}
			if ( 1 === (int) $site->mature ) {
				$class .= 'site-mature ';
			}
			if ( 1 === (int) $site->deleted ) {
				$class .= 'site-deleted ';
			}
			if ( 1 === (int) $site->archived ) {
				$class .= 'site-archived ';
			}

			$actions['view'] = '<a class="' . $class . '" href="' . esc_url( get_home_url( $site->userblog_id ) ) . '">' . __( 'View' ) . '</a>';

			/**
			 * Filters the action links displayed next the sites a user belongs to
			 * in the Network Admin Users list table.
			 *
			 * @since 3.1.0
			 *
			 * @param string[] $actions     An array of action links to be displayed. Default 'Edit', 'View'.
			 * @param int      $userblog_id The site ID.
			 */
			$actions = apply_filters( 'ms_user_list_site_actions', $actions, $site->userblog_id );

			$action_count = count( $actions );

			$i = 0;

			foreach ( $actions as $action => $link ) {
				++$i;

				$sep = ( $i < $action_count ) ? ' | ' : '';

				echo "<span class='$action'>$link$sep</span>";
			}

			echo '</small></span><br/>';
		}
	}

	/**
	 * Handles the default column output.
	 *
	 * @since 4.3.0
	 * @since 5.9.0 Renamed `$user` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param WP_User $item        The current WP_User object.
	 * @param string  $column_name The current column name.
	 */
	public function column_default( $item, $column_name ) {
		/** This filter is documented in wp-admin/includes/class-wp-users-list-table.php */
		echo apply_filters(
			'manage_users_custom_column',
			'', // Custom column output. Default empty.
			$column_name,
			$item->ID // User ID.
		);
	}

	public function display_rows() {
		foreach ( $this->items as $user ) {
			$class = '';

			$status_list = array(
				'spam'    => 'site-spammed',
				'deleted' => 'site-deleted',
			);

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
	 * @since 5.9.0 Renamed `$user` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param WP_User $item        User being acted upon.
	 * @param string  $column_name Current column name.
	 * @param string  $primary     Primary column name.
	 * @return string Row actions output for users in Multisite, or an empty string
	 *                if the current column is not the primary column.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		// Restores the more descriptive, specific name for use within this method.
		$user         = $item;
		$super_admins = get_super_admins();

		$actions = array();

		if ( current_user_can( 'edit_user', $user->ID ) ) {
			$edit_link       = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user->ID ) ) );
			$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
		}

		if ( current_user_can( 'delete_user', $user->ID ) && ! in_array( $user->user_login, $super_admins, true ) ) {
			$actions['delete'] = '<a href="' . esc_url( network_admin_url( add_query_arg( '_wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), wp_nonce_url( 'users.php', 'deleteuser' ) . '&amp;action=deleteuser&amp;id=' . $user->ID ) ) ) . '" class="delete">' . __( 'Delete' ) . '</a>';
		}

		/**
		 * Filters the action links displayed under each user in the Network Admin Users list table.
		 *
		 * @since 3.2.0
		 *
		 * @param string[] $actions An array of action links to be displayed. Default 'Edit', 'Delete'.
		 * @param WP_User  $user    WP_User object.
		 */
		$actions = apply_filters( 'ms_user_row_actions', $actions, $user );

		return $this->row_actions( $actions );
	}
}
