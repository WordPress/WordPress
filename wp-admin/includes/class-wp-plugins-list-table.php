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

	function __construct( $args = array() ) {
		global $status, $page;

		parent::__construct( array(
			'plural' => 'plugins',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );

		$status = 'all';
		if ( isset( $_REQUEST['plugin_status'] ) && in_array( $_REQUEST['plugin_status'], array( 'active', 'inactive', 'recently_activated', 'upgrade', 'mustuse', 'dropins', 'search' ) ) )
			$status = $_REQUEST['plugin_status'];

		if ( isset($_REQUEST['s']) )
			$_SERVER['REQUEST_URI'] = add_query_arg('s', wp_unslash($_REQUEST['s']) );

		$page = $this->get_pagenum();
	}

	function get_table_classes() {
		return array( 'widefat', $this->_args['plural'] );
	}

	function ajax_user_can() {
		return current_user_can('activate_plugins');
	}

	function prepare_items() {
		global $status, $plugins, $totals, $page, $orderby, $order, $s;

		wp_reset_vars( array( 'orderby', 'order', 's' ) );

		/**
		 * Filter the full array of plugins to list in the Plugins list table.
		 *
		 * @since 3.0.0
		 *
		 * @see get_plugins()
		 *
		 * @param array $plugins An array of plugins to display in the list table.
		 */
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

		$screen = $this->screen;

		if ( ! is_multisite() || ( $screen->in_admin( 'network' ) && current_user_can( 'manage_network_plugins' ) ) ) {

			/**
			 * Filter whether to display the advanced plugins list table.
			 *
			 * There are two types of advanced plugins - must-use and drop-ins -
			 * which can be used in a single site or Multisite network.
			 *
			 * The $type parameter allows you to differentiate between the type of advanced
			 * plugins to filter the display of. Contexts include 'mustuse' and 'dropins'.
			 *
			 * @since 3.0.0
			 *
			 * @param bool   $show Whether to show the advanced plugins for the specified
			 *                     plugin type. Default true.
			 * @param string $type The plugin type. Accepts 'mustuse', 'dropins'.
			 */
			if ( apply_filters( 'show_advanced_plugins', true, 'mustuse' ) ) {
				$plugins['mustuse'] = get_mu_plugins();
			}

			/** This action is documented in wp-admin/includes/class-wp-plugins-list-table.php */
			if ( apply_filters( 'show_advanced_plugins', true, 'dropins' ) )
				$plugins['dropins'] = get_dropins();

			if ( current_user_can( 'update_plugins' ) ) {
				$current = get_site_transient( 'update_plugins' );
				foreach ( (array) $plugins['all'] as $plugin_file => $plugin_data ) {
					if ( isset( $current->response[ $plugin_file ] ) ) {
						$plugins['all'][ $plugin_file ]['update'] = true;
						$plugins['upgrade'][ $plugin_file ] = $plugins['all'][ $plugin_file ];
					}
				}
			}
		}

		set_transient( 'plugin_slugs', array_keys( $plugins['all'] ), DAY_IN_SECONDS );

		if ( ! $screen->in_admin( 'network' ) ) {
			$recently_activated = get_option( 'recently_activated', array() );

			foreach ( $recently_activated as $key => $time )
				if ( $time + WEEK_IN_SECONDS < time() )
					unset( $recently_activated[$key] );
			update_option( 'recently_activated', $recently_activated );
		}

		foreach ( (array) $plugins['all'] as $plugin_file => $plugin_data ) {
			// Filter into individual sections
			if ( is_multisite() && ! $screen->in_admin( 'network' ) && is_network_only_plugin( $plugin_file ) && ! is_plugin_active( $plugin_file ) ) {
				// On the non-network screen, filter out network-only plugins as long as they're not individually activated
				unset( $plugins['all'][ $plugin_file ] );
			} elseif ( ! $screen->in_admin( 'network' ) && is_plugin_active_for_network( $plugin_file ) ) {
				// On the non-network screen, filter out network activated plugins
				unset( $plugins['all'][ $plugin_file ] );
			} elseif ( ( ! $screen->in_admin( 'network' ) && is_plugin_active( $plugin_file ) )
				|| ( $screen->in_admin( 'network' ) && is_plugin_active_for_network( $plugin_file ) ) ) {
				// On the non-network screen, populate the active list with plugins that are individually activated
				// On the network-admin screen, populate the active list with plugins that are network activated
				$plugins['active'][ $plugin_file ] = $plugin_data;
			} else {
				if ( ! $screen->in_admin( 'network' ) && isset( $recently_activated[ $plugin_file ] ) ) {
					// On the non-network screen, populate the recently activated list with plugins that have been recently activated
					$plugins['recently_activated'][ $plugin_file ] = $plugin_data;
				}
				// Populate the inactive list with plugins that aren't activated
				$plugins['inactive'][ $plugin_file ] = $plugin_data;
			}
		}

		if ( $s ) {
			$status = 'search';
			$plugins['search'] = array_filter( $plugins['all'], array( $this, '_search_callback' ) );
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

			uasort( $this->items, array( $this, '_order_callback' ) );
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
			$term = wp_unslash( $_REQUEST['s'] );

		foreach ( $plugin as $value ) {
			if ( false !== stripos( strip_tags( $value ), $term ) ) {
				return true;
			}
		}

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

		if ( 'active' != $status )
			$actions['activate-selected'] = $this->screen->in_admin( 'network' ) ? __( 'Network Activate' ) : __( 'Activate' );

		if ( 'inactive' != $status && 'recent' != $status )
			$actions['deactivate-selected'] = $this->screen->in_admin( 'network' ) ? __( 'Network Deactivate' ) : __( 'Deactivate' );

		if ( !is_multisite() || $this->screen->in_admin( 'network' ) ) {
			if ( current_user_can( 'update_plugins' ) )
				$actions['update-selected'] = __( 'Update' );
			if ( current_user_can( 'delete_plugins' ) && ( 'active' != $status ) )
				$actions['delete-selected'] = __( 'Delete' );
		}

		return $actions;
	}

	function bulk_actions() {
		global $status;

		if ( in_array( $status, array( 'mustuse', 'dropins' ) ) )
			return;

		parent::bulk_actions();
	}

	function extra_tablenav( $which ) {
		global $status;

		if ( ! in_array($status, array('recently_activated', 'mustuse', 'dropins') ) )
			return;

		echo '<div class="alignleft actions">';

		if ( ! $this->screen->in_admin( 'network' ) && 'recently_activated' == $status )
			submit_button( __( 'Clear List' ), 'button', 'clear-recent-list', false );
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

		if ( is_multisite() && ! $this->screen->in_admin( 'network' ) && in_array( $status, array( 'mustuse', 'dropins' ) ) )
			return;

		foreach ( $this->items as $plugin_file => $plugin_data )
			$this->single_row( array( $plugin_file, $plugin_data ) );
	}

	function single_row( $item ) {
		global $status, $page, $s, $totals;

		list( $plugin_file, $plugin_data ) = $item;
		$context = $status;
		$screen = $this->screen;

		// preorder
		$actions = array(
			'deactivate' => '',
			'activate' => '',
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
			} elseif ( defined( $dropins[ $plugin_file ][1] ) && constant( $dropins[ $plugin_file ][1] ) ) { // Constant is true
				$is_active = true;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . '</strong></p>';
			} else {
				$is_active = false;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . ' <span class="attention">' . __('Inactive:') . '</span></strong> ' . sprintf( __( 'Requires <code>%s</code> in <code>wp-config.php</code>.' ), "define('" . $dropins[ $plugin_file ][1] . "', true);" ) . '</p>';
			}
			if ( $plugin_data['Description'] )
				$description .= '<p>' . $plugin_data['Description'] . '</p>';
		} else {
			if ( $screen->in_admin( 'network' ) )
				$is_active = is_plugin_active_for_network( $plugin_file );
			else
				$is_active = is_plugin_active( $plugin_file );

			if ( $screen->in_admin( 'network' ) ) {
				if ( $is_active ) {
					if ( current_user_can( 'manage_network_plugins' ) )
						$actions['deactivate'] = '<a href="' . wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . urlencode( $plugin_file ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Deactivate this plugin') . '">' . __('Network Deactivate') . '</a>';
				} else {
					if ( current_user_can( 'manage_network_plugins' ) )
						$actions['activate'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . urlencode( $plugin_file ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin for all sites in this network') . '" class="edit">' . __('Network Activate') . '</a>';
					if ( current_user_can( 'delete_plugins' ) && ! is_plugin_active( $plugin_file ) )
						$actions['delete'] = '<a href="' . wp_nonce_url('plugins.php?action=delete-selected&amp;checked[]=' . urlencode( $plugin_file ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'bulk-plugins') . '" title="' . esc_attr__('Delete this plugin') . '" class="delete">' . __('Delete') . '</a>';
				}
			} else {
				if ( $is_active ) {
					$actions['deactivate'] = '<a href="' . wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . urlencode( $plugin_file ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Deactivate this plugin') . '">' . __('Deactivate') . '</a>';
				} else {
					$actions['activate'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . urlencode( $plugin_file ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin') . '" class="edit">' . __('Activate') . '</a>';

					if ( ! is_multisite() && current_user_can('delete_plugins') )
						$actions['delete'] = '<a href="' . wp_nonce_url('plugins.php?action=delete-selected&amp;checked[]=' . urlencode( $plugin_file ) . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'bulk-plugins') . '" title="' . esc_attr__('Delete this plugin') . '" class="delete">' . __('Delete') . '</a>';
				} // end if $is_active
			 } // end if $screen->in_admin( 'network' )

			if ( ( ! is_multisite() || $screen->in_admin( 'network' ) ) && current_user_can('edit_plugins') && is_writable(WP_PLUGIN_DIR . '/' . $plugin_file) )
				$actions['edit'] = '<a href="plugin-editor.php?file=' . urlencode( $plugin_file ) . '" title="' . esc_attr__('Open this file in the Plugin Editor') . '" class="edit">' . __('Edit') . '</a>';
		} // end if $context

		$prefix = $screen->in_admin( 'network' ) ? 'network_admin_' : '';

		/**
		 * Filter the action links displayed for each plugin in the Plugins list table.
		 *
		 * The dynamic portion of the hook name, $prefix, refers to the context the
		 * action links are displayed in. The 'network_admin_' prefix is used if the
		 * current screen is the Network plugins list table. The prefix is empty ('')
		 * if the current screen is the site plugins list table.
		 *
		 * The default action links for the Network plugins list table include
		 * 'Network Activate', 'Network Deactivate', 'Edit', and 'Delete'.
		 *
		 * The default action links for the site plugins list table include
		 * 'Activate', 'Deactivate', and 'Edit', for a network site, and
		 * 'Activate', 'Deactivate', 'Edit', and 'Delete' for a single site.
		 *
		 * @since 2.5.0
		 *
		 * @param array  $actions     An array of plugin action links.
		 * @param string $plugin_file Path to the plugin file.
		 * @param array  $plugin_data An array of plugin data.
		 * @param string $context     The plugin context. Defaults are 'All', 'Active',
		 *                            'Inactive', 'Recently Activated', 'Upgrade',
		 *                            'Must-Use', 'Drop-ins', 'Search'.
		 */
		$actions = apply_filters( $prefix . 'plugin_action_links', array_filter( $actions ), $plugin_file, $plugin_data, $context );

		/**
		 * Filter the list of action links displayed for a specific plugin.
		 *
		 * The first dynamic portion of the hook name, $prefix, refers to the context
		 * the action links are displayed in. The 'network_admin_' prefix is used if the
		 * current screen is the Network plugins list table. The prefix is empty ('')
		 * if the current screen is the site plugins list table.
		 *
		 * The second dynamic portion of the hook name, $plugin_file, refers to the path
		 * to the plugin file, relative to the plugins directory.
		 *
		 * @since 2.7.0
		 *
		 * @param array  $actions     An array of plugin action links.
		 * @param string $plugin_file Path to the plugin file.
		 * @param array  $plugin_data An array of plugin data.
		 * @param string $context     The plugin context. Defaults are 'All', 'Active',
		 *                            'Inactive', 'Recently Activated', 'Upgrade',
		 *                            'Must-Use', 'Drop-ins', 'Search'.
		 */
		$actions = apply_filters( $prefix . "plugin_action_links_$plugin_file", $actions, $plugin_file, $plugin_data, $context );

		$class = $is_active ? 'active' : 'inactive';
		$checkbox_id =  "checkbox_" . md5($plugin_data['Name']);
		if ( in_array( $status, array( 'mustuse', 'dropins' ) ) ) {
			$checkbox = '';
		} else {
			$checkbox = "<label class='screen-reader-text' for='" . $checkbox_id . "' >" . sprintf( __( 'Select %s' ), $plugin_data['Name'] ) . "</label>"
				. "<input type='checkbox' name='checked[]' value='" . esc_attr( $plugin_file ) . "' id='" . $checkbox_id . "' />";
		}
		if ( 'dropins' != $context ) {
			$description = '<p>' . ( $plugin_data['Description'] ? $plugin_data['Description'] : '&nbsp;' ) . '</p>';
			$plugin_name = $plugin_data['Name'];
		}

		$id = sanitize_title( $plugin_name );
		if ( ! empty( $totals['upgrade'] ) && ! empty( $plugin_data['update'] ) )
			$class .= ' update';

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

					/**
					 * Filter the array of row meta for each plugin in the Plugins list table.
					 *
					 * @since 2.8.0
					 *
					 * @param array  $plugin_meta An array of the plugin's metadata,
					 *                            including the version, author,
					 *                            author URI, and plugin URI.
					 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
					 * @param array  $plugin_data An array of plugin data.
					 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
					 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
					 *                            'Drop-ins', 'Search'.
					 */
					$plugin_meta = apply_filters( 'plugin_row_meta', $plugin_meta, $plugin_file, $plugin_data, $status );
					echo implode( ' | ', $plugin_meta );

					echo "</div></td>";
					break;
				default:
					echo "<td class='$column_name column-$column_name'$style>";

					/**
					 * Fires inside each custom column of the Plugins list table.
					 *
					 * @since 3.1.0
					 *
					 * @param string $column_name Name of the column.
					 * @param string $plugin_file Path to the plugin file.
					 * @param array  $plugin_data An array of plugin data.
					 */
					do_action( 'manage_plugins_custom_column', $column_name, $plugin_file, $plugin_data );
					echo "</td>";
			}
		}

		echo "</tr>";

		/**
		 * Fires after each row in the Plugins list table.
		 *
		 * @since 2.3.0
		 *
		 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
		 * @param array  $plugin_data An array of plugin data.
		 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
		 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                            'Drop-ins', 'Search'.
		 */
		do_action( 'after_plugin_row', $plugin_file, $plugin_data, $status );

		/**
		 * Fires after each specific row in the Plugins list table.
		 *
		 * The dynamic portion of the hook name, $plugin_file, refers to the path
		 * to the plugin file, relative to the plugins directory.
		 *
		 * @since 2.7.0
		 *
		 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
		 * @param array  $plugin_data An array of plugin data.
		 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
		 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                            'Drop-ins', 'Search'.
		 */
		do_action( "after_plugin_row_$plugin_file", $plugin_file, $plugin_data, $status );
	}
}
