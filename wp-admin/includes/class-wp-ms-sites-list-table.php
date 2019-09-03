<?php
/**
 * List Table API: WP_MS_Sites_List_Table class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

/**
 * Core class used to implement displaying sites in a list table for the network admin.
 *
 * @since 3.1.0
 * @access private
 *
 * @see WP_List_Table
 */
class WP_MS_Sites_List_Table extends WP_List_Table {

	/**
	 * Site status list.
	 *
	 * @since 4.3.0
	 * @var array
	 */
	public $status_list;

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		$this->status_list = array(
			'archived' => array( 'site-archived', __( 'Archived' ) ),
			'spam'     => array( 'site-spammed', _x( 'Spam', 'site' ) ),
			'deleted'  => array( 'site-deleted', __( 'Deleted' ) ),
			'mature'   => array( 'site-mature', __( 'Mature' ) ),
		);

		parent::__construct(
			array(
				'plural' => 'sites',
				'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			)
		);
	}

	/**
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can( 'manage_sites' );
	}

	/**
	 * Prepares the list of sites for display.
	 *
	 * @since 3.1.0
	 *
	 * @global string $s
	 * @global string $mode
	 * @global wpdb   $wpdb WordPress database abstraction object.
	 */
	public function prepare_items() {
		global $s, $mode, $wpdb;

		if ( ! empty( $_REQUEST['mode'] ) ) {
			$mode = $_REQUEST['mode'] === 'excerpt' ? 'excerpt' : 'list';
			set_user_setting( 'sites_list_mode', $mode );
		} else {
			$mode = get_user_setting( 'sites_list_mode', 'list' );
		}

		$per_page = $this->get_items_per_page( 'sites_network_per_page' );

		$pagenum = $this->get_pagenum();

		$s    = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
		$wild = '';
		if ( false !== strpos( $s, '*' ) ) {
			$wild = '*';
			$s    = trim( $s, '*' );
		}

		/*
		 * If the network is large and a search is not being performed, show only
		 * the latest sites with no paging in order to avoid expensive count queries.
		 */
		if ( ! $s && wp_is_large_network() ) {
			if ( ! isset( $_REQUEST['orderby'] ) ) {
				$_GET['orderby']     = '';
				$_REQUEST['orderby'] = '';
			}
			if ( ! isset( $_REQUEST['order'] ) ) {
				$_GET['order']     = 'DESC';
				$_REQUEST['order'] = 'DESC';
			}
		}

		$args = array(
			'number'     => intval( $per_page ),
			'offset'     => intval( ( $pagenum - 1 ) * $per_page ),
			'network_id' => get_current_network_id(),
		);

		if ( empty( $s ) ) {
			// Nothing to do.
		} elseif ( preg_match( '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $s ) ||
					preg_match( '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.?$/', $s ) ||
					preg_match( '/^[0-9]{1,3}\.[0-9]{1,3}\.?$/', $s ) ||
					preg_match( '/^[0-9]{1,3}\.$/', $s ) ) {
			// IPv4 address
			$sql          = $wpdb->prepare( "SELECT blog_id FROM {$wpdb->registration_log} WHERE {$wpdb->registration_log}.IP LIKE %s", $wpdb->esc_like( $s ) . ( ! empty( $wild ) ? '%' : '' ) );
			$reg_blog_ids = $wpdb->get_col( $sql );

			if ( $reg_blog_ids ) {
				$args['site__in'] = $reg_blog_ids;
			}
		} elseif ( is_numeric( $s ) && empty( $wild ) ) {
			$args['ID'] = $s;
		} else {
			$args['search'] = $s;

			if ( ! is_subdomain_install() ) {
				$args['search_columns'] = array( 'path' );
			}
		}

		$order_by = isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : '';
		if ( 'registered' === $order_by ) {
			// registered is a valid field name.
		} elseif ( 'lastupdated' === $order_by ) {
			$order_by = 'last_updated';
		} elseif ( 'blogname' === $order_by ) {
			if ( is_subdomain_install() ) {
				$order_by = 'domain';
			} else {
				$order_by = 'path';
			}
		} elseif ( 'blog_id' === $order_by ) {
			$order_by = 'id';
		} elseif ( ! $order_by ) {
			$order_by = false;
		}

		$args['orderby'] = $order_by;

		if ( $order_by ) {
			$args['order'] = ( isset( $_REQUEST['order'] ) && 'DESC' === strtoupper( $_REQUEST['order'] ) ) ? 'DESC' : 'ASC';
		}

		if ( wp_is_large_network() ) {
			$args['no_found_rows'] = true;
		} else {
			$args['no_found_rows'] = false;
		}

		/**
		 * Filters the arguments for the site query in the sites list table.
		 *
		 * @since 4.6.0
		 *
		 * @param array $args An array of get_sites() arguments.
		 */
		$args = apply_filters( 'ms_sites_list_table_query_args', $args );

		$_sites = get_sites( $args );
		if ( is_array( $_sites ) ) {
			update_site_cache( $_sites );

			$this->items = array_slice( $_sites, 0, $per_page );
		}

		$total_sites = get_sites(
			array_merge(
				$args,
				array(
					'count'  => true,
					'offset' => 0,
					'number' => 0,
				)
			)
		);

		$this->set_pagination_args(
			array(
				'total_items' => $total_sites,
				'per_page'    => $per_page,
			)
		);
	}

	/**
	 */
	public function no_items() {
		_e( 'No sites found.' );
	}

	/**
	 * @return array
	 */
	protected function get_bulk_actions() {
		$actions = array();
		if ( current_user_can( 'delete_sites' ) ) {
			$actions['delete'] = __( 'Delete' );
		}
		$actions['spam']    = _x( 'Mark as Spam', 'site' );
		$actions['notspam'] = _x( 'Not Spam', 'site' );

		return $actions;
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
		$sites_columns = array(
			'cb'          => '<input type="checkbox" />',
			'blogname'    => __( 'URL' ),
			'lastupdated' => __( 'Last Updated' ),
			'registered'  => _x( 'Registered', 'site' ),
			'users'       => __( 'Users' ),
		);

		if ( has_filter( 'wpmublogsaction' ) ) {
			$sites_columns['plugins'] = __( 'Actions' );
		}

		/**
		 * Filters the displayed site columns in Sites list table.
		 *
		 * @since MU (3.0.0)
		 *
		 * @param string[] $sites_columns An array of displayed site columns. Default 'cb',
		 *                               'blogname', 'lastupdated', 'registered', 'users'.
		 */
		return apply_filters( 'wpmu_blogs_columns', $sites_columns );
	}

	/**
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'blogname'    => 'blogname',
			'lastupdated' => 'lastupdated',
			'registered'  => 'blog_id',
		);
	}

	/**
	 * Handles the checkbox column output.
	 *
	 * @since 4.3.0
	 *
	 * @param array $blog Current site.
	 */
	public function column_cb( $blog ) {
		if ( ! is_main_site( $blog['blog_id'] ) ) :
			$blogname = untrailingslashit( $blog['domain'] . $blog['path'] );
			?>
			<label class="screen-reader-text" for="blog_<?php echo $blog['blog_id']; ?>">
				<?php
				/* translators: %s: Site URL. */
				printf( __( 'Select %s' ), $blogname );
				?>
			</label>
			<input type="checkbox" id="blog_<?php echo $blog['blog_id']; ?>" name="allblogs[]" value="<?php echo esc_attr( $blog['blog_id'] ); ?>" />
			<?php
		endif;
	}

	/**
	 * Handles the ID column output.
	 *
	 * @since 4.4.0
	 *
	 * @param array $blog Current site.
	 */
	public function column_id( $blog ) {
		echo $blog['blog_id'];
	}

	/**
	 * Handles the site name column output.
	 *
	 * @since 4.3.0
	 *
	 * @global string $mode List table view mode.
	 *
	 * @param array $blog Current site.
	 */
	public function column_blogname( $blog ) {
		global $mode;

		$blogname    = untrailingslashit( $blog['domain'] . $blog['path'] );
		$blog_states = array();
		reset( $this->status_list );

		foreach ( $this->status_list as $status => $col ) {
			if ( $blog[ $status ] == 1 ) {
				$blog_states[] = $col[1];
			}
		}
		$blog_state = '';
		if ( ! empty( $blog_states ) ) {
			$state_count = count( $blog_states );
			$i           = 0;
			$blog_state .= ' &mdash; ';
			foreach ( $blog_states as $state ) {
				++$i;
				$sep         = ( $i == $state_count ) ? '' : ', ';
				$blog_state .= "<span class='post-state'>$state$sep</span>";
			}
		}

		?>
		<strong>
			<a href="<?php echo esc_url( network_admin_url( 'site-info.php?id=' . $blog['blog_id'] ) ); ?>" class="edit"><?php echo $blogname; ?></a>
			<?php echo $blog_state; ?>
		</strong>
		<?php
		if ( 'list' !== $mode ) {
			switch_to_blog( $blog['blog_id'] );
			echo '<p>';
			printf(
				/* translators: 1: Site title, 2: Site tagline. */
				__( '%1$s &#8211; %2$s' ),
				get_option( 'blogname' ),
				'<em>' . get_option( 'blogdescription ' ) . '</em>'
			);
			echo '</p>';
			restore_current_blog();
		}
	}

	/**
	 * Handles the lastupdated column output.
	 *
	 * @since 4.3.0
	 *
	 * @global string $mode List table view mode.
	 *
	 * @param array $blog Current site.
	 */
	public function column_lastupdated( $blog ) {
		global $mode;

		if ( 'list' === $mode ) {
			$date = __( 'Y/m/d' );
		} else {
			$date = __( 'Y/m/d g:i:s a' );
		}

		echo ( $blog['last_updated'] === '0000-00-00 00:00:00' ) ? __( 'Never' ) : mysql2date( $date, $blog['last_updated'] );
	}

	/**
	 * Handles the registered column output.
	 *
	 * @since 4.3.0
	 *
	 * @global string $mode List table view mode.
	 *
	 * @param array $blog Current site.
	 */
	public function column_registered( $blog ) {
		global $mode;

		if ( 'list' === $mode ) {
			$date = __( 'Y/m/d' );
		} else {
			$date = __( 'Y/m/d g:i:s a' );
		}

		if ( $blog['registered'] === '0000-00-00 00:00:00' ) {
			echo '&#x2014;';
		} else {
			echo mysql2date( $date, $blog['registered'] );
		}
	}

	/**
	 * Handles the users column output.
	 *
	 * @since 4.3.0
	 *
	 * @param array $blog Current site.
	 */
	public function column_users( $blog ) {
		$user_count = wp_cache_get( $blog['blog_id'] . '_user_count', 'blog-details' );
		if ( ! $user_count ) {
			$blog_users = new WP_User_Query(
				array(
					'blog_id'     => $blog['blog_id'],
					'fields'      => 'ID',
					'number'      => 1,
					'count_total' => true,
				)
			);
			$user_count = $blog_users->get_total();
			wp_cache_set( $blog['blog_id'] . '_user_count', $user_count, 'blog-details', 12 * HOUR_IN_SECONDS );
		}

		printf(
			'<a href="%s">%s</a>',
			esc_url( network_admin_url( 'site-users.php?id=' . $blog['blog_id'] ) ),
			number_format_i18n( $user_count )
		);
	}

	/**
	 * Handles the plugins column output.
	 *
	 * @since 4.3.0
	 *
	 * @param array $blog Current site.
	 */
	public function column_plugins( $blog ) {
		if ( has_filter( 'wpmublogsaction' ) ) {
			/**
			 * Fires inside the auxiliary 'Actions' column of the Sites list table.
			 *
			 * By default this column is hidden unless something is hooked to the action.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param int $blog_id The site ID.
			 */
			do_action( 'wpmublogsaction', $blog['blog_id'] );
		}
	}

	/**
	 * Handles output for the default column.
	 *
	 * @since 4.3.0
	 *
	 * @param array  $blog        Current site.
	 * @param string $column_name Current column name.
	 */
	public function column_default( $blog, $column_name ) {
		/**
		 * Fires for each registered custom column in the Sites list table.
		 *
		 * @since 3.1.0
		 *
		 * @param string $column_name The name of the column to display.
		 * @param int    $blog_id     The site ID.
		 */
		do_action( 'manage_sites_custom_column', $column_name, $blog['blog_id'] );
	}

	/**
	 * @global string $mode
	 */
	public function display_rows() {
		foreach ( $this->items as $blog ) {
			$blog  = $blog->to_array();
			$class = '';
			reset( $this->status_list );

			foreach ( $this->status_list as $status => $col ) {
				if ( $blog[ $status ] == 1 ) {
					$class = " class='{$col[0]}'";
				}
			}

			echo "<tr{$class}>";

			$this->single_row_columns( $blog );

			echo '</tr>';
		}
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 4.3.0
	 *
	 * @return string Name of the default primary column, in this case, 'blogname'.
	 */
	protected function get_default_primary_column_name() {
		return 'blogname';
	}

	/**
	 * Generates and displays row action links.
	 *
	 * @since 4.3.0
	 *
	 * @param object $blog        Site being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string Row actions output.
	 */
	protected function handle_row_actions( $blog, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return;
		}

		$blogname = untrailingslashit( $blog['domain'] . $blog['path'] );

		// Preordered.
		$actions = array(
			'edit'       => '',
			'backend'    => '',
			'activate'   => '',
			'deactivate' => '',
			'archive'    => '',
			'unarchive'  => '',
			'spam'       => '',
			'unspam'     => '',
			'delete'     => '',
			'visit'      => '',
		);

		$actions['edit']    = '<a href="' . esc_url( network_admin_url( 'site-info.php?id=' . $blog['blog_id'] ) ) . '">' . __( 'Edit' ) . '</a>';
		$actions['backend'] = "<a href='" . esc_url( get_admin_url( $blog['blog_id'] ) ) . "' class='edit'>" . __( 'Dashboard' ) . '</a>';
		if ( get_network()->site_id != $blog['blog_id'] ) {
			if ( $blog['deleted'] == '1' ) {
				$actions['activate'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=activateblog&amp;id=' . $blog['blog_id'] ), 'activateblog_' . $blog['blog_id'] ) ) . '">' . __( 'Activate' ) . '</a>';
			} else {
				$actions['deactivate'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=deactivateblog&amp;id=' . $blog['blog_id'] ), 'deactivateblog_' . $blog['blog_id'] ) ) . '">' . __( 'Deactivate' ) . '</a>';
			}

			if ( $blog['archived'] == '1' ) {
				$actions['unarchive'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=unarchiveblog&amp;id=' . $blog['blog_id'] ), 'unarchiveblog_' . $blog['blog_id'] ) ) . '">' . __( 'Unarchive' ) . '</a>';
			} else {
				$actions['archive'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=archiveblog&amp;id=' . $blog['blog_id'] ), 'archiveblog_' . $blog['blog_id'] ) ) . '">' . _x( 'Archive', 'verb; site' ) . '</a>';
			}

			if ( $blog['spam'] == '1' ) {
				$actions['unspam'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=unspamblog&amp;id=' . $blog['blog_id'] ), 'unspamblog_' . $blog['blog_id'] ) ) . '">' . _x( 'Not Spam', 'site' ) . '</a>';
			} else {
				$actions['spam'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=spamblog&amp;id=' . $blog['blog_id'] ), 'spamblog_' . $blog['blog_id'] ) ) . '">' . _x( 'Spam', 'site' ) . '</a>';
			}

			if ( current_user_can( 'delete_site', $blog['blog_id'] ) ) {
				$actions['delete'] = '<a href="' . esc_url( wp_nonce_url( network_admin_url( 'sites.php?action=confirm&amp;action2=deleteblog&amp;id=' . $blog['blog_id'] ), 'deleteblog_' . $blog['blog_id'] ) ) . '">' . __( 'Delete' ) . '</a>';
			}
		}

		$actions['visit'] = "<a href='" . esc_url( get_home_url( $blog['blog_id'], '/' ) ) . "' rel='bookmark'>" . __( 'Visit' ) . '</a>';

		/**
		 * Filters the action links displayed for each site in the Sites list table.
		 *
		 * The 'Edit', 'Dashboard', 'Delete', and 'Visit' links are displayed by
		 * default for each site. The site's status determines whether to show the
		 * 'Activate' or 'Deactivate' link, 'Unarchive' or 'Archive' links, and
		 * 'Not Spam' or 'Spam' link for each site.
		 *
		 * @since 3.1.0
		 *
		 * @param string[] $actions  An array of action links to be displayed.
		 * @param int      $blog_id  The site ID.
		 * @param string   $blogname Site path, formatted depending on whether it is a sub-domain
		 *                           or subdirectory multisite installation.
		 */
		$actions = apply_filters( 'manage_sites_action_links', array_filter( $actions ), $blog['blog_id'], $blogname );
		return $this->row_actions( $actions );
	}
}
