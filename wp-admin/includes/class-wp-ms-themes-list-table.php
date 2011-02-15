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

	var $site_id;
	var $is_site_themes;

	function WP_MS_Themes_List_Table() {
		global $status, $page;

		$default_status = get_user_option( 'themes_last_view' );
		if ( empty( $default_status ) )
			$default_status = 'all';
		$status = isset( $_REQUEST['theme_status'] ) ? $_REQUEST['theme_status'] : $default_status;
		if ( !in_array( $status, array( 'all', 'enabled', 'disabled', 'upgrade', 'search' ) ) )
			$status = 'all';
		if ( $status != $default_status && 'search' != $status )
			update_user_meta( get_current_user_id(), 'themes_last_view', $status );

		$page = $this->get_pagenum();

		$screen = get_current_screen();
		$this->is_site_themes = ( 'site-themes-network' == $screen->id ) ? true : false;

		if ( $this->is_site_themes )
			$this->site_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

		parent::WP_List_Table( array(
			'plural' => 'themes'
		) );
	}

	function get_table_classes() {
		return array( 'widefat', 'plugins' );	// todo: remove and add CSS for .themes
	}

	function ajax_user_can() {
		$menu_perms = get_site_option( 'menu_items', array() );

		if ( empty( $menu_perms['themes'] ) && ! is_super_admin() )
			return false;

		if ( $this->is_site_themes && !current_user_can('manage_sites') )
			return false;
		elseif ( !$this->is_site_themes && !current_user_can('manage_network_themes') )
			return false;
		return true;
	}

	function prepare_items() {
		global $status, $themes, $totals, $page, $orderby, $order, $s;

		wp_reset_vars( array( 'orderby', 'order', 's' ) );

		$themes = array(
			'all' => apply_filters( 'all_themes', get_themes() ),
			'search' => array(),
			'enabled' => array(),
			'disabled' => array(),
			'upgrade' => array()
		);

		$site_allowed_themes = get_site_allowed_themes();
		if ( !$this->is_site_themes ) {
			$allowed_themes = $site_allowed_themes;
			$themes_per_page = $this->get_items_per_page( 'themes_network_per_page' );
		} else {
			$allowed_themes = wpmu_get_blog_allowedthemes( $this->site_id );
			$themes_per_page = $this->get_items_per_page( 'site_themes_network_per_page' );
		}

		$current = get_site_transient( 'update_themes' );

		foreach ( (array) $themes['all'] as $key => $theme ) {
			$theme_key = $theme['Stylesheet'];

			if ( isset( $allowed_themes [ $theme_key ] ) )  {
				$themes['all'][$key]['enabled'] = true;
				$themes['enabled'][$key] = $themes['all'][$key];
			}
			else {
				$themes['all'][$key]['enabled'] = false;
				$themes['disabled'][$key] = $themes['all'][$key];
			}
			if ( isset( $current->response[ $theme['Template'] ] ) )
				$themes['upgrade'][$key] = $themes['all'][$key];

			if ( $this->is_site_themes && isset( $site_allowed_themes[$theme_key] ) ) {
				unset( $themes['all'][$key] );
				unset( $themes['enabled'][$key] );
				unset( $themes['disabled'][$key] );
			}
		}

		if ( !current_user_can( 'update_themes' ) || $this->is_site_themes )
			$themes['upgrade'] = array();

		if ( $s ) {
			$status = 'search';
			$themes['search'] = array_filter( $themes['all'], array( &$this, '_search_callback' ) );
		}

		$totals = array();
		foreach ( $themes as $type => $list )
			$totals[ $type ] = count( $list );

		if ( empty( $themes[ $status ] ) && !in_array( $status, array( 'all', 'search' ) ) )
			$status = 'all';

		$this->items = $themes[ $status ];
		$total_this_page = $totals[ $status ];

		if ( $orderby ) {
			$orderby = ucfirst( $orderby );
			$order = strtoupper( $order );

			uasort( $this->items, array( &$this, '_order_callback' ) );
		}

		$start = ( $page - 1 ) * $themes_per_page;

		if ( $total_this_page > $themes_per_page )
			$this->items = array_slice( $this->items, $start, $themes_per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_this_page,
			'per_page' => $themes_per_page,
		) );
	}

	function _search_callback( $theme ) {
		static $term;
		if ( is_null( $term ) )
			$term = stripslashes( $_REQUEST['s'] );

		$search_fields = array( 'Name', 'Title', 'Description', 'Author', 'Author Name', 'Author URI', 'Template', 'Stylesheet' );
		foreach ( $search_fields as $field )
			if ( stripos( $theme[ $field ], $term ) !== false )
				return true;

		return false;
	}

	function _order_callback( $theme_a, $theme_b ) {
		global $orderby, $order;

		$a = $theme_a[$orderby];
		$b = $theme_b[$orderby];

		if ( $a == $b )
			return 0;

		if ( 'DESC' == $order )
			return ( $a < $b ) ? 1 : -1;
		else
			return ( $a < $b ) ? -1 : 1;
	}

	function no_items() {
		global $themes;

		if ( !empty( $themes['all'] ) )
			_e( 'No themes found.' );
		else
			_e( 'You do not appear to have any themes available at this time.' );
	}

	function get_columns() {
		global $status;

		return array(
			'cb'          => '<input type="checkbox" />',
			'name'        => __( 'Theme' ),
			'description' => __( 'Description' ),
		);
	}

	function get_sortable_columns() {
		return array(
			'name'         => 'name',
		);
	}

	function get_views() {
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

	function get_bulk_actions() {
		global $status;

		$actions = array();
		if ( 'enabled' != $status )
			$actions['enable-selected'] = $this->is_site_themes ? __( 'Enable' ) : __( 'Network Enable' );
		if ( 'disabled' != $status )
			$actions['disable-selected'] = $this->is_site_themes ? __( 'Disable' ) : __( 'Network Disable' );
		if ( ! $this->is_site_themes ) {
			if ( current_user_can( 'delete_themes' ) )
				$actions['delete-selected'] = __( 'Delete' );
			if ( current_user_can( 'update_themes' ) )
				$actions['update-selected'] = __( 'Update' );
		}
		return $actions;
	}

	function bulk_actions( $which ) {
		global $status;
		parent::bulk_actions( $which );
	}

	function current_action() {
		return parent::current_action();
	}

	function display_rows() {
		foreach ( $this->items as $key => $theme )
			$this->single_row( $key, $theme );
	}

	function single_row( $key, $theme ) {
		global $status, $page, $s;

		$context = $status;

		if ( $this->is_site_themes )
			$url = "site-themes.php?id={$this->site_id}&amp;";
		else
			$url = 'themes.php?';

		// preorder
		$actions = array(
			'enable' => '',
			'disable' => '',
			'edit' => '',
			'delete' => ''
		);

		$theme_key = $theme['Stylesheet'];

		if ( empty( $theme['enabled'] ) )
			$actions['enable'] = '<a href="' . esc_url( wp_nonce_url($url . 'action=enable&amp;theme=' . $theme_key . '&amp;paged=' . $page . '&amp;s=' . $s, 'enable-theme_' . $theme_key) ) . '" title="' . esc_attr__('Enable this theme') . '" class="edit">' . ( $this->is_site_themes ? __( 'Enable' ) : __( 'Network Enable' ) ) . '</a>';
		else
			$actions['disable'] = '<a href="' . esc_url( wp_nonce_url($url . 'action=disable&amp;theme=' . $theme_key . '&amp;paged=' . $page . '&amp;s=' . $s, 'disable-theme_' . $theme_key) ) . '" title="' . esc_attr__('Disable this theme') . '">' . ( $this->is_site_themes ? __( 'Disable' ) : __( 'Network Disable' ) ) . '</a>';

		if ( current_user_can('edit_themes') )
			$actions['edit'] = '<a href="' . esc_url('theme-editor.php?theme=' . urlencode( $theme['Name'] )) . '" title="' . esc_attr__('Open this theme in the Theme Editor') . '" class="edit">' . __('Edit') . '</a>';

		if ( empty( $theme['enabled'] ) && current_user_can( 'delete_themes' ) && ! $this->is_site_themes && $theme_key != get_option( 'stylesheet' ) && $theme_key != get_option( 'template' ) )
			$actions['delete'] = '<a href="' . esc_url( wp_nonce_url( 'themes.php?action=delete-selected&amp;checked[]=' . $theme_key . '&amp;theme_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'bulk-themes' ) ) . '" title="' . esc_attr__( 'Delete this theme' ) . '" class="delete">' . __( 'Delete' ) . '</a>';

		$actions = apply_filters( 'theme_action_links', array_filter( $actions ), $theme_key, $theme, $context );
		$actions = apply_filters( "theme_action_links_$theme_key", $actions, $theme_key, $theme, $context );

		$class = empty( $theme['enabled'] ) ? 'inactive' : 'active';
		$checkbox_id = "checkbox_" . md5($theme['Name']);
		$checkbox = "<input type='checkbox' name='checked[]' value='" . esc_attr( $theme_key ) . "' id='" . $checkbox_id . "' /><label class='screen-reader-text' for='" . $checkbox_id . "' >" . __('Select') . " " . $theme['Name'] . "</label>";

		$description = '<p>' . $theme['Description'] . '</p>';
		$theme_name = $theme['Name'];

		$id = sanitize_title( $theme_name );

		echo "<tr id='$id' class='$class'>";

		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			switch ( $column_name ) {
				case 'cb':
					echo "<th scope='row' class='check-column'>$checkbox</th>";
					break;
				case 'name':
					echo "<td class='theme-title'$style><strong>$theme_name</strong>";
					echo $this->row_actions( $actions, true );
					echo "</td>";
					break;
				case 'description':
					echo "<td class='column-description desc'$style>
						<div class='theme-description'>$description</div>
						<div class='$class second theme-version-author-uri'>";

					$theme_meta = array();

					if ( !empty( $theme['Version'] ) )
						$theme_meta[] = sprintf( __( 'Version %s' ), $theme['Version'] );

					if ( !empty( $theme['Author'] ) )
						$theme_meta[] = sprintf( __( 'By %s' ), $theme['Author'] );

					if ( !empty( $theme['Theme URI'] ) )
						$theme_meta[] = '<a href="' . $theme['Theme URI'] . '" title="' . esc_attr__( 'Visit theme homepage' ) . '">' . __( 'Visit Theme Site' ) . '</a>';

					$theme_meta = apply_filters( 'theme_row_meta', $theme_meta, $theme_key, $theme, $status );
					echo implode( ' | ', $theme_meta );

					echo "</div></td>";
					break;

				default:
					echo "<td class='$column_name column-$column_name'$style>";
					do_action( 'manage_themes_custom_column', $column_name, $theme_key, $theme );
					echo "</td>";
			}
		}

		echo "</tr>";

		if ( $this->is_site_themes )
			remove_action( "after_theme_row_$theme_key", 'wp_theme_update_row' );
		do_action( 'after_theme_row', $theme_key, $theme, $status );
		do_action( "after_theme_row_$theme_key", $theme_key, $theme, $status );
	}
}

?>
