<?php
/**
 * MS Themes List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class WP_MS_Themes_List_Table extends WP_List_Table {

	public $site_id;
	public $is_site_themes;

	private $has_items;

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @global string $status
	 * @global int    $page
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		global $status, $page;

		parent::__construct( array(
			'plural' => 'themes',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );

		$status = isset( $_REQUEST['theme_status'] ) ? $_REQUEST['theme_status'] : 'all';
		if ( !in_array( $status, array( 'all', 'enabled', 'disabled', 'upgrade', 'search', 'broken' ) ) )
			$status = 'all';

		$page = $this->get_pagenum();

		$this->is_site_themes = ( 'site-themes-network' == $this->screen->id ) ? true : false;

		if ( $this->is_site_themes )
			$this->site_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
	}

	/**
	 *
	 * @return array
	 */
	protected function get_table_classes() {
		// todo: remove and add CSS for .themes
		return array( 'widefat', 'plugins' );
	}

	/**
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		if ( $this->is_site_themes )
			return current_user_can( 'manage_sites' );
		else
			return current_user_can( 'manage_network_themes' );
	}

	/**
	 *
	 * @global string $status
	 * @global array $totals
	 * @global int $page
	 * @global string $orderby
	 * @global string $order
	 * @global string $s
	 */
	public function prepare_items() {
		global $status, $totals, $page, $orderby, $order, $s;

		wp_reset_vars( array( 'orderby', 'order', 's' ) );

		$themes = array(
			/**
			 * Filter the full array of WP_Theme objects to list in the Multisite
			 * themes list table.
			 *
			 * @since 3.1.0
			 *
			 * @param array $all An array of WP_Theme objects to display in the list table.
			 */
			'all' => apply_filters( 'all_themes', wp_get_themes() ),
			'search' => array(),
			'enabled' => array(),
			'disabled' => array(),
			'upgrade' => array(),
			'broken' => $this->is_site_themes ? array() : wp_get_themes( array( 'errors' => true ) ),
		);

		if ( $this->is_site_themes ) {
			$themes_per_page = $this->get_items_per_page( 'site_themes_network_per_page' );
			$allowed_where = 'site';
		} else {
			$themes_per_page = $this->get_items_per_page( 'themes_network_per_page' );
			$allowed_where = 'network';
		}

		$maybe_update = current_user_can( 'update_themes' ) && ! $this->is_site_themes && $current = get_site_transient( 'update_themes' );

		foreach ( (array) $themes['all'] as $key => $theme ) {
			if ( $this->is_site_themes && $theme->is_allowed( 'network' ) ) {
				unset( $themes['all'][ $key ] );
				continue;
			}

			if ( $maybe_update && isset( $current->response[ $key ] ) ) {
				$themes['all'][ $key ]->update = true;
				$themes['upgrade'][ $key ] = $themes['all'][ $key ];
			}

			$filter = $theme->is_allowed( $allowed_where, $this->site_id ) ? 'enabled' : 'disabled';
			$themes[ $filter ][ $key ] = $themes['all'][ $key ];
		}

		if ( $s ) {
			$status = 'search';
			$themes['search'] = array_filter( array_merge( $themes['all'], $themes['broken'] ), array( $this, '_search_callback' ) );
		}

		$totals = array();
		foreach ( $themes as $type => $list )
			$totals[ $type ] = count( $list );

		if ( empty( $themes[ $status ] ) && !in_array( $status, array( 'all', 'search' ) ) )
			$status = 'all';

		$this->items = $themes[ $status ];
		WP_Theme::sort_by_name( $this->items );

		$this->has_items = ! empty( $themes['all'] );
		$total_this_page = $totals[ $status ];

		if ( $orderby ) {
			$orderby = ucfirst( $orderby );
			$order = strtoupper( $order );

			if ( $orderby == 'Name' ) {
				if ( 'ASC' == $order )
					$this->items = array_reverse( $this->items );
			} else {
				uasort( $this->items, array( $this, '_order_callback' ) );
			}
		}

		$start = ( $page - 1 ) * $themes_per_page;

		if ( $total_this_page > $themes_per_page )
			$this->items = array_slice( $this->items, $start, $themes_per_page, true );

		$this->set_pagination_args( array(
			'total_items' => $total_this_page,
			'per_page' => $themes_per_page,
		) );
	}

	/**
	 * @staticvar string $term
	 * @param WP_Theme $theme
	 * @return bool
	 */
	public function _search_callback( $theme ) {
		static $term = null;
		if ( is_null( $term ) )
			$term = wp_unslash( $_REQUEST['s'] );

		foreach ( array( 'Name', 'Description', 'Author', 'Author', 'AuthorURI' ) as $field ) {
			// Don't mark up; Do translate.
			if ( false !== stripos( $theme->display( $field, false, true ), $term ) )
				return true;
		}

		if ( false !== stripos( $theme->get_stylesheet(), $term ) )
			return true;

		if ( false !== stripos( $theme->get_template(), $term ) )
			return true;

		return false;
	}

	// Not used by any core columns.
	/**
	 * @global string $orderby
	 * @global string $order
	 * @param array $theme_a
	 * @param array $theme_b
	 * @return int
	 */
	public function _order_callback( $theme_a, $theme_b ) {
		global $orderby, $order;

		$a = $theme_a[ $orderby ];
		$b = $theme_b[ $orderby ];

		if ( $a == $b )
			return 0;

		if ( 'DESC' == $order )
			return ( $a < $b ) ? 1 : -1;
		else
			return ( $a < $b ) ? -1 : 1;
	}

	/**
	 * @access public
	 */
	public function no_items() {
		if ( ! $this->has_items )
			_e( 'No themes found.' );
		else
			_e( 'You do not appear to have any themes available at this time.' );
	}

	/**
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'name'        => __( 'Theme' ),
			'description' => __( 'Description' ),
		);
	}

	/**
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'name'         => 'name',
		);
	}

	/**
	 * Get the name of the primary column.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @return string Unalterable name of the primary column name, in this case, 'name'.
	 */
	protected function get_primary_column_name() {
		return 'name';
	}

	/**
	 *
	 * @global array $totals
	 * @global string $status
	 * @return array
	 */
	protected function get_views() {
		global $totals, $status;

		$status_links = array();
		foreach ( $totals as $type => $count ) {
			if ( !$count )
				continue;

			switch ( $type ) {
				case 'all':
					$text = _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $count, 'themes' );
					break;
				case 'enabled':
					$text = _n( 'Enabled <span class="count">(%s)</span>', 'Enabled <span class="count">(%s)</span>', $count );
					break;
				case 'disabled':
					$text = _n( 'Disabled <span class="count">(%s)</span>', 'Disabled <span class="count">(%s)</span>', $count );
					break;
				case 'upgrade':
					$text = _n( 'Update Available <span class="count">(%s)</span>', 'Update Available <span class="count">(%s)</span>', $count );
					break;
				case 'broken' :
					$text = _n( 'Broken <span class="count">(%s)</span>', 'Broken <span class="count">(%s)</span>', $count );
					break;
			}

			if ( $this->is_site_themes )
				$url = 'site-themes.php?id=' . $this->site_id;
			else
				$url = 'themes.php';

			if ( 'search' != $type ) {
				$status_links[$type] = sprintf( "<a href='%s' %s>%s</a>",
					esc_url( add_query_arg('theme_status', $type, $url) ),
					( $type == $status ) ? ' class="current"' : '',
					sprintf( $text, number_format_i18n( $count ) )
				);
			}
		}

		return $status_links;
	}

	/**
	 * @global string $status
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		global $status;

		$actions = array();
		if ( 'enabled' != $status )
			$actions['enable-selected'] = $this->is_site_themes ? __( 'Enable' ) : __( 'Network Enable' );
		if ( 'disabled' != $status )
			$actions['disable-selected'] = $this->is_site_themes ? __( 'Disable' ) : __( 'Network Disable' );
		if ( ! $this->is_site_themes ) {
			if ( current_user_can( 'update_themes' ) )
				$actions['update-selected'] = __( 'Update' );
			if ( current_user_can( 'delete_themes' ) )
				$actions['delete-selected'] = __( 'Delete' );
		}
		return $actions;
	}

	/**
	 * @access public
	 */
	public function display_rows() {
		foreach ( $this->items as $theme )
			$this->single_row( $theme );
	}

	/**
	 * @since 4.3.0
	 *
	 * @param WP_Theme $theme
	 */
	public function column_cb( $theme ) {
		$checkbox_id = 'checkbox_' . md5( $theme->get('Name') );
		?>
		<input type="checkbox" name="checked[]" value="<?php echo esc_attr( $theme->get_stylesheet() ) ?>" id="<?php echo $checkbox_id ?>" />
		<label class="screen-reader-text" for="<?php echo $checkbox_id ?>" ><?php _e( 'Select' ) ?>  <?php echo $theme->display( 'Name' ) ?></label>
		<?php
	}

	/**
	 * @since 4.3.0
	 *
	 * @global string $status
	 * @global int    $page
	 * @global string $s
	 *
	 * @param WP_Theme $theme
	 */
	public function column_name( $theme ) {
		global $status, $page, $s;

		$context = $status;

		if ( $this->is_site_themes ) {
			$url = "site-themes.php?id={$this->site_id}&amp;";
			$allowed = $theme->is_allowed( 'site', $this->site_id );
		} else {
			$url = 'themes.php?';
			$allowed = $theme->is_allowed( 'network' );
		}

		// Pre-order.
		$actions = array(
			'enable' => '',
			'disable' => '',
			'edit' => '',
			'delete' => ''
		);

		$stylesheet = $theme->get_stylesheet();
		$theme_key = urlencode( $stylesheet );

		if ( ! $allowed ) {
			if ( ! $theme->errors() ) {
				$actions['enable'] = '<a href="' . esc_url( wp_nonce_url($url . 'action=enable&amp;theme=' . $theme_key . '&amp;paged=' . $page . '&amp;s=' . $s, 'enable-theme_' . $stylesheet ) ) . '" title="' . esc_attr__('Enable this theme') . '" class="edit">' . ( $this->is_site_themes ? __( 'Enable' ) : __( 'Network Enable' ) ) . '</a>';
			}
		} else {
			$actions['disable'] = '<a href="' . esc_url( wp_nonce_url($url . 'action=disable&amp;theme=' . $theme_key . '&amp;paged=' . $page . '&amp;s=' . $s, 'disable-theme_' . $stylesheet ) ) . '" title="' . esc_attr__('Disable this theme') . '">' . ( $this->is_site_themes ? __( 'Disable' ) : __( 'Network Disable' ) ) . '</a>';
		}

		if ( current_user_can('edit_themes') ) {
			$actions['edit'] = '<a href="' . esc_url('theme-editor.php?theme=' . $theme_key ) . '" title="' . esc_attr__('Open this theme in the Theme Editor') . '" class="edit">' . __('Edit') . '</a>';
		}

		if ( ! $allowed && current_user_can( 'delete_themes' ) && ! $this->is_site_themes && $stylesheet != get_option( 'stylesheet' ) && $stylesheet != get_option( 'template' ) ) {
			$actions['delete'] = '<a href="' . esc_url( wp_nonce_url( 'themes.php?action=delete-selected&amp;checked[]=' . $theme_key . '&amp;theme_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'bulk-themes' ) ) . '" title="' . esc_attr__( 'Delete this theme' ) . '" class="delete">' . __( 'Delete' ) . '</a>';
		}
		/**
		 * Filter the action links displayed for each theme in the Multisite
		 * themes list table.
		 *
		 * The action links displayed are determined by the theme's status, and
		 * which Multisite themes list table is being displayed - the Network
		 * themes list table (themes.php), which displays all installed themes,
		 * or the Site themes list table (site-themes.php), which displays the
		 * non-network enabled themes when editing a site in the Network admin.
		 *
		 * The default action links for the Network themes list table include
		 * 'Network Enable', 'Network Disable', 'Edit', and 'Delete'.
		 *
		 * The default action links for the Site themes list table include
		 * 'Enable', 'Disable', and 'Edit'.
		 *
		 * @since 2.8.0
		 *
		 * @param array    $actions An array of action links.
		 * @param WP_Theme $theme   The current WP_Theme object.
		 * @param string   $context Status of the theme.
		 */
		$actions = apply_filters( 'theme_action_links', array_filter( $actions ), $theme, $context );

		/**
		 * Filter the action links of a specific theme in the Multisite themes
		 * list table.
		 *
		 * The dynamic portion of the hook name, `$stylesheet`, refers to the
		 * directory name of the theme, which in most cases is synonymous
		 * with the template name.
		 *
		 * @since 3.1.0
		 *
		 * @param array    $actions An array of action links.
		 * @param WP_Theme $theme   The current WP_Theme object.
		 * @param string   $context Status of the theme.
		 */
		$actions = apply_filters( "theme_action_links_$stylesheet", $actions, $theme, $context );

		echo $this->row_actions( $actions, true );
	}

	/**
	 * @since 4.3.0
	 *
	 * @global string $status
	 * @global array  $totals
	 *
	 * @param WP_Theme $theme
	 */
	public function column_description( $theme ) {
		global $status, $totals;
		if ( $theme->errors() ) {
			$pre = $status == 'broken' ? __( 'Broken Theme:' ) . ' ' : '';
			echo '<p><strong class="attention">' . $pre . $theme->errors()->get_error_message() . '</strong></p>';
		}

		if ( $this->is_site_themes ) {
			$allowed = $theme->is_allowed( 'site', $this->site_id );
		} else {
			$allowed = $theme->is_allowed( 'network' );
		}

		$class = ! $allowed ? 'inactive' : 'active';
		if ( ! empty( $totals['upgrade'] ) && ! empty( $theme->update ) )
			$class .= ' update';

		echo "<div class='theme-description'><p>" . $theme->display( 'Description' ) . "</p></div>
			<div class='$class second theme-version-author-uri'>";

		$stylesheet = $theme->get_stylesheet();
		$theme_meta = array();

		if ( $theme->get('Version') ) {
			$theme_meta[] = sprintf( __( 'Version %s' ), $theme->display('Version') );
		}
		$theme_meta[] = sprintf( __( 'By %s' ), $theme->display('Author') );

		if ( $theme->get('ThemeURI') ) {
			$theme_meta[] = '<a href="' . $theme->display('ThemeURI') . '" title="' . esc_attr__( 'Visit theme homepage' ) . '">' . __( 'Visit Theme Site' ) . '</a>';
		}
		/**
		 * Filter the array of row meta for each theme in the Multisite themes
		 * list table.
		 *
		 * @since 3.1.0
		 *
		 * @param array    $theme_meta An array of the theme's metadata,
		 *                             including the version, author, and
		 *                             theme URI.
		 * @param string   $stylesheet Directory name of the theme.
		 * @param WP_Theme $theme      WP_Theme object.
		 * @param string   $status     Status of the theme.
		 */
		$theme_meta = apply_filters( 'theme_row_meta', $theme_meta, $stylesheet, $theme, $status );
		echo implode( ' | ', $theme_meta );

		echo '</div>';
	}

	/**
	 * @since 4.3.0
	 *
	 * @param WP_Theme $theme
	 * @param string   $column_name
	 */
	public function column_default( $theme, $column_name ) {
		$stylesheet = $theme->get_stylesheet();
		/**
		 * Fires inside each custom column of the Multisite themes list table.
		 *
		 * @since 3.1.0
		 *
		 * @param string   $column_name Name of the column.
		 * @param string   $stylesheet  Directory name of the theme.
		 * @param WP_Theme $theme       Current WP_Theme object.
		 */
		do_action( 'manage_themes_custom_column', $column_name, $stylesheet, $theme );
	}

	/**
	 * @since 4.3.0
	 *
	 * @param WP_Theme $item
	 */
	public function single_row_columns( $item ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$extra_classes = '';
			if ( in_array( $column_name, $hidden ) ) {
				$extra_classes .= ' hidden';
			}

			switch ( $column_name ) {
				case 'cb':
					echo '<th scope="row" class="check-column">';

					$this->column_cb( $item );

					echo '</th>';
					break;

				case 'name':
					echo "<td class='theme-title column-primary{$extra_classes}'><strong>" . $item->display('Name') . "</strong>";

					$this->column_name( $item );

					echo "</td>";
					break;

				case 'description':
					echo "<td class='column-description desc{$extra_classes}'>";

					$this->column_description( $item );

					echo '</td>';
					break;

				default:
					echo "<td class='$column_name column-$column_name{$extra_classes}'>";

					$this->column_default( $item, $column_name );

					echo "</td>";
					break;
			}
		}
	}

	/**
	 * @global string $status
	 * @global array  $totals
	 *
	 * @param WP_Theme $theme
	 */
	public function single_row( $theme ) {
		global $status, $totals;

		if ( $this->is_site_themes ) {
			$allowed = $theme->is_allowed( 'site', $this->site_id );
		} else {
			$allowed = $theme->is_allowed( 'network' );
		}

		$stylesheet = $theme->get_stylesheet();

		$class = ! $allowed ? 'inactive' : 'active';

		$id = sanitize_html_class( $theme->get_stylesheet() );

		if ( ! empty( $totals['upgrade'] ) && ! empty( $theme->update ) ) {
			$class .= ' update';
		}

		echo "<tr id='$id' class='$class'>";

		$this->single_row_columns( $theme );

		echo "</tr>";

		if ( $this->is_site_themes )
			remove_action( "after_theme_row_$stylesheet", 'wp_theme_update_row' );

		/**
		 * Fires after each row in the Multisite themes list table.
		 *
		 * @since 3.1.0
		 *
		 * @param string   $stylesheet Directory name of the theme.
		 * @param WP_Theme $theme      Current WP_Theme object.
		 * @param string   $status     Status of the theme.
		 */
		do_action( 'after_theme_row', $stylesheet, $theme, $status );

		/**
		 * Fires after each specific row in the Multisite themes list table.
		 *
		 * The dynamic portion of the hook name, `$stylesheet`, refers to the
		 * directory name of the theme, most often synonymous with the template
		 * name of the theme.
		 *
		 * @since 3.5.0
		 *
		 * @param string   $stylesheet Directory name of the theme.
		 * @param WP_Theme $theme      Current WP_Theme object.
		 * @param string   $status     Status of the theme.
		 */
		do_action( "after_theme_row_$stylesheet", $stylesheet, $theme, $status );
	}
}
