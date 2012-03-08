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

	function __construct() {
		global $status, $page;

		$status = isset( $_REQUEST['theme_status'] ) ? $_REQUEST['theme_status'] : 'all';
		if ( !in_array( $status, array( 'all', 'enabled', 'disabled', 'upgrade', 'search', 'broken' ) ) )
			$status = 'all';

		$page = $this->get_pagenum();

		$screen = get_current_screen();
		$this->is_site_themes = ( 'site-themes-network' == $screen->id ) ? true : false;

		if ( $this->is_site_themes )
			$this->site_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

		parent::__construct( array(
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
		global $status, $totals, $page, $orderby, $order, $s;

		wp_reset_vars( array( 'orderby', 'order', 's' ) );

		$themes = array(
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

		$maybe_update = current_user_can( 'update_themes' ) && ! $this->is_site_themes && get_site_transient( 'update_themes' );

		foreach ( (array) $themes['all'] as $key => $theme ) {
			if ( $this->is_site_themes && $theme->is_allowed( 'network' ) ) {
				unset( $themes['all'][ $key ] );
				continue;
			}

			$filter = $theme->is_allowed( $allowed_where, $this->site_id ) ? 'enabled' : 'disabled';
			$themes[ $filter ][ $key ] = $themes['all'][ $key ];

			if ( $maybe_update && isset( $current->response[ $key ] ) )
				$themes['upgrade'][ $key ] = $themes['all'][ $key ];
		}

		if ( $s ) {
			$status = 'search';
			$themes['search'] = array_filter( array_merge( $themes['all'], $themes['broken'] ), array( &$this, '_search_callback' ) );
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
				uasort( $this->items, array( &$this, '_order_callback' ) );
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

	function _search_callback( $theme ) {
		static $term;
		if ( is_null( $term ) )
			$term = stripslashes( $_REQUEST['s'] );

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
	function _order_callback( $theme_a, $theme_b ) {
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

	function no_items() {
		if ( ! $this->has_items )
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

		if ( $this->is_site_themes ) {
			$url = "site-themes.php?id={$this->site_id}&amp;";
			$allowed = $theme->is_allowed( 'site', $this->site_id );
		} else {
			$url = 'themes.php?';
			$allowed = $theme->is_allowed( 'network' );
		}

		// preorder
		$actions = array(
			'enable' => '',
			'disable' => '',
			'edit' => '',
			'delete' => ''
		);

		$theme_key = $theme->get_stylesheet();

		if ( ! $allowed ) {
			if ( ! $theme->errors() )
				$actions['enable'] = '<a href="' . esc_url( wp_nonce_url($url . 'action=enable&amp;theme=' . $theme_key . '&amp;paged=' . $page . '&amp;s=' . $s, 'enable-theme_' . $theme_key) ) . '" title="' . esc_attr__('Enable this theme') . '" class="edit">' . ( $this->is_site_themes ? __( 'Enable' ) : __( 'Network Enable' ) ) . '</a>';
		} else {
			$actions['disable'] = '<a href="' . esc_url( wp_nonce_url($url . 'action=disable&amp;theme=' . $theme_key . '&amp;paged=' . $page . '&amp;s=' . $s, 'disable-theme_' . $theme_key) ) . '" title="' . esc_attr__('Disable this theme') . '">' . ( $this->is_site_themes ? __( 'Disable' ) : __( 'Network Disable' ) ) . '</a>';
		}

		if ( current_user_can('edit_themes') )
			$actions['edit'] = '<a href="' . esc_url('theme-editor.php?theme=' .  $theme_key ) . '" title="' . esc_attr__('Open this theme in the Theme Editor') . '" class="edit">' . __('Edit') . '</a>';

		if ( ! $allowed && current_user_can( 'delete_themes' ) && ! $this->is_site_themes && $theme_key != get_option( 'stylesheet' ) && $theme_key != get_option( 'template' ) )
			$actions['delete'] = '<a href="' . esc_url( wp_nonce_url( 'themes.php?action=delete-selected&amp;checked[]=' . $theme_key . '&amp;theme_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'bulk-themes' ) ) . '" title="' . esc_attr__( 'Delete this theme' ) . '" class="delete">' . __( 'Delete' ) . '</a>';

		$actions = apply_filters( 'theme_action_links', array_filter( $actions ), $theme_key, $theme, $context );
		$actions = apply_filters( "theme_action_links_$theme_key", $actions, $theme_key, $theme, $context );

		$class = ! $allowed ? 'inactive' : 'active';
		$checkbox_id = "checkbox_" . md5( $theme->get('Name') );
		$checkbox = "<input type='checkbox' name='checked[]' value='" . esc_attr( $theme_key ) . "' id='" . $checkbox_id . "' /><label class='screen-reader-text' for='" . $checkbox_id . "' >" . __('Select') . " " . $theme->display('Name') . "</label>";

		$id = sanitize_html_class( $theme->get_stylesheet() );

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
					echo "<td class='theme-title'$style><strong>" . $theme->display('Name') . "</strong>";
					echo $this->row_actions( $actions, true );
					echo "</td>";
					break;
				case 'description':
					echo "<td class='column-description desc'$style>";
					if ( $theme->errors() ) {
						$pre = $status == 'broken' ? '' : __( 'Broken Theme:' ) . ' ';
						echo '<p><strong class="attention">' . $pre . $theme->errors()->get_error_message() . '</strong></p>';
					}
					echo "<div class='theme-description'><p>" . $theme->display( 'Description' ) . "</p></div>
						<div class='$class second theme-version-author-uri'>";

					$theme_meta = array();

					if ( $theme->get('Version') )
						$theme_meta[] = sprintf( __( 'Version %s' ), $theme->display('Version') );

					$theme_meta[] = sprintf( __( 'By %s' ), $theme->display('Author') );

					if ( $theme->get('ThemeURI') )
						$theme_meta[] = '<a href="' . $theme->display('ThemeURI') . '" title="' . esc_attr__( 'Visit theme homepage' ) . '">' . __( 'Visit Theme Site' ) . '</a>';

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
