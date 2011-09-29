<?php
/**
 * Plugins List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class WP_Plugins_List_Table extends WP_List_Table {

	function __construct() {
		global $status, $page;

		$status = 'all';
		if ( isset( $_REQUEST['plugin_status'] ) && in_array( $_REQUEST['plugin_status'], array( 'active', 'inactive', 'recently_activated', 'upgrade', 'network', 'mustuse', 'dropins', 'search' ) ) )
			$status = $_REQUEST['plugin_status'];

		if ( isset($_REQUEST['s']) )
			$_SERVER['REQUEST_URI'] = add_query_arg('s', stripslashes($_REQUEST['s']) );

		$page = $this->get_pagenum();

		parent::__construct( array(
			'plural' => 'plugins',
		) );
	}

	function get_table_classes() {
		return array( 'widefat', $this->_args['plural'] );
	}

	function ajax_user_can() {
		if ( is_multisite() ) {
			$menu_perms = get_site_option( 'menu_items', array() );

			if ( empty( $menu_perms['plugins'] ) && ! is_super_admin() )
				return false;
		}

		return current_user_can('activate_plugins');
	}

	function prepare_items() {
		global $status, $plugins, $totals, $page, $orderby, $order, $s;

		wp_reset_vars( array( 'orderby', 'order', 's' ) );

		$plugins = array(
			'all' => apply_filters( 'all_plugins', get_plugins() ),
			'search' => array(),
			'active' => array(),
			'inactive' => array(),
			'recently_activated' => array(),
			'upgrade' => array(),
			'mustuse' => array(),
			'dropins' => array()
		);

		$screen = get_current_screen();

		if ( ! is_multisite() || ( $screen->is_network && current_user_can('manage_network_plugins') ) ) {
			if ( apply_filters( 'show_advanced_plugins', true, 'mustuse' ) )
				$plugins['mustuse'] = get_mu_plugins();
			if ( apply_filters( 'show_advanced_plugins', true, 'dropins' ) )
				$plugins['dropins'] = get_dropins();

			$current = get_site_transient( 'update_plugins' );
			foreach ( (array) $plugins['all'] as $plugin_file => $plugin_data ) {
				if ( isset( $current->response[ $plugin_file ] ) )
					$plugins['upgrade'][ $plugin_file ] = $plugin_data;
			}
		}

		set_transient( 'plugin_slugs', array_keys( $plugins['all'] ), 86400 );

		$recently_activated = get_option( 'recently_activated', array() );

		$one_week = 7*24*60*60;
		foreach ( $recently_activated as $key => $time )
			if ( $time + $one_week < time() )
				unset( $recently_activated[$key] );
		update_option( 'recently_activated', $recently_activated );

		foreach ( (array) $plugins['all'] as $plugin_file => $plugin_data ) {
			// Filter into individual sections
			if ( is_multisite() && is_network_only_plugin( $plugin_file ) && !$screen->is_network ) {
				unset( $plugins['all'][ $plugin_file] );
			} elseif ( is_plugin_active_for_network($plugin_file) && !$screen->is_network ) {
				unset( $plugins['all'][ $plugin_file ] );
			} elseif ( is_multisite() && is_network_only_plugin( $plugin_file ) && !current_user_can( 'manage_network_plugins' ) ) {
				$plugins['network'][ $plugin_file ] = $plugin_data;
			} elseif ( ( !$screen->is_network && is_plugin_active( $plugin_file ) )
				|| ( $screen->is_network && is_plugin_active_for_network( $plugin_file ) ) ) {
				$plugins['active'][ $plugin_file ] = $plugin_data;
			} else {
				if ( !$screen->is_network && isset( $recently_activated[ $plugin_file ] ) ) // Was the plugin recently activated?
					$plugins['recently_activated'][ $plugin_file ] = $plugin_data;
				$plugins['inactive'][ $plugin_file ] = $plugin_data;
			}
		}

		if ( !current_user_can( 'update_plugins' ) )
			$plugins['upgrade'] = array();

		if ( $s ) {
			$status = 'search';
			$plugins['search'] = array_filter( $plugins['all'], array( &$this, '_search_callback' ) );
		}

		$totals = array();
		foreach ( $plugins as $type => $list )
			$totals[ $type ] = count( $list );

		if ( empty( $plugins[ $status ] ) && !in_array( $status, array( 'all', 'search' ) ) )
			$status = 'all';

		$this->items = array();
		foreach ( $plugins[ $status ] as $plugin_file => $plugin_data ) {
			// Translate, Don't Apply Markup, Sanitize HTML
			$this->items[$plugin_file] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
		}

		$total_this_page = $totals[ $status ];

		if ( $orderby ) {
			$orderby = ucfirst( $orderby );
			$order = strtoupper( $order );

			uasort( $this->items, array( &$this, '_order_callback' ) );
		}

		$plugins_per_page = $this->get_items_per_page( str_replace( '-', '_', $screen->id . '_per_page' ), 999 );

		$start = ( $page - 1 ) * $plugins_per_page;

		if ( $total_this_page > $plugins_per_page )
			$this->items = array_slice( $this->items, $start, $plugins_per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_this_page,
			'per_page' => $plugins_per_page,
		) );
	}

	function _search_callback( $plugin ) {
		static $term;
		if ( is_null( $term ) )
			$term = stripslashes( $_REQUEST['s'] );

		foreach ( $plugin as $value )
			if ( stripos( $value, $term ) !== false )
				return true;

		return false;
	}

	function _order_callback( $plugin_a, $plugin_b ) {
		global $orderby, $order;

		$a = $plugin_a[$orderby];
		$b = $plugin_b[$orderby];

		if ( $a == $b )
			return 0;

		if ( 'DESC' == $order )
			return ( $a < $b ) ? 1 : -1;
		else
			return ( $a < $b ) ? -1 : 1;
	}

	function no_items() {
		global $plugins;

		if ( !empty( $plugins['all'] ) )
			_e( 'No plugins found.' );
		else
			_e( 'You do not appear to have any plugins available at this time.' );
	}

	function get_columns() {
		global $status;

		return array(
			'cb'          => !in_array( $status, array( 'mustuse', 'dropins' ) ) ? '<input type="checkbox" />' : '',
			'name'        => __( 'Plugin' ),
			'description' => __( 'Description' ),
		);
	}

	function get_sortable_columns() {
		return array();
	}

	function get_views() {
		global $totals, $status;

		$status_links = array();
		foreach ( $totals as $type => $count ) {
			if ( !$count )
				continue;

			switch ( $type ) {
				case 'all':
					$text = _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $count, 'plugins' );
					break;
				case 'active':
					$text = _n( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', $count );
					break;
				case 'recently_activated':
					$text = _n( 'Recently Active <span class="count">(%s)</span>', 'Recently Active <span class="count">(%s)</span>', $count );
					break;
				case 'inactive':
					$text = _n( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', $count );
					break;
				case 'network':
					$text = _n( 'Network <span class="count">(%s)</span>', 'Network <span class="count">(%s)</span>', $count );
					break;
				case 'mustuse':
					$text = _n( 'Must-Use <span class="count">(%s)</span>', 'Must-Use <span class="count">(%s)</span>', $count );
					break;
				case 'dropins':
					$text = _n( 'Drop-ins <span class="count">(%s)</span>', 'Drop-ins <span class="count">(%s)</span>', $count );
					break;
				case 'upgrade':
					$text = _n( 'Update Available <span class="count">(%s)</span>', 'Update Available <span class="count">(%s)</span>', $count );
					break;
			}

			if ( 'search' != $type ) {
				$status_links[$type] = sprintf( "<a href='%s' %s>%s</a>",
					add_query_arg('plugin_status', $type, 'plugins.php'),
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

		$screen = get_current_screen();

		if ( 'active' != $status ) {
			$action = $screen->is_network ? 'network-activate-selected' : 'activate-selected';
			$actions[ $action ] = __( 'Activate' );
		}

		if ( 'inactive' != $status && 'recent' != $status )
			$actions['deactivate-selected'] = __( 'Deactivate' );

		if ( !is_multisite() || $screen->is_network ) {
			if ( current_user_can( 'update_plugins' ) )
				$actions['update-selected'] = __( 'Update' );
			if ( current_user_can( 'delete_plugins' ) && ( 'active' != $status ) )
				$actions['delete-selected'] = __( 'Delete' );
		}

		return $actions;
	}

	function bulk_actions( $which ) {
		global $status;

		if ( in_array( $status, array( 'mustuse', 'dropins' ) ) )
			return;

		parent::bulk_actions( $which );
	}

	function extra_tablenav( $which ) {
		global $status;

		if ( ! in_array($status, array('recently_activated', 'mustuse', 'dropins') ) )
			return;

		echo '<div class="alignleft actions">';

		if ( 'recently_activated' == $status )
			submit_button( __( 'Clear List' ), 'secondary', 'clear-recent-list', false );
		elseif ( 'top' == $which && 'mustuse' == $status )
			echo '<p>' . sprintf( __( 'Files in the <code>%s</code> directory are executed automatically.' ), str_replace( ABSPATH, '/', WPMU_PLUGIN_DIR ) ) . '</p>';
		elseif ( 'top' == $which && 'dropins' == $status )
			echo '<p>' . sprintf( __( 'Drop-ins are advanced plugins in the <code>%s</code> directory that replace WordPress functionality when present.' ), str_replace( ABSPATH, '', WP_CONTENT_DIR ) ) . '</p>';

		echo '</div>';
	}

	function current_action() {
		if ( isset($_POST['clear-recent-list']) )
			return 'clear-recent-list';

		return parent::current_action();
	}

	function display_rows() {
		global $status;

		$screen = get_current_screen();

		if ( is_multisite() && !$screen->is_network && in_array( $status, array( 'mustuse', 'dropins' ) ) )
			return;

		foreach ( $this->items as $plugin_file => $plugin_data )
			$this->single_row( $plugin_file, $plugin_data );
	}

	function single_row( $plugin_file, $plugin_data ) {
		global $status, $page, $s;

		$context = $status;

		$screen = get_current_screen();

		// preorder
		$actions = array(
			'network_deactivate' => '', 'deactivate' => '',
			'network_only' => '', 'activate' => '',
			'network_activate' => '',
			'edit' => '',
			'delete' => '',
		);

		if ( 'mustuse' == $context ) {
			$is_active = true;
		} elseif ( 'dropins' == $context ) {
			$dropins = _get_dropins();
			$plugin_name = $plugin_file;
			if ( $plugin_file != $plugin_data['Name'] )
				$plugin_name .= '<br/>' . $plugin_data['Name'];
			if ( true === ( $dropins[ $plugin_file ][1] ) ) { // Doesn't require a constant
				$is_active = true;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . '</strong></p>';
			} elseif ( constant( $dropins[ $plugin_file ][1] ) ) { // Constant is true
				$is_active = true;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . '</strong></p>';
			} else {
				$is_active = false;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . ' <span class="attention">' . __('Inactive:') . '</span></strong> ' . sprintf( __( 'Requires <code>%s</code> in <code>wp-config.php</code>.' ), "define('" . $dropins[ $plugin_file ][1] . "', true);" ) . '</p>';
			}
			if ( $plugin_data['Description'] )
				$description .= '<p>' . $plugin_data['Description'] . '</p>';
		} else {
			$is_active_for_network = is_plugin_active_for_network($plugin_file);
			if ( $screen->is_network )
				$is_active = $is_active_for_network;
			else
				$is_active = is_plugin_active( $plugin_file );

			if ( $is_active_for_network && !is_super_admin() && !$screen->is_network )
				return;

			if ( $screen->is_network ) {
				if ( $is_active_for_network ) {
					if ( current_user_can( 'manage_network_plugins' ) )
						$actions['network_deactivate'] = '<a href="' . wp_nonce_url('plugins.php?action=deactivate&amp;networkwide=1&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Deactivate this plugin') . '">' . __('Network Deactivate') . '</a>';
				} else {
					if ( current_user_can( 'manage_network_plugins' ) )
						$actions['network_activate'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;networkwide=1&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin for all sites in this network') . '" class="edit">' . __('Network Activate') . '</a>';
					if ( current_user_can( 'delete_plugins' ) && ! is_plugin_active( $plugin_file ) )
						$actions['delete'] = '<a href="' . wp_nonce_url('plugins.php?action=delete-selected&amp;checked[]=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'bulk-plugins') . '" title="' . esc_attr__('Delete this plugin') . '" class="delete">' . __('Delete') . '</a>';
				}
			} else {
				if ( $is_active ) {
					$actions['deactivate'] = '<a href="' . wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Deactivate this plugin') . '">' . __('Deactivate') . '</a>';
				} else {
					$actions['activate'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin') . '" class="edit">' . __('Activate') . '</a>';

					if ( ! is_multisite() && current_user_can('delete_plugins') )
						$actions['delete'] = '<a href="' . wp_nonce_url('plugins.php?action=delete-selected&amp;checked[]=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'bulk-plugins') . '" title="' . esc_attr__('Delete this plugin') . '" class="delete">' . __('Delete') . '</a>';
				} // end if $is_active
			 } // end if $screen->is_network

			if ( ( ! is_multisite() || $screen->is_network ) && current_user_can('edit_plugins') && is_writable(WP_PLUGIN_DIR . '/' . $plugin_file) )
				$actions['edit'] = '<a href="plugin-editor.php?file=' . $plugin_file . '" title="' . esc_attr__('Open this file in the Plugin Editor') . '" class="edit">' . __('Edit') . '</a>';
		} // end if $context

		$prefix = $screen->is_network ? 'network_admin_' : '';
		$actions = apply_filters( $prefix . 'plugin_action_links', array_filter( $actions ), $plugin_file, $plugin_data, $context );
		$actions = apply_filters( $prefix . "plugin_action_links_$plugin_file", $actions, $plugin_file, $plugin_data, $context );

		$class = $is_active ? 'active' : 'inactive';
		$checkbox_id =  "checkbox_" . md5($plugin_data['Name']);
		$checkbox = in_array( $status, array( 'mustuse', 'dropins' ) ) ? '' : "<input type='checkbox' name='checked[]' value='" . esc_attr( $plugin_file ) . "' id='" . $checkbox_id . "' /><label class='screen-reader-text' for='" . $checkbox_id . "' >" . __('Select') . " " . $plugin_data['Name'] . "</label>";
		if ( 'dropins' != $context ) {
			$description = '<p>' . ( $plugin_data['Description'] ? $plugin_data['Description'] : '&nbsp;' ) . '</p>';
			$plugin_name = $plugin_data['Name'];
		}

		$id = sanitize_title( $plugin_name );

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
					echo "<td class='plugin-title'$style><strong>$plugin_name</strong>";
					echo $this->row_actions( $actions, true );
					echo "</td>";
					break;
				case 'description':
					echo "<td class='column-description desc'$style>
						<div class='plugin-description'>$description</div>
						<div class='$class second plugin-version-author-uri'>";

					$plugin_meta = array();
					if ( !empty( $plugin_data['Version'] ) )
						$plugin_meta[] = sprintf( __( 'Version %s' ), $plugin_data['Version'] );
					if ( !empty( $plugin_data['Author'] ) ) {
						$author = $plugin_data['Author'];
						if ( !empty( $plugin_data['AuthorURI'] ) )
							$author = '<a href="' . $plugin_data['AuthorURI'] . '" title="' . esc_attr__( 'Visit author homepage' ) . '">' . $plugin_data['Author'] . '</a>';
						$plugin_meta[] = sprintf( __( 'By %s' ), $author );
					}
					if ( ! empty( $plugin_data['PluginURI'] ) )
						$plugin_meta[] = '<a href="' . $plugin_data['PluginURI'] . '" title="' . esc_attr__( 'Visit plugin site' ) . '">' . __( 'Visit plugin site' ) . '</a>';

					$plugin_meta = apply_filters( 'plugin_row_meta', $plugin_meta, $plugin_file, $plugin_data, $status );
					echo implode( ' | ', $plugin_meta );

					echo "</div></td>";
					break;
				default:
					echo "<td class='$column_name column-$column_name'$style>";
					do_action( 'manage_plugins_custom_column', $column_name, $plugin_file, $plugin_data );
					echo "</td>";
			}
		}

		echo "</tr>";

		do_action( 'after_plugin_row', $plugin_file, $plugin_data, $status );
		do_action( "after_plugin_row_$plugin_file", $plugin_file, $plugin_data, $status );
	}
}

?>
