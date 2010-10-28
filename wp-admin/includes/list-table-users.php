<?php
/**
 * Users List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */
class WP_Users_Table extends WP_List_Table {

	function WP_Users_Table() {
		parent::WP_List_Table( array(
			'screen' => 'users',
			'plural' => 'users'
		) );
	}

	function check_permissions() {
		if ( !current_user_can('list_users') )
			wp_die(__('Cheatin&#8217; uh?'));
	}

	function prepare_items() {
		global $role, $usersearch;

		$usersearch = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

		$role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : '';

		$users_per_page = $this->get_items_per_page( 'users_per_page' );

		$paged = $this->get_pagenum();

		$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged-1 ) * $users_per_page,
			'role' => $role,
			'search' => $usersearch
		);

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

	function no_items() {
		_e( 'No matching users were found.' );
	}

	function get_views() {
		global $wp_roles, $role;

		$users_of_blog = count_users();
		$total_users = $users_of_blog['total_users'];
		$avail_roles =& $users_of_blog['avail_roles'];
		unset($users_of_blog);

		$current_role = false;
		$class = empty($role) ? ' class="current"' : '';
		$role_links = array();
		$role_links['all'] = "<li><a href='users.php'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_users, 'users' ), number_format_i18n( $total_users ) ) . '</a>';
		foreach ( $wp_roles->get_names() as $this_role => $name ) {
			if ( !isset($avail_roles[$this_role]) )
				continue;

			$class = '';

			if ( $this_role == $role ) {
				$current_role = $role;
				$class = ' class="current"';
			}

			$name = translate_user_role( $name );
			/* translators: User role name with count */
			$name = sprintf( __('%1$s <span class="count">(%2$s)</span>'), $name, $avail_roles[$this_role] );
			$role_links[$this_role] = "<li><a href='users.php?role=$this_role'$class>$name</a>";
		}

		return $role_links;
	}

	function get_bulk_actions() {
		$actions = array();

		if ( !is_multisite() && current_user_can( 'delete_users' ) )
			$actions['delete'] = __( 'Delete' );
		else
			$actions['remove'] = __( 'Remove' );

		return $actions;
	}

	function extra_tablenav( $which ) {
		if ( 'top' != $which )
			return;
?>
	<div class="alignleft actions">
		<label class="screen-reader-text" for="new_role"><?php _e( 'Change role to&hellip;' ) ?></label>
		<select name="new_role" id="new_role">
			<option value=''><?php _e( 'Change role to&hellip;' ) ?></option>
			<?php wp_dropdown_roles(); ?>
		</select>
		<?php submit_button( __( 'Change' ), 'secondary', 'changeit', false ); ?>
	</div>
<?php
	}

	function current_action() {
		if ( isset($_REQUEST['changeit']) && !empty($_REQUEST['new_role']) )
			return 'promote';

		return parent::current_action();
	}

	function get_columns() {
		return array(
			'cb'       => '<input type="checkbox" />',
			'username' => __( 'Login' ),
			'name'     => __( 'Name' ),
			'email'    => __( 'E-mail' ),
			'role'     => __( 'Role' ),
			'posts'    => __( 'Posts' )
		);
	}

	function get_sortable_columns() {
		return array(
			'username' => 'login',
			'name'     => 'name',
			'email'    => 'email',
			'posts'    => 'post_count',
		);
	}

	function display_rows() {
		// Query the post counts for this page
		$post_counts = count_many_users_posts( array_keys( $this->items ) );

		$style = '';
		foreach ( $this->items as $userid => $user_object ) {
			$role = reset( $user_object->roles );

			if ( is_multisite() && empty( $role ) )
				continue;

			$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
			echo "\n\t", $this->single_row( $user_object, $style, $role, $post_counts[ $userid ] );
		}
	}

	/**
	 * Generate HTML for a single row on the users.php admin panel.
	 *
	 * @since 2.1.0
	 *
	 * @param object $user_object
	 * @param string $style Optional. Attributes added to the TR element.  Must be sanitized.
	 * @param string $role Key for the $wp_roles array.
	 * @param int $numposts Optional. Post count to display for this user.  Defaults to zero, as in, a new user has made zero posts.
	 * @return string
	 */
	function single_row( $user_object, $style = '', $role = '', $numposts = 0 ) {
		global $wp_roles;

		if ( !( is_object( $user_object ) && is_a( $user_object, 'WP_User' ) ) )
			$user_object = new WP_User( (int) $user_object );
		$user_object = sanitize_user_object( $user_object, 'display' );
		$email = $user_object->user_email;
		$url = $user_object->user_url;
		$short_url = str_replace( 'http://', '', $url );
		$short_url = str_replace( 'www.', '', $short_url );
		if ( '/' == substr( $short_url, -1 ) )
			$short_url = substr( $short_url, 0, -1 );
		if ( strlen( $short_url ) > 35 )
			$short_url = substr( $short_url, 0, 32 ).'...';
		$checkbox = '';
		// Check if the user for this row is editable
		if ( current_user_can( 'list_users' ) ) {
			// Set up the user editing link
			// TODO: make profile/user-edit determination a separate function
			if ( get_current_user_id() == $user_object->ID ) {
				$edit_link = 'profile.php';
			} else {
				$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( esc_url( stripslashes( $_SERVER['REQUEST_URI'] ) ) ), "user-edit.php?user_id=$user_object->ID" ) );
			}
			$edit = "<strong><a href=\"$edit_link\">$user_object->user_login</a></strong><br />";

			// Set up the hover actions for this user
			$actions = array();

			if ( current_user_can( 'edit_user',  $user_object->ID ) ) {
				$edit = "<strong><a href=\"$edit_link\">$user_object->user_login</a></strong><br />";
				$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
			} else {
				$edit = "<strong>$user_object->user_login</strong><br />";
			}

			if ( !is_multisite() && get_current_user_id() != $user_object->ID && current_user_can( 'delete_user', $user_object->ID ) )
				$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url( "users.php?action=delete&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Delete' ) . "</a>";
			if ( is_multisite() && get_current_user_id() != $user_object->ID && current_user_can( 'remove_user', $user_object->ID ) )
				$actions['remove'] = "<a class='submitdelete' href='" . wp_nonce_url( "users.php?action=remove&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Remove' ) . "</a>";
			$actions = apply_filters( 'user_row_actions', $actions, $user_object );
			$edit .= $this->row_actions( $actions );

			// Set up the checkbox ( because the user is editable, otherwise its empty )
			$checkbox = "<input type='checkbox' name='users[]' id='user_{$user_object->ID}' class='$role' value='{$user_object->ID}' />";

		} else {
			$edit = '<strong>' . $user_object->user_login . '</strong>';
		}
		$role_name = isset( $wp_roles->role_names[$role] ) ? translate_user_role( $wp_roles->role_names[$role] ) : __( 'None' );
		$r = "<tr id='user-$user_object->ID'$style>";
		$avatar = get_avatar( $user_object->ID, 32 );

		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";

			switch ( $column_name ) {
				case 'cb':
					$r .= "<th scope='row' class='check-column'>$checkbox</th>";
					break;
				case 'username':
					$r .= "<td $attributes>$avatar $edit</td>";
					break;
				case 'name':
					$r .= "<td $attributes>$user_object->first_name $user_object->last_name</td>";
					break;
				case 'email':
					$r .= "<td $attributes><a href='mailto:$email' title='" . sprintf( __( 'E-mail: %s' ), $email ) . "'>$email</a></td>";
					break;
				case 'role':
					$r .= "<td $attributes>$role_name</td>";
					break;
				case 'posts':
					$attributes = 'class="posts column-posts num"' . $style;
					$r .= "<td $attributes>";
					if ( $numposts > 0 ) {
						$r .= "<a href='edit.php?author=$user_object->ID' title='" . __( 'View posts by this author' ) . "' class='edit'>";
						$r .= $numposts;
						$r .= '</a>';
					} else {
						$r .= 0;
					}
					$r .= "</td>";
					break;
				default:
					$r .= "<td $attributes>";
					$r .= apply_filters( 'manage_users_custom_column', '', $column_name, $user_object->ID );
					$r .= "</td>";
			}
		}
		$r .= '</tr>';

		return $r;
	}
}

?>